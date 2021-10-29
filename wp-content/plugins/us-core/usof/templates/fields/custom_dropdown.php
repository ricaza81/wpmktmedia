<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Custom Dropdown
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['options'] array List of key => option_html pairs
 *
 * @var   $value array List of checked keys
 */

$output = '<div class="usof-custom-dropdown">';
$input_atts = array(
	'type' => 'hidden',
	'name' => $name,
	'value' => $value,
);
$output .= '<input' . us_implode_atts( $input_atts ) . '>';
$output .= '<div class="usof-custom-dropdown-list">';

foreach ( $field['options'] as $key => $option_html ) {
	$item_atts = array(
		'class' => 'usof-custom-dropdown-item',
		'data-value' => $key,
	);

	if ( $key == $value ) {
		$item_atts['class'] .= ' current';
	}

	$output .= '<div' . us_implode_atts( $item_atts ) . '>';
	$output .= $option_html;
	$output .= '</div>';
}

$output .= '</div>';
$output .= '</div>';

echo $output;
