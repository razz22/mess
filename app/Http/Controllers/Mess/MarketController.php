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

        $routines = MarketRoutine::where('mess_id', $mess->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with(['assignedTo', 'exchange'])
            ->orderBy('date')
            ->get()
            ->keyBy(fn($r) => $r->date->format('Y-m-d'));

        $members = $mess->activeMembers()->with('user')->get();
        $member  = Auth::user()->getMembershipIn($mess->id);

        // Calendar data
        $calendarDays = $this->buildCalendar($year, $month);

        return view('mess.market', compact('mess', 'routines', 'members', 'member', 'calendarDays', 'month', 'year'));
    }

    public function assign(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'date'        => 'required|date',
            'assigned_to' => 'required|exists:users,id',
        ]);

        MarketRoutine::updateOrCreate(
            ['mess_id' => $mess->id, 'date' => $request->date],
            [
                'assigned_to' => $request->assigned_to,
                'assigned_by' => Auth::id(),
                'status'      => 'pending',
                'notes'       => $request->notes,
            ]
        );

        return back()->with('success', 'Market routine assigned.');
    }

    public function listItems(Mess $mess, MarketRoutine $routine)
    {
        if ($routine->mess_id !== $mess->id) abort(403);
        $this->authorizeMember($mess);

        $items  = $routine->listItems()->with('addedBy')->get();
        $member = Auth::user()->getMembershipIn($mess->id);

        return view('mess.market-list', compact('mess', 'routine', 'items', 'member'));
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
        ]);

        MarketListItem::create([
            'routine_id'     => $routine->id,
            'mess_id'        => $mess->id,
            'item_name'      => $request->item_name,
            'quantity'       => $request->quantity,
            'unit'           => $request->unit,
            'estimated_cost' => $request->estimated_cost ?? 0,
            'added_by'       => Auth::id(),
        ]);

        return back()->with('success', 'Item added to list.');
    }

    public function updateListItem(Request $request, Mess $mess, MarketListItem $item)
    {
        if ($item->mess_id !== $mess->id) abort(403);

        $item->update([
            'actual_cost' => $request->actual_cost ?? $item->actual_cost,
            'purchased'   => $request->boolean('purchased'),
        ]);

        // Update routine total
        $total = $item->routine->listItems()->sum('actual_cost');
        $item->routine->update(['total_spent' => $total]);

        return response()->json(['success' => true, 'total' => $total]);
    }

    public function completeRoutine(Request $request, Mess $mess, MarketRoutine $routine)
    {
        if ($routine->mess_id !== $mess->id) abort(403);

        $canComplete = Auth::id() === $routine->assigned_to || Auth::user()->isManagerOf($mess->id);
        if (!$canComplete) abort(403);

        $total = $routine->listItems()->sum('actual_cost');
        $routine->update(['status' => 'completed', 'total_spent' => $total]);

        // Create an expense for market
        if ($total > 0) {
            Expense::create([
                'mess_id'          => $mess->id,
                'title'            => 'Market - ' . $routine->date->format('d M Y'),
                'amount'           => $total,
                'expense_date'     => $routine->date,
                'added_by'         => Auth::id(),
                'member_id'        => $routine->assigned_to,
                'is_market_expense'=> true,
            ]);
        }

        return back()->with('success', 'Market routine completed.');
    }

    public function requestExchange(Request $request, Mess $mess, MarketRoutine $routine)
    {
        if ($routine->assigned_to !== Auth::id()) abort(403);

        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'reason'     => 'nullable|string|max:500',
        ]);

        MarketRoutineExchange::create([
            'routine_id'   => $routine->id,
            'from_user_id' => Auth::id(),
            'to_user_id'   => $request->to_user_id,
            'reason'       => $request->reason,
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Exchange request sent.');
    }

    public function respondExchange(Request $request, Mess $mess, MarketRoutineExchange $exchange)
    {
        if ($exchange->to_user_id !== Auth::id() && !Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate(['action' => 'required|in:accept,reject']);

        if ($request->action === 'accept') {
            $exchange->update(['status' => 'accepted', 'responded_at' => now()]);
            $exchange->routine->update([
                'assigned_to' => $exchange->to_user_id,
                'status'      => 'pending',
            ]);
        } else {
            $exchange->update(['status' => 'rejected', 'responded_at' => now()]);
        }

        return back()->with('success', 'Exchange ' . $request->action . 'ed.');
    }

    private function buildCalendar(int $year, int $month): array
    {
        $firstDay = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDow = $firstDay->dayOfWeek;

        $days = [];
        // Pad start
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
