<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: carousel
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

global $pagenow;

// Get params from Grid config and exclude unneeded
// Receive data only on the edit page or create a record
$grid_params = (
		wp_doing_ajax()
		OR in_array( $pagenow, array( 'post.php', 'post-new.php' ) )
		OR apply_filters( 'usb_is_builder_page', NULL )
	)
	? us_config( 'elements/grid.params' )
	: array();

foreach( $grid_params as $grid_param_name => &$grid_param ) {

	// Exclude settings, which excluded for Carousel
	if ( ! empty( $grid_param['exclude_for_carousel'] ) ) {
		unset( $grid_params[ $grid_param_name ] );

		// Exclude Design options for correct params order
	} elseif ( in_array( $grid_param_name, array_keys( $design_options_params ) ) ) {
		unset( $grid_params[ $grid_param_name ] );
	}

	// Remove old weight
	unset( $grid_param['weight'] );
}

return array(
	'title' => __( 'Carousel', 'us' ),
	'description' => __( 'List of images, posts, pages or any custom post types', 'us' ),
	'category' => __( 'Grid', 'us' ),
	'icon' => 'fas fa-laptop-code',
	'params' => us_set_params_weight(

		// Settings from the grid
		$grid_params,

		// Carousel
		array(
			'carousel_arrows' => array(
				'type' => 'switch',
				'switch_text' => __( 'Prev/Next arrows', 'us' ),
				'std' => FALSE,
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => TRUE,
			),
			'carousel_arrows_style' => array(
				'title' => __( 'Arrows Style', 'us' ),
				'description' => $misc['desc_btn_styles'],
				'type' => 'select',
				'options' => us_array_merge(
					array(
						'circle' => '– ' . __( 'Circles', 'us' ) . ' –',
						'block' => '– ' . __( 'Full height blocks', 'us' ) . ' –',
					), us_get_btn_styles()
				),
				'std' => 'circle',
				'cols' => 2,
				'show_if' => array( 'carousel_arrows', '!=', FALSE ),
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => array(
					'elm' => '.w-grid-list',
					'mod' => 'navstyle',
				),
			),
			'carousel_arrows_size' => array(
				'title' => __( 'Arrows Size', 'us' ),
				'type' => 'slider',
				'std' => '1.8rem',
				'options' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
					'rem' => array(
						'min' => 1.0,
						'max' => 3.0,
						'step' => 0.1,
					),
					'em' => array(
						'min' => 1.0,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
				'show_if' => array( 'carousel_arrows', '!=', FALSE ),
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => array(
					'elm' => '.w-grid-list',
					'css' => '--arrows-size',
				),
			),
			'carousel_arrows_pos' => array(
				'title' => __( 'Arrows Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'outside' => __( 'Outside', 'us' ),
					'inside' => __( 'Inside', 'us' ),
				),
				'std' => 'outside',
				'cols' => 2,
				'show_if' => array( 'carousel_arrows', '!=', FALSE ),
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => array(
					'elm' => '.w-grid-list',
					'mod' => 'navpos',
				),
			),
			'carousel_arrows_offset' => array(
				'title' => __( 'Arrows Offset', 'us' ),
				'type' => 'slider',
				'std' => '0px',
				'options' => array(
					'px' => array(
						'min' => -60,
						'max' => 60,
					),
					'rem' => array(
						'min' => -3.0,
						'max' => 3.0,
						'step' => 0.1,
					),
					'em' => array(
						'min' => -3.0,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
				'show_if' => array( 'carousel_arrows', '!=', FALSE ),
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => array(
					'elm' => '.w-grid-list',
					'css' => '--arrows-offset',
				),
			),
			'carousel_dots' => array(
				'type' => 'switch',
				'switch_text' => __( 'Navigation Dots', 'us' ),
				'std' => FALSE,
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => TRUE,
			),
			'carousel_center' => array(
				'type' => 'switch',
				'switch_text' => __( 'First item in the center', 'us' ),
				'std' => FALSE,
				'classes' => 'for_above',
				'show_if' => array( 'columns', '!=', '1' ),
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => TRUE,
			),
			'carousel_slideby' => array(
				'type' => 'switch',
				'switch_text' => __( 'Slide by several items instead of one', 'us' ),
				'std' => FALSE,
				'classes' => 'for_above',
				'show_if' => array( 'carousel_center', '!=', '1' ),
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => TRUE,
			),
			'carousel_loop' => array(
				'type' => 'switch',
				'switch_text' => __( 'Infinite loop', 'us' ),
				'std' => FALSE,
				'classes' => 'for_above',
				'show_if' => array( 'carousel_slideby', '!=', '1' ),
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => TRUE,
			),
			'carousel_autoheight' => array(
				'type' => 'switch',
				'switch_text' => __( 'Auto height (for 1 column only)', 'us' ),
				'std' => FALSE,
				'classes' => 'for_above',
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => TRUE,
			),
			'carousel_fade' => array(
				'type' => 'switch',
				'switch_text' => __( 'Fade transition (for 1 column only)', 'us' ),
				'std' => FALSE,
				'classes' => 'for_above',
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => TRUE,
			),
			'carousel_autoplay' => array(
				'type' => 'switch',
				'switch_text' => __( 'Auto Rotation', 'us' ),
				'std' => FALSE,
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => TRUE,
			),
			'carousel_interval' => array(
				'title' => __( 'Auto Rotation Interval', 'us' ),
				'type' => 'slider',
				'std' => '3s',
				'options' => array(
					's' => array(
						'min' => 1.0,
						'max' => 9.0,
						'step' => 0.5,
					),
				),
				'show_if' => array( 'carousel_autoplay', '!=', FALSE ),
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => TRUE,
			),
			'carousel_autoplay_smooth' => array(
				'type' => 'switch',
				'switch_text' => __( 'Continual Rotation', 'us' ),
				'std' => FALSE,
				'classes' => 'for_above',
				'show_if' => array( 'carousel_autoplay', '!=', FALSE ),
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => TRUE,
			),
			'carousel_speed' => array(
				'title' => __( 'Transition Duration', 'us' ),
				'type' => 'slider',
				'std' => '250ms',
				'options' => array(
					'ms' => array(
						'min' => 0,
						'max' => 2000,
						'step' => 50,
					),
				),
				'show_if' => array( 'carousel_fade', '=', FALSE ),
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => TRUE,
			),
			'carousel_transition' => array(
				'title' => __( 'Transition Effect', 'us' ),
				'description' => '<a href="http://cubic-bezier.com/" target="_blank" rel="noopener">' . __( 'Use timing function', 'us' ) . '</a>' . '. ' . __( 'Examples:', 'us' ) . ' <span class="usof-example">linear</span>, <span class="usof-example">cubic-bezier(0,1,.8,1)</span>, <span class="usof-example">cubic-bezier(.78,.13,.15,.86)</span>',
				'type' => 'text',
				'std' => '',
				'show_if' => array( 'carousel_fade', '=', FALSE ),
				'group' => __( 'Carousel', 'us' ),
				'usb_preview' => TRUE,
			),
		),

		// Responsive options section
		array(
			'breakpoint_1_width' => array(
				'title' => __( 'Below screen width', 'us' ),
				'type' => 'slider',
				'options' => array(
					'px' => array(
						'min' => 900,
						'max' => 1500,
					),
				),
				'std' => '1200px',
				'cols' => 2,
				'group' => __( 'Responsive', 'us' ),
				'usb_preview' => TRUE,
			),
			'breakpoint_1_cols' => array(
				'title' => __( 'show', 'us' ),
				'type' => 'select',
				'options' => $misc['column_values'],
				'std' => '3',
				'cols' => 2,
				'group' => __( 'Responsive', 'us' ),
				'usb_preview' => TRUE,
			),
			'breakpoint_1_autoplay' => array(
				'type' => 'switch',
				'switch_text' => __( 'Auto Rotation', 'us' ),
				'std' => TRUE,
				'classes' => 'for_above',
				'group' => __( 'Responsive', 'us' ),
				'usb_preview' => TRUE,
			),
			'breakpoint_2_width' => array(
				'title' => __( 'Below screen width', 'us' ),
				'type' => 'slider',
				'options' => array(
					'px' => array(
						'min' => 600,
						'max' => 1200,
					),
				),
				'std' => '900px',
				'cols' => 2,
				'group' => __( 'Responsive', 'us' ),
				'usb_preview' => TRUE,
			),
			'breakpoint_2_cols' => array(
				'title' => __( 'show', 'us' ),
				'type' => 'select',
				'options' => $misc['column_values'],
				'std' => '2',
				'cols' => 2,
				'group' => __( 'Responsive', 'us' ),
				'usb_preview' => TRUE,
			),
			'breakpoint_2_autoplay' => array(
				'type' => 'switch',
				'switch_text' => __( 'Auto Rotation', 'us' ),
				'std' => TRUE,
				'classes' => 'for_above',
				'group' => __( 'Responsive', 'us' ),
				'usb_preview' => TRUE,
			),
			'breakpoint_3_width' => array(
				'title' => __( 'Below screen width', 'us' ),
				'type' => 'slider',
				'options' => array(
					'px' => array(
						'min' => 300,
						'max' => 900,
					),
				),
				'std' => '600px',
				'cols' => 2,
				'group' => __( 'Responsive', 'us' ),
				'usb_preview' => TRUE,
			),
			'breakpoint_3_cols' => array(
				'title' => __( 'show', 'us' ),
				'type' => 'select',
				'options' => $misc['column_values'],
				'std' => '1',
				'cols' => 2,
				'group' => __( 'Responsive', 'us' ),
				'usb_preview' => TRUE,
			),
			'breakpoint_3_autoplay' => array(
				'type' => 'switch',
				'switch_text' => __( 'Auto Rotation', 'us' ),
				'std' => TRUE,
				'classes' => 'for_above',
				'group' => __( 'Responsive', 'us' ),
				'usb_preview' => TRUE,
			),
		),

		$design_options_params
	),
	'usb_init_js' => '$elm.wGrid()',
);
