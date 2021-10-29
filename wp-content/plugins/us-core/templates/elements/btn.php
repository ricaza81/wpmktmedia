<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Button element
 */

global $us_grid_object_type;

// Check existence of Button Style, if not, set the default
$btn_styles = us_get_btn_styles();
if ( ! array_key_exists( $style, $btn_styles ) ) {
	$style = '1';
}

$_atts['class'] = 'w-btn us-btn-style_' . $style;
$_atts['class'] .= isset( $classes ) ? $classes : '';

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

$wrapper_class = '';
if ( $us_elm_context == 'shortcode' ) {
	if ( $width_type ) {
		$wrapper_class .= ' width_full';
	} else {
		$wrapper_class .= ' align_' . $align;
	}
}

// Icon
$icon_html = '';
if ( ! empty( $icon ) ) {
	$icon_html = us_prepare_icon_tag( $icon );
	$_atts['class'] .= ' icon_at' . $iconpos;

	// Swap icon position for RTL
	if ( is_rtl() ) {
		$iconpos = ( $iconpos == 'left' ) ? 'right' : 'left';
	}
}

// Apply filters to button label
$label = us_replace_dynamic_value( $label, $us_elm_context, $us_grid_object_type );
$label = trim( strip_tags( $label, '<br>' ) );
$label = wptexturize( $label );

if ( $label === '' ) {
	$_atts['class'] .= ' text_none';
	$_atts['aria-label'] = us_translate( 'Button' );
}

// Link
if ( $link_type === 'none' ) {
	$link_atts['href'] = 'javascript:void(0)';
} elseif ( $link_type === 'post' ) {

	// Terms of selected taxonomy in Grid
	if ( $us_elm_context == 'grid' AND $us_grid_object_type == 'term' ) {
		global $us_grid_term;
		$link_atts['href'] = get_term_link( $us_grid_term );
	} else {
		$link_atts['href'] = apply_filters( 'the_permalink', get_permalink() );

		// Force opening in a new tab for "Link" post format
		if ( get_post_format() == 'link' ) {
			$link_atts['target'] = '_blank';
			$link_atts['rel'] = 'noopener';
		}
	}
} elseif ( $link_type === 'elm_value' AND ! empty( $label ) ) {
	if ( is_email( $label ) ) {
		$link_atts['href'] = 'mailto:' . $label;
	} elseif ( strpos( $label, '+' ) === 0 ) {
		$link_atts['href'] = 'tel:' . $label;
	} else {
		$link_atts['href'] = esc_url( $label );
	}
} elseif ( $link_type === 'custom' ) {
	$link_atts = us_generate_link_atts( $link );
} elseif ( $link_type === 'onclick' ) {
	if ( ! empty( $onclick_code ) ) {
		// If there are errors in custom JS, an error message will be displayed
		// in the console, and this will not break the work of the site.
		$onclick_code = 'try{' . trim( $onclick_code ) . '}catch(e){console.error(e)}';
	} else {
		$onclick_code = 'return false'; // Default value
	}
	// NOTE: On the output, the value is filtered using `esc_attr()`,
	// and there is no need for additional filtering `esc_js()`.
	$link_atts['onclick'] = $onclick_code;
	$link_atts['href'] = '#';

} else {
	$link_atts = us_generate_link_atts( 'url:{{' . $link_type . '}}|||' );
}

// Don't show the button if it has no link
if (
	empty( $link_atts['href'] )
	AND ! apply_filters( 'usb_is_preview_page', NULL )
) {
	return;

	// Force "Open in a new tab" attributes
} elseif ( empty( $link_atts['target'] ) AND $link_new_tab ) {
	$link_atts['target'] = '_blank';
	$link_atts['rel'] = 'noopener nofollow';
}

$_atts = $_atts + $link_atts;

// Output the element
$output = '';
if ( $us_elm_context == 'shortcode' ) {
	$output .= '<div class="w-btn-wrapper' . $wrapper_class . '">';
}
$output .= '<a' . us_implode_atts( $_atts ) . '>';
if ( $iconpos == 'left' ) {
	$output .= $icon_html;
}
if ( $label !== '' ) {
	$output .= '<span class="w-btn-label">' . $label . '</span>';
}
if ( $iconpos == 'right' ) {
	$output .= $icon_html;
}
$output .= '</a>';
if ( $us_elm_context == 'shortcode' ) {
	$output .= '</div>';
}

echo $output;
