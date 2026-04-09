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
                <div class="row g-0 text-center">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="col fw-bold py-2 bg-light border-bottom border-end" style="flex:0 0 14.2857%;max-width:14.2857%">{{ $day }}</div>
                    @endforeach
                </div>
                <div class="row g-0" id="calendarGrid">
                    @foreach($calendarDays as $calDay)
                    <div class="col border-bottom border-end" style="min-height:90px;flex:0 0 14.2857%;max-width:14.2857%">
                        @if($calDay)
                        @php
                            $dateStr = $calDay->format('Y-m-d');
                            $routine = $dateRoutineMap[$dateStr] ?? null;
                            $isToday = $calDay->isToday();
                            $isFriday = $calDay->dayOfWeek === 5;
                            $isRangeStart = $routine && $routine->start_date->format('Y-m-d') === $dateStr;
                            $isRangeEnd   = $routine && $routine->end_date->format('Y-m-d') === $dateStr;
                        @endphp
                        <div class="p-1 h-100 d-flex flex-column {{ $isToday ? 'bg-primary-subtle' : '' }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold {{ $isToday ? 'text-primary' : 'text-muted' }}" style="font-size:12px">{{ $calDay->day }}</span>
                                @if($isFriday)<span class="badge bg-warning-subtle text-warning" style="font-size:8px">Fri</span>@endif
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
                            @endphp
                            <div class="rounded p-1 bg-{{ $statusColor }}-subtle flex-grow-1 d-flex flex-column" style="font-size:10px">
                                <div class="fw-semibold text-truncate">
                                    {{ $routine->assignedTo->name }}{{ $rangeLabel }}
                                </div>
                                <span class="badge bg-{{ $statusColor }} mt-auto" style="font-size:8px">{{ ucfirst($routine->status) }}</span>
                                @if($routine->total_spent > 0)
                                <span class="text-muted" style="font-size:9px">৳{{ number_format($routine->total_spent, 0) }}</span>
                                @endif
                                <div class="d-flex gap-1 mt-1">
                                    <a href="{{ route('mess.market.list', [$mess->id, $routine->id]) }}"
                                       class="btn btn-xs btn-outline-primary flex-grow-1" style="font-size:8px;padding:1px 3px">
                                       <i class="ti ti-list"></i> List
                                    </a>
                                    @if($isManager && $isRangeStart)
                                    <button onclick="openQuickAdd('{{ $dateStr }}')"
                                        class="btn btn-xs btn-outline-success" style="font-size:8px;padding:1px 4px" title="Add item">
                                        <i class="ti ti-plus"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            @elseif($isManager)
                            <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                                <button onclick="prefillAssign('{{ $dateStr }}')"
                                    class="btn btn-xs btn-outline-secondary" style="font-size:9px;opacity:0.5"
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
                                        @endif
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

<!-- Quick Add Item to Routine Modal (from calendar) -->
<div class="modal fade" id="quickAddModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-shopping-cart me-2"></i>Add Item to Routine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="quickAddContent">Loading...</div>
            </div>
        </div>
    </div>
</div>

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

    const memberOptions = members.map(m => `<option value="${m.id}">${m.name}</option>`).join('');
    const html = `
        <form id="quickAddForm">
            <div class="mb-2">
                <label class="form-label fw-semibold">Item Name</label>
                <input type="text" id="qa_item" class="form-control" required placeholder="e.g. Fish, Oil">
            </div>
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <label class="form-label">Est. Cost (৳)</label>
                    <input type="number" id="qa_est" class="form-control" step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="col-6">
                    <label class="form-label">Actual Cost (৳)</label>
                    <input type="number" id="qa_actual" class="form-control" step="0.01" min="0" placeholder="0.00">
                </div>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <label class="form-label">Quantity</label>
                    <input type="text" id="qa_qty" class="form-control" placeholder="e.g. 2 kg">
                </div>
                <div class="col-6">
                    <label class="form-label">Buyer (optional)</label>
                    <select id="qa_buyer" class="form-select">
                        <option value="">— Default —</option>
                        ${memberOptions}
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" id="qa_date" class="form-control" value="${dateStr}">
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary flex-grow-1" onclick="submitQuickAdd(${routineId})">
                    <i class="ti ti-plus me-1"></i>Add Item
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>`;

    document.getElementById('quickAddContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('quickAddModal')).show();
}

function submitQuickAdd(routineId) {
    const item = document.getElementById('qa_item').value.trim();
    if (!item) { alert('Item name required'); return; }

    const formData = new FormData();
    formData.append('_token', csrf);
    formData.append('item_name', item);
    formData.append('estimated_cost', document.getElementById('qa_est').value || 0);
    formData.append('actual_cost', document.getElementById('qa_actual').value || 0);
    formData.append('quantity', document.getElementById('qa_qty').value);
    const buyer = document.getElementById('qa_buyer').value;
    if (buyer) formData.append('assigned_to', buyer);
    formData.append('expense_date', document.getElementById('qa_date').value);

    fetch(`/mess/${messId}/market/${routineId}/list`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf },
        body: formData,
    }).then(r => {
        if (r.ok) { location.reload(); }
        else { alert('Error adding item.'); }
    });
}
</script>
@endsection
