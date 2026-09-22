@extends('system.layout')

@section('content')

    {!! Form::open(['id'=>'main-form','onsubmit' =>  isset($result) ? 'FormSubmit("'.route('system.user.update',$result->id).'");return false;':'FormSubmit("'.route('system.user.store') .'");return false;','method' => isset($result) ?  'PATCH' : 'POST']) !!}
    <div id="form-alert-message"></div>
    <!--begin::Row-->
    <div class="row gx-10 ">
        <!--begin::Col-->
        <div class="col-lg-6">
            {{ label( __('Email'),'required') }}
            <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::email('email',isset($result->id) ? $result->email:old('email'),['class'=>'form-control form-control-solid']) !!}
                <div class="invalid-feedback" id="email-form-error"></div>
            </div>

            <!--end::Input group-->
        </div>
        <!--end::Col-->
        <input type="hidden" name="telephone_code" id="telephone_code" value="{{ $user->telephone_code ?? '20' }}">

        <!--begin::Col-->
        <div class="col-lg-6 ">
            {{ label(__('Name'),'required') }}
            <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::text('name',isset($result->id) ? $result->name:old('name'),['class'=>'form-control form-control-solid']) !!}
                <div class="invalid-feedback" id="name-form-error"></div>
            </div>

            <!--end::Input group-->
        </div>
        <!--begin::Col-->
        <div class="col-lg-6 ">
            {{ label(__('Password'),isset($result) ? '':'required') }}
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
            {{ label( __('Confirm Password'),isset($result) ? '':'required') }}
            <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::password('password_confirmation', ['class' => 'form-control form-control-solid','id'=>'password_confirmation']) !!}
                <div class="invalid-feedback" id="password_confirmation-form-error"></div>
            </div>

            <!--end::Input group-->
        </div>

        <!--end::Col-->
        <div class="col-lg-6 ">
            {{ label(__('Mobile')) }}
            <div class="mb-5 telephone_country">
                {!! Form::tel('telephone', isset($result) ?$telephone:null,['class'=>'form-control form-control-solid valid_telephone add_telephone numeric-only','id'=>'telephone']) !!}
                <div class="invalid-feedback" id="telephone-form-error"></div>
            </div>
        </div>
        <!--begin::Col-->
        <!--begin::Col-->
        <div class="col-lg-6">
            {{ label( __('Permission Group'),'required') }}
            <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::select('permission_group_id',[__('Select Permission Group')]+$PermissionGroup,isset($result->id) ? $result->permission_group_id:old('user_group_id'),['class'=>'form-select  form-select-solid','id'=>'user_group_id',' data-placeholder'=>__('Select an option')]) !!}
                <div class="invalid-feedback" id="permission_group_id-form-error"></div>
            </div>
            <!--end::Input group-->
        </div>
        <!--end::Col-->

        <!--begin::Col-->
        <div class="col-lg-6 ">
            {{ label(__('Status'),'required') }}
            <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::select('status',status_select_data(),isset($result->id) ? $result->status:old('status'),['class'=>'form-select form-select-solid','id'=>'status',' data-placeholder'=>__('Select an option')]) !!}
                <div class="invalid-feedback" id="status-form-error"></div>
            </div>

            <!--end::Input group-->
        </div>
        <!--begin::Col-->
        <div class="col-lg-6 ">
            {{ label(__('Force Reset Password'),'required') }}
            <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::select('force_reset_password',force_reset_password_select_data(),isset($result->id) ? $result->force_reset_password:old('force_reset_password'),['class'=>'form-select form-select-solid','id'=>'force_reset_password',' data-placeholder'=>__('Select Force Reset Password')]) !!}
                <div class="invalid-feedback" id="force_reset_password-form-error"></div>
            </div>

            <!--end::Input group-->
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->
    <div class="separator separator-dashed mb-8"></div>

    <button type="submit" class="btn btn-primary submit">
        <span class="indicator-label">{{ isset($result->id)? __('Update') :  __('Create')}}</span>
        <span class="indicator-progress">{{__('Please wait')}}...
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
    </button>

    {!! Form::close() !!}

@endsection


