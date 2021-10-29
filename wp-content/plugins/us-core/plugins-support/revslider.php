<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Revolution Slider Support
 *
 * @link http://codecanyon.net/item/slider-revolution-responsive-wordpress-plugin/2751380
 */

if ( ! class_exists( 'RevSliderFront' ) ) {
	return;
}

if ( function_exists( 'set_revslider_as_theme' ) ) {
	if ( ! defined( 'REV_SLIDER_AS_THEME' ) ) {
		define( 'REV_SLIDER_AS_THEME', TRUE );
	}
	set_revslider_as_theme();
}

// Actually the revslider's code above doesn't work as expected, so turning off the notifications manually
if ( get_option( 'revslider-valid-notice', 'true' ) != 'false' ) {
	update_option( 'revslider-valid-notice', 'false' );
}
if ( get_option( 'revslider-notices', array() ) != array() ) {
	update_option( 'revslider-notices', array() );
}

// Remove notices on "Plugins" admin page
global $pagenow;
if ( $pagenow == 'plugins.php' ) {
	remove_action( 'admin_notices', array( 'RevSliderAdmin', 'add_plugins_page_notices' ) );
}

// Move js for Admin Bar lower so it is not echoed before jquery core in footer
if ( ! function_exists( 'us_move_revslider_js_footer' ) ) {
	function us_move_revslider_js_footer() {
		remove_action( 'wp_footer', array( 'RevSliderFront', 'putAdminBarMenus' ) );
		add_action( 'wp_footer', array( 'RevSliderFront', 'putAdminBarMenus' ), 99 );
	}

	add_action( 'wp_enqueue_scripts', 'us_move_revslider_js_footer' );
}

// Remove Slider's FontAwesome library
if ( ! function_exists( 'us_remove_slider_fontawesome' ) ) {
	add_action( 'wp_enqueue_scripts', 'us_remove_slider_fontawesome' );
	function us_remove_slider_fontawesome() {
		remove_action( 'wp_footer', array( 'RevSliderFront', 'load_icon_fonts' ) );
	}
}

if ( ! function_exists( 'us_include_revslider_js_for_row_bg' ) ) {
	function us_include_revslider_js_for_row_bg() {
		$isPutIn = FALSE;
		if ( class_exists( 'UniteFunctionsRev' ) ) {
			// Object to access RevSlider functions
			$uniteFunctionsRev = new UniteFunctionsRev;

			if (
				method_exists( $uniteFunctionsRev, 'get_global_settings' )
				AND method_exists( $uniteFunctionsRev, 'get_val' )
				AND method_exists( $uniteFunctionsRev, 'check_add_to' )
			) {
				// Get all global settings RevSlider
				$arrValues = (array) $uniteFunctionsRev->get_global_settings();

				/**
				 * Check if RevSlider is enabled globally, then we do nothing
				 * @var string $arrValues ['include']
				 */
				if ( $uniteFunctionsRev->get_val( $arrValues, "include", 'false' ) === 'true' ) {
					return;
				}

				/**
				 * Getting a list of post IDs where RevSlider connects
				 * @var string $arrValues ['includeids']
				 */
				$strPutIn = $uniteFunctionsRev->get_val( $arrValues, "includeids", '' );
				// Check it has the current post element RevSlider
				$revSliderOutput = new RevSliderOutput;
				$isPutIn = $revSliderOutput->check_add_to( $strPutIn, TRUE );
			}
		}

		// Search shortcode in content
		if ( $isPutIn === FALSE ) {
			$post_content = '';
			$page_blocks_content = us_get_current_page_block_content();

			$is_slider_on_page_block = FALSE; // Default
			$post_id = get_the_ID();
			if ( is_singular() AND $post = get_post( $post_id ) ) {
				$post_content = $post->post_content;

				// Find slider usage in post page blocks
				$is_slider_on_page_block = us_find_element_in_post_page_blocks( $post_id, '[rev_slider' );
			}

			// Find slider usage in content template page blocks
			if ( ! $is_slider_on_page_block AND is_numeric( $content_template_id = us_get_page_area_id( 'content' ) ) ) {
				$is_slider_on_page_block = us_find_element_in_post_page_blocks( $content_template_id, '[rev_slider' );
			}

			$has_slider_post_content = ( ! empty( $post_content ) AND stripos( $post_content, 'us_bg_rev_slider=' ) !== FALSE );
			$has_slider_page_blocks_content = ( ! empty( $page_blocks_content )
				AND ( stripos( $page_blocks_content, 'us_bg_rev_slider=' ) !== FALSE
					OR stripos( $page_blocks_content, '[rev_slider' ) !== FALSE ) );

			$has_slider_special_page_content = FALSE; // default value
			// If library not included check extra conditions
			if ( ! $has_slider_post_content AND ! $has_slider_post_content ) {
				// If current page is special - get current page id and check is used rev slider in content
				if ( is_404() ) {
					$postID = us_get_option( 'page_404' );
				} elseif ( is_search() ) {
					$postID = us_get_option( 'search_page' );
				} elseif ( is_home() ) {
					$postID = us_get_option( 'posts_page' );
				} elseif ( get_post_type() == 'us_portfolio' AND us_get_option( 'portfolio_breadcrumbs_page' ) != '' ) {
					$postID = us_get_option( 'portfolio_breadcrumbs_page' );
				}

				if ( ! empty( $postID ) AND $post = get_post( $postID ) ) {
					$post_content = $post->post_content;
					$has_slider_special_page_content = ( ! empty( $post_content ) AND stripos( $post_content, '[rev_slider' ) !== FALSE );
				}
			}

			// If we managed to find rev_slider, then we will connect the libraries
			if (
				$is_slider_on_page_block
				OR $has_slider_post_content
				OR $has_slider_page_blocks_content
				OR $has_slider_special_page_content
			) {
				add_filter( 'revslider_include_libraries', '__return_true' );
			}
		}
	}

	add_action( 'wp_enqueue_scripts', 'us_include_revslider_js_for_row_bg', 5 );
}
