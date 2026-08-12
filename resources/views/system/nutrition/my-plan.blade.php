@extends('system.layout')

@section('header')
<style>
    /* ===== Trainee Nutrition Portal Styling ===== */
    .nutrition-welcome-banner {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        padding: 2.25rem 2.5rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 52, 96, 0.2);
    }

    .nutrition-welcome-banner::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -15%;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(80, 205, 137, 0.15) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* ===== CKEditor Inner HTML Content Adaptation ===== */
    .nutrition-content {
        line-height: 1.8;
        font-size: 1.02rem;
    }
    .nutrition-content p {
        color: inherit;
        margin-bottom: 0.85rem;
    }
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
    .nutrition-content ul, .nutrition-content ol {
        padding-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .nutrition-content li {
        margin-bottom: 0.35rem;
    }
    .nutrition-content table {
        width: 100%;
        margin-bottom: 1.25rem;
        border-collapse: collapse;
    }
    .nutrition-content th, .nutrition-content td {
        padding: 0.75rem 1rem;
        border: 1px solid var(--bs-gray-300, #e4e6ef);
    }
    [data-bs-theme="dark"] .nutrition-content th,
    [data-bs-theme="dark"] .nutrition-content td {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    .nutrition-content th {
        background: var(--bs-gray-100, #f8f9fa);
        font-weight: 700;
    }
    [data-bs-theme="dark"] .nutrition-content th {
        background: rgba(255, 255, 255, 0.05) !important;
    }
    .nutrition-content blockquote {
        border-left: 4px solid #50cd89;
        padding: 0.75rem 1.25rem;
        background: var(--bs-gray-100, #f8f9fa);
        margin-bottom: 1rem;
        border-radius: 0 8px 8px 0;
    }
    [data-bs-theme="dark"] .nutrition-content blockquote {
        background: rgba(255, 255, 255, 0.04) !important;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(80, 205, 137, 0.1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .nutrition-welcome-banner { padding: 1.5rem; }
    }
</style>
@endsection

@section('content')

    <!-- Welcome Banner -->
    <div class="nutrition-welcome-banner mb-8">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-4">
            <div>
                <h1 class="text-white fw-bolder fs-1 mb-2">
                    {{ __('Welcome') }}, {{ $user->name }} 🥗
                </h1>
                <p class="text-gray-300 fs-6 mb-0">
                    {{ __('Here is your active customized nutrition plan.') }}
                </p>
            </div>

            @if($plan)
                <div class="d-flex align-items-center gap-3">
                    <span class="badge badge-light-success fs-7 fw-bold px-4 py-2 rounded-pill">
                        <i class="fa-solid fa-circle-check text-success me-1"></i> {{ __('Active Plan') }}
                    </span>
                </div>
            @endif
        </div>
    </div>

    @if($trainee && ($trainee->status == 'expired' || $trainee->status == 'inactive'))
        <!-- Expired State -->
        <div class="card card-flush border border-danger border-2 shadow-sm">
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-state-icon bg-light-danger">
                        <i class="fa-solid fa-triangle-exclamation text-danger fs-1"></i>
                    </div>
                    <h3 class="text-danger fw-bolder mb-2">{{ __('Membership Inactive or Expired') }}</h3>
                    <p class="text-gray-600 fs-6 mb-0">{{ __('Your subscription has expired or is inactive. Please renew your membership to access your assigned nutrition program.') }}</p>
                </div>
            </div>
        </div>
    @elseif($plan)

        <!-- Active Plan Card -->
        <div class="card card-flush shadow-sm mb-8">
            <div class="card-header pt-7 pb-5 border-bottom border-gray-200">
                <div class="card-title">
                    <h3 class="fw-bolder text-gray-900 fs-2 mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-apple-whole text-success"></i>
                        <span>{{ $plan->name ? $plan->name : __('My Nutrition Plan') }}</span>
                    </h3>
                </div>
                <div class="card-toolbar text-gray-500 fs-7">
                    <i class="fa-regular fa-clock me-1"></i>{{ $plan->created_at ? $plan->created_at->format('Y-m-d') : '' }}
                </div>
            </div>

            <div class="card-body p-8">
                <div class="nutrition-content text-gray-800 fs-6 lh-base">
                    @if($plan->description)
                        {!! $plan->description !!}
                    @else
                        <div class="text-gray-500 text-center py-5">
                            {{ __('Your nutrition plan has no written details yet.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    @else
        <!-- No Plan Assigned State -->
        <div class="card card-flush shadow-sm">
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fa-solid fa-apple-whole text-success fs-1"></i>
                    </div>
                    <h3 class="fw-bolder text-gray-900 mb-2">{{ __('No Active Nutrition Plan Yet') }}</h3>
                    <p class="text-gray-600 fs-6 mb-0">{{ __('Your trainer has not assigned an active nutrition plan to you yet. Please check back later.') }}</p>
                </div>
            </div>
        </div>
    @endif

@endsection
