@extends('system.layout')

@section('content')
<div class="d-flex flex-column gap-7 gap-lg-10">
    <!-- Header -->
    <div class="card card-flush shadow-sm">
        <div class="card-header align-items-center py-5">
            <div class="card-title">
                <div class="d-flex align-items-center gap-3">
                    <span class="symbol symbol-45px symbol-circle bg-light-success">
                        <i class="fab fa-whatsapp text-success fs-2x p-3"></i>
                    </span>
                    <div>
                        <h2 class="fs-2 fw-bold mb-0">{{ __('WhatsApp Broadcast Campaigns') }}</h2>
                        <span class="text-muted fs-7">{{ __('Manual Click-to-Chat campaign manager using official WhatsApp links.') }}</span>
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('system.whatsapp-broadcast.create') }}" class="btn btn-sm btn-success">
                    <i class="ki-outline ki-plus fs-4 me-1"></i>
                    {{ __('New Broadcast Campaign') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Campaigns List -->
    <div class="card card-flush shadow-sm">
        <div class="card-body p-0">
            @if($campaigns->isEmpty())
                <div class="text-center py-15">
                    <div class="symbol symbol-70px symbol-circle bg-light-success mb-4">
                        <i class="fab fa-whatsapp fs-3x text-success p-4"></i>
                    </div>
                    <h3 class="fs-3 fw-bold text-gray-800">{{ __('No broadcast campaigns yet') }}</h3>
                    <p class="text-muted fs-6 mb-5">{{ __('Start your first manual WhatsApp campaign to communicate announcements or renewal reminders with trainees.') }}</p>
                    <a href="{{ route('system.whatsapp-broadcast.create') }}" class="btn btn-success">
                        <i class="ki-outline ki-plus fs-4 me-1"></i>
                        {{ __('Create Your First Campaign') }}
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-dashed align-middle gs-6 gy-5 mb-0">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th>#</th>
                                <th>{{ __('Campaign Title') }}</th>
                                <th>{{ __('Coach') }}</th>
                                <th>{{ __('Recipients') }}</th>
                                <th>{{ __('Progress & Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campaigns as $campaign)
                                @php
                                    $statusBadge = match($campaign->status) {
                                        'completed' => 'badge-light-success',
                                        'in_progress' => 'badge-light-primary',
                                        'cancelled' => 'badge-light-danger',
                                        default => 'badge-light-secondary'
                                    };
                                    $statusLabel = match($campaign->status) {
                                        'completed' => __('Completed'),
                                        'in_progress' => __('In Progress'),
                                        'cancelled' => __('Cancelled'),
                                        default => __('Draft')
                                    };
                                    $processed = $campaign->confirmed_count + $campaign->skipped_count;
                                    $total = max(1, $campaign->total_recipients);
                                    $percent = min(100, (int) round(($processed / $total) * 100));
                                @endphp
                                <tr>
                                    <td>{{ $campaign->id }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <a href="{{ route('system.whatsapp-broadcast.show', $campaign->id) }}" class="text-gray-900 fw-bold text-hover-primary fs-6">
                                                {{ $campaign->title }}
                                            </a>
                                            <span class="text-muted fs-8 text-truncate" style="max-width: 280px;">
                                                {{ $campaign->message_template }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-gray-800 fw-semibold fs-7">{{ $campaign->coach?->name ?? __('Unknown') }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge badge-light fw-bold fs-7">{{ $campaign->total_recipients }} {{ __('recipients') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1" style="min-width: 140px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge {{ $statusBadge }} fw-semibold fs-8">{{ $statusLabel }}</span>
                                                <span class="text-muted fs-8">{{ $percent }}%</span>
                                            </div>
                                            <div class="progress h-6px w-100 bg-light">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%"></div>
                                            </div>
                                            <div class="d-flex gap-2 fs-9 text-muted mt-1">
                                                <span class="text-success"><i class="ki-outline ki-check-circle fs-8 text-success me-1"></i>{{ $campaign->confirmed_count }}</span>
                                                <span class="text-warning"><i class="ki-outline ki-eye fs-8 text-warning me-1"></i>{{ $campaign->opened_count }}</span>
                                                <span class="text-secondary"><i class="ki-outline ki-cross-circle fs-8 text-secondary me-1"></i>{{ $campaign->skipped_count }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fs-7">{{ $campaign->created_at->format('Y-m-d') }}</span>
                                        <div class="text-gray-400 fs-8">{{ $campaign->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="text-end">
                                        @if($campaign->status === 'in_progress' || $campaign->status === 'draft')
                                            <a href="{{ route('system.whatsapp-broadcast.process', $campaign->id) }}" class="btn btn-sm btn-success">
                                                <i class="fab fa-whatsapp me-1"></i>
                                                {{ __('Continue') }}
                                            </a>
                                        @else
                                            <a href="{{ route('system.whatsapp-broadcast.show', $campaign->id) }}" class="btn btn-sm btn-light">
                                                <i class="ki-outline ki-eye me-1"></i>
                                                {{ __('Details') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($campaigns->hasPages())
                    <div class="card-footer py-4 d-flex justify-content-center">
                        {{ $campaigns->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

