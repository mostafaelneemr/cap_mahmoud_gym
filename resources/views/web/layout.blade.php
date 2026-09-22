<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Home - Mahmoud Shaltout Fitness Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- Reset & Base Styles --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: #333333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px 10px;
            overflow-x: hidden;
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
            position: relative;
        }

        /* --- Reveal-on-scroll --- */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
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
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .nav-links a {
            color: #d1d1d1;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: #ffffff;
        }

        .nav-links a.active-link {
            background: linear-gradient(135deg, #c90000, #7a0000);
            color: #ffffff;
            padding: 6px 18px;
            border-radius: 20px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(168, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }

        .user-profile {
            background: linear-gradient(145deg, #c40000, #7a0000);
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(168, 0, 0, 0.45), inset 0 1px 1px rgba(255, 255, 255, 0.2);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .user-profile:hover {
            transform: translateY(-2px) scale(1.06);
            box-shadow: 0 8px 16px rgba(168, 0, 0, 0.6), inset 0 1px 1px rgba(255, 255, 255, 0.25);
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
            background-image: linear-gradient(90deg, rgba(0,0,0,0.94) 0%, rgba(0,0,0,0.82) 32%, rgba(0,0,0,0.4) 60%, rgba(0,0,0,0.15) 100%), url('images/hero-man.png');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            margin: 0 20px 25px 20px;
            border-radius: 24px;
            padding: 70px 60px;
            min-height: 440px;
            display: flex;
            align-items: center;
            border: 1px solid #1a1a1a;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            top: 50%;
            right: 10%;
            width: 360px;
            height: 360px;
            background: radial-gradient(circle, rgba(168, 0, 0, 0.3), transparent 70%);
            transform: translateY(-50%);
            filter: blur(10px);
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
            text-shadow: 0 0 25px rgba(168, 0, 0, 0.55);
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
            background: linear-gradient(145deg, #c90000, #8a0000);
            color: #ffffff;
            padding: 12px 26px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 6px 0 #5c0000, 0 10px 18px rgba(0, 0, 0, 0.45);
            transform: translateY(0);
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.3s ease;
            position: relative;
        }

        .btn-primary:hover {
            background: linear-gradient(145deg, #de0000, #9a0000);
            transform: translateY(-3px);
            box-shadow: 0 9px 0 #5c0000, 0 16px 22px rgba(0, 0, 0, 0.5);
        }

        .btn-primary:active {
            transform: translateY(3px);
            box-shadow: 0 2px 0 #5c0000, 0 4px 10px rgba(0, 0, 0, 0.4);
        }



        /* --- About Section --- */
        .about-section {
            background-color: #080808;
            margin: 0 20px 25px 20px;
            border-radius: 24px;
            padding: 40px 30px;
            text-align: center;
            border: 1px solid #1a1a1a;
        }

        .about-section .section-tag {
            color: #777777;
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
        }

        .about-section h2 {
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .about-section p.description {
            color: #888888;
            font-size: 13px;
            max-width: 650px;
            margin: 0 auto 40px auto;
            line-height: 1.6;
        }

        .features-grid {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 30px;
            position: relative;
            perspective: 900px;
        }

        .feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            width: 120px;
        }

        .feature-icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 1px dashed #a80000;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #000000;
            color: #ffffff;
            font-size: 24px;
            transition: transform 0.35s ease, box-shadow 0.35s ease, background-color 0.35s ease;
        }

        .feature-item:hover .feature-icon-circle {
            transform: translateY(-8px) rotateY(15deg);
            background-color: #1a0000;
            box-shadow: 0 14px 24px rgba(168, 0, 0, 0.4);
            border-style: solid;
        }

        .feature-item span {
            font-size: 12px;
            color: #cccccc;
            font-weight: 500;
        }

        .about-center-img {
            width: 260px;
            height: 260px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #222222;
            flex-shrink: 0;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            transition: transform 0.4s ease;
        }

        .about-center-img:hover {
            transform: scale(1.04);
        }

        .about-center-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* --- Performance Section --- */
        .performance-section {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin: 0 20px 25px 20px;
        }

        .perf-card-text {
            background-color: #080808;
            border-radius: 24px;
            padding: 40px 35px;
            border: 1px solid #1a1a1a;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .perf-card-text h2 {
            color: #a80000;
            font-size: 24px;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        .perf-card-text p {
            color: #888888;
            font-size: 13px;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .perf-card-img {
            border-radius: 24px;
            height: 100%;
            min-height: 300px;
            border: 1px solid #1a1a1a;
            background-color: #111111;
            background-image: url('images/coach-portrait.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            transition: transform 0.5s ease;
            margin-bottom: 35px;
        }

        .perf-card-img:hover {
            transform: scale(1.02);
        }

        /* --- Services Section --- */
        .services-section {
            background-color: #080808;
            margin: 0 20px 25px 20px;
            border-radius: 24px;
            padding: 40px;
            border: 1px solid #1a1a1a;
        }

        .services-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .services-header h2 {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .services-header h3 {
            color: #a80000;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .services-header p {
            color: #888888;
            font-size: 13px;
            max-width: 550px;
            margin: 0 auto;
            line-height: 1.5;
        }

        .services-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: center;
        }

        .services-cards {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .service-card-main,
        .service-card-sub {
            transition: transform 0.35s ease, box-shadow 0.35s ease;
            will-change: transform;
        }

        .service-card-main {
            background: linear-gradient(155deg, #9a9a9a, #7c7c7c);
            color: #000000;
            border-radius: 18px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.35);
            border: 2px solid #a80000;
        }

        .service-card-main:hover,
        .service-card-sub:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 30px rgba(0, 0, 0, 0.45);
        }

        .service-card-main i {
            font-size: 32px;
            color: #a80000;
            margin-bottom: 10px;
        }

        .service-card-main h4 {
            font-size: 19px;
            margin-bottom: 10px;
            text-transform: lowercase;
            font-weight: 800;
            letter-spacing: 0.3px;
        }

        .service-card-main p {
            font-size: 12px;
            color: #111111;
            line-height: 1.5;
            font-weight: 700;
        }

        .service-sub-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .service-card-sub {
            background: linear-gradient(155deg, #9a9a9a, #7c7c7c);
            color: #000000;
            border-radius: 18px;
            padding: 20px 15px;
            text-align: center;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .service-card-sub h5 {
            font-size: 14px;
            margin-bottom: 8px;
        }

        .service-card-sub p {
            font-size: 11px;
            color: #222222;
            line-height: 1.4;
            font-weight: 500;
        }

        .services-logo-side {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .services-logo-side img {
            max-width: 280px;
            object-fit: contain;
            filter: drop-shadow(0 10px 20px rgba(168, 0, 0, 0.35));
        }

        /* --- Why Choose Us Section --- */
        .why-us-section {
            background-color: #080808;
            margin: 0 20px 25px 20px;
            border-radius: 24px;
            padding: 40px 30px;
            text-align: center;
            border: 1px solid #1a1a1a;
        }

        .why-us-section h2 {
            font-size: 28px;
            margin-bottom: 35px;
        }

        .why-us-grid {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 0;
            width: 100%;
            max-width: 620px;
            margin: 0 auto;
            text-align: left;
        }

        .why-us-card {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px 10px;
            border-bottom: 1px solid #1c1c1c;
            transition: padding-left 0.35s ease, background-color 0.35s ease;
        }

        .why-us-card:last-child {
            border-bottom: none;
        }

        .why-us-card:hover {
            padding-left: 18px;
            background-color: #0d0d0d;
        }

        .why-us-rank {
            font-size: 13px;
            font-weight: 700;
            color: #3a3a3a;
            width: 26px;
            flex-shrink: 0;
        }

        .why-us-card:first-child .why-us-rank {
            color: #a80000;
        }

        .why-us-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(145deg, #c40000, #7a0000);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            color: #ffffff;
            flex-shrink: 0;
            box-shadow: 0 6px 14px rgba(168, 0, 0, 0.4);
            transition: transform 0.35s ease, box-shadow 0.35s ease;
        }

        .why-us-card:first-child .why-us-icon {
            width: 60px;
            height: 60px;
            font-size: 24px;
        }

        .why-us-card:hover .why-us-icon {
            transform: translateY(-4px) rotateX(10deg);
            box-shadow: 0 14px 22px rgba(168, 0, 0, 0.55);
        }

        .why-us-text {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .why-us-card p {
            font-size: 14px;
            color: #e8e8e8;
            line-height: 1.4;
            font-weight: 600;
        }

        .why-us-card:first-child p {
            font-size: 16px;
        }

        .why-us-card span.why-us-note {
            font-size: 12px;
            color: #888888;
            font-weight: 400;
        }

        /* --- CTA Bar Section --- */
        .cta-section {
            background-color: #000000;
            margin: 0 20px 30px 20px;
            padding: 25px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 18px;
            border: 1px solid #1a1a1a;
        }

        .cta-section h3 {
            font-size: 22px;
            letter-spacing: 0.5px;
        }

        .btn-secondary {
            background-color: #888888;
            color: #000000;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 0.5px;
            box-shadow: 0 5px 0 #5b5b5b, 0 8px 14px rgba(0, 0, 0, 0.4);
            transform: translateY(0);
            transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 8px 0 #5b5b5b, 0 12px 18px rgba(0, 0, 0, 0.45);
        }

        .btn-secondary:active {
            transform: translateY(3px);
            box-shadow: 0 2px 0 #5b5b5b, 0 4px 8px rgba(0, 0, 0, 0.35);
        }

        /* --- Footer --- */
        footer {
            background-color: #400000;
        }

        .footer-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 40px;
            flex-wrap: wrap;
            gap: 20px;
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
            flex-wrap: wrap;
            justify-content: center;
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
            transition: transform 0.25s ease, color 0.25s ease;
            display: inline-block;
        }

        .footer-socials a:hover {
            transform: translateY(-4px) scale(1.15);
            color: #ffd1d1;
        }

        .footer-bottom {
            background-color: #000000;
            text-align: center;
            padding: 16px;
            font-size: 12px;
            color: #777777;
        }

        /* --- Respect reduced motion preference --- */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.001ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.001ms !important;
                scroll-behavior: auto !important;
            }
            .reveal {
                opacity: 1;
                transform: none;
            }
        }

        /* --- Responsive Styles (Mobile & Tablet) --- */
        @media (max-width: 992px) {
            .hero-section {
                justify-content: center;
                text-align: center;
                padding: 50px 20px;
                background-position: center 30%;
                min-height: 480px;
            }
            .hero-content {
                max-width: 100%;
            }
            .features-grid {
                flex-wrap: wrap;
            }
            .performance-section {
                grid-template-columns: 1fr;
            }
            .services-content {
                grid-template-columns: 1fr;
            }
            .services-logo-side {
                order: -1;
            }
            .cta-section {
                flex-direction: column;
                gap: 20px;
                text-align: center;
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
            .why-us-grid {
                grid-template-columns: 1fr 1fr;
            }
            .footer-top {
                flex-direction: column;
                text-align: center;
            }
            .about-center-img {
                width: 200px;
                height: 200px;
            }
            .hero-content h1 {
                font-size: 30px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 12px 8px;
            }
            .main-container {
                border-radius: 20px;
            }
            .hero-section,
            .about-section,
            .services-section,
            .why-us-section {
                margin-left: 12px;
                margin-right: 12px;
                padding-left: 20px;
                padding-right: 20px;
            }
            .performance-section,
            .cta-section {
                margin-left: 12px;
                margin-right: 12px;
            }
            .hero-section {
                min-height: 420px;
                padding: 40px 18px;
                background-position: 68% center;
            }
            .hero-content h1 {
                font-size: 26px;
            }
            .hero-content p {
                font-size: 13px;
            }
            .about-section h2 {
                font-size: 24px;
            }
            .about-center-img {
                width: 160px;
                height: 160px;
                order: -1;
            }
            .feature-item {
                width: 90px;
            }
            .feature-icon-circle {
                width: 64px;
                height: 64px;
                font-size: 20px;
            }
            .why-us-card {
                gap: 14px;
                padding: 16px 4px;
            }
            .why-us-rank {
                width: 20px;
                font-size: 11px;
            }
            .service-sub-grid {
                grid-template-columns: 1fr;
            }
            .cta-section h3 {
                font-size: 18px;
            }
            .btn-primary,
            .btn-secondary {
                width: 100%;
                justify-content: center;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <div class="main-container">
        @include('web.partials.header')

        @yield('content')

        @include('web.partials.footer')
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const hamburgerBtn = document.getElementById("hamburgerBtn");
            const mobileNav = document.getElementById("mobileNav");

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

            // Close the mobile menu automatically once a link is chosen
            mobileNav.querySelectorAll("a").forEach((link) => {
                link.addEventListener("click", closeMobileNav);
            });

            // Scroll-reveal animation for sections
            const revealEls = document.querySelectorAll(".reveal");
            if ("IntersectionObserver" in window && revealEls.length) {
                const observer = new IntersectionObserver(
                    (entries) => {
                        entries.forEach((entry) => {
                            if (entry.isIntersecting) {
                                entry.target.classList.add("is-visible");
                                observer.unobserve(entry.target);
                            }
                        });
                    },
                    { threshold: 0.15 }
                );
                revealEls.forEach((el) => observer.observe(el));
            } else {
                revealEls.forEach((el) => el.classList.add("is-visible"));
            }
        });
    </script>
</body>
</html>
