@extends('web.layout')

@section('content')

    <section class="hero-section">
        <div class="hero-content">
            <span class="sub-heading">GROW STRONGER</span>
            <h1>Your <span class="highlight">Fitness</span> Journey Starts Here</h1>
            <p>Ignite your inner power, break your limits, and start building the stronger version of yourself today.</p>
            <a href="join_us.html" class="btn-primary">
                JOIN US <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>
    </section>

    <section class="about-section reveal">
        <span class="section-tag">About</span>
        <h2>Built For Everyone Powered By Passion</h2>
        <p class="description">"We don't just build bodies; we build lifestyles. We tailor every data-driven program to your unique needs, where your progress is our only priority."</p>

        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon-circle">
                    <i class="fa-solid fa-dumbbell"></i>
                </div>
                <span>Custom Plans</span>
            </div>

            <div class="feature-item">
                <div class="feature-icon-circle">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <span>Data Driven</span>
            </div>

            <div class="about-center-img">
                <img src="images/about-coach.png" alt="Coach Transformation">
            </div>

            <div class="feature-item">
                <div class="feature-icon-circle">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <span>Elite Coaching</span>
            </div>

            <div class="feature-item">
                <div class="feature-icon-circle">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <span>24/7 Support</span>
            </div>
        </div>
    </section>

    <section class="performance-section">
        <div class="perf-card-img reveal"></div>

        <div class="perf-card-text reveal">
            <h2>"Turning Your Potential into Performance"</h2>
            <p>"I believe that every body has a story, and yours is waiting to be written. By focusing deeply on science-based training and sustainable habits, I don't just help you reach your goals—I help you redefine what's possible. My approach is simple: precision, consistency, and a partnership committed to your lasting success."</p>
            <div>
                <a href="contact_us.html" class="btn-primary">
                    Contact Us <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
            </div>
        </div>

    </section>

    <section class="services-section reveal">
        <div class="services-header">
            <h2>Our Services</h2>
            <h3>Your Path to the Top</h3>
            <p>"We don't just provide generic workout plans; we build a complete system that evolves with you. Here are the services we offer to ensure you reach your goals efficiently and safely"</p>
        </div>

        <div class="services-content">
            <div class="services-cards">
                <div class="service-card-main">
                    <i class="fa-solid fa-signal"></i>
                    <h4 dir="ltr">online training</h4>
                    <p dir="ltr">Professional personal training delivered wherever you are. Get full coaching, a custom workout plan, and technique guidance online, as if we were training together in person.</p>
                </div>

                <div class="service-sub-grid">
                    <div class="service-card-sub">
                        <h5>Nutritional Guidance</h5>
                        <p>Precise and smart nutrition plans that help you reach your goals without deprivation, focusing on building sustainable healthy habits.</p>
                    </div>
                    <div class="service-card-sub">
                        <h5>Progress Tracking</h5>
                        <p>Precise and continuous monitoring of your results, with periodic adjustments to your program to ensure constant improvement and break plateaus.</p>
                    </div>
                </div>
            </div>

            <div class="services-logo-side">
                <img src="images/logo.png" alt="Mahmoud Shaltout Brand Logo">
            </div>
        </div>
    </section>

    <section class="why-us-section reveal">
        <h2>Here's Why Members Love Us</h2>
        <div class="why-us-grid">
            <div class="why-us-card">
                <span class="why-us-rank">01</span>
                <div class="why-us-icon">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <div class="why-us-text">
                    <p>Friendly & Professional Coaching</p>
                    <span class="why-us-note">The reason most members stay</span>
                </div>
            </div>

            <div class="why-us-card">
                <span class="why-us-rank">02</span>
                <div class="why-us-icon">
                    <i class="fa-solid fa-dumbbell"></i>
                </div>
                <div class="why-us-text">
                    <p>Personalized Training</p>
                    <span class="why-us-note">Plans built around your goals</span>
                </div>
            </div>

            <div class="why-us-card">
                <span class="why-us-rank">03</span>
                <div class="why-us-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="why-us-text">
                    <p>Community Support & Challenges</p>
                    <span class="why-us-note">You never train alone</span>
                </div>
            </div>

            <div class="why-us-card">
                <span class="why-us-rank">04</span>
                <div class="why-us-icon">
                    <i class="fa-solid fa-dollar-sign"></i>
                </div>
                <div class="why-us-text">
                    <p>Affordable Coaching Plans</p>
                    <span class="why-us-note">Real results without overpaying</span>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section reveal">
        <h3>LET'S START YOUR TRAINING TODAY</h3>
        <a href="contact_us.html" class="btn-secondary">CONTACT US</a>
    </section>


@endsection
