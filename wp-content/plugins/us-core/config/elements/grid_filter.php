<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

$_custom_fields = array();

// Add WooCommerce related fields
if ( class_exists( 'woocommerce' ) ) {
	$_custom_fields['cf|_price'] = us_translate( 'Price', 'woocommerce' );
}

// Add fields from "Advanced Custom Fields" plugin
if ( function_exists( 'acf_get_field_groups' ) AND $acf_groups = acf_get_field_groups() ) {
	foreach ( $acf_groups as $group ) {
		foreach ( (array) acf_get_fields( $group['ID'] ) as $field ) {

			// Only specific ACF types
			if ( in_array( $field['type'], array( 'number', 'range', 'select', 'checkbox', 'radio' ) ) ) {
				$_custom_fields[ 'cf|' . $field['name'] ] = $group['title'] . ': ' . $field['label'];
			}
		}
	}
}

return array(
	'title' => __( 'Grid Filter', 'us' ),
	'icon' => 'fas fa-filter',
	'params' => array_merge(
		array(

			// General
			'filter_items' => array(
				'title' => __( 'Filter by', 'us' ),
				'type' => 'group',
				'params' => array(
					'source' => array(
						'type' => 'us_grouped_select',
						'settings' => array(
							array(
								'label' => __( 'Taxonomies', 'us' ),
								'options' => us_get_taxonomies( FALSE, TRUE, '', 'tax|' ),
							),
							array(
								'label' => us_translate( 'Custom fields' ),
								'options' => $_custom_fields,
							),
						),
						'std' => 'category',
						'admin_label' => TRUE,
					),
					'ui_type' => array(
						'title' => us_translate( 'Type' ),
						'type' => 'select',
						'options' => array(
							'checkbox' => __( 'Checkboxes', 'us' ),
							'dropdown' => __( 'Dropdown', 'us' ),
							'radio' => __( 'Radio buttons', 'us' ),
							'range' => __( 'Number Range', 'us' ),
						),
						'std' => 'checkbox',
					),
					'show_all_value' => array(
						'switch_text' => __( 'Show "All" value', 'us' ),
						'type' => 'switch',
						'std' => '1',
						'show_if' => array( 'ui_type', '=', 'radio' ),
					),
					'show_amount' => array(
						'type' => 'switch',
						'switch_text' => __( 'Show amount of relevant posts', 'us' ),
						'std' => FALSE,
						'show_if' => array( 'ui_type', '!=', 'range' ),
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
			'layout' => array(
				'title' => __( 'Layout', 'us' ),
				'type' => 'select',
				'options' => array(
					'hor' => __( 'Horizontal', 'us' ),
					'ver' => __( 'Vertical', 'us' ),
				),
				'std' => 'hor',
				'admin_label' => TRUE,
				'group' => us_translate( 'Appearance' ),
			),
			'style' => array(
				'title' => us_translate( 'Style' ),
				'type' => 'select',
				'options' => array(
					'drop_default' => __( 'Dropdown', 'us' ) . ' - ' . us_translate( 'Default' ),
					'drop_trendy' => __( 'Dropdown', 'us' ) . ' - ' . __( 'Trendy', 'us' ),
					'switch_default' => __( 'Switch', 'us' ) . ' - ' . us_translate( 'Default' ),
					'switch_trendy' => __( 'Switch', 'us' ) . ' - ' . __( 'Trendy', 'us' ),
				),
				'std' => 'drop_default',
				'cols' => 2,
				'admin_label' => TRUE,
				'show_if' => array( 'layout', '=', 'hor' ),
				'group' => us_translate( 'Appearance' ),
			),
			'align' => array(
				'title' => us_translate( 'Alignment' ),
				'type' => 'select',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'center' => us_translate( 'Center' ),
					'right' => us_translate( 'Right' ),
					'justify' => us_translate( 'Justify' ),
				),
				'std' => 'left',
				'cols' => 2,
				'show_if' => array( 'layout', '=', 'hor' ),
				'group' => us_translate( 'Appearance' ),
			),
			'values_drop' => array(
				'title' => __( 'Show the list of values', 'us' ),
				'type' => 'select',
				'options' => array(
					'hover' => __( 'On hover', 'us' ),
					'click' => __( 'On click', 'us' ),
				),
				'std' => 'hover',
				'show_if' => array( 'style', '=', array( 'drop_default', 'drop_trendy' ) ),
				'group' => us_translate( 'Appearance' ),
			),
			'show_item_title' => array(
				'switch_text' => __( 'Show titles before values', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'show_if' => array( 'style', '=', array( 'switch_default', 'switch_trendy' ) ),
				'group' => us_translate( 'Appearance' ),
			),
			'values_max_height' => array(
				'title' => __( 'Max Height of the list of values', 'us' ),
				'description' => $misc['desc_height'],
				'type' => 'text',
				'std' => '40vh',
				'group' => us_translate( 'Appearance' ),
			),
			'hide_disabled_values' => array(
				'switch_text' => __( 'Hide unavailable values', 'us' ),
				'description' => __( 'When turned off, unavailable values will remain visible, but not clickable.', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'group' => us_translate( 'Appearance' ),
			),

			// Mobiles
			'mobile_width' => array(
				'title' => __( 'Mobile view at screen width', 'us' ),
				'description' => __( 'Leave blank to not apply mobile view.', 'us' ),
				'type' => 'text',
				'std' => '600px',
				'group' => __( 'Mobiles', 'us' ),
			),
			'mobile_button_label' => array(
				'title' => __( 'Button Label', 'us' ),
				'type' => 'text',
				'std' => __( 'Filters', 'us' ),
				'group' => __( 'Mobiles', 'us' ),
			),
			'mobile_button_style' => array(
				'title' => __( 'Button Style', 'us' ),
				'description' => $misc['desc_btn_styles'],
				'type' => 'select',
				'options' => us_array_merge(
					array(
						'' => '– ' . us_translate( 'None' ) . ' –'
					),
					us_get_btn_styles()
				),
				'std' => '',
				'group' => __( 'Mobiles', 'us' ),
			),
			'mobile_button_icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'group' => __( 'Mobiles', 'us' ),
			),
			'mobile_button_iconpos' => array(
				'title' => __( 'Icon Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'left',
				'group' => __( 'Mobiles', 'us' ),
			),

		), $design_options
	),
);
