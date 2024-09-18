<?php

namespace PrimeSlider;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

/**
 * Main class for element pack
 */
class Prime_Slider_Loader {

	/**
	 * @var Prime_Slider_Loader
	 */
	private static $_instance;

	/**
	 * @var Manager
	 */
	private $_modules_manager;

	private $classes_aliases = [ 
		'PrimeSlider\Modules\PanelPostsControl\Module' => 'PrimeSlider\Modules\QueryControl\Module',
		'PrimeSlider\Modules\PanelPostsControl\Controls\Group_Control_Posts' => 'PrimeSlider\Modules\QueryControl\Controls\Group_Control_Posts',
		'PrimeSlider\Modules\PanelPostsControl\Controls\Query' => 'PrimeSlider\Modules\QueryControl\Controls\Query',
	];

	public $elements_data = [ 
		'sections' => [],
		'columns' => [],
		'widgets' => [],
	];

	/**
	 * @deprecated
	 *
	 * @return string
	 */
	public function get_version() {
		return BDTPS_CORE_VER;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'bdthemes-prime-slider' ), '1.6.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'bdthemes-prime-slider' ), '1.6.0' );
	}

	/**
	 * @return Plugin
	 */

	public static function elementor() {
		return Plugin::$instance;
	}

	/**
	 * @return Prime_Slider_Loader
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		do_action( 'bdthemes_prime_slider_lite/init' );

		return self::$_instance;
	}

	/**
	 * we loaded module manager + admin php from here
	 * @return [type] [description]
	 */
	private function _includes() {
		$duplicator = prime_slider_option( 'duplicator', 'prime_slider_other_settings', 'off' );
		$live_copy = prime_slider_option( 'live-copy', 'prime_slider_other_settings', 'off' );

		// Admin settings controller
		require_once BDTPS_CORE_ADMIN_PATH . 'module-settings.php';
		//Assets Manager
		// require_once 'admin/optimizer/asset-minifier-manager.php';

		// Dynamic Select control
		require BDTPS_CORE_PATH . 'traits/query-controls/select-input/dynamic-select-input-module.php';
		require BDTPS_CORE_PATH . 'traits/query-controls/select-input/dynamic-select.php';
		// Global Controls
		require_once BDTPS_CORE_PATH . 'traits/global-widget-controls.php';
		//require_once BDTPS_CORE_PATH . 'traits/global-swiper-controls.php';
		//require_once BDTPS_CORE_PATH . 'traits/global-mask-controls.php';

		require BDTPS_CORE_PATH . 'includes/modules-manager.php';

		if ( ! class_exists( 'BdThemes_Duplicator' ) ) {
			if ( $duplicator == 'on' ) {
				require BDTPS_CORE_PATH . 'includes/class-duplicator.php';
			}
		}

		if ( ! class_exists( 'BdThemes_Live_Copy' ) ) {
			if ( ( $live_copy == 'on' ) && ( ! is_plugin_active( 'live-copy-paste/live-copy-paste.php' ) ) ) {
				require_once BDTPS_CORE_PATH . 'includes/live-copy/class-live-copy.php';
			}
		}


		if ( ps_is_dashboard_enabled() ) {
			if ( is_admin() ) {
				// Load admin class for admin related content process
				require_once BDTPS_CORE_ADMIN_PATH . 'admin-notice.php';
				require_once BDTPS_CORE_ADMIN_PATH . 'admin.php';
				// require BDTPS_CORE_PATH . 'includes/admin-feeds.php';
				new Admin();
			}
		}
	}

	/**
	 * Autoloader function for all classes files
	 * @param  [type] $class [description]
	 * @return [type]        [description]
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$has_class_alias = isset( $this->classes_aliases[ $class ] );

		// Backward Compatibility: Save old class name for set an alias after the new class is loaded
		if ( $has_class_alias ) {
			$class_alias_name = $this->classes_aliases[ $class ];
			$class_to_load = $class_alias_name;
		} else {
			$class_to_load = $class;
		}

		if ( ! class_exists( $class_to_load ) ) {
			$filename = strtolower(
				preg_replace(
					[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
					[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
					$class_to_load
				)
			);
			$filename = BDTPS_CORE_PATH . $filename . '.php';

			if ( is_readable( $filename ) ) {
				include( $filename );
			}
		}

		if ( $has_class_alias ) {
			class_alias( $class_alias_name, $class );
		}
	}

	/**
	 * Register all script that need for any specific widget on call basis.
	 * @return [type] [description]
	 */
	public function register_site_scripts() {

		//TODO more attractive animation
		//Thirdparty widgets
		if ( prime_slider_is_widget_enabled( 'multiscroll' ) ) {
			wp_register_script( 'jquery-multiscroll', BDTPS_CORE_ASSETS_URL . 'vendor/js/jquery.multiscroll.min.js', [ 'jquery', 'elementor-frontend' ], BDTPS_CORE_VER, true );
			wp_register_script( 'easings', BDTPS_CORE_ASSETS_URL . 'vendor/js/jquery.easings.min.js', [ 'jquery', 'elementor-frontend' ], BDTPS_CORE_VER, true );
		}
		if ( prime_slider_is_widget_enabled( 'pagepiling' ) ) {
			wp_register_script( 'jquery-pagepiling', BDTPS_CORE_ASSETS_URL . 'vendor/js/jquery.pagepiling.min.js', [ 'jquery', 'elementor-frontend' ], BDTPS_CORE_VER, true );
		}
		if ( prime_slider_is_widget_enabled( 'knily' ) or prime_slider_is_third_party_enabled( 'woolamp' ) ) {
			wp_register_script( 'goodshare', BDTPS_CORE_ASSETS_URL . 'vendor/js/goodshare.min.js', [ 'jquery', 'elementor-frontend' ], BDTPS_CORE_VER, true );
		}

		if ( prime_slider_is_third_party_enabled( 'woocircle' ) ) {
			wp_register_script( 'classie', BDTPS_CORE_ASSETS_URL . 'vendor/js/classie.min.js', [ 'jquery', 'elementor-frontend' ], BDTPS_CORE_VER, true );
			wp_register_script( 'dynamics', BDTPS_CORE_ASSETS_URL . 'vendor/js/dynamics.min.js', [ 'jquery', 'elementor-frontend' ], BDTPS_CORE_VER, true );
		}
		if ( prime_slider_is_widget_enabled( 'pieces' ) ) {
			wp_register_script( 'pieces', BDTPS_CORE_ASSETS_URL . 'vendor/js/pieces.min.js', [ 'jquery', 'elementor-frontend' ], BDTPS_CORE_VER, true );
		}
		if ( prime_slider_is_widget_enabled( 'fortune' ) or prime_slider_is_widget_enabled( 'knily' ) or prime_slider_is_widget_enabled( 'storker' ) or prime_slider_is_widget_enabled( 'omatic' ) or prime_slider_is_widget_enabled( 'sniper' ) or prime_slider_is_widget_enabled( 'mercury' ) or prime_slider_is_widget_enabled( 'coddle' ) or prime_slider_is_widget_enabled( 'escape' ) or prime_slider_is_widget_enabled( 'titanic' ) or prime_slider_is_widget_enabled( 'woohotspot' ) ) {
			wp_register_script( 'shutters', BDTPS_CORE_ASSETS_URL . 'vendor/js/effect-shutters.min.js', [ 'jquery', 'elementor-frontend' ], BDTPS_CORE_VER, true );
			wp_register_script( 'gl', BDTPS_CORE_ASSETS_URL . 'vendor/js/swiper-gl.min.js', [ 'jquery', 'elementor-frontend' ], BDTPS_CORE_VER, true );
			wp_register_script( 'slicer', BDTPS_CORE_ASSETS_URL . 'vendor/js/effect-slicer.min.js', [ 'jquery', 'elementor-frontend' ], BDTPS_CORE_VER, true );
			wp_register_script( 'tinder', BDTPS_CORE_ASSETS_URL . 'vendor/js/effect-tinder.min.js', [ 'jquery', 'elementor-frontend' ], BDTPS_CORE_VER, true );
		}
		if ( prime_slider_is_widget_enabled( 'fluent' ) or prime_slider_is_widget_enabled( 'flogia' ) ) {
			wp_register_script( 'mThumbnailScroller', BDTPS_CORE_ASSETS_URL . 'vendor/js/jquery.mThumbnailScroller.min.js', [ 'jquery', 'elementor-frontend' ], BDTPS_CORE_VER, true );
		}

		wp_register_script( 'bdt-parallax', BDTPS_CORE_ASSETS_URL . 'vendor/js/parallax.min.js', [ 'jquery' ], null, true );

	}

	public function register_site_styles() {
		$direction_suffix = is_rtl() ? '.rtl' : '';

		wp_register_style( 'prime-slider-font', BDTPS_CORE_ASSETS_URL . 'css/prime-slider-font' . $direction_suffix . '.css', [], BDTPS_CORE_VER );

	}

	/**
	 * Loading site related style from here.
	 * @return [type] [description]
	 */
	public function enqueue_site_styles() {

		$direction_suffix = is_rtl() ? '.rtl' : '';

		wp_enqueue_style( 'bdt-uikit', BDTPS_CORE_ASSETS_URL . 'css/bdt-uikit' . $direction_suffix . '.css', [], '3.17.0' );
		wp_enqueue_style( 'prime-slider-site', BDTPS_CORE_ASSETS_URL . 'css/prime-slider-site' . $direction_suffix . '.css', [], BDTPS_CORE_VER );
		wp_register_style( 'splitting', BDTPS_CORE_ASSETS_URL . 'vendor/css/splitting' . $direction_suffix . '.css', [], BDTPS_CORE_VER );
	}


	/**
	 * Loading site related script that needs all time such as uikit.
	 * @return [type] [description]
	 */
	public function enqueue_site_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.min' : '.min';

		wp_enqueue_script( 'bdt-uikit', BDTPS_CORE_ASSETS_URL . 'js/bdt-uikit.min.js', [ 'jquery' ], '3.17.0' );
		wp_enqueue_script( 'prime-slider-site', BDTPS_CORE_ASSETS_URL . 'js/prime-slider-site' . $suffix . '.js', [ 'jquery', 'elementor-frontend' ], BDTPS_CORE_VER );
	}

	public function enqueue_editor_scripts() {

		wp_enqueue_script('ps-editor', BDTPS_CORE_ASSETS_URL . 'js/prime-slider-editor.min.js', [
			'backbone-marionette',
			'elementor-common-modules',
			'elementor-editor-modules',
		], BDTPS_CORE_VER, true);

		$_is_ps_pro_activated = false;
		if (function_exists('ps_license_validation') && true === ps_license_validation()) {
			$_is_ps_pro_activated = true;
		}

		$localize_data = [
			'pro_installed'  => _is_ps_pro_activated(),
			'pro_license_activated'  => $_is_ps_pro_activated,
			'promotional_widgets'   => [],
		];

		if (!$_is_ps_pro_activated) {
			$pro_widget_map = new \PrimeSlider\Includes\Pro_Widget_Map();
			$localize_data['promotional_widgets'] = $pro_widget_map->get_pro_widget_map();
		}

		wp_localize_script('ps-editor', 'PrimeSliderConfigEditor', $localize_data);
	}

	/**
	 * Load editor editor related style from here
	 * @return [type] [description]
	 */
	public function enqueue_preview_styles() {
		$direction_suffix = is_rtl() ? '.rtl' : '';

		wp_enqueue_style( 'prime-slider-preview', BDTPS_CORE_ASSETS_URL . 'css/prime-slider-preview' . $direction_suffix . '.css', array(), BDTPS_CORE_VER );
	}


	public function enqueue_editor_styles() {
		$direction_suffix = is_rtl() ? '-rtl' : '';

		wp_enqueue_style( 'prime-slider-editor', BDTPS_CORE_ASSETS_URL . 'css/prime-slider-editor' . $direction_suffix . '.css', array(), BDTPS_CORE_VER );
		wp_enqueue_style( 'prime-slider-font', BDTPS_CORE_ASSETS_URL . 'css/prime-slider-font' . $direction_suffix . '.css', [], BDTPS_CORE_VER );
	}


	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'ps-notice-js', BDTPS_CORE_ADMIN_URL . 'assets/js/ps-notice.js', [ 'jquery' ], BDTPS_CORE_VER, true );
		$script_config = [ 
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'prime-slider' ),
		];
		wp_localize_script( 'ps-notice-js', 'PrimeSliderNoticeConfig', $script_config );
	}


	/**
	 * initialize the category
	 * @return [type] [description]
	 */
	public function prime_slider_init() {
		$this->_modules_manager = new Manager();
		do_action( 'bdthemes_prime_slider/init' );
	}

	/**
	 * initialize the category
	 */
	public function category_register() {
		$elementor = Plugin::$instance;

		// Add element category in panel
		$elementor->elements_manager->add_category(BDTPS_CORE_SLUG, ['title' => BDTPS_CORE_TITLE, 'icon' => 'font']);
	}

	private function setup_hooks() {
		add_action('elementor/elements/categories_registered', [$this, 'category_register']);
		add_action( 'elementor/init', [ $this, 'prime_slider_init' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_styles' ] );

		add_action( 'elementor/frontend/before_register_styles', [ $this, 'register_site_styles' ] );
		add_action( 'elementor/frontend/before_register_scripts', [ $this, 'register_site_scripts' ] );

		add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_preview_styles' ] );
		add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_scripts']);

		add_action( 'elementor/frontend/after_register_styles', [ $this, 'enqueue_site_styles' ] );
		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'enqueue_site_scripts' ] );

		// load WordPress dashboard scripts
		add_action( 'admin_init', [ $this, 'enqueue_admin_scripts' ] );
	}

	/**
	 * Prime_Slider_Loader constructor.
	 * @throws \Exception
	 */
	private function __construct() {
		// Register class automatically
		spl_autoload_register( [ $this, 'autoload' ] );
		// Include some backend files
		$this->_includes();
		// Finally hooked up all things here
		$this->setup_hooks();
	}
}

if ( ! defined( 'BDTPS_CORE_TESTS' ) ) {
	// In tests we run the instance manually.
	Prime_Slider_Loader::instance();
}

// handy function for push data
function prime_slider_config() {
	return Prime_Slider_Loader::instance();
}
