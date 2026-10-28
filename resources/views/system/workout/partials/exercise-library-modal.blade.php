<style>
    .el-picker-list {
        max-height: 380px;
        overflow-y: auto;
    }
    .el-picker-item {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.15s ease;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(15, 23, 42, 0.35);
        margin-bottom: 0.5rem;
    }
    [data-bs-theme="light"] .el-picker-item {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    .el-picker-item:hover {
        border-color: #38bdf8;
        background: rgba(56, 189, 248, 0.08);
    }
    .wb-library-badge {
        font-size: 0.72rem !important;
        padding: 3px 7px !important;
        vertical-align: middle;
    }
    .wb-library-wrap {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
</style>

<!-- Exercise Library Selection Modal -->
<div class="modal fade" id="exerciseLibraryPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header py-4 px-6 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded" style="width: 36px; height: 36px; background: rgba(56, 189, 248, 0.15); color: #38BDF8;">
                        <i class="fas fa-book-open fs-5"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold m-0">@lang('Central Exercise Video Library')</h5>
                        <p class="text-muted fs-8 mb-0">@lang('Pick an exercise to link its video URL across plans.')</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-6">
                <!-- Search Box -->
                <div class="position-relative mb-5">
                    <i class="fas fa-search position-absolute top-50 translate-middle-y ms-4 text-muted fs-5"></i>
                    <input type="text" id="el-picker-search-input" class="form-control form-control-solid ps-12"
                           placeholder="@lang('Search active exercises by name...')" autocomplete="off">
                    <span id="el-picker-spinner" class="spinner-border spinner-border-sm text-primary position-absolute top-50 translate-middle-y me-4 d-none" style="right: 15px;" role="status"></span>
                </div>

                <!-- Exercise List -->
                <div id="el-picker-list-container" class="el-picker-list">
                    <div id="el-picker-loading" class="text-center py-8 d-none text-muted">
                        <span class="spinner-border spinner-border-sm text-primary me-2"></span> @lang('Loading library exercises...')
                    </div>
                    <div id="el-picker-empty" class="text-center py-8 d-none text-muted">
                        <i class="fas fa-dumbbell fs-1 mb-3 text-gray-500"></i>
                        <div class="fs-6 fw-semibold">@lang('No matching active exercises found.')</div>
                        <div class="fs-8 mt-1">@lang('Make sure the exercise exists in the library and has active status.')</div>
                    </div>
                    <div id="el-picker-results"></div>
                </div>
            </div>
            <div class="modal-footer py-3 px-6 d-flex justify-content-between align-items-center">
                <a href="{{ route('system.exercise-library.index') }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light">
                    <i class="fas fa-cog me-1"></i> @lang('Manage Library')
                </a>
                <button type="button" class="btn btn-sm btn-light-primary" data-bs-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
window.GymLibraryPicker = window.GymLibraryPicker || (function ($) {
    var activeRow = null;
    var searchTimer = null;
    var searchUrl = "{{ route('system.exercise-library.search') }}";

    function loadExercises(term) {
        $('#el-picker-spinner').removeClass('d-none');
        $('#el-picker-empty').addClass('d-none');
        $('#el-picker-results').empty();

        $.ajax({
            url: searchUrl,
            type: 'GET',
            data: { q: term || '' },
            dataType: 'json',
            success: function (res) {
                $('#el-picker-spinner').addClass('d-none');
                var items = (res && res.results) ? res.results : [];

                if (items.length === 0) {
                    $('#el-picker-empty').removeClass('d-none');
                    return;
                }

                var html = '';
                items.forEach(function (item) {
                    var safeName = $('<div>').text(item.name).html();
                    var safeDesc = item.description ? $('<div>').text(item.description).html() : '';
                    var safeUrl = item.video_url ? $('<div>').text(item.video_url).html() : '';
                    var formattedUrl = item.formatted_video_url ? $('<div>').text(item.formatted_video_url).html() : safeUrl;

                    html += `
                    <div class="el-picker-item d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex flex-column flex-grow-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold fs-6 text-gray-800">${safeName}</span>
                                ${item.video_url ? '<span class="badge badge-light-success fs-9"><i class="fas fa-video me-1"></i>Video</span>' : '<span class="badge badge-light-secondary fs-9">No Video</span>'}
                            </div>
                            ${safeDesc ? `<span class="text-muted fs-8 mt-1">${safeDesc}</span>` : ''}
                            ${safeUrl ? `<span class="text-primary fs-8 text-truncate mt-1" style="max-width: 320px;"><i class="fab fa-youtube me-1"></i>${safeUrl}</span>` : ''}
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            ${safeUrl ? `
                            <a href="${safeUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-icon btn-light" title="@lang('Preview Video')">
                                <i class="fas fa-external-link-alt fs-8"></i>
                            </a>` : ''}
                            <button type="button" class="btn btn-sm btn-primary btn-select-library-item"
                                    data-id="${item.id}"
                                    data-name="${safeName}"
                                    data-url="${safeUrl}"
                                    data-formatted-url="${formattedUrl}">
                                <i class="fas fa-check me-1"></i> @lang('Select')
                            </button>
                        </div>
                    </div>`;
                });

                $('#el-picker-results').html(html);
            },
            error: function () {
                $('#el-picker-spinner').addClass('d-none');
                $('#el-picker-empty').removeClass('d-none');
            }
        });
    }

    $(document).ready(function () {
        // Open Picker Modal
        $(document).on('click', '.btn-open-library-picker', function (e) {
            e.preventDefault();
            activeRow = $(this).closest('.exercise-row');
            $('#el-picker-search-input').val('');
            loadExercises('');
            var modal = new bootstrap.Modal(document.getElementById('exerciseLibraryPickerModal'));
            modal.show();
        });

        // Search Debounce
        $('#el-picker-search-input').on('input', function () {
            clearTimeout(searchTimer);
            var term = $(this).val();
            searchTimer = setTimeout(function () {
                loadExercises(term);
            }, 250);
        });

        // Select an item from the library
        $(document).on('click', '.btn-select-library-item', function (e) {
            e.preventDefault();
            if (!activeRow || activeRow.length === 0) return;

            var id = $(this).data('id');
            var name = $(this).data('name');
            var url = $(this).data('url');
            var formattedUrl = $(this).data('formatted-url');

            // 1. Populate exercise name
            var nameInput = activeRow.find('.wb-name');
            if (nameInput.length) {
                nameInput.val(name).trigger('input');
            }

            // 2. Set hidden library ID
            var libraryInput = activeRow.find('.wb-library-id');
            if (libraryInput.length) {
                libraryInput.val(id);
            }

            // 3. Populate video link
            var videoInput = activeRow.find('.wb-video-input');
            if (videoInput.length) {
                videoInput.val(url).trigger('input');
            }

            // 4. Update preview button
            var previewBtn = activeRow.find('.wb-video-preview-btn');
            if (previewBtn.length) {
                if (url) {
                    previewBtn.removeClass('is-disabled').removeAttr('aria-disabled').attr('data-video-url', formattedUrl || url);
                } else {
                    previewBtn.addClass('is-disabled').attr('aria-disabled', 'true');
                }
            }

            // 5. Show linked badge & unlink button
            activeRow.find('.wb-library-badge').removeClass('d-none').show();
            activeRow.find('.btn-unlink-library').removeClass('d-none').show();

            // Close modal
            var modalEl = document.getElementById('exerciseLibraryPickerModal');
            var modalInst = bootstrap.Modal.getInstance(modalEl);
            if (modalInst) {
                modalInst.hide();
            }
        });

        // Unlink from library
        $(document).on('click', '.btn-unlink-library', function (e) {
            e.preventDefault();
            var row = $(this).closest('.exercise-row');
            row.find('.wb-library-id').val('');
            row.find('.wb-library-badge').addClass('d-none').hide();
            $(this).addClass('d-none').hide();
        });
    });

    return {
        loadExercises: loadExercises
    };
})(jQuery);
</script>
