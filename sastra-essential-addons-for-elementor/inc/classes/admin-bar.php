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
			if (tmpcoder_is_elementor_editor()) {
				\Elementor\Plugin::$instance->files_manager->clear_cache();
		 	} 
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

		.tmpcoder-admin-bar-image {
			width: 10px !important;
		    height: 10px !important;
		    display: inline-block;
		    margin-right: 5px !important;
		    // filter: brightness(0);
    		// opacity: 0.6;
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
			TMPCODER_PLUGIN_URI . 'assets/js/admin/admin'.tmpcoder_script_suffix().'.js',
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
		$websites_icon = sprintf( '<img class="tmpcoder-admin-bar-image" src="%s">', TMPCODER_ADDONS_ASSETS_URL .'images/prebuilt-websites-admin-bar.svg' );
		$blocks_icon = sprintf( '<img class="tmpcoder-admin-bar-image" src="%s">', TMPCODER_ADDONS_ASSETS_URL .'images/prebuilt-block-admin-bar.svg' ); 
		$builder_icon = sprintf( '<img class="tmpcoder-admin-bar-image" src="%s">', TMPCODER_ADDONS_ASSETS_URL .'images/site-builder-admin-bar.svg' ); 

		$admin_bar->add_menu( [
			'id'    => 'spexo-addons',
			'title' => sprintf( '<img src="%s"> Spexo Addons', TMPCODER_ADDONS_ASSETS_URL .'images/logo-40x40.svg' ),
			'href'  => tmpcoder_get_dashboard_link(),
			'meta'  => [
				'title' => __( 'Spexo Addons', 'sastra-essential-addons-for-elementor' ),
			]
		] );

		$admin_bar->add_menu( [
			'id'     => 'tmpcoder-clear-all-cache',
			'parent' => 'spexo-addons',
			'title'  => $icon . __( 'Regenerate Cache', 'sastra-essential-addons-for-elementor' ),
			'href'   => '#',
			'meta'   => [
				'class' => 'tmpcoderjs-clear-cache tmpcoder-clear-all-cache',
			]
		] );

		$admin_bar->add_menu( [
			'id'     => 'tmpcoder-prebuilt-websites',
			'parent' => 'spexo-addons',
			'title'  => $websites_icon . __( 'Prebuilt websites', 'sastra-essential-addons-for-elementor' ),
			'href'   => admin_url('admin.php?page=tmpcoder-import-demo'),
			'meta'   => [
				'class' => '',
			]
		] );

		$admin_bar->add_menu( [
			'id'     => 'tmpcoder-prebuilt-blocks',
			'parent' => 'spexo-addons',
			'title'  => $blocks_icon . __( 'Prebuilt Blocks', 'sastra-essential-addons-for-elementor' ),
			'href'   => admin_url('admin.php?page=spexo-welcome&tab=prebuilt-blocks'),
			'meta'   => [
				'class' => '',
			]
		] );

		$admin_bar->add_menu( [
			'id'     => 'tmpcoder-site-builder',
			'parent' => 'spexo-addons',
			'title'  => $builder_icon . __( 'Site Builder', 'sastra-essential-addons-for-elementor' ),
			'href'   => admin_url('admin.php?page=spexo-welcome&tab=site-builder'),
			'meta'   => [
				'class' => '',
			]
		] );
	}
}

TMPCODER_Admin_Bar::init();