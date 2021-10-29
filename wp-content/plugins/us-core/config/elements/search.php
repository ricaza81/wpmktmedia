<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => us_translate( 'Search' ),
	'icon' => 'fas fa-search',
	'params' => array_merge( array(

		'text' => array(
			'title' => __( 'Placeholder', 'us' ),
			'type' => 'text',
			'std' => us_translate( 'Search' ),
			'admin_label' => TRUE,
		),
		'product_search' => array(
			'type' => 'switch',
			'switch_text' => __( 'Search Shop Products only', 'us' ),
			'std' => FALSE,
			'place_if' => class_exists( 'woocommerce' ),
		),
		'layout' => array(
			'title' => __( 'Layout', 'us' ),
			'type' => 'radio',
			'context' => array( 'header' ),
			'options' => array(
				'simple' => __( 'Simple', 'us' ),
				'modern' => __( 'Modern', 'us' ),
				'fullwidth' => us_translate( 'Full width' ),
				'fullscreen' => __( 'Full Screen', 'us' ),
			),
			'std' => 'fullwidth',
		),
		'field_width' => array(
			'title' => __( 'Field Width', 'us' ),
			'type' => 'slider',
			'std' => '240px',
			'context' => array( 'header' ),
			'options' => array(
				'px' => array(
					'min' => 200,
					'max' => 800,
				),
				'rem' => array(
					'min' => 10,
					'max' => 60,
				),
				'vw' => array(
					'min' => 10,
					'max' => 60,
				),
			),
			'cols' => 2,
			'show_if' => array( 'layout', '=', array( 'simple', 'modern' ) ),
		),
		'field_width_tablets' => array(
			'title' => __( 'Field Width on Tablets', 'us' ),
			'type' => 'slider',
			'std' => '200px',
			'context' => array( 'header' ),
			'options' => array(
				'px' => array(
					'min' => 200,
					'max' => 600,
				),
				'rem' => array(
					'min' => 10,
					'max' => 40,
				),
				'vw' => array(
					'min' => 10,
					'max' => 40,
				),
			),
			'cols' => 2,
			'show_if' => array( 'layout', '=', array( 'simple', 'modern' ) ),
		),

		'field_bg_color' => array(
			'title' => __( 'Search Field Background', 'us' ),
			'type' => 'color',
			'clear_pos' => 'left',
			'std' => '',
			'cols' => 2,
		),
		'field_text_color' => array(
			'title' => __( 'Search Field Text', 'us' ),
			'type' => 'color',
			'clear_pos' => 'left',
			'with_gradient' => FALSE,
			'std' => '',
			'cols' => 2,
		),
		'icon' => array(
			'title' => __( 'Icon', 'us' ),
			'type' => 'icon',
			'std' => 'fas|search',
		),
		'icon_size' => array(
			'title' => __( 'Icon Size', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '18px',
			'header_cols' => 3,
		),
		'icon_size_tablets' => array(
			'title' => __( 'Icon Size on Tablets', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'context' => array( 'header' ),
			'std' => '20px',
			'cols' => 3,
		),
		'icon_size_mobiles' => array(
			'title' => __( 'Icon Size on Mobiles', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'context' => array( 'header' ),
			'std' => '22px',
			'cols' => 3,
		),

	), $design_options ),
);
