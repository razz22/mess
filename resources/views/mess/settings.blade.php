<?php $page = "mess-settings" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">Mess Settings — {{ $mess->name }}</h4>
                <h6>Configure your mess preferences</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('mess.dashboard', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <form action="{{ route('mess.update', $mess->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="row g-3">
                <!-- Basic Info -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header"><h6 class="mb-0"><i class="ti ti-info-circle me-2"></i>Basic Info</h6></div>
                        <div class="card-body">
                            <div class="mb-3 text-center">
                                <div class="avatar avatar-xxl mb-2">
                                    @if($mess->avatar)
                                    <img src="{{ asset('storage/'.$mess->avatar) }}" class="img-fluid rounded-circle" id="avatarImg" alt="">
                                    @else
                                    <span class="avatar-title rounded-circle bg-primary text-white fs-2" id="avatarPlaceholder">
                                        {{ strtoupper(substr($mess->name, 0, 1)) }}
                                    </span>
                                    <img src="" class="img-fluid rounded-circle d-none" id="avatarImg" alt="">
                                    @endif
                                </div>
                                <label class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-upload me-1"></i>Change Logo
                                    <input type="file" name="avatar" accept="image/*" class="d-none" onchange="previewAvatar(this)">
                                </label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mess Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $mess->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $mess->description) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $mess->address) }}">
                            </div>

                            <!-- Invite Code -->
                            <div class="p-3 bg-light rounded">
                                <label class="form-label fw-semibold mb-1">Invite Code</label>
                                <div class="d-flex align-items-center gap-2">
                                    <code class="fs-5 fw-bold">{{ $mess->invite_code }}</code>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="navigator.clipboard.writeText('{{ $mess->invite_code }}');this.textContent='Copied!'">
                                        <i class="ti ti-copy me-1"></i>Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meal Settings -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header"><h6 class="mb-0"><i class="ti ti-tools-kitchen-2 me-2"></i>Meal Settings</h6></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Meal Cost Calculation Mode</label>
                                <div class="d-flex flex-column gap-2 mt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="meal_cost_mode" id="modeMonthly" value="monthly"
                                            {{ ($settings->meal_cost_mode ?? 'monthly') === 'monthly' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="modeMonthly">
                                            <span class="fw-semibold">Monthly</span>
                                            <div class="text-muted small">Total monthly expenses ÷ total meals = per meal rate</div>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="meal_cost_mode" id="modeDaily" value="daily"
                                            {{ ($settings->meal_cost_mode ?? 'monthly') === 'daily' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="modeDaily">
                                            <span class="fw-semibold">Daily</span>
                                            <div class="text-muted small">Each day's market expenses ÷ that day's total meals = daily meal rate</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="allow_meal_off" id="allowMealOff"
                                        {{ ($settings->allow_meal_off ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allowMealOff">Allow members to mark meal OFF</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="auto_meal_on" id="autoMealOn"
                                        {{ ($settings->auto_meal_on ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="autoMealOn">Auto mark all members ON (default)</label>
                                </div>
                                <div class="form-text">If enabled, all members are marked ON by default each day</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i>Save Settings
                </button>
            </div>
        </form>

        <!-- Danger Zone -->
        <div class="card mt-4 border-danger">
            <div class="card-header bg-danger-subtle">
                <h6 class="mb-0 text-danger"><i class="ti ti-alert-triangle me-2"></i>Danger Zone</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">Delete This Mess</div>
                        <div class="text-muted small">This will permanently delete the mess and all its data.</div>
                    </div>
                    <form action="{{ route('mess.destroy', $mess->id) }}" method="POST"
                        onsubmit="return confirm('Are you sure? This cannot be undone!')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="ti ti-trash me-1"></i>Delete Mess
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('avatarImg');
            img.src = e.target.result;
            img.classList.remove('d-none');
            const ph = document.getElementById('avatarPlaceholder');
            if (ph) ph.classList.add('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
