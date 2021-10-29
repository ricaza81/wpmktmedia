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

$classes = isset( $classes ) ? $classes : '';
$classes .= $icon_html = $closer_html = '';

$classes .= ' color_' . $color;

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

if ( ! empty( $icon ) ) {
	$icon_html = '<div class="w-message-icon">' . us_prepare_icon_tag( $icon ) . '</div>';
	$classes .= ' with_icon';
}

// Add close button
if ( $closing AND ! us_amp() ) {
	$classes .= ' with_close';
	$closer_html = '<a class="w-message-close" href="javascript:void(0)" aria-label="' . us_translate( 'Close' ) . '"></a>';
}

// Output the element
$output = '<div class="w-message' . $classes . '"' . $el_id . '>' . $icon_html;
$output .= '<div class="w-message-body">' . do_shortcode( wpautop( $content ) ) . '</div>' . $closer_html . '</div>';

echo $output;
