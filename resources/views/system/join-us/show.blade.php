@extends('system.layout')

@section('content')

<div class="row">
    <div class="col-12">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div class="card card-flush shadow-sm mb-6">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h3 class="fw-bold text-gray-900 m-0">
                        <i class="fa-solid fa-person-running text-primary fs-3 me-2"></i>
                        {{ __('Submissions') }} - {{ $result->name }}
                    </h3>
                </div>
                <div class="card-toolbar gap-3">
                    {!! $result->status_label !!}
                    <a href="{{ route('system.join-us.index') }}" class="btn btn-sm btn-light">
                        <i class="fa-solid fa-arrow-left me-1"></i>{{ __('Back to List') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-6">

            {{-- ── Submission Details ─────────────────────────────────── --}}
            <div class="col-lg-8">
                <div class="card card-flush shadow-sm h-100">
                    <div class="card-header border-0 pt-6">
                        <h4 class="card-title fw-bold text-gray-800">{{ __('Applicant Details') }}</h4>
                    </div>
                    <div class="card-body pt-2">

                        <div class="row g-5">

                            <div class="col-sm-6">
                                <label class="text-muted fs-7 fw-semibold mb-1">{{ __('Full Name') }}</label>
                                <div class="fw-bold text-gray-800 fs-6">{{ $result->name }}</div>
                            </div>

                            <div class="col-sm-6">
                                <label class="text-muted fs-7 fw-semibold mb-1">{{ __('Phone') }}</label>
                                <div class="fw-bold text-gray-800 fs-6">
                                    <a href="tel:{{ $result->phone }}" class="text-gray-800">{{ $result->phone }}</a>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <label class="text-muted fs-7 fw-semibold mb-1">{{ __('Age') }}</label>
                                <div class="fw-bold text-gray-800 fs-6">{{ $result->age }} {{ __('years') }}</div>
                            </div>

                            <div class="col-sm-4">
                                <label class="text-muted fs-7 fw-semibold mb-1">{{ __('Country') }}</label>
                                <div class="fw-bold text-gray-800 fs-6">{{ $result->country }}</div>
                            </div>

                            <div class="col-sm-4">
                                <label class="text-muted fs-7 fw-semibold mb-1">{{ __('Governorate') }}</label>
                                <div class="fw-bold text-gray-800 fs-6">{{ $result->governorate }}</div>
                            </div>

                            <div class="col-sm-6">
                                <label class="text-muted fs-7 fw-semibold mb-1">{{ __('Training Level') }}</label>
                                <div class="fw-bold text-gray-800 fs-6">{{ $result->training_level }}</div>
                            </div>

                            <div class="col-sm-6">
                                <label class="text-muted fs-7 fw-semibold mb-1">{{ __('Submitted') }}</label>
                                <div class="fw-bold text-gray-800 fs-6">{{ $result->created_at?->format('d M Y, H:i') }}</div>
                            </div>

                            <div class="col-12">
                                <label class="text-muted fs-7 fw-semibold mb-1">{{ __('Training Goal') }}</label>
                                <div class="fw-semibold text-gray-700 bg-light rounded p-4">{{ $result->goal }}</div>
                            </div>

                            <div class="col-12">
                                <label class="text-muted fs-7 fw-semibold mb-1">{{ __('Injuries') }}</label>
                                <div class="fw-semibold text-gray-700 bg-light rounded p-4">
                                    {{ $result->injuries }}
                                    @if($result->injury_details)
                                        <hr class="my-2">
                                        <span class="text-muted fs-7">{{ __('Details') }}: </span>{{ $result->injury_details }}
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="text-muted fs-7 fw-semibold mb-1">{{ __('Reason for Joining') }}</label>
                                <div class="fw-semibold text-gray-700 bg-light rounded p-4">{{ $result->reason }}</div>
                            </div>

                            <div class="col-12">
                                <label class="text-muted fs-7 fw-semibold mb-1">{{ __('Daily Routine') }}</label>
                                <div class="fw-semibold text-gray-700 bg-light rounded p-4">{{ $result->routine }}</div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- ── Status Management ──────────────────────────────────── --}}
            <div class="col-lg-4">
                <div class="card card-flush shadow-sm">
                    <div class="card-header border-0 pt-6">
                        <h4 class="card-title fw-bold text-gray-800">{{ __('Update Status') }}</h4>
                    </div>
                    <div class="card-body pt-2">

                        <form action="{{ route('system.join-us.update-status', $result->id) }}"
                              method="POST" id="statusForm">
                            @csrf

                            <div class="mb-5">
                                <label class="form-label fw-semibold required">{{ __('Status') }}</label>
                                <select name="status" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                                    @foreach(['new' => __('New'), 'contacted' => __('Contacted'), 'converted' => __('Converted'), 'rejected' => __('Rejected')] as $val => $label)
                                        <option value="{{ $val }}" {{ $result->status === $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-5">
                                <label class="form-label fw-semibold">{{ __('Admin Notes') }}</label>
                                <textarea name="notes" rows="5"
                                          class="form-control form-control-solid"
                                          placeholder="{{ __('Optional notes about this applicant...') }}">{{ $result->notes }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-floppy-disk me-2"></i>{{ __('Save Changes') }}
                            </button>

                        </form>

                    </div>
                </div>

                {{-- ── Delete ─────────────────────────────────────────── --}}
{{--                <div class="card card-flush shadow-sm mt-6 border border-danger border-dashed">--}}
{{--                    <div class="card-body py-5">--}}
{{--                        <p class="text-danger fw-semibold fs-7 mb-3">--}}
{{--                            <i class="fa-solid fa-triangle-exclamation me-1"></i>--}}
{{--                            {{ __('Deleting this submission is permanent and cannot be undone.') }}--}}
{{--                        </p>--}}
{{--                        <form action="{{ route('system.join-us.destroy', $result->id) }}"--}}
{{--                              method="POST"--}}
{{--                              onsubmit="return confirm('{{ __('Are you sure you want to delete this submission?') }}')">--}}
{{--                            @csrf--}}
{{--                            @method('DELETE')--}}
{{--                            <button type="submit" class="btn btn-sm btn-light-danger w-100">--}}
{{--                                <i class="fa-solid fa-trash me-2"></i>{{ __('Delete Submission') }}--}}
{{--                            </button>--}}
{{--                        </form>--}}
{{--                    </div>--}}
{{--                </div>--}}

            </div>

        </div>

    </div>
</div>

@endsection

@section('footer')
<script>
    // AJAX form submit for status update (matching existing pattern)
    $('#statusForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (res) {
                if (res.status) {
                    flash_success(res.message);
                    setTimeout(() => { window.location.reload(); }, 1000);
                } else {
                    flash_error(res.message);
                }
            },
            error: function (xhr) {
                flash_error('{{ __('Something went wrong.') }}');
            }
        });
    });
</script>
@endsection
