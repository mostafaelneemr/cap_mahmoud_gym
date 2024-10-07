@if ($type == 'shipping')
    <div class="badge badge-primary">{{ __($type) }}</div>
@elseif ($type == 'return')
    <div class="badge badge-warning">{{ __($type) }}</div>
@else
    --
@endif
