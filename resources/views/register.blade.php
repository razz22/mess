<?php $page = 'register'; ?>
@extends('layout.mainlayout')
@section('content')
<div class="account-content">
    <div class="login-wrapper register-wrap bg-img">
        <div class="login-content authent-content">
            <form action="{{ route('register.custom') }}" method="POST">
                @csrf
                <div class="login-userset">
                    <div class="login-logo logo-normal">
                        <a href="{{ url('/') }}">
                            <img src="{{URL::asset('build/img/logo.svg')}}" alt="Mess Manager">
                        </a>
                    </div>
                    <a href="{{ url('/') }}" class="login-logo logo-white">
                        <img src="{{URL::asset('build/img/logo-white.svg')}}" alt="Mess Manager">
                    </a>
                    <div class="login-userheading">
                        <h3>Create Account</h3>
                        <h4>Register to start managing your mess</h4>
                    </div>

                    @if($errors->any())
                    <div class="alert alert-danger py-2 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control border-end-0 @error('name') is-invalid @enderror"
                                placeholder="Your full name" required>
                            <span class="input-group-text border-start-0">
                                <i class="ti ti-user"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control border-end-0 @error('email') is-invalid @enderror"
                                placeholder="your@email.com" required>
                            <span class="input-group-text border-start-0">
                                <i class="ti ti-mail"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone (Optional)</label>
                        <div class="input-group">
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="form-control border-end-0"
                                placeholder="01XXXXXXXXX">
                            <span class="input-group-text border-start-0">
                                <i class="ti ti-phone"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="pass-group">
                            <input type="password" name="password" class="pass-input form-control"
                                placeholder="Minimum 6 characters" required>
                            <span class="ti toggle-password ti-eye-off text-gray-9"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <div class="pass-group">
                            <input type="password" name="password_confirmation" class="pass-inputs form-control"
                                placeholder="Repeat your password" required>
                            <span class="ti toggle-passwords ti-eye-off text-gray-9"></span>
                        </div>
                    </div>
                    <div class="form-login authentication-check mb-3">
                        <label class="checkboxs ps-4 mb-0 pb-0 line-height-1">
                            <input type="checkbox" required>
                            <span class="checkmarks"></span>
                            I agree to the <a href="#" class="text-primary">Terms & Privacy</a>
                        </label>
                    </div>
                    <div class="form-login">
                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </div>
                    <div class="signinform mt-3">
                        <h4>Already have an account? <a href="{{ route('signin') }}" class="hover-a">Sign In</a></h4>
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
                            Sign up with Google
                        </a>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="{{ url('/') }}" class="text-muted small"><i class="ti ti-arrow-left me-1"></i>Back to Home</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
