<div class="d-flex flex-column gap-7 gap-lg-10">
    <div class="d-flex flex-column gap-7 gap-lg-10">
        <div class=" card-flush  flex-row-fluid">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>{{__('Info')}}</h2>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5">
                        <tbody class="fw-semibold text-gray-600">
                        <tr>
                                <td class="text-muted td_width">{{__('ID')}}</td>
                            <td class="fw-bold text-end td_width">{{$result->id}}</td>
                            <td class="text-muted">{{  __('User')}}</td>
                            <td class="fw-bold text-end">{{$result->user->name??''}}</td>
                        </tr>
                        <tr>
                            <td class="text-muted td_width">{{  __('Created At')}}</td>
                            <td class="fw-bold text-end td_width">{{$result->created_at->diffForHumans()??''}}</td>
                            <td class="text-muted">{{__('Updated At')}}</td>
                            <td class="fw-bold text-end">{{$result->updated_at->diffForHumans()??''}}</td>
                        </tr>
                        </tbody>
                    </table>
                    <!--end::Table-->
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <hr class="hr" />
        <div class=" card-flush  flex-row-fluid">

            <div class="card-body pt-0">
                <h2>{{__('Address')}}</h2>
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5">
                        <tbody class="fw-semibold text-gray-600">
                        @if(isset($result->location))
                            <tr>
                                <td class="text-muted td_width" >{{__('Country')}}</td>
                                <td class="fw-bold text-end td_width" >{{$result->location->country}} ({{$result->location->countryCode}})</td>
                                <td class="text-muted">{{__('city')}}</td>
                                <td class="fw-bold text-end">{{$result->location->city}}</td>
                            </tr>
                            <tr>
                                <td class="text-muted td_width" >{{__('Region Name')}}</td>
                                <td class="fw-bold text-end td_width" >{{$result->location->regionName}}</td>
                                <td class="text-muted">{{__('ISP')}}</td>
                                <td class="fw-bold text-end">{{$result->location->isp}}</td>
                            </tr>
                            <tr>
                                <td class="text-muted td_width">{{__('Latitude')}}</td>
                                <td class="fw-bold text-end td_width">{{$result->location->lat}}</td>
                                <td class="text-muted">{{__('Longitude')}}</td>
                                <td class="fw-bold text-end">{{$result->location->lon}}</td>
                            </tr>
                        @endif

                        </tbody>
                    </table>
                    <!--end::Table-->
                </div>
            </div>

            <!--end::Card body-->
        </div>
        <hr/>
        <div class=" card-flush  flex-row-fluid">
            <div class="card-body pt-0">
                <h2>{{__('Platform')}}</h2>
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5">
                        <tbody class="fw-semibold text-gray-600">
                        <tr>
                            <td class="text-muted td_width">{{__('Device')}}</td>
                            <td class="fw-bold text-end td_width">
                                {{$result->agent->device()}}
                                @if($result->agent->isDesktop())
                                    {{__('Desktop')}}
                                @elseif($result->agent->isMobile())
                                    {{__('Mobile')}}
                                @elseif($result->agent->isTablet())
                                    {{__('Tablet')}}
                                @else
                                    --
                                @endif
                            </td>
                            <td class="text-muted">{{__('Platform')}}</td>
                            <td class="fw-bold text-end">
                                {{$result->agent->platform()}} {{$result->agent->version($result->agent->platform())}}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted td_width">{{__('IP')}}</td>
                            <td class="fw-bold text-end td_width">
                                {{$result->ip}}
                            </td>
                            <td class="text-muted">{{__('Created At')}}</td>
                            <td class="fw-bold text-end">
                                @if($result->created_at == null)
                                    --
                                @else
                                    {{$result->created_at->diffForHumans()}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted td_width">{{__('Browser')}}</td>
                            <td class="fw-bold text-end td_width">{{$result->agent->browser()}}</td>
                            <td class="text-muted">{{__('Languages')}}</td>
                            <td class="fw-bold text-end">{{implode(',',$result->agent->languages())}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
