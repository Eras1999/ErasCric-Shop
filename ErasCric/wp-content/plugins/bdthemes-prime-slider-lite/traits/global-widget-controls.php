<?php

namespace PrimeSlider\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use PrimeSlider\Utils;

defined( 'ABSPATH' ) || die();

trait Global_Widget_Controls {

	/**
	 * Pagination controls
	 */
	protected function register_pagination_controls() {

		$this->start_controls_tabs( 'tabs_pagination_style' );

		$this->start_controls_tab(
			'tab_pagination_normal',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-prime-slider' ),
			]
		);

		$this->add_control(
			'pagination_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li a, {{WRAPPER}} ul.bdt-pagination li span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'pagination_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} ul.bdt-pagination li a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'     => 'pagination_border',
				'label'    => esc_html__( 'Border', 'bdthemes-prime-slider' ),
				'selector' => '{{WRAPPER}} ul.bdt-pagination li a',
			]
		);

		$this->add_responsive_control(
			'pagination_offset',
			[ 
				'label'     => esc_html__( 'Offset', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-pagination' => 'margin-top: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_space',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-pagination'     => 'margin-left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-pagination > *' => 'padding-left: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_padding',
			[ 
				'label'     => esc_html__( 'Padding', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li a' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_radius',
			[ 
				'label'     => esc_html__( 'Radius', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li a' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_arrow_size',
			[ 
				'label'     => esc_html__( 'Arrow Size', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li a svg' => 'height: {{SIZE}}px; width: auto;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'pagination_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-prime-slider' ),
				'selector' => '{{WRAPPER}} ul.bdt-pagination li a, {{WRAPPER}} ul.bdt-pagination li span',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pagination_hover',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-prime-slider' ),
			]
		);

		$this->add_control(
			'pagination_hover_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li a:hover, {{WRAPPER}} ul.bdt-pagination li a:hover span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_hover_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li a:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [ 
					'pagination_border_border!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'pagination_hover_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} ul.bdt-pagination li a:hover',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pagination_active',
			[ 
				'label' => esc_html__( 'Active', 'bdthemes-prime-slider' ),
			]
		);

		$this->add_control(
			'pagination_active_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li.bdt-active a, {{WRAPPER}} ul.bdt-pagination li.bdt-active span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_active_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li.bdt-active a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'pagination_active_background',
				'selector' => '{{WRAPPER}} ul.bdt-pagination li.bdt-active a',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
	}

	/**
	 * Query controls
	 */
	protected function register_query_controls() {

		$this->add_control(
			'post_source',
			[ 
				'label'       => _x( 'Source', 'Posts Query Control', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [ 
					''        => esc_html__( 'Show All', 'bdthemes-prime-slider' ),
					'by_name' => esc_html__( 'Manual Selection', 'bdthemes-prime-slider' ),
				],
				'label_block' => true,
			]
		);

		$this->add_control(
			'post_categories',
			[ 
				'label'       => esc_html__( 'Categories', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => prime_slider_get_category( 'category' ),
				'default'     => [],
				'label_block' => true,
				'multiple'    => true,
				'condition'   => [ 
					'post_source' => 'by_name',
				],
			]
		);

		$this->add_control(
			'limit',
			[ 
				'label'   => esc_html__( 'Limit', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3,
			]
		);

		$this->add_control(
			'orderby',
			[ 
				'label'   => esc_html__( 'Order by', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [ 
					'date'     => esc_html__( 'Date', 'bdthemes-prime-slider' ),
					'title'    => esc_html__( 'Title', 'bdthemes-prime-slider' ),
					'category' => esc_html__( 'Category', 'bdthemes-prime-slider' ),
					'rand'     => esc_html__( 'Random', 'bdthemes-prime-slider' ),
				],
			]
		);

		$this->add_control(
			'order',
			[ 
				'label'   => esc_html__( 'Order', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => [ 
					'DESC' => esc_html__( 'Descending', 'bdthemes-prime-slider' ),
					'ASC'  => esc_html__( 'Ascending', 'bdthemes-prime-slider' ),
				],
			]
		);
	}

	/**
	 * Reveal Effects controls
	 */
	protected function register_reveal_effects() {
		$this->start_controls_section(
			'section_reveal_effects',
			[ 
				'label' => esc_html__( 'Reveal Effects', 'bdthemes-prime-slider' ) . BDTPS_CORE_PC,
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'reveal_effects_enable',
			[ 
				'label'        => esc_html__( 'Reveal Effects', 'bdthemes-prime-slider' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'reveal-effects-active-',
				'classes'      => BDTPS_CORE_IS_PC,
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'reveal_effects_color',
			[ 
				'label'     => __( 'Background', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'reveal_effects_enable' => 'yes'
				]
			]
		);
		$this->add_control(
			'reveal_effects_direction',
			[ 
				'label'     => __( 'Direction', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'lr',
				'options'   => [ 
					'lr' => __( 'Left to Right', 'bdthemes-prime-slider' ),
					'rl' => __( 'Right to Left', 'bdthemes-prime-slider' ),
					'c'  => __( 'Center', 'bdthemes-prime-slider' ),
					'tb' => __( 'Top to Bottom', 'bdthemes-prime-slider' ),
					'bt' => __( 'Bottom to top', 'bdthemes-prime-slider' )
				],
				'condition' => [ 
					'reveal_effects_enable' => 'yes'
				]
			]
		);
		$this->add_control(
			'reveal_effects_easing',
			[ 
				'label'     => __( 'Easing', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'easeOutQuint',
				'options'   => [ 
					'easeOutQuad'     => esc_html__( 'Ease Out Quad', 'bdthemes-prime-slider' ),
					'easeOutCubic'    => esc_html__( 'Ease Out Cubic', 'bdthemes-prime-slider' ),
					'easeOutQuart'    => esc_html__( 'Ease Out Quart', 'bdthemes-prime-slider' ),
					'easeOutQuint'    => esc_html__( 'Ease Out Quint', 'bdthemes-prime-slider' ),
					'easeOutSine'     => esc_html__( 'Ease Out Sine', 'bdthemes-prime-slider' ),
					'easeOutExpo'     => esc_html__( 'Ease Out Expo', 'bdthemes-prime-slider' ),
					'easeOutCirc'     => esc_html__( 'Ease Out Circ', 'bdthemes-prime-slider' ),
					'easeOutBack'     => esc_html__( 'Ease Out Back', 'bdthemes-prime-slider' ),
					'easeOutBounce'   => esc_html__( 'Ease Out Bounce', 'bdthemes-prime-slider' ),
					'easeOutInQuad'   => esc_html__( 'Ease Out In Quad', 'bdthemes-prime-slider' ),
					'easeOutInCubic'  => esc_html__( 'Ease Out In Cubic', 'bdthemes-prime-slider' ),
					'easeOutInQuart'  => esc_html__( 'Ease Out In Quart', 'bdthemes-prime-slider' ),
					'easeOutInQuint'  => esc_html__( 'Ease Out In Quint', 'bdthemes-prime-slider' ),
					'easeOutInSine'   => esc_html__( 'Ease Out In Sine', 'bdthemes-prime-slider' ),
					'easeOutInExpo'   => esc_html__( 'Ease Out In Expo', 'bdthemes-prime-slider' ),
					'easeOutInCirc'   => esc_html__( 'Ease Out In Circ', 'bdthemes-prime-slider' ),
					'easeOutInBack'   => esc_html__( 'Ease Out In Back', 'bdthemes-prime-slider' ),
					'easeOutInBounce' => esc_html__( 'Ease Out In Bounce', 'bdthemes-prime-slider' ),
				],
				'condition' => [ 
					'reveal_effects_enable' => 'yes'
				]
			]
		);
		$this->add_control(
			'reveal_effects_speed',
			[ 
				'label'      => __( 'Speed', 'bdthemes-prime-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min'  => 0,
						'max'  => 5000,
						'step' => 1,
					],

				],
				'default'    => [ 
					'unit' => 'px',
					'size' => 1000,
				],
				'condition'  => [ 
					'reveal_effects_enable' => 'yes'
				]
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Background Settings
	 */
	protected function register_background_settings( $class_name ) {

		$this->add_control(
			'background_image_toggle',
			[ 
				'label'        => __( 'Background Image Settings', 'bdthemes-element-pack' ) . BDTPS_CORE_PC,
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'None', 'bdthemes-element-pack' ),
				'label_on'     => __( 'Custom', 'bdthemes-element-pack' ),
				'return_value' => 'yes',
				'classes'   => BDTPS_CORE_IS_PC
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'background_image_position',
			[ 
				'label'       => _x( 'Position', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => [ 
					''              => _x( 'Default', 'bdthemes-prime-slider' ),
					'center center' => _x( 'Center Center', 'bdthemes-prime-slider' ),
					'center left'   => _x( 'Center Left', 'bdthemes-prime-slider' ),
					'center right'  => _x( 'Center Right', 'bdthemes-prime-slider' ),
					'top center'    => _x( 'Top Center', 'bdthemes-prime-slider' ),
					'top left'      => _x( 'Top Left', 'bdthemes-prime-slider' ),
					'top right'     => _x( 'Top Right', 'bdthemes-prime-slider' ),
					'bottom center' => _x( 'Bottom Center', 'bdthemes-prime-slider' ),
					'bottom left'   => _x( 'Bottom Left', 'bdthemes-prime-slider' ),
					'bottom right'  => _x( 'Bottom Right', 'bdthemes-prime-slider' ),
				],
				'selectors'   => [ 
					'{{WRAPPER}} ' . $class_name . '' => 'background-position: {{VALUE}};',
				],
				'condition'   => [ 
					'background_image_toggle' => 'yes'
				],
				'render_type' => 'ui',
			]
		);

		// $this->add_responsive_control(
		// 	'background_image_attachment',
		// 	[
		// 		'label'   => _x('Attachment', 'bdthemes-prime-slider'),
		// 		'type'    => Controls_Manager::SELECT,
		// 		'default' => '',
		// 		'options' => [
		// 			''       => _x('Default', 'bdthemes-prime-slider'),
		// 			'scroll' => _x('Scroll', 'bdthemes-prime-slider'),
		// 			'fixed'  => _x('Fixed', 'bdthemes-prime-slider'),
		// 		],
		// 		'selectors' => [
		// 			'{{WRAPPER}} ' . $class_name . '' => 'background-attachment: {{VALUE}};',
		// 		],
		// 		'condition' => [
		// 			'background_image_toggle' => 'yes'
		// 		],
		// 		'render_type' => 'ui',
		// 	]
		// );

		$this->add_responsive_control(
			'background_image_repeat',
			[ 
				'label'       => _x( 'Repeat', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => [ 
					''          => _x( 'Default', 'bdthemes-prime-slider' ),
					'no-repeat' => _x( 'No-repeat', 'bdthemes-prime-slider' ),
					'repeat'    => _x( 'Repeat', 'bdthemes-prime-slider' ),
					'repeat-x'  => _x( 'Repeat-x', 'bdthemes-prime-slider' ),
					'repeat-y'  => _x( 'Repeat-y', 'bdthemes-prime-slider' ),
				],
				'selectors'   => [ 
					'{{WRAPPER}} ' . $class_name . '' => 'background-repeat: {{VALUE}};',
				],
				'condition'   => [ 
					'background_image_toggle' => 'yes'
				],
				'render_type' => 'ui',
			]
		);

		$this->add_responsive_control(
			'background_image_size',
			[ 
				'label'       => _x( 'Size', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => [ 
					''        => _x( 'Default', 'bdthemes-prime-slider' ),
					'auto'    => _x( 'Auto', 'bdthemes-prime-slider' ),
					'cover'   => _x( 'Cover', 'bdthemes-prime-slider' ),
					'contain' => _x( 'Contain', 'bdthemes-prime-slider' ),
					'initial' => _x( 'Custom', 'bdthemes-prime-slider' ),
				],
				'selectors'   => [ 
					'{{WRAPPER}} ' . $class_name . '' => 'background-size: {{VALUE}};',
				],
				'condition'   => [ 
					'background_image_toggle' => 'yes'
				],
				'render_type' => 'ui',
			]
		);

		$this->add_responsive_control(
			'background_image_width',
			[ 
				'label'       => _x( 'Width', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', 'em', '%', 'vw' ],
				'range'       => [ 
					'px' => [ 
						'min' => 0,
						'max' => 1000,
					],
					'%'  => [ 
						'min' => 0,
						'max' => 100,
					],
					'vw' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'default'     => [ 
					'size' => 100,
					'unit' => '%',
				],
				'required'    => true,
				'selectors'   => [ 
					'{{WRAPPER}} ' . $class_name . '' => 'background-size: {{SIZE}}{{UNIT}} auto',

				],
				'condition'   => [ 
					'background_image_size' => [ 'initial' ],
				],
				'render_type' => 'ui',
			]
		);

		$this->end_popover();
	}

	/**
	 * Social Link controls
	 */
	protected function register_social_links_controls() {
		$this->start_controls_section(
			'section_content_social_link',
			[ 
				'label'     => __( 'Social Link', 'bdthemes-prime-slider' ),
				'condition' => [ 
					'show_social_icon' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'social_link_title',
			[ 
				'label' => __( 'Title', 'bdthemes-prime-slider' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'social_link',
			[ 
				'label' => __( 'Link', 'bdthemes-prime-slider' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'social_icon',
			[ 
				'label' => __( 'Choose Icon', 'bdthemes-prime-slider' ),
				'type'  => Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'social_link_list',
			[ 
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [ 
					[ 
						'social_link'       => __( 'http://www.facebook.com/bdthemes/', 'bdthemes-prime-slider' ),
						'social_icon'       => [ 
							'value'   => 'fab fa-facebook-f',
							'library' => 'fa-brands',
						],
						'social_link_title' => 'Facebook',
					],
					[ 
						'social_link'       => __( 'http://www.twitter.com/bdthemes/', 'bdthemes-prime-slider' ),
						'social_icon'       => [ 
							'value'   => 'fab fa-twitter',
							'library' => 'fa-brands',
						],
						'social_link_title' => 'Twitter',
					],
					[ 
						'social_link'       => __( 'http://www.instagram.com/bdthemes/', 'bdthemes-prime-slider' ),
						'social_icon'       => [ 
							'value'   => 'fab fa-instagram',
							'library' => 'fa-brands',
						],
						'social_link_title' => 'Instagram',
					],
				],
				'title_field' => '{{{ social_link_title }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Social Link style controls
	 */
	protected function register_social_links_controls_style() {
		$this->start_controls_section(
			'section_style_social_icon',
			[ 
				'label'     => esc_html__( 'Social Link', 'bdthemes-prime-slider' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_social_icon' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_social_icon_style' );

		$this->start_controls_tab(
			'tab_social_icon_normal',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-prime-slider' ),
			]
		);

		$this->add_control(
			'social_icon_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-prime-slider .bdt-social-icon i'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-prime-slider .bdt-social-icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'social_icon_background',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-social-icon a:before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'social_icon_border',
				'label'       => esc_html__( 'Border', 'bdthemes-prime-slider' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-prime-slider .bdt-social-icon a',
			]
		);

		$this->add_responsive_control(
			'social_icon_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-prime-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-prime-slider .bdt-social-icon a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'social_icon_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-prime-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-prime-slider .bdt-social-icon a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'social_icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-social-icon a',
			]
		);

		$this->add_responsive_control(
			'social_icon_size',
			[ 
				'label'     => __( 'Icon Size', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-prime-slider .bdt-social-icon a' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'social_icon_spacing',
			[ 
				'label'     => esc_html__( 'Space Between', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-prime-slider .bdt-social-icon' => 'grid-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'social_icon_horizontal_offset',
			[ 
				'label'     => esc_html__( 'Horizontal Offset', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-prime-slider .bdt-social-icon' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'social_icon_tooltip',
			[ 
				'label' => esc_html__( 'Show Tooltip', 'bdthemes-prime-slider' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_social_icon_hover',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-prime-slider' ),
			]
		);

		$this->add_control(
			'social_icon_hover_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-prime-slider .bdt-social-icon a:hover i'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-prime-slider .bdt-social-icon a:hover svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'social_icon_hover_background',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-social-icon a:hover',
			]
		);

		$this->add_control(
			'icon_hover_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'social_icon_border_border!' => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-prime-slider .bdt-social-icon a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Show Price controls
	 */
	protected function register_show_price_controls() {
		$this->add_control(
			'show_price',
			[ 
				'label'   => __( 'Show Price', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
	}

	/**
	 * Show Cart controls
	 */
	protected function register_show_cart_controls() {
		$this->add_control(
			'show_cart',
			[ 
				'label'   => __( 'Add to Cart', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
	}

	/**
	 * Slider Settings Controls
	 */
	protected function register_slider_settings_controls() {
		$this->add_control(
			'autoplay',
			[ 
				'label'   => esc_html__( 'Autoplay', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_interval',
			[ 
				'label'     => esc_html__( 'Autoplay Interval (ms)', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 7000,
				'condition' => [ 
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[ 
				'label' => esc_html__( 'Pause on Hover', 'bdthemes-prime-slider' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'velocity',
			[ 
				'label' => __( 'Animation Speed', 'bdthemes-prime-slider' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [ 
					'px' => [ 
						'min'  => 0.1,
						'max'  => 1,
						'step' => 0.1,
					],
				],
			]
		);

		$this->add_control(
			'finite',
			[ 
				'label'   => esc_html__( 'Loop', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
	}

	/**
	 * Ken Burns Controls
	 */
	protected function register_ken_burns_controls() {
		$this->add_control(
			'kenburns_animation',
			[ 
				'label'     => esc_html__( 'Kenburns Animation', 'bdthemes-prime-slider' ),
				'separator' => 'before',
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'kenburns_reverse',
			[ 
				'label'     => esc_html__( 'Kenburn Reverse', 'bdthemes-prime-slider' ) . BDTPS_CORE_PC,
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [ 
					'kenburns_animation' => 'yes',
				],
			]
		);
	}

	/**
	 * Advanced Animation Controls
	 */
	protected function register_advanced_animation_controls() {

		$this->add_control(
			'animation_on',
			[ 
				'label'     => __( 'Animation On', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'words',
				'options'   => [ 
					'chars' => 'Chars',
					'words' => 'Words',
					'lines' => 'Lines',
				],
				'condition' => [ 
					'animation_status' => 'yes'
				]
			]
		);

		$this->add_control(
			'animation_options',
			[ 
				'label'        => __( 'Animation Options', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'Default', 'bdthemes-element-pack' ),
				'label_on'     => __( 'Custom', 'bdthemes-element-pack' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 
					'animation_status' => 'yes'
				]
			]
		);

		$this->start_popover();

		$this->add_control(
			'anim_perspective',
			[ 
				'label'       => esc_html__( 'Perspective', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::SLIDER,
				'placeholder' => '400',
				'range'       => [ 
					'px' => [ 
						'min' => 50,
						'max' => 400,
					],
				],
				'condition'   => [ 
					'animation_status'  => 'yes',
					'animation_options' => 'yes'
				]
			]
		);

		$this->add_control(
			'anim_duration',
			[ 
				'label'     => esc_html__( 'Transition Duration', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min'  => 0.1,
						'step' => 0.1,
						'max'  => 1,
					],
				],
				'condition' => [ 
					'animation_status'  => 'yes',
					'animation_options' => 'yes'
				]
			]
		);

		$this->add_control(
			'anim_scale',
			[ 
				'label'     => esc_html__( 'Scale', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 1,
						'max' => 10,
					],
				],
				'condition' => [ 
					'animation_status'  => 'yes',
					'animation_options' => 'yes'
				]
			]
		);

		$this->add_control(
			'anim_rotationY',
			[ 
				'label'     => esc_html__( 'rotationY', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => -360,
						'max' => 360,
					],
				],
				'condition' => [ 
					'animation_status'  => 'yes',
					'animation_options' => 'yes'
				]
			]
		);

		$this->add_control(
			'anim_rotationX',
			[ 
				'label'     => esc_html__( 'rotationX', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => -360,
						'max' => 360,
					],
				],
				'condition' => [ 
					'animation_status'  => 'yes',
					'animation_options' => 'yes'
				]
			]
		);

		$this->add_control(
			'anim_transform_origin',
			[ 
				'label'     => esc_html__( 'Transform Origin', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '0% 50% -50',
				'condition' => [ 
					'animation_status'  => 'yes',
					'animation_options' => 'yes'
				]
			]
		);

		$this->end_popover();

	}

	/**
	 * Swiper Effects controls
	 */
	protected function register_swiper_effects_controls() {
		$this->add_control(
			'swiper_effect',
			[ 
				'label'   => esc_html__( 'Swiper Effect', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [ 
					'slide'     => esc_html__( 'Slide', 'bdthemes-prime-slider' ),
					'fade'      => esc_html__( 'Fade', 'bdthemes-prime-slider' ),
					'cube'      => esc_html__( 'Cube', 'bdthemes-prime-slider' ),
					'coverflow' => esc_html__( 'Coverflow', 'bdthemes-prime-slider' ),
					'flip'      => esc_html__( 'Flip', 'bdthemes-prime-slider' ),
					'shutters'  => esc_html__( 'Shutters', 'bdthemes-prime-slider' ),
					'slicer'    => esc_html__( 'Slicer', 'bdthemes-prime-slider' ),
					'tinder'    => esc_html__( 'Tinder', 'bdthemes-prime-slider' ),
					'gl'        => esc_html__( 'GL', 'bdthemes-prime-slider' ),
					'creative'  => esc_html__( 'Creative', 'bdthemes-prime-slider' ),
				],
			]
		);
		//gl_shader control
		$this->add_control(
			'gl_shader',
			[ 
				'label'     => esc_html__( 'GL Shader', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'random',
				'options'   => [ 
					'random'         => esc_html__( 'random', 'bdthemes-prime-slider' ),
					'dots'           => esc_html__( 'dots', 'bdthemes-prime-slider' ),
					'flyeye'         => esc_html__( 'flyeye', 'bdthemes-prime-slider' ),
					'morph-x'        => esc_html__( 'morph-x', 'bdthemes-prime-slider' ),
					'morph-y'        => esc_html__( 'morph-y', 'bdthemes-prime-slider' ),
					'page-curl'      => esc_html__( 'page-curl', 'bdthemes-prime-slider' ),
					'peel-x'         => esc_html__( 'peel-x', 'bdthemes-prime-slider' ),
					'peel-y'         => esc_html__( 'peel-y', 'bdthemes-prime-slider' ),
					'polygons-fall'  => esc_html__( 'polygons-fall', 'bdthemes-prime-slider' ),
					'polygons-morph' => esc_html__( 'polygons-morph', 'bdthemes-prime-slider' ),
					'polygons-wind'  => esc_html__( 'polygons-wind', 'bdthemes-prime-slider' ),
					'pixelize'       => esc_html__( 'pixelize', 'bdthemes-prime-slider' ),
					'ripple'         => esc_html__( 'ripple', 'bdthemes-prime-slider' ),
					'shutters'       => esc_html__( 'shutters', 'bdthemes-prime-slider' ),
					'slices'         => esc_html__( 'slices', 'bdthemes-prime-slider' ),
					'squares'        => esc_html__( 'squares', 'bdthemes-prime-slider' ),
					'stretch'        => esc_html__( 'stretch', 'bdthemes-prime-slider' ),
					'wave-x'         => esc_html__( 'wave-x', 'bdthemes-prime-slider' ),
					'wind'           => esc_html__( 'wind', 'bdthemes-prime-slider' ),
				],
				'condition' => [ 
					'swiper_effect' => 'gl',
				],
			]
		);

		//creative effect control
		$this->add_control(
			'creative_effect',
			[ 
				'label'     => esc_html__( 'Creative Effect', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'creative-1',
				'options'   => [ 
					'creative-1' => esc_html__( 'Creative 1', 'bdthemes-prime-slider' ),
					'creative-2' => esc_html__( 'Creative 2', 'bdthemes-prime-slider' ),
					'creative-3' => esc_html__( 'Creative 3', 'bdthemes-prime-slider' ),
					'creative-4' => esc_html__( 'Creative 4', 'bdthemes-prime-slider' ),
					'creative-5' => esc_html__( 'Creative 5', 'bdthemes-prime-slider' ),
				],
				'condition' => [ 
					'swiper_effect' => 'creative',
				],
			]
		);
	}

	/**
	 * Show Category Controls
	 */
	protected function register_show_category_controls() {
		$this->add_control(
			'show_category',
			[ 
				'label'     => esc_html__( 'Show Category', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);
	}

	/**
	 * Slider Height controls
	 */
	protected function register_slider_height_controls() {

		$this->add_control(
			'slider_size_ratio',
			[ 
				'label'       => esc_html__( 'Size Ratio', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::IMAGE_DIMENSIONS,
				'description' => 'Slider ratio to width and height, such as 16:9',
				'separator'   => 'before',
				'condition'   => [ 
					'enable_height!' => 'yes'
				]
			]
		);

		$this->add_control(
			'slider_min_height',
			[ 
				'label'     => esc_html__( 'Minimum Height', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 50,
						'max' => 1024,
					],
				],
				'condition' => [ 
					'enable_height!' => 'yes'
				]
			]
		);

		$this->add_control(
			'enable_height',
			[ 
				'label'   => esc_html__( 'Enable Viewport Height', 'bdthemes-prime-slider' ) . BDTPS_CORE_PC,
				'type'    => Controls_Manager::SWITCHER,
				'classes' => BDTPS_CORE_IS_PC
			]
		);

		$this->add_control(
			'viewport_height',
			[ 
				'label'       => esc_html__( 'Height', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'vh' ],
				'range'       => [ 
					'vh' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'default'     => [ 
					'unit' => 'vh',
					'size' => 70,
				],
				'condition'   => [ 
					'enable_height' => 'yes'
				],
				'selectors'   => [ 
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items' => 'min-height: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'template',
			]
		);
	}

	/**
	 * Scroll Down controls
	 */
	protected function register_scroll_down_controls() {
		$this->start_controls_section(
			'section_content_scroll_button',
			[ 
				'label'     => esc_html__( 'Scroll Down', 'bdthemes-prime-slider' ),
				'condition' => [ 
					'show_scroll_button' => [ 'yes' ],
				],
			]
		);

		$this->add_control(
			'duration',
			[ 
				'label'      => esc_html__( 'Duration', 'bdthemes-prime-slider' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min'  => 100,
						'max'  => 5000,
						'step' => 50,
					],
				],
			]
		);

		$this->add_control(
			'offset',
			[ 
				'label' => esc_html__( 'Offset', 'bdthemes-prime-slider' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [ 
					'px' => [ 
						'min'  => -200,
						'max'  => 200,
						'step' => 10,
					],
				],
			]
		);

		$this->add_control(
			'scroll_button_text',
			[ 
				'label'       => esc_html__( 'Button Text', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => esc_html__( 'Scroll Down', 'bdthemes-prime-slider' ),
				'placeholder' => esc_html__( 'Scroll Down', 'bdthemes-prime-slider' ),
			]
		);

		$this->add_control(
			'section_id',
			[ 
				'label'       => esc_html__( 'Section ID', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'my-header',
				'description' => esc_html__( "By clicking this scroll button, to which section in your page you want to go? Just write that's section ID here such 'my-header'. N.B: No need to add '#'.", 'bdthemes-prime-slider' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Show Title controls
	 */
	protected function register_show_title_controls() {
		$this->add_control(
			'show_title',
			[ 
				'label'     => esc_html__( 'Show Title', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'title_html_tag',
			[ 
				'label'     => __( 'Title HTML Tag', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h1',
				'options'   => prime_slider_title_tags(),
				'condition' => [ 
					'show_title' => 'yes'
				]
			]
		);
	}

	/**
	 * Show Title & Title Tags Controls
	 */
	protected function register_show_title_and_title_tags_controls() {
		$this->add_control(
			'show_title',
			[ 
				'label'     => esc_html__( 'Show Title', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'title_tags',
			[ 
				'label'     => __( 'Title HTML Tag', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => prime_slider_title_tags(),
				'default'   => 'h3',
				'condition' => [ 
					'show_title' => 'yes',
				],
			]
		);
	}

	/**
	 * Show Sub Title controls
	 */
	protected function register_show_sub_title_controls() {
		$this->add_control(
			'show_sub_title',
			[ 
				'label'     => esc_html__( 'Show Sub Title', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'sub_title_html_tag',
			[ 
				'label'     => __( 'Sub Title HTML Tag', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h4',
				'options'   => prime_slider_title_tags(),
				'condition' => [ 
					'show_sub_title' => 'yes'
				]
			]
		);
	}

	/**
	 * Show Text controls
	 */
	protected function register_show_text_controls() {
		$this->add_control(
			'show_text',
			[ 
				'label'     => esc_html__( 'Show Text', 'bdthemes-prime-slider' ) . BDTPS_CORE_PC,
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
				'classes'   => BDTPS_CORE_IS_PC
			]
		);
	}

	/**
	 * Text Hide On controls
	 */
	protected function register_text_hide_on_controls() {
		$this->add_control(
			'text_hide_on',
			[ 
				'label'              => __( 'Text Hide On', 'bdthemes-prime-slider' ),
				'type'               => Controls_Manager::SELECT2,
				'multiple'           => true,
				'label_block'        => true,
				'options'            => [ 
					'desktop' => __( 'Desktop', 'bdthemes-prime-slider' ),
					'tablet'  => __( 'Tablet', 'bdthemes-prime-slider' ),
					'mobile'  => __( 'Mobile', 'bdthemes-prime-slider' ),
				],
				'frontend_available' => true,
				'condition'          => [ 
					'show_text' => 'yes'
				]
			]
		);
	}

	/**
	 * Show readmore controls
	 */
	protected function register_show_readmore_controls() {
		$this->add_control(
			'show_readmore',
			[ 
				'label'   => esc_html__( 'Show Read More', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
	}

	/**
	 * Show Excerpt controls
	 */
	protected function register_show_excerpt_controls() {
		$this->add_control(
			'show_excerpt',
			[ 
				'label'   => esc_html__( 'Show Text', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
	}

	/**
	 * Show Post Excerpt controls
	 */
	protected function register_show_post_excerpt_controls() {
		$this->add_control(
			'show_excerpt',
			[ 
				'label'     => __( 'Show Text', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'excerpt_length',
			[ 
				'label'       => __( 'Text Limit', 'bdthemes-prime-slider' ) . BDTPS_CORE_PC,
				'description' => esc_html__( 'It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 30,
				'condition'   => [ 
					'show_excerpt' => 'yes',
				],
				'classes'   => BDTPS_CORE_IS_PC
			]
		);

		$this->add_control(
			'strip_shortcode',
			[ 
				'label'     => esc_html__( 'Strip Shortcode', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [ 
					'show_excerpt' => 'yes',
				],
			]
		);
	}

	/**
	 * Show date & human diff time controls
	 */
	protected function register_show_date_and_human_diff_time_controls() {
		$this->add_control(
			'show_date',
			[ 
				'label'     => esc_html__( 'Show Date', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'human_diff_time',
			[ 
				'label'     => esc_html__( 'Human Different Time', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [ 
					'show_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'human_diff_time_short',
			[ 
				'label'       => esc_html__( 'Time Short Format', 'bdthemes-prime-slider' ),
				'description' => esc_html__( 'This will work for Hours, Minute and Seconds', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => [ 
					'human_diff_time' => 'yes',
					'show_date'       => 'yes'
				]
			]
		);

		$this->add_control(
			'show_time',
			[ 
				'label'     => esc_html__( 'Show Time', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [ 
					'human_diff_time' => '',
					'show_date'       => 'yes'
				]
			]
		);
	}

	/**
	 * Show Social Link controls
	 */
	protected function register_show_social_link_controls() {
		$this->add_control(
			'show_social_icon',
			[ 
				'label'   => esc_html__( 'Show Social Link', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
	}

	/**
	 * Thumbnail Size controls
	 */
	protected function register_thumbnail_size_controls() {
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[ 
				'name'      => 'thumbnail_size',
				'label'     => esc_html__( 'Image Size', 'bdthemes-prime-slider' ),
				'exclude'   => [ 'custom' ],
				'default'   => 'full',
				'separator' => 'before'
			]
		);
	}

	/**
	 * Show Scroll Button controls
	 */
	protected function register_show_scroll_button_controls() {
		$this->add_control(
			'show_scroll_button',
			[ 
				'label'     => esc_html__( 'Show Scroll Button', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);
	}

	/**
	 * Show Button Text controls
	 */
	protected function register_show_button_text_controls() {
		$this->add_control(
			'show_button_text',
			[ 
				'label'     => esc_html__( 'Show Button', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);
	}

	/**
	 * Show button & button text controls
	 */
	protected function register_show_button_and_button_text_controls() {
		$this->add_control(
			'show_button',
			[ 
				'label'     => esc_html__( 'Show Button', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'button_text',
			[ 
				'label'       => esc_html__( 'Button Text', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'More Details', 'bdthemes-prime-slider' ),
				'default'     => esc_html__( 'More Details', 'bdthemes-prime-slider' ),
				'label_block' => false,
			]
		);
	}

	/**
	 * Show Navigation controls
	 */
	protected function register_show_navigation_controls() {
		$this->add_control(
			'show_navigation_arrows',
			[ 
				'label'   => esc_html__( 'Show Navigation', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
	}

	/**
	 * Show Pagination controls
	 */
	protected function register_show_pagination_controls() {
		$this->add_control(
			'show_navigation_dots',
			[ 
				'label'   => esc_html__( 'Show Pagination', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
	}

	/**
	 * Repeater Controls start here
	 */

	/**
	 * Repeater Title Controls
	 */
	protected function register_repeater_title_controls( $repeater ) {
		$repeater->add_control(
			'title',
			[ 
				'label'       => esc_html__( 'Title', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);
	}

	/**
	 * Repeater Title Link Controls
	 */
	protected function register_repeater_title_link_controls( $repeater ) {
		$repeater->add_control(
			'title_link',
			[ 
				'label'         => esc_html__( 'Title Link', 'bdthemes-prime-slider' ),
				'type'          => Controls_Manager::URL,
				'default'       => [ 'url' => '' ],
				'show_external' => false,
				'dynamic'       => [ 'active' => true ],
				'condition'     => [ 
					'title!' => '',
				],
			]
		);
	}

	/**
	 * Repeater Sub Title Controls
	 */
	protected function register_repeater_sub_title_controls( $repeater ) {
		$repeater->add_control(
			'sub_title',
			[ 
				'label'       => esc_html__( 'Sub Title', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);
	}

	/**
	 * Repeater Text Controls
	 */
	protected function register_repeater_text_controls( $repeater ) {
		$repeater->add_control(
			'text',
			[ 
				'label'       => esc_html__( 'Text', 'bdthemes-prime-slider' ),
				'default'     => esc_html__( 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem, totam rem aperiam, eaque ipsa quae ab illo inventore et quasi architecto beatae vitae dicta sunt explicabo.', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::WYSIWYG,
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);
	}

	/**
	 * Repeater Image Controls
	 */
	protected function register_repeater_image_controls( $repeater ) {
		$repeater->add_control(
			'image',
			[ 
				'label'   => esc_html__( 'Image', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [ 
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [ 'active' => true ],
			]
		);
	}

	/**
	 * Repeater Button Text & Link Controls
	 */
	protected function register_repeater_button_text_link_controls( $repeater ) {
		$repeater->add_control(
			'slide_button_text',
			[ 
				'label'       => esc_html__( 'Button Text', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Read More', 'bdthemes-prime-slider' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'button_link',
			[ 
				'label'     => esc_html__( 'Button Link', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::URL,
				'default'   => [ 'url' => '#' ],
				'dynamic'   => [ 'active' => true ],
				'condition' => [ 
					'slide_button_text!' => '',
				],
			]
		);
	}

	/**
	 * Repeater Excerpt Controls
	 */
	protected function register_repeater_excerpt_controls( $repeater ) {
		$repeater->add_control(
			'excerpt',
			[ 
				'label'       => esc_html__( 'Excerpt', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => esc_html__( 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem, totam rem aperiam, eaque ipsa quae ab illo inventore et quasi architecto beatae vitae dicta sunt explicabo.', 'bdthemes-prime-slider' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);
	}

	/**
	 * Autoplay controls
	 */
	protected function register_autoplay_controls() {
		$this->add_control(
			'autoplay',
			[ 
				'label'   => __( 'Autoplay', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[ 
				'label'     => esc_html__( 'Autoplay Speed', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [ 
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pauseonhover',
			[ 
				'label' => esc_html__( 'Pause on Hover', 'bdthemes-prime-slider' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);
	}

	/**
	 * Grab Cursor controls
	 */
	protected function register_grab_cursor_controls() {
		$this->add_control(
			'grab_cursor',
			[ 
				'label' => __( 'Grab Cursor', 'bdthemes-prime-slider' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);
	}

	/**
	 * Centered Slides controls
	 */
	protected function register_centered_slides_controls() {
		$this->add_control(
			'centered_slides',
			[ 
				'label'       => __( 'Center Slide', 'bdthemes-prime-slider' ),
				'description' => __( 'Use even items from Layout > Columns settings for better preview.', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::SWITCHER,
			]
		);
	}

	/**
	 * Show Author controls
	 */
	protected function register_show_author_controls() {
		$this->add_control(
			'show_author',
			[ 
				'label'     => esc_html__( 'Show Author', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);
	}

	/**
	 * Meta Separator controls
	 */
	protected function register_meta_separator_controls() {
		$this->add_control(
			'meta_separator',
			[ 
				'label'       => __( 'Separator', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '//',
				'label_block' => false,
			]
		);
	}

	/**
	 * Primary Thumbnail controls
	 */
	protected function register_primary_thumbnail_controls() {
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[ 
				'name'      => 'primary_thumbnail',
				'exclude'   => [ 'custom' ],
				'default'   => 'full',
				'separator' => 'before'
			]
		);
	}

	/**
	 * Free Mode controls
	 */
	protected function register_free_mode_controls() {
		$this->add_control(
			'free_mode',
			[ 
				'label' => __( 'Drag free Mode', 'bdthemes-prime-slider' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);
	}

	/**
	 * Speed & Observer controls
	 */
	protected function register_speed_observer_controls() {
		$this->add_control(
			'speed',
			[ 
				'label'   => __( 'Animation Speed (ms)', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [ 
					'size' => 900,
				],
				'range'   => [ 
					'px' => [ 
						'min'  => 100,
						'max'  => 5000,
						'step' => 50,
					],
				],
			]
		);

		$this->add_control(
			'observer',
			[ 
				'label'       => __( 'Observer', 'bdthemes-prime-slider' ),
				'description' => __( 'When you use carousel in any hidden place (in tabs, accordion etc) keep it yes.', 'bdthemes-prime-slider' ),
				'type'        => Controls_Manager::SWITCHER,
			]
		);
	}

	/**
	 * loop controls
	 */
	protected function register_loop_controls() {
		$this->add_control(
			'loop',
			[ 
				'label'   => __( 'Loop', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
	}

	/**
	 * Loop, Rewind & Mousewheel controls
	 */
	protected function register_loop_rewind_mousewheel_controls() {
		$this->add_control(
			'loop',
			[ 
				'label'     => __( 'Loop', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [ 
					'swiper_effect!' => [ 'slicer', 'tinder' ]
				],
			]
		);

		$this->add_control(
			'rewind',
			[ 
				'label'     => __( 'Rewind', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [ 
					'swiper_effect' => [ 'slicer', 'tinder' ]
				],
			]
		);

		$this->add_control(
			'mousewheel',
			[ 
				'label' => __( 'Mousewheel', 'bdthemes-prime-slider' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);
	}

	/**
	 * Social links text controls
	 */
	protected function register_social_links_text_controls() {
		$this->start_controls_section(
			'section_content_social_link',
			[ 
				'label'     => __( 'Social Share', 'bdthemes-prime-slider' ),
				'condition' => [ 
					'show_social_icon' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'social_link_title',
			[ 
				'label'   => __( 'Title', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Facebook',
			]
		);

		$repeater->add_control(
			'social_link',
			[ 
				'label'   => __( 'Link', 'bdthemes-prime-slider' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'http://www.facebook.com/bdthemes/', 'bdthemes-prime-slider' ),
			]
		);

		$this->add_control(
			'social_link_list',
			[ 
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [ 
					[ 
						'social_link'       => __( 'http://www.facebook.com/bdthemes/', 'bdthemes-prime-slider' ),
						'social_link_title' => 'Facebook',
					],
					[ 
						'social_link'       => __( 'http://www.twitter.com/bdthemes/', 'bdthemes-prime-slider' ),
						'social_link_title' => 'Twitter',
					],
					[ 
						'social_link'       => __( 'http://www.instagram.com/bdthemes/', 'bdthemes-prime-slider' ),
						'social_link_title' => 'Instagram',
					],
				],
				'title_field' => '{{{ social_link_title }}}',
			]
		);

		$this->end_controls_section();
	}





	/**
	 * Render part start from here
	 */
	/**
	 * Reveal Effect
	 */
	public function reveal_effects_attr( $attribute_name ) {
		$settings = $this->get_settings_for_display();

		$reveal_effects = prime_slider_option( 'reveal-effects', 'prime_slider_other_settings', 'off' );

		//Reveal Effect
		if ( ( 'on' === $reveal_effects ) && ( 'yes' === $settings['reveal_effects_enable'] ) ) {
			$this->add_render_attribute( 'prime-slider', 'class', 'reveal-active-' . $this->get_id() );
			$this->add_render_attribute( $attribute_name, 'data-reveal-enable', $settings['reveal_effects_enable'] );
			$this->add_render_attribute(
				[ 
					$attribute_name => [ 
						'data-reveal-settings' => [ 
							wp_json_encode( [ 
								"bgColors"  => $settings["reveal_effects_color"] ? $settings["reveal_effects_color"] : "#333",
								"direction" => $settings['reveal_effects_direction'] ? $settings['reveal_effects_direction'] : 'lr',
								"duration"  => $settings['reveal_effects_speed']['size'] ? $settings['reveal_effects_speed']['size'] : 1000,
								"easing"    => $settings['reveal_effects_easing']
							] )
						],
					]
				]
			);
		}
	}


	/**
	 * Advanced Animation
	 */
	public function adv_anim( $attribute_name ) {
		$settings = $this->get_settings_for_display();

		$animation_of = ( isset( $settings['animation_of'] ) ) ? implode( ", ", $settings['animation_of'] ) : '.bdt-image-expand-sub-title';

		$animation_of = ( strlen( $animation_of ) ) > 0 ? $animation_of : '.bdt-image-expand-sub-title';

		if ( true === _is_ps_pro_activated() ) {
			$animation_status = ( $settings['animation_status'] == 'yes' ? 'yes' : 'no' );
		} else {
			$animation_status = 'no';
		}

		if ( $animation_status == 'yes' ) {
			$this->add_render_attribute(
				[ 
					$attribute_name => [ 
						'data-settings' => [ 
							wp_json_encode( [ 
								'id'                    => '#bdt-' . $this->get_id(),
								'animation_status'      => $animation_status,
								'animation_of'          => $animation_of,
								'animation_on'          => $settings['animation_on'],
								'anim_perspective'      => ( $settings['anim_perspective']['size'] ) ? $settings['anim_perspective']['size'] : 400,
								'anim_duration'         => ( $settings['anim_duration']['size'] ) ? $settings['anim_duration']['size'] : 0.1,
								'anim_scale'            => ( $settings['anim_scale']['size'] ) ? $settings['anim_scale']['size'] : 0,
								'anim_rotation_y'       => ( $settings['anim_rotationY']['size'] ) ? $settings['anim_rotationY']['size'] : 80,
								'anim_rotation_x'       => ( $settings['anim_rotationX']['size'] ) ? $settings['anim_rotationX']['size'] : 180,
								'anim_transform_origin' => ( $settings['anim_transform_origin'] ) ? $settings['anim_transform_origin'] : '0% 50% -50',
							] )
						]
					]
				]
			);
		} else {
			$this->add_render_attribute(
				[ 
					$attribute_name => [ 
						'data-settings' => [ 
							wp_json_encode( [ 
								'id'               => '#bdt-' . $this->get_id(),
								'animation_status' => $animation_status,
							] )
						]
					]
				]
			);
		}
	}

	/**
	 * Slideshow Settings
	 */
	public function render_slideshows_settings( $min_height ) {
		$settings = $this->get_settings_for_display();

		//Viewport Height
		$ratio = ( ! empty( $settings['slider_size_ratio']['width'] ) && ! empty( $settings['slider_size_ratio']['height'] ) ) ? $settings['slider_size_ratio']['width'] . ":" . $settings['slider_size_ratio']['height'] : '16:9';

		if ( isset( $settings["viewport_height"]["size"] ) && 'vh' == $settings['viewport_height']['unit'] ) {
			$ratio = false;
		}

		$this->add_render_attribute( 'slideshow-items', 'class', 'bdt-slideshow-items' );

		if ( isset( $settings["viewport_height"]["size"] ) && $ratio == false ) {
			$this->add_render_attribute(
				[ 
					'slideshow-items' => [ 
						'style' => 'min-height:' . $settings["viewport_height"]["size"] . 'vh'
					]
				]
			);
		}

		$this->add_render_attribute(
			[ 
				'slideshow' => [ 
					'bdt-slideshow' => [ 
						wp_json_encode( [ 
							'animation'         => 'fade',
							'ratio'             => $ratio,
							'min-height'        => ( ! empty( $settings['slider_min_height']['size'] ) && $ratio !== false ) ? $settings['slider_min_height']['size'] : ( $ratio !== false ? $min_height : false ),
							'autoplay'          => ( $settings['autoplay'] ) ? true : false,
							'autoplay-interval' => $settings['autoplay_interval'],
							'pause-on-hover'    => ( 'yes' === $settings['pause_on_hover'] ) ? true : false,
							'velocity'          => ( $settings['velocity']['size'] ) ? $settings['velocity']['size'] : 1,
							'finite'            => ( $settings['finite'] ) ? false : true,
						] ),
					],
				],
			]
		);

		?>
		<div class="bdt-prime-slider">
			<div <?php $this->print_render_attribute_string( 'prime-slider' ); ?>>
				<div class="bdt-position-relative bdt-visible-toggle" <?php $this->print_render_attribute_string( 'slideshow' ); ?>>
					<ul <?php $this->print_render_attribute_string( 'slideshow-items' ); ?>>
						<?php
	}

	/**
	 * Sub Title Text Here
	 */
	public function render_sub_title( $slide, $class, $data_reveal ) {
		$settings = $this->get_settings_for_display();

		if ( '' == $settings['show_sub_title'] ) {
			return;
		}

		printf(
			'<%1$s class="%2$s" data-reveal="%3$s">%4$s</%1$s>',
			esc_attr( Utils::get_valid_html_tag( $settings['sub_title_html_tag'] ) ),
			esc_attr( $class ), esc_attr( $data_reveal ),
			wp_kses_post( $slide['sub_title'] ),
			prime_slider_allow_tags( 'title' )
		);
	}

	/**
	 * Title Text Here
	 */
	public function render_title( $slide, $class, $data_reveal ) {
		$settings = $this->get_settings_for_display();

		if ( '' == $settings['show_title'] ) {
			return;
		}

		?>
						<<?php echo esc_attr( Utils::get_valid_html_tag( $settings['title_html_tag'] ) ); ?> class="
							<?php echo esc_attr( $class ); ?>" data-reveal="<?php echo esc_attr( $data_reveal ); ?>">
							<?php if ( '' !== $slide['title_link']['url'] ) : ?>
								<a href="<?php echo esc_url( $slide['title_link']['url'] ); ?>">
								<?php endif; ?>
								<?php echo wp_kses_post( prime_slider_first_word( $slide['title'] ) ); ?>
								<?php if ( '' !== $slide['title_link']['url'] ) : ?>
								</a>
							<?php endif; ?>
						</<?php echo esc_attr( Utils::get_valid_html_tag( $settings['title_html_tag'] ) ); ?>>
						<?php
	}

	/**
	 * Post Title Here
	 */
	public function render_post_title() {
		$settings = $this->get_settings_for_display();

		if ( ! $this->get_settings( 'show_title' ) ) {
			return;
		}

		printf(
			'<%1$s class="bdt-title" data-reveal="reveal-active">
				<a href="%2$s" title="%3$s">%4$s</a>
			</%1$s>',
			esc_attr( Utils::get_valid_html_tag( $settings['title_tags'] ) ),
			esc_url( get_permalink() ),
			esc_attr( get_the_title() ),
			esc_html( get_the_title() )
		);
	}

	/**
	 * Background image
	 */
	public function rendar_item_image( $item, $class ) {
		$settings = $this->get_settings_for_display();

		$image_src = Group_Control_Image_Size::get_attachment_image_src( $item['image']['id'], 'thumbnail_size', $settings );

		if ( $image_src ) {
			$image_final_src = $image_src;
		} elseif ( $item['image']['url'] ) {
			$image_final_src = $item['image']['url'];
		} else {
			return;
		}
		?>

						<div class="<?php echo esc_attr( $class ); ?>"
							style="background-image: url('<?php echo esc_url( $image_final_src ); ?>')"></div>

						<?php
	}

	/**
	 * Image
	 */
	public function rendar_image( $slide, $reveal ) {
		$settings = $this->get_settings_for_display();

		$gl       = $settings['swiper_effect'] == 'gl' ? ' swiper-gl-image' : '';
		$shutters = $settings['swiper_effect'] == 'shutters' ? ' swiper-shutters-image' : '';
		$slicer   = $settings['swiper_effect'] == 'slicer' ? ' swiper-slicer-image' : '';
		?>

						<div class="bdt-image-wrap" <?php echo esc_attr( $reveal ) ?>>
							<?php
							$thumb_url = Group_Control_Image_Size::get_attachment_image_src( $slide['image']['id'], 'thumbnail_size', $settings );
							if ( ! $thumb_url ) {
								printf( '<img src="%1$s" alt="%2$s" class="bdt-img %3$s">', esc_url( $slide['image']['url'] ), esc_html( $slide['title'] ), esc_attr( $gl . $shutters . $slicer ) );
							} else {
								print( wp_get_attachment_image(
									$slide['image']['id'],
									$settings['thumbnail_size_size'],
									false,
									[ 
										'class' => 'bdt-img' . $gl . $shutters . $slicer,
										'alt'   => esc_html( $slide['title'] )
									]
								) );
							}
							?>
						</div>

						<?php
	}

	/**
	 * Social Icons Here
	 */
	public function render_social_link( $position = 'right', $label = false, $class = [] ) {
		$settings = $this->get_active_settings();

		if ( '' == $settings['show_social_icon'] ) {
			return;
		}

		$this->add_render_attribute( 'social-icon', 'class', 'bdt-social-icon reveal-muted' );
		$this->add_render_attribute( 'social-icon', 'class', $class );

		?>
						<div <?php $this->print_render_attribute_string( 'social-icon' ); ?>>

							<?php if ( $label ) : ?>
								<h3>
									<?php esc_html_e( 'Follow Us', 'bdthemes-prime-slider' ); ?>
								</h3>
							<?php endif; ?>

							<?php foreach ( $settings['social_link_list'] as $link ) :
								$tooltip = ( 'yes' == $settings['social_icon_tooltip'] ) ? ' title="' . esc_attr( $link['social_link_title'] ) . '" bdt-tooltip="pos: ' . $position . '"' : ''; ?>

								<a href="<?php echo esc_url( $link['social_link'] ); ?>" target="_blank" <?php echo wp_kses_post( $tooltip ); ?>>
									<span><span>
											<?php Icons_Manager::render_icon( $link['social_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] ); ?>
										</span></span>
								</a>
							<?php endforeach; ?>
						</div>
						<?php
	}

	public function rendar_post_image( $class ) {
		$settings = $this->get_settings_for_display();

		$placeholder_image_src = Utils::get_placeholder_image_src();
		$image_src             = Group_Control_Image_Size::get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail_size', $settings );

		if ( $image_src ) {
			$image_final_src = $image_src;
		} elseif ( $placeholder_image_src ) {
			$image_final_src = $placeholder_image_src;
		} else {
			return;
		}

		?>

						<div class="<?php echo esc_attr( $class ); ?>"
							style="background-image: url('<?php echo esc_url( $image_final_src ); ?>')"></div>

						<?php
	}

	public function render_image( $post_id, $size ) {
		$placeholder_image_src = Utils::get_placeholder_image_src();
		$image_src             = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );

		if ( ! $image_src ) {
			printf( '<img src="%1$s" alt="%2$s" class="bdt-img swiper-lazy">', esc_url( $placeholder_image_src ), esc_html( get_the_title() ) );
		} else {
			print( wp_get_attachment_image(
				get_post_thumbnail_id(),
				$size,
				false,
				[ 
					'class' => 'bdt-img swiper-lazy',
					'alt'   => esc_html( get_the_title() )
				]
			) );
		}
	}




}
