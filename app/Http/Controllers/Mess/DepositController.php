<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\MemberDeposit;
use App\Models\Mess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepositController extends Controller
{
    use AuthorizesMessAccess;
    public function index(Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403, 'Managers only.');

        $month = request('month', now()->month);
        $year  = request('year', now()->year);

        $members = $mess->activeMembers()->with('user')->get();

        // Get deposits for this month
        $deposits = MemberDeposit::where('mess_id', $mess->id)
            ->where('month', $month)
            ->where('year', $year)
            ->with(['user', 'receivedBy'])
            ->orderByDesc('deposit_date')
            ->get();

        // Per member deposit total
        $depositsByMember = $deposits->groupBy('user_id')
            ->map(fn($d) => $d->sum('amount'));

        $totalDeposit = $deposits->sum('amount');
        $member = Auth::user()->getMembershipIn($mess->id);

        return view('mess.deposits', compact(
            'mess', 'members', 'deposits', 'depositsByMember',
            'totalDeposit', 'member', 'month', 'year'
        ));
    }

    public function store(Request $request, Mess $mess)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);

        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'amount'       => 'required|numeric|min:0.01',
            'month'        => 'required|integer|min:1|max:12',
            'year'         => 'required|integer|min:2020|max:2100',
            'deposit_date' => 'required|date',
        ]);

        MemberDeposit::create([
            'mess_id'      => $mess->id,
            'user_id'      => $request->user_id,
            'amount'       => $request->amount,
            'month'        => $request->month,
            'year'         => $request->year,
            'deposit_date' => $request->deposit_date,
            'note'         => $request->note,
            'received_by'  => Auth::id(),
        ]);

        return back()->with('success', 'Deposit recorded successfully.');
    }

    public function destroy(Mess $mess, MemberDeposit $deposit)
    {
        if (!Auth::user()->isManagerOf($mess->id)) abort(403);
        if ((int) $deposit->mess_id !== (int) $mess->id) abort(403);
        $deposit->delete();
        return back()->with('success', 'Deposit deleted.');
    }

    private function authorizeMember(Mess $mess): void
    {
        if (!Auth::user()->getMembershipIn($mess->id)) abort(403);
    }
}
