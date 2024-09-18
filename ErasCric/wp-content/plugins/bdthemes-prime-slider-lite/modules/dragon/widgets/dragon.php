<?php

namespace PrimeSlider\Modules\Dragon\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Stroke;
use PrimeSlider\Utils;
use Elementor\Repeater;

use PrimeSlider\Traits\Global_Widget_Controls;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class dragon extends Widget_Base {

	use Global_Widget_Controls;
	
	public function get_name() {
		return 'prime-slider-dragon';
	}

	public function get_title() {
		return BDTPS . esc_html__('Dragon', 'bdthemes-prime-slider');
	}

	public function get_icon() {
		return 'bdt-widget-icon ps-wi-dragon';
	}

	public function get_categories() {
		return ['prime-slider'];
	}

	public function get_keywords() {
		return [ 'prime slider', 'slider', 'Dragon', 'prime' ];
	}

	public function get_style_depends() {
		return ['ps-dragon'];
	}

	public function get_script_depends() {
		$reveal_effects = prime_slider_option('reveal-effects', 'prime_slider_other_settings', 'off');
		if ('on' === $reveal_effects) {
			if ( true === _is_ps_pro_activated() ) {
				return ['gsap', 'split-text', 'anime', 'revealFx', 'ps-dragon'];
			} else {
				return [];
			}
		} else {
			if ( true === _is_ps_pro_activated() ) {
				return ['gsap', 'split-text', 'ps-dragon'];
			} else {
				return [];
			}
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/eL0a9f7VEtc';
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

		$repeater->start_controls_tabs('tabs_slider_items');

		$repeater->start_controls_tab(
			'tab_slider_content',
			[
				'label' => esc_html__('Content', 'bdthemes-prime-slider'),
			]
		);

		/**
         * Repeater Sub Title Controls
         */
        $this->register_repeater_sub_title_controls($repeater);

		/**
         * Repeater Title Controls
         */
        $this->register_repeater_title_controls($repeater);

		/**
         * Repeater Button Text & Link Controls
         */
        $this->register_repeater_button_text_link_controls($repeater);

		/**
         * Repeater Image Controls
         */
        $this->register_repeater_image_controls($repeater);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'tab_slider_Optional',
			[
				'label' => esc_html__('Optional', 'bdthemes-prime-slider'),
			]
		);

		/**
         * Repeater Title Link Controls
         */
        $this->register_repeater_title_link_controls($repeater);

		/**
         * Repeater Excerpt Controls
         */
        $this->register_repeater_excerpt_controls($repeater);

		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();

		$this->add_control(
			'slides',
			[
				'label'   => esc_html__('Items', 'bdthemes-prime-slider'),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[
						'sub_title' => esc_html__('Addons For Elementor', 'bdthemes-prime-slider'),
						'title' => esc_html__('Prime Slider', 'bdthemes-prime-slider'),
						'image' => ['url' => BDTPS_CORE_ASSETS_URL . 'images/gallery/img-1.svg']
					],
					[
						'sub_title' => esc_html__('Addons For Elementor', 'bdthemes-prime-slider'),
						'title' => esc_html__('Element Pack', 'bdthemes-prime-slider'),
						'image' => ['url' => BDTPS_CORE_ASSETS_URL . 'images/gallery/img-2.svg']
					],
					[
						'sub_title' => esc_html__('Discover your Talents', 'bdthemes-prime-slider'),
						'title' => esc_html__('On Elementor', 'bdthemes-prime-slider'),
						'image' => ['url' => BDTPS_CORE_ASSETS_URL . 'images/gallery/img-3.svg']
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Additional Settings', 'bdthemes-prime-slider'),
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

		/**
		 * Show Excerpt Controls
		 */
		$this->register_show_excerpt_controls();

		/**
		 * Show Button Text Controls
		 */
		$this->register_show_button_text_controls();

		/**
		 * Show Social Link Controls
		 */
		$this->register_show_social_link_controls();

		/**
		* Show Pagination Controls
		*/
		$this->register_show_pagination_controls();

		$this->add_control(
			'show_blur_effect',
			[
				'label'   => esc_html__('Show Blur Effect', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'bdt-ps-blur-effect--',
				'classes'   => BDTPS_CORE_IS_PC
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
				'label' 	=> __('Social Link', 'bdthemes-prime-slider'),
				'condition' => [
					'show_social_icon' => 'yes',
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
						'social_link_title' => 'Facebook',
					],
					[
						'social_link'       => __('http://www.twitter.com/bdthemes/', 'bdthemes-prime-slider'),
						'social_link_title' => 'Twitter',
					],
					[
						'social_link'       => __('http://www.instagram.com/bdthemes/', 'bdthemes-prime-slider'),
						'social_link_title' => 'Instagram',
					],
				],
				'title_field' => '{{{ social_link_title }}}',
			]
		);

		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_content_animation',
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
                'classes'   => BDTPS_CORE_IS_PC
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
						'.bdt-slider-excerpt' => __('Excerpt', 'bdthemes-element-pack'),
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
				'classes'   => BDTPS_CORE_IS_PC
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label'     => esc_html__('Overlay Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'overlay' => ['background', 'blend'],
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

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .bdt-prime-slider-dragon .bdt-ps-dragon-bg img',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-prime-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-prime-slider-wrapper .bdt-prime-slider-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-prime-slider' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-prime-slider-wrapper .bdt-prime-slider-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_width',
			[
				'label' => esc_html__('Content Width', 'bdthemes-prime-slider'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 220,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-prime-slider-wrapper .bdt-prime-slider-content' => 'max-width: {{SIZE}}{{UNIT}};',
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

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .bdt-title-tag, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .bdt-title-tag a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'first_word_title_color',
			[
				'label'     => esc_html__( 'First Word Color', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .frist-word' => 'color: {{VALUE}}; -webkit-text-stroke-color: {{VALUE}};',
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
				'label'    => esc_html__('Text Stroke', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .bdt-title-tag',
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-main-title .bdt-title-tag' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_title' => ['yes'],
				],
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
				'label'    => esc_html__('Typography', 'bdthemes-prime-slider'),
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

		$this->start_controls_tab(
			'slider_style_excerpt',
			[
				'label'     => esc_html__('Excerpt', 'bdthemes-prime-slider'),
				'condition' => [
					'show_excerpt' => ['yes'],
				],
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-slider-excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		if ( $this->get_skins() ) {
			$this->add_control(
				'excerpt_background_color',
				[
					'label'     => esc_html__('Background', 'bdthemes-prime-slider'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-prime-slider-skin-slice .bdt-slide-text-btn-area' => 'background: {{VALUE}};',
					],
					'condition' => [
						'_skin'  => ['slice'],
					],
				]
			);
		} else {
			$this->add_control(
				'excerpt_background_color',
				[
					'label'     => esc_html__('Background', 'bdthemes-prime-slider'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-prime-slider-skin-slice .bdt-slide-text-btn-area' => 'background: {{VALUE}};',
					],
					'condition' => [
						'_skin'  => [''],
					],
				]
			);
		}

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'excerpt_typography',
				'label'    => esc_html__('Typography', 'bdthemes-prime-slider'),
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-slider-excerpt',
			]
		);

		$this->add_responsive_control(
			'excerpt_width',
			[
				'label' 	 	 => __('Width (px)', 'bdthemes-prime-slider'),
				'type' 			 => Controls_Manager::SLIDER,
				'default' 		 => [
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'size_units' 	 => ['px'],
				'range' 		 => [
					'px' => [
						'min' => 100,
						'max' => 800,
					],
				],
				'selectors'  	 => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-slider-excerpt' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'prime_slider_excerpt_spacing',
			[
				'label' 	=> esc_html__('Excerpt Spacing', 'bdthemes-prime-slider'),
				'type'  	=> Controls_Manager::SLIDER,
				'range' 	=> [
					'px' 		=> [
						'min' 		=> 0,
						'max' 		=> 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-slider-excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_excerpt'  => ['yes'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'slider_button_style',
			[
				'label' 	=> __('Button', 'bdthemes-prime-slider'),
				'condition' => [
					'show_button_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'slider_button_style_normal',
			[
				'label' 	=> esc_html__('Normal', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'slide_button_text_color',
			[
				'label' 	=> __('Text Color', 'bdthemes-prime-slider'),
				'type' 		=> Controls_Manager::COLOR,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-ps-dragon-button .bdt-ps-button-text' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'slide_button_icon_color',
			[
				'label' 	=> __('Icon Color', 'bdthemes-prime-slider'),
				'type' 	 	=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-ps-dragon-button .bdt-ps-button-arrow, {{WRAPPER}} .bdt-prime-slider-dragon .bdt-ps-dragon-button .bdt-ps-button-small-circle' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-ps-dragon-button .bdt-ps-button-arrow:after' => is_rtl() ? 'border-right-color: {{VALUE}};' : 'border-left-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'slide_button_circle_color',
			[
				'label' 	=> __('Circle Color', 'bdthemes-prime-slider'),
				'type' 	 	=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-ps-dragon-button .bdt-ps-button-border-circle' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'slide_button_typography',
				'selector'  => '{{WRAPPER}} .bdt-prime-slider-dragon .bdt-ps-dragon-button .bdt-ps-button-text',
			]
		);

		$this->add_control(
			'slider_button_style_hover',
			[
				'label' 	=> esc_html__('Hover', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'slide_button_hover_text_color',
			[
				'label' 	=> __('Text Color', 'bdthemes-prime-slider'),
				'type' 		=> Controls_Manager::COLOR,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-ps-dragon-button:hover .bdt-ps-button-text' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'slide_button_hover_circle_color',
			[
				'label' 	=> __('Circle Color', 'bdthemes-prime-slider'),
				'type' 	 	=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-ps-dragon-button:hover .bdt-ps-button-border-circle' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_social_icon',
			[
				'label'     => esc_html__('Social Link', 'bdthemes-prime-slider'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_social_icon' => 'yes',
				],
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
				'label'     => esc_html__('Text Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-prime-slider-social-icon a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'social_icon_cirlce_color',
			[
				'label'     => esc_html__('Circle Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-prime-slider-social-icon a:before' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'social_icon_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-prime-slider'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-prime-slider-social-icon a'    => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'social_icon_typography',
				'selector'  => '{{WRAPPER}} .bdt-prime-slider-dragon .bdt-prime-slider-social-icon a',
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
				'label'     => esc_html__('Text Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-prime-slider-social-icon a:hover' => 'color: {{VALUE}};',
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
			'navigation_number_color',
			[
				'label'     => __('Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-dotnav li:after' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-dotnav li:before' => 'background: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
			]
		);

		$this->add_control(
			'navigation_number_hover_color',
			[
				'label'     => __('Hover Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-dotnav li:hover:after' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
			]
		);

		$this->add_control(
			'navigation_number_active_color',
			[
				'label'     => __('Active Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-dotnav li.bdt-active:after' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-dotnav li.bdt-active:before' => 'background: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
			]
		);

		$this->add_responsive_control(
			'navigation_number_size',
			[
				'label'     => __('Size', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-dotnav li:after' => 'font-size: {{SIZE}}px',
				],
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
			]
		);

		$this->add_responsive_control(
			'navigation_line_width',
			[
				'label'     => __('Line Width', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-dragon .bdt-dotnav li.bdt-active:before' => 'width: {{SIZE}}px',
				],
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
				'classes'   => BDTPS_CORE_IS_PC
			]
		);

		$this->end_controls_section();
	}

	public function render_header($skin_name = 'dragon') {

		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('prime-slider', 'class', 'bdt-prime-slider-' . $skin_name);
		
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
        $this->render_slideshows_settings('440');
	}

    public function render_navigation_dots() {
        $settings = $this->get_settings_for_display();

        ?>
            <?php if ($settings['show_navigation_dots']) : ?>

                <ul class="bdt-slideshow-nav bdt-dotnav bdt-dotnav-vertical reveal-muted">

                    <?php $slide_index = 1; foreach ( $settings['slides'] as $slide ) : ?>
                    <li bdt-slideshow-item="<?php echo (esc_attr($slide_index) - 1); ?>" data-label="<?php echo wp_kses_post(str_pad( $slide_index, 2, '0', STR_PAD_LEFT)); ?>" ><a href="#"></a></li>
                    <?php $slide_index++;  endforeach; ?>
                    
                </ul>

            <?php endif; ?>

        <?php
    }

    public function render_footer() {

        ?>

					</ul>

					<?php $this->render_navigation_dots(); ?>
					
				</div>
				<?php $this->render_social_link(); ?>
			</div>
		</div>
        <?php
	}

	public function render_social_link($class = []) {
		$settings  = $this->get_active_settings();

		if ('' == $settings['show_social_icon']) {
			return;
		}

		$this->add_render_attribute('social-icon', 'class', 'bdt-prime-slider-social-icon reveal-muted');
		$this->add_render_attribute('social-icon', 'class', $class);

		?>

		<div <?php $this->print_render_attribute_string('social-icon'); ?>>

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

	public function render_button($content, $link_key) {
		$settings = $this->get_settings_for_display();

		if (!empty($content['button_link']['url'])) {
			$this->add_link_attributes($link_key, $content['button_link']);
		}
		$this->add_render_attribute($link_key, 'class', 'bdt-ps-dragon-button reveal-muted', true);

		?>

		<?php if ($content['slide_button_text'] && ('yes' == $settings['show_button_text'])) : ?>

			<a <?php $this->print_render_attribute_string($link_key); ?>>

				<?php

					$this->add_render_attribute([
						'content-wrapper' => [
							'class' => 'bdt-prime-slider-button-wrapper',
						],
						'text' => [
							'class' => 'bdt-prime-slider-button-text bdt-flex bdt-flex-middle bdt-flex-inline',
						],
					], '', '', true);

				?>
				
				<div class="bdt-ps-button">
					<div class="bdt-ps-button-text"><?php echo wp_kses($content['slide_button_text'], prime_slider_allow_tags('title')); ?></div>
					
					<div class="bdt-ps-button-wrapper">
						<div class="bdt-ps-button-arrow"></div>
						<div class="bdt-ps-button-border-circle"></div>
						<div class="bdt-ps-button-mask-circle">
						<div class="bdt-ps-button-small-circle"></div>
						</div>
					</div>
				</div>

			</a>
		<?php endif;
	}

	public function render_item_content($slide_content, $link_key) {
        $settings = $this->get_settings_for_display();

		$parallax_sub_title     = 'x: 100,-100; opacity: 1,1,0';
		$parallax_title         = 'x: 200,-200; opacity: 1,1,0';
		$parallax_text          = 'x: 300,-300; opacity: 1,1,0';

		if ( true === _is_ps_pro_activated() ) {
			if ($settings['animation_status'] == 'yes' && !empty($settings['animation_of'])) {

				if (in_array(".bdt-sub-title-inner", $settings['animation_of'])) {
					$parallax_sub_title = '';
				}

				if (in_array(".bdt-title-tag", $settings['animation_of'])) {
					$parallax_title = '';
				}
				if (in_array(".bdt-slider-excerpt", $settings['animation_of'])) {
					$parallax_text = '';
				}
			}
		}

        ?>
		<div class="bdt-prime-slider-wrapper">
			<div class="bdt-prime-slider-content">

				<?php if ($slide_content['sub_title'] && ('yes' == $settings['show_sub_title'])) : ?>
					<div class="bdt-sub-title">

						<<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?> class="bdt-sub-title-inner" data-bdt-slideshow-parallax="<?php echo esc_attr($parallax_sub_title); ?>" data-reveal="reveal-active">
                            <?php echo wp_kses_post($slide_content['sub_title']); ?>
                        </<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?>>

					</div>
				<?php endif; ?>

				<?php if ($slide_content['title'] && ('yes' == $settings['show_title'])) : ?>
					<div class="bdt-main-title">
						<<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?> class="bdt-title-tag" data-bdt-slideshow-parallax="<?php echo esc_attr($parallax_title); ?>"  data-reveal="reveal-active">
							<?php if ('' !== $slide_content['title_link']['url']) : ?>
								<a href="<?php echo esc_url($slide_content['title_link']['url']); ?>">
								<?php endif; ?>
								<?php echo wp_kses_post( prime_slider_first_word($slide_content['title']) ); ?>
								<?php if ('' !== $slide_content['title_link']['url']) : ?>
								</a>
							<?php endif; ?>
						</<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?>>
					</div>
				<?php endif; ?>

				<?php if ($slide_content['excerpt'] && ('yes' == $settings['show_excerpt'])) : ?>
					<div class="bdt-slider-excerpt" data-reveal="reveal-active" data-bdt-slideshow-parallax="<?php echo esc_attr($parallax_text); ?>">
						<?php echo wp_kses_post($slide_content['excerpt']); ?>
					</div>
				<?php endif; ?>

				<div data-bdt-slideshow-parallax="x: 400,-400; opacity: 1,1,0">
					<?php $this->render_button($slide_content, $link_key); ?>
				</div>
					
			</div>
		</div>
        <?php
    }

    public function render_slides_loop() {
        $settings = $this->get_settings_for_display();

        foreach ($settings['slides'] as $index => $slide) : ?>
		<li class="bdt-slideshow-item bdt-flex bdt-flex-middle elementor-repeater-item-<?php echo esc_attr($slide['_id']); ?>">

			<div class="bdt-ps-dragon-bg">
				<?php $this->rendar_item_image($slide, 'bdt-ps-slide-img'); ?>
			</div>

			<div class="bdt-ps-dragon-slide-image">
				<?php $this->rendar_item_image($slide, 'bdt-ps-slide-img'); ?>
			</div>

			<?php if ('none' !== $settings['overlay']) :
				$blend_type = ('blend' == $settings['overlay']) ? ' bdt-blend-' . $settings['blend_type'] : ''; ?>
				<div class="bdt-overlay-default bdt-position-cover<?php echo esc_attr($blend_type); ?>"></div>
			<?php endif; ?>
			
			<?php $this->render_item_content($slide, 'link_'.$index); ?>

		</li>
        <?php 
		endforeach;
    }

    public function render() {
        $this->render_header();
        $this->render_slides_loop();
        $this->render_footer();
    }
}