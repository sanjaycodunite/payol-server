

(function($) {
    "use strict";
    
    
    $(document).on ('ready', function (){

        $.fn.visible = function(partial) {
            var $t            = $(this),
                $w            = $(window),
                viewTop       = $w.scrollTop(),
                viewBottom    = viewTop + $w.height(),
                _top          = $t.offset().top,
                _bottom       = _top + $t.height(),
                compareTop    = partial === true ? _bottom : _top,
                compareBottom = partial === true ? _top : _bottom;
          
          return ((compareBottom <= viewBottom) && (compareTop >= viewTop));
        };


        // toggleClass
        $(document).on('click', '[data-toggle-class]', function (e) {
          var $self = $(this);
          var attr = $self.attr('data-toggle-class');
          var target = $self.attr('data-toggle-class-target') || $self.attr('data-target');
          var closest = $self.attr('data-target-closest');
          var classes = ( attr && attr.split(',')) || '',
            targets = (target && target.split(',')) || Array($self),
            key = 0;
          $.each(classes, function( index, value ) {
            var target = closest ? $self.closest(targets[(targets.length == 1 ? 0 : key)]) : $( targets[(targets.length == 1 ? 0 : key)] ),
                      current = target.attr('data-class'),
                      _class = classes[index];
                  (current != _class) && target.removeClass( target.attr('data-class') );
            target.toggleClass(classes[index]);
            target.attr('data-class', _class);
            key++;
          });
          $self.toggleClass('active');
          $self.attr('href') == "#" ? e.preventDefault() : '';
        });


// -------------------------- scroll animate
        var links = $('a.scroll-target');
        links.on('click', function() {
            if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') || location.hostname == this.hostname) {
            var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                if (target.length) {
                $('html,body').animate({
                    scrollTop: target.offset().top - 75,
                    }, 1000);
                    return false;
                }
            }
        });

// ------------------------- Tooltips
        $('[data-toggle="tooltip"]').tooltip();



// ----------------------- SVG convert Function
        $('img.svg').each(function(){
        var $img = $(this);
        var imgID = $img.attr('id');
        var imgClass = $img.attr('class');
        var imgURL = $img.attr('src');
    
        $.get(imgURL, function(data) {
            // Get the SVG tag, ignore the rest
            var $svg = $(data).find('svg');
    
            // Add replaced image's ID to the new SVG
            if(typeof imgID !== 'undefined') {
                $svg = $svg.attr('id', imgID);
            }
            // Add replaced image's classes to the new SVG
            if(typeof imgClass !== 'undefined') {
                $svg = $svg.attr('class', imgClass+' replaced-svg');
            }
    
            // Remove any invalid XML tags as per http://validator.w3.org
            $svg = $svg.removeAttr('xmlns:a');
            
            // Check if the viewport is set, else we gonna set it if we can.
            if(!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
                $svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'))
            }
    
            // Replace image with new SVG
            $img.replaceWith($svg);
    
            }, 'xml');
    
        });

// ----------------------------- Sidebar Menu/E-commerce
        var subMenu = $ (".main-menu-list ul li.dropdown-holder>a"),
          expender = $ (".main-menu-list ul li.dropdown-holder .expander");

        if ($('.sidebar-menu-open').length) {
          $('.sidebar-menu-open').on('click', function () {
            $('#sidebar-menu').addClass("show-menu");
          });
        }

        if ($('.close-button').length) {
          $('.close-button').on('click', function () {
            $('#sidebar-menu').removeClass("show-menu");
          });
        }
        subMenu.on("click", function (e) {
            e.preventDefault();
        });

        subMenu.append(function () {
          return '<button type="button" class="expander"><i class="fa fa-chevron-down" aria-hidden="true"></i></button>';
        });

        subMenu.on('click', function () {
          if ( $(this).parent('li').children('ul').hasClass('show') ) {
              $(this).parent('li').children('ul').removeClass('show');
          } else {
              $('.sub-menu.show').removeClass('show');
              $(this).parent('li').children('ul').addClass('show');    
          }
       });

// ------------------------ Product Quantity Selector
        if ($(".product-value").length) {
            $('.value-increase').on('click',function(){
              var $qty=$(this).closest('ul').find('.product-value');
              var currentVal = parseInt($qty.val());
              if (!isNaN(currentVal)) {
                  $qty.val(currentVal + 1);
              }
          });
          $('.value-decrease').on('click',function(){
              var $qty=$(this).closest('ul').find('.product-value');
              var currentVal = parseInt($qty.val());
              if (!isNaN(currentVal) && currentVal > 1) {
                  $qty.val(currentVal - 1);
              }
          });
        }

        
// --------------------------- Animated Bootstrap Banner
          //Function to animate slider captions
          function doAnimations(elems) {
            //Cache the animationend event in a variable
            var animEndEv = "webkitAnimationEnd animationend";

            elems.each(function() {
              var $this = $(this),
                $animationType = $this.data("animation");
              $this.addClass($animationType).one(animEndEv, function() {
                $this.removeClass($animationType);
              });
            });
          }

          //Variables on page load
          var $myCarousel = $(".carousel"),
            $firstAnimatingElems = $myCarousel
              .find(".carousel-item:first")
              .find("[data-animation ^= 'animated']");

          //Initialize carousel
          $myCarousel.carousel();

          //Animate captions in first slide on page load
          doAnimations($firstAnimatingElems);

          //Other slides to be animated on carousel slide event
          $myCarousel.on("slide.bs.carousel", function(e) {
            var $animatingElems = $(e.relatedTarget).find(
              "[data-animation ^= 'animated']"
            );
            doAnimations($animatingElems);
          });

          // scroll slides on mouse scroll 
          $('#eCommerce-carousel').bind('mousewheel DOMMouseScroll', function(e){

                  if(e.originalEvent.wheelDelta > 0 || e.originalEvent.detail < 0) {
                      $(this).carousel('prev');
                
                
                  }
                  else{
                      $(this).carousel('next');
                
                  }
              });


// ---------------------------- Select Dropdown
        if($('select').length) {
          $('.theme-select-menu').selectize();
        }


        
// ------------------------ Navigation Scroll
        $(window).on('scroll', function (){   
          var sticky = $('.theme-main-menu'),
          scroll = $(window).scrollTop();
          if (scroll >= 100) sticky.addClass('fixed');
          else sticky.removeClass('fixed');

        });

// -------------------- Remove Placeholder When Focus Or Click
        $("input,textarea").each( function(){
            $(this).data('holder',$(this).attr('placeholder'));
            $(this).on('focusin', function() {
                $(this).attr('placeholder','');
            });
            $(this).on('focusout', function() {
                $(this).attr('placeholder',$(this).data('holder'));
            });     
        });
        
// -------------------- From Bottom to Top Button
            //Check to see if the window is top if not then display button
        $(window).on('scroll', function (){
          if ($(this).scrollTop() > 200) {
            $('.scroll-top').fadeIn();
          } else {
            $('.scroll-top').fadeOut();
          }
        });


//---------------------- Click event to scroll to top
        $('.scroll-top').on('click', function() {
          $('html, body').animate({scrollTop : 0},1500);
          return false;
        });


// ----------------------------- Counter Function
        var timer = $('.timer');
        if(timer.length) {
            timer.appear(function () {
              timer.countTo();
          });
        }

// ------------------------ Hover Tilt effect
        var tiltBlock = $('.js-tilt');
          if(tiltBlock.length) {
            $('.js-tilt').tilt({
                glare: true,
                maxGlare: 0.4
            });
        }


// ------------------------ Modal box
        if ($(".iziModal").length) { 
          $(".iziModal").iziModal({
            width: 2550,
            overlayColor: 'rgba(255, 255, 255, 0.95)',
            fullscreen: true,
          });
        }
        

// ----------------------- Progress Bar
        $('.progress-bar').each(function(){
            var width = $(this).data('percent');
            $(this).css({'transition': 'width 3s'});
            $(this).appear(function() {
                console.log('hello');
                $(this).css('width', width + '%');
                $(this).find('.count').countTo({
                    from: 0,
                    to: width,
                    speed: 3000,
                    refreshInterval: 50,
                });
            });
        });

        
// --------------------------- Theme Main Banner Slider One
        var banner = $(".banner-one");
        if (banner.length) {
          banner.camera({ //here I declared some settings, the height and the presence of the thumbnails 
            height: '940px',
            pagination: false,
            navigation: false,
            thumbnails: false,
            playPause: false,
            pauseOnClick: false,
            autoPlay:true,
            hover: false,
            overlayer: true,
            loader: 'none',
            minHeight: '400px',
            time: 6000000,
          });
        }



// ------------------------------- Gallery Slider
        var tSlider = $ (".gallery-slider");
        if(tSlider.length) {
            tSlider.owlCarousel({
              loop:true,
              nav:true,
              navText: ["<i class='flaticon-back'></i>" , "<i class='flaticon-next'></i>"],
              dots:false,
              autoplay:true,
              autoplayTimeout:4000,
              smartSpeed:1200,
              autoplayHoverPause:true,
              lazyLoad:true,
              responsive:{
                    0:{
                        items:1
                    },
                    600:{
                        items:2
                    },
                    1100:{
                        items:3
                    },
                    1550:{
                        items:4,
                    }
                },
          });
        }


// ------------------------------- Testimonial Slider
        var agnTslider = $ (".agn-testimonial-slider");
        if(agnTslider.length) {
            agnTslider.owlCarousel({
              loop:true,
              nav:true,
              navText: ["<i class='flaticon-back'></i>" , "<i class='flaticon-next'></i>"],
              dots:false,
              autoplay:true,
              autoplayTimeout:4000,
              smartSpeed:1200,
              autoplayHoverPause:true,
              lazyLoad:true,
              items:1
          });
        }



// -------------------------------- Accordion Panel
          if ($('.theme-accordion > .panel').length) {
            $('.theme-accordion > .panel').on('show.bs.collapse', function (e) {
                  var heading = $(this).find('.panel-heading');
                  heading.addClass("active-panel");
                  
            });
            $('.theme-accordion > .panel').on('hidden.bs.collapse', function (e) {
                var heading = $(this).find('.panel-heading');
                  heading.removeClass("active-panel");
                  //setProgressBar(heading.get(0).id);
            });
          }


// ---------------------------- Partical Bg
          if ($("#particles").length) {
            particlesJS("particles", {
                "particles": {
                  "number": {
                    "value": 200,
                    "density": {
                      "enable": true,
                      "value_area": 800
                    }
                  },
                  "color": {
                    "value": "#636593"
                  },
                  "shape": {
                    "type": "circle",
                    "stroke": {
                      "width": 0,
                      "color": "#000000"
                    },
                    "polygon": {
                      "nb_sides": 5
                    },
                    "image": {
                      "src": "img/github.svg",
                      "width": 100,
                      "height": 100
                    }
                  },
                  "opacity": {
                    "value": 0.7,
                    "random": true,
                    "anim": {
                      "enable": true,
                      "speed": 1,
                      "opacity_min": 0,
                      "sync": false
                    }
                  },
                  "size": {
                    "value": 3,
                    "random": true,
                    "anim": {
                      "enable": false,
                      "speed": 4,
                      "size_min": 0.3,
                      "sync": false
                    }
                  },
                  "line_linked": {
                    "enable": false,
                    "distance": 150,
                    "color": "#ffffff",
                    "opacity": 0.4,
                    "width": 1
                  },
                  "move": {
                    "enable": true,
                    "speed": 1,
                    "direction": "none",
                    "random": true,
                    "straight": false,
                    "out_mode": "out",
                    "bounce": false,
                    "attract": {
                      "enable": false,
                      "rotateX": 600,
                      "rotateY": 600
                    }
                  }
                },
                "interactivity": {
                  "detect_on": "canvas",
                  "events": {
                    "onhover": {
                      "enable": true,
                      "mode": "bubble"
                    },
                    "onclick": {
                      "enable": true,
                      "mode": "repulse"
                    },
                    "resize": true
                  },
                  "modes": {
                    "grab": {
                      "distance": 400,
                      "line_linked": {
                        "opacity": 1
                      }
                    },
                    "bubble": {
                      "distance": 250,
                      "size": 0,
                      "duration": 2,
                      "opacity": 0,
                      "speed": 3
                    },
                    "repulse": {
                      "distance": 400,
                      "duration": 0.4
                    },
                    "push": {
                      "particles_nb": 4
                    },
                    "remove": {
                      "particles_nb": 2
                    }
                  }
                },
                "retina_detect": true
              });
          }


         
    }); //End Window Ready Function

    


    $(window).on ('load', function (){ // makes sure the whole site is loaded

        // -------------------- Site Preloader
        $('#ctn-preloader').fadeOut(); // will first fade out the loading animation
        $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
        $('body').delay(350).css({'overflow':'visible'});


// ------------------------------- AOS Animation
        if ($("[data-aos]").length) { 
            AOS.init({
            duration: 1000,
            mirror: true
          });
        }

// ------------------------------- WOW Animation 
        if ($(".wow").length) { 
            var wow = new WOW({
            boxClass:     'wow',      // animated element css class (default is wow)
            animateClass: 'animated', // animation css class (default is animated)
            offset:       20,          // distance to the element when triggering the animation (default is 0)
            mobile:       true,       // trigger animations on mobile devices (default is true)
            live:         true,       // act on asynchronously loaded content (default is true)
          });
          wow.init();
        }

// ----------------------------- isotop gallery
        if ($("#isotop-gallery-wrapper , .masnory-blog-wrapper").length) {
            var $grid = $('#isotop-gallery-wrapper , .masnory-blog-wrapper').isotope({
              // options
              itemSelector: '.isotop-item',
              percentPosition: true,
              masonry: {
                // use element for option
                columnWidth: '.grid-sizer'
              }

            });

            // filter items on button click
            $('.isotop-menu-wrapper').on( 'click', 'li', function() {
              var filterValue = $(this).attr('data-filter');
              $grid.isotope({ filter: filterValue });
            });

            // change is-checked class on buttons
            $('.isotop-menu-wrapper').each( function( i, buttonGroup ) {
                var $buttonGroup = $( buttonGroup );
                $buttonGroup.on( 'click', 'li', function() {
                  $buttonGroup.find('.is-checked').removeClass('is-checked');
                  $( this ).addClass('is-checked');
                });
            });
        }


// ------------------------------------- Fancybox
        var fancy = $ (".fancybox");
        if(fancy.length) {
          fancy.fancybox({
            arrows: true,
            buttons: [
              "zoom",
              //"share",
              "slideShow",
              //"fullScreen",
              //"download",
              "thumbs",
              "close"
            ],
            animationEffect: "zoom-in-out",
            transitionEffect: "zoom-in-out",
          });
        }


    });  //End On Load Function





    $(window).on ('scroll', function (){ // makes sure the whole site is loaded

        // --------------------- Viewport Animation 
        $(".hide-pr").each(function(i, el) {
          var el = $(el);
          if (el.visible(true)) {
            el.addClass("show-pr"); 
          } else {
            el.removeClass("show-pr");
          }
        });

    });  //End On Scroll Function


    
})(jQuery);