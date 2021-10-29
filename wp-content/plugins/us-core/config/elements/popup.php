<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

// Get Page Blocks
global $pagenow;
$us_page_blocks_list = array();
if ( is_admin() AND
	( wp_doing_ajax() OR in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) ) {
	$us_page_blocks_list = us_get_posts_titles_for( 'us_page_block' );
}

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Popup', 'us' ),
	'description' => __( 'Window appears in the foreground of the page content', 'us' ),
	'icon' => 'fas fa-window-restore',
	'params' => array_merge( array(

		// General
		'use_page_block' => array(
			'title' => __( 'Page Block', 'us' ),
			'type' => 'select',
			'hints_for' => 'us_page_block',
			'options' => us_array_merge(
				array( 'none' => '– ' . us_translate( 'None' ) . ' –' ),
				$us_page_blocks_list
			),
			'std' => 'none',
			'group' => us_translate( 'Content' ),
		),
		'title' => array(
			'title' => us_translate( 'Title' ),
			'type' => 'text',
			'std' => '',
			'holder' => 'div',
			'show_if' => array( 'use_page_block', '=', 'none' ),
			'group' => us_translate( 'Content' ),
		),
		'content' => array(
			'type' => 'editor',
			'std' => __( 'This content will appear inside a popup...', 'us' ),
			'show_if' => array( 'use_page_block', '=', 'none' ),
			'group' => us_translate( 'Content' ),
		),

		// Trigger
		'show_on' => array(
			'title' => __( 'Show Popup on', 'us' ),
			'type' => 'select',
			'options' => array(
				'btn' => __( 'Button click', 'us' ),
				'image' => __( 'Image click', 'us' ),
				'icon' => __( 'Icon click', 'us' ),
				'selector' => __( 'Custom element click', 'us' ),
				'load' => __( 'Page load', 'us' ),
			),
			'std' => 'btn',
			'group' => __( 'Trigger', 'us' ),
		),
		'btn_label' => array(
			'title' => __( 'Button Label', 'us' ),
			'type' => 'text',
			'std' => __( 'Click Me', 'us' ),
			'cols' => 2,
			'admin_label' => TRUE,
			'show_if' => array( 'show_on', '=', 'btn' ),
			'group' => __( 'Trigger', 'us' ),
		),
		'btn_size' => array(
			'title' => __( 'Button Size', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '',
			'cols' => 2,
			'show_if' => array( 'show_on', '=', 'btn' ),
			'group' => __( 'Trigger', 'us' ),
		),
		'btn_style' => array(
			'title' => __( 'Button Style', 'us' ),
			'description' => $misc['desc_btn_styles'],
			'type' => 'select',
			'options' => us_get_btn_styles(),
			'std' => '1',
			'show_if' => array( 'show_on', '=', 'btn' ),
			'group' => __( 'Trigger', 'us' ),
		),
		'image' => array(
			'title' => us_translate( 'Image' ),
			'type' => 'upload',
			'cols' => 2,
			'show_if' => array( 'show_on', '=', 'image' ),
			'group' => __( 'Trigger', 'us' ),
		),
		'image_size' => array(
			'title' => __( 'Image Size', 'us' ),
			'description' => $misc['desc_img_sizes'],
			'type' => 'select',
			'options' => us_get_image_sizes_list(),
			'std' => 'large',
			'cols' => 2,
			'show_if' => array( 'show_on', '=', 'image' ),
			'group' => __( 'Trigger', 'us' ),
		),
		'align' => array(
			'title' => us_translate( 'Alignment' ),
			'type' => 'select',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'center' => us_translate( 'Center' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'left',
			'show_if' => array( 'show_on', '=', array( 'btn', 'image', 'icon' ) ),
			'context' => array( 'shortcode' ),
			'group' => __( 'Trigger', 'us' ),
		),
		'btn_icon' => array(
			'title' => __( 'Icon', 'us' ),
			'type' => 'icon',
			'std' => '',
			'show_if' => array( 'show_on', '=', array( 'btn', 'icon' ) ),
			'group' => __( 'Trigger', 'us' ),
		),
		'btn_iconpos' => array(
			'title' => __( 'Icon Position', 'us' ),
			'type' => 'radio',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'left',
			'show_if' => array( 'show_on', '=', 'btn' ),
			'group' => __( 'Trigger', 'us' ),
		),
		'trigger_selector' => array(
			'title' => __( 'Custom element CSS selector', 'us' ),
			'description' => __( 'Use class or ID.', 'us' ) . ' ' . __( 'Examples:', 'us' ) . ' <span class="usof-example">.my-element</span>, <span class="usof-example">#my-element</span>',
			'type' => 'text',
			'std' => '.my-element',
			'show_if' => array( 'show_on', '=', 'selector' ),
			'group' => __( 'Trigger', 'us' ),
		),
		'show_delay' => array(
			'title' => __( 'Delay after page load (in seconds)', 'us' ),
			'type' => 'text',
			'std' => '2',
			'show_if' => array( 'show_on', '=', 'load' ),
			'group' => __( 'Trigger', 'us' ),
		),

		// Style
		'popup_width' => array(
			'title' => __( 'Popup Width', 'us' ),
			'description' => $misc['desc_width'],
			'type' => 'text',
			'std' => '600px',
			'group' => us_translate( 'Style' ),
		),
		'popup_padding' => array(
			'title' => __( 'Popup Padding', 'us' ),
			'description' => $misc['desc_padding'],
			'type' => 'text',
			'std' => '5%',
			'cols' => 2,
			'group' => us_translate( 'Style' ),
		),
		'popup_border_radius' => array(
			'title' => __( 'Popup Corners Radius', 'us' ),
			'description' => $misc['desc_border_radius'],
			'type' => 'text',
			'std' => '',
			'cols' => 2,
			'group' => us_translate( 'Style' ),
		),
		'title_bgcolor' => array(
			'title' => __( 'Title Background Color', 'us' ),
			'type' => 'color',
			'std' => '_content_bg_alt',
			'cols' => 2,
			'show_if' => array( 'use_page_block', '=', 'none' ),
			'group' => us_translate( 'Style' ),
		),
		'title_textcolor' => array(
			'title' => __( 'Title Text Color', 'us' ),
			'type' => 'color',
			'with_gradient' => FALSE,
			'std' => '_content_heading',
			'cols' => 2,
			'show_if' => array( 'use_page_block', '=', 'none' ),
			'group' => us_translate( 'Style' ),
		),
		'content_bgcolor' => array(
			'title' => __( 'Popup Background Color', 'us' ),
			'type' => 'color',
			'std' => '_content_bg',
			'cols' => 2,
			'group' => us_translate( 'Style' ),
		),
		'content_textcolor' => array(
			'title' => __( 'Popup Text Color', 'us' ),
			'type' => 'color',
			'with_gradient' => FALSE,
			'std' => '_content_text',
			'cols' => 2,
			'group' => us_translate( 'Style' ),
		),
		'overlay_bgcolor' => array(
			'title' => __( 'Background Overlay', 'us' ),
			'type' => 'color',
			'std' => 'rgba(0,0,0,0.85)',
			'group' => us_translate( 'Style' ),
		),
		'animation' => array(
			'title' => __( 'Animation Type', 'us' ),
			'type' => 'select',
			'options' => array(
				'fadeIn' => __( 'Fade', 'us' ),
				'scaleUp' => __( 'Scale Up', 'us' ),
				'scaleDown' => __( 'Scale Down', 'us' ),
				'slideTop' => __( 'Slide from the Top', 'us' ),
				'slideBottom' => __( 'Slide from the Bottom', 'us' ),
				'flipHor' => __( '3D Flip', 'us' ) . ' (' . __( 'Horizontal', 'us' ) . ')',
				'flipVer' => __( '3D Flip', 'us' ) . ' (' . __( 'Vertical', 'us' ) . ')',
			),
			'std' => 'fadeIn',
			'group' => us_translate( 'Style' ),
		),

	), $design_options ),
);
