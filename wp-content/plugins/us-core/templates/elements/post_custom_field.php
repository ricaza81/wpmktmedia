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
if ( in_array( $key, array( 'us_tile_icon', 'us_testimonial_rating' ) ) ) {
	$type = 'icon';
}

// Get the value from custom field
if ( $key == 'custom' AND ! empty( $custom_key ) ) {
	if ( usb_is_preview_page_for_template() AND $us_elm_context == 'shortcode' ) {
		$value = $custom_key;
	} elseif ( $postID ) {
		$value = get_post_meta( $postID, $custom_key, TRUE );
	} elseif ( $term ) {
		$value = get_term_meta( $term->term_id, $custom_key, TRUE );
	}

	// In Live Builder for Page Block / Content template show placeholder for shortcode
} elseif ( usb_is_preview_page_for_template() AND $us_elm_context == 'shortcode' ) {
	$image_fields = us_config( 'elements/post_custom_field.params.thumbnail_size.show_if.2', array() );
	if ( in_array( $key, $image_fields ) ) {
		$type = 'image';
	} else {
		$value = us_config( 'elements/post_custom_field.usb_preview_dummy_data.' . $key, '' );
	}

} elseif ( ! in_array( $key, array_keys( $us_predefined_post_custom_fields ) ) ) {

	// Get ACF value
	if ( function_exists( 'get_field_object' ) ) {
		if ( $postID ) {
			$acf_obj = get_field_object( $key, $postID );
		} elseif ( $term ) {
			$acf_obj = get_field_object( $key, $term );
		}
		$value = us_arr_path( $acf_obj, 'value', '' );

		// Force "image" type
		if ( isset( $acf_obj['type'] ) AND $acf_obj['type'] === 'image' ) {
			$type = 'image';

			// Get image ID, if return format set as "array"
			if ( isset( $value[ 'id' ] ) ) {
				$value = (string) $value['id'];
			}
		}
	}

} else {
	if ( $postID ) {
		$value = usof_meta( $key, $postID );
	} elseif ( $term ) {
		$value = get_term_meta( $term->term_id, $key, TRUE );
	}

	// Add <p> and <br> if custom field has WYSIWYG
	if ( is_string( $value ) AND $type == 'text' ) {
		$value = wpautop( $value );
	}
}

// At this point the $value can contain an array, so we need to transform it to a string
if ( is_array( $value ) ) {
	$_has_array = array_filter( $value, 'is_array' );
	$_has_object = array_filter( $value, 'is_object' );

	// If array contain arrays or objects inside, output specified notification
    if ( $_has_array OR $_has_object ) {
		$value = 'Unsupported format';

		// in other cases separate values by comma
	} else {
		$value = implode( ', ', $value );
	}
}

// In case the value is an object output specified notification
if ( is_object( $value ) ) {
	$value = 'Unsupported format';
}

// Don't output the element, when it's an object OR its value is empty
if (
	! usb_is_preview_page()
	AND $hide_empty
	AND (
		$value === ''
		OR $value === FALSE
		OR $value === NULL
	)
) {
	return;
}

// CSS classes & ID
$_atts['class'] = 'w-post-elm post_custom_field';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' type_' . $type;
if ( $link != 'none' AND $color_link ) {
	$_atts['class'] .= ' color_link_inherit';
}

// When some values are set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'border-radius' ) ) {
	$_atts['class'] .= ' has_border_radius';
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

	// Get image by ID
	if ( is_numeric( $value ) ) {
		$value = wp_get_attachment_image( $value, $thumbnail_size );
	}

	// If there is no image, display the placeholder
	if ( empty( $value ) ) {
		$value = us_get_img_placeholder( $thumbnail_size );
	}

	// Set Aspect Ratio values
	if ( $has_ratio ) {
		$ratio_array = us_get_aspect_ratio_values( $ratio, $ratio_width, $ratio_height );
		$ratio_helper_html = '<div style="padding-bottom:' . round( $ratio_array[1] / $ratio_array[0] * 100, 4 ) . '%"></div>';
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

// Text before/after values
$text_before = trim( strip_tags( $text_before, '<br>' ) );
$text_after = trim( strip_tags( $text_after, '<br>' ) );

if ( $text_before !== '' ) {
	$text_before_html = sprintf( '<%s class="w-post-elm-before">%s </%s>', $text_before_tag, $text_before, $text_before_tag );
} else {
	$text_before_html = '';
}
if ( $text_after !== '' ) {
	$text_after_html = sprintf( '<%s class="w-post-elm-after"> %s</%s>', $text_after_tag, $text_after, $text_after_tag );
} else {
	$text_after_html = '';
}

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
$output = '<' . $tag . us_implode_atts( $_atts ) . '>';
if ( ! empty( $icon ) ) {
	$output .= us_prepare_icon_tag( $icon );
}
$output .= $text_before_html;

if ( ! empty( $link_atts['href'] ) ) {
	$output .= '<a' . us_implode_atts( $link_atts ) . '>';
}

$output .= $ratio_helper_html;

// Wrap the value into additional <span>, if it doesn't have a <div>
if ( $type === 'text' AND strpos( $value, '<div' ) === FALSE ) {
	$output .= '<span class="w-post-elm-value">' . $value . '</span>';
} else {
	$output .= $value;
}

if ( ! empty( $link_atts['href'] ) ) {
	$output .= '</a>';
}
$output .= $text_after_html;
$output .= '</' . $tag . '>';

echo $output;
