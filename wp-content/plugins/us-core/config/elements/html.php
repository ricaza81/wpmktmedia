<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$design_options = us_config( 'elements_design_options' );
$hover_options = us_config( 'elements_hover_options' );

return array(
	'title' => __( 'Custom HTML', 'us' ),
	'icon' => 'fas fa-code',
	'params' => array_merge( array(

		'content' => array(
			'description' => sprintf( __( 'Added content will be displayed inside the %s block', 'us' ), '<code>&lt;div class="w-html"&gt;&lt;/div&gt;</code>' ),
			'type' => 'html',
			'encoded' => TRUE,
			'std' => '',
			'classes' => 'desc_2',
		),

	), $design_options, $hover_options ),
);
