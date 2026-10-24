@php
    $totalSets = $exercise->getTotalSetsCount();
@endphp
<tr id="inline-load-row-{{ $exercise->id }}" class="inline-load-adjustment-row d-none" style="background: rgba(0, 158, 247, 0.04);">
    <td colspan="9" class="p-4">
        <form class="inline-load-form d-flex flex-column gap-3" data-exercise-id="{{ $exercise->id }}">
            <input type="hidden" name="exercise_id" value="{{ $exercise->id }}">
            
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom pb-2" style="border-color: rgba(0, 158, 247, 0.15) !important;">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-circle badge-primary" style="width: 22px; height: 22px; font-size: 0.75rem;"><i class="fas fa-sliders-h text-white fs-9"></i></span>
                    <span class="fs-7 fw-bold text-gray-800">{{ __('طلب تعديل أوزان المجموعات') }} - <span class="text-primary">{{ $exercise->name }}</span></span>
                </div>
                <span class="fs-8 text-muted">{{ __('اختر المجموعة أو المجموعات وأدخل الوزن المطلوب لكل منها:') }}</span>
            </div>

            {{-- Sets Selection List --}}
            <div class="d-flex flex-wrap align-items-center gap-3 set-inputs-container py-1">
                @for($s = 1; $s <= $totalSets; $s++)
                    @php
                        $setBase = $exercise->getWeightForSet($s);
                    @endphp
                    <div class="set-selection-card p-2 border rounded-3 bg-white shadow-xs d-flex align-items-center gap-2" style="border-color: #e4e6ef; min-width: 170px;">
                        <div class="form-check form-check-custom form-check-solid form-check-sm m-0">
                            <input class="form-check-input set-checkbox cursor-pointer" type="checkbox" id="set-chk-{{ $exercise->id }}-{{ $s }}" data-set="{{ $s }}" value="{{ $s }}">
                            <label class="form-check-label fw-bold text-gray-800 fs-8 cursor-pointer" for="set-chk-{{ $exercise->id }}-{{ $s }}">
                                {{ __('Set') }} {{ $s }}
                            </label>
                        </div>
                        <div class="d-flex flex-column">
                            @if($setBase)
                                <span class="fs-9 text-muted" title="{{ __('الوزن الحالي المقيد') }}">الحالي: <strong class="text-gray-700">{{ $setBase }}</strong></span>
                            @else
                                <span class="fs-9 text-muted">—</span>
                            @endif
                        </div>
                        <input type="text" class="form-control form-control-sm form-control-solid set-weight-input text-center ms-auto" 
                               data-set="{{ $s }}"
                               placeholder="{{ $setBase ?: __('الوزن') }}" 
                               style="width: 78px; font-size: 0.8rem; padding: 2px 6px;" 
                               disabled>
                    </div>
                @endfor
            </div>

            {{-- Comment & Submit Controls --}}
            <div class="d-flex flex-wrap align-items-center gap-3 pt-2">
                <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 220px;">
                    <span class="fs-8 fw-bold text-gray-700 text-nowrap"><i class="fas fa-comment-dots text-info me-1"></i>{{ __('ملاحظة للمدرب') }}:</span>
                    <input type="text" name="notes" class="form-control form-control-sm form-control-solid" 
                           placeholder="{{ __('اكتب ملاحظتك هنا (اختياري)...') }}">
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="submit" class="btn btn-sm btn-primary px-4 py-2 d-inline-flex align-items-center gap-2">
                        <span class="btn-text">{{ __('إرسال الطلب') }}</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                    <button type="button" class="btn btn-sm btn-light btn-close-inline-load px-3 py-2" data-target="#inline-load-row-{{ $exercise->id }}">
                        {{ __('إلغاء') }}
                    </button>
                </div>
            </div>
            <div class="inline-feedback-msg w-100 fs-8 d-none mt-1"></div>
        </form>
    </td>
</tr>
