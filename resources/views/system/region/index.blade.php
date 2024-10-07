@extends('system.layout')

@section('content')

    {!! Form::open(['id'=>'filterForm','onsubmit'=>'filterFunction("'.$datatableURL.'","'.$datatableVar.'",$(this));return false;']) !!}
    <!--begin::Card-->
    <div class="d-flex align-items-center col-12 p-0 parent">

        <!--begin:Action-->
        <div class="d-flex align-items-center mb-2 filter-btn-div">
            <a id="kt_horizontal_search_advanced_link " class="btn btn-primary filter-btn" data-bs-toggle="collapse"
               href="#kt_advanced_search_form" aria-expanded="false"><i class="fas fa-filter p-0"></i></a>
        </div>
        <!--end:Action-->
    </div>
    <div class="card mb-7">
        <!--begin::Card body-->
        <div class="card-body d-flex flex-wrap p-0">

            <div class="collapse show col-12 mt-9 p-5" id="kt_advanced_search_form">

                <div class="row g-8">
                    <div class="row gx-10">
                        <div class="col-lg-2">
                            <div class="mb-5">
                                {!! Form::text('id',null,['class'=>'form-control form-control-solid',
                                'autocomplete'=>'off','placeholder' => __('ID'),'oninput' => "submitForm()"]) !!}
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="mb-5">
                                {!! Form::select('address_country',[''=>'']+$countries,null,
                                                 ['id'=>'address_country','class'=>'form-control form-control-solid select_ajax',
                                   'data-control' => 'select2','data-placeholder' => __('Country'),'data-allow-clear'=>"true",
                                                 'onchange' => "submitForm();",'data-hide-search'=>"false"]) !!}
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="mb-5">
                                {!! Form::text('name_with_lang',null,['class'=>'form-control form-control-solid',
                                'autocomplete'=>'off','placeholder' => __('Name'),'oninput' => "submitForm()"]) !!}
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="mb-5">
                                {!! Form::select('status',[''=>'']+default_status(null,true,true),null,
                                    ['id'=>'status','class'=>'form-control form-control-solid select_ajax',
                                    'data-placeholder' => __('Status'),'data-control' => 'select2',
                                    'onchange' => "submitForm()",'data-hide-search'=>"true",
                                    'data-allow-clear'=>"true"]) !!}
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <button type="reset" class="btn btn-warning" onclick="resetForm()">{{__('Reset')}}</button>
                        </div>
                    </div>
                </div>

            </div>
            <!--end::Advance form-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
    {!! Form::close() !!}


        @include('system.datatable')

@endsection

@section('footer')

    <script>

        function submitForm(){
            $('#filterForm').submit();
        }

        function resetForm(){
            $('#address_country').val('').trigger('change');
            $('#status').val('').change();
            var form = $('#filterForm');
            form[0].reset();
            submitForm();
        }


    </script>

@endsection
