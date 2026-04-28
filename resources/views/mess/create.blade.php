<?php $page = "mess-create" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold">{{ __('Create New Mess') }}</h4>
                <h6>{{ __('Set up your mess and start managing') }}</h6>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ti ti-building-community me-2 text-primary"></i>{{ __('Mess Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('mess.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3 text-center">
                                <div class="avatar avatar-xxl mb-2" id="avatarPreview">
                                    <span class="avatar-title rounded-circle bg-light text-muted fs-1" id="avatarPlaceholder">
                                        <i class="ti ti-building-community"></i>
                                    </span>
                                    <img id="avatarImg" src="" class="img-fluid rounded-circle d-none" alt="">
                                </div>
                                <div>
                                    <label class="btn btn-outline-primary btn-sm">
                                        <i class="ti ti-upload me-1"></i>Upload Logo
                                        <input type="file" name="avatar" accept="image/*" class="d-none" onchange="previewAvatar(this)">
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mess Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="e.g. Green House Mess" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Description') }}</label>
                                <textarea name="description" class="form-control" rows="3"
                                    placeholder="Brief description of your mess">{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Address') }}</label>
                                <input type="text" name="address" class="form-control"
                                    value="{{ old('address') }}" placeholder="Mess address / location">
                            </div>

                            <div class="alert alert-info d-flex align-items-start gap-2 mb-4">
                                <i class="ti ti-info-circle fs-5 mt-1"></i>
                                <div>
                                    <strong>{{ __('Free Plan') }}:</strong> {{ __('Default') }} {{ $sysSettings->default_max_members }} {{ __('members per mess') }}. {{ __('Upgrade subscription to add more members') }}.
                                    {{ __('You can create up to') }} <strong>{{ $sysSettings->default_max_messes }} {{ __('messes') }}</strong>.
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-building-community me-1"></i>Create Mess
                                </button>
                                <a href="{{ route('mess.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatarImg').src = e.target.result;
            document.getElementById('avatarImg').classList.remove('d-none');
            document.getElementById('avatarPlaceholder').classList.add('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
