<?php
/**
 * Wishlist counter
 *
 * @since   1.4.0
 * @package TInvWishlist\Public
 */

// Abort direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Drop down widget
 */
class TInvWL_Public_WishlistCounter {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private static $_name;

	/**
	 * Counter
	 *
	 * @var float
	 */
	private $counter;

	/**
	 * User wishlists
	 *
	 * @var array
	 */
	private $user_wishlists;

	/**
	 * Guest wishlist
	 *
	 * @var array
	 */
	private $guest_wishlist;

	/**
	 * This class
	 *
	 * @var \TInvWL_Public_WishlistCounter
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Public_WishlistCounter
	 */
	public static function instance( string $plugin_name = TINVWL_PREFIX ): \TInvWL_Public_WishlistCounter {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $plugin_name );
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 */
	public function __construct( string $plugin_name ) {
		self::$_name = $plugin_name;
		$this->define_hooks();
	}

	/**
	 * Define hooks
	 */
	private function define_hooks(): void {
		if ( tinv_get_option( 'topline', 'menu' ) && ! is_admin() ) {
			add_filter( 'wp_get_nav_menu_items', array( $this, 'add_to_menu' ), 999, 3 );
		}
	}

	/**
	 * Adds a wishlist link to the menu.
	 *
	 * @param array $items An array of menu item post objects.
	 * @param object $menu The menu object.
	 * @param array $args An array of arguments used to retrieve menu item objects.
	 *
	 * @return array
	 */
	public function add_to_menu( $items, $menu, $args ) {
		$menu_cnt = count( $items ) + 1;
		$menu_ids = tinv_get_option( 'topline', 'menu' );

		if ( ! is_array( $menu_ids ) ) {
			$menu_ids = array( $menu_ids );
			$menu_ids = array_filter( $menu_ids );
		}

		foreach ( $menu_ids as $menu_id ) {
			if ( apply_filters( 'wpml_object_id', absint( $menu_id ), 'nav_menu', true ) == $menu->term_id && apply_filters( 'tinvwl_add_to_menu', true, $menu_id ) ) {

				$menu_order = tinv_get_option( 'topline', 'menu_order' ) ? tinv_get_option( 'topline', 'menu_order' ) : 100;

				// Item title.

				$show_icon   = (bool) tinv_get_option( 'topline', 'icon' );
				$icon_type   = tinv_get_option( 'topline', 'icon' );
				$icon_class  = ( $show_icon && tinv_get_option( 'topline', 'icon' ) ) ? 'top_wishlist-' . tinv_get_option( 'topline', 'icon' ) : '';
				$icon_style  = ( $show_icon && tinv_get_option( 'topline', 'icon' ) ) ? esc_attr( 'top_wishlist-' . tinv_get_option( 'topline', 'icon_style' ) ) : '';
				$icon_upload = tinv_get_option( 'topline', 'icon_upload' );

				$counter = tinv_get_option( 'topline', 'show_counter' ) ? '<span class="wishlist_products_counter_number"></span>' : '';

				$text = tinv_get_option( 'topline', 'show_text' ) ? apply_filters( 'tinvwl_wishlist_products_counter_text', tinv_get_option( 'topline', 'text' ) ) : '';

				$icon = '<span class="wishlist_products_counter ' . $icon_class . ' ' . $icon_style . ( empty( $text ) ? ' no-txt' : '' ) . ( 0 < $this->get_counter() ? ' wishlist-counter-with-products' : '' ) . '" >';

				if ( $icon_class && 'custom' === $icon_type && ! empty( $icon_upload ) ) {
					$icon .= sprintf( '<img src="%s"  alt="%s"/>', esc_url( $icon_upload ), esc_attr( $text ) );
				}

				$icon .= '</span>';

				$menu_title = apply_filters( 'tinvwl_wishlist_products_counter_menu_html', $icon . ' ' . $text . ' ' . $counter, $icon, $text, $counter );

				if ( $menu_title ) {

					$wishlist_item = (object) array(
						'ID'               => $menu_cnt + 2147480000,
						'object_id'        => apply_filters( 'wpml_object_id', tinv_get_option( 'page', 'wishlist' ), 'page', true ),
						'db_id'            => $menu_cnt + 2147480000,
						'title'            => $menu_title,
						'url'              => esc_url( tinv_url_wishlist_default() ),
						'menu_order'       => $menu_order,
						'menu_item_parent' => 0,
						'type'             => 'post',
						'post_parent'      => 0,
						'filter'           => 'raw',
						'target'           => '',
						'attr_title'       => '',
						'object'           => get_post_type( get_post( apply_filters( 'wpml_object_id', tinv_get_option( 'page', 'wishlist' ), 'page', true ) ) ),
						'classes'          => array(),
						'description'      => '',
						'xfn'              => '',
						'status'           => '',
					);

					foreach ( array_keys( $items ) as $key ) {

						if ( $items[ $key ]->menu_order > ( $menu_order - 1 ) ) {
							$items[ $key ]->menu_order = $items[ $key ]->menu_order + 1;
						}
					}

					if ( $menu_order < $menu_cnt ) {
						array_splice( $items, $menu_order - 1, 0, array( $wishlist_item ) );
					} else {
						$items[] = $wishlist_item;
					}
				}
			}
		}

		return $items;
	}

	/**
	 * Outputs the HTML.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	function htmloutput( $atts ) {
		$data = array(
			'icon'         => tinv_get_option( 'topline', 'icon' ),
			'icon_class'   => ( $atts['show_icon'] && tinv_get_option( 'topline', 'icon' ) ) ? 'top_wishlist-' . tinv_get_option( 'topline', 'icon' ) : '',
			'icon_style'   => ( $atts['show_icon'] && tinv_get_option( 'topline', 'icon' ) ) ? esc_attr( 'top_wishlist-' . tinv_get_option( 'topline', 'icon_style' ) ) : '',
			'icon_upload'  => tinv_get_option( 'topline', 'icon_upload' ),
			'text'         => $atts['show_text'] ? $atts['text'] : '',
			'counter'      => $atts['show_counter'],
			'show_counter' => $atts['show_counter'],
		);
		tinv_wishlist_template( 'ti-wishlist-product-counter.php', $data );
	}


	function get_counter() {
		return $this->counter ? $this->counter : $this->counter();
	}

	function get_user_wishlists() {
		return $this->user_wishlists ? $this->user_wishlists : $this->user_wishlists();
	}

	function get_guest_wishlist() {
		return $this->guest_wishlist ? $this->guest_wishlist : $this->guest_wishlist();
	}

	function guest_wishlist() {
		$wl                   = new TInvWL_Wishlist();
		$wishlist             = $wl->get_by_sharekey_default();
		$this->guest_wishlist = $wishlist;

		return $wishlist;
	}

	function user_wishlists() {
		$wl = new TInvWL_Wishlist();

		return $wl->add_user_default();
	}

	/**
	 * Gets the count of the product in all wishlists.
	 *
	 * @return int
	 */
	public function counter() {
		global $wpdb;
		$count = 0;
		if ( is_user_logged_in() ) {
			$wishlist = $this->get_user_wishlists();
			$wlp      = new TInvWL_Product();
			if ( $wishlist ) {
				$counts = $wlp->get( array(
					'external'    => false,
					'wishlist_id' => $wishlist['ID'],
					'sql'         => 'SELECT COUNT(`quantity`) AS `quantity` FROM {table} t1 INNER JOIN ' . $wpdb->prefix . 'posts t2 on t1.product_id = t2.ID AND t2.post_status IN ("publish","private") WHERE {where} ',
				) );
				$counts = array_shift( $counts );
				$count  = absint( $counts['quantity'] );
			}
		} else {
			$wishlist = $this->get_guest_wishlist();
			if ( ! empty( $wishlist ) ) {
				$wishlist = array_shift( $wishlist );
				$wlp      = new TInvWL_Product( $wishlist );
				$counts   = $wlp->get_wishlist( array(
					'external' => false,
					'sql'      => sprintf( 'SELECT %s(`quantity`) AS `quantity` FROM {table}  t1 INNER JOIN ' . $wpdb->prefix . 'posts t2 on t1.product_id = t2.ID AND t2.post_status IN ("publish","private") WHERE {where}', 'COUNT' ),
				) );
				$counts   = array_shift( $counts );
				$count    = absint( $counts['quantity'] );
			}
		}

		return $count ?: ( tinv_get_option( 'topline', 'hide_zero_counter' ) ? false : 0 );
	}

	/**
	 * Processes the shortcode.
	 *
	 * @param array $atts Array parameter from shortcode.
	 *
	 * @return string
	 */
	function shortcode( $atts = array() ) {
		$default = array(
			'show_icon'    => (bool) tinv_get_option( 'topline', 'icon' ),
			'show_text'    => tinv_get_option( 'topline', 'show_text' ),
			'text'         => apply_filters( 'tinvwl_wishlist_products_counter_text', tinv_get_option( 'topline', 'text' ) ),
			'show_counter' => tinv_get_option( 'topline', 'show_counter' ),
		);
		$atts    = filter_var_array( shortcode_atts( $default, $atts ), array(
			'show_icon'    => FILTER_VALIDATE_BOOLEAN,
			'show_text'    => FILTER_VALIDATE_BOOLEAN,
			'show_counter' => FILTER_VALIDATE_BOOLEAN,
			'text'         => FILTER_DEFAULT,
		) );
		ob_start();
		$this->htmloutput( $atts );

		return ob_get_clean();
	}
}
