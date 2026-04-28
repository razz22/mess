<?php $page = "mess-rules" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-list-check me-2 text-primary"></i>{{ __('Mess Rules') }}</h4>
                <h6 class="text-muted">{{ $mess->name }}</h6>
            </div>
            @if($canManage)
            <div class="page-btn">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRuleModal">
                    <i class="ti ti-plus me-1"></i>Add Rule
                </button>
            </div>
            @endif
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="ti ti-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        {{-- Header banner --}}
        <div class="card mb-4" style="border:none;overflow:hidden;border-radius:14px;box-shadow:0 4px 24px rgba(32,107,196,.13);">
            <div style="background:linear-gradient(135deg,#206bc4 0%,#1a55a0 100%);padding:32px 36px;position:relative;overflow:hidden;">
                <div style="position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.06);"></div>
                <div style="position:absolute;right:80px;bottom:-50px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,.04);"></div>
                <div style="position:relative;z-index:1;display:flex;align-items:center;gap:20px;">
                    <div style="background:rgba(255,255,255,.15);border-radius:12px;width:56px;height:56px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ti ti-gavel" style="font-size:28px;color:#fff;"></i>
                    </div>
                    <div>
                        <div style="color:#fff;font-size:20px;font-weight:700;">House Rules</div>
                        <div style="color:rgba(255,255,255,.75);font-size:13px;margin-top:3px;">{{ $mess->name }} — Guidelines every member must follow</div>
                        <div style="margin-top:10px;display:flex;gap:10px;flex-wrap:wrap;">
                            <span style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 14px;font-size:12px;color:#fff;font-weight:600;">
                                <i class="ti ti-list me-1"></i>{{ $rules->count() }} Rule{{ $rules->count() !== 1 ? 's' : '' }}
                            </span>
                            <span style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:20px;padding:3px 14px;font-size:12px;color:#fff;">
                                <i class="ti ti-users me-1"></i>All Members
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($rules->isEmpty())
        <div class="card" style="border:none;box-shadow:0 2px 12px rgba(0,0,0,.06);border-radius:14px;">
            <div class="card-body text-center py-5">
                <div style="background:#f0f4ff;border-radius:50%;width:80px;height:80px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="ti ti-list-check" style="font-size:36px;color:#206bc4;opacity:.5;"></i>
                </div>
                <h6 class="fw-bold text-muted">No rules added yet</h6>
                @if($canManage)
                <p class="text-muted small mb-3">Add house rules that all members should follow.</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRuleModal">
                    <i class="ti ti-plus me-1"></i>Add First Rule
                </button>
                @else
                <p class="text-muted small mb-0">The owner hasn't added any rules yet.</p>
                @endif
            </div>
        </div>
        @else
        <div class="row g-3" id="rulesList">
            @foreach($rules as $i => $rule)
            <div class="col-12 rule-item" data-id="{{ $rule->id }}">
                <div class="card" style="border:none;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.06);transition:box-shadow .2s;border-left:4px solid #206bc4;">
                    <div class="card-body py-3 px-4">
                        <div class="d-flex align-items-start gap-3">
                            {{-- Number badge --}}
                            <div style="background:linear-gradient(135deg,#206bc4,#1a55a0);color:#fff;border-radius:10px;width:38px;height:38px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0;">
                                {{ $i + 1 }}
                            </div>
                            {{-- Content --}}
                            <div class="flex-grow-1">
                                <div class="fw-bold" style="font-size:15px;color:#1a2332;">{{ $rule->title }}</div>
                                @if($rule->description)
                                <div class="rule-description mt-2" style="font-size:13px;line-height:1.7;color:#495057;">{!! $rule->description !!}</div>
                                @endif
                                <div class="mt-2" style="font-size:11px;color:#adb5bd;">
                                    <i class="ti ti-user me-1"></i>Added by {{ $rule->createdBy->name }}
                                    · {{ $rule->created_at->format('d M Y') }}
                                </div>
                            </div>
                            {{-- Actions (owner only) --}}
                            @if($canManage)
                            <div class="d-flex gap-1 flex-shrink-0">
                                <button class="btn btn-sm btn-outline-primary py-1 px-2"
                                    onclick="openEdit({{ $rule->id }}, '{{ addslashes($rule->title) }}', {{ json_encode($rule->description ?? '') }})"
                                    title="Edit">
                                    <i class="ti ti-pencil" style="font-size:13px;"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger py-1 px-2"
                                    onclick="openDelete({{ $rule->id }}, '{{ addslashes($rule->title) }}')"
                                    title="Delete">
                                    <i class="ti ti-trash" style="font-size:13px;"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-3 text-center text-muted small">
            <i class="ti ti-info-circle me-1"></i>These rules apply to all members of <strong>{{ $mess->name }}</strong>.
        </div>
        @endif

    </div>
</div>

{{-- Add Rule Modal --}}
@if($canManage)
<div class="modal fade" id="addRuleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#206bc4,#1a55a0);border-radius:14px 14px 0 0;border:none;">
                <h5 class="modal-title text-white fw-bold"><i class="ti ti-plus me-2"></i>Add New Rule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mess.rules.store', $mess->id) }}" method="POST" id="addRuleForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rule Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Keep common areas clean" required maxlength="255">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Description <span class="text-muted small fw-normal">(optional)</span></label>
                        <div id="addEditor" style="height:220px;border-radius:0 0 6px 6px;font-size:13px;"></div>
                        <input type="hidden" name="description" id="addDescInput">
                        <div class="form-text">Use the toolbar to format your rule. Max 2000 characters.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="ti ti-check me-1"></i>Save Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Rule Modal --}}
<div class="modal fade" id="editRuleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#206bc4,#1a55a0);border-radius:14px 14px 0 0;border:none;">
                <h5 class="modal-title text-white fw-bold"><i class="ti ti-pencil me-2"></i>Edit Rule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRuleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rule Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="editTitle" class="form-control" required maxlength="255">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Description <span class="text-muted small fw-normal">(optional)</span></label>
                        <div id="editEditor" style="height:220px;border-radius:0 0 6px 6px;font-size:13px;"></div>
                        <input type="hidden" name="description" id="editDescInput">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="ti ti-check me-1"></i>Update Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Rule Modal --}}
<div class="modal fade" id="deleteRuleModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-body text-center py-4 px-4">
                <div class="mb-3">
                    <span class="avatar avatar-lg bg-danger-subtle rounded-circle">
                        <i class="ti ti-trash text-danger fs-3"></i>
                    </span>
                </div>
                <h6 class="fw-bold mb-1">Delete Rule?</h6>
                <p class="text-muted small mb-0"><strong id="deleteRuleName"></strong> will be permanently removed.</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4 gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <form id="deleteRuleForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm px-4">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Quill CDN --}}
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<script>
const baseUrl = '{{ rtrim(url('/'), '/') }}';
const messId  = {{ $mess->id }};

const toolbarOptions = [
    [{ 'header': [1, 2, 3, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ 'color': [] }, { 'background': [] }],
    [{ 'align': [] }],
    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
    ['link', 'image'],
    ['clean']
];

const addQuill = new Quill('#addEditor', {
    theme: 'snow',
    placeholder: 'Explain the rule in detail...',
    modules: { toolbar: toolbarOptions }
});

const editQuill = new Quill('#editEditor', {
    theme: 'snow',
    placeholder: 'Explain the rule in detail...',
    modules: { toolbar: toolbarOptions }
});

// Sync Quill content to hidden inputs before submit
document.getElementById('addRuleForm').addEventListener('submit', function() {
    const html = addQuill.root.innerHTML;
    document.getElementById('addDescInput').value = (html === '<p><br></p>') ? '' : html;
});

document.getElementById('editRuleForm').addEventListener('submit', function() {
    const html = editQuill.root.innerHTML;
    document.getElementById('editDescInput').value = (html === '<p><br></p>') ? '' : html;
});

// Clear add editor when modal closes
document.getElementById('addRuleModal').addEventListener('hidden.bs.modal', function() {
    addQuill.setContents([]);
});

function openEdit(id, title, desc) {
    document.getElementById('editTitle').value = title;
    editQuill.root.innerHTML = desc || '';
    document.getElementById('editRuleForm').action = baseUrl + '/mess/' + messId + '/rules/' + id;
    new bootstrap.Modal(document.getElementById('editRuleModal')).show();
}

function openDelete(id, title) {
    document.getElementById('deleteRuleName').textContent = title;
    document.getElementById('deleteRuleForm').action = baseUrl + '/mess/' + messId + '/rules/' + id;
    new bootstrap.Modal(document.getElementById('deleteRuleModal')).show();
}
</script>
@endif

<style>
.rule-item .card:hover { box-shadow: 0 6px 24px rgba(32,107,196,.15) !important; }
.rule-description img { max-width: 100%; border-radius: 6px; margin: 4px 0; }
.rule-description ul, .rule-description ol { padding-left: 1.4em; margin-bottom: 0; }
.rule-description p { margin-bottom: 0.4em; }
.rule-description p:last-child { margin-bottom: 0; }
/* Style Quill toolbar to match app theme */
.ql-toolbar.ql-snow { border-radius: 6px 6px 0 0; border-color: #dee2e6; background: #f8f9fa; }
.ql-container.ql-snow { border-color: #dee2e6; }
</style>
@endsection
