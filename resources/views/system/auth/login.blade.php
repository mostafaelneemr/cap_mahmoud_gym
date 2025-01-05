
<!DOCTYPE html>

<html lang="en">
<!--begin::Head-->
<head><base href="../../../">
    <meta charset="utf-8" />
    <title>Mahmoud Shaltout | Login</title>
    <meta name="description" content="Login page example" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!--begin::Fonts-->
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" /> -->
    <!--end::Fonts-->
    <!--begin::Global Theme Styles(used by all pages)-->
    <base href="{{ asset('') }}">
    <link href="assets/plugins/global/plugins.bundle{{ direction() }}.css?v=1.0" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle{{ direction() }}.css?v=1.1" rel="stylesheet" type="text/css" />
    <!--end::Global Theme Styles-->
    <link rel="shortcut icon" href="{{ asset('assets/web/img/icons/logo.ico') }}" />
    <script type="application/javascript">
        var $global_lang ='{{lang()}}';
    </script>
    <!--begin::Page Custom Styles(used by this page)-->
    <link href="{{('assets/css/css/pages/login/login-3.css')}}" rel="stylesheet" type="text/css" />
    <!--end::Page Custom Styles-->


</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
<!--begin::Main-->
<div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-3 login-signin-on d-flex flex-column flex-lg-row flex-row-fluid bg-white" id="kt_login">
        <!--begin::Aside-->
        <div class="login-aside d-flex flex-row-auto bgi-size-cover bgi-no-repeat p-10 p-lg-10" style="background-image: url('{{('assets/media/bg/bg-4.jpg')}}');">
            <!--begin: Aside Container-->
            <div class="d-flex flex-row-fluid flex-column justify-content-between">
                <!--begin: Aside header-->
                <a href="#" class="flex-column-auto mt-5">
                    <img src="assets/media/logos/logo-letter-1.png" class="max-h-70px" alt="" />
                </a>
                <!--end: Aside header-->
                <!--begin: Aside content-->
                <div class="flex-column-fluid d-flex flex-column justify-content-center">
                    <h3 class="font-size-h1 mb-5 text-white">Welcome to Dashboard!</h3>
                    <p class="font-weight-lighter text-white opacity-80">"Mahmoud Shaltout extends his warmest regards and welcomes you with great honor."</p>
                </div>
                <!--end: Aside content-->

            </div>
            <!--end: Aside Container-->
        </div>
        <!--begin::Aside-->
        <!--begin::Content-->
        <div class="flex-row-fluid d-flex flex-column position-relative p-7 overflow-hidden">

            <!--begin::Content body-->
            <div class="d-flex flex-column-fluid flex-center mt-30 mt-lg-0">
                <!--begin::Signin-->
                <div class="login-form login-signin">
                    <div class="text-center mb-10 mb-lg-20">
                        <h3 class="font-size-h1">Sign In</h3>
                        <p class="text-muted font-weight-bold">Enter your username and password</p>
                    </div>
                    <div class="w-lg-500px px-10">
                        {!! Form::open([
                            'id' => 'main-form',
                            'onsubmit' =>'FormSubmit("' . route('login') . '");return false;',
                            'method' =>  'POST',
                        ]) !!}
                        <!--begin::Form-->
                        <div id="form-alert-message"></div>
                        <div class="originalForm">

                            <!--begin::Input group=-->
                            <div class="fv-row mb-8">
                                <!--begin::Email-->
                                {!! Form::text('email', old('email') ? old('email') : '', ['class' => 'form-control bg-transparent',
                                    'placeholder' => __('E-Mail'),
                                ]) !!}
                                <div class="invalid-feedback" id="email-form-error"></div>
                                <!--end::Email-->
                            </div>
                            <!--end::Input group=-->
                            <div class="fv-row mb-3">
                                <!--begin::Password-->
                                {!! Form::password('password', ['class' => 'form-control bg-transparent','id' => 'password',
                                    'placeholder' => __('Password'),
                                ]) !!}
                                <div class="invalid-feedback" id="password-form-error"></div>
                                <!--end::Password-->
                            </div>
                            <!--end::Input group=-->
                        </div>
                        <!--begin::Submit button-->
                        <div class="d-grid login-form">
                            <!--begin::Action-->
                            <button type="submit" class="show-qrCode btn btn-primary er fs-6 px-8 py-4" data-bs-toggle="modal">
                                <span class="indicator-label">{{__('Sign In')}}</span>
                                <span class="indicator-progress">{{__('Please wait ...')}}
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                            <!--end::Action-->
                        </div>
                        {!! Form::close() !!}

                    </div>

                </div>
                <!--end::Signin-->

                <!--begin::Forgot-->
                <div class="login-form login-forgot">
                    <div class="text-center mb-10 mb-lg-20">
                        <h3 class="font-size-h1">Forgotten Password ?</h3>
                        <p class="text-muted font-weight-bold">Enter your email to reset your password</p>
                    </div>
                    <!--begin::Form-->
                    <form class="form" novalidate="novalidate" id="kt_login_forgot_form">
                        <div class="form-group">
                            <input class="form-control form-control-solid h-auto py-5 px-6" type="email" placeholder="Email" name="email" autocomplete="off" />
                        </div>
                        <div class="form-group d-flex flex-wrap flex-center">
                            <button type="button" id="kt_login_forgot_submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-4">Submit</button>
                            <button type="button" id="kt_login_forgot_cancel" class="btn btn-light-primary font-weight-bold px-9 py-4 my-3 mx-4">Cancel</button>
                        </div>
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Forgot-->
            </div>
            <!--end::Content body-->

        </div>
        <!--end::Content-->
    </div>
    <!--end::Login-->
</div>
<!--end::Main-->
<script>
    var hostUrl = "assets/";
</script>
<!--begin::Global Config(global config for global JS scripts)-->
<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 },
        "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#6993FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" },
                "light": { "white": "#ffffff", "primary": "#E1E9FF", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" },
                "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } },
            "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } },
        "font-family": "Poppins" };
</script>
<!--end::Global Config-->
<!--begin::Global Theme Bundle(used by all pages)-->
<script src="{{('assets/plugins/global/plugins.bundle.js')}}"></script>
<script src="{{('assets/js/scripts.bundle.js')}}"></script>

<!--end::Global Theme Bundle-->
<!--begin::Page Scripts(used by this page)-->
<script src="{{('assets/js/login/login.js')}}"></script>
<script src="{{asset('assets/js/custom.js')}}?v={{time()}}"></script>

<!--end::Page Scripts-->
</body>
<!--end::Body-->
</html>
