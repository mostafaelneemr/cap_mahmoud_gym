@extends('system.layout')

@section('content')

    {!! Form::open(['id'=>'main-form','onsubmit' => 'FormSubmit("'.route('system.workout.store') .'");return false;','method' => 'POST']) !!}
    <div id="form-alert-message"></div>

    <div class="stepper stepper-pills stepper-column d-flex flex-column flex-xl-row w-100" id="kt_dynamic_stepper">

        <div class="d-flex flex-row-auto bgi-no-repeat bgi-position-y-bottom bgi-position-x-center bgi-size-contain bgi-no-repeat w-xl-250px me-10 font-sans">
            <div class="stepper-nav flex-wrap" id="stepper-nav-container">

                <div class="stepper-item mx-2 my-4 current" data-kt-stepper-element="nav">
                    <div class="stepper-wrapper d-flex align-items-center">
                        <div class="stepper-icon w-40px h-40px">
                            <i class="stepper-check fas fa-check"></i>
                            <span class="stepper-number">1</span>
                        </div>
                        <div class="stepper-label">
                            <h3 class="stepper-title">@lang('Program Settings')</h3>
                            <div class="stepper-desc">@lang('Define days and split type')</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="flex-row-fluid py-lg-5 px-lg-5">
            <div id="stepper-content-container">
                <!-- محتوى الخطوة الأولى الثابتة -->
                <div class="card-body current" data-kt-stepper-element="content">
                    <div class="w-100">
                        <div class="pb-10 pb-lg-15">
                            <h2 class="fw-bolder text-gray-900">@lang('Setup Workout Program Structure')</h2>
                            <div
                                class="text-muted fw-bold fs-6">@lang('Select the trainee and number of training days')</div>
                        </div>

                        <div class="fv-row mb-10">
                            <label class="form-label required">@lang('Trainee')</label>


                            <select name="trainee_id" class="form-select form-select-solid" data-control="select2" required>
                                <option value="">@lang('Select Trainee')</option>
                                @foreach($trainees as $trainee)
                                    <option value="{{ $trainee->id }}" {{ (isset($selectedTraineeId) && $selectedTraineeId == $trainee->id) ? 'selected' : '' }}>
                                        {{ $trainee->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="fv-row mb-10">
                            <label class="form-label required">@lang('Training Days Per Week')</label>
                            <select id="days_count" class="form-select form-select-solid" data-control="select2"
                                    data-hide-search="true">
                                <option value="2">@lang('2 Day')</option>
                                <option value="3">@lang('3 Day')</option>
                                <option value="4">@lang('4 Day')</option>
                                <option value="5">@lang('5 Day')</option>
                                <option value="6">@lang('6 Day')</option>
                                <option value="7">@lang('7 Day')</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-stack pt-10">
                <div class="mr-2">
                    <button type="button" class="btn btn-lg btn-light-primary me-3" data-kt-stepper-action="previous">
                        <i class="ki-duotone ki-arrow-left fs-4 me-1"><span class="path1"></span><span
                                class="path2"></span></i> @lang('Back')
                    </button>
                </div>
                <div>
                    <button type="submit" class="btn btn-lg btn-success" data-kt-stepper-action="submit">
                        @lang('Save Program')
                        <i class="ki-duotone ki-check fs-4 ms-1"></i>
                    </button>

                    <button type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="next">@lang('Next')
                        <i class="ki-duotone ki-arrow-right fs-4 ms-1"><span class="path1"></span><span
                                class="path2"></span></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}

@endsection

@section('footer')
    <script type="text/javascript">

        $(document).ready(function() {
            @if(isset($selectedTraineeId) && $selectedTraineeId)
            $('select[name="trainee_id"]').val('{{ $selectedTraineeId }}').trigger('change');
            @endif
        });
        $(document).ready(function () {
            var stepperElement = document.querySelector("#kt_dynamic_stepper");
            var stepperObj;

            var navContainer = $("#stepper-nav-container");
            var contentContainer = $("#stepper-content-container");

            $(document).on('click', '[data-kt-stepper-action="next"]', function (e) {

                if (!stepperObj || stepperObj.getCurrentStepIndex() === 1) {
                    var daysCount = $("#days_count").val();

                    if (!daysCount) {
                        alert("{{ __('Please select the number of training days.') }}");
                        return false;
                    }

                    $(".dynamic-step").remove();

                    for (var i = 1; i <= daysCount; i++) {

                        var navHtml = `
                        <div class="stepper-item mx-2 my-4 dynamic-step" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper d-flex align-items-center">
                                <div class="stepper-icon w-40px h-40px">
                                    <i class="stepper-check fas fa-check"></i>
                                    <span class="stepper-number">${i + 1}</span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title">@lang('Program Day') ${i}</h3>
                                </div>
                            </div>
                        </div>`;
                        navContainer.append(navHtml);

                        var contentHtml = `
                        <div class="card-body dynamic-step" data-kt-stepper-element="content">
                            <div class="w-100">
                                <div class="pb-5 mb-5 border-bottom border-gray-200">
                                    <h2 class="fw-bolder text-gray-900 mb-4">@lang('Title Program') (${i})</h2>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-gray-700">@lang('Day Name (English)')</label>
                                            <input type="text" name="program[day_${i}][day_name]" class="form-control form-control-solid" placeholder="e.g. Day ${i}: Chest & Triceps"/>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-gray-700">اسم اليوم التدريبي (عربي)</label>
                                            <input type="text" name="program[day_${i}][day_name_ar]" class="form-control form-control-solid" dir="rtl" placeholder="مثال: اليوم ${i}: صدر وترايسبس"/>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- المقطع الأول: الإحماء -->
                                <div class="mb-5">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-warning">@lang('Warmup (English)')</label>
                                            <textarea name="program[day_${i}][warmup]" class="form-control form-control-solid" rows="2" placeholder="Warm up instructions in English..."></textarea>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-warning">الإحماء (عربي)</label>
                                            <textarea name="program[day_${i}][warmup_ar]" class="form-control form-control-solid" dir="rtl" rows="2" placeholder="تعليمات الإحماء بالعربي..."></textarea>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-5 border p-5 rounded bg-light">
                                    <label class="form-label fw-bold text-success fs-5 mb-3">@lang('Exercises')</label>

                                    <div id="exercises-container-day-${i}" data-exercise-index="1">
                                        <div class="p-4 mb-4 rounded border border-gray-300 bg-white exercise-row">
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fs-7 fw-bold text-gray-700">@lang('Exercise Name (English)')</label>
                                                    <input type="text" name="program[day_${i}][exercises][ex_1][name]" class="form-control form-control-solid" placeholder="e.g. Incline Bench Press">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fs-7 fw-bold text-gray-700">اسم التمرين (عربي)</label>
                                                    <input type="text" name="program[day_${i}][exercises][ex_1][name_ar]" class="form-control form-control-solid" dir="rtl" placeholder="مثال: بنش مائل بالبار">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="row g-2 align-items-end">
                                                <div class="col-6 col-md-1">
                                                    <label class="form-label fs-7 fw-bold">@lang('Sets')</label>
                                                    <input type="text" name="program[day_${i}][exercises][ex_1][sets]" class="form-control form-control-solid text-center" placeholder="4">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="col-6 col-md-1">
                                                    <label class="form-label fs-7 fw-bold">@lang('Reps')</label>
                                                    <input type="text" name="program[day_${i}][exercises][ex_1][reps]" class="form-control form-control-solid text-center" placeholder="10">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="col-6 col-md-1">
                                                    <label class="form-label fs-7 fw-bold">@lang('Rest')</label>
                                                    <input type="text" name="program[day_${i}][exercises][ex_1][rest]" class="form-control form-control-solid text-center" placeholder="90s">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="col-6 col-md-1">
                                                    <label class="form-label fs-7 fw-bold">@lang('Weight')</label>
                                                    <input type="text" name="program[day_${i}][exercises][ex_1][weight]" class="form-control form-control-solid text-center" placeholder="20kg">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="col-6 col-md-1">
                                                    <label class="form-label fs-7 fw-bold">@lang('Tempo')</label>
                                                    <input type="text" name="program[day_${i}][exercises][ex_1][tempo]" class="form-control form-control-solid text-center" placeholder="3-0-1">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="col-12 col-md-5">
                                                    <label class="form-label fs-7 fw-bold">@lang('Link')</label>
                                                    <input type="url" name="program[day_${i}][exercises][ex_1][link]" class="form-control form-control-solid" placeholder="https://youtube.com/...">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="col-12 col-md-2 d-flex justify-content-end">
                                                    <button type="button" class="btn btn-icon btn-light-danger btn-sm delete-exercise-btn w-100"><i class="fas fa-trash fs-6"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-sm btn-light-success mt-2 add-exercise-btn" data-day-id="${i}">
                                        <i class="fas fa-plus me-1"></i>@lang('Add Exercise')
                                    </button>
                                </div>

                                <div class="mb-5">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-primary">@lang('Post-Workout (English)')</label>
                                            <textarea name="program[day_${i}][post_workout]" class="form-control form-control-solid" rows="2" placeholder="Cool down instructions in English..."></textarea>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-primary">التهدئة / ما بعد التمرين (عربي)</label>
                                            <textarea name="program[day_${i}][post_workout_ar]" class="form-control form-control-solid" dir="rtl" rows="2" placeholder="تعليمات التهدئة والاستشفاء بالعربي..."></textarea>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                        contentContainer.append(contentHtml);
                    }

                    stepperObj = new KTStepper(stepperElement);

                    stepperObj.on("kt.stepper.next", function (stepper) {
                        stepper.goNext();
                    });

                    stepperObj.on("kt.stepper.previous", function (stepper) {
                        stepper.goPrevious();
                    });

                    stepperObj.goTo(2);
                }
            });

            $(document).on('click', '.add-exercise-btn', function () {
                var dayId = $(this).data('day-id');
                var container = $(`#exercises-container-day-${dayId}`);

                var currentExerciseIdx = parseInt(container.attr('data-exercise-index')) + 1;
                container.attr('data-exercise-index', currentExerciseIdx);

                var newExerciseRow = `
                <div class="p-4 mb-4 rounded border border-gray-300 bg-white exercise-row" style="display:none;">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-bold text-gray-700">@lang('Exercise Name (English)')</label>
                            <input type="text" name="program[day_${dayId}][exercises][ex_${currentExerciseIdx}][name]" class="form-control form-control-solid" placeholder="e.g. Dumbbell Lateral Raise">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-bold text-gray-700">اسم التمرين (عربي)</label>
                            <input type="text" name="program[day_${dayId}][exercises][ex_${currentExerciseIdx}][name_ar]" class="form-control form-control-solid" dir="rtl" placeholder="مثال: رفرفة جانبي بالدمبل">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="row g-2 align-items-end">
                        <div class="col-6 col-md-1">
                            <input type="text" name="program[day_${dayId}][exercises][ex_${currentExerciseIdx}][sets]" class="form-control form-control-solid text-center" placeholder="4">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-6 col-md-1">
                            <input type="text" name="program[day_${dayId}][exercises][ex_${currentExerciseIdx}][reps]" class="form-control form-control-solid text-center" placeholder="10">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-6 col-md-1">
                            <input type="text" name="program[day_${dayId}][exercises][ex_${currentExerciseIdx}][rest]" class="form-control form-control-solid text-center" placeholder="90s">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-6 col-md-1">
                            <input type="text" name="program[day_${dayId}][exercises][ex_${currentExerciseIdx}][weight]" class="form-control form-control-solid text-center" placeholder="20kg">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-6 col-md-1">
                            <input type="text" name="program[day_${dayId}][exercises][ex_${currentExerciseIdx}][tempo]" class="form-control form-control-solid text-center" placeholder="3-0-1">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-5">
                            <input type="url" name="program[day_${dayId}][exercises][ex_${currentExerciseIdx}][link]" class="form-control form-control-solid" placeholder="https://youtube.com/...">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-2 d-flex justify-content-end">
                            <button type="button" class="btn btn-icon btn-light-danger btn-sm delete-exercise-btn w-100"><i class="fas fa-trash fs-6"></i></button>
                        </div>
                    </div>
                </div>`;

                container.append(newExerciseRow);
                container.find('.exercise-row').last().slideDown(200);
            });

            $(document).on('click', '.delete-exercise-btn', function () {
                var row = $(this).closest('.exercise-row');
                var container = $(this).closest('[id^="exercises-container-day-"]');

                if (container.find('.exercise-row').length > 1) {
                    row.slideUp(200, function () {
                        $(this).remove();
                    });
                } else {
                    container.find('.exercise-row input').val('');
                }
            });
        });
    </script>
@endsection
