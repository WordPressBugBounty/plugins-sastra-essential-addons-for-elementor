(function ($) {
  "use strict";

  // Initial token retrieval
  tmpcoder.getToken();

  // Load More button click event
  $(document).on("click", ".tmpcoder-load-more-button", function (e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    var $this = $(this),
        $LoaderSpan = $(".tmpcoder_load_more_text", $this),
        $text = $LoaderSpan.html(),
        $widget_id = $this.data("widget"),
        $page_id = $this.data("page-id"),
        $nonce = localize.nonce,
        $scope = $(".elementor-element-" + $widget_id),
        $class = $this.data("class"),
        $args = $this.data("args"),
        $layout = $this.data("layout"),
        $template_info = $this.data("template"),
        $page = parseInt($this.data("page")) + 1,
        $max_page = $this.data("max-page") !== undefined ? parseInt($this.data("max-page")) : false,
        $exclude_ids = [],
        $active_term_id = 0,
        $active_taxonomy = '';

    $this.attr('disabled', true);

    if (typeof $widget_id === "undefined" || typeof $args === "undefined") return;

    var obj = {};
    var $data = {
      action: "load_more",
      class: $class,
      args: $args,
      page: $page,
      page_id: $page_id,
      widget_id: $widget_id,
      nonce: $nonce,
      template_info: $template_info
    };

    // Woo Product Gallery logic
    if ($data.class === "Spexo_Addons_Elementor\\Elements\\Woo_Product_Gallery") {
      var $taxonomy = {
        taxonomy: $('.tmpcoder-cat-tab li a.active', $scope).data('taxonomy'),
        field: 'term_id',
        terms: $('.tmpcoder-cat-tab li a.active', $scope).data('id'),
        terms_tag: $('.tmpcoder-cat-tab li a.active', $scope).data('tagid')
      };

      if (localStorage.getItem('tmpcoder-cat-tab') === 'true') {
        localStorage.removeItem('tmpcoder-cat-tab');
        var $gallery_page = 2;
      } else {
        var active_tab = $('.tmpcoder-cat-tab li a.active', $scope);
        var paging = parseInt(active_tab.data("page"));

        if (isNaN(paging)) {
          paging = active_tab.length ? 1 : parseInt($('.tmpcoder-load-more-button', $scope).data("page")) || 1;
          active_tab.data("page", 1);
        }

        var $gallery_page = paging + 1;
      }

      $data.taxonomy = $taxonomy;
      $data.page = isNaN($gallery_page) ? $page : $gallery_page;
    }

    // Dynamic Filterable Gallery logic
    if ($data.class === "Spexo_Addons_Elementor\\Pro\\Elements\\Dynamic_Filterable_Gallery") {
      $('.dynamic-gallery-item-inner', $scope).each(function () {
        $exclude_ids.push($(this).data('itemid'));
      });

      $active_term_id = $(".elementor-element-" + $widget_id + ' .dynamic-gallery-category.active').data('termid') || 0;
      $active_taxonomy = $(".elementor-element-" + $widget_id + ' .dynamic-gallery-category.active').data('taxonomy') || '';

      $data.page = 1;
      $data.exclude_ids = JSON.stringify($exclude_ids);
      $data.active_term_id = $active_term_id;
      $data.active_taxonomy = $active_taxonomy;
    }

    // Parse arguments
    String($args).split("&").forEach(function (item) {
      var arr = String(item).split("=");
      obj[arr[0]] = arr[1];
    });

    // Exclude already printed posts if orderby is random
    if (obj.orderby === "rand") {
      var $printed = $(".tmpcoder-grid-post");
      if ($printed.length) {
        var $ids = [];
        $printed.each(function () {
          $ids.push($(this).data("id"));
        });
        $data.post__not_in = $ids;
      }
    }

    $this.addClass("button--loading");
    $LoaderSpan.html(localize.i18n.loading);

    function filterable_gallery_load_more_btn($btn) {
      var active_tab = $btn.closest('.tmpcoder-filter-gallery-wrapper').find('.dynamic-gallery-category.active'),
          active_filter = active_tab.data('filter'),
          rest_filter = active_tab.siblings().not('.no-more-posts');

      $btn.addClass('hide');
      active_tab.addClass('no-more-posts');

      if (rest_filter.length === 1 && rest_filter.data('filter') === '*') {
        rest_filter.addClass('no-more-posts');
      }

      if (active_filter === '*') {
        active_tab.siblings().addClass('no-more-posts');
      }
    }

    // AJAX request
    $.ajax({
      url: localize.ajaxurl,
      type: "post",
      data: $data,
      success: function (response) {
        var $content = $(response);
        $this.removeAttr('disabled');

        if ($content.hasClass("no-posts-found") || $content.length === 0) {
          if ($data.class === "Spexo_Addons_Elementor\\Elements\\Woo_Product_Gallery") {
            $this.removeClass('button--loading').addClass('hide-load-more');
            $LoaderSpan.html($text);
            if ($this.parent().hasClass('tmpcoder-infinity-scroll')) {
              $this.parent().remove();
            }
          } else if ($data.class === "Spexo_Addons_Elementor\\Pro\\Elements\\Dynamic_Filterable_Gallery") {
            $this.removeClass('button--loading');
            $LoaderSpan.html($text);
            filterable_gallery_load_more_btn($this);
          } else {
            $this.remove();
          }
        } else {
            if ($data.class === "TMPCODER\\Widgets\\Product_Grid") {
            $content = $content.filter("li");
            $(".tmpcoder-product-grid .products", $scope).append($content);

            if ($layout === "masonry") {
              var dynamicID = "tmpcoder-product-" + Date.now();
              var $isotope = $(".tmpcoder-product-grid .products", $scope).isotope();
              $isotope.isotope("appended", $content).isotope("layout");
              $isotope.imagesLoaded().progress(function () {
                $isotope.isotope("layout");
              });

              $content.find(".woocommerce-product-gallery").addClass(dynamicID).addClass("tmpcoder-new-product");
              $(".woocommerce-product-gallery." + dynamicID, $scope).each(function () {
                $(this).wc_product_gallery();
              });
            } else {
              var _dynamicID = "tmpcoder-product-" + Date.now();
              $content.find('.woocommerce-product-gallery').addClass(_dynamicID).addClass('tmpcoder-new-product');
              $(".woocommerce-product-gallery." + _dynamicID, $scope).each(function () {
                $(this).wc_product_gallery();
              });
            }

            if ($page >= $max_page) {
              $this.remove();
            }
          } else {
            $(".tmpcoder-post-appender", $scope).append($content);

            if ($layout === "masonry") {
              var $isotope = $(".tmpcoder-post-appender", $scope).isotope();
              $isotope.isotope("appended", $content).isotope("layout");
              $isotope.imagesLoaded().progress(function () {
                $isotope.isotope("layout");
              });
            }
          }

          $this.removeClass("button--loading");
          $LoaderSpan.html($text);

          if ($data.class === "Spexo_Addons_Elementor\\Elements\\Woo_Product_Gallery" && $('.tmpcoder-cat-tab li a.active', $scope).length) {
            $('.tmpcoder-cat-tab li a.active', $scope).data("page", $gallery_page);
          } else {
            $this.data("page", $page);
          }

          if ($data.class === "Spexo_Addons_Elementor\\Pro\\Elements\\Dynamic_Filterable_Gallery") {
            var found_posts = $($content[0]);
            if (found_posts.hasClass('found_posts') && (found_posts.text() - obj.posts_per_page < 1)) {
              filterable_gallery_load_more_btn($this);
            }
          } else if ($max_page && $data.page >= $max_page) {
            $this.addClass('hide-load-more');
          }
        }
      },
      error: function (response) {
        console.log(response);
      }
    });
  });

  // Infinity Scroll
  $(window).on('scroll', function () {
    var scrollElements = $('.tmpcoder-infinity-scroll');
    if (scrollElements.length < 1) return false;

    $.each(scrollElements, function (index, element) {
      var scrollElement = $(element),
          offset = scrollElement.data('offset'),
          elementTop = scrollElement.offset().top,
          elementBottom = elementTop + scrollElement.outerHeight() - offset,
          viewportTop = $(window).scrollTop(),
          viewportHalf = viewportTop + $(window).height() - offset,
          inView = elementBottom > viewportTop && elementTop < viewportHalf;

      if (inView) {
        $(".tmpcoder-load-more-button", scrollElement).trigger('click');
      }
    });
  });
})(jQuery);
