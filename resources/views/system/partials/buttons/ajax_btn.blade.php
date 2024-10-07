{!! Form::button(__($title), [
    'id' => isset($params['class']) ? $params['class'] : md5($action),
    'class' => '  btn btn-icon btn-info  ' . isset($params['class']) ? $params['class'] : md5($action),
    'onclick' => $action,
    'href' => 'javascript:;',
    'title' => __($title),
]) !!}
