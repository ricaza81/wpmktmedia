<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: product_gallery
 */

$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => us_translate( 'Product gallery', 'woocommerce' ),
	'icon' => 'fas fa-images',
	'place_if' => class_exists( 'woocommerce' ),
	'category' => __( 'Post Elements', 'us' ),
	'params' => us_set_params_weight(

		// General section
		array(
			'hide_input' => array(
				'title' => sprintf( __( 'Edit Product gallery appearance on %sTheme Options%s.', 'us' ), '<a target="_blank" rel="noopener" href="' . admin_url() . 'admin.php?page=us-theme-options#woocommerce">', '</a>' ),
				'type' => 'info',
				'usb_preview' => TRUE,
			),
		),

		$design_options_params
	),
);
