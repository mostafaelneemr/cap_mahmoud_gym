<p class="text-xs-left"><small class="text-danger">
        @foreach ($error->get($fieldName) as $errorMsg)
            @if (is_array($errorMsg))
                {{ implode(',', $errorMsg) }} <br />;
            @else
                {{ $errorMsg }} <br />
            @endforeach
    </small>
</p>
