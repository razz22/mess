<?php $page = "admin-settings" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-settings me-2 text-primary"></i>System Settings</h4>
                <h6 class="text-muted">Global defaults and login configuration</h6>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row g-3">

            {{-- Default Limits --}}
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="ti ti-adjustments me-2"></i>Default Limits</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="bkash_number" value="{{ $settings->bkash_number }}">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Default Member Limit <span class="text-danger">*</span></label>
                                <input type="number" name="default_max_members" min="1" max="1000"
                                    class="form-control @error('default_max_members') is-invalid @enderror"
                                    value="{{ old('default_max_members', $settings->default_max_members) }}">
                                <div class="form-text">Initial member limit for every new mess created.</div>
                                @error('default_max_members')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Default Mess Creation Limit <span class="text-danger">*</span></label>
                                <input type="number" name="default_max_messes" min="1" max="50"
                                    class="form-control @error('default_max_messes') is-invalid @enderror"
                                    value="{{ old('default_max_messes', $settings->default_max_messes) }}">
                                <div class="form-text">How many messes a user can create by default.</div>
                                @error('default_max_messes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-device-floppy me-1"></i>Save Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Google OAuth Settings --}}
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header" style="background: linear-gradient(135deg,#4285f4,#34a853); color:#fff;">
                        <h6 class="mb-0"><i class="ti ti-brand-google me-2"></i>Google Login (OAuth)</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            {{-- hidden: carry over other fields so they don't reset --}}
                            <input type="hidden" name="default_max_members" value="{{ $settings->default_max_members }}">
                            <input type="hidden" name="default_max_messes"  value="{{ $settings->default_max_messes }}">
                            <input type="hidden" name="bkash_number"        value="{{ $settings->bkash_number }}">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Google Client ID</label>
                                <input type="text" name="google_client_id"
                                    class="form-control @error('google_client_id') is-invalid @enderror"
                                    placeholder="xxxxxxxxxx.apps.googleusercontent.com"
                                    value="{{ old('google_client_id', $settings->google_client_id) }}">
                                @error('google_client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Google Client Secret</label>
                                <input type="password" name="google_client_secret"
                                    class="form-control @error('google_client_secret') is-invalid @enderror"
                                    placeholder="{{ $settings->google_client_secret ? '●●●●●●●●●●●●' : 'Enter secret' }}"
                                    value="{{ old('google_client_secret', '') }}">
                                <div class="form-text">Leave blank to keep existing secret.</div>
                                @error('google_client_secret')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Redirect URI (copy to Google Console)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm bg-light" readonly
                                        value="{{ route('auth.google.callback') }}" id="googleRedirectUri">
                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                        onclick="navigator.clipboard.writeText(document.getElementById('googleRedirectUri').value);this.innerHTML='<i class=\'ti ti-check\'></i>';setTimeout(()=>this.innerHTML='<i class=\'ti ti-copy\'></i>',1500)">
                                        <i class="ti ti-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-4 form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    name="google_login_enabled" id="googleLoginEnabled" value="1"
                                    {{ $settings->google_login_enabled ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="googleLoginEnabled">
                                    Enable Google Login
                                </label>
                            </div>
                            <button type="submit" class="btn w-100 text-white" style="background:#4285f4;">
                                <i class="ti ti-brand-google me-1"></i>Save Google Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- bKash Payment Number --}}
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header" style="background:linear-gradient(135deg,#e2136e,#f95c8a);color:#fff;">
                        <h6 class="mb-0"><i class="ti ti-brand-cashapp me-2"></i>bKash Payment Number</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            {{-- carry over other fields --}}
                            <input type="hidden" name="default_max_members" value="{{ $settings->default_max_members }}">
                            <input type="hidden" name="default_max_messes"  value="{{ $settings->default_max_messes }}">
                            <input type="hidden" name="google_client_id"    value="{{ $settings->google_client_id }}">
                            <input type="hidden" name="google_login_enabled" value="{{ $settings->google_login_enabled ? '1' : '0' }}">

                            <p class="text-muted small mb-3">This number will be shown to mess owners when they subscribe to a plan, so they know where to send the bKash payment.</p>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">bKash Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-phone"></i></span>
                                    <input type="text" name="bkash_number"
                                        class="form-control @error('bkash_number') is-invalid @enderror"
                                        placeholder="01XXXXXXXXXX"
                                        value="{{ old('bkash_number', $settings->bkash_number) }}"
                                        maxlength="20">
                                </div>
                                <div class="form-text">Leave blank to hide from the payment modal.</div>
                                @error('bkash_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn w-100 text-white" style="background:#e2136e;">
                                <i class="ti ti-device-floppy me-1"></i>Save bKash Number
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>{{-- /row --}}

    </div>
</div>
@endsection
