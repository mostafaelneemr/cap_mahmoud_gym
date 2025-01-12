
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
                            {!! Form::select('message_read',['all'=>'All']+message_read(null,true,true),null,
                                ['id'=>'message_read','class'=>'form-control form-control-solid select_local',
                                'data-placeholder' => __('message'),'data-control' => 'select2',
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
