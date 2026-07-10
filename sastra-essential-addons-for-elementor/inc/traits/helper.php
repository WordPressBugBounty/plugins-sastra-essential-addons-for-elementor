<?php

namespace Spexo_Addons_Elementor\Traits;

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

use Elementor\Plugin;
use \Spexo_Addons_Elementor\Classes\Helper as HelperClass;

trait Helper
{
    use Template_Query;

    public function tmpcoder_load_more_button_style() {
        return do_action( 'tmpcoder/controls/load_more_button_style', $this );
    }

    public function tmpcoder_read_more_button_style() {
        return do_action( 'tmpcoder/controls/read_more_button_style', $this );
    }

    public function tmpcoder_controls_custom_positioning( $_1, $_2, $_3, $_4 ) {
        return do_action( 'tmpcoder/controls/custom_positioning', $this, $_1, $_2, $_3, $_4 );
    }

    public function tmpcoder_get_all_types_post() {
        return HelperClass::get_post_types();
    }

	/**
	 * It returns the widget settings provided the page id and widget id
	 * @param int $page_id Page ID where the widget is used
	 * @param string $widget_id the id of the widget whose settings we want to fetch
	 *
	 * @return array
	 */
	public function tmpcoder_get_widget_settings( $page_id, $widget_id ) {
		$document = Plugin::$instance->documents->get( $page_id );
		$settings = [];
		if ( $document ) {
			$elements    = Plugin::instance()->documents->get( $page_id )->get_elements_data();
			// $widget_data = $this->find_element_recursive( $elements, $widget_id );
            $widget_data = HelperClass::find_element_recursive( $elements, $widget_id );
            if(!empty($widget_data)) {
                $widget      = Plugin::instance()->elements_manager->create_element_instance( $widget_data );
                if ( $widget ) {
                    $settings    = $widget->get_settings_for_display();
                }
            }
		}
		return $settings;
	}
	
    public function print_load_more_button($settings, $args, $plugin_type = 'free')
    {
        //@TODO; not all widget's settings contain posts_per_page name exactly, so adjust the settings before passing here or run a migration and make all settings key generalize for load more feature.
        if (!isset($this->page_id)) {
            if ( Plugin::$instance->documents->get_current() ) {
                $this->page_id = Plugin::$instance->documents->get_current()->get_main_id();
            }else{
                $this->page_id = null;
            }
        }

	    $max_page = empty( $args['max_page'] ) ? false : $args['max_page'];
	    unset( $args['max_page'] );

        if ( isset( $args['found_posts'] ) && $args['found_posts'] <= $args['posts_per_page'] ){
	        $this->add_render_attribute( 'load-more', [ 'class' => 'hide-load-more' ] );
	        unset( $args['found_posts'] );
        }

	    $this->add_render_attribute( 'load-more', [
		    'class'          => "tmpcoder-load-more-button",
		    'id'             => "tmpcoder-load-more-btn-" . $this->get_id(),
		    'data-widget-id' => $this->get_id(),
		    'data-widget'    => $this->get_id(),
		    'data-page-id'   => $this->page_id,
		    'data-template'  => json_encode( [
			    'dir'       => $plugin_type,
			    'file_name' => $settings['loadable_file_name'],
			    'name'      => $this->process_directory_name()
		    ],
			    1 ),
		    'data-class'     => get_class( $this ),
		    'data-layout'    => isset( $settings['layout_mode'] ) ? $settings['layout_mode'] : "",
		    'data-page'      => 1,
		    'data-args'      => http_build_query( $args ),
	    ]);

	    if ( $max_page ) {
		    $this->add_render_attribute( 'load-more', [ 'data-max-page' => $max_page ] );
	    }

        if ( $args['posts_per_page'] != '-1' ) {
            $this->add_render_attribute( 'load-more-wrap', 'class', 'tmpcoder-load-more-button-wrap' );
        
            if ( "tmpcoder-dynamic-filterable-gallery" == $this->get_name() ){
                $this->add_render_attribute( 'load-more-wrap', 'class', 'dynamic-filter-gallery-loadmore' );
            }
            
            if ( 'infinity' === $settings['show_load_more'] ) {
                $this->add_render_attribute( 'load-more-wrap', 'class', 'tmpcoder-infinity-scroll' );
                $this->add_render_attribute( 'load-more-wrap', 'data-offset', esc_attr( $settings['load_more_infinityscroll_offset'] ) );
            } else if ( ! ( 'true' == $settings['show_load_more'] || 1 == $settings['show_load_more'] || 'yes' == $settings['show_load_more'] ) ){
                $this->add_render_attribute( 'load-more-wrap', 'class', 'tmpcoder-force-hide' );
            }

            do_action( 'tmpcoder/global/before-load-more-button', $settings, $args, $plugin_type );
            ?>
            <div <?php $this->print_render_attribute_string( 'load-more-wrap' ); ?>>
                <button <?php $this->print_render_attribute_string( 'load-more' ); ?>>
                    <span class="tmpcoder-btn-loader button__loader"></span>
                    <span class="tmpcoder_load_more_text"><?php echo esc_html($settings['show_load_more_text']) ?></span>
                </button>
            </div>
            <?php 
            do_action( 'tmpcoder/global/after-load-more-button', $settings, $args, $plugin_type );
        }
    }

    public function tmpcoder_product_grid_script(){
		if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {
			if ( current_theme_supports( 'wc-product-gallery-zoom' ) ) {
				wp_enqueue_script( 'zoom' );
			}
			if ( current_theme_supports( 'wc-product-gallery-slider' ) ) {
				wp_enqueue_script( 'flexslider' );
			}
			if ( current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
				wp_enqueue_script( 'photoswipe-ui-default' );
				wp_enqueue_style( 'photoswipe-default-skin' );
				if ( has_action( 'wp_footer', 'woocommerce_photoswipe' ) === false ) {
					add_action( 'wp_footer', 'woocommerce_photoswipe', 15 );
				}
			}
            wp_enqueue_script( 'wc-add-to-cart-variation' );
			wp_enqueue_script( 'wc-single-product' );
		}
	}

	/**
	* Rating Markup
	*/
	public function tmpcoder_rating_markup( $html, $rating, $count ) {

		if ( 0 == $rating ) {
			$html  = '<div class="tmpcoder-star-rating star-rating">';
			$html .= wc_get_star_rating_html( $rating, $count );
			$html .= '</div>';
		}
		return $html;
	}

	public function tmpcoder_product_wrapper_class( $classes, $product_id, $widget_name ) {

		if ( ! is_plugin_active( 'woo-variation-swatches-pro/woo-variation-swatches-pro.php' ) ) {
			return $classes;
		}

		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return $classes;
		}

		if ( $product->is_type( 'variable' ) ) {
			$classes[] = 'wvs-archive-product-wrapper';
		}

		return $classes;
	}

	public function tmpcoder_woo_cart_empty_action() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		if ( isset( $_GET['empty_cart'] ) && 'yes' === esc_html( $_GET['empty_cart'] ) ) {
			WC()->cart->empty_cart();
		}
	}

	/**
	 * Add data-atc-popup attribute to loop add-to-cart links when enabled.
	 *
	 * @param array $settings Widget settings.
	 * @return void
	 */
	public function maybe_apply_added_to_cart_action_filter( $settings ) {
		if ( empty( $settings['added_to_cart_action'] ) || 'sidebar' !== $settings['added_to_cart_action'] ) {
			return;
		}

		add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'filter_add_to_cart_link_atc_popup' ], 10, 2 );
	}

	/**
	 * Inject mini-cart action attribute on add-to-cart button markup.
	 *
	 * @param string     $link    Add to cart link HTML.
	 * @param \WC_Product $product Product object.
	 * @return string
	 */
	public function filter_add_to_cart_link_atc_popup( $link, $product ) {
		if ( false !== strpos( $link, 'data-atc-popup' ) ) {
			return $link;
		}

		return preg_replace( '/(\sclass=")/', ' data-atc-popup="sidebar"$1', $link, 1 );
	}

	/**
	 * Product Grid Classic: disable out-of-stock loop add-to-cart actions.
	 *
	 * @param array $settings Widget settings.
	 * @return void
	 */
	public function maybe_apply_out_of_stock_atc_filter( $settings ) {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		HelperClass::$tmpcoder_pgc_oos_atc_context = [
			'show_custom'  => boolval( $settings['show_add_to_cart_custom_text'] ?? false ),
			'default_text' => isset( $settings['add_to_cart_default_product_button_text'] ) ? $settings['add_to_cart_default_product_button_text'] : '',
		];

		add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'filter_out_of_stock_loop_add_to_cart_link' ], 25, 2 );
	}

	/**
	 * Remove out-of-stock loop add-to-cart filter after Product Grid Classic render.
	 *
	 * @return void
	 */
	public function maybe_remove_out_of_stock_atc_filter() {
		remove_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'filter_out_of_stock_loop_add_to_cart_link' ], 25 );
		HelperClass::$tmpcoder_pgc_oos_atc_context = null;
	}

	/**
	 * Mark out-of-stock loop add-to-cart links as disabled for Product Grid Classic.
	 *
	 * @param string     $link    Add to cart link HTML.
	 * @param \WC_Product $product Product object.
	 * @return string
	 */
	public function filter_out_of_stock_loop_add_to_cart_link( $link, $product ) {
		if ( ! $product instanceof \WC_Product || $product->is_in_stock() ) {
			return $link;
		}

		if ( ! $this->tmpcoder_is_pgc_oos_loop_atc_action( $link, $product ) ) {
			return $link;
		}

		if ( false !== strpos( $link, 'tmpcoder-oos-atc-disabled' ) ) {
			return $link;
		}

		if ( false !== strpos( $link, 'class="' ) ) {
			$link = preg_replace( '/class="([^"]*)"/', 'class="$1 tmpcoder-oos-atc-disabled"', $link, 1 );
		}

		$link = preg_replace( '/<a\s/', '<a aria-disabled="true" tabindex="-1" ', $link, 1 );
		$link = preg_replace( '/href="[^"]*"/', 'href="#"', $link, 1 );

		return $link;
	}

	/**
	 * Whether a loop button is an add-to-cart action (not a product details link).
	 *
	 * @param string      $link    Add to cart link HTML.
	 * @param \WC_Product $product Product object.
	 * @return bool
	 */
	private function tmpcoder_is_pgc_oos_loop_atc_action( $link, $product ) {
		if ( false !== strpos( $link, 'product_type_grouped' )
			|| false !== strpos( $link, 'product_type_external' ) ) {
			return false;
		}

		if ( false !== strpos( $link, 'add_to_cart_button' )
			|| false !== strpos( $link, 'ajax_add_to_cart' ) ) {
			return true;
		}

		if ( false !== strpos( $link, 'cfvsw_ajax_add_to_cart' )
			&& ( false !== strpos( $link, 'cfvsw_variation_found' ) || false !== strpos( $link, 'add_to_cart_button' ) ) ) {
			return true;
		}

		if ( false !== strpos( $link, 'product_type_variable' ) ) {
			return false;
		}

		if ( 'simple' === $product->get_type()
			&& ! $product->is_purchasable()
			&& $this->tmpcoder_pgc_oos_simple_uses_custom_atc_label() ) {
			return true;
		}

		return false;
	}

	/**
	 * Whether out-of-stock simple products use a custom non-view label.
	 *
	 * @return bool
	 */
	private function tmpcoder_pgc_oos_simple_uses_custom_atc_label() {
		$context = HelperClass::$tmpcoder_pgc_oos_atc_context;

		if ( empty( $context['show_custom'] ) ) {
			return false;
		}

		$view_labels = [
			__( 'Read more', 'sastra-essential-addons-for-elementor' ),
			esc_html__( 'Read More', 'sastra-essential-addons-for-elementor' ),
			esc_html__( 'View More', 'sastra-essential-addons-for-elementor' ),
		];

		return ! in_array( $context['default_text'], $view_labels, true );
	}

	public function change_add_woo_checkout_update_order_reviewto_cart_text( $add_to_cart_text ) {
		add_filter( 'woocommerce_product_add_to_cart_text', function ( $default ) use ( $add_to_cart_text ) {
			global $product;
			switch ( $product->get_type() ) {
				case 'external':
					return $add_to_cart_text[ 'add_to_cart_external_product_button_text' ];
					break;
				case 'grouped':
					return $add_to_cart_text[ 'add_to_cart_grouped_product_button_text' ];
					break;
				case 'simple':
					return $add_to_cart_text[ 'add_to_cart_simple_product_button_text' ];
					break;
				case 'variable':
					return $add_to_cart_text[ 'add_to_cart_variable_product_button_text' ];
					break;
				default:
					return $default;
			}
		} );
	}

    /**
	 * return file path which are store in theme Template directory
	 * @param $file
	 */
	public function retrive_theme_path() {
		$current_theme = wp_get_theme();
		return sprintf(
			'%s/%s',
			$current_theme->theme_root,
			$current_theme->stylesheet
		);
	}

	/**
	 * tmpcoder_wpml_template_translation
	 * @param $id
	 * @return mixed|void
	 */
    public function tmpcoder_wpml_template_translation($id){
	    $postType = get_post_type( $id );
	    if ( 'elementor_library' === $postType ) {
		    return apply_filters( 'wpml_object_id', $id, $postType, true );
	    }
	    return $id;
    }

	/**
	 * tmpcoder_sanitize_template_param
     * Removes special characters that are illegal in filenames
     *
	 * @param array $template_info
	 *
     * @access public
	 * @return array
     * @since 5.0.4
	 */
    public function tmpcoder_sanitize_template_param( $template_info ){
	    $template_info = array_map( 'sanitize_text_field', $template_info );
	    return array_map( 'sanitize_file_name', $template_info );
    }

	/**
	 * sanitize_taxonomy_data
     * Sanitize all value for tax query
     *
	 * @param array $tax_list taxonomy param list
	 *
     * @access protected
	 * @return array|array[]|string[]
	 * @since 5.0.4
	 */
    public function sanitize_taxonomy_data( $tax_list ){
	    return array_map( function ( $param ) {
		    return is_array( $param ) ? array_map( 'sanitize_text_field', $param ) : sanitize_text_field( $param );
	    }, $tax_list );
    }
}
