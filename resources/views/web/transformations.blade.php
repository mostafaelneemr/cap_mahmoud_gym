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
            background-image: linear-gradient(90deg, rgba(0,0,0,0.94) 0%, rgba(0,0,0,0.82) 32%, rgba(0,0,0,0.4) 60%, rgba(0,0,0,0.15) 100%), url('images/transformation-split.png');
            background-size: cover;
            background-position: center 20%;
            background-repeat: no-repeat;
            margin: 0 20px 25px 20px;
            border-radius: 24px;
            padding: 40px 60px;
            min-height: 440px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #1a1a1a;
            position: relative;
            overflow: hidden;
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

        /* --- Results Section (Gallery Carousel) --- */
        .results-section {
            background-color: #080808;
            margin: 0 20px 25px 20px;
            border-radius: 24px;
            padding: 35px 20px;
            text-align: center;
            border: 1px solid #1a1a1a;
            position: relative;
            overflow: hidden;
        }

        .results-section::before {
            content: "";
            position: absolute;
            top: -80px;
            left: -80px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(168, 0, 0, 0.25), transparent 70%);
            filter: blur(8px);
            pointer-events: none;
        }

        .results-section::after {
            content: "";
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 180px;
            height: 180px;
            border: 2px solid rgba(168, 0, 0, 0.3);
            border-radius: 50%;
            pointer-events: none;
        }

        /* تعديل الخط لعنوان RESULTS don't LIE ليطابق الصورة */
        .results-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 38px;
            font-weight: 900;
            margin-bottom: 30px;
            letter-spacing: 10px;
            text-transform: uppercase;
            color: #b30000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            line-height: 1.2;
        }

        .results-title .handwriting {
               font-family: 'Caveat', cursive;
               color: #ffffff;
               font-weight: 700;
               font-size: 42px;
               text-transform: none;
               letter-spacing: normal;
               margin: 0 4px;
           }

        .carousel-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            position: relative;
        }

        .nav-arrow {
            background: none;
            border: none;
            color: #ffffff;
            font-size: 28px;
            cursor: pointer;
            padding: 10px;
            transition: color 0.3s;
        }

        .nav-arrow:hover {
            color: #a80000;
        }

        .results-grid {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            scroll-behavior: smooth;
        }


        .results-grid::-webkit-scrollbar {
            display: none;
        }

        .transformation-card {
            display: flex;
            gap: 10px;
            background-color: #111111;
            padding: 10px;
            border-radius: 12px;
            border: 1px solid #222222;
        }

        .photo-box {
            position: relative;
            width: 130px;
            height: 180px;
            border-radius: 8px;
            overflow: hidden;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tag-badge {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #800000;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 12px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* --- Customer Reviews Section --- */
        .reviews-section {
            background-color: #080808;
            margin: 0 20px 25px 20px;
            border-radius: 24px;
            padding: 40px;
            border: 1px solid #1a1a1a;
            position: relative;
            overflow: hidden;
        }

        .reviews-section::before {
            content: "";
            position: absolute;
            top: -60px;
            right: -60px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(168, 0, 0, 0.22), transparent 70%);
            filter: blur(8px);
            pointer-events: none;
        }

        .reviews-section h2 {
            text-align: center;
            font-size: 32px;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 800px;
            margin: 0 auto 40px auto;
        }

        .review-card {
            background-color: #d9d9d9;
            color: #000000;
            border-radius: 16px;
            padding: 16px 20px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            position: relative;
        }

        .review-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            border: 2px solid #a80000;
        }

        .review-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .review-info {
            flex-grow: 1;
        }

        .review-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 4px;
        }

        .review-header h4 {
            font-size: 15px;
            font-weight: 700;
        }

        .review-stars {
            color: #a80000;
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .rating-score {
            color: #222222;
            font-size: 12px;
            font-weight: bold;
            margin-left: 4px;
        }

        .review-text {
            font-size: 12px;
            color: #333333;
            line-height: 1.4;
            margin-top: 4px;
        }

        .quote-icon {
            color: #a80000;
            font-size: 24px;
            opacity: 0.8;
            margin-left: auto;
        }

        /* --- Submit Review Form Card --- */
        .submit-review-card {
            border: 1px solid #444444;
            border-radius: 20px;
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
            background-color: #000000;
        }

        .submit-review-card h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #ffffff;
        }

        .star-rating-input {
            margin-bottom: 20px;
        }

        .star-rating-input p {
            font-size: 12px;
            color: #aaaaaa;
            margin-bottom: 6px;
        }

        .stars-select {
            display: flex;
            gap: 6px;
            color: #a80000;
            font-size: 18px;
            cursor: pointer;
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }

        .review-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .review-field label {
            font-size: 12px;
            color: #aaaaaa;
        }

        .review-field input,
        .review-field textarea {
            background-color: #0a0a0a;
            border: 1px solid #333333;
            border-radius: 8px;
            padding: 12px;
            color: #ffffff;
            font-size: 13px;
            outline: none;
        }

        .review-field input:focus,
        .review-field textarea:focus {
            border-color: #a80000;
        }

        .review-field textarea {
            resize: none;
            height: 100px;
        }

        .btn-feedback {
            background-color: #800000;
            color: #ffffff;
            border: none;
            padding: 12px 35px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 15px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-feedback:hover {
            background-color: #a80000;
        }

        footer {
            background-color: #400000;
            margin-top: 20px;
        }

        @media (max-width: 992px) {
            .hero-section {
                flex-direction: column;
                text-align: center;
                padding: 40px 20px;
            }

            .hero-content {
                max-width: 100%;
                margin-bottom: 30px;
            }
        }

        @media (max-width: 768px) {
            .form-row-2 {
                grid-template-columns: 1fr;
            }

            .reviews-section {
                padding: 25px 15px;
            }

            .footer-top {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .results-title {
                font-size: 28px;
                letter-spacing: 5px;
            }

            .results-title .handwriting {
                font-size: 32px;
            }
        }

    </style>

@endsection

@section('content')

    <section class="hero-section" @if(!empty($transHero['image'])) style="background-image: linear-gradient(90deg, rgba(0,0,0,0.94) 0%, rgba(0,0,0,0.82) 32%, rgba(0,0,0,0.4) 60%, rgba(0,0,0,0.15) 100%), url('{{ $transHero['image'] }}');" @endif>
        <div class="hero-content">
            <span class="sub-heading">{{ $transHero['tagline'] ?? 'GROW STRONGER' }}</span>
            <h1>{!! !empty($transHero['title']) ? e($transHero['title']) : '<span class="highlight">The Journey</span> of Transformation' !!}</h1>
            <p>{{ $transHero['subtitle'] ?? 'Steady steps of training and commitment, see how willpower turned into real results.' }}</p>
            <a href="{{ route('web.join-us') }}" class="btn-primary">
                {{ $transHero['button_text'] ?? 'JOIN US' }} <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>
    </section>

    <section class="results-section">
        <h2 class="results-title">
            <span class="results-line">{{ $transformation['section_title'] ?? 'RESULTS' }}</span>
            <span class="results-line"><span class="handwriting">don't</span> LIE</span>
        </h2>

        <div class="carousel-container">
            <button type="button" class="nav-arrow" id="slidePrev" aria-label="Previous">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <div class="results-grid" id="resultsGrid">
                @php
                    $transItems = !empty($transformation['items']) ? $transformation['items'] : [];
                @endphp

                @forelse($transItems as $item)
                    <div class="transformation-card">
                        @if(!empty($item['before_image']))
                            <div class="photo-box">
                                <img src="{{ $item['before_image'] }}" alt="{{ $item['trainee_name'] ?? 'Before' }}">
                                <span class="tag-badge">{{ __('BEFORE') }}</span>
                            </div>
                        @endif
                        @if(!empty($item['after_image']))
                            <div class="photo-box">
                                <img src="{{ $item['after_image'] }}" alt="{{ $item['trainee_name'] ?? 'After' }}">
                                <span class="tag-badge">{{ __('AFTER') }}</span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="transformation-card">
                        <div class="photo-box">
                            <img src="{{ asset('images/about-coach.png') }}" alt="Before Transformation">
                            <span class="tag-badge">{{ __('BEFORE') }}</span>
                        </div>
                        <div class="photo-box">
                            <img src="{{ asset('images/about-coach.png') }}" alt="After Transformation">
                            <span class="tag-badge">{{ __('AFTER') }}</span>
                        </div>
                    </div>
                    <div class="transformation-card">
                        <div class="photo-box">
                            <img src="{{ asset('images/about-coach.png') }}" alt="Before Transformation">
                            <span class="tag-badge">{{ __('BEFORE') }}</span>
                        </div>
                        <div class="photo-box">
                            <img src="{{ asset('images/about-coach.png') }}" alt="After Transformation">
                            <span class="tag-badge">{{ __('AFTER') }}</span>
                        </div>
                    </div>
                @endforelse
            </div>

            <button type="button" class="nav-arrow" id="slideNext" aria-label="Next">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </section>

    <section class="reviews-section">
        <h2>{{ $reviews['section_title'] ?? 'Our Customer Reviews' }}</h2>

        <div class="reviews-list">
            @php
                $reviewItems = !empty($reviews['items']) ? $reviews['items'] : [
                    [
                        'name' => 'Hamza',
                        'rating' => 5.0,
                        'avatar' => asset('images/user1.jpg'),
                        'review' => "Honestly coach, you've been so committed with me and followed up with me every single day. I lost a lot of weight during the period I trained with you, which was less than 3 months, and of course I found a very satisfying result ❤️❤️"
                    ],
                    [
                        'name' => 'Eyad',
                        'rating' => 5.0,
                        'avatar' => asset('images/user2.jpg'),
                        'review' => "The follow-up was excellent; he monitored me 24 hours a day, tracking the number of meals I ate and the progress at each stage. Overall, I was very happy that I was training with him."
                    ],
                    [
                        'name' => 'Mohamed',
                        'rating' => 5.0,
                        'avatar' => asset('images/user3.jpg'),
                        'review' => "Honestly bro, no need for words — the transformation is unbelievable. Everything changed: my shape, my body, my movement, everything. I went from 130 to 90 kg in 9 months with no starvation, and without losing any muscle. Amazing work, and what's coming next will be even better, God willing."
                    ],
                    [
                        'name' => 'Mustafa',
                        'rating' => 5.0,
                        'avatar' => asset('images/user4.jpg'),
                        'review' => "Of course my testimony about you is biased, but you deserve to know that you're truly one of the best coaches I've worked with. You're very attentive to every detail of the workout, you explain every exercise in a simple way, and you follow up on performance step by step so it's done correctly and gives the best results. The training program was organized and suited to my level, and you always motivate me to keep going and improve myself. Thanks for your care and effort, and may God always grant you success. 💪"
                    ]
                ];
            @endphp

            @foreach($reviewItems as $rev)
                <div class="review-card">
                    <div class="review-avatar">
                        <img src="{{ !empty($rev['avatar']) ? $rev['avatar'] : asset('images/user1.jpg') }}" alt="{{ $rev['name'] }}">
                    </div>
                    <div class="review-info">
                        <div class="review-header">
                            <h4>{{ $rev['name'] }}</h4>
                            <div class="review-stars">
                                @for($s = 1; $s <= 5; $s++)
                                    <i class="fa-solid fa-star" style="color: {{ $s <= round($rev['rating'] ?? 5) ? '#a80000' : '#888888' }};"></i>
                                @endfor
                                <span class="rating-score">{{ number_format($rev['rating'] ?? 5.0, 1) }}</span>
                            </div>
                        </div>
                        <p class="review-text">{{ $rev['review'] }}</p>
                    </div>
                    <div class="quote-icon"><i class="fa-solid fa-quote-right"></i></div>
                </div>
            @endforeach
        </div>

        <div class="submit-review-card">
            <h3>{{ __('Submit Your Review') }}</h3>
            <div id="reviewAlert" style="display:none; margin-bottom: 15px; padding: 12px 18px; border-radius: 10px; font-size: 14px;"></div>

            <form id="reviewForm" action="{{ route('web.reviews.store') }}" method="POST">
                @csrf
                <input type="hidden" name="rating" id="selectedRating" value="5">

                <div class="star-rating-input">
                    <p>{{ __('Add Your Rating*') }}</p>
                    <div class="stars-select" id="starContainer">
                        <i class="fa-solid fa-star" data-value="1"></i>
                        <i class="fa-solid fa-star" data-value="2"></i>
                        <i class="fa-solid fa-star" data-value="3"></i>
                        <i class="fa-solid fa-star" data-value="4"></i>
                        <i class="fa-solid fa-star" data-value="5"></i>
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="review-field">
                        <label for="revName">{{ __('Name*') }}</label>
                        <input type="text" name="name" id="revName" required value="{{ old('name') }}">
                    </div>
                    <div class="review-field">
                        <label for="revEmail">{{ __('Email*') }}</label>
                        <input type="email" name="email" id="revEmail" required value="{{ old('email') }}">
                    </div>
                </div>

                <div class="review-field">
                    <label for="revMessage">{{ __('Write Your Review*') }}</label>
                    <textarea name="message" id="revMessage" required>{{ old('message') }}</textarea>
                </div>

                <button type="submit" class="btn-feedback" id="reviewSubmitBtn">{{ __('Leave Feedback') }}</button>
            </form>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Interactive Star Rating
            const stars = document.querySelectorAll("#starContainer i");
            const ratingInput = document.getElementById("selectedRating");

            const setStars = (val) => {
                stars.forEach((star) => {
                    const sVal = parseInt(star.getAttribute("data-value"), 10);
                    if (sVal <= val) {
                        star.classList.remove("fa-regular");
                        star.classList.add("fa-solid");
                        star.style.color = "#a80000";
                    } else {
                        star.classList.remove("fa-solid");
                        star.classList.add("fa-regular");
                        star.style.color = "#666666";
                    }
                });
            };

            setStars(5);

            stars.forEach((star) => {
                star.addEventListener("click", () => {
                    const val = parseInt(star.getAttribute("data-value"), 10);
                    ratingInput.value = val;
                    setStars(val);
                });
            });

            // Results Carousel Navigation
            const grid = document.getElementById("resultsGrid");
            const prevBtn = document.getElementById("slidePrev");
            const nextBtn = document.getElementById("slideNext");

            if (grid && prevBtn && nextBtn) {
                prevBtn.addEventListener("click", () => {
                    grid.scrollBy({ left: -280, behavior: "smooth" });
                });
                nextBtn.addEventListener("click", () => {
                    grid.scrollBy({ left: 280, behavior: "smooth" });
                });
            }

            // AJAX Form Submission for smooth UX
            const form = document.getElementById("reviewForm");
            const alertBox = document.getElementById("reviewAlert");
            const submitBtn = document.getElementById("reviewSubmitBtn");

            if (form) {
                form.addEventListener("submit", async (e) => {
                    e.preventDefault();
                    submitBtn.disabled = true;
                    submitBtn.innerText = "{{ __('Submitting...') }}";

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
                            alertBox.innerText = resData.message || "{{ __('Thank you for your review! It will appear after moderation.') }}";
                            form.reset();
                            setStars(5);
                        } else {
                            alertBox.style.backgroundColor = "rgba(168, 0, 0, 0.2)";
                            alertBox.style.border = "1px solid #a80000";
                            alertBox.style.color = "#ff6b6b";
                            alertBox.innerText = resData.message || "{{ __('Please check your input and try again.') }}";
                        }
                    } catch (err) {
                        form.submit(); // fallback to standard POST
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.innerText = "{{ __('Leave Feedback') }}";
                    }
                });
            }
        });
    </script>
@endsection
