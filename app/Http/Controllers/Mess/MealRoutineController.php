<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Models\Mess;
use App\Models\MessMealRoutine;
use App\Models\MessMealType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealRoutineController extends Controller
{
    public function index(Mess $mess, Request $request)
    {
        $member = $mess->members()->where('user_id', Auth::id())->where('is_active', true)->first();
        abort_if(!$member, 403);

        $isManager = Auth::user()->isManagerOf($mess->id);

        // All meal types for this mess
        $mealTypes = MessMealType::where('mess_id', $mess->id)->orderBy('id')->get();

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

        // Today's routine for selected type
        $today     = now();
        $weekNo    = MessMealRoutine::weekNoForDate($today);
        $dayOfWeek = (int) $today->format('w');
        $todayItems = $grid[$weekNo][$dayOfWeek] ?? null;

        return view('mess.meal-routine', compact(
            'mess', 'member', 'isManager', 'grid', 'todayItems',
            'weekNo', 'dayOfWeek', 'mealTypes', 'selectedType'
        ));
    }

    public function upsert(Request $request, Mess $mess)
    {
        abort_unless(Auth::user()->isManagerOf($mess->id), 403);

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
        abort_unless(Auth::user()->isManagerOf($mess->id), 403);

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
