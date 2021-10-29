<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Simple Menu', 'us' ),
	'icon' => 'fas fa-bars',
	'params' => array_merge( array(

		'source' => array(
			'title' => us_translate( 'Menu' ),
			'description' => $misc['desc_menu_select'],
			'type' => 'select',
			'options' => us_get_nav_menus(),
			'std' => '',
			'admin_label' => TRUE,
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
		),
		'spread' => array(
			'type' => 'switch',
			'switch_text' => __( 'Spread menu items evenly over the available width', 'us' ),
			'std' => FALSE,
			'classes' => 'for_above',
			'shortcode_show_if' => array( 'layout', '=', 'hor' ),
		),
		'responsive_width' => array(
			'title' => __( 'Switch to vertical at screens below', 'us' ),
			'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">600px</span>, <span class="usof-example">768px</span>. ' . __( 'Leave blank to enable horizontal scrolling on small screens.', 'us' ),
			'type' => 'text',
			'std' => '600px',
			'context' => array( 'shortcode' ),
			'show_if' => array( 'layout', '=', 'hor' ),
		),

		// Main items
		'main_style' => array(
			'title' => us_translate( 'Style' ),
			'type' => 'select',
			'options' => array(
				'links' => us_translate( 'Links' ),
				'blocks' => us_translate( 'Blocks' ),
			),
			'std' => 'links',
			'context' => array( 'shortcode' ),
			'group' => _x( 'Main items', 'In menus', 'us' ),
		),
		'main_gap' => array(
			'title' => __( 'Gap between Items', 'us' ),
			'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">0</span>, <span class="usof-example">5px</span>, <span class="usof-example">0.5rem</span>, <span class="usof-example">1.5rem</span>',
			'type' => 'text',
			'std' => '1.5rem',
			'group' => _x( 'Main items', 'In menus', 'us' ),
		),
		'main_ver_indent' => array(
			'title' => __( 'Vertical Indents', 'us' ),
			'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">0.8em</span>, <span class="usof-example">10px</span>',
			'type' => 'text',
			'std' => '0.8em',
			'cols' => 2,
			'show_if' => array( 'main_style', '=', 'blocks' ),
			'context' => array( 'shortcode' ),
			'group' => _x( 'Main items', 'In menus', 'us' ),
		),
		'main_hor_indent' => array(
			'title' => __( 'Horizontal Indents', 'us' ),
			'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">0.8em</span>, <span class="usof-example">20px</span>',
			'type' => 'text',
			'std' => '0.8em',
			'cols' => 2,
			'show_if' => array( 'main_style', '=', 'blocks' ),
			'context' => array( 'shortcode' ),
			'group' => _x( 'Main items', 'In menus', 'us' ),
		),

		// Main items Colors
		'main_color_bg' => array(
			'title' => __( 'Menu Item Background', 'us' ),
			'type' => 'color',
			'clear_pos' => 'left',
			'std' => 'rgba(0,0,0,0.1)',
			'cols' => 2,
			'context' => array( 'shortcode' ),
			'show_if' => array( 'main_style', '=', 'blocks' ),
			'group' => _x( 'Main items', 'In menus', 'us' ),
		),
		'main_color_text' => array(
			'title' => __( 'Menu Item Text', 'us' ),
			'type' => 'color',
			'clear_pos' => 'left',
			'with_gradient' => FALSE,
			'std' => 'inherit',
			'cols' => 2,
			'context' => array( 'shortcode' ),
			'group' => _x( 'Main items', 'In menus', 'us' ),
		),
		'main_color_bg_hover' => array(
			'title' => __( 'Menu Item Background on hover', 'us' ),
			'type' => 'color',
			'clear_pos' => 'left',
			'std' => '',
			'cols' => 2,
			'context' => array( 'shortcode' ),
			'show_if' => array( 'main_style', '=', 'blocks' ),
			'group' => _x( 'Main items', 'In menus', 'us' ),
		),
		'main_color_text_hover' => array(
			'title' => __( 'Menu Item Text on hover', 'us' ),
			'type' => 'color',
			'clear_pos' => 'left',
			'with_gradient' => FALSE,
			'std' => '',
			'cols' => 2,
			'context' => array( 'shortcode' ),
			'group' => _x( 'Main items', 'In menus', 'us' ),
		),
		'main_color_bg_active' => array(
			'title' => __( 'Active Menu Item Background', 'us' ),
			'type' => 'color',
			'clear_pos' => 'left',
			'std' => '',
			'cols' => 2,
			'context' => array( 'shortcode' ),
			'show_if' => array( 'main_style', '=', 'blocks' ),
			'group' => _x( 'Main items', 'In menus', 'us' ),
		),
		'main_color_text_active' => array(
			'title' => __( 'Active Menu Item Text', 'us' ),
			'type' => 'color',
			'clear_pos' => 'left',
			'with_gradient' => FALSE,
			'std' => '',
			'cols' => 2,
			'context' => array( 'shortcode' ),
			'group' => _x( 'Main items', 'In menus', 'us' ),
		),

		// Sub items
		'sub_items' => array(
			'type' => 'switch',
			'switch_text' => __( 'Show menu sub items', 'us' ),
			'std' => FALSE,
			'context' => array( 'shortcode' ),
			'group' => _x( 'Sub items', 'In menus', 'us' ),
		),
		'sub_gap' => array(
			'title' => __( 'Gap between Items', 'us' ),
			'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">10px</span>, <span class="usof-example">0.5rem</span>',
			'type' => 'text',
			'std' => '',
			'context' => array( 'shortcode' ),
			'show_if' => array( 'sub_items', '=', '1' ),
			'group' => _x( 'Sub items', 'In menus', 'us' ),
		),

	), $design_options ),
	'deprecated_params' => array(
		'align',
	)
);
