<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_contacts
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 * @param  $address		 string Addresss
 * @param  $phone		 string Phone
 * @param  $fax			 string Mobiles
 * @param  $email		 string Email
 * @param  $el_class	 string Extra class name
 */

$_atts['class'] = 'w-contacts';
$_atts['class'] .= isset( $classes ) ? $classes : '';
if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Output the element
$output = '<div ' . us_implode_atts( $_atts ) . '>';
$output .= '<div class="w-contacts-list">';
if ( ! empty( $address ) ) {
	$output .= '<div class="w-contacts-item for_address"><span class="w-contacts-item-value">' . $address . '</span></div>';
}
if ( ! empty( $phone ) ) {
	$output .= '<div class="w-contacts-item for_phone"><span class="w-contacts-item-value">' . $phone . '</span></div>';
}
if ( ! empty( $fax ) ) {
	$output .= '<div class="w-contacts-item for_mobile"><span class="w-contacts-item-value">' . $fax . '</span></div>';
}
if ( ! empty( $email ) AND is_email( $email ) ) {
	$output .= '<div class="w-contacts-item for_email"><span class="w-contacts-item-value">';
	$output .= '<a href="mailto:' . $email . '">' . $email . '</a></span></div>';
}
$output .= '</div>';
$output .= '</div>';

echo $output;
