<?php $page = "mess-notices" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        {{-- Page Header --}}
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold mb-0"><i class="ti ti-speakerphone me-2 text-primary"></i>{{ __('Notices') }}</h4>
                <p class="text-muted small mb-0 mt-1">{{ $mess->name }} &middot; {{ $totalCount }} {{ __('total notices') }}</p>
            </div>
            @if($canManage)
            <div class="page-btn">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoticeModal">
                    <i class="ti ti-circle-plus me-1"></i>{{ __('Add Notice') }}
                </button>
            </div>
            @endif
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
            <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Stats bar (manager only) --}}
        @if($canManage)
        <div class="row g-3 mb-4">
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-4 text-dark">{{ $totalCount }}</div>
                    <div class="text-muted small">{{ __('Total') }}</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-4 text-success">{{ $publishedCount }}</div>
                    <div class="text-muted small">{{ __('Published') }}</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-4 text-warning">{{ $draftCount }}</div>
                    <div class="text-muted small">{{ __('Drafts') }}</div>
                </div>
            </div>
        </div>

        {{-- Filter tabs --}}
        <ul class="nav nav-tabs nav-tabs-bordered mb-4" id="noticeTabs">
            <li class="nav-item">
                <a class="nav-link {{ request('filter','all') === 'all' ? 'active' : '' }} d-flex align-items-center gap-2"
                   href="{{ request()->fullUrlWithQuery(['filter' => 'all', 'page' => 1]) }}">
                    <i class="ti ti-list"></i>{{ __('All') }}
                    <span class="badge bg-secondary rounded-pill">{{ $totalCount }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') === 'published' ? 'active' : '' }} d-flex align-items-center gap-2"
                   href="{{ request()->fullUrlWithQuery(['filter' => 'published', 'page' => 1]) }}">
                    <i class="ti ti-circle-check text-success"></i>{{ __('Published') }}
                    <span class="badge bg-success rounded-pill">{{ $publishedCount }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') === 'draft' ? 'active' : '' }} d-flex align-items-center gap-2"
                   href="{{ request()->fullUrlWithQuery(['filter' => 'draft', 'page' => 1]) }}">
                    <i class="ti ti-pencil text-warning"></i>{{ __('Drafts') }}
                    <span class="badge bg-warning rounded-pill">{{ $draftCount }}</span>
                </a>
            </li>
        </ul>
        @endif

        @include('mess.partials.notice-list', ['list' => $notices])

        {{-- Pagination --}}
        @if($notices->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $notices->links('vendor.pagination.bootstrap-5') }}
        </div>
        @endif

    </div>
</div>

{{-- ====== ADD NOTICE MODAL ====== --}}
@if($canManage)
<div class="modal fade" id="addNoticeModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold mb-0"><i class="ti ti-speakerphone me-2 text-primary"></i>{{ __('Add Notice') }}</h5>
                    <p class="text-muted small mb-0 mt-1">{{ __('Publishing will instantly notify all mess members.') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addNoticeForm" action="{{ route('mess.notices.store', $mess->id) }}" method="POST">
                @csrf
                <div class="modal-body px-4 pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg" required
                               placeholder="{{ __('Notice title...') }}" maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Body') }} <span class="text-danger">*</span></label>
                        <div id="addNoticeEditor" style="min-height:180px;"></div>
                        <input type="hidden" name="body" id="addNoticeBody">
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">{{ __('Status') }}</label>
                        <div class="d-flex gap-3 flex-wrap">
                            <label class="status-option d-flex align-items-center gap-2 p-3 rounded border cursor-pointer" style="flex:1;min-width:140px;">
                                <input type="radio" name="status" value="draft" checked class="form-check-input mt-0">
                                <span>
                                    <span class="badge bg-warning mb-1 d-block" style="width:fit-content;">{{ __('Draft') }}</span>
                                    <small class="text-muted">{{ __('Save for later') }}</small>
                                </span>
                            </label>
                            <label class="status-option d-flex align-items-center gap-2 p-3 rounded border cursor-pointer" style="flex:1;min-width:140px;">
                                <input type="radio" name="status" id="statusPublish" value="published" class="form-check-input mt-0">
                                <span>
                                    <span class="badge bg-success mb-1 d-block" style="width:fit-content;">{{ __('Publish Now') }}</span>
                                    <small class="text-muted">{{ __('Notify all members') }}</small>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-device-floppy me-1"></i>{{ __('Save Notice') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ====== EDIT NOTICE MODAL ====== --}}
<div class="modal fade" id="editNoticeModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold mb-0"><i class="ti ti-edit me-2 text-warning"></i>{{ __('Edit Notice') }}</h5>
                    <p class="text-muted small mb-0 mt-1">{{ __('Changes to a published notice will not re-notify members.') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editNoticeForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body px-4 pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="editNoticeTitle" class="form-control form-control-lg" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Body') }} <span class="text-danger">*</span></label>
                        <div id="editNoticeEditor" style="min-height:180px;"></div>
                        <input type="hidden" name="body" id="editNoticeBody">
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">{{ __('Status') }}</label>
                        <div class="d-flex gap-3 flex-wrap">
                            <label class="status-option d-flex align-items-center gap-2 p-3 rounded border cursor-pointer" style="flex:1;min-width:140px;">
                                <input type="radio" name="status" id="editStatusDraft" value="draft" class="form-check-input mt-0">
                                <span>
                                    <span class="badge bg-warning mb-1 d-block" style="width:fit-content;">{{ __('Draft') }}</span>
                                    <small class="text-muted">{{ __('Save for later') }}</small>
                                </span>
                            </label>
                            <label class="status-option d-flex align-items-center gap-2 p-3 rounded border cursor-pointer" style="flex:1;min-width:140px;">
                                <input type="radio" name="status" id="editStatusPublish" value="published" class="form-check-input mt-0">
                                <span>
                                    <span class="badge bg-success mb-1 d-block" style="width:fit-content;">{{ __('Published') }}</span>
                                    <small class="text-muted">{{ __('Visible to members') }}</small>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-warning px-4">
                        <i class="ti ti-device-floppy me-1"></i>{{ __('Update Notice') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ====== DELETE MODAL ====== --}}
<div class="modal fade" id="deleteNoticeModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle" style="width:56px;height:56px;">
                        <i class="ti ti-trash text-danger" style="font-size:24px;"></i>
                    </span>
                </div>
                <h6 class="fw-bold mb-1">{{ __('Delete Notice') }}</h6>
                <p class="text-muted small mb-3"><strong id="deleteNoticeName"></strong><br>{{ __('This cannot be undone.') }}</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <form id="deleteNoticeForm" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quill CDN --}}
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
const toolbarOptions = [
    ['bold','italic','underline'],
    [{ 'color': [] }],
    [{ 'align': [] }],
    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
    ['link'],['clean']
];
const addQuill  = new Quill('#addNoticeEditor',  { theme:'snow', placeholder:'{{ __("Write the notice content...") }}', modules:{ toolbar: toolbarOptions } });
const editQuill = new Quill('#editNoticeEditor', { theme:'snow', placeholder:'{{ __("Write the notice content...") }}', modules:{ toolbar: toolbarOptions } });

document.getElementById('addNoticeForm').addEventListener('submit', function() {
    const h = addQuill.root.innerHTML;
    document.getElementById('addNoticeBody').value = (h === '<p><br></p>') ? '' : h;
});
document.getElementById('editNoticeForm').addEventListener('submit', function() {
    const h = editQuill.root.innerHTML;
    document.getElementById('editNoticeBody').value = (h === '<p><br></p>') ? '' : h;
});
document.getElementById('addNoticeModal').addEventListener('hidden.bs.modal', function() {
    addQuill.setContents([]);
    document.querySelector('#addNoticeForm input[name=title]').value = '';
});

function openEditNotice(id, title, body, status) {
    document.getElementById('editNoticeTitle').value = title;
    editQuill.root.innerHTML = body || '';
    document.querySelector('#editStatusDraft').checked   = (status === 'draft');
    document.querySelector('#editStatusPublish').checked = (status === 'published');
    document.getElementById('editNoticeForm').action = '{{ url("mess/".$mess->id."/notices") }}/' + id;
    new bootstrap.Modal(document.getElementById('editNoticeModal')).show();
}
function openDeleteNotice(id, title) {
    document.getElementById('deleteNoticeName').textContent = title;
    document.getElementById('deleteNoticeForm').action = '{{ url("mess/".$mess->id."/notices") }}/' + id;
    new bootstrap.Modal(document.getElementById('deleteNoticeModal')).show();
}
</script>
@endif

<style>
.nav-tabs-bordered .nav-link { border:none; border-bottom:2px solid transparent; border-radius:0; padding:.6rem 1rem; color:#6c757d; font-weight:500; }
.nav-tabs-bordered .nav-link.active { border-bottom-color:#206bc4; color:#206bc4; background:transparent; }
.nav-tabs-bordered .nav-link:hover { color:#206bc4; background:transparent; }
.status-option:has(input:checked) { border-color:#206bc4 !important; background:#f0f5ff; }
.status-option { cursor:pointer; transition:border-color .15s,background .15s; }
.ql-toolbar.ql-snow { border-radius:6px 6px 0 0; border-color:#dee2e6; background:#f8f9fa; }
.ql-container.ql-snow { border-color:#dee2e6; border-radius:0 0 6px 6px; min-height:160px; }
</style>
@endsection
