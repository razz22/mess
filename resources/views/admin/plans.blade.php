<?php $page = "admin-plans" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-packages me-2 text-primary"></i>Subscription Plans</h4>
                <h6 class="text-muted">Create and manage subscription plans for mess owners</h6>
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
                                    placeholder="e.g. Basic, Standard, Premium" value="{{ old('name') }}">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" class="form-control" rows="2"
                                    placeholder="Short tagline for this plan">{{ old('description') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Features <span class="text-muted fw-normal">(one per line)</span></label>
                                <textarea name="features" class="form-control" rows="4"
                                    placeholder="All Free features&#10;Priority support&#10;Advanced reports&#10;Export to PDF/Excel">{{ old('features') }}</textarea>
                                <div class="form-text">Each line becomes a bullet point on the landing page pricing card.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Button Label</label>
                                <input type="text" name="button_label" class="form-control"
                                    placeholder="e.g. Start Standard, Go Premium"
                                    value="{{ old('button_label') }}">
                                <div class="form-text">Leave blank to auto-generate from plan name.</div>
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
                                    <input type="number" name="price" min="1" step="0.01"
                                        class="form-control @error('price') is-invalid @enderror"
                                        value="{{ old('price', 299) }}">
                                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
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
                            <div class="mb-4 form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
                                    {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_featured">
                                    Mark as Popular <span class="text-muted fw-normal">(shows "Popular" badge)</span>
                                </label>
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
                                    <th>Plan</th>
                                    <th class="text-center">Members</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Duration</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Sort</th>
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
                                    <td class="text-center text-muted small">{{ $plan->sort_order }}</td>
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
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="ep_description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Features <span class="text-muted fw-normal">(one per line)</span></label>
                        <textarea name="features" id="ep_features" class="form-control" rows="4"
                            placeholder="All Free features&#10;Priority support&#10;Advanced reports"></textarea>
                        <div class="form-text">Each line becomes a bullet point on the landing page.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Button Label</label>
                        <input type="text" name="button_label" id="ep_button_label" class="form-control"
                            placeholder="e.g. Start Standard, Go Premium">
                        <div class="form-text">Leave blank to auto-generate from plan name.</div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Max Members <span class="text-danger">*</span></label>
                            <input type="number" name="max_members" id="ep_max_members" min="1" max="1000" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Price (৳) <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="ep_price" min="1" step="0.01" class="form-control" required>
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
                    <div class="mb-2 form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="ep_is_featured" value="1">
                        <label class="form-check-label fw-semibold" for="ep_is_featured">Mark as Popular</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="ep_is_active" value="1">
                        <label class="form-check-label fw-semibold" for="ep_is_active">Active (visible on landing page)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Save Changes</button>
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
    document.getElementById('ep_features').value        = Array.isArray(plan.features) ? plan.features.join('\n') : (plan.features || '');
    document.getElementById('ep_button_label').value    = plan.button_label || '';
    document.getElementById('ep_max_members').value     = plan.max_members;
    document.getElementById('ep_price').value           = plan.price;
    document.getElementById('ep_duration_months').value = plan.duration_months;
    document.getElementById('ep_sort_order').value      = plan.sort_order;
    document.getElementById('ep_is_featured').checked   = !!plan.is_featured;
    document.getElementById('ep_is_active').checked     = !!plan.is_active;
    new bootstrap.Modal(document.getElementById('editPlanModal')).show();
}
</script>
@endpush
@endsection
