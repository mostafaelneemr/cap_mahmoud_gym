@extends('web.index')

@section('content')

    <section class="breadcrumb-section set-bg spad" data-setbg="{{asset('assets/web/img/about-bread.jpg')}}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-text">
                        <h2>Contact</h2>
                        <div class="breadcrumb-controls">
                            <a href="{{route('home')}}"><i class="fa fa-home"></i> Home</a>
                            <span>Contact</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="contact-info">
                        <h4>Information</h4>
                        <ul>
                            <li><i class="fa fa-phone"></i>
                                <a style="color: #afb4bf;" href="https://wa.me/{{setting('mobile')->value ?? ''}}">{{setting('mobile')->value ?? ''}}</a>
                            </li>
                            <li><i class="fa fa-envelope"></i>
                                <a style="color: #afb4bf;" href="mailto:{{setting('email')->value ?? ''}}">{{ setting('email')->value ?? ''}}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="contact-address">
                        <h4>Address</h4>
                        <ul>
                            <li><i class="fa fa-map-marker"></i>{{setting('address')->value ?? ''}}</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-8 offset-lg-1">
                    <div class="contact-form">
                        <h4>Get in touch</h4>
                        <div id="form-alert-message"></div>
                        <form name="contact-form" action="{{route('sendmail')}}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <input type="text" name="name" placeholder="Name" required>
                                </div>
                                <div class="col-lg-6">
                                    <input type="email" name="email" placeholder="Email" required>
                                </div>
                                <div class="col-lg-12">
                                    <input type="tel" name="telephone" placeholder="Telephone" required>
                                </div>
                                <div class="col-lg-12">
                                    <textarea placeholder="Message" name="message" required></textarea>
                                    <button type="submit" class="c-btn">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
