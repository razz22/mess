<?php $page = "mess-market" ?>
@extends('layout.mainlayout')
@section('content')
@php
    $isManager = $member && in_array($member->role, ['owner', 'manager']);
    $isOwner   = $member && $member->role === 'owner';
@endphp
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">Market Routine — {{ $mess->name }}</h4>
                <h6>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h6>
            </div>
            <div class="page-btn d-flex gap-2 flex-wrap">
                @if($isManager)
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignModal">
                    <i class="ti ti-user-check me-1"></i>Assign Range
                </button>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#quickExpenseModal">
                    <i class="ti ti-plus me-1"></i>Add Market Expense
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
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <!-- Month Navigation -->
        <div class="card mb-3">
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
                <strong class="fs-6">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</strong>
                <a href="?month={{ $nextMonth }}&year={{ $nextYear }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ti ti-chevron-right"></i>
                </a>
                <a href="?month={{ now()->month }}&year={{ now()->year }}" class="btn btn-sm btn-primary ms-2">Today</a>
            </div>
        </div>

        <!-- Calendar -->
        <div class="card mb-4">
            <div class="card-header"><h6 class="mb-0"><i class="ti ti-calendar me-2"></i>Calendar View</h6></div>
            <div class="card-body p-0">
                <div class="row g-0 text-center cal-header-row">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="col fw-bold py-2 bg-light border-bottom border-end cal-day-hdr">
                        <span class="d-none d-sm-inline">{{ $day }}</span>
                        <span class="d-inline d-sm-none">{{ substr($day,0,1) }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="row g-0" id="calendarGrid">
                    @foreach($calendarDays as $calDay)
                    <div class="cal-cell col border-bottom border-end">
                        @if($calDay)
                        @php
                            $dateStr = $calDay->format('Y-m-d');
                            $routine = $dateRoutineMap[$dateStr] ?? null;
                            $isToday = $calDay->isToday();
                            $isFriday = $calDay->dayOfWeek === 5;
                            $isRangeStart = $routine && $routine->start_date->format('Y-m-d') === $dateStr;
                            $isRangeEnd   = $routine && $routine->end_date->format('Y-m-d') === $dateStr;
                        @endphp
                        <div class="cal-inner h-100 d-flex flex-column {{ $isToday ? 'bg-primary-subtle' : '' }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="cal-date-num fw-bold {{ $isToday ? 'text-primary' : 'text-muted' }}">{{ $calDay->day }}</span>
                                @if($isFriday)<span class="cal-fri-badge badge bg-warning-subtle text-warning d-none d-sm-inline">Fri</span>@endif
                            </div>
                            @if($routine)
                            @php
                                $statusColor = match($routine->status) {
                                    'completed' => 'success',
                                    'pending'   => 'warning',
                                    'exchanged' => 'info',
                                    default     => 'secondary',
                                };
                                $rangeLabel = '';
                                if ($isRangeStart && $isRangeEnd) $rangeLabel = '';
                                elseif ($isRangeStart) $rangeLabel = ' ▶';
                                elseif ($isRangeEnd)   $rangeLabel = ' ◀';
                                else $rangeLabel = ' ─';
                                $firstName = explode(' ', $routine->assignedTo->name)[0];
                            @endphp
                            <div class="cal-routine-block rounded p-1 bg-{{ $statusColor }}-subtle flex-grow-1 d-flex flex-column">
                                <div class="cal-name fw-semibold text-truncate">
                                    <span class="d-none d-sm-inline">{{ $routine->assignedTo->name }}{{ $rangeLabel }}</span>
                                    <span class="d-inline d-sm-none">{{ $firstName }}{{ $rangeLabel }}</span>
                                </div>
                                <span class="cal-status-badge badge bg-{{ $statusColor }} mt-auto">{{ ucfirst($routine->status) }}</span>
                                @if($routine->status === 'completed' && $routine->total_spent > 0)
                                <div class="cal-cost mt-1 rounded text-center">
                                    <span class="cal-cost-label d-none d-sm-inline text-muted">Cost: </span>
                                    <span class="fw-bold text-success">৳{{ number_format($routine->total_spent, 0) }}</span>
                                </div>
                                @endif
                                <div class="cal-actions d-flex gap-1 mt-1">
                                    <a href="{{ route('mess.market.list', [$mess->id, $routine->id]) }}"
                                       class="btn btn-xs btn-outline-primary flex-grow-1 cal-btn">
                                       <i class="ti ti-list"></i><span class="d-none d-md-inline"> List</span>
                                    </a>
                                    @if($isManager && $isRangeStart)
                                    <button onclick="openQuickAdd('{{ $dateStr }}')"
                                        class="btn btn-xs btn-outline-success cal-btn" title="Add item">
                                        <i class="ti ti-plus"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            @elseif($isManager)
                            <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                                <button onclick="prefillAssign('{{ $dateStr }}')"
                                    class="btn btn-xs btn-outline-secondary cal-btn" style="opacity:0.5"
                                    title="Assign duty for this day">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Assignments Table -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="ti ti-list-check me-2"></i>Assignments This Month</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Period</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th class="text-end">Spent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($routines as $routine)
                        @php $statusColor = match($routine->status) { 'completed'=>'success','pending'=>'warning','exchanged'=>'info',default=>'secondary' }; @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $routine->start_date->format('d M') }}
                                @if($routine->start_date->ne($routine->end_date))
                                    → {{ $routine->end_date->format('d M') }}
                                @endif
                                </div>
                                @php $days = $routine->start_date->diffInDays($routine->end_date) + 1; @endphp
                                @if($days > 1)<small class="text-muted">{{ $days }} days</small>@endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:30px;height:30px;border-radius:50%;background:#6c757d;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;flex-shrink:0">
                                        {{ strtoupper(substr($routine->assignedTo->name,0,1)) }}
                                    </div>
                                    {{ $routine->assignedTo->name }}
                                </div>
                            </td>
                            <td><span class="badge bg-{{ $statusColor }}">{{ ucfirst($routine->status) }}</span></td>
                            <td class="text-end fw-semibold">{{ $routine->total_spent > 0 ? '৳'.number_format($routine->total_spent,2) : '—' }}</td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('mess.market.list', [$mess->id, $routine->id]) }}" class="btn btn-xs btn-outline-primary">
                                        <i class="ti ti-list me-1"></i>List
                                    </a>
                                    @php
                                        $canSuperRemove = Auth::user()->is_super_admin || $isOwner;
                                        $hasItems       = $routine->listItems->isNotEmpty();
                                        $isCompleted    = $routine->status === 'completed';
                                        // Remove allowed: manager if pending+no items; owner/super always
                                        $canRemove = $isManager && (
                                            $canSuperRemove ||
                                            (! $isCompleted && ! $hasItems)
                                        );
                                    @endphp

                                    @if($routine->status === 'pending')
                                        @if($routine->assigned_to === Auth::id())
                                        <button class="btn btn-xs btn-outline-warning" onclick="openExchangeModal({{ $routine->id }}, true)">
                                            <i class="ti ti-arrows-exchange me-1"></i>Exchange
                                        </button>
                                        @elseif(!$isManager)
                                        <button class="btn btn-xs btn-outline-info" onclick="openExchangeModal({{ $routine->id }}, false)">
                                            <i class="ti ti-hand-stop me-1"></i>Volunteer
                                        </button>
                                        @endif
                                        @if($isManager)
                                        <button class="btn btn-xs btn-outline-secondary" onclick="openReassignModal({{ $routine->id }})">
                                            <i class="ti ti-edit me-1"></i>Reassign
                                        </button>
                                        <button class="btn btn-xs btn-success"
                                            onclick="openApproveModal('{{ route('mess.market.complete', [$mess->id, $routine->id]) }}','{{ addslashes($routine->assignedTo->name) }}','{{ $routine->start_date->format('d M') }}','{{ $routine->total_spent > 0 ? '৳'.number_format($routine->total_spent,0) : '' }}')">
                                            <i class="ti ti-check me-1"></i>Approve
                                        </button>
                                        @endif
                                    @endif

                                    @if($canRemove)
                                    <button class="btn btn-xs btn-outline-danger"
                                        onclick="openUnassignModal(
                                            '{{ route('mess.market.unassign', [$mess->id, $routine->id]) }}',
                                            '{{ addslashes($routine->assignedTo->name) }}',
                                            '{{ $routine->start_date->format('d M') }}{{ $routine->start_date->ne($routine->end_date) ? ' → '.$routine->end_date->format('d M') : '' }}',
                                            {{ $hasItems ? 'true' : 'false' }},
                                            {{ $isCompleted ? 'true' : 'false' }}
                                        )">
                                        <i class="ti ti-user-minus me-1"></i>Remove
                                    </button>
                                    @endif
                                    {{-- Pending exchange requests for this routine --}}
                                    @foreach($routine->exchanges->where('status','pending') as $exc)
                                        @if($exc->to_user_id === Auth::id() || ($isManager && $routine->assigned_to === Auth::id()))
                                        <form action="{{ route('mess.market.exchange.respond', [$mess->id, $exc->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="action" value="accept">
                                            <button type="submit" class="btn btn-xs btn-success" onclick="return confirm('Accept exchange from {{ $exc->fromUser->name ?? '' }}?')">
                                                <i class="ti ti-check"></i> Accept
                                            </button>
                                        </form>
                                        <form action="{{ route('mess.market.exchange.respond', [$mess->id, $exc->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-xs btn-outline-danger">
                                                <i class="ti ti-x"></i> Reject
                                            </button>
                                        </form>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No assignments this month.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Individual Market Expenses -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="ti ti-receipt me-2"></i>Individual Market Expenses This Month</h6>
                @if($isManager)
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#quickExpenseModal">
                    <i class="ti ti-plus me-1"></i>Add
                </button>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Buyer</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quickExpenses as $qe)
                        <tr>
                            <td>{{ $qe->expense_date->format('d M') }}</td>
                            <td>{{ $qe->title }}</td>
                            <td>{{ $qe->member?->name ?? '—' }}</td>
                            <td class="text-end fw-semibold">৳{{ number_format($qe->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-2 small">No individual expenses this month.</td></tr>
                        @endforelse
                    </tbody>
                    @if($quickExpenses->count() > 0)
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="3">Total</td>
                            <td class="text-end">৳{{ number_format($quickExpenses->sum('amount'), 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Assign Date Range Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-calendar-plus me-2"></i>Assign Market Duty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.market.assign', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">From Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="assignStartDate" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">To Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="assignEndDate" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assign To <span class="text-danger">*</span></label>
                        <select name="assigned_to" class="form-select" required>
                            @foreach($members as $m)
                            <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional instructions..."></textarea>
                    </div>
                    @if($isOwner)
                    <div class="alert alert-info py-2 small mb-0">
                        <i class="ti ti-info-circle me-1"></i>As owner, you can assign overlapping dates (overrides existing).
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Exchange/Volunteer Modal -->
<div class="modal fade" id="exchangeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exchangeModalTitle">Exchange Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="exchangeForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3" id="exchangeToDiv">
                        <label class="form-label fw-semibold">Exchange With</label>
                        <select name="to_user_id" class="form-select">
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
                    <button type="submit" class="btn btn-warning" id="exchangeSubmitBtn">Send Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Shared Add Items Modal (used from calendar quick-add) --}}
@php
    // For the market page modal, we use a placeholder routine; the form action is set dynamically by JS
    $calRoutine = $routines->first() ?? new \App\Models\MarketRoutine([
        'assigned_to' => null,
        'start_date'  => now(),
        'end_date'    => now(),
    ]);
    // Use first routine just for member list / defaults; action URL set by JS per cell
@endphp
@include('mess.partials.add-items-modal', [
    'addRoute' => route('mess.market.list.add', [$mess->id, $calRoutine->id ?? 0]),
    'members'  => $members,
    'routine'  => $calRoutine,
])

<!-- Quick Market Expense Modal (standalone, no routine) -->
<div class="modal fade" id="quickExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-receipt me-2"></i>Add Market Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.market.quick-expense', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Item / Description <span class="text-danger">*</span></label>
                        <input type="text" name="item_name" class="form-control" required placeholder="e.g. Fish, Vegetables">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Amount (৳) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bought By <span class="text-danger">*</span></label>
                        <select name="member_id" class="form-select" required>
                            @foreach($members as $m)
                            <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info py-2 small mb-0">
                        <i class="ti ti-info-circle me-1"></i>This expense will count toward meal cost calculation.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
// Build routine data for JS quick-add (routine id keyed by start_date)
$routineJs = [];
foreach($routines as $r) {
    $routineJs[$r->start_date->format('Y-m-d')] = $r->id;
}
@endphp

<style>
/* Calendar cell sizing */
.cal-cell {
    flex: 0 0 14.2857%;
    max-width: 14.2857%;
    min-height: clamp(70px, 12vw, 110px);
}
.cal-day-hdr {
    flex: 0 0 14.2857%;
    max-width: 14.2857%;
    font-size: clamp(9px, 2vw, 14px);
}
.cal-inner {
    padding: clamp(2px, 0.8vw, 6px);
}
.cal-date-num {
    font-size: clamp(9px, 2.2vw, 13px);
    line-height: 1;
}
.cal-routine-block {
    font-size: clamp(8px, 1.8vw, 11px);
}
.cal-name {
    font-size: clamp(8px, 1.8vw, 11px);
    line-height: 1.2;
}
.cal-status-badge {
    font-size: clamp(6px, 1.4vw, 9px);
    padding: 1px 3px;
}
.cal-cost {
    font-size: clamp(7px, 1.6vw, 10px);
    line-height: 1.3;
    background: rgba(0,0,0,.06);
    padding: 1px 2px;
}
.cal-btn {
    font-size: clamp(7px, 1.6vw, 10px);
    padding: clamp(1px, 0.3vw, 2px) clamp(2px, 0.5vw, 4px);
    min-width: 0;
}
/* On very small screens hide cost label text, keep number */
@media (max-width: 400px) {
    .cal-cost { font-size: 7px; }
    .cal-name  { font-size: 7px; }
    .cal-status-badge { display: none !important; }
}
</style>

<script>
const messId = {{ $mess->id }};
const routineByDate = @json($routineJs);
const members = @json($members->map(fn($m) => ['id' => $m->user->id, 'name' => $m->user->name])->values());
const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

function prefillAssign(dateStr) {
    document.getElementById('assignStartDate').value = dateStr;
    document.getElementById('assignEndDate').value = dateStr;
    new bootstrap.Modal(document.getElementById('assignModal')).show();
}

function openExchangeModal(routineId, isAssigned) {
    const form = document.getElementById('exchangeForm');
    form.action = `/mess/${messId}/market/${routineId}/exchange`;

    const title = document.getElementById('exchangeModalTitle');
    const toDiv = document.getElementById('exchangeToDiv');
    const btn   = document.getElementById('exchangeSubmitBtn');

    if (isAssigned) {
        title.textContent = 'Request Exchange';
        toDiv.style.display = 'block';
        btn.textContent = 'Send Exchange Request';
    } else {
        title.textContent = 'Volunteer to Take Over';
        toDiv.style.display = 'none'; // no to_user needed, backend will use assigned_to
        btn.textContent = 'Volunteer';
    }

    new bootstrap.Modal(document.getElementById('exchangeModal')).show();
}

function openReassignModal(routineId) {
    document.getElementById('assignModal').querySelectorAll('input[type=date]').forEach(el => el.value = '');
    new bootstrap.Modal(document.getElementById('assignModal')).show();
}

function openQuickAdd(dateStr) {
    const routineId = routineByDate[dateStr];
    if (!routineId) return;
    // Point the shared modal form to the correct routine
    document.getElementById('addItemsForm').action = `/mess/${messId}/market/${routineId}/list`;
    // Set the date input default
    const dateInput = document.getElementById('ai_date');
    if (dateInput) { dateInput.value = dateStr; dateInput.min = dateStr; dateInput.max = dateStr; }
    new bootstrap.Modal(document.getElementById('addItemModal')).show();
}

function openUnassignModal(action, name, period, hasItems, isCompleted) {
    document.getElementById('unassignForm').action   = action;
    document.getElementById('unassignName').textContent   = name;
    document.getElementById('unassignPeriod').textContent = period;

    var warn    = document.getElementById('unassignWarning');
    var warnTxt = document.getElementById('unassignWarningText');

    if (isCompleted) {
        warn.classList.remove('d-none');
        warnTxt.textContent = 'This routine is completed. Removing it will also delete the associated expense records.';
    } else if (hasItems) {
        warn.classList.remove('d-none');
        warnTxt.textContent = 'This routine has list items. All items and their expense records will also be deleted.';
    } else {
        warn.classList.add('d-none');
    }

    new bootstrap.Modal(document.getElementById('unassignModal')).show();
}

function openApproveModal(action, name, date, spent) {
    document.getElementById('approveRoutineForm').action = action;
    document.getElementById('approveModalName').textContent  = name;
    document.getElementById('approveModalDate').textContent  = date;
    var spentEl = document.getElementById('approveModalSpent');
    if (spent) {
        spentEl.textContent = spent;
        spentEl.closest('.approve-spent-row').classList.remove('d-none');
    } else {
        spentEl.closest('.approve-spent-row').classList.add('d-none');
    }
    new bootstrap.Modal(document.getElementById('approveRoutineModal')).show();
}
</script>

{{-- Unassign Routine Confirmation Modal --}}
<div class="modal fade" id="unassignModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <span class="rounded-circle d-flex align-items-center justify-content-center bg-danger text-white" style="width:32px;height:32px;flex-shrink:0">
                        <i class="ti ti-user-minus fs-6"></i>
                    </span>
                    Remove Assignment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <div id="unassignWarning" class="alert alert-warning py-2 small d-none">
                    <i class="ti ti-alert-triangle me-1"></i>
                    <span id="unassignWarningText"></span>
                </div>
                <p class="mb-2 text-muted small">This will permanently remove the assignment.</p>
                <div class="rounded p-3 bg-light border">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Assigned To</span>
                        <span class="fw-semibold small" id="unassignName"></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Period</span>
                        <span class="fw-semibold small" id="unassignPeriod"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Cancel
                </button>
                <form id="unassignForm" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="ti ti-user-minus me-1"></i>Yes, Remove
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Approve Routine Confirmation Modal --}}
<div class="modal fade" id="approveRoutineModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <span class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white" style="width:32px;height:32px;flex-shrink:0">
                        <i class="ti ti-check fs-6"></i>
                    </span>
                    Approve Market Routine
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-2 text-muted small">You are about to mark this routine as <strong class="text-success">completed</strong>.</p>
                <div class="rounded p-3 bg-light border">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Assigned To</span>
                        <span class="fw-semibold small" id="approveModalName"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Date</span>
                        <span class="fw-semibold small" id="approveModalDate"></span>
                    </div>
                    <div class="d-flex justify-content-between approve-spent-row">
                        <span class="text-muted small">Total Spent</span>
                        <span class="fw-bold small text-success" id="approveModalSpent"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Cancel
                </button>
                <form id="approveRoutineForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success px-4">
                        <i class="ti ti-check me-1"></i>Yes, Approve
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
