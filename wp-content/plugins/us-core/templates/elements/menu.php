<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Menu element
 *
 * @var $hover_effect    string Hover Effect: 'simple' / 'underline'
 * @var $dropdown_effect string Dropdown Effect
 * @var $vstretch        boolean Stretch menu items vertically to fit the available height
 * @var $indents         int Items Indents
 * @var $mobile_width    int On which screen width menu becomes mobile
 * @var $mobile_behavior boolean Mobile behavior
 * @var $design_options  array
 * @var $id              string
 * @var $classes         string
 * @var $source          string WP Menu source
 */

if ( substr( $source, 0, 9 ) == 'location:' ) {
	$location = substr( $source, 9 );
	$theme_locations = get_nav_menu_locations();
	if ( isset( $theme_locations[ $location ] ) ) {
		$menu_obj = get_term( $theme_locations[ $location ], 'nav_menu' );
		if ( $menu_obj ) {
			$source = $menu_obj->slug;
		} else {
			return;
		}
	} else {
		return;
	}
} else {
	$location = NULL;
}

if ( empty( $location ) AND ( empty( $source ) OR ! is_nav_menu( $source ) ) ) {
	return;
}

// Force dropdown "none" animation for AMP
if ( us_amp() ) {
	$dropdown_effect = 'none';
}

$_atts['class'] = 'w-nav';
$_atts['class'] .= us_amp() ? ' type_mobile' : ' type_desktop';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ( $vstretch ) ? ' height_full' : '';
$_atts['class'] .= ( $spread ) ? ' spread' : '';
$_atts['class'] .= ' dropdown_' . $dropdown_effect;
$_atts['class'] .= ' m_align_' . $mobile_align;
$_atts['class'] .= ' m_layout_' . $mobile_layout;

if ( $mobile_layout == 'panel' ) {
	$_atts['class'] .= ' m_effect_' . $mobile_effect_p;
}
if ( $mobile_layout == 'fullscreen' ) {
	$_atts['class'] .= ' m_effect_' . $mobile_effect_f;
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

if ( us_get_option( 'schema_markup' ) ) {
	$_atts['itemscope'] = '';
	$_atts['itemtype'] = 'https://schema.org/SiteNavigationElement';
}

// Output the element
echo '<nav' . us_implode_atts( $_atts ) . '>';

$_open_button_atts['class'] = 'w-nav-control';
$_open_button_atts['aria-label'] = us_translate( 'Menu' );

// Set AMP page attributes
if ( us_amp() ) {
	$amp_menu_id = str_replace( 'menu:', '', $id );

	$_open_button_atts['id'] = 'hamburger-' . $amp_menu_id;
	$_open_button_atts['on'] = 'tap:hamburger-' . $amp_menu_id . '.toggleClass(class=\'active\'),w-nav-list-' . $amp_menu_id . '.toggleClass(class=\'active\')';

} else {
	$_open_button_atts['href'] = 'javascript:void(0);';
}

echo '<a' . us_implode_atts( $_open_button_atts ) . '>';

if ( $mobile_icon_text == 'left' ) {
	echo '<span>' . strip_tags( $mobile_icon_text_label ) . '</span>';
}
echo '<div class="w-nav-icon"><div></div></div>';
if ( $mobile_icon_text == 'right' ) {
	echo '<span>' . strip_tags( $mobile_icon_text_label ) . '</span>';
}
echo '</a>';

$_list_atts['class'] = 'w-nav-list level_1 hide_for_mobiles hover_' . $hover_effect;

if ( us_amp() ) {
	$_list_atts['id'] = 'w-nav-list-' . $amp_menu_id;
}

// Items list
echo '<ul' . us_implode_atts( $_list_atts ) . '>';
if ( $location ) {
	wp_nav_menu(
		array(
			'theme_location' => $location,
			'container' => FALSE,
			'walker' => new US_Walker_Nav_Menu( $mobile_behavior ),
			'items_wrap' => '%3$s',
			'fallback_cb' => FALSE,
		)
	);
} else {
	wp_nav_menu(
		array(
			'menu' => $source,
			'container' => FALSE,
			'walker' => new US_Walker_Nav_Menu( $mobile_behavior ),
			'items_wrap' => '%3$s',
			'fallback_cb' => FALSE,
		)
	);
}

$_close_button_atts['class'] = 'w-nav-close';
if ( us_amp() ) {
	$_close_button_atts['id'] = 'w-nav-close-' . $amp_menu_id;
	$_close_button_atts['on'] = 'tap:hamburger-' . $amp_menu_id . '.toggleClass(class=\'active\'),w-nav-list-' . $amp_menu_id . '.toggleClass(class=\'active\')';
}
echo '<li' . us_implode_atts( $_close_button_atts ) . '></li>';
echo '</ul>';

if ( ! us_amp() ) {
	echo '<div class="w-nav-options hidden"';
	echo us_pass_data_to_js(
		array(
			'mobileWidth' => (int) $mobile_width,
			'mobileBehavior' => (int) $mobile_behavior,
		)
	);
	echo '></div>';
}

echo '</nav>';
