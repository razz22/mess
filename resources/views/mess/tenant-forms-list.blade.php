<?php $page = "mess-members" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">ভাড়াটিয়া নিবন্ধন ফরম</h4>
                <h6>{{ $mess->name }}</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('mess.members', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back to Members
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ti ti-file-description fs-1 text-primary d-block mb-3"></i>
                <h5 class="mb-2">ভাড়াটিয়া নিবন্ধন ফরম</h5>
                <p class="text-muted mb-4">Click below to download the blank Tenant Registration Form.</p>
                <a href="{{ asset('build/doc/Tenant-Registration-Form.pdf') }}" download="Tenant-Registration-Form.pdf" class="btn btn-success btn-lg">
                    <i class="ti ti-download me-2"></i>Download PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
