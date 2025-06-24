
// jQuery(window).on('elementor/frontend/init', function () {
tmpcoder.hooks.addAction("init", "ea", function () {

  const productGrid = function ($scope, $) {
    elementorFrontend.hooks.doAction("quickViewAddMarkup", $scope, $);

    const $wrap = $scope.find("#tmpcoder-product-grid");
    const widgetId = $wrap.data("widget-id");
    const pageId = $wrap.data("page-id");
    const nonce = $wrap.data("nonce");

    const body = document.body;
    const overlay = document.createElement("div");
    overlay.id = "wcpc-overlay";
    overlay.classList.add("wcpc-overlay");
    body.appendChild(overlay);

    const overlayNode = document.getElementById("wcpc-overlay");
    const $doc = $(document);

    let loader = false;
    let compareBtn = false;
    let hasCompareIcon = false;
    let compareBtnSpan = false;
    let requestType = false;

    const iconBeforeCompare = '<i class="fas fa-exchange-alt"></i>';
    const iconAfterCompare = '<i class="fas fa-check-circle"></i>';

    const modalTemplate = `
      <div class="tmpcoder-wcpc-modal">
        <i title="Close" class="close-modal far fa-times-circle"></i>
        <div class="modal__content" id="tmpcoder_modal_content"></div>
      </div>
    `;
    $(body).append(modalTemplate);

    const $modalContentWraper = $("#tmpcoder_modal_content");
    const modal = document.querySelector(".tmpcoder-wcpc-modal");

    const ajaxDataBase = [
      { name: "action", value: "tmpcoder_product_grid" },
      { name: "widget_id", value: widgetId },
      { name: "page_id", value: pageId },
      { name: "nonce", value: nonce }
    ];

    const sendData = (ajaxData, successCb, errorCb, beforeCb, completeCb) => {
      $.ajax({
        url: localize.ajaxurl,
        type: "POST",
        dataType: "json",
        data: ajaxData,
        beforeSend: beforeCb,
        success: successCb,
        error: errorCb,
        complete: completeCb
      });
    };

    if ($wrap.hasClass('masonry')) {
      $doc.ajaxComplete(() => $(window).trigger('resize'));
    }

    // Compare button handler
    $doc.on("click", ".tmpcoder-wc-compare", function (e) {
      e.preventDefault();
      requestType = "compare";

      compareBtn = $(this);
      compareBtnSpan = compareBtn.find(".tmpcoder-wc-compare-text");
      hasCompareIcon = !compareBtnSpan.length && compareBtn.hasClass("tmpcoder-wc-compare-icon");
      if (!hasCompareIcon) loader = compareBtn.find(".tmpcoder-wc-compare-loader").show();

      const product_id = compareBtn.data("product-id");
      let oldProductIds = JSON.parse(localStorage.getItem('productIds') || '[]');
      oldProductIds.push(product_id);

      const ajaxData = [...ajaxDataBase,
        { name: "product_id", value: product_id },
        { name: "product_ids", value: JSON.stringify(oldProductIds) }
      ];

      sendData(ajaxData, handleSuccess, handleError);
    });

    // Modal close handler
    $doc.on("click", ".close-modal", () => {
      modal.style.visibility = overlayNode.style.visibility = "hidden";
      modal.style.opacity = overlayNode.style.opacity = "0";
    });

    // Remove from compare handler
    $doc.on("click", ".tmpcoder-wc-remove", function (e) {
      e.preventDefault();
      requestType = "remove";

      const $rBtn = $(this);
      const productId = $rBtn.data("product-id");
      $rBtn.addClass("disable").prop("disabled", true);

      let oldProductIds = JSON.parse(localStorage.getItem('productIds') || '[]');
      oldProductIds.push(productId);

      const rmData = [...ajaxDataBase,
        { name: "product_id", value: productId },
        { name: "remove_product", value: 1 },
        { name: "product_ids", value: JSON.stringify(oldProductIds) }
      ];

      compareBtn = $(`button[data-product-id='${productId}']`);
      compareBtnSpan = compareBtn.find(".tmpcoder-wc-compare-text");
      hasCompareIcon = !compareBtnSpan.length && compareBtn.hasClass("tmpcoder-wc-compare-icon");

      sendData(rmData, handleSuccess, handleError);
    });

    // Success callback
    function handleSuccess(data) {
      if (data?.success) {
        $modalContentWraper.html(data.data.compare_table);
        modal.style.visibility = overlayNode.style.visibility = "visible";
        modal.style.opacity = overlayNode.style.opacity = "1";
        localStorage.setItem('productIds', JSON.stringify(data.data.product_ids));
      }

      loader && loader.hide();

      if (requestType === "compare") {
        if (compareBtnSpan?.length) compareBtnSpan.text(localize.i18n.added);
        else if (hasCompareIcon) compareBtn.html(iconAfterCompare);
      } else if (requestType === "remove") {
        if (compareBtnSpan?.length) compareBtnSpan.text(localize.i18n.compare);
        else if (hasCompareIcon) compareBtn.html(iconBeforeCompare);
      }
    }

    function handleError(xhr, err) {
      console.error(err.toString());
    }

    // Pagination handler
    $(".tmpcoder-woo-pagination", $scope).on("click", "a", function (e) {
      e.preventDefault();

      const $this = $(this);
      const navClass = $this.closest(".tmpcoder-woo-pagination");
      const nth = $this.data("pnumber");
      const lmt = navClass.data("plimit");
      const args = navClass.data("args");
      const widgetid = navClass.data("widgetid");
      const pageid = navClass.data("pageid");
      const template_info = navClass.data("template");
      const widgetclass = `.elementor-element-${widgetid}`;

      // Fetch products
      $.post(localize.ajaxurl, {
        action: "woo_product_pagination_product",
        number: nth,
        limit: lmt,
        args,
        widget_id: widgetid,
        page_id: pageid,
        security: localize.nonce,
        templateInfo: template_info
      }, function (response) {
        $(`${widgetclass} .tmpcoder-product-grid .products`).html(response);
        $(`${widgetclass} .woocommerce-product-gallery`).each(function () {
          $(this).wc_product_gallery();
        });
        $('html, body').animate({ scrollTop: $(`${widgetclass} .tmpcoder-product-grid`).offset().top - 50 }, 500);
      }).beforeSend = () => $(widgetclass).addClass("tmpcoder-product-loader");

      // Fetch pagination UI
      $.post(localize.ajaxurl, {
        action: "woo_product_pagination",
        number: nth,
        limit: lmt,
        args,
        widget_id: widgetid,
        page_id: pageid,
        security: localize.nonce,
        template_name: template_info.name
      }, function (response) {
        $(`${widgetclass} .tmpcoder-product-grid .tmpcoder-woo-pagination`).html(response);
        $('html, body').animate({ scrollTop: $(`${widgetclass} .tmpcoder-product-grid`).offset().top - 50 }, 500);
      }).complete = () => $(widgetclass).removeClass("tmpcoder-product-loader");
    });

    // tmpcoder.hooks.doAction("quickViewPopupViewInit", $scope, $);
    elementorFrontend.hooks.doAction("quickViewPopupViewInit", $scope, $);

    if (editorCheck()) {
      $(".tmpcoder-product-image-wrap .woocommerce-product-gallery").css("opacity", "1");
    }

    if (!$(document).find(".tmpcoder-woocommerce-popup-view").length) {
      $("body").append(`
        <div style="display: none" class="tmpcoder-woocommerce-popup-view tmpcoder-product-popup tmpcoder-product-zoom-in woocommerce">
          <div class="tmpcoder-product-modal-bg"></div>
          <div class="tmpcoder-popup-details-render tmpcoder-woo-slider-popup">
            <div class="tmpcoder-preloader"></div>
          </div>
        </div>
      `);
    }
  };

  if (tmpcoder.elementStatusCheck('tmpcoderProductGridLoad') && typeof window.forceFullyRun === "undefined") {
    return;
  }

  // tmpcoder.hooks.addAction("frontend/element_ready/eicon-woocommerce.default", productGrid);
  elementorFrontend.hooks.addAction("frontend/element_ready/eicon-woocommerce.default", productGrid);
});
