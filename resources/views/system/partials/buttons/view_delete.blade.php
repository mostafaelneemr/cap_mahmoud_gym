@php
    $row_id = 'tr_' . $row_id;
@endphp
{!! Form::button(
    '<span class="menu-icon"><i class="fa fa-trash  text-danger"></i></span><span class="menu-title text-danger"></span>',
    [
        'class' => 'menu-link px-3 datatable-menu-delete',
        'title' => __('Delete'),
        'id' => md5($link),
        'onclick' => 'deleteRecord(this.id,' . "'$link'" . ',' . "'$row_id'" . ')',
    ],
) !!}
