{{--
    Shared styles and scripts for Warmup Links (max 2) and Machine Image Upload.
--}}
<style>
    /* Warmup links section */
    .wl-section {
        margin-top: 0.65rem;
        padding: 0.65rem 0.85rem;
        background: rgba(15, 23, 42, 0.45);
        border: 1px dashed var(--wb-row-border, rgba(51, 65, 85, 0.6));
        border-radius: 10px;
    }
    [data-bs-theme="light"] .wl-section {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    .wl-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    .wl-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--wb-text-secondary, #cbd5e1);
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin: 0;
    }
    [data-bs-theme="light"] .wl-title {
        color: #475569;
    }
    .wl-badge-counter {
        font-size: 0.72rem;
        padding: 2px 8px;
        border-radius: 6px;
        background: rgba(250, 204, 21, 0.12);
        color: #facc15;
        font-weight: 600;
    }
    [data-bs-theme="light"] .wl-badge-counter {
        background: rgba(217, 119, 6, 0.12);
        color: #b45309;
    }
    .wl-list {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }
    .wl-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        animation: wlFadeIn 0.2s ease-out;
    }
    @keyframes wlFadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .wl-input-wrap {
        position: relative;
        flex: 1;
    }
    .wl-input-wrap i {
        position: absolute;
        top: 50%;
        inset-inline-start: 0.85rem;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.85rem;
        pointer-events: none;
    }
    .wl-input-wrap .form-control {
        padding-inline-start: 2.25rem !important;
        font-size: 0.85rem !important;
        height: 38px !important;
    }
    .wl-preview-btn {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 8px;
        background: rgba(0, 158, 247, 0.12);
        color: #009ef7;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
        flex-shrink: 0;
        font-size: 0.85rem;
    }
    .wl-preview-btn:hover:not(.is-disabled):not(:disabled) {
        background: #009ef7;
        color: #fff;
        transform: scale(1.05);
    }
    .wl-preview-btn.is-disabled,
    .wl-preview-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background: rgba(148, 163, 184, 0.1);
        color: #94a3b8;
    }
    .wl-remove-btn {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 8px;
        background: rgba(244, 63, 94, 0.12);
        color: #f43f5e;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
        flex-shrink: 0;
    }
    .wl-remove-btn:hover {
        background: #f43f5e;
        color: #fff;
        transform: scale(1.05);
    }
    .wl-add-btn {
        margin-top: 0.45rem;
        border: 1px dashed var(--wb-accent, #facc15);
        background: rgba(250, 204, 21, 0.08);
        color: var(--wb-accent, #facc15);
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.35rem 0.75rem;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .wl-add-btn:hover:not(:disabled) {
        background: var(--wb-accent, #facc15);
        color: #0f172a;
    }
    .wl-add-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        display: none !important;
    }

    /* Machine image UI inside exercise row */
    .mi-wrapper {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .mi-preview-btn {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        border: 1px solid var(--wb-input-border, #334155);
        background: var(--wb-input-bg, rgba(2, 6, 23, 0.55));
        padding: 0;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: border-color 0.15s ease, transform 0.15s ease;
    }
    .mi-preview-btn:hover {
        border-color: var(--wb-accent, #facc15);
        transform: scale(1.04);
    }
    .mi-preview-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }
    .mi-preview-empty {
        font-size: 0.95rem;
        color: var(--wb-text-muted, #94a3b8);
    }
    .mi-wrapper.has-image .mi-preview-img {
        display: block;
    }
    .mi-wrapper.has-image .mi-preview-empty {
        display: none;
    }
    .mi-wrapper.has-image .mi-preview-btn::after {
        content: '\f00e';
        font-family: 'Font Awesome 5 Free', 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        opacity: 0;
        transition: opacity 0.15s ease;
    }
    .mi-wrapper.has-image .mi-preview-btn:hover::after {
        opacity: 1;
    }
    .mi-actions {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        flex: 1;
    }
    .mi-upload-label {
        border: 1px solid var(--wb-input-border, #334155);
        background: var(--wb-input-bg, rgba(2, 6, 23, 0.55));
        color: var(--wb-text, #f1f5f9);
        border-radius: 8px;
        padding: 0.4rem 0.65rem;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin: 0;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    [data-bs-theme="light"] .mi-upload-label {
        background: #fff;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .mi-upload-label:hover {
        border-color: var(--wb-accent, #facc15);
        color: var(--wb-accent, #facc15);
    }
    .mi-file-input {
        display: none;
    }
    .mi-remove-btn {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 8px;
        background: rgba(244, 63, 94, 0.12);
        color: #f43f5e;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
        flex-shrink: 0;
    }
    .mi-wrapper.has-image .mi-remove-btn {
        display: inline-flex;
    }
    .mi-remove-btn:hover {
        background: #f43f5e;
        color: #fff;
    }

    /* Badges for warmup links on show views */
    .warmup-links-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        margin-top: 0.45rem;
    }
    .warmup-link-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.65rem;
        border-radius: 7px;
        font-size: 0.78rem;
        font-weight: 600;
        background: rgba(250, 204, 21, 0.12);
        color: #facc15;
        border: 1px solid rgba(250, 204, 21, 0.3);
        text-decoration: none;
        transition: all 0.15s ease;
        cursor: pointer;
    }
    .warmup-link-pill:hover {
        background: #facc15;
        color: #0f172a !important;
        transform: translateY(-1px);
    }
    [data-bs-theme="light"] .warmup-link-pill {
        background: rgba(217, 119, 6, 0.1);
        color: #b45309;
        border-color: rgba(217, 119, 6, 0.25);
    }
    [data-bs-theme="light"] .warmup-link-pill:hover {
        background: #d97706;
        color: #fff !important;
    }
</style>

{{-- Global Lightbox Modal for Machine Images --}}
<div class="modal fade" id="gymMachineImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden" style="background: #0f172a; color: #fff;">
            <div class="modal-header border-0 py-3 px-4 d-flex justify-content-between align-items-center" style="background: rgba(30, 41, 59, 0.75);">
                <h5 class="modal-title fw-bold fs-6 m-0 d-flex align-items-center gap-2" id="gymMachineImageModalTitle">
                    <i class="fas fa-camera text-warning"></i>
                    <span>@lang('Equipment / Machine Image')</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center" style="background: #020617;">
                <img id="gymMachineImageModalImg" src="" alt="Machine Preview" class="img-fluid" style="max-height: 75vh; width: auto; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
window.GymMedia = window.GymMedia || (function ($) {
    const MAX_WARMUP = 5;

    function buildWarmupLinksHtml(baseName, existingLinks) {
        let links = Array.isArray(existingLinks) ? existingLinks.filter(l => l && String(l).trim() !== '') : [];
        if (links.length === 0) {
            links = [''];
        }
        if (links.length > MAX_WARMUP) {
            links = links.slice(0, MAX_WARMUP);
        }

        const isFull = links.length >= MAX_WARMUP;

        let rowsHtml = '';
        links.forEach((link, idx) => {
            const safeLink = link ? $('<div>').text(link).html() : '';
            const hasLink = !!(link && String(link).trim() !== '');
            rowsHtml += `
            <div class="wl-item" data-index="${idx}">
                <div class="wl-input-wrap">
                    <i class="fas fa-link"></i>
                    <input type="url" name="${baseName}[]" value="${safeLink}"
                           class="form-control form-control-solid wl-input" maxlength="500"
                           placeholder="https://video link.com/... (@lang('Warmup Link') ${idx + 1})"
                           aria-label="@lang('Warmup Link') ${idx + 1}">
                </div>
                <button type="button" class="wl-preview-btn ${hasLink ? '' : 'is-disabled'}"
                        title="@lang('Preview / Open Video')"
                        aria-label="@lang('Preview / Open Video')"
                        ${hasLink ? '' : 'aria-disabled="true"'}>
                    <i class="fas fa-external-link-alt"></i>
                </button>
                <button type="button" class="wl-remove-btn" title="@lang('Remove')">
                    <i class="fas fa-times"></i>
                </button>
            </div>`;
        });

        return `
        <div class="wl-section" data-base-name="${baseName}">
            <div class="wl-header">
                <span class="wl-title"><i class="fas fa-play-circle text-warning"></i> @lang('Warmup Video Links')</span>
                <span class="wl-badge-counter"><span class="wl-count">${links.filter(l => String(l).trim() !== '').length || links.length}</span> / ${MAX_WARMUP}</span>
            </div>
            <div class="wl-list">
                ${rowsHtml}
            </div>
            <button type="button" class="wl-add-btn" ${isFull ? 'disabled style="display:none !important;"' : ''}>
                <i class="fas fa-plus"></i> @lang('Add Warmup Link')
            </button>
        </div>`;
    }

    function buildMachineImageHtml(baseName, existingUrl, exerciseName) {
        const hasImg = !!existingUrl;
        const safeUrl = existingUrl ? $('<div>').text(existingUrl).html() : '';
        const safeName = exerciseName ? $('<div>').text(exerciseName).html() : '';

        return `
        <div class="mi-wrapper ${hasImg ? 'has-image' : ''}" data-base-name="${baseName}">
            <button type="button" class="mi-preview-btn" data-img-url="${safeUrl}" data-title="${safeName}" title="${hasImg ? '@lang('View Machine Image')' : '@lang('Upload Machine Image')'}">
                <img src="${safeUrl}" alt="Machine" class="mi-preview-img">
                <span class="mi-preview-empty"><i class="fas fa-camera"></i></span>
            </button>
            <div class="mi-actions">
                <label class="mi-upload-label">
                    <i class="fas fa-upload"></i>
                    <span class="mi-upload-text">${hasImg ? '@lang('Change Photo')' : '@lang('Machine Photo')'}</span>
                    <input type="file" name="${baseName}[machine_image]" class="mi-file-input" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                </label>
                <button type="button" class="mi-remove-btn" title="@lang('Remove Image')">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <input type="hidden" name="${baseName}[remove_machine_image]" class="mi-remove-flag" value="0">
            </div>
        </div>`;
    }

    function updateWarmupCounter($section) {
        const count = $section.find('.wl-item').length;
        $section.find('.wl-count').text(count);
        const $addBtn = $section.find('.wl-add-btn');
        if (count >= MAX_WARMUP) {
            $addBtn.prop('disabled', true).hide();
        } else {
            $addBtn.prop('disabled', false).show();
        }
    }

    // Add warmup link (up to MAX_WARMUP = 5)
    $(document).on('click', '.wl-add-btn', function () {
        const $section = $(this).closest('.wl-section');
        const count = $section.find('.wl-item').length;
        if (count >= MAX_WARMUP) return;

        const baseName = $section.data('base-name');
        const nextIdx = count + 1;
        const newRow = `
        <div class="wl-item" data-index="${count}">
            <div class="wl-input-wrap">
                <i class="fas fa-link"></i>
                <input type="url" name="${baseName}[]" value=""
                       class="form-control form-control-solid wl-input" maxlength="500"
                       placeholder="https://video link.com/... (@lang('Warmup Link') ${nextIdx})"
                       aria-label="@lang('Warmup Link') ${nextIdx}">
            </div>
            <button type="button" class="wl-preview-btn is-disabled"
                    title="@lang('Preview / Open Video')"
                    aria-label="@lang('Preview / Open Video')"
                    aria-disabled="true">
                <i class="fas fa-external-link-alt"></i>
            </button>
            <button type="button" class="wl-remove-btn" title="@lang('Remove')">
                <i class="fas fa-times"></i>
            </button>
        </div>`;
        $section.find('.wl-list').append(newRow);
        $section.find('.wl-item:last-child input').trigger('focus');
        updateWarmupCounter($section);
    });

    // Remove warmup link
    $(document).on('click', '.wl-remove-btn', function () {
        const $section = $(this).closest('.wl-section');
        const $items = $section.find('.wl-item');
        if ($items.length > 1) {
            $(this).closest('.wl-item').remove();
        } else {
            // If only 1 link left, just clear its text
            $items.find('input').val('');
            $items.find('.wl-preview-btn').addClass('is-disabled').attr('aria-disabled', 'true');
        }
        updateWarmupCounter($section);
    });

    // Preview / Open Warmup Video Link in new tab
    $(document).on('click', '.wl-preview-btn', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const $item = $btn.closest('.wl-item');
        let url = $.trim($item.find('.wl-input').val());

        if (!url) {
            const noUrlMsg = "{{ __('Please enter a video URL first.') }}";
            if (typeof notify === 'function') {
                notify(noUrlMsg, 'warning');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: "{{ __('No URL') }}",
                    text: noUrlMsg,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert(noUrlMsg);
            }
            $item.find('.wl-input').trigger('focus');
            return;
        }

        if (!/^https?:\/\//i.test(url)) {
            url = 'https://' + url;
        }

        try {
            const parsed = new URL(url);
            if (!parsed.hostname || !parsed.hostname.includes('.')) {
                throw new Error('Invalid host');
            }
            window.open(url, '_blank', 'noopener,noreferrer');
        } catch (err) {
            const invalidMsg = "{{ __('Please enter a valid URL.') }}";
            if (typeof notify === 'function') {
                notify(invalidMsg, 'error');
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: "{{ __('Invalid URL') }}",
                    text: invalidMsg,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert(invalidMsg);
            }
            $item.find('.wl-input').trigger('focus');
        }
    });

    // Toggle Preview button active/disabled state on typing
    $(document).on('input change', '.wl-input', function () {
        const val = $.trim($(this).val());
        const $btn = $(this).closest('.wl-item').find('.wl-preview-btn');
        if (val && (val.includes('.') || /^https?:\/\//i.test(val))) {
            $btn.removeClass('is-disabled').removeAttr('aria-disabled');
        } else {
            $btn.addClass('is-disabled').attr('aria-disabled', 'true');
        }
    });

    // Machine image file selection -> instant thumbnail preview
    $(document).on('change', '.mi-file-input', function () {
        const input = this;
        const $wrapper = $(input).closest('.mi-wrapper');
        const file = input.files && input.files[0];

        if (file) {
            if (!file.type.match(/^image\/(jpeg|png|webp|jpg)$/)) {
                if (typeof notify === 'function') {
                    notify("{{ __('Please select a valid image file (JPG, PNG, WEBP).') }}", 'error');
                } else {
                    alert("{{ __('Please select a valid image file (JPG, PNG, WEBP).') }}");
                }
                $(input).val('');
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const dataUrl = e.target.result;
                $wrapper.find('.mi-preview-img').attr('src', dataUrl);
                $wrapper.find('.mi-preview-btn').attr('data-img-url', dataUrl);
                $wrapper.addClass('has-image');
                $wrapper.find('.mi-upload-text').text("{{ __('Change Photo') }}");
                $wrapper.find('.mi-remove-flag').val('0');
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove machine image
    $(document).on('click', '.mi-remove-btn', function () {
        const $wrapper = $(this).closest('.mi-wrapper');
        $wrapper.find('.mi-file-input').val('');
        $wrapper.find('.mi-preview-img').attr('src', '');
        $wrapper.find('.mi-preview-btn').attr('data-img-url', '');
        $wrapper.removeClass('has-image');
        $wrapper.find('.mi-upload-text').text("{{ __('Machine Photo') }}");
        $wrapper.find('.mi-remove-flag').val('1'); // Mark for deletion on backend
    });

    // Click on thumbnail -> show lightbox modal or trigger upload if empty
    $(document).on('click', '.mi-preview-btn', function (e) {
        e.preventDefault();
        const imgUrl = $(this).attr('data-img-url');
        const title = $(this).attr('data-title') || "{{ __('Machine / Equipment') }}";

        if (imgUrl && imgUrl.trim() !== '') {
            $('#gymMachineImageModalTitle span').text(title);
            $('#gymMachineImageModalImg').attr('src', imgUrl);
            const modal = new bootstrap.Modal(document.getElementById('gymMachineImageModal'));
            modal.show();
        } else {
            // Trigger file picker
            $(this).closest('.mi-wrapper').find('.mi-file-input').trigger('click');
        }
    });

    return {
        buildWarmupLinks: buildWarmupLinksHtml,
        buildMachineImage: buildMachineImageHtml
    };
})(jQuery);
</script>
