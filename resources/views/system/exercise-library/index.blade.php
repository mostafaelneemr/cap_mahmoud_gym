@extends('system.layout')

@section('header')
<style>
    .video-modal-container iframe,
    .video-modal-container video {
        width: 100%;
        height: 420px;
        border-radius: 8px;
        border: none;
    }
</style>
@endsection

@section('content')

    @include('system.datatable')

    <!-- Video Preview Modal -->
    <div class="modal fade" id="exercise-video-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="exercise-video-modal-title">@lang('Exercise Video Preview')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center video-modal-container">
                    <div id="exercise-video-frame-wrap" class="d-none">
                        <iframe id="exercise-video-iframe" src="" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                    </div>
                    <div id="exercise-video-direct-wrap" class="d-none">
                        <video id="exercise-video-player" controls style="max-height: 420px; width: 100%;">
                            <source id="exercise-video-source" src="" type="video/mp4">
                            @lang('Your browser does not support the video tag.')
                        </video>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="exercise-video-external-link" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-light-primary btn-sm">
                        <i class="fas fa-external-link-alt me-1"></i> @lang('Open in New Tab')
                    </a>
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer')
<script>
    $(document).ready(function() {
        // Video Preview Modal Trigger
        $(document).on('click', '.btn-preview-video', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var name = $(this).data('name') || "@lang('Exercise Video')";

            $('#exercise-video-modal-title').text(name);
            $('#exercise-video-external-link').attr('href', url);

            var isDirectVideo = url.match(/\.(mp4|webm|ogg)(\?.*)?$/i);

            if (isDirectVideo) {
                $('#exercise-video-frame-wrap').addClass('d-none');
                $('#exercise-video-iframe').attr('src', '');
                $('#exercise-video-direct-wrap').removeClass('d-none');
                $('#exercise-video-source').attr('src', url);
                var player = document.getElementById('exercise-video-player');
                player.load();
            } else {
                $('#exercise-video-direct-wrap').addClass('d-none');
                var player = document.getElementById('exercise-video-player');
                if (player) { player.pause(); }
                $('#exercise-video-frame-wrap').removeClass('d-none');
                $('#exercise-video-iframe').attr('src', url);
            }

            var modal = new bootstrap.Modal(document.getElementById('exercise-video-modal'));
            modal.show();
        });

        // Clear video on modal close
        $('#exercise-video-modal').on('hidden.bs.modal', function () {
            $('#exercise-video-iframe').attr('src', '');
            var player = document.getElementById('exercise-video-player');
            if (player) {
                player.pause();
                $('#exercise-video-source').attr('src', '');
            }
        });

        // Quick Toggle Active/Inactive Status
        $(document).on('click', '.btn-toggle-status', function(e) {
            e.preventDefault();
            var btn = $(this);
            var url = btn.data('url');
            var currentStatus = btn.data('status');
            var confirmMsg = (currentStatus === 'active')
                ? "@lang('Are you sure you want to deactivate this exercise? It will no longer be selectable for new workout plans.')"
                : "@lang('Are you sure you want to activate this exercise?')";

            var executeToggle = function() {
                btn.prop('disabled', true);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        btn.prop('disabled', false);
                        if (res.status || res.success) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: "@lang('Success')",
                                    text: res.message || "@lang('Status updated successfully')",
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                            if (typeof window.LaravelDataTables !== 'undefined') {
                                Object.keys(window.LaravelDataTables).forEach(function(key) {
                                    window.LaravelDataTables[key].ajax.reload(null, false);
                                });
                            } else {
                                location.reload();
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ icon: 'error', title: "@lang('Error')", text: res.message || "@lang('Failed to update status')" });
                            }
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false);
                        var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "@lang('An error occurred')";
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: "@lang('Error')", text: errMsg });
                        }
                    }
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: "@lang('Confirm Status Change')",
                    text: confirmMsg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "@lang('Yes, proceed!')",
                    cancelButtonText: "@lang('Cancel')"
                }).then(function(result) {
                    if (result.isConfirmed) {
                        executeToggle();
                    }
                });
            } else {
                if (confirm(confirmMsg)) {
                    executeToggle();
                }
            }
        });
    });
</script>
@endsection
