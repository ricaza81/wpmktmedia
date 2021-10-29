<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * bbPress Support
 *
 * @link https://bbpress.org/
 */

if ( ! class_exists( 'bbPress' ) ) {
	return;
}

add_action( 'wp_enqueue_scripts', 'us_bbpress_enqueue_styles', 14 );
function us_bbpress_enqueue_styles( $styles ) {
	global $us_template_directory_uri;
	wp_dequeue_style( 'bbp-default' );
	
	$min_ext = defined( 'US_DEV' ) ? '' : '.min';
	if ( defined( 'US_DEV' ) OR ! us_get_option( 'optimize_assets', 0 ) ) {
		wp_enqueue_style( 'us-bbpress', $us_template_directory_uri . '/common/css/plugins/bbpress' . $min_ext . '.css', array(), US_THEMEVERSION, 'all' );
	}
}

// Remove Forum summaries
add_filter( 'bbp_get_single_forum_description', '__return_false', 10, 2 );
add_filter( 'bbp_get_single_topic_description', '__return_false', 10, 2 );
