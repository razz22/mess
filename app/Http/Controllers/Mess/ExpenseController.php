<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Mess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    use AuthorizesMessAccess;
    public function index(Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403, 'Managers only.');

        $month = (int) request('month', now()->month);
        $year  = (int) request('year', now()->year);

        // Auto-seed recurring categories that have no expense entry for this month yet
        $recurringCats = $mess->expenseCategories()
            ->where('is_active', true)
            ->where('is_recurring', true)
            ->whereNotNull('recurring_amount')
            ->get();

        foreach ($recurringCats as $cat) {
            $exists = Expense::where('mess_id', $mess->id)
                ->where('category_id', $cat->id)
                ->where('is_recurring_entry', true)
                ->whereMonth('expense_date', $month)
                ->whereYear('expense_date', $year)
                ->exists();

            if (!$exists) {
                Expense::create([
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

        $expenses = Expense::where('mess_id', $mess->id)
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->with(['category', 'addedBy', 'member'])
            ->orderByDesc('expense_date')
            ->get();

        $categories   = $mess->expenseCategories()->where('is_active', true)->get();
        $totalExpense = $expenses->sum('amount');
        $member       = Auth::user()->getMembershipIn($mess->id);

        // Group by category
        $byCategory = $expenses->groupBy('category_id')->map(fn($g) => $g->sum('amount'));

        return view('mess.expenses', compact(
            'mess', 'expenses', 'categories', 'totalExpense', 'member', 'byCategory', 'month', 'year'
        ));
    }

    public function store(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'category_id'  => 'nullable|exists:expense_categories,id',
        ]);

        Expense::create([
            'mess_id'      => $mess->id,
            'category_id'  => $request->category_id,
            'title'        => $request->title,
            'amount'       => $request->amount,
            'expense_date' => $request->expense_date,
            'description'  => $request->description,
            'added_by'     => Auth::id(),
        ]);

        return back()->with('success', 'Expense added successfully.');
    }

    public function update(Request $request, Mess $mess, Expense $expense)
    {
        if ((int) $expense->mess_id !== (int) $mess->id) abort(403);
        $member           = Auth::user()->getMembershipIn($mess->id);
        $isOwnerOrManager = Auth::user()->is_super_admin
            || ($member && in_array($member->role, ['owner', 'manager']));
        $isOwn = (int) $expense->added_by === (int) Auth::id();
        if (!$isOwnerOrManager && !$isOwn) abort(403);

        $request->validate([
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
        ]);

        $expense->update($request->only('title', 'amount', 'expense_date', 'category_id', 'description'));
        return back()->with('success', 'Expense updated.');
    }

    public function destroy(Mess $mess, Expense $expense)
    {
        if ((int) $expense->mess_id !== (int) $mess->id) abort(403);
        $member           = Auth::user()->getMembershipIn($mess->id);
        $isOwnerOrManager = Auth::user()->is_super_admin
            || ($member && in_array($member->role, ['owner', 'manager']));
        $isOwn = (int) $expense->added_by === (int) Auth::id();
        if (!$isOwnerOrManager && !$isOwn) abort(403);
        $expense->delete();
        return back()->with('success', 'Expense deleted.');
    }

    // Expense Categories
    public function storeCategory(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'name'             => 'required|string|max:100',
            'recurring_amount' => 'nullable|numeric|min:0.01',
        ]);

        ExpenseCategory::create([
            'mess_id'          => $mess->id,
            'name'             => $request->name,
            'icon'             => $request->icon ?? 'ti-coins',
            'color'            => $request->color ?? 'primary',
            'is_recurring'     => $request->boolean('is_recurring'),
            'recurring_amount' => $request->boolean('is_recurring') ? $request->recurring_amount : null,
        ]);

        return back()->with('success', 'Category added.');
    }

    public function updateCategory(Request $request, Mess $mess, ExpenseCategory $category)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);
        if ((int) $category->mess_id !== (int) $mess->id) abort(403);

        $request->validate([
            'name'             => 'required|string|max:100',
            'recurring_amount' => 'nullable|numeric|min:0.01',
        ]);

        $isRecurring = $request->boolean('is_recurring');

        $category->update([
            'name'             => $request->name,
            'icon'             => $request->icon ?? $category->icon,
            'color'            => $request->color ?? $category->color,
            'is_recurring'     => $isRecurring,
            'recurring_amount' => $isRecurring ? $request->recurring_amount : null,
        ]);

        return back()->with('success', 'Category updated.');
    }

    public function destroyCategory(Mess $mess, ExpenseCategory $category)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);
        if ((int) $category->mess_id !== (int) $mess->id) abort(403);

        // If expenses use this category, just null them out rather than blocking
        $category->expenses()->update(['category_id' => null]);
        $category->delete();

        return back()->with('success', '"' . $category->name . '" category deleted.');
    }

    private function authorizeMember(Mess $mess): void
    {
        if (!Auth::user()->getMembershipIn($mess->id)) abort(403);
    }
}
