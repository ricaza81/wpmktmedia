<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Gravity Forms support
 *
 * @link http://www.gravityforms.com/
 */

if ( ! class_exists( 'GFForms' ) ) {
	return;
}

// Add theme styling
if ( defined( 'US_DEV' ) OR ! us_get_option( 'optimize_assets', 0 ) ) {
	add_action( 'wp_enqueue_scripts', 'us_gforms_add_styles', 14 );
}
function us_gforms_add_styles( $styles ) {
	global $us_template_directory_uri;
	$min_ext = defined( 'US_DEV' ) ? '' : '.min';
	wp_enqueue_style( 'us-gravityforms', $us_template_directory_uri . '/common/css/plugins/gravityforms' . $min_ext . '.css', array(), US_THEMEVERSION, 'all' );
}

// Remove plugin's datepicker CSS
add_action( 'wp_enqueue_scripts', 'us_gforms_remove_styles', 15 );
function us_gforms_remove_styles() {
	wp_dequeue_style( 'gforms_datepicker_css' );
	wp_deregister_style( 'gforms_datepicker_css' );
}
