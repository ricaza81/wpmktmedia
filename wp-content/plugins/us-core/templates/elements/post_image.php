<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Post Image element
 *
 * @var $thumbnail_size string Image WordPress size
 * @var $popup_thumbnail_size string Image WordPress size
 * @var $placeholder bool Use placeholder if post has no thumbnail?
 * @var $media_preview bool Show media preview for video and gallery posts?
 * @var $link string Link type: 'post' / 'custom' / 'none'
 * @var $custom_link array
 * @var $design_options array
 *
 * @var $classes string
 * @var $id string
 *
 * @var $has_ratio bool is use aspect ratio: '1'/'0'
 * @var $ratio string ratio value: '1x1'/'custom'
 * @var $ratio_width string width value: '1'
 * @var $ratio_height string height value: '1'
 */

if ( is_admin() AND ( ! defined( 'DOING_AJAX' ) OR ! DOING_AJAX ) ) {
	return;
}

global $us_grid_img_size, $_wp_additional_image_sizes, $us_post_img_ratio, $us_post_slider_size;

$_atts['class'] = 'w-post-elm post_image';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= $circle ? ' as_circle' : '';

// When width or height are set in Design options, add the specific classes
if ( us_design_options_has_property( $css, 'width' ) ) {
	$_atts['class'] .= ' has_width';
}
if ( us_design_options_has_property( $css, 'height' ) ) {
	$_atts['class'] .= ' has_height';
}

// Set Aspect Ratio values
$ratio_helper = '';
if ( $has_ratio ) {
	$ratio_array = us_get_aspect_ratio_values( $ratio, $ratio_width, $ratio_height );
	$ratio_helper = '<div style="padding-bottom:' . number_format( $ratio_array[1] / $ratio_array[0] * 100, 4 ) . '%"></div>';
	$_atts['class'] .= ' has_ratio';
} elseif ( $stretch ) {
	$_atts['class'] .= ' stretched';
}

if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
if ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) {
	$_atts['id'] = $el_id;
}

// Overwrite thumbnail_size from [us_grid] shortcode if set
if ( ! empty( $us_grid_img_size ) AND $us_grid_img_size != 'default' ) {
	$thumbnail_size = $us_grid_img_size;
}

// Calculate aspect ratio for media preview and for placeholder
if ( isset( $_wp_additional_image_sizes[ $thumbnail_size ] ) AND $_wp_additional_image_sizes[ $thumbnail_size ]['width'] != 0 AND $_wp_additional_image_sizes[ $thumbnail_size ]['height'] != 0 ) {
	$us_post_img_ratio = number_format( $_wp_additional_image_sizes[ $thumbnail_size ]['height'] / $_wp_additional_image_sizes[ $thumbnail_size ]['width'] * 100, 4 );
}

global $us_grid_object_type;

if ( $us_elm_context == 'grid' AND $us_grid_object_type == 'term' ) {
	global $us_grid_term;
} elseif ( $us_elm_context == 'shortcode' AND ( is_tax() OR is_tag() OR is_category() ) ) {
	$us_grid_term = get_queried_object();
} else {
	$us_grid_term = NULL;
}

// Link
if ( $link === 'none' ) {
	$link_atts = array();
} elseif ( $link === 'post' ) {

	// Terms of selected taxonomy in Grid
	if ( $us_grid_term ) {
		$link_atts['href'] = get_term_link( $us_grid_term );
	} else {
		$link_atts['href'] = apply_filters( 'the_permalink', get_permalink() );

		// Force opening in a new tab for "Link" post format
		if ( get_post_format() == 'link' ) {
			$link_atts['target'] = '_blank';
			$link_atts['rel'] = 'noopener';
		}
	}

} elseif ( $link === 'custom' ) {
	$link_atts = us_generate_link_atts( $custom_link );

} elseif ( $link === 'popup_post_image' ) {
	if ( get_post_type() == 'attachment' ) {
		$full_image_url = wp_get_attachment_image_url( get_the_ID(), 'full' );
	} else {
		$full_image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	}
	$link_atts = array(
		'href' => $full_image_url,
		'ref' => us_amp() ? '' : 'magnificPopup',
	);

} else {
	$link_atts = us_generate_link_atts( 'url:{{' . $link . '}}|||' );
}

// Force "Open in a new tab" attributes
if ( ! empty( $link_atts['href'] ) AND empty( $link_atts['target'] ) AND $link_new_tab ) {
	$link_atts['target'] = '_blank';
	$link_atts['rel'] = 'noopener nofollow';
}

$_post_preview = '';

// Get image of taxonomy term (works for WooCommerce Product categories, brands, tags etc.)
if ( isset( $us_grid_term->taxonomy ) AND strpos( $us_grid_term->taxonomy, 'product_' ) === 0 ) {
	if ( $term_thumbnail_id = get_term_meta( $us_grid_term->term_id, 'thumbnail_id', TRUE ) ) {
		$_post_preview = wp_get_attachment_image( $term_thumbnail_id, $thumbnail_size );

	} elseif ( $placeholder ) {
		$_atts['class'] .= ' with_placeholder';

		// Use WooCommerce placeholder if enabled
		if ( function_exists( 'wc_placeholder_img_src' ) ) {
			$_post_preview = '<img src="' . wc_placeholder_img_src( $thumbnail_size ) . '" loading="lazy" alt=""/>';
		} else {
			$_post_preview = us_get_img_placeholder( $thumbnail_size );
		}
	} else {
		return;
	}
}

// Generate media preview
if ( $_post_preview == '' AND $media_preview AND ! post_password_required() AND ! $us_grid_term ) {

	// for WooCommerce Products with gallery
	if ( get_post_type() == 'product' ) {
		$postID = get_the_ID();
		if ( $product_images = get_post_meta( $postID, '_product_image_gallery', TRUE ) ) {
			$img_ids = explode( ',', get_post_thumbnail_id() . ',' . $product_images );

			// Remove empty ids to avoid duplications in output
			$img_ids = array_diff( $img_ids, array( '' ) );

			foreach ( $img_ids as $key => $img_id ) {
				$img_width = number_format( 100 / count( $img_ids ), 2 );

				$_post_preview .= '<div class="w-post-slider-trigger"';
				$_post_preview .= ' style="width:' . $img_width . '%; left:' . $key * $img_width . '%;"></div>';
				$_post_preview .= wp_get_attachment_image( $img_id, $thumbnail_size );
			}
		}

		// for Posts with Video, Audio, Gallery formats
	} else {
		$us_post_slider_size = $thumbnail_size;
		$the_content = get_the_content();
		$_post_preview = us_get_post_preview( $the_content );

		if ( $_post_preview != '' ) {
			$_atts['class'] .= ' media_preview'; // add CSS class for media preview
			$link_atts = array(); // remove link for media preview
		}
	}
}

// Output image of attachment post type
if ( $_post_preview == '' AND get_post_type() == 'attachment' ) {
	$_post_preview = wp_get_attachment_image( get_the_ID(), $thumbnail_size );
}

// Output Featured image if the current post has it
if ( $_post_preview == '' AND has_post_thumbnail() AND is_null( $us_grid_term ) ) {
	$_post_preview = get_the_post_thumbnail( get_the_ID(), $thumbnail_size );
}

// Output the first image from the content of Gallery format
if ( $_post_preview == '' AND get_post_format() == 'gallery' ) {
	$the_content = get_the_content();
	if ( preg_match( '~\[us_image_slider.+?\]|\[gallery.+?\]~', $the_content, $matches ) ) {
		$gallery = preg_replace( '~(vc_gallery|gallery)~', 'us_image_slider', $matches[0] );
		preg_match( '~\[us_image_slider(.+?)\]~', $gallery, $matches2 );
		$shortcode_atts = shortcode_parse_atts( $matches2[1] );
		if ( ! empty( $shortcode_atts['ids'] ) ) {
			$ids = explode( ',', $shortcode_atts['ids'] );
			if ( count( $ids ) > 0 ) {
				$_post_preview = wp_get_attachment_image( $ids[0], $thumbnail_size );
			}
		}
	}
}

// Output placeholder if enabled
if ( $_post_preview == '' AND $placeholder ) {
	$_atts['class'] .= ' with_placeholder';

	// Use WooCommerce placeholder if enabled
	if ( get_post_type() == 'product' AND function_exists( 'wc_placeholder_img_src' ) ) {
		$_post_preview = '<img src="' . wc_placeholder_img_src( $thumbnail_size ) . '" loading="lazy" alt=""/>';
	} else {
		$_post_preview = us_get_img_placeholder( $thumbnail_size );
	}
}

// Don't output the element without any content
if ( $_post_preview == '' ) {
	return;
}

$output = '<div ' . us_implode_atts( $_atts ) . '>';
$output .= $ratio_helper;
if ( ! empty( $link_atts['href'] ) ) {
	$link_atts['aria-label'] = strip_tags( get_the_title() );
	$output .= '<a ' . us_implode_atts( $link_atts ) . '>';
}

$output .= $_post_preview;

if ( ! empty( $link_atts['href'] ) ) {
	$output .= '</a>';
}
$output .= '</div>';

echo apply_filters( 'us_post_image', $output, $_post_preview, $link_atts );
