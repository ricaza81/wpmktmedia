<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Color
 *
 * Simple color picker
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['text'] string Field additional text
 * @param $field ['disable_dynamic_vars'] bool Disables list of variables from Theme Options > Colors
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @var   $value string Current value
 */

// Check the color value for gradient
if ( preg_match( '~^\#([\da-f])([\da-f])([\da-f])$~', $value, $matches ) ) {
	$value = '#' . $matches[1] . $matches[1] . $matches[2] . $matches[2] . $matches[3] . $matches[3];
}

$atts = array(
	'class' => 'usof-color',
);

if ( isset( $field['clear_pos'] ) ) {
	$atts['class'] .= ' clear_' . $field['clear_pos'];
}

// Disable gradient colorpicker
if ( ! isset( $field['with_gradient'] ) OR $field['with_gradient'] !== FALSE ) {
	$atts['class'] .= ' with-gradient';
}

// Enable dynamic variable support
if ( ! isset( $field['disable_dynamic_vars'] ) ) {
	$atts['class'] .= ' dynamic_colors';
	$atts['data-nonce'] = wp_create_nonce( 'us_ajax_color_dynamic_colors' );
	$atts['data-action'] = 'usof_dynamic_colors';
}

$input_atts = array(
	'autocomplete' => 'off',
	'class' => 'usof-color-value',
	'name' => $name,
	'type' => 'text',
	'value' => $value,
);

// Getting color based on dynamic variables and parameters
$background = us_get_color( $value, /* Gradient */ TRUE, /* CSS var */ FALSE );

// Add the color of the dynamic variable to the attribute
if ( strpos( $value, '_' ) === 0 ) {
	$atts['data-value'] = $background;
}

// Output color input setting
$output = '<div'. us_implode_atts( $atts ) .'>';
$output .= '<div class="usof-color-preview" style="background: ' . $background . '"></div>';
$output .= '<input'. us_implode_atts( $input_atts ) .' />';

// Output list of dynamic variables
if ( ! isset( $field['disable_dynamic_vars'] ) ) {
	$output .= '<div class="usof-color-arrow" title="' . __( 'Colors from the Color Scheme', 'us' ) . '"></div>';
	$output .= '<div class="usof-color-list"></div>';
}

// Output "Clear" button, if set
if ( isset( $field['clear_pos'] ) ) {
	$output .= '<div class="usof-color-clear" title="' . us_translate( 'Clear' ) . '"></div>';
}

$output .= '</div>';

// Global output of a group of colors from for a color picker
// NOTE: The current output should be carried out once since
// all settings will be available globally.
global $_is_output_dynamic_colors;
if ( empty( $_is_output_dynamic_colors ) ) {
	$output .= '<script>
		if ( window.$usof === undefined ) {
			window.$usof = {};
		}
		// Import dynamic colors
		window.$usof.dynamicColors = \''. json_encode( (array) usof_get_dynamic_colors(), JSON_HEX_APOS ) .'\';
	</script>';
	$_is_output_dynamic_colors = TRUE;
}

if ( ! empty( $field['text'] ) ) {
	$output .= '<div class="usof-color-text">' . $field['text'] . '</div>';
}

echo $output;
