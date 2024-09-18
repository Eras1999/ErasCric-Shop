<?php

    namespace PrimeSlider\Modules\Mercury\Widgets;

    use Elementor\Controls_Manager;
    use Elementor\Group_Control_Background;
    use Elementor\Group_Control_Border;
    use Elementor\Group_Control_Box_Shadow;
    use Elementor\Group_Control_Typography;
    use Elementor\Group_Control_Text_Shadow;
    use Elementor\Widget_Base;
    use Elementor\Plugin;

    use PrimeSlider\Traits\Global_Widget_Controls;
    use PrimeSlider\Traits\QueryControls\GroupQuery\Group_Control_Query;
    use PrimeSlider\Utils;
    use WP_Query;

    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

// Exit if accessed directly

class Mercury extends Widget_Base {
    use Group_Control_Query;
    use Global_Widget_Controls;

    public function get_name() {
        return 'prime-slider-mercury';
    }

    public function get_title() {
        return BDTPS . esc_html__( 'Mercury', 'bdthemes-prime-slider' );
    }

    public function get_icon() {
        return 'bdt-widget-icon ps-wi-mercury';
    }

    public function get_categories() {
        return ['prime-slider'];
    }

    public function get_keywords() {
        return ['prime slider', 'slider', 'mercury', 'prime', 'blog', 'post', 'news'];
    }

    public function get_style_depends() {
        return ['ps-mercury', 'prime-slider-font'];
    }

    public function get_script_depends() {
        $reveal_effects = prime_slider_option('reveal-effects', 'prime_slider_other_settings', 'off');
        if ('on' === $reveal_effects) {
            if ( true === _is_ps_pro_activated() ) {
                return ['shutters', 'gl', 'slicer', 'tinder', 'anime', 'revealFx', 'ps-mercury'];
            } else {
                return ['shutters', 'gl', 'slicer', 'tinder', 'ps-mercury'];
            }
        } else {
            return ['shutters', 'gl', 'slicer', 'tinder', 'ps-mercury'];
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/4Dk1ysRtGWk';
    }

    protected function register_controls() {
        $reveal_effects = prime_slider_option('reveal-effects', 'prime_slider_other_settings', 'off');
        $this->start_controls_section(
            'section_content_layout',
            [
                'label' => esc_html__( 'Layout', 'bdthemes-prime-slider' ),
            ]
        );

        $this->add_responsive_control(
            'item_height',
            [
                'label' => esc_html__('Image Height', 'bdthemes-prime-slider'),
                'type'  => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'vh' ],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1080,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors'   => [
                    '{{WRAPPER}} .bdt-mercury-image-slider .bdt-item' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_max_width',
            [
                'label' => esc_html__('Content Max Width', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1200,
                    ],
                ],
                'selectors'   => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-content' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'classes'    => BDTPS_CORE_IS_PC
            ]
        );

        $this->add_control(
            'content_reverse',
            [
                'label'   => esc_html__( 'Content Reverse', 'bdthemes-prime-slider' ) . BDTPS_CORE_PC,
                'type'    => Controls_Manager::SWITCHER,
                'prefix_class' => 'bdt-reverse--',
                'render_type' => 'template',
                'classes'    => BDTPS_CORE_IS_PC
            ]
        );

        $this->add_responsive_control(
            'content_alignment',
            [
                'label'     => esc_html__( 'Alignment', 'bdthemes-prime-slider' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => esc_html__( 'Left', 'bdthemes-prime-slider' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'bdthemes-prime-slider' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__( 'Right', 'bdthemes-prime-slider' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        /**
         * Primary Thumbnail Controls
         */
        $this->register_primary_thumbnail_controls();

        $this->end_controls_section();

        //New Query Builder Settings
        $this->start_controls_section(
            'section_post_query_builder',
            [
                'label' => __( 'Query', 'bdthemes-prime-slider' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->register_query_builder_controls();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_additional_settings',
            [
                'label' => esc_html__( 'Additional Options', 'bdthemes-prime-slider' ),
            ]
        );

        /**
         * Show title & title tags controls
         */
        $this->register_show_title_and_title_tags_controls();

        /**
         * Show Post Excerpt Controls
         */
        $this->register_show_post_excerpt_controls();

        /**
         * Show Category Controls
         */
        $this->register_show_category_controls();

        /**
         * Show Author Controls
         */
        $this->register_show_author_controls();

        /**
         * Meta Separator Controls
         */
        $this->register_meta_separator_controls();

        /**
         * Show date & human diff time Controls
         */
        $this->register_show_date_and_human_diff_time_controls();

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

        /**
		 * Grab Cursor Controls
		 */
		$this->register_grab_cursor_controls();

        /**
		 * Loop, Rewind & mousewheel Controls
		 */
		
        $this->add_control(
			'loop',
			[ 
				'label'     => __( 'Loop', 'bdthemes-prime-slider' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [ 
					'effect!' => [ 'slicer', 'tinder' ]
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
					'effect' => [ 'slicer', 'tinder' ]
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

        /**
		 * Speed & Observer Controls
		 */
		$this->register_speed_observer_controls();

        $this->add_control(
            'effect',
            [
                'label'   => esc_html__( 'Swiper Effect', 'bdthemes-prime-slider' ) . BDTPS_CORE_NC,
                'type'    => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => esc_html__( 'Slide', 'bdthemes-prime-slider' ),
                    'fade'  => esc_html__( 'Fade', 'bdthemes-prime-slider' ),
                    'cube'  => esc_html__( 'Cube', 'bdthemes-prime-slider' ),
                    'coverflow' => esc_html__( 'Coverflow', 'bdthemes-prime-slider' ),
                    'flip'  => esc_html__( 'Flip', 'bdthemes-prime-slider' ),
                    'shutters' => esc_html__( 'Shutters', 'bdthemes-prime-slider' ),
                    'slicer' => esc_html__( 'Slicer', 'bdthemes-prime-slider' ),
                    'tinder' => esc_html__( 'Tinder', 'bdthemes-prime-slider' ),
                    'gl'    => esc_html__( 'GL', 'bdthemes-prime-slider' ),
                    'creative' => esc_html__( 'Creative', 'bdthemes-prime-slider' ),
                ],
            ]
        );
        //gl_shader control
        $this->add_control(
            'gl_shader',
            [
                'label'   => esc_html__( 'GL Shader', 'bdthemes-prime-slider' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'random',
                'options' => [
                    'random' => esc_html__( 'random', 'bdthemes-prime-slider'),
                    'dots' => esc_html__( 'dots', 'bdthemes-prime-slider'),
                    'flyeye' => esc_html__( 'flyeye', 'bdthemes-prime-slider'),
                    'morph-x' => esc_html__( 'morph-x', 'bdthemes-prime-slider'),
                    'morph-y' => esc_html__( 'morph-y', 'bdthemes-prime-slider'),
                    'page-curl' => esc_html__( 'page-curl', 'bdthemes-prime-slider'),
                    'peel-x' => esc_html__( 'peel-x', 'bdthemes-prime-slider'),
                    'peel-y' => esc_html__( 'peel-y', 'bdthemes-prime-slider'),
                    'polygons-fall' => esc_html__( 'polygons-fall', 'bdthemes-prime-slider'),
                    'polygons-morph' => esc_html__( 'polygons-morph', 'bdthemes-prime-slider'),
                    'polygons-wind' => esc_html__( 'polygons-wind', 'bdthemes-prime-slider'),
                    'pixelize' => esc_html__( 'pixelize', 'bdthemes-prime-slider'),
                    'ripple' => esc_html__( 'ripple', 'bdthemes-prime-slider'),
                    'shutters' => esc_html__( 'shutters', 'bdthemes-prime-slider'),
                    'slices' => esc_html__( 'slices', 'bdthemes-prime-slider'),
                    'squares' => esc_html__( 'squares', 'bdthemes-prime-slider'),
                    'stretch' => esc_html__( 'stretch', 'bdthemes-prime-slider'),
                    'wave-x' => esc_html__( 'wave-x', 'bdthemes-prime-slider'),
                    'wind' => esc_html__( 'wind', 'bdthemes-prime-slider'),
                ],
                'condition' => [
                    'effect' => 'gl',
                ],
            ]
        );
    
        //creative effect control
        $this->add_control(
            'creative_effect',
            [
                'label'   => esc_html__( 'Creative Effect', 'bdthemes-prime-slider' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'creative-1',
                'options' => [
                    'creative-1' => esc_html__( 'Creative 1', 'bdthemes-prime-slider' ),
                    'creative-2' => esc_html__( 'Creative 2', 'bdthemes-prime-slider' ),
                    'creative-3' => esc_html__( 'Creative 3', 'bdthemes-prime-slider' ),
                    'creative-4' => esc_html__( 'Creative 4', 'bdthemes-prime-slider' ),
                    'creative-5' => esc_html__( 'Creative 5', 'bdthemes-prime-slider' ),
                ],
                'condition' => [
                    'effect' => 'creative',
                ],
            ]
        );

        $this->add_control(
            'show_navigation',
            [
                'label' => __('Show Navigation', 'bdthemes-prime-slider'),
                'type'  => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before'
            ]
        );

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
                'label'     => __( 'Items', 'bdthemes-prime-slider' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'Background',
                'selector'  => '{{WRAPPER}}  .bdt-mercury-slider',
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' 	 => __('Content Padding', 'bdthemes-prime-slider'),
                'type' 		 => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
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
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label'     => esc_html__('Hover Color', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label'      => esc_html__('Spacing', 'bdthemes-prime-slider'),
                'type'       => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 100,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'title_typography',
                'label'     => esc_html__('Typography', 'bdthemes-prime-slider'),
                'selector'  => '{{WRAPPER}} .bdt-mercury-content-slider .bdt-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow',
                'label' => __('Text Shadow', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-mercury-content-slider .bdt-title a',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_text',
            [
                'label'     => esc_html__('Text', 'bdthemes-prime-slider'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-desc' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_margin',
            [
                'label' 	 => __('Margin', 'bdthemes-prime-slider'),
                'type' 		 => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_width',
            [
                'label' => esc_html__('Max Width', 'bdthemes-prime-slider'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 800,
                    ],
                ],
                'selectors'   => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-desc' => 'max-width: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'text_typography',
                'label'     => esc_html__('Typography', 'bdthemes-prime-slider'),
                'selector'  => '{{WRAPPER}} .bdt-mercury-content-slider .bdt-desc',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_meta',
            [
                'label'      => esc_html__( 'Meta', 'bdthemes-prime-slider' ),
                'tab'        => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms'    => [
                        [
                            'name'  => 'show_author',
                            'value' => 'yes'
                        ],
                        [
                            'name'  => 'show_date',
                            'value' => 'yes'
                        ]
                    ]
                ],
            ]
        );

        $this->add_control(
            'meta_color',
            [
                'label'     => esc_html__( 'Color', 'bdthemes-prime-slider' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-meta, {{WRAPPER}} .bdt-mercury-content-slider .bdt-author a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'meta_hover_color',
            [
                'label'     => esc_html__( 'Author Hover Color', 'bdthemes-prime-slider' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-author a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_space_between',
            [
                'label'     => esc_html__( 'Space Between', 'bdthemes-prime-slider' ),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-separator' => 'margin: 0 {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'meta_typography',
                'label'    => esc_html__( 'Typography', 'bdthemes-prime-slider' ),
                'selector' => '{{WRAPPER}} .bdt-mercury-content-slider .bdt-meta',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_category',
            [
                'label'     => esc_html__( 'Category', 'bdthemes-prime-slider' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_category' => 'yes'
                ],
            ]
        );

        $this->add_responsive_control(
            'category_bottom_spacing',
            [
                'label'     => esc_html__( 'Spacing', 'bdthemes-prime-slider' ),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_category_style' );

        $this->start_controls_tab(
            'tab_category_normal',
            [
                'label' => esc_html__( 'Normal', 'bdthemes-prime-slider' ),
            ]
        );

        $this->add_control(
            'category_color',
            [
                'label'     => esc_html__( 'Color', 'bdthemes-prime-slider' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-category a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'category_background',
                'selector' => '{{WRAPPER}} .bdt-mercury-content-slider .bdt-category a',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'           => 'category_border',
                'label'          => __( 'Border', 'bdthemes-prime-slider' ),
                'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'width'  => [
                        'default' => [
                            'top'      => '1',
                            'right'    => '1',
                            'bottom'   => '1',
                            'left'     => '1',
                            'isLinked' => false,
                        ],
                    ],
                    'color'  => [
                        'default' => '#e7e7e7',
                    ],
                ],
                'selector'       => '{{WRAPPER}} .bdt-mercury-content-slider .bdt-category a',
            ]
        );

        $this->add_responsive_control(
            'category_border_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'bdthemes-prime-slider' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_padding',
            [
                'label'      => esc_html__( 'Padding', 'bdthemes-prime-slider' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'category_space_between',
            [
                'label'     => esc_html__( 'Space Between', 'bdthemes-prime-slider' ),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-category a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'category_shadow',
                'selector' => '{{WRAPPER}} .bdt-mercury-content-slider .bdt-category a',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'category_typography',
                'label'    => esc_html__( 'Typography', 'bdthemes-prime-slider' ),
                'selector' => '{{WRAPPER}} .bdt-mercury-content-slider .bdt-category a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_category_hover',
            [
                'label'     => esc_html__( 'Hover', 'bdthemes-prime-slider' ),
            ]
        );

        $this->add_control(
            'category_hover_color',
            [
                'label'     => esc_html__( 'Color', 'bdthemes-prime-slider' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-category a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'category_hover_background',
                'selector' => '{{WRAPPER}} .bdt-mercury-content-slider .bdt-category a:hover',
            ]
        );

        $this->add_control(
            'category_hover_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'bdthemes-prime-slider' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'category_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-content-slider .bdt-category a:hover' => 'border-color: {{VALUE}};',
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
                'label'     => __( 'Navigation', 'bdthemes-prime-slider' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'tabs_navigation_arrows_style' );

        $this->start_controls_tab(
            'tabs_nav_arrows_normal',
            [
                'label'     => __( 'Normal', 'bdthemes-prime-slider' ),
            ]
        );

        $this->add_control(
            'arrows_color',
            [
                'label'     => __( 'Color', 'bdthemes-prime-slider' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-next, {{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-prev' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'arrows_background',
                'selector' => '{{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-next, {{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-prev',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'arrows_border',
                'selector' => '{{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-next, {{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-prev',
            ]
        );

        $this->add_responsive_control(
            'arrows_border_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'bdthemes-prime-slider' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-next, {{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_size',
            [
                'label'     => esc_html__( 'Size', 'bdthemes-prime-slider' ),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-next, {{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-prev' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_horizontal_offset',
            [
                'label'     => esc_html__( 'Horizontal Offset', 'bdthemes-prime-slider' ) . BDTPS_CORE_PC,
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-next' => 'right: {{SIZE}}px;',
                    '{{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-prev' => 'left: {{SIZE}}px;',
                ],
                'classes'    => BDTPS_CORE_IS_PC
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'arrows_shadow',
                'selector' => '{{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-next, {{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-prev',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'arrows_typography',
                'label'    => esc_html__( 'Icon Typography', 'bdthemes-prime-slider' ),
                'selector' => '{{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-next, {{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-prev',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tabs_nav_arrows_hover',
            [
                'label'     => __( 'Hover', 'bdthemes-prime-slider' ),
            ]
        );

        $this->add_control(
            'arrows_hover_color',
            [
                'label'     => __( 'Color', 'bdthemes-prime-slider' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-next:hover, {{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-prev:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'arrows_hover_background',
                'selector' => '{{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-next:hover, {{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-prev:hover',
            ]
        );

        $this->add_control(
            'arrows_hover_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'bdthemes-prime-slider' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'arrows_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-next:hover, {{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-prev:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'arrows_hover_shadow',
                'selector' => '{{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-next:hover, {{WRAPPER}} .bdt-mercury-image-slider .bdt-navigation-wrap .bdt-button-prev:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();


    }

    public function query_posts() {
        $settings = $this->get_settings();

        $args = [];

        if ( $settings['posts_limit'] ) {
            $args['posts_per_page'] = $settings['posts_limit'];
            $args['paged']          = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
        }

        $default = $this->getGroupControlQueryArgs();
        $args = array_merge( $default, $args );

        $query = new WP_Query( $args );

        return $query;
    }

    public function render_excerpt($excerpt_length) {

        if (!$this->get_settings('show_excerpt')) {
            return;
        }
        $strip_shortcode = $this->get_settings_for_display('strip_shortcode');
        ?>
        <div class="bdt-desc" data-reveal="reveal-active" data-swiper-parallax="-150" data-swiper-parallax-duration="1100">
            <?php
            if (has_excerpt()) {
                the_excerpt();
            } else {
                echo wp_kses_post(prime_slider_custom_excerpt($excerpt_length, $strip_shortcode));
            }
            ?>
        </div>
        <?php
    }

    public function render_category() {
        if ( !$this->get_settings( 'show_category' ) ) {
            return;
        }

        ?>
        <div class="bdt-category" data-reveal="reveal-active" data-swiper-parallax="-300" data-swiper-parallax-duration="600">
            <?php echo wp_kses_post(get_the_category_list( ' ' )); ?>
        </div>
        <?php
    }

    public function render_date() {
        $settings = $this->get_settings_for_display();


        if ( ! $this->get_settings( 'show_date' ) ) {
            return;
        }

        ?>
        <div class="bdt-flex bdt-flex-middle">
            <div class="bdt-date">
                <i class="ps-wi-calendar" aria-hidden="true"></i>
                <span>
                    <?php if ( $settings['human_diff_time'] == 'yes' ) {
                        echo wp_kses_post(prime_slider_post_time_diff( ( $settings['human_diff_time_short'] == 'yes' ) ? 'short' : '' ));
                    } else {
                        echo get_the_date();
                    } ?>
                </span>

            </div>
            <?php if ($settings['show_time']) : ?>
            <div class="bdt-post-time">
                <i class="ps-wi-clock-o" aria-hidden="true"></i>
                <?php echo wp_kses_post(get_the_time()); ?>
            </div>
            <?php endif; ?>
        </div>

        <?php
    }

    public function render_author() {

        if ( ! $this->get_settings( 'show_author' ) ) {
            return;
        }
        ?>
        <div class="bdt-author">
            <span class="bdt-by"><?php echo esc_html__( 'by', 'bdthemes-prime-slider' ) ?></span>
            <a class="bdt-author-name" href="<?php echo esc_url(get_author_posts_url( get_the_author_meta( 'ID' ) )); ?>">
                <?php echo get_the_author() ?>
            </a>
        </div>
        <?php
    }

    public function render_button() {
        $settings   = $this->get_settings_for_display();
        if ( ! $this->get_settings( 'show_button' ) ) {
            return;
        }
        ?>
        <div class="bdt-mercury-play-and-button-wrap">
            <div class="bdt-mercury-button-wrap" data-swiper-parallax-y ="-50" data-swiper-parallax-duration="700">
                <a href="<?php echo esc_url(get_permalink()); ?>">
                    <span><?php echo esc_html( $settings['button_text'] ); ?></span>
                    <i class="ps-wi-arrow-right"></i>
                </a>
            </div>
        </div>
        <?php
    }

    protected function render_header() {
        $settings   = $this->get_settings_for_display();
        $id         = 'bdt-prime-slider-' . $this->get_id();

        $this->add_render_attribute('prime-slider', 'class', 'bdt-prime-slider-mercury');
        
        /**
         * Reveal Effects
         */
        $this->reveal_effects_attr('prime-slider-mercury');

        $this->add_render_attribute( 'prime-slider-mercury', 'id', $id );
        $this->add_render_attribute( 'prime-slider-mercury', 'class', [ 'bdt-mercury-slider', 'elementor-swiper' ] );

        $this->add_render_attribute(
            [
                'prime-slider-mercury' => [
                    'data-settings' => [
                        wp_json_encode(array_filter([
                            "autoplay"       => ("yes" == $settings["autoplay"]) ? ["delay" => $settings["autoplay_speed"]] : false,
                            "loop"           => ($settings["loop"] == "yes") ? true : false,
                            "rewind"         => (isset($settings["rewind"]) && $settings["rewind"] == "yes") ? true : false,
                            "speed"          => $settings["speed"]["size"],
                            "effect"        => isset($settings["effect"]) ? $settings["effect"] : 'slide',
                            "gl"             => [
                                'shader' => isset($settings["gl_shader"]) ? $settings["gl_shader"] : 'random',
                            ],
                            "creativeEffect" => isset($settings["creative_effect"]) ? $settings["creative_effect"] : false,
                            "fadeEffect"     => ['crossFade' => true],
                            "lazy"           => true,
                            "parallax"       => true,
                            "watchSlidesProgress" => true,
                            "slidesPerGroupAuto" => false,
                            "mousewheel"     => ($settings["mousewheel"] === "yes") ? true : false,
                            "grabCursor"     => ($settings["grab_cursor"] === "yes") ? true : false,
                            "pauseOnHover"   => ("yes" == $settings["pauseonhover"]) ? true : false,
                            "slidesPerView"  => 1,
                            "loopedSlides"   => 4,
                            "observer"       => ($settings["observer"]) ? true : false,
                            "observeParents" => ($settings["observer"]) ? true : false,

                            "navigation" => [
                                "nextEl" => "#" . $id . " .bdt-button-next",
                                "prevEl" => "#" . $id . " .bdt-button-prev",
                            ],
                            "pagination" => [
                                "el"             => "#" . $id . " .swiper-pagination",
                                "clickable"      => "true",
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
        $this->add_render_attribute('swiper', 'class', 'bdt-mercury-image-slider ' . $swiper_class);

        ?>
        <div <?php $this->print_render_attribute_string( 'prime-slider' ); ?>>
        <div <?php $this->print_render_attribute_string( 'prime-slider-mercury' ); ?>>
            <div <?php $this->print_render_attribute_string( 'swiper' ); ?>>
                <div class="swiper-wrapper">
        <?php
    }

    public function render_footer() {
        $settings = $this->get_settings_for_display();
        ?>
                </div>
                <?php if ($settings['show_navigation']) : ?>
                <div class="bdt-navigation-wrap reveal-muted">
                    <div class="bdt-button-next">
                        <i class="ps-wi-arrow-right"></i>
                    </div>
                    <div class="bdt-button-prev">
                        <i class="ps-wi-arrow-left"></i>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php
    }

    public function render_thumbnav($excerpt_length) {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('thumb-item', 'class', 'bdt-item swiper-slide', true);

        ?>

        <div <?php $this->print_render_attribute_string('thumb-item'); ?>>
            <div class="bdt-content">
                <?php $this->render_category(); ?>
                <?php if ($settings['show_title']) : ?>
                    <div data-swiper-parallax="-200" data-swiper-parallax-duration="1000">
                        <?php $this->render_post_title(); ?>
                    </div>
                <?php endif; ?>

                <?php $this->render_excerpt( $excerpt_length ); ?>

                <?php if ($settings['show_author'] or $settings['show_date']) : ?>
                    <div class="bdt-meta" data-reveal="reveal-active" data-swiper-parallax="-300" data-swiper-parallax-duration="800">
                        <?php $this->render_author(); ?>
                        <span class="bdt-separator"><?php echo esc_html($settings['meta_separator']); ?></span>
                        <?php $this->render_date(); ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
        <?php
    }

    public function render_image($post_id, $size) {
        $settings = $this->get_settings_for_display();

        $placeholder_image_src = Utils::get_placeholder_image_src();
        $image_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);

        $gl = $settings['effect'] == 'gl' ? ' swiper-gl-image' : '';
        $shutters = $settings['effect'] == 'shutters' ? ' swiper-shutters-image' : '';
        $slicer = $settings['effect'] == 'slicer' ? ' swiper-slicer-image' : '';

        if (!$image_src) {
            printf('<img src="%1$s" alt="%2$s" class="bdt-img %3$s swiper-lazy">', esc_url($placeholder_image_src), esc_html(get_the_title()), esc_attr($gl.$shutters.$slicer));
        } else {
            print(wp_get_attachment_image(
                get_post_thumbnail_id(),
                $size,
                false,
                [
                    'class' => 'bdt-img swiper-lazy' . esc_attr($gl.$shutters.$slicer),
                    'alt' => esc_html(get_the_title())
                ]
            ));
        }
    }

    public function render_slider_item($post_id, $image_size) {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('slider-item', 'class', 'bdt-item  swiper-slide', true);

        ?>

        <div <?php $this->print_render_attribute_string('slider-item'); ?>>
            <div class="bdt-img-wrap" data-reveal="reveal-active">
                <?php $this->render_image($post_id, $image_size); ?>
            </div>
        </div>

        <?php
    }

    public function render() {
        $settings = $this->get_settings_for_display();

        $wp_query = $this->query_posts();
        if ( !$wp_query->found_posts ) {
            return;
        }

        $swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
        $this->add_render_attribute('swiper-thumbs', 'class', 'bdt-mercury-content-slider ' . $swiper_class);

        ?>

            <?php

            $this->render_header();

            while ($wp_query->have_posts()) {
                $wp_query->the_post();
                $thumbnail_size = $settings['primary_thumbnail_size'];

                $this->render_slider_item(get_the_ID(), $thumbnail_size);
            }

            $this->render_footer();

            ?>
            <div thumbsSlider="" <?php $this->print_render_attribute_string( 'swiper-thumbs' ); ?>>
                <div class="swiper-wrapper">
                    <?php
                    while ($wp_query->have_posts()) {
                        $wp_query->the_post();
                        $this->render_thumbnav($settings['excerpt_length']);
                    }
                    ?>
                </div>
            </div>

        </div>
        </div>
        <?php
        wp_reset_postdata();
    }
}