{{--
    Warmup links Blade partial (max 5).
    Params:
      - $name: input array base name, default 'warmup_links'
      - $links: array of existing link URLs (optional)
--}}
@php
    $maxWarmup = \App\Models\WorkoutPlan::MAX_WARMUP_LINKS;
    $rawLinks = is_array($links ?? null) ? $links : [];
    $cleanLinks = array_values(array_filter($rawLinks, fn($l) => !empty($l) && trim((string)$l) !== ''));
    if (empty($cleanLinks)) {
        $cleanLinks = [''];
    }
    if (count($cleanLinks) > $maxWarmup) {
        $cleanLinks = array_slice($cleanLinks, 0, $maxWarmup);
    }
    $baseName = $name ?? 'warmup_links';
    $isFull = count($cleanLinks) >= $maxWarmup;
@endphp
<div class="wl-section" data-base-name="{{ $baseName }}">
    <div class="wl-header">
        <span class="wl-title"><i class="fas fa-play-circle text-warning"></i> @lang('Warmup Video Links')</span>
        <span class="wl-badge-counter"><span class="wl-count">{{ count(array_filter($cleanLinks, fn($l) => !empty(trim((string)$l)))) ?: count($cleanLinks) }}</span> / {{ $maxWarmup }}</span>
    </div>
    <div class="wl-list">
        @foreach($cleanLinks as $idx => $link)
            @php
                $hasLink = !empty(trim((string)$link));
            @endphp
            <div class="wl-item" data-index="{{ $idx }}">
                <div class="wl-input-wrap">
                    <i class="fas fa-link"></i>
                    <input type="url" name="{{ $baseName }}[]" value="{{ $link }}"
                           class="form-control form-control-solid wl-input" maxlength="500"
                           placeholder="https://Video Link.com/... (@lang('Warmup Link') {{ $idx + 1 }})"
                           aria-label="@lang('Warmup Link') {{ $idx + 1 }}">
                </div>
                <button type="button" class="wl-preview-btn {{ $hasLink ? '' : 'is-disabled' }}"
                        title="@lang('Preview / Open Video')"
                        aria-label="@lang('Preview / Open Video')"
                        @if(!$hasLink) aria-disabled="true" @endif>
                    <i class="fas fa-external-link-alt"></i>
                </button>
                <button type="button" class="wl-remove-btn" title="@lang('Remove')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endforeach
    </div>
    <button type="button" class="wl-add-btn" @if($isFull) disabled style="display:none !important;" @endif>
        <i class="fas fa-plus"></i> @lang('Add Warmup Link')
    </button>
</div>
