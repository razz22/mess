<?php $page = "mess-meal-items" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        {{-- Header --}}
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">Meal Plan — {{ $mess->name }}</h4>
                <h6 class="text-muted">{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
                    @if($date === $today) <span class="badge bg-success ms-2">Today</span> @endif
                </h6>
            </div>
            <div class="page-btn d-flex gap-2 align-items-center">
                <a href="{{ route('mess.meals', $mess->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Attendance
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
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
                        <input type="text" id="mealItemDatePicker" class="form-control fw-semibold"
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
                </div>
            </div>
        </div>

        {{-- Meal type columns --}}
        @if($mealTypes->isEmpty())
        <div class="alert alert-info">No meal types configured for this mess.</div>
        @else
        <div class="row g-3">
            @foreach($mealTypes as $mt)
            @php
                $mtItems = $items[$mt->name] ?? collect();
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    {{-- Meal type header --}}
                    <div class="card-header d-flex align-items-center justify-content-between py-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold">{{ $mt->name }}</span>
                            @if($mt->close_time)
                            <span class="text-muted small">· closes {{ \Carbon\Carbon::parse($mt->close_time)->format('g:i A') }}</span>
                            @endif
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $mtItems->count() }}</span>
                    </div>

                    {{-- Items list --}}
                    <div class="card-body p-0">
                        @if($mtItems->isEmpty())
                        <div class="text-center text-muted py-4" style="font-size:12px;" id="empty-{{ $mt->name }}">
                            <i class="ti ti-salad fs-4 d-block mb-1 opacity-50"></i>
                            No items planned
                        </div>
                        @else
                        <div id="empty-{{ $mt->name }}" class="d-none"></div>
                        @endif

                        <ul class="list-group list-group-flush" id="list-{{ $mt->name }}">
                            @foreach($mtItems as $item)
                            <li class="list-group-item d-flex align-items-center justify-content-between gap-2 py-2 px-3" id="item-{{ $item->id }}">
                                <div class="d-flex align-items-center gap-2 flex-grow-1">
                                    <i class="ti ti-circle-filled text-primary" style="font-size:7px;flex-shrink:0;"></i>
                                    <span class="small fw-semibold">{{ $item->name }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                    <span class="text-muted" style="font-size:10px;">{{ $item->createdBy->name }}</span>
                                    @if($isManager || $item->created_by === Auth::id())
                                    <button class="btn btn-xs btn-outline-danger py-0 px-1 ms-1"
                                        onclick="deleteItem({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                        title="Remove">
                                        <i class="ti ti-x" style="font-size:11px;"></i>
                                    </button>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Add item form --}}
                    <div class="card-footer p-2 bg-white border-top">
                        <form class="add-item-form d-flex gap-2" data-meal="{{ $mt->name }}">
                            @csrf
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Add item… (e.g. Rice, Fish curry)"
                                maxlength="255" autocomplete="off">
                            <button type="submit" class="btn btn-sm btn-primary flex-shrink-0">
                                <i class="ti ti-plus"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</div>

<style>
.list-group-item:hover { background: #f8f9fa; }
.btn-xs { padding: 0.1rem 0.3rem; font-size: 0.7rem; }
</style>

<script>
var messId  = {{ $mess->id }};
var csrf    = document.querySelector('meta[name="csrf-token"]').content;
var pageDate = '{{ $date }}';

// ── Flatpickr date navigation ────────────────────────────────
window.addEventListener('load', function () {
    if (typeof flatpickr !== 'undefined') {
        flatpickr('#mealItemDatePicker', {
            dateFormat: 'd M Y',
            defaultDate: pageDate,
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

// ── Add item (AJAX inline) ───────────────────────────────────
document.querySelectorAll('.add-item-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var mealType = form.getAttribute('data-meal');
        var input    = form.querySelector('input[type="text"]');
        var name     = input.value.trim();
        if (!name) return;

        var btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;

        fetch('/mess/' + messId + '/meal-items', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ name: name, meal_type: mealType, date: pageDate })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            btn.disabled = false;
            if (!data.success) {
                showItemToast(data.message || 'Error adding item.', 'danger');
                return;
            }
            input.value = '';
            appendItem(mealType, data.item);
            showItemToast('"' + data.item.name + '" added to ' + mealType, 'success');
        })
        .catch(function () {
            btn.disabled = false;
            showItemToast('Request failed.', 'danger');
        });
    });
});

function appendItem(mealType, item) {
    var list    = document.getElementById('list-' + mealType);
    var empty   = document.getElementById('empty-' + mealType);
    if (!list) return;

    // Hide empty placeholder
    if (empty) empty.classList.add('d-none');

    var canDelete = {{ $isManager ? 'true' : 'false' }} || item.is_mine;
    var li = document.createElement('li');
    li.id = 'item-' + item.id;
    li.className = 'list-group-item d-flex align-items-center justify-content-between gap-2 py-2 px-3';
    li.innerHTML =
        '<div class="d-flex align-items-center gap-2 flex-grow-1">' +
            '<i class="ti ti-circle-filled text-primary" style="font-size:7px;flex-shrink:0;"></i>' +
            '<span class="small fw-semibold">' + escHtml(item.name) + '</span>' +
        '</div>' +
        '<div class="d-flex align-items-center gap-1 flex-shrink-0">' +
            '<span class="text-muted" style="font-size:10px;">' + escHtml(item.created_by_name) + '</span>' +
            (canDelete
                ? '<button class="btn btn-xs btn-outline-danger py-0 px-1 ms-1" onclick="deleteItem(' + item.id + ',\'' + escHtml(item.name).replace(/'/g,"\\'") + '\')" title="Remove"><i class="ti ti-x" style="font-size:11px;"></i></button>'
                : '') +
        '</div>';
    list.appendChild(li);

    // Update badge count
    var header = list.closest('.card').querySelector('.badge.bg-primary');
    if (header) header.textContent = list.children.length;
}

function deleteItem(itemId, name) {
    if (!confirm('Remove "' + name + '"?')) return;

    fetch('/mess/' + messId + '/meal-items/' + itemId, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (!data.success) { showItemToast('Could not remove item.', 'danger'); return; }
        var li = document.getElementById('item-' + itemId);
        if (li) {
            var list = li.closest('ul');
            li.remove();
            // Update badge
            if (list) {
                var header = list.closest('.card').querySelector('.badge.bg-primary');
                if (header) header.textContent = list.children.length;
                // Show empty placeholder if no items left
                if (list.children.length === 0) {
                    var mealType = list.id.replace('list-', '');
                    var empty = document.getElementById('empty-' + mealType);
                    if (empty) empty.classList.remove('d-none');
                }
            }
        }
        showItemToast('"' + name + '" removed.', 'warning');
    })
    .catch(function () { showItemToast('Request failed.', 'danger'); });
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Simple toast (reuse pattern from meal-attendance) ────────
function showItemToast(message, type) {
    var colors = { success:'#198754', danger:'#dc3545', warning:'#ff9f43', info:'#0dcaf0' };
    var icons  = { success:'ti-circle-check', danger:'ti-circle-x', warning:'ti-alert-triangle', info:'ti-info-circle' };
    var id = 'toast-' + Date.now();
    var html =
        '<div id="' + id + '" class="toast align-items-center border-0 mb-2 show" role="alert" ' +
        'style="background:' + (colors[type]||colors.info) + ';color:#fff;min-width:220px;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,.2)">' +
        '<div class="d-flex align-items-center px-3 py-2 gap-2">' +
        '<i class="ti ' + (icons[type]||icons.info) + ' fs-5"></i>' +
        '<div class="flex-grow-1 fw-semibold" style="font-size:14px">' + message + '</div>' +
        '<button type="button" onclick="this.closest(\'.toast\').remove()" style="background:none;border:none;color:#fff;opacity:.8;cursor:pointer;font-size:18px;line-height:1">&times;</button>' +
        '</div>' +
        '<div style="height:3px;background:rgba(255,255,255,.4);border-radius:0 0 10px 10px;">' +
        '<div style="height:100%;background:rgba(255,255,255,.7);width:100%;animation:toastProgress 3s linear forwards;"></div>' +
        '</div></div>';
    var c = document.getElementById('item-toast-container');
    if (c) { c.insertAdjacentHTML('beforeend', html); }
    setTimeout(function () {
        var el = document.getElementById(id);
        if (el) { el.style.opacity='0'; el.style.transition='opacity .4s'; setTimeout(function(){ el.remove(); }, 400); }
    }, 3000);
}
</script>

<div id="item-toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:99999;"></div>
<style>@keyframes toastProgress { from { width:100%; } to { width:0%; } }</style>
@endsection
