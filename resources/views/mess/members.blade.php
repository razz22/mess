<?php $page = "mess-members" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">Members — {{ $mess->name }}</h4>
                <h6>{{ $members->count() }} / {{ $mess->getEffectiveMaxMembers() }} members</h6>
            </div>
            <div class="page-btn d-flex gap-2">
                <a href="{{ route('mess.dashboard', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
                @if($members->count() < $mess->getEffectiveMaxMembers() && Auth::user()->isManagerOf($mess->id))
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="ti ti-user-plus me-1"></i>Add Member
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
                    <span>Share invite code: <strong class="fs-5">{{ $mess->invite_code }}</strong> for members to join</span>
                </div>
                <button class="btn btn-sm btn-outline-primary" onclick="navigator.clipboard.writeText('{{ $mess->invite_code }}');this.textContent='Copied!'">
                    <i class="ti ti-copy me-1"></i>Copy Code
                </button>
            </div>
        </div>

        <!-- Members Table -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">All Members</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Member</th>
                            <th>Phone</th>
                            <th>NID</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Carry Forward</th>
                            <th>Status</th>
                            @if(Auth::user()->isManagerOf($mess->id))
                            <th>Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $i => $member)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm" style="width:36px;height:36px;flex-shrink:0;">
                                        @if($member->user->avatar)
                                        <img src="{{ asset('storage/'.$member->user->avatar) }}" class="img-fluid rounded-circle" style="width:36px;height:36px;object-fit:cover;" alt="">
                                        @else
                                        <span class="avatar-title rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold" style="width:36px;height:36px;font-size:15px;">{{ strtoupper(substr($member->user->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $member->user->name }}</div>
                                        <div class="text-muted small">{{ $member->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted small">{{ $member->user->phone ?? '—' }}</td>
                            <td>
                                @if($member->user->nid_document)
                                <a href="{{ asset('storage/'.$member->user->nid_document) }}" target="_blank" class="btn btn-xs btn-outline-info">
                                    <i class="ti ti-file me-1"></i>View
                                </a>
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $member->role === 'owner' ? 'danger' : ($member->role === 'manager' ? 'warning' : ($member->role === 'author' ? 'info' : 'secondary')) }}">
                                    {{ ucfirst($member->role) }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $member->joined_at ? $member->joined_at->format('d M Y') : 'N/A' }}</td>
                            <td>
                                <span class="{{ $member->carry_forward >= 0 ? 'text-success' : 'text-danger' }}">
                                    ৳{{ number_format(abs($member->carry_forward), 2) }}
                                    {{ $member->carry_forward < 0 ? '(Owe)' : '' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $member->is_active ? 'success' : 'danger' }}">
                                    {{ $member->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            @if(Auth::user()->isManagerOf($mess->id))
                            <td>
                                <div class="d-flex gap-1">
                                    @if($member->role !== 'owner' && Auth::user()->isOwnerOf($mess->id))
                                    <button class="btn btn-xs btn-outline-secondary"
                                        onclick="openEditModal(
                                            {{ $member->id }},
                                            '{{ addslashes($member->user->name) }}',
                                            '{{ addslashes($member->user->email) }}',
                                            '{{ addslashes($member->user->phone ?? '') }}',
                                            '{{ $member->role }}',
                                            '{{ $member->user->nid_document ? asset('storage/'.$member->user->nid_document) : '' }}'
                                        )">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <form action="{{ route('mess.members.remove', [$mess->id, $member->id]) }}" method="POST" class="remove-member-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="confirmRemove(this)" data-name="{{ $member->user->name }}"><i class="ti ti-user-minus"></i></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMemberModalLabel"><i class="ti ti-user-plus me-2"></i>Add New Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.members.store', $mess->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter full name" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="Enter email" value="{{ old('email') }}" required>
                            <div class="form-text">Used to log in to the system.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="memberPassword" class="form-control" placeholder="Set a password" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass()">
                                    <i class="ti ti-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            <div class="form-text">Minimum 6 characters.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="e.g. 01XXXXXXXXX" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role</label>
                            <select name="role" class="form-select">
                                <option value="member">Member</option>
                                <option value="author">Author</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NID / Document</label>
                            <input type="file" name="nid_document" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="form-text">JPG, PNG or PDF — max 2MB.</div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3 mb-0 py-2">
                        <i class="ti ti-info-circle me-1"></i>
                        A new account will be created. The member can sign in using their email and password.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-user-plus me-1"></i>Create & Add Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMemberModalLabel"><i class="ti ti-user-edit me-2"></i>Edit Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMemberForm" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="editEmail" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">New Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="editPassword" class="form-control" placeholder="Leave blank to keep current" minlength="6">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleEditPass()">
                                    <i class="ti ti-eye" id="editEyeIcon"></i>
                                </button>
                            </div>
                            <div class="form-text">Leave blank to keep the current password.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" id="editPhone" class="form-control" placeholder="e.g. 01XXXXXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role</label>
                            <select name="role" id="editRole" class="form-select">
                                <option value="member">Member</option>
                                <option value="author">Author</option>
                                <option value="manager">Manager</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NID / Document</label>
                            <input type="file" name="nid_document" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="form-text" id="editNidCurrent"></div>
                        </div>
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

@if($errors->any())
<script>
document.addEventListener('DOMContentLoaded', function () {
    new bootstrap.Modal(document.getElementById('addMemberModal')).show();
});
</script>
@endif

<script>
function openEditModal(memberId, name, email, phone, role, nidUrl) {
    const form = document.getElementById('editMemberForm');
    form.action = '/mess/{{ $mess->id }}/members/' + memberId;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editPhone').value = phone;
    document.getElementById('editRole').value = role;
    document.getElementById('editPassword').value = '';
    const nidHint = document.getElementById('editNidCurrent');
    nidHint.innerHTML = nidUrl
        ? 'Current: <a href="' + nidUrl + '" target="_blank">View document</a> &mdash; upload to replace'
        : 'No document uploaded yet.';
    new bootstrap.Modal(document.getElementById('editMemberModal')).show();
}

function confirmRemove(btn) {
    const name = btn.dataset.name;
    Swal.fire({
        title: 'Remove Member?',
        text: name + ' will be removed from this mess.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, remove',
        cancelButtonText: 'Cancel',
    }).then(function(result) {
        if (result.isConfirmed) {
            btn.closest('form').submit();
        }
    });
}

function toggleEditPass() {
    const input = document.getElementById('editPassword');
    const icon = document.getElementById('editEyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'ti ti-eye-off';
    } else {
        input.type = 'password';
        icon.className = 'ti ti-eye';
    }
}

function togglePass() {
    const input = document.getElementById('memberPassword');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'ti ti-eye-off';
    } else {
        input.type = 'password';
        icon.className = 'ti ti-eye';
    }
}
</script>
@endsection
