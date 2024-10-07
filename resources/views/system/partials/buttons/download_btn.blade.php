{!! Form::button('<i class="fa-solid fa-file-excel"></i>', [
    'id' => 'ExportReporttoExcel',
    'class' => '  btn btn-icon btn-info  ',
    'onclick' =>  'filterFunction("'.$datatableURL.'","'.$datatableVar.'",$("#filterForm"),true);return false;',
    'href' => 'javascript:;',
    'title' => isset($title) ? $title : __('Download Template'),
]) !!}
