<?php
namespace PrimeSlider\Modules\General\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;
use PrimeSlider\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Crelly extends Elementor_Skin_Base {

	public function get_id() {
		return 'crelly';
	}

	public function get_title() {
		return esc_html__( 'Crelly', 'bdthemes-prime-slider' );
    }

    public function render_navigation_arrows() {
        $settings = $this->parent->get_settings_for_display();

        ?>

            <?php if ($settings['show_navigation_arrows']) : ?>

                <div class="bdt-navigation-arrows bdt-position-bottom-left bdt-margin-large-bottom reveal-muted">
                    <a class="bdt-prime-slider-previous" href="#" bdt-slidenav-previous bdt-slideshow-item="previous" ></a>
        
                    <a class="bdt-prime-slider-next" href="#" bdt-slidenav-next bdt-slideshow-item="next"></a>

                    <ul class="bdt-ps-counternav bdt-position-bottom bdt-visible@s">
                        <?php $slide_index = 1; foreach ( $settings['slides'] as $slide ) : ?>
                            <li bdt-slideshow-item="<?php echo esc_attr($slide_index - 1); ?>" data-label="<?php echo esc_attr(str_pad( $slide_index, 2, '0', STR_PAD_LEFT)); ?>" ><a href="#"><?php echo esc_attr(str_pad( $slide_index, 2, '0', STR_PAD_LEFT)); ?></a></li>
                        <?php $slide_index++;  endforeach; ?>

                        <span><?php echo esc_attr(str_pad( $slide_index - 1, 2, '0', STR_PAD_LEFT)); ?></span>
                    </ul>

                </div>

            <?php endif; ?>

        <?php
    }

    public function render_navigation_dots() {
        $settings = $this->parent->get_settings_for_display();

        ?>

            <?php if ($settings['show_navigation_dots']) : ?>

                <ul class="bdt-slideshow-nav bdt-dotnav bdt-dotnav-vertical bdt-position-center-left reveal-muted"></ul>

            <?php endif; ?>

        <?php
    }

    public function render_footer() {
        ?>
        
            </ul>
                
                <?php $this->render_navigation_arrows(); ?>
                <?php $this->render_navigation_dots(); ?>

                </div>
                
                <?php $this->parent->render_social_link( 'top', true, ['bdt-flex', 'bdt-flex-middle',  'bdt-margin-large-bottom', 'bdt-position-bottom-right'] ); ?>
            </div>
        </div>
        <?php
    }

    public function rendar_item_content($slide_content) {
        $settings = $this->parent->get_settings_for_display();

        $this->parent->add_render_attribute(
			[
				'title-link' => [
					'class' => [
						'bdt-slider-title-link',
					],
					'href'   => $slide_content['title_link']['url'] ? esc_url($slide_content['title_link']['url']) : 'javascript:void(0);',
					'target' => $slide_content['title_link']['is_external'] ? '_blank' : '_self'
				]
			], '', '', true
        );

        $parallax_sub_title = 'data-bdt-slideshow-parallax="x: 300,0,-100; opacity: 1,1,0"';   
        $parallax_title     = 'data-bdt-slideshow-parallax="x: 500,0,-100; opacity: 1,1,0"';
        $parallax_excerpt   = 'data-bdt-slideshow-parallax="y: 200,0,-100; opacity: 1,1,0"';

        if ( true === _is_ps_pro_activated() ) {
            if($settings['animation_status'] == 'yes' && !empty($settings['animation_of'])){

                if( in_array( ".bdt-ps-sub-title" ,$settings['animation_of'] ) )
                {
                    $parallax_sub_title ='';
                }
                if( in_array( ".bdt-title-tag" ,$settings['animation_of'] ) )
                {
                    $parallax_title ='';
                }
                if( in_array( ".bdt-slider-excerpt" ,$settings['animation_of'] ) )
                {
                    $parallax_excerpt ='';
                }

            }
        }
        
        ?>

        <div class="bdt-container">
            <div class="bdt-slideshow-content-wrapper">
                <div class="bdt-prime-slider-wrapper">
                    <div class="bdt-prime-slider-content">

                        <div class="bdt-prime-slider-desc">

                            <?php if ($slide_content['sub_title'] && ( 'yes' == $settings['show_sub_title'] )) : ?>
                                <div class="bdt-sub-title bdt-text-left">
                                    <<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?> <?php echo wp_kses_post($parallax_sub_title); ?> data-reveal="reveal-active" class="bdt-ps-sub-title">
                                    <?php echo wp_kses_post($slide_content['sub_title']); ?>
                                    </<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?>>
                                </div>
                            <?php endif; ?>

                            <?php if ($slide_content['title'] && ( 'yes' == $settings['show_title'] )) : ?>
                                <div class="bdt-main-title bdt-text-left"  <?php echo wp_kses_post($parallax_title); ?> data-reveal="reveal-active">
                                    <<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?> class="bdt-title-tag">
                                        <?php if ( '' !== $slide_content['title_link']['url'] ) : ?>
                                            <a <?php $this->parent->print_render_attribute_string( 'title-link' ); ?>>
                                        <?php endif; ?>
                                            <?php echo wp_kses_post($slide_content['title']); ?>
                                        <?php if ( '' !== $slide_content['title_link']['url'] ) : ?>
                                            </a>
                                        <?php endif; ?>
                                    </<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?>>
                                </div>
                            <?php endif; ?>
                            
                        </div>
                                    
                    </div>
                    <?php if ($slide_content['excerpt'] && ( 'yes' == $settings['show_excerpt'] )) : ?>
                    <div class="bdt-slider-excerpt-content" <?php echo wp_kses_post($parallax_excerpt); ?>>
                        <?php if ($settings['show_otherview'] == 'yes') : ?>
                        <h3 data-reveal="reveal-active"><?php echo esc_html('Overview', 'bdthemes-prime-slider') ?></h3>
                        <?php endif; ?>
                        <div class="bdt-slider-excerpt" data-reveal="reveal-active">
                            <?php echo wp_kses_post($slide_content['excerpt']); ?>
                        </div>
                        <div class="bdt-crelly-btn" data-bdt-slideshow-parallax="y: 200,0,-100; opacity: 1,1,0">
                            <?php $this->parent->render_button($slide_content); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }

	public function render_slides_loop() {
        $settings = $this->parent->get_settings_for_display();

        $kenburns_reverse = $settings['kenburns_reverse'] ? ' bdt-animation-reverse' : '';

        foreach ( $settings['slides'] as $slide ) : ?>
                    
            <li class="bdt-slideshow-item bdt-flex bdt-flex-middle bdt-flex-center elementor-repeater-item-<?php echo esc_attr($slide['_id']); ?>">
                <?php if( 'yes' == $settings['kenburns_animation'] ) : ?>
                    <div class="bdt-position-cover bdt-animation-kenburns<?php echo esc_attr( $kenburns_reverse ); ?> bdt-transform-origin-center-left">
                <?php endif; ?>

                    <?php if (( $slide['background'] == 'image' ) && $slide['image']) : ?>
                        <?php $this->parent->rendar_item_image($slide, $slide['title']); ?>
                    <?php elseif (( $slide['background'] == 'video' ) && $slide['video_link']) : ?>
                        <?php $this->parent->rendar_item_video($slide); ?>
                    <?php elseif (( $slide['background'] == 'youtube' ) && $slide['youtube_link']) : ?>
                        <?php $this->parent->rendar_item_youtube($slide); ?>
                    <?php endif; ?>

                <?php if( 'yes' == $settings['kenburns_animation'] ) : ?>
                    </div>
                <?php endif; ?>

                <?php if( 'none' !== $settings['overlay'] ) :
                    $blend_type = ( 'blend' == $settings['overlay']) ? ' bdt-blend-'.$settings['blend_type'] : ''; ?>
                    <div class="bdt-overlay-default bdt-position-cover<?php echo esc_attr($blend_type); ?>"></div>
                <?php endif; ?>

                <?php 
                    
                    $this->rendar_item_content( $slide );
                    
                ?>
            </li>

        <?php endforeach;
    }

    public function render() {
        $skin_name = 'crelly';

        $this->parent->render_header( $skin_name );


        $this->render_slides_loop();

        $this->render_footer();
    }
}