<div class="rating">
    @for ($i = 1; $i <= $rating; $i++)
{{--        <div class="rating-label @if ($i <= $rating) checked @endif">--}}
            <i class="fa fa-star"></i>
{{--        </div>--}}
    @endfor

</div>
