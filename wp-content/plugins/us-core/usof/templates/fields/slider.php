<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Slider
 *
 * Slider-selector of the integer value within some range.
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['options'] array with "min", "max", "step" values
 * @param $field ['std'] string Default value
 *
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @var   $name  string Field name
 * @var   $value string Current value
 */

// Set fallback values when "options" param is not set in config
if ( ! isset( $field['options'] ) ) {
	$field['options'] = array(
		'px' => array(
			'min' => 0,
			'max' => 100,
			'step' => 1,
		),
	);
}

// Get the first unit name from config
$unit_keys = array_keys( $field['options'] );
$first_unit = isset( $unit_keys[0] ) ? $unit_keys[0] : '';

$output = '<div class="usof-slider';
if ( count( $field['options'] ) > 1 ) {
	$output .= ' with_units'; // add class when more then 1 units in config
}
$output .= '">';
$output .= '<div class="usof-slider-selector">';

// Prepare value for regex
$value = esc_attr( trim( $value ) );
$value = str_replace( ',', '.', $value );

// Cut the string to get a unit
$units_expression = implode( '|', array_map( 'preg_quote', $unit_keys ) );
preg_match( '#^(-?\d+)(\.)?(\d+)?(' . $units_expression . ')?$#i', $value, $matches );

if ( isset( $matches[4] ) ) {
	$current_unit = $matches[4];
	$valueOutput = $value;
} else {
	$current_unit = $first_unit;
	$valueOutput = $value . $first_unit;
}

$output .= '<input type="text" name="' . $name . '" value="' . $valueOutput . '" />';

// Define current unit params
$max = isset( $field['options'][ $current_unit ]['max'] ) ? $field['options'][ $current_unit ]['max'] : 100;
$min = isset( $field['options'][ $current_unit ]['min'] ) ? $field['options'][ $current_unit ]['min'] : 0;
$step = isset( $field['options'][ $current_unit ]['step'] ) ? $field['options'][ $current_unit ]['step'] : 1;

// Output units selection based on config
$output .= '<div class="usof-slider-selector-units"';
$output .= ' data-units_expression="' . $units_expression . '"';
$output .= ' data-unit="' . $current_unit . '"';
$output .= ' data-max="' . $max . '"';
$output .= ' data-min="' . $min . '"';
$output .= ' data-step="' . $step . '"';
$output .= '>';
foreach ( $field['options'] as $unit => $values ) {
	$data_atts = ' data-unit="' . $unit . '"';
	$data_atts .= ' data-max="' . ( isset( $values['max'] ) ? $values['max'] : 100 ) . '"';
	$data_atts .= ' data-min="' . ( isset( $values['min'] ) ? $values['min'] : 0 ) . '"';
	$data_atts .= ' data-step="' . ( isset( $values['step'] ) ? $values['step'] : 1 ) . '"';

	$output .= '<div class="usof-slider-selector-unit"' . $data_atts . '>';
	if ( empty( $unit ) ) {
		$output .= '<i>' . __( 'No units', 'us' ) . '</i>'; // case for empty unit, like in "line-height" option
	} else {
		$output .= $unit;
	}
	$output .= '</div>';
}
$output .= '</div></div>';

// Calculate slider range offset in percents based on current "min" and "max" values
$offset_direction = ( is_rtl() ) ? 'right' : 'left';
$float_value = floatval( $value );

if ( $max <= $min ) {
	$offset = 100;
} else {
	if ( $float_value < $min ) {
		$offset = 0;
	} elseif ( $float_value >= $max ) {
		$offset = 100;
	} else {
		$offset = ( min( $max, max( $min, $float_value ) ) - $min ) * 100 / ( $max - $min );
	}
}

$output .= '<div class="usof-slider-box"><div class="usof-slider-box-h">';
$output .= '<div class="usof-slider-range" style="' . $offset_direction . ':' . $offset . '%;">';
$output .= '<div class="usof-slider-runner" draggable="true"></div>';
$output .= '</div></div></div>';

$output .= '</div>';

echo $output;
