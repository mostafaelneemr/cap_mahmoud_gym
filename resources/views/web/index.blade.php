{{--
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $settings['site_title'] ?? 'Coach Mahmoud Shaltout | Coming Soon' }}</title>
    <meta name="description"
        content="Your Ultimate Fitness & Transformation Journey Starts Soon. Elite online coaching, customized workout programs, and tailored nutrition plans by Cap Mahmoud Shaltout.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            dark: '#0a0a0c',
                            card: '#121216',
                            cardHover: '#181820',
                            red: '#dc2626',
                            redGlow: '#ff1744',
                            accent: '#ef4444',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #0a0a0c;
            color: #f3f4f6;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        /* Ambient Radial Glow Background */
        .ambient-glow-1 {
            position: absolute;
            top: -15%;
            left: 50%;
            transform: translateX(-50%);
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.18) 0%, rgba(10, 10, 12, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }

        .ambient-glow-2 {
            position: absolute;
            bottom: -20%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.12) 0%, rgba(10, 10, 12, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* Glassmorphism Card Style */
        .glass-card {
            background: rgba(18, 18, 22, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .glass-card-glow {
            border: 1px solid rgba(220, 38, 38, 0.25);
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.15);
        }

        /* Glowing Text Effect */
        .text-glow {
            text-shadow: 0 0 25px rgba(220, 38, 38, 0.5);
        }

        /* Red Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #ffffff 30%, #ef4444 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Pulsating Dot */
        .pulse-dot {
            box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
            animation: pulse-red 2s infinite;
        }

        @keyframes pulse-red {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(220, 38, 38, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
            }
        }
    </style>
</head>

<body class="relative min-h-screen flex flex-col justify-between selection:bg-red-600 selection:text-white">

    <!-- Ambient Background Lighting -->
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <!-- Header Navigation -->
    <header class="relative z-10 w-full max-w-7xl mx-auto px-6 py-8 flex items-center justify-between">
        <!-- Logo / Brand Title -->
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            @if(!empty($settings['site_logo']) || !empty($settings['logo']))
            <img src="{{ asset($settings['site_logo'] ?? $settings['logo']) }}" alt="Cap Mahmoud Shaltout"
                class="h-10 w-auto object-contain">
            @else
            <div
                class="w-10 h-10 rounded-xl bg-gradient-to-tr from-red-600 to-red-500 flex items-center justify-center text-white shadow-lg shadow-red-600/30 group-hover:scale-105 transition-transform duration-300">
                <i class="fa-solid fa-dumbbell text-lg"></i>
            </div>
            <div class="flex flex-col">
                <span
                    class="font-heading font-black text-xl tracking-wider text-white group-hover:text-red-500 transition-colors">CAP
                    MAHMOUD</span>
                <span class="text-xs font-semibold text-gray-400 tracking-widest uppercase">SHALTOUT COACHING</span>
            </div>
            @endif
        </a>

        <!-- Status Badge -->
        <div
            class="hidden sm:flex items-center gap-2.5 px-4 py-2 rounded-full bg-brand-card border border-white/10 text-xs font-medium text-gray-300">
            <span class="w-2.5 h-2.5 rounded-full bg-red-600 pulse-dot"></span>
            <span>PLATFORM UNDER DEVELOPMENT</span>
        </div>
    </header>

    <!-- Main Hero Section -->
    <main class="relative z-10 w-full max-w-5xl mx-auto px-6 my-auto text-center py-12">

        <!-- Animated Badge -->
        <div
            class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-600/10 border border-red-600/30 text-red-500 text-xs font-bold tracking-widest uppercase mb-8 shadow-inner">
            <i class="fa-solid fa-bolt text-red-500 animate-bounce"></i>
            <span>GROW STRONGER</span>
        </div>

        <!-- H1 Main Headline -->
        <h1
            class="font-heading text-4xl sm:text-6xl md:text-7xl font-black tracking-tight text-white leading-none mb-6">
            YOUR ULTIMATE <br class="hidden sm:inline">
            <span class="gradient-text text-glow">TRANSFORMATION</span> STARTS SOON
        </h1>

        <!-- Subtitle Description -->
        <p class="max-w-2xl mx-auto text-gray-400 text-base sm:text-lg md:text-xl font-normal leading-relaxed mb-10">
            We are building an all-in-one elite fitness & nutrition experience tailored for your physique, goals, and
            mindset. Get ready for real results.
        </p>

        <!-- Countdown Timer Card -->
        <div class="glass-card glass-card-glow rounded-3xl p-6 sm:p-8 max-w-3xl mx-auto mb-12">
            <div class="grid grid-cols-4 gap-3 sm:gap-6 text-center" id="countdown">
                <div
                    class="flex flex-col items-center justify-center p-3 sm:p-4 rounded-2xl bg-white/5 border border-white/5">
                    <span class="font-heading font-black text-2xl sm:text-4xl text-white mb-1" id="days">00</span>
                    <span class="text-[10px] sm:text-xs font-bold tracking-wider text-gray-400 uppercase">DAYS</span>
                </div>
                <div
                    class="flex flex-col items-center justify-center p-3 sm:p-4 rounded-2xl bg-white/5 border border-white/5">
                    <span class="font-heading font-black text-2xl sm:text-4xl text-white mb-1" id="hours">00</span>
                    <span class="text-[10px] sm:text-xs font-bold tracking-wider text-gray-400 uppercase">HOURS</span>
                </div>
                <div
                    class="flex flex-col items-center justify-center p-3 sm:p-4 rounded-2xl bg-white/5 border border-white/5">
                    <span class="font-heading font-black text-2xl sm:text-4xl text-red-500 mb-1" id="minutes">00</span>
                    <span class="text-[10px] sm:text-xs font-bold tracking-wider text-gray-400 uppercase">MINUTES</span>
                </div>
                <div
                    class="flex flex-col items-center justify-center p-3 sm:p-4 rounded-2xl bg-white/5 border border-white/5">
                    <span class="font-heading font-black text-2xl sm:text-4xl text-red-500 mb-1" id="seconds">00</span>
                    <span class="text-[10px] sm:text-xs font-bold tracking-wider text-gray-400 uppercase">SECONDS</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons & VIP Form -->
        <div class="max-w-xl mx-auto space-y-6">

            <!-- Direct Action WhatsApp Button -->
            @php
            $whatsappNumber = $settings['whatsapp'] ?? ($settings['site_mobile'] ?? '01144470845');
            $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsappNumber);
            $waLink = !empty($cleanWhatsapp) ? "https://wa.me/{$cleanWhatsapp}?text=" . urlencode("Hello Coach Mahmoud
            Shaltout, I want to inquire about online coaching!") : "#";
            @endphp

            <a href="{{ $waLink }}" target="_blank"
                class="w-full inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white font-heading font-bold text-lg shadow-xl shadow-red-600/30 hover:shadow-red-600/50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 group">
                <i
                    class="fa-brands fa-whatsapp text-2xl text-emerald-400 group-hover:scale-110 transition-transform"></i>
                <span>CONNECT ON WHATSAPP NOW</span>
            </a>

            <!-- VIP Early Notification Form -->
            <form action="{{ route('sendmail') }}" method="POST" class="flex flex-col sm:flex-row gap-3 pt-2">
                @csrf
                <input type="hidden" name="subject" value="Coming Soon VIP Waitlist Request">
                <input type="email" name="email" required placeholder="Enter your email to get VIP launch discount..."
                    class="flex-grow px-5 py-4 rounded-2xl bg-brand-card border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-red-600 transition-colors">
                <button type="submit"
                    class="px-6 py-4 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/10 text-white font-semibold text-sm whitespace-nowrap transition-colors flex items-center justify-center gap-2">
                    <span>GET NOTIFIED</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            @if(session('success') || session('status'))
            <div
                class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm font-medium">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') ?? session('status') }}
            </div>
            @endif

        </div>

        <!-- Social Quick Links -->
        <div class="mt-12 flex items-center justify-center gap-4">
            <span class="text-xs font-semibold text-gray-500 tracking-wider uppercase me-2">FOLLOW COACH:</span>


            <a href="#"
                class="w-10 h-10 rounded-xl bg-brand-card hover:bg-red-600/20 border border-white/10 hover:border-red-600/40 flex items-center justify-center text-gray-300 hover:text-red-500 transition-all duration-300">
                <i class="fa-brands fa-instagram text-base"></i>
            </a>
            <a href="#"
                class="w-10 h-10 rounded-xl bg-brand-card hover:bg-red-600/20 border border-white/10 hover:border-red-600/40 flex items-center justify-center text-gray-300 hover:text-red-500 transition-all duration-300">
                <i class="fa-brands fa-tiktok text-base"></i>
            </a>
            <a href="#"
                class="w-10 h-10 rounded-xl bg-brand-card hover:bg-red-600/20 border border-white/10 hover:border-red-600/40 flex items-center justify-center text-gray-300 hover:text-red-500 transition-all duration-300">
                <i class="fa-brands fa-facebook text-base"></i>
            </a>

        </div>




    </main>

    <!-- Footer Section -->
    <footer
        class="relative z-10 w-full max-w-7xl mx-auto px-6 py-6 text-center border-t border-white/5 text-gray-500 text-xs">
        <p>&copy; {{ date('Y') }} Coach Mahmoud Shaltout. All Rights Reserved.</p>
    </footer>

    <!-- Countdown Timer Script -->
    <script>
        // Target Launch Date: 30 days from today
        var targetDate = new Date();
        targetDate.setDate(targetDate.getDate() + 30);

        function updateCountdown() {
            var now = new Date().getTime();
            var distance = targetDate.getTime() - now;

            if (distance < 0) {
                distance = 0;
            }

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('days').innerText = String(days).padStart(2, '0');
            document.getElementById('hours').innerText = String(hours).padStart(2, '0');
            document.getElementById('minutes').innerText = String(minutes).padStart(2, '0');
            document.getElementById('seconds').innerText = String(seconds).padStart(2, '0');
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
</body>

</html> --}}