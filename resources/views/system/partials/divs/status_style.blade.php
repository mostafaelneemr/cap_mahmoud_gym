@if ($status == 0)
    <div class="badge badge-warning">{{ __('Pending') }}</div>
@elseif ($status == 1)
    <div class="badge badge-success">{{ __('Approved') }}</div>
@elseif ($status == 2)
    <div class="badge badge-danger">{{ __('Disapproved') }}</div>
@else
    --
@endif
