<?php $page = "mess-upgrade-history" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-history me-2 text-primary"></i>Upgrade History</h4>
                <h6 class="text-muted">{{ $mess->name }} — All subscription upgrade requests</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('mess.upgrade', $mess->id) }}" class="btn btn-primary btn-sm">
                    <i class="ti ti-rocket me-1"></i>Upgrade Mess
                </a>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="text-muted small mb-1"><i class="ti ti-list me-1"></i>Total Requests</div>
                    <div class="fs-3 fw-bold text-primary">{{ $upgrades->count() }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="text-muted small mb-1"><i class="ti ti-circle-check me-1"></i>Approved</div>
                    <div class="fs-3 fw-bold text-success">{{ $upgrades->where('status','approved')->count() }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="text-muted small mb-1"><i class="ti ti-clock me-1"></i>Pending</div>
                    <div class="fs-3 fw-bold text-warning">{{ $upgrades->where('status','pending')->count() }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="text-muted small mb-1"><i class="ti ti-currency-taka me-1"></i>Total Paid</div>
                    <div class="fs-3 fw-bold text-info">৳{{ number_format($upgrades->where('status','approved')->sum('amount'), 0) }}</div>
                </div>
            </div>
        </div>

        {{-- DataTable --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0"><i class="ti ti-table me-2"></i>All Requests</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="upgradeTable" class="table table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Plan</th>
                                <th>Member Limit</th>
                                <th>Amount</th>
                                <th>bKash Tx ID</th>
                                <th>Status</th>
                                <th>Reviewed</th>
                                <th>Admin Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upgrades as $i => $u)
                            <tr>
                                <td class="text-muted small">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold small">{{ $u->created_at->format('d M Y') }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $u->created_at->format('h:i A') }}</div>
                                </td>
                                <td>
                                    @if($u->plan)
                                    <span class="badge rounded-pill" style="background:#4361ee18;color:#4361ee;font-size:12px;padding:5px 10px;">
                                        <i class="ti ti-rocket me-1"></i>{{ $u->plan->name }}
                                    </span>
                                    @else
                                    <span class="text-muted small">Custom</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="text-muted small">{{ $u->current_limit }}</span>
                                        <i class="ti ti-arrow-narrow-right text-muted" style="font-size:14px;"></i>
                                        <span class="fw-bold text-primary">{{ $u->requested_limit }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($u->amount > 0)
                                    <span class="fw-semibold text-success">৳{{ number_format($u->amount, 0) }}</span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($u->transaction_id)
                                    <code class="small" style="background:#f3f4f6;padding:3px 7px;border-radius:6px;color:#374151;">
                                        {{ $u->transaction_id }}
                                    </code>
                                    @else
                                    <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($u->status) {
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            default    => 'bg-warning text-dark',
                                        };
                                        $icon = match($u->status) {
                                            'approved' => 'ti-circle-check',
                                            'rejected' => 'ti-circle-x',
                                            default    => 'ti-clock',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill px-2 py-1">
                                        <i class="ti {{ $icon }} me-1"></i>{{ ucfirst($u->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($u->reviewed_at)
                                    <div class="small">{{ $u->reviewed_at->format('d M Y') }}</div>
                                    @if($u->reviewer)
                                    <div class="text-muted" style="font-size:11px;">by {{ $u->reviewer->name }}</div>
                                    @endif
                                    @else
                                    <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="small text-muted" style="max-width:160px;">
                                    {{ $u->admin_notes ?: '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
$(function () {
    $('#upgradeTable').DataTable({
        order: [[1, 'desc']],
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        language: {
            search: '',
            searchPlaceholder: 'Search requests…',
            lengthMenu: 'Show _MENU_ rows',
            info: 'Showing _START_–_END_ of _TOTAL_ requests',
            emptyTable: 'No upgrade requests found.',
            zeroRecords: 'No matching requests found.',
        },
        columnDefs: [
            { orderable: false, targets: [5, 8] }
        ],
        dom: "<'row mb-3 px-3'<'col-sm-6'l><'col-sm-6 text-end'f>>" +
             "<'row'<'col-12'tr>>" +
             "<'row px-3 mt-3'<'col-sm-5'i><'col-sm-7 text-end'p>>",
    });
});
</script>
@endpush
@endsection
