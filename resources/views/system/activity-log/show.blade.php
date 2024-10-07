@extends('system.layout')

@section('content')

            <!--begin::Order details page-->
            <div class="d-flex flex-column gap-7 gap-lg-10">
                <div class="d-flex flex-column flex-xl-row gap-7 gap-lg-10">
                    <div class="card card-flush  flex-row-fluid">
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
                                <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5  ">
                                    <tbody class="fw-semibold text-gray-600">
                                    <tr>
                                        <td class="text-muted">{{__('ID')}}</td>
                                        <td class="fw-bold text-end">{{$result->id}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{__('Log Name')}}</td>
                                        <td class="fw-bold text-end">{{$result->log_name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{__('Status')}}</td>
                                        <td class="fw-bold text-end">{{$result->description}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{__('Model')}}</td>
                                        <td class="fw-bold text-end">{{last(explode('\\',$result->subject_type))}} ({{$result->subject_id}})</td>
                                    </tr>
                                    @if(isset($result->causer->id))
                                        <tr>
                                            <td class="text-muted">{{__('User')}}</td>
                                            <td class="fw-bold text-end">
                                                <a target="_blank" href="{{route('system.user.show',$result->causer->id)}}">{{$result->causer->name}}</a>
                                            </td>
                                        </tr>
                                    @endif


                                    </tbody>
                                </table>
                                <!--end::Table-->
                            </div>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <div class="card card-flush  flex-row-fluid">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>{{__('Country')}}</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5">
                                    <tbody class="fw-semibold text-gray-600">
                                    @if(isset($result->location))
                                        <tr>
                                            <td class="text-muted">{{__('Country')}}</td>
                                            <td class="fw-bold text-end">{{$result->location->country}} ({{$result->location->countryCode}})</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{__('city')}}</td>
                                            <td class="fw-bold text-end">{{$result->location->city}}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{__('Region Name')}}</td>
                                            <td class="fw-bold text-end">{{$result->location->regionName}}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{__('ISP')}}</td>
                                            <td class="fw-bold text-end">{{$result->location->isp}}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{__('Latitude')}}</td>
                                            <td class="fw-bold text-end">{{$result->location->lat}}</td>
                                        </tr>
                                        <tr>
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
                    <div class="card card-flush  flex-row-fluid">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>{{__('Platform')}}</h2>
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
                                        <td class="text-muted">{{__('Device')}}</td>
                                        <td class="fw-bold text-end">
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
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{__('Platform')}}</td>
                                        <td class="fw-bold text-end">
                                            {{$result->agent->platform()}} {{$result->agent->version($result->agent->platform())}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{__('IP')}}</td>
                                        <td class="fw-bold text-end">
                                            {{$result->ip}}
                                        </td>
                                    </tr>
                                    <tr>
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
                                        <td class="text-muted">{{__('Browser')}}</td>
                                        <td class="fw-bold text-end">{{$result->agent->browser()}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{__('Languages')}}</td>
                                        <td class="fw-bold text-end">{{implode(',',$result->agent->languages())}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <!--end::Table-->
                            </div>
                        </div>

                        <!--end::Card body-->
                    </div>

                </div>
                <!--end::Order summary-->
                <!--begin::Tab content-->
                <div class="tab-content">
                    <div class="tab-pane fade show active">
                        <!--begin::Orders-->
                        <div class="d-flex flex-column gap-7 gap-lg-10">
                            <!--begin::Order history-->
                            <div class="card card-flush  flex-row-fluid">
                                <!--begin::Card header-->
                                <div class="card-header">
                                    <div class="card-title">
                                        <h2>{{__('Data')}}</h2>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                                            <thead>
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="min-w-100px">{{__('Key')}}</th>
                                                @if(isset($result->properties['old']))
                                                    <th class="min-w-70px">{{__('Old')}}</th>
                                                    <th class="min-w-70px">{{__('New')}}</th>
                                                @else
                                                    <th class="min-w-70px">{{__('Attributes')}}</th>
                                                @endif
                                            </tr>
                                            </thead>
                                            <tbody class="fw-semibold text-gray-600">
                                            @if(isset($result->properties['attributes']))
                                                @php
                                                    $keys = array_keys($result->properties['attributes']);
                                                @endphp

                                            @foreach($keys as $value)
                                                <tr>
                                                    <td>
                                                        <div class="badge badge-light-primary">
                                                        {{$value}}
                                                        </div>
                                                    </td>
                                                    @if(isset($result->properties['old']))
                                                        <td>
                                                            <div class="badge badge-light-danger">
                                                            @if(is_array($result->properties['old'][$value]))
                                                                <pre>{{print_r($result->properties['old'][$value])}}</pre>
                                                            @else
                                                                {{$result->properties['old'][$value]}}
                                                            @endif
                                                            </div>
                                                        </td>
                                                    @endif
                                                    <td>
                                                        <div class="badge badge-light-success">
                                                        @if(is_array($result->properties['attributes'][$value]))
                                                            <pre>{{print_r($result->properties['attributes'][$value])}}</pre>
                                                        @else
                                                            {{$result->properties['attributes'][$value]}}
                                                        @endif
                                                        </div>
                                                    </td>

                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                </div>
                                <!--end::Card body-->
                            </div>
                        </div>
                        <!--end::Orders-->
                    </div>
                    <!--end::Tab pane-->
                </div>
                <!--end::Tab content-->
            </div>
            <!--end::Order details page-->



@endsection
