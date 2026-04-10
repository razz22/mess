<?php $page = "mess-market-list" ?>
@extends('layout.mainlayout')
@section('content')
@php
    $isManager = $member && in_array($member->role, ['owner', 'manager']);
    $canEdit   = $isManager || $routine->assigned_to === Auth::id();
@endphp
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">
                    Shopping List —
                    {{ $routine->start_date->format('d M') }}
                    @if($routine->start_date->ne($routine->end_date))
                        → {{ $routine->end_date->format('d M Y') }}
                    @else
                        {{ $routine->start_date->format('Y') }}
                    @endif
                </h4>
                <h6>Assigned to: <strong>{{ $routine->assignedTo->name }}</strong>
                    <span class="badge bg-{{ $routine->status === 'completed' ? 'success' : 'warning' }} ms-2">{{ ucfirst($routine->status) }}</span>
                </h6>
            </div>
            <div class="page-btn d-flex gap-2">
                @if($canEdit && $routine->status === 'pending')
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="ti ti-plus me-1"></i>Add Item
                </button>
                <form action="{{ route('mess.market.complete', [$mess->id, $routine->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Mark as completed? This will create market expenses.')">
                        <i class="ti ti-check me-1"></i>Mark Complete
                    </button>
                </form>
                @endif
                <a href="{{ route('mess.market', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Items List</h6>
                        <span class="text-muted small">
                            Purchased: <strong>{{ $items->where('purchased', true)->count() }}</strong> / {{ $items->count() }}
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Buyer</th>
                                    <th>Date</th>
                                    <th class="text-end">Est.</th>
                                    <th class="text-end">Actual</th>
                                    <th class="text-center">Done</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                <tr id="row-{{ $item->id }}" class="{{ $item->purchased ? 'table-success opacity-75' : '' }}">
                                    <td>
                                        <div class="fw-semibold {{ $item->purchased ? 'text-decoration-line-through text-muted' : '' }}">
                                            {{ $item->item_name }}
                                        </div>
                                        <small class="text-muted">Added by {{ $item->addedBy->name }}</small>
                                    </td>
                                    <td class="text-nowrap">{{ $item->quantity }} {{ $item->unit }}</td>
                                    <td>
                                        @if($canEdit)
                                        <select class="form-select form-select-sm" style="min-width:110px"
                                            onchange="updateItem({{ $item->id }}, {assigned_to: this.value})">
                                            <option value="">— Default —</option>
                                            @foreach($members as $m)
                                            <option value="{{ $m->user->id }}" {{ $item->assigned_to == $m->user->id ? 'selected' : '' }}>
                                                {{ $m->user->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @else
                                            {{ $item->assignedTo?->name ?? $routine->assignedTo->name }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($canEdit)
                                        <input type="date" class="form-control form-control-sm" style="min-width:120px"
                                            value="{{ $item->expense_date?->format('Y-m-d') ?? '' }}"
                                            onchange="updateItem({{ $item->id }}, {expense_date: this.value})"
                                            placeholder="{{ $routine->start_date->format('Y-m-d') }}">
                                        @else
                                            {{ $item->expense_date?->format('d M') ?? '—' }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($canEdit)
                                        <input type="number" class="form-control form-control-sm text-end" style="width:90px"
                                            value="{{ $item->estimated_cost }}" step="0.01" min="0"
                                            onchange="updateItem({{ $item->id }}, {estimated_cost: this.value})">
                                        @else
                                            {{ $item->estimated_cost > 0 ? '৳'.number_format($item->estimated_cost,2) : '—' }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($canEdit)
                                        <input type="number" class="form-control form-control-sm text-end" style="width:90px"
                                            value="{{ $item->actual_cost }}" step="0.01" min="0"
                                            onchange="updateItem({{ $item->id }}, {actual_cost: this.value})">
                                        @else
                                            {{ $item->actual_cost > 0 ? '৳'.number_format($item->actual_cost,2) : '—' }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($canEdit)
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" {{ $item->purchased ? 'checked' : '' }}
                                                onchange="updateItem({{ $item->id }}, {purchased: this.checked ? 1 : 0}); toggleRow({{ $item->id }}, this.checked)">
                                        </div>
                                        @else
                                        <span class="badge bg-{{ $item->purchased ? 'success' : 'secondary' }}">
                                            {{ $item->purchased ? 'Done' : 'Pending' }}
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-4">
                                    <i class="ti ti-shopping-cart-off fs-3 d-block mb-2 opacity-30"></i>
                                    No items yet. @if($canEdit)Click <strong>Add Item</strong> to start.@endif
                                </td></tr>
                                @endforelse
                            </tbody>
                            @if($items->count() > 0)
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="4">Total</td>
                                    <td class="text-end">৳{{ number_format($items->sum('estimated_cost'),2) }}</td>
                                    <td class="text-end" id="actualTotal">৳{{ number_format($items->sum('actual_cost'),2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Summary -->
                <div class="card mb-3">
                    <div class="card-header"><h6 class="mb-0"><i class="ti ti-chart-bar me-2"></i>Summary</h6></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Items</span><strong>{{ $items->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-success">Purchased</span><strong class="text-success">{{ $items->where('purchased',true)->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Est. Total</span><strong>৳{{ number_format($items->sum('estimated_cost'),2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Actual Total</span>
                            <strong class="text-primary fs-5" id="actualTotalCard">৳{{ number_format($items->sum('actual_cost'),2) }}</strong>
                        </div>
                        @if($items->sum('actual_cost') > 0 && $items->sum('estimated_cost') > 0)
                        @php $diff = $items->sum('actual_cost') - $items->sum('estimated_cost'); @endphp
                        <div class="mt-2 small {{ $diff > 0 ? 'text-danger' : 'text-success' }}">
                            {{ $diff > 0 ? '▲ Over by' : '▼ Under by' }} ৳{{ number_format(abs($diff),2) }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Buyer Breakdown -->
                @if($items->count() > 0)
                @php
                    $byBuyer = $items->groupBy(fn($i) => $i->assigned_to ?? $routine->assigned_to);
                @endphp
                <div class="card mb-3">
                    <div class="card-header"><h6 class="mb-0"><i class="ti ti-users me-2"></i>By Buyer</h6></div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($byBuyer as $buyerId => $buyerItems)
                            @php
                                $buyerName = $buyerItems->first()->assignedTo?->name ?? $routine->assignedTo->name;
                                $buyerTotal = $buyerItems->sum('actual_cost');
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $buyerName }}</span>
                                <span class="fw-semibold">৳{{ number_format($buyerTotal,2) }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                @if($routine->notes)
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Notes</h6></div>
                    <div class="card-body"><p class="mb-0 text-muted">{{ $routine->notes }}</p></div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-shopping-cart me-2"></i>Add Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.market.list.add', [$mess->id, $routine->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                        <input type="text" name="item_name" class="form-control" required placeholder="e.g. Potato, Fish, Oil">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Quantity</label>
                            <input type="text" name="quantity" class="form-control" placeholder="e.g. 2">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit" class="form-control" placeholder="kg, litre, pcs">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Est. Cost (৳)</label>
                            <input type="number" name="estimated_cost" class="form-control" step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Actual Cost (৳)</label>
                            <input type="number" name="actual_cost" class="form-control" step="0.01" min="0" placeholder="0.00">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Bought By</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">— {{ $routine->assignedTo->name }} (default) —</option>
                                @foreach($members as $m)
                                @if($m->user->id !== $routine->assigned_to)
                                <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Purchase Date</label>
                            <input type="date" name="expense_date" class="form-control"
                                value="{{ $routine->start_date->format('Y-m-d') }}"
                                min="{{ $routine->start_date->format('Y-m-d') }}"
                                max="{{ $routine->end_date->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const messId = {{ $mess->id }};
const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

function updateItem(itemId, data) {
    fetch(`/mess/${messId}/market/list/${itemId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(d => {
        if (d.total !== undefined) {
            const fmt = '৳' + parseFloat(d.total).toFixed(2);
            const el1 = document.getElementById('actualTotal');
            const el2 = document.getElementById('actualTotalCard');
            if (el1) el1.textContent = fmt;
            if (el2) el2.textContent = fmt;
        }
    });
}

function toggleRow(itemId, purchased) {
    const row = document.getElementById('row-' + itemId);
    if (row) {
        row.classList.toggle('table-success', purchased);
        row.classList.toggle('opacity-75', purchased);
    }
}
</script>
@endsection
