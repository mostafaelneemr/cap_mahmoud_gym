@extends('system.layout')

@section('content')
<div class="d-flex flex-column gap-7 gap-lg-10">
    <!-- Campaign Header & Actions -->
    <div class="card card-flush shadow-sm">
        <div class="card-header align-items-center py-5">
            <div class="card-title">
                <div class="d-flex align-items-center gap-3">
                    <span class="symbol symbol-45px symbol-circle bg-light-success">
                        <i class="fab fa-whatsapp text-success fs-2x p-3"></i>
                    </span>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h2 class="fs-2 fw-bold mb-0">{{ $campaign->title }}</h2>
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
                            @endphp
                            <span class="badge {{ $statusBadge }} fw-bold fs-7">{{ $statusLabel }}</span>
                        </div>
                        <span class="text-muted fs-7">
                            {{ __('Initiated by :name on :date', ['name' => $campaign->coach?->name ?? __('Coach'), 'date' => $campaign->created_at->format('Y-m-d H:i')]) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-toolbar gap-3">
                <a href="{{ route('system.whatsapp-broadcast.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-outline ki-arrow-left fs-4 me-1"></i>
                    {{ __('Back to List') }}
                </a>
                @if($campaign->status === 'in_progress' || $campaign->status === 'draft')
                    <a href="{{ route('system.whatsapp-broadcast.process', $campaign->id) }}" class="btn btn-sm btn-success">
                        <i class="fab fa-whatsapp me-1"></i>
                        {{ __('Resume Sending') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row g-5 g-xl-10">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-flush shadow-sm">
                <div class="card-body p-5 text-center">
                    <span class="fs-2tx fw-bold text-gray-800 d-block">{{ $campaign->total_recipients }}</span>
                    <span class="text-muted fs-7 fw-semibold">{{ __('Total Audience') }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-flush shadow-sm">
                <div class="card-body p-5 text-center bg-light-success bg-opacity-30 border border-success border-dashed rounded">
                    <span class="fs-2tx fw-bold text-success d-block">{{ $campaign->confirmed_count }}</span>
                    <span class="text-success fs-7 fw-semibold">{{ __('Confirmed Sent') }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-flush shadow-sm">
                <div class="card-body p-5 text-center bg-light-warning bg-opacity-30 border border-warning border-dashed rounded">
                    <span class="fs-2tx fw-bold text-warning d-block">{{ $campaign->opened_count }}</span>
                    <span class="text-warning fs-7 fw-semibold">{{ __('Chats Opened') }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-flush shadow-sm">
                <div class="card-body p-5 text-center bg-light-secondary border border-gray-300 border-dashed rounded">
                    <span class="fs-2tx fw-bold text-gray-600 d-block">{{ $campaign->skipped_count }}</span>
                    <span class="text-muted fs-7 fw-semibold">{{ __('Skipped') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Template Used -->
    <div class="card card-flush shadow-sm">
        <div class="card-header py-4">
            <div class="card-title">
                <h4 class="fw-bold mb-0">{{ __('Message Template Used') }}</h4>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="p-4 bg-light rounded border text-gray-800 fs-7" style="white-space: pre-wrap;">{{ $campaign->message_template }}</div>
        </div>
    </div>

    <!-- Recipients Audit Table -->
    <div class="card card-flush shadow-sm">
        <div class="card-header py-4">
            <div class="card-title">
                <h4 class="fw-bold mb-0">{{ __('Recipients Audit & Delivery Log') }} ({{ $campaign->recipients->count() }})</h4>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gs-6 gy-4 mb-0">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th>#</th>
                            <th>{{ __('Recipient') }}</th>
                            <th>{{ __('Phone (Normalized)') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Opened At') }}</th>
                            <th>{{ __('Confirmed At') }}</th>
                            <th class="text-end">{{ __('Quick Link') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaign->recipients as $idx => $recipient)
                            @php
                                $rBadge = match($recipient->status) {
                                    'confirmed' => 'badge-light-success',
                                    'opened' => 'badge-light-warning',
                                    'skipped' => 'badge-light-secondary',
                                    default => 'badge-light-primary'
                                };
                                $rLabel = match($recipient->status) {
                                    'confirmed' => __('Confirmed Sent'),
                                    'opened' => __('Opened in WA'),
                                    'skipped' => __('Skipped'),
                                    default => __('Pending')
                                };
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-900 fw-bold fs-7">{{ $recipient->recipient_name }}</span>
                                        <span class="text-muted fs-8">ID: {{ $recipient->trainee_id }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-success font-monospace fs-7">+{{ $recipient->phone_normalized }}</span>
                                        <span class="text-muted fs-8">{{ $recipient->phone_raw }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $rBadge }} fw-semibold fs-8">{{ $rLabel }}</span>
                                </td>
                                <td>
                                    <span class="text-muted fs-8">{{ $recipient->opened_at ? $recipient->opened_at->format('Y-m-d H:i:s') : '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted fs-8">{{ $recipient->confirmed_at ? $recipient->confirmed_at->format('Y-m-d H:i:s') : '-' }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ $recipient->wa_me_url }}" target="_blank" class="btn btn-xs btn-light-success">
                                        <i class="fab fa-whatsapp me-1"></i>
                                        {{ __('Open Chat') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

