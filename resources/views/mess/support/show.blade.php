@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
<div class="content">

{{-- Page Header --}}
<div class="page-header mb-4">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <a href="{{ route('mess.support.index', $mess) }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
            <i class="ti ti-arrow-left"></i>{{ __('Back') }}
        </a>
        <div>
            <h4 class="fw-bold mb-1">{{ $token->subject }}</h4>
            <div class="d-flex align-items-center gap-3 flex-wrap small text-muted">
                <span><i class="ti ti-hash me-1"></i><code class="text-primary fw-semibold">{{ $token->token }}</code></span>
                @php
                    $isExpired   = $token->isExpired();
                    $userMsgs    = $messages->where('sender_type', 'user');
                    $adminMsgs   = $messages->where('sender_type', 'admin');
                    $statusLabel = $token->status === 'open' && !$isExpired ? __('Open') : ($token->status === 'closed' ? __('Closed') : __('Expired'));
                    $badgeClass  = $token->status === 'open' && !$isExpired
                        ? 'bg-success-subtle text-success border-success-subtle'
                        : ($token->status === 'closed' ? 'bg-secondary-subtle text-secondary border-secondary-subtle' : 'bg-warning-subtle text-warning border-warning-subtle');
                @endphp
                <span class="badge border {{ $badgeClass }}">{{ $statusLabel }}</span>
                <span><i class="ti ti-message-2 me-1"></i>{{ $token->user_message_count }}/2 {{ __('messages used') }}</span>
                @if($token->status === 'open' && !$isExpired)
                <span class="text-success small"><i class="ti ti-clock me-1"></i>{{ __('Expires') }} {{ $token->expires_at->diffForHumans() }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="ti ti-circle-check fs-5 flex-shrink-0"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="ti ti-alert-circle fs-5 flex-shrink-0"></i>
    <div>{{ $errors->first() }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
<div class="col-lg-8">

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-0" style="border-bottom:none;">
        <li class="nav-item">
            <button class="nav-link active fw-semibold d-flex align-items-center gap-2" data-bs-toggle="tab" data-bs-target="#tab-my-messages">
                <i class="ti ti-message-2"></i>{{ __('My Messages') }}
                <span class="badge bg-secondary-subtle text-secondary border" style="font-size:10px;">{{ $userMsgs->count() }}/2</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-semibold d-flex align-items-center gap-2" data-bs-toggle="tab" data-bs-target="#tab-admin-response">
                <i class="ti ti-shield-check"></i>{{ __('Admin Response') }}
                @if($adminMsgs->count())
                <span class="badge bg-primary" style="font-size:10px;">{{ $adminMsgs->count() }}</span>
                @else
                <span class="badge bg-secondary-subtle text-secondary border" style="font-size:10px;">0</span>
                @endif
            </button>
        </li>
    </ul>

    <div class="tab-content card border-0 shadow-sm" style="border-radius:0 14px 14px 14px;overflow:hidden;">

        {{-- ===== TAB 1: My Messages ===== --}}
        <div class="tab-pane fade show active" id="tab-my-messages">

            {{-- Messages list --}}
            <div class="p-4" style="background:#fafbfc;">
                @if($userMsgs->isEmpty())
                <div class="text-center text-muted py-4">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle mb-3" style="width:52px;height:52px;">
                        <i class="ti ti-message-dots text-primary fs-3"></i>
                    </span>
                    <p class="small mb-0">{{ __('No messages sent yet. Use the form below to describe your issue.') }}</p>
                </div>
                @else
                <div class="d-flex flex-column gap-4">
                    @foreach($userMsgs as $msg)
                    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
                        <div class="card-header border-0 d-flex align-items-center gap-3 py-2 px-3" style="background:#f0f4ff;">
                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0" style="width:32px;height:32px;font-size:13px;font-weight:700;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold small text-dark">{{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $msg->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <p class="mb-0" style="font-size:14px;line-height:1.8;color:#374151;white-space:pre-wrap;">{{ $msg->message }}</p>
                        </div>
                        @if($msg->image_path)
                        <div class="card-footer bg-white border-top px-3 pb-3 pt-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-paperclip text-muted small"></i>
                                <span class="small text-muted fw-semibold">{{ __('Attached Image') }}</span>
                            </div>
                            <a href="{{ asset($msg->image_path) }}" target="_blank" class="d-inline-block">
                                <img src="{{ asset($msg->image_path) }}" alt="{{ __('Attachment') }}"
                                     class="rounded-3 img-fluid border"
                                     style="max-height:280px;max-width:100%;cursor:zoom-in;box-shadow:0 2px 8px rgba(0,0,0,.08);">
                            </a>
                            <div class="mt-2">
                                <a href="{{ asset($msg->image_path) }}" target="_blank"
                                   class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                                    <i class="ti ti-external-link"></i>{{ __('Open Full Image') }}
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Send Form --}}
            <div class="border-top bg-white p-4">
                @if($token->userCanMessage())
                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="ti ti-send text-primary"></i>{{ __('Send a Message') }}
                    <span class="badge bg-secondary-subtle text-secondary border ms-auto" style="font-size:11px;">
                        {{ 2 - $token->user_message_count }} {{ __('remaining') }}
                    </span>
                </h6>
                <form action="{{ route('mess.support.message', [$mess, $token]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4"
                                  placeholder="{{ __('Describe your issue in detail...') }}"
                                  maxlength="2000" required style="resize:vertical;">{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-semibold">
                            <i class="ti ti-paperclip me-1"></i>{{ __('Attach Image') }}
                            <span class="fw-normal">({{ __('optional, max 2MB') }})</span>
                        </label>
                        <input type="file" name="image" id="image-input" class="form-control" accept="image/*"
                               onchange="previewSupportImage(this)">
                        <div id="image-preview-wrap" class="mt-2 d-none">
                            <img id="image-preview" src="" alt="preview"
                                 class="rounded-3 border img-fluid" style="max-height:160px;">
                            <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 mt-2"
                                    onclick="clearSupportImage()">
                                <i class="ti ti-x"></i>{{ __('Remove') }}
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                        <i class="ti ti-send"></i>{{ __('Submit Message') }}
                    </button>
                </form>
                @else
                <div class="d-flex align-items-center gap-3 py-2">
                    <i class="ti ti-alert-triangle text-warning fs-4 flex-shrink-0"></i>
                    <div>
                        @if($token->status === 'closed')
                            <strong class="small">{{ __('Ticket Closed') }}</strong>
                            <div class="text-muted" style="font-size:12px;">{{ __('This ticket has been closed by the admin.') }}</div>
                        @elseif($isExpired)
                            <strong class="small">{{ __('Token Expired') }}</strong>
                            <div class="text-muted" style="font-size:12px;">{{ __('Please create a new support token if you need further help.') }}</div>
                        @else
                            <strong class="small">{{ __('Message Limit Reached') }}</strong>
                            <div class="text-muted" style="font-size:12px;">{{ __('You have used all 2 messages for this token.') }}</div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

        </div>

        {{-- ===== TAB 2: Admin Response ===== --}}
        <div class="tab-pane fade" id="tab-admin-response">
            <div class="p-4" style="background:#f4f8ff;min-height:300px;">
                @if($adminMsgs->isEmpty())
                <div class="text-center text-muted py-5">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle mb-3" style="width:56px;height:56px;">
                        <i class="ti ti-clock text-primary fs-3"></i>
                    </span>
                    <h6 class="fw-normal mb-1">{{ __('Awaiting Admin Response') }}</h6>
                    <p class="small text-muted mb-0">{{ __('The super admin will reply to your ticket here. Please check back later.') }}</p>
                </div>
                @else
                <div class="d-flex flex-column gap-4">
                    @foreach($adminMsgs as $msg)
                    <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
                        <div class="card-header border-0 d-flex align-items-center gap-3 py-2 px-3"
                             style="background:linear-gradient(135deg,#206bc4 0%,#1a56a8 100%);">
                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-white flex-shrink-0" style="width:32px;height:32px;">
                                <i class="ti ti-shield-check text-primary" style="font-size:13px;"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-white small">{{ __('Super Admin') }}</div>
                                <div class="text-white opacity-75" style="font-size:11px;">{{ $msg->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                            <span class="badge bg-white text-primary ms-auto" style="font-size:10px;">{{ __('Admin Reply') }}</span>
                        </div>
                        <div class="card-body p-3" style="background:#fff;">
                            <p class="mb-0" style="font-size:14px;line-height:1.8;color:#374151;white-space:pre-wrap;">{{ $msg->message }}</p>
                        </div>
                        @if($msg->image_path)
                        <div class="card-footer bg-white border-top px-3 pb-3 pt-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-paperclip text-muted small"></i>
                                <span class="small text-muted fw-semibold">{{ __('Attached Image') }}</span>
                            </div>
                            <a href="{{ asset($msg->image_path) }}" target="_blank" class="d-inline-block">
                                <img src="{{ asset($msg->image_path) }}" alt="{{ __('Attachment') }}"
                                     class="rounded-3 img-fluid border"
                                     style="max-height:280px;max-width:100%;cursor:zoom-in;box-shadow:0 2px 8px rgba(0,0,0,.08);">
                            </a>
                            <div class="mt-2">
                                <a href="{{ asset($msg->image_path) }}" target="_blank"
                                   class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                                    <i class="ti ti-external-link"></i>{{ __('Open Full Image') }}
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

    </div>{{-- end tab-content --}}

</div>

{{-- Right Sidebar --}}
<div class="col-lg-4">
    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
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
                <dd class="col-7 mb-3">{{ $token->created_at->format('d M Y') }}<br>
                    <span class="text-muted" style="font-size:11px;">{{ $token->created_at->format('h:i A') }}</span>
                </dd>

                @if($token->status === 'open' && !$isExpired)
                <dt class="col-5 text-muted fw-normal mb-3">{{ __('Expires') }}</dt>
                <dd class="col-7 mb-3 text-success fw-semibold">{{ $token->expires_at->diffForHumans() }}</dd>
                @endif

                <dt class="col-5 text-muted fw-normal mb-3">{{ __('Your msgs') }}</dt>
                <dd class="col-7 mb-3">
                    <div class="d-flex gap-1 mb-1">
                        @for($i = 0; $i < 2; $i++)
                        <div class="rounded-2" style="width:28px;height:7px;background:{{ $i < $token->user_message_count ? '#206bc4' : '#e5e7eb' }};"></div>
                        @endfor
                    </div>
                    <span class="text-muted">{{ $token->user_message_count }}/2 {{ __('used') }}</span>
                </dd>

                <dt class="col-5 text-muted fw-normal mb-0">{{ __('Admin replies') }}</dt>
                <dd class="col-7 mb-0 fw-semibold text-primary">{{ $adminMsgs->count() }}</dd>
            </dl>
        </div>
    </div>

    <div class="alert border-0 d-flex gap-3" style="background:#e8f0fe;border-radius:12px;">
        <i class="ti ti-bulb text-primary fs-5 flex-shrink-0 mt-1"></i>
        <div class="small text-muted lh-lg">
            {{ __('Switch to the "Admin Response" tab to see replies from the super admin. Attach a screenshot in your message to help explain the issue faster.') }}
        </div>
    </div>
</div>

</div>
</div>
</div>

@push('scripts')
<script>
function previewSupportImage(input){
    var wrap=document.getElementById('image-preview-wrap'),img=document.getElementById('image-preview');
    if(input.files&&input.files[0]){var r=new FileReader();r.onload=function(e){img.src=e.target.result;wrap.classList.remove('d-none');};r.readAsDataURL(input.files[0]);}
}
function clearSupportImage(){
    document.getElementById('image-input').value='';
    document.getElementById('image-preview').src='';
    document.getElementById('image-preview-wrap').classList.add('d-none');
}

// Auto-switch to admin response tab if there are unread replies
@if($adminMsgs->where('is_read', false)->count() > 0)
document.addEventListener('DOMContentLoaded', function(){
    var tab = document.querySelector('[data-bs-target="#tab-admin-response"]');
    if(tab) new bootstrap.Tab(tab).show();
});
@endif
</script>
@endpush
@endsection
