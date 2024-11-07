/*=========/=========/=========/=========/=========/=========/=========
    File Name: custom.js
    Template Name: Landigoo
    Created By: MelodyThemes
    Envato Profile: http://themeforest.net/user/melodythemes
    Website: https://melodythemes.com
    Version: 1.0
=========/=========/=========/=========/=========/=========/========= */

/* =========/=========/=========/=========/=========/=========/=========
	
	* 1.  // Preloader *
	* 2.  // Smooth Scroll *
	* 3.  // Bootstrap Touch Slider *
	* 4.  // Glyphicon Plus and Minus *
	* 5.  // Add class Active *
	* 6.  // Logo Carousel *
	* 7.  // Testimonial Slider 1 *
	* 8.  // Testimonial Slider 2 *
	* 9.  // Our Team Slider *
	* 10. // Scroll to top *
	* 11. // Fixed menu *
	* 12. // Scroll to top *
	* 13. // WOW *
	* 14. // Accordion 1,2,3,4 and Tabs *
	* 15. // Datepicker *
	* 16. // Selectpicker *
	* 17. // Testimonial Slider 3 *
	* 18. // News Slider *
	* 18. // Comments Slider *
	* 18. // Map *
	
=========/=========/=========/=========/=========/=========/========= */


(function($) {
    "use strict";
			
		/*=========/=========/=========/=========/=========/=========/=========
			Preloader
		=========/=========/=========/=========/=========/=========/=========*/
		
		$(window).on('load', function() { 
		  $('#loader').fadeOut(); 
		  $('#preloader').delay(550).fadeOut('slow'); 
		  $('body').delay(450).css({'overflow':'visible'});
		})
		
		/* =========/=========/=========/=========/=========/=========/=========
			Smooth Scroll
		=========/=========/=========/=========/=========/=========/========= */
		
		$('a.scroll').on("click", function(e) {
			var anchor = $(this);
			$('html, body').stop().animate({
				scrollTop: $(anchor.attr('href')).offset().top - 50
			}, 1000);
			e.preventDefault();
		});
			
		
		//** ----- **//	
		$('body').scrollspy({
			target: '.navbar-collapse',
			offset: 125
		});	
		
		/* =========/=========/=========/=========/=========/=========/=========
			Bootstrap Touch Slider
		=========/=========/=========/=========/=========/=========/========= */
		
		$('#bootstrap-touch-slider').bsTouchSlider();
			var percent = 0,
			interval = 40,
			$bar = $('.transition-timer-carousel-progress-bar'),
			$crsl = $('#bootstrap-touch-slider');
			$('.carousel-indicators li, .carousel-control').click(function (){$bar.css({width:0.5+'%'});});
			$crsl.carousel({
				interval: false,
				pause: true
			}).on('slide.bs.carousel', function (){percent = 0;});
			function progressBarCarousel() {
				$bar.css({width:percent+'%'});
				percent = percent +0.5;
				if (percent>=100) {
					percent=0;
					$crsl.carousel('next');
				}
			}
			var barInterval = setInterval(progressBarCarousel, interval);
			if (!(/Mobi/.test(navigator.userAgent))) {
				$crsl.hover(function(){
							clearInterval(barInterval);
						},
						function(){
							barInterval = setInterval(progressBarCarousel, interval);
						}
				);
			}
			
		/* =========/=========/=========/=========/=========/=========/=========
			Glyphicon Plus and Minus 
		=========/=========/=========/=========/=========/=========/========= */
		
		$(document).ready(function(){
			$(".collapse.in").each(function(){
				$(this).siblings(".panel-heading").find(".glyphicon").addClass("glyphicon-minus").removeClass("glyphicon-plus");
			});
			$(".collapse").on('show.bs.collapse', function(){
				$(this).parent().find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
			}).on('hide.bs.collapse', function(){
				$(this).parent().find(".glyphicon").removeClass("glyphicon-minus").addClass("glyphicon-plus");
			});
		});
		
		/* =========/=========/=========/=========/=========/=========/=========
			Add class Active
		=========/=========/=========/=========/=========/=========/========= */
		
		$('.panel-heading a').click(function() {
			$('.panel-heading').removeClass('active');
			if(!$(this).closest('.panel').find('.panel-collapse').hasClass('in'))
				$(this).parents('.panel-heading').addClass('active');
		 });
		
		/* =========/=========/=========/=========/=========/=========/=========
			Logo Carousel
		=========/=========/=========/=========/=========/=========/========= */
		
		$(document).ready(function(){
			$("#logo-carousel").owlCarousel({
				items:5,
				itemsDesktop        : [1199, 5],
				itemsDesktopSmall   : [980, 3],
				itemsTablet         : [767, 3],
				itemsMobile         : [479, 3],
				pagination: false,
				autoPlay:true
			});
		});


		$(document).ready(function(){
			$("#service-carousel").owlCarousel({
				items:3,
				itemsDesktop        : [1199, 3],
				itemsDesktopSmall   : [980, 2],
				itemsTablet         : [767, 2],
				itemsMobile         : [479, 1],
				pagination: true,
				autoPlay:true
			});
		});
		/* =========/=========/=========/=========/=========/=========/=========
			Testimonial Slider 1
		=========/=========/=========/=========/=========/=========/========= */
		
		$(document).ready(function(){
			$("#testimonial-slider").owlCarousel({
				items:2,
				itemsDesktop:[1000,1],
				itemsDesktopSmall:[979,1],
				itemsTablet:[768,1],
				pagination: true,
				autoPlay:true
			});
		});
		
		/* =========/=========/=========/=========/=========/=========/=========
			Testimonial Slider 2
		=========/=========/=========/=========/=========/=========/========= */
		
		$(document).ready(function(){
			$("#testimonial-slider-1").owlCarousel({
				items:3,
				itemsDesktop:[1000,2],
				itemsDesktopSmall:[979,2],
				itemsTablet:[550,1],
				pagination:false,
				navigation:true,
				navigationText:["",""],
				rewindNav : false,
				autoPlay:true
			});
		});
		
		/* =========/=========/=========/=========/=========/=========/=========
			Our Team Slider 
		=========/=========/=========/=========/=========/=========/========= */
		
		$(document).ready(function(){
			$("#our-team").owlCarousel({
				items:3,
				itemsDesktop        : [1000,3],
				itemsDesktopSmall   : [979,2],
				itemsTablet         : [767, 2],
				itemsMobile         : [479, 1],
				pagination: false,
				navigation: true,
				autoPlay:true
			});
		});
		
		/* =========/=========/=========/=========/=========/=========/=========
			Fixed menu
		=========/=========/=========/=========/=========/=========/========= */
		
		$(window).on('scroll', function () {
			if ($(window).scrollTop() > 50) {
				$('.top-head').addClass('fixed-menu');
			} else {
				$('.top-head').removeClass('fixed-menu');
			}
		});
		
		/* =========/=========/=========/=========/=========/=========/=========
			Scroll to top  
		=========/=========/=========/=========/=========/=========/========= */
		
		if ($('#scroll-to-top').length) {
			var scrollTrigger = 100, // px
				backToTop = function () {
					var scrollTop = $(window).scrollTop();
					if (scrollTop > scrollTrigger) {
						$('#scroll-to-top').addClass('show');
					} else {
						$('#scroll-to-top').removeClass('show');
					}
				};
			backToTop();
			$(window).on('scroll', function () {
				backToTop();
			});
			$('#scroll-to-top').on('click', function (e) {
				e.preventDefault();
				$('html,body').animate({
					scrollTop: 0
				}, 700);
			});
		}
		
		/* =========/=========/=========/=========/=========/=========/=========
			WOW
		=========/=========/=========/=========/=========/=========/========= */
		
		var wow = new WOW(
		  {
			boxClass:     'wow',      // animated element css class (default is wow)
			animateClass: 'animated', // animation css class (default is animated)
			offset:       0,          // distance to the element when triggering the animation (default is 0)
			mobile:       false       // trigger animations on mobile devices (true is default)
		  }
		);
		wow.init();
		
		/* =========/=========/=========/=========/=========/=========/=========
			Accordion 1,2,3,4 and Tabs
		=========/=========/=========/=========/=========/=========/========= */
		
		  $( function() {
			$( "#accordion-0" ).accordion();
			$( "#accordion-1" ).accordion();
			$( "#accordion-2" ).accordion();
			$( "#accordion-3" ).accordion();
		  } );
		  
		    $( function() {
				$( "#tabs" ).tabs();
			  } );
	
		/* =========/=========/=========/=========/=========/=========/=========
			Datepicker
		=========/=========/=========/=========/=========/=========/========= */
		
		 $('.datepicker-date').datepicker();
		 
			 			 
		/* =========/=========/=========/=========/=========/=========/=========
			Selectpicker
		=========/=========/=========/=========/=========/=========/========= */
		
		$('.selectpicker').selectpicker();
		
		
		/* =========/=========/=========/=========/=========/=========/=========
			Testimonial Slider 3
		=========/=========/=========/=========/=========/=========/========= */
		
				var prevButton = '<button type="button" data-role="none" class="slick-prev" aria-label="prev"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" version="1.1"><path fill="#111111" d="M 16,16.46 11.415,11.875 16,7.29 14.585,5.875 l -6,6 6,6 z" /></svg></button>',
			nextButton = '<button type="button" data-role="none" class="slick-next" aria-label="next"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="#111111" d="M8.585 16.46l4.585-4.585-4.585-4.585 1.415-1.415 6 6-6 6z"></path></svg></button>';
		$('.single-item').slick({
			dots: true,
			infinite: true,
			speed: 300,
			slidesToShow: 1,
			adaptiveHeight: true,
			cssEase: 'ease-in-out',
			prevArrow: prevButton,
			nextArrow: nextButton
		});
		$('.quote-container').mousedown(function() {
			$('.single-item').addClass('dragging');
		});
		$('.quote-container').mouseup(function() {
			$('.single-item').removeClass('dragging');
		});
		
		/* =========/=========/=========/=========/=========/=========/=========
			News Slider
		=========/=========/=========/=========/=========/=========/========= */
		
		$(document).ready(function() {
			var nt_example1 = $('#news-slider').newsTicker({
                row_height: 80,
                max_rows: 3,
                duration: 4000,
                prevButton: $('#news-prev'),
                nextButton: $('#news-next')
            });
		
		});
		/* =========/=========/=========/=========/=========/=========/=========
			Comments Slider
		=========/=========/=========/=========/=========/=========/========= */
		
		$(document).ready(function() {
			var nt_example1 = $('#comments-slider').newsTicker({
                row_height: 80,
                max_rows: 2,
                duration: 4000,
                prevButton: $('#comments-prev'),
                nextButton: $('#comments-next')
            });
		
		});
		
		/* =========/=========/=========/=========/=========/=========/=========
			Map
		=========/=========/=========/=========/=========/=========/========= */
		
		$(document).ready( function() {
			$('#map').simpleMap({
				autoLoad: true,
				mapSetup: {
					zoom: 12
				},
				markerConfig: {
					title: "Flaxhost"
				},
				infoWindowConfig:  {
					html: '<h5>Flaxhost<\/h5><p>1004 Floor Number, Building Name Business Location 150150<\/p>'
				}
			});
			$('.sm-trigger').simpleMap({
				mapElementId: "map",
				mapSetup: {
					zoom: 10,
					streetViewControl: true,
					panControl:true
				}
			});
			$('#needToLoadOverlayTrig').simpleMap({
				useOverlay: true,
				infoWindowConfig: {
					autoShow: true,
					html: "<h5>Built in Overlay<\/h5><p>Plugin comes with it's own overlay. Classes conveniently provided for your styling needs.<\/p>"
				}
			});
		});
		
		
		
})(jQuery);