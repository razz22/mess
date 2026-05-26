<?php $page = "mess-manager" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">{{ __('Manager Rotation') }} — {{ $mess->name }}</h4>
                <h6>Monthly manager assignment & voting</h6>
            </div>
            <div class="page-btn">
                @if($mess->owner_id === Auth::id() || ($member && $member->role === 'owner'))
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

        <!-- Current Managers -->
        @if($currentRotations->isNotEmpty())
        <div class="card mb-4 border-warning">
            <div class="card-header bg-warning-subtle d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="ti ti-crown text-warning me-2"></i>Current Manager(s)</h6>
                <span class="badge bg-warning text-dark">{{ $currentRotations->count() }}</span>
            </div>
            <div class="card-body p-0">
                @foreach($currentRotations as $currentRotation)
                @php $hasVoted = in_array($currentRotation->id, $votedRotationIds); @endphp
                <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    @if($mess->owner_id === Auth::id() || ($member && $member->role === 'owner'))
                    <div class="d-flex justify-content-end mb-2">
                        <form id="removeManagerForm_{{ $currentRotation->id }}" action="{{ route('mess.manager.remove', [$mess->id, $currentRotation->id]) }}" method="POST" class="d-none">
                            @csrf @method('DELETE')
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="confirmRemoveManager(
                                '{{ addslashes($currentRotation->user->name) }}',
                                '{{ date('F Y', mktime(0,0,0,$currentRotation->month,1,$currentRotation->year)) }}',
                                'removeManagerForm_{{ $currentRotation->id }}'
                            )">
                            <i class="ti ti-user-minus me-1"></i>Remove Manager
                        </button>
                    </div>
                    @endif
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-lg">
                                    @if($currentRotation->user->avatar)
                                    <img src="{{ asset('storage/'.$currentRotation->user->avatar) }}" class="rounded-circle" style="width:48px;height:48px;object-fit:cover;" alt="">
                                    @else
                                    <span class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center fw-bold" style="width:48px;height:48px;font-size:18px;">
                                        {{ strtoupper(substr($currentRotation->user->name, 0, 1)) }}
                                    </span>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $currentRotation->user->name }}</h6>
                                    <div class="text-muted small">Manager of {{ date('F Y', mktime(0,0,0,$currentRotation->month,1,$currentRotation->year)) }}</div>
                                    <div class="mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                        <i class="ti ti-star{{ $i <= $currentRotation->getAverageRating() ? '-filled text-warning' : ' text-muted' }}" style="font-size:13px"></i>
                                        @endfor
                                        <span class="ms-1 small text-muted">({{ $currentRotation->votes->count() }} votes)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-2 mt-md-0">
                            @if(!$hasVoted && $currentRotation->user_id !== Auth::id())
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <h6 class="mb-2 small fw-semibold">Rate this Manager</h6>
                                    <form action="{{ route('mess.manager.vote', [$mess->id, $currentRotation->id]) }}" method="POST">
                                        @csrf
                                        <div class="mb-2 star-rating" id="stars_{{ $currentRotation->id }}">
                                            @for($s = 5; $s >= 1; $s--)
                                            <input type="radio" name="rating" value="{{ $s }}" id="star{{ $currentRotation->id }}_{{ $s }}" required>
                                            <label for="star{{ $currentRotation->id }}_{{ $s }}" title="{{ $s }} star">&#9733;</label>
                                            @endfor
                                        </div>
                                        <div class="mb-2">
                                            <input type="text" name="comment" class="form-control form-control-sm" placeholder="Comment (optional)">
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-sm">Submit Vote</button>
                                    </form>
                                </div>
                            </div>
                            @elseif($hasVoted)
                            <div class="alert alert-success mb-0 py-2 small">
                                <i class="ti ti-check me-1"></i>You have voted for this manager.
                            </div>
                            @else
                            <div class="alert alert-info mb-0 py-2 small">
                                <i class="ti ti-info-circle me-1"></i>You are a current manager.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="alert alert-warning d-flex align-items-center gap-2">
            <i class="ti ti-info-circle fs-5"></i>
            <div>
                No manager assigned for the current month via the rotation system.
                @if($mess->owner_id === Auth::id() || ($member && $member->role === 'owner'))
                Use the <strong>Assign Manager</strong> button above to set one.
                @php $manualManager = $mess->activeMembers()->where('role','manager')->with('user')->first(); @endphp
                @if($manualManager)
                <br><span class="small text-muted">Note: <strong>{{ $manualManager->user->name }}</strong> has the manager role but was not assigned through this system.</span>
                @endif
                @endif
            </div>
        </div>
        @endif

        <!-- Nominations -->
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h6 class="mb-0"><i class="ti ti-hand-finger text-primary me-2"></i>Manager Nominations — {{ date('F Y', mktime(0,0,0,$nomMonth,1,$nomYear)) }}</h6>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <form method="GET" action="{{ route('mess.manager', $mess->id) }}" class="d-flex gap-2 align-items-center">
                        @foreach(request()->except(['nom_month','nom_year']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <select name="nom_month" class="form-select form-select-sm" style="width:120px;" onchange="this.form.submit()">
                            @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $nomMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endfor
                        </select>
                        <select name="nom_year" class="form-select form-select-sm" style="width:90px;" onchange="this.form.submit()">
                            @for($y = now()->year + 1; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $nomYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </form>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#nominateModal">
                        <i class="ti ti-plus me-1"></i>Nominate
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                @forelse($nominations as $nom)
                @php $iVoted = in_array($nom->id, $myNominationVoteIds); @endphp
                <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="row align-items-center g-2">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2">
                                <div>
                                    @if($nom->nominee->avatar)
                                    <img src="{{ asset('storage/'.$nom->nominee->avatar) }}" class="rounded-circle" style="width:38px;height:38px;object-fit:cover;" alt="">
                                    @else
                                    <span class="avatar-title rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:38px;height:38px;">
                                        {{ strtoupper(substr($nom->nominee->name, 0, 1)) }}
                                    </span>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $nom->nominee->name }}</div>
                                    <div class="text-muted small">Nominated by {{ $nom->nominator->name }}</div>
                                    <div class="text-muted small"><i class="ti ti-calendar me-1"></i>{{ date('F Y', mktime(0,0,0,$nom->month,1,$nom->year)) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-5 fw-bold text-primary">{{ $nom->votes_count }}</span>
                                <span class="text-muted small">{{ Str::plural('vote', $nom->votes_count) }}</span>
                                @if($nom->votes_count > 0)
                                <div class="d-flex flex-wrap gap-1 ms-1">
                                    @foreach($nom->votes->take(5) as $v)
                                    <span class="badge bg-light text-dark border" title="{{ $v->voter->name ?? '' }}" style="font-size:.7rem;">
                                        {{ strtoupper(substr($v->voter->name ?? '?', 0, 1)) }}
                                    </span>
                                    @endforeach
                                    @if($nom->votes_count > 5)
                                    <span class="badge bg-secondary" style="font-size:.7rem;">+{{ $nom->votes_count - 5 }}</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-5 d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                            @if($nom->user_id !== Auth::id() && !$iVoted)
                            <form action="{{ route('mess.manager.nomination.vote', [$mess->id, $nom->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-thumb-up me-1"></i>Vote
                                </button>
                            </form>
                            @elseif($iVoted)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                <i class="ti ti-check me-1"></i>Voted
                            </span>
                            @endif
                            @if($mess->owner_id === Auth::id() || ($member && $member->role === 'owner'))
                            <form action="{{ route('mess.manager.assign', $mess->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $nom->user_id }}">
                                <input type="hidden" name="month" value="{{ $nom->month }}">
                                <input type="hidden" name="year" value="{{ $nom->year }}">
                                <button type="submit" class="btn btn-sm btn-warning">
                                    <i class="ti ti-crown me-1"></i>Assign as Manager
                                </button>
                            </form>
                            @endif
                            @if($mess->owner_id === Auth::id() || ($member && $member->role === 'owner') || $nom->user_id === Auth::id() || $nom->nominated_by === Auth::id())
                            <form action="{{ route('mess.manager.nomination.remove', [$mess->id, $nom->id]) }}" method="POST"
                                onsubmit="return confirm('Remove this nomination?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="ti ti-mood-empty" style="font-size:2rem;opacity:.3"></i>
                    <p class="mt-1 mb-0 small">No nominations for this month yet. Be the first to nominate!</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- History Filter -->
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" action="{{ route('mess.manager', $mess->id) }}" class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label mb-1 small fw-semibold">Month</label>
                        <select name="month" class="form-select form-select-sm" style="min-width:130px;">
                            <option value="">All Months</option>
                            @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $filterMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label mb-1 small fw-semibold">Year</label>
                        <select name="year" class="form-select form-select-sm" style="min-width:100px;">
                            <option value="">All Years</option>
                            @for($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="ti ti-filter me-1"></i>Filter</button>
                        @if($filterMonth != now()->month || $filterYear != now()->year)
                        <a href="{{ route('mess.manager', $mess->id) }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-x me-1"></i>Clear</a>
                        @endif
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                            <i class="ti ti-calendar me-1"></i>
                            {{ date('F', mktime(0,0,0,$filterMonth,1)) }} {{ $filterYear }}
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <!-- History -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0"><i class="ti ti-history me-2 text-muted"></i>Monthly Manager History</h6>
                <span class="badge bg-secondary">{{ $rotations->total() }} record(s)</span>
            </div>
            <div class="card-body p-0">
                @forelse($rotations as $rotation)
                @php
                    $avg      = $rotation->getAverageRating();
                    $votesCnt = $rotation->votes->count();
                    $pct      = $avg * 20; // 0-100 for progress bar
                @endphp
                <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="row align-items-center g-3">
                        <!-- Avatar + Name -->
                        <div class="col-md-3 col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-md flex-shrink-0">
                                    @if($rotation->user->avatar)
                                    <img src="{{ asset('storage/'.$rotation->user->avatar) }}" class="rounded-circle" style="width:42px;height:42px;object-fit:cover;" alt="">
                                    @else
                                    <span class="rounded-circle bg-warning text-white fw-bold d-flex align-items-center justify-content-center" style="width:42px;height:42px;font-size:1rem;">
                                        {{ strtoupper(substr($rotation->user->name, 0, 1)) }}
                                    </span>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $rotation->user->name }}</div>
                                    <div class="text-muted small">
                                        <i class="ti ti-calendar me-1"></i>{{ date('F Y', mktime(0,0,0,$rotation->month,1,$rotation->year)) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Stars + Rating -->
                        <div class="col-md-3 col-sm-6">
                            <div class="d-flex align-items-center gap-2">
                                <div>
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="ti ti-star{{ $i <= $avg ? '-filled text-warning' : ' text-muted' }}" style="font-size:15px"></i>
                                    @endfor
                                </div>
                                <span class="fw-bold text-warning">{{ $avg }}</span>
                                <span class="text-muted small">/ 5</span>
                            </div>
                            <div class="progress mt-1" style="height:5px;width:120px;">
                                <div class="progress-bar bg-warning" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                        <!-- Vote count -->
                        <div class="col-md-2 col-sm-4">
                            <div class="text-muted small">Total Votes</div>
                            <div class="fw-semibold">
                                <i class="ti ti-thumb-up text-primary me-1"></i>{{ $votesCnt }}
                                {{ Str::plural('vote', $votesCnt) }}
                            </div>
                        </div>
                        <!-- Status -->
                        <div class="col-md-2 col-sm-4">
                            <span class="badge rounded-pill bg-{{ $rotation->is_current ? 'warning text-dark' : 'light text-secondary border' }} px-3 py-2">
                                <i class="ti ti-{{ $rotation->is_current ? 'crown' : 'clock' }} me-1"></i>
                                {{ $rotation->is_current ? 'Current' : 'Past' }}
                            </span>
                        </div>
                        <!-- Details toggle -->
                        <div class="col-md-2 col-sm-4 text-md-end">
                            @if($votesCnt > 0)
                            <button class="btn btn-sm btn-outline-secondary" type="button"
                                data-bs-toggle="collapse" data-bs-target="#votes_{{ $rotation->id }}">
                                <i class="ti ti-message-circle me-1"></i>Reviews
                            </button>
                            @else
                            <span class="text-muted small">No reviews yet</span>
                            @endif
                        </div>
                    </div>

                    <!-- Collapsible vote comments -->
                    @if($votesCnt > 0)
                    <div class="collapse mt-3" id="votes_{{ $rotation->id }}">
                        <div class="bg-light rounded p-3">
                            <h6 class="small fw-semibold text-muted mb-2 text-uppercase">Member Reviews</h6>
                            <div class="row g-2">
                                @foreach($rotation->votes as $vote)
                                <div class="col-md-6">
                                    <div class="d-flex gap-2 bg-white rounded p-2 border h-100">
                                        <div class="flex-shrink-0">
                                            @if(!empty($vote->voter->avatar))
                                            <img src="{{ asset('storage/'.$vote->voter->avatar) }}" class="rounded-circle" style="width:30px;height:30px;object-fit:cover;" alt="">
                                            @else
                                            <span class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-semibold" style="width:30px;height:30px;font-size:.75rem;">
                                                {{ strtoupper(substr($vote->voter->name ?? '?', 0, 1)) }}
                                            </span>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="small fw-semibold">{{ $vote->voter->name ?? 'Unknown' }}</span>
                                                <span class="ms-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                    <i class="ti ti-star{{ $i <= $vote->rating ? '-filled text-warning' : ' text-muted' }}" style="font-size:11px"></i>
                                                    @endfor
                                                </span>
                                            </div>
                                            @if($vote->comment)
                                            <div class="text-muted small mt-1">"{{ $vote->comment }}"</div>
                                            @else
                                            <div class="text-muted small mt-1 fst-italic">No comment</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="ti ti-crown-off" style="font-size:2.5rem;opacity:.3"></i>
                    <p class="mt-2 mb-0">No manager history yet.</p>
                </div>
                @endforelse
            </div>
            @if($rotations->hasPages())
            <div class="card-footer d-flex justify-content-end">
                {{ $rotations->links() }}
            </div>
            @endif
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-warning">Assign Manager</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Remove Manager Confirm Modal -->
<div class="modal fade" id="removeManagerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-4 pb-2">
                <div class="mb-3" style="font-size:3rem;line-height:1;">
                    <span style="color:#dc3545;">&#9888;</span>
                </div>
                <h5 class="fw-bold mb-1">Remove Manager?</h5>
                <p class="text-muted mb-0">
                    Remove <strong id="rmName"></strong> as manager for <strong id="rmMonth"></strong>?
                </p>
                <p class="text-muted small mt-1">Their role will be reverted to member.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pt-0 pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4" id="rmConfirmBtn">
                    <i class="ti ti-user-minus me-1"></i>Yes, Remove
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmRemoveManager(name, month, formId) {
    document.getElementById('rmName').textContent  = name;
    document.getElementById('rmMonth').textContent = month;
    document.getElementById('rmConfirmBtn').onclick = function() {
        document.getElementById(formId).submit();
    };
    new bootstrap.Modal(document.getElementById('removeManagerModal')).show();
}
</script>

<!-- Nominate Modal -->
<div class="modal fade" id="nominateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-hand-finger me-2 text-primary"></i>Nominate a Manager</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.manager.nominate', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">For Month <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-7">
                                <select name="month" class="form-select" required>
                                    @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $nomMonth ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-5">
                                <select name="year" class="form-select" required>
                                    @for($y = now()->year + 1; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ $y == $nomYear ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Member <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            <option value="">— Choose a member —</option>
                            @foreach($members as $m)
                            <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info py-2 small mb-0">
                        <i class="ti ti-info-circle me-1"></i>You can nominate yourself or any other member. Each member can only be nominated once per month.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-send me-1"></i>Submit Nomination</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.star-rating{display:flex;flex-direction:row-reverse;justify-content:flex-end;gap:4px}
.star-rating input[type="radio"]{position:absolute;opacity:0;width:0;height:0;pointer-events:none}
.star-rating label{font-size:1.8rem;color:#ddd;cursor:pointer;line-height:1;transition:color .15s;user-select:none}
.star-rating label:hover,.star-rating label:hover~label,.star-rating input[type="radio"]:checked~label{color:#f6a817}
</style>
@endsection
