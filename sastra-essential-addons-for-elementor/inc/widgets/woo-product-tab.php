<?php
namespace TMPCODER\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TMPCODER_Product_Tabs extends Widget_Base {

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
  
        wp_register_script( 'tmpcoder-product-script', 
        TMPCODER_PLUGIN_URI . 'assets/js/admin/tmpcoder-product-script'.tmpcoder_script_suffix().'.js',
        [ 'jquery', 'elementor-frontend' ], 
        tmpcoder_get_plugin_version(),
        true );
    }
	
	public function get_name() {
		return 'tmpcoder-product-tabs';
	}

	public function get_title() {
		return esc_html__( 'Product Tabs', 'sastra-essential-addons-for-elementor' );
	}

	public function get_icon() {
		return 'tmpcoder-icon eicon-product-tabs';
	}

	public function get_categories() {
		return tmpcoder_show_theme_buider_widget_on('type_single_product') ? [ 'tmpcoder-woocommerce-builder-widgets'] : [];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'product-tabs', 'product', 'tabs' ];
	}
	
	public function get_script_depends() {
		return [ 'wc-single-product', 'tmpcoder-product-script'];
	}
	
	public function get_style_depends() {
		return [ 'tmpcoder-product-tabs' ];
	}

	public function add_control_tabs_position() {
		$this->add_control(
			'tabs_position',
			[
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__( 'Label Position', 'sastra-essential-addons-for-elementor' ),
				'default' => 'above',
				'options' => [
					'above' => esc_html__( 'Horizontal', 'sastra-essential-addons-for-elementor' ),
					'pro-lt' => esc_html__( 'Vertical Left (Pro)', 'sastra-essential-addons-for-elementor' ),
					'pro-rt' => esc_html__( 'Vertical Right (Pro)', 'sastra-essential-addons-for-elementor' ),
				],
				'prefix_class' => 'tmpcoder-tabs-position-',
			]
		);
	}

	public function add_controls_group_tabs_label_adjustments() {}

	protected function register_controls() {

		$this->start_controls_section(
			'section_product_tabs_style',
			[
				'label' => esc_html__( 'Tab Labels', 'sastra-essential-addons-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
            'wc_style_warning',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__( 'The product tabs preview show in frontend.', 'sastra-essential-addons-for-elementor' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );

		$this->add_control_tabs_position();

		// Upgrade to Pro Notice
		tmpcoder_upgrade_pro_notice( $this, Controls_Manager::RAW_HTML, 'product-tabs', 'tabs_position', ['pro-lt', 'pro-rt'] );

		$this->add_controls_group_tabs_label_adjustments();

		$this->add_control(
			'tabs_style_divider',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);
		
		$this->start_controls_tabs( 'tabs_style' );

		$this->start_controls_tab( 
			'normal_tabs_style',
			[
				'label' => esc_html__( 'Normal', 'sastra-essential-addons-for-elementor' ),
			]
		);

		$this->add_control(
			'tab_text_color',
			[
				'label' => esc_html__( 'Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#787878',
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tab_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'alpha' => true,
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tabs_border_color',
			[
				'label' => esc_html__( 'Border Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'selectors' => [
					// '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li a' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li' => 'border-color: transparent !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tab_typography',
				'label' => esc_html__( 'Typography', 'sastra-essential-addons-for-elementor' ),
				'selector' => '{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li a',
				'fields_options' => [
					'typography' => [
						'default' => 'custom',
					],
					'font_size' => [
						'default' => [
							'size' => '14',
							'unit' => 'px',
						],
					]
				]
			]
		);

		$this->add_control(
			'tab_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0.5,
				'min' => 0,
				'max' => 5,
				'step' => 0.1,
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li a' => '-webkit-transition-duration: {{VALUE}}s;transition-duration: {{VALUE}}s',
				],
			]
		);

		$this->add_control(
			'tab_border_type',
			[
				'label' => esc_html__( 'Border Type', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => esc_html__( 'None', 'sastra-essential-addons-for-elementor' ),
					'solid' => esc_html__( 'Solid', 'sastra-essential-addons-for-elementor' ),
					'double' => esc_html__( 'Double', 'sastra-essential-addons-for-elementor' ),
					'dotted' => esc_html__( 'Dotted', 'sastra-essential-addons-for-elementor' ),
					'dashed' => esc_html__( 'Dashed', 'sastra-essential-addons-for-elementor' ),
					'groove' => esc_html__( 'Groove', 'sastra-essential-addons-for-elementor' ),
				],
				'default' => 'solid',
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li a' => 'border-style: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'tab_border_width',
			[
				'label' => esc_html__( 'Border Width', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top' => 1,
					'right' => 1,
					'bottom' => 0,
					'left' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}.tmpcoder-tabs-position-above .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li.active:after' => 'margin-bottom: -{{TOP}}px; height: {{TOP}}px; width: calc(100% - {{TOP}}*2px)',
				],
				'condition' => [
					'tab_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'tab_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li'=> 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li a'=> 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_padding',
			[
				'label' => esc_html__( 'Padding', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top' => 10,
					'right' => 10,
					'bottom' => 10,
					'left' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		
		$this->add_responsive_control(
			'tab_margin',
			[
				'label' => esc_html__( 'Margin', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				// 'allowed_dimensions' => ['left', 'right'],
				'size_units' => [ 'px' ],
				'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .wc-tabs li a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', // li or a ?
					'{{WRAPPER}} .wc-tabs li.active a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} -0.1px {{LEFT}}{{UNIT}};', // li or a ?
					'{{WRAPPER}} .wc-tabs li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', // li or a ?
					'{{WRAPPER}} .wc-tabs li.active' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} -0.1px {{LEFT}}{{UNIT}};', // li or a ?
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 
			'hover_tabs_style',
			[
				'label' => esc_html__( 'Hover', 'sastra-essential-addons-for-elementor' ),
			]
		);

		$this->add_control(
			'hover_tab_text_color',
			[
				'label' => esc_html__( 'Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#5729d9',
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li:hover a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'hover_tab_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'alpha' => true,
				'selectors' => [
					// '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel, {{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li:hover' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel, {{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li:hover a' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li.active' => 'border-bottom-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tab_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E8E8E8',
				'selectors' => [
					// '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel' => 'border-color: {{VALUE}}',
					// '{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li.active' => 'border-color: {{VALUE}} {{VALUE}} {{active_tab_bg_color.VALUE}} {{VALUE}}',
					// '{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li:not(.active)' => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li:hover a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tab_typography_hover',
				'label' => esc_html__( 'Typography', 'sastra-essential-addons-for-elementor' ),
				'selector' => '{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li a:hover',
				'fields_options' => [
					'typography' => [
						'default' => 'custom',
					],
					'font_size' => [
						'default' => [
							'size' => '14',
							'unit' => 'px',
						],
					]
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 
			'active_tabs_style',
			[
				'label' => esc_html__( 'Active', 'sastra-essential-addons-for-elementor' ),
			]
		);

		$this->add_control(
			'active_tab_text_color',
			[
				'label' => esc_html__( 'Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#5729d9',
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li.active a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'active_tab_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'alpha' => true,
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li.active a' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li.active' => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li.active:after' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tab_active_border_color',
			[
				'label' => esc_html__( 'Border Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E8E8E8',
				'selectors' => [
					// '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel' => 'border-color: {{VALUE}}',
					// '{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li.active' => 'border-color: {{VALUE}} {{VALUE}} {{active_tab_bg_color.VALUE}} {{VALUE}}',
					// '{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li:not(.active)' => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li.active a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tab_typography_active',
				'label' => esc_html__( 'Typography', 'sastra-essential-addons-for-elementor' ),
				'selector' => '{{WRAPPER}} .tmpcoder-product-tabs .woocommerce-tabs ul.tabs li.active a',
				'separator' => 'before',
				'fields_options' => [
					'typography' => [
						'default' => 'custom',
					],
					'font_size' => [
						'default' => [
							'size' => '14',
							'unit' => 'px',
						],
					]
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_product_panel_style',
			[
				'label' => esc_html__( 'Tabs Content', 'sastra-essential-addons-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#787878',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-Tabs-panel' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tab_content_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'alpha' => true,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'content_border_color',
			[
				'label' => esc_html__( 'Border Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E8E8E8',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					// 'content_border_type!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'label' => esc_html__( 'Typography', 'sastra-essential-addons-for-elementor' ),
				'selector' => '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel',
				'fields_options' => [
					'typography' => [
						'default' => 'custom',
					],
					'font_size' => [
						'default' => [
							'size' => '14',
							'unit' => 'px',
						],
					]
				]
			]
		);

		$this->add_control(
			'panel_heading_style',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Title', 'sastra-essential-addons-for-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_tab_content_titles',
			[
				'label' => esc_html__( 'Show Title', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'label_block' => false,
                'selectors_dictionary' => [
					'' => 'display: none  !important;',
					'yes' => 'display: block !important;',
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-Tabs-panel h2:not(.woocommerce-Reviews-title):not(.elementor-heading-title)' => '{{value}}',
					'{{WRAPPER}} .woocommerce-Reviews-title:not(:first-of-type)' => '{{value}}',
				]
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-Tabs-panel h2:not(.elementor-heading-title)' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_tab_content_titles' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_heading_typography',
				'label' => esc_html__( 'Typography', 'sastra-essential-addons-for-elementor' ),
				'selector' => '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel h2:not(.elementor-heading-title)',
				'fields_options' => [
					'typography' => [
						'default' => 'custom',
					],
					'font_size' => [
						'default' => [
							'size' => '14',
							'unit' => 'px',
						],
					]
				],
				'condition' => [
					'show_tab_content_titles' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'heading_distance',
			[
				'label' => esc_html__( 'Distance', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-Tabs-panel h2:not(.elementor-heading-title)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_tab_content_titles' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'panel_padding',
			[
				'label' => esc_html__( 'Padding', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top' => 35,
					'right' => 35,
					'bottom' => 35,
					'left' => 35,
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', // li or a ?
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'content_border_type',
			[
				'label' => esc_html__( 'Border Type', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => esc_html__( 'None', 'sastra-essential-addons-for-elementor' ),
					'solid' => esc_html__( 'Solid', 'sastra-essential-addons-for-elementor' ),
					'double' => esc_html__( 'Double', 'sastra-essential-addons-for-elementor' ),
					'dotted' => esc_html__( 'Dotted', 'sastra-essential-addons-for-elementor' ),
					'dashed' => esc_html__( 'Dashed', 'sastra-essential-addons-for-elementor' ),
					'groove' => esc_html__( 'Groove', 'sastra-essential-addons-for-elementor' ),
				],
				'default' => 'solid',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel' => 'border-style: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'panel_border_width',
			[
				'label' => esc_html__( 'Border Width', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top' => 1,
					'right' => 1,
					'bottom' => 1,
					'left' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'content_border_type!' => 'none',
				]
			]
		);

		$this->add_control(
			'panel_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .woocommerce-tabs ul.wc-tabs' => 'margin-left: {{TOP}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'additional_info_syles',
			[
				'label' => esc_html__('Additional Information', 'sastra-essential-addons-for-elementor'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'additional_info_label',
			[
				'label'     => esc_html__('Attribute Name', 'sastra-essential-addons-for-elementor'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'additional_info_label_color',
			[
				'label'     => esc_html__('Color', 'sastra-essential-addons-for-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#787878',
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs table th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'additional_info_label_odd_bg_color',
			[
				'label'     => esc_html__('Background Color', 'sastra-essential-addons-for-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs table th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'additional_info_label_even_bg_color',
			[
				'label'     => esc_html__('Even Background Color', 'sastra-essential-addons-for-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs table tr:nth-child(even) th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'additional_info_th_typography',
				'label'          => esc_html__('Typography', 'sastra-essential-addons-for-elementor'),
				'selector'       => '{{WRAPPER}} .woocommerce-Tabs-panel tr :is(th)',
				'exclude'        => ['font_family', 'text_transform', 'text_decoration'],
				'fields_options' => [
					'typography'      => [
						'default' => 'custom',
					],
					'font_size'      => [
						'label'      => esc_html__('Font Size (px)', 'sastra-essential-addons-for-elementor'),
						'size_units' => ['px'],
						'default'    => [
							'size' => '14',
							'unit' => 'px',
						],
					],
					'line_height'     => [
						'label'      => esc_html__('Line Height (px)', 'sastra-essential-addons-for-elementor'),
						'default' => [
							'size' => '14',
							'unit' => 'px',
						],
						'size_units' => ['px'],
						'tablet_default' => [
							'unit' => 'px',
						],
						'mobile_default' => [
							'unit' => 'px',
						],
					],
					'letter_spacing' => [
						'label'      => esc_html__('Letter Spacing (px)', 'sastra-essential-addons-for-elementor'),
						'size_units' => ['px'],
					],
				],
			]
		);

		$this->add_control(
			'additional_info_th_align',
			[
				'label' => esc_html__( 'Alignment', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'sastra-essential-addons-for-elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'sastra-essential-addons-for-elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'sastra-essential-addons-for-elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs table th' => 'text-align: {{VALUE}}',
				],
				'default' => 'left',
			]
		);

		$this->add_responsive_control(
			'additional_info_label_width',
			[
				'label'      => esc_html__('Width', 'sastra-essential-addons-for-elementor'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => 21,
				],
				'selectors'  => [
					'{{WRAPPER}} .tmpcoder-product-tabs table th' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'additional_info_value_heading',
			[
				'label'     => esc_html__('Attribute Value', 'sastra-essential-addons-for-elementor'),
				'type'      => Controls_Manager::HEADING,
                'separator'  => 'before',
			]
		);

		$this->add_control(
			'additional_information_value_color',
			[
				'label'     => esc_html__('Color', 'sastra-essential-addons-for-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#787878',
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs table td p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tmpcoder-product-tabs table td a' => 'color: {{VALUE}};' ,
					'{{WRAPPER}} .tmpcoder-product-tabs table td' => 'color: {{VALUE}};' 
				]
			]
		);

		$this->add_control(
			'additional_information_value_odd_bg_color',
			[
				'label'     => esc_html__('Background Color', 'sastra-essential-addons-for-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs table td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'additional_information_value_even_bg_color',
			[
				'label'     => esc_html__('Even Background Color', 'sastra-essential-addons-for-elementor'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs table tr:nth-child(even) td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'additional_info_td_typography',
				'label'          => esc_html__('Typography', 'sastra-essential-addons-for-elementor'),
				'selector'       => '{{WRAPPER}} .woocommerce-Tabs-panel tr :is(td, p)',
				'exclude'        => ['font_family', 'text_transform', 'text_decoration'],
				'fields_options' => [
					'typography'      => [
						'default' => 'custom',
					],
					'font_size'      => [
						'label'      => esc_html__('Font Size (px)', 'sastra-essential-addons-for-elementor'),
						'size_units' => ['px'],
						'default'    => [
							'size' => '14',
							'unit' => 'px',
						],
					]
				],
			]
		);

		$this->add_responsive_control(
			'additional_info_padding',
			[
				'label'      => esc_html__('Padding', 'sastra-essential-addons-for-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'default'    => [
					'top'      => '15',
					'right'    => '35',
					'bottom'   => '15',
					'left'     => '35',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'separator' => 'before',
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .tmpcoder-product-tabs table td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .tmpcoder-product-tabs table th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'additional_info_divider_color',
			[
				'label'     => esc_html__('Divider (Border) Color', 'sastra-essential-addons-for-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#E8E8E8',
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs table td' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .tmpcoder-product-tabs table th' => 'border-color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'additional_info_border_width',
			[
				'label' => esc_html__( 'Divider (Border) Width', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .tmpcoder-product-tabs table tr td' => 'border-left-width: {{SIZE}}px; border-left-style: solid;',
					'{{WRAPPER}} .tmpcoder-product-tabs table tr th' => 'border-right-width: {{SIZE}}px; border-right-style: solid;',
					'{{WRAPPER}} .tmpcoder-product-tabs table tr:not(:last-child) td' => 'border-bottom-width: {{SIZE}}px; border-bottom-style: solid;',
					'{{WRAPPER}} .tmpcoder-product-tabs table tr:not(:last-child) th' => 'border-bottom-width: {{SIZE}}px; border-bottom-style: solid;',
					'{{WRAPPER}}.tmpcoder-add-info-borders-yes .tmpcoder-product-tabs table td' => 'border-width: {{SIZE}}px; border-style: solid;',
					'{{WRAPPER}}.tmpcoder-add-info-borders-yes .tmpcoder-product-tabs table th' => 'border-width: {{SIZE}}px; border-style: solid;',
				],
			]
		);

		$this->add_control(
			'additional_info_show_borders',
			[
				'label' => esc_html__( 'Show Table Borders', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'tmpcoder-add-info-borders-'
			]
		);

		$this->end_controls_section();

		// Styles ====================
		// Section: Comments ---------
		$this->start_controls_section(
			'section_style_comments',
			[
				'label' => esc_html__( 'Review Comments', 'sastra-essential-addons-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_control(
			'comment_author_color',
			[
				'label' => esc_html__( 'Author Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#222222',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-review__author' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comment_date_color',
			[
				'label' => esc_html__( 'Date Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#787878',
				'selectors' => [
					'{{WRAPPER}} .woocommerce-review__published-date' => 'color: {{VALUE}};',
					'body {{WRAPPER}} .comment-text p.meta' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'comment_rating_color',
			[
				'label' => esc_html__( 'Rating Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFD726',
				'selectors' => [
					'{{WRAPPER}}.elementor-widget-tmpcoder-product-tabs .star-rating span::before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comment_rating_unmarked_color',
			[
				'label' => esc_html__( 'Rating Unmarked Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#D2CDCD',
				'selectors' => [
					'{{WRAPPER}}.elementor-widget-tmpcoder-product-tabs .star-rating::before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comment_description_color',
			[
				'label' => esc_html__( 'Description Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#787878',
				'selectors' => [
					'{{WRAPPER}} .description p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comment_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'sastra-essential-addons-for-elementor' ), 
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .comment-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'comment_border_color',
			[
				'label' => esc_html__( 'Border Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E8E8E8',
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li .comment-text' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'comment_shadow',
				'selector' => '{{WRAPPER}} .comment-text',
			]
		);

		$this->add_control(
			'comment_border_type',
			[
				'label' => esc_html__( 'Border Type', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => esc_html__( 'None', 'sastra-essential-addons-for-elementor' ),
					'solid' => esc_html__( 'Solid', 'sastra-essential-addons-for-elementor' ),
					'double' => esc_html__( 'Double', 'sastra-essential-addons-for-elementor' ),
					'dotted' => esc_html__( 'Dotted', 'sastra-essential-addons-for-elementor' ),
					'dashed' => esc_html__( 'Dashed', 'sastra-essential-addons-for-elementor' ),
					'groove' => esc_html__( 'Groove', 'sastra-essential-addons-for-elementor' ),
				],
				'default' => 'none',
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li .comment-text' => 'border-style: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'comment_border_width',
			[
				'label' => esc_html__( 'Border Width', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top' => 1,
					'right' => 1,
					'bottom' => 1,
					'left' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li .comment-text' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'comment_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'comment_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li .comment-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'comment_border_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'comment_padding',
			[
				'label' => esc_html__( 'Padding', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top' => 5,
					'right' => 5,
					'bottom' => 5,
					'left' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li .comment-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'comment_spacing',
			[
				'label' => esc_html__( 'Vertical Gutter', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],				
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li .comment-text' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'comment_rating_font_size',
			[
				'label' => esc_html__( 'Rating Icon Size', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'rem', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],				
				'default' => [
					'unit' => 'px',
					'size' => 18,
				],
				'selectors' => [
					// '{{WRAPPER}}.elementor-widget-tmpcoder-product-tabs .star-rating span::before' => 'font-size: {{VALUE}}px;',
					'{{WRAPPER}}.elementor-widget-tmpcoder-product-tabs .star-rating' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'review_avatar_styles',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Avatar', 'sastra-essential-addons-for-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'avatar_size',
			[
				'label' => esc_html__( 'Image Size', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li img.avatar' => 'width: {{SIZE}}px; height: auto;',
				],
			]
		);

		$this->add_responsive_control(
			'avatar_margin',
			[
				'label' => esc_html__( 'Distance', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-widget-tmpcoder-product-tabs #reviews #comments ol.commentlist li .comment-text' => 'margin-left: calc({{SIZE}}px + {{avatar_size.SIZE}}px);'
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'avatar_border_color',
			[
				'label' => esc_html__( 'Border Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E8E8E8',
				'selectors' => [
					// '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} #reviews #comments ol.commentlist li img.avatar' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'avatar_border_type',
			[
				'label' => esc_html__( 'Border Type', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => esc_html__( 'None', 'sastra-essential-addons-for-elementor' ),
					'solid' => esc_html__( 'Solid', 'sastra-essential-addons-for-elementor' ),
					'double' => esc_html__( 'Double', 'sastra-essential-addons-for-elementor' ),
					'dotted' => esc_html__( 'Dotted', 'sastra-essential-addons-for-elementor' ),
					'dashed' => esc_html__( 'Dashed', 'sastra-essential-addons-for-elementor' ),
					'groove' => esc_html__( 'Groove', 'sastra-essential-addons-for-elementor' ),
				],
				'default' => 'none',
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li img.avatar' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'avatar_border_width',
			[
				'label' => esc_html__( 'Border Width', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top' => 1,
					'right' => 1,
					'bottom' => 1,
					'left' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li img.avatar' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'avatar_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'avatar_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => 50,
					'right' => 50,
					'bottom' => 50,
					'left' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} #reviews #comments ol.commentlist li img.avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_review_styles_forms',
			[
				'label' => esc_html__( 'Review Forms', 'sastra-essential-addons-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_control(
			'review_labels_styles',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Labels', 'sastra-essential-addons-for-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'labels_color',
			[
				'label' => esc_html__( 'Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#787878',
				'selectors' => [
					'{{WRAPPER}} .comment-notes' => 'color: {{VALUE}};',
					'{{WRAPPER}} .comment-form-rating label' => 'color: {{VALUE}};',
					'{{WRAPPER}} .comment-form-comment label' => 'color: {{VALUE}};',
					'{{WRAPPER}} .comment-form-author label' => 'color: {{VALUE}};',
					'{{WRAPPER}} .comment-form-email label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'labels_color_required',
			[
				'label' => esc_html__( 'Required Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#F92B2B',
				'selectors' => [
					'{{WRAPPER}} .required' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'field_label_title_typography',
				'selector' => '{{WRAPPER}} .woocommerce-tabs .woocommerce-Tabs-panel label',
				'fields_options' => [
					'typography' => [
						'default' => 'custom',
					],
					'font_size' => [
						'default' => [
							'size' => '14',
							'unit' => 'px',
						],
					]
				]
			]
		);

		$this->add_responsive_control(
			'field_label_distance',
			[
				'label' => esc_html__( 'Bottom Distance', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],				
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .comment-form-comment label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .comment-form-rating label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .comment-form-author label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .comment-form-email label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .comment-reply-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'review_rating_styles',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Rating', 'sastra-essential-addons-for-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'rating_color',
			[
				'label' => esc_html__( 'Icon Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#C1C1C1',
				'selectors' => [
					'{{WRAPPER}}.elementor-widget-tmpcoder-product-tabs p.stars.selected a.active:before' => 'color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-tmpcoder-product-tabs p.stars:hover a:before' => 'color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-tmpcoder-product-tabs p.stars.selected a:not(.active):before' => 'color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-tmpcoder-product-tabs p.stars.selected a.active:before' => 'color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-tmpcoder-product-tabs p.stars a:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_size',
			[
				'label' => esc_html__( 'Icon Size', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 18,
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-widget-tmpcoder-product-tabs p.stars a::before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_gutter',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__( 'Icon Gutter', 'sastra-essential-addons-for-elementor' ),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} p.stars a' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'rating_gutter_divider',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->start_controls_tabs( 'tabs_forms_inputs_style' );

		$this->start_controls_tab(
			'tab_inputs_normal',
			[
				'label' => esc_html__( 'Normal', 'sastra-essential-addons-for-elementor' ),
			]
		);

		$this->add_control(
			'input_color',
			[
				'label' => esc_html__( 'Text Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#787878',
				'selectors' => [
					'{{WRAPPER}} .comment-form-comment textarea' => 'color: {{VALUE}}',
					'{{WRAPPER}} .comment-form-author input' => 'color: {{VALUE}};',
					'{{WRAPPER}} .comment-form-email input' => 'color: {{VALUE}};',
					'{{WRAPPER}} .comment-form-author label' => 'display: block;',
					'{{WRAPPER}} .comment-form-email label' => 'display: block;',
				]
			]
		);

		$this->add_control(
			'input_background_color',
			[
				'label' => esc_html__( 'Background Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .comment-form-comment textarea' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .comment-form-author input' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .comment-form-email input' => 'background-color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'input_border_color',
			[
				'label' => esc_html__( 'Border Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E8E8E8',
				'selectors' => [
					'{{WRAPPER}} .comment-form-comment textarea' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .comment-form-author input' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .comment-form-email input' => 'border-color: {{VALUE}}',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'input_box_shadow',
				'selector' => '{{WRAPPER}} .comment-form-comment textarea, {{WRAPPER}} .comment-form-author input, {{WRAPPER}} .comment-form-email input',
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'selector' => '{{WRAPPER}} .comment-form-comment textarea, {{WRAPPER}} .comment-form-author input, {{WRAPPER}} .comment-form-email input',
				'fields_options' => [
					'typography' => [
						'default' => 'custom',
					],
					'font_size' => [
						'default' => [
							'size' => '14',
							'unit' => 'px',
						],
					]
				]
			]
		);

		$this->add_control(
			'input_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0.5,
				'min' => 0,
				'max' => 5,
				'step' => 0.1,
				'selectors' => [
					'{{WRAPPER}} .comment-form-comment textarea' => 'transition-duration: {{VALUE}}s',
					'{{WRAPPER}} .comment-form-author input' => 'transition-duration: {{VALUE}}s',
					'{{WRAPPER}} .comment-form-email input' => 'transition-duration: {{VALUE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_inputs_focus',
			[
				'label' => esc_html__( 'Focus', 'sastra-essential-addons-for-elementor' ),
			]
		);

		$this->add_control(
			'input_color_fc',
			[
				'label' => esc_html__( 'Text Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .comment-form-comment textarea:focus' => 'color: {{VALUE}}',
					'{{WRAPPER}} .comment-form-author input:focus' => 'color: {{VALUE}}',
					'{{WRAPPER}} .comment-form-email input:focus' => 'color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'input_background_color_fc',
			[
				'label' => esc_html__( 'Background Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .comment-form-comment textarea:focus' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .comment-form-author input:focus' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .comment-form-email input:focus' => 'background-color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'input_border_color_fc',
			[
				'label' => esc_html__( 'Border Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E8E8E8',
				'selectors' => [
					'{{WRAPPER}} .comment-form-comment textarea:focus' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .comment-form-author input:focus' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .comment-form-email input:focus' => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'input_box_shadow_fc',
				'selector' => '{{WRAPPER}} .comment-form-comment textarea:focus, {{WRAPPER}} .comment-form-author input:focus, {{WRAPPER}} .comment-form-email input:focus',
				'separator' => 'after',
				'fields_options' => [
                    'box_shadow_type' =>
                        [ 
                            'default' =>'yes' 
                        ],
                    'box_shadow' => [
                        'default' =>
                            [
                                'horizontal' => 0,
                                'vertical' => 0,
                                'blur' => 10,
                                'spread' => 0,
                                'color' => 'rgba(0,0,0,0.5)'
                            ]
                    ]
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'input_border_type',
			[
				'label' => esc_html__( 'Border Type', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => esc_html__( 'None', 'sastra-essential-addons-for-elementor' ),
					'solid' => esc_html__( 'Solid', 'sastra-essential-addons-for-elementor' ),
					'double' => esc_html__( 'Double', 'sastra-essential-addons-for-elementor' ),
					'dotted' => esc_html__( 'Dotted', 'sastra-essential-addons-for-elementor' ),
					'dashed' => esc_html__( 'Dashed', 'sastra-essential-addons-for-elementor' ),
					'groove' => esc_html__( 'Groove', 'sastra-essential-addons-for-elementor' ),
				],
				'default' => 'solid',
				'selectors' => [
					'{{WRAPPER}} .comment-form-comment textarea' => 'border-style: {{VALUE}};',
					'{{WRAPPER}} .comment-form-author input' => 'border-style: {{VALUE}}',
					'{{WRAPPER}} .comment-form-email input' => 'border-style: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'input_border_width',
			[
				'label' => esc_html__( 'Border Width', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top' => 1,
					'right' => 1,
					'bottom' => 1,
					'left' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .comment-form-comment textarea' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .comment-form-author input' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					'{{WRAPPER}} .comment-form-email input' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'input_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'input_radius',
			[
				'label' => esc_html__( 'Border Radius', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .comment-form-comment textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .comment-form-author input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					'{{WRAPPER}} .comment-form-email input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'input_padding',
			[
				'label' => esc_html__( 'Padding', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top' => 10,
					'right' => 10,
					'bottom' => 10,
					'left' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .comment-form-comment textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .comment-form-author input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .comment-form-email input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_responsive_control(
			'input_height',
			[
				'label' => esc_html__( 'Input Height', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 150,
					],
				],				
				'default' => [
					'unit' => 'px',
					'size' => 42,
				],
				'selectors' => [
					'{{WRAPPER}} .comment-form-author input' => 'height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .comment-form-email input' => 'height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'textarea_height',
			[
				'label' => esc_html__( 'Textarea (Message) Height', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 500,
					],
				],				
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
				'selectors' => [
					'{{WRAPPER}} .comment-form-comment textarea#comment' => 'height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'input_spacing',
			[
				'label' => esc_html__( 'Vertical Gutter', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],				
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} #review_form #respond p:not(.form-submit)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} #review_form #respond .comment-form-author' => 'width: calc(50% - {{SIZE}}px/2); margin-right: {{SIZE}}px;',
					'{{WRAPPER}} #review_form #respond .comment-form-email' => 'width: calc(50% - {{SIZE}}px/2);',
				],
			]
		);

		$this->end_controls_section(); // End Controls Section
		
		$this->start_controls_section(
			'section_style_submit_btn',
			[
				'label' => esc_html__( 'Submit Button', 'sastra-essential-addons-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_control(
			'submit_btn_align',
			[
				'label' => esc_html__( 'Alignment', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'sastra-essential-addons-for-elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'sastra-essential-addons-for-elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'sastra-essential-addons-for-elementor' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'sastra-essential-addons-for-elementor' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'tmpcoder-forms-submit-',
				'selectors' => [
					'{{WRAPPER}} .form-submit' => 'text-align: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'submit_btn_align_divider',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->start_controls_tabs( 'tabs_submit_btn_style' );

		$this->start_controls_tab(
			'tab_submit_btn_normal',
			[
				'label' => esc_html__( 'Normal', 'sastra-essential-addons-for-elementor' ),
			]
		);

		$this->add_control(
			'submit_btn_color',
			[
				'label' => esc_html__( 'Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} #respond .comment-form .form-submit input#submit' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'submit_btn_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#5729d9',
				'selectors' => [
					'{{WRAPPER}} #respond .comment-form .form-submit input#submit' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'submit_btn_border_color',
			[
				'label' => esc_html__( 'Border Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E8E8E8',
				'selectors' => [
					'{{WRAPPER}} #respond .comment-form .form-submit input#submit' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'submit_btn_box_shadow',
				'selector' => '{{WRAPPER}} #respond .comment-form .form-submit input#submit',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'submit_btn_typography',
				'selector' => '{{WRAPPER}} #respond .comment-form .form-submit input#submit',
				'typography' => [
					'default' => 'custom',
				],
				'font_weight' => [
					'default' => '400',
				],
				'font_size' => [
					'default' => [
						'size' => '14',
						'unit' => 'px',
					],
				],
				'letter_spacing' => [
					'default' => [
						'size' => '0.2'
					]
				],
			]
		);

		$this->add_control(
			'submit_btn_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0.5,
				'min' => 0,
				'max' => 5,
				'step' => 0.1,
				'selectors' => [
					'{{WRAPPER}} #respond .comment-form .form-submit input#submit' => 'transition-duration: {{VALUE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_submit_btn_hover',
			[
				'label' => esc_html__( 'Hover', 'sastra-essential-addons-for-elementor' ),
			]
		);

		$this->add_control(
			'submit_btn_color_hr',
			[
				'label' => esc_html__( 'Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}}  #respond .comment-form .form-submit input#submit:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'submit_btn_bg_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1F18E3',
				'selectors' => [
					'{{WRAPPER}} #respond .comment-form .form-submit input#submit:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'submit_btn_border_color_hr',
			[
				'label' => esc_html__( 'Border Color', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E8E8E8',
				'selectors' => [
					'{{WRAPPER}} #respond .comment-form .form-submit input#submit:hover' => 'border-color: {{VALUE}}',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'submit_btn_box_shadow_hr',
				'selector' => '{{WRAPPER}} #respond .comment-form .form-submit input#submit:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'submit_btn_border_type',
			[
				'label' => esc_html__( 'Border Type', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => esc_html__( 'None', 'sastra-essential-addons-for-elementor' ),
					'solid' => esc_html__( 'Solid', 'sastra-essential-addons-for-elementor' ),
					'double' => esc_html__( 'Double', 'sastra-essential-addons-for-elementor' ),
					'dotted' => esc_html__( 'Dotted', 'sastra-essential-addons-for-elementor' ),
					'dashed' => esc_html__( 'Dashed', 'sastra-essential-addons-for-elementor' ),
					'groove' => esc_html__( 'Groove', 'sastra-essential-addons-for-elementor' ),
				],
				'default' => 'none',
				'selectors' => [
					'{{WRAPPER}} #respond .comment-form .form-submit input#submit' => 'border-style: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'submit_btn_border_width',
			[
				'label' => esc_html__( 'Border Width', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top' => 1,
					'right' => 1,
					'bottom' => 1,
					'left' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} #respond .comment-form .form-submit input#submit' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'submit_btn_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'submit_btn_radius',
			[
				'label' => esc_html__( 'Border Radius', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} #respond .comment-form .form-submit input#submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'submit_btn_padding',
			[
				'label' => esc_html__( 'Padding', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
					'top' => 12,
					'right' => 40,
					'bottom' => 12,
					'left' => 40,
				],
				'selectors' => [
					'{{WRAPPER}} #respond .comment-form .form-submit input#submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'submit_btn_spacing',
			[
				'label' => esc_html__( 'Top Distance', 'sastra-essential-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],				
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}} #respond .comment-form .form-submit input#submit' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Section: Request New Feature
		tmpcoder_add_section_request_feature( $this, Controls_Manager::RAW_HTML, Controls_Manager::TAB_STYLE );

		// Section: Pro Features
		tmpcoder_pro_features_list_section( $this, Controls_Manager::TAB_STYLE, Controls_Manager::RAW_HTML, 'product-tabs', [
			'Tab Label Horizontal Align',
			'Vertical Tabs Layout'
		] );
    }
	
	public function change_html($reviews_title, $count, $product) {

		$average = $product->get_average_rating();

		$rating_5 = $product->get_rating_count(5);
		$rating_4 = $product->get_rating_count(4);
		$rating_3 = $product->get_rating_count(3);
		$rating_2 = $product->get_rating_count(2);
		$rating_1 = $product->get_rating_count(1);
		$total = $rating_1 + $rating_2 + $rating_3 + $rating_4 + $rating_5;
		$pct5 = $pct4 = $pct3 = $pct2 = $pct1 = 0;

		if ($total > 0) {
			$pct5 = ceil($rating_5 * 100 / $total);
			$pct4 = ceil($rating_4 * 100 / $total);
			$pct3 = ceil($rating_3 * 100 / $total);
			$pct2 = ceil($rating_2 * 100 / $total);
			$pct1 = ceil($rating_1 * 100 / $total);
		}

		$details = '<div class="tmpcoder-individual-rating"><span>' . esc_html__('5 star', 'sastra-essential-addons-for-elementor') . '</span> <span class="tmpcoder-individual-rating-cont"><span style="width: ' . $pct5 . '%"> </span></span> <span>' . $pct5 . '%</span></div><br/> ';
		$details .= '<div class="tmpcoder-individual-rating"><span>' . esc_html__('4 star', 'sastra-essential-addons-for-elementor') . '</span> <span class="tmpcoder-individual-rating-cont"><span style="width: ' . $pct4 . '%"> </span></span> <span>' . $pct4 . '%</span></div><br/> ';
		$details .= '<div class="tmpcoder-individual-rating"><span>' . esc_html__('3 star', 'sastra-essential-addons-for-elementor') . '</span> <span class="tmpcoder-individual-rating-cont"><span style="width: ' . $pct3 . '%"> </span></span> <span>' . $pct3 . '%</span></div><br/> ';
		$details .= '<div class="tmpcoder-individual-rating"><span>' . esc_html__('2 star', 'sastra-essential-addons-for-elementor') . '</span> <span class="tmpcoder-individual-rating-cont"><span style="width: ' . $pct2 . '%"> </span></span> <span>' . $pct2 . '%</span></div><br/> ';
		$details .= '<div class="tmpcoder-individual-rating"><span>' . esc_html__('1 star', 'sastra-essential-addons-for-elementor') . '</span> <span class="tmpcoder-individual-rating-cont"><span style="width: ' . $pct1 . '%"> </span></span> <span>' . $pct1 . '%</span></div><br/> ';


		$htm = '</h2>';

		// $htm .= wc_get_rating_html($average, $count);

		$htm .= '<h2 class="woocommerce-Reviews-title">';

		return $htm . $reviews_title;
	}

    protected function render() {
    	
    	if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<h2>'. esc_html__( 'WooCommerce is NOT active!', 'sastra-essential-addons-for-elementor' ) .'</h2>';
			return;
		}

		$is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
        
        global $product;

		// Temp log out user
		if ( $is_editor ) {
			$store_current_user = wp_get_current_user()->ID;
			wp_set_current_user( 0 );

			$lastId  = tmpcoder_get_last_product_id();
			$product = wc_get_product($lastId);
		}
		else
		{
        	$product = wc_get_product();
		}

        if ( empty( $product ) ) {
            return;
        }

        setup_postdata( $product->get_id() );

		add_filter('woocommerce_reviews_title', [$this, 'change_html'], 99, 3);

		echo '<div class="tmpcoder-product-tabs">';

        wc_get_template( 'single-product/tabs/tabs.php' );
		
		echo '</div>';
    }
}