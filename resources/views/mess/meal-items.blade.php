<?php $page = "mess-meal-items" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">Meal Items — {{ $mess->name }}</h4>
                <h6>Kanban board for meal planning</h6>
            </div>
            <div class="page-btn d-flex gap-2">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="ti ti-circle-plus me-1"></i>Add Item
                </button>
                <a href="{{ route('mess.meals', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Attendance
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <!-- Kanban Board -->
        <div class="row g-3">
            @foreach(['todo' => ['label' => 'To Do', 'color' => 'secondary', 'icon' => 'ti-list'], 'in_progress' => ['label' => 'Cooking', 'color' => 'warning', 'icon' => 'ti-flame'], 'done' => ['label' => 'Done', 'color' => 'success', 'icon' => 'ti-check']] as $status => $config)
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg-{{ $config['color'] }}-subtle d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti {{ $config['icon'] }} text-{{ $config['color'] }}"></i>
                            <h6 class="mb-0 fw-bold">{{ $config['label'] }}</h6>
                        </div>
                        <span class="badge bg-{{ $config['color'] }}">{{ $items[$status]->count() }}</span>
                    </div>
                    <div class="card-body p-2" id="kanban-{{ $status }}" style="min-height: 300px;">
                        @forelse($items[$status] as $item)
                        <div class="card mb-2 kanban-item" data-id="{{ $item->id }}">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold small">{{ $item->name }}</div>
                                        @if($item->description)
                                        <div class="text-muted" style="font-size:11px">{{ $item->description }}</div>
                                        @endif
                                        <div class="mt-1">
                                            <span class="badge bg-light text-dark" style="font-size:10px">
                                                <i class="ti ti-tag me-1"></i>{{ ucfirst($item->category) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-xs btn-outline-secondary" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @if($status !== 'todo')
                                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="moveItem({{ $item->id }}, 'todo')">
                                                <i class="ti ti-list me-1"></i>Move to To Do
                                            </a></li>
                                            @endif
                                            @if($status !== 'in_progress')
                                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="moveItem({{ $item->id }}, 'in_progress')">
                                                <i class="ti ti-flame me-1"></i>Move to Cooking
                                            </a></li>
                                            @endif
                                            @if($status !== 'done')
                                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="moveItem({{ $item->id }}, 'done')">
                                                <i class="ti ti-check me-1"></i>Mark Done
                                            </a></li>
                                            @endif
                                            @if($member->canManage())
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('mess.meal-items.destroy', [$mess->id, $item->id]) }}" method="POST" onsubmit="return confirm('Delete?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="ti ti-trash me-1"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-1 pt-1 border-top d-flex justify-content-between">
                                    <small class="text-muted">{{ $item->createdBy->name }}</small>
                                    <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4" style="font-size:12px">
                            <i class="ti ti-inbox fs-3 d-block mb-1"></i>No items
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Meal Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.meal-items.store', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Item Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Rice, Chicken Curry" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="general">General</option>
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                            <option value="snack">Snack</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Optional notes"></textarea>
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

function moveItem(itemId, newStatus) {
    fetch(`/mess/${messId}/meal-items/${itemId}/status`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ status: newStatus })
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); })
    .catch(e => alert('Error: ' + e));
}
</script>
@endsection
