@extends('web.index')

@section('content')

    <!-- Hero Section Begin -->

    <section class="hero-section">
    @foreach($sliders as $slider)
        <div class="hero-items owl-carousel">
            <div class="single-hero-item set-bg" data-setbg="{{ $slider->image }}">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="hero-text">
                                <h2>{{$slider->title_en}}</h2>
                                <h1>{{$slider->sub_title_en}}</h1>
{{--                                <a href="#" class="primary-btn">{{$slider->button_en}}</a>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    @endforeach

        {{-- <div class="single-hero-item set-bg" data-setbg="img/hero-slider/hero-2.jpg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="hero-text">
                            <h2>Join Us Now</h2>
                            <h1>FITNESS & SPORT</h1>
                            <a href="#" class="primary-btn">Read More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="single-hero-item set-bg" data-setbg="img/hero-slider/hero-3.jpg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="hero-text">
                            <h2>Join Us Now</h2>
                            <h1>FITNESS & SPORT</h1>
                            <a href="#" class="primary-btn">Read More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    </section>
    <!-- Hero End -->

    <!-- Feature Section Begin -->
    {{-- <section class="feature-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-item set-bg" data-setbg="img/feature/feature-1.jpg">
                        <h3>GROUP CLASSES</h3>
                        <p>The Sopranos manages to address the biases<br /> and benefits of therapy</p>
                        <a href="#" class="primary-btn f-btn">Read More</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item set-bg" data-setbg="img/feature/feature-2.jpg">
                        <h3>PERSONAL TRAINING</h3>
                        <p>Strep throat is very common during the flu<br /> seasons and it can be preceded</p>
                        <a href="#" class="primary-btn f-btn">Read More</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item set-bg" data-setbg="img/feature/feature-3.jpg">
                        <h3>Sports Nutrition</h3>
                        <p>A Human Being’s right to life implies his right to<br /> have the free and unrestricted</p>
                        <a href="#" class="primary-btn f-btn">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Feature Section End -->

    <!-- About Section Begin -->
    {{-- <section class="home-about spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="about-text">
                        <h2>WELCOME TO CROSSFIT</h2>
                        <p class="short-details">CrossFit is a cutting-edge functional fitness system that can help
                            everyday men.</p>
                        <p class="long-details">Success isn’t really that difficult. There is a significant portion of
                            the population here in North America, that actually want and need success to be hard! For
                            those of you who are serious about having more, doing more, giving more and being more.</p>
                        <a href="#" class="primary-btn about-btn">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-img">
                        <img src="img/home-about.jpg" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- About Section End -->

    <!-- Classes Section Begin -->
    {{-- <section class="classes-section">
        <div class="class-title set-bg" data-setbg="img/classes-title-bg.jpg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 m-auto text-center">
                        <div class="section-title pl-lg-4 pr-lg-4 pl-0 pr-0">
                            <h2>Choose Your Program</h2>
                            <p>Our Crossfit experts can help you discover new training techniques and exercises that offer a dynamic and efficient full-body workout.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="classes-item set-bg" data-setbg="img/classes/class-1.jpg">
                        <h4>Crossfit Level 1</h4>
                        <p>Sufferers around the globe will be happy to hear that there are sleep apnea remedies.</p>
                        <a href="#" class="primary-btn class-btn">Read More</a>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="classes-item set-bg" data-setbg="img/classes/class-2.jpg">
                        <h4>BootCamp</h4>
                        <p>The oil, also called linseed oil, has many industrial uses – it is an important ingredient
                        </p>
                        <a href="#" class="primary-btn class-btn">Read More</a>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="classes-item set-bg" data-setbg="img/classes/class-3.jpg">
                        <h4>Energy Blast</h4>
                        <p>It is a very common occurrence like cold or fever depending upon your lifestyle. </p>
                        <a href="#" class="primary-btn class-btn">Read More</a>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="classes-item set-bg" data-setbg="img/classes/class-4.jpg">
                        <h4>CLASSIC BODY BALANCE</h4>
                        <p>The procedure is usually a preferred alternative to photorefractive keratectomy,</p>
                        <a href="#" class="primary-btn class-btn">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Classes Section End -->

    <!-- Class Time Section Begin -->
    {{-- <section class="classtime-section class-time-table spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="section-title">
                        <h2>Classtime Table</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="timetable-controls">
                        <ul>
                            <li class="active" data-tsfilter="all">all class</li>
                            <li data-tsfilter="crossfit">crossfit</li>
                            <li data-tsfilter="lunge">lunge ball</li>
                            <li data-tsfilter="ppsr">ppsr</li>
                            <li data-tsfilter="walls">walls</li>
                            <li data-tsfilter="candy">candy</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="classtime-table">
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Monday</th>
                            <th>Tuesday</th>
                            <th>Wednesday</th>
                            <th>Thursday</th>
                            <th>Friday</th>
                            <th>Saturday</th>
                            <th>Sunday</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="workout-time">10.00</td>
                            <td class="hover-bg ts-item" data-tsmeta="crossfit">
                                <span>10.00 - 14.00</span>
                                <h6>Crossfit lv1</h6>
                            </td>
                            <td></td>
                            <td class="hover-bg ts-item" data-tsmeta="crossfit">
                                <span>10.00 - 15.00</span>
                                <h6>Crossfit lv1</h6>
                            </td>
                            <td></td>
                            <td class="hover-bg ts-item" data-tsmeta="lunge">
                                <span>10.00 - 13.00</span>
                                <h6>Lunge Ball Bur</h6>
                            </td>
                            <td></td>
                            <td class="hover-bg ts-item" data-tsmeta="lunge">
                                <span>10.00 - 13.30</span>
                                <h6>Lunge Ball Bur</h6>
                            </td>
                        </tr>
                        <tr>
                            <td class="workout-time">14.00</td>
                            <td></td>
                            <td class="hover-bg ts-item" data-tsmeta="lunge">
                                <span>14.00 - 17.00</span>
                                <h6>Lunge Ball Bur</h6>
                            </td>
                            <td></td>
                            <td class="hover-bg ts-item" data-tsmeta="crossfit">
                                <span>14.00 - 17.00</span>
                                <h6>Crossfit lv1</h6>
                            </td>
                            <td></td>
                            <td class="hover-bg ts-item" data-tsmeta="walls">
                                <span>14.00 - 15.30</span>
                                <h6>Walls to Knees</h6>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="workout-time">16.00</td>
                            <td class="hover-bg ts-item" data-tsmeta="lunge">
                                <span>16.00 - 18.00</span>
                                <h6>Lunge Ball Bur</h6>
                            </td>
                            <td></td>
                            <td class="hover-bg ts-item" data-tsmeta="candy">
                                <span>16.00 - 19.00</span>
                                <h6>Candy</h6>
                            </td>
                            <td></td>
                            <td class="hover-bg ts-item" data-tsmeta="candy">
                                <span>16.00 - 19.00</span>
                                <h6>Candy</h6>
                            </td>
                            <td class="hover-bg ts-item" data-tsmeta="ppsr">
                                <span>16.00 - 17.00</span>
                                <h6>Ppsr</h6>
                            </td>
                            <td class="hover-bg ts-item" data-tsmeta="murph">
                                <span>16.00 - 20.00</span>
                                <h6>murph</h6>
                            </td>
                        </tr>
                        <tr>
                            <td class="workout-time">18.00</td>
                            <td class="hover-bg ts-item" data-tsmeta="walls">
                                <span>18.00 - 20.00</span>
                                <h6>Walls to Knees</h6>
                            </td>
                            <td class="hover-bg ts-item" data-tsmeta="ppsr">
                                <span>18.00 - 20.00</span>
                                <h6>ppsr</h6>
                            </td>
                            <td></td>
                            <td class="hover-bg ts-item" data-tsmeta="chelsea">
                                <span>18.00 - 22.00</span>
                                <h6>Chelsea</h6>
                            </td>
                            <td></td>
                            <td class="hover-bg ts-item" data-tsmeta="annie">
                                <span>18.00 - 22.00</span>
                                <h6>annie</h6>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="workout-time">20.00</td>
                            <td class="hover-bg ts-item" data-tsmeta="lunge">
                                <span>21.00 - 23.00</span>
                                <h6>Lunge Ball Bur</h6>
                            </td>
                            <td class="hover-bg ts-item" data-tsmeta="walls">
                                <span>20.00 - 22.00</span>
                                <h6>Walls to Knees</h6>
                            </td>
                            <td class="hover-bg ts-item" data-tsmeta="walls">
                                <span>20.30 - 23.00</span>
                                <h6>Walls to Knees</h6>
                            </td>
                            <td></td>
                            <td class="hover-bg ts-item" data-tsmeta="crossfit">
                                <span>22.00 - 23.00</span>
                                <h6>Crossfit Lv2</h6>
                            </td>
                            <td></td>
                            <td class="hover-bg ts-item" data-tsmeta="crossfit">
                                <span>21.00 - 23.00</span>
                                <h6>Crossfit Lv2</h6>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section> --}}
    <!-- Class Time Section End -->

    <!-- Price Plan Section Begin -->
    {{-- <section class="price-section spad set-bg" data-setbg="img/price-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2>CHOOSE YOUR PRICING PLAN</h2>
                        <p>These reports started to surface when Congress was having hearings about the<br />
                            painkiller, Vioxx. A Food and Drug Administration staff member.</p>
                    </div>
                    <div class="toggle-option">
                        <ul>
                            <li>Monthly</li>
                            <li>
                                <label class="switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </li>
                            <li>Years</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="single-price-plan">
                        <h4>Normal</h4>
                        <div class="price-plan">
                            <h2>55 <span>$</span></h2>
                            <p>Monthly</p>
                        </div>
                        <ul>
                            <li>Unlimited access to the gym</li>
                            <li>1 classes per week</li>
                            <li>FREE drinking package</li>
                            <li>1 Free personal training</li>
                        </ul>
                        <a href="#" class="primary-btn price-btn">Get Started</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="single-price-plan">
                        <h4>Professional</h4>
                        <div class="price-plan">
                            <h2>95 <span>$</span></h2>
                            <p>Monthly</p>
                        </div>
                        <ul>
                            <li>Unlimited access to the gym</li>
                            <li>2 classes per week</li>
                            <li>FREE drinking package</li>
                            <li>2 Free personal training</li>
                        </ul>
                        <a href="#" class="primary-btn price-btn">Get Started</a>
                        <div class="tic-text">
                            <i class="fa fa-star"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="single-price-plan">
                        <h4>Advanced</h4>
                        <div class="price-plan">
                            <h2>165 <span>$</span></h2>
                            <p>Monthly</p>
                        </div>
                        <ul>
                            <li>Unlimited access to the gym</li>
                            <li>6 classes per week</li>
                            <li>FREE drinking package</li>
                            <li>5 Free personal training</li>
                        </ul>
                        <a href="#" class="primary-btn price-btn">Get Started</a>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Price Plan Section End -->

    <!-- Choseus Section Begin -->
    <section class="chooseus-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2>Why People Choose Us</h2>
                        <p>Our sport experts and latest sports equipment are the winning combination.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach($items as $item)

                <div class="col-lg-4 col-sm-6">
                    <div class="choose-item">
                        <img src="{{$item->image}}" alt="">
                        <h5>{{$item->title_en}}</h5>
                        <p>{{$item->text_en}} </p>
                    </div>
                </div>
                @endforeach()
            </div>
        </div>
    </section>
    <!-- Choseus Section End -->

{{--    @dd(setting('testimonial_image'))--}}
    <!-- Testimonial Section End -->
    <section class="testimonial-section set-bg spad" data-setbg="{{ setting('testimonial_image')->value }}">
        <div class="container">
            <div class="row">
                @foreach($testimonials as $testimonial)

                <div class="col-lg-10 offset-lg-1">
                    <div class="testimonial-slider owl-carousel">
                        <div class="ts-item">
                                @include('system.testimonial.rating',['rating' => $testimonial->rating])

                            <h4>{{ $testimonial->text_en }}</h4>
                            <div class="author-name">
                                <h5>{{ $testimonial->name_en }}</h5>
                                <span>{{ $testimonial->title_en }}</span>
                            </div>
                            <div class="author-pic">
                                <img src="{{ $testimonial->image }}" alt="">
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Testimonial Section End -->


    <!-- Video Section Begin -->
    {{-- <section class="video-section set-bg" data-setbg="img/video-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="video-text">
                        <h2>Gym In Dowtown New York</h2>
                        <a href="https://www.youtube.com/watch?v=X_9VoqR5ojM" class="play-btn video-popup">
                            <i class="fa fa-play"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Video Section End -->

    <!-- Blog Section Begin -->
    {{-- <section class="blog-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="section-title">
                        <h2>From Our Blog</h2>
                        <p>List of all news and events that take place related to us</p>
                    </div>
                </div>
            </div>
            <div class="row blog-gird">
                <div class="grid-sizer"></div>
                <div class="col-lg-4 col-md-6 grid-item">
                    <div class="blog-item large-item set-bg" data-setbg="img/blog/blog-1.jpg">
                        <a href="#" class="blog-text">
                            <div class="categories">Gym & Croosfit</div>
                            <h5>Many people sign up for affiliate programs</h5>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 grid-item">
                    <div class="blog-item instagram-item">
                        <a href="#" class="instagram-text">
                            <div class="categories">Gym & Croosfit <i class="fa fa-instagram"></i></div>
                            <h5>Follow Our Classes Gyming on Instagram # BodyBuilding # photo</h5>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 grid-item">
                    <div class="blog-item small-item set-bg" data-setbg="img/blog/blog-2.jpg">
                        <a href="#" class="blog-text">
                            <div class="categories">Gym & Croosfit</div>
                            <h5>Does Hydroderm Work</h5>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 grid-item">
                    <div class="blog-item large-item xls-large set-bg" data-setbg="img/blog/blog-3.jpg">
                        <a href="#" class="blog-text">
                            <div class="categories">Gym & Croosfit</div>
                            <h5>Many people sign up for affiliate programs</h5>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 grid-item">
                    <div class="blog-item large-item set-bg" data-setbg="img/blog/blog-4.jpg">
                        <a href="#" class="blog-text">
                            <div class="categories">Gym & Croosfit</div>
                            <h5>Many people sign up for affiliate programs</h5>
                        </a>
                        <div class="play-btn">
                            <a href="https://www.youtube.com/watch?v=X_9VoqR5ojM" class="play-in-btn video-popup">
                                <i class="fa fa-play"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 grid-item">
                    <div class="blog-item small-item set-bg" data-setbg="img/blog/blog-5.jpg">
                        <a href="#" class="blog-text">
                            <div class="categories">Gym & Croosfit</div>
                            <h5>Your Antibiotic One Day To 10 Day Options</h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Blog Section End -->

    <!-- Cta Section Begin -->
    {{-- <section class="cta-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cta-text">
                        <h3>GeT Started Today</h3>
                        <p>New student special! $30 unlimited Gym for the first week anh 50% of our member!</p>
                    </div>
                    <a href="#" class="primary-btn cta-btn">book now</a>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Cta Section End -->

    <!-- Map Section Begin -->
    {{-- <div class="map">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d188618.51311104256!2d-71.236572!3d42.381647!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sbd!4v1576756626784!5m2!1sen!2sbd" height="590" style="border: 0" allowfullscreen=""></iframe>
        <div class="map-contact-detalis">
            <div class="open-time">
                <h5>Weekday:</h5>
                <ul>
                    <li>Weekday: <span>06:30 - 11:00</span></li>
                    <li>Saturday: <span>07:00 - 22:00</span></li>
                    <li>Sunday: <span>09:00 - 18:00</span></li>
                </ul>
            </div>
            <div class="map-contact-form">
                <h5>Contact Us</h5>
                <form action="#">
                    <input type="text" placeholder="Name">
                    <input type="text" class="phone" placeholder="Phone">
                    <textarea placeholder="Message"></textarea>
                    <button type="submit" class="web-btn">Submit Now</button>
                </form>
            </div>
        </div>
    </div> --}}
    <!-- Map Section End -->

@endsection
