<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: vc_row
 *
 * Overloaded by UpSolution custom implementation to allow creating fullwidth sections and provide lots of additional
 * features.
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode                    string Current shortcode name
 * @var $shortcode_base               string The original called shortcode name (differs if called an alias)
 * @var $content                      string Shortcode's inner content
 * @var $content_placement            string Columns Content Position: 'top' / 'middle' / 'bottom'
 * @var $gap                          string gap class for columns
 * @var $height                       string Height type. Possible values: 'default' / 'small' / 'medium' / 'large' / 'huge' / 'auto' /  'full'
 * @var $valign                       string Vertical align for full-height sections: '' / 'center'
 * @var $width                        string Section width: '' / 'full'
 * @var $color_scheme                 string Color scheme: '' / 'alternate' / 'primary' / 'secondary' / 'custom'
 * @var $us_bg_image_source           string Background image source: 'none' / 'media' / 'featured' / 'custom'
 * @var $us_bg_image                  int Background image ID (from WordPress media)
 * @var $us_bg_size                   string Background size: 'cover' / 'contain' / 'initial'
 * @var $us_bg_repeat                 string Background size: 'repeat' / 'repeat-x' / 'repeat-y' / 'no-repeat'
 * @var $us_bg_pos                    string Background position: 'top left' / 'top center' / 'top right' / 'center left' / 'center center' / 'center right' /  'bottom left' / 'bottom center' / 'bottom right'
 * @var $us_bg_parallax               string Parallax type: '' / 'vertical' / 'horizontal' / 'still'
 * @var $us_bg_parallax_width         string Parallax background width: '110' / '120' / '130' / '140' / '150'
 * @var $us_bg_parallax_reverse       bool Reverse vertival parllax effect?
 * @var $us_bg_video                  string Link to video file
 * @var $us_bg_overlay_color          string
 * @var $sticky                       bool Fix this row at the top of a page during scroll
 * @var $sticky_disable_width         int When screen width is less than this value, sticky row becomes not sticky
 * @var $us_bg_video_disable_width    int When screen width is less than this value, video will be replaced with background image
 * @var $el_id                        string
 * @var $el_class                     string
 * @var $disable_element              string
 * @var $css                          string
 * @var $us_shape_show_top            string Is display Shape top Divider value '1' / '0'
 * @var $us_shape_show_bottom         string Is display Shape bottom Shape Divider value '1' / '0'
 * @var $us_shape_top                 string Shape Divider type: 'curve' / 'triangle'
 * @var $us_shape_bottom              string Shape Divider type: 'curve' / 'triangle'
 * @var $us_shape_custom_top          string Shape Divider id of media attached file
 * @var $us_shape_custom_bottom       string Shape Divider id of media attached file
 * @var $us_shape_height_top          string Shape Divider height in vh '15vh' / '25vh'
 * @var $us_shape_height_bottom       string Shape Divider height in vh '15vh' / '25vh'
 * @var $us_shape_color_top           string Shape Divider color
 * @var $us_shape_color_bottom        string Shape Divider color
 * @var $us_shape_overlap_top         string Shape Divider on front or no
 * @var $us_shape_overlap_bottom      string Shape Divider on front or no
 * @var $us_shape_flip_top            string Shape Divider invert layout
 * @var $us_shape_flip_bottom         string Shape Divider invert layout
 * @var $classes                      string Extend class names
 *
 * Deprecated params but they need for compatibility with new one
 * @var $us_shape                     string Shape Divider type: 'curve' / 'triangle'
 * @var $us_shape_height              string Shape Divider height in vh '15vh' / '25vh'
 * @var $us_shape_position            string Shape Divider position: 'top' / 'bottom'
 * @var $us_shape_color               string Shape Divider color
 * @var $us_shape_overlap             string Shape Divider on front or no
 * @var $us_shape_flip                string Shape Divider invert layout
 *
 * @var $us_shape_bring_to_front string Bring to front element
 */

// Check the inner content for Page Blocks ans Post Content with the parent Row excluded,
// if so, output these Page Blocks and Post Content only
if ( preg_match_all( '/\[(us_\w+)\s(.*remove_rows="parent_row"?[^\]]+)\]/', $content, $shortcode_matches ) ) {
	$new_content = '';
	$shortcodes_regex = get_shortcode_regex( $shortcode_matches[1] );

	if ( preg_match_all( '/' . $shortcodes_regex . '/', $content, $matches, PREG_PATTERN_ORDER ) ) {
		foreach ( us_arr_path( $matches, '0', array() ) as $item_shortcode ) {
			if ( strpos( $item_shortcode, 'remove_rows="parent_row"' ) !== FALSE ) {
				$new_content .= $item_shortcode;
			}
		}
	}
	echo do_shortcode( $new_content );

	return;
}

$atts = us_shortcode_atts( $atts, $shortcode_base );
$classes = isset( $classes ) ? $classes : '';

if ( $disable_element === 'yes' ) {
	if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) {
		$classes .= ' vc_hidden-lg vc_hidden-xs vc_hidden-sm vc_hidden-md';
	} else {
		return '';
	}
}

if ( $height == 'default' ) {
	$classes .= ' height_' . us_get_option( 'row_height', 'medium' );
} else {
	$classes .= ' height_' . $height;
}
if ( $height == 'full' AND ! empty( $valign ) ) {
	$classes .= ' valign_' . $valign;
}
if ( $width == 'full' ) {
	$classes .= ' width_full';
}
if ( $color_scheme != '' ) {
	$classes .= ' color_' . $color_scheme;
}
if ( $sticky ) {
	$classes .= ' type_sticky';
}
if ( ! empty( $el_class ) ) {
	$classes .= ' ' . $el_class;
}

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$classes .= ' has_text_color';
}

// Generate Background Image output
// Media library source
if ( $us_bg_image_source == 'media' ) {
	$image_src = wp_get_attachment_image_src( $us_bg_image, 'full' );

	// Use placeholder, if the specified image doesn't exist
	if ( ! empty( $us_bg_image ) AND ! $image_src ) {
		$bg_image_url = us_get_img_placeholder( 'full', TRUE );
	}

	// Featured image source
} elseif (
	$us_bg_image_source == 'featured'
	AND (
		isset( $GLOBALS['post'] )
		OR is_404()
		OR is_search()
		OR is_archive()
		OR (
			is_home()
			AND ! have_posts()
		)
	)
) {
	$us_layout = US_Layout::instance();
	if ( ! empty( $us_layout->post_id ) ) {
		$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $us_layout->post_id ), 'full' );

		// Get WooCommerce Product Category term image
	} elseif ( class_exists( 'woocommerce' ) AND is_product_category() ) {

		if ( $term_thumbnail_id = get_term_meta( get_queried_object_id(), 'thumbnail_id', TRUE ) ) {
			$image_src = wp_get_attachment_image_src( $term_thumbnail_id, 'full' );
		}
	}

	// Custom field image source
} elseif (
	$us_bg_image_source != 'none'
	AND $object_id = get_the_ID()
) {
	// ACF Custom Image
	if (
		function_exists( 'acf_get_field' )
		AND $acf_field = acf_get_field( $us_bg_image_source )
		AND us_arr_path( $acf_field, 'type', '' ) === 'image'
	) {
		$object_id = get_queried_object_id();
	}

	$meta_type = is_archive()
		? 'term'
		: 'post';

	$_value = get_metadata( $meta_type, $object_id, $us_bg_image_source, TRUE );
	$image_src = wp_get_attachment_image_src( $_value, 'full' );
}

// Get background image attributes
$bg_img_atts = '';
if ( isset( $image_src ) AND $image_src ) {
	$bg_image_url = $image_src[0];
	$bg_img_atts = ' data-img-width="' . esc_attr( $image_src[1] ) . '" data-img-height="' . esc_attr( $image_src[2] ) . '"';
}

// Generate background block, if the image exists
$bg_image_html = '';
if ( ! empty( $bg_image_url ) ) {
	$classes .= ' with_img';
	$bg_image_inline_css = 'background-image: url(' . esc_url( $bg_image_url ) . ');';
	if ( $us_bg_pos != 'center center' ) {
		$bg_image_inline_css .= 'background-position: ' . $us_bg_pos . ';';
	}
	if ( $us_bg_repeat != 'repeat' ) {
		$bg_image_inline_css .= 'background-repeat: ' . $us_bg_repeat . ';';
	}
	if ( $us_bg_size == 'initial' ) {
		$bg_image_inline_css .= 'background-size: auto;'; // fix for IE11, which doesn't support "background-size: initial"
	} elseif ( $us_bg_size != 'cover' ) {
		$bg_image_inline_css .= 'background-size: ' . $us_bg_size . ';';
	}
	$bg_image_additional_class = ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) ? ' loaded' : '';
	$bg_image_html = '<div class="l-section-img' . $bg_image_additional_class . '" style="' . $bg_image_inline_css . '"' . $bg_img_atts . '></div>';
}


$bg_slider_html = $bg_video_html = '';
if ( ! us_amp() ) {
	// Background Video
	if ( $us_bg_show == 'video' AND $us_bg_video != '' ) {
		$classes .= ' with_video';
		$provider_matched = FALSE;

		$bg_video_html .= '<div class="l-section-video"';

		// Add data to hide video on the screen width via JS
		if ( $us_bg_video_disable_width ) {
			$bg_video_html .= ' data-video-disable-width="' . intval( $us_bg_video_disable_width ) . '"';
		}
		$bg_video_html .= '>';

		foreach ( us_config( 'embeds' ) as $provider => $embed ) {
			if ( $embed['type'] != 'video' OR ! preg_match( $embed['regex'], $us_bg_video, $matches ) ) {
				continue;
			}
			$provider_matched = TRUE;
			$video_id = $matches[ $embed['match_index'] ];
			if ( $provider == 'youtube' ) {
				$classes .= ' with_youtube';
				$video_params = '?autoplay=1&loop=1&playlist=' . $video_id . '&controls=0&mute=1&iv_load_policy=3&disablekb=1&wmode=transparent';
			} elseif ( $provider == 'vimeo' ) {
				$classes .= ' with_vimeo';
				$video_params = '?autoplay=1&loop=1&muted=1&title=0&byline=0&background=1';
			}
			$embed_html = str_replace( '<id>', $matches[ $embed['match_index'] ], $embed['html'] );
			$embed_html = str_replace( '<url_params>', $video_params, $embed_html );
			break;
		}
		if ( $provider_matched ) {
			$bg_video_html .= $embed_html;
		} else {
			$bg_video_html .= '<video muted loop autoplay playsinline preload="auto">';
			$video_ext = 'mp4'; //use mp4 as default extension
			$file_path_info = pathinfo( $us_bg_video );
			if ( isset( $file_path_info['extension'] ) ) {
				if ( in_array( $file_path_info['extension'], array( 'ogg', 'ogv' ) ) ) {
					$video_ext = 'ogg';
				} elseif ( $file_path_info['extension'] == 'webm' ) {
					$video_ext = 'webm';
				}
			}
			$bg_video_html .= '<source type="video/' . $video_ext . '" src="' . $us_bg_video . '" />';
			$bg_video_html .= '</video>';
		}
		$bg_video_html .= '</div>';
	} else {
		if ( $us_bg_parallax == 'vertical' ) {
			$classes .= ' parallax_ver';
			if ( $us_bg_parallax_reverse ) {
				$classes .= ' parallaxdir_reversed';
			}
			if ( in_array( $us_bg_pos, array( 'top right', 'center right', 'bottom right' ) ) ) {
				$classes .= ' parallax_xpos_right';
			} elseif ( in_array( $us_bg_pos, array( 'top left', 'center left', 'bottom left' ) ) ) {
				$classes .= ' parallax_xpos_left';
			}
		} elseif ( $us_bg_parallax == 'fixed' OR $us_bg_parallax == 'still' ) {
			$classes .= ' parallax_fixed';
		} elseif ( $us_bg_parallax == 'horizontal' ) {
			$classes .= ' parallax_hor';
			$classes .= ' bgwidth_' . $us_bg_parallax_width;
		}
	}

	// Image Slider
	if ( $us_bg_show == 'img_slider' AND ! empty( $us_bg_slider_ids ) AND ! us_amp() ) {
		$classes .= ' with_slider';
		$img_slider_shortcode = '[us_image_slider';
		$img_slider_shortcode .= ' ids="' . $us_bg_slider_ids . '"';
		$img_slider_shortcode .= ' transition="' . $us_bg_slider_transition . '"';
		$img_slider_shortcode .= ' transition_speed="' . $us_bg_slider_speed . '"';
		$img_slider_shortcode .= ' autoplay_period="' . $us_bg_slider_interval . '"';
		$img_slider_shortcode .= ' arrows="hide" autoplay="1" pause_on_hover="" img_size="full" img_fit="cover"]';

		$bg_slider_html = '<div class="l-section-slider">' . do_shortcode( $img_slider_shortcode ) . '</div>';

		// Revolution Slider
	} elseif ( $us_bg_show == 'rev_slider' AND class_exists( 'RevSlider' ) ) {
		$classes .= ' with_slider';
		$bg_slider_html = '<div class="l-section-slider">' . do_shortcode( '[rev_slider ' . $us_bg_rev_slider . ']' ) . '</div>';
	}
}


// Background Overlay
$bg_overlay_html = '';
if ( ! empty( $us_bg_overlay_color ) AND $us_bg_overlay_color = us_get_color( $us_bg_overlay_color, /* Gradient */ TRUE ) ) {
	$classes .= ' with_overlay';
	$bg_overlay_html = '<div class="l-section-overlay" style="background: ' . $us_bg_overlay_color . '"></div>';
}

$classes = apply_filters( 'vc_shortcodes_css_class', $classes, $shortcode_base, $atts );
if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) {
	$classes .= ' vc_row';
}
$defaults_atts = us_config( 'shortcodes.modified.vc_row.atts', array() );

// Shape Divider
$bg_shape_html = '';

/*
 * Compatibility old shape params with new (after version 7.1)
 */
if (
	empty( $us_shape_show_top )
	AND empty( $us_shape_show_bottom )
	AND ! empty( $us_shape_position )
	AND ! empty( $us_shape )
	AND ( $us_shape != 'none' )
) {
	${'us_shape_show_' . $us_shape_position} = 1;
	${'us_shape_' . $us_shape_position} = $us_shape;

	if (
		${'us_shape_height_' . $us_shape_position} == $defaults_atts[ 'us_shape_height_' . $us_shape_position ]
		AND $us_shape_height != $defaults_atts['us_shape_height']
	) {
		${'us_shape_height_' . $us_shape_position} = $us_shape_height;
	}
	if (
		${'us_shape_color_' . $us_shape_position} == $defaults_atts[ 'us_shape_color_' . $us_shape_position ]
		AND $us_shape_color != $defaults_atts['us_shape_color']
	) {
		${'us_shape_color_' . $us_shape_position} = $us_shape_color;
	}
	if (
		${'us_shape_overlap_' . $us_shape_position} == $defaults_atts[ 'us_shape_overlap_' . $us_shape_position ]
		AND $us_shape_overlap != $defaults_atts['us_shape_overlap']
	) {
		${'us_shape_overlap_' . $us_shape_position} = $us_shape_overlap;
	}
	if (
		${'us_shape_flip_' . $us_shape_position} == $defaults_atts[ 'us_shape_flip_' . $us_shape_position ]
		AND $us_shape_flip != $defaults_atts['us_shape_flip']
	) {
		${'us_shape_flip_' . $us_shape_position} = $us_shape_flip;
	}

}
if ( $us_shape_show_top OR $us_shape_show_bottom ) {
	$positions = array();
	if ( $us_shape_show_top ) {
		$positions[] = 'top';
	}
	if ( $us_shape_show_bottom ) {
		$positions[] = 'bottom';
	}

	$classes .= ' with_shape';

	foreach ( $positions as $position ) {

		// If checkbox checked for current position (top or bottom) generate shape html
		if ( ${'us_shape_show_' . $position} ) {
			${'svg_' . $position} = $shape_html = '';

			// Use custom file, if it was uploaded in Row settings
			if ( ${'us_shape_' . $position} == 'custom' AND ! empty( ${'us_shape_custom_' . $position} ) ) {
				$shape_id = ${'us_shape_custom_' . $position};

				// Get file MIME type to handle SVGs separately
				$mime_type = get_post_mime_type( $shape_id );
				if ( strpos( $mime_type, 'svg' ) !== FALSE ) {
					$svg_filepath = get_attached_file( $shape_id );
				} else {
					$shape_html = wp_get_attachment_image( intval( $shape_id ), 'full' );
				}

			} else {

				// Use built-in shapes
				$svg_filepath = sprintf( '%s/assets/shapes/%s.svg', US_CORE_DIR, ${'us_shape_' . $position} );
			}

			// In case SVG is valid, use its content as shape html
			if ( isset( $svg_filepath ) AND $svg_filepath = realpath( $svg_filepath ) ) {
				$shape_html = file_get_contents( $svg_filepath );
			}

			// CSS Classes for
			${'shape_classes_' . $position} = array(
				'l-section-shape',
				'type_' . esc_attr( ${'us_shape_' . $position} ),
				"pos_{$position}",
			);

			if ( ${'us_shape_overlap_' . $position} ) {
				${'shape_classes_' . $position}[] = 'on_front';
			}
			if ( ${'us_shape_flip_' . $position} ) {
				${'shape_classes_' . $position}[] = 'hor_flip';
			}

			// Height and color
			$svg_inline_css_data = array( 'color' => '', 'height' => '' );
			if ( ${'us_shape_height_' . $position} !== $defaults_atts["us_shape_height_{$position}"] ) {
				$svg_inline_css_data['height'] = ${'us_shape_height_' . $position};
			}
			if ( us_get_color( ${'us_shape_color_' . $position} ) !== $defaults_atts["us_shape_color_{$position}"] ) {
				$svg_inline_css_data['color'] = us_get_color( ${'us_shape_color_' . $position} );
			}
			${'svg_inline_css_' . $position} = us_prepare_inline_css( $svg_inline_css_data );

			$bg_shape_html .= '<div class="' . implode( ' ', ${'shape_classes_' . $position} ) . '"';
			$bg_shape_html .= ${'svg_inline_css_' . $position};
			$bg_shape_html .= '>';
			$bg_shape_html .= $shape_html;
			$bg_shape_html .= '</div>';
		}
	}

}

// Output the element
$output = '<section class="l-section wpb_row' . $classes . '"';
if ( ! empty( $el_id ) ) {
	$output .= ' id="' . $el_id . '"';
}
$output .= '>';

$output .= $bg_image_html;
$output .= $bg_video_html;
$output .= $bg_slider_html;
$output .= $bg_overlay_html;
$output .= $bg_shape_html;

$output .= '<div class="l-section-h i-cf">';

$inner_output = do_shortcode( $content );

// If the row has no inner rows, preparing wrapper for inner columns
if ( substr( $inner_output, 0, 18 ) != '<div class="g-cols' ) {

	$cols_gap_styles = '';
	$cols_class_name = ( $columns_type ) ? ' type_boxes' : ' type_default';

	if ( ! empty( $content_placement ) ) {
		$cols_class_name .= ' valign_' . $content_placement;
	}
	if ( ! empty( $columns_reverse ) ) {
		$cols_class_name .= ' reversed';
	}

	// Prepare extra styles for columns gap
	$gap = trim( $gap );
	if ( ! empty( $gap ) ) {
		$gap = trim( strip_tags( $gap ) );
		$gap_class = 'gap-' . str_replace( array( '.', ',', ' ' ), '-', $gap );
		$cols_class_name .= ' ' . $gap_class;

		$cols_gap_styles = '<style>';
		if ( $columns_type ) {
			$cols_gap_styles .= '.g-cols.' . $gap_class . '{margin:0 -' . $gap . '}';
		} else {
			$cols_gap_styles .= '.g-cols.' . $gap_class . '{margin:0 calc(-1.5rem - ' . $gap . ')}';
		}
		$cols_gap_styles .= '.' . $gap_class . ' > .vc_column_container {padding:' . $gap . '}';
		$cols_gap_styles .= '</style>';
	}

	$output .= '<div class="g-cols vc_row' . $cols_class_name . '">';
	$output .= $cols_gap_styles . $inner_output;
	$output .= '</div>';
} else {
	$output .= $inner_output;
}

$output .= '</div>';

$output .= '</section>';

echo $output;
