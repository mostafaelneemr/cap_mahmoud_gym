@extends('system.layout')

@section('links')
    <span style="padding-left:10px;padding-right:10px;"> {{edit_links('system.user.edit',route('system.user.edit',$result->id) )}}</span>
@endsection

@section('content')
    <div class="card mb-6 mb-xl-9">
        <div class="card-body pt-9 pb-0">
            <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                <div class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4">
                    <img class="mw-50px mw-lg-75px"  src="assets/media/avatars/blank.png" style="max-width: 100% !important;" alt="image" />
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        <div class="d-flex flex-column">

                            <div class="d-flex align-items-center mb-1">
                                <a href="javascript:void(0);" class="text-gray-800 text-hover-primary fs-2 fw-bold me-3">
                                    {{$result->firstname.' '.$result->lastname}}
                                </a>
                                <span class="badge badge-light-{{$result->status ==1 ? 'success':'danger'}} me-auto">
                                     {{$result->status ==1 ? __('Active'):__('In-Active')}}
                                </span>
                            </div>
                            <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400">
                                {{$result->username}}
                            </div>
                            <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400">
                                <a href="mailto:{{$result->email}}">{{$result->email}}</a>
                            </div>
                            @if(!empty($result->department->name))
                            <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400">
                                {{$result->department->name??""}}
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-wrap justify-content-start">
                        <div class="d-flex flex-wrap">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bold">{{$result->id}}</div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">{{__('ID')}}</div>
                            </div>
                            @if(!empty($result->createdBy->name))
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bold">{{$result->createdBy->name}}</div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">{{__('Created By')}}</div>
                            </div>
                            @endif
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bold">
                                        @if(!empty($result->permission_group))
                                            <a href="{{route('system.permission-group.edit',$result->permission_group_id)}}" target="_blank">{{$result->permission_group->name}}</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">{{__('Permission Group')}}</div>
                            </div>
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bold"> {{$result->created_at->format('Y-m-d')??''}}</div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">{{__('Created At')}}</div>
                            </div>
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bold"> {{$result->updated_at->format('Y-m-d')??''}}</div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">{{__('Updated At')}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="separator"></div>
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6 active" data-bs-toggle="tab" href="#kt_staff_auth_sessions_tab">{{__('Auth Sessions')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6" data-bs-toggle="tab" href="#kt_staff_logs_tab">{{__('Activity Log')}}</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="d-flex flex-column flex-xl-row">
        <div class="flex-lg-row-fluid">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="kt_staff_auth_sessions_tab" role="tabpanel">
                    <div class="card pt-4 mb-6 mb-xl-9">
                        <div class="card-header border-0">
                            <div class="card-title">
                                <h2>{{__('Auth Sessions')}}</h2>
                            </div>
                        </div>
                        {{view('system.datatable',$authSessionData)}}
                    </div>
                </div>
                <div class="tab-pane fade" id="kt_staff_logs_tab" role="tabpanel">
                    <div class="card pt-4 mb-6 mb-xl-9">
                        <div class="card-header border-0">
                            <div class="card-title">
                                <h2>{{__('Activity Log')}}</h2>
                            </div>
                        </div>
                        {{view('system.datatable',$activityLogData)}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-details" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-body" id="show_data">

                </div>
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
                </div>

            </div>
        </div>
    </div>
    <!--begin::Modal - Upload File-->
    <!--end::Modal - Upload File-->
@endsection

