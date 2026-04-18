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
                <h6 class="fs-14">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h6>
            </div>
            <div class="page-btn d-flex gap-2 flex-wrap">
                @if($member)
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignModal">
                    <i class="ti ti-user-check me-1"></i>Assign Dates
                </button>
                @endif
                @if($isManager)
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
                            $calPalette = [
                                '#fff8e1','#fce4ec','#e8f5e9','#e3f2fd','#f3e5f5',
                                '#e0f7fa','#fff3e0','#e8eaf6','#f1f8e9','#fdf3e7',
                                '#fce8ff','#e0f2f1','#fff9c4','#e1f5fe','#fbe9e7',
                                '#e8f8f5','#fef9e7','#eaf4fb','#fdedec','#f9ebea',
                            ];
                            $cellBg = $isToday ? '#dbeafe' : $calPalette[$calDay->day % count($calPalette)];
                        @endphp
                        @php
                            $hasItems      = $routine ? $routine->listItems->count() > 0 : false;
                            $displayStatus = $routine ? ($routine->status === 'pending' && !$hasItems ? 'assigned' : $routine->status) : null;
                            $statusColor   = match($displayStatus) {
                                'completed' => 'success',
                                'pending'   => 'warning',
                                'exchanged' => 'info',
                                'assigned'  => 'secondary',
                                default     => null,
                            };
                            $firstName    = $routine ? explode(' ', $routine->assignedTo->name)[0] : null;
                            $isAssignedToMe = $routine && $routine->assigned_to === Auth::id();
                            $canAddItem   = $routine && $routine->status !== 'completed' && ($isManager || $isAssignedToMe);
                            $calCanSuperRemove  = Auth::user()->is_super_admin || $isOwner;
                            $calIsCompleted     = $routine && $routine->status === 'completed';
                            $calHasItems        = $routine && $routine->listItems->isNotEmpty();
                            $calIsAssignedToMe2 = $routine && $routine->assigned_to === Auth::id();
                            $calCanRemove       = $routine && ($calCanSuperRemove
                                || ($isManager && !$calIsCompleted)
                                || ($calIsAssignedToMe2 && !$calIsCompleted && !$calHasItems));
                            // build JSON for mobile modal
                            $dayData = json_encode([
                                'date'        => $dateStr,
                                'label'       => $calDay->format('d M Y') . ($isFriday ? ' (Fri)' : ''),
                                'hasRoutine'  => (bool)$routine,
                                'name'        => $routine ? $routine->assignedTo->name : null,
                                'status'      => $displayStatus,
                                'statusColor' => $statusColor,
                                'totalSpent'  => $routine ? $routine->total_spent : 0,
                                'listUrl'     => $routine ? route('mess.market.list', [$mess->id, $routine->id]) : null,
                                'canAdd'      => (bool)($canAddItem && $isRangeStart),
                                'canRemove'   => (bool)$calCanRemove,
                                'unassignUrl' => $routine ? route('mess.market.unassign', [$mess->id, $routine->id]) : null,
                                'canAssign'   => !$routine && (bool)$member,
                                'hasItems'    => (bool)$calHasItems,
                                'isCompleted' => (bool)$calIsCompleted,
                            ]);
                        @endphp
                        {{-- Desktop cell --}}
                        <div class="cal-inner h-100 d-flex flex-column d-none d-sm-flex" style="background:{{ $cellBg }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="cal-date-num fw-bold {{ $isToday ? 'text-primary' : 'text-muted' }}">{{ $calDay->day }}</span>
                                @if($isFriday)<span class="cal-fri-badge badge bg-warning-subtle text-warning d-none d-sm-inline">Fri</span>@endif
                            </div>
                            @if($routine)
                            <div class="cal-routine-block rounded p-1 bg-{{ $statusColor }}-subtle flex-grow-1 d-flex flex-column">
                                <div class="cal-name fw-semibold text-truncate">{{ $routine->assignedTo->name }}</div>
                                <span class="cal-status-badge badge bg-{{ $statusColor }} mt-auto">{{ ucfirst($displayStatus) }}</span>
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
                                    @if($canAddItem && $isRangeStart)
                                    <button onclick="openQuickAdd('{{ $dateStr }}')"
                                        class="btn btn-xs btn-outline-success cal-btn" title="Add item">
                                        <i class="ti ti-plus"></i>
                                    </button>
                                    @endif
                                    @if($calCanRemove)
                                    <button class="btn btn-xs btn-outline-danger cal-btn" title="Remove"
                                        onclick="openUnassignModal('{{ route('mess.market.unassign', [$mess->id, $routine->id]) }}','{{ addslashes($routine->assignedTo->name) }}','{{ $routine->start_date->format('d M Y') }}',{{ $calHasItems ? 'true' : 'false' }},{{ $calIsCompleted ? 'true' : 'false' }})">
                                        <i class="ti ti-x"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            @elseif($isManager || $member)
                            <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                                <button onclick="prefillAssign('{{ $dateStr }}')"
                                    class="btn btn-xs btn-outline-{{ $isManager ? 'secondary' : 'primary' }} cal-btn" style="opacity:0.55">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                        {{-- Mobile cell: minimal, tap opens detail modal --}}
                        <div class="cal-mobile-cell d-flex d-sm-none flex-column align-items-center justify-content-start pt-1 h-100"
                             style="background:{{ $cellBg }}"
                             onclick="openDayModal({{ $dayData }})" style="cursor:pointer;min-height:52px">
                            <span class="fw-bold {{ $isToday ? 'text-primary' : 'text-muted' }}" style="font-size:13px;line-height:1.2">{{ $calDay->day }}</span>
                            @if($routine)
                            <i class="ti ti-list mt-auto mb-1 text-{{ $statusColor }}" style="font-size:12px"></i>
                            @elseif($member || $isManager)
                            <i class="ti ti-plus mt-auto mb-1 text-muted" style="font-size:11px"></i>
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
                        @php
                            $hasItems = $routine->listItems->count() > 0;
                            $displayStatus = $routine->status === 'pending' && !$hasItems ? 'assigned' : $routine->status;
                            $statusColor = match($displayStatus) { 'completed'=>'success','pending'=>'warning','exchanged'=>'info',default=>'secondary' };
                        @endphp
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
                                    @if($routine->assignedTo->avatar)
                                    <img src="{{ asset('storage/'.$routine->assignedTo->avatar) }}" class="rounded-circle" style="width:30px;height:30px;object-fit:cover;flex-shrink:0;" alt="">
                                    @else
                                    <div style="width:30px;height:30px;border-radius:50%;background:#6c757d;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;flex-shrink:0">
                                        {{ strtoupper(substr($routine->assignedTo->name,0,1)) }}
                                    </div>
                                    @endif
                                    {{ $routine->assignedTo->name }}
                                </div>
                            </td>
                            <td><span class="badge bg-{{ $statusColor }}">{{ ucfirst($displayStatus) }}</span></td>
                            <td class="text-end fw-semibold">{{ $routine->total_spent > 0 ? '৳'.number_format($routine->total_spent,2) : '—' }}</td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('mess.market.list', [$mess->id, $routine->id]) }}" class="btn btn-xs btn-outline-primary">
                                        <i class="ti ti-list me-1"></i>List
                                    </a>
                                    @php
                                        $canSuperRemove  = Auth::user()->is_super_admin || $isOwner;
                                        $hasItems        = $routine->listItems->isNotEmpty();
                                        $isCompleted     = $routine->status === 'completed';
                                        $isAssignedToMe2 = $routine->assigned_to === Auth::id();
                                        // Manager/owner/super: can always remove (owner/super even if completed)
                                        // Assigned member: can remove own pending assignment only if no items
                                        $canRemove = $canSuperRemove
                                            || ($isManager && !$isCompleted)
                                            || ($isAssignedToMe2 && !$isCompleted && !$hasItems);
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
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#quickExpenseModal">
                    <i class="ti ti-plus me-1"></i>Add
                </button>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-calendar-plus me-2"></i>Assign Market Duty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.market.assign', $mess->id) }}" method="POST" id="assignForm">
                @csrf
                <div class="modal-body">
                    <!-- Member selector -->
                    @if($isManager)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assign To <span class="text-danger">*</span></label>
                        <select name="assigned_to" class="form-select" required>
                            @foreach($members as $m)
                            <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <input type="hidden" name="assigned_to" value="{{ Auth::id() }}">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assigning As</label>
                        <div class="form-control bg-light text-muted">{{ Auth::user()->name }}</div>
                    </div>
                    @endif

                    <!-- Multi-select calendar -->
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Select Dates <span class="text-danger">*</span>
                            <small class="text-muted fw-normal ms-2">Click dates to toggle selection</small>
                        </label>
                    </div>
                    <!-- Calendar nav -->
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="assignCalPrev"><i class="ti ti-chevron-left"></i></button>
                        <span class="fw-semibold flex-grow-1 text-center" id="assignCalTitle"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="assignCalNext"><i class="ti ti-chevron-right"></i></button>
                    </div>
                    <div id="assignCalGrid" class="assign-cal-grid mb-2"></div>
                    <!-- Hidden date inputs injected by JS -->
                    <div id="assignDatesInputs"></div>
                    <!-- Selected summary -->
                    <div id="assignSelectedSummary" class="small text-muted mb-2"></div>

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
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="assignClearBtn">Clear Selection</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="assignSubmitBtn" disabled>Assign Selected Dates</button>
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
                    @if($isManager)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bought By <span class="text-danger">*</span></label>
                        <select name="member_id" class="form-select" required>
                            @foreach($members as $m)
                            <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <input type="hidden" name="member_id" value="{{ Auth::id() }}">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bought By</label>
                        <div class="form-control bg-light text-muted">{{ Auth::user()->name }}</div>
                    </div>
                    @endif
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
/* Mobile calendar cell */
.cal-mobile-cell { border-radius: 0; transition: background .1s; }
.cal-mobile-cell:active { background: #e9ecef !important; }

/* Assign modal multi-select calendar */
.assign-cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 8px;
    background: #f8f9fa;
}
.assign-cal-header {
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    color: #6c757d;
    padding: 4px 0;
    text-transform: uppercase;
}
.assign-cal-day {
    text-align: center;
    padding: 6px 2px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    border: 2px solid transparent;
    transition: all .15s;
    user-select: none;
    background: #fff;
}
.assign-cal-day:hover:not(.ac-empty):not(.ac-taken) { background: #d1e7dd; border-color: #198754; }
.assign-cal-day.ac-empty { visibility: hidden; }
.assign-cal-day.ac-today { border-color: #0d6efd; background: #e7f1ff; }
.assign-cal-day.ac-selected { background: #198754; color: #fff; border-color: #198754; }
.assign-cal-day.ac-taken { background: #f8d7da; color: #842029; cursor: not-allowed; font-size: 10px; }
.assign-cal-day.ac-taken:hover { background: #f8d7da; border-color: transparent; }
.assign-cal-day.ac-weekend { color: #6f42c1; }
.assign-cal-day.ac-selected.ac-weekend { color: #fff; }
</style>

<script>
const messId = {{ $mess->id }};
const routineByDate = @json($routineJs);
const members = @json($members->map(fn($m) => ['id' => $m->user->id, 'name' => $m->user->name])->values());
const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

// ---- Multi-select assign calendar ----
const takenDates = new Set(@json(array_keys($dateRoutineMap)));
let acYear  = {{ $year }};
let acMonth = {{ $month }}; // 1-based
let acSelected = new Set();

function acRender() {
    const grid  = document.getElementById('assignCalGrid');
    const title = document.getElementById('assignCalTitle');
    const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    title.textContent = months[acMonth - 1] + ' ' + acYear;

    const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    let html = days.map(d => `<div class="assign-cal-header">${d}</div>`).join('');

    const first = new Date(acYear, acMonth - 1, 1);
    const daysInMonth = new Date(acYear, acMonth, 0).getDate();
    const startDow = first.getDay();
    const today = new Date().toISOString().slice(0,10);

    for (let i = 0; i < startDow; i++) html += `<div class="assign-cal-day ac-empty"></div>`;
    for (let d = 1; d <= daysInMonth; d++) {
        const dt   = `${acYear}-${String(acMonth).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const dow  = (startDow + d - 1) % 7;
        const isTaken    = takenDates.has(dt);
        const isSelected = acSelected.has(dt);
        const isToday    = dt === today;
        const isWeekend  = dow === 0 || dow === 6;
        let cls = 'assign-cal-day';
        if (isTaken)    cls += ' ac-taken';
        else if (isSelected) cls += ' ac-selected' + (isWeekend ? ' ac-weekend' : '');
        else { if (isToday) cls += ' ac-today'; if (isWeekend) cls += ' ac-weekend'; }
        const label = isTaken ? `${d}<br><span style="font-size:9px">taken</span>` : d;
        html += `<div class="${cls}" data-date="${dt}" onclick="acToggle('${dt}',${isTaken})">${label}</div>`;
    }
    grid.innerHTML = html;
    acUpdateInputs();
}

function acToggle(dt, isTaken) {
    if (isTaken) return;
    acSelected.has(dt) ? acSelected.delete(dt) : acSelected.add(dt);
    acRender();
}

function acUpdateInputs() {
    const container = document.getElementById('assignDatesInputs');
    const summary   = document.getElementById('assignSelectedSummary');
    const btn       = document.getElementById('assignSubmitBtn');
    const sorted    = [...acSelected].sort();
    container.innerHTML = sorted.map(d => `<input type="hidden" name="dates[]" value="${d}">`).join('');
    if (sorted.length === 0) {
        summary.textContent = 'No dates selected';
        btn.disabled = true;
    } else {
        summary.textContent = `${sorted.length} date(s) selected: ${sorted.join(', ')}`;
        btn.disabled = false;
    }
}

document.getElementById('assignCalPrev').addEventListener('click', () => {
    acMonth--; if (acMonth < 1) { acMonth = 12; acYear--; } acRender();
});
document.getElementById('assignCalNext').addEventListener('click', () => {
    acMonth++; if (acMonth > 12) { acMonth = 1; acYear++; } acRender();
});
document.getElementById('assignClearBtn').addEventListener('click', () => {
    acSelected.clear(); acRender();
});
document.getElementById('assignModal').addEventListener('shown.bs.modal', () => acRender());

function prefillAssign(dateStr) {
    acSelected.clear();
    acSelected.add(dateStr);
    // Navigate calendar to the date's month
    const parts = dateStr.split('-');
    acYear = parseInt(parts[0]); acMonth = parseInt(parts[1]);
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

function openMemberExpense(dateStr) {
    var dateInput = document.querySelector('#quickExpenseModal input[name="expense_date"]');
    if (dateInput) dateInput.value = dateStr;
    new bootstrap.Modal(document.getElementById('quickExpenseModal')).show();
}

function openReassignModal(routineId) {
    acSelected.clear();
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

function openDayModal(d) {
    document.getElementById('dayDetailTitle').textContent = d.label;
    const body   = document.getElementById('dayDetailBody');
    const footer = document.getElementById('dayDetailFooter');
    let html = '';

    if (d.hasRoutine) {
        const colorMap = {success:'#198754',warning:'#ffc107',info:'#0dcaf0',secondary:'#6c757d'};
        const dotColor = colorMap[d.statusColor] || '#6c757d';
        html += `<div class="d-flex align-items-center gap-2 mb-3">
            <span style="width:10px;height:10px;border-radius:50%;background:${dotColor};display:inline-block;flex-shrink:0"></span>
            <div>
                <div class="fw-bold fs-6">${d.name}</div>
                <span class="badge" style="background:${dotColor};font-size:11px">${d.status.charAt(0).toUpperCase()+d.status.slice(1)}</span>
            </div>
        </div>`;
        if (d.totalSpent > 0) {
            html += `<div class="alert alert-success py-2 mb-2 d-flex justify-content-between align-items-center">
                <span><i class="ti ti-receipt me-1"></i>Total Spent</span>
                <strong>৳${Number(d.totalSpent).toLocaleString()}</strong>
            </div>`;
        }
    } else {
        html += `<p class="text-muted mb-2"><i class="ti ti-calendar-off me-1"></i>No assignment for this date.</p>`;
    }

    body.innerHTML = html;

    // Footer actions
    let fhtml = `<button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>`;
    if (d.hasRoutine && d.listUrl) {
        fhtml += `<a href="${d.listUrl}" class="btn btn-primary btn-sm"><i class="ti ti-list me-1"></i>View List</a>`;
    }
    if (d.canAdd) {
        fhtml += `<button class="btn btn-success btn-sm" onclick="bootstrap.Modal.getInstance(document.getElementById('dayDetailModal')).hide();openQuickAdd('${d.date}')">
            <i class="ti ti-plus me-1"></i>Add Item</button>`;
    }
    if (d.canAssign) {
        fhtml += `<button class="btn btn-outline-primary btn-sm" onclick="bootstrap.Modal.getInstance(document.getElementById('dayDetailModal')).hide();prefillAssign('${d.date}')">
            <i class="ti ti-user-check me-1"></i>Assign</button>`;
    }
    if (d.canRemove && d.unassignUrl) {
        fhtml += `<button class="btn btn-danger btn-sm" onclick="bootstrap.Modal.getInstance(document.getElementById('dayDetailModal')).hide();openUnassignModal('${d.unassignUrl}','${d.name}','${d.label}',${d.hasItems},${d.isCompleted})">
            <i class="ti ti-x me-1"></i>Remove</button>`;
    }
    footer.innerHTML = fhtml;

    new bootstrap.Modal(document.getElementById('dayDetailModal')).show();
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

{{-- Mobile Day Detail Modal --}}
<div class="modal fade" id="dayDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title fw-bold" id="dayDetailTitle"></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="dayDetailBody"></div>
            <div class="modal-footer py-2 d-flex flex-wrap gap-2" id="dayDetailFooter"></div>
        </div>
    </div>
</div>

{{-- Unassign Routine Confirmation Modal --}}
<div class="modal fade" id="unassignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
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
