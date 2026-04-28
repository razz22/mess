<?php $page = "mess-members" ?>
@extends('layout.mainlayout')
@section('content')
@php
    $u = $member->user;
    $bgMap = ['A+'=>'danger','A-'=>'danger','B+'=>'primary','B-'=>'primary','AB+'=>'purple','AB-'=>'purple','O+'=>'success','O-'=>'success'];
    $bgColor = $bgMap[$u->blood_group ?? ''] ?? 'secondary';
    $roleColor = match($member->role) { 'owner'=>'danger','manager'=>'warning','author'=>'info', default=>'secondary' };
    $occIcons = ['student'=>'ti-school','employed'=>'ti-briefcase','business'=>'ti-building-store','freelance'=>'ti-device-laptop','other'=>'ti-user'];
    $occIcon = $occIcons[$u->occupation_type ?? ''] ?? 'ti-user';
@endphp
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">{{ __('Member Profile') }}</h4>
                <h6>{{ $mess->name }}</h6>
            </div>
            <div class="page-btn d-flex gap-2">
                <a href="{{ route('mess.members', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back to Members
                </a>
                @if($isManager || $isSelf || $isSuperAdmin)
                <button class="btn btn-warning btn-sm" onclick="document.getElementById('editProfileForm').scrollIntoView({behavior:'smooth'})">
                    <i class="ti ti-edit me-1"></i>Edit Profile
                </button>
                @endif
            </div>
        </div>

        <!-- Profile Header Card -->
        <div class="card mb-4" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%)">
            <div class="card-body text-white p-4">
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <div style="flex-shrink:0">
                        @if($u->avatar)
                        <img src="{{ asset('storage/'.$u->avatar) }}" alt="{{ $u->name }}"
                            style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:4px solid rgba(255,255,255,0.4)">
                        @else
                        <div style="width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.2);border:4px solid rgba(255,255,255,0.4);display:flex;align-items:center;justify-content:center;font-size:40px;font-weight:700">
                            {{ strtoupper(substr($u->name,0,1)) }}
                        </div>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="mb-1 fw-bold">{{ $u->name }}</h3>
                        <div class="d-flex gap-2 flex-wrap mb-2">
                            <span class="badge bg-white bg-opacity-25 text-white fs-6">{{ ucfirst($member->role) }}</span>
                            @if($u->blood_group)
                            <span class="badge bg-danger fs-6"><i class="ti ti-droplet me-1"></i>{{ $u->blood_group }}</span>
                            @endif
                            @if($u->gender)
                            <span class="badge bg-white bg-opacity-25 text-white">{{ ucfirst($u->gender) }}</span>
                            @endif
                            <span class="badge bg-{{ $member->is_active ? 'success' : 'danger' }}">
                                {{ $member->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="d-flex gap-4 flex-wrap small opacity-90">
                            @if($u->email)<div><i class="ti ti-mail me-1"></i>{{ $u->email }}</div>@endif
                            @if($u->phone)<div><i class="ti ti-phone me-1"></i>{{ $u->phone }}</div>@endif
                            @if($member->joined_at)<div><i class="ti ti-calendar me-1"></i>Joined {{ $member->joined_at->format('d M Y') }}</div>@endif
                        </div>
                    </div>
                    @if($u->date_of_birth)
                    <div class="text-center">
                        <div style="font-size:32px;font-weight:700">{{ \Carbon\Carbon::parse($u->date_of_birth)->age }}</div>
                        <div class="small opacity-75">Years old</div>
                        <div class="small opacity-75">{{ \Carbon\Carbon::parse($u->date_of_birth)->format('d M Y') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column -->
            <div class="col-lg-4">
                <!-- Personal Info -->
                <div class="card mb-3">
                    <div class="card-header"><h6 class="mb-0"><i class="ti ti-id-badge me-2 text-success"></i>Personal Info</h6></div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <span class="text-muted small">Blood Group</span>
                                @if($u->blood_group)
                                <span class="badge bg-danger">{{ $u->blood_group }}</span>
                                @else<span class="text-muted small">—</span>@endif
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <span class="text-muted small">Gender</span>
                                <span>{{ $u->gender ? ucfirst($u->gender) : '—' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <span class="text-muted small">Date of Birth</span>
                                <span>{{ $u->date_of_birth ? \Carbon\Carbon::parse($u->date_of_birth)->format('d M Y') : '—' }}</span>
                            </li>
                            <li class="list-group-item">
                                <div class="text-muted small mb-1">Home Address</div>
                                <div>{{ $u->address ?: '—' }}</div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Occupation -->
                <div class="card mb-3">
                    <div class="card-header"><h6 class="mb-0"><i class="ti {{ $occIcon }} me-2 text-info"></i>Occupation</h6></div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted small">Type</span>
                                <span>{{ $u->occupation_type ? ucfirst($u->occupation_type) : '—' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted small">{{ $u->occupation_type === 'student' ? 'School / University' : 'Company / Office' }}</span>
                                <span class="text-end" style="max-width:60%">{{ $u->organization ?: '—' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="card mb-3">
                    <div class="card-header"><h6 class="mb-0"><i class="ti ti-phone-call me-2 text-warning"></i>Emergency Contact</h6></div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted small">Name</span>
                                <span>{{ $u->emergency_contact_name ?: '—' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted small">Phone</span>
                                @if($u->emergency_contact_phone)
                                <a href="tel:{{ $u->emergency_contact_phone }}">{{ $u->emergency_contact_phone }}</a>
                                @else<span>—</span>@endif
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted small">Relation</span>
                                <span>{{ $u->emergency_contact_relation ?: '—' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- NID -->
                @if($isManager && $u->nid_document)
                <div class="card">
                    <div class="card-header"><h6 class="mb-0"><i class="ti ti-file me-2"></i>NID / Document</h6></div>
                    <div class="card-body text-center">
                        <a href="{{ asset('storage/'.$u->nid_document) }}" target="_blank" class="btn btn-outline-info">
                            <i class="ti ti-external-link me-1"></i>View Document
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="col-lg-8">
                <!-- Financial Summary -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-3">
                        <div class="card text-center border-0 bg-primary-subtle h-100">
                            <div class="card-body py-3">
                                <div class="fs-4 fw-bold text-primary">৳{{ number_format($member->house_rent,0) }}</div>
                                <div class="small text-muted">House Rent/mo</div>
                            </div>
                        </div>
                    </div>
                    @if($member->service_charge > 0)
                    <div class="col-sm-3">
                        <div class="card text-center border-0 bg-warning-subtle h-100">
                            <div class="card-body py-3">
                                <div class="fs-4 fw-bold text-warning">৳{{ number_format($member->service_charge,0) }}</div>
                                <div class="small text-muted">Service Charge</div>
                                @if($member->service_charge_date)
                                <div style="font-size:10px" class="text-muted">{{ $member->service_charge_date->format('d M Y') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-sm-3">
                        <div class="card text-center border-0 bg-success-subtle h-100">
                            <div class="card-body py-3">
                                <div class="fs-4 fw-bold text-success">৳{{ number_format($member->advance_amount,0) }}</div>
                                <div class="small text-muted">Advance Paid</div>
                                @if($member->advance_date)
                                <div style="font-size:10px" class="text-muted">{{ $member->advance_date->format('d M Y') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card text-center border-0 {{ $member->carry_forward < 0 ? 'bg-danger-subtle' : 'bg-success-subtle' }} h-100">
                            <div class="card-body py-3">
                                <div class="fs-4 fw-bold {{ $member->carry_forward < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $member->carry_forward < 0 ? '-' : '+' }}৳{{ number_format(abs($member->carry_forward),0) }}
                                </div>
                                <div class="small text-muted">Balance</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card text-center border-0 bg-light h-100">
                            <div class="card-body py-3">
                                <div class="fs-4 fw-bold">{{ $member->joined_at ? $member->joined_at->diffInDays(now()) : '—' }}</div>
                                <div class="small text-muted">Days in Mess</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tenant Registration Form -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="ti ti-file-description me-2 text-primary"></i>ভাড়াটিয়া নিবন্ধন ফরম</h6>
                    </div>
                    <div class="card-body">
                        <a href="{{ asset('build/doc/Tenant-Registration-Form.pdf') }}" download="Tenant-Registration-Form.pdf" class="btn btn-success btn-sm">
                            <i class="ti ti-download me-1"></i>Download PDF
                        </a>
                    </div>
                </div>

                <!-- Manager Notes -->
                @if($isManager && $member->notes)
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning-subtle">
                        <h6 class="mb-0"><i class="ti ti-notes me-2 text-warning"></i>Internal Notes</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $member->notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Edit Form -->
                @if($isManager || $isSelf || $isSuperAdmin)
                <div id="editProfileForm">
                <form action="{{ route('mess.members.update', [$mess->id, $member->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show py-2 mb-3">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
                    @endif
                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show py-2 mb-3">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <!-- Account -->
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2"><h6 class="mb-0 text-primary"><i class="ti ti-lock me-2"></i>Account Credentials</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $u->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $u->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">New Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="pfPass" class="form-control" placeholder="Leave blank to keep" minlength="6">
                                        <button class="btn btn-outline-secondary" type="button" onclick="var i=document.getElementById('pfPass');i.type=i.type==='password'?'text':'password';this.querySelector('i').className=i.type==='password'?'ti ti-eye':'ti ti-eye-off'"><i class="ti ti-eye"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $u->phone) }}">
                                </div>
                                @if(($isManager || $isSuperAdmin) && $member->role !== 'owner')
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Role</label>
                                    <select name="role" class="form-select">
                                        <option value="member" {{ $member->role==='member'?'selected':'' }}>Member</option>
                                        <option value="author" {{ $member->role==='author'?'selected':'' }}>Author</option>
                                        <option value="manager" {{ $member->role==='manager'?'selected':'' }}>Manager</option>
                                    </select>
                                </div>
                                @endif
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Photo</label>
                                    <input type="file" name="avatar" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">NID / Document</label>
                                    <input type="file" name="nid_document" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
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
                                    <select name="gender" class="form-select">
                                        <option value="">— Select —</option>
                                        @foreach(['male','female','other'] as $g)
                                        <option value="{{ $g }}" {{ $u->gender===$g?'selected':'' }}>{{ ucfirst($g) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $u->date_of_birth?->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Blood Group</label>
                                    <select name="blood_group" class="form-select">
                                        <option value="">— Select —</option>
                                        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                        <option value="{{ $bg }}" {{ $u->blood_group===$bg?'selected':'' }}>{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($isManager || $isSuperAdmin)
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Join Date</label>
                                    <input type="date" name="joined_at" class="form-control" value="{{ old('joined_at', $member->joined_at?->format('Y-m-d')) }}">
                                </div>
                                @endif
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Home Address</label>
                                    <textarea name="address" class="form-control" rows="2">{{ old('address', $u->address) }}</textarea>
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
                                    <label class="form-label fw-semibold">Type</label>
                                    <select name="occupation_type" class="form-select">
                                        <option value="">— Select —</option>
                                        @foreach(['student','employed','business','freelance','other'] as $ot)
                                        <option value="{{ $ot }}" {{ $u->occupation_type===$ot?'selected':'' }}>{{ ucfirst($ot) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">School / University / Company</label>
                                    <input type="text" name="organization" class="form-control" value="{{ old('organization', $u->organization) }}">
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
                                    <label class="form-label fw-semibold">Name</label>
                                    <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $u->emergency_contact_name) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $u->emergency_contact_phone) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Relation</label>
                                    <input type="text" name="emergency_contact_relation" class="form-control" value="{{ old('emergency_contact_relation', $u->emergency_contact_relation) }}" placeholder="e.g. Father, Spouse">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Housing & Financial — owner/super admin only -->
                    @if($isSuperAdmin || $isOwner)
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2"><h6 class="mb-0 text-danger"><i class="ti ti-home me-2"></i>Housing & Financial</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Monthly House Rent (৳)</label>
                                    <input type="number" name="house_rent" class="form-control" step="0.01" min="0" value="{{ old('house_rent', $member->house_rent) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Service Charge (৳) <span class="text-muted fw-normal small">(optional)</span></label>
                                    <input type="number" name="service_charge" class="form-control" step="0.01" min="0" value="{{ old('service_charge', $member->service_charge ?? 0) }}" placeholder="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Service Charge Date <span class="text-muted fw-normal small">(optional)</span></label>
                                    <input type="date" name="service_charge_date" class="form-control" value="{{ old('service_charge_date', $member->service_charge_date?->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Advance Amount (৳)</label>
                                    <input type="number" name="advance_amount" class="form-control" step="0.01" min="0" value="{{ old('advance_amount', $member->advance_amount) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Advance Date</label>
                                    <input type="date" name="advance_date" class="form-control" value="{{ old('advance_date', $member->advance_date?->format('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($isManager || $isSuperAdmin)
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2"><h6 class="mb-0 text-secondary"><i class="ti ti-notes me-2"></i>Internal Notes</h6></div>
                        <div class="card-body">
                            <textarea name="notes" class="form-control" rows="3" placeholder="Visible to managers only">{{ old('notes', $member->notes) }}</textarea>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="ti ti-device-floppy me-1"></i>Save Changes
                        </button>
                        <a href="{{ route('mess.members', $mess->id) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
                </div>
                @endif
            </div>

            {{-- ── Show Cause Letters ── --}}
            @if($showCauses->count() > 0 || $isManager)
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-danger"><i class="ti ti-file-alert me-2"></i>Show Cause Letters</h6>
                    <span class="badge bg-{{ $showCauses->count() > 0 ? 'danger' : 'secondary' }}">{{ $showCauses->count() }}</span>
                </div>
                @if($showCauses->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($showCauses as $sc)
                    <a href="{{ route('mess.show-causes.show', [$mess->id, $sc->id]) }}"
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold small">{{ $sc->subject }}</div>
                            <small class="text-muted">
                                {{ $sc->issued_at->format('d M Y') }} · by {{ $sc->issuedBy->name }}
                                @if($isSelf && $sc->status === 'pending')
                                <span class="text-danger fw-semibold ms-1">⚠ Reply required</span>
                                @endif
                            </small>
                        </div>
                        {!! $sc->statusBadge() !!}
                    </a>
                    @endforeach
                </div>
                @else
                <div class="card-body text-muted small">No show cause letters issued.</div>
                @endif
                @if($isManager && $member->role !== 'owner')
                <div class="card-footer">
                    <a href="{{ route('mess.show-causes.index', $mess->id) }}" class="btn btn-sm btn-outline-danger">
                        <i class="ti ti-file-plus me-1"></i>Issue New Letter
                    </a>
                </div>
                @endif
            </div>
            @endif

            {{-- ── Leave Requests ── --}}
            @php
                $noticeMonths = $mess->leave_notice_months ?? 1;
                $minLastDate  = \App\Models\MessLeaveRequest::minLastDate(now(), $noticeMonths);
            @endphp
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-danger"><i class="ti ti-logout me-2"></i>Leave Requests</h6>
                    <span class="badge bg-secondary">{{ $leaveRequests->count() }}</span>
                </div>

                {{-- Active / pending notice --}}
                @if($activeLeavePending)
                <div class="alert alert-{{ $activeLeavePending->status === 'approved' ? 'success' : 'warning' }} m-3 mb-0">
                    <strong>{{ $activeLeavePending->status === 'approved' ? 'Leave Approved' : 'Leave Pending' }}:</strong>
                    Last date is <strong>{{ $activeLeavePending->last_date->format('d M Y') }}</strong>.
                    @if($activeLeavePending->status === 'pending' && $isSelf)
                    <form action="{{ route('mess.leave.cancel', [$mess->id, $activeLeavePending->id]) }}" method="POST" class="d-inline ms-2">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-outline-danger" onclick="return confirm('Cancel your leave request?')">
                            <i class="ti ti-x"></i> Cancel Request
                        </button>
                    </form>
                    @endif
                </div>
                @endif

                {{-- Apply form — only if no active/pending leave and viewing own profile --}}
                @if($isSelf && !$activeLeavePending && $member->is_active)
                <div class="card-body border-bottom">
                    <h6 class="fw-semibold mb-3">Apply for Leave</h6>
                    <form action="{{ route('mess.leave.store', $mess->id) }}" method="POST">
                        @csrf
                        @if($errors->has('last_date'))
                        <div class="alert alert-danger py-2 small">{{ $errors->first('last_date') }}</div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Last Date <span class="text-danger">*</span></label>
                                <input type="date" name="last_date" class="form-control {{ $errors->has('last_date') ? 'is-invalid' : '' }}"
                                    min="{{ $minLastDate->toDateString() }}"
                                    value="{{ old('last_date', $minLastDate->toDateString()) }}" required>
                                <div class="form-text">
                                    Notice required: <strong>{{ $noticeMonths }} month(s)</strong>.
                                    Earliest last date: <strong>{{ $minLastDate->format('d M Y') }}</strong>.
                                </div>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">Reason <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="text" name="reason" class="form-control" maxlength="500"
                                    value="{{ old('reason') }}" placeholder="e.g. Moving to a new city">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Submit a leave request? This will need manager approval.')">
                                    <i class="ti ti-logout me-1"></i>Submit Leave Request
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @endif

                {{-- Leave history --}}
                @if($leaveRequests->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Applied</th>
                                <th>Last Date</th>
                                <th>Reason</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Note') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveRequests as $lv)
                            <tr>
                                <td class="small">{{ $lv->applied_at->format('d M Y') }}</td>
                                <td class="small fw-semibold">{{ $lv->last_date->format('d M Y') }}</td>
                                <td class="small text-muted">{{ $lv->reason ?: '—' }}</td>
                                <td>{!! $lv->statusBadge() !!}</td>
                                <td class="small text-muted">
                                    @if($lv->review_note)
                                    <span title="{{ $lv->reviewedBy?->name }}">{{ $lv->review_note }}</span>
                                    @else
                                    —
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="card-body text-muted small">No leave history.</div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
