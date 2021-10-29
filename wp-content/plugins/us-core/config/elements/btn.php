<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );
$hover_options = us_config( 'elements_hover_options' );
$link_custom_values = us_get_elm_link_options();

return array(
	'title' => __( 'Button', 'us' ),
	'icon' => 'icon-wpb-ui-button',
	'admin_enqueue_js' => US_CORE_URI . '/plugins-support/js_composer/js/us_icon_view.js',
	'js_view' => 'ViewUsIcon',
	'params' => array_merge( array(

		// General
		'label' => array(
			'title' => __( 'Button Label', 'us' ),
			'type' => 'text',
			'std' => __( 'Click Me', 'us' ),
			'holder' => 'button',
		),
		'link_type' => array(
			'title' => us_translate( 'Link' ),
			'type' => 'select',
			'options' => array_merge(
				array(
					'none' => us_translate( 'None' ),
					'post' => __( 'To a Post', 'us' ),
					'elm_value' => __( 'Use the element value as link', 'us' ),
					'onclick' => __( 'Onclick JavaScript action', 'us' ),
				),
				$link_custom_values,
				array( 'custom' => __( 'Custom', 'us' ) )
			),
			'std' => 'custom',
			'grid_std' => 'post',
		),
		'link_new_tab' => array(
			'type' => 'switch',
			'switch_text' => us_translate( 'Open link in a new tab' ),
			'std' => FALSE,
			'classes' => 'for_above',
			'show_if' => array( 'link_type', '!=', array( 'none', 'onclick', 'custom' ) ),
		),
		'link' => array(
			'placeholder' => us_translate( 'Enter the URL' ),
			'description' => $misc['desc_grid_custom_link'],
			'type' => 'link',
			'std' => array(),
			'shortcode_std' => '',
			'classes' => 'for_above desc_3',
			'show_if' => array( 'link_type', '=', 'custom' ),
		),
		'onclick_code' => array(
			'type' => 'text',
			'std' => 'return false',
			'classes' => 'for_above',
			'show_if' => array( 'link_type', '=', 'onclick' ),
		),
		'style' => array(
			'title' => us_translate( 'Style' ),
			'description' => $misc['desc_btn_styles'],
			'type' => 'select',
			'options' => us_get_btn_styles(),
			'std' => '1',
		),
		'width_type' => array(
			'title' => us_translate( 'Width' ),
			'type' => 'select',
			'options' => array(
				'auto' => us_translate( 'Auto' ),
				'full' => __( 'Stretch to the full width', 'us' ),
			),
			'std' => 'auto',
			'context' => array( 'shortcode' ),
		),
		'align' => array(
			'title' => __( 'Button Position', 'us' ),
			'type' => 'select',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'center' => us_translate( 'Center' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'left',
			'show_if' => array( 'width_type', '=', 'auto' ),
			'context' => array( 'shortcode' ),
		),

		'icon' => array(
			'title' => __( 'Icon', 'us' ),
			'type' => 'icon',
			'std' => '',
		),
		'iconpos' => array(
			'title' => __( 'Icon Position', 'us' ),
			'type' => 'radio',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'left',
		),

	), $design_options, $hover_options ),
);
