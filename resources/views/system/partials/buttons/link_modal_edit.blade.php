{!! Form::button('<i class="fa fa-pen-to-square"></i>', [
    'title' => __('Edit'),
    'class' => "btn btn-icon btn-info  $class",
    'id' => md5('edit_' . $rowId),
    'onclick' =>
        'editModal(this.id,' .
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
