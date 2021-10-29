<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_sharing
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @param $type             string Type: 'simple' / 'solid' / 'outlined' / 'fixed'
 * @param $align         string Alignment: 'left' / 'center' / 'right'
 * @param $color         string Color Style: 'default' / 'primary' / 'secondary'
 * @param $counters         string Share Counters: 'show' / 'hide'
 * @param $email         bool Is Email button available?
 * @param $facebook         bool Is Facebook button available?
 * @param $twitter         bool Is Twitter button available?
 * @param $gplus         bool Is Google button available?
 * @param $linkedin         bool Is LinkedIn button available?
 * @param $pinterest     bool Is Pinterest button available?
 * @param $vk             bool Is VK button available?
 * @param $url             string Sharing URL
 * @param $el_class         string Extra class name
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 */

$_atts['class'] = 'w-sharing';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' type_' . $type;
$_atts['class'] .= ' align_' . $align;
$_atts['class'] .= ' color_' . $color;

if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// The list of available sharing providers
$providers_list = array(
	'email' => __( 'Email this', 'us' ),
	'facebook' => __( 'Share this', 'us' ),
	'twitter' => __( 'Tweet this', 'us' ),
	'linkedin' => __( 'Share this', 'us' ),
	'pinterest' => __( 'Pin this', 'us' ),
	'vk' => __( 'Share this', 'us' ),
	'whatsapp' => __( 'Share this', 'us' ),
	'xing' => __( 'Share this', 'us' ),
	'reddit' => __( 'Share this', 'us' ),
);

$enabled_providers = explode( ',', $providers );

// Keep only the enabled in settings providers
foreach ( $providers_list as $provider => $provider_data ) {
	if ( ! in_array( $provider, $enabled_providers ) ) {
		unset( $providers_list[ $provider ] );
	}
}
if ( empty( $providers_list ) ) {
	return;
}

// Use the current page URL, if not set
if ( empty( $url ) ) {
	$url = wp_parse_url( home_url(), PHP_URL_SCHEME ) . '://';
	$url .= wp_parse_url( home_url(), PHP_URL_HOST );
	$url .= str_replace( '?us_iframe=1', '', $_SERVER['REQUEST_URI'] );
}

if ( $counters == 'show' ) {
	$counts = us_get_sharing_counts( $url, array_keys( $providers_list ) );
}

$post_thumbnail = get_the_post_thumbnail_url( NULL, 'large' );

$list_atts = array(
	'class' => 'w-sharing-list',
	'data-sharing-url' => $url,
	'data-sharing-image' => ( $post_thumbnail ) ? $post_thumbnail : '',
);

// Output the element
$output = '<div ' . us_implode_atts( $_atts ) . '>';
$output .= '<div ' . us_implode_atts( $list_atts ) . '>';

$items_html = '';
foreach ( $providers_list as $provider => $provider_title ) {

	// Leave only supported by AMP providers with specific semantics
	// https://amp.dev/documentation/components/amp-social-share/
	if ( us_amp() ) {
		if ( ! in_array( $provider, array( 'vk', 'xing', 'reddit' ) ) ) {
			$amp_item_atts = array(
				'type' => $provider,
				'aria-label' => $provider_title,
			);

			// Add required attributes
			if ( $provider == 'pinterest' ) {
				$amp_item_atts['data-param-media'] = $post_thumbnail;
			} elseif ( $provider == 'facebook' ) {
				$amp_item_atts['data-param-app_id'] = trim( us_get_option( 'facebook_app_id', '' ) );
			}
			$items_html .= '<amp-social-share ' . us_implode_atts( $amp_item_atts ) . '></amp-social-share>';
		}
		continue;
	}

	$item_atts = array(
		'class' => 'w-sharing-item ' . $provider,
		'href' => 'javascript:void(0)',
		'title' => $provider_title,
		'aria-label' => $provider_title,
	);

	$items_html .= '<a ' . us_implode_atts( $item_atts ) . '>';

	if ( $provider == 'email' ) {
		$items_html .= '<i class="fas fa-envelope"></i>';
	} else {
		$items_html .= '<i class="fab fa-' . $provider . '"></i>';
	}

	if ( $counters == 'show' AND ! empty( $counts[ $provider ] ) ) {
		$items_html .= '<span class="w-sharing-count">' . $counts[ $provider ] . '</span>';
	}
	$items_html .= '</a>';
}
$output .= $items_html;
$output .= '</div>';

// Add tooltip semantics, if text selection is enabled
if ( $text_selection AND ! us_amp() ) {
	$sharing_area = ( $text_selection_post ) ? 'post_content' : 'l-main';
	$output .= '<div class="w-sharing-tooltip" style="display:none" data-sharing-area="' . $sharing_area . '">';
	$output .= '<div ' . us_implode_atts( $list_atts ) . '>';
	$output .= $items_html;

	// Add "copy2clipboard" item
	$item_atts = array(
		'class' => 'w-sharing-item copy2clipboard',
		'href' => 'javascript:void(0)',
		'title' => us_translate( 'Copy' ),
		'aria-label' => us_translate( 'Copy' ),
	);
	$output .= '<a ' . us_implode_atts( $item_atts ) . '>';
	$output .= '<i class="fas fa-copy"></i>'; // predefined icon
	$output .= '</a>';
	$output .= '</div></div>';
}

$output .= '</div>';

echo $output;
