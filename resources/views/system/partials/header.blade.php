<!--begin::Header-->
<div id="kt_app_header" class="app-header d-flex flex-column flex-stack">
    <!--begin::Header main-->
    <div class="d-flex flex-stack flex-grow-1" style="padding: 10px 0;  @if(env('APP_ENV') == 'local') background-color: #f7b733; @elseif(env('APP_ENV') == 'test') background-color: #ffc7005c; @endif " >
        <div class="app-header-logo d-flex align-items-center ps-lg-12" id="kt_app_header_logo">
            <!--begin::Sidebar toggle-->
            <div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-sm btn-icon bg-body btn-color-gray-500 btn-active-color-primary w-30px h-30px ms-n2 me-4 d-none d-lg-flex" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
                <i class="ki-outline ki-abstract-14 fs-3 mt-1"></i>
            </div>
            <!--end::Sidebar toggle-->
            <!--begin::Sidebar mobile toggle-->
            <div class="btn btn-icon btn-active-color-primary w-35px h-35px ms-3 me-2 d-flex d-lg-none" id="kt_app_sidebar_mobile_toggle">
                <i class="ki-outline ki-abstract-14 fs-2"></i>
            </div>
            <!--end::Sidebar mobile toggle-->
            <!--begin::Logo-->
{{--            <a href="apps/customers/list.html" class="app-sidebar-logo">--}}
{{--                <img alt="Logo" src="assets/media/logos/demo39.svg" class="h-25px theme-light-show" />--}}
{{--                <img alt="Logo" src="assets/media/logos/demo39-dark.svg" class="h-25px theme-dark-show" />--}}
{{--            </a>--}}
            <a href="{{route('system.dashboard')}}" class="brand-logo">
                <img alt="Logo" src="{{ setting('logo')->value ?? '' }}" style="width:160px;"/>
            </a>
            <!--end::Logo-->
        </div>
        <!--begin::Navbar-->
        <div class="app-navbar flex-grow-1 justify-content-end" id="kt_app_header_navbar">
            <div class="app-navbar-item d-flex align-items-stretch flex-lg-grow-1">
                <!--begin::Search-->
                <div id="kt_header_search" class="header-search d-flex align-items-center w-lg-350px" data-kt-search-keypress="true" data-kt-search-min-length="2" data-kt-search-enter="enter" data-kt-search-layout="menu" data-kt-search-responsive="true" data-kt-menu-trigger="auto" data-kt-menu-permanent="true" data-kt-menu-placement="bottom-start">
                    <!--begin::Tablet and mobile search toggle-->
                    <div data-kt-search-element="toggle" class="search-toggle-mobile d-flex d-lg-none align-items-center">
                        <div class="d-flex">
                            <i class="ki-outline ki-magnifier fs-1 fs-1"></i>
                        </div>
                    </div>
                    <!--end::Tablet and mobile search toggle-->
                    <!--begin::Form(use d-none d-lg-block classes for responsive search)-->
{{--                    <form data-kt-search-element="form" class="d-none d-lg-block w-100 position-relative mb-5 mb-lg-0" autocomplete="off">--}}
{{--                        <!--begin::Hidden input(Added to disable form autocomplete)-->--}}
{{--                        <input type="hidden" />--}}
{{--                        <!--end::Hidden input-->--}}
{{--                        <!--begin::Icon-->--}}
{{--                        <i class="ki-outline ki-magnifier search-icon fs-2 text-gray-500 position-absolute top-50 translate-middle-y ms-5"></i>--}}
{{--                        <!--end::Icon-->--}}
{{--                        <!--begin::Input-->--}}
{{--                        <input type="text" class="search-input form-control form-control border-0 h-lg-45px ps-13" name="search" id="search" readonly value="" placeholder="{{__('Search...')}}" data-kt-search-element="input" />--}}
{{--                        <!--end::Input-->--}}
{{--                        <!--begin::Spinner-->--}}
{{--                        <span class="search-spinner position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-5" data-kt-search-element="spinner">--}}
{{--											<span class="spinner-border h-15px w-15px align-middle text-gray-400"></span>--}}
{{--										</span>--}}
{{--                        <!--end::Spinner-->--}}
{{--                        <!--begin::Reset-->--}}
{{--                        <span class="search-reset btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-4" data-kt-search-element="clear">--}}
{{--											<i class="ki-outline ki-cross fs-2 fs-lg-1 me-0"></i>--}}
{{--										</span>--}}
{{--                        <!--end::Reset-->--}}
{{--                    </form>--}}
                    <!--end::Form-->
                    <!--begin::Menu-->
                    <div data-kt-search-element="content" class="menu menu-sub menu-sub-dropdown py-7 px-7 overflow-hidden w-300px w-md-350px">
                        <!--begin::Wrapper-->
                        <div data-kt-search-element="wrapper">
                            <div class="" data-kt-search-element="main">
                                <!--begin::Heading-->
                                <div class="d-flex flex-stack fw-semibold mb-4">
                                    <!--begin::Label-->
                                    <span class="text-muted fs-6 me-2">{{__('Quick Search')}}</span>
                                    <!--end::Label-->

                                </div>
                                <!--end::Heading-->
                                <!--begin::Items-->
                                <div class="scroll-y mh-200px mh-lg-325px">
                                    <div class="d-flex flex-wrap justify-content-between">
                                        <div class="mb-5 col-5">
                                            <input type="number" autofocus  id="quick_search_id" oninput="$('#quick_search_name').val('')"  class="form-control form-control-sm form-control-solid @if(lang() =='ar') search_id @endif" placeholder="{{__('ID')}}" name="quick_search_id" style=""/>
                                        </div>
                                        <div class="mb-5 col-6">
                                            <input type="text" id="quick_search_name" oninput="$('#quick_search_id').val('')" class="form-control form-control-sm form-control-solid @if(lang() =='ar') search_id @endif" placeholder="{{__('Reference ID')}}" name="quick_search_name"  />
                                        </div>
                                    </div>
                                        <!--end::Input group-->
                                        <!--begin::Input group-->
                                        <div class="mb-5">
                                            <!--begin::Radio group-->
                                            <div class="nav-group nav-group-fluid">
                                                <!--begin::Option-->
                                                <label>
                                                    <input type="radio" class="btn-check" name="quick_search_type" value="order" checked="checked" />
                                                    <span class="btn btn-sm btn-color-muted btn-active btn-active-primary">{{__('')}}</span>
                                                </label>
                                                <!--end::Option-->

                                            </div>
                                            <!--end::Radio group-->
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <a onclick="QuickSearch()" class="btn btn-sm fw-bold btn-primary" >{{__('Search')}}</a>
                                        </div>
                                </div>
                                <!--end::Items-->
                            </div>
                        </div>
                    </div>
                    <!--end::Menu-->


                </div>
                <!--end::Search-->
            </div>



            <div class="app-navbar-item ms-2 ms-lg-6" id="kt_header_user_menu_toggle">
                <!--begin::Menu wrapper-->
                <div class="cursor-pointer symbol symbol-circle symbol-30px symbol-lg-45px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                    <img src="assets/media/avatars/blank.png" alt="user" />
                </div>
                <!--begin::User account menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <div class="menu-content d-flex align-items-center px-3">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-50px me-5">
                                <img alt="Logo" src="assets/media/avatars/blank.png" />
                            </div>
                            <!--end::Avatar-->
                            <!--begin::Username-->
                            <div class="d-flex flex-column">
                                <div class="fw-bold d-flex align-items-center fs-5">{{auth()->user()->name}}</div>
                                <div class="fw-bold d-flex align-items-center fs-5"><span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">{{auth()->user()->permission_group->name}}</span></div>

                                <a  class="fw-semibold text-muted text-hover-primary fs-7">{{auth()->user()->email}}</a>
                            </div>
                            <!--end::Username-->
                        </div>
                    </div>
                    <!--end::Menu item-->

                    <!--begin::Menu item-->
                    <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                        <a href="#" class="menu-link px-5">
											<span class="menu-title position-relative">{{__('Mode')}}
											<span class="ms-5 position-absolute translate-middle-y top-50 end-0">
												<i class="ki-outline ki-night-day theme-light-show fs-2"></i>
												<i class="ki-outline ki-moon theme-dark-show fs-2"></i>
											</span></span>
                        </a>
                        <!--begin::Menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3 my-0">
                                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
													<span class="menu-icon" data-kt-element="icon">
														<i class="ki-outline ki-night-day fs-2"></i>
													</span>
                                    <span class="menu-title">{{__('Light')}}</span>
                                </a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3 my-0">
                                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
													<span class="menu-icon" data-kt-element="icon">
														<i class="ki-outline ki-moon fs-2"></i>
													</span>
                                    <span class="menu-title">{{__('Dark')}}</span>
                                </a>
                            </div>
                            <!--end::Menu item-->

                        </div>
                        <!--end::Menu-->
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                        <a href="#" class="menu-link px-5">
											<span class="menu-title position-relative">{{__('Language')}}
											<span class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">
                                                @if(lang() == 'ar') عربي @else  English @endif

											<img class="w-15px h-15px rounded-1 ms-2"
                                                 @if(lang() == 'ar') src="assets/media/misc/saudi-arabia.svg" @else src="assets/media/misc/united-states.svg" @endif alt="" />

                                            </span></span>
                        </a>
                        <!--begin::Menu sub-->
                        <div class="menu-sub menu-sub-dropdown w-175px py-4">


                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="{{url()->current()}}?lang=ar&backByLanguage=true"  class="menu-link d-flex px-5 @if(lang() == 'ar') active @endif">
												<span class="symbol symbol-20px me-4">
													<img class="rounded-1" src="assets/media/misc/saudi-arabia.svg" alt="" />
												</span>عربي</a>
                            </div>
                            <!--end::Menu item-->

                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="{{url()->current()}}?lang=en-gb&backByLanguage=true" class="menu-link d-flex px-5 @if(lang() == 'en-gb') active @endif">
												<span class="symbol symbol-20px me-4">
													<img class="rounded-1" src="assets/media/misc/united-states.svg" alt="" />
												</span>English</a>
                            </div>
                            <!--end::Menu item-->

                        </div>
                        <!--end::Menu sub-->
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-5 my-1">
                        <a href="{{route('system.user.show-profile')}}" class="menu-link px-5">{{__('Profile')}}</a>
                    </div>
                    <div class="menu-item px-5 my-1">
                        <a href="{{route('system.user.user-sessions')}}" class="menu-link px-5">{{__('Auth Sessions')}}</a>
                    </div>
                    <!--end::Menu item-->

                </div>
                <!--end::User account menu-->
                <!--end::Menu wrapper-->
            </div>
            <!--end::User menu-->
            <!--begin::Action-->
            <div class="app-navbar-item ms-2 ms-lg-6 me-lg-6">
                <!--begin::Link-->
                <a href="{{route('logout')}}" title="{{__('Logout')}}" class="btn btn-icon btn-custom btn-color-gray-600 btn-active-color-primary w-35px h-35px w-md-40px h-md-40px">
                    <i class="ki-outline ki-exit-{{ lang() == 'ar'?'left':'right'  }} fs-1"></i>
                </a>
                <!--end::Link-->
            </div>
            <!--end::Action-->

        </div>
        <!--end::Navbar-->
    </div>
    <!--end::Header main-->
{{--    <!--begin::Separator-->--}}
{{--    <div class="app-header-separator"></div>--}}
{{--    <!--end::Separator-->--}}
</div>
<!--end::Header-->

