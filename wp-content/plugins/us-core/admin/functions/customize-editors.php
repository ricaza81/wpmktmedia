<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Enables visual appearance of TinyMCE and Gutenberg editors, like on frontend
 */

// Add custom css file for TinyMCE
add_filter( 'mce_css', 'us_include_generated_styles_for_tinymce' );
function us_include_generated_styles_for_tinymce( $mce_css ) {
	$editor_css_file = us_get_asset_file( 'tinymce' );
	$mce_css_array = array();
	if ( ! empty( $mce_css ) ) {
		$mce_css_array = explode( ',', $mce_css );
	}

	if ( file_exists( $editor_css_file ) ) {
		$editor_file_url = us_get_asset_file( 'tinymce', TRUE );
		$mce_css_array[] = $editor_file_url;
	}

	if ( $fonts_file_url = us_enqueue_fonts( TRUE ) ) {
		$mce_css_array[] = $fonts_file_url;
	}

	if ( ! empty( $mce_css_array ) ) {
		$mce_css = implode( ',', $mce_css_array );
	}

	return $mce_css;
}

// Add Gogle Fonts css file for Gutenberg
add_action( 'enqueue_block_editor_assets', 'us_enqueue_fonts' );

// Add custom css file for Gutenberg
add_action( 'enqueue_block_editor_assets', 'us_include_generated_styles_for_gutenberg' );
function us_include_generated_styles_for_gutenberg() {
	$editor_css_file = us_get_asset_file( 'gutenberg' );

	if ( file_exists( $editor_css_file ) ) {
		$editor_file_url = us_get_asset_file( 'gutenberg', TRUE );
		wp_enqueue_style( 'us-gutenberg-editor-styles', $editor_file_url, FALSE, US_CORE_VERSION, 'all' );
	}
}

// Add Gutenberg color palette from Theme Options > Colors
$color_palette = array();
$predefined_colors = array(
	'color_content_primary',
	'color_content_secondary',
	'color_content_heading',
	'color_content_text',
	'color_content_faded',
	'color_content_border',
	'color_content_bg_alt',
	'color_content_bg',
);
foreach ( $predefined_colors as $color ) {
	array_push(
		$color_palette,
		array(
			'name' => us_config( 'theme-options.colors.fields.' . $color . '.text' ),
			'slug' => str_replace( 'color_', '', $color ),
			'color' => us_get_color( $color, /* Gradient */ FALSE, /* css_var */ FALSE ),
		)
	);
}
add_theme_support( 'editor-color-palette', $color_palette );
