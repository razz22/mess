<?php $page = "mess-market" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">Market Routine — {{ $mess->name }}</h4>
                <h6>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h6>
            </div>
            <div class="page-btn d-flex gap-2">
                @if($member->canManage())
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignModal">
                    <i class="ti ti-user-check me-1"></i>Assign Market
                </button>
                @endif
                <a href="{{ route('mess.dashboard', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <!-- Month Navigation -->
        <div class="card mb-4">
            <div class="card-body py-2 d-flex align-items-center gap-3 flex-wrap">
                @php
                    $prevMonth = $month == 1 ? 12 : $month - 1;
                    $prevYear  = $month == 1 ? $year - 1 : $year;
                    $nextMonth = $month == 12 ? 1 : $month + 1;
                    $nextYear  = $month == 12 ? $year + 1 : $year;
                @endphp
                <a href="?month={{ $prevMonth }}&year={{ $prevYear }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ti ti-chevron-left"></i>
                </a>
                <strong>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</strong>
                <a href="?month={{ $nextMonth }}&year={{ $nextYear }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ti ti-chevron-right"></i>
                </a>
                <a href="?month={{ now()->month }}&year={{ now()->year }}" class="btn btn-sm btn-primary ms-2">This Month</a>
            </div>
        </div>

        <!-- Calendar -->
        <div class="card">
            <div class="card-body">
                <div class="row g-0 text-center">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="col fw-bold py-2 bg-light border">{{ $day }}</div>
                    @endforeach
                </div>
                <div class="row g-0" id="calendarGrid">
                    @foreach($calendarDays as $calDay)
                    <div class="col border" style="min-height:100px; flex: 0 0 14.285714%; max-width: 14.285714%;">
                        @if($calDay)
                        @php
                            $dateStr = $calDay->format('Y-m-d');
                            $routine = $routines[$dateStr] ?? null;
                            $isToday = $calDay->isToday();
                        @endphp
                        <div class="p-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge {{ $isToday ? 'bg-primary' : 'bg-light text-dark' }}">{{ $calDay->day }}</span>
                                @if($calDay->dayOfWeek === 5)<span class="badge bg-warning-subtle text-warning" style="font-size:9px">Friday</span>@endif
                            </div>
                            @if($routine)
                            <div class="rounded p-1 mb-1 bg-{{ $routine->status === 'completed' ? 'success' : ($routine->status === 'pending' ? 'warning' : 'info') }}-subtle">
                                <div style="font-size:11px; font-weight:600" class="text-truncate">
                                    {{ $routine->assignedTo->name }}
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span style="font-size:9px" class="badge bg-{{ $routine->status === 'completed' ? 'success' : ($routine->status === 'pending' ? 'warning' : 'info') }}">
                                        {{ ucfirst($routine->status) }}
                                    </span>
                                    @if($routine->total_spent > 0)
                                    <span style="font-size:9px" class="text-muted">৳{{ number_format($routine->total_spent, 0) }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('mess.market.list', [$mess->id, $routine->id]) }}" class="btn btn-xs btn-outline-primary w-100 mt-1" style="font-size:9px">
                                    List
                                </a>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Routine Summary Table -->
        <div class="card mt-4">
            <div class="card-header"><h6 class="mb-0">Market Assignments This Month</h6></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Total Spent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($routines as $routine)
                        <tr>
                            <td>{{ $routine->date->format('d M') }}</td>
                            <td>{{ $routine->assignedTo->name }}</td>
                            <td><span class="badge bg-{{ $routine->status === 'completed' ? 'success' : ($routine->status === 'pending' ? 'warning' : 'info') }}">{{ ucfirst($routine->status) }}</span></td>
                            <td>{{ $routine->total_spent > 0 ? '৳'.number_format($routine->total_spent, 2) : '-' }}</td>
                            <td>
                                <a href="{{ route('mess.market.list', [$mess->id, $routine->id]) }}" class="btn btn-xs btn-outline-primary">
                                    <i class="ti ti-list me-1"></i>List
                                </a>
                                @if($routine->assigned_to === Auth::id() && $routine->status === 'pending')
                                <button class="btn btn-xs btn-outline-warning ms-1" onclick="requestExchange({{ $routine->id }})">
                                    <i class="ti ti-arrows-exchange me-1"></i>Exchange
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">No assignments this month.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Assign Market Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Market Duty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.market.assign', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign To</label>
                        <select name="assigned_to" class="form-select" required>
                            @foreach($members as $m)
                            <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Exchange Request Modal -->
<div class="modal fade" id="exchangeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Exchange</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="exchangeForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Exchange With</label>
                        <select name="to_user_id" class="form-select" required>
                            @foreach($members as $m)
                            @if($m->user->id !== Auth::id())
                            <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="Why do you want to exchange?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Send Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function requestExchange(routineId) {
    const form = document.getElementById('exchangeForm');
    form.action = `/mess/{{ $mess->id }}/market/${routineId}/exchange`;
    new bootstrap.Modal(document.getElementById('exchangeModal')).show();
}
</script>
@endsection
