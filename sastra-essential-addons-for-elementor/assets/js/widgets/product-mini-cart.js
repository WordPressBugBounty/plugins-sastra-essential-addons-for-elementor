(function ($) {
    "use strict";

    const widgetProductMiniCart = function ($scope, $) {

        $scope.find('.tmpcoder-mini-cart').css({ "display": "none" });

        // $( document.body ).trigger( 'wc_fragment_refresh' );

        var animationSpeed = $scope.find('.tmpcoder-mini-cart-wrap').data('animation');

        $('body').on('click', function (e) {
            if (!e.target.classList.value.includes('tmpcoder-mini-cart') && !e.target.closest('.tmpcoder-mini-cart')) {
                if ($scope.hasClass('tmpcoder-mini-cart-slide')) {
                    $scope.find('.tmpcoder-mini-cart').slideUp(animationSpeed);
                } else {
                    $scope.find('.tmpcoder-mini-cart').fadeOut(animationSpeed);
                }
            }
        });

        if ($scope.hasClass('tmpcoder-mini-cart-sidebar')) {
            if ($('#wpadminbar').length) {
                $scope.find('.tmpcoder-mini-cart').css({
                    // 'top': $('#wpadminbar').css('height'),
                    // 'height': $scope.find('.tmpcoder-shopping-cart-wrap').css('height') -  $('#wpadminbar').css('height')
                    'z-index': 999999
                });
            }

            closeSideBar();

            $scope.find('.tmpcoder-shopping-cart-wrap').on('click', function (e) {
                // if ( !e.target.classList.value.includes('widget_shopping_cart_content') && !e.target.closest('.widget_shopping_cart_content') ) {
                if (!e.target.classList.value.includes('tmpcoder-shopping-cart-inner-wrap') && !e.target.closest('.tmpcoder-shopping-cart-inner-wrap')) {
                    // $scope.find('.widget_shopping_cart_content').addClass('tmpcoder-mini-cart-slide-out');
                    $scope.find('.tmpcoder-shopping-cart-inner-wrap').addClass('tmpcoder-mini-cart-slide-out');
                    $scope.find('.tmpcoder-mini-cart-slide-out').css('animation-speed', animationSpeed);
                    $scope.find('.tmpcoder-shopping-cart-wrap').fadeOut(animationSpeed);
                    $('body').removeClass('tmpcoder-mini-cart-sidebar-body');
                    setTimeout(function () {
                        // $scope.find('.widget_shopping_cart_content').removeClass('tmpcoder-mini-cart-slide-out');
                        $scope.find('.tmpcoder-shopping-cart-inner-wrap').removeClass('tmpcoder-mini-cart-slide-out');
                        $scope.find('.tmpcoder-mini-cart').css({ "display": "none" });
                    }, animationSpeed + 100);
                }
            });
        }

        if ($scope.find('.tmpcoder-mini-cart').length) {
            if ($scope.hasClass('tmpcoder-mini-cart-sidebar') || $scope.hasClass('tmpcoder-mini-cart-dropdown')) {
                $scope.find('.tmpcoder-mini-cart-toggle-btn').on('click', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    if ('none' === $scope.find('.tmpcoder-mini-cart').css("display")) {
                        if ($scope.hasClass('tmpcoder-mini-cart-slide')) {
                            $scope.find('.tmpcoder-mini-cart').slideDown(animationSpeed);
                        } else {
                            $scope.find('.tmpcoder-mini-cart').fadeIn(animationSpeed);
                        }
                        if ($scope.hasClass('tmpcoder-mini-cart-sidebar')) {
                            $scope.find('.tmpcoder-shopping-cart-wrap').fadeIn(animationSpeed);
                            // $scope.find('.widget_shopping_cart_content').addClass('tmpcoder-mini-cart-slide-in');
                            $scope.find('.tmpcoder-shopping-cart-inner-wrap').addClass('tmpcoder-mini-cart-slide-in');
                            $scope.find('.tmpcoder-mini-cart-slide-in').css('animation-speed', animationSpeed);
                            $('body').addClass('tmpcoder-mini-cart-sidebar-body');
                        }
                        setTimeout(function () {
                            // $scope.find('.widget_shopping_cart_content').removeClass('tmpcoder-mini-cart-slide-in');
                            $scope.find('.tmpcoder-shopping-cart-inner-wrap').removeClass('tmpcoder-mini-cart-slide-in');
                            if ($scope.hasClass('tmpcoder-mini-cart-sidebar')) {
                                $scope.find('.tmpcoder-woo-mini-cart').trigger('resize');
                            }
                        }, animationSpeed + 100);
                    } else {
                        if ($scope.hasClass('tmpcoder-mini-cart-slide')) {
                            $scope.find('.tmpcoder-mini-cart').slideUp(animationSpeed);
                        } else {
                            $scope.find('.tmpcoder-mini-cart').fadeOut(animationSpeed);
                        }
                    }
                });
            }
        }

        var mutationObserver = new MutationObserver(function (mutations) {
            if ($scope.hasClass('tmpcoder-mini-cart-sidebar')) {
                closeSideBar();

                // if ( $scope.find('.tmpcoder-mini-cart').data('close-cart-heading') ) {
                //  $scope.find('.tmpcoder-close-cart h2').text($scope.find('.tmpcoder-mini-cart').data('close-cart-heading').replace(/-/g, ' '));
                // }
            }

            $scope.find('.woocommerce-mini-cart-item').on('click', '.tmpcoder-remove-item-from-mini-cart', function () {
                $(this).closest('li').addClass('tmpcoder-before-remove-from-mini-cart');
            });
        });

        // Listen to Mini Cart Changes
        mutationObserver.observe($scope[0], {
            childList: true,
            subtree: true,
        });

        function closeSideBar() {
            $scope.find('.tmpcoder-close-cart span').on('click', function (e) {
                // $scope.find('.widget_shopping_cart_content').addClass('tmpcoder-mini-cart-slide-out');
                $scope.find('.tmpcoder-shopping-cart-inner-wrap').addClass('tmpcoder-mini-cart-slide-out');
                $scope.find('.tmpcoder-mini-cart-slide-out').css('animation-speed', animationSpeed);
                $scope.find('.tmpcoder-shopping-cart-wrap').fadeOut(animationSpeed);
                $('body').removeClass('tmpcoder-mini-cart-sidebar-body');
                setTimeout(function () {
                    // $scope.find('.widget_shopping_cart_content').removeClass('tmpcoder-mini-cart-slide-out');
                    $scope.find('.tmpcoder-shopping-cart-inner-wrap').removeClass('tmpcoder-mini-cart-slide-out');
                    $scope.find('.tmpcoder-mini-cart').css({ "display": "none" });
                }, animationSpeed + 100);
            });
        }
    }
    
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/tmpcoder-product-mini-cart.default",
            widgetProductMiniCart);
    });
})(jQuery);