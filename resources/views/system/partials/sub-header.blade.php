@yield('sub_header')


<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <!--begin::Toolbar container-->
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <!--begin::Toolbar wrapper-->
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">

            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
            @if(isset($pageTitle))
                <!--begin::Title-->
                <h1 class="page-heading d-flex flex-column justify-content-center text-dark fw-bold fs-3 m-0">{!!$pageTitle!!}</h1>
                <!--end::Title-->
            @endif
                @if(isset($breadcrumb))
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        @foreach($breadcrumb as $key => $value)
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a @if(isset($value['url'])) href="{!!url($value['url'])!!}" @endif  class="text-muted @if(isset($value['url'])) text-hover-primary @endif">{!!$value['text']!!}</a>
                            </li>
                            <!--end::Item-->
                            @if(isset($breadcrumb[$key+1]))
                                <!--begin::Item-->
                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                </li>
                                <!--end::Item-->
                            @endif
                        @endforeach
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a     class="text-muted  ">{!! @$pageTitle!!}</a>
                        </li>
                    </ul>
                    <!--end::Breadcrumb-->
                @endif
            </div>
            <!--end::Page title-->

            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @yield('links')
                @yield('plus-sub-header-btn')
                @if(isset($addButton))
                    {!!add_links(route($addButton['route']),$addButton['route'] ,!empty($addButton['text'])?$addButton['text']:'')!!}
                @endif
                @if(isset($addModalIcon))
                    {!!link_add_modal_icon($addModalIcon['route'],$addModalIcon['modalId'],(isset($addModalIcon['title']) ? $addModalIcon['title'] : null))!!}
                @endif
                @if(isset($editModalButton))
                    {!!$editModalButton!!}
                @endif
                @if(isset($addModalButton))
                    @foreach($addModalButton as $key => $modalButton)
                        {!!link_modal($modalButton['route'],$modalButton['title'],$modalButton['modalId'],$modalButton['icon'])!!}
                    @endforeach

                @endif

                    @if(isset($showAdvancedFilter) && $showAdvancedFilter)
                        {!!filter_btn()!!}
                    @endif
                    @if(isset($showDownloadExcel) && $showDownloadExcel == true)
                    @include('system.partials.buttons.download_btn')
                  @endif
               @yield('sub-header-btns')
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar wrapper-->
    </div>
    <!--end::Toolbar container-->
</div>
<!--end::Toolbar-->
