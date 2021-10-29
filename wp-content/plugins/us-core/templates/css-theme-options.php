<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Generates and outputs theme options' generated styleshets
 *
 * @action Before the template: us_before_template:templates/css-theme-options
 * @action After the template: us_after_template:templates/css-theme-options
 */

global $us_template_directory_uri;

// Define if supported plugins are enabled
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

$with_shop = class_exists( 'woocommerce' );
$with_events = function_exists( 'tribe_get_option' );
$with_forums = class_exists( 'bbPress' );
$with_gforms = class_exists( 'GFForms' );

// Add filter to remove protocols from URLs for better compatibility with caching plugins and services
if ( ! us_get_option( 'keep_url_protocol', 1 ) ) {
	add_filter( 'clean_url', 'us_remove_url_protocol', 10 );
}

// Helper function to determine if CSS asset is used
if ( ! function_exists( 'us_is_asset_used' ) ) {
	function us_is_asset_used( $asset_name ) {
		$_assets = us_get_option( 'assets' );
		if (
			us_get_option( 'optimize_assets', FALSE )
			AND isset( $_assets[ $asset_name ] )
			AND $_assets[ $asset_name ] == 0
		) {
			return FALSE;
		}
		return TRUE;
	}
}

// Output styles for fallback icons
if ( us_get_option( 'optimize_assets', FALSE ) ) {
?>
.fa-angle-down:before { content: "\f107" }
.fa-angle-left:before { content: "\f104" }
.fa-angle-right:before { content: "\f105" }
.fa-angle-up:before { content: "\f106" }
.fa-bars:before { content: "\f0c9" }
.fa-check:before { content: "\f00c" }
.fa-comments:before { content: "\f086" }
.fa-copy:before { content: "\f0c5" }
.fa-envelope:before { content: "\f0e0" }
.fa-map-marker-alt:before { content: "\f3c5" }
.fa-mobile:before { content: "\f10b" }
.fa-phone:before { content: "\f095" }
.fa-play:before { content: "\f04b" }
.fa-quote-left:before { content: "\f10d" }
.fa-search-plus:before { content: "\f00e" }
.fa-search:before { content: "\f002" }
.fa-shopping-cart:before { content: "\f07a" }
.fa-star:before { content: "\f005" }
.fa-tags:before { content: "\f02c" }
.fa-times:before { content: "\f00d" }
<?php } ?>

.style_phone6-1 > * {
	background-image: url(<?= esc_url( $us_template_directory_uri ) ?>/img/phone-6-black-real.png);
	}
.style_phone6-2 > * {
	background-image: url(<?= esc_url( $us_template_directory_uri ) ?>/img/phone-6-white-real.png);
	}
.style_phone6-3 > * {
	background-image: url(<?= esc_url( $us_template_directory_uri ) ?>/img/phone-6-black-flat.png);
	}
.style_phone6-4 > * {
	background-image: url(<?= esc_url( $us_template_directory_uri ) ?>/img/phone-6-white-flat.png);
	}

<?php if ( us_is_asset_used( 'lmaps' ) ) { ?>
/* Default icon Leaflet URLs */
.leaflet-default-icon-path {
	background-image: url(<?= esc_url( $us_template_directory_uri ) ?>/common/css/vendor/images/marker-icon.png);
	}
<?php } ?>



/* Typography
  =============================================================================================================================== */
<?php

// Global Text
$css = 'html, .l-header .widget, .menu-item-object-us_page_block {';
$css .= us_get_font_css( 'body', FALSE, TRUE );
$css .= 'font-size:' . us_get_option( 'body_fontsize' ) . ';';
$css .= 'line-height:' . us_get_option( 'body_lineheight' ) . ';';
$css .= '}';

// Uploaded Fonts
$uploaded_fonts = us_get_option( 'uploaded_fonts', array() );
if ( is_array( $uploaded_fonts ) AND count( $uploaded_fonts ) > 0 ) {
	foreach ( $uploaded_fonts as $uploaded_font ) {
		$files = explode( ',', $uploaded_font['files'] );
		$urls = array();
		foreach ( $files as $file ) {
			if ( $url = wp_get_attachment_url( $file ) ) {
				$urls[] = 'url(' . esc_url( $url ) . ') format("' . pathinfo( $url, PATHINFO_EXTENSION ) . '")';
			}
		}
		if ( count( $urls ) ) {
			$css .= '@font-face {';
			$css .= 'font-display: ' . us_get_option( 'font_display', 'swap' ) . ';';
			$css .= 'font-style: ' . ( ! empty( $uploaded_font['italic'] ) ? 'italic' : 'normal' ) . ';';
			$css .= 'font-family:"' . strip_tags( $uploaded_font['name'] ) . '";';
			$css .= 'font-weight:' . $uploaded_font['weight'] . ';';
			$css .= 'src:' . implode( ', ', $urls ) . ';';
			$css .= '}';
		}
	}
}

// Headings h1-h6
for ( $i = 1; $i <= 6; $i ++ ) {
	if ( $i == 4 ) { // set to some elements styles as <h4>
		if ( $with_shop ) {
			$css .= '.woocommerce-Reviews-title,';
		}
		$css .= '.widgettitle, .comment-reply-title, h' . $i . '{';
	} else {
		$css .= 'h' . $i . '{';
	}
	$css .= us_get_font_css( 'h' . $i );
	$css .= 'font-weight:' . us_get_option( 'h' . $i . '_fontweight' ) . ';';
	$css .= 'font-size:' . us_get_option( 'h' . $i . '_fontsize' ) . ';';
	$css .= 'line-height:' . us_get_option( 'h' . $i . '_lineheight' ) . ';';
	$css .= 'letter-spacing:' . us_get_option( 'h' . $i . '_letterspacing' ) . ';';
	$css .= 'margin-bottom:' . us_get_option( 'h' . $i . '_bottom_indent' ) . ';';
	if ( is_array( us_get_option( 'h' . $i . '_transform' ) ) ) {
		if ( in_array( 'italic', us_get_option( 'h' . $i . '_transform' ) ) ) {
			$css .= 'font-style: italic;';
		}
		if ( in_array( 'uppercase', us_get_option( 'h' . $i . '_transform' ) ) ) {
			$css .= 'text-transform: uppercase;';
		}
	}
	$css .= '}';
}

echo strip_tags( $css );
?>
@media (max-width: 767px) {
html {
	font-size: <?= us_get_option( 'body_fontsize_mobile' ) ?>;
	line-height: <?= us_get_option( 'body_lineheight_mobile' ) ?>;
	}
h1 {
	font-size: <?= us_get_option( 'h1_fontsize_mobile' ) ?>;
	}
h1.vc_custom_heading:not([class*="us_custom_"]) {
	font-size: <?= us_get_option( 'h1_fontsize_mobile' ) ?> !important;
	}
h2 {
	font-size: <?= us_get_option( 'h2_fontsize_mobile' ) ?>;
	}
h2.vc_custom_heading:not([class*="us_custom_"]) {
	font-size: <?= us_get_option( 'h2_fontsize_mobile' ) ?> !important;
	}
h3 {
	font-size: <?= us_get_option( 'h3_fontsize_mobile' ) ?>;
	}
h3.vc_custom_heading:not([class*="us_custom_"]) {
	font-size: <?= us_get_option( 'h3_fontsize_mobile' ) ?> !important;
	}
h4,
<?php if ( $with_shop ) { ?>
.woocommerce-Reviews-title,
<?php } ?>
.widgettitle,
.comment-reply-title {
	font-size: <?= us_get_option( 'h4_fontsize_mobile' ) ?>;
	}
h4.vc_custom_heading:not([class*="us_custom_"]) {
	font-size: <?= us_get_option( 'h4_fontsize_mobile' ) ?> !important;
	}
h5 {
	font-size: <?= us_get_option( 'h5_fontsize_mobile' ) ?>;
	}
h5.vc_custom_heading:not([class*="us_custom_"]) {
	font-size: <?= us_get_option( 'h5_fontsize_mobile' ) ?> !important;
	}
h6 {
	font-size: <?= us_get_option( 'h6_fontsize_mobile' ) ?>;
	}
h6.vc_custom_heading:not([class*="us_custom_"]) {
	font-size: <?= us_get_option( 'h6_fontsize_mobile' ) ?> !important;
	}
}



/* Site Layout
  =============================================================================================================================== */
body { background:
<?php
$background_image = '';
$background_color = us_get_color( 'color_body_bg', /* Gradient */ TRUE );

// Add image properties when image is set
if ( $body_bg_image = us_get_option( 'body_bg_image', '' ) ) {
	$img_arr = explode( '|', $body_bg_image );
	$body_bg_image_url = wp_get_attachment_image_url( $img_arr[0], 'full' );

	$background_image .= 'url(' . $body_bg_image_url . ') ';
	$background_image .= us_get_option( 'body_bg_image_position' );
	if ( us_get_option( 'body_bg_image_size' ) != 'initial' ) {
		$background_image .= '/' . us_get_option( 'body_bg_image_size' );
	}
	$background_image .= ' ';
	$background_image .= us_get_option( 'body_bg_image_repeat' );
	if ( ! us_get_option( 'body_bg_image_attachment', 0 ) ) {
		$background_image .= ' fixed';
	}
	// If the color value contains gradient, add comma for correct appearance
	if ( strpos( $background_color, 'gradient' ) !== FALSE ) {
		$background_image .= ',';
	}
}
// Output single combined background value
echo esc_attr( $background_image . ' ' . $background_color );
?>
}
.l-canvas.type_boxed,
.l-canvas.type_boxed .l-subheader,
.l-canvas.type_boxed ~ .l-footer {
	max-width: <?= us_get_option( 'site_canvas_width' ) ?>;
	}
.l-subheader-h,
.l-section-h,
.l-main .aligncenter,
.w-tabs-section-content-h {
	max-width: <?= us_get_option( 'site_content_width' ) ?>;
	}
.post-password-form {
	max-width: calc(<?= us_get_option( 'site_content_width' ) ?> + 5rem);
	}

/* Limit width for centered images */
@media screen and (max-width: <?= ( intval( us_get_option( 'site_content_width' ) ) + intval( us_get_option( 'body_fontsize' ) ) * 5 ) ?>px) {
.l-main .aligncenter {
	max-width: calc(100vw - 5rem);
	}
}

<?php if ( us_get_option( 'row_height' ) == 'custom' ) { ?>
.l-section.height_custom {
	padding-top: <?= us_get_option( 'row_height_custom' ) ?>;
	padding-bottom: <?= us_get_option( 'row_height_custom' ) ?>;
	}
<?php } ?>

/* Full width for Gutenberg blocks */
<?php if ( ! us_get_option( 'disable_block_editor_assets', 0 ) ) { ?>
@media screen and (min-width: <?= ( intval( us_get_option( 'site_content_width' ) ) + intval( us_get_option( 'body_fontsize' ) ) * 5 ) ?>px) {
.l-main .alignfull {
	margin-left: calc(<?= intval( us_get_option( 'site_content_width' ) ) / 2 ?>px - 50vw);
	margin-right: calc(<?= intval( us_get_option( 'site_content_width' ) ) / 2 ?>px - 50vw);
	}
}
<?php } ?>

<?php if ( floatval( us_get_option( 'text_bottom_indent' ) ) != 0 ) { ?>
/* Text Block bottom indent */
.wpb_text_column:not(:last-child) {
	margin-bottom: <?= us_get_option( 'text_bottom_indent' ) ?>;
	}
<?php } ?>

<?php if ( us_get_option( 'enable_sidebar_titlebar', 0 ) ) { ?>
.g-cols .l-sidebar {
	width: <?= us_get_option( 'sidebar_width' ) ?>;
	}
.g-cols .l-content {
	width: <?= 100 - floatval( us_get_option( 'sidebar_width' ) ) ?>%;
	}
<?php } ?>

/* Footer Reveal Effect */
<?php if ( us_get_option( 'footer_reveal', 0 ) ) { ?>
@media (min-width: <?= us_get_option( 'columns_stacking_width' ) ?>) {
body.footer_reveal .l-canvas {
	position: relative;
	z-index: 1;
	}
body.footer_reveal .l-footer {
	position: fixed;
	bottom: 0;
	}
body.footer_reveal .l-canvas.type_boxed ~ .l-footer {
	left: 0;
	right: 0;
	}
}
<?php } ?>

/* Columns width regarding columns_stacking_width */
<?php
if ( us_is_asset_used( 'columns' ) ) { ?>

	@media (max-width: <?= ( intval( us_get_option( 'columns_stacking_width' ) ) - 1 ) ?>px) {
	.l-canvas {
		overflow: hidden;
		}
	.g-cols.reversed {
		flex-direction: column-reverse;
		}
	.g-cols > div:not([class*=" vc_col-"]) {
		width: 100%;
		margin: 0 0 1.5rem;
		}
	.g-cols.type_boxes > div,
	.g-cols.reversed > div:first-child,
	.g-cols:not(.reversed) > div:last-child,
	.g-cols > div.has-fill {
		margin-bottom: 0;
		}
	.g-cols.type_default > .wpb_column.stretched {
		margin-left: -1rem;
		margin-right: -1rem;
		width: auto;
		}
	.g-cols.type_boxes > .wpb_column.stretched {
		margin-left: -2.5rem;
		margin-right: -2.5rem;
		width: auto;
		}

	.align_center_xs,
	.align_center_xs .w-socials {
		text-align: center;
		}
	.align_center_xs .w-hwrapper > * {
		margin: 0.5rem 0;
		width: 100%;
		}
	}

	@media (min-width: <?= us_get_option( 'columns_stacking_width' ) ?>) {
	.l-section.for_sidebar.at_left > div > .g-cols {
		flex-direction: row-reverse;
		}
	.vc_column-inner.type_sticky > .wpb_wrapper {
		position: -webkit-sticky;
		position: sticky;
		}
	}

	@media screen and (min-width: <?= ( intval( us_get_option( 'site_content_width' ) ) + intval( us_get_option( 'body_fontsize' ) ) * 5 ) ?>px) {
	.g-cols.type_default > .wpb_column.stretched:first-of-type {
		margin-left: calc(<?= intval( us_get_option( 'site_content_width' ) ) / 2 ?>px + 1.5rem - 50vw);
		}
	.g-cols.type_default > .wpb_column.stretched:last-of-type {
		margin-right: calc(<?= intval( us_get_option( 'site_content_width' ) ) / 2 ?>px + 1.5rem - 50vw);
		}
	.g-cols.type_boxes > .wpb_column.stretched:first-of-type {
		margin-left: calc(<?= intval( us_get_option( 'site_content_width' ) ) / 2 ?>px - 50vw);
		}
	.g-cols.type_boxes > .wpb_column.stretched:last-of-type {
		margin-right: calc(<?= intval( us_get_option( 'site_content_width' ) ) / 2 ?>px - 50vw);
		}
	}
<?php
}



/* Buttons Styles
  =============================================================================================================================== */
$btn_styles = us_get_option( 'buttons' );

// Default Shadow colors
if ( ! isset( $btn_styles[0]['color_shadow'] ) OR empty( $btn_styles[0]['color_shadow'] ) ) {
	$btn_styles[0]['color_shadow'] = 'rgba(0,0,0,0.2)';
}
if ( ! isset( $btn_styles[0]['color_shadow_hover'] ) OR empty( $btn_styles[0]['color_shadow_hover'] ) ) {
	$btn_styles[0]['color_shadow_hover'] = 'rgba(0,0,0,0.2)';
}

// Set Default Style for non-editable button elements
$buttons_css = 'button[type="submit"]:not(.w-btn),';
$buttons_css .= 'input[type="submit"] {';
if ( $btn_styles[0]['font'] != 'body' ) {
	$buttons_css .= us_get_font_css( $btn_styles[0]['font'] );
}
if ( isset( $btn_styles[0]['font_size'] ) ) {
	$buttons_css .= 'font-size:' . $btn_styles[0]['font_size'] . ';';
}
if ( isset( $btn_styles[0]['line_height'] ) ) {
	$buttons_css .= 'line-height:' . $btn_styles[0]['line_height'] . '!important;';
}
$buttons_css .= 'font-weight:' . $btn_styles[0]['font_weight'] . ';';
$buttons_css .= 'font-style:' . ( in_array( 'italic', $btn_styles[0]['text_style'] ) ? 'italic' : 'normal' ) . ';';
$buttons_css .= 'text-transform:' . ( in_array( 'uppercase', $btn_styles[0]['text_style'] ) ? 'uppercase' : 'none' ) . ';';
$buttons_css .= 'letter-spacing:' . $btn_styles[0]['letter_spacing'] . ';';
$buttons_css .= 'border-radius:' . $btn_styles[0]['border_radius'] . ';';
$buttons_css .= 'padding:' . $btn_styles[0]['height'] . ' ' . $btn_styles[0]['width'] . ';';
$buttons_css .= 'box-shadow: 0 ' . floatval( $btn_styles[0]['shadow'] ) / 2 . 'em ' . $btn_styles[0]['shadow'] . ' ' . us_get_color( $btn_styles[0]['color_shadow'] ) . ';';
$buttons_css .= 'background:' . (
	! empty( $btn_styles[0]['color_bg'] )
		? us_get_color( $btn_styles[0]['color_bg'], /* Gradient */ TRUE )
		: 'transparent'
	) . ';';
$buttons_css .= 'border-color:' . (
	! empty( $btn_styles[0]['color_border'] )
		? us_get_color( $btn_styles[0]['color_border'] )
		: 'transparent'
	) . ';';
$buttons_css .= 'color:' . (
	! empty( $btn_styles[0]['color_text'] )
		? us_get_color( $btn_styles[0]['color_text'] )
		: 'inherit'
	) . '!important;';
$buttons_css .= '}';

// Border
$buttons_css .= 'button[type="submit"]:not(.w-btn):before,';
$buttons_css .= 'input[type="submit"] {';
$buttons_css .= 'border-width:' . $btn_styles[0]['border_width'] . ';';
$buttons_css .= '}';

// Hover State
$buttons_css .= '.no-touch button[type="submit"]:not(.w-btn):hover,';
$buttons_css .= '.no-touch input[type="submit"]:hover {';
$buttons_css .= 'box-shadow: 0 ' . floatval( $btn_styles[0]['shadow_hover'] ) / 2 . 'em ' . $btn_styles[0]['shadow_hover'] . ' ' . us_get_color( $btn_styles[0]['color_shadow_hover'] ) . ';';
$buttons_css .= 'background:' . (
	! empty( $btn_styles[0]['color_bg_hover'] )
		? us_get_color( $btn_styles[0]['color_bg_hover'], /* Gradient */ TRUE )
		: 'transparent'
	) . ';';
$buttons_css .= 'border-color:' . (
	! empty( $btn_styles[0]['color_border_hover'] )
		? us_get_color( $btn_styles[0]['color_border_hover'] )
		: 'transparent'
	) . ';';
$buttons_css .= 'color:' . (
	! empty( $btn_styles[0]['color_text_hover'] )
		? us_get_color( $btn_styles[0]['color_text_hover'] )
		: 'inherit'
	) . '!important;';
$buttons_css .= '}';

// Remove transition if the default button background has a gradient (cause gradients don't support transition)
if ( strpos( $btn_styles[0]['color_bg'], 'gradient' ) !== FALSE OR strpos( $btn_styles[0]['color_bg_hover'], 'gradient' ) !== FALSE ) {
	$buttons_css .= 'button[type="submit"], input[type="submit"] { transition: none; }';
}

// Generate Buttons Styles
foreach ( $btn_styles as $btn_style ) {

	// Default Shadow colors
	if ( ! isset( $btn_style['color_shadow'] ) OR empty( $btn_style['color_shadow'] ) ) {
		$btn_style['color_shadow'] = 'rgba(0,0,0,0.2)';
	}
	if ( ! isset( $btn_style['color_shadow_hover'] ) OR empty( $btn_style['color_shadow_hover'] ) ) {
		$btn_style['color_shadow_hover'] = 'rgba(0,0,0,0.2)';
	}

	// Default State
	if ( $with_shop AND us_get_option( 'shop_secondary_btn_style' ) == $btn_style['id'] ) {
		$buttons_css .= '.woocommerce .button, .woocommerce .actions .button,';
	}
	if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
		$buttons_css .= '.woocommerce .button.alt, .woocommerce .button.checkout, .woocommerce .button.add_to_cart_button,';
	}
	$buttons_css .= '.us-nav-style_' . $btn_style['id'] . ' > *,';
	$buttons_css .= '.navstyle_' . $btn_style['id'] . ' > .owl-nav div,';
	$buttons_css .= '.us-btn-style_' . $btn_style['id'] . '{';
	$buttons_css .= us_get_font_css( $btn_style['font'] );
	if ( isset( $btn_style['font_size'] ) ) {
		$buttons_css .= 'font-size:' . $btn_style['font_size'] . ';';
	}
	if ( isset( $btn_style['line_height'] ) ) {
		$buttons_css .= 'line-height:' . $btn_style['line_height'] . '!important;';
	}
	$buttons_css .= 'font-weight:' . $btn_style['font_weight'] . ';';
	$buttons_css .= 'font-style:' . ( in_array( 'italic', $btn_style['text_style'] ) ? 'italic' : 'normal' ) . ';';
	$buttons_css .= 'text-transform:' . ( in_array( 'uppercase', $btn_style['text_style'] ) ? 'uppercase' : 'none' ) . ';';
	$buttons_css .= 'letter-spacing:' . $btn_style['letter_spacing'] . ';';
	$buttons_css .= 'border-radius:' . $btn_style['border_radius'] . ';';
	$buttons_css .= 'padding:' . $btn_style['height'] . ' ' . $btn_style['width'] . ';';
	$buttons_css .= 'background:' . ( ! empty( us_get_color( $btn_style['color_bg'], /* Gradient */ TRUE ) )
		? us_get_color( $btn_style['color_bg'], /* Gradient */ TRUE )
		: 'transparent' ) . ';';
	if ( ! empty( $btn_style['color_border'] ) ) {
		$border_color = us_get_color( $btn_style['color_border'], /* Gradient*/ TRUE );
		if ( strpos( $border_color, 'gradient' ) !== FALSE ) {
			$buttons_css .= 'border-image:' . $border_color . ' 1;';
		} else {
			$buttons_css .= 'border-color:' . $border_color . ';';
		}
	} else {
		$buttons_css .= 'border-color: transparent;';
	}
	if ( ! empty( $btn_style['color_text'] ) ) {
		$buttons_css .= 'color:' . us_get_color( $btn_style['color_text'] ) . '!important;';
	}
	if ( ! empty( $btn_style['shadow'] ) ) {
		$buttons_css .= 'box-shadow: 0 ' . floatval( $btn_style['shadow'] ) / 2 . 'em ' . $btn_style['shadow'] . ' ' . us_get_color( $btn_style['color_shadow'] ) . ';';
	} else {
		$buttons_css .= 'box-shadow: none';
	}
	$buttons_css .= '}';

	// Border imitation
	if ( $with_shop AND us_get_option( 'shop_secondary_btn_style' ) == $btn_style['id'] ) {
		$buttons_css .= '.woocommerce .button:before, .woocommerce .actions .button:before,';
	}
	if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
		$buttons_css .= '.woocommerce .button.alt:before, .woocommerce .button.checkout:before, .woocommerce .button.add_to_cart_button:before,';
	}
	$buttons_css .= '.us-nav-style_' . $btn_style['id'] . ' > *:before,';
	$buttons_css .= '.navstyle_' . $btn_style['id'] . ' > .owl-nav div:before,';
	$buttons_css .= '.us-btn-style_' . $btn_style['id'] . ':before {';
	$buttons_css .= 'border-width:' . $btn_style['border_width'] . ';';
	$buttons_css .= '}';

	// Hover State
	if ( $with_shop AND us_get_option( 'shop_secondary_btn_style' ) == $btn_style['id'] ) {
		$buttons_css .= '.no-touch .woocommerce .button:hover, .no-touch .woocommerce .actions .button:hover,';
	}
	if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
		$buttons_css .= '.no-touch .woocommerce .button.alt:hover, .no-touch .woocommerce .button.checkout:hover, .no-touch .woocommerce .button.add_to_cart_button:hover,';
	}
	$buttons_css .= '.us-nav-style_' . $btn_style['id'] . ' > span.current,';
	$buttons_css .= '.no-touch .us-nav-style_' . $btn_style['id'] . ' > a:hover,';
	$buttons_css .= '.no-touch .navstyle_' . $btn_style['id'] . ' > .owl-nav div:hover,';
	$buttons_css .= '.no-touch .us-btn-style_' . $btn_style['id'] . ':hover {';

	$buttons_css .= 'box-shadow: 0 ' . floatval( $btn_style['shadow_hover'] ) / 2 . 'em ' . $btn_style['shadow_hover'] . ' ' . us_get_color( $btn_style['color_shadow_hover'] ) . ';';
	$buttons_css .= 'background:' . (
		! empty( $btn_style['color_bg_hover'] )
			? us_get_color( $btn_style['color_bg_hover'], /* Gradient */ TRUE )
			: 'transparent'
		) . ';';
	if ( ! empty( $btn_style['color_border_hover'] ) ) {
		$border_color = us_get_color( $btn_style['color_border_hover'], /* Gradient */ TRUE );
		if ( strpos( $border_color, 'gradient' ) !== FALSE ) {
			$buttons_css .= 'border-image:' . $border_color . ' 1;';
		} else {
			$buttons_css .= 'border-color:' . $border_color . ';';
		}
	} else {
		$buttons_css .= 'border-color: transparent;';
	}
	if ( ! empty( $btn_style['color_text_hover'] ) ) {
		$buttons_css .= 'color:' . us_get_color( $btn_style['color_text_hover'] ) . '!important;';
	}
	$buttons_css .= '}';

	// Add min-width for Pagination to make correct circles or squares
	if ( isset( $btn_style['line_height'] ) ) {
		$btn_line_height = strpos( $btn_style['line_height'], 'px' ) !== FALSE ? $btn_style['line_height'] : $btn_style['line_height'] . 'em';
	} else {
		$btn_line_height = '1.2em';
	}
	$buttons_css .= '.us-nav-style_' . $btn_style['id'] . ' > *{';
	$buttons_css .= 'min-width:calc(' . $btn_line_height . ' + 2 * ' . $btn_style['height'] . ');';
	$buttons_css .= '}';

	// Check if the button background has a gradient
	$has_gradient = FALSE;
	if (
		strpos( us_get_color( $btn_style['color_bg'], /* Gradient */ TRUE ), 'gradient' ) !== FALSE
		OR strpos( us_get_color( $btn_style['color_bg_hover'], /* Gradient */ TRUE ), 'gradient' ) !== FALSE
	) {
		$has_gradient = TRUE;
	}

	// Extra layer for "Slide" hover type OR for gradient backgrounds (cause gradients don't support transition)
	if ( ( isset( $btn_style['hover'] ) AND $btn_style['hover'] == 'slide' ) OR $has_gradient ) {

		if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
			$buttons_css .= '.woocommerce .button.add_to_cart_button,';
		}
		$buttons_css .= '.us-btn-style_' . $btn_style['id'] . '{';
		$buttons_css .= 'overflow: hidden;';
		$buttons_css .= '-webkit-transform: translateZ(0);'; // fix for Safari
		$buttons_css .= '}';

		if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
			$buttons_css .= '.no-touch .woocommerce .button.add_to_cart_button > *,';
		}
		$buttons_css .= '.us-btn-style_' . $btn_style['id'] . ' > * {';
		$buttons_css .= 'position: relative;';
		$buttons_css .= 'z-index: 1;';
		$buttons_css .= '}';

		if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
			$buttons_css .= '.no-touch .woocommerce .button.add_to_cart_button:hover,';
		}
		$buttons_css .= '.no-touch .us-btn-style_' . $btn_style['id'] . ':hover {';
		if ( ! empty( us_get_color( $btn_style['color_bg'], /* Gradient */ TRUE ) ) AND ! empty( $btn_style['color_bg_hover'] ) ) {
			$buttons_css .= 'background:' . us_get_color( $btn_style['color_bg'], /* Gradient */ TRUE ) . ';';
		} else {
			$buttons_css .= 'background: transparent;';
		}
		$buttons_css .= '}';

		if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
			$buttons_css .= '.no-touch .woocommerce .button.add_to_cart_button:after,';
		}
		$buttons_css .= '.no-touch .us-btn-style_' . $btn_style['id'] . ':after {';
		$buttons_css .= 'content: ""; position: absolute; top: 0; left: 0; right: 0;';
		if ( $btn_style['hover'] == 'slide' ) {
			$buttons_css .= 'height: 0; transition: height 0.3s;';
		} else {
			$buttons_css .= 'bottom: 0; opacity: 0; transition: opacity 0.3s;';
		}
		$buttons_css .= 'background:' . (
			! empty( $btn_style['color_bg_hover'] )
				? us_get_color( $btn_style['color_bg_hover'], /* Gradient */ TRUE )
				: 'transparent'
			) . ';';
		$buttons_css .= '}';

		if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
			$buttons_css .= '.no-touch .woocommerce .button.add_to_cart_button:hover:after,';
		}
		$buttons_css .= '.no-touch .us-btn-style_' . $btn_style['id'] . ':hover:after {';
		if ( $btn_style['hover'] == 'slide' ) {
			$buttons_css .= 'height: 100%;';
		} else {
			$buttons_css .= 'opacity: 1;';
		}
		$buttons_css .= '}';
	}

}

// Include buttons CSS if admin included buttons CSS into optimized CSS file or no CSS optimization was selected
if ( us_is_asset_used( 'buttons' ) ) {
	echo strip_tags( $buttons_css );
}
unset( $buttons_css );

// Generate Input Fields styles
foreach( us_get_option( 'input_fields' ) as $input_fields ) {

	// Check if the fields has default colors to override them in Rows with other Color Style
	if ( empty( $input_fields['color_bg'] ) OR $input_fields['color_bg'] === 'transparent' ) {
		$_fields_have_no_bg_color = TRUE;
	}
	if ( $input_fields['color_bg'] == '_content_bg_alt' ) {
		$_fields_have_alt_bg_color = TRUE;
	}
	if ( $input_fields['color_border'] == '_content_border' ) {
		$_fields_have_border_color = TRUE;
	}
	if ( $input_fields['color_text'] == '_content_text' ) {
		$_fields_have_text_color = TRUE;
	}

	// Default styles
	echo '.w-filter.state_desktop.style_drop_default .w-filter-item-title,';
	echo '.select2-selection,';
	echo 'select,';
	echo 'textarea,';
	echo 'input:not([type="submit"]),';
	echo '.w-form-checkbox,';
	echo '.w-form-radio {';
	echo us_get_font_css( $input_fields['font'] );
	echo sprintf( 'font-size:%s;', $input_fields['font_size'] );
	echo sprintf( 'font-weight:%s;', $input_fields['font_weight'] );
	echo sprintf( 'letter-spacing:%s;', $input_fields['letter_spacing'] );
	echo sprintf( 'border-width:%s;', $input_fields['border_width'] );
	echo sprintf( 'border-radius:%s;', $input_fields['border_radius'] );

	if ( ! empty( $input_fields['color_bg'] ) ) {
		echo sprintf( 'background:%s;', us_get_color( $input_fields['color_bg'], /* Gradient */ TRUE ) );
	}
	if ( ! empty( $input_fields['color_border'] ) ) {
		echo sprintf( 'border-color:%s;', us_get_color( $input_fields['color_border'] ) );
	}
	if ( ! empty( $input_fields['color_text'] ) ) {
		echo sprintf( 'color:%s;', us_get_color( $input_fields['color_text'] ) );
	}
	if ( ! empty( $input_fields['color_shadow'] ) ) {
		$_shadow_inset = ! empty( $input_fields['shadow_inset'] ) ? 'inset' : '';
		echo sprintf(
			'box-shadow: %s %s %s %s %s %s;',
			$input_fields['shadow_offset_h'],
			$input_fields['shadow_offset_v'],
			$input_fields['shadow_blur'],
			$input_fields['shadow_spread'],
			us_get_color( $input_fields['color_shadow'] ),
			$_shadow_inset
		);
	}
	echo '}';

	// For select2 dropdown
	if ( ! empty( $input_fields['color_bg'] ) AND us_get_color( $input_fields['color_bg'], /* Gradient */ TRUE ) !== 'transparent' ) {
		echo 'body .select2-dropdown {';
		echo sprintf( 'background:%s;', us_get_color( $input_fields['color_bg'], /* Gradient */ TRUE ) );
		if ( ! empty( $input_fields['color_text'] ) ) {
			echo sprintf( 'color:%s;', us_get_color( $input_fields['color_text'] ) );
		}
		echo '}';
	}

	// For select and input separately
	echo '.w-filter.state_desktop.style_drop_default .w-filter-item-title,';
	echo '.select2-selection,';
	echo 'select,';
	echo 'input:not([type="submit"]) {';
	echo sprintf( 'min-height:%s;', $input_fields['height'] );
	echo sprintf( 'line-height:%s;', $input_fields['height'] );
	echo sprintf( 'padding:0 %s;', $input_fields['padding'] );
	echo '}';
	echo 'select {';
	echo sprintf( 'height:%s;', $input_fields['height'] ); // fallback for correct appearance
	echo '}';

	// For textarea separately
	echo 'textarea {';
	echo sprintf( 'padding: calc(%s/2 + %s - 0.7em) %s;', $input_fields['height'], $input_fields['border_width'], $input_fields['padding'] );
	echo '}';

	// On Focus styles
	echo '.w-filter.state_desktop.style_drop_default .w-filter-item-title:focus,';
	echo '.select2-container--open .select2-selection,';
	echo 'select:focus,';
	echo 'textarea:focus,';
	echo 'input:not([type="submit"]):focus,';
	echo 'input:focus + .w-form-checkbox,';
	echo 'input:focus + .w-form-radio {';

	if ( ! empty( $input_fields['color_bg_focus'] ) ) {
		echo sprintf( 'background:%s !important;', us_get_color( $input_fields['color_bg_focus'], /* Gradient */ TRUE ) );
	}
	if ( ! empty( $input_fields['color_border_focus'] ) ) {
		echo sprintf( 'border-color:%s !important;', us_get_color( $input_fields['color_border_focus'] ) );
	}
	if ( ! empty( $input_fields['color_text_focus'] ) ) {
		echo sprintf( 'color:%s !important;', us_get_color( $input_fields['color_text_focus'] ) );
	}
	if ( ! empty( $input_fields['color_shadow'] ) OR ! empty( $input_fields['color_shadow_focus'] )	) {

		$_shadow_focus_color = ! empty( $input_fields['color_shadow_focus'] )
			? us_get_color( $input_fields['color_shadow_focus'] )
			: us_get_color( $input_fields['color_shadow'] );
		$_shadow_focus_inset = ! empty( $input_fields['shadow_focus_inset'] ) ? 'inset' : '';

		echo sprintf(
			'box-shadow: %s %s %s %s %s %s;',
			$input_fields['shadow_focus_offset_h'],
			$input_fields['shadow_focus_offset_v'],
			$input_fields['shadow_focus_blur'],
			$input_fields['shadow_focus_spread'],
			$_shadow_focus_color,
			$_shadow_focus_inset
		);
	}
	echo '}';

	// Dropdown arrow, Search icon separately
	if ( $with_shop ) {
		echo '.woocommerce-ordering:after,';
		echo '.woocommerce-select:after,';
		echo '.widget_product_search form:after,';
	}
	echo '.w-filter-item[data-ui_type="dropdown"] .w-filter-item-values:after,';
	echo '.w-filter.state_desktop.style_drop_default .w-filter-item-title:after,';
	echo '.w-order-select:after,';
	echo '.select2-selection__arrow:after,';
	echo '.w-search-form-btn,';
	echo '.widget_search form:after,';
	echo '.w-form-row-field:after {';
	echo sprintf( 'font-size: %s;', $input_fields['font_size'] );
	echo sprintf( 'margin:0 %s;', $input_fields['padding'] );
	echo sprintf( 'color:%s;', us_get_color( $input_fields['color_text'] ) );
	echo '}';

	// Fields icons separately
	echo '.w-form-row-field > i {';
	echo sprintf( 'font-size: %s;', $input_fields['font_size'] );
	echo sprintf( 'top: calc(%s/2);', $input_fields['height'] );
	echo sprintf( 'margin: %s;', $input_fields['border_width'] );
	echo sprintf( 'padding:0 %s;', $input_fields['padding'] );
	echo sprintf( 'color:%s;', us_get_color( $input_fields['color_text'] ) );
	echo '}';
	echo '.w-form-row.with_icon input,';
	echo '.w-form-row.with_icon textarea,';
	echo '.w-form-row.with_icon select {';
	echo sprintf( 'padding-%s: calc(1.8em + %s);', ( is_rtl() ? 'right' : 'left' ), $input_fields['padding'] );
	echo '}';

	if ( ! empty( $input_fields['color_text_focus'] ) ) {
		echo '.w-form-row.focused .w-form-row-field > i {';
		echo sprintf( 'color:%s;', us_get_color( $input_fields['color_text_focus'] ) );
		echo '}';
	}

	// For form label separately
	echo '.w-form-row.move_label .w-form-row-label {';
	echo sprintf( 'font-size:%s;', $input_fields['font_size'] );
	echo sprintf( 'top: calc(%s/2 + %s - 0.7em);', $input_fields['height'], $input_fields['border_width'] );
	echo sprintf( 'margin: 0 %s;', $input_fields['padding'] );
	if ( ! empty( $input_fields['color_bg'] ) AND us_get_color( $input_fields['color_bg'], /* Gradient*/ TRUE ) !== 'transparent' ) {
		echo sprintf( 'background-color:%s;', us_get_color( $input_fields['color_bg'], /* Gradient*/ TRUE ) );
	}
	if ( ! empty( $input_fields['color_text'] ) ) {
		echo sprintf( 'color:%s;', us_get_color( $input_fields['color_text'] ) );
	}
	echo '}';
	echo '.w-form-row.with_icon.move_label .w-form-row-label {';
	echo sprintf( 'margin-%s: calc(1.6em + %s);', ( is_rtl() ? 'right' : 'left' ), $input_fields['padding'] );
	echo '}';
}

if ( us_get_option( 'keyboard_accessibility' ) ) { ?>
a:focus,
button:focus,
input[type="checkbox"]:focus + i,
input[type="submit"]:focus {
	outline: 2px dotted <?= us_get_color( 'color_content_primary' ) ?>;
	}
<?php } else { ?>
a,
button,
input[type="submit"],
.ui-slider-handle {
	outline: none !important;
	}
<?php } ?>

/* Back to top Button */
<?php if ( ! us_get_option( 'back_to_top_style', '' ) ) { ?>
.w-toplink,
<?php } ?>
.w-header-show {
	background: <?php echo us_get_color( 'back_to_top_color', /* Gradient */ TRUE ) ?>;
	}

/* Colors
  =============================================================================================================================== */

body {
	-webkit-tap-highlight-color: <?= us_hex2rgba( us_get_color( 'color_content_primary' ), 0.2 ) ?>;
	}



/*************************** Content Colors ***************************/

/* Background Color */
.has-content-bg-background-color,
body.us_iframe,
.l-preloader,
.l-canvas,
.l-footer,
.l-popup-box-content,
.l-cookie,
.g-filters.style_1 .g-filters-item.active,
.w-filter.state_mobile .w-filter-list,
.w-filter.state_desktop[class*="style_drop_"] .w-filter-item-values,
<?php if ( ! empty( $_fields_have_no_bg_color ) ) { ?>
.w-form-row.move_label .w-form-row-label,
<?php } ?>
.w-pricing-item-h,
.w-tabs.style_default .w-tabs-item.active,
.no-touch .w-tabs.style_default .w-tabs-item.active:hover,
.w-tabs.style_modern .w-tabs-item:after,
.w-tabs.style_timeline .w-tabs-item,
.w-tabs.style_timeline .w-tabs-section-header-h,
.leaflet-popup-content-wrapper,
.leaflet-popup-tip,
.ui-datepicker,
<?php if ( $with_shop ) { ?>
.w-cart-dropdown,
.woocommerce-tabs .tabs li.active,
.no-touch .woocommerce-tabs .tabs li.active:hover,
.woocommerce .comment-respond,
.woocommerce .shipping-calculator-form,
.woocommerce #payment .payment_box,
.select2-dropdown,
<?php } ?>
<?php if ( $with_gforms ) { ?>
.chosen-search input,
.chosen-choices li.search-choice,
<?php } ?>
<?php if ( $with_forums ) { ?>
#bbp-user-navigation li.current,
<?php } ?>
.wpml-ls-statics-footer {
	background: <?= us_get_color( 'color_content_bg', TRUE ) ?>;
	}
<?php if ( $with_shop ) { ?>
.woocommerce #payment .payment_methods li > input:checked + label,
.woocommerce .blockUI.blockOverlay {
	background: <?= us_get_color( 'color_content_bg', TRUE ) ?> !important;
	}
<?php } ?>
.has-content-bg-color,
.w-iconbox.style_circle.color_contrast .w-iconbox-icon {
	color: <?= us_get_color( 'color_content_bg' ) ?>;
	}

/* Alternate Background Color */
.has-content-bg-alt-background-color,
.w-actionbox.color_light,
.g-filters.style_1,
.g-filters.style_2 .g-filters-item.active,
.w-filter.state_desktop.style_switch_default .w-filter-item-value.selected,
.w-flipbox-front,
.w-grid-none,
.w-ibanner,
.w-iconbox.style_circle.color_light .w-iconbox-icon,
.w-pricing.style_simple .w-pricing-item-header,
.w-pricing.style_cards .w-pricing-item-header,
.w-pricing.style_flat .w-pricing-item-h,
.w-progbar-bar,
.w-progbar.style_3 .w-progbar-bar:before,
.w-progbar.style_3 .w-progbar-bar-count,
.w-socials.style_solid .w-socials-item-link,
.w-tabs.style_default .w-tabs-list,
.w-tabs.style_timeline.zephyr .w-tabs-item,
.w-tabs.style_timeline.zephyr .w-tabs-section-header-h,
.no-touch .l-main .widget_nav_menu a:hover,
.no-touch .navstyle_circle.navpos_outside > .owl-nav div:hover,
.no-touch .navstyle_block.navpos_outside > .owl-nav div:hover,
.no-touch .ui-datepicker a:hover,
<?php if ( $with_shop ) { ?>
.woocommerce .quantity .plus,
.woocommerce .quantity .minus,
.woocommerce-tabs .tabs,
.woocommerce .cart_totals,
.woocommerce-checkout #order_review,
.woocommerce-table--order-details,
.woocommerce ul.order_details,
.widget_layered_nav li a:before,
<?php } ?>
<?php if ( $with_gforms ) { ?>
.chosen-single,
.chosen-drop,
.chosen-choices,
.gf_progressbar,
<?php } ?>
<?php if ( $with_forums ) { ?>
#subscription-toggle,
#favorite-toggle,
#bbp-user-navigation,
<?php } ?>
<?php if ( $with_events ) { ?>
.single-tribe_events .tribe-events-event-meta,
<?php } ?>
.smile-icon-timeline-wrap .timeline-wrapper .timeline-block,
.smile-icon-timeline-wrap .timeline-feature-item.feat-item,
.wpml-ls-legacy-dropdown a,
.wpml-ls-legacy-dropdown-click a,
.tablepress .row-hover tr:hover td {
	background: <?= us_get_color( 'color_content_bg_alt', TRUE ) ?>;
	}
.timeline-wrapper .timeline-post-right .ult-timeline-arrow l,
.timeline-wrapper .timeline-post-left .ult-timeline-arrow l,
.timeline-feature-item.feat-item .ult-timeline-arrow l {
	border-color: <?= us_get_color( 'color_content_bg_alt' ) ?>;
	}
.has-content-bg-alt-color {
	color: <?= us_get_color( 'color_content_bg_alt' ) ?>;
	}

/* Border Color */
hr,
td,
th,
.l-section,
.vc_column_container,
.vc_column-inner,
.w-comments .children,
.w-image,
.w-pricing-item-h,
.w-sharing-item,
.w-tabs-list,
.w-tabs-section,
.widget_calendar #calendar_wrap,
.l-main .widget_nav_menu .menu,
.l-main .widget_nav_menu .menu-item a,
<?php if ( $with_shop ) { ?>
.woocommerce .commentlist .comment-text,
.widget_price_filter .ui-slider-handle,
.w-post-elm.attributes.display_table > div > *,
<?php } ?>
<?php if ( $with_forums ) { ?>
#bbpress-forums .bbp-body > ul,
#bbpress-forums li.bbp-header,
.bbp-replies .bbp-body,
div.bbp-forum-header,
div.bbp-topic-header,
div.bbp-reply-header,
.bbp-pagination-links a,
.bbp-pagination-links span,
span.bbp-topic-pagination a.page-numbers,
<?php } ?>
<?php if ( $with_events ) { ?>
.tribe-common--breakpoint-medium.tribe-events .tribe-events-c-events-bar--border,
<?php } ?>
<?php if ( $with_gforms ) { ?>
.gform_wrapper .gsection,
.gform_wrapper .gf_page_steps,
.form_saved_message,
<?php } ?>
.smile-icon-timeline-wrap .timeline-line {
	border-color: <?= us_get_color( 'color_content_border' ) ?>;
	}
.has-content-border-color,
.w-separator.color_border,
.w-iconbox.color_light .w-iconbox-icon {
	color: <?= us_get_color( 'color_content_border' ) ?>;
	}
.has-content-border-background-color,
.w-flipbox-back,
.w-iconbox.style_circle.color_light .w-iconbox-icon,
<?php if ( $with_shop ) { ?>
.no-touch .woocommerce .quantity .plus:hover,
.no-touch .woocommerce .quantity .minus:hover,
.no-touch .woocommerce #payment .payment_methods li > label:hover,
.widget_price_filter .ui-slider:before,
<?php } ?>
.no-touch .wpml-ls-sub-menu a:hover {
	background: <?= us_get_color( 'color_content_border', TRUE ) ?>;
	}
.w-iconbox.style_outlined.color_light .w-iconbox-icon,
.w-socials.style_outlined .w-socials-item-link,
.pagination > :not(.custom) > .page-numbers {
	box-shadow: 0 0 0 2px <?= us_get_color( 'color_content_border' ) ?> inset;
	}

/* Heading Color */
.has-content-heading-color,
.l-cookie,
h1, h2, h3, h4, h5, h6,
<?php if ( $with_shop ) { ?>
.woocommerce .product .price,
<?php } ?>
.w-counter.color_heading .w-counter-value {
	<?php if ( strpos( us_get_color( 'color_content_heading', TRUE ), 'gradient' ) !== FALSE ) { ?>
	background: <?= us_get_color( 'color_content_heading', TRUE ) ?>;
	-webkit-background-clip: text;
	background-clip: text;
	color: transparent;
	<?php } else { ?>
	color: <?= us_get_color( 'color_content_heading' ) ?>;
	<?php } ?>
	}
.has-content-heading-background-color,
.w-progbar.color_heading .w-progbar-bar-h {
	background: <?= us_get_color( 'color_content_heading', TRUE ) ?>;
	}
<?php
// Headings 1-6 colors
for ( $i = 1; $i <= 6; $i ++ ) {
	if ( $h_color = us_get_color( 'h' . $i . '_color', TRUE ) ) {
		$h_color_important = us_get_option( 'h' . $i . '_color_override', array() ) ? '!important' : '';

		echo 'h' . $i . '{';
		if ( strpos( $h_color, 'gradient' ) !== FALSE ) {
			echo 'background-image:' . $h_color . ';';
			echo '-webkit-background-clip: text;';
			echo 'background-clip: text;';
			echo 'color: transparent' . $h_color_important . ';';
		} else {
			echo 'color:' . $h_color . $h_color_important . ';';
		}
		echo '}';
	}
}
?>

/* Text Color */
.l-canvas,
.l-footer,
.l-popup-box-content,
.w-ibanner,
.w-filter.state_mobile .w-filter-list,
.w-filter.state_desktop[class*="style_drop_"] .w-filter-item-values,
.w-iconbox.color_light.style_circle .w-iconbox-icon,
.w-tabs.style_timeline .w-tabs-item,
.w-tabs.style_timeline .w-tabs-section-header-h,
.leaflet-popup-content-wrapper,
.leaflet-popup-tip,
.ui-datepicker,
<?php if ( $with_shop ) { ?>
.w-cart-dropdown,
.woocommerce .comment-respond,
.select2-dropdown,
<?php } ?>
.has-content-text-color {
	color: <?= us_get_color( 'color_content_text' ) ?>;
	}
.has-content-text-background-color,
.w-iconbox.style_circle.color_contrast .w-iconbox-icon,
.w-scroller-dot span {
	background: <?= us_get_color( 'color_content_text', TRUE ) ?>;
	}
.w-iconbox.style_outlined.color_contrast .w-iconbox-icon {
	box-shadow: 0 0 0 2px <?= us_get_color( 'color_content_text' ) ?> inset;
	}
.w-scroller-dot span {
	box-shadow: 0 0 0 2px <?= us_get_color( 'color_content_text' ) ?>;
	}

/* Link Color */
a {
	color: <?= us_get_color( 'color_content_link' ) ?>;
	}

/* Link Hover Color */
.no-touch a:hover,
.no-touch .tablepress .sorting:hover,
.no-touch .post_navigation.layout_simple a:hover .post_navigation-item-title {
	color: <?= us_get_color( 'color_content_link_hover' ) ?>;
	}
<?php if ( $with_shop ) { ?>
.no-touch .w-cart-dropdown a:not(.button):hover {
	color: <?= us_get_color( 'color_content_link_hover' ) ?> !important;
	}
<?php } ?>

/* Primary Color */
.has-content-primary-color,
.g-preloader,
.l-main .w-contacts-item:before,
.w-counter.color_primary .w-counter-value,
.g-filters.style_1 .g-filters-item.active,
.g-filters.style_3 .g-filters-item.active,
.w-filter.state_desktop.style_switch_trendy .w-filter-item-value.selected,
.w-iconbox.color_primary .w-iconbox-icon,
.no-touch .w-post-elm .w-post-slider-trigger:hover,
.w-separator.color_primary,
.w-sharing.type_outlined.color_primary .w-sharing-item,
.no-touch .w-sharing.type_simple.color_primary .w-sharing-item:hover i,
.w-tabs.style_default .w-tabs-item.active,
.w-tabs.style_trendy .w-tabs-item.active,
.w-tabs-section.active:not(.has_text_color) .w-tabs-section-header,
.owl-dot span,
.rsBullet span,
.tablepress .sorting_asc,
.tablepress .sorting_desc,
<?php if ( $with_shop ) { ?>
.price > ins,
.star-rating span:before,
.woocommerce-tabs .tabs li.active,
.woocommerce-product-gallery li img,
.no-touch .woocommerce-tabs .tabs li.active:hover,
.woocommerce #payment .payment_methods li > input:checked + label,
<?php } ?>
<?php if ( $with_forums ) { ?>
#subscription-toggle span.is-subscribed:before,
#favorite-toggle span.is-favorite:before,
<?php } ?>
.highlight_primary {
	color: <?= us_get_color( 'color_content_primary' ) ?>;
	}
.has-content-primary-background-color,
.l-section.color_primary,
.us-btn-style_badge,
.no-touch .post_navigation.layout_sided a:hover .post_navigation-item-arrow,
.highlight_primary_bg,
.w-actionbox.color_primary,
.no-touch .g-filters.style_1 .g-filters-item:hover,
.no-touch .g-filters.style_2 .g-filters-item:hover,
.no-touch .w-filter.state_desktop.style_switch_default .w-filter-item-value:hover,
.bypostauthor > .w-comments-item-meta .w-comments-item-author span,
.w-filter-opener:not(.w-btn) span:after,
.w-grid .with_quote_icon,
.w-iconbox.style_circle.color_primary .w-iconbox-icon,
.no-touch .w-iconbox.style_circle .w-iconbox-icon:before,
.no-touch .w-iconbox.style_outlined .w-iconbox-icon:before,
.no-touch .w-person-links-item:before,
.w-pricing.style_simple .type_featured .w-pricing-item-header,
.w-pricing.style_cards .type_featured .w-pricing-item-header,
.w-pricing.style_flat .type_featured .w-pricing-item-h,
.w-progbar.color_primary .w-progbar-bar-h,
.w-sharing.type_solid.color_primary .w-sharing-item,
.w-sharing.type_fixed.color_primary .w-sharing-item,
.w-sharing.type_outlined.color_primary .w-sharing-item:before,
.no-touch .w-sharing-tooltip .w-sharing-item:hover,
.w-socials-item-link-hover,
.w-tabs-list-bar,
.w-tabs.style_modern .w-tabs-list,
.w-tabs.style_timeline .w-tabs-item:before,
.w-tabs.style_timeline .w-tabs-section-header-h:before,
.no-touch .w-header-show:hover,
.ui-datepicker .ui-datepicker-calendar a.ui-state-active,
<?php if ( ! us_get_option( 'back_to_top_style', '' ) ) { ?>
.no-touch .w-toplink.active:hover,
<?php } ?>
.no-touch .pagination > :not(.custom) > .page-numbers:before,
.pagination > :not(.custom) > .page-numbers.current,
.l-main .widget_nav_menu .menu-item.current-menu-item > a,
.rsThumb.rsNavSelected,
<?php if ( $with_shop ) { ?>
p.demo_store,
.woocommerce .onsale,
.widget_price_filter .ui-slider-range,
.widget_layered_nav li.chosen a:before,
<?php } ?>
<?php if ( $with_forums ) { ?>
.no-touch .bbp-pagination-links a:hover,
.bbp-pagination-links span.current,
.no-touch span.bbp-topic-pagination a.page-numbers:hover,
<?php } ?>
<?php if ( $with_gforms ) { ?>
.chosen-results li.highlighted,
.gform_page_footer .gform_next_button,
.gf_progressbar .gf_progressbar_percentage,
<?php } ?>
.select2-results__option--highlighted {
	background: <?= us_get_color( 'color_content_primary', TRUE ) ?>;
	}
.w-iconbox.color_primary.style_outlined .w-iconbox-icon,
.w-sharing.type_outlined.color_primary .w-sharing-item,
.w-tabs.style_timeline .w-tabs-item,
.w-tabs.style_timeline .w-tabs-section-header-h {
	box-shadow: 0 0 0 2px <?= us_get_color( 'color_content_primary' ) ?> inset;
	}

<?php if ( strpos( us_get_color( 'color_content_primary', TRUE ), 'gradient' ) !== FALSE ) { ?>
.w-iconbox.color_primary.style_default .w-iconbox-icon i:not(.fad) {
	background: <?= us_get_color( 'color_content_primary', TRUE ) ?>;
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	}
<?php } ?>

/* Secondary Color */
.has-content-secondary-color,
.w-counter.color_secondary .w-counter-value,
.w-iconbox.color_secondary .w-iconbox-icon,
.w-separator.color_secondary,
.w-sharing.type_outlined.color_secondary .w-sharing-item,
.no-touch .w-sharing.type_simple.color_secondary .w-sharing-item:hover i,
.highlight_secondary {
	color: <?= us_get_color( 'color_content_secondary' ) ?>;
	}
.has-content-secondary-background-color,
.l-section.color_secondary,
.w-actionbox.color_secondary,
.no-touch .us-btn-style_badge:hover,
.w-iconbox.style_circle.color_secondary .w-iconbox-icon,
.w-progbar.color_secondary .w-progbar-bar-h,
.w-sharing.type_solid.color_secondary .w-sharing-item,
.w-sharing.type_fixed.color_secondary .w-sharing-item,
.w-sharing.type_outlined.color_secondary .w-sharing-item:before,
.highlight_secondary_bg {
	background: <?= us_get_color( 'color_content_secondary', TRUE ) ?>;
	}
.w-iconbox.color_secondary.style_outlined .w-iconbox-icon,
.w-sharing.type_outlined.color_secondary .w-sharing-item {
	box-shadow: 0 0 0 2px <?= us_get_color( 'color_content_secondary' ) ?> inset;
	}
<?php if ( strpos( us_get_color( 'color_content_secondary', TRUE ), 'gradient' ) !== FALSE ) { ?>
.w-iconbox.color_secondary.style_default .w-iconbox-icon i:not(.fad) {
	background: <?= us_get_color( 'color_content_secondary', TRUE ) ?>;
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	}
<?php } ?>

/* Fade Elements Color */
.has-content-faded-color,
blockquote:before,
.w-form-row-description,
.l-main .post-author-website,
.l-main .widget_tag_cloud,
<?php if ( $with_shop ) { ?>
.l-main .widget_product_tag_cloud,
<?php } ?>
<?php if ( $with_forums ) { ?>
p.bbp-topic-meta,
<?php } ?>
.highlight_faded {
	color: <?= us_get_color( 'color_content_faded' ) ?>;
	}
.has-content-faded-background-color {
	background: <?= us_get_color( 'color_content_faded', TRUE ) ?>;
	}

/*************************** Alternate Content Colors ***************************/

/* Background Color */
.l-section.color_alternate,
.color_alternate .g-filters.style_1 .g-filters-item.active,
<?php if ( ! empty( $_fields_have_no_bg_color ) ) { ?>
.color_alternate .w-form-row.move_label .w-form-row-label,
<?php } ?>
.color_alternate .w-pricing-item-h,
.color_alternate .w-tabs.style_default .w-tabs-item.active,
.color_alternate .w-tabs.style_modern .w-tabs-item:after,
.no-touch .color_alternate .w-tabs.style_default .w-tabs-item.active:hover,
.color_alternate .w-tabs.style_timeline .w-tabs-item,
.color_alternate .w-tabs.style_timeline .w-tabs-section-header-h {
	background: <?= us_get_color( 'color_alt_content_bg', TRUE ) ?>;
	}
.color_alternate .w-iconbox.style_circle.color_contrast .w-iconbox-icon {
	color: <?= us_get_color( 'color_alt_content_bg' ) ?>;
	}

/* Alternate Background Color */
<?php if ( ! empty( $_fields_have_alt_bg_color ) ) { ?>
.color_alternate input:not([type="submit"]),
.color_alternate textarea,
.color_alternate select,
.color_alternate .w-form-checkbox,
.color_alternate .w-form-radio,
.color_alternate .move_label .w-form-row-label,
<?php } ?>
.color_alternate .g-filters.style_1,
.color_alternate .g-filters.style_2 .g-filters-item.active,
.color_alternate .w-filter.state_desktop.style_switch_default .w-filter-item-value.selected,
.color_alternate .w-grid-none,
.color_alternate .w-iconbox.style_circle.color_light .w-iconbox-icon,
.color_alternate .w-pricing.style_simple .w-pricing-item-header,
.color_alternate .w-pricing.style_cards .w-pricing-item-header,
.color_alternate .w-pricing.style_flat .w-pricing-item-h,
.color_alternate .w-progbar-bar,
.color_alternate .w-socials.style_solid .w-socials-item-link,
.color_alternate .w-tabs.style_default .w-tabs-list,
.color_alternate .ginput_container_creditcard {
	background: <?= us_get_color( 'color_alt_content_bg_alt', TRUE ) ?>;
	}

/* Border Color */
.l-section.color_alternate,
.color_alternate td,
.color_alternate th,
.color_alternate .vc_column_container,
.color_alternate .vc_column-inner,
.color_alternate .w-comments .children,
.color_alternate .w-image,
.color_alternate .w-pricing-item-h,
.color_alternate .w-sharing-item,
.color_alternate .w-tabs-list,
.color_alternate .w-tabs-section {
	border-color: <?= us_get_color( 'color_alt_content_border' ) ?>;
	}
.color_alternate .w-separator.color_border,
.color_alternate .w-iconbox.color_light .w-iconbox-icon {
	color: <?= us_get_color( 'color_alt_content_border' ) ?>;
	}
.color_alternate .w-iconbox.style_circle.color_light .w-iconbox-icon {
	background: <?= us_get_color( 'color_alt_content_border', TRUE ) ?>;
	}
.color_alternate .w-iconbox.style_outlined.color_light .w-iconbox-icon,
.color_alternate .w-socials.style_outlined .w-socials-item-link,
.color_alternate .pagination > :not(.custom) > .page-numbers {
	box-shadow: 0 0 0 2px <?= us_get_color( 'color_alt_content_border' ) ?> inset;
	}

/* Heading Color */
.l-section.color_alternate h1,
.l-section.color_alternate h2,
.l-section.color_alternate h3,
.l-section.color_alternate h4,
.l-section.color_alternate h5,
.l-section.color_alternate h6,
.color_alternate .w-counter.color_heading .w-counter-value {
	<?php if ( strpos( us_get_color( 'color_alt_content_heading', TRUE ), 'gradient' ) !== FALSE ) { ?>
	background: <?= us_get_color( 'color_alt_content_heading', TRUE ) ?>;
	-webkit-background-clip: text;
	background-clip: text;
	color: transparent;
	<?php } else { ?>
	color: <?= us_get_color( 'color_alt_content_heading' ) ?>;
	<?php } ?>
	}
.color_alternate .w-progbar.color_heading .w-progbar-bar-h {
	background: <?= us_get_color( 'color_alt_content_heading', TRUE ) ?>;
	}

/* Text Color */
.l-section.color_alternate,
.color_alternate .w-iconbox.color_contrast .w-iconbox-icon,
.color_alternate .w-iconbox.color_light.style_circle .w-iconbox-icon,
.color_alternate .w-tabs.style_timeline .w-tabs-item,
.color_alternate .w-tabs.style_timeline .w-tabs-section-header-h {
	color: <?= us_get_color( 'color_alt_content_text' ) ?>;
	}
.color_alternate .w-iconbox.style_circle.color_contrast .w-iconbox-icon {
	background: <?= us_get_color( 'color_alt_content_text', TRUE ) ?>;
	}
.color_alternate .w-iconbox.style_outlined.color_contrast .w-iconbox-icon {
	box-shadow: 0 0 0 2px <?= us_get_color( 'color_alt_content_text' ) ?> inset;
	}

/* Link Color */
.color_alternate a {
	color: <?= us_get_color( 'color_alt_content_link' ) ?>;
	}

/* Link Hover Color */
.no-touch .color_alternate a:hover {
	color: <?= us_get_color( 'color_alt_content_link_hover' ) ?>;
	}

/* Primary Color */
.color_alternate .highlight_primary,
.l-main .color_alternate .w-contacts-item:before,
.color_alternate .w-counter.color_primary .w-counter-value,
.color_alternate .g-preloader,
.color_alternate .g-filters.style_1 .g-filters-item.active,
.color_alternate .g-filters.style_3 .g-filters-item.active,
.color_alternate .w-filter.state_desktop.style_switch_trendy .w-filter-item-value.selected,
.color_alternate .w-iconbox.color_primary .w-iconbox-icon,
.color_alternate .w-separator.color_primary,
.color_alternate .w-tabs.style_default .w-tabs-item.active,
.color_alternate .w-tabs.style_trendy .w-tabs-item.active,
.color_alternate .w-tabs-section.active:not(.has_text_color) .w-tabs-section-header {
	color: <?= us_get_color( 'color_alt_content_primary' ) ?>;
	}
.color_alternate .highlight_primary_bg,
.color_alternate .w-actionbox.color_primary,
.no-touch .color_alternate .g-filters.style_1 .g-filters-item:hover,
.no-touch .color_alternate .g-filters.style_2 .g-filters-item:hover,
.no-touch .color_alternate .w-filter.state_desktop.style_switch_default .w-filter-item-value:hover,
.color_alternate .w-iconbox.style_circle.color_primary .w-iconbox-icon,
.no-touch .color_alternate .w-iconbox.style_circle .w-iconbox-icon:before,
.no-touch .color_alternate .w-iconbox.style_outlined .w-iconbox-icon:before,
.color_alternate .w-pricing.style_simple .type_featured .w-pricing-item-header,
.color_alternate .w-pricing.style_cards .type_featured .w-pricing-item-header,
.color_alternate .w-pricing.style_flat .type_featured .w-pricing-item-h,
.color_alternate .w-progbar.color_primary .w-progbar-bar-h,
.color_alternate .w-tabs.style_modern .w-tabs-list,
.color_alternate .w-tabs.style_trendy .w-tabs-item:after,
.color_alternate .w-tabs.style_timeline .w-tabs-item:before,
.color_alternate .w-tabs.style_timeline .w-tabs-section-header-h:before,
.no-touch .color_alternate .pagination > :not(.custom) > .page-numbers:before,
.color_alternate .pagination > :not(.custom) > .page-numbers.current {
	background: <?= us_get_color( 'color_alt_content_primary', TRUE ) ?>;
	}
.color_alternate .w-iconbox.color_primary.style_outlined .w-iconbox-icon,
.color_alternate .w-tabs.style_timeline .w-tabs-item,
.color_alternate .w-tabs.style_timeline .w-tabs-section-header-h {
	box-shadow: 0 0 0 2px <?= us_get_color( 'color_alt_content_primary' ) ?> inset;
	}

/* Secondary Color */
.color_alternate .highlight_secondary,
.color_alternate .w-counter.color_secondary .w-counter-value,
.color_alternate .w-iconbox.color_secondary .w-iconbox-icon,
.color_alternate .w-separator.color_secondary {
	color: <?= us_get_color( 'color_alt_content_secondary' ) ?>;
	}
.color_alternate .highlight_secondary_bg,
.color_alternate .w-actionbox.color_secondary,
.color_alternate .w-iconbox.style_circle.color_secondary .w-iconbox-icon,
.color_alternate .w-progbar.color_secondary .w-progbar-bar-h {
	background: <?= us_get_color( 'color_alt_content_secondary', TRUE ) ?>;
	}
.color_alternate .w-iconbox.color_secondary.style_outlined .w-iconbox-icon {
	box-shadow: 0 0 0 2px <?= us_get_color( 'color_alt_content_secondary' ) ?> inset;
	}

/* Fade Elements Color */
.color_alternate .highlight_faded {
	color: <?= us_get_color( 'color_alt_content_faded' ) ?>;
	}

/*************************** Top Footer Colors ***************************/

/* Background Color */
<?php if ( ! empty( $_fields_have_no_bg_color ) ) { ?>
.color_footer-top .w-form-row.move_label .w-form-row-label,
<?php } ?>
.color_footer-top {
	background: <?= us_get_color( 'color_subfooter_bg', TRUE ) ?>;
	}

/* Alternate Background Color */
<?php if ( ! empty( $_fields_have_alt_bg_color ) ) { ?>
.color_footer-top input:not([type="submit"]),
.color_footer-top textarea,
.color_footer-top select,
.color_footer-top .w-form-checkbox,
.color_footer-top .w-form-radio,
.color_footer-top .w-form-row.move_label .w-form-row-label,
<?php } ?>
.color_footer-top .w-socials.style_solid .w-socials-item-link {
	background: <?= us_get_color( 'color_subfooter_bg_alt', TRUE ) ?>;
	}

/* Border Color */
<?php if ( ! empty( $_fields_have_border_color ) ) { ?>
.color_footer-top input:not([type="submit"]),
.color_footer-top textarea,
.color_footer-top select,
.color_footer-top .w-form-checkbox,
.color_footer-top .w-form-radio,
<?php } ?>
.color_footer-top,
.color_footer-top td,
.color_footer-top th,
.color_footer-top .vc_column_container,
.color_footer-top .vc_column-inner,
.color_footer-top .w-image,
.color_footer-top .w-pricing-item-h,
.color_footer-top .w-sharing-item,
.color_footer-top .w-tabs-list,
.color_footer-top .w-tabs-section {
	border-color: <?= us_get_color( 'color_subfooter_border' ) ?>;
	}
.color_footer-top .w-separator.color_border {
	color: <?= us_get_color( 'color_subfooter_border' ) ?>;
	}
.color_footer-top .w-socials.style_outlined .w-socials-item-link {
	box-shadow: 0 0 0 2px <?= us_get_color( 'color_subfooter_border' ) ?> inset;
	}

/* Text Color */
.color_footer-top {
	color: <?= us_get_color( 'color_subfooter_text' ) ?>;
	}

/* Link Color */
.color_footer-top a {
	color: <?= us_get_color( 'color_subfooter_link' ) ?>;
	}

/* Link Hover Color */
.no-touch .color_footer-top a:hover {
	color: <?= us_get_color( 'color_subfooter_link_hover' ) ?>;
	}

/*************************** Bottom Footer Colors ***************************/

/* Background Color */
<?php if ( ! empty( $_fields_have_no_bg_color ) ) { ?>
.color_footer-bottom .w-form-row.move_label .w-form-row-label,
<?php } ?>
.color_footer-bottom {
	background: <?= us_get_color( 'color_footer_bg', TRUE ) ?>;
	}

/* Alternate Background Color */
<?php if ( ! empty( $_fields_have_alt_bg_color ) ) { ?>
.color_footer-bottom input:not([type="submit"]),
.color_footer-bottom textarea,
.color_footer-bottom select,
.color_footer-bottom .w-form-checkbox,
.color_footer-bottom .w-form-radio,
.color_footer-bottom .w-form-row.move_label .w-form-row-label,
<?php } ?>
.color_footer-bottom .w-socials.style_solid .w-socials-item-link {
	background: <?= us_get_color( 'color_footer_bg_alt', TRUE ) ?>;
	}

/* Border Color */
<?php if ( ! empty( $_fields_have_border_color ) ) { ?>
.color_footer-bottom input:not([type="submit"]),
.color_footer-bottom textarea,
.color_footer-bottom select,
.color_footer-bottom .w-form-checkbox,
.color_footer-bottom .w-form-radio,
<?php } ?>
.color_footer-bottom,
.color_footer-bottom td,
.color_footer-bottom th,
.color_footer-bottom .vc_column_container,
.color_footer-bottom .vc_column-inner,
.color_footer-bottom .w-image,
.color_footer-bottom .w-pricing-item-h,
.color_footer-bottom .w-sharing-item,
.color_footer-bottom .w-tabs-list,
.color_footer-bottom .w-tabs-section {
	border-color: <?= us_get_color( 'color_footer_border' ) ?>;
	}
.color_footer-bottom .w-separator.color_border {
	color: <?= us_get_color( 'color_footer_border' ) ?>;
	}
.color_footer-bottom .w-socials.style_outlined .w-socials-item-link {
	box-shadow: 0 0 0 2px <?= us_get_color( 'color_footer_border' ) ?> inset;
	}

/* Text Color */
.color_footer-bottom {
	color: <?= us_get_color( 'color_footer_text' ) ?>;
	}

/* Link Color */
.color_footer-bottom a {
	color: <?= us_get_color( 'color_footer_link' ) ?>;
	}

/* Link Hover Color */
.no-touch .color_footer-bottom a:hover {
	color: <?= us_get_color( 'color_footer_link_hover' ) ?>;
	}

<?php if ( ! empty( $_fields_have_text_color ) ) { ?>
.color_alternate input:not([type="submit"]),
.color_alternate textarea,
.color_alternate select,
.color_alternate .w-form-checkbox,
.color_alternate .w-form-radio,
.color_alternate .w-form-row-field > i,
.color_alternate .w-form-row-field:after,
.color_alternate .widget_search form:after,
.color_footer-top input:not([type="submit"]),
.color_footer-top textarea,
.color_footer-top select,
.color_footer-top .w-form-checkbox,
.color_footer-top .w-form-radio,
.color_footer-top .w-form-row-field > i,
.color_footer-top .w-form-row-field:after,
.color_footer-top .widget_search form:after,
.color_footer-bottom input:not([type="submit"]),
.color_footer-bottom textarea,
.color_footer-bottom select,
.color_footer-bottom .w-form-checkbox,
.color_footer-bottom .w-form-radio,
.color_footer-bottom .w-form-row-field > i,
.color_footer-bottom .w-form-row-field:after,
.color_footer-bottom .widget_search form:after {
	color: inherit;
	}
<?php }

/* WooCommerce Product gallery settings
  =============================================================================================================================== */
if ( $with_shop AND us_get_option( 'product_gallery' ) == 'slider' ) {

	if ( us_get_option( 'product_gallery_thumbs_pos' ) == 'bottom' ) {
		$cols = intval( us_get_option( 'product_gallery_thumbs_cols', 4 ) );
		echo '.woocommerce-product-gallery--columns-' . $cols . ' li { width:' . sprintf( '%0.3f', 100 / $cols ) . '%; }';
	} else {
		echo '.woocommerce-product-gallery { display: flex;	}';
		echo '.woocommerce-product-gallery ol {	display: block; order: -1; }';
		echo '.woocommerce-product-gallery ol > li { width:' . us_get_option( 'product_gallery_thumbs_width', '6rem' ) . '; }';
	}

	// Gaps between thumbnails
	if ( $gap_half = intval( us_get_option( 'product_gallery_thumbs_gap', 0 ) ) / 2 ) {
		if ( us_get_option( 'product_gallery_thumbs_pos' ) == 'bottom' ) {
			echo '.woocommerce-product-gallery ol { margin:' . $gap_half . 'px -' . $gap_half . 'px 0; }';
		} else {
			echo '.woocommerce-product-gallery ol { margin: -' . $gap_half . 'px ' . $gap_half . 'px -' . $gap_half . 'px -' . $gap_half . 'px; }';
			echo '.rtl .woocommerce-product-gallery ol { margin: -' . $gap_half . 'px -' . $gap_half . 'px -' . $gap_half . 'px ' . $gap_half . 'px; }';
		}
		echo '.woocommerce-product-gallery ol > li { padding:' . $gap_half . 'px; }';
	}
}

/* Menu Dropdown Settings
  =============================================================================================================================== */
global $wpdb;

$wpdb_query = 'SELECT posts.ID as ID, meta.meta_value as value FROM ' . $wpdb->posts . ' posts ';
$wpdb_query .= 'RIGHT JOIN ' . $wpdb->postmeta . ' meta on (posts.id = meta.post_id AND meta.meta_key = "us_mega_menu_settings")';
$wpdb_query .= ' WHERE post_type = "nav_menu_item"';
$results = $wpdb->get_results( $wpdb_query, ARRAY_A );

foreach( $results as $result ) {

	$menu_item_id = $result['ID'];
	$settings = unserialize( $result['value'] );
	$dropdown_css_props = '';

	if ( ! isset( $settings['drop_to'] ) ) {
		// Fallback condition for theme versions prior to 6.2 (instead of migration)
		if ( isset( $settings['direction'] ) ) {
			$settings['drop_to'] = ( $settings['direction'] ) ? 'left' : 'right';
		} else {
			$settings['drop_to'] = 'right';
		}
	}

	// Full Width
	if ( $settings['width'] == 'full' ) {
		$dropdown_css_props .= 'left: 0; right: 0;';
		$dropdown_css_props .= 'transform-origin: 50% 0;';

		// Auto or Custom Width
	} else {

		// Center
		if ( $settings['drop_to'] == 'center' ) {
			$dropdown_css_props .= 'left: 50%; right: auto;';

			// Need margin-left for correct centering based on custom width divided by two
			if ( $settings['width'] == 'custom' AND preg_match( '~^(\d*\.?\d*)(.*)$~', $settings['custom_width'], $matches ) ) {
				$dropdown_css_props .= 'margin-left: -' . ( $matches[1] / 2 ) . $matches[2] . ';';
			} else {
				$dropdown_css_props .= 'margin-left: -6rem;';
			}

			// Left
		} elseif ( $settings['drop_to'] == 'left' ) {
			if ( is_rtl() ) {
				$dropdown_css_props .= 'left: 0; right: auto; transform-origin: 0 0;';
			} else {
				$dropdown_css_props .= 'left: auto; right: 0; transform-origin: 100% 0;';
			}
		}
	}

	$background_color = us_get_color( $settings['color_bg'], /* Gradient */ TRUE );
	$background_image = '';

	// Add image properties when image is set
	if ( $bg_image = $settings['bg_image'] ) {
		$img_arr = explode( '|', $bg_image );
		$body_bg_image_url = wp_get_attachment_image_url( $img_arr[0], 'full' );

		$background_image .= 'url(' . $body_bg_image_url . ') ';
		$background_image .= $settings['bg_image_position'];
		if ( $settings['bg_image_size'] != 'initial' ) {
			$background_image .= '/' . $settings['bg_image_size'];
		}
		$background_image .= ' ';
		$background_image .= $settings['bg_image_repeat'];

		// If the color value contains gradient, add comma for correct appearance
		if ( strpos( $background_color, 'gradient' ) !== FALSE ) {
			$background_image .= ',';
		}
	}

	// Output single combined background value
	if ( $background_image != '' OR $background_color != '' ) {
		$dropdown_css_props .= 'background:' . $background_image . ' ' . $background_color . ';';
	}

	if ( $settings['color_text'] != '' ) {
		$dropdown_css_props .= 'color:' . us_get_color( $settings['color_text'] ) . ';';
	}
	if ( $settings['width'] == 'custom' ) {
		$dropdown_css_props .= 'width:' . $settings['custom_width'] . ';';
	}

	// Stretch background to the screen edges
	if ( $settings['width'] == 'full' AND isset( $settings['stretch'] ) AND $settings['stretch'] ) {
		$dropdown_css_props .= 'margin: 0 -50vw;';
		$dropdown_css_props .= 'padding:' . $settings['padding'] . ' 50vw;';
	} elseif ( intval( $settings['padding'] ) != 0 ) {
		$dropdown_css_props .= 'padding:' . $settings['padding'] . ';';
	}

	// Output dropdown CSS if it's not empty
	if ( ! empty( $dropdown_css_props ) ) {
		echo '.header_hor .w-nav.type_desktop .menu-item-' . $menu_item_id . ' .w-nav-list.level_2 {';
		echo strip_tags( $dropdown_css_props );
		echo '}';
	}

	// Make menu item static in 2 cases
	if ( $settings['width'] == 'full' OR ( isset( $settings['drop_from'] ) AND $settings['drop_from'] == 'header' ) ) {
		echo '.header_hor .w-nav.type_desktop .menu-item-' . $menu_item_id . ' { position: static; }';
	}

}

// Remove filter for protocols removal from URLs for better compatibility with caching plugins and services
if ( ! us_get_option( 'keep_url_protocol', 1 ) ) {
	remove_filter( 'clean_url', 'us_remove_url_protocol', 10 );
}
