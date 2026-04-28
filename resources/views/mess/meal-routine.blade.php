<?php $page = "mess-meal-routine" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-calendar-event me-2 text-primary"></i>{{ __('Monthly Meal Routine') }}</h4>
                <h6 class="text-muted">{{ $mess->name }} &mdash; {{ $monthStart->format('F Y') }}</h6>
            </div>
            <div class="page-btn d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="ti ti-printer me-1"></i>Print
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        @if($mealTypes->isEmpty())
        <div class="alert alert-warning">
            <i class="ti ti-alert-triangle me-2"></i>No meal types configured for this mess yet.
            Please add meal types from <a href="{{ route('mess.meals', $mess->id) }}">Meal Attendance</a> first.
        </div>
        @else

        {{-- Meal Type Selector --}}
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="fw-semibold text-muted small">Select Meal:</span>
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($mealTypes as $mt)
                        <a href="{{ route('mess.meal-routine', $mess->id) }}?meal_type={{ urlencode($mt->name) }}&month={{ $monthParam }}"
                           class="btn btn-sm {{ $selectedType === $mt->name ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="ti ti-tools-kitchen-2 me-1"></i>{{ $mt->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Today's Meals for selected type --}}
        @if($todayItems)
        <div class="alert alert-success d-flex align-items-start gap-3 mb-3">
            <i class="ti ti-tools-kitchen-2 fs-3 mt-1 text-success flex-shrink-0"></i>
            <div>
                <div class="fw-bold mb-1">
                    Today's {{ $selectedType }} Menu
                    <span class="fw-normal text-muted small">— {{ $today->format('l') }}, Week {{ $weekNo }}</span>
                </div>
                <div style="white-space:pre-wrap;">{{ $todayItems }}</div>
            </div>
        </div>
        @else
        <div class="alert alert-secondary d-flex align-items-center gap-2 mb-3">
            <i class="ti ti-info-circle"></i>
            <span>No <strong>{{ $selectedType }}</strong> menu set for today ({{ $today->format('l') }}, Week {{ $weekNo }}).</span>
            @if($canEdit)<span class="ms-1 small text-muted">Click a date below to add.</span>@endif
        </div>
        @endif

        {{-- Month Navigation + Calendar --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('mess.meal-routine', $mess->id) }}?meal_type={{ urlencode($selectedType) }}&month={{ $prevMonth }}"
                       class="btn btn-sm btn-outline-secondary px-2">&#8249;</a>
                    <h6 class="mb-0 fw-semibold">
                        <span class="badge bg-primary me-1">{{ $selectedType }}</span>
                        {{ $monthStart->format('F Y') }}
                    </h6>
                    <a href="{{ route('mess.meal-routine', $mess->id) }}?meal_type={{ urlencode($selectedType) }}&month={{ $nextMonth }}"
                       class="btn btn-sm btn-outline-secondary px-2">&#8250;</a>
                    @if($monthParam !== now()->format('Y-m'))
                    <a href="{{ route('mess.meal-routine', $mess->id) }}?meal_type={{ urlencode($selectedType) }}&month={{ now()->format('Y-m') }}"
                       class="btn btn-sm btn-outline-primary">Today</a>
                    @endif
                </div>
                @if($canEdit)
                <span class="badge bg-info text-dark small">Click any date to edit</span>
                @endif
            </div>
            <div class="card-body p-2">
                {{-- Day headers --}}
                <div class="routine-cal-grid mb-1">
                    @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dh)
                    <div class="text-center fw-semibold small text-muted py-1">{{ $dh }}</div>
                    @endforeach
                </div>
                {{-- Calendar days --}}
                <div class="routine-cal-grid">
                    @foreach($calDays as $day)
                    @php
                        $inMonth  = $day->month === $monthStart->month;
                        $isToday  = $day->isSameDay($today);
                        $dow      = (int) $day->format('w');
                        $wn       = \App\Models\MessMealRoutine::weekNoForDate($day);
                        $items    = $grid[$wn][$dow] ?? null;
                        $isWeekend = $dow === 5 || $dow === 6;
                        $mrPalette = [
                            '#fff8e1','#fce4ec','#e8f5e9','#e3f2fd','#f3e5f5',
                            '#e0f7fa','#fff3e0','#e8eaf6','#f1f8e9','#fdf3e7',
                            '#fce8ff','#e0f2f1','#fff9c4','#e1f5fe','#fbe9e7',
                            '#e8f8f5','#fef9e7','#eaf4fb','#fdedec','#f9ebea',
                        ];
                        $mrCellBg = !$inMonth ? '#f8f9fa' : ($isToday ? '#dbeafe' : $mrPalette[$day->day % count($mrPalette)]);
                    @endphp
                    <div class="routine-day-cell {{ $isToday ? 'today' : '' }} {{ !$inMonth ? 'out-of-month' : '' }} {{ $isWeekend && $inMonth ? 'weekend' : '' }} {{ $canEdit && $inMonth ? 'editable' : '' }}"
                        style="background:{{ $mrCellBg }}"
                        @if($canEdit && $inMonth)
                        data-week="{{ $wn }}" data-day="{{ $dow }}" data-items="{{ e($items ?? '') }}"
                        onclick="openEdit(this)"
                        onmouseenter="if(this.classList.contains('editable'))this.style.background='rgba(13,110,253,0.07)'"
                        onmouseleave="this.style.background='{{ $mrCellBg }}'"
                        @endif
                    >
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="day-num {{ $isToday ? 'text-white bg-primary rounded-circle' : ($inMonth ? '' : 'text-muted') }}"
                                  style="{{ $isToday ? 'width:22px;height:22px;display:inline-flex;align-items:center;justify-content:center;font-size:12px;' : '' }}">
                                {{ $day->format('j') }}
                            </span>
                            @if($inMonth)
                            <span class="badge" style="font-size:9px;background:#e9ecef;color:#555;">W{{ $wn }}</span>
                            @endif
                        </div>
                        @if($inMonth)
                            @if($items)
                            <div class="routine-items">{{ $items }}</div>
                            @elseif($canEdit)
                            <div class="add-hint"><i class="ti ti-plus" style="font-size:10px;"></i></div>
                            @endif
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @if($canEdit)
            <div class="card-footer text-muted small">
                <i class="ti ti-pencil me-1"></i>Click any date to add or edit items for <strong>{{ $selectedType }}</strong>.
            </div>
            @endif
        </div>

        @endif {{-- end mealTypes not empty --}}
    </div>
</div>

@if($canEdit)
<div class="modal fade" id="editRoutineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editRoutineTitle"><i class="ti ti-pencil me-2"></i>Edit Meal Items</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-semibold">
                    Items for <span class="badge bg-primary" id="editMealTypeLabel"></span> <span class="text-danger">*</span>
                </label>
                <textarea id="editItems" class="form-control" rows="5"
                    placeholder="e.g. Plain Rice, Chicken Curry (150g)&#10;Vegetables, Dal, Salad"></textarea>
                <div class="form-text">Each item on a new line or separated by commas.</div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-outline-danger btn-sm" id="clearRoutineBtn">
                    <i class="ti ti-trash me-1"></i>Clear
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="saveRoutineBtn">
                        <i class="ti ti-check me-1"></i>Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<style>
.routine-cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
}
.routine-day-cell {
    min-height: 80px;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 6px 7px;
    font-size: 12px;
    position: relative;
}
.routine-day-cell.out-of-month {
    border-color: #f0f0f0;
}
.routine-day-cell.today {
    border-color: #0d6efd;
    border-width: 2px;
}
.routine-day-cell.editable {
    cursor: pointer;
    transition: background .15s;
}
.day-num {
    font-size: 12px;
    font-weight: 600;
    line-height: 1;
}
.routine-items {
    font-size: 11px;
    color: #333;
    white-space: pre-wrap;
    line-height: 1.4;
    margin-top: 2px;
}
.add-hint {
    color: #bbb;
    margin-top: 4px;
}
@media (max-width: 576px) {
    .routine-day-cell { min-height: 60px; padding: 4px 5px; }
    .routine-items { font-size: 10px; }
}
@media print {
    .page-header, .alert, .card-footer, .btn, nav, aside, .sidebar { display:none!important; }
    .routine-day-cell { min-height: 60px; font-size: 10px; }
    body { margin: 0; }
}
</style>

@if($canEdit)
<script>
(function(){
    const upsertUrl  = '{{ route("mess.meal-routine.upsert", $mess->id) }}';
    const destroyUrl = '{{ route("mess.meal-routine.destroy", $mess->id) }}';
    const csrf       = '{{ csrf_token() }}';
    const mealType   = @json($selectedType);
    const dayNames   = @json(\App\Models\MessMealRoutine::$dayNames);

    let modal, editWeek, editDay, editItemsEl;

    document.addEventListener('DOMContentLoaded', function () {
        modal       = new bootstrap.Modal(document.getElementById('editRoutineModal'));
        editWeek    = document.createElement('input'); editWeek.type = 'hidden';
        editDay     = document.createElement('input'); editDay.type  = 'hidden';
        editItemsEl = document.getElementById('editItems');
        document.getElementById('editMealTypeLabel').textContent = mealType;
        document.getElementById('saveRoutineBtn').addEventListener('click', saveRoutine);
        document.getElementById('clearRoutineBtn').addEventListener('click', clearRoutine);
    });

    window.openEdit = function (cell) {
        editWeek.value    = cell.dataset.week;
        editDay.value     = cell.dataset.day;
        editItemsEl.value = cell.dataset.items || '';
        editItemsEl.classList.remove('is-invalid');
        document.getElementById('editRoutineTitle').innerHTML =
            '<i class="ti ti-pencil me-2"></i>' + cell.closest('.routine-day-cell').querySelector('.day-num').textContent.trim()
            + ' ' + '{{ $monthStart->format("M Y") }}' + ' &mdash; ' + dayNames[editDay.value];
        modal.show();
        setTimeout(() => editItemsEl.focus(), 300);
    };

    function saveRoutine() {
        const items = editItemsEl.value.trim();
        if (!items) { editItemsEl.classList.add('is-invalid'); return; }
        editItemsEl.classList.remove('is-invalid');
        fetch(upsertUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ meal_type: mealType, week_no: editWeek.value, day_of_week: editDay.value, items })
        }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
    }

    function clearRoutine() {
        if (!confirm('Clear items for this date?')) return;
        fetch(destroyUrl, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ meal_type: mealType, week_no: editWeek.value, day_of_week: editDay.value })
        }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
    }
})();
</script>
@endif

@endsection
