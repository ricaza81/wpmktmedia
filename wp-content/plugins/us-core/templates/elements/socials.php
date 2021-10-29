<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Social Links element
 */

$_atts['class'] = 'w-socials';
$_atts['class'] .= isset( $classes ) ? $classes : '';

if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Fallback since 7.1
if ( ! empty( $color ) ) {
	$icons_color = $color;
}
if ( ! empty( $align ) ) {
	$_atts['class'] .= ' align_' . $align;
}

$_atts['class'] .= ' color_' . $icons_color;
$_atts['class'] .= ' shape_' . $shape;
if ( $shape != 'none' ) {
	$_atts['class'] .= ' style_' . $style;
	$_atts['class'] .= ' hover_' . $hover;
}

$list_inline_css = $item_inline_css = '';
if ( $us_elm_context == 'shortcode' ) {
	$list_inline_css = us_prepare_inline_css(
		array(
			'margin' => empty( $gap ) ? '' : '-' . $gap,
			'font-size' => empty( $size ) ? '' : $size, // used in Widget
		)
	);
	$item_inline_css = us_prepare_inline_css(
		array(
			'padding' => empty( $gap ) ? '' : $gap,
		)
	);
} else {
	$hide_tooltip = TRUE; // force hidding tooltip in header
}

// Output the element
$output = '<div ' . us_implode_atts( $_atts ) . '>';
$output .= '<div class="w-socials-list"' . $list_inline_css . '>';

$social_links = us_config( 'social_links' );

// Decoding items in case it is shortcode
if ( ! empty( $items ) AND ! is_array( $items ) ) {
	$items = json_decode( urldecode( $items ), TRUE );
	if ( ! is_array( $items ) ) {
		$items = array();
	}
} elseif ( empty( $items ) OR ! is_array( $items ) ) {
	$items = array();
}

foreach ( $items as $index => $item ) {
	if ( empty( $item['url'] ) ) {
		continue;
	}

	$item_custom_bg = '';
	$item_title = isset( $social_links[ $item['type'] ] ) ? $social_links[ $item['type'] ] : $item['type'];
	$link_atts = array(
		'class' => 'w-socials-item-link',
		'href' => $item['url'],
		'target' => '_blank',
		'rel' => 'noopener nofollow',
	);

	// Custom type
	if ( $item['type'] == 'custom' ) {
		$item_icon = $item['icon'];

		// Add fallback "Title" if title is not set by user
		$item_title = ! empty( $item['title'] ) ? $item['title'] : us_translate( 'Title' );

		$item_custom_bg = us_prepare_inline_css(
			array(
				'background' => us_get_color( $item['color'], /* Gradient */ TRUE ),
			)
		);

		if ( $icons_color == 'brand' AND ! empty( $item['color'] ) ) {
			$link_atts['style'] = 'color:' . us_get_color( $item['color'] );
		}

	// 500px
	} elseif ( $item['type'] == 's500px' ) {
		$item_icon = 'fab|500px';

	// Vimeo
	} elseif ( $item['type'] == 'vimeo' ) {
		$item_icon = 'fab|vimeo-v';

	// WeChat
	} elseif ( $item['type'] == 'wechat' ) {
		$item_icon = 'fab|weixin';

	// RSS
	} elseif ( $item['type'] == 'rss' ) {
		$item_icon = 'fas|rss';

	// Email
	} elseif ( $item['type'] == 'email' ) {
		if ( is_email( $link_atts['href'] ) ) {
			$link_atts['href'] = 'mailto:' . $link_atts['href'];
		}
		unset( $link_atts['target'] );
		unset( $link_atts['rel'] );
		$item_icon = 'fas|envelope';

	// Skype
	} elseif ( $item['type'] == 'skype' ) {
		if ( strpos( $link_atts['href'], ':' ) === FALSE ) {
			$link_atts['href'] = 'skype:' . $link_atts['href'];
		}
		unset( $link_atts['target'] );
		unset( $link_atts['rel'] );
		$item_icon = 'fab|' . $item['type'];

	} else {
		$item_icon = 'fab|' . $item['type'];
	}

	$link_atts['title'] = $item_title;
	$link_atts['aria-label'] = $item_title;

	$output .= '<div class="w-socials-item ' . $item['type'] . '"' . $item_inline_css . '>';

	$output .= '<a ' . us_implode_atts( $link_atts ) . '>';
	$output .= '<span class="w-socials-item-link-hover"' . $item_custom_bg . '></span>';
	$output .= us_prepare_icon_tag( $item_icon );
	$output .= '</a>';

	if ( ! $hide_tooltip ) {
		$output .= '<div class="w-socials-item-popup"><span>' . strip_tags( $item_title ) . '</span></div>';
	}
	$output .= '</div>';
}

$output .= '</div></div>';

echo $output;
