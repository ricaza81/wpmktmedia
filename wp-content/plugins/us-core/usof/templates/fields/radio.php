<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Radio
 *
 * Radio buttons selector
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['options'] array List of key => title pairs
 *
 * @var   $value array List of checked keys
 */

$output = '';

// Add to the output of radio buttons
foreach ( us_arr_path( $field, 'options', array() ) as $key => $label ) {
	$radio_atts = us_implode_atts( array(
		'name' => '', // NOTE: Do not set the field name to disable links between the selection by the browser itself!
		'type' => 'radio',
		'value' => $key,
	) );
	$output .= '<label title="' . esc_attr( $label ) . '">';
	$output .= '<input' . $radio_atts . checked( $value, $key, /* Default */FALSE ) . '>';

	// Output icons instead of labels if set
	$output .= '<span class="usof-radio-value">';
	$output .= ! empty( $field['labels_as_icons'] )
		? '<i class="' . esc_attr( str_replace( '*', $key, $field['labels_as_icons'] ) ) . '"></i>'
		: $label;
	$output .= '</span>';

	$output .= '</label>';
}

// Hidden field for correct data transfer via POST and uniqueness of buttons outside the form
$input_atts = array(
	'name' => $name, // Name to define in GET/POST/REQUEST
	'type' => 'hidden',
	'value' => $value,
);
$output .= '<input' . us_implode_atts( $input_atts ) . '>';

echo '<div class="usof-radio">' . $output . '</div>';
