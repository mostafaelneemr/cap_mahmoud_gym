<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $pageTitle ?? 'Mahmoud Shaltout Fitness Center' }}</title>

    @include('web.partials.style')
    <style>
        .web-alert {
            margin: 15px 25px 0 25px;
            padding: 14px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            position: relative;
            z-index: 100;
            animation: fadeIn 0.3s ease;
        }
        .web-alert-success {
            background-color: rgba(37, 211, 102, 0.15);
            border: 1px solid #25D366;
            color: #25D366;
        }
        .web-alert-error {
            background-color: rgba(168, 0, 0, 0.2);
            border: 1px solid #a80000;
            color: #ff6b6b;
        }
        .web-alert .alert-close {
            margin-left: auto;
            background: none;
            border: none;
            color: inherit;
            font-size: 20px;
            cursor: pointer;
            line-height: 1;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="main-container">
        @include('web.partials.header')

        @if(session('success'))
            <div class="web-alert web-alert-success" id="flashAlert">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="alert-close" onclick="document.getElementById('flashAlert').remove()">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="web-alert web-alert-error" id="flashAlert">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="alert-close" onclick="document.getElementById('flashAlert').remove()">&times;</button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="web-alert web-alert-error" id="flashAlert">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
                <button type="button" class="alert-close" onclick="document.getElementById('flashAlert').remove()">&times;</button>
            </div>
        @endif

        @yield('content')

        @include('web.partials.footer')
    </div>

    @include('web.partials.script')

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
