@extends('system.layout')
@section('content')

    <!--begin::Row-->
    <div class="row">
        <div class="col-12">
            <div class="card">

                <!-- Display validation errors or status messages -->
                @if($errors->any())
                    <div class="alert alert-danger fade show" role="alert">
                        <div class="alert-icon"><i class="flaticon-warning"></i></div>
                        <div class="alert-text">{{ __('Some fields are invalid. Please fix them.') }}</div>
                        <div class="alert-close">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="la la-close"></i></span>
                            </button>
                        </div>
                    </div>
                @elseif(Session::has('status'))
                    <div class="alert alert-{{ Session::get('status') }} fade show" role="alert">
                        <div class="alert-icon"><i class="flaticon-warning"></i></div>
                        <div class="alert-text">{{ Session::get('msg') }}</div>
                        <div class="alert-close">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="la la-close"></i></span>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Navigation tabs for setting groups -->
                <div class="example-preview">
                    <ul class="nav nav-tabs nav-tabs-line">

                        @foreach($settingGroups as $key => $value)
                            <li class="nav-item">
                                <a class="nav-link @if($key == 0) active @endif" data-bs-toggle="tab"
                                   href="#kt_tab_pane_1_{{$key}}_tab_content" role="tab">
                                    {{ str_replace('_', ' ', $value->group_name) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-body">
                    {!! Form::open(['route' => 'system.setting.update', 'method' => 'PATCH', 'files' => true]) !!}

                    <div class="tab-content mt-5" id="myTabContent">
                        @foreach($settingGroups as $key => $value)
                            <div class="tab-pane fade @if($key == 0) active show @endif"
                                 id="kt_tab_pane_1_{{$key}}_tab_content" role="tabpanel">
                                @foreach($setting[$key] as $sKey => $sValue)
                                    @if($sValue->input_type == 'text')
                                        <div class="form-group row">
                                            {!! Form::label($sValue->name, $sValue->{'shown_name_'.\App::getLocale()}, ['class' => 'col-3 col-form-label']) !!}
                                            <div class="col-9">
                                                {!! Form::text($sValue->name, $sValue->value, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    @elseif($sValue->input_type == 'number')
                                        <div class="form-group row">
                                            {!! Form::label($sValue->name, $sValue->{'shown_name_'.\App::getLocale()}, ['class' => 'col-3 col-form-label']) !!}
                                            <div class="col-9">
                                                {!! Form::number($sValue->name, $sValue->value, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    @elseif($sValue->input_type == 'textarea')
                                        <div class="form-group row">
                                            {!! Form::label($sValue->name, $sValue->{'shown_name_'.\App::getLocale()}, ['class' => 'col-3 col-form-label']) !!}
                                            <div class="col-9">
                                                {!! Form::textarea($sValue->name, $sValue->value, ['class' => 'form-control', 'rows' => 3]) !!}
                                            </div>
                                        </div>
                                    @elseif($sValue->input_type == 'image')
                                        <div class="form-group row">
                                            {!! Form::label($sValue->name, $sValue->{'shown_name_'.\App::getLocale()}, ['class' => 'col-3 col-form-label']) !!}
                                            <div @if($sValue->value) class="col-7" @else class="col-9" @endif>
                                                {!! Form::file($sValue->name, ['class' => 'form-control']) !!}
                                            </div>
                                            @if($sValue->name == 'logo')
                                                <span class="text-center">{{__('Image dimensions :')}} 218 × 28</span>
                                            @elseif($sValue->name == 'testimonial_image')
                                                <span class="text-center">{{__('Image dimensions :')}} 1920 × 500</span>
                                            @endif
                                            @if($sValue->value)
                                                <div class="col-2">
                                                    <a target="_blank" href="{{ asset($sValue->value) }}">{{ __('View') }}</a>
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($sValue->input_type == 'select')
                                        <div class="form-group row">
                                            {!! Form::label($sValue->name, $sValue->{'shown_name_'.\App::getLocale()}, ['class' => 'col-3 col-form-label']) !!}
                                            <div class="col-9">
                                                @php
                                                    $listValues = $sValue->option_list;
                                                    $listSelect = [];
                                                    foreach($listValues as $lKey => $lValue) {
                                                        $listSelect[$lKey] = __($lValue);
                                                    }
                                                @endphp
                                                {!! Form::select($sValue->name, $listSelect, $sValue->value, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-9"></div>
                            <div class="col-3">
                                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content -->
@endsection
