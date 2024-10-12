@extends('system.layout')

@section('content')

    <!--begin::Form-->
    {!! Form::open(['id'=>'main-form','onsubmit' =>  isset($blog) ? 'FormSubmit("'.route('system.blog.update',$blog->id).'");return false;':'FormSubmit("'.route('system.blog.store') .'");return false;','method' => isset($blog) ?  'PATCH' : 'POST']) !!}

    <div id="form-alert-message"></div>

    <div class="card mb-6 mb-xl-9">
        <div class="card-body pt-9 pb-0">
            <ul class="nav nav-tabs flex-nowrap text-nowrap mb-5 fs-6 fw-bold">
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6 active" data-bs-toggle="tab"
                       href="#description">{{__('General')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6" data-bs-toggle="tab"
                       href="#topic_data">{{__('Data')}}</a>
                </li>
            </ul>


            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="description" role="tabpanel">


                    <div class="tab-content" id="myTabContent2">
                        <div class="tab-pane fade show active" role="tabpanel">
                            <div class="row gx-10 ">
                                <div class="col-lg-4">
                                    {{ label( __('Title'),'required') }}
                                    <div class="mb-5">
                                        {{ Form::input('text','title', isset($blog) ?? $blog->title,
                                        ['id' => 'title','class' => 'form-control form-control-solid',]
                                        )}}
                                        <div class="invalid-feedback" id="title-form-error"></div>
                                    </div>
                                </div>

                                <div class="col-lg-8">
                                    {{ label( __('Name')) }}
                                    <div class="mb-5">
                                        {{ Form::input('text','name',isset($blog) ?? $blog->name,
                                           ['id' => 'name', 'class' => 'form-control form-control-solid',]
                                        ) }}
                                        <div class="invalid-feedback" id="name-form-error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="topic_data" role="tabpanel">
                    @isset($blog->id)
                        <input type="hidden" value="{{$blog->image}}" name="old_image">

                        <div class="form-group">
                            <div class="text-center">
                                <img src="{{asset($blog->image)}}"
                                     class="rounded-circle h-25 w-25" alt="image blog">
                            </div>
                        </div>
                    @endisset


                    <div class="row gx-10 mt-5">
                        <div class="col-lg-6">
                            <div>
                                {{ label( __('Image'), isset($blog->id) ? '' : 'required') }}
                                <div class="mb-5">
                                    {!! Form::file('image', ['id' => 'file']) !!}
                                    <div class="invalid-feedback" id="input[image]-form-error"></div>
                                </div>
                            </div>
                            <span>{{__('Image dimensions :')}} 70 × 67</span>
                        </div>


                        <div class="col-lg-6">
                            {{ label(__('Status')) }}
                            <div class="mb-5">
                                {!! Form::select('status',[''=>'']+default_status(false,true),isset($blog->status) ? $blog->status:old('input[status]'),
                                    ['class'=>'form-select form-select-solid ','id'=>'status',' data-placeholder'=>__('Select an Status')]) !!}
                                <div class="invalid-feedback" id="status-form-error"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="separator separator-dashed mb-8"></div>

    <button type="submit" class="btn btn-primary submit">
        <span class="indicator-label">{{ isset($blog)? __('Update') :  __('Create')}}</span>
        <span class="indicator-progress">{{__('Please wait')}}...
			<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        </span>
    </button>

    {!! Form::close() !!}

@endsection
