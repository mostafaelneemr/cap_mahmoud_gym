@extends('system.layout')
@section('header')
    <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <!-- begin:: Content -->
    <div class="k-content	k-grid__item k-grid__item--fluid k-grid k-grid--hor" id="k_content">

        <!-- begin:: Content Head -->
        <div class="k-content__head	k-grid__item">
            <div class="k-content__head-main">
                <h3 class="k-content__head-title">{{$pageTitle}}</h3>
                <div class="k-content__head-breadcrumbs">
                    <a href="{{route('system.dashboard')}}" class="k-content__head-breadcrumb-home"><i class="flaticon2-shelter"></i></a>

                    @foreach($breadcrumb as $key => $value)
                        <span class="k-content__head-breadcrumb-separator"></span>
                        @if(isset($value['url']))
                            <a href="{{$value['url']}}" class="k-content__head-breadcrumb-link">{{$value['text']}}</a>
                        @else
                            <span class="k-content__head-breadcrumb-link k-content__head-breadcrumb-link--active">{{$value['text']}}</span>
                        @endif
                    @endforeach

                </div>
            </div>
        </div>

        <!-- end:: Content Head -->

        <!-- begin:: Content Body -->
        <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">
            <div class="k-portlet k-profile">
                <div class="k-profile__content">
                    <div class="row">
                        <div class="col-md-12 col-lg-5 col-xl-4">
                            <div class="k-profile__main">
                                <div class="k-profile__main-pic">
                                    <img src="{{asset('assets/media/users/300_21.jpg')}}" alt="" />
                                    <label class="k-profile__main-pic-upload">
                                        <i class="flaticon-photo-camera"></i>
                                    </label>
                                </div>
                                <div class="k-profile__main-info">
                                    <div class="k-profile__main-info-name">
                                        <i class="fas fa-{{$result->gender}}"></i>
                                        {{$result->name}}
                                    </div>
                                    <div class="k-profile__main-info-position">{{$result->job_title}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-4">
                            <div class="k-profile__contact">
                                <a href="tel:{{$result->mobile}}" class="k-profile__contact-item">
                                    <span class="k-profile__contact-item-icon"><i class="flaticon-support"></i></span>
                                    <span class="k-profile__contact-item-text">{{$result->mobile}}</span>
                                </a>
                                <a href="#" class="k-profile__contact-item">
                                    <span class="k-profile__contact-item-icon"><i class="flaticon-email-black-circular-button k-font-danger"></i></span>
                                    <span class="k-profile__contact-item-text">{{$result->email}}</span>
                                </a>
                            </div>
                        </div>
                        @if($result->address || $result->description)
                            <div class="col-md-12 col-lg-3 col-xl-4">
                                <div class="k-profile__stats">
                                    @if($result->address)
                                        <div class="k-profile__stats-item">
                                            <div class="k-profile__stats-item-label">{{__('Address')}}</div>
                                            <div class="k-profile__stats-item-chart">
                                                {{$result->address}}
                                            </div>
                                        </div>
                                    @endif
                                    @if($result->description)
                                        <div class="k-profile__stats-item">
                                            <div class="k-profile__stats-item-label">{{__('Description')}}</div>
                                            <div class="k-profile__stats-item-chart">
                                                {{$result->description}}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="k-profile__nav">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#k_tabs_1_1" role="tab">{{__('Information')}}</a>
                        </li>
                        
                    </ul>
                </div>
            </div>

            <!--end::Portlet-->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="k_tabs_1_1" role="tabpanel">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">{{__(':name\'s information',['name'=> $result->name])}}</h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <table class="table table-hover">

                                        <tbody>

                                        <tr>
                                            <td>{{__('ID')}}</td>
                                            <td>
                                                {{$result->id}}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{__('Birthdate')}}</td>
                                            <td>
                                                {{$result->birthdate}}
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>{{__('Status')}}</td>
                                            <td>
                                                {{__(ucfirst($result->status))}}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{__('Permission Group')}}</td>
                                            <td>
                                                {{$result->permission_group->name}}
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                    </div>
                    <!--end::Row-->
                </div>


            </div>
        </div>
        <!-- end:: Content -->
        @endsection
        @section('footer')
            <script src="{{asset('assets/demo/default/custom/components/charts/Chart.min.js')}}" type="text/javascript"></script>
            {{--        <script src="{{asset('assets/demo/default/custom/components/charts/chart-js.js')}}" type="text/javascript"></script>--}}

            <script type="text/javascript">
                $datatable = $('#datatable-main').DataTable({
                    "iDisplayLength": 25,
                    processing: true,
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "ajax": {
                        "url": "{{url()->full()}}",
                        "type": "GET",
                        "data": function(data){
                            data.isDataTable = "true";
                        }
                    }
                    /*,
                    "fnPreDrawCallback": function(oSettings) {
                        for (var i = 0, iLen = oSettings.aoData.length; i < iLen; i++) {
                            if(oSettings.aoData[i]._aData[6] != ''){
                                oSettings.aoData[i].nTr.className = oSettings.aoData[i]._aData[6];
                            }
                        }
                    }*/
                });


                function targetChart($year,$showloading){
                    if($showloading){
                        addLoading();
                    }

                    $.get('{{route('system.ajax.get')}}',{'type': 'user-target-chart','year': $year,'staff_id': {{$result->id}} },function($data){
                        if($showloading) {
                            removeLoading();
                        }
                        if(!$data.status){
                            toastr.error('{{__('Unable to get target data')}}', 'Error !', {"closeButton": true});
                            return;
                        }

                        var barChartData = {
                            labels: $data.data.label,
                            datasets: [{
                                label: '{{__('Target')}}',
                                backgroundColor: '#5d78ff',
//                            borderColor: '#6e4ff5',
                                borderWidth: 1,
                                data: $data.data.target
                            }, {
                                label: '{{__('Achieved')}}',
                                backgroundColor: '#c5cbe3',
//                            borderColor: '#6e4ff5',
                                borderWidth: 1,
                                data: $data.data.achieved
                            }]

                        };
                        var ctx = $('#k_chartjs_1');
                        var myBarChart = new Chart(ctx, {
                            height: '300px',
                            type: 'bar',
                            data: barChartData,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                legend: {
                                    position: 'top'
                                }
                            }
                        });

//                    ctx.height = 300;

                    });
                }

                $(document).ready(function(){
                    targetChart('{{date('Y')}}');
                });




            </script>
@endsection
