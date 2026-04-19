<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AuthorizesMessAccess;
use App\Models\AdvancePayment;
use App\Models\Mess;
use App\Models\MessLeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    use AuthorizesMessAccess;
    // Manager: list all leave requests
    public function index(Mess $mess)
    {
        abort_unless(Auth::user()->isManagerOf($mess->id), 403);

        $leaves = MessLeaveRequest::where('mess_id', $mess->id)
            ->with(['member.user', 'reviewedBy'])
            ->orderByRaw("FIELD(status,'pending','approved','rejected','cancelled')")
            ->orderByDesc('applied_at')
            ->get();

        // Compute current advance balance per member (received - refunded - adjusted)
        $advanceBalances = [];
        foreach ($leaves as $lv) {
            if (isset($advanceBalances[$lv->member_id])) continue;
            $payments = AdvancePayment::where('member_id', $lv->member_id)->get();
            $received = $payments->where('transaction_type', 'received')->sum('amount');
            $out      = $payments->whereIn('transaction_type', ['refunded', 'adjusted'])->sum('amount');
            $advanceBalances[$lv->member_id] = max(0, $received - $out);
        }

        return view('mess.leave-requests', compact('mess', 'leaves', 'advanceBalances'));
    }

    // Member: own leave page
    public function myLeave(Mess $mess)
    {
        $member = $mess->members()->where('user_id', Auth::id())->where('is_active', true)->firstOrFail();

        $noticeMonths = max(1, (int) ($mess->leave_notice_months ?? 1));
        $minLastDate  = MessLeaveRequest::minLastDate(now(), $noticeMonths);

        $leaveRequests   = MessLeaveRequest::where('member_id', $member->id)
            ->with('reviewedBy')
            ->orderByDesc('applied_at')
            ->get();

        $activeLeavePending = $leaveRequests->whereIn('status', ['pending', 'approved'])->first();

        return view('mess.my-leave', compact(
            'mess', 'member', 'noticeMonths', 'minLastDate', 'leaveRequests', 'activeLeavePending'
        ));
    }

    // Member: apply for leave
    public function store(Request $request, Mess $mess)
    {
        $member = $mess->members()->where('user_id', Auth::id())->where('is_active', true)->firstOrFail();

        $existing = MessLeaveRequest::where('member_id', $member->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();
        if ($existing) {
            return back()->with('error', 'You already have an active or pending leave request.');
        }

        $noticeMonths = max(1, (int) ($mess->leave_notice_months ?? 1));
        $minLastDate  = MessLeaveRequest::minLastDate(now(), $noticeMonths);

        $request->validate([
            'last_date' => [
                'required', 'date',
                'after_or_equal:' . $minLastDate->toDateString(),
            ],
            'reason' => 'nullable|string|max:1000',
        ], [
            'last_date.after_or_equal' => "With {$noticeMonths} month(s) notice, your last date must be on or after " . $minLastDate->format('d M Y') . '.',
        ]);

        MessLeaveRequest::create([
            'mess_id'                => $mess->id,
            'member_id'              => $member->id,
            'applied_at'             => now()->toDateString(),
            'last_date'              => $request->last_date,
            'reason'                 => $request->reason,
            'notice_months_required' => $noticeMonths,
            'status'                 => 'pending',
        ]);

        return back()->with('success', 'Leave request submitted. Awaiting manager approval.');
    }

    // Manager: approve
    public function approve(Request $request, Mess $mess, MessLeaveRequest $leave)
    {
        abort_unless(Auth::user()->isManagerOf($mess->id), 403);
        abort_if((int) $leave->mess_id !== (int) $mess->id, 403);
        abort_if($leave->status !== 'pending', 422);

        $request->validate(['review_note' => 'nullable|string|max:500']);

        $leave->update([
            'status'      => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_note' => $request->review_note,
        ]);

        $leave->member()->update(['is_active' => false]);

        return back()->with('success', 'Leave approved. Member has been deactivated.');
    }

    // Manager: reject
    public function reject(Request $request, Mess $mess, MessLeaveRequest $leave)
    {
        abort_unless(Auth::user()->isManagerOf($mess->id), 403);
        abort_if((int) $leave->mess_id !== (int) $mess->id, 403);
        abort_if($leave->status !== 'pending', 422);

        $request->validate(['review_note' => 'nullable|string|max:500']);

        $leave->update([
            'status'      => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_note' => $request->review_note,
        ]);

        return back()->with('success', 'Leave request rejected.');
    }

    // Manager: refund advance for an approved leave
    public function refundAdvance(Request $request, Mess $mess, MessLeaveRequest $leave)
    {
        abort_unless(Auth::user()->isManagerOf($mess->id), 403);
        abort_if((int) $leave->mess_id !== (int) $mess->id, 403);
        abort_if($leave->status !== 'approved', 422);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes'  => 'nullable|string|max:500',
        ]);

        AdvancePayment::create([
            'mess_id'          => $mess->id,
            'member_id'        => $leave->member_id,
            'transaction_type' => 'refunded',
            'amount'           => $request->amount,
            'transaction_date' => now()->toDateString(),
            'notes'            => $request->notes ?: 'Advance refund on leave approval',
            'processed_by'     => Auth::id(),
        ]);

        return back()->with('success', 'Advance refund of ৳' . number_format($request->amount, 2) . ' recorded.');
    }

    // Member: cancel own pending request
    public function cancel(Mess $mess, MessLeaveRequest $leave)
    {
        $member = $mess->members()->where('user_id', Auth::id())->first();
        abort_if(!$member || $leave->member_id !== $member->id, 403);
        abort_if($leave->status !== 'pending', 422);

        $leave->update(['status' => 'cancelled']);

        return back()->with('success', 'Leave request cancelled.');
    }
}
