@if ($default_cuurency && $default_cuurency->value == $code)
    {{ $title }} <span class="badge badge-primary">({{ __('Default') }})</span>
@else
    {{ $title }}
@endif
