<?php

namespace PrimeSlider\Modules\Flogia\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use PrimeSlider\Traits\Global_Widget_Controls;
use PrimeSlider\Traits\QueryControls\GroupQuery\Group_Control_Query;
use PrimeSlider\Utils;
use WP_Query;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class Flogia extends Widget_Base {

    use Group_Control_Query;
    use Global_Widget_Controls;

    public function get_name() {
        return 'prime-slider-flogia';
    }

    public function get_title() {
        return BDTPS . esc_html__('Flogia', 'bdthemes-prime-slider');
    }

    public function get_icon() {
        return 'bdt-widget-icon ps-wi-flogia';
    }

    public function get_categories() {
        return ['prime-slider'];
    }

    public function get_keywords() {
        return ['prime slider', 'slider', 'blog', 'prime', 'flogia'];
    }

    public function get_style_depends() {
        return ['ps-flogia'];
    }

    public function get_script_depends() {
        $reveal_effects = prime_slider_option('reveal-effects', 'prime_slider_other_settings', 'off');
        if ('on' === $reveal_effects) {
            if ( true === _is_ps_pro_activated() ) {
                return ['gsap', 'split-text', 'mThumbnailScroller', 'anime', 'revealFx', 'ps-flogia'];
            } else {
                return ['mThumbnailScroller', 'ps-flogia'];
            }
        } else {
            if ( true === _is_ps_pro_activated() ) {
                return ['gsap', 'split-text', 'mThumbnailScroller', 'ps-flogia'];
            } else {
                return ['mThumbnailScroller', 'ps-flogia'];
            }
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/Ayo1oEALF_8';
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
            'content_max_width',
            [
                'label' => esc_html__('Content Max Width', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 220,
                        'max' => 1600,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-flogia .bdt-ps-container' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
                ],
                'classes'   => BDTPS_CORE_IS_PC
            ]
        );

        //content align controls
        $this->add_responsive_control(
            'content_position',
            [
                'label' => esc_html__('Content Position', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Start', 'bdthemes-prime-slider'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-prime-slider'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('End', 'bdthemes-prime-slider'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slideshow-item' => 'align-items: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_alignment',
            [
                'label' => esc_html__('Alignment', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'bdthemes-prime-slider'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-prime-slider'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'bdthemes-prime-slider'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__('Justify', 'bdthemes-prime-slider'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        /**
		* Thumbnail Size Controls
		*/
		$this->register_thumbnail_size_controls();

        //Global background settings Controls
        $this->register_background_settings('.bdt-prime-slider .bdt-slideshow-item>.bdt-ps-slide-img');

        /**
		* Show Title Controls
		*/
		$this->register_show_title_controls();

        /**
         * Show Post Excerpt Controls
         */
        $this->register_show_post_excerpt_controls();

        /**
         * Show Category Controls
         */
        $this->register_show_category_controls();

        $this->add_control(
            'show_admin_info',
            [
                'label' => esc_html__('Show Author', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'published_by',
            [
                'label' => esc_html__('Published By', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'show_admin_info' => 'yes',
                ],
                'classes'   => BDTPS_CORE_IS_PC
            ]
        );

        $this->add_control(
            'show_thumbnav',
            [
                'label' => esc_html__('Show Thumbs', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
                'classes'   => BDTPS_CORE_IS_PC
            ]
        );

        $this->add_control(
            'thumbs_hide_on',
            [
                'label' => __('Thumbs Hide On', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'label_block' => false,
                'options' => [
                    'desktop' => __('Desktop', 'bdthemes-element-pack'),
                    'tablet' => __('Tablet', 'bdthemes-element-pack'),
                    'mobile' => __('Mobile', 'bdthemes-element-pack'),
                ],
                'frontend_available' => true,
                'condition' => [
                    'show_thumbnav' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_navigation_arrows_dots',
            [
                'label' => esc_html__('Show Navigation', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_post_query_builder',
            [
                'label' => __('Query', 'bdthemes-prime-slider'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->register_query_builder_controls();

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

        /**
         * Ken Burns Controls
         */
        $this->register_ken_burns_controls();

        $this->end_controls_section();

        /**
         * Advanced Animation
         */
        $this->start_controls_section(
            'section_advanced_animation',
            [
                'label' => esc_html__('Advanced Animation', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'animation_status',
            [
                'label' => esc_html__('Advanced Animation', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'classes' => BDTPS_CORE_IS_PC,
            ]
        );
        if ( true === _is_ps_pro_activated() ) {

            $this->add_control(
                'animation_of',
                [
                    'label' => __('Animation Of', 'bdthemes-element-pack'),
                    'type' => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => [
                        '.bdt-title-tag' => __('Title', 'bdthemes-element-pack'),
                        '.bdt-blog-text' => __('Excerpt', 'bdthemes-element-pack'),
                    ],
                    'default' => ['.bdt-title-tag'],
                    'condition' => [
                        'animation_status' => 'yes',
                    ],
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
                'label' => esc_html__('Sliders', 'bdthemes-prime-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'overlay',
            [
                'label' => esc_html__('Overlay', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type' => Controls_Manager::SELECT,
                'default' => 'background',
                'options' => [
                    'none' => esc_html__('None', 'bdthemes-prime-slider'),
                    'background' => esc_html__('Background', 'bdthemes-prime-slider'),
                    'blend' => esc_html__('Blend', 'bdthemes-prime-slider'),
                ],
                'separator' => 'before',
                'classes'   => BDTPS_CORE_IS_PC
            ]
        );

        $this->add_control(
            'overlay_color',
            [
                'label' => esc_html__('Overlay Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'overlay' => ['background', 'blend'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-slideshow .bdt-overlay-default' => 'background-color: {{VALUE}};',
                ],
                'classes'   => BDTPS_CORE_IS_PC
            ]
        );

        $this->add_control(
            'blend_type',
            [
                'label' => esc_html__('Blend Type', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SELECT,
                'default' => 'multiply',
                'options' => prime_slider_blend_options(),
                'condition' => [
                    'overlay' => 'blend',
                ],
            ]
        );

        $this->add_responsive_control(
            'ps_content_innner_padding',
            [
                'label' => esc_html__('Content Padding', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_width',
            [
                'label' => esc_html__('Content Width', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 220,
                        'max' => 1200,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-content' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_slider_style');

        $this->start_controls_tab(
            'tab_slider_title',
            [
                'label' => __('Title', 'bdthemes-prime-slider'),
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-content .bdt-title-tag a' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_title' => ['yes'],
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label' => esc_html__('Hover Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-content .bdt-title-tag a:hover, {{WRAPPER}} .bdt-prime-slider .bdt-ps-content .bdt-title-tag a:hover span' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_title' => ['yes'],
                ],
            ]
        );

        $this->add_control(
            'first_word_title_color',
            [
                'label' => esc_html__('First Word Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-content .bdt-title-tag a span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Typography', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-content .bdt-title-tag',
                'condition' => [
                    'show_title' => ['yes'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow',
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-content .bdt-title-tag a',
                'condition' => [
                    'show_title' => ['yes'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'title_text_stroke',
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-content .bdt-title-tag a',
                'fields_options' => [
                    'text_stroke_type' => [
                        'label' => esc_html__('Text Stroke', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                    ],
                ],
                'condition' => [
                    'show_title' => ['yes'],
                ],
                'classes'   => BDTPS_CORE_IS_PC
            ]
        );

        $this->add_responsive_control(
            'prime_slider_title_spacing',
            [
                'label' => esc_html__('Spacing', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-content .bdt-title-tag' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_title' => ['yes'],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'slider_style_excerpt',
            [
                'label' => esc_html__('Text', 'bdthemes-prime-slider'),
                'condition' => [
                    'show_excerpt' => ['yes'],
                ],
            ]
        );

        $this->add_control(
            'excerpt_color',
            [
                'label' => esc_html__('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-blog-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'excerpt_typography',
                'label' => esc_html__('Typography', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-blog-text',
            ]
        );

        $this->add_responsive_control(
            'excerpt_width',
            [
                'label' => __('Width (px)', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 800,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-blog-text' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'prime_slider_excerpt_spacing',
            [
                'label' => esc_html__('Spacing', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-blog-text' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_excerpt' => ['yes'],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_slider_category',
            [
                'label' => __('Category', 'bdthemes-prime-slider'),
                'condition' => [
                    'show_category' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'category_normal_heading',
            [
                'label' => __('NORMAL', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'category_icon_color',
            [
                'label' => __('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-category a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'category_icon_background_color',
            [
                'label' => __('Background Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-category a' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'category_border',
                'label' => esc_html__('Border', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-category a',
            ]
        );

        $this->add_responsive_control(
            'category_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'category_typography',
                'label' => esc_html__('Typography', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-category a',
            ]
        );

        $this->add_responsive_control(
            'ps_category_spacing',
            [
                'label' => esc_html__('Spacing', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-category-wrapper' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ps_category_space_between',
            [
                'label' => esc_html__('Space Between', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-category a + a' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'category_hover_heading',
            [
                'label' => __('HOVER', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'category_hover_color',
            [
                'label' => __('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-category a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'category_hover_background_color',
            [
                'label' => __('Background Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-category a:hover' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'category_hover_border_color',
            [
                'label'     => __('Border Color', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'category_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-category a:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_slider_meta',
            [
                'label' => __('Author', 'bdthemes-prime-slider'),
                'condition' => [
                    'show_admin_info' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'meta_text_color',
            [
                'label' => __('Text Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-meta .bdt-author, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-meta .bdt-author a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'meta_text_hover_color',
            [
                'label' => __('Hover Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-meta .bdt-author a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'meta_typography',
                'label' => esc_html__('Typography', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-meta .bdt-author',
            ]
        );

        $this->add_control(
            'author_avatar_heading',
            [
                'label' => __('AVATAR', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'avatar_size',
            [
                'label'      => _x('Size', 'bdthemes-prime-slider'),
                'type'       => Controls_Manager::SELECT,
                'default'    => '42',
                'options'    => [
                    '24'        => '24 X 24',
                    '36'        => '36 X 36',
                    '42'        => '42 X 42',
                    '48'        => '48 X 48',
                    '60'        => '60 X 60',
                    '70'        => '70 X 70',
                    '90'        => '90 X 90',
                ],
            ]
        );

        $this->add_responsive_control(
            'avatar_spacing',
            [
                'label' => esc_html__('Spacing', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-post-slider-author' => 'margin-right: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_featured_post',
            [
                'label' => esc_html__('Thumbs', 'bdthemes-prime-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_thumbnav' => 'yes',
                ],
            ]
        );

        //tabs controls
        $this->start_controls_tabs('tabs_featured_post_style');
        //normal tab
        $this->start_controls_tab(
            'tab_featured_post_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-prime-slider'),
            ]
        );
        
        $this->add_control(
            'featured_post_title_color',
            [
                'label' => __('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-thumbnav>a span' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'featured_post_background_color',
            [
                'label' => __('Background Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-thumbnav>a span' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'featured_post_overlay_color',
            [
                'label' => __('Overlay Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-thumbnav .bdt-thumb-content:before' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'featured_thumbs_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-ps-thumbnav .bdt-thumb-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'featured_thumbs_padding',
            [
                'label' => esc_html__('Title Padding', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-flogia .bdt-ps-thumbnav>a span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'featured_thumbs_title_margin',
            [
                'label' => esc_html__('Title Margin', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-flogia .bdt-ps-thumbnav>a span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'featured_thumbs_margin',
            [
                'label' => esc_html__('Thumbs Margin', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-flogia .bdt-thumb-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'featured_typography',
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-thumbnav>a span',
            ]
        );

        $this->add_control(
            'featured_post_alignment',
            [
                'label' => __('Alignment', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'bdthemes-prime-slider'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-prime-slider'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'bdthemes-prime-slider'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle' => false,
            ]
        );

        $this->end_controls_tab();
        //active tab
        $this->start_controls_tab(
            'tab_featured_post_active',
            [
                'label' => esc_html__('Active', 'bdthemes-prime-slider'),
            ]
        );

        $this->add_control(
            'featured_post_title_color_active',
            [
                'label' => __('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-thumbnav.bdt-active>a span' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'featured_post_background_color_active',
            [
                'label' => __('Background Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-thumbnav.bdt-active>a span' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'featured_post_overlay_color_active',
            [
                'label' => __('Overlay Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-thumbnav.bdt-active .bdt-thumb-content:before' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'featured_thumbs_border',
                'label' => __('Border', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-ps-thumbnav.bdt-active .bdt-thumb-content',
            ]
        );

        //end active tab
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_navigation',
            [
                'label' => __('Navigation', 'bdthemes-prime-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_navigation_arrows_dots' => ['yes'],
                ],
            ]
        );

        $this->start_controls_tabs('tabs_navigation_style');

        $this->start_controls_tab(
            'tab_nav_arrows_dots_style',
            [
                'label' => __('Normal', 'bdthemes-prime-slider'),
            ]
        );

        $this->add_control(
            'arrows_color',
            [
                'label' => __('Arrows Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous svg, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next svg' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'dots_color',
            [
                'label' => __('Dots Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-dotnav li a:before' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_nav_arrows_dots_hover_style',
            [
                'label' => __('Hover', 'bdthemes-prime-slider'),
            ]
        );

        $this->add_control(
            'arrows_hover_color',
            [
                'label' => __('Arrows Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous:hover svg, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next:hover svg' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'dots_hover_color',
            [
                'label' => __('Dots Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-dotnav li:hover a:before' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_nav_arrows_dots_active_style',
            [
                'label' => __('Active', 'bdthemes-prime-slider'),
            ]
        );

        $this->add_control(
            'dots_active_color',
            [
                'label' => __('Dots Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-dotnav li.bdt-active a:before' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'dots_active_border_color',
            [
                'label' => __('Dots Border Color', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-dotnav li.bdt-active a:after' => 'border-color:{{VALUE}}',
                ],
                'classes'   => BDTPS_CORE_IS_PC
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'nav_offset',
            [
                'label' => __('Horizontal Offset', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-flogia .bdt-navigation-arrows' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function query_posts() {
        $settings = $this->get_settings();
        $args = [];

        if ($settings['posts_limit']) {
            $args['posts_per_page'] = $settings['posts_limit'];
            $args['paged'] = max(1, get_query_var('paged'), get_query_var('page'));
        }

        $default = $this->getGroupControlQueryArgs();
        $args = array_merge($default, $args);

        $query = new WP_Query($args);

        return $query;
    }

    public function render_header($skin_name = 'flogia') {
        $settings = $this->get_settings_for_display();

        /**
         * Advanced Animation
         */
        $this->adv_anim('slideshow');
        $this->add_render_attribute('slideshow', 'id', 'bdt-' . $this->get_id());

        $this->add_render_attribute('prime-slider', 'class', 'bdt-prime-slider-' . $skin_name);

        /**
         * Reveal Effects
         */
        $this->reveal_effects_attr('slideshow');

        /**
         * Slideshow Settings
         */
        $this->render_slideshows_settings('520');
    }

    public function render_category() {
        ?>
        <span class="bdt-ps-category" data-reveal="reveal-active">
            <span><?php echo get_the_category_list(', '); ?></span>
        </span>
        <?php
    }

    public function render_navigation_arrows_dots() {
        $settings = $this->get_settings_for_display();
        ?>

        <?php if ($settings['show_navigation_arrows_dots']): ?>
            <div class="bdt-navigation-arrows bdt-position-center-right bdt-position-large bdt-position-z-index  reveal-muted">
                <a class="bdt-prime-slider-previous" href="#" bdt-slidenav-previous bdt-slideshow-item="previous"></a>

                <ul class="bdt-slideshow-nav bdt-ps-dotnav bdt-dotnav bdt-dotnav-vertical"></ul>

                <a class="bdt-prime-slider-next" href="#" bdt-slidenav-next bdt-slideshow-item="next"></a>
            </div>
        <?php endif;?>

        <?php
    }

    public function render_thumbnav() {
        $settings = $this->get_settings_for_display();

        $thumbs_hide_on_setup = '';
        if (!empty($settings['thumbs_hide_on'])) {
            foreach ($settings['thumbs_hide_on'] as $element) {

                if ($element == 'desktop') {
                    $thumbs_hide_on_setup .= ' bdt-desktop';
                }
                if ($element == 'tablet') {
                    $thumbs_hide_on_setup .= ' bdt-tablet';
                }
                if ($element == 'mobile') {
                    $thumbs_hide_on_setup .= ' bdt-mobile';
                }
            }
        }

        ?>

        <?php if ('yes' == $settings['show_thumbnav']): ?>
            <div class="bdt-thumb-wrapper bdt-position-bottom-<?php echo esc_attr($settings['featured_post_alignment']); ?> bdt-position-large <?php echo esc_attr($thumbs_hide_on_setup); ?>">
            <div class="bdt-thumbnav-scroller">
                <ul class="bdt-slider-items">
                    <?php
            $slide_index = 1;

            $wp_query = $this->query_posts();

            if (!$wp_query->found_posts) {
                return;
            }

            while ($wp_query->have_posts()) {
                $wp_query->the_post();

            ?>

            <li class="bdt-ps-thumbnav bdt-position-relative" bdt-slideshow-item="<?php echo esc_attr($slide_index - 1); ?>">
                <a href="#">
                    <div class="bdt-thumb-content">
                        <?php $this->rendar_thumb_image();?>
                        <span><?php echo get_the_title(); ?></span>
                    </div>
                </a>
                <?php $slide_index++;?>
            </li>

            <?php
            }

        wp_reset_postdata();?>

                    </ul>
                </div>
                </div>
            <?php endif;?>

        <?php
    }

    public function render_footer() {
        ?>

                    </ul>

                    <?php $this->render_navigation_arrows_dots();?>

                    <?php $this->render_thumbnav();?>

                </div>

            </div>
        </div>
        <?php
    }

    public function rendar_thumb_image() {
        $settings = $this->get_settings_for_display();

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

            <img src="<?php echo esc_url($image_final_src); ?>" alt="<?php echo get_the_title(); ?>">

        <?php
    }

    public function render_excerpt() {
        $settings = $this->get_settings_for_display();

        if (!$this->get_settings('show_excerpt')) {
            return;
        }

        $strip_shortcode = $this->get_settings_for_display('strip_shortcode');

        $parallax_text = 'data-bdt-slideshow-parallax="y: 100,0,-60; opacity: 1,1,0"';

        if ( true === _is_ps_pro_activated() ) {
            if ($settings['animation_status'] == 'yes' && !empty($settings['animation_of'])) {

                if (in_array(".bdt-blog-text", $settings['animation_of'])) {
                    $parallax_text = '';
                }
            }
        }

        ?>
            <div class="bdt-blog-text" <?php echo wp_kses_post($parallax_text); ?> data-reveal="reveal-active">
                <?php
                if (has_excerpt()) {
                    the_excerpt();
                } else {
                    echo wp_kses_post(prime_slider_custom_excerpt($this->get_settings_for_display('excerpt_length'), $strip_shortcode));
                }
                ?>
            </div>
        <?php
    }

    public function render_item_content($post) {
        $settings = $this->get_settings_for_display();

        $parallax_title = 'data-bdt-slideshow-parallax="y: 80,0,-80; opacity: 1,1,0"';

        if ( true === _is_ps_pro_activated() ) {
            if ($settings['animation_status'] == 'yes' && !empty($settings['animation_of'])) {

                if (in_array(".bdt-title-tag", $settings['animation_of'])) {
                    $parallax_title = '';
                }
            }
        }

        $avatar_size = $settings['avatar_size'];

        ?>

        <div class="bdt-ps-container">
            <div class="bdt-ps-content">

                <?php if ('yes' == $settings['show_category']): ?>
                    <div class="bdt-ps-category-wrapper" data-bdt-slideshow-parallax="y: 50,0,-50; opacity: 1,1,0">
                        <?php $this->render_category();?>
                    </div>
                <?php endif;?>

                <?php if ('yes' == $settings['show_title']): ?>
                    <div class="bdt-main-title">
                        <<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?> class="bdt-title-tag" <?php echo wp_kses_post($parallax_title); ?> data-reveal="reveal-active">

                            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                                <?php echo wp_kses_post(prime_slider_first_word(get_the_title())); ?>
                            </a>

                        </<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?>>

                        <?php $this->render_excerpt();?>

                    </div>
                <?php endif;?>

                <?php if ('yes' == $settings['show_admin_info']): ?>
                    <div class="bdt-prime-slider-meta bdt-flex-inline bdt-flex-middle" data-reveal="reveal-active" data-bdt-slideshow-parallax="y: 70,-30">
                        <div class="bdt-post-slider-author bdt-flex bdt-margin-small-right bdt-border-circle bdt-overflow-hidden">
                            <?php echo get_avatar(get_the_author_meta('ID'), $avatar_size); ?>
                        </div>
                        <div class="bdt-meta-author bdt-flex bdt-flex-middle">
                            <span class="bdt-author">
                                <?php if ($settings['published_by'] == 'yes'): ?>
                                    <?php echo esc_html_x('Published by ', 'Frontend', 'bdthemes-prime-slider'); ?>
                                <?php endif;?>
                                <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php echo esc_attr(get_the_author()); ?></a>
                            </span>
                        </div>
                    </div>
                <?php endif;?>

            </div>
        </div>

        <?php
    }

    public function render_slides_loop() {
        $settings = $this->get_settings_for_display();

        $kenburns_reverse = $settings['kenburns_reverse'] ? ' bdt-animation-reverse' : '';

        $slide_index = 1;

        global $post;

        $wp_query = $this->query_posts();

        if (!$wp_query->found_posts) {
            return;
        }

        while ($wp_query->have_posts()) {
            $wp_query->the_post();

            ?>

                <li class="bdt-slideshow-item bdt-flex elementor-repeater-item-<?php echo esc_attr(get_the_ID()); ?>">

                    <?php if ('yes' == $settings['kenburns_animation']): ?>
                        <div class="bdt-position-cover bdt-animation-kenburns<?php echo esc_attr($kenburns_reverse); ?> bdt-transform-origin-center-left">
                        <?php endif;?>

                        <?php $this->rendar_post_image("bdt-ps-slide-img"); ?>

                        <?php if ('yes' == $settings['kenburns_animation']): ?>
                        </div>
                    <?php endif;?>

                    <?php if ('none' !== $settings['overlay']):
                $blend_type = ('blend' == $settings['overlay']) ? ' bdt-blend-' . $settings['blend_type'] : '';?>
	                        <div class="bdt-overlay-default bdt-position-cover<?php echo esc_attr($blend_type); ?>"></div>
	                    <?php endif;?>

                    <?php $this->render_item_content($post);?>

                    <?php $slide_index++;?>

                </li>


            <?php
        }

        wp_reset_postdata();
    }

    public function render() {

        $this->render_header();

        $this->render_slides_loop();

        $this->render_footer();
    }
}
