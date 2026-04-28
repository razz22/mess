<?php $page = "mess-settings" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">{{ __('Mess Settings') }} — {{ $mess->name }}</h4>
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
                                <label class="form-label fw-semibold">{{ __('Description') }}</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $mess->description) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Address') }}</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $mess->address) }}">
                            </div>

                            <!-- Invite Code -->
                            <div class="p-3 bg-light rounded">
                                <label class="form-label fw-semibold mb-1">{{ __('Invite Code') }}</label>
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
                        <div class="card-header"><h6 class="mb-0"><i class="ti ti-tools-kitchen-2 me-2"></i>{{ __('Meal Settings') }}</h6></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Meal Cost Calculation Mode') }}</label>
                                <div class="d-flex flex-column gap-2 mt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="meal_cost_mode" id="modeMonthly" value="monthly"
                                            {{ ($settings->meal_cost_mode ?? 'monthly') === 'monthly' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="modeMonthly">
                                            <span class="fw-semibold">{{ __('Monthly') }}</span>
                                            <div class="text-muted small">Total monthly expenses ÷ total meals = per meal rate</div>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="meal_cost_mode" id="modeDaily" value="daily"
                                            {{ ($settings->meal_cost_mode ?? 'monthly') === 'daily' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="modeDaily">
                                            <span class="fw-semibold">{{ __('Daily') }}</span>
                                            <div class="text-muted small">Each day's market expenses ÷ that day's total meals = daily meal rate</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="allow_meal_off" id="allowMealOff"
                                        {{ ($settings->allow_meal_off ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allowMealOff">{{ __('Allow members to mark meal OFF') }}</label>
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

                    {{-- Leave Settings --}}
                    <div class="card mb-4">
                        <div class="card-header"><h6 class="mb-0"><i class="ti ti-logout me-2 text-danger"></i>{{ __('Leave Settings') }}</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ __('Leave Notice Period') }}</label>
                                    <div class="input-group" style="max-width:220px;">
                                        <input type="number" name="leave_notice_months" class="form-control"
                                            min="1" max="6" value="{{ $mess->leave_notice_months ?? 1 }}">
                                        <span class="input-group-text">month(s)</span>
                                    </div>
                                    <div class="form-text">Minimum months of notice a member must give before their last date. e.g. 1 = must give notice by end of current month.</div>
                                </div>
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

        <!-- Phone Book -->
        @if(session('contact_success'))
        <div class="alert alert-success alert-dismissible fade show mt-3">{{ session('contact_success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="ti ti-address-book me-2 text-success"></i>{{ __('Phone Book') }}</h6>
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addContactModal">
                    <i class="ti ti-plus me-1"></i>Add Contact
                </button>
            </div>
            @if($contacts->isEmpty())
            <div class="card-body text-center text-muted py-4">
                <i class="ti ti-address-book fs-2 d-block mb-2 opacity-30"></i>
                No contacts yet. Add your cook or other contacts.
            </div>
            @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>Phone (WhatsApp)</th>
                            <th>{{ __('Notes') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contacts as $c)
                        <tr>
                            <td class="fw-semibold">{{ $c->name }}</td>
                            <td><i class="ti ti-brand-whatsapp text-success me-1"></i>{{ $c->phone }}</td>
                            <td class="text-muted small">{{ $c->notes ?? '—' }}</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-outline-primary py-0"
                                    onclick="openEditContact({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ $c->phone }}', '{{ addslashes($c->notes ?? '') }}')">
                                    <i class="ti ti-edit" style="font-size:11px"></i>
                                </button>
                                <form action="{{ route('mess.contacts.destroy', [$mess->id, $c->id]) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger py-0 ms-1"
                                        onclick="return confirm('Delete this contact?')">
                                        <i class="ti ti-trash" style="font-size:11px"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Danger Zone -->
        <div class="card mt-4 border-danger">
            <div class="card-header bg-danger-subtle">
                <h6 class="mb-0 text-danger"><i class="ti ti-alert-triangle me-2"></i>{{ __('Danger Zone') }}</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ __('Delete This Mess') }}</div>
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
{{-- Add Contact Modal --}}
<div class="modal fade" id="addContactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-address-book me-2"></i>{{ __('Add Contact') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.contacts.store', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="label" value="contact">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required maxlength="100" placeholder="e.g. Rahim, Cook, Supplier…">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">WhatsApp Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" required maxlength="20"
                            placeholder="e.g. 8801XXXXXXXXX (with country code)">
                        <div class="form-text">Include country code without + (e.g. 8801712345678 for Bangladesh)</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">{{ __('Notes') }}</label>
                        <input type="text" name="notes" class="form-control" maxlength="255" placeholder="Optional note">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success"><i class="ti ti-check me-1"></i>{{ __('Save Contact') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Contact Modal --}}
<div class="modal fade" id="editContactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-edit me-2"></i>{{ __('Edit Contact') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editContactForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="label" value="contact">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="ecName" class="form-control" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">WhatsApp Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="ecPhone" class="form-control" required maxlength="20">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">{{ __('Notes') }}</label>
                        <input type="text" name="notes" id="ecNotes" class="form-control" maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>{{ __('Update') }}</button>
                </div>
            </form>
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

function openEditContact(id, name, phone, notes) {
    const baseUrl = '{{ url("mess/" . $mess->id . "/contacts") }}';
    document.getElementById('editContactForm').action = baseUrl + '/' + id;
    document.getElementById('ecName').value  = name;
    document.getElementById('ecPhone').value = phone;
    document.getElementById('ecNotes').value = notes;
    new bootstrap.Modal(document.getElementById('editContactModal')).show();
}
</script>
@endsection
