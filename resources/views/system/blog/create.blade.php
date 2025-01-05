@extends('system.layout')

@section('content')

    {!! Form::open(['id'=>'main-form','onsubmit' =>  isset($post) ? 'FormSubmit("'.route('system.blog.update',$post->id).'");return false;':'FormSubmit("'.route('system.blog.store') .'");return false;','method' => isset($result) ?  'PATCH' : 'POST']) !!}

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
                                    <div class="col-lg-6">
                                        {{ label( __('Title'),'required') }}
                                        <div class="mb-5">
                                            {{ Form::input(
                                                'text',
                                                'input[lang]['.$language['id'].'][title]',
                                                isset($result[$language['id']]) ? $result[$language['id']]['title'] : '',
                                                [
                                                    'id' => 'input[lang]['.$language['id'].'][name]',
                                                    'class' => 'form-control form-control-solid',
                                                ]
                                            ) }}
                                            <div class="invalid-feedback"
                                                 id="input[lang][{{$language['id']}}][title]-form-error"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        {{ label( __('Meta Tag Title'),'required') }}
                                        <div class="mb-5">
                                            {{ Form::input(
                                                'text',
                                                'input[lang]['.$language['id'].'][meta_title]',
                                                isset($result[$language['id']]) ? $result[$language['id']]['meta_title'] : '',
                                                [
                                                    'id' => 'input[lang]['.$language['id'].'][meta_title]',
                                                    'class' => 'form-control form-control-solid',
                                                ]
                                            ) }}
                                            <div class="invalid-feedback"
                                                 id="input[lang][{{$language['id']}}][meta_title]-form-error"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row gx-10 ">
                                    <div class="col-lg-6">
                                        {{ label( __('Meta Tag Description')) }}
                                        <div class="mb-5">
                                            <textarea name="input[lang][{{$language['id']}}][meta_description]"
                                                      class="form-control form-control-solid"
                                                      id="input[lang][{{$language['id']}}][meta_description]-form-input">{{isset($result[$language['id']]) ? $result[$language['id']]['meta_description'] : ''}}</textarea>
                                            <div class="invalid-feedback"
                                                 id="input[lang][{{$language['id']}}][meta_description]-form-error"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        {{ label( __('Meta Tag Keywords')) }}
                                        <div class="mb-5">
                                            <textarea name="input[lang][{{$language['id']}}][meta_keyword]"
                                                      class="form-control form-control-solid"
                                                      id="input[lang][{{$language['id']}}][meta_keyword]-form-input">{{isset($result[$language['id']]) ? $result[$language['id']]['meta_keyword'] : ''}}</textarea>
                                            <div class="invalid-feedback"
                                                 id="input[lang][{{$language['id']}}][meta_keyword]-form-error"></div>
                                        </div>
                                    </div>
                                </div>
{{--                                <div class="row gx-10 ">--}}
{{--                                    <div class="col-lg-12">--}}
{{--                                        {{ label( __('Short Description')) }}--}}
{{--                                        <div class="mb-5">--}}
{{--                                            <textarea name="input[lang][{{$language['id']}}][short_description] "--}}
{{--                                                      class="form-control form-control-solid"--}}
{{--                                                      rows="7"--}}
{{--                                                      id="input[lang][{{$language['id']}}][short_description]-form-input">{{isset($result[$language['id']]) ? $result[$language['id']]['short_description'] : ''}}</textarea>--}}
{{--                                            <div class="invalid-feedback"--}}
{{--                                                 id="input[lang][{{$language['id']}}][short_description]-form-error"></div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

                                <div class="row gx-10 ">
                                    <div class="col-lg-12">
                                        {{ label( __('Description')) }}
                                        <div class="mb-5">
                                            <textarea name="input[lang][{{$language['id']}}][description] "
                                                      class="form-control form-control-solid description{{$language['id']}}"
                                                      id="input[lang][{{$language['id']}}][description]-form-input">{{isset($result[$language['id']]) ? $result[$language['id']]['description'] : ''}}</textarea>
                                            <div class="invalid-feedback"
                                                 id="input[lang][{{$language['id']}}][description]-form-error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="tab-pane fade" id="topic_data" role="tabpanel">

                    @isset($testimonial->id)
                        <input type="hidden" value="{{$testimonial->image}}" name="old_image">

                        <div class="form-group">
                            <div class="text-center">
                                <img src="{{asset($testimonial->image)}}"
                                     class="rounded-circle h-60 w-60" alt="image testimonial">
                            </div>
                        </div>
                    @endisset

                        <div class="row gx-10 ">
                            <div class="col-lg-6">
                                <div>
                                    {{ label( __('Image')) }}
                                    <div class="mb-5">
                                        {!! Form::file('image', ['id' => 'file']) !!}
                                        <div class="invalid-feedback" id="input[image]-form-error"></div>
                                    </div>
                                </div>
                                <span>{{__('Image dimensions :')}} 60 × 60</span>
                            </div>

                            <div class="col-lg-6 ">
                                {{ label(__('Status')) }}
                                <div class="mb-5">
                                    {!! Form::select('input[status]',status_select_data(),isset($post->status) ? $post->status:old('input[status]'),['class'=>'form-select form-select-solid','id'=>'input[status]',' data-placeholder'=>__('Select an option')]) !!}
                                    <div class="invalid-feedback" id="input[status]-form-error"></div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>


    <div class="separator separator-dashed mb-8"></div>

    <button type="submit" class="btn btn-primary submit">
        <span class="indicator-label">{{ isset($post)? __('Update') :  __('Create')}}</span>
        <span class="indicator-progress">{{__('Please wait')}}...
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        </span>
    </button>

    {!! Form::close() !!}

@endsection

@section('footer')
    <script type="text/javascript">
        @foreach($languages as $language)
        text_editor('description{{$language['id']}}','{{$language['id']}}');
        @endforeach

    </script>
@endsection
