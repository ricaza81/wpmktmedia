<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: additional_menu
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );
$nav_menus = us_get_nav_menus();

/**
 * @return array
 */
return array(
	'title' => __( 'Simple Menu', 'us' ),
	'icon' => 'fas fa-bars',
	'params' => us_set_params_weight(

		// General section
		array(
			'source' => array(
				'title' => us_translate( 'Menu' ),
				'description' => $misc['desc_menu_select'],
				'type' => 'select',
				'options' => $nav_menus,
				'std' => key( $nav_menus ),
				'admin_label' => TRUE,
				'usb_preview' => TRUE,
				'usb_default_value' => TRUE,
			),
			'layout' => array(
				'title' => __( 'Layout', 'us' ),
				'type' => 'radio',
				'options' => array(
					'ver' => __( 'Vertical', 'us' ),
					'hor' => __( 'Horizontal', 'us' ),
				),
				'std' => 'ver',
				'admin_label' => TRUE,
				'context' => array( 'shortcode' ),
				'usb_preview' => TRUE,
			),
			'spread' => array(
				'type' => 'switch',
				'switch_text' => __( 'Spread menu items evenly over the available width', 'us' ),
				'std' => FALSE,
				'classes' => 'for_above',
				'shortcode_show_if' => array( 'layout', '=', 'hor' ),
				'usb_preview' => array(
					'toggle_class' => 'spread',
				),
			),
			'responsive_width' => array(
				'title' => __( 'Switch to vertical at screens below', 'us' ),
				'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">600px</span>, <span class="usof-example">768px</span>. ' . __( 'Leave blank to enable horizontal scrolling on small screens.', 'us' ),
				'type' => 'text',
				'std' => '600px',
				'context' => array( 'shortcode' ),
				'show_if' => array( 'layout', '=', 'hor' ),
				'usb_preview' => TRUE,
			),
		),

		// Main items section
		array(
			'main_style' => array(
				'title' => us_translate( 'Style' ),
				'type' => 'radio',
				'options' => array(
					'links' => us_translate( 'Links' ),
					'blocks' => us_translate( 'Blocks' ),
				),
				'std' => 'links',
				'context' => array( 'shortcode' ),
				'group' => _x( 'Main items', 'In menus', 'us' ),
				'usb_preview' => TRUE,
			),
			'main_gap' => array(
				'title' => __( 'Gap between Items', 'us' ),
				'type' => 'slider',
				'std' => '1.5rem',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'rem' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
					'em' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
				'group' => _x( 'Main items', 'In menus', 'us' ),
				'usb_preview' => array(
					'css' => '--main-gap',
				),
			),
			'main_ver_indent' => array(
				'title' => __( 'Vertical Indents', 'us' ),
				'type' => 'slider',
				'std' => '0.8em',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'rem' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
					'em' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
				'show_if' => array( 'main_style', '=', 'blocks' ),
				'context' => array( 'shortcode' ),
				'group' => _x( 'Main items', 'In menus', 'us' ),
				'usb_preview' => array(
					'css' => '--main-ver-indent',
				),
			),
			'main_hor_indent' => array(
				'title' => __( 'Horizontal Indents', 'us' ),
				'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">0.8em</span>, <span class="usof-example">20px</span>',
				'type' => 'slider',
				'std' => '0.8em',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'rem' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
					'em' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
				'show_if' => array( 'main_style', '=', 'blocks' ),
				'context' => array( 'shortcode' ),
				'group' => _x( 'Main items', 'In menus', 'us' ),
				'usb_preview' => array(
					'css' => '--main-hor-indent',
				),
			),
		),

		// Main items color section
		array(
			'main_color_bg' => array(
				'title' => __( 'Menu Item Background', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'std' => 'rgba(0,0,0,0.1)',
				'cols' => 2,
				'context' => array( 'shortcode' ),
				'show_if' => array( 'main_style', '=', 'blocks' ),
				'group' => _x( 'Main items', 'In menus', 'us' ),
				'usb_preview' => TRUE,
			),
			'main_color_text' => array(
				'title' => __( 'Menu Item Text', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => 'inherit',
				'cols' => 2,
				'context' => array( 'shortcode' ),
				'group' => _x( 'Main items', 'In menus', 'us' ),
				'usb_preview' => TRUE,
			),
			'main_color_bg_hover' => array(
				'title' => __( 'Menu Item Background on hover', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'std' => '',
				'cols' => 2,
				'context' => array( 'shortcode' ),
				'show_if' => array( 'main_style', '=', 'blocks' ),
				'group' => _x( 'Main items', 'In menus', 'us' ),
				'usb_preview' => TRUE,
			),
			'main_color_text_hover' => array(
				'title' => __( 'Menu Item Text on hover', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => '',
				'cols' => 2,
				'context' => array( 'shortcode' ),
				'group' => _x( 'Main items', 'In menus', 'us' ),
				'usb_preview' => TRUE,
			),
			'main_color_bg_active' => array(
				'title' => __( 'Active Menu Item Background', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'std' => '',
				'cols' => 2,
				'context' => array( 'shortcode' ),
				'show_if' => array( 'main_style', '=', 'blocks' ),
				'group' => _x( 'Main items', 'In menus', 'us' ),
				'usb_preview' => TRUE,
			),
			'main_color_text_active' => array(
				'title' => __( 'Active Menu Item Text', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => '',
				'cols' => 2,
				'context' => array( 'shortcode' ),
				'group' => _x( 'Main items', 'In menus', 'us' ),
				'usb_preview' => TRUE,
			),
		),

		// Sub items section
		array(
			'sub_items' => array(
				'type' => 'switch',
				'switch_text' => __( 'Show menu sub items', 'us' ),
				'std' => FALSE,
				'context' => array( 'shortcode' ),
				'group' => _x( 'Sub items', 'In menus', 'us' ),
				'usb_preview' => TRUE,
			),
			'sub_gap' => array(
				'title' => __( 'Gap between Items', 'us' ),
				'type' => 'slider',
				'std' => '0px',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'rem' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
					'em' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
				'context' => array( 'shortcode' ),
				'show_if' => array( 'sub_items', '=', '1' ),
				'group' => _x( 'Sub items', 'In menus', 'us' ),
				'usb_preview' => array(
					'css' => '--sub-gap',
				),
			),
		),

		$design_options_params
	),
	'fallback_params' => array(
		'align',
	)
);
