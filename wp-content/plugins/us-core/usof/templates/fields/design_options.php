<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: design_options
 *
 * Design options.
 *
 * @var $name string Field name
 * @var $params array Fields options
 * @var $states array States list
 * @var $classes string Class for value field needed to support js_composer
 * @var $value array Current value
 */


$name = isset( $name ) ? $name : '';
$value = ( isset( $value ) AND is_string( $value ) ) ? $value : '';
if ( ! isset( $params ) ) {
	$params = isset( $field['params'] ) ? $field['params'] : array();
}
if ( ! isset( $classes ) ) {
	$classes = isset( $field[ 'classes' ] ) ? $field[ 'classes' ] : '';
}
if ( ! isset( $states ) ) {
	$states = isset( $field['states'] ) ? $field['states'] : array( 'tablets', 'mobiles' );
}

$states_names = array(
	'tablets' => __( 'Tablets', 'us' ),
	'mobiles' => __( 'Mobiles', 'us' ),
);

$out_params = array();

// Group params
if ( $groups = wp_list_pluck( $params, 'group' ) ) {
	foreach ( array_unique( array_values( $groups ) ) as $group ) {
		$group_id = str_replace( ' ' , '_', $group );
		$header = '<div class="usof-design-options-header" data-accordion-id="' . esc_attr( $group_id ) . '">';
		$header .= '<span class="usof-design-options-header-title">' . strip_tags( $group ) . '</span>';
		$header .= '<span class="usof-design-options-reset">' . strip_tags( __( 'Reset', 'us' ) ) . '</span>';
		$header .= '<span class="usof-design-options-responsive"></span>';
		$header .= '</div>';
		$out_params[ $group_id ]['name'] = $header;
	}
}

// Parameters to be added to inline css
foreach ( $params as $param_name => $param ) {

	// Force "width_full" class
	if ( ! isset( $param['classes'] ) ) {
		$param['classes'] = 'width_full';
	} else {
		$param['classes'] .= ' width_full';
	}

	$field = us_get_template(
		'usof/templates/field', array(
			'name' => $param_name,
			'id' => 'usof_design_' . $param_name,
			'field' => $param,
			'std' => '',
		)
	);
	$group_id = str_replace( ' ' , '_', $param['group'] );
	if ( ! empty( $group_id ) AND array_key_exists( $group_id, $out_params ) ) {
		$out_params[ $group_id ][] = $field;
	} else {
		$out_params[] = $field;
	}
}

// This is a hidden field in which all parameter values will be written.
$input_atts = array(
	'class' => 'usof_design_value ' . $classes,
	'name' => esc_attr( $name ),
	'type' => 'hidden',
	'value' => esc_attr( $value ),
);

// HTML output structure
$output = '<div class="usof-design-options" '. us_pass_data_to_js( $states ) .'>';
$output .= '<input ' . us_implode_atts( $input_atts ) . '>';
if ( ! empty( $out_params ) ) {
	foreach ( $out_params as $id => $param ) {
		if ( isset( $param['name'] ) ) {
			$output .= $param['name'];
			unset( $param['name'] );
		}
		$output .= '<div class="usof-design-options-content" data-accordion-content="' . esc_attr( $id ) . '">';

		// States
		$output .= '<div class="us-bld-states">';
		$output .= '<div class="us-bld-state active" data-device-type="default">' . us_translate( 'Default' ) . '</div>';
		foreach ( $states as $state_name ) {
			$output .= '<div class="us-bld-state" data-device-type="'. esc_attr( $state_name ) .'">';
			$output .= ( isset( $states_names[ $state_name ] ) ? $states_names[ $state_name ] : 'Unkown' );
			$output .= '</div>';
		}
		$output .= '</div>';

		$output .= '<div class="usof-design-options-content-fields" data-device-type-content="default">';
		$output .= is_array( $param )
			? implode( '', $param )
			: $param;
		$output .= '</div>';
		$output .= '</div>';
	}
}
$output .= '</div>';

echo $output;
