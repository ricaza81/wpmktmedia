<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: cta
 */

$btn_styles = us_get_btn_styles();

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'ActionBox', 'us' ),
	'description' => __( 'Content box with call to action button', 'us' ),
	'icon' => 'fas fa-file-invoice',
	'params' => us_set_params_weight(

		// General section
		array(
			'title' => array(
				'title' => us_translate( 'Title' ),
				'type' => 'text',
				'std' => 'This is ActionBox',
				'holder' => 'div',
				'usb_preview' => array(
					'elm' => '.w-actionbox-title',
					'attr' => 'html',
				),
			),
			'title_size' => array(
				'title' => __( 'Title Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'show_if' => array( 'title', '!=', '' ),
				'usb_preview' => array(
					'elm' => '.w-actionbox-title',
					'css' => 'font-size',
				),
			),
			'title_tag' => array(
				'title' => __( 'Title HTML tag', 'us' ),
				'type' => 'select',
				'options' => $misc['html_tag_values'],
				'std' => 'h2',
				'show_if' => array( 'title', '!=', '' ),
				'cols' => 2,
				'usb_preview' => array(
					'elm' => '.w-actionbox-title',
					'attr' => 'tag',
				),
			),
			'content' => array(
				'title' => us_translate( 'Description' ),
				'type' => 'editor',
				'editor_settings' => array(
					'editor_height' => 200,
					'media_buttons' => FALSE, // remove Add Media button
					'teeny' => TRUE, // remove extra WP editor buttons
				),
				'std' => '',
				'holder' => 'div',
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-actionbox-description',
				),
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
				'usb_preview' => array(
					'mod' => 'color',
				),
			),
			'controls' => array(
				'title' => __( 'Buttons Location', 'us' ),
				'type' => 'radio',
				'options' => array(
					'right' => us_translate( 'Right' ),
					'bottom' => us_translate( 'Bottom' ),
				),
				'std' => 'right',
				'usb_preview' => array(
					'mod' => 'controls',
				),
			),
		),

		// Button 1 section,
		array(
			'btn_label' => array(
				'title' => __( 'Button Label', 'us' ),
				'type' => 'text',
				'std' => __( 'Click Me', 'us' ),
				'group' => __( 'Button', 'us' ) . ' 1',
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-btn:first > .w-btn-label',
				),
			),
			'btn_link' => array(
				'title' => us_translate( 'Link' ),
				'type' => 'link',
				'std' => '',
				'group' => __( 'Button', 'us' ) . ' 1',
				'usb_preview' => TRUE,
			),
			'btn_style' => array(
				'title' => us_translate( 'Style' ),
				'description' => $misc['desc_btn_styles'],
				'type' => 'select',
				'options' => $btn_styles,
				'std' => '1',
				'group' => __( 'Button', 'us' ) . ' 1',
				'usb_preview' => array(
					'mod' => 'us-btn-style',
					'elm' => '.w-btn:first',
				),
			),
			'btn_size' => array(
				'title' => us_translate( 'Size' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '',
				'group' => __( 'Button', 'us' ) . ' 1',
				'usb_preview' => array(
					'css' => 'font-size',
					'elm' => '.w-btn:first',
				),
			),
			'btn_icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'group' => __( 'Button', 'us' ) . ' 1',
				'usb_preview' => TRUE,
			),
			'btn_iconpos' => array(
				'title' => __( 'Icon Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'left',
				'group' => __( 'Button', 'us' ) . ' 1',
				'usb_preview' => TRUE,
			),
		),

		// Button 2 section
		array(
			'second_button' => array(
				'type' => 'switch',
				'switch_text' => __( 'Display second button', 'us' ),
				'std' => FALSE,
				'group' => __( 'Button', 'us' ) . ' 2',
				'usb_preview' => TRUE,
			),
			'btn2_label' => array(
				'title' => __( 'Button Label', 'us' ),
				'type' => 'text',
				'std' => __( 'Click Me', 'us' ),
				'show_if' => array( 'second_button', '!=', FALSE ),
				'group' => __( 'Button', 'us' ) . ' 2',
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-btn:last > .w-btn-label',
				),
			),
			'btn2_link' => array(
				'title' => us_translate( 'Link' ),
				'type' => 'link',
				'std' => '',
				'show_if' => array( 'second_button', '!=', FALSE ),
				'group' => __( 'Button', 'us' ) . ' 2',
				'usb_preview' => TRUE,
			),
			'btn2_style' => array(
				'title' => us_translate( 'Style' ),
				'description' => $misc['desc_btn_styles'],
				'type' => 'select',
				'options' => $btn_styles,
				'std' => '1',
				'show_if' => array( 'second_button', '!=', FALSE ),
				'group' => __( 'Button', 'us' ) . ' 2',
				'usb_preview' => array(
					'mod' => 'us-btn-style',
					'elm' => '.w-btn:last',
				),
			),
			'btn2_size' => array(
				'title' => us_translate( 'Size' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '',
				'show_if' => array( 'second_button', '!=', FALSE ),
				'group' => __( 'Button', 'us' ) . ' 2',
				'usb_preview' => array(
					'css' => 'font-size',
					'elm' => '.w-btn:last',
				),
			),
			'btn2_icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'show_if' => array( 'second_button', '!=', FALSE ),
				'group' => __( 'Button', 'us' ) . ' 2',
				'usb_preview' => TRUE,
			),
			'btn2_iconpos' => array(
				'title' => __( 'Icon Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'left',
				'show_if' => array( 'second_button', '!=', FALSE ),
				'group' => __( 'Button', 'us' ) . ' 2',
				'usb_preview' => TRUE,
			),
		),

		$design_options_params
	),
);
