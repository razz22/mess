@php
    $isEdit     = !is_null($sub);
    $oldMessIds = old('mess_ids', $isEdit ? ($sub->mess_ids ?? []) : []);
    $oldMessIds = array_map('strval', (array) $oldMessIds);
    $isFree     = old('is_free', $isEdit ? $sub->is_free : false);
@endphp

<div class="row g-3">

    {{-- Label --}}
    <div class="col-12">
        <label class="form-label fw-semibold">{{ __('Subscription Label') }} <span class="text-danger">*</span></label>
        <input type="text" name="label" class="form-control @error('label') is-invalid @enderror"
               value="{{ old('label', $isEdit ? $sub->label : '') }}"
               placeholder="{{ __('e.g. Ramadan Special, VIP Plan...') }}" maxlength="255" required>
        @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Max Members --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">{{ __('Max Members') }} <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="number" name="max_members" class="form-control @error('max_members') is-invalid @enderror"
                   value="{{ old('max_members', $isEdit ? $sub->max_members : '') }}"
                   placeholder="e.g. 50" min="1" max="9999" required>
            <span class="input-group-text"><i class="ti ti-users"></i></span>
        </div>
        @error('max_members')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    {{-- Price --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">{{ __('Pricing') }}</label>
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="is_free" id="{{ $isEdit ? 'is-free-'.$sub->id : 'is-free-new' }}"
                   value="1" {{ $isFree ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold text-success" for="{{ $isEdit ? 'is-free-'.$sub->id : 'is-free-new' }}">
                <i class="ti ti-gift me-1"></i>{{ __('Make it Free') }}
            </label>
        </div>
        <div class="price-wrap {{ $isFree ? 'd-none' : '' }}">
            <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                       value="{{ old('price', $isEdit ? $sub->price : '') }}"
                       placeholder="0.00" min="0" step="0.01">
            </div>
            @error('price')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
    </div>

    {{-- Assign Messes --}}
    <div class="col-12">
        <label class="form-label fw-semibold">{{ __('Assign to Messes') }} <span class="text-danger">*</span></label>
        <select name="mess_ids[]" multiple class="form-control cs-mess-select @error('mess_ids') is-invalid @enderror"
                style="width:100%;">
            @foreach($messes as $m)
            <option value="{{ $m->id }}" {{ in_array((string)$m->id, $oldMessIds) ? 'selected' : '' }}>
                {{ $m->name }}
            </option>
            @endforeach
        </select>
        @error('mess_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    {{-- Starts At --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">{{ __('Starts At') }}</label>
        <input type="datetime-local" name="starts_at" class="form-control"
               value="{{ old('starts_at', $isEdit && $sub->starts_at ? $sub->starts_at->format('Y-m-d\TH:i') : '') }}">
        <div class="form-text text-muted">{{ __('Leave blank to start immediately.') }}</div>
    </div>

    {{-- Expires At --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">{{ __('Expires At') }}</label>
        <input type="datetime-local" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror"
               value="{{ old('expires_at', $isEdit && $sub->expires_at ? $sub->expires_at->format('Y-m-d\TH:i') : '') }}">
        <div class="form-text text-muted">{{ __('Leave blank to never expire.') }}</div>
        @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Notes --}}
    <div class="col-12">
        <label class="form-label fw-semibold small">{{ __('Notes') }} <span class="fw-normal text-muted">({{ __('optional') }})</span></label>
        <textarea name="notes" class="form-control" rows="2" maxlength="1000"
                  placeholder="{{ __('Internal notes about this subscription...') }}">{{ old('notes', $isEdit ? $sub->notes : '') }}</textarea>
    </div>

</div>
