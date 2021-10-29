<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Interactive Banner
 *
 * @var $image string Image ID
 * @var $size string Image size
 * @var $title string
 * @var $desc string description field
 * @var $link string
 * @var $font string
 * @var $tag string Title HTML tag
 * @var $align string
 * @var $animation string
 * @var $easing string
 * @var $ratio  string Aspect ratio: '2x1' / '3x2' / '4x3' / '1x1' / '3x4' / '2x3' / '1x2'
 * @var $desc_size string
 * @var $classes string Extend class names
 */

$_atts['class'] = 'w-ibanner';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' animation_' . $animation;
$_atts['class'] .= ' ratio_' . $ratio;
$_atts['class'] .= ' easing_' . $easing;

if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Fallback since version 7.1
if ( ! empty( $align ) ) {
	$_atts['class'] .= ' align_' . $align;
}

$title_inline_css = us_prepare_inline_css(
	array(
		'font-size' => $title_size,
	)
);

$title = wptexturize( $title );
$title = strip_tags( $title, '<strong><br>' );

$image_url = wp_get_attachment_image_url( $image, $size );
if ( empty( $image_url ) ) {
	$image_url = us_get_img_placeholder( $size, TRUE );
}

// Output the element
$output = '<div ' . us_implode_atts( $_atts ) . '>';
$output .= '<div class="w-ibanner-h">';

// Banner Image
$output .= '<div class="w-ibanner-image" style="background-image: url(' . esc_url( $image_url ) . ')"></div>';

$output .= '<div class="w-ibanner-content"><div class="w-ibanner-content-h">';

// Banner Title
$output .= '<' . $title_tag . ' class="w-ibanner-title"' . $title_inline_css . '>';
$output .= $title;
$output .= '</' . $title_tag . '>';

// Banner Description
$output .= '<div class="w-ibanner-desc">' . wpautop( $desc ) . '</div>';

$output .= '</div></div></div>';

// Banner link
$link_atts = us_generate_link_atts( $link );
if ( ! empty( $link_atts['href'] ) ) {
	$link_atts['aria-label'] = strip_tags( $title );
	$output .= '<a ' . us_implode_atts( $link_atts ) . '></a>';
}

$output .= '</div>';

echo $output;
