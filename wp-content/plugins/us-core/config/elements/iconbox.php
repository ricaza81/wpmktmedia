<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: iconbox
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'IconBox', 'us' ),
	'category' => __( 'Basic', 'us' ),
	'icon' => 'fas fa-star',
	'admin_enqueue_js' => US_CORE_URI . '/plugins-support/js_composer/js/us_icon_view.js',
	'js_view' => 'ViewUsIcon',
	'params' => us_set_params_weight(

		// General
		array(
			'icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => 'fas|star', // When changing the value, check the removal of icons in the js_view:ViewUsIcon
				'usb_preview' => TRUE,
			),
			'img' => array(
				'title' => us_translate( 'Image' ),
				'description' => __( 'Will be shown instead of the icon', 'us' ),
				'type' => 'upload',
				'extension' => 'png,jpg,jpeg,gif,svg',
				'usb_preview' => TRUE,
			),
			'link' => array(
				'title' => us_translate( 'Link' ),
				'description' => __( 'Will be applied to the icon and title', 'us' ),
				'type' => 'link',
				'std' => '',
				'usb_preview' => TRUE,
			),
			'title' => array(
				'title' => us_translate( 'Title' ),
				'type' => 'text',
				'std' => '',
				'holder' => 'div',
				'usb_preview' => TRUE,
			),
			'title_size' => array(
				'title' => __( 'Title Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'show_if' => array( 'title', '!=', '' ),
				'usb_preview' => array(
					'elm' => '.w-iconbox-title',
					'css' => 'font-size',
				),
			),
			'title_tag' => array(
				'title' => __( 'Title HTML tag', 'us' ),
				'type' => 'select',
				'options' => $misc['html_tag_values'],
				'std' => 'h4',
				'cols' => 2,
				'show_if' => array( 'title', '!=', '' ),
				'usb_preview' => array(
					'elm' => '.w-iconbox-title',
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
				'usb_preview' => TRUE,
			),

			// Appearance
			'style' => array(
				'title' => __( 'Icon Style', 'us' ),
				'type' => 'radio',
				'options' => array(
					'default' => __( 'Simple', 'us' ),
					'circle' => __( 'Solid', 'us' ),
					'outlined' => __( 'Outlined', 'us' ),
				),
				'std' => 'default',
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'style',
				),
			),
			'color' => array(
				'title' => __( 'Icon Color', 'us' ),
				'type' => 'select',
				'options' => array(
					'primary' => __( 'Primary (theme color)', 'us' ),
					'secondary' => __( 'Secondary (theme color)', 'us' ),
					'light' => __( 'Border (theme color)', 'us' ),
					'contrast' => __( 'Text (theme color)', 'us' ),
					'custom' => __( 'Custom colors', 'us' ),
				),
				'std' => 'primary',
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'color',
				),
			),
			'icon_color' => array(
				'title' => __( 'Icon Color', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'std' => '',
				'cols' => 2,
				'show_if' => array( 'color', '=', 'custom' ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'elm' => '.w-iconbox-icon',
					'css' => 'color',
				),
			),
			'circle_color' => array(
				'title' => __( 'Icon Circle Color', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'std' => '',
				'cols' => 2,
				'show_if' => array( 'color', '=', 'custom' ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'elm' => '.w-iconbox-icon',
					'css' => 'background',
				),
			),
			'size' => array(
				'title' => __( 'Icon Size', 'us' ),
				'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">50px</span>, <span class="usof-example">3rem</span>, <span class="usof-example">max( 40px, 4vw )</span>, <span class="usof-example">calc( 2rem + 2vmax )</span>',
				'type' => 'text',
				'std' => '2rem',
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'elm' => '.w-iconbox-icon',
					'css' => 'font-size',
				),
			),
			'iconpos' => array(
				'title' => __( 'Icon Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'top' => us_translate( 'Top' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'top',
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'alignment' => array(
				'title' => us_translate( 'Alignment' ),
				'type' => 'radio',
				'labels_as_icons' => 'fas fa-align-*',
				'options' => array(
					'none' => us_translate( 'Default' ),
					'left' => us_translate( 'Left' ),
					'center' => us_translate( 'Center' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'center',
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'align',
				),
			),
		),

		$design_options_params
	),
);
