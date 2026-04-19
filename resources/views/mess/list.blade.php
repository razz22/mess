<?php $page = "mess-list" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4 class="fw-bold">My Messes</h4>
                    <h6>Manage your mess memberships</h6>
                </div>
            </div>
            <div class="page-btn d-flex gap-2">
                @php
                    $activeMess = Auth::user()->getActiveMess();
                    $canCreate  = Auth::user()->ownedMesses()->count() < 2
                               && (!$activeMess || !Auth::user()->isBasicMemberOf($activeMess->id));
                @endphp
                @if($canCreate)
                <a href="{{ route('mess.create') }}" class="btn btn-primary">
                    <i class="ti ti-circle-plus me-1"></i>Create Mess
                </a>
                @endif
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Join by Code -->
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-semibold mb-3"><i class="ti ti-key me-2 text-primary"></i>Join a Mess by Invite Code</h6>
                <form action="{{ route('mess.join.post') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input type="text" name="invite_code" class="form-control w-auto" placeholder="Enter 8-digit invite code" maxlength="8" style="text-transform:uppercase">
                    <button type="submit" class="btn btn-outline-primary">Join</button>
                </form>
            </div>
        </div>

        {{-- Super Admin: flat list of ALL messes --}}
        @if(Auth::user()->is_super_admin)
        @if(isset($allMesses) && $allMesses->isNotEmpty())
        <div class="row g-3">
            @foreach($allMesses as $mess)
            @php $isActive = $mess->status === 'active'; @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    {{-- Status bar on top --}}
                    <div style="height:4px;background:{{ $isActive ? '#10b981' : '#9ca3af' }};"></div>
                    <div class="card-body">
                        {{-- Status badge row --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            @if($isActive)
                            <span class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill" style="background:#d1fae5;color:#065f46;font-size:.75rem;font-weight:600;">
                                <span class="rounded-circle" style="width:6px;height:6px;background:#10b981;display:inline-block;"></span> Active
                            </span>
                            @else
                            <span class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill" style="background:#f3f4f6;color:#6b7280;font-size:.75rem;font-weight:600;">
                                <span class="rounded-circle" style="width:6px;height:6px;background:#9ca3af;display:inline-block;"></span> Inactive
                            </span>
                            @endif
                            <button type="button"
                                class="btn btn-sm {{ $isActive ? 'btn-outline-warning' : 'btn-outline-success' }} py-0 px-2"
                                style="font-size:.75rem;"
                                onclick="openToggleModal({{ $mess->id }}, '{{ addslashes($mess->name) }}', '{{ $mess->status }}')">
                                <i class="ti ti-{{ $isActive ? 'ban' : 'shield-check' }} me-1"></i>{{ $isActive ? 'Deactivate' : 'Activate' }}
                            </button>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3 flex-shrink-0" style="width:52px;height:52px;border-radius:50%;overflow:hidden;background:#e9ecef;display:flex;align-items:center;justify-content:center">
                                @if($mess->avatar)
                                    <img src="{{ asset('storage/'.$mess->avatar) }}" alt="{{ $mess->name }}" style="width:100%;height:100%;object-fit:cover">
                                @else
                                    <span style="font-size:22px;font-weight:700;color:#fff;background:#198754;width:100%;height:100%;display:flex;align-items:center;justify-content:center;border-radius:50%">
                                        {{ strtoupper(substr($mess->name, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">{{ $mess->name }}</h6>
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    @if($mess->owner->avatar)
                                    <img src="{{ asset('storage/'.$mess->owner->avatar) }}" class="rounded-circle" style="width:16px;height:16px;object-fit:cover">
                                    @else
                                    <span class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white" style="width:16px;height:16px;font-size:8px;flex-shrink:0">{{ strtoupper(substr($mess->owner->name,0,1)) }}</span>
                                    @endif
                                    <span class="small text-muted">{{ $mess->owner->name }}</span>
                                    <span class="badge bg-secondary" style="font-size:.65rem;">Owner</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mb-3">
                            <div class="text-center">
                                <div class="fw-bold text-primary">{{ $mess->active_members_count }}</div>
                                <div class="text-muted" style="font-size:.7rem;">Members</div>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold text-muted">{{ $mess->max_members }}</div>
                                <div class="text-muted" style="font-size:.7rem;">Capacity</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('mess.dashboard', $mess->id) }}" class="btn btn-danger btn-sm flex-fill">
                                <i class="ti ti-door-enter me-1"></i>Enter
                            </a>
                            <a href="{{ route('admin.mess.show', $mess->id) }}" class="btn btn-outline-secondary btn-sm" title="Admin Manage">
                                <i class="ti ti-settings"></i>
                            </a>
                        </div>
                        <div class="mt-2 pt-2 border-top">
                            <small class="text-muted"><i class="ti ti-calendar me-1"></i>Created {{ $mess->created_at->format('d M Y') }}</small>
                            &nbsp;&middot;&nbsp;
                            <small class="text-muted"><i class="ti ti-key me-1"></i><code>{{ $mess->invite_code }}</code></small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="card"><div class="card-body text-center py-4 text-muted">No messes on the platform yet.</div></div>
        @endif

        {{-- Regular users --}}
        @elseif($memberships->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <i class="ti ti-building-community fs-1 text-muted"></i>
                </div>
                <h5>No Mess Yet</h5>
                <p class="text-muted">Create your first mess or join one using an invite code above.</p>
                <a href="{{ route('mess.create') }}" class="btn btn-primary mt-2">
                    <i class="ti ti-circle-plus me-1"></i>Create Mess
                </a>
            </div>
        </div>
        @else
        <div class="row g-3">
            @foreach($memberships as $membership)
            @php $isActive = $membership->mess->status === 'active'; @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    {{-- Status bar on top --}}
                    <div style="height:4px;background:{{ $isActive ? '#10b981' : '#9ca3af' }};"></div>
                    <div class="card-body">
                        {{-- Status badge row --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            @if($isActive)
                            <span class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill" style="background:#d1fae5;color:#065f46;font-size:.75rem;font-weight:600;">
                                <span class="rounded-circle" style="width:6px;height:6px;background:#10b981;display:inline-block;"></span> Active
                            </span>
                            @else
                            <span class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill" style="background:#f3f4f6;color:#6b7280;font-size:.75rem;font-weight:600;">
                                <span class="rounded-circle" style="width:6px;height:6px;background:#9ca3af;display:inline-block;"></span> Inactive
                            </span>
                            @endif
                            <span class="badge bg-{{ $membership->role === 'owner' ? 'danger' : ($membership->role === 'manager' ? 'warning' : ($membership->role === 'author' ? 'info' : 'secondary')) }}">
                                {{ ucfirst($membership->role) }}
                            </span>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3 flex-shrink-0" style="width:52px;height:52px;border-radius:50%;overflow:hidden;background:#e9ecef;display:flex;align-items:center;justify-content:center">
                                @if($membership->mess->avatar)
                                    <img src="{{ asset('storage/'.$membership->mess->avatar) }}" alt="{{ $membership->mess->name }}" style="width:100%;height:100%;object-fit:cover">
                                @else
                                    <span style="font-size:22px;font-weight:700;color:#fff;background:#0d6efd;width:100%;height:100%;display:flex;align-items:center;justify-content:center;border-radius:50%">
                                        {{ strtoupper(substr($membership->mess->name, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">{{ $membership->mess->name }}</h6>
                                @if($membership->mess->description)
                                <p class="text-muted mb-0" style="font-size:.75rem;">{{ Str::limit($membership->mess->description, 60) }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-3 mb-3">
                            <div class="text-center">
                                <div class="fw-bold text-primary">{{ $membership->mess->getMemberCount() }}</div>
                                <div class="text-muted" style="font-size:.7rem;">Members</div>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold text-success">{{ $membership->mess->getEffectiveMaxMembers() }}</div>
                                <div class="text-muted" style="font-size:.7rem;">Capacity</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('mess.dashboard', $membership->mess->id) }}" class="btn btn-primary btn-sm flex-fill">
                                <i class="ti ti-door-enter me-1"></i>Enter
                            </a>
                            @if($membership->role === 'owner')
                            <a href="{{ route('mess.settings', $membership->mess->id) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-settings"></i>
                            </a>
                            @endif
                        </div>

                        <div class="mt-2 pt-2 border-top d-flex flex-wrap gap-2">
                            <small class="text-muted"><i class="ti ti-key me-1"></i><code>{{ $membership->mess->invite_code }}</code></small>
                            <small class="text-muted ms-auto">Joined {{ $membership->joined_at ? $membership->joined_at->diffForHumans() : 'N/A' }}</small>
                            <small class="text-muted w-100"><i class="ti ti-calendar me-1"></i>Created {{ $membership->mess->created_at->format('d M Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Toggle Status Modal (super admin only) --}}
@if(Auth::user()->is_super_admin)
<div class="modal fade" id="messToggleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div id="toggleModalIcon" class="mb-3">
                    <span id="toggleIconCircle" class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;">
                        <i id="toggleIconEl" style="font-size:2rem;"></i>
                    </span>
                </div>
                <h5 class="fw-bold mb-1" id="toggleModalTitle"></h5>
                <p class="text-muted mb-4" id="toggleModalDesc"></p>
                <form id="toggleStatusForm" method="POST">
                    @csrf
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn px-4" id="toggleModalBtn"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openToggleModal(messId, messName, currentStatus) {
    const isActive = currentStatus === 'active';
    const baseUrl  = '{{ url("admin/messes") }}/' + messId + '/toggle-status';

    document.getElementById('toggleStatusForm').action = baseUrl;
    document.getElementById('toggleModalTitle').textContent = isActive
        ? 'Deactivate ' + messName + '?'
        : 'Activate ' + messName + '?';
    document.getElementById('toggleModalDesc').textContent = isActive
        ? 'Members will no longer be able to add meal attendance, deposits, expenses, or generate reports until this mess is reactivated.'
        : 'Members will regain full access to add meal attendance, deposits, expenses, and generate reports.';

    const circle = document.getElementById('toggleIconCircle');
    const icon   = document.getElementById('toggleIconEl');
    const btn    = document.getElementById('toggleModalBtn');

    if (isActive) {
        circle.style.background = '#fef3c7';
        icon.className = 'ti ti-ban';
        icon.style.color = '#d97706';
        btn.className = 'btn btn-warning px-4';
        btn.innerHTML = '<i class="ti ti-ban me-1"></i>Yes, Deactivate';
    } else {
        circle.style.background = '#d1fae5';
        icon.className = 'ti ti-shield-check';
        icon.style.color = '#059669';
        btn.className = 'btn btn-success px-4';
        btn.innerHTML = '<i class="ti ti-shield-check me-1"></i>Yes, Activate';
    }

    new bootstrap.Modal(document.getElementById('messToggleModal')).show();
}
</script>
@endif
@endsection
