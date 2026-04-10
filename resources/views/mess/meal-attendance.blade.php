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
                            <span class="badge bg-{{ $mt->isExpired() && !$isPast ? 'secondary' : 'primary' }}-subtle border border-{{ $mt->isExpired() && !$isPast ? 'secondary' : 'primary' }} text-dark">
                                {{ $mt->name }}
                                @if($mt->close_time) <span class="text-muted">· closes {{ \Carbon\Carbon::parse($mt->close_time)->format('g:i A') }}</span> @endif
                            </span>
                            @if($isManager && $sch && $sch->status === 'open' && !$isPast)
                            <button class="btn btn-xs btn-outline-danger py-0" onclick="closeMeal({{ $sch->id }}, '{{ $mt->name }}')" title="Close {{ $mt->name }}">
                                <i class="ti ti-lock" style="font-size:11px"></i>
                            </button>
                            @elseif($sch && $sch->status === 'closed')
                            <span class="badge bg-secondary" style="font-size:10px">Closed</span>
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
                                    $sch     = $schedules[$mt->name] ?? null;
                                    $expired = !$isPast && $mt->isExpired();
                                    $closed  = $sch && $sch->status === 'closed';
                                    $totalQ  = $sch ? $allAttendances->filter(fn($g,$k) => str_starts_with($k, $sch->id.'_'))->flatten()->sum('quantity') : 0;
                                @endphp
                                <th class="text-center {{ $closed ? 'bg-secondary bg-opacity-10' : 'bg-light' }}" style="min-width:140px;">
                                    <div class="fw-semibold">{{ $mt->name }}</div>
                                    @if($closed)
                                        <span class="badge bg-secondary" style="font-size:10px">Closed</span>
                                    @elseif($expired)
                                        <span class="badge bg-warning text-dark" style="font-size:10px">Expired</span>
                                    @elseif($sch)
                                        <div class="text-success" style="font-size:10px" id="hdr-qty-{{ $sch->id }}">{{ $totalQ }} meals · {{ $members->count() }} members</div>
                                    @endif
                                </th>
                                @endforeach
                                <th class="bg-light text-center" style="min-width:90px;">Total<br><span style="font-size:10px;font-weight:400">meals today</span></th>
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
                                    $qty       = $att ? (float)$att->quantity : 1.0; // default 1
                                    $presets   = [0, 0.5, 1, 1.5, 2, 2.5, 3];
                                    $isCustom  = !in_array($qty, $presets);
                                    $locked    = $isPast || !$sch || $sch->status === 'closed'
                                                 || (!$isManager && $mt->isExpired());
                                    $canChange = $canEdit && !$locked;
                                    $totalQty += $qty;
                                    $cost      = null; // rate calculated from market expenses, not meal type
                                @endphp
                                <td class="text-center align-middle p-1 {{ $locked ? 'bg-light' : '' }}">
                                    @if($sch)
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <select class="meal-qty-select form-select form-select-sm text-center fw-semibold"
                                            style="width:100px;font-size:13px;border-radius:20px;
                                                   background:{{ $qty == 0 ? '#fff5f5' : ($qty > 1 ? '#f0f8ff' : '#f0fff4') }};
                                                   color:{{ $qty == 0 ? '#dc3545' : ($qty > 1 ? '#0d6efd' : '#198754') }};
                                                   border-color:{{ $qty == 0 ? '#dc3545' : ($qty > 1 ? '#0d6efd' : '#198754') }};"
                                            data-schedule="{{ $sch->id }}"
                                            data-user="{{ $mem->user_id }}"
                                            {{ !$canChange ? 'disabled' : '' }}
                                            onchange="{{ $canChange ? 'changeMealQty(this)' : '' }}"
                                            title="{{ $locked ? ($isPast ? 'Past date' : ($sch->status==='closed' ? 'Meal closed' : 'Time expired')) : '' }}"
                                        >
                                            <option value="0"      {{ (!$isCustom && $qty == 0)   ? 'selected' : '' }}>✕ Off</option>
                                            <option value="0.5"    {{ (!$isCustom && $qty == 0.5) ? 'selected' : '' }}>½ meal</option>
                                            <option value="1"      {{ (!$isCustom && $qty == 1)   ? 'selected' : '' }}>1 meal</option>
                                            <option value="1.5"    {{ (!$isCustom && $qty == 1.5) ? 'selected' : '' }}>1½ meals</option>
                                            <option value="2"      {{ (!$isCustom && $qty == 2)   ? 'selected' : '' }}>2 meals</option>
                                            <option value="2.5"    {{ (!$isCustom && $qty == 2.5) ? 'selected' : '' }}>2½ meals</option>
                                            <option value="3"      {{ (!$isCustom && $qty == 3)   ? 'selected' : '' }}>3 meals</option>
                                            <option value="custom" {{ $isCustom ? 'selected' : '' }}>✎ Custom…</option>
                                        </select>
                                        <input type="number"
                                            class="meal-custom-input form-control form-control-sm text-center fw-semibold {{ $isCustom ? '' : 'd-none' }}"
                                            style="width:100px;font-size:13px;border-radius:20px;"
                                            min="0" step="0.5" placeholder="e.g. 4"
                                            value="{{ $isCustom ? $qty : '' }}"
                                            data-schedule="{{ $sch->id }}"
                                            data-user="{{ $mem->user_id }}"
                                            {{ !$canChange ? 'disabled' : '' }}
                                            onkeydown="if(event.key==='Enter'){event.preventDefault();submitCustomQty(this);}"
                                            onblur="submitCustomQty(this)">
                                    </div>
                                    @else
                                    <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                @endforeach

                                {{-- Row total --}}
                                <td class="text-center align-middle fw-bold" id="row-total-{{ $mem->user_id }}">
                                    <span class="badge rounded-pill bg-{{ $totalQty > 0 ? 'success' : 'danger' }}" style="font-size:13px;">{{ $totalQty }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light fw-semibold">
                                <td class="bg-light small" style="position:sticky;left:0;z-index:1;">Total meals</td>
                                @foreach($mealTypes as $mt)
                                @php
                                    $sch  = $schedules[$mt->name] ?? null;
                                    $sumQ = 0;
                                    if ($sch) {
                                        $sumQ = $allAttendances->filter(fn($g, $k) => str_starts_with($k, $sch->id.'_'))->flatten()->sum('quantity');
                                    }
                                @endphp
                                <td class="text-center" id="foot-{{ $sch?->id ?? 'none' }}">
                                    <span class="badge bg-primary rounded-pill">{{ $sumQ }}</span>
                                </td>
                                @endforeach
                                <td></td>
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
                                <th>Name</th>
                                <th>Closes At</th>
                                <th>Status</th>
                                <th class="text-center" style="width:120px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allMealTypes as $mt)
                            <tr class="{{ !$mt->is_active ? 'opacity-50' : '' }}">
                                <td class="fw-semibold align-middle">
                                    {{ $mt->name }}
                                    @if(!$mt->is_active)<span class="badge bg-secondary ms-1" style="font-size:10px">Disabled</span>@endif
                                </td>
                                <td class="align-middle">{{ $mt->close_time ? \Carbon\Carbon::parse($mt->close_time)->format('g:i A') : '—' }}</td>
                                <td class="align-middle">
                                    <span class="badge bg-{{ $mt->is_active ? 'success' : 'secondary' }}">{{ $mt->is_active ? 'Active' : 'Disabled' }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <button class="btn btn-xs btn-outline-primary py-0"
                                        onclick="openEditMealType({{ $mt->id }},'{{ addslashes($mt->name) }}','{{ $mt->close_time ? substr($mt->close_time,0,5) : '' }}',{{ $mt->is_active ? 'true' : 'false' }})"
                                        title="Edit">
                                        <i class="ti ti-edit" style="font-size:11px"></i>
                                    </button>
                                    @if($mt->is_active)
                                    <form action="{{ route('mess.meal-types.destroy', [$mess->id, $mt->id]) }}" method="POST" class="d-inline ms-1">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-outline-warning py-0" title="Disable"
                                            onclick="return confirm('Disable {{ addslashes($mt->name) }}?')">
                                            <i class="ti ti-eye-off" style="font-size:11px"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button class="btn btn-xs btn-outline-success py-0 ms-1"
                                        onclick="openEditMealType({{ $mt->id }},'{{ addslashes($mt->name) }}','{{ $mt->close_time ? substr($mt->close_time,0,5) : '' }}',false,'enable')"
                                        title="Re-enable">
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
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Attendance Closes At</label>
                        <input type="time" name="close_time" class="form-control">
                        <div class="form-text">Leave blank for no cutoff.</div>
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
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="is_active" id="edit-mt-active" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Disabled</option>
                        </select>
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
@endif

<style>
.meal-sheet th, .meal-sheet td { border-color: #dee2e6 !important; }
.meal-sheet tbody tr:hover td { background-color: rgba(0,0,0,0.02); }
.meal-sheet tbody tr:hover td:first-child { background-color: inherit; }
.meal-toggle { transition: all .15s ease; }
.meal-toggle.loading { opacity: 0.5; pointer-events: none; }
@keyframes toastProgress { from { width: 100%; } to { width: 0%; } }
</style>

<div id="meal-toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:99999;"></div>

<script>
var csrf        = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var isManager   = {{ $isManager ? 'true' : 'false' }};
var myChanges   = {{ $myChangesToday }};
var attendanceUrl   = '{{ route("mess.meals.attendance", $mess->id) }}';
var mealCloseBase   = '{{ url("mess/" . $mess->id . "/meals") }}';

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

// ── Flatpickr — init after all footer scripts are loaded ─────
window.addEventListener('load', function () {
    if (typeof flatpickr !== 'undefined') {
        flatpickr('#mealDatePicker', {
            dateFormat: 'd M Y',
            defaultDate: '{{ $date }}',
            maxDate: '{{ $maxDate }}',
            disableMobile: true,
            onChange: function (selectedDates) {
                var d   = selectedDates[0];
                var y   = d.getFullYear();
                var m   = String(d.getMonth() + 1).padStart(2, '0');
                var day = String(d.getDate()).padStart(2, '0');
                window.location.href = '?date=' + y + '-' + m + '-' + day;
            }
        });
    }
});

// ── Meal quantity change ─────────────────────────────────────
function changeMealQty(select) {
    // "Custom…" chosen — show the number input instead of sending
    if (select.value === 'custom') {
        var wrap  = select.closest('.d-flex.flex-column');
        var input = wrap ? wrap.querySelector('.meal-custom-input') : null;
        if (input) {
            input.classList.remove('d-none');
            input.value = '';
            input.focus();
        }
        return;
    }

    var scheduleId = select.getAttribute('data-schedule');
    var userId     = select.getAttribute('data-user');
    var quantity   = parseFloat(select.value);

    sendMealQty(select, null, scheduleId, userId, quantity);
}

// Custom input: submitted on Enter or blur
function submitCustomQty(input) {
    // Ignore blur if input is empty / not shown
    if (input.classList.contains('d-none') || input.value === '') return;

    var quantity = parseFloat(input.value);
    if (isNaN(quantity) || quantity < 0) {
        showToast('Please enter a valid quantity (e.g. 4 or 4.5).', 'danger');
        return;
    }
    // Must be a multiple of 0.5
    if (Math.round(quantity * 10) % 5 !== 0) {
        showToast('Quantity must be in steps of 0.5 (e.g. 4, 4.5, 5).', 'warning');
        input.focus();
        return;
    }

    var scheduleId = input.getAttribute('data-schedule');
    var userId     = input.getAttribute('data-user');

    // Find the sibling select to sync its visual state
    var wrap   = input.closest('.d-flex.flex-column');
    var select = wrap ? wrap.querySelector('.meal-qty-select') : null;

    sendMealQty(select, input, scheduleId, userId, quantity);
}

function sendMealQty(select, customInput, scheduleId, userId, quantity) {
    // Lock both controls while sending
    if (select)      select.disabled = true;
    if (customInput) customInput.disabled = true;

    fetch(attendanceUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ schedule_id: scheduleId, user_id: userId, quantity: quantity })
    })
    .then(function (response) {
        return response.text().then(function (text) {
            return { ok: response.ok, status: response.status, text: text };
        });
    })
    .then(function (result) {
        if (select)      select.disabled = false;
        if (customInput) customInput.disabled = false;

        var data = {};
        try { data = JSON.parse(result.text); } catch (e) {}

        if (!result.ok) {
            showToast(data.error || data.message || 'Server error ' + result.status, 'danger');
            if (data.limit_reached) { disableMySelects(); }
            return;
        }

        // If custom value, keep the input visible and update select to show "Custom…"
        if (customInput) {
            customInput.classList.remove('d-none');
            if (select) {
                select.value = 'custom';
                applySelectStyle(select, quantity);
            }
        } else if (select) {
            applySelectStyle(select, quantity);
            // Hide custom input if it was previously visible
            var wrap  = select.closest('.d-flex.flex-column');
            var ci    = wrap ? wrap.querySelector('.meal-custom-input') : null;
            if (ci) ci.classList.add('d-none');
        }

        // Live totals
        updateHeaderQty(scheduleId, data.totalQty);
        updateFooterQty(scheduleId, data.totalQty);
        updateRowTotalById(userId);

        // Toast
        if (quantity === 0) {
            showToast('Meal turned OFF', 'warning');
        } else {
            showToast('Set to ' + quantity + ' meal' + (quantity !== 1 ? 's' : ''), 'success');
        }

        // Remaining badge
        if (!isManager && data.remaining !== null && data.remaining !== undefined) {
            updateRemainingBadge(data.remaining);
            if (data.remaining === 0) { disableMySelects(); }
        }
    })
    .catch(function () {
        if (select)      select.disabled = false;
        if (customInput) customInput.disabled = false;
        showToast('Request failed. Check your connection.', 'danger');
    });
}

function applySelectStyle(select, qty) {
    qty = parseFloat(qty);
    if (qty === 0) {
        select.style.background   = '#fff5f5';
        select.style.color        = '#dc3545';
        select.style.borderColor  = '#dc3545';
    } else if (qty > 1) {
        select.style.background   = '#f0f8ff';
        select.style.color        = '#0d6efd';
        select.style.borderColor  = '#0d6efd';
    } else {
        select.style.background   = '#f0fff4';
        select.style.color        = '#198754';
        select.style.borderColor  = '#198754';
    }
}

function updateHeaderQty(scheduleId, totalQty) {
    var el = document.getElementById('hdr-qty-' + scheduleId);
    if (el) {
        var members = el.textContent.match(/·\s*(\d+)\s*members/);
        var mCount  = members ? members[1] : '';
        el.textContent = totalQty + ' meals' + (mCount ? ' · ' + mCount + ' members' : '');
    }
}

function updateFooterQty(scheduleId, totalQty) {
    var cell = document.getElementById('foot-' + scheduleId);
    if (!cell) return;
    var badge = cell.querySelector('.badge');
    if (badge) badge.textContent = totalQty;
}

function updateRowTotalById(userId) {
    var cell = document.getElementById('row-total-' + userId);
    if (!cell) return;
    var row = cell.closest('tr');
    var total = 0;
    // Sum preset selects (skip "custom" entries — use the input value instead)
    row.querySelectorAll('.meal-qty-select').forEach(function(s) {
        if (s.value !== 'custom') { total += parseFloat(s.value) || 0; }
    });
    row.querySelectorAll('.meal-custom-input:not(.d-none)').forEach(function(i) {
        total += parseFloat(i.value) || 0;
    });
    var badge = cell.querySelector('span');
    if (badge) {
        badge.textContent    = total;
        badge.className      = 'badge rounded-pill bg-' + (total > 0 ? 'success' : 'danger');
        badge.style.fontSize = '13px';
    }
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

function disableMySelects() {
    var myUserId = '{{ Auth::id() }}';
    document.querySelectorAll('.meal-qty-select[data-user="' + myUserId + '"]').forEach(function(s) {
        s.disabled = true;
        s.title    = 'You have used all 3 changes for today. Contact your manager.';
    });
    showToast('You have used all 3 changes for today. Contact your manager.', 'danger');
}

function openEditMealType(id, name, closeTime, isActive, action) {
    var base = '/mess/' + messId + '/meal-types/' + id;
    document.getElementById('editMealTypeForm').action = base;
    document.getElementById('edit-mt-name').value  = name;
    document.getElementById('edit-mt-close').value = closeTime || '';
    document.getElementById('edit-mt-active').value = (action === 'enable' || isActive) ? '1' : '0';
    var modal = new bootstrap.Modal(document.getElementById('editMealTypeModal'));
    modal.show();
}

function closeMeal(scheduleId, mealName) {
    var cost = prompt('Close "' + mealName + '"?\n\nEnter total meal cost in ৳ (leave blank for 0):');
    if (cost === null) return;

    showToast('Closing ' + mealName + '…', 'info');

    fetch(mealCloseBase + '/' + scheduleId + '/close', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ meal_cost: parseFloat(cost) || 0 })
    })
    .then(function (r) { return r.json(); })
    .then(function () {
        showToast(mealName + ' closed successfully.', 'success');
        setTimeout(function () { location.reload(); }, 1500);
    })
    .catch(function () {
        showToast('Failed to close meal.', 'danger');
    });
}
</script>
@endsection
