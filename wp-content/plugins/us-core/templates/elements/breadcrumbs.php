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

$_atts['class'] = 'g-breadcrumbs';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' separator_' . $separator_type;
$_atts['class'] .= ' align_' . $align;

if ( ! $show_current ) {
	$_atts['class'] .= ' hide_current';
}

// When some values are set in Design options, add the specific classes
if ( us_design_options_has_property( $css, 'color' ) ) {
	$_atts['class'] .= ' has_text_color';
}

$item_atts['class'] = 'g-breadcrumbs-item';

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
$link_attr = $name_attr = $position_attr = '';
if ( us_get_option( 'schema_markup' ) ) {

	// Do not add markup for WooCommerce Breadcrumbs
	if ( ! ( function_exists( 'woocommerce_breadcrumb' ) AND is_woocommerce() ) ) {
		$_atts['itemscope'] = '';
		$_atts['itemtype'] = 'http://schema.org/BreadcrumbList';

		$item_atts['itemscope'] = '';
		$item_atts['itemprop'] = 'itemListElement';
		$item_atts['itemtype'] = 'http://schema.org/ListItem';
	}

	$link_attr = ' itemprop="item"';
	$name_attr = ' itemprop="name"';
	$position_attr = ' itemprop="position"';
}

// Homepage Label
$home = strip_tags( $home );

// The breadcrumb’s container starting code
$list_before = '<ol' . us_implode_atts( $_atts ) . '>';

// The breadcrumb’s container ending code
$list_after = '</ol>';

// Code before single crumb
$item_before = '<li' . us_implode_atts( $item_atts ) . '>';

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
