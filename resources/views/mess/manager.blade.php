<?php $page = "mess-manager" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">Manager Rotation — {{ $mess->name }}</h4>
                <h6>Monthly manager assignment & voting</h6>
            </div>
            <div class="page-btn">
                @if($mess->owner_id === Auth::id())
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignManagerModal">
                    <i class="ti ti-crown me-1"></i>Assign Manager
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

        <!-- Current Manager -->
        @if($currentRotation)
        <div class="card mb-4 border-warning">
            <div class="card-header bg-warning-subtle">
                <h6 class="mb-0"><i class="ti ti-crown text-warning me-2"></i>Current Manager</h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-xl">
                                <span class="avatar-title rounded-circle bg-warning text-white fs-4">
                                    {{ strtoupper(substr($currentRotation->user->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $currentRotation->user->name }}</h5>
                                <div class="text-muted">Manager of {{ date('F Y', mktime(0,0,0,$currentRotation->month,1,$currentRotation->year)) }}</div>
                                <div class="mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="ti ti-star{{ $i <= $currentRotation->getAverageRating() ? '-filled text-warning' : ' text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-1 small text-muted">({{ $currentRotation->votes->count() }} votes)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        @if(!$hasVoted && $currentRotation->user_id !== Auth::id())
                        <div class="card bg-light">
                            <div class="card-body py-3">
                                <h6 class="mb-2">Rate this Manager</h6>
                                <form action="{{ route('mess.manager.vote', [$mess->id, $currentRotation->id]) }}" method="POST">
                                    @csrf
                                    <div class="mb-2">
                                        <div class="d-flex gap-2">
                                            @for($s = 1; $s <= 5; $s++)
                                            <div>
                                                <input type="radio" name="rating" value="{{ $s }}" id="star{{ $s }}" class="d-none" required>
                                                <label for="star{{ $s }}" class="btn btn-sm btn-outline-warning">{{ $s }}★</label>
                                            </div>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" name="comment" class="form-control form-control-sm" placeholder="Comment (optional)">
                                    </div>
                                    <button type="submit" class="btn btn-warning btn-sm">Submit Vote</button>
                                </form>
                            </div>
                        </div>
                        @elseif($hasVoted)
                        <div class="alert alert-success mb-0 py-2">
                            <i class="ti ti-check me-1"></i>You have already voted for this manager.
                        </div>
                        @else
                        <div class="alert alert-info mb-0 py-2">
                            <i class="ti ti-info-circle me-1"></i>You are the current manager.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-warning">
            <i class="ti ti-info-circle me-1"></i>No manager assigned for the current month.
            @if($mess->owner_id === Auth::id())
            Please assign a manager.
            @endif
        </div>
        @endif

        <!-- History -->
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Manager History</h6></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Month / Year</th>
                            <th>Manager</th>
                            <th>Avg. Rating</th>
                            <th>Votes</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rotations as $rotation)
                        <tr>
                            <td>{{ date('F Y', mktime(0,0,0,$rotation->month,1,$rotation->year)) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-title rounded-circle bg-warning text-white">{{ strtoupper(substr($rotation->user->name, 0, 1)) }}</span>
                                    </div>
                                    {{ $rotation->user->name }}
                                </div>
                            </td>
                            <td>
                                @php $avg = $rotation->getAverageRating(); @endphp
                                @for($i = 1; $i <= 5; $i++)
                                <i class="ti ti-star{{ $i <= $avg ? '-filled text-warning' : ' text-muted' }}" style="font-size:12px"></i>
                                @endfor
                                <span class="small ms-1">{{ $avg }}</span>
                            </td>
                            <td>{{ $rotation->votes->count() }} votes</td>
                            <td>
                                <span class="badge bg-{{ $rotation->is_current ? 'warning' : 'secondary' }}">
                                    {{ $rotation->is_current ? 'Current' : 'Past' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No manager history yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Assign Manager Modal -->
<div class="modal fade" id="assignManagerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Manager</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.manager.assign', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Member <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            @foreach($members as $m)
                            @if($m->user->id !== Auth::id())
                            <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Month</label>
                            <select name="month" class="form-select">
                                @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Year</label>
                            <input type="number" name="year" class="form-control" value="{{ now()->year }}">
                        </div>
                    </div>
                    <div class="alert alert-warning py-2 small">
                        <i class="ti ti-info-circle me-1"></i>This will end the current manager's term and assign the selected member as the new manager.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Assign Manager</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
