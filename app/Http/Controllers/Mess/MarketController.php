<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
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
    use AuthorizesMessAccess;
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
        $user   = Auth::user();
        $member = $user->getMembershipIn($mess->id);
        if (!$member && !$user->is_super_admin) abort(403);

        $isManager = $user->isManagerOf($mess->id);

        $request->validate([
            'dates'       => 'required|array|min:1',
            'dates.*'     => 'required|date',
            'assigned_to' => 'required|exists:users,id',
        ]);

        // Non-managers can only assign themselves
        $assignedTo = $isManager ? $request->assigned_to : $user->id;

        $isOwner = $mess->owner_id === $user->id || $user->is_super_admin;
        $today   = now()->toDateString();
        $created = 0;
        $skipped = 0;

        foreach ($request->dates as $date) {
            // Non-managers cannot assign past dates
            if (!$isManager && $date < $today) {
                $skipped++;
                continue;
            }

            if (!$isOwner) {
                $overlap = MarketRoutine::where('mess_id', $mess->id)
                    ->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->exists();

                if ($overlap) {
                    $skipped++;
                    continue;
                }
            }

            MarketRoutine::create([
                'mess_id'     => $mess->id,
                'start_date'  => $date,
                'end_date'    => $date,
                'assigned_to' => $assignedTo,
                'assigned_by' => Auth::id(),
                'status'      => 'pending',
                'notes'       => $request->notes,
            ]);
            $created++;
        }

        if ($created === 0) {
            return back()->with('error', 'All selected dates overlap with existing assignments. Only the owner can override.');
        }

        $msg = $created . ' date' . ($created > 1 ? 's' : '') . ' assigned.';
        if ($skipped) $msg .= " {$skipped} date(s) skipped due to overlap.";

        return back()->with('success', $msg);
    }

    public function listItems(Mess $mess, MarketRoutine $routine)
    {
        if ((int) $routine->mess_id !== (int) $mess->id) abort(403);
        $this->authorizeMember($mess);

        $items   = $routine->listItems()->with(['addedBy', 'assignedTo'])->get();
        $member  = Auth::user()->getMembershipIn($mess->id);
        $members = $mess->activeMembers()->with('user')->get();

        return view('mess.market-list', compact('mess', 'routine', 'items', 'member', 'members'));
    }

    public function addListItem(Request $request, Mess $mess, MarketRoutine $routine)
    {
        if ((int) $routine->mess_id !== (int) $mess->id) abort(403);
        $this->authorizeMember($mess);

        $request->validate([
            'items'                  => 'required|array|min:1',
            'items.*.item_name'      => 'required|string|max:255',
            'items.*.quantity'       => 'nullable|string|max:50',
            'items.*.unit'           => 'nullable|string|max:20',
            'items.*.actual_cost'    => 'nullable|numeric|min:0',
            'items.*.assigned_to'    => 'nullable|exists:users,id',
            'items.*.expense_date'   => 'nullable|date',
        ]);

        foreach ($request->items as $item) {
            MarketListItem::create([
                'routine_id'     => $routine->id,
                'mess_id'        => $mess->id,
                'item_name'      => $item['item_name'],
                'quantity'       => $item['quantity'] ?? null,
                'unit'           => $item['unit'] ?? null,
                'actual_cost'    => $item['actual_cost'] ?? 0,
                'assigned_to'    => $item['assigned_to'] ?? null,
                'expense_date'   => $routine->start_date,
                'is_approved'    => false,
                'added_by'       => Auth::id(),
            ]);
        }

        $total = $routine->listItems()->sum('actual_cost');

        // If already approved and new items added, flag for re-approval
        $newStatus = in_array($routine->status, ['approved', 'completed'])
            ? 'pending_reapproval'
            : $routine->status;

        $routine->update(['total_spent' => $total, 'status' => $newStatus]);

        $count = count($request->items);
        $suffix = $newStatus === 'pending_reapproval' ? ' The list now requires re-approval.' : '';
        return back()->with('success', $count . ' item' . ($count > 1 ? 's' : '') . ' added successfully.' . $suffix);
    }

    public function unassign(Request $request, Mess $mess, MarketRoutine $routine)
    {
        if ((int) $routine->mess_id !== (int) $mess->id) abort(403);

        $user         = Auth::user();
        $isSuperAdmin = $user->is_super_admin;
        $isOwner      = $mess->owner_id === $user->id;
        $isManager    = $user->isManagerOf($mess->id);
        $isAssigned   = $routine->assigned_to === $user->id;

        // Access: super admin, owner, manager, or the assigned member
        if (! $isSuperAdmin && ! $isManager && ! $isAssigned) {
            abort(403, 'You do not have permission to remove this assignment.');
        }

        // Completed routines: only owner or super admin can remove
        if ($routine->status === 'completed' && ! $isSuperAdmin && ! $isOwner) {
            return back()->with('error', 'Completed routines can only be removed by the owner or super admin.');
        }

        // Assigned member (non-manager): can only remove if no items
        if ($isAssigned && ! $isManager && ! $isSuperAdmin && ! $isOwner) {
            if ($routine->listItems()->exists()) {
                return back()->with('error', 'Remove all list items first before removing your assignment.');
            }
        }

        // Manager (non-owner/super): cannot remove if has items (only owner/super can force)
        if ($routine->status === 'pending' && $routine->listItems()->exists()) {
            if (! $isSuperAdmin && ! $isOwner) {
                return back()->with('error', 'Remove all list items first before unassigning this routine.');
            }
        }

        // Delete list items and expenses tied to this routine, then the routine itself
        \App\Models\Expense::where('routine_id', $routine->id)->delete();
        $routine->listItems()->delete();
        $routine->delete();

        return back()->with('success', 'Market assignment removed.');
    }

    public function updateListItem(Request $request, Mess $mess, MarketListItem $item)
    {
        if ((int) $item->mess_id !== (int) $mess->id) abort(403);

        $data = [];
        if ($request->has('actual_cost')) $data['actual_cost'] = $request->actual_cost;
        if ($request->has('purchased'))   $data['purchased']   = $request->boolean('purchased');
        // assigned_to is always the routine's assigned member — not user-editable
        if ($request->has('item_name'))   $data['item_name']   = $request->item_name;
        if ($request->has('quantity'))    $data['quantity']    = $request->quantity ?: null;
        if ($request->has('unit'))        $data['unit']        = $request->unit ?: null;
        // expense_date is always the routine's start_date — not user-editable

        $item->update($data);

        $total = $item->routine->listItems()->sum('actual_cost');
        $item->routine->update(['total_spent' => $total]);

        return response()->json(['success' => true, 'total' => $total]);
    }

    public function deleteListItem(Request $request, Mess $mess, MarketListItem $item)
    {
        if ((int) $item->mess_id !== (int) $mess->id) abort(403);

        $isManager    = Auth::user()->isManagerOf($mess->id);
        $isSuperAdmin = Auth::user()->is_super_admin;
        $isAssigned   = (int) $item->routine->assigned_to === (int) Auth::id();

        // Members can only delete unapproved items they added
        if (!$isManager && !$isSuperAdmin) {
            if ($item->is_approved) {
                return response()->json(['error' => 'Approved items cannot be deleted.'], 403);
            }
            if (!$isAssigned && (int) $item->added_by !== (int) Auth::id()) {
                return response()->json(['error' => 'You can only delete items you added.'], 403);
            }
        }

        $routine = $item->routine;
        $item->delete();

        $total = $routine->listItems()->sum('actual_cost');
        $routine->update(['total_spent' => $total]);

        return response()->json(['success' => true, 'total' => $total]);
    }

    public function completeRoutine(Mess $mess, MarketRoutine $routine)
    {
        if ((int) $routine->mess_id !== (int) $mess->id) abort(403);

        $canComplete = Auth::user()->isManagerOf($mess->id) || Auth::user()->is_super_admin;
        if (!$canComplete) abort(403, 'Only a manager or owner can approve this list.');

        $total = $routine->listItems()->sum('actual_cost');
        $routine->update(['status' => 'approved', 'total_spent' => $total]);

        // Mark all current items as approved
        $routine->listItems()->update(['is_approved' => true]);

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

        return back()->with('success', 'Shopping list approved and expense recorded.');
    }

    public function addQuickExpense(Request $request, Mess $mess)
    {
        $user      = Auth::user();
        $member    = $user->getMembershipIn($mess->id);
        $isManager = $user->isManagerOf($mess->id);
        if (!$member && !$user->is_super_admin) abort(403);

        $request->validate([
            'item_name'    => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'member_id'    => 'nullable|exists:users,id',
        ]);

        // Non-managers can only submit for themselves
        $memberId = $isManager ? ($request->member_id ?? $user->id) : $user->id;

        Expense::create([
            'mess_id'           => $mess->id,
            'title'             => $request->item_name,
            'amount'            => $request->amount,
            'expense_date'      => $request->expense_date,
            'added_by'          => Auth::id(),
            'member_id'         => $memberId,
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
