{!! Form::button('read', [
    'class' => "btn btn-icon  btn-sm $btnClass",
    'title' => __('Update'),
    'id' => md5($rowId . '_' . $icon),
    'onclick' => 'updateRecord(this.id,' . "'$rowId '". ',' . "'$status'" . ',' . "'$link'" . ')',
]) !!}
