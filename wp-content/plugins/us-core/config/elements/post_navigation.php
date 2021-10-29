<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$taxonomies_options = us_get_taxonomies();
$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Post Prev/Next Navigation', 'us' ),
	'category' => __( 'Post Elements', 'us' ),
	'icon' => 'fas fa-exchange-alt',
	'params' => array_merge( array(

		'layout' => array(
			'title' => __( 'Layout', 'us' ),
			'type' => 'radio',
			'options' => array(
				'simple' => __( 'Simple links', 'us' ),
				'sided' => __( 'On sides of the screen', 'us' ),
			),
			'std' => 'simple',
			'admin_label' => TRUE,
		),
		'invert' => array(
			'type' => 'switch',
			'switch_text' => __( 'Invert position of previous and next', 'us' ),
			'std' => FALSE,
		),
		'in_same_term' => array(
			'type' => 'switch',
			'switch_text' => __( 'Navigate within the specified taxonomy', 'us' ),
			'std' => FALSE,
		),
		'taxonomy' => array(
			'type' => 'select',
			'options' => $taxonomies_options,
			'std' => key( $taxonomies_options ),
			'classes' => 'for_above',
			'show_if' => array( 'in_same_term', '!=', FALSE ),
		),
		'prev_post_text' => array(
			'title' => __( 'Previous Post subtitle', 'us' ),
			'type' => 'text',
			'std' => us_translate( 'Previous Post' ),
			'cols' => 2,
			'show_if' => array( 'layout', '=', 'simple' ),
		),
		'next_post_text' => array(
			'title' => __( 'Next Post subtitle', 'us' ),
			'type' => 'text',
			'std' => us_translate( 'Next Post' ),
			'cols' => 2,
			'show_if' => array( 'layout', '=', 'simple' ),
		),

	), $design_options ),
);
