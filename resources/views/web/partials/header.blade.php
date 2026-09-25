<header class="navbar">
    <div class="logo">
        <a href="{{ route('web.home') }}">
            <img src="{{ !empty($global['site_logo']) ? $global['site_logo'] : asset('assets/web/img/logo.png') }}" alt="{{ $global['site_title'] ?? 'Mahmoud Shaltout' }} Logo">
        </a>
    </div>

    <nav class="nav-links">
        <a href="{{ route('web.home') }}" class="{{ request()->routeIs('web.home') ? 'active-link' : '' }}">{{ __('Home') }}</a>
        <a href="{{ route('web.transformations') }}" class="{{ request()->routeIs('web.transformations') ? 'active-link' : '' }}">{{ __('Transformations') }}</a>
        <a href="{{ route('web.join-us') }}" class="{{ request()->routeIs('web.join-us') ? 'active-link' : '' }}">{{ __('Join Us') }}</a>
        <a href="{{ route('web.contact') }}" class="{{ request()->routeIs('web.contact') ? 'active-link' : '' }}">{{ __('Contact Us') }}</a>
    </nav>

    <div class="nav-right-actions">
        <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle Navigation" aria-expanded="false" aria-controls="mobileNav">
            <i class="fa-solid fa-bars"></i>
        </button>

        <a href="{{ auth()->check() ? route('system.dashboard') : (Route::has('login') ? route('login') : url('/system/login')) }}" style="text-decoration:none; color:inherit;" title="{{ auth()->check() ? __('Dashboard') : __('Login') }}">
            <div class="user-profile">
                <i class="fa-regular fa-user"></i>
            </div>
        </a>
    </div>

    <nav class="mobile-nav-menu" id="mobileNav">
        <a href="{{ route('web.home') }}" class="{{ request()->routeIs('web.home') ? 'active-link' : '' }}">{{ __('Home') }}</a>
        <a href="{{ route('web.transformations') }}" class="{{ request()->routeIs('web.transformations') ? 'active-link' : '' }}">{{ __('Transformations') }}</a>
        <a href="{{ route('web.join-us') }}" class="{{ request()->routeIs('web.join-us') ? 'active-link' : '' }}">{{ __('Join Us') }}</a>
        <a href="{{ route('web.contact') }}" class="{{ request()->routeIs('web.contact') ? 'active-link' : '' }}">{{ __('Contact Us') }}</a>
    </nav>
</header>
