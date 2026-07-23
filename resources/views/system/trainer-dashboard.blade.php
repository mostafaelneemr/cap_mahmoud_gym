@extends('system.layout')

@section('header')
    <style>
        /* ===== Trainee Dashboard Custom Styles ===== */
        .trainee-welcome-card {
            background: linear-gradient(135deg, #1e1e2d 0%, #2d2d44 50%, #1a1a2e 100%);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 2rem 2.5rem;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .trainee-welcome-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(0, 158, 247, 0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .trainee-welcome-card .welcome-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .trainee-welcome-card .welcome-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1rem;
        }

        .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 10px 18px;
            color: #fff;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .stat-badge:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-2px);
        }

        .stat-badge i {
            font-size: 1.1rem;
        }

        .stat-badge .stat-label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.75rem;
        }

        .stat-badge .stat-value {
            font-weight: 600;
        }

        /* Day Tabs */
        .day-tabs-wrapper {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .day-tabs .nav-link {
            border: none;
            color: #7e8299;
            font-weight: 600;
            padding: 14px 24px;
            border-radius: 0;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .day-tabs .nav-link:hover {
            color: #009ef7;
            background: rgba(0, 158, 247, 0.04);
        }

        .day-tabs .nav-link.active {
            color: #009ef7;
            border-bottom-color: #009ef7;
            background: rgba(0, 158, 247, 0.06);
        }

        .day-tabs .nav-link .day-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: #f1f1f2;
            font-size: 0.8rem;
            font-weight: 700;
            margin-inline-end: 8px;
            transition: all 0.3s ease;
        }

        .day-tabs .nav-link.active .day-number {
            background: #009ef7;
            color: #fff;
        }

        /* Day Content Card */
        .day-content-card {
            border: none;
            border-radius: 0 0 14px 14px;
            box-shadow: none;
        }

        .warmup-section,
        .post-workout-section {
            background: linear-gradient(135deg, #fff8e1 0%, #fff3cd 100%);
            border-radius: 12px;
            padding: 16px 20px;
            border-left: 4px solid #ffc107;
        }

        .warmup-section {
            border-left-color: #ffc107;
            background: linear-gradient(135deg, #fff8e1 0%, #fff3cd 100%);
        }

        .post-workout-section {
            border-left-color: #7239ea;
            background: linear-gradient(135deg, #f8f5ff 0%, #ede5ff 100%);
        }

        .section-label {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        /* Exercise Table */
        .exercise-table-wrapper {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #f1f1f2;
        }

        .exercise-table {
            margin-bottom: 0;
        }

        .exercise-table thead th {
            background: #f9fafb;
            border-bottom: 2px solid #e9ecef;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #7e8299;
            padding: 14px 16px;
            white-space: nowrap;
        }

        .exercise-table tbody tr {
            transition: all 0.2s ease;
        }

        .exercise-table tbody tr:hover {
            background: rgba(0, 158, 247, 0.03);
        }

        .exercise-table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f5f5f5;
            font-size: 0.95rem;
        }

        .exercise-name {
            font-weight: 600;
            color: #181c32;
        }

        .exercise-index {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, #009ef7, #0095e8);
            color: #fff;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .badge-metric {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-sets {
            background: rgba(80, 205, 137, 0.1);
            color: #50cd89;
        }

        .badge-reps {
            background: rgba(0, 158, 247, 0.1);
            color: #009ef7;
        }

        .badge-rest {
            background: rgba(255, 168, 0, 0.1);
            color: #ffa800;
        }

        .badge-weight {
            background: rgba(114, 57, 234, 0.1);
            color: #7239ea;
        }

        .badge-tempo {
            background: rgba(245, 108, 108, 0.1);
            color: #f56c6c;
        }

        .video-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            background: rgba(255, 0, 0, 0.08);
            color: #e02d2d;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .video-link:hover {
            background: rgba(255, 0, 0, 0.15);
            color: #c62828;
            transform: translateY(-1px);
        }

        /* Video Modal */
        #videoModal .modal-content {
            background: #1a1a2e;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            overflow: hidden;
        }

        #videoModal .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 16px 24px;
        }

        #videoModal .modal-title {
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
        }

        #videoModal .btn-close {
            filter: invert(1);
            opacity: 0.6;
        }

        #videoModal .btn-close:hover {
            opacity: 1;
        }

        #videoModal .modal-body {
            padding: 0;
            background: #000;
        }

        #videoModal .video-iframe-wrapper {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%;
            /* 16:9 */
            height: 0;
            overflow: hidden;
        }

        #videoModal .video-iframe-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state-icon {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f1f1f2, #e8e8ed);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .empty-state-icon i {
            font-size: 3rem;
            color: #b5b5c3;
        }

        .empty-state h3 {
            font-weight: 700;
            color: #181c32;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #a1a5b7;
            font-size: 1rem;
        }

        /* Status Badges */
        .status-active {
            background: rgba(80, 205, 137, 0.12);
            color: #50cd89;
        }

        .status-inactive {
            background: rgba(181, 181, 195, 0.12);
            color: #b5b5c3;
        }

        .status-expired {
            background: rgba(245, 108, 108, 0.12);
            color: #f56c6c;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .trainee-welcome-card {
                padding: 1.5rem;
            }

            .trainee-welcome-card .welcome-title {
                font-size: 1.4rem;
            }

            .stat-badge {
                padding: 8px 12px;
                font-size: 0.8rem;
            }

            .day-tabs .nav-link {
                padding: 10px 14px;
                font-size: 0.85rem;
            }

            .exercise-table-wrapper {
                overflow-x: auto;
            }
        }
    </style>
@endsection

@section('content')

    {{-- ===== Welcome Card ===== --}}
    <div class="trainee-welcome-card mb-8">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-4">
            <div>
                <h1 class="welcome-title">
                    {{ __('Welcome') }}, {{ $user->name }} 👋
                </h1>
                <p class="welcome-subtitle mb-0">{{ __('Here is your workout program') }}</p>
            </div>

            @if($trainee)
                <div class="d-flex flex-wrap gap-3">
                    <div class="stat-badge">
                        <i class="fas fa-dumbbell text-info"></i>
                        <div>
                            <div class="stat-label">{{ __('Level') }}</div>
                            <div class="stat-value">{{ ucfirst($trainee->training_level) }}</div>
                        </div>
                    </div>

                    <div class="stat-badge">
                        <i class="fas fa-calendar-check text-success"></i>
                        <div>
                            <div class="stat-label">{{ __('Start') }}</div>
                            <div class="stat-value">{{ $trainee->membership_start }}</div>
                        </div>
                    </div>

                    <div class="stat-badge">
                        <i class="fas fa-calendar-times text-danger"></i>
                        <div>
                            <div class="stat-label">{{ __('End') }}</div>
                            <div class="stat-value">{{ $trainee->membership_end }}</div>
                        </div>
                    </div>

                    <div class="stat-badge">
                <span class="badge rounded-pill fs-8 px-3 py-2
                            @if($trainee->status == 'active') status-active
                            @elseif($trainee->status == 'expired') status-expired
                            @else status-inactive @endif">
                    {{ ucfirst($trainee->status) }}
                </span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== Workout Program Section ===== --}}
    @if($trainee && count($workoutPlans) > 0)

        <div class="day-tabs-wrapper">
            {{-- Day Tabs --}}
            <ul class="nav nav-tabs day-tabs border-0 px-3 pt-3 flex-nowrap overflow-auto" id="workoutDayTabs"
                role="tablist">
                @foreach($workoutPlans as $index => $plan)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="day-tab-{{ $plan->id }}"
                                data-bs-toggle="tab" data-bs-target="#day-content-{{ $plan->id }}" type="button"
                                role="tab"
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            <span class="day-number">{{ $index + 1 }}</span>
                            {{ $plan->day_name }}
                        </button>
                    </li>
                @endforeach
            </ul>

            {{-- Day Content --}}
            <div class="tab-content p-6" id="workoutDayTabsContent">
                @foreach($workoutPlans as $index => $plan)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="day-content-{{ $plan->id }}"
                         role="tabpanel">

                        {{-- Warmup --}}
                        @if($plan->warmup)
                            <div class="warmup-section mb-5">
                                <div class="section-label text-warning">
                                    <i class="fas fa-fire me-1"></i> {{ __('Warm Up') }}
                                </div>
                                <div class="text-gray-800 fw-semibold">{{ $plan->warmup }}</div>
                            </div>
                        @endif

                        {{-- Exercises Table --}}
                        @if($plan->exercises && count($plan->exercises) > 0)
                            <div class="exercise-table-wrapper mb-5">
                                <table class="table exercise-table">
                                    <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>{{ __('Exercise') }}</th>
                                        <th>{{ __('Sets') }}</th>
                                        <th>{{ __('Reps') }}</th>
                                        <th>{{ __('Rest') }}</th>
                                        <th>{{ __('Weight') }}</th>
                                        <th>{{ __('Tempo') }}</th>
                                        <th>{{ __('Video') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($plan->exercises as $exIndex => $exercise)
                                        <tr>
                                            <td>
                                                <span class="exercise-index">{{ $exIndex + 1 }}</span>
                                            </td>
                                            <td>
                                                <span class="exercise-name">{{ $exercise->name }}</span>
                                            </td>
                                            <td>
                                                @if($exercise->sets)
                                                    <span class="badge-metric badge-sets">
                                    <i class="fas fa-layer-group"></i>
                                    {{ $exercise->sets }}
                                </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($exercise->reps)
                                                    <span class="badge-metric badge-reps">
                                    <i class="fas fa-redo"></i>
                                    {{ $exercise->reps }}
                                </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($exercise->rest)
                                                    <span class="badge-metric badge-rest">
                                    <i class="fas fa-clock"></i>
                                    {{ $exercise->rest }}
                                </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($exercise->internal_weight)
                                                    <span class="badge-metric badge-weight">
                                    <i class="fas fa-weight-hanging"></i>
                                    {{ $exercise->internal_weight }}
                                </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($exercise->tempo)
                                                    <span class="badge-metric badge-tempo">
                                    <i class="fas fa-tachometer-alt"></i>
                                    {{ $exercise->tempo }}
                                </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($exercise->link)
                                                    <button type="button" class="video-link open-video-btn"
                                                            data-video-url="{{ $exercise->link }}"
                                                            data-exercise-name="{{ $exercise->name }}">
                                                        <i class="fab fa-youtube"></i>
                                                        {{ __('Watch') }}
                                                    </button>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-muted">
                                <i class="fas fa-dumbbell fs-2 mb-3 d-block"></i>
                                {{ __('No exercises added for this day yet') }}
                            </div>
                        @endif

                        {{-- Post Workout --}}
                        @if($plan->post_workout)
                            <div class="post-workout-section">
                                <div class="section-label text-primary">
                                    <i class="fas fa-flag-checkered me-1"></i> {{ __('Post Workout') }}
                                </div>
                                <div class="text-gray-800 fw-semibold">{{ $plan->post_workout }}</div>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        </div>

    @else

        @if(isset($isExpired) && $isExpired)
            {{-- Expired State --}}
            <div class="card border border-danger border-2">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-state-icon bg-light-danger">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                        </div>
                        <h3 class="text-danger">{{ __('Membership Expired or Inactive') }}</h3>
                        <p>{{ __('Your gym subscription has expired or is inactive. Please renew your membership with the administration to access your assigned workout programs and exercises.') }}</p>
                    </div>
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <h3>{{ __('No Workout Program Yet') }}</h3>
                        <p>{{ __('Your trainer has not assigned a workout program to you yet. Please check back later.') }}</p>
                    </div>
                </div>
            </div>
        @endif

    @endif


    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="videoModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="video-iframe-wrapper">
                        <iframe id="videoIframe" src="" allowfullscreen
                                referrerpolicy="strict-origin-when-cross-origin"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer')
    <script>
        $(document).ready(function () {
            function toEmbedUrl(url) {
                if (!url) return url;

                // 1. Google Drive
                if (url.indexOf('drive.google.com') !== -1) {
                    var driveMatch = url.match(/\/d\/([a-zA-Z0-9_-]+)/);
                    if (driveMatch && driveMatch[1]) {
                        return 'https://drive.google.com/file/d/' + driveMatch[1] + '/preview';
                    }
                }

                // 2. YouTube
                var videoId = null;

                // youtu.be/VIDEO_ID
                var match = url.match(/youtu\.be\/([a-zA-Z0-9_-]+)/);
                if (match) videoId = match[1];

                // youtube.com/watch?v=VIDEO_ID
                if (!videoId) {
                    match = url.match(/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]+)/);
                    if (match) videoId = match[1];
                }

                // youtube.com/shorts/VIDEO_ID
                if (!videoId) {
                    match = url.match(/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/);
                    if (match) videoId = match[1];
                }

                // m.youtube.com/watch?v=VIDEO_ID
                if (!videoId) {
                    match = url.match(/m\.youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]+)/);
                    if (match) videoId = match[1];
                }

                // youtube.com/embed/VIDEO_ID
                if (!videoId) {
                    match = url.match(/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/);
                    if (match) return 'https://www.youtube.com/embed/' + match[1];
                }

                if (videoId) {
                    return 'https://www.youtube.com/embed/' + videoId;
                }

                return url;
            }

            // Open video modal
            $(document).on('click', '.open-video-btn', function () {
                var rawVideoUrl = $(this).data('video-url');
                var exerciseName = $(this).data('exercise-name');
                var embedUrl = toEmbedUrl(rawVideoUrl);

                $('#videoModalLabel').text(exerciseName);
                $('#videoIframe').attr('src', embedUrl);

                // تحديث زرار الفتح المباشر بالرابط الأصلي
                $('#directVideoLink').attr('href', rawVideoUrl);

                $('#videoModal').modal('show');
            });

            // Clear iframe src on close
            $('#videoModal').on('hidden.bs.modal', function () {
                $('#videoIframe').attr('src', '');
                $('#directVideoLink').attr('href', '#');
            });
        });
    </script>
@endsection
