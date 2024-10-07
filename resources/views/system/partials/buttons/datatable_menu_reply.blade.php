@php
    $replies = route('system.review.replies');
@endphp
{!! Form::button('<i class="fa fa-comments "></i>', [
    'class' => 'btn btn-icon btn-warning   btn-sm  btn-sm ',
    'title' => __('Reply'),
    'onclick' =>
        'showReply(' . "'$review_id'" . ',' . "'$parentId'" . ',' . "'$replies'" . ',' . "'$comments'" . ')',
]) !!}
