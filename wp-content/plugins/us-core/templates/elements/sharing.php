<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_sharing
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @param $type           string Type: 'simple' / 'solid' / 'outlined' / 'fixed'
 * @param $align          string Alignment: 'left' / 'center' / 'right'
 * @param $color          string Color Style: 'default' / 'primary' / 'secondary'
 * @param $email          bool Is Email button available?
 * @param $facebook       bool Is Facebook button available?
 * @param $twitter        bool Is Twitter button available?
 * @param $gplus          bool Is Google button available?
 * @param $linkedin       bool Is LinkedIn button available?
 * @param $pinterest      bool Is Pinterest button available?
 * @param $vk             bool Is VK button available?
 * @param $url            string Sharing URL
 * @param $el_class       string Extra class name
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 */

// Sharing Index not to add several tooltips
global $us_sharing_index;
if ( $text_selection ) {
	$us_sharing_index = isset( $us_sharing_index ) ? ( $us_sharing_index + 1 ) : 1;
}

$_atts['class'] = 'w-sharing';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' type_' . $type;
$_atts['class'] .= ' align_' . $align;
$_atts['class'] .= ' color_' . $color;

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// The list of available sharing providers
$providers_list = array(
	'email' => array(
		'url' => 'mailto:?subject={{text}}&body={{url}}',
		'features' => '', // use empty string just to define it
		'title' => __( 'Email this', 'us' ),
	),
	'facebook' => array(
		'url' => 'https://www.facebook.com/sharer/sharer.php?u={{url}}&quote={{text}}',
		'features' => 'toolbar=0,width=900,height=500',
		'title' => __( 'Share this', 'us' ),
	),
	'twitter' => array(
		'url' => 'https://twitter.com/intent/tweet?text={{text}}&url={{url}}',
		'features' => 'toolbar=0,width=650,height=360',
		'title' => __( 'Tweet this', 'us' ),
	),
	'linkedin' => array(
		'url' => 'https://www.linkedin.com/shareArticle?mini=true&url={{url}}',
		'features' => 'toolbar=no,width=550,height=550',
		'title' => __( 'Share this', 'us' ),
	),
	'pinterest' => array(
		'url' => 'https://www.pinterest.com/pin/create/button/?url={{url}}&media={{image}}&description={{text}}',
		'features' => 'toolbar=no,width=700,height=300',
		'title' => __( 'Pin this', 'us' ),
	),
	'vk' => array(
		'url' => 'https://vk.com/share.php?url={{url}}&title={{text}}&description=&image={{image}}',
		'features' => 'toolbar=no,width=700,height=300',
		'title' => __( 'Share this', 'us' ),
	),
	'whatsapp' => array(
		'url' => 'https://web.whatsapp.com/send?text={{text}} {{url}}',
		'features' => 'toolbar=0,width=900,height=500',
		'title' => __( 'Share this', 'us' ),
	),
	'xing' => array(
		'url' => 'https://www.xing.com/spi/shares/new?url={{url}}',
		'features' => 'toolbar=no,width=900,height=500',
		'title' => __( 'Share this', 'us' ),
	),
	'reddit' => array(
		'url' => 'https://www.reddit.com/submit?url={{url}}&title={{text}}',
		'features' => 'toolbar=no,width=900,height=500',
		'title' => __( 'Share this', 'us' ),
	),
	'telegram' => array(
		'url' => 'https://t.me/share/url?url={{url}}&text={{text}}',
		'features' => 'toolbar=no,width=600,height=450',
		'title' => __( 'Share this', 'us' ),
	),
);

$enabled_providers = explode( ',', $providers );

// Keep only the enabled in settings providers
foreach ( $providers_list as $provider => $provider_data ) {
	if ( ! in_array( $provider, $enabled_providers ) ) {
		unset( $providers_list[ $provider ] );
	}
}

// Use the current page URL, if not set
if ( empty( $url ) ) {
	$url = wp_parse_url( home_url(), PHP_URL_SCHEME ) . '://';
	$url .= wp_parse_url( home_url(), PHP_URL_HOST );
	$url .= str_replace( '?us_iframe=1', '', $_SERVER['REQUEST_URI'] );
}

$post_thumbnail = get_the_post_thumbnail_url( NULL, 'large' );
$post_thumbnail = $post_thumbnail ?: '';

$list_atts = array(
	'class' => 'w-sharing-list',
);

// Set attribute to find an image in content
if ( ! $post_thumbnail ) {
	$list_atts['data-content-image'] = 'true';
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= '<div' . us_implode_atts( $list_atts ) . '>';

$items_html = $tooltip_items_html = '';
foreach ( $providers_list as $provider => $provider_data ) {

	// Leave only supported by AMP providers with specific semantics
	// https://amp.dev/documentation/components/amp-social-share/
	if ( us_amp() ) {
		if ( ! in_array( $provider, array( 'vk', 'xing', 'reddit' ) ) ) {
			$amp_item_atts = array(
				'type' => $provider,
				'aria-label' => $provider_data['title'],
			);

			// Add required attributes
			if ( $provider == 'pinterest' ) {
				$amp_item_atts['data-param-media'] = $post_thumbnail;
			} elseif ( $provider == 'facebook' ) {
				$amp_item_atts['data-param-app_id'] = trim( us_get_option( 'facebook_app_id', '' ) );
			}
			$items_html .= '<amp-social-share' . us_implode_atts( $amp_item_atts ) . '></amp-social-share>';
		}
		continue;
	}

	$api_url = $provider_data['url'];
	$api_url = str_replace( '{{url}}', $url, $api_url );
	$api_url = str_replace( '{{text}}', get_the_title(), $api_url );
	if ( $post_thumbnail ) {
		$api_url = str_replace( '{{image}}', $post_thumbnail, $api_url );
	}


	$item_atts = array(
		'class' => 'w-sharing-item ' . $provider,
		'href' => $api_url,
		'title' => $provider_data['title'],
		'aria-label' => $provider_data['title'],
	);

	if ( $provider != 'email' ) {
		$item_atts['onclick'] = 'window.open(this.href, "' . $provider . '", "' . $provider_data['features'] . '"); return false;';
	}

	$items_html .= '<a' . us_implode_atts( $item_atts ) . '>';

	if ( $text_selection ) {
		$api_url = $provider_data['url'];
		$api_url = str_replace( '{{url}}', $url, $api_url );
		if ( $post_thumbnail ) {
			$api_url = str_replace( '{{image}}', $post_thumbnail, $api_url );
		}
		$item_atts['href'] = '';
		$item_atts['data-url'] = $api_url;
	}

	$tooltip_items_html .= '<a ' . us_implode_atts( $item_atts ) . '>';

	if ( $provider == 'email' ) {
		$items_html .= '<i class="fas fa-envelope"></i>';
		$tooltip_items_html .= '<i class="fas fa-envelope"></i>';
	} else {
		$items_html .= '<i class="fab fa-' . $provider . '"></i>';
		$tooltip_items_html .= '<i class="fab fa-' . $provider . '"></i>';
	}

	$items_html .= '</a>';
	$tooltip_items_html .= '</a>';
}
$output .= $items_html;
$output .= '</div>';

$list_atts['data-sharing-url'] = $url;
// Add tooltip semantics, if text selection is enabled
if ( $text_selection AND ! us_amp() AND $us_sharing_index === 1 ) {
	$sharing_area = ( $text_selection_post ) ? 'post_content' : 'l-main';
	$output .= '<div class="w-sharing-tooltip active" style="display:none" data-sharing-area="' . $sharing_area . '">';
	$output .= '<div ' . us_implode_atts( $list_atts ) . '>';
	$output .= $tooltip_items_html;

	// Add "copy2clipboard" item
	$item_atts = array(
		'class' => 'w-sharing-item copy2clipboard',
		'href' => 'javascript:void(0)',
		'title' => us_translate( 'Copy' ),
		'aria-label' => us_translate( 'Copy' ),
	);
	$output .= '<a' . us_implode_atts( $item_atts ) . '>';
	$output .= '<i class="fas fa-copy"></i>'; // predefined icon
	$output .= '</a>';

	$output .= '</div></div>';
}

$output .= '</div>';

echo $output;

