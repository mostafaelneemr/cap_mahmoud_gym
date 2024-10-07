{!! Form::button('<i class="' . $icon . '"></i><span class="menu-title">' . __($title) . '</span>', [
    'class' => "btn btn-flex btn-$color float-end",
    'data-bs-toggle' => 'modal',
    'data-bs-target' => '#' . $modalId,
    'href' => 'javascript:;',
    'value' => __($title),
    'title' => __($title),
]) !!}
