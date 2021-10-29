<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: product_field
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );

/**
 * @return array
 */
return array(
	'title' => us_translate( 'Product data', 'woocommerce' ),
	'icon' => 'fas fa-shopping-cart',
	'place_if' => class_exists( 'woocommerce' ),
	'category' => __( 'Post Elements', 'us' ),
	'params' => us_set_params_weight(

		// General section
		array(
			'type' => array(
				'title' => us_translate( 'Show' ),
				'type' => 'select',
				'options' => array(
					'price' => us_translate( 'Price', 'woocommerce' ),
					'rating' => us_translate( 'Rating', 'woocommerce' ),
					'sku' => us_translate( 'SKU', 'woocommerce' ),
					'sale_badge' => __( 'Sale Badge', 'us' ),
					'weight' => us_translate( 'Weight', 'woocommerce' ),
					'dimensions' => us_translate( 'Dimensions', 'woocommerce' ),
					'attributes' => us_translate( 'List of attributes.', 'woocommerce' ),
					'stock' => us_translate( 'Stock status', 'woocommerce' ),
					'default_actions' => __( 'Actions for plugins compatibility', 'us' ),
				),
				'std' => 'price',
				'admin_label' => TRUE,
				'usb_preview' => TRUE,
			),
			'sale_text' => array(
				'title' => __( 'Sale Badge Text', 'us' ),
				'type' => 'text',
				'std' => us_translate( 'Sale!', 'woocommerce' ),
				'show_if' => array( 'type', '=', 'sale_badge' ),
				'usb_preview' => TRUE,
			),
			'display_type' => array(
				'switch_text' => __( 'Show as table', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'show_if' => array( 'type', '=', 'attributes' ),
				'usb_preview' => TRUE,
			),
			'out_of_stock_only' => array(
				'switch_text' => __( 'Show only when out of stock', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'show_if' => array( 'type', '=', 'stock' ),
				'usb_preview' => TRUE,
			),
		),

		$design_options_params,
		$hover_options_params
	),
	'usb_preview_dummy_data' => array(
		'price' => '$100.99',
		'weight' => '2.55 kg',
		'dimensions' => '20 × 50 × 15',
		'attributes' => array(
			'1' => array(
				'label' => us_translate( 'Color' ),
				'value' => us_translate( 'Red' ),
			),
			'2' => array(
				'label' => us_translate( 'Size' ),
				'value' => 'S, M, L, XL',
			),
		),
	),
);
