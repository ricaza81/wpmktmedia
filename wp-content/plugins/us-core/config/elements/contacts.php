<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: contacts
 */

$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => us_translate( 'Contact Info' ),
	'icon' => 'fas fa-phone',
	'params' => us_set_params_weight(

		// General section
		array(
			'address' => array(
				'title' => __( 'Address', 'us' ),
				'type' => 'text',
				'std' => '',
				'usb_preview' => TRUE,
			),
			'phone' => array(
				'title' => __( 'Phone', 'us' ),
				'type' => 'text',
				'std' => '0123456789',
				'usb_preview' => TRUE,
			),
			'fax' => array(
				'title' => __( 'Mobiles', 'us' ),
				'type' => 'text',
				'std' => '',
				'usb_preview' => TRUE,
			),
			'email' => array(
				'title' => us_translate( 'Email' ),
				'type' => 'text',
				'std' => '',
				'usb_preview' => TRUE,
			),
		),

		$design_options_params
	),
);
