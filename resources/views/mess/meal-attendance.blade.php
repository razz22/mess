<?php $page = "mess-meals" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        {{-- Header --}}
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">Meal Attendance — {{ $mess->name }}</h4>
                <h6 class="text-muted">{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
                    @if($isPast) <span class="badge bg-secondary ms-2">Past — Read Only</span> @endif
                    @if($date === $today) <span class="badge bg-success ms-2">Today</span> @endif
                </h6>
            </div>
            <div class="page-btn d-flex gap-2 align-items-center">
                @if(!$isManager && $date === $today)
                @php $remaining = max(0, 3 - $myChangesToday); @endphp
                <div class="d-flex align-items-center gap-1 px-3 py-1 rounded border {{ $remaining === 0 ? 'border-danger bg-danger-subtle' : 'border-secondary bg-light' }}">
                    <i class="ti ti-refresh {{ $remaining === 0 ? 'text-danger' : 'text-muted' }} fs-6"></i>
                    <span class="small fw-semibold {{ $remaining === 0 ? 'text-danger' : 'text-muted' }}" id="changes-remaining">
                        {{ $remaining }}/3 changes left today
                    </span>
                </div>
                @endif
                <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#bulkAttendanceModal">
                    <i class="ti ti-calendar-stats me-1"></i>Bulk Attendance
                </button>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#whatsappModal">
                    <i class="ti ti-brand-whatsapp me-1"></i>Share Meal Count
                </button>
                @if($isManager)
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMealTypeModal">
                    <i class="ti ti-plus me-1"></i>Add Meal Type
                </button>
                @endif
                <a href="{{ route('mess.dashboard', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        {{-- Date Navigation --}}
        @php
            $prevDate = \Carbon\Carbon::parse($date)->subDay()->toDateString();
            $nextDate = \Carbon\Carbon::parse($date)->addDay()->toDateString();
            $maxDate  = \Carbon\Carbon::parse($today)->addDays(7)->toDateString();
        @endphp
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="?date={{ $prevDate }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                        <i class="ti ti-chevron-left"></i> Prev
                    </a>

                    <div class="input-group input-group-sm" style="width:200px;">
                        <span class="input-group-text bg-white"><i class="ti ti-calendar text-primary"></i></span>
                        <input type="text" id="mealDatePicker" class="form-control fw-semibold"
                            value="{{ \Carbon\Carbon::parse($date)->format('d M Y') }}"
                            placeholder="Pick a date" readonly style="cursor:pointer;">
                    </div>

                    @if($date < $maxDate)
                    <a href="?date={{ $nextDate }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                        Next <i class="ti ti-chevron-right"></i>
                    </a>
                    @endif
                    @if($date !== $today)
                    <a href="?date={{ $today }}" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                        <i class="ti ti-calendar-today"></i> Today
                    </a>
                    @endif

                    {{-- Meal type badges / close times --}}
                    <div class="ms-auto d-flex gap-2 flex-wrap align-items-center">
                        @foreach($mealTypes as $mt)
                        @php $sch = $schedules[$mt->name] ?? null; @endphp
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-{{ $mt->isExpired($date) && !$isPast ? 'secondary' : 'primary' }}-subtle border border-{{ $mt->isExpired($date) && !$isPast ? 'secondary' : 'primary' }} text-dark">
                                {{ $mt->name }}
                                @if($mt->close_time) <span class="text-muted">· closes {{ $mt->closeLabel() }}</span> @endif
                            </span>
                            @if($isManager && $sch && $sch->status === 'open' && !$isPast)
                            <button class="btn btn-xs btn-outline-danger py-0" onclick="closeMeal({{ $sch->id }}, '{{ $mt->name }}')" title="Close {{ $mt->name }}">
                                <i class="ti ti-lock" style="font-size:11px"></i>
                            </button>
                            @elseif($sch && $sch->status === 'closed')
                            <span class="badge bg-secondary" style="font-size:10px"><i class="ti ti-lock me-1" style="font-size:9px"></i>Closed</span>
                            @if($isOwner || $isSuperAdmin)
                            <button class="btn btn-xs btn-outline-success py-0" onclick="reopenMeal({{ $sch->id }}, '{{ $mt->name }}')" title="Reopen {{ $mt->name }}">
                                <i class="ti ti-lock-open" style="font-size:11px"></i>
                            </button>
                            @endif
                            @endif
                        </div>
                        @endforeach

                        @if($isManager && $mealTypes->count() > 3)
                        {{-- manage meal types link --}}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Spreadsheet --}}
        @if($mealTypes->isEmpty())
        <div class="alert alert-info">No meal types configured. <button class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#addMealTypeModal">Add Meal Type</button></div>
        @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive" style="overflow-x:auto;">
                    <table class="table table-bordered mb-0 meal-sheet">
                        <thead>
                            <tr class="align-middle">
                                <th class="bg-light" style="min-width:180px;position:sticky;left:0;z-index:2;">
                                    <i class="ti ti-users text-primary me-1"></i> Member
                                </th>
                                @foreach($mealTypes as $mt)
                                @php
                                    $sch        = $schedules[$mt->name] ?? null;
                                    $expired    = !$isPast && $mt->isExpired($date);
                                    $closed     = $sch && $sch->status === 'closed';
                                    $schAtts    = $sch ? $allAttendances->filter(fn($g,$k) => str_starts_with($k, $sch->id.'_'))->flatten() : collect();
                                    $sumFull    = $schAtts->sum('full_qty');
                                    $sumHalf    = $schAtts->sum('half_qty');
                                    $mtItems    = $routineItems[$mt->name] ?? null;
                                    $itemList   = $mtItems ? array_filter(array_map('trim', explode(',', $mtItems))) : [];
                                @endphp
                                <th class="text-center {{ $closed ? 'bg-secondary bg-opacity-10' : 'bg-light' }}" style="min-width:160px;">
                                    <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                                        <span class="fw-semibold">{{ $mt->name }}</span>
                                        <button type="button" class="routine-view-btn btn btn-xs p-0 border-0 {{ !empty($itemList) ? 'text-primary' : 'text-muted' }}"
                                            title="{{ !empty($itemList) ? 'View / edit menu' : 'Add menu items' }}"
                                            data-meal-type="{{ $mt->name }}"
                                            onclick="showRoutineItems(this.dataset.mealType)">
                                            <i class="ti {{ !empty($itemList) ? 'ti-list-details' : 'ti-circle-plus' }}" style="font-size:16px;"></i>
                                        </button>
                                    </div>
                                    {{-- Meal items in bordered div (updated live via JS) --}}
                                    <div class="hdr-items-{{ Str::slug($mt->name) }} text-muted mx-1 mb-1" style="font-size:10px;border:1px solid #dee2e6;border-radius:5px;padding:3px 6px;{{ empty($itemList) ? 'display:none;' : '' }}">{{ implode(', ', $itemList) }}</div>
                                    {{-- Status / counts --}}
                                    @if($closed)
                                        <span class="badge bg-secondary" style="font-size:10px">Closed</span>
                                    @elseif($expired)
                                        <span class="badge bg-warning text-dark" style="font-size:10px">Expired</span>
                                    @elseif($sch)
                                        <div class="d-flex justify-content-center gap-2 mt-1" id="hdr-qty-{{ $sch->id }}" style="font-size:10px;">
                                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2">
                                                Full <strong>{{ $sumFull }}</strong>
                                            </span>
                                            <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2" style="color:#9a6700 !important;">
                                                ½ <strong>{{ $sumHalf }}</strong>
                                            </span>
                                        </div>
                                    @endif
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php $myId = Auth::id(); @endphp
                            @foreach($members as $mem)
                            @php
                                $isMe     = $mem->user_id === $myId;
                                $canEdit  = $isManager || $isMe;
                                $totalQty = 0;
                            @endphp
                            <tr class="{{ $isMe ? 'table-primary bg-opacity-25' : '' }}">
                                {{-- Member name sticky --}}
                                <td class="align-middle {{ $isMe ? 'bg-primary bg-opacity-10' : 'bg-white' }}" style="position:sticky;left:0;z-index:1;">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($mem->user->avatar)
                                        <img src="{{ asset('storage/'.$mem->user->avatar) }}" class="rounded-circle" style="width:30px;height:30px;object-fit:cover;" alt="">
                                        @else
                                        <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold" style="width:30px;height:30px;font-size:13px;flex-shrink:0;">{{ strtoupper(substr($mem->user->name,0,1)) }}</span>
                                        @endif
                                        <div>
                                            <div class="fw-semibold small">{{ $mem->user->name }}</div>
                                            @if($isMe)<div class="text-primary" style="font-size:10px">You</div>@endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Quantity cells --}}
                                @foreach($mealTypes as $mt)
                                @php
                                    $sch       = $schedules[$mt->name] ?? null;
                                    $key       = $sch ? ($sch->id . '_' . $mem->user_id) : null;
                                    $att       = $key ? ($allAttendances[$key]->first() ?? null) : null;
                                    $fullQty   = $att ? (int)$att->full_qty : 0;
                                    $halfQty   = $att ? (int)$att->half_qty : 0;
                                    $qty       = $fullQty + $halfQty * 0.5;
                                    $locked    = $isPast || !$sch || $sch->status === 'closed'
                                                 || (!$isManager && $mt->isExpired($date));
                                    $canChange = $canEdit && !$locked;
                                    $lockTitle = $locked ? ($isPast ? 'Past date' : ($sch && $sch->status==='closed' ? 'Meal closed' : 'Time expired')) : '';
                                @endphp
                                <td class="text-center align-middle p-1 {{ $locked ? 'bg-light' : '' }}">
                                    @if($sch)
                                    @php
                                        $cellBg = $qty == 0 ? '#fff5f5' : ($fullQty > 0 && $halfQty > 0 ? '#f5f0ff' : ($halfQty > 0 ? '#fff8e1' : '#f0fff4'));
                                    @endphp
                                    <div class="meal-cell d-flex flex-column align-items-center gap-1"
                                         data-schedule="{{ $sch->id }}" data-user="{{ $mem->user_id }}"
                                         style="min-width:110px;">
                                        {{-- Full meals spinner --}}
                                        <div class="d-flex align-items-center gap-1 w-100 justify-content-center">
                                            <span class="text-muted" style="font-size:10px;width:28px;text-align:right;">Full</span>
                                            <div class="input-group input-group-sm" style="width:80px;">
                                                <button type="button" class="btn btn-outline-secondary px-1 py-0 qty-btn" style="font-size:12px;"
                                                    data-target="full" data-dir="-1" data-schedule="{{ $sch->id }}" data-user="{{ $mem->user_id }}"
                                                    {{ !$canChange ? 'disabled' : '' }} title="{{ $lockTitle }}">−</button>
                                                <input type="number" min="0" max="20" step="1" value="{{ $fullQty }}"
                                                    class="form-control text-center fw-bold px-0 full-qty-input"
                                                    style="font-size:13px;background:{{ $cellBg }};"
                                                    data-schedule="{{ $sch->id }}" data-user="{{ $mem->user_id }}"
                                                    {{ !$canChange ? 'disabled' : '' }}
                                                    onchange="submitMealQty({{ $sch->id }}, {{ $mem->user_id }})"
                                                    title="{{ $lockTitle }}">
                                                <button type="button" class="btn btn-outline-secondary px-1 py-0 qty-btn" style="font-size:12px;"
                                                    data-target="full" data-dir="1" data-schedule="{{ $sch->id }}" data-user="{{ $mem->user_id }}"
                                                    {{ !$canChange ? 'disabled' : '' }} title="{{ $lockTitle }}">+</button>
                                            </div>
                                        </div>
                                        {{-- Half meals spinner --}}
                                        <div class="d-flex align-items-center gap-1 w-100 justify-content-center">
                                            <span class="text-muted" style="font-size:10px;width:28px;text-align:right;">½</span>
                                            <div class="input-group input-group-sm" style="width:80px;">
                                                <button type="button" class="btn btn-outline-secondary px-1 py-0 qty-btn" style="font-size:12px;"
                                                    data-target="half" data-dir="-1" data-schedule="{{ $sch->id }}" data-user="{{ $mem->user_id }}"
                                                    {{ !$canChange ? 'disabled' : '' }} title="{{ $lockTitle }}">−</button>
                                                <input type="number" min="0" max="20" step="1" value="{{ $halfQty }}"
                                                    class="form-control text-center fw-bold px-0 half-qty-input"
                                                    style="font-size:13px;background:{{ $cellBg }};"
                                                    data-schedule="{{ $sch->id }}" data-user="{{ $mem->user_id }}"
                                                    {{ !$canChange ? 'disabled' : '' }}
                                                    onchange="submitMealQty({{ $sch->id }}, {{ $mem->user_id }})"
                                                    title="{{ $lockTitle }}">
                                                <button type="button" class="btn btn-outline-secondary px-1 py-0 qty-btn" style="font-size:12px;"
                                                    data-target="half" data-dir="1" data-schedule="{{ $sch->id }}" data-user="{{ $mem->user_id }}"
                                                    {{ !$canChange ? 'disabled' : '' }} title="{{ $lockTitle }}">+</button>
                                            </div>
                                        </div>
                                        {{-- Off button --}}
                                        @if($canChange)
                                        <button type="button" class="btn btn-xs off-btn px-2 py-0 {{ $qty == 0 ? 'btn-danger' : 'btn-outline-danger' }}"
                                            style="font-size:11px;border-radius:20px;"
                                            data-schedule="{{ $sch->id }}" data-user="{{ $mem->user_id }}"
                                            onclick="setMealOff({{ $sch->id }}, {{ $mem->user_id }}, this)"
                                            title="Mark as Off (not eating)">
                                            ✕ Off
                                        </button>
                                        @endif
                                        {{-- Summary badge --}}
                                        <div class="meal-summary-badge" data-schedule="{{ $sch->id }}" data-user="{{ $mem->user_id }}">
                                            @if($qty == 0)
                                            <span class="badge bg-danger-subtle text-danger" style="font-size:10px;">Off</span>
                                            @else
                                            <span class="badge bg-success-subtle text-success" style="font-size:10px;">
                                                {{ $fullQty > 0 ? $fullQty.'F' : '' }}{{ $fullQty > 0 && $halfQty > 0 ? '+' : '' }}{{ $halfQty > 0 ? $halfQty.'H' : '' }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    @else
                                    <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                @endforeach

                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light fw-semibold">
                                <td class="bg-light small" style="position:sticky;left:0;z-index:1;">Totals</td>
                                @foreach($mealTypes as $mt)
                                @php
                                    $sch     = $schedules[$mt->name] ?? null;
                                    $ftAtts  = $sch ? $allAttendances->filter(fn($g, $k) => str_starts_with($k, $sch->id.'_'))->flatten() : collect();
                                    $ftFull  = $ftAtts->sum('full_qty');
                                    $ftHalf  = $ftAtts->sum('half_qty');
                                @endphp
                                <td class="text-center" id="foot-{{ $sch?->id ?? 'none' }}">
                                    <div class="d-flex justify-content-center gap-1">
                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2" style="font-size:11px;">
                                            Full <strong>{{ $ftFull }}</strong>
                                        </span>
                                        <span class="badge rounded-pill bg-warning bg-opacity-10 border border-warning border-opacity-25 px-2" style="font-size:11px;color:#9a6700;">
                                            ½ <strong>{{ $ftHalf }}</strong>
                                        </span>
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Meal Type Management (manager only) --}}
        @if($isManager)
        <div class="card mt-3">
            <div class="card-header py-2 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold"><i class="ti ti-tools-kitchen-2 me-2"></i>Manage Meal Types</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50px" class="text-center">#</th>
                                <th>Name</th>
                                <th>Closes At</th>
                                <th>Status</th>
                                <th class="text-center" style="width:120px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allMealTypes as $mt)
                            <tr class="{{ !$mt->is_active ? 'opacity-50' : '' }}">
                                <td class="text-center align-middle">
                                    <span class="badge bg-secondary rounded-pill">{{ $mt->sort_order ?? '—' }}</span>
                                </td>
                                <td class="fw-semibold align-middle">
                                    {{ $mt->name }}
                                    @if(!$mt->is_active)<span class="badge bg-secondary ms-1" style="font-size:10px">Disabled</span>@endif
                                </td>
                                <td class="align-middle">
                                    @if($mt->close_time)
                                        {{ \Carbon\Carbon::parse($mt->close_time)->format('g:i A') }}
                                        @if($mt->close_days_before > 0)
                                            <br><small class="text-muted">{{ $mt->close_days_before === 1 ? 'prev. day' : $mt->close_days_before . ' days before' }}</small>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-{{ $mt->is_active ? 'success' : 'secondary' }}">{{ $mt->is_active ? 'Active' : 'Disabled' }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <button class="btn btn-xs btn-outline-primary py-0"
                                        onclick="openEditMealType({{ $mt->id }},'{{ addslashes($mt->name) }}','{{ $mt->close_time ? substr($mt->close_time,0,5) : '' }}',{{ $mt->close_days_before ?? 0 }},{{ $mt->is_active ? 'true' : 'false' }},{{ $mt->sort_order ?? 1 }})"
                                        title="Edit">
                                        <i class="ti ti-edit" style="font-size:11px"></i>
                                    </button>
                                    @if($mt->is_active)
                                    <button class="btn btn-xs btn-outline-warning py-0 ms-1" title="Disable"
                                        onclick="openToggleMealType({{ $mt->id }},'{{ addslashes($mt->name) }}','disable')">
                                        <i class="ti ti-eye-off" style="font-size:11px"></i>
                                    </button>
                                    @else
                                    <button class="btn btn-xs btn-outline-success py-0 ms-1" title="Re-enable"
                                        onclick="openToggleMealType({{ $mt->id }},'{{ addslashes($mt->name) }}','enable')">
                                        <i class="ti ti-eye" style="font-size:11px"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Add Meal Type Modal --}}
@if($isManager)
<div class="modal fade" id="addMealTypeModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Meal Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.meal-types.store', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Snacks, Brunch" required maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Attendance Closes At</label>
                        <input type="time" name="close_time" class="form-control">
                        <div class="form-text">Leave blank for no cutoff.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Closes How Many Days Before?</label>
                        <select name="close_days_before" class="form-select">
                            <option value="0">Same day (default)</option>
                            <option value="1">1 day before (previous day)</option>
                            <option value="2">2 days before</option>
                            <option value="3">3 days before</option>
                        </select>
                        <div class="form-text">e.g. set to "1 day before" if members must book dinner by 10 PM the previous night.</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                        <input type="number" name="sort_order" class="form-control" min="1" max="99" value="{{ (\App\Models\MessMealType::where('mess_id', $mess->id)->max('sort_order') ?? 0) + 1 }}" required>
                        <div class="form-text">Lower number = shown first in table. e.g. Breakfast=1, Lunch=2, Dinner=3.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-plus me-1"></i>Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Meal Type Modal --}}
<div class="modal fade" id="editMealTypeModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Meal Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMealTypeForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-mt-name" class="form-control" required maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Attendance Closes At</label>
                        <input type="time" name="close_time" id="edit-mt-close" class="form-control">
                        <div class="form-text">Leave blank for no cutoff.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Closes How Many Days Before?</label>
                        <select name="close_days_before" id="edit-mt-days" class="form-select">
                            <option value="0">Same day (default)</option>
                            <option value="1">1 day before (previous day)</option>
                            <option value="2">2 days before</option>
                            <option value="3">3 days before</option>
                        </select>
                        <div class="form-text">e.g. "1 day before" means members must book by the previous day at the set time.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="is_active" id="edit-mt-active" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Disabled</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                        <input type="number" name="sort_order" id="edit-mt-sort" class="form-control" min="1" max="99" required>
                        <div class="form-text">Lower number = shown first in table. e.g. Breakfast=1, Lunch=2, Dinner=3.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-check me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Toggle (Disable / Enable) Meal Type Confirmation Modal --}}
<div class="modal fade" id="toggleMealTypeModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0" id="toggleMealTypeHeader">
                <h5 class="modal-title d-flex align-items-center gap-2" id="toggleMealTypeTitle">
                    <i class="ti ti-alert-triangle text-warning fs-5"></i> Confirm Action
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-3">
                <div id="toggleMealTypeIcon" class="mb-3" style="font-size:2.5rem;"></div>
                <p class="mb-0 fw-semibold fs-6" id="toggleMealTypeMsg"></p>
                <p class="text-muted small mt-1" id="toggleMealTypeSubMsg"></p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>No, Cancel
                </button>
                <button type="button" class="btn px-4" id="toggleMealTypeConfirmBtn">
                    <i class="ti ti-check me-1"></i>Yes, Confirm
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Hidden form submitted on confirm --}}
<form id="toggleMealTypeForm" method="POST" class="d-none">
    @csrf @method('DELETE')
</form>

@endif {{-- end @if($isManager) for add-meal-type modal --}}

{{-- ===================== BULK ATTENDANCE MODAL (available to all members) ===================== --}}
<div class="modal fade" id="bulkAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="ti ti-calendar-stats me-2"></i>Bulk Meal Attendance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.meals.bulk', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body">

                    @if(!$isManager)
                    <div class="alert alert-warning py-2 small mb-3">
                        <i class="ti ti-info-circle me-1"></i>
                        <strong>Members:</strong> You can only set attendance for yourself. Past dates, expired meal types, and closed meals are not editable.
                    </div>
                    @endif

                    {{-- Step 1: Members --}}
                    @if($isManager)
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-primary"><i class="ti ti-users me-2"></i>Step 1 — Select Members</h6>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="bulkSelectAll()">All</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="bulkSelectNone()">None</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-2" id="bulkMemberList">
                                @foreach($members as $m)
                                <div class="col-md-4 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input bulk-member-cb" type="checkbox"
                                            name="user_ids[]" value="{{ $m->user->id }}"
                                            id="bm_{{ $m->user->id }}" checked>
                                        <label class="form-check-label small" for="bm_{{ $m->user->id }}">
                                            {{ $m->user->name }}
                                            <span class="badge bg-secondary" style="font-size:9px">{{ ucfirst($m->role) }}</span>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @else
                    {{-- Non-manager: show own name, submit only own user id --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0 text-primary"><i class="ti ti-user me-2"></i>Step 1 — Member</h6>
                        </div>
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center gap-2">
                                @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/'.Auth::user()->avatar) }}" class="rounded-circle" width="32" height="32" style="object-fit:cover;">
                                @else
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width:32px;height:32px;font-size:13px;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                @endif
                                <span class="fw-semibold">{{ Auth::user()->name }}</span>
                                <span class="badge bg-secondary ms-1" style="font-size:10px;">{{ ucfirst($member->role ?? 'member') }}</span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="user_ids[]" value="{{ Auth::id() }}">
                    @endif

                    {{-- Step 2: Dates --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <h6 class="mb-0 text-success"><i class="ti ti-calendar me-2"></i>Step 2 — Select Dates</h6>
                                <div class="d-flex flex-wrap gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="bulkQuick('fri')">All Fri</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="bulkQuick('sat')">All Sat</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="bulkQuick('weekend')">Weekends</button>
                                    <button type="button" class="btn btn-xs btn-outline-success" onclick="bulkQuick('all')">All</button>
                                    <button type="button" class="btn btn-xs btn-outline-danger" onclick="bulkQuick('clear')">Clear</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            {{-- Inline Calendar --}}
                            <div class="d-flex align-items-center justify-content-between px-1 mb-2">
                                <button type="button" class="btn btn-xs btn-outline-secondary px-2" onclick="bulkCalPrev()">&#8249;</button>
                                <span class="fw-semibold small" id="bulkCalTitle"></span>
                                <button type="button" class="btn btn-xs btn-outline-secondary px-2" onclick="bulkCalNext()">&#8250;</button>
                            </div>
                            <div id="bulkCalGrid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:3px;text-align:center;"></div>
                            <div id="bulkDatesHidden" class="mt-2"></div>
                            <div class="text-muted small mt-2 px-1" id="bulkDateCount">No dates selected.</div>
                        </div>
                    </div>

                    {{-- Step 3: Meal Types --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-info"><i class="ti ti-bowl me-2"></i>Step 3 — Meal Types</h6>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-xs btn-outline-info" onclick="bulkMtAll()">All</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="bulkMtNone()">None</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                @foreach($mealTypes as $mt)
                                <div class="col-auto">
                                    <div class="form-check">
                                        <input class="form-check-input bulk-mt-cb" type="checkbox"
                                            name="meal_types[]" value="{{ $mt->name }}"
                                            id="bmt_{{ $mt->id }}" checked>
                                        <label class="form-check-label small" for="bmt_{{ $mt->id }}">
                                            {{ $mt->name }}
                                            @if($mt->close_time)
                                            <span class="text-muted">· {{ $mt->closeLabel() }}</span>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Step 4: Quantity --}}
                    <div class="card mb-0">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0 text-warning"><i class="ti ti-hash me-2"></i>Step 4 — Set Quantity</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <button type="button" class="btn btn-danger btn-sm px-3" id="bulkOffBtn" onclick="setBulkOff()">
                                    ✕ Off (mark as not eating)
                                </button>
                                <span class="text-muted small ms-2">or set quantities below</span>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-semibold">Full meals</label>
                                    <div class="input-group input-group-sm">
                                        <button type="button" class="btn btn-outline-secondary px-2" onclick="adjustBulk('full',-1)">−</button>
                                        <input type="number" name="full_qty" id="bulkFullQty" class="form-control text-center fw-bold" min="0" max="20" step="1" value="1">
                                        <button type="button" class="btn btn-outline-secondary px-2" onclick="adjustBulk('full',1)">+</button>
                                    </div>
                                    <div class="form-text">1 = one full meal</div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-semibold">Half meals (½)</label>
                                    <div class="input-group input-group-sm">
                                        <button type="button" class="btn btn-outline-secondary px-2" onclick="adjustBulk('half',-1)">−</button>
                                        <input type="number" name="half_qty" id="bulkHalfQty" class="form-control text-center fw-bold" min="0" max="20" step="1" value="0">
                                        <button type="button" class="btn btn-outline-secondary px-2" onclick="adjustBulk('half',1)">+</button>
                                    </div>
                                    <div class="form-text">e.g. 2 = two half portions</div>
                                </div>
                            </div>
                            <div id="bulkQtySummary" class="alert alert-info py-2 mb-0 small">
                                <i class="ti ti-info-circle me-1"></i>
                                <span id="bulkQtyEquiv">1</span> Full + 0 Half per member per date.
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <div class="me-auto text-muted small" id="bulkSummaryText"></div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="bulkSubmitBtn">
                        <i class="ti ti-check me-1"></i>Apply Bulk Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.meal-sheet th, .meal-sheet td { border-color: #dee2e6 !important; }
.meal-sheet tbody tr:hover td { background-color: rgba(0,0,0,0.02); }
.meal-sheet tbody tr:hover td:first-child { background-color: inherit; }
.meal-toggle { transition: all .15s ease; }
.meal-toggle.loading { opacity: 0.5; pointer-events: none; }
@keyframes toastProgress { from { width: 100%; } to { width: 0%; } }
</style>

<div id="meal-toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:99999;"></div>

<link rel="stylesheet" href="{{ url('build/plugins/flatpickr/flatpickr.css') }}">
<script src="{{ URL::asset('build/plugins/flatpickr/flatpickr.js') }}"></script>
<script>
var csrf        = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var isManager   = {{ $isManager ? 'true' : 'false' }};
var myChanges   = {{ $myChangesToday }};
var attendanceUrl   = '{{ route("mess.meals.attendance", $mess->id) }}';
var mealCloseBase    = '{{ url("mess/" . $mess->id . "/meals") }}';
var mealTypeBase     = '{{ url("mess/" . $mess->id . "/meal-types") }}';

// ── Bootstrap Toast helper ───────────────────────────────────
function showToast(message, type) {
    // type: success | danger | warning | info
    var colors = {
        success: '#198754',
        danger:  '#dc3545',
        warning: '#ff9f43',
        info:    '#0dcaf0'
    };
    var icons = {
        success: 'ti-circle-check',
        danger:  'ti-circle-x',
        warning: 'ti-alert-triangle',
        info:    'ti-info-circle'
    };
    var bg   = colors[type]  || colors.info;
    var icon = icons[type]   || icons.info;
    var id   = 'toast-' + Date.now();

    var html =
        '<div id="' + id + '" class="toast align-items-center border-0 mb-2 show" role="alert" ' +
        'style="background:' + bg + ';color:#fff;min-width:220px;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,.2)">' +
        '<div class="d-flex align-items-center px-3 py-2 gap-2">' +
        '<i class="ti ' + icon + ' fs-5"></i>' +
        '<div class="flex-grow-1 fw-semibold" style="font-size:14px">' + message + '</div>' +
        '<button type="button" onclick="this.closest(\'.toast\').remove()" ' +
        'style="background:none;border:none;color:#fff;opacity:.8;cursor:pointer;font-size:18px;line-height:1">&times;</button>' +
        '</div>' +
        '<div class="toast-progress" style="height:3px;background:rgba(255,255,255,.4);border-radius:0 0 10px 10px;">' +
        '<div style="height:100%;background:rgba(255,255,255,.7);width:100%;animation:toastProgress 3s linear forwards;"></div>' +
        '</div></div>';

    var container = document.getElementById('meal-toast-container');
    container.insertAdjacentHTML('beforeend', html);

    setTimeout(function () {
        var el = document.getElementById(id);
        if (el) { el.style.opacity = '0'; el.style.transition = 'opacity .4s'; setTimeout(function(){ el.remove(); }, 400); }
    }, 3000);
}

// Disable my selects on load if limit already reached
if (!isManager && myChanges >= 3) {
    window.addEventListener('DOMContentLoaded', function () { disableMySelects(); });
}

// Force integer-only on all qty inputs (block decimal entry)
document.addEventListener('input', function(e) {
    if (e.target.matches('.full-qty-input, .half-qty-input, #bulkFullQty, #bulkHalfQty')) {
        var v = e.target.value;
        if (v !== '' && v !== '-') {
            e.target.value = Math.max(0, Math.floor(parseFloat(v) || 0));
        }
    }
});

// ── Flatpickr date picker ────────────────────────────────────
@php $dateParts = explode('-', $date); @endphp
flatpickr('#mealDatePicker', {
    dateFormat: 'd M Y',
    defaultDate: new Date({{ (int)$dateParts[0] }}, {{ (int)$dateParts[1] - 1 }}, {{ (int)$dateParts[2] }}),
    disableMobile: true,
    onChange: function (selectedDates) {
        var d   = selectedDates[0];
        var y   = d.getFullYear();
        var m   = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        window.location.href = '?date=' + y + '-' + m + '-' + day;
    }
});

// ── Meal quantity change ─────────────────────────────────────
// +/- buttons
document.addEventListener('click', function(e) {
    var btn = e.target.closest('.qty-btn');
    if (!btn) return;
    var scheduleId = btn.getAttribute('data-schedule');
    var userId     = btn.getAttribute('data-user');
    var target     = btn.getAttribute('data-target'); // 'full' or 'half'
    var dir        = parseInt(btn.getAttribute('data-dir'));
    var cell       = document.querySelector('.meal-cell[data-schedule="' + scheduleId + '"][data-user="' + userId + '"]');
    if (!cell) return;
    var inp = cell.querySelector('.' + target + '-qty-input');
    if (!inp || inp.disabled) return;
    var newVal = Math.max(0, (parseInt(inp.value) || 0) + dir);
    inp.value = newVal;
    submitMealQty(scheduleId, userId);
});

// debounce timers per cell
var _mealTimers = {};

function submitMealQty(scheduleId, userId) {
    var key = scheduleId + '_' + userId;
    clearTimeout(_mealTimers[key]);
    _mealTimers[key] = setTimeout(function() { _doSubmitMealQty(scheduleId, userId); }, 300);
}

function _doSubmitMealQty(scheduleId, userId) {
    var cell    = document.querySelector('.meal-cell[data-schedule="' + scheduleId + '"][data-user="' + userId + '"]');
    if (!cell) return;
    var fullInp = cell.querySelector('.full-qty-input');
    var halfInp = cell.querySelector('.half-qty-input');
    var fullQty = parseInt(fullInp ? fullInp.value : 0) || 0;
    var halfQty = parseInt(halfInp ? halfInp.value : 0) || 0;

    // Lock
    cell.querySelectorAll('input, button').forEach(function(el) { el.disabled = true; });

    fetch(attendanceUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ schedule_id: scheduleId, user_id: userId, full_qty: fullQty, half_qty: halfQty })
    })
    .then(function(r) { return r.text().then(function(t) { return { ok: r.ok, status: r.status, text: t }; }); })
    .then(function(result) {
        cell.querySelectorAll('input, button').forEach(function(el) { el.disabled = false; });

        var data = {};
        try { data = JSON.parse(result.text); } catch(e) {}

        if (!result.ok) {
            showToast(data.error || data.message || 'Server error ' + result.status, 'danger');
            if (data.limit_reached) { disableMyInputs(); }
            return;
        }

        // Update summary badge
        var badge = cell.querySelector('.meal-summary-badge');
        var qty   = fullQty + halfQty * 0.5;
        if (badge) {
            if (qty === 0) {
                badge.innerHTML = '<span class="badge bg-danger-subtle text-danger" style="font-size:10px;">Off</span>';
            } else {
                var label = (fullQty > 0 ? fullQty + 'F' : '') + (fullQty > 0 && halfQty > 0 ? '+' : '') + (halfQty > 0 ? halfQty + 'H' : '');
                badge.innerHTML = '<span class="badge bg-success-subtle text-success" style="font-size:10px;">' + label + '</span>';
            }
            // Cell bg
            var bg = qty === 0 ? '#fff5f5' : (fullQty > 0 && halfQty > 0 ? '#f5f0ff' : (halfQty > 0 ? '#fff8e1' : '#f0fff4'));
            cell.querySelectorAll('input[type=number]').forEach(function(i) { i.style.background = bg; });
        }
        // Off button style
        cell.querySelectorAll('.off-btn').forEach(function(b) {
            if (qty === 0) {
                b.classList.remove('btn-outline-danger'); b.classList.add('btn-danger');
            } else {
                b.classList.remove('btn-danger'); b.classList.add('btn-outline-danger');
            }
        });

        updateHeaderQty(scheduleId, data.sumFull, data.sumHalf);
        updateFooterQty(scheduleId, data.sumFull, data.sumHalf);

        var qty = fullQty + halfQty * 0.5;
        if (qty === 0) {
            showToast('Meal turned OFF', 'warning');
        } else {
            var lbl = (fullQty > 0 ? fullQty + ' full' : '') + (fullQty > 0 && halfQty > 0 ? ' + ' : '') + (halfQty > 0 ? halfQty + ' half' : '');
            showToast('Set to ' + lbl, 'success');
        }

        if (!isManager && data.remaining !== null && data.remaining !== undefined) {
            updateRemainingBadge(data.remaining);
            if (data.remaining === 0) { disableMyInputs(); }
        }
    })
    .catch(function() {
        cell.querySelectorAll('input, button').forEach(function(el) { el.disabled = false; });
        showToast('Request failed. Check your connection.', 'danger');
    });
}

function updateHeaderQty(scheduleId, sumFull, sumHalf) {
    var el = document.getElementById('hdr-qty-' + scheduleId);
    if (!el) return;
    var badges = el.querySelectorAll('strong');
    if (badges.length >= 2) {
        badges[0].textContent = sumFull;
        badges[1].textContent = sumHalf;
    }
}

function updateFooterQty(scheduleId, sumFull, sumHalf) {
    var cell = document.getElementById('foot-' + scheduleId);
    if (!cell) return;
    var badges = cell.querySelectorAll('strong');
    if (badges.length >= 2) {
        badges[0].textContent = sumFull;
        badges[1].textContent = sumHalf;
    }
}

function updateRowTotalById(userId) {}

function setMealOff(scheduleId, userId, btn) {
    var cell    = document.querySelector('.meal-cell[data-schedule="' + scheduleId + '"][data-user="' + userId + '"]');
    if (!cell) return;
    var fullInp = cell.querySelector('.full-qty-input');
    var halfInp = cell.querySelector('.half-qty-input');
    if (fullInp) fullInp.value = 0;
    if (halfInp) halfInp.value = 0;
    // Style off button as active
    cell.querySelectorAll('.off-btn').forEach(function(b) {
        b.classList.remove('btn-outline-danger');
        b.classList.add('btn-danger');
    });
    submitMealQty(scheduleId, userId);
}

function updateRemainingBadge(remaining) {
    var el = document.getElementById('changes-remaining');
    if (!el) return;
    el.textContent = remaining + '/3 changes left today';
    var wrapper = el.closest('.d-flex.align-items-center');
    if (wrapper && remaining === 0) {
        wrapper.classList.remove('border-secondary', 'bg-light');
        wrapper.classList.add('border-danger', 'bg-danger-subtle');
        el.classList.remove('text-muted');
        el.classList.add('text-danger');
        var icon = wrapper.querySelector('i');
        if (icon) { icon.classList.remove('text-muted'); icon.classList.add('text-danger'); }
    }
}

function disableMyInputs() {
    var myUserId = '{{ Auth::id() }}';
    var msg = 'You have used all 3 changes for today. Contact your manager.';
    document.querySelectorAll('.full-qty-input[data-user="' + myUserId + '"], .half-qty-input[data-user="' + myUserId + '"], .qty-btn[data-user="' + myUserId + '"]').forEach(function(el) {
        el.disabled = true;
        el.title = msg;
    });
    showToast(msg, 'danger');
}
function disableMySelects() { disableMyInputs(); }

function openToggleMealType(id, name, action) {
    var isDisable = action === 'disable';
    var form = document.getElementById('toggleMealTypeForm');
    form.action = mealTypeBase + '/' + id;

    var icon    = document.getElementById('toggleMealTypeIcon');
    var msg     = document.getElementById('toggleMealTypeMsg');
    var subMsg  = document.getElementById('toggleMealTypeSubMsg');
    var btn     = document.getElementById('toggleMealTypeConfirmBtn');
    var header  = document.getElementById('toggleMealTypeHeader');

    if (isDisable) {
        icon.innerHTML   = '<i class="ti ti-eye-off text-warning"></i>';
        msg.textContent  = 'Disable "' + name + '"?';
        subMsg.textContent = 'Members will no longer be able to mark attendance for this meal type.';
        btn.className    = 'btn btn-warning px-4';
        btn.innerHTML    = '<i class="ti ti-eye-off me-1"></i>Yes, Disable';
        header.querySelector('i').className = 'ti ti-alert-triangle text-warning fs-5';
    } else {
        icon.innerHTML   = '<i class="ti ti-eye text-success"></i>';
        msg.textContent  = 'Re-enable "' + name + '"?';
        subMsg.textContent = 'This meal type will become active and visible to all members.';
        btn.className    = 'btn btn-success px-4';
        btn.innerHTML    = '<i class="ti ti-eye me-1"></i>Yes, Enable';
        header.querySelector('i').className = 'ti ti-circle-check text-success fs-5';
        // Re-enable uses PUT to update is_active=1 via edit form instead
        // For simplicity, swap method to PUT and add hidden field
    }

    btn.onclick = function() {
        if (!isDisable) {
            // For enable: switch to PUT with is_active=1
            var methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.value = 'PUT';
            var nameInput = form.querySelector('input[name="name"]');
            if (!nameInput) {
                nameInput = document.createElement('input');
                nameInput.type = 'hidden';
                nameInput.name = 'name';
                form.appendChild(nameInput);
            }
            nameInput.value = name;
            var activeInput = form.querySelector('input[name="is_active"]');
            if (!activeInput) {
                activeInput = document.createElement('input');
                activeInput.type = 'hidden';
                activeInput.name = 'is_active';
                form.appendChild(activeInput);
            }
            activeInput.value = '1';
        } else {
            var methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.value = 'DELETE';
            // Remove any leftover enable inputs
            ['name','is_active'].forEach(function(n) {
                var el = form.querySelector('input[name="' + n + '"]');
                if (el) el.remove();
            });
        }
        form.submit();
    };

    new bootstrap.Modal(document.getElementById('toggleMealTypeModal')).show();
}

function openEditMealType(id, name, closeTime, daysBefore, isActive, sortOrder) {
    var base = mealTypeBase + '/' + id;
    document.getElementById('editMealTypeForm').action = base;
    document.getElementById('edit-mt-name').value   = name;
    document.getElementById('edit-mt-close').value  = closeTime || '';
    document.getElementById('edit-mt-days').value   = String(daysBefore || 0);
    document.getElementById('edit-mt-active').value = isActive ? '1' : '0';
    document.getElementById('edit-mt-sort').value   = sortOrder || 1;
    var modal = new bootstrap.Modal(document.getElementById('editMealTypeModal'));
    modal.show();
}

function closeMeal(scheduleId, mealName) {
    document.getElementById('closeMealName').textContent = mealName;
    document.getElementById('closeMealScheduleId').value = scheduleId;
    document.getElementById('closeMealMealName').value   = mealName;
    new bootstrap.Modal(document.getElementById('closeMealModal')).show();
}

function reopenMeal(scheduleId, mealName) {
    document.getElementById('reopenMealName').textContent = mealName;
    document.getElementById('reopenMealScheduleId').value = scheduleId;
    document.getElementById('reopenMealMealName').value   = mealName;
    new bootstrap.Modal(document.getElementById('reopenMealModal')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Close meal confirm
    var closeBtn = document.getElementById('closeMealConfirmBtn');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            var scheduleId = document.getElementById('closeMealScheduleId').value;
            var mealName   = document.getElementById('closeMealMealName').value;
            bootstrap.Modal.getInstance(document.getElementById('closeMealModal')).hide();
            showToast('Closing ' + mealName + '…', 'info');
            fetch(mealCloseBase + '/' + scheduleId + '/close', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ meal_cost: 0 })
            })
            .then(function(r) { return r.json(); })
            .then(function() {
                showToast(mealName + ' closed successfully.', 'success');
                setTimeout(function() { location.reload(); }, 1500);
            })
            .catch(function() { showToast('Failed to close meal.', 'danger'); });
        });
    }

    // Reopen meal confirm
    var reopenBtn = document.getElementById('reopenMealConfirmBtn');
    if (reopenBtn) {
        reopenBtn.addEventListener('click', function() {
            var scheduleId = document.getElementById('reopenMealScheduleId').value;
            var mealName   = document.getElementById('reopenMealMealName').value;
            bootstrap.Modal.getInstance(document.getElementById('reopenMealModal')).hide();
            showToast('Reopening ' + mealName + '…', 'info');
            fetch(mealCloseBase + '/' + scheduleId + '/reopen', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({})
            })
            .then(function(r) { return r.json(); })
            .then(function() {
                showToast(mealName + ' reopened successfully.', 'success');
                setTimeout(function() { location.reload(); }, 1500);
            })
            .catch(function() { showToast('Failed to reopen meal.', 'danger'); });
        });
    }
});

// ===================== BULK ATTENDANCE JS =====================
(function () {
    var bulkDates   = [];
    var calYear, calMonth;
    var today = new Date().toISOString().substring(0, 10);

    // ── helpers ──────────────────────────────────────────────────────────────
    function pad(n) { return String(n).padStart(2, '0'); }
    function toYMD(y, m, d) { return y + '-' + pad(m + 1) + '-' + pad(d); }
    function toggle(d) {
        var i = bulkDates.indexOf(d);
        if (i === -1) bulkDates.push(d); else bulkDates.splice(i, 1);
    }

    // ── calendar render ───────────────────────────────────────────────────────
    function renderCal() {
        var grid  = document.getElementById('bulkCalGrid');
        var title = document.getElementById('bulkCalTitle');
        if (!grid) return;
        var names = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        var monthNames = ['January','February','March','April','May','June',
                          'July','August','September','October','November','December'];
        title.textContent = monthNames[calMonth] + ' ' + calYear;
        grid.innerHTML = '';

        // Day-of-week headers
        names.forEach(function (n) {
            var h = document.createElement('div');
            h.textContent = n;
            h.style.cssText = 'font-size:10px;font-weight:600;color:#6c757d;padding:2px 0;';
            grid.appendChild(h);
        });

        var firstDow = new Date(calYear, calMonth, 1).getDay();
        var daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();

        // Empty cells before first day
        for (var i = 0; i < firstDow; i++) {
            grid.appendChild(document.createElement('div'));
        }

        for (var d = 1; d <= daysInMonth; d++) {
            var ymd = toYMD(calYear, calMonth, d);
            var cell = document.createElement('button');
            cell.type = 'button';
            cell.textContent = d;
            var dow = new Date(calYear, calMonth, d).getDay();
            var isWeekend = (dow === 5 || dow === 6);
            var selected  = bulkDates.indexOf(ymd) !== -1;
            var isToday   = ymd === today;
            var isPastDay = ymd < today;
            var blocked   = !isManager && isPastDay;

            if (blocked) {
                cell.disabled = true;
                cell.style.cssText = 'width:100%;border-radius:6px;font-size:12px;padding:5px 2px;border:1px solid;' +
                    'background:#f0f0f0;color:#bbb;border-color:#e0e0e0;cursor:not-allowed;text-decoration:line-through;';
                cell.title = 'Cannot edit past dates';
            } else {
                cell.style.cssText = [
                    'width:100%;border-radius:6px;font-size:12px;padding:5px 2px;border:1px solid;',
                    'cursor:pointer;transition:all .12s;font-weight:' + (isWeekend ? '600' : '400') + ';',
                    selected
                        ? 'background:#198754;color:#fff;border-color:#198754;'
                        : isToday
                            ? 'background:#e8f5e9;color:#198754;border-color:#a5d6a7;'
                            : isWeekend
                                ? 'background:#fff8e1;color:#795548;border-color:#ffe082;'
                                : 'background:#f8f9fa;color:#333;border-color:#dee2e6;'
                ].join('');

                cell.dataset.ymd = ymd;
                cell.addEventListener('click', function () {
                    toggle(this.dataset.ymd);
                    renderCal();
                    syncHidden();
                    updateBulkSummary();
                });
            }
            grid.appendChild(cell);
        }

        document.getElementById('bulkDateCount').textContent =
            bulkDates.length > 0 ? bulkDates.length + ' date(s) selected' : 'No dates selected.';
    }

    function syncHidden() {
        var hidden = document.getElementById('bulkDatesHidden');
        hidden.innerHTML = '';
        bulkDates.forEach(function (d) {
            var inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'dates[]'; inp.value = d;
            hidden.appendChild(inp);
        });
    }

    // ── quick-select ──────────────────────────────────────────────────────────
    window.bulkQuick = function (type) {
        var daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();
        if (type === 'clear') { bulkDates = []; }
        else {
            for (var d = 1; d <= daysInMonth; d++) {
                var ymd     = toYMD(calYear, calMonth, d);
                var dow     = new Date(calYear, calMonth, d).getDay();
                var already = bulkDates.indexOf(ymd) !== -1;
                // Non-managers cannot select past dates
                if (!isManager && ymd < today) continue;
                if (type === 'all')     { if (!already) bulkDates.push(ymd); }
                else if (type === 'fri')     { if (dow === 5 && !already) bulkDates.push(ymd); }
                else if (type === 'sat')     { if (dow === 6 && !already) bulkDates.push(ymd); }
                else if (type === 'weekend') { if ((dow === 5 || dow === 6) && !already) bulkDates.push(ymd); }
            }
        }
        renderCal(); syncHidden(); updateBulkSummary();
    };

    // ── month navigation ──────────────────────────────────────────────────────
    window.bulkCalPrev = function () {
        if (calMonth === 0) { calMonth = 11; calYear--; } else { calMonth--; }
        renderCal();
    };
    window.bulkCalNext = function () {
        if (calMonth === 11) { calMonth = 0; calYear++; } else { calMonth++; }
        renderCal();
    };

    // ── bulk qty spinners ─────────────────────────────────────────────────────
    window.setBulkOff = function() {
        document.getElementById('bulkFullQty').value = 0;
        document.getElementById('bulkHalfQty').value = 0;
        var btn = document.getElementById('bulkOffBtn');
        if (btn) { btn.classList.remove('btn-outline-danger'); btn.classList.add('btn-danger'); }
        updateBulkQtyEquiv();
        updateBulkSummary();
    };

    window.adjustBulk = function(type, dir) {
        var id  = type === 'full' ? 'bulkFullQty' : 'bulkHalfQty';
        var inp = document.getElementById(id);
        if (!inp) return;
        inp.value = Math.max(0, (parseInt(inp.value) || 0) + dir);
        resetBulkOffBtn();
        updateBulkQtyEquiv();
        updateBulkSummary();
    };

    function resetBulkOffBtn() {
        var full = parseInt(document.getElementById('bulkFullQty').value) || 0;
        var half = parseInt(document.getElementById('bulkHalfQty').value) || 0;
        var btn  = document.getElementById('bulkOffBtn');
        if (!btn) return;
        if (full === 0 && half === 0) {
            btn.classList.remove('btn-outline-danger'); btn.classList.add('btn-danger');
        } else {
            btn.classList.remove('btn-danger'); btn.classList.add('btn-outline-danger');
        }
    }

    function updateBulkQtyEquiv() {
        var full = parseInt(document.getElementById('bulkFullQty').value) || 0;
        var half = parseInt(document.getElementById('bulkHalfQty').value) || 0;
        var el   = document.getElementById('bulkQtyEquiv');
        if (el) el.parentElement.innerHTML = '<i class="ti ti-info-circle me-1"></i><strong>' + full + '</strong> Full + <strong>' + half + '</strong> Half per member per date.';
    }

    document.getElementById('bulkFullQty').addEventListener('input', function() { resetBulkOffBtn(); updateBulkQtyEquiv(); updateBulkSummary(); });
    document.getElementById('bulkHalfQty').addEventListener('input', function() { resetBulkOffBtn(); updateBulkQtyEquiv(); updateBulkSummary(); });

    // ── member / meal-type toggles ────────────────────────────────────────────
    window.bulkSelectAll  = function () { document.querySelectorAll('.bulk-member-cb').forEach(function(c){ c.checked = true; }); updateBulkSummary(); };
    window.bulkSelectNone = function () { document.querySelectorAll('.bulk-member-cb').forEach(function(c){ c.checked = false; }); updateBulkSummary(); };
    window.bulkMtAll      = function () { document.querySelectorAll('.bulk-mt-cb').forEach(function(c){ c.checked = true; }); updateBulkSummary(); };
    window.bulkMtNone     = function () { document.querySelectorAll('.bulk-mt-cb').forEach(function(c){ c.checked = false; }); updateBulkSummary(); };

    // ── summary ───────────────────────────────────────────────────────────────
    function updateBulkSummary() {
        var members   = document.querySelectorAll('.bulk-member-cb:checked').length;
        var mealTypes = document.querySelectorAll('.bulk-mt-cb:checked').length;
        var dates     = bulkDates.length;
        var full      = parseInt(document.getElementById('bulkFullQty').value) || 0;
        var half      = parseInt(document.getElementById('bulkHalfQty').value) || 0;
        var equiv     = full + half * 0.5;
        var total     = members * mealTypes * dates;
        var text      = document.getElementById('bulkSummaryText');
        if (!text) return;
        var qtyLabel  = equiv === 0 ? 'Off' : (full + 'F + ' + half + 'H (' + equiv + ')');
        text.innerHTML = total > 0
            ? '<strong>' + total + '</strong> record(s) → <strong>' + qtyLabel + '</strong>'
            : 'Select members, dates and meal types.';
    }
    window.updateBulkSummary = updateBulkSummary;

    // ── init ──────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        var initDate = new Date('{{ $date }}');
        calYear  = initDate.getFullYear();
        calMonth = initDate.getMonth();
        renderCal();

        document.querySelectorAll('.bulk-member-cb, .bulk-mt-cb').forEach(function (cb) {
            cb.addEventListener('change', updateBulkSummary);
        });

        // Re-init calendar when modal opens (in case month drifted)
        var modal = document.getElementById('bulkAttendanceModal');
        if (modal) {
            modal.addEventListener('show.bs.modal', function () {
                renderCal();
                updateBulkSummary();
            });
        }
    });
}());
</script>

@if($isManager)
{{-- ===================== WHATSAPP MODAL ===================== --}}
@php
    $dateLabel = \Carbon\Carbon::parse($date)->format('l, d M Y');
    $messName  = $mess->name;
@endphp
<div class="modal fade" id="whatsappModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="ti ti-brand-whatsapp me-2"></i>Share Meal Count via WhatsApp</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Contact selector --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Send To <span class="text-danger">*</span></label>
                    @if($contacts->isEmpty())
                    <div class="alert alert-warning py-2 small">
                        <i class="ti ti-alert-circle me-1"></i>No contacts saved.
                        <a href="{{ route('mess.settings', $mess->id) }}" class="fw-semibold">Add contacts in Mess Settings → Phone Book</a>
                    </div>
                    @else
                    <select id="waContact" class="form-select" onchange="buildWaMessage()">
                        <option value="">— Select Contact —</option>
                        @foreach($contacts as $c)
                        <option value="{{ $c->waPhone() }}" data-name="{{ $c->name }}">
                            {{ $c->name }} — {{ $c->phone }}
                        </option>
                        @endforeach
                        <option value="custom">✎ Enter manually…</option>
                    </select>
                    <input type="text" id="waCustomPhone" class="form-control mt-2 d-none"
                        placeholder="WhatsApp number with country code (e.g. 8801712345678)"
                        oninput="buildWaMessage()">
                    @endif
                </div>

                {{-- Meal checkboxes with totals --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Include Meals</label>
                    <div class="row g-2">
                        @foreach($mealTypes as $mt)
                        @php
                            $tot     = $mealTotals[$mt->name] ?? ['full' => 0, 'half' => 0];
                            $hasData = ($tot['full'] + $tot['half']) > 0;
                        @endphp
                        <div class="col-auto">
                            <div class="form-check form-check-inline border rounded px-3 py-2">
                                <input class="form-check-input wa-meal-check" type="checkbox"
                                    id="waMeal_{{ $loop->index }}"
                                    data-name="{{ $mt->name }}"
                                    data-schedule="{{ ($schedules[$mt->name] ?? null)?->id ?? '' }}"
                                    data-full="{{ $tot['full'] }}"
                                    data-half="{{ $tot['half'] }}"
                                    {{ $hasData ? 'checked' : '' }}
                                    onchange="buildWaMessage()">
                                <label class="form-check-label" for="waMeal_{{ $loop->index }}">
                                    <span class="fw-semibold">{{ $mt->name }}</span>
                                    <span class="ms-1" style="font-size:11px;">
                                        <span class="badge bg-success bg-opacity-75">{{ $tot['full'] }}F</span>
                                        <span class="badge bg-warning text-dark bg-opacity-75">{{ $tot['half'] }}H</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-2 small text-muted">
                        Total selected: <strong id="waTotalSel">0F + 0H</strong>
                    </div>
                </div>

                {{-- Custom note --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Extra Note <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="text" id="waNote" class="form-control" maxlength="200"
                        placeholder="e.g. Please cook extra rice today" oninput="buildWaMessage()">
                </div>

                {{-- Message preview --}}
                <div>
                    <label class="form-label fw-semibold">Message Preview</label>
                    <textarea id="waPreview" class="form-control font-monospace" rows="8" readonly
                        style="font-size:13px;background:#f8f9fa;resize:none;"></textarea>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="copyWaMessage()">
                    <i class="ti ti-copy me-1"></i>Copy Message
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a id="waSendBtn" href="#" target="_blank" rel="noopener"
                        class="btn btn-success disabled" style="pointer-events:none;">
                        <i class="ti ti-brand-whatsapp me-1"></i>Open WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const dateLabel = @json($dateLabel);
    const messName  = @json($messName);

    function buildWaMessage() {
        const checks = document.querySelectorAll('.wa-meal-check:checked');
        let lines = [], grandFull = 0, grandHalf = 0;
        checks.forEach(c => {
            const n      = c.dataset.name;
            const schedId = c.dataset.schedule;
            let f = parseInt(c.dataset.full) || 0;
            let h = parseInt(c.dataset.half) || 0;
            // Read live totals from footer Full/Half badges if available
            if (schedId) {
                const footCell = document.getElementById('foot-' + schedId);
                if (footCell) {
                    const strongs = footCell.querySelectorAll('strong');
                    if (strongs.length >= 2) {
                        f = parseInt(strongs[0].textContent) || 0;
                        h = parseInt(strongs[1].textContent) || 0;
                    }
                }
            }
            // Also update data attributes for future reads
            c.dataset.full = f;
            c.dataset.half = h;
            const parts = [];
            if (f > 0) parts.push(f + ' Full');
            if (h > 0) parts.push(h + ' Half');
            lines.push(n + ': ' + (parts.length ? parts.join(' + ') : '0'));
            grandFull += f;
            grandHalf += h;
        });
        document.getElementById('waTotalSel').textContent = grandFull + 'F + ' + grandHalf + 'H';

        let msg = '🍽️ *' + messName + '* — Meal Count\n';
        msg += '📅 ' + dateLabel + '\n\n';
        if (lines.length === 0) {
            msg += '(No meals selected)';
        } else {
            lines.forEach(l => { msg += '• ' + l + '\n'; });
            const totalParts = [];
            if (grandFull > 0) totalParts.push(grandFull + ' Full');
            if (grandHalf > 0) totalParts.push(grandHalf + ' Half');
            msg += '\n*Total: ' + (totalParts.length ? totalParts.join(' + ') : '0') + '*';
        }
        const note = document.getElementById('waNote').value.trim();
        if (note) msg += '\n\n📝 ' + note;

        document.getElementById('waPreview').value = msg;

        // Enable send button if contact chosen and at least one meal
        const phone = getPhone();
        const btn = document.getElementById('waSendBtn');
        if (!phone || lines.length === 0) {
            btn.classList.add('disabled');
            btn.style.pointerEvents = 'none';
            btn.href = '#';
        } else {
            const url = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(msg);
            btn.href = url;
            btn.classList.remove('disabled');
            btn.style.pointerEvents = '';
        }
    }

    function getPhone() {
        const sel = document.getElementById('waContact');
        if (!sel) return null;
        if (sel.value === 'custom') {
            const v = document.getElementById('waCustomPhone').value.replace(/\D/g, '');
            return v.length >= 7 ? v : null;
        }
        return sel.value || null;
    }

    window.buildWaMessage = buildWaMessage;

    window.copyWaMessage = function() {
        const ta = document.getElementById('waPreview');
        navigator.clipboard.writeText(ta.value).then(() => {
            const btn = event.target.closest('button');
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="ti ti-check me-1"></i>Copied!';
            setTimeout(() => { btn.innerHTML = orig; }, 2000);
        });
    };

    // Show/hide custom phone input
    const contactSel = document.getElementById('waContact');
    if (contactSel) {
        contactSel.addEventListener('change', function() {
            const custom = document.getElementById('waCustomPhone');
            custom.classList.toggle('d-none', this.value !== 'custom');
            buildWaMessage();
        });
    }

    // Init message on modal open
    const waModal = document.getElementById('whatsappModal');
    if (waModal) {
        waModal.addEventListener('show.bs.modal', buildWaMessage);
    }
}());
</script>
@endif
{{-- Routine Items Viewer Modal --}}
<div class="modal fade" id="routineItemsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <div>
                    <h6 class="modal-title mb-0"><i class="ti ti-tools-kitchen-2 me-2"></i><span id="routineModalTitle"></span></h6>
                    <div class="text-white text-opacity-75" style="font-size:11px;" id="routineModalDate"></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Items display --}}
                <div id="routineModalPills" class="mb-3"></div>
                {{-- Empty state --}}
                <div id="routineModalEmpty" class="text-center py-3 d-none">
                    <i class="ti ti-salad text-muted" style="font-size:36px;"></i>
                    <div class="text-muted small mt-1">No menu set for this day.</div>
                </div>
                {{-- Edit / Add form --}}
                <div id="routineModalForm" class="d-none">
                    <label class="form-label small fw-semibold">Menu items <span class="text-muted fw-normal">(comma or line separated)</span></label>
                    <textarea id="routineModalTextarea" class="form-control" rows="4"
                        placeholder="e.g. Plain Rice, Chicken Curry, Dal, Salad"></textarea>
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="routineModalCancelBtn">Cancel</button>
                        <button type="button" class="btn btn-primary btn-sm flex-fill" id="routineModalSaveBtn">
                            <i class="ti ti-check me-1"></i>Save Menu
                        </button>
                    </div>
                </div>
                {{-- Edit trigger (shown when items exist and form is hidden) --}}
                <div id="routineModalEditRow" class="d-none text-end">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="routineModalEditBtn">
                        <i class="ti ti-pencil me-1"></i>Edit Menu
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function(){
    var _mealType;
    var _weekNo    = {{ \App\Models\MessMealRoutine::weekNoForDate(\Carbon\Carbon::parse($date)) }};
    var _dayOfWeek = {{ (int)\Carbon\Carbon::parse($date)->format('w') }};
    var upsertUrl  = '{{ route("mess.meal-routine.upsert", $mess->id) }}';
    var csrf       = '{{ csrf_token() }}';

    // JS map of mealType -> items (initialized from server data, updated after save)
    var routineMap = @json($routineItems->toArray());

    function slugMealType(name) {
        return name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    }

    function renderModalPills(items) {
        var pillsEl  = document.getElementById('routineModalPills');
        var emptyEl  = document.getElementById('routineModalEmpty');
        var editRow  = document.getElementById('routineModalEditRow');
        pillsEl.innerHTML = '';
        if (!items) {
            emptyEl.classList.remove('d-none');
            editRow.classList.add('d-none');
            return;
        }
        emptyEl.classList.add('d-none');
        var list = items.split(/[\n,]/).map(function(s){ return s.trim(); }).filter(Boolean);
        pillsEl.style.cssText = 'font-size:14px;color:#333;border:1px solid #e3e3e3;border-radius:6px;padding:10px;';
        pillsEl.textContent = list.join(', ');
        editRow.classList.remove('d-none');
    }

    function updateHeaderPills(mealType, items) {
        var slug = slugMealType(mealType);
        var container = document.querySelector('.hdr-items-' + slug);
        if (!container) return;
        container.innerHTML = '';
        if (!items) { container.style.display = 'none'; return; }
        var list = items.split(/[\n,]/).map(function(s){ return s.trim(); }).filter(Boolean);
        container.textContent = list.join(', ');
        container.style.display = '';
        // Update icon button style
        document.querySelectorAll('.routine-view-btn').forEach(function(b) {
            if (b.dataset.mealType === mealType) {
                b.classList.remove('text-muted');
                b.classList.add('text-primary');
                b.querySelector('i').className = 'ti ti-list-details';
            }
        });
    }

    window.showRoutineItems = function(mealType) {
        _mealType = mealType;
        var items = routineMap[mealType] || null;
        document.getElementById('routineModalTitle').textContent = mealType;
        document.getElementById('routineModalDate').textContent  = '{{ \Carbon\Carbon::parse($date)->format("l, d M Y") }}';
        document.getElementById('routineModalForm').classList.add('d-none');
        document.getElementById('routineModalTextarea').value = '';
        document.getElementById('routineModalTextarea').classList.remove('is-invalid');
        renderModalPills(items);
        if (!items) {
            document.getElementById('routineModalForm').classList.remove('d-none');
            document.getElementById('routineModalEditRow').classList.add('d-none');
        }
        new bootstrap.Modal(document.getElementById('routineItemsModal')).show();
    };

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('routineModalEditBtn').addEventListener('click', function() {
            var items = routineMap[_mealType] || '';
            document.getElementById('routineModalTextarea').value = items;
            document.getElementById('routineModalForm').classList.remove('d-none');
            document.getElementById('routineModalEditRow').classList.add('d-none');
        });

        document.getElementById('routineModalCancelBtn').addEventListener('click', function() {
            document.getElementById('routineModalForm').classList.add('d-none');
            var items = routineMap[_mealType] || null;
            if (items) document.getElementById('routineModalEditRow').classList.remove('d-none');
        });

        document.getElementById('routineModalSaveBtn').addEventListener('click', function() {
            var textarea = document.getElementById('routineModalTextarea');
            var items    = textarea.value.trim();
            if (!items) { textarea.classList.add('is-invalid'); return; }
            textarea.classList.remove('is-invalid');
            var btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';
            fetch(upsertUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ meal_type: _mealType, week_no: _weekNo, day_of_week: _dayOfWeek, items: items })
            })
            .then(function(r){ return r.json(); })
            .then(function(d){
                if (d.success) {
                    routineMap[_mealType] = items;
                    renderModalPills(items);
                    updateHeaderPills(_mealType, items);
                    document.getElementById('routineModalForm').classList.add('d-none');
                }
                btn.disabled = false;
                btn.innerHTML = '<i class="ti ti-check me-1"></i>Save Menu';
            })
            .catch(function(){
                btn.disabled = false;
                btn.innerHTML = '<i class="ti ti-check me-1"></i>Save Menu';
            });
        });
    });
})();
</script>
</script>
{{-- Reopen Meal Confirmation Modal --}}
<div class="modal fade" id="reopenMealModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:60px;height:60px;background:#d1fae5;">
                        <i class="ti ti-lock-open" style="font-size:1.8rem;color:#059669;"></i>
                    </span>
                </div>
                <h6 class="fw-bold mb-1">Reopen <span id="reopenMealName"></span>?</h6>
                <p class="text-muted small mb-4">Members will be able to change their attendance again after reopening.</p>
                <input type="hidden" id="reopenMealScheduleId">
                <input type="hidden" id="reopenMealMealName">
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success px-4" id="reopenMealConfirmBtn">
                        <i class="ti ti-lock-open me-1"></i>Yes, Reopen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Close Meal Confirmation Modal --}}
<div class="modal fade" id="closeMealModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:60px;height:60px;background:#fef3c7;">
                        <i class="ti ti-lock" style="font-size:1.8rem;color:#d97706;"></i>
                    </span>
                </div>
                <h6 class="fw-bold mb-1">Close <span id="closeMealName"></span>?</h6>
                <p class="text-muted small mb-4">No further attendance changes will be allowed after closing.</p>
                <input type="hidden" id="closeMealScheduleId">
                <input type="hidden" id="closeMealMealName">
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning px-4" id="closeMealConfirmBtn">
                        <i class="ti ti-lock me-1"></i>Yes, Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
