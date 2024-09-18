<?php

namespace PrimeSlider\Modules\Woocommerce\Widgets;

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
use PrimeSlider\Traits\QueryControls\GroupQuery\Group_Control_Query;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Woocommerce extends Widget_Base {
	use Group_Control_Query;
	use Global_Widget_Controls;

	public function get_name() {
		return 'prime-slider-woocommerce';
	}

	public function get_title() {
		return BDTPS . esc_html__('WooCommerce', 'bdthemes-prime-slider');
	}

	public function get_icon() {
		return 'bdt-widget-icon ps-wi-woocommerce';
	}

	public function get_categories() {
		return ['prime-slider'];
	}

	public function get_keywords() {
		return ['prime slider', 'slider', 'woocommerce', 'prime', 'wc slider'];
	}

	public function get_style_depends() {
		return ['ps-woocommerce'];
	}

	public function get_script_depends() {
		$reveal_effects = prime_slider_option('reveal-effects', 'prime_slider_other_settings', 'off');
		if ('on' === $reveal_effects) {
			if ( true === _is_ps_pro_activated() ) {
				return ['gsap', 'split-text', 'anime', 'revealFx', 'ps-woocommerce'];
			} else {
				return [];
			}
		} else {
			if ( true === _is_ps_pro_activated() ) {
				return ['gsap', 'split-text', 'ps-woocommerce'];
			} else {
				return [];
			}
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/6Wkk2EMN2ps';
	}

	protected function register_controls() {
		$reveal_effects = prime_slider_option('reveal-effects', 'prime_slider_other_settings', 'off');
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-prime-slider'),
			]
		);

		/**
         * Slider Height Controls
         */
        $this->register_slider_height_controls();

		$this->add_responsive_control(
			'wc_background_image_width',
			[
				'label' => esc_html__('Image Width', 'bdthemes-prime-slider'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 220,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-wc-product-img, {{WRAPPER}} .bdt-prime-slider .bdt-ps-dotnav-width' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		/**
		* Thumbnail Size Controls
		*/
		$this->register_thumbnail_size_controls();

		//Global background settings Controls
		$this->register_background_settings('.bdt-prime-slider-skin-woocommerce .bdt-ps-wc-product-img');

		/**
		* Show Title Controls
		*/
		$this->register_show_title_controls();

		/**
         * Show Category Controls
         */
        $this->register_show_category_controls();

		/**
		* Show Excerpt Controls
		*/
		$this->register_show_excerpt_controls();

		/**
		 * Show Price Controls
		 */
		$this->register_show_price_controls();

		/**
		 * Show Cart Controls
		 */
		$this->register_show_cart_controls();

		/**
         * Show social links Controls
         */
        $this->register_show_social_link_controls();

		/**
         * Show Scroll Button Controls
         */
        $this->register_show_scroll_button_controls();

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
				'label'   => esc_html__('Alignment', 'bdthemes-prime-slider'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_post_query_builder',
			[
				'label' => esc_html__('Query', 'bdthemes-prime-slider'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_query_builder_controls();

		$this->update_control(
			'posts_source',
			[
				'type'      => Controls_Manager::SELECT,
				'default'   => 'product',
				'options' => [
					'product' 			 => esc_html__('Product', 'bdthemes-prime-slider'),
					'manual_selection'   => esc_html__('Manual Selection', 'bdthemes-prime-slider'),
					'current_query'      => esc_html__('Current Query', 'bdthemes-prime-slider'),
					'_related_post_type' => esc_html__('Related', 'bdthemes-prime-slider'),
				]
			]
		);
		$this->update_control(
			'posts_limit',
			[
				'label'     => esc_html__('Limit', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3,
			]
		);
		$this->end_controls_section();

		/**
		 * Social links Text Controls
		 */
		$this->register_social_links_text_controls();

		/**
         * Scroll Down Controls
         */
        $this->register_scroll_down_controls();


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
						'.bdt-ps-title' => __('Title', 'bdthemes-element-pack'),
						'.bdt-ps-text' => __('Excerpt', 'bdthemes-element-pack'),
					],
					'default'  => ['.bdt-ps-title'],
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
				'default' => 'background',
				'options' => [
					'none'       => esc_html__('None', 'bdthemes-prime-slider'),
					'background' => esc_html__('Background', 'bdthemes-prime-slider'),
					'blend'      => esc_html__('Blend', 'bdthemes-prime-slider'),
				],
				'classes'   => BDTPS_CORE_IS_PC
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
					'{{WRAPPER}} .bdt-prime-slider-skin-woocommerce .bdt-overlay-default' => 'background-color: {{VALUE}};'
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
			'content_padding',
			[
				'label'      => esc_html__('Content Padding', 'bdthemes-prime-slider'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider-skin-woocommerce .bdt-ps-slideshow-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-title' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-title a' => 'color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-title',
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-title a',
				'fields_options' => [
					'text_stroke_type' => [
						'label' => esc_html__('Text Stroke', 'bdthemes-prime-slider'),
					],
				],
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'slider_category_style',
			[
				'label' 	=> __('Category', 'bdthemes-prime-slider'),
				'condition' => [
					'show_category' => ['yes'],
				],
			]
		);

		$this->add_control(
			'category_heading_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'category_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_background_color',
			[
				'label'     => esc_html__('Background', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-category a:before' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'category_border',
				'label'       => __('Border', 'bdthemes-element-pack'),
				'selector'    => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-prime-slider'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-prime-slider'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-category a',
			]
		);

		$this->add_responsive_control(
			'prime_slider_category_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-prime-slider'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-category' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_category' => ['yes'],
				],
			]
		);

		$this->add_control(
			'category_heading_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'category_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		// $this->add_control(
		// 	'category_hover_background_color',
		// 	[
		// 		'label'     => esc_html__('Background', 'bdthemes-prime-slider'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-category a:hover' => 'background: {{VALUE}};',
		// 		],
		// 	]
		// );

		$this->add_control(
			'category_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-category a:hover' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'excerpt_typography',
				'label'    => esc_html__('Typography', 'bdthemes-prime-slider'),
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-text',
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-text' => 'max-width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-text' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_excerpt'  => ['yes'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_style_price',
			[
				'label'     => __('Price', 'bdthemes-element-pack'),
				'condition' => [
					'show_price' => 'yes',
				],
			]
		);

		$this->add_control(
			'old_price_heading',
			[
				'label' => __('Old Price', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'old_price_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-price del, {{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-price .price > span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'old_price_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-price del, {{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-price .price > span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'old_price_typography',
				'label'    => __('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-price del, {{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-price .price > span',
			]
		);

		$this->add_control(
			'sale_price_heading',
			[
				'label'     => __('Sale Price', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sale_price_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-price ins' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'sale_price_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-price ins' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_price_typography',
				'label'    => __('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-price ins',
			]
		);


		$this->add_responsive_control(
			'sale_price_spacing',
			[
				'label'      => __('Spacing', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-price' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => __('Add to Cart Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_cart' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .button:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'label'       => __('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .button',
			]
		);

		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .button',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_quantity',
			[
				'label' => __('Quantity', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'quantity_button_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .input-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'quantity_button_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .input-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'quantity_button_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .input-text',
			]
		);

		$this->add_responsive_control(
			'quantity_button_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .input-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'quantity_button_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .input-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'quantity_button_shadow',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .input-text',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'quantity_button_typography',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-slideshow-content-wrapper .bdt-ps-add-to-cart .input-text',
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

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'social_icon_background',
				'selector'  => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a:before',
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

		$this->add_control(
			'social_icon_spacing',
			[
				'label' => esc_html__('Icon Spacing', 'bdthemes-prime-slider'),
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
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'social_icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a',
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
			'section_style_scroll_button',
			[
				'label' 	=> esc_html__('Scroll Down', 'bdthemes-prime-slider'),
				'tab'   	=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_scroll_button' => ['yes'],
				],
			]
		);

		$this->start_controls_tabs('tabs_scroll_button_style');

		$this->start_controls_tab(
			'tab_scroll_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-prime-slider'),
			]
		);

		$this->add_control(
			'scroll_button_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-scroll-down span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-prime-slider .bdt-scroll-down span svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'scroll_button_typography',
				'label'    => esc_html__('Typography', 'bdthemes-prime-slider'),
				'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-scroll-down span',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_scroll_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-prime-slider'),
			]
		);

		$this->add_control(
			'scroll_button_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-scroll-down:hover span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-prime-slider .bdt-scroll-down:hover span svg *' => 'fill: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous svg, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next:before, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous:before' => 'background: {{VALUE}}',
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

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 		=> 'arrows_border',
				'selector'  => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'arrows_border_radius',
			[
				'label' 	 => __('Border Radius', 'bdthemes-prime-slider'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dot_number_color',
			[
				'label'     => __('Total Number Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-dotnav span' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-dotnav span:before' => 'background: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
				'separator'	=> 'before',
			]
		);

		$this->add_control(
			'active_dot_number_color',
			[
				'label'     => __('Active Number Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-ps-dotnav li a' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous:hover svg, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next:hover svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next:before, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous:before' => 'background: {{VALUE}}',
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
				'selector' 	=> '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next::before, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous::before',
				'condition' => [
					'show_navigation_arrows' => ['yes'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_border_color',
			[
				'label' 	=> __('Border Color', 'bdthemes-prime-slider'),
				'type' 		=> Controls_Manager::COLOR,
				'condition' => [
					'arrows_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next:hover, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	//WC-Slider
	public function render_query() {
		$default = $this->getGroupControlQueryArgs();
		$wp_query = new WP_Query($default);
		return $wp_query;
	}

	public function render_header($skin_name = 'woocommerce') {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('prime-slider', 'class', 'bdt-prime-slider-skin-' . $skin_name);

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
					<a class="bdt-prime-slider-previous bdt-position-center-left" href="#" bdt-slidenav-previous bdt-slideshow-item="previous"></a>
					<a class="bdt-prime-slider-next bdt-position-center-right" href="#" bdt-slidenav-next bdt-slideshow-item="next"></a>
				</div>
			<?php endif; ?>

		<?php
	}

	public function render_navigation_dots() {
		$settings  = $this->get_active_settings();

		if ('' == $settings['show_navigation_dots']) {
			return;
		}

		?>
			<ul class="bdt-ps-dotnav bdt-position-bottom-right reveal-muted">
				<?php $slide_index = 1;
				$wp_query = $this->render_query();
				while ($wp_query->have_posts()) : $wp_query->the_post();
				?>

					<li bdt-slideshow-item="<?php echo esc_attr($slide_index - 1); ?>" data-label="<?php echo esc_attr(str_pad($slide_index, 2, '0', STR_PAD_LEFT)); ?>"><a href="#"><?php echo esc_attr(str_pad($slide_index, 2, '0', STR_PAD_LEFT)); ?></a>
						<?php $slide_index++; ?>
					</li>

				<?php
				endwhile;
				wp_reset_postdata();
				?>
				<span><?php echo esc_attr(str_pad($slide_index - 1, 2, '0', STR_PAD_LEFT)); ?></span>
			</ul>
		<?php
	}

	public function render_footer() {
		?>

					</ul>

					<div class="bdt-grid reveal-muted">
						<div class="bdt-width-expand bdt-position-relative">
							<?php $this->render_navigation_dots(); ?>
						</div>
						<div class="bdt-ps-dotnav-width"></div>
					</div>
					<?php $this->render_navigation_arrows(); ?>

				</div>
				<?php $this->render_scroll_button(); ?>
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

		$this->add_render_attribute('social-icon', 'class', 'bdt-prime-slider-social-icon');
		$this->add_render_attribute('social-icon', 'data-reveal', 'reveal-active');
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

	public function render_scroll_button_text() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('content-wrapper', 'class', 'bdt-scroll-down-content-wrapper reveal-muted');
		$this->add_render_attribute('text', 'class', 'bdt-scroll-down-text');

		?>
			<span bdt-scrollspy="cls: bdt-animation-slide-right; repeat: true" <?php $this->print_render_attribute_string('content-wrapper'); ?>>
				<span class="bdt-scroll-icon">

					<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" style="enable-background:new 0 0 64 64;" xml:space="preserve">
						<g>
							<g>
								<polygon points="31,0 31,60.586 23.707,53.293 22.293,54.854 31.293,64 32.707,64 41.707,54.854 40.293,53.366 33,60.586 33,0 " />
							</g>
						</g>
						<g></g>
						<g></g>
						<g></g>
						<g></g>
						<g></g>
						<g></g>
						<g></g>
						<g></g>
						<g></g>
						<g></g>
						<g></g>
						<g></g>
						<g></g>
						<g></g>
						<g></g>
					</svg>

				</span>
				<span <?php $this->print_render_attribute_string('text'); ?>><?php echo wp_kses($settings['scroll_button_text'], prime_slider_allow_tags('title')); ?></span>
			</span>
		<?php
	}

	public function render_scroll_button() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('bdt-scroll-down', 'class', ['bdt-scroll-down']);

		if ('' == $settings['show_scroll_button']) {
			return;
		}

		$this->add_render_attribute(
			[
				'bdt-scroll-down' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							'duration' => ('' != $settings['duration']['size']) ? $settings['duration']['size'] : '',
							'offset' => ('' != $settings['offset']['size']) ? $settings['offset']['size'] : '',
						]))
					]
				]
			]
		);

		$this->add_render_attribute('bdt-scroll-down', 'data-selector', '#' . esc_attr($settings['section_id']));

		$this->add_render_attribute('bdt-scroll-wrapper', 'class', 'bdt-scroll-down-wrapper');

		?>
			<div <?php $this->print_render_attribute_string('bdt-scroll-wrapper'); ?>>
				<button <?php $this->print_render_attribute_string('bdt-scroll-down'); ?>>
					<?php $this->render_scroll_button_text(); ?>
				</button>
			</div>

		<?php
	}

	public function render_item_content() {
		$settings = $this->get_settings_for_display();

		$parallax_title         = 'data-bdt-slideshow-parallax="y: 70,0,-100; opacity: 1,1,0"';
		$parallax_text           = 'data-bdt-slideshow-parallax="y: 90,0,-90; opacity: 1,1,0"';

		if ( true === _is_ps_pro_activated() ) {
			if ($settings['animation_status'] == 'yes' && !empty($settings['animation_of'])) {

				if (in_array(".bdt-ps-title", $settings['animation_of'])) {
					$parallax_title = '';
				}
				if (in_array(".bdt-ps-text", $settings['animation_of'])) {
					$parallax_text = '';
				}
			}
		}

		?>
			<div class="bdt-ps-slideshow-content-wrapper">

				<?php if ($settings['show_category']) : ?>
					<div class="bdt-ps-category" data-reveal="reveal-active" data-bdt-slideshow-parallax="y: 50,0,-110; opacity: 1,1,0">
						<?php echo wc_get_product_category_list(get_the_ID(), ' '); ?>
					</div>
				<?php endif; ?>

				<?php if ($settings['show_title']) : ?>
					<<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?> class="bdt-ps-title" data-reveal="reveal-active" <?php echo wp_kses_post($parallax_title); ?>>
						<a href="<?php the_permalink(); ?>">
							<?php the_title(); ?>
						</a>
					</<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?>>
				<?php endif; ?>

				<?php if ($settings['show_excerpt']) : ?>
					<div class="bdt-ps-text" data-reveal="reveal-active" <?php echo wp_kses_post($parallax_text); ?>><?php the_excerpt(); ?></div>
				<?php endif; ?>

				<?php if ($settings['show_price']) : ?>
					<div class="bdt-ps-price" data-reveal="reveal-active" data-bdt-slideshow-parallax="y: 100,0,-70; opacity: 1,1,0">
						<span class="wae-product-price"><?php woocommerce_template_single_price(); ?></span>
					</div>
				<?php endif; ?>

				<?php if ($settings['show_cart']) : ?>
					<div class="bdt-ps-add-to-cart-btn" data-reveal="reveal-active" data-bdt-slideshow-parallax="y: 110,0,-50; opacity: 1,1,0">
						<?php if ($settings['show_cart']) : ?>
							<div class="bdt-ps-add-to-cart">
								<?php woocommerce_template_single_add_to_cart(); ?>
							</div>
						<?php endif; ?>

					</div>
				<?php endif; ?>

			</div>
			<?php
	}

	public function render_slides_loop() {
		$settings = $this->get_settings_for_display();

		$wp_query = $this->render_query();

		while ($wp_query->have_posts()) : $wp_query->the_post();
			$placeholder_image_src = Utils::get_placeholder_image_src();
			$image_src = Group_Control_Image_Size::get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail_size', $settings);

			if ($image_src) {
				$image_final_src = $image_src;
			} elseif ($placeholder_image_src) {
				$image_final_src = $placeholder_image_src;
			} else {
				return;
			}

			?>

				<li class="bdt-slideshow-item elementor-repeater-item-<?php echo esc_attr(get_the_ID()); ?>">

					<div class="bdt-ps-item-inner bdt-flex bdt-flex-middle" bdt-grid>
						<div class="bdt-width-expand bdt-content-position-center">
							<?php $this->render_item_content(); ?>
						</div>

						<div class="bdt-ps-wc-product-img" data-reveal="reveal-active" style="background-image: url('<?php echo esc_url($image_final_src); ?>')">

						</div>
					</div>
					<?php if ('none' !== $settings['overlay']) :
							$blend_type = ('blend' == $settings['overlay']) ? ' bdt-blend-' . $settings['blend_type'] : ''; ?>
						<div class="bdt-overlay-default bdt-position-cover<?php echo esc_attr($blend_type); ?>"></div>
					<?php endif; ?>

				</li>

		<?php

		endwhile;
		wp_reset_postdata();
	}

	public function render() {

		$this->render_header();

		$this->render_slides_loop();

		$this->render_footer();
	}
}
