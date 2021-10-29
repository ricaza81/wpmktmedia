<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_pricing
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 * @param $style         string Table style: '1' / '2'
 * @param $items         string Pricing table items
 * @param $el_class         string Extra class name
 */

$_atts['class'] = 'w-pricing';
$_atts['class'] .= isset( $classes ) ? $classes : '';

if ( empty( $items ) ) {
	$items = array();
} else {
	$items = json_decode( urldecode( $items ), TRUE );
	if ( ! is_array( $items ) ) {
		$items = array();
	}
}
if ( ! empty( $style ) ) {
	$_atts['class'] .= ' style_' . $style;
}
if ( count( $items ) > 0 ) {
	$_atts['class'] .= ' items_' . count( $items );
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

$items_html = '';
foreach ( $items as $index => $item ) {
	/**
	 * Filtering the included items
	 *
	 * @param $item ['title'] string Item title
	 * @param $item ['type'] string Item type: 1/0
	 * @param $item ['price'] string Item price
	 * @param $item ['substring'] string Price substring
	 * @param $item ['features'] string Comma-separated list of features
	 * @param $item ['btn_text'] string Button label
	 * @param $item ['btn_link'] string Button link in a serialized format: 'url:http%3A%2F%2Fwordpress.org|title:WP%20Website|target:_blank|rel:nofollow'
	 * @param $item ['btn_style'] string Button Style
	 * @param $item ['btn_size'] string Button size
	 * @param $item ['btn_icon'] string Button icon
	 * @param $item ['btn_iconpos'] string Icon position: 'left' / 'right'
	 */
	$item['type'] = ( isset( $item['type'] ) AND $item['type'] ) ? 'featured' : 'default';
	$item['btn_iconpos'] = ( isset( $item['btn_iconpos'] ) ) ? $item['btn_iconpos'] : 'left';

	$items_html .= '<div class="w-pricing-item type_' . $item['type'] . '">';
	$items_html .= '<div class="w-pricing-item-h">';
	$items_html .= '<div class="w-pricing-item-header">';
	if ( ! empty( $item['title'] ) ) {
		$items_html .= '<div class="w-pricing-item-title">' . us_replace_dynamic_value( $item['title'] ) . '</div>';
	}
	$items_html .= '<div class="w-pricing-item-price">';
	if ( ! empty( $item['price'] ) ) {
		$items_html .= us_replace_dynamic_value( $item['price'] );
	}
	if ( ! empty( $item['substring'] ) ) {
		$items_html .= '<small>' . us_replace_dynamic_value( $item['substring'] ) . '</small>';
	}
	$items_html .= '</div></div>';
	if ( ! empty( $item['features'] ) ) {
		$items_html .= '<ul class="w-pricing-item-features">';
		$features = explode( "\n", trim( $item['features'] ) );
		foreach ( $features as $feature ) {
			$items_html .= '<li class="w-pricing-item-feature">' . us_replace_dynamic_value( $feature ) . '</li>';
		}
		$items_html .= '</ul>';
	}
	if ( ! empty( $item['btn_text'] ) AND ! empty( $item['btn_link'] ) ) {

		// Check existence of Button Style, if not, set the default
		$btn_styles = us_get_btn_styles();
		if ( ! array_key_exists( $item['btn_style'], $btn_styles ) ) {
			$item['btn_style'] = '1';
		}

		$btn_atts['class'] = 'w-btn us-btn-style_' . $item['btn_style'];

		if ( ! empty( $item['btn_size'] ) ) {
			$btn_atts['style'] = 'font-size:' . $item['btn_size'];
		}

		$icon_html = '';
		if ( ! empty( $item['btn_icon'] ) ) {
			$icon_html = us_prepare_icon_tag( $item['btn_icon'] );
			$btn_atts['class'] .= ' icon_at' . $item['btn_iconpos'];
		}
		$btn_link_atts = us_generate_link_atts( $item['btn_link'] );

		// Apply filters to button label
		$btn_label = us_replace_dynamic_value( $item['btn_text'] );
		$btn_label = strip_tags( $btn_label, '<br>' );
		$btn_label = wptexturize( $btn_label );

		$items_html .= '<div class="w-pricing-item-footer">';
		$items_html .= '<a' . us_implode_atts( $btn_atts + $btn_link_atts ) . '>';
		$items_html .= ( $item['btn_iconpos'] == 'left' ) ? $icon_html : '';
		$items_html .= '<span class="w-btn-label">' . $btn_label . '</span>';
		$items_html .= ( $item['btn_iconpos'] == 'right' ) ? $icon_html : '';
		$items_html .= '</a>';

		$items_html .= '</div>';
	}
	$items_html .= '</div></div>';
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>' . $items_html . '</div>';

echo $output;
