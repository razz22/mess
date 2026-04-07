<?php $page = "mess-market-list" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">Shopping List — {{ $routine->date->format('d F Y') }}</h4>
                <h6>Assigned to: <strong>{{ $routine->assignedTo->name }}</strong>
                    <span class="badge bg-{{ $routine->status === 'completed' ? 'success' : 'warning' }} ms-2">{{ ucfirst($routine->status) }}</span>
                </h6>
            </div>
            <div class="page-btn d-flex gap-2">
                <a href="{{ route('mess.market', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back to Calendar
                </a>
                @if($routine->assigned_to === Auth::id() && $routine->status === 'pending')
                <form action="{{ route('mess.market.complete', [$mess->id, $routine->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Mark as completed?')">
                        <i class="ti ti-check me-1"></i>Mark Complete
                    </button>
                </form>
                @endif
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row g-3">
            <div class="col-lg-8">
                <!-- Shopping List -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Items List</h6>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Purchased: {{ $items->where('purchased', true)->count() }} / {{ $items->count() }}</span>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                <i class="ti ti-plus me-1"></i>Add
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Est. Cost</th>
                                    <th>Actual Cost</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                <tr class="{{ $item->purchased ? 'table-success opacity-75' : '' }}">
                                    <td>
                                        <div class="fw-semibold {{ $item->purchased ? 'text-decoration-line-through' : '' }}">{{ $item->item_name }}</div>
                                        <small class="text-muted">By {{ $item->addedBy->name }}</small>
                                    </td>
                                    <td>{{ $item->quantity }} {{ $item->unit }}</td>
                                    <td>{{ $item->estimated_cost > 0 ? '৳'.number_format($item->estimated_cost, 2) : '-' }}</td>
                                    <td>
                                        @if($routine->assigned_to === Auth::id() || $member->canManage())
                                        <input type="number" class="form-control form-control-sm" style="width:100px"
                                            value="{{ $item->actual_cost }}" step="0.01" min="0"
                                            onchange="updateItem({{ $item->id }}, this.value, {{ $item->purchased ? 'true' : 'false' }})">
                                        @else
                                        {{ $item->actual_cost > 0 ? '৳'.number_format($item->actual_cost, 2) : '-' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($routine->assigned_to === Auth::id() || $member->canManage())
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" {{ $item->purchased ? 'checked' : '' }}
                                                onchange="updateItem({{ $item->id }}, {{ $item->actual_cost }}, this.checked)">
                                        </div>
                                        @else
                                        <span class="badge bg-{{ $item->purchased ? 'success' : 'secondary' }}">
                                            {{ $item->purchased ? 'Bought' : 'Pending' }}
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">No items added yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Summary Card -->
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Summary</h6></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Items</span>
                            <strong>{{ $items->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Purchased</span>
                            <strong class="text-success">{{ $items->where('purchased', true)->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Est. Total</span>
                            <strong>৳{{ number_format($items->sum('estimated_cost'), 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Actual Total</span>
                            <strong class="text-primary" id="actualTotal">৳{{ number_format($items->sum('actual_cost'), 2) }}</strong>
                        </div>
                    </div>
                </div>

                @if($routine->notes)
                <div class="card mt-3">
                    <div class="card-header"><h6 class="mb-0">Notes</h6></div>
                    <div class="card-body">
                        <p class="mb-0 text-muted">{{ $routine->notes }}</p>
                    </div>
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
                <h5 class="modal-title">Add Item to List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.market.list.add', [$mess->id, $routine->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Item Name <span class="text-danger">*</span></label>
                        <input type="text" name="item_name" class="form-control" required placeholder="e.g. Potato, Oil">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Quantity</label>
                            <input type="text" name="quantity" class="form-control" placeholder="e.g. 2">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit" class="form-control" placeholder="kg, litre, piece">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estimated Cost (৳)</label>
                        <input type="number" name="estimated_cost" class="form-control" step="0.01" min="0" placeholder="0.00">
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

function updateItem(itemId, actualCost, purchased) {
    fetch(`/mess/${messId}/market/list/${itemId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ actual_cost: actualCost, purchased: purchased })
    })
    .then(r => r.json())
    .then(d => {
        if (d.total !== undefined) {
            document.getElementById('actualTotal').textContent = '৳' + parseFloat(d.total).toFixed(2);
        }
    });
}
</script>
@endsection
