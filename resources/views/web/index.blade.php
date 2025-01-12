<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Activitar Template">
    <meta name="keywords" content="Activitar, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset('assets/web/img/icons/logo.ico') }}" />
    <title>
        @yield('title')
    </title>

    @include('web.partials.style')

</head>

<body>
<!-- Page Preloder -->
<div id="preloder">
    <div class="loader"></div>
</div>

<!-- Header Section Begin -->
<header class="header-section">
    @include('web.partials.header')
</header>

<!-- Header End -->

@yield('content')
@include('web.book-now')

<!-- Footer Section Begin -->
<footer class="footer-section">
    @include('web.partials.footer')
</footer>
<!-- Footer Section End -->

<!-- Js Plugins -->

@include('web.partials.script')

</body>

</html>
