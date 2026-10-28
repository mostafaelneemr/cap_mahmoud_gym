@extends('system.layout')

@section('header')
<style>
    .el-header-banner {
        background: linear-gradient(135deg, #1e1e2d 0%, #2b2b40 100%);
        border-radius: 14px;
        padding: 1.5rem 2rem;
        color: #ffffff;
        margin-bottom: 2rem;
    }
    [data-bs-theme="dark"] .el-header-banner {
        background: linear-gradient(135deg, #151521 0%, #1e1e2d 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .video-live-preview-box {
        background: #0f172a;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        min-height: 240px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed rgba(255,255,255,0.2);
    }
    .video-live-preview-box iframe,
    .video-live-preview-box video {
        width: 100%;
        height: 320px;
        border: none;
    }
</style>
@endsection

@section('content')

    @php
        $isEdit = isset($record) && $record;
        $actionUrl = $isEdit ? route('system.exercise-library.update', $record->id) : route('system.exercise-library.store');
        $method = $isEdit ? 'PATCH' : 'POST';
    @endphp

    <div class="el-header-banner d-flex align-items-center justify-content-between flex-wrap gap-4">
        <div class="d-flex align-items-center gap-3">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(56, 189, 248, 0.15); color: #38BDF8;">
                <i class="fas fa-video fs-3"></i>
            </span>
            <div>
                <h3 class="mb-1 text-white fw-bold">{{ $isEdit ? __('Edit Central Exercise') : __('Register New Exercise') }}</h3>
                <p class="mb-0 text-white-50 fs-7">
                    {{ __('Register once in the central video library to use across all trainee workout plans.') }}
                </p>
            </div>
        </div>
        <div>
            <a href="{{ route('system.exercise-library.index') }}" class="btn btn-sm btn-light">
                <i class="fas fa-arrow-left me-1"></i> @lang('Back to Library')
            </a>
        </div>
    </div>

    {!! Form::open(['id'=>'main-form','onsubmit' => 'FormSubmit("'.$actionUrl.'");return false;','method' => $method]) !!}
    <div id="form-alert-message"></div>

    <div class="card card-flush shadow-sm mb-7">
        <div class="card-body p-8">
            <div class="row g-5">
                <!-- Exercise Name -->
                <div class="col-md-6 fv-row mb-5">
                    <label class="form-label required fw-bold text-gray-900 fs-6">@lang('Exercise Name')</label>
                    <input type="text" name="name" id="name-form-input" class="form-control form-control-solid"
                           maxlength="150" required placeholder="@lang('e.g. Incline Dumbbell Press / Lat Pulldown')"
                           value="{{ $isEdit ? $record->name : old('name') }}">
                    <div class="invalid-feedback" id="name-form-error"></div>
                </div>

                <!-- Status -->
                <div class="col-md-6 fv-row mb-5">
                    <label class="form-label required fw-bold text-gray-900 fs-6">@lang('Status')</label>
                    <select name="status" id="status-form-input" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                        <option value="active" {{ ($isEdit ? $record->status : 'active') === 'active' ? 'selected' : '' }}>
                            @lang('Active (Available for workout plans)')
                        </option>
                        <option value="inactive" {{ ($isEdit ? $record->status : '') === 'inactive' ? 'selected' : '' }}>
                            @lang('Inactive (Temporarily hidden from new selection)')
                        </option>
                    </select>
                    <div class="form-text text-muted fs-8">
                        @lang('Inactive status prevents selection for new plans, but never breaks existing assigned workouts.')
                    </div>
                    <div class="invalid-feedback" id="status-form-error"></div>
                </div>

                <!-- Video URL -->
                <div class="col-md-12 fv-row mb-5">
                    <label class="form-label fw-bold text-gray-900 fs-6">@lang('Exercise Video URL')</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fab fa-youtube text-danger fs-5"></i></span>
                        <input type="url" name="video_url" id="video_url-form-input" class="form-control form-control-solid"
                               maxlength="255" placeholder="https://www.youtube.com/watch?v=... or https://drive.google.com/..."
                               value="{{ $isEdit ? $record->video_url : old('video_url') }}">
                        <button type="button" class="btn btn-light-primary" id="btn-refresh-preview" title="@lang('Preview Video')">
                            <i class="fas fa-play me-1"></i> @lang('Test Preview')
                        </button>
                    </div>
                    <div class="form-text text-muted fs-8">
                        @lang('Supports YouTube videos, YouTube Shorts, Google Drive share links, and direct video links (.mp4).')
                    </div>
                    <div class="invalid-feedback" id="video_url-form-error"></div>
                </div>

                <!-- Live Preview Section -->
                <div class="col-md-12 mb-5">
                    <label class="form-label fw-semibold text-gray-700 fs-7">@lang('Live Video Preview')</label>
                    <div id="video-preview-wrapper" class="video-live-preview-box">
                        <div id="video-preview-placeholder" class="text-center p-6 text-muted">
                            <i class="fas fa-film fs-1 mb-3 text-gray-600"></i>
                            <div class="fs-6">@lang('Enter a video URL above to preview the video.')</div>
                        </div>
                        <iframe id="video-preview-iframe" class="d-none" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                        <video id="video-preview-player" class="d-none" controls>
                            <source id="video-preview-source" src="" type="video/mp4">
                        </video>
                    </div>
                </div>

                <!-- Description / Form Cues -->
                <div class="col-md-12 fv-row mb-5">
                    <label class="form-label fw-bold text-gray-900 fs-6">@lang('Description / Form Cues (Optional)')</label>
                    <textarea name="description" id="description-form-input" rows="3" class="form-control form-control-solid"
                              placeholder="@lang('Technique tips, muscle focus, tempo recommendations, or setup instructions...')">{{ $isEdit ? $record->description : old('description') }}</textarea>
                    <div class="invalid-feedback" id="description-form-error"></div>
                </div>

                @if($isEdit && isset($record->exercises_count))
                    <div class="col-md-12">
                        <div class="alert alert-dismissible bg-light-primary border border-primary d-flex flex-column flex-sm-row p-5 mb-5">
                            <i class="fas fa-info-circle fs-2hx text-primary me-4 mb-5 mb-sm-0"></i>
                            <div class="d-flex flex-column pe-0 pe-sm-10">
                                <h5 class="mb-1 text-primary">@lang('Linked Workout Plans Notice')</h5>
                                <span>
                                    @lang('This exercise is currently linked to :count workout plan assignments. Updating the video URL here will instantly update the video link across all :count assigned plans without altering each trainee\'s personalized weights, sets, or reps.', ['count' => $record->exercises()->count()])
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-3 pt-6 border-top">
                <a href="{{ route('system.exercise-library.index') }}" class="btn btn-light">
                    @lang('Cancel')
                </a>
                <button type="submit" class="btn btn-primary" id="btn-save-exercise">
                    <i class="fas fa-check me-1"></i> {{ $isEdit ? __('Update Exercise') : __('Save Exercise') }}
                </button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}

@endsection

@section('footer')
<script>
    $(document).ready(function() {
        function formatEmbedUrl(url) {
            if (!url) return null;
            url = url.trim();
            if (!url) return null;

            if (url.indexOf('drive.google.com') !== -1) {
                var driveMatch = url.match(/\/d\/([a-zA-Z0-9_-]+)/);
                if (driveMatch && driveMatch[1]) {
                    return "https://drive.google.com/file/d/" + driveMatch[1] + "/preview";
                }
            }

            var ytMatch = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
            if (ytMatch && ytMatch[1]) {
                return "https://www.youtube.com/embed/" + ytMatch[1];
            }

            return url;
        }

        function updateLivePreview() {
            var url = $('#video_url-form-input').val();
            var embedUrl = formatEmbedUrl(url);

            if (!embedUrl) {
                $('#video-preview-iframe').addClass('d-none').attr('src', '');
                $('#video-preview-player').addClass('d-none');
                $('#video-preview-placeholder').removeClass('d-none');
                return;
            }

            var isDirectVideo = embedUrl.match(/\.(mp4|webm|ogg)(\?.*)?$/i);
            $('#video-preview-placeholder').addClass('d-none');

            if (isDirectVideo) {
                $('#video-preview-iframe').addClass('d-none').attr('src', '');
                $('#video-preview-player').removeClass('d-none');
                $('#video-preview-source').attr('src', embedUrl);
                document.getElementById('video-preview-player').load();
            } else {
                $('#video-preview-player').addClass('d-none');
                var player = document.getElementById('video-preview-player');
                if (player) { player.pause(); }
                $('#video-preview-iframe').removeClass('d-none').attr('src', embedUrl);
            }
        }

        $('#video_url-form-input').on('input change blur', function() {
            updateLivePreview();
        });

        $('#btn-refresh-preview').on('click', function() {
            updateLivePreview();
        });

        // Initialize preview on edit if video_url is present
        @if($isEdit && !empty($record->video_url))
            updateLivePreview();
        @endif
    });
</script>
@endsection
