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

if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

$wrapper_class = '';
if ( $us_elm_context == 'shortcode' ) {
	$wrapper_class .= ' width_' . $width_type;
	if ( $width_type != 'full' ) {
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

// Text
$text = trim( strip_tags( $label, '<br>' ) );
if ( $text == '' ) {
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
} elseif ( $link_type === 'elm_value' AND ! empty( $text ) ) {
	if ( is_email( $text ) ) {
		$link_atts['href'] = 'mailto:' . $text;
	} elseif ( strpos( $text, '+' ) === 0 ) {
		$link_atts['href'] = 'tel:' . $text;
	} else {
		$link_atts['href'] = esc_url( $text );
	}
} elseif ( $link_type === 'custom' ) {
	$link_atts = us_generate_link_atts( $link );
} elseif ( $link_type === 'onclick' ) {
	$onclick_code = ! empty( $onclick_code ) ? $onclick_code : 'return false';
	$link_atts['href'] = '#';
	$link_atts['onclick'] = esc_js( trim( $onclick_code ) );
} else {
	$link_atts = us_generate_link_atts( 'url:{{' . $link_type . '}}|||' );
}

// Don't show the button if it has no link
if ( empty( $link_atts['href'] ) ) {
	return;

	// Force "Open in a new tab" attributes
} elseif ( empty( $link_atts['target'] ) AND $link_new_tab ) {
	$link_atts['target'] = '_blank';
	$link_atts['rel'] = 'noopener nofollow';
}

$_atts = $_atts + $link_atts;

// Apply filters to button text
$text = us_replace_dynamic_value( $text, $us_elm_context, $us_grid_object_type );

// Output the element
$output = '';
if ( $us_elm_context == 'shortcode' ) {
	$output .= '<div class="w-btn-wrapper' . $wrapper_class . '">';
}
$output .= '<a ' . us_implode_atts( $_atts ) . '>';
if ( $iconpos == 'left' ) {
	$output .= $icon_html;
}
if ( $text != '' ) {
	$output .= '<span class="w-btn-label">' . wptexturize( $text ) . '</span>';
}
if ( $iconpos == 'right' ) {
	$output .= $icon_html;
}
$output .= '</a>';
if ( $us_elm_context == 'shortcode' ) {
	$output .= '</div>';
}

echo $output;
