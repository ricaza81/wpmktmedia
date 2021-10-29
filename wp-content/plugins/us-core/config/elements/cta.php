<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$btn_styles = us_get_btn_styles();

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'ActionBox', 'us' ),
	'description' => __( 'Content box with call to action button', 'us' ),
	'icon' => 'icon-wpb-call-to-action',
	'params' => array_merge( array(

		// General
		'title' => array(
			'title' => us_translate( 'Title' ),
			'type' => 'text',
			'std' => 'This is ActionBox',
			'holder' => 'div',
		),
		'title_size' => array(
			'title' => __( 'Title Size', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '',
			'cols' => 2,
			'show_if' => array( 'title', '!=', '' ),
		),
		'title_tag' => array(
			'title' => __( 'Title HTML tag', 'us' ),
			'type' => 'select',
			'options' => $misc['html_tag_values'],
			'std' => 'h2',
			'show_if' => array( 'title', '!=', '' ),
			'cols' => 2,
		),
		'content' => array(
			'title' => us_translate( 'Description' ),
			'type' => 'textarea',
			'std' => '',
			'holder' => 'div',
		),
		'color' => array(
			'title' => us_translate( 'Colors' ),
			'type' => 'select',
			'options' => array(
				'primary' => __( 'Primary bg & White text', 'us' ),
				'secondary' => __( 'Secondary bg & White text', 'us' ),
				'light' => __( 'Alternate bg & Content text', 'us' ),
			),
			'std' => 'primary',
		),
		'controls' => array(
			'title' => __( 'Buttons Location', 'us' ),
			'type' => 'select',
			'options' => array(
				'right' => us_translate( 'Right' ),
				'bottom' => us_translate( 'Bottom' ),
			),
			'std' => 'right',
		),

		// Button 1
		'btn_label' => array(
			'title' => __( 'Button Label', 'us' ),
			'type' => 'text',
			'std' => __( 'Click Me', 'us' ),
			'group' => __( 'Button', 'us' ) . ' 1',
		),
		'btn_link' => array(
			'title' => us_translate( 'Link' ),
			'type' => 'link',
			'std' => '',
			'group' => __( 'Button', 'us' ) . ' 1',
		),
		'btn_style' => array(
			'title' => us_translate( 'Style' ),
			'description' => $misc['desc_btn_styles'],
			'type' => 'select',
			'options' => $btn_styles,
			'std' => '1',
			'group' => __( 'Button', 'us' ) . ' 1',
		),
		'btn_size' => array(
			'title' => us_translate( 'Size' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '',
			'group' => __( 'Button', 'us' ) . ' 1',
		),
		'btn_icon' => array(
			'title' => __( 'Icon', 'us' ),
			'type' => 'icon',
			'std' => '',
			'group' => __( 'Button', 'us' ) . ' 1',
		),
		'btn_iconpos' => array(
			'title' => __( 'Icon Position', 'us' ),
			'type' => 'select',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'left',
			'group' => __( 'Button', 'us' ) . ' 1',
		),

		// Button 2
		'second_button' => array(
			'type' => 'switch',
			'switch_text' => __( 'Display second button', 'us' ),
			'std' => FALSE,
			'group' => __( 'Button', 'us' ) . ' 2',
		),
		'btn2_label' => array(
			'title' => __( 'Button Label', 'us' ),
			'type' => 'text',
			'std' => __( 'Click Me', 'us' ),
			'show_if' => array( 'second_button', '!=', FALSE ),
			'group' => __( 'Button', 'us' ) . ' 2',
		),
		'btn2_link' => array(
			'title' => us_translate( 'Link' ),
			'type' => 'link',
			'std' => '',
			'show_if' => array( 'second_button', '!=', FALSE ),
			'group' => __( 'Button', 'us' ) . ' 2',
		),
		'btn2_style' => array(
			'title' => us_translate( 'Style' ),
			'description' => $misc['desc_btn_styles'],
			'type' => 'select',
			'options' => $btn_styles,
			'std' => '1',
			'show_if' => array( 'second_button', '!=', FALSE ),
			'group' => __( 'Button', 'us' ) . ' 2',
		),
		'btn2_size' => array(
			'title' => us_translate( 'Size' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '',
			'show_if' => array( 'second_button', '!=', FALSE ),
			'group' => __( 'Button', 'us' ) . ' 2',
		),
		'btn2_icon' => array(
			'title' => __( 'Icon', 'us' ),
			'type' => 'icon',
			'std' => '',
			'show_if' => array( 'second_button', '!=', FALSE ),
			'group' => __( 'Button', 'us' ) . ' 2',
		),
		'btn2_iconpos' => array(
			'title' => __( 'Icon Position', 'us' ),
			'type' => 'select',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'left',
			'show_if' => array( 'second_button', '!=', FALSE ),
			'group' => __( 'Button', 'us' ) . ' 2',
		),

	), $design_options ),
);
