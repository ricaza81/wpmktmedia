<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * TinyMCE Support
 *
 * @link https://www.tinymce.com
 */

function us_tiny_mce_color_pickers( $init ) {

	if ( defined( 'US_THEMENAME' ) ) {
		$palette = get_option( 'usof_color_palette_' . US_THEMENAME );

		$custom_colours = '';
		if ( ! empty( $palette ) AND is_array( $palette ) ) {
			foreach ( $palette as $color ) {
				$color = us_rgba2hex( $color );
				$color = substr( $color, 1 );
				if ( $color == '000000' OR $color == 'ffffff' ) {
					continue;
				}
				$custom_colours .= "\"$color\", \"#$color\",";
			}
		}
	}


	$default_colors = '
		"000000", "Black",
		"993300", "Burnt orange",
		"333300", "Dark olive",
		"003300", "Dark green",
		"003366", "Dark azure",
		"000080", "Navy Blue",
		"333399", "Indigo",
		"333333", "Very dark gray",
		"800000", "Maroon",
		"FF6600", "Orange",
		"808000", "Olive",
		"008000", "Green",
		"008080", "Teal",
		"0000FF", "Blue",
		"666699", "Grayish blue",
		"808080", "Gray",
		"FF0000", "Red",
		"FF9900", "Amber",
		"99CC00", "Yellow green",
		"339966", "Sea green",
		"33CCCC", "Turquoise",
		"3366FF", "Royal blue",
		"800080", "Purple",
		"999999", "Medium gray",
		"FF00FF", "Magenta",
		"FFCC00", "Gold",
		"FFFF00", "Yellow",
		"00FF00", "Lime",
		"00FFFF", "Aqua",
		"00CCFF", "Sky blue",
		"993366", "Red violet",
		"FFFFFF", "White",
		"FF99CC", "Pink",
		"FFCC99", "Peach",
		"FFFF99", "Light yellow",
		"CCFFCC", "Pale green",
		"CCFFFF", "Pale cyan",
		"99CCFF", "Light sky blue",
		"CC99FF", "Plum"
	';

	$init['textcolor_map'] = '[' . $custom_colours . $default_colors . ']';
	$init['textcolor_rows'] = 6;

	return $init;
}

add_filter( 'tiny_mce_before_init', 'us_tiny_mce_color_pickers' );
