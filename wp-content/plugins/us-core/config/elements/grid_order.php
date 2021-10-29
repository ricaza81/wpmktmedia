<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for us_grid_order
 */

// Get design options
$design_options = us_config( 'elements_design_options' );

// Get sorting options for grid config
$orderby_values = us_grid_get_orderby_options();
unset( $orderby_values['post__in'] );

return array(
	'title' => __( 'Grid Order', 'us' ),
	'icon' => 'fas fa-sort-amount-down',
	'params' => array_merge( array(

		'orderby_items' => array(
			'title' => us_translate( 'Order' ),
			'type' => 'group',
			'params' => array(
				'value' => array(
					'type' => 'select',
					'options' => $orderby_values,
					'std' => 'date',
					'admin_label' => TRUE,
				),
				'custom_field' => array(
					'description' => __( 'Enter custom field name to order items by its value', 'us' ),
					'type' => 'text',
					'std' => '',
					'placeholder' => 'my_custom_field',
					'classes' => 'for_above',
					'admin_label' => TRUE,
					'show_if' => array( 'value', '=', 'custom' ),
				),
				'custom_field_numeric' => array(
					'type' => 'switch',
					'switch_text' => __( 'Order by numeric values', 'us' ),
					'std' => FALSE,
					'classes' => 'for_above',
					'show_if' => array( 'value', '=', 'custom' ),
				),
				'invert' => array(
					'type' => 'switch',
					'switch_text' => __( 'Invert order', 'us' ),
					'std' => FALSE,
					'classes' => 'for_above',
				),
				'label' => array(
					'title' => us_translate( 'Title' ),
					'description' => __( 'Leave blank to use the default.', 'us' ),
					'type' => 'text',
					'std' => '',
					'admin_label' => TRUE,
				),
			),
		),

		// Appearance
		'first_label' => array(
			'title' => __( 'First Value Title', 'us' ),
			'type' => 'text',
			'std' => us_translate( 'Default' ),
			'group' => us_translate( 'Appearance' ),
		),
		'text_before' => array(
			'title' => __( 'Text before dropdown', 'us' ),
			'type' => 'text',
			'std' => '',
			'admin_label' => TRUE,
			'group' => us_translate( 'Appearance' ),
		),
		'width_full' => array(
			'switch_text' => __( 'Stretch to the full width', 'us' ),
			'type' => 'switch',
			'std' => FALSE,
			'group' => us_translate( 'Appearance' ),
		),

	), $design_options ),
);
