<?php $page = "mess-member-reports" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">Member Reports — {{ $mess->name }}</h4>
                <h6>Report misconduct & earn reward points</h6>
            </div>
            <div class="page-btn">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addReportModal">
                    <i class="ti ti-flag me-1"></i>Submit Report
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="alert alert-info d-flex gap-2">
            <i class="ti ti-info-circle fs-5 mt-1 flex-shrink-0"></i>
            <div>
                You can report members for rule violations (not contributing to market, disrespecting others, etc.).
                If the report is <strong>resolved</strong>, you earn <strong>reward points</strong> for the month.
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0">All Reports</h6></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reported By</th>
                            <th>Reported</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Points</th>
                            @if($member->canManage()) <th>Actions</th> @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                        <tr>
                            <td>{{ $report->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm">@if($report->reporter->avatar)<img src="{{ asset('storage/'.$report->reporter->avatar) }}" class="avatar-title rounded-circle" style="object-fit:cover;" alt="">@else<span class="avatar-title rounded-circle bg-info text-white">{{ strtoupper(substr($report->reporter->name, 0, 1)) }}</span>@endif</div>
                                    {{ $report->reporter->name }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm">@if($report->reported->avatar)<img src="{{ asset('storage/'.$report->reported->avatar) }}" class="avatar-title rounded-circle" style="object-fit:cover;" alt="">@else<span class="avatar-title rounded-circle bg-danger text-white">{{ strtoupper(substr($report->reported->name, 0, 1)) }}</span>@endif</div>
                                    {{ $report->reported->name }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold small">{{ $report->reason }}</div>
                                @if($report->details)
                                <div class="text-muted" style="font-size:11px">{{ Str::limit($report->details, 50) }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ match($report->status) { 'pending' => 'warning', 'reviewed' => 'info', 'resolved' => 'success', 'dismissed' => 'secondary', default => 'secondary' } }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td>
                                @if($report->points_awarded > 0)
                                <span class="badge bg-warning">+{{ number_format($report->points_awarded, 0) }} pts</span>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            @if($member->canManage())
                            <td>
                                @if($report->status === 'pending')
                                <button class="btn btn-xs btn-outline-primary" onclick="reviewReport({{ $report->id }})">
                                    <i class="ti ti-eye me-1"></i>Review
                                </button>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="{{ $member->canManage() ? 7 : 6 }}" class="text-center text-muted py-3">No reports submitted yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $reports->links() }}</div>
        </div>
    </div>
</div>

<!-- Submit Report Modal -->
<div class="modal fade" id="addReportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-flag text-danger me-2"></i>Submit Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.report.members.store', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Report Against <span class="text-danger">*</span></label>
                        <select name="reported_id" class="form-select" required>
                            <option value="">-- Select Member --</option>
                            @foreach($reports->getCollection()->pluck('reported')->unique('id') ?? [] as $r)
                            @endforeach
                            {{-- Load all active members from mess --}}
                            @php
                                $messMembers = $mess->activeMembers()->with('user')->get();
                            @endphp
                            @foreach($messMembers as $m)
                            @if($m->user->id !== Auth::id())
                            <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <input type="text" name="reason" class="form-control" required placeholder="Brief reason for report">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Details</label>
                        <textarea name="details" class="form-control" rows="3" placeholder="Provide more details..."></textarea>
                    </div>
                    <div class="alert alert-warning py-2 small mb-0">
                        <i class="ti ti-info-circle me-1"></i>False reports may reduce your reward points.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reviewForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Decision</label>
                        <select name="status" class="form-select">
                            <option value="reviewed">Reviewed (Noted)</option>
                            <option value="resolved">Resolved (Award Points)</option>
                            <option value="dismissed">Dismissed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Points for Reporter (if resolved)</label>
                        <input type="number" name="points" class="form-control" value="10" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Review Note</label>
                        <textarea name="review_note" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function reviewReport(reportId) {
    const form = document.getElementById('reviewForm');
    form.action = `/mess/{{ $mess->id }}/report/members/${reportId}/review`;
    new bootstrap.Modal(document.getElementById('reviewModal')).show();
}
</script>
@endsection
