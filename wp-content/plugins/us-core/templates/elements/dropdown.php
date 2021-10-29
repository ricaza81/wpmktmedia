<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output dropdown element
 *
 * @var $source            string Source: 'own' / 'sidebar' / 'wpml' / 'polylang'
 * @var $link_title        string
 * @var $link_icon         string
 * @var $sidebar_id        string
 * @var $links             array
 * @var $wpml_switcher     array
 * @var $dropdown_open     string 'click' / 'hover'
 * @var $dropdown_dir      string 'left' / 'right'
 * @var $dropdown_effect   string
 * @var $size              int
 * @var $size_tablets      int
 * @var $size_mobiles      int
 * @var $design_options    array
 * @var $classes           string
 * @var $id                string
 */

if ( in_array( $source, array( 'wpml', 'polylang' ) ) AND ! has_filter( 'us_tr_current_language' ) ) {
	return;
}

$_atts['class'] = 'w-dropdown';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' source_' . $source;
$_atts['class'] .= ' dropdown_' . $dropdown_effect;
$_atts['class'] .= ' drop_to_' . $dropdown_dir;
$_atts['class'] .= ' open_on_' . $dropdown_open;

if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}
if ( us_amp() AND empty( $_atts['id'] ) ) {
	$_atts['id'] = str_replace( ':', '_', $id );
}

$data = array(
	'current' => array(),
	'list' => array(),
);

// Custom Links
if ( $source == 'own' AND is_array( $links ) ) {
	foreach ( $links as $link ) {
		$data['list'][] = array(
			'icon' => ! empty( $link['icon'] ) ? us_prepare_icon_tag( $link['icon'] ) : '',
			'title' => $link['label'],
			'link_atts' => us_generate_link_atts( $link['url'] ),
		);
	}

	// WPML Language Switcher
} elseif ( $source == 'wpml' AND class_exists( 'SitePress' ) ) {
	$wpml_langs = apply_filters( 'wpml_active_languages', NULL );
	foreach ( $wpml_langs as $wpml_lang ) {
		$data_language = array(
			'title' => '',
			'icon' => '',
		);

		if ( in_array( 'native_lang', $wpml_switcher ) ) {
			$data_language['title'] = $wpml_lang['native_name'];
			if ( in_array( 'display_lang', $wpml_switcher ) AND $wpml_lang['native_name'] != $wpml_lang['translated_name'] ) {
				$data_language['title'] .= ' (' . $wpml_lang['translated_name'] . ')';
			}
		} elseif ( in_array( 'display_lang', $wpml_switcher ) ) {
			$data_language['title'] = $wpml_lang['translated_name'];
		}
		if ( in_array( 'flag', $wpml_switcher ) ) {
			$data_language['flag'] = '<img src="' . $wpml_lang['country_flag_url'] . '" alt="' . $wpml_lang['language_code'] . '" />';
		}

		if ( $wpml_lang['active'] ) {
			$data['current'] = $data_language;
		} else {
			$data_language['link_atts']['href'] = $wpml_lang['url'];
			$data['list'][] = $data_language;
		}
	}

	// Polylang Language Switcher
} elseif ( $source == 'polylang' AND function_exists( 'pll_the_languages' ) ) {
	$pll_langs = pll_the_languages( array( 'raw' => 1 ) );
	foreach ( $pll_langs as $pll_lang ) {
		$data_language = array(
			'title' => in_array( 'full_name', $polylang_switcher ) ? $pll_lang['name'] : '',
			'flag' => in_array( 'flag', $polylang_switcher ) ? '<img src="' . $pll_lang['flag'] . '" alt="' . $pll_lang['name'] . '" />' : '',
			'icon' => '', // set empty icon
		);

		if ( $pll_lang['current_lang'] ) {
			$data['current'] = $data_language;
		} else {
			$data_language['link_atts']['href'] = $pll_lang['url'];
			$data['list'][] = $data_language;
		}
	}
}
if ( in_array( $source, array( 'sidebar', 'own' ) ) ) {
	$data['current']['title'] = $link_title;
	$data['current']['icon'] = us_prepare_icon_tag( $link_icon );
}

// Output the element
$output = '<div ' . us_implode_atts( $_atts ) . '>';
$output .= '<div class="w-dropdown-h">';
if ( ! empty( $data['current'] ) ) {
	$output .= '<div class="w-dropdown-current">';
	$current_anchor_atts = array(
		'class' => 'w-dropdown-item',
	);

	// Add specific attribute for AMP version
	if ( us_amp() ) {
		$current_anchor_atts['on'] = 'tap:' . $_atts['id'] . '.toggleClass(class=\'opened\')';
	} else {
		$current_anchor_atts['href'] = 'javascript:void(0)';
	}

	$output .= '<a ' . us_implode_atts( $current_anchor_atts ) . '>';
	if ( ! empty( $data['current']['flag'] ) ) {
		$output .= $data['current']['flag'];
	}
	$output .= $data['current']['icon'];
	$output .= '<span class="w-dropdown-item-title">' . strip_tags( $data['current']['title'] ) . '</span>';
	$output .= '</a></div>';
}
$output .= '<div class="w-dropdown-list">';
$output .= '<div class="w-dropdown-list-h">';
if ( $source == 'sidebar' ) {
	ob_start();
	dynamic_sidebar( $sidebar_id );
	$output .= ob_get_clean();
} else {
	foreach ( $data['list'] as $link ) {
		$link['link_atts']['class'] = 'w-dropdown-item smooth-scroll';
		$output .= '<a ' . us_implode_atts( $link['link_atts'] ) . '>';
		if ( ! empty( $link['flag'] ) ) {
			$output .= $link['flag'];
		}
		$output .= $link['icon'];
		$output .= '<span class="w-dropdown-item-title">' . strip_tags( $link['title'] ) . '</span>';
		$output .= '</a>';
	}
}
$output .= '</div></div>';
$output .= '</div></div>';

echo $output;
