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

        @if($memberships->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <i class="ti ti-building-community fs-1 text-muted"></i>
                </div>
                <h5>No Mess Yet</h5>
                <p class="text-muted">Create your first mess or join one using an invite code.</p>
                <a href="{{ route('mess.create') }}" class="btn btn-primary mt-2">
                    <i class="ti ti-circle-plus me-1"></i>Create Mess
                </a>
            </div>
        </div>
        @else
        <div class="row">
            @foreach($memberships as $membership)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-lg me-3">
                                @if($membership->mess->avatar)
                                    <img src="{{ asset('storage/'.$membership->mess->avatar) }}" class="img-fluid rounded-circle" alt="">
                                @else
                                    <span class="avatar-title rounded-circle bg-primary text-white fs-4">
                                        {{ strtoupper(substr($membership->mess->name, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">{{ $membership->mess->name }}</h6>
                                <span class="badge bg-{{ $membership->role === 'owner' ? 'danger' : ($membership->role === 'manager' ? 'warning' : ($membership->role === 'author' ? 'info' : 'secondary')) }} fs-10">
                                    {{ ucfirst($membership->role) }}
                                </span>
                            </div>
                        </div>

                        @if($membership->mess->description)
                        <p class="text-muted small mb-3">{{ Str::limit($membership->mess->description, 80) }}</p>
                        @endif

                        <div class="d-flex gap-3 mb-3">
                            <div class="text-center">
                                <div class="fw-bold text-primary">{{ $membership->mess->getMemberCount() }}</div>
                                <div class="fs-10 text-muted">Members</div>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold text-success">{{ $membership->mess->getEffectiveMaxMembers() }}</div>
                                <div class="fs-10 text-muted">Capacity</div>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold">
                                    <span class="badge bg-{{ $membership->mess->status === 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($membership->mess->status) }}
                                    </span>
                                </div>
                                <div class="fs-10 text-muted">Status</div>
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

                        <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="ti ti-key me-1"></i>Code: <code>{{ $membership->mess->invite_code }}</code>
                            </small>
                            <small class="text-muted">Joined {{ $membership->joined_at ? $membership->joined_at->diffForHumans() : 'N/A' }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
