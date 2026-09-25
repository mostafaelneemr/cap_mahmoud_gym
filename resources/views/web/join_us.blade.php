@extends('web.layout')

@section('style')

    <style>
        .nav-links a.active-link {
            background-color: #9e0000;
            color: #ffffff;
            padding: 6px 18px;
            border-radius: 20px;
            font-weight: 600;
        }

        .user-profile {
            background-color: #a80000;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }

        .hero-section {
            background-image: linear-gradient(90deg, rgba(13, 13, 13, 0.95) 0%, rgba(13, 13, 13, 0.4) 60%, rgba(13, 13, 13, 0.1) 100%), url('images/hero-man.png');
            background-position: right center;
            background-repeat: no-repeat;
            background-size: contain;
            margin: 0 20px 30px 20px;
            border-radius: 24px;
            padding: 60px;
            display: flex;
            align-items: center;
            border: 1px solid #1a1a1a;
            position: relative;
            overflow: hidden;
            min-height: 380px;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 8%;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(168, 0, 0, 0.28), transparent 70%);
            transform: translateY(-50%);
            filter: blur(10px);
            pointer-events: none;
        }

        .hero-section::after {
            content: "";
            position: absolute;
            bottom: -60px;
            right: 12%;
            width: 140px;
            height: 140px;
            border: 2px solid rgba(168, 0, 0, 0.35);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-content {
            max-width: 480px;
            z-index: 2;
        }

        .hero-content p {
            color: #cccccc;
            font-size: 14px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background-color: #a80000;
            color: #ffffff;
            padding: 12px 26px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #c90000;
        }

        /* --- Form Section --- */
        .join-section {
            padding: 10px 40px 40px 40px;
            position: relative;
        }

        .section-title {
            text-align: center;
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 35px;
            color: #a80000;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 1;
        }

        .join-section::before {
            content: "";
            position: absolute;
            top: 10px;
            left: 20px;
            width: 160px;
            height: 160px;
            background: radial-gradient(circle, rgba(168, 0, 0, 0.18), transparent 70%);
            filter: blur(8px);
            pointer-events: none;
            z-index: 0;
        }

        .form-container-card {
            background-color: #616161;
            border-radius: 24px;
            padding: 40px;
            max-width: 850px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            box-shadow: 0 20px 40px rgba(168, 0, 0, 0.12);
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 18px;
        }

        .form-group label {
            font-size: 13px;
            margin-bottom: 8px;
            color: #111111;
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea {
            background-color: #d9d9d9;
            border: none;
            border-radius: 20px;
            padding: 14px 20px;
            font-size: 14px;
            color: #000000;
            outline: none;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            background-color: #ffffff;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #777777;
        }

        .form-group textarea {
            resize: none;
            border-radius: 16px;
        }

        .btn-submit {
            width: 100%;
            background-color: #9e0000;
            color: #ffffff;
            border: none;
            padding: 16px;
            border-radius: 25px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .btn-submit:hover {
            background-color: #c90000;
        }

        /* --- Send Method Choice Modal --- */
        .send-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .send-modal-overlay.open {
            display: flex;
        }

        .send-modal-box {
            background-color: #1a1a1a;
            border: 1px solid #333333;
            border-radius: 20px;
            padding: 30px;
            max-width: 360px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
        }

        .send-modal-box h3 {
            color: #ffffff;
            font-size: 18px;
            margin-bottom: 22px;
            font-weight: 600;
        }

        .send-modal-actions {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .send-modal-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px;
            border-radius: 10px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            color: #ffffff;
            transition: transform 0.15s ease, background-color 0.2s ease;
        }

        .send-modal-btn:hover {
            transform: translateY(-2px);
        }

        .send-modal-btn.whatsapp-btn {
            background-color: #25D366;
        }

        .send-modal-btn.email-btn {
            background-color: #a80000;
        }

        .send-modal-cancel {
            margin-top: 18px;
            background: transparent;
            border: none;
            color: #999999;
            font-size: 13px;
            cursor: pointer;
            text-decoration: underline;
        }

        /* --- Responsive Queries --- */
        @media (max-width: 992px) {
            .hero-section {
                background-position: center bottom;
                background-size: cover;
                background-image: linear-gradient(180deg, rgba(13, 13, 13, 0.95) 0%, rgba(13, 13, 13, 0.7) 100%), url('images/hero-man.png');
                text-align: center;
                padding: 50px 20px;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {

            .form-grid-2 {
                grid-template-columns: 1fr;
            }

            .form-container-card {
                padding: 25px 20px;
            }

            .footer-top {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .join-section {
                padding: 10px 20px 30px 20px;
            }
        }
    </style>

@endsection


@section('content')

    <section class="hero-section" @if(!empty($joinHero['image'])) style="background-image: linear-gradient(90deg, rgba(13, 13, 13, 0.95) 0%, rgba(13, 13, 13, 0.4) 60%, rgba(13, 13, 13, 0.1) 100%), url('{{ $joinHero['image'] }}');" @endif>
        <div class="hero-content">
            <span class="sub-heading">{{ $joinHero['tagline'] ?? 'GROW STRONGER' }}</span>
            <h1>{!! !empty($joinHero['title']) ? e($joinHero['title']) : 'Your <span class="highlight">Fitness</span> Journey Starts Here' !!}</h1>
            <p>{{ $joinHero['subtitle'] ?? 'Ignite your inner power, break your limits, and start building the strongest version of yourself today.' }}</p>
            <a href="{{ route('web.contact') }}" class="btn-primary">
                {{ $joinHero['button_text'] ?? 'CONTACT US' }} <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>
    </section>

    <section class="join-section">
        <h2 class="section-title">{{ $joinForm['form_title'] ?? 'Ready to Transform' }}</h2>

        <div class="form-container-card">
            <div id="joinAlert" style="display:none; margin-bottom: 20px; padding: 14px 20px; border-radius: 14px; font-size: 14px;"></div>

            <form id="joinForm" action="{{ route('web.join-us.store') }}" method="POST">
                @csrf
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="name">{{ __('Name') }}*</label>
                        <input type="text" name="name" id="name" placeholder="{{ __('Name') }}" required value="{{ old('name') }}">
                    </div>
                    <div class="form-group">
                        <label for="phone">{{ __('Phone') }}*</label>
                        <input type="tel" name="phone" id="phone" placeholder="{{ __('Phone') }}" required value="{{ old('phone') }}">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="age">{{ __('Age') }}</label>
                        <input type="number" name="age" id="age" placeholder="{{ __('Age') }}" value="{{ old('age') }}">
                    </div>
                    <div class="form-group">
                        <label for="country">{{ __('Country') }}</label>
                        <input type="text" name="country" id="country" placeholder="{{ __('Country') }}" value="{{ old('country') }}">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="governorate">{{ __('Governorate') }}</label>
                        <input type="text" name="governorate" id="governorate" placeholder="{{ __('Governorate') }}" value="{{ old('governorate') }}">
                    </div>
                    <div class="form-group">
                        <label for="trainingLevel">{{ __('Training Level') }}</label>
                        <input type="text" name="training_level" id="trainingLevel" placeholder="{{ __('Training Level') }}" value="{{ old('training_level') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="goal">{{ __('What is your online training goal?') }}</label>
                    <input type="text" name="goal" id="goal" placeholder="{{ __('What is your online training goal?') }}" value="{{ old('goal') }}">
                </div>

                <div class="form-group">
                    <label for="injuries">{{ __('Do you have any injuries ?') }}</label>
                    <input type="text" name="injuries" id="injuries" placeholder="{{ __('Do you have any injuries ?') }}" value="{{ old('injuries') }}">
                </div>

                <div class="form-group">
                    <label for="injuryDetails">{{ __('If you have any injuries, what are they and please provide details.') }}</label>
                    <input type="text" name="injury_details" id="injuryDetails" placeholder="{{ __('If you have any injuries, what are they and please provide details.') }}" value="{{ old('injury_details') }}">
                </div>

                <div class="form-group">
                    <label for="reason">{{ __('Why do you want to train with me?') }}</label>
                    <input type="text" name="reason" id="reason" placeholder="{{ __('Why do you want to train with me?') }}" value="{{ old('reason') }}">
                </div>

                <div class="form-group">
                    <label for="routine">{{ __('Write your daily routine in detail') }}</label>
                    <input type="text" name="routine" id="routine" placeholder="{{ __('Write your daily routine in detail') }}" value="{{ old('routine') }}">
                </div>

                <button type="submit" class="btn-submit" id="joinSubmitBtn">{{ __('Submit Application') }}</button>
            </form>
        </div>
    </section>

    <!-- Optional Direct Send Choice Modal -->
    <div class="send-modal-overlay" id="sendChoiceModal">
        <div class="send-modal-box">
            <h3>{{ __('Application Sent! Would you also like to contact directly?') }}</h3>
            <div class="send-modal-actions">
                <a href="{{ $global['social_links']['whatsapp'] ?? 'https://wa.me/201144470845' }}" target="_blank" class="send-modal-btn whatsapp-btn" style="text-decoration:none;">
                    <i class="fa-brands fa-whatsapp"></i> {{ __('Chat via WhatsApp') }}
                </a>
                <a href="{{ route('web.contact') }}" class="send-modal-btn email-btn" style="text-decoration:none;">
                    <i class="fa-solid fa-envelope"></i> {{ __('View Contact Info') }}
                </a>
            </div>
            <button type="button" class="send-modal-cancel" id="closeSendModal">{{ __('Done') }}</button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.getElementById("joinForm");
            const alertBox = document.getElementById("joinAlert");
            const submitBtn = document.getElementById("joinSubmitBtn");
            const modal = document.getElementById("sendChoiceModal");
            const closeModalBtn = document.getElementById("closeSendModal");

            if (closeModalBtn && modal) {
                closeModalBtn.addEventListener("click", () => {
                    modal.classList.remove("open");
                });
            }

            if (form) {
                form.addEventListener("submit", async (e) => {
                    e.preventDefault();
                    submitBtn.disabled = true;
                    submitBtn.innerText = "{{ __('Submitting Application...') }}";

                    const formData = new FormData(form);

                    try {
                        const response = await fetch(form.action, {
                            method: "POST",
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "Accept": "application/json"
                            },
                            body: formData
                        });

                        const resData = await response.json();

                        alertBox.style.display = "block";
                        if (response.ok && resData.status) {
                            alertBox.style.backgroundColor = "rgba(37, 211, 102, 0.15)";
                            alertBox.style.border = "1px solid #25D366";
                            alertBox.style.color = "#25D366";
                            alertBox.innerText = resData.message || "{{ __('Your application has been received! We will contact you shortly.') }}";
                            form.reset();

                            if (modal) {
                                modal.classList.add("open");
                            }
                        } else {
                            alertBox.style.backgroundColor = "rgba(168, 0, 0, 0.2)";
                            alertBox.style.border = "1px solid #a80000";
                            alertBox.style.color = "#ff6b6b";
                            alertBox.innerText = resData.message || "{{ __('Please check your input and try again.') }}";
                        }
                    } catch (err) {
                        form.submit(); // fallback to standard form submit
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.innerText = "{{ __('Submit Application') }}";
                    }
                });
            }
        });
    </script>
@endsection




