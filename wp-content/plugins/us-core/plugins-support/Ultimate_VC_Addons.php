<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ultimate Addons for WPBakery Page Builder support
 *
 * @link http://codecanyon.net/item/ultimate-addons-for-visual-composer/6892199?ref=UpSolution
 */

if ( ! class_exists( 'Ultimate_VC_Addons' ) ) {
	return;
}

defined( 'ULTIMATE_USE_BUILTIN' ) OR define( 'ULTIMATE_USE_BUILTIN', TRUE );
defined( 'ULTIMATE_NO_EDIT_PAGE_NOTICE' ) OR define( 'ULTIMATE_NO_EDIT_PAGE_NOTICE', TRUE );
defined( 'ULTIMATE_NO_PLUGIN_PAGE_NOTICE' ) OR define( 'ULTIMATE_NO_PLUGIN_PAGE_NOTICE', TRUE );

// Removing potentially dangerous functions
if ( ! function_exists( 'bsf_grant_developer_access' ) ) {
	function bsf_grant_developer_access() {
	}
}
if ( ! function_exists( 'bsf_allow_developer_access' ) ) {
	function bsf_allow_developer_access() {
	}
}
if ( ! function_exists( 'bsf_process_developer_login' ) ) {
	function bsf_process_developer_login() {
	}
}
if ( ! function_exists( 'bsf_notices' ) ) {
	function bsf_notices() {
	}
}
add_action( 'init', 'us_sanitize_ultimate_addons', 20 );
function us_sanitize_ultimate_addons() {
	remove_action( 'admin_init', 'bsf_update_all_product_version', 1000 );
	remove_action( 'admin_notices', 'bsf_notices', 1000 );
	remove_action( 'network_admin_notices', 'bsf_notices', 1000 );
	remove_action( 'admin_footer', 'bsf_update_counter', 999 );
	remove_action( 'wp_ajax_bsf_update_client_license', 'bsf_server_update_client_license' );
	remove_action( 'wp_ajax_nopriv_bsf_update_client_license', 'bsf_server_update_client_license' );
}

// Disabling after-activation redirect to Ultimate Addons Dashboard
if ( get_option( 'ultimate_vc_addons_redirect' ) == TRUE ) {
	update_option( 'ultimate_vc_addons_redirect', FALSE );
}

add_action( 'admin_init', 'us_ultimate_addons_for_vc_integration' );
function us_ultimate_addons_for_vc_integration() {
	if ( get_option( 'ultimate_updater' ) != 'disabled' ) {
		update_option( 'ultimate_updater', 'disabled' );
	}
}

add_action( 'core_upgrade_preamble', 'us_ultimate_addons_core_upgrade_preamble' );
function us_ultimate_addons_core_upgrade_preamble() {
	remove_action( 'core_upgrade_preamble', 'list_bsf_products_updates', 999 );
}

add_filter( 'pre_set_site_transient_update_plugins', 'us_ultimate_addons_update_plugins_transient', 99 );
function us_ultimate_addons_update_plugins_transient( $_transient_data ) {
	if ( isset( $_transient_data->response['Ultimate_VC_Addons/Ultimate_VC_Addons.php'] ) AND empty( $_transient_data->response['Ultimate_VC_Addons/Ultimate_VC_Addons.php']->package ) ) {
		unset( $_transient_data->response['Ultimate_VC_Addons/Ultimate_VC_Addons.php'] );
	}

	return $_transient_data;
}

if ( ! function_exists( 'us_ultimate_front_scripts_post_content' ) ) {
	/**
	 * Combining all shortcodes from Page Blocks so Ultimate VC can detect them
	 *
	 * @param string $content The content
	 * @return string
	 */
	function us_ultimate_front_scripts_post_content( $content ) {
		global $post;
		if (
			$post instanceof WP_Post
			AND function_exists( 'us_get_recursive_parse_page_block' )
		) {
			// Add content from content template, this will get all the nesting for Ultimate VC
			foreach ( array( 'content', 'titlebar', 'sidebar', 'footer' ) as $area ) {
				if (
					$area_id = us_get_page_area_id( $area )
					AND $_post = get_post( (int)$area_id )
					AND ! empty( $_post->post_content )
				) {
					$content .= $_post->post_content;
				}
			}
			$content .= $post->post_content;
			us_get_recursive_parse_page_block( $post, function( $post ) use ( &$content ) {
				if ( $post instanceof WP_Post ) {
					$content .= $post->post_content;
				}
			} );
		}
		return $content . us_get_current_page_block_content();
	}
	add_filter( 'ultimate_front_scripts_post_content', 'us_ultimate_front_scripts_post_content' );
}

add_action( 'wp_enqueue_scripts', 'us_ult_addons_404_search_enqueue_scripts', 1 );
function us_ult_addons_404_search_enqueue_scripts() {
	$us_ult_addons_check_element_on_page = FALSE;
	if ( is_404() OR is_search() OR is_home() ) {
		$content = '';
		if ( is_404() AND $page_404 = get_post( us_get_option( 'page_404' ) ) ) {
			$content = $page_404->post_content;
		}
		if ( is_search() AND $search_page = get_post( us_get_option( 'search_page' ) ) ) {
			$content = $search_page->post_content;
		}
		if ( is_home() AND $posts_page = get_post( us_get_option( 'posts_page' ) ) ) {
			$content = $posts_page->post_content;
		}
		$content = us_ultimate_front_scripts_post_content( $content );

		$us_ult_addons_check_element_on_page = us_ult_addons_check_element_on_page( $content );

	}

	if ( is_singular() AND ! $us_ult_addons_check_element_on_page ) {
		// If any pageblock contains ult elements - enqueue styles
		if ( function_exists( 'us_get_recursive_parse_page_block' ) ) {
			us_get_recursive_parse_page_block( get_post( get_the_id() ), function( $post ) use ( &$us_ult_addons_check_element_on_page ) {
				if ( $us_ult_addons_check_element_on_page ) {
					return;
				}
				if ( $post instanceof WP_Post ) {
					$us_ult_addons_check_element_on_page = us_ult_addons_check_element_on_page( $post->post_content );
				}
			} );
		}
	}

	if ( $us_ult_addons_check_element_on_page ) {
		add_filter( 'option_bsf_options', 'us_ult_addons_force_global_scripts_filter' );
		add_filter( 'default_option_bsf_options', 'us_ult_addons_force_global_scripts_filter' );
	}

}

function us_ult_addons_force_global_scripts_filter( $bsf_options ) {
	if ( ! is_array( $bsf_options ) ) {
		$bsf_options = array();
	}
	$bsf_options['ultimate_global_scripts'] = 'enable';

	return $bsf_options;
}

function us_ult_addons_check_element_on_page( $post_content ) {
	// check for background
	$found_ultimate_backgrounds = FALSE;
	if ( stripos( $post_content, 'bg_type=' ) ) {
		preg_match( '/bg_type="(.*?)"/', $post_content, $output );
		if (
			$output[1] === 'bg_color'
			OR $output[1] === 'grad'
			OR $output[1] === 'image'
			OR $output[1] === 'u_iframe'
			OR $output[1] === 'video'
		) {
			$found_ultimate_backgrounds = TRUE;
		}
	}
	if (
		stripos( $post_content, '[ultimate_spacer' )
		OR stripos( $post_content, '[ult_buttons' )
		OR stripos( $post_content, '[ultimate_icon_list' )
		OR stripos( $post_content, '[just_icon' )
		OR stripos( $post_content, '[ult_animation_block' )
		OR stripos( $post_content, '[icon_counter' )
		OR stripos( $post_content, '[ultimate_google_map' )
		OR stripos( $post_content, '[icon_timeline' )
		OR stripos( $post_content, '[bsf-info-box' )
		OR stripos( $post_content, '[info_list' )
		OR stripos( $post_content, '[ultimate_info_table' )
		OR stripos( $post_content, '[interactive_banner_2' )
		OR stripos( $post_content, '[interactive_banner' )
		OR stripos( $post_content, '[ultimate_pricing' )
		OR stripos( $post_content, '[ultimate_icons' )
		OR stripos( $post_content, '[ultimate_heading' )
		OR stripos( $post_content, '[ultimate_carousel' )
		OR stripos( $post_content, '[ult_countdown' )
		OR stripos( $post_content, '[ultimate_info_banner' )
		OR stripos( $post_content, '[swatch_container' )
		OR stripos( $post_content, '[ult_ihover' )
		OR stripos( $post_content, '[ult_hotspot' )
		OR stripos( $post_content, '[ult_content_box' )
		OR stripos( $post_content, '[ultimate_ctation' )
		OR stripos( $post_content, '[stat_counter' )
		OR stripos( $post_content, '[ultimate_video_banner' )
		OR stripos( $post_content, '[ult_dualbutton' )
		OR stripos( $post_content, '[ult_createlink' )
		OR stripos( $post_content, '[ultimate_img_separator' )
		OR stripos( $post_content, '[ult_tab_element' )
		OR stripos( $post_content, '[ultimate_exp_section' )
		OR stripos( $post_content, '[info_circle' )
		OR stripos( $post_content, '[ultimate_modal' )
		OR stripos( $post_content, '[ult_sticky_section' )
		OR stripos( $post_content, '[ult_team' )
		OR stripos( $post_content, '[ultimate_fancytext' )
		OR stripos( $post_content, '[ult_range_slider' )
		OR $found_ultimate_backgrounds
	) {
		return TRUE;
	} else {
		return FALSE;
	}
}
