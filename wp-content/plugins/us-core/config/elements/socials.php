<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Social Links', 'us' ),
	'icon' => 'fab fa-facebook',
	'params' => array_merge( array(

		// General
		'items' => array(
			'type' => 'group',
			'show_controls' => TRUE,
			'is_sortable' => TRUE,
			'params' => array(
				'type' => array(
					'shortcode_title' => us_translate( 'Type' ),
					'type' => 'select',
					'options' => array_merge(
						us_config( 'social_links' ),
						array( 'custom' => __( 'Custom Icon', 'us' ) )
					),
					'std' => 's500px',
					'cols' => 2,
					'admin_label' => TRUE,
					'classes' => 'for_socials',
				),
				'url' => array(
					'shortcode_title' => us_translate( 'Enter the URL' ),
					'placeholder' => us_translate( 'Enter the URL' ),
					'type' => 'text',
					'std' => '',
					'cols' => 2,
				),
				'custom_start' => array(
					'type' => 'wrapper_start',
					'show_if' => array( 'type', '=', 'custom' ),
				),
				'icon' => array(
					'type' => 'icon',
					'std' => 'fab|apple',
					'show_if' => array( 'type', '=', 'custom' ),
				),
				'title' => array(
					'shortcode_title' => us_translate( 'Title' ),
					'placeholder' => us_translate( 'Title' ),
					'type' => 'text',
					'std' => '',
					'cols' => 2,
					'show_if' => array( 'type', '=', 'custom' ),
				),
				'color' => array(
					'shortcode_title' => us_translate( 'Color' ),
					'type' => 'color',
					'std' => '_content_faded',
					'cols' => 2,
					'show_if' => array( 'type', '=', 'custom' ),
				),
				'custom_end' => array(
					'type' => 'wrapper_end',
				),
			),
			'std' => array(
				array(
					'type' => 'facebook',
					'url' => '#',
				),
				array(
					'type' => 'twitter',
					'url' => '#',
				),
			),
		),

		// Appearance
		'icons_color' => array(
			'title' => __( 'Icons Color', 'us' ),
			'type' => 'select',
			'options' => array(
				'brand' => __( 'Default brands colors', 'us' ),
				'text' => __( 'Text (theme color)', 'us' ),
				'link' => __( 'Link (theme color)', 'us' ),
			),
			'std' => 'brand',
			'admin_label' => TRUE,
			'group' => us_translate( 'Appearance' ),
		),
		'shape' => array(
			'title' => __( 'Icons Shape', 'us' ),
			'type' => 'select',
			'options' => array(
				'none' => us_translate( 'None' ),
				'square' => __( 'Square', 'us' ),
				'rounded' => __( 'Rounded Square', 'us' ),
				'circle' => __( 'Circle', 'us' ),
			),
			'std' => 'square',
			'admin_label' => TRUE,
			'group' => us_translate( 'Appearance' ),
		),
		'style' => array(
			'title' => __( 'Icons Style', 'us' ),
			'type' => 'select',
			'options' => array(
				'default' => __( 'Simple', 'us' ),
				'outlined' => __( 'With outline', 'us' ),
				'solid' => __( 'With light background', 'us' ),
				'colored' => __( 'With colored background', 'us' ),
			),
			'std' => 'default',
			'cols' => 2,
			'show_if' => array( 'shape', '!=', 'none' ),
			'group' => us_translate( 'Appearance' ),
		),
		'hover' => array(
			'title' => __( 'Hover Style', 'us' ),
			'type' => 'select',
			'options' => array(
				'fade' => __( 'Fade', 'us' ),
				'slide' => __( 'Slide', 'us' ),
				'none' => us_translate( 'None' ),
			),
			'std' => 'fade',
			'cols' => 2,
			'show_if' => array( 'shape', '!=', 'none' ),
			'group' => us_translate( 'Appearance' ),
		),
		'gap' => array(
			'title' => __( 'Gap between Icons', 'us' ),
			'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">2px</span>, <span class="usof-example">0.1em</span>',
			'type' => 'text',
			'std' => '0',
			'group' => us_translate( 'Appearance' ),
		),
		'hide_tooltip' => array(
			'type' => 'switch',
			'switch_text' => __( 'Hide tooltip on hover', 'us' ),
			'std' => FALSE,
			'group' => us_translate( 'Appearance' ),
			'context' => array( 'shortcode' )
		),

	), $design_options ),
	'deprecated_params' => array(
		'color',
		'align',
	),
);
