<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Check Table
 *
 * Multiple selector as table
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['options'] array List of key => title pairs
 *
 * @var   $id    string Field ID
 * @var   $name  string Field name
 * @var   $field array Field options
 *
 * @var   $value array List of checked keys
 */

if ( ! is_array( $value ) ) {
	$value = array();
}

if ( $context === 'metabox' ) {
	$name .= '[]';
}

$output = '';

// Auto Optimize feature
if ( ! empty( $field['show_auto_optimize_button'] ) ) {

	// Data to be exported to the AutoOptimizeAssets class
	$auto_optimize_data = array(
		'_nonce' => wp_create_nonce( 'us_ajax_auto_optimize_assets' ),
	);

	// Output button
	$output .= '<div class="usof-button type_auto_optimize" ' . us_pass_data_to_js( $auto_optimize_data ) . '>';
	$output .= '<span class="usof-button-text">' . __( 'Auto Optimize', 'us' ) . '</span>';
	$output .= '<span class="usof-preloader"></span>';
	$output .= '</div>';
	$output .= '<div class="usof-form-row-desc">';
	$output .= '<div class="usof-form-row-desc-icon"></div>';
	$output .= '<div class="usof-form-row-desc-text">';
	$output .= __( 'The checkboxes will be checked / unchecked depending on the components and settings used on the website.', 'us' );
	$output .= '</div></div>';
	$output .= '<div class="usof-message type_auto_optimize hidden"></div>';
}

foreach ( $field['options'] as $key => &$option ) {
	$option['key'] = $key;
}
unset( $option );

// Sort parameters by the `title` field before output
usort( $field['options'], function( $a, $b ) {
	return strcmp( $a['title'], $b['title'] );
} );

// Output Table
$output .= '<ul class="usof-checkbox-list">';
$i = 1;
foreach ( $field['options'] as $option ) {
	if ( isset( $option['apply_if'] ) AND ! $option['apply_if'] ) {
		continue;
	}
	$output .= '<li class="usof-checkbox for_' . $option['key'] . '">';
	$output .= '<label>';

	// Show helper label in the first checkbox only
	if ( $i === 1 ) {
		$output .= '<span>' . strip_tags( __( 'All sizes in kilobytes', 'us' ) ) . '</span>';
	}

	$output .= '<input type="checkbox" name="' . esc_attr( $name ) . '" value="' . esc_attr( $option['key'] ) . '"';
	if ( ! isset( $value[ $option['key'] ] ) OR $value[ $option['key'] ] == 1 ) {
		$output .= ' checked';
	}
	$output .= '>';
	$output .= '<span class="usof-checkbox-text">' . $option['title'] . '</span>';
	$output .= '<span class="usof-checkbox-size for_js">';
	if ( isset( $option['js_size'] ) AND $option['js_size'] ) {
		$output .= $option['js_size'];
	} else {
		$output .= '-';
	}
	$output .= '</span>';
	$output .= '<span class="usof-checkbox-size for_css">';
	if ( isset( $option['css_size'] ) AND $option['css_size'] ) {
		$output .= $option['css_size'];
	} else {
		$output .= '-';
	}
	$output .= '</span>';
	$output .= '</label>';
	$output .= '</li>';

	$i ++;
}
$output .= '</ul>';

echo $output;
