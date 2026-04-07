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
                @if($member->canManage())
                <form action="{{ route('mess.report.generate', $mess->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="ti ti-refresh me-1"></i>Generate Report
                    </button>
                </form>
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
                        <div class="fs-4 fw-bold text-primary">৳{{ number_format($perMealCost, 2) }}</div>
                        <div class="text-muted small">Per Meal Cost</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Member Wise Report -->
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $m)
                        @php $summary = $summaries[$m->user->id] ?? null; @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-title rounded-circle bg-primary text-white">{{ strtoupper(substr($m->user->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $m->user->name }}</div>
                                        <span class="badge bg-{{ $m->role === 'owner' ? 'danger' : ($m->role === 'manager' ? 'warning' : 'secondary') }} fs-10">{{ ucfirst($m->role) }}</span>
                                    </div>
                                </div>
                            </td>
                            @if($summary)
                            <td class="text-center fw-bold">{{ $summary->total_meal_days }}</td>
                            <td>৳{{ number_format($summary->meal_cost, 2) }}</td>
                            <td>৳{{ number_format($summary->total_expenses, 2) }}</td>
                            <td>৳{{ number_format($summary->market_expense, 2) }}</td>
                            <td class="fw-bold">৳{{ number_format($summary->total_payable, 2) }}</td>
                            <td class="text-success fw-bold">৳{{ number_format($summary->total_deposit, 2) }}</td>
                            <td class="fw-bold {{ $summary->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $summary->due_amount > 0 ? '-' : '+' }}৳{{ number_format($summary->due_amount, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-{{ $summary->status === 'settled' ? 'success' : ($summary->due_amount > 0 ? 'danger' : 'warning') }}">
                                    {{ $summary->status === 'settled' ? 'Settled' : ($summary->due_amount > 0 ? 'Due' : 'Extra') }}
                                </span>
                            </td>
                            @else
                            <td colspan="8" class="text-muted text-center">Not generated</td>
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
@endsection
