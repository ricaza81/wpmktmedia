<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output html element
 *
 * @var $content        string
 * @var $design_options array
 * @var $classes        string
 * @var $id             string
 */

$classes = isset( $classes ) ? $classes : '';
$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';

echo '<div class="w-html' . $classes . '">';
echo do_shortcode( rawurldecode( base64_decode( $content ) ) );
echo '</div>';
