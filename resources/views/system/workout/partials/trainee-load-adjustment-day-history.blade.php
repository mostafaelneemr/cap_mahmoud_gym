@php
    $hasAdjustments = \Illuminate\Support\Facades\Schema::hasTable('trainee_load_adjustments') && $plan->relationLoaded('loadAdjustments');
    $dayAdjustments = $hasAdjustments && $plan->loadAdjustments ? $plan->loadAdjustments : collect();
@endphp

<div class="day-load-history-section mt-8 pt-6 border-top" style="border-color: rgba(255, 255, 255, 0.08) !important;">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge badge-circle badge-light-primary" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                <i class="fas fa-history text-primary fs-7"></i>
            </span>
            <h4 class="fs-6 fw-bold m-0 text-gray-800 d-flex align-items-center gap-2">
                <span>{{ __('سجل تحديثات الأوزان لليوم التدريبي') }}</span>
                @if($dayAdjustments->count() > 0)
                    <span class="badge badge-light-primary fs-9 ms-1">{{ $dayAdjustments->count() }} {{ __('طلبات') }}</span>
                @endif
            </h4>
        </div>
        <button class="btn btn-sm btn-light-secondary btn-color-gray-700 py-1 px-3 fs-8" type="button" 
                data-bs-toggle="collapse" data-bs-target="#day-history-collapse-{{ $plan->id }}" 
                aria-expanded="{{ $dayAdjustments->count() > 0 ? 'true' : 'false' }}">
            <i class="fas fa-chevron-down fs-9 me-1"></i>
            <span>{{ __('عرض / إخفاء السجل') }}</span>
        </button>
    </div>

    <div class="collapse {{ $dayAdjustments->count() > 0 ? 'show' : '' }}" id="day-history-collapse-{{ $plan->id }}">
        @if($dayAdjustments->isEmpty())
            <div class="p-4 rounded-3 text-center text-muted border border-dashed" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.1) !important;">
                <i class="fas fa-clipboard-check fs-3 mb-2 text-gray-500 d-block"></i>
                <p class="fs-8 mb-0">{{ __('لا توجد طلبات تحديث أوزان مسجلة لهذا اليوم التدريبي حتى الآن.') }}</p>
            </div>
        @else
            <div class="d-flex flex-column gap-4">
                @foreach($dayAdjustments as $adj)
                    <div class="card rounded-3 shadow-none border" style="background: var(--wb-surface-solid, #ffffff); border-color: var(--wb-row-border, #e4e6ef) !important;">
                        {{-- Card Header --}}
                        <div class="card-header min-h-auto py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom" style="border-color: var(--wb-row-border, #e4e6ef) !important;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-gray-800 fs-7">{{ $adj->exercise_name }}</span>
                                <span class="text-muted fs-9">• {{ $adj->created_at ? $adj->created_at->format('Y-m-d H:i') : '' }}</span>
                            </div>
                            <div>
                                @if($adj->status === 'reviewed')
                                    <span class="badge badge-light-success fs-9"><i class="fas fa-check-circle me-1"></i>{{ __('تمت المراجعة') }}</span>
                                @elseif($adj->status === 'conflict')
                                    <span class="badge badge-light-warning fs-9"><i class="fas fa-exclamation-triangle me-1"></i>{{ __('تعارض قيد المراجعة') }}</span>
                                @elseif($adj->status === 'partially_reviewed')
                                    <span class="badge badge-light-info fs-9"><i class="fas fa-tasks me-1"></i>{{ __('مراجعة جزئية') }}</span>
                                @elseif($adj->status === 'dismissed')
                                    <span class="badge badge-light-secondary fs-9">{{ __('ملغي') }}</span>
                                @else
                                    <span class="badge badge-light-warning fs-9"><i class="fas fa-clock me-1"></i>{{ __('قيد المراجعة') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="card-body p-4">
                            @if($adj->notes)
                                <div class="mb-3 p-3 rounded bg-light-info text-gray-700 fs-8">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="fas fa-comment text-info mt-1 fs-9"></i>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                                <strong class="text-info">{{ __('ملاحظة المتدرب') }}:</strong>
                                                <span class="fs-9 text-muted comment-metadata">
                                                    <span class="comment-author">{{ __('Added by') }}: <strong class="text-gray-800">{{ $adj->trainee_author_name ?: __('المتدرب') }}</strong></span>
                                                    @if($adj->created_at)
                                                        <span class="mx-1">•</span>
                                                        <span class="comment-time">{{ __('Added at') }}: {{ $adj->created_at->format('Y-m-d H:i') }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="text-gray-800">{{ $adj->notes }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($adj->hasSetItems())
                                {{-- Set-Level Items Table --}}
                                <div class="table-responsive">
                                    <table class="table table-sm table-row-bordered align-middle gs-0 gy-2 mb-0" style="font-size: 0.8rem;">
                                        <thead>
                                            <tr class="fw-bold text-muted bg-light">
                                                <th class="ps-2 min-w-80px">{{ __('المجموعة') }}</th>
                                                <th class="min-w-90px">{{ __('الوزن الأساسي') }}</th>
                                                <th class="min-w-100px">{{ __('الوزن المطلوب') }}</th>
                                                <th class="min-w-100px">{{ __('الوزن المعتمد') }}</th>
                                                <th class="min-w-110px">{{ __('الحالة') }}</th>
                                                <th class="min-w-140px">{{ __('توجيهات المدرب') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($adj->items as $item)
                                                <tr>
                                                    <td class="ps-2 fw-bold text-gray-800">
                                                        {{ __('المجموعة') }} {{ $item->set_number }}
                                                    </td>
                                                    <td>
                                                        <span class="text-muted">{{ $item->base_weight ?: '—' }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light-primary fw-bold">{{ $item->suggested_weight }}</span>
                                                    </td>
                                                    <td>
                                                        @if($item->approved_weight)
                                                            <span class="badge badge-light-success fw-bold">{{ $item->approved_weight }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item->status === 'approved')
                                                            <span class="badge badge-light-success fs-9">{{ __('معتمد ومطبق') }}</span>
                                                        @elseif($item->status === 'rejected')
                                                            <span class="badge badge-light-danger fs-9">{{ __('مرفوض') }}</span>
                                                        @elseif($item->status === 'conflict')
                                                            <span class="badge badge-light-warning fs-9" title="{{ __('تم تغيير وزن الخطة بعد تقديم الطلب') }}">{{ __('تعارض بالأوزان') }}</span>
                                                        @else
                                                            <span class="badge badge-light-warning fs-9">{{ __('قيد المراجعة') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item->coach_notes)
                                                            <div class="d-flex flex-column gap-1">
                                                                <span class="text-gray-800 fs-9 d-inline-flex align-items-start gap-1">
                                                                    <i class="fas fa-reply text-info fs-9 mt-1"></i>
                                                                    <span>{{ $item->coach_notes }}</span>
                                                                </span>
                                                                <span class="fs-9 text-muted comment-metadata">
                                                                    <span class="comment-author">{{ __('Added by') }}: <strong class="text-gray-700">{{ $item->coach_author_name ?: __('المدرب') }}</strong></span>
                                                                    @if($item->coach_notes_timestamp)
                                                                        <span class="mx-1">•</span>
                                                                        <span class="comment-time">{{ __('Added at') }}: {{ $item->coach_notes_timestamp->format('Y-m-d H:i') }}</span>
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @else
                                                            <span class="text-muted fs-9">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                {{-- Legacy Display --}}
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 fs-8">
                                    <div>
                                        <span class="text-muted">{{ __('الوزن السابق') }}:</span> <strong>{{ $adj->prescribed_load ?: '—' }}</strong>
                                        <span class="mx-2">|</span>
                                        <span class="text-muted">{{ __('الوزن المبلغ عنه') }}:</span> <strong class="text-primary">{{ $adj->reported_load ?: '—' }}</strong>
                                    </div>
                                    @if($adj->coach_notes)
                                        <div class="text-info fs-9 mt-2 p-2 rounded bg-light">
                                            <div class="d-flex align-items-center gap-1">
                                                <i class="fas fa-reply me-1"></i><strong>{{ __('رد المدرب') }}:</strong> <span class="text-gray-800">{{ $adj->coach_notes }}</span>
                                            </div>
                                            <div class="text-muted fs-9 mt-1 comment-metadata">
                                                <span class="comment-author">{{ __('Added by') }}: <strong class="text-gray-700">{{ $adj->coach_author_name ?: __('المدرب') }}</strong></span>
                                                @if($adj->coach_notes_timestamp)
                                                    <span class="mx-1">•</span>
                                                    <span class="comment-time">{{ __('Added at') }}: {{ $adj->coach_notes_timestamp->format('Y-m-d H:i') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if($adj->coach_notes && $adj->hasSetItems())
                                <div class="mt-3 p-3 rounded bg-light-success text-gray-800 fs-8">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="fas fa-check-double text-success mt-1 fs-9"></i>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                                <strong class="text-success">{{ __('توجيه عام من المدرب') }}:</strong>
                                                <span class="fs-9 text-muted comment-metadata">
                                                    <span class="comment-author">{{ __('Added by') }}: <strong class="text-gray-800">{{ $adj->coach_author_name ?: __('المدرب') }}</strong></span>
                                                    @if($adj->coach_notes_timestamp)
                                                        <span class="mx-1">•</span>
                                                        <span class="comment-time">{{ __('Added at') }}: {{ $adj->coach_notes_timestamp->format('Y-m-d H:i') }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div>{{ $adj->coach_notes }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
