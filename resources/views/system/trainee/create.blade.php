@extends('system.layout')
@section('header')


@endsection
@section('content')

    {!! Form::open([
        'id' => 'main-form',
        'url' => isset($result) ? route('system.trainee.update', $result->id) : route('system.trainee.store'),
        'method' => isset($result) ? 'PATCH' : 'POST',
        'onsubmit' => isset($result) ? 'FormSubmit("'.route('system.trainee.update',$result->id).'");return false;' : 'FormSubmit("'.route('system.trainee.store') .'");return false;'
    ]) !!}
    <div id="form-alert-message"></div>
    <!--begin::Row-->
    <div class="row gx-10 ">
        <!--begin::Col-->
        <div class="col-lg-6">
            {{ label( __('Email'),'required') }}
            <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::email('email',isset($result->id) ? $result->email:old('email'),['class'=>'form-control
                form-control-solid']) !!}
                <div class="invalid-feedback" id="email-form-error"></div>
            </div>
        </div>
        <!--end::Col-->

        <!--begin::Col-->
        <div class="col-lg-6 ">
            {{ label(__('Name'),'required') }}
            <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::text('name',isset($result->id) ? $result->name:old('name'),
                ['class'=>'form-control form-control-solid']) !!}
                <div class="invalid-feedback" id="name-form-error"></div>
            </div>
        </div>
        <!--begin::Col-->
        <div class="col-lg-6 ">
            {{ label(__('Password'),isset($result) ? '':'required') }}
            <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::password('password', ['class' => 'form-control form-control-solid','id'=>'password']) !!}
                <div class="invalid-feedback" id="password-form-error"></div>
            </div>

        </div>

        <div class="col-lg-6">
            {{ label( __('Confirm Password'),isset($result) ? '':'required') }}
            <div class="mb-5">
                {!! Form::password('password_confirmation',
                ['class' => 'form-control form-control-solid','id'=>'password_confirmation']) !!}
                <div class="invalid-feedback" id="password_confirmation-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('Membership Start'),'required') }}
            <div class="mb-5">

                {!! Form::text('membership_start',isset($result->id) ? $trainee->membership_start:old('membership_start'),
                ['class'=>'form-control form-control-solid dp']) !!}
                <div class="invalid-feedback" id="membership_start-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('Membership End'),'required') }}
            <div class="mb-5">
                {!! Form::text('membership_end',isset($result->id) ? $trainee->membership_end:old('membership_end'),
                ['class'=>'form-control form-control-solid dp']) !!}
                <div class="invalid-feedback" id="membership_end-form-error"></div>
            </div>
        </div>
        <input type="hidden" name="telephone_code" id="telephone_code" value="{{ $user->telephone_code ?? '20' }}">
        <div class="col-lg-6 ">
            {{ label(__('Mobile')) }}
            <div class="mb-5 telephone_country">
                {!! Form::tel('telephone', isset($result) ? $telephone:null,['class'=>'form-control form-control-solid valid_telephone add_telephone numeric-only','id'=>'telephone']) !!}
                <div class="invalid-feedback" id="telephone-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('Status'),'required') }}
            <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::select('status',trainee_status(),isset($result->id) ? $trainee->status:old('status'),
                ['class'=>'form-select form-select-solid','id'=>'status','data-placeholder'=>__('Select an option')]) !!}
                <div class="invalid-feedback" id="status-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('Age'),'required') }}
            <div class="mb-5">
                {!! Form::number('age',isset($result->id) ? $trainee->age:old('age'),
                ['class'=>'form-control form-control-solid']) !!}
                <div class="invalid-feedback" id="age-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('Weight'),'required') }}
            <div class="mb-5">
                {!! Form::number('weight',isset($result->id) ? $trainee->weight:old('weight'),['class'=>'form-control
                form-control-solid']) !!}
                <div class="invalid-feedback" id="weight-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('Height'),'required') }}
            <div class="mb-5">
                {!! Form::number('height',isset($result->id) ? $trainee->height:old('height'),['class'=>'form-control
                form-control-solid']) !!}
                <div class="invalid-feedback" id="height-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('Training Level'),'required') }}
            <div class="mb-5">
                {!! Form::select('training_level',[''=>'']+training_level(),
                isset($result->id) ? $trainee->training_level:old('training_level'),
                ['class'=>'form-control form-control-solid','data-placeholder'=>__('Select Training Level')]) !!}
                <div class="invalid-feedback" id="training_level-form-error"></div>
            </div>
        </div>
    </div>
    <!--end::Row-->
    <div class="separator separator-dashed mb-8"></div>

    <button type="submit" class="btn btn-primary submit">
        <span class="indicator-label">{{ isset($result->id)? __('Update') : __('Create')}}</span>
        <span class="indicator-progress">{{__('Please wait')}}...
        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
    </button>

    {!! Form::close() !!}

@endsection

