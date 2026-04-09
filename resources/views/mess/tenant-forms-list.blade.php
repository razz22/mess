<?php $page = "mess-members" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">ভাড়াটিয়া নিবন্ধন ফরম</h4>
                <h6>{{ $mess->name }} — সকল সদস্যের ফরম</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('mess.members', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back to Members
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        @php
            $submitted = $forms->where('submitted_at', '!=', null);
            $drafts    = $forms->whereNull('submitted_at');
        @endphp

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-sm-4">
                <div class="card border-0 bg-success-subtle text-center py-3">
                    <div class="fs-2 fw-bold text-success">{{ $submitted->count() }}</div>
                    <div class="text-muted small">Submitted Forms</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card border-0 bg-warning-subtle text-center py-3">
                    <div class="fs-2 fw-bold text-warning">{{ $drafts->count() }}</div>
                    <div class="text-muted small">Draft / Incomplete</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card border-0 bg-secondary-subtle text-center py-3">
                    <div class="fs-2 fw-bold">{{ $forms->count() }}</div>
                    <div class="text-muted small">Total Forms</div>
                </div>
            </div>
        </div>

        <!-- Forms Grid -->
        <div class="row g-4">
            @forelse($forms as $form)
            @php $u = $form->member->user; @endphp
            <div class="col-xl-4 col-lg-6">
                <div class="card h-100 border-{{ $form->isSubmitted() ? 'success' : 'warning' }} shadow-sm">
                    <!-- Card Header -->
                    <div class="card-header bg-{{ $form->isSubmitted() ? 'success' : 'warning' }} {{ $form->isSubmitted() ? 'text-white' : '' }} py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">{{ $form->tenant_name ?: $u->name }}</h6>
                        <span class="badge bg-{{ $form->isSubmitted() ? 'white text-success' : 'dark' }}">
                            {{ $form->isSubmitted() ? 'Submitted' : 'Draft' }}
                        </span>
                    </div>

                    <!-- Form Preview — mimics the official form layout -->
                    <div class="card-body p-3" style="font-size:12px">
                        <!-- Photo + Basic -->
                        <div class="d-flex gap-3 mb-3">
                            <div>
                                @if($form->passport_photo)
                                <img src="{{ asset('storage/'.$form->passport_photo) }}" alt="" style="width:70px;height:90px;object-fit:cover;border:1px solid #dee2e6">
                                @else
                                <div style="width:70px;height:90px;border:1px dashed #aaa;display:flex;align-items:center;justify-content:center;background:#f8f9fa" class="text-muted small text-center">
                                    No<br>Photo
                                </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <table class="table table-sm table-borderless mb-0" style="font-size:11px">
                                    <tr><td class="text-muted py-0">নাম</td><td class="py-0 fw-semibold">{{ $form->tenant_name ?: '—' }}</td></tr>
                                    <tr><td class="text-muted py-0">পিতার নাম</td><td class="py-0">{{ $form->father_name ?: '—' }}</td></tr>
                                    <tr><td class="text-muted py-0">জন্ম তারিখ</td><td class="py-0">{{ $form->date_of_birth?->format('d/m/Y') ?? '—' }}</td></tr>
                                    <tr><td class="text-muted py-0">বৈবাহিক</td><td class="py-0">{{ $form->marital_status ?: '—' }}</td></tr>
                                    <tr><td class="text-muted py-0">NID</td><td class="py-0 text-monospace">{{ $form->nid_number ?: '—' }}</td></tr>
                                </table>
                            </div>
                        </div>

                        <!-- Location -->
                        @if($form->flat_floor || $form->house_holding)
                        <div class="p-2 bg-light rounded mb-2" style="font-size:11px">
                            <i class="ti ti-map-pin me-1 text-muted"></i>
                            {{ collect([$form->flat_floor, $form->house_holding, $form->road, $form->area, $form->post_code])->filter()->implode(', ') }}
                            @if($form->police_station) | থানাঃ {{ $form->police_station }} @endif
                        </div>
                        @endif

                        <!-- Contact Row -->
                        <div class="d-flex gap-3 flex-wrap mb-2" style="font-size:11px">
                            @if($form->mobile)<span><i class="ti ti-phone me-1 text-muted"></i>{{ $form->mobile }}</span>@endif
                            @if($form->religion)<span><i class="ti ti-star me-1 text-muted"></i>{{ $form->religion }}</span>@endif
                            @if($form->marital_status)<span class="badge bg-secondary">{{ $form->marital_status }}</span>@endif
                        </div>

                        <!-- Emergency -->
                        @if($form->emergency_name)
                        <div class="border-top pt-2 mt-1" style="font-size:11px">
                            <span class="text-muted">জরুরী যোগাযোগঃ</span>
                            <strong>{{ $form->emergency_name }}</strong>
                            @if($form->emergency_relation)({{ $form->emergency_relation }})@endif
                            @if($form->emergency_mobile) — {{ $form->emergency_mobile }}@endif
                        </div>
                        @endif

                        <!-- Family members count -->
                        @if($form->family_members && count(array_filter($form->family_members, fn($fm) => !empty($fm['name']))) > 0)
                        @php $count = count(array_filter($form->family_members, fn($fm) => !empty($fm['name']))); @endphp
                        <div class="mt-1" style="font-size:11px">
                            <i class="ti ti-users me-1 text-muted"></i>{{ $count }} সঙ্গীয় সদস্য
                        </div>
                        @endif

                        <!-- Submitted date -->
                        @if($form->isSubmitted())
                        <div class="border-top mt-2 pt-2 text-success" style="font-size:11px">
                            <i class="ti ti-circle-check me-1"></i>Submitted: {{ $form->submitted_at->format('d M Y, h:i A') }}
                        </div>
                        @endif
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="card-footer py-2 d-flex gap-2">
                        <a href="{{ route('mess.tenant-forms.view', [$mess->id, $form->id]) }}"
                           class="btn btn-sm btn-outline-primary flex-grow-1">
                            <i class="ti ti-eye me-1"></i>View
                        </a>
                        <a href="{{ route('mess.tenant-forms.download', [$mess->id, $form->id]) }}"
                           class="btn btn-sm btn-success" title="Download PDF">
                            <i class="ti ti-download"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card py-5 text-center">
                    <div class="text-muted">
                        <i class="ti ti-file-off fs-1 d-block mb-3 opacity-30"></i>
                        <h5>No tenant registration forms yet</h5>
                        <p>Members can fill the form from their dashboard.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
