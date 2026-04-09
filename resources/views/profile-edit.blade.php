@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">My Profile</h4>
                <h6>Manage your account information</h6>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">

                <!-- Left: Avatar + Password -->
                <div class="col-xl-3 col-lg-4">

                    <!-- Avatar Card -->
                    <div class="card mb-4">
                        <div class="card-body text-center py-4">
                            <div class="position-relative d-inline-block mb-3">
                                <img id="avatarPreview"
                                     src="{{ $user->avatar_url }}"
                                     alt="Avatar"
                                     class="rounded-circle object-fit-cover"
                                     style="width:100px;height:100px;border:3px solid #e9ecef">
                                <label for="avatarInput"
                                       class="position-absolute bottom-0 end-0 btn btn-primary btn-sm rounded-circle p-1"
                                       style="width:28px;height:28px;cursor:pointer" title="Change photo">
                                    <i class="ti ti-camera" style="font-size:13px"></i>
                                </label>
                                <input type="file" id="avatarInput" name="avatar" accept="image/*" class="d-none">
                            </div>
                            <h6 class="fw-semibold mb-0">{{ $user->name }}</h6>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="ti ti-lock me-2 text-warning"></i>Change Password</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Leave blank to keep current">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                       placeholder="Repeat new password">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right: Profile Fields -->
                <div class="col-xl-9 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="ti ti-user me-2 text-primary"></i>Personal Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                           class="form-control @error('name') is-invalid @enderror" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                           class="form-control @error('email') is-invalid @enderror" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                           class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="date_of_birth"
                                           value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                                           class="form-control @error('date_of_birth') is-invalid @enderror">
                                    @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                        <option value="">— Select —</option>
                                        <option value="male"   {{ old('gender', $user->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other"  {{ old('gender', $user->gender) === 'other'  ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Blood Group</label>
                                    <select name="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                                        <option value="">— Select —</option>
                                        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                        <option value="{{ $bg }}" {{ old('blood_group', $user->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                    @error('blood_group')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-9">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" value="{{ old('address', $user->address) }}"
                                           class="form-control @error('address') is-invalid @enderror">
                                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Occupation / Type</label>
                                    <input type="text" name="occupation_type" value="{{ old('occupation_type', $user->occupation_type) }}"
                                           class="form-control @error('occupation_type') is-invalid @enderror"
                                           placeholder="e.g. Student, Service, Business">
                                    @error('occupation_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Organization / Institution</label>
                                    <input type="text" name="organization" value="{{ old('organization', $user->organization) }}"
                                           class="form-control @error('organization') is-invalid @enderror">
                                    @error('organization')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="ti ti-phone-call me-2 text-danger"></i>Emergency Contact</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Contact Name</label>
                                    <input type="text" name="emergency_contact_name"
                                           value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}"
                                           class="form-control @error('emergency_contact_name') is-invalid @enderror">
                                    @error('emergency_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Relation</label>
                                    <input type="text" name="emergency_contact_relation"
                                           value="{{ old('emergency_contact_relation', $user->emergency_contact_relation) }}"
                                           class="form-control @error('emergency_contact_relation') is-invalid @enderror"
                                           placeholder="e.g. Father, Spouse">
                                    @error('emergency_contact_relation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="emergency_contact_phone"
                                           value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}"
                                           class="form-control @error('emergency_contact_phone') is-invalid @enderror">
                                    @error('emergency_contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 gap-2">
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ti ti-device-floppy me-1"></i>Save Changes
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('avatarInput').addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
