<?php $page = "mess-market-list" ?>
@extends('layout.mainlayout')
@section('content')
@php
    $isOwner      = $member && $member->role === 'owner';
    $isSuperAdmin = Auth::user()->is_super_admin;
    $isManager    = $member && in_array($member->role, ['owner', 'manager']);
    $isAssigned   = $routine->assigned_to === Auth::id();
    $status       = $routine->status; // pending | approved | pending_reapproval | completed(legacy)
    $isApproved   = in_array($status, ['approved', 'completed']);
    $needsReapproval = $status === 'pending_reapproval';
    $needsApproval   = in_array($status, ['pending', 'pending_reapproval']);

    // Edit cost/purchased: approved→only owner/super; otherwise manager or assigned member
    $canEdit = $isApproved
        ? ($isSuperAdmin || $isOwner)
        : ($isManager || $isAssigned);

    // Add items: assigned member or manager always can; adding to approved triggers re-approval
    $canAdd = $isManager || $isAssigned || $isSuperAdmin;

    // Approve button: only manager/owner, when status needs approval
    $canApprove = ($isManager || $isSuperAdmin) && $needsApproval;
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
                    @php
                        $badgeColor = match($status) {
                            'approved','completed' => 'success',
                            'pending_reapproval'   => 'danger',
                            default                => 'warning',
                        };
                        $badgeLabel = match($status) {
                            'approved','completed' => 'Approved',
                            'pending_reapproval'   => 'Needs Re-approval',
                            default                => 'Pending Approval',
                        };
                    @endphp
                    <span class="badge bg-{{ $badgeColor }} ms-2">{{ $badgeLabel }}</span>
                </h6>
            </div>
            <div class="page-btn d-flex gap-2">
                @if($canApprove)
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="ti ti-circle-check me-1"></i>{{ $needsReapproval ? 'Re-approve' : 'Approve' }}
                </button>
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
                            <strong id="itemCount">{{ $items->count() }}</strong> items
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th style="width:90px">Qty</th>
                                    <th class="text-end" style="width:110px">Cost (৳)</th>
                                    <th style="width:70px"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsTbody">
                                @forelse($items as $item)
                                @php
                                    $itemCanEdit = $isManager || $isSuperAdmin || (!$item->is_approved);
                                    $itemCanDelete = $isManager || $isSuperAdmin || (!$item->is_approved && ($item->added_by === Auth::id() || $isAssigned));
                                @endphp
                                <tr id="row-{{ $item->id }}">
                                    {{-- View mode --}}
                                    <td id="view-name-{{ $item->id }}">
                                        <div class="fw-semibold">{{ $item->item_name }}</div>
                                        <small class="text-muted d-flex align-items-center gap-1">
                                            {{ $item->addedBy->name }}
                                            @if($item->is_approved)
                                            <span class="badge bg-success-subtle text-success" style="font-size:9px;">approved</span>
                                            @else
                                            <span class="badge bg-warning-subtle text-warning" style="font-size:9px;">new</span>
                                            @endif
                                        </small>
                                    </td>
                                    <td id="view-qty-{{ $item->id }}">
                                        <span class="text-nowrap small">{{ $item->quantity }}{{ $item->unit ? ' '.$item->unit : '' }}</span>
                                    </td>
                                    <td class="text-end" id="view-cost-{{ $item->id }}">
                                        <span class="fw-semibold">{{ $item->actual_cost > 0 ? '৳'.number_format($item->actual_cost,2) : '—' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            @if($itemCanEdit)
                                            <button class="btn btn-xs btn-outline-primary py-0 px-1" onclick="startEdit({{ $item->id }}, '{{ addslashes($item->item_name) }}', '{{ $item->quantity }}', '{{ $item->unit }}', {{ $item->actual_cost }})" title="Edit">
                                                <i class="ti ti-pencil" style="font-size:12px"></i>
                                            </button>
                                            @endif
                                            @if($itemCanDelete)
                                            <button class="btn btn-xs btn-outline-danger py-0 px-1" onclick="deleteItem({{ $item->id }}, '{{ addslashes($item->item_name) }}')" title="Delete">
                                                <i class="ti ti-trash" style="font-size:12px"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                {{-- Edit mode (hidden) --}}
                                <tr id="edit-row-{{ $item->id }}" class="d-none table-warning">
                                    <td>
                                        <input type="text" class="form-control form-control-sm" id="edit-name-{{ $item->id }}" value="{{ $item->item_name }}" placeholder="Item name">
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <input type="text" class="form-control form-control-sm" id="edit-qty-{{ $item->id }}" value="{{ $item->quantity }}" placeholder="Qty" style="width:50px">
                                            <input type="text" class="form-control form-control-sm" id="edit-unit-{{ $item->id }}" value="{{ $item->unit }}" placeholder="Unit" style="width:45px">
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <input type="number" class="form-control form-control-sm text-end" id="edit-cost-{{ $item->id }}" value="{{ $item->actual_cost }}" step="0.01" min="0" style="width:95px">
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button class="btn btn-xs btn-success py-0 px-1" onclick="saveEdit({{ $item->id }})" title="Save">
                                                <i class="ti ti-check" style="font-size:12px"></i>
                                            </button>
                                            <button class="btn btn-xs btn-secondary py-0 px-1" onclick="cancelEdit({{ $item->id }})" title="Cancel">
                                                <i class="ti ti-x" style="font-size:12px"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr id="emptyRow"><td colspan="4" class="text-center text-muted py-4">
                                    <i class="ti ti-shopping-cart-off fs-3 d-block mb-2 opacity-30"></i>
                                    No items yet. Add an item below.
                                </td></tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="2">Total</td>
                                    <td class="text-end" id="actualTotal">৳{{ number_format($items->sum('actual_cost'),2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Inline Add Form --}}
                    @if($canAdd)
                    <div class="card-footer bg-light border-top">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ti ti-plus text-primary"></i>
                            <span class="fw-semibold small text-primary">Add Item</span>
                        </div>
                        <form id="addItemForm" onsubmit="submitAddItem(event)">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-sm-5">
                                    <input type="text" id="new_name" class="form-control form-control-sm" placeholder="Item name *" required>
                                </div>
                                <div class="col-5 col-sm-2">
                                    <input type="text" id="new_qty" class="form-control form-control-sm" placeholder="Qty">
                                </div>
                                <div class="col-4 col-sm-2">
                                    <input type="text" id="new_unit" class="form-control form-control-sm" placeholder="Unit">
                                </div>
                                <div class="col-6 col-sm-2">
                                    <input type="number" id="new_cost" class="form-control form-control-sm" placeholder="Cost ৳" step="0.01" min="0">
                                </div>
                                <div class="col-6 col-sm-1">
                                    <button type="submit" class="btn btn-primary btn-sm w-100" id="addBtn">
                                        <i class="ti ti-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                @php
                    $totalItems = $items->count();
                    $grandTotal = $items->sum('actual_cost');
                @endphp

                <!-- Summary Card -->
                <div class="card mb-3" style="border:none;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                    <div class="card-body p-0">
                        <!-- Header strip -->
                        <div style="background:linear-gradient(135deg,#206bc4,#1a55a0);border-radius:8px 8px 0 0;padding:16px 20px;color:#fff;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="ti ti-shopping-cart fs-5"></i>
                                <span class="fw-bold" style="font-size:14px;">Shopping Summary</span>
                            </div>
                            <div style="font-size:11px;opacity:.8;">{{ $routine->start_date->format('d M Y') }} · {{ $routine->assignedTo->name }}</div>
                        </div>

                        <!-- Total amount -->
                        <div style="padding:16px 20px 10px;border-bottom:1px solid #f1f3f5;">
                            <div class="text-muted small mb-1">Total Spent</div>
                            <div class="fw-bold text-primary" style="font-size:26px;" id="actualTotalCard">৳{{ number_format($grandTotal, 2) }}</div>
                        </div>

                        <!-- Items count -->
                        <div style="padding:12px 20px;border-bottom:1px solid #f1f3f5;">
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Total Items</span>
                                <span class="fw-semibold">{{ $totalItems }}</span>
                            </div>
                        </div>

                        <!-- Status badge -->
                        <div style="padding:12px 20px;">
                            @if($isApproved)
                            <span class="badge bg-success w-100 py-2" style="font-size:12px;"><i class="ti ti-circle-check me-1"></i>Approved</span>
                            @elseif($needsReapproval)
                            <span class="badge bg-danger w-100 py-2" style="font-size:12px;"><i class="ti ti-alert-circle me-1"></i>New Items Added — Awaiting Re-approval</span>
                            @else
                            <span class="badge bg-warning text-dark w-100 py-2" style="font-size:12px;"><i class="ti ti-clock me-1"></i>Awaiting Approval</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($routine->notes)
                <div class="card" style="border:none;box-shadow:0 2px 12px rgba(0,0,0,.06);">
                    <div class="card-header bg-light border-0 py-2">
                        <h6 class="mb-0 small fw-bold text-muted"><i class="ti ti-notes me-1"></i>Notes</h6>
                    </div>
                    <div class="card-body py-2"><p class="mb-0 text-muted small">{{ $routine->notes }}</p></div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
const messId  = {{ $mess->id }};
const baseUrl = '{{ rtrim(url('/'), '/') }}';
const csrf    = document.querySelector('meta[name="csrf-token"]')?.content;
const addUrl  = '{{ route('mess.market.list.add', [$mess->id, $routine->id]) }}';

function setTotal(total) {
    const fmt = '৳' + parseFloat(total).toFixed(2);
    ['actualTotal','actualTotalCard'].forEach(id => { const el = document.getElementById(id); if (el) el.textContent = fmt; });
}

function updateItem(itemId, data) {
    fetch(baseUrl + `/mess/${messId}/market/list/${itemId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify(data),
    }).then(r => r.json()).then(d => { if (d.total !== undefined) setTotal(d.total); });
}


// ---- Inline Add ----
function submitAddItem(e) {
    e.preventDefault();
    const name = document.getElementById('new_name').value.trim();
    if (!name) { document.getElementById('new_name').focus(); return; }
    const btn = document.getElementById('addBtn');
    btn.disabled = true; btn.innerHTML = '<i class="ti ti-loader-2 ti-spin"></i>';

    const formData = new FormData();
    formData.append('_token', csrf);
    formData.append('items[0][item_name]', name);
    formData.append('items[0][quantity]', document.getElementById('new_qty').value.trim());
    formData.append('items[0][unit]', document.getElementById('new_unit').value.trim());
    formData.append('items[0][actual_cost]', document.getElementById('new_cost').value || 0);
    formData.append('items[0][assigned_to]', '{{ $routine->assigned_to }}');
    formData.append('items[0][expense_date]', '{{ $routine->start_date->format('Y-m-d') }}');

    fetch(addUrl, { method: 'POST', body: formData })
        .then(r => { if (!r.ok) throw new Error(); return r; })
        .then(() => window.location.reload())
        .catch(() => { btn.disabled = false; btn.innerHTML = '<i class="ti ti-plus"></i>'; alert('Error adding item.'); });
}

// ---- Edit ----
function startEdit(id) {
    document.getElementById('row-' + id).classList.add('d-none');
    document.getElementById('edit-row-' + id).classList.remove('d-none');
    document.getElementById('edit-name-' + id).focus();
}

function cancelEdit(id) {
    document.getElementById('edit-row-' + id).classList.add('d-none');
    document.getElementById('row-' + id).classList.remove('d-none');
}

function saveEdit(id) {
    const name = document.getElementById('edit-name-' + id).value.trim();
    if (!name) { document.getElementById('edit-name-' + id).focus(); return; }
    const qty  = document.getElementById('edit-qty-' + id).value.trim();
    const unit = document.getElementById('edit-unit-' + id).value.trim();
    const cost = parseFloat(document.getElementById('edit-cost-' + id).value) || 0;

    fetch(baseUrl + `/mess/${messId}/market/list/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ item_name: name, quantity: qty, unit: unit, actual_cost: cost }),
    }).then(r => r.json()).then(d => {
        if (d.total !== undefined) setTotal(d.total);
        document.getElementById('view-name-' + id).querySelector('.fw-semibold').textContent = name;
        document.getElementById('view-qty-' + id).querySelector('span').textContent = qty + (unit ? ' ' + unit : '');
        document.getElementById('view-cost-' + id).querySelector('span').textContent = cost > 0 ? '৳' + cost.toFixed(2) : '—';
        cancelEdit(id);
    });
}

// ---- Delete ----
var _deleteTargetId = null;
function deleteItem(id, name) {
    _deleteTargetId = id;
    document.getElementById('deleteItemName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteItemModal')).show();
}

function confirmDeleteItem() {
    const id = _deleteTargetId;
    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteItemModal'));
    modal.hide();
    const fd = new FormData();
    fd.append('_token', csrf);
    fd.append('_method', 'DELETE');
    fetch(baseUrl + `/mess/${messId}/market/list/${id}`, {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        body: fd,
    }).then(r => {
        if (!r.ok) {
            r.text().then(t => console.error('Delete failed:', r.status, t));
            alert('Could not delete item (server error ' + r.status + ').');
            return;
        }
        return r.json();
    }).then(d => {
        if (!d) return;
        if (d.error) { alert(d.error); return; }
        document.getElementById('row-' + id)?.remove();
        document.getElementById('edit-row-' + id)?.remove();
        if (d.total !== undefined) setTotal(d.total);
        const remaining = document.getElementById('itemsTbody').querySelectorAll('tr[id^="row-"]').length;
        const countEl = document.getElementById('itemCount'); if (countEl) countEl.textContent = remaining;
        if (remaining === 0 && !document.getElementById('emptyRow')) {
            const tr = document.createElement('tr'); tr.id = 'emptyRow';
            tr.innerHTML = '<td colspan="4" class="text-center text-muted py-4"><i class="ti ti-shopping-cart-off fs-3 d-block mb-2 opacity-30"></i>No items yet.</td>';
            document.getElementById('itemsTbody').appendChild(tr);
        }
    }).catch(err => { console.error('Delete error:', err); alert('Delete failed. Please try again.'); });
}
</script>

{{-- Approve confirmation modal --}}
@if($canApprove)
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
        <div class="modal-content">
            <div class="modal-body text-center py-4 px-4">
                <div class="mb-3">
                    <span class="avatar avatar-lg bg-success-subtle rounded-circle">
                        <i class="ti ti-circle-check text-success fs-3"></i>
                    </span>
                </div>
                <h5 class="fw-bold mb-2">{{ $needsReapproval ? 'Re-approve List?' : 'Approve Shopping List?' }}</h5>
                <p class="text-muted small mb-0">
                    {{ $needsReapproval
                        ? 'New items have been added. Re-approving will update the recorded expense.'
                        : 'Approve this shopping list and create a market expense record.' }}
                </p>
                <div class="mt-3 p-3 rounded" style="background:#f8f9fa;font-size:13px;">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Assigned to</span>
                        <strong>{{ $routine->assignedTo->name }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Date</span>
                        <strong>{{ $routine->start_date->format('d M Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Amount</span>
                        <strong class="text-success">৳{{ number_format($grandTotal, 2) }}</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4 gap-2">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('mess.market.complete', [$mess->id, $routine->id]) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success px-4">
                        <i class="ti ti-circle-check me-1"></i>Yes, {{ $needsReapproval ? 'Re-approve' : 'Approve' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Delete confirmation modal --}}
<div class="modal fade" id="deleteItemModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <span class="avatar avatar-lg bg-danger-subtle rounded-circle">
                        <i class="ti ti-trash text-danger fs-3"></i>
                    </span>
                </div>
                <h6 class="fw-bold mb-1">Delete Item?</h6>
                <p class="text-muted small mb-0"><strong id="deleteItemName"></strong> will be permanently removed.</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4 gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">No, Keep</button>
                <button type="button" class="btn btn-danger btn-sm px-4" onclick="confirmDeleteItem()">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection
