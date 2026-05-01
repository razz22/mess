<?php $page = "mess-members" ?>
@extends('layout.mainlayout')
@section('content')
@php
    $isManager = Auth::user()->isManagerOf($mess->id);
    $isOwner   = $mess->owner_id === Auth::id() || Auth::user()->is_super_admin;
@endphp
<div class="page-wrapper">
    <div class="content">
        @php $customSub = \App\Models\CustomSubscription::active()->forMess($mess->id)->first(); @endphp
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">{{ __('Members') }} — {{ $mess->name }}</h4>
                <h6 class="d-flex align-items-center gap-2">
                    {{ $members->count() }} / {{ $mess->getEffectiveMaxMembers() }} {{ __('members') }}
                    @if($customSub)
                    <span class="badge d-inline-flex align-items-center gap-1" style="background:#6366f1;font-size:11px;">
                        <i class="ti ti-star" style="font-size:10px;"></i>{{ $customSub->label }}
                    </span>
                    @endif
                </h6>
            </div>
            <div class="page-btn d-flex gap-2">
                <a href="{{ route('mess.dashboard', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>{{ __('Back') }}
                </a>
                @if($members->count() < $mess->getEffectiveMaxMembers() && $isManager)
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="ti ti-user-plus me-1"></i>{{ __('Add Member') }}
                </button>
                @endif
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Invite Code Banner -->
        <div class="card mb-4 border-primary">
            <div class="card-body py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <i class="ti ti-link me-2 text-primary"></i>
                    <span>{{ __('Share invite code') }}: <strong class="fs-5 font-monospace">{{ $mess->invite_code }}</strong> {{ __('for members to join') }}</span>
                </div>
                <button class="btn btn-sm btn-outline-primary" onclick="navigator.clipboard.writeText('{{ $mess->invite_code }}');this.innerHTML='<i class=\'ti ti-check me-1\'></i>{{ __('Copied!') }}'">
                    <i class="ti ti-copy me-1"></i>{{ __('Copy Code') }}
                </button>
            </div>
        </div>

        <!-- Member Cards -->
        <div class="row g-3">
            @foreach($members as $m)
            @php
                $u = $m->user;
                $roleColor = match($m->role) { 'owner'=>'danger','manager'=>'warning','author'=>'info', default=>'secondary' };
                $bgMap = ['A+'=>'danger','A-'=>'danger','B+'=>'primary','B-'=>'primary','AB+'=>'purple','AB-'=>'purple','O+'=>'success','O-'=>'success'];
                $bgColor = $bgMap[$u->blood_group ?? ''] ?? 'secondary';
            @endphp
            <div class="col-xl-4 col-lg-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3">
                            <!-- Avatar -->
                            <div style="flex-shrink:0">
                                @if($u->avatar)
                                <img src="{{ asset('storage/'.$u->avatar) }}" alt="{{ $u->name }}"
                                    style="width:56px;height:56px;border-radius:50%;object-fit:cover;border:2px solid #e9ecef">
                                @else
                                <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:#fff;border:2px solid #e9ecef">
                                    {{ strtoupper(substr($u->name,0,1)) }}
                                </div>
                                @endif
                            </div>
                            <!-- Info -->
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h6 class="mb-0 fw-bold text-truncate">{{ $u->name }}</h6>
                                    <span class="badge bg-{{ $roleColor }}">{{ ucfirst($m->role) }}</span>
                                    @if($u->blood_group)
                                    <span class="badge bg-{{ $bgColor }}-subtle text-{{ $bgColor }} border border-{{ $bgColor }}" style="font-size:10px">
                                        <i class="ti ti-droplet me-1"></i>{{ $u->blood_group }}
                                    </span>
                                    @endif
                                </div>
                                <div class="text-muted small mt-1">{{ $u->email }}</div>
                                @if($u->phone)
                                <div class="text-muted small"><i class="ti ti-phone me-1"></i>{{ $u->phone }}</div>
                                @endif
                                @if($u->occupation_type)
                                <div class="small mt-1">
                                    <i class="ti ti-{{ $u->occupation_type === 'student' ? 'school' : 'briefcase' }} me-1 text-muted"></i>
                                    <span class="text-capitalize">{{ $u->occupation_type }}</span>
                                    @if($u->organization) — <span class="text-muted">{{ $u->organization }}</span>@endif
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Stats Row -->
                        <div class="row g-2 mt-2">
                            <div class="col-4 text-center">
                                <div class="bg-light rounded p-1">
                                    <div class="fw-bold small">{{ $m->joined_at ? $m->joined_at->format('d M Y') : '—' }}</div>
                                    <div style="font-size:10px" class="text-muted">{{ __('Joined') }}</div>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="bg-light rounded p-1">
                                    <div class="fw-bold small {{ $m->carry_forward < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $m->carry_forward < 0 ? '-' : '' }}৳{{ number_format(abs($m->carry_forward),0) }}
                                    </div>
                                    <div style="font-size:10px" class="text-muted">{{ __('Balance') }}</div>
                                </div>
                            </div>
                            @if($isOwner || $m->user_id === Auth::id())
                            <div class="col-4 text-center">
                                <div class="bg-light rounded p-1">
                                    <div class="fw-bold small">
                                        @if($m->house_rent > 0) ৳{{ number_format($m->house_rent,0) }}
                                        @else <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                    <div style="font-size:10px" class="text-muted">{{ __('Rent/mo') }}</div>
                                </div>
                            </div>
                            @else
                            <div class="col-4 text-center">
                                <div class="bg-light rounded p-1">
                                    <div class="fw-bold small text-muted">—</div>
                                    <div style="font-size:10px" class="text-muted">{{ __('Rent/mo') }}</div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('mess.members.profile', [$mess->id, $m->id]) }}"
                               class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="ti ti-user me-1"></i>{{ __('Profile') }}
                            </a>
                            @if($isManager && $m->role !== 'owner')
                            <button class="btn btn-sm btn-outline-secondary"
                                onclick="openEditModal({{ $m->id }}, {{ json_encode($u) }}, {{ json_encode($m) }})"
                                title="Edit">
                                <i class="ti ti-edit"></i>
                            </button>
                            @if($mess->owner_id === Auth::id())
                            <form action="{{ route('mess.members.remove', [$mess->id, $m->id]) }}" method="POST" class="remove-form">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmRemove(this, '{{ addslashes($u->name) }}')" title="Remove">
                                    <i class="ti ti-user-minus"></i>
                                </button>
                            </form>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ===================== ADD MEMBER MODAL ===================== -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ti ti-user-plus me-2"></i>{{ __('Add New Member') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.members.store', $mess->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Avatar Preview -->
                    <div class="text-center mb-4">
                        <div id="addAvatarPreview" style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:inline-flex;align-items:center;justify-content:center;font-size:28px;color:#fff;margin-bottom:8px">
                            <i class="ti ti-user"></i>
                        </div><br>
                        <label class="btn btn-sm btn-outline-primary mt-1">
                            <i class="ti ti-camera me-1"></i>{{ __('Upload Photo') }}
                            <input type="file" name="avatar" accept="image/*" class="d-none" onchange="previewAvatar(this,'addAvatarPreview')">
                        </label>
                        <div class="form-text">JPG/PNG max 3MB</div>
                    </div>

                    <!-- Section: Account -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2"><h6 class="mb-0 text-primary"><i class="ti ti-lock me-2"></i>Account Credentials</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Full name">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="email@example.com">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="addPass" class="form-control" required minlength="6" placeholder="Min 6 chars">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVis('addPass','addPassIcon')"><i id="addPassIcon" class="ti ti-eye"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="01XXXXXXXXX">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Role</label>
                                    <select name="role" class="form-select">
                                        <option value="member">Member</option>
                                        <option value="author">Author</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">NID / Document</label>
                                    <input type="file" name="nid_document" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                    <div class="form-text">JPG, PNG or PDF — max 3MB</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Personal -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2"><h6 class="mb-0 text-success"><i class="ti ti-id-badge me-2"></i>Personal Information</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Gender</label>
                                    <select name="gender" class="form-select">
                                        <option value="">— Select —</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Blood Group</label>
                                    <select name="blood_group" class="form-select">
                                        <option value="">— Select —</option>
                                        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                        <option value="{{ $bg }}">{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Join Date</label>
                                    <input type="date" name="joined_at" class="form-control" value="{{ now()->toDateString() }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Home Address</label>
                                    <textarea name="address" class="form-control" rows="2" placeholder="Permanent home address...">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Occupation -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2"><h6 class="mb-0 text-info"><i class="ti ti-briefcase me-2"></i>Occupation / Education</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Occupation Type</label>
                                    <select name="occupation_type" class="form-select" id="addOccType" onchange="toggleOrg('addOccType','addOrgLabel')">
                                        <option value="">— Select —</option>
                                        <option value="student">Student</option>
                                        <option value="employed">Employed</option>
                                        <option value="business">Business</option>
                                        <option value="freelance">Freelance</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold" id="addOrgLabel">School / University / Company</label>
                                    <input type="text" name="organization" class="form-control" placeholder="Organization name" value="{{ old('organization') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Emergency Contact -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2"><h6 class="mb-0 text-warning"><i class="ti ti-phone-call me-2"></i>Emergency Contact</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Contact Name</label>
                                    <input type="text" name="emergency_contact_name" class="form-control" placeholder="Full name" value="{{ old('emergency_contact_name') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Contact Phone</label>
                                    <input type="text" name="emergency_contact_phone" class="form-control" placeholder="01XXXXXXXXX" value="{{ old('emergency_contact_phone') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Relation</label>
                                    <input type="text" name="emergency_contact_relation" class="form-control" placeholder="e.g. Father, Mother, Spouse" value="{{ old('emergency_contact_relation') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Housing / Mess -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2"><h6 class="mb-0 text-danger"><i class="ti ti-home me-2"></i>Housing & Financial</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Monthly House Rent (৳)</label>
                                    <input type="number" name="house_rent" class="form-control" step="0.01" min="0" value="0" placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Service Charge (৳) <span class="text-muted fw-normal small">(optional)</span></label>
                                    <input type="number" name="service_charge" class="form-control" step="0.01" min="0" value="0" placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Service Charge Date <span class="text-muted fw-normal small">(optional)</span></label>
                                    <input type="date" name="service_charge_date" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Advance Amount (৳)</label>
                                    <input type="number" name="advance_amount" class="form-control" step="0.01" min="0" value="0" placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Advance Date</label>
                                    <input type="date" name="advance_date" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Internal Notes</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="Private notes visible only to managers...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-user-plus me-1"></i>{{ __('Create & Add Member') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================== EDIT MEMBER MODAL ===================== -->
<div class="modal fade" id="editMemberModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="ti ti-user-edit me-2"></i>{{ __('Edit Member') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMemberForm" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    <!-- Avatar Preview -->
                    <div class="text-center mb-4">
                        <div id="editAvatarWrap">
                            <div id="editAvatarPlaceholder" style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:inline-flex;align-items:center;justify-content:center;font-size:28px;color:#fff">
                                <i class="ti ti-user"></i>
                            </div>
                            <img id="editAvatarImg" src="" alt="" style="width:80px;height:80px;border-radius:50%;object-fit:cover;display:none">
                        </div>
                        <br>
                        <label class="btn btn-sm btn-outline-warning mt-1">
                            <i class="ti ti-camera me-1"></i>{{ __('Change Photo') }}
                            <input type="file" name="avatar" accept="image/*" class="d-none" onchange="previewAvatar(this,'editAvatarImg','editAvatarPlaceholder')">
                        </label>
                    </div>

                    <!-- Account -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2"><h6 class="mb-0 text-primary"><i class="ti ti-lock me-2"></i>Account Credentials</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="eName" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="eEmail" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">New Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="ePass" class="form-control" placeholder="Leave blank to keep current" minlength="6">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVis('ePass','ePassIcon')"><i id="ePassIcon" class="ti ti-eye"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <input type="text" name="phone" id="ePhone" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Role</label>
                                    <select name="role" id="eRole" class="form-select">
                                        <option value="member">Member</option>
                                        <option value="author">Author</option>
                                        @if(Auth::user()->is_super_admin)
                                        <option value="manager">Manager</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">NID / Document</label>
                                    <input type="file" name="nid_document" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                    <div class="form-text" id="eNidHint"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2"><h6 class="mb-0 text-success"><i class="ti ti-id-badge me-2"></i>Personal Information</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Gender</label>
                                    <select name="gender" id="eGender" class="form-select">
                                        <option value="">— Select —</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Date of Birth</label>
                                    <input type="date" name="date_of_birth" id="eDob" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Blood Group</label>
                                    <select name="blood_group" id="eBlood" class="form-select">
                                        <option value="">— Select —</option>
                                        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                        <option value="{{ $bg }}">{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Join Date</label>
                                    <input type="date" name="joined_at" id="eJoined" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Home Address</label>
                                    <textarea name="address" id="eAddress" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Occupation -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2"><h6 class="mb-0 text-info"><i class="ti ti-briefcase me-2"></i>Occupation / Education</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Occupation Type</label>
                                    <select name="occupation_type" id="eOccType" class="form-select" onchange="toggleOrg('eOccType','eOrgLabel')">
                                        <option value="">— Select —</option>
                                        <option value="student">Student</option>
                                        <option value="employed">Employed</option>
                                        <option value="business">Business</option>
                                        <option value="freelance">Freelance</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold" id="eOrgLabel">School / University / Company</label>
                                    <input type="text" name="organization" id="eOrg" class="form-control" placeholder="Organization name">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2"><h6 class="mb-0 text-warning"><i class="ti ti-phone-call me-2"></i>Emergency Contact</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Contact Name</label>
                                    <input type="text" name="emergency_contact_name" id="eEcName" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Contact Phone</label>
                                    <input type="text" name="emergency_contact_phone" id="eEcPhone" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Relation</label>
                                    <input type="text" name="emergency_contact_relation" id="eEcRel" class="form-control" placeholder="e.g. Father, Spouse">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Housing -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2"><h6 class="mb-0 text-danger"><i class="ti ti-home me-2"></i>Housing & Financial</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Monthly House Rent (৳)</label>
                                    <input type="number" name="house_rent" id="eRent" class="form-control" step="0.01" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Service Charge (৳) <span class="text-muted fw-normal small">(optional)</span></label>
                                    <input type="number" name="service_charge" id="eServiceCharge" class="form-control" step="0.01" min="0" placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Service Charge Date <span class="text-muted fw-normal small">(optional)</span></label>
                                    <input type="date" name="service_charge_date" id="eServiceChargeDate" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Advance Amount (৳)</label>
                                    <input type="number" name="advance_amount" id="eAdvance" class="form-control" step="0.01" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Advance Date</label>
                                    <input type="date" name="advance_date" id="eAdvDate" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Internal Notes</label>
                                    <textarea name="notes" id="eNotes" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-warning"><i class="ti ti-device-floppy me-1"></i>{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
<script>document.addEventListener('DOMContentLoaded',()=>new bootstrap.Modal(document.getElementById('addMemberModal')).show());</script>
@endif

<script>
function previewAvatar(input, imgId, placeholderId = null) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            if (typeof imgId === 'string' && imgId === 'addAvatarPreview') {
                const el = document.getElementById('addAvatarPreview');
                el.innerHTML = '';
                el.style.background = 'none';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width:80px;height:80px;border-radius:50%;object-fit:cover';
                el.appendChild(img);
            } else {
                const img = document.getElementById(imgId);
                img.src = e.target.result;
                img.style.display = 'inline-block';
                if (placeholderId) {
                    const ph = document.getElementById(placeholderId);
                    if (ph) ph.style.display = 'none';
                }
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleVis(inputId, iconId) {
    const el = document.getElementById(inputId);
    const ic = document.getElementById(iconId);
    el.type = el.type === 'password' ? 'text' : 'password';
    ic.className = el.type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
}

function toggleOrg(selectId, labelId) {
    const val = document.getElementById(selectId).value;
    const map = { student:'School / University', employed:'Office / Company', business:'Business Name', freelance:'Platform / Agency', other:'Organization' };
    const lbl = document.getElementById(labelId);
    if (lbl) lbl.textContent = map[val] || 'School / University / Company';
}

function openEditModal(memberId, u, m) {
    const form = document.getElementById('editMemberForm');
    form.action = '{{ url("mess/" . $mess->id . "/members") }}/' + memberId;

    // Account
    document.getElementById('eName').value    = u.name || '';
    document.getElementById('eEmail').value   = u.email || '';
    document.getElementById('ePhone').value   = u.phone || '';
    document.getElementById('eRole').value    = m.role || 'member';
    document.getElementById('ePass').value    = '';

    // NID hint
    const nidHint = document.getElementById('eNidHint');
    nidHint.innerHTML = u.nid_document
        ? 'Current: <a href="{{ asset("storage/") }}' + u.nid_document + '" target="_blank">View</a> — upload to replace'
        : 'No document yet.';

    // Avatar
    const img = document.getElementById('editAvatarImg');
    const ph  = document.getElementById('editAvatarPlaceholder');
    if (u.avatar) {
        img.src = '{{ asset("storage/") }}' + u.avatar;
        img.style.display = 'inline-block';
        ph.style.display = 'none';
    } else {
        img.style.display = 'none';
        ph.style.display = 'inline-flex';
    }

    // Personal
    document.getElementById('eGender').value  = u.gender || '';
    document.getElementById('eDob').value     = u.date_of_birth || '';
    document.getElementById('eBlood').value   = u.blood_group || '';
    document.getElementById('eJoined').value  = m.joined_at ? m.joined_at.substring(0,10) : '';
    document.getElementById('eAddress').value = u.address || '';

    // Occupation
    document.getElementById('eOccType').value = u.occupation_type || '';
    document.getElementById('eOrg').value     = u.organization || '';
    toggleOrg('eOccType','eOrgLabel');

    // Emergency
    document.getElementById('eEcName').value  = u.emergency_contact_name || '';
    document.getElementById('eEcPhone').value = u.emergency_contact_phone || '';
    document.getElementById('eEcRel').value   = u.emergency_contact_relation || '';

    // Housing
    document.getElementById('eRent').value               = m.house_rent || 0;
    document.getElementById('eServiceCharge').value      = m.service_charge || 0;
    document.getElementById('eServiceChargeDate').value  = m.service_charge_date || '';
    document.getElementById('eAdvance').value            = m.advance_amount || 0;
    document.getElementById('eAdvDate').value            = m.advance_date || '';
    document.getElementById('eNotes').value              = m.notes || '';

    new bootstrap.Modal(document.getElementById('editMemberModal')).show();
}

function confirmRemove(btn, name) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Remove Member?',
            text: name + ' will be removed from this mess.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, remove',
        }).then(r => { if (r.isConfirmed) btn.closest('form').submit(); });
    } else {
        if (confirm('Remove ' + name + '?')) btn.closest('form').submit();
    }
}
</script>
@endsection
