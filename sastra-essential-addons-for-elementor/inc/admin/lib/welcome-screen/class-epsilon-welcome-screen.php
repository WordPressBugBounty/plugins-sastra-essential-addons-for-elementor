<?php
/**
 * Epsilon Welcome Screen
 *
 * @package Epsilon Framework
 */

use TMPCODER\Classes\Pro_Modules;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class TMPCODER_Welcome_Screen
*/

class TMPCODER_Welcome_Screen {
	/**
	 * Theme name
	 *
	 * @var string
	 */
	public $theme_name = '';

	/**
	 * Theme slug
	 *
	 * @var string
	 */
	public $theme_slug = '';

	/**
	 * Author Logo
	 *
	 * @var string
	 */
	public $author_logo = '';

	/**
	 * Required actions
	 *
	 * @var array|mixed
	 */
	public $actions = array();

	/**
	 * Actions count
	 *
	 * @var int
	 */
	public $actions_count = 0;

	/**
	 * Required Plugins
	 *
	 * @var array|mixed
	 */
	public $plugins = array();

	/**
	 * Notice message
	 *
	 * @var mixed|string
	 */
	public $notice = '';

	/**
	 * Tab sections
	 *
	 * @var array
	 */
	public $sections = array();

	/**
	 * EDD Strings
	 *
	 * @var array
	 */
	public $strings = array();

	/**
	 * EDD load
	 *
	 * @var bool
	 */
	public $edd = false;

	/**
	 * If we have an EDD product, we need to add an ID
	 *
	 * @var string
	 */
	public $download_id = '';

	/**
	 * TMPCODER_Welcome_Screen constructor.
	 *
	 * @param array $config Configuration array.
	 */
	public function __construct( $config = array() ) {
		$theme = (is_object(wp_get_theme()->parent())) ? wp_get_theme()->parent() : wp_get_theme();
		$defaults = array(
			'theme-name'  => TMPCODER_PLUGIN_NAME,
			'theme-slug'  => TMPCODER_THEME,
			'author-logo' => get_template_directory_uri() . '/inc/admin/lib/welcome-screen/img/templatescoder.png',
			'actions'     => array(),
			'plugins'     => array(),
			'notice'      => '',
			'sections'    => array(),
			'edd'         => false,
			'download_id' => '',
		);

		$config = wp_parse_args( $config, $defaults );

		/**
		 * Configure our welcome screen
		 */
		$this->theme_name    = TMPCODER_PLUGIN_NAME;
		$this->theme_slug    = TMPCODER_THEME;
		$this->author_logo   = $config['author-logo'];
		$this->actions       = $config['actions'];
		$this->actions_count = $this->count_actions();
		$this->plugins       = $config['plugins'];
		$this->notice        = $config['notice'];
		$this->sections      = $config['sections'];
		$this->edd           = $config['edd'];
		$this->download_id   = $config['download_id'];

		if ( $this->edd ) {
			$this->strings = EDD_Theme_Helper::get_strings();
		}

		// if ( empty( $config['sections'] ) ) {
		// 	$this->sections = $this->set_default_sections( $config );
		// }

		/**
		 * Create the dashboard page
		 */
		add_action( 'admin_menu', array( $this, 'welcome_screen_menu' ) );

		/**
		 * Load the welcome screen styles and scripts
		 */
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

		add_action( 'admin_init', [$this, 'tmpcoder_register_addons_settings'] );
	}

	// Register Settings
	function tmpcoder_register_addons_settings() {

		function tmpcoder_sanitize_register_key( $input ) {
			return sanitize_text_field( $input );
		}

	    register_setting( 'tmpcoder-settings', 'tmpcoder_mailchimp_api_key', 'tmpcoder_sanitize_register_key' );

	    // WooCommerce
	    register_setting( 'tmpcoder-settings', 'tmpcoder_add_wishlist_to_my_account', 'tmpcoder_sanitize_register_key'  );
	    register_setting( 'tmpcoder-settings', 'tmpcoder_wishlist_page', 'tmpcoder_sanitize_register_key'  );
    	register_setting( 'tmpcoder-settings', 'tmpcoder_compare_page', 'tmpcoder_sanitize_register_key'  );
    	register_setting( 'tmpcoder-settings', 'tmpcoder_woo_shop_ppp', 'tmpcoder_sanitize_register_key'  );
	    register_setting( 'tmpcoder-settings', 'tmpcoder_woo_shop_cat_ppp', 'tmpcoder_sanitize_register_key'  );
	    register_setting( 'tmpcoder-settings', 'tmpcoder_woo_shop_tag_ppp', 'tmpcoder_sanitize_register_key'  );
    	
	    // Extensions
        register_setting('tmpcoder-elements-settings', 'tmpcoder-particles', 'tmpcoder_sanitize_register_key');
        register_setting('tmpcoder-elements-settings', 'tmpcoder-parallax-background', 'tmpcoder_sanitize_register_key');
        register_setting('tmpcoder-elements-settings', 'tmpcoder-parallax-multi-layer', 'tmpcoder_sanitize_register_key');
        register_setting('tmpcoder-elements-settings', 'tmpcoder-custom-css', 'tmpcoder_sanitize_register_key');
        register_setting('tmpcoder-elements-settings', 'tmpcoder-sticky-section', 'tmpcoder_sanitize_register_key');
        register_setting('tmpcoder-elements-settings', 'tmpcoder-floating-effects', 'tmpcoder_sanitize_register_key');
        register_setting('tmpcoder-elements-settings', 'tmpcoder-scroll-effects-pro', 'tmpcoder_sanitize_register_key');

        // Element Toggle
        if ( false == get_option( 'tmpcoder-element-toggle-all' ) ) {
			add_option( 'tmpcoder-element-toggle-all',"on" );
		}
		
	    register_setting( 'tmpcoder-elements-settings', 'tmpcoder-element-toggle-all', [ 'default' => 'on' ]  );

	    // Widgets
	    foreach ( tmpcoder_get_registered_modules() as $title => $data ) {
	        $slug = $data[0];
        	if ( false == get_option( 'tmpcoder-element-'.$slug ) ) {
				add_option( 'tmpcoder-element-'.$slug ,"on" );
			}
	        register_setting( 'tmpcoder-elements-settings', 'tmpcoder-element-'. $slug, [ 'default' => 'on' ] );
	    }

	    $theme_builder_modules = tmpcoder_get_theme_builder_modules();

	    $theme_builder_modules_pro = (tmpcoder_is_availble() && defined( 'TMPCODER_ADDONS_PRO_VERSION' )) ? Pro_Modules::tmpcoder_get_theme_builder_modules() : [];

        // Theme Builder
	    foreach ( array_merge($theme_builder_modules,$theme_builder_modules_pro) as $title => $data ) {
	        $slug = $data[0];
	        $slug = str_replace('-pro', '', $slug);
        	if ( false == get_option( 'tmpcoder-element-'.$slug ) ) {
				add_option( 'tmpcoder-element-'.$slug ,"on" );
			}
	        register_setting( 'tmpcoder-elements-settings', 'tmpcoder-element-'. $slug, [ 'default' => 'on' ] );
	    }

	    $woo_modules = tmpcoder_get_woocommerce_builder_modules();
	    $woo_modules_pro = (tmpcoder_is_availble() && defined( 'TMPCODER_ADDONS_PRO_VERSION' )) ? Pro_Modules::tmpcoder_get_woocommerce_builder_modules() : [];

	    // WooCommerce Builder
	    foreach ( array_merge($woo_modules, $woo_modules_pro) as $title => $data ) {
	        $slug = is_array($data) ? $data[0] : $data;
	        $slug = str_replace('-pro', '', $slug);
			if ( false == get_option( 'tmpcoder-element-'.$slug ) ) {
				add_option( 'tmpcoder-element-'.$slug ,"on" );
			}
	        register_setting( 'tmpcoder-elements-settings', 'tmpcoder-element-'. $slug, [ 'default' => 'on' ] );
	    }
	}
	
	/**
	 * Instance constructor
	 *
	 * @param array $config Configuration array.
	 *
	 * @returns object
	 */
	public static function get_instance( $config = array() ) {
		static $inst;

		if ( ! $inst ) {
			$inst = new TMPCODER_Welcome_Screen( $config );
		}

		return $inst;
	}
    
	/**
	 * Load welcome screen css and javascript
	 */
	public function enqueue() {
		if ( is_admin() ) {
			wp_enqueue_style(
				'welcome-screen',
				TMPCODER_PLUGIN_URI . 'inc/admin/lib/welcome-screen/css/welcome'.tmpcoder_script_suffix().'.css', 
                array(),  
                tmpcoder_get_plugin_version()
			);

			wp_enqueue_script(
				'welcome-screen',
				TMPCODER_PLUGIN_URI . 'inc/admin/lib/welcome-screen/js/welcome'.tmpcoder_script_suffix().'.js',
				array(
					'jquery-ui-slider',
				),
				tmpcoder_get_plugin_version()
			);

            wp_localize_script(
				'welcome-screen',
				'welcomeScreen',
				array(
					'nr_actions_required'      => absint( $this->count_actions() ),
					'template_directory'       => esc_url( get_template_directory_uri() ),
					'no_required_actions_text' => esc_html__( 'Hooray! There are no required actions for you right now.', 'sastra-essential-addons-for-elementor' ),
					'ajax_nonce'               => wp_create_nonce( 'welcome_nonce' ),
					'ajax_url'                 => admin_url('admin-ajax.php'),
					'activating_string'        => esc_html__( 'Activating', 'sastra-essential-addons-for-elementor' ),
					'body_class'               => 'appearance_page_' . $this->theme_slug . '-welcome',
					'no_actions'               => esc_html__( 'Hooray! There are no required actions for you right now.', 'sastra-essential-addons-for-elementor' ),
                    'global_options_link' => esc_url('admin.php?page=spexo_addons_global_settings'),
                    'widget_settings_link' => esc_url('admin.php?page='.TMPCODER_THEME.'-welcome&tab=widgets'),
                    'global_settings_link' => esc_url('admin.php?page='.TMPCODER_THEME.'-welcome&tab=settings'),
				)
			);
		}
	}

	/**
	 * Return the actions left
	 *
	 * @return array|mixed
	 */
	private function get_actions_left() {
		if ( ! empty( $this->actions ) ) {
			$actions_left = get_option( $this->theme_slug . '_actions_left', array() );
			return $actions_left;
		}

		return array();
	}

	/**
	 * Returns the plugins left to be installed
	 *
	 * @return array|mixed
	 */
	private function get_plugins_left() {
		if ( ! empty( $this->plugins ) ) {
			$plugins_left = get_option( $this->theme_slug . '_plugins_left', array() );
			if ( empty( $plugins_left ) ) {
				foreach ( $this->plugins as $plugin => $prop ) {
					$plugins_left[ $plugin ] = true;
				}

				return $plugins_left;
			}

			return $plugins_left;
		}

		return array();
	}


	/**
	 * Registers the welcome screen menu
	 */
	public function welcome_screen_menu() {
		
		$title = sprintf(
            /* Translators: 1: Menu Title */
             esc_html__( 'About %1$s', 'sastra-essential-addons-for-elementor' ), esc_html( 'Spexo Addons for Elementor' ) );

		if ( 0 < $this->actions_count ) {
			$title .= '<span class="badge-action-count">' . esc_html( $this->actions_count ) . '</span>';
		}
		
		if (did_action( 'elementor/loaded' )) {
			add_menu_page('Spexo Addons', 'Spexo Addons', 'manage_options', 'spexo-welcome',[$this,'render_welcome_screen'],TMPCODER_ADDONS_ASSETS_URL.'images/logo-icon.svg', 30 );
		}
	}

	/**
	 * Render the welcome screen
	 */
	public function render_welcome_screen() {
		require_once( ABSPATH . 'wp-load.php' );
		require_once( ABSPATH . 'wp-admin/admin.php' );
		require_once( ABSPATH . 'wp-admin/admin-header.php' );

		$theme = (is_object(wp_get_theme()->parent())) ? wp_get_theme()->parent() : wp_get_theme();
        $tab   = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'getting-started';// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		
		if( defined('TMPCODER_PRO_ADDONS_ASSETS_URL') ) {
			$headers = get_headers(TMPCODER_PRO_ADDONS_ASSETS_URL . 'images/spexo-logo-web-pro.svg');
			$main_header_logo = ($headers && strpos($headers[0], '200')) ? TMPCODER_PRO_ADDONS_ASSETS_URL.'images/spexo-logo-web-pro.svg' : TMPCODER_ADDONS_ASSETS_URL.'images/spexo-logo-web.svg' ;

	    } else {
	        $main_header_logo = TMPCODER_ADDONS_ASSETS_URL.'images/spexo-logo-web.svg';
	    }

		if ( function_exists( 'tmpcoder_render_admin_header' ) ) {
			tmpcoder_render_admin_header( $main_header_logo, $tab );
		}
	}

	/**
	 * Count the number of actions left
	 *
	 * @return int
	 */
	private function count_actions() {
		$actions_left = get_option( $this->theme_slug . '_actions_left', array() );

		$i = 0;
		foreach ( $this->actions as $action ) {
			$true = false;

			if ( ! $action['check'] ) {
				$true = true;
			}

			if ( ! empty( $actions_left ) && isset( $actions_left[ $action['id'] ] ) && ! $actions_left[ $action['id'] ] ) {
				$true = false;
			}

			if ( $true ) {
				$i ++;
			}
		}

		return $i;
	}
}