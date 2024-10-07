@if ($type == 'auto')
    <div class="badge badge-dark">{{ __($type) }}</div>
@elseif($type == 'manual')
    <div class="badge badge-secondary">{{ __($type) }}</div>
@elseif($type == 'niceone')
    <div class="badge badge-info">{{ __($type) }}</div>
@else
    --
@endif
