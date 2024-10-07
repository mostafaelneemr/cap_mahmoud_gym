


    <!--begin::Card-->
    <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <!--begin::Table-->
            <div class="table-responsive">
                <table class="table align-middle table-row-bordered table-row-solid gy-4 gs-9 {{isset($customClass)?$customClass:''}}" id="{{$datatableID}}">
                    <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                    <tr  class="">
                        @foreach($tableColumns as $key => $value)
                            <th >{!!   $value !!}</th>
                        @endforeach
                    </tr>
                    </thead>
{{--                    @if(count($jsColumns) > count($filterIgnoreColumns))--}}
{{--                    <thead >--}}
{{--                        <tr class=" thead2">--}}

{{--                                @php $counter = 0; @endphp--}}
{{--                                @foreach($jsColumns as $key => $value)--}}
{{--                                    @if(in_array($key,$filterIgnoreColumns))--}}
{{--                                        <td></td>--}}
{{--                                    @php $counter++ @endphp--}}
{{--                                        @continue--}}
{{--                                        @endif--}}
{{--                                        <th>{{$tableColumns[$counter]}}</th>--}}
{{--                                    @php $counter++ @endphp--}}
{{--                                @endforeach--}}

{{--                        </tr>--}}
{{--                    </thead>--}}
{{--                    @endif--}}
                    <tbody class="fw-6 fw-semibold text-gray-600" >

                    </tbody>

                </table>
            </div>
            <!--end::Table-->
            {{-- start::spinner --}}
            <div class="app-default app-default-loader w-100 h-100" data-kt-app-page-loading-enabled="true" data-kt-app-page-loading="on">
                <!--begin::Page loading(append to div)-->
                <div class="page-loader page-loader-custom position-absolute  align-items-start" style="top: 50px; left:0; right:0; margin:20px; z-index:3; opacity:.6">
        <span class="spinner-border text-primary mt-10" role="status">
            <span class="visually-hidden">{{__('Loading...')}}</span>
        </span>
                </div>
            </div>
            {{-- end::spinner --}}
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->


    <script type="text/javascript">

        var datatableID = "#{{$datatableID}}" ;
        var datatableURL = "{{$datatableURL}}" ;
        var datatableVar = "{{$datatableVar}}" ;
        var js_column =  @php echo json_encode($jsColumns); @endphp ;
        var js_dataTableSort =  @php echo json_encode($dataTableSort); @endphp ;

        createDatatable(datatableID,datatableURL,datatableVar,js_column,js_dataTableSort);



    </script>



