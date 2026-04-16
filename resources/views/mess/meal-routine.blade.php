<?php $page = "mess-meal-routine" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-calendar-event me-2 text-primary"></i>Monthly Meal Routine</h4>
                <h6 class="text-muted">{{ $mess->name }} &mdash; Weekly Menu Chart</h6>
            </div>
            <div class="page-btn">
                <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="ti ti-printer me-1"></i>Print
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        @php
            $dayNames   = \App\Models\MessMealRoutine::$dayNames;
            $weekLabels = \App\Models\MessMealRoutine::$weekLabels;
            $displayDays = [0,1,2,3,4,5,6];
        @endphp

        @if($mealTypes->isEmpty())
        <div class="alert alert-warning">
            <i class="ti ti-alert-triangle me-2"></i>No meal types configured for this mess yet.
            @if($isManager)
            Please add meal types from <a href="{{ route('mess.meals', $mess->id) }}">Meal Attendance</a> first.
            @endif
        </div>
        @else

        {{-- Meal Type Selector --}}
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="fw-semibold text-muted small">Select Meal:</span>
                    <div class="d-flex gap-2 flex-wrap" id="mealTypeTabs">
                        @foreach($mealTypes as $mt)
                        <a href="{{ route('mess.meal-routine', $mess->id) }}?meal_type={{ urlencode($mt->name) }}"
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
                    <span class="fw-normal text-muted small">— {{ $dayNames[$dayOfWeek] }}, {{ $weekLabels[$weekNo] }}</span>
                </div>
                <div style="white-space:pre-wrap;">{{ $todayItems }}</div>
            </div>
        </div>
        @else
        <div class="alert alert-secondary d-flex align-items-center gap-2 mb-3">
            <i class="ti ti-info-circle"></i>
            <span>No <strong>{{ $selectedType }}</strong> menu set for today ({{ $dayNames[$dayOfWeek] }}, {{ $weekLabels[$weekNo] }}).</span>
            @if($isManager)
            <span class="ms-1 small text-muted">Click a cell below to add.</span>
            @endif
        </div>
        @endif

        {{-- Routine Grid --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold">
                    <i class="ti ti-table me-2"></i>
                    <span class="badge bg-primary me-1">{{ $selectedType }}</span>
                    Weekly Menu Chart
                </h6>
                @if($isManager)
                <span class="badge bg-info text-dark">Click any cell to edit</span>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 routine-table" style="min-width:700px;">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" style="width:110px;">Day</th>
                                @foreach($weekLabels as $wn => $wl)
                                <th class="text-center">{{ $wl }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($displayDays as $dow)
                            @php $isToday = ($dow === $dayOfWeek); @endphp
                            <tr class="{{ $isToday ? 'table-success' : '' }}">
                                <td class="fw-semibold text-center align-middle {{ $isToday ? 'bg-success bg-opacity-25' : 'bg-light' }}" style="font-size:13px;">
                                    {{ $dayNames[$dow] }}
                                    @if($isToday)<br><span class="badge bg-success" style="font-size:10px;">Today</span>@endif
                                </td>
                                @foreach($weekLabels as $wn => $wl)
                                @php
                                    $items = $grid[$wn][$dow] ?? null;
                                    $isHighlight = $isToday && ($wn === $weekNo);
                                @endphp
                                <td class="align-middle p-0 {{ $isHighlight ? 'bg-success bg-opacity-10' : '' }}">
                                    @if($isManager)
                                    <div class="routine-cell"
                                         onclick="openEdit({{ $wn }}, {{ $dow }}, {{ json_encode($items) }})"
                                         style="cursor:pointer;min-height:64px;padding:10px 12px;transition:background .15s;"
                                         onmouseenter="this.style.background='rgba(13,110,253,0.07)'"
                                         onmouseleave="this.style.background=''">
                                        @if($items)
                                        <div class="small" style="white-space:pre-wrap;">{{ $items }}</div>
                                        @else
                                        <div class="text-muted small d-flex align-items-center gap-1 opacity-40">
                                            <i class="ti ti-plus"></i><span>Add</span>
                                        </div>
                                        @endif
                                    </div>
                                    @else
                                    <div style="min-height:64px;padding:10px 12px;">
                                        @if($items)
                                        <div class="small" style="white-space:pre-wrap;">{{ $items }}</div>
                                        @else
                                        <span class="text-muted small">—</span>
                                        @endif
                                    </div>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if($isManager)
            <div class="card-footer text-muted small">
                <i class="ti ti-pencil me-1"></i>Click any cell to add or edit items for <strong>{{ $selectedType }}</strong>.
            </div>
            @endif
        </div>

        @endif {{-- end mealTypes not empty --}}
    </div>
</div>

@if($isManager)
<div class="modal fade" id="editRoutineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editRoutineTitle"><i class="ti ti-pencil me-2"></i>Edit Meal Items</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label fw-semibold">Items for <span class="badge bg-primary" id="editMealTypeLabel"></span> <span class="text-danger">*</span></label>
                    <textarea id="editItems" class="form-control" rows="5"
                        placeholder="e.g. Plain Rice, Chicken Curry (150g)&#10;Vegetables, Dal, Salad"></textarea>
                    <div class="form-text">Each item on a new line or separated by commas.</div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-outline-danger btn-sm" id="clearRoutineBtn">
                    <i class="ti ti-trash me-1"></i>Clear Cell
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
@media print {
    .page-header, .alert, .card-footer, .btn, nav, aside, .sidebar,
    #mealTypeTabs, .card:first-of-type { display:none!important; }
    .routine-table { font-size:12px; }
    .routine-cell { cursor:default!important; }
    body { margin:0; }
}
</style>

@if($isManager)
<script>
(function(){
    const upsertUrl  = '{{ route("mess.meal-routine.upsert", $mess->id) }}';
    const destroyUrl = '{{ route("mess.meal-routine.destroy", $mess->id) }}';
    const csrf       = '{{ csrf_token() }}';
    const dayNames   = @json(\App\Models\MessMealRoutine::$dayNames);
    const weekLabels = @json(\App\Models\MessMealRoutine::$weekLabels);
    const mealType   = @json($selectedType);

    let modal, editWeek, editDay, editItems;

    document.addEventListener('DOMContentLoaded', function() {
        modal     = new bootstrap.Modal(document.getElementById('editRoutineModal'));
        editWeek  = document.getElementById('editWeek_hidden') || (function(){
            const i = document.createElement('input'); i.type='hidden'; i.id='editWeek_hidden';
            document.body.appendChild(i); return i;
        })();
        editDay   = document.getElementById('editDay_hidden') || (function(){
            const i = document.createElement('input'); i.type='hidden'; i.id='editDay_hidden';
            document.body.appendChild(i); return i;
        })();
        editItems = document.getElementById('editItems');
        document.getElementById('editMealTypeLabel').textContent = mealType;
        document.getElementById('saveRoutineBtn').addEventListener('click', saveRoutine);
        document.getElementById('clearRoutineBtn').addEventListener('click', clearRoutine);
    });

    window.openEdit = function(week, day, items) {
        editWeek.value  = week;
        editDay.value   = day;
        editItems.value = items || '';
        editItems.classList.remove('is-invalid');
        document.getElementById('editRoutineTitle').innerHTML =
            '<i class="ti ti-pencil me-2"></i>' + dayNames[day] + ' &mdash; ' + weekLabels[week];
        modal.show();
        setTimeout(() => editItems.focus(), 300);
    };

    function saveRoutine() {
        const items = editItems.value.trim();
        if (!items) { editItems.classList.add('is-invalid'); return; }
        editItems.classList.remove('is-invalid');

        fetch(upsertUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ meal_type: mealType, week_no: editWeek.value, day_of_week: editDay.value, items })
        })
        .then(r => r.json())
        .then(d => { if (d.success) location.reload(); });
    }

    function clearRoutine() {
        if (!confirm('Clear items for this cell?')) return;
        fetch(destroyUrl, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ meal_type: mealType, week_no: editWeek.value, day_of_week: editDay.value })
        })
        .then(r => r.json())
        .then(d => { if (d.success) location.reload(); });
    }
})();
</script>
@endif

@endsection
