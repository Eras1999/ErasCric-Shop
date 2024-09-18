<?php
namespace PrimeSlider\Modules\Sequester\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Stroke;
use PrimeSlider\Utils;
use Elementor\Repeater;

use PrimeSlider\Traits\Global_Widget_Controls;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Sequester extends Widget_Base {

	use Global_Widget_Controls;

	public function get_name() {
		return 'prime-slider-sequester';
	}

	public function get_title() {
		return BDTPS . esc_html__('Sequester', 'bdthemes-prime-slider');
	}

	public function get_icon() {
		return 'bdt-widget-icon ps-wi-sequester';
	}

	public function get_categories() {
		return ['prime-slider'];
	}

	public function get_keywords() {
		return [ 'prime slider', 'slider', 'sequester', 'prime' ];
	}

	public function get_style_depends() {
		return ['ps-sequester'];
	}

	public function get_script_depends() {
		$reveal_effects = prime_slider_option('reveal-effects', 'prime_slider_other_settings', 'off');
		if ('on' === $reveal_effects) {
			if ( true === _is_ps_pro_activated() ) {
				return ['gsap', 'split-text', 'anime', 'revealFx', 'ps-sequester'];
			} else {
				return [];
			}
		} else {
			if ( true === _is_ps_pro_activated() ) {
				return ['gsap', 'split-text', 'ps-sequester'];
			} else {
				return [];
			}
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/pk5kCstNHBY';
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

		$repeater->start_controls_tabs('tabs_repeater_content');

		$repeater->start_controls_tab(
			'tab_main_content',
			[
				'label' => esc_html__('Content', 'bdthemes-prime-slider'),
			]
		);

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
			'tab_optional_content',
			[
				'label' => esc_html__('Optional', 'bdthemes-prime-slider'),
			]
		);

		/**
         * Repeater Sub Title Controls
         */
        $this->register_repeater_sub_title_controls($repeater);

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
				'label'   => esc_html__('Slider Items', 'bdthemes-prime-slider'),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[
						'sub_title' => esc_html__('This is a Label', 'bdthemes-prime-slider'),
						'title' => esc_html__('Sequester Slide One', 'bdthemes-prime-slider'),
						'image' => ['url' => BDTPS_CORE_ASSETS_URL . 'images/svg-img/item-01.svg']
					],
					[
						'sub_title' => esc_html__('This is a Label', 'bdthemes-prime-slider'),
						'title' => esc_html__('Sequester Slide Two', 'bdthemes-prime-slider'),
						'image' => ['url' => BDTPS_CORE_ASSETS_URL . 'images/svg-img/item-02.svg']
					],
					[
						'sub_title' => esc_html__('This is a Label', 'bdthemes-prime-slider'),
						'title' => esc_html__('Sequester Slide Three', 'bdthemes-prime-slider'),
						'image' => ['url' => BDTPS_CORE_ASSETS_URL . 'images/svg-img/item-03.svg']
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

		/**
		* Show Button Text Controls
		*/
		$this->register_show_button_text_controls();

		/**
		* Show Excerpt Controls
		*/
		$this->register_show_excerpt_controls();

		/**
         * Show social links Controls
         */
        $this->register_show_social_link_controls();

		/**
		* Show Navigation Controls
		*/
		$this->register_show_navigation_controls();

		/**
		* Show Pagination Controls
		*/
		$this->register_show_pagination_controls();

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
					'justify' => [
						'title' => esc_html__( 'Justified', 'bdthemes-prime-slider' ),
						'icon'  => 'eicon-text-align-justify',
					],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

		/**
		* Thumbnail Size Controls
		*/
		$this->register_thumbnail_size_controls();

		$this->add_control(
			'show_image_match_height',
			[
				'label'   => esc_html__('Image Match Height', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'bdt-ps-image-match-height--',
				'classes'    => BDTPS_CORE_IS_PC
			]
		);
		
		$this->end_controls_section();

		

		/**
		 * Social links Text Controls
		 */
		$this->register_social_links_text_controls();

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
			'custom_overlay_color',
			[
				'label'   => esc_html__('Custom Overlay', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
				'type'    => Controls_Manager::SWITCHER,
				'classes'    => BDTPS_CORE_IS_PC
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label'     => esc_html__('Overlay Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-slideshow-item .bdt-slide-image:before' => 'background: {{VALUE}};'
				],
				'condition' => [
					'custom_overlay_color' => 'yes'
				]
			]
		);

		$this->add_control(
			'overlay_opacity',
			[
				'label' => esc_html__('Opacity', 'bdthemes-prime-slider'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'step' => 0.1,
						'max' => 1,
					],
				],
				'default' => [
					'size' => 0.1
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-slideshow-item .bdt-slide-image:before' => 'opacity: {{SIZE}};'
				],
				'condition' => [
					'custom_overlay_color' => 'yes'
				]
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title' => 'max-width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .bdt-title-tag, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .bdt-title-tag a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_control(
            'first_word_title_color',
            [
                'label'     => esc_html__('First Word Color', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .bdt-title-tag .frist-word' => 'color: {{VALUE}};',
                ],
                'condition' => [
					'show_title' => ['yes'],
				],
				'classes'    => BDTPS_CORE_IS_PC
            ]
        );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-prime-slider'),
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .bdt-title-tag',
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .bdt-title-tag',
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .bdt-title-tag' => 'padding-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-sub-title-inner' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-prime-slider'),
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-sub-title-inner',
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-sub-title .bdt-sub-title-inner' => 'padding-bottom: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-slider-excerpt' => 'padding-bottom: {{SIZE}}{{UNIT}};',
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
				'label' 	=> __('Color', 'bdthemes-prime-slider'),
				'type' 		=> Controls_Manager::COLOR,
				'default' 	=> '',
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn svg *' => 'stroke: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'slide_button_background_color',
			[
				'label' 	=> __('Background', 'bdthemes-prime-slider'),
				'type' 	 	=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 	   => 'slide_button_border',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn',
			]
		);

		$this->add_control(
			'slide_button_border_radius',
			[
				'label'		 => __('Border Radius', 'bdthemes-prime-slider'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 	   => 'slide_button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn',
			]
		);

		$this->add_responsive_control(
			'slide_button_text_padding',
			[
				'label' 	 => __('Padding', 'bdthemes-prime-slider'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'slide_button_typography',
				'selector'  => '{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn',
			]
		);

		$this->add_control(
			'slider_button_icon_heading',
			[
				'label' 	=> esc_html__('Icon', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'slide_button_icon_color',
			[
				'label' 	=> __('Color', 'bdthemes-prime-slider'),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-slide-btn svg *' => 'stroke: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'slide_button_icon_background_color',
			[
				'label' 	=> __('Background', 'bdthemes-prime-slider'),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-slide-btn .bdt-slide-btn-icon' => 'background-color: {{VALUE}};',
				],
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
			'slide_button_hover_color',
			[
				'label' 	=> __('Color', 'bdthemes-prime-slider'),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn:hover svg *' => 'stroke: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'slide_button_background_hover_color',
			[
				'label' 	=> __('Background', 'bdthemes-prime-slider'),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'slide_button_hover_border_color',
			[
				'label' 	=> __('Border Color', 'bdthemes-prime-slider'),
				'type' 		=> Controls_Manager::COLOR,
				'condition' => [
					'slide_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'slider_button_icon_hover_heading',
			[
				'label' 	=> esc_html__('Icon', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'slide_button_icon_hover_color',
			[
				'label' 	=> __('Color', 'bdthemes-prime-slider'),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content .bdt-slide-btn:hover svg *' => 'stroke: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'slide_button_icon_hover_bg_color',
			[
				'label' 	=> __('Background', 'bdthemes-prime-slider'),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-slide-btn .bdt-slide-btn-icon::after' => 'background-color: {{VALUE}};',
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
				'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'social_icon_style_color',
			[
				'label'     => esc_html__('Line Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-prime-slider-social-icon a .bdt-social-share-title:before' => 'background: {{VALUE}};',
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

		$this->add_control(
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

		$this->add_control(
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

		$this->add_control(
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a'    => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'social_text_typography',
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
				'types'     => ['classic', 'gradient'],
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

		$this->start_controls_tabs('tabs_navigation_style');

		$this->start_controls_tab(
			'tab_navigation_arrows_style',
			[
				'label' 	=> __('Normal', 'bdthemes-prime-slider'),
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => __('Arrows Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-prime-slider-next svg, {{WRAPPER}} .bdt-prime-slider-sequester .bdt-prime-slider-previous svg' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_arrows' => ['yes'],
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' 		=> 'arrows_background',
				'label' 	=> __('Background', 'bdthemes-prime-slider'),
				'types' 	=> ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous',
				'condition' => [
					'show_navigation_arrows' => ['yes'],
				],
			]
		);

		$this->add_control(
			'arrows_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-prime-slider-next::before, {{WRAPPER}} .bdt-prime-slider-sequester .bdt-prime-slider-previous::before' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_arrows' => ['yes'],
				],
			]
		);

		$this->add_control(
			'navi_dot_heading',
			[
				'label'     => __('DOTS', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'navi_dot_color',
			[
				'label'     => __('Dot Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-dotnav li:before' => 'background: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
			]
		);

		$this->add_control(
			'active_dot_color',
			[
				'label'     => __('Active Dot Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-dotnav li:hover:before, {{WRAPPER}} .bdt-prime-slider-sequester .bdt-dotnav li.bdt-active:before' => 'background: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
			]
		);

		$this->add_control(
			'dots_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-dotnav li a::before' => 'border: 1px solid {{VALUE}}',
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-dotnav li.bdt-active a::after' => 'border-top-color: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
			]
		);

		$this->add_control(
			'active_dots_border_color',
			[
				'label'     => __('Active Border Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-dotnav li.bdt-active a::before' => 'border-top-color: {{VALUE}}; border-right-color: {{VALUE}}; border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-dotnav li.bdt-active a::after' => 'border-top-color: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_navigation_arrows_hover_style',
			[
				'label' 	=> __('Hover', 'bdthemes-prime-slider'),
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => __('Arrows Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-prime-slider-next:hover svg, {{WRAPPER}} .bdt-prime-slider-sequester .bdt-prime-slider-previous:hover svg' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_arrows' => ['yes'],
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' 		=> 'arrows_hover_background',
				'label' 	=> __('Background', 'bdthemes-prime-slider'),
				'types' 	=> ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next:hover, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous:hover',
				'condition' => [
					'show_navigation_arrows' => ['yes'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-prime-slider-next:hover:before, {{WRAPPER}} .bdt-prime-slider-sequester .bdt-prime-slider-previous:hover:before' => 'border-top-color: {{VALUE}}; border-right-color: {{VALUE}}; border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .bdt-prime-slider-sequester .bdt-prime-slider-next:hover::after, {{WRAPPER}} .bdt-prime-slider-sequester .bdt-prime-slider-previous:hover::after' => 'border-top-color: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_arrows' => ['yes'],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function render_header() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('prime-slider', 'class', 'bdt-prime-slider-sequester');

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
        $this->render_slideshows_settings('540');
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

			<ul class="bdt-slideshow-nav bdt-dotnav reveal-muted"></ul>

        <?php endif; ?>

        <?php
    }

	public function render_social_link() {
		$settings  = $this->get_active_settings();

		if ('' == $settings['show_social_icon']) {
			return;
		}

		$this->add_render_attribute('social-icon', 'class', 'bdt-prime-slider-social-icon');
		$this->add_render_attribute('social-icon', 'data-reveal', 'reveal-active');

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

    public function render_footer() {

        ?>

                </ul>

                <?php $this->render_navigation_arrows(); ?>
                <?php $this->render_navigation_dots(); ?>
				
			</div>
			<?php $this->render_social_link(); ?>
		</div>
		</div>
        <?php
	}

	public function rendar_item_image($item, $alt = '') {
		$settings = $this->get_settings_for_display();

		$image_src = Group_Control_Image_Size::get_attachment_image_src($item['image']['id'], 'thumbnail_size', $settings);

		if ( ! $image_src ) {
			$image_src = $item['image']['url'];
		}

		?>

		<img src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_html($alt); ?>">

		<?php
	}

	public function render_button($content) {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('slider-button', 'class', 'bdt-slide-btn', true);
		$this->add_render_attribute('slider-button', 'data-reveal', 'reveal-active', true);

		if (isset($content['button_link']['url'])) {
			$this->add_render_attribute('slider-button', 'href', esc_url($content['button_link']['url']), true);

			if ($content['button_link']['is_external']) {
				$this->add_render_attribute('slider-button', 'target', '_blank', true);
			}

			if ($content['button_link']['nofollow']) {
				$this->add_render_attribute('slider-button', 'rel', 'nofollow', true);
			}
		} else {
			$this->add_render_attribute('slider-button', 'href', '#', true);
		}

		?>

		<?php if ($content['slide_button_text'] && ('yes' == $settings['show_button_text'])) : ?>

			<a <?php $this->print_render_attribute_string('slider-button'); ?>>

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
				<span <?php $this->print_render_attribute_string('content-wrapper'); ?>>

					<span <?php $this->print_render_attribute_string('text'); ?>><?php echo wp_kses($content['slide_button_text'], prime_slider_allow_tags('title')); ?><span class="bdt-slide-btn-icon"><svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="arrow-right"><polyline fill="none" stroke="#000" points="10 5 15 9.5 10 14"></polyline><line fill="none" stroke="#000" x1="4" y1="9.5" x2="15" y2="9.5"></line></svg></span></span>

				</span>


			</a>
		<?php endif;
	}

	public function render_item_content($slide_content) {
        $settings = $this->get_settings_for_display();

		$parallax_sub_title     = 'data-bdt-slideshow-parallax="y: 50,0,-50; opacity: 1,1,0"';
		$parallax_title         = 'data-bdt-slideshow-parallax="y: 50,0,-50; opacity: 1,1,0"';
		$parallax_text          = 'data-bdt-slideshow-parallax="y: 100,0,-80; opacity: 1,1,0"';

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

		<div class="bdt-prime-slider-content">
			<div class="bdt-prime-slider-desc">

				<?php if ($slide_content['sub_title'] && ('yes' == $settings['show_sub_title'])) : ?>
					<div class="bdt-sub-title">
						<<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?> class="bdt-sub-title-inner" <?php echo wp_kses_post($parallax_sub_title); ?> data-reveal="reveal-active">
							<?php echo wp_kses_post($slide_content['sub_title']); ?>
						</<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?>>
					</div>
				<?php endif; ?>

				<?php if ($slide_content['title'] && ('yes' == $settings['show_title'])) : ?>
					<div class="bdt-main-title">
						<<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?> class="bdt-title-tag" <?php echo wp_kses_post($parallax_title); ?> data-reveal="reveal-active">
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

				<?php if ($slide_content['excerpt'] && ('yes' == $settings['show_excerpt'])) : ?>
					<div class="bdt-slider-excerpt" <?php echo wp_kses_post($parallax_title); ?> data-reveal="reveal-active">
						<?php echo wp_kses_post($slide_content['excerpt']); ?>
					</div>
				<?php endif; ?>

				<div class="bdt-button-wrapper" data-bdt-slideshow-parallax="y: 150,0,-100; opacity: 1,1,0">
					<?php $this->render_button($slide_content); ?>
				</div>
				
			</div>
		</div>

        <?php
    }

    public function render_slides_loop() {
        $settings = $this->get_settings_for_display();

		foreach ($settings['slides'] as $slide) : 
		
			$image_src = Group_Control_Image_Size::get_attachment_image_src( $slide['image']['id'], 'thumbnail_size', $settings);
			if ( ! $image_src ) {
				$image_src = $slide['image']['url'];
			}
			?>

            <li class="bdt-slideshow-item bdt-flex bdt-flex-row bdt-flex-middle elementor-repeater-item-<?php echo esc_attr($slide['_id']); ?> ">

                    <?php $this->render_item_content($slide); ?>

                    <div class="bdt-slide-image" style="background-image: url('<?php echo esc_url( $image_src); ?>');">
                        
                    </div>
            </li>

        <?php endforeach;
    }

    public function render() {
		
        $this->render_header();

        $this->render_slides_loop();

        $this->render_footer();
    }
}