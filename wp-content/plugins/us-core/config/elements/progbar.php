<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Progress Bar', 'us' ),
	'icon' => 'icon-wpb-graph',
	'params' => array_merge( array(

		'title' => array(
			'title' => us_translate( 'Title' ),
			'type' => 'text',
			'std' => 'This is Progress Bar',
			'holder' => 'div',
		),
		'title_size' => array(
			'title' => __( 'Title Size', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '1rem',
			'cols' => 2,
			'show_if' => array( 'title', '!=', '' ),
		),
		'title_tag' => array(
			'title' => __( 'Title HTML tag', 'us' ),
			'type' => 'select',
			'options' => $misc['html_tag_values'],
			'std' => 'h6',
			'cols' => 2,
			'show_if' => array( 'title', '!=', '' ),
		),
		'count' => array(
			'title' => __( 'Progress Value', 'us' ),
			'type' => 'text',
			'std' => '50%',
			'holder' => 'span',
			'cols' => 2,
		),
		'final_value' => array(
			'title' => __( 'Final Value', 'us' ),
			'type' => 'text',
			'std' => '100%',
			'holder' => 'span',
			'cols' => 2,
		),
		'hide_count' => array(
			'type' => 'switch',
			'switch_text' => __( 'Hide progress value counter', 'us' ),
			'std' => FALSE,
			'cols' => 2,
			'classes' => 'for_above',
		),
		'hide_final_value' => array(
			'type' => 'switch',
			'switch_text' => __( 'Hide final value', 'us' ),
			'std' => '1',
			'classes' => 'for_above',
			'cols' => 2,
			'show_if' => array( 'hide_count', '=', FALSE ),
		),
		'style' => array(
			'title' => us_translate( 'Style' ),
			'type' => 'select',
			'options' => array(
				'1' => us_translate( 'Style' ) . ' 1',
				'2' => us_translate( 'Style' ) . ' 2',
				'3' => us_translate( 'Style' ) . ' 3',
				'4' => us_translate( 'Style' ) . ' 4',
				'5' => us_translate( 'Style' ) . ' 5',
			),
			'std' => '1',
			'admin_label' => TRUE,
		),
		'color' => array(
			'title' => __( 'Progress Bar Color', 'us' ),
			'type' => 'select',
			'options' => array(
				'primary' => __( 'Primary (theme color)', 'us' ),
				'secondary' => __( 'Secondary (theme color)', 'us' ),
				'heading' => __( 'Heading (theme color)', 'us' ),
				'text' => __( 'Text (theme color)', 'us' ),
				'custom' => us_translate( 'Custom color' ),
			),
			'std' => 'primary',
		),
		'bar_color' => array(
			'type' => 'color',
			'clear_pos' => 'left',
			'std' => '',
			'classes' => 'for_above',
			'show_if' => array( 'color', '=', 'custom' ),
		),
		'size' => array(
			'title' => __( 'Progress Bar Height', 'us' ),
			'description' => $misc['desc_font_size'], // don't change to the "desc_height"
			'type' => 'text',
			'std' => '10px',
		),

	), $design_options ),
);
