<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Page Scroller', 'us' ),
	'description' => __( 'Accurate scroll to page sections', 'us' ),
	'icon' => 'fas fa-ellipsis-v',
	'params' => array_merge( array(

		'disable_width' => array(
			'title' => __( 'Disable scrolling at width', 'us' ),
			'description' => __( 'When screen width is less than this value, scrolling by rows will be disabled.', 'us' ),
			'type' => 'text',
			'std' => '768px',
			'admin_label' => TRUE,
		),
		'speed' => array(
			'title' => __( 'Scroll Speed (milliseconds)', 'us' ),
			'type' => 'text',
			'std' => '1000',
		),
		'dots' => array(
			'type' => 'switch',
			'switch_text' => __( 'Show Navigation Dots', 'us' ),
			'std' => FALSE,
		),
		'dots_style' => array(
			'title' => __( 'Dots Style', 'us' ),
			'type' => 'select',
			'options' => array(
				'1' => us_translate( 'Style' ) . ' 1',
				'2' => us_translate( 'Style' ) . ' 2',
				'3' => us_translate( 'Style' ) . ' 3',
				'4' => us_translate( 'Style' ) . ' 4',
			),
			'std' => '1',
			'cols' => 2,
			'show_if' => array( 'dots', '!=', FALSE ),
		),
		'dots_pos' => array(
			'title' => __( 'Dots Position', 'us' ),
			'type' => 'select',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'right',
			'cols' => 2,
			'show_if' => array( 'dots', '!=', FALSE ),
		),
		'dots_size' => array(
			'title' => __( 'Dots Size', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '10px',
			'cols' => 2,
			'show_if' => array( 'dots', '!=', FALSE ),
		),
		'dots_color' => array(
			'title' => __( 'Dots Color', 'us' ),
			'type' => 'color',
			'clear_pos' => 'left',
			'with_gradient' => FALSE,
			'std' => '',
			'cols' => 2,
			'show_if' => array( 'dots', '!=', FALSE ),
		),
		'include_footer' => array(
			'type' => 'switch',
			'switch_text' => __( 'Show dots for Footer', 'us' ),
			'std' => FALSE,
			'show_if' => array( 'dots', '!=', FALSE ),
		),

	), $design_options ),
);
