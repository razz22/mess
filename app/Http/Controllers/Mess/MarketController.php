<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\MarketListItem;
use App\Models\MarketRoutine;
use App\Models\MarketRoutineExchange;
use App\Models\Mess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketController extends Controller
{
    public function index(Mess $mess)
    {
        $this->authorizeMember($mess);

        $month = request('month', now()->month);
        $year  = request('year', now()->year);

        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $monthEnd   = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Load all routines that overlap with this month
        $routines = MarketRoutine::where('mess_id', $mess->id)
            ->where(function ($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('start_date', [$monthStart, $monthEnd])
                  ->orWhereBetween('end_date', [$monthStart, $monthEnd])
                  ->orWhere(function ($q2) use ($monthStart, $monthEnd) {
                      $q2->where('start_date', '<=', $monthStart)
                         ->where('end_date', '>=', $monthEnd);
                  });
            })
            ->with(['assignedTo', 'exchange', 'exchanges.fromUser', 'listItems'])
            ->orderBy('start_date')
            ->get();

        // Build date → routine map for calendar
        $dateRoutineMap = [];
        foreach ($routines as $routine) {
            $cur = $routine->start_date->copy();
            while ($cur <= $routine->end_date) {
                $dateRoutineMap[$cur->format('Y-m-d')] = $routine;
                $cur->addDay();
            }
        }

        // Individual market expenses this month (not from routines, for the quick list)
        $quickExpenses = Expense::where('mess_id', $mess->id)
            ->where('is_market_expense', true)
            ->whereNull('routine_id')
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->with('member')
            ->orderBy('expense_date')
            ->get();

        $members = $mess->activeMembers()->with('user')->get();
        $member  = Auth::user()->getMembershipIn($mess->id);
        $calendarDays = $this->buildCalendar($year, $month);

        return view('mess.market', compact(
            'mess', 'routines', 'dateRoutineMap', 'members', 'member',
            'calendarDays', 'month', 'year', 'quickExpenses'
        ));
    }

    public function assign(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'assigned_to' => 'required|exists:users,id',
        ]);

        // Check overlap with existing routines unless force-override by owner
        $isOwner = $mess->owner_id === Auth::id();
        if (!$isOwner) {
            $overlap = MarketRoutine::where('mess_id', $mess->id)
                ->where('start_date', '<=', $request->end_date)
                ->where('end_date', '>=', $request->start_date)
                ->exists();

            if ($overlap) {
                return back()->with('error', 'Date range overlaps with an existing assignment. Only the owner can override.');
            }
        }

        MarketRoutine::create([
            'mess_id'     => $mess->id,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'assigned_to' => $request->assigned_to,
            'assigned_by' => Auth::id(),
            'status'      => 'pending',
            'notes'       => $request->notes,
        ]);

        return back()->with('success', 'Market duty assigned.');
    }

    public function listItems(Mess $mess, MarketRoutine $routine)
    {
        if ($routine->mess_id !== $mess->id) abort(403);
        $this->authorizeMember($mess);

        $items   = $routine->listItems()->with(['addedBy', 'assignedTo'])->get();
        $member  = Auth::user()->getMembershipIn($mess->id);
        $members = $mess->activeMembers()->with('user')->get();

        return view('mess.market-list', compact('mess', 'routine', 'items', 'member', 'members'));
    }

    public function addListItem(Request $request, Mess $mess, MarketRoutine $routine)
    {
        if ($routine->mess_id !== $mess->id) abort(403);
        $this->authorizeMember($mess);

        $request->validate([
            'item_name'      => 'required|string|max:255',
            'quantity'       => 'nullable|string|max:50',
            'unit'           => 'nullable|string|max:20',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost'    => 'nullable|numeric|min:0',
            'assigned_to'    => 'nullable|exists:users,id',
            'expense_date'   => 'nullable|date',
        ]);

        MarketListItem::create([
            'routine_id'     => $routine->id,
            'mess_id'        => $mess->id,
            'item_name'      => $request->item_name,
            'quantity'       => $request->quantity,
            'unit'           => $request->unit,
            'estimated_cost' => $request->estimated_cost ?? 0,
            'actual_cost'    => $request->actual_cost ?? 0,
            'assigned_to'    => $request->assigned_to,
            'expense_date'   => $request->expense_date,
            'added_by'       => Auth::id(),
        ]);

        // Update routine total
        $total = $routine->listItems()->sum('actual_cost');
        $routine->update(['total_spent' => $total]);

        return back()->with('success', 'Item added.');
    }

    public function updateListItem(Request $request, Mess $mess, MarketListItem $item)
    {
        if ($item->mess_id !== $mess->id) abort(403);

        $data = [];
        if ($request->has('actual_cost')) $data['actual_cost'] = $request->actual_cost;
        if ($request->has('purchased'))   $data['purchased']   = $request->boolean('purchased');
        if ($request->has('assigned_to')) $data['assigned_to'] = $request->assigned_to ?: null;
        if ($request->has('item_name'))   $data['item_name']   = $request->item_name;
        if ($request->has('estimated_cost')) $data['estimated_cost'] = $request->estimated_cost;
        if ($request->has('expense_date'))   $data['expense_date'] = $request->expense_date ?: null;

        $item->update($data);

        $total = $item->routine->listItems()->sum('actual_cost');
        $item->routine->update(['total_spent' => $total]);

        return response()->json(['success' => true, 'total' => $total]);
    }

    public function completeRoutine(Mess $mess, MarketRoutine $routine)
    {
        if ($routine->mess_id !== $mess->id) abort(403);

        $canComplete = Auth::id() === $routine->assigned_to || Auth::user()->isManagerOf($mess->id);
        if (!$canComplete) abort(403);

        $total = $routine->listItems()->sum('actual_cost');
        $routine->update(['status' => 'completed', 'total_spent' => $total]);

        // Create one expense per distinct expense_date in list items, else one for start_date
        $itemsByDate = $routine->listItems()->where('actual_cost', '>', 0)->get()
            ->groupBy(fn($i) => $i->expense_date ? $i->expense_date->format('Y-m-d') : $routine->start_date->format('Y-m-d'));

        if ($itemsByDate->isEmpty() && $total > 0) {
            Expense::firstOrCreate(
                ['mess_id' => $mess->id, 'expense_date' => $routine->start_date, 'routine_id' => $routine->id],
                [
                    'title'             => 'Market - ' . $routine->start_date->format('d M') . ($routine->end_date->ne($routine->start_date) ? ' to ' . $routine->end_date->format('d M Y') : ' ' . $routine->start_date->format('Y')),
                    'amount'            => $total,
                    'added_by'          => Auth::id(),
                    'member_id'         => $routine->assigned_to,
                    'is_market_expense' => true,
                ]
            );
        } else {
            foreach ($itemsByDate as $dateStr => $items) {
                $dayTotal    = $items->sum('actual_cost');
                $assignedTo  = $items->first()->assigned_to ?? $routine->assigned_to;
                $expenseDate = Carbon::parse($dateStr);

                Expense::updateOrCreate(
                    ['mess_id' => $mess->id, 'expense_date' => $expenseDate, 'routine_id' => $routine->id, 'member_id' => $assignedTo],
                    [
                        'title'             => 'Market - ' . $expenseDate->format('d M Y'),
                        'amount'            => $dayTotal,
                        'added_by'          => Auth::id(),
                        'is_market_expense' => true,
                    ]
                );
            }
        }

        return back()->with('success', 'Routine completed and expense recorded.');
    }

    public function addQuickExpense(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'item_name'    => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'member_id'    => 'required|exists:users,id',
        ]);

        Expense::create([
            'mess_id'           => $mess->id,
            'title'             => $request->item_name,
            'amount'            => $request->amount,
            'expense_date'      => $request->expense_date,
            'added_by'          => Auth::id(),
            'member_id'         => $request->member_id,
            'is_market_expense' => true,
        ]);

        return back()->with('success', 'Market expense added.');
    }

    public function requestExchange(Request $request, Mess $mess, MarketRoutine $routine)
    {
        // Any active member can offer to exchange (take over or request swap)
        $member = Auth::user()->getMembershipIn($mess->id);
        if (!$member) abort(403);

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        // If current user IS the assigned person, they can request exchange with specific person
        // If another member, they volunteer to take over
        $fromUser = Auth::id();
        $toUser   = $routine->assigned_to; // offer goes to assigned person (or manager approves)

        if ($fromUser === $routine->assigned_to) {
            // Assigned person wants to swap with someone specific
            $request->validate(['to_user_id' => 'required|exists:users,id']);
            $toUser = $request->to_user_id;
        }

        // Check no pending exchange already
        $existing = MarketRoutineExchange::where('routine_id', $routine->id)
            ->where('status', 'pending')
            ->where('from_user_id', $fromUser)
            ->exists();

        if ($existing) {
            return back()->with('error', 'You already have a pending exchange request for this routine.');
        }

        MarketRoutineExchange::create([
            'routine_id'   => $routine->id,
            'from_user_id' => $fromUser,
            'to_user_id'   => $toUser,
            'reason'       => $request->reason,
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Exchange request sent.');
    }

    public function respondExchange(Request $request, Mess $mess, MarketRoutineExchange $exchange)
    {
        // Assigned person or manager can respond
        $canRespond = $exchange->to_user_id === Auth::id()
            || $exchange->routine->assigned_to === Auth::id()
            || Auth::user()->isManagerOf($mess->id);
        if (!$canRespond) abort(403);

        $request->validate(['action' => 'required|in:accept,reject']);

        if ($request->action === 'accept') {
            $exchange->update(['status' => 'accepted', 'responded_at' => now()]);
            // Reassign: the person who made the request takes over
            $exchange->routine->update([
                'assigned_to' => $exchange->from_user_id,
                'status'      => 'pending',
            ]);
        } else {
            $exchange->update(['status' => 'rejected', 'responded_at' => now()]);
        }

        return back()->with('success', 'Exchange ' . $request->action . 'ed.');
    }

    private function buildCalendar(int $year, int $month): array
    {
        $firstDay    = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDow    = $firstDay->dayOfWeek;

        $days = [];
        for ($i = 0; $i < $startDow; $i++) {
            $days[] = null;
        }
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $days[] = Carbon::createFromDate($year, $month, $d);
        }
        return $days;
    }

    private function authorizeMember(Mess $mess): void
    {
        if (!Auth::user()->getMembershipIn($mess->id)) abort(403);
    }
}
