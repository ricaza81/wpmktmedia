<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Flipbox
 *
 * @var $design_css_class string Custom design css class
 */

// When rotating cubetilt in diagonal direction, we're actually doing a cube flip animation instead
if ( in_array( $direction, array( 'ne', 'se', 'sw', 'nw' ) ) ) {
	if ( $animation == 'cubetilt' ) {
		$animation = 'cubeflip';
	}
	if ( $animation == 'cubeflip' AND $link_type == 'btn' ) {
		$direction = 'n'; // disable diagonal directions, when back side has a button
	}
}

// Main element classes
$_atts['class'] = 'w-flipbox';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' animation_' . $animation;
$_atts['class'] .= ' direction_' . $direction;

// Move us_custom_* class to front and back containers
if ( ! empty( $design_css_class ) ) {
	$_atts['class'] = str_replace( ' ' . $design_css_class, '', $_atts['class'] );
} else {
	$design_css_class = '';
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}
// If we are in WPB front end editor mode, make sure the flipbox has an ID
if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() AND empty( $_atts['id'] ) ) {
	$_atts['id'] = us_uniqid();
}

// Link
$tag = 'div';
$btn_html = '';
$link_atts = us_generate_link_atts( $link );

// Check existence of Button Style, if not, set the default
$btn_styles = us_get_btn_styles();
if ( ! array_key_exists( $btn_style, $btn_styles ) ) {
	$btn_style = '1';
}

if ( ! empty( $link_atts['href'] ) ) {
	if ( $link_type == 'container' ) {
		$tag = 'a';
		$_atts = $_atts + $link_atts;
	} elseif ( $link_type == 'btn' ) {

		// Apply filters to button label
		$btn_label = us_replace_dynamic_value( $btn_label );
		$btn_label = strip_tags( $btn_label, '<br>' );
		$btn_label = wptexturize( $btn_label );

		if ( $btn_label !== '' ) {
			$link_atts['class'] = 'w-btn us-btn-style_' . $btn_style;
			if ( ! empty( $btn_size ) ) {
				$link_atts['style'] = 'font-size:' . $btn_style;
			}

			$btn_html .= '<a' . us_implode_atts( $link_atts ) . '>';
			$btn_html .= '<span>' . $btn_label . '</span>';
			$btn_html .= '</a>';
		}
	}
}

// Output the element
$output = '<' . $tag . us_implode_atts( $_atts ) . '>';

$helper_inline_css = us_prepare_inline_css(
	array(
		'transition-duration' => (float) $duration . 's',
	)
);
$output .= '<div class="w-flipbox-h easing_' . $easing . '"' . $helper_inline_css . '>';
$output .= '<div class="w-flipbox-hh">';

if ( $animation == 'cubeflip' AND in_array( $direction, array( 'ne', 'se', 'sw', 'nw' ) ) ) {
	$output .= '<div class="w-flipbox-hhh">';
}

// Front Side
$front_atts = array(
	'class' => 'w-flipbox-front ' . $design_css_class,
	'style' => '',
);
if ( us_get_color( $front_bgcolor, /* Gradient */ TRUE ) ) {
	$front_atts['style'] .= 'background:' . us_get_color( $front_bgcolor, /* Gradient */ TRUE ) . ';';
}
if ( us_get_color( $front_textcolor ) ) {
	$front_atts['style'] .= 'color:'. us_get_color( $front_textcolor ) . ';';
}
if ( $front_bgimage_url = wp_get_attachment_image_url( $front_bgimage, $front_bgimage_size ) ) {
	$front_atts['style'] .= 'background-image:url(' . $front_bgimage_url . ');';
}

$output .= '<div' . us_implode_atts( $front_atts ) . '>';
$output .= '<div class="w-flipbox-front-h">';
$output_front_icon = '';
if ( $front_icon_type == 'font' ) {
	$icon_inline_css = array(
		'font-size' => $front_icon_size,
		'background' => us_get_color( $front_icon_bgcolor, /* Gradient */ TRUE ),
		'color' => us_get_color( $front_icon_color ),
	);
	$output_front_icon .= '<div class="w-flipbox-front-icon style_' . $front_icon_style . '"' . us_prepare_inline_css( $icon_inline_css ) . '>';
	$output_front_icon .= us_prepare_icon_tag( $front_icon_name );
	$output_front_icon .= '</div>';
} elseif ( $front_icon_type == 'image' ) {
	$icon_inline_css = array(
		'width' => $front_icon_image_width,
	);

	$output_front_icon .= '<div class="w-flipbox-front-icon type_image"' . us_prepare_inline_css( $icon_inline_css ) . '>';
	$front_icon_image_html = wp_get_attachment_image( $front_icon_image, 'medium' );
	if ( empty( $front_icon_image_html ) ) {
		$front_icon_image_html = us_get_img_placeholder( 'medium' );
	}
	$output_front_icon .= $front_icon_image_html;
	$output_front_icon .= '</div>';
}
$output_front_title = '';
if ( ! empty( $front_title ) ) {

	// Apply filters to title
	$front_title = us_replace_dynamic_value( $front_title );
	$front_title = strip_tags( $front_title, '<br>' );
	$front_title = wptexturize( $front_title );

	$output_front_title .= '<' . $front_title_tag . ' class="w-flipbox-front-title"';
	$output_front_title .= us_prepare_inline_css(
		array(
			'font-size' => $front_title_size,
		)
	);
	$output_front_title .= '>' . $front_title . '</' . $front_title_tag . '>';
}
$output_front_desc = '';
if ( ! empty( $front_desc ) ) {
	$front_desc = us_replace_dynamic_value( $front_desc );
	$output_front_desc .= '<div class="w-flipbox-front-desc">' . wpautop( $front_desc ) . '</div>';
}
if ( $front_icon_pos == 'below_title' ) {
	$output .= $output_front_title . $output_front_icon . $output_front_desc;
} elseif ( $front_icon_pos == 'below_desc' ) {
	$output .= $output_front_title . $output_front_desc . $output_front_icon;
} else/*if ( $front_icon_pos == 'above_title' )*/ {
	$output .= $output_front_icon . $output_front_title . $output_front_desc;
}
$output .= '</div></div>';

// Don't output backside and animation on AMP
if ( ! us_amp() ) {

	// Back Side
	$back_atts = array(
		'class' => 'w-flipbox-back ' . $design_css_class,
		'style' => 'display:none;',
	);
	if ( us_get_color( $back_bgcolor, /* Gradient */ TRUE ) ) {
		$back_atts['style'] .= 'background:' . us_get_color( $back_bgcolor, /* Gradient */ TRUE ) . ';';
	}
	if ( us_get_color( $back_textcolor ) ) {
		$back_atts['style'] .= 'color:'. us_get_color( $back_textcolor ) . ';';
	}
	if ( $back_bgimage_url = wp_get_attachment_image_url( $back_bgimage, $back_bgimage_size ) ) {
		$back_atts['style'] .= 'background-image:url(' . $back_bgimage_url . ');';
	}

	$output .= '<div' . us_implode_atts( $back_atts ) . '>';
	$output .= '<div class="w-flipbox-back-h">';
	if ( ! empty( $back_title ) ) {

		// Apply filters to title
		$back_title = us_replace_dynamic_value( $back_title );
		$back_title = strip_tags( $back_title, '<br>' );
		$back_title = wptexturize( $back_title );

		$output .= '<' . $back_title_tag . ' class="w-flipbox-back-title"';
		$output .= us_prepare_inline_css(
			array(
				'font-size' => $back_title_size,
			)
		);
		$output .= '>' . $back_title . '</' . $back_title_tag . '>';
	}
	if ( ! empty( $back_desc ) ) {
		$back_desc = us_replace_dynamic_value( $back_desc );
		$output .= '<div class="w-flipbox-back-desc">' . wpautop( $back_desc ) . '</div>';
	}
	$output .= $btn_html;
	$output .= '</div></div>';

	// We need additional dom-elements for 'cubeflip' animations (:before / :after won't suit)
	if ( $animation == 'cubeflip' ) {

		$front_bgcolor = ( ! empty( $front_bgcolor ) )
			? us_get_color( $front_bgcolor, /* Gradient */ FALSE, FALSE )
			: us_get_color( '_content_bg_alt', /* Gradient */ FALSE, FALSE );

		// Top & bottom flank with shaded color
		if ( in_array( $direction, array( 'ne', 'e', 'se', 'sw', 'w', 'nw' ) ) ) {
			$shaded_color = us_shade_color( $front_bgcolor );
			$output .= '<div class="w-flipbox-yflank"' . us_prepare_inline_css(
					array(
						'display' => 'none',
						'background' => $shaded_color,
					)
				) . '></div>';
		}

		// Left & right flank with shaded color
		if ( in_array( $direction, array( 'n', 'ne', 'se', 's', 'sw', 'nw' ) ) ) {
			$shaded_color = us_shade_color( $front_bgcolor, 0.1 );
			$output .= '<div class="w-flipbox-xflank"' . us_prepare_inline_css(
					array(
						'display' => 'none',
						'background' => $shaded_color,
					)
				) . '></div>';
		}
	}

	if ( $animation == 'cubeflip' AND in_array( $direction, array( 'ne', 'se', 'sw', 'nw' ) ) ) {
		$output .= '</div>';
	}
}

$output .= '</div></div>';
$output .= '</' . $tag . '>';

// If we are in WPB front end editor mode, apply JS to the flipbox
if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) {
	$output .= '<script>
	jQuery( function( $ ) {
		if ( typeof $us !== "undefined" && typeof $.fn.wFlipBox === "function" ) {
			var $elm = jQuery( "#' . $_atts['id'] . '" );
			if ( $elm.data( "wFlipBox" ) === undefined ) {
				$elm.wFlipBox();
			}
		}
	} );
	</script>';
}

echo $output;
