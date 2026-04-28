@extends('layout.mainlayout')
@section('content')

<div class="page-wrapper">
<div class="content">

{{-- Page Header --}}
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <span class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width:36px;height:36px;">
                    <i class="ti ti-messages fs-5"></i>
                </span>
                {{ __('Suggestions to Admin') }}
            </h4>
            <p class="text-muted mb-0 small">{{ __('Send your suggestions or feedback directly to the super admin.') }}</p>
        </div>
    </div>
</div>

@if($user->is_blocked)
<div class="alert alert-danger d-flex align-items-center gap-3 mb-4" role="alert">
    <i class="ti ti-ban fs-4 flex-shrink-0"></i>
    <div>
        <strong>{{ __('Your account is currently blocked.') }}</strong>
        @if($user->blocked_until)
        {{ __('This block will expire on') }} <strong>{{ \Carbon\Carbon::parse($user->blocked_until)->format('d M Y, h:i A') }}</strong>.
        @else
        {{ __('This block is permanent. Contact the administrator for assistance.') }}
        @endif
        @if($user->block_reason)
        <br><span class="small">{{ __('Reason') }}: {{ $user->block_reason }}</span>
        @endif
    </div>
</div>
@endif

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">

            {{-- Chat Header --}}
            <div class="card-header border-0 d-flex align-items-center gap-3 py-3" style="background:linear-gradient(135deg,#206bc4 0%,#1a56a8 100%);">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-white" style="width:44px;height:44px;">
                    <i class="ti ti-shield-check text-primary fs-5"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-white fw-bold">{{ __('Super Admin') }}</h6>
                    <span class="text-white opacity-75 small">{{ __('Mess Management Support') }}</span>
                </div>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:10px;" id="ws-status-badge">
                        <i class="ti ti-circle-filled me-1" style="font-size:7px;" id="ws-dot"></i><span id="ws-label">{{ __('Connecting...') }}</span>
                    </span>
                </div>
            </div>

            {{-- Messages --}}
            <div class="p-4 overflow-auto" id="msg-scroll" style="height:450px;background:#f4f6fb;">
                @if($messages->isEmpty())
                <div class="text-center text-muted py-5" id="empty-state">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle" style="width:64px;height:64px;">
                            <i class="ti ti-message-dots text-primary fs-2"></i>
                        </span>
                    </div>
                    <h6 class="fw-normal">{{ __('Start the conversation') }}</h6>
                    <p class="text-muted small mb-0">{{ __('Send your first suggestion or feedback to the admin below.') }}</p>
                </div>
                @else
                <div class="text-center mb-4">
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2" style="font-size:11px;border-radius:20px;">
                        {{ __('This is your private conversation with the super admin.') }}
                    </span>
                </div>
                @endif

                <div id="msg-list">
                @foreach($messages as $msg)
                @php $isOwner = $msg->sender_role === 'owner'; @endphp
                <div class="d-flex {{ $isOwner ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                    @if(!$isOwner)
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0 me-2" style="width:32px;height:32px;font-size:12px;align-self:flex-end;">
                        <i class="ti ti-shield-check" style="font-size:14px;"></i>
                    </div>
                    @endif
                    <div style="max-width:75%;">
                        <div class="rounded-3 px-3 py-2 {{ $isOwner ? 'bg-primary text-white' : 'bg-white border' }}" style="box-shadow:0 1px 4px rgba(0,0,0,.08);">
                            <p class="mb-0" style="font-size:14px;line-height:1.6;white-space:pre-wrap;">{{ $msg->message }}</p>
                        </div>
                        <div class="mt-1 {{ $isOwner ? 'text-end' : '' }}" style="font-size:10px;color:#9ca3af;">
                            @if(!$isOwner)<i class="ti ti-shield-check me-1"></i>{{ __('Admin') }} &middot; @else {{ $mess->name }} &middot; @endif
                            {{ $msg->created_at->format('d M Y, h:i A') }}
                            @if($isOwner && $msg->is_read)&middot; <i class="ti ti-checks" title="{{ __('Read by admin') }}"></i>@endif
                        </div>
                    </div>
                    @if($isOwner)
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary flex-shrink-0 ms-2" style="width:32px;height:32px;font-size:13px;font-weight:700;align-self:flex-end;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    @endif
                </div>
                @endforeach
                </div>
            </div>

            {{-- Input Box --}}
            <div class="card-footer border-0 bg-white px-4 py-3">
                @if($user->is_blocked)
                <div class="text-center text-danger small py-2">
                    <i class="ti ti-ban me-1"></i>{{ __('You cannot send messages while your account is blocked.') }}
                </div>
                @else
                <form id="send-form" action="{{ route('mess.suggestions.store', $mess->id) }}" method="POST" onsubmit="return false;">
                    @csrf
                    <div class="d-flex gap-2 align-items-end">
                        <div class="flex-grow-1">
                            <textarea name="message" id="msg-input" class="form-control" rows="3"
                                      placeholder="{{ __('Write your suggestion, feedback, or feature request...') }}"
                                      style="resize:none;border-radius:12px;"
                                      maxlength="5000" required></textarea>
                            <div class="text-end mt-1">
                                <span class="text-muted" style="font-size:10px;" id="char-counter">0 / 5000</span>
                            </div>
                        </div>
                        <button type="button" id="send-btn" class="btn btn-primary d-flex align-items-center justify-content-center"
                                style="width:52px;height:52px;border-radius:50%;flex-shrink:0;margin-bottom:22px;">
                            <i class="ti ti-send fs-5"></i>
                        </button>
                    </div>
                </form>
                @endif
            </div>

        </div>

        <div class="alert alert-info d-flex gap-3 mt-3 border-0" style="background:#e8f0fe;border-radius:10px;">
            <i class="ti ti-info-circle text-primary fs-5 flex-shrink-0 mt-1"></i>
            <div class="small">
                <strong>{{ __('How suggestions work') }}:</strong>
                {{ __('Your messages are seen only by the super admin. The admin may reply to you here. This is not a public forum — all conversations are private.') }}
            </div>
        </div>
    </div>
</div>

</div>
</div>

@push('scripts')
<script src="{{ URL::asset('build/js/pusher.min.js') }}"></script>
<script>
var MESS_ID       = {{ $mess->id }};
var OWNER_INITIAL = {{ json_encode(strtoupper(substr($user->name, 0, 1))) }};
var MESS_NAME     = {{ json_encode($mess->name) }};
var SEND_URL      = {{ json_encode(route('mess.suggestions.store', $mess->id)) }};
var _csrf         = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var _body         = document.body;
var _rKey         = _body.getAttribute('data-reverb-key');
var _rHost        = _body.getAttribute('data-reverb-host') || 'localhost';
var _rPort        = parseInt(_body.getAttribute('data-reverb-port') || '8080');
var _rScheme      = _body.getAttribute('data-reverb-scheme') || 'http';

// ---- Helpers ----
function suggScrollBottom(){ var s = document.getElementById('msg-scroll'); if(s) s.scrollTop = s.scrollHeight; }
function suggEscHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function suggNowStr(){ return new Date().toLocaleString('en-GB',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}); }

function suggAppendMessage(data, isSelf){
    var empty = document.getElementById('empty-state');
    if(empty) empty.parentNode.removeChild(empty);
    var list = document.getElementById('msg-list');
    if(!list) return;
    var aL = '<div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0 me-2" style="width:32px;height:32px;font-size:12px;align-self:flex-end;"><i class="ti ti-shield-check" style="font-size:14px;"></i></div>';
    var aR = '<div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary flex-shrink-0 ms-2" style="width:32px;height:32px;font-size:13px;font-weight:700;align-self:flex-end;">' + OWNER_INITIAL + '</div>';
    var bubble = '<div style="max-width:75%;">'
        + '<div class="rounded-3 px-3 py-2 ' + (isSelf ? 'bg-primary text-white' : 'bg-white border') + '" style="box-shadow:0 1px 4px rgba(0,0,0,.08);">'
        + '<p class="mb-0" style="font-size:14px;line-height:1.6;white-space:pre-wrap;">' + suggEscHtml(data.message) + '</p>'
        + '</div><div class="mt-1 ' + (isSelf ? 'text-end' : '') + '" style="font-size:10px;color:#9ca3af;">'
        + (isSelf ? MESS_NAME : '<i class="ti ti-shield-check me-1"></i>{{ __("Admin") }}') + ' &middot; ' + (data.created_at || suggNowStr())
        + '</div></div>';
    var div = document.createElement('div');
    div.className = 'd-flex ' + (isSelf ? 'justify-content-end' : 'justify-content-start') + ' mb-3';
    div.innerHTML = isSelf ? (bubble + aR) : (aL + bubble);
    list.appendChild(div);
    suggScrollBottom();
}

// ---- Send button ----
var _sendBtn  = document.getElementById('send-btn');
var _msgInput = document.getElementById('msg-input');
var _charCnt  = document.getElementById('char-counter');

if(_msgInput){
    _msgInput.addEventListener('input', function(){
        if(_charCnt) _charCnt.textContent = this.value.length + ' / 5000';
    });
    _msgInput.addEventListener('keydown', function(e){
        if(e.ctrlKey && e.key === 'Enter' && _sendBtn) _sendBtn.click();
    });
}

if(_sendBtn){
    _sendBtn.addEventListener('click', function(){
        var txt = _msgInput ? _msgInput.value.trim() : '';
        if(!txt) return;
        suggAppendMessage({ message: txt, created_at: suggNowStr() }, true);
        _msgInput.value = '';
        if(_charCnt) _charCnt.textContent = '0 / 5000';
        _sendBtn.disabled = true;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', SEND_URL);
        xhr.setRequestHeader('X-CSRF-TOKEN', _csrf);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onloadend = function(){ _sendBtn.disabled = false; };
        xhr.send('message=' + encodeURIComponent(txt));
    });
}

// ---- WebSocket (for receiving admin replies in real-time) ----
function suggSetStatus(ok){
    var badge = document.getElementById('ws-status-badge');
    var dot   = document.getElementById('ws-dot');
    var label = document.getElementById('ws-label');
    if(!badge) return;
    badge.className = ok ? 'badge bg-success-subtle text-success border border-success-subtle' : 'badge bg-warning-subtle text-warning border border-warning-subtle';
    if(dot)   dot.style.color = ok ? '#2fb344' : '#f59f00';
    if(label) label.textContent = ok ? '{{ __("Live") }}' : '{{ __("Reconnecting...") }}';
}

if(_rKey && typeof Pusher !== 'undefined'){
    try {
        var _pusher = new Pusher(_rKey, {
            wsHost: _rHost, wsPort: _rPort, wssPort: _rPort,
            forceTLS: _rScheme === 'https',
            enabledTransports: ['ws','wss'],
            disableStats: true, cluster: 'mt1',
            authEndpoint: '/broadcasting/auth',
            auth: { headers: { 'X-CSRF-TOKEN': _csrf, 'X-Requested-With': 'XMLHttpRequest' } },
        });
        _pusher.connection.bind('connected',    function(){ suggSetStatus(true); });
        _pusher.connection.bind('disconnected', function(){ suggSetStatus(false); });
        _pusher.connection.bind('failed',       function(){ suggSetStatus(false); });
        var _ch = _pusher.subscribe('private-mess-suggestions.' + MESS_ID);
        _ch.bind('.message.sent', function(data){
            if(data.sender_role === 'admin') suggAppendMessage(data, false);
        });
    } catch(e){ suggSetStatus(false); }
} else {
    suggSetStatus(false);
}

suggScrollBottom();
</script>
@endpush
@endsection
