<!DOCTYPE html>

<html @if(lang() == 'ar') lang="ar" direction="rtl" style="direction: rtl;" @else  lang="en" direction="ltr" @endif>

<!-- begin::Head -->
<head>
    <meta charset="utf-8"/>
    <title>{{(isset($pageTitle))?$pageTitle:__(ucfirst(request()->route()->getActionMethod()))}} </title>

    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="ajax-post" content="{{route('system.misc.ajax')}}">
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700"/>
    <!--end::Fonts-->


    <base href="{{asset('')}}">
    <link rel="shortcut icon" href="assets/web/img/icons/logo.ico"/>
    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="assets/plugins/custom/datatables/datatables.bundle{{direction()}}.css" rel="stylesheet"
          type="text/css"/>
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="assets/plugins/global/plugins.bundle{{direction()}}.css?v=1.0" rel="stylesheet" type="text/css"/>
    <link href="assets/css/style.bundle{{direction()}}.css?v=1.1" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="assets/css/magnificant_popup.css"/>
    <link href="assets/css/custom.css?v={{time()}}" rel="stylesheet"/>
     @if(lang() =='ar')
        <link href="assets/css/custom-ar.css?v={{time()}}" rel="stylesheet"/>
     @endif
     <link href="assets/plugins/custom/summernote/summernote-lite.min.css" rel="stylesheet">
    <!--end::Global Stylesheets Bundle-->
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
            .select2-container--bootstrap5 .select2-selection__clear{
                left: 3rem !important;
                right: unset !important;
            }
            button.select2-selection__choice__remove {
                left: 5px;
                right: unset;
            }
        </style>
    @endif

    <link rel="stylesheet" href="assets/css/intlTelInput.css"/>
    <link rel="stylesheet" href="assets/css/intlTellStyle.ltr.css"/>
    @if(lang() == 'ar')
        <link rel="stylesheet" href="assets/css/intlTellStyle.rtl.css"/>
    @endif
<!--begin::Javascript-->
<script type="application/javascript">
    var hostUrl = "assets/";
    var SystemBaseUrl = "{{env('APP_URL')}}/system/";
    var $global_lang = '{{lang()}}';
   const globalTranslations = <?php include(resource_path().'/lang/ar.json') ?>;
</script>

    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js?v=1.0"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Vendors Javascript(used for this page only)-->
    <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <!--end::Vendors Javascript-->

    <script src="assets/js/magnificant_popup.js?v=1.0"></script>
    <script src="assets/js/custom.js?v={{time()}}"></script>
{{--    <script src="assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>--}}

    @php
        $auth = \Illuminate\Support\Facades\Auth::user();
    @endphp

    @yield('header')

    @if(env('APP_ENV') != 'production')

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-9P1L9R01GG"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-9P1L9R01GG');
    </script>
    @endif
</head>
<!--end::Head-->
@yield('filter')
<!--begin::Page loading(append to body)-->
<div class="page-loader flex-column bg-dark bg-opacity-25">
    <span class="spinner-border text-primary" role="status"></span>
    <span class="text-gray-800 fs-6 fw-semibold mt-5">{{__('Loading...')}}</span>
</div>
<!--end::Page loading-->

<!--begin::Body-->
<body id="kt_app_body" data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true"
      data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
      data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true"
      class="app-default"  @if(lang() == 'ar') lang="ar" direction="rtl" style="direction: rtl;" @else  lang="en" direction="ltr" @endif>


<!--begin::Theme mode setup on page load-->
<script>var defaultThemeMode = "light";
    var themeMode;
    if (document.documentElement) {
        if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
            themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
        } else {
            if (localStorage.getItem("data-bs-theme") !== null) {
                themeMode = localStorage.getItem("data-bs-theme");
            } else {
                themeMode = defaultThemeMode;
            }
        }
        if (themeMode === "system") {
            themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        }
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    }</script>

<!--end::Theme mode setup on page load-->
<!--begin::App-->
<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
    <!--begin::Page-->
    <div class="app-page flex-column flex-column-fluid" id="kt_app_page">

        @include('system.partials.header')

        <!--begin::Wrapper-->
        <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

            @include('system.partials.aside_menu')

            <!--begin::Main-->
            <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                <!--begin::Content wrapper-->
                <div class="d-flex flex-column flex-column-fluid responsive-correction" >
                    @include('system.partials.sub-header')

                    <!--begin::Content-->
                    <div id="kt_app_content" class="app-content flex-column-fluid">
                        <!--begin::Content container-->
                        <div id="kt_app_content_container" class="app-container container-fluid">
                            @stack('filter')
                            @yield('content')
                        </div>
                        <!--end::Content container-->
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Content wrapper-->
                @include('system.partials.footer')
            </div>
            <!--end:::Main-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Page-->
</div>
<!--end::App-->
<!--begin::Drawers-->

<div class="alert-error-disable">

    <div
        class="alert alert-dismissible bg-light-danger border border-danger border-3 d-flex flex-column flex-sm-row p-5 mb-10">
        <!--begin::Icon-->
        <i class="ki-duotone ki-information-3  fs-2hx text-danger me-4 mb-5 mb-sm-0"><span class="path1"></span><span
                class="path2"></span><span class="path3"></span></i>
        <!--end::Icon-->
        <!--begin::Wrapper-->
        <div class="d-flex flex-column pe-0 pe-sm-10">
            <!--begin::Title-->
            <h5 class="mb-1" id="div_title"></h5>
            <!--end::Title-->
            <!--begin::Content-->
            <span id="div_message"></span>
            <!--end::Content-->
        </div>
        <!--end::Wrapper-->
        <!--begin::Close-->
        <button type="button"
                class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto"
                data-bs-dismiss="alert">
            <i class="ki-duotone ki-cross fs-1 text-danger"><span class="path1"></span><span class="path2"></span></i>
        </button>
        <!--end::Close-->
    </div>
</div>
<div class="alert-success-disable">
    <!--begin::Alert-->
    <div
        class="alert alert-dismissible bg-light-primary border-3 border-primary d-flex flex-column flex-sm-row p-5 mb-10">
        <!--begin::Icon-->
        <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
        </i>
        <!--end::Icon-->
        <!--begin::Wrapper-->
        <div class="d-flex flex-column pe-0 pe-sm-10">
            <!--begin::Title-->
            <h5 class="mb-1" id="div_title"></h5>
            <!--end::Title-->
            <!--begin::Content-->
            <span id="div_message"></span>
            <!--end::Content-->
        </div>
        <!--end::Wrapper-->
        <!--begin::Close-->
        <button type="button"
                class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto"
                data-bs-dismiss="alert">
            <i class="ki-duotone ki-cross fs-1 text-success"><span class="path1"></span><span class="path2"></span></i>
        </button>
        <!--end::Close-->
    </div>
    <!--end::Alert-->

</div>


<!-- Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" data-bs-keyboard="false" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel"> {{__('Reset Password')}}</h5>
            </div>
            {!! Form::open(['id'=>'reset-password-form','onsubmit' => 'FormSubmit("'.route('system.reset-password') .'","reset-password-form");return false;','class'=>'form','name'=>'reset-password-form','method' =>  'POST']) !!}
            <div class="modal-body">
                <div class="error-div">

                </div>
                <div class="row">
                    <div class="col-12">
                        {{ label(__('New Password'),'required') }}
                        <div class="mb-5">
                            {!! Form::password('password',null,['id'=>'password','class'=>'form-control form-control-solid','data-placeholder' => __('New Password')]) !!}
                            <div class="invalid-feedback" id="password-form-error"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        {{ label(__('Confirm New Password'),'required') }}
                        <div class="mb-5">
                            {!! Form::password('password_confirmation',null,['id'=>'confirm_new_password','class'=>'form-control form-control-solid','data-placeholder' => __('Confirm New Password')]) !!}
                            <div class="invalid-feedback" id="confirm_new_password-form-error"></div>
                        </div>
                    </div>
                    <div class="order-inputs"></div>
                </div>
            </div>
            <div class="modal-footer">
                <!--begin::Button-->
                <button type="submit" class="btn btn-primary sub-btn" >
                    <span class="indicator-label">{{  __('Update')}}</span>
                    <span class="indicator-progress">{{__('Please wait')}}...
                           <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
                <!--end::Button-->
            </div>
         </form>
            {!! Form::close() !!}

        </div>
    </div>
</div>

<!--begin::Scrolltop-->
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-outline ki-arrow-up"></i>
</div>
<!--end::Scrolltop-->
<script src="assets/js/intlTelInput-jquery-{{opencart_lang()}}.js"></script>
<script src="{{asset('assets/plugins/custom/summernote/summernote-lite.min.js')}}"></script>
<script src="{{asset('assets/plugins/custom/summernote/summernote-ar-AR.js')}}"></script>
<script>
     function text_editor(className,lang_id=1){
        var lang =  'en-US';
        @if(languageId() == 2)
          lang =  'ar-AR';
        @endif
        var direction = 'ltr';
        if(lang_id == 2){
            direction = 'rtl';
        }

         $('.'+className).summernote({
           callbacks: {
               onInit: function(e) {
                   $(this).next().find('.note-editing-area').attr('dir', direction);
               }

           },
           disableDragAndDrop: true,
           lang: lang, // default: 'en-US'
           height: 300,
               toolbar: [
               ['style', ['style']],
               ['font', ['bold', 'underline', 'clear']],
               ['color', ['color']],
               ['para', ['ul', 'ol', 'paragraph']],
               ['table', ['table']],
               ['insert', ['link' /*, 'image', 'video'*/]],
               ['view', [/*'fullscreen',*/ 'codeview', 'help']]
           ]
       });




    }

    function resetForm(formName) {
        $(`form[name="${formName}"]`).trigger('reset');
    }

    function setFormAttributes(route, method, formName) {
        let formSelector = $(`form[name="${formName}"]`);
        $('#patch_method').val(method);
        formSelector.attr('onSubmit', `FormSubmit('${route}', '${formName}');return false;`);
    }
</script>

<script>


    $('select').not('.not-select').select2({
        "language": {
            "inputTooShort": function(){
                return __('Please enter 1 or more characters');
            },
            "noResults": function(){
                return __('No Results Found');
            }
        },
        @if(lang() == 'ar')
        dir: "rtl"
        @endif
    });

    @if(request()->session()->get('msg'))
    notify("{{ request()->session()->get('msg') }}", "{{ request()->session()->get('type') }}")
    @endif
    $(function () {


            var minDate = $('.dp').data('mindate');
            var minDateValue = null;
            if(minDate === true){
                 minDateValue = moment();
            }

            var maxDate = $('.dp').data('maxdate');
            var maxDateValue = new Date();
            if (maxDate == true){
                maxDateValue = '';
            }

            var formate_type = $('.dp').data('format');
        $('.dp').daterangepicker({
            autoUpdateInput: false,
            todayHighlight: true,
            singleDatePicker: true,
            autoClose: true,
            autoApply: true,
            showDropdowns: true,
            minDate:minDateValue,
            minYear: 2017,
            timePicker:formate_type == 'datetime' ? true : false,
            maxYear: parseInt(moment().format('YYYY'), 11),
            maxDate: maxDateValue,
            locale: {
                cancelLabel: 'Clear',
                format:formate_type == 'datetime' ? 'YYYY-MM-DD HH:mm:ss' : 'YYYY-MM-DD'
            }
        });
        $('.dp').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(formate_type == 'datetime' ?  picker.startDate.format('YYYY-MM-DD HH:mm:ss') : picker.startDate.format('YYYY-MM-DD'));
        });
        $('.dp').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });



    });



    function QuickSearch(){
        var search_id = document.getElementById('quick_search_id').value;
        var search_name = document.getElementById('quick_search_name').value;

        switch ($('input[name="quick_search_type"]:checked').val()) {
            case 'order':
                var name_key = 'reference_id';
                break;

            default:
                break;
        }

        if (search_id == '' && search_name == '') {
            document.getElementById("quick_search_id").style.borderColor = "red";
            document.getElementById("quick_search_name").style.borderColor = "red";
            return;
        }

        if (search_id && !search_name) {
            var url = $('input[name="quick_search_type"]:checked').val() + '/' + $("#quick_search_id").val();
        } else if (!search_id && search_name) {
            var url = $('input[name="quick_search_type"]:checked').val() + '?' + name_key + '=' + $(
                "#quick_search_name").val();
        }

        window.location.href = SystemBaseUrl  + url

    }

        var search_id = document.getElementById("quick_search_id");
        search_id.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                QuickSearch();
            }

        });

        var search_name = document.getElementById("quick_search_name");
        search_name.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                QuickSearch();
            }
        });


    function showDetails($route){
        $.ajax({
            url: $route,
            type: "GET",
            dataType: "json",
            success: function (data) {
                const show_data = document.getElementById("show_data");
                show_data.innerHTML = data;
                $('#modal-details').modal('show');
            },
            error: function (err) {

            }
        })
    }
</script>

@if(env('APP_ENV') == 'local')
<script>
    $('body').on('xhr.dt', function (e, settings, data, xhr) {
        if (typeof phpdebugbar != "undefined") {
            if (xhr.getAllResponseHeaders()) {
                phpdebugbar.ajaxHandler.handle(xhr);
            }
        }
    });
</script>
@endif

<script>
    $('select').not('.not-select2').select2({
        "language": {
            "inputTooShort": function () {
                return __('Please enter 1 or more characters');
            },
            "noResults": function () {
                return __('No Results Found');
            }
        },
        @if(lang() == 'ar')
        dir: "rtl"
        @endif
    });
    const x = $(".add_telephone").intlTelInput({
        onlyCountries: ['sa', 'kw', 'ae', 'bh', 'om', 'qa'],
        initialCountry: '{{ $code ?? 'sa' }}',
        separateDialCode: true,
        formatOnDisplay: false,
        autoHideDialCode: false
    });
    $(".add_telephone").on("countrychange", function () {

        var country_code = $(".add_telephone").intlTelInput("getSelectedCountryData").dialCode;
        $("input[name='telephone_code']").val(country_code);
    });

    function check_telephone() {
        var phone = $("#telephone").val();
        var country_codes = ['973', '965', '968', '966', '971'];

        if (phone != '') {
            phone = phone.replace(/\D/g, '')
            if (phone.substring(0, 1) == 0) {
                $("#telephone").val(phone.substring(1));
                check_telephone()
                return;
            }
            if (country_codes.includes(phone.substring(0, 3))) {
                $("#telephone").val(phone.substring(3));
                check_telephone()
                return;
            }
            $("#telephone").val(phone);

        }
    }

    $(document).ready(function() {
        @if(session('force_password_reset'))
        $("#resetPasswordModal").modal("show");
        @else
        console.log('Force password reset session is not set.');
        @endif

        $('#resetPasswordModal').on('hide.bs.modal', function () {
            let passwordReset = false; // This should be set based on your form logic

            if (!passwordReset) {
                setTimeout(function() {
                    $('#resetPasswordModal').modal('show');
                }, 10000);
            }
        });

    });

</script>

@yield('footer')

</body>
</html>
