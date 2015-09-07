/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */

(function($) {

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Sage = {
    // All pages
    'common': {
      init: function() {
        // JavaScript to be fired on all pages
        var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        // Work with cookie
        function getCookie(name) {
          var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
          ));
          return matches ? decodeURIComponent(matches[1]) : undefined;
        }

        // options - объект с свойствами cookie (expires, path, domain, secure)
        function setCookie(name, value, options) {
          options = options || {};

          var expires = options.expires;

          if (typeof expires === "number" && expires) {
            var d = new Date();
            d.setTime(d.getTime() + expires*1000);
            expires = options.expires = d;
          }
          if (expires && expires.toUTCString) {
            options.expires = expires.toUTCString();
          }

          value = encodeURIComponent(value);

          var updatedCookie = name + "=" + value;

          for(var propName in options) {
            updatedCookie += "; " + propName;
            var propValue = options[propName];
            if (propValue !== true) {
              updatedCookie += "=" + propValue;
            }
          }

          document.cookie = updatedCookie;
        }

        function deleteCookie(name) {
          setCookie(name, "", { expires: -1 });
        }

        // Debounced resize
        var debounce = function(func, wait, immediate) {
          var timeout;
          return function() {
            var context = this, args = arguments;
            var later = function() {
              timeout = null;
              if (!immediate){
                func.apply(context, args);
              }
            };
            if (immediate && !timeout){
              func.apply(context, args);
            }
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
          };
        };

        // Disable 300ms click delay on mobile
        FastClick.attach(document.body);

        // WP admin bar fix
        (function(adminbar){
            if(document.getElementById(adminbar)){
                $('.navbar-fixed-top').css({
                    'margin-top' : $('#'+adminbar).height()
                });
            }
        })('wpadminbar');


        // Tooltip
        $('.bs-tooltip').tooltip();

        // Popover
        $(".bs-popover")
          .on('click', function(e) {e.preventDefault(); return true;})
          .popover();

        // Datepicker
        $('.datepicker').datepicker({
            language:   'en',
            autoclose:  true,
            format: "yyyy-mm-dd"
        });

        // Responsive video
        $('#main').fitVids();

        // Video lightbox
        $('.video-lightbox').magnificPopup({
            type: 'iframe'
        });

        // Image gallery lightbox
        $('.gallery').each(function(){
            $(this).find('a.thumbnail')
            .magnificPopup({
                type:       'image',
                enableEscapeKey: true,
                gallery:    {
                    enabled:    true,
                    tPrev:      '',
                    tNext:      '',
                    tCounter: '%curr% | %total%'
                },
                mainClass: 'mfp-with-zoom',
                zoom: {
                  enabled: true,
                  duration: 300,
                  easing: 'ease-in-out',
                }
            });
        });

        // Price Range
        [].forEach.call(document.querySelectorAll('.nouislider'), function(obj){
            var slider = $('<div/>');
            $(obj).hide().after(slider);

            noUiSlider.create(slider[0], {
                start: [ 0, 100 ],
                step: 1,
                connect: true,
                range: {
                    'min': [ 0 ],
                    'max': [ 100 ]
                }
            });
        });

        // ADD SLIDEDOWN ANIMATION TO DROPDOWN //
        $('.dropdown').on('show.bs.dropdown', function(e){
            $(this).find('.dropdown-menu').first().stop(true, true).slideDown(300, 'easeOutBack');
        });

        // ADD SLIDEUP ANIMATION TO DROPDOWN //
        $('.dropdown').on('hide.bs.dropdown', function(e){
            e.preventDefault();
            $(this).find('.dropdown-menu').first().stop(true, true).slideUp(300, 'easeInBack', function(){
                $('.dropdown').removeClass('open');
                $('.dropdown').find('.dropdown-toggle').attr('aria-expanded','false');
            });
        });


        var ripples = [
          ".carousel-control",
          ".btn:not(.btn-link)",
          ".card-image",
          ".navbar a:not(.withoutripple)",
          ".dropdown-menu a",
          ".nav-tabs a:not(.withoutripple)",
          ".withripple",
          ".pagination li:not(.active, .disabled) a:not(.withoutripple)"
        ].join(",");

        $(ripples).ripples();

        // Intense images
        var images = document.querySelectorAll('img');
        if(images.length){
          [].forEach.call(images, function(i){
            if(i.getAttribute('data-run') === 'intense') {
              new Intense(i);
            }
          });
        }

        // File input replacement
        $("input[type=file]").fileinput({
            uploadExtraData: {kvId: '10'},
        });

        // Parallax effect for Bootstrap Carousel
        $('.carousel-inline[data-type="parallax"] .item').each(function(){
         // declare the variable to affect the defined data-type
         var $scroll = $(this);
         $scroll.data('speed', 5);

          $(window).scroll(function() {
            // also, negative value because we're scrolling upwards
            var yPos = -($(window).scrollTop() / $scroll.data('speed'));

            // background position
            var coords = '50% '+ yPos + 'px';

            // move the background
            $scroll.css({ backgroundPosition: coords });
          });

        });

        // Material choices
        var materialChoices = function(){
            $('.checkbox input[type=checkbox]').after("<span class=checkbox-material><span class=check></span></span>");
            $('.radio input[type=radio]').after("<span class=radio-material><span class=circle></span><span class=check></span></span>");
            $('.togglebutton input[type=checkbox]').after("<span class=toggle></span>");
            $('select.form-control').dropdownjs();
        };

        // Gravity Forms render tweak
        var gravityChoices = function(){
            $('.gfield_checkbox > li').addClass('checkbox');
            $('.gfield_radio > li').addClass('radio');
            $('select.gfield_select').addClass('form-control');
        };

        gravityChoices();
        materialChoices();

        $(document).bind('gform_post_render', function(event, form_id, cur_page){
            var form = $('#gform_' + form_id);
            gravityChoices();
            materialChoices();
        });

        // Set .wrap padding-top equal to navbar height
        var wrapper = function(){
          return {
            spaceTop: function(){
                [].forEach.call(document.querySelectorAll('.navbar-fixed-top'), function(object){
                   object.nextElementSibling.style.paddingTop = object.clientHeight + 'px';
                });
            }
          };
        };

        // Set .navbar margin-top equal to #wpadminbar height
        var navbar = function(){
          return {
            spaceTop: function(){
                [].forEach.call(document.querySelectorAll('.navbar-fixed-top'), function(object){
                  var adminbar = document.getElementById('wpadminbar');
                  if(adminbar){
                    object.style.marginTop = adminbar.clientHeight + 'px';
                  }
                });
            },
            getHeight: function(){
                [].forEach.call(document.querySelectorAll('.banner'), function(object){
                  return object.clientHeight;
                });
            }
          };
        };

        // Set slider min-height equal to screen height
        var slider = function(){
          return {
            setHeight: function(){
                [].forEach.call(document.querySelectorAll('.carousel-fullscreen .item'), function(object){
                    var navbarHeight;
                    [].forEach.call(document.querySelectorAll('.banner'), function(object){
                        navbarHeight = object.clientHeight;
                    });
                    object.style.height = window.innerHeight - navbarHeight + 'px';
                });
            }
          };
        };

        // wait until users finishes resizing the browser
        var debouncedResize = debounce(function() {
            wrapper().spaceTop();
            navbar().spaceTop();
            slider().setHeight();
        }, 100);


        $(window).load(function(){

            // when the window resizes, redraw the grid
            $(window).resize(debouncedResize).trigger('resize');

            // needed by preloaded
            $('body').addClass('loaded');

            // Controls .wrap offset
            //wrapper().setOffTop();

            $('.carousel-inline').each(function() {
                var $myCarousel = $(this),
                    $firstAnimatingElems = $myCarousel.find('.item:first').find("[data-animated = 'true']");

                //Animate captions in first slide on page load
                doAnimations($firstAnimatingElems);

                //Other slides to be animated on carousel slide event
                $myCarousel.on('slide.bs.carousel', function(e) {
                    var $animatingElems = $(e.relatedTarget).find("[data-animated = 'true']");
                    prepareAnimations($animatingElems);
                }).on('slid.bs.carousel', function(e){
                    var $animatingElems = $(e.relatedTarget).find("[data-animated = 'true']");
                    doAnimations($animatingElems);
                });
            });

        });

        function prepareAnimations(elems) {

            elems.each(function() {
                var $this = $(this),
                    $animationType = $this.data('animation');
                $this.removeClass('animated ' + $animationType);
            });
        }

        function doAnimations(elems) {
            var animEndEv = 'webkitAnimationEnd animationend';

            elems.each(function() {
                var $this = $(this),
                    $animationType = $this.data('animation');
                $this.addClass('animated ' + $animationType).one(animEndEv, function() {
                    $this.removeClass($animationType);
                });
            });
        }



      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page
      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS
      }
    },
    // About us page, note the change from about-us to about_us.
    'about_us': {
      init: function() {
        // JavaScript to be fired on the about us page
      }
    }
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function(func, funcname, args) {
      var fire;
      var namespace = Sage;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
