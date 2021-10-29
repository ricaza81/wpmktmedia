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

// We will sort all the parameters by weight, this will allow us to display
// the parameters and groups in the correct order
if ( ! empty( $params ) AND is_array( $params ) ) {
	uasort( $params, function( $a, $b ) {
		$a_weight = isset( $a['weight'] ) ? (int) $a['weight'] : 0;
		$b_weight = isset( $b['weight'] ) ? (int) $b['weight'] : 0;
		return ( $b_weight - $a_weight );
	} );
}

// Validating, sanitizing and grouping params
$groups = $groups_indexes = array();
foreach ( $params as $param_name => &$param ) {
	if ( isset( $param['context'] ) AND ! in_array( $context, $param['context'] ) ) {
		continue;
	}

	$param['classes'] = isset( $param['classes'] ) ? $param['classes'] : '';
	$param['std'] = isset( $param['std'] ) ? $param['std'] : '';
	$param['type'] = isset( $param['type'] ) ? $param['type'] : 'text';

	// Check if context specific standard value is set
	$param['std'] = isset( $param[ $context . '_std' ] ) ? $param[ $context . '_std' ] : $param['std'];

	// Filling missing values with standard ones
	if ( ! isset( $values[ $param_name ] ) ) {
		$values[ $param_name ] = $param['std'];
	}

	$main_group = us_translate( 'General' );
	$group = isset( $param['group'] ) ? $param['group'] : $main_group;
	if ( ! isset( $groups[ $group ] ) ) {
		$groups_indexes[] = $group;
		$groups[ $group ] = array();
	}
	$groups[ $group ][ $param_name ] = &$param;
}
unset( $param );

$output = '<div class="usof-form for_' . $type . '">';
if ( count( $groups_indexes ) > 1 ) {
	$output .= '<div class="usof-tabs">';
	$output .= '<div class="usof-tabs-list">';
	foreach ( $groups_indexes as $index => $group ) {
		$output .= '<div class="usof-tabs-item' . ( $index ? '' : ' active' ) . '">' . $group . '</div>';
	}
	$output .= '</div>';
	$output .= '<div class="usof-tabs-sections">';
}

foreach ( $groups_indexes as $index => $group ) {
	if ( count( $groups_indexes ) > 1 ) {
		$output .= '<div class="usof-tabs-section" style="display: ' . ( $index ? 'none' : 'flex' ) . '">';
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

	$show_fields = array();
	$group_params = &$groups[ $group ];
	foreach ( $group_params as $param_name => &$field ) {
		foreach ( $attributes_with_prefixes as $attribute ) {
			if ( ! empty( $field[ $context . '_' . $attribute ] ) ) {
				$field[ $attribute ] = $field[ $context . '_' . $attribute ];
			}
		}

		// If the parent parameter is hidden, then hide all children
		if ( ! isset( $show_fields[ $param_name ] ) ) {
			$show_if = us_arr_path( $field, 'show_if' );
			$show_fields[ $param_name ] = ( ! $show_if OR usof_execute_show_if( $show_if, $values ) );

			if ( is_array( $show_if ) ) {
				// If we have one condition, then turn it into an array to simplify checking
				if (
					isset( $show_if[0] )
					AND is_string( $show_if[0] )
					AND count( $show_if ) === 3
				) {
					$show_if = array( $show_if );
				}

				$condition_names = array();
				foreach ( $show_if as $index => $condition ) {
					$condition_field_name = is_array( $condition )
						? us_arr_path( $condition, '0' )
						: $condition;

					if (
						$condition_field_name
						AND ! in_array( strtolower( $condition_field_name ), array( 'or', 'and' ) )
						AND us_arr_path( $show_fields, $condition_field_name ) === FALSE
					) {
						$show_fields[ $param_name ] = FALSE;

						// Show the field by default
					} else {
						$show_fields[ $param_name ] = TRUE;
					}
				}
			}
		}

		$output .= us_get_template(
			'usof/templates/field', array(
				'field' => $field,
				'show_field' => us_arr_path( $show_fields,  $param_name ),
				'id' => $context . '_' . $type . '_' . $param_name,
				'name' => $param_name,
				'values' => $values,
				'context' => $context,
			)
		);
	}
	unset( $group_params, $field, $show_fields );

	if ( count( $groups_indexes ) > 1 ) {
		$output .= '</div>'; // .usof-tabs-section
	}
}

if ( count( $groups ) > 1 ) {
	$output .= '</div>'; // .usof-tabs-sections
	$output .= '</div>'; // .usof-tabs
}
$output .= '</div>';

echo $output;
