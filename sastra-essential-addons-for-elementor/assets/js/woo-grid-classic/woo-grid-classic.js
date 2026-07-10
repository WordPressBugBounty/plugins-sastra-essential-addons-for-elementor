(function () {
	const handleProductGridClassicOosAtcClick = function ( e ) {
		const target = e.target.closest( '.tmpcoder-product-grid .tmpcoder-oos-atc-disabled' );

		if ( ! target ) {
			return;
		}

		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();

		if ( typeof localize !== 'undefined' && localize.i18n && localize.i18n.product_unavailable ) {
			window.alert( localize.i18n.product_unavailable );
		}
	};

	const bindProductGridClassicOosAtc = function () {
		document.removeEventListener( 'click', handleProductGridClassicOosAtcClick, true );
		document.addEventListener( 'click', handleProductGridClassicOosAtcClick, true );
	};

	bindProductGridClassicOosAtc();
	window.addEventListener( 'pageshow', bindProductGridClassicOosAtc );
	tmpcoder.hooks.addAction( 'init', 'ea', bindProductGridClassicOosAtc );
})();

tmpcoder.hooks.addAction("init", "ea", function() {

	const productGrid = function($scope, $) {
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

		const ajaxDataBase = [{
				name: "action",
				value: "tmpcoder_product_grid"
			},
			{
				name: "widget_id",
				value: widgetId
			},
			{
				name: "page_id",
				value: pageId
			},
			{
				name: "nonce",
				value: nonce
			}
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
		$doc.on("click", ".tmpcoder-wc-compare", function(e) {
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
				{
					name: "product_id",
					value: product_id
				},
				{
					name: "product_ids",
					value: JSON.stringify(oldProductIds)
				}
			];

			sendData(ajaxData, handleSuccess, handleError);
		});

		// Modal close handler
		$doc.on("click", ".close-modal", () => {
			modal.style.visibility = overlayNode.style.visibility = "hidden";
			modal.style.opacity = overlayNode.style.opacity = "0";
		});

		// Remove from compare handler
		$doc.on("click", ".tmpcoder-wc-remove", function(e) {
			e.preventDefault();
			requestType = "remove";

			const $rBtn = $(this);
			const productId = $rBtn.data("product-id");
			$rBtn.addClass("disable").prop("disabled", true);

			let oldProductIds = JSON.parse(localStorage.getItem('productIds') || '[]');
			oldProductIds.push(productId);

			const rmData = [...ajaxDataBase,
				{
					name: "product_id",
					value: productId
				},
				{
					name: "remove_product",
					value: 1
				},
				{
					name: "product_ids",
					value: JSON.stringify(oldProductIds)
				}
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
		$(".tmpcoder-woo-pagination", $scope).on("click", "a", function(e) {
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
			}, function(response) {
				$(`${widgetclass} .tmpcoder-product-grid .products`).html(response);
				$(`${widgetclass} .woocommerce-product-gallery`).each(function() {
					$(this).wc_product_gallery();
				});
				$('html, body').animate({
					scrollTop: $(`${widgetclass} .tmpcoder-product-grid`).offset().top - 50
				}, 500);
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
			}, function(response) {
				$(`${widgetclass} .tmpcoder-product-grid .tmpcoder-woo-pagination`).html(response);
				$('html, body').animate({
					scrollTop: $(`${widgetclass} .tmpcoder-product-grid`).offset().top - 50
				}, 500);
			}).complete = () => $(widgetclass).removeClass("tmpcoder-product-loader");
		});

		elementorFrontend.hooks.doAction("quickViewPopupViewInit", $scope, $);

		initAddedToCartAction($scope, $);

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

        // Secondary image on hover
		// Init Media Hover Link
		mediaHoverLink();

		// Media Hover Link
		function mediaHoverLink() {
			var iGrid = $scope.find('.tmpcoder-grid');
		//  var iGrid = $scope.find('.tmpcoder-product-wrap');

		if (!iGrid.length) {
			var iGridDefault = $scope.find('ul.products li.product ');

			if (iGridDefault.length) {
				var iGrid = $scope.find('ul.products li.product ');
			}
		}	

			if ('yes' === $scope.find('.tmpcoder-grid-image-wrap').data('img-on-hover')) {
				var img;
				var thisImgSrc;
				let secondaryImg;
				iGrid.find('.tmpcoder-grid-media-wrap').on('mouseover', function () {

					$(this).find('.tmpcoder-grid-image-wrap').css('position','relative');

					if ($(this).find('img:nth-of-type(2)').attr('src') !== undefined && $(this).find('img:nth-of-type(2)').attr('src') !== '') {

						if ($(this).closest('[data-widget_type="eicon-woocommerce.default"]')) {
							$(this).find('.grid-main-image').addClass('tmpcoder-hidden-img');
							$(this).find('img:nth-of-type(2)').removeClass('tmpcoder-hidden-img');
						}
						else {
							$(this).find('img:first-of-type').addClass('tmpcoder-hidden-img');
							$(this).find('img:nth-of-type(2)').removeClass('tmpcoder-hidden-img');
						}
					}
				});

				iGrid.find('.tmpcoder-grid-media-wrap').on('mouseleave', function () {

					if ($(this).find('img:nth-of-type(2)').attr('src') !== undefined && $(this).find('img:nth-of-type(2)').attr('src') !== '') {

						$(this).find('img:nth-of-type(2)').addClass('tmpcoder-hidden-img');
						$(this).find('img:first-of-type').removeClass('tmpcoder-hidden-img');
					}
				});
			}
		}
	};

	const openMiniCartSidebar = function($) {
		if ($('.tmpcoder-sticky-replace-header-yes').length) {
			if (!$('.tmpcoder-sticky-section-yes').hasClass('tmpcoder-visibility-hidden')) {
				$('.tmpcoder-sticky-section-yes .tmpcoder-mini-cart-toggle-wrap a').trigger('click');
			} else {
				$('.tmpcoder-hidden-header .tmpcoder-mini-cart-toggle-wrap a').trigger('click');
			}
		} else if ($('.tmpcoder-mini-cart-toggle-wrap a').length) {
			$('.tmpcoder-mini-cart-toggle-wrap a').each(function() {
				if ('none' === $(this).closest('.tmpcoder-mini-cart-inner').find('.tmpcoder-mini-cart').css('display')) {
					$(this).trigger('click');
				}
			});
		}
	};

	const initAddedToCartAction = function($scope, $) {
		if (!$scope.find('.add_to_cart_button[data-atc-popup="sidebar"]').length) {
			return;
		}

		$('body').on('added_to_cart.tmpcoderWcpc', function(ev, fragments, hash, button) {
			if (!button.closest($scope[0]).length || 'sidebar' !== button.data('atc-popup')) {
				return;
			}

			openMiniCartSidebar($);
		});
	};

	// Slider functionality
	const initProductSlider = function($scope, $) {
		const $slider = $scope.find('.tmpcoder-product-slider');
		const $container = $scope.find('#tmpcoder-product-grid');
		const $sliderContainer = $scope.find('.tmpcoder-slider-container');
			
		// console.log('initProductSlider');

		if ($slider.length && $container.hasClass('slider')) {
			// Count total products
			const totalProducts = $slider.find('.product').length;
			const sliderClass = $scope.attr('class');
			const sliderColumnsDesktop = sliderClass.match(/tmpcoder-grid-slider-columns-\d/) ? +sliderClass.match(/tmpcoder-grid-slider-columns-\d/).join().slice(-1) : (parseInt($container.data('slider-columns'), 10) || 3);
			const sliderColumnsWideScreen = sliderClass.match(/columns--widescreen\d/) ? +sliderClass.match(/columns--widescreen\d/).join().slice(-1) : sliderColumnsDesktop;
			const sliderColumnsLaptop = sliderClass.match(/columns--laptop\d/) ? +sliderClass.match(/columns--laptop\d/).join().slice(-1) : sliderColumnsDesktop;
			const sliderColumnsTablet = sliderClass.match(/columns--tablet\d/) ? +sliderClass.match(/columns--tablet\d/).join().slice(-1) : (parseInt($container.data('slider-columns-tablet'), 10) || 2);
			const sliderColumnsTabletExtra = sliderClass.match(/columns--tablet_extra\d/) ? +sliderClass.match(/columns--tablet_extra\d/).join().slice(-1) : sliderColumnsTablet;
			const sliderColumnsMobileExtra = sliderClass.match(/columns--mobile_extra\d/) ? +sliderClass.match(/columns--mobile_extra\d/).join().slice(-1) : sliderColumnsTablet;
			const sliderColumnsMobile = sliderClass.match(/columns--mobile\d/) ? +sliderClass.match(/columns--mobile\d/).join().slice(-1) : (parseInt($container.data('slider-columns-mobile'), 10) || 1);
			const sliderSlidesToScroll = parseInt($container.data('slider-slides-to-scroll'), 10) || 1;
			const infiniteLoopEnabled = $container.data('slider-infinite-loop') === 'yes';

			const getSlidesToShow = function(columns) {
				return Math.min(columns, totalProducts);
			};

			const getSlidesToScroll = function(columns) {
				return sliderSlidesToScroll > columns ? 1 : sliderSlidesToScroll;
			};

			const getInfinite = function(columns) {
				return infiniteLoopEnabled && totalProducts > columns;
			};

			// Get settings from data attributes (columns from wrapper classes — Post Grid parity)
			const settings = {
				slidesToShow: getSlidesToShow(sliderColumnsDesktop),
				slidesToScroll: getSlidesToScroll(sliderColumnsDesktop),
				autoplay: $container.data('slider-autoplay') === 'yes',
				autoplaySpeed: parseInt($container.data('slider-autoplay-speed'), 10) || 3000,
				pauseOnHover: $container.data('slider-pause-on-hover') === 'yes',
				infinite: getInfinite(sliderColumnsDesktop),
				arrows: $container.data('slider-navigation') === 'yes',
				dots: $container.data('slider-pagination') === 'yes',
				centerMode: false,
				variableWidth: false,
				adaptiveHeight: true,
				responsive: [
					{
						breakpoint: 10000,
						settings: {
							slidesToShow: getSlidesToShow(sliderColumnsWideScreen),
							slidesToScroll: getSlidesToScroll(sliderColumnsWideScreen),
							infinite: getInfinite(sliderColumnsWideScreen)
						}
					},
					{
						breakpoint: 2399,
						settings: {
							slidesToShow: getSlidesToShow(sliderColumnsDesktop),
							slidesToScroll: getSlidesToScroll(sliderColumnsDesktop),
							infinite: getInfinite(sliderColumnsDesktop)
						}
					},
					{
						breakpoint: 1221,
						settings: {
							slidesToShow: getSlidesToShow(sliderColumnsLaptop),
							slidesToScroll: getSlidesToScroll(sliderColumnsLaptop),
							infinite: getInfinite(sliderColumnsLaptop)
						}
					},
					{
						breakpoint: 1200,
						settings: {
							slidesToShow: getSlidesToShow(sliderColumnsTabletExtra),
							slidesToScroll: getSlidesToScroll(sliderColumnsTabletExtra),
							infinite: getInfinite(sliderColumnsTabletExtra)
						}
					},
					{
						breakpoint: 1024,
						settings: {
							slidesToShow: getSlidesToShow(sliderColumnsTablet),
							slidesToScroll: getSlidesToScroll(sliderColumnsTablet),
							infinite: getInfinite(sliderColumnsTablet)
						}
					},
					{
						breakpoint: 880,
						settings: {
							slidesToShow: getSlidesToShow(sliderColumnsMobileExtra),
							slidesToScroll: getSlidesToScroll(sliderColumnsMobileExtra),
							infinite: getInfinite(sliderColumnsMobileExtra)
						}
					},
					{
						breakpoint: 768,
						settings: {
							slidesToShow: getSlidesToShow(sliderColumnsMobile),
							slidesToScroll: getSlidesToScroll(sliderColumnsMobile),
							infinite: getInfinite(sliderColumnsMobile)
						}
					}
				]
			};

			// Custom navigation
			if (settings.arrows) {
				const $nav = $scope.find('.tmpcoder-slider-nav');
				if ($nav.length) {
					settings.prevArrow = $nav.find('.tmpcoder-slider-prev');
					settings.nextArrow = $nav.find('.tmpcoder-slider-next');
				}
			}

			// Custom dots container
			if (settings.dots) {
				const $dotsContainer = $scope.find('.tmpcoder-slider-dots');
				if ($dotsContainer.length) {
					settings.appendDots = $dotsContainer;
				}
			}

			// CFVSW binds preventDefault on .cfvsw-shop-variations at load; Slick clones miss it.
			const bindCfvswSliderPrevent = function() {
				$slider
					.off('click.cfvswSliderCompat', '.cfvsw-shop-variations')
					.on('click.cfvswSliderCompat', '.cfvsw-shop-variations', function(e) {
						e.preventDefault();
					});
			};

			const sliderCfvswFormsReady = function() {
				const $forms = $slider.find('.cfvsw_variations_form');

				if ( ! $forms.length ) {
					return false;
				}

				let ready = true;

				$forms.each( function() {
					const events = $._data( this, 'events' ) || {};

					if ( ! events.found_variation || ! events.found_variation.length ) {
						ready = false;
					}
				} );

				return ready;
			};

			const initSliderCfvswOnce = function() {
				if ( $slider.data( 'tmpcoderCfvswInitDone' ) ) {
					return;
				}

				const $forms = $slider.find( '.cfvsw_variations_form' );

				if ( ! $forms.length ) {
					return;
				}

				// Clones inherit variation-function-added without jQuery handlers.
				$forms.removeClass( 'variation-function-added' );
				document.dispatchEvent( new Event( 'cfvswVariationLoad' ) );

				if ( sliderCfvswFormsReady() ) {
					$slider.data( 'tmpcoderCfvswInitDone', true );
				}
			};

			const scheduleSliderCfvswFallback = function() {
				const retrySliderCfvswInit = function() {
					if ( $slider.data( 'tmpcoderCfvswInitDone' ) ) {
						return;
					}

					if ( ! $slider.hasClass( 'slick-initialized' ) ) {
						return;
					}

					initSliderCfvswOnce();
				};

				if ( document.readyState === 'complete' ) {
					retrySliderCfvswInit();
				} else {
					$( window ).one( 'load.tmpcoderPgcCfvsw', retrySliderCfvswInit );
				}
			};

			// Bind before Slick so delegated handler survives clone DOM rebuilds.
			bindCfvswSliderPrevent();

			// CFVSW once after Slick init — proven last DOM rebuild on page load.
			$slider.on( 'init.tmpcoderPgcCfvsw', function() {
				bindCfvswSliderPrevent();
				initSliderCfvswOnce();
				scheduleSliderCfvswFallback();
			});

			// Initialize Slick slider only if we have products
			if (totalProducts > 0) {
				$slider.slick(settings);

				// Fallback if init already fired before handler attached.
				if ($slider.hasClass('slick-initialized')) {
					bindCfvswSliderPrevent();
					initSliderCfvswOnce();
					scheduleSliderCfvswFallback();
				}

				// Wait for slider to be fully initialized
				$slider.on('init', function() {
					// console.log('Slider fully initialized');
					
					// Add class to show slider container with smooth transition
					setTimeout(function() {
						$sliderContainer.addClass('slider-ready');
					}, 100);
				});
				
				// If slider is already initialized (sometimes happens)
				if ($slider.hasClass('slick-initialized')) {
					setTimeout(function() {
						$sliderContainer.addClass('slider-ready');
					}, 100);
				}

				// console.log('Slider initialized with settings:', settings);

			} else {
				// console.log('No products found for slider');
				// Show container even if no products
				$sliderContainer.addClass('slider-ready');
			}
		}
	};

	// Enhanced productGrid function with slider support
	const enhancedProductGrid = function($scope, $) {
		// Call original function
		productGrid($scope, $);
			
		// console.log('originalProductGrid');

		// Initialize slider if layout is slider
		const $container = $scope.find('#tmpcoder-product-grid');
		if ($container.hasClass('slider')) {
			// Before window.load so CFVSW can init the final Slick DOM (originals + clones) once.
			setTimeout(function() {
				initProductSlider($scope, $);
			}, 100);
		}
	};

	if (tmpcoder.elementStatusCheck('tmpcoderProductGridLoad') && typeof window.forceFullyRun === "undefined") {
		return;
	}

	elementorFrontend.hooks.addAction("frontend/element_ready/eicon-woocommerce.default", enhancedProductGrid);
});