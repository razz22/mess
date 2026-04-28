<?php $page = "mess-show-causes" ?>
@extends('layout.mainlayout')
@section('content')
@php $isOwner = $mess->owner_id === Auth::id(); @endphp
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">{{ __('Show Cause Letters') }} — {{ $mess->name }}</h4>
                <h6 class="text-muted">{{ __('Formal notices issued to members') }}</h6>
            </div>
            <div class="page-btn d-flex gap-2">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#issueModal">
                    <i class="ti ti-file-plus me-1"></i>Issue Letter
                </button>
                <a href="{{ route('mess.dashboard', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ __('All Show Cause Letters') }}</h6>
                <span class="badge bg-secondary">{{ $causes->count() }} total</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Member') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th>{{ __('Issued By') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($causes as $c)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $c->member->user->name }}</div>
                                <small class="text-muted">{{ ucfirst($c->member->role) }}</small>
                            </td>
                            <td>{{ $c->subject }}</td>
                            <td class="text-muted small">{{ $c->issuedBy->name }}</td>
                            <td class="text-muted small text-nowrap">{{ $c->issued_at->format('d M Y') }}</td>
                            <td>{!! $c->statusBadge() !!}</td>
                            <td class="text-center">
                                <a href="{{ route('mess.show-causes.show', [$mess->id, $c->id]) }}"
                                   class="btn btn-xs btn-outline-primary py-0" title="View">
                                    <i class="ti ti-eye" style="font-size:11px"></i>
                                </a>
                                @if($isOwner)
                                <form action="{{ route('mess.show-causes.destroy', [$mess->id, $c->id]) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger py-0 ms-1"
                                        onclick="return confirm('Delete this show cause letter?')" title="Delete">
                                        <i class="ti ti-trash" style="font-size:11px"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="ti ti-file-off fs-2 d-block mb-2 opacity-30"></i>
                                No show cause letters issued yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Issue Modal --}}
<div class="modal fade" id="issueModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="ti ti-file-alert me-2"></i>{{ __('Issue Show Cause Letter') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.show-causes.store', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Member <span class="text-danger">*</span></label>
                        <select name="member_id" class="form-select" required>
                            <option value="">— Select Member —</option>
                            @foreach($members as $m)
                                @if($m->role !== 'owner')
                                <option value="{{ $m->id }}">{{ $m->user->name }} ({{ ucfirst($m->role) }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" required maxlength="200"
                            placeholder="e.g. Violation of mess rules on 14 April 2026">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Letter Body <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control" rows="8" required maxlength="5000"
                            placeholder="Write the formal show cause notice here..."></textarea>
                        <div class="form-text">Max 5000 characters. Be formal and specific about the rule violation.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger"><i class="ti ti-send me-1"></i>{{ __('Issue Letter') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
