<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_message
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 * @param $color		 string Message box color: 'info' / 'attention' / 'success' / 'error' / 'custom'
 * @param $bg_color		 string Background color
 * @param $text_color	 string Text color
 * @param $icon			 string Icon
 * @param $closing		 bool Enable closing?
 * @param $el_class		 string Extra class name
 */

$_atts['class'] = 'w-message';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' color_' . $color;

if ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) {
	$_atts['id'] = $el_id;
}

// Add icon
if ( ! empty( $icon ) ) {
	$_atts['class'] .= ' with_icon';
	$icon_html = '<div class="w-message-icon">' . us_prepare_icon_tag( $icon ) . '</div>';
} else {
	$icon_html = '';
}

// Add close button
if ( $closing AND ! us_amp() ) {
	$_atts['class'] .= ' with_close';
	$closer_html = '<a class="w-message-close" href="javascript:void(0)" aria-label="' . us_translate( 'Close' ) . '"></a>';
} else {
	$closer_html = '';
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= $icon_html;
$output .= '<div class="w-message-body">';
$output .= do_shortcode( wpautop( $content ) );
$output .= '</div>';
$output .= $closer_html;
$output .= '</div>';

echo $output;
