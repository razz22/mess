<?php $page = "mess-dashboard" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="add-item d-flex align-items-center gap-3">
                @if($mess->avatar)
                <div class="avatar avatar-md">
                    <img src="{{ asset('storage/'.$mess->avatar) }}" class="img-fluid rounded-circle" alt="">
                </div>
                @endif
                <div class="page-title">
                    <h4 class="fw-bold">{{ $mess->name }}</h4>
                    <h6>
                        <span class="badge bg-{{ $member->role === 'owner' ? 'danger' : ($member->role === 'manager' ? 'warning' : ($member->role === 'author' ? 'info' : 'secondary')) }}">
                            {{ ucfirst($member->role) }}
                        </span>
                        @if($currentManager)
                        &nbsp;<span class="text-muted small"><i class="ti ti-crown me-1"></i>Manager: <strong>{{ $currentManager->user->name }}</strong></span>
                        @endif
                    </h6>
                </div>
            </div>
            <div class="page-btn d-flex gap-2">
                <small class="text-muted align-self-center">
                    <i class="ti ti-key me-1"></i>Code: <code>{{ $mess->invite_code }}</code>
                </small>
                @if($member->isOwner())
                <a href="{{ route('mess.settings', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-settings me-1"></i>Settings
                </a>
                @endif
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg bg-primary-transparent rounded">
                            <i class="ti ti-users fs-4 text-primary"></i>
                        </div>
                        <div>
                            <div class="fs-24 fw-bold text-primary">{{ $totalMembers }}</div>
                            <div class="text-muted small">Members</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg bg-success-transparent rounded">
                            <i class="ti ti-cash fs-4 text-success"></i>
                        </div>
                        <div>
                            <div class="fs-24 fw-bold text-success">৳{{ number_format($monthlyDeposits, 0) }}</div>
                            <div class="text-muted small">This Month Deposits</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg bg-warning-transparent rounded">
                            <i class="ti ti-coins fs-4 text-warning"></i>
                        </div>
                        <div>
                            <div class="fs-24 fw-bold text-warning">৳{{ number_format($monthlyExpenses, 0) }}</div>
                            <div class="text-muted small">This Month Expenses</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg bg-{{ ($monthlyDeposits - $monthlyExpenses) >= 0 ? 'info' : 'danger' }}-transparent rounded">
                            <i class="ti ti-wallet fs-4 text-{{ ($monthlyDeposits - $monthlyExpenses) >= 0 ? 'info' : 'danger' }}"></i>
                        </div>
                        <div>
                            <div class="fs-24 fw-bold text-{{ ($monthlyDeposits - $monthlyExpenses) >= 0 ? 'info' : 'danger' }}">
                                ৳{{ number_format($monthlyDeposits - $monthlyExpenses, 0) }}
                            </div>
                            <div class="text-muted small">Cash in Hand</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Today's Meals -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0"><i class="ti ti-tools-kitchen-2 me-2 text-primary"></i>Today's Meals</h6>
                        <a href="{{ route('mess.meals', $mess->id) }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @forelse($todayMeals as $meal)
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-{{ $meal->type === 'breakfast' ? 'warning' : ($meal->type === 'lunch' ? 'success' : 'primary') }}-subtle text-{{ $meal->type === 'breakfast' ? 'warning' : ($meal->type === 'lunch' ? 'success' : 'primary') }}">
                                    {{ ucfirst($meal->type) }}
                                </span>
                                <span class="text-muted small">{{ $meal->presentCount() }} attending</span>
                            </div>
                            <span class="badge bg-{{ $meal->status === 'open' ? 'success' : 'secondary' }}">{{ ucfirst($meal->status) }}</span>
                        </div>
                        @empty
                        <p class="text-muted text-center py-3">No meals scheduled today.</p>
                        @endforelse
                        <div class="mt-3">
                            <a href="{{ route('mess.meals', $mess->id) }}" class="btn btn-primary btn-sm w-100">
                                <i class="ti ti-checkbox me-1"></i>Mark Attendance
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Market -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0"><i class="ti ti-shopping-cart me-2 text-success"></i>Today's Market</h6>
                        <a href="{{ route('mess.market', $mess->id) }}" class="btn btn-sm btn-outline-success">View Calendar</a>
                    </div>
                    <div class="card-body">
                        @if($todayRoutine)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="avatar avatar-md">
                                <span class="avatar-title rounded-circle bg-success text-white">
                                    {{ strtoupper(substr($todayRoutine->assignedTo->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $todayRoutine->assignedTo->name }}</div>
                                <div class="small text-muted">Assigned to market</div>
                            </div>
                            <span class="badge ms-auto bg-{{ $todayRoutine->status === 'completed' ? 'success' : ($todayRoutine->status === 'pending' ? 'warning' : 'info') }}">
                                {{ ucfirst($todayRoutine->status) }}
                            </span>
                        </div>
                        @if($todayRoutine->total_spent > 0)
                        <div class="text-muted small">Total Spent: <strong>৳{{ number_format($todayRoutine->total_spent, 2) }}</strong></div>
                        @endif
                        <a href="{{ route('mess.market.list', [$mess->id, $todayRoutine->id]) }}" class="btn btn-success btn-sm w-100 mt-2">
                            <i class="ti ti-list me-1"></i>View Shopping List
                        </a>
                        @else
                        <div class="text-center py-3">
                            <i class="ti ti-calendar-off fs-3 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">No market assigned for today.</p>
                        </div>
                        @if($member->canManage())
                        <button class="btn btn-outline-success btn-sm w-100 mt-2" data-bs-toggle="modal" data-bs-target="#assignMarketModal">
                            <i class="ti ti-plus me-1"></i>Assign Today
                        </button>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- My Monthly Summary -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0"><i class="ti ti-report-analytics me-2 text-info"></i>My Summary ({{ now()->format('F Y') }})</h6>
                        <a href="{{ route('mess.report.monthly', $mess->id) }}" class="btn btn-sm btn-outline-info">Full Report</a>
                    </div>
                    <div class="card-body">
                        @if($mySummary)
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <tr><td class="text-muted">Meal Days</td><td class="fw-semibold">{{ $mySummary->total_meal_days }}</td></tr>
                                <tr><td class="text-muted">Meal Cost</td><td class="fw-semibold text-warning">৳{{ number_format($mySummary->meal_cost, 2) }}</td></tr>
                                <tr><td class="text-muted">Expenses Share</td><td class="fw-semibold text-danger">৳{{ number_format($mySummary->total_expenses, 2) }}</td></tr>
                                <tr><td class="text-muted">Market Expense</td><td class="fw-semibold text-danger">৳{{ number_format($mySummary->market_expense, 2) }}</td></tr>
                                <tr><td class="text-muted">Total Deposit</td><td class="fw-semibold text-success">৳{{ number_format($mySummary->total_deposit, 2) }}</td></tr>
                                <tr class="border-top"><td class="fw-bold">Due Amount</td>
                                    <td class="fw-bold {{ $mySummary->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                        ৳{{ number_format($mySummary->due_amount, 2) }}
                                        {{ $mySummary->due_amount > 0 ? '(Owe)' : '(Clear)' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-3">
                            <i class="ti ti-chart-bar-off fs-3 text-muted"></i>
                            <p class="text-muted mt-2 mb-2">Report not generated yet.</p>
                            @if($member->canManage())
                            <form action="{{ route('mess.report.generate', $mess->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="month" value="{{ now()->month }}">
                                <input type="hidden" name="year" value="{{ now()->year }}">
                                <button type="submit" class="btn btn-info btn-sm">Generate Now</button>
                            </form>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="ti ti-apps me-2 text-secondary"></i>Quick Access</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-4">
                                <a href="{{ route('mess.meals', $mess->id) }}" class="card text-decoration-none h-100">
                                    <div class="card-body text-center py-3">
                                        <i class="ti ti-tools-kitchen-2 fs-3 text-primary mb-1"></i>
                                        <div class="small fw-semibold">Meals</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('mess.market', $mess->id) }}" class="card text-decoration-none h-100">
                                    <div class="card-body text-center py-3">
                                        <i class="ti ti-shopping-cart fs-3 text-success mb-1"></i>
                                        <div class="small fw-semibold">Market</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('mess.expenses', $mess->id) }}" class="card text-decoration-none h-100">
                                    <div class="card-body text-center py-3">
                                        <i class="ti ti-coins fs-3 text-warning mb-1"></i>
                                        <div class="small fw-semibold">Expenses</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('mess.deposits', $mess->id) }}" class="card text-decoration-none h-100">
                                    <div class="card-body text-center py-3">
                                        <i class="ti ti-cash fs-3 text-info mb-1"></i>
                                        <div class="small fw-semibold">Deposits</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('mess.members', $mess->id) }}" class="card text-decoration-none h-100">
                                    <div class="card-body text-center py-3">
                                        <i class="ti ti-users fs-3 text-secondary mb-1"></i>
                                        <div class="small fw-semibold">Members</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('mess.rewards', $mess->id) }}" class="card text-decoration-none h-100">
                                    <div class="card-body text-center py-3">
                                        <i class="ti ti-trophy fs-3 text-danger mb-1"></i>
                                        <div class="small fw-semibold">Rewards</div>
                                        @if($pendingReports > 0)
                                        <span class="badge bg-danger rounded-pill">{{ $pendingReports }}</span>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
