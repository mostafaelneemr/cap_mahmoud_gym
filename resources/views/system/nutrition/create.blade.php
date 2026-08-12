@extends('system.layout')

@section('header')
<style>
    .nutrition-header-banner {
        background: linear-gradient(135deg, #1e1e2d 0%, #2b2b40 100%);
        border-radius: 14px;
        padding: 1.75rem 2rem;
        color: #ffffff;
        margin-bottom: 2rem;
    }
    [data-bs-theme="dark"] .nutrition-header-banner {
        background: linear-gradient(135deg, #151521 0%, #1e1e2d 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
</style>
@endsection

@section('content')

    @php
        $isEdit = isset($plan) && $plan;
        $actionUrl = $isEdit ? route('system.nutrition.update', $plan->id) : route('system.nutrition.store');
        $method = $isEdit ? 'PATCH' : 'POST';
    @endphp

    {!! Form::open(['id'=>'main-form','onsubmit' => 'FormSubmit("'.$actionUrl.'");return false;','method' => $method]) !!}
    <div id="form-alert-message"></div>

    <div class="card card-flush shadow-sm mb-7">
        <div class="card-body p-8">
            <div class="row g-5">
                <!-- Trainee Selection -->
                <div class="col-md-6 fv-row mb-5">
                    <label class="form-label required fw-bold text-gray-900 fs-6">@lang('Trainee')</label>
                    <select name="trainee_id" class="form-select form-select-solid" data-control="select2" required>
                        <option value="">@lang('Select Trainee')</option>
                        @foreach($trainees as $trainee)
                            @php
                                $optionVal = $trainee->id ?? ($trainee->user ? $trainee->user->id : $trainee->user_id);
                                $traineeUserId = $trainee->user ? $trainee->user->id : ($trainee->user_id ?? $trainee->id);
                                $traineeName = $trainee->user ? $trainee->user->name : ($trainee->name ?? __('Trainee').' #'.$trainee->id);
                                $selectedId = $isEdit ? $plan->trainee_id : ($selectedTraineeId ?? null);
                                $isSelected = ($selectedId == $optionVal) || ($selectedId == $traineeUserId) || (isset($trainee->id) && $selectedId == $trainee->id);
                            @endphp
                            <option value="{{ $optionVal }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $traineeName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Plan Name / Title -->
                <div class="col-md-6 fv-row mb-5">
                    <label class="form-label fw-bold text-gray-900 fs-6">@lang('Plan Name / Title')</label>
                    <input type="text" name="name" class="form-control form-control-solid" placeholder="@lang('e.g. Winter Cutting 2200 kcal Plan')" value="{{ $isEdit ? $plan->name : '' }}">
                </div>

                <!-- Plan Description (CKEditor / Summernote Rich Text Editor) -->
                <div class="col-12 fv-row mb-5">
                    <label class="form-label fw-bold text-gray-900 fs-6 mb-2">@lang('Nutrition Plan Details & Instructions')</label>
                    <textarea name="description" class="form-control form-control-solid text-editor" rows="12" placeholder="@lang('Enter detailed meal plan instructions, food items, macro breakdowns, and notes...')">{{ $isEdit ? $plan->description : '' }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-end py-6 px-9 border-0">
            <a href="{{ route('system.nutrition.index') }}" class="btn btn-light me-3">@lang('Cancel')</a>
            <button type="submit" class="btn btn-success fw-bold px-8">
                <i class="fa-solid fa-check me-2"></i>{{ $isEdit ? __('Update Nutrition Plan') : __('Save Nutrition Plan') }}
            </button>
        </div>
    </div>

    {!! Form::close() !!}

@endsection

@section('footer')
<script type="text/javascript">
    $(document).ready(function() {
        if (typeof text_editor === 'function') {
            text_editor('text-editor');
        }
    });
</script>
@endsection
