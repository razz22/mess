<?php $page = "admin-users" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-users me-2 text-primary"></i>User Management</h4>
                <h6 class="text-muted">All registered users on the platform</h6>
            </div>
            <div class="page-btn">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="ti ti-user-plus me-1"></i>Create User
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        {{-- Search & Filter --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
                    <input type="text" name="search" class="form-control form-control-sm" style="width:220px"
                        placeholder="Name, email, phone..." value="{{ request('search') }}">
                    <select name="status" class="form-select form-select-sm" style="width:130px" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Banned</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="ti ti-search"></i></button>
                    @if(request()->hasAny(['search','status']))
                    <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                    <span class="ms-auto text-muted small">{{ $users->total() }} user(s) found</span>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Phone</th>
                            <th>Owned Messes</th>
                            <th>Last Login</th>
                            <th>Joined</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($u->avatar)
                                    <img src="{{ asset('storage/'.$u->avatar) }}" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;flex-shrink:0;">
                                    @else
                                    <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold" style="width:36px;height:36px;font-size:13px;flex-shrink:0;">{{ strtoupper(substr($u->name,0,1)) }}</span>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $u->name }}</div>
                                        <div class="text-muted small">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted small">{{ $u->phone ?: '—' }}</td>
                            <td>
                                @foreach($u->ownedMesses as $m)
                                <a href="{{ route('admin.mess.show', $m->id) }}" class="badge bg-light text-dark border text-decoration-none me-1">{{ $m->name }}</a>
                                @endforeach
                                @if($u->ownedMesses->isEmpty())<span class="text-muted small">—</span>@endif
                                <div class="mt-1">
                                    <span class="text-muted" style="font-size:11px;">Can create: </span>
                                    <span class="badge bg-info text-dark" style="font-size:11px;">{{ $u->max_messes ?? 2 }}</span>
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-1 ms-1"
                                        data-bs-toggle="modal" data-bs-target="#messLimitModal{{ $u->id }}"
                                        title="Set mess creation limit"><i class="ti ti-edit" style="font-size:10px"></i></button>
                                </div>
                            </td>
                            <td class="small text-muted">
                                {{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="small text-muted">{{ $u->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge {{ $u->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $u->is_active ? 'Active' : 'Banned' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    {{-- Impersonate --}}
                                    <form action="{{ route('admin.impersonate', $u->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-outline-info" title="View as this user">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                    </form>
                                    {{-- Edit --}}
                                    <button class="btn btn-xs btn-outline-primary" title="Edit"
                                        onclick="editUser({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->email) }}', '{{ addslashes($u->phone ?? '') }}', {{ $u->is_active ? 'true' : 'false' }})">
                                        <i class="ti ti-pencil"></i>
                                    </button>
                                    {{-- Reset Password --}}
                                    <button class="btn btn-xs btn-outline-warning" title="Reset Password"
                                        onclick="resetPassword({{ $u->id }}, '{{ addslashes($u->name) }}')">
                                        <i class="ti ti-key"></i>
                                    </button>
                                    {{-- Ban/Unban --}}
                                    <form action="{{ route('admin.users.toggle', $u->id) }}" method="POST"
                                        onsubmit="return confirm('{{ $u->is_active ? 'Ban' : 'Activate' }} this user?')">
                                        @csrf
                                        <button type="submit" class="btn btn-xs {{ $u->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $u->is_active ? 'Ban User' : 'Activate User' }}">
                                            <i class="ti ti-{{ $u->is_active ? 'ban' : 'circle-check' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="ti ti-users-off fs-2 d-block mb-2"></i>No users found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
            <div class="card-footer">{{ $users->links() }}</div>
            @endif
        </div>

    </div>
</div>

{{-- Create User Modal --}}
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ti ti-user-plus me-2"></i>Create New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="createActive" checked>
                                <label class="form-check-label fw-semibold" for="createActive">Active (can login)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit User Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ti ti-pencil me-2"></i>Edit User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editUserName" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="editUserEmail" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" id="editUserPhone" class="form-control">
                        </div>
                        <div class="col-12 d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editUserActive">
                                <label class="form-check-label fw-semibold" for="editUserActive">Active (can login)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reset Password Modal --}}
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="ti ti-key me-2"></i>Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPasswordForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="small text-muted">Setting new password for: <strong id="resetPasswordUserName"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning btn-sm text-dark">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(id, name, email, phone, isActive) {
    var base = '{{ route("admin.users.update", "__ID__") }}';
    document.getElementById('editUserForm').action = base.replace('__ID__', id);
    document.getElementById('editUserName').value  = name;
    document.getElementById('editUserEmail').value = email;
    document.getElementById('editUserPhone').value = phone;
    document.getElementById('editUserActive').checked = isActive;
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

function resetPassword(id, name) {
    var base = '{{ route("admin.users.reset-password", "__ID__") }}';
    document.getElementById('resetPasswordForm').action     = base.replace('__ID__', id);
    document.getElementById('resetPasswordUserName').textContent = name;
    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
}
</script>

{{-- Mess Creation Limit Modals --}}
@foreach($users as $u)
@if(!$u->is_super_admin)
<div class="modal fade" id="messLimitModal{{ $u->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-info text-white py-2">
                <h6 class="modal-title mb-0"><i class="ti ti-building-plus me-2"></i>Mess Creation Limit</h6>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.user.set-mess-limit', $u->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-1 text-muted small">User: <strong>{{ $u->name }}</strong></div>
                    <label class="form-label fw-semibold">Max Messes Allowed <span class="text-danger">*</span></label>
                    <input type="number" name="max_messes" class="form-control" min="1" max="20"
                        value="{{ $u->max_messes ?? 2 }}" required>
                    <div class="form-text">Currently owns: {{ $u->ownedMesses->count() }} mess(es)</div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info btn-sm text-white"><i class="ti ti-check me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection
