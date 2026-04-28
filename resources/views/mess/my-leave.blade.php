<?php $page = "mess-leave" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-logout me-2 text-danger"></i>{{ __('Leave Application') }}</h4>
                <h6 class="text-muted">{{ $mess->name }}</h6>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row g-3">

            {{-- Left: Apply Form --}}
            <div class="col-lg-5">

                {{-- Active/Pending Notice --}}
                @if($activeLeavePending)
                <div class="card border-{{ $activeLeavePending->status === 'approved' ? 'success' : 'warning' }} mb-3">
                    <div class="card-body d-flex align-items-start gap-3">
                        <i class="ti ti-{{ $activeLeavePending->status === 'approved' ? 'circle-check' : 'clock' }} fs-2 text-{{ $activeLeavePending->status === 'approved' ? 'success' : 'warning' }} flex-shrink-0 mt-1"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold mb-1">
                                @if($activeLeavePending->status === 'approved')
                                    Leave Approved
                                @else
                                    Leave Request Pending
                                @endif
                            </div>
                            <div class="small text-muted mb-1">
                                Applied: {{ $activeLeavePending->applied_at->format('d M Y') }}
                            </div>
                            <div class="small mb-2">
                                Last Date: <strong>{{ $activeLeavePending->last_date->format('d M Y') }}</strong>
                            </div>
                            @if($activeLeavePending->reason)
                            <div class="small text-muted mb-2">Reason: {{ $activeLeavePending->reason }}</div>
                            @endif
                            @if($activeLeavePending->status === 'pending')
                            <form action="{{ route('mess.leave.cancel', [$mess->id, $activeLeavePending->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Cancel your leave request?')">
                                    <i class="ti ti-x me-1"></i>Cancel Request
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @else
                {{-- Apply Form --}}
                <div class="card">
                    <div class="card-header bg-danger bg-opacity-10">
                        <h6 class="mb-0 text-danger fw-semibold"><i class="ti ti-file-plus me-2"></i>Apply for Leave</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="ti ti-info-circle me-1"></i>
                            Notice period: <strong>{{ $noticeMonths }} month(s)</strong>.
                            Your last date must be on or after <strong>{{ $minLastDate->format('d M Y') }}</strong>.
                        </div>

                        <form action="{{ route('mess.leave.store', $mess->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Last Working Date <span class="text-danger">*</span></label>
                                <input type="date" name="last_date"
                                    class="form-control {{ $errors->has('last_date') ? 'is-invalid' : '' }}"
                                    min="{{ $minLastDate->toDateString() }}"
                                    value="{{ old('last_date', $minLastDate->toDateString()) }}" required>
                                @error('last_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Earliest allowed: <strong>{{ $minLastDate->format('d M Y') }}</strong>
                                    (end of {{ now()->format('M Y') }}
                                    @if($noticeMonths > 1) + {{ $noticeMonths - 1 }} more month(s)@endif)
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Reason <span class="text-muted fw-normal">(optional)</span></label>
                                <textarea name="reason" class="form-control" rows="3" maxlength="1000"
                                    placeholder="e.g. Moving to a new city, job change…">{{ old('reason') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-danger w-100"
                                    onclick="return confirm('Submit leave request? A manager will review and approve it.')">
                                <i class="ti ti-send me-1"></i>Submit Leave Request
                            </button>
                        </form>
                    </div>
                </div>
                @endif

            </div>

            {{-- Right: History --}}
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold"><i class="ti ti-history me-2"></i>Leave History</h6>
                    </div>

                    @if($leaveRequests->isEmpty())
                    <div class="card-body text-center text-muted py-5">
                        <i class="ti ti-logout fs-1 d-block mb-2 opacity-30"></i>
                        No leave requests yet.
                    </div>
                    @else
                    <div class="list-group list-group-flush">
                        @foreach($leaveRequests as $lv)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold small mb-1">
                                        Last date: {{ $lv->last_date->format('d M Y') }}
                                        <span class="text-muted fw-normal ms-2">· Applied {{ $lv->applied_at->format('d M Y') }}</span>
                                    </div>
                                    @if($lv->reason)
                                    <div class="small text-muted mb-1">{{ $lv->reason }}</div>
                                    @endif
                                    @if($lv->review_note)
                                    <div class="small text-muted fst-italic">
                                        <i class="ti ti-message me-1"></i>{{ $lv->review_note }}
                                        @if($lv->reviewedBy) — {{ $lv->reviewedBy->name }}@endif
                                    </div>
                                    @endif
                                </div>
                                <div class="d-flex flex-column align-items-end gap-2 ms-3">
                                    {!! $lv->statusBadge() !!}
                                    @if($lv->status === 'pending')
                                    <form action="{{ route('mess.leave.cancel', [$mess->id, $lv->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-outline-danger"
                                                onclick="return confirm('Cancel this request?')">
                                            <i class="ti ti-x"></i> Cancel
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
