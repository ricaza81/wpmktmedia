<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Select
 *
 * Drop-down selector field.
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['options'] array List of value => title pairs
 *
 * @var   $value string Current value
 */

$output = '<div class="usof-select">';

$output .= '<select name="' . $name . '">';

$is_first_optgroup = TRUE;
foreach ( $field['options'] as $key => $option ) {
	if ( is_array( $option ) AND isset( $option['optgroup'] ) AND $option['optgroup'] ) {
		if ( ! $is_first_optgroup ) {
			$output .= '</optgroup>';
		}
		$output .= '<optgroup label="' . esc_attr( $option['title'] ) . '">';
		$is_first_optgroup = FALSE;

	} else {
		$output .= '<option value="' . esc_attr( $key ) . '"' . selected( $value, $key, FALSE ) . '>' . $option . '</option>';
	}
}
if ( ! $is_first_optgroup ) {
	$output .= '</optgroup>';
}

$output .= '</select>';
$output .= '</div>';

echo $output;
