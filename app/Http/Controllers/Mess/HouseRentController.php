<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\AdvancePayment;
use App\Models\Mess;
use App\Models\MessFund;
use App\Models\MessFundTransaction;
use App\Models\MessMember;
use App\Models\MessRentFundTransaction;
use App\Models\MessRentInvoice;
use App\Models\MessRentInvoiceExpense;
use App\Models\RentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HouseRentController extends Controller
{
    use AuthorizesMessAccess;
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

        // All paid check
        $allPaid = $members->filter(fn($m) => $m->house_rent > 0)->isNotEmpty()
            && $members->filter(fn($m) => $m->house_rent > 0)->every(fn($m) => ($paidByMember[$m->id] ?? 0) >= $m->house_rent);

        // Invoices (landlord) — all, ordered latest first
        $invoices = MessRentInvoice::where('mess_id', $mess->id)
            ->with(['issuedBy', 'paidBy'])
            ->orderByDesc('year')->orderByDesc('month')->orderByDesc('id')
            ->get();

        // House Rent Fund (running balance)
        $fundTransactions = MessRentFundTransaction::where('mess_id', $mess->id)
            ->with(['invoice', 'recordedBy'])
            ->orderByDesc('transaction_date')->orderByDesc('id')
            ->get();
        $fundBalance  = (float)($fundTransactions->where('type','credit')->sum('amount')
                       - $fundTransactions->where('type','debit')->sum('amount'));
        $totalCredited = (float)$fundTransactions->where('type','credit')->sum('amount');
        $totalDebited  = (float)$fundTransactions->where('type','debit')->sum('amount');

        // Named funds (for other purposes)
        $funds = MessFund::where('mess_id', $mess->id)->orderBy('name')->get();

        return view('mess.rent-management', compact(
            'mess', 'members', 'month', 'year',
            'monthPayments', 'paidByMember',
            'totalExpected', 'totalCollected', 'outstanding',
            'allPayments', 'allPaid',
            'advancePayments', 'advanceBalanceByMember', 'totalAdvanceHeld',
            'invoices', 'funds',
            'fundTransactions', 'fundBalance', 'totalCredited', 'totalDebited'
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
        abort_if((int) $payment->mess_id !== (int) $mess->id, 403);

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
        abort_if((int) $payment->mess_id !== (int) $mess->id, 403);

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
        abort_if((int) $advance->mess_id !== (int) $mess->id, 403);

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
        abort_if((int) $advance->mess_id !== (int) $mess->id, 403);

        $advance->delete();

        return back()->with('success', 'Advance transaction deleted.');
    }

    // -------------------------------------------------------------------------
    // Rent Invoices
    // -------------------------------------------------------------------------

    public function showInvoice(Mess $mess, MessRentInvoice $invoice)
    {
        $this->assertOwner($mess);
        abort_if((int) $invoice->mess_id !== (int) $mess->id, 403);

        $invoice->load(['issuedBy', 'paidBy']);

        $month = $invoice->month;
        $year  = $invoice->year;

        // Total collected from members for that month
        $members       = MessMember::where('mess_id', $mess->id)->where('is_active', true)->get();
        $monthPayments = RentPayment::where('mess_id', $mess->id)
            ->where('month', $month)->where('year', $year)->get();

        $totalCollected = 0;
        foreach ($members as $m) {
            $credits = $monthPayments->where('member_id', $m->id)
                ->whereIn('payment_type', ['rent','penalty','adjustment'])->sum('amount');
            $debits  = $monthPayments->where('member_id', $m->id)
                ->where('payment_type', 'discount')->sum('amount');
            $totalCollected += max(0, $credits - $debits);
        }

        $surplus = $totalCollected - $invoice->rent_amount;

        // Direct expenses recorded against this invoice's surplus
        $expenses      = MessRentInvoiceExpense::where('invoice_id', $invoice->id)
            ->with('recordedBy')->orderByDesc('expense_date')->get();
        $totalExpensed = $expenses->sum('amount');
        $remaining     = $surplus - $totalExpensed;

        return view('mess.rent-invoice-view', compact(
            'mess', 'invoice', 'totalCollected', 'surplus',
            'expenses', 'totalExpensed', 'remaining', 'month', 'year'
        ));
    }

    public function invoiceExpense(Request $request, Mess $mess, MessRentInvoice $invoice)
    {
        $this->assertOwner($mess);
        abort_if((int) $invoice->mess_id !== (int) $mess->id, 403);

        $data = $request->validate([
            'description'  => 'required|string|max:500',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
        ]);

        MessRentInvoiceExpense::create([
            'invoice_id'   => $invoice->id,
            'mess_id'      => $mess->id,
            'description'  => $data['description'],
            'amount'       => $data['amount'],
            'expense_date' => $data['expense_date'],
            'recorded_by'  => Auth::id(),
        ]);

        return back()->with('success', '৳' . number_format($data['amount'], 2) . ' expense recorded from surplus.');
    }

    public function destroyInvoiceExpense(Mess $mess, MessRentInvoiceExpense $expense)
    {
        $this->assertOwner($mess);
        abort_if((int) $expense->mess_id !== (int) $mess->id, 403);
        $expense->delete();
        return back()->with('success', 'Expense deleted.');
    }

    public function storeInvoice(Request $request, Mess $mess)
    {
        $this->assertOwner($mess);

        $data = $request->validate([
            'month'              => 'required|integer|between:1,12',
            'year'               => 'required|integer|min:2020|max:2100',
            'house_owner_name'   => 'required|string|max:150',
            'house_owner_phone'  => 'nullable|string|max:30',
            'property_address'   => 'nullable|string|max:500',
            'rent_amount'        => 'required|numeric|min:0.01',
            'invoice_date'       => 'required|date',
            'due_date'           => 'nullable|date',
            'notes'              => 'nullable|string|max:500',
        ]);

        MessRentInvoice::create(array_merge($data, [
            'mess_id'    => $mess->id,
            'invoice_no' => MessRentInvoice::generateNo($mess->id, $data['month'], $data['year']),
            'status'     => 'draft',
            'issued_by'  => Auth::id(),
        ]));

        return back()->with('success', 'Invoice created.');
    }

    public function markInvoicePaid(Mess $mess, MessRentInvoice $invoice)
    {
        $this->assertOwner($mess);
        abort_if((int) $invoice->mess_id !== (int) $mess->id, 403);

        $invoice->update([
            'status'  => 'paid',
            'paid_by' => Auth::id(),
            'paid_at' => now(),
        ]);

        // Calculate surplus for that month and auto-credit the fund
        $members       = MessMember::where('mess_id', $mess->id)->where('is_active', true)->get();
        $monthPayments = RentPayment::where('mess_id', $mess->id)
            ->where('month', $invoice->month)->where('year', $invoice->year)->get();

        $totalCollected = 0;
        foreach ($members as $m) {
            $credits = $monthPayments->where('member_id', $m->id)
                ->whereIn('payment_type', ['rent','penalty','adjustment'])->sum('amount');
            $debits  = $monthPayments->where('member_id', $m->id)
                ->where('payment_type', 'discount')->sum('amount');
            $totalCollected += max(0, $credits - $debits);
        }

        $surplus = $totalCollected - $invoice->rent_amount;

        if ($surplus > 0) {
            MessRentFundTransaction::create([
                'mess_id'          => $mess->id,
                'type'             => 'credit',
                'amount'           => $surplus,
                'description'      => 'Rent surplus — ' . date('F Y', mktime(0,0,0,$invoice->month,1,$invoice->year)),
                'note'             => 'Auto-credited: collected ৳' . number_format($totalCollected, 2) . ' − rent ৳' . number_format($invoice->rent_amount, 2),
                'transaction_date' => now()->toDateString(),
                'source'           => 'surplus',
                'invoice_id'       => $invoice->id,
                'recorded_by'      => Auth::id(),
            ]);

            return back()->with('success', 'Invoice marked as paid. Surplus ৳' . number_format($surplus, 2) . ' credited to House Rent Fund.');
        }

        return back()->with('success', 'Invoice marked as paid.');
    }

    public function storeFundExpense(Request $request, Mess $mess)
    {
        $this->assertOwner($mess);

        $data = $request->validate([
            'description'      => 'required|string|max:300',
            'amount'           => 'required|numeric|min:0.01',
            'note'             => 'nullable|string|max:500',
            'transaction_date' => 'required|date',
        ]);

        // Check balance
        $balance = (float)(MessRentFundTransaction::where('mess_id',$mess->id)->where('type','credit')->sum('amount')
                 - MessRentFundTransaction::where('mess_id',$mess->id)->where('type','debit')->sum('amount'));

        if ($data['amount'] > $balance) {
            return back()->with('error', 'Expense amount exceeds available fund balance (৳' . number_format($balance, 2) . ').');
        }

        MessRentFundTransaction::create([
            'mess_id'          => $mess->id,
            'type'             => 'debit',
            'amount'           => $data['amount'],
            'description'      => $data['description'],
            'note'             => $data['note'],
            'transaction_date' => $data['transaction_date'],
            'source'           => 'expense',
            'recorded_by'      => Auth::id(),
        ]);

        return back()->with('success', '৳' . number_format($data['amount'], 2) . ' expense recorded. Fund balance updated.');
    }

    public function destroyFundTransaction(Mess $mess, MessRentFundTransaction $transaction)
    {
        $this->assertOwner($mess);
        abort_if((int) $transaction->mess_id !== (int) $mess->id, 403);
        $transaction->delete();
        return back()->with('success', 'Transaction deleted.');
    }

    public function cancelInvoice(Mess $mess, MessRentInvoice $invoice)
    {
        $this->assertOwner($mess);
        abort_if((int) $invoice->mess_id !== (int) $mess->id, 403);

        $invoice->update(['status' => 'cancelled']);
        return back()->with('success', 'Invoice cancelled.');
    }

    public function destroyInvoice(Mess $mess, MessRentInvoice $invoice)
    {
        $this->assertOwner($mess);
        abort_if((int) $invoice->mess_id !== (int) $mess->id, 403);

        $invoice->delete();
        return back()->with('success', 'Invoice deleted.');
    }

    // -------------------------------------------------------------------------
    // Funds
    // -------------------------------------------------------------------------

    public function storeFund(Request $request, Mess $mess)
    {
        $this->assertOwner($mess);

        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        MessFund::create(array_merge($data, ['mess_id' => $mess->id]));
        return back()->with('success', 'Fund created.');
    }

    public function fundTransaction(Request $request, Mess $mess, MessFund $fund)
    {
        $this->assertOwner($mess);
        abort_if((int) $fund->mess_id !== (int) $mess->id, 403);

        $data = $request->validate([
            'type'             => 'required|in:credit,debit',
            'amount'           => 'required|numeric|min:0.01',
            'source'           => 'required|in:rent_surplus,expense,manual',
            'notes'            => 'nullable|string|max:500',
            'transaction_date' => 'required|date',
        ]);

        MessFundTransaction::create(array_merge($data, [
            'fund_id'     => $fund->id,
            'mess_id'     => $mess->id,
            'recorded_by' => Auth::id(),
        ]));

        $label = $data['type'] === 'credit' ? 'added to' : 'debited from';
        return back()->with('success', "৳" . number_format($data['amount'], 2) . " {$label} {$fund->name}.");
    }

    public function destroyFund(Mess $mess, MessFund $fund)
    {
        $this->assertOwner($mess);
        abort_if((int) $fund->mess_id !== (int) $mess->id, 403);

        $fund->delete(); // cascades transactions
        return back()->with('success', 'Fund deleted.');
    }

}
