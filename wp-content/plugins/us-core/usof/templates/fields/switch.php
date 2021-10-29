<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Switch
 *
 * On-off switcher
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['options'] array Array of two key => title pairs
 * @param $field ['text'] array Additional text to show right near the switcher
 *
 * @var   $value string Current value
 */

$input_atts = array(
	'type' => 'checkbox',
	'name' => $name,
	'value' => (int) $value,
);
if ( ! empty( $field['disabled'] ) ) {
	$input_atts['disabled'] = '';
}
// For control in html output
if ( ! empty( $input_atts['value'] ) ) {
	$input_atts['checked'] = 'checked';
}

$output = '<div class="usof-switcher"><label>';
$output .= '<input' . us_implode_atts( $input_atts ) . checked( $input_atts['value'], 1, FALSE ) . '>';
$output .= '<span class="usof-switcher-box"><i></i></span>';
if ( ! empty( $field['switch_text'] ) ) {
	$output .= '<span class="usof-switcher-text">' . $field['switch_text'] . '</span>';
}
$output .= '</label></div>';

echo $output;
