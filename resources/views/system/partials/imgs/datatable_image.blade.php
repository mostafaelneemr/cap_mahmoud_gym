@if (empty($path))
    <img src="{{ asset('/assets/media/misc/no_image.svg') }}" class="datatable-image" />
@else
    <a target="_blank" href="{{  $path }}">
        <img src="{{ $path }}" class="datatable-image"> </a>
@endif
