<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: vc_tta_tour
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

$general_params = array(

	// Tabs
	'layout' => array(
		'title' => us_translate( 'Style' ),
		'type' => 'select',
		'options' => array(
			'default' => __( 'Simple', 'us' ),
			'modern' => __( 'Modern', 'us' ),
			'trendy' => __( 'Trendy', 'us' ),
		),
		'std' => 'default',
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'mod' => 'style',
		),
	),
	'switch_sections' => array(
		'title' => __( 'Switch sections', 'us' ),
		'type' => 'radio',
		'options' => array(
			'click' => __( 'On click', 'us' ),
			'hover' => __( 'On hover', 'us' ),
		),
		'std' => 'click',
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => TRUE,
	),
	'tab_position' => array(
		'title' => __( 'Position', 'us' ),
		'type' => 'radio',
		'options' => array(
			'left' => is_rtl() ? us_translate( 'Right' ) : us_translate( 'Left' ),
			'right' => is_rtl() ? us_translate( 'Left' ) : us_translate( 'Right' ),
		),
		'std' => 'left',
		'cols' => 3,
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'mod' => 'navpos',
		),
	),
	'c_align' => array(
		'title' => us_translate( 'Alignment' ),
		'type' => 'radio',
		'labels_as_icons' => 'fas fa-align-*',
		'options' => array(
			'none' => us_translate( 'Default' ),
			'left' => us_translate( 'Left', 'us' ),
			'center' => us_translate( 'Center', 'us' ),
			'right' => us_translate( 'Right', 'us' ),
		),
		'std' => 'none',
		'cols' => 3,
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'elm' => '> .w-tabs-list',
			'mod' => 'align',
		),
	),
	'controls_size' => array(
		'title' => us_translate( 'Width' ),
		'type' => 'select',
		'options' => array(
			'auto' => us_translate( 'Auto' ),
			'10' => '10%',
			'20' => '20%',
			'30' => '30%',
			'40' => '40%',
			'50' => '50%',
		),
		'std' => 'auto',
		'cols' => 3,
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'mod' => 'navwidth',
		),
	),
	'title_font' => array(
		'title' => __( 'Font', 'us' ),
		'type' => 'select',
		'options' => us_get_fonts(),
		'std' => '',
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'elm' => '> .w-tabs-list',
			'css' => 'font-family',
		),
	),
	'title_weight' => array(
		'title' => __( 'Font Weight', 'us' ),
		'type' => 'select',
		'options' => array(
			'' => us_translate( 'Default' ),
			'100' => '100 ' . __( 'thin', 'us' ),
			'200' => '200 ' . __( 'extra-light', 'us' ),
			'300' => '300 ' . __( 'light', 'us' ),
			'400' => '400 ' . __( 'normal', 'us' ),
			'500' => '500 ' . __( 'medium', 'us' ),
			'600' => '600 ' . __( 'semi-bold', 'us' ),
			'700' => '700 ' . __( 'bold', 'us' ),
			'800' => '800 ' . __( 'extra-bold', 'us' ),
			'900' => '900 ' . __( 'ultra-bold', 'us' ),
		),
		'std' => '',
		'cols' => 2,
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'elm' => '> .w-tabs-list',
			'css' => 'font-weight',
		),
	),
	'title_transform' => array(
		'title' => __( 'Text Transform', 'us' ),
		'type' => 'select',
		'options' => array(
			'' => us_translate( 'Default' ),
			'none' => us_translate( 'None' ),
			'uppercase' => 'UPPERCASE',
			'lowercase' => 'lowercase',
			'capitalize' => 'Capitalize',
		),
		'std' => '',
		'cols' => 2,
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'elm' => '> .w-tabs-list',
			'css' => 'text-transform',
		),
	),
	'title_size' => array(
		'title' => __( 'Font Size', 'us' ),
		'description' => $misc['desc_font_size'],
		'type' => 'text',
		'std' => '1em',
		'cols' => 2,
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'css' => '--sections-title-size',
		),
	),
	'title_lineheight' => array(
		'title' => __( 'Line height', 'us' ),
		'description' => us_arr_path( $misc, 'desc_line_height', '' ),
		'type' => 'text',
		'cols' => 2,
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'elm' => '> .w-tabs-list',
			'css' => 'line-height',
		),
	),

	// Accordion
	'accordion_at_width' => array(
		'title' => __( 'Transform to Accordion at the screen width', 'us' ),
		'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">300px</span>, <span class="usof-example">768px</span>. ' . __( 'Leave empty to transform automatically based on the total width of the tabs.', 'us' ),
		'type' => 'text',
		'std' => '',
		'group' => us_translate( 'Accordion', 'js_composer' ),
		'usb_preview' => TRUE,
	),
);

// Copy the parameters from vc_tta_accordion
$copy_params = array(
	'scrolling',
	'remove_indents',
	'c_align',
	'title_tag',
	'c_icon',
	'c_position',
);
$accordion_params = us_config( 'elements/vc_tta_accordion.params', array() );
foreach ( $copy_params as $param_name ) {
	if ( ! empty( $accordion_params[ $param_name ] ) ) {

		// Remove weight for correct order
		unset( $accordion_params[ $param_name ]['weight'] );

		// Add Accordion group name
		$accordion_params[ $param_name ]['group'] = us_translate( 'Accordion', 'js_composer' );

		$general_params[ $param_name ] = $accordion_params[ $param_name ];
	}
}

/**
 * @return array
 */
return array(
	'title' => __( 'Vertical Tabs', 'us' ),
	'category' => __( 'Containers', 'us' ),
	'icon' => 'fas fa-folder-plus',
	'is_container' => TRUE,
	'weight' => 350, // go after Tabs element, which has "360" weight
	'as_child' => array(
		'except' => 'vc_tta_section',
	),
	'as_parent' => array(
		'only' => 'vc_tta_section',
	),
	'params' => us_set_params_weight(
		$general_params,
		$design_options_params
	),

	// Default VC params which are not supported by the theme
	'vc_remove_params' => array(
		'active_section',
		'alignment',
		'autoplay',
		'color',
		'css_animation',
		'gap',
		'no_fill_content_area',
		'pagination_color',
		'pagination_style',
		'shape',
		'spacing',
		'style',
		'title',
	),

	'usb_init_js' => '$elm.wTabs()',
);
