<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: add_to_cart
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );

/**
 * @return array
 */
return array(
	'title' => sprintf( __( '"%s" Button', 'us' ), us_translate( 'Add to cart', 'woocommerce' ) ),
	'icon' => 'fas fa-cart-plus',
	'place_if' => class_exists( 'woocommerce' ),
	'category' => __( 'Post Elements', 'us' ),
	'params' => us_set_params_weight(

		// General section
		array(
			'view_cart_link' => array(
				'type' => 'switch',
				'switch_text' => __( 'Show link to cart when adding products', 'us' ),
				'std' => FALSE,
				'context' => array( 'grid' ),
				'usb_preview' => array(
					'toggle_class' => 'no_view_cart_link',
				),
			),
		),

		$design_options_params,
		$hover_options_params
	),
);
