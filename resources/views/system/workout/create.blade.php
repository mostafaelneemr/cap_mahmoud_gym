@extends('system.layout')

@section('header')
    @include('system.workout.partials.exercise-table-styles')
    @include('system.workout.partials.warmup-editor-assets')
    @include('system.workout.partials.workout-media-assets')
    <style>
        .is-invalid + .select2-container .select2-selection,
        .select2-container--bootstrap5.is-invalid .select2-selection,
        .select2-container .select2-selection.border-danger {
            border-color: #f1416c !important;
        }
    </style>
@endsection

@section('content')

    {!! Form::open(['id'=>'main-form','files' => true, 'enctype' => 'multipart/form-data', 'onsubmit' => 'FormSubmit("'.route('system.workout.store') .'");return false;','method' => 'POST']) !!}
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


        <div class="flex-row-fluid py-lg-5 px-lg-5 wb-scope">
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

                            <select name="trainee_id" id="trainee_id-form-input" class="form-select form-select-solid" data-control="select2" data-placeholder="@lang('Select Trainee')">
                                <option value="">@lang('Select Trainee')</option>
                                @foreach($trainees as $trainee)
                                    <option value="{{ $trainee->id }}" {{ (isset($selectedTraineeId) && $selectedTraineeId == $trainee->id) ? 'selected' : '' }}>
                                        {{ $trainee->user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="trainee_id-form-error"></div>
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
    @include('system.workout.partials.weight-inputs-script')
    <script type="text/javascript">

        $(document).ready(function() {
            @if(isset($selectedTraineeId) && $selectedTraineeId)
            $('select[name="trainee_id"]').val('{{ $selectedTraineeId }}').trigger('change');
            @endif
        });

        /**
         * One exercise = one <tbody class="exercise-row"> (main metrics row + detail row).
         * Field names are unchanged: program[day_X][exercises][ex_Y][...]
         */
        function exerciseRowHtml(dayId, exIdx) {
            var base = `program[day_${dayId}][exercises][ex_${exIdx}]`;
            return `
            <tbody class="exercise-row wb-row-enter">
                <tr class="wb-row-main">
                    <td class="wb-cell-idx" rowspan="3"><span class="wb-idx" aria-hidden="true"></span></td>
                    <td class="wb-cell-name" data-label="@lang('Exercise')">
                        <input type="text" name="${base}[name]" class="form-control wb-name" maxlength="150" autocomplete="off"
                               placeholder="@lang('e.g. Incline Bench Press')" aria-label="@lang('Exercise Name')">
                        <div class="invalid-feedback"></div>
                    </td>
                    <td data-label="@lang('Sets')">
                        <input type="text" name="${base}[sets]" class="form-control wb-num" maxlength="50" inputmode="numeric"
                               placeholder="4" aria-label="@lang('Sets')">
                    </td>
                    <td data-label="@lang('Reps')">
                        <input type="text" name="${base}[reps]" class="form-control wb-num" maxlength="50"
                               placeholder="8-12" aria-label="@lang('Reps')">
                    </td>
                    <td data-label="@lang('Rest')">
                        <input type="text" name="${base}[rest]" class="form-control wb-num" maxlength="50"
                               placeholder="90s" aria-label="@lang('Rest')">
                    </td>
                    <td data-label="@lang('Tempo')">
                        <input type="text" name="${base}[tempo]" class="form-control wb-num" maxlength="50"
                               placeholder="3-0-1" aria-label="@lang('Tempo')">
                    </td>
                    <td class="wb-cell-weights" data-label="@lang('Weights') (max 4)">
                        ${GymWeights.build(`${base}[internal_weight]`)}
                    </td>
                    <td class="wb-cell-action" rowspan="3">
                        <button type="button" class="wb-delete delete-exercise-btn" title="@lang('Delete Exercise')" aria-label="@lang('Delete Exercise')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                <tr class="wb-row-detail wb-row-media">
                    <td colspan="3" data-label="@lang('Video Link')">
                        <span class="wb-mini-label">@lang('Video Link')</span>
                        <div class="wb-video-link-wrap">
                            <div class="wb-input-icon">
                                <i class="fab fa-youtube"></i>
                                <input type="url" name="${base}[link]" class="form-control wb-video-input" maxlength="255"
                                       placeholder="https://video link.com/..." aria-label="@lang('Video Link')">
                            </div>
                            <button type="button" class="wb-video-preview-btn is-disabled"
                                    title="@lang('Preview / Open Video')"
                                    aria-label="@lang('Preview / Open Video')"
                                    aria-disabled="true">
                                <i class="fas fa-external-link-alt"></i>
                            </button>
                        </div>
                    </td>
                    <td colspan="3" data-label="@lang('Machine / Equipment Photo')">
                        <span class="wb-mini-label">@lang('Machine Photo')</span>
                        ${GymMedia.buildMachineImage(base, '', '')}
                    </td>
                </tr>
                <tr class="wb-row-detail wb-row-notes">
                    <td colspan="6" data-label="@lang('Coach Notes')">
                        <span class="wb-mini-label">@lang('Coach Notes')</span>
                        <textarea name="${base}[notes_en]" rows="1" class="form-control wb-notes"
                                  placeholder="@lang('Form cues, progression, tips...')" aria-label="@lang('Coach Notes')"></textarea>
                    </td>
                </tr>
            </tbody>`;
        }

        function dayStepHtml(i) {
            return `
            <div class="card-body dynamic-step" data-kt-stepper-element="content">
                <div class="w-100 wb-day">
                    <div class="wb-day__header">
                        <span class="wb-day__badge">D${i}</span>
                        <div>
                            <h2 class="wb-day__title">@lang('Title Program') (${i})</h2>
                            <p class="wb-day__subtitle">@lang('Name the session, then build the exercise list')</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="form-label">@lang('Day Name')</label>
                        <input type="text" name="program[day_${i}][day_name]" class="form-control form-control-solid" maxlength="100"
                               placeholder="e.g. Day ${i}: Chest & Triceps"/>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label wb-field-label"><i class="fas fa-fire text-warning me-1"></i>@lang('Warmup Instructions & Video Links')</label>
                        <div class="warmup-quill-wrapper">
                            <div id="warmup_editor_step_${i}" class="warmup-quill-editor" data-placeholder="@lang('Warm up instructions, stretch routine, embed video links...')"></div>
                            <input type="hidden" name="program[day_${i}][warmup]" id="warmup_input_step_${i}" class="warmup-hidden-input">
                        </div>
                    </div>

                    <section class="wb-exercises" aria-label="@lang('Exercises')">
                        <header class="wb-exercises__header">
                            <div class="wb-exercises__title">
                                <span class="wb-exercises__icon"><i class="fas fa-dumbbell"></i></span>
                                <div>
                                    <h3>@lang('Exercises')</h3>
                                    <p>@lang('Sets, reps, rest, tempo and up to 4 target weights')</p>
                                </div>
                            </div>
                            <span class="wb-count"><span class="wb-count__num" id="exercise-count-day-${i}">1</span> @lang('Exercises')</span>
                        </header>

                        <div class="wb-table-wrap">
                            <table class="wb-table" id="exercises-container-day-${i}" data-exercise-index="1">
                                <colgroup>
                                    <col class="wb-col-idx">
                                    <col class="wb-col-name">
                                    <col class="wb-col-num">
                                    <col class="wb-col-num">
                                    <col class="wb-col-num">
                                    <col class="wb-col-tempo">
                                    <col class="wb-col-weights">
                                    <col class="wb-col-action">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">@lang('Exercise')</th>
                                        <th scope="col" class="text-center">@lang('Sets')</th>
                                        <th scope="col" class="text-center">@lang('Reps')</th>
                                        <th scope="col" class="text-center">@lang('Rest')</th>
                                        <th scope="col" class="text-center">@lang('Tempo')</th>
                                        <th scope="col">@lang('Weights')<span class="wb-th-hint">max 4</span></th>
                                        <th scope="col"><span class="visually-hidden">@lang('Actions')</span></th>
                                    </tr>
                                </thead>
                                ${exerciseRowHtml(i, 1)}
                            </table>
                        </div>

                        <div class="wb-exercises__footer">
                            <button type="button" class="wb-add add-exercise-btn" data-day-id="${i}">
                                <i class="fas fa-plus"></i>@lang('Add Exercise')
                            </button>
                        </div>
                    </section>

                    <div class="mb-5">
                        <label class="form-label wb-field-label"><i class="fas fa-snowflake text-info me-1"></i>@lang('Post-Workout Instructions & Video Links')</label>
                        <div class="warmup-quill-wrapper">
                            <div id="post_workout_editor_step_${i}" class="warmup-quill-editor" data-placeholder="@lang('Cool down instructions, post-stretch routine, embed video links, table...')"></div>
                            <input type="hidden" name="program[day_${i}][post_workout]" id="post_workout_input_step_${i}" class="warmup-hidden-input">
                        </div>
                    </div>
                </div>
            </div>`;
        }

        function updateExerciseCount(container) {
            var dayId = (container.attr('id') || '').replace('exercises-container-day-', '');
            $(`#exercise-count-day-${dayId}`).text(container.find('.exercise-row').length);
        }

        $(document).ready(function () {
            var stepperElement = document.querySelector("#kt_dynamic_stepper");
            var stepperObj;

            var navContainer = $("#stepper-nav-container");
            var contentContainer = $("#stepper-content-container");

            function validateStep1() {
                var $traineeSelect = $('select[name="trainee_id"]');
                var traineeId = $traineeSelect.val();
                var isValid = true;

                if (!traineeId) {
                    $traineeSelect.addClass('is-invalid');
                    $traineeSelect.next('.select2-container').find('.select2-selection').addClass('border-danger');
                    $('#trainee_id-form-error').text("{{ __('Please select a trainee.') }}").css('display', 'block');
                    if (typeof notify === 'function') {
                        notify("{{ __('Please select a trainee.') }}", 'error');
                    }
                    isValid = false;
                } else {
                    $traineeSelect.removeClass('is-invalid');
                    $traineeSelect.next('.select2-container').find('.select2-selection').removeClass('border-danger');
                    $('#trainee_id-form-error').text('').css('display', 'none');
                }

                var daysCount = $("#days_count").val();
                if (!daysCount) {
                    if (typeof notify === 'function') {
                        notify("{{ __('Please select the number of training days.') }}", 'error');
                    } else {
                        alert("{{ __('Please select the number of training days.') }}");
                    }
                    isValid = false;
                }

                return isValid;
            }

            $('select[name="trainee_id"]').on('change select2:select', function () {
                if ($(this).val()) {
                    $(this).removeClass('is-invalid');
                    $(this).next('.select2-container').find('.select2-selection').removeClass('border-danger');
                    $('#trainee_id-form-error').text('').css('display', 'none');
                }
            });

            function validateDayExercises(dayNum) {
                var container = $(`#exercises-container-day-${dayNum}`);
                if (!container.length) return true;

                var validCount = 0;
                container.find('.wb-name').each(function () {
                    if ($.trim($(this).val()) !== '') {
                        validCount++;
                    }
                });

                if (validCount === 0) {
                    var $firstInput = container.find('.wb-name').first();
                    $firstInput.addClass('is-invalid');
                    $firstInput.siblings('.invalid-feedback')
                        .text("{{ __('Please enter an exercise name. Cannot approve workout without exercises.') }}")
                        .css('display', 'block');
                    $firstInput.trigger('focus');
                    var errorMsg = "{{ __('Day :num has no exercises. Cannot approve workout without exercises.', ['num' => ':NUM']) }}".replace(':NUM', dayNum);
                    if (typeof notify === 'function') {
                        notify(errorMsg, 'error');
                    } else {
                        alert(errorMsg);
                    }
                    return false;
                }

                return true;
            }

            function validateAllWorkoutDays() {
                var daysCount = parseInt($("#days_count").val()) || 0;
                for (var i = 1; i <= daysCount; i++) {
                    if (!validateDayExercises(i)) {
                        if (stepperObj) {
                            stepperObj.goTo(i + 1);
                        }
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                        return false;
                    }
                }
                return true;
            }

            $(document).on('input', '.wb-name', function () {
                if ($.trim($(this).val()) !== '') {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').text('').css('display', 'none');
                }
            });

            // Ensure all Quill editor instances sync their HTML into hidden inputs before submit
            $(document).on('click', '#main-form button[type="submit"], #main-form input[type="submit"]', function () {
                if (typeof WarmupQuill !== 'undefined') {
                    WarmupQuill.syncAll();
                }
            });

            $('#main-form').on('submit', function (e) {
                if (typeof WarmupQuill !== 'undefined') {
                    WarmupQuill.syncAll();
                }

                if (!validateStep1()) {
                    e.preventDefault();
                    if (stepperObj) {
                        stepperObj.goTo(1);
                    }
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                    return false;
                }

                if (!validateAllWorkoutDays()) {
                    e.preventDefault();
                    return false;
                }
            });

            $(document).on('click', '[data-kt-stepper-action="next"]', function (e) {

                if (!stepperObj || stepperObj.getCurrentStepIndex() === 1) {
                    if (!validateStep1()) {
                        e.preventDefault();
                        return false;
                    }

                    var daysCount = $("#days_count").val();
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
                        contentContainer.append(dayStepHtml(i));
                        if (typeof WarmupQuill !== 'undefined') {
                            WarmupQuill.init(`#warmup_editor_step_${i}`, `#warmup_input_step_${i}`);
                            WarmupQuill.init(`#post_workout_editor_step_${i}`, `#post_workout_input_step_${i}`);
                        }
                    }

                    stepperObj = new KTStepper(stepperElement);

                    stepperObj.on("kt.stepper.next", function (stepper) {
                        var currentStep = stepper.getCurrentStepIndex();
                        if (currentStep >= 2) {
                            var dayNum = currentStep - 1;
                            if (!validateDayExercises(dayNum)) {
                                return false;
                            }
                        }
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

                var $row = $(exerciseRowHtml(dayId, currentExerciseIdx));
                container.append($row);
                updateExerciseCount(container);
                $row.find('.wb-name').trigger('focus');
            });

            $(document).on('click', '.delete-exercise-btn', function () {
                var row = $(this).closest('.exercise-row');
                var container = $(this).closest('[id^="exercises-container-day-"]');

                if (container.find('.exercise-row').length > 1) {
                    row.removeClass('wb-row-enter').addClass('wb-row-leave');
                    setTimeout(function () {
                        row.remove();
                        updateExerciseCount(container);
                    }, 200);
                } else {
                    container.find('.exercise-row input, .exercise-row textarea').val('');
                    container.find('.wb-notes').css('height', '');
                    GymWeights.reset(container.find('.exercise-row'));
                    var $mi = container.find('.exercise-row .mi-wrapper');
                    $mi.removeClass('has-image').find('.mi-file-input').val('');
                    $mi.find('.mi-preview-img').attr('src', '');
                    $mi.find('.mi-preview-btn').attr('data-img-url', '');
                    $mi.find('.mi-upload-text').text("{{ __('Machine Photo') }}");
                    $mi.find('.mi-remove-flag').val('0');
                    container.find('.exercise-row .wb-video-preview-btn').addClass('is-disabled').attr('aria-disabled', 'true');
                }
            });

            // Auto-grow coach notes
            $(document).on('input', '.wb-notes', function () {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });
    </script>
@endsection
