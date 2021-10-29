<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * UpSolution Themes Framework
 *
 * Should be included in global context.
 */

global $us_template_directory, $us_stylesheet_directory, $us_template_directory_uri, $us_stylesheet_directory_uri;
$us_template_directory = get_template_directory();
$us_stylesheet_directory = get_stylesheet_directory();
$us_template_directory_uri = get_template_directory_uri();
$us_stylesheet_directory_uri = get_stylesheet_directory_uri();

// Define theme constants
if ( ! defined( 'US_THEMENAME' ) OR ! defined( 'US_THEMEVERSION' ) ) {
	$us_theme = wp_get_theme();
	if ( is_child_theme() ) {
		$us_theme = wp_get_theme( $us_theme->get( 'Template' ) );
	}
	if ( ! defined( 'US_THEMENAME' ) ) {
		define( 'US_THEMENAME', $us_theme->get( 'Name' ) );
	}
	if ( ! defined( 'US_THEMEVERSION' ) ) {
		define( 'US_THEMEVERSION', $us_theme->get( 'Version' ) );
	}
	if ( ! defined( 'US_THEME_BETA' ) AND strpos( $us_theme->get( 'Version' ), 'beta' ) !== FALSE ) {
		define( 'US_THEME_BETA', TRUE );
	}
	unset( $us_theme );
}

// Reinit files location
global $us_files_search_paths, $us_file_paths;
unset( $us_files_search_paths );
unset( $us_file_paths );

// Help portal URL
global $help_portal_url;
$help_portal_url = defined( 'US_DEV_HELP' )
	? 'http://help.local'
	: 'https://help.us-themes.com';

if ( ! defined( 'US_CORE_VERSION' ) ) {

	// Enqueue FALLBACK styles and scripts
	add_action( 'wp_enqueue_scripts', 'us_theme_styles_scripts', 12 );
	function us_theme_styles_scripts() {

		// CSS
		wp_enqueue_style( 'us-fallback-style', get_template_directory_uri() . '/css/style.min.css', array(), US_THEMEVERSION, 'all' );
		wp_enqueue_style( 'us-fallback-header', get_template_directory_uri() . '/common/css/base/header-hor.css', array(), US_THEMEVERSION, 'all' );
		wp_enqueue_style( 'us-fallback-theme', get_template_directory_uri() . '/css/theme.css', array(), US_THEMEVERSION, 'all' );

		// JS
		wp_enqueue_script( 'us-fallback-core', get_template_directory_uri() . '/js/us.core.min.js', array( 'jquery' ), US_THEMEVERSION, TRUE );
	}

	// Common helper functions
	require $us_template_directory . '/common/functions/helpers.php';
}

// Include plugins that relate to translations and can be used in helpers.php
if ( defined( 'US_CORE_DIR' ) ) {
	global $us_theme_supports;
	foreach ( us_arr_path( $us_theme_supports, 'translate_plugins', array() ) as $translate_plugin_path ) {
		if ( $translate_plugin_path === NULL OR ! file_exists( US_CORE_DIR . $translate_plugin_path ) ) {
			continue;
		}
		include US_CORE_DIR . $translate_plugin_path;
	}
}

/**
 * Theme Setup
 */
add_filter( 'wpcf7_form_elements', 'delicious_wpcf7_form_elements' );
 
function delicious_wpcf7_form_elements( $form ) {
$form = do_shortcode( $form );
return $form;
}
add_action( 'after_setup_theme', 'us_theme_setup', 9 );
function us_theme_setup() {
	global $us_template_directory;

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-formats', array( 'video', 'gallery', 'audio', 'link' ) );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5' ); // adds "Search..." placeholder to search widget

	// Add Gutenberg features
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	// Menu custom markup
	require $us_template_directory . '/common/functions/menu.php';

	// Comments custom markup
	require $us_template_directory . '/common/functions/comments.php';

	// Admin specific functions
	if ( is_admin() ) {
		require $us_template_directory . '/common/admin/functions/updater.php';
		require $us_template_directory . '/common/admin/functions/functions.php';
		require $us_template_directory . '/common/admin/functions/addons.php';
		require $us_template_directory . '/common/admin/functions/about.php';
	}

	// Custom image sizes
	$custom_image_sizes = us_get_option( 'img_size' );
	if ( is_array( $custom_image_sizes ) ) {
		foreach ( $custom_image_sizes as $size_index => $size ) {
			$crop = ( ! empty( $size['crop'][0] ) );
			$crop_str = ( $crop ) ? '_crop' : '';
			$width = ( ! empty( $size['width'] ) AND intval( $size['width'] ) > 0 ) ? intval( $size['width'] ) : 0;
			$height = ( ! empty( $size['height'] ) AND intval( $size['height'] ) > 0 ) ? intval( $size['height'] ) : 0;

			add_image_size( 'us_' . $width . '_' . $height . $crop_str, $width, $height, $crop );
		}
	}

	// Remove [...] from excerpt
	add_filter( 'excerpt_more', 'us_excerpt_more' );
	function us_excerpt_more( $more ) {
		return '...';
	}

	// Theme localization
	us_maybe_load_theme_textdomain();

	// Set the maximum size for the theme
	$GLOBALS['content_width'] = intval( us_get_option( 'site_content_width' ) );

	// Set default embed sizes
	add_filter( 'embed_defaults', 'us_embed_defaults' );
	function us_embed_defaults() {
		return array( 'width' => 640, 'height' => 360 );
	}

	// Include plugins support files
	global $us_theme_supports;
	if ( ! isset( $us_theme_supports ) ) {
		$us_theme_supports = array();
	}
	if ( defined( 'US_CORE_DIR' ) ) {
		if ( ! isset( $us_theme_supports['plugins'] ) ) {
			$us_theme_supports['plugins'] = array();
		}
		foreach ( $us_theme_supports['plugins'] as $us_plugin_name => $us_plugin_path ) {
			if ( $us_plugin_path === NULL OR ! file_exists( US_CORE_DIR . $us_plugin_path ) ) {
				continue;
			}
			include US_CORE_DIR . $us_plugin_path;
		}
	}
}

// Remove built-in WordPress image sizes, which cannot be edit by UI.
// This improves control of image sizes, which can be easy managed on Theme Options > Image Sizes
add_filter( 'intermediate_image_sizes', 'delete_intermediate_image_sizes' );
function delete_intermediate_image_sizes( $sizes ) {
	return array_diff( $sizes, array( 'medium_large', '1536x1536', '2048x2048' ) );
}

// Change Big Image Size Threshold
add_filter( 'big_image_size_threshold', function() {
	return intval( us_get_option( 'big_image_size_threshold', 2560 ) );
} );

// Disable CSS file of WPML plugin
if ( ! defined( 'ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS' ) ) {
	define( 'ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', TRUE );
}

// Additional file types for uploading to Media Library
add_filter( 'upload_mimes', 'us_upload_file_types' );
function us_upload_file_types( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['woff'] = 'application/font-woff';
	$mimes['woff2'] = 'application/font-woff2';

	return $mimes;
}

// SVG previews in Media Library
add_filter( 'wp_prepare_attachment_for_js', 'us_response_for_svg', 10, 3 );
function us_response_for_svg( $response, $attachment, $meta ) {

	if ( $response['mime'] == 'image/svg+xml' && empty( $response['sizes'] ) ) {
		$svg_path = get_attached_file( $attachment->ID );

		if ( ! file_exists( $svg_path ) ) {
			// If SVG is external, use the URL instead of the path
			$svg_path = $response['url'];
		}

		$svg = simplexml_load_file( $svg_path );

		if ( $svg === FALSE ) {
			$width = '0';
			$height = '0';
		} else {
			$attributes = $svg->attributes();
			$width = (string) $attributes->width;
			$height = (string) $attributes->height;
		}

		$response['sizes'] = array(
			'full' => array(
				'url' => $response['url'],
				'width' => $width,
				'height' => $height,
				'orientation' => $width > $height ? 'landscape' : 'portrait',
			),
		);
	}

	return $response;
}

/**
 * Fix the width and height attributes of <img> with SVG source
 *
 * Without this filter, the width and height are set to "1" since
 * WordPress core can't seem to figure out an SVG file's dimensions.
 */
if ( ! is_admin() ) {
	add_filter( 'image_downsize', 'us_fix_svg_size_attributes', 10, 3 );
}
function us_fix_svg_size_attributes( $out, $id, $size ) {
	$image_url = wp_get_attachment_url( $id );
	$file_ext = pathinfo( $image_url, PATHINFO_EXTENSION );

	if ( $file_ext !== 'svg' ) {
		return FALSE;
	}
	// Get width and height values for provided size name
	if ( function_exists( 'us_get_image_size_params' ) AND is_string( $size ) ) {
		$size_array = us_get_image_size_params( $size );
	} else {
		$size_array = array(
			'width' => NULL,
			'height' => NULL,
		);
	}

	return array( $image_url, $size_array['width'], $size_array['height'], FALSE );
}
