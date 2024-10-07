@if ($status == 'true')
    <div class="badge badge-success">{{ __('true') }}</div>
@elseif ($status == 'false')
    <div class="badge badge-danger">{{ __('false') }}</div>
@elseif ($status == 'pending')
    <div class="badge badge-primary">{{ __('pending') }}</div>
@else
    --
@endif
