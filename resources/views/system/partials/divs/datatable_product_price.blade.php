@php
    $style = '';
    $return = '';
@endphp
@if (!empty($special_price))
    @php
        $style = 'style="text-decoration: line-through;"';
    @endphp
    <div class="badge badge-light-success">{{ amount($special_price, 2) }}</div>
@endif
<div class="badge badge-light-primary" {{ $style }}>{{ amount($price, 2) }}</div>
