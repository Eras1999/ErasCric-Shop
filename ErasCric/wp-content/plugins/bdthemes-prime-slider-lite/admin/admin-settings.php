<?php

use PrimeSlider\Utils;
use PrimeSlider\Admin\ModuleService;
use Elementor\Modules\Usage\Module;
use Elementor\Tracker;

/**
 * Prime Slider Admin Settings Class
 */

class PrimeSlider_Admin_Settings {

	public static $modules_list = null;
	public static $modules_names = null;

	public static $modules_list_only_widgets = null;
	public static $modules_names_only_widgets = null;

	public static $modules_list_only_3rdparty = null;
	public static $modules_names_only_3rdparty = null;

	const PAGE_ID = 'prime_slider_options';

	private $settings_api;

	public $responseObj;
	public $showMessage = false;
	private $is_activated = false;

	function __construct() {
		$this->settings_api = new PrimeSlider_Settings_API;

		if (!defined('BDTPS_CORE_HIDE')) {
			add_action('admin_init', [$this, 'admin_init']);
			add_action('admin_menu', [$this, 'admin_menu'], 201);
		}

		/**
		 * Mini-Cart issue fixed
		 * Check if MiniCart activate in EP and Elementor
		 * If both is activated then Show Notice
		 */

		$ps_3rdPartyOption = get_option('prime_slider_third_party_widget');

		$el_use_mini_cart = get_option('elementor_use_mini_cart_template');

		if ($el_use_mini_cart !== false && $ps_3rdPartyOption !== false) {
			if ($ps_3rdPartyOption) {
				if ('yes' == $el_use_mini_cart && isset($ps_3rdPartyOption['wc-mini-cart']) && 'off' !== trim($ps_3rdPartyOption['wc-mini-cart'])) {
					add_action('admin_notices', [$this, 'el_use_mini_cart'], 10, 3);
				}
			}
		}
	}

	/**
	 * Get used widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */
	public static function get_used_widgets() {

		$used_widgets = array();

		if (class_exists('Elementor\Modules\Usage\Module')) {

			$module     = Module::instance();
			$elements   = $module->get_formatted_usage('raw');
			$ps_widgets = self::get_ps_widgets_names();

			if (is_array($elements) || is_object($elements)) {

				foreach ($elements as $post_type => $data) {
					foreach ($data['elements'] as $element => $count) {
						if (in_array($element, $ps_widgets, true)) {
							if (isset($used_widgets[$element])) {
								$used_widgets[$element] += $count;
							} else {
								$used_widgets[$element] = $count;
							}
						}
					}
				}
			}
		}

		return $used_widgets;
	}

	/**
	 * Get used separate widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_used_only_widgets() {

		$used_widgets = array();

		if (class_exists('Elementor\Modules\Usage\Module')) {

			$module     = Module::instance();
			$elements   = $module->get_formatted_usage('raw');
			$ps_widgets = self::get_ps_only_widgets();

			if (is_array($elements) || is_object($elements)) {

				foreach ($elements as $post_type => $data) {
					foreach ($data['elements'] as $element => $count) {
						if (in_array($element, $ps_widgets, true)) {
							if (isset($used_widgets[$element])) {
								$used_widgets[$element] += $count;
							} else {
								$used_widgets[$element] = $count;
							}
						}
					}
				}
			}
		}

		return $used_widgets;
	}

	/**
	 * Get used only separate 3rdParty widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_used_only_3rdparty() {

		$used_widgets = array();

		if (class_exists('Elementor\Modules\Usage\Module')) {

			$module     = Module::instance();
			$elements   = $module->get_formatted_usage('raw');
			$ps_widgets = self::get_ps_only_3rdparty_names();

			if (is_array($elements) || is_object($elements)) {

				foreach ($elements as $post_type => $data) {
					foreach ($data['elements'] as $element => $count) {
						if (in_array($element, $ps_widgets, true)) {
							if (isset($used_widgets[$element])) {
								$used_widgets[$element] += $count;
							} else {
								$used_widgets[$element] = $count;
							}
						}
					}
				}
			}
		}

		return $used_widgets;
	}

	/**
	 * Get unused widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_unused_widgets() {

		if (!current_user_can('install_plugins')) {
			die();
		}

		$ps_widgets = self::get_ps_widgets_names();

		$used_widgets = self::get_used_widgets();

		$unused_widgets = array_diff($ps_widgets, array_keys($used_widgets));

		return $unused_widgets;
	}

	/**
	 * Get unused separate widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_unused_only_widgets() {

		if (!current_user_can('install_plugins')) {
			die();
		}

		$ps_widgets = self::get_ps_only_widgets();

		$used_widgets = self::get_used_only_widgets();

		$unused_widgets = array_diff($ps_widgets, array_keys($used_widgets));

		return $unused_widgets;
	}

	/**
	 * Get unused separate 3rdparty widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_unused_only_3rdparty() {

		if (!current_user_can('install_plugins')) {
			die();
		}

		$ps_widgets = self::get_ps_only_3rdparty_names();

		$used_widgets = self::get_used_only_3rdparty();

		$unused_widgets = array_diff($ps_widgets, array_keys($used_widgets));

		return $unused_widgets;
	}

	/**
	 * Get widgets name
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_ps_widgets_names() {
		$names = self::$modules_names;

		if (null === $names) {
			$names = array_map(
				function ($item) {
					return isset($item['name']) ? 'prime-slider-' . str_replace('_', '-', $item['name']) : 'none';
				},
				self::$modules_list
			);
		}

		return $names;
	}

	/**
	 * Get separate widgets name
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_ps_only_widgets() {
		$names = self::$modules_names_only_widgets;

		if (null === $names) {
			$names = array_map(
				function ($item) {
					return isset($item['name']) ? 'prime-slider-' . str_replace('_', '-', $item['name']) : 'none';
				},
				self::$modules_list_only_widgets
			);
		}

		return $names;
	}

	/**
	 * Get separate 3rdParty widgets name
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_ps_only_3rdparty_names() {
		$names = self::$modules_names_only_3rdparty;

		if (null === $names) {
			$names = array_map(
				function ($item) {
					return isset($item['name']) ? 'prime-slider-' . str_replace('_', '-', $item['name']) : 'none';
				},
				self::$modules_list_only_3rdparty
			);
		}

		return $names;
	}

	/**
	 * Get URL with page id
	 *
	 * @access public
	 *
	 */

	public static function get_url() {
		return admin_url('admin.php?page=' . self::PAGE_ID);
	}

	/**
	 * Init settings API
	 *
	 * @access public
	 *
	 */

	public function admin_init() {

		//set the settings
		$this->settings_api->set_sections($this->get_settings_sections());
		$this->settings_api->set_fields($this->prime_slider_admin_settings());

		//initialize settings
		$this->settings_api->admin_init();
	}

	/**
	 * Add Plugin Menus
	 *
	 * @access public
	 *
	 */

	public function admin_menu() {
		add_menu_page(
			BDTPS_CORE_TITLE . ' ' . esc_html__('Dashboard', 'bdthemes-prime-slider'),
			BDTPS_CORE_TITLE,
			'manage_options',
			self::PAGE_ID,
			[$this, 'plugin_page'],
			$this->prime_slider_icon(),
			58
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTPS_CORE_TITLE,
			esc_html__('Core Widgets', 'bdthemes-prime-slider'),
			'manage_options',
			self::PAGE_ID . '#prime_slider_active_modules',
			[$this, 'display_page']
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTPS_CORE_TITLE,
			esc_html__('3rd Party Widgets', 'bdthemes-prime-slider'),
			'manage_options',
			self::PAGE_ID . '#prime_slider_third_party_widget',
			[$this, 'display_page']
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTPS_CORE_TITLE,
			esc_html__('Extensions', 'bdthemes-prime-slider'),
			'manage_options',
			self::PAGE_ID . '#prime_slider_elementor_extend',
			[$this, 'display_page']
		);

		// add_submenu_page(
		//     self::PAGE_ID,
		//     BDTPS_CORE_TITLE,
		//     esc_html__('API Settings', 'bdthemes-prime-slider'),
		//     'manage_options',
		//     self::PAGE_ID . '#prime_slider_api_settings',
		//     [$this, 'display_page']
		// );

		add_submenu_page(
			self::PAGE_ID,
			BDTPS_CORE_TITLE,
			esc_html__('Other Settings', 'bdthemes-prime-slider'),
			'manage_options',
			self::PAGE_ID . '#prime_slider_other_settings',
			[$this, 'display_page']
		);

		if (true !== _is_ps_pro_activated()) {
			add_submenu_page(
				self::PAGE_ID,
				BDTPS_CORE_TITLE,
				esc_html__('Get Pro', 'bdthemes-prime-slider'),
				'manage_options',
				self::PAGE_ID . '#prime_slider_get_pro',
				[$this, 'display_page']
			);
		}
	}

	/**
	 * Get SVG Icons of Prime Slider
	 *
	 * @access public
	 * @return string
	 */

	public function prime_slider_icon() {
		return 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyMy4wLjMsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCAyMzAuNyAyNTQuOCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjMwLjcgMjU0Ljg7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtmaWxsOnVybCgjU1ZHSURfMV8pO30NCgkuc3Qxe2ZpbGw6dXJsKCNTVkdJRF8yXyk7fQ0KCS5zdDJ7ZmlsbDp1cmwoI1NWR0lEXzNfKTt9DQoJLnN0M3tmaWxsOnVybCgjU1ZHSURfNF8pO30NCgkuc3Q0e2ZpbGw6dXJsKCNTVkdJRF81Xyk7fQ0KPC9zdHlsZT4NCjxnPg0KCTxsaW5lYXJHcmFkaWVudCBpZD0iU1ZHSURfMV8iIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIiB4MT0iMTY1Ljg4MTMiIHkxPSItOS4xNzQyIiB4Mj0iLTE0Ljk3ODMiIHkyPSIxOTIuNzE1NiI+DQoJCTxzdG9wICBvZmZzZXQ9IjAiIHN0eWxlPSJzdG9wLWNvbG9yOiNGQzZBMkMiLz4NCgkJPHN0b3AgIG9mZnNldD0iMSIgc3R5bGU9InN0b3AtY29sb3I6I0ZFNTE2QiIvPg0KCTwvbGluZWFyR3JhZGllbnQ+DQoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTIwMi4yLDY5LjJoLTE3NGMtMywwLTUuNS0yLjUtNS41LTUuNVYzMS4xYzAtMywyLjUtNS41LDUuNS01LjVoMTc0YzMsMCw1LjUsMi41LDUuNSw1LjV2MzIuNg0KCQlDMjA3LjcsNjYuOCwyMDUuMiw2OS4yLDIwMi4yLDY5LjJ6Ii8+DQoJPGxpbmVhckdyYWRpZW50IGlkPSJTVkdJRF8yXyIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiIHgxPSIyMDUuNjI4MSIgeTE9IjI2LjQzMjMiIHgyPSIyNC43Njg1IiB5Mj0iMjI4LjMyMjEiPg0KCQk8c3RvcCAgb2Zmc2V0PSIwIiBzdHlsZT0ic3RvcC1jb2xvcjojRkM2QTJDIi8+DQoJCTxzdG9wICBvZmZzZXQ9IjEiIHN0eWxlPSJzdG9wLWNvbG9yOiNGRTUxNkIiLz4NCgk8L2xpbmVhckdyYWRpZW50Pg0KCTxwYXRoIGNsYXNzPSJzdDEiIGQ9Ik0yMDIuMiwxNDkuMmgtMTc0Yy0zLDAtNS41LTIuNS01LjUtNS41di0zMi42YzAtMywyLjUtNS41LDUuNS01LjVoMTc0YzMsMCw1LjUsMi41LDUuNSw1LjV2MzIuNg0KCQlDMjA3LjcsMTQ2LjgsMjA1LjIsMTQ5LjIsMjAyLjIsMTQ5LjJ6Ii8+DQoJPGxpbmVhckdyYWRpZW50IGlkPSJTVkdJRF8zXyIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiIHgxPSIyMjMuMDM5IiB5MT0iNDIuMDI5NSIgeDI9IjQyLjE3OTQiIHkyPSIyNDMuOTE5NCI+DQoJCTxzdG9wICBvZmZzZXQ9IjAiIHN0eWxlPSJzdG9wLWNvbG9yOiNGQzZBMkMiLz4NCgkJPHN0b3AgIG9mZnNldD0iMSIgc3R5bGU9InN0b3AtY29sb3I6I0ZFNTE2QiIvPg0KCTwvbGluZWFyR3JhZGllbnQ+DQoJPHBhdGggY2xhc3M9InN0MiIgZD0iTTEyMS42LDIyOS4ySDI4LjJjLTMsMC01LjUtMi41LTUuNS01LjV2LTMyLjZjMC0zLDIuNS01LjUsNS41LTUuNWg5My41YzMsMCw1LjUsMi41LDUuNSw1LjV2MzIuNg0KCQlDMTI3LjIsMjI2LjcsMTI0LjcsMjI5LjIsMTIxLjYsMjI5LjJ6Ii8+DQoJPGxpbmVhckdyYWRpZW50IGlkPSJTVkdJRF80XyIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiIHgxPSIxNDYuMDMzMSIgeTE9Ii0yNi45NTUiIHgyPSItMzQuODI2NiIgeTI9IjE3NC45MzQ4Ij4NCgkJPHN0b3AgIG9mZnNldD0iMCIgc3R5bGU9InN0b3AtY29sb3I6I0ZDNkEyQyIvPg0KCQk8c3RvcCAgb2Zmc2V0PSIxIiBzdHlsZT0ic3RvcC1jb2xvcjojRkU1MTZCIi8+DQoJPC9saW5lYXJHcmFkaWVudD4NCgk8cGF0aCBjbGFzcz0ic3QzIiBkPSJNNjYuMyw0NS43VjEyN2MwLDMtMi41LDUuNS01LjUsNS41SDI4LjJjLTMsMC01LjUtMi41LTUuNS01LjVWNDUuN2MwLTMsMi41LTUuNSw1LjUtNS41aDMyLjYNCgkJQzYzLjgsNDAuMiw2Ni4zLDQyLjcsNjYuMyw0NS43eiIvPg0KCTxsaW5lYXJHcmFkaWVudCBpZD0iU1ZHSURfNV8iIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIiB4MT0iMjY0LjcxMzQiIHkxPSI3OS4zNjI4IiB4Mj0iODMuODUzNyIgeTI9IjI4MS4yNTI2Ij4NCgkJPHN0b3AgIG9mZnNldD0iMCIgc3R5bGU9InN0b3AtY29sb3I6I0ZDNkEyQyIvPg0KCQk8c3RvcCAgb2Zmc2V0PSIxIiBzdHlsZT0ic3RvcC1jb2xvcjojRkU1MTZCIi8+DQoJPC9saW5lYXJHcmFkaWVudD4NCgk8cGF0aCBjbGFzcz0ic3Q0IiBkPSJNMjA3LjcsMTExLjF2MTEyLjZjMCwzLTIuNSw1LjUtNS41LDUuNWgtMzIuNmMtMywwLTUuNS0yLjUtNS41LTUuNVYxMTEuMWMwLTMsMi41LTUuNSw1LjUtNS41aDMyLjYNCgkJQzIwNS4yLDEwNS42LDIwNy43LDEwOCwyMDcuNywxMTEuMXoiLz4NCjwvZz4NCjwvc3ZnPg0K';
	}

	/**
	 * Get SVG Icons of Prime Slider
	 *
	 * @access public
	 * @return array
	 */

	public function get_settings_sections() {
		$sections = [
			[
				'id'    => 'prime_slider_active_modules',
				'title' => esc_html__('Core Widgets', 'bdthemes-prime-slider')
			],
			[
				'id'    => 'prime_slider_third_party_widget',
				'title' => esc_html__('3rd Party Widgets', 'bdthemes-prime-slider')
			],
			[
				'id'    => 'prime_slider_elementor_extend',
				'title' => esc_html__('Extensions', 'bdthemes-prime-slider')
			],
			// [
			//     'id'    => 'prime_slider_api_settings',
			//     'title' => esc_html__('API Settings', 'bdthemes-prime-slider'),
			// ],
			[
				'id'    => 'prime_slider_other_settings',
				'title' => esc_html__('Other Settings', 'bdthemes-prime-slider'),
			],
		];

		return $sections;
	}

	/**
	 * Merge Admin Settings
	 *
	 * @access protected
	 * @return array
	 */

	protected function prime_slider_admin_settings() {

		return ModuleService::get_widget_settings(function ($settings) {
			$settings_fields = $settings['settings_fields'];

			self::$modules_list               = array_merge($settings_fields['prime_slider_active_modules'], $settings_fields['prime_slider_third_party_widget']);
			self::$modules_list_only_widgets  = $settings_fields['prime_slider_active_modules'];
			self::$modules_list_only_3rdparty = $settings_fields['prime_slider_third_party_widget'];

			return $settings_fields;
		});
	}

	/**
	 * Get Welcome Panel
	 *
	 * @access public
	 * @return void
	 */

	public function prime_slider_welcome() {
		$track_nw_msg = '';
		if (!Tracker::is_allow_track()) {
			$track_nw     = esc_html__('This feature is not working because the Elementor Usage Data Sharing feature is Not Enabled.', 'bdthemes-prime-slider');
			$track_nw_msg = 'bdt-tooltip="' . $track_nw . '"';
		}
?>

		<div class="ps-dashboard-panel" bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">

			<div class="bdt-grid" bdt-grid bdt-height-match="target: > div > .bdt-card">
				<div class="bdt-width-1-2@m bdt-width-1-4@l">
					<div class="ps-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<?php
						$used_widgets    = count(self::get_used_widgets());
						$un_used_widgets = count(self::get_unused_widgets());
						?>


						<div class="ps-count-canvas-wrap bdt-flex bdt-flex-between">
							<div class="ps-count-wrap">
								<h1 class="ps-feature-title">All Widgets</h1>
								<div class="ps-widget-count">Used: <b>
										<?php echo esc_html($used_widgets); ?>
									</b></div>
								<div class="ps-widget-count">Unused: <b>
										<?php echo esc_html($un_used_widgets); ?>
									</b></div>
								<div class="ps-widget-count">Total:
									<b>
										<?php echo esc_html($used_widgets + $un_used_widgets); ?>
									</b>
								</div>
							</div>

							<div class="ps-canvas-wrap">
								<canvas id="bdt-db-total-status" style="height: 120px; width: 120px;" data-label="Total Widgets Status - (<?php echo esc_html($used_widgets + $un_used_widgets); ?>)" data-labels="<?php echo esc_attr('Used, Unused'); ?>" data-value="<?php echo esc_attr($used_widgets) . ',' . esc_attr($un_used_widgets); ?>" data-bg="#FFD166, #fff4d9" data-bg-hover="#0673e1, #e71522"></canvas>
							</div>
						</div>

					</div>
				</div>
				<div class="bdt-width-1-2@m bdt-width-1-4@l">
					<div class="ps-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<?php
						$used_only_widgets   = count(self::get_used_only_widgets());
						$unused_only_widgets = count(self::get_unused_only_widgets());
						?>


						<div class="ps-count-canvas-wrap bdt-flex bdt-flex-between">
							<div class="ps-count-wrap">
								<h1 class="ps-feature-title">Core</h1>
								<div class="ps-widget-count">Used: <b>
										<?php echo esc_html($used_only_widgets); ?>
									</b></div>
								<div class="ps-widget-count">Unused: <b>
										<?php echo esc_html($unused_only_widgets); ?>
									</b></div>
								<div class="ps-widget-count">Total:
									<b>
										<?php echo esc_html($used_only_widgets + $unused_only_widgets); ?>
									</b>
								</div>
							</div>

							<div class="ps-canvas-wrap">
								<canvas id="bdt-db-only-widget-status" style="height: 120px; width: 120px;" data-label="Core Widgets Status - (<?php echo esc_attr($used_only_widgets + $unused_only_widgets); ?>)" data-labels="<?php echo esc_attr('Used, Unused'); ?>" data-value="<?php echo esc_attr($used_only_widgets) . ',' . esc_attr($unused_only_widgets); ?>" data-bg="#EF476F, #ffcdd9" data-bg-hover="#0673e1, #e71522"></canvas>
							</div>
						</div>

					</div>
				</div>
				<div class="bdt-width-1-2@m bdt-width-1-4@l">
					<div class="ps-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<?php
						$used_only_3rdparty   = count(self::get_used_only_3rdparty());
						$unused_only_3rdparty = count(self::get_unused_only_3rdparty());
						?>


						<div class="ps-count-canvas-wrap bdt-flex bdt-flex-between">
							<div class="ps-count-wrap">
								<h1 class="ps-feature-title">3rd Party</h1>
								<div class="ps-widget-count">Used: <b>
										<?php echo esc_html($used_only_3rdparty); ?>
									</b></div>
								<div class="ps-widget-count">Unused: <b>
										<?php echo esc_html($unused_only_3rdparty); ?>
									</b></div>
								<div class="ps-widget-count">Total:
									<b>
										<?php echo esc_html($used_only_3rdparty + $unused_only_3rdparty); ?>
									</b>
								</div>
							</div>

							<div class="ps-canvas-wrap">
								<canvas id="bdt-db-only-3rdparty-status" style="height: 120px; width: 120px;" data-label="3rd Party Widgets Status - (<?php echo esc_attr($used_only_3rdparty + $unused_only_3rdparty); ?>)" data-labels="<?php echo esc_attr('Used, Unused'); ?>" data-value="<?php echo esc_attr($used_only_3rdparty) . ',' . esc_attr($unused_only_3rdparty); ?>" data-bg="#06D6A0, #B6FFEC" data-bg-hover="#0673e1, #e71522"></canvas>
							</div>
						</div>

					</div>
				</div>

				<div class="bdt-width-1-2@m bdt-width-1-4@l">
					<div class="ps-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<div class="ps-count-canvas-wrap bdt-flex bdt-flex-between">
							<div class="ps-count-wrap">
								<h1 class="ps-feature-title">Active</h1>
								<div class="ps-widget-count">Core: <b id="bdt-total-widgets-status-core"></b></div>
								<div class="ps-widget-count">3rd Party: <b id="bdt-total-widgets-status-3rd"></b></div>
								<div class="ps-widget-count">Total: <b id="bdt-total-widgets-status-heading"></b></div>
							</div>

							<div class="ps-canvas-wrap">
								<canvas id="bdt-total-widgets-status" style="height: 120px; width: 120px;" data-label="Total Active Widgets Status" data-labels="<?php echo esc_attr('Core, 3rd Party'); ?>" data-bg="#0680d6, #B0EBFF" data-bg-hover="#0673e1, #B0EBFF">
								</canvas>
							</div>
						</div>

					</div>
				</div>
			</div>

			<?php if ( !Tracker::is_allow_track() ) : ?>
				<div class="bdt-border-rounded bdt-box-shadow-small bdt-alert-warning" bdt-alert>
					<a href class="bdt-alert-close" bdt-close></a>
					<div class="bdt-text-default">
						<?php
						esc_html_e('To view widgets analytics, Elementor Usage Data Sharing feature by Elementor needs to be activated. Please activate the feature to get widget analytics instantly ', 'bdthemes-element-pack');
						echo '<a href="' . esc_url(admin_url('admin.php?page=elementor')) . '">from here.</a>';
						?>
					</div>
				</div>
			<?php endif; ?>

			<div class="bdt-grid" bdt-grid bdt-height-match="target: > div > .bdt-card">
				<div class="bdt-width-1-3@m ps-support-section">
					<div class="ps-support-content bdt-card bdt-card-body">
						<h1 class="ps-feature-title">Support And Feedback</h1>
						<p>Feeling like to consult with an expert? Take live Chat support immediately from <a href="https://PrimeSlider.pro" target="_blank" rel="">PrimeSlider</a>. We are always
							ready to help
							you 24/7.</p>
						<p><strong>Or if you’re facing technical issues with our plugin, then please create a support
								ticket</strong></p>
						<a class="bdt-button bdt-btn-blue bdt-margin-small-top bdt-margin-small-right" target="_blank" rel="" href="https://bdthemes.com/all-knowledge-base-of-prime-slider/">Knowledge
							Base</a>
						<a class="bdt-button bdt-btn-grey bdt-margin-small-top" target="_blank" href="https://bdthemes.com/support/">Get Support</a>
					</div>
				</div>

				<div class="bdt-width-2-3@m">
					<div class="bdt-card bdt-card-body ps-system-requirement">
						<h1 class="ps-feature-title bdt-margin-small-bottom">System Requirement</h1>
						<?php $this->prime_slider_system_requirement(); ?>
					</div>
				</div>
			</div>

			<div class="bdt-grid" bdt-grid bdt-height-match="target: > div > .bdt-card">
				<div class="bdt-width-1-2@m ps-support-section">
					<div class="bdt-card bdt-card-body ps-feedback-bg">
						<h1 class="ps-feature-title">Missing Any Feature?</h1>
						<p style="max-width: 520px;">Are you in need of a feature that’s not available in our plugin?
							Feel free to do a feature request from here,</p>
						<a class="bdt-button bdt-btn-grey bdt-margin-small-top" target="_blank" rel="" href="https://feedback.bdthemes.com/b/6vr2250l/feature-requests/">Request Feature</a>
					</div>
				</div>

				<div class="bdt-width-1-2@m">
					<div class="bdt-card bdt-card-body ps-tryaddon-bg">
						<h1 class="ps-feature-title">Try Our Others Addons</h1>
						<p style="max-width: 520px;">
							<b>Element Pack, Ultimate Post Kit, Ultimate Store Kit, Pixel Gallery & Live Copy Paste </b> addons
							for <b>Elementor</b> is the best slider, blogs and eCommerce plugin for WordPress.
						</p>
						<div class="bdt-others-plugins-link">
							<a class="bdt-button bdt-btn-ep bdt-margin-small-right" target="_blank" href="https://wordpress.org/plugins/bdthemes-element-pack-lite/" bdt-tooltip="Element Pack Lite provides more than 50+ essential elements for everyday applications to simplify the whole web building process. It's Free! Download it.">Element
								pack</a>
							<a class="bdt-button bdt-btn-ps bdt-margin-small-right" target="_blank" rel="" href="https://wordpress.org/plugins/ultimate-post-kit/" bdt-tooltip="Best blogging addon for building quality blogging website with fine-tuned features and widgets. It's Free! Download it.">Ultimate
								Post Kit</a>
							<a class="bdt-button bdt-btn-usk bdt-margin-small-right" target="_blank" rel="" href="https://wordpress.org/plugins/ultimate-store-kit/" bdt-tooltip="The only eCommmerce addon for answering all your online store design problems in one package. It's Free! Download it.">Ultimate
								Store Kit</a>
							<a class="bdt-button bdt-btn-pg bdt-margin-small-right" target="_blank" href="https://wordpress.org/plugins/pixel-gallery/" bdt-tooltip="Pixel Gallery provides more than 30+ essential elements for everyday applications to simplify the whole web building process. It's Free! Download it.">Pixel
								Gallery</a>
							<a class="bdt-button bdt-btn-live-copy bdt-margin-small-right" target="_blank" rel="" href="https://wordpress.org/plugins/live-copy-paste/" bdt-tooltip="Superfast cross-domain copy-paste mechanism for WordPress websites with true UI copy experience. It's Free! Download it.">Live
								Copy Paste</a>
						</div>
					</div>
				</div>
			</div>

		</div>


	<?php
	}

	/**
	 * Get Pro
	 *
	 * @access public
	 * @return void
	 */

	function prime_slider_get_pro() {
	?>
		<div class="ps-dashboard-panel" bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">

			<div class="bdt-grid" bdt-grid bdt-height-match="target: > div > .bdt-card" style="max-width: 800px; margin-left: auto; margin-right: auto;">
				<div class="bdt-width-1-1@m ps-comparision bdt-text-center">
					<h1 class="bdt-text-bold">WHY GO WITH PRO?</h1>
					<h2>Just Compare With Prime Slider Free Vs Pro</h2>


					<div>

						<ul class="bdt-list bdt-list-divider bdt-text-left bdt-text-normal" style="font-size: 16px;">


							<li class="bdt-text-bold">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">Features</div>
									<div class="bdt-width-auto@m">Free</div>
									<div class="bdt-width-auto@m">Pro</div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><span bdt-tooltip="pos: top-left; title: Free have 27+ Widgets but Pro have 21+ core widgets">Core
											Widgets</span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><span bdt-tooltip="pos: top-left; title: Free have 3+ Widgets but Pro have 3+ 3rd party widgets">3rd
											Party Widgets</span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">Theme Compatibility</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">Dynamic Content & Custom Fields Capabilities</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">Proper Documentation</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">Updates & Support</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">Rooten Theme Pro Features</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">Priority Support</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">Ready Made Pages</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">Ready Made Blocks</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">Elementor Extended Widgets</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">Live Copy or Paste</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">Duplicator</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">Reveal Effects</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
						</ul>


						<div class="ps-dashboard-divider"></div>


						<div class="ps-more-features">
							<ul class="bdt-list bdt-list-divider bdt-text-left" style="font-size: 16px;">
								<li>
									<div class="bdt-grid">
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span> Incredibly Advanced
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span> Refund or Cancel Anytime
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span> Dynamic Content
										</div>
									</div>
								</li>

								<li>
									<div class="bdt-grid">
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span> Super-Flexible Widgets
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span> 24/7 Premium Support
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span> Third Party Plugins
										</div>
									</div>
								</li>

								<li>
									<div class="bdt-grid">
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span> Special Discount!
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span> Custom Field Integration
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span> With Live Chat Support
										</div>
									</div>
								</li>

								<li>
									<div class="bdt-grid">
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span> Trusted Payment Methods
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span> Interactive Effects
										</div>
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span> Video Tutorial
										</div>
									</div>
								</li>
							</ul>

							<?php if (true !== _is_ps_pro_activated()) : ?>
								<div class="ps-purchase-button">
									<a href="https://primeslider.pro/pricing/" target="_blank">Purchase Now</a>
								</div>
							<?php endif; ?>

						</div>

					</div>
				</div>
			</div>

		</div>
	<?php
	}


	/**
	 * Display System Requirement
	 *
	 * @access public
	 * @return void
	 */

	function prime_slider_system_requirement() {
		$php_version        = phpversion();
		$max_execution_time = ini_get('max_execution_time');
		$memory_limit       = ini_get('memory_limit');
		$post_limit         = ini_get('post_max_size');
		$uploads            = wp_upload_dir();
		$upload_path        = $uploads['basedir'];

		$environment = Utils::get_environment_info();


	?>
		<ul class="check-system-status bdt-grid bdt-child-width-1-2@m bdt-grid-small ">
			<li>
				<div>

					<span class="label1">PHP Version: </span>

					<?php
					if (version_compare($php_version, '7.0.0', '<')) {
						echo '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';
						echo '<span class="label2" title="Min: 7.0 Recommended" bdt-tooltip>Currently: ' . esc_html($php_version) . '</span>';
					} else {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
						echo '<span class="label2">Currently: ' . esc_html($php_version) . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1">Max execution time: </span>

					<?php
					if ($max_execution_time < '90') {
						echo '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';
						echo '<span class="label2" title="Min: 90 Recommended" bdt-tooltip>Currently: ' . esc_html($max_execution_time) . '</span>';
					} else {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
						echo '<span class="label2">Currently: ' . esc_html($max_execution_time) . '</span>';
					}
					?>
				</div>
			</li>
			<li>
				<div>
					<span class="label1">Memory Limit: </span>

					<?php
					if (intval($memory_limit) < '812') {
						echo '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';
						echo '<span class="label2" title="Min: 812M Recommended" bdt-tooltip>Currently: ' . esc_html($memory_limit) . '</span>';
					} else {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
						echo '<span class="label2">Currently: ' . esc_html($memory_limit) . '</span>';
					}
					?>
				</div>
			</li>
			<li>
				<div>
					<span class="label1">Max Post Limit: </span>

					<?php
					if (intval($post_limit) < '32') {
						echo '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';
						echo '<span class="label2" title="Min: 32M Recommended" bdt-tooltip>Currently: ' . esc_html($post_limit) . '</span>';
					} else {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
						echo '<span class="label2">Currently: ' . esc_html($post_limit) . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1">Uploads folder writable: </span>

					<?php
					if (!is_writable($upload_path)) {
						echo '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';
					} else {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1">MultiSite: </span>

					<?php
					if ($environment['wp_multisite']) {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
						echo '<span class="label2">MultiSite</span>';
					} else {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
						echo '<span class="label2">No MultiSite </span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1">GZip Enabled: </span>

					<?php
					if ($environment['gzip_enabled']) {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
					} else {
						echo '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1">Debug Mode: </span>
					<?php
					if ($environment['wp_debug_mode']) {
						echo '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';
						echo '<span class="label2">Currently Turned On</span>';
					} else {
						echo '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
						echo '<span class="label2">Currently Turned Off</span>';
					}
					?>
				</div>
			</li>

		</ul>

		<div class="bdt-admin-alert">
			<strong>Note:</strong> If you have multiple addons like <b>Prime Slider</b> so you need some more
			requirement some
			cases so make sure you added more memory for others addon too.
		</div>
	<?php
	}

	/**
	 * Display Plugin Page
	 *
	 * @access public
	 * @return void
	 */

	function plugin_page() {

		echo '<div class="wrap prime-slider-dashboard">';
		echo '<h1>' . wp_kses_post(BDTPS_CORE_TITLE) . ' Settings</h1>';

		$this->settings_api->show_navigation();

	?>


		<div class="bdt-switcher bdt-tab-container bdt-container-xlarge">
			<div id="prime_slider_welcome_page" class="ps-option-page group">
				<?php $this->prime_slider_welcome(); ?>

				<?php if (!defined('BDTPS_CORE_WL')) {
					$this->footer_info();
				} ?>
			</div>

			<?php
			$this->settings_api->show_forms();
			?>

			<?php if (_is_ps_pro_activated() !== true) : ?>
				<div id="prime_slider_get_pro" class="ps-option-page group">
					<?php $this->prime_slider_get_pro(); ?>
				</div>
			<?php endif; ?>

			<div id="prime_slider_pro_license_settings_page" class="ps-option-page group">

				<?php
				if (_is_ps_pro_activated() == true) {
					apply_filters('ps_license_page', '');
				}
				?>
			</div>

		</div>

		</div>

		<?php

		$this->script();

		?>

	<?php
	}


	/**
	 * Tabbable JavaScript codes & Initiate Color Picker
	 *
	 * This code uses localstorage for displaying active tabs
	 */
	function script() {
	?>
		<script>
			jQuery(document).ready(function() {
				jQuery('.ps-no-result').removeClass('bdt-animation-shake');
			});

			function filterSearch(e) {
				var parentID = '#' + jQuery(e).data('id');

				var search = jQuery(parentID).find('.bdt-search-input').val().toLowerCase();

				jQuery(".ps-options .ps-option-item").filter(function() {
					jQuery(this).toggle(jQuery(this).attr('data-widget-name').toLowerCase().indexOf(search) > -1)
				});

				if (!search) {
					jQuery(parentID).find('.bdt-search-input').attr('bdt-filter-control', "");
					jQuery(parentID).find('.ps-widget-all').trigger('click');
				} else {
					jQuery(parentID).find('.bdt-search-input').attr('bdt-filter-control', "filter: [data-widget-name*='" + search + "']");
					jQuery(parentID).find('.bdt-search-input').removeClass('bdt-active'); // Thanks to Bar-Rabbas
					jQuery(parentID).find('.bdt-search-input').trigger('click');
				}
			}

			jQuery('.ps-options-parent').each(function(e, item) {
				var eachItem = '#' + jQuery(item).attr('id');
				jQuery(eachItem).on("beforeFilter", function() {
					jQuery(eachItem).find('.ps-no-result').removeClass('bdt-animation-shake');
				});

				jQuery(eachItem).on("afterFilter", function() {

					var isElementVisible = false;
					var i = 0;

					while (!isElementVisible && i < jQuery(eachItem).find(".ps-option-item").length) {
						if (jQuery(eachItem).find(".ps-option-item").eq(i).is(":visible")) {
							isElementVisible = true;
						}
						i++;
					}

					if (isElementVisible === false) {
						jQuery(eachItem).find('.ps-no-result').addClass('bdt-animation-shake');
					}
				});


			});


			jQuery('.ps-widget-filter-nav li a').on('click', function(e) {
				jQuery(this).closest('.bdt-widget-filter-wrapper').find('.bdt-search-input').val('');
				jQuery(this).closest('.bdt-widget-filter-wrapper').find('.bdt-search-input').val('').attr('bdt-filter-control', '');
			});


			jQuery(document).ready(function($) {
				'use strict';

				function hashHandler() {
					var $tab = jQuery('.prime-slider-dashboard .bdt-tab');
					if (window.location.hash) {
						var hash = window.location.hash.substring(1);
						bdtUIkit.tab($tab).show(jQuery('#bdt-' + hash).data('tab-index'));
					}
				}

				jQuery(window).on('load', function() {
					hashHandler();
				});

				window.addEventListener("hashchange", hashHandler, true);

				jQuery('.toplevel_page_prime_slider_options > ul > li > a ').on('click', function(event) {
					jQuery(this).parent().siblings().removeClass('current');
					jQuery(this).parent().addClass('current');
				});

				jQuery('#prime_slider_active_modules_page a.ps-active-all-widget').click(function() {

					jQuery('#prime_slider_active_modules_page .checkbox:visible').not("[disabled]").each(function() {
						jQuery(this).attr('checked', 'checked').prop("checked", true);
					});

					jQuery(this).addClass('bdt-active');
					jQuery('a.ps-deactive-all-widget').removeClass('bdt-active');
				});

				jQuery('#prime_slider_active_modules_page a.ps-deactive-all-widget').click(function() {

					jQuery('#prime_slider_active_modules_page .checkbox:visible').not("[disabled]").each(function() {
						jQuery(this).removeAttr('checked');
					});

					jQuery(this).addClass('bdt-active');
					jQuery('a.ps-active-all-widget').removeClass('bdt-active');
				});

				jQuery('#prime_slider_third_party_widget_page a.ps-active-all-widget').click(function() {

					jQuery('#prime_slider_third_party_widget_page .checkbox:visible').not("[disabled]").each(function() {
						jQuery(this).attr('checked', 'checked').prop("checked", true);
					});

					jQuery(this).addClass('bdt-active');
					jQuery('a.ps-deactive-all-widget').removeClass('bdt-active');
				});

				jQuery('#prime_slider_third_party_widget_page a.ps-deactive-all-widget').click(function() {

					jQuery('#prime_slider_third_party_widget_page .checkbox:visible').not("[disabled]").each(function() {
						jQuery(this).removeAttr('checked');
					});

					jQuery(this).addClass('bdt-active');
					jQuery('a.ps-active-all-widget').removeClass('bdt-active');
				});

				jQuery('#prime_slider_elementor_extend_page a.ps-active-all-widget').click(function() {

					jQuery('#prime_slider_elementor_extend_page .checkbox:visible').not("[disabled]").each(function() {
						jQuery(this).attr('checked', 'checked').prop("checked", true);
					});

					jQuery(this).addClass('bdt-active');
					jQuery('a.ps-deactive-all-widget').removeClass('bdt-active');
				});

				jQuery('#prime_slider_elementor_extend_page a.ps-deactive-all-widget').click(function() {

					jQuery('#prime_slider_elementor_extend_page .checkbox:visible').not("[disabled]").each(function() {
						jQuery(this).removeAttr('checked');
					});

					jQuery(this).addClass('bdt-active');
					jQuery('a.ps-active-all-widget').removeClass('bdt-active');
				});


				jQuery('form.settings-save').submit(function(event) {
					event.preventDefault();

					bdtUIkit.notification({
						message: '<div bdt-spinner></div> <?php esc_html_e('Please wait, Saving settings...', 'bdthemes-prime-slider') ?>',
						timeout: false
					});

					jQuery(this).ajaxSubmit({
						success: function() {
							bdtUIkit.notification.closeAll();
							bdtUIkit.notification({
								message: '<span class="dashicons dashicons-yes"></span> <?php esc_html_e('Settings Saved Successfully.', 'bdthemes-prime-slider') ?>',
								status: 'primary'
							});
						},
						error: function(data) {
							bdtUIkit.notification.closeAll();
							bdtUIkit.notification({
								message: '<span bdt-icon=\'icon: warning\'></span> <?php esc_html_e('Unknown error, make sure access is correct!', 'bdthemes-prime-slider') ?>',
								status: 'warning'
							});
						}
					});

					return false;
				});

				jQuery('#prime_slider_active_modules_page .ps-pro-inactive .checkbox').each(function() {
					jQuery(this).removeAttr('checked');
					jQuery(this).attr("disabled", true);
				});
				jQuery('#prime_slider_third_party_widget_page .ps-pro-inactive .checkbox').each(function() {
					jQuery(this).removeAttr('checked');
					jQuery(this).attr("disabled", true);
				});
				jQuery('#prime_slider_elementor_extend_page .ps-pro-inactive .checkbox').each(function() {
					jQuery(this).removeAttr('checked');
					jQuery(this).attr("disabled", true);
				});
				jQuery('#prime_slider_other_settings_page .ps-pro-inactive .checkbox').each(function() {
					jQuery(this).removeAttr('checked');
					jQuery(this).attr("disabled", true);
				});

			});
		</script>
	<?php
	}

	/**
	 * Display Footer
	 *
	 * @access public
	 * @return void
	 */

	function footer_info() {
	?>

		<div class="prime-slider-footer-info bdt-margin-medium-top">

			<div class="bdt-grid ">

				<div class="bdt-width-auto@s ps-setting-save-btn">



				</div>

				<div class="bdt-width-expand@s bdt-text-right">
					<p class="">
						Prime Slider plugin made with love by <a target="_blank" href="https://bdthemes.com">BdThemes</a> Team.
						<br>All rights reserved by <a target="_blank" href="https://bdthemes.com">BdThemes.com</a>.
					</p>
				</div>
			</div>

		</div>

<?php
	}

	/**
	 * Get all the pages
	 *
	 * @return array page names with key value pairs
	 */
	function get_pages() {
		$pages         = get_pages();
		$pages_options = [];
		if ($pages) {
			foreach ($pages as $page) {
				$pages_options[$page->ID] = $page->post_title;
			}
		}

		return $pages_options;
	}
}

new PrimeSlider_Admin_Settings();
