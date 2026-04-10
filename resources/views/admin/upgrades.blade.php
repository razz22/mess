<?php $page = "admin-upgrades" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-rocket me-2 text-primary"></i>Upgrade Requests</h4>
                <h6 class="text-muted">bKash payment upgrade requests from mess owners</h6>
            </div>
            @if($pendingCount > 0)
            <div class="page-btn">
                <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                    <i class="ti ti-clock me-1"></i>{{ $pendingCount }} Pending
                </span>
            </div>
            @endif
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        {{-- Filter --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                    <label class="text-muted small me-1">Filter:</label>
                    @foreach([''=>'All', 'pending'=>'Pending', 'approved'=>'Approved', 'rejected'=>'Rejected'] as $val => $label)
                    <a href="?status={{ $val }}" class="btn btn-sm {{ request('status',$val===''?'':request('status')) === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
                        {{ $label }}
                        @if($val === 'pending' && $pendingCount > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    @endforeach
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mess / Owner</th>
                            <th>Requested</th>
                            <th>Amount</th>
                            <th>bKash Details</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upgrades as $u)
                        <tr class="{{ $u->status === 'pending' ? 'table-warning bg-opacity-25' : '' }}">
                            <td>
                                <div class="fw-semibold">{{ $u->mess->name }}</div>
                                <div class="text-muted small">{{ $u->user->name }} · {{ $u->user->email }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-secondary">{{ $u->current_limit }}</span>
                                    <i class="ti ti-arrow-right" style="font-size:11px;"></i>
                                    <span class="badge bg-primary">{{ $u->requested_limit }}</span>
                                </div>
                                <div class="text-muted" style="font-size:11px;">+{{ $u->requested_limit - $u->current_limit }} slots</div>
                            </td>
                            <td>
                                @if($u->amount > 0)
                                <span class="fw-bold text-success">৳{{ number_format($u->amount, 2) }}</span>
                                @else
                                <span class="text-muted small">Admin set</span>
                                @endif
                            </td>
                            <td>
                                @if($u->bkash_number)
                                <div class="small"><i class="ti ti-device-mobile me-1 text-muted"></i>{{ $u->bkash_number }}</div>
                                <div class="small"><code>{{ $u->transaction_id }}</code></div>
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $u->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <span class="badge bg-{{ $u->status_badge }}">{{ ucfirst($u->status) }}</span>
                                @if($u->reviewed_at)
                                <div class="text-muted" style="font-size:10px;">{{ $u->reviewed_at->format('d M Y') }}</div>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($u->status === 'pending')
                                <button class="btn btn-xs btn-success" data-bs-toggle="modal"
                                    data-bs-target="#approveModal{{ $u->id }}" title="Approve">
                                    <i class="ti ti-check me-1"></i>Approve
                                </button>
                                <button class="btn btn-xs btn-outline-danger ms-1" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal{{ $u->id }}" title="Reject">
                                    <i class="ti ti-x me-1"></i>Reject
                                </button>
                                @else
                                @if($u->admin_notes)
                                <span class="text-muted small" title="{{ $u->admin_notes }}">
                                    <i class="ti ti-notes me-1"></i>{{ Str::limit($u->admin_notes, 30) }}
                                </span>
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="ti ti-rocket-off fs-2 d-block mb-2"></i>No upgrade requests found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($upgrades->hasPages())
            <div class="card-footer">{{ $upgrades->links() }}</div>
            @endif
        </div>

    </div>
</div>

{{-- Approve / Reject Modals --}}
@foreach($upgrades->where('status','pending') as $u)
<div class="modal fade" id="approveModal{{ $u->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="ti ti-check me-2"></i>Approve Upgrade</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.upgrade.approve', $u->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-success mb-3">
                        <strong>{{ $u->mess->name }}</strong> will be upgraded from
                        <strong>{{ $u->current_limit }}</strong> → <strong>{{ $u->requested_limit }}</strong> members.
                        @if($u->amount > 0)
                        <br>Payment: <strong>৳{{ number_format($u->amount,2) }}</strong> via bKash
                        (<code>{{ $u->transaction_id }}</code> from {{ $u->bkash_number }})
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Note (optional)</label>
                        <input type="text" name="admin_notes" class="form-control" placeholder="e.g. Payment verified">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="ti ti-check me-1"></i>Confirm Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal{{ $u->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="ti ti-x me-2"></i>Reject Upgrade</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.upgrade.reject', $u->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        Rejecting upgrade for <strong>{{ $u->mess->name }}</strong>.
                        @if($u->amount > 0) Please ensure you refund ৳{{ number_format($u->amount,2) }} if payment was received. @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <input type="text" name="admin_notes" class="form-control" placeholder="e.g. Transaction ID not found" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="ti ti-x me-1"></i>Confirm Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
