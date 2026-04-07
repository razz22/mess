<?php $page = "mess-deposits" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">Member Deposits — {{ $mess->name }}</h4>
                <h6>{{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</h6>
            </div>
            <div class="page-btn">
                @if($member->canManage())
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDepositModal">
                    <i class="ti ti-circle-plus me-1"></i>Record Deposit
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

        <!-- Per Member Summary -->
        <div class="card mb-4">
            <div class="card-header"><h6 class="mb-0">Deposit Summary per Member</h6></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Total Deposited</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $m)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-title rounded-circle bg-primary text-white">{{ strtoupper(substr($m->user->name, 0, 1)) }}</span>
                                    </div>
                                    <span>{{ $m->user->name }}</span>
                                </div>
                            </td>
                            <td class="fw-bold {{ ($depositsByMember[$m->user->id] ?? 0) > 0 ? 'text-success' : 'text-muted' }}">
                                ৳{{ number_format($depositsByMember[$m->user->id] ?? 0, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-{{ ($depositsByMember[$m->user->id] ?? 0) > 0 ? 'success' : 'warning' }}">
                                    {{ ($depositsByMember[$m->user->id] ?? 0) > 0 ? 'Deposited' : 'Pending' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-success fw-bold">
                            <td>Total</td>
                            <td>৳{{ number_format($totalDeposit, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Deposit History -->
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Deposit History</h6></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Member</th>
                            <th>Amount</th>
                            <th>Note</th>
                            <th>Received By</th>
                            @if($member->canManage()) <th>Actions</th> @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deposits as $deposit)
                        <tr>
                            <td>{{ $deposit->deposit_date->format('d M Y') }}</td>
                            <td>{{ $deposit->user->name }}</td>
                            <td class="fw-bold text-success">৳{{ number_format($deposit->amount, 2) }}</td>
                            <td class="text-muted small">{{ $deposit->note ?: '—' }}</td>
                            <td class="text-muted small">{{ $deposit->receivedBy->name }}</td>
                            @if($member->canManage())
                            <td>
                                <form action="{{ route('mess.deposits.destroy', [$mess->id, $deposit->id]) }}" method="POST" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger"><i class="ti ti-trash"></i></button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No deposits this month.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Deposit Modal -->
<div class="modal fade" id="addDepositModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Deposit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.deposits.store', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Member <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            @foreach($members as $m)
                            <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" required step="0.01" min="0.01">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Month</label>
                            <select name="month" class="form-select">
                                @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Year</label>
                            <input type="number" name="year" class="form-control" value="{{ now()->year }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deposit Date</label>
                        <input type="date" name="deposit_date" class="form-control" value="{{ now()->toDateString() }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <input type="text" name="note" class="form-control" placeholder="Optional note">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Deposit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
