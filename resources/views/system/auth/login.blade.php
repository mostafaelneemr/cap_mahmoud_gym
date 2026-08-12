<!DOCTYPE html>

<html lang="en">
<!--begin::Head-->

<head>
    <base href="../../../">
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

<body id="kt_body"
    class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
    <!--begin::Main-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Login-->
        <div class="login login-3 login-signin-on d-flex flex-column flex-lg-row flex-row-fluid bg-white" id="kt_login">
            <!--begin::Aside-->
            <div class="login-aside d-flex flex-row-auto bgi-size-cover bgi-no-repeat p-10 p-lg-10"
                style="background-image: url('{{('assets/media/bg/bg-4.jpg')}}');">
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
                        <p class="font-weight-lighter text-white opacity-80">"Mahmoud Shaltout extends his warmest
                            regards and welcomes you with great honor."</p>
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
                            'method' => 'POST',
                            ]) !!}
                            <!--begin::Form-->
                            <div id="form-alert-message">
                                @if(session('error'))
                                    <div class="alert alert-danger font-weight-bold p-4 mb-5 text-center" style="font-size: 1.05rem; border-radius: 8px;">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                @if(session('status'))
                                    <div class="alert alert-success font-weight-bold p-4 mb-5 text-center" style="font-size: 1.05rem; border-radius: 8px;">
                                        {{ session('status') }}
                                    </div>
                                @endif
                            </div>
                            <div id="form-alert-message"></div>
                            <div class="originalForm">

                                <!--begin::Input group=-->
                                <div class="fv-row mb-8">
                                    <!--begin::Email-->
                                    {!! Form::text('email', old('email') ? old('email') : '', ['class' => 'form-control
                                    bg-transparent',
                                    'placeholder' => __('E-Mail'),
                                    ]) !!}
                                    <div class="invalid-feedback" id="email-form-error"></div>
                                    <!--end::Email-->
                                </div>
                                <!--end::Input group=-->
                                <div class="fv-row mb-3">
                                    <!--begin::Password-->
                                    {!! Form::password('password', ['class' => 'form-control bg-transparent','id' =>
                                    'password',
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
                                <button type="submit" class="show-qrCode btn btn-primary er fs-6 px-8 py-4"
                                    data-bs-toggle="modal">
                                    <span class="indicator-label">{{__('Sign In')}}</span>
                                    <span class="indicator-progress">{{__('Please wait ...')}}
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                                <!--end::Action-->
                            </div>


                            {!! Form::close() !!}

                            <div class="text-center my-6">
                                <span class="text-muted font-weight-bold fs-7 px-3 bg-white" style="position: relative; z-index: 1;">{{ __('OR (Trainee Login)') }}</span>
                                <hr style="margin-top: -10px; border-color: #e4e6ef;">
                            </div>
                            <div class="d-grid">
                                <a href="{{ route('auth.google') }}" class="btn btn-outline btn-outline-primary font-weight-bold font-size-h6 px-8 py-4 d-flex align-items-center justify-content-center gap-3" style="border-radius: 8px; border-width: 2px;">
                                    <svg width="22" height="22" viewBox="0 0 48 48">
                                        <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                                        <path fill="#FF3D00" d="m6.306 14.691 6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                                        <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
                                        <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
                                    </svg>
                                    <span>{{ __('Continue with Google') }}</span>
                                </a>
                            </div>

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
                                <input class="form-control form-control-solid h-auto py-5 px-6" type="email"
                                    placeholder="Email" name="email" autocomplete="off" />
                            </div>
                            <div class="form-group d-flex flex-wrap flex-center">
                                <button type="button" id="kt_login_forgot_submit"
                                    class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-4">Submit</button>
                                <button type="button" id="kt_login_forgot_cancel"
                                    class="btn btn-light-primary font-weight-bold px-9 py-4 my-3 mx-4">Cancel</button>
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
    <script>
        var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 },
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
