@extends('system.layout')

@section('header')
    <style>
        /* ==========================================================================
           GYM TRENDY UI/UX - TRAINEE PROFILE & WORKOUT EDITOR
           ========================================================================== */
        :root {
            --gym-neon-lime: #ccff00;
            --gym-electric-blue: #009ef7;
            --gym-purple-glow: #7239ea;
            --gym-dark-card: #1e1e2d;
            --gym-dark-bg: #151521;
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
            color: #fff;
            letter-spacing: -0.5px;
        }

        .stat-box-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.55);
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
            background: var(--gym-dark-card);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            color: #fff;
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
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.07);
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
            background: rgba(30, 30, 45, 0.6);
            border: 1px solid rgba(255, 168, 0, 0.15);
            border-radius: 16px;
            overflow: hidden;
        }

        .archived-toggle-btn {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
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
            color: #ffa800;
        }

        .archived-toggle-btn .toggle-icon {
            transition: transform 0.3s ease;
        }

        .archived-toggle-btn[aria-expanded="true"] .toggle-icon {
            transform: rotate(180deg);
        }

        .archived-day-pill {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            opacity: 0.85;
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
            color: rgba(255, 255, 255, 0.4);
            padding: 10px 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .archived-exercise-table tbody td {
            padding: 10px 12px;
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        }

        .archived-exercise-table tbody tr:last-child td {
            border-bottom: none;
        }

        .archived-date-label {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.35);
            font-style: italic;
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
        <span class="d-inline-block">
            {{ edit_links('system.trainee.edit', route('system.trainee.edit', $result->user_id)) }}
        </span>

        <a href="{{ route('system.nutrition.create', ['trainee' => Crypt::encrypt($result->id)]) }}" class="btn btn-sm btn-light-success fw-bold rounded-3 d-flex align-items-center gap-2">
            <i class="fa-solid fa-apple-whole fs-4"></i>
            <span>{{ __('Add Nutrition Plan') }}</span>
        </a>

        @if($result->activeWorkoutPlans->count() > 0)
            <form action="{{ route('system.trainee.reset-plan', $result->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to archive the current workout plan for this trainee? This action cannot be undone.') }}')">
                @csrf
                <button type="submit" class="btn btn-sm btn-light-danger fw-bold rounded-3 d-flex align-items-center gap-1">
                    <i class="ki-duotone ki-trash fs-4"></i>
                    <span>{{ __('Archive Workout Plan') }}</span>
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
                <img class="rounded-4" src="{{ asset('assets/media/avatars/blank.png') }}" style="border: 2px solid var(--gym-neon-lime); width: 100px; height: 100px; object-fit: cover;" alt="avatar" />
                <div class="position-absolute bottom-0 end-0 rounded-circle border border-dark w-20px h-20px {{ $result->status == 1 ? 'bg-success' : 'bg-danger' }}" title="{{ $result->status == 1 ? __('Active') : __('In-Active') }}"></div>
            </div>

            <!-- Details -->
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-white fs-1 fw-bold">{{ $result->user->name ?? '' }}</span>
                        <span class="badge {{ $result->status == 1 ? 'badge-gym-neon' : 'badge-light-danger' }}">
                            {{ $result->status == 1 ? __('Active Trainee') : __('In-Active') }}
                        </span>
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
                        <div class="trainee-stat-box text-center" style="border-color: rgba(204, 255, 0, 0.3);">
                            <div class="stat-box-label mb-1">{{ __('Weight') }}</div>
                            <div class="stat-box-value" style="color: var(--gym-neon-lime);">{{ $result->weight ?? '-' }} <span class="fs-8 text-gray-500">KG</span></div>
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
                            <div class="stat-box-value fs-5 text-gray-300">{{ $result->membership_start ?? '-' }}</div>
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
        <div class="tab-pane fade show active" id="kt_staff_programs_tab" role="tabpanel">
            {{-- ===== ACTIVE WORKOUT PLANS ===== --}}
            @if($result->activeWorkoutPlans->count() > 0)
                <div class="card mb-8 rounded-4 shadow-sm" style="background: var(--gym-dark-card); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="card-header border-bottom border-gray-800 pt-6 pb-5">
                        <div class="card-title">
                            <h2 class="text-white fw-bold m-0 d-flex align-items-center gap-2">
                                <i class="fas fa-dumbbell" style="color: var(--gym-neon-lime);"></i>
                                <span>{{ __('Current Workout Program') }}</span>
                                <span class="badge badge-gym-neon fs-8 ms-2">{{ __('Active') }}</span>
                            </h2>
                        </div>
                    </div>
                    <div class="card-body p-6 p-lg-8">
                        <!-- Days Nav Tabs -->
                        <ul class="nav nav-pills nav-pills-custom mb-8 d-flex flex-wrap gap-4" id="pills-tab" role="tablist">
                            @foreach($result->activeWorkoutPlans as $index => $plan)
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link d-flex flex-column flex-center p-5 text-center {{ $index == 0 ? 'active' : '' }}"
                                       data-bs-toggle="pill"
                                       href="#plan_day_{{ $plan->id }}"
                                       style="min-width: 140px; min-height: 110px;">
                                        <div class="nav-icon mb-3">
                                            <i class="fas fa-calendar-day fs-2x text-info"></i>
                                        </div>
                                        <div>
                                            <span class="day-title-text fw-bold fs-5 d-block">{{ $plan->day_name }}</span>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Days Content -->
                        <div class="tab-content" id="pills-tabContent">
                            @foreach($result->activeWorkoutPlans as $index => $plan)
                                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="plan_day_{{ $plan->id }}" role="tabpanel">

                                    {!! Form::open(['url' => route('system.workout.updateDay', $plan->id), 'method' => 'POST', 'class' => 'workout-day-form']) !!}

                                    <div class="p-6 rounded-4" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.06);">
                                        <div class="d-flex justify-content-between align-items-center mb-6 pb-4 border-bottom border-gray-800">
                                            <h3 class="fw-bolder text-white m-0 d-flex align-items-center gap-2">
                                                <span class="badge badge-gym-neon">{{ $plan->day_name }}</span>
                                                <span>@lang('Exercises List')</span>
                                            </h3>
                                        </div>

                                        <div id="exercises-container-day-{{ $plan->id }}" data-exercise-index="{{ $plan->exercises->count() > 0 ? $plan->exercises->count() : 1 }}">

                                            @if($plan->exercises->count() > 0)
                                                @foreach($plan->exercises as $exIndex => $exercise)
                                                    @php $currentExIdx = $exIndex + 1; @endphp
                                                    <div class="exercise-row exercise-row-card align-items-end">
                                                        <input type="hidden" name="exercises[ex_{{ $currentExIdx }}][exercise_id]" value="{{ $exercise->id }}">

                                                        <div class="row g-4 align-items-end">
                                                            <div class="col-12 col-md-3">
                                                                <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Exercise')</label>
                                                                <input type="text" name="exercises[ex_{{ $currentExIdx }}][name]" value="{{ $exercise->name }}" class="form-control form-control-solid" required>
                                                            </div>
                                                            <div class="col-6 col-md-1">
                                                                <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Sets')</label>
                                                                <input type="text" name="exercises[ex_{{ $currentExIdx }}][sets]" value="{{ $exercise->sets }}" class="form-control form-control-solid text-center">
                                                            </div>
                                                            <div class="col-6 col-md-1">
                                                                <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Reps')</label>
                                                                <input type="text" name="exercises[ex_{{ $currentExIdx }}][reps]" value="{{ $exercise->reps }}" class="form-control form-control-solid text-center">
                                                            </div>
                                                            <div class="col-6 col-md-1">
                                                                <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Rest')</label>
                                                                <input type="text" name="exercises[ex_{{ $currentExIdx }}][rest]" value="{{ $exercise->rest }}" class="form-control form-control-solid text-center">
                                                            </div>
                                                            <div class="col-6 col-md-1">
                                                                <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Weight')</label>
                                                                <input type="text" name="exercises[ex_{{ $currentExIdx }}][weight]" value="{{ $exercise->internal_weight }}" class="form-control form-control-solid text-center">
                                                            </div>
                                                            <div class="col-6 col-md-1">
                                                                <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Tempo')</label>
                                                                <input type="text" name="exercises[ex_{{ $currentExIdx }}][tempo]" value="{{ $exercise->tempo }}" class="form-control form-control-solid text-center">
                                                            </div>
                                                            <div class="col-12 col-md-3">
                                                                <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Video Tutorial Link')</label>
                                                                <input type="url" name="exercises[ex_{{ $currentExIdx }}][link]" value="{{ $exercise->link }}" class="form-control form-control-solid" placeholder="https://youtube.com/...">
                                                            </div>
                                                            <div class="col-12 col-md-1 d-flex justify-content-end">
                                                                <button type="button" class="btn btn-icon btn-light-danger delete-exercise-btn w-100 rounded-3" title="@lang('Delete Exercise')">
                                                                    <i class="fas fa-trash fs-5"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="exercise-row exercise-row-card align-items-end">
                                                    <div class="row g-4 align-items-end">
                                                        <div class="col-12 col-md-3">
                                                            <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Exercise')</label>
                                                            <input type="text" name="exercises[ex_1][name]" class="form-control form-control-solid" required>
                                                        </div>
                                                        <div class="col-6 col-md-1">
                                                            <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Sets')</label>
                                                            <input type="text" name="exercises[ex_1][sets]" class="form-control form-control-solid text-center">
                                                        </div>
                                                        <div class="col-6 col-md-1">
                                                            <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Reps')</label>
                                                            <input type="text" name="exercises[ex_1][reps]" class="form-control form-control-solid text-center">
                                                        </div>
                                                        <div class="col-6 col-md-1">
                                                            <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Rest')</label>
                                                            <input type="text" name="exercises[ex_1][rest]" class="form-control form-control-solid text-center">
                                                        </div>
                                                        <div class="col-6 col-md-1">
                                                            <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Weight')</label>
                                                            <input type="text" name="exercises[ex_1][weight]" class="form-control form-control-solid text-center">
                                                        </div>
                                                        <div class="col-6 col-md-1">
                                                            <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Tempo')</label>
                                                            <input type="text" name="exercises[ex_1][tempo]" class="form-control form-control-solid text-center">
                                                        </div>
                                                        <div class="col-12 col-md-3">
                                                            <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Video Tutorial Link')</label>
                                                            <input type="url" name="exercises[ex_1][link]" class="form-control form-control-solid" placeholder="https://youtube.com/...">
                                                        </div>
                                                        <div class="col-12 col-md-1 d-flex justify-content-end">
                                                            <button type="button" class="btn btn-icon btn-light-danger delete-exercise-btn w-100 rounded-3" title="@lang('Delete Exercise')">
                                                                <i class="fas fa-trash fs-5"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="d-flex flex-wrap justify-content-between align-items-center mt-6 pt-4 border-top border-gray-800 gap-4">
                                            <button type="button" class="btn btn-light-primary rounded-3 add-exercise-btn d-flex align-items-center gap-2 fw-bold px-5 py-3" data-day-id="{{ $plan->id }}">
                                                <i class="fas fa-plus fs-5"></i>
                                                <span>@lang('Add Exercise')</span>
                                            </button>

                                            <button type="submit" class="btn btn-gym-neon d-flex align-items-center gap-2 px-6 py-3">
                                                <i class="fas fa-save fs-4 text-dark"></i>
                                                <span>@lang('Update Day')</span>
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
                <div class="card rounded-4 shadow-sm mb-8" style="background: var(--gym-dark-card); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="card-body text-center py-12">
                        <div class="symbol symbol-100px bg-light-primary rounded-circle mb-6 d-inline-flex flex-center">
                            <i class="fas fa-dumbbell fs-1x text-primary"></i>
                        </div>
                        <h3 class="text-white fw-bolder mb-3 fs-2">@lang('No Workout Program Assigned')</h3>
                        <p class="text-gray-400 fs-5 mb-8 max-w-500px mx-auto">@lang('This trainee does not currently have an active workout plan.')</p>
                        <a href="{{ route('system.workout.create', ['trainee' => Crypt::encrypt($result->id)]) }}" class="btn btn-gym-neon px-6 py-3">
                            <i class="ki-duotone ki-plus fs-2 text-dark me-1"></i> @lang('Assign Program')
                        </a>
                    </div>
                </div>
            @endif

            {{-- ===== ARCHIVED WORKOUT HISTORY ===== --}}
            @if($result->archivedWorkoutPlans->count() > 0)
                <div class="archived-history-card mt-6">
                    <div class="p-5">
                        <button class="archived-toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#archivedPlansCollapse" aria-expanded="false" aria-controls="archivedPlansCollapse">
                            <i class="fas fa-chevron-down toggle-icon" style="color: #ffa800;"></i>
                            <i class="fas fa-archive" style="color: #ffa800;"></i>
                            <span>{{ __('Workout History') }}</span>
                            <span class="badge badge-archived ms-2">{{ $result->archivedWorkoutPlans->count() }} {{ __('archived days') }}</span>
                        </button>
                    </div>

                    <div class="collapse" id="archivedPlansCollapse">
                        <div class="px-5 pb-5">
                            <div class="border-top pt-5" style="border-color: rgba(255,255,255,0.06) !important;">
                                @php
                                    $archivedGrouped = $result->archivedWorkoutPlans->groupBy(function($plan) {
                                        return $plan->updated_at ? $plan->updated_at->format('Y-m-d') : 'unknown';
                                    });
                                @endphp

                                @foreach($archivedGrouped as $archivedDate => $archivedPlans)
                                    <div class="mb-6">
                                        <div class="d-flex align-items-center gap-2 mb-4">
                                            <i class="fas fa-clock" style="color: rgba(255,168,0,0.5);"></i>
                                            <span class="archived-date-label">
                                                {{ __('Archived on') }}: {{ $archivedDate !== 'unknown' ? \Carbon\Carbon::parse($archivedDate)->format('M d, Y') : __('Unknown date') }}
                                            </span>
                                        </div>

                                        @foreach($archivedPlans as $archivedPlan)
                                            <div class="archived-day-pill">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="fas fa-calendar-day" style="color: rgba(255,255,255,0.3);"></i>
                                                        <span class="fw-bold" style="color: rgba(255,255,255,0.7);">{{ $archivedPlan->day_name }}</span>
                                                        <span class="badge badge-archived">{{ __('Archived') }}</span>
                                                    </div>
                                                    @if($archivedPlan->exercises->count() > 0)
                                                        <span style="color: rgba(255,255,255,0.3); font-size: 0.8rem;">{{ $archivedPlan->exercises->count() }} {{ __('exercises') }}</span>
                                                    @endif
                                                </div>

                                                @if($archivedPlan->warmup)
                                                    <div class="mb-3 px-3 py-2 rounded-3" style="background: rgba(255,193,7,0.06); border-left: 3px solid rgba(255,193,7,0.3);">
                                                        <small class="fw-bold" style="color: rgba(255,193,7,0.6);"><i class="fas fa-fire me-1"></i>{{ __('Warm Up') }}:</small>
                                                        <span style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">{{ $archivedPlan->warmup }}</span>
                                                    </div>
                                                @endif

                                                @if($archivedPlan->exercises->count() > 0)
                                                    <table class="archived-exercise-table">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>{{ __('Exercise') }}</th>
                                                                <th>{{ __('Sets') }}</th>
                                                                <th>{{ __('Reps') }}</th>
                                                                <th>{{ __('Rest') }}</th>
                                                                <th>{{ __('Weight') }}</th>
                                                                <th>{{ __('Tempo') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($archivedPlan->exercises as $exIdx => $exercise)
                                                                <tr>
                                                                    <td>{{ $exIdx + 1 }}</td>
                                                                    <td style="color: rgba(255,255,255,0.7); font-weight: 600;">{{ $exercise->name }}</td>
                                                                    <td>{{ $exercise->sets ?? '—' }}</td>
                                                                    <td>{{ $exercise->reps ?? '—' }}</td>
                                                                    <td>{{ $exercise->rest ?? '—' }}</td>
                                                                    <td>{{ $exercise->internal_weight ?? '—' }}</td>
                                                                    <td>{{ $exercise->tempo ?? '—' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @else
                                                    <p class="mb-0" style="color: rgba(255,255,255,0.3); font-size: 0.85rem;"><em>{{ __('No exercises recorded for this day.') }}</em></p>
                                                @endif

                                                @if($archivedPlan->post_workout)
                                                    <div class="mt-3 px-3 py-2 rounded-3" style="background: rgba(114,57,234,0.06); border-left: 3px solid rgba(114,57,234,0.3);">
                                                        <small class="fw-bold" style="color: rgba(114,57,234,0.6);"><i class="fas fa-flag-checkered me-1"></i>{{ __('Post Workout') }}:</small>
                                                        <span style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">{{ $archivedPlan->post_workout }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
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
                            <a href="{{ route('system.nutrition.edit', $activeNutrition->id) }}" class="btn btn-sm btn-light-primary fw-bold rounded-3">
                                <i class="fa-solid fa-pen-to-square me-1"></i>@lang('Edit Plan')
                            </a>
                            <a href="{{ route('system.nutrition.create', ['trainee' => Crypt::encrypt($result->id)]) }}" class="btn btn-sm btn-success fw-bold rounded-3">
                                <i class="fa-solid fa-plus me-1"></i>@lang('New Plan')
                            </a>
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

        <!-- HISTORY / LOGS TAB -->
        <div class="tab-pane fade" id="kt_staff_logs_tab" role="tabpanel">
            <div class="card rounded-4 shadow-sm mb-8" style="background: var(--gym-dark-card); border: 1px solid rgba(255,255,255,0.08);">
                <div class="card-header border-bottom border-gray-800 pt-6 pb-5">
                    <div class="card-title">
                        <h2 class="text-white fw-bold m-0">{{ __('History (Audit Log)') }}</h2>
                    </div>
                </div>
                <div class="card-body p-6">
                    {{ view('system.datatable', $activityLogData) }}
                </div>
            </div>
        </div>

        <!-- AUTH SESSIONS TAB -->
        <div class="tab-pane fade" id="kt_staff_auth_sessions_tab" role="tabpanel">
            <div class="card rounded-4 shadow-sm mb-8" style="background: var(--gym-dark-card); border: 1px solid rgba(255,255,255,0.08);">
                <div class="card-header border-bottom border-gray-800 pt-6 pb-5">
                    <div class="card-title">
                        <h2 class="text-white fw-bold m-0">{{ __('Auth Sessions') }}</h2>
                    </div>
                </div>
                <div class="card-body p-6">
                    {{ view('system.datatable', $authSessionData) }}
                </div>
            </div>
        </div>

    </div>

@endsection

@section('footer')
    <script type="text/javascript">
        $(document).ready(function() {
            // Add new exercise
            $(document).on('click', '.add-exercise-btn', function() {
                var dayId = $(this).data('day-id');
                var container = $(`#exercises-container-day-${dayId}`);

                var currentExerciseIdx = parseInt(container.attr('data-exercise-index')) + 1;
                container.attr('data-exercise-index', currentExerciseIdx);

                var newExerciseRow = `
                <div class="exercise-row exercise-row-card align-items-end" style="display:none;">
                    <div class="row g-4 align-items-end">
                        <div class="col-12 col-md-3">
                            <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Exercise')</label>
                            <input type="text" name="exercises[ex_${currentExerciseIdx}][name]" class="form-control form-control-solid" required>
                        </div>
                        <div class="col-6 col-md-1">
                            <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Sets')</label>
                            <input type="text" name="exercises[ex_${currentExerciseIdx}][sets]" class="form-control form-control-solid text-center">
                        </div>
                        <div class="col-6 col-md-1">
                            <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Reps')</label>
                            <input type="text" name="exercises[ex_${currentExerciseIdx}][reps]" class="form-control form-control-solid text-center">
                        </div>
                        <div class="col-6 col-md-1">
                            <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Rest')</label>
                            <input type="text" name="exercises[ex_${currentExerciseIdx}][rest]" class="form-control form-control-solid text-center">
                        </div>
                        <div class="col-6 col-md-1">
                            <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Weight')</label>
                            <input type="text" name="exercises[ex_${currentExerciseIdx}][weight]" class="form-control form-control-solid text-center">
                        </div>
                        <div class="col-6 col-md-1">
                            <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Tempo')</label>
                            <input type="text" name="exercises[ex_${currentExerciseIdx}][tempo]" class="form-control form-control-solid text-center">
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fs-7 fw-bold text-gray-300 mb-2">@lang('Video Tutorial Link')</label>
                            <input type="url" name="exercises[ex_${currentExerciseIdx}][link]" class="form-control form-control-solid" placeholder="https://youtube.com/...">
                        </div>
                        <div class="col-12 col-md-1 d-flex justify-content-end">
                            <button type="button" class="btn btn-icon btn-light-danger delete-exercise-btn w-100 rounded-3" title="@lang('Delete Exercise')">
                                <i class="fas fa-trash fs-5"></i>
                            </button>
                        </div>
                    </div>
                </div>`;

                container.append(newExerciseRow);
                container.find('.exercise-row').last().slideDown(250);
            });

            // Delete exercise
            $(document).on('click', '.delete-exercise-btn', function() {
                var row = $(this).closest('.exercise-row');
                var container = $(this).closest('[id^="exercises-container-day-"]');

                if (container.find('.exercise-row').length > 1) {
                    row.slideUp(200, function() {
                        $(this).remove();
                    });
                } else {
                    container.find('.exercise-row input:not([type=hidden])').val('');
                }
            });

            // Handle form submission via ajax
            $('.workout-day-form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var btn = form.find('button[type="submit"]');
                var originalText = btn.html();

                btn.html('<i class="fas fa-spinner fa-spin me-1"></i> @lang("Saving...")').prop('disabled', true);

                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: form.serialize(),
                    success: function(response) {
                        btn.html(originalText).prop('disabled', false);
                        if(typeof FormAlert === "function") {
                            FormAlert('success', 'Day updated successfully!');
                        } else {
                            alert('Day updated successfully!');
                        }
                    },
                    error: function(xhr) {
                        btn.html(originalText).prop('disabled', false);
                        alert('Error updating day. Please check inputs.');
                    }
                });
            });
        });
    </script>
@endsection

