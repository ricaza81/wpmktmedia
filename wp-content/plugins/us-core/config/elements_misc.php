<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Common variables used in several configs to avoid translation duplications
 */

return array(

	// Columns
	'column_values' => array(
		'10' => sprintf( us_translate_n( '%s column', '%s columns', 10 ), 10 ),
		'9' => sprintf( us_translate_n( '%s column', '%s columns', 9 ), 9 ),
		'8' => sprintf( us_translate_n( '%s column', '%s columns', 8 ), 8 ),
		'7' => sprintf( us_translate_n( '%s column', '%s columns', 7 ), 7 ),
		'6' => sprintf( us_translate_n( '%s column', '%s columns', 6 ), 6 ),
		'5' => sprintf( us_translate_n( '%s column', '%s columns', 5 ), 5 ),
		'4' => sprintf( us_translate_n( '%s column', '%s columns', 4 ), 4 ),
		'3' => sprintf( us_translate_n( '%s column', '%s columns', 3 ), 3 ),
		'2' => sprintf( us_translate_n( '%s column', '%s columns', 2 ), 2 ),
		'1' => sprintf( us_translate_n( '%s column', '%s columns', 1 ), 1 ),
	),

	// Dropdown effects for header
	'dropdown_effect_values' => array(
		'none' => us_translate( 'None' ),
		'opacity' => __( 'Fade', 'us' ),
		'slide' => __( 'SlideDown', 'us' ),
		'height' => __( 'Fade + SlideDown', 'us' ),
		'afb' => __( 'Appear From Bottom', 'us' ),
		'hor' => __( 'Horizontal Slide', 'us' ),
		'mdesign' => __( 'Material Design Effect', 'us' ),
	),

	// HTML tags
	'html_tag_values' => array(
		'h1' => 'h1',
		'h2' => 'h2',
		'h3' => 'h3',
		'h4' => 'h4',
		'h5' => 'h5',
		'h6' => 'h6',
		'div' => 'div',
		'p' => 'p',
		'span' => 'span',
	),

	// Font size examples
	'desc_font_size' => __( 'Examples:', 'us' ) . ' <span class="usof-example">16px</span>, <span class="usof-example">1.2rem</span>, <span class="usof-example">max( 1rem, 1vw )</span>',

	// Line height examples
	'desc_line_height' => __( 'Examples:', 'us' ) . ' <span class="usof-example">28px</span>, <span class="usof-example">1.7</span>',

	// Letter spacing examples
	'desc_letter_spacing' => __( 'Examples:', 'us' ) . ' <span class="usof-example">1px</span>, <span class="usof-example">-0.04em</span>',

	// Integers examples
	'desc_integers' => __( 'Examples:', 'us' ) . ' <span class="usof-example">-1</span>, <span class="usof-example">0</span>, <span class="usof-example">3</span>, <span class="usof-example">111</span>',

	// Height examples
	'desc_height' => __( 'Examples:', 'us' ) . ' <span class="usof-example">200px</span>, <span class="usof-example">15rem</span>, <span class="usof-example">10vh</span>',

	// Width examples
	'desc_width' => __( 'Examples:', 'us' ) . ' <span class="usof-example">200px</span>, <span class="usof-example">100%</span>, <span class="usof-example">14rem</span>, <span class="usof-example">10vw</span>',

	// Padding examples
	'desc_padding' => __( 'Examples:', 'us' ) . ' <span class="usof-example">20px</span>, <span class="usof-example">15%</span>, <span class="usof-example">1rem</span>, <span class="usof-example">2vw</span>',

	// Border radius examples
	'desc_border_radius' => __( 'Examples:', 'us' ) . ' <span class="usof-example">5px</span>, <span class="usof-example">50%</span>, <span class="usof-example">0.2rem</span>',

	// Pixels only
	'desc_pixels' => __( 'In pixels:', 'us' ) . ' <span class="usof-example">32px</span>, <span class="usof-example">64px</span>, <span class="usof-example">128px</span>',

	// Background Position
	'desc_bg_pos' => __( 'Examples:', 'us' ) . ' <span class="usof-example">50%</span>, <span class="usof-example">100px 200px</span>, <span class="usof-example">0 100%</span>, <span class="usof-example">20vw 0</span>',

	// Background Image Size
	'desc_bg_size' => __( 'Examples:', 'us' ) . ' <span class="usof-example">cover</span>, <span class="usof-example">contain</span>, <span class="usof-example">50%</span>, <span class="usof-example">300px 200px</span>',

	// Box Shadow
	'desc_shadow' => __( 'Examples:', 'us' ) . ' <span class="usof-example">0</span>, <span class="usof-example">1px</span>, <span class="usof-example">3px</span>, <span class="usof-example">10px</span>',

	// Menu selection
	'desc_menu_select' => sprintf( __( 'Add or edit a menu on the %s page', 'us' ), '<a href="' . admin_url( 'nav-menus.php' ) . '" target="_blank" rel="noopener">' . us_translate( 'Menus' ) . '</a>' ),

	// Image Sizes
	'desc_img_sizes' => '<a target="_blank" rel="noopener" href="' . admin_url( 'admin.php?page=us-theme-options#image_sizes' ) . '">' . __( 'Edit image sizes', 'us' ) . '</a>.',

	// Button styles
	'desc_btn_styles' => sprintf( __( 'Add or edit Button Styles on %sTheme Options%s', 'us' ), '<a href="' . admin_url( 'admin.php?page=us-theme-options#buttons' ) . '" target="_blank" rel="noopener">', '</a>' ),

	// Custom link for Grid Layout
	'desc_grid_custom_link' => sprintf( __( 'To apply a URL from a custom field, use its name between the %s symbols.', 'us' ), '{{ }}' ) . ' ' . __( 'Examples:', 'us' ) . ' <span class="usof-example">{{custom_field_name}}</span>',

	// Header Description
	'headers_description' => sprintf( __( 'Add or edit a Header on the %s page', 'us' ), '<a href="' . admin_url( 'edit.php?post_type=us_header' ) . '" target="_blank" rel="noopener">' . _x( 'Headers', 'site top area', 'us' ) . '</a>' ),

	// Content Description
	'content_description' => sprintf( __( 'Add or edit a Content Template on the %s page', 'us' ), '<a href="' . admin_url( 'edit.php?post_type=us_content_template' ) . '" target="_blank" rel="noopener">' . __( 'Content templates', 'us' ) . '</a>' ),

	// Footer Description
	'footers_description' => sprintf( __( 'Add or edit a Footer on the %s page', 'us' ), '<a href="' . admin_url( 'edit.php?post_type=us_page_block' ) . '" target="_blank" rel="noopener">' . __( 'Page Blocks', 'us' ) . '</a>' ),

	// Seconds only
	'desc_seconds' => __( 'In seconds:', 'us' ) . ' <span class="usof-example">3</span>, <span class="usof-example">5</span>, <span class="usof-example">8</span>',

	// Milliseconds only
	'desc_milliseconds' => __( 'In milliseconds:', 'us' ) . ' <span class="usof-example">300</span>, <span class="usof-example">600</span>, <span class="usof-example">1000</span>',

);
