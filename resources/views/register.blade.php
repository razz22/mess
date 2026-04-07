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
                    <div class="mt-3 text-center">
                        <a href="{{ url('/') }}" class="text-muted small"><i class="ti ti-arrow-left me-1"></i>Back to Home</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
