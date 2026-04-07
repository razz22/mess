<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Models\MealAttendance;
use App\Models\MealSchedule;
use App\Models\MemberDeposit;
use App\Models\MemberMonthlySummary;
use App\Models\Mess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function monthly(Mess $mess)
    {
        $this->authorizeMember($mess);

        $month = request('month', now()->month);
        $year  = request('year', now()->year);

        $members = $mess->activeMembers()->with('user')->get();

        // Get or generate monthly summaries
        $summaries = MemberMonthlySummary::where('mess_id', $mess->id)
            ->where('month', $month)
            ->where('year', $year)
            ->with('user')
            ->get()
            ->keyBy('user_id');

        // Aggregate expense data
        $totalExpenses = $mess->expenses()
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount');

        $totalDeposits = $mess->deposits()
            ->where('month', $month)
            ->where('year', $year)
            ->sum('amount');

        // Total meal count this month
        $totalMeals = MealSchedule::where('mess_id', $mess->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->count();

        // Meal attendance summary per member
        $mealByMember = MealAttendance::whereHas('schedule', fn($q) =>
                $q->where('mess_id', $mess->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
            )
            ->where('status', 'on')
            ->select('user_id', DB::raw('count(*) as meal_count'))
            ->groupBy('user_id')
            ->pluck('meal_count', 'user_id');

        $totalMealCount = $mealByMember->sum();
        $perMealCost    = $totalMealCount > 0 ? ($totalExpenses / $totalMealCount) : 0;

        // Cash in hand = total deposits - total expenses
        $cashInHand = $totalDeposits - $totalExpenses;

        $expensesByCategory = $mess->expenses()
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn($g) => ['name' => $g->first()->category?->name ?? 'Uncategorized', 'amount' => $g->sum('amount')]);

        $member = Auth::user()->getMembershipIn($mess->id);

        return view('mess.monthly-report', compact(
            'mess', 'members', 'summaries', 'month', 'year',
            'totalExpenses', 'totalDeposits', 'totalMeals',
            'mealByMember', 'perMealCost', 'cashInHand',
            'expensesByCategory', 'member'
        ));
    }

    public function generate(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $month = $request->month ?? now()->month;
        $year  = $request->year  ?? now()->year;

        $members = $mess->activeMembers()->pluck('user_id');

        $totalExpenses = $mess->expenses()
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount');

        $mealByMember = MealAttendance::whereHas('schedule', fn($q) =>
                $q->where('mess_id', $mess->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
            )
            ->where('status', 'on')
            ->select('user_id', DB::raw('count(*) as meal_count'))
            ->groupBy('user_id')
            ->pluck('meal_count', 'user_id');

        $totalMealCount = $mealByMember->sum();
        $perMealCost    = $totalMealCount > 0 ? ($totalExpenses / $totalMealCount) : 0;

        foreach ($members as $userId) {
            $memberMeals    = $mealByMember[$userId] ?? 0;
            $mealCost       = $memberMeals * $perMealCost;
            $marketExpense  = $mess->expenses()
                ->where('member_id', $userId)
                ->where('is_market_expense', true)
                ->whereMonth('expense_date', $month)
                ->whereYear('expense_date', $year)
                ->sum('amount');

            $sharedExpense = $totalMealCount > 0
                ? (($totalExpenses - $mess->expenses()->where('is_market_expense', true)->whereMonth('expense_date', $month)->whereYear('expense_date', $year)->sum('amount')) / count($members))
                : 0;

            $totalDeposit = MemberDeposit::where('mess_id', $mess->id)
                ->where('user_id', $userId)
                ->where('month', $month)
                ->where('year', $year)
                ->sum('amount');

            // Previous carry forward
            $prevSummary = MemberMonthlySummary::where([
                'mess_id' => $mess->id,
                'user_id' => $userId,
                'month'   => $month == 1 ? 12 : $month - 1,
                'year'    => $month == 1 ? $year - 1 : $year,
            ])->first();
            $carryIn = $prevSummary ? -$prevSummary->due_amount : 0; // negative due = credit

            $totalPayable  = $mealCost + $sharedExpense + $marketExpense;
            $netAfterDeposit = $totalDeposit + $carryIn - $totalPayable;
            $dueAmount     = -$netAfterDeposit; // positive = member owes
            $carryOut      = $netAfterDeposit < 0 ? 0 : $netAfterDeposit;

            MemberMonthlySummary::updateOrCreate(
                ['mess_id' => $mess->id, 'user_id' => $userId, 'month' => $month, 'year' => $year],
                [
                    'total_meal_days'  => $memberMeals,
                    'meal_cost'        => round($mealCost, 2),
                    'total_expenses'   => round($sharedExpense, 2),
                    'market_expense'   => round($marketExpense, 2),
                    'total_deposit'    => round($totalDeposit, 2),
                    'carry_forward_in' => round(max(0, $carryIn), 2),
                    'total_payable'    => round($totalPayable, 2),
                    'due_amount'       => round(max(0, $dueAmount), 2),
                    'carry_forward_out'=> round($carryOut, 2),
                    'generated_at'     => now(),
                ]
            );
        }

        return back()->with('success', 'Monthly report generated for ' . date('F Y', mktime(0,0,0,$month,1,$year)) . '.');
    }

    public function memberReports(Mess $mess)
    {
        $this->authorizeMember($mess);

        $reports = \App\Models\MemberReport::where('mess_id', $mess->id)
            ->with(['reporter', 'reported', 'reviewedBy'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $member = Auth::user()->getMembershipIn($mess->id);

        return view('mess.member-reports', compact('mess', 'reports', 'member'));
    }

    public function storeReport(Request $request, Mess $mess)
    {
        $this->authorizeMember($mess);

        $request->validate([
            'reported_id' => 'required|exists:users,id',
            'reason'      => 'required|string|max:255',
            'details'     => 'nullable|string|max:1000',
        ]);

        if ($request->reported_id === Auth::id()) {
            return back()->with('error', 'You cannot report yourself.');
        }

        \App\Models\MemberReport::create([
            'mess_id'     => $mess->id,
            'reporter_id' => Auth::id(),
            'reported_id' => $request->reported_id,
            'reason'      => $request->reason,
            'details'     => $request->details,
            'status'      => 'pending',
        ]);

        return back()->with('success', 'Report submitted. You will earn points if the report is reviewed and resolved.');
    }

    public function reviewReport(Request $request, Mess $mess, \App\Models\MemberReport $report)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'status'      => 'required|in:reviewed,resolved,dismissed',
            'review_note' => 'nullable|string|max:500',
            'points'      => 'nullable|numeric|min:0',
        ]);

        $report->update([
            'status'         => $request->status,
            'review_note'    => $request->review_note,
            'reviewed_by'    => Auth::id(),
            'reviewed_at'    => now(),
            'points_awarded' => $request->points ?? 0,
        ]);

        // Award points to reporter if resolved
        if ($request->status === 'resolved' && $request->points > 0) {
            \App\Models\MemberReward::create([
                'mess_id'     => $mess->id,
                'user_id'     => $report->reporter_id,
                'month'       => now()->month,
                'year'        => now()->year,
                'type'        => 'report_points',
                'points'      => $request->points,
                'reason'      => 'Report resolved: ' . $report->reason,
                'awarded_by'  => Auth::id(),
            ]);
        }

        return back()->with('success', 'Report reviewed.');
    }

    private function authorizeMember(Mess $mess): void
    {
        if (!Auth::user()->getMembershipIn($mess->id)) abort(403);
    }
}
