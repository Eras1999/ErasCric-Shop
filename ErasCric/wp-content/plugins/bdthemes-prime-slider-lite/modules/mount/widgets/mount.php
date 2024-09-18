<?php

namespace PrimeSlider\Modules\Mount\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use PrimeSlider\Utils;
use Elementor\Repeater;

use PrimeSlider\Traits\Global_Widget_Controls;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Mount extends Widget_Base {

	use Global_Widget_Controls;

	public function get_name() {
		return 'prime-slider-mount';
	}

	public function get_title() {
		return BDTPS . esc_html__('Mount', 'bdthemes-prime-slider');
	}

	public function get_icon() {
		return 'bdt-widget-icon ps-wi-mount';
	}

	public function get_categories() {
		return ['prime-slider'];
	}

	public function get_keywords() {
		return [ 'prime slider', 'slider', 'mount', 'prime' ];
	}

	public function get_style_depends() {
		return ['ps-mount'];
	}

	public function get_script_depends() {
		$reveal_effects = prime_slider_option('reveal-effects', 'prime_slider_other_settings', 'off');
		if ('on' === $reveal_effects) {
			if ( true === _is_ps_pro_activated() ) {
				return ['gsap', 'split-text', 'anime', 'revealFx', 'ps-mount'];
			} else {
				return [];
			}
		} else {
			if ( true === _is_ps_pro_activated() ) {
				return ['gsap', 'split-text', 'ps-mount'];
			} else {
				return [];
			}
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/DGIlfM61T0E';
	}

	protected function register_controls() {
		$reveal_effects = prime_slider_option('reveal-effects', 'prime_slider_other_settings', 'off');

		$this->start_controls_section(
			'section_content_sliders',
			[
				'label' => esc_html__('Sliders', 'bdthemes-prime-slider'),
			]
		);

		$repeater = new Repeater();

		/**
         * Repeater Sub Title Controls
         */
        $this->register_repeater_sub_title_controls($repeater);

		/**
         * Repeater Title Controls
         */
        $this->register_repeater_title_controls($repeater);

		/**
         * Repeater Title Link Controls
         */
        $this->register_repeater_title_link_controls($repeater);

		/**
         * Repeater Image Controls
         */
        $this->register_repeater_image_controls($repeater);

		$this->add_control(
			'slides',
			[
				'label'   => esc_html__('Slider Items', 'bdthemes-prime-slider'),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[
						'sub_title' => esc_html__('This is a Label', 'bdthemes-prime-slider'),
						'title'     => esc_html__('Mount Slider Item One', 'bdthemes-prime-slider'),
						'image'     => ['url' => BDTPS_CORE_ASSETS_URL . 'images/gallery/img-1.svg']
					],
					[
						'sub_title' => esc_html__('This is a Label', 'bdthemes-prime-slider'),
						'title'     => esc_html__('Mount Slider Item Two', 'bdthemes-prime-slider'),
						'image'     => ['url' => BDTPS_CORE_ASSETS_URL . 'images/gallery/img-2.svg']
					],
					[
						'sub_title' => esc_html__('This is a Label', 'bdthemes-prime-slider'),
						'title'     => esc_html__('Mount Slider Item Three', 'bdthemes-prime-slider'),
						'image'     => ['url' => BDTPS_CORE_ASSETS_URL . 'images/gallery/img-3.svg']
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Additional Options', 'bdthemes-prime-slider'),
			]
		);

		/**
         * Slider Height Controls
         */
        $this->register_slider_height_controls();

		/**
		* Show Title Controls
		*/
		$this->register_show_title_controls();

		/**
		* Show Sub Title Controls
		*/
		$this->register_show_sub_title_controls();

		$this->add_control(
		'show_social_share',
			[
				'label'   => esc_html__('Show Social Share', 'bdthemes-prime-slider'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		/**
		* Show Navigation Controls
		*/
		$this->register_show_navigation_controls();

		$this->add_control(
			'show_navigation_dots',
			[
				'label'   => esc_html__('Show Pagination', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'classes'    => BDTPS_CORE_IS_PC
			]
		);

		$this->add_responsive_control(
            'content_alignment',
            [
                'label'   => esc_html__( 'Alignment', 'bdthemes-prime-slider' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'bdthemes-prime-slider' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'bdthemes-prime-slider' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'bdthemes-prime-slider' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content *' => 'text-align: {{VALUE}} !important;',
                ],
            ]
        );

		/**
		* Thumbnail Size Controls
		*/
		$this->register_thumbnail_size_controls();

		//Global background settings Controls
        $this->register_background_settings('.bdt-prime-slider .bdt-slideshow-item .bdt-ps-slide-img');

		$this->end_controls_section();

		

		$this->start_controls_section(
			'section_content_social_link',
			[
				'label' 	=> __('Social Share', 'bdthemes-prime-slider'),
				'condition' => [
					'show_social_share' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'social_link_title',
			[
				'label'   => __('Title', 'bdthemes-prime-slider'),
				'type'    => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'social_link',
			[
				'label'   => __('Link', 'bdthemes-prime-slider'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('http://www.facebook.com/bdthemes/', 'bdthemes-prime-slider'),
			]
		);

		$this->add_control(
			'social_link_list',
			[
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[
						'social_link'       => __('http://www.facebook.com/bdthemes/', 'bdthemes-prime-slider'),
						'social_link_title' => 'Fb',
					],
					[
						'social_link'       => __('http://www.twitter.com/bdthemes/', 'bdthemes-prime-slider'),
						'social_link_title' => 'Tw',
					],
					[
						'social_link'       => __('http://www.instagram.com/bdthemes/', 'bdthemes-prime-slider'),
						'social_link_title' => 'Ig',
					],
				],
				'title_field' => '{{{ social_link_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_animation',
			[
				'label' => esc_html__('Slider Settings', 'bdthemes-prime-slider'),
			]
		);

		/**
         * Slider Settings Controls
         */
        $this->register_slider_settings_controls();

		$this->end_controls_section();

		/**
         * Advanced Animation
         */
		$this->start_controls_section(
			'section_advanced_animation',
			[
				'label'     => esc_html__('Advanced Animation', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
				'tab'       => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'animation_status',
			[
				'label'   => esc_html__('Advanced Animation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'classes'   => BDTPS_CORE_IS_PC,
			]
		);

		if ( true === _is_ps_pro_activated() ) {

			$this->add_control(
				'animation_of',
				[
					'label'	   => __('Animation Of', 'bdthemes-element-pack'),
					'type' 	   => Controls_Manager::SELECT2,
					'multiple' => true,
					'options'  => [
						'.bdt-sub-title-inner' => __('Sub Title', 'bdthemes-element-pack'),
						'.bdt-title-tag' => __('Title', 'bdthemes-element-pack'),
					],
					'default'  => ['.bdt-title-tag'],
					'condition' => [
						'animation_status' => 'yes'
					]
				]
			);

			/**
             * Advanced Animation
             */
            $this->register_advanced_animation_controls();
		}

		$this->end_controls_section();

		/**
		 * Reveal Effects
		 */
		if ('on' === $reveal_effects) {
			$this->register_reveal_effects();
		}

		//Style Start
		$this->start_controls_section(
			'section_style_sliders',
			[
				'label'     => esc_html__('Sliders', 'bdthemes-prime-slider'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'overlay',
			[
				'label'   => esc_html__('Overlay', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'       => esc_html__('None', 'bdthemes-prime-slider'),
					'background' => esc_html__('Background', 'bdthemes-prime-slider'),
					'blend'      => esc_html__('Blend', 'bdthemes-prime-slider'),
				],
				'separator' => 'before',
				'classes'    => BDTPS_CORE_IS_PC
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label'     => esc_html__('Overlay Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'overlay' => ['background', 'blend']
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-overlay-default' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'blend_type',
			[
				'label'     => esc_html__('Blend Type', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'multiply',
				'options'   => prime_slider_blend_options(),
				'condition' => [
					'overlay' => 'blend',
				],
			]
		);

		$this->add_responsive_control(
			'content_inner_padding',
			[
				'label'      => esc_html__('Content Padding', 'bdthemes-prime-slider'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider-mount .bdt-prime-slider-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before'
			]
		);

		$this->start_controls_tabs('slider_item_style');

		$this->start_controls_tab(
			'slider_title_style',
			[
				'label' 	=> __('Title', 'bdthemes-prime-slider'),
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_responsive_control(
			'title_width',
			[
				'label' => esc_html__('Title Width', 'bdthemes-prime-slider'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 220,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .bdt-title-tag' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-prime-slider'),
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .bdt-title-tag',
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .bdt-title-tag',
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_responsive_control(
			'prime_slider_title_spacing',
			[
				'label' => esc_html__('Title Spacing', 'bdthemes-prime-slider'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'title_advanced_style',
			[
				'label' => esc_html__('Advanced Style', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
				'type'  => Controls_Manager::SWITCHER,
				'classes'    => BDTPS_CORE_IS_PC
			]
		);

		$this->add_control(
            'first_word_title_color',
            [
                'label'     => esc_html__('First Word Color', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .bdt-title-tag .frist-word' => 'color: {{VALUE}};',
                ],
                'condition' => [
					'title_advanced_style' => 'yes'
				]
            ]
        );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'title_background',
				'label' => __( 'Background', 'bdthemes-prime-slider'),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .bdt-title-tag',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => __( 'Text Shadow', 'bdthemes-prime-slider'),
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .bdt-title-tag',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 	   => 'title_border',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .bdt-title-tag',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_control(
			'title_border_radius',
			[
				'label'		 => __('Border Radius', 'bdthemes-prime-slider'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .bdt-title-tag' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 	   => 'title_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .bdt-title-tag',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'title_text_padding',
			[
				'label' 	 => __('Padding', 'bdthemes-prime-slider'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .bdt-title-tag' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'slider_sub_title_style',
			[
				'label' 	=> __('Sub Title', 'bdthemes-prime-slider'),
				'condition' => [
					'show_sub_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'sub_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-sub-title-inner' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-sub-title-inner',
			]
		);

		$this->add_responsive_control(
			'prime_slider_sub_title_spacing',
			[
				'label' => esc_html__('Sub Title Spacing', 'bdthemes-prime-slider'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-sub-title .bdt-sub-title-inner' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_sub_title' => ['yes'],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_social_icon',
			[
				'label'     => esc_html__('Social Share', 'bdthemes-prime-slider'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_social_share' => 'yes',
				],
			]
		);

		$this->add_control(
			'social_icon_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-mount .bdt-prime-slider-social-icon h3' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'social_icon_text_typography',
				'selector' => '{{WRAPPER}} .bdt-prime-slider-mount .bdt-prime-slider-social-icon h3',
			]
		);

		$this->start_controls_tabs('tabs_social_icon_style');

		$this->start_controls_tab(
			'tab_social_icon_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-prime-slider'),
			]
		);

		$this->add_control(
			'social_icon_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'social_icon_background',
				'selector'  => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'social_icon_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a',
			]
		);

		$this->add_responsive_control(
			'social_icon_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-prime-slider'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'social_icon_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-prime-slider'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'social_icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a',
			]
		);

		$this->add_responsive_control(
			'social_icon_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-prime-slider'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> '_icon',
				'selector' 	=> '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_social_icon_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-prime-slider'),
			]
		);

		$this->add_control(
			'social_icon_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'social_icon_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a:hover',
			]
		);

		$this->add_control(
			'icon_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'social_icon_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'     => __('Navigation', 'bdthemes-prime-slider'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => __('Arrows Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous svg, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next:before, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous:before' => 'background: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_arrows' => ['yes'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => __('Arrows Hover Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous:hover svg, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next:hover svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next:before, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous:before' => 'background: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_arrows' => ['yes'],
				],
			]
		);
		
		$this->add_control(
			'pagination_heading',
			[
				'label'     => __('Pagination', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
				'separator' => 'before'
			]
		);
		
		$this->add_control(
			'active_dot_number_color',
			[
				'label'     => __('Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-mount .bdt-ps-dotnav li a, {{WRAPPER}} .bdt-prime-slider-mount .bdt-ps-dotnav span' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-prime-slider-mount .bdt-ps-dotnav span:before' => 'background: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'dots_size',
				'label'     => __('Typography', 'bdthemes-prime-slider'),
				'selector' 	=> '{{WRAPPER}} .bdt-prime-slider-mount .bdt-ps-dotnav li a, {{WRAPPER}} .bdt-prime-slider-mount .bdt-ps-dotnav span',
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
			]
		);

		$this->end_controls_section();

	}

	public function render_header() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('prime-slider', 'class', 'bdt-prime-slider-mount');

		
		/**
         * Advanced Animation
         */
		$this->adv_anim('slideshow');
		$this->add_render_attribute('slideshow', 'id', 'bdt-' . $this->get_id());

		/**
		 * Reveal Effects
		 */
		$this->reveal_effects_attr('slideshow');

		/**
         * Slideshow Settings
         */
        $this->render_slideshows_settings('460');
	}

	public function render_navigation_arrows() {
		$settings = $this->get_settings_for_display();

		?>

		<?php if ($settings['show_navigation_arrows']) : ?>
		<div class="bdt-navigation-arrows reveal-muted">
			<a class="bdt-prime-slider-previous" href="#" bdt-slidenav-previous bdt-slideshow-item="previous"></a>
			<a class="bdt-prime-slider-next" href="#" bdt-slidenav-next bdt-slideshow-item="next"></a>
		</div>
		<?php endif; ?>

		<?php
	}

    public function render_navigation_dots() {
        $settings = $this->get_settings_for_display();

        ?>

        <?php if ($settings['show_navigation_dots']) : ?>

            <ul class="bdt-ps-dotnav reveal-muted">
                <?php $slide_index = 1; foreach ( $settings['slides'] as $slide ) : ?>
                    <li bdt-slideshow-item="<?php echo esc_attr($slide_index - 1); ?>" data-label="<?php echo esc_attr(str_pad( $slide_index, 2, '0', STR_PAD_LEFT)); ?>" ><a href="#"><?php echo esc_attr(str_pad( $slide_index, 2, '0', STR_PAD_LEFT)); ?></a></li>
                <?php $slide_index++;  endforeach; ?>

                <span><?php echo esc_attr(str_pad( $slide_index - 1, 2, '0', STR_PAD_LEFT)); ?></span>
            </ul>

        <?php endif; ?>

        <?php
    }

    public function render_footer() {

        ?>

                </ul>

				<?php $this->render_navigation_dots(); ?>
				<?php $this->render_navigation_arrows(); ?>
				
            </div>
			<?php $this->render_social_link(); ?>
		</div>
		</div>
        <?php
	}

	public function render_social_link($class = []) {
		$settings  = $this->get_active_settings();

		if ('' == $settings['show_social_share']) {
			return;
		}

		$this->add_render_attribute('social-icon', 'class', 'bdt-prime-slider-social-icon reveal-muted');
		$this->add_render_attribute('social-icon', 'class', $class);

		?>

			<div <?php $this->print_render_attribute_string('social-icon'); ?>>

				<h3><?php echo esc_html('Follow Us', 'bdthemes-prime-slider') ?></h3>

				<?php foreach ($settings['social_link_list'] as $link) : ?>

					<a href="<?php echo esc_url($link['social_link']); ?>" target="_blank">
						<span class="bdt-social-share-title">
							<?php echo esc_html($link['social_link_title']); ?>
						</span>
					</a>
					
				<?php endforeach; ?>

			</div>

		<?php
	}
	
	public function render_item_content($slide_content) {
        $settings = $this->get_settings_for_display();

        ?>
			<div class="bdt-prime-slider-content">

				<?php if ($slide_content['sub_title'] && ('yes' == $settings['show_sub_title'])) : ?>
					<div class="bdt-sub-title">
						<<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?> class="bdt-sub-title-inner" data-reveal="reveal-active">
							<?php echo wp_kses_post($slide_content['sub_title']); ?>
						</<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?>>
					</div>
				<?php endif; ?>

				<?php if ($slide_content['title'] && ('yes' == $settings['show_title'])) : ?>
					<div class="bdt-main-title">
						<<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?> class="bdt-title-tag" data-reveal="reveal-active">
							<?php if ('' !== $slide_content['title_link']['url']) : ?>
								<a href="<?php echo esc_url($slide_content['title_link']['url']); ?>">
								<?php endif; ?>
								<?php echo wp_kses_post(prime_slider_first_word($slide_content['title'])); ?>
								<?php if ('' !== $slide_content['title_link']['url']) : ?>
								</a>
							<?php endif; ?>
						</<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?>>
					</div>
				<?php endif; ?>

			</div>

        <?php
    }

    public function render_slides_loop() {
        $settings = $this->get_settings_for_display();

        foreach ($settings['slides'] as $slide) : ?>

            <li class="bdt-slideshow-item bdt-flex bdt-flex-middle elementor-repeater-item-<?php echo esc_attr($slide['_id']); ?>">

				<?php $this->rendar_item_image($slide, "bdt-ps-slide-img") ?>

				<?php if ('none' !== $settings['overlay']) :
					$blend_type = ('blend' == $settings['overlay']) ? ' bdt-blend-' . $settings['blend_type'] : ''; ?>
					<div class="bdt-overlay-default bdt-position-cover<?php echo esc_attr($blend_type); ?>"></div>
				<?php endif; ?>
				
				<?php $this->render_item_content($slide); ?>

            </li>

        <?php endforeach;
    }

    public function render() {
        $this->render_header();
        $this->render_slides_loop();
        $this->render_footer();
    }
}