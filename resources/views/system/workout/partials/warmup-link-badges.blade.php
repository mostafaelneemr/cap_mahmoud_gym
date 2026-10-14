{{--
    Display warmup links as clickable badge pills.
    Params:
      - $links: array of URL strings
      - $modal: bool, if true opens #videoModal / open-video-btn, else opens link in new window
      - $title: day name / title
--}}
@php
    $badgeLinks = \App\Models\WorkoutPlan::normalizeWarmupLinks($links ?? []) ?? [];
@endphp
@if(!empty($badgeLinks))
    <div class="warmup-links-badges">
        @foreach($badgeLinks as $linkIdx => $link)
            @if(!empty($modal))
                <button type="button" class="warmup-link-pill open-video-btn"
                        data-video-url="{{ $link }}"
                        data-exercise-name="{{ trim(($title ?? '') . ' — ' . __('Warmup Video') . ' ' . ($linkIdx + 1), ' —') }}">
                    <i class="fas fa-play text-warning"></i>
                    <span>@lang('Warmup Video') {{ $linkIdx + 1 }}</span>
                </button>
            @else
                <a href="{{ $link }}" target="_blank" rel="noopener noreferrer" class="warmup-link-pill"
                   title="{{ $link }}">
                    <i class="fas fa-play text-warning"></i>
                    <span>@lang('Warmup Video') {{ $linkIdx + 1 }}</span>
                    <i class="fas fa-external-link-alt fs-9 ms-1 opacity-75"></i>
                </a>
            @endif
        @endforeach
    </div>
@endif
