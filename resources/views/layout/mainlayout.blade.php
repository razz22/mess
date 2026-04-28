<!DOCTYPE html>
@if (!Route::is(['layout-horizontal','layout-detached','layout-modern','layout-two-column','layout-hovered','layout-boxed','layout-rtl','layout-dark']))
<html lang="en">
@endif
@if (Route::is(['layout-horizontal']))
<html lang="en" data-layout="horizontal">
@endif
@if (Route::is(['layout-detached']))
<html lang="en" data-layout="detached">
@endif
@if (Route::is(['layout-modern']))
<html lang="en" data-layout="modern">
@endif
@if (Route::is(['layout-two-column']))
<html lang="en" data-layout="twocolumn">
@endif
@if (Route::is(['layout-hovered']))
<html lang="en" data-layout="layout-hovered">
@endif
@if (Route::is(['layout-boxed']))
<html lang="en" data-layout="default" data-width="box">
@endif
@if (Route::is(['layout-rtl']))
<html lang="en" data-layout-mode="light_mode">
@endif
@if (Route::is(['layout-dark']))
<html lang="en" data-theme="dark">
@endif
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="POS - Bootstrap Admin Template">
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects">
    <meta name="author" content="Dreamguys - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mess Management System</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('/build/img/favicon.png')}}">

    @include('layout.partials.head')
</head>


@if (!Route::is(['under-maintenance', 'coming-soon', 'error-404', 'error-500','two-step-verification-3','two-step-verification-2','two-step-verification','email-verification-3','email-verification-2','email-verification','reset-password-3','reset-password-2','reset-password','forgot-password-3','forgot-password-2','forgot-password','register-3','register-2','register','signin-3','signin-2','signin','success','success-2','success-3','layout-horizontal',
'layout-hovered','layout-boxed','layout-rtl','pos','pos-2','pos-3','pos-4','pos-5']))

    <body
        @auth
        @php
            $__messId = session('active_mess_id');
        @endphp
        data-reverb-key="{{ config('broadcasting.connections.reverb.key') }}"
        data-reverb-host="{{ config('broadcasting.connections.reverb.options.host', 'localhost') }}"
        data-reverb-port="{{ config('broadcasting.connections.reverb.options.port', 8080) }}"
        data-reverb-scheme="{{ config('broadcasting.connections.reverb.options.scheme', 'http') }}"
        @if($__messId)
        data-mess-id="{{ $__messId }}"
        data-notices-latest-url="{{ route('mess.notices.latest', $__messId) }}"
        data-notices-markall-url="{{ route('mess.notices.markallread', $__messId) }}"
        @endif
        @endauth
    >
@endif
@if (Route::is(['layout-horizontal']))
<body class="menu-horizontal">
@endif
@if (Route::is(['layout-hovered']))
<body class="mini-sidebar expand-menu">
@endif
@if (Route::is(['layout-boxed']))
<body class="mini-sidebar layout-box-mode">
@endif
@if (Route::is(['layout-rtl']))
<body class="layout-mode-rtl">
@endif

@if (Route::is(['under-maintenance', 'coming-soon', 'error-404', 'error-500']))

    <body class="error-page">
@endif
@if (Route::is(['two-step-verification','email-verification','reset-password','forgot-password','register-2','register','signin','success']))

    <body class="account-page">
@endif

@if(Route::is(['two-step-verification-3','two-step-verification-2','success-2','success-3','signin-3','signin-2','reset-password-3','reset-password-2','forgot-password-3','forgot-password-2','email-verification-3','email-verification-2','register-3']))
    <body class="account-page bg-white">
@endif

@if(Route::is(['lock-screen']))
    <img src="{{URL::asset('build/img/bg/lock-screen-bg.png')}}" alt="bg" class="lock-screen-bg position-absolute img-fluid d-sm-none d-md-none d-lg-flex">
@endif

@if(Route::is(['pos','pos-2','pos-3','pos-4','pos-5']))
<body class="pos-page">
@endif
@component('components.loader')
@endcomponent
<!-- Main Wrapper -->
@if (!Route::is(['lock-screen','pos','pos-3','pos-4','pos-5']))
    <div class="main-wrapper">
@endif
@if (Route::is(['lock-screen']))
    <div class="main-wrapper login-body">
@endif
@if (Route::is(['pos']))

<div class="main-wrapper pos-five">
@endif
@if (Route::is(['pos-3']))
<div class="main-wrapper pos-two">
@endif
@if (Route::is(['pos-4']))
<div class="main-wrapper pos-three">
@endif
@if (Route::is(['pos-5']))
<div class="main-wrapper pos-three pos-four">
 @endif
@if (!Route::is(['under-maintenance', 'coming-soon','error-404','error-500','two-step-verification-3','two-step-verification-2','two-step-verification','email-verification-3','email-verification-2','email-verification','reset-password-3','reset-password-2','reset-password','forgot-password-3','forgot-password-2','forgot-password','register-3','register-2','register','signin-3','signin-2','signin','success','success-2','success-3','lock-screen']))
    @include('layout.partials.header')
@endif
@if (!Route::is(['under-maintenance', 'coming-soon','error-404','error-500','two-step-verification-3','two-step-verification-2','two-step-verification','email-verification-3','email-verification-2','email-verification','reset-password-3','reset-password-2','reset-password','forgot-password-3','forgot-password-2','forgot-password','register-3','register-2','register','signin-3','signin-2','signin','success','success-2','success-3','lock-screen']))
    @include('layout.partials.sidebar')
    @include('layout.partials.collapsed-sidebar')
    @include('layout.partials.horizontal-sidebar')
@endif
{{-- Impersonation Banner --}}
@if(session('impersonating_admin_id'))
<div style="position:fixed;top:0;left:0;right:0;z-index:9999;background:#dc3545;color:#fff;text-align:center;padding:6px 16px;font-size:13px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:12px;">
    <i class="ti ti-eye"></i>
    Viewing as <strong>&nbsp;{{ Auth::user()->name }}&nbsp;</strong> — Super Admin Impersonation
    <form action="{{ route('admin.exit-impersonation') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" style="background:#fff;color:#dc3545;border:none;border-radius:4px;padding:2px 10px;font-size:12px;font-weight:700;cursor:pointer;">Exit</button>
    </form>
</div>
@endif

{{-- Subscription Expiry Alert --}}
@auth
@php
    $__activeMess = Auth::user()->getActiveMess();
    $__sub = $__activeMess?->subscription;
    $__daysLeft = $__sub?->expires_at ? (int) now()->diffInDays($__sub->expires_at, false) : null;
    $__showAlert = $__sub && $__daysLeft !== null && $__daysLeft <= 7 && $__daysLeft >= 0;
    $__expired   = $__sub && $__daysLeft !== null && $__daysLeft < 0;
@endphp
@if($__showAlert || $__expired)
<div style="background:{{ $__expired ? '#dc3545' : ($__daysLeft <= 3 ? '#f59e0b' : '#3b82f6') }};
            color:#fff;padding:10px 24px;display:flex;align-items:center;justify-content:space-between;
            gap:12px;font-size:13px;font-weight:500;flex-wrap:wrap;">
    <div class="d-flex align-items-center gap-2">
        <i class="ti ti-{{ $__expired ? 'alert-circle' : 'clock' }} fs-5 flex-shrink-0"></i>
        @if($__expired)
            Your <strong>&nbsp;{{ $__sub->plan }}&nbsp;</strong> subscription has <strong>expired</strong>. Renew now to restore your member limit.
        @elseif($__daysLeft === 0)
            Your <strong>&nbsp;{{ $__sub->plan }}&nbsp;</strong> subscription expires <strong>today</strong>!
        @else
            Your <strong>&nbsp;{{ $__sub->plan }}&nbsp;</strong> subscription expires in <strong>&nbsp;{{ $__daysLeft }} day{{ $__daysLeft > 1 ? 's' : '' }}&nbsp;</strong> on {{ $__sub->expires_at->format('d M Y') }}.
        @endif
    </div>
    @if($__activeMess && Auth::user()->isManagerOf($__activeMess->id))
    <a href="{{ route('mess.upgrade', $__activeMess->id) }}"
       style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.5);
              border-radius:6px;padding:5px 14px;font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;">
        <i class="ti ti-rocket me-1"></i>Renew Now
    </a>
    @endif
</div>
@endif
@endauth

@yield('content')
</div>
<!-- /Main Wrapper -->

@component('components.modalpopup')
@endcomponent
@include('layout.partials.footer-scripts')
@stack('scripts')
</body>

</html>
