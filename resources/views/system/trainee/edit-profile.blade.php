@extends('system.layout')

@section('content')


    {!! Form::open(['id'=>'main-form','onsubmit' =>  isset($result) ? 'FormSubmit("'.route('system.user.update-profile').'");return false;':'','method' => isset($result) ?  'PATCH' : 'POST']) !!}
    <div id="form-alert-message"></div>
    <!--begin::Row-->
    <div class="row gx-10 ">
        <!--begin::Col-->
        <div class="col-lg-6 ">
        {{ label(__('Name')) }} <span class="required"></span>
        <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::text('name',isset($result->id) ? $result->name:old('name'),['disabled'=>true,'class'=>'form-control form-control-solid']) !!}
                <div class="invalid-feedback" id="name-form-error"></div>
            </div>

            <!--end::Input group-->
        </div>
        <!--end::Col-->
        <!--begin::Col-->
        <div class="col-lg-6">
        {{ label( __('Last Name')) }}<span class="required"></span>
        <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::text('lastname',isset($result->id) ? $result->lastname:old('lastname'),['disabled'=>true,'class'=>'form-control form-control-solid']) !!}
                <div class="invalid-feedback" id="lastname-form-error"></div>
            </div>

            <!--end::Input group-->
        </div>

        <div class="col-lg-6 ">
        {{ label(__('Administration Language')) }}
        <!--begin::Input group-->
            <div class="mb-5">
                default_language
                {!! Form::select('default_language',language_data(),isset($result->id) ? $result->default_language:old('default_language'),['class'=>'form-select form-select-solid','id'=>'default_language',' data-placeholder'=>__('Select an option')]) !!}
                <div class="invalid-feedback" id="default_language-form-error"></div>
            </div>
        </div>
        <!--end::Col-->
        <!--begin::Col-->
        <div class="col-lg-6 ">
        {{ label(__('Password')) }}
        <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::password('password', ['class' => 'form-control form-control-solid','id'=>'password']) !!}
                <div class="invalid-feedback" id="password-form-error"></div>
            </div>

            <!--end::Input group-->
        </div>
        <!--end::Col-->
        <!--begin::Col-->
        <div class="col-lg-6">
        {{ label( __('Confirm Password')) }}
        <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::password('password_confirmation', ['class' => 'form-control form-control-solid','id'=>'password_confirmation']) !!}
                <div class="invalid-feedback" id="password_confirmation-form-error"></div>
            </div>

            <!--end::Input group-->
        </div>
        <!--end::Col-->

    </div>
    <!--end::Row-->
    <div class="separator separator-dashed mb-8"></div>


    {{--                    {{ save_button() }}--}}

    <button type="submit" class="btn btn-primary submit">
        <span class="indicator-label">{{ isset($result->id)? __('Update') :  __('Create')}}</span>
        <span class="indicator-progress">{{__('Please wait')}}...
						<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
    </button>




    {!! Form::close() !!}

@endsection

