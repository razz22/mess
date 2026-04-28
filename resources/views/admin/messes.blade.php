<?php $page = "admin-messes" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-building-community me-2 text-success"></i>Mess Management</h4>
                <h6 class="text-muted">All messes on the platform</h6>
            </div>
            <div class="page-btn">
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createMessModal">
                    <i class="ti ti-circle-plus me-1"></i>Create Mess for User
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        {{-- Search --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                    <input type="text" name="search" class="form-control form-control-sm" style="width:250px"
                        placeholder="Mess name, address, owner name..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="ti ti-search"></i></button>
                    @if(request('search'))
                    <a href="{{ route('admin.messes') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                    <span class="ms-auto text-muted small">{{ $messes->total() }} mess(es) found</span>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mess</th>
                            <th>Owner</th>
                            <th>Invite Code</th>
                            <th>Members</th>
                            <th>Max</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messes as $m)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($m->avatar)
                                    <img src="{{ asset('storage/'.$m->avatar) }}" class="rounded" style="width:36px;height:36px;object-fit:cover;flex-shrink:0;">
                                    @else
                                    <span class="rounded bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width:36px;height:36px;font-size:13px;flex-shrink:0;">{{ strtoupper(substr($m->name,0,1)) }}</span>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $m->name }}</div>
                                        @if($m->address)<div class="text-muted small">{{ Str::limit($m->address, 35) }}</div>@endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold small">{{ $m->owner->name }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $m->owner->email }}</div>
                            </td>
                            <td><code class="small">{{ $m->invite_code }}</code></td>
                            <td>
                                <span class="fw-semibold {{ $m->active_members_count >= $m->max_members ? 'text-danger' : 'text-success' }}">
                                    {{ $m->active_members_count }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $m->max_members }}</span>
                                <button class="btn btn-xs btn-outline-primary ms-1 py-0 px-1"
                                    data-bs-toggle="modal" data-bs-target="#limitModal{{ $m->id }}"
                                    title="Change limit"><i class="ti ti-edit" style="font-size:11px"></i></button>
                            </td>
                            <td class="small text-muted">{{ $m->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('admin.mess.show', $m->id) }}" class="btn btn-xs btn-outline-primary" title="Manage Mess">
                                        <i class="ti ti-settings"></i>
                                    </a>
                                    <form action="{{ route('admin.mess.destroy', $m->id) }}" method="POST"
                                        onsubmit="return confirm('Delete mess \"{{ addslashes($m->name) }}\"? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete"><i class="ti ti-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="ti ti-building-off fs-2 d-block mb-2"></i>No messes found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($messes->hasPages())
            <div class="card-footer">{{ $messes->links() }}</div>
            @endif
        </div>

    </div>
</div>

{{-- Set Member Limit Modals --}}
@foreach($messes as $m)
<div class="modal fade" id="limitModal{{ $m->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title mb-0"><i class="ti ti-users me-2"></i>Set Member Limit</h6>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.mess.set-limit', $m->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-1 text-muted small">Mess: <strong>{{ $m->name }}</strong></div>
                    <label class="form-label fw-semibold">Max Members <span class="text-danger">*</span></label>
                    <input type="number" name="max_members" class="form-control" min="1" max="1000"
                        value="{{ $m->max_members }}" required autofocus>
                    <div class="form-text">Current: {{ $m->active_members_count }} active members</div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-check me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Create Mess Modal --}}
<div class="modal fade" id="createMessModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="ti ti-building-community me-2"></i>Create Mess for a User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.mess.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Owner (User) <span class="text-danger">*</span></label>
                        <select name="owner_id" class="form-select" required>
                            <option value="">— Select User —</option>
                            @foreach($allUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mess Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success"><i class="ti ti-device-floppy me-1"></i>Create Mess</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
