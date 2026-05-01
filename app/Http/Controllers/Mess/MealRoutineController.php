<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\Mess;
use App\Models\MessMealRoutine;
use App\Models\MessMealType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealRoutineController extends Controller
{
    use AuthorizesMessAccess;
    public function index(Mess $mess, Request $request)
    {
        $member = $mess->members()->where('user_id', Auth::id())->where('is_active', true)->first();
        abort_if(!$member && !Auth::user()->is_super_admin, 403);

        $isManager = Auth::user()->isManagerOf($mess->id);
        $canEdit   = true; // all active members can add/edit the routine

        // Month navigation
        $monthParam  = $request->get('month', now()->format('Y-m'));
        $monthStart  = Carbon::parse($monthParam . '-01')->startOfMonth();
        $monthEnd    = $monthStart->copy()->endOfMonth();
        $prevMonth   = $monthStart->copy()->subMonth()->format('Y-m');
        $nextMonth   = $monthStart->copy()->addMonth()->format('Y-m');

        // Active meal types for this mess
        $mealTypes = MessMealType::where('mess_id', $mess->id)->where('is_active', true)->orderBy('id')->get();

        // Selected meal type (default to first)
        $selectedType = $request->get('meal_type', $mealTypes->first()?->name ?? '');

        // Load routines for selected meal type, keyed by [week_no][day_of_week]
        $routines = MessMealRoutine::where('mess_id', $mess->id)
            ->where('meal_type', $selectedType)
            ->get();

        $grid = [];
        foreach ($routines as $r) {
            $grid[$r->week_no][$r->day_of_week] = $r->items;
        }

        // Today info
        $today      = now();
        $weekNo     = MessMealRoutine::weekNoForDate($today);
        $dayOfWeek  = (int) $today->format('w');
        $todayItems = $grid[$weekNo][$dayOfWeek] ?? null;

        // Build calendar days array for the month
        // Pad from Sunday of first week to Saturday of last week
        $calStart = $monthStart->copy()->startOfWeek(Carbon::SUNDAY);
        $calEnd   = $monthEnd->copy()->endOfWeek(Carbon::SATURDAY);

        $calDays = [];
        $cursor  = $calStart->copy();
        while ($cursor <= $calEnd) {
            $calDays[] = $cursor->copy();
            $cursor->addDay();
        }

        return view('mess.meal-routine', compact(
            'mess', 'member', 'isManager', 'canEdit', 'grid', 'todayItems',
            'weekNo', 'dayOfWeek', 'mealTypes', 'selectedType',
            'monthStart', 'monthEnd', 'monthParam', 'prevMonth', 'nextMonth',
            'calDays', 'today'
        ));
    }

    public function upsert(Request $request, Mess $mess)
    {
        $member = $mess->members()->where('user_id', Auth::id())->where('is_active', true)->first();
        abort_if(!$member && !Auth::user()->is_super_admin, 403);

        $request->validate([
            'meal_type'   => 'required|string|max:80',
            'week_no'     => 'required|integer|between:1,4',
            'day_of_week' => 'required|integer|between:0,6',
            'items'       => 'required|string|max:1000',
        ]);

        MessMealRoutine::updateOrCreate(
            [
                'mess_id'     => $mess->id,
                'meal_type'   => $request->meal_type,
                'week_no'     => $request->week_no,
                'day_of_week' => $request->day_of_week,
            ],
            ['items' => trim($request->items), 'updated_by' => Auth::id()]
        );

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request, Mess $mess)
    {
        $member = $mess->members()->where('user_id', Auth::id())->where('is_active', true)->first();
        abort_if(!$member && !Auth::user()->is_super_admin, 403);

        $request->validate([
            'meal_type'   => 'required|string|max:80',
            'week_no'     => 'required|integer|between:1,4',
            'day_of_week' => 'required|integer|between:0,6',
        ]);

        MessMealRoutine::where('mess_id', $mess->id)
            ->where('meal_type', $request->meal_type)
            ->where('week_no', $request->week_no)
            ->where('day_of_week', $request->day_of_week)
            ->delete();

        return response()->json(['success' => true]);
    }
}
