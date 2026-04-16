<?php $page = "mess-rent" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        {{-- Page Header --}}
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-home-dollar me-2 text-primary"></i>House Rent Management</h4>
                <h6 class="text-muted">{{ $mess->name }} &mdash; {{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</h6>
            </div>
            <div class="page-btn d-flex gap-2 flex-wrap">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addInvoiceModal">
                    <i class="ti ti-file-invoice me-1"></i>Create Invoice
                </button>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="ti ti-circle-plus me-1"></i>Record Payment
                </button>
                <button class="btn btn-warning btn-sm text-dark" data-bs-toggle="modal" data-bs-target="#addAdvanceModal">
                    <i class="ti ti-coin me-1"></i>Record Advance
                </button>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2">
            <i class="ti ti-circle-check me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2">
            <i class="ti ti-alert-circle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Month / Year Filter --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="small text-muted fw-semibold">Filter:</span>
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
                    <span class="text-muted small ms-1">Showing rent data for {{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</span>
                </form>
            </div>
        </div>

        {{-- Summary Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary fs-3">
                            <i class="ti ti-building"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Expected This Month</div>
                            <div class="fw-bold fs-14">৳{{ number_format($totalExpected, 0) }}</div>
                            <div class="small text-muted">{{ $members->count() }} rooms</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success fs-3">
                            <i class="ti ti-cash"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Collected</div>
                            <div class="fw-bold fs-14 text-success">৳{{ number_format($totalCollected, 0) }}</div>
                            @if($totalExpected > 0)
                            <div class="small text-muted">{{ number_format($totalExpected > 0 ? ($totalCollected / $totalExpected * 100) : 0, 0) }}% of expected</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 bg-danger bg-opacity-10 text-danger fs-3">
                            <i class="ti ti-alert-triangle"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Outstanding</div>
                            <div class="fw-bold fs-14 {{ $outstanding > 0 ? 'text-danger' : 'text-success' }}">
                                ৳{{ number_format($outstanding, 0) }}
                            </div>
                            @php
                                $pendingCount = $members->filter(fn($m) => ($paidByMember[$m->id] ?? 0) < $m->house_rent && $m->house_rent > 0)->count();
                            @endphp
                            <div class="small text-muted">{{ $pendingCount }} member(s) pending</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 bg-warning bg-opacity-10 text-warning fs-3">
                            <i class="ti ti-safe"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Advance Held</div>
                            <div class="fw-bold fs-14 text-warning">৳{{ number_format($totalAdvanceHeld, 0) }}</div>
                            <div class="small text-muted">Security deposits</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm border-info border-opacity-25">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 bg-info bg-opacity-10 text-info fs-3">
                            <i class="ti ti-piggy-bank"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Rent Fund Balance</div>
                            <div class="fw-bold fs-14 {{ $fundBalance >= 0 ? 'text-info' : 'text-danger' }}">
                                ৳{{ number_format($fundBalance, 2) }}
                            </div>
                            <div class="small text-muted">Available to expense</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- All Paid Banner --}}
        @if($allPaid)
        <div class="alert alert-success d-flex align-items-center gap-3 mb-3 py-2">
            <i class="ti ti-circle-check fs-3"></i>
            <div>
                <strong>All members have paid!</strong>
                All rent for {{ date('F Y', mktime(0,0,0,$month,1,$year)) }} is collected.
                <span class="ms-2 text-success fw-bold">৳{{ number_format($totalCollected, 0) }} collected.</span>
            </div>
        </div>
        @endif

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-0" id="rentTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" id="tab-status" data-bs-toggle="tab" data-bs-target="#pane-status" type="button">
                    <i class="ti ti-list-check me-1"></i>Monthly Status
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="tab-invoices" data-bs-toggle="tab" data-bs-target="#pane-invoices" type="button">
                    <i class="ti ti-file-invoice me-1"></i>Invoices
                    @if($invoices->count())<span class="badge bg-primary ms-1">{{ $invoices->count() }}</span>@endif
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="tab-fund" data-bs-toggle="tab" data-bs-target="#pane-fund" type="button">
                    <i class="ti ti-piggy-bank me-1"></i>Rent Fund
                    @if($fundBalance > 0)<span class="badge bg-info ms-1">৳{{ number_format($fundBalance,0) }}</span>@endif
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="tab-history" data-bs-toggle="tab" data-bs-target="#pane-history" type="button">
                    <i class="ti ti-clock-history me-1"></i>Payment History
                    @if($allPayments->count())<span class="badge bg-secondary ms-1">{{ $allPayments->count() }}</span>@endif
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="tab-advance" data-bs-toggle="tab" data-bs-target="#pane-advance" type="button">
                    <i class="ti ti-coin me-1"></i>Advance Ledger
                    @if($advancePayments->count())<span class="badge bg-secondary ms-1">{{ $advancePayments->count() }}</span>@endif
                </button>
            </li>
        </ul>

        <div class="tab-content">

            {{-- ===================================================== --}}
            {{-- TAB 1: Monthly Status                                 --}}
            {{-- ===================================================== --}}
            <div class="tab-pane fade show active" id="pane-status">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Rent Status — {{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:50px">Room</th>
                                    <th>Member</th>
                                    <th>Monthly Rent</th>
                                    <th>Paid This Month</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Advance Held</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($members as $m)
                                @php
                                    $paid    = $paidByMember[$m->id] ?? 0;
                                    $balance = $m->house_rent - $paid;
                                    $advance = $advanceBalanceByMember[$m->id] ?? 0;

                                    if ($m->house_rent == 0) {
                                        $statusLabel = 'Not Set';
                                        $statusBadge = 'bg-secondary';
                                    } elseif ($balance <= 0) {
                                        $statusLabel = $balance < 0 ? 'Overpaid' : 'Paid';
                                        $statusBadge = 'bg-success';
                                    } elseif ($paid > 0) {
                                        $statusLabel = 'Partial';
                                        $statusBadge = 'bg-warning text-dark';
                                    } else {
                                        $statusLabel = 'Pending';
                                        $statusBadge = 'bg-danger';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        @if($m->room_no)
                                        <span class="badge bg-light text-dark border fw-semibold">{{ $m->room_no }}</span>
                                        @else
                                        <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($m->user->avatar)
                                            <img src="{{ asset('storage/'.$m->user->avatar) }}" class="rounded-circle" style="width:34px;height:34px;object-fit:cover;flex-shrink:0;" alt="">
                                            @else
                                            <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold" style="width:34px;height:34px;font-size:13px;flex-shrink:0;">{{ strtoupper(substr($m->user->name, 0, 1)) }}</span>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $m->user->name }}</div>
                                                <div class="small text-muted">{{ ucfirst($m->role) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-semibold">
                                        @if($m->house_rent > 0)
                                        ৳{{ number_format($m->house_rent, 0) }}
                                        @else
                                        <span class="text-muted small">Not set</span>
                                        @endif
                                    </td>
                                    <td class="{{ $paid > 0 ? 'text-success fw-semibold' : 'text-muted' }}">
                                        ৳{{ number_format($paid, 0) }}
                                    </td>
                                    <td class="{{ $balance > 0 ? 'text-danger fw-semibold' : ($balance < 0 ? 'text-success fw-semibold' : 'text-muted') }}">
                                        @if($m->house_rent > 0)
                                            {{ $balance < 0 ? '+' : '' }}৳{{ number_format(abs($balance), 0) }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td><span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span></td>
                                    <td class="{{ $advance > 0 ? 'text-warning fw-semibold' : 'text-muted' }}">
                                        ৳{{ number_format($advance, 0) }}
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button class="btn btn-xs btn-outline-primary"
                                                title="Set Rent / Room"
                                                onclick="openSetRent({{ $m->id }}, '{{ addslashes($m->user->name) }}', '{{ $m->house_rent }}', '{{ addslashes($m->room_no ?? '') }}', '{{ addslashes($m->notes ?? '') }}')">
                                                <i class="ti ti-settings"></i>
                                            </button>
                                            <button class="btn btn-xs btn-success"
                                                title="Record Payment"
                                                onclick="openAddPayment({{ $m->id }}, '{{ addslashes($m->user->name) }}')">
                                                <i class="ti ti-plus"></i>
                                            </button>
                                            <button class="btn btn-xs btn-warning text-dark"
                                                title="Record Advance"
                                                onclick="openAddAdvance({{ $m->id }}, '{{ addslashes($m->user->name) }}')">
                                                <i class="ti ti-coin"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="ti ti-users-off fs-2 d-block mb-2"></i>No active members found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($members->count())
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="2">Total</td>
                                    <td>৳{{ number_format($totalExpected, 0) }}</td>
                                    <td class="text-success">৳{{ number_format($totalCollected, 0) }}</td>
                                    <td class="{{ $outstanding > 0 ? 'text-danger' : 'text-success' }}">৳{{ number_format($outstanding, 0) }}</td>
                                    <td></td>
                                    <td class="text-warning">৳{{ number_format($totalAdvanceHeld, 0) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- This Month's Payments --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Payments recorded in {{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Member</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Method</th>
                                    <th>Notes</th>
                                    <th>Recorded By</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthPayments as $p)
                                <tr>
                                    <td class="small">{{ $p->payment_date->format('d M Y') }}</td>
                                    <td>{{ $p->member->user->name }}</td>
                                    <td class="fw-semibold {{ $p->payment_type === 'discount' ? 'text-danger' : 'text-success' }}">
                                        {{ $p->payment_type === 'discount' ? '-' : '+' }}৳{{ number_format($p->amount, 2) }}
                                    </td>
                                    <td>
                                        @php
                                            $typeBadge = match($p->payment_type) {
                                                'rent'       => 'bg-success',
                                                'penalty'    => 'bg-danger',
                                                'discount'   => 'bg-warning text-dark',
                                                'adjustment' => 'bg-info text-dark',
                                                default      => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $typeBadge }}">{{ $p->payment_type_label }}</span>
                                    </td>
                                    <td class="small text-muted">{{ $p->payment_method_label }}</td>
                                    <td class="small text-muted">{{ $p->notes ?: '—' }}</td>
                                    <td class="small text-muted">{{ $p->receivedBy->name }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button class="btn btn-xs btn-outline-primary"
                                                onclick="editPayment({{ $p->id }}, '{{ $p->amount }}', '{{ $p->payment_type }}', '{{ $p->payment_method }}', '{{ $p->payment_date->format('Y-m-d') }}', '{{ addslashes($p->notes ?? '') }}')">
                                                <i class="ti ti-pencil"></i>
                                            </button>
                                            <form action="{{ route('mess.rent.payments.destroy', [$mess->id, $p->id]) }}" method="POST"
                                                onsubmit="return confirm('Delete this payment entry?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger"><i class="ti ti-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-3">No payments recorded for this month.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>{{-- /tab-pane status --}}

            {{-- ===================================================== --}}
            {{-- TAB: Invoices (Landlord)                             --}}
            {{-- ===================================================== --}}
            <div class="tab-pane fade" id="pane-invoices">
                @php
                    $totalPaidToLandlord = $invoices->where('status','paid')->sum('rent_amount');
                    $surplus = $totalCollected - $totalPaidToLandlord;
                @endphp

                {{-- Surplus summary --}}
                <div class="row g-3 mt-1 mb-3">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success fs-3"><i class="ti ti-cash"></i></div>
                                <div>
                                    <div class="small text-muted">Collected from Members</div>
                                    <div class="fw-bold fs-14 text-success">৳{{ number_format($totalCollected, 2) }}</div>
                                    <div class="small text-muted">{{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="rounded-3 p-2 bg-danger bg-opacity-10 text-danger fs-3"><i class="ti ti-home-dollar"></i></div>
                                <div>
                                    <div class="small text-muted">Paid to House Owner</div>
                                    <div class="fw-bold fs-14 text-danger">৳{{ number_format($totalPaidToLandlord, 2) }}</div>
                                    <div class="small text-muted">Paid invoices (all time)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="rounded-3 p-2 bg-{{ $surplus >= 0 ? 'info' : 'warning' }} bg-opacity-10 text-{{ $surplus >= 0 ? 'info' : 'warning' }} fs-3">
                                    <i class="ti ti-piggy-bank"></i>
                                </div>
                                <div>
                                    <div class="small text-muted">Surplus This Month</div>
                                    <div class="fw-bold fs-14 text-{{ $surplus >= 0 ? 'info' : 'danger' }}">৳{{ number_format($surplus, 2) }}</div>
                                    <div class="small text-muted">Collected − Paid to owner</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="ti ti-file-invoice me-1"></i>House Rent Invoices — To House Owner</h6>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addInvoiceModal">
                            <i class="ti ti-file-plus me-1"></i>Create Invoice
                        </button>
                    </div>
                    @if($invoices->isEmpty())
                    <div class="card-body text-center text-muted py-5">
                        <i class="ti ti-file-invoice fs-1 d-block mb-2 opacity-30"></i>
                        No invoices yet. Create one to record the rent payment to the house owner.
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Period</th>
                                    <th>House Owner</th>
                                    <th>Rent Amount</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $inv)
                                <tr>
                                    <td>
                                        <a href="{{ route('mess.rent.invoices.show', [$mess->id, $inv->id]) }}" class="fw-semibold small text-primary">
                                            {{ $inv->invoice_no }}
                                        </a>
                                    </td>
                                    <td class="small">{{ date('M Y', mktime(0,0,0,$inv->month,1,$inv->year)) }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $inv->house_owner_name }}</div>
                                        @if($inv->house_owner_phone)<div class="small text-muted">{{ $inv->house_owner_phone }}</div>@endif
                                        @if($inv->property_address)<div class="small text-muted text-truncate" style="max-width:200px">{{ $inv->property_address }}</div>@endif
                                    </td>
                                    <td class="fw-bold text-danger">৳{{ number_format($inv->rent_amount, 2) }}</td>
                                    <td class="small text-muted">{{ $inv->invoice_date->format('d M Y') }}</td>
                                    <td class="small {{ $inv->due_date && $inv->due_date->isPast() && $inv->status !== 'paid' ? 'text-danger fw-semibold' : 'text-muted' }}">
                                        {{ $inv->due_date ? $inv->due_date->format('d M Y') : '—' }}
                                    </td>
                                    <td>
                                        {!! $inv->statusBadge() !!}
                                        @if($inv->status === 'paid' && $inv->paid_at)
                                        <div class="text-muted" style="font-size:10px">{{ $inv->paid_at->format('d M Y') }} by {{ $inv->paidBy->name }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('mess.rent.invoices.show', [$mess->id, $inv->id]) }}"
                                               class="btn btn-xs btn-outline-secondary" title="View Invoice">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            @if($inv->status === 'draft')
                                            <form action="{{ route('mess.rent.invoices.paid', [$mess->id, $inv->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-success" title="Mark as Paid">
                                                    <i class="ti ti-check"></i> Paid
                                                </button>
                                            </form>
                                            <form action="{{ route('mess.rent.invoices.cancel', [$mess->id, $inv->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-outline-warning" title="Cancel" onclick="return confirm('Cancel this invoice?')">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            </form>
                                            @endif
                                            <form action="{{ route('mess.rent.invoices.destroy', [$mess->id, $inv->id]) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete" onclick="return confirm('Delete invoice?')">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- Hidden printable invoices --}}
                <div class="d-none">
                    @foreach($invoices as $inv)
                    <div class="invoice-print" data-id="{{ $inv->id }}">
                        <div style="font-family:Arial,sans-serif;max-width:620px;margin:0 auto;padding:32px;border:1px solid #ccc;">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;">
                                <div>
                                    <div style="font-size:11px;color:#999;text-transform:uppercase;margin-bottom:4px;">From (Tenant)</div>
                                    <h2 style="margin:0 0 4px;color:#222;">{{ $mess->name }}</h2>
                                    @if($mess->address)<div style="color:#555;font-size:13px;">{{ $mess->address }}</div>@endif
                                </div>
                                <div style="text-align:right;">
                                    <h3 style="margin:0 0 4px;color:#0d6efd;font-size:20px;">RENT PAYMENT INVOICE</h3>
                                    <div style="font-size:13px;color:#666;">{{ $inv->invoice_no }}</div>
                                    <div style="font-size:12px;color:#999;margin-top:4px;">{{ date('F Y', mktime(0,0,0,$inv->month,1,$inv->year)) }}</div>
                                </div>
                            </div>
                            <hr style="border-color:#eee;margin-bottom:20px;">
                            <div style="display:flex;justify-content:space-between;margin-bottom:24px;">
                                <div>
                                    <div style="font-size:11px;color:#999;text-transform:uppercase;margin-bottom:4px;">To (House Owner)</div>
                                    <div style="font-weight:bold;font-size:15px;">{{ $inv->house_owner_name }}</div>
                                    @if($inv->house_owner_phone)<div style="font-size:13px;color:#555;">{{ $inv->house_owner_phone }}</div>@endif
                                    @if($inv->property_address)<div style="font-size:13px;color:#555;">{{ $inv->property_address }}</div>@endif
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-size:12px;color:#999;">Invoice Date</div>
                                    <div style="margin-bottom:8px;">{{ $inv->invoice_date->format('d M Y') }}</div>
                                    @if($inv->due_date)
                                    <div style="font-size:12px;color:#999;">Due Date</div>
                                    <div>{{ $inv->due_date->format('d M Y') }}</div>
                                    @endif
                                </div>
                            </div>
                            <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
                                <thead>
                                    <tr style="background:#f0f4ff;">
                                        <th style="padding:10px 12px;text-align:left;border:1px solid #dde;font-size:13px;">Description</th>
                                        <th style="padding:10px 12px;text-align:right;border:1px solid #dde;font-size:13px;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding:12px;border:1px solid #dde;">House Rent — {{ date('F Y', mktime(0,0,0,$inv->month,1,$inv->year)) }}</td>
                                        <td style="padding:12px;text-align:right;border:1px solid #dde;font-weight:bold;">৳{{ number_format($inv->rent_amount, 2) }}</td>
                                    </tr>
                                    @if($inv->notes)
                                    <tr>
                                        <td colspan="2" style="padding:8px 12px;border:1px solid #dde;font-size:12px;color:#666;font-style:italic;">Note: {{ $inv->notes }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr style="background:#e8f5e9;">
                                        <th style="padding:14px 12px;border:1px solid #dde;font-size:16px;">Total</th>
                                        <th style="padding:14px 12px;text-align:right;border:1px solid #dde;font-size:18px;color:#198754;">৳{{ number_format($inv->rent_amount, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div style="display:flex;justify-content:space-between;font-size:12px;color:#888;margin-top:16px;">
                                <div>Status: <strong style="color:{{ $inv->status === 'paid' ? '#198754' : ($inv->status === 'cancelled' ? '#6c757d' : '#856404') }}">{{ ucfirst($inv->status) }}</strong>
                                    @if($inv->status === 'paid' && $inv->paid_at) — {{ $inv->paid_at->format('d M Y') }}@endif
                                </div>
                                <div>Issued by: {{ $inv->issuedBy->name }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>{{-- /tab-pane invoices --}}

            {{-- ===================================================== --}}
            {{-- TAB: Rent Fund                                        --}}
            {{-- ===================================================== --}}
            <div class="tab-pane fade" id="pane-fund">
                {{-- Summary Cards --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="card border-0 bg-success bg-opacity-10 h-100">
                            <div class="card-body text-center">
                                <div class="small text-muted mb-1">Total Credited (Surplus)</div>
                                <div class="fs-4 fw-bold text-success">৳{{ number_format($totalCredited, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-danger bg-opacity-10 h-100">
                            <div class="card-body text-center">
                                <div class="small text-muted mb-1">Total Expensed</div>
                                <div class="fs-4 fw-bold text-danger">৳{{ number_format($totalDebited, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-info bg-opacity-10 h-100">
                            <div class="card-body text-center">
                                <div class="small text-muted mb-1">Current Balance</div>
                                <div class="fs-4 fw-bold text-info">৳{{ number_format($fundBalance, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Add Expense Form --}}
                @if(Auth::user()->isManagerOf($mess->id) || Auth::user()->isOwnerOf($mess->id))
                <div class="card mb-3">
                    <div class="card-header"><h6 class="mb-0"><i class="ti ti-receipt me-1"></i>Record Expense from Rent Fund</h6></div>
                    <div class="card-body">
                        @if($fundBalance <= 0)
                        <div class="alert alert-warning mb-0">No balance available in the rent fund to record an expense.</div>
                        @else
                        <form action="{{ route('mess.rent.fund.expense', $mess->id) }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label">Description <span class="text-danger">*</span></label>
                                    <input type="text" name="description" class="form-control" placeholder="e.g. Electricity bill, Cleaning supplies" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" max="{{ $fundBalance }}" placeholder="0.00" required>
                                    </div>
                                    <div class="form-text">Max: ৳{{ number_format($fundBalance, 2) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="ti ti-minus me-1"></i>Expense
                                    </button>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Note <span class="text-muted small">(optional)</span></label>
                                    <textarea name="note" class="form-control" rows="2" placeholder="Additional note…"></textarea>
                                </div>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Transaction History --}}
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Transaction History</h6></div>
                    @if($fundTransactions->isEmpty())
                    <div class="card-body text-center text-muted py-4">
                        <i class="ti ti-piggy-bank fs-1 d-block mb-2 opacity-30"></i>
                        No transactions yet. Surplus will be credited here when invoices are marked as paid.
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Source</th>
                                    <th>Note</th>
                                    <th>By</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fundTransactions as $t)
                                <tr>
                                    <td class="small">{{ $t->transaction_date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge {{ $t->type === 'credit' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($t->type) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold {{ $t->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                        {{ $t->type === 'credit' ? '+' : '-' }}৳{{ number_format($t->amount, 2) }}
                                    </td>
                                    <td class="small">{{ $t->description }}</td>
                                    <td class="small text-muted">{{ str_replace('_', ' ', ucfirst($t->source)) }}</td>
                                    <td class="small text-muted">{{ $t->note ?: '—' }}</td>
                                    <td class="small text-muted">{{ $t->recordedBy->name ?? '—' }}</td>
                                    <td>
                                        @if(Auth::user()->isManagerOf($mess->id) || Auth::user()->isOwnerOf($mess->id))
                                        <form action="{{ route('mess.rent.fund.destroy', [$mess->id, $t->id]) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger" onclick="return confirm('Delete this transaction?')">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>{{-- /tab-pane fund --}}

            {{-- ===================================================== --}}
            {{-- TAB 2: Full Payment History                           --}}
            {{-- ===================================================== --}}
            <div class="tab-pane fade" id="pane-history">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">All Payment Records</h6>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                            <i class="ti ti-circle-plus me-1"></i>Record Payment
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="paymentHistoryTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Period</th>
                                    <th>Date</th>
                                    <th>Member</th>
                                    <th>Room</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Method</th>
                                    <th>Notes</th>
                                    <th>Recorded By</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allPayments as $p)
                                <tr>
                                    <td class="small fw-semibold">{{ date('M Y', mktime(0,0,0,$p->month,1,$p->year)) }}</td>
                                    <td class="small">{{ $p->payment_date->format('d M Y') }}</td>
                                    <td>{{ $p->member->user->name }}</td>
                                    <td class="small text-muted">{{ $p->member->room_no ?: '—' }}</td>
                                    <td class="fw-semibold {{ $p->payment_type === 'discount' ? 'text-danger' : 'text-success' }}">
                                        {{ $p->payment_type === 'discount' ? '-' : '+' }}৳{{ number_format($p->amount, 2) }}
                                    </td>
                                    <td>
                                        @php
                                            $typeBadge = match($p->payment_type) {
                                                'rent'       => 'bg-success',
                                                'penalty'    => 'bg-danger',
                                                'discount'   => 'bg-warning text-dark',
                                                'adjustment' => 'bg-info text-dark',
                                                default      => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $typeBadge }}">{{ $p->payment_type_label }}</span>
                                    </td>
                                    <td class="small text-muted">{{ $p->payment_method_label }}</td>
                                    <td class="small text-muted">{{ Str::limit($p->notes, 40) ?: '—' }}</td>
                                    <td class="small text-muted">{{ $p->receivedBy->name }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button class="btn btn-xs btn-outline-primary"
                                                onclick="editPayment({{ $p->id }}, '{{ $p->amount }}', '{{ $p->payment_type }}', '{{ $p->payment_method }}', '{{ $p->payment_date->format('Y-m-d') }}', '{{ addslashes($p->notes ?? '') }}')">
                                                <i class="ti ti-pencil"></i>
                                            </button>
                                            <form action="{{ route('mess.rent.payments.destroy', [$mess->id, $p->id]) }}" method="POST"
                                                onsubmit="return confirm('Delete this payment?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger"><i class="ti ti-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="ti ti-receipt-off fs-2 d-block mb-2"></i>No payment records yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>{{-- /tab-pane history --}}

            {{-- ===================================================== --}}
            {{-- TAB 3: Advance / Security Deposit Ledger              --}}
            {{-- ===================================================== --}}
            <div class="tab-pane fade" id="pane-advance">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Advance &amp; Security Deposit Ledger</h6>
                        <button class="btn btn-warning btn-sm text-dark" data-bs-toggle="modal" data-bs-target="#addAdvanceModal">
                            <i class="ti ti-circle-plus me-1"></i>Record Transaction
                        </button>
                    </div>

                    {{-- Per-member advance summary --}}
                    <div class="px-3 pt-3 pb-1">
                        <div class="row g-2">
                            @foreach($members->where(fn($m) => ($advanceBalanceByMember[$m->id] ?? 0) != 0) as $m)
                            <div class="col-auto">
                                <div class="border rounded px-3 py-2 d-flex align-items-center gap-2 bg-light">
                                    <span class="fw-semibold small">{{ $m->user->name }}</span>
                                    @if($m->room_no)<span class="badge bg-secondary" style="font-size:9px;">{{ $m->room_no }}</span>@endif
                                    <span class="fw-bold small {{ ($advanceBalanceByMember[$m->id] ?? 0) > 0 ? 'text-warning' : 'text-danger' }}">
                                        ৳{{ number_format($advanceBalanceByMember[$m->id] ?? 0, 0) }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                            @if($members->filter(fn($m) => ($advanceBalanceByMember[$m->id] ?? 0) != 0)->isEmpty())
                            <div class="col-12"><span class="text-muted small">No advance balances yet.</span></div>
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Member</th>
                                    <th>Room</th>
                                    <th>Transaction</th>
                                    <th>Amount</th>
                                    <th>Running Balance</th>
                                    <th>Notes</th>
                                    <th>By</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Running balance per member as we iterate
                                    $runningBalance = [];
                                    foreach ($members as $m) $runningBalance[$m->id] = 0;
                                    // Pre-group and sort by member+date for running balance
                                    $advSortedByMember = $advancePayments->sortBy(fn($a) => $a->member_id . '_' . $a->transaction_date->format('Ymd') . '_' . str_pad($a->id, 10, '0', STR_PAD_LEFT));
                                    $memberRunning = [];
                                @endphp
                                @forelse($advancePayments as $a)
                                @php
                                    $mid = $a->member_id;
                                    if (!isset($memberRunning[$mid])) $memberRunning[$mid] = 0;
                                    $memberRunning[$mid] += $a->signed_amount;
                                    $txBadge = match($a->transaction_type) {
                                        'received' => 'bg-success',
                                        'refunded' => 'bg-danger',
                                        'adjusted' => 'bg-info text-dark',
                                        default    => 'bg-secondary',
                                    };
                                @endphp
                                <tr>
                                    <td class="small">{{ $a->transaction_date->format('d M Y') }}</td>
                                    <td class="fw-semibold">{{ $a->member->user->name }}</td>
                                    <td class="small text-muted">{{ $a->member->room_no ?: '—' }}</td>
                                    <td><span class="badge {{ $txBadge }}">{{ $a->transaction_type_label }}</span></td>
                                    <td class="fw-semibold {{ $a->transaction_type === 'received' ? 'text-success' : 'text-danger' }}">
                                        {{ $a->transaction_type === 'received' ? '+' : '-' }}৳{{ number_format($a->amount, 2) }}
                                    </td>
                                    <td class="fw-semibold {{ $memberRunning[$mid] >= 0 ? 'text-primary' : 'text-danger' }}">
                                        ৳{{ number_format($memberRunning[$mid], 2) }}
                                    </td>
                                    <td class="small text-muted">{{ Str::limit($a->notes, 40) ?: '—' }}</td>
                                    <td class="small text-muted">{{ $a->processedBy->name }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button class="btn btn-xs btn-outline-primary"
                                                onclick="editAdvance({{ $a->id }}, '{{ $a->transaction_type }}', '{{ $a->amount }}', '{{ $a->transaction_date->format('Y-m-d') }}', '{{ addslashes($a->notes ?? '') }}')">
                                                <i class="ti ti-pencil"></i>
                                            </button>
                                            <form action="{{ route('mess.rent.advances.destroy', [$mess->id, $a->id]) }}" method="POST"
                                                onsubmit="return confirm('Delete this advance transaction?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger"><i class="ti ti-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="ti ti-safe fs-2 d-block mb-2"></i>No advance transactions recorded.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>{{-- /tab-pane advance --}}

        </div>{{-- /tab-content --}}
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL: Create Invoice (to House Owner / Landlord)            --}}
{{-- ============================================================== --}}
<div class="modal fade" id="addInvoiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ti ti-file-invoice me-2"></i>Create Rent Payment Invoice</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.rent.invoices.store', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info py-2 small mb-3">
                        <i class="ti ti-info-circle me-1"></i>
                        This invoice records the rent payment from <strong>{{ $mess->name }}</strong> to the house owner/landlord.
                    </div>

                    <div class="row g-3">
                        {{-- House Owner Info --}}
                        <div class="col-12">
                            <h6 class="fw-semibold text-muted border-bottom pb-1 mb-2">House Owner Details</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Owner Name <span class="text-danger">*</span></label>
                            <input type="text" name="house_owner_name" class="form-control" required maxlength="150"
                                placeholder="e.g. Mr. Karim">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Owner Phone</label>
                            <input type="text" name="house_owner_phone" class="form-control" maxlength="30"
                                placeholder="e.g. 01XXXXXXXXX">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Property Address</label>
                            <input type="text" name="property_address" class="form-control" maxlength="500"
                                placeholder="House/flat address" value="{{ $mess->address ?? '' }}">
                        </div>

                        {{-- Invoice Details --}}
                        <div class="col-12 mt-1">
                            <h6 class="fw-semibold text-muted border-bottom pb-1 mb-2">Invoice Details</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Month <span class="text-danger">*</span></label>
                            <select name="month" class="form-select" required>
                                @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Year <span class="text-danger">*</span></label>
                            <select name="year" class="form-select" required>
                                @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Rent Amount (৳) <span class="text-danger">*</span></label>
                            <input type="number" name="rent_amount" class="form-control" required min="1" step="0.01"
                                placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" name="invoice_date" class="form-control" required value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Due Date</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" maxlength="500"
                                placeholder="e.g. Paid via bKash, receipt no. 12345"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-file-invoice me-1"></i>Create Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- ============================================================== --}}
{{-- MODAL: Set Rent & Room for a Member                           --}}
{{-- ============================================================== --}}
<div class="modal fade" id="setRentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ti ti-settings me-2"></i>Set Rent — <span id="setRentMemberName"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="setRentForm" action="{{ route('mess.rent.set', $mess->id) }}" method="POST">
                @csrf
                <input type="hidden" name="member_id" id="setRentMemberId">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Room / Unit No.</label>
                            <input type="text" name="room_no" id="setRentRoomNo" class="form-control" placeholder="e.g. 2A, Room 3">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Monthly Rent (৳) <span class="text-danger">*</span></label>
                            <input type="number" name="house_rent" id="setRentAmount" class="form-control" required step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Internal Notes</label>
                            <textarea name="notes" id="setRentNotes" class="form-control" rows="2" placeholder="Any notes about this member's rental arrangement..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL: Record Rent Payment                                    --}}
{{-- ============================================================== --}}
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="ti ti-cash me-2"></i>Record Rent Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.rent.payments.store', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Member <span class="text-danger">*</span></label>
                        <select name="member_id" id="addPaymentMemberId" class="form-select" required>
                            <option value="">— Select Member —</option>
                            @foreach($members as $m)
                            <option value="{{ $m->id }}" data-rent="{{ $m->house_rent }}">
                                {{ $m->user->name }}{{ $m->room_no ? ' ('.$m->room_no.')' : '' }}
                                — ৳{{ number_format($m->house_rent, 0) }}/mo
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Month <span class="text-danger">*</span></label>
                            <select name="month" class="form-select" required>
                                @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Year <span class="text-danger">*</span></label>
                            <input type="number" name="year" class="form-control" value="{{ $year }}" required min="2020" max="2100">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="addPaymentAmount" class="form-control" required step="0.01" min="0.01" placeholder="0.00">
                        <div class="form-text" id="addPaymentHint"></div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Payment Type <span class="text-danger">*</span></label>
                            <select name="payment_type" class="form-select" required>
                                <option value="rent" selected>Rent</option>
                                <option value="penalty">Penalty (Late Fee)</option>
                                <option value="discount">Discount / Waiver</option>
                                <option value="adjustment">Adjustment</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash" selected>Cash</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Receipt no., any remarks...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="ti ti-device-floppy me-1"></i>Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL: Edit Rent Payment                                      --}}
{{-- ============================================================== --}}
<div class="modal fade" id="editPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ti ti-pencil me-2"></i>Edit Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPaymentForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="editPaymentAmount" class="form-control" required step="0.01" min="0.01">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Payment Type</label>
                            <select name="payment_type" id="editPaymentType" class="form-select" required>
                                <option value="rent">Rent</option>
                                <option value="penalty">Penalty (Late Fee)</option>
                                <option value="discount">Discount / Waiver</option>
                                <option value="adjustment">Adjustment</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Payment Method</label>
                            <select name="payment_method" id="editPaymentMethod" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Date</label>
                        <input type="date" name="payment_date" id="editPaymentDate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <input type="text" name="notes" id="editPaymentNotes" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL: Record Advance Transaction                             --}}
{{-- ============================================================== --}}
<div class="modal fade" id="addAdvanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="ti ti-safe me-2"></i>Record Advance / Security Deposit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.rent.advances.store', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Member <span class="text-danger">*</span></label>
                        <select name="member_id" id="addAdvanceMemberId" class="form-select" required>
                            <option value="">— Select Member —</option>
                            @foreach($members as $m)
                            <option value="{{ $m->id }}">
                                {{ $m->user->name }}{{ $m->room_no ? ' ('.$m->room_no.')' : '' }}
                                — Balance: ৳{{ number_format($advanceBalanceByMember[$m->id] ?? 0, 0) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Transaction Type <span class="text-danger">*</span></label>
                        <select name="transaction_type" class="form-select" required>
                            <option value="received" selected>Received from Tenant</option>
                            <option value="refunded">Refunded to Tenant</option>
                            <option value="adjusted">Adjusted to Rent</option>
                        </select>
                        <div class="form-text">
                            <b>Received</b>: collected from tenant &nbsp;|&nbsp;
                            <b>Refunded</b>: returned to tenant &nbsp;|&nbsp;
                            <b>Adjusted</b>: applied against rent due
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" required step="0.01" min="0.01" placeholder="0.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Transaction Date <span class="text-danger">*</span></label>
                        <input type="date" name="transaction_date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Purpose, reference, remarks...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark"><i class="ti ti-device-floppy me-1"></i>Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL: Edit Advance Transaction                               --}}
{{-- ============================================================== --}}
<div class="modal fade" id="editAdvanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ti ti-pencil me-2"></i>Edit Advance Transaction</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAdvanceForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Transaction Type</label>
                        <select name="transaction_type" id="editAdvanceType" class="form-select" required>
                            <option value="received">Received from Tenant</option>
                            <option value="refunded">Refunded to Tenant</option>
                            <option value="adjusted">Adjusted to Rent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount (৳)</label>
                        <input type="number" name="amount" id="editAdvanceAmount" class="form-control" required step="0.01" min="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Transaction Date</label>
                        <input type="date" name="transaction_date" id="editAdvanceDate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <input type="text" name="notes" id="editAdvanceNotes" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ---- Set Rent Modal ----
function openSetRent(memberId, name, rent, roomNo, notes) {
    document.getElementById('setRentMemberId').value         = memberId;
    document.getElementById('setRentMemberName').textContent = name;
    document.getElementById('setRentAmount').value           = rent;
    document.getElementById('setRentRoomNo').value           = roomNo;
    document.getElementById('setRentNotes').value            = notes;
    new bootstrap.Modal(document.getElementById('setRentModal')).show();
}

// ---- Add Payment Modal (pre-select member) ----
function openAddPayment(memberId, memberName) {
    var sel = document.getElementById('addPaymentMemberId');
    sel.value = memberId;
    sel.dispatchEvent(new Event('change'));
    new bootstrap.Modal(document.getElementById('addPaymentModal')).show();
}

// Auto-fill amount hint when member selected
document.addEventListener('DOMContentLoaded', function () {
    var memberSel = document.getElementById('addPaymentMemberId');
    if (memberSel) {
        memberSel.addEventListener('change', function () {
            var opt  = this.options[this.selectedIndex];
            var rent = opt ? (parseFloat(opt.dataset.rent) || 0) : 0;
            var hint = document.getElementById('addPaymentHint');
            if (rent > 0) {
                hint.textContent = 'Monthly rent: ৳' + rent.toFixed(2);
                document.getElementById('addPaymentAmount').placeholder = rent.toFixed(2);
            } else {
                hint.textContent = '';
                document.getElementById('addPaymentAmount').placeholder = '0.00';
            }
        });
    }
});

// ---- Edit Payment Modal ----
function editPayment(id, amount, type, method, date, notes) {
    var base = '{{ route("mess.rent.payments.update", [$mess->id, "__ID__"]) }}';
    document.getElementById('editPaymentForm').action      = base.replace('__ID__', id);
    document.getElementById('editPaymentAmount').value     = amount;
    document.getElementById('editPaymentType').value       = type;
    document.getElementById('editPaymentMethod').value     = method;
    document.getElementById('editPaymentDate').value       = date;
    document.getElementById('editPaymentNotes').value      = notes;
    new bootstrap.Modal(document.getElementById('editPaymentModal')).show();
}

// ---- Add Advance Modal (pre-select member) ----
function openAddAdvance(memberId, memberName) {
    var sel = document.getElementById('addAdvanceMemberId');
    if (memberId) sel.value = memberId;
    new bootstrap.Modal(document.getElementById('addAdvanceModal')).show();
}

// ---- Edit Advance Modal ----
function editAdvance(id, type, amount, date, notes) {
    var base = '{{ route("mess.rent.advances.update", [$mess->id, "__ID__"]) }}';
    document.getElementById('editAdvanceForm').action      = base.replace('__ID__', id);
    document.getElementById('editAdvanceType').value       = type;
    document.getElementById('editAdvanceAmount').value     = amount;
    document.getElementById('editAdvanceDate').value       = date;
    document.getElementById('editAdvanceNotes').value      = notes;
    new bootstrap.Modal(document.getElementById('editAdvanceModal')).show();
}


// ---- Print Invoice ----
function printInvoice(id) {
    var el = document.querySelector('.invoice-print[data-id="' + id + '"]');
    if (!el) return;
    var win = window.open('', '_blank', 'width=700,height=800');
    win.document.write('<html><head><title>Invoice</title></head><body>' + el.innerHTML + '</body></html>');
    win.document.close();
    win.focus();
    win.print();
    win.close();
}
</script>
@endsection
