@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
<div class="content">

<div class="page-header mb-4">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <a href="{{ route('admin.support.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
            <i class="ti ti-arrow-left"></i>{{ __('Back') }}
        </a>
        <div class="flex-grow-1">
            <h4 class="fw-bold mb-1">{{ $token->subject }}</h4>
            <div class="d-flex align-items-center gap-3 flex-wrap small text-muted">
                <span><i class="ti ti-hash me-1"></i><code class="text-primary fw-semibold">{{ $token->token }}</code></span>
                @php
                    $isExpired   = $token->isExpired();
                    $statusLabel = $token->status === 'open' && !$isExpired ? __('Open') : ($token->status === 'closed' ? __('Closed') : __('Expired'));
                    $badgeClass  = $token->status === 'open' && !$isExpired
                        ? 'bg-success-subtle text-success border-success-subtle'
                        : ($token->status === 'closed' ? 'bg-secondary-subtle text-secondary border-secondary-subtle' : 'bg-warning-subtle text-warning border-warning-subtle');
                @endphp
                <span class="badge border {{ $badgeClass }}">{{ $statusLabel }}</span>
                <span><i class="ti ti-building me-1"></i>{{ $token->mess->name }}</span>
                <span><i class="ti ti-user me-1"></i>{{ $token->user->name }} &middot; <strong>{{ $role }}</strong></span>
            </div>
        </div>
        @if($token->status === 'open')
        <form action="{{ route('admin.support.close', $token) }}" method="POST" onsubmit="return confirm('{{ __('Close this ticket?') }}')">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                <i class="ti ti-lock"></i>{{ __('Close Ticket') }}
            </button>
        </form>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="ti ti-circle-check fs-5 flex-shrink-0"></i><div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
<div class="col-lg-8">

    {{-- Messages as individual detail cards --}}
    @if($messages->isEmpty())
    <div class="card border-0 shadow-sm text-center py-5" style="border-radius:14px;">
        <div class="card-body">
            <i class="ti ti-message-dots fs-1 text-primary opacity-50 d-block mb-2"></i>
            <h6 class="fw-normal text-muted">{{ __('No messages yet on this ticket.') }}</h6>
        </div>
    </div>
    @else
    <div class="d-flex flex-column gap-4">
        @foreach($messages as $msg)
        @php $isAdmin = $msg->sender_type === 'admin'; @endphp
        <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">

            {{-- Message Header --}}
            <div class="card-header border-0 d-flex align-items-center gap-3 py-3 px-4"
                 style="{{ $isAdmin ? 'background:linear-gradient(135deg,#206bc4 0%,#1a56a8 100%);' : 'background:#f8f9fb;' }}">
                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                     style="width:40px;height:40px;{{ $isAdmin ? 'background:rgba(255,255,255,0.2);' : 'background:#e8f0fe;' }}">
                    @if($isAdmin)
                        <i class="ti ti-shield-check text-white fs-5"></i>
                    @else
                        <span class="fw-bold" style="color:#206bc4;font-size:15px;">{{ strtoupper(substr($token->user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold {{ $isAdmin ? 'text-white' : 'text-dark' }}">
                        {{ $isAdmin ? __('Super Admin') : $token->user->name }}
                    </div>
                    <div class="small {{ $isAdmin ? 'text-white opacity-75' : 'text-muted' }}">
                        @if(!$isAdmin){{ $token->mess->name }} &middot; {{ $role }} &middot; @endif
                        {{ $msg->created_at->format('d M Y, h:i A') }}
                    </div>
                </div>
                <span class="badge {{ $isAdmin ? 'bg-white text-primary' : 'bg-info-subtle text-info border border-info-subtle' }}" style="font-size:11px;">
                    {{ $isAdmin ? __('Admin Reply') : __('User Message') }}
                </span>
            </div>

            {{-- Message Body --}}
            <div class="card-body p-4">
                <p class="mb-0" style="font-size:15px;line-height:1.8;color:#374151;white-space:pre-wrap;">{{ $msg->message }}</p>
            </div>

            {{-- Attachment --}}
            @if($msg->image_path)
            <div class="card-footer border-top bg-white px-4 pb-4 pt-0">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="ti ti-paperclip text-muted"></i>
                    <span class="small text-muted fw-semibold">{{ __('Attached Image') }}</span>
                </div>
                <a href="{{ asset($msg->image_path) }}" target="_blank" class="d-inline-block">
                    <img src="{{ asset($msg->image_path) }}" alt="{{ __('Attachment') }}"
                         class="rounded-3 img-fluid border"
                         style="max-height:320px;max-width:100%;cursor:zoom-in;box-shadow:0 2px 8px rgba(0,0,0,.1);">
                </a>
                <div class="mt-2">
                    <a href="{{ asset($msg->image_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                        <i class="ti ti-external-link"></i>{{ __('Open Full Image') }}
                    </a>
                </div>
            </div>
            @endif

        </div>
        @endforeach
    </div>
    @endif

    {{-- Admin Reply Form --}}
    @if($token->status !== 'closed')
    <div class="card border-0 shadow-sm mt-4" style="border-radius:14px;overflow:hidden;">
        <div class="card-header border-0 py-3 px-4" style="background:#f8f9fb;">
            <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="ti ti-send text-primary"></i>{{ __('Send Reply') }}
            </h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.support.reply', $token) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Your Response') }} <span class="text-danger">*</span></label>
                    <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="5"
                              placeholder="{{ __('Write your response to the user...') }}"
                              maxlength="2000" required style="resize:vertical;">{{ old('message') }}</textarea>
                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold small">
                        <i class="ti ti-paperclip me-1"></i>{{ __('Attach Image') }}
                        <span class="fw-normal text-muted">({{ __('optional, max 2MB') }})</span>
                    </label>
                    <input type="file" name="image" id="admin-img-input" class="form-control" accept="image/*"
                           onchange="prevAdminImg(this)">
                    <div id="admin-img-wrap" class="mt-2 d-none">
                        <img id="admin-img-prev" src="" class="rounded-3 border img-fluid" style="max-height:160px;">
                        <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 mt-2"
                                onclick="clearAdminImg()">
                            <i class="ti ti-x"></i>{{ __('Remove') }}
                        </button>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4">
                        <i class="ti ti-send"></i>{{ __('Send Reply') }}
                    </button>
                    <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-2"
                            onclick="if(confirm('{{ __('Close this ticket?') }}')) document.getElementById('close-form').submit();">
                        <i class="ti ti-lock"></i>{{ __('Close After Reply') }}
                    </button>
                </div>
            </form>

            {{-- Standalone close form (outside reply form) --}}
            <form id="close-form" action="{{ route('admin.support.close', $token) }}" method="POST" class="d-none">
                @csrf @method('PATCH')
            </form>
        </div>
    </div>
    @else
    <div class="alert border-0 mt-4 d-flex align-items-center gap-3" style="background:#f1f3f5;border-radius:12px;">
        <i class="ti ti-lock text-secondary fs-4 flex-shrink-0"></i>
        <div>
            <strong>{{ __('Ticket Closed') }}</strong>
            <div class="small text-muted">{{ __('This ticket has been closed. No further replies can be sent.') }}</div>
        </div>
    </div>
    @endif

</div>

{{-- Right Sidebar: Ticket Info --}}
<div class="col-lg-4">

    {{-- User Info --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
        <div class="card-header border-0 py-3 px-4" style="background:#f8f9fb;">
            <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="ti ti-user text-primary"></i>{{ __('Submitted By') }}
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0"
                     style="width:44px;height:44px;font-size:16px;font-weight:700;">
                    {{ strtoupper(substr($token->user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="fw-semibold">{{ $token->user->name }}</div>
                    <div class="text-muted small">{{ $token->user->email }}</div>
                </div>
            </div>
            <dl class="row g-0 mb-0 small">
                <dt class="col-5 text-muted fw-normal mb-2">{{ __('Mess') }}</dt>
                <dd class="col-7 mb-2 fw-semibold">{{ $token->mess->name }}</dd>
                <dt class="col-5 text-muted fw-normal mb-2">{{ __('Role') }}</dt>
                <dd class="col-7 mb-2"><span class="badge bg-info-subtle text-info border border-info-subtle">{{ $role }}</span></dd>
                <dt class="col-5 text-muted fw-normal mb-0">{{ __('Messages') }}</dt>
                <dd class="col-7 mb-0">{{ $token->user_message_count }}/2 {{ __('used') }}</dd>
            </dl>
        </div>
    </div>

    {{-- Ticket Info --}}
    <div class="card border-0 shadow-sm" style="border-radius:14px;">
        <div class="card-header border-0 py-3 px-4" style="background:#f8f9fb;">
            <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="ti ti-ticket text-primary"></i>{{ __('Ticket Details') }}
            </h6>
        </div>
        <div class="card-body p-4">
            <dl class="row g-0 mb-0 small">
                <dt class="col-5 text-muted fw-normal mb-3">{{ __('Token') }}</dt>
                <dd class="col-7 mb-3"><code class="text-primary fw-semibold">{{ $token->token }}</code></dd>

                <dt class="col-5 text-muted fw-normal mb-3">{{ __('Status') }}</dt>
                <dd class="col-7 mb-3"><span class="badge border {{ $badgeClass }}">{{ $statusLabel }}</span></dd>

                <dt class="col-5 text-muted fw-normal mb-3">{{ __('Created') }}</dt>
                <dd class="col-7 mb-3">{{ $token->created_at->format('d M Y, h:i A') }}</dd>

                <dt class="col-5 text-muted fw-normal mb-3">{{ __('Expires') }}</dt>
                <dd class="col-7 mb-3">{{ $token->expires_at->format('d M Y, h:i A') }}</dd>

                <dt class="col-5 text-muted fw-normal mb-0">{{ __('Total Msgs') }}</dt>
                <dd class="col-7 mb-0">{{ $messages->count() }}</dd>
            </dl>
        </div>
    </div>

</div>
</div>

</div>
</div>
@push('scripts')
<script>
function prevAdminImg(i){ var w=document.getElementById('admin-img-wrap'),p=document.getElementById('admin-img-prev'); if(i.files&&i.files[0]){var r=new FileReader();r.onload=function(e){p.src=e.target.result;w.classList.remove('d-none');};r.readAsDataURL(i.files[0]);} }
function clearAdminImg(){ document.getElementById('admin-img-input').value='';document.getElementById('admin-img-prev').src='';document.getElementById('admin-img-wrap').classList.add('d-none'); }
</script>
@endpush
@endsection
