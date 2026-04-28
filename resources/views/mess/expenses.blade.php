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
                                            <button class="btn btn-xs btn-outline-primary"
                                                onclick="openEditModal({{ $expense->id }}, '{{ addslashes($expense->title) }}', {{ $expense->amount }}, '{{ $expense->expense_date->format('Y-m-d') }}', {{ $expense->category_id ?? 'null' }})"
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
                                <tr><td colspan="6" class="text-center text-muted py-3">{{ __('No expenses this month.') }}</td></tr>
                                @endforelse
                            </tbody>
                            @if($expenses->count() > 0)
                            <tfoot>
                                <tr class="table-danger">
                                    <td colspan="3" class="fw-bold text-end">{{ __('Total') }}</td>
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
                    <div class="mb-3">
                        <label class="form-label">Remarks <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Monthly electricity bill">
                    </div>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Expense') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editExpenseForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Remarks <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="editTitle" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="editAmount" class="form-control" required step="0.01" min="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Date') }}</label>
                        <input type="date" name="expense_date" id="editDate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Category') }}</label>
                        <select name="category_id" id="editCategory" class="form-select">
                            <option value="">-- No Category --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
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

<script>
function openEditModal(id, title, amount, date, categoryId) {
    var baseUrl = '{{ route('mess.expenses.update', [$mess->id, '__ID__']) }}';
    document.getElementById('editExpenseForm').action = baseUrl.replace('__ID__', id);
    document.getElementById('editTitle').value   = title;
    document.getElementById('editAmount').value  = amount;
    document.getElementById('editDate').value    = date;
    var sel = document.getElementById('editCategory');
    sel.value = categoryId !== null ? categoryId : '';
    var modal = new bootstrap.Modal(document.getElementById('editExpenseModal'));
    modal.show();
}
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
</script>
@endsection
