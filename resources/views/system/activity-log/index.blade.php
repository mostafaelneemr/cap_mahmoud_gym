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
                                {!! Form::select('event',[''=>'']+event_enum(null,true),null,
                                    ['id'=>'event','class'=>'form-control form-control-solid select_local',
                                    'data-placeholder' => __('Event'),'data-control' => 'select2',
                                    'onchange' => "submitForm()",'data-hide-search'=>"true",
                                    'data-allow-clear'=>"true"]) !!}
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="mb-5">
                                {!! Form::text('created_at_from',null,['class'=>'form-control form-control-solid dp',
                                'id'=>'created_at1','autocomplete'=>'off','placeholder' => __('Date From'),
                               ]) !!}
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="mb-5">
                                {!! Form::text('created_at_to',null,['class'=>'form-control form-control-solid dp',
                                   'id'=>'created_at2', 'autocomplete'=>'off','placeholder' => __('Date To')]) !!}
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="mb-5">
                                {!! Form::text('subject_id',null,['class'=>'form-control form-control-solid',
                                'autocomplete'=>'off','placeholder' => __('Subject ID'),'oninput' => "submitForm()"]) !!}
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="mb-5">
                                <select name="subject_type" id="subject_type" onchange="submitForm()"
                                        class="form-control form-control-solid select_local"
                                        data-placeholder="{{ __('Select Model') }}"
                                        data-allow-clear="true">
                                    <option selected disabled></option>
                                    @foreach($models as $k=>$val)
                                        <option value="{{$val}}">{{$val}}</option>
                                    @endforeach
                                </select>
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
            $('#event').val('').change();
            $('#subject_type').val('').change();
            var form = $('#filterForm');
            form[0].reset();
            submitForm();
        }

        $('#created_at1').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
            $('#filterForm').submit();

            setTimeout(() => {
                setToDate(picker.startDate.format('YYYY-MM-DD'))

            }, 300);
        });


        function setToDate(value) {
            $('#created_at2').daterangepicker({
                autoUpdateInput: false,
                todayHighlight: true,
                singleDatePicker: true,
                autoClose: true,
                autoApply: true,
                showDropdowns: true,
                minDate: new Date(value.replace(/(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3")),
                minYear: 2017,
                timePicker: false,
                maxYear: parseInt(moment().format('YYYY'), 11),
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            });
            $('#created_at2').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
            });
            $('#created_at2').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });
            $('#created_at2').on('apply.daterangepicker', function (ev, picker) {
                $('#filterForm').submit();
            });
        }
    </script>

@endsection

