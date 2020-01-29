<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        @yield("title")
    </title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('roots/css/bootstrap.min.css') }}">
    <!-- Font-awesome  -->
    <link rel="stylesheet" href="{{ asset('roots/css/font-awesome.min.css') }}">
    <!-- Swiper Css -->
    <link rel="stylesheet" href="{{ asset('roots/css/swiper.min.css') }}"/>
    <!-- Style Css -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"/>


</head>

<body>

<div id="wrap">
    <!-- Loading Animation-->
    <div id="layout-loading">
        <div class="loader-effect"></div>
    </div>

        <!-- Header -->
    @yield("header")

    <!-- Content -->
    @yield("footer")


</div>


<!-- Scripts -->


<!--Js-->
<script src="{{ asset('roots/js/jquery-3.3.1.min.js') }}"></script>
<!-- Bootstrap JS -->
<script src="{{ asset('roots/js/bootstrap.min.js') }}"></script>
<!--swiper-->
<script src="{{ asset('js/swiper.min.js') }}"></script>
<script>

    jQuery(document).ready(function($)
    {

        function getSlide() {
            var wW = $(window).width();
            if (wW < 601) {
                return 1;
            }
            return 3;
        }
        var mySwiper = $('.screen_carousel').swiper({
            mode:'horizontal',
            loop: true,
            speed: 1000,
            autoplay: 2000,
            effect: 'coverflow',
            slidesPerView: getSlide(),
            grabCursor: true,
            pagination: '.screen-pagination',
            paginationClickable: true,
            nextButton: '.arrow-right',
            prevButton: '.arrow-left',
            keyboardControl: true,
            coverflow: {
                rotate: 0,
                stretch: 90,
                depth: 200,
                modifier: 1,
                slideShadows : true
            }
        });

    });
</script>

<!-- Bootstrap JS -->
<script src="{{ asset('js/functions.js') }}"></script>

</body>
</html>
