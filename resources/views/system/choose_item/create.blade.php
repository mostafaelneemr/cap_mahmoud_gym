@extends('system.layout')

@section('content')

    <!--begin::Form-->
    {!! Form::open(['id'=>'main-form','onsubmit' =>  isset($item) ? 'FormSubmit("'.route('system.choose-item.update',$item->id).'");return false;':'FormSubmit("'.route('system.choose-item.store') .'");return false;','method' => isset($item) ?  'PATCH' : 'POST']) !!}

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

                    <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6 fw-bold">
                        @foreach($languages as $language)
                            <li class="nav-item">
                                <a class="nav-link text-active-primary py-5 mb-5 me-6 @if($language['id'] == 1) active @endif "
                                   data-bs-toggle="tab" href="#language{{$language['id']}}">
                                    @if(!empty($language['name']))
                                        <img src="{{$language['image']}}" height="20px" width="20px" class="me-2">
                                    @endif
                                    {{$language['name']}}</a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content" id="myTabContent2">
                        @foreach($languages as $language)
                            <div class="tab-pane fade  @if($language['id'] == 1) show active @endif"
                                 id="language{{$language['id']}}" role="tabpanel">
                                <div class="row gx-10 ">
                                    <div class="col-lg-4">
                                        {{ label( __('Title'),'required') }}
                                        <div class="mb-5">
                                            {{ Form::input(
                                                'text',
                                                'input[lang]['.$language['id'].'][title]',
                                                isset($item) ? $item->{$language['id'] == 2 ? 'title_ar' : 'title_en'} : '',                                                [
                                                    'id' => 'input[lang]['.$language['id'].'][name]',
                                                    'class' => 'form-control form-control-solid',
                                                ]
                                            ) }}
                                            <div class="invalid-feedback"
                                                 id="input[lang][{{$language['id']}}][title]-form-error"></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-8">
                                        {{ label( __('Text')) }}
                                        <div class="mb-5">
                                            {{ Form::input(
                                                'text',
                                                'input[lang]['.$language['id'].'][text]',
                                                isset($item) ? $item->{$language['id'] == 2 ? 'text_ar' : 'text_en'} : '',
                                                [
                                                    'id' => 'input[lang]['.$language['id'].'][text]',
                                                    'class' => 'form-control form-control-solid',
                                                ]
                                            ) }}
                                            <div class="invalid-feedback"
                                                id="input[lang][{{$language['id']}}][text]-form-error">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="tab-pane fade" id="topic_data" role="tabpanel">
                    @isset($item->id)
                        <input type="hidden" value="{{$item->image}}" name="old_image">

                        <div class="form-group">
                            <div class="text-center">
                                <img src="{{asset($item->image)}}"
                                class="rounded-circle h-25 w-25" alt="image slider">
                            </div>
                        </div>
                    @endisset


                    <div class="row gx-10 mt-5">
                        <div class="col-lg-6">
                            <div>
                                {{ label( __('Image'), isset($item->id) ? '' : 'required') }}
                                <div class="mb-5">
                                    {!! Form::file('image', ['id' => 'file']) !!}
                                    <div class="invalid-feedback" id="input[image]-form-error"></div>
                                </div>
                            </div>
                            <span>{{__('Image dimensions :')}} 70 × 67</span>
                        </div>


                        <div class="col-lg-6">
                            {{ label(__('Status')) }}
                            <!--begin::Input group-->
                            <div class="mb-5">
                                {!! Form::select('status',[''=>'']+default_status(false,true),isset($item->status) ? $item->status:old('input[status]'),
                                    ['class'=>'form-select form-select-solid ','id'=>'status',' data-placeholder'=>__('Select an Status')]) !!}
                                <div class="invalid-feedback" id="status-form-error"></div>
                            </div>

                            <!--end::Input group-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="separator separator-dashed mb-8"></div>

    <button type="submit" class="btn btn-primary submit">
        <span class="indicator-label">{{ isset($item)? __('Update') :  __('Create')}}</span>
        <span class="indicator-progress">{{__('Please wait')}}...
			<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        </span>
    </button>

    {!! Form::close() !!}

@endsection
