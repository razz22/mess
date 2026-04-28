@extends('layout.mainlayout')
@section('content')

<div class="page-wrapper">
<div class="content">

<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <span class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width:36px;height:36px;">
                    <i class="ti ti-messages fs-5"></i>
                </span>
                {{ __('Owner Suggestions') }}
                @if($totalUnread > 0)
                <span class="badge bg-danger" id="total-unread-badge" style="font-size:11px;">{{ $totalUnread }} {{ __('new') }}</span>
                @else
                <span class="badge bg-danger d-none" id="total-unread-badge" style="font-size:11px;"></span>
                @endif
            </h4>
            <p class="text-muted mb-0 small">{{ __('Messages from mess owners. Reply privately to each owner.') }}</p>
        </div>
        <span class="badge border" id="ws-status-badge" style="font-size:11px;">
            <i class="ti ti-circle-filled me-1" style="font-size:7px;" id="ws-dot"></i><span id="ws-label">{{ __('Connecting...') }}</span>
        </span>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ti ti-circle-check me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-0" style="height:calc(100vh - 220px);min-height:500px;">

    {{-- Thread List --}}
    <div class="col-md-4 col-lg-3 border-end" style="overflow-y:auto;background:#f8f9fa;">
        <div class="p-3 border-bottom bg-white sticky-top">
            <input type="text" id="thread-search" class="form-control form-control-sm" placeholder="{{ __('Search mess or owner...') }}">
        </div>
        @if($threads->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="ti ti-inbox fs-1 d-block mb-2"></i>
            <span class="small">{{ __('No messages yet.') }}</span>
        </div>
        @else
        <div id="thread-list">
        @foreach($threads as $mess)
        @php
            $lastMsg  = $mess->adminMessages->first();
            $unread   = $mess->unread_count;
            $isActive = $selectedMess && $selectedMess->id == $mess->id;
        @endphp
        <a href="{{ route('admin.suggestions.index', ['mess_id' => $mess->id]) }}"
           data-mess-id="{{ $mess->id }}"
           class="d-flex align-items-start gap-3 p-3 text-decoration-none border-bottom thread-item {{ $isActive ? 'bg-primary-subtle' : 'bg-white' }}"
           style="transition:.15s;">
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0" style="width:42px;height:42px;font-size:16px;font-weight:700;">
                {{ strtoupper(substr($mess->name, 0, 1)) }}
            </div>
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="fw-semibold text-dark small text-truncate" style="max-width:130px;">{{ $mess->name }}</span>
                    @if($lastMsg)
                    <span class="text-muted thread-time-{{ $mess->id }}" style="font-size:10px;white-space:nowrap;">{{ $lastMsg->created_at->diffForHumans(null, true) }}</span>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <span class="text-muted small text-truncate" style="font-size:11px;max-width:130px;">{{ $mess->owner->name }}</span>
                    <span class="badge bg-danger rounded-pill thread-badge-{{ $mess->id }} {{ $unread > 0 ? '' : 'd-none' }}" style="font-size:10px;">{{ $unread ?: '' }}</span>
                </div>
                @if($lastMsg)
                <p class="text-muted mb-0 text-truncate thread-preview-{{ $mess->id }}" style="font-size:11px;">
                    {{ $lastMsg->sender_role === 'admin' ? __('You').': ' : '' }}{{ Str::limit($lastMsg->message, 45) }}
                </p>
                @else
                <p class="text-muted mb-0 text-truncate thread-preview-{{ $mess->id }}" style="font-size:11px;"></p>
                @endif
            </div>
        </a>
        @endforeach
        </div>
        @endif
    </div>

    {{-- Conversation --}}
    <div class="col-md-8 col-lg-9 d-flex flex-column" style="background:#fff;">

        @if(!$selectedMess)
        <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center text-muted">
            <i class="ti ti-message-2 fs-1 mb-3 text-primary opacity-50"></i>
            <h6 class="fw-normal">{{ __('Select a mess to view the conversation') }}</h6>
            <p class="small mb-0">{{ __('Click any mess on the left to open the thread.') }}</p>
        </div>
        @else

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width:44px;height:44px;font-size:18px;font-weight:700;">
                    {{ strtoupper(substr($selectedMess->name, 0, 1)) }}
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">{{ $selectedMess->name }}</h6>
                    <span class="text-muted small">{{ __('Owner') }}: {{ $selectedMess->owner->name }} &middot; {{ $selectedMess->owner->email }}</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($selectedMess->owner->is_blocked)
                <form action="{{ route('admin.suggestions.unblock', $selectedMess->owner->id) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-success"><i class="ti ti-lock-open me-1"></i>{{ __('Unblock Owner') }}</button>
                </form>
                <span class="badge bg-danger">
                    {{ __('Blocked') }}
                    @if($selectedMess->owner->blocked_until)
                    {{ __('until') }} {{ \Carbon\Carbon::parse($selectedMess->owner->blocked_until)->format('d M Y') }}
                    @else ({{ __('Permanent') }}) @endif
                </span>
                @else
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#blockModal">
                    <i class="ti ti-ban me-1"></i>{{ __('Block Owner') }}
                </button>
                @endif
            </div>
        </div>

        {{-- Messages --}}
        <div class="flex-grow-1 p-4 overflow-auto" id="msg-scroll" style="background:#f4f6fb;">
            @if($messages->isEmpty())
            <div class="text-center text-muted py-5" id="empty-state">
                <i class="ti ti-message-dots fs-1 d-block mb-2 opacity-50"></i>
                <span class="small">{{ __('No messages in this thread yet.') }}</span>
            </div>
            @endif
            <div id="msg-list">
            @foreach($messages as $msg)
            @php $isAdmin = $msg->sender_role === 'admin'; @endphp
            <div class="d-flex {{ $isAdmin ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                @if(!$isAdmin)
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0 me-2" style="width:32px;height:32px;font-size:13px;font-weight:700;align-self:flex-end;">
                    {{ strtoupper(substr($selectedMess->owner->name, 0, 1)) }}
                </div>
                @endif
                <div style="max-width:70%;">
                    <div class="rounded-3 px-3 py-2 {{ $isAdmin ? 'bg-primary text-white' : 'bg-white border' }}" style="box-shadow:0 1px 3px rgba(0,0,0,.08);">
                        <p class="mb-0" style="font-size:14px;line-height:1.6;white-space:pre-wrap;">{{ $msg->message }}</p>
                    </div>
                    <div class="mt-1 {{ $isAdmin ? 'text-end' : '' }}" style="font-size:10px;color:#9ca3af;">
                        @if($isAdmin)<i class="ti ti-shield-check me-1"></i>{{ __('Super Admin') }} &middot; @else {{ $selectedMess->owner->name }} ({{ $selectedMess->name }}) &middot; @endif
                        {{ $msg->created_at->format('d M Y, h:i A') }}
                        @if($isAdmin && $msg->is_read)&middot; <i class="ti ti-checks" title="{{ __('Read') }}"></i>@endif
                    </div>
                </div>
                @if($isAdmin)
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-danger text-white flex-shrink-0 ms-2" style="width:32px;height:32px;align-self:flex-end;">
                    <i class="ti ti-shield-check" style="font-size:14px;"></i>
                </div>
                @endif
            </div>
            @endforeach
            </div>
        </div>

        {{-- Reply --}}
        @if(!$selectedMess->owner->is_blocked)
        <div class="border-top px-4 py-3 bg-white">
            <form id="reply-form" action="{{ route('admin.suggestions.reply', $selectedMess->id) }}" method="POST" onsubmit="return false;">
                @csrf
                <div class="d-flex gap-2 align-items-end">
                    <div class="flex-grow-1">
                        <textarea name="message" id="msg-input" class="form-control" rows="2"
                                  placeholder="{{ __('Write a reply to the mess owner...') }}"
                                  style="resize:none;" maxlength="5000" required></textarea>
                    </div>
                    <button type="button" id="send-btn" class="btn btn-primary px-4" style="height:68px;">
                        <i class="ti ti-send fs-5"></i>
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="border-top px-4 py-3 bg-white text-center text-muted small">
            <i class="ti ti-ban me-1 text-danger"></i>{{ __('This owner is blocked. Unblock to send messages.') }}
        </div>
        @endif

        {{-- Block Modal --}}
        <div class="modal fade" id="blockModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('admin.suggestions.block', $selectedMess->owner->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title d-flex align-items-center gap-2">
                                <span class="d-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger" style="width:34px;height:34px;"><i class="ti ti-ban fs-5"></i></span>
                                {{ __('Block Owner') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body pt-2">
                            <p class="text-muted small mb-3">{{ __('Blocking') }} <strong>{{ $selectedMess->owner->name }}</strong> {{ __('will prevent them from sending messages.') }}</p>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">{{ __('Block Type') }}</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="block_type" id="bt_temp" value="temporary" checked onchange="toggleBlockUntil(this)">
                                        <label class="form-check-label small" for="bt_temp">{{ __('Temporary') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="block_type" id="bt_perm" value="permanent" onchange="toggleBlockUntil(this)">
                                        <label class="form-check-label small" for="bt_perm">{{ __('Permanent') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3" id="block-until-wrap">
                                <label class="form-label fw-semibold small">{{ __('Block Until') }}</label>
                                <input type="datetime-local" name="blocked_until" class="form-control form-control-sm" min="{{ now()->addHour()->format('Y-m-d\TH:i') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">{{ __('Reason') }} <span class="text-muted">({{ __('optional') }})</span></label>
                                <textarea name="block_reason" class="form-control form-control-sm" rows="2" placeholder="{{ __('Explain why this owner is being blocked...') }}" maxlength="500"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-ban me-1"></i>{{ __('Block') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @endif
    </div>
</div>

</div>
</div>

@push('scripts')
<script src="{{ URL::asset('build/js/pusher.min.js') }}"></script>
<script>
var _aSEL_ID    = {{ $selectedMess ? $selectedMess->id : 'null' }};
var _aOWNER_INI = {{ $selectedMess ? json_encode(strtoupper(substr($selectedMess->owner->name, 0, 1))) : json_encode('?') }};
var _aOWNER_NM  = {{ $selectedMess ? json_encode($selectedMess->owner->name) : json_encode('') }};
var _aMESS_NM   = {{ $selectedMess ? json_encode($selectedMess->name) : json_encode('') }};
var _aREPLY_URL = {{ $selectedMess ? json_encode(route('admin.suggestions.reply', $selectedMess->id)) : 'null' }};
var _aCsrf      = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var _aBody      = document.body;
var _aRKey      = _aBody.getAttribute('data-reverb-key');
var _aRHost     = _aBody.getAttribute('data-reverb-host') || 'localhost';
var _aRPort     = parseInt(_aBody.getAttribute('data-reverb-port') || '8080');
var _aRScheme   = _aBody.getAttribute('data-reverb-scheme') || 'http';

function aEscHtml(str){ return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function aScrollBottom(){ var s=document.getElementById('msg-scroll'); if(s) s.scrollTop=s.scrollHeight; }
function aNowStr(){ return new Date().toLocaleString('en-GB',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}); }

function aAppendMessage(data, isSelf){
    var empty = document.getElementById('empty-state');
    if(empty && empty.parentNode) empty.parentNode.removeChild(empty);
    var list = document.getElementById('msg-list');
    if(!list) return;
    var avatarLeft  = '<div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white flex-shrink-0 me-2" style="width:32px;height:32px;font-size:13px;font-weight:700;align-self:flex-end;">' + _aOWNER_INI + '</div>';
    var avatarRight = '<div class="d-flex align-items-center justify-content-center rounded-circle bg-danger text-white flex-shrink-0 ms-2" style="width:32px;height:32px;align-self:flex-end;"><i class="ti ti-shield-check" style="font-size:14px;"></i></div>';
    var bubble = '<div style="max-width:70%;">'
        + '<div class="rounded-3 px-3 py-2 ' + (isSelf ? 'bg-primary text-white' : 'bg-white border') + '" style="box-shadow:0 1px 3px rgba(0,0,0,.08);">'
        + '<p class="mb-0" style="font-size:14px;line-height:1.6;white-space:pre-wrap;">' + aEscHtml(data.message) + '</p>'
        + '</div>'
        + '<div class="mt-1 ' + (isSelf ? 'text-end' : '') + '" style="font-size:10px;color:#9ca3af;">'
        + (isSelf ? '<i class="ti ti-shield-check me-1"></i>{{ __("Super Admin") }}' : (_aOWNER_NM + ' (' + _aMESS_NM + ')')) + ' &middot; ' + (data.created_at || aNowStr())
        + '</div></div>';
    var div = document.createElement('div');
    div.className = 'd-flex ' + (isSelf ? 'justify-content-end' : 'justify-content-start') + ' mb-3';
    div.innerHTML = isSelf ? (bubble + avatarRight) : (avatarLeft + bubble);
    list.appendChild(div);
    aScrollBottom();
}

// ---- Send button ----
var _aSendBtn  = document.getElementById('send-btn');
var _aMsgInput = document.getElementById('msg-input');

if(_aMsgInput){
    _aMsgInput.addEventListener('keydown', function(e){
        if(e.ctrlKey && e.key === 'Enter' && _aSendBtn) _aSendBtn.click();
    });
}

if(_aSendBtn){
    _aSendBtn.addEventListener('click', function(){
        if(!_aREPLY_URL) return;
        var txt = _aMsgInput ? _aMsgInput.value.trim() : '';
        if(!txt) return;
        aAppendMessage({ message: txt, created_at: aNowStr() }, true);
        _aMsgInput.value = '';
        _aMsgInput.focus();
        _aSendBtn.disabled = true;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', _aREPLY_URL);
        xhr.setRequestHeader('X-CSRF-TOKEN', _aCsrf);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onloadend = function(){ _aSendBtn.disabled = false; };
        xhr.send('message=' + encodeURIComponent(txt));
    });
}

// ---- Thread search ----
var _aSearch = document.getElementById('thread-search');
if(_aSearch){
    _aSearch.addEventListener('input', function(){
        var q = this.value.toLowerCase();
        document.querySelectorAll('.thread-item').forEach(function(el){
            el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}

// ---- WebSocket ----
function aSetStatus(ok){
    var badge=document.getElementById('ws-status-badge'), dot=document.getElementById('ws-dot'), label=document.getElementById('ws-label');
    if(!badge) return;
    badge.className = ok ? 'badge bg-success-subtle text-success border border-success-subtle' : 'badge bg-warning-subtle text-warning border border-warning-subtle';
    if(dot)   dot.style.color = ok ? '#2fb344' : '#f59f00';
    if(label) label.textContent = ok ? '{{ __("Live") }}' : '{{ __("Reconnecting...") }}';
}

if(_aRKey && typeof Pusher !== 'undefined'){
    try {
        var _aPusher = new Pusher(_aRKey, {
            wsHost: _aRHost, wsPort: _aRPort, wssPort: _aRPort,
            forceTLS: _aRScheme === 'https',
            enabledTransports: ['ws','wss'], disableStats: true, cluster: 'mt1',
            authEndpoint: '/broadcasting/auth',
            auth: { headers: { 'X-CSRF-TOKEN': _aCsrf, 'X-Requested-With': 'XMLHttpRequest' } },
        });
        _aPusher.connection.bind('connected',    function(){ aSetStatus(true); });
        _aPusher.connection.bind('disconnected', function(){ aSetStatus(false); });
        _aPusher.connection.bind('failed',       function(){ aSetStatus(false); });

        if(_aSEL_ID){
            var _aCh = _aPusher.subscribe('private-mess-suggestions.' + _aSEL_ID);
            _aCh.bind('.message.sent', function(data){
                if(data.sender_role === 'owner'){
                    aAppendMessage(data, false);
                    var b=document.querySelector('.thread-badge-'+_aSEL_ID); if(b){b.textContent='';b.classList.add('d-none');}
                }
            });
        }

        var _aAdminCh = _aPusher.subscribe('private-admin-suggestions');
        _aAdminCh.bind('.message.sent', function(data){
            if(data.sender_role !== 'owner') return;
            var prev=document.querySelector('.thread-preview-'+data.mess_id); if(prev) prev.textContent=data.message.substring(0,45);
            if(data.mess_id !== _aSEL_ID){
                var b=document.querySelector('.thread-badge-'+data.mess_id);
                if(b){ b.textContent=(parseInt(b.textContent)||0)+1; b.classList.remove('d-none'); }
                var tot=document.getElementById('total-unread-badge');
                if(tot){ tot.textContent=(parseInt(tot.textContent)||0)+1+' {{ __("new") }}'; tot.classList.remove('d-none'); }
                if(typeof toastr!=='undefined') toastr.info('<strong>'+aEscHtml(data.mess_name)+'</strong> ('+aEscHtml(data.owner_name)+')<br>'+aEscHtml(data.message.substring(0,80)),'{{ __("New Suggestion") }}',{timeOut:8000,closeButton:true,progressBar:true,allowHtml:true});
            }
        });
    } catch(e){ aSetStatus(false); }
} else {
    aSetStatus(false);
}

aScrollBottom();

function toggleBlockUntil(radio){
    var w = document.getElementById('block-until-wrap');
    if(w) w.style.display = radio.value === 'temporary' ? '' : 'none';
}
</script>
@endpush
@endsection
