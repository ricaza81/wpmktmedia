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
 * @var $columns_gap                  string gap class for columns
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
 * @var $_atts['class']                      string Extend class names
 *
 * @var $us_shape_bring_to_front string Bring to front element
 */

// Check the inner content for Page Blocks ans Post Content with the parent Row excluded,
// if so, output these Page Blocks and Post Content only
if (
	preg_match_all( '/\[(us_\w+)\s(.*remove_rows="parent_row"?[^\]]+)\]/', $content, $shortcode_matches )
	AND ! apply_filters( 'usb_is_preview_page', NULL )
) {
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

// Fallback for old full height value (after version 8.0)
if ( $height == 'full' ) {
	$full_height = TRUE;
	$height = 'medium';

	if ( ! empty( $atts['valign'] ) ) {
		$v_align = $atts['valign'];
	} else {
		$v_align = 'top';
	}
}

$_atts['class'] = 'l-section';
$_atts['class'] .= ' wpb_row'; // for correct output the html of some plugins, like Ultimate Addons
$_atts['class'] .= isset( $classes ) ? $classes : '';

if ( $height == 'default' ) {
	$_atts['class'] .= ' height_' . us_get_option( 'row_height', 'medium' );
} else {
	$_atts['class'] .= ' height_' . $height;
}
if ( $full_height ) {
	$_atts['class'] .= ' full_height valign_' . $v_align;
}
if ( $width ) {
	$_atts['class'] .= ' width_full';
}
if ( $color_scheme != '' ) {
	$_atts['class'] .= ' color_' . $color_scheme;
}
if ( $sticky ) {
	$_atts['class'] .= ' type_sticky';
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Disable Row if set, works in WPBakery only
if ( ! empty( $atts['disable_element'] ) ) {
	if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) {
		$_atts['class'] .= ' vc_hidden-lg vc_hidden-md vc_hidden-sm vc_hidden-xs';
	} else {
		return '';
	}
}

// When some values are set in Design options, add the specific classes
if ( us_design_options_has_property( $css, 'color' ) ) {
	$_atts['class'] .= ' has_text_color';
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
	AND $object_id = get_queried_object_id()
) {

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
	$_atts['class'] .= ' with_img';
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
	$bg_image_html = '<div class="l-section-img" style="' . $bg_image_inline_css . '"' . $bg_img_atts . '></div>';
}


$bg_slider_html = $bg_video_html = '';
if ( ! us_amp() ) {
	// Background Video
	if ( $us_bg_show == 'video' AND $us_bg_video != '' ) {
		$_atts['class'] .= ' with_video';
		$provider_matched = FALSE;

		$bg_video_html .= '<div class="l-section-video"';

		// Add data to hide video on the screen width via JS
		if ( $us_bg_video_disable_width ) {
			$bg_video_html .= ' data-video-disable-width="' . (int) $us_bg_video_disable_width . '"';
		}
		$bg_video_html .= '>';

		foreach ( us_config( 'embeds' ) as $provider => $embed ) {
			if ( ! preg_match( $embed['url_regex'], $us_bg_video, $matches ) ) {
				continue;
			}
			$provider_matched = TRUE;
			$video_id = $matches[1];
			if ( $provider == 'youtube' ) {
				$_atts['class'] .= ' with_youtube';
				$video_params = 'autoplay=1&loop=1&playlist=' . $video_id . '&controls=0&mute=1&iv_load_policy=3&disablekb=1&wmode=transparent';
			} elseif ( $provider == 'vimeo' ) {
				$_atts['class'] .= ' with_vimeo';
				$video_params = '?autoplay=1&loop=1&muted=1&title=0&byline=0&background=1';
			}
			// Removing autoplay for builder preview
			if ( apply_filters( 'usb_is_preview_page', NULL ) ) {
				$video_params = str_replace( 'autoplay=1&loop=1&', '', $video_params );
			}
			$embed_html = ( ! empty( $embed['iframe_html'] ) ) ? $embed['iframe_html'] : $embed['player_html'];
			$embed_html = str_replace( '<video_id>', $video_id, $embed_html );
			$embed_html = str_replace( '<player_url_params>', $video_params, $embed_html );
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
			$_atts['class'] .= ' parallax_ver';
			if ( $us_bg_parallax_reverse ) {
				$_atts['class'] .= ' parallaxdir_reversed';
			}
			if ( in_array( $us_bg_pos, array( 'top right', 'center right', 'bottom right' ) ) ) {
				$_atts['class'] .= ' parallax_xpos_right';
			} elseif ( in_array( $us_bg_pos, array( 'top left', 'center left', 'bottom left' ) ) ) {
				$_atts['class'] .= ' parallax_xpos_left';
			}
		} elseif ( $us_bg_parallax == 'fixed' OR $us_bg_parallax == 'still' ) {
			$_atts['class'] .= ' parallax_fixed';
		} elseif ( $us_bg_parallax == 'horizontal' ) {
			$_atts['class'] .= ' parallax_hor';
			$_atts['class'] .= ' bgwidth_' . $us_bg_parallax_width;
		}
	}

	// Image Slider
	if ( $us_bg_show == 'img_slider' AND ! empty( $us_bg_slider_ids ) AND ! us_amp() ) {
		$_atts['class'] .= ' with_slider';
		$img_slider_shortcode = '[us_image_slider';
		$img_slider_shortcode .= ' ids="' . $us_bg_slider_ids . '"';
		$img_slider_shortcode .= ' transition="' . $us_bg_slider_transition . '"';
		$img_slider_shortcode .= ' transition_speed="' . $us_bg_slider_speed . '"';
		$img_slider_shortcode .= ' autoplay_period="' . $us_bg_slider_interval . '"';
		$img_slider_shortcode .= ' arrows="hide" autoplay="1" pause_on_hover="" img_size="full" img_fit="cover"]';

		$bg_slider_html = '<div class="l-section-slider">' . do_shortcode( $img_slider_shortcode ) . '</div>';

		// Revolution Slider
	} elseif ( $us_bg_show == 'rev_slider' AND class_exists( 'RevSlider' ) ) {
		$_atts['class'] .= ' with_slider';
		$bg_slider_html = '<div class="l-section-slider">' . do_shortcode( '[rev_slider ' . $us_bg_rev_slider . ']' ) . '</div>';
	}
}

// Background Overlay
$bg_overlay_html = '';
if ( apply_filters( 'usb_is_preview_page', NULL ) OR ! empty( $us_bg_overlay_color ) ) {
	$bg_overlay_html = '<div class="l-section-overlay" style="background:' . us_get_color( $us_bg_overlay_color, TRUE ) . '"></div>';
}

// Shape Divider
$bg_shape_html = '';

/*
 * Fallback for old shape params (after version 7.1)
 */
if (
	empty( $us_shape_show_top )
	AND empty( $us_shape_show_bottom )
	AND isset( $atts['us_shape'] )
	AND ( $atts['us_shape'] !== 'none' )
) {
	if ( ! isset( $atts['us_shape_position'] ) ) {
		$old_shape_pos = 'bottom';
	} else {
		$old_shape_pos = 'top';
	}

	${'us_shape_show_' . $old_shape_pos} = 1;
	${'us_shape_' . $old_shape_pos} = $atts['us_shape'];

	if ( ! empty( $atts['us_shape_height'] )	) {
		${'us_shape_height_' . $old_shape_pos} = $atts['us_shape_height'];
	}
	if ( ! empty( $atts['us_shape_color'] )	) {
		${'us_shape_color_' . $old_shape_pos} = $atts['us_shape_color'];
	}
	if ( ! empty( $atts['us_shape_overlap'] )	) {
		${'us_shape_overlap_' . $old_shape_pos} = $atts['us_shape_overlap'];
	}
	if ( ! empty( $atts['us_shape_flip'] )	) {
		${'us_shape_flip_' . $old_shape_pos} = $atts['us_shape_flip'];
	}
}
if ( $us_shape_show_top OR $us_shape_show_bottom ) {
	$_atts['class'] .= ' with_shape';

	$positions = array();
	if ( $us_shape_show_top ) {
		$positions[] = 'top';
	}
	if ( $us_shape_show_bottom ) {
		$positions[] = 'bottom';
	}

	foreach ( $positions as $pos ) {

		// If checkbox checked for current position (top or bottom) generate shape html
		if ( ${'us_shape_show_' . $pos} ) {
			$shape_html = '';

			// Get built-in shapes
			$svg_filepath = sprintf( '%s/assets/shapes/%s.svg', US_CORE_DIR, ${'us_shape_' . $pos} );

			// Get custom file, if it was uploaded in Row settings
			if ( ${'us_shape_' . $pos} === 'custom' AND $shape_id = ${'us_shape_custom_' . $pos} ) {

				// Get file MIME type to handle SVGs separately
				$mime_type = get_post_mime_type( $shape_id );
				if ( strpos( $mime_type, 'svg' ) !== FALSE ) {
					$svg_filepath = get_attached_file( $shape_id );

					// Support non-SVG images
				} else {
					$svg_filepath = '';
					$shape_html = wp_get_attachment_image( $shape_id, 'full' );
				}
			}

			// In case SVG is valid, use its content as shape html
			if ( ! empty( $svg_filepath ) AND $svg_filepath = realpath( $svg_filepath ) ) {
				$shape_html = file_get_contents( $svg_filepath );
			}

			// Attributes for shape div
			${'shape_atts_' . $pos} = array(
				'class' => 'l-section-shape',
				'style' => '',
			);

			// Type and position classes
			${'shape_atts_' . $pos}['class'] .= ' type_' . ${'us_shape_' . $pos};
			${'shape_atts_' . $pos}['class'] .= " pos_{$pos}";

			// Overlap class
			if ( ${'us_shape_overlap_' . $pos} ) {
				${'shape_atts_' . $pos}['class'] .= ' on_front';
			}

			// Flip class
			if ( ${'us_shape_flip_' . $pos} ) {
				${'shape_atts_' . $pos}['class'] .= ' hor_flip';
			}

			// Height style
			if ( ${'us_shape_height_' . $pos} !== '15vh' ) {
				${'shape_atts_' . $pos}['style'] .= 'height:' . ${'us_shape_height_' . $pos} . ';';
			}

			// Color style
			if ( ${'us_shape_color_' . $pos} !== '_content_bg' ) {
				${'shape_atts_' . $pos}['style'] .= 'color:' . us_get_color( ${'us_shape_color_' . $pos} );
			}

			$bg_shape_html .= '<div' . us_implode_atts( ${'shape_atts_' . $pos} ) . '>';
			$bg_shape_html .= $shape_html;
			$bg_shape_html .= '</div>';
		}
	}
}

// Output the element
$output = '<section' . us_implode_atts( $_atts ) . '>';
$output .= $bg_image_html;
$output .= $bg_video_html;
$output .= $bg_slider_html;
$output .= $bg_overlay_html;
$output .= $bg_shape_html;
$output .= '<div class="l-section-h i-cf">';

$cols_atts = array(
	'class' => 'g-cols',
	'style' => '',
);

// New Columns Layout after version 8.0
if ( us_get_option( 'live_builder' ) AND us_get_option( 'grid_columns_layout' ) ) {

	// Fallback for old columns layout (after version 8.0)
	$columns_fallback_result = us_vc_row_columns_fallback_helper( $shortcode_base, $content );
	if ( $columns === '1' AND ! empty( $columns_fallback_result['columns'] ) ) {
		$columns = $columns_fallback_result['columns'];
	}
	if ( ! empty( $columns_fallback_result['columns_layout'] ) ) {
		$columns_layout = $columns_fallback_result['columns_layout'];
	}

	// Fallback for $gap param (after version 8.0)
	if ( $columns_type ) {

		// If the "Additional gap" was set, get its value and double it as new columns gap
		// Example: 5px becomes 10px
		// Example: 0.7rem becomes 1.4rem
		if ( ! empty( $gap ) AND preg_match( '~^(\d*\.?\d*)(.*)$~', $gap, $matches ) ) {
			$columns_gap = ( $matches[1] * 2 ) . $matches[2];
		} else {
			$columns_gap = '0rem';
		}
	} elseif ( ! empty( $gap ) ) {
		$columns_gap = 'calc(3rem + ' . $gap . ')';
	}

	$cols_atts['class'] .= ' via_grid';
	$cols_atts['class'] .= ' cols_' . $columns;
	$cols_atts['class'] .= ' laptops-cols_' . $laptops_columns;
	$cols_atts['class'] .= ' tablets-cols_' . $tablets_columns;
	$cols_atts['class'] .= ' mobiles-cols_' . $mobiles_columns;

	// Add columns gap when it is not default
	if ( $columns_gap !== '3rem' ) {

		// Use zero for empty value
		if ( $columns_gap === '' ) {
			$columns_gap = '0';
		}
		$cols_atts['style'] .= 'grid-gap:' . esc_attr( $columns_gap ) . ';';
	}

	// Add custom columns layout via inline style
	if ( $columns === 'custom' AND ! empty( $columns_layout ) ) {
		$cols_atts['style'] .= '--custom-columns:' . esc_attr( $columns_layout );
	}

} else {
	$cols_atts['class'] .= ' via_flex';
	if ( ! empty( $gap ) ) {
		$cols_atts['style'] .= '--additional-gap:' . esc_attr( $gap ) . ';';
	}
}

$cols_atts['class'] .= ' valign_' . $content_placement;

if ( ! empty( $columns_type ) ) {
	$cols_atts['class'] .= ' type_boxes';
} else {
	$cols_atts['class'] .= ' type_default';
}
if ( ! empty( $columns_reverse ) ) {
	$cols_atts['class'] .= ' reversed';
}

$output .= '<div' . us_implode_atts( $cols_atts ) . '>';
$output .= do_shortcode( $content );
$output .= '</div>';

$output .= '</div>';
$output .= '</section>';

echo $output;
