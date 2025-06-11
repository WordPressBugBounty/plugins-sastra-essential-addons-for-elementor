<?php

namespace TMPCODER;

/**
 * Prevent loading this file directly
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists('TemplatesWidgetRegister') ){
    
    class TemplatesWidgetRegister {

        private static $_instance = null;

        /**
         * @var \ReflectionClass
         */

        private $reflection;

        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function tmpcoder_register_widgets(){

            $this->reflection = new \ReflectionClass( $this );

            $widget_manager = \Elementor\Plugin::instance()->widgets_manager;

            $tmpcoder_get_all_widgtes = tmpcoder_get_all_widgtes();

            if (is_array($tmpcoder_get_all_widgtes) && !empty($tmpcoder_get_all_widgtes)) {
                foreach ($tmpcoder_get_all_widgtes as $key => $widget) {

                    $slug = 'tmpcoder-element-'.$widget[0];
                    $item_status = get_option($slug,true);

                    if (!empty($item_status) && $item_status == 'on') {

                        if (isset($widget[5]) && !empty($widget[5])) {

                            if (isset($widget[4]) && !empty($widget[4])) {

                                $filename = TMPCODER_PLUGIN_DIR.'inc/widgets/'.$widget[4];

                                if (file_exists($filename)) {
                                    require $filename;                           
                                    $class_name = $this->reflection->getNamespaceName().'\Widgets\\'. $widget[5];
                                    $widget_manager->register( new $class_name() );
                                }
                            }
                        }
                    }
                }
            }
        }

        public function __construct(){
          
            add_action('elementor/widgets/register', [$this, 'tmpcoder_register_widgets'], 99);

            add_action( 'wp_enqueue_scripts', [$this, 'tmpcoder_enqueue_scripts'], 998 );

            add_action( 'elementor/controls/controls_registered', [ $this, 'register_custom_control' ] );

            // Editor CSS
            add_action( 'elementor/preview/enqueue_styles', [ $this, 'tmpcoder_enqueue_editor_styles' ], 998 );

            add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'tmpcoder_enqueue_inner_panel_scripts' ], 988 );

            // Editor CSS/JS
            add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_panel_styles' ], 988 );

            add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'tmpcoder_enqueue_panel_scripts' ], 988 );

            // Register Scripts
            add_action( 'elementor/frontend/before_register_scripts', [ $this, 'tmpcoder_register_scripts' ], 998 );

            // Frontend JS
            add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'tmpcoder_enqueue_frontend_scripts' ], 998 );

            // Promote Premium Widgets
            if ( current_user_can('administrator') ) {
              add_filter('elementor/editor/localize_settings', [$this, 'tmpcoder_promote_premium_widgets'], 100);
            }

            // Enqueue Scripts
            add_action( 'admin_enqueue_scripts', [ $this, 'tmpcoder_templates_library_scripts' ] );

            add_action('elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ],0);
        }

        public function add_elementor_widget_categories( $elements_manager ) {

            // Get all existing categories (reflection trick)

            $reflection = new \ReflectionClass($elements_manager);
            $property = $reflection->getProperty('categories');
            $property->setAccessible(true);
            $categories = $property->getValue($elements_manager);

            // Backup and remove our custom categories if already added
            unset(
                $categories['tmpcoder-widgets-category'],
                $categories['tmpcoder-theme-builder-widgets'],
                $categories['tmpcoder-woocommerce-builder-widgets'],
                $categories['tmpcoder-header-builder-widgets'],
                $categories['tmpcoder-premium-widgets']
            );

            // Apply new order
            $ordered_custom_categories = [
                'tmpcoder-widgets-category' => [
                    'title' => sprintf(wp_kses_post('%s Widgets <a class="tmpcoder-editor-upgrade-pro-icon" href="'.TMPCODER_PURCHASE_PRO_URL.'" target="_blank"><i class="eicon-upgrade-crown"></i>Upgrade</a>'), 'Spexo Addons'),
                    'icon' => 'fa fa-star',
                ],
                'tmpcoder-theme-builder-widgets' => [
                    'title' => sprintf(wp_kses_post('%s Theme Builder <a class="tmpcoder-editor-upgrade-pro-icon" href="'.TMPCODER_PURCHASE_PRO_URL.'" target="_blank"><i class="eicon-upgrade-crown"></i>Upgrade</a>'), 'Spexo Addons'),
                    'icon' => 'fa fa-star',
                ],
                'tmpcoder-woocommerce-builder-widgets' => [
                    'title' => sprintf(wp_kses_post('%s Woo Builder <a class="tmpcoder-editor-upgrade-pro-icon" href="'.TMPCODER_PURCHASE_PRO_URL.'" target="_blank"><i class="eicon-upgrade-crown"></i>Upgrade</a>'), 'Spexo Addons'),
                    'icon' => 'fa fa-star',
                ],
                'tmpcoder-header-builder-widgets' => [
                    'title' => sprintf(wp_kses_post('%s Header Builder <a class="tmpcoder-editor-upgrade-pro-icon" href="'.TMPCODER_PURCHASE_PRO_URL.'" target="_blank"><i class="eicon-upgrade-crown"></i>Upgrade</a>'), 'Spexo Addons'),
                    'icon' => 'fa fa-star',
                ],
                'tmpcoder-premium-widgets' => [
                    'title' => sprintf(wp_kses_post('%s Premium Widgets <a class="tmpcoder-editor-upgrade-pro-icon" href="'.TMPCODER_PURCHASE_PRO_URL.'" target="_blank"><i class="eicon-upgrade-crown"></i>Upgrade</a>'), 'Spexo Addons'),
                    'icon' => 'fa fa-star',
                ],
            ];

            // Add custom categories in desired order
            foreach ( $ordered_custom_categories as $key => $args ) {
                $elements_manager->add_category( $key, $args );
            }

            // Re-set the original categories (fallback if needed)
            $property->setValue($elements_manager, array_merge($ordered_custom_categories, $categories));
        }


        /**
        ** Enqueue Scripts and Styles
        */

        public function tmpcoder_templates_library_scripts( $hook ) {

            $screen = get_current_screen();

            if ( strpos($hook, TMPCODER_THEME.'-premade-blocks') || strpos($hook, 'spexo-welcome') ) {
                wp_enqueue_style( 'tmpcoder-premade-blocks-css', TMPCODER_PLUGIN_URI .'assets/css/admin/premade-blocks'.tmpcoder_script_suffix().'.css', [], tmpcoder_get_plugin_version() );
                wp_enqueue_script( 'tmpcoder-macy-js', TMPCODER_PLUGIN_URI .'assets/js/lib/macy/macy'.tmpcoder_script_suffix().'.js', ['jquery'], tmpcoder_get_plugin_version(), false );
                wp_enqueue_script( 'tmpcoder-premade-blocks-js', TMPCODER_PLUGIN_URI .'assets/js/admin/premade-blocks'.tmpcoder_script_suffix().'.js', ['jquery'], tmpcoder_get_plugin_version(), false );
            }

            wp_localize_script(
                'tmpcoder-premade-blocks-js',
                'TmpcoderLibFrontJs',
                [
                    'TMPCODER_PURCHASE_PRO_URL' => TMPCODER_PURCHASE_PRO_URL,
                    'logo_url' => TMPCODER_ADDONS_ASSETS_URL .'images/logo-40x40.svg',
                    'demos_url' => TMPCODER_DEMO_IMPORT_API,
                    'nonce' => wp_create_nonce('tmpcoder-library-frontend-js')
                ]
            );
              
              if ( strpos($hook, 'sastra-theme-builder') || strpos($hook, 'spexo-welcome') ) {
                         
                // enqueue CSS
                wp_enqueue_style( 'tmpcoder-plugin-options-css', TMPCODER_PLUGIN_URI .'assets/css/admin/plugin-options'.tmpcoder_script_suffix().'.css', [], tmpcoder_get_plugin_version() );

                // enqueue JS
                wp_enqueue_script( 'tmpcoder-plugin-options-js', TMPCODER_PLUGIN_URI .'assets/js/admin/plugin-options'.tmpcoder_script_suffix().'.js', ['jquery'], tmpcoder_get_plugin_version(), false );

                wp_localize_script(
                      'tmpcoder-plugin-options-js',
                      'TmpcoderPluginOptions', // This is used in the js file to group all of your scripts together
                      [
                            'nonce' => wp_create_nonce( 'tmpcoder-plugin-options-js' ),
                            'post_type' => TMPCODER_THEME_ADVANCED_HOOKS_POST_TYPE,
                            'admin_url' => admin_url(),
                            'valid_name_msg' => esc_html('Enter valid tamplate name', 'sastra-essential-addons-for-elementor'),
                      ]
                );
              }
        }

        public function tmpcoder_enqueue_panel_scripts() {

            wp_enqueue_script(
              'tmpcoder-editor-js',
              TMPCODER_PLUGIN_URI . 'assets/js/admin/editor'.tmpcoder_script_suffix().'.js',
              [ 'jquery', 'wp-i18n' ],
              tmpcoder_get_plugin_version(),
              true
            );

            if (function_exists('tmpcoder_is_key_expired')) {
              $is_key_expire = tmpcoder_is_key_expired();  
            }
            else
            {
              $is_key_expire = false;  
            }

            wp_localize_script( 'tmpcoder-editor-js', 'tmpcoder_config', array( 
              'TMPCODER_PURCHASE_PRO_URL'   => esc_url(TMPCODER_PURCHASE_PRO_URL),
              'tmpcoder_registered_modules' => tmpcoder_get_all_widgtes(),
              'TMPCODER_DEMO_IMPORT_API'    => esc_url(TMPCODER_DEMO_IMPORT_API),
              'is_key_expire' => $is_key_expire,
              'renew_button_text' => esc_html('Renew Here'),
              'header_editor_btn_text' => esc_html('Edit Header'),
              'header_editor_btn_text_title' => esc_html('Redirect to Header Layout Editor'),
              'footer_editor_btn_text' => esc_html('Edit Footer'),
              'footer_editor_btn_text_title' => esc_html('Redirect to Footer Layout Editor'),
              'single_editor_btn_text' => esc_html('Edit Single Post Layout'),
              'single_editor_btn_text_title' => esc_html('Redirect to Single Post Layout Editor'),
              'single_product_editor_btn_text' => esc_html('Edit Single Product Layout'),
              'single_product_editor_btn_text_title' => esc_html('Redirect to Single Product Layout Editor'),
              'expire_notice' => esc_html('Your license has expired. Please renew immediately to avoid service interruption.'),
            ));
        }

        public function tmpcoder_enqueue_editor_styles() {

          wp_enqueue_style(
            'widgets-editor',
            TMPCODER_PLUGIN_URI . 'assets/css/admin/widgets-editor.min.css',
            [],
            tmpcoder_get_plugin_version()
          );

          wp_enqueue_style(
            'tmpcoder-flipster-css',
            TMPCODER_PLUGIN_URI . 'assets/css/lib/flipster/jquery.flipster'.tmpcoder_script_suffix().'.css',
            [],
            tmpcoder_get_plugin_version()
          );

          wp_enqueue_style(
            'tmpcoder-animations-link-css',
            TMPCODER_PLUGIN_URI.'assets/grid-widgets/lib/tmpcoder-link-animations'.tmpcoder_script_suffix().'.css',
            [],
            tmpcoder_get_plugin_version()
          );

          wp_enqueue_style(
              'tmpcoder-loading-animations-css',
              TMPCODER_PLUGIN_URI.'assets/grid-widgets/lib/loading-animations'.tmpcoder_script_suffix().'.css',
              [],
              tmpcoder_get_plugin_version()
            );

          wp_enqueue_style( 
            'tmpcoder-button-animations-css',
            TMPCODER_PLUGIN_URI . 'assets/grid-widgets/lib/button-animations'.tmpcoder_script_suffix().'.css',
            [],
           tmpcoder_get_plugin_version() 
          );

          wp_enqueue_style(
            'tmpcoder-animations-css',
            TMPCODER_PLUGIN_URI . 'assets/css/lib/animations/tmpcoder-animations'.tmpcoder_script_suffix().'.css',
            [],
            tmpcoder_get_plugin_version()
          );

          wp_enqueue_style( 
            'tmpcoder-text-animations-css', 
            TMPCODER_PLUGIN_URI . 'assets/css/lib/animations/text-animations'.tmpcoder_script_suffix().'.css', 
            [], 
            tmpcoder_get_plugin_version() 
          );

        }

        public function tmpcoder_enqueue_inner_panel_scripts() {

            wp_enqueue_script(
                'tmpcoder-macy-js',
                TMPCODER_PLUGIN_URI . 'assets/js/lib/macy/macy'.tmpcoder_script_suffix().'.js',
                [],
                tmpcoder_get_plugin_version(),
                true
            );

            wp_enqueue_script( 
                'tmpcoder-prebuild-script-js', 
                TMPCODER_PLUGIN_URI .'assets/js/admin/prebuild-script'.tmpcoder_script_suffix().'.js', 
                [ 'jquery', 'tmpcoder-macy-js' ],
                tmpcoder_get_plugin_version(), 
                ['strategy' => 'async', 'in_footer' => true]
            );

            wp_localize_script(
                'tmpcoder-prebuild-script-js',
                'TmpcoderLibFrontJs',
                [
                    'logo_url' => TMPCODER_ADDONS_ASSETS_URL .'images/logo-40x40.svg',
                    'demos_url' => TMPCODER_DEMO_IMPORT_API,
                    'nonce' => wp_create_nonce('tmpcoder-library-frontend-js')
                ]
            );

            wp_enqueue_script(
                'tmpcoder-library-editor-js',
                TMPCODER_PLUGIN_URI . 'assets/js/admin/library-editor'. tmpcoder_script_suffix() .'.js',
                [ 'jquery' ],
                tmpcoder_get_plugin_version(),
                true
            );
        }

        public function enqueue_panel_styles() {

            wp_enqueue_style(
              'tmpcoder-addon-library-editor-css',
              TMPCODER_PLUGIN_URI . 'assets/css/admin/editor'. tmpcoder_script_suffix() .'.css',
              [],
              tmpcoder_get_plugin_version()
            );

            $custom_css = "
              .tmpcoder-pro-notice {
                background: #404349 !important;
                border-color: #323232 !important;
              }
              .elementor-control select option[value*=pro-] {
                background: #60646e !important;
              }
              .elementor-panel .tmpcoder-icon:after {
                box-shadow: 0 0 2px 2px #6985ee !important;
              }
              .tmpcoder-pro-notice > span {
                color: #fff !important;
                font-weight: bold;
              }
            ";
            
            $ui_theme = isset(get_user_meta(get_current_user_id(), 'elementor_preferences')[0]['ui_theme']) ? get_user_meta(get_current_user_id(), 'elementor_preferences')[0]['ui_theme'] : '';
            
            if ( $ui_theme && $ui_theme === 'dark' ) {
              wp_add_inline_style( 'elementor-editor-dark-mode', $custom_css );
            }
        }

        public function tmpcoder_register_scripts(){

          wp_register_script(
            'jquery-event-move',
            TMPCODER_PLUGIN_URI . 'assets/js/lib/jquery-event-move/jquery.event.move'. tmpcoder_script_suffix() .'.js',
            [],
            tmpcoder_get_plugin_version(),
            true
          );

          wp_register_script(
            'tmpcoder-marquee',
            TMPCODER_PLUGIN_URI . 'assets/js/lib/marquee/marquee'. tmpcoder_script_suffix() .'.js',
            [
              'jquery',
            ],
            tmpcoder_get_plugin_version(),
            true
          );

          wp_register_script(
            'tmpcoder-infinite-scroll',
            TMPCODER_PLUGIN_URI . 'assets/js/lib/infinite-scroll/infinite-scroll'. tmpcoder_script_suffix() .'.js',
            [
              'jquery',
            ],
            tmpcoder_get_plugin_version(),
            true
          );

          wp_register_script(
            'tmpcoder-lottie-animations-lib',
            TMPCODER_PLUGIN_URI . 'assets/js/lib/lottie/lottie'.tmpcoder_script_suffix().'.js',
            [],
            tmpcoder_get_plugin_version(),
            true
          );

          wp_register_script( 
            'tmpcoder-table-to-excel-js',
             TMPCODER_PLUGIN_URI  . 'assets/js/lib/tableToExcel/tableToExcel'.tmpcoder_script_suffix().'.js',
             [],
             tmpcoder_get_plugin_version(),
             true 
          );

          wp_register_script('tmpcoder-aos-js', TMPCODER_PLUGIN_URI.'assets/js/lib/aos/aos'.tmpcoder_script_suffix().'.js', [], tmpcoder_get_plugin_version(), true);

          wp_register_script(
            'tmpcoder-flipster',
            TMPCODER_PLUGIN_URI . 'assets/js/lib/flipster/jquery.flipster'.tmpcoder_script_suffix().'.js',
            [],
            tmpcoder_get_plugin_version(),
            true
          );

          wp_register_script( 
            'tmpcoder-slick',
             TMPCODER_PLUGIN_URI . 'assets/grid-widgets/lib/slick/slick'.tmpcoder_script_suffix().'.js',
             [ 'jquery' ],
             tmpcoder_get_plugin_version(),
             true 
          );

          wp_register_script( 
            'tmpcoder-lightgallery',
             TMPCODER_PLUGIN_URI.'assets/grid-widgets/lib/lightgallery/lightgallery'.tmpcoder_script_suffix().'.js',
             [ 'jquery' ],
             tmpcoder_get_plugin_version(),
             true 
          );

          wp_register_script( 
            'tmpcoder-isotope',
             TMPCODER_PLUGIN_URI.'assets/grid-widgets/lib/isotope/isotope'.tmpcoder_script_suffix().'.js',
             [ 'jquery' ],
             tmpcoder_get_plugin_version(),
             true 
          );

          wp_register_script( 
            'tmpcoder-grid-widgets',
            TMPCODER_PLUGIN_URI.'assets/js/widgets/grid-widgets'.tmpcoder_script_suffix().'.js',
            [ 'jquery' ],
            tmpcoder_get_plugin_version(),
            true
          );

          wp_register_script( 
            'tmpcoder-script-js',
            TMPCODER_PLUGIN_URI.'assets/js/script'.tmpcoder_script_suffix().'.js',
            [ 'jquery', 'elementor-frontend' ],
            tmpcoder_get_plugin_version(),
            true 
          );

          $tmpcoder_get_all_widgtes = tmpcoder_get_all_widgtes();

          if (is_array($tmpcoder_get_all_widgtes) && !empty($tmpcoder_get_all_widgtes)) {

            foreach ($tmpcoder_get_all_widgtes as $key => $widget) {

              $slug = 'tmpcoder-element-'.$widget[0];
              $item_status = get_option($slug, true);

              if (!empty($item_status) && $item_status == 'on') {

                if (isset($widget[0]) && !empty($widget[0])) {

                  $css_path = TMPCODER_PLUGIN_DIR.'assets/css/widgets/'.$widget[0].tmpcoder_script_suffix().'.css';
                  $js_path = TMPCODER_PLUGIN_DIR.'assets/js/widgets/'.$widget[0].tmpcoder_script_suffix().'.js';

                  $css_url = TMPCODER_PLUGIN_URI.'assets/css/widgets/'.$widget[0].tmpcoder_script_suffix().'.css';
                  $js_url = TMPCODER_PLUGIN_URI.'assets/js/widgets/'.$widget[0].tmpcoder_script_suffix().'.js';

                  // if (file_exists($css_path)) {
                  //   wp_register_style( 
                  //     'tmpcoder-'.$widget[0], 
                  //     $css_url, 
                  //     [], 
                  //     tmpcoder_get_plugin_version() 
                  //   );
                  // }
                  
                  if (file_exists($js_path)) {
                    wp_register_script( 
                      'tmpcoder-'.$widget[0],
                      $js_url, 
                      [], 
                      tmpcoder_get_plugin_version(), 
                      true 
                    ); 
                  }
                }
              }
            }
          }
        }
        
        public function tmpcoder_enqueue_scripts(){
            
            if ( isset( $_GET['preview'] ) && $_GET['preview'] === 'true' ) {// phpcs:ignore WordPress.Security.NonceVerification.Recommended
              wp_enqueue_style(
                'widgets-editor',
                TMPCODER_PLUGIN_URI.'assets/css/admin/widgets-editor.min.css',
                [],
                tmpcoder_get_plugin_version()
              );
            }

            /* Enqueue the widgets style & script start */

            wp_register_style( 
              'tmpcoder-text-animations-css', 
              TMPCODER_PLUGIN_URI . 'assets/css/lib/animations/text-animations'.tmpcoder_script_suffix().'.css', 
              [], 
              tmpcoder_get_plugin_version() 
            );

            wp_register_style(
              'tmpcoder-animations-css',
              TMPCODER_PLUGIN_URI . 'assets/css/lib/animations/tmpcoder-animations'.tmpcoder_script_suffix().'.css',
              [],
              tmpcoder_get_plugin_version()
            );

            /* Post/Product Style & Script Start */
            
            wp_enqueue_style( 'tmpcoder-woo-grid-css', TMPCODER_PLUGIN_URI . 'assets/grid-widgets/frontend'.tmpcoder_script_suffix().'.css', array('elementor-icons'), tmpcoder_get_plugin_version() );
            
            wp_register_style(
              'tmpcoder-link-animations-css',
              TMPCODER_PLUGIN_URI.'assets/grid-widgets/lib/tmpcoder-link-animations'.tmpcoder_script_suffix().'.css',
              [],
              tmpcoder_get_plugin_version()
            );

            wp_register_style(
              'tmpcoder-button-animations-css',
              TMPCODER_PLUGIN_URI . 'assets/grid-widgets/lib/button-animations'.tmpcoder_script_suffix().'.css',
              [],
              tmpcoder_get_plugin_version()
            );
            
            wp_register_style(
              'tmpcoder-loading-animations-css',
              TMPCODER_PLUGIN_URI.'assets/grid-widgets/lib/loading-animations'.tmpcoder_script_suffix().'.css',
              [],
              tmpcoder_get_plugin_version()
            );

            wp_register_style(
              'tmpcoder-lightgallery-css',
              TMPCODER_PLUGIN_URI . 'assets/grid-widgets/lib/lightgallery/lightgallery'.tmpcoder_script_suffix().'.css',
              [],
              tmpcoder_get_plugin_version()
            );

            $get_reg_settins = get_option('tmpcoder_video_settings_options');
            if ( !empty($get_reg_settins) ){
                wp_enqueue_script( 'tmpcoder-player-vimeo-js', 'https://player.vimeo.com/api/player.js', '', tmpcoder_get_plugin_version(), true );
            }

            /* Enqueue the widgets style & script end */

            /* Post/Product Style & Script End */

            // Posts Timeline
            wp_register_style( 
              'tmpcoder-aos-css', 
              TMPCODER_PLUGIN_URI.'assets/css/lib/aos/aos'.tmpcoder_script_suffix().'.css',
              [], 
              tmpcoder_get_plugin_version()
            );

            wp_register_style(
              'tmpcoder-flipster-css',
              TMPCODER_PLUGIN_URI . 'assets/css/lib/flipster/jquery.flipster'.tmpcoder_script_suffix().'.css',
              [],
              tmpcoder_get_plugin_version()
            );

            // Load FontAwesome - TODO: Check if necessary (maybe elementor is already loading this)
              
            wp_enqueue_style(
              'font-awesome-5-all',
              ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/all'. tmpcoder_script_suffix() .'.css',
              false,
              tmpcoder_get_plugin_version()
            );

            wp_enqueue_style(
              'animations',
              ELEMENTOR_ASSETS_URL . 'lib/animations/animations.min.css',   
              false,
              tmpcoder_get_plugin_version()
            );
            
            if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {

              wp_enqueue_style('tmpcoder-prebuild-style', TMPCODER_PLUGIN_URI . 'assets/css/admin/prebuild-style'.tmpcoder_script_suffix().'.css', array(), tmpcoder_get_plugin_version(), false  );

            }

            wp_enqueue_style('tmpcoder-frontend-style', TMPCODER_PLUGIN_URI . 'assets/css/frontend'.tmpcoder_script_suffix().'.css', array(), tmpcoder_get_plugin_version(), false  );
            
        }

        public function tmpcoder_enqueue_frontend_scripts(){

          wp_enqueue_script( 
            'tmpcoder-script-js', TMPCODER_PLUGIN_URI .'assets/js/script'.tmpcoder_script_suffix().'.js', 
            [
              'jquery',
              'elementor-frontend'
            ], 
            tmpcoder_get_plugin_version(), 
            true 
          );

          $tmpcoder_site_settings = get_option(TMPCODER_THEME_OPTION_NAME);
          if ( empty($tmpcoder_site_settings) ){
              $tmpcoder_site_settings = get_option('tmpcoder_global_theme_options_sastrawp');
          }

          wp_localize_script( 'tmpcoder-script-js', 'tmpcoder_plugin_script', array( 
              'ajax_url' => admin_url( 'admin-ajax.php' ),
              'resturl' => get_rest_url() . 'tmpcoderaddons/v1',
              'nonce' => wp_create_nonce('spexo-addons'),
              'addedToCartText' => esc_html__('was added to cart', 'sastra-essential-addons-for-elementor'),
              'viewCart' => esc_html__('View Cart', 'sastra-essential-addons-for-elementor'),
              'comparePageID' => tmpcoder_get_settings('tmpcoder_compare_page'),
              'comparePageURL' => get_permalink(tmpcoder_get_settings('tmpcoder_compare_page')),
              'wishlistPageID' => tmpcoder_get_settings('tmpcoder_wishlist_page'),
              'wishlistPageURL' => get_permalink(tmpcoder_get_settings('tmpcoder_wishlist_page')),
              'chooseQuantityText' => esc_html__('Please select the required number of items.', 'sastra-essential-addons-for-elementor'),
              'site_key' => get_option('tmpcoder_recaptcha_v3_site_key'),
              'is_admin' => current_user_can('manage_options'),
              'input_empty' => esc_html__('Please fill out this field', 'sastra-essential-addons-for-elementor'),
              'select_empty' => esc_html__('Nothing selected', 'sastra-essential-addons-for-elementor'),
              'file_empty' => esc_html__('Please upload a file', 'sastra-essential-addons-for-elementor'),
              'recaptcha_error' => esc_html__('Recaptcha Error', 'sastra-essential-addons-for-elementor'),
              'tmpcoder_site_settings' => $tmpcoder_site_settings,
          ));
        }

        public function register_custom_control(){

            require TMPCODER_PLUGIN_DIR.'inc/controls/tmpcoder-ajax-select2/tmpcoder-control-ajax-select2.php';
            require_once (TMPCODER_PLUGIN_DIR . 'inc/controls/tmpcoder-ajax-select2/tmpcoder-control-icons.php');

            // Register Custom Controls
            $controls_manager = \Elementor\Plugin::$instance->controls_manager;
            $controls_manager->register( new TMPCODER_Control_Ajax_Select2() );
            $controls_manager->register( new TMPCODER_Control_Animations() );
            $controls_manager->register( new TMPCODER_Control_Animations_Alt() );
            $controls_manager->register( new TMPCODER_Control_Button_Animations() );
            $controls_manager->register( new TMPCODER_Control_Arrow_Icons() );
        } 

        public function tmpcoder_promote_premium_widgets($config){

            $config['promotionWidgets'] = isset($config['promotionWidgets']) && !empty($config['promotionWidgets']) ? $config['promotionWidgets'] : [];
            
            $category = 'tmpcoder-premium-widgets';

            if ( ! tmpcoder_is_availble() ) {
              $promotion_widgets = [
                [
                  'name' => 'tmpcoder-woo-category-grid',
                  'title' => __('Woo Category Grid', 'sastra-essential-addons-for-elementor'),
                  'icon' => 'tmpcoder-icon eicon-gallery-grid',
                  'categories' => '["'. $category .'"]',
                ],
                [
                  'name' => 'tmpcoder-my-account',
                  'title' => __('My Account', 'sastra-essential-addons-for-elementor'),
                  'icon' => 'tmpcoder-icon eicon-my-account',
                  'categories' => '["'. $category .'"]',
                ],
                [
                  'name' => 'tmpcoder-product-filters',
                  'title' => __('Product Filters', 'sastra-essential-addons-for-elementor'),
                  'icon' => 'tmpcoder-icon eicon-filter',
                  'categories' => '["'. $category .'"]',
                ],
                [
                  'name' => 'tmpcoder-product-breadcrumbs',
                  'title' => __('Product Breadcrumbs', 'sastra-essential-addons-for-elementor'),
                  'icon' => 'tmpcoder-icon eicon-product-breadcrumbs',
                  'categories' => '["'. $category .'"]',
                ],
                [
                  'name' => 'tmpcoder-breadcrumbs',
                  'title' => __('Post Breadcrumbs', 'sastra-essential-addons-for-elementor'),
                  'icon' => 'tmpcoder-icon eicon-product-breadcrumbs',
                  'categories' => '["'. $category .'"]',
                ],
                [
                  'name' => 'tmpcoder-category-grid',
                  'title' => __('Category Grid', 'sastra-essential-addons-for-elementor'),
                  'icon' => 'tmpcoder-icon eicon-gallery-grid',
                  'categories' => '["'. $category .'"]',
                ],
                [
                  'name' => 'tmpcoder-wishlist-button',
                  'title' => __('Wishlist Button', 'sastra-essential-addons-for-elementor'),
                  'icon' => 'tmpcoder-icon eicon-heart',
                  'categories' => '["'. $category .'"]',
                ],
                [
                  'name' => 'tmpcoder-mini-wishlist',
                  'title' => __('Mini Wishlist', 'sastra-essential-addons-for-elementor'),
                  'icon' => 'tmpcoder-icon eicon-heart',
                  'categories' => '["'. $category .'"]',
                ],
                [
                  'name' => 'tmpcoder-wishlist',
                  'title' => __('Wishlist Table', 'sastra-essential-addons-for-elementor'),
                  'icon' => 'tmpcoder-icon eicon-heart',
                  'categories' => '["'. $category .'"]',
                ],
                [
                  'name' => 'tmpcoder-compare-button',
                  'title' => __('Compare Button', 'sastra-essential-addons-for-elementor'),
                  'icon' => 'tmpcoder-icon eicon-exchange', // TMPCODER INFO -  new icon needed for compare
                  'categories' => '["'. $category .'"]',
                ],
                [
                  'name' => 'tmpcoder-mini-compare',
                  'title' => __('Mini Compare', 'sastra-essential-addons-for-elementor'),
                  'icon' => 'tmpcoder-icon eicon-exchange',
                  'categories' => '["'. $category .'"]',
                ],
                [
                  'name' => 'tmpcoder-compare',
                  'title' => __('Compare Table', 'sastra-essential-addons-for-elementor'),
                  'icon' => 'tmpcoder-icon eicon-exchange',
                  'categories' => '["'. $category .'"]',
                ],
                [
                  'name' => 'tmpcoder-custom-field',
                  'title' => __('Custom Field', 'sastra-essential-addons-for-elementor'),
                  'icon' => 'tmpcoder-icon eicon-database',
                  'categories' => '["'. $category .'"]',
                ],
              ];
              
              $config['promotionWidgets'] = array_merge( $config['promotionWidgets'], $promotion_widgets );
            }

            return $config;
        }
    }

    // Instantiate Plugin Class
    TemplatesWidgetRegister::instance();
}