<?php $page = "admin-settings" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-settings me-2 text-primary"></i>System Settings</h4>
                <h6 class="text-muted">Global defaults and subscription plan management</h6>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row g-3">

            {{-- Default Limits --}}
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="ti ti-adjustments me-2"></i>Default Limits</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Default Member Limit <span class="text-danger">*</span></label>
                                <input type="number" name="default_max_members" min="1" max="1000"
                                    class="form-control @error('default_max_members') is-invalid @enderror"
                                    value="{{ old('default_max_members', $settings->default_max_members) }}">
                                <div class="form-text">Initial member limit for every new mess created.</div>
                                @error('default_max_members')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Default Mess Creation Limit <span class="text-danger">*</span></label>
                                <input type="number" name="default_max_messes" min="1" max="50"
                                    class="form-control @error('default_max_messes') is-invalid @enderror"
                                    value="{{ old('default_max_messes', $settings->default_max_messes) }}">
                                <div class="form-text">How many messes a user can create by default.</div>
                                @error('default_max_messes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-device-floppy me-1"></i>Save Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Add New Plan --}}
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="ti ti-plus me-2"></i>Add Subscription Plan</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.plans.store') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label fw-semibold small">Plan Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                    placeholder="e.g. Basic, Standard, Premium" value="{{ old('name') }}">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold small">Description</label>
                                <textarea name="description" class="form-control form-control-sm" rows="2"
                                    placeholder="Features or notes…">{{ old('description') }}</textarea>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label fw-semibold small">Max Members <span class="text-danger">*</span></label>
                                    <input type="number" name="max_members" min="1" max="1000"
                                        class="form-control form-control-sm @error('max_members') is-invalid @enderror"
                                        value="{{ old('max_members', 30) }}">
                                    @error('max_members')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold small">Price (৳) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" min="0" step="0.01"
                                        class="form-control form-control-sm @error('price') is-invalid @enderror"
                                        value="{{ old('price', 0) }}">
                                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold small">Duration (months) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration_months" min="1" max="24"
                                        class="form-control form-control-sm @error('duration_months') is-invalid @enderror"
                                        value="{{ old('duration_months', 1) }}">
                                    @error('duration_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold small">Sort Order</label>
                                    <input type="number" name="sort_order" min="0"
                                        class="form-control form-control-sm"
                                        value="{{ old('sort_order', 0) }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success w-100 btn-sm">
                                <i class="ti ti-plus me-1"></i>Create Plan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Existing Plans --}}
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0"><i class="ti ti-list me-2"></i>Subscription Plans</h6>
                        <span class="badge bg-secondary">{{ $plans->count() }}</span>
                    </div>
                    @if($plans->isEmpty())
                    <div class="card-body text-center text-muted py-5">
                        <i class="ti ti-package-off fs-2 d-block mb-2 opacity-50"></i>
                        No plans yet. Create one on the left.
                    </div>
                    @else
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($plans as $plan)
                            <div class="list-group-item px-3 py-2">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <strong class="small">{{ $plan->name }}</strong>
                                            @if($plan->is_active)
                                            <span class="badge bg-success" style="font-size:10px;">Active</span>
                                            @else
                                            <span class="badge bg-secondary" style="font-size:10px;">Inactive</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted">
                                            <i class="ti ti-users me-1"></i>{{ $plan->max_members }} members &nbsp;
                                            <i class="ti ti-currency-taka me-1"></i>৳{{ number_format($plan->price, 0) }}
                                            / {{ $plan->duration_months }} mo
                                        </div>
                                        @if($plan->description)
                                        <div class="small text-muted mt-1">{{ Str::limit($plan->description, 60) }}</div>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-xs btn-outline-primary" title="Edit"
                                            onclick="editPlan({{ $plan->id }}, {{ json_encode($plan) }})">
                                            <i class="ti ti-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST"
                                            onsubmit="return confirm('Delete plan \'{{ $plan->name }}\'?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
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
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Plan Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="ep_name" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea name="description" id="ep_description" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Max Members <span class="text-danger">*</span></label>
                            <input type="number" name="max_members" id="ep_max_members" min="1" max="1000" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Price (৳) <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="ep_price" min="0" step="0.01" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Duration (months) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_months" id="ep_duration_months" min="1" max="24" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Sort Order</label>
                            <input type="number" name="sort_order" id="ep_sort_order" min="0" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="ep_is_active" value="1">
                        <label class="form-check-label small" for="ep_is_active">Active (visible to mess owners)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-device-floppy me-1"></i>Save Changes</button>
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
    document.getElementById('ep_name').value           = plan.name;
    document.getElementById('ep_description').value   = plan.description || '';
    document.getElementById('ep_max_members').value   = plan.max_members;
    document.getElementById('ep_price').value          = plan.price;
    document.getElementById('ep_duration_months').value = plan.duration_months;
    document.getElementById('ep_sort_order').value    = plan.sort_order;
    document.getElementById('ep_is_active').checked   = !!plan.is_active;
    new bootstrap.Modal(document.getElementById('editPlanModal')).show();
}
</script>
@endpush
@endsection
