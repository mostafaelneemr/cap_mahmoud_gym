<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ setting('profile_title') ?? 'Connect with Coach Mahmoud' }}</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b0f19;
            --card-glass: rgba(26, 35, 51, 0.65);
            --card-hover: rgba(38, 51, 74, 0.85);
            --card-border: rgba(255, 255, 255, 0.1);
            --card-border-hover: rgba(16, 185, 129, 0.55);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #10b981;
            --accent-glow: rgba(16, 185, 129, 0.35);
            --accent-hover: #059669;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.25rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Glowing Spheres for Glassmorphism Background */
        body::before {
            content: '';
            position: absolute;
            top: -10%;
            right: 15%;
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.18) 0%, transparent 70%);
            filter: blur(60px);
            z-index: 0;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -10%;
            left: 10%;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.14) 0%, transparent 70%);
            filter: blur(70px);
            z-index: 0;
            pointer-events: none;
        }

        .dashboard-container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        /* Profile & Header Area */
        .profile-section {
            margin-bottom: 2.25rem;
            animation: fadeInDown 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .profile-avatar-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 1.25rem;
        }

        .profile-pic {
            width: 114px;
            height: 114px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent);
            padding: 3px;
            background-color: #1e293b;
            box-shadow: 0 12px 30px var(--accent-glow);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease;
        }

        .profile-avatar-wrapper:hover .profile-pic {
            transform: scale(1.04) rotate(2deg);
            box-shadow: 0 16px 40px rgba(16, 185, 129, 0.5);
        }

        .profile-badge {
            position: absolute;
            bottom: 6px;
            right: 6px;
            background: var(--accent);
            color: #fff;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            border: 2px solid var(--bg-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        .profile-name {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
            letter-spacing: 0.4px;
            background: linear-gradient(135deg, #ffffff 0%, #a7f3d0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .profile-bio {
            font-size: 0.98rem;
            color: var(--text-muted);
            font-weight: 400;
            line-height: 1.55;
            padding: 0 0.5rem;
            margin-bottom: 1.25rem;
        }

        /* Quick Social Icons Bar below bio */
        .quick-social-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .quick-icon-btn {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--card-glass);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            backdrop-filter: blur(12px);
        }

        .quick-icon-btn:hover {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            transform: translateY(-3px) scale(1.08);
            box-shadow: 0 8px 20px var(--accent-glow);
        }

        /* Links List Section */
        .links-section {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both;
        }

        .link-card {
            display: flex;
            align-items: center;
            background: var(--card-glass);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 1.1rem 1.35rem;
            text-decoration: none;
            color: var(--text-main);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            backdrop-filter: blur(16px);
            position: relative;
            overflow: hidden;
            min-height: 64px;
        }

        /* Frosted Glass Glow Edge on Hover */
        .link-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.08), transparent);
            transition: left 0.6s ease;
        }

        .link-card:hover {
            background: var(--card-hover);
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.35), 0 0 0 1px var(--card-border-hover);
            border-color: var(--card-border-hover);
        }

        .link-card:hover::after {
            left: 100%;
        }

        .link-card:active {
            transform: translateY(-1px) scale(0.99);
        }

        /* Link Icon Container */
        .link-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.07);
            margin-right: 1.15rem;
            flex-shrink: 0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .link-card:hover .link-icon-box {
            background: var(--accent);
            border-color: var(--accent);
            transform: scale(1.08) rotate(-4deg);
            box-shadow: 0 6px 15px var(--accent-glow);
        }

        .link-icon-box i {
            font-size: 1.35rem;
            color: var(--accent);
            transition: color 0.3s ease;
        }

        .link-card:hover .link-icon-box i {
            color: #ffffff;
        }

        .link-icon-img {
            width: 26px;
            height: 26px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .link-title {
            font-size: 1.05rem;
            font-weight: 600;
            flex-grow: 1;
            text-align: left;
            letter-spacing: 0.2px;
        }

        .link-arrow {
            color: var(--text-muted);
            font-size: 0.95rem;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .link-card:hover .link-arrow {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(4px);
        }

        /* Empty State */
        .empty-state {
            padding: 2.5rem 1.5rem;
            background: var(--card-glass);
            border-radius: 20px;
            border: 1px solid var(--card-border);
            backdrop-filter: blur(16px);
        }

        /* Footer */
        .footer {
            margin-top: 3.5rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.3s both;
            letter-spacing: 0.3px;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mobile Optimization */
        @media (max-width: 480px) {
            body {
                padding: 2rem 1rem;
            }

            .profile-pic {
                width: 104px;
                height: 104px;
            }

            .profile-name {
                font-size: 1.55rem;
            }

            .link-card {
                padding: 1rem 1.15rem;
                border-radius: 18px;
                min-height: 60px;
            }

            .link-icon-box {
                width: 40px;
                height: 40px;
                margin-right: 0.95rem;
            }

            .link-title {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>

    <div class="dashboard-container">
        <!-- Profile Section -->
        <div class="profile-section">
            <div class="profile-avatar-wrapper">
                @php
                $profileImg = setting('profile_image') ?? setting('logo') ?? 'upload/blank.png';
                $avatarUrl = str_starts_with($profileImg, 'http') ? $profileImg : asset($profileImg);
                @endphp
                <img src="{{ $avatarUrl }}" alt="{{ setting('profile_title') ?? 'Coach Mahmoud' }}" class="profile-pic"
                    onerror="this.src='https://ui-avatars.com/api/?name=Coach+Mahmoud&background=10b981&color=fff&size=256';">
                <div class="profile-badge" title="Verified Coach">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>

            <h1 class="profile-name">{{ setting('profile_title') ?? 'Coach Mahmoud Shaltout' }}</h1>

            <p class="profile-bio">
                {{ setting('profile_bio') ?? 'Official links, social media, and resources for the ultimate fitness
                experience.' }}
            </p>

            <!-- Quick Social Bar -->
            <div class="quick-social-bar">
                @if(isset($links) && count($links) > 0)
                @foreach($links->take(4) as $quickLink)
                <a href="{{ $quickLink->url }}" class="quick-icon-btn" target="_blank" rel="noopener noreferrer"
                    title="{{ $quickLink->title }}">
                    @php
                    $qIcon = trim($quickLink->icon ?? '');
                    $isQImg = str_contains($qIcon, '/') || str_contains($qIcon, '.') || str_starts_with($qIcon, 'http');
                    @endphp
                    @if($isQImg)
                    <img src="{{ str_starts_with($qIcon, 'http') ? $qIcon : asset($qIcon) }}" class="link-icon-img"
                        style="width:20px;height:20px;" alt="{{ $quickLink->title }}">
                    @else
                    <i
                        class="{{ str_starts_with($qIcon, 'fa-') ? $qIcon : 'fa-brands fa-' . strtolower($qIcon != '' ? $qIcon : 'link') }}"></i>
                    @endif
                </a>
                @endforeach
                @endif
            </div>
        </div>

        <!-- Dynamic Links Section -->
        <div class="links-section">
            @forelse($links as $link)
            @php
            $iconVal = trim($link->icon ?? '');
            $isImgIcon = str_contains($iconVal, '/') || str_contains($iconVal, '.') || str_starts_with($iconVal,
            'http');
            @endphp
            <a href="{{ $link->url }}" class="link-card" target="_blank" rel="noopener noreferrer">
                <div class="link-icon-box">
                    @if($isImgIcon)
                    <img src="{{ str_starts_with($iconVal, 'http') ? $iconVal : asset($iconVal) }}"
                        class="link-icon-img" alt="{{ $link->title }}">
                    @else
                    <i
                        class="{{ str_starts_with($iconVal, 'fa-') ? $iconVal : (in_array(strtolower($iconVal), ['link', 'globe', 'phone', 'envelope', 'location-dot', 'dumbbell']) ? 'fa-solid fa-' . strtolower($iconVal) : 'fa-brands fa-' . strtolower($iconVal != '' ? $iconVal : 'link')) }}"></i>
                    @endif
                </div>
                <span class="link-title">{{ $link->title }}</span>
                <div class="link-arrow">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </a>
            @empty
            <div class="empty-state">
                <i class="fa-solid fa-globe"
                    style="font-size: 2.5rem; color: var(--accent); margin-bottom: 1rem; opacity: 0.8;"></i>
                <p style="color: var(--text-muted); font-size: 1.05rem;">Stay tuned! Links are coming soon.</p>
            </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} {{ setting('profile_title') ?? 'Cap Mahmoud Gym' }}. All rights reserved.
        </div>
    </div>

    <script>
        // Automatic FontAwesome fallback class normalizer
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.link-icon-box i, .quick-icon-btn i').forEach(icon => {
                const classList = Array.from(icon.classList);
                const isBrand = ['fa-facebook', 'fa-instagram', 'fa-twitter', 'fa-tiktok', 'fa-youtube', 'fa-whatsapp', 'fa-telegram', 'fa-snapchat', 'fa-linkedin'].some(cls => classList.includes(cls));
                
                if (!isBrand && classList.includes('fa-brands')) {
                    icon.classList.remove('fa-brands');
                    icon.classList.add('fa-solid');
                }
            });
        });
    </script>
</body>

</html>