<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_breadcrumbs
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @param $home              string Homepage Label
 * @param $font_size         string Font Size
 * @param $align             string Alignment
 * @param $separator_type    string Separator Type: 'icon' / 'custom'
 * @param $separator_icon    string Separator Icon
 * @param $separator_symbol  string Separator Symbol
 * @param $show_current      bool   Show current page?
 * @param $el_class          string Extra class name
 * @var   $shortcode         string Current shortcode name
 * @var   $shortcode_base    string The original called shortcode name (differs if called an alias)
 * @var   $content           string Shortcode's inner content
 * @var   $classes           string Extend class names
 *
 */


// Don't show the element on the homepage
if ( is_front_page() ) {
	return;
}

$classes = isset( $classes ) ? $classes : '';
$classes .= ' separator_' . $separator_type;
$classes .= ' align_' . $align;
if ( ! $show_current ) {
	$classes .= ' hide_current';
}

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$classes .= ' has_text_color';
}

// Generate separator between crumbs
$delimiter = '';
if ( $separator_type == 'icon' ) {
	$delimiter = us_prepare_icon_tag( $separator_icon );
} elseif ( $separator_type == 'custom' ) {
	$delimiter = strip_tags( $separator_symbol );
}
if ( $delimiter != '' ) {
	$delimiter = '<li class="g-breadcrumbs-separator">' . $delimiter . '</li>';
}

// Generate microdata markup
$microdata_list = $microdata_item = $link_attr = $name_attr = $position_attr = '';
if ( us_get_option( 'schema_markup' ) ) {
	$microdata_list = ' itemscope itemtype="http://schema.org/BreadcrumbList"';
	$microdata_item = ' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"';
	$link_attr = ' itemprop="item"';
	$name_attr = ' itemprop="name"';
	$position_attr = ' itemprop="position"';
}

if ( function_exists( 'woocommerce_breadcrumb' ) AND is_woocommerce() ) {
	// Remove markup from WooCommerce Breadcrumbs
	$microdata_list = $microdata_item = '';
}

// Homepage Label
$home = strip_tags( $home );

// The breadcrumb’s container starting code
$list_before = '<ol class="g-breadcrumbs' . $classes . '"' . $el_id . $microdata_list . '>';

// The breadcrumb’s container ending code
$list_after = '</ol>';

// Code before single crumb
$item_before = '<li class="g-breadcrumbs-item"' . $microdata_item . '>';

// Code after single crumb
$item_after = '</li>';

// Return default WooCommerce breadcrumbs
if ( function_exists( 'woocommerce_breadcrumb' ) AND is_woocommerce() ) {

	return woocommerce_breadcrumb(
		array(
			'wrap_before' => $list_before,
			'wrap_after' => $list_after,
			'delimiter' => $delimiter,
			'before' => $item_before,
			'after' => $item_after,
			'home' => $home,
		)
	);

	// Return default bbPress breadcrumbs
} elseif ( function_exists( 'bbp_get_breadcrumb' ) AND is_singular( array( 'topic', 'forum', 'reply' ) ) ) {
	echo bbp_get_breadcrumb(
		array(
			'before' => $list_before,
			'after' => $list_after,
			'sep' => $delimiter,
			'crumb_before' => $item_before,
			'crumb_after' => $item_after,
		)
	);

	// Output theme breadcrumbs
} else {
	$us_breadcrumbs = new US_Breadcrumbs( $delimiter, $home, $item_before, $item_after, $link_attr, $name_attr, $position_attr );
	echo $list_before . $us_breadcrumbs->render() . $list_after;
}
