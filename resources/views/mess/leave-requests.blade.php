<?php $page = "mess-leave" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-logout me-2 text-danger"></i>{{ __('Leave Requests') }}</h4>
                <h6 class="text-muted">{{ $mess->name }}</h6>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="ti ti-list me-2"></i>{{ __('All Leave Requests') }}</h6>
                <span class="badge bg-warning text-dark">
                    Notice Period: {{ $mess->leave_notice_months ?? 1 }} month(s)
                </span>
            </div>

            @if($leaves->isEmpty())
            <div class="card-body text-center text-muted py-5">
                <i class="ti ti-logout fs-1 d-block mb-2 opacity-30"></i>
                No leave requests yet.
            </div>
            @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Member') }}</th>
                            <th>{{ __('Applied') }}</th>
                            <th>{{ __('Last Date') }}</th>
                            <th>{{ __('Notice Required') }}</th>
                            <th>{{ __('Reason') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Reviewed By') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaves as $lv)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $lv->member->user->name ?? '—' }}</div>
                                <div class="small text-muted">{{ ucfirst($lv->member->role ?? '') }}</div>
                            </td>
                            <td class="small">{{ $lv->applied_at->format('d M Y') }}</td>
                            <td class="small fw-semibold">{{ $lv->last_date->format('d M Y') }}</td>
                            <td class="small text-center">{{ $lv->notice_months_required }} mo</td>
                            <td class="small text-muted" style="max-width:180px;">{{ $lv->reason ?: '—' }}</td>
                            <td>{!! $lv->statusBadge() !!}</td>
                            <td class="small text-muted">
                                @if($lv->reviewedBy)
                                {{ $lv->reviewedBy->name }}<br>
                                <span style="font-size:11px;">{{ $lv->reviewed_at->format('d M Y') }}</span>
                                @if($lv->review_note)
                                <div class="text-muted" style="font-size:11px;font-style:italic;">{{ $lv->review_note }}</div>
                                @endif
                                @else
                                —
                                @endif
                            </td>
                            <td>
                                        @if($lv->status === 'pending')
                                <div class="d-flex gap-1 flex-wrap">
                                    <button class="btn btn-xs btn-success"
                                            onclick="openReview({{ $lv->id }}, 'approve', '{{ addslashes($lv->member->user->name ?? '') }}')">
                                        <i class="ti ti-check"></i> Approve
                                    </button>
                                    <button class="btn btn-xs btn-danger"
                                            onclick="openReview({{ $lv->id }}, 'reject', '{{ addslashes($lv->member->user->name ?? '') }}')">
                                        <i class="ti ti-x"></i> Reject
                                    </button>
                                </div>
                                @elseif($lv->status === 'approved')
                                @php $advBal = $advanceBalances[$lv->member_id] ?? 0; @endphp
                                <button class="btn btn-xs btn-warning text-dark"
                                        onclick="openRefund({{ $lv->id }}, '{{ addslashes($lv->member->user->name ?? '') }}', {{ $advBal }})">
                                    <i class="ti ti-coin-rupee"></i> Refund Advance
                                </button>
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Review Modal --}}
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="reviewModalHeader">
                <h5 class="modal-title" id="reviewModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reviewForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p id="reviewModalDesc" class="text-muted small mb-3"></p>
                    <div>
                        <label class="form-label fw-semibold">Note <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea name="review_note" class="form-control" rows="3" placeholder="Add a note for the member..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn" id="reviewSubmitBtn">{{ __('Confirm') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Refund Advance Modal --}}
<div class="modal fade" id="refundModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning bg-opacity-10">
                <h5 class="modal-title fw-bold"><i class="ti ti-coin-rupee me-2 text-warning"></i>Refund Advance — <span id="refundMemberName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="refundForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info py-2 small mb-3" id="refundBalanceInfo"></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Refund Amount (৳) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">৳</span>
                            <input type="number" name="amount" id="refundAmount" class="form-control"
                                   step="0.01" min="0.01" placeholder="0.00" required>
                        </div>
                        <div class="form-text">Pre-filled with current advance balance. You can adjust.</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Note <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="e.g. Refunded via bank transfer on departure"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-warning text-dark">
                        <i class="ti ti-check me-1"></i>Record Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRefund(leaveId, memberName, advBalance) {
    const base = '{{ url("mess/" . $mess->id . "/leave") }}';
    document.getElementById('refundForm').action = base + '/' + leaveId + '/refund';
    document.getElementById('refundMemberName').textContent = memberName;
    document.getElementById('refundAmount').value = advBalance > 0 ? advBalance : '';
    document.getElementById('refundAmount').max   = advBalance > 0 ? advBalance : '';
    document.getElementById('refundBalanceInfo').textContent =
        advBalance > 0
            ? 'Current advance balance for ' + memberName + ': ৳' + parseFloat(advBalance).toFixed(2)
            : memberName + ' has no recorded advance balance. You can still enter a manual amount.';
    new bootstrap.Modal(document.getElementById('refundModal')).show();
    setTimeout(() => document.getElementById('refundAmount').focus(), 300);
}

function openReview(leaveId, action, memberName) {
    const isApprove = action === 'approve';
    const base = '{{ url("mess/" . $mess->id . "/leave") }}';
    document.getElementById('reviewForm').action = base + '/' + leaveId + '/' + action;
    document.getElementById('reviewModalTitle').textContent = (isApprove ? 'Approve' : 'Reject') + ' Leave — ' + memberName;
    document.getElementById('reviewModalHeader').className = 'modal-header ' + (isApprove ? 'bg-success text-white' : 'bg-danger text-white');
    document.getElementById('reviewModalDesc').textContent = isApprove
        ? 'Approving this leave will immediately deactivate the member.'
        : 'The member will be notified that their leave request was rejected.';
    const btn = document.getElementById('reviewSubmitBtn');
    btn.className = 'btn ' + (isApprove ? 'btn-success' : 'btn-danger');
    btn.textContent = isApprove ? 'Approve' : 'Reject';
    new bootstrap.Modal(document.getElementById('reviewModal')).show();
}
</script>
@endsection
