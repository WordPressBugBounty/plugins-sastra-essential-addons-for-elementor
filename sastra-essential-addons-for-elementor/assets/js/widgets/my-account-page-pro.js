(function ($) {
    "use strict";

    const widgetPageMyAccount = function($scope) {

        if ( editorCheck() ) {
        
            $scope.find(".woocommerce-MyAccount-content").each(function() {
                if ( $(this).index() !== 1 ) {
                    $(this).css('display', 'none');
                }
            });

            $scope.find('.woocommerce-MyAccount-navigation-link').on('click', function() {
                var tabContent, tabLinks, pageName;

                tabContent = $scope.find(".woocommerce-MyAccount-content");
                tabContent.each(function() {
                    $(this).css('display', 'none');
                });

                tabLinks = $scope.find(".woocommerce-MyAccount-navigation-link");
                tabLinks.each(function() {
                    $(this).removeClass('is-active');
                });

                pageName = $(this).attr('class').slice($(this).attr('class').indexOf('--') + 2);
                $(this).addClass('is-active');

                $scope.find('[tmpcoder-my-account-page="'+ pageName +'"]').css('display', 'block');

            });
        }
        
        if ( $scope.find('.tmpcoder-wishlist-remove').length ) {
            $scope.find('.tmpcoder-wishlist-remove').on('click', function(e) {
                e.preventDefault();
                var product_id = $(this).data('product-id');
                $.ajax({
                    url: tmpcoder_plugin_script.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'remove_from_wishlist',
                        nonce: tmpcoder_plugin_script.nonce,
                        product_id: product_id,
                    },
                    success: function() {
                        $scope.find('.tmpcoder-wishlist-product[data-product-id="' + product_id + '"]').remove();
                        changeActionTargetProductId(product_id);
                        $(document).trigger('removed_from_wishlist');
                    }
                });
            });

            $(document).on('removed_from_wishlist', function() {
                $scope.find('.tmpcoder-wishlist-product[data-product-id="' + actionTargetProductId + '"]').remove();
            });

        }
        
    } // End of widgetPageMyAccount
    
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/tmpcoder-my-account-pro.default",
            widgetPageMyAccount);
    });
})(jQuery);