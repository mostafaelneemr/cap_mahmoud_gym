<tr class="coach-set-items-detail-row bg-light" id="coach-items-detail-{{ $adj->id }}">
    <td colspan="8" class="p-3">
        <div class="card card-bordered rounded-3 shadow-none border p-3" style="background: #fafbfc; border-color: #e4e6ef !important;">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-circle badge-primary" style="width: 22px; height: 22px; font-size: 0.75rem;"><i class="fas fa-layer-group text-white fs-9"></i></span>
                    <strong class="text-gray-800 fs-8">{{ __('مجموعات التمرين المطلوب تعديلها ومراجعتها بشكل مستقل:') }}</strong>
                </div>
                <span class="fs-9 text-muted">{{ __('يمكن اعتماد أو رفض كل مجموعة بشكل مستقل، أو تحديد وزن بديل') }}</span>
            </div>

            <div class="d-flex flex-column gap-3">
                @foreach($adj->items as $item)
                    @php
                        $currentPersisted = $adj->exercise ? $adj->exercise->getWeightForSet($item->set_number) : null;
                        $hasConflictNow = ($item->status === 'conflict') || ($item->isPending() && $currentPersisted !== $item->base_weight);
                    @endphp
                    <div class="coach-item-review-card p-3 rounded border bg-white {{ $hasConflictNow ? 'border-warning shadow-xs' : '' }}" 
                         id="coach-item-card-{{ $item->id }}" style="border-color: {{ $hasConflictNow ? '#ffa800' : '#e4e6ef' }} !important;">
                        
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge badge-light-primary fw-bold fs-8 px-2 py-1">
                                    {{ __('المجموعة') }} {{ $item->set_number }}
                                </span>
                                <div class="fs-8">
                                    <span class="text-muted">{{ __('الأساسي:') }}</span> <strong>{{ $item->base_weight ?: '—' }}</strong>
                                    <span class="mx-2 text-gray-300">|</span>
                                    <span class="text-muted">{{ __('الحالي بالخطة:') }}</span> <strong>{{ $currentPersisted ?: '—' }}</strong>
                                    <span class="mx-2 text-gray-300">|</span>
                                    <span class="text-muted">{{ __('المطلوب:') }}</span> <strong class="text-primary fs-7">{{ $item->suggested_weight }}</strong>
                                    @if($item->approved_weight)
                                        <span class="mx-2 text-gray-300">|</span>
                                        <span class="text-muted">{{ __('المعتمد:') }}</span> <strong class="text-success fs-7">{{ $item->approved_weight }}</strong>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                {{-- Status Badge --}}
                                @if($item->status === 'approved')
                                    <span class="badge badge-light-success fs-8"><i class="fas fa-check me-1"></i>{{ __('معتمد ومطبق') }}</span>
                                @elseif($item->status === 'rejected')
                                    <span class="badge badge-light-danger fs-8"><i class="fas fa-times me-1"></i>{{ __('مرفوض') }}</span>
                                @elseif($hasConflictNow)
                                    <span class="badge badge-light-warning fs-8"><i class="fas fa-exclamation-triangle me-1"></i>{{ __('تعارض في الوزن') }}</span>
                                @else
                                    <span class="badge badge-light-warning fs-8"><i class="fas fa-clock me-1"></i>{{ __('قيد المراجعة') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Conflict Explanation Banner --}}
                        @if($hasConflictNow)
                            <div class="alert alert-warning py-2 px-3 mt-2 mb-2 fs-9 d-flex align-items-center gap-2">
                                <i class="fas fa-exclamation-circle fs-7 text-warning"></i>
                                <div>
                                    <strong>{{ __('تم رصد تعارض:') }}</strong>
                                    {{ __('تم تعديل وزن الخطة التدريبية (حالياً :current) بعد تقديم هذا الطلب (الأساسي كان :base). لحماية الخطة تم إيقاف التطبيق التلقائي.', ['current' => ($currentPersisted ?: '—'), 'base' => ($item->base_weight ?: '—')]) }}
                                </div>
                            </div>
                        @endif

                        {{-- Action Controls (only if pending or conflict) --}}
                        @if($item->isPending() || $hasConflictNow)
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-2 border-top" style="border-color: #f1f1f4 !important;">
                                <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 380px;">
                                    <input type="text" class="form-control form-control-sm form-control-solid coach-item-comment-input" 
                                           placeholder="{{ __('ملاحظة المدرب لهذه المجموعة (اختياري)...') }}" value="{{ $item->coach_notes }}">
                                </div>

                                <div class="d-flex align-items-center flex-wrap gap-2 ms-auto">
                                    @if($hasConflictNow)
                                        {{-- Conflict Resolution Actions --}}
                                        <button type="button" class="btn btn-sm btn-warning px-3 py-1 btn-coach-item-action fs-8" 
                                                data-item-id="{{ $item->id }}" data-action="resolve_approve" data-force="1">
                                            <i class="fas fa-bolt fs-9 me-1"></i>{{ __('فرض الاعتماد (:weight)', ['weight' => $item->suggested_weight]) }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light-danger px-3 py-1 btn-coach-item-action fs-8" 
                                                data-item-id="{{ $item->id }}" data-action="resolve_reject" data-force="1">
                                            <i class="fas fa-times fs-9 me-1"></i>{{ __('رفض الطلب المتعارض') }}
                                        </button>
                                    @else
                                        {{-- Normal Set Approval / Alternative / Rejection --}}
                                        <button type="button" class="btn btn-sm btn-success px-3 py-1 btn-coach-item-action fs-8" 
                                                data-item-id="{{ $item->id }}" data-action="approve" title="{{ __('اعتماد الوزن المقترح') }}">
                                            <i class="fas fa-check fs-9 me-1"></i>{{ __('اعتماد (:weight)', ['weight' => $item->suggested_weight]) }}
                                        </button>

                                        {{-- Alternative Weight Input & Button --}}
                                        <div class="d-flex align-items-center gap-1">
                                            <input type="text" class="form-control form-control-sm form-control-solid coach-item-alt-weight text-center" 
                                                   placeholder="{{ __('وزن بديل') }}" style="width: 80px; font-size: 0.8rem; padding: 2px 6px;">
                                            <button type="button" class="btn btn-sm btn-light-primary px-2 py-1 btn-coach-item-action fs-8" 
                                                    data-item-id="{{ $item->id }}" data-action="approve_alternative" title="{{ __('اعتماد بوزن بديل يحدده المدرب') }}">
                                                {{ __('اعتماد بوزن بديل') }}
                                            </button>
                                        </div>

                                        <button type="button" class="btn btn-sm btn-light-danger px-2 py-1 btn-coach-item-action fs-8" 
                                                data-item-id="{{ $item->id }}" data-action="reject" title="{{ __('رفض هذا الطلب') }}">
                                            <i class="fas fa-times fs-9 me-1"></i>{{ __('رفض') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @else
                            {{-- Historical Decision Record for this Set --}}
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2 pt-2 border-top fs-9 text-muted" style="border-color: #f1f1f4 !important;">
                                <div>
                                    <i class="fas fa-user-check text-info me-1"></i>
                                    <span>{{ __('المراجع:') }} {{ $item->reviewer ? $item->reviewer->name : __('المدرب') }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $item->reviewed_at ? $item->reviewed_at->format('Y-m-d H:i') : '' }}</span>
                                </div>
                                @if($item->coach_notes)
                                    <div class="d-flex flex-column align-items-end text-end">
                                        <div class="text-gray-800">
                                            <i class="fas fa-comment text-primary me-1"></i>{{ $item->coach_notes }}
                                        </div>
                                        <div class="fs-9 text-muted comment-metadata mt-1">
                                            <span class="comment-author">{{ __('Added by') }}: <strong class="text-gray-700">{{ $item->coach_author_name ?: ($item->reviewer ? $item->reviewer->name : __('المدرب')) }}</strong></span>
                                            @if($item->coach_notes_timestamp)
                                                <span class="mx-1">•</span>
                                                <span class="comment-time">{{ __('Added at') }}: {{ $item->coach_notes_timestamp->format('Y-m-d H:i') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        </div>
    </td>
</tr>
