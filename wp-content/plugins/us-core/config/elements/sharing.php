<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: sharing
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Sharing Buttons', 'us' ),
	'icon' => 'fas fa-share-alt',
	'params' => us_set_params_weight(

		// General section
		array(
			'providers' => array(
				'type' => 'checkboxes',
				'options' => array(
					'email' => us_translate( 'Email' ),
					'facebook' => 'Facebook',
					'twitter' => 'Twitter',
					'linkedin' => 'LinkedIn',
					'pinterest' => 'Pinterest',
					'vk' => 'Vkontakte',
					'whatsapp' => 'WhatsApp',
					'xing' => 'Xing',
					'reddit' => 'Reddit',
					'telegram' => 'Telegram',
				),
				'std' => 'facebook,twitter',
				'usb_preview' => TRUE,
			),
			'type' => array(
				'title' => us_translate( 'Style' ),
				'type' => 'select',
				'options' => array(
					'simple' => __( 'Simple', 'us' ),
					'solid' => __( 'Solid', 'us' ),
					'outlined' => __( 'Outlined', 'us' ),
					'fixed' => __( 'Fixed', 'us' ),
				),
				'std' => 'simple',
				'cols' => 2,
				'admin_label' => TRUE,
				'usb_preview' => array(
					'mod' => 'type',
				),
			),
			'align' => array(
				'title' => us_translate( 'Alignment' ),
				'type' => 'radio',
				'labels_as_icons' => 'fas fa-align-*',
				'options' => array(
					'none' => us_translate( 'Default' ),
					'left' => us_translate( 'Left' ),
					'center' => us_translate( 'Center' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'none',
				'cols' => 2,
				'usb_preview' => array(
					'mod' => 'align',
				),
			),
			'color' => array(
				'title' => us_translate( 'Colors' ),
				'type' => 'select',
				'options' => array(
					'default' => __( 'Default brands colors', 'us' ),
					'primary' => __( 'Primary (theme color)', 'us' ),
					'secondary' => __( 'Secondary (theme color)', 'us' ),
				),
				'std' => 'default',
				'cols' => 2,
				'admin_label' => TRUE,
				'usb_preview' => array(
					'mod' => 'color',
				),
			),
			'text_selection' => array(
				'switch_text' => __( 'Allow to share selected text', 'us' ),
				'description' => __( 'When you select text on a page, a panel with buttons appears, and you can quickly share the selected text.', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'usb_preview' => TRUE,
			),
			'text_selection_post' => array(
				'switch_text' => __( 'Text selection inside post content only', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'classes' => 'for_above',
				'show_if' => array( 'text_selection', '=', '1' ),
				'usb_preview' => TRUE,
			),
			'url' => array(
				'title' => __( 'Sharing URL (optional)', 'us' ),
				'description' => __( 'If not specified, the opened page URL will be used by default', 'us' ),
				'type' => 'text',
				'std' => '',
				'usb_preview' => TRUE,
			),
		),

		$design_options_params
	),
);
