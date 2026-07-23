@extends('system.layout')

@section('content')

    <!--begin::Form-->
    {!! Form::open(['id'=>'main-form','onsubmit' =>  isset($socialLink) ? 'FormSubmit("'.route('system.social-links.update',$socialLink->id).'");return false;':'FormSubmit("'.route('system.social-links.store') .'");return false;','method' => isset($socialLink) ?  'PATCH' : 'POST']) !!}

    <div id="form-alert-message"></div>

    <div class="card mb-6 mb-xl-9">
        <div class="card-body pt-9 pb-0">

            <div class="row gx-10 ">
                <div class="col-lg-6">
                    {{ label( __('Title'),'required') }}
                    <div class="mb-5">
                        {{ Form::input('text', 'title', isset($socialLink) ? $socialLink->title : '', [
                            'id' => 'title',
                            'class' => 'form-control form-control-solid',
                        ]) }}
                        <div class="invalid-feedback" id="title-form-error"></div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{ label( __('URL'),'required') }}
                    <div class="mb-5">
                        {{ Form::input('text', 'url', isset($socialLink) ? $socialLink->url : '', [
                            'id' => 'url',
                            'class' => 'form-control form-control-solid',
                        ]) }}
                        <div class="invalid-feedback" id="url-form-error"></div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{ label( __('Icon (e.g. fa-facebook)')) }}
                    <div class="mb-5">
                        {{ Form::input('text', 'icon', isset($socialLink) ? $socialLink->icon : 'fa-link', [
                            'id' => 'icon',
                            'class' => 'form-control form-control-solid',
                        ]) }}
                        <div class="invalid-feedback" id="icon-form-error"></div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{ label(__('Order')) }}
                    <div class="mb-5">
                        {{ Form::input('number', 'order', isset($socialLink) ? $socialLink->order : 0, [
                            'id' => 'order',
                            'class' => 'form-control form-control-solid',
                        ]) }}
                        <div class="invalid-feedback" id="order-form-error"></div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{ label(__('Status'),'required') }}
                    <div class="mb-5">
                        {!! Form::select('is_active',['' => '', 1 => __('Active'), 0 => __('Inactive')], isset($socialLink) ? $socialLink->is_active : 1,
                            ['class'=>'form-select form-select-solid ','id'=>'is_active',' data-placeholder'=>__('Select Status')]) !!}
                        <div class="invalid-feedback" id="is_active-form-error"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="separator separator-dashed mb-8"></div>

    <button type="submit" class="btn btn-primary submit">
        <span class="indicator-label">{{ isset($socialLink)? __('Update') :  __('Create')}}</span>
        <span class="indicator-progress">{{__('Please wait')}}...
			<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        </span>
    </button>

    {!! Form::close() !!}

@endsection
