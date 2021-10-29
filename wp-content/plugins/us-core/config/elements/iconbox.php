<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'IconBox', 'us' ),
	'description' => __( 'Icon, title, description', 'us' ),
	'admin_enqueue_js' => US_CORE_URI . '/plugins-support/js_composer/js/us_icon_view.js',
	'js_view' => 'ViewUsIcon',
	'params' => array_merge( array(

		// Icon
		'icon' => array(
			'title' => __( 'Icon', 'us' ),
			'type' => 'icon',
			'std' => 'fas|star',
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
			'cols' => 2,
		),
		'style' => array(
			'title' => __( 'Icon Style', 'us' ),
			'type' => 'select',
			'options' => array(
				'default' => __( 'Simple', 'us' ),
				'circle' => __( 'Inside the Solid circle', 'us' ),
				'outlined' => __( 'Inside the Outlined circle', 'us' ),
			),
			'std' => 'default',
			'cols' => 2,
		),
		'icon_color' => array(
			'title' => __( 'Icon Color', 'us' ),
			'type' => 'color',
			'clear_pos' => 'left',
			'std' => '',
			'cols' => 2,
			'show_if' => array( 'color', '=', 'custom' ),
		),
		'circle_color' => array(
			'title' => __( 'Icon Circle Color', 'us' ),
			'type' => 'color',
			'clear_pos' => 'left',
			'std' => '',
			'cols' => 2,
			'show_if' => array( 'color', '=', 'custom' ),
		),
		'size' => array(
			'title' => __( 'Icon Size', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '36px',
			'cols' => 2,
		),
		'iconpos' => array(
			'title' => __( 'Icon Position', 'us' ),
			'type' => 'select',
			'options' => array(
				'top' => us_translate( 'Top' ),
				'left' => us_translate( 'Left' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'top',
			'cols' => 2,
		),

		// Title & Description
		'title' => array(
			'title' => us_translate( 'Title' ),
			'type' => 'text',
			'std' => '',
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
			'std' => 'h4',
			'cols' => 2,
			'show_if' => array( 'title', '!=', '' ),
		),
		'content' => array(
			'title' => us_translate( 'Description' ),
			'type' => 'editor',
			'std' => '',
			'holder' => 'div',
		),

		// More Options
		'link' => array(
			'title' => us_translate( 'Link' ),
			'description' => __( 'Will be applied to the icon and title', 'us' ),
			'type' => 'link',
			'std' => '',
			'group' => __( 'More Options', 'us' ),
		),
		'alignment' => array(
			'title' => us_translate( 'Alignment' ),
			'type' => 'select',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'center' => us_translate( 'Center' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'center',
			'group' => __( 'More Options', 'us' ),
		),
		'img' => array(
			'title' => us_translate( 'Image' ),
			'description' => __( 'Will be shown instead of the icon', 'us' ),
			'type' => 'upload',
			'is_multiple' => FALSE,
			'extension' => 'png,jpg,jpeg,gif,svg',
			'group' => __( 'More Options', 'us' ),
		),

	), $design_options ),
);
