@extends('system.layout')
@section('content')
    <div class="card card-custom gutter-b">

        <div class="card-body">
                {!! Form::open(['route' => 'system.user.change-password-post', 'method' => 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    <div class="k-portlet__body">
                        <div id="form-alert-message"></div>


                            <div class="form-group row">

                                <div class="col-md-12">
                                    <label>{{__('Currant Password')}} *</label>
                                    {!! Form::password('currant_password',['class'=>'form-control','id'=>'currant_password-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="currant_password-form-error"></div>
                                </div>

                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>{{__('Password')}} *</label>
                                    {!! Form::password('password',['class'=>'form-control','id'=>'password-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="password-form-error"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Confirm password')}} *</label>
                                    {!! Form::password('password_confirmation',['class'=>'form-control','id'=>'password_confirmation-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="password_confirmation-form-error"></div>
                                </div>

                            </div>



                    <div class="k-portlet__foot">
                        <div class="k-form__actions">
                            <div class="row" style="float: right;">
                                    <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                            </div>
                        </div>
                    </div>



            </div>
                {!! Form::close() !!}
        </div>
    </div>

@endsection
@section('footer')
    <script type="text/javascript">
        function submitMainForm(){
            formSubmit(
                '{{route('system.user.change-password-post')}}',
                $('#main-form').serialize(),
                function ($data) {
                    if(!$data.status){
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                        pageAlert('#form-alert-message','error',$data.message);
                    }else{
                        window.location = $data.data.url;
                    }
                },
                function ($data){
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                    pageAlert('#form-alert-message','error',$data.message);
                }
            );
        }

    </script>
@endsection
