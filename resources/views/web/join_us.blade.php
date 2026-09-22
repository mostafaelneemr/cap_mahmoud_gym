<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Us - Mahmoud Shaltout Fitness Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            .hero-content {
                max-width: 100%;
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
                <a href="transformations.html">Transformations</a>
                <a href="join_us.html" class="active-link">Join Us</a>
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
                <a href="transformations.html">Transformations</a>
                <a href="join_us.html" class="active-link">Join Us</a>
                <a href="contact_us.html">Contact Us</a>
            </nav>
        </header>

        <section class="hero-section">
            <div class="hero-content">
                <span class="sub-heading">GROW STRONGER</span>
                <h1>Your <span class="highlight">Fitness</span> Journey Starts Here</h1>
                <p>Ignite your inner power, break your limits, and start building the strongest version of yourself today.</p>
                <a href="contact_us.html" class="btn-primary">
                    CONTACT US <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
            </div>
        </section>

        <section class="join-section">
            <h2 class="section-title">Ready to Transform</h2>
            
            <div class="form-container-card">
                <form id="joinForm">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" placeholder="Name" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" placeholder="Phone" required>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="number" id="age" placeholder="Age" required>
                        </div>
                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" id="country" placeholder="Country" required>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="governorate">Governorate</label>
                            <input type="text" id="governorate" placeholder="Governorate" required>
                        </div>
                        <div class="form-group">
                            <label for="trainingLevel">Training Level</label>
                            <input type="text" id="trainingLevel" placeholder="Training Level" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="goal">What is your online training goal?</label>
                        <input type="text" id="goal" placeholder="What is your online training goal?" required>
                    </div>

                    <div class="form-group">
                        <label for="injuries">Do you have any injuries ?</label>
                        <input type="text" id="injuries" placeholder="Do you have any injuries ?" required>
                    </div>

                    <div class="form-group">
                        <label for="injuryDetails">If you have any injuries, what are they and please provide details.</label>
                        <input type="text" id="injuryDetails" placeholder="If you have any injuries, what are they and please provide details.">
                    </div>

                    <div class="form-group">
                        <label for="reason">Why do you want to train with me?</label>
                        <input type="text" id="reason" placeholder="Why do you want to train with me?" required>
                    </div>

                    <div class="form-group">
                        <label for="routine">Write your daily routine in detail</label>
                        <input type="text" id="routine" placeholder="Write your daily routine in detail" required>
                    </div>

                    <button type="submit" class="btn-submit">Submit</button>
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

    <div class="send-modal-overlay" id="sendModalOverlay">
        <div class="send-modal-box">
            <h3>How would you like to send your application?</h3>
            <div class="send-modal-actions">
                <button type="button" class="send-modal-btn whatsapp-btn" id="sendViaWhatsapp">
                    <i class="fa-brands fa-whatsapp"></i> Send via WhatsApp
                </button>
                <button type="button" class="send-modal-btn email-btn" id="sendViaEmail">
                    <i class="fa-solid fa-envelope"></i> Send via Email
                </button>
            </div>
            <button type="button" class="send-modal-cancel" id="sendModalCancel">Cancel</button>
        </div>
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

            const joinForm = document.getElementById("joinForm");
            const sendModalOverlay = document.getElementById("sendModalOverlay");
            const sendViaWhatsapp = document.getElementById("sendViaWhatsapp");
            const sendViaEmail = document.getElementById("sendViaEmail");
            const sendModalCancel = document.getElementById("sendModalCancel");

            let pendingApplication = null;

            const closeSendModal = () => {
                sendModalOverlay.classList.remove("open");
                pendingApplication = null;
            };

            const buildApplicationLines = (data) => [
                `Name: ${data.name}`,
                `Phone: ${data.phone}`,
                `Age: ${data.age}`,
                `Country: ${data.country}`,
                `Governorate: ${data.governorate}`,
                `Training Level: ${data.trainingLevel}`,
                `Training Goal: ${data.goal}`,
                `Has Injuries: ${data.injuries}`,
                `Injury Details: ${data.injuryDetails || "-"}`,
                `Reason for Training: ${data.reason}`,
                `Daily Routine: ${data.routine}`
            ];

            joinForm.addEventListener("submit", (e) => {
                e.preventDefault();

                const name = document.getElementById("name").value.trim();
                const phone = document.getElementById("phone").value.trim();
                const age = document.getElementById("age").value.trim();
                const country = document.getElementById("country").value.trim();
                const governorate = document.getElementById("governorate").value.trim();
                const trainingLevel = document.getElementById("trainingLevel").value.trim();
                const goal = document.getElementById("goal").value.trim();
                const injuries = document.getElementById("injuries").value.trim();
                const injuryDetails = document.getElementById("injuryDetails").value.trim();
                const reason = document.getElementById("reason").value.trim();
                const routine = document.getElementById("routine").value.trim();

                if (name && phone && age && country && governorate && trainingLevel && goal && injuries && reason && routine) {
                    pendingApplication = { name, phone, age, country, governorate, trainingLevel, goal, injuries, injuryDetails, reason, routine };
                    sendModalOverlay.classList.add("open");
                } else {
                    alert("Please complete all required fields.");
                }
            });

            sendViaWhatsapp.addEventListener("click", () => {
                if (!pendingApplication) return;
                const messageLines = ["New Join Us Application:", ...buildApplicationLines(pendingApplication)];
                const whatsappUrl = `https://wa.me/201144470845?text=${encodeURIComponent(messageLines.join("\n"))}`;
                window.open(whatsappUrl, "_blank");
                joinForm.reset();
                closeSendModal();
            });

            sendViaEmail.addEventListener("click", () => {
                if (!pendingApplication) return;
                const subject = `New Join Us Application from ${pendingApplication.name}`;
                const body = buildApplicationLines(pendingApplication).join("\n");
                const mailtoUrl = `mailto:Mahmouedmohamed785@gmail.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
                window.location.href = mailtoUrl;
                joinForm.reset();
                closeSendModal();
            });

            sendModalCancel.addEventListener("click", closeSendModal);
            sendModalOverlay.addEventListener("click", (e) => {
                if (e.target === sendModalOverlay) closeSendModal();
            });
        });
    </script>
</body>
</html>