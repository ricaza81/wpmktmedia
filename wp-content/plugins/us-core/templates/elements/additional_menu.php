<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Simple Menu element
 */

if ( ! is_nav_menu( $source ) ) {
	return;
}

$_atts['class'] = 'w-menu';
$_atts['class'] .= isset( $classes ) ? $classes : '';

// Force horizontal layout for element in header
if ( $us_elm_context == 'header' ) {
	$layout = 'hor';
}

$_atts['class'] .= ' layout_' . $layout;
$_atts['class'] .= ( $spread ) ? ' spread' : '';

$css_styles = '';
$depth = 1;
if ( $us_elm_context == 'shortcode' ) {
	$responsive_width = trim( $responsive_width );

	$_atts['class'] .= ' style_' . $main_style;
	$_atts['class'] .= empty( $responsive_width ) ? ' not_responsive' : '';

	// Fallback since version 7.1
	if ( ! empty( $align ) ) {
		$_atts['class'] .= ' align_' . $align;
	}

	// Needs to override alignment on mobiles
	if ( in_array( 'mobiles', us_design_options_has_property( $css, 'text-align' ) ) ) {
		$_atts['class'] .= ' has_text_align_on_mobiles';
	}

	// Generate unique class for css styles
	global $us_menu_id;
	$us_menu_id = isset( $us_menu_id ) ? ( $us_menu_id + 1 ) : 1;
	$_atts['class'] .= ' us_menu_' . $us_menu_id;

	// Gap between Main items
	if ( ! empty( $main_gap ) ) {
		$gap_direction = 'bottom';
		if ( $layout == 'hor' ) {
			$gap_direction = is_rtl() ? 'left' : 'right';
		}
		$css_styles .= '.us_menu_' . $us_menu_id . ' .menu > li { margin-' . $gap_direction . ':' . $main_gap . '; }';
	}

	// Gap between Main items
	if ( $main_style == 'blocks' ) {
		$css_styles .= '.us_menu_' . $us_menu_id . ' .menu > li > a { padding:' . $main_ver_indent . ' ' . $main_hor_indent . '; }';
	}

	// Main Items colors
	if ( ! empty( $main_color_bg ) AND $main_style == 'blocks' ) {
		$main_color_bg = us_get_color( $main_color_bg, /* Gradient */ TRUE );
		$css_styles .= '.us_menu_' . $us_menu_id . ' .menu > li > a { background:' . $main_color_bg . '; }';
	}
	if ( ! empty( $main_color_text ) ) {
		$main_color_text = us_get_color( $main_color_text );
		$css_styles .= '.us_menu_' . $us_menu_id . ' .menu > li > a { color:' . $main_color_text . '; }';
	}
	if ( ! empty( $main_color_bg_hover ) AND $main_style == 'blocks' ) {
		$main_color_bg_hover = us_get_color( $main_color_bg_hover, /* Gradient */ TRUE );
		$css_styles .= '.us_menu_' . $us_menu_id . ' .menu > .menu-item:not(.current-menu-item) > a:hover { background:' . $main_color_bg_hover . '; }';
	}
	if ( ! empty( $main_color_text_hover ) ) {
		$main_color_text_hover = us_get_color( $main_color_text_hover );
		$css_styles .= '.us_menu_' . $us_menu_id . ' .menu > .menu-item:not(.current-menu-item) > a:hover { color:' . $main_color_text_hover . '; }';
	}
	if ( ! empty( $main_color_bg_active ) AND $main_style == 'blocks' ) {
		$main_color_bg_active = us_get_color( $main_color_bg_active, /* Gradient */ TRUE );
		$css_styles .= '.us_menu_' . $us_menu_id . ' .menu > .current-menu-item > a { background:' . $main_color_bg_active . '; }';
	}
	if ( ! empty( $main_color_text_active ) ) {
		$main_color_text_active = us_get_color( $main_color_text_active );
		$css_styles .= '.us_menu_' . $us_menu_id . ' .menu > .current-menu-item > a { color:' . $main_color_text_active . '; }';
	}

	// Show Sub items
	if ( $sub_items ) {
		$depth = 0;
		$_atts['class'] .= ' with_children';

		// Gap between Sub items
		if ( ! empty( $sub_gap ) ) {
			$css_styles .= '.us_menu_' . $us_menu_id . ' .sub-menu { margin-top:' . $sub_gap . '; }';
			$css_styles .= '.us_menu_' . $us_menu_id . ' .sub-menu li { margin-bottom:' . $sub_gap . '; }';
		}
	}

	// Switch horizontal to vertical at screens below defined width
	if ( ! empty( $responsive_width ) ) {
		$css_styles .= '@media ( max-width:' . $responsive_width . ' ) {';
		$css_styles .= '.us_menu_' . $us_menu_id . ' .menu { display: block !important; }';
		if ( ! empty( $main_gap ) ) {
			$css_styles .= '.us_menu_' . $us_menu_id . ' .menu > li { margin: 0 0 ' . $main_gap . '; }';
		}
		$css_styles .= '}';
	}
}

if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Output the element
$output = '<div ' . us_implode_atts( $_atts ) . '>';
$output .= wp_nav_menu(
	array(
		'menu' => $source,
		'container' => FALSE,
		'depth' => $depth,
		'item_spacing' => 'discard',
		'echo' => FALSE,
	)
);
if ( ! empty( $css_styles ) ) {
	$output .= '<style>' . us_minify_css( $css_styles ) . '</style>';
}
$output .= '</div>';

echo $output;
