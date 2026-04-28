<?php $page = "mess-upgrade" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-rocket me-2 text-primary"></i>{{ __('Upgrade Mess') }}</h4>
                <h6 class="text-muted">{{ $mess->name }} — Choose a subscription plan</h6>
            </div>
            <div class="page-btn d-flex gap-2">
                <a href="{{ route('mess.upgrade.history', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-history me-1"></i>Upgrade History
                </a>
                <a href="{{ route('mess.settings', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="ti ti-circle-check me-2"></i>{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        @php $customSub = \App\Models\CustomSubscription::active()->forMess($mess->id)->first(); @endphp

        {{-- Status Bar --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="text-muted small mb-1"><i class="ti ti-users me-1"></i>{{ __('Current Members') }}</div>
                    <div class="fs-3 fw-bold text-primary">{{ $mess->getMemberCount() }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="text-muted small mb-1"><i class="ti ti-users-plus me-1"></i>{{ __('Member Limit') }}</div>
                    <div class="fs-3 fw-bold text-info">{{ $mess->getEffectiveMaxMembers() }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="text-muted small mb-1"><i class="ti ti-chart-bar me-1"></i>{{ __('Slots Free') }}</div>
                    <div class="fs-3 fw-bold text-success">{{ max(0, $mess->getEffectiveMaxMembers() - $mess->getMemberCount()) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="text-muted small mb-1"><i class="ti ti-clock me-1"></i>{{ __('Pending Request') }}</div>
                    <div class="fs-3 fw-bold {{ $pendingUpgrade ? 'text-warning' : 'text-muted' }}">
                        {{ $pendingUpgrade ? 'Yes' : 'No' }}
                    </div>
                </div>
            </div>
        </div>

        @if($pendingUpgrade)
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
            <i class="ti ti-clock fs-5 flex-shrink-0"></i>
            <div>
                You have a pending <strong>{{ $pendingUpgrade->plan?->name ?? 'upgrade' }}</strong> request awaiting admin approval.
                New subscriptions are locked until it is reviewed.
                <a href="{{ route('mess.upgrade.history', $mess->id) }}" class="alert-link ms-1">View history →</a>
            </div>
        </div>
        @endif

        {{-- Heading --}}
        <div class="text-center mb-4">
            <h5 class="fw-bold mb-1">{{ __('Choose the Right Plan for Your Mess') }}</h5>
            <p class="text-muted mb-0">Click any plan to subscribe via bKash payment</p>
        </div>

        {{-- Plan Cards --}}
        @if($plans->isEmpty() && !$customSub)
        <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="ti ti-package-off fs-1 d-block mb-3 opacity-40"></i>
                No subscription plans available yet. Please contact the administrator.
            </div>
        </div>
        @else
        @php
            $planColors  = ['#4361ee','#7209b7','#f72585','#3a0ca3','#06b6d4','#059669'];
            $planGrads   = [
                'linear-gradient(135deg,#4361ee,#7b8cde)',
                'linear-gradient(135deg,#7209b7,#c77dff)',
                'linear-gradient(135deg,#f72585,#ff85a1)',
                'linear-gradient(135deg,#3a0ca3,#7048e8)',
                'linear-gradient(135deg,#06b6d4,#38bdf8)',
                'linear-gradient(135deg,#059669,#34d399)',
            ];
            $planIcons = ['ti-rocket','ti-crown','ti-diamond','ti-bolt','ti-stars','ti-award'];
        @endphp
        <div class="row g-4 justify-content-center">

            {{-- Custom Subscription Card (shown as active/selected) --}}
            @if($customSub)
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div class="position-relative" style="border-radius:20px;border:2px solid #6366f1;
                     background:#fff;overflow:hidden;box-shadow:0 8px 32px rgba(99,102,241,.28);">

                    {{-- ACTIVE ribbon --}}
                    <div style="position:absolute;top:14px;right:-28px;background:#6366f1;color:#fff;
                                font-size:10px;font-weight:700;padding:4px 36px;transform:rotate(45deg);
                                letter-spacing:.5px;z-index:2;">{{ __('ACTIVE') }}</div>

                    {{-- Header --}}
                    <div style="background:linear-gradient(135deg,#6366f1,#818cf8);padding:28px 20px 24px;text-align:center;">
                        <div style="width:58px;height:58px;border-radius:50%;background:rgba(255,255,255,.2);
                                    display:flex;align-items:center;justify-content:center;margin:0 auto 12px;
                                    box-shadow:0 4px 12px rgba(0,0,0,.15);">
                            <i class="ti ti-star" style="font-size:26px;color:#fff;"></i>
                        </div>
                        <div style="color:rgba(255,255,255,.85);font-size:11px;font-weight:700;
                                    letter-spacing:1.5px;text-transform:uppercase;margin-bottom:10px;">
                            {{ $customSub->label }}
                        </div>
                        @if($customSub->is_free)
                        <div style="color:#fff;font-size:36px;font-weight:800;line-height:1;">{{ __('Free') }}</div>
                        @else
                        <div style="color:#fff;line-height:1;">
                            <span style="font-size:15px;vertical-align:top;line-height:28px;font-weight:600;">৳</span>
                            <span style="font-size:42px;font-weight:800;">{{ number_format($customSub->price, 0) }}</span>
                        </div>
                        @endif
                        <div style="color:rgba(255,255,255,.7);font-size:12px;margin-top:4px;">
                            {{ __('Custom Plan') }} &middot; {{ __('Assigned by Admin') }}
                        </div>
                    </div>

                    {{-- Body --}}
                    <div style="padding:20px 22px 22px;">
                        <ul style="list-style:none;padding:0;margin:0 0 20px;">
                            <li style="display:flex;align-items:center;gap:9px;padding:7px 0;
                                       font-size:13px;color:#374151;border-bottom:1px dashed #f0f0f0;">
                                <span style="width:22px;height:22px;border-radius:50%;background:#6366f118;
                                             display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="ti ti-users" style="color:#6366f1;font-size:13px;"></i>
                                </span>
                                {{ __('Up to') }} <strong style="margin:0 3px;">{{ $customSub->max_members }}</strong> {{ __('members') }}
                            </li>
                            <li style="display:flex;align-items:center;gap:9px;padding:7px 0;
                                       font-size:13px;color:#374151;border-bottom:1px dashed #f0f0f0;">
                                <span style="width:22px;height:22px;border-radius:50%;background:#6366f118;
                                             display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="ti ti-user-check" style="color:#6366f1;font-size:13px;"></i>
                                </span>
                                {{ __('Currently') }}: <strong style="margin:0 3px;">{{ $mess->getMemberCount() }}</strong> {{ __('members') }}
                            </li>
                            @if($customSub->expires_at)
                            <li style="display:flex;align-items:center;gap:9px;padding:7px 0;
                                       font-size:13px;color:#374151;border-bottom:1px dashed #f0f0f0;">
                                <span style="width:22px;height:22px;border-radius:50%;background:#6366f118;
                                             display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="ti ti-calendar" style="color:#6366f1;font-size:13px;"></i>
                                </span>
                                {{ __('Valid until') }}: <strong style="margin:0 3px;">{{ $customSub->expires_at->format('d M Y') }}</strong>
                            </li>
                            @endif
                        </ul>

                        <div style="background:#ede9fe;color:#4338ca;border-radius:10px;padding:10px 14px;
                                    text-align:center;font-size:13px;font-weight:700;border:1px solid #c4b5fd;">
                            <i class="ti ti-star me-1"></i>{{ __('Custom Plan Active') }}
                        </div>
                        @if($customSub->expires_at)
                        <div style="text-align:center;font-size:11px;color:#6b7280;margin-top:7px;">
                            <i class="ti ti-clock me-1"></i>
                            {{ max(0, (int) now()->diffInDays($customSub->expires_at, false)) }} {{ __('days left') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            @foreach($plans as $i => $plan)
            @php
                $color = $planColors[$i % count($planColors)];
                $grad  = $planGrads[$i % count($planGrads)];
                $icon  = $planIcons[$i % count($planIcons)];
                $popular = $i === 1; // highlight 2nd plan as "Popular"
            @endphp
            @php
                $isPending    = $pendingUpgrade && $pendingUpgrade->plan_id == $plan->id;
                $isLocked     = $pendingUpgrade && !$isPending;
                $isActivePlan = $activeSub && $activeSub->plan_id == $plan->id;
            @endphp
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div class="plan-card position-relative {{ $isLocked ? 'opacity-50' : '' }}"
                    style="border-radius:20px;
                           border:2px solid {{ $isActivePlan ? '#10b981' : ($isPending ? '#f59e0b' : ($popular ? $color : '#e8eaf0')) }};
                           background:#fff; overflow:hidden;
                           box-shadow: {{ $isActivePlan ? '0 8px 32px rgba(16,185,129,.22)' : ($isPending ? '0 8px 32px rgba(245,158,11,.25)' : ($popular ? '0 8px 32px rgba(0,0,0,.14)' : '0 2px 14px rgba(0,0,0,.07)')) }};
                           transition:all .25s ease;
                           cursor:{{ ($isActivePlan || $isPending || $isLocked) ? 'not-allowed' : 'pointer' }};"
                    data-plan-id="{{ ($isActivePlan || $isPending || $isLocked) ? '' : $plan->id }}"
                    data-max-members="{{ $plan->max_members }}"
                    data-price="{{ $plan->price }}"
                    data-name="{{ $plan->name }}"
                    data-duration="{{ $plan->duration_months }}"
                    data-color="{{ $color }}">

                    @if($isActivePlan)
                    <div style="position:absolute;top:14px;right:-28px;background:#10b981;color:#fff;
                                font-size:10px;font-weight:700;padding:4px 36px;transform:rotate(45deg);
                                letter-spacing:.5px;z-index:2;">{{ __('ACTIVE') }}</div>
                    @elseif($popular)
                    <div style="position:absolute;top:14px;right:-28px;background:#f72585;color:#fff;
                                font-size:10px;font-weight:700;padding:4px 36px;transform:rotate(45deg);
                                letter-spacing:.5px;z-index:2;">{{ __('POPULAR') }}</div>
                    @endif

                    {{-- Gradient Header --}}
                    <div style="background:{{ $grad }};padding:28px 20px 24px;text-align:center;position:relative;">
                        <div style="width:58px;height:58px;border-radius:50%;background:rgba(255,255,255,.2);
                                    display:flex;align-items:center;justify-content:center;margin:0 auto 12px;
                                    box-shadow:0 4px 12px rgba(0,0,0,.15);">
                            @if($isActivePlan)
                            <i class="ti ti-circle-check" style="font-size:30px;color:#fff;"></i>
                            @else
                            <i class="ti {{ $icon }}" style="font-size:26px;color:#fff;"></i>
                            @endif
                        </div>
                        <div style="color:rgba(255,255,255,.85);font-size:11px;font-weight:700;
                                    letter-spacing:1.5px;text-transform:uppercase;margin-bottom:10px;">
                            {{ $plan->name }}
                        </div>
                        <div style="color:#fff;line-height:1;">
                            <span style="font-size:15px;vertical-align:top;line-height:28px;font-weight:600;">৳</span>
                            <span style="font-size:42px;font-weight:800;">{{ number_format($plan->price, 0) }}</span>
                        </div>
                        <div style="color:rgba(255,255,255,.7);font-size:12px;margin-top:4px;">
                            per {{ $plan->duration_months }} month{{ $plan->duration_months > 1 ? 's' : '' }}
                        </div>
                    </div>

                    {{-- Features Body --}}
                    <div style="padding:20px 22px 22px;">
                        <ul style="list-style:none;padding:0;margin:0 0 20px;">
                            <li style="display:flex;align-items:center;gap:9px;padding:7px 0;
                                       font-size:13px;color:#374151;border-bottom:1px dashed #f0f0f0;">
                                <span style="width:22px;height:22px;border-radius:50%;background:{{ $color }}18;
                                             display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="ti ti-users" style="color:{{ $color }};font-size:13px;"></i>
                                </span>
                                Up to <strong style="margin:0 3px;">{{ $plan->max_members }}</strong> members
                            </li>
                            <li style="display:flex;align-items:center;gap:9px;padding:7px 0;
                                       font-size:13px;color:#374151;border-bottom:1px dashed #f0f0f0;">
                                <span style="width:22px;height:22px;border-radius:50%;background:{{ $color }}18;
                                             display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="ti ti-calendar" style="color:{{ $color }};font-size:13px;"></i>
                                </span>
                                Valid <strong style="margin:0 3px;">{{ $plan->duration_months }} month{{ $plan->duration_months > 1 ? 's' : '' }}</strong>
                            </li>
                            @if($plan->description)
                            @foreach(array_filter(array_map('trim', explode("\n", $plan->description))) as $line)
                            <li style="display:flex;align-items:flex-start;gap:9px;padding:7px 0;
                                       font-size:13px;color:#374151;border-bottom:1px dashed #f0f0f0;">
                                <span style="width:22px;height:22px;border-radius:50%;background:{{ $color }}18;
                                             display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                                    <i class="ti ti-check" style="color:{{ $color }};font-size:13px;"></i>
                                </span>
                                {{ $line }}
                            </li>
                            @endforeach
                            @endif
                        </ul>

                        @if($isActivePlan)
                        {{-- Active plan: tick + expiry --}}
                        <div style="background:#d1fae5;color:#065f46;border-radius:10px;padding:10px 14px;
                                    text-align:center;font-size:13px;font-weight:700;border:1px solid #6ee7b7;">
                            <i class="ti ti-circle-check me-1"></i>Current Plan
                        </div>
                        @if($activeSub->expires_at)
                        <div style="text-align:center;font-size:11px;color:#6b7280;margin-top:7px;">
                            <i class="ti ti-calendar me-1"></i>
                            Expires {{ $activeSub->expires_at->format('d M Y') }}
                            ({{ max(0, (int) now()->diffInDays($activeSub->expires_at, false)) }} days left)
                        </div>
                        @endif
                        @elseif($isPending)
                        <div style="background:#fef3c7;color:#b45309;border-radius:10px;padding:10px 16px;
                                    text-align:center;font-size:13px;font-weight:700;border:1px solid #fcd34d;">
                            <i class="ti ti-clock me-1"></i>Pending Review
                        </div>
                        @elseif($isLocked)
                        <div style="background:#f3f4f6;color:#9ca3af;border-radius:10px;padding:10px 16px;
                                    text-align:center;font-size:13px;font-weight:600;">
                            <i class="ti ti-lock me-1"></i>Locked
                        </div>
                        @else
                        <div class="plan-btn" style="background:{{ $grad }};color:#fff;border-radius:10px;
                                    padding:10px 16px;text-align:center;font-size:13px;font-weight:700;
                                    letter-spacing:.3px;box-shadow:0 4px 12px {{ $color }}44;">
                            <i class="ti ti-bolt me-1"></i>Subscribe Now
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</div>

{{-- bKash Payment Modal --}}
<div class="modal fade" id="bkashModal" tabindex="-1" aria-labelledby="bkashModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px;overflow:hidden;border:none;">

            {{-- Coloured modal header (colour set by JS) --}}
            <div id="modalHeader" style="padding:24px 24px 20px;background:linear-gradient(135deg,#4361ee,#7b8cde);">
                <div class="d-flex align-items-center gap-3">
                    <div id="modalIcon" style="width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,.2);
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ti ti-brand-cashapp" style="font-size:22px;color:#fff;"></i>
                    </div>
                    <div>
                        <div id="bkashModalLabel" style="color:#fff;font-size:16px;font-weight:700;line-height:1.2;">bKash Payment</div>
                        <div id="modalSubtitle" style="color:rgba(255,255,255,.75);font-size:12px;margin-top:2px;"></div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <div class="modal-body p-0">
                {{-- How to pay steps --}}
                <div style="background:#f8f9ff;padding:14px 22px;border-bottom:1px solid #f0f0f0;">
                    <div style="font-size:12px;color:#6b7280;font-weight:600;margin-bottom:8px;letter-spacing:.5px;text-transform:uppercase;">
                        <i class="ti ti-info-circle me-1"></i>How to Pay
                    </div>
                    <div class="d-flex gap-2">
                        @foreach(['Send bKash to <strong>01XXXXXXXXXX</strong>','Copy the Transaction ID','Fill the form & submit'] as $si => $step)
                        <div style="flex:1;background:#fff;border-radius:10px;padding:10px 8px;text-align:center;border:1px solid #e8eaf0;">
                            <div style="width:22px;height:22px;border-radius:50%;background:#4361ee;color:#fff;
                                        font-size:11px;font-weight:700;display:flex;align-items:center;
                                        justify-content:center;margin:0 auto 6px;">{{ $si+1 }}</div>
                            <div style="font-size:11px;color:#374151;">{!! $step !!}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Form --}}
                <form action="{{ route('mess.upgrade.store', $mess->id) }}" method="POST" style="padding:20px 22px 4px;">
                    @csrf
                    <input type="hidden" name="plan_id" id="fi_plan_id">
                    <input type="hidden" name="requested_limit" id="fi_requested_limit">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Amount (৳) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">৳</span>
                            <input type="number" name="amount" id="fi_amount" step="0.01" min="0"
                                class="form-control @error('amount') is-invalid @enderror"
                                value="{{ old('amount') }}" readonly>
                        </div>
                        @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Your bKash Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-phone"></i></span>
                            <input type="text" name="bkash_number"
                                class="form-control @error('bkash_number') is-invalid @enderror"
                                placeholder="01XXXXXXXXXX" value="{{ old('bkash_number') }}">
                        </div>
                        @error('bkash_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">bKash Transaction ID <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-hash"></i></span>
                            <input type="text" name="transaction_id"
                                class="form-control @error('transaction_id') is-invalid @enderror"
                                placeholder="e.g. 8N6A1O6EBN" value="{{ old('transaction_id') }}">
                        </div>
                        @error('transaction_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="modal-footer border-0 px-0 pt-0 pb-4">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" id="submitBtn" class="btn btn-primary flex-grow-1">
                            <i class="ti ti-send me-1"></i>Submit Upgrade Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    // Hover lift
    $(document).on('mouseenter', '.plan-card[data-plan-id]', function () {
        if (!$(this).data('plan-id')) return;
        $(this).css({ transform: 'translateY(-6px)', boxShadow: '0 14px 40px rgba(0,0,0,.14)' });
    }).on('mouseleave', '.plan-card[data-plan-id]', function () {
        $(this).css({ transform: '', boxShadow: $(this).data('shadow-orig') || '0 2px 14px rgba(0,0,0,.07)' });
    });

    // Click → open modal
    $(document).on('click', '.plan-card[data-plan-id]', function () {
        var $card = $(this);
        var planId = $card.data('plan-id');
        if (!planId) return;

        var name     = $card.data('name');
        var members  = $card.data('max-members');
        var price    = $card.data('price');
        var duration = $card.data('duration');
        var color    = $card.data('color');

        // Fill hidden fields
        $('#fi_plan_id').val(planId);
        $('#fi_requested_limit').val(members);
        $('#fi_amount').val(price);

        // Style modal header with plan colour
        var grad = 'linear-gradient(135deg,' + color + ',' + color + 'cc)';
        $('#modalHeader').css('background', grad);
        $('#submitBtn').css({ background: grad, border: 'none' });

        // Update modal text
        $('#bkashModalLabel').text(name + ' Plan');
        $('#modalSubtitle').html(
            '<i class="ti ti-users me-1"></i>' + members + ' members &nbsp;·&nbsp; ' +
            '<i class="ti ti-currency-taka me-1"></i>৳' + parseInt(price).toLocaleString() + ' / ' + duration + ' month' + (duration > 1 ? 's' : '')
        );

        new bootstrap.Modal(document.getElementById('bkashModal')).show();
    });

    // Auto-open modal if validation failed (re-populate from old input)
    @if($errors->any() && old('plan_id'))
    var modal = new bootstrap.Modal(document.getElementById('bkashModal'));
    $('#fi_plan_id').val('{{ old('plan_id') }}');
    $('#fi_requested_limit').val('{{ old('requested_limit') }}');
    $('#fi_amount').val('{{ old('amount') }}');
    modal.show();
    @endif
});
</script>
@endpush
@endsection
