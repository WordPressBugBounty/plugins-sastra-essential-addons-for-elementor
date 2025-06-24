(function ($) {
  window.isEditMode = false;

  window.tmpcoder = window.ea = {
    hooks: wp.hooks.createHooks(), // Make sure @wordpress/hooks is enqueued
    isEditMode: false,

    elementStatusCheck(name) {
      if (window.eaElementList && name in window.eaElementList) {
        return true;
      } else {
        window.eaElementList = {
          ...(window.eaElementList || {}),
          [name]: true
        };
        return false;
      }
    },

    debounce(func, delay) {
      let timeout;
      return function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, arguments), delay);
      };
    },

    getToken() {
      if (localize.nonce && !tmpcoder.noncegenerated) {
        $.post(localize.ajaxurl, { action: "tmpcoder_get_token" }, response => {
          if (response.success) {
            localize.nonce = response.data.nonce;
            tmpcoder.noncegenerated = true;
          }
        });
      }
    },

    sanitizeURL(url) {
      if (url.startsWith('/') || url.startsWith('#')) return url;
      try {
        const urlObject = new URL(url);
        const validProtocols = ['http:', 'https:', 'ftp:', 'mailto:', 'tel:'];
        if (!validProtocols.includes(urlObject.protocol)) {
          throw new Error('Invalid protocol');
        }
        return urlObject.toString();
      } catch {
        return '#';
      }
    }
  };

  // Reinit certain widgets
  tmpcoder.hooks.addAction("widgets.reinit", "ea", function ($content) {
    const selectors = [
      ".tmpcoder-filter-gallery-container",
      ".tmpcoder-post-grid:not(.tmpcoder-post-carousel)",
      ".tmpcoder-twitter-feed-masonry",
      ".tmpcoder-instafeed",
      ".premium-gallery-container"
    ];
    selectors.forEach(sel => {
      const $el = $content.find(sel);
      if ($el.length) $el.isotope("layout");
    });

    const triggers = {
      ".tmpcoder-event-calendar-cls": "eventCalendar.reinit",
      ".tmpcoder-testimonial-slider": "testimonialSlider.reinit",
      ".tmpcoder-tm-carousel": "teamMemberCarousel.reinit",
      ".tmpcoder-post-carousel:not(.tmpcoder-post-grid)": "postCarousel.reinit",
      ".tmpcoder-logo-carousel": "logoCarousel.reinit",
      ".tmpcoder-twitter-feed-carousel": "twitterCarousel.reinit"
    };
    for (const [sel, action] of Object.entries(triggers)) {
      if ($content.find(sel).length) {
        tmpcoder.hooks.doAction(action);
      }
    }
  });

  // Resize Swiper inside advanced tabs/accordion
  const ea_swiper_slider_init_inside_template = content => {
    if (window.tmpcoderPreventResizeOnClick === undefined) {
      window.dispatchEvent(new Event('resize'));
    }
    content = typeof content === 'object' ? content : $(content);
    content.find('.swiper-wrapper').each(function () {
      $(this).css('transform', $(this).css('transform'));
    });
  };

  tmpcoder.hooks.addAction("ea-advanced-tabs-triggered", "ea", ea_swiper_slider_init_inside_template);
  tmpcoder.hooks.addAction("ea-advanced-accordion-triggered", "ea", ea_swiper_slider_init_inside_template);

  $(window).on("elementor/frontend/init", function () {
    window.isEditMode = elementorFrontend.isEditMode();
    window.tmpcoder.isEditMode = window.isEditMode;

    tmpcoder.hooks.doAction("init");
    if (window.isEditMode) {
      tmpcoder.hooks.doAction("editMode.init");
    }
  });

  // Advanced Accordion Hashclick
  let isTriggerOnHashchange = true;
  window.addEventListener('hashchange', () => {
    if (!isTriggerOnHashchange) return;
    let hash = window.location.hash.substr(1);
    if (!hash || hash === 'undefined') return;
    hash = hash === 'safari' ? 'tmpcoder-safari' : hash;
    if (/^[A-Za-z][-A-Za-z0-9_:.]*$/.test(hash)) {
      $(`#${hash}`).trigger('click');
    }
  });

  $('a').on('click', function () {
    let hashURL = $(this).attr('href') || '';
    let isStartWithHash = hashURL.startsWith('#');
    if (!isStartWithHash) {
      hashURL = hashURL.replace(localize.page_permalink, '');
      isStartWithHash = hashURL.startsWith('#');
    }
    if (isStartWithHash) {
      isTriggerOnHashchange = false;
      setTimeout(() => { isTriggerOnHashchange = true; }, 100);
    }

    try {
      if (hashURL.startsWith('#!')) {
        $(`${hashURL.replace('#!', '#')}`).trigger('click');
      } else if ($(hashURL).hasClass('tmpcoder-tab-item-trigger') || $(hashURL).hasClass('tmpcoder-accordion-header')) {
        $(hashURL).trigger('click');
        const tabs = $(hashURL).closest('.tmpcoder-advance-tabs');
        if (tabs.length) {
          const offset = parseFloat(tabs.data('custom-id-offset')) || 0;
          $('html, body').animate({ scrollTop: $(hashURL).offset().top - offset }, 300);
        }
      }
    } catch (err) { }
  });

  $(document).on('click', '.e-n-tab-title', function () {
    setTimeout(() => window.dispatchEvent(new Event('resize')), 100);
  });

  // Quantity plus/minus for Savoy theme
  $(document).on('click', '.theme-savoy .tmpcoder-product-popup .nm-qty-minus, .theme-savoy .tmpcoder-product-popup .nm-qty-plus', function () {
    const $qty = $(this).closest('.quantity').find('.qty');
    let val = parseFloat($qty.val()) || 0;
    const max = parseFloat($qty.attr('max')) || '';
    const min = parseFloat($qty.attr('min')) || 0;
    const step = parseFloat($qty.attr('step')) || 1;

    if ($(this).hasClass('nm-qty-plus')) {
      $qty.val((max && val >= max) ? max : val + step);
    } else {
      $qty.val((min && val <= min) ? min : Math.max(val - step, 0));
    }
  });

  // In viewport checker
  $.fn.isInViewport = function (offset = 2) {
    if (!this.length) return false;
    const elTop = this.offset().top;
    const elBottom = elTop + this.outerHeight() / offset;
    const vpTop = $(window).scrollTop();
    const vpHalf = vpTop + $(window).height() / offset;
    return elBottom > vpTop && elTop < vpHalf;
  };

  // Auto trigger login/reset popups
  $(document).ready(function () {
    const params = new URLSearchParams(location.search);
    if (params.has('popup-selector') && (params.has('tmpcoder-lostpassword') || params.has('tmpcoder-resetpassword'))) {
      let selector = params.get('popup-selector').replace(/_/g, " ");
      if (/^[A-Za-z.#][A-Za-z0-9_:.#\s-]*$/.test(selector)) {
        setTimeout(() => { $(selector).trigger('click'); }, 300);
      }
    }
  });

  // Onpage template editing (Elementor Pro)
  $(document).on('click', '.tmpcoder-onpage-edit-template', function () {
    const $this = $(this);
    const templateID = $this.data('tmpcoder-template-id');
    const pageID = $this.data('page-id');
    const mode = $this.data('mode');

    if (mode === 'edit') {
      parent.window.$e.internal('panel/state-loading');
      parent.window.$e.run('editor/documents/switch', {
        id: parseInt(templateID)
      }).then(() => {
        $this.data('mode', 'save');
        $this.find('span').text('Save & Back');
        $this.find('i').addClass('eicon-arrow-left').removeClass('eicon-edit');
        $this.closest('.tmpcoder-onpage-edit-template-wrapper').addClass('tmpcoder-onpage-edit-activate').parent().addClass('tmpcoder-widget-otea-active');
        parent.window.$e.internal('panel/state-ready');
      });
    } else if (mode === 'save') {
      parent.window.$e.internal('panel/state-loading');
      parent.window.$e.run('editor/documents/switch', {
        id: parseInt(pageID),
        mode: 'save',
        shouldScroll: false
      }).then(() => {
        parent.window.$e.internal('panel/state-ready');
        $this.data('mode', 'edit');
      });
    }
  });
})(jQuery);
