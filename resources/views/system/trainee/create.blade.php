@extends('system.layout')
@section('header')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css"
          integrity="sha512-gxWow8Mo6q6pLa1XH/CcH8JyiSDEtiwJV78E+D+QP0EVasFs8wKXq16G8CLD4CJ2SnonHr4Lm/yY2fSI2+cbmw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <style>
        .iti {
            display: block;
        }

        @media (min-width: 1200px) and (max-width: 1399px) {
            .container, .container-sm, .container-md, .container-lg, .container-xl {
                max-width: 95%;
            }
        }

        @media (max-width: 991.98px) {
            .card.card-custom > .card-header .card-title, .card.card-custom > .card-header .card-title .card-label {
                font-size: 1rem;
            }
        }

        .iti--separate-dial-code .iti__selected-flag {
            background-color: transparent;
        }

        .iti--allow-dropdown .iti__flag-container:hover .iti__selected-flag {
            background-color: transparent;
        }

        @if(lang() == 'ar')

   /*.iti--allow-dropdown .iti__flag-container, .iti--separate-dial-code .iti__flag-container {*/
        /*          left: 30px !important;*/
        /*      }*/

        input[type=tel] {
            padding-right: 6px;
            padding-left: 80px !important;
            margin-left: 0;
        }

        .iti--separate-dial-code .iti__selected-flag {
            background-color: transparent;
        }

        .iti--allow-dropdown .iti__flag-container:hover .iti__selected-flag {
            background-color: transparent;
        }

        @else
    @endif

    .iti__flag-container {
            direction: ltr;
        }

        .iti__country-list {
            position: relative;
        }

        .wizard-icon-font:before {
            font-weight: bold;
        }

    </style>
@endsection
@section('content')

    {!! Form::open(['id'=>'main-form','onsubmit' =>  isset($result) ? 'FormSubmit("'.route('system.trainee.update',$result->id).'");return false;':'FormSubmit("'.route('system.trainee.store') .'");return false;','method' => isset($result) ?  'PATCH' : 'POST']) !!}
    <div id="form-alert-message"></div>
    <!--begin::Row-->
    <div class="row gx-10 ">
        <!--begin::Col-->
        <div class="col-lg-6">
            {{ label( __('Email'),'required') }}
            <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::email('email',isset($result->id) ? $result->email:old('email'),['class'=>'form-control form-control-solid']) !!}
                <div class="invalid-feedback" id="email-form-error"></div>
            </div>
        </div>
        <!--end::Col-->

        <!--begin::Col-->
        <div class="col-lg-6 ">
            {{ label(__('Name'),'required') }}
            <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::text('name',isset($result->id) ? $result->name:old('name'),['class'=>'form-control form-control-solid']) !!}
                <div class="invalid-feedback" id="name-form-error"></div>
            </div>
        </div>
        <!--begin::Col-->
        <div class="col-lg-6 ">
            {{ label(__('Password'),isset($result) ? '':'required') }}
            <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::password('password', ['class' => 'form-control form-control-solid','id'=>'password']) !!}
                <div class="invalid-feedback" id="password-form-error"></div>
            </div>

        </div>

        <div class="col-lg-6">
            {{ label( __('Confirm Password'),isset($result) ? '':'required') }}
            <div class="mb-5">
                {!! Form::password('password_confirmation', ['class' => 'form-control form-control-solid','id'=>'password_confirmation']) !!}
                <div class="invalid-feedback" id="password_confirmation-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('Membership Start'),'required') }}
            <div class="mb-5">
                {!! Form::text('membership_start',isset($result->id) ? $result->membership_start:old('membership_start'),
                        ['class'=>'form-control form-control-solid dp']) !!}
                <div class="invalid-feedback" id="membership_start-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('Membership End'),'required') }}
            <div class="mb-5">
                {!! Form::text('membership_end',isset($result->id) ? $result->membership_end:old('membership_end'),
                    ['class'=>'form-control form-control-solid dp']) !!}
                <div class="invalid-feedback" id="membership_end-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            <input type="hidden" name="telephone_code" value="{{$telephone_code}}">
            {{ label(__('Telephone')) }}
            <div class="mb-5">
                {!! Form::tel('telephone', isset($result) ?$telephone:null,['class'=>'form-control form-control-solid valid_telephone','id'=>'telephone']) !!}
                <div class="invalid-feedback" id="telephone-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('Status'),'required') }}
            <!--begin::Input group-->
            <div class="mb-5">
                {!! Form::select('status',status_select_data(),isset($result->id) ? $result->status:old('status'),['class'=>'form-select form-select-solid','id'=>'status',' data-placeholder'=>__('Select an option')]) !!}
                <div class="invalid-feedback" id="status-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('Age'),'required') }}
            <div class="mb-5">
                {!! Form::number('age',isset($result->id) ? $result->age:old('age'),['class'=>'form-control form-control-solid']) !!}
                <div class="invalid-feedback" id="age-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('Weight'),'required') }}
            <div class="mb-5">
                {!! Form::number('weight',isset($result->id) ? $result->weight:old('weight'),['class'=>'form-control form-control-solid']) !!}
                <div class="invalid-feedback" id="weight-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('Height'),'required') }}
            <div class="mb-5">
                {!! Form::number('height',isset($result->id) ? $result->height:old('height'),['class'=>'form-control form-control-solid']) !!}
                <div class="invalid-feedback" id="height-form-error"></div>
            </div>
        </div>

        <div class="col-lg-6 ">
            {{ label(__('Training Level'),'required') }}
            <div class="mb-5">
                {!! Form::select('training_level',[''=>'']+training_level(),isset($result->id) ? $result->training_level:old('training_level'),
                        ['class'=>'form-control form-control-solid','data-placeholder'=>__('Select Training Level')]) !!}
                <div class="invalid-feedback" id="training_level-form-error"></div>
            </div>
        </div>
    </div>
    <!--end::Row-->
    <div class="separator separator-dashed mb-8"></div>

    <button type="submit" class="btn btn-primary submit">
        <span class="indicator-label">{{ isset($result->id)? __('Update') :  __('Create')}}</span>
        <span class="indicator-progress">{{__('Please wait')}}...
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
    </button>

    {!! Form::close() !!}

@endsection

@section('footer')
    <script type="text/javascript">
        function check_telephone() {
            var phone = $("#telephone").val();
            var country_codes = ['973', '965', '968', '966', '971', '+20'];
            console.log(phone.substring(0, 3))

            if (phone != '') {
                phone = phone.replace(/\D/g, '')
                if (phone.substring(0, 1) == 0) {
                    $("#telephone").val(phone.substring(1));
                    check_telephone()
                    return;
                }
                if (country_codes.includes(phone.substring(0, 3))) {
                    console.log(phone.substring(3));
                    $("#telephone").val(phone.substring(3));
                    check_telephone()
                    return;
                }
                $("#telephone").val(phone);

            }
        }

        $("#telephone").intlTelInput({
            onlyCountries: ['sa', 'kw', 'ae', 'bh', 'om', 'eg'],
            initialCountry: '{{$code}}',
            separateDialCode: true,
            formatOnDisplay: false,
            autoHideDialCode: false
        });
        $("#telephone").on("countrychange", function () {

            var country_code = $("#telephone").intlTelInput("getSelectedCountryData").dialCode;
            $("input[name='telephone_code']").val(country_code);
        });


    </script>
@endsection

