<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Popup
 */

$_atts['class'] = 'w-popup';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' align_' . $align;

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Generate ID needed for AMP <lightbox>
if ( us_amp() ) {
	$_amp_ID = 'w-popup-' . ( empty( $el_id ) ? mt_rand( 1, 9999 ) : $el_id );
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';

// Trigger link
if ( us_amp() ) {
	$trigger_link['on'] = 'tap:' . $_amp_ID . '.open,' . $_amp_ID . '.toggleClass(class=\'opened\')';
} else {
	$trigger_link['href'] = 'javascript:void(0)';
}

if ( $show_on == 'image' ) {
	$image_html = wp_get_attachment_image( $image, $image_size );
	if ( empty( $image_html ) ) {
		$image_html = us_get_img_placeholder( $image_size );
	}
	$image_atts = array(
		'class' => 'w-popup-trigger type_image',
		'aria-label' => __( 'Popup', 'us' ),
	);
	$output .= '<a' . us_implode_atts( $image_atts + $trigger_link ) . '>' . $image_html . '</a>';

} elseif ( $show_on == 'load' ) {
	$output .= '<span class="w-popup-trigger type_load" data-delay="' . (int) $show_delay . '"></span>';

} elseif ( $show_on == 'selector' ) {
	$output .= '<span class="w-popup-trigger type_selector" data-selector="' . esc_attr( $trigger_selector ) . '"></span>';

} elseif ( $show_on == 'icon' ) {
	$icon_atts = array(
		'class' => 'w-popup-trigger type_icon',
		'aria-label' => __( 'Popup', 'us' ),
	);
	$output .= '<a' . us_implode_atts( $icon_atts + $trigger_link ) . '>' . us_prepare_icon_tag( $btn_icon ) . '</a>';

} else/*if ( $show_on == 'btn' )*/ {

	// Check existence of Button Style, if not, set the default
	$btn_styles = us_get_btn_styles();
	if ( ! array_key_exists( $btn_style, $btn_styles ) ) {
		$btn_style = '1';
	}

	$btn_atts = array(
		'class' => 'w-popup-trigger type_btn w-btn us-btn-style_' . $btn_style,
	);

	if ( ! empty( $btn_size ) ) {
		$btn_atts['style'] = 'font-size:' . $btn_size;
	}

	// Icon
	$icon_html = '';
	if ( ! empty( $btn_icon ) ) {
		$icon_html = us_prepare_icon_tag( $btn_icon );
		$btn_atts['class'] .= ' icon_at' . $btn_iconpos;
	}

	$output .= '<div class="w-btn-wrapper">';
	$output .= '<a' . us_implode_atts( $btn_atts + $trigger_link ) . '>';
	if ( is_rtl() ) {
		$btn_iconpos = ( $btn_iconpos == 'left' ) ? 'right' : 'left';
	}
	if ( $btn_iconpos == 'left' ) {
		$output .= $icon_html;
	}
	$output .= '<span class="w-btn-label">' . trim( strip_tags( $btn_label, '<br>' ) ) . '</span>';
	if ( $btn_iconpos == 'right' ) {
		$output .= $icon_html;
	}
	$output .= '</a>';
	$output .= '</div>';
}

// Add AMP specific lightbox semantics
if ( us_amp() ) {
	$output .= '<amp-lightbox id="' . $_amp_ID . '" layout="nodisplay" on="tap:' . $_amp_ID . '.toggleClass(class=\'opened\'),' . $_amp_ID . '.close">';
}

// Overlay
$output .= '<div class="w-popup-overlay"';
$output .= us_prepare_inline_css(
	array(
		'background' => us_get_color( $overlay_bgcolor, /* Gradient */ TRUE ),
	)
);
$output .= '></div>';

$popup_classes = us_amp() ? '' : ' animation_' . $animation;

// Popup title
$output_title = '';
if ( $use_page_block === 'none' AND ! empty( $title ) ) {
	$popup_classes .= ' with_title';

	$output_title .= '<div class="w-popup-box-title"';
	$output_title .= us_prepare_inline_css(
		array(
			'color' => us_get_color( $title_textcolor ),
			'background' => us_get_color( $title_bgcolor, /* Gradient */ TRUE ),
		)
	);
	$output_title .= '>' . esc_html( $title ) . '</div>';
} else {
	$popup_classes .= ' without_title';
}

// The Popup itself
$output .= '<div class="w-popup-wrap">';
$output .= '<div class="w-popup-box' . $popup_classes . '"';
$output .= us_prepare_inline_css(
	array(
		'border-radius' => $popup_border_radius,
		'width' => $popup_width,
	)
);
$output .= '><div class="w-popup-box-h">';
$output .= $output_title;

// Popup content
$output .= '<div class="w-popup-box-content"';
$output .= us_prepare_inline_css(
	array(
		'padding' => $popup_padding,
		'background' => us_get_color( $content_bgcolor, /* Gradient */ TRUE ),
		'color' => us_get_color( $content_textcolor ),
	)
);
$output .= '>';

if ( $use_page_block === 'none' ) {
	$output .= do_shortcode( wpautop( $content ) );
} else {
	$output .= do_shortcode( '[us_page_block id="' . $use_page_block . '"]' );
}

$output .= '</div>'; // .w-popup-box-content
$output .= '</div></div>'; // .w-popup-box

// Popup closer
$output .= '<div class="w-popup-closer"';
$output .= us_prepare_inline_css(
	array(
		'background' => us_get_color( $content_bgcolor, /* Gradient */ TRUE ),
		'color' => us_get_color( $content_textcolor ),
	)
);
$output .= '></div>';
$output .= '</div>'; // .w-popup-wrap

if ( us_amp() ) {
	$output .= '</amp-lightbox>';
}

$output .= '</div>'; // .w-popup

echo $output;
