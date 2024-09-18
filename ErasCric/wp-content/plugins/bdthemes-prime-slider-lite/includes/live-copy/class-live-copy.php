<?php

namespace PrimeSlider\Includes\LiveCopy;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
class BdThemes_Live_Copy {

    function __construct() {
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'live_copy_enqueue']);
    }

    public function live_copy_enqueue() {
        wp_enqueue_script('bdt-live-copy-scripts', BDTPS_CORE_URL . 'includes/live-copy/assets/bdt-live-copy.js', ['jquery', 'elementor-editor'], BDTPS_CORE_VER, true);
    }
}
new BdThemes_Live_Copy();
