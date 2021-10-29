<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Post Custom Field element
 *
 * @var $classes string
 * @var $id string
 */

global $us_grid_object_type;

if ( $us_elm_context == 'grid' ) {
	if ( $us_grid_object_type == 'term' ) {
		global $us_grid_term;
		$term = $us_grid_term;
		$postID = NULL;
	} else { /* elseif $us_grid_object_type == 'post' */
		$postID = get_the_ID();
		$term = NULL;
	}

} else { /* elseif $us_elm_context == 'shortcode' */

	// TODO, maybe we should replace $us_elm_context with $us_grid_listing_outputs_items
	global $us_grid_listing_outputs_items;

	// Shortcodes in full content element inside grid
	if ( ! empty( $us_grid_listing_outputs_items ) ) {
		if ( $us_grid_object_type == 'term' ) {
			global $us_grid_term;
			$term = $us_grid_term;
			$postID = NULL;
		} else {
			$postID = get_the_ID();
			$term = NULL;
		}

		// Rest of conditions for shortcodes in regular content
	} elseif ( is_tax() OR is_tag() OR is_category() ) {
		$term = get_queried_object();
		$postID = NULL;
	} elseif (
		is_404()
		AND ( $page_404_ID = us_get_option( 'page_404' ) ) !== 'default'
	) {
		$postID = $page_404_ID;
		$term = NULL;
	} elseif (
		is_search()
		AND ( $search_page_ID = us_get_option( 'search_page' ) ) !== 'default'
	) {
		$postID = $search_page_ID;
		$term = NULL;
	} else { /* regular post */
		$postID = get_the_ID();
		$term = NULL;
	}
}

global $us_predefined_post_custom_fields;
$value = '';
$type = 'text';

// Force type for specific meta keys
if ( $key == 'us_tile_additional_image' ) {
	$type = 'image';
}
if ( $key == 'us_tile_icon' ) {
	$type = 'icon';
}

// Get the value from custom field
if ( $key == 'custom' ) {

	if ( ! empty( $custom_key ) ) {
		if ( $postID ) {
			$value = get_post_meta( $postID, $custom_key, TRUE );
		} elseif ( $term ) {
			$value = get_term_meta( $term->term_id, $custom_key, TRUE );
		}
	}

} elseif ( ! in_array( $key, array_keys( $us_predefined_post_custom_fields ) ) ) {

	// Get ACF value
	if ( function_exists( 'get_field_object' ) ) {
		if ( $postID ) {
			$acf_obj = get_field_object( $key, $postID );
			$value = us_arr_path( $acf_obj, 'value', '' );

			// Force "image" type
			// TODO: Add support for link
			if ( isset( $acf_obj['type'] ) AND $acf_obj['type'] == 'image' ) {
				$type = 'image';
			} elseif ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}

			// Get Label if value : label is used
			if (
				us_arr_path( $acf_obj, 'menu_order', NULL ) === 0
				AND $choices = us_arr_path( $acf_obj, 'choices', array() )
				AND is_array( $choices )
				AND isset( $choices[ $value ] )
			) {
				$value = $choices[ $value ];
			}

		} elseif ( $term ) {
			$value = get_field( $key, $term );

			if ( is_array( $value ) ) {
				// TODO: Add support for link
				if ( isset( $value['type'] ) AND $value['type'] == 'image' ) {
					$type = 'image';
				} else {
					$value = implode( ', ', $value );
				}
			}
		}
	}

} else {
	if ( $postID ) {
		$value = get_post_meta( $postID, $key, TRUE );
	} elseif ( $term ) {
		$value = get_term_meta( $term->term_id, $key, TRUE );
	}

	// Format the value
	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	} elseif ( $type == 'text' ) {
		$value = wpautop( $value ); // add <p> and <br> if custom field has WYSIWYG
	}
}

// Don't output the element, when it's an object OR its value is empty string
if ( is_object( $value ) OR ( $hide_empty AND $value == '' ) ) {
	return;
}

// CSS classes & ID
$_atts['class'] = 'w-post-elm post_custom_field';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' type_' . $type;
if ( $link != 'none' AND $color_link ) {
	$_atts['class'] .= ' color_link_inherit';
}

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$_atts['class'] .= ' has_text_color';
}
if ( us_design_options_has_property( $css, 'border-radius' ) ) {
	$_atts['class'] .= ' has_border_radius';
}

if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
if ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) {
	$_atts['id'] = $el_id;
}

// Generate image semantics
$ratio_helper_html = '';
if ( $type == 'image' ) {
	global $us_grid_img_size;
	if ( ! empty( $us_grid_img_size ) AND $us_grid_img_size != 'default' ) {
		$thumbnail_size = $us_grid_img_size;
	}

	// Format the value to get image ID
	$value_image_ID = is_array( $value ) ? $value['id'] : intval( $value );

	$value = wp_get_attachment_image( $value_image_ID, $thumbnail_size );
	if ( empty( $value ) ) {
		$value = us_get_img_placeholder( $thumbnail_size );
	}

	// Set Aspect Ratio values
	if ( $has_ratio ) {
		$ratio_array = us_get_aspect_ratio_values( $ratio, $ratio_width, $ratio_height );
		$ratio_helper_html = '<div style="padding-bottom:' . number_format( $ratio_array[1] / $ratio_array[0] * 100, 4 ) . '%"></div>';
		$_atts['class'] .= ' has_ratio';
	} elseif ( $stretch ) {
		$_atts['class'] .= ' stretched';
	}
}

// Generate special semantics for Testimonial Rating
if ( $key == 'us_testimonial_rating' ) {
	$rating_value = (int) strip_tags( $value );

	if ( $rating_value == 0 ) {
		return;
	} else {
		$value = '<div class="w-testimonial-rating">';
		for ( $i = 1; $i <= $rating_value; $i ++ ) {
			$value .= '<i></i>';
		}
		$value .= '</div>';
	}
}

// Generate icon specific semantics
if ( $key == 'us_tile_icon' ) {
	$value = us_prepare_icon_tag( $value );
}

// Text before value
$text_before = ( trim( $text_before ) != '' ) ? '<span class="w-post-elm-before">' . trim( $text_before ) . ' </span>' : '';

// Text after value
$text_after = ( trim( $text_after ) != '' ) ? ' <span class="w-post-elm-after">' . trim( $text_after ) . ' </span>' : '';

// Link
if ( $link === 'none' ) {
	$link_atts = array();

} elseif ( $link === 'post' ) {
	if ( $postID ) {
		$link_atts['href'] = apply_filters( 'the_permalink', get_permalink() );
		if ( get_post_format() == 'link' ) {
			$link_atts['target'] = '_blank';
			$link_atts['rel'] = 'noopener';
		}
	} elseif ( $term ) {
		$link_atts['href'] = get_term_link( $term );
	}

} elseif ( $link === 'popup_post_image' AND $type == 'image' ) {
	$full_image_url = wp_get_attachment_image_url( $value_image_ID, 'full' );
	if ( empty( $full_image_url ) ) {
		$full_image_url = us_get_img_placeholder( 'full', TRUE );
	}
	$link_atts = array(
		'href' => $full_image_url,
		'ref' => us_amp() ? '' : 'magnificPopup',
	);

} elseif ( $link === 'elm_value' AND ! empty( $value ) ) {
	if ( is_email( $value ) ) {
		$link_atts['href'] = 'mailto:' . $value;
	} elseif ( strpos( $value, '+' ) === 0 ) {
		$link_atts['href'] = 'tel:' . $value;
	} else {
		$link_atts['href'] = esc_url( $value );
	}

} elseif ( $link === 'onclick' ) {
	$onclick_code = ! empty( $onclick_code ) ? $onclick_code : 'return false';
	$link_atts['href'] = '#';
	$link_atts['onclick'] = esc_js( trim( $onclick_code ) );

} elseif ( $link === 'custom' ) {
	$link_atts = us_generate_link_atts( $custom_link );

} else {
	$link_atts = us_generate_link_atts( 'url:{{' . $link . '}}|||' );
}

// Force "Open in a new tab" attributes
if ( ! empty( $link_atts['href'] ) AND empty( $link_atts['target'] ) AND $link_new_tab ) {
	$link_atts['target'] = '_blank';
	$link_atts['rel'] = 'noopener nofollow';
}

// Output the element
$output = '<' . $tag . ' ' . us_implode_atts( $_atts ) . '>';
if ( ! empty( $icon ) ) {
	$output .= us_prepare_icon_tag( $icon );
}
$output .= $text_before;

if ( ! empty( $link_atts['href'] ) ) {
	$output .= '<a ' . us_implode_atts( $link_atts ) . '>';
}

$output .= $ratio_helper_html;
$output .= $value;

if ( ! empty( $link_atts['href'] ) ) {
	$output .= '</a>';
}
$output .= $text_after;
$output .= '</' . $tag . '>';

echo $output;
