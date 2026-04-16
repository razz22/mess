<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Models\Mess;
use App\Models\MealAttendance;
use App\Models\MealSchedule;
use App\Models\MessContact;
use App\Models\MessMealType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
{
    public function index(Mess $mess)
    {
        $this->authorizeMember($mess);

        $date       = request('date', now()->toDateString());
        $today      = now()->toDateString();
        $isPast     = $date < $today;
        $isManager  = Auth::user()->isManagerOf($mess->id);
        $isBasic    = Auth::user()->isBasicMemberOf($mess->id);

        // Ensure meal types exist (seed defaults for old messes)
        $this->ensureDefaultMealTypes($mess);

        $mealTypes = $mess->mealTypes()->get();

        // Auto-create schedules for today (not for past dates)
        if (!$isPast) {
            $this->ensureMealSchedules($mess, $date, $mealTypes);
        }

        // Load schedules keyed by meal type name
        $schedules = MealSchedule::where('mess_id', $mess->id)
            ->where('date', $date)
            ->get()
            ->keyBy('type');

        $members = $mess->activeMembers()->with('user')->get();

        // Load ALL attendances for this date
        $scheduleIds = $schedules->pluck('id');
        $allAttendances = MealAttendance::whereIn('meal_schedule_id', $scheduleIds)
            ->get()
            ->groupBy(fn($a) => $a->meal_schedule_id . '_' . $a->user_id);

        $member = Auth::user()->getMembershipIn($mess->id);

        // Get today's change count for the current user
        $myChangesToday = 0;
        if (!$isManager && $date === now()->toDateString()) {
            $log = DB::table('meal_attendance_change_logs')
                ->where('mess_id', $mess->id)
                ->where('user_id', Auth::id())
                ->where('log_date', $date)
                ->first();
            $myChangesToday = $log ? $log->changes : 0;
        }

        $allMealTypes = MessMealType::where('mess_id', $mess->id)
            ->orderByRaw('CASE WHEN close_time IS NULL THEN 1 ELSE 0 END')
            ->orderBy('close_days_before', 'desc')
            ->orderBy('close_time')
            ->orderBy('sort_order')
            ->get();

        // Meal totals for WhatsApp sending (manager only)
        $mealTotals = [];
        foreach ($mealTypes as $mt) {
            $sch = $schedules[$mt->name] ?? null;
            $total = $sch
                ? $allAttendances->filter(fn($g, $k) => str_starts_with($k, $sch->id . '_'))->flatten()->sum('quantity')
                : 0;
            $mealTotals[$mt->name] = (float) $total;
        }

        $contacts = $isManager
            ? MessContact::where('mess_id', $mess->id)->orderBy('label')->orderBy('name')->get()
            : collect();

        return view('mess.meal-attendance', compact(
            'mess', 'mealTypes', 'allMealTypes', 'schedules', 'members',
            'allAttendances', 'date', 'today', 'isPast',
            'isManager', 'isBasic', 'member', 'myChangesToday',
            'mealTotals', 'contacts'
        ));
    }

    public function markAttendance(Request $request, Mess $mess)
    {
        $this->authorizeMember($mess);

        $request->validate([
            'schedule_id' => 'required|exists:meal_schedules,id',
            'user_id'     => 'nullable|exists:users,id',
            'quantity'    => ['required', 'numeric', 'min:0', 'max:99', function ($attr, $value, $fail) {
                if (fmod(round($value * 10), 5) !== 0.0) {
                    $fail('Quantity must be in steps of 0.5.');
                }
            }],
        ]);

        $schedule     = MealSchedule::findOrFail($request->schedule_id);
        if ($schedule->mess_id !== $mess->id) abort(403);

        $isManager    = Auth::user()->isManagerOf($mess->id);
        $targetUserId = $request->user_id ?? Auth::id();
        $quantity     = (float) $request->quantity;
        $status       = $quantity > 0 ? 'on' : 'off';

        if (!$isManager && $targetUserId != Auth::id()) {
            return response()->json(['error' => 'You can only manage your own attendance.'], 403);
        }

        if ($schedule->date->toDateString() < now()->toDateString()) {
            return response()->json(['error' => 'Cannot modify attendance for past dates.'], 422);
        }

        if (!$isManager) {
            $mealType = $mess->mealTypes()->where('name', $schedule->type)->first();
            if ($mealType && $mealType->isExpired()) {
                return response()->json(['error' => 'Attendance time has expired for ' . $schedule->type . '.'], 422);
            }
        }

        // 3-change daily limit for members
        if (!$isManager) {
            $today   = now()->toDateString();
            $log     = DB::table('meal_attendance_change_logs')
                ->where('mess_id', $mess->id)->where('user_id', $targetUserId)->where('log_date', $today)->first();
            $changes = $log ? $log->changes : 0;

            if ($changes >= 3) {
                return response()->json([
                    'error'         => 'You cannot change meal attendance more than 3 times per day. Contact your manager.',
                    'limit_reached' => true,
                ], 422);
            }

            DB::table('meal_attendance_change_logs')->upsert(
                ['mess_id' => $mess->id, 'user_id' => $targetUserId, 'log_date' => $today, 'changes' => $changes + 1, 'created_at' => now(), 'updated_at' => now()],
                ['mess_id', 'user_id', 'log_date'],
                ['changes' => $changes + 1, 'updated_at' => now()]
            );
        }

        MealAttendance::updateOrCreate(
            ['meal_schedule_id' => $schedule->id, 'user_id' => $targetUserId],
            ['mess_id' => $mess->id, 'status' => $status, 'quantity' => $quantity, 'marked_at' => now()]
        );

        $totalQty = MealAttendance::where('meal_schedule_id', $schedule->id)->sum('quantity');
        $onCount  = MealAttendance::where('meal_schedule_id', $schedule->id)->where('quantity', '>', 0)->count();

        $remaining = null;
        if (!$isManager) {
            $today = now()->toDateString();
            $log   = DB::table('meal_attendance_change_logs')
                ->where('mess_id', $mess->id)->where('user_id', $targetUserId)->where('log_date', $today)->first();
            $remaining = max(0, 3 - ($log ? $log->changes : 0));
        }

        return response()->json([
            'success'   => true,
            'quantity'  => $quantity,
            'status'    => $status,
            'totalQty'  => $totalQty,
            'onCount'   => $onCount,
            'remaining' => $remaining,
        ]);
    }

    // Manager: add a custom meal type
    public function storeMealType(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'name'             => 'required|string|max:50',
            'close_time'       => 'nullable|date_format:H:i',
            'close_days_before'=> 'nullable|integer|min:0|max:30',
            'rate'             => 'nullable|numeric|min:0',
        ]);

        // Re-activate if previously deactivated
        $existing = MessMealType::where('mess_id', $mess->id)->where('name', ucfirst($request->name))->first();
        if ($existing) {
            $existing->update([
                'is_active'         => true,
                'close_time'        => $request->close_time ? $request->close_time . ':00' : $existing->close_time,
                'close_days_before' => $request->close_days_before ?? $existing->close_days_before,
                'rate'              => $request->rate ?? $existing->rate,
            ]);
            return back()->with('success', '"' . $existing->name . '" re-activated.');
        }

        $max = MessMealType::where('mess_id', $mess->id)->max('sort_order') ?? 0;

        MessMealType::create([
            'mess_id'           => $mess->id,
            'name'              => ucfirst($request->name),
            'close_time'        => $request->close_time ? $request->close_time . ':00' : null,
            'close_days_before' => $request->close_days_before ?? 0,
            'rate'              => $request->rate ?? 0,
            'sort_order'        => $max + 1,
        ]);

        return back()->with('success', 'Meal type added.');
    }

    public function bulkAttendance(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'dates'      => 'required|array|min:1',
            'dates.*'    => 'date',
            'meal_types' => 'required|array|min:1',
            'meal_types.*'=> 'string|max:50',
            'quantity'   => ['required', 'numeric', 'min:0', 'max:99', function ($attr, $value, $fail) {
                if (fmod(round($value * 10), 5) !== 0.0) {
                    $fail('Quantity must be in steps of 0.5.');
                }
            }],
        ]);

        $quantity  = (float) $request->quantity;
        $status    = $quantity > 0 ? 'on' : 'off';
        $mealTypes = $mess->mealTypes()->pluck('name')->toArray();
        $count     = 0;

        foreach ($request->dates as $date) {
            // Ensure schedules exist for this date
            foreach ($request->meal_types as $typeName) {
                if (!in_array($typeName, $mealTypes)) continue;

                $schedule = MealSchedule::firstOrCreate(
                    ['mess_id' => $mess->id, 'date' => $date, 'type' => $typeName],
                    ['status' => 'open']
                );

                foreach ($request->user_ids as $userId) {
                    MealAttendance::updateOrCreate(
                        ['meal_schedule_id' => $schedule->id, 'user_id' => $userId],
                        ['mess_id' => $mess->id, 'status' => $status, 'quantity' => $quantity, 'marked_at' => now()]
                    );
                    $count++;
                }
            }
        }

        $label = $quantity > 0 ? "set to {$quantity}" : 'marked as Off';
        return back()->with('success', "{$count} attendance record(s) {$label} successfully.");
    }

    // Manager: update meal type (name, rate, close_time, active)
    public function updateMealType(Request $request, Mess $mess, MessMealType $mealType)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);
        if ($mealType->mess_id !== $mess->id) abort(403);

        $request->validate([
            'name'              => 'required|string|max:50',
            'close_time'        => 'nullable|date_format:H:i',
            'close_days_before' => 'nullable|integer|min:0|max:30',
            'rate'              => 'nullable|numeric|min:0',
            'is_active'         => 'nullable|boolean',
        ]);

        $mealType->update([
            'name'              => ucfirst($request->name),
            'close_time'        => $request->close_time ? $request->close_time . ':00' : null,
            'close_days_before' => $request->close_days_before ?? 0,
            'rate'              => $request->rate ?? $mealType->rate,
            'is_active'         => $request->has('is_active') ? (bool)$request->is_active : $mealType->is_active,
        ]);

        return back()->with('success', $mealType->name . ' updated.');
    }

    // Manager: toggle active / deactivate meal type
    public function destroyMealType(Mess $mess, MessMealType $mealType)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);
        if ($mealType->mess_id !== $mess->id) abort(403);

        $mealType->update(['is_active' => false]);
        return back()->with('success', '"' . $mealType->name . '" disabled.');
    }

    public function closeMeal(Request $request, Mess $mess, MealSchedule $schedule)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);
        if ($schedule->mess_id !== $mess->id) abort(403);

        $schedule->update([
            'status'    => 'closed',
            'closed_by' => Auth::id(),
            'closed_at' => now(),
            'meal_cost' => $request->meal_cost ?? 0,
        ]);

        return response()->json(['success' => true]);
    }

    public function mealItems(Mess $mess)
    {
        $this->authorizeMember($mess);

        $date      = request('date', now()->toDateString());
        $today     = now()->toDateString();
        $mealTypes = $mess->mealTypes()->get(); // active meal types, sorted
        $member    = Auth::user()->getMembershipIn($mess->id);
        $isManager = Auth::user()->isManagerOf($mess->id);

        // Load all items for this date, keyed by meal_type
        $items = $mess->mealItems()
            ->where('date', $date)
            ->with('createdBy')
            ->orderBy('created_at')
            ->get()
            ->groupBy('meal_type');

        return view('mess.meal-items', compact('mess', 'mealTypes', 'items', 'date', 'today', 'member', 'isManager'));
    }

    public function storeMealItem(Request $request, Mess $mess)
    {
        $this->authorizeMember($mess);
        $request->validate([
            'name'      => 'required|string|max:255',
            'meal_type' => 'required|string|max:50',
            'date'      => 'required|date',
        ]);

        $item = $mess->mealItems()->create([
            'date'       => $request->date,
            'meal_type'  => $request->meal_type,
            'name'       => $request->name,
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'item'    => [
                'id'              => $item->id,
                'name'            => $item->name,
                'meal_type'       => $item->meal_type,
                'created_by_name' => Auth::user()->name,
                'is_mine'         => true,
            ],
        ]);
    }

    public function destroyMealItem(Mess $mess, \App\Models\MealItem $item)
    {
        $this->authorizeMember($mess);
        if ($item->created_by !== Auth::id() && !Auth::user()->isManagerOf($mess->id)) abort(403);
        $item->delete();
        return response()->json(['success' => true]);
    }

    private function authorizeMember(Mess $mess): void
    {
        if (!Auth::user()->getMembershipIn($mess->id)) abort(403);
    }

    private function ensureDefaultMealTypes(Mess $mess): void
    {
        if ($mess->mealTypes()->count() === 0) {
            $defaults = [
                ['name' => 'Breakfast', 'close_time' => '09:00:00', 'sort_order' => 1],
                ['name' => 'Lunch',     'close_time' => '14:00:00', 'sort_order' => 2],
                ['name' => 'Dinner',    'close_time' => '21:00:00', 'sort_order' => 3],
            ];
            foreach ($defaults as $d) {
                MessMealType::firstOrCreate(['mess_id' => $mess->id, 'name' => $d['name']], $d);
            }
        }
    }

    private function ensureMealSchedules(Mess $mess, string $date, $mealTypes): void
    {
        foreach ($mealTypes as $type) {
            MealSchedule::firstOrCreate(
                ['mess_id' => $mess->id, 'date' => $date, 'type' => $type->name],
                ['status' => 'open']
            );
        }

        // Auto-mark all members ON
        $settings = $mess->settings;
        if ($settings && $settings->auto_meal_on) {
            $schedules = MealSchedule::where('mess_id', $mess->id)->where('date', $date)->get();
            $memberIds = $mess->activeMembers()->pluck('user_id');

            foreach ($schedules as $schedule) {
                foreach ($memberIds as $userId) {
                    MealAttendance::firstOrCreate(
                        ['meal_schedule_id' => $schedule->id, 'user_id' => $userId],
                        ['mess_id' => $mess->id, 'status' => 'on', 'marked_at' => now()]
                    );
                }
            }
        }
    }
}
