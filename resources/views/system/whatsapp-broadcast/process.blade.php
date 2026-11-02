@extends('system.layout')

@section('content')
<div class="d-flex flex-column gap-7 gap-lg-10">
    <!-- Header with Campaign Info and Progress -->
    <div class="card card-flush shadow-sm">
        <div class="card-body py-5">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-4 mb-4">
                <div class="d-flex align-items-center gap-3">
                    <span class="symbol symbol-45px symbol-circle bg-light-success">
                        <i class="fab fa-whatsapp text-success fs-2x p-3"></i>
                    </span>
                    <div>
                        <h2 class="fs-3 fw-bold mb-0">{{ $campaign->title }}</h2>
                        <span class="text-muted fs-7">{{ __('Sequential Manual Sending') }} • {{ __('Step :current of :total', ['current' => $currentIndex, 'total' => $totalCount]) }}</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('system.whatsapp-broadcast.show', $campaign->id) }}" class="btn btn-sm btn-light">
                        <i class="ki-outline ki-eye fs-5 me-1"></i>
                        {{ __('View Summary') }}
                    </a>
                    <form action="{{ route('system.whatsapp-broadcast.cancel', $campaign->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to stop and cancel this campaign?') }}');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-light-danger">
                            <i class="ki-outline ki-cross fs-5 me-1"></i>
                            {{ __('Cancel Campaign') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="d-flex flex-column gap-2">
                <div class="d-flex justify-content-between fs-7 fw-bold text-gray-700">
                    <span>{{ __('Overall Progress') }}</span>
                    <span>{{ $progressPercent }}% ({{ $currentIndex - 1 }} / {{ $totalCount }})</span>
                </div>
                <div class="progress h-8px w-100 bg-light">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $progressPercent }}%"></div>
                </div>
                <div class="d-flex gap-4 fs-8 text-muted mt-1">
                    <span class="text-success"><i class="ki-outline ki-check-circle fs-7 text-success me-1"></i>{{ $campaign->confirmed_count }} {{ __('Sent & Confirmed') }}</span>
                    <span class="text-secondary"><i class="ki-outline ki-cross-circle fs-7 text-secondary me-1"></i>{{ $campaign->skipped_count }} {{ __('Skipped') }}</span>
                    <span class="text-primary"><i class="ki-outline ki-time fs-7 text-primary me-1"></i>{{ max(0, $totalCount - ($currentIndex - 1)) }} {{ __('Remaining') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Active Recipient Box -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-flush shadow-sm border border-success border-2">
                <div class="card-header bg-light-success py-4">
                    <div class="card-title">
                        <span class="badge badge-success fs-7 fw-bold me-3">{{ __('Recipient :num of :total', ['num' => $currentIndex, 'total' => $totalCount]) }}</span>
                        <h3 class="fs-4 fw-bolder mb-0 text-gray-900">{{ $recipient->recipient_name }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <span class="badge badge-light-primary fs-8">ID: {{ $recipient->trainee_id }}</span>
                    </div>
                </div>

                <div class="card-body p-7">
                    <!-- Phone info -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between bg-light rounded p-4 mb-6">
                        <div>
                            <span class="text-muted fs-8 d-block">{{ __('Original Input Phone') }}</span>
                            <span class="fw-bold fs-6 text-gray-800">{{ $recipient->phone_raw }}</span>
                        </div>
                        <div>
                            <span class="text-muted fs-8 d-block">{{ __('Normalized WhatsApp Number (E.164)') }}</span>
                            <span class="fw-bold fs-6 text-success font-monospace">
                                <i class="fab fa-whatsapp text-success me-1"></i>
                                +{{ $recipient->phone_normalized }}
                            </span>
                        </div>
                    </div>

                    <!-- Rendered Message Box -->
                    <div class="mb-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-gray-800 mb-0">{{ __('Personalized Message to Send') }}</label>
                            <button type="button" class="btn btn-xs btn-light" id="btn-copy-msg" onclick="navigator.clipboard.writeText(document.getElementById('rendered-msg-text').innerText); alert('{{ __('Message copied to clipboard!') }}');">
                                <i class="ki-outline ki-copy fs-7 me-1"></i> {{ __('Copy') }}
                            </button>
                        </div>
                        <div class="p-4 bg-light-success bg-opacity-30 border border-success border-dashed rounded fs-6 text-gray-900 shadow-sm" id="rendered-msg-text" style="white-space: pre-wrap; line-height: 1.6;">{{ $recipient->message_rendered }}</div>
                    </div>

                    <!-- Step 1: Open WhatsApp Button -->
                    <div class="text-center mb-8">
                        <a href="{{ $recipient->wa_me_url }}" target="_blank" id="btn-open-wa" class="btn btn-lg btn-success w-100 py-4 fs-5 fw-bolder shadow-sm">
                            <i class="fab fa-whatsapp fs-2 me-2"></i>
                            {{ __('Step 1: Open WhatsApp Chat (wa.me)') }}
                        </a>
                        <div class="fs-8 text-muted mt-2">
                            <i class="ki-outline ki-information-2 me-1"></i>
                            {{ __('Opens WhatsApp with recipient and prefilled message. Review and press Send in WhatsApp.') }}
                        </div>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <!-- Step 2: Confirmation / Skip Actions -->
                    <div class="text-center mb-4">
                        <h5 class="fs-6 fw-bold text-gray-700 mb-4">{{ __('Step 2: Confirm Action to Move to Next Recipient') }}</h5>
                        
                        <div class="d-flex flex-wrap justify-content-center gap-4">
                            <!-- Confirm Sent Form -->
                            <form action="{{ route('system.whatsapp-broadcast.action', $campaign->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="recipient_id" value="{{ $recipient->id }}">
                                <input type="hidden" name="action" value="confirm">
                                <button type="submit" class="btn btn-success px-8 py-3 fs-6 fw-bold">
                                    <i class="ki-outline ki-check-circle fs-3 me-2"></i>
                                    {{ __('Confirm Sent & Next') }}
                                </button>
                            </form>

                            <!-- Skip Form -->
                            <form action="{{ route('system.whatsapp-broadcast.action', $campaign->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="recipient_id" value="{{ $recipient->id }}">
                                <input type="hidden" name="action" value="skip">
                                <button type="submit" class="btn btn-light-warning px-6 py-3 fs-6 fw-bold">
                                    <i class="ki-outline ki-arrow-right fs-3 me-2"></i>
                                    {{ __('Skip Recipient') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Disclaimer -->
                    <div class="alert alert-dismissible bg-light-secondary d-flex align-items-center p-3 mb-0">
                        <i class="ki-outline ki-shield-tick fs-4 text-primary me-3"></i>
                        <div class="fs-8 text-gray-600">
                            {{ __('Notice: The system will never falsely mark a message as sent. Marking is entirely based on your manual confirmation above.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const openBtn = document.getElementById('btn-open-wa');
    const actionUrl = "{{ route('system.whatsapp-broadcast.action', $campaign->id) }}";
    const recipientId = "{{ $recipient->id }}";
    const csrfToken = "{{ csrf_token() }}";

    if (openBtn) {
        openBtn.addEventListener('click', function() {
            // Signal that chat link was opened
            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    recipient_id: recipientId,
                    action: 'open'
                })
            }).catch(e => console.log('Opened tracking:', e));
        });
    }
});
</script>
@endsection

