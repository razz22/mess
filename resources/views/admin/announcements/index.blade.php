@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
<div class="content">

{{-- Page Header --}}
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <span class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width:36px;height:36px;">
                    <i class="ti ti-speakerphone fs-5"></i>
                </span>
                {{ __('Announcements') }}
            </h4>
            <p class="text-muted mb-0 small">{{ __('Post announcements to all messes or a specific mess.') }}</p>
        </div>
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="ti ti-plus"></i>{{ __('New Announcement') }}
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
    <i class="ti ti-circle-check fs-5 flex-shrink-0"></i><div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Announcements Table --}}
<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="card-body p-0">
        @if($announcements->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="ti ti-speakerphone fs-1 opacity-25 d-block mb-2"></i>
            <h6 class="fw-normal">{{ __('No announcements yet.') }}</h6>
            <p class="small mb-0">{{ __('Click "New Announcement" to create one.') }}</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8f9fb;">
                    <tr>
                        <th class="px-4 py-3 fw-semibold small text-muted">{{ __('Title') }}</th>
                        <th class="py-3 fw-semibold small text-muted">{{ __('Audience') }}</th>
                        <th class="py-3 fw-semibold small text-muted">{{ __('Mess') }}</th>
                        <th class="py-3 fw-semibold small text-muted">{{ __('Expires') }}</th>
                        <th class="py-3 fw-semibold small text-muted">{{ __('Status') }}</th>
                        <th class="py-3 fw-semibold small text-muted">{{ __('Created') }}</th>
                        <th class="py-3 fw-semibold small text-muted text-end pe-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($announcements as $ann)
                @php $active = $ann->isActive(); @endphp
                <tr>
                    <td class="px-4 py-3">
                        <div class="fw-semibold">{{ $ann->title }}</div>
                        <div class="small text-muted text-truncate" style="max-width:260px;">{{ $ann->body }}</div>
                    </td>
                    <td class="py-3">
                        @if($ann->audience === 'all')
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ __('All Messes') }}</span>
                        @else
                        <span class="badge bg-info-subtle text-info border border-info-subtle">{{ __('Individual') }}</span>
                        @endif
                    </td>
                    <td class="py-3" style="max-width:200px;">
                        @if($ann->audience === 'individual' && !empty($ann->mess_ids))
                            <span class="small">{{ $ann->mess_names ?: '—' }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="py-3 small">
                        @if($ann->expires_at)
                            <span class="{{ $active ? 'text-body' : 'text-danger' }}">
                                {{ $ann->expires_at->format('d M Y, h:i A') }}
                            </span>
                        @else
                            <span class="text-muted">{{ __('Never') }}</span>
                        @endif
                    </td>
                    <td class="py-3">
                        @if($active)
                        <span class="badge bg-success-subtle text-success border border-success-subtle">{{ __('Active') }}</span>
                        @else
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">{{ __('Expired') }}</span>
                        @endif
                    </td>
                    <td class="py-3 small text-muted">{{ $ann->created_at->format('d M Y') }}</td>
                    <td class="py-3 pe-4 text-end">
                        <button class="btn btn-sm btn-outline-secondary me-1"
                                data-bs-toggle="modal" data-bs-target="#editModal{{ $ann->id }}">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal" data-bs-target="#deleteModal{{ $ann->id }}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>

                {{-- Edit Modal for this announcement --}}
                <div class="modal fade" id="editModal{{ $ann->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold">{{ __('Edit Announcement') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.announcements.update', $ann) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-body px-4">
                                    @include('admin.announcements._form', ['ann' => $ann, 'messes' => $messes])
                                </div>
                                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Delete Confirmation Modal --}}
                <div class="modal fade" id="deleteModal{{ $ann->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-body text-center px-4 pt-4 pb-3">
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-danger-subtle mx-auto mb-3"
                                     style="width:56px;height:56px;">
                                    <i class="ti ti-trash text-danger fs-3"></i>
                                </div>
                                <h6 class="fw-bold mb-1">{{ __('Delete Announcement?') }}</h6>
                                <p class="text-muted small mb-0">{{ __('This will permanently remove the announcement') }} <strong>"{{ $ann->title }}"</strong>. {{ __('This action cannot be undone.') }}</p>
                            </div>
                            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">{{ __('No, Cancel') }}</button>
                                <form action="{{ route('admin.announcements.destroy', $ann) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger px-4">{{ __('Yes, Delete') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                @endforeach
                </tbody>
            </table>
        </div>
        @if($announcements->hasPages())
        <div class="px-4 py-3 border-top">{{ $announcements->links() }}</div>
        @endif
        @endif
    </div>
</div>

</div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <i class="ti ti-speakerphone text-primary"></i>{{ __('New Announcement') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    @include('admin.announcements._form', ['ann' => null, 'messes' => $messes])
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                        <i class="ti ti-speakerphone"></i>{{ __('Post Announcement') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {

    function initModal(modalEl) {
        var $modal = $(modalEl);

        // Initialize Select2 inside this modal
        $modal.find('.ann-mess-select').select2({
            placeholder: '{{ __('Search and select messes...') }}',
            allowClear: true,
            width: '100%',
            dropdownParent: $modal,
        });

        // Show/hide mess selector based on audience radio
        var $wrap     = $modal.find('[data-audience-select]');
        var $messWrap = $wrap.find('[data-mess-wrap]');
        $wrap.find('input[name="audience"]').on('change', function () {
            $messWrap.toggleClass('d-none', $(this).val() !== 'individual');
        });
    }

    // Init every modal when it opens
    document.querySelectorAll('.modal').forEach(function (modalEl) {
        modalEl.addEventListener('shown.bs.modal', function () {
            initModal(this);
        });
    });

    // Re-open create modal on validation error (Select2 init triggered by shown.bs.modal)
    @if($errors->any())
    var createModal = new bootstrap.Modal(document.getElementById('createModal'));
    createModal.show();
    @endif

});
</script>
@endpush
@endsection
