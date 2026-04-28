<?php $page = "admin-plans" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-packages me-2 text-primary"></i>Subscription Plans</h4>
                <h6 class="text-muted">Manage plans shown on the public pricing page</h6>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row g-4">

            {{-- Add New Plan --}}
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="ti ti-plus me-2"></i>Add New Plan</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.plans.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Plan Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    placeholder="e.g. Free, Standard, Premium" value="{{ old('name') }}">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Features <small class="text-muted fw-normal">(one per line — shown as bullets on landing page)</small></label>
                                <textarea name="description" class="form-control" rows="5"
                                    placeholder="Up to 20 members&#10;All core features&#10;Meal attendance&#10;Monthly reports">{{ old('description') }}</textarea>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Max Members <span class="text-danger">*</span></label>
                                    <input type="number" name="max_members" min="1" max="1000"
                                        class="form-control @error('max_members') is-invalid @enderror"
                                        value="{{ old('max_members', 30) }}">
                                    @error('max_members')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Price (৳) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" min="0" step="0.01"
                                        class="form-control @error('price') is-invalid @enderror"
                                        value="{{ old('price', 0) }}">
                                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Duration (months) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration_months" min="1" max="24"
                                        class="form-control @error('duration_months') is-invalid @enderror"
                                        value="{{ old('duration_months', 1) }}">
                                    @error('duration_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Sort Order</label>
                                    <input type="number" name="sort_order" min="0"
                                        class="form-control"
                                        value="{{ old('sort_order', 0) }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="ti ti-plus me-1"></i>Create Plan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Plans List --}}
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0"><i class="ti ti-list me-2"></i>All Plans</h6>
                        <span class="badge bg-secondary">{{ $plans->count() }} plans</span>
                    </div>
                    @if($plans->isEmpty())
                    <div class="card-body text-center text-muted py-5">
                        <i class="ti ti-package-off fs-1 d-block mb-2 opacity-50"></i>
                        <p>No subscription plans yet. Create one on the left.</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Plan') }}</th>
                                    <th class="text-center">Members</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Duration</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Featured</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plans as $i => $plan)
                                <tr>
                                    <td class="text-muted small">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $plan->name }}</div>
                                        @if($plan->description)
                                        <div class="small text-muted">{{ Str::limit($plan->description, 60) }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark">
                                            <i class="ti ti-users me-1"></i>{{ $plan->max_members }}
                                        </span>
                                    </td>
                                    <td class="text-center fw-semibold text-success">৳{{ number_format($plan->price, 0) }}</td>
                                    <td class="text-center text-muted small">{{ $plan->duration_months }} month{{ $plan->duration_months > 1 ? 's' : '' }}</td>
                                    <td class="text-center">
                                        @if($plan->is_active)
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($plan->is_featured)
                                        <span class="badge bg-warning text-dark"><i class="ti ti-star-filled me-1"></i>Popular</span>
                                        @else
                                        <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button class="btn btn-sm btn-outline-primary" title="Edit"
                                                onclick="editPlan({{ $plan->id }}, {{ json_encode($plan) }})">
                                                <i class="ti ti-pencil"></i>
                                            </button>
                                            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST"
                                                onsubmit="return confirm('Delete plan \'{{ $plan->name }}\'?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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
            </div>

        </div>{{-- /row --}}
    </div>
</div>

{{-- Edit Plan Modal --}}
<div class="modal fade" id="editPlanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-pencil me-2"></i>Edit Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPlanForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Plan Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="ep_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Features <small class="text-muted fw-normal">(one per line)</small></label>
                        <textarea name="description" id="ep_description" class="form-control" rows="5"
                            placeholder="Up to 20 members&#10;All core features&#10;Monthly reports"></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Max Members <span class="text-danger">*</span></label>
                            <input type="number" name="max_members" id="ep_max_members" min="1" max="1000" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Price (৳) <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="ep_price" min="0" step="0.01" class="form-control" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Duration (months) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_months" id="ep_duration_months" min="1" max="24" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" id="ep_sort_order" min="0" class="form-control">
                        </div>
                    </div>
                    <div class="d-flex gap-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="ep_is_active" value="1">
                            <label class="form-check-label fw-semibold" for="ep_is_active">Active</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="ep_is_featured" value="1">
                            <label class="form-check-label fw-semibold" for="ep_is_featured">
                                <i class="ti ti-star text-warning me-1"></i>Featured / Popular
                                <small class="text-muted fw-normal">(highlights this plan on landing page)</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editPlan(id, plan) {
    const form = document.getElementById('editPlanForm');
    form.action = '/admin/plans/' + id;
    document.getElementById('ep_name').value             = plan.name;
    document.getElementById('ep_description').value     = plan.description || '';
    document.getElementById('ep_max_members').value     = plan.max_members;
    document.getElementById('ep_price').value           = plan.price;
    document.getElementById('ep_duration_months').value = plan.duration_months;
    document.getElementById('ep_sort_order').value      = plan.sort_order;
    document.getElementById('ep_is_active').checked     = !!plan.is_active;
    document.getElementById('ep_is_featured').checked   = !!plan.is_featured;
    new bootstrap.Modal(document.getElementById('editPlanModal')).show();
}
</script>
@endpush
@endsection
