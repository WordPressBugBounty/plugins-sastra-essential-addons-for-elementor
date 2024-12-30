<?php
/**
 * Template part for the getting started tab in welcome screen
 *
 * @package Epsilon Framework
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Getting started template
*/

$theme_option_url = admin_url('admin.php?page=sastra-theme-builder');
$count          = $this->count_actions();

$installationStepList = array();
$featuredList = array();

    $featuredArr = array(
        'title' => __( 'Prebuilt Blocks', 'sastra-essential-addons-for-elementor' ),
        'icon' => 'prebuilt-block.svg',
        'action_link' => admin_url('admin.php?page=spexo-welcome&tab=prebuilt-blocks'),
        'description' => __( 'Various ready to use blocks to speed up your desiging process.', 'sastra-essential-addons-for-elementor' ),
        'action_link_text' => 'Read More',
        'target' => '',
        'extra_class' => '',
        'is_lock' => false,
    );
    array_push($featuredList, $featuredArr);

    if ( function_exists('get_template') && in_array(get_template(), array('sastrawp', 'spexo')) ) {
        $featuredArr = array(
            'title' => __( 'Prebuilt Websites', 'sastra-essential-addons-for-elementor' ),
            'icon' => 'prebuilt-websites.svg',
            'action_link' => admin_url('admin.php?page=tmpcoder-import-demo'),
            'description' => __( 'Launch your site instantly with prebuilt layouts and one-click import.', 'sastra-essential-addons-for-elementor' ),
            'action_link_text' => 'Read More',
            'target' => '',
            'extra_class' => '',
            'is_lock' => false,
        );
        array_push($featuredList, $featuredArr);
    } else {
        $featuredArr = array(
            'title' => __( 'Prebuilt Websites', 'sastra-essential-addons-for-elementor' ),
            'icon' => 'prebuilt-websites.svg',
            'action_link' => admin_url('theme-install.php?search=spexo'),
            'description' => __( 'Install Spexo theme and get prebuilt website.', 'sastra-essential-addons-for-elementor' ),
            'action_link_text' => 'Get Spexo Theme',
            'target' => '',
            'extra_class' => 'tmpcoder-lock-pre-websites',
            'is_lock' => true,
        );
        array_push($featuredList, $featuredArr);
    }

     $featuredArr = array(
        'title' => __( 'Site Builder', 'sastra-essential-addons-for-elementor' ),
        'icon' => 'site-builder.svg',
        'action_link' => admin_url('admin.php?page=spexo-welcome&tab=site-builder'),
        'description' => __( 'Customize your global sections and pages as per your requirements.', 'sastra-essential-addons-for-elementor' ),
        'action_link_text' => 'Read More',
        'target' => '',
        'extra_class' => '',
        'is_lock' => false,
    );
    array_push($featuredList, $featuredArr);

    $featuredArr = array(
        'title' => __( 'Widget Settings', 'sastra-essential-addons-for-elementor' ),
        'icon' => 'widget-setting.svg',
        'action_link' => admin_url('admin.php?page=spexo-welcome&tab=widgets'),
        'description' => __( 'Turn off unused widgets to optimize your website speed.', 'sastra-essential-addons-for-elementor' ),
        'action_link_text' => 'Read More',
        'target' => '',
        'extra_class' => ( function_exists('get_template') && in_array(get_template(), array('sastrawp', 'spexo')) ? 'set-box' : ''),
        'is_lock' => false,
    );
    array_push($featuredList, $featuredArr);

    if ( ( function_exists('get_template') && get_template() == 'sastrawp' ) && class_exists('ReduxFramework') ) {
        $featuredArr = array(
            'title' => __( 'Global Options', 'sastra-essential-addons-for-elementor' ),
            'icon' => 'global-setting.svg',
            'action_link' => admin_url('admin.php?page=sastra_addon_global_settings'),
            'description' => __( 'Control all site-wide settings easily with Global Options.', 'sastra-essential-addons-for-elementor' ),
            'action_link_text' => 'Read More',
            'target' => '',
            'extra_class' => 'set-box',
            'is_lock' => false,
        );
        array_push($featuredList, $featuredArr);
    }

    if ( ( function_exists('get_template') && get_template() == 'spexo' ) && class_exists('ReduxFramework') ) {
        $featuredArr = array(
            'title' => __( 'Global Options', 'sastra-essential-addons-for-elementor' ),
            'icon' => 'global-setting.svg',
            'action_link' => admin_url('admin.php?page=spexo_addons_global_settings'),
            'description' => __( 'Control all site-wide settings easily with Global Options.', 'sastra-essential-addons-for-elementor' ),
            'action_link_text' => 'Read More',
            'target' => '',
            'extra_class' => 'set-box',
            'is_lock' => false,
        );
        array_push($featuredList, $featuredArr);
    }
?>


<div class="wc-part">
    <div class="row">
        <div class="col-xl-8">
            <div class="wc-data">
                <h2>
                    <img src="<?php echo esc_url(TMPCODER_ADDONS_ASSETS_URL.'images/launch.svg'); ?>">
                    <span>
                        <?php
                        echo sprintf(
                            /* translators: 1: Welcome Screen Title. */
                             esc_html__( 'Welcome to %1$s - v', 'sastra-essential-addons-for-elementor' ), esc_html( ucfirst(TMPCODER_PLUGIN_NAME) ) ) . esc_html( TMPCODER_PLUGIN_VER );
                        ?>
                    </span>
                </h2>
                <p>
                <?php
                    echo sprintf(
                        /* translators: 1: Welcome Screen Description. */
                        esc_html__( '%1$s is now installed and ready to use! Get ready to build something beautiful. We hope you enjoy it! We want to make sure you have the best experience using %1$s and that is why we gathered here all the necessary information for you. We hope you will enjoy using %1$s ', 'sastra-essential-addons-for-elementor' ), esc_html( ucfirst(TMPCODER_PLUGIN_NAME) ) );
                    ?>
                </p>
            </div>
            
            <div class="block-part">
                <div class="row">
                    <?php
                    if ( !empty( $featuredList ) ){
                        foreach ($featuredList as $key => $featuredItem) {
                            if ( $featuredItem['is_lock'] != true ) {
                                ?>
                                <div class="col-xl-4 <?php echo esc_attr($featuredItem['extra_class']) ?>">
                                    <div class="common-box-shadow">
                                        <a target="<?php echo esc_attr($featuredItem['target']) ?>" href="<?php echo esc_url( $featuredItem['action_link'] ); ?>">
                                            <div class="h-icon"><img src="<?php echo esc_url(TMPCODER_ADDONS_ASSETS_URL.'images/'.$featuredItem['icon']); ?>"></div>
                                            <h3><?php echo esc_html($featuredItem['title']); ?></h3>
                                            <p><?php echo esc_html($featuredItem['description']); ?></p>
                                            <!-- <span class="read-more"><?php echo esc_html__( 'Read More', 'sastra-essential-addons-for-elementor' ); ?></span> -->
                                            <span class="read-more"><?php echo esc_html($featuredItem['action_link_text']); ?></span>
                                        </a>
                                    </div>     
                                </div>
                                <?php 
                            } else {
                                ?>
                                <div class="col-xl-4 <?php echo esc_attr($featuredItem['extra_class']) ?>">
                                    <div class="tmpcoder-settings-group-woo common-box-shadow">
                                        <div class="tmpcoder-settings-group-tooltip">
                                            <div class="tmpcoder-setting">
                                                <a target="<?php echo esc_attr($featuredItem['target']) ?>" href="<?php echo esc_url( $featuredItem['action_link'] ); ?>">
                                                    <div class="h-icon"><img src="<?php echo esc_url(TMPCODER_ADDONS_ASSETS_URL.'images/'.$featuredItem['icon']); ?>"></div>
                                                    <h3><?php echo esc_html($featuredItem['title']); ?></h3>
                                                    <p><?php echo esc_html($featuredItem['description']); ?></p>
                                                    <span class="read-more"><?php echo esc_html($featuredItem['action_link_text']); ?></span>
                                                </a>
                                                <div class="tmpcoder-setting-tooltip">
                                                    <a href="<?php echo esc_url( $featuredItem['action_link'] ); ?>" class="tmpcoder-setting-tooltip-link" target="<?php echo esc_attr($featuredItem['target']) ?>">
                                                        <span class="dashicons dashicons-lock"></span>
                                                        <span class="dashicons dashicons-unlock"></span>
                                                    </a>
                                                    <div class="tmpcoder-setting-tooltip-text"><?php esc_html_e( 'Get Spexo Theme', 'sastra-essential-addons-for-elementor' ); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="col-xl-4 help-box-main">
            <?php /*
            <div class="common-box-shadow help-box">
                <a href="<?php echo esc_url(admin_url('admin.php?page=spexo-welcome&tab=system-info')); ?>" target="_blank">
                    <h3><img src="<?php echo esc_url(TMPCODER_ADDONS_ASSETS_URL.'images/system-info-tab.svg'); ?>"><span><?php echo esc_html__( 'System Info', 'sastra-essential-addons-for-elementor' ); ?></span></h3>
                    <p> <?php echo esc_html__( 'Comprehensive System Information for Plugin Compatibility and Performance', 'sastra-essential-addons-for-elementor' ); ?> </p>
                </a>
            </div>*/
            ?>
            <div class="common-box-shadow help-box">
                <a href="<?php echo esc_url(TMPCODER_RATING_LINK); ?>" target="_blank">
                    <h3><img src="<?php echo esc_url(TMPCODER_ADDONS_ASSETS_URL.'images/rate-us.svg'); ?>"><span><?php echo esc_html__( 'Rate Us', 'sastra-essential-addons-for-elementor' ); ?></span></h3>
                    <p> <?php echo esc_html__( 'Take your 2 minutes to review the plugin and spread the love to encourage us to keep it going.', 'sastra-essential-addons-for-elementor' ); ?> </p>
                </a>
            </div>
            <div class="common-box-shadow help-box">
                <a href="<?php echo esc_url( TMPCODER_NEED_HELP_URL ); ?>" target="_blank">
                    <h3><img src="<?php echo esc_url(TMPCODER_ADDONS_ASSETS_URL.'images/need-help.svg'); ?>"><span><?php echo esc_html__( 'Need Help?', 'sastra-essential-addons-for-elementor' ); ?></span></h3>
                    <p> <?php echo esc_html__( 'Stuck with something? Get help from live chat or submit a support ticket.', 'sastra-essential-addons-for-elementor' ); ?> </p>
                </a>
            </div>

            <?php if (!defined( 'TMPCODER_ADDONS_PRO_VERSION' )) { ?>

            <div class="common-box-shadow help-box relative">
                <div class="pro-box-overlay"></div>
                <div class="pro-box-main">
                    <h3><img src="<?php echo esc_url(TMPCODER_ADDONS_ASSETS_URL.'images/rocket.svg'); ?>"><span><?php echo esc_html__( 'Get Spexo Addons Pro', 'sastra-essential-addons-for-elementor' ); ?></span></h3>
                    
                    <p> <?php echo esc_html__( 'Unlock access to all our premium widgets and features.', 'sastra-essential-addons-for-elementor' ); ?></p>
                    <ul>
                        <li><img src="<?php echo esc_url(TMPCODER_ADDONS_ASSETS_URL.'images/icon-check-white.svg'); ?>"><span><?php echo esc_html('80+ Pro Widgets','sastra-essential-addons-for-elementor'); ?></span></li>
                        <li><img src="<?php echo esc_url(TMPCODER_ADDONS_ASSETS_URL.'images/icon-check-white.svg'); ?>"><span><?php echo esc_html('75+ Pro Prebuilt Blocks','sastra-essential-addons-for-elementor'); ?></span></li>
                        <li><img src="<?php echo esc_url(TMPCODER_ADDONS_ASSETS_URL.'images/icon-check-white.svg'); ?>"><span><?php echo esc_html('25+ Pro Prebuilt Sections','sastra-essential-addons-for-elementor'); ?></span></li>
                        <li><img src="<?php echo esc_url(TMPCODER_ADDONS_ASSETS_URL.'images/icon-check-white.svg'); ?>"><span><?php echo esc_html('15+ Pro Prebuilt WebSites','sastra-essential-addons-for-elementor'); ?></span></li>
                    </ul>
                    <a href="<?php echo esc_url( TMPCODER_PURCHASE_PRO_URL.'?ref=tmpcoder-welcome-screen' ); ?>" target="_blank" class="pro-btn-link"><?php echo esc_html('Get Spexo Addons Pro','sastra-essential-addons-for-elementor'); ?></a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>