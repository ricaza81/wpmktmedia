<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Counter
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 */


$classes = isset( $classes ) ? $classes : '';
$classes .= ' color_' . $color;
$classes .= ' align_' . $align;

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$classes .= ' has_text_color';
}

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

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

// Finding numbers positions in both initial and final strings
$pos = array();
foreach ( array( 'initial', 'final' ) as $key ) {
	$pos[ $key ] = array();
	// In this array we'll store the string's character number, where primitive changes from letter to number or back
	preg_match_all( '~(\(\-?\d+([\.,\'· ]\d+)*\))|(\-?\d+([\.,\'· ]\d+)*)~u', $$key, $matches, PREG_OFFSET_CAPTURE );
	foreach ( $matches[0] as $match ) {
		$pos[ $key ][] = $match[1];
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
$output = '<div class="w-counter' . $classes . '" data-duration="' . intval( $duration ) * 1000 . '"' . $el_id . '>';
$output .= '<div class="w-counter-value"' . $value_inline_css . '>';


if ( us_amp() ) {
	// Output only final value on AMP
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
	$output .= '<' . $title_tag .' class="w-counter-title"' . $title_inline_css . '>';
	$output .= wptexturize( $title );
	$output .= '</' . $title_tag . '>';
}
$output .= '</div>';

// If we are in front end editor mode, apply JS to logos
if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) {
	$output .= '<script>
	jQuery(function($){
		if (typeof $.fn.wCounter === "function") {
			jQuery(".w-counter").wCounter();
		}
	});
	</script>';
}

echo $output;
