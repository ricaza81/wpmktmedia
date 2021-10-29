<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$design_options = us_config( 'elements_design_options' );

return array(
	'title' => us_translate( 'Contact Info' ),
	'icon' => 'fas fa-phone',
	'params' => array_merge( array(

		'address' => array(
			'title' => __( 'Address', 'us' ),
			'type' => 'text',
			'std' => '',
		),
		'phone' => array(
			'title' => __( 'Phone', 'us' ),
			'type' => 'text',
			'std' => '',
		),
		'fax' => array(
			'title' => __( 'Mobiles', 'us' ),
			'type' => 'text',
			'std' => '',
		),
		'email' => array(
			'title' => us_translate( 'Email' ),
			'type' => 'text',
			'std' => '',
		),

	), $design_options ),
);
