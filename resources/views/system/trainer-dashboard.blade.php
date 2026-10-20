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

                    <button type="button" class="stat-badge btn btn-sm border-0 text-info fw-bold" data-bs-toggle="modal" data-bs-target="#traineeHistoryModal">
                        <i class="fas fa-history me-1"></i>
                        <span>{{ __('My Load Reports') }}</span>
                    </button>
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
                        @if($plan->warmup || !empty($plan->warmup_links_list) || $warmupExercises->count() > 0)
                            <div class="warmup-section mb-6">
                                <div class="section-label d-flex align-items-center gap-2 mb-3" style="color: #A78BFA;">
                                    <i class="fas fa-fire fs-4"></i>
                                    <span class="fs-5 fw-bold">{{ __('Warmup Routine') }}</span>
                                    @if($warmupExercises->count() > 0)
                                        <span class="badge fs-8 ms-1" style="background: rgba(167, 139, 250, 0.15); color: #A78BFA; border: 1px solid rgba(167, 139, 250, 0.25); border-radius: 9999px; padding: 4px 10px;">{{ $warmupExercises->count() }} {{ __('exercises') }}</span>
                                    @endif
                                </div>
                                @if($plan->warmup)
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
                                                        <div class="d-flex align-items-center justify-content-between gap-1">
                                                            @if($exercise->internal_weight)
                                                                <span class="badge-metric badge-weight">
                                                                    <i class="fas fa-weight-hanging"></i> {{ $exercise->internal_weight_display }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-icon btn-light-primary btn-report-load p-0" 
                                                                    style="width: 26px; height: 26px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center;"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#loadAdjustmentModal"
                                                                    data-exercise-id="{{ $exercise->id }}"
                                                                    data-exercise-name="{{ $exercise->name }}"
                                                                    data-day-name="{{ $plan->day_name }}"
                                                                    data-prescribed-weight="{{ $exercise->internal_weight_display ?: __('No prescribed load') }}"
                                                                    title="{{ __('Report Load Adjustment') }}">
                                                                <i class="fas fa-sliders-h fs-8"></i>
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
                                                </tr>
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
                                                <div class="d-flex align-items-center justify-content-between gap-1">
                                                    @if($exercise->internal_weight)
                                                        <span class="badge-metric badge-weight">
                                                            <i class="fas fa-weight-hanging"></i>
                                                            {{ $exercise->internal_weight_display }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                    <button type="button" 
                                                            class="btn btn-sm btn-icon btn-light-primary btn-report-load p-0" 
                                                            style="width: 26px; height: 26px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center;"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#loadAdjustmentModal"
                                                            data-exercise-id="{{ $exercise->id }}"
                                                            data-exercise-name="{{ $exercise->name }}"
                                                            data-day-name="{{ $plan->day_name }}"
                                                            data-prescribed-weight="{{ $exercise->internal_weight_display ?: __('No prescribed load') }}"
                                                            title="{{ __('Report Load Adjustment') }}">
                                                        <i class="fas fa-sliders-h fs-8"></i>
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
                        @if($plan->post_workout || $postWorkoutExercises->count() > 0)
                            <div class="post-workout-section mb-6">
                                <div class="section-label d-flex align-items-center gap-2 mb-3" style="color: #A78BFA;">
                                    <i class="fas fa-spa fs-4"></i>
                                    <span class="fs-5 fw-bold">{{ __('Post-Workout Routine') }}</span>
                                    @if($postWorkoutExercises->count() > 0)
                                        <span class="badge fs-8 ms-1" style="background: rgba(167, 139, 250, 0.15); color: #A78BFA; border: 1px solid rgba(167, 139, 250, 0.25); border-radius: 9999px; padding: 4px 10px;">{{ $postWorkoutExercises->count() }} {{ __('exercises') }}</span>
                                    @endif
                                </div>

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
                                                        <div class="d-flex align-items-center justify-content-between gap-1">
                                                            @if($exercise->internal_weight)
                                                                <span class="badge-metric badge-weight">
                                                                    <i class="fas fa-weight-hanging"></i> {{ $exercise->internal_weight_display }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-icon btn-light-primary btn-report-load p-0" 
                                                                    style="width: 26px; height: 26px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center;"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#loadAdjustmentModal"
                                                                    data-exercise-id="{{ $exercise->id }}"
                                                                    data-exercise-name="{{ $exercise->name }}"
                                                                    data-day-name="{{ $plan->day_name }}"
                                                                    data-prescribed-weight="{{ $exercise->internal_weight_display ?: __('No prescribed load') }}"
                                                                    title="{{ __('Report Load Adjustment') }}">
                                                                <i class="fas fa-sliders-h fs-8"></i>
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
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                @if($plan->post_workout)
                                    <div class="warmup-rich-content text-gray-800 fw-semibold">{!! $plan->display_post_workout !!}</div>
                                @endif
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

    {{-- ===== TRAINEE LOAD ADJUSTMENT REPORT MODAL ===== --}}
    <div class="modal fade" id="loadAdjustmentModal" tabindex="-1" aria-labelledby="loadAdjustmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border shadow-lg" style="background: #1e1e2d; border-color: rgba(255,255,255,0.1) !important; color: #fff;">
                <div class="modal-header border-bottom py-4 px-6" style="border-color: rgba(255,255,255,0.08) !important; background: rgba(0,0,0,0.2);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-sliders-h text-primary fs-3"></i>
                        <h4 class="modal-title fw-bold text-white mb-0" id="loadAdjustmentModalLabel">
                            {{ __('Report Load Adjustment') }}
                        </h4>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="trainee-load-adjustment-form" method="POST" action="{{ route('system.load-adjustment.store') }}">
                    @csrf
                    <input type="hidden" name="exercise_id" id="adj_exercise_id" value="">

                    <div class="modal-body p-6">
                        {{-- Exercise & Day Context Banner --}}
                        <div class="p-3 rounded-3 mb-4" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                            <div class="fs-5 fw-bold text-white mb-1" id="adjExerciseName"></div>
                            <div class="fs-7 text-muted mb-2">
                                <i class="fas fa-calendar-day me-1"></i><span id="adjDayName"></span>
                            </div>
                            <div class="d-flex align-items-center gap-2 fs-7 pt-2 border-top border-secondary">
                                <span class="text-gray-400">{{ __('Coach Prescribed Load (Reference):') }}</span>
                                <span class="badge badge-light-warning fw-bold fs-7" id="adjPrescribedLoad"></span>
                            </div>
                        </div>

                        {{-- Input: Actual Load Used --}}
                        <div class="mb-4">
                            <label class="form-label required fw-bold text-white">
                                {{ __('Actual Load Used (Reported Load)') }}
                            </label>
                            <input type="text" 
                                   class="form-control form-control-solid bg-dark text-white border-secondary" 
                                   name="reported_load" 
                                   id="adjReportedLoad" 
                                   placeholder="{{ __('e.g. 25kg or 25kg / 30kg') }}" 
                                   required 
                                   maxlength="100">
                            <div class="form-text text-muted fs-8 mt-1">
                                <i class="fas fa-info-circle me-1"></i>
                                {{ __('Enter the actual weights you lifted. This report informs your coach and does NOT overwrite your workout plan.') }}
                            </div>
                        </div>

                        {{-- Input: Optional Notes --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-white">
                                {{ __('Notes / Feedback for Coach (Optional)') }}
                            </label>
                            <textarea class="form-control form-control-solid bg-dark text-white border-secondary" 
                                      name="notes" 
                                      id="adjNotes" 
                                      rows="2" 
                                      maxlength="1000" 
                                      placeholder="{{ __('e.g. Completed all reps easily, ready to progress next week.') }}"></textarea>
                        </div>

                        {{-- Toggle Previous Exercise History --}}
                        <div class="pt-2 border-top border-secondary">
                            <button type="button" class="btn btn-sm btn-link text-info p-0 text-decoration-none d-flex align-items-center justify-content-between w-100" id="btnToggleExerciseHistory">
                                <span><i class="fas fa-history me-1"></i>{{ __('View your previous reports for this exercise') }}</span>
                                <i class="fas fa-chevron-down fs-9" id="historyChevron"></i>
                            </button>
                            <div id="exerciseHistoryWrapper" class="mt-3" style="display: none;">
                                <div id="exerciseHistoryLoading" class="text-center py-3 text-muted fs-8" style="display: none;">
                                    <span class="spinner-border spinner-border-sm me-1"></span> {{ __('Loading history...') }}
                                </div>
                                <div id="exerciseHistoryList"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-3 px-6 d-flex justify-content-between" style="border-color: rgba(255,255,255,0.08) !important; background: rgba(0,0,0,0.2);">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" id="submitAdjustmentBtn" class="btn btn-primary fw-bold px-5">
                            <i class="fas fa-paper-plane me-1"></i>{{ __('Send to Coach') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== ALL MY LOAD REPORTS MODAL ===== --}}
    <div class="modal fade" id="traineeHistoryModal" tabindex="-1" aria-labelledby="traineeHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-4 border shadow-lg" style="background: #1e1e2d; border-color: rgba(255,255,255,0.1) !important; color: #fff;">
                <div class="modal-header border-bottom py-4 px-6" style="border-color: rgba(255,255,255,0.08) !important; background: rgba(0,0,0,0.2);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-history text-info fs-3"></i>
                        <h4 class="modal-title fw-bold text-white mb-0" id="traineeHistoryModalLabel">
                            {{ __('My Load Adjustment Reports History') }}
                        </h4>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-6">
                    <div id="allHistoryLoading" class="text-center py-8 text-muted">
                        <span class="spinner-border text-primary mb-2"></span>
                        <div>{{ __('Loading your reports history...') }}</div>
                    </div>
                    <div id="allHistoryContainer" style="display: none;"></div>
                </div>
                <div class="modal-footer border-top py-3 px-6" style="border-color: rgba(255,255,255,0.08) !important; background: rgba(0,0,0,0.2);">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
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

            // ===== LOAD ADJUSTMENT TRAINEE HANDLERS =====
            $(document).on('click', '.btn-report-load', function () {
                var btn = $(this);
                var exerciseId = btn.data('exercise-id');
                var exName = btn.data('exercise-name');
                var dayName = btn.data('day-name');
                var prescribedWeight = btn.data('prescribed-weight') || "{{ __('No prescribed load') }}";

                $('#adj_exercise_id').val(exerciseId);
                $('#adjExerciseName').text(exName);
                $('#adjDayName').text(dayName);
                $('#adjPrescribedLoad').text(prescribedWeight);
                $('#adjReportedLoad').val('');
                $('#adjNotes').val('');

                // Reset exercise history toggle
                $('#exerciseHistoryWrapper').hide();
                $('#historyChevron').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                $('#exerciseHistoryList').empty();
            });

            // Toggle history for specific exercise inside modal
            $('#btnToggleExerciseHistory').on('click', function () {
                var wrapper = $('#exerciseHistoryWrapper');
                var chevron = $('#historyChevron');
                var exerciseId = $('#adj_exercise_id').val();

                if (wrapper.is(':visible')) {
                    wrapper.slideUp();
                    chevron.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                } else {
                    wrapper.slideDown();
                    chevron.removeClass('fa-chevron-down').addClass('fa-chevron-up');

                    if ($('#exerciseHistoryList').is(':empty')) {
                        $('#exerciseHistoryLoading').show();
                        $.ajax({
                            url: "{{ route('system.load-adjustment.trainee-history') }}",
                            method: 'GET',
                            data: { exercise_id: exerciseId },
                            success: function (resp) {
                                $('#exerciseHistoryLoading').hide();
                                var reports = (resp.data && resp.data.reports) ? resp.data.reports : [];
                                if (reports.length === 0) {
                                    $('#exerciseHistoryList').html('<div class="text-muted fs-8 py-2 text-center">{{ __("No previous reports for this exercise.") }}</div>');
                                    return;
                                }

                                var html = '<div class="list-group list-group-flush border-0">';
                                reports.forEach(function (r) {
                                    var badgeClass = r.status === 'reviewed' ? 'badge-light-success' : (r.status === 'dismissed' ? 'badge-light-secondary' : 'badge-light-warning');
                                    var statusText = r.status.charAt(0).toUpperCase() + r.status.slice(1);
                                    var dateStr = r.created_at ? r.created_at.substring(0, 10) : '';

                                    html += '<div class="list-group-item bg-transparent text-white px-0 py-2 border-secondary d-flex justify-content-between align-items-center">';
                                    html += '<div>';
                                    html += '<span class="fw-bold text-primary">' + (r.reported_load || '') + '</span>';
                                    if (r.prescribed_load) {
                                        html += ' <span class="text-muted fs-9">({{ __("Ref:") }} ' + r.prescribed_load + ')</span>';
                                    }
                                    if (r.notes) {
                                        html += '<div class="text-muted fs-9 mt-1"><i class="fas fa-comment-dots me-1"></i>' + r.notes + '</div>';
                                    }
                                    html += '</div>';
                                    html += '<div class="text-end">';
                                    html += '<span class="badge ' + badgeClass + ' fs-9">' + statusText + '</span>';
                                    html += '<div class="text-muted fs-9 mt-1">' + dateStr + '</div>';
                                    html += '</div>';
                                    html += '</div>';
                                });
                                html += '</div>';
                                $('#exerciseHistoryList').html(html);
                            },
                            error: function () {
                                $('#exerciseHistoryLoading').hide();
                                $('#exerciseHistoryList').html('<div class="text-danger fs-8 py-2">{{ __("Could not load history.") }}</div>');
                            }
                        });
                    }
                }
            });

            // Submit Trainee Load Adjustment Form via AJAX
            $('#trainee-load-adjustment-form').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var btn = $('#submitAdjustmentBtn');
                btn.prop('disabled', true);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function (resp) {
                        btn.prop('disabled', false);
                        $('#loadAdjustmentModal').modal('hide');
                        var msg = resp.message || "{{ __('Load adjustment report submitted successfully to your coach!') }}";
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: "{{ __('Report Sent') }}",
                                text: msg
                            });
                        } else {
                            alert(msg);
                        }
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false);
                        var errMsg = "{{ __('Failed to submit report. Please check your input.') }}";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: "{{ __('Error') }}", text: errMsg });
                        } else {
                            alert(errMsg);
                        }
                    }
                });
            });

            // Modal: All Trainee Reports History
            $('#traineeHistoryModal').on('show.bs.modal', function () {
                $('#allHistoryLoading').show();
                $('#allHistoryContainer').hide().empty();

                $.ajax({
                    url: "{{ route('system.load-adjustment.trainee-history') }}",
                    method: 'GET',
                    success: function (resp) {
                        $('#allHistoryLoading').hide();
                        var reports = (resp.data && resp.data.reports) ? resp.data.reports : [];
                        if (reports.length === 0) {
                            $('#allHistoryContainer').html('<div class="text-center py-8 text-muted">{{ __("You have not submitted any load adjustment reports yet.") }}</div>').show();
                            return;
                        }

                        var html = '<div class="table-responsive">';
                        html += '<table class="table table-row-bordered align-middle text-white">';
                        html += '<thead class="text-gray-500 fs-7 text-uppercase fw-bold border-secondary"><tr>';
                        html += '<th>{{ __("Date") }}</th>';
                        html += '<th>{{ __("Exercise") }}</th>';
                        html += '<th>{{ __("Prescribed (Ref)") }}</th>';
                        html += '<th>{{ __("Your Load") }}</th>';
                        html += '<th>{{ __("Note") }}</th>';
                        html += '<th>{{ __("Status") }}</th>';
                        html += '</tr></thead><tbody class="fs-7">';

                        reports.forEach(function (r) {
                            var badgeClass = r.status === 'reviewed' ? 'badge-light-success' : (r.status === 'dismissed' ? 'badge-light-secondary' : 'badge-light-warning');
                            var statusText = r.status.charAt(0).toUpperCase() + r.status.slice(1);
                            var dateStr = r.created_at ? r.created_at.substring(0, 10) : '';

                            html += '<tr>';
                            html += '<td class="text-muted">' + dateStr + '</td>';
                            html += '<td class="fw-bold text-white">' + (r.exercise_name || '') + '<div class="text-muted fs-8">' + (r.workout_day_name || '') + '</div></td>';
                            html += '<td><span class="badge badge-light-warning fs-8">' + (r.prescribed_load || '—') + '</span></td>';
                            html += '<td><span class="badge badge-light-primary fw-bold fs-7">' + (r.reported_load || '') + '</span></td>';
                            html += '<td>' + (r.notes ? '<span title="' + r.notes + '"><i class="fas fa-comment-dots text-info me-1"></i>' + r.notes.substring(0, 30) + '...</span>' : '—') + '</td>';
                            html += '<td><span class="badge ' + badgeClass + '">' + statusText + '</span></td>';
                            html += '</tr>';
                        });

                        html += '</tbody></table></div>';
                        $('#allHistoryContainer').html(html).show();
                    },
                    error: function () {
                        $('#allHistoryLoading').hide();
                        $('#allHistoryContainer').html('<div class="text-danger py-4 text-center">{{ __("Could not load reports history.") }}</div>').show();
                    }
                });
            });
        });
    </script>
@endsection
