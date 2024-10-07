{!! Form::button('<i class="fa fa-share-alt "></i>', [
    'title' => __('Share'),
    'class' => 'btn btn-icon btn-success   btn-sm  datatable-menu-delete',
    'onclick' => 'shareModal(' . "'$linkId'" . ',' . "'$type'" . ')',
]) !!}
