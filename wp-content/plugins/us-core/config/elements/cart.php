<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => us_translate( 'Cart', 'woocommerce' ),
	'icon' => 'fas fa-shopping-cart',
	'place_if' => class_exists( 'woocommerce' ),
	'params' => array_merge( array(

		'hide_empty' => array(
			'type' => 'switch',
			'switch_text' => us_translate( 'Hide if cart is empty', 'woocommerce' ),
			'std' => FALSE,
		),
		'icon' => array(
			'title' => __( 'Icon', 'us' ),
			'type' => 'icon',
			'std' => 'fas|shopping-cart',
		),
		'size' => array(
			'title' => __( 'Icon Size', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '20px',
			'cols' => 3,
		),
		'size_tablets' => array(
			'title' => __( 'Icon Size on Tablets', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '20px',
			'cols' => 3,
		),
		'size_mobiles' => array(
			'title' => __( 'Icon Size on Mobiles', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '20px',
			'cols' => 3,
		),
		'quantity_color_bg' => array(
			'title' => __( 'Quantity Badge Background', 'us' ),
			'type' => 'color',
			'std' => '_header_middle_text_hover',
			'cols' => 2,
		),
		'quantity_color_text' => array(
			'title' => __( 'Quantity Badge Text', 'us' ),
			'type' => 'color',
			'with_gradient' => FALSE,
			'std' => '_header_middle_bg',
			'cols' => 2,
		),
		'vstretch' => array(
			'title' => us_translate( 'Height' ),
			'type' => 'switch',
			'switch_text' => __( 'Stretch to the full available height', 'us' ),
			'std' => TRUE,
		),
		'dropdown_effect' => array(
			'title' => __( 'Dropdown Effect', 'us' ),
			'type' => 'select',
			'options' => $misc['dropdown_effect_values'],
			'std' => 'height',
		),

	), $design_options ),
);
