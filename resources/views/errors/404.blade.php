<!DOCTYPE html>

<html lang="en">

<!--begin::Head-->
<head>
    <meta charset="utf-8"/>
    <title>{{__('404')}} </title>

    <meta name="description" content="">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700"/>
    <!--end::Fonts-->


    <base href="{{asset('')}}">
    <link rel="shortcut icon" href="logo/favicon.ico"/>
    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="assets/plugins/custom/datatables/datatables.bundle{{direction()}}.css" rel="stylesheet"
          type="text/css"/>
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="assets/plugins/global/plugins.bundle{{direction()}}.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/style.bundle{{direction()}}.css" rel="stylesheet" type="text/css"/>

    <link href="assets/css/custom.css" rel="stylesheet"/>
    <!--end::Global Stylesheets Bundle-->
    {{--    <script type="text/javascript" src="../resources/lang/ar.json"></script>--}}
    @if(lang() == 'ar')
        <style>
            @font-face {
                font-display: swap;
                font-family: DINNextLTW23 ;
                src: url('{{asset('/assets/fonts/DINNextLTW23.ttf')}}');

            }
            body{
                font-family: 'DINNextLTW23' !important;
            }
            .select2-results__option.select2-results__option--selected:after{
                left: 1.25rem !important;
                right: unset !important;
            }
        </style>
    @endif


    <!--begin::Javascript-->
    <script type="application/javascript">
        var hostUrl = "assets/";
        var $global_lang = '{{lang()}}';
        const globalTranslations = <?php include(resource_path().'/lang/ar.json') ?>;
    </script>


    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Vendors Javascript(used for this page only)-->
    <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <!--end::Vendors Javascript-->

    <script src="assets/js/custom.js"></script>

</head>
<body id="kt_body" class="app-blank bgi-size-cover bgi-position-center bgi-no-repeat">

<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>

<div class="d-flex flex-column flex-root" id="kt_app_root">
    <div class="d-flex flex-column flex-center flex-column-fluid">

        <div class="d-flex flex-column flex-center text-center">
            <div class="card-body">
                <!--begin::Title-->
                <h1 class="fw-bolder fs-2hx text-gray-900 ">{{__("Sorry!")}}</h1>
                <!--end::Title-->
                <!--begin::Text-->
                <div class="fw-semibold fs-6 text-gray-500">{{__("Page you are looking for could not be found.")}}</div>
                <!--end::Text-->
                <!--begin::Illustration-->
                <div class="">
                    <img src="assets/media/auth/404-error.png" class="mw-100 mh-300px theme-light-show" alt="" />
                    <img src="assets/media/auth/404-error-dark.png" class="mw-100 mh-300px theme-dark-show" alt="" />
                </div>

                <div class="mb-0">
                    <a href="{{route('system.dashboard')}}" class="btn btn-sm btn-primary">{{__('Home')}}</a>
                </div>
            </div>
        </div>
    </div>
    <!--end::Authentication - Signup Welcome Message-->
</div>
<!--end::Root-->
<!--begin::Javascript-->
<script>var hostUrl = "assets/";</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="assets/plugins/global/plugins.bundle.js"></script>
<script src="assets/js/scripts.bundle.js"></script>
<!--end::Global Javascript Bundle-->
<!--end::Javascript-->
</body>
<!--end::Body-->
</html>
