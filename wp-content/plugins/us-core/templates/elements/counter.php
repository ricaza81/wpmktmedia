<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Counter
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 */

$_atts['class'] = 'w-counter';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' color_' . $color;
$_atts['class'] .= ' align_' . $align;

// When some values are set in Design options, add the specific classes
if ( us_design_options_has_property( $css, 'font-size' ) ) {
	$_atts['class'] .= ' has_font_size';
}
if ( us_design_options_has_property( $css, 'color' ) ) {
	$_atts['class'] .= ' has_text_color';
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}
// If we are in WPB front end editor mode, make sure the counter has an ID
if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() AND empty( $_atts['id'] ) ) {
	$_atts['id'] = us_uniqid();
}

$_atts['data-duration'] = (int) $duration * 1000;

// Generate inline styles for Value
$value_inline_css = us_prepare_inline_css(
	array(
		'color' => ( $color == 'custom' )
			? us_get_color( $custom_color )
			: '',
	)
);
$title_inline_css = us_prepare_inline_css(
	array(
		'font-size' => $title_size,
	)
);

// Check for custom fields
$final = us_replace_dynamic_value( $final );

// Finding numbers positions in both initial and final strings
$pos = array();
foreach ( array( 'initial', 'final' ) as $key ) {
	$pos[ $key ] = array();
	// In this array we'll store the string's character number, where primitive changes from letter to number or back
	preg_match_all( '~(\(\-?\d+([\.,\'· ]\d+)*\))|(\-?\d+([\.,\'· ]\d+)*)~u', $$key, $matches, PREG_OFFSET_CAPTURE );
	foreach ( $matches[0] as $match ) {
		/**
		 * preg_* functions are not multi-byte encodings friendly,
		 * so instead of direct usage of position captured by preg_match_all,
		 * get the string part from start to the position first and then measure its length with multi-byte function
		 */
		$pos[ $key ][] = mb_strlen( substr( $$key, 0, $match[1] ) );
		$pos[ $key ][] = $match[1] + mb_strlen( $match[0] );
	}
};

// Making sure we have the equal number of numbers in both strings
if ( count( $pos['initial'] ) != count( $pos['final'] ) ) {
	// Not-paired numbers will be treated as letters
	if ( count( $pos['initial'] ) > count( $pos['final'] ) ) {
		$pos['initial'] = array_slice( $pos['initial'], 0, count( $pos['final'] ) );
	} else/*if ( count( $positions['initial'] ) < count( $positions['final'] ) )*/ {
		$pos['final'] = array_slice( $pos['final'], 0, count( $pos['initial'] ) );
	}
}

// Position boundaries
foreach ( array( 'initial', 'final' ) as $key ) {
	array_unshift( $pos[ $key ], 0 );
	$pos[ $key ][] = mb_strlen( $$key );
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= '<div class="w-counter-value"' . $value_inline_css . '>';

// Output the final value on AMP or Builder preview
if ( us_amp() ) {
	$output .= '<span class="w-counter-value-part">' . $final . '</span>';

} else {
	// Determining if we treat each part as a number or as a letter combination
	for ( $index = 0, $length = count( $pos['initial'] ) - 1; $index < $length; $index++ ) {
		$part_type = ( $index % 2 ) ? 'number' : 'text';
		$part_initial = mb_substr( $initial, $pos['initial'][ $index ], $pos['initial'][ $index + 1 ] - $pos['initial'][ $index ] );
		$part_final = mb_substr( $final, $pos['final'][ $index ], $pos['final'][ $index + 1 ] - $pos['final'][ $index ] );
		$output .= '<span class="w-counter-value-part type_' . $part_type . '" data-final="' . esc_attr( $part_final ) . '">' . $part_initial . '</span>';
	}
}

$output .= '</div>';

if ( ! empty( $title ) ) {

	// Apply filters to title
	$title = us_replace_dynamic_value( $title );
	$title = wptexturize( $title );

	$output .= '<' . $title_tag . ' class="w-counter-title"' . $title_inline_css . '>' . $title . '</' . $title_tag . '>';
}
$output .= '</div>';

// If we are in WPB front end editor mode, apply JS to the counter
if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) {
	$output .= '<script>
	jQuery( function( $ ) {
		if ( typeof $us !== "undefined" && typeof $.fn.wCounter === "function" ) {
			var $elm = jQuery( "#' . $_atts['id'] . '" );
			if ( $elm.data( "wCounter" ) === undefined ) {
				$elm.wCounter();
			}
		}
	} );
	</script>';
}

echo $output;
