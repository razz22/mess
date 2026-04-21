<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\MealAttendance;
use App\Models\MealSchedule;
use App\Models\MemberExpenseExclusion as MemberCategoryExclusion;
use App\Models\MemberDeposit;
use App\Models\MemberMonthlySummary;
use App\Models\Mess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use AuthorizesMessAccess;
    public function monthly(Mess $mess)
    {
        $this->authorizeMember($mess);

        $month = request('month', now()->month);
        $year  = request('year', now()->year);

        $myId    = Auth::id();
        $members = $mess->activeMembers()->with('user')->get()
            ->sortBy(fn($m) => $m->user_id === $myId ? 0 : 1)->values();

        // Auto-seed recurring category expenses for this month
        $recurringCats = $mess->expenseCategories()
            ->where('is_active', true)
            ->where('is_recurring', true)
            ->whereNotNull('recurring_amount')
            ->get();

        foreach ($recurringCats as $cat) {
            $exists = \App\Models\Expense::where('mess_id', $mess->id)
                ->where('category_id', $cat->id)
                ->where('is_recurring_entry', true)
                ->whereMonth('expense_date', $month)
                ->whereYear('expense_date', $year)
                ->exists();

            if (!$exists) {
                \App\Models\Expense::create([
                    'mess_id'            => $mess->id,
                    'category_id'        => $cat->id,
                    'title'              => $cat->name . ' (Recurring)',
                    'amount'             => $cat->recurring_amount,
                    'expense_date'       => \Carbon\Carbon::createFromDate($year, $month, 1)->toDateString(),
                    'added_by'           => Auth::id(),
                    'is_recurring_entry' => true,
                ]);
            }
        }

        // Auto-generate summaries on every page load (managers only)
        if (Auth::user()->isManagerOf($mess->id)) {
            $this->doGenerate($mess, $month, $year);
        }

        // Load summaries
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

        // Meal attendance summary per member (sum quantity, not count rows)
        $mealByMember = MealAttendance::whereHas('schedule', fn($q) =>
                $q->where('mess_id', $mess->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
            )
            ->select('user_id', DB::raw('SUM(quantity) as meal_count'))
            ->groupBy('user_id')
            ->pluck('meal_count', 'user_id');

        $totalMealCount  = $mealByMember->sum();
        $costMode        = $mess->settings?->meal_cost_mode ?? 'monthly';
        $totalMarket     = $mess->expenses()
            ->whereMonth('expense_date', $month)->whereYear('expense_date', $year)
            ->where('is_market_expense', true)->sum('amount');
        $perMealCost     = ($costMode === 'monthly' && $totalMealCount > 0)
            ? ($totalMarket / $totalMealCount)
            : 0; // daily mode: no single rate to display

        // Cash in hand = total deposits - total expenses
        $cashInHand = $totalDeposits - $totalExpenses;

        // All expenses by category (for the bottom display panel)
        $expensesByCategory = $mess->expenses()
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn($g) => ['name' => $g->first()->category?->name ?? 'Uncategorized', 'amount' => $g->sum('amount')]);

        $member        = Auth::user()->getMembershipIn($mess->id);
        $allCategories = $mess->expenseCategories()->where('is_active', true)->get();

        // Non-market expense amounts per category (used for the per-member exclusion modal)
        // Only non-market expenses count toward Expense Cost, so only those categories should be excludable
        $nonMarketByCategory = $mess->expenses()
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->where('is_market_expense', false)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $totalMembers = $mess->activeMembers()->count();

        $categoriesForModal = $allCategories
            ->filter(fn($c) => isset($nonMarketByCategory[$c->id]) && $nonMarketByCategory[$c->id] > 0)
            ->map(fn($c) => [
                'id'           => $c->id,
                'name'         => $c->name,
                'is_recurring' => (bool) $c->is_recurring,
                'total'        => (float) $nonMarketByCategory[$c->id],
                'per_head'     => $totalMembers > 0 ? round($nonMarketByCategory[$c->id] / $totalMembers, 2) : 0,
            ])->values();

        // Category exclusions per member: [userId => [categoryId, ...]]
        $memberCategoryExclusions = MemberCategoryExclusion::where('mess_id', $mess->id)
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->groupBy('user_id')
            ->map(fn($g) => $g->pluck('category_id')->toArray());

        // Extra stats for top summary block
        $totalNonMarket = $totalExpenses - $totalMarket;
        $totalDue       = $summaries->sum(fn($s) => max(0, $s->due_amount));
        $totalExtra     = $summaries->sum(fn($s) => max(0, -$s->due_amount));

        return view('mess.monthly-report', compact(
            'mess', 'members', 'summaries', 'month', 'year',
            'totalExpenses', 'totalDeposits', 'totalMeals',
            'mealByMember', 'totalMealCount', 'perMealCost', 'cashInHand', 'costMode',
            'totalMarket', 'totalNonMarket', 'totalDue', 'totalExtra',
            'expensesByCategory', 'member', 'memberCategoryExclusions', 'allCategories', 'categoriesForModal'
        ));
    }

    public function generate(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $month = $request->month ?? now()->month;
        $year  = $request->year  ?? now()->year;

        $this->doGenerate($mess, $month, $year);

        return back()->with('success', 'Report refreshed for ' . date('F Y', mktime(0,0,0,$month,1,$year)) . '.');
    }

    public function toggleSharedExpense(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month'   => 'required|integer|min:1|max:12',
            'year'    => 'required|integer',
        ]);

        // Owner cannot be excluded from shared expenses
        if ((int) $request->user_id === (int) $mess->owner_id) {
            return response()->json(['success' => false, 'message' => 'The owner cannot be excluded from shared expenses.'], 422);
        }

        // Ensure a summary row exists before toggling
        $summary = MemberMonthlySummary::firstOrCreate(
            ['mess_id' => $mess->id, 'user_id' => $request->user_id, 'month' => $request->month, 'year' => $request->year],
            ['exclude_from_shared' => false]
        );

        $summary->update(['exclude_from_shared' => !$summary->exclude_from_shared]);

        // Re-generate so totals reflect the change immediately
        $this->doGenerate($mess, $request->month, $request->year);

        return response()->json(['success' => true, 'excluded' => $summary->fresh()->exclude_from_shared]);
    }

    public function toggleCategoryExpense(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'category_id' => 'required|exists:expense_categories,id',
            'month'       => 'required|integer|min:1|max:12',
            'year'        => 'required|integer',
        ]);

        $existing = MemberCategoryExclusion::where([
            'mess_id'     => $mess->id,
            'user_id'     => $request->user_id,
            'category_id' => $request->category_id,
            'month'       => $request->month,
            'year'        => $request->year,
        ])->first();

        if ($existing) {
            $existing->delete();
            $excluded = false;
        } else {
            MemberCategoryExclusion::create([
                'mess_id'     => $mess->id,
                'user_id'     => $request->user_id,
                'category_id' => $request->category_id,
                'month'       => $request->month,
                'year'        => $request->year,
            ]);
            $excluded = true;
        }

        $this->doGenerate($mess, $request->month, $request->year);

        return response()->json(['success' => true, 'excluded' => $excluded]);
    }

    public function payExtra(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month'   => 'required|integer|min:1|max:12',
            'year'    => 'required|integer',
        ]);

        $summary = MemberMonthlySummary::where([
            'mess_id' => $mess->id,
            'user_id' => $request->user_id,
            'month'   => $request->month,
            'year'    => $request->year,
        ])->firstOrFail();

        if ($summary->due_amount >= 0) {
            return response()->json(['success' => false, 'message' => 'No extra balance to pay out.'], 422);
        }

        $summary->update(['status' => 'paid_out']);

        return response()->json(['success' => true, 'message' => 'Extra amount marked as paid out to member.']);
    }

    public function carryExtraAsDeposit(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month'   => 'required|integer|min:1|max:12',
            'year'    => 'required|integer',
            'note'    => 'nullable|string|max:255',
        ]);

        $summary = MemberMonthlySummary::where([
            'mess_id' => $mess->id,
            'user_id' => $request->user_id,
            'month'   => $request->month,
            'year'    => $request->year,
        ])->firstOrFail();

        if ($summary->due_amount >= 0) {
            return response()->json(['success' => false, 'message' => 'No extra balance to carry forward.'], 422);
        }

        $extraAmount = abs($summary->due_amount);
        $nextMonth   = $request->month == 12 ? 1  : $request->month + 1;
        $nextYear    = $request->month == 12 ? $request->year + 1 : $request->year;

        MemberDeposit::create([
            'mess_id'      => $mess->id,
            'user_id'      => $request->user_id,
            'amount'       => $extraAmount,
            'month'        => $nextMonth,
            'year'         => $nextYear,
            'deposit_date' => \Carbon\Carbon::create($nextYear, $nextMonth, 1)->toDateString(),
            'note'         => $request->note ?? 'Carried forward from ' . date('F Y', mktime(0, 0, 0, $request->month, 1, $request->year)),
            'received_by'  => Auth::id(),
        ]);

        $summary->update(['status' => 'carried_forward']);

        return response()->json(['success' => true, 'message' => '৳' . number_format($extraAmount, 2) . ' added as deposit for ' . date('F Y', mktime(0, 0, 0, $nextMonth, 1, $nextYear)) . '.']);
    }

    private function doGenerate(Mess $mess, int $month, int $year): void
    {
        $memberIds = $mess->activeMembers()->pluck('user_id');

        // Load existing global exclusion flags (persisted per member per month)
        $exclusions = MemberMonthlySummary::where('mess_id', $mess->id)
            ->where('month', $month)
            ->where('year', $year)
            ->whereIn('user_id', $memberIds)
            ->pluck('exclude_from_shared', 'user_id');

        // Load per-category exclusions: [userId => [categoryId, ...]]
        $categoryExclusions = MemberCategoryExclusion::where('mess_id', $mess->id)
            ->where('month', $month)
            ->where('year', $year)
            ->whereIn('user_id', $memberIds)
            ->get()
            ->groupBy('user_id')
            ->map(fn($g) => $g->pluck('category_id')->toArray());

        $costMode = $mess->settings?->meal_cost_mode ?? 'monthly';

        $totalExpenses = $mess->expenses()
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount');

        $mealByMember = MealAttendance::whereHas('schedule', fn($q) =>
                $q->where('mess_id', $mess->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
            )
            ->select('user_id', DB::raw('SUM(quantity) as meal_count'))
            ->groupBy('user_id')
            ->pluck('meal_count', 'user_id');

        $totalMealCount = $mealByMember->sum();

        // --- Meal cost calculation ---
        // Monthly mode: total market expenses / total meals = uniform per-meal rate
        // Daily mode:   for each day, (market expenses that day / meals that day) × member's meals = member's cost
        $memberMealCosts = [];

        if ($costMode === 'daily') {
            // Load schedules for this month
            $monthSchedules = MealSchedule::where('mess_id', $mess->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->select('id', 'date')
                ->get()
                ->keyBy('id');

            // Load all attendances for those schedules
            $monthAttendances = MealAttendance::whereIn('meal_schedule_id', $monthSchedules->keys())
                ->select('meal_schedule_id', 'user_id', DB::raw('SUM(quantity) as qty'))
                ->groupBy('meal_schedule_id', 'user_id')
                ->get();

            // Daily total meals across all members
            $dailyTotalMeals = [];
            foreach ($monthAttendances as $att) {
                $d = \Carbon\Carbon::parse($monthSchedules[$att->meal_schedule_id]->date)->toDateString();
                $dailyTotalMeals[$d] = ($dailyTotalMeals[$d] ?? 0) + $att->qty;
            }

            // Daily market expenses
            $dailyMarket = $mess->expenses()
                ->whereMonth('expense_date', $month)
                ->whereYear('expense_date', $year)
                ->where('is_market_expense', true)
                ->select('expense_date', DB::raw('SUM(amount) as total'))
                ->groupBy('expense_date')
                ->get()
                ->mapWithKeys(fn($r) => [\Carbon\Carbon::parse($r->expense_date)->toDateString() => (float)$r->total]);

            // Per-member meal cost = sum over days of (member meals that day × daily rate)
            foreach ($monthAttendances as $att) {
                $d          = \Carbon\Carbon::parse($monthSchedules[$att->meal_schedule_id]->date)->toDateString();
                $dayTotal   = $dailyTotalMeals[$d] ?? 0;
                $dayMarket  = $dailyMarket[$d] ?? 0;
                $dailyRate  = $dayTotal > 0 ? ($dayMarket / $dayTotal) : 0;
                $memberMealCosts[$att->user_id] = ($memberMealCosts[$att->user_id] ?? 0) + ($att->qty * $dailyRate);
            }
            $perMealCost = 0; // not a single rate in daily mode
        } else {
            // Monthly: total market expenses / total meals
            $totalMarketForMeal = $mess->expenses()
                ->whereMonth('expense_date', $month)
                ->whereYear('expense_date', $year)
                ->where('is_market_expense', true)
                ->sum('amount');
            $perMealCost = $totalMealCount > 0 ? ($totalMarketForMeal / $totalMealCount) : 0;
        }

        // Non-market expenses grouped by category
        $expensesByCategory = $mess->expenses()
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->where('is_market_expense', false)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        // Per-category per-head cost: only members not globally excluded AND not category-excluded pay
        $categoryPerHead = [];
        foreach ($expensesByCategory as $catId => $total) {
            $payingCount = $memberIds->filter(function ($id) use ($exclusions, $categoryExclusions, $catId) {
                if ($exclusions[$id] ?? false) return false;
                return !in_array($catId, $categoryExclusions[$id] ?? []);
            })->count();
            $categoryPerHead[$catId] = $payingCount > 0 ? ($total / $payingCount) : 0;
        }

        foreach ($memberIds as $userId) {
            // Owner is never excluded from shared expenses
            $excluded = $userId === $mess->owner_id ? false : (bool)($exclusions[$userId] ?? false);
            $memberMeals = (float)($mealByMember[$userId] ?? 0);
            $mealCost    = $costMode === 'daily'
                ? (float)($memberMealCosts[$userId] ?? 0)
                : $memberMeals * $perMealCost;

            if ($excluded) {
                $memberShared = 0;
            } else {
                $memberCatExcls = $categoryExclusions[$userId] ?? [];
                $memberShared   = 0;
                foreach ($categoryPerHead as $catId => $perHead) {
                    if (!in_array($catId, $memberCatExcls)) {
                        $memberShared += $perHead;
                    }
                }
            }

            $totalDeposit = MemberDeposit::where('mess_id', $mess->id)
                ->where('user_id', $userId)
                ->where('month', $month)
                ->where('year', $year)
                ->sum('amount');

            $prevSummary = MemberMonthlySummary::where([
                'mess_id' => $mess->id,
                'user_id' => $userId,
                'month'   => $month == 1 ? 12 : $month - 1,
                'year'    => $month == 1 ? $year - 1 : $year,
            ])->first();
            // If previous month extra was paid out or carried forward as a deposit, don't double-carry it
            $carryIn = 0;
            if ($prevSummary && !in_array($prevSummary->status, ['paid_out', 'carried_forward'])) {
                $carryIn = -$prevSummary->due_amount;
            }

            // Total payable = Meal Cost (from Market Routine) + Expense Cost (shared non-meal expenses)
            $totalPayable    = $mealCost + $memberShared;
            $netAfterDeposit = $totalDeposit + $carryIn - $totalPayable;
            $dueAmount       = -$netAfterDeposit;
            $carryOut        = $netAfterDeposit < 0 ? 0 : $netAfterDeposit;

            MemberMonthlySummary::updateOrCreate(
                ['mess_id' => $mess->id, 'user_id' => $userId, 'month' => $month, 'year' => $year],
                [
                    'total_meal_days'     => $memberMeals,
                    'meal_cost'           => round($mealCost, 2),
                    'total_expenses'      => round($memberShared, 2),
                    'total_deposit'       => round($totalDeposit, 2),
                    'carry_forward_in'    => round(max(0, $carryIn), 2),
                    'total_payable'       => round($totalPayable, 2),
                    'due_amount'          => round($dueAmount, 2),
                    'carry_forward_out'   => round($carryOut, 2),
                    'exclude_from_shared' => $excluded,
                    'generated_at'        => now(),
                ]
            );
        }
    }

    public function memberDetail(Mess $mess)
    {
        $this->authorizeMember($mess);

        $month  = (int) request('month', now()->month);
        $year   = (int) request('year', now()->year);
        $userId = (int) request('user_id', Auth::id());

        // Only manager/owner/super-admin can view other members' details
        $viewer = Auth::user();
        if ($userId !== $viewer->id && !$viewer->isManagerOf($mess->id) && !$viewer->is_super_admin) {
            abort(403);
        }

        $targetMember = $mess->activeMembers()->with('user')->where('user_id', $userId)->firstOrFail();
        $summary      = MemberMonthlySummary::where(['mess_id' => $mess->id, 'user_id' => $userId, 'month' => $month, 'year' => $year])->first();

        // Meal attendance day-by-day
        $attendances = MealAttendance::whereHas('schedule', fn($q) =>
                $q->where('mess_id', $mess->id)->whereMonth('date', $month)->whereYear('date', $year)
            )
            ->where('user_id', $userId)
            ->with(['schedule'])
            ->orderBy('created_at')
            ->get();

        // Group by date → meal type (schedule->type is the meal type name string)
        $attendanceByDate = [];
        foreach ($attendances as $att) {
            $dateKey  = $att->schedule->date->format('Y-m-d');
            $typeName = $att->schedule->type ?? '—';
            $attendanceByDate[$dateKey][$typeName] = $att;
        }
        ksort($attendanceByDate);

        // Meal types active this month
        $mealTypes = \App\Models\MessMealType::where('mess_id', $mess->id)
            ->where('is_active', true)->orderBy('sort_order')->get();

        // All non-market expenses this month grouped by category
        $costMode = $mess->settings?->meal_cost_mode ?? 'monthly';
        $allExpenses = \App\Models\Expense::where('mess_id', $mess->id)
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->where('is_market_expense', false)
            ->with('category')
            ->orderBy('expense_date')
            ->get();

        $totalMembers = $mess->activeMembers()->count();

        // Category exclusions for this member
        $catExclIds = MemberCategoryExclusion::where(['mess_id' => $mess->id, 'user_id' => $userId, 'month' => $month, 'year' => $year])
            ->pluck('category_id')->toArray();

        // Build expense lines with per-head share
        $nonMarketByCategory = $allExpenses->groupBy('category_id')
            ->map(fn($g) => $g->sum('amount'));

        // Count paying members per category (those not excluded from that category or globally)
        $globalExclusions = MemberMonthlySummary::where(['mess_id' => $mess->id, 'month' => $month, 'year' => $year])
            ->where('exclude_from_shared', true)->pluck('user_id')->toArray();
        $allCatExclusions = MemberCategoryExclusion::where(['mess_id' => $mess->id, 'month' => $month, 'year' => $year])
            ->get()->groupBy('user_id')->map(fn($g) => $g->pluck('category_id')->toArray());
        $allMemberIds = $mess->activeMembers()->pluck('user_id');

        $expenseLines = [];
        foreach ($allExpenses->groupBy('category_id') as $catId => $catExpenses) {
            if (in_array($catId, $catExclIds)) continue; // member excluded from this category
            if ((bool)($globalExclusions[$userId] ?? false)) continue;

            $catTotal    = $catExpenses->sum('amount');
            $payingCount = $allMemberIds->filter(function ($id) use ($globalExclusions, $allCatExclusions, $catId) {
                if (in_array($id, $globalExclusions)) return false;
                return !in_array($catId, $allCatExclusions[$id] ?? []);
            })->count();
            $perHead     = $payingCount > 0 ? $catTotal / $payingCount : 0;
            $catName     = $catExpenses->first()->category?->name ?? 'Uncategorized';

            $expenseLines[] = [
                'category'  => $catName,
                'total'     => $catTotal,
                'per_head'  => $perHead,
                'items'     => $catExpenses->map(fn($e) => [
                    'title'  => $e->title,
                    'amount' => $e->amount,
                    'date'   => $e->expense_date->format('d M'),
                ])->toArray(),
            ];
        }

        // Market (meal cost) expenses
        $marketExpenses = \App\Models\Expense::where('mess_id', $mess->id)
            ->whereMonth('expense_date', $month)->whereYear('expense_date', $year)
            ->where('is_market_expense', true)
            ->with('member')
            ->orderBy('expense_date')->get();

        // Deposits this month
        $deposits = MemberDeposit::where(['mess_id' => $mess->id, 'user_id' => $userId, 'month' => $month, 'year' => $year])
            ->orderBy('created_at')->get();

        $monthLabel = \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y');

        return view('mess.member-detail-report', compact(
            'mess', 'targetMember', 'summary', 'month', 'year', 'monthLabel',
            'attendanceByDate', 'mealTypes', 'expenseLines', 'marketExpenses',
            'deposits', 'costMode', 'totalMembers'
        ));
    }

    public function allMembersDetail(Mess $mess)
    {
        $this->authorizeMember($mess);

        $month = (int) request('month', now()->month);
        $year  = (int) request('year', now()->year);

        $myId    = Auth::id();
        $members = $mess->activeMembers()->with('user')->get()
            ->sortBy(fn($m) => $m->user_id === $myId ? 0 : 1)->values();

        $summaries = MemberMonthlySummary::where('mess_id', $mess->id)
            ->where('month', $month)->where('year', $year)
            ->get()->keyBy('user_id');

        $marketExpenses = \App\Models\Expense::where('mess_id', $mess->id)
            ->whereMonth('expense_date', $month)->whereYear('expense_date', $year)
            ->where('is_market_expense', true)
            ->with('member')->orderBy('expense_date')->get();

        $monthLabel = \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y');

        return view('mess.all-members-report', compact(
            'mess', 'members', 'summaries', 'month', 'year', 'monthLabel', 'marketExpenses'
        ));
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
