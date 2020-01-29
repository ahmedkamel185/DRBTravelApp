
//Loading
$(window).on('load', function() {
    $('.loader-effect').fadeOut();
    $('#layout-loading').delay(150).fadeOut('slow');

});

$(document).ready(function() {

    'use strict';

    /*-----------------------------
		HEADER FIXED JS
	-------------------------------*/
    var wind = $(window);
    var sticky = $(".navigation");
    wind.on("scroll", function() {
        var scroll = wind.scrollTop();
        if (scroll < 1) {
            sticky.removeClass("nav-fixed");
        } else {
            sticky.addClass("nav-fixed");
        }
    });

    /*-----------------------------
        SMOOTH SCROLL JS
    -------------------------------*/
    var sections = $('.scroll')
        , nav = $('nav')
        , nav_height = nav.outerHeight();


    var cur_pos = $(this).scrollTop();
    sections.each(function() {
        var top = $(this).offset().top - nav_height,
            bottom = top + $(this).outerHeight();
        if (cur_pos >= top && cur_pos <= bottom) {
            nav.find('a').removeClass('active');
            sections.removeClass('active');

            $(this).addClass('active');
            nav.find('a[href="#'+$(this).attr('id')+'"]').addClass('active');
        }
    });
    $(window).on('scroll', function () {
        var cur_pos = $(this).scrollTop();
        sections.each(function() {
            var top = $(this).offset().top - nav_height,
                bottom = top + $(this).outerHeight();
            if (cur_pos >= top && cur_pos <= bottom) {
                nav.find('a').removeClass('active');
                sections.removeClass('active');

                $(this).addClass('active');
                nav.find('a[href="#'+$(this).attr('id')+'"]').addClass('active');
            }
        });
    });




    $(function() {
        $('.navigation ul li a, .download-btn ul li a').on('click', function() {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top
                    }, 800);
                    return false;
                }
            }
        });
    });

    $('.navbar-collapse a').on('click', function() {
        $(".navbar-collapse").collapse('hide');
        $('.hamburger').removeClass('is-active collapsed')
    });

    /* Toggle menu button*/
    $('.hamburger').on('click', function() {
        $(this).toggleClass('is-active','fast');
    })

});