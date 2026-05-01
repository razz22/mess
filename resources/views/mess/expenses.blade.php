<?php $page = "mess-expenses" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">{{ __('Expenses') }} — {{ $mess->name }}</h4>
                <h6>{{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</h6>
            </div>
            <div class="page-btn d-flex gap-2">
                @if($member->canManage())
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                    <i class="ti ti-circle-plus me-1"></i>{{ __('Add Expense') }}
                </button>
                @endif
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

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted small">{{ __('Total Expenses') }}</div>
                        <div class="fs-4 fw-bold text-danger">৳{{ number_format($totalExpense, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted small">{{ __('Total Entries') }}</div>
                        <div class="fs-4 fw-bold">{{ $expenses->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted small">{{ __('Per Member Estimate') }}</div>
                        @php $memberCount = $mess->getMemberCount(); @endphp
                        <div class="fs-4 fw-bold text-primary">৳{{ $memberCount > 0 ? number_format($totalExpense / $memberCount, 2) : '0.00' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">{{ __('Expense List') }}</h6></div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Remarks') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Assigned To') }}</th>
                                    <th>{{ __('Added By') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->expense_date->format('d M') }}</td>
                                    <td>
                                        {{ $expense->title }}
                                        @if($expense->is_market_expense)
                                        <span class="badge bg-success-subtle text-success ms-1" style="font-size:10px">{{ __('Market') }}</span>
                                        @endif
                                        @if($expense->is_market_expense && $expense->individualPurchase)
                                        @php $ip = $expense->individualPurchase; @endphp
                                        <button class="btn btn-xs btn-outline-info ms-1 py-0 px-1"
                                            onclick="openExpenseDetail({{ json_encode([
                                                'title'   => $expense->title,
                                                'date'    => $expense->expense_date->format('d M Y'),
                                                'buyer'   => $expense->member?->name ?? '—',
                                                'addedBy' => $expense->addedBy->name,
                                                'total'   => number_format($expense->amount, 2),
                                                'items'   => $ip->items,
                                                'desc'    => $ip->description,
                                            ]) }})"
                                            title="View Items">
                                            <i class="ti ti-eye" style="font-size:12px"></i>
                                        </button>
                                        @endif
                                        @if($expense->is_recurring_entry)
                                        <span class="badge bg-info-subtle text-info ms-1" style="font-size:10px"><i class="ti ti-refresh me-1"></i>{{ __('Auto') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($expense->category)
                                        <span class="badge bg-{{ $expense->category->color }}-subtle text-{{ $expense->category->color }}">
                                            <i class="{{ $expense->category->icon }} me-1"></i>{{ $expense->category->name }}
                                        </span>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-danger">৳{{ number_format($expense->amount, 2) }}</td>
                                    <td class="small">
                                        @if($expense->member)
                                        <div class="d-flex align-items-center gap-1">
                                            @if($expense->member->avatar)
                                            <img src="{{ asset('storage/'.$expense->member->avatar) }}" class="rounded-circle" style="width:22px;height:22px;object-fit:cover;" alt="">
                                            @else
                                            <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-semibold" style="width:22px;height:22px;font-size:10px;flex-shrink:0;">{{ strtoupper(substr($expense->member->name,0,1)) }}</span>
                                            @endif
                                            <span>{{ $expense->member->name }}</span>
                                        </div>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $expense->addedBy->name }}</td>
                                    @php
                                        $isOwnerOrManager = in_array($member->role, ['owner', 'manager']) || Auth::user()->is_super_admin;
                                        $canEditExpense   = $isOwnerOrManager || $expense->added_by === Auth::id();
                                        $canDeleteExpense = $isOwnerOrManager || $expense->added_by === Auth::id();
                                    @endphp
                                    @if($canEditExpense || $canDeleteExpense)
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if($canEditExpense)
                                            @php
                                                $ipEdit = $expense->individualPurchase;
                                                $routineItems = $expense->routineItems ?? collect();
                                                // Build items array for routine expenses
                                                $routineItemsArr = $routineItems->map(fn($i) => [
                                                    'id'   => $i->id,
                                                    'name' => $i->item_name,
                                                    'cost' => (float)$i->actual_cost,
                                                ])->values()->toArray();
                                            @endphp
                                            <button class="btn btn-xs btn-outline-primary"
                                                onclick="openEditModal(
                                                    {{ $expense->id }},
                                                    '{{ addslashes($expense->title) }}',
                                                    {{ $expense->amount }},
                                                    '{{ $expense->expense_date->format('Y-m-d') }}',
                                                    {{ $expense->category_id ?? 'null' }},
                                                    {{ $ipEdit ? json_encode($ipEdit->items) : 'null' }},
                                                    {{ $ipEdit ? $ipEdit->id : 'null' }},
                                                    {{ count($routineItemsArr) ? json_encode($routineItemsArr) : 'null' }}
                                                )"
                                                title="Edit">
                                                <i class="ti ti-pencil"></i>
                                            </button>
                                            @endif
                                            @if($canDeleteExpense)
                                            <button type="button" class="btn btn-xs btn-outline-danger" title="Delete"
                                                onclick="openDeleteModal(
                                                    '{{ route('mess.expenses.destroy', [$mess->id, $expense->id]) }}',
                                                    '{{ addslashes($expense->title) }}',
                                                    'expense'
                                                )"><i class="ti ti-trash"></i></button>
                                            @endif
                                        </div>
                                    </td>
                                    @else
                                    <td></td>
                                    @endif
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-3">{{ __('No expenses this month.') }}</td></tr>
                                @endforelse
                            </tbody>
                            @if($expenses->count() > 0)
                            <tfoot>
                                <tr class="table-danger">
                                    <td colspan="4" class="fw-bold text-end">{{ __('Total') }}</td>
                                    <td class="fw-bold">৳{{ number_format($totalExpense, 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- By Category -->
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">{{ __('By Category') }}</h6></div>
                    <div class="card-body">
                        @forelse($byCategory as $catId => $amount)
                        @php $cat = $categories->where('id', $catId)->first(); @endphp
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="{{ $cat?->icon ?? 'ti-coins' }} text-{{ $cat?->color ?? 'secondary' }}"></i>
                                <span class="small">{{ $cat?->name ?? __('Uncategorized') }}</span>
                            </div>
                            <strong class="text-danger small">৳{{ number_format($amount, 2) }}</strong>
                        </div>
                        @empty
                        <p class="text-muted text-center">{{ __('No data') }}</p>
                        @endforelse
                    </div>
                </div>

                <!-- Categories List -->
                <div class="card mt-3">
                    <div class="card-header d-flex align-items-center justify-content-between py-2">
                        <h6 class="mb-0">{{ __('Categories') }}</h6>
                        @if($member->canManage())
                        <button class="btn btn-xs btn-outline-primary py-0" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="ti ti-plus" style="font-size:12px"></i> Add
                        </button>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        @forelse($categories as $cat)
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-{{ $cat->color }}-subtle text-{{ $cat->color }} p-2">
                                    <i class="{{ $cat->icon }}"></i>
                                </span>
                                <div>
                                    <span class="small fw-semibold d-block">{{ $cat->name }}</span>
                                    @if($cat->is_recurring && $cat->recurring_amount)
                                    <span class="d-inline-flex align-items-center gap-1" style="font-size:.7rem;color:#0891b2;">
                                        <i class="ti ti-refresh"></i> Recurring · ৳{{ number_format($cat->recurring_amount, 2) }}/mo
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @if($member->canManage())
                            <div class="d-flex gap-1">
                                <button class="btn btn-xs btn-outline-secondary py-0 px-1" title="Edit"
                                    onclick="openEditCategoryModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->icon }}', '{{ $cat->color }}', {{ $cat->is_recurring ? 'true' : 'false' }}, '{{ $cat->recurring_amount ?? '' }}')">
                                    <i class="ti ti-pencil" style="font-size:11px"></i>
                                </button>
                                                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1" title="Delete"
                                    onclick="openDeleteModal(
                                        '{{ route('mess.expense-categories.destroy', [$mess->id, $cat->id]) }}',
                                        '{{ addslashes($cat->name) }}',
                                        'category'
                                    )"><i class="ti ti-trash" style="font-size:11px"></i></button>
                            </div>
                            @endif
                        </div>
                        @empty
                        <p class="text-muted text-center small py-3">{{ __('No categories yet.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Expense') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.expenses.store', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Category') }}</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- No Category --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="title" value="Expense">
                    <div class="mb-3">
                        <label class="form-label">Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" required step="0.01" min="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Date') }}</label>
                        <input type="date" name="expense_date" class="form-control" value="{{ now()->toDateString() }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Add Expense') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Expense Modal -->
<div class="modal fade" id="editExpenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">

            {{-- Gradient header --}}
            <div style="background:linear-gradient(135deg,#4361ee,#7b8cde);padding:20px 24px 18px;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ti ti-pencil" style="font-size:20px;color:#fff;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-white">{{ __('Edit Expense') }}</h5>
                        <div id="editModalSubtitle" style="color:rgba(255,255,255,.75);font-size:12px;margin-top:2px;"></div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <form id="editExpenseForm" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="individual_purchase_id" id="editIpId">
                <input type="hidden" name="title" id="editTitle" value="Expense">

                <div class="modal-body p-4">

                    {{-- Date + Category row --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small text-muted text-uppercase" style="letter-spacing:.5px;">
                                <i class="ti ti-calendar me-1"></i>{{ __('Date') }}
                            </label>
                            <input type="date" name="expense_date" id="editDate" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small text-muted text-uppercase" style="letter-spacing:.5px;">
                                <i class="ti ti-tag me-1"></i>{{ __('Category') }}
                            </label>
                            <select name="category_id" id="editCategory" class="form-select">
                                <option value="">— No Category —</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Items section (market expenses) --}}
                    <div id="editItemsSection" style="display:none;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <label class="form-label fw-semibold small text-muted text-uppercase mb-0" style="letter-spacing:.5px;">
                                <i class="ti ti-list me-1"></i>{{ __('Items') }}
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" onclick="addEditItem()" style="font-size:12px;">
                                <i class="ti ti-plus"></i>Add Item
                            </button>
                        </div>

                        {{-- Column headers --}}
                        <div class="d-flex gap-2 mb-2 px-1" style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;">
                            <div style="flex:1;">Item Name</div>
                            <div style="width:110px;text-align:right;">Cost (৳)</div>
                            <div style="width:36px;"></div>
                        </div>

                        <div id="editItemsList" class="d-flex flex-column gap-2 mb-3"></div>

                        {{-- Total bar --}}
                        <div class="d-flex align-items-center justify-content-between rounded-3 px-4 py-3"
                             style="background:linear-gradient(135deg,#f0f4ff,#e8f0fe);border:1px solid #c7d2fe;">
                            <span class="fw-semibold text-primary small">
                                <i class="ti ti-calculator me-1"></i>Total
                            </span>
                            <span class="fw-bold text-primary fs-5">৳<span id="editItemsTotal">0.00</span></span>
                        </div>
                        <input type="hidden" name="amount" id="editAmountHidden">
                    </div>

                    {{-- Plain amount (non-market) --}}
                    <div id="editAmountSection">
                        <label class="form-label fw-semibold small text-muted text-uppercase" style="letter-spacing:.5px;">
                            <i class="ti ti-coins me-1"></i>Amount (৳) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">৳</span>
                            <input type="number" name="amount" id="editAmount" class="form-control form-control-lg" step="0.01" min="0.01" style="font-size:1.1rem;font-weight:600;">
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>{{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary px-5 fw-semibold">
                        <i class="ti ti-device-floppy me-1"></i>{{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var _editMode = null;

function openEditModal(id, title, amount, date, categoryId, ipItems, ipId, routineItems) {
    var baseUrl = '{{ route('mess.expenses.update', [$mess->id, '__ID__']) }}';
    document.getElementById('editExpenseForm').action = baseUrl.replace('__ID__', id);
    document.getElementById('editTitle').value = title;
    document.getElementById('editDate').value  = date;
    document.getElementById('editIpId').value  = ipId || '';
    document.getElementById('editCategory').value = categoryId !== null ? categoryId : '';

    var hasIpItems      = ipItems && ipItems.length > 0;
    var hasRoutineItems = routineItems && routineItems.length > 0;

    if (hasIpItems || hasRoutineItems) {
        _editMode = hasRoutineItems ? 'routine' : 'ip';
        document.getElementById('editItemsSection').style.display = '';
        document.getElementById('editAmountSection').style.display = 'none';
        document.getElementById('editAmount').removeAttribute('required');
        document.getElementById('editModalSubtitle').innerHTML =
            '<i class="ti ti-shopping-cart me-1"></i>Market Expense — edit items below';
        renderEditItems(hasRoutineItems ? routineItems : ipItems);
    } else {
        _editMode = null;
        document.getElementById('editItemsSection').style.display = 'none';
        document.getElementById('editAmountSection').style.display = '';
        document.getElementById('editAmount').setAttribute('required', 'required');
        document.getElementById('editAmount').value = amount;
        document.getElementById('editModalSubtitle').innerHTML =
            '<i class="ti ti-coins me-1"></i>General Expense';
    }

    new bootstrap.Modal(document.getElementById('editExpenseModal')).show();
}

function renderEditItems(items) {
    var container = document.getElementById('editItemsList');
    container.innerHTML = '';
    items.forEach(function(item) {
        container.appendChild(buildItemRow(
            item.name || item.item_name || '',
            item.cost ?? item.actual_cost ?? 0,
            item.id || null
        ));
    });
    recalcEditTotal();
}

function buildItemRow(name, cost, itemId) {
    var row = document.createElement('div');
    row.className = 'edit-item-row d-flex gap-2 align-items-center p-2 rounded-3 border bg-white';
    row.style.cssText = 'background:#f8faff!important;border-color:#e0e7ff!important;';
    row.dataset.itemId = itemId || '';
    row.innerHTML =
        '<div style="flex:1;">' +
            '<input type="text" class="form-control form-control-sm edit-item-name" placeholder="Item name" value="' + escHtml(name) + '" required>' +
        '</div>' +
        '<div style="width:110px;" class="input-group input-group-sm">' +
            '<span class="input-group-text px-2" style="font-size:12px;">৳</span>' +
            '<input type="number" class="form-control edit-item-cost" placeholder="0.00" value="' + parseFloat(cost||0).toFixed(2) + '" step="0.01" min="0" required oninput="recalcEditTotal()">' +
        '</div>' +
        '<button type="button" class="btn btn-sm btn-outline-danger px-2" onclick="removeEditItem(this)" title="Remove" style="flex-shrink:0;">' +
            '<i class="ti ti-trash" style="font-size:13px;"></i>' +
        '</button>';
    return row;
}

function addEditItem() {
    document.getElementById('editItemsList').appendChild(buildItemRow('', 0, null));
    recalcEditTotal();
}

function removeEditItem(btn) {
    btn.closest('.edit-item-row').remove();
    recalcEditTotal();
}

function recalcEditTotal() {
    var total = 0;
    document.querySelectorAll('.edit-item-cost').forEach(function(inp) {
        total += parseFloat(inp.value) || 0;
    });
    document.getElementById('editItemsTotal').textContent = total.toFixed(2);
    document.getElementById('editAmountHidden').value = total.toFixed(2);
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.getElementById('editExpenseForm').addEventListener('submit', function(e) {
    if (document.getElementById('editItemsSection').style.display === 'none') return;

    var items = [];
    document.querySelectorAll('.edit-item-row').forEach(function(row) {
        var name = row.querySelector('.edit-item-name').value.trim();
        var cost = parseFloat(row.querySelector('.edit-item-cost').value) || 0;
        if (name) items.push({ id: row.dataset.itemId || null, name: name, cost: cost });
    });

    if (items.length === 0) { e.preventDefault(); alert('Please add at least one item.'); return; }

    var inp = document.getElementById('editItemsJson') || document.createElement('input');
    inp.type = 'hidden'; inp.id = 'editItemsJson'; inp.name = 'items_json';
    this.appendChild(inp);
    inp.value = JSON.stringify(items);

    var modeInp = document.getElementById('editItemsMode') || document.createElement('input');
    modeInp.type = 'hidden'; modeInp.id = 'editItemsMode'; modeInp.name = 'items_mode';
    this.appendChild(modeInp);
    modeInp.value = _editMode;
});
</script>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Expense Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.expense-categories.store', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Water Bill">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Icon (Tabler)</label>
                            <input type="text" name="icon" class="form-control" placeholder="ti-droplet">
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('Color') }}</label>
                            <select name="color" class="form-select">
                                <option value="primary">{{ __('Blue') }}</option>
                                <option value="success">{{ __('Green') }}</option>
                                <option value="warning">{{ __('Yellow') }}</option>
                                <option value="danger">Red</option>
                                <option value="info">{{ __('Cyan') }}</option>
                                <option value="secondary">{{ __('Grey') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_recurring" id="addIsRecurring" value="1"
                                onchange="document.getElementById('addRecurringAmountRow').style.display = this.checked ? '' : 'none'">
                            <label class="form-check-label fw-semibold" for="addIsRecurring">
                                <i class="ti ti-refresh me-1 text-info"></i>Recurring monthly
                            </label>
                        </div>
                        <small class="text-muted ms-4">If checked, this amount will be auto-added at the start of each month.</small>
                    </div>
                    <div id="addRecurringAmountRow" class="mt-2 ms-4" style="display:none;">
                        <label class="form-label">Monthly Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="recurring_amount" class="form-control" step="0.01" min="0.01" placeholder="e.g. 1500">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Add Category') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="ecName" class="form-control" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Icon (Tabler)</label>
                            <input type="text" name="icon" id="ecIcon" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ __('Color') }}</label>
                            <select name="color" id="ecColor" class="form-select">
                                <option value="primary">{{ __('Blue') }}</option>
                                <option value="success">{{ __('Green') }}</option>
                                <option value="warning">{{ __('Yellow') }}</option>
                                <option value="danger">Red</option>
                                <option value="info">{{ __('Cyan') }}</option>
                                <option value="secondary">{{ __('Grey') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_recurring" id="ecIsRecurring" value="1"
                                onchange="document.getElementById('ecRecurringAmountRow').style.display = this.checked ? '' : 'none'">
                            <label class="form-check-label fw-semibold" for="ecIsRecurring">
                                <i class="ti ti-refresh me-1 text-info"></i>Recurring monthly
                            </label>
                        </div>
                        <small class="text-muted ms-4">If checked, this amount will be auto-added at the start of each month.</small>
                    </div>
                    <div id="ecRecurringAmountRow" class="mt-2 ms-4" style="display:none;">
                        <label class="form-label">Monthly Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="recurring_amount" id="ecRecurringAmount" class="form-control" step="0.01" min="0.01">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <span class="rounded-circle d-flex align-items-center justify-content-center bg-danger text-white flex-shrink-0" style="width:32px;height:32px">
                        <i class="ti ti-trash fs-6"></i>
                    </span>
                    <span id="deleteModalTitle">Delete</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-2 text-muted small" id="deleteModalMessage"></p>
                <div class="rounded p-3 bg-light border">
                    <span class="fw-semibold small" id="deleteModalItemName"></span>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>{{ __('Cancel') }}
                </button>
                <form id="deleteConfirmForm" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="ti ti-trash me-1"></i>{{ __('Yes, Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openDeleteModal(action, name, type) {
    document.getElementById('deleteConfirmForm').action = action;
    document.getElementById('deleteModalItemName').textContent = name;
    if (type === 'category') {
        document.getElementById('deleteModalTitle').textContent = '{{ __("Delete Category") }}';
        document.getElementById('deleteModalMessage').textContent = '{{ __("Existing expenses will become uncategorized. This action cannot be undone.") }}';
    } else {
        document.getElementById('deleteModalTitle').textContent = '{{ __("Delete Expense") }}';
        document.getElementById('deleteModalMessage').textContent = '{{ __("This will permanently delete the expense record.") }}';
    }
    new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
}
</script>

<script>
function openEditCategoryModal(id, name, icon, color, isRecurring, recurringAmount) {
    var base = '{{ route("mess.expense-categories.update", [$mess->id, "__ID__"]) }}';
    document.getElementById('editCategoryForm').action = base.replace('__ID__', id);
    document.getElementById('ecName').value  = name;
    document.getElementById('ecIcon').value  = icon;
    document.getElementById('ecColor').value = color;

    var chk = document.getElementById('ecIsRecurring');
    chk.checked = isRecurring;
    var row = document.getElementById('ecRecurringAmountRow');
    row.style.display = isRecurring ? '' : 'none';
    document.getElementById('ecRecurringAmount').value = recurringAmount || '';

    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}

function openExpenseDetail(data) {
    document.getElementById('edTitle').textContent   = data.title;
    document.getElementById('edDate').textContent    = data.date;
    document.getElementById('edBuyer').textContent   = data.buyer;
    document.getElementById('edAddedBy').textContent = data.addedBy;
    document.getElementById('edTotal').textContent   = '৳' + data.total;
    document.getElementById('edDesc').textContent    = data.desc || '';
    document.getElementById('edDescRow').style.display = data.desc ? '' : 'none';

    var tbody = document.getElementById('edItemsBody');
    tbody.innerHTML = '';
    var grandTotal = 0;
    (data.items || []).forEach(function(it) {
        grandTotal += parseFloat(it.actual_cost) || 0;
        var qty = it.quantity ? (it.quantity + (it.unit ? ' ' + it.unit : '')) : '—';
        tbody.insertAdjacentHTML('beforeend',
            '<tr>' +
            '<td class="py-2 ps-3">' + it.item_name + '</td>' +
            '<td class="py-2 text-center text-muted small">' + qty + '</td>' +
            '<td class="py-2 pe-3 text-end fw-semibold">৳' + parseFloat(it.actual_cost).toFixed(2) + '</td>' +
            '</tr>'
        );
    });
    document.getElementById('edGrandTotal').textContent = '৳' + grandTotal.toFixed(2);
    new bootstrap.Modal(document.getElementById('expenseDetailModal')).show();
}
</script>

{{-- Individual Market Expense Detail Modal --}}
<div class="modal fade" id="expenseDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 px-4 pt-4 pb-2" style="background:linear-gradient(135deg,#198754 0%,#20c997 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                         style="width:40px;height:40px;background:rgba(255,255,255,.2);">
                        <i class="ti ti-receipt text-white fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="edTitle"></h6>
                        <div class="small mt-1" style="color:rgba(255,255,255,.8);" id="edDate"></div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                {{-- Meta info --}}
                <div class="rounded-3 bg-light border p-3 mb-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="text-muted small">Buyer</div>
                            <div class="fw-semibold" id="edBuyer"></div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Added By</div>
                            <div class="fw-semibold" id="edAddedBy"></div>
                        </div>
                        <div class="col-12" id="edDescRow">
                            <div class="text-muted small">Description</div>
                            <div class="fw-semibold" id="edDesc"></div>
                        </div>
                    </div>
                </div>
                {{-- Items table --}}
                <table class="table table-sm mb-0" style="font-size:.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Item</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end pe-3">Cost</th>
                        </tr>
                    </thead>
                    <tbody id="edItemsBody"></tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="ps-3 fw-bold">Total</td>
                            <td class="text-end pe-3 fw-bold text-success" id="edGrandTotal"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-between align-items-center px-4 pb-3">
                <span class="fw-bold text-success fs-6"><i class="ti ti-coin me-1"></i><span id="edTotal"></span></span>
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
