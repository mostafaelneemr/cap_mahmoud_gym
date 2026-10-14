{{--
    Machine / Equipment Image Input Blade Partial.
    Params:
      - $name: base name for exercise (e.g. "exercises[ex_1]")
      - $url: existing image URL (optional)
      - $alt: exercise name / description for modal preview
--}}
@php
    $hasImg = !empty($url);
    $baseName = $name ?? 'exercise';
    $safeUrl = $url ?? '';
    $safeAlt = $alt ?? __('Machine / Equipment');
@endphp
<div class="mi-wrapper {{ $hasImg ? 'has-image' : '' }}" data-base-name="{{ $baseName }}">
    <button type="button" class="mi-preview-btn" data-img-url="{{ $safeUrl }}" data-title="{{ $safeAlt }}" title="{{ $hasImg ? __('View Machine Image') : __('Upload Machine Image') }}">
        <img src="{{ $safeUrl }}" alt="Machine" class="mi-preview-img">
        <span class="mi-preview-empty"><i class="fas fa-camera"></i></span>
    </button>
    <div class="mi-actions">
        <label class="mi-upload-label">
            <i class="fas fa-upload"></i>
            <span class="mi-upload-text">{{ $hasImg ? __('Change Photo') : __('Machine Photo') }}</span>
            <input type="file" name="{{ $baseName }}[machine_image]" class="mi-file-input" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        </label>
        <button type="button" class="mi-remove-btn" title="@lang('Remove Image')">
            <i class="fas fa-trash-alt"></i>
        </button>
        <input type="hidden" name="{{ $baseName }}[remove_machine_image]" class="mi-remove-flag" value="0">
    </div>
</div>
