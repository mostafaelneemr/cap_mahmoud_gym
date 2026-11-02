@extends('system.layout')

@section('header')
<style>
    /* Premium SaaS Dashboard Styling */
    .dashboard-hero {
        background: linear-gradient(135deg, rgba(24, 28, 50, 0.05) 0%, rgba(54, 153, 255, 0.08) 100%);
        border: 1px solid rgba(54, 153, 255, 0.15);
        border-radius: 1rem;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
    }
    [data-bs-theme="dark"] .dashboard-hero {
        background: linear-gradient(135deg, rgba(30, 30, 45, 0.9) 0%, rgba(20, 20, 31, 0.95) 100%);
        border-color: rgba(255, 255, 255, 0.08);
    }

    .stat-card-modern {
        border-radius: 1rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        background: var(--bs-card-bg, #ffffff);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    [data-bs-theme="dark"] .stat-card-modern {
        border-color: rgba(255, 255, 255, 0.06);
    }
    .stat-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        border-color: rgba(54, 153, 255, 0.3);
    }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1.25rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        background: var(--bs-card-bg, #ffffff);
        color: var(--bs-body-color);
        transition: all 0.2s ease;
        text-decoration: none;
    }
    [data-bs-theme="dark"] .quick-action-btn {
        border-color: rgba(255, 255, 255, 0.08);
    }
    .quick-action-btn:hover {
        background: rgba(54, 153, 255, 0.08);
        border-color: var(--bs-primary);
        color: var(--bs-primary);
        transform: translateY(-2px);
    }

    .progress-bar-glow {
        box-shadow: 0 0 12px rgba(54, 153, 255, 0.5);
    }

    .activity-timeline {
        position: relative;
        padding-inline-start: 1.5rem;
    }
    .activity-timeline::before {
        content: '';
        position: absolute;
        inset-inline-start: 7px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: var(--bs-border-color, #e4e6ef);
    }
    .activity-dot {
        position: absolute;
        inset-inline-start: -1.5rem;
        top: 4px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: var(--bs-card-bg, #fff);
        border: 3px solid var(--bs-primary);
        z-index: 1;
    }

    .badge-soft-success { background: rgba(80, 205, 137, 0.12); color: #50cd89; }
    .badge-soft-danger { background: rgba(241, 65, 108, 0.12); color: #f1416c; }
    .badge-soft-warning { background: rgba(255, 199, 0, 0.12); color: #ffc700; }
    .badge-soft-primary { background: rgba(54, 153, 255, 0.12); color: #3699ff; }
    .badge-soft-info { background: rgba(114, 57, 234, 0.12); color: #7239ea; }

    .nav-pills-custom .nav-link {
        font-weight: 600;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        color: var(--bs-gray-600);
        transition: all 0.2s ease;
    }
    .nav-pills-custom .nav-link.active {
        background-color: var(--bs-primary);
        color: #fff;
    }
</style>
@endsection

@section('content')
<!--begin::Hero Banner-->
<div class="dashboard-hero d-flex flex-stack flex-wrap gap-4">
    <div class="d-flex align-items-center gap-4">
        <div class="symbol symbol-60px symbol-circle bg-light-primary">
            <span class="symbol-label bg-primary text-white fs-1 fw-bold">
                {{ mb_substr(auth()->user()->name ?? 'C', 0, 1) }}
            </span>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-light-primary fw-bold fs-8">{{ __('Coach Dashboard') }}</span>
            </div>
            <h1 class="text-gray-900 fw-bolder fs-2x mb-1">
                {{ __('Welcome back, Coach :name', ['name' => auth()->user()->name]) }} 👋
            </h1>
            <p class="text-muted fw-semibold fs-6 mb-0">
                {{ __('Gym Operations Command Center — Real-time performance, trainees progress, and pending requests.') }}
            </p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="badge badge-light-primary px-4 py-3 fs-7 fw-bold">
            <i class="fa-regular fa-calendar me-2"></i>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </span>
    </div>
</div>
<!--end::Hero Banner-->

<!--begin::Quick Operations Toolbar-->
<div class="row g-3 mb-8">
    @if(userCan('system.trainee.create'))
    <div class="col-sm-6 col-md-4 col-xl-2">
        <a href="{{ route('system.trainee.create') }}" class="quick-action-btn shadow-sm">
            <div class="stat-icon-wrapper badge-soft-primary"><i class="fa-solid fa-user-plus text-primary"></i></div>
            <span>{{ __('New Trainee') }}</span>
        </a>
    </div>
    @endif

    @if(userCan('system.workout.create'))
    <div class="col-sm-6 col-md-4 col-xl-2">
        <a href="{{ route('system.workout.create') }}" class="quick-action-btn shadow-sm">
            <div class="stat-icon-wrapper badge-soft-success"><i class="fa-solid fa-dumbbell text-success"></i></div>
            <span>{{ __('New Workout') }}</span>
        </a>
    </div>
    @endif

    @if(userCan('system.nutrition.create'))
    <div class="col-sm-6 col-md-4 col-xl-2">
        <a href="{{ route('system.nutrition.create') }}" class="quick-action-btn shadow-sm">
            <div class="stat-icon-wrapper badge-soft-warning"><i class="fa-solid fa-apple-whole text-warning"></i></div>
            <span>{{ __('New Diet') }}</span>
        </a>
    </div>
    @endif

    @if(userCan('system.trainee-progress.index'))
    <div class="col-sm-6 col-md-4 col-xl-2">
        <a href="{{ route('system.trainee-progress.index') }}" class="quick-action-btn shadow-sm">
            <div class="stat-icon-wrapper badge-soft-info"><i class="fa-solid fa-camera-retro text-info"></i></div>
            <span>{{ __('Check-ins Gallery') }}</span>
        </a>
    </div>
    @endif

    @if(userCan('system.trainee.index'))
    <div class="col-sm-6 col-md-4 col-xl-2">
        <a href="{{ route('system.trainee.index') }}" class="quick-action-btn shadow-sm">
            <div class="stat-icon-wrapper badge-soft-danger"><i class="fa-solid fa-users-gear text-danger"></i></div>
            <span>{{ __('All Trainees') }}</span>
        </a>
    </div>
    @endif

    @if(userCan('system.whatsapp-broadcast.index'))
    <div class="col-sm-6 col-md-4 col-xl-2">
        <a href="{{ route('system.whatsapp-broadcast.index') }}" class="quick-action-btn shadow-sm">
            <div class="stat-icon-wrapper badge-soft-success"><i class="fab fa-whatsapp text-success"></i></div>
            <span>{{ __('WhatsApp Broadcast') }}</span>
        </a>
    </div>
    @endif
</div>
<!--end::Quick Operations Toolbar-->

<!--begin::Section A: Overview Statistics-->
<div class="row g-5 g-xl-6 mb-8">
    <!-- Total Trainees -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-modern p-6 h-100 shadow-sm d-flex flex-column justify-content-between">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <span class="fw-bold fs-6 text-muted">{{ __('Total Trainees') }}</span>
                <div class="stat-icon-wrapper badge-soft-primary">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div>
                <div class="fs-2hx fw-bolder text-gray-900 lh-1 mb-2">
                    {{ number_format($mainStats['total_trainees'] ?? $totalTrainees ?? 0) }}
                </div>
                <div class="d-flex align-items-center justify-content-between text-muted fs-7">
                    <span>{{ __('Active Ratio') }}: <strong class="text-primary">{{ $mainStats['active_ratio_percentage'] ?? 0 }}%</strong></span>
                    <span>{{ __('Inactive') }}: <strong class="text-secondary">{{ $mainStats['inactive_trainees'] ?? 0 }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Trainees -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-modern p-6 h-100 shadow-sm d-flex flex-column justify-content-between">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <span class="fw-bold fs-6 text-muted">{{ __('Active Trainees') }}</span>
                <div class="stat-icon-wrapper badge-soft-success">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>
            <div>
                <div class="fs-2hx fw-bolder text-gray-900 lh-1 mb-2">
                    {{ number_format($mainStats['active_trainees'] ?? $activeMemberships ?? 0) }}
                </div>
                <div class="d-flex align-items-center justify-content-between text-muted fs-7">
                    <span>{{ __('Active Memberships') }}</span>
                    <span class="text-success fw-bold"><i class="fa-solid fa-circle-check text-success me-1"></i>{{ __('Active') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- New Trainees This Month -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-modern p-6 h-100 shadow-sm d-flex flex-column justify-content-between">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <span class="fw-bold fs-6 text-muted">{{ __('New This Month') }}</span>
                <div class="stat-icon-wrapper badge-soft-info">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
            </div>
            <div>
                <div class="fs-2hx fw-bolder text-gray-900 lh-1 mb-2">
                    {{ number_format($mainStats['new_this_month'] ?? 0) }}
                </div>
                <div class="d-flex align-items-center justify-content-between text-muted fs-7">
                    <span>{{ __('vs. Last Month') }} ({{ $mainStats['new_last_month'] ?? 0 }})</span>
                    @php $growth = $mainStats['growth_percentage'] ?? 0; @endphp
                    @if($growth >= 0)
                        <span class="badge badge-soft-success fw-bold"><i class="fa-solid fa-arrow-up text-success me-1"></i>+{{ $growth }}%</span>
                    @else
                        <span class="badge badge-soft-danger fw-bold"><i class="fa-solid fa-arrow-down text-danger me-1"></i>{{ $growth }}%</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Subscriptions Attention (Expiring / Expired) -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-modern p-6 h-100 shadow-sm d-flex flex-column justify-content-between">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <span class="fw-bold fs-6 text-muted">{{ __('Subscriptions Status') }}</span>
                <div class="stat-icon-wrapper badge-soft-danger">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </div>
            </div>
            <div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <span class="fs-2hx fw-bolder text-danger lh-1">{{ $mainStats['expiring_soon_trainees'] ?? 0 }}</span>
                    <span class="fs-7 text-muted">{{ __('Expiring (7d)') }}</span>
                    <span class="text-muted mx-1">/</span>
                    <span class="fs-4 fw-bold text-gray-600 lh-1">{{ $mainStats['expired_trainees'] ?? 0 }}</span>
                    <span class="fs-7 text-muted">{{ __('Expired') }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between text-muted fs-7">
                    <span>{{ __('Action Required') }}</span>
                    <span class="badge badge-soft-warning fw-bold">{{ __('Follow Up') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Section A-->

<!--begin::Section C & B: Training Health & Financial Status Overview-->
<div class="row g-5 g-xl-6 mb-8">
    <!-- Training & Nutrition Coverage Indicators -->
    <div class="col-lg-8">
        <div class="card card-flush h-100 shadow-sm border-0">
            <div class="card-header align-items-center py-5">
                <div class="card-title d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clipboard-check text-primary fs-3"></i>
                    <h3 class="card-label fw-bold text-gray-900 fs-4 mb-0">{{ __('Training & Nutrition Program Indicators') }}</h3>
                </div>
                <div class="card-toolbar">
                    <span class="text-muted fs-7">{{ __('Calculated for active members') }}</span>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row g-5">
                    <!-- Workout Coverage Card -->
                    <div class="col-md-6">
                        <div class="p-5 rounded-3 bg-light border border-dashed border-gray-300">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-gray-700 fs-6">{{ __('Workout Plans Coverage') }}</span>
                                <span class="fw-bolder fs-5 text-primary">{{ $trainingStats['workout_coverage_pct'] ?? 0 }}%</span>
                            </div>
                            <div class="progress h-8px bg-white rounded-pill mb-3">
                                <div class="progress-bar bg-primary rounded-pill progress-bar-glow" role="progressbar"
                                     style="width: {{ $trainingStats['workout_coverage_pct'] ?? 0 }}%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted fs-7">
                                <span>{{ __('Assigned') }}: <strong class="text-gray-900">{{ $trainingStats['with_active_workout'] ?? 0 }}</strong></span>
                                <span>{{ __('Missing Plan') }}: <strong class="text-danger">{{ $trainingStats['missing_workout'] ?? $pendingWorkouts ?? 0 }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Nutrition Coverage Card -->
                    <div class="col-md-6">
                        <div class="p-5 rounded-3 bg-light border border-dashed border-gray-300">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-gray-700 fs-6">{{ __('Nutrition Plans Coverage') }}</span>
                                <span class="fw-bolder fs-5 text-success">{{ $trainingStats['nutrition_coverage_pct'] ?? 0 }}%</span>
                            </div>
                            <div class="progress h-8px bg-white rounded-pill mb-3">
                                <div class="progress-bar bg-success rounded-pill" role="progressbar"
                                     style="width: {{ $trainingStats['nutrition_coverage_pct'] ?? 0 }}%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted fs-7">
                                <span>{{ __('Assigned') }}: <strong class="text-gray-900">{{ $trainingStats['with_active_nutrition'] ?? 0 }}</strong></span>
                                <span>{{ __('Active Members') }}: <strong class="text-gray-900">{{ $trainingStats['active_trainees'] ?? 0 }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="separator separator-dashed my-6"></div>

                <!-- Secondary Operational Indicators -->
                <div class="row g-4 text-center">
                    <div class="col-sm-6">
                        <div class="p-4 rounded-2 bg-light-warning">
                            <div class="fs-2hx fw-bolder text-warning lh-1 mb-1">
                                {{ $trainingStats['pending_load_adjustments'] ?? 0 }}
                            </div>
                            <span class="fs-7 fw-semibold text-gray-700">{{ __('Pending Load Adjustment Requests') }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-4 rounded-2 bg-light-info">
                            <div class="fs-2hx fw-bolder text-info lh-1 mb-1">
                                {{ $trainingStats['trainees_needing_checkin'] ?? 0 }}
                            </div>
                            <span class="fs-7 fw-semibold text-gray-700">{{ __('Trainees Overdue for Check-in (14d+)') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Breakdown & Financial Ledger Status -->
    <div class="col-lg-4">
        <div class="card card-flush h-100 shadow-sm border-0">
            <div class="card-header align-items-center py-5">
                <div class="card-title d-flex align-items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar text-success fs-3"></i>
                    <h3 class="card-label fw-bold text-gray-900 fs-4 mb-0">{{ __('Subscription & Financial Overview') }}</h3>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-muted fs-7">{{ __('Active Subscriptions') }}</span>
                        <span class="badge badge-soft-success fw-bold">{{ $subscriptionBreakdown['active_pct'] ?? 0 }}%</span>
                    </div>
                    <div class="fs-2hx fw-bolder text-gray-900">{{ $subscriptionBreakdown['active'] ?? 0 }}</div>
                </div>

                <div class="d-flex flex-column gap-3 mb-6">
                    <div class="d-flex align-items-center justify-content-between p-3 rounded bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-circle badge-warning" style="width: 10px; height: 10px;"></span>
                            <span class="fs-7 fw-semibold text-gray-700">{{ __('Expiring Soon (Within 7 Days)') }}</span>
                        </div>
                        <span class="fw-bold text-warning fs-6">{{ $subscriptionBreakdown['expiring_soon'] ?? 0 }}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-3 rounded bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-circle badge-danger" style="width: 10px; height: 10px;"></span>
                            <span class="fs-7 fw-semibold text-gray-700">{{ __('Expired Memberships') }}</span>
                        </div>
                        <span class="fw-bold text-danger fs-6">{{ $subscriptionBreakdown['expired'] ?? 0 }}</span>
                    </div>
                </div>

                <!-- Financial Notice Note Card -->
                <div class="p-4 rounded-3 bg-light-primary border border-primary border-dashed">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fa-solid fa-circle-info text-primary mt-1"></i>
                        <div class="fs-7 text-gray-700">
                            <strong>{{ __('Financial Ledger Notice') }}:</strong>
                            {{ __('Online payment gateway and automated transaction billing are currently managed through offline records. Revenue KPIs will activate automatically when the payment gateway ledger is enabled.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Section C & B-->

<!--begin::Section E: Interactive Charts-->
{{--<div class="row g-5 g-xl-6 mb-8">--}}
{{--    <!-- Trainee Registration Growth Trend Chart -->--}}
{{--    <div class="col-lg-8">--}}
{{--        <div class="card card-flush h-100 shadow-sm border-0">--}}
{{--            <div class="card-header align-items-center py-5">--}}
{{--                <div class="card-title d-flex align-items-center gap-2">--}}
{{--                    <i class="fa-solid fa-chart-line text-primary fs-3"></i>--}}
{{--                    <h3 class="card-label fw-bold text-gray-900 fs-4 mb-0">{{ __('Trainee Growth Trend (Last 6 Months)') }}</h3>--}}
{{--                </div>--}}
{{--                <div class="card-toolbar">--}}
{{--                    <span class="badge badge-light-primary fw-bold">{{ __('Monthly Registrations') }}</span>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="card-body pt-0">--}}
{{--                <div id="trainee_growth_chart" style="height: 280px;"></div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <!-- Membership Status Breakdown Donut Chart -->--}}
{{--    <div class="col-lg-4">--}}
{{--        <div class="card card-flush h-100 shadow-sm border-0">--}}
{{--            <div class="card-header align-items-center py-5">--}}
{{--                <div class="card-title d-flex align-items-center gap-2">--}}
{{--                    <i class="fa-solid fa-chart-pie text-success fs-3"></i>--}}
{{--                    <h3 class="card-label fw-bold text-gray-900 fs-4 mb-0">{{ __('Membership Distribution') }}</h3>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="card-body pt-0 d-flex flex-column justify-content-center">--}}
{{--                <div id="subscription_breakdown_chart" style="height: 280px;"></div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<!--end::Section E Charts-->

<!--begin::Section: Action Items Hub & Recent Activity-->
<div class="row g-5 g-xl-6 mb-8">
    <!-- Action Items Tabbed Hub -->
    <div class="col-lg-8">
        <div class="card card-flush h-100 shadow-sm border-0">
            <div class="card-header align-items-center py-4">
                <div class="card-title">
                    <h3 class="card-label fw-bold text-gray-900 fs-4 mb-0">{{ __('Action Items & Alerts') }}</h3>
                </div>
                <div class="card-toolbar">
                    <ul class="nav nav-pills nav-pills-custom" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_missing_workouts">
                                {{ __('Missing Plans') }}
                                <span class="badge badge-light-danger ms-1">{{ count($pendingActions['missing_workouts'] ?? []) }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_pending_loads">
                                {{ __('Load Adjustments') }}
                                <span class="badge badge-light-warning ms-1">{{ count($pendingActions['pending_adjustments'] ?? []) }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_expiring_subs">
                                {{ __('Expiring Soon') }}
                                <span class="badge badge-light-info ms-1">{{ count($pendingActions['expiring_subscriptions'] ?? []) }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_recently_expired">
                                {{ __('Expired') }}
                                <span class="badge badge-light-secondary ms-1">{{ count($recentlyExpired ?? []) }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_recent_trainees">
                                {{ __('Recent Trainees') }}
                                <span class="badge badge-light-primary ms-1">{{ count($recentTrainees['recently_added'] ?? []) }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_program_activity">
                                {{ __('Program Activity') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body pt-2">
                <div class="tab-content">
                    <!-- Tab 1: Missing Workout Plans -->
                    <div class="tab-pane fade show active" id="tab_missing_workouts" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>{{ __('Trainee') }}</th>
                                        <th>{{ __('Fitness Level') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th class="text-end">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingActions['missing_workouts'] ?? $pendingTrainees as $trainee)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-circle symbol-35px me-3">
                                                    <span class="symbol-label bg-light-primary text-primary fw-bold">
                                                        {{ mb_substr($trainee->user?->name ?? 'T', 0, 1) }}
                                                    </span>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <a href="{{ route('system.trainee.show', $trainee->id) }}" class="text-gray-900 text-hover-primary fw-bold fs-6">
                                                        {{ $trainee->user?->name ?? __('N/A') }}
                                                    </a>
                                                    <span class="text-muted fs-7">{{ $trainee->user?->mobile ?? $trainee->user?->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-soft-info">{{ __($trainee->training_level ?? 'Normal') }}</span></td>
                                        <td><span class="badge badge-soft-success">{{ __($trainee->status ?? 'active') }}</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('system.trainee.get-workout', $trainee->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fa-solid fa-plus me-1"></i> {{ __('Assign Plan') }}
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8 text-muted">
                                            <i class="fa-solid fa-circle-check fs-2x text-success mb-2 d-block"></i>
                                            {{ __('All active trainees currently have active workout plans assigned!') }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: Pending Load Adjustments -->
                    <div class="tab-pane fade" id="tab_pending_loads" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>{{ __('Trainee') }}</th>
                                        <th>{{ __('Exercise') }}</th>
                                        <th>{{ __('Suggested') }}</th>
                                        <th class="text-end">{{ __('Review') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingActions['pending_adjustments'] ?? [] as $adj)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-circle symbol-35px me-3">
                                                    <span class="symbol-label bg-light-warning text-warning fw-bold">
                                                        {{ mb_substr($adj->trainee?->user?->name ?? 'T', 0, 1) }}
                                                    </span>
                                                </div>
                                                <span class="fw-bold text-gray-900">{{ $adj->trainee?->user?->name ?? __('Trainee') }}</span>
                                            </div>
                                        </td>
                                        <td><span class="text-gray-800 fw-semibold">{{ $adj->exercise?->name ?? __('Exercise') }}</span></td>
                                        <td><span class="badge badge-soft-warning">{{ $adj->suggested_weight }} kg</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('system.trainee.show', $adj->trainee_id) }}" class="btn btn-sm btn-light-warning">
                                                <i class="fa-solid fa-eye me-1"></i> {{ __('Review') }}
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8 text-muted">
                                            <i class="fa-solid fa-thumbs-up fs-2x text-success mb-2 d-block"></i>
                                            {{ __('No pending exercise load adjustments at this time.') }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 3: Expiring Subscriptions -->
                    <div class="tab-pane fade" id="tab_expiring_subs" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>{{ __('Trainee') }}</th>
                                        <th>{{ __('Expiration Date') }}</th>
                                        <th>{{ __('Remaining') }}</th>
                                        <th class="text-end">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingActions['expiring_subscriptions'] ?? [] as $sub)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-gray-900">{{ $sub->user?->name ?? __('Trainee') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-warning">{{ \Carbon\Carbon::parse($sub->membership_end)->format('d M Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="text-danger fw-semibold">{{ \Carbon\Carbon::parse($sub->membership_end)->diffForHumans() }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('system.trainee.show', $sub->id) }}" class="btn btn-sm btn-light-primary">
                                                <i class="fa-solid fa-arrows-rotate me-1"></i> {{ __('Renew') }}
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8 text-muted">
                                            <i class="fa-solid fa-circle-check fs-2x text-success mb-2 d-block"></i>
                                            {{ __('No memberships are expiring within the next 7 days.') }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 4: Recently Expired Subscriptions -->
                    <div class="tab-pane fade" id="tab_recently_expired" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>{{ __('Trainee') }}</th>
                                        <th>{{ __('Expired Since') }}</th>
                                        <th>{{ __('Contact') }}</th>
                                        <th class="text-end">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentlyExpired ?? [] as $expTrainee)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-circle symbol-35px me-3">
                                                    <span class="symbol-label bg-light-danger text-danger fw-bold">
                                                        {{ mb_substr($expTrainee->user?->name ?? 'T', 0, 1) }}
                                                    </span>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <a href="{{ route('system.trainee.show', $expTrainee->id) }}" class="text-gray-900 text-hover-primary fw-bold fs-6">
                                                        {{ $expTrainee->user?->name ?? __('N/A') }}
                                                    </a>
                                                    <span class="text-muted fs-7">{{ $expTrainee->user?->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-danger">
                                                {{ $expTrainee->membership_end ? \Carbon\Carbon::parse($expTrainee->membership_end)->format('d M Y') : __('Expired') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-gray-700 fs-7">{{ $expTrainee->user?->mobile ?? __('No mobile') }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('system.trainee.show', $expTrainee->id) }}" class="btn btn-sm btn-light-warning">
                                                <i class="fa-solid fa-arrows-rotate me-1"></i> {{ __('Follow Up') }}
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8 text-muted">
                                            <i class="fa-solid fa-circle-check fs-2x text-success mb-2 d-block"></i>
                                            {{ __('No recently expired subscriptions recorded.') }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 5: Recent Trainees -->
                    <div class="tab-pane fade" id="tab_recent_trainees" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th>{{ __('Trainee') }}</th>
                                        <th>{{ __('Joined Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th class="text-end">{{ __('Profile') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTrainees['recently_added'] ?? [] as $rTrainee)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-circle symbol-35px me-3">
                                                    <span class="symbol-label bg-light-info text-info fw-bold">
                                                        {{ mb_substr($rTrainee->user?->name ?? 'T', 0, 1) }}
                                                    </span>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <a href="{{ route('system.trainee.show', $rTrainee->id) }}" class="text-gray-900 text-hover-primary fw-bold fs-6">
                                                        {{ $rTrainee->user?->name ?? __('N/A') }}
                                                    </a>
                                                    <span class="text-muted fs-7">{{ $rTrainee->user?->mobile ?? $rTrainee->user?->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-gray-700 fs-7">{{ $rTrainee->created_at ? $rTrainee->created_at->format('d M Y') : '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $rTrainee->status === 'active' ? 'badge-soft-success' : 'badge-soft-secondary' }}">{{ __($rTrainee->status ?? 'normal') }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('system.trainee.show', $rTrainee->id) }}" class="btn btn-sm btn-light-primary">
                                                <i class="fa-solid fa-user me-1"></i> {{ __('View Profile') }}
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8 text-muted">
                                            {{ __('No trainees registered yet.') }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 6: Recent Program & Progress Activities -->
                    <div class="tab-pane fade" id="tab_program_activity" role="tabpanel">
                        <div class="d-flex flex-column gap-4 py-2">
                            <!-- Recent Workouts -->
                            @if(isset($programActivity['recent_workouts']) && $programActivity['recent_workouts']->isNotEmpty())
                                <div>
                                    <h6 class="fw-bold text-gray-800 fs-7 mb-2"><i class="fa-solid fa-dumbbell text-primary me-2"></i>{{ __('Recently Assigned Workouts') }}</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($programActivity['recent_workouts']->take(3) as $wPlan)
                                            <a href="{{ route('system.trainee.show', $wPlan->trainee_id) }}" class="d-flex align-items-center p-2 rounded bg-light text-gray-800 text-hover-primary text-decoration-none border fs-7">
                                                <span class="fw-bold me-2">{{ $wPlan->trainee?->user?->name ?? __('Trainee') }}:</span>
                                                <span class="badge badge-light-primary me-2">{{ $wPlan->day_name }}</span>
                                                <span class="text-muted fs-8">{{ $wPlan->updated_at ? $wPlan->updated_at->diffForHumans() : '' }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Recent Nutrition -->
                            @if(isset($programActivity['recent_nutritions']) && $programActivity['recent_nutritions']->isNotEmpty())
                                <div>
                                    <h6 class="fw-bold text-gray-800 fs-7 mb-2"><i class="fa-solid fa-apple-whole text-success me-2"></i>{{ __('Recently Assigned Nutrition Plans') }}</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($programActivity['recent_nutritions']->take(3) as $nPlan)
                                            <a href="{{ route('system.trainee.show', $nPlan->trainee_id) }}" class="d-flex align-items-center p-2 rounded bg-light text-gray-800 text-hover-success text-decoration-none border fs-7">
                                                <span class="fw-bold me-2">{{ $nPlan->trainee?->user?->name ?? __('Trainee') }}:</span>
                                                <span class="badge badge-light-success me-2">{{ $nPlan->name }}</span>
                                                <span class="text-muted fs-8">{{ $nPlan->updated_at ? $nPlan->updated_at->diffForHumans() : '' }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Recent Check-ins -->
                            @if(isset($programActivity['recent_checkins']) && $programActivity['recent_checkins']->isNotEmpty())
                                <div>
                                    <h6 class="fw-bold text-gray-800 fs-7 mb-2"><i class="fa-solid fa-camera-retro text-info me-2"></i>{{ __('Recent Trainee Progress Check-ins') }}</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($programActivity['recent_checkins']->take(3) as $chk)
                                            <a href="{{ route('system.trainee-progress.index', ['trainee_id' => $chk->trainee_id]) }}" class="d-flex align-items-center p-2 rounded bg-light text-gray-800 text-hover-info text-decoration-none border fs-7">
                                                <span class="fw-bold me-2">{{ $chk->trainee?->user?->name ?? __('Trainee') }}:</span>
                                                <span class="badge badge-light-info me-2">{{ $chk->weight }} kg</span>
                                                <span class="text-muted fs-8">{{ $chk->created_at ? $chk->created_at->diffForHumans() : '' }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if((!isset($programActivity['recent_workouts']) || $programActivity['recent_workouts']->isEmpty()) && (!isset($programActivity['recent_nutritions']) || $programActivity['recent_nutritions']->isEmpty()) && (!isset($programActivity['recent_checkins']) || $programActivity['recent_checkins']->isEmpty()))
                                <div class="text-center py-8 text-muted">
                                    <i class="fa-solid fa-clipboard-list fs-2x text-muted mb-2 d-block"></i>
                                    {{ __('No workout, nutrition, or progress activity recorded yet.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side: Recent Activity & Quick Share -->
    <div class="col-lg-4">
        <!-- Recent Activity Feed -->
        <div class="card card-flush shadow-sm border-0 mb-6">
            <div class="card-header align-items-center py-5">
                <div class="card-title d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-primary fs-3"></i>
                    <h3 class="card-label fw-bold text-gray-900 fs-4 mb-0">{{ __('Recent System Activity') }}</h3>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="activity-timeline">
                    @forelse($recentActivities as $activity)
                    <div class="position-relative mb-5">
                        <span class="activity-dot"></span>
                        <div class="fs-7 fw-bold text-gray-800">{{ $activity->description }}</div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="text-muted fs-8">{{ $activity->causer?->name ?? __('System') }}</span>
                            <span class="text-muted fs-8">•</span>
                            <span class="text-muted fs-8">{{ $activity->created_at ? $activity->created_at->diffForHumans() : '' }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-muted fs-7">
                        <i class="fa-solid fa-bell-slash fs-2x text-muted mb-2 d-block"></i>
                        {{ __('No recent activities recorded.') }}
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Share Website Card -->
        <div class="card card-flush shadow-sm border-0">
            <div class="card-header align-items-center py-4">
                <div class="card-title d-flex align-items-center gap-2">
                    <i class="fa-solid fa-share-nodes text-info fs-4"></i>
                    <h3 class="card-label fw-bold text-gray-900 fs-5 mb-0">{{ __('Share Portal Link') }}</h3>
                </div>
            </div>
            <div class="card-body pt-0">
                <p class="text-muted fs-7 mb-3">
                    {{ __('Share this direct link on social media profiles (Instagram, TikTok) for prospective trainees.') }}
                </p>
                <div class="input-group mb-3">
                    <input type="text" id="coach-portal-url" class="form-control form-control-sm form-control-solid bg-light"
                           value="{{ $websiteUrl ?? url('/') }}" readonly>
                    <button class="btn btn-sm btn-primary" type="button" onclick="copyPortalUrl()">
                        <i class="fa-regular fa-copy me-1"></i> {{ __('Copy') }}
                    </button>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted fs-8">
                    <i class="fa-solid fa-shield-halved text-success"></i>
                    <span>{{ __('SSL Secured & Fast CDN Enabled') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Section: Action Items Hub & Recent Activity-->
@endsection

@section('scripts')
<script>
    function copyPortalUrl() {
        var copyText = document.getElementById("coach-portal-url");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);

        Swal.fire({
            text: "{{ __('Portal link copied to clipboard!') }}",
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: "{{ __('OK') }}",
            customClass: {
                confirmButton: "btn btn-primary"
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Theme mode detection for charts
        var isDarkMode = document.documentElement.getAttribute("data-bs-theme") === "dark";
        var textColor = isDarkMode ? "#92929f" : "#5e6278";
        var borderColor = isDarkMode ? "rgba(255, 255, 255, 0.08)" : "#e4e6ef";

        // 1. Trainee Monthly Growth Chart (ApexCharts)
        var growthCategories = @json($monthlyGrowth['categories'] ?? []);
        var growthSeries = @json($monthlyGrowth['series'] ?? []);

        var growthOptions = {
            series: [{
                name: "{{ __('New Trainees') }}",
                data: growthSeries
            }],
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: ['#3699ff']
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100],
                    colorStops: [
                        { offset: 0, color: '#3699ff', opacity: 0.4 },
                        { offset: 100, color: '#3699ff', opacity: 0.0 }
                    ]
                }
            },
            xaxis: {
                categories: growthCategories,
                labels: {
                    style: { colors: textColor, fontSize: '12px' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: textColor, fontSize: '12px' },
                    formatter: function (val) { return Math.round(val); }
                }
            },
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4
            },
            tooltip: {
                theme: isDarkMode ? 'dark' : 'light',
                y: {
                    formatter: function (val) {
                        return val + " {{ __('Trainees') }}";
                    }
                }
            }
        };

        if (document.querySelector("#trainee_growth_chart") && typeof ApexCharts !== 'undefined') {
            var growthChart = new ApexCharts(document.querySelector("#trainee_growth_chart"), growthOptions);
            growthChart.render();
        }

        // 2. Subscription Status Distribution Donut Chart
        var subData = [
            {{ $subscriptionBreakdown['active'] ?? 0 }},
            {{ $subscriptionBreakdown['expiring_soon'] ?? 0 }},
            {{ $subscriptionBreakdown['expired'] ?? 0 }},
            {{ $subscriptionBreakdown['inactive'] ?? 0 }}
        ];

        var donutOptions = {
            series: subData,
            labels: [
                "{{ __('Active') }}",
                "{{ __('Expiring (7d)') }}",
                "{{ __('Expired') }}",
                "{{ __('Inactive') }}"
            ],
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'inherit'
            },
            colors: ['#50cd89', '#ffc700', '#f1416c', '#a1a5b7'],
            legend: {
                position: 'bottom',
                labels: { colors: textColor }
            },
            dataLabels: { enabled: true },
            stroke: {
                colors: [isDarkMode ? '#1e1e2d' : '#ffffff'],
                width: 2
            },
            tooltip: {
                theme: isDarkMode ? 'dark' : 'light'
            }
        };

        if (document.querySelector("#subscription_breakdown_chart") && typeof ApexCharts !== 'undefined') {
            var donutChart = new ApexCharts(document.querySelector("#subscription_breakdown_chart"), donutOptions);
            donutChart.render();
        }
    });
</script>
@endsection
