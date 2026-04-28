@php
    $isEdit     = !is_null($ann);
    $oldAud     = old('audience', $isEdit ? $ann->audience : 'all');
    $oldMessIds = old('mess_ids', $isEdit ? ($ann->mess_ids ?? []) : []);
    $oldMessIds = array_map('strval', (array) $oldMessIds);
    $oldExp     = old('expires_at', $isEdit && $ann->expires_at ? $ann->expires_at->format('Y-m-d\TH:i') : '');
@endphp

<div data-audience-select>
    <div class="mb-3">
        <label class="form-label fw-semibold">{{ __('Title') }} <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $isEdit ? $ann->title : '') }}"
               placeholder="{{ __('Announcement title...') }}" maxlength="255" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">{{ __('Message') }} <span class="text-danger">*</span></label>
        <textarea name="body" class="form-control @error('body') is-invalid @enderror"
                  rows="4" maxlength="5000" required
                  placeholder="{{ __('Write your announcement here...') }}">{{ old('body', $isEdit ? $ann->body : '') }}</textarea>
        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">{{ __('Audience') }} <span class="text-danger">*</span></label>
        <div class="d-flex gap-4">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="audience"
                       id="{{ $isEdit ? 'aud-all-'.$ann->id : 'aud-all' }}"
                       value="all" {{ $oldAud === 'all' ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $isEdit ? 'aud-all-'.$ann->id : 'aud-all' }}">
                    <i class="ti ti-world me-1 text-primary"></i>{{ __('All Messes') }}
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="audience"
                       id="{{ $isEdit ? 'aud-ind-'.$ann->id : 'aud-ind' }}"
                       value="individual" {{ $oldAud === 'individual' ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $isEdit ? 'aud-ind-'.$ann->id : 'aud-ind' }}">
                    <i class="ti ti-building me-1 text-info"></i>{{ __('Selected Messes') }}
                </label>
            </div>
        </div>
    </div>

    <div data-mess-wrap class="mb-3 {{ $oldAud !== 'individual' ? 'd-none' : '' }}">
        <label class="form-label fw-semibold">
            {{ __('Select Messes') }} <span class="text-danger">*</span>
        </label>
        <select name="mess_ids[]" multiple
                class="form-control ann-mess-select @error('mess_ids') is-invalid @enderror"
                style="width:100%;">
            @foreach($messes as $m)
            <option value="{{ $m->id }}" {{ in_array((string)$m->id, $oldMessIds) ? 'selected' : '' }}>
                {{ $m->name }}
            </option>
            @endforeach
        </select>
        @error('mess_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="mb-0">
        <label class="form-label fw-semibold">
            {{ __('Visible Until') }}
            <span class="fw-normal text-muted small">({{ __('optional — leave blank to show indefinitely') }})</span>
        </label>
        <input type="datetime-local" name="expires_at"
               class="form-control @error('expires_at') is-invalid @enderror"
               value="{{ $oldExp }}">
        @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text text-muted">{{ __('After this date the announcement will no longer appear on mess dashboards.') }}</div>
    </div>
</div>
