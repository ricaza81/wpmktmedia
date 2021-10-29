<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Template header
 */

$us_layout = US_Layout::instance();
?>
<!DOCTYPE HTML>
<html <?php language_attributes( 'html' ) ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php

	wp_head();

	// Output font-face styles here to prevent Google PageSpeed Insights showing "Preload key requests"
	global $us_template_directory_uri;
	$icon_fonts_css = '';
	foreach ( us_get_available_icon_sets() as $icon_set_slug => $icon_set ) {
		$icon_fonts_css .= '@font-face {';
		$icon_fonts_css .= 'font-display: block;';
		$icon_fonts_css .= 'font-style: normal;';
		$icon_fonts_css .= 'font-family: "' . $icon_set['font_family'] . '";';
		$icon_fonts_css .= 'font-weight: ' . $icon_set['font_weight'] . ';';

		// Default font files from the theme
		if ( us_get_option( 'icons_' . $icon_set_slug ) == 'default' ) {
			$icon_fonts_css .= 'src: url("' . esc_url( $us_template_directory_uri ) . '/fonts/' . $icon_set['font_file_name'] . '.woff2?ver=' . US_THEMEVERSION . '") format("woff2"),';
			$icon_fonts_css .= 'url("' . esc_url( $us_template_directory_uri ) . '/fonts/' . $icon_set['font_file_name'] . '.woff?ver=' . US_THEMEVERSION . '") format("woff");';

			// Custom font file, set in theme options by user
		} elseif ( $custom_font_url = us_get_option( 'icons_' . $icon_set_slug . '_custom_font' ) ) {
			$custom_font_ext = pathinfo( $custom_font_url, PATHINFO_EXTENSION );
			$icon_fonts_css .= 'src: url("' . esc_url( $custom_font_url ) . '") format("' . $custom_font_ext . '");';
		}

		$icon_fonts_css .= '}';

		// Change <i> main class
		if ( $icon_set_slug === 'material' ) {
			$icon_set_slug = 'material-icons';
		}

		$icon_fonts_css .= '.' . $icon_set_slug . ' {';
		$icon_fonts_css .= 'font-family: "' . $icon_set['font_family'] . '";';
		$icon_fonts_css .= 'font-weight: ' . $icon_set['font_weight'] . ';';
		$icon_fonts_css .= '}';

		// Output specific styles for Duotone icons
		if ( $icon_set_slug === 'fad' ) {
			$icon_fonts_css .= '.fad { position: relative; }';
			$icon_fonts_css .= '.fad:before { position: absolute; }';
			$icon_fonts_css .= '.fad:after { opacity: 0.4; }';
		}
	}

	// If FA Solid, Regular, Light sets are not default, then we need to use fallback icon font
	if (
		US_THEMENAME === 'Impreza'
		AND us_get_option( 'icons_fas' ) != 'default'
		AND us_get_option( 'icons_far' ) != 'default'
		AND us_get_option( 'icons_fal' ) != 'default'
		AND us_get_option( 'fallback_icon_font', 1 )
	) {
		$icon_fonts_css .= '@font-face {';
		$icon_fonts_css .= 'font-family: "fontawesome";';
		$icon_fonts_css .= 'font-display: block;';
		$icon_fonts_css .= 'font-style: normal;';
		$icon_fonts_css .= 'font-weight: 400;';
		$icon_fonts_css .= 'src: url("' . esc_url( $us_template_directory_uri ) . '/fonts/fa-fallback.woff?ver=' . US_THEMEVERSION . '") format("woff");';
		$icon_fonts_css .= '}';
	}

	if ( ! empty( $icon_fonts_css ) ) {
		?>
		<style id="us-icon-fonts"><?php echo us_minify_css( $icon_fonts_css ) ?></style>
		<?php
	}

	// Theme Options CSS
	if ( defined( 'US_DEV' ) OR ! us_get_option( 'optimize_assets', 0 ) ) {
		?>
		<style id="us-theme-options-css"><?php echo us_get_theme_options_css() ?></style>
		<?php
	}

	// Header CSS
	if ( $us_layout->header_show != 'never' ) {
		?>
		<style id="us-header-css"><?php echo us_minify_css( us_get_template( 'templates/css-header' ) ) ?></style>
		<?php
	}

	// Custom CSS from Theme Options
	if ( ! us_get_option( 'optimize_assets', 0 ) AND $us_custom_css = us_get_option( 'custom_css', '' ) ) {
		?>
		<style id="us-custom-css"><?php echo us_minify_css( $us_custom_css ) ?></style>
		<?php
	}

	// Custom HTML before </head>
	echo us_get_option( 'custom_html_head', '' );

	// Helper action
	do_action( 'us_before_closing_head_tag' );
	?>
</head>
<body <?php body_class( 'l-body ' . $us_layout->body_classes() );
if ( us_get_option( 'schema_markup' ) ) {
	if ( us_is_faqs_page() ) {
		echo ' itemscope itemtype="https://schema.org/FAQPage"';
	} else {
		echo ' itemscope itemtype="https://schema.org/WebPage"';
	}
}
if ( us_amp() ) {
	echo ' id="amp-body-id"';
}
?>>
<?php
// WP requirement
wp_body_open();

// Preloader screen
global $us_iframe;
if ( ! us_amp() AND empty( $us_iframe ) AND us_get_option( 'preloader' ) != 'disabled' AND defined( 'US_CORE_VERSION' ) ) {
	add_action( 'us_before_canvas', 'us_display_preloader', 100 );
	function us_display_preloader() {
		$preloader_type = us_get_option( 'preloader' );
		if ( ! in_array( $preloader_type, array_merge( us_get_preloader_numeric_types(), array( 'custom' ) ) ) ) {
			$preloader_type = 1;
		}

		if ( $preloader_type == 'custom' AND $preloader_image = us_get_option( 'preloader_image', '' ) ) {
			$preloader_image_html = wp_get_attachment_image( $preloader_image, 'medium' );
			if ( empty( $preloader_image_html ) ) {
				$preloader_image_html = us_get_img_placeholder( 'medium' );
			}
		} else {
			$preloader_image_html = '';
		}

		?>
		<div class="l-preloader">
			<div class="l-preloader-spinner">
				<div class="g-preloader type_<?php echo $preloader_type ?>">
					<div><?php echo $preloader_image_html ?></div>
				</div>
			</div>
		</div>
		<?php
	}
}

do_action( 'us_before_canvas' ) ?>

<div class="l-canvas <?php echo $us_layout->canvas_classes() ?>">
	<?php
	if ( $us_layout->header_show != 'never' ) {

		do_action( 'us_before_header' );

		us_load_template( 'templates/l-header' );

		do_action( 'us_after_header' );

	} ?>
