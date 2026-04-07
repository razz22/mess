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
                            <img src="{{URL::asset('build/img/logo.svg')}}" alt="Mess Manager">
                        </a>
                    </div>
                    <a href="{{ url('/') }}" class="login-logo logo-white">
                        <img src="{{URL::asset('build/img/logo-white.svg')}}" alt="Mess Manager">
                    </a>
                    <div class="login-userheading">
                        <h3>Sign In</h3>
                        <h4 class="fs-16">Welcome back! Sign in to manage your mess.</h4>
                    </div>

                    @if(session('success'))
                    <div class="alert alert-success py-2 mb-3">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                    <div class="alert alert-danger py-2 mb-3">{{ $errors->first() }}</div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control border-end-0 @error('email') is-invalid @enderror"
                                placeholder="Enter your email" required>
                            <span class="input-group-text border-start-0">
                                <i class="ti ti-mail"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="pass-group">
                            <input type="password" name="password" class="pass-input form-control" placeholder="Enter your password" required>
                            <span class="ti toggle-password ti-eye-off text-gray-9"></span>
                        </div>
                    </div>
                    <div class="form-login authentication-check">
                        <div class="row">
                            <div class="col-12 d-flex align-items-center justify-content-between">
                                <div class="custom-control custom-checkbox">
                                    <label class="checkboxs ps-4 mb-0 pb-0 line-height-1 fs-16 text-gray-6">
                                        <input type="checkbox" name="remember">
                                        <span class="checkmarks"></span>Remember me
                                    </label>
                                </div>
                                <div class="text-end">
                                    <a class="text-orange fs-16 fw-medium" href="{{ url('forgot-password') }}">Forgot Password?</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-login mt-3">
                        <button type="submit" class="btn btn-primary w-100">Sign In</button>
                    </div>
                    <div class="signinform mt-3">
                        <h4>New here? <a href="{{ route('register') }}" class="hover-a">Create an account</a></h4>
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
