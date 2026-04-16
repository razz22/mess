<?php $page = "mess-rent" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-file-invoice me-2 text-primary"></i>{{ $invoice->invoice_no }}</h4>
                <h6 class="text-muted">{{ date('F Y', mktime(0,0,0,$invoice->month,1,$invoice->year)) }} &mdash; {{ $mess->name }}</h6>
            </div>
            <div class="page-btn d-flex gap-2 no-print">
                <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="ti ti-printer me-1"></i>Print
                </button>
                <a href="{{ route('mess.rent.index', $mess->id) }}?month={{ $invoice->month }}&year={{ $invoice->year }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 no-print">
            <i class="ti ti-circle-check me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 no-print">
            {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row g-3">

            {{-- ===================== LEFT: INVOICE DOCUMENT ===================== --}}
            <div class="col-lg-7">
                <div class="card" id="invoiceDocument">
                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold mb-1">From (Tenant)</div>
                                <h4 class="fw-bold mb-1">{{ $mess->name }}</h4>
                                @if($mess->address)
                                <div class="text-muted small"><i class="ti ti-map-pin me-1"></i>{{ $mess->address }}</div>
                                @endif
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary" style="font-size:18px;letter-spacing:1px;">RENT INVOICE</div>
                                <div><code class="small text-muted">{{ $invoice->invoice_no }}</code></div>
                                <div class="mt-1">{!! $invoice->statusBadge() !!}</div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="text-muted small text-uppercase fw-semibold mb-1">To (House Owner)</div>
                                <div class="fw-bold fs-14">{{ $invoice->house_owner_name }}</div>
                                @if($invoice->house_owner_phone)
                                <div class="small text-muted"><i class="ti ti-phone me-1"></i>{{ $invoice->house_owner_phone }}</div>
                                @endif
                                @if($invoice->property_address)
                                <div class="small text-muted"><i class="ti ti-map-pin me-1"></i>{{ $invoice->property_address }}</div>
                                @endif
                            </div>
                            <div class="col-6 text-end">
                                <div class="mb-2">
                                    <div class="text-muted small fw-semibold">Invoice Date</div>
                                    <div>{{ $invoice->invoice_date->format('d M Y') }}</div>
                                </div>
                                @if($invoice->due_date)
                                <div class="mb-2">
                                    <div class="text-muted small fw-semibold">Due Date</div>
                                    <div class="{{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-danger fw-semibold' : '' }}">
                                        {{ $invoice->due_date->format('d M Y') }}
                                    </div>
                                </div>
                                @endif
                                <div>
                                    <div class="text-muted small fw-semibold">Period</div>
                                    <div>{{ date('F Y', mktime(0,0,0,$invoice->month,1,$invoice->year)) }}</div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end" style="width:160px">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>House Rent — {{ date('F Y', mktime(0,0,0,$invoice->month,1,$invoice->year)) }}</td>
                                    <td class="text-end fw-bold">৳{{ number_format($invoice->rent_amount, 2) }}</td>
                                </tr>
                                @if($invoice->notes)
                                <tr class="table-light">
                                    <td colspan="2" class="text-muted small fst-italic">
                                        <i class="ti ti-note me-1"></i>{{ $invoice->notes }}
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <th class="fs-14">Total Payable</th>
                                    <th class="text-end fs-14 text-success">৳{{ number_format($invoice->rent_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="d-flex justify-content-between align-items-center text-muted small">
                            <div>Issued by: <strong>{{ $invoice->issuedBy->name }}</strong></div>
                            @if($invoice->status === 'paid' && $invoice->paid_at)
                            <div class="text-success fw-semibold">
                                <i class="ti ti-circle-check me-1"></i>
                                Paid {{ $invoice->paid_at->format('d M Y') }} by {{ $invoice->paidBy->name }}
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($invoice->status === 'draft')
                    <div class="card-footer d-flex gap-2 no-print">
                        <form action="{{ route('mess.rent.invoices.paid', [$mess->id, $invoice->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="ti ti-check me-1"></i>Mark as Paid
                            </button>
                        </form>
                        <form action="{{ route('mess.rent.invoices.cancel', [$mess->id, $invoice->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning btn-sm"
                                onclick="return confirm('Cancel this invoice?')">
                                <i class="ti ti-x me-1"></i>Cancel Invoice
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ===================== RIGHT: SURPLUS & EXPENSE ===================== --}}
            <div class="col-lg-5 no-print">

                {{-- Surplus Summary Card --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="ti ti-calculator me-1 text-info"></i>
                            Surplus — {{ date('F Y', mktime(0,0,0,$month,1,$year)) }}
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-borderless mb-0 small">
                            <tr>
                                <td class="text-muted ps-3">Collected from Members</td>
                                <td class="text-end pe-3 text-success fw-semibold">৳{{ number_format($totalCollected, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-3">Paid to House Owner</td>
                                <td class="text-end pe-3 text-danger fw-semibold">− ৳{{ number_format($invoice->rent_amount, 2) }}</td>
                            </tr>
                            @if($totalExpensed > 0)
                            <tr>
                                <td class="text-muted ps-3">Expensed from Surplus</td>
                                <td class="text-end pe-3 text-warning fw-semibold">− ৳{{ number_format($totalExpensed, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="border-top">
                                <td class="ps-3 fw-bold">Remaining Surplus</td>
                                <td class="text-end pe-3 fw-bold fs-5 {{ $remaining >= 0 ? 'text-info' : 'text-danger' }}">
                                    ৳{{ number_format($remaining, 2) }}
                                </td>
                            </tr>
                        </table>

                        @if($remaining > 0)
                        <div class="px-3 pb-3">
                            <div class="alert alert-info py-2 small mb-0">
                                <i class="ti ti-piggy-bank me-1"></i>
                                <strong>৳{{ number_format($remaining, 2) }}</strong> surplus available to expense below.
                            </div>
                        </div>
                        @elseif($remaining < 0)
                        <div class="px-3 pb-3">
                            <div class="alert alert-danger py-2 small mb-0">
                                <i class="ti ti-alert-triangle me-1"></i>
                                Over-expensed by <strong>৳{{ number_format(abs($remaining), 2) }}</strong>.
                            </div>
                        </div>
                        @else
                        <div class="px-3 pb-3">
                            <div class="alert alert-success py-2 small mb-0">
                                <i class="ti ti-circle-check me-1"></i>Surplus fully utilised.
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Add Expense Form --}}
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="ti ti-receipt me-1 text-danger"></i>Add Expense from Surplus</h6>
                    </div>
                    <div class="card-body p-3">
                        @if($remaining <= 0)
                        <div class="text-center text-muted py-3">
                            <i class="ti ti-ban fs-2 d-block mb-1 opacity-30"></i>
                            No surplus remaining to expense.
                        </div>
                        @else
                        <form action="{{ route('mess.rent.invoices.expense', [$mess->id, $invoice->id]) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Description <span class="text-danger">*</span></label>
                                <input type="text" name="description" class="form-control form-control-sm"
                                    required maxlength="500"
                                    placeholder="e.g. Cook bill, Maintenance, Cleaning…">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Amount (৳) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" class="form-control form-control-sm"
                                    required min="0.01" step="0.01" max="{{ $remaining }}"
                                    placeholder="Max ৳{{ number_format($remaining, 2) }}">
                                <div class="form-text">Available: <strong class="text-info">৳{{ number_format($remaining, 2) }}</strong></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Date <span class="text-danger">*</span></label>
                                <input type="date" name="expense_date" class="form-control form-control-sm"
                                    required value="{{ now()->toDateString() }}">
                            </div>
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="ti ti-minus me-1"></i>Record Expense
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                {{-- Expenses List --}}
                @if($expenses->count())
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Surplus Expenses</h6>
                        <span class="badge bg-danger">৳{{ number_format($totalExpensed, 2) }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $e)
                                <tr>
                                    <td class="text-muted text-nowrap">{{ $e->expense_date->format('d M Y') }}</td>
                                    <td>{{ $e->description }}</td>
                                    <td class="text-end text-danger fw-semibold">৳{{ number_format($e->amount, 2) }}</td>
                                    <td>
                                        <form action="{{ route('mess.rent.invoices.expense.destroy', [$mess->id, $e->id]) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger"
                                                onclick="return confirm('Delete this expense?')">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2">Total</th>
                                    <th class="text-end text-danger">৳{{ number_format($totalExpensed, 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endif

            </div>{{-- /col right --}}
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, nav, .sidebar, .header, header, footer { display: none !important; }
    .col-lg-7 { width:100%!important; flex:0 0 100%!important; max-width:100%!important; }
    .col-lg-5 { display:none!important; }
    .page-wrapper { padding:0!important; }
    .content { padding:0!important; }
    #invoiceDocument { border:1px solid #ccc!important; box-shadow:none!important; }
}
</style>
@endsection
