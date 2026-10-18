@extends('system.layout')

@section('header')
    @include('system.workout.partials.exercise-table-styles')
    @include('system.workout.partials.warmup-editor-assets')
    @include('system.workout.partials.workout-media-assets')
    <style>
        /* ==========================================================================
           GYM TRENDY UI/UX - TRAINEE PROFILE & WORKOUT EDITOR
           ========================================================================== */
        :root {
            --gym-neon-lime: #D97706;
            --gym-electric-blue: #009ef7;
            --gym-purple-glow: #7239ea;
            --gym-dark-card: var(--gp-bg-surface);
            --gym-dark-bg: var(--gp-bg-root);
        }
        [data-bs-theme="dark"] {
            --gym-neon-lime: #FACC15;
            --gym-dark-card: var(--gp-bg-surface);
            --gym-dark-bg: var(--gp-bg-root);
        }

        /* Profile Header Card */
        .trainee-profile-card {
            background: linear-gradient(135deg, #1e1e2d 0%, #2a2a3f 60%, #151521 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 2.25rem;
            color: #fff;
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }

        .trainee-profile-card::after {
            content: '';
            position: absolute;
            bottom: -30%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(0, 158, 247, 0.12) 0%, rgba(204, 255, 0, 0.06) 60%, transparent 80%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Stat Boxes */
        .trainee-stat-box {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.25rem 1rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            backdrop-filter: blur(10px);
        }

        .trainee-stat-box:hover {
            border-color: var(--gym-electric-blue);
            background: rgba(0, 158, 247, 0.08);
            transform: translateY(-4px);
            box-shadow: 0 12px 25px -8px rgba(0, 0, 0, 0.4);
        }

        .stat-box-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--gp-text-primary);
            letter-spacing: -0.5px;
        }

        .stat-box-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gp-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Neon & Status Badges */
        .badge-gym-neon {
            background: rgba(204, 255, 0, 0.15);
            color: var(--gym-neon-lime);
            border: 1px solid rgba(204, 255, 0, 0.3);
            border-radius: 10px;
            font-weight: 700;
            padding: 6px 14px;
        }

        .badge-archived {
            background: rgba(255, 168, 0, 0.12);
            color: #ffa800;
            border: 1px solid rgba(255, 168, 0, 0.25);
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.7rem;
            padding: 3px 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Modern Main Tabs */
        .gym-trendy-tabs {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            gap: 12px;
        }

        .gym-trendy-tabs .nav-link {
            border: none !important;
            border-radius: 14px !important;
            padding: 14px 24px !important;
            color: #7e8299;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .gym-trendy-tabs .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .gym-trendy-tabs .nav-link.active {
            background: rgba(0, 158, 247, 0.18) !important;
            color: var(--gym-electric-blue) !important;
            box-shadow: inset 0 0 0 1px rgba(0, 158, 247, 0.4), 0 8px 20px -6px rgba(0, 158, 247, 0.3);
        }

        /* Day Selector Pills */
        .nav-pills-custom .nav-link {
            background: var(--gp-bg-elevated);
            border: 1px solid var(--gp-border);
            border-radius: 16px !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            color: var(--gp-text-secondary);
        }

        .nav-pills-custom .nav-link:hover {
            border-color: rgba(204, 255, 0, 0.4);
            transform: translateY(-3px);
        }

        .nav-pills-custom .nav-link.active {
            background: linear-gradient(135deg, var(--gym-dark-card), #28283d) !important;
            border-color: var(--gym-neon-lime) !important;
            box-shadow: 0 10px 25px -8px rgba(204, 255, 0, 0.3);
        }

        .nav-pills-custom .nav-link.active .day-title-text {
            color: var(--gym-neon-lime) !important;
        }

        /* Exercise Form Row Card */
        .exercise-row-card {
            background: var(--gp-bg-surface);
            border: 1px solid var(--gp-border);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            transition: border-color 0.25s ease;
        }

        .exercise-row-card:hover {
            border-color: rgba(0, 158, 247, 0.4);
            background: rgba(255, 255, 255, 0.04);
        }

        /* Buttons & Actions */
        .btn-gym-neon {
            background-color: var(--gym-neon-lime) !important;
            color: #12121d !important;
            font-weight: 700 !important;
            border-radius: 12px !important;
            padding: 0.75rem 1.75rem !important;
            box-shadow: 0 6px 20px -5px rgba(204, 255, 0, 0.4);
            transition: all 0.25s ease !important;
        }

        .btn-gym-neon:hover {
            background-color: #b8e600 !important;
            transform: scale(1.03);
            box-shadow: 0 8px 25px -3px rgba(204, 255, 0, 0.6);
        }

        /* Archived History Section */
        .archived-history-card {
            background: var(--gp-bg-surface);
            border: 1px solid var(--gp-border);
            border-radius: 16px;
            overflow: hidden;
        }

        .archived-toggle-btn {
            background: transparent;
            border: none;
            color: var(--gp-text-primary);
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0;
            transition: color 0.2s ease;
            width: 100%;
            text-align: left;
        }

        .archived-toggle-btn:hover {
            color: var(--gp-accent);
        }

        .archived-toggle-btn .toggle-icon {
            transition: transform 0.3s ease;
        }

        .archived-toggle-btn[aria-expanded="true"] .toggle-icon {
            transform: rotate(180deg);
        }

        .archived-day-pill {
            background: var(--gp-bg-elevated);
            border: 1px solid var(--gp-border);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
        }

        .archived-exercise-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .archived-exercise-table thead th {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gp-text-muted);
            padding: 10px 12px;
            border-bottom: 1px solid var(--gp-border);
        }

        .archived-exercise-table tbody td {
            padding: 10px 12px;
            color: var(--gp-text-secondary);
            font-size: 0.9rem;
            border-bottom: 1px solid var(--gp-border);
        }

        .archived-exercise-table tbody tr:last-child td {
            border-bottom: none;
        }

        .archived-date-label {
            font-size: 0.75rem;
            color: var(--gp-text-muted);
            font-style: italic;
        }

        /* Inactive Day & Tab Badging */
        .wb-tab-pill--inactive {
            opacity: 0.72;
            border: 2px dashed rgba(245, 158, 11, 0.45) !important;
        }
        .wb-tab-pill--inactive:hover {
            opacity: 0.95;
        }
        .wb-day--inactive {
            border-color: rgba(245, 158, 11, 0.4) !important;
            background: rgba(30, 41, 59, 0.5) !important;
        }
        .wb-day--inactive .wb-day__badge {
            background: rgba(245, 158, 11, 0.15) !important;
            color: #f59e0b !important;
            border-color: rgba(245, 158, 11, 0.4) !important;
        }

        /* Archived Program Version Card */
        .archived-program-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .archived-program-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35) !important;
        }
        .transition-transform {
            transition: transform 0.25s ease;
        }
        [aria-expanded="true"] .transition-transform {
            transform: rotate(180deg);
        }

        /* ===== CKEditor Inner HTML Content Adaptation ===== */
        .nutrition-content {
            line-height: 1.8;
            font-size: 1rem;
        }
        .nutrition-content p { color: inherit; margin-bottom: 0.85rem; }
        .nutrition-content h1, .nutrition-content h2, .nutrition-content h3, .nutrition-content h4, .nutrition-content h5 {
            color: var(--bs-gray-900, #181c32);
            font-weight: 700;
            margin-top: 1.25rem;
            margin-bottom: 0.75rem;
        }
        [data-bs-theme="dark"] .nutrition-content h1,
        [data-bs-theme="dark"] .nutrition-content h2,
        [data-bs-theme="dark"] .nutrition-content h3,
        [data-bs-theme="dark"] .nutrition-content h4,
        [data-bs-theme="dark"] .nutrition-content h5 {
            color: #ffffff !important;
        }
        .nutrition-content ul, .nutrition-content ol { padding-left: 1.5rem; margin-bottom: 1rem; }
        .nutrition-content li { margin-bottom: 0.35rem; }
        .nutrition-content table { width: 100%; margin-bottom: 1.25rem; border-collapse: collapse; }
        .nutrition-content th, .nutrition-content td { padding: 0.75rem 1rem; border: 1px solid var(--bs-gray-300, #e4e6ef); }
        [data-bs-theme="dark"] .nutrition-content th,
        [data-bs-theme="dark"] .nutrition-content td { border-color: rgba(255, 255, 255, 0.1) !important; }
        .nutrition-content th { background: var(--bs-gray-100, #f8f9fa); font-weight: 700; }
        [data-bs-theme="dark"] .nutrition-content th { background: rgba(255, 255, 255, 0.05) !important; }
        .nutrition-content blockquote {
            border-left: 4px solid #50cd89;
            padding: 0.75rem 1.25rem;
            background: var(--bs-gray-100, #f8f9fa);
            margin-bottom: 1rem;
            border-radius: 0 8px 8px 0;
        }
        [data-bs-theme="dark"] .nutrition-content blockquote { background: rgba(255, 255, 255, 0.04) !important; }
    </style>
@endsection

@section('links')

    <div class="d-flex align-items-center gap-3">
        @if(userCan('system.trainee.edit'))
            <span class="d-inline-block">
                {{ edit_links('system.trainee.edit', route('system.trainee.edit', $result->user_id)) }}
            </span>
        @endif

        @if(userCan('system.nutrition.create'))
            <a href="{{ route('system.nutrition.create', ['trainee' => Crypt::encrypt($result->id)]) }}"
               class="btn btn-sm btn-light-success fw-bold rounded-3 d-flex align-items-center gap-2">
                <i class="fa-solid fa-apple-whole fs-4"></i>
                <span>{{ __('Add Nutrition Plan') }}</span>
            </a>
        @endif

{{--        @if(userCan('system.trainee.impersonate'))--}}
{{--            <a href="{{ route('system.trainee.impersonate', $result->id) }}"--}}
{{--               class="btn btn-sm btn-light-info fw-bold rounded-3 d-flex align-items-center gap-2"--}}
{{--               title="{{ __('View portal from trainee perspective') }}">--}}
{{--                <i class="ki-duotone ki-eye fs-4 text-info">--}}
{{--                    <span class="path1"></span>--}}
{{--                    <span class="path2"></span>--}}
{{--                    <span class="path3"></span>--}}
{{--                </i>--}}
{{--                <span>{{ __('View as Trainee') }}</span>--}}
{{--            </a>--}}
{{--        @endif--}}

        @if(userCan('system.trainee.reset-plan'))
            @if($result->currentWorkoutPlans->count() > 0)
                <form action="{{ route('system.trainee.reset-plan', $result->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('{{ __('Are you sure you want to archive the current workout plan for this trainee? This action cannot be undone.') }}')">
                    @csrf
                    <button type="submit"
                            class="btn btn-sm btn-light-danger fw-bold rounded-3 d-flex align-items-center gap-1">
                        <i class="ki-duotone ki-trash fs-4"></i>
                        <span>{{ __('Archive Workout Plan') }}</span>
                    </button>
                </form>
            @endif
        @endif

        @if($result->archivedWorkoutPlans->count() > 0)
            @php
                $restoreTopConfirmMsg = $result->currentWorkoutPlans->count() > 0
                    ? __('The trainee currently has an active workout plan. Restoring this archived program will archive the current active plan and replace it. Are you sure you want to proceed?')
                    : __('Are you sure you want to restore this archived workout program?');
                $latestBatch = $result->archived_program_batches->first();
            @endphp
            <form action="{{ route('system.trainee.restore-plan', $result->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ addslashes($restoreTopConfirmMsg) }}')">
                @csrf
                @if($latestBatch && !empty($latestBatch['plan_ids_str']))
                    <input type="hidden" name="plan_ids" value="{{ $latestBatch['plan_ids_str'] }}">
                @endif
                <button type="submit" class="btn btn-sm btn-light-warning fw-bold rounded-3 d-flex align-items-center gap-1">
                    <i class="ki-duotone ki-arrows-loop fs-4"></i>
                    <span>{{ __('Restore Latest Program') }}</span>
                </button>
            </form>
        @endif
    </div>
@endsection

@section('content')
    <div id="form-alert-message"></div>

    <div class="trainee-profile-card mb-8">
        <div class="d-flex flex-wrap flex-sm-nowrap align-items-center gap-6 mb-6">
            <!-- Avatar -->
            <div class="symbol symbol-100px symbol-fixed position-relative flex-shrink-0">
                <img class="rounded-4" src="{{ asset('assets/media/avatars/blank.png') }}" style="border: 2px solid var(--gym-neon-lime); width: 100px; height: 100px; object-fit: contain;" alt="avatar" />

                <div class="position-absolute bottom-0 end-0 rounded-circle border border-dark w-20px h-20px
                 {{ $result->status == 'active' ? 'bg-success' : ($result->status == 'inactive' ? 'bg-danger' : 'bg-warning') }}"
                     title="{{ ucfirst($result->status) }}">
                </div>

            </div>

            <!-- Details -->
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fs-1 fw-bold" style="color: var(--gp-text-primary);">{{ optional($result->user)->name ?? '' }}</span>
                        <span class="badge
                            {{ $result->status == 'active' ? 'badge-gym-neon' :
                                ($result->status == 'inactive' ? 'badge-light-danger' : 'badge-light-warning')
                            }}">
                            {{ $result->status == 'active' ? __('Active Trainee'):
                                ($result->status == 'inactive' ? __('In-Active') : __('Expired')) }}</span>

{{--                        <a href="{{ route('system.trainee.impersonate', $result->id) }}" class="btn btn-sm btn-outline btn-outline-info btn-active-light-info fw-bold rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" title="{{ __('View portal from trainee perspective') }}">--}}
{{--                            <i class="ki-duotone ki-eye fs-6 text-info">--}}
{{--                                <span class="path1"></span>--}}
{{--                                <span class="path2"></span>--}}
{{--                                <span class="path3"></span>--}}
{{--                            </i>--}}
{{--                            <span class="fs-8">{{ __('View as Trainee') }}</span>--}}
{{--                        </a>--}}
                    </div>
                </div>

                <div class="d-flex flex-wrap fw-semibold fs-6 text-gray-400 mb-6 gap-6">
                    @if(!empty($result->user->email))
                        <div class="d-flex align-items-center gap-2">
                            <i class="ki-duotone ki-sms fs-4 text-gray-500"></i>
                            <span>{{ $result->user->email }}</span>
                        </div>
                    @endif
                    @if(!empty($result->user->mobile))
                        <div class="d-flex align-items-center gap-2">
                            <i class="ki-duotone ki-phone fs-4 text-gray-500"></i>
                            <span style="direction: ltr; display: inline-block;">{{ $result->user->mobile }}</span>
                        </div>
                    @endif
                </div>

                <!-- Stats Grid -->
                <div class="row g-3">
                    <!-- Age -->
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div class="trainee-stat-box text-center">
                            <div class="stat-box-label mb-1">{{ __('Age') }}</div>
                            <div class="stat-box-value">{{ $result->age ?? '-' }} <span class="fs-8 text-gray-500">{{ __('Yrs') }}</span></div>
                        </div>
                    </div>
                    <!-- Weight -->
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div class="trainee-stat-box text-center">
                            <div class="stat-box-label mb-1">{{ __('Weight') }}</div>
                            <div class="stat-box-value text-primary">{{ $result->weight ?? '-' }} <span class="fs-8 text-muted">KG</span></div>
                        </div>
                    </div>
                    <!-- Height -->
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div class="trainee-stat-box text-center">
                            <div class="stat-box-label mb-1">{{ __('Height') }}</div>
                            <div class="stat-box-value">{{ $result->height ?? '-' }} <span class="fs-8 text-gray-500">CM</span></div>
                        </div>
                    </div>
                    <!-- Level -->
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div class="trainee-stat-box text-center">
                            <div class="stat-box-label mb-1">{{ __('Level') }}</div>
                            <div class="stat-box-value text-info fs-4">{{ ucfirst($result->training_level ?? '-') }}</div>
                        </div>
                    </div>
                    <!-- Member Since -->
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div class="trainee-stat-box text-center">
                            <div class="stat-box-label mb-1">{{ __('Member Since') }}</div>
                            <div class="stat-box-value fs-5 text-gray-800">{{ $result->membership_start ?? '-' }}</div>
                        </div>
                    </div>
                    <!-- Ends -->
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div class="trainee-stat-box text-center">
                            <div class="stat-box-label mb-1">{{ __('Ends') }}</div>
                            <div class="stat-box-value fs-5 {{ (strtotime($result->membership_end) < time()) ? 'text-danger' : 'text-success' }}">
                                {{ $result->membership_end ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation Tabs -->
        <ul class="nav nav-pills gym-trendy-tabs mt-6 pt-4">
            <li class="nav-item">
                <a class="nav-link active d-flex align-items-center gap-2" data-bs-toggle="tab" href="#kt_staff_programs_tab">
                    <span>⚡</span>
                    <span>{{ __('Workout Program') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#kt_staff_nutrition_tab">
                    <span>🥗</span>
                    <span>{{ __('Nutrition Plan') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#kt_progress_photos_tab">
                    <span>📸</span>
                    <span>{{ __('Progress & Photos') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#kt_staff_logs_tab">
                    <span>📊</span>
                    <span>{{ __('History (Audit Log)') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#kt_staff_auth_sessions_tab">
                    <span>🔒</span>
                    <span>{{ __('Auth Sessions') }}</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content" id="myTabContent">

        <!-- WORKOUT PROGRAM TAB -->
        <div class="tab-pane fade show active wb-scope" id="kt_staff_programs_tab" role="tabpanel">
            {{-- ===== CURRENT WORKOUT PLANS (ACTIVE & INACTIVE) ===== --}}
            @if($result->currentWorkoutPlans->count() > 0)
                <div class="card mb-8 rounded-4 shadow-sm border" style="background: var(--wb-surface-solid); border-color: var(--wb-row-border) !important;">
                    <div class="card-header border-bottom pt-6 pb-5" style="border-color: var(--wb-row-border) !important; background: var(--wb-head-bg);">
                        <div class="card-title">
                            <h2 class="fw-bold m-0 d-flex align-items-center gap-2" style="color: var(--wb-text);">
                                <i class="fas fa-dumbbell" style="color: var(--wb-accent);"></i>
                                <span>{{ __('Current Workout Program') }}</span>
                                <span class="badge badge-light-success fs-8 ms-2">{{ __('Active') }}</span>
                            </h2>
                        </div>
                    </div>
                    <div class="card-body p-6 p-lg-8" style="background: transparent;">
                        <!-- Days Nav Tabs -->
                        <ul class="nav nav-pills nav-pills-custom mb-8 d-flex flex-wrap gap-4 align-items-stretch" id="pills-tab" role="tablist">
                            @foreach($result->currentWorkoutPlans as $index => $plan)
                                @php
                                    $isDayInactive = ($plan->status === 'inactive');
                                @endphp
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link d-flex flex-column flex-center p-5 text-center {{ $index == 0 ? 'active' : '' }} {{ $isDayInactive ? 'wb-tab-pill--inactive' : '' }}"
                                       id="tab_pill_{{ $plan->id }}"
                                       data-bs-toggle="pill"
                                       href="#plan_day_{{ $plan->id }}"
                                       style="min-width: 140px; min-height: 110px;">
                                        <div class="nav-icon mb-2">
                                            <i class="fas fa-calendar-day fs-2x" style="color: var(--wb-accent);"></i>
                                        </div>
                                        <div>
                                            <span class="day-title-text fw-bold fs-5 d-block">{{ $plan->day_name ?? '' }}</span>
                                            <span class="badge badge-sm mt-1 day-status-badge-{{ $plan->id }} {{ $isDayInactive ? 'badge-light-warning text-warning' : 'badge-light-success text-success' }}" style="font-size: 0.72rem;">
                                                {{ $isDayInactive ? __('Inactive') : __('Active') }}
                                            </span>
                                        </div>
                                    </a>
                                </li>
                            @endforeach

                            <li class="nav-item d-flex align-items-stretch" role="presentation">
                                <button type="button" class="btn btn-outline btn-outline-dashed d-flex flex-column flex-center p-4 text-center rounded-3 hover-elevate-up"
                                        data-bs-toggle="modal" data-bs-target="#inlineAddDayModal"
                                        style="min-width: 130px; min-height: 110px; border-width: 2px; border-color: var(--wb-accent) !important; color: var(--wb-accent) !important; background: var(--wb-accent-soft);">
                                    <i class="fas fa-plus-circle fs-1 mb-2" style="color: var(--wb-accent);"></i>
                                    <span class="fw-bolder fs-6" style="color: var(--wb-text);">{{ __('+ Add Day') }}</span>
                                </button>
                            </li>
                        </ul>

                        <!-- Days Content -->
                        <div class="tab-content" id="pills-tabContent">
                            @foreach($result->currentWorkoutPlans as $index => $plan)
                                @php
                                    $isDayInactive = ($plan->status === 'inactive');
                                @endphp
                                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="plan_day_{{ $plan->id }}" role="tabpanel">

                                    {!! Form::open(['url' => route('system.workout.updateDay', $plan->id), 'method' => 'POST', 'class' => 'workout-day-form', 'files' => true, 'enctype' => 'multipart/form-data']) !!}

                                    <div class="wb-day {{ $isDayInactive ? 'wb-day--inactive' : '' }}" id="wb_day_card_{{ $plan->id }}">
                                        <div class="wb-day__header d-flex align-items-center justify-content-between flex-wrap gap-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="wb-day__badge">D{{ $index + 1 }}</span>
                                                <div>
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <h2 class="wb-day__title m-0">{{ $plan->day_name ?? '' }}</h2>
                                                        <span class="badge {{ $isDayInactive ? 'badge-light-warning text-warning' : 'badge-light-success text-success' }} fs-8 day-header-badge-{{ $plan->id }}">
                                                            {{ $isDayInactive ? __('Inactive') : __('Active') }}
                                                        </span>
                                                    </div>
                                                    <p class="wb-day__subtitle mb-0">@lang('Exercises list and training instructions')</p>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                @if(userCan('system.workout.toggleDayStatus'))
                                                    <button type="button"
                                                            class="btn btn-sm {{ $isDayInactive ? 'btn-light-success text-success' : 'btn-light-warning text-warning' }} fw-bold d-flex align-items-center gap-1 toggle-day-status-btn"
                                                            data-day-id="{{ $plan->id }}"
                                                            data-status="{{ $plan->status ?? 'active' }}"
                                                            data-url="{{ route('system.workout.toggleDayStatus', $plan->id) }}">
                                                        <i class="fas {{ $isDayInactive ? 'fa-play' : 'fa-pause' }} fs-7"></i>
                                                        <span
                                                            class="toggle-status-text">{{ $isDayInactive ? __('Activate Day') : __('Deactivate Day') }}</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mb-6">
                                            <label class="form-label" style="color: var(--wb-text-secondary, #cbd5e1); font-weight: 600;">@lang('Day Name')</label>
                                            <input type="text" name="day_name" value="{{ $plan->day_name }}" class="form-control form-control-solid" maxlength="100"
                                                   placeholder="e.g. Day {{ $index + 1 }}: Chest & Triceps"/>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label wb-field-label" style="color: var(--wb-text-secondary, #cbd5e1); font-weight: 600;"><i class="fas fa-fire text-warning me-1"></i>@lang('Warmup Instructions & Video Links')</label>
                                            <div class="warmup-quill-wrapper">
                                                <div id="warmup_editor_day_{{ $plan->id }}" class="warmup-quill-editor" data-placeholder="@lang('Warm up instructions, stretch routine, embed video links...')">{!! $plan->warmup !!}</div>
                                                <input type="hidden" name="warmup" id="warmup_input_day_{{ $plan->id }}" class="warmup-hidden-input" value="{{ $plan->warmup }}">
                                            </div>
                                        </div>

                                        <section class="wb-exercises" aria-label="@lang('Exercises')">
                                            <header class="wb-exercises__header">
                                                <div class="wb-exercises__title">
                                                    <span class="wb-exercises__icon"><i class="fas fa-dumbbell"></i></span>
                                                    <div>
                                                        <h3>@lang('Exercises')</h3>
                                                        <p>@lang('Sets, reps, rest, tempo and up to 4 target weights')</p>
                                                    </div>
                                                </div>
                                                <span class="wb-count"><span class="wb-count__num" id="exercise-count-day-{{ $plan->id }}">{{ $plan->exercises->count() > 0 ? $plan->exercises->count() : 1 }}</span> @lang('Exercises')</span>
                                            </header>

                                            <div class="wb-table-wrap">
                                                <table class="wb-table" id="exercises-container-day-{{ $plan->id }}" data-exercise-index="{{ $plan->exercises->count() > 0 ? $plan->exercises->count() : 1 }}">
                                                    <colgroup>
                                                        <col class="wb-col-idx">
                                                        <col class="wb-col-name">
                                                        <col class="wb-col-num">
                                                        <col class="wb-col-num">
                                                        <col class="wb-col-num">
                                                        <col class="wb-col-tempo">
                                                        <col class="wb-col-weights">
                                                        <col class="wb-col-action">
                                                    </colgroup>
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">@lang('Exercise')</th>
                                                            <th scope="col" class="text-center">@lang('Sets')</th>
                                                            <th scope="col" class="text-center">@lang('Reps')</th>
                                                            <th scope="col" class="text-center">@lang('Rest')</th>
                                                            <th scope="col" class="text-center">@lang('Tempo')</th>
                                                            <th scope="col">@lang('Weights')<span class="wb-th-hint">max 4</span></th>
                                                            <th scope="col"><span class="visually-hidden">@lang('Actions')</span></th>
                                                        </tr>
                                                    </thead>
                                                    @forelse($plan->exercises as $exIndex => $exercise)
                                                        @php $currentExIdx = $exIndex + 1; @endphp
                                                        <tbody class="exercise-row wb-row-enter">
                                                        <tr class="wb-row-main">
                                                            <td class="wb-cell-idx" rowspan="3">
                                                                <input type="hidden"
                                                                       name="exercises[ex_{{ $currentExIdx }}][exercise_id]"
                                                                       value="{{ $exercise->id }}">
                                                                <span class="wb-idx"
                                                                                                      aria-hidden="true">{{ $currentExIdx }}</span>
                                                            </td>
                                                            <td class="wb-cell-name" data-label="@lang('Exercise')">
                                                                <input type="text"
                                                                       name="exercises[ex_{{ $currentExIdx }}][name]"
                                                                       value="{{ $exercise->name }}"
                                                                       class="form-control wb-name" maxlength="150"
                                                                       autocomplete="off"
                                                                       placeholder="@lang('e.g. Incline Bench Press')"
                                                                       aria-label="@lang('Exercise Name')">
                                                                <div class="invalid-feedback"></div>
                                                            </td>
                                                            <td data-label="@lang('Sets')">
                                                                <input type="text"
                                                                       name="exercises[ex_{{ $currentExIdx }}][sets]"
                                                                       value="{{ $exercise->sets }}"
                                                                       class="form-control wb-num" maxlength="50"
                                                                       inputmode="numeric"
                                                                       placeholder="4" aria-label="@lang('Sets')">
                                                            </td>
                                                            <td data-label="@lang('Reps')">
                                                                <input type="text"
                                                                       name="exercises[ex_{{ $currentExIdx }}][reps]"
                                                                       value="{{ $exercise->reps }}"
                                                                       class="form-control wb-num" maxlength="50"
                                                                       placeholder="8-12" aria-label="@lang('Reps')">
                                                            </td>
                                                            <td data-label="@lang('Rest')">
                                                                <input type="text"
                                                                       name="exercises[ex_{{ $currentExIdx }}][rest]"
                                                                       value="{{ $exercise->rest }}"
                                                                       class="form-control wb-num" maxlength="50"
                                                                       placeholder="90s" aria-label="@lang('Rest')">
                                                            </td>
                                                            <td data-label="@lang('Tempo')">
                                                                <input type="text"
                                                                       name="exercises[ex_{{ $currentExIdx }}][tempo]"
                                                                       value="{{ $exercise->tempo }}"
                                                                       class="form-control wb-num" maxlength="50"
                                                                       placeholder="3-0-1" aria-label="@lang('Tempo')">
                                                            </td>
                                                            <td class="wb-cell-weights"
                                                                data-label="@lang('Weights') (max 4)">
                                                                @include('system.workout.partials.weight-inputs', [
                                                                    'name' => "exercises[ex_{$currentExIdx}][internal_weight]",
                                                                    'weights' => $exercise->internal_weight
                                                                ])
                                                            </td>
                                                            <td class="wb-cell-action" rowspan="3">
                                                                <button type="button"
                                                                        class="wb-delete delete-exercise-btn"
                                                                        title="@lang('Delete Exercise')"
                                                                        aria-label="@lang('Delete Exercise')">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr class="wb-row-detail wb-row-media">
                                                            <td colspan="3" data-label="@lang('Video Link')">
                                                                <span class="wb-mini-label">@lang('Video Link')</span>
                                                                @php $hasExLink = !empty(trim((string)$exercise->link)); @endphp
                                                                <div class="wb-video-link-wrap">
                                                                    <div class="wb-input-icon">
                                                                        <i class="fab fa-youtube"></i>
                                                                        <input type="url"
                                                                               name="exercises[ex_{{ $currentExIdx }}][link]"
                                                                               value="{{ $exercise->link }}"
                                                                               class="form-control wb-video-input" maxlength="255"
                                                                               placeholder="https://video link.com/..."
                                                                               aria-label="@lang('Video Link')">
                                                                    </div>
                                                                    <button type="button" class="wb-video-preview-btn {{ $hasExLink ? '' : 'is-disabled' }}"
                                                                            title="@lang('Preview / Open Video')"
                                                                            aria-label="@lang('Preview / Open Video')"
                                                                            @if(!$hasExLink) aria-disabled="true" @endif>
                                                                        <i class="fas fa-external-link-alt"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                            <td colspan="3"
                                                                data-label="@lang('Machine / Equipment Photo')">
                                                                <span
                                                                    class="wb-mini-label">@lang('Machine Photo')</span>
                                                                @include('system.workout.partials.machine-image-input', [
                                                                    'name' => "exercises[ex_{$currentExIdx}]",
                                                                    'url' => $exercise->machine_image_url,
                                                                    'alt' => $exercise->name
                                                                ])
                                                            </td>
                                                        </tr>
                                                        <tr class="wb-row-detail wb-row-notes">
                                                            <td colspan="6" data-label="@lang('Coach Notes')">
                                                                <span class="wb-mini-label">@lang('Coach Notes')</span>
                                                                <textarea
                                                                    name="exercises[ex_{{ $currentExIdx }}][notes_en]"
                                                                    rows="1" class="form-control wb-notes"
                                                                    placeholder="@lang('Form cues, progression, tips...')"
                                                                    aria-label="@lang('Coach Notes')">{{ $exercise->notes_en }}</textarea>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    @empty
                                                        <tbody class="exercise-row wb-row-enter">
                                                        <tr class="wb-row-main">
                                                            <td class="wb-cell-idx" rowspan="3"><span class="wb-idx"
                                                                                                      aria-hidden="true"></span>
                                                            </td>
                                                            <td class="wb-cell-name" data-label="@lang('Exercise')">
                                                                <input type="text" name="exercises[ex_1][name]"
                                                                       class="form-control wb-name" maxlength="150"
                                                                       autocomplete="off"
                                                                       placeholder="@lang('e.g. Incline Bench Press')"
                                                                       aria-label="@lang('Exercise Name')">
                                                                <div class="invalid-feedback"></div>
                                                            </td>
                                                            <td data-label="@lang('Sets')">
                                                                <input type="text" name="exercises[ex_1][sets]"
                                                                       class="form-control wb-num" maxlength="50"
                                                                       inputmode="numeric"
                                                                       placeholder="4" aria-label="@lang('Sets')">
                                                            </td>
                                                            <td data-label="@lang('Reps')">
                                                                <input type="text" name="exercises[ex_1][reps]"
                                                                       class="form-control wb-num" maxlength="50"
                                                                       placeholder="8-12" aria-label="@lang('Reps')">
                                                            </td>
                                                            <td data-label="@lang('Rest')">
                                                                <input type="text" name="exercises[ex_1][rest]"
                                                                       class="form-control wb-num" maxlength="50"
                                                                       placeholder="90s" aria-label="@lang('Rest')">
                                                            </td>
                                                            <td data-label="@lang('Tempo')">
                                                                <input type="text" name="exercises[ex_1][tempo]"
                                                                       class="form-control wb-num" maxlength="50"
                                                                       placeholder="3-0-1" aria-label="@lang('Tempo')">
                                                            </td>
                                                            <td class="wb-cell-weights"
                                                                data-label="@lang('Weights') (max 4)">
                                                                @include('system.workout.partials.weight-inputs', [
                                                                    'name' => "exercises[ex_1][internal_weight]",
                                                                    'weights' => []
                                                                ])
                                                            </td>
                                                            <td class="wb-cell-action" rowspan="3">
                                                                <button type="button"
                                                                        class="wb-delete delete-exercise-btn"
                                                                        title="@lang('Delete Exercise')"
                                                                        aria-label="@lang('Delete Exercise')">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr class="wb-row-detail wb-row-media">
                                                            <td colspan="3" data-label="@lang('Video Link')">
                                                                <span class="wb-mini-label">@lang('Video Link')</span>
                                                                <div class="wb-video-link-wrap">
                                                                    <div class="wb-input-icon">
                                                                        <i class="fab fa-youtube"></i>
                                                                        <input type="url" name="exercises[ex_1][link]"
                                                                               class="form-control wb-video-input" maxlength="255"
                                                                               placeholder="https://video link.com/..."
                                                                               aria-label="@lang('Video Link')">
                                                                    </div>
                                                                    <button type="button" class="wb-video-preview-btn is-disabled"
                                                                            title="@lang('Preview / Open Video')"
                                                                            aria-label="@lang('Preview / Open Video')"
                                                                            aria-disabled="true">
                                                                        <i class="fas fa-external-link-alt"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                            <td colspan="3"
                                                                data-label="@lang('Machine / Equipment Photo')">
                                                                <span
                                                                    class="wb-mini-label">@lang('Machine Photo')</span>
                                                                @include('system.workout.partials.machine-image-input', [
                                                                    'name' => 'exercises[ex_1]',
                                                                    'url' => null,
                                                                    'alt' => ''
                                                                ])
                                                            </td>
                                                        </tr>
                                                        <tr class="wb-row-detail wb-row-notes">
                                                            <td colspan="6" data-label="@lang('Coach Notes')">
                                                                <span class="wb-mini-label">@lang('Coach Notes')</span>
                                                                <textarea name="exercises[ex_1][notes_en]" rows="1"
                                                                          class="form-control wb-notes"
                                                                          placeholder="@lang('Form cues, progression, tips...')"
                                                                          aria-label="@lang('Coach Notes')"></textarea>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    @endforelse
                                                </table>
                                            </div>

                                            <div class="wb-exercises__footer">
                                                <button type="button" class="wb-add add-exercise-btn" data-day-id="{{ $plan->id }}">
                                                    <i class="fas fa-plus"></i>@lang('Add Exercise')
                                                </button>
                                            </div>
                                        </section>

                                        <div class="mb-5">
                                            <label class="form-label wb-field-label" style="color: var(--wb-text-secondary, #cbd5e1); font-weight: 600;"><i class="fas fa-snowflake text-info me-1"></i>@lang('Post-Workout Instructions & Video Links')</label>
                                            <div class="warmup-quill-wrapper">
                                                <div id="post_workout_editor_day_{{ $plan->id }}" class="warmup-quill-editor" data-placeholder="@lang('Cool down instructions, post-stretch routine, embed video links, table...')">{!! $plan->post_workout !!}</div>
                                                <input type="hidden" name="post_workout" id="post_workout_input_day_{{ $plan->id }}" class="warmup-hidden-input" value="{{ $plan->post_workout }}">
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end align-items-center mt-6 pt-4 border-top" style="border-color: var(--wb-row-border) !important;">
                                            <button type="submit" class="btn btn-gym-neon d-flex align-items-center gap-2 px-6 py-3">
                                                <i class="fas fa-save fs-4 text-dark"></i>
                                                <span class="text-dark fw-bold">@lang('Update Day')</span>
                                            </button>
                                        </div>
                                    </div>

                                    {!! Form::close() !!}

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="card rounded-4 shadow-sm mb-8 border" style="background: var(--wb-surface-solid); border-color: var(--wb-row-border) !important;">
                    <div class="card-body text-center py-12">
                        <div class="symbol symbol-100px rounded-circle mb-6 d-inline-flex flex-center" style="background: var(--wb-accent-soft); color: var(--wb-accent);">
                            <i class="fas fa-dumbbell fs-1x" style="color: inherit;"></i>
                        </div>
                        <h3 class="fw-bolder mb-3 fs-2"
                            style="color: var(--wb-text);">@lang('No Workout Program Assigned')</h3>
                        <p class="fs-5 mb-8 max-w-500px mx-auto"
                           style="color: var(--wb-text-muted);">@lang('This trainee does not currently have an active workout plan.')</p>

                        @if(userCan('system.workout.create'))
                            <a href="{{ route('system.workout.create', ['trainee' => Crypt::encrypt($result->id)]) }}"
                               class="btn btn-gym-neon px-6 py-3 fw-bold">
                                <i class="ki-duotone ki-plus fs-2 me-1 text-dark"></i> <span
                                    class="text-dark">@lang('Assign Program')</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ===== ARCHIVED WORKOUT HISTORY (PROGRAM-LEVEL VERSIONING) ===== --}}
            @if($result->archived_program_batches->count() > 0)
                <div class="mt-8">
                    <div class="d-flex align-items-center justify-content-between mb-5 flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="symbol symbol-40px rounded-3 d-inline-flex flex-center" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
                                <i class="fas fa-history fs-3" style="color: inherit;"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold fs-4 m-0" style="color: var(--wb-text);">{{ __('Workout Routine History & Archived Programs') }}</h3>
                                <p class="fs-7 mb-0" style="color: var(--wb-text-muted);">
                                    {{ __('Browse past versions of this trainee\'s routine and restore any previous program at any time.') }}
                                </p>
                            </div>
                        </div>
                        <span class="badge badge-light-warning fw-bold fs-7 px-3 py-2">
                            {{ $result->archived_program_batches->count() }} {{ $result->archived_program_batches->count() === 1 ? __('Archived Routine') : __('Archived Routines') }} ({{ $result->archivedWorkoutPlans->count() }} {{ __('archived days') }})
                        </span>
                    </div>

                    <div class="d-flex flex-column gap-5">
                        @foreach($result->archived_program_batches as $bIndex => $batch)
                            @php
                                $collapseId = 'archived_batch_' . md5($batch['batch_key'] . '_' . $bIndex);
                                $restoreConfirmMsg = $result->currentWorkoutPlans->count() > 0
                                    ? __('The trainee currently has an active workout routine. Restoring this archived program will archive the current active program and restore this :days-day routine (:exercises exercises). Are you sure you want to proceed?', ['days' => $batch['days_count'], 'exercises' => $batch['exercises_count']])
                                    : __('Are you sure you want to restore this archived program routine (:days days, :exercises exercises)?', ['days' => $batch['days_count'], 'exercises' => $batch['exercises_count']]);
                            @endphp
                            <div class="archived-program-card rounded-4 border overflow-hidden"
                                 style="background: var(--wb-surface-solid); border-color: var(--wb-row-border) !important; box-shadow: 0 4px 20px rgba(0,0,0,0.25);">

                                {{-- Header with Summary and Dedicated Restore Button --}}
                                <div class="p-5 d-flex align-items-center justify-content-between flex-wrap gap-4"
                                     style="background: var(--wb-head-bg); border-bottom: 1px solid var(--wb-row-border);">

                                    <div class="d-flex align-items-center gap-4 flex-grow-1 cursor-pointer"
                                         data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                                        <div class="symbol symbol-40px rounded-3 d-inline-flex flex-center flex-shrink-0"
                                             style="background: var(--wb-accent-soft); color: var(--wb-accent); border: 1px solid var(--wb-row-border);">
                                            <i class="fas fa-layer-group fs-4" style="color: inherit;"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <h4 class="fw-bold fs-5 m-0" style="color: var(--wb-text);">
                                                    {{ $batch['routine_name'] }}
                                                </h4>
                                                <span class="badge badge-light-info fs-8 fw-semibold">
                                                    <i class="fas fa-clock fs-9 me-1"></i>{{ $batch['archived_date_formatted'] }}
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center gap-3 mt-1 fs-7" style="color: var(--wb-text-secondary, #cbd5e1);">
                                                <span><i class="fas fa-calendar-day text-primary me-1"></i><strong>{{ $batch['days_count'] }}</strong> {{ __('Days') }}</span>
                                                <span>&bull;</span>
                                                <span><i class="fas fa-dumbbell text-success me-1"></i><strong>{{ $batch['exercises_count'] }}</strong> {{ __('Exercises') }}</span>
                                                <span>&bull;</span>
                                                <span class="text-muted"><i class="fas fa-chevron-down fs-9 ms-1"></i> {{ __('Click to expand / collapse') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        @if(userCan('system.trainee.restore-plan'))
                                            <form action="{{ route('system.trainee.restore-plan', $result->id) }}" method="POST" class="d-inline restore-batch-form"
                                                  data-confirm-msg="{{ $restoreConfirmMsg }}">
                                                @csrf
                                                <input type="hidden" name="plan_ids" value="{{ $batch['plan_ids_str'] }}">
                                                <button type="submit" class="btn btn-sm btn-gym-neon fw-bold d-flex align-items-center gap-2 px-4 py-2 shadow-sm">
                                                    <i class="ki-duotone ki-arrows-loop fs-4 text-dark"></i>
                                                    <span class="text-dark">{{ __('Restore This Program') }}</span>
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-icon btn-light"
                                                data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Collapsible Body: Days and Exercises --}}
                                <div class="collapse" id="{{ $collapseId }}">
                                    <div class="p-5 p-lg-6 d-flex flex-column gap-5">
                                        @foreach($batch['plans'] as $dIdx => $archivedPlan)
                                            <div class="archived-day-pill" style="background: var(--wb-row-bg); border: 1px solid var(--wb-row-border); border-radius: 14px; padding: 1.25rem;">
                                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge fw-bold" style="background: var(--wb-accent-soft); color: var(--wb-accent); border: 1px solid var(--wb-row-border);">
                                                            Day {{ $dIdx + 1 }}
                                                        </span>
                                                        <span class="fw-bold fs-5" style="color: var(--wb-text);">{{ $archivedPlan->day_name ?? '' }}</span>
                                                        <span class="badge badge-archived">{{ __('Archived') }}</span>
                                                    </div>
                                                    @if($archivedPlan->exercises->count() > 0)
                                                        <span class="text-muted fs-8">{{ $archivedPlan->exercises->count() }} {{ __('exercises') }}</span>
                                                    @endif
                                                </div>

                                                @if($archivedPlan->warmup || !empty($archivedPlan->warmup_links_list))
                                                    <div class="mb-3 px-3 py-2 rounded-3 warmup-display-box" style="background: rgba(255,193,7,0.06); border-left: 3px solid rgba(255,193,7,0.3);">
                                                        <small class="fw-bold text-warning d-flex align-items-center gap-1 mb-1"><i class="fas fa-fire"></i><span>{{ __('Warm Up') }}:</span></small>
                                                        @if($archivedPlan->warmup)
                                                            <div class="warmup-rich-content fs-7" style="color: var(--wb-text-secondary, #cbd5e1);">{!! $archivedPlan->display_warmup !!}</div>
                                                        @endif
                                                        @if(!empty($archivedPlan->warmup_links_list))
                                                            @include('system.workout.partials.warmup-link-badges', ['links' => $archivedPlan->warmup_links_list, 'title' => $archivedPlan->day_name])
                                                        @endif
                                                    </div>
                                                @endif

                                                @if($archivedPlan->exercises->count() > 0)
                                                    <div class="table-responsive">
                                                        <table class="archived-exercise-table">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>{{ __('Exercise') }}</th>
                                                                    <th class="text-center">{{ __('Sets') }}</th>
                                                                    <th class="text-center">{{ __('Reps') }}</th>
                                                                    <th class="text-center">{{ __('Rest') }}</th>
                                                                    <th>{{ __('Weights') }}</th>
                                                                    <th class="text-center">{{ __('Tempo') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($archivedPlan->exercises as $exIdx => $exercise)
                                                                    <tr>
                                                                        <td class="fw-bold" style="color: var(--wb-accent);">{{ $exIdx + 1 }}</td>
                                                                        <td class="fw-semibold" style="color: var(--wb-text);">
                                                                            <div class="d-flex flex-column">
                                                                                <div class="d-flex align-items-center gap-2">
                                                                                    <span>{{ $exercise->name }}</span>
                                                                                    @if($exercise->machine_image_url)
                                                                                        <button type="button" class="btn btn-sm btn-icon btn-light-warning mi-preview-btn p-0" style="width:26px;height:26px;border-radius:6px;overflow:hidden;display:inline-flex;" data-img-url="{{ $exercise->machine_image_url }}" data-title="{{ $exercise->name }}" title="{{ __('View Equipment Image') }}">
                                                                                            <img src="{{ $exercise->machine_image_url }}" style="width:100%;height:100%;object-fit:cover;">
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                                @if($exercise->notes_en)
                                                                                    <small class="text-muted fs-8 mt-1"><i class="fas fa-comment-dots text-warning me-1"></i>{{ $exercise->notes_en }}</small>
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-center">{{ $exercise->sets ?? '—' }}</td>
                                                                        <td class="text-center">{{ $exercise->reps ?? '—' }}</td>
                                                                        <td class="text-center">{{ $exercise->rest ?? '—' }}</td>
                                                                        <td>
                                                                            @php
                                                                                $weightsList = \App\Models\Exercise::normalizeWeights($exercise->internal_weight);
                                                                            @endphp
                                                                            @if(!empty($weightsList))
                                                                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                                                                    @foreach($weightsList as $weight)
                                                                                        <span class="badge fw-bold px-2 py-1" style="background: var(--wb-accent-soft); color: var(--wb-accent); border: 1px solid var(--wb-row-border); border-radius: 6px; font-size: 0.78rem;">
                                                                                            {{ $weight }}
                                                                                        </span>
                                                                                    @endforeach
                                                                                </div>
                                                                            @else
                                                                                <span class="text-muted">—</span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-center">{{ $exercise->tempo ?? '—' }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <p class="mb-0 text-muted fs-7"><em>{{ __('No exercises recorded for this day.') }}</em></p>
                                                @endif

                                                @if($archivedPlan->post_workout)
                                                    <div class="mt-3 px-3 py-2 rounded-3 post-workout-display-box" style="background: rgba(6,182,212,0.06); border-left: 3px solid rgba(6,182,212,0.4);">
                                                        <small class="fw-bold text-info d-flex align-items-center gap-1 mb-1"><i class="fas fa-snowflake"></i><span>{{ __('Post Workout') }}:</span></small>
                                                        <div class="warmup-rich-content fs-7" style="color: var(--wb-text-secondary, #cbd5e1);">{!! $archivedPlan->display_post_workout !!}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- NUTRITION PLAN TAB -->
        <div class="tab-pane fade" id="kt_staff_nutrition_tab" role="tabpanel">
            @php
                $activeNutrition = $result->activeNutritionPlans->first() ?? \App\Models\NutritionPlan::where('trainee_id', $result->user_id)->active()->latest()->first();
            @endphp

            @if($activeNutrition)
                <div class="card card-flush shadow-sm mb-8">
                    <div class="card-header pt-6 pb-5 border-bottom border-gray-200 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="card-title m-0">
                            <h2 class="text-gray-900 fw-bold m-0 d-flex align-items-center gap-2">
                                <i class="fa-solid fa-apple-whole text-success"></i>
                                <span>{{ $activeNutrition->name ? $activeNutrition->name : __('Current Nutrition Plan') }}</span>
                                <span class="badge badge-light-success fs-8 ms-2">{{ __('Active') }}</span>
                            </h2>
                        </div>
                        <div class="d-flex align-items-center gap-2">

{{--                            <a href="{{ route('system.nutrition.my-plan', ['trainee' => Crypt::encrypt($result->id)]) }}" class="btn btn-sm btn-light-info fw-bold rounded-3">--}}
{{--                                <i class="fa-solid fa-eye me-1"></i>@lang('Trainee View')--}}
{{--                            </a>--}}

                            @if(userCan('system.nutrition.edit'))
                                <a href="{{ route('system.nutrition.edit', $activeNutrition->id) }}"
                                   class="btn btn-sm btn-light-primary fw-bold rounded-3">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>@lang('Edit Plan')
                                </a>
                            @endif

                            @if(userCan('system.nutrition.create'))
                                <a href="{{ route('system.nutrition.create', ['trainee' => Crypt::encrypt($result->id)]) }}"
                                   class="btn btn-sm btn-success fw-bold rounded-3">
                                    <i class="fa-solid fa-plus me-1"></i>@lang('New Plan')
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-6 p-lg-8">
                        <div class="nutrition-content text-gray-800 fs-6 lh-base">
                            @if($activeNutrition->description)
                                {!! $activeNutrition->description !!}
                            @else
                                <div class="text-gray-500 text-center py-5">
                                    @lang('No written details in this nutrition plan.')
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="card card-flush shadow-sm mb-8">
                    <div class="card-body text-center py-12">
                        <div class="symbol symbol-100px bg-light-success rounded-circle mb-6 d-inline-flex flex-center">
                            <i class="fa-solid fa-apple-whole fs-1x text-success"></i>
                        </div>
                        <h3 class="text-gray-900 fw-bolder mb-3 fs-2">@lang('No Active Nutrition Plan Assigned')</h3>
                        <p class="text-gray-600 fs-5 mb-8 max-w-500px mx-auto">@lang('This trainee does not currently have an active nutrition plan.')</p>
                        <a href="{{ route('system.nutrition.create', ['trainee' => Crypt::encrypt($result->id)]) }}" class="btn btn-success fw-bold px-6 py-3">
                            <i class="ki-duotone ki-plus fs-2 me-1"></i> @lang('Assign Nutrition Plan')
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- PROGRESS & PHOTOS TAB -->
        <div class="tab-pane fade" id="kt_progress_photos_tab" role="tabpanel">
            <div class="card rounded-4 shadow-sm mb-8 border">
                <div class="card-header border-bottom border-gray-200 pt-6 pb-5 d-flex align-items-center justify-content-between">
                    <div class="card-title">
                        <h2 class="text-gray-900 dark:text-white fw-bold m-0 d-flex align-items-center gap-2">
                            <span>📸</span> {{ __('Trainee Progress Check-ins & Photos') }}
                        </h2>
                    </div>
                    <div>
                        <a href="{{ route('system.trainee-progress.index', ['trainee_id' => $result->id]) }}" class="btn btn-sm btn-primary fw-bold" target="_blank">
                            <i class="fa fa-external-link-alt me-1"></i> {{ __('Open Full Progress Gallery') }}
                        </a>
                    </div>
                </div>
                <div class="card-body p-6">
                    @php
                        $traineeCheckins = \App\Models\TraineeProgressCheckin::with(['photos', 'coach'])
                            ->where('trainee_id', $result->id)
                            ->orderBy('checkin_date', 'desc')
                            ->get();
                    @endphp

                    <!-- Coach Add Check-in Form -->
                    <div class="bg-light-primary rounded-4 p-5 mb-6 border border-primary border-opacity-25">
                        <h4 class="fw-bold text-primary mb-3">
                            <i class="fa fa-plus-circle me-1"></i> {{ __('Upload Check-in / Photos on Behalf of Trainee') }}
                        </h4>
                        <form action="{{ route('system.trainee-progress.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="trainee_id" value="{{ $result->id }}">

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">{{ __('Check-in Date') }}</label>
                                    <input type="date" name="checkin_date" class="form-control form-control-solid" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">{{ __('Weight (KG)') }}</label>
                                    <input type="number" step="0.1" name="weight" class="form-control form-control-solid" placeholder="e.g. 78.5">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">{{ __('Upload Photos') }} <span class="text-danger">*</span></label>
                                    <input type="file" name="photos[]" class="form-control form-control-solid" required multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('Notes / Observations') }}</label>
                                    <textarea name="notes" class="form-control form-control-solid" rows="2" placeholder="{{ __('Trainee comments...') }}"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-info">{{ __('Coach Feedback') }}</label>
                                    <textarea name="coach_notes" class="form-control form-control-solid border-info" rows="2" placeholder="{{ __('Your coach notes...') }}"></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary fw-bold">
                                        <i class="fa fa-cloud-upload-alt me-1"></i> {{ __('Save Check-in') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>


                    <div class="row g-4">
                        @forelse($traineeCheckins as $ck)
                            <div class="col-12">
                                <div class="border rounded-4 p-4 bg-surface shadow-xs">
                                    <div
                                        class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <span
                                                class="badge bg-primary fs-7 fw-bold">{{ $ck->checkin_date->format('Y-m-d') }}</span>
                                            @if($ck->weight)
                                                <span class="badge bg-light-warning text-warning fs-7 fw-bold">⚖️ {{ $ck->weight }} KG</span>
                                            @endif
                                        </div>
                                        <form action="{{ route('system.trainee-progress.destroy', $ck->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('{{ __('Delete this check-in session?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-icon btn-light-danger btn-sm rounded-circle">
                                                <i class="fa fa-trash fs-7"></i>
                                            </button>
                                        </form>
                                    </div>

                                    @if($ck->notes)
                                        <div class="mb-2 fs-7"><strong>{{ __('Notes') }}:</strong> {{ $ck->notes }}
                                        </div>
                                    @endif

                                    <div class="bg-light-info rounded p-3 mb-3 border border-info border-opacity-25">
                                        <form action="{{ route('system.trainee-progress.update-notes', $ck->id) }}"
                                              method="POST" class="d-flex gap-2 align-items-center">
                                            @csrf
                                            <input type="text" name="coach_notes" value="{{ $ck->coach_notes }}"
                                                   class="form-control form-control-solid form-control-sm"
                                                   placeholder="{{ __('Write coach feedback...') }}">
                                            <button type="submit"
                                                    class="btn btn-sm btn-info fw-bold text-nowrap">{{ __('Update Feedback') }}</button>
                                        </form>
                                    </div>

                                    @if($ck->photos->count() > 0)
                                        <div class="row g-2">
                                            @foreach($ck->photos as $pt)
                                                <div class="col-4 col-sm-3 col-md-2">
                                                    <a href="{{ $pt->photo_url }}" target="_blank"
                                                       class="d-block overflow-hidden rounded border border-gray-300"
                                                       style="aspect-ratio:1/1;">
                                                        <img src="{{ $pt->photo_url }}" class="w-100 h-100"
                                                             style="object-fit:cover;">
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-muted">
                                <i class="fa fa-camera-retro fs-2x mb-2 opacity-50"></i>
                                <p class="mb-0">{{ __('No progress check-ins uploaded for this trainee yet.') }}</p>
                            </div>
                        @endforelse
                    </div>


                </div>
            </div>
        </div>

        <!-- HISTORY / LOGS TAB -->
        <div class="tab-pane fade" id="kt_staff_logs_tab" role="tabpanel">
            <div class="card rounded-4 shadow-sm mb-8 border">
                <div class="card-header border-bottom border-gray-200 pt-6 pb-5">
                    <div class="card-title">
                        <h2 class="text-gray-900 fw-bold m-0">{{ __('History (Audit Log)') }}</h2>
                    </div>
                </div>
                <div class="card-body p-6">
                    {{ view('system.datatable', $activityLogData) }}
                </div>
            </div>
        </div>

        <!-- AUTH SESSIONS TAB -->
        <div class="tab-pane fade" id="kt_staff_auth_sessions_tab" role="tabpanel">
            <div class="card rounded-4 shadow-sm mb-8 border">
                <div class="card-header border-bottom border-gray-200 pt-6 pb-5">
                    <div class="card-title">
                        <h2 class="text-gray-900 fw-bold m-0">{{ __('Auth Sessions') }}</h2>
                    </div>
                </div>
                <div class="card-body p-6">
                    {{ view('system.datatable', $authSessionData) }}
                </div>
            </div>
        </div>

    </div>

    {{-- ===== INLINE ADD WORKOUT DAY MODAL ===== --}}
    <div class="modal fade" id="inlineAddDayModal" tabindex="-1" aria-labelledby="inlineAddDayModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content rounded-4 border shadow-lg" style="background: var(--wb-surface-solid); border-color: var(--wb-row-border) !important; color: var(--wb-text);">
                <div class="modal-header border-bottom py-4 px-6" style="border-color: var(--wb-row-border) !important; background: var(--wb-head-bg);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="symbol symbol-40px rounded-3 d-inline-flex flex-center" style="background: var(--wb-accent-soft); color: var(--wb-accent); border: 1px solid var(--wb-row-border);">
                            <i class="fas fa-calendar-plus fs-3" style="color: inherit;"></i>
                        </div>
                        <div>
                            <h3 class="modal-title fw-bold fs-4 m-0" id="inlineAddDayModalLabel" style="color: var(--wb-text);">
                                {{ __('Add Workout Day') }}
                            </h3>
                            <p class="fs-7 mb-0" style="color: var(--wb-text-muted);">
                                {{ __('Create and attach a new workout routine day directly to this trainee profile.') }}
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-6 p-lg-8">
                    <form id="inline-add-day-form" action="{{ route('system.workout.storeInlineDay') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="trainee_id" value="{{ $result->id }}">

                        <div class="wb-day mb-0" style="background: transparent; border: none; padding: 0;">
                            <div class="row g-4 mb-6">
                                <div class="col-12 col-md-6">
                                    <label class="form-label required" style="color: var(--wb-text-secondary, #cbd5e1); font-weight: 600;">@lang('Day Name')</label>
                                    <input type="text" name="day_name" id="inline_day_name" class="form-control form-control-solid" maxlength="100" required
                                           placeholder="e.g. Day {{ ($result->currentWorkoutPlans->count() ?? 0) + 1 }}: Shoulders & Abs"/>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label wb-field-label" style="color: var(--wb-text-secondary, #cbd5e1); font-weight: 600;"><i class="fas fa-fire text-warning me-1"></i>@lang('Warmup Instructions & Video Links')</label>
                                    <div class="warmup-quill-wrapper">
                                        <div id="inline_warmup_editor" class="warmup-quill-editor" data-placeholder="@lang('Warm up instructions, stretch routine, embed video links...')"></div>
                                        <input type="hidden" name="warmup" id="inline_warmup_input" class="warmup-hidden-input">
                                    </div>
                                </div>
                            </div>

                            <section class="wb-exercises" aria-label="@lang('Exercises')">
                                <header class="wb-exercises__header">
                                    <div class="wb-exercises__title">
                                        <span class="wb-exercises__icon"><i class="fas fa-dumbbell"></i></span>
                                        <div>
                                            <h3>@lang('Exercises')</h3>
                                            <p>@lang('Sets, reps, rest, tempo and up to 4 target weights')</p>
                                        </div>
                                    </div>
                                    <span class="wb-count"><span class="wb-count__num" id="modal-exercise-count">1</span> @lang('Exercises')</span>
                                </header>

                                <div class="wb-table-wrap">
                                    <table class="wb-table" id="modal-exercises-container" data-exercise-index="1">
                                        <colgroup>
                                            <col class="wb-col-idx">
                                            <col class="wb-col-name">
                                            <col class="wb-col-num">
                                            <col class="wb-col-num">
                                            <col class="wb-col-num">
                                            <col class="wb-col-tempo">
                                            <col class="wb-col-weights">
                                            <col class="wb-col-action">
                                        </colgroup>
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">@lang('Exercise') <span class="text-danger">*</span></th>
                                                <th scope="col" class="text-center">@lang('Sets')</th>
                                                <th scope="col" class="text-center">@lang('Reps')</th>
                                                <th scope="col" class="text-center">@lang('Rest')</th>
                                                <th scope="col" class="text-center">@lang('Tempo')</th>
                                                <th scope="col">@lang('Weights')<span class="wb-th-hint">max 4</span></th>
                                                <th scope="col"><span class="visually-hidden">@lang('Actions')</span></th>
                                            </tr>
                                        </thead>
                                        <tbody class="exercise-row wb-row-enter">
                                            <tr class="wb-row-main">
                                                <td class="wb-cell-idx" rowspan="3"><span class="wb-idx" aria-hidden="true">1</span></td>
                                                <td class="wb-cell-name" data-label="@lang('Exercise')">
                                                    <input type="text" name="exercises[ex_1][name]" class="form-control wb-name" maxlength="150" autocomplete="off"
                                                           placeholder="@lang('e.g. Incline Bench Press')" aria-label="@lang('Exercise Name')" required>
                                                    <div class="invalid-feedback"></div>
                                                </td>
                                                <td data-label="@lang('Sets')">
                                                    <input type="text" name="exercises[ex_1][sets]" class="form-control wb-num" maxlength="50" inputmode="numeric"
                                                           placeholder="4" aria-label="@lang('Sets')">
                                                </td>
                                                <td data-label="@lang('Reps')">
                                                    <input type="text" name="exercises[ex_1][reps]" class="form-control wb-num" maxlength="50"
                                                           placeholder="8-12" aria-label="@lang('Reps')">
                                                </td>
                                                <td data-label="@lang('Rest')">
                                                    <input type="text" name="exercises[ex_1][rest]" class="form-control wb-num" maxlength="50"
                                                           placeholder="90s" aria-label="@lang('Rest')">
                                                </td>
                                                <td data-label="@lang('Tempo')">
                                                    <input type="text" name="exercises[ex_1][tempo]" class="form-control wb-num" maxlength="50"
                                                           placeholder="3-0-1" aria-label="@lang('Tempo')">
                                                </td>
                                                <td class="wb-cell-weights" data-label="@lang('Weights') (max 4)">
                                                    @include('system.workout.partials.weight-inputs', [
                                                        'name' => "exercises[ex_1][internal_weight]",
                                                        'weights' => []
                                                    ])
                                                </td>
                                                <td class="wb-cell-action" rowspan="3">
                                                    <button type="button" class="wb-delete delete-exercise-btn" title="@lang('Delete Exercise')" aria-label="@lang('Delete Exercise')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="wb-row-detail wb-row-media">
                                                <td colspan="3" data-label="@lang('Video Link')">
                                                    <span class="wb-mini-label">@lang('Video Link')</span>
                                                    <div class="wb-video-link-wrap">
                                                        <div class="wb-input-icon">
                                                            <i class="fab fa-youtube"></i>
                                                            <input type="url" name="exercises[ex_1][link]" class="form-control wb-video-input" maxlength="255"
                                                                   placeholder="https://video link.com/..." aria-label="@lang('Video Link')">
                                                        </div>
                                                        <button type="button" class="wb-video-preview-btn is-disabled"
                                                                title="@lang('Preview / Open Video')"
                                                                aria-label="@lang('Preview / Open Video')"
                                                                aria-disabled="true">
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td colspan="3" data-label="@lang('Machine / Equipment Photo')">
                                                    <span class="wb-mini-label">@lang('Machine Photo')</span>
                                                    @include('system.workout.partials.machine-image-input', [
                                                        'name' => 'exercises[ex_1]',
                                                        'url' => null,
                                                        'alt' => ''
                                                    ])
                                                </td>
                                            </tr>
                                            <tr class="wb-row-detail wb-row-notes">
                                                <td colspan="6" data-label="@lang('Coach Notes')">
                                                    <span class="wb-mini-label">@lang('Coach Notes')</span>
                                                    <textarea name="exercises[ex_1][notes_en]" rows="1" class="form-control wb-notes"
                                                              placeholder="@lang('Form cues, progression, tips...')" aria-label="@lang('Coach Notes')"></textarea>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="wb-exercises__footer">
                                    <button type="button" class="wb-add" id="modal-add-exercise-btn">
                                        <i class="fas fa-plus"></i>@lang('Add Exercise')
                                    </button>
                                </div>
                            </section>

                            <div class="mt-5">
                                <label class="form-label wb-field-label" style="color: var(--wb-text-secondary, #cbd5e1); font-weight: 600;"><i class="fas fa-snowflake text-info me-1"></i>@lang('Post-Workout Instructions & Video Links')</label>
                                <div class="warmup-quill-wrapper">
                                    <div id="inline_post_workout_editor" class="warmup-quill-editor" data-placeholder="@lang('Cool down instructions, post-stretch routine, embed video links, table...')"></div>
                                    <input type="hidden" name="post_workout" id="inline_post_workout_input" class="warmup-hidden-input">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer border-top py-4 px-6 d-flex justify-content-between" style="border-color: var(--wb-row-border) !important; background: var(--wb-head-bg);">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" form="inline-add-day-form" id="save-inline-day-btn" class="btn btn-gym-neon fw-bold d-flex align-items-center gap-2 px-6 py-3">
                        <i class="fas fa-plus text-dark fs-5"></i>
                        <span class="text-dark">@lang('Save & Add Day')</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer')
    @include('system.workout.partials.weight-inputs-script')
    <script type="text/javascript">
        /**
         * One exercise = one <tbody class="exercise-row"> (main metrics row + detail row).
         * Field names match the day form: exercises[ex_Y][...]
         */
        function traineeExerciseRowHtml(scopeId, exIdx) {
            var base = `exercises[ex_${exIdx}]`;
            return `
            <tbody class="exercise-row wb-row-enter">
                <tr class="wb-row-main">
                    <td class="wb-cell-idx" rowspan="3"><span class="wb-idx" aria-hidden="true">${exIdx}</span></td>
                    <td class="wb-cell-name" data-label="@lang('Exercise')">
                        <input type="text" name="${base}[name]" class="form-control wb-name" maxlength="150" autocomplete="off"
                               placeholder="@lang('e.g. Incline Bench Press')" aria-label="@lang('Exercise Name')">
                        <div class="invalid-feedback"></div>
                    </td>
                    <td data-label="@lang('Sets')">
                        <input type="text" name="${base}[sets]" class="form-control wb-num" maxlength="50" inputmode="numeric"
                               placeholder="4" aria-label="@lang('Sets')">
                    </td>
                    <td data-label="@lang('Reps')">
                        <input type="text" name="${base}[reps]" class="form-control wb-num" maxlength="50"
                               placeholder="8-12" aria-label="@lang('Reps')">
                    </td>
                    <td data-label="@lang('Rest')">
                        <input type="text" name="${base}[rest]" class="form-control wb-num" maxlength="50"
                               placeholder="90s" aria-label="@lang('Rest')">
                    </td>
                    <td data-label="@lang('Tempo')">
                        <input type="text" name="${base}[tempo]" class="form-control wb-num" maxlength="50"
                               placeholder="3-0-1" aria-label="@lang('Tempo')">
                    </td>
                    <td class="wb-cell-weights" data-label="@lang('Weights') (max 4)">
                        ${GymWeights.build(`${base}[internal_weight]`)}
                    </td>
                    <td class="wb-cell-action" rowspan="3">
                        <button type="button" class="wb-delete delete-exercise-btn" title="@lang('Delete Exercise')" aria-label="@lang('Delete Exercise')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                <tr class="wb-row-detail wb-row-media">
                    <td colspan="3" data-label="@lang('Video Link')">
                        <span class="wb-mini-label">@lang('Video Link')</span>
                        <div class="wb-video-link-wrap">
                            <div class="wb-input-icon">
                                <i class="fab fa-youtube"></i>
                                <input type="url" name="${base}[link]" class="form-control wb-video-input" maxlength="255"
                                       placeholder="https://video link.com/..." aria-label="@lang('Video Link')">
                            </div>
                            <button type="button" class="wb-video-preview-btn is-disabled"
                                    title="@lang('Preview / Open Video')"
                                    aria-label="@lang('Preview / Open Video')"
                                    aria-disabled="true">
                                <i class="fas fa-external-link-alt"></i>
                            </button>
                        </div>
                    </td>
                    <td colspan="3" data-label="@lang('Machine / Equipment Photo')">
                        <span class="wb-mini-label">@lang('Machine Photo')</span>
                        ${GymMedia.buildMachineImage(base, '', '')}
                    </td>
                </tr>
                <tr class="wb-row-detail wb-row-notes">
                    <td colspan="6" data-label="@lang('Coach Notes')">
                        <span class="wb-mini-label">@lang('Coach Notes')</span>
                        <textarea name="${base}[notes_en]" rows="1" class="form-control wb-notes"
                                  placeholder="@lang('Form cues, progression, tips...')" aria-label="@lang('Coach Notes')"></textarea>
                    </td>
                </tr>
            </tbody>`;
        }

        function updateExerciseCount(container) {
            var dayId = (container.attr('id') || '').replace('exercises-container-day-', '');
            $(`#exercise-count-day-${dayId}`).text(container.find('.exercise-row').length);
        }

        $(document).ready(function() {
            // Initialize Quill Editors for all workout plan days
            if (typeof WarmupQuill !== 'undefined') {
                @if($result->currentWorkoutPlans->count() > 0)
                    @foreach($result->currentWorkoutPlans as $plan)
                        WarmupQuill.init('#warmup_editor_day_{{ $plan->id }}', '#warmup_input_day_{{ $plan->id }}', {!! json_encode($plan->warmup ?? '') !!});
                        WarmupQuill.init('#post_workout_editor_day_{{ $plan->id }}', '#post_workout_input_day_{{ $plan->id }}', {!! json_encode($plan->post_workout ?? '') !!});
                    @endforeach
                @endif
                WarmupQuill.init('#inline_warmup_editor', '#inline_warmup_input', '');
                WarmupQuill.init('#inline_post_workout_editor', '#inline_post_workout_input', '');
            }

            // Reset inline modal editors on close and re-check on shown
            $('#inlineAddDayModal').on('hidden.bs.modal', function () {
                if (typeof WarmupQuill !== 'undefined') {
                    WarmupQuill.reset('#inline_warmup_editor');
                    WarmupQuill.reset('#inline_post_workout_editor');
                }
            });

            $('#inlineAddDayModal').on('shown.bs.modal', function () {
                if (typeof WarmupQuill !== 'undefined') {
                    WarmupQuill.init('#inline_warmup_editor', '#inline_warmup_input');
                    WarmupQuill.init('#inline_post_workout_editor', '#inline_post_workout_input');
                }
            });

            // Add new exercise to day form
            $(document).on('click', '.add-exercise-btn', function() {
                var dayId = $(this).data('day-id');
                var container = $(`#exercises-container-day-${dayId}`);

                var currentExerciseIdx = parseInt(container.attr('data-exercise-index') || 1) + 1;
                container.attr('data-exercise-index', currentExerciseIdx);

                var $row = $(traineeExerciseRowHtml(dayId, currentExerciseIdx));
                container.append($row);
                updateExerciseCount(container);
                $row.find('.wb-name').trigger('focus');
            });

            // Add new exercise to modal form
            $(document).on('click', '#modal-add-exercise-btn', function() {
                var container = $('#modal-exercises-container');
                var currentExerciseIdx = parseInt(container.attr('data-exercise-index') || 1) + 1;
                container.attr('data-exercise-index', currentExerciseIdx);

                var $row = $(traineeExerciseRowHtml('modal', currentExerciseIdx));
                container.append($row);
                $('#modal-exercise-count').text(container.find('.exercise-row').length);
                $row.find('.wb-name').trigger('focus');
            });

            // Delete exercise (both day forms & modal form)
            $(document).on('click', '.delete-exercise-btn', function() {
                var row = $(this).closest('.exercise-row');
                var container = $(this).closest('table');
                var isModal = (container.attr('id') === 'modal-exercises-container');

                if (container.find('.exercise-row').length > 1) {
                    row.removeClass('wb-row-enter').addClass('wb-row-leave');
                    setTimeout(function () {
                        row.remove();
                        if (isModal) {
                            $('#modal-exercise-count').text(container.find('.exercise-row').length);
                        } else {
                            updateExerciseCount(container);
                        }
                    }, 200);
                } else {
                    row.find('input:not([type=hidden]), textarea').val('');
                    row.find('.wb-notes').css('height', '');
                    GymWeights.reset(row);
                    var $mi = row.find('.mi-wrapper');
                    $mi.removeClass('has-image').find('.mi-file-input').val('');
                    $mi.find('.mi-preview-img').attr('src', '');
                    $mi.find('.mi-preview-btn').attr('data-img-url', '');
                    $mi.find('.mi-upload-text').text("{{ __('Machine Photo') }}");
                    $mi.find('.mi-remove-flag').val('1');
                    row.find('.wb-video-preview-btn').addClass('is-disabled').attr('aria-disabled', 'true');
                }
            });

            // Auto-grow coach notes
            $(document).on('input', '.wb-notes', function () {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });

            $(document).on('input', '.wb-name', function () {
                if ($.trim($(this).val()) !== '') {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').text('').css('display', 'none');
                }
            });

            $(document).on('click', '.workout-day-form button[type="submit"]', function () {
                var form = $(this).closest('form');
                if (typeof WarmupQuill !== 'undefined') {
                    WarmupQuill.syncForm(form);
                }
            });

            // Handle day form submission via AJAX (delegated)
            $(document).on('submit', '.workout-day-form', function(e) {
                e.preventDefault();
                var form = $(this);

                // Sync Quill editor content to hidden input
                if (typeof WarmupQuill !== 'undefined') {
                    WarmupQuill.syncForm(form);
                }

                // Clear previous validation feedback
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').text('').css('display', 'none');

                var validExCount = 0;
                form.find('.wb-name').each(function () {
                    if ($.trim($(this).val()) !== '') {
                        validExCount++;
                    }
                });

                if (validExCount === 0) {
                    var $firstInput = form.find('.wb-name').first();
                    $firstInput.addClass('is-invalid');
                    $firstInput.siblings('.invalid-feedback')
                        .text("{{ __('Cannot save workout day without exercises. Please enter an exercise name.') }}")
                        .css('display', 'block');
                    $firstInput.trigger('focus');
                    var errorMsg = "{{ __('Cannot save workout day without exercises. Please add at least one exercise.') }}";
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'warning', title: "{{ __('Missing Exercises') }}", text: errorMsg });
                    } else if (typeof notify === 'function') {
                        notify(errorMsg, 'error');
                    } else {
                        alert(errorMsg);
                    }
                    return false;
                }

                var btn = form.find('button[type="submit"]');
                var originalText = btn.html();

                btn.html('<i class="fas fa-spinner fa-spin me-1"></i> @lang("Saving...")').prop('disabled', true);

                var formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method') || 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || form.find('input[name="_token"]').val(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        btn.html(originalText).prop('disabled', false);
                        var successMsg = (response && response.message) ? response.message : "{{ __('Day updated successfully') }}";
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: successMsg,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else if (typeof notify === 'function') {
                            notify(successMsg, 'success');
                        } else if(typeof FormAlert === "function") {
                            FormAlert('success', successMsg);
                        } else {
                            alert(successMsg);
                        }

                        // Live DOM update: sync day title and tab pill
                        var updatedDayName = $.trim(form.find('input[name="day_name"]').val());
                        if (updatedDayName) {
                            form.find('.wb-day__title').text(updatedDayName);
                            var formAction = form.attr('action') || '';
                            var dayIdMatch = formAction.match(/day\/(\d+)\/update/);
                            if (dayIdMatch && dayIdMatch[1]) {
                                $('#tab_pill_' + dayIdMatch[1] + ' .day-title-text').text(updatedDayName);
                            }
                        }
                    },
                    error: function(xhr) {
                        btn.html(originalText).prop('disabled', false);
                        var errorMsg = "{{ __('Error updating day. Please check inputs.') }}";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var firstErr = Object.values(xhr.responseJSON.errors)[0];
                            if (Array.isArray(firstErr) && firstErr[0]) {
                                errorMsg = firstErr[0];
                            }
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: "{{ __('Error') }}", text: errorMsg });
                        } else if (typeof notify === 'function') {
                            notify(errorMsg, 'error');
                        } else {
                            alert(errorMsg);
                        }

                        // Highlight specific invalid fields if returned
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(field, messages) {
                                var msg = Array.isArray(messages) ? messages[0] : messages;
                                var $fieldInput = form.find(`[name="${field}"]`);
                                if (!$fieldInput.length) {
                                    var arrayField = field.replace(/\.\d+/g, '[]');
                                    $fieldInput = form.find(`[name="${arrayField}"]`);
                                }
                                if ($fieldInput.length) {
                                    $fieldInput.addClass('is-invalid');
                                    $fieldInput.siblings('.invalid-feedback').text(msg).css('display', 'block');
                                }
                            });
                        }
                    }
                });
            });

            // Toggle Day Active / Inactive Status
            $(document).on('click', '.toggle-day-status-btn', function (e) {
                e.preventDefault();
                var btn = $(this);
                var dayId = btn.data('day-id');
                var currentStatus = btn.attr('data-status');
                var url = btn.data('url');

                var confirmMsg = (currentStatus === 'inactive')
                    ? "{{ __('Are you sure you want to activate this workout day?') }}"
                    : "{{ __('Are you sure you want to deactivate this workout day? It will be temporarily muted.') }}";

                var doToggle = function () {
                    btn.prop('disabled', true);
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            btn.prop('disabled', false);
                            var newStatus = res.data && res.data.status ? res.data.status : (res.new_status || (currentStatus === 'inactive' ? 'active' : 'inactive'));
                            btn.attr('data-status', newStatus);

                            var tabBadge = $('.day-status-badge-' + dayId);
                            var headerBadge = $('.day-header-badge-' + dayId);
                            var dayCard = $('#wb_day_card_' + dayId);
                            var tabPill = $('#tab_pill_' + dayId);

                            if (newStatus === 'inactive') {
                                btn.removeClass('btn-light-warning text-warning').addClass('btn-light-success text-success');
                                btn.find('i').removeClass('fa-pause').addClass('fa-play');
                                btn.find('.toggle-status-text').text("{{ __('Activate Day') }}");

                                tabBadge.removeClass('badge-light-success text-success').addClass('badge-light-warning text-warning').text("{{ __('Inactive') }}");
                                headerBadge.removeClass('badge-light-success text-success').addClass('badge-light-warning text-warning').text("{{ __('Inactive') }}");
                                dayCard.addClass('wb-day--inactive');
                                tabPill.addClass('wb-tab-pill--inactive');
                            } else {
                                btn.removeClass('btn-light-success text-success').addClass('btn-light-warning text-warning');
                                btn.find('i').removeClass('fa-play').addClass('fa-pause');
                                btn.find('.toggle-status-text').text("{{ __('Deactivate Day') }}");

                                tabBadge.removeClass('badge-light-warning text-warning').addClass('badge-light-success text-success').text("{{ __('Active') }}");
                                headerBadge.removeClass('badge-light-warning text-warning').addClass('badge-light-success text-success').text("{{ __('Active') }}");
                                dayCard.removeClass('wb-day--inactive');
                                tabPill.removeClass('wb-tab-pill--inactive');
                            }

                            var successMsg = res.message || "{{ __('Day status updated successfully') }}";
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: successMsg,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else if (typeof notify === 'function') {
                                notify(successMsg, 'success');
                            } else {
                                alert(successMsg);
                            }
                        },
                        error: function (xhr) {
                            btn.prop('disabled', false);
                            var err = "{{ __('Failed to update day status') }}";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                err = xhr.responseJSON.message;
                            }
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ icon: 'error', title: "{{ __('Error') }}", text: err });
                            } else {
                                alert(err);
                            }
                        }
                    });
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: "{{ __('Confirm Status Change') }}",
                        text: confirmMsg,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: "{{ __('Yes, proceed') }}",
                        cancelButtonText: "{{ __('Cancel') }}",
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#6b7280'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            doToggle();
                        }
                    });
                } else {
                    if (confirm(confirmMsg)) {
                        doToggle();
                    }
                }
            });

            $(document).on('click', '#save-inline-day-btn', function () {
                if (typeof WarmupQuill !== 'undefined') {
                    WarmupQuill.sync('#inline_warmup_editor');
                    WarmupQuill.sync('#inline_post_workout_editor');
                }
            });

            // Handle Inline Add Day Form Submission
            $('#inline-add-day-form').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);

                if (typeof WarmupQuill !== 'undefined') {
                    WarmupQuill.sync('#inline_warmup_editor');
                    WarmupQuill.sync('#inline_post_workout_editor');
                }

                var dayName = $.trim($('#inline_day_name').val());
                if (!dayName) {
                    $('#inline_day_name').addClass('is-invalid');
                    $('#inline_day_name').siblings('.invalid-feedback')
                        .text("{{ __('Please enter a day name.') }}")
                        .css('display', 'block');
                    $('#inline_day_name').trigger('focus');
                    return false;
                }

                var validExCount = 0;
                form.find('.wb-name').each(function () {
                    if ($.trim($(this).val()) !== '') {
                        validExCount++;
                    }
                });

                if (validExCount === 0) {
                    var $firstInput = form.find('.wb-name').first();
                    $firstInput.addClass('is-invalid');
                    $firstInput.siblings('.invalid-feedback')
                        .text("{{ __('Cannot save workout day without exercises. Please enter an exercise name.') }}")
                        .css('display', 'block');
                    $firstInput.trigger('focus');
                    var errorMsg = "{{ __('Cannot save workout day without exercises. Please add at least one exercise.') }}";
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'warning', title: "{{ __('Missing Exercises') }}", text: errorMsg });
                    } else if (typeof notify === 'function') {
                        notify(errorMsg, 'error');
                    } else {
                        alert(errorMsg);
                    }
                    return false;
                }

                var btn = $('#save-inline-day-btn');
                var originalHtml = btn.html();
                btn.html('<i class="fas fa-spinner fa-spin text-dark me-1"></i> <span class="text-dark">@lang("Saving...")</span>').prop('disabled', true);

                var formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        btn.html(originalHtml).prop('disabled', false);
                        var successMsg = (res.data && res.data.message) || res.message || "{{ __('New workout day added successfully!') }}";

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: successMsg,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            alert(successMsg);
                            window.location.reload();
                        }
                    },
                    error: function (xhr) {
                        btn.html(originalHtml).prop('disabled', false);
                        var errMsg = "{{ __('Failed to add workout day. Please check inputs.') }}";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var firstErr = Object.values(xhr.responseJSON.errors)[0];
                            if (Array.isArray(firstErr) && firstErr[0]) {
                                errMsg = firstErr[0];
                            }
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: "{{ __('Error') }}", text: errMsg });
                        } else {
                            alert(errMsg);
                        }
                    }
                });
            });

            // Confirm SweetAlert for Program Batch Restores
            $(document).on('submit', '.restore-batch-form', function(e) {
                var form = this;
                var msg = $(form).data('confirm-msg') || "{{ __('Are you sure you want to restore this archived workout program?') }}";

                if (typeof Swal !== 'undefined') {
                    e.preventDefault();
                    Swal.fire({
                        title: "{{ __('Restore Archived Program') }}",
                        text: msg,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: "{{ __('Yes, Restore Program') }}",
                        cancelButtonText: "{{ __('Cancel') }}",
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#6b7280'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
@endsection

