<div class="container-fluid">
    <div class="logo">
        <a href="{{route('home')}}">
            <img src="{{ setting('logo')->value ?? '' }}" alt="">
        </a>
    </div>
{{--    <div class="top-social">--}}
{{--        <a href="#"><i class="fa fa-pinterest-p"></i></a>--}}
{{--        <a href="#"><i class="fa fa-linkedin"></i></a>--}}
{{--        <a href="#"><i class="fa fa-pinterest-p"></i></a>--}}
{{--        <a href="#"><i class="fa fa-youtube-play"></i></a>--}}
{{--        <a href="#"><i class="fa fa-instagram"></i></a>--}}
{{--    </div>--}}
    <div class="container">
        <div class="nav-menu" style="text-align: end;">
            <nav class="mainmenu mobile-menu">
                <ul>
                    <li ><a href="{{route('home')}}">Home</a></li>
{{--                    <li><a href="./about-us.html">About us</a></li>--}}
{{--                    <li><a href="./schedule.html">Schedule</a></li>--}}
{{--                    <li><a href="./gallery.html">Gallery</a></li>--}}
{{--                    <li><a href="./blog.html">Blog</a>--}}
{{--                        <ul class="dropdown">--}}
{{--                            <li><a href="./about-us.html">About Us</a></li>--}}
{{--                            <li><a href="./blog-single.html">Blog Details</a></li>--}}
{{--                        </ul>--}}
{{--                    </li>--}}
                    <li><a href="{{route('contact')}}">Contacts</a></li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="mobile-menu-wrap"></div>
</div>
