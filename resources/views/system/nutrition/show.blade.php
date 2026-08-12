@extends('system.layout')

@section('header')
<style>
    /* ===== CKEditor HTML Content Theme Adaptation ===== */
    .nutrition-content {
        line-height: 1.8;
        font-size: 1rem;
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
</style>
@endsection

@section('content')

<div class="mb-7 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('system.nutrition.index') }}" class="btn btn-light-primary btn-sm fw-bold">
            <i class="fa-solid fa-arrow-left me-1"></i>@lang('Back to Nutrition Plans')
        </a>
    </div>
    @if($plan)
        <div class="d-flex align-items-center gap-2">
            <span class="badge badge-light-success fs-7 fw-bolder px-4 py-2 text-uppercase">{{ ucfirst($plan->status) }}</span>
            <a href="{{ route('system.nutrition.edit', $plan->id) }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-pen-to-square me-1"></i>@lang('Edit Plan')
            </a>
        </div>
    @endif
</div>

@if($plan)
    <div class="card card-flush shadow-sm mb-7">
        <div class="card-header pt-7 pb-5 border-bottom border-gray-200">
            <div class="card-title flex-column">
                <div class="text-gray-500 fs-7 fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-user text-primary me-1"></i>
                    <span>{{ $plan->user ? $plan->user->name : '' }}</span>
                </div>
                <h1 class="text-gray-900 fw-bolder fs-2 m-0">{{ $plan->name ? $plan->name : __('Nutrition Plan') }}</h1>
            </div>
            <div class="card-toolbar text-gray-500 fs-7">
                <i class="fa-regular fa-clock me-1"></i>@lang('Assigned'): {{ $plan->created_at ? $plan->created_at->format('Y-m-d H:i') : '' }}
            </div>
        </div>

        <div class="card-body p-8">
            <div class="nutrition-content text-gray-800 fs-6 lh-base">
                @if($plan->description)
                    {!! $plan->description !!}
                @else
                    <div class="text-gray-500 text-center py-5">@lang('No details added for this plan.')</div>
                @endif
            </div>
        </div>
    </div>
@else
    <div class="card card-flush shadow-sm">
        <div class="card-body text-center py-10">
            <h3 class="text-danger fw-bolder">@lang('Plan Not Found')</h3>
        </div>
    </div>
@endif

@endsection
