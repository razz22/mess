<?php $page = "mess-report" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">Monthly Report — {{ $mess->name }}</h4>
                <h6>{{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</h6>
            </div>
            <div class="page-btn d-flex gap-2">
                <a href="{{ route('mess.dashboard', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <!-- Month Filter -->
        <div class="card mb-4">
            <div class="card-body py-2">
                <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                    <select name="month" class="form-select form-select-sm" style="width:130px" onchange="this.form.submit()">
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                    <select name="year" class="form-select form-select-sm" style="width:100px" onchange="this.form.submit()">
                        @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>
        </div>

        <!-- Overview Stats -->
        <div class="row g-3 mb-4">
            {{-- Row 1: Cost breakdown --}}
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 text-center">
                        <div class="text-muted small mb-1"><i class="ti ti-shopping-cart me-1"></i>Market Cost</div>
                        <div class="fs-4 fw-bold text-primary">৳{{ number_format($totalMarket, 2) }}</div>
                        <div class="text-muted" style="font-size:11px">Bazaar → drives meal rate</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 text-center">
                        <div class="text-muted small mb-1"><i class="ti ti-receipt me-1"></i>Shared Expenses</div>
                        <div class="fs-4 fw-bold text-warning">৳{{ number_format($totalNonMarket, 2) }}</div>
                        <div class="text-muted" style="font-size:11px">Cook, bills, utilities → split equally</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 text-center">
                        <div class="text-muted small mb-1"><i class="ti ti-report-money me-1"></i>Total Expenses</div>
                        <div class="fs-4 fw-bold text-danger">৳{{ number_format($totalExpenses, 2) }}</div>
                        <div class="text-muted" style="font-size:11px">Market + Other</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 text-center">
                        <div class="text-muted small mb-1"><i class="ti ti-cash me-1"></i>Total Deposits</div>
                        <div class="fs-4 fw-bold text-success">৳{{ number_format($totalDeposits, 2) }}</div>
                        <div class="text-muted" style="font-size:11px">All members combined</div>
                    </div>
                </div>
            </div>

            {{-- Row 2: Meal & balance stats --}}
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 text-center">
                        <div class="text-muted small mb-1"><i class="ti ti-bowl me-1"></i>Total Meals</div>
                        <div class="fs-4 fw-bold text-info">{{ number_format($totalMealCount, 1) }}</div>
                        <div class="text-muted" style="font-size:11px">All members combined</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 text-center">
                        <div class="text-muted small mb-1"><i class="ti ti-coin me-1"></i>Per Meal Rate</div>
                        @if($costMode === 'daily')
                        <div class="fs-5 fw-bold text-primary">Daily</div>
                        <div class="text-muted" style="font-size:11px">Rate varies by day</div>
                        @else
                        <div class="fs-4 fw-bold text-primary">৳{{ number_format($perMealCost, 2) }}</div>
                        <div class="text-muted" style="font-size:11px">Market ÷ Total meals</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 text-center">
                        <div class="text-muted small mb-1"><i class="ti ti-alert-triangle me-1"></i>Total Due</div>
                        <div class="fs-4 fw-bold {{ $totalDue > 0 ? 'text-danger' : 'text-success' }}">৳{{ number_format($totalDue, 2) }}</div>
                        <div class="text-muted" style="font-size:11px">Members who owe</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 text-center">
                        <div class="text-muted small mb-1"><i class="ti ti-wallet me-1"></i>Cash in Hand</div>
                        <div class="fs-4 fw-bold text-{{ $cashInHand >= 0 ? 'teal' : 'danger' }}" style="{{ $cashInHand >= 0 ? 'color:#0d9488' : '' }}">৳{{ number_format(abs($cashInHand), 2) }}</div>
                        <div class="text-muted" style="font-size:11px">{{ $cashInHand >= 0 ? 'Surplus' : 'Deficit' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Member Wise Report -->
        @php $isManager = $member && in_array($member->role, ['owner', 'manager']); @endphp
        <div class="card mb-4">
            <div class="card-header"><h6 class="mb-0">Member-Wise Summary</h6></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Member</th>
                            <th class="text-center">Meals</th>
                            <th class="text-center">Per Meal Rate</th>
                            <th class="text-end">Meal Cost</th>
                            <th>
                                Expense Cost
                                <span class="text-muted" style="font-size:10px;font-weight:normal;display:block;">Shared expenses ÷ members</span>
                            </th>
                            <th class="text-end">Total Payable</th>
                            <th class="text-end">Deposited</th>
                            <th class="text-end">Due / Extra</th>
                            <th>Status</th>
                            @if($isManager) <th></th> @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $m)
                        @php
                            $summary      = $summaries[$m->user->id] ?? null;
                            $excluded     = $summary?->exclude_from_shared ?? false;
                            $catExclIds   = $memberCategoryExclusions[$m->user->id] ?? [];
                            $catExclCount = count($catExclIds);
                        @endphp
                        <tr id="row-{{ $m->user->id }}" class="{{ $excluded ? 'table-secondary opacity-75' : '' }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($m->user->avatar)
                                    <img src="{{ asset('storage/'.$m->user->avatar) }}" class="rounded-circle" style="width:34px;height:34px;object-fit:cover;flex-shrink:0;" alt="">
                                    @else
                                    <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold" style="width:34px;height:34px;font-size:13px;flex-shrink:0;">{{ strtoupper(substr($m->user->name,0,1)) }}</span>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $m->user->name }}</div>
                                        <span class="badge bg-{{ $m->role === 'owner' ? 'danger' : ($m->role === 'manager' ? 'warning' : 'secondary') }} fs-10">{{ ucfirst($m->role) }}</span>
                                    </div>
                                </div>
                            </td>
                            @if($summary)
                            @php
                                $memberMeals = (float)$summary->total_meal_days;
                                $memberRate  = ($costMode === 'daily')
                                    ? ($memberMeals > 0 ? $summary->meal_cost / $memberMeals : 0)
                                    : $perMealCost;
                            @endphp
                            <td class="text-center fw-bold">{{ number_format($memberMeals, 1) }}</td>
                            <td class="text-center">
                                @if($costMode === 'daily')
                                <span class="text-muted small">৳{{ number_format($memberRate, 2) }}<br><span style="font-size:10px">avg/meal</span></span>
                                @else
                                <span class="badge bg-primary-subtle text-primary">৳{{ number_format($perMealCost, 2) }}</span>
                                @endif
                            </td>
                            <td class="text-end">৳{{ number_format($summary->meal_cost, 2) }}</td>
                            {{-- Expense Cost: member's share of non-meal shared expenses --}}
                            <td>
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <span class="fw-semibold">৳{{ number_format($summary->total_expenses, 2) }}</span>
                                    @if($isManager && $m->user_id !== $mess->owner_id)
                                    {{-- Remove ALL expenses for this member (not for owner) --}}
                                    <button class="btn btn-xs {{ $excluded ? 'btn-success' : 'btn-outline-warning' }} py-0 px-1"
                                        id="toggle-btn-{{ $m->user->id }}"
                                        onclick="toggleShared({{ $m->user->id }}, {{ $excluded ? 'true' : 'false' }})"
                                        title="{{ $excluded ? 'Include in all shared expenses' : 'Exclude from ALL shared expenses' }}">
                                        <i class="ti {{ $excluded ? 'ti-user-check' : 'ti-user-minus' }}" style="font-size:11px"></i>
                                    </button>
                                    @endif
                                    @if($isManager && !$excluded)
                                    {{-- Remove individual categories --}}
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-1"
                                        onclick="openCategoryModal({{ $m->user->id }}, '{{ addslashes($m->user->name) }}')"
                                        title="Remove individual expense categories">
                                        <i class="ti ti-adjustments-horizontal" style="font-size:11px"></i>
                                    </button>
                                    @endif
                                    @if($excluded)
                                    <span class="badge bg-secondary" title="Excluded from all shared expenses">No expense</span>
                                    @elseif($catExclCount > 0)
                                    <span class="badge bg-warning text-dark" title="{{ $catExclCount }} category(s) excluded">-{{ $catExclCount }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end fw-bold">৳{{ number_format($summary->total_payable, 2) }}</td>
                            <td class="text-end text-success fw-bold">৳{{ number_format($summary->total_deposit, 2) }}</td>
                            <td class="text-end fw-bold {{ $summary->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $summary->due_amount > 0 ? '-' : '+' }}৳{{ number_format(abs($summary->due_amount), 2) }}
                            </td>
                            <td>
                                @if($summary->status === 'paid_out')
                                <span class="badge bg-secondary">Paid Out</span>
                                @elseif($summary->status === 'carried_forward')
                                <span class="badge bg-info text-dark">Carried Fwd</span>
                                @elseif($summary->status === 'settled')
                                <span class="badge bg-success">Settled</span>
                                @elseif($summary->due_amount > 0)
                                <span class="badge bg-danger">Due</span>
                                @else
                                <span class="badge bg-warning text-dark">Extra</span>
                                @endif
                            </td>
                            @if($isManager)
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <button class="btn btn-xs btn-outline-success py-0 px-2"
                                        onclick="openDepositModal({{ $m->user->id }}, '{{ addslashes($m->user->name) }}')"
                                        title="Add deposit">
                                        <i class="ti ti-cash me-1" style="font-size:11px"></i>Deposit
                                    </button>
                                    @if($summary->due_amount < 0 && !in_array($summary->status, ['paid_out','carried_forward']))
                                    <button class="btn btn-xs btn-outline-info py-0 px-2"
                                        onclick="openExtraModal({{ $m->user->id }}, '{{ addslashes($m->user->name) }}', {{ abs($summary->due_amount) }})"
                                        title="Manage extra balance">
                                        <i class="ti ti-coins me-1" style="font-size:11px"></i>Extra
                                    </button>
                                    @endif
                                </div>
                            </td>
                            @endif
                            @else
                            <td colspan="8" class="text-muted text-center fst-italic">No data — report not generated yet</td>
                            @if($isManager)
                            <td>
                                <button class="btn btn-xs btn-outline-success py-0 px-2"
                                    onclick="openDepositModal({{ $m->user->id }}, '{{ addslashes($m->user->name) }}')"
                                    title="Add deposit for {{ $m->user->name }}">
                                    <i class="ti ti-cash me-1" style="font-size:11px"></i>Deposit
                                </button>
                            </td>
                            @endif
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Expense by Category -->
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Expenses by Category</h6></div>
                    <div class="card-body">
                        @forelse($expensesByCategory as $cat)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span>{{ $cat['name'] }}</span>
                            <strong class="text-danger">৳{{ number_format($cat['amount'], 2) }}</strong>
                        </div>
                        @empty
                        <p class="text-muted text-center">No expenses</p>
                        @endforelse
                        @if($expensesByCategory->isNotEmpty())
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-1">
                            <span class="fw-bold">Total</span>
                            <strong class="text-danger">৳{{ number_format($expensesByCategory->sum('amount'), 2) }}</strong>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Meal Attendance Summary</h6></div>
                    <div class="card-body">
                        @foreach($members as $m)
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <span class="small">{{ $m->user->name }}</span>
                            <span class="badge bg-primary">{{ $mealByMember[$m->user->id] ?? 0 }} meals</span>
                        </div>
                        @endforeach
                        <div class="d-flex justify-content-between mt-2 fw-bold">
                            <span>Total Meals</span>
                            <span>{{ $mealByMember->sum() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manage Extra Modal -->
<div class="modal fade" id="extraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Extra Balance — <span id="extraMemberName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success mb-3">
                    <i class="ti ti-coins me-1"></i>
                    Extra balance: <strong id="extraAmountDisplay"></strong>
                </div>
                <p class="text-muted small mb-3">Choose what to do with this extra amount:</p>

                <!-- Option 1: Pay out -->
                <div class="card mb-3 border-secondary">
                    <div class="card-body py-3">
                        <h6 class="fw-semibold mb-1"><i class="ti ti-cash-banknote me-1 text-secondary"></i>Pay Out to Member</h6>
                        <p class="text-muted small mb-2">Record that the extra amount was physically given back to the member. This clears the balance and it will not carry forward.</p>
                        <button class="btn btn-secondary btn-sm" id="payOutBtn" onclick="payExtraOut()">
                            <i class="ti ti-check me-1"></i>Mark as Paid Out
                        </button>
                    </div>
                </div>

                <!-- Option 2: Carry to next month as deposit -->
                <div class="card border-info">
                    <div class="card-body py-3">
                        <h6 class="fw-semibold mb-1"><i class="ti ti-arrow-forward me-1 text-info"></i>Add as Next Month Deposit</h6>
                        <p class="text-muted small mb-2">Create an explicit deposit entry for next month. This will appear in the next month's deposit list.</p>
                        <div class="mb-2">
                            <input type="text" id="carryNoteInput" class="form-control form-control-sm" placeholder="Note (optional)" maxlength="255">
                        </div>
                        <button class="btn btn-info btn-sm text-white" id="carryBtn" onclick="carryExtraAsDeposit()">
                            <i class="ti ti-arrow-right me-1"></i>Add as Next Month Deposit
                        </button>
                    </div>
                </div>

                <div id="extraResultMsg" class="mt-3 d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Deposit — <span id="depositMemberName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.deposits.store', $mess->id) }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="depositUserId">
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control form-control-lg" required step="0.01" min="0.01" placeholder="0.00" autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deposit Date <span class="text-danger">*</span></label>
                        <input type="date" name="deposit_date" id="depositDate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <input type="text" name="note" class="form-control" placeholder="Optional note">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="ti ti-cash me-1"></i>Save Deposit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Category Exclusion Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-adjustments-horizontal me-2 text-primary"></i>Expense Cost — <span id="catModalMemberName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="catModalBody">
                <p class="text-muted text-center">Loading…</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
var csrf   = document.querySelector('meta[name="csrf-token"]').content;
var month  = {{ $month }};
var year   = {{ $year }};
var reportBase = '{{ url("mess/" . $mess->id . "/report") }}';

var expenseCategories   = @json($categoriesForModal);   // non-market categories with per_head cost
var memberCatExclusions = @json($memberCategoryExclusions);

function toggleShared(userId, currentlyExcluded) {
    var btn = document.getElementById('toggle-btn-' + userId);
    if (btn) btn.disabled = true;

    fetch(reportBase + '/toggle-shared', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ user_id: userId, month: month, year: year })
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success) {
            location.reload();
        } else {
            if (btn) btn.disabled = false;
        }
    })
    .catch(function () {
        if (btn) btn.disabled = false;
        alert('Request failed. Please try again.');
    });
}

var extraUserId = null;

function openExtraModal(userId, userName, amount) {
    extraUserId = userId;
    document.getElementById('extraMemberName').textContent = userName;
    document.getElementById('extraAmountDisplay').textContent = '৳' + parseFloat(amount).toFixed(2);
    document.getElementById('carryNoteInput').value = '';
    document.getElementById('extraResultMsg').classList.add('d-none');
    document.getElementById('payOutBtn').disabled = false;
    document.getElementById('carryBtn').disabled = false;
    var modal = new bootstrap.Modal(document.getElementById('extraModal'));
    modal.show();
}

function payExtraOut() {
    if (!extraUserId) return;
    document.getElementById('payOutBtn').disabled = true;
    fetch(reportBase + '/pay-extra', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ user_id: extraUserId, month: month, year: year })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showExtraResult('success', data.message);
            setTimeout(function() { location.reload(); }, 1200);
        } else {
            showExtraResult('danger', data.message || 'Failed.');
            document.getElementById('payOutBtn').disabled = false;
        }
    })
    .catch(function() {
        showExtraResult('danger', 'Request failed. Please try again.');
        document.getElementById('payOutBtn').disabled = false;
    });
}

function carryExtraAsDeposit() {
    if (!extraUserId) return;
    var note = document.getElementById('carryNoteInput').value;
    document.getElementById('carryBtn').disabled = true;
    fetch(reportBase + '/carry-extra', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ user_id: extraUserId, month: month, year: year, note: note })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showExtraResult('success', data.message);
            setTimeout(function() { location.reload(); }, 1400);
        } else {
            showExtraResult('danger', data.message || 'Failed.');
            document.getElementById('carryBtn').disabled = false;
        }
    })
    .catch(function() {
        showExtraResult('danger', 'Request failed. Please try again.');
        document.getElementById('carryBtn').disabled = false;
    });
}

function showExtraResult(type, msg) {
    var el = document.getElementById('extraResultMsg');
    el.className = 'mt-3 alert alert-' + type;
    el.textContent = msg;
}

function openDepositModal(userId, userName) {
    document.getElementById('depositUserId').value      = userId;
    document.getElementById('depositMemberName').textContent = userName;
    document.getElementById('depositDate').value        = new Date().toISOString().split('T')[0];
    var modal = new bootstrap.Modal(document.getElementById('depositModal'));
    modal.show();
}

var currentModalUserId = null;

function openCategoryModal(userId, userName) {
    currentModalUserId = userId;
    document.getElementById('catModalMemberName').textContent = userName;
    renderCategoryModal(userId);
    var modal = new bootstrap.Modal(document.getElementById('categoryModal'));
    modal.show();
}

function renderCategoryModal(userId) {
    var body = document.getElementById('catModalBody');
    var excluded = memberCatExclusions[userId] || [];

    if (!expenseCategories || expenseCategories.length === 0) {
        body.innerHTML = '<p class="text-muted text-center py-3">No shared expense categories this month.</p>';
        return;
    }

    var html = '<div class="list-group list-group-flush">';
    expenseCategories.forEach(function(cat) {
        var isExcluded = excluded.indexOf(cat.id) !== -1;
        html += '<div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">';
        html += '<div>';
        html += '<span class="fw-semibold">' + cat.name + '</span>';
        if (cat.is_recurring) {
            html += ' <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 ms-1" style="font-size:10px;">Recurring</span>';
        }
        html += '<div class="text-muted" style="font-size:11px">';
        html += 'Total: ৳' + parseFloat(cat.total).toFixed(2);
        html += ' &nbsp;·&nbsp; Your share: <strong class="text-danger">৳' + parseFloat(cat.per_head).toFixed(2) + '</strong>';
        html += '</div>';
        if (isExcluded) {
            html += '<span class="badge bg-danger mt-1">Removed from this member</span>';
        }
        html += '</div>';
        html += '<button class="btn btn-sm ' + (isExcluded ? 'btn-outline-success' : 'btn-outline-danger') + ' flex-shrink-0"';
        html += ' onclick="toggleCategory(' + userId + ',' + cat.id + ')"';
        html += ' id="cat-btn-' + userId + '-' + cat.id + '"';
        html += ' title="' + (isExcluded ? 'Add back: member pays ৳' + parseFloat(cat.per_head).toFixed(2) : 'Remove: member skips ৳' + parseFloat(cat.per_head).toFixed(2)) + '">';
        html += isExcluded ? '<i class="ti ti-plus me-1"></i>Add Back' : '<i class="ti ti-minus me-1"></i>Remove';
        html += '</button>';
        html += '</div>';
    });
    html += '</div>';
    html += '<p class="text-muted small mt-3 mb-0"><i class="ti ti-info-circle me-1"></i>Removing a category exempts this member from that expense. The amount is then redistributed among the remaining members.</p>';
    body.innerHTML = html;
}

function toggleCategory(userId, categoryId) {
    var btn = document.getElementById('cat-btn-' + userId + '-' + categoryId);
    if (btn) btn.disabled = true;

    fetch(reportBase + '/toggle-category', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ user_id: userId, category_id: categoryId, month: month, year: year })
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success) {
            // Update local exclusion state
            if (!memberCatExclusions[userId]) memberCatExclusions[userId] = [];
            if (data.excluded) {
                if (memberCatExclusions[userId].indexOf(categoryId) === -1) {
                    memberCatExclusions[userId].push(categoryId);
                }
            } else {
                memberCatExclusions[userId] = memberCatExclusions[userId].filter(function(id) { return id !== categoryId; });
            }
            // Re-render modal and reload page to refresh totals
            location.reload();
        } else {
            if (btn) btn.disabled = false;
        }
    })
    .catch(function () {
        if (btn) btn.disabled = false;
        alert('Request failed. Please try again.');
    });
}
</script>
@endsection
