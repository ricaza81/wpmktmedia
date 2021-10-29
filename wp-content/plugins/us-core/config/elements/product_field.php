<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );
$hover_options = us_config( 'elements_hover_options' );

return array(
	'title' => us_translate( 'Product data', 'woocommerce' ),
	'category' => __( 'Post Elements', 'us' ),
	'params' => array_merge( array(
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
		),
		'sale_text' => array(
			'title' => __( 'Sale Badge Text', 'us' ),
			'type' => 'text',
			'std' => us_translate( 'Sale!', 'woocommerce' ),
			'show_if' => array( 'type', '=', 'sale_badge' ),
		),
		'display_type' => array(
			'switch_text' => __( 'Show as table', 'us' ),
			'type' => 'switch',
			'std' => FALSE,
			'show_if' => array( 'type', '=', 'attributes' ),
		),
		'out_of_stock_only' => array(
			'switch_text' => __( 'Show only when out of stock', 'us' ),
			'type' => 'switch',
			'std' => FALSE,
			'show_if' => array( 'type', '=', 'stock' ),
		),

	), $design_options, $hover_options ),
);
