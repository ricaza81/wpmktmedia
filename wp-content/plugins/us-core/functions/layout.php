<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

class US_Layout {

	/**
	 * @var US_Layout
	 */
	protected static $instance;

	/**
	 * Singleton pattern: US_Layout::instance()->do_something()
	 *
	 * @return US_Layout
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * @var string Canvas type: wide / boxed
	 */
	public $canvas_type;

	/**
	 * @var string Default-state header orientation: 'hor' / 'ver'
	 */
	public $header_orientation;

	/**
	 * @var string Default-state header position: 'static' / 'fixed'
	 */
	public $header_pos;

	/**
	 * @var string Default-state header background: 'solid' / 'transparent'
	 */
	public $header_bg;

	/**
	 * @var string Default-state header show: 'always' / 'never'
	 */
	public $header_show;

	protected function __construct() {

		do_action( 'us_layout_before_init', $this );

		if ( WP_DEBUG AND ! ( isset( $GLOBALS['post'] ) OR is_404() OR is_search() OR is_archive() OR ( is_home() AND ! have_posts() ) ) ) {
			wp_die( 'US_Layout can be inited only after the current post is obtained' );
		}

		global $us_header_settings;
		$this->canvas_type = us_get_option( 'canvas_layout', 'wide' );
		$this->header_pos = us_get_header_option( 'sticky', 'default', FALSE ) ? 'fixed' : 'static';
		$this->header_initial_pos = 'top';
		$this->header_bg = us_get_header_option( 'transparent', 'default', FALSE ) ? 'transparent' : 'solid';
		$this->header_shadow = us_get_header_option( 'shadow', 'default', 'thin' );
		$this->header_show = ( ! empty( $us_header_settings['header_id'] ) ) ? 'always' : 'never';

		// Get orientation from Mobiles state for AMP templates
		if ( us_amp() ) {
			$this->header_orientation = us_get_header_option( 'orientation', 'mobiles', 'hor' );
		} else {
			$this->header_orientation = us_get_header_option( 'orientation', 'default', 'hor' );
		}

		$postID = NULL;
		if ( is_singular() ) {
			$postID = get_the_ID();
		}
		if ( is_404() ) {
			$postID = us_get_option( 'page_404' );
		}
		if ( is_search() AND ! is_post_type_archive( 'product' ) ) {
			$postID = us_get_option( 'search_page' );
		}
		if ( is_home() ) {
			$postID = us_get_option( 'posts_page' );
		}
		if ( is_singular( array_keys( us_get_public_post_types() ) ) OR ( ( is_404() OR is_search() OR is_home() ) AND $postID != NULL AND $postID != 'default' ) ) {

			if ( metadata_exists( 'post', $postID, 'us_header_id' ) AND usof_meta( 'us_header_id', $postID ) == '' ) {
				$this->header_show = 'never';
			} elseif ( usof_meta( 'us_header_sticky_pos', $postID ) != '' AND $this->header_orientation == 'hor' ) {
				$this->header_initial_pos = usof_meta( 'us_header_sticky_pos', $postID );
			}
		}

		// Remove header for popup iframes (available in Grid Overriding Link)
		global $us_iframe;
		if ( ! empty( $us_iframe ) ) {
			$this->header_show = 'never';
		}

		$this->post_id = $postID;

		if ( $this->header_orientation == 'ver' ) {
			$this->header_pos = 'fixed';
			$this->header_bg = 'solid';
		}

		// Reset orientation when the header is not shown
		if ( $this->header_show == 'never' ) {
			$this->header_orientation = 'none';
		}

		do_action( 'us_layout_after_init', $this );
	}

	/**
	 * Obtain theme-defined CSS classes for <body> element
	 *
	 * @return string
	 */
	public function body_classes() {
		$classes = defined( 'US_THEMENAME' ) ? US_THEMENAME . '_' . US_THEMEVERSION : '';
		$classes .= defined( 'US_CORE_VERSION' ) ? ' us-core_' . US_CORE_VERSION : '';
		$classes .= ' header_' . $this->header_orientation;
		$classes .= us_get_option( 'links_underline', 0 ) ? ' links_underline' : '';
		$classes .= us_get_option( 'rounded_corners', 1 ) ? '' : ' rounded_none';

		// Classes sensitive to AMP
		if ( ! us_amp() ) {
			$classes .= ' headerinpos_' . $this->header_initial_pos;
			$classes .= us_get_option( 'footer_reveal', 0 ) ? ' footer_reveal' : '';
			$classes .= ' state_default';
		} else {
			$classes .= ' state_mobiles';
		}

		// Add class for pages opened inside iframe
		global $us_iframe;
		if ( ! empty( $us_iframe ) ) {
			$classes .= ' us_iframe';
		}

		// Add class for pages opened inside US Builder
		if ( apply_filters( 'usb_is_preview_page', NULL ) ) {
			$classes .= ' usb_preview';
		}

		return $classes;
	}

	/**
	 * Obtain CSS classes for .l-canvas
	 *
	 * @return string
	 */
	public function canvas_classes() {

		$classes = 'type_' . $this->canvas_type;

		return $classes;
	}

	/**
	 * Obtain CSS classes for .l-header
	 *
	 * @return string
	 */
	public function header_classes() {

		$classes = 'pos_' . $this->header_pos;
		$classes .= ' shadow_' . $this->header_shadow;

		// Classes sensitive to AMP
		if ( ! us_amp() ) {
			$classes .= ' bg_' . $this->header_bg;
		} else {
			$classes .= ' bg_solid';
		}

		return $classes;
	}

}
