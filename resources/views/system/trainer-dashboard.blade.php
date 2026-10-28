@extends('system.layout')

@section('header')
    @include('system.workout.partials.warmup-editor-assets')
    @include('system.workout.partials.workout-media-assets')
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
            color: #ffffff !important;
        }

        .trainee-welcome-card .welcome-subtitle {
            color: rgba(255, 255, 255, 0.75);
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
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.75rem;
        }

        .stat-badge .stat-value {
            font-weight: 600;
            color: #ffffff;
        }

        /* Day Tabs */
        .day-tabs-wrapper {
            background: var(--gp-bg-surface, #fff);
            border: 1px solid var(--gp-border, #e9ecef);
            border-radius: 14px;
            box-shadow: var(--gp-shadow-sm, 0 2px 15px rgba(0, 0, 0, 0.04));
            overflow: hidden;
        }

        .day-tabs .nav-link {
            border: none;
            color: var(--gp-text-secondary, #7e8299);
            font-weight: 600;
            padding: 14px 24px;
            border-radius: 0;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .day-tabs .nav-link:hover {
            color: #009ef7;
            background: rgba(0, 158, 247, 0.05);
        }

        .day-tabs .nav-link.active {
            color: #009ef7;
            border-bottom-color: #009ef7;
            background: rgba(0, 158, 247, 0.08);
        }

        .day-tabs .nav-link .day-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--gp-bg-elevated, #f1f1f2);
            color: var(--gp-text-primary, #181c32);
            font-size: 0.8rem;
            font-weight: 700;
            margin-inline-end: 8px;
            transition: all 0.3s ease;
        }

        .day-tabs .nav-link.active .day-number {
            background: #009ef7;
            color: #ffffff;
        }

        /* Day Content Card */
        .day-content-card {
            border: none;
            border-radius: 0 0 14px 14px;
            box-shadow: none;
            background: transparent;
        }

        /* ==============================================================
           Warmup & Post-Workout Routines — Unified Lavender/Violet Theme
           Palette: #A78BFA (Tailwind violet-400)
           Default: subtle lavender gradient with soft violet border
           Hover: smooth transition to neutral muted gray/slate
           Main Program is NOT affected.
           ============================================================== */
        .warmup-section,
        .post-workout-section {
            --rt-accent: #A78BFA;
            --rt-accent-rgb: 167, 139, 250;
            --rt-base: rgba(15, 23, 42, 0.8);
            --rt-hover: rgba(30, 41, 59, 0.8);
            --rt-border: rgba(167, 139, 250, 0.3);
            --rt-hover-border: rgba(71, 85, 105, 0.5);
            position: relative;
            isolation: isolate;
            border-radius: 0.75rem !important; /* rounded-xl */
            padding: 20px 22px !important;
            background: var(--rt-base) !important;
            border: 1px solid var(--rt-border) !important;
            border-inline-start: 1px solid var(--rt-border) !important;
            box-shadow: 0 10px 28px -12px rgba(0, 0, 0, 0.55) !important;
            transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out !important;
        }

        .warmup-section::before,
        .post-workout-section::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            border-radius: inherit;
            pointer-events: none;
            background: linear-gradient(135deg,
                rgba(167, 139, 250, 0.10) 0%,
                rgba(15, 23, 42, 0.60) 50%,
                rgba(15, 23, 42, 0.80) 100%);
            opacity: 1;
            transition: opacity 0.3s ease-in-out;
        }

        .warmup-section:hover,
        .post-workout-section:hover {
            background: var(--rt-hover) !important;
            border-color: var(--rt-hover-border) !important;
            box-shadow: 0 14px 32px -10px rgba(0, 0, 0, 0.65) !important;
        }

        .warmup-section:hover::before,
        .post-workout-section:hover::before {
            opacity: 0;
        }

        /* Light Theme Dashboard Equivalent */
        [data-bs-theme="light"] .warmup-section,
        [data-bs-theme="light"] .post-workout-section {
            --rt-base: rgba(241, 245, 249, 0.85);
            --rt-hover: rgba(226, 232, 240, 0.75);
            --rt-border: rgba(167, 139, 250, 0.25);
            --rt-hover-border: rgba(203, 213, 225, 1);
            background: var(--rt-base) !important;
            border: 1px solid var(--rt-border) !important;
            border-inline-start: 1px solid var(--rt-border) !important;
            box-shadow: 0 8px 24px -12px rgba(15, 23, 42, 0.12) !important;
        }

        [data-bs-theme="light"] .warmup-section::before,
        [data-bs-theme="light"] .post-workout-section::before {
            background: linear-gradient(135deg,
                rgba(167, 139, 250, 0.15) 0%,
                rgba(241, 245, 249, 0.70) 50%,
                rgba(241, 245, 249, 0.90) 100%);
        }

        [data-bs-theme="light"] .warmup-section:hover,
        [data-bs-theme="light"] .post-workout-section:hover {
            background: var(--rt-hover) !important;
            border-color: var(--rt-hover-border) !important;
        }

        /* Header / Section Badges: Soft violet pill tag */
        .warmup-section .section-label,
        .post-workout-section .section-label {
            color: #A78BFA !important;
        }

        .warmup-section .section-label .badge,
        .post-workout-section .section-label .badge {
            background: rgba(167, 139, 250, 0.15) !important;
            color: #A78BFA !important;
            border: 1px solid rgba(167, 139, 250, 0.25) !important;
            border-radius: 9999px !important;
            padding: 4px 10px !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.03em !important;
        }

        /* Warmup & Post-Workout Inner Table Card */
        .warmup-section .exercise-table-wrapper,
        .post-workout-section .exercise-table-wrapper {
            background: rgba(15, 23, 42, 0.55) !important;
            border: 1px solid rgba(167, 139, 250, 0.22) !important;
            border-radius: 10px !important;
            overflow: hidden !important;
            box-shadow: 0 4px 16px -6px rgba(0, 0, 0, 0.4) !important;
            transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out !important;
        }

        .warmup-section:hover .exercise-table-wrapper,
        .post-workout-section:hover .exercise-table-wrapper {
            background: rgba(15, 23, 42, 0.75) !important;
            border-color: rgba(71, 85, 105, 0.45) !important;
        }

        [data-bs-theme="light"] .warmup-section .exercise-table-wrapper,
        [data-bs-theme="light"] .post-workout-section .exercise-table-wrapper {
            background: rgba(255, 255, 255, 0.85) !important;
            border-color: rgba(167, 139, 250, 0.22) !important;
            box-shadow: 0 4px 12px -4px rgba(15, 23, 42, 0.08) !important;
        }

        [data-bs-theme="light"] .warmup-section:hover .exercise-table-wrapper,
        [data-bs-theme="light"] .post-workout-section:hover .exercise-table-wrapper {
            background: rgba(255, 255, 255, 0.98) !important;
            border-color: rgba(203, 213, 225, 0.8) !important;
        }

        /* Table Header Row: subtle violet-tinted borders and text accents */
        .warmup-section .exercise-table thead th,
        .post-workout-section .exercise-table thead th {
            background: rgba(15, 23, 42, 0.7) !important;
            color: #C4B5FD !important;
            border-bottom: 1px solid rgba(167, 139, 250, 0.22) !important;
            font-size: 0.78rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.05em !important;
        }

        [data-bs-theme="light"] .warmup-section .exercise-table thead th,
        [data-bs-theme="light"] .post-workout-section .exercise-table thead th {
            background: rgba(243, 232, 255, 0.45) !important;
            color: #6D28D9 !important;
            border-bottom: 1px solid rgba(167, 139, 250, 0.28) !important;
        }

        /* Table Body Rows */
        .warmup-section .exercise-table tbody td,
        .post-workout-section .exercise-table tbody td {
            border-bottom: 1px solid rgba(167, 139, 250, 0.12) !important;
        }

        [data-bs-theme="light"] .warmup-section .exercise-table tbody td,
        [data-bs-theme="light"] .post-workout-section .exercise-table tbody td {
            border-bottom: 1px solid rgba(167, 139, 250, 0.15) !important;
        }

        .warmup-section .exercise-table tbody tr:hover,
        .post-workout-section .exercise-table tbody tr:hover {
            background: rgba(167, 139, 250, 0.06) !important;
        }

        /* Exercise Number Index Badge: Soft Lavender */
        .warmup-section .exercise-index,
        .post-workout-section .exercise-index {
            background: rgba(167, 139, 250, 0.18) !important;
            color: #A78BFA !important;
            border: 1px solid rgba(167, 139, 250, 0.35) !important;
            font-weight: 800 !important;
        }

        [data-bs-theme="light"] .warmup-section .exercise-index,
        [data-bs-theme="light"] .post-workout-section .exercise-index {
            background: rgba(167, 139, 250, 0.15) !important;
            color: #7C3AED !important;
            border-color: rgba(167, 139, 250, 0.35) !important;
        }

        /* Exercise Name in Warmup & Post-Workout */
        .warmup-section .exercise-name,
        .post-workout-section .exercise-name {
            color: #F1F5F9 !important;
            font-weight: 600 !important;
        }

        [data-bs-theme="light"] .warmup-section .exercise-name,
        [data-bs-theme="light"] .post-workout-section .exercise-name {
            color: #1E293B !important;
        }

        /* Rich Content & Text notes inside Warmup & Post-Workout */
        .warmup-section .warmup-rich-content,
        .post-workout-section .warmup-rich-content {
            color: #E2E8F0 !important;
        }

        [data-bs-theme="light"] .warmup-section .warmup-rich-content,
        [data-bs-theme="light"] .post-workout-section .warmup-rich-content {
            color: #334155 !important;
        }

        .section-label {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        /* Exercise Table */
        .exercise-table-wrapper {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--gp-border, #f1f1f2);
            background: var(--gp-bg-surface, #fff);
        }

        .exercise-table {
            margin-bottom: 0;
            background-color: transparent;
        }

        .exercise-table thead th {
            background: var(--gp-bg-elevated, #f9fafb);
            border-bottom: 2px solid var(--gp-border, #e9ecef);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gp-text-secondary, #7e8299);
            padding: 14px 16px;
            white-space: nowrap;
        }

        .exercise-table tbody tr {
            transition: all 0.2s ease;
            background-color: transparent;
        }

        .exercise-table tbody tr:hover {
            background: var(--gp-bg-elevated, rgba(0, 158, 247, 0.03));
        }

        .exercise-table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid var(--gp-border, #f5f5f5);
            font-size: 0.95rem;
            color: var(--gp-text-secondary, #475569);
        }

        .exercise-name {
            font-weight: 600;
            color: var(--gp-text-primary, #181c32);
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
            background: rgba(80, 205, 137, 0.12);
            color: #50cd89;
        }

        .badge-reps {
            background: rgba(0, 158, 247, 0.12);
            color: #009ef7;
        }

        .badge-rest {
            background: rgba(255, 168, 0, 0.12);
            color: #ffa800;
        }

        .badge-weight {
            background: rgba(114, 57, 234, 0.12);
            color: #7239ea;
        }

        .badge-tempo {
            background: rgba(245, 108, 108, 0.12);
            color: #f56c6c;
        }

        .badge-notes {
            background: rgba(255, 193, 7, 0.12);
            color: #b45309;
            white-space: normal;
            min-width: 140px;
            max-width: 260px;
            line-height: 1.4;
            font-size: 0.83rem;
            text-align: start;
            font-weight: 500;
            display: inline-flex;
            align-items: flex-start;
            gap: 6px;
            padding: 6px 10px;
            word-break: break-word;
        }

        .badge-notes i {
            color: #d97706;
            margin-top: 3px;
            font-size: 0.85rem;
            flex-shrink: 0;
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
            background: var(--gp-bg-surface, #1a1a2e);
            border: 1px solid var(--gp-border, rgba(255, 255, 255, 0.1));
            border-radius: 16px;
            overflow: hidden;
        }

        #videoModal .modal-header {
            border-bottom: 1px solid var(--gp-border, rgba(255, 255, 255, 0.08));
            padding: 16px 24px;
        }

        #videoModal .modal-title {
            color: var(--gp-text-primary, #fff);
            font-weight: 700;
            font-size: 1.1rem;
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
            background: var(--gp-bg-elevated, #f1f1f2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .empty-state-icon i {
            font-size: 3rem;
            color: var(--gp-text-muted, #b5b5c3);
        }

        .empty-state h3 {
            font-weight: 700;
            color: var(--gp-text-primary, #181c32);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--gp-text-secondary, #a1a5b7);
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

                        @php
                            $warmupExercises = $plan->warmupExercises;
                            $mainExercises = $plan->mainExercises;
                            $postWorkoutExercises = $plan->postWorkoutExercises;

                            // Fallback for legacy data where type was not set
                            if ($mainExercises->isEmpty() && $warmupExercises->isEmpty() && $postWorkoutExercises->isEmpty()) {
                                $mainExercises = $plan->exercises;
                            }
                        @endphp

                        {{-- 1. Warmup Routine --}}
                        @if($plan->display_warmup || !empty($plan->warmup_links_list) || $warmupExercises->count() > 0)
                            <div class="warmup-section mb-6">
                                <div class="section-label d-flex align-items-center gap-2 mb-3" style="color: #A78BFA;">
                                    <i class="fas fa-fire fs-4"></i>
                                    <span class="fs-5 fw-bold">{{ __('Warmup Routine') }}</span>
                                    @if($warmupExercises->count() > 0)
                                        <span class="badge fs-8 ms-1" style="background: rgba(167, 139, 250, 0.15); color: #A78BFA; border: 1px solid rgba(167, 139, 250, 0.25); border-radius: 9999px; padding: 4px 10px;">{{ $warmupExercises->count() }} {{ __('exercises') }}</span>
                                    @endif
                                </div>
                                @if($plan->display_warmup)
                                    <div class="warmup-rich-content text-gray-800 fw-semibold mb-3">{!! $plan->display_warmup !!}</div>
                                @endif
                                @if(!empty($plan->warmup_links_list))
                                    <div class="mb-3">
                                        @include('system.workout.partials.warmup-link-badges', ['links' => $plan->warmup_links_list, 'modal' => true, 'title' => $plan->day_name])
                                    </div>
                                @endif

                                @if($warmupExercises->count() > 0)
                                    <div class="exercise-table-wrapper mb-4">
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
                                                <th>{{ __('Note') }}</th>
                                                <th>{{ __('Media') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($warmupExercises as $wIdx => $exercise)
                                                <tr>
                                                    <td>
                                                        <span class="exercise-index">{{ $wIdx + 1 }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="exercise-name">{{ $exercise->name }}</span>
                                                    </td>
                                                    <td>
                                                        @if($exercise->sets)
                                                            <span class="badge-metric badge-sets">
                                                                <i class="fas fa-layer-group"></i> {{ $exercise->sets }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($exercise->reps)
                                                            <span class="badge-metric badge-reps">
                                                                <i class="fas fa-redo"></i> {{ $exercise->reps }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($exercise->rest)
                                                            <span class="badge-metric badge-rest">
                                                                <i class="fas fa-clock"></i> {{ $exercise->rest }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-1">
                                                            @if($exercise->internal_weight)
                                                                <span class="badge-metric badge-weight">
                                                                    <i class="fas fa-weight-hanging"></i> {{ $exercise->internal_weight_display }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                            <button type="button" 
                                                                    class="btn btn-icon btn-sm btn-light-primary btn-toggle-inline-load p-0 ms-1" 
                                                                    data-target="#inline-load-row-{{ $exercise->id }}" 
                                                                    title="{{ __('Update Weight') }}"
                                                                    style="width: 22px; height: 22px; border-radius: 6px; flex-shrink: 0;">
                                                                <i class="fas fa-plus fs-8"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($exercise->tempo)
                                                            <span class="badge-metric badge-tempo">
                                                                <i class="fas fa-tachometer-alt"></i> {{ $exercise->tempo }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($exercise->notes_en)
                                                            <span class="badge-metric badge-notes" title="{{ $exercise->notes_en }}">
                                                                <i class="fas fa-comment-dots"></i>
                                                                <span>{{ $exercise->notes_en }}</span>
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
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
                                                        </div>
                                                    </td>
                                                @include('system.workout.partials.trainee-load-adjustment-inline-row', ['exercise' => $exercise])
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- 2. Main Program --}}
                        <div class="main-program-section mb-6">
                            <div class="section-label text-warning d-flex align-items-center gap-2 mb-3">
                                <i class="fas fa-dumbbell fs-4"></i>
                                <span class="fs-5 fw-bold">{{ __('Main Program') }}</span>
                                @if($mainExercises->count() > 0)
                                    <span class="badge badge-light-primary fs-8 ms-1">{{ $mainExercises->count() }} {{ __('exercises') }}</span>
                                @endif
                            </div>

                            @if($mainExercises && count($mainExercises) > 0)
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
                                            <th>{{ __('Note') }}</th>
                                            <th>{{ __('Media') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($mainExercises as $exIndex => $exercise)
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
                                                <div class="d-flex align-items-center gap-1">
                                                    @if($exercise->internal_weight)
                                                        <span class="badge-metric badge-weight">
                                                            <i class="fas fa-weight-hanging"></i>
                                                            {{ $exercise->internal_weight_display }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                    <button type="button" 
                                                            class="btn btn-icon btn-sm btn-light-primary btn-toggle-inline-load p-0 ms-1" 
                                                            data-target="#inline-load-row-{{ $exercise->id }}" 
                                                            title="{{ __('Update Weight') }}"
                                                            style="width: 22px; height: 22px; border-radius: 6px; flex-shrink: 0;">
                                                        <i class="fas fa-plus fs-8"></i>
                                                    </button>
                                                </div>
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
                                                @if($exercise->notes_en)
                                                    <span class="badge-metric badge-notes" title="{{ $exercise->notes_en }}">
                                                        <i class="fas fa-comment-dots"></i>
                                                        <span>{{ $exercise->notes_en }}</span>
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($exercise->link)
                                                        <button type="button" class="video-link open-video-btn"
                                                                data-video-url="{{ $exercise->link }}"
                                                                data-exercise-name="{{ $exercise->name }}">
                                                            <i class="fab fa-youtube"></i>
                                                            {{ __('Watch') }}
                                                        </button>
                                                    @endif
                                                    @if($exercise->machine_image_url)
                                                        <button type="button" class="btn btn-sm btn-icon btn-light-warning mi-preview-btn p-0" style="width:34px;height:34px;border-radius:8px;overflow:hidden;display:inline-flex;" data-img-url="{{ $exercise->machine_image_url }}" data-title="{{ $exercise->name }}" title="{{ __('View Equipment Image') }}">
                                                            <img src="{{ $exercise->machine_image_url }}" style="width:100%;height:100%;object-fit:cover;">
                                                        </button>
                                                    @endif
                                                    @if(!$exercise->link && !$exercise->machine_image_url)
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @include('system.workout.partials.trainee-load-adjustment-inline-row', ['exercise' => $exercise])
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-muted">
                                <i class="fas fa-dumbbell fs-2 mb-3 d-block"></i>
                                {{ __('No main exercises added for this day yet') }}
                            </div>
                        @endif
                        </div>

                        {{-- 3. Post-Workout Routine --}}
                        @if($plan->display_post_workout || $postWorkoutExercises->count() > 0)
                            <div class="post-workout-section mb-6">
                                <div class="section-label d-flex align-items-center gap-2 mb-3" style="color: #A78BFA;">
                                    <i class="fas fa-spa fs-4"></i>
                                    <span class="fs-5 fw-bold">{{ __('Post-Workout Routine') }}</span>
                                    @if($postWorkoutExercises->count() > 0)
                                        <span class="badge fs-8 ms-1" style="background: rgba(167, 139, 250, 0.15); color: #A78BFA; border: 1px solid rgba(167, 139, 250, 0.25); border-radius: 9999px; padding: 4px 10px;">{{ $postWorkoutExercises->count() }} {{ __('exercises') }}</span>
                                    @endif
                                </div>
                                @if($plan->display_post_workout)
                                    <div class="warmup-rich-content text-gray-800 fw-semibold mb-3">{!! $plan->display_post_workout !!}</div>
                                @endif

                                @if($postWorkoutExercises->count() > 0)
                                    <div class="exercise-table-wrapper mb-4">
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
                                                <th>{{ __('Note') }}</th>
                                                <th>{{ __('Media') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($postWorkoutExercises as $pIdx => $exercise)
                                                <tr>
                                                    <td>
                                                        <span class="exercise-index">{{ $pIdx + 1 }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="exercise-name">{{ $exercise->name }}</span>
                                                    </td>
                                                    <td>
                                                        @if($exercise->sets)
                                                            <span class="badge-metric badge-sets">
                                                                <i class="fas fa-layer-group"></i> {{ $exercise->sets }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($exercise->reps)
                                                            <span class="badge-metric badge-reps">
                                                                <i class="fas fa-redo"></i> {{ $exercise->reps }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($exercise->rest)
                                                            <span class="badge-metric badge-rest">
                                                                <i class="fas fa-clock"></i> {{ $exercise->rest }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-1">
                                                            @if($exercise->internal_weight)
                                                                <span class="badge-metric badge-weight">
                                                                    <i class="fas fa-weight-hanging"></i> {{ $exercise->internal_weight_display }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                            <button type="button" 
                                                                    class="btn btn-icon btn-sm btn-light-primary btn-toggle-inline-load p-0 ms-1" 
                                                                    data-target="#inline-load-row-{{ $exercise->id }}" 
                                                                    title="{{ __('Update Weight') }}"
                                                                    style="width: 22px; height: 22px; border-radius: 6px; flex-shrink: 0;">
                                                                <i class="fas fa-plus fs-8"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($exercise->tempo)
                                                            <span class="badge-metric badge-tempo">
                                                                <i class="fas fa-tachometer-alt"></i> {{ $exercise->tempo }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($exercise->notes_en)
                                                            <span class="badge-metric badge-notes" title="{{ $exercise->notes_en }}">
                                                                <i class="fas fa-comment-dots"></i>
                                                                <span>{{ $exercise->notes_en }}</span>
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
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
                                                        </div>
                                                    </td>
                                                @include('system.workout.partials.trainee-load-adjustment-inline-row', ['exercise' => $exercise])
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- 4. Day Load Adjustment History & Feedback --}}
                        @include('system.workout.partials.trainee-load-adjustment-day-history', ['plan' => $plan])

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

            // ─── Inline Exercise Load Updates ───────────────────────────
            // Toggle inline load adjustment row
            $(document).on('click', '.btn-toggle-inline-load', function () {
                var targetSelector = $(this).data('target');
                var $row = $(targetSelector);
                if ($row.hasClass('d-none')) {
                    $('.inline-load-adjustment-row').addClass('d-none');
                    $row.removeClass('d-none');
                    var $firstChk = $row.find('.set-checkbox').first();
                    if ($firstChk.length && !$row.find('.set-checkbox:checked').length) {
                        $firstChk.prop('checked', true).trigger('change');
                    }
                } else {
                    $row.addClass('d-none');
                }
            });

            // Close inline form on cancel
            $(document).on('click', '.btn-close-inline-load', function () {
                var targetSelector = $(this).data('target');
                var $row = $(targetSelector);
                $row.addClass('d-none');
                $row.find('.inline-feedback-msg').addClass('d-none').empty();
            });

            // Toggle set input enable/disable when checkbox is toggled
            $(document).on('change', '.set-checkbox', function () {
                var $chk = $(this);
                var setNum = $chk.data('set');
                var $card = $chk.closest('.set-selection-card');
                var $input = $card.find('.set-weight-input');
                if ($chk.is(':checked')) {
                    $input.prop('disabled', false).focus();
                    $card.addClass('border-primary bg-light-primary');
                } else {
                    $input.prop('disabled', true).val('');
                    $card.removeClass('border-primary bg-light-primary');
                }
            });

            // Handle AJAX submission of inline load adjustment
            $(document).on('submit', '.inline-load-form', function (e) {
                e.preventDefault();
                var $form = $(this);
                var $row = $form.closest('tr');
                var $submitBtn = $form.find('button[type="submit"]');
                var $spinner = $submitBtn.find('.spinner-border');
                var $feedback = $form.find('.inline-feedback-msg');

                // Collect selected sets
                var sets = [];
                var missingWeight = false;
                $form.find('.set-checkbox:checked').each(function () {
                    var setNum = $(this).data('set');
                    var weight = $.trim($form.find('.set-weight-input[data-set="' + setNum + '"]').val());
                    if (!weight) {
                        missingWeight = true;
                    }
                    sets.push({
                        set_number: setNum,
                        suggested_weight: weight
                    });
                });

                if (missingWeight) {
                    $feedback.removeClass('d-none text-success')
                             .addClass('text-danger')
                             .text("{{ __('يرجى إدخال الوزن المطلوب لكل مجموعة تم تحديدها.') }}");
                    return;
                }

                var notes = $.trim($form.find('input[name="notes"]').val());
                var reportedLoad = $.trim($form.find('input[name="reported_load"]').val());

                if (sets.length === 0 && !reportedLoad && !notes) {
                    $feedback.removeClass('d-none text-success')
                             .addClass('text-danger')
                             .text("{{ __('يجب اختيار مجموعة وتحديد وزنها أو كتابة ملاحظة على الأقل.') }}");
                    return;
                }

                // Prevent duplicate submission
                $submitBtn.prop('disabled', true);
                $spinner.removeClass('d-none');
                $feedback.addClass('d-none').empty();

                var postData = {
                    _token: "{{ csrf_token() }}",
                    exercise_id: $form.find('input[name="exercise_id"]').val(),
                    notes: notes
                };

                if (sets.length > 0) {
                    postData.sets = sets;
                } else if (reportedLoad) {
                    postData.reported_load = reportedLoad;
                }

                $.ajax({
                    url: "{{ route('system.load-adjustment.store') }}",
                    method: "POST",
                    data: postData,
                    dataType: "json",
                    success: function (res) {
                        $spinner.addClass('d-none');
                        var msg = res.message || "{{ __('تم إرسال تحديث الوزن بنجاح للمدرب!') }}";
                        $feedback.removeClass('d-none text-danger')
                                 .addClass('text-success')
                                 .html('<i class="fas fa-check-circle me-1"></i> ' + msg);

                        if (typeof toastr !== 'undefined') {
                            toastr.success(msg);
                        }

                        $form.find('.set-checkbox').prop('checked', false).trigger('change');
                        $form.find('input[name="reported_load"]').val('');
                        $form.find('input[name="notes"]').val('');

                        setTimeout(function () {
                            $row.addClass('d-none');
                            $feedback.addClass('d-none').empty();
                            $submitBtn.prop('disabled', false);
                            window.location.reload();
                        }, 1200);
                    },
                    error: function (xhr) {
                        $spinner.addClass('d-none');
                        $submitBtn.prop('disabled', false);

                        var errMsg = "{{ __('حدث خطأ أثناء إرسال التحديث، يرجى المحاولة مرة أخرى.') }}";
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errMsg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                var errList = [];
                                $.each(xhr.responseJSON.errors, function (key, val) {
                                    errList.push(val[0]);
                                });
                                errMsg = errList.join(' ');
                            }
                        }

                        $feedback.removeClass('d-none text-success')
                                 .addClass('text-danger')
                                 .html('<i class="fas fa-exclamation-circle me-1"></i> ' + errMsg);

                        if (typeof toastr !== 'undefined') {
                            toastr.error(errMsg);
                        }
                    }
                });
            });
        });
    </script>
@endsection
