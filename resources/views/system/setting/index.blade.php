@extends('system.layout')
@section('content')

    <!--begin::Row-->
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            @if($errors->any())
                <div class="alert alert-danger fade show" role="alert">
                    <div class="alert-icon"><i class="flaticon-warning"></i></div>
                    <div class="alert-text">{{__('Some fields are invalid please fix them')}}</div>
                    <div class="alert-close">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="la la-close"></i></span>
                        </button>
                    </div>
                </div>
            @elseif(Session::has('status'))
                <div class="alert alert-{{Session::get('status')}} fade show" role="alert">
                    <div class="alert-icon"><i class="flaticon-warning"></i></div>
                    <div class="alert-text">{{ Session::get('msg') }}</div>
                    <div class="alert-close">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="la la-close"></i></span>
                        </button>
                    </div>
                </div>
            @endif
            <div class="example-preview">
                <ul class="nav nav-tabs nav-tabs-line">
                    @foreach($settingGroups as $key => $value)
                        <li class="nav-item">
                            <a class="nav-link @if($key == 0) active @endif" data-toggle="tab" href="#kt_tab_pane_1_{{$key}}_tab_content" role="tab">
                                {{__(str_replace('_',' ',$value->group_name))}}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

                <div class="card-body">
                    {!! Form::open(['route' => 'system.setting.update','method' => 'PATCH' ,'files' => true]) !!}
                    <div class="tab-content mt-5" id="myTabContent">

                        @foreach($settingGroups as $groupIndex => $group)
                        <div class="tab-pane fade @if($groupIndex == 0) active show @endif" id="kt_tab_pane_1_{{ $group->group_name }}_tab_content" role="tabpanel">
                            @if(isset($setting[$group->group_name]))
                                @foreach($setting[$group->group_name] as $sKey => $sValue)
                                    @if($sValue->input_type == 'text')
                                        <div class="form-group row mb-2">
                                            {!! Form::label($sValue->name, $sValue->{'shown_name_'.\App::getLocale()}, ['class'=>'col-md-3 col-form-label']) !!}
                                            <div class="col-md-9">
                                                {!! Form::text($sValue->name, $sValue->value, ['class'=>'form-control']) !!}
                                            </div>
                                        </div>
                                    @elseif($sValue->input_type == 'number')
                                        <div class="form-group row mb-2">
                                            {!! Form::label($sValue->name, $sValue->{'shown_name_'.\App::getLocale()}, ['class'=>'col-md-3 col-form-label']) !!}
                                            <div class="col-md-9">
                                                {!! Form::number($sValue->name, $sValue->value, ['class'=>'form-control']) !!}
                                            </div>
                                        </div>
                                    @elseif($sValue->input_type == 'textarea')
                                        <div class="form-group row mb-2">
                                            {!! Form::label($sValue->name, $sValue->{'shown_name_'.\App::getLocale()}, ['class'=>'col-md-3 col-form-label']) !!}
                                            <div class="col-md-9">
                                                {!! Form::textarea($sValue->name, $sValue->value, ['class'=>'form-control','rows'=>3]) !!}
                                            </div>
                                        </div>
                                    @elseif($sValue->input_type == 'image')
                                        <div class="form-group row mb-2">
                                            {!! Form::label($sValue->name, $sValue->{'shown_name_'.\App::getLocale()}, ['class'=>'col-md-3 col-form-label']) !!}
                                            <div @if($sValue->value) class="col-md-5" @else class="col-md-9" @endif>
                                                {!! Form::file($sValue->name, ['class'=>'form-control']) !!}
                                            </div>
                                            @if($sValue->value)
                                                <div class="col-md-2">
                                                    <a target="_blank" href="{{asset($sValue->value)}}">{{__('View')}}</a>
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($sValue->input_type == 'select')
                                        <div class="form-group row mb-2">
                                            {!! Form::label($sValue->name, $sValue->{'shown_name_'.\App::getLocale()}, ['class'=>'col-md-3 col-form-label']) !!}
                                            <div class="col-md-9">
                                                @php
                                                    $listValues = $sValue->option_list;
                                                    $listSelect = [];
                                                    foreach($listValues as $lKey => $lValue){
                                                        $listSelect[$lKey] = __($lValue);
                                                    }
                                                @endphp
                                                {!! Form::select($sValue->name, $listSelect, $sValue->value, ['class'=>'form-control']) !!}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    @endforeach

                </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-9"></div>
                            <div class="col-md-3">

                                    <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>

                            </div>
                        </div>
                    </div>



                </div>

                {!! Form::close() !!}
        </div> </div></div>

        <!-- end:: Content -->
        @endsection
