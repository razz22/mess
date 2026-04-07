<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Models\Mess;
use App\Models\MealAttendance;
use App\Models\MealSchedule;
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

        return view('mess.meal-attendance', compact(
            'mess', 'mealTypes', 'schedules', 'members',
            'allAttendances', 'date', 'today', 'isPast',
            'isManager', 'isBasic', 'member', 'myChangesToday'
        ));
    }

    public function markAttendance(Request $request, Mess $mess)
    {
        $this->authorizeMember($mess);

        $request->validate([
            'schedule_id' => 'required|exists:meal_schedules,id',
            'user_id'     => 'nullable|exists:users,id',
            'status'      => 'required|in:on,off',
        ]);

        $schedule = MealSchedule::findOrFail($request->schedule_id);
        if ($schedule->mess_id !== $mess->id) abort(403);

        $isManager = Auth::user()->isManagerOf($mess->id);
        $targetUserId = $request->user_id ?? Auth::id();

        // Members can only mark their own attendance
        if (!$isManager && $targetUserId != Auth::id()) {
            return response()->json(['error' => 'You can only manage your own attendance.'], 403);
        }

        // Block past date changes
        if ($schedule->date->toDateString() < now()->toDateString()) {
            return response()->json(['error' => 'Cannot modify attendance for past dates.'], 422);
        }

        // Check close time (members only)
        if (!$isManager) {
            $mealType = $mess->mealTypes()->where('name', $schedule->type)->first();
            if ($mealType && $mealType->isExpired()) {
                return response()->json(['error' => 'Attendance time has expired for ' . $schedule->type . '.'], 422);
            }
        }

        // Enforce 3-change daily limit for non-managers
        if (!$isManager) {
            $today  = now()->toDateString();
            $log    = DB::table('meal_attendance_change_logs')
                ->where('mess_id', $mess->id)
                ->where('user_id', $targetUserId)
                ->where('log_date', $today)
                ->first();

            $changes = $log ? $log->changes : 0;

            if ($changes >= 3) {
                return response()->json([
                    'error'         => 'You cannot change your meal attendance more than 3 times per day. Please contact your manager.',
                    'limit_reached' => true,
                ], 422);
            }

            // Increment counter
            DB::table('meal_attendance_change_logs')->upsert(
                ['mess_id' => $mess->id, 'user_id' => $targetUserId, 'log_date' => $today, 'changes' => $changes + 1, 'created_at' => now(), 'updated_at' => now()],
                ['mess_id', 'user_id', 'log_date'],
                ['changes' => $changes + 1, 'updated_at' => now()]
            );
        }

        MealAttendance::updateOrCreate(
            ['meal_schedule_id' => $schedule->id, 'user_id' => $targetUserId],
            ['mess_id' => $mess->id, 'status' => $request->status, 'marked_at' => now()]
        );

        $on  = MealAttendance::where('meal_schedule_id', $schedule->id)->where('status', 'on')->count();
        $off = MealAttendance::where('meal_schedule_id', $schedule->id)->where('status', 'off')->count();

        // Return remaining changes for non-managers
        $remaining = null;
        if (!$isManager) {
            $today = now()->toDateString();
            $log   = DB::table('meal_attendance_change_logs')
                ->where('mess_id', $mess->id)
                ->where('user_id', $targetUserId)
                ->where('log_date', $today)
                ->first();
            $remaining = max(0, 3 - ($log ? $log->changes : 0));
        }

        return response()->json(['success' => true, 'status' => $request->status, 'on' => $on, 'off' => $off, 'remaining' => $remaining]);
    }

    // Manager: add a custom meal type
    public function storeMealType(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'name'       => 'required|string|max:50',
            'close_time' => 'nullable|date_format:H:i',
        ]);

        if ($mess->mealTypes()->where('name', $request->name)->exists()) {
            return back()->with('error', 'Meal type "' . $request->name . '" already exists.');
        }

        $max = $mess->mealTypes()->max('sort_order') ?? 0;

        MessMealType::create([
            'mess_id'    => $mess->id,
            'name'       => ucfirst($request->name),
            'close_time' => $request->close_time ? $request->close_time . ':00' : null,
            'sort_order' => $max + 1,
        ]);

        return back()->with('success', 'Meal type added.');
    }

    // Manager: remove a meal type
    public function destroyMealType(Mess $mess, MessMealType $mealType)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);
        if ($mealType->mess_id !== $mess->id) abort(403);

        $mealType->update(['is_active' => false]);
        return back()->with('success', 'Meal type removed.');
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
        $member = Auth::user()->getMembershipIn($mess->id);

        $items = [
            'todo'        => $mess->mealItems()->where('status', 'todo')->orderBy('sort_order')->get(),
            'in_progress' => $mess->mealItems()->where('status', 'in_progress')->orderBy('sort_order')->get(),
            'done'        => $mess->mealItems()->where('status', 'done')->orderBy('sort_order')->get(),
        ];

        return view('mess.meal-items', compact('mess', 'items', 'member'));
    }

    public function storeMealItem(Request $request, Mess $mess)
    {
        $this->authorizeMember($mess);
        $request->validate(['name' => 'required|string|max:255', 'category' => 'nullable|string|max:50']);

        $mess->mealItems()->create([
            'name'        => $request->name,
            'description' => $request->description,
            'category'    => $request->category ?? 'general',
            'status'      => 'todo',
            'created_by'  => Auth::id(),
        ]);

        return back()->with('success', 'Meal item added.');
    }

    public function updateMealItemStatus(Request $request, Mess $mess, \App\Models\MealItem $item)
    {
        $this->authorizeMember($mess);
        $request->validate(['status' => 'required|in:todo,in_progress,done']);
        $item->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function destroyMealItem(Mess $mess, \App\Models\MealItem $item)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);
        $item->delete();
        return back()->with('success', 'Item deleted.');
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
