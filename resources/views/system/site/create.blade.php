@extends('system.layout')

@section('content')
    {!! Form::open(['id'=>'main-form','onsubmit' =>  isset($result) ? 'FormSubmit("'.route('system.site.update',$result->id).'");return false;':'FormSubmit("'.route('system.site.store') .'");return false;','method' => isset($result) ?  'PATCH' : 'POST']) !!}
    <div id="form-alert-message"></div>
    <!--begin::Row-->
    <div class="row gx-10 ">

        <div class="col-lg-6 ">
            {{ label(__('Icon'),'required') }}
            <div class="mb-5">
                {!! Form::text('icon',isset($result->icon) ? $result->icon:null,['class'=>'form-control form-control-solid']) !!}
                <div class="invalid-feedback" id="icon-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('title'),'required') }}
            <div class="mb-5">
                {!! Form::text('title',isset($result->title) ? $result->title:null,['class'=>'form-control form-control-solid']) !!}
                <div class="invalid-feedback" id="title-form-error"></div>
            </div>
        </div>

        <div class="col-lg-12 ">
            {{ label(__('Link'),'required') }}
            <div class="mb-5">
                {!! Form::text('link_url',isset($result->link_url) ? $result->link_url:null,['class'=>'form-control form-control-solid']) !!}
                <div class="invalid-feedback" id="link_url-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6">
            {{ label(__('Status'),'required') }}
            <div class="mb-5">
                {!! Form::select('status',[''=>'']+default_status(null,true),isset($result->status) ? $result->status:null,['class'=>'form-select form-select-solid','id'=>'status',' data-placeholder'=>__('Select an option')]) !!}
                <div class="invalid-feedback" id="status-form-error"></div>
            </div>
        </div>

    </div>
    <!--end::Row-->
    <div class="separator separator-dashed mb-8"></div>

    <button type="submit" class="btn btn-primary submit">
        <span class="indicator-label">{{ isset($result->id)? __('Update') :  __('Create')}}</span>
        <span class="indicator-progress">{{__('Please wait')}}...
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        </span>
    </button>

    {!! Form::close() !!}

@endsection
