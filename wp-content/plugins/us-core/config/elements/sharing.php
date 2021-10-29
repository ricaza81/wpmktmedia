<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Sharing Buttons', 'us' ),
	'icon' => 'fas fa-share-alt',
	'params' => array_merge( array(

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
			),
			'std' => array( 'facebook', 'twitter' ),
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
		),
		'align' => array(
			'title' => us_translate( 'Alignment' ),
			'type' => 'select',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'center' => us_translate( 'Center' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'left',
			'cols' => 2,
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
		),
		'counters' => array(
			'title' => __( 'Share Counters', 'us' ),
			'type' => 'select',
			'options' => array(
				'show' => us_translate( 'Show' ),
				'hide' => us_translate( 'None' ),
			),
			'std' => 'show',
			'cols' => 2,
		),
		'text_selection' => array(
			'switch_text' => __( 'Allow to share selected text', 'us' ),
			'description' => __( 'When you select text on a page, a panel with buttons appears, and you can quickly share the selected text.', 'us' ),
			'type' => 'switch',
			'std' => FALSE,
		),
		'text_selection_post' => array(
			'switch_text' => __( 'Text selection inside post content only', 'us' ),
			'type' => 'switch',
			'std' => FALSE,
			'classes' => 'for_above',
			'show_if' => array( 'text_selection', '=', '1' ),
		),
		'url' => array(
			'title' => __( 'Sharing URL (optional)', 'us' ),
			'description' => __( 'If not specified, the opened page URL will be used by default', 'us' ),
			'type' => 'textfield',
			'std' => '',
		),

	), $design_options ),
);
