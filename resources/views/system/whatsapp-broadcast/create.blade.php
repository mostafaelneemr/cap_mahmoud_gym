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
                        <h2 class="fs-2 fw-bold mb-0">{{ __('Create WhatsApp Broadcast Campaign') }}</h2>
                        <span class="text-muted fs-7">{{ __('Filter target trainees, craft your personalized message, and prepare sequential Click-to-Chat links.') }}</span>
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('system.whatsapp-broadcast.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-outline ki-arrow-left fs-4 me-1"></i>
                    {{ __('Back to Campaigns') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Notice Card -->
    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
        <i class="ki-outline ki-information-5 fs-2tx text-warning me-4"></i>
        <div class="d-flex flex-stack flex-grow-1">
            <div class="fw-semibold">
                <h4 class="text-gray-900 fw-bold">{{ __('Manual Sending Policy & WhatsApp Terms of Service') }}</h4>
                <div class="fs-7 text-gray-700">
                    {{ __('This application strictly uses official WhatsApp Click-to-Chat links (wa.me). No automated bots or unofficial APIs are used. You will review each recipient one by one, open their WhatsApp chat, and manually press Send in WhatsApp. This fully adheres to Meta guidelines and prevents account bans.') }}
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('system.whatsapp-broadcast.store') }}" method="POST" id="campaign-form">
        @csrf
        <div class="row g-7">
            <!-- Left Column: Settings & Template -->
            <div class="col-lg-7">
                <div class="card card-flush shadow-sm mb-7">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="fw-bold">{{ __('1. Campaign Information & Message') }}</h3>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <!-- Campaign Title -->
                        <div class="mb-5">
                            <label class="required form-label fw-semibold fs-6">{{ __('Campaign Title') }}</label>
                            <input type="text" name="title" class="form-control form-control-solid @error('title') is-invalid @enderror" 
                                   placeholder="{{ __('e.g., Summer Training Schedule Update') }}" 
                                   value="{{ old('title') }}" required />
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message Template -->
                        <div class="mb-5">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="required form-label fw-semibold fs-6 mb-0">{{ __('Message Template') }}</label>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-xs btn-light-primary insert-token" data-token="name">
                                        <i class="ki-outline ki-plus fs-8"></i>
                                        <code>@{{name}}</code>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-light-info insert-token" data-token="id">
                                        <i class="ki-outline ki-plus fs-8"></i>
                                        <code>@{{id}}</code>
                                    </button>
                                </div>
                            </div>
                            <textarea name="message_template" id="message_template" rows="5" 
                                      class="form-control form-control-solid @error('message_template') is-invalid @enderror" 
                                      placeholder="{{ __('Hello name, this is Coach Mahmoud with an update regarding your program...') }}" 
                                      required>{{ old('message_template') }}</textarea>
                            <div class="form-text fs-8 text-muted">
                                {{ __('Click badges above to insert dynamic recipient variables. Each variable will be replaced for every recipient.') }}
                            </div>
                            @error('message_template')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Live Message Preview -->
                        <div class="bg-light-success bg-opacity-30 rounded p-4 border border-success border-dashed">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fab fa-whatsapp text-success fs-5"></i>
                                <span class="fw-bold fs-7 text-gray-800">{{ __('Live Personalized Preview (Sample Trainee: أحمد محمد):') }}</span>
                            </div>
                            <div id="live-rendered-preview" class="p-3 bg-white rounded border fs-7 text-gray-800" style="white-space: pre-wrap; min-height: 50px;">
                                {{ __('Type a message template above to see the live preview...') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recipient Filters -->
                <div class="card card-flush shadow-sm">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="fw-bold">{{ __('2. Target Trainee Filters') }}</h3>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-5">
                            <!-- Subscription status -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-6">{{ __('Subscription Status') }}</label>
                                <select name="subscription_status" id="filter_subscription" class="form-select form-select-solid filter-trigger">
                                    <option value="all" {{ old('subscription_status') === 'all' ? 'selected' : '' }}>{{ __('All Trainees') }}</option>
                                    <option value="active" {{ old('subscription_status') === 'active' ? 'selected' : '' }}>{{ __('Active Subscriptions Only') }}</option>
                                    <option value="expired" {{ old('subscription_status') === 'expired' ? 'selected' : '' }}>{{ __('Expired Subscriptions Only') }}</option>
                                    <option value="none" {{ old('subscription_status') === 'none' ? 'selected' : '' }}>{{ __('No Subscription') }}</option>
                                </select>
                            </div>

                            <!-- Coach assignment -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-6">{{ __('Coach Assignment') }}</label>
                                <select name="coach_assigned" id="filter_coach" class="form-select form-select-solid filter-trigger">
                                    <option value="all" {{ old('coach_assigned') === 'all' ? 'selected' : '' }}>{{ __('All Coaches (Gym Wide)') }}</option>
                                    <option value="only_mine" {{ old('coach_assigned') === 'only_mine' ? 'selected' : '' }}>{{ __('Assigned to Me Only') }}</option>
                                </select>
                            </div>

                            <!-- WhatsApp Opt-Out Checkbox -->
                            <div class="col-12">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input filter-trigger" type="checkbox" name="exclude_opt_out" id="filter_opt_out" value="1" checked />
                                    <label class="form-check-label fw-semibold text-gray-800" for="filter_opt_out">
                                        {{ __('Exclude trainees who opted out of WhatsApp broadcasts (Recommended)') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Audience Breakdown & Confirmation -->
            <div class="col-lg-5">
                <div class="card card-flush shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="fw-bold">{{ __('3. Audience Preview') }}</h3>
                        </div>
                        <div class="card-toolbar">
                            <span id="preview-spinner" class="spinner-border spinner-border-sm text-primary d-none"></span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <!-- Stat Badges -->
                        <div class="row g-3 mb-6">
                            <div class="col-6">
                                <div class="border border-success border-dashed rounded p-4 text-center bg-light-success bg-opacity-40">
                                    <span class="fs-2tx fw-bold text-success d-block" id="count-eligible">{{ collect($evaluation['eligible'])->count() }}</span>
                                    <span class="fw-semibold fs-7 text-gray-700">{{ __('Eligible Trainees') }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border border-danger border-dashed rounded p-4 text-center bg-light-danger bg-opacity-40">
                                    <span class="fs-2tx fw-bold text-danger d-block" id="count-excluded">{{ collect($evaluation['excluded'])->count() }}</span>
                                    <span class="fw-semibold fs-7 text-gray-700">{{ __('Excluded Trainees') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Breakdown list tabs -->
                        <ul class="nav nav-tabs nav-line-tabs mb-4 fs-7">
                            <li class="nav-item">
                                <a class="nav-link active fw-bold text-success" data-bs-toggle="tab" href="#tab-eligible">
                                    {{ __('Eligible Sample') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold text-danger" data-bs-toggle="tab" href="#tab-excluded">
                                    {{ __('Excluded & Reasons') }}
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content" id="audienceTabContent">
                            <!-- Eligible Tab -->
                            <div class="tab-pane fade show active scroll-y mh-300px" id="tab-eligible" role="tabpanel">
                                <div id="eligible-list" class="d-flex flex-column gap-2">
                                    @foreach(collect($evaluation['eligible'])->take(15) as $item)
                                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                            <div>
                                                <span class="fw-semibold fs-7 text-gray-800 d-block">{{ $item['name'] }}</span>
                                                <span class="text-muted fs-8">{{ $item['phone_raw'] }}</span>
                                            </div>
                                            <span class="badge badge-light-success fs-8"><code>{{ $item['phone_normalized'] }}</code></span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Excluded Tab -->
                            <div class="tab-pane fade scroll-y mh-300px" id="tab-excluded" role="tabpanel">
                                <div id="excluded-list" class="d-flex flex-column gap-2">
                                    @foreach(collect($evaluation['excluded'])->take(15) as $item)
                                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                            <div>
                                                <span class="fw-semibold fs-7 text-gray-800 d-block">{{ $item['name'] }}</span>
                                                <span class="text-muted fs-8">{{ $item['phone_raw'] ?? __('No phone') }}</span>
                                            </div>
                                            <span class="badge badge-light-danger fs-8">{{ $item['reason'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="separator my-6"></div>

                        <!-- Action Submit -->
                        <button type="submit" class="btn btn-success w-100 py-4 fs-6 fw-bold" id="btn-submit-campaign">
                            <i class="fab fa-whatsapp fs-3 me-2"></i>
                            {{ __('Create Campaign & Start Sending') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const templateInput = document.getElementById('message_template');
    const livePreview = document.getElementById('live-rendered-preview');
    const countEligible = document.getElementById('count-eligible');
    const countExcluded = document.getElementById('count-excluded');
    const eligibleList = document.getElementById('eligible-list');
    const excludedList = document.getElementById('excluded-list');
    const spinner = document.getElementById('preview-spinner');
    const previewUrl = "{{ route('system.whatsapp-broadcast.preview-recipients') }}";
    const csrfToken = "{{ csrf_token() }}";

    // Token insertion
document.querySelectorAll('.insert-token').forEach(btn => {
    btn.addEventListener('click', function() {
        const token = '@{{' + this.getAttribute('data-token') + '}}';

        const start = templateInput.selectionStart;
        const end = templateInput.selectionEnd;
        const text = templateInput.value;

        templateInput.value =
            text.substring(0, start) + token + text.substring(end);

        templateInput.focus();
        templateInput.selectionStart =
            templateInput.selectionEnd = start + token.length;

        updateLivePreview();
    });
});

    function updateLivePreview() {
        let val = templateInput.value;
        if (!val.trim()) {
            livePreview.textContent = "{{ __('Type a message template above to see the live preview...') }}";
            return;
        }
        val = val.replace(/\{\{name\}\}/gi, 'أحمد محمد');
        val = val.replace(/\{\{id\}\}/gi, 'TR-104');
        livePreview.textContent = val;
    }

    templateInput.addEventListener('input', updateLivePreview);
    updateLivePreview();

    // Filters update AJAX
    function refreshAudience() {
        if (spinner) spinner.classList.remove('d-none');

        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('subscription_status', document.getElementById('filter_subscription').value);
        formData.append('coach_assigned', document.getElementById('filter_coach').value);
        formData.append('exclude_opt_out', document.getElementById('filter_opt_out').checked ? '1' : '0');

        fetch(previewUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (spinner) spinner.classList.add('d-none');
            if (data.status) {
                countEligible.textContent = data.eligible_count;
                countExcluded.textContent = data.excluded_count;

                // Render eligible list
                let eHtml = '';
                data.eligible.forEach(item => {
                    eHtml += `
                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                            <div>
                                <span class="fw-semibold fs-7 text-gray-800 d-block">${item.name}</span>
                                <span class="text-muted fs-8">${item.phone_raw || ''}</span>
                            </div>
                            <span class="badge badge-light-success fs-8"><code>${item.phone_normalized}</code></span>
                        </div>
                    `;
                });
                eligibleList.innerHTML = eHtml || '<div class="text-muted fs-8 text-center py-4">{{ __("No eligible trainees found") }}</div>';

                // Render excluded list
                let xHtml = '';
                data.excluded.forEach(item => {
                    xHtml += `
                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                            <div>
                                <span class="fw-semibold fs-7 text-gray-800 d-block">${item.name}</span>
                                <span class="text-muted fs-8">${item.phone_raw || '{{ __("No phone") }}'}</span>
                            </div>
                            <span class="badge badge-light-danger fs-8">${item.reason}</span>
                        </div>
                    `;
                });
                excludedList.innerHTML = xHtml || '<div class="text-muted fs-8 text-center py-4">{{ __("No excluded trainees") }}</div>';
            }
        })
        .catch(err => {
            if (spinner) spinner.classList.add('d-none');
            console.error('Error previewing audience:', err);
        });
    }

    document.querySelectorAll('.filter-trigger').forEach(el => {
        el.addEventListener('change', refreshAudience);
    });
});
</script>
@endsection

