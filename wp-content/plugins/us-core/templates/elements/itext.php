<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Interactive Text
 */

$_atts['class'] = 'w-itext';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' type_' . $animation_type;
$_atts['class'] .= ' align_' . $align;

// Reset some values, if part animation is disabled
if ( $disable_part_animation ) {
	$_atts['class'] .= ' disable_part_animation';

	$dynamic_bold = $html_spaces = FALSE;
	$dynamic_color = '';
}

if ( $dynamic_bold ) {
	$_atts['class'] .= ' dynamic_bold';
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Allows to use nbsps and other entities
$texts = html_entity_decode( $texts );
$texts_arr = explode( "\n", strip_tags( $texts ) );

$js_data = array(
	'duration' => (float) $duration * 1000,
	'delay' => (float) $delay * 1000,
	'disablePartAnimation' => ! ! $disable_part_animation,
);
if ( ! empty( $dynamic_color ) ) {
	$js_data['dynamicColor'] = us_get_color( $dynamic_color );
}

$groups = $group_map_changes = $group_map_unique = array();

if ( ! $disable_part_animation ) {

	// Getting words and their delimiters to work on this level of abstraction
	$_parts = $_parts_reverse = array();
	foreach ( $texts_arr as $index => $text ) {
		if ( preg_match_all( '~[\w\-]+|[^\w\-]+~u', $text, $matches ) ) {
			$_parts[ $index ] = $_parts_reverse[ $index ] = $matches[0];
		}
	}

	$_max_parts = count( max( $_parts ) );

	// Getting the whole set of parts with all the intermediate values (part_index => part_states)
	for ( $i = count( $_parts ) - 1; $i > - 1; $i -- ) {
		$empty_list = array_fill( 0, $_max_parts, ' ' );
		$_parts[ $i ] = $_parts[ $i ] + $empty_list;
		$empty_list = array_fill( 0, count( $_parts[ $i ] ) - count( $_parts_reverse[ $i ] ), ' ' );
		$_parts_reverse[ $i ] = array_merge( $empty_list, $_parts_reverse[ $i ] );
	}

	// Determine where fewer changes are and choose a smaller option
	$_part_changes = $_part_reverse_changes = 0;
	for ( $i = $_max_parts - 1; $i > - 1; $i -- ) {
		if ( count( array_unique( wp_list_pluck( $_parts, $i ) ) ) > 1 ) {
			$_part_changes ++;
		}
		if ( count( array_unique( wp_list_pluck( $_parts_reverse, $i ) ) ) > 1 ) {
			$_part_reverse_changes ++;
		}
	}
	$_parts = $_part_changes < $_part_reverse_changes
		? $_parts
		: $_parts_reverse;
	unset( $_part_reverse, $_part_changes, $_part_reverse_changes );

	// Grouping and receiving map changes
	for ( $i = count( max( $_parts ) ); $i > 0; $i -- ) {
		$groups[ $i ] = wp_list_pluck( $_parts, $i - 1 );
		$group_map_unique[ $i - 1 ] = count( array_unique( $groups[ $i ] ) );
		$group_map_changes[ $i - 1 ] = $group_map_unique[ $i - 1 ] > 1;
	}

	$groups = array_reverse( $groups );
	$group_map_changes = array_reverse( $group_map_changes );

} else {
	$groups = array( $texts_arr );
	$group_map_changes = array_fill( 0, count( $texts_arr ), TRUE );
	$animation_type = 'fadeIn';
}

$nbsp_char = html_entity_decode( '&nbsp;', ENT_COMPAT | ENT_HTML401, 'UTF-8' );
$js_data['html_nbsp_char'] = TRUE;
if ( empty( $html_spaces ) ) {
	$nbsp_char = ' ';
	$js_data['html_nbsp_char'] = FALSE;
}

// Adding spaces to words and
for ( $i = count( $groups ) - 1; $i > 0; $i -- ) {
	$is_empty = ! preg_replace( '/([\s]+)$/ui', '', implode( '', $groups[ $i ] ) );
	if ( $group_map_unique[ $i ] == 1 AND $is_empty ) {
		unset( $group_map_changes[ $i ] );
	}
	if ( isset( $groups[ $i - 1 ] ) AND $is_empty ) {
		foreach ( $groups[ $i - 1 ] as &$text ) {
			$text .= $nbsp_char;
		}
		unset( $text, $groups[ $i ] );
	}
}
unset( $group_map_unique );

// Reset indexes
$groups = array_values( $groups );
$group_map_changes = array_values( $group_map_changes );

// The combination of words that are near or all for printing
for ( $i = count( $groups ); $i > 0; $i -- ) {
	if (
		isset( $group_map_changes[ $i ], $group_map_changes[ $i - 1 ] )
		AND $group_map_changes[ $i ] === TRUE
		AND $group_map_changes[ $i - 1 ] === TRUE
		OR (
			$animation_type === 'typingChars'
			AND $group_map_changes[0] === TRUE
		)
	) {
		foreach ( $groups[ $i - 1 ] as $text_i => &$text ) {
			if ( isset( $groups[ $i ][ $text_i ] ) ) {
				$text .= $groups[ $i ][ $text_i ];
			}
		}
		unset( $text, $groups[ $i ], $group_map_changes[ $i ] );
	}
}

// Reset indexes
$groups = array_values( $groups );
$group_map_changes = array_values( $group_map_changes );
$group_keys = array_keys( $groups );

// The for spaces
foreach ( $groups[ end( $group_keys ) ] as &$text ) {

	// Replacing spaces with html entities
	$text = str_replace( ' ', $nbsp_char, $text );

	// Remove extra spaces from the end of a line
	$text = preg_replace( '/([\s]+)$/ui', '', $text );
}
unset( $text );

// Output the element
$output = '<' . $tag . us_implode_atts( $_atts );
if ( ! us_amp() ) {
	$output .= us_pass_data_to_js( $js_data );
}
$output .= '>';

foreach ( $groups as $index => $group ) {
	// Remove leading spaces in the first part
	if ( $index === 0 ) {
		$group = array_map( 'ltrim', $group );
	}
	ksort( $group );
	if ( empty( $group_map_changes[ $index ] ) ) {

		// Static part
		$output .= $group[0];
	} else {
		$_part_atts = array(
			'class' => 'w-itext-part',
		);

		// Delete all indents and spaces at the beginning of a line
		$group = array_map(
			function ( $text ) use ( $nbsp_char ) {
				// Remove ASCII spaces https://en.wikipedia.org/wiki/Non-breaking_space
				$text = preg_replace( '/^(\xc2\xa0)+/', '', $text );
				$text = preg_replace( '/^\s+/', '', $text );

				return $text;
			}, $group
		);

		// Animation classes (just in case site editor wants some custom styling for them)
		if ( ! empty( $group_map_changes[ $index ] ) AND ! us_amp() ) {
			for ( $i = 0; $i < count( array_unique( $groups[ $index ] ) ); $i ++ ) {
				$_part_atts['class'] .= ' changesat_' . $i;
			}
		}

		if ( ! empty( $group_map_changes[ $index ] ) ) {

			// Highlighting dynamic parts at start
			$_part_atts['class'] .= ' dynamic';
			if ( ! empty( $dynamic_color ) ) {
				$_part_atts['style'] = 'color:' . $dynamic_color;
			}
		}

		$text = preg_replace( '/\s+/', $nbsp_char, htmlentities( $group[0] ) );

		$output .= '<span' . us_implode_atts( $_part_atts );
		if ( ! us_amp() ) {
			$output .= us_pass_data_to_js( $group );
		}
		$output .= '>';
		$output .= '<span>' . $text . '</span>';

		if ( $animation_type === 'typingChars' AND $text && $text !== $nbsp_char ) {
			$output .= '<i class="w-itext-cursor"></i>';
		}
		$output .= '</span>';
	}
}
$output .= '</' . $tag . '>';

echo $output;
