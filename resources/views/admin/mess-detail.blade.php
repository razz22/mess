<?php $page = "admin-mess-detail" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        {{-- Breadcrumb --}}
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">
                    <a href="{{ route('admin.messes') }}" class="text-muted text-decoration-none small"><i class="ti ti-arrow-left me-1"></i>Messes</a>
                    <span class="mx-2 text-muted">/</span>
                    <i class="ti ti-building-community me-2 text-success"></i>{{ $mess->name }}
                </h4>
                <h6 class="text-muted">Owner: {{ $mess->owner->name }} &middot; Code: <code>{{ $mess->invite_code }}</code></h6>
            </div>
            <div class="page-btn d-flex gap-2">
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="ti ti-user-plus me-1"></i>Add Member
                </button>
                <form action="{{ route('admin.mess.destroy', $mess->id) }}" method="POST"
                    onsubmit="return confirm('Permanently delete this mess?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-trash me-1"></i>Delete Mess</button>
                </form>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        {{-- Mess Info + Quick Stats --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            @if($mess->avatar)
                            <img src="{{ asset('storage/'.$mess->avatar) }}" class="rounded" style="width:60px;height:60px;object-fit:cover;">
                            @else
                            <span class="rounded bg-success text-white d-flex align-items-center justify-content-center fw-bold fs-3" style="width:60px;height:60px;">{{ strtoupper(substr($mess->name,0,1)) }}</span>
                            @endif
                            <div>
                                <div class="fw-bold fs-5">{{ $mess->name }}</div>
                                @if($mess->description)<div class="text-muted small">{{ $mess->description }}</div>@endif
                            </div>
                        </div>
                        <ul class="list-unstyled small mb-0">
                            <li class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Owner</span>
                                <span class="fw-semibold">{{ $mess->owner->name }}</span>
                            </li>
                            <li class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Invite Code</span>
                                <code>{{ $mess->invite_code }}</code>
                            </li>
                            <li class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Members</span>
                                <span class="fw-semibold">{{ $members->count() }} / {{ $mess->max_members }}</span>
                            </li>
                            <li class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Address</span>
                                <span class="text-end">{{ $mess->address ?: '—' }}</span>
                            </li>
                            <li class="d-flex justify-content-between py-1">
                                <span class="text-muted">Created</span>
                                <span>{{ $mess->created_at->format('d M Y') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card h-100 shadow-sm">
                    <div class="card-header"><h6 class="mb-0">This Month's Rent Overview — {{ now()->format('F Y') }}</h6></div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Member</th><th>Room</th><th>Monthly Rent</th><th>Paid</th><th>Balance</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @foreach($members as $m)
                                @php
                                    $paid    = $paidByMember[$m->id] ?? 0;
                                    $balance = $m->house_rent - $paid;
                                    if ($m->house_rent == 0) { $badge = 'bg-secondary'; $label = 'Not Set'; }
                                    elseif ($balance <= 0) { $badge = 'bg-success'; $label = 'Paid'; }
                                    elseif ($paid > 0) { $badge = 'bg-warning text-dark'; $label = 'Partial'; }
                                    else { $badge = 'bg-danger'; $label = 'Pending'; }
                                @endphp
                                <tr>
                                    <td class="fw-semibold small">{{ $m->user->name }}</td>
                                    <td class="text-muted small">{{ $m->room_no ?: '—' }}</td>
                                    <td class="small">{{ $m->house_rent > 0 ? '৳'.number_format($m->house_rent,0) : '—' }}</td>
                                    <td class="text-success small fw-semibold">৳{{ number_format($paid,0) }}</td>
                                    <td class="{{ $balance > 0 ? 'text-danger' : 'text-muted' }} small">{{ $m->house_rent > 0 ? '৳'.number_format(abs($balance),0) : '—' }}</td>
                                    <td><span class="badge {{ $badge }}">{{ $label }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Members Table --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Active Members ({{ $members->count() }})</h6>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="ti ti-user-plus me-1"></i>Add Member
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Member</th>
                            <th>Role</th>
                            <th>Room</th>
                            <th>Monthly Rent</th>
                            <th>Joined</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $m)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($m->user->avatar)
                                    <img src="{{ asset('storage/'.$m->user->avatar) }}" class="rounded-circle" style="width:34px;height:34px;object-fit:cover;flex-shrink:0;">
                                    @else
                                    <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold" style="width:34px;height:34px;font-size:13px;flex-shrink:0;">{{ strtoupper(substr($m->user->name,0,1)) }}</span>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $m->user->name }}</div>
                                        <div class="text-muted small">{{ $m->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $roleBadge = match($m->role) {
                                        'owner'   => 'bg-danger',
                                        'manager' => 'bg-warning text-dark',
                                        'author'  => 'bg-info text-dark',
                                        default   => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $roleBadge }}">{{ ucfirst($m->role) }}</span>
                            </td>
                            <td class="small text-muted">{{ $m->room_no ?: '—' }}</td>
                            <td class="small">{{ $m->house_rent > 0 ? '৳'.number_format($m->house_rent,0) : '—' }}</td>
                            <td class="small text-muted">{{ $m->joined_at ? $m->joined_at->format('d M Y') : '—' }}</td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    {{-- Change role (not for owner) --}}
                                    @if($m->role !== 'owner')
                                    <button class="btn btn-xs btn-outline-primary"
                                        onclick="changeRole({{ $m->id }}, '{{ $m->role }}', '{{ addslashes($m->user->name) }}')">
                                        <i class="ti ti-badge"></i>
                                    </button>
                                    @endif
                                    {{-- Remove (not for owner) --}}
                                    @if($m->role !== 'owner')
                                    <form action="{{ route('admin.mess.member.remove', [$mess->id, $m->id]) }}" method="POST"
                                        onsubmit="return confirm('Remove {{ addslashes($m->user->name) }} from this mess?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-outline-danger"><i class="ti ti-user-off"></i></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No members.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Add Member Modal --}}
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="ti ti-user-plus me-2"></i>Add Member to {{ $mess->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.mess.member.add', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select User <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            <option value="">— Select User —</option>
                            @php $existingIds = $members->pluck('user_id')->toArray(); @endphp
                            @foreach($allUsers as $u)
                            @if(!in_array($u->id, $existingIds))
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endif
                            @endforeach
                        </select>
                        <div class="form-text">Only users not already in this mess are shown.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="member" selected>Member</option>
                            <option value="author">Author</option>
                            <option value="manager">Manager</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="ti ti-user-plus me-1"></i>Add Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Change Role Modal --}}
<div class="modal fade" id="changeRoleModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ti ti-badge me-2"></i>Change Role</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="changeRoleForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <p class="small text-muted mb-3">Changing role for: <strong id="changeRoleMemberName"></strong></p>
                    <select name="role" id="changeRoleSelect" class="form-select" required>
                        <option value="member">Member</option>
                        <option value="author">Author</option>
                        <option value="manager">Manager</option>
                        <option value="owner">Owner (transfers ownership)</option>
                    </select>
                    <div class="form-text mt-2 text-warning">
                        <i class="ti ti-alert-triangle me-1"></i>Setting "Owner" will transfer ownership of this mess.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function changeRole(memberId, currentRole, memberName) {
    var base = '{{ route("admin.mess.member.role", [$mess->id, "__ID__"]) }}';
    document.getElementById('changeRoleForm').action            = base.replace('__ID__', memberId);
    document.getElementById('changeRoleMemberName').textContent = memberName;
    document.getElementById('changeRoleSelect').value           = currentRole;
    new bootstrap.Modal(document.getElementById('changeRoleModal')).show();
}
</script>
@endsection
