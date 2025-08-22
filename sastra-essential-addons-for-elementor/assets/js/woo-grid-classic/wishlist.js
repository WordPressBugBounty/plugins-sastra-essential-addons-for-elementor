(function ($) {
    "use strict";

    const widgetGrid = function ($scope, $) {

        var iGrid = $scope.find('.tmpcoder-grid');

        var loadedItems;

        // if (!iGrid.length) {
        //     return;
        // }

        if (!iGrid.length) {

            var iGridDefault = $scope.find('ul.products li.product ');

            if (iGridDefault.length) {
                var iGrid = $scope.find('ul.products li.product ');
            } else {
                return;
            }
        }

        // Settings
        var settings = iGrid.attr('data-settings');

        checkWishlistAndCompare();
        addRemoveCompare();
        addRemoveWishlist();

        // var mutationObserver = new MutationObserver(function (mutations) {
        //     // checkWishlistAndCompare();
        //     addRemoveCompare();
        //     addRemoveWishlist();
        // });

        // mutationObserver.observe($scope[0], {
        //     childList: true,
        //     subtree: true,
        // });

        function checkWishlistAndCompare() {
            var wishlistArray;

            if (iGrid.find('.tmpcoder-wishlist-add').length) {
                $.ajax({
                    url: tmpcoder_plugin_script.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'check_product_in_wishlist_grid',
                        nonce: tmpcoder_plugin_script.nonce,
                    },
                    success: function (response) {
                        wishlistArray = response;
                    }
                });


                iGrid.find('.tmpcoder-wishlist-add').each(function () {
                    var wishlistBtn = $(this);

                    if ($.inArray(wishlistBtn.data('product-id'), wishlistArray) !== -1) {
                        if (!wishlistBtn.hasClass('tmpcoder-button-hidden')) {
                            wishlistBtn.addClass('tmpcoder-button-hidden');
                        }

                        if (wishlistBtn.next().hasClass('tmpcoder-button-hidden')) {
                            wishlistBtn.next().removeClass('tmpcoder-button-hidden');
                        }
                    }
                });
            }

            if (iGrid.find('.tmpcoder-compare-add').length > 0) {
                var compareArray = [];

                $.ajax({
                    url: tmpcoder_plugin_script.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'check_product_in_compare_grid',
                        nonce: tmpcoder_plugin_script.nonce,
                    },
                    success: function (response) {
                        compareArray = response;
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });


                iGrid.find('.tmpcoder-compare-add').each(function () {
                    var compareBtn = $(this);

                    if ($.inArray(compareBtn.data('product-id'), compareArray) !== -1) {
                        if (!compareBtn.hasClass('tmpcoder-button-hidden')) {
                            compareBtn.addClass('tmpcoder-button-hidden');
                        }

                        if (compareBtn.next().hasClass('tmpcoder-button-hidden')) {
                            compareBtn.next().removeClass('tmpcoder-button-hidden');
                        }
                    }
                });

            }
        }

        function addRemoveCompare() {
            if (iGrid.find('.tmpcoder-compare-add').length) {    

                $scope.find('.tmpcoder-compare-add').click(function (e) {
                    e.preventDefault();
                    var event_target = $(this);
                    var product_id = $(this).data('product-id');

                    event_target.fadeTo(500, 0);

                    $.ajax({
                        url: tmpcoder_plugin_script.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'add_to_compare',
                            product_id: product_id,
                            nonce: tmpcoder_plugin_script.nonce,
                        },
                        success: function () {
                            $scope.find('.tmpcoder-compare-add[data-product-id="' + product_id + '"]').hide();
                            $scope.find('.tmpcoder-compare-remove[data-product-id="' + product_id + '"]').show();
                            $scope.find('.tmpcoder-compare-remove[data-product-id="' + product_id + '"]').fadeTo(500, 1);
                            changeActionTargetProductId(product_id);
                            $(document).trigger('added_to_compare');

                            if ('sidebar' === event_target.data('atcompare-popup')) {
                                // TMPCODER INFO -  configure after adding compare dropdown functinality
                                if ($('.tmpcoder-compare-toggle-btn').length) {
                                    $('.tmpcoder-compare-toggle-btn').each(function () {
                                        if ('none' === $(this).next('.tmpcoder-compare').css('display')) {
                                            $(this).trigger('click');
                                        }
                                    });
                                }
                            } else if ('popup' === event_target.data('atcompare-popup')) {
                                // Popup Link needs wishlist
                                var popupItem = event_target.closest('.tmpcoder-grid-item'),
                                    popupText = popupItem.find('.tmpcoder-grid-item-title').text(),
                                    popupLink = tmpcoder_plugin_script.comparePageURL,
                                    popupTarget = 'yes' == event_target.data('open-in-new-tab') ? '_blank' : '_self',
                                    popupImageSrc = popupItem.find('.tmpcoder-grid-image-wrap').length ? popupItem.find('.tmpcoder-grid-image-wrap').data('src') : '',
                                    popupAnimation = event_target.data('atcompare-animation'),
                                    fadeOutIn = event_target.data('atcompare-fade-out-in'),
                                    animTime = event_target.data('atcompare-animation-time'),
                                    popupImage,
                                    animationClass = 'tmpcoder-added-to-compare-default',
                                    removeAnimationClass;

                                if ('slide-left' === popupAnimation) {
                                    animationClass = 'tmpcoder-added-to-compare-slide-in-left';
                                    removeAnimationClass = 'tmpcoder-added-to-compare-slide-out-left';
                                } else if ('scale-up' === popupAnimation) {
                                    animationClass = 'tmpcoder-added-to-compare-scale-up';
                                    removeAnimationClass = 'tmpcoder-added-to-compare-scale-down';
                                } else if ('skew' === popupAnimation) {
                                    animationClass = 'tmpcoder-added-to-compare-skew';
                                    removeAnimationClass = 'tmpcoder-added-to-compare-skew-off';
                                } else if ('fade' === popupAnimation) {
                                    animationClass = 'tmpcoder-added-to-compare-fade';
                                    removeAnimationClass = 'tmpcoder-added-to-compare-fade-out';
                                } else {
                                    removeAnimationClass = 'tmpcoder-added-to-compare-popup-hide';
                                }

                                if ('' !== popupImageSrc) {
                                    popupImage = '<div class="tmpcoder-added-tcomp-popup-img"><img src=' + popupImageSrc + ' alt="" /></div>';
                                } else {
                                    popupImage = '';
                                }

                                if (!($scope.find('.tmpcoder-grid').find('#tmpcoder-added-to-comp-' + product_id).length > 0)) {
                                    $scope.find('.tmpcoder-grid').append('<div id="tmpcoder-added-to-comp-' + product_id + '" class="tmpcoder-added-to-compare-popup ' + animationClass + '">' + popupImage + '<div class="tmpcoder-added-tc-title"><p>' + popupText + ' was added to Compare</p><p><a target=' + popupTarget + ' href=' + popupLink + '>View Compare</a></p></div></div>');

                                    setTimeout(() => {
                                        $scope.find('#tmpcoder-added-to-comp-' + product_id).addClass(removeAnimationClass);
                                        setTimeout(() => {
                                            $scope.find('#tmpcoder-added-to-comp-' + product_id).remove();
                                        }, animTime * 1000);
                                    }, fadeOutIn * 1000);
                                }
                            }
                        },
                        error: function (response) {
                            var error_message = response.responseJSON.message;
                            // Display error message
                            alert(error_message);
                        }
                    });
                });

                $scope.find('.tmpcoder-compare-remove').click(function (e) {
                    e.preventDefault();
                    var product_id = $(this).data('product-id');
                    $(this).fadeTo(500, 0);

                    $.ajax({
                        url: tmpcoder_plugin_script.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'remove_from_compare',
                            nonce: tmpcoder_plugin_script.nonce,
                            product_id: product_id
                        },
                        success: function () {
                            $scope.find('.tmpcoder-compare-remove[data-product-id="' + product_id + '"]').hide();
                            $scope.find('.tmpcoder-compare-add[data-product-id="' + product_id + '"]').show();
                            $scope.find('.tmpcoder-compare-add[data-product-id="' + product_id + '"]').fadeTo(500, 1);
                            changeActionTargetProductId(product_id);
                            $(document).trigger('removed_from_compare');
                        }
                    });
                });

                $(document).on('removed_from_compare', function () {
                    $scope.find('.tmpcoder-compare-remove[data-product-id="' + actionTargetProductId + '"]').hide();
                    $scope.find('.tmpcoder-compare-add[data-product-id="' + actionTargetProductId + '"]').show();
                    $scope.find('.tmpcoder-compare-add[data-product-id="' + actionTargetProductId + '"]').fadeTo(500, 1);
                });

            }
        }

        function addRemoveWishlist() {

            let isPopupActive = false;
            if (iGrid.find('.tmpcoder-wishlist-add').length) {

                $scope.find('.tmpcoder-wishlist-add').click(function (e) {
                    e.preventDefault();
                    var event_target = $(this);
                    var product_id = $(this).data('product-id');

                    event_target.fadeTo(500, 0);

                    $.ajax({
                        url: tmpcoder_plugin_script.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'add_to_wishlist',
                            nonce: tmpcoder_plugin_script.nonce,
                            product_id: product_id
                        },
                        success: function () {
                            $scope.find('.tmpcoder-wishlist-add[data-product-id="' + product_id + '"]').hide();
                            $scope.find('.tmpcoder-wishlist-remove[data-product-id="' + product_id + '"]').show();
                            $scope.find('.tmpcoder-wishlist-remove[data-product-id="' + product_id + '"]').fadeTo(500, 1);
                            changeActionTargetProductId(product_id);
                            $(document).trigger('added_to_wishlist');

                            if ('sidebar' === event_target.data('atw-popup')) {
                                // TMPCODER INFO -  configure after adding wishlist dropdown functinality
                                if ($('.tmpcoder-wishlist-toggle-btn').length) {
                                    $('.tmpcoder-wishlist-toggle-btn').each(function () {
                                        if ('none' === $(this).next('.tmpcoder-wishlist').css('display')) {
                                            $(this).trigger('click');
                                        }
                                    });
                                }
                            } else if ('popup' === event_target.data('atw-popup')) {
                                // Popup Link needs wishlist
                                var popupItem = event_target.closest('.tmpcoder-grid-item'),
                                    popupText = popupItem.find('.tmpcoder-grid-item-title').text(),
                                    popupLink = tmpcoder_plugin_script.wishlistPageURL,
                                    popupTarget = 'yes' == event_target.data('open-in-new-tab') ? '_blank' : '_self',
                                    popupImageSrc = popupItem.find('.tmpcoder-grid-image-wrap').length ? popupItem.find('.tmpcoder-grid-image-wrap').data('src') : '',
                                    popupAnimation = event_target.data('atw-animation'),
                                    fadeOutIn = event_target.data('atw-fade-out-in'),
                                    animTime = event_target.data('atw-animation-time'),
                                    popupImage,
                                    animationClass = 'tmpcoder-added-to-wishlist-default',
                                    removeAnimationClass;

                                if ('slide-left' === popupAnimation) {
                                    animationClass = 'tmpcoder-added-to-wishlist-slide-in-left';
                                    removeAnimationClass = 'tmpcoder-added-to-wishlist-slide-out-left';
                                } else if ('scale-up' === popupAnimation) {
                                    animationClass = 'tmpcoder-added-to-wishlist-scale-up';
                                    removeAnimationClass = 'tmpcoder-added-to-wishlist-scale-down';
                                } else if ('skew' === popupAnimation) {
                                    animationClass = 'tmpcoder-added-to-wishlist-skew';
                                    removeAnimationClass = 'tmpcoder-added-to-wishlist-skew-off';
                                } else if ('fade' === popupAnimation) {
                                    animationClass = 'tmpcoder-added-to-wishlist-fade';
                                    removeAnimationClass = 'tmpcoder-added-to-wishlist-fade-out';
                                } else {
                                    removeAnimationClass = 'tmpcoder-added-to-wishlist-popup-hide';
                                }

                                if ('' !== popupImageSrc) {
                                    popupImage = '<div class="tmpcoder-added-tw-popup-img"><img src=' + popupImageSrc + ' alt="" /></div>';
                                } else {
                                    popupImage = '';
                                }
                                if (!isPopupActive) {
                                    isPopupActive = true;

                                    if (!($scope.find('.tmpcoder-grid').find('#tmpcoder-added-to-wish-' + product_id).length > 0)) {
                                        $scope.find('.tmpcoder-grid').append('<div id="tmpcoder-added-to-wish-' + product_id + '" class="tmpcoder-added-to-wishlist-popup ' + animationClass + '">' + popupImage + '<div class="tmpcoder-added-tw-title"><p>' + popupText + ' was added to Wishlist</p><p><a target="' + popupTarget + '" href=' + popupLink + '>View Wishlist</a></p></div></div>');

                                        setTimeout(() => {
                                            $scope.find('#tmpcoder-added-to-wish-' + product_id).addClass(removeAnimationClass);
                                            setTimeout(() => {
                                                $scope.find('#tmpcoder-added-to-wish-' + product_id).remove();
                                            }, animTime * 1000);
                                        }, fadeOutIn * 1000);
                                    }
                                }
                            }
                        },
                        error: function (response) {
                            var error_message = response.responseJSON.message;
                            // Display error message
                            alert(error_message);
                        }
                    });
                });

                $scope.find('.tmpcoder-wishlist-remove').on('click', function (e) {
                    e.preventDefault();
                    var product_id = $(this).data('product-id');

                    $(this).fadeTo(500, 0);

                    $.ajax({
                        url: tmpcoder_plugin_script.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'remove_from_wishlist',
                            nonce: tmpcoder_plugin_script.nonce,
                            product_id: product_id
                        },
                        success: function () {
                            $scope.find('.tmpcoder-wishlist-remove[data-product-id="' + product_id + '"]').hide();
                            $scope.find('.tmpcoder-wishlist-add[data-product-id="' + product_id + '"]').show();
                            $scope.find('.tmpcoder-wishlist-add[data-product-id="' + product_id + '"]').fadeTo(500, 1);
                            changeActionTargetProductId(product_id);
                            $(document).trigger('removed_from_wishlist');
                        }
                    });
                });

                $(document).on('removed_from_wishlist', function () {
                    $scope.find('.tmpcoder-wishlist-remove[data-product-id="' + actionTargetProductId + '"]').hide();
                    $scope.find('.tmpcoder-wishlist-add[data-product-id="' + actionTargetProductId + '"]').show();
                    $scope.find('.tmpcoder-wishlist-add[data-product-id="' + actionTargetProductId + '"]').fadeTo(500, 1);
                });

            }
        }
    }
    
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/eicon-woocommerce.default', widgetGrid);
    });
})(jQuery);