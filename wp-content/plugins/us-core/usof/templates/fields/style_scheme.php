<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Color Scheme
 *
 * Drop-down selector field.
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 *
 * @var   $value string Current value
 */

// Could already be defined in parent function
if ( ! isset( $color_schemes ) ) {
	$color_schemes = us_config( 'color-schemes' );
}
if ( ! isset( $custom_color_schemes ) ) {
	$custom_color_schemes = get_option( 'usof_style_schemes_' . US_THEMENAME );

	// Reverse Custom schemes order to make last added item first
	if ( is_array( $custom_color_schemes ) ) {
		$custom_color_schemes = array_reverse( $custom_color_schemes, TRUE );
	} else {
		$custom_color_schemes = array();
	}
}

// Window title and close control
$output = '<div class="us-bld-window-title">' . __( 'Color Schemes', 'us' ) . '</div>';
$output .= '<div class="us-bld-window-closer" title="' . us_translate( 'Close' ) . '"></div>';

$output .= '<div class="usof-schemes">';

// Save as new controls
$output .= '<div class="usof-schemes-controls">';
$output .= '<div class="usof-schemes-text">' . __( 'Save current colors as', 'us' ) . '</div>';
$output .= '<input type="text" id="scheme_name" value="" placeholder="' . __( 'Color Scheme Name', 'us' ) . '"/>';
$output .= '<button id="save_new_scheme" class="usof-button" disabled type="button">';
$output .= '<span>' . _x( 'Save as new', 'color scheme', 'us' ) . '</span>';
$output .= '<span class="usof-preloader"></span>';
$output .= '</button>';
$output .= '</div>';

// Schemes list
$output .= '<ul class="usof-schemes-list">';

// Custom schemes
foreach ( $custom_color_schemes as $key => &$scheme ) {
	$output .= '<li class="usof-schemes-item type_custom" data-id="' . $key . '">';
	$output .= usof_color_scheme_preview( $scheme );
	// Overwrite btn
	$output .= '<div class="usof-schemes-item-save" title="' . us_translate( 'Save' ) . '"></div>';
	// Delete btn
	$output .= '<div class="usof-schemes-item-delete" title="' . us_translate( 'Delete' ) . '"></div>';
	$output .= '</li>';
}
// Predefined schemes
foreach ( $color_schemes as $key => &$scheme ) {
	$output .= '<li class="usof-schemes-item" data-id="' . $key . '">';
	$output .= usof_color_scheme_preview( $scheme );
	$output .= '</li>';
}

$output .= '</ul>';

$output .= '</div>';

$first_color_scheme = array_shift( $color_schemes );
$color_scheme_colors = array_keys( $first_color_scheme['values'] );
$output .= '<div class="usof-form-row-control-colors-json hidden"' . us_pass_data_to_js( $color_scheme_colors ) . '></div>';

$i18n = array(
	'delete_confirm' => __( 'Are you sure want to delete the element?', 'us' ),
);
$output .= '<div class="usof-form-row-control-i18n hidden"' . us_pass_data_to_js( $i18n ) . '></div>';

echo $output;
