<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_scroller
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @param $speed          string Scroll Speed
 * @param $dots           bool Show navigation dots?
 * @param $dots_pos       string Dots Position
 * @param $dots_size      string Dots Size
 * @param $dots_color     string Dots color value
 * @param $disable_width  string Dots color value
 * @param $el_class       string Extra class name
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 */

// Don't output Page Scroller on AMP
if ( us_amp() AND ! apply_filters( 'usb_is_preview_page', NULL ) ) {
	return;
}

$_atts = array(
	'class' => 'w-scroller',
	'aria-hidden' => 'true',
);
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' style_' . $dots_style;
$_atts['class'] .= ' pos_' . $dots_pos;
if ( $dots ) {
	$_atts['class'] .= ' with_dots';
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

$_atts['data-speed'] = (int) $speed;
$_atts['data-disablewidth'] = (int) $disable_width;
if ( $include_footer ) {
	$_atts['data-footer-dots'] = 'true';
}

$dots_inline_css = us_prepare_inline_css(
	array(
		'font-size' => $dots_size,
		'color' => us_get_color( $dots_color ),
	)
);

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= '<div class="w-scroller-dots"' . $dots_inline_css . '>';
$output .= '<a href="javascript:void(0);" tabindex="-1" class="w-scroller-dot"><span></span></a>';
$output .= '</div>';
$output .= '</div>';

echo $output;
