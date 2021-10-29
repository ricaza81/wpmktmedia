<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: vc_tta_section
 *
 * Overloaded by UpSolution custom implementation.
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode         string Current shortcode name
 * @var $shortcode_base    string The original called shortcode name (differs if called an alias)
 * @var $content           string Shortcode's inner content
 * @var $classes           string Extend class names
 * @var $design_css_class  string Custom design css class
 *
 * @var $title             string Section title
 * @var $icon              string Icon
 * @var $i_position        string Icon position: 'left' / 'right'
 * @var $active            bool Tab is opened when page loads
 * @var $indents           string Indents type: '' / 'none'
 * @var $bg_color          string Background color
 * @var $text_color        string Text color
 * @var $title_tag         string Title HTML tag (inherited from wrapping vc_tta_tabs shortcode): 'div' / 'h2'/ 'h3'/ 'h4'/ 'h5'/ 'h6'/ 'p'
 * @var $el_class          string Extra class name
 * @var $el_id             string ID
 */

/*
 * Global variable for storing the tabs container options along with its child tabs options
 */
global $us_tabs_options;
if ( is_array( $us_tabs_options ) ) {
	// Get last index
	$us_tabs_options_keys = array_keys( $us_tabs_options );
	$index = end( $us_tabs_options_keys );
	if ( ! empty( $us_tabs_options[ $index ] ) ) {
		// Tab indexes start from 1
		if ( ! isset( $us_tabs_options[ $index ]['us_tab_index'] ) ) {
			$us_tabs_options[ $index ]['us_tab_index'] = 0;
		}
		$us_tabs_options[ $index ]['us_tab_index'] ++;

		extract( $us_tabs_options[ $index ] );
	}
	unset( $index, $us_tabs_options_keys );
}

$us_tab_index = isset( $us_tab_index ) ? $us_tab_index : 1;

// We could overload some atts at vc_tabs implementation, so apply them here as well
if ( isset( $us_tabs_atts[ $us_tab_index - 1 ] ) ) {
	foreach ( $us_tabs_atts[ $us_tab_index - 1 ] as $_key => $_value ) {
		${$_key} = $_value;
	}
}

$content_html = do_shortcode( $content );

$_atts['class'] = 'w-tabs-section';
$_atts['class'] .= isset( $classes ) ? $classes : '';

if ( $indents ) {
	$_atts['class'] .= ' no_indents';
}
if ( $active ) {
	$_atts['class'] .= ' active';
}
if ( $text_color ) {
	$_atts['class'] .= ' has_text_color';
}

// Hide the section with empty content, if it is not USBuilder page
if ( $content_html == '' AND ! apply_filters( 'usb_is_preview_page', NULL ) ) {
	$_atts['class'] .= ' content-empty';
}

// Generate reqiured ID
if ( ! empty( $atts['tab_id'] ) ) {
	$_atts['id'] = $atts['tab_id']; // for old $tab_id value (after version 8.0)
} elseif ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
} else {
	$_atts['id'] = us_uniqid();
}

if ( empty( $tab_link ) ) {
	$tab_link = 'javascript:void(0)';
}

$inline_css = us_prepare_inline_css(
	array(
		'background' => us_get_color( $bg_color, /* Gradient */ TRUE ),
		'color' => us_get_color( $text_color ),
	)
);

$title_atts = array(
	'class' => 'w-tabs-section-title',
);
$content_atts = array(
	'class' => 'w-tabs-section-content',
	'id' => 'content-' . $_atts['id'],
	'aria-expanded' => $active ? 'true' : 'false',
);
$content_h_atts = array(
	'class' => 'w-tabs-section-content-h i-cf',
);

// Move us_custom_* class to other container
if ( ! empty( $design_css_class ) ) {
	$_atts['class'] = str_replace( ' ' . $design_css_class, '', $_atts['class'] );
	$content_atts['class'] .= ' ' . $design_css_class;
}

// Add atts for FAQs page
if ( ! empty( $us_faq_markup ) ) {
	$_atts['itemscope'] = '';
	$_atts['itemprop'] = 'mainEntity';
	$_atts['itemtype'] = 'https://schema.org/Question';
	$title_atts['itemprop'] = 'name';
	$content_atts['itemscope'] = '';
	$content_atts['itemprop'] = 'acceptedAnswer';
	$content_atts['itemtype'] = 'https://schema.org/Answer';
	$content_h_atts['itemprop'] = 'text';
}

// Apply filters to title text
$title = us_replace_dynamic_value( $title );
$title = wptexturize( $title );

$btn_atts = array(
	'aria-controls' => 'content-' . $_atts['id'],
	'class' => 'w-tabs-section-header' . ( $active ? ' active' : '' ),
);

// If icon is set
if ( $icon ) {
	$btn_atts['class'] .= ' with_icon';
}

// Add specific attributes for opening sections on AMP without JS
if ( us_amp() ) {
	$btn_atts['id'] = 'btn-' . $_atts['id'];
	$btn_atts['on'] = 'tap:' . $_atts['id'] . '.toggleClass(class="active",force=true)';

	foreach ( $us_tabs_atts as $amp_id ) {
		if ( $amp_id['el_id'] == $_atts['id'] ) {
			continue;
		}
		$btn_atts['on'] .= ',' . $amp_id['el_id'] . '.toggleClass(class="active",force=false)';
	}
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . $inline_css . '>';
$output .= '<button' . us_implode_atts( $btn_atts ) . '>';
if ( $icon AND $i_position == 'left' ) {
	$output .= us_prepare_icon_tag( $icon );
}
$output .= '<' . $title_tag . us_implode_atts( $title_atts ) . '>' . $title . '</' . $title_tag . '>';
if ( $icon AND $i_position == 'right' ) {
	$output .= us_prepare_icon_tag( $icon );
}
$output .= '<div class="w-tabs-section-control"></div>';
$output .= '</button>';
$output .= '<div' . us_implode_atts( $content_atts ) . '>';
$output .= '<div' . us_implode_atts( $content_h_atts ) . '>' . $content_html . '</div>';
$output .= '</div>';
$output .= '</div>';

echo $output;
