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
    <!-- Font-awesome  -->
    <link rel="stylesheet" href="{{ asset('roots/css/font-awesome.min.css') }}">
    <!-- Swiper Css -->
    <link rel="stylesheet" href="{{ asset('roots/css/swiper.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"/>
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('roots/css/bootstrap.rtl.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.rtl.css') }}"/>
    @else
        <link rel="stylesheet" href="{{ asset('roots/css/bootstrap.min.css') }}">
    @endif






</head>

<body>

<div id="wrap">
    <!-- Loading Animation-->
    <div id="layout-loading">
        <div class="loader-effect"></div>
    </div>

    <div id="header">
        <div class="navigation fixed-top scroll">
            <div class="container">
                <nav id="navbar-example" class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand brand-logo" href="index.html">
                        <img src="{{ asset('images/logo.png') }}">
                    </a>

                    <button class="navbar-toggler hamburger " style=" visibility: visible;" type="button" data-toggle="collapse" data-target="#nav-content" aria-controls="nav-content" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="hamburger-box">
                            <span class="hamburger-label"></span>
                            <span class="hamburger-inner"></span>
                          </span>
                    </button>

                    <div class="top-nav collapse navbar-collapse" id="nav-content">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a href="#header">{{ __('welcome-view.home') }}</a>
                            </li>
                            <li class="nav-item">
                                <a href="#features">{{ __('welcome-view.explore') }}</a>
                            </li>
                            <li class="nav-item">
                                <a href="#about">{{ __('welcome-view.download') }}</a>
                            </li>
                            <li class="nav-item">
                                <a href="#screenshots">{{ __('welcome-view.screenshots') }}</a>
                            </li>
                            <li class="nav-item">
                                <a href="#contact">{{ __('welcome-view.contact') }}</a>
                            </li>
                            <li class="nav-item">
                                @if(app()->getLocale() == 'ar')
                                    <a href="/">{{ __('welcome-view.arabic') }}</a>
                                @else
                                    <a href="/ar">{{ __('welcome-view.arabic') }}</a>
                                @endif

                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>



<!-- Page intro -->
<div id="hero-bg" class="scroll">
    <div class="container">
        <div class="hero-inner">
            <div class="hero-type">
                <h1> {{ __('welcome-view.join') }}</h1>
                <p>{{ __('welcome-view.download-app') }}</p>
                <div class="download-button">
                    <a href="#" class="mr-2">
                        <i class="fa fa-android d-inline-block mr-2"></i>
                        <span>{{ __('welcome-view.download-w') }}</span>
                    </a>
                    <a href="#">
                        <i class="fa fa-apple d-inline-block mr-2"></i>
                        <span>{{ __('welcome-view.download-w') }}</span>
                    </a>
                </div>
            </div>

            <div class="hero-image">
                @if(app()->getLocale() == 'ar')
                    <img src="{{ asset('images/homepage-ar.png') }}" alt="App" title="">
                @else
                    <img src="{{ asset('images/homepage-en.png') }}" alt="App" title="">
                @endif

            </div>
        </div>

    </div>
</div>
<!-- /. Page intro -->

<!-- Features -->
<section  id="features" class="scroll">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">{{ __('welcome-view.intro') }}</h2>
        </div>
        <div class="row mt-80">
            <div class="col-lg-4 col-sm-6">
                <div class="feature-list">
                <span class="f-icon f-icon-one">
                   <i class="fa fa-map-o"></i>
                </span>
                    <div>
                        <h3>{{ __('welcome-view.h-feature1') }}</h3>
                        <p>{{ __('welcome-view.feature1') }}</p>
                    </div>
                </div>

            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="feature-list ">
                <span class="f-icon f-icon-two">
                  <i class="fa fa-share-alt"></i>
                </span>
                    <h3>{{ __('welcome-view.h-feature2') }}</h3>
                    <p>{{ __('welcome-view.feature2') }}</p>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="feature-list ">
                <span class="f-icon f-icon-three ">
                  <i class="fa fa-id-badge"></i>
                </span>
                    <h3>{{ __('welcome-view.h-feature3') }}</h3>
                    <p>{{ __('welcome-view.feature3') }}</p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6">
                <div class="feature-list ">
                <span class="f-icon f-icon-four ">
                  <i class="fa fa-comments-o"></i>
                </span>
                    <h3>{{ __('welcome-view.h-feature4') }}</h3>
                    <p>{{ __('welcome-view.feature4') }}</p>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="feature-list ">
                <span class="f-icon f-icon-five">
                  <i class="fa fa-folder-open-o"></i>
                </span>
                    <h3>{{ __('welcome-view.h-feature5') }}</h3>
                    <p>{{ __('welcome-view.feature5') }}</p>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="feature-list ">
                <span class="f-icon f-icon-six">
                  <i class="fa fa-globe"></i>
                </span>
                    <h3>{{ __('welcome-view.h-feature6') }}</h3>
                    <p>{{ __('welcome-view.feature6') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.Features -->


<!-- About -->
<section class="features-style-one scroll" id="about" >
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="feature-style-content">

                    <div class="desc-title-icon"><img src="{{ asset('images/icon.png') }}"></div>
                    <h3>{!! __('welcome-view.features') !!}</h3>
                    <p>{!! __('welcome-view.about') !!}</p>
                    <div class="download-button colored mb-1">
                        <a href="#" class="mr-2">
                            <i class="fa fa-android d-inline-block mr-2"></i>
                            <span>{{ __('welcome-view.download-w') }}</span>
                        </a>
                        <a href="#">
                            <i class="fa fa-apple d-inline-block mr-2"></i>
                            <span>{{ __('welcome-view.download-w') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 dflex">
                <img src="{{ asset('images/data-moc.png') }}"  class="animated" alt="App screenshopts"/>
            </div>
        </div>
    </div>
</section>
<!-- ./About -->

<div class="clearfix"></div>

<!-- Screenshots -->
<section class="screenshot_section scroll" id="screenshots">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">{{ __('welcome-view.overview') }}</h2>
            <!--  <p class="sec-title-desc">Demonstrating core competencies to in turn innovate. Create stakeholder engagement so that we
               gain traction.</p> -->
        </div>
    </div>
    <div class="screen_wrap">
        <div class="swiper-container screen_carousel">
            <div class="swiper-wrapper">

                <div class="swiper-slide">
                    <img src="{{ asset('images/1.jpg') }}" alt="App Screen">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/2.jpg') }}" alt="App Screen">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/3.jpg') }}" alt="App Screen">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/4.jpg') }}" alt="App Screen">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/5.jpg') }}" alt="App Screen">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/6.jpg') }}" alt="App Screen">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/7.jpg') }}" alt="App Screen">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/8.jpg') }}" alt="App Screen">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/9.jpg') }}" alt="App Screen">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/10.jpg') }}" alt="App Screen">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/11.jpg') }}" alt="App Screen">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/12.jpg') }}" alt="App Screen">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/13.jpg') }}" alt="App Screen">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/14.jpg') }}" alt="App Screen">
                </div>

            </div>
            <div class="screen-pagination"></div>

        </div>
    </div>
</section>
<!-- ./Screenshots -->

<!-- Contact-->
<section id="contact" class="scroll">
    <div class="container">
        <div class="contact-inner">
            <div class="row-centered">
                <div class="col-centered col-lg-7">

                    <h2 class="title-h2">{{ __('welcome-view.question') }}</h2>

                    <p class="font-p mg-tp-30 mg-bt-60 ">
                        {{ __('welcome-view.contact-us') }}
                    </p>

                </div>
            </div>
            <div class="row">
                <div class=" col-md-6 col-sm-6 col-lg-6 col-12">

                    <div class="content-info">

                        <form action="#" method="post">

                            <div class="form-group">

                                <input type="text" name="name" class="form-control" placeholder="Name">
                            </div>

                            <div class="form-group">

                                <input type="email" name="email" class="form-control" placeholder="Email">

                            </div>

                            <div class="form-group">
                                <textarea class="form-control" rows="4" cols="3" placeholder="Your message "></textarea>

                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-purple">{{ __('welcome-view.send-m') }}
                                </button>
                            </div>
                        </form>

                    </div>

                </div>

                <div class=" col-md-6 col-sm-6 col-lg-6 col-12">

                    <div class="info-icon">

                        <div class="icon-contact bg-icon-1">

                            <img src="{{ asset('images/map.svg') }}" alt="">
                        </div>

                        <div class="content-contact">
                            <h3>{{ __('welcome-view.address-w') }}</h3>
                            <p>{{ __('welcome-view.address') }}</p>

                        </div>

                    </div>

                    <!--  <div class="info-icon">

                         <div class="icon-contact bg-icon-2">

                             <img src="assets/images/phone.svg" alt="">
                         </div>

                         <div class="content-contact">
                             <h3>Phone</h3>
                             <p><a href="tel:+966531089888"> <i class="fa fa-phone"></i><span class="iq-fw-5">+966531089888</span></a></p>

                         </div>

                     </div> -->

                    <div class="info-icon">
                        <div class="icon-contact bg-icon-3">

                            <img src="{{ asset('images/email.svg') }}" alt="">
                        </div>

                        <div class="content-contact">
                            <h3>{{ __('welcome-view.email') }}</h3>
                            <p><a href="mailto:Info@drb.com?subject=&quot;Home msil&quot;">Info@drb.com</a></p>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>


    <footer class="top60">
        <div class="container">
            <div class="top-footer">
                <div>
                    <a href="#" class="mr-2"><img src="{{ asset('images/logo2.png') }}" alt="brand"></a>
                    <a href="#"><span>{{ __('welcome-view.get-start') }}</span></a>
                    <span>
                        {{ __('welcome-view.intro') }}

                    </span>
                </div>
                <div class=" footer-social">
                    <a href="https://www.facebook.com/Wakeb.tech/" class="facebook" arget="_bank" title="facebook"><i class="fa fa-facebook" ></i></a>
                    <a href="https://twitter.com/WAKEB_Data" class="twitter"><i class="fa fa-twitter" arget="_bank" title="twitter"></i></a>
                    <a href="https://www.linkedin.com/company/wakeb-data" class="linkedin" arget="_bank" title="linkedin"><i class="fa fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="btm-footer container-fluid">
            <center>{!! __('welcome-view.copy-rights') !!} </center>
        </div>
    </footer>

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
