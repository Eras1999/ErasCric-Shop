<?php

namespace PrimeSlider\Modules\Isolate\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;
use PrimeSlider\Modules\Isolate\Skins;
use PrimeSlider\Traits\Global_Widget_Controls;
use PrimeSlider\Utils;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

class Isolate extends Widget_Base {

    use Global_Widget_Controls;

    public function get_name() {
        return 'prime-slider-isolate';
    }

    public function get_title() {
        return BDTPS . esc_html__('Isolate', 'bdthemes-prime-slider');
    }

    public function get_icon() {
        return 'bdt-widget-icon ps-wi-isolate';
    }

    public function get_categories() {
        return ['prime-slider'];
    }

    public function get_keywords() {
        return ['prime slider', 'slider', 'isolate', 'prime'];
    }

    public function get_style_depends() {
        return ['elementor-icons-fa-solid', 'elementor-icons-fa-brands', 'prime-slider-font', 'ps-isolate'];
    }

    public function get_script_depends() {
        $reveal_effects = prime_slider_option('reveal-effects', 'prime_slider_other_settings', 'off');
        if ('on' === $reveal_effects) {
            if ( true === _is_ps_pro_activated() ) {
                return ['gsap', 'split-text', 'anime', 'revealFx', 'ps-isolate'];
            } else {
                return [];
            }
        } else {
            if ( true === _is_ps_pro_activated() ) {
                return ['gsap', 'split-text', 'ps-isolate'];
            } else {
                return [];
            }
        }
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/8wlCWhSMQno';
    }

    public function register_skins() {
        $this->add_skin(new Skins\Skin_Locate($this));
        $this->add_skin(new Skins\Skin_Slice($this));
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

        $repeater->start_controls_tabs('tabs_slider_item_content');

        $repeater->start_controls_tab(
            'tab_slider_content',
            [
                'label' => __('Content', 'bdthemes-prime-slider'),
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

        $repeater->add_control(
			'image_link_type',
			[
				'label'       => esc_html__('Lightbox/Link', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'youtube',
				'label_block' => true,
				'options'     => [
					''           => esc_html__('Selected Image', 'bdthemes-element-pack'),
					'website'    => esc_html__('Website', 'bdthemes-element-pack'),
					'video'      => esc_html__('Video', 'bdthemes-element-pack'),
					'youtube'    => esc_html__('YouTube', 'bdthemes-element-pack'),
					'vimeo'      => esc_html__('Vimeo', 'bdthemes-element-pack'),
					'google-map' => esc_html__('Google Map', 'bdthemes-element-pack'),
				],
			]
		);

		$repeater->add_control(
			'image_link_video',
			[
				'label'         => __('Video Source', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'default'       => [
					'url' => '//clips.vorwaerts-gmbh.de/big_buck_bunny.mp4',
				],
				'placeholder'   => '//example.com/video.mp4',
				'label_block'   => true,
				'condition'     => [
					'image_link_type' => 'video',
				],
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'lightbox_link',
			[
				'label'         => __('YouTube Source', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'default'       => [
					'url' => 'https://www.youtube.com/watch?v=YE7VzlLtp-4',
				],
				'placeholder'   => 'https://youtube.com/watch?v=xyzxyz',
				'label_block'   => true,
				'condition'     => [
					'image_link_type' => 'youtube',
				],
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'image_link_vimeo',
			[
				'label'         => __('Vimeo Source', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'default'       => [
					'url' => 'https://vimeo.com/1084537',
				],
				'placeholder'   => 'https://vimeo.com/123123',
				'label_block'   => true,
				'condition'     => [
					'image_link_type' => 'vimeo',
				],
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'image_link_google_map',
			[
				'label'         => __('Goggle Map Embed URL', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'default'       => [
					'url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4740.819266853735!2d9.99008871708242!3d53.550454675412404!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x3f9d24afe84a0263!2sRathaus!5e0!3m2!1sde!2sde!4v1499675200938',
				],
				'placeholder'   => '//google.com/maps/embed?pb',
				'label_block'   => true,
				'condition'     => [
					'image_link_type' => 'google-map',
				],
				'dynamic'     => ['active' => true],
			]
		);

		$repeater->add_control(
			'image_link_website',
			[
				'name'          => 'image_link_website',
				'label'         => esc_html__('Custom Link', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'show_external' => false,
				'condition'     => [
					'image_link_type' => 'website',
				],
				'dynamic' => ['active' => true],
			]
		);

        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
            'tab_slider_optional',
            [
                'label' => __('Optional', 'bdthemes-prime-slider'),
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
                'label' => esc_html__('Slider Items', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'sub_title' => esc_html__('Sub Title', 'bdthemes-prime-slider'),
                        'title' => esc_html__('Massive', 'bdthemes-prime-slider'),
                        'image' => ['url' => BDTPS_CORE_ASSETS_URL . 'images/gallery/img-4.svg'],
                    ],
                    [
                        'sub_title' => esc_html__('Sub Title', 'bdthemes-prime-slider'),
                        'title' => esc_html__('Vibrant', 'bdthemes-prime-slider'),
                        'image' => ['url' => BDTPS_CORE_ASSETS_URL . 'images/gallery/img-5.svg'],
                    ],
                    [
                        'sub_title' => esc_html__('Sub Title', 'bdthemes-prime-slider'),
                        'title' => esc_html__('Wallow', 'bdthemes-prime-slider'),
                        'image' => ['url' => BDTPS_CORE_ASSETS_URL . 'images/gallery/img-6.svg'],
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

        $this->add_control(
            'show_play_button',
            [
                'label' => esc_html__('Show Play Button', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    '_skin!' => ['locate'],
                ],
            ]
        );

        $this->add_control(
            'show_social_icon',
            [
                'label' => esc_html__('Show Social Icon', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    '_skin' => ['locate', 'slice'],
                ],
            ]
        );

        $this->add_control(
            'show_scroll_button',
            [
                'label' => esc_html__('Show Scroll Button', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    '_skin!' => 'slice',
                ],
            ]
        );

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
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_position',
            [
                'label' => esc_html__('Content Position', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'inherit' => [
                        'title' => esc_html__('Left', 'bdthemes-prime-slider'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'row-reverse' => [
                        'title' => esc_html__('Right', 'bdthemes-prime-slider'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'inherit',
                'toggle' => false,
                'condition' => [
                    '_skin!' => ['locate', 'slice'],
                ],
                'separator' => 'before',
                'classes' => BDTPS_CORE_IS_PC
            ]
        );

        $this->add_control(
            'content_column_position',
            [
                'label' => esc_html__('Column Position', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'column' => [
                        'title' => esc_html__('Top', 'bdthemes-prime-slider'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'column-reverse' => [
                        'title' => esc_html__('Bottom', 'bdthemes-prime-slider'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'column',
                'toggle' => false,
                'condition' => [
                    '_skin!' => ['locate', 'slice'],
                ],
                'classes' => BDTPS_CORE_IS_PC
            ]
        );

        /**
		* Thumbnail Size Controls
		*/
		$this->register_thumbnail_size_controls();

        $this->add_control(
            'image_offset_toggle',
            [
                'label' => __('Image Match Height', 'bdthemes-element-pack') . BDTPS_CORE_PC,
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __('None', 'bdthemes-element-pack'),
                'label_on' => __('Custom', 'bdthemes-element-pack'),
                'return_value' => 'yes',
                'condition' => [
                    '_skin' => '',
                ],
                'classes' => BDTPS_CORE_IS_PC
            ]
        );

        $this->start_popover();

        $this->add_control(
            'image_match_height_desktop',
            [
                'label' => esc_html__('Desktop', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'prefix_class' => 'bdt-image-match-height-desktop--',
                'render_type' => 'template',
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_control(
            'image_match_height_tablet',
            [
                'label' => esc_html__('Tablet', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'prefix_class' => 'bdt-image-match-height-tablet--',
                'render_type' => 'template',
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_control(
            'image_match_height_mobile',
            [
                'label' => esc_html__('Mobile', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'prefix_class' => 'bdt-image-match-height-mobile--',
                'render_type' => 'template',
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_control(
            'important_note',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('If you turn on this option, then no need to set exact sized image, otherwise image will set by ratio of its actual size.', 'bdthemes-prime-slider'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->end_popover();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_social_link',
            [
                'label' => __('Social Icon', 'bdthemes-prime-slider'),
                'condition' => [
                    'show_social_icon' => 'yes',
                    '_skin' => ['locate', 'slice'],
                ],
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'social_link_title',
            [
                'label' => __('Title', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'social_link',
            [
                'label' => __('Link', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'social_icon',
            [
                'label' => __('Choose Icon', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::ICONS,
            ]
        );

        $this->add_control(
            'social_link_list',
            [
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'social_link' => __('http://www.facebook.com/bdthemes/', 'bdthemes-prime-slider'),
                        'social_icon' => [
                            'value' => 'fab fa-facebook-f',
                            'library' => 'fa-brands',
                        ],
                        'social_link_title' => 'Facebook',
                    ],
                    [
                        'social_link' => __('http://www.twitter.com/bdthemes/', 'bdthemes-prime-slider'),
                        'social_icon' => [
                            'value' => 'fab fa-twitter',
                            'library' => 'fa-brands',
                        ],
                        'social_link_title' => 'Twitter',
                    ],
                    [
                        'social_link' => __('http://www.instagram.com/bdthemes/', 'bdthemes-prime-slider'),
                        'social_icon' => [
                            'value' => 'fab fa-instagram',
                            'library' => 'fa-brands',
                        ],
                        'social_link_title' => 'Instagram',
                    ],
                ],
                'title_field' => '{{{ social_link_title }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_scroll_button',
            [
                'label' => esc_html__('Scroll Down', 'bdthemes-prime-slider'),
                'condition' => [
                    'show_scroll_button' => ['yes'],
                    '_skin!' => 'slice',
                ],
            ]
        );

        $this->add_control(
            'duration',
            [
                'label' => esc_html__('Duration', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 5000,
                        'step' => 50,
                    ],
                ],
            ]
        );

        $this->add_control(
            'offset',
            [
                'label' => esc_html__('Offset', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 200,
                        'step' => 10,
                    ],
                ],
                'classes' => BDTPS_CORE_IS_PC
            ]
        );

        $this->add_control(
            'scroll_button_text',
            [
                'label' => esc_html__('Button Text', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
                'default' => esc_html__('Scroll Down', 'bdthemes-prime-slider'),
                'placeholder' => esc_html__('Scroll Down', 'bdthemes-prime-slider'),
            ]
        );

        $this->add_control(
            'section_id',
            [
                'label' => esc_html__('Section ID', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::TEXT,
                'default' => 'my-header',
                'description' => esc_html__("By clicking this scroll button, to which section in your page you want to go? Just write that's section ID here such 'my-header'. N.B: No need to add '#'.", 'bdthemes-prime-slider'),
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

        $this->add_control(
            'animation_parallax',
            [
                'label' => esc_html__('Parallax Animation', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        /**
         * Ken Burns Controls
         */
        $this->register_ken_burns_controls();

        $this->end_controls_section();

        /**
         * Reveal Effects
         */
        if ('on' === $reveal_effects) {
            $this->register_reveal_effects();
        }

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
                        '.bdt-ps-sub-title' => __('Sub Title', 'bdthemes-element-pack'),
                        '.bdt-title-tag' => __('Title', 'bdthemes-element-pack'),
                        '.bdt-slider-excerpt' => __('Excerpt', 'bdthemes-element-pack'),
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
                'default' => 'none',
                'options' => [
                    'none' => esc_html__('None', 'bdthemes-prime-slider'),
                    'background' => esc_html__('Background', 'bdthemes-prime-slider'),
                    'blend' => esc_html__('Blend', 'bdthemes-prime-slider'),
                ],
                'condition' => [
                    '_skin!' => ['locate'],
                ],
                'classes' => BDTPS_CORE_IS_PC
            ]
        );

        $this->add_control(
            'overlay_color',
            [
                'label' => esc_html__('Overlay Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'overlay' => ['background', 'blend'],
                    '_skin!' => ['locate'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-slideshow .bdt-overlay-default' => 'background-color: {{VALUE}};',
                ],
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
                    '_skin!' => ['locate'],
                ],
            ]
        );

        $this->add_control(
            'slider_background_color',
            [
                'label' => esc_html__('Content / Primary Background Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-skin-locate, {{WRAPPER}} .bdt-prime-slider-skin-isolate, {{WRAPPER}} .bdt-prime-slider-skin-locate .bdt-prime-slider-desc' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => ['locate'],
                ],
            ]
        );

        $this->add_control(
            'slider_background_before_color',
            [
                'label' => esc_html__('Secondary Background Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-skin-locate:before' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => ['locate'],
                ],
            ]
        );

        $this->add_control(
            'ps_slice_background',
            [
                'label' => esc_html__('Background', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-skin-slice' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => ['slice'],
                ],
            ]
        );

        $this->add_control(
            'ps_slice_before_background',
            [
                'label' => esc_html__('Primary Background', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-skin-slice:before' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => ['slice'],
                ],
            ]
        );

        $this->add_control(
            'ps_slice_after_background',
            [
                'label' => esc_html__('Secondary Background', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-skin-slice:after' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => ['slice'],
                ],
            ]
        );

        $this->add_responsive_control(
            'slice_image_size',
            [
                'label' => esc_html__('Image Size(%)', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-skin-slice .bdt-slideshow-item .bdt-slide-overlay img' => 'width: {{SIZE}}%;',
                ],
                'condition' => [
                    '_skin' => ['slice'],
                ],
            ]
        );

        $this->start_controls_tabs('slider_item_style');

        $this->start_controls_tab(
            'slider_title_style',
            [
                'label' => __('Title', 'bdthemes-prime-slider'),
                'condition' => [
                    'show_title' => ['yes'],
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .bdt-title-tag, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .bdt-title-tag a' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_title' => ['yes'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
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
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .bdt-title-tag, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .bdt-title-tag a',
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

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow',
                'label' => __('Text Shadow', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .bdt-title-tag, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .bdt-title-tag a',
                'condition' => [
                    'show_title' => ['yes'],
                ],
            ]
        );

        $this->add_responsive_control(
            'prime_slider_title_spacing',
            [
                'label' => esc_html__('Title Spacing', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
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

        $this->add_responsive_control(
            'prime_slider_left_spacing',
            [
                'label' => esc_html__('Left Spacing', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-skin-slice .bdt-prime-slider-desc .bdt-title-tag, {{WRAPPER}} .bdt-prime-slider-skin-slice .bdt-prime-slider-desc .bdt-sub-title-tag' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_title' => ['yes'],
                    '_skin' => 'slice',
                ],
            ]
        );

        $this->add_control(
            'first_word_style',
            [
                'label' => esc_html__('First Word Style', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type' => Controls_Manager::SWITCHER,
                'classes' => BDTPS_CORE_IS_PC
            ]
        );

        $this->add_control(
            'first_word_text_color',
            [
                'label' => esc_html__('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .frist-word' => 'color: {{VALUE}}; -webkit-text-stroke-color: {{VALUE}};',
                ],
                'condition' => [
                    'show_title' => ['yes'],
                    'first_word_style' => ['yes'],
                ],
            ]
        );

        $this->add_control(
            'first_word_line_color',
            [
                'label' => esc_html__('Line Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .frist-word:before' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    'show_title' => ['yes'],
                    'first_word_style' => ['yes'],
                    '_skin' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'first_word_typography',
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-main-title .frist-word',
                'condition' => [
                    'show_title' => ['yes'],
                    'first_word_style' => ['yes'],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'slider_sub_title_style',
            [
                'label' => __('Sub Title', 'bdthemes-prime-slider'),
                'condition' => [
                    'show_sub_title' => ['yes'],
                ],
            ]
        );

        $this->add_control(
            'sub_title_color',
            [
                'label' => esc_html__('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-sub-title-tag' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sub_title_typography',
                'label' => esc_html__('Typography', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-sub-title-tag',
            ]
        );

        $this->add_responsive_control(
            'prime_slider_sub_title_spacing',
            [
                'label' => esc_html__('Sub Title Spacing', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-desc .bdt-sub-title .bdt-sub-title-tag, {{WRAPPER}} .bdt-prime-slider-skin-slice .bdt-prime-slider-desc .bdt-sub-title-tag' => 'padding-bottom: {{SIZE}}{{UNIT}};',
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
                'label' => esc_html__('Excerpt', 'bdthemes-prime-slider'),
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
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slider-excerpt' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'excerpt_background_color',
            [
                'label' => esc_html__('Background', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-skin-slice .bdt-slide-text-btn-area' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => ['slice'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'excerpt_typography',
                'label' => esc_html__('Typography', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-slider-excerpt',
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
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slider-excerpt' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'prime_slider_excerpt_spacing',
            [
                'label' => esc_html__('Excerpt Spacing', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slider-excerpt' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_excerpt' => ['yes'],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'slider_button_style',
            [
                'label' => __('Button', 'bdthemes-prime-slider'),
                'condition' => [
                    'show_button_text' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'slider_button_style_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'slide_button_text_color',
            [
                'label' => __('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
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
                'label' => __('Background', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    '_skin!' => 'slice',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'slice_skin_button_background_color',
                'label' => __('Background', 'bdthemes-prime-slider'),
                'types' => ['gradient'],
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn',
                'condition' => [
                    '_skin' => 'slice',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'slide_button_border',
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn',
            ]
        );

        $this->add_responsive_control(
            'slide_button_border_radius',
            [
                'label' => __('Border Radius', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn, {{WRAPPER}} .bdt-prime-slider-skin-isolate .bdt-slide-btn:before, {{WRAPPER}} .bdt-prime-slider-skin-isolate .bdt-slide-btn:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'slide_button_text_padding',
            [
                'label' => __('Padding', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'slide_button_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'slide_button_typography',
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn',
            ]
        );

        $this->add_control(
            'slider_button_style_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'slide_button_hover_color',
            [
                'label' => __('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
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
                'label' => __('Background', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-skin-isolate .bdt-slide-btn:before, {{WRAPPER}} .bdt-prime-slider-skin-locate .bdt-slide-btn:hover' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    '_skin!' => 'slice',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'slice_skin_button_hover_background_color',
                'label' => __('Background', 'bdthemes-prime-slider'),
                'types' => ['gradient'],
                'selector' => '{{WRAPPER}} .bdt-prime-slider-skin-slice .bdt-slide-btn::before',
                'condition' => [
                    '_skin' => 'slice',
                ],
            ]
        );

        $this->add_control(
            'slide_button_hover_border_color',
            [
                'label' => __('Border Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'slide_button_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slide-btn:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_slide_play_button',
            [
                'label' => esc_html__('Lightbox Play Button', 'bdthemes-prime-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_play_button' => ['yes'],
                    '_skin!' => ['locate'],
                ],
            ]
        );

        $this->add_control(
            'fancy_animation',
            [
                'label' => esc_html__('Animation', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'shadow-pulse',
                'options' => [
                    'shadow-pulse' => esc_html__('Shadow Pulse', 'bdthemes-element-pack'),
                    'multi-shadow' => esc_html__('Multi Shadow', 'bdthemes-element-pack'),
                    'line-bounce' => esc_html__('Line Bounce', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_control(
            'fancy_border_color',
            [
                'label' => esc_html__('Animated Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slide-play-button a:before, {{WRAPPER}} .bdt-prime-slider .bdt-slide-play-button a:after' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'fancy_animation' => 'line-bounce',
                    '_skin' => '',
                ],
            ]
        );

        $this->add_control(
            'button_shadow_color',
            [
                'label' => esc_html__('Animated Shadow Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slide-play-button a' => '--box-shadow-color: {{VALUE}};',
                ],
                'condition' => [
                    'fancy_animation!' => 'line-bounce',
                    '_skin' => '',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_play_button_style');

        $this->start_controls_tab(
            'tab_play_button_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-prime-slider'),
            ]
        );

        $this->add_control(
            'slide_play_button_icon_color',
            [
                'label' => esc_html__('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slide-play-button a svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'slide_play_button_background_color',
            [
                'label' => esc_html__('Background', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slide-play-button a' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'slide_play_button_border',
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-slide-play-button a',
            ]
        );

        $this->add_responsive_control(
            'slide_play_button_border_radius',
            [
                'label' => __('Border Radius', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slide-play-button a, {{WRAPPER}} .bdt-prime-slider-skin-isolate .bdt-slide-play-button.bdt-line-bounce a:before, {{WRAPPER}} .bdt-prime-slider-skin-isolate .bdt-slide-play-button.bdt-line-bounce a:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'slide_play_button_typography',
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-slide-play-button a',
                'condition' => [
                    '_skin!' => ['slice'],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_play_button_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-prime-slider'),
            ]
        );

        $this->add_control(
            'slide_play_button_hover_color',
            [
                'label' => esc_html__('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slide-play-button a:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'play_btn_hover_background_color',
            [
                'label' => esc_html__('Background Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slide-play-button a:hover' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'slide_play_button_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-slide-play-button a:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_social_icon',
            [
                'label' => esc_html__('Social Icon', 'bdthemes-prime-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_social_icon' => 'yes',
                    '_skin' => ['locate', 'slice'],
                ],
            ]
        );

        $this->add_control(
            'social_icon_sec_bg_color',
            [
                'label' => esc_html__('Background Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => ['slice'],
                ],
            ]
        );

        $this->add_control(
            'social_icon_line_bg_color',
            [
                'label' => esc_html__('Line Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-skin-slice .bdt-prime-slider-social-icon a:before' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => ['slice'],
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
                'label' => esc_html__('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'social_icon_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a',

            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'social_icon_border',
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'social_icon_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'social_icon_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'social_icon_shadow',
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a',
            ]
        );

        $this->add_responsive_control(
            'social_icon_size',
            [
                'label' => __('Icon Size', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'social_icon_spacing',
            [
                'label' => esc_html__('Icon Spacing', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'social_icon_tooltip',
            [
                'label' => esc_html__('Show Tooltip', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
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
                'label' => esc_html__('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'social_icon_hover_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a:hover',

            ]
        );

        $this->add_control(
            'icon_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'social_icon_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a:hover' => 'border-color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_scroll_button',
            [
                'label' => esc_html__('Scroll Down', 'bdthemes-prime-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_scroll_button' => ['yes'],
                    '_skin!' => 'slice',
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
                'label' => esc_html__('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-scroll-down span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'scroll_button_text_background',
            [
                'label' => esc_html__('Background', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-skin-isolate .bdt-scroll-down-wrapper .bdt-scroll-icon' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'scroll_button_border',
                'selector' => '{{WRAPPER}} .bdt-prime-slider-skin-isolate .bdt-scroll-down-wrapper .bdt-scroll-icon',
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'scroll_button_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-skin-isolate .bdt-scroll-down-wrapper .bdt-scroll-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'scroll_button_padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-skin-isolate .bdt-scroll-down-wrapper .bdt-scroll-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'scroll_button_typography',
                'label' => esc_html__('Typography', 'bdthemes-prime-slider'),
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
                'label' => esc_html__('Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-scroll-down:hover span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'scroll_button_hover_background',
            [
                'label' => esc_html__('Background', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-scroll-icon::before' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->add_control(
            'scroll_button_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'scroll_button_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-social-icon a:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    '_skin' => '',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_navigation',
            [
                'label' => __('Navigation', 'bdthemes-prime-slider'),
                'tab' => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'show_navigation_arrows',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'show_navigation_dots',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $this->start_controls_tabs('tabs_navigation_style');

        $this->start_controls_tab(
            'tab_navigation_arrows_style',
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
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous i, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next i, {{WRAPPER}} .bdt-prime-slider-skin-locate .bdt-prime-slider-previous, {{WRAPPER}} .bdt-prime-slider-skin-locate .bdt-prime-slider-next' => 'color: {{VALUE}}',
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
                'name' => 'arrows_background',
                'label' => __('Background', 'bdthemes-prime-slider'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous',
                'condition' => [
                    'show_navigation_arrows' => ['yes'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'arrows_border',
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous',
                'condition' => [
                    'show_navigation_arrows' => ['yes'],
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_border_radius',
            [
                'label' => __('Border Radius', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'show_navigation_arrows' => ['yes'],
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_padding',
            [
                'label' => __('Padding', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'show_navigation_arrows' => ['yes'],
                ],
            ]
        );

        $this->add_responsive_control(
            'arrows_margin',
            [
                'label' => __('Margin', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'show_navigation_arrows' => ['yes'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'arrows_size',
                'label' => __('Typography', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous',
                'condition' => [
                    'show_navigation_arrows' => ['yes'],
                ],
            ]
        );

        $this->add_control(
            'pagination_heading',
            [
                'label' => __('Pagination', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'show_navigation_dots' => ['yes'],
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'navi_dot_color',
            [
                'label' => __('Dot Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-dotnav li a:after' => 'background: {{VALUE}}',
                ],
                'condition' => [
                    'show_navigation_dots' => ['yes'],
                    '_skin' => '',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'active_dot_color',
            [
                'label' => __('Active Dot Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-dotnav li:hover a:after, {{WRAPPER}} .bdt-dotnav li.bdt-active a:after' => 'background: {{VALUE}}',
                ],
                'condition' => [
                    'show_navigation_dots' => ['yes'],
                    '_skin' => '',
                ],
            ]
        );

        $this->add_control(
            'border_dot_color',
            [
                'label' => __('Border Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider-skin-isolate .bdt-dotnav li a:before' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'show_navigation_dots' => ['yes'],
                    '_skin' => '',
                ],
            ]
        );

        $this->add_control(
            'active_dot_number_color',
            [
                'label' => __('Active Number Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-dotnav li a, {{WRAPPER}} .bdt-prime-slider .bdt-ps-dotnav span' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .bdt-prime-slider .bdt-ps-dotnav span:before' => 'background: {{VALUE}}',
                ],
                'condition' => [
                    'show_navigation_dots' => ['yes'],
                    '_skin' => ['locate', 'slice'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'dots_size',
                'label' => __('Typography', 'bdthemes-prime-slider'),
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-ps-dotnav li a, {{WRAPPER}} .bdt-prime-slider .bdt-ps-dotnav span',
                'condition' => [
                    'show_navigation_dots' => ['yes'],
                    '_skin' => ['locate', 'slice'],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_navigation_arrows_hover_style',
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
                    '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous:hover i, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next:hover i' => 'color: {{VALUE}}',
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
                'name' => 'arrows_hover_background',
                'label' => __('Background', 'bdthemes-prime-slider'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next:before, {{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous:before',
                'condition' => [
                    'show_navigation_arrows' => ['yes'],
                ],
                'condition' => [
                    '_skin!' => 'locate',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'locate_arrows_hover_background',
                'label' => __('Background', 'bdthemes-prime-slider'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-next:hover,
				{{WRAPPER}} .bdt-prime-slider .bdt-prime-slider-previous:hover',
                'condition' => [
                    'show_navigation_arrows' => ['yes'],
                ],
                'condition' => [
                    '_skin' => 'locate',
                ],
            ]
        );

        $this->add_control(
            'arrows_hover_border_color',
            [
                'label' => __('Border Color', 'bdthemes-prime-slider'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'arrows_border_border!' => '',
                    'show_navigation_arrows' => ['yes'],
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

    public function render_header($skin_name = 'isolate') {

        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('prime-slider', 'class', 'bdt-prime-slider-skin-' . $skin_name);

        $this->add_render_attribute('prime-slider', 'class', 'content-position-' . $settings['content_position']);
        $this->add_render_attribute('prime-slider', 'class', 'content-position-' . $settings['content_column_position']);

        $this->add_render_attribute('prime-slider', 'data-bdt-lightbox', 'toggle: .bdt-slide-play-button>a; animation: slide; video-autoplay: true;');

        /**
         * Reveal Effects
         */
        $this->reveal_effects_attr('slideshow');

        
        $this->adv_anim('slideshow');

        $this->add_render_attribute('slideshow', 'id', 'bdt-' . $this->get_id());

        /**
         * Slideshow Settings
         */
        $this->render_slideshows_settings('600');
    }

    public function render_navigation_arrows() {
        $settings = $this->get_settings_for_display();

        ?>

            <?php if ($settings['show_navigation_arrows']): ?>
                <div class="bdt-flex bdt-flex-column bdt-navigation-arrows reveal-muted">
                    <div class="bdt-width-expand@s">
                    </div>
                    <div class="bdt-width-1-1 bdt-width-1-2@s">
                        <a class="bdt-prime-slider-previous" href="#" bdt-slideshow-item="previous"><i class="ps-wi-arrow-left-5"></i></a>

                        <a class="bdt-prime-slider-next" href="#" bdt-slideshow-item="next"><i class="ps-wi-arrow-right-5"></i></a>
                    </div>
                </div>


            <?php endif;?>

        <?php
    }

    public function render_navigation_dots() {
        $settings = $this->get_settings_for_display();

        ?>

            <?php if ($settings['show_navigation_dots']): ?>

                <ul class="bdt-slideshow-nav bdt-dotnav bdt-dotnav-vertical bdt-margin-medium-right bdt-position-center-right reveal-muted"></ul>

            <?php endif;?>

        <?php
    }

    public function render_footer() {

        ?>

					</ul>

					<?php $this->render_navigation_arrows();?>
					<?php $this->render_navigation_dots();?>

				</div>
				<?php $this->render_scroll_button();?>
			</div>
			</div>
		<?php
    }

    public function render_scroll_button_text() {

        $this->add_render_attribute('content-wrapper', 'class', 'bdt-scroll-down-content-wrapper');
        $this->add_render_attribute('text', 'class', 'bdt-scroll-down-text');

        ?>
			<span bdt-scrollspy="cls: bdt-animation-slide-right; repeat: true" <?php $this->print_render_attribute_string('content-wrapper');?>>
				<span class="bdt-scroll-icon">
					<span class="ps-wi-arrow-down-4"></span>
				</span>
			</span>
		<?php
    }

    public function render_scroll_button() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('bdt-scroll-down', 'class', ['bdt-scroll-down reveal-muted']);

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
                        ])),
                    ],
                ],
            ]
        );

        $this->add_render_attribute('bdt-scroll-down', 'data-selector', '#' . esc_attr($settings['section_id']));

        $this->add_render_attribute('bdt-scroll-wrapper', 'class', 'bdt-scroll-down-wrapper');

        ?>
			<div <?php $this->print_render_attribute_string('bdt-scroll-wrapper');?>>
				<span <?php $this->print_render_attribute_string('bdt-scroll-down');?>>
					<?php $this->render_scroll_button_text();?>
				</span>
			</div>

		<?php
    }

    public function rendar_item_image($slide) {
        $settings = $this->get_settings_for_display();

		$thumb_url = Group_Control_Image_Size::get_attachment_image_src($slide['image']['id'], 'thumbnail_size', $settings);
		if (!$thumb_url) {
			printf('<img src="%1$s" alt="%2$s">', esc_url($slide['image']['url']), esc_html($slide['title']));
		} else {
			print(wp_get_attachment_image(
				$slide['image']['id'],
				$settings['thumbnail_size_size'],
				false,
				[
					'alt' => esc_html($slide['title'])
				]
			));
		}
	}

    public function render_button($content) {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('slider-button', 'class', 'bdt-slide-btn', true);

        if (isset($content['button_link']['url'])) {
            $this->add_render_attribute('slider-button', 'href', esc_url($content['button_link']['url']), true);

            if ($content['button_link']['is_external']) {
                $this->add_render_attribute('slider-button', 'target', '_blank', true);
            }

            if ($content['button_link']['nofollow']) {
                $this->add_render_attribute('slider-button', 'rel', 'nofollow', true);
            }
        } else {
            $this->add_render_attribute('slider-button', 'href', 'javascript:void(0);', true);
        }

        ?>

			<?php if ($content['slide_button_text'] && ('yes' == $settings['show_button_text'])): ?>

				<a <?php $this->print_render_attribute_string('slider-button');?>>

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
					<span <?php $this->print_render_attribute_string('content-wrapper');?>>

						<span <?php $this->print_render_attribute_string('text');?>><?php echo wp_kses($content['slide_button_text'], prime_slider_allow_tags('title')); ?><span class="bdt-slide-btn-icon"><svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="arrow-right">
									<polyline fill="none" stroke="#000" points="10 5 15 9.5 10 14"></polyline>
									<line fill="none" stroke="#000" x1="4" y1="9.5" x2="15" y2="9.5"></line>
								</svg></span></span>

					</span>


				</a>
			<?php endif;
    }

    public function render_play_button($slide, $index) {
        $settings = $this->get_settings_for_display();
        $id = $this->get_id();

        if ('' == $settings['show_play_button']) {
            return;
        }

        // remove global lightbox
        $image_url = wp_get_attachment_image_src($slide['image']['id'], 'full');
        $this->add_render_attribute('lightbox-content-' . $index, 'data-elementor-open-lightbox', 'no', true);

		if ($slide['image_link_type']) {
			if ('google-map' == $slide['image_link_type'] and '' != $slide['image_link_google_map']) {
				$this->add_render_attribute('lightbox-content-' . $index, 'href', esc_url($slide['image_link_google_map']['url']));
				$this->add_render_attribute('lightbox-content-' . $index, 'data-type', 'iframe');
			} elseif ('video' == $slide['image_link_type'] and '' != $slide['image_link_video']) {
				$this->add_render_attribute('lightbox-content-' . $index, 'href', esc_url($slide['image_link_video']['url']));
				$this->add_render_attribute('lightbox-content-' . $index, 'data-type', 'video');
			} elseif ('youtube' == $slide['image_link_type'] and '' != $slide['lightbox_link']) {
				$this->add_render_attribute('lightbox-content-' . $index, 'href', esc_url($slide['lightbox_link']['url']));
				$this->add_render_attribute('lightbox-content-' . $index, 'data-type', false);
			} elseif ('vimeo' == $slide['image_link_type'] and '' != $slide['image_link_vimeo']) {
				$this->add_render_attribute('lightbox-content-' . $index, 'href', esc_url($slide['image_link_vimeo']['url']));
				$this->add_render_attribute('lightbox-content-' . $index, 'data-type', false);
			} else {
				$this->add_render_attribute('lightbox-content-' . $index, 'href', esc_url($slide['image_link_website']['url']));
				$this->add_render_attribute('lightbox-content-' . $index, 'data-type', 'iframe');
			}
		} else {
			if (!$image_url) {
				$this->add_render_attribute('lightbox-content-' . $index, 'href', esc_url($slide['image']['url']));
			} else {
				$this->add_render_attribute('lightbox-content-' . $index, 'href', esc_url($image_url[0]));
			}
		}



        if ('shadow-pulse' == $settings['fancy_animation']) {
            $this->add_render_attribute('lightbox', 'class', 'bdt-slide-play-button bdt-position-center bdt-shadow-pulse reveal-muted', true);
        } elseif ('line-bounce' == $settings['fancy_animation']) {
            $this->add_render_attribute('lightbox', 'class', 'bdt-slide-play-button bdt-position-center bdt-line-bounce reveal-muted', true);
        } elseif ('multi-shadow' == $settings['fancy_animation']) {
            $this->add_render_attribute('lightbox', 'class', 'bdt-slide-play-button bdt-position-center bdt-multi-shadow reveal-muted', true);
        } else {
            $this->add_render_attribute('lightbox', 'class', 'bdt-slide-play-button bdt-position-center reveal-muted', true);
        }

        ?>
			<div <?php $this->print_render_attribute_string('lightbox');?>>

				<a <?php $this->print_render_attribute_string('lightbox-content-' . $index);?>>
					<svg aria-hidden="true" class="" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"></path></svg>
				</a>

			</div>
		<?php
    }

    public function render_item_content($slide_content) {
        $settings = $this->get_settings_for_display();

        $parallax_button = $parallax_sub_title = $parallax_title = $parallax_inner_excerpt = $parallax_excerpt = '';
        if ($settings['animation_parallax'] == 'yes') {
            $parallax_sub_title = 'data-bdt-slideshow-parallax="y: 50,0,-50; opacity: 1,1,0"';
            $parallax_title = 'data-bdt-slideshow-parallax="y: 50,0,-50; opacity: 1,1,0"';
            $parallax_excerpt = 'data-bdt-slideshow-parallax="y: 100,0,-80; opacity: 1,1,0"';
            $parallax_button = 'data-bdt-slideshow-parallax="y: 150,0,-100; opacity: 1,1,0"';
        }

        if ( true === _is_ps_pro_activated() ) {
            if ($settings['animation_status'] == 'yes' && !empty($settings['animation_of'])) {

                if (in_array(".bdt-ps-sub-title", $settings['animation_of'])) {
                    $parallax_sub_title = '';
                }
                if (in_array(".bdt-title-tag", $settings['animation_of'])) {
                    $parallax_title = '';
                }
                if (in_array(".bdt-slider-excerpt", $settings['animation_of'])) {
                    $parallax_excerpt = '';
                }
            }
        }

        ?>
        <div class="bdt-slideshow-content-wrapper">
            <div class="bdt-prime-slider-wrapper">
                <div class="bdt-prime-slider-content">
                    <div class="bdt-prime-slider-desc">

                        <?php if ($slide_content['sub_title'] && ('yes' == $settings['show_sub_title'])): ?>
                            <div class="bdt-sub-title bdt-ps-sub-title">
                                <<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?> class="bdt-sub-title-tag" data-reveal="reveal-active" <?php echo wp_kses_post($parallax_sub_title); ?>>
                                    <?php echo wp_kses_post($slide_content['sub_title']); ?>
                                </<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?>>
                            </div>
                        <?php endif;?>

                        <?php if ($slide_content['title'] && ('yes' == $settings['show_title'])): ?>
                            <div class="bdt-main-title">
                                <<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?> class="bdt-title-tag" data-reveal="reveal-active" <?php echo wp_kses_post($parallax_title); ?>>
                                    <?php if ('' !== $slide_content['title_link']['url']): ?>
                                        <a href="<?php echo esc_url($slide_content['title_link']['url']); ?>">
                                        <?php endif;?>
                                        <?php echo wp_kses_post(prime_slider_first_word($slide_content['title'])); ?>
                                        <?php if ('' !== $slide_content['title_link']['url']): ?>
                                        </a>
                                    <?php endif;?>
                                </<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?>>
                            </div>
                        <?php endif;?>

                        <?php if ($slide_content['excerpt'] && ('yes' == $settings['show_excerpt'])): ?>
                            <div class="bdt-slider-excerpt" data-reveal="reveal-active" <?php echo wp_kses_post($parallax_excerpt); ?>>
                                <?php echo wp_kses_post($slide_content['excerpt']); ?>
                            </div>
                        <?php endif;?>

                        <div class="bdt-isolate-btn" data-reveal="reveal-active" <?php echo wp_kses_post($parallax_button); ?>>
                            <?php $this->render_button($slide_content);?>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <?php
    }

    public function render_slides_loop() {
        $settings = $this->get_settings_for_display();

        $kenburns_reverse = $settings['kenburns_reverse'] ? ' bdt-animation-reverse' : '';
        $index = 0;
        foreach ($settings['slides'] as $slide): 
            $index += 1;
        ?>
         
        <li class="bdt-slideshow-item bdt-flex bdt-flex-<?php echo esc_attr($settings['content_column_position']); ?> bdt-flex-middle elementor-repeater-item-<?php echo esc_attr($slide['_id']); ?> ">
            <div class="bdt-width-1-1 bdt-width-1-2@s">
                <?php $this->render_item_content($slide);?>
            </div>
            <div class="bdt-width-1-1 bdt-width-1-2@s bdt-match-height">
                <div class="bdt-position-relative bdt-slide-overlay" data-reveal="reveal-active">
                    <?php if ('yes' == $settings['kenburns_animation']): ?>
                        <div class="bdt-position-cover bdt-animation-kenburns<?php echo esc_attr($kenburns_reverse); ?> bdt-transform-origin-center-left">
                        <?php endif;?>

                        <?php $this->rendar_item_image($slide);?>

                        <?php if ('yes' == $settings['kenburns_animation']): ?>
                        </div>
                    <?php endif;?>

                    <?php if ('none' !== $settings['overlay']):
                        $blend_type = ('blend' == $settings['overlay']) ? ' bdt-blend-' . $settings['blend_type'] : '';?>
                        <div class="bdt-overlay-default bdt-position-cover<?php echo esc_attr($blend_type); ?>"></div>
                    <?php endif;?>
                </div>
            </div>
            <?php $this->render_play_button($slide, $index);?>
        </li>

	    <?php endforeach;
    }

    public function render() {
        $this->render_header();
        $this->render_slides_loop();
        $this->render_footer();
    }
}
