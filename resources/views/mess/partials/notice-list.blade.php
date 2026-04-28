@if($list->isEmpty())
<div class="text-center py-5">
    <div class="mb-3">
        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle" style="width:72px;height:72px;">
            <i class="ti ti-speakerphone text-primary" style="font-size:32px;"></i>
        </span>
    </div>
    <h6 class="text-muted fw-normal">{{ __('No notices yet.') }}</h6>
    @if(isset($canManage) && $canManage)
    <p class="text-muted small mb-0">{{ __('Publish a notice to notify all mess members.') }}</p>
    @endif
</div>
@else
<div class="notice-list">
    @foreach($list as $notice)
    @php
        $isRead     = in_array($notice->id, $readIds ?? []);
        $isNew      = !$isRead && $notice->status === 'published';
        $isDraft    = $notice->status === 'draft';
    @endphp
    <div class="notice-item {{ $isNew ? 'notice-item--unread' : '' }} {{ $isDraft ? 'notice-item--draft' : '' }} mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-0">
                <div class="d-flex">

                    {{-- Left accent bar + icon --}}
                    <div class="notice-item__accent d-flex flex-column align-items-center justify-content-start py-3 px-3"
                         style="min-width:60px;background:{{ $isDraft ? '#fff8e1' : ($isNew ? '#e8f0fe' : '#f8f9fa') }};border-radius:8px 0 0 8px;">
                        <span class="d-flex align-items-center justify-content-center rounded-circle mb-2"
                              style="width:38px;height:38px;background:{{ $isDraft ? '#ffc107' : ($isNew ? '#206bc4' : '#adb5bd') }}22;">
                            <i class="ti {{ $isDraft ? 'ti-pencil' : 'ti-speakerphone' }}"
                               style="font-size:18px;color:{{ $isDraft ? '#e6a817' : ($isNew ? '#206bc4' : '#6c757d') }};"></i>
                        </span>
                        @if($isNew)
                        <span class="badge rounded-pill bg-danger" style="font-size:9px;writing-mode:horizontal-tb;letter-spacing:.5px;">NEW</span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-grow-1 p-3 min-w-0">
                        <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
                            <div class="min-w-0">
                                {{-- Status + title --}}
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    @if($isDraft)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size:10px;">
                                        <i class="ti ti-pencil me-1"></i>{{ __('Draft') }}
                                    </span>
                                    @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:10px;">
                                        <i class="ti ti-circle-check me-1"></i>{{ __('Published') }}
                                    </span>
                                    @endif
                                </div>
                                <h6 class="mb-1 {{ $isNew ? 'fw-bold' : 'fw-semibold' }}">
                                    <a href="{{ route('mess.notices.show', [$mess->id, $notice->id]) }}"
                                       class="text-decoration-none {{ $isNew ? 'text-primary' : 'text-dark' }}">
                                        {{ $notice->title }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-2" style="line-height:1.5;">
                                    {{ Str::limit(strip_tags($notice->body), 140) }}
                                </p>
                            </div>
                        </div>

                        {{-- Meta row --}}
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3 text-muted" style="font-size:11px;">
                                <span class="d-flex align-items-center gap-1">
                                    <span class="avatar avatar-xs rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center" style="width:20px;height:20px;">
                                        <i class="ti ti-user" style="font-size:10px;color:#206bc4;"></i>
                                    </span>
                                    {{ $notice->author->name }}
                                </span>
                                @if($notice->published_at)
                                <span class="d-flex align-items-center gap-1">
                                    <i class="ti ti-clock" style="font-size:11px;"></i>
                                    {{ $notice->published_at->format('d M Y, h:i A') }}
                                    <span class="text-muted fst-italic">({{ $notice->published_at->diffForHumans() }})</span>
                                </span>
                                @else
                                <span class="d-flex align-items-center gap-1 fst-italic">
                                    <i class="ti ti-clock-pause" style="font-size:11px;"></i>
                                    {{ __('Not published') }}
                                </span>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('mess.notices.show', [$mess->id, $notice->id]) }}"
                                   class="btn btn-sm {{ $isNew ? 'btn-primary' : 'btn-outline-secondary' }}" style="font-size:12px;">
                                    <i class="ti ti-eye me-1"></i>{{ __('Read') }}
                                </a>
                                @if(isset($canManage) && $canManage)
                                <button class="btn btn-sm btn-outline-secondary" style="font-size:12px;"
                                    onclick="openEditNotice({{ $notice->id }}, {{ json_encode($notice->title) }}, {{ json_encode($notice->body) }}, '{{ $notice->status }}')"
                                    title="{{ __('Edit') }}">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" style="font-size:12px;"
                                    onclick="openDeleteNotice({{ $notice->id }}, {{ json_encode($notice->title) }})"
                                    title="{{ __('Delete') }}">
                                    <i class="ti ti-trash"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<style>
.notice-item .card { border-radius: 8px; transition: box-shadow .2s, transform .15s; }
.notice-item .card:hover { box-shadow: 0 6px 24px rgba(32,107,196,.13) !important; transform: translateY(-1px); }
.notice-item--unread .card { border-left: 4px solid #206bc4 !important; }
.notice-item--draft .card  { border-left: 4px solid #f59f00 !important; }
@keyframes pulseNew {
    0%,100% { opacity:1; } 50% { opacity:.55; }
}
.notice-item--unread .badge.bg-danger { animation: pulseNew 1.6s infinite; }
</style>
