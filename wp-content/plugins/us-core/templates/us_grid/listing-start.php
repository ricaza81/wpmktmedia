<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Opening part of Grid output
 */

global $us_grid_layouts;
$us_grid_layouts = isset( $us_grid_layouts ) ? $us_grid_layouts : array();
$us_grid_index = isset( $us_grid_index ) ? intval( $us_grid_index ) : 0;
$is_widget = isset( $is_widget ) ? $is_widget : FALSE;
$filter_html = isset( $filter_html ) ? $filter_html : '';
$data_atts = isset( $data_atts ) ? $data_atts : '';

// Check Grid params and use default values from config, if its not set
$default_grid_params = us_shortcode_atts( array(), 'us_grid' );
foreach ( $default_grid_params as $param => $value ) {
	if ( ! isset( $$param ) ) {
		$$param = $value;
	}
}

// Check Carousel params and use default values from config, if its not set
if ( $type == 'carousel' ) {
	$default_carousel_params = us_shortcode_atts( array(), 'us_carousel' );
	foreach ( $default_carousel_params as $param => $value ) {
		if ( ! isset( $$param ) ) {
			$$param = $value;
		}
	}
}

// Set unique grid ID
$grid_elm_id = ( ! empty( $el_id ) ) ? $el_id : 'us_grid_' . $us_grid_index;

// Force items aspect ratio to "square" for Metro type
if ( $type == 'metro' ) {
	$items_ratio = '1x1';
}

// Check if grid items has specific Aspect Ratio
if ( $items_ratio != 'default' OR us_arr_path( $grid_layout_settings, 'default.options.fixed' ) ) {
	$items_have_ratio = TRUE;
} else {
	$items_have_ratio = FALSE;
}

// Grid HTML attributes
$grid_atts = array(
	'class' => 'w-grid',
	'id' => $grid_elm_id,
);
$grid_atts['class'] .= isset( $classes ) ? $classes : '';
$grid_atts['class'] .= ' type_' . $type;
$grid_atts['class'] .= ' layout_' . $items_layout;

if ( $columns != 1 AND $type != 'metro' ) {
	$grid_atts['class'] .= ' cols_' . $columns;
}
if ( $items_valign ) {
	$grid_atts['class'] .= ' valign_center';
}
if ( $pagination == 'regular' ) {
	$grid_atts['class'] .= ' with_pagination';
}
if ( ! $items_have_ratio AND us_arr_path( $grid_layout_settings, 'default.options.overflow' ) ) {
	$grid_atts['class'] .= ' overflow_hidden';
}
if ( $overriding_link == 'popup_post' ) {
	$grid_atts['class'] .= ' popup_page';
}

if ( $filter_html ) {
	$grid_atts['class'] .= ' with_filters';
}

// Add "object-fit" script fix for IE11
if ( ! us_get_option( 'ajax_load_js', 0 ) ) {
	wp_enqueue_script( 'us-objectfit' );
}

// Apply isotope script for Masonry
if ( $type === 'masonry' AND $columns > 1 ) {
	if ( ! us_get_option( 'ajax_load_js', 0 ) ) {
		wp_enqueue_script( 'us-isotope' );
	}
	$grid_atts['class'] .= ' with_isotope';
}

// Apply items appearance animation on loading
if ( $load_animation !== 'none' ) {
	$grid_atts['class'] .= ' with_css_animation';
}

// Output attributes for Carousel type
$list_classes = $current_grid_css = '';
if ( $type == 'carousel' ) {
	if ( ! us_get_option( 'ajax_load_js', 0 ) ) {
		wp_enqueue_script( 'us-owl' );
	}

	$list_classes .= ' owl-carousel';
	$list_classes .= ' navstyle_' . $carousel_arrows_style;
	$list_classes .= ' navpos_' . $carousel_arrows_pos;
	if ( $carousel_dots ) {
		$list_classes .= ' with_dots';
	}
	if ( $columns == 1 AND $carousel_autoheight ) {
		$list_classes .= ' autoheight';
	}

	// Customize Carousel Arrows for current listing only
	if ( $carousel_arrows ) {
		if ( ! empty( $carousel_arrows_size ) ) {
			$current_grid_css .= '#' . $grid_elm_id . ' .owl-nav div { font-size: ' . strip_tags( $carousel_arrows_size ) . '}';
		}
		if ( ! empty( $carousel_arrows_offset ) ) {
			$current_grid_css .= '#' . $grid_elm_id . ' .owl-nav div { margin-left: ' . strip_tags( $carousel_arrows_offset ) . '; margin-right: ' . strip_tags( $carousel_arrows_offset ) . '}';
		}
	}
}

// Generate items gap via CSS
if ( ! empty( $items_gap ) ) {
	if ( $columns != 1 ) {
		$current_grid_css .= '#' . $grid_elm_id . ' .w-grid-item { padding: ' . $items_gap . '}';

		if ( ! empty( $filter_html ) AND $pagination == 'none' ) {
			$current_grid_css .= '#' . $grid_elm_id . ' .w-grid-list { margin: ' . $items_gap . ' -' . $items_gap . ' -' . $items_gap . '}';
		}
		if ( ! empty( $filter_html ) AND $pagination != 'none' ) {
			$current_grid_css .= '#' . $grid_elm_id . ' .w-grid-list { margin: ' . $items_gap . ' -' . $items_gap . '}';
		}
		if ( empty( $filter_html ) AND $pagination != 'none' ) {
			$current_grid_css .= '#' . $grid_elm_id . ' .w-grid-list { margin: -' . $items_gap . ' -' . $items_gap . ' ' . $items_gap . '}';
		}
		if ( empty( $filter_html ) AND $pagination == 'none' ) {
			$current_grid_css .= '#' . $grid_elm_id . ' .w-grid-list { margin: -' . $items_gap . '}';
		}

		// Force gap between neighbour "w-grid" elements
		$current_grid_css .= '.w-grid + #' . $grid_elm_id . ' .w-grid-list { margin-top: ' . $items_gap . '}';
	} elseif ( $type != 'carousel' ) {
		$current_grid_css .= '#' . $grid_elm_id . ' .w-grid-item:not(:last-child) { margin-bottom: ' . $items_gap . '}';
		$current_grid_css .= '#' . $grid_elm_id . ' .g-loadmore { margin-top: ' . $items_gap . '}';
	}
} else {
	$grid_atts['class'] .= ' no_gap';
}

// Generate columns responsive CSS for 3 breakpoints
if ( ! in_array( $type, array( 'carousel', 'metro' ) ) AND ! $is_widget ) {
	for ( $i = 1; $i < 4; $i ++ ) {
		$responsive_cols = intval( ${'breakpoint_' . $i . '_cols'} );
		$responsive_cols = ( $responsive_cols !== 0 ) ? $responsive_cols : $default_grid_params[ 'breakpoint_' . $i . '_cols' ];
		$responsive_width = intval( ${'breakpoint_' . $i . '_width'} );

		if ( $columns > $responsive_cols ) {
			$current_grid_css .= '@media (max-width:' . ( $responsive_width - 1 ) . 'px) {';
			if ( $responsive_cols == 1 AND ! empty( $items_gap ) ) {
				$current_grid_css .= '#' . $grid_elm_id . ' .w-grid-list { margin: 0 }';
			}
			$current_grid_css .= '#' . $grid_elm_id . ' .w-grid-item { width:' . number_format( 100 / $responsive_cols, 4 ) . '%;';
			if ( $responsive_cols == 1 AND ! empty( $items_gap ) ) {
				$current_grid_css .= 'padding: 0; margin-bottom: ' . $items_gap;
			}
			$current_grid_css .= '}';
			if ( $responsive_cols != 1 AND $items_have_ratio AND ! $ignore_items_size ) {
				$current_grid_css .= '#' . $grid_elm_id . ' .w-grid-item.size_2x1,';
				$current_grid_css .= '#' . $grid_elm_id . ' .w-grid-item.size_2x2 {';
				$current_grid_css .= 'width:' . number_format( 200 / $responsive_cols, 4 ) . '% }';
			}
			$current_grid_css .= '}';
		}
	}
}

// Add Post Title font-size for current Grid only
if ( trim( $title_size ) != '' ) {
	$current_grid_css .= '@media (min-width:' . us_get_option( 'tablets_breakpoint', '1024px' ) . ') {';
	$current_grid_css .= '#' . $grid_elm_id . ' .w-post-elm.post_title { font-size: ' . strip_tags( $title_size ) . ' !important }';
	$current_grid_css .= '}';
}

$grid_layout_css = '';

// Generate CSS for items Aspect Ratio
if ( $items_have_ratio ) {

	// Always calculate Aspect Ratio of used Grid Layout to add it into common css
	$layout_ratio = us_arr_path( $grid_layout_settings, 'default.options.ratio' );
	$layout_ratio_width = us_arr_path( $grid_layout_settings, 'default.options.ratio_width' );
	$layout_ratio_height = us_arr_path( $grid_layout_settings, 'default.options.ratio_height' );

	$ratio_array = us_get_aspect_ratio_values( $layout_ratio, $layout_ratio_width, $layout_ratio_height );

	$grid_layout_css .= '.layout_' . $items_layout . ' .w-grid-item-h:before {';
	$grid_layout_css .= 'padding-bottom:' . number_format( $ratio_array[1] / $ratio_array[0] * 100, 4 ) . '% }';

	// Fix aspect ratio regarding meta custom size and items gap
	if ( empty( $items_gap ) ) {
		$items_gap = '0px'; // needed for CSS calc function
	}
	if ( $type != 'carousel' AND ! $is_widget AND ! $ignore_items_size ) {
		$grid_layout_css .= '@media (min-width:' . intval( $breakpoint_3_width ) . 'px) {';
		$grid_layout_css .= '.layout_' . $items_layout . ' .w-grid-item.size_1x2 .w-grid-item-h:before {';
		$grid_layout_css .= 'padding-bottom: calc(' . number_format( ( $ratio_array[1] * 2 ) / $ratio_array[0] * 100, 4 ) . '% + ' . $items_gap . ' + ' . $items_gap . ')}';
		$grid_layout_css .= '.layout_' . $items_layout . ' .w-grid-item.size_2x1 .w-grid-item-h:before {';
		$grid_layout_css .= 'padding-bottom: calc(' . number_format( $ratio_array[1] / ( $ratio_array[0] * 2 ) * 100, 4 ) . '% - ' . $items_gap . ' * ' . number_format( $ratio_array[1] / $ratio_array[0], 4 ) . ')}';
		$grid_layout_css .= '.layout_' . $items_layout . ' .w-grid-item.size_2x2 .w-grid-item-h:before {';
		$grid_layout_css .= 'padding-bottom: calc(' . number_format( $ratio_array[1] / $ratio_array[0] * 100, 4 ) . '% - ' . $items_gap . ' * ' . 2 * ( number_format( $ratio_array[1] / $ratio_array[0] - 1, 4 ) ) . ')}';
		$grid_layout_css .= '}';
	}

	// If Aspect Ratio is overriding by current Grid, add relevant css into current element only
	if ( $items_ratio != 'default' ) {
		$ratio_array = us_get_aspect_ratio_values( $items_ratio, $items_ratio_width, $items_ratio_height );

		$current_grid_css .= '#' . $grid_elm_id . ' .w-grid-item-h:before {';
		$current_grid_css .= 'padding-bottom:' . number_format( $ratio_array[1] / $ratio_array[0] * 100, 4 ) . '% }';

		$grid_atts['class'] .= ' ratio_' . $items_ratio;
	} else {
		$grid_atts['class'] .= ' ratio_' . $layout_ratio;
	}
}

// Generate Grid Layout CSS, if it doesn't previously added
if ( ! in_array( $items_layout, $us_grid_layouts ) ) {
	$item_bg_color = us_arr_path( $grid_layout_settings, 'default.options.color_bg' );
	$item_bg_color = us_get_color( $item_bg_color, /* Gradient */ TRUE );
	$item_text_color = us_arr_path( $grid_layout_settings, 'default.options.color_text' );
	$item_text_color = us_get_color( $item_text_color );
	$item_bg_img_source = us_arr_path( $grid_layout_settings, 'default.options.bg_img_source' );
	$item_border_radius = floatval( us_arr_path( $grid_layout_settings, 'default.options.border_radius' ) );
	$item_box_shadow = floatval( us_arr_path( $grid_layout_settings, 'default.options.box_shadow' ) );
	$item_box_shadow_hover = floatval( us_arr_path( $grid_layout_settings, 'default.options.box_shadow_hover' ) );

	// Generate Background Image output
	if ( $item_bg_img_source == 'media' ) {
		$item_bg_img_arr = explode( '|', us_arr_path( $grid_layout_settings, 'default.options.bg_img' ) );

		$item_bg_img = 'url(' . wp_get_attachment_image_url( $item_bg_img_arr[0], 'full' ) . ') ';
		$item_bg_img .= us_arr_path( $grid_layout_settings, 'default.options.bg_img_position' );
		$item_bg_img .= '/';
		$item_bg_img .= us_arr_path( $grid_layout_settings, 'default.options.bg_img_size' );
		$item_bg_img .= ' ';
		$item_bg_img .= us_arr_path( $grid_layout_settings, 'default.options.bg_img_repeat' );

		// If the color value contains gradient, add comma for correct appearance
		if ( strpos( $item_bg_color, 'gradient' ) !== FALSE ) {
			$item_bg_img .= ',';
		}
	} else {
		$item_bg_img = '';
	}

	$grid_layout_css .= '.layout_' . $items_layout . ' .w-grid-item-h {';
	if ( $item_bg_img != '' OR $item_bg_color != '' ) {
		$grid_layout_css .= 'background:' . $item_bg_img . ' ' . $item_bg_color . ';';
	}
	if ( ! empty( $item_text_color ) ) {
		$grid_layout_css .= 'color:' . $item_text_color . ';';
	}
	if ( ! empty( $item_border_radius ) ) {
		$grid_layout_css .= 'border-radius:' . $item_border_radius . 'rem;';
		$grid_layout_css .= 'z-index: 3;';
	}
	if ( ! empty( $item_box_shadow ) OR ! empty( $item_box_shadow_hover ) ) {
		$grid_layout_css .= 'box-shadow:';
		$grid_layout_css .= '0 ' . number_format( $item_box_shadow / 10, 2 ) . 'rem ' . number_format( $item_box_shadow / 5, 2 ) . 'rem rgba(0,0,0,0.1),';
		$grid_layout_css .= '0 ' . number_format( $item_box_shadow / 3, 2 ) . 'rem ' . number_format( $item_box_shadow, 2 ) . 'rem rgba(0,0,0,0.1);';
		$grid_layout_css .= 'transition-duration: 0.3s;';
	}
	$grid_layout_css .= '}';
	if ( $item_box_shadow_hover != $item_box_shadow AND ! us_amp() ) {
		$grid_layout_css .= '.no-touch .layout_' . $items_layout . ' .w-grid-item-h:hover { box-shadow:';
		$grid_layout_css .= '0 ' . number_format( $item_box_shadow_hover / 10, 2 ) . 'rem ' . number_format( $item_box_shadow_hover / 5, 2 ) . 'rem rgba(0,0,0,0.1),';
		$grid_layout_css .= '0 ' . number_format( $item_box_shadow_hover / 3, 2 ) . 'rem ' . number_format( $item_box_shadow_hover, 2 ) . 'rem rgba(0,0,0,0.15);';
		$grid_layout_css .= 'z-index: 4;';
		$grid_layout_css .= '}';
	}

	// Generate Grid Layout elements CSS
	$grid_jsoncss_collection = array();
	foreach ( $grid_layout_settings['data'] as $elm_id => $elm ) {

		$elm_class = 'usg_' . str_replace( ':', '_', $elm_id );

		// CSS of Hover effects
		if ( ! empty( $elm['hover'] ) ) {
			$grid_layout_css .= '.layout_' . $items_layout . ' .' . $elm_class . '{';
			$grid_layout_css .= isset( $elm['transition_duration'] ) ? 'transition-duration:' . $elm['transition_duration'] . ';' : '';
			if ( isset( $elm['transform_origin_X'] ) AND isset( $elm['transform_origin_Y'] ) ) {
				$grid_layout_css .= 'transform-origin: ' . $elm['transform_origin_X'] . ' ' . $elm['transform_origin_Y'] . ';';
			}
			if ( isset( $elm['scale'] ) AND isset( $elm['translateX'] ) AND isset( $elm['translateY'] ) ) {
				$grid_layout_css .= 'transform: scale(' . $elm['scale'] . ') translate(' . $elm['translateX'] . ',' . $elm['translateY'] . ');';
			}
			$grid_layout_css .= ( isset( $elm['opacity'] ) AND intval( $elm['opacity'] ) != 1 ) ? 'opacity:' . $elm['opacity'] . ';' : '';
			$grid_layout_css .= '}';

			// Generate hover styles for not AMP only
			if ( ! us_amp() ) {
				$grid_layout_css .= '.layout_' . $items_layout . ' .w-grid-item-h:hover .' . $elm_class . '{';
				if ( isset( $elm['scale_hover'] ) AND isset( $elm['translateX_hover'] ) AND isset( $elm['translateY_hover'] ) ) {
					$grid_layout_css .= 'transform: scale(' . $elm['scale_hover'] . ') translate(' . $elm['translateX_hover'] . ',' . $elm['translateY_hover'] . ');';
				}
				$grid_layout_css .= isset( $elm['opacity_hover'] ) ? 'opacity:' . $elm['opacity_hover'] . ';' : '';

				if ( $color_bg_hover = us_arr_path( $elm, 'color_bg_hover', FALSE ) ) {
					$grid_layout_css .= sprintf( 'background: %s !important;', us_get_color( $color_bg_hover, /* Gradient */ TRUE ) );
				}
				if ( $color_border_hover = us_arr_path( $elm, 'color_border_hover', FALSE ) ) {
					$grid_layout_css .= sprintf( 'border-color: %s !important;', us_get_color( $color_border_hover ) );
				}
				if ( $color_text_hover = us_arr_path( $elm, 'color_text_hover', FALSE ) ) {
					$grid_layout_css .= sprintf( 'color: %s !important;', us_get_color( $color_text_hover ) );
				}

				$grid_layout_css .= '}';
			}
		}

		// Hide regarding 2 screen width breakpoints
		$elm_hide_below = isset( $elm['hide_below'] ) ? intval( $elm['hide_below'] ) : 0;
		$elm_hide_above = isset( $elm['hide_above'] ) ? intval( $elm['hide_above'] ) : 0;
		if ( ! empty( $elm_hide_below ) OR ! empty( $elm_hide_above ) ) {
			$grid_layout_css .= '@media';
			if ( $elm_hide_above ) {
				$grid_layout_css .= '(min-width:' . ( $elm_hide_above + 1 ) . 'px)';
			}
			if ( $elm_hide_above AND $elm_hide_below ) {
				$grid_layout_css .= ( $elm_hide_below > $elm_hide_above ) ? ' and ' : ' or ';
			}
			if ( $elm_hide_below ) {
				$grid_layout_css .= '(max-width:' . ( $elm_hide_below - 1 ) . 'px)';
			}
			$grid_layout_css .= '{';
			$grid_layout_css .= '.layout_' . $items_layout . ' .' . $elm_class . '{ display: none !important; }';
			$grid_layout_css .= '}';
		}

		// CSS Design Options
		if ( ! empty( $elm['css'] ) AND is_array( $elm['css'] ) ) {
			foreach ( array( 'default', 'tablets', 'mobiles' ) as $device_type ) {
				if ( $css_options = us_arr_path( $elm, 'css.' . $device_type, FALSE ) ) {
					$css_options = apply_filters( 'us_output_design_css_options', $css_options, $device_type );
					$grid_jsoncss_collection[ $device_type ][ 'layout_' . $items_layout . ' .' . $elm_class ] = $css_options;
				}
			}
		}
	}

	$grid_layout_css .= us_jsoncss_compile( $grid_jsoncss_collection );
}

// Define if the Grid is available for filtering via Grid Filter and sorting via Grid Order
global $us_context_layout;
if (
	! $filter_html
	AND $type !== 'carousel'
	AND ( $us_context_layout === 'main' OR ( is_null( $us_context_layout ) AND $us_grid_index === 1 ) )
) {
	if ( is_archive() ) {
		$grid_atts['data-filterable'] = 'true';
	} elseif ( ! us_post_type_is_available( $post_type, array(
		'ids',
		'ids_terms',
		'taxonomy_terms',
		'current_child_terms',
	) ) ) {
		$grid_atts['data-filterable'] = 'true';
	}
}

// Output the Grid semantics
echo '<div ' . us_implode_atts( $grid_atts ) .'>';

// Add CSS customizations for the current Grid only
if ( ! empty( $current_grid_css ) ) {
	echo '<style id="' . $grid_elm_id . '_css">' . us_minify_css( $current_grid_css ) . '</style>';
}

// Add Grid Layout CSS, if it doesn't previously added
if ( ! in_array( $items_layout, $us_grid_layouts ) ) {
	$us_grid_layouts[] = $items_layout;
	echo '<style>' . us_minify_css( $grid_layout_css ) . '</style>';
}

echo $filter_html;
echo '<div class="w-grid-list' . $list_classes . '" '. $data_atts .'>';
