<?php $page = "mess-notices" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold mb-0"><i class="ti ti-speakerphone me-2 text-primary"></i>{{ __('Notice') }}</h4>
                <p class="text-muted small mb-0 mt-1">
                    <a href="{{ route('mess.notices.index', $mess->id) }}" class="text-muted text-decoration-none">
                        <i class="ti ti-arrow-left me-1"></i>{{ __('Back to Notices') }}
                    </a>
                </p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">

                {{-- Hero banner --}}
                <div class="notice-hero rounded-top-3 p-4 pb-5 position-relative overflow-hidden mb-n4"
                     style="background: linear-gradient(135deg, #1a56db 0%, #0e3a8a 100%);">
                    {{-- decorative circles --}}
                    <span class="position-absolute" style="width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.06);top:-60px;right:-60px;"></span>
                    <span class="position-absolute" style="width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.04);bottom:-30px;left:30px;"></span>

                    <div class="d-flex align-items-start justify-content-between gap-3 position-relative">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                                 style="width:52px;height:52px;background:rgba(255,255,255,.18);">
                                <i class="ti ti-speakerphone text-white" style="font-size:26px;"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    @if($notice->status === 'published')
                                    <span class="badge bg-success bg-opacity-90" style="font-size:11px;">
                                        <i class="ti ti-circle-check me-1"></i>{{ __('Published') }}
                                    </span>
                                    @else
                                    <span class="badge bg-warning text-dark" style="font-size:11px;">
                                        <i class="ti ti-pencil me-1"></i>{{ __('Draft') }}
                                    </span>
                                    @endif
                                    <span class="badge bg-white bg-opacity-20 text-white" style="font-size:11px;">
                                        <i class="ti ti-building-community me-1"></i>{{ $mess->name }}
                                    </span>
                                </div>
                                <h4 class="fw-bold text-white mb-0" style="line-height:1.35;">{{ $notice->title }}</h4>
                            </div>
                        </div>
                    </div>

                    {{-- Meta row --}}
                    <div class="d-flex align-items-center gap-4 mt-3 ps-1 position-relative" style="font-size:13px;">
                        <span class="d-flex align-items-center gap-2 text-white text-opacity-80">
                            <span class="d-flex align-items-center justify-content-center rounded-circle"
                                  style="width:26px;height:26px;background:rgba(255,255,255,.2);">
                                <i class="ti ti-user" style="font-size:13px;"></i>
                            </span>
                            {{ $notice->author->name }}
                        </span>
                        @if($notice->published_at)
                        <span class="d-flex align-items-center gap-1 text-white text-opacity-70">
                            <i class="ti ti-calendar" style="font-size:13px;"></i>
                            {{ $notice->published_at->format('d M Y') }}
                            &middot;
                            {{ $notice->published_at->format('h:i A') }}
                        </span>
                        <span class="text-white text-opacity-50 fst-italic" style="font-size:12px;">
                            {{ $notice->published_at->diffForHumans() }}
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Notice body card --}}
                <div class="card border-0 shadow" style="border-radius:0 0 12px 12px;">
                    <div class="card-body px-4 pt-5 pb-4">
                        <div class="notice-body">
                            {!! $notice->body !!}
                        </div>
                    </div>

                    <div class="card-footer bg-transparent border-top d-flex align-items-center justify-content-between px-4 py-3">
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <i class="ti ti-clock" style="font-size:13px;"></i>
                            @if($notice->published_at)
                                {{ __('Published') }} {{ $notice->published_at->diffForHumans() }}
                            @else
                                {{ __('Not published') }}
                            @endif
                        </div>
                        <a href="{{ route('mess.notices.index', $mess->id) }}"
                           class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                            <i class="ti ti-arrow-left"></i>{{ __('Back to Notices') }}
                        </a>
                    </div>
                </div>

                {{-- Previous / Next navigation --}}
                @if(isset($prev) || isset($next))
                <div class="row g-3 mt-2">
                    <div class="col-6">
                        @if($prev)
                        <a href="{{ route('mess.notices.show', [$mess->id, $prev->id]) }}"
                           class="card border-0 shadow-sm text-decoration-none notice-nav-card h-100 p-3 d-flex flex-row align-items-center gap-2">
                            <i class="ti ti-chevron-left text-primary flex-shrink-0 fs-5"></i>
                            <div class="min-w-0">
                                <div class="text-muted" style="font-size:11px;">{{ __('Previous') }}</div>
                                <div class="fw-semibold text-dark text-truncate small">{{ $prev->title }}</div>
                            </div>
                        </a>
                        @endif
                    </div>
                    <div class="col-6">
                        @if($next)
                        <a href="{{ route('mess.notices.show', [$mess->id, $next->id]) }}"
                           class="card border-0 shadow-sm text-decoration-none notice-nav-card h-100 p-3 d-flex flex-row align-items-center justify-content-end gap-2 text-end">
                            <div class="min-w-0">
                                <div class="text-muted" style="font-size:11px;">{{ __('Next') }}</div>
                                <div class="fw-semibold text-dark text-truncate small">{{ $next->title }}</div>
                            </div>
                            <i class="ti ti-chevron-right text-primary flex-shrink-0 fs-5"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>
</div>

<style>
.notice-hero { border-radius: 12px 12px 0 0; }
.notice-body {
    font-size: 15px;
    line-height: 1.8;
    color: #374151;
}
.notice-body h1,.notice-body h2,.notice-body h3,.notice-body h4 {
    font-weight: 700;
    margin-top: 1.4em;
    margin-bottom: .5em;
    color: #111827;
}
.notice-body p  { margin-bottom: .9em; }
.notice-body ul,.notice-body ol { padding-left: 1.6em; margin-bottom: .9em; }
.notice-body li { margin-bottom: .3em; }
.notice-body img { max-width:100%; border-radius:8px; margin:8px 0; box-shadow:0 2px 12px rgba(0,0,0,.1); }
.notice-body a  { color: #1a56db; text-decoration: underline; }
.notice-body blockquote {
    border-left: 4px solid #1a56db;
    background: #f0f5ff;
    margin: 1em 0;
    padding: .75em 1em;
    border-radius: 0 8px 8px 0;
    color: #374151;
}
.notice-body strong { color: #111827; }
.notice-nav-card {
    border-radius: 10px;
    transition: box-shadow .2s, transform .15s;
}
.notice-nav-card:hover {
    box-shadow: 0 4px 16px rgba(26,86,219,.15) !important;
    transform: translateY(-2px);
}
</style>
@endsection
