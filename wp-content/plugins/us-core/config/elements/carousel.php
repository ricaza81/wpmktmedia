<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

global $pagenow;

// Get params from Grid config and exclude unneeded
// Receive data only on the edit page or create a record
$grid_params = ( wp_doing_ajax() OR in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) )
	? us_config( 'elements/grid.params' )
	: array();

foreach( $grid_params as $grid_param_name => $grid_param ) {

	// Exclude settings, which excluded for Carousel
	if ( ! empty( $grid_param['exclude_for_carousel'] ) ) {
		unset( $grid_params[ $grid_param_name ] );

		// Exclude Design options for correct params order
	} elseif ( in_array( $grid_param_name, array_keys( $design_options ) ) ) {
		unset( $grid_params[ $grid_param_name ] );
	}
}

return array(
	'title' => __( 'Carousel', 'us' ),
	'description' => __( 'List of images, posts, pages or any custom post types', 'us' ),
	'icon' => 'fas fa-laptop-code',
	'params' => array_merge( $grid_params, array(

		// Carousel options
		'carousel_arrows' => array(
			'type' => 'switch',
			'switch_text' => __( 'Prev/Next arrows', 'us' ),
			'std' => FALSE,
			'group' => __( 'Carousel', 'us' ),
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
		),
		'carousel_arrows_size' => array(
			'title' => __( 'Arrows Size', 'us' ),
			'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">26px</span>, <span class="usof-example">3rem</span>',
			'type' => 'text',
			'std' => '1.8rem',
			'cols' => 2,
			'show_if' => array( 'carousel_arrows', '!=', FALSE ),
			'group' => __( 'Carousel', 'us' ),
		),
		'carousel_arrows_pos' => array(
			'title' => __( 'Arrows Position', 'us' ),
			'type' => 'select',
			'options' => array(
				'outside' => __( 'Outside', 'us' ),
				'inside' => __( 'Inside', 'us' ),
			),
			'std' => 'outside',
			'cols' => 2,
			'show_if' => array( 'carousel_arrows', '!=', FALSE ),
			'group' => __( 'Carousel', 'us' ),
		),
		'carousel_arrows_offset' => array(
			'title' => __( 'Arrows Offset', 'us' ),
			'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">20px</span>, <span class="usof-example">2rem</span>',
			'type' => 'text',
			'std' => '',
			'cols' => 2,
			'show_if' => array( 'carousel_arrows', '!=', FALSE ),
			'group' => __( 'Carousel', 'us' ),
		),
		'carousel_dots' => array(
			'type' => 'switch',
			'switch_text' => __( 'Navigation Dots', 'us' ),
			'std' => FALSE,
			'group' => __( 'Carousel', 'us' ),
		),
		'carousel_center' => array(
			'type' => 'switch',
			'switch_text' => __( 'First item in the center', 'us' ),
			'std' => FALSE,
			'classes' => 'for_above',
			'show_if' => array( 'columns', '!=', '1' ),
			'group' => __( 'Carousel', 'us' ),
		),
		'carousel_slideby' => array(
			'type' => 'switch',
			'switch_text' => __( 'Slide by several items instead of one', 'us' ),
			'std' => FALSE,
			'classes' => 'for_above',
			'show_if' => array( 'carousel_center', '!=', '1' ),
			'group' => __( 'Carousel', 'us' ),
		),
		'carousel_loop' => array(
			'type' => 'switch',
			'switch_text' => __( 'Infinite loop', 'us' ),
			'std' => FALSE,
			'classes' => 'for_above',
			'show_if' => array( 'carousel_slideby', '!=', '1' ),
			'group' => __( 'Carousel', 'us' ),
		),
		'carousel_autoheight' => array(
			'type' => 'switch',
			'switch_text' => __( 'Auto height (for 1 column only)', 'us' ),
			'std' => FALSE,
			'classes' => 'for_above',
			'group' => __( 'Carousel', 'us' ),
		),
		'carousel_fade' => array(
			'type' => 'switch',
			'switch_text' => __( 'Fade transition (for 1 column only)', 'us' ),
			'std' => FALSE,
			'classes' => 'for_above',
			'group' => __( 'Carousel', 'us' ),
		),
		'carousel_autoplay' => array(
			'type' => 'switch',
			'switch_text' => __( 'Auto Rotation', 'us' ),
			'std' => FALSE,
			'group' => __( 'Carousel', 'us' ),
		),
		'carousel_interval' => array(
			'title' => __( 'Auto Rotation Interval', 'us' ),
			'description' => $misc['desc_seconds'],
			'type' => 'text',
			'std' => '3',
			'show_if' => array( 'carousel_autoplay', '!=', FALSE ),
			'group' => __( 'Carousel', 'us' ),
		),
		'carousel_autoplay_smooth' => array(
			'type' => 'switch',
			'switch_text' => __( 'Continual Rotation', 'us' ),
			'std' => FALSE,
			'classes' => 'for_above',
			'show_if' => array( 'carousel_autoplay', '!=', FALSE ),
			'group' => __( 'Carousel', 'us' ),
		),
		'carousel_speed' => array(
			'title' => __( 'Transition Duration', 'us' ),
			'description' => $misc['desc_milliseconds'],
			'type' => 'text',
			'std' => '250',
			'show_if' => array( 'carousel_fade', '=', FALSE ),
			'group' => __( 'Carousel', 'us' ),
		),
		'carousel_transition' => array(
			'title' => __( 'Transition Effect', 'us' ),
			'description' => '<a href="http://cubic-bezier.com/" target="_blank" rel="noopener">' . __( 'Use timing function', 'us' ) . '</a>' . '. ' . __( 'Examples:', 'us' ) . ' <span class="usof-example">linear</span>, <span class="usof-example">cubic-bezier(0,1,.8,1)</span>, <span class="usof-example">cubic-bezier(.78,.13,.15,.86)</span>',
			'type' => 'text',
			'std' => '',
			'show_if' => array( 'carousel_fade', '=', FALSE ),
			'group' => __( 'Carousel', 'us' ),
		),

		// Responsive
		'breakpoint_1_width' => array(
			'title' => __( 'Below screen width', 'us' ),
			'type' => 'text',
			'std' => '1200px',
			'cols' => 2,
			'group' => us_translate( 'Responsive Options', 'js_composer' ),
		),
		'breakpoint_1_cols' => array(
			'title' => __( 'show', 'us' ),
			'type' => 'select',
			'options' => $misc['column_values'],
			'std' => '3',
			'cols' => 2,
			'group' => us_translate( 'Responsive Options', 'js_composer' ),
		),
		'breakpoint_1_autoplay' => array(
			'type' => 'switch',
			'switch_text' => __( 'Auto Rotation', 'us' ),
			'std' => TRUE,
			'classes' => 'for_above',
			'group' => us_translate( 'Responsive Options', 'js_composer' ),
		),
		'breakpoint_2_width' => array(
			'title' => __( 'Below screen width', 'us' ),
			'type' => 'text',
			'std' => '900px',
			'cols' => 2,
			'group' => us_translate( 'Responsive Options', 'js_composer' ),
		),
		'breakpoint_2_cols' => array(
			'title' => __( 'show', 'us' ),
			'type' => 'select',
			'options' => $misc['column_values'],
			'std' => '2',
			'cols' => 2,
			'group' => us_translate( 'Responsive Options', 'js_composer' ),
		),
		'breakpoint_2_autoplay' => array(
			'type' => 'switch',
			'switch_text' => __( 'Auto Rotation', 'us' ),
			'std' => TRUE,
			'classes' => 'for_above',
			'group' => us_translate( 'Responsive Options', 'js_composer' ),
		),
		'breakpoint_3_width' => array(
			'title' => __( 'Below screen width', 'us' ),
			'type' => 'text',
			'std' => '600px',
			'cols' => 2,
			'group' => us_translate( 'Responsive Options', 'js_composer' ),
		),
		'breakpoint_3_cols' => array(
			'title' => __( 'show', 'us' ),
			'type' => 'select',
			'options' => $misc['column_values'],
			'std' => '1',
			'cols' => 2,
			'group' => us_translate( 'Responsive Options', 'js_composer' ),
		),
		'breakpoint_3_autoplay' => array(
			'type' => 'switch',
			'switch_text' => __( 'Auto Rotation', 'us' ),
			'std' => TRUE,
			'classes' => 'for_above',
			'group' => us_translate( 'Responsive Options', 'js_composer' ),
		),

	), $design_options ),
);
