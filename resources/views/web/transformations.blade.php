<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transformations - Mahmoud Shaltout Fitness Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Montserrat:wght@900&display=swap" rel="stylesheet">

    <style>
        /* --- Reset & Base Styles --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #333333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px 10px;
        }

        .main-container {
            width: 100%;
            max-width: 1100px;
            background-color: #000000;
            border-radius: 28px;
            overflow: hidden;
            color: #ffffff;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8);
            border: 1px solid #222222;
        }

        /* --- Header / Navbar --- */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 40px;
            background-color: #000000;
            position: relative;
            z-index: 100;
        }

        .logo img {
            height: 40px;
            object-fit: contain;
        }

        .nav-links {
            background-color: #3f3f3f;
            padding: 8px 24px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-links a {
            color: #d1d1d1;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            color: #ffffff;
        }

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

        .user-profile i {
            color: #ffffff;
            font-size: 16px;
        }

        /* --- Hamburger Menu Icon & Mobile Navigation --- */
        .hamburger-btn {
            display: none;
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 24px;
            cursor: pointer;
            padding: 5px;
            z-index: 101;
        }

        .nav-right-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .mobile-nav-menu {
            display: flex;
            flex-direction: column;
            background-color: #141414;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
            z-index: 100;
            border-top: 1px solid #262626;
            transition: max-height 0.35s ease, padding 0.35s ease;
        }

        .mobile-nav-menu.open {
            max-height: 320px;
            padding: 14px 20px;
        }

        .mobile-nav-menu a {
            color: #ffffff;
            text-decoration: none;
            padding: 12px 15px;
            font-size: 15px;
            font-weight: 500;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .mobile-nav-menu a:hover {
            background-color: #262626;
        }

        .mobile-nav-menu a.active-link {
            background: linear-gradient(135deg, #c90000, #7a0000);
            font-weight: 600;
        }

        /* --- Hero Section --- */
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

        .hero-content {
            max-width: 450px;
            z-index: 2;
        }

        .sub-heading {
            font-size: 12px;
            letter-spacing: 2.5px;
            color: #888888;
            font-weight: 700;
            display: block;
            margin-bottom: 8px;
        }

        .hero-content h1 {
            font-size: 38px;
            line-height: 1.2;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .hero-content h1 span.highlight {
            color: #a80000;
        }

        .hero-content p {
            color: #888888;
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

        /* --- Footer --- */
        footer {
            background-color: #400000;
            margin-top: 20px;
        }

        .footer-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 40px;
        }

        .footer-logo img {
            height: 62px;
            object-fit: contain;
            animation: footerLogoFloat 3s ease-in-out infinite;
        }

        @keyframes footerLogoFloat {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-6px) scale(1.04); }
        }

        .footer-nav {
            display: flex;
            gap: 30px;
        }

        .footer-nav a {
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            transition: opacity 0.3s;
        }

        .footer-nav a:hover {
            opacity: 0.8;
        }

        .footer-socials {
            display: flex;
            gap: 18px;
        }

        .footer-socials a {
            color: #ffffff;
            font-size: 20px;
            text-decoration: none;
        }

        .footer-bottom {
            background-color: #000000;
            text-align: center;
            padding: 16px;
            font-size: 12px;
            color: #777777;
        }

        /* --- Responsive Styles --- */
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
            .navbar {
                padding: 18px 20px;
            }
            .nav-links {
                display: none;
            }
            .hamburger-btn {
                display: block;
            }
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
</head>
<body>

    <div class="main-container">
        <header class="navbar">
            <div class="logo">
                <a href="index.html">
                    <img src="images/logo.png" alt="Mahmoud Shaltout Logo">
                </a>
            </div>
            <nav class="nav-links">
                <a href="index.html">Home</a>
                <a href="transformations.html" class="active-link">Transformations</a>
                <a href="join_us.html">Join Us</a>
                <a href="contact_us.html">Contact Us</a>
            </nav>

            <div class="nav-right-actions">
                <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle Navigation" aria-expanded="false" aria-controls="mobileNav">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="user-profile">
                    <i class="fa-regular fa-user"></i>
                </div>
            </div>

            <nav class="mobile-nav-menu" id="mobileNav">
                <a href="index.html">Home</a>
                <a href="transformations.html" class="active-link">Transformations</a>
                <a href="join_us.html">Join Us</a>
                <a href="contact_us.html">Contact Us</a>
            </nav>
        </header>

        <section class="hero-section">
            <div class="hero-content">
                <span class="sub-heading">GROW STRONGER</span>
                <h1><span class="highlight">The Journey</span> of Transformation</h1>
                <p>Steady steps of training and commitment, see how willpower turned into real results.</p>
                <a href="join_us.html" class="btn-primary">
                    JOIN US <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
            </div>
        </section>

        <section class="results-section">
            <h2 class="results-title">
                <span class="results-line">RESULTS</span>
                <span class="results-line"><span class="handwriting">don't</span> LIE</span>
            </h2>
            
            <div class="carousel-container">
                <div class="results-grid" id="resultsGrid"></div>
            </div>
        </section>

        <section class="reviews-section">
            <h2>Our Customer Reviews</h2>

            <div class="reviews-list">
                <div class="review-card">
                    <div class="review-avatar">
                        <img src="images/user1.jpg" alt="Hamza">
                    </div>
                    <div class="review-info">
                        <div class="review-header">
                            <h4>Hamza</h4>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <span class="rating-score">5.0</span>
                            </div>
                        </div>
                        <p class="review-text">Honestly coach, you've been so committed with me and followed up with me every single day. I lost a lot of weight during the period I trained with you, which was less than 3 months, and of course I found a very satisfying result ❤️❤️</p>
                    </div>
                </div>

                <div class="review-card">
                    <div class="review-avatar">
                        <img src="images/user2.jpg" alt="Eyad">
                    </div>
                    <div class="review-info">
                        <div class="review-header">
                            <h4>Eyad</h4>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <span class="rating-score">5.0</span>
                            </div>
                        </div>
                        <p class="review-text">The follow-up was excellent; he monitored me 24 hours a day, tracking the number of meals I ate and the progress at each stage. Overall, I was very happy that I was training with him.</p>
                    </div>
                </div>

                <div class="review-card">
                    <div class="review-avatar">
                        <img src="images/user3.jpg" alt="Mohamed">
                    </div>
                    <div class="review-info">
                        <div class="review-header">
                            <h4>Mohamed</h4>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <span class="rating-score">5.0</span>
                            </div>
                        </div>
                        <p class="review-text">Honestly bro, no need for words — the transformation is unbelievable. Everything changed: my shape, my body, my movement, everything. I went from 130 to 90 kg in 9 months with no starvation, and without losing any muscle. Amazing work, and what's coming next will be even better, God willing.</p>
                    </div>
                </div>

                <div class="review-card">
                    <div class="review-avatar">
                        <img src="images/user4.jpg" alt="Mustafa">
                    </div>
                    <div class="review-info">
                        <div class="review-header">
                            <h4>Mustafa</h4>
                            <div class="review-stars">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <span class="rating-score">5.0</span>
                            </div>
                        </div>
                        <p class="review-text">Of course my testimony about you is biased, but you deserve to know that you're truly one of the best coaches I've worked with. You're very attentive to every detail of the workout, you explain every exercise in a simple way, and you follow up on performance step by step so it's done correctly and gives the best results. The training program was organized and suited to my level, and you always motivate me to keep going and improve myself. Thanks for your care and effort, and may God always grant you success. 💪</p>
                    </div>
                </div>
            </div>

            <div class="submit-review-card">
                <h3>Submit Your Review</h3>
                <form id="reviewForm">
                    <div class="star-rating-input">
                        <p>Add Your Rating*</p>
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
                            <label for="revName">Name*</label>
                            <input type="text" id="revName" required>
                        </div>
                        <div class="review-field">
                            <label for="revEmail">Email*</label>
                            <input type="email" id="revEmail" required>
                        </div>
                    </div>

                    <div class="review-field">
                        <label for="revMessage">Write Your Review*</label>
                        <textarea id="revMessage" required></textarea>
                    </div>

                    <button type="submit" class="btn-feedback">Leave Feedback</button>
                </form>
            </div>
        </section>

        <footer>
            <div class="footer-top">
                <div class="footer-logo">
                    <a href="index.html">
                        <img src="images/logo.png" alt="Mahmoud Shaltout Logo">
                    </a>
                </div>
                <div class="footer-socials">
                    <a href="https://www.instagram.com/mahmoudshaltout2823" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://wa.me/201144470845" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="https://www.tiktok.com/@mahmoudshaltout2823?_r=1&_t=ZS-9857s4cJBuD" target="_blank" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>Copyright© Mahmoud Shaltout . All Right Reserved</p>
            </div>
        </footer>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Hamburger mobile navigation
            const hamburgerBtn = document.getElementById("hamburgerBtn");
            const mobileNav = document.getElementById("mobileNav");

            if (hamburgerBtn && mobileNav) {
                const closeMobileNav = () => {
                    mobileNav.classList.remove("open");
                    hamburgerBtn.setAttribute("aria-expanded", "false");
                    const icon = hamburgerBtn.querySelector("i");
                    icon.classList.remove("fa-xmark");
                    icon.classList.add("fa-bars");
                };

                hamburgerBtn.addEventListener("click", () => {
                    const isOpen = mobileNav.classList.toggle("open");
                    hamburgerBtn.setAttribute("aria-expanded", String(isOpen));
                    const icon = hamburgerBtn.querySelector("i");
                    if (isOpen) {
                        icon.classList.remove("fa-bars");
                        icon.classList.add("fa-xmark");
                    } else {
                        icon.classList.remove("fa-xmark");
                        icon.classList.add("fa-bars");
                    }
                });

                mobileNav.querySelectorAll("a").forEach((link) => {
                    link.addEventListener("click", closeMobileNav);
                });
            }

            // Build transformation cards
            const resultsGrid = document.getElementById("resultsGrid");

            const transformationPairs = [
                { before: "images/person1-before.jpg", after: "images/person1-after.jpg" },
                { before: "images/person2-before.jpg", after: "images/person2-after.jpg" },
                { before: "images/person3-before.jpg", after: "images/person3-after.jpg" },
                { before: "images/person4-before.jpg", after: "images/person4-after.jpg" },
                { before: "images/person5-before.jpg", after: "images/person5-after.jpg" },
                { before: "images/person6-before.jpg", after: "images/person6-after.jpg" },
                { before: "images/person7-before.jpg", after: "images/person7-after.jpg" },
            ];

            const buildCard = (pair) => {
                const card = document.createElement("div");
                card.className = "transformation-card";
                card.innerHTML = `
                    <div class="photo-box">
                        <img src="${pair.before}" alt="Before">
                        <span class="tag-badge">Before</span>
                    </div>
                    <div class="photo-box">
                        <img src="${pair.after}" alt="After">
                        <span class="tag-badge">After</span>
                    </div>
                `;
                return card;
            };

            if (resultsGrid) {
                [...transformationPairs, ...transformationPairs].forEach((pair) => {
                    resultsGrid.appendChild(buildCard(pair));
                });
            }

            // Continuous auto-scroll
            if (resultsGrid) {
                let autoScrollPaused = false;
                let halfWidth = 0;

                const recalcHalfWidth = () => {
                    halfWidth = resultsGrid.scrollWidth / 2;
                };
                recalcHalfWidth();
                window.addEventListener("resize", recalcHalfWidth);

                resultsGrid.addEventListener("mouseenter", () => autoScrollPaused = true);
                resultsGrid.addEventListener("mouseleave", () => autoScrollPaused = false);
                resultsGrid.addEventListener("touchstart", () => autoScrollPaused = true, { passive: true });
                resultsGrid.addEventListener("touchend", () => autoScrollPaused = false);

                const autoScrollStep = () => {
                    if (!autoScrollPaused) {
                        resultsGrid.scrollLeft += 0.6;
                        if (resultsGrid.scrollLeft >= halfWidth) {
                            resultsGrid.scrollLeft -= halfWidth;
                        }
                    }
                    requestAnimationFrame(autoScrollStep);
                };
                requestAnimationFrame(autoScrollStep);
            }

            // Interactive Star Rating
            const stars = document.querySelectorAll("#starContainer i");
            let selectedRating = 5;

            stars.forEach((star, index) => {
                star.addEventListener("click", () => {
                    selectedRating = index + 1;
                    stars.forEach((s, idx) => {
                        if (idx < selectedRating) {
                            s.style.color = "#a80000";
                        } else {
                            s.style.color = "#444444";
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>