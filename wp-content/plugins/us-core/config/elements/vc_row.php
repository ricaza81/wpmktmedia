<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: vs_row
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );
$grid_columns_layout = ( us_get_option( 'live_builder' ) AND us_get_option( 'grid_columns_layout' ) );

// Add option to set Rev Slider as row background
$revslider_params = $revslider_option = array();
if ( class_exists( 'RevSlider' ) ) {
	$revsliders = array();
	$sliders = ( new RevSlider() )->getArrSliders();
	foreach ( $sliders as $slider ) {
		$revsliders[ $slider->getAlias() ] = $slider->getTitle();
	}
	if ( ! empty( $revsliders ) ) {
		$revslider_params['us_bg_rev_slider'] = array(
			'type' => 'select',
			'options' => $revsliders,
			'std' => '',
			'show_if' => array( 'us_bg_show', '=', 'rev_slider' ),
			'usb_preview' => TRUE,
		);
	}
	$revslider_option = array(
		'rev_slider' => us_translate( 'Slider Revolution', 'revslider' )
	);
}

// Configure images sources for Row Background
$img_sources = array(
	'none' => us_translate( 'None' ),
	'media' => __( 'Custom', 'us' ),
	'featured' => us_translate( 'Featured Image' ),
	'us_tile_additional_image' => __( 'Custom appearance in Grid', 'us' ) . ': ' . us_translate( 'Images' ),
);

// Add image types from ACF
if ( function_exists( 'acf_get_field_groups' ) AND $acf_groups = acf_get_field_groups() ) {
	foreach ( $acf_groups as $group ) {
		$fields = acf_get_fields( $group['ID'] );
		foreach ( $fields as $field ) {
			if ( $field['type'] == 'image' ) {
				$img_sources[ $field['name'] ] = $group['title'] . ': ' . $field['label'];
			}
		}
	}
}

// Shape Divider section
$shape_divider_params = array();
foreach ( array( 'top', 'bottom' ) as $pos ) {
	// Filter to determine the first and last element in a row via jQuery filters
	$elm_filter = ( $pos === 'top' ? ':first' : ':last' );
	$shape_divider_params = array_merge( $shape_divider_params, array(
		"us_shape_show_{$pos}" => array(
			'switch_text' => ( $pos === 'top' ) ? __( 'Show at the top', 'us' ) : __( 'Show at the bottom', 'us' ),
			'type' => 'switch',
			'std' => FALSE,
			'group' => __( 'Shape Divider', 'us' ),
			'usb_preview' => TRUE,
		),
		"us_shape_{$pos}" => array(
			'type' => 'imgradio',
			'preview_path' => '/assets/shapes/%s.svg',
			'options' => array(
				'tilt' => __( 'Tilt', 'us' ),
				'curve' => __( 'Curve', 'us' ),
				'curve-inv' => __( 'Curve (inv)', 'us' ),
				'triangle' => __( 'Triangle', 'us' ),
				'triangle-inv' => __( 'Triangle (inv)', 'us' ),
				'triangle-2' => __( 'Triangle 2', 'us' ),
				'triangle-2-inv' => __( 'Triangle 2 (inv)', 'us' ),
				'wave' => __( 'Wave', 'us' ),
				'zigzag' => __( 'Zigzag', 'us' ),
				'custom' => __( 'Custom', 'us' ),
			),
			'std' => 'tilt',
			'classes' => 'us_shape_' . $pos,
			'cols' => 1, // for correct UI in WPBakery
			'show_if' => array( "us_shape_show_{$pos}", '!=', FALSE ),
			'group' => __( 'Shape Divider', 'us' ),
			'usb_preview' => TRUE,
		),
		"us_shape_custom_{$pos}" => array(
			'type' => 'upload',
			'show_if' => array( "us_shape_{$pos}", '=', 'custom' ),
			'group' => __( 'Shape Divider', 'us' ),
			'usb_preview' => TRUE,
		),
		"us_shape_height_{$pos}" => array(
			'title' => us_translate( 'Height' ),
			'type' => 'slider',
			'options' => array(
				'vh' => array(
					'min' => 1,
					'max' => 75,
				),
				'vw' => array(
					'min' => 1,
					'max' => 75,
				),
				'vmax' => array(
					'min' => 1,
					'max' => 75,
				),
				'vmin' => array(
					'min' => 1,
					'max' => 75,
				),
				'%' => array(
					'min' => 1,
					'max' => 75,
				),
			),
			'std' => '15vh',
			'cols' => 2,
			'show_if' => array( "us_shape_show_{$pos}", '!=', FALSE ),
			'group' => __( 'Shape Divider', 'us' ),
			'usb_preview' => array(
				'elm' => ".l-section-shape{$elm_filter}",
				'css' => 'height',
			),
		),
		"us_shape_color_{$pos}" => array(
			'title' => us_translate( 'Color' ),
			'type' => 'color',
			'clear_pos' => 'right',
			'std' => '_content_bg',
			'with_gradient' => FALSE,
			'cols' => 2,
			'show_if' => array( "us_shape_show_{$pos}", '!=', FALSE ),
			'group' => __( 'Shape Divider', 'us' ),
			'usb_preview' => array(
				'elm' => ".l-section-shape{$elm_filter}",
				'css' => 'color',
			),
		),
		"us_shape_overlap_{$pos}" => array(
			'switch_text' => __( 'Overlap the content of this Row', 'us' ),
			'type' => 'switch',
			'std' => FALSE,
			'show_if' => array( "us_shape_show_{$pos}", '!=', FALSE ),
			'group' => __( 'Shape Divider', 'us' ),
			'usb_preview' => array(
				'elm' => ".l-section-shape{$elm_filter}",
				'toggle_class' => 'on_front',
			),
		),
		"us_shape_flip_{$pos}" => array(
			'switch_text' => __( 'Flip horizontally', 'us' ),
			'type' => 'switch',
			'std' => FALSE,
			'show_if' => array( "us_shape_show_{$pos}", '!=', FALSE ),
			'group' => __( 'Shape Divider', 'us' ),
			'usb_preview' => array(
				'elm' => ".l-section-shape{$elm_filter}",
				'toggle_class' => 'hor_flip',
			),
		),
	) );
	unset( $elm_filter );
}

// Get the default Row height value from Theme Options
$default_height = '';
$default_height_options = us_config( 'theme-options.layout.fields.row_height.options' );
foreach ( $default_height_options as $key => $title ) {
	if ( $key === us_get_option( 'row_height' ) ) {
		if ( $key == 'custom' ) {
			$default_height = us_get_option( 'row_height_custom' );
		} else {
			$default_height = $title;
		}
	}
}

// General
$general_params = array(
	'height' => array(
		'title' => __( 'Vertical Indents', 'us' ),
		'description' => sprintf( __( 'Change the default value in %sTheme Options%s', 'us' ), '<a target="_blank" rel="noopener" href="' . admin_url( 'admin.php?page=us-theme-options#layout' ) . '">', '</a>' ),
		'type' => 'select',
		'options' => array(
			'default' => us_translate( 'Default' ) . ' (' . $default_height . ')',
			'auto' => us_translate( 'None' ),
			'small' => 'S',
			'medium' => 'M',
			'large' => 'L',
			'huge' => 'XL',
		),
		'std' => 'default',
		'usb_preview' => TRUE,
	),
	'full_height' => array(
		'switch_text' => __( 'Full Height Row', 'us' ),
		'type' => 'switch',
		'std' => FALSE,
		'usb_preview' => array(
			'toggle_class' => 'full_height',
		),
	),
	'v_align' => array(
		'title' => __( 'Row Content Position', 'us' ),
		'type' => 'radio',
		'options' => array(
			'top' => us_translate( 'Top' ),
			'center' => us_translate( 'Middle' ),
			'bottom' => us_translate( 'Bottom' ),
		),
		'std' => 'center',
		'show_if' => array( 'full_height', '=', '1' ),
		'usb_preview' => array(
			'mod' => 'valign',
		),
	),
	'width' => array(
		'switch_text' => __( 'Full Width Content', 'us' ),
		'type' => 'switch',
		'std' => FALSE,
		'usb_preview' => array(
			'toggle_class' => 'width_full',
		),
	),
	'color_scheme' => array(
		'title' => __( 'Row Color Style', 'us' ),
		'type' => 'select',
		'options' => array(
			'' => __( 'Content colors', 'us' ),
			'alternate' => __( 'Alternate Content colors', 'us' ),
			'primary' => __( 'Primary bg & White text', 'us' ),
			'secondary' => __( 'Secondary bg & White text', 'us' ),
			'footer-bottom' => __( 'Footer colors', 'us' ),
			'footer-top' => __( 'Alternate Footer colors', 'us' ),
		),
		'std' => '',
		'usb_preview' => array(
			'mod' => 'color',
		),
	),

	// Background Image
	'us_bg_image_source' => array(
		'title' => __( 'Background Image', 'us' ),
		'type' => 'select',
		'options' => $img_sources,
		'std' => 'none',
		'usb_preview' => TRUE,
	),
	'us_bg_image' => array(
		'type' => 'upload',
		'show_if' => array( 'us_bg_image_source', '=', 'media' ),
		'usb_preview' => TRUE,
	),
	'us_bg_size' => array(
		'title' => __( 'Background Image Size', 'us' ),
		'type' => 'radio',
		'options' => array(
			'cover' => __( 'Fill Area', 'us' ),
			'contain' => __( 'Fit to Area', 'us' ),
			'initial' => __( 'Initial', 'us' ),
		),
		'std' => 'cover',
		'show_if' => array( 'us_bg_image_source', '!=', 'none' ),
		'cols' => 2,
		'usb_preview' => array(
			'elm' => '.l-section-img',
			'css' => 'background-size',
		),
	),
	'us_bg_pos' => array(
		'title' => __( 'Background Image Position', 'us' ),
		'type' => 'radio',
		'labels_as_icons' => 'fas fa-arrow-up',
		'options' => array(
			'top left' => us_translate( 'Top Left' ),
			'top center' => us_translate( 'Top' ),
			'top right' => us_translate( 'Top Right' ),
			'center left' => us_translate( 'Left' ),
			'center center' => us_translate( 'Center' ),
			'center right' => us_translate( 'Right' ),
			'bottom left' => us_translate( 'Bottom Left' ),
			'bottom center' => us_translate( 'Bottom' ),
			'bottom right' => us_translate( 'Bottom Right' ),
		),
		'std' => 'center center',
		'classes' => 'bgpos',
		'show_if' => array( 'us_bg_image_source', '!=', 'none' ),
		'cols' => 2,
		'usb_preview' => array(
			'elm' => '.l-section-img',
			'css' => 'background-position',
		),
	),
	'us_bg_repeat' => array(
		'title' => __( 'Background Image Repeat', 'us' ),
		'type' => 'select',
		'options' => array(
			'repeat' => __( 'Repeat', 'us' ),
			'repeat-x' => __( 'Horizontally', 'us' ),
			'repeat-y' => __( 'Vertically', 'us' ),
			'no-repeat' => us_translate( 'None' ),
		),
		'std' => 'repeat',
		'show_if' => array( 'us_bg_image_source', '!=', 'none' ),
		'cols' => 2,
		'usb_preview' => array(
			'elm' => '.l-section-img',
			'css' => 'background-repeat',
		),
	),

	// Parallax
	'us_bg_parallax' => array(
		'title' => __( 'Parallax Effect', 'us' ),
		'type' => 'select',
		'options' => array(
			'' => us_translate( 'None' ),
			'vertical' => __( 'Vertical Parallax', 'us' ),
			'horizontal' => __( 'Horizontal Parallax', 'us' ),
			'still' => __( 'Fixed', 'us' ),
		),
		'std' => '',
		'show_if' => array( 'us_bg_image_source', '!=', 'none' ),
		'cols' => 2,
		'usb_preview' => TRUE,
	),
	'us_bg_parallax_width' => array(
		'title' => __( 'Parallax Background Width', 'us' ),
		'type' => 'radio',
		'options' => array(
			'110' => '110%',
			'120' => '120%',
			'130' => '130%',
			'140' => '140%',
			'150' => '150%',
		),
		'std' => '130',
		'show_if' => array( 'us_bg_parallax', '=', 'horizontal' ),
		'usb_preview' => array(
			'mod' => 'bgwidth'
		),
	),
	'us_bg_parallax_reverse' => array(
		'switch_text' => __( 'Reverse Vertical Parallax Effect', 'us' ),
		'type' => 'switch',
		'std' => FALSE,
		'show_if' => array( 'us_bg_parallax', '=', 'vertical' ),
		'usb_preview' => array(
			'toggle_class' => 'parallaxdir_reversed',
		),
	),
	'us_bg_show' => array(
		'title' => __( 'Show on background', 'us' ),
		'type' => 'select',
		'options' => array_merge(
			array(
				'' => us_translate( 'None' ),
				'video' => us_translate( 'Video' ),
				'img_slider' => __( 'Image Slider', 'us' ),
			),
			$revslider_option
		),
		'std' => '',
		'usb_preview' => TRUE,
	),

	// Video
	'us_bg_video' => array(
		'description' => __( 'Link to YouTube, Vimeo or video file (mp4, webm, ogg)', 'us' ),
		'type' => 'text',
		'std' => '',
		'show_if' => array( 'us_bg_show', '=', 'video' ),
		'usb_preview' => TRUE,
	),
	'us_bg_video_disable_width' => array(
		'title' => __( 'Hide video at width', 'us' ),
		'description' => __( 'When screen width is less than this value, background video will be hidden', 'us' ),
		'type' => 'text',
		'std' => '600px',
		'show_if' => array( 'us_bg_show', '=', 'video' ),
		'usb_preview' => array(
			'elm' => 'l-section-video',
			'attr' => 'data-video-disable-width',
		),
	),

	// Slider
	'us_bg_slider_ids' => array(
		'type' => 'upload',
		'is_multiple' => TRUE,
		'show_if' => array( 'us_bg_show', '=', 'img_slider' ),
		'usb_preview' => TRUE,
	),
	'us_bg_slider_transition' => array(
		'title' => __( 'Transition Effect', 'us' ),
		'type' => 'radio',
		'options' => array(
			'slide' => __( 'Slide', 'us' ),
			'crossfade' => __( 'Fade', 'us' ),
		),
		'std' => 'slide',
		'show_if' => array( 'us_bg_show', '=', 'img_slider' ),
		'cols' => 3,
		'usb_preview' => TRUE,
	),
	'us_bg_slider_speed' => array(
		'title' => __( 'Transition Duration', 'us' ),
		'description' => us_arr_path( $misc, 'desc_milliseconds', '' ),
		'type' => 'text',
		'std' => '1000',
		'show_if' => array( 'us_bg_show', '=', 'img_slider' ),
		'cols' => 3,
		'usb_preview' => TRUE,
	),
	'us_bg_slider_interval' => array(
		'title' => __( 'Auto Rotation Interval', 'us' ),
		'description' => us_arr_path( $misc, 'desc_seconds', '' ),
		'type' => 'text',
		'std' => '3',
		'show_if' => array( 'us_bg_show', '=', 'img_slider' ),
		'cols' => 3,
		'usb_preview' => TRUE,
	),

	// Overlay
	'us_bg_overlay_color' => array(
		'title' => __( 'Background Overlay', 'us' ),
		'type' => 'color',
		'std' => '',
		'usb_preview' => array(
			'elm' => '.l-section-overlay:first',
			'css' => 'background',
		),
	),

	// Sticky Row
	'sticky' => array(
		'title' => __( 'Sticky Row', 'us' ),
		'switch_text' => __( 'Fix this row at the top of a page during scroll', 'us' ),
		'type' => 'switch',
		'std' => FALSE,
		'usb_preview' => array(
			'toggle_class' => 'type_sticky',
		),
	),
);

// Columns
$breakpoints = us_get_responsive_states();
$columns_params = array(
	'columns' => array(
		'title' => $breakpoints['default']['title'],
		'type' => 'custom_dropdown',
		'options' => array(
			'1' => '<i>1</i>',
			'2' => '<i>2</i> <i></i>',
			'3' => '<i>3</i> <i></i> <i></i>',
			'4' => '<i>4</i> <i></i> <i></i> <i></i>',
			'5' => '<i>5</i> <i></i> <i></i> <i></i> <i></i>',
			'6' => '<i>6</i> <i></i> <i></i> <i></i> <i></i> <i></i>',
			'1-5' => '<i>1/6</i> <i>5/6</i>',
			'1-4' => '<i>1/5</i> <i>4/5</i>',
			'1-3' => '<i>1/4</i> <i>3/4</i>',
			'1-2' => '<i>1/3</i> <i>2/3</i>',
			'2-3' => '<i>2/5</i> <i>3/5</i>',
			'3-2' => '<i>3/5</i> <i>2/5</i>',
			'2-1' => '<i>2/3</i> <i>1/3</i>',
			'3-1' => '<i>3/4</i> <i>1/4</i>',
			'4-1' => '<i>4/5</i> <i>1/5</i>',
			'5-1' => '<i>5/6</i> <i>1/6</i>',
			'1-4-1' => '<i>1/6</i> <i>2/3</i> <i>1/6</i>',
			'1-3-1' => '<i>1/5</i> <i>3/5</i> <i>1/5</i>',
			'1-2-1' => '<i>1/4</i> <i>1/2</i> <i>1/4</i>',
			'custom' => '<i>' . __( 'Custom', 'us' ) . '</i>',
		),
		'std' => '1',
		'group' => us_translate( 'Columns' ),
		'usb_preview' => array(
			'elm' => '.l-section-h > .g-cols',
			'mod' => 'cols',
		),
	),
	'columns_layout' => array(
		'title' => __( 'Custom Columns Layout', 'us' ),
		'description' => sprintf( __( 'Use %s values', 'us' ), '<a href="https://developer.mozilla.org/docs/Web/CSS/grid-template-columns" target="_blank">grid-template-columns</a>' ),
		'type' => 'text',
		'std' => '',
		'place_if' => $grid_columns_layout,
		'show_if' => array( 'columns', '=', 'custom' ),
		'group' => us_translate( 'Columns' ),
		'usb_preview' => array(
			'elm' => '.l-section-h > .g-cols',
			'css' => '--custom-columns',
		),
	),
	'laptops_columns' => array(
		'title' => $breakpoints['laptops']['title'],
		'type' => 'custom_dropdown',
		'options' => array(
			'inherit' => '<i>' . __( 'As on Desktops', 'us' ) . '</i>',
			'1' => '<i>1</i>',
			'2' => '<i>2</i> <i></i>',
			'3' => '<i>3</i> <i></i> <i></i>',
			'4' => '<i>4</i> <i></i> <i></i> <i></i>',
			'5' => '<i>5</i> <i></i> <i></i> <i></i> <i></i>',
			'6' => '<i>6</i> <i></i> <i></i> <i></i> <i></i> <i></i>',
			'1-5' => '<i>1/6</i> <i>5/6</i>',
			'1-4' => '<i>1/5</i> <i>4/5</i>',
			'1-3' => '<i>1/4</i> <i>3/4</i>',
			'1-2' => '<i>1/3</i> <i>2/3</i>',
			'2-3' => '<i>2/5</i> <i>3/5</i>',
			'3-2' => '<i>3/5</i> <i>2/5</i>',
			'2-1' => '<i>2/3</i> <i>1/3</i>',
			'3-1' => '<i>3/4</i> <i>1/4</i>',
			'4-1' => '<i>4/5</i> <i>1/5</i>',
			'5-1' => '<i>5/6</i> <i>1/6</i>',
			'1-4-1' => '<i>1/6</i> <i>2/3</i> <i>1/6</i>',
			'1-3-1' => '<i>1/5</i> <i>3/5</i> <i>1/5</i>',
			'1-2-1' => '<i>1/4</i> <i>1/2</i> <i>1/4</i>',
		),
		'std' => 'inherit',
		'place_if' => $grid_columns_layout,
		'group' => us_translate( 'Columns' ),
		'usb_preview' => array(
			'elm' => '.l-section-h > .g-cols',
			'mod' => 'laptops-cols',
		),
	),
	'tablets_columns' => array(
		'title' => $breakpoints['tablets']['title'],
		'type' => 'custom_dropdown',
		'options' => array(
			'inherit' => '<i>' . __( 'As on Desktops', 'us' ) . '</i>',
			'1' => '<i>1</i>',
			'2' => '<i>2</i> <i></i>',
			'3' => '<i>3</i> <i></i> <i></i>',
			'4' => '<i>4</i> <i></i> <i></i> <i></i>',
			'5' => '<i>5</i> <i></i> <i></i> <i></i> <i></i>',
			'6' => '<i>6</i> <i></i> <i></i> <i></i> <i></i> <i></i>',
			'1-5' => '<i>1/6</i> <i>5/6</i>',
			'1-4' => '<i>1/5</i> <i>4/5</i>',
			'1-3' => '<i>1/4</i> <i>3/4</i>',
			'1-2' => '<i>1/3</i> <i>2/3</i>',
			'2-3' => '<i>2/5</i> <i>3/5</i>',
			'3-2' => '<i>3/5</i> <i>2/5</i>',
			'2-1' => '<i>2/3</i> <i>1/3</i>',
			'3-1' => '<i>3/4</i> <i>1/4</i>',
			'4-1' => '<i>4/5</i> <i>1/5</i>',
			'5-1' => '<i>5/6</i> <i>1/6</i>',
			'1-4-1' => '<i>1/6</i> <i>2/3</i> <i>1/6</i>',
			'1-3-1' => '<i>1/5</i> <i>3/5</i> <i>1/5</i>',
			'1-2-1' => '<i>1/4</i> <i>1/2</i> <i>1/4</i>',
		),
		'std' => 'inherit',
		'place_if' => $grid_columns_layout,
		'group' => us_translate( 'Columns' ),
		'usb_preview' => array(
			'elm' => '.l-section-h > .g-cols',
			'mod' => 'tablets-cols',
		),
	),
	'mobiles_columns' => array(
		'title' => $breakpoints['mobiles']['title'],
		'type' => 'custom_dropdown',
		'options' => array(
			'1' => '<i>1</i>',
			'2' => '<i>2</i> <i></i>',
			'3' => '<i>3</i> <i></i> <i></i>',
			'4' => '<i>4</i> <i></i> <i></i> <i></i>',
		),
		'std' => '1',
		'place_if' => $grid_columns_layout,
		'group' => us_translate( 'Columns' ),
		'usb_preview' => array(
			'elm' => '.l-section-h > .g-cols',
			'mod' => 'mobiles-cols',
		),
	),
	'columns_gap' => array(
		'title' =>  __( 'Gap between columns', 'us' ),
		'type' => 'slider',
		'options' => array(
			'px' => array(
				'min' => 0,
				'max' => 100,
			),
			'%' => array(
				'min' => 0.0,
				'max' => 25.0,
				'step' => 0.5,
			),
			'rem' => array(
				'min' => 0.0,
				'max' => 6.0,
				'step' => 0.1,
			),
			'vw' => array(
				'min' => 0.0,
				'max' => 6.0,
				'step' => 0.1,
			),
			'vh' => array(
				'min' => 0.0,
				'max' => 6.0,
				'step' => 0.1,
			),
		),
		'std' => '3rem',
		'place_if' => $grid_columns_layout,
		'group' => us_translate( 'Columns' ),
		'usb_preview' => array(
			'elm' => '.l-section-h > .g-cols',
			'css' => 'grid-gap',
		),
	),
	'content_placement' => array(
		'title' => __( 'Columns Content Position', 'us' ),
		'type' => 'radio',
		'options' => array(
			'top' => us_translate( 'Top' ),
			'middle' => us_translate( 'Middle' ),
			'bottom' => us_translate( 'Bottom' ),
		),
		'std' => 'top',
		'cols' => 2,
		'group' => us_translate( 'Columns' ),
		'usb_preview' => array(
			'elm' => '.l-section-h > .g-cols',
			'mod' => 'valign',
		),
	),
	'gap' => array(
		'title' => __( 'Additional gap', 'us' ),
		'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">2px</span>, <span class="usof-example">1.5rem</span>, <span class="usof-example">1vw</span>',
		'type' => 'text',
		'std' => '',
		'cols' => 2,
		'place_if' => ! $grid_columns_layout,
		'group' => us_translate( 'Columns' ),
		'usb_preview' => TRUE,
	),
	'columns_type' => array(
		'switch_text' => __( 'Add extra padding around columns content', 'us' ),
		'description' => __( 'Improves appearance of columns with background', 'us' ),
		'type' => 'switch',
		'std' => FALSE,
		'group' => us_translate( 'Columns' ),
		'usb_preview' => array(
			'elm' => '.l-section-h > .g-cols',
			'toggle_class' => 'type_boxes',
		),
	),
	'columns_reverse' => array(
		'switch_text' => __( 'Reverse order for columns stacking', 'us' ),
		'description' => __( 'The last column will be shown on the top.', 'us' ) . ' ' . sprintf( __( 'Applied when the screen width is less than %s', 'us' ), '<a target="_blank" rel="noopener" href="' . admin_url( 'admin.php?page=us-theme-options#layout' ) . '" title="' . __( 'edit in Theme Options', 'us' ) . '">' . us_get_option( 'columns_stacking_width' ) . '</a>' ),
		'type' => 'switch',
		'std' => FALSE,
		'group' => us_translate( 'Columns' ),
		'usb_preview' => array(
			'elm' => '.l-section-h > .g-cols',
			'toggle_class' => 'reversed',
		),
	),
);

/**
 * @return array
 */
return array(
	'title' => __( 'Row/Section', 'us' ),
	'category' => __( 'Containers', 'us' ),
	'icon' => 'fas fa-border-all',
	'is_container' => TRUE,
	'weight' => 400, // sets the FIRST position in "Add element" lists
	'usb_preload' => TRUE,
	'as_parent' => array(
		'only' => 'vc_column',
	),
	'as_child' => array(
		'only' => 'container',
	),
	'params' => us_set_params_weight(
		$general_params,
		$revslider_params,
		$columns_params,
		$shape_divider_params,
		$design_options_params
	),

	// Default VC params which are not supported by the theme
	'vc_remove_params' => array(
		'columns_placement',
		'css_animation',
		'equal_height',
		'full_height',
		'full_width',
		'parallax',
		'parallax_image',
		'parallax_speed_bg',
		'parallax_speed_video',
		'rtl_reverse',
		'video_bg',
		'video_bg_parallax',
		'video_bg_url',
	),

	// Not used params, required for correct fallback
	'fallback_params' => array(
		'us_shape',
		'us_shape_color',
		'us_shape_flip',
		'us_shape_height',
		'us_shape_overlap',
		'us_shape_position',
		'valign',
	),

	'usb_init_js' => 'jQuery( \'.w-slider\', $elm ).wSlider(); jQuery( $elm ).wSectionVideo()',
);
