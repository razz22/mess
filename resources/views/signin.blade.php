<?php $page = 'signin'; ?>
@extends('layout.mainlayout')
@section('content')
<div class="account-content">
    <div class="login-wrapper bg-img">
        <div class="login-content authent-content">
            <form action="{{ route('signin.custom') }}" method="POST">
                @csrf
                <div class="login-userset">
                    <div class="login-logo logo-normal">
                        <a href="{{ url('/') }}">
                            <img src="{{URL::asset('build/img/logo.svg')}}" alt="Thaka Khawa">
                        </a>
                    </div>
                    <a href="{{ url('/') }}" class="login-logo logo-white">
                        <img src="{{URL::asset('build/img/logo-white.svg')}}" alt="Thaka Khawa">
                    </a>
                    <div class="login-userheading">
                        <h3>{{ __('Sign In') }}</h3>
                        <h4 class="fs-16">{{ __('Welcome back! Sign in to manage your mess.') }}</h4>
                    </div>

                    @if(session('success'))
                    <div class="alert alert-success py-2 mb-3">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                    <div class="alert alert-danger py-2 mb-3">{{ $errors->first() }}</div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control border-end-0 @error('email') is-invalid @enderror"
                                placeholder="{{ __('Email') }}" required>
                            <span class="input-group-text border-start-0">
                                <i class="ti ti-mail"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Password') }} <span class="text-danger">*</span></label>
                        <div class="pass-group">
                            <input type="password" name="password" class="pass-input form-control" placeholder="{{ __('Password') }}" required>
                            <span class="ti toggle-password ti-eye-off text-gray-9"></span>
                        </div>
                    </div>
                    <div class="form-login authentication-check">
                        <div class="row">
                            <div class="col-12 d-flex align-items-center justify-content-between">
                                <div class="custom-control custom-checkbox">
                                    <label class="checkboxs ps-4 mb-0 pb-0 line-height-1 fs-16 text-gray-6">
                                        <input type="checkbox" name="remember">
                                        <span class="checkmarks"></span>{{ __('Remember me') }}
                                    </label>
                                </div>
                                <div class="text-end">
                                    <a class="text-orange fs-16 fw-medium" href="{{ url('forgot-password') }}">{{ __('Forgot Password?') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-login mt-3">
                        <button type="submit" class="btn btn-primary w-100">{{ __('Sign In') }}</button>
                    </div>
                    <div class="signinform mt-3">
                        <h4>{{ __("Don't have an account?") }} <a href="{{ route('register') }}" class="hover-a">{{ __('Create Account') }}</a></h4>
                    </div>

                    <div class="mt-3 d-flex align-items-center gap-2">
                        <hr class="flex-grow-1 m-0"><span class="text-muted small px-2">OR</span><hr class="flex-grow-1 m-0">
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('auth.google') }}" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                                <path fill="#4285F4" d="M44.5 20H24v8.5h11.9C34.2 33.3 29.6 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.7 1.1 7.8 2.9l6.3-6.3C34.4 5.9 29.5 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20c11 0 20-8 20-20 0-1.3-.2-2.7-.5-4z"/>
                                <path fill="#34A853" d="M6.3 14.7l7 5.1C15 16.1 19.2 13 24 13c3 0 5.7 1.1 7.8 2.9l6.3-6.3C34.4 5.9 29.5 4 24 4 16.3 4 9.7 8.4 6.3 14.7z"/>
                                <path fill="#FBBC05" d="M24 44c5.4 0 10.3-1.8 14.1-4.9l-6.5-5.3C29.6 35.4 26.9 36 24 36c-5.6 0-10.2-3.7-11.9-8.7l-7 5.4C8.8 39.7 15.8 44 24 44z"/>
                                <path fill="#EA4335" d="M44.5 20H24v8.5h11.9c-.9 2.6-2.6 4.8-4.8 6.3l6.5 5.3C41.8 36.5 44 30.7 44 24c0-1.3-.2-2.7-.5-4z"/>
                            </svg>
                            {{ __('Continue with Google') }}
                        </a>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="{{ url('/') }}" class="text-muted small"><i class="ti ti-arrow-left me-1"></i>{{ __('Back to Home') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
