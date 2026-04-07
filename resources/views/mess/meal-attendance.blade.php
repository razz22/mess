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
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="ti ti-users text-primary"></i> Member
                                    </div>
                                </th>
                                @foreach($mealTypes as $mt)
                                @php
                                    $sch = $schedules[$mt->name] ?? null;
                                    $expired = !$isPast && $mt->isExpired();
                                    $closed  = $sch && $sch->status === 'closed';
                                @endphp
                                <th class="text-center {{ $closed ? 'bg-secondary bg-opacity-10' : 'bg-light' }}" style="min-width:120px;">
                                    <div class="fw-semibold">{{ $mt->name }}</div>
                                    @if($closed)
                                        <div class="badge bg-secondary" style="font-size:10px">Closed</div>
                                    @elseif($expired)
                                        <div class="badge bg-warning text-dark" style="font-size:10px">Expired</div>
                                    @else
                                        @if($sch)
                                        @php
                                            $onCount = $allAttendances->filter(fn($g,$k) => str_starts_with($k, $sch->id.'_'))
                                                ->flatten()->where('status','on')->count();
                                        @endphp
                                        <div class="text-success" style="font-size:11px">{{ $onCount }}/{{ $members->count() }} on</div>
                                        @endif
                                    @endif
                                </th>
                                @endforeach
                                <th class="bg-light text-center" style="min-width:80px;">Total Meals</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $myId = Auth::id(); @endphp
                            @foreach($members as $mem)
                            @php
                                $isMe = $mem->user_id === $myId;
                                $canEdit = $isManager || $isMe;
                                $totalOn = 0;
                            @endphp
                            <tr class="{{ $isMe ? 'table-primary bg-opacity-25' : '' }}">
                                {{-- Member name (sticky) --}}
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

                                {{-- Meal toggles --}}
                                @foreach($mealTypes as $mt)
                                @php
                                    $sch    = $schedules[$mt->name] ?? null;
                                    $key    = $sch ? ($sch->id . '_' . $mem->user_id) : null;
                                    $att    = $key ? ($allAttendances[$key]->first() ?? null) : null;
                                    $status = $att ? $att->status : 'on'; // default ON
                                    $locked = $isPast || !$sch || $sch->status === 'closed'
                                              || (!$isManager && $mt->isExpired());
                                    $canToggle = $canEdit && !$locked;
                                    if ($status === 'on') $totalOn++;
                                @endphp
                                <td class="text-center align-middle p-2 {{ $locked ? 'bg-light' : '' }}">
                                    @if($sch)
                                    <button
                                        class="meal-toggle btn btn-sm rounded-pill px-3 fw-semibold
                                               {{ $status === 'on' ? 'btn-success' : 'btn-danger' }}
                                               {{ !$canToggle ? 'disabled opacity-75' : '' }}"
                                        style="font-size:12px;min-width:60px;cursor:{{ $canToggle ? 'pointer' : 'default' }};"
                                        data-schedule="{{ $sch->id }}"
                                        data-user="{{ $mem->user_id }}"
                                        data-status="{{ $status }}"
                                        @if($canToggle) onclick="toggleMeal(this)" @endif
                                        title="{{ $locked ? ($isPast ? 'Past date' : ($sch->status==='closed' ? 'Meal closed' : 'Time expired')) : '' }}"
                                    >
                                        @if($status === 'on')
                                            <i class="ti ti-check me-1"></i>ON
                                        @else
                                            <i class="ti ti-x me-1"></i>OFF
                                        @endif
                                    </button>
                                    @else
                                    <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                @endforeach

                                {{-- Total meals column --}}
                                <td class="text-center align-middle fw-bold">
                                    <span class="badge bg-{{ $totalOn > 0 ? 'success' : 'danger' }} rounded-pill">{{ $totalOn }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        {{-- Summary row --}}
                        <tfoot>
                            <tr class="bg-light fw-semibold">
                                <td class="bg-light" style="position:sticky;left:0;z-index:1;">Total ON</td>
                                @foreach($mealTypes as $mt)
                                @php
                                    $sch = $schedules[$mt->name] ?? null;
                                    $cnt = 0;
                                    if ($sch) {
                                        $cnt = $allAttendances->filter(fn($g,$k) => str_starts_with($k, $sch->id.'_'))
                                            ->flatten()->where('status','on')->count();
                                    }
                                @endphp
                                <td class="text-center" id="count-{{ $sch?->id ?? 'none' }}">
                                    <span class="badge bg-success rounded-pill">{{ $cnt }}</span>
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
        @if($isManager && $mealTypes->isNotEmpty())
        <div class="card mt-3">
            <div class="card-header py-2">
                <h6 class="mb-0 fw-semibold"><i class="ti ti-tools-kitchen-2 me-2"></i>Manage Meal Types</h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($mess->mealTypes()->get() as $mt)
                    <div class="d-flex align-items-center gap-1 border rounded px-3 py-2">
                        <span class="fw-semibold">{{ $mt->name }}</span>
                        @if($mt->close_time)
                        <span class="text-muted small ms-1">· {{ \Carbon\Carbon::parse($mt->close_time)->format('g:i A') }}</span>
                        @endif
                        @if(!in_array($mt->name, ['Breakfast','Lunch','Dinner']))
                        <form action="{{ route('mess.meal-types.destroy', [$mess->id, $mt->id]) }}" method="POST" class="ms-1">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-1" onclick="return confirm('Remove {{ $mt->name }}?')">
                                <i class="ti ti-trash" style="font-size:11px"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                    @endforeach
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
var messId      = {{ $mess->id }};
var csrf        = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var isManager   = {{ $isManager ? 'true' : 'false' }};
var myChanges   = {{ $myChangesToday }};

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

// Disable my toggles on load if limit already reached
if (!isManager && myChanges >= 3) {
    window.addEventListener('DOMContentLoaded', function () { disableMyToggles(); });
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

// ── Meal toggle ──────────────────────────────────────────────
function toggleMeal(btn) {
    var scheduleId = btn.getAttribute('data-schedule');
    var userId     = btn.getAttribute('data-user');
    var curStatus  = btn.getAttribute('data-status');
    var newStatus  = curStatus === 'on' ? 'off' : 'on';

    btn.setAttribute('disabled', 'disabled');
    btn.style.opacity = '0.6';

    fetch('/mess/' + messId + '/meals/attendance', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ schedule_id: scheduleId, user_id: userId, status: newStatus })
    })
    .then(function (response) {
        return response.text().then(function (text) {
            return { ok: response.ok, status: response.status, text: text };
        });
    })
    .then(function (result) {
        btn.removeAttribute('disabled');
        btn.style.opacity = '';

        var data = {};
        try { data = JSON.parse(result.text); } catch (e) {}

        if (!result.ok) {
            showToast(data.error || data.message || 'Server error ' + result.status, 'danger');
            // If limit reached, disable all MY toggles
            if (data.limit_reached) {
                disableMyToggles();
            }
            return;
        }

        btn.setAttribute('data-status', newStatus);
        btn.classList.remove('btn-success', 'btn-danger');

        if (newStatus === 'off') {
            btn.classList.add('btn-danger');
            btn.innerHTML = '<i class="ti ti-x me-1"></i>OFF';
            showToast('Meal turned OFF', 'warning');
        } else {
            btn.classList.add('btn-success');
            btn.innerHTML = '<i class="ti ti-check me-1"></i>ON';
            showToast('Meal turned ON', 'success');
        }

        updateCount(scheduleId, data.on);
        updateRowTotal(btn);

        // Update remaining changes badge
        if (!isManager && data.remaining !== null && data.remaining !== undefined) {
            myChanges = 3 - data.remaining;
            updateRemainingBadge(data.remaining);
            if (data.remaining === 0) {
                disableMyToggles();
            }
        }
    })
    .catch(function () {
        btn.removeAttribute('disabled');
        btn.style.opacity = '';
        showToast('Request failed. Check your connection.', 'danger');
    });
}

function updateCount(scheduleId, onCount) {
    var cell = document.getElementById('count-' + scheduleId);
    if (cell) cell.innerHTML = '<span class="badge bg-success rounded-pill">' + onCount + '</span>';
}

function updateRowTotal(btn) {
    var row     = btn.closest('tr');
    var toggles = row.querySelectorAll('.meal-toggle');
    var count   = 0;
    toggles.forEach(function(t) { if (t.getAttribute('data-status') === 'on') count++; });
    var totalCell = row.querySelector('td:last-child span');
    if (totalCell) {
        totalCell.textContent = count;
        totalCell.className   = 'badge rounded-pill bg-' + (count > 0 ? 'success' : 'danger');
    }
}

function updateRemainingBadge(remaining) {
    var el = document.getElementById('changes-remaining');
    if (!el) return;
    el.textContent = remaining + '/3 changes left today';
    var wrapper = el.closest('.d-flex.align-items-center');
    if (wrapper) {
        if (remaining === 0) {
            wrapper.classList.remove('border-secondary', 'bg-light');
            wrapper.classList.add('border-danger', 'bg-danger-subtle');
            el.classList.remove('text-muted');
            el.classList.add('text-danger');
            wrapper.querySelector('i').classList.remove('text-muted');
            wrapper.querySelector('i').classList.add('text-danger');
        }
    }
}

function disableMyToggles() {
    var myUserId = '{{ Auth::id() }}';
    document.querySelectorAll('.meal-toggle[data-user="' + myUserId + '"]').forEach(function(btn) {
        btn.setAttribute('disabled', 'disabled');
        btn.style.opacity = '0.5';
        btn.style.cursor  = 'not-allowed';
        btn.title = 'You have used all 3 changes for today. Contact your manager.';
    });
    showToast('You have used all 3 changes for today. Contact your manager.', 'danger');
}

function closeMeal(scheduleId, mealName) {
    var cost = prompt('Close "' + mealName + '"?\n\nEnter total meal cost in ৳ (leave blank for 0):');
    if (cost === null) return;

    showToast('Closing ' + mealName + '…', 'info');

    fetch('/mess/' + messId + '/meals/' + scheduleId + '/close', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
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
