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
	$classes = isset( $field['classes'] ) ? $field['classes'] : '';
}

$out_params = array();

// Group params
if ( $groups = wp_list_pluck( $params, 'group' ) ) {
	foreach ( array_unique( array_values( $groups ) ) as $group ) {
		$group_id = str_replace( ' ' , '_', $group );
		$header = '<div class="usof-design-options-header" data-accordion-id="' . esc_attr( $group_id ) . '">';
		$header .= '<span class="usof-design-options-header-title">' . strip_tags( $group ) . '</span>';
		$header .= '<span class="usof-design-options-responsive ui-icon_devices"></span>';
		$header .= '<span class="usof-design-options-reset">' . strip_tags( __( 'Reset', 'us' ) ) . '</span>';
		$header .= '</div>';
		$out_params[ $group_id ]['name'] = $header;
	}
}

// Parameters to be added to inline css
foreach ( $params as $param_name => $param ) {

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

// Get responsive states
$states = us_get_responsive_states();

// HTML output structure
$output = '<div class="usof-design-options" ' . us_pass_data_to_js( array_keys( $states ) ) . '>';

// Structure for displaying buttons Copy/Paste and import the value
$output .= '<div class="usof-design-options-import">';

$output .= '<div class="usof-design-options-import-header">';
$output .= '<button class="usof-button" data-action="copy" type="button" disabled>' . strip_tags( us_translate( 'Copy' ) ) . '</button>';
$output .= '<button class="usof-button" data-action="paste" type="button">' . strip_tags( us_translate( 'Paste' ) ) . '</button>';
$output .= '</div>';

// Required field for working design options, all parameters values will be written into it
$input_atts = array(
	'class' => 'usof_design_value ' . $classes,
	'name' => $name,
);
$output .= '<textarea' . us_implode_atts( $input_atts ) . '>' . $value . '</textarea>';
$output .= '<div class="usof-design-options-import-novalid">' . strip_tags( us_translate( 'Invalid data provided.' ) ) . '</div>';

$output .= '<div class="usof-design-options-import-footer">';
$output .= '<button class="usof-button" data-action="cancel" type="button">' . strip_tags( us_translate( 'Close' ) ) . '</button>';
$output .= '<button class="usof-button button-primary" data-action="apply" type="button">' . strip_tags( us_translate( 'Apply' ) ) . '</button>';
$output .='</div>';

$output .= '</div>';

if ( ! empty( $out_params ) ) {
	foreach ( $out_params as $id => $param ) {
		if ( isset( $param['name'] ) ) {
			$output .= $param['name'];
			unset( $param['name'] );
		}
		$output .= '<div class="usof-design-options-content" data-accordion-content="' . esc_attr( $id ) . '">';

		// States
		$output .= '<div class="us-builder-states">';
		foreach ( $states as $state => $data ) {
			$state_atts = array(
				'class' => 'us-builder-state ui-icon_devices_' . $state,
				'data-responsive-state' => $state,
				'title' => strip_tags( $data['title'] ),
			);
			if ( $state === 'default' ) {
				$state_atts['class'] .= ' active';
			}
			$output .= '<div' . us_implode_atts( $state_atts ) . '></div>';
		}
		$output .= '</div>';

		// This block is duplicated on the frontend side to implement settings for different devices
		$output .= '<div class="usof-design-options-content-fields" data-responsive-state-content="default">';
		$output .= is_array( $param ) ? implode( '', $param ) : $param;
		$output .= '</div>';

		$output .= '</div>';
	}
}
$output .= '</div>';

echo $output;
