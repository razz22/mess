<?php $page = "mess-expenses" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">Expenses — {{ $mess->name }}</h4>
                <h6>{{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</h6>
            </div>
            <div class="page-btn d-flex gap-2">
                @if($member->canManage())
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                    <i class="ti ti-circle-plus me-1"></i>Add Expense
                </button>
                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="ti ti-tag me-1"></i>Category
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
                        <div class="text-muted small">Total Expenses</div>
                        <div class="fs-4 fw-bold text-danger">৳{{ number_format($totalExpense, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted small">Total Entries</div>
                        <div class="fs-4 fw-bold">{{ $expenses->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted small">Per Member Estimate</div>
                        @php $memberCount = $mess->getMemberCount(); @endphp
                        <div class="fs-4 fw-bold text-primary">৳{{ $memberCount > 0 ? number_format($totalExpense / $memberCount, 2) : '0.00' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Expense List</h6></div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Added By</th>
                                    @if($member->canManage()) <th>Actions</th> @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->expense_date->format('d M') }}</td>
                                    <td>
                                        {{ $expense->title }}
                                        @if($expense->is_market_expense)
                                        <span class="badge bg-success-subtle text-success ms-1" style="font-size:10px">Market</span>
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
                                    @if($member->canManage())
                                    <td>
                                        <form action="{{ route('mess.expenses.destroy', [$mess->id, $expense->id]) }}" method="POST" onsubmit="return confirm('Delete?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger"><i class="ti ti-trash"></i></button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-3">No expenses this month.</td></tr>
                                @endforelse
                            </tbody>
                            @if($expenses->count() > 0)
                            <tfoot>
                                <tr class="table-danger">
                                    <td colspan="3" class="fw-bold text-end">Total</td>
                                    <td class="fw-bold">৳{{ number_format($totalExpense, 2) }}</td>
                                    <td colspan="{{ $member->canManage() ? 2 : 1 }}"></td>
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
                    <div class="card-header"><h6 class="mb-0">By Category</h6></div>
                    <div class="card-body">
                        @forelse($byCategory as $catId => $amount)
                        @php $cat = $categories->where('id', $catId)->first(); @endphp
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="{{ $cat?->icon ?? 'ti-coins' }} text-{{ $cat?->color ?? 'secondary' }}"></i>
                                <span class="small">{{ $cat?->name ?? 'Uncategorized' }}</span>
                            </div>
                            <strong class="text-danger small">৳{{ number_format($amount, 2) }}</strong>
                        </div>
                        @empty
                        <p class="text-muted text-center">No data</p>
                        @endforelse
                    </div>
                </div>

                <!-- Categories List -->
                <div class="card mt-3">
                    <div class="card-header"><h6 class="mb-0">Categories</h6></div>
                    <div class="card-body p-2">
                        @foreach($categories as $cat)
                        <span class="badge bg-{{ $cat->color }}-subtle text-{{ $cat->color }} m-1 p-2">
                            <i class="{{ $cat->icon }} me-1"></i>{{ $cat->name }}
                        </span>
                        @endforeach
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
                <h5 class="modal-title">Add Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.expenses.store', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Monthly electricity bill">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" required step="0.01" min="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="expense_date" class="form-control" value="{{ now()->toDateString() }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- No Category --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Expense Category</h5>
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
                            <label class="form-label">Color</label>
                            <select name="color" class="form-select">
                                <option value="primary">Blue</option>
                                <option value="success">Green</option>
                                <option value="warning">Yellow</option>
                                <option value="danger">Red</option>
                                <option value="info">Cyan</option>
                                <option value="secondary">Grey</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
