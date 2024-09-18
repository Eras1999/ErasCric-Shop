<?php
namespace PrimeSlider\Modules\Sniper\Widgets;

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
use Elementor\Plugin;

use PrimeSlider\Traits\Global_Widget_Controls;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Sniper extends Widget_Base {

	use Global_Widget_Controls;

	public function get_name() {
		return 'prime-slider-sniper';
	}

	public function get_title() {
		return BDTPS . esc_html__('Sniper', 'bdthemes-prime-slider');
	}

	public function get_icon() {
		return 'bdt-widget-icon ps-wi-sniper';
	}

	public function get_categories() {
		return ['prime-slider'];
	}

	public function get_keywords() {
		return [ 'prime slider', 'slider', 'sniper', 'prime' ];
	}

	public function get_style_depends() {
		return ['ps-sniper'];
	}
	public function get_script_depends() {
		return ['shutters', 'gl', 'slicer', 'tinder', 'ps-sniper'];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/KZstgwk-pog?si=k0t9Gj7POSuEzDIi';
	}

	protected function register_controls() {

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
						'title' => esc_html__('Sniper Slide 1', 'bdthemes-prime-slider'),
						'image' => ['url' => BDTPS_CORE_ASSETS_URL . 'images/gallery/item-1.svg']
					],
					[
						'title' => esc_html__('Sniper Slide 2', 'bdthemes-prime-slider'),
						'image' => ['url' => BDTPS_CORE_ASSETS_URL . 'images/gallery/item-2.svg']
					],
					[
						'title' => esc_html__('Sniper Slide 3', 'bdthemes-prime-slider'),
						'image' => ['url' => BDTPS_CORE_ASSETS_URL . 'images/gallery/item-3.svg']
					],
					[
						'title' => esc_html__('Sniper Slide 4', 'bdthemes-prime-slider'),
						'image' => ['url' => BDTPS_CORE_ASSETS_URL . 'images/gallery/item-4.svg']
					],
					[
						'title' => esc_html__('Sniper Slide 5', 'bdthemes-prime-slider'),
						'image' => ['url' => BDTPS_CORE_ASSETS_URL . 'images/gallery/item-5.svg']
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

		$this->add_responsive_control(
			'item_height',
			[
				'label' => esc_html__('Height', 'bdthemes-prime-slider'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px', 'vh' ],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1080,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .bdt-sniper-slider' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		/**
		* Show Title Controls
		*/
		$this->register_show_title_controls();

		/**
		* Show Sub Title Controls
		*/
		$this->register_show_sub_title_controls();

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
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-sniper-slider .bdt-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

		/**
		* Thumbnail Size Controls
		*/
		$this->register_thumbnail_size_controls();
		
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
				'label'     => esc_html__( 'Slides to Scroll', 'bdthemes-prime-slider' ),
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
				'label'   => __( 'Center Slide', 'bdthemes-prime-slider' ),
				'description'   => __( 'Use even items from Layout > Columns settings for better preview.', 'bdthemes-prime-slider' ),
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
		 * Loop, Rewind & mousewheel Controls
		 */
		$this->register_loop_rewind_mousewheel_controls();

		/**
		 * Speed & Observer Controls
		 */
		$this->register_speed_observer_controls();

		/**
		 * Swiper Effects global controls
		 */
		$this->register_swiper_effects_controls();

		$this->end_controls_section();
	
		//Style Start
		$this->start_controls_section(
			'section_style_sliders',
			[
				'label'     => esc_html__('Sliders', 'bdthemes-prime-slider'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-sniper-slider' => 'background-color: {{VALUE}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'image_background',
				'label' => esc_html__('Background', 'pixel-gallery'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .bdt-sniper-slider .bdt-ps-iamge-overlay::before',
				'fields_options' => [
					'background' => [
						'label' => esc_html__('Overlay Color', 'pixel-gallery'),
						'default' => 'gradient',
					],
					'color' => [
						'default' => '#111019',
					],
					'color_b' => [
						'default' => '#00000000',
					],
					'gradient_type' => [
						'default' => 'linear',
					],
					'gradient_angle' => [
						'default' => [
							'unit' => 'deg',
							'size' => 45,
						],
					],
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

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-sniper-slider .bdt-title, {{WRAPPER}} .bdt-sniper-slider .bdt-title a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-sniper-slider .bdt-title:hover, {{WRAPPER}} .bdt-sniper-slider .bdt-title a:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'title_active_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-sniper-slider .swiper-slide-active .bdt-title, {{WRAPPER}} .bdt-sniper-slider .swiper-slide-active .bdt-title a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_title' => ['yes'],
				],
				'classes'   => BDTPS_CORE_IS_PC,
			]
		);

		$this->add_control(
            'first_word_title_color',
            [
                'label'     => esc_html__('First Word Color', 'bdthemes-prime-slider'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-sniper-slider .bdt-title .frist-word' => 'color: {{VALUE}};',
                ],
                'condition' => [
					'show_title' => ['yes'],
				],
            ]
        );

		$this->add_control(
            'first_word_title_active_color',
            [
                'label'     => esc_html__('First Word Active Color', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-sniper-slider .swiper-slide-active .bdt-title .frist-word' => 'color: {{VALUE}};',
                ],
                'condition' => [
					'show_title' => ['yes'],
				],
				'classes'   => BDTPS_CORE_IS_PC,
            ]
        );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-prime-slider'),
				'selector' => '{{WRAPPER}} .bdt-sniper-slider .bdt-title',
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => 'title_text_stroke',
				'selector' => '{{WRAPPER}} .bdt-sniper-slider .bdt-title',
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .bdt-sniper-slider .bdt-title',
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
					'{{WRAPPER}} .bdt-sniper-slider .bdt-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-prime-slider'),
				'selector' => '{{WRAPPER}} .bdt-sniper-slider .bdt-sub-title',
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
					'{{WRAPPER}} .bdt-sniper-slider .bdt-sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_sub_title' => ['yes'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'slider_grid_line_style',
			[
				'label' 	=> __('Grid Line', 'bdthemes-prime-slider'),
				'condition' => [
					'show_sub_title' => ['yes'],
				],
				'classes'   => BDTPS_CORE_IS_PC,
			]
		);

		$this->add_control(
			'grid_line_color',
			[
				'label'     => esc_html__('Grid Line Color', 'bdthemes-prime-slider') . BDTPS_CORE_PC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-sniper-slider .bdt-grid-line span' => 'border-color: {{VALUE}};',
				],
				'classes'   => BDTPS_CORE_IS_PC,
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
					'{{WRAPPER}} .bdt-sniper-slider .bdt-navigation-wrap .bdt-nav-btn' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-sniper-slider .bdt-navigation-wrap .bdt-nav-btn::before' => 'background: {{VALUE}}',
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
					'{{WRAPPER}} .bdt-sniper-slider .bdt-navigation-wrap .bdt-nav-btn:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-sniper-slider .bdt-navigation-wrap .bdt-nav-btn:hover::before' => 'background: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_arrows' => ['yes'],
				],
			]
		);

		$this->add_control(
			'Fraction_pag_heading',
			[
				'label' => esc_html__( 'Fraction Pagination', 'bdthemes-prime-slider' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$this->add_control(
			'pagination_color',
			[
				'label'     => __('Pagination Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-sniper-slider .bdt-pagination-wrap .bdt-pagination' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
			]
		);

		$this->add_control(
			'pagination_total_color',
			[
				'label'     => __('Pagination Total Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-sniper-slider .bdt-pagination-wrap .swiper-pagination-total' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_navigation_dots' => ['yes'],
				],
			]
		);



		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'pagination_typography',
				'label'    => esc_html__('Typography', 'bdthemes-prime-slider'),
				'selector' => '{{WRAPPER}} .bdt-sniper-slider .bdt-pagination-wrap .bdt-pagination',
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'scrollbar_heading',
			[
				'label' => esc_html__( 'Scrollbar', 'bdthemes-prime-slider' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'scrollbar_line_color',
			[
				'label'     => __('line Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-sniper-slider .bdt-pagination-wrap .swiper-scrollbar' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'scrollbar_active_color',
			[
				'label'     => __('Active Line Color', 'bdthemes-prime-slider'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-sniper-slider .bdt-pagination-wrap .swiper-scrollbar-drag' => 'background: {{VALUE}}',
				],
			]
		);


		$this->end_controls_section();
	}

	protected function render_header() {
		$settings   = $this->get_settings_for_display();
		$id         = 'bdt-prime-slider-' . $this->get_id();

		$this->add_render_attribute( 'prime-slider-sniper', 'id', $id );
		$this->add_render_attribute( 'prime-slider-sniper', 'class', [ 'bdt-sniper-slider', 'elementor-swiper' ] );

		$this->add_render_attribute(
			[
				'prime-slider-sniper' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							"autoplay"       => ("yes" == $settings["autoplay"]) ? ["delay" => $settings["autoplay_speed"]] : false,
							"loop"           => (isset($settings["loop"]) && $settings["loop"] == "yes") ? true : false,
							"rewind"           => (isset($settings["rewind"]) && $settings["rewind"] == "yes") ? true : false,
							"speed"          => $settings["speed"]["size"],
							"effect"        => isset($settings["swiper_effect"]) ? $settings["swiper_effect"] : 'slide',
							"gl"             => [
								'shader' => isset($settings["gl_shader"]) ? $settings["gl_shader"] : 'random',
							],
							"creativeEffect" => isset($settings["creative_effect"]) ? $settings["creative_effect"] : false,
							"fadeEffect"     => ['crossFade' => true],
							"lazy"           => true,
							"parallax"       => true,
							"watchSlidesProgress" => true,
							"slidesPerGroupAuto" => false,
							"grabCursor"     => ($settings["grab_cursor"] === "yes") ? true : false,
							"pauseOnHover"   => ("yes" == $settings["pauseonhover"]) ? true : false,
							"slidesPerView"  => 1,
							"loopedSlides"   => 4,
							"observer"       => ($settings["observer"]) ? true : false,
							"observeParents" => ($settings["observer"]) ? true : false,
							"mousewheel" => ($settings["mousewheel"]) ? true : false,
							"scrollbar" => [
								"el"             => "#" . $id . " .swiper-scrollbar",
							],
							"pagination" => [
								'el'   => "#" . $id . " .bdt-pagination",
								'type' => "fraction",
							],
							"lazy" => [
								"loadPrevNext"  => "true",
							],
							"navigation" => [
								"nextEl" => "#" . $id . " .bdt-button-next",
								"prevEl" => "#" . $id . " .bdt-button-prev",
							],
						]))
					]
				]
			]
		);

		$swiper_class = Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
		$this->add_render_attribute('swiper', 'class', 'bdt-main-slider ' . $swiper_class);

		?>
		<div <?php $this->print_render_attribute_string( 'prime-slider-sniper' ); ?>>
		<div <?php $this->print_render_attribute_string( 'swiper' ); ?>>
			<div class="bdt-ps-iamge-overlay"></div>
			<div class="bdt-grid-line">
				<span></span>
				<span></span>
				<span></span>
				<span></span>
			</div>
			<div class="swiper-wrapper">
		<?php
	}

    public function render_footer() {
		$settings = $this->get_settings_for_display();
		?> 
				</div>

				<?php if ($settings['show_navigation_dots']) : ?>
				<div class="bdt-pagination-wrap">
                    <div class="bdt-pagination"></div>
                    <div class="swiper-scrollbar"></div>
                </div>
				<?php endif; ?>

			</div>
			<div thumbsSlider="" class="bdt-thumbs-slider">
                <div class="swiper-wrapper">

				<?php foreach ($settings['slides'] as $slide) : ?>
					<div class="swiper-slide bdt-item">
                        <div class="bdt-content">

							<?php if ($slide['sub_title'] && ('yes' == $settings['show_sub_title'])) : ?>
								<<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?> class="bdt-sub-title">
									<span><?php echo wp_kses_post($slide['sub_title']); ?></span>
								</<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?>>
							<?php endif; ?>

							<?php if ($slide['title'] && ('yes' == $settings['show_title'])) : ?>
								<<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?> class="bdt-title">
									<?php if ('' !== $slide['title_link']['url']) : ?>
										<a href="<?php echo esc_url($slide['title_link']['url']); ?>">
										<?php endif; ?>
										<?php echo wp_kses_post(prime_slider_first_word($slide['title'])); ?>
										<?php if ('' !== $slide['title_link']['url']) : ?>
										</a>
									<?php endif; ?>
								</<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?>>
							<?php endif; ?>
						
                        </div>
                    </div>
				<?php endforeach; ?>

                </div>
            </div>

			<?php if ($settings['show_navigation_arrows']) : ?>
            <div class="bdt-navigation-wrap">
                <div class="bdt-nav-btn bdt-button-next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right-fill" viewBox="0 0 16 16">
                        <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                      </svg>
                </div>
                <div class="bdt-nav-btn bdt-button-prev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-left-fill" viewBox="0 0 16 16">
                        <path d="m3.86 8.753 5.482 4.796c.646.566 1.658.106 1.658-.753V3.204a1 1 0 0 0-1.659-.753l-5.48 4.796a1 1 0 0 0 0 1.506z"/>
                      </svg>
                </div>
            </div>
			<?php endif; ?>
		</div>
		<?php
	}

    public function render_slides_loop() {
        $settings = $this->get_settings_for_display();

		foreach ($settings['slides'] as $slide) : 
		
			?>
			<div class="bdt-item swiper-slide">
				<?php $this->rendar_image($slide, ''); ?>
			</div>

        <?php endforeach;
    }

    public function render() {
		
        $this->render_header();

        $this->render_slides_loop();

        $this->render_footer();
    }
}