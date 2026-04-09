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
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold text-danger">৳{{ number_format($totalExpenses, 2) }}</div>
                        <div class="text-muted small">Total Expenses</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold text-success">৳{{ number_format($totalDeposits, 2) }}</div>
                        <div class="text-muted small">Total Deposits</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold text-{{ $cashInHand >= 0 ? 'info' : 'danger' }}">৳{{ number_format($cashInHand, 2) }}</div>
                        <div class="text-muted small">Cash in Hand</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center">
                    <div class="card-body py-3">
                        @if($costMode === 'daily')
                        <div class="fs-5 fw-bold text-primary">Daily Rate</div>
                        <div class="text-muted small">Per Meal Cost (varies by day)</div>
                        @else
                        <div class="fs-4 fw-bold text-primary">৳{{ number_format($perMealCost, 2) }}</div>
                        <div class="text-muted small">Per Meal Cost (monthly avg)</div>
                        @endif
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
                            <th>Meal Cost</th>
                            <th>Shared Exp.</th>
                            <th>Market Exp.</th>
                            <th>Total Payable</th>
                            <th>Deposited</th>
                            <th>Due / Extra</th>
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
                        <tr id="row-{{ $m->user->id }}" class="{{ $excluded ? 'opacity-75' : '' }}">
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
                            <td class="text-center fw-bold">{{ $summary->total_meal_days }}</td>
                            <td>৳{{ number_format($summary->meal_cost, 2) }}</td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if($excluded)
                                    <span class="text-muted small fst-italic">Excluded</span>
                                    @else
                                    <span>৳{{ number_format($summary->total_expenses, 2) }}</span>
                                    @if($catExclCount > 0)
                                    <span class="badge bg-warning text-dark" title="{{ $catExclCount }} category(s) excluded">-{{ $catExclCount }} cat.</span>
                                    @endif
                                    @endif
                                    @if($isManager)
                                    <button class="btn btn-xs {{ $excluded ? 'btn-outline-success' : 'btn-outline-warning' }} py-0 px-1"
                                        id="toggle-btn-{{ $m->user->id }}"
                                        onclick="toggleShared({{ $m->user->id }}, {{ $excluded ? 'true' : 'false' }})"
                                        title="{{ $excluded ? 'Include in shared expenses' : 'Exclude from all shared expenses' }}">
                                        <i class="ti {{ $excluded ? 'ti-user-check' : 'ti-user-minus' }}" style="font-size:11px"></i>
                                    </button>
                                    @if(!$excluded)
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-1"
                                        onclick="openCategoryModal({{ $m->user->id }}, '{{ addslashes($m->user->name) }}')"
                                        title="Manage category exclusions">
                                        <i class="ti ti-adjustments-horizontal" style="font-size:11px"></i>
                                    </button>
                                    @endif
                                    @endif
                                </div>
                            </td>
                            <td>৳{{ number_format($summary->market_expense, 2) }}</td>
                            <td class="fw-bold">৳{{ number_format($summary->total_payable, 2) }}</td>
                            <td class="text-success fw-bold">৳{{ number_format($summary->total_deposit, 2) }}</td>
                            <td class="fw-bold {{ $summary->due_amount > 0 ? 'text-danger' : 'text-success' }}">
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
                            <td colspan="8" class="text-muted text-center">—</td>
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
                <h5 class="modal-title">Manage Expense Categories — <span id="catModalMemberName"></span></h5>
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
var messId = {{ $mess->id }};
var csrf   = document.querySelector('meta[name="csrf-token"]').content;
var month  = {{ $month }};
var year   = {{ $year }};

// Category data passed from PHP: {categoryId: {name, amount}}
var expenseCategoriesRaw = @json($expensesByCategory);
var expenseCategories = Object.keys(expenseCategoriesRaw).map(function(id) {
    return { id: parseInt(id), name: expenseCategoriesRaw[id].name, amount: expenseCategoriesRaw[id].amount };
});
var memberCatExclusions = @json($memberCategoryExclusions);

function toggleShared(userId, currentlyExcluded) {
    var btn = document.getElementById('toggle-btn-' + userId);
    if (btn) btn.disabled = true;

    fetch('/mess/' + messId + '/report/toggle-shared', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
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
    fetch('/mess/' + messId + '/report/pay-extra', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
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
    fetch('/mess/' + messId + '/report/carry-extra', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
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
        body.innerHTML = '<p class="text-muted text-center">No expense categories this month.</p>';
        return;
    }

    var html = '<div class="list-group list-group-flush">';
    expenseCategories.forEach(function(cat) {
        var isExcluded = excluded.indexOf(cat.id) !== -1;
        html += '<div class="list-group-item d-flex justify-content-between align-items-center px-0">';
        html += '<div>';
        html += '<span class="fw-semibold">' + cat.name + '</span>';
        html += '<span class="text-muted small ms-2">৳' + parseFloat(cat.amount).toFixed(2) + '</span>';
        if (isExcluded) {
            html += ' <span class="badge bg-danger ms-1">Removed</span>';
        }
        html += '</div>';
        html += '<button class="btn btn-sm ' + (isExcluded ? 'btn-outline-success' : 'btn-outline-danger') + '"';
        html += ' onclick="toggleCategory(' + userId + ',' + cat.id + ')"';
        html += ' id="cat-btn-' + userId + '-' + cat.id + '"';
        html += ' title="' + (isExcluded ? 'Add back this category' : 'Remove this category for this member') + '">';
        html += isExcluded ? '<i class="ti ti-plus me-1"></i>Add Back' : '<i class="ti ti-minus me-1"></i>Remove';
        html += '</button>';
        html += '</div>';
    });
    html += '</div>';
    html += '<p class="text-muted small mt-2 mb-0">Removing a category means this member will not share that expense. Other members will split it instead.</p>';
    body.innerHTML = html;
}

function toggleCategory(userId, categoryId) {
    var btn = document.getElementById('cat-btn-' + userId + '-' + categoryId);
    if (btn) btn.disabled = true;

    fetch('/mess/' + messId + '/report/toggle-category', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
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
