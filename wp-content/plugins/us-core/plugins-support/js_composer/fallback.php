<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Add filters here for shortcodes editing windows, to move deprecated attributes values to the new ones
 */

// Row
add_filter( 'vc_edit_form_fields_attributes_vc_row', 'us_vc_edit_form_fields_attributes_vc_row', 710 );
function us_vc_edit_form_fields_attributes_vc_row( $atts ) {

	// Shape Divider
	if (
		empty( $atts['us_shape_show_top'] )
		AND empty( $atts['us_shape_show_bottom'] )
		AND ! empty( $atts['us_shape'] )
		AND $atts['us_shape'] != 'none'
	) {
		$us_shape_position = ( ! empty( $atts['us_shape_position'] ) )
			? $atts['us_shape_position']
			: 'bottom';
		$atts[ 'us_shape_show_' . $us_shape_position ] = 1;
		$atts[ 'us_shape_' . $us_shape_position ] = $atts['us_shape'];

		if ( ! empty( $atts['us_shape_height'] ) ) {
			$atts[ 'us_shape_height_' . $us_shape_position ] = $atts['us_shape_height'];
		}
		if ( ! empty( $atts['us_shape_color'] ) ) {
			$atts[ 'us_shape_color_' . $us_shape_position ] = $atts['us_shape_color'];
		}
		if ( ! empty( $atts['us_shape_overlap'] ) ) {
			$atts[ 'us_shape_overlap_' . $us_shape_position ] = $atts['us_shape_overlap'];
		}
		if ( ! empty( $atts['us_shape_flip'] ) ) {
			$atts[ 'us_shape_flip_' . $us_shape_position ] = $atts['us_shape_flip'];
		}

		// Removing old shape divider params
		foreach ( array( 'us_shape', 'us_shape_height', 'us_shape_position', 'us_shape_color', 'us_shape_overlap', 'us_shape_flip' ) as $param_name ) {
			$atts[ $param_name ] = '';
		}
	}

	return $atts;
}

// Text
add_filter( 'vc_edit_form_fields_attributes_us_text', 'us_vc_edit_form_fields_attributes_us_text', 710 );
function us_vc_edit_form_fields_attributes_us_text( $atts ) {

	// Alignment
	if ( ! empty( $atts['align'] ) AND $atts['align'] != 'none' ) {
		if ( ! empty( $atts['css'] ) ) {
			$css_arr = json_decode( rawurldecode( $atts['css'] ), TRUE );
			if ( ! is_array( $css_arr ) ) {
				$css_arr = array();
			}
		} else {
			$css_arr = array();
		}
		if ( empty( $css_arr['default']['text-align'] ) ) {
			if ( ! isset( $css_arr['default'] ) ) {
				$css_arr['default'] = array();
			}
			$css_arr['default']['text-align'] = $atts['align'];
			$atts['css'] = rawurlencode( json_encode( $css_arr ) );
		}
		$atts['align'] = '';
	}

	return $atts;
}

// Interactive Banner
add_filter( 'vc_edit_form_fields_attributes_us_ibanner', 'us_vc_edit_form_fields_attributes_us_ibanner', 710 );
function us_vc_edit_form_fields_attributes_us_ibanner( $atts ) {

	// Alignment
	if ( ! empty( $atts['align'] ) AND ( $atts['align'] != 'left' OR is_rtl() ) ) {
		if ( ! empty( $atts['css'] ) ) {
			$css_arr = json_decode( rawurldecode( $atts['css'] ), TRUE );
			if ( ! is_array( $css_arr ) ) {
				$css_arr = array();
			}
		} else {
			$css_arr = array();
		}
		if ( empty( $css_arr['default']['text-align'] ) ) {
			if ( ! isset( $css_arr['default'] ) ) {
				$css_arr['default'] = array();
			}
			$css_arr['default']['text-align'] = $atts['align'];
			$atts['css'] = rawurlencode( json_encode( $css_arr ) );
		}
		$atts['align'] = '';
	}

	return $atts;
}

// Simple Menu
add_filter( 'vc_edit_form_fields_attributes_us_additional_menu', 'us_vc_edit_form_fields_attributes_us_additional_menu', 710 );
function us_vc_edit_form_fields_attributes_us_additional_menu( $atts ) {

	// Alignment
	if ( ! empty( $atts['align'] ) AND ( $atts['align'] != 'left' OR is_rtl() ) ) {
		if ( ! empty( $atts['css'] ) ) {
			$css_arr = json_decode( rawurldecode( $atts['css'] ), TRUE );
			if ( ! is_array( $css_arr ) ) {
				$css_arr = array();
			}
		} else {
			$css_arr = array();
		}
		if ( empty( $css_arr['default']['text-align'] ) ) {
			if ( ! isset( $css_arr['default'] ) ) {
				$css_arr['default'] = array();
			}
			$css_arr['default']['text-align'] = $atts['align'];
			$atts['css'] = rawurlencode( json_encode( $css_arr ) );
		}
		$atts['align'] = '';
	}

	return $atts;
}

// Social Links
add_filter( 'vc_edit_form_fields_attributes_us_socials', 'us_vc_edit_form_fields_attributes_us_socials', 710 );
function us_vc_edit_form_fields_attributes_us_socials( $atts ) {

	// Color
	if ( ! empty( $atts['color'] ) AND $atts['align'] != 'brand' AND empty( $atts['icons_color'] ) ) {
		$atts['icons_color'] = $atts['color'];
		$atts['color'] = '';
	}

	// Alignment
	if ( ! empty( $atts['align'] ) AND ( $atts['align'] != 'left' OR is_rtl() ) ) {
		if ( ! empty( $atts['css'] ) ) {
			$css_arr = json_decode( rawurldecode( $atts['css'] ), TRUE );
			if ( ! is_array( $css_arr ) ) {
				$css_arr = array();
			}
		} else {
			$css_arr = array();
		}
		if ( empty( $css_arr['default']['text-align'] ) ) {
			if ( ! isset( $css_arr['default'] ) ) {
				$css_arr['default'] = array();
			}
			$css_arr['default']['text-align'] = $atts['align'];
			$atts['css'] = rawurlencode( json_encode( $css_arr ) );
		}
		$atts['align'] = '';
	}

	return $atts;
}

// US Grid
if ( ! function_exists( 'us_vc_edit_form_fields_attributes_us_grid' ) ) {
	/**
	 * @param array $atts
	 * @return array
	 */
	function us_vc_edit_form_fields_attributes_us_grid( $atts ) {
		foreach ( $atts as $key => $values ) {
			// Replacing taxonomy identifiers with slug
			if ( strpos( $key, 'taxonomy_' ) === 0 AND ! empty( $values ) ) {

				$values = explode( ',', $values );
				$taxonomy = substr( $key, strlen( 'taxonomy_' ) );

				foreach ( $values as &$value ) {
					if ( is_numeric( $value ) AND $term = get_term( (int) $value, $taxonomy ) ) {
						$value = $term->slug;
					}
				}
				unset( $value );

				$atts[ $key ] = implode( ',', $values );
			}
		}
		return $atts;
	}
	add_filter( 'vc_edit_form_fields_attributes_us_grid', 'us_vc_edit_form_fields_attributes_us_grid', 710 );
}
