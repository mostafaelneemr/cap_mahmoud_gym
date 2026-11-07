@extends('web.layout')

@section('content')

    <section class="hero-section" @if(!empty($hero['image'])) style="background-image: linear-gradient(90deg, rgba(0,0,0,0.94) 0%, rgba(0,0,0,0.82) 32%, rgba(0,0,0,0.4) 60%, rgba(0,0,0,0.15) 100%), url('{{ $hero['image'] }}');" @endif>
        <div class="hero-content">
            <span class="sub-heading">{{ $hero['tagline'] ?? 'GROW STRONGER' }}</span>
            <h1>{!! !empty($hero['title']) ? e($hero['title']) : 'Your <span class="highlight">Fitness</span> Journey Starts Here' !!}</h1>
            <p>{{ $hero['subtitle'] ?? 'Ignite your inner power, break your limits, and start building the stronger version of yourself today.' }}</p>
            <a href="{{ route('web.join-us') }}" class="btn-primary">
                {{ $hero['button_text'] ?? 'JOIN US' }} <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>
    </section>

    <section class="about-section reveal">
        <span class="section-tag">{{ $about['tagline'] ?? 'About' }}</span>
        <h2>{{ $about['title'] ?? 'Built For Everyone Powered By Passion' }}</h2>
        <p class="description">"{{ $about['bio'] ?? "We don't just build bodies; we build lifestyles. We tailor every data-driven program to your unique needs, where your progress is our only priority." }}"</p>

        <div class="features-grid">
            @php
                $features = !empty($about['features']) ? $about['features'] : [
                    ['icon' => 'fa-solid fa-dumbbell', 'title' => 'Custom Plans'],
                    ['icon' => 'fa-solid fa-chart-line', 'title' => 'Data Driven'],
                    ['icon' => 'fa-solid fa-trophy', 'title' => 'Elite Coaching'],
                    ['icon' => 'fa-solid fa-comments', 'title' => '24/7 Support'],
                ];
                $leftFeatures = array_slice($features, 0, 2);
                $rightFeatures = array_slice($features, 2, 2);
            @endphp

            @foreach($leftFeatures as $feat)
                <div class="feature-item">
                    <div class="feature-icon-circle">
                        <i class="{{ $feat['icon'] ?? 'fa-solid fa-dumbbell' }}"></i>
                    </div>
                    <span>{{ $feat['title'] }}</span>
                </div>
            @endforeach

            <div class="about-center-img">
                <img src="{{ !empty($about['image']) ? $about['image'] : asset('images/about-coach.png') }}" alt="{{ $about['title'] ?? 'Coach' }}">
            </div>

            @foreach($rightFeatures as $feat)
                <div class="feature-item">
                    <div class="feature-icon-circle">
                        <i class="{{ $feat['icon'] ?? 'fa-solid fa-dumbbell' }}"></i>
                    </div>
                    <span>{{ $feat['title'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="performance-section">
        <div class="perf-card-img reveal" @if(!empty($quote['image'])) style="background-image: url('{{ $quote['image'] }}');" @endif></div>

        <div class="perf-card-text reveal">
            <h2>"{{ $quote['title'] ?? 'Turning Your Potential into Performance' }}"</h2>
            <p>"{{ $quote['text'] ?? "I believe that every body has a story, and yours is waiting to be written. By focusing deeply on science-based training and sustainable habits, I don't just help you reach your goals—I help you redefine what's possible. My approach is simple: precision, consistency, and a partnership committed to your lasting success." }}"</p>
            <div>
                <a href="{{ route('web.contact') }}" class="btn-primary">
                    {{ $quote['button_text'] ?? 'Contact Us' }} <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="services-section reveal">
        <div class="services-header">
            <h2>{{ $service['tagline'] ?? 'Our Services' }}</h2>
            <h3>{{ $service['title'] ?? 'Your Path to the Top' }}</h3>
            <p>"{{ $service['subtitle'] ?? "We don't just provide generic workout plans; we build a complete system that evolves with you. Here are the services we offer to ensure you reach your goals efficiently and safely" }}"</p>
        </div>

        <div class="services-content">
            @php
                $serviceItems = !empty($service['items']) ? $service['items'] : [];
                $mainService = $serviceItems[0] ?? [
                    'icon' => 'fa-solid fa-signal',
                    'title' => 'online training',
                    'description' => 'Professional personal training delivered wherever you are. Get full coaching, a custom workout plan, and technique guidance online, as if we were training together in person.'
                ];
                $subServices = count($serviceItems) > 1 ? array_slice($serviceItems, 1) : [
                    [
                        'title' => 'Nutritional Guidance',
                        'description' => 'Precise and smart nutrition plans that help you reach your goals without deprivation, focusing on building sustainable healthy habits.'
                    ],
                    [
                        'title' => 'Progress Tracking',
                        'description' => 'Precise and continuous monitoring of your results, with periodic adjustments to your program to ensure constant improvement and break plateaus.'
                    ]
                ];
            @endphp

            <div class="services-cards">
                <div class="service-card-main">
                    <i class="{{ $mainService['icon'] ?? 'fa-solid fa-signal' }}"></i>
                    <h4 dir="ltr">{{ $mainService['title'] }}</h4>
                    <p dir="ltr">{{ $mainService['description'] }}</p>
                </div>

                <div class="service-sub-grid">
                    @foreach($subServices as $sub)
                        <div class="service-card-sub">
                            <h5>{{ $sub['title'] }}</h5>
                            <p>{{ $sub['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="services-logo-side">
                <img src="{{ !empty($global['site_logo']) ? $global['site_logo'] : asset('assets/web/img/logo.png') }}" alt="{{ $global['site_title'] ?? 'Mahmoud Shaltout Brand Logo' }}">
            </div>
        </div>
    </section>

    <section class="why-us-section reveal">
        <h2>{{ $whyUs['title'] ?? "Here's Why Members Love Us" }}</h2>
        <div class="why-us-grid">
            @php
                $whyUsItems = !empty($whyUs['items']) ? $whyUs['items'] : [
                    ['number' => '01', 'icon' => 'fa-solid fa-handshake', 'title' => 'Friendly & Professional Coaching', 'subtitle' => 'The reason most members stay'],
                    ['number' => '02', 'icon' => 'fa-solid fa-dumbbell', 'title' => 'Personalized Training', 'subtitle' => 'Plans built around your goals'],
                    ['number' => '03', 'icon' => 'fa-solid fa-users', 'title' => 'Community Support & Challenges', 'subtitle' => 'You never train alone'],
                    ['number' => '04', 'icon' => 'fa-solid fa-dollar-sign', 'title' => 'Affordable Coaching Plans', 'subtitle' => 'Real results without overpaying'],
                ];
            @endphp

            @foreach($whyUsItems as $item)
                <div class="why-us-card">
                    <span class="why-us-rank">{{ $item['number'] }}</span>
                    <div class="why-us-icon">
                        <i class="{{ $item['icon'] ?? 'fa-solid fa-dumbbell' }}"></i>
                    </div>
                    <div class="why-us-text">
                        <p>{{ $item['title'] }}</p>
                        @if(!empty($item['subtitle']))
                            <span class="why-us-note">{{ $item['subtitle'] }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="cta-section reveal">
        <h3>{{ __('LET\'S START YOUR TRAINING TODAY') }}</h3>
        <a href="{{ route('web.contact') }}" class="btn-secondary">{{ __('CONTACT US') }}</a>
    </section>
@endsection
