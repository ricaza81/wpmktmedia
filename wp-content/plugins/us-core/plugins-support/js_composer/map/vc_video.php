<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Modifying shortcode: vc_video
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config ['atts'] array Shortcode's attributes and default values
 */

$misc = us_config( 'elements_misc' );

vc_remove_param( 'vc_video', 'title' );
vc_remove_param( 'vc_video', 'el_width' );
vc_remove_param( 'vc_video', 'el_aspect' );
vc_remove_param( 'vc_video', 'css_animation' );

vc_update_shortcode_param(
	'vc_video', array(
		'param_name' => 'el_class',
		'description' => '',
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 10,
		'group' => __( 'Design', 'us' ),
	)
);
vc_update_shortcode_param(
	'vc_video', array(
		'param_name' => 'el_id',
		'description' => '',
		'edit_field_class' => 'vc_col-sm-6',
		'group' => __( 'Design', 'us' ),
	)
);

vc_update_shortcode_param(
	'vc_video', array(
		'param_name' => 'css',
		'type' => 'us_design_options',
		'heading' => '',
		'params' => us_config( 'elements_design_options.css.params', array() ),
		'group' => __( 'Design', 'us' ),
	)
);

// Get "oEmbed" field types from "Advanced Custom Fields" plugin
$with_acf = FALSE;
if ( function_exists( 'acf_get_field_groups' ) AND $acf_groups = acf_get_field_groups() ) {
	$acf_custom_fields = array();

	foreach ( $acf_groups as $group ) {
		$fields = acf_get_fields( $group['ID'] );
		foreach ( $fields as $field ) {

			// Get ACF 'oembed' type fields
			if ( in_array( $field['type'], array( 'oembed' ) ) ) {
				$acf_custom_fields[ $field['name'] ] = $group['title'] . ': ' . $field['label'];
			}
		}
	}

	if ( ! empty( $acf_custom_fields ) ) {
		$with_acf = TRUE;
		$acf_video_param = array(
			array(
				'param_name' => 'source',
				'heading' => us_translate( 'Show' ),
				'type' => 'dropdown',
				'value' => array_merge(
					array_flip( $acf_custom_fields ),
					array( __( 'Custom', 'us' ) => 'custom' )
				),
				'std' => 'custom',
				'admin_label' => TRUE,
				'weight' => 65,
			),
		);
	}
}

$params = array(
	array(
		'param_name' => 'link',
		'heading' => __( 'Video link', 'us' ),
		'description' => sprintf( __( 'Check supported formats on %s', 'us' ), '<a href="https://wordpress.org/support/article/embeds/" target="_blank" rel="noopener">WordPress Codex</a>' ),
		'type' => 'textfield',
		'std' => $config['atts']['link'],
		'admin_label' => TRUE,
		'weight' => 60,
		'dependency' => $with_acf ? array( 'element' => 'source', 'value' => 'custom' ) : array(),
	),
	array(
		'param_name' => 'hide_controls',
		'type' => 'checkbox',
		'value' => array( __( 'Hide YouTube controls while watching', 'us' ) => TRUE ),
		( ( $config['atts']['hide_controls'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['hide_controls'],
		'weight' => 52,
	),
	array(
		'param_name' => 'hide_video_title',
		'type' => 'checkbox',
		'value' => array( __( 'Hide Vimeo video title (only if the owner allows)', 'us' ) => TRUE ),
		( ( $config['atts']['hide_video_title'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['hide_video_title'],
		'weight' => 53,
	),
	array(
		'param_name' => 'ratio',
		'heading' => __( 'Aspect Ratio', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			'21:9' => '21x9',
			'16:9' => '16x9',
			'4:3' => '4x3',
			'3:2' => '3x2',
			'1:1' => '1x1',
		),
		'std' => $config['atts']['ratio'],
		'weight' => 50,
	),
	array(
		'param_name' => 'align',
		'heading' => __( 'Video Alignment', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			us_translate( 'Left' ) => 'left',
			us_translate( 'Center' ) => 'center',
			us_translate( 'Right' ) => 'right',
		),
		'std' => $config['atts']['align'],
		'weight' => 30,
	),

	// Image Overlay
	array(
		'param_name' => 'overlay_image',
		'heading' => __( 'Image Overlay', 'us' ),
		'type' => 'attach_image',
		'std' => $config['atts']['overlay_image'],
		'weight' => 28,
	),
	array(
		'param_name' => 'overlay_icon',
		'type' => 'checkbox',
		'value' => array( __( 'Show Play icon', 'us' ) => TRUE ),
		( ( $config['atts']['overlay_icon'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['overlay_icon'],
		'weight' => 26,
		'dependency' => array( 'element' => 'overlay_image', 'not_empty' => TRUE ),
	),
	array(
		'param_name' => 'overlay_icon_bg_color',
		'heading' => __( 'Background Color', 'us' ),
		'type' => 'us_color',
		'std' => $config['atts']['overlay_icon_bg_color'],
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 24,
		'dependency' => array( 'element' => 'overlay_icon', 'not_empty' => TRUE ),
	),
	array(
		'param_name' => 'overlay_icon_text_color',
		'heading' => __( 'Icon Color', 'us' ),
		'type' => 'us_color',
		'with_gradient' => FALSE,
		'std' => $config['atts']['overlay_icon_text_color'],
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 22,
		'dependency' => array( 'element' => 'overlay_icon', 'not_empty' => TRUE ),
	),
	array(
		'param_name' => 'overlay_icon_size',
		'heading' => __( 'Icon Size', 'us' ),
		'description' => $misc['desc_font_size'],
		'type' => 'textfield',
		'std' => $config['atts']['overlay_icon_size'],
		'weight' => 22,
		'dependency' => array( 'element' => 'overlay_icon', 'not_empty' => TRUE ),
	),
);

if ( ! empty( $acf_video_param ) ) {
	$params = array_merge( $params, $acf_video_param );
}

vc_add_params(
	'vc_video', $params
);
