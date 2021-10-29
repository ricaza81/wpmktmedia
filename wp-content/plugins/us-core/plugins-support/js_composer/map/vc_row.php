<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Extending shortcode: vc_row
 */

$misc = us_config( 'elements_misc' );

vc_remove_param( 'vc_row', 'full_width' );
vc_remove_param( 'vc_row', 'full_height' );
vc_remove_param( 'vc_row', 'content_placement' );
vc_remove_param( 'vc_row', 'video_bg' );
vc_remove_param( 'vc_row', 'video_bg_url' );
vc_remove_param( 'vc_row', 'video_bg_parallax' );
vc_remove_param( 'vc_row', 'columns_placement' );
vc_remove_param( 'vc_row', 'equal_height' );
vc_remove_param( 'vc_row', 'parallax_speed_video' );
vc_remove_param( 'vc_row', 'parallax_speed_bg' );
vc_remove_param( 'vc_row', 'css_animation' );
vc_remove_param( 'vc_row', 'rtl_reverse' );

if ( ! vc_is_page_editable() ) {
	vc_remove_param( 'vc_row', 'parallax' );
	vc_remove_param( 'vc_row', 'parallax_image' );
}

vc_update_shortcode_param(
	'vc_row', array(
		'heading' => __( 'Additional gap', 'us' ),
		'param_name' => 'gap',
		'type' => 'textfield',
		'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">2px</span>, <span class="usof-example">1.5rem</span>, <span class="usof-example">1vw</span>',
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 9,
		'group' => us_translate( 'Columns' ),
	)
);
vc_update_shortcode_param(
	'vc_row', array(
		'param_name' => 'el_class',
		'description' => '',
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 1,
		'group' => __( 'Design', 'us' ),
	)
);
vc_update_shortcode_param(
	'vc_row', array(
		'param_name' => 'el_id',
		'description' => '',
		'edit_field_class' => 'vc_col-sm-6',
		'group' => __( 'Design', 'us' ),
	)
);

vc_update_shortcode_param(
	'vc_row', array(
		'param_name' => 'css',
		'type' => 'us_design_options',
		'heading' => '',
		'params' => us_config( 'elements_design_options.css.params', array() ),
		'group' => __( 'Design', 'us' ),
	)
);

// Add option to set Rev Slider as row background
if ( class_exists( 'RevSlider' ) ) {
	$slider = new RevSlider();
	$arrSliders = $slider->getArrSliders();
	$revsliders = array();
	if ( $arrSliders ) {
		foreach ( $arrSliders as $slider ) {
			$revsliders[ $slider->getTitle() ] = $slider->getAlias();
		}
	}
	vc_add_param(
		'vc_row', array(
			'param_name' => 'us_bg_rev_slider',
			'type' => 'dropdown',
			'value' => $revsliders,
			'std' => $config['atts']['us_bg_rev_slider'],
			'dependency' => array( 'element' => 'us_bg_show', 'value' => 'rev_slider' ),
			'weight' => 45,
		)
	);

	$revslider_option = array( us_translate( 'Slider Revolution', 'revslider' ) => 'rev_slider' );
} else {
	$revslider_option = array();
}

// Configure images sources for Row Background
$image_sources = array(
	'none' => us_translate( 'None' ),
	'media' => __( 'Custom', 'us' ),
	'featured' => us_translate( 'Featured Image' ),
	'us_tile_additional_image' => __( 'Custom appearance in Grid', 'us' ) . ': ' . __( 'Additional Image', 'us' ),
);

// Add image types from ACF
if ( function_exists( 'acf_get_field_groups' ) AND $acf_groups = acf_get_field_groups() ) {
	foreach ( $acf_groups as $group ) {
		$fields = acf_get_fields( $group['ID'] );
		foreach ( $fields as $field ) {
			if ( $field['type'] == 'image' ) {
				$image_sources[ $field['name'] ] = $group['title'] . ': ' . $field['label'];
			}
		}
	}
}

// Shape Divider
$shape_divider_settings = array();
foreach ( array( 'top', 'bottom' ) as $direction ) {
	$shape_divider_settings = array_merge( $shape_divider_settings, array(
		array(
			'param_name' => "us_shape_show_{$direction}",
			'type' => 'checkbox',
			'value' => array( __( "Show {$direction} shape divider", 'us' ) => TRUE ),
			( ( $config['atts']["us_shape_show_{$direction}"] !== FALSE ) ?
				'std' : '_std' ) => $config['atts']["us_shape_show_{$direction}"],
			'group' => __( 'Shape Divider', 'us' ),
			'weight' => 100,
		),
		array(
			'param_name' => "us_shape_{$direction}",
			'type' => 'us_imgradio',
			'preview_path' => '/assets/shapes/%s.svg',
			'value' => array(
				__( 'Tilt', 'us' ) => 'tilt',
				__( 'Curve', 'us' ) => 'curve',
				__( 'Curve (inv)', 'us' ) => 'curve-inv',
				__( 'Triangle', 'us' ) => 'triangle',
				__( 'Triangle (inv)', 'us' ) => 'triangle-inv',
				__( 'Triangle 2', 'us' ) => 'triangle-2',
				__( 'Triangle 2 (inv)', 'us' ) => 'triangle-2-inv',
				__( 'Wave', 'us' ) => 'wave',
				__( 'Zigzag', 'us' ) => 'zigzag',
				__( 'Custom', 'us' ) => 'custom',
			),
			'std' => $config['atts']["us_shape_{$direction}"],
			'edit_field_class' => 'vc_col-sm-12 radio_cols-4 us_shape_' . $direction,
			'dependency' => array(
				'element' => "us_shape_show_{$direction}",
				'value' => array(
					'1',
				),
			),
			'group' => __( 'Shape Divider', 'us' ),
			'weight' => 100,
		),
		array(
			'param_name' => "us_shape_custom_{$direction}",
			'type' => 'attach_image',
			'std' => $config['atts']["us_shape_custom_{$direction}"],
			'dependency' => array(
				'element' => "us_shape_{$direction}",
				'value' => array(
					'custom',
				),
			),
			'group' => __( 'Shape Divider', 'us' ),
			'weight' => 100,
		),
		array(
			'heading' => us_translate( 'Height' ),
			'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">15vh</span>, <span class="usof-example">10vw</span>, <span class="usof-example">30%</span>, <span class="usof-example">200px</span>',
			'param_name' => "us_shape_height_{$direction}",
			'type' => 'textfield',
			'std' => $config['atts']["us_shape_height_{$direction}"],
			'edit_field_class' => 'vc_col-sm-6',
			'dependency' => array(
				'element' => "us_shape_show_{$direction}",
				'value' => array(
					'1',
				),
			),
			'group' => __( 'Shape Divider', 'us' ),
			'weight' => 100,
		),
		array(
			'heading' => us_translate( 'Color' ),
			'param_name' => "us_shape_color_{$direction}",
			'type' => 'us_color',
			'std' => $config['atts']["us_shape_color_{$direction}"],
			'with_gradient' => FALSE,
			'edit_field_class' => 'vc_col-sm-6',
			'dependency' => array(
				'element' => "us_shape_show_{$direction}",
				'value' => array(
					'1',
				),
			),
			'group' => __( 'Shape Divider', 'us' ),
			'weight' => 100,
		),
		array(
			'param_name' => "us_shape_overlap_{$direction}",
			'type' => 'checkbox',
			'value' => array( __( 'Overlap the content of this Row', 'us' ) => TRUE ),
			( ( $config['atts']["us_shape_overlap_{$direction}"] !== FALSE ) ?
				'std' : '_std' ) => $config['atts']["us_shape_overlap_{$direction}"],
			'dependency' => array(
				'element' => "us_shape_show_{$direction}",
				'value' => array(
					'1',
				),
			),
			'group' => __( 'Shape Divider', 'us' ),
			'weight' => 100,
		),
		array(
			'param_name' => "us_shape_flip_{$direction}",
			'type' => 'checkbox',
			'value' => array( __( 'Flip horizontally', 'us' ) => TRUE ),
			( ( $config['atts']["us_shape_flip_{$direction}"] !== FALSE ) ?
				'std' : '_std' ) => $config['atts']["us_shape_flip_{$direction}"],
			'dependency' => array(
				'element' => "us_shape_show_{$direction}",
				'value' => array(
					'1',
				),
			),
			'group' => __( 'Shape Divider', 'us' ),
			'weight' => 100,
		),
	) );
}

// Deprecated params
$deprecated_settings = array();
foreach ( array( 'us_shape', 'us_shape_height', 'us_shape_position', 'us_shape_color', 'us_shape_overlap', 'us_shape_flip' ) as $param_name ) {
	$deprecated_settings[] = array(
		'type' => 'textfield',
		'param_name' => $param_name,
		'std' => '',
		'edit_field_class' => 'hidden',
		'weight' => 1,
	);
}

vc_add_params(
	'vc_row', array_merge(
		$shape_divider_settings, array(
			array(
				'param_name' => 'content_placement',
				'heading' => __( 'Columns Content Position', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'Top' ) => 'top',
					us_translate( 'Middle' ) => 'middle',
					us_translate( 'Bottom' ) => 'bottom',
				),
				'std' => $config['atts']['content_placement'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 10,
				'group' => us_translate( 'Columns' ),
			),
			array(
				'param_name' => 'columns_type',
				'description' => __( 'Improves appearance of columns with background', 'us' ),
				'type' => 'checkbox',
				'value' => array( __( 'Add extra padding around columns content', 'us' ) => TRUE ),
				( ( $config['atts']['columns_type'] !== FALSE ) ?
					'std' : '_std' ) => $config['atts']['columns_type'],
				'weight' => 8,
				'group' => us_translate( 'Columns' ),
			),
			array(
				'param_name' => 'columns_reverse',
				'description' => __( 'The last column will be shown on the top.', 'us' ) . ' ' . sprintf( __( 'Applied when the screen width is less than %s', 'us' ), '<a target="_blank" rel="noopener" href="' . admin_url( 'admin.php?page=us-theme-options' ) . '#layout" title="' . __( 'edit in Theme Options', 'us' ) . '">' . us_get_option( 'columns_stacking_width' ) . '</a>' ),
				'type' => 'checkbox',
				'value' => array( __( 'Reverse order for columns stacking', 'us' ) => TRUE ),
				( ( $config['atts']['columns_reverse'] !== FALSE ) ?
					'std' : '_std' ) => $config['atts']['columns_reverse'],
				'weight' => 7,
				'group' => us_translate( 'Columns' ),
			),
			array(
				'param_name' => 'height',
				'heading' => __( 'Row Height', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Default from Theme Options', 'us' ) => 'default',
					__( 'Equals the content height', 'us' ) => 'auto',
					__( 'Small', 'us' ) => 'small',
					__( 'Medium', 'us' ) => 'medium',
					__( 'Large', 'us' ) => 'large',
					__( 'Huge', 'us' ) => 'huge',
					__( 'Full Screen', 'us' ) => 'full',
				),
				'std' => $config['atts']['height'],
				'weight' => 170,
			),
			array(
				'param_name' => 'valign',
				'heading' => __( 'Row Content Position', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'Top' ) => 'top',
					us_translate( 'Middle' ) => 'center',
					us_translate( 'Bottom' ) => 'bottom',
				),
				'std' => $config['atts']['valign'],
				'dependency' => array( 'element' => 'height', 'value' => 'full' ),
				'weight' => 160,
			),
			array(
				'param_name' => 'width',
				'heading' => __( 'Full Width Content', 'us' ),
				'type' => 'checkbox',
				'value' => array( __( 'Stretch content of this row to the screen width', 'us' ) => 'full' ),
				( ( $config['atts']['width'] !== FALSE ) ?
					'std' : '_std' ) => $config['atts']['width'],
				'weight' => 150,
			),
			array(
				'param_name' => 'color_scheme',
				'heading' => __( 'Row Color Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Content colors', 'us' ) => '',
					__( 'Alternate Content colors', 'us' ) => 'alternate',
					__( 'Primary bg & White text', 'us' ) => 'primary',
					__( 'Secondary bg & White text', 'us' ) => 'secondary',
					__( 'Footer colors', 'us' ) => 'footer-bottom',
					__( 'Alternate Footer colors', 'us' ) => 'footer-top',
				),
				'std' => $config['atts']['color_scheme'],
				'weight' => 140,
			),
			array(
				'param_name' => 'us_bg_image_source',
				'heading' => __( 'Background Image', 'us' ),
				'type' => 'dropdown',
				'value' => array_flip( $image_sources ),
				'std' => $config['atts']['us_bg_image_source'],
				'weight' => 110,
			),

			// Background Image
			array(
				'param_name' => 'us_bg_image',
				'type' => 'attach_image',
				'std' => $config['atts']['us_bg_image'],
				'dependency' => array( 'element' => 'us_bg_image_source', 'value' => 'media' ),
				'weight' => 100,
			),
			array(
				'param_name' => 'us_bg_size',
				'heading' => __( 'Background Image Size', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Fill Area', 'us' ) => 'cover',
					__( 'Fit to Area', 'us' ) => 'contain',
					__( 'Initial', 'us' ) => 'initial',
				),
				'std' => $config['atts']['us_bg_size'],
				'dependency' => array(
					'element' => 'us_bg_image_source',
					'value' => array_slice( array_keys( $image_sources ), 1 ),
				),
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 90,
			),
			array(
				'param_name' => 'us_bg_pos',
				'heading' => __( 'Background Image Position', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'Top Left' ) => 'top left',
					us_translate( 'Top' ) => 'top center',
					us_translate( 'Top Right' ) => 'top right',
					us_translate( 'Left' ) => 'center left',
					us_translate( 'Center' ) => 'center center',
					us_translate( 'Right' ) => 'center right',
					us_translate( 'Bottom Left' ) => 'bottom left',
					us_translate( 'Bottom' ) => 'bottom center',
					us_translate( 'Bottom Right' ) => 'bottom right',
				),
				'std' => $config['atts']['us_bg_pos'],
				'dependency' => array(
					'element' => 'us_bg_image_source',
					'value' => array_slice( array_keys( $image_sources ), 1 ),
				),
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 85,
			),
			array(
				'param_name' => 'us_bg_parallax',
				'heading' => __( 'Parallax Effect', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'None' ) => '',
					__( 'Vertical Parallax', 'us' ) => 'vertical',
					__( 'Horizontal Parallax', 'us' ) => 'horizontal',
					__( 'Fixed', 'us' ) => 'still',
				),
				'std' => $config['atts']['us_bg_parallax'],
				'dependency' => array(
					'element' => 'us_bg_image_source',
					'value' => array_slice( array_keys( $image_sources ), 1 ),
				),
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 82,
			),
			array(
				'param_name' => 'us_bg_repeat',
				'heading' => __( 'Background Image Repeat', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Repeat', 'us' ) => 'repeat',
					__( 'Horizontally', 'us' ) => 'repeat-x',
					__( 'Vertically', 'us' ) => 'repeat-y',
					us_translate( 'None' ) => 'no-repeat',
				),
				'std' => $config['atts']['us_bg_repeat'],
				'dependency' => array(
					'element' => 'us_bg_image_source',
					'value' => array_slice( array_keys( $image_sources ), 1 ),
				),
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 80,
			),
			array(
				'param_name' => 'us_bg_parallax_width',
				'heading' => __( 'Parallax Background Width', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					'110%' => '110',
					'120%' => '120',
					'130%' => '130',
					'140%' => '140',
					'150%' => '150',
				),
				'std' => $config['atts']['us_bg_parallax_width'],
				'dependency' => array( 'element' => 'us_bg_parallax', 'value' => 'horizontal' ),
				'weight' => 70,
			),
			array(
				'param_name' => 'us_bg_parallax_reverse',
				'type' => 'checkbox',
				'value' => array( __( 'Reverse Vertical Parallax Effect', 'us' ) => TRUE ),
				( ( $config['atts']['us_bg_parallax_reverse'] !== FALSE ) ?
					'std' : '_std' ) => $config['atts']['us_bg_parallax_reverse'],
				'dependency' => array(
					'element' => 'us_bg_parallax',
					'value' => 'vertical',
				),
				'weight' => 60,
			),

			// Show on background
			array(
				'param_name' => 'us_bg_show',
				'heading' => __( 'Show on background', 'us' ),
				'type' => 'dropdown',
				'value' => array_merge(
					array(
						us_translate( 'None' ) => '',
						us_translate( 'Video' ) => 'video',
						__( 'Image Slider', 'us' ) => 'img_slider',
					),
					$revslider_option
				),
				'std' => $config['atts']['us_bg_show'],
				'weight' => 59,
			),

			// Video
			array(
				'param_name' => 'us_bg_video',
				'description' => __( 'Link to YouTube, Vimeo or video file (mp4, webm, ogg)', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['us_bg_video'],
				'dependency' => array( 'element' => 'us_bg_show', 'value' => 'video' ),
				'weight' => 58,
			),
			array(
				'param_name' => 'us_bg_video_disable_width',
				'heading' => __( 'Hide video at width', 'us' ),
				'description' => __( 'When screen width is less than this value, background video will be hidden', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['us_bg_video_disable_width'],
				'dependency' => array( 'element' => 'us_bg_show', 'value' => 'video' ),
				'weight' => 57,
			),

			// Slider
			array(
				'param_name' => 'us_bg_slider_ids',
				'type' => 'attach_images',
				'std' => $config['atts']['us_bg_slider_ids'],
				'dependency' => array( 'element' => 'us_bg_show', 'value' => 'img_slider' ),
				'weight' => 49,
			),
			array(
				'param_name' => 'us_bg_slider_transition',
				'heading' => __( 'Transition Effect', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Slide', 'us' ) => 'slide',
					__( 'Fade', 'us' ) => 'crossfade',
				),
				'std' => $config['atts']['us_bg_slider_transition'],
				'dependency' => array( 'element' => 'us_bg_show', 'value' => 'img_slider' ),
				'edit_field_class' => 'vc_col-sm-4',
				'weight' => 48,
			),
			array(
				'param_name' => 'us_bg_slider_speed',
				'heading' => __( 'Transition Duration', 'us' ),
				'description' => $misc['desc_milliseconds'],
				'type' => 'textfield',
				'std' => $config['atts']['us_bg_slider_speed'],
				'dependency' => array( 'element' => 'us_bg_show', 'value' => 'img_slider' ),
				'edit_field_class' => 'vc_col-sm-4',
				'weight' => 47,
			),
			array(
				'param_name' => 'us_bg_slider_interval',
				'heading' => __( 'Auto Rotation Interval', 'us' ),
				'description' => $misc['desc_seconds'],
				'type' => 'textfield',
				'std' => $config['atts']['us_bg_slider_interval'],
				'dependency' => array( 'element' => 'us_bg_show', 'value' => 'img_slider' ),
				'edit_field_class' => 'vc_col-sm-4',
				'weight' => 46,
			),
			array(
				'param_name' => 'us_bg_overlay_color',
				'heading' => __( 'Background Overlay', 'us' ),
				'type' => 'us_color',
				'clear_pos' => 'right',
				'std' => $config['atts']['us_bg_overlay_color'],
				'weight' => 45,
			),

			// Sticky & Disable Row
			array(
				'param_name' => 'sticky',
				'heading' => __( 'Sticky Row', 'us' ),
				'type' => 'checkbox',
				'value' => array( __( 'Fix this row at the top of a page during scroll', 'us' ) => TRUE ),
				( ( $config['atts']['sticky'] !== FALSE ) ?
					'std' : '_std' ) => $config['atts']['sticky'],
				'weight' => 30,
			),
		), $deprecated_settings
	)
);

if ( class_exists( 'Ultimate_VC_Addons' ) ) {
	vc_add_param(
		'vc_row', array(
			'param_name' => 'us_notification',
			'type' => 'ult_param_heading',
			'text' => __( 'Background Image, Background Video, Background Overlay settings located below will override the settings located at "Background" and "Effect" tabs.', 'us' ),
			'edit_field_class' => 'ult-param-important-wrapper ult-dashicon vc_column vc_col-sm-12',
			'weight' => 110,
		)
	);
}


if ( ! class_exists( 'Us_WPBakeryShortCode_Vc_Row' ) ) {

	if ( ! class_exists( 'WPBakeryShortCode_Vc_Row' ) ) {
		require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-row.php' );
	}

	/**
	 * Extending the standard WPBakeryShortCode_Vc_Row class
	 */
	class Us_WPBakeryShortCode_Vc_Row extends WPBakeryShortCode_Vc_Row {
		/**
		 * Generate controls for row
		 * @param $controls
		 * @param string $extended_css
		 * @return string
		 * @throws \Exception
		 */
		public function getColumnControls( $controls, $extended_css = '' ) {
			$output = parent::getColumnControls( $controls, $extended_css = '' );

			// Adding a new controller to copy the shortcode to the clipboard
			return str_replace( '<a class="vc_control column_toggle', '<a class="vc_control column_copy_clipboard vc_column-copy-clipboard" href="#" title="' . us_translate( 'Copy' ) . '" data-vc-control="row-copy-clipboard"><i class="fas fa-copy"></i></a><a class="vc_control column_toggle', $output );
		}
	}

	vc_map_update( 'vc_row', array(
		// Assign a custom class to handle shortcode
		'php_class_name' => 'US_WPBakeryShortCode_Vc_Row'
	) );
}

// Setting proper shortcode order in VC shortcodes listing
vc_map_update( 'vc_row', array( 'weight' => 390 ) );
