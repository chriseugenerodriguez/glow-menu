var glowmenu = glowmenu || {
    init: function() {
        try {
            glowmenu.pricing(); 
            glowmenu.aesthetics();
            glowmenu.choosemenu();
            glowmenu.woocommerce();
            glowmenu.customize();
            glowmenu.designer();
        } catch (e) {
            console.debug(e);
        }
    },
    resize: function() {
        try {
            glowmenu.aesthetics();
        } catch (e) {
            console.debug(e);
        }
    },
    load: function() {
        try {

            glowmenu.woofilter();
            glowmenu.homecarousal();
        } catch (e) {
            console.debug(e);
        }
    },
    aesthetics: function() { 
        setTimeout(function() {
            $('.woocommerce-cart .woocommerce-message, .woocommerce-cart .woocommerce-error').fadeOut(500);
        }, 5000); 


        if (window.innerWidth < 768) {
            $('.archive #theme-wrapper .sidebar .filter').affix({
                offset: {
                    top: 230,
                }
            });            
        }

        $('.archive #theme-wrapper .sidebar .filter').click(function(){
            $(this).parent().find('.filter-wrapper').addClass('active');
            $('body').addClass('overflow');
            $(this).parent().find('.filter-exit').click(function(){
                $('.archive #theme-wrapper .sidebar .filter-wrapper').removeClass('active');
                $('body').removeClass('overflow');
            });
        });

        $('.archive #theme-wrapper .sidebar, .woocommerce .cart-collaterals').theiaStickySidebar({
            additionalMarginTop: 30
        })


        
        $('footer .container .nav .widget-box').each(function(){
            $(this).find('.arrow-down').click(function(){
                $('footer .container .nav .widget-box').removeClass('is-active');
                $(this).parent().parent().toggleClass('is-active');
            })
        });

        // LOGIN
        $('.sign-up-register .link-register #to-login-box').on('click', function(e) {
            $('.sign-up-register #register, .sign-up-register .link-register').hide();
            $('.sign-up-register #login, .sign-up-register .link-sign-in').show();
            e.preventDefault();
        })
        $('.sign-up-register .link-sign-in #to-signup-box').on('click', function(e) {
            $('.sign-up-register #register, .sign-up-register .link-register').show();
            $('.sign-up-register #login, .sign-up-register .link-sign-in').hide();
            e.preventDefault();
        })

        // ERROR LOGIN
        if($('.profilepress-login-status').length){
            $(this).find('a').remove();
            $('.sign-up-register').addClass('fade-in');
            $('body').addClass('modal-open');

            setTimeout(function() {
                $('.sign-up-register .container').addClass('fade-in');
            }, 50);
        }

        $('.header__shopping__items__login').click(function(e) {
            $('.sign-up-register').addClass('fade-in');
            $('.sign-up-register #register, .sign-up-register .link-register').hide();
            $('.sign-up-register #login, .sign-up-register .link-sign-in').show();

            $('body').addClass('modal-open');

            setTimeout(function() {
                $('.sign-up-register .container').addClass('fade-in');
            }, 50);

            e.preventDefault();
        })
        $('.single-product #theme-wrapper .product_info .button.login-form').click(function(e){
            $('.sign-up-register').addClass('fade-in');
            $('.sign-up-register #register, .sign-up-register .link-register').show();
            $('.sign-up-register #login, .sign-up-register .link-sign-in').hide();

            $('body').addClass('modal-open');

            setTimeout(function() {
                $('.sign-up-register .container').addClass('fade-in');
            }, 50);

            e.preventDefault();
        })

        $('.modal .container abbr').on('click', function(e) {
            $('body').removeClass('modal-open');
            $('.sign-up-register').removeClass('fade-in');
            $('.sign-up-register .container').removeClass('fade-in');
        });
        if ($('body').hasClass('single-product')) {
            $('.sign-up-register #register, .sign-up-register .link-register').show();
            $('.sign-up-register #login, .sign-up-register .link-sign-in').hide();
        };
    },
    pricing: function() {

        var quantity = Number('20');
        var finish = Number($('.page-template-pricing .features__options__finish option:selected').attr('cost'));
        var glow = Number($('.page-template-pricing .features__options__glow option:selected').attr('cost'));
        var size = Number('2.38');
        var sum = Math.round((quantity * (finish + size + glow))* 100) / 100;
        $('.page-template-pricing .scrollbar__estimated__cost').text('$' + sum);

        $('.features__options__finish, .features__options__glow, .features__options__size').change(function(){
            quantity = Number($( ".page-template-pricing #quantity-slider span .tooltip" ).text());
            finish = Number($('.page-template-pricing .features__options__finish option:selected').attr('cost'));
            glow = Number($('.page-template-pricing .features__options__glow option:selected').attr('cost'));

            sum = Math.round((quantity * (finish + size + glow))* 100) / 100;

            $('.page-template-pricing .page-template-pricing .scrollbar__estimated__cost').text('$' + sum);

        }).trigger('change');

        $(".page-template-pricing #quantity-slider").slider({
            range:'max',
            max: 200,
            min: 20,
            value: 10,
            step: 10,
            animate: "fast",
            slide: function(event, ui) {
                $( "#quantity-slider span .tooltip" ).text( ui.value );
            },
            change: function() {
                quantity = Number($( "#quantity-slider span .tooltip" ).text());
                sum = Math.round((quantity * (finish + size + glow))* 100) / 100;
                $('.page-template-pricing .scrollbar__estimated__cost').text('$' + sum);
            }
        });
        $('.page-template-pricing #quantity-slider span').html('<div class="tooltip"></div>');
        $(".page-template-pricing #quantity-slider span .tooltip" ).text( $( "#quantity-slider" ).slider( "value" ) );


        $('.page-template-pricing .scrollbar').affix({
            offset: {
                top: 305,
            }
        });

        $('.page-template-pricing .scrollbar a').click(function(e){
            e.preventDefault();
            var target = $($(this).attr('href'));
            $('html, body').animate({
                scrollTop: target.offset().top
            }, 500);
            return false;
        });

        var $sections = $('.page-template-pricing pricing section');
        $(window).scroll(function(){
            
            var currentScroll = $(this).scrollTop();
            var $currentSection
            
            $sections.each(function(){
                var divPosition = $(this).offset().top;
                  
                  // If the divPosition is less the the currentScroll position the div we are testing has moved above the window edge.
                  // the -1 is so that it includes the div 1px before the div leave the top of the window.
                if( divPosition - 1 < currentScroll ){
                    // We have either read the section or are currently reading the section so we'll call it our current section
                    $currentSection = $(this);
                }
                  
                var id = $currentSection.attr('id');
                $('.page-template-pricing .scrollbar a').removeClass('active');
                $(".page-template-pricing .scrollbar [href=#"+id+"]").addClass('active');
              
            })
        });
    },
    homecarousal: function() {
        var carousel = $('.home .slider__products');
        carousel.find('img').unwrap().unwrap().unwrap().unwrap();
        carousel.find('.woocommerce').remove();
        carousel.owlCarousel({
            items: 3,
            center:true,
            dots: true,
            nav: false,
            autoplay: false,
            touchDrag:true,
            mouseDrag:true,
            animateIn: false,
            loop: false,
            autoHeight: false
        });


        var slider = $('.home .custom__slider').owlCarousel({
            items: 8,
            margin: 20,
            dots: false,
            nav: false,
            touchDrag:false,
            mouseDrag:true,
            loop: true,
            autoHeight: true,
            center: true,
            responsiveClass:true,
            responsive:{
                0:{
                    items:2,
                },
                600:{
                    items:4,
                },
                1000:{
                    items:6,
                },
                1400:{
                    items:8,
                }
            }
        });
        var slider2 = $('.home .custom__slider-second').owlCarousel({
            items: 8,
            margin: 20,
            dots: false,
            nav: false,
            touchDrag:false,
            mouseDrag:true,
            loop: true,
            autoHeight: true,
            responsiveClass:true,
            responsive:{
                0:{
                    items:2,
                },
                600:{
                    items:4,
                },
                1000:{
                    items:6,
                },
                1400:{
                    items:8,
                }
            }
        });
        slider.on('resized.owl.carousel', function(event){
            $(this).find('.owl-height').css('height', $(this).find('.owl-item').height())
        });
        slider2.on('resized.owl.carousel', function(event){
            $(this).find('.owl-height').css('height', $(this).find('.owl-item').height())
        });

    },
    woocommerce: function() {

        if (window.innerWidth > 992) {
            $('ul.products li a').hoverIntent(function() {
                $(this).children('.wp-post-image').removeClass('fadeIn').addClass('animated fadeOut');
                $(this).children('.secondary-image').removeClass('fadeOut').addClass('animated fadeIn');
            }, function() {
                $(this).children('.wp-post-image').removeClass('fadeOut').addClass('fadeIn');
                $(this).children('.secondary-image').removeClass('fadeIn').addClass('fadeOut');
            });
        };


        if ($('body').hasClass('archive')) {
            $('.woocommerce-pagination, #theme-wrapper .filter section ul li small, #theme-wrapper ul li a h3').remove();

        };

        if ($('body').hasClass('single-product')) {
            $('#pageandresult').remove();
            $('.single-product .product > .row').wrapAll('<div class="container-fluid"></div>');
            $('body').addClass($('.product_meta .posted_in a').text().toLowerCase());

            $('.single-product #theme-wrapper .content').addClass('animated slideInLeft');

            // NO CUSTOMIZE MESSAGE MD <
            $('<div class="no-customizer visible-xs visible-sm visible-md hidden-lg wocommerce-message woocommerce-info">To customize this product, please view it on 1100 res or higher.</div>').insertBefore('.single-product .type-product');

            $('.product_meta .posted_in a[rel="tag"]').insertBefore('.product_title');
            $('.product_meta').remove();

            $('header .slider-tabs span a').removeClass('active');
            $('.woocommerce-page img').attr('title', '');


           $('.single-product #theme-wrapper div .images img').each(function() {
                $(this).wrapAll('<div class="image"></div>');
            });
            $('.single-product #theme-wrapper .content .images figure').attr('class', 'visible-lg visible-md hidden-xs hidden-sm');
            $('.single-product #theme-wrapper .content .images figure').clone().appendTo('.single-product #theme-wrapper .content .images').attr('class', 'hidden-lg hidden-md visible-xs visible-sm product-slider');
            $('.single-product #theme-wrapper .content .images figure.product-slider div, .single-product #theme-wrapper .content .images figure.product-slider a').contents().unwrap();

            var slider = $('.single-product #theme-wrapper .content .images figure.product-slider');

            slider.owlCarousel({
                items: 1,
                dots: true,
                nav: false,
                autoplay: false,
                mouseDrag: true,
                touchDrag: true,
                loop: false,
                animateIn: 'fadeIn',
                animateOut: 'fadeOut',
                autoHeight: true
            });

            slider.on('resized.owl.carousel', function(event) {
                $(this).find('.owl-height').css('height', $(this).find('.owl-item.active').height() );
            })

            $('.fpd-sub-panel .fpd-panel-tabs-content .fpd-slider-group').remove();
        };

        if ($('body').hasClass('single-product') || $('body').hasClass('woocommerce-cart')) {

            $('.quantity input').on('keyup keydown', function(e) {
                if ($(this).val() > 200 && e.keyCode != 46 && e.keyCode != 8) {
                    e.preventDefault();
                    $(this).val(200);
                };
                if ($(this).val() < 20 && e.keyCode != 46 && e.keyCode != 8) {
                    e.preventDefault();
                    $(this).val(20);
                };
            });
        };

        if ($('body').hasClass('woocommerce-view-order')) {
            $('.woocommerce .woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--orders').addClass('is-active')
        };

        var checkVisible = setInterval(function() {
            var tar = $('.single-product #theme-wrapper .product_info .single_add_to_cart_button');
            // if element doesn't exist or isn't visible then end
            if (!$(tar).length || !$(tar).is(':visible'))
                return;

            // if element does exist and is visible then stop the interval and run code 
            clearInterval(checkVisible);
            // place your code here to run when the element becomes visible
            $('.single-product #theme-wrapper .product_info .fpd-modal-mode-btn').addClass('alternate');
        }, 1000);
    },
    woofilter: function() {

        var $products = $(".archive.woocommerce ul.products");

        $products.isotope({
            itemSelector: 'li',
            filter: '*',
            percentPosition: true,
            getSortData: {               
                size: "[data-size]",
                 
            },
            layoutMode: 'packery',
            animationOptions: {
                duration: 750,
                easing: 'linear',
                queue: false
            }
        });

        $('.archive.woocommerce ul.products li').each(function(i) {
            setTimeout(function() {
                $('.archive.woocommerce ul.products li').eq(i).addClass("post-loaded animate");
            }, 200 * (i + 1));
        });
        $('.archive.woocommerce ul.products li').each(function(i) {
            setTimeout(function() {
                $('.archive.woocommerce ul.products li').eq(i).removeClass("post-loaded");
            }, 400 * (i + 1));
        });


        var s;   
        $(".field-text").on("input", "#menu-search", function() {       
            clearTimeout(s);       
            var e = $(this);       
            s = setTimeout(function() {           
                var t = e.val().toLowerCase(),
                    o = t ? function() {                   
                        var e = $(this),
                            o = e.data("name") ? e.data("name") : "";                   
                        return o.match(new RegExp(t));               
                    } : "*";           
                $(".products").isotope({               
                    filter: o           
                });
            }, 300);   
        });
        $products.on('arrangeComplete',
            function(event, filteredItems) {
                if (filteredItems.length == '1') {
                    $('.archive #theme-wrapper #pageandresult p.woocommerce-result-count').text('Showing ' + filteredItems.length + ' result');
                };
                if (filteredItems.length > '1') {
                    $('.archive #theme-wrapper #pageandresult p.woocommerce-result-count').text('Showing ' + filteredItems.length + ' results');
                };
                if (filteredItems.length == '0') {
                    $('.archive #theme-wrapper #pageandresult p.woocommerce-result-count').text('No results');
                };
            }
        );
    },
    designer: function() {

        var url = window.location.href;

        if (url.indexOf("?start_customizing") > -1 || url.indexOf("?tm_cart_item_key") > -1 || url.indexOf("?cart_item_key") > -1) {

            $('.fpd-dropdown > .fpd-dropdown-list > *:first-child, .fpd-element-toolbar > .fpd-row .fpd-tool-text-stroke').remove();

            $('footer').hide();
            $('.single-product #theme-wrapper .product_info > :not("h1"):not("a[rel=tag]")').wrapAll('<div class="pricing_info"></div>');
            $('<div class="size"></div>').prependTo('.fpd-product-designer-wrapper');
            $('<div class="options"><ul class="modules"><li class="upload">Upload Image</li><li class="text">Add Text</li><li class="layers">Layers</li><li class="download">Download</li><li class="print">Print</li><li class="reset">Reset</li></ul><ul class="result"><li class="save">Save</li><li class="load">Load</li></ul><button class="button done-editing">Done</button></div>').appendTo('.single-product #theme-wrapper .product_info');
            $('.fpd-modal-product-designer.fpd-modal-overlay .fpd-done').addClass('button').appendTo('.single-product #theme-wrapper .product_info .options');
            $('<p>If you want text to glow just click fill icon when selecting layer and select add glow.</p>').prependTo('.single-product #theme-wrapper .product_info .options');
            $('.fpd-modal-product-designer.fpd-modal-overlay .fpd-modal-wrapper .options ul li').on('click', function() {
                $(this).addClass('active');
                $('.fpd-dialog-head .fpd-close-dialog, .fpd-modal-wrapper > .fpd-modal-close').on('click', function() {
                    $('.single-product #theme-wrapper .product_info .options ul li').removeClass('active');
                });
            });

            $(".single-product #theme-wrapper .product_info .options ul li.upload").click(function() {
                $(".fpd-mainbar div[data-module='images']").trigger("click");
            });
            $(".single-product #theme-wrapper .product_info .options ul li.text").click(function() {
                $(".fpd-mainbar div[data-module='text']").trigger("click");
            });
            $(".single-product #theme-wrapper .product_info .options ul li.download").click(function() {
                if($('body').hasClass('logged-in')){
                    $('.fpd-action-btn[data-action="download"]').trigger('click');
                    $('.fpd-sub-tooltip-theme .tooltipster-content .fpd-item[data-value="pdf"]').trigger('click');
                } else {
                    $('.header__shopping__items__login').trigger('click');
                }       
            });
            $(".single-product #theme-wrapper .product_info .options ul li.print").click(function() {
                if($('body').hasClass('logged-in')){
                    $('.fpd-action-btn[data-action="print"]').trigger('click');
                } else {
                    $('.header__shopping__items__login').trigger('click');
                }   
            });
            $(".single-product #theme-wrapper .product_info .options ul li.load").click(function() {
                if($('body').hasClass('logged-in')){
                    $('.fpd-action-btn[data-action="load"]').trigger('click');
                } else {
                    $('.header__shopping__items__login').trigger('click');
                }    
            });
            $(".single-product #theme-wrapper .product_info .options ul li.save").click(function() {
                if($('body').hasClass('logged-in')){
                    $('.fpd-action-btn[data-action="save"]').trigger('click');
                } else {
                    $('.header__shopping__items__login').trigger('click');
                }
             });
            $(".single-product #theme-wrapper .product_info .options ul li.layers").click(function() {
                $('.fpd-action-btn[data-action="manage-layers"]').trigger('click');
            });
            $(".single-product #theme-wrapper .product_info .options ul li.reset").click(function() {
                $('.fpd-action-btn[data-action="reset-product"]').trigger('click');
            });
            $(".fpd-modal-product-designer.fpd-modal-overlay .header .header-bar .exit").click(function() {
                $('.fpd-modal-product-designer.fpd-modal-overlay .fpd-modal-wrapper .fpd-modal-close').trigger('click');
            });

            // Show/hide product info
            $('.single-product #theme-wrapper .product_info .options button.done-editing').click(function() {
                $('.single-product #theme-wrapper .product_info .options').hide();

                $('.single-product #theme-wrapper .product_info .pricing_info').show();
                if (!$(".single-product #theme-wrapper .product_info .pricing_info .alternate").length) {
                    $('<span class="alternate">Customize</span>').appendTo('.single-product #theme-wrapper .product_info .pricing_info .cart');
                };
                if (!$(".single-product #theme-wrapper .product_info .pricing_info .reset").length) {
                    $('<span class="reset" onclick="location.reload();">Reset Order</span>').appendTo('.single-product #theme-wrapper .product_info .pricing_info .cart');
                };
                $('.single-product #theme-wrapper .product_info .pricing_info .cart .alternate').on('click', function() {
                    $('.single-product #theme-wrapper .product_info .options').show();
                    $('.single-product #theme-wrapper .product_info .pricing_info').hide();
                });
            });

            // Top Bar
            var sizevisible;
            $('.fancy-product .options').hide();
            function autorun() {
                if ($(".single-product .fpd-container > .fpd-loader-wrapper").is(":hidden")) {

                    var size = $('.single-product .fpd-product-designer-wrapper .size');

                    if ($('.single-product #theme-wrapper .product .content').hasClass('slim')) {
                        size.html('Slim - 4 x 6');
                    };
                    if ($('.single-product #theme-wrapper .product .content').hasClass('square')) {
                        size.html('Square - 5 x 5');
                    };
                    if ($('.single-product #theme-wrapper .product .content').hasClass('half letter')) {
                        size.html('Half Letter - 4.25 x 11');
                    };
                    if ($('.single-product #theme-wrapper .product .content').hasClass('table tent')) {
                        size.html('Table Tent - 5 x 7');
                    };
                    if ($('.single-product #theme-wrapper .product .content').hasClass('half legal')) {
                        size.html('Half Legal - 4.25 x 14');
                    };

                    $('<div class="size-wrapper" style="display:none;"><div class="size"></div><div class="sides"><span class="f active">Front</span><sep>|</sep><span class="b">Back</span></div></div>').prependTo('.fpd-product-designer-wrapper');

                    $(".single-product .fpd-product-designer-wrapper .sides span.f").click(function() {
                        $(".single-product .fpd-product-designer-wrapper .sides span").removeClass('active');
                        $(this).addClass('active');
                        $('.fpd-views-inside-bottom .fpd-views-selection div:first-child').trigger('click');
                    });
                    $(".single-product .fpd-product-designer-wrapper .sides span.b").click(function() {
                        $(".single-product .fpd-product-designer-wrapper .sides span").removeClass('active');
                        $(this).addClass('active');
                        $('.fpd-views-inside-bottom .fpd-views-selection div:last-child').trigger('click');
                    });
                    $('.fancy-product .options, .fancy-product .size-wrapper').fadeIn(300);
                    clearTimeout(sizevisible);
                };
            };
            sizevisible = setInterval(autorun, 100);

            // PRODUCT SINGLE CUSTOMIZE SIDEBAR
            $('.single-product #theme-wrapper .product .product_info').theiaStickySidebar({
                additionalMarginTop: 30
            })

            // Security if on tablet or below redirect to pdp
            if($(window).width() < 1100){
                var s = window.location.pathname;
                s = s.split('?');
                document.location = s;
            }
        };
        if ($('body').hasClass('single-product')) {

            $('.single-product #theme-wrapper .content .images figure .woocommerce-product-gallery__image, .single-product #theme-wrapper .content .images figure a').contents().unwrap();
            $('.single-product #theme-wrapper .content .images figure .image').addClass('active');

            
                $('.single-product #theme-wrapper .product_info .options button.done-editing').click(function() {
                    $('.single-product #theme-wrapper .product_info .tm-epo-field.tmcp-select').each(function() {
                        $(this).data('currentValue', $(this).val());
                    });
                    $('.single-product #theme-wrapper .product_info .tm-epo-field.tmcp-select').off('change').on('change.confirm', function() {
                        $(this).val($(this).data('currentValue'));
                        if(url.indexOf("?tm_cart_item_key") > -1 || url.indexOf("?cart_item_key") > -1){
                            if (confirm('You are unable to change items in your cart, please remove this item from cart and start over.')){
                                document.location = 'http://glowmenus.com/cart';
                            }
                        }
                        if(url.indexOf("?start_customizing") > -1 ) {
                            if (confirm('If you want to change options your going to have to start over, click OK to restart.')){
                                var s = window.location.pathname;
                                s = s.split('?');
                                document.location = s;
                            }
                        }
                    });
                });
        };

    },
    choosemenu: function() {
        var items = $('.page-template-titlewsub .menu-features .menu-types li');
        var menus = $('.page-template-titlewsub .menu-features section');

        $(items).on('click', function() {
            var id = $(this).attr("id");
            items.removeClass('active');
            menus.removeClass('active fadeIn animated');

            $(this).addClass('active');

            menus.filter(function() {
                return $(this).attr('id') === id;
            }).addClass('active fadeIn animated');
        });
        if ($('body').hasClass('page-id-90') || $('body').hasClass('postid-489') || $('body').hasClass('postid-488') || $('body').hasClass('postid-487') || $('body').hasClass('postid-486') || $('body').hasClass('postid-485')) {
            $('body').addClass('custom');
            $('.single-product #theme-wrapper .product_info > a').attr('href', '/custom-menus')
        };
        if ($('body').hasClass('custom')) {
            $('header .menu ul li.back a').html('← Back to Custom Designs');
            $('header .menu ul li.back a').attr('href', 'http://glowmenus.com/custom-menus/');
            $('header .slider-tabs span a').removeClass('active');
            $('header .slider-tabs span:last-child a').addClass('active');
        };
    },
    customize: function() {
        var customize = '?start_customizing';
        $('custom-menus .customize__select__paper').addClass('active');

        $('custom-menus .customize__select__paper.active').on('change', function() {
            var active = $('custom-menus .customize__select__paper__sizes').val();
            
            $('custom-menus .custom').attr('size', '');
            $('custom-menus .custom').attr('size', active);

            if(!$('.customize__select__options .customize__select__options__paper').hasClass('active')){
                $('.customize__select__options li').removeClass('active');   
                $('.customize__select__options .customize__select__options__paper').addClass('active');                
            }

        }).change();

        $('custom-menus .customize__select__paper h4 span').click(function(){
            $('custom-menus .customize__select__glow').show();
            $('custom-menus .customize__select__paper, custom-menus .customize__select__finish').hide();
            $('custom-menus .customize__select__paper').removeClass('active');
            $('custom-menus .customize__select__glow').addClass('active');
                
            if(!$('.customize__select__options .customize__select__options__glow').hasClass('active')){
                $('.customize__select__options .customize__select__options__finish').removeClass('active');   
                $('.customize__select__options .customize__select__options__glow').addClass('active');                
            }

            $('custom-menus .customize__select__glow__color').on('change', function() {
                var active = $(this).val();
                var s = active.substring(0, active.indexOf('_'));
                $('custom-menus .custom').attr('color', '');
                $('custom-menus .custom').attr('color', s);

            }).change();
        })

        $('custom-menus .customize__select__glow h4 span').click(function(){
            $('custom-menus .customize__select__finish').show();
            $('custom-menus .customize__select__paper, custom-menus .customize__select__glow').hide();
            $('custom-menus .customize__select__glow').removeClass('active');
            $('custom-menus .customize__select__finish').addClass('active');
            
            if(!$('.customize__select__options .customize__select__options__finish').hasClass('active')){  
                $('.customize__select__options .customize__select__options__finish').addClass('active');                
            }

            $('custom-menus .customize__select__button').show();

            $('custom-menus .customize__select__finish__types').on('change', function() {
                var active = $(this).val();
                var s = active.substring(0, active.indexOf('_')); 

                $('custom-menus .custom').attr('finish', '');
                $('custom-menus .custom').attr('finish', s);
    
            }).change();
        });
    

        $('custom-menus .customize__select__button').on('click', function() {
            var size = $('.customize__select__paper__sizes').val();
            var glow = $('.customize__select__glow__color').val();
            var finish = $('.customize__select__finish__types').val();

            $(this).attr('href', '/menus/custom-menus/' + size + '/?start_customizing');

            Cookies.set('finish', finish, { expires: 1 });
            Cookies.set('glow', glow, { expires: 1 });
        });
        if ($('body').hasClass('postid-486') || $('body').hasClass('postid-489') || $('body').hasClass('postid-488') || $('body').hasClass('postid-3886') || $('body').hasClass('postid-3887')) {
            $(".single-product #theme-wrapper .product_info .tm-extra-product-options ul.tmcp-elements li.tmcp-field-wrap .tm-epo-field#tmcp_select_1").val(Cookies.get('finish'));
            $(".single-product #theme-wrapper .product_info .tm-extra-product-options ul.tmcp-elements li.tmcp-field-wrap .tm-epo-field#tmcp_select_2").val(Cookies.get('glow'));
           
        } else {
            var url = window.location.href;
            if(!$('body').hasClass('page-id-90') || url.indexOf("?start_customizing") > -1 || url.indexOf("?tm_cart_item_key") > -1 || url.indexOf("?cart_item_key") > -1){
                Cookies.remove('finish');
                Cookies.remove('glow');
            }
        };
        if ($('body').hasClass('fancy-product')) {
            $('.single-product #theme-wrapper .product .product_info .yith-wcwl-add-to-wishlist, .single-product #theme-wrapper .product .product_info .sharedaddy').remove();
        }
    },
};

var $ = jQuery.noConflict();
$(function() {   
    glowmenu.init();
});
$(window).resize(function() {   
    glowmenu.resize();
});
$(window).load(function() {
    glowmenu.load();
});
