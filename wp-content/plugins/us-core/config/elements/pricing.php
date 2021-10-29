<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: pricing
 */

$btn_styles = us_get_btn_styles();

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

$items_std = array(
	array(
		'title' => 'Free',
		'price' => '$0',
		'substring' => 'per month',
		'features' => '1 project
1 user
200 tasks
No support',
		'btn_text' => 'Sign up',
		'btn_link' => 'url:#|||',
		'btn_color' => 'light',
		'btn_style' => '1',
		'btn_size' => '15px',
		'btn_iconpos' => 'left',
	),
	array(
		'title' => 'Standard',
		'type' => '1',
		'price' => '$24',
		'substring' => 'per month',
		'features' => '10 projects
10 users
Unlimited tasks
Premium support',
		'btn_text' => 'Sign up',
		'btn_link' => 'url:#|||',
		'btn_color' => 'primary',
		'btn_style' => '1',
		'btn_size' => '15px',
		'btn_iconpos' => 'left',
	),
	array(
		'title' => 'Premium',
		'price' => '$50',
		'substring' => 'per month',
		'features' => 'Unlimited projects
Unlimited users
Unlimited tasks
Premium support',
		'btn_text' => 'Sign up',
		'btn_link' => 'url:#|||',
		'btn_color' => 'light',
		'btn_style' => '1',
		'btn_size' => '15px',
		'btn_iconpos' => 'left',
	),
);

/**
 * @return array
 */
return array(
	'title' => __( 'Pricing Table', 'us' ),
	'icon' => 'fas fa-dollar-sign',
	'params' => us_set_params_weight(

		// General section
		array(
			'style' => array(
				'title' => us_translate( 'Style' ),
				'type' => 'select',
				'options' => array(
					'simple' => __( 'Simple', 'us' ),
					'cards' => __( 'Cards', 'us' ),
					'flat' => __( 'Flat', 'us' ),
				),
				'std' => 'simple',
				'admin_label' => TRUE,
				'usb_preview' => array(
					'mod' => 'style',
				),
			),
			'items' => array(
				'type' => 'group',
				'show_controls' => TRUE,
				'is_sortable' => TRUE,
				'is_accordion' => TRUE,
				'accordion_title' => 'title',
				'usb_preview' => TRUE,
				'params' => array(
					'title' => array(
						'title' => us_translate( 'Title' ),
						'type' => 'text',
						'std' => 'New Item',
						'admin_label' => TRUE,
					),
					'type' => array(
						'type' => 'switch',
						'switch_text' => __( 'Mark this item as featured', 'us' ),
						'std' => FALSE,
					),
					'price' => array(
						'title' => __( 'Price', 'us' ),
						'type' => 'text',
						'std' => '',
						'cols' => 2,
						'admin_label' => TRUE,
					),
					'substring' => array(
						'title' => __( 'Price Substring', 'us' ),
						'type' => 'text',
						'std' => '',
						'cols' => 2,
					),
					'features' => array(
						'title' => __( 'Features List', 'us' ),
						'type' => 'textarea',
						'std' => '',
					),
					'btn_text' => array(
						'title' => __( 'Button Label', 'us' ),
						'type' => 'text',
						'std' => '',
					),
					'btn_link' => array(
						'title' => __( 'Button Link', 'us' ),
						'type' => 'link',
						'std' => '',
					),
					'btn_style' => array(
						'title' => __( 'Button Style', 'us' ),
						'description' => $misc['desc_btn_styles'],
						'type' => 'select',
						'options' => $btn_styles,
						'std' => '1',
					),
					'btn_size' => array(
						'title' => __( 'Button Size', 'us' ),
						'description' => $misc['desc_font_size'],
						'type' => 'text',
						'std' => '',
					),
					'btn_icon' => array(
						'title' => __( 'Button Icon', 'us' ),
						'type' => 'icon',
						'std' => '',
					),
					'btn_iconpos' => array(
						'title' => __( 'Button Icon Position', 'us' ),
						'type' => 'radio',
						'options' => array(
							'left' => us_translate( 'Left' ),
							'right' => us_translate( 'Right' ),
						),
						'std' => 'left',
					),
				),
				'std' => $items_std,
			),
		),

		$design_options_params
	),
);
