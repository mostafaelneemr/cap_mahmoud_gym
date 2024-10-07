@php
    $row_id = 'tr_' . $row_id;
@endphp
{!! Form::button('<i class="fa fa-trash "></i>', [
    'class' => 'btn btn-icon btn-danger   btn-sm datatable-menu-delete',
    'title' => __('Delete'),
    'id' => md5($link),
    'onclick' => 'deleteRecord(this.id,' . "'$link '" . ',' . "'$row_id'" . ')',
]) !!}
