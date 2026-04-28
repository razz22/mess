<?php $page = "mess-rewards" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">{{ __('Rewards & Leaderboard') }} — {{ $mess->name }}</h4>
                <h6>{{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</h6>
            </div>
            <div class="page-btn">
                @if($member->canManage())
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRewardModal">
                    <i class="ti ti-trophy me-1"></i>Award Reward
                </button>
                @endif
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <!-- Month Filter -->
        <div class="card mb-4">
            <div class="card-body py-2">
                <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                    <select name="month" class="form-select form-select-sm" style="width:130px" onchange="this.form.submit()">
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                    <select name="year" class="form-select form-select-sm" style="width:100px" onchange="this.form.submit()">
                        @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>
        </div>

        <div class="row g-3">
            <!-- Leaderboard -->
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header bg-warning-subtle">
                        <h6 class="mb-0"><i class="ti ti-medal text-warning me-2"></i>Leaderboard</h6>
                    </div>
                    <div class="card-body">
                        @forelse($leaderboard as $i => $entry)
                        <div class="d-flex align-items-center gap-3 py-2 {{ $i < $leaderboard->count()-1 ? 'border-bottom' : '' }}">
                            <div class="fw-bold fs-4 text-{{ $i === 0 ? 'warning' : ($i === 1 ? 'secondary' : ($i === 2 ? 'danger' : 'muted')) }}" style="width:30px">
                                {{ $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : '#'.($i+1))) }}
                            </div>
                            <div class="avatar avatar-sm">
                                @if($entry->user->avatar)
                                <img src="{{ asset('storage/'.$entry->user->avatar) }}" class="avatar-title rounded-circle" style="object-fit:cover;" alt="">
                                @else
                                <span class="avatar-title rounded-circle bg-{{ $i === 0 ? 'warning' : 'primary' }} text-white">
                                    {{ strtoupper(substr($entry->user->name, 0, 1)) }}
                                </span>
                                @endif
                            </div>
                            <div class="flex-fill">
                                <div class="fw-semibold">{{ $entry->user->name }}</div>
                            </div>
                            <div class="fw-bold text-warning">{{ number_format($entry->total_points, 0) }} pts</div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <i class="ti ti-trophy-off fs-3 text-muted d-block mb-2"></i>
                            <p class="text-muted">No rewards this month yet.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Rewards List -->
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header"><h6 class="mb-0">All Rewards</h6></div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Member') }}</th>
                                    <th>Type</th>
                                    <th>Points</th>
                                    <th>Gift</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rewards as $reward)
                                <tr>
                                    <td>{{ $reward->user->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $reward->type === 'manager_reward' ? 'warning' : ($reward->type === 'report_points' ? 'info' : 'success') }}">
                                            {{ match($reward->type) {
                                                'manager_reward' => '👑 Manager',
                                                'member_reward'  => '🌟 Member',
                                                'report_points'  => '📋 Report',
                                                'best_market'    => '🛒 Market',
                                                default          => $reward->type
                                            } }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-warning">{{ number_format($reward->points, 0) }}</td>
                                    <td class="text-muted small">{{ $reward->gift_description ?: '—' }}</td>
                                    <td class="text-muted small">{{ Str::limit($reward->reason, 40) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">No rewards this month.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Current Manager Rating -->
            @if($currentRotation)
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning-subtle">
                        <h6 class="mb-0"><i class="ti ti-crown text-warning me-2"></i>Manager Performance — {{ $currentRotation->user->name }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center">
                                <div class="fs-1 fw-bold text-warning">{{ $managerRating }}</div>
                                <div class="d-flex justify-content-center">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="ti ti-star{{ $i <= $managerRating ? '-filled text-warning' : ' text-muted' }} fs-5"></i>
                                    @endfor
                                </div>
                                <div class="text-muted small mt-1">{{ $currentRotation->votes->count() }} votes</div>
                            </div>
                            <div class="col-md-9">
                                @foreach($currentRotation->votes as $vote)
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="avatar avatar-sm">@if($vote->voter->avatar)<img src="{{ asset('storage/'.$vote->voter->avatar) }}" class="avatar-title rounded-circle" style="object-fit:cover;" alt="">@else<span class="avatar-title rounded-circle bg-secondary text-white">{{ strtoupper(substr($vote->voter->name, 0, 1)) }}</span>@endif</div>
                                    <div>
                                        <span class="fw-semibold small">{{ $vote->voter->name }}</span>
                                        <div>@for($i = 1; $i <= 5; $i++)<i class="ti ti-star{{ $i <= $vote->rating ? '-filled text-warning' : ' text-muted' }}" style="font-size:11px"></i>@endfor</div>
                                        @if($vote->comment)<div class="text-muted" style="font-size:12px">{{ $vote->comment }}</div>@endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Award Reward Modal -->
<div class="modal fade" id="addRewardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-trophy text-warning me-2"></i>Award Reward</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.rewards.store', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Member <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            @foreach($members as $m)
                            <option value="{{ $m->user->id }}">{{ $m->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reward Type</label>
                        <select name="type" class="form-select">
                            <option value="member_reward">🌟 Best Member</option>
                            <option value="manager_reward">👑 Manager Reward</option>
                            <option value="best_market">🛒 Best Market</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Month</label>
                            <select name="month" class="form-select">
                                @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Year</label>
                            <input type="number" name="year" class="form-control" value="{{ $year }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Points <span class="text-danger">*</span></label>
                        <input type="number" name="points" class="form-control" required min="1" placeholder="e.g. 50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gift / Prize</label>
                        <input type="text" name="gift_description" class="form-control" placeholder="e.g. Free meal for a week">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="Why are you awarding this?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-warning">Award!</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
