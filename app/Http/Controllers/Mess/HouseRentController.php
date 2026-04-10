<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
use App\Models\Mess;
use App\Models\MessMember;
use App\Models\RentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HouseRentController extends Controller
{
    private function assertOwner(Mess $mess): void
    {
        if (!Auth::user()->isOwnerOf($mess->id)) {
            abort(403, 'Only the owner can access house rent management.');
        }
    }

    // -------------------------------------------------------------------------
    // Main Dashboard
    // -------------------------------------------------------------------------

    public function index(Mess $mess, Request $request)
    {
        $this->assertOwner($mess);

        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year',  now()->year);

        $members = MessMember::where('mess_id', $mess->id)
            ->where('is_active', true)
            ->with('user')
            ->orderBy('room_no')
            ->orderBy('id')
            ->get();

        // Payments for selected month/year
        $monthPayments = RentPayment::where('mess_id', $mess->id)
            ->where('month', $month)
            ->where('year', $year)
            ->with(['member.user', 'receivedBy'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();

        // Net paid per member this month
        // Credits: rent, penalty, adjustment  |  Debits: discount
        $paidByMember = [];
        foreach ($members as $m) {
            $credits = $monthPayments->where('member_id', $m->id)
                ->whereIn('payment_type', ['rent', 'penalty', 'adjustment'])
                ->sum('amount');
            $debits  = $monthPayments->where('member_id', $m->id)
                ->where('payment_type', 'discount')
                ->sum('amount');
            $paidByMember[$m->id] = $credits - $debits;
        }

        // Summary stats
        $totalExpected  = $members->sum('house_rent');
        $totalCollected = max(0, array_sum($paidByMember));
        $outstanding    = max(0, $totalExpected - $totalCollected);

        // All payments (history view — all months, latest first)
        $allPayments = RentPayment::where('mess_id', $mess->id)
            ->with(['member.user', 'receivedBy'])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();

        // Advance payments ledger (all time)
        $advancePayments = AdvancePayment::where('mess_id', $mess->id)
            ->with(['member.user', 'processedBy'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        // Net advance balance per member (received − refunded − adjusted)
        $advanceBalanceByMember = [];
        foreach ($members as $m) {
            $received = $advancePayments->where('member_id', $m->id)
                ->where('transaction_type', 'received')
                ->sum('amount');
            $out      = $advancePayments->where('member_id', $m->id)
                ->whereIn('transaction_type', ['refunded', 'adjusted'])
                ->sum('amount');
            $advanceBalanceByMember[$m->id] = $received - $out;
        }
        $totalAdvanceHeld = array_sum(array_filter($advanceBalanceByMember, fn($v) => $v > 0));

        return view('mess.rent-management', compact(
            'mess', 'members', 'month', 'year',
            'monthPayments', 'paidByMember',
            'totalExpected', 'totalCollected', 'outstanding',
            'allPayments',
            'advancePayments', 'advanceBalanceByMember', 'totalAdvanceHeld'
        ));
    }

    // -------------------------------------------------------------------------
    // Set Monthly Rent & Room for a Member
    // -------------------------------------------------------------------------

    public function setRent(Request $request, Mess $mess)
    {
        $this->assertOwner($mess);

        $data = $request->validate([
            'member_id'  => 'required|integer|exists:mess_members,id',
            'house_rent' => 'required|numeric|min:0',
            'room_no'    => 'nullable|string|max:50',
            'notes'      => 'nullable|string|max:500',
        ]);

        $member = MessMember::where('id', $data['member_id'])
            ->where('mess_id', $mess->id)
            ->firstOrFail();

        $member->update([
            'house_rent' => $data['house_rent'],
            'room_no'    => $data['room_no'] ?? $member->room_no,
            'notes'      => $data['notes'] ?? $member->notes,
        ]);

        return back()->with('success', "Rent configuration updated for {$member->user->name}.");
    }

    // -------------------------------------------------------------------------
    // Rent Payments CRUD
    // -------------------------------------------------------------------------

    public function storePayment(Request $request, Mess $mess)
    {
        $this->assertOwner($mess);

        $data = $request->validate([
            'member_id'      => 'required|integer|exists:mess_members,id',
            'month'          => 'required|integer|between:1,12',
            'year'           => 'required|integer|min:2020|max:2100',
            'amount'         => 'required|numeric|min:0.01',
            'payment_type'   => 'required|in:rent,penalty,discount,adjustment',
            'payment_method' => 'required|in:cash,bkash,nagad,bank_transfer,other',
            'payment_date'   => 'required|date',
            'notes'          => 'nullable|string|max:500',
        ]);

        MessMember::where('id', $data['member_id'])
            ->where('mess_id', $mess->id)
            ->firstOrFail();

        RentPayment::create(array_merge($data, [
            'mess_id'     => $mess->id,
            'received_by' => Auth::id(),
        ]));

        return back()->with('success', 'Rent payment recorded successfully.');
    }

    public function updatePayment(Request $request, Mess $mess, RentPayment $payment)
    {
        $this->assertOwner($mess);
        abort_if($payment->mess_id !== $mess->id, 403);

        $data = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_type'   => 'required|in:rent,penalty,discount,adjustment',
            'payment_method' => 'required|in:cash,bkash,nagad,bank_transfer,other',
            'payment_date'   => 'required|date',
            'notes'          => 'nullable|string|max:500',
        ]);

        $payment->update($data);

        return back()->with('success', 'Payment updated successfully.');
    }

    public function destroyPayment(Mess $mess, RentPayment $payment)
    {
        $this->assertOwner($mess);
        abort_if($payment->mess_id !== $mess->id, 403);

        $payment->delete();

        return back()->with('success', 'Payment deleted.');
    }

    // -------------------------------------------------------------------------
    // Advance Payments CRUD
    // -------------------------------------------------------------------------

    public function storeAdvance(Request $request, Mess $mess)
    {
        $this->assertOwner($mess);

        $data = $request->validate([
            'member_id'        => 'required|integer|exists:mess_members,id',
            'transaction_type' => 'required|in:received,refunded,adjusted',
            'amount'           => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'notes'            => 'nullable|string|max:500',
        ]);

        MessMember::where('id', $data['member_id'])
            ->where('mess_id', $mess->id)
            ->firstOrFail();

        AdvancePayment::create(array_merge($data, [
            'mess_id'      => $mess->id,
            'processed_by' => Auth::id(),
        ]));

        return back()->with('success', 'Advance transaction recorded successfully.');
    }

    public function updateAdvance(Request $request, Mess $mess, AdvancePayment $advance)
    {
        $this->assertOwner($mess);
        abort_if($advance->mess_id !== $mess->id, 403);

        $data = $request->validate([
            'transaction_type' => 'required|in:received,refunded,adjusted',
            'amount'           => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'notes'            => 'nullable|string|max:500',
        ]);

        $advance->update($data);

        return back()->with('success', 'Advance transaction updated.');
    }

    public function destroyAdvance(Mess $mess, AdvancePayment $advance)
    {
        $this->assertOwner($mess);
        abort_if($advance->mess_id !== $mess->id, 403);

        $advance->delete();

        return back()->with('success', 'Advance transaction deleted.');
    }
}
