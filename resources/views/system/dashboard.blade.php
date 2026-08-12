@extends('system.layout')

@section('header')
<style>
    /* Premium Timeline Widget Styling */
    .dashboard-timeline {
        position: relative;
        padding-left: 28px;
    }

    .dashboard-timeline::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 5px;
        bottom: 5px;
        width: 2px;
        background: var(--gp-border);
    }

    .dashboard-timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .dashboard-timeline-item:last-child {
        margin-bottom: 0;
    }

    .dashboard-timeline-badge {
        position: absolute;
        left: -28px;
        top: 3px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--gp-bg-surface) !important;
        border: 3px solid var(--gp-accent);
        z-index: 1;
    }

    .dashboard-timeline-badge.badge-created {
        border-color: #22C55E !important;
    }

    .dashboard-timeline-badge.badge-deleted {
        border-color: #F43F5E !important;
    }

    .dashboard-timeline-badge.badge-updated {
        border-color: #FACC15 !important;
    }

    /* Custom Input-Group highlight matching theme */
    .theme-copy-input {
        border-right: none !important;
    }

    .theme-copy-btn {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
</style>
@endsection

@section('content')
<!--begin::Row-->
<div class="row g-5 g-xl-10 mb-8">
    <!--begin::Col (Total Trainees)-->
    <div class="col-md-6 col-lg-3">
        <div class="card card-flush h-md-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-light-primary text-primary">
                            <i class="fa-solid fa-users fs-2x text-warning"></i>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-800 text-hover-primary fw-bold fs-4">{{ __('Total Trainees') }}</span>
                        <div class="text-muted fs-7 fw-semibold">{{ __('Total registered members') }}</div>
                    </div>
                </div>
                <div class="d-flex flex-column mt-5">
                    <span class="fs-2hx fw-bold text-gray-900 lh-1 ls-n2">{{ $totalTrainees }}</span>
                </div>
            </div>
        </div>
    </div>
    <!--end::Col-->

    <!--begin::Col (Pending Workouts)-->
    <div class="col-md-6 col-lg-3">
        <div class="card card-flush h-md-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-light-danger text-danger">
                            <i class="fa-solid fa-triangle-exclamation fs-2x text-danger"></i>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-800 text-hover-primary fw-bold fs-4">{{ __('Pending Workouts') }}</span>
                        <div class="text-muted fs-7 fw-semibold">{{ __('Missing workout plans') }}</div>
                    </div>
                </div>
                <div class="d-flex flex-column mt-5">
                    <span class="fs-2hx fw-bold text-gray-900 lh-1 ls-n2">{{ $pendingWorkouts }}</span>
                </div>
            </div>
        </div>
    </div>
    <!--end::Col-->

    <!--begin::Col (Active Memberships)-->
    <div class="col-md-6 col-lg-3">
        <div class="card card-flush h-md-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-light-success text-success">
                            <i class="fa-solid fa-circle-check fs-2x text-success"></i>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-800 text-hover-primary fw-bold fs-4">{{ __('Active Members') }}</span>
                        <div class="text-muted fs-7 fw-semibold">{{ __('Active subscriptions') }}</div>
                    </div>
                </div>
                <div class="d-flex flex-column mt-5">
                    <span class="fs-2hx fw-bold text-gray-900 lh-1 ls-n2">{{ $activeMemberships }}</span>
                </div>
            </div>
        </div>
    </div>
    <!--end::Col-->

    <!--begin::Col (Total Linktree Clicks)-->
    <div class="col-md-6 col-lg-3">
        <div class="card card-flush h-md-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-between p-6">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-light-info text-info">
                            <i class="fa-solid fa-arrow-pointer fs-2x text-info"></i>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-800 text-hover-primary fw-bold fs-4">{{ __('Website Clicks') }}</span>
                        <div class="text-muted fs-7 fw-semibold">{{ __('Total profile clicks') }}</div>
                    </div>
                </div>
                <div class="d-flex flex-column mt-5">
                    <span class="fs-2hx fw-bold text-gray-900 lh-1 ls-n2">{{ $clicksCount }}</span>
                </div>
            </div>
        </div>
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->

<!--begin::Row-->
<div class="row g-5 g-xl-10">
    <!--begin::Col (Left content - Action Items Table)-->
    <div class="col-lg-8 col-xl-8 mb-5">
        <div class="card card-flush h-xl-100 shadow-sm">
            <!--begin::Card header-->
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    <h3 class="card-label fw-bold text-gray-900 fs-3">{{ __('Action Items') }} <span
                            class="text-muted fs-6 fw-normal">({{ __('Trainees Missing Workout Plan') }})</span></h3>
                </div>
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-50px">{{ __('ID') }}</th>
                                <th class="min-w-150px">{{ __('Trainee') }}</th>
                                <th class="min-w-100px">{{ __('Level') }}</th>
                                <th class="min-w-120px">{{ __('Status') }}</th>
                                <th class="text-end min-w-100px">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @forelse($pendingTrainees as $trainee)
                            <tr>
                                <td>#{{ $trainee->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-circle symbol-35px me-3">
                                            <span class="symbol-label bg-light-warning text-warning fw-bold">
                                                {{ mb_substr($trainee->user?->name ?? 'T', 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <a href="{{ route('system.trainee.show', $trainee->id) }}"
                                                class="text-gray-800 text-hover-primary fw-bold fs-6">
                                                {{ $trainee->user?->name ?? __('N/A') }}
                                            </a>
                                            <span class="text-muted fs-7">{{ $trainee->user?->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary">{{ __($trainee->training_level) }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-light-danger">{{ __($trainee->status) }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('system.workout.create', ['trainee' => \Crypt::encrypt($trainee->id)]) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-plus me-1"></i> {{ __('Assign Plan') }}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 text-muted">
                                    <i class="fa-solid fa-circle-check fs-2x text-success mb-3 d-block"></i>
                                    {{ __('All trainees currently have assigned workout plans!') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!--end::Card body-->
        </div>
    </div>
    <!--end::Col-->

    <!--begin::Col (Right content - Quick Share & Activity Timeline)-->
    <div class="col-lg-4 col-xl-4 mb-5">
        <!--begin::Quick Share Card-->
        <div class="card card-flush mb-6 shadow-sm">
            <div class="card-header py-5">
                <div class="card-title">
                    <h3 class="card-label fw-bold text-gray-900 fs-3">{{ __('Quick Share Linktree') }}</h3>
                </div>
            </div>
            <div class="card-body pt-0">
                <p class="text-muted fs-7 mb-4">{{ __('Share this link tree on social bios (Instagram, TikTok) for trainees to connect.') }}</p>
                <div class="input-group mb-3">
                    <input type="text" id="linktree-url"
                        class="form-control form-control-solid bg-light theme-copy-input" readonly
                        value="{{ $linktreeUrl }}">
                    <button class="btn btn-primary theme-copy-btn" type="button" onclick="copyLinktreeUrl()">
                        <i class="fa-solid fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>
        <!--end::Quick Share Card-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->
@endsection

@section('footer')
<script>
    function copyLinktreeUrl() {
    var copyText = document.getElementById("linktree-url");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);

    Swal.fire({
        text: "{{ __('Link copied to clipboard') }}",
        icon: "success",
        buttonsStyling: false,
        confirmButtonText: "{{ __('OK') }}",
        customClass: {
            confirmButton: "btn btn-primary"
        }
    });
}
</script>
@endsection
