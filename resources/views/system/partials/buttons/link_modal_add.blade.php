{!! Form::button('<i class="fa-solid fa-plus"></i>', [
    'title' => __('Add'),
    'class' => "btn btn-icon btn-success  $class",
    'id' => md5('add_' . $rowId),
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
