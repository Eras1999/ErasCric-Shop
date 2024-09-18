<?php

namespace PrimeSlider\Modules\Vertex\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Widget_Base;
use Elementor\Plugin;

use PrimeSlider\Traits\Global_Widget_Controls;
use PrimeSlider\Traits\QueryControls\GroupQuery\Group_Control_Query;
use PrimeSlider\Utils;
use WP_Query;

if (!defined('ABSPATH')) {
    exit;
}

// Exit if accessed directly

class Vertex extends Widget_Base {

    use Group_Control_Query;
    use Global_Widget_Controls;

    public function get_name() {
        return 'prime-slider-vertex';
    }

    public function get_title() {
        return BDTPS . esc_html__('Vertex', 'bdthemes-prime-slider');
    }

    public function get_icon() {
        return 'bdt-widget-icon ps-wi-vertex';
    }

    public function get_categories() {
        return ['prime-slider'];
    }

    public function get_keywords() {
        return ['prime slider', 'slider', 'vertex', 'prime', 'blog', 'post', 'news'];
    }

    public function get_style_depends() {
        return ['ps-vertex', 'prime-slider-font'];
    }

    public function get_script_depends() {
        $reveal_effects = prime_slider_option('reveal-effects', 'prime_slider_other_settings', 'off');
        if ('on' === $reveal_effects) {
            if ( true === _is_ps_pro_activated() ) {
                return ['anime', 'revealFx', 'ps-vertex'];
            } else {
                return ['ps-vertex'];
            }
        } else {
            return ['ps-vertex'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/ktEB9YdU8A8';
    }

    protected function register_controls() {
        $reveal_effects = prime_slider_option('reveal-effects', 'prime_slider_other_settings', 'off');
        $this->start_controls_section(
            'section_content_layout',
            [
                'label' => esc_html__('Layout', 'bdthemes-prime-slider'),
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label'          => __('Columns', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type'           => Controls_Manager::SELECT,
                'default'        => 3,
                'tablet_default' => 3,
                'mobile_default' => 1,
                'options'        => [
                    1 => '1',
                    3 => '3',
                    5 => '5',
                ],
                'classes'   => BDTPS_CORE_IS_PC
            ]
        );

        $this->add_responsive_control(
            'slider_height',
            [
                'label' => esc_html__('Height', 'bdthemes-prime-slider'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-item' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-img-wrap .bdt-img' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'vertical_spacing',
            [
                'label' => esc_html__('Vertical Spacing', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .swiper-vertex' => 'padding: {{SIZE}}{{UNIT}} 0;',
                ],
                'classes'   => BDTPS_CORE_IS_PC
            ]
        );

        $this->add_responsive_control(
            'content_alignment',
            [
                'label'     => esc_html__('Alignment', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => esc_html__('Left', 'bdthemes-prime-slider'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-prime-slider'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__('Right', 'bdthemes-prime-slider'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        /**
         * Primary Thumbnail Controls
         */
        $this->register_primary_thumbnail_controls();

        /**
         * Show title & title tags controls
         */
        $this->register_show_title_and_title_tags_controls();

        /**
         * Show Category Controls
         */
        $this->register_show_category_controls();

        $this->end_controls_section();

        //New Query Builder Settings
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
            'section_slider_settings',
            [
                'label' => __('Slider Settings', 'bdthemes-prime-slider'),
            ]
        );

        /**
		 * Autoplay Controls
		 */
		$this->register_autoplay_controls();

        $this->add_responsive_control(
            'slides_to_scroll',
            [
                'type'      => Controls_Manager::SELECT,
                'label'     => esc_html__('Slides to Scroll', 'bdthemes-prime-slider'),
                'default'        => 1,
                'tablet_default' => 1,
                'mobile_default' => 1,
                'options'   => [
                    1 => '1',
                    2 => '2',
                    3 => '3',
                    4 => '4',
                    5 => '5',
                    6 => '6',
                ],
            ]
        );

        $this->add_control(
            'centered_slides',
            [
                'label'   => __('Center Slide', 'bdthemes-prime-slider'),
                'description'   => __('Use even items from Layout > Columns settings for better preview.', 'bdthemes-prime-slider'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes'
            ]
        );

        /**
		 * Grab Cursor Controls
		 */
		$this->register_grab_cursor_controls();

        /**
		 * Free Mode Controls
		 */
		$this->register_free_mode_controls();

        /**
		 * Loop Controls
		 */
		$this->register_loop_controls();

        /**
		 * Speed & Observer Controls
		 */
		$this->register_speed_observer_controls();

        $this->end_controls_section();

        /**
         * Reveal Effects
         */
        if ('on' === $reveal_effects) {
            $this->register_reveal_effects();
        }

        //style
        $this->start_controls_section(
            'section_style_layout',
            [
                'label'     => __('Sliders', 'bdthemes-prime-slider'),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label'      => __('Content Padding', 'bdthemes-prime-slider'),
                'type'          => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-prime-slider'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-img-wrap .bdt-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'border_style_color',
            [
                'label'     => esc_html__('Border Line Color', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .swiper-vertex::before' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-img-wrap .bdt-img' => 'border-color: {{VALUE}};',
                ],
                'separator' => 'before'
            ]
        );

        $this->add_responsive_control(
            'border_width_type',
            [
                'label'     => esc_html__('Border Line Width', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type'      => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .swiper-vertex::before' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-img-wrap .bdt-img' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
                'classes'   => BDTPS_CORE_IS_PC
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_title',
            [
                'label'     => esc_html__('Title', 'bdthemes-prime-slider'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label'     => esc_html__('Hover Color', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'title_typography',
                'label'     => esc_html__('Typography', 'bdthemes-prime-slider'),
                'selector'  => '{{WRAPPER}} .bdt-vertex-slider .bdt-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow',
                'label' => __('Text Shadow', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-vertex-slider .bdt-title a',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_category',
            [
                'label'     => esc_html__('Category', 'bdthemes-prime-slider'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_category' => 'yes'
                ],
            ]
        );

        $this->add_responsive_control(
            'category_bottom_spacing',
            [
                'label'     => esc_html__('Spacing', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_category_style');

        $this->start_controls_tab(
            'tab_category_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-prime-slider'),
            ]
        );

        $this->add_control(
            'category_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-category a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'category_background',
                'selector' => '{{WRAPPER}} .bdt-vertex-slider .bdt-category a',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'category_border',
                'selector' => '{{WRAPPER}} .bdt-vertex-slider .bdt-category a',
            ]
        );

        $this->add_responsive_control(
            'category_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-prime-slider'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_space_between',
            [
                'label'     => esc_html__('Space Between', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'category_shadow',
                'selector' => '{{WRAPPER}} .bdt-vertex-slider .bdt-category a',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'category_typography',
                'label'    => esc_html__('Typography', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-vertex-slider .bdt-category a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_category_hover',
            [
                'label'     => esc_html__('Hover', 'bdthemes-prime-slider'),
            ]
        );

        $this->add_control(
            'category_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-category a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'category_hover_background',
                'selector' => '{{WRAPPER}} .bdt-vertex-slider .bdt-category a:hover',
            ]
        );

        $this->add_control(
            'category_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'category_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-category a:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        //Navigation Css
        $this->start_controls_section(
            'section_style_navigation',
            [
                'label'     => __('Navigation', 'bdthemes-prime-slider'),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_navigation_arrows_style');

        $this->start_controls_tab(
            'tabs_nav_arrows_normal',
            [
                'label'     => __('Normal', 'bdthemes-prime-slider'),
            ]
        );

        $this->add_control(
            'arrows_color',
            [
                'label'     => __('Color', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-next, {{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-prev' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'arrows_background',
                'selector' => '{{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-next, {{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-prev',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'arrows_border',
                'selector' => '{{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-next, {{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-prev',
            ]
        );

        $this->add_responsive_control(
            'arrows_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-prime-slider'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-next, {{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_size',
            [
                'label'     => esc_html__('Size', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-next, {{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-prev' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'arrows_shadow',
                'selector' => '{{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-next, {{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-prev',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'arrows_typography',
                'label'    => esc_html__('Icon Typography', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-next, {{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-prev',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tabs_nav_arrows_hover',
            [
                'label'     => __('Hover', 'bdthemes-prime-slider'),
            ]
        );

        $this->add_control(
            'arrows_hover_color',
            [
                'label'     => __('Color', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-next:hover, {{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-prev:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'arrows_hover_background',
                'selector' => '{{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-next::before, {{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-prev::before',
            ]
        );

        $this->add_control(
            'arrows_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'arrows_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-next:hover, {{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-prev:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'arrows_hover_shadow',
                'selector' => '{{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-next:hover, {{WRAPPER}} .bdt-vertex-slider .bdt-navigation-wrap .bdt-navigation-prev:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    public function query_posts() {
        $settings = $this->get_settings();

        $args = [];

        if ($settings['posts_limit']) {
            $args['posts_per_page'] = $settings['posts_limit'];
            $args['paged']          = max(1, get_query_var('paged'), get_query_var('page'));
        }

        $default = $this->getGroupControlQueryArgs();
        $args = array_merge($default, $args);

        $query = new WP_Query($args);

        return $query;
    }

    public function render_category() {
        if (!$this->get_settings('show_category')) {
            return;
        }

        ?>
        <div class="bdt-category" data-reveal="reveal-active" data-swiper-parallax="-200" data-swiper-parallax-duration="700">
            <?php echo get_the_category_list(' '); ?>
        </div>
        <?php
    }

    protected function render_header() {
        $settings   = $this->get_settings_for_display();
        $id         = 'bdt-prime-slider-' . $this->get_id();

        $this->add_render_attribute('prime-slider-vertex', 'id', $id);
        $this->add_render_attribute('prime-slider-vertex', 'class', ['bdt-vertex-slider', 'bdt-prime-slider-vertex', 'elementor-swiper']);

        $this->add_render_attribute('prime-slider', 'class', 'bdt-prime-slider');
        /**
		 * Reveal Effects
		 */
		$this->reveal_effects_attr('prime-slider-vertex');

        $elementor_vp_lg = get_option('elementor_viewport_lg');
        $elementor_vp_md = get_option('elementor_viewport_md');
        $viewport_lg     = !empty($elementor_vp_lg) ? $elementor_vp_lg - 1 : 1023;
        $viewport_md     = !empty($elementor_vp_md) ? $elementor_vp_md - 1 : 767;

        $this->add_render_attribute(
            [
                'prime-slider-vertex' => [
                    'data-settings' => [
                        wp_json_encode(array_filter([
                            "autoplay"       => ("yes" == $settings["autoplay"]) ? ["delay" => $settings["autoplay_speed"]] : false,
                            "loop"           => ($settings["loop"] == "yes") ? true : false,
                            "speed"          => $settings["speed"]["size"],
                            "pauseOnHover"   => ("yes" == $settings["pauseonhover"]) ? true : false,
                            "slidesPerView"  => isset($settings["columns_mobile"]) ? (int)$settings["columns_mobile"] : 1,
                            "slidesPerGroup" => isset($settings["slides_to_scroll_mobile"]) ? (int)$settings["slides_to_scroll_mobile"] : 1,
                            "spaceBetween"   => 0,
                            "centeredSlides" => ($settings["centered_slides"] === "yes") ? true : false,
                            "grabCursor"     => ($settings["grab_cursor"] === "yes") ? true : false,
                            "freeMode"       => ($settings["free_mode"] === "yes") ? true : false,
                            "effect"         => 'slide',
                            "parallax"       => true,
                            "observer"       => ($settings["observer"]) ? true : false,
                            "observeParents" => ($settings["observer"]) ? true : false,
                            "breakpoints"    => [
                                (int) $viewport_md => [
                                    "slidesPerView"  => isset($settings["columns_tablet"]) ? (int)$settings["columns_tablet"] : 3,
                                    "slidesPerGroup" => isset($settings["slides_to_scroll_tablet"]) ? (int)$settings["slides_to_scroll_tablet"] : 1,
                                ],
                                (int) $viewport_lg => [
                                    "slidesPerView"  => isset($settings["columns"]) ? (int)$settings["columns"] : 3,
                                    "slidesPerGroup" => isset($settings["slides_to_scroll"]) ? (int)$settings["slides_to_scroll"] : 1,
                                ]
                            ],
                            "navigation" => [
                                "nextEl" => "#" . $id . " .bdt-navigation-next",
                                "prevEl" => "#" . $id . " .bdt-navigation-prev",
                            ],
                            "lazy" => [
                                "loadPrevNext"  => "true",
                            ],
                        ]))
                    ]
                ]
            ]
        );

        $swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'swiper-vertex ' . $swiper_class);

        ?>
        <div <?php $this->print_render_attribute_string('prime-slider'); ?>>
        <div <?php $this->print_render_attribute_string('prime-slider-vertex'); ?>>
        <div <?php $this->print_render_attribute_string('swiper'); ?>>
                <div class="swiper-wrapper">
        <?php
    }

    public function render_footer() {
        $settings = $this->get_settings_for_display();
        ?>
                </div>

                <div class="bdt-navigation-wrap reveal-muted">
                    <div class="bdt-navigation-prev">
                        <i class="ps-wi-arrow-left"></i>
                    </div>
                    <div class="bdt-navigation-next">
                        <i class="ps-wi-arrow-right"></i>
                    </div>
                </div>

            </div>
        </div>
        </div>
        <?php
    }

    public function render_slider_item($post_id, $image_size, $slide_index) {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('slider-item', 'class', 'bdt-item swiper-slide', true);

        ?>
        <div <?php echo $this->get_render_attribute_string('slider-item'); ?>>
            <div class="bdt-img-wrap reveal-muted">
                <?php $this->render_image($post_id, $image_size); ?>
            </div>
            <div class="bdt-content">
                <?php $this->render_category(); ?>
                <?php if ($settings['show_title']) : ?>
                    <div data-swiper-parallax="-300" data-swiper-parallax-duration="900">
                        <?php $this->render_post_title(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php
    }

    public function render() {
        $settings = $this->get_settings_for_display();

        $wp_query = $this->query_posts();
        if (!$wp_query->found_posts) {
            return;
        }

        $slide_index = 1;
        $this->render_header();

        while ($wp_query->have_posts()) {
            $wp_query->the_post();
            $thumbnail_size = $settings['primary_thumbnail_size'];

            $this->render_slider_item(get_the_ID(), $thumbnail_size, $slide_index);
            $slide_index++;
        }

        $this->render_footer();

        wp_reset_postdata();
    }
}
