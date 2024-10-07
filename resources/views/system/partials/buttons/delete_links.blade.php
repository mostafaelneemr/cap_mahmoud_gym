{!! Form::button('<i class="fa fa-trash"></i>', [
    'class' => '  btn btn-icon btn-danger  ' . md5($link),
    'id' => md5($link),
    'onclick' => 'deleteRecord(this.id,' . "'$link'" . ',' . "'$row_id'" . ')',
    'href' => 'javascript:;',
    'title' => __('Delete'),
]) !!}
