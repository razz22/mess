@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
<div class="content">

{{-- Page Header --}}
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <span class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width:36px;height:36px;">
                    <i class="ti ti-ticket fs-5"></i>
                </span>
                {{ __('Support Center') }}
            </h4>
            <p class="text-muted mb-0 small">{{ __('Submit a support token to get help from the super admin.') }}</p>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="ti ti-circle-check fs-5 flex-shrink-0"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">

    {{-- Left: Info + Create --}}
    <div class="col-lg-5">

        {{-- Professional Info Card --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;overflow:hidden;">
            <div class="card-body p-0">
                <div class="p-4" style="background:linear-gradient(135deg,#206bc4 0%,#1a56a8 100%);">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white" style="width:48px;height:48px;">
                            <i class="ti ti-headset text-primary fs-4"></i>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-0">{{ __('Need Help?') }}</h5>
                            <span class="text-white opacity-75 small">{{ __('We\'re here to assist you') }}</span>
                        </div>
                    </div>
                    <p class="text-white opacity-90 mb-0 small lh-lg">
                        {{ __('If you are experiencing any issues with the application, have a feature suggestion, or need technical assistance, you can raise a support token. Our super admin will review your request and respond as soon as possible.') }}
                    </p>
                </div>
                <div class="p-4 bg-white">
                    <div class="row g-3 mb-3">
                        <div class="col-4 text-center">
                            <div class="rounded-3 p-2" style="background:#f0f6ff;">
                                <i class="ti ti-ticket text-primary fs-4 d-block mb-1"></i>
                                <span class="small fw-semibold text-primary">{{ __('2 tokens') }}</span>
                                <div class="text-muted" style="font-size:10px;">{{ __('per day') }}</div>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="rounded-3 p-2" style="background:#fff3f0;">
                                <i class="ti ti-message-2 text-danger fs-4 d-block mb-1"></i>
                                <span class="small fw-semibold text-danger">{{ __('2 messages') }}</span>
                                <div class="text-muted" style="font-size:10px;">{{ __('per token') }}</div>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="rounded-3 p-2" style="background:#f0fff4;">
                                <i class="ti ti-clock text-success fs-4 d-block mb-1"></i>
                                <span class="small fw-semibold text-success">{{ __('1 hour') }}</span>
                                <div class="text-muted" style="font-size:10px;">{{ __('token life') }}</div>
                            </div>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li class="d-flex align-items-start gap-2 mb-2"><i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>{{ __('Attach a screenshot or image for context') }}</li>
                        <li class="d-flex align-items-start gap-2 mb-2"><i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>{{ __('Describe your issue clearly in the message') }}</li>
                        <li class="d-flex align-items-start gap-2"><i class="ti ti-circle-check text-success mt-1 flex-shrink-0"></i>{{ __('Each token is private and visible only to you and the admin') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Create Token Form --}}
        @if($todayCount >= 2)
        <div class="alert alert-warning d-flex align-items-center gap-3 border-0" style="border-radius:12px;">
            <i class="ti ti-alert-triangle fs-4 flex-shrink-0 text-warning"></i>
            <div>
                <strong>{{ __('Daily limit reached') }}</strong>
                <div class="small text-muted">{{ __('You have used your 2 support tokens for today. Please try again tomorrow.') }}</div>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-plus-circle text-primary"></i>{{ __('Create New Support Token') }}
                </h6>
                <p class="text-muted small mb-0 mt-1">{{ __('Tokens used today:') }} <strong>{{ $todayCount }}/2</strong></p>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('mess.support.store', $mess) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('Subject / Issue Title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                               placeholder="{{ __('e.g. Meal count incorrect for this month') }}"
                               value="{{ old('subject') }}" maxlength="255" required>
                        @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-ticket me-2"></i>{{ __('Generate Support Token') }}
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>

    {{-- Right: Token History --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-history text-primary"></i>{{ __('My Support Tokens') }}
                </h6>
                <span class="badge bg-secondary-subtle text-secondary border">{{ $tokens->count() }} {{ __('total') }}</span>
            </div>
            <div class="card-body px-4 pb-4">
                @if($tokens->isEmpty())
                <div class="text-center text-muted py-5">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle" style="width:64px;height:64px;">
                            <i class="ti ti-ticket text-primary fs-2"></i>
                        </span>
                    </div>
                    <h6 class="fw-normal">{{ __('No tokens yet') }}</h6>
                    <p class="text-muted small mb-0">{{ __('Your support tokens will appear here once created.') }}</p>
                </div>
                @else
                <div class="d-flex flex-column gap-3">
                    @foreach($tokens as $token)
                    @php
                        $isExpired  = $token->isExpired();
                        $badgeClass = $token->status === 'open' && !$isExpired
                            ? 'bg-success-subtle text-success border-success-subtle'
                            : ($token->status === 'closed' ? 'bg-secondary-subtle text-secondary border-secondary-subtle' : 'bg-warning-subtle text-warning border-warning-subtle');
                        $statusLabel = $token->status === 'open' && !$isExpired ? __('Open') : ($token->status === 'closed' ? __('Closed') : __('Expired'));
                        $unread = $token->messages->where('sender_type','admin')->where('is_read',false)->count();
                    @endphp
                    <div class="border rounded-3 p-3 d-flex align-items-start gap-3" style="background:#fafbfc;">
                        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width:40px;height:40px;background:#e8f0fe;">
                            <i class="ti ti-ticket text-primary"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="fw-semibold text-truncate" style="max-width:200px;">{{ $token->subject }}</span>
                                <span class="badge border {{ $badgeClass }}" style="font-size:10px;">{{ $statusLabel }}</span>
                                @if($unread)
                                <span class="badge bg-danger" style="font-size:10px;">{{ $unread }} {{ __('new reply') }}</span>
                                @endif
                            </div>
                            <div class="small text-muted d-flex align-items-center gap-3 flex-wrap">
                                <span><i class="ti ti-hash me-1"></i><code class="text-primary">{{ $token->token }}</code></span>
                                <span><i class="ti ti-message-2 me-1"></i>{{ $token->user_message_count }}/2 {{ __('messages') }}</span>
                                <span><i class="ti ti-clock me-1"></i>
                                    @if($token->status === 'open' && !$isExpired)
                                        {{ __('Expires') }} {{ $token->expires_at->diffForHumans() }}
                                    @else
                                        {{ $token->created_at->format('d M Y, h:i A') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('mess.support.show', [$mess, $token]) }}" class="btn btn-sm btn-outline-primary flex-shrink-0">
                            {{ __('View') }}
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
</div>
</div>
@endsection
