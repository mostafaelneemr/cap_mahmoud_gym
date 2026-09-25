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
            transition: transform 0.2s ease;
        }

        .user-profile:hover {
            transform: scale(1.05);
        }

        .hero-section {
            background-image: linear-gradient(90deg, rgba(0, 0, 0, 0.94) 0%, rgba(0, 0, 0, 0.82) 32%, rgba(0, 0, 0, 0.4) 60%, rgba(0, 0, 0, 0.15) 100%), url('images/hero-man.png');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            margin: 0 20px 30px 20px;
            border-radius: 24px;
            padding: 70px 60px;
            min-height: 380px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            position: relative;
            overflow: hidden;
            border: 1px solid #1a1a1a;
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
            max-width: 420px;
            z-index: 2;
        }

        .hero-content h1 {
            font-size: 42px;
            margin-bottom: 12px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .hero-content p {
            color: #888888;
            font-size: 15px;
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

        /* --- Contact Section --- */
        .contact-section {
            padding: 20px 40px 40px 40px;
        }

        .section-title {
            text-align: center;
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 40px;
            color: #e6e6e6;
        }


        .contact-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 20px;
        }

        /* Left Side Info Card */
        .info-card {
            background-color: #3a3a3a;
            border-radius: 20px;
            padding: 35px 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(168, 0, 0, 0.25);
        }

        .info-card::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 160px;
            height: 160px;
            background: radial-gradient(circle, rgba(168, 0, 0, 0.25), transparent 70%);
            filter: blur(6px);
            pointer-events: none;
        }

        .info-card h3 {
            font-size: 22px;
            margin-bottom: 35px;
            font-weight: 600;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }


        .icon-circle {
            width: 42px;
            height: 42px;
            background-color: #ffffff;
            color: #000000;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .info-text span {
            display: block;
            font-size: 13px;
            color: #aaaaaa;
            margin-bottom: 2px;
        }

        .info-text p {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .social-links {
            margin-top: 40px;
            display: flex;
            gap: 18px;
        }

        .social-links a {
            color: #ffffff;
            font-size: 22px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: #a80000;
        }

        /* Right Side Form Card */
        .form-card {
            background-color: #616161;
            border-radius: 20px;
            padding: 35px 40px;
        }

        .form-card h3 {
            font-size: 22px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
            color: #ffffff;
        }

        .form-row {
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
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 14px;
            color: #000000;
            outline: none;
            transition: background-color 0.3s ease;
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
        }

        .btn-submit {
            width: 100%;
            background-color: #800000;
            color: #ffffff;
            border: none;
            padding: 16px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: #a80000;
        }

        footer {
            background-color: #400000;
            margin-top: 30px;
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
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .hero-section {
                text-align: center;
                justify-content: center;
                padding: 40px 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .footer-top {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .contact-section {
                padding: 10px 20px 30px 20px;

            }

            .form-card {
                padding: 25px 20px;
            }
        }
    </style>

@endsection

@section('content')

    <section class="hero-section" @if(!empty($contactHero['image'])) style="background-image: linear-gradient(90deg, rgba(0, 0, 0, 0.94) 0%, rgba(0, 0, 0, 0.82) 32%, rgba(0, 0, 0, 0.4) 60%, rgba(0, 0, 0, 0.15) 100%), url('{{ $contactHero['image'] }}');" @endif>
        <div class="hero-content">
            <span class="sub-heading">{{ $contactHero['tagline'] ?? 'GROW STRONGER' }}</span>
            <h1>{!! !empty($contactHero['title']) ? e($contactHero['title']) : 'Contact Us' !!}</h1>
            <p>{{ $contactHero['subtitle'] ?? 'We provide the right fitness and coaching solutions tailored to your personal needs and goals.' }}</p>
            <a href="{{ route('web.join-us') }}" class="btn-primary">
                {{ $contactHero['button_text'] ?? 'Join Us' }} <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>
    </section>

    <section class="contact-section">
        <h2 class="section-title" dir="ltr">{{ $contactData['header_title'] ?? 'Have questions or need help?' }}</h2>

        <div class="contact-grid">
            <div class="info-card">
                <div>
                    <h3>{{ __('Get in Touch') }}</h3>

                    <div class="info-item">
                        <div class="icon-circle">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="info-text">
                            <span>{{ __('Call Us') }}</span>
                            <p>{{ $contactData['get_in_touch']['phone'] ?? ($global['contact']['phone'] ?? '01144470845') }}</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="icon-circle">
                            <i class="fa-solid fa-comment-dots"></i>
                        </div>
                        <div class="info-text">
                            <span>{{ __('Message') }}</span>
                            <p>{{ $contactData['get_in_touch']['message'] ?? ($global['contact']['phone'] ?? '01144470845') }}</p>
                        </div>
                    </div>
                </div>

                <div class="social-links">
                    @if(!empty($global['social_links']['instagram']))
                        <a href="{{ $global['social_links']['instagram'] }}" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    @else
                        <a href="https://www.instagram.com/mahmoudshaltout2823" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    @endif

                    @if(!empty($global['social_links']['whatsapp']))
                        <a href="{{ $global['social_links']['whatsapp'] }}" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                    @else
                        <a href="https://wa.me/201144470845" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                    @endif

                    @if(!empty($global['social_links']['tiktok']))
                        <a href="{{ $global['social_links']['tiktok'] }}" target="_blank" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                    @else
                        <a href="https://www.tiktok.com/@mahmoudshaltout2823?_r=1&_t=ZS-9857s4cJBuD" target="_blank" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                    @endif

                    @if(!empty($global['social_links']['facebook']))
                        <a href="{{ $global['social_links']['facebook'] }}" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
                    @endif
                </div>
            </div>

            <div class="form-card">
                <h3>{{ __('Send A Message') }}</h3>
                <div id="contactAlert" style="display:none; margin-bottom: 20px; padding: 14px 20px; border-radius: 12px; font-size: 14px;"></div>

                <form id="contactForm" action="{{ route('web.contact.store') }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">{{ __('Name') }}*</label>
                            <input type="text" name="name" id="name" placeholder="{{ __('Name') }}" required value="{{ old('name') }}">
                        </div>
                        <div class="form-group">
                            <label for="phone">{{ __('Phone') }}</label>
                            <input type="tel" name="phone" id="phone" placeholder="{{ __('Phone') }}" value="{{ old('phone') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">{{ __('Email') }}*</label>
                        <input type="email" name="email" id="email" placeholder="{{ __('Email') }}" required value="{{ old('email') }}">
                    </div>

                    <div class="form-group">
                        <label for="message">{{ __('Message') }}*</label>
                        <textarea name="message" id="message" rows="4" placeholder="{{ __('Message') }}" required>{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="btn-submit" id="contactSubmitBtn">{{ __('Submit') }}</button>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.getElementById("contactForm");
            const alertBox = document.getElementById("contactAlert");
            const submitBtn = document.getElementById("contactSubmitBtn");

            if (form) {
                form.addEventListener("submit", async (e) => {
                    e.preventDefault();
                    submitBtn.disabled = true;
                    submitBtn.innerText = "{{ __('Sending...') }}";

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
                            alertBox.innerText = resData.message || "{{ __('Your message has been sent successfully! We will get back to you soon.') }}";
                            form.reset();
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
                        submitBtn.innerText = "{{ __('Submit') }}";
                    }
                });
            }
        });
    </script>
@endsection
