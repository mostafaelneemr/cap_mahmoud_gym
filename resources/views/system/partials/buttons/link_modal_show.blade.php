{!! Form::button('<i class="fa fa-eye"></i>', [
    'title' => __('show'),
    'class' => "btn btn-icon btn-primary   btn-sm  $class",
    'id' => md5('show_' . $rowId),
    'onclick' =>
        'showModal(this.id,' .
        "'$link'" .
        ',' .
        "'$modalId'" .
        ',' .
        "'$rowId'" .
        ',' .
        "'$prevStatus'" .
        ',' .
        "'$type'" .
        ',' .
        "'$updateUrl'" .
        ')',
]) !!}
