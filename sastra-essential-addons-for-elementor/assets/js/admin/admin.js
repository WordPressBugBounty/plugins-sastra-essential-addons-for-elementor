"use strict";
(function ($) {

  $(function () {
    var $clearCache = $(".tmpcoderjs-clear-cache"),
      $tmpcoderMenu = $("#toplevel_page_spexo-addons .toplevel_page_spexo-addons .wp-menu-name"),
      menuText = $tmpcoderMenu.text();
    $tmpcoderMenu.text(menuText.replace(/\s/, ""));
    $clearCache.on("click", "a", function (e) {
      e.preventDefault();
      var type = "all",
        $m = $(e.delegateTarget);
      if ($m.hasClass("tmpcoder-clear-page-cache")) {
        type = "page";
      }
      $m.addClass("tmpcoder-clear-cache--init");

      if ($clearCache.hasClass("tools-btn")) {
        $('.welcome-backend-loader').fadeIn();
        $('.tmpcoder-theme-welcome').css('opacity','0.5');
      }

      $.post(SpexoAdmin.ajax_url, {
        action: "tmpcoder_clear_cache",
        type: type,
        nonce: SpexoAdmin.nonce,
        post_id: SpexoAdmin.post_id
      }).done(function (res) {

        $m.removeClass("tmpcoder-clear-cache--init").addClass("tmpcoder-clear-cache--done");
        if ($clearCache.hasClass("tools-btn")) {
          $('.welcome-backend-loader').fadeOut();
          $('.tmpcoder-theme-welcome').css('opacity','1');
          $('.tmpcoder-settings-saved').stop().fadeIn(500).delay(1000).fadeOut(1000);
        }
        else
        {
            if ($('#wpbody').length){
                $('#wpbody').append('<div class="tmpcoder-css-regenerated tmpcoder-settings-saved"><span>Assets Regenerated</span><span class="dashicons dashicons-smiley"></span></div>');
            }
            else
            {
                $('body').append('<div class="tmpcoder-css-regenerated tmpcoder-settings-saved"><span>Assets Regenerated</span><span class="dashicons dashicons-smiley"></span></div>');
            }

            $('.tmpcoder-css-regenerated').css({
                position: 'fixed',
                zIndex: '99999',
                top: '60px',
                right: '30px',
                padding: '15px 25px',
                borderRadius: '3px',
                color: '#fff',
                background: '#562ad5',
                boxShadow: '0 2px 10px 3px rgba(0, 0, 0, .2)',
                textTransform: 'uppercase',
                fontWeight: '600',
                letterSpacing: '1px',
            });
            $('.tmpcoder-css-regenerated').stop().fadeIn(500).delay(1000).fadeOut(1000);
        }
      });
    });
  });

  /* Plugin Deactive Popup Js - Start */

    $(function () {

        var $document = $(document),
            $deactivationPopUp = $('.tmpcoder-deactivation-popup');


        if ($deactivationPopUp.length < 1)
            return;

        $(document).on('click', 'tr[data-slug="spexo-addons-for-elementor"] .deactivate a, tr[data-slug="spexo-addons-pro"] .deactivate a , tr[data-slug="sastra-essential-addons-for-elementor"] .deactivate a', function (event) {
            event.preventDefault();

            $deactivationPopUp.removeClass('hidden');

            if ($(this).attr('id') == 'deactivate-spexo-addons-pro') {
                $('.tmpcoder-deactivation-popup button[data-action]').addClass('tmpcoder-is-pro-addon');
            }
            else
            {
                $('.tmpcoder-deactivation-popup button[data-action]').removeClass('tmpcoder-is-pro-addon');    
            }

            var data_slug = $(this).closest('tr').attr('data-slug');
            
            $('.tmpcoder-deactivation-popup button[data-action]').attr('data-slug', data_slug);
        });

        $document.on('click', '.tmpcoder-deactivation-popup .close, .tmpcoder-deactivation-popup .dashicons,  .tmpcoder-deactivation-popup', function (event) {

            if (this === event.target) {
                $deactivationPopUp.addClass('hidden');
            }

        });

        $document.on('change', '.tmpcoder-deactivation-popup input[name][type="radio"]', function () {
            var $this = $(this);

            var value = $this.val(),
                name = $this.attr('name');

            value = typeof value === 'string' && value !== '' ? value : undefined;
            name = typeof name === 'string' && name !== '' ? name : undefined;

            if (value === undefined || name === undefined) {
                return;
            }

            var $targetedMessage = $('p[data-' + name + '="' + value + '"]'),
                $relatedSections = $this.parents('.body').find('section[data-' + name + ']'),
                $relatedMessages = $this.parents('.body').find('p[data-' + name + ']:not(p[data-' + name + '="' + value + '"])');

            $relatedMessages.addClass('hidden');
            $targetedMessage.removeClass('hidden');
            $relatedSections.removeClass('hidden');

        });

        $document.on('keyup', '.tmpcoder-deactivation-popup input[name], .tmpcoder-deactivation-popup textarea[name]', function (event) {

            var allowed = ['Enter', 'Escape'];

            if (!allowed.includes(event.key)) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            if (event.key === allowed[0]) {
                $('.tmpcoder-deactivation-popup [data-action="deactivation"]').click();
            } else if (event.key === allowed[1]) {
                $('.tmpcoder-deactivation-popup .close').click();
            }
        });

        $document.on('click', '.tmpcoder-deactivation-popup button[data-action]', function (event) {

            var $this = $(this),
                $optionsWrappers = $this.parents('.body').find('.options-wrap'),
                $toggle = $optionsWrappers.find('input[name][type="checkbox"]:checked, input[name][type="radio"]:checked'),
                $fields = $optionsWrappers.find('input[name], textarea[name]').not('input[type="checkbox"], input[type="radio"]');

            var data = {
                action: $this.data('action')
            };

            $this.text($this.attr('data-text'));

            var is_pro = false;
            if ($this.hasClass('tmpcoder-is-pro-addon')){
                is_pro = true;
            }

            data.action = typeof data.action === 'string' && data.action !== '' ? data.action : undefined;

            if ($toggle.length > 0) {
                $toggle.each(function () {
                    var $this = $(this),
                        value = $this.val(),
                        key = $this.attr('name');

                    if (typeof value === 'string' && value !== '' && typeof key === 'string' && key !== '') {
                        data[key] = value;
                    }
                });
            }

            if ($fields.length > 0) {
                $fields.each(function () {
                    var $this = $(this),
                        value = $this.val(),
                        key = $this.attr('name');

                    if (typeof value === 'string' && value !== '' && typeof key === 'string' && key !== '') {
                        data[key] = value;
                    }
                })
            }

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'tmpcoder_handle_feedback_action',
                    data: data,
                    is_pro:is_pro,
                    _wpnonce:SpexoAdmin._wpnonce
                },
                beforeSend: function () {
                    $this.prop('disabled', true);
                },
                error: function (error) {
                    console.log(error);
                },
                complete: function (res) {

                    $deactivationPopUp.addClass('hidden');
                    $this.prop('disabled', false);

                    console.log(res);

                    var $deactivateLink = $('tr[data-slug="'+$this.data('slug')+'"] .deactivate a');

                    if ($deactivateLink.length > 0) {
                        var deactivateUrl = $deactivateLink.attr('href');

                        if (typeof deactivateUrl === 'string' && deactivateUrl !== '') {
                            window.location.href = deactivateUrl;
                        } else {
                            window.location.reload();
                        }
                    }
                }
            });
        });
    });

    /* Plugin Deactive Popup Js - End */

    jQuery(document).on( 'click', '.tmpcoder-plugin-update-notice .notice-dismiss', function() {
        jQuery(document).find('.tmpcoder-plugin-update-notice').slideUp();
        console.log('works update dismiss');
        jQuery.post({
            url: SpexoAdmin.ajax_url,
            data: {
                nonce: SpexoAdmin._wpnonce_,
                action: 'tmpcoder_plugin_update_dismiss_notice',
            }
        });
    });

  /* Plugin Feature List Notice - end  */

    jQuery(document).on( 'click', '.tmpcoder-pro-features-notice .notice-dismiss', function() {

        jQuery('body').removeClass('tmpcoder-pro-features-body');

        jQuery(document).find('.tmpcoder-pro-features-notice-wrap').fadeOut();

        jQuery(document).find('.tmpcoder-pro-features-notice').slideUp();

        setTimeout(function(){
            jQuery(document).find('.tmpcoder-pro-features-notice').remove();
        },300);
        
        jQuery.post({
            url: SpexoAdmin.ajax_url,
            data: {
                nonce: SpexoAdmin._wpnonce_,
                action: 'tmpcoder_pro_features_dismiss_notice'
            }
        });
    });

})(jQuery);