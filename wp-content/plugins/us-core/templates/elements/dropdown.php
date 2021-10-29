<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output dropdown element
 *
 * @var $source            string Source: 'own' / 'sidebar' / 'wpml' / 'polylang'
 * @var $link_title        string
 * @var $link_icon         string
 * @var $sidebar_id        string
 * @var $links             array
 * @var $wpml_switcher     string / array
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
if ( $source == 'own' ) {
	// Decoding links in case it is shortcode
	if ( ! empty( $links ) AND ! is_array( $links ) ) {
		$links = json_decode( urldecode( $links ), TRUE );
		if ( ! is_array( $links ) ) {
			$links = array();
		}
	} elseif ( empty( $links ) OR ! is_array( $links ) ) {
		$links = array();
	}

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

		// Fallback for var type
		if ( is_array( $wpml_switcher ) ) {
			$wpml_switcher = implode( ',', $wpml_switcher );
		}

		if ( strpos( $wpml_switcher, 'native_lang' ) !== FALSE ) {
			$data_language['title'] = $wpml_lang['native_name'];
			if ( strpos( $wpml_switcher, 'display_lang' ) !== FALSE AND $wpml_lang['native_name'] != $wpml_lang['translated_name'] ) {
				$data_language['title'] .= ' (' . $wpml_lang['translated_name'] . ')';
			}
		} elseif ( strpos( $wpml_switcher, 'display_lang' ) !== FALSE ) {
			$data_language['title'] = $wpml_lang['translated_name'];
		}
		if ( strpos( $wpml_switcher, 'flag' ) !== FALSE ) {
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
			'title' => '',
			'icon' => '',
		);

		// Fallback for var type
		if ( is_array( $polylang_switcher ) ) {
			$polylang_switcher = implode( ',', $polylang_switcher );
		}

		if ( strpos( $polylang_switcher, 'full_name' ) !== FALSE ) {
			$data_language['title'] = $pll_lang['name'];
		}
		if ( strpos( $polylang_switcher, 'flag' ) !== FALSE ) {
			$data_language['flag'] = '<img src="' . $pll_lang['flag'] . '" alt="' . $pll_lang['name'] . '" />';
		}

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
$output = '<div' . us_implode_atts( $_atts ) . '>';
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

	$output .= '<a' . us_implode_atts( $current_anchor_atts ) . '>';
	if ( ! empty( $data['current']['flag'] ) ) {
		$output .= $data['current']['flag'];
	}
	$output .= $data['current']['icon'];

	// Apply filters to title
	$title = us_replace_dynamic_value( $data['current']['title'] );
	$title = strip_tags( $title );
	$title = wptexturize( $title );

	$output .= '<span class="w-dropdown-item-title">' . $title . '</span>';
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
		$output .= '<a' . us_implode_atts( $link['link_atts'] ) . '>';
		if ( ! empty( $link['flag'] ) ) {
			$output .= $link['flag'];
		}
		$output .= $link['icon'];

		// Apply filters to title
		$title = us_replace_dynamic_value( $link['title'] );
		$title = strip_tags( $title );
		$title = wptexturize( $title );

		$output .= '<span class="w-dropdown-item-title">' . $title . '</span>';
		$output .= '</a>';
	}
}
$output .= '</div></div>';
$output .= '</div></div>';

echo $output;
