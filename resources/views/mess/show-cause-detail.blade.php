<?php $page = "mess-show-causes" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">Show Cause — {{ $cause->subject }}</h4>
                <h6>{!! $cause->statusBadge() !!}</h6>
            </div>
            <div class="page-btn">
                @if($isManager)
                <a href="{{ route('mess.show-causes.index', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back to List
                </a>
                @else
                <a href="{{ route('mess.members.profile', [$mess->id, $cause->member_id]) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back to Profile
                </a>
                @endif
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row g-3">
            <div class="col-lg-8">

                {{-- Letter --}}
                <div class="card border-danger mb-3">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <div>
                            <i class="ti ti-file-alert me-2"></i>
                            <strong>Show Cause Notice</strong>
                        </div>
                        <small>{{ $cause->issued_at->format('d M Y, h:i A') }}</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="row g-2 text-muted small mb-3">
                                <div class="col-sm-6">
                                    <span class="fw-semibold text-dark">To:</span> {{ $cause->member->user->name }}
                                    <span class="badge bg-secondary ms-1">{{ ucfirst($cause->member->role) }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="fw-semibold text-dark">From:</span> {{ $cause->issuedBy->name }}
                                </div>
                                <div class="col-12">
                                    <span class="fw-semibold text-dark">Subject:</span> {{ $cause->subject }}
                                </div>
                            </div>
                            <hr>
                            <div class="mt-3" style="white-space:pre-wrap;line-height:1.8;">{{ $cause->body }}</div>
                        </div>
                    </div>
                </div>

                {{-- Member Reply --}}
                @if($cause->member_reply)
                <div class="card border-info mb-3">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <div><i class="ti ti-message-reply me-2"></i><strong>Member's Reply</strong></div>
                        <small>{{ $cause->replied_at?->format('d M Y, h:i A') }}</small>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            @if($cause->member->user->avatar)
                            <img src="{{ asset('storage/'.$cause->member->user->avatar) }}" class="rounded-circle flex-shrink-0" style="width:36px;height:36px;object-fit:cover;">
                            @else
                            <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:36px;height:36px;">
                                {{ strtoupper(substr($cause->member->user->name, 0, 1)) }}
                            </div>
                            @endif
                            <div>
                                <div class="fw-semibold small mb-1">{{ $cause->member->user->name }}</div>
                                <div style="white-space:pre-wrap;line-height:1.8;">{{ $cause->member_reply }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Final Reply --}}
                @if($cause->final_reply)
                <div class="card border-success mb-3">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <div><i class="ti ti-message-check me-2"></i><strong>Management's Final Reply</strong></div>
                        <small>{{ $cause->final_replied_at?->format('d M Y, h:i A') }}</small>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            @if($cause->issuedBy->avatar)
                            <img src="{{ asset('storage/'.$cause->issuedBy->avatar) }}" class="rounded-circle flex-shrink-0" style="width:36px;height:36px;object-fit:cover;">
                            @else
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:36px;height:36px;">
                                {{ strtoupper(substr($cause->issuedBy->name, 0, 1)) }}
                            </div>
                            @endif
                            <div>
                                <div class="fw-semibold small mb-1">{{ $cause->issuedBy->name }}</div>
                                <div style="white-space:pre-wrap;line-height:1.8;">{{ $cause->final_reply }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Member reply form --}}
                @if($isConcernedMember && $cause->status === 'pending')
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <i class="ti ti-pencil me-2"></i><strong>Submit Your Reply</strong>
                    </div>
                    <form action="{{ route('mess.show-causes.reply', [$mess->id, $cause->id]) }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <textarea name="member_reply" class="form-control" rows="6" required maxlength="5000"
                                placeholder="Write your response to this show cause notice..."></textarea>
                            <div class="form-text mt-1">You can only reply once. Make your response thoughtful and complete.</div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-send me-1"></i>Submit Reply
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                {{-- Manager final reply form --}}
                @if($isManager && $cause->status === 'replied')
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <i class="ti ti-message-check me-2"></i><strong>Submit Final Reply & Close</strong>
                    </div>
                    <form action="{{ route('mess.show-causes.final-reply', [$mess->id, $cause->id]) }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <textarea name="final_reply" class="form-control" rows="5" required maxlength="5000"
                                placeholder="Write your final response to the member's reply..."></textarea>
                            <div class="form-text mt-1">This will close the show cause. No further replies will be possible.</div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-lock me-1"></i>Submit & Close
                            </button>
                        </div>
                    </form>
                </div>
                @endif

            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header"><h6 class="mb-0">Timeline</h6></div>
                    <div class="card-body p-3">
                        <div class="d-flex gap-3 mb-3">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center text-white" style="width:32px;height:32px;flex-shrink:0;">
                                    <i class="ti ti-file-alert" style="font-size:14px"></i>
                                </div>
                                @if($cause->member_reply)<div style="width:2px;flex:1;background:#dee2e6;margin:4px auto;min-height:30px"></div>@endif
                            </div>
                            <div class="pt-1">
                                <div class="fw-semibold small">Letter Issued</div>
                                <div class="text-muted" style="font-size:11px">{{ $cause->issued_at->format('d M Y, h:i A') }}</div>
                                <div class="text-muted" style="font-size:11px">By {{ $cause->issuedBy->name }}</div>
                            </div>
                        </div>

                        @if($cause->member_reply)
                        <div class="d-flex gap-3 mb-3">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle bg-info d-flex align-items-center justify-content-center text-white" style="width:32px;height:32px;flex-shrink:0;">
                                    <i class="ti ti-message-reply" style="font-size:14px"></i>
                                </div>
                                @if($cause->final_reply)<div style="width:2px;flex:1;background:#dee2e6;margin:4px auto;min-height:30px"></div>@endif
                            </div>
                            <div class="pt-1">
                                <div class="fw-semibold small">Member Replied</div>
                                <div class="text-muted" style="font-size:11px">{{ $cause->replied_at->format('d M Y, h:i A') }}</div>
                            </div>
                        </div>
                        @endif

                        @if($cause->final_reply)
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center text-white" style="width:32px;height:32px;flex-shrink:0;">
                                <i class="ti ti-check" style="font-size:14px"></i>
                            </div>
                            <div class="pt-1">
                                <div class="fw-semibold small">Closed</div>
                                <div class="text-muted" style="font-size:11px">{{ $cause->final_replied_at->format('d M Y, h:i A') }}</div>
                            </div>
                        </div>
                        @endif

                        @if($cause->status === 'pending')
                        <div class="alert alert-warning py-2 mb-0 mt-3 small">
                            <i class="ti ti-clock me-1"></i>Awaiting member reply.
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Member Info</h6></div>
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            @if($cause->member->user->avatar)
                            <img src="{{ asset('storage/'.$cause->member->user->avatar) }}" class="rounded-circle" style="width:44px;height:44px;object-fit:cover;">
                            @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:44px;height:44px;">
                                {{ strtoupper(substr($cause->member->user->name, 0, 1)) }}
                            </div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $cause->member->user->name }}</div>
                                <span class="badge bg-secondary">{{ ucfirst($cause->member->role) }}</span>
                            </div>
                        </div>
                        <div class="text-muted small">{{ $cause->member->user->email }}</div>
                        @if($cause->member->user->phone)
                        <div class="text-muted small mt-1"><i class="ti ti-phone me-1"></i>{{ $cause->member->user->phone }}</div>
                        @endif
                        @if($isManager)
                        <a href="{{ route('mess.members.profile', [$mess->id, $cause->member_id]) }}"
                           class="btn btn-outline-primary btn-sm w-100 mt-3">
                            <i class="ti ti-user me-1"></i>View Profile
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
