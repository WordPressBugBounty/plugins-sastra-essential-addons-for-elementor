<?php
namespace Spexo_Addons\Elementor;

defined( 'ABSPATH' ) || die();

class TMPCODER_Admin_Bar {

	public static function init() {
		add_action( 'admin_bar_menu', [__CLASS__, 'add_toolbar_items'], 500 );
		add_action( 'wp_enqueue_scripts', [__CLASS__, 'enqueue_assets'] );
		add_action( 'admin_enqueue_scripts', [__CLASS__, 'enqueue_assets'] );
		add_action( 'wp_ajax_tmpcoder_clear_cache', [__CLASS__, 'clear_cache' ] );
	}

	public static function clear_cache() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! check_ajax_referer( 'tmpcoder_clear_cache', 'nonce' ) ) {
			wp_send_json_error();
		}

		$type = '';
		if (isset($_POST['type'])) {
			$type = sanitize_text_field(wp_unslash($_POST['type']));	
		}

		$post_id = isset( $_POST['post_id'] ) ? absint($_POST['post_id']) : 0;
		$assets_cache = new Assets_Cache( $post_id );
		if ( $type === 'page' ) {
			$assets_cache->delete();
		} elseif ( $type === 'all' ) {
			$assets_cache->delete_all();
		}
		wp_send_json_success();
	}

	public static function enqueue_assets() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$custom_css = '#wp-admin-bar-spexo-addons > .ab-item > img {
		    margin-top: -4px;
		    width: 18px;
		    height: 18px;
		    vertical-align: text-bottom;
		    display: inline-block;
		}

		#wp-admin-bar-spexo-addons .ab-item .dashicons {
		    position: relative;
		    top: 7px;
		    display: inline-block;
		    font-weight: normal;
		    font-style: normal;
		    font-variant: normal;
		    font-size: inherit;
		    font-family: dashicons;
		    line-height: 1;

		    -webkit-font-smoothing: antialiased;
		    -moz-osx-font-smoothing: grayscale;
		    text-rendering: auto;
		}

		#wp-admin-bar-spexo-addons .ab-item .dashicons-update-alt:before {
		    content: "\f113";
		}

		#wp-admin-bar-spexo-addons .tmpcoder-clear-cache--done .ab-item > i {
		    color: #46b450;
		}

		#wp-admin-bar-spexo-addons .tmpcoder-clear-cache--init .ab-item > i {
		    -webkit-animation: tmpcoder-inifinite-rotate .5s infinite linear;
		            animation: tmpcoder-inifinite-rotate .5s infinite linear;
		}

		@-webkit-keyframes tmpcoder-inifinite-rotate {
		    from {
		        -webkit-transform: rotate(0deg);
		                transform: rotate(0deg);
		    }
		    to {
		        -webkit-transform: rotate(359deg);
		                transform: rotate(359deg);
		    }
		}

		@keyframes tmpcoder-inifinite-rotate {
		    from {
		        -webkit-transform: rotate(0deg);
		                transform: rotate(0deg);
		    }
		    to {
		        -webkit-transform: rotate(359deg);
		                transform: rotate(359deg);
		    }
		}';

		wp_register_style( 'tmpcoder-admin-bar-cach', false );
		wp_enqueue_style( 'tmpcoder-admin-bar-cach' );
		wp_add_inline_style( 'tmpcoder-admin-bar-cach', $custom_css );

		wp_enqueue_script(
			'spexo-elementor-addons-admin',
			TMPCODER_PLUGIN_URI . 'assets/js/admin/admin.min.js',
			['jquery'],
			TMPCODER_PLUGIN_VER,
			true
		);
		
		wp_localize_script(
			'spexo-elementor-addons-admin',
			'SpexoAdmin',
			[
				'nonce'    => wp_create_nonce( 'tmpcoder_clear_cache' ),
				'post_id'  => get_queried_object_id(),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			]
		);
	}

	public static function add_toolbar_items( \WP_Admin_Bar $admin_bar ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$icon = '<i class="dashicons dashicons-update-alt"></i> ';

		$admin_bar->add_menu( [
			'id'    => 'spexo-addons',
			'title' => sprintf( '<img src="%s">', TMPCODER_ADDONS_ASSETS_URL .'images/logo-40x40.svg' ),
			'href'  => tmpcoder_get_dashboard_link(),
			'meta'  => [
				'title' => __( 'SpexoAddons', 'sastra-essential-addons-for-elementor' ),
			]
		] );

		if ( is_singular() ) {
			$admin_bar->add_menu( [
				'id'     => 'tmpcoder-clear-page-cache',
				'parent' => 'spexo-addons',
				'title'  => $icon . __( 'Page: Renew On Demand Assets', 'sastra-essential-addons-for-elementor' ),
				'href'   => '#',
				'meta'   => [
					'class' => 'tmpcoderjs-clear-cache tmpcoder-clear-page-cache',
				]
			] );
		}

		$admin_bar->add_menu( [
			'id'     => 'tmpcoder-clear-all-cache',
			'parent' => 'spexo-addons',
			'title'  => $icon . __( 'Global: Renew On Demand Assets', 'sastra-essential-addons-for-elementor' ),
			'href'   => '#',
			'meta'   => [
				'class' => 'tmpcoderjs-clear-cache tmpcoder-clear-all-cache',
			]
		] );
	}
}

TMPCODER_Admin_Bar::init();