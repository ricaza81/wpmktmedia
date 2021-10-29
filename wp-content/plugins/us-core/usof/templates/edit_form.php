<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a single element's editing form
 *
 * @var $type    string Element type
 * @var $params  array  List of config-based params
 * @var $values  array  List of param_name => value
 * @var $context string Context param states which builder is it
 */

// Validating and sanitizing input
$values = ( isset( $values ) AND is_array( $values ) ) ? $values : array();
$context = isset( $context ) ? $context : 'header';

// Validating, sanitizing and grouping params
$groups = array();
foreach ( $params as $param_name => &$param ) {
	if ( isset( $param['context'] ) AND ! in_array( $context, $param['context'] ) ) {
		continue;
	}
	$param['type'] = isset( $param['type'] ) ? $param['type'] : 'textfield';
	if ( $param['type'] == 'image' ) {
		$param['type'] = 'images';
		$param['multiple'] = FALSE;
	}
	if ( $param['type'] == 'html' AND $param_name != 'content' ) {
		// For VC-compatibility we may have only one wysiwyg field and it should be called content
		$param['type'] = 'textarea';
	}
	$param['classes'] = isset( $param['classes'] ) ? $param['classes'] : '';
	$param['std'] = isset( $param['std'] ) ? $param['std'] : '';
	// Check if context specific standard value is set
	$param['std'] = isset( $param[ $context . '_std' ] ) ? $param[ $context . '_std' ] : $param['std'];
	// Filling missing values with standard ones
	if ( ! isset( $values[ $param_name ] ) ) {
		$values[ $param_name ] = $param['std'];
	}
	$group = isset( $param['group'] ) ? $param['group'] : us_translate( 'General' );
	if ( ! isset( $groups[ $group ] ) ) {
		$groups[ $group ] = array();
	}
	$groups[ $group ][ $param_name ] = &$param;
}

$output = '<div class="usof-form for_' . $type . '">';
if ( count( $groups ) > 1 ) {
	$group_index = 0;
	$output .= '<div class="usof-tabs">';
	$output .= '<div class="usof-tabs-list">';
	foreach ( $groups as $group => &$group_params ) {
		$output .= '<div class="usof-tabs-item' . ( $group_index ? '' : ' active' ) . '">' . $group . '</div>';
		$group_index ++;
	}
	$output .= '</div>';
	$output .= '<div class="usof-tabs-sections">';
}

$group_index = 0;
foreach ( $groups as &$group_params ) {
	if ( count( $groups ) > 1 ) {
		$output .= '<div class="usof-tabs-section" style="display: ' . ( $group_index ? 'none' : 'flex' ) . '">';
	}
	$attributes_with_prefixes = array(
		'title',
		'description',
		'std',
		'cols',
		'classes',
		'show_if',
		'states',
		'with_position',
	);
	foreach ( $group_params as $param_name => &$field ) {
		foreach ( $attributes_with_prefixes as $attribute ) {
			if ( ! empty( $field[ $context . '_' . $attribute ] ) ) {
				$field[ $attribute ] = $field[ $context . '_' . $attribute ];
			}
		}
		$output .= us_get_template(
			'usof/templates/field', array(
				'name' => $param_name,
				'id' => 'hb_elm_' . $type . '_' . $param_name,
				'field' => $field,
				'values' => $values,
			)
		);
	}
	if ( count( $groups ) > 1 ) {
		$output .= '</div>'; // .usof-tabs-section
	}
	$group_index ++;
}

if ( count( $groups ) > 1 ) {
	$output .= '</div>'; // .usof-tabs-sections
	$output .= '</div>'; // .usof-tabs
}
$output .= '</div>';

echo $output;
