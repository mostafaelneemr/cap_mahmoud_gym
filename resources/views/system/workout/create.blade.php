@extends('system.layout')

@section('header')
    @include('system.workout.partials.exercise-table-styles')
    @include('system.workout.partials.warmup-editor-assets')
    @include('system.workout.partials.workout-media-assets')
    @include('system.workout.partials.exercise-library-modal')
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
        function exerciseRowHtml(dayId, exIdx, type = 'main') {
            var uniqueKey = type + '_' + Date.now() + '_' + Math.floor(Math.random() * 10000);
            var base = `program[day_${dayId}][exercises][${uniqueKey}]`;
            var isMain = (type === 'main');

            var mediaCellHtml = isMain ? `
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
            ` : `
                <td colspan="6" data-label="@lang('Video Link')">
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
            `;

            return `
            <tbody class="exercise-row wb-row-enter" data-exercise-type="${type}">
                <tr class="wb-row-main">
                    <td class="wb-cell-idx" rowspan="3">
                        <input type="hidden" name="${base}[type]" value="${type}">
                        <input type="hidden" name="${base}[exercise_library_id]" class="wb-library-id" value="">
                        <span class="wb-idx" aria-hidden="true">${exIdx}</span>
                    </td>
                    <td class="wb-cell-name" data-label="@lang('Exercise')">
                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                            <button type="button" class="btn btn-icon btn-sm btn-light-primary btn-open-library-picker" title="@lang('Pick from Central Exercise Library')">
                                <i class="fas fa-book-open fs-7"></i>
                            </button>
                            <span class="badge badge-light-primary wb-library-badge d-none fs-9">
                                <i class="fas fa-link me-1"></i> @lang('Library')
                            </span>
                            <button type="button" class="btn btn-icon btn-sm btn-light-danger btn-unlink-library d-none" title="@lang('Unlink from Library')">
                                <i class="fas fa-unlink fs-8"></i>
                            </button>
                        </div>
                        <input type="text" name="${base}[name]" class="form-control wb-name" maxlength="150" autocomplete="off"
                               placeholder="${isMain ? '@lang('e.g. Incline Bench Press')' : (type === 'warmup' ? '@lang('e.g. Arm Circles / Shoulder Dislocates')' : '@lang('e.g. Static Quad Stretch / Foam Rolling')')}" aria-label="@lang('Exercise Name')">
                        <div class="invalid-feedback"></div>
                    </td>
                    <td data-label="@lang('Sets')">
                        <input type="text" name="${base}[sets]" class="form-control wb-num" maxlength="50" inputmode="numeric"
                               placeholder="${type === 'warmup' ? '2' : (isMain ? '4' : '1')}" aria-label="@lang('Sets')">
                    </td>
                    <td data-label="@lang('Reps')">
                        <input type="text" name="${base}[reps]" class="form-control wb-num" maxlength="50"
                               placeholder="${type === 'warmup' ? '15' : (isMain ? '8-12' : '30s')}" aria-label="@lang('Reps')">
                    </td>
                    <td data-label="@lang('Rest')">
                        <input type="text" name="${base}[rest]" class="form-control wb-num" maxlength="50"
                               placeholder="${type === 'warmup' ? '30s' : (isMain ? '90s' : '0s')}" aria-label="@lang('Rest')">
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
                    ${mediaCellHtml}
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

        function richTextSectionHtml(dayId, section) {
            var isWarmup = (section === 'warmup');
            var editorId = (isWarmup ? 'warmup_editor_step_' : 'post_workout_editor_step_') + dayId;
            var inputId = (isWarmup ? 'warmup_input_step_' : 'post_workout_input_step_') + dayId;
            var inputName = `program[day_${dayId}][${isWarmup ? 'warmup' : 'post_workout'}]`;
            var title = isWarmup ? "@lang('Warmup Routine')" : "@lang('Post-Workout Routine')";
            var subtitle = isWarmup ? "@lang('Warmup exercises, activation movements and dynamic stretches')" : "@lang('Cooldown exercises, static stretches and recovery')";
            var icon = isWarmup ? 'fas fa-fire' : 'fas fa-spa';
            var themeClass = isWarmup ? 'wb-exercises--warmup' : 'wb-exercises--post';
            var iconColor = isWarmup ? '#A78BFA' : '#38BDF8';
            var iconBg = isWarmup ? 'rgba(167, 139, 250, 0.15)' : 'rgba(56, 189, 248, 0.15)';
            var placeholder = isWarmup ? "@lang('Warm up instructions, stretch routine, embed video links...')" : "@lang('Cooldown exercises, static stretches and recovery instructions...')";

            return `
            <div class="mb-6 wb-rich-section ${themeClass}">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="d-inline-flex align-items-center justify-content-center rounded" style="width: 28px; height: 28px; background: ${iconBg}; color: ${iconColor};">
                        <i class="${icon} fs-6"></i>
                    </span>
                    <div>
                        <h4 class="mb-0 fs-6 fw-bold wb-rich-title">${title}</h4>
                        <p class="mb-0 fs-8 wb-rich-desc">${subtitle}</p>
                    </div>
                </div>
                <div class="warmup-quill-wrapper">
                    <div id="${editorId}" class="warmup-quill-editor" data-placeholder="${placeholder}"></div>
                    <input type="hidden" name="${inputName}" id="${inputId}" class="warmup-hidden-input" value="">
                </div>
            </div>`;
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

                    {{-- 1. Warmup Rich Text Editor --}}
                    ${richTextSectionHtml(i, 'warmup')}

                    {{-- 2. Main Exercises Section --}}
                    <section class="wb-exercises mb-6" aria-label="@lang('Exercises')">
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
                                ${exerciseRowHtml(i, 1, 'main')}
                            </table>
                        </div>

                        <div class="wb-exercises__footer">
                            <button type="button" class="wb-add add-exercise-btn" data-day-id="${i}">
                                <i class="fas fa-plus"></i>@lang('Add Exercise')
                            </button>
                        </div>
                    </section>

                    {{-- 3. Post-Workout Rich Text Editor --}}
                    ${richTextSectionHtml(i, 'post_workout')}
                </div>
            </div>`;
        }

        function reindexExerciseTable(container) {
            container.find('tbody.exercise-row').each(function(idx) {
                $(this).find('.wb-idx').text(idx + 1);
            });
        }

        function updateExerciseCount(container) {
            var id = container.attr('id') || '';
            if (id.startsWith('warmup-exercises-container-day-')) {
                var dayId = id.replace('warmup-exercises-container-day-', '');
                $(`#warmup-count-day-${dayId}`).text(container.find('.exercise-row').length);
            } else if (id.startsWith('post-workout-exercises-container-day-')) {
                var dayId = id.replace('post-workout-exercises-container-day-', '');
                $(`#post-workout-count-day-${dayId}`).text(container.find('.exercise-row').length);
            } else if (id.startsWith('exercises-container-day-')) {
                var dayId = id.replace('exercises-container-day-', '');
                $(`#exercise-count-day-${dayId}`).text(container.find('.exercise-row').length);
            }
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

                var currentExerciseIdx = parseInt(container.attr('data-exercise-index') || 0) + 1;
                container.attr('data-exercise-index', currentExerciseIdx);

                var $row = $(exerciseRowHtml(dayId, currentExerciseIdx, 'main'));
                container.append($row);
                reindexExerciseTable(container);
                updateExerciseCount(container);
                $row.find('.wb-name').trigger('focus');
            });

            $(document).on('click', '.delete-exercise-btn', function () {
                var row = $(this).closest('.exercise-row');
                var container = $(this).closest('table.wb-table');
                var isMain = (container.attr('id') || '').startsWith('exercises-container-day-');

                if (!isMain) {
                    row.removeClass('wb-row-enter').addClass('wb-row-leave');
                    setTimeout(function () {
                        row.remove();
                        reindexExerciseTable(container);
                        updateExerciseCount(container);
                        if (container.find('.exercise-row').length === 0) {
                            container.find('.wb-empty-placeholder').show();
                        }
                    }, 200);
                } else {
                    if (container.find('.exercise-row').length > 1) {
                        row.removeClass('wb-row-enter').addClass('wb-row-leave');
                        setTimeout(function () {
                            row.remove();
                            reindexExerciseTable(container);
                            updateExerciseCount(container);
                        }, 200);
                    } else {
                        container.find('.exercise-row input:not([name*="[type]"]), .exercise-row textarea').val('');
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
