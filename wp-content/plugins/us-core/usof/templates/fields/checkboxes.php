<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Checkboxes
 *
 * Multiple selector
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

// Get current value
$value = ! is_array( $value )
	? array( $value )
	: $value;

// Separator for values in a string
$_separator = ',';

$output = '<ul class="usof-checkbox-list">';

// Add to the output of checkboxes
foreach ( us_arr_path( $field, 'options', array() ) as $key => $label ) {
	$checkbox_atts = array(
		// NOTE: Do not set the field name to disable links between the selection by the browser itself!
		// Exception: MetaBox fields
		'name' => ( $context === 'metabox' ) ? $name . '[]' : '',
		'type' => 'checkbox',
		'value' => $key,
	);
	if ( in_array( $key, $value ) ) {
		$checkbox_atts['checked'] = 'checked';
	}
	$output .= '<li class="usof-checkbox">';
	$output .= '<label title="' . strip_tags( $label ) . '">';
	$output .= '<input' . us_implode_atts( $checkbox_atts ) . '>';
	$output .= '<span class="usof-checkbox-text">' . $label . '</span>';
	$output .= '</label>';
	$output .= '</li>';
}

// Hidden field for correct data transfer via POST and uniqueness of buttons outside the form
$input_atts = array(
	'name' => $name, // Name to define in GET/POST/REQUEST
	'type' => 'hidden',
	'value' => implode( $_separator, $value ),
	'data-separator' => $_separator,
);
if ( $context === 'metabox' ) {
	$input_atts['data-metabox'] = '1';
}
$output .= '<li class="hidden"><input' . us_implode_atts( $input_atts ) . '></li>';

$output .= '</ul>';
echo $output;
