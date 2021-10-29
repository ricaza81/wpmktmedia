<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: vc_video
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

/**
 * ACF Video section
 *
 * @var array
 */
$acf_video_param = array();
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
		$acf_video_param = array(
			'source' => array(
				'title' => us_translate( 'Show' ),
				'type' => 'select',
				'options' => array_merge(
					$acf_custom_fields,
					array( 'custom' => __( 'Custom', 'us' ) )
				),
				'std' => 'custom',
				'admin_label' => TRUE,
				'usb_preview' => TRUE,
			),
		);
	}
}

/**
 * General section
 *
 * @var array
 */
$general_params = array(
	'link' => array(
		'title' => __( 'Video link', 'us' ),
		'description' => sprintf( __( 'Check supported formats on %s', 'us' ), '<a href="https://wordpress.org/support/article/embeds/" target="_blank" rel="noopener">WordPress Codex</a>' ),
		'type' => 'text',
		'std' => 'https://youtu.be/XuWr9gJa6P0',
		'admin_label' => TRUE,
		'show_if' => ! empty( $acf_video_param ) ? array( 'source', '=', 'custom' ) : array(),
		'usb_preview' => TRUE,
	),
	'hide_controls' => array(
		'switch_text' => __( 'Hide YouTube controls while watching', 'us' ),
		'type' => 'switch',
		'std' => FALSE,
		'usb_preview' => TRUE,
	),
	'hide_video_title' => array(
		'switch_text' => __( 'Hide Vimeo video title (only if the owner allows)', 'us' ),
		'type' => 'switch',
		'std' => FALSE,
		'usb_preview' => TRUE,
	),
	'ratio' => array(
		'title' => __( 'Aspect Ratio', 'us' ),
		'type' => 'select',
		'options' => array(
			'21x9' => '21:9',
			'16x9' => '16:9',
			'4x3' => '4:3',
			'3x2' => '3:2',
			'1x1' => '1:1',
		),
		'std' => '16x9',
		'usb_preview' => TRUE,
	),
	'align' => array(
		'title' => __( 'Video Alignment', 'us' ),
		'type' => 'radio',
		'labels_as_icons' => 'fas fa-align-*',
		'options' => array(
			'none' => us_translate( 'Default' ),
			'left' => us_translate( 'Left' ),
			'center' => us_translate( 'Center' ),
			'right' => us_translate( 'Right' ),
		),
		'std' => 'none',
		'usb_preview' => array(
			'mod' => 'align',
		),
	),
	'overlay_image' => array(
		'title' => __( 'Image Overlay', 'us' ),
		'type' => 'upload',
		'std' => '',
		'usb_preview' => TRUE,
	),
	'overlay_icon' => array(
		'switch_text' => __( 'Show Play icon', 'us' ),
		'type' => 'switch',
		'std' => TRUE,
		'show_if' => array( 'overlay_image', '!=', '' ),
		'usb_preview' => TRUE,
	),
	'overlay_icon_bg_color' => array(
		'title' => __( 'Background Color', 'us' ),
		'type' => 'color',
		'clear_pos' => 'right',
		'std' => 'rgba(0,0,0,0.5)',
		'cols' => 2,
		'show_if' => array( 'overlay_icon', '!=', '' ),
		'usb_preview' => array(
			'elm' => '.w-video-icon',
			'css' => 'background',
		),
	),
	'overlay_icon_text_color' => array(
		'title' => __( 'Icon Color', 'us' ),
		'type' => 'color',
		'clear_pos' => 'right',
		'std' => '#fff',
		'cols' => 2,
		'show_if' => array( 'overlay_icon', '!=', '' ),
		'usb_preview' => array(
			'elm' => '.w-video-icon',
			'css' => 'color',
		),
	),
	'overlay_icon_size' => array(
		'title' => __( 'Icon Size', 'us' ),
		'description' => us_arr_path( $misc, 'desc_font_size', '' ),
		'type' => 'text',
		'std' => '1.5rem',
		'show_if' => array( 'overlay_icon', '!=', '' ),
		'usb_preview' => array(
			'elm' => '.w-video-icon',
			'css' => 'font-size',
		),
	),
);

/**
 * @return array
 */
return array(
	'title' => __( 'Video Player', 'us' ),
	'icon' => 'fas fa-play-circle',
	'params' => us_set_params_weight(
		$general_params,
		$acf_video_param,
		$design_options_params
	),

	// Default VC params which are not supported by the theme
	'vc_remove_params' => array(
		'css_animation',
		'el_aspect',
		'el_width',
		'title',
	),

	'usb_init_js' => '$elm.wVideo()',
);
