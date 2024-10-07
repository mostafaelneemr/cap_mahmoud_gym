@extends('system.layout')


@section('content')


    <div class="row clearfix">

        <div class="col-12">
            <div class="card">


                {!! Form::open(['id'=>'main-form','onsubmit' =>   isset($permission_group->id) ? 'FormSubmit("'.route('system.permission-group.update',$permission_group->id).'");return false;':'FormSubmit("'.route('system.permission-group.store') .'");return false;','method' => isset($permission_group->id) ?  'PATCH' : 'POST']) !!}
                <div id="form-alert-message"></div>
                <div class="card-body">

                    <div class="row" style="align-items: end;">
                        <div class="form-group col-md-5{!! formError($errors,'name',true) !!}">
                            <div class="controls">
                                {{ label(__('Name'),'required') }}
                                {!! Form::text('name',isset($permission_group->id) ? $permission_group->name:old('name'),['class'=>'form-control','id'=>'name']) !!}
                                <div class="invalid-feedback" id="name-form-error"></div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="form-group">
{{--                                {!! Form::label('new_admin_default_route', __('Default Route') , ['class' =>--}}
{{--                                'font-weight-bolder']) !!}--}}

                                {{ label(__('Default Route')) }}
                                <select name="new_admin_default_route" id="new_admin_default_route"
                                        class="form-control general_select2"
                                        data-allow-clear="true"
                                        data-placeholder="{{__('Select Default Route')}}">
                                    <option value=""></option>
                                    @foreach($routes as $key=>$route)
                                        <option value="{{$route}}"
                                        @if (isset($permission_group->id) && $permission_group->new_admin_default_route==$route ) {{'selected'}}@endif
                                        >{{$key}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2  ">
                            <button type="submit" class="btn btn-primary submit">
                                <span class="indicator-label">{{__('Save')}}</span>
                                <span class="indicator-progress">{{__('Please wait...')}}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>

                    </div>
                    <div class="mb-4" style="margin-top: 10px;">
                        <a href="javascript:void(0);" class="btn btn-primary mr-2 mt-md-0 mt-4 w-100 w-md-auto"
                           onclick="CheckAll(true);">
                            <i class="far fa-check-square"></i> {{__('Select All')}}
                        </a>
                        <a href="javascript:void(0);" class="btn mt-md-0 mt-4 btn-warning mr-2 w-100 w-md-auto"
                           onclick="CheckAll(false);">
                            <i class="far fa-square"></i> {{__('Deselect All')}}
                        </a>
                    </div>

                    @foreach($permissions as $permission)

                        @if(isset($permission['group_title']))
                            <h1 class="anchor fw-bold mb-5" id="interactive-button-value" data-kt-scroll-offset="50">
                                {{$permission['group_title']}}</h1>

                        @endif

                        <div class="card mb-4">
                            <div class="card-body  pt-0">
                                <div class="bs-popover-left call-out-border-left permissions p-2 ">
                                    <div class="row my-5">
                                        <div class="col-md-6 col-12">
                                            <h4 class="primary pull-left  text-success">
                                                {{trans(ucfirst($permission['name']))}}
                                            </h4>
                                        </div>
                                        <div class="col-12 text-md-right form-check form-check-custom form-check-success">
                                            <label >
                                                <input type="checkbox" onclick="CheckPerms(this);" class="form-check-input">
                                                <span ></span>
                                                <p class="d-inline-block ml-3 mb-0">@lang('Select All')</p>
                                            </label>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row">
                                        @foreach($permission['permissions'] as $key=>$val)
                                            <div class="col-md-4 form-check form-check-custom form-check-success">
                                                <label >
                                                    {!! Form::checkbox("permissions[]", "$key", isset($permission_group->id) ?
                                                    !array_diff($val,$currentpermissions) : false,['class' => 'form-check-input','style' => 'margin-bottom:15px;']) !!}
                                                    <span ></span>
                                                    {{trans(ucfirst(str_replace('-',' ',$key)))}}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach


                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-3 col-12 offset-md-9">
                            <button type="submit" class="btn btn-primary submit">
                                <span class="indicator-label">{{__('Save')}}</span>
                                <span class="indicator-progress">{{__('Please wait...')}}
						<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
        <!-- #END# Basic Examples -->
    </div>
    <script>
        function CheckPerms(perm) {
            var permessions = $(perm).parents('.permissions').find('input[type=\'checkbox\']');
            //console.log(permessions);
            if ($(perm).is(':checked')) {
                $(permessions).prop('checked', true);
            } else {
                $(permessions).prop('checked', false);
            }
        }
        function CheckAll(val) {
            if (val == true) {
                $("input[type='checkbox']").prop("checked", true);
            } else {
                $("input[type='checkbox']").prop("checked", false);
            }
        }

    </script>
@endsection
