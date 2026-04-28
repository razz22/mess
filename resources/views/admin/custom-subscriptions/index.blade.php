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
                    <i class="ti ti-star fs-5"></i>
                </span>
                {{ __('Custom Subscriptions') }}
            </h4>
            <p class="text-muted mb-0 small">{{ __('Assign custom member limits and pricing to specific messes.') }}</p>
        </div>
        <button class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="ti ti-plus"></i>{{ __('New Custom Subscription') }}
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
    <i class="ti ti-circle-check fs-5 flex-shrink-0"></i><div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Table --}}
<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="card-body p-0">
        @if($subscriptions->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="ti ti-star fs-1 opacity-25 d-block mb-2"></i>
            <h6 class="fw-normal">{{ __('No custom subscriptions yet.') }}</h6>
            <p class="small mb-0">{{ __('Click "New Custom Subscription" to create one.') }}</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8f9fb;">
                    <tr>
                        <th class="px-4 py-3 fw-semibold small text-muted">{{ __('Label') }}</th>
                        <th class="py-3 fw-semibold small text-muted">{{ __('Members') }}</th>
                        <th class="py-3 fw-semibold small text-muted">{{ __('Price') }}</th>
                        <th class="py-3 fw-semibold small text-muted">{{ __('Assigned Messes') }}</th>
                        <th class="py-3 fw-semibold small text-muted">{{ __('Starts') }}</th>
                        <th class="py-3 fw-semibold small text-muted">{{ __('Expires') }}</th>
                        <th class="py-3 fw-semibold small text-muted">{{ __('Status') }}</th>
                        <th class="py-3 fw-semibold small text-muted text-end pe-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($subscriptions as $sub)
                @php $active = $sub->isActive(); @endphp
                <tr>
                    <td class="px-4 py-3">
                        <div class="fw-semibold">{{ $sub->label }}</div>
                        @if($sub->notes)
                        <div class="small text-muted text-truncate" style="max-width:200px;">{{ $sub->notes }}</div>
                        @endif
                    </td>
                    <td class="py-3">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                            {{ $sub->max_members }} {{ __('members') }}
                        </span>
                    </td>
                    <td class="py-3">
                        @if($sub->is_free)
                        <span class="badge bg-success-subtle text-success border border-success-subtle">{{ __('Free') }}</span>
                        @else
                        <span class="fw-semibold">৳{{ number_format($sub->price, 2) }}</span>
                        @endif
                    </td>
                    <td class="py-3 small" style="max-width:220px;">
                        <span class="text-truncate d-block">{{ $sub->mess_names }}</span>
                    </td>
                    <td class="py-3 small">{{ $sub->starts_at->format('d M Y') }}</td>
                    <td class="py-3 small">
                        @if($sub->expires_at)
                        <span class="{{ $active ? 'text-body' : 'text-danger' }}">
                            {{ $sub->expires_at->format('d M Y') }}
                        </span>
                        @else
                        <span class="text-muted">{{ __('Never') }}</span>
                        @endif
                    </td>
                    <td class="py-3">
                        @if($active)
                        <span class="badge bg-success-subtle text-success border border-success-subtle">{{ __('Active') }}</span>
                        @elseif($sub->status === 'inactive')
                        <span class="badge bg-secondary-subtle text-secondary border">{{ __('Inactive') }}</span>
                        @else
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">{{ __('Expired') }}</span>
                        @endif
                    </td>
                    <td class="py-3 pe-4 text-end">
                        <button class="btn btn-sm btn-outline-secondary me-1"
                                data-bs-toggle="modal" data-bs-target="#editModal{{ $sub->id }}">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal" data-bs-target="#deleteModal{{ $sub->id }}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>

                {{-- Edit Modal --}}
                <div class="modal fade" id="editModal{{ $sub->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold">{{ __('Edit Custom Subscription') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.custom-subscriptions.update', $sub) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-body px-4">
                                    @include('admin.custom-subscriptions._form', ['sub' => $sub, 'messes' => $messes])
                                    <div class="mb-3 mt-3">
                                        <label class="form-label fw-semibold">{{ __('Status') }}</label>
                                        <select name="status" class="form-select">
                                            <option value="active"   {{ $sub->status === 'active'   ? 'selected' : '' }}>{{ __('Active') }}</option>
                                            <option value="inactive" {{ $sub->status === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Delete Modal --}}
                <div class="modal fade" id="deleteModal{{ $sub->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-body text-center px-4 pt-4 pb-3">
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-danger-subtle mx-auto mb-3"
                                     style="width:56px;height:56px;">
                                    <i class="ti ti-trash text-danger fs-3"></i>
                                </div>
                                <h6 class="fw-bold mb-1">{{ __('Delete Custom Subscription?') }}</h6>
                                <p class="text-muted small mb-0">
                                    {{ __('This will remove') }} <strong>"{{ $sub->label }}"</strong>.
                                    {{ __('Assigned messes will revert to their default limits.') }}
                                </p>
                            </div>
                            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">{{ __('No, Cancel') }}</button>
                                <form action="{{ route('admin.custom-subscriptions.destroy', $sub) }}" method="POST">
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
        @if($subscriptions->hasPages())
        <div class="px-4 py-3 border-top">{{ $subscriptions->links() }}</div>
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
                    <i class="ti ti-star text-primary"></i>{{ __('New Custom Subscription') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.custom-subscriptions.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    @include('admin.custom-subscriptions._form', ['sub' => null, 'messes' => $messes])
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                        <i class="ti ti-star"></i>{{ __('Assign Subscription') }}
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

        // Select2 for mess multi-select
        $modal.find('.cs-mess-select').select2({
            placeholder: '{{ __('Search and select messes...') }}',
            allowClear: true,
            width: '100%',
            dropdownParent: $modal,
        });

        // Free toggle
        $modal.find('input[name="is_free"]').on('change', function () {
            $modal.find('.price-wrap').toggleClass('d-none', $(this).is(':checked'));
        });
    }

    document.querySelectorAll('.modal').forEach(function (modalEl) {
        modalEl.addEventListener('shown.bs.modal', function () { initModal(this); });
    });

    @if($errors->any())
    new bootstrap.Modal(document.getElementById('createModal')).show();
    @endif
});
</script>
@endpush
@endsection
