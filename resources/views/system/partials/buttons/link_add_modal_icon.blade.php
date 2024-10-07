@php
    $class = 'btn-icon';
    if($title){
        $class = '';
    }
@endphp
{!! Form::button('<i class="fa fa-circle-plus"></i> '.$title, [
    'class' => 'btn  btn-success '.$class,
    'data-bs-toggle' => 'modal',
    'data-bs-target' => '#' . $modalId,
    'href' => 'javascript:;',
    'value' => __('Add'),
    'title' => __('Add'),
]) !!}
