<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: vc_tta_section
 */

$design_options_params = us_config( 'elements_design_options' );

/**
 * General section
 *
 * @var array
 */
$general_params = array(
	'title' => array(
		'title' => us_translate( 'Title' ),
		'type' => 'text',
		'std' => 'Tab 1',
		'usb_preview' => TRUE,
	),
	'tab_link' => array(
		'title' => us_translate( 'Link' ),
		'type' => 'link',
		'std' => '',
		'usb_preview' => TRUE,
	),
	'active' => array(
		'switch_text' => __( 'Show this section open', 'us' ),
		'type' => 'switch',
		'std' => FALSE,
	),
	'indents' => array(
		'switch_text' => __( 'Stretch this section content to the full available area', 'us' ),
		'type' => 'switch',
		'std' => '',
		'usb_preview' => array(
			'toggle_class' => 'no_indents',
		),
	),
	'icon' => array(
		'title' => __( 'Icon', 'us' ),
		'type' => 'icon',
		'std' => '',
		'usb_preview' => TRUE,
	),
	'i_position' => array(
		'title' => __( 'Icon Position', 'us' ),
		'type' => 'radio',
		'options' => array(
			'left' => __( 'Before title', 'us' ),
			'right' => __( 'After title', 'us' ),
		),
		'std' => 'left',
		'usb_preview' => TRUE,
	),
	'bg_color' => array(
		'title' => __( 'Background Color', 'us' ),
		'type' => 'color',
		'clear_pos' => 'right',
		'cols' => 2,
		'std' => '',
		'usb_preview' => array(
			'css' => 'background',
		),
	),
	'text_color' => array(
		'title' => __( 'Text Color', 'us' ),
		'type' => 'color',
		'clear_pos' => 'right',
		'cols' => 2,
		'std' => '',
		'usb_preview' => array(
			'css' => 'color',
		),
	),
);

/**
 * @return array
 */
return array(
	'title' => __( 'Section', 'us' ),
	'category' => __( 'Containers', 'us' ),
	'is_container' => TRUE,
	'hide_on_adding_list' => TRUE,
	'as_child' => array(
		'only' => 'vc_tta_tour,vc_tta_tabs,vc_tta_accordion',
	),
	'params' => us_set_params_weight(
		$general_params,
		$design_options_params
	),

	// Default VC params which are not supported by the theme
	'vc_remove_params' => array(
		'add_icon',
		'i_icon_entypo',
		'i_icon_fontawesome',
		'i_icon_linecons',
		'i_icon_material',
		'i_icon_monosocial',
		'i_icon_openiconic',
		'i_icon_typicons',
		'i_type',
	),
	'fallback_params' => array(
		'tab_id',
	),
);
