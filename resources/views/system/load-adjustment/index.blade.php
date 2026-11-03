@extends('system.layout')

@section('content')
<div class="d-flex flex-column gap-7 gap-lg-10" id="load_adjustment_center">

    <!-- Header / Title Banner -->
    <div class="card card-flush shadow-sm">
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
                <div class="d-flex align-items-center gap-3">
                    <span class="symbol symbol-50px symbol-circle bg-light-primary">
                        <i class="ki-outline ki-weight text-primary fs-2x p-3"></i>
                    </span>
                    <div>
                        <h2 class="fs-2 fw-bold mb-1">{{ __('Load Adjustment Center') }}</h2>
                        <span class="text-muted fs-7">{{ __('متابعة واعتماد طلبات تعديل أوزان المجموعات للمتدربين بشكل مستقل وفوري.') }}</span>
                    </div>
                </div>
            </div>
            <div class="card-toolbar flex-row-fluid justify-content-end gap-3">
                <a href="{{ route('system.workout.index') }}" class="btn btn-sm btn-light">
                    <i class="ki-outline ki-clipboard-list fs-4 me-1"></i>
                    {{ __('Workout Plans') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Top KPI Summary Cards -->
    <div class="row g-5 g-xl-8">
        <!-- Pending Sets -->
        <div class="col-md-4">
            <div class="card card-flush shadow-sm border border-warning border-dashed h-100">
                <div class="card-body d-flex align-items-center justify-content-between p-6">
                    <div class="d-flex flex-column">
                        <span class="fs-7 fw-semibold text-muted mb-1">{{ __('مجموعات تنتظر القرار') }}</span>
                        <span class="fs-2hx fw-bold text-gray-900" id="stat-pending-sets">{{ $stats['pending_sets_count'] ?? 0 }}</span>
                        <span class="fs-8 text-warning mt-1">{{ __('مجموعات فردية تتطلب البت فيها') }}</span>
                    </div>
                    <span class="symbol symbol-50px symbol-circle bg-light-warning">
                        <i class="ki-outline ki-time text-warning fs-2x p-3"></i>
                    </span>
                </div>
            </div>
        </div>

        <!-- Requests Awaiting Review -->
        <div class="col-md-4">
            <div class="card card-flush shadow-sm border border-primary border-dashed h-100">
                <div class="card-body d-flex align-items-center justify-content-between p-6">
                    <div class="d-flex flex-column">
                        <span class="fs-7 fw-semibold text-muted mb-1">{{ __('طلبات قيد المراجعة') }}</span>
                        <span class="fs-2hx fw-bold text-gray-900" id="stat-pending-requests">{{ $stats['pending_requests_count'] ?? 0 }}</span>
                        <span class="fs-8 text-primary mt-1">{{ __('جلسات طلبات تعديل الحمل') }}</span>
                    </div>
                    <span class="symbol symbol-50px symbol-circle bg-light-primary">
                        <i class="ki-outline ki-clipboard-list text-primary fs-2x p-3"></i>
                    </span>
                </div>
            </div>
        </div>

        <!-- Conflicting Sets -->
        <div class="col-md-4">
            <div class="card card-flush shadow-sm border border-danger border-dashed h-100">
                <div class="card-body d-flex align-items-center justify-content-between p-6">
                    <div class="d-flex flex-column">
                        <span class="fs-7 fw-semibold text-muted mb-1">{{ __('مجموعات بها تعارض') }}</span>
                        <span class="fs-2hx fw-bold text-gray-900" id="stat-conflict-sets">{{ $stats['conflict_sets_count'] ?? 0 }}</span>
                        <span class="fs-8 text-danger mt-1">{{ __('تغير وزن الخطة بعد تقديم الطلب') }}</span>
                    </div>
                    <span class="symbol symbol-50px symbol-circle bg-light-danger">
                        <i class="ki-outline ki-information-2 text-danger fs-2x p-3"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="card card-flush shadow-sm">
        <div class="card-body p-6">
            <form method="GET" action="{{ route('system.load-adjustment.index') }}" class="row g-3 align-items-end">
                <!-- Trainee Search -->
                <div class="col-md-4">
                    <label class="form-label fs-8 fw-semibold text-gray-700">{{ __('بحث باسم المتدرب أو البريد') }}</label>
                    <div class="position-relative">
                        <i class="ki-outline ki-magnifier fs-4 position-absolute top-50 start-0 translate-middle-y ms-3 text-gray-400"></i>
                        <input type="text" name="trainee" class="form-control form-control-solid ps-10 fs-7" 
                               placeholder="{{ __('اكتب اسم أو بريد المتدرب...') }}" value="{{ $filters['trainee'] ?? '' }}">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="col-md-3">
                    <label class="form-label fs-8 fw-semibold text-gray-700">{{ __('حالة المراجعة') }}</label>
                    <select name="status" class="form-select form-select-solid fs-7">
                        <option value="pending" {{ ($filters['status'] ?? 'pending') === 'pending' ? 'selected' : '' }}>{{ __('قيد الانتظار والمراجعة الجزئية') }}</option>
                        <option value="conflict" {{ ($filters['status'] ?? '') === 'conflict' ? 'selected' : '' }}>{{ __('المتعارضة فقط') }}</option>
                        <option value="reviewed" {{ ($filters['status'] ?? '') === 'reviewed' ? 'selected' : '' }}>{{ __('تمت المراجعة بالكامل') }}</option>
                        <option value="dismissed" {{ ($filters['status'] ?? '') === 'dismissed' ? 'selected' : '' }}>{{ __('تم الإلغاء') }}</option>
                        <option value="all" {{ ($filters['status'] ?? '') === 'all' ? 'selected' : '' }}>{{ __('كافة الطلبات') }}</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div class="col-md-3">
                    <label class="form-label fs-8 fw-semibold text-gray-700">{{ __('التاريخ') }}</label>
                    <input type="date" name="date" class="form-control form-control-solid fs-7" value="{{ $filters['date'] ?? '' }}">
                </div>

                <!-- Actions -->
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fs-7">
                        <i class="ki-outline ki-filter fs-4 me-1"></i>
                        {{ __('تصفية') }}
                    </button>
                    @if(!empty($filters['trainee']) || ($filters['status'] ?? 'pending') !== 'pending' || !empty($filters['date']))
                        <a href="{{ route('system.load-adjustment.index') }}" class="btn btn-light-secondary fs-7" title="{{ __('إعادة ضبط') }}">
                            <i class="ki-outline ki-arrows-circle fs-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Requests Listing -->
    <div class="d-flex flex-column gap-5">
        @if($adjustments->isEmpty())
            <div class="card card-flush shadow-sm">
                <div class="card-body text-center py-15">
                    <div class="symbol symbol-70px symbol-circle bg-light-success mb-4">
                        <i class="ki-outline ki-check-circle fs-3x text-success p-4"></i>
                    </div>
                    <h3 class="fs-3 fw-bold text-gray-800">{{ __('لا توجد طلبات تعديل أوزان معلقة!') }}</h3>
                    <p class="text-muted fs-6 mb-0">{{ __('تمت مراجعة والبت في كافة أحمال تمارين المتدربين بنجاح.') }}</p>
                </div>
            </div>
        @else
            @foreach($adjustments as $adj)
                @php
                    $isHighlighted = ($highlightedRequestId && (int)$highlightedRequestId === (int)$adj->id);
                    $hasPendingItems = $adj->items->contains(fn($i) => $i->isPending() || $i->hasConflict());
                @endphp
                <div class="card card-flush shadow-sm request-card-wrapper {{ $isHighlighted ? 'border border-primary border-2 shadow' : '' }}" 
                     id="request-card-{{ $adj->id }}" 
                     style="transition: all 0.3s ease;">
                    
                    <!-- Request Card Header -->
                    <div class="card-header border-bottom py-4 bg-light bg-opacity-50">
                        <div class="card-title d-flex align-items-center gap-3">
                            <span class="symbol symbol-40px symbol-circle bg-primary text-white fs-5 fw-bold d-flex align-items-center justify-content-center">
                                {{ mb_substr($adj->trainee ? $adj->trainee->name : 'T', 0, 1) }}
                            </span>
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ $adj->trainee_id ? route('system.trainee.show', ['trainee' => $adj->trainee_id]) : '#' }}" 
                                       class="fs-6 fw-bold text-gray-900 text-hover-primary" 
                                       title="{{ __('فتح ملف المتدرب') }}">
                                        {{ $adj->trainee ? $adj->trainee->name : __('Trainee') }}
                                    </a>
                                    <span class="badge badge-light fs-8">{{ $adj->workout_day_name ?? '—' }}</span>
                                </div>
                                <span class="text-muted fs-8">
                                    <i class="ki-outline ki-time fs-8 me-1"></i>
                                    {{ $adj->created_at ? $adj->created_at->format('Y-m-d H:i') : '—' }}
                                    ({{ $adj->created_at ? $adj->created_at->diffForHumans() : '' }})
                                </span>
                            </div>
                        </div>

                        <div class="card-toolbar d-flex align-items-center gap-2">
                            <!-- Overall Status Badge -->
                            @if($adj->status === 'reviewed')
                                <span class="badge badge-light-success fs-8 px-3 py-2 fw-bold" id="parent-badge-{{ $adj->id }}">
                                    <i class="ki-outline ki-check-circle me-1"></i>{{ __('تمت المراجعة') }}
                                </span>
                            @elseif($adj->status === 'conflict')
                                <span class="badge badge-light-danger fs-8 px-3 py-2 fw-bold" id="parent-badge-{{ $adj->id }}">
                                    <i class="ki-outline ki-information-2 me-1"></i>{{ __('تعارض بالأوزان') }}
                                </span>
                            @elseif($adj->status === 'partially_reviewed')
                                <span class="badge badge-light-info fs-8 px-3 py-2 fw-bold" id="parent-badge-{{ $adj->id }}">
                                    <i class="ki-outline ki-time me-1"></i>{{ __('مراجعة جزئية') }}
                                </span>
                            @elseif($adj->status === 'dismissed')
                                <span class="badge badge-light-secondary fs-8 px-3 py-2 fw-bold" id="parent-badge-{{ $adj->id }}">
                                    <i class="ki-outline ki-cross-circle me-1"></i>{{ __('تم الإلغاء') }}
                                </span>
                            @else
                                <span class="badge badge-light-warning fs-8 px-3 py-2 fw-bold" id="parent-badge-{{ $adj->id }}">
                                    <i class="ki-outline ki-time me-1"></i>{{ __('قيد المراجعة') }}
                                </span>
                            @endif

                            <a href="{{ $adj->trainee_id ? route('system.trainee.show', ['trainee' => $adj->trainee_id, 'tab' => 'load_updates']) . '#kt_trainee_load_updates_tab' : '#' }}" 
                               class="btn btn-icon btn-sm btn-light" 
                               title="{{ __('عرض التبويب في ملف المتدرب') }}">
                                <i class="ki-outline ki-exit-right-corner fs-5"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Request Body -->
                    <div class="card-body p-6">
                        <!-- Exercise Info & Trainee Note -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-5 p-4 rounded bg-light">
                            <div class="d-flex align-items-center gap-3">
                                <span class="symbol symbol-35px symbol-circle bg-light-primary text-primary">
                                    <i class="ki-outline ki-weight fs-4 p-2"></i>
                                </span>
                                <div>
                                    <div class="fs-6 fw-bold text-gray-900">{{ $adj->exercise_name }}</div>
                                    <span class="text-muted fs-8">
                                        {{ __('الوزن المسجل بالخطة:') }} <strong>{{ $adj->prescribed_load ?: '—' }}</strong>
                                    </span>
                                </div>
                            </div>

                            @if($adj->notes)
                                <div class="bg-white p-3 rounded border border-dashed flex-grow-1 ms-md-4" style="max-width: 500px;">
                                    <div class="fs-8 text-gray-800 fst-italic">
                                        <i class="ki-outline ki-quote fs-6 me-1 text-primary"></i>
                                        "{{ $adj->notes }}"
                                    </div>
                                    <div class="fs-9 text-muted mt-1">
                                        {{ __('ملاحظة المتدرب') }} • {{ $adj->created_at ? $adj->created_at->format('H:i') : '' }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Requested Sets Section -->
                        <div class="d-flex flex-column gap-3">
                            <div class="fs-7 fw-bold text-gray-800 d-flex align-items-center gap-2 mb-1">
                                <i class="ki-outline ki-element-plus text-primary fs-5"></i>
                                <span>{{ __('المجموعات المطلوب تعديلها واتخاذ القرار بشأنها:') }}</span>
                            </div>

                            @if($adj->items->isEmpty())
                                <div class="alert alert-light-secondary py-3 px-4 fs-8">
                                    {{ __('تم تقديم هذا الطلب كتقرير إجمالي دون مجموعات منفصلة.') }} 
                                    ({{ __('الوزن المبلغ عنه:') }} <strong>{{ $adj->reported_load ?: '—' }}</strong>)
                                </div>
                            @else
                                <div class="row g-3">
                                    @foreach($adj->items as $item)
                                        @php
                                            $currentPersisted = $adj->exercise ? $adj->exercise->getWeightForSet($item->set_number) : null;
                                            $hasConflictNow = ($item->status === 'conflict') || ($item->isPending() && $currentPersisted !== $item->base_weight);
                                        @endphp
                                        <div class="col-12" id="item-row-{{ $item->id }}">
                                            <div class="p-4 rounded border {{ $hasConflictNow ? 'border-danger bg-light-danger bg-opacity-25' : ($item->isApproved() ? 'border-success bg-light-success bg-opacity-10' : ($item->isRejected() ? 'border-secondary bg-light' : 'border-gray-300 bg-white')) }}" 
                                                 style="transition: all 0.2s ease;">
                                                
                                                <!-- Row Top: Set meta and status -->
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <span class="badge badge-primary fs-7 fw-bold px-3 py-1">
                                                            {{ __('المجموعة') }} {{ $item->set_number }}
                                                        </span>
                                                        <div class="fs-8">
                                                            <span class="text-muted">{{ __('الأساسي وقت الطلب:') }}</span> <strong>{{ $item->base_weight ?: '—' }}</strong>
                                                            <span class="mx-2 text-gray-300">•</span>
                                                            <span class="text-muted">{{ __('الحالي بالخطة:') }}</span> <strong>{{ $currentPersisted ?: '—' }}</strong>
                                                            <span class="mx-2 text-gray-300">•</span>
                                                            <span class="text-muted">{{ __('المطلوب:') }}</span> <strong class="text-primary fs-7">{{ $item->suggested_weight }}</strong>
                                                            @if($item->approved_weight)
                                                                <span class="mx-2 text-gray-300">•</span>
                                                                <span class="text-muted">{{ __('المعتمد:') }}</span> <strong class="text-success fs-7">{{ $item->approved_weight }}</strong>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="d-flex align-items-center gap-2" id="item-badge-container-{{ $item->id }}">
                                                        @if($item->status === 'approved')
                                                            <span class="badge badge-light-success fs-8 px-2 py-1">
                                                                <i class="ki-outline ki-check-circle fs-8 me-1"></i>{{ __('معتمد ومطبق') }}
                                                            </span>
                                                        @elseif($item->status === 'rejected')
                                                            <span class="badge badge-light-danger fs-8 px-2 py-1">
                                                                <i class="ki-outline ki-cross-circle fs-8 me-1"></i>{{ __('مرفوض') }}
                                                            </span>
                                                        @elseif($hasConflictNow)
                                                            <span class="badge badge-light-danger fs-8 px-2 py-1">
                                                                <i class="ki-outline ki-information-2 fs-8 me-1"></i>{{ __('تعارض في الوزن') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-light-warning fs-8 px-2 py-1">
                                                                <i class="ki-outline ki-time fs-8 me-1"></i>{{ __('قيد المراجعة') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Conflict Banner -->
                                                @if($hasConflictNow)
                                                    <div class="alert alert-danger py-2 px-3 my-2 fs-8 d-flex align-items-center gap-2">
                                                        <i class="ki-outline ki-information-2 fs-4 text-danger"></i>
                                                        <div>
                                                            <strong>{{ __('تنبيه تعارض:') }}</strong>
                                                            {{ __('تغير وزن الخطة التدريبية (حالياً :current) عن الوزن الأساسي (:base) وقت تقديم الطلب. لحماية الخطة تم إيقاف التطبيق التلقائي حتى تقرر أنت.', ['current' => ($currentPersisted ?: '—'), 'base' => ($item->base_weight ?: '—')]) }}
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Actions Bar (If pending or conflict) -->
                                                @if($item->isPending() || $hasConflictNow)
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-3 pt-2 border-top border-gray-200" 
                                                         id="item-controls-{{ $item->id }}">
                                                        
                                                        <!-- Coach Note Input -->
                                                        <div class="flex-grow-1" style="min-width: 200px; max-width: 380px;">
                                                            <input type="text" class="form-control form-control-sm form-control-solid item-coach-notes" 
                                                                   placeholder="{{ __('ملاحظة المدرب لهذه المجموعة (اختياري)...') }}" 
                                                                   value="{{ $item->coach_notes }}">
                                                        </div>

                                                        <!-- Action Buttons -->
                                                        <div class="d-flex align-items-center flex-wrap gap-2 ms-auto">
                                                            @if($hasConflictNow)
                                                                <!-- Force Conflict Resolution -->
                                                                <button type="button" class="btn btn-sm btn-danger px-3 py-1 fs-8 btn-set-action" 
                                                                        data-item-id="{{ $item->id }}" 
                                                                        data-action="resolve_approve" 
                                                                        data-force="1">
                                                                    <i class="ki-outline ki-check-circle fs-7 me-1"></i>
                                                                    {{ __('فرض الاعتماد (:weight)', ['weight' => $item->suggested_weight]) }}
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-light-danger px-3 py-1 fs-8 btn-set-action" 
                                                                        data-item-id="{{ $item->id }}" 
                                                                        data-action="resolve_reject" 
                                                                        data-force="1">
                                                                    <i class="ki-outline ki-cross-circle fs-7 me-1"></i>
                                                                    {{ __('رفض المتعارض') }}
                                                                </button>
                                                            @else
                                                                <!-- Standard Approval -->
                                                                <button type="button" class="btn btn-sm btn-success px-3 py-1 fs-8 btn-set-action" 
                                                                        data-item-id="{{ $item->id }}" 
                                                                        data-action="approve" 
                                                                        title="{{ __('اعتماد الوزن المقترح') }}">
                                                                    <i class="ki-outline ki-check fs-7 me-1"></i>
                                                                    {{ __('اعتماد (:weight)', ['weight' => $item->suggested_weight]) }}
                                                                </button>

                                                                <!-- Alternative Weight Box & Button -->
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <input type="text" class="form-control form-control-sm form-control-solid text-center item-alt-weight" 
                                                                           placeholder="{{ __('وزن بديل') }}" style="width: 80px; font-size: 0.8rem; padding: 3px 6px;">
                                                                    <button type="button" class="btn btn-sm btn-light-primary px-2 py-1 fs-8 btn-set-action" 
                                                                            data-item-id="{{ $item->id }}" 
                                                                            data-action="approve_alternative" 
                                                                            title="{{ __('اعتماد بوزن بديل يحدده المدرب') }}">
                                                                        {{ __('اعتماد بوزن بديل') }}
                                                                    </button>
                                                                </div>

                                                                <!-- Rejection -->
                                                                <button type="button" class="btn btn-sm btn-light-danger px-3 py-1 fs-8 btn-set-action" 
                                                                        data-item-id="{{ $item->id }}" 
                                                                        data-action="reject" 
                                                                        title="{{ __('رفض هذا الطلب وتثبيت الوزن الحالي') }}">
                                                                    <i class="ki-outline ki-cross fs-7 me-1"></i>
                                                                    {{ __('رفض') }}
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <!-- Historical Record for finalized item -->
                                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2 pt-2 border-top border-gray-200 fs-9 text-muted">
                                                        <div>
                                                            <i class="ki-outline ki-user-check text-success fs-8 me-1"></i>
                                                            <span>{{ __('المراجع:') }} <strong>{{ $item->reviewer ? $item->reviewer->name : __('المدرب') }}</strong></span>
                                                            <span class="mx-2">•</span>
                                                            <span>{{ $item->reviewed_at ? $item->reviewed_at->format('Y-m-d H:i') : '' }}</span>
                                                        </div>
                                                        @if($item->coach_notes)
                                                            <div class="text-gray-800">
                                                                <i class="ki-outline ki-message-text-2 text-primary fs-8 me-1"></i>
                                                                {{ $item->coach_notes }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $adjustments->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Interactive Review Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = "{{ csrf_token() }}";
    const highlightedId = "{{ $highlightedRequestId ?? '' }}";

    // Auto-scroll to highlighted request card if present
    if (highlightedId) {
        const el = document.getElementById('request-card-' + highlightedId);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el.classList.add('border-primary');
        }
    }

    // Handle Set Decision Actions (Approve, Alternative, Reject, Conflicts)
    document.querySelectorAll('.btn-set-action').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const itemId = this.getAttribute('data-item-id');
            const action = this.getAttribute('data-action');
            const force = this.getAttribute('data-force') || 0;
            const row = document.getElementById('item-row-' + itemId);
            if (!row) return;

            const notesInput = row.querySelector('.item-coach-notes');
            const notes = notesInput ? notesInput.value.trim() : '';

            const altInput = row.querySelector('.item-alt-weight');
            const altWeight = altInput ? altInput.value.trim() : '';

            if (action === 'approve_alternative' && !altWeight) {
                alert("{{ __('يرجى كتابة الوزن البديل المراد اعتماده في المربع المخصص.') }}");
                if (altInput) altInput.focus();
                return;
            }

            const self = this;
            self.disabled = true;
            self.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            const url = "{{ url('/system/load-adjustment/item') }}/" + itemId + "/review";

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    action: action,
                    approved_weight: altWeight,
                    coach_notes: notes,
                    force: force
                })
            })
            .then(res => res.json())
            .then(data => {
                self.disabled = false;
                if (data.status) {
                    // Update KPI counters dynamically
                    const pendingEl = document.getElementById('stat-pending-sets');
                    if (pendingEl && parseInt(pendingEl.innerText) > 0) {
                        pendingEl.innerText = parseInt(pendingEl.innerText) - 1;
                    }

                    // Reload page or smoothly update card
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    alert(data.message || "{{ __('حدث خطأ أثناء حفظ القرار.') }}");
                    self.innerHTML = "{{ __('إعادة المحاولة') }}";
                }
            })
            .catch(err => {
                self.disabled = false;
                alert("{{ __('تعذر الاتصال بالخادم. يرجى إعادة المحاولة.') }}");
            });
        });
    });
});
</script>
@endsection
