@if (in_array($status, ['PAID', 'paid', 'Paid']))
    <span class="badge badge-light-primary">{{__($status) }}</span>
@elseif (in_array($status, ['un-paid', 'Un-paid','unpaid','Unpaid']))
    <span class="badge badge-light-warning">{{ __($status) }}</span>
@else
    --
@endif
