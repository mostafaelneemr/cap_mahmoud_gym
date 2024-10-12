<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="footer-logo-item">
                <div class="f-logo">
                    <a href="{{route('home')}}"><img src="{{ setting('logo')->value ?? '' }}" alt=""></a>
                </div>
                <p>Despite growth of the Internet over the past seven years, the use of toll-free phone numbers
                    in television advertising continues.</p>
                <div class="social-links">
                    <h6>Follow us</h6>
                    <a href="{{ setting('facebook')->value ?? '' }} " target="_blank"><i class="fa fa-facebook"></i></a>
                    <a href="{{ setting('tiktok')->value ?? '' }} " target="_blank" class="font-weight-bold"><i class="fa-brands fa-tiktok"></i>T</a>
                    <a href="{{ setting('google')->value ?? '' }} " target="_blank"><i class="fa fa-google"></i></a>
                    <a href="{{ setting('linkedin')->value ?? '' }} " target="_blank"><i class="fa fa-linkedin"></i></a>
                    <a href="{{ setting('instagram')->value ?? '' }} " target="_blank"><i class="fa fa-instagram"></i></a>
                </div>
            </div>
        </div>
{{--        <div class="col-lg-3 offset-lg-1">--}}
{{--            <div class="footer-widget">--}}
{{--                <h5>Our Blog</h5>--}}
{{--                <div class="footer-blog">--}}
{{--                    <a href="#" class="fb-item">--}}
{{--                        <h6>Most people who work</h6>--}}
{{--                        <span class="blog-time"><i class="fa fa-clock-o"></i> Jan 02, 2019</span>--}}
{{--                    </a>--}}
{{--                    <a href="#" class="fb-item">--}}
{{--                        <h6>Freelance Design Tricks How </h6>--}}
{{--                        <span class="blog-time"><i class="fa fa-clock-o"></i> Jan 02, 2019</span>--}}
{{--                    </a>--}}
{{--                    <a href="#" class="fb-item">--}}
{{--                        <h6>have a computer at home have had </h6>--}}
{{--                        <span class="blog-time"><i class="fa fa-clock-o"></i> Jan 02, 2019</span>--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="col-lg-2">--}}
{{--            <div class="footer-widget">--}}
{{--                <h5>Program</h5>--}}
{{--                <ul class="workout-program">--}}
{{--                    <li><a href="#">Bodybuilding</a></li>--}}
{{--                    <li><a href="#">Running</a></li>--}}
{{--                    <li><a href="#">Streching</a></li>--}}
{{--                    <li><a href="#">Weight Loss</a></li>--}}
{{--                    <li><a href="#">Gym Fitness</a></li>--}}
{{--                </ul>--}}
{{--            </div>--}}
{{--        </div>--}}
        <div class="col-lg-4">
            <div class="footer-widget">
                <h5>Get Info</h5>
                <ul class="footer-info">
                    <li>
                        <i class="fa fa-phone"></i> <span>Phone:</span>
                        <a style="color: #afb4bf;" href="https://wa.me/{{ setting('mobile')->value ?? '' }}">{{ setting('mobile')->value ?? '' }}</a>
                    </li>
                    <li>
                        <i class="fa fa-envelope-o"></i> <span>Email:</span>
                        <a style="color: #afb4bf;" href="mailto:{{setting('email')->value ?? ''}}">{{ setting('email')->value ?? ''}}</a>
                    </li>
                    <li>
                        <i class="fa fa-map-marker"></i> <span>Address</span> {{ setting('address')->value ?? '' }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="copyright-text">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="ct-inside">
                    Copyright &copy;<script>document.write(new Date().getFullYear());</script>
                    All rights reserved | This template is made with <i class="fa fa-heart-o" aria-hidden="true"></i>
                    by <a href="https://www.elnemr.info/" target="_blank">Elnemr</a>
                </div>
            </div>
        </div>
    </div>
</div>
