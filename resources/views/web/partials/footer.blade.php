<footer>
    <div class="footer-top">
        <div class="footer-logo">
            <a href="{{ route('web.home') }}">
                <img src="{{ !empty($global['site_logo']) ? $global['site_logo'] : asset('assets/web/img/logo.png') }}" alt="{{ $global['site_title'] ?? 'Mahmoud Shaltout' }} Logo">
            </a>
        </div>
        <div class="footer-socials">
            @if(!empty($global['social_links']['instagram']))
                <a href="{{ $global['social_links']['instagram'] }}" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            @else
                <a href="https://www.instagram.com/mahmoudshaltout2823" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            @endif

            @if(!empty($global['social_links']['whatsapp']))
                <a href="{{ $global['social_links']['whatsapp'] }}" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
            @else
                <a href="https://wa.me/201144470845" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
            @endif

            @if(!empty($global['social_links']['tiktok']))
                <a href="{{ $global['social_links']['tiktok'] }}" target="_blank" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
            @else
                <a href="https://www.tiktok.com/@mahmoudshaltout2823?_r=1&_t=ZS-9857s4cJBuD" target="_blank" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
            @endif

            @if(!empty($global['social_links']['facebook']))
                <a href="{{ $global['social_links']['facebook'] }}" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
            @endif
        </div>
    </div>
    <div class="footer-bottom">
        <p>{{ $global['copyright'] ?? ('Copyright© ' . date('Y') . ' Mahmoud Shaltout . All Right Reserved') }}</p>
    </div>
</footer>
