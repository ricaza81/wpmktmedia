<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

// TODO move this file to admin/functions after merge into dev branch, also add if_function_exists wrappings
/**
 * Add filters here for shortcodes editing windows, to move deprecated attributes values to the new ones
 * NOTE: `usb_fallback_atts_{shortcode}` used to prepare the output of content in USBuilder.
 */

if ( ! function_exists( 'us_vc_row_columns_fallback_helper' ) ) {
	function us_vc_row_columns_fallback_helper( $shortcode_base, $content ) {
		$result_atts = array();
		$preg_patern = ( $shortcode_base == 'vc_row_inner' )
			? '~\[vc_column_inner(([^\]]+)?)~'
			: '~\[vc_column((?!\_)([^\]]+)?)~'; // Using (?!\_) in regex to prevent matching vc_column_inner shortcode

		$_cols_widths = array();

		if ( preg_match_all( $preg_patern, $content, $_cols_matches ) ) {
			foreach ( $_cols_matches[0] as $key => $_cols_match ) {
				$_col_width = '1/1'; // default value

				if ( ! empty( $_cols_matches[1][ $key ] ) ) {
					$_col_atts = shortcode_parse_atts( $_cols_matches[1][ $key ] );

					$_col_width = isset( $_col_atts['width'] ) ? $_col_atts['width'] : $_col_width;
				}

				$_cols_widths[] = $_col_width;
			}
		}

		// If all columns are equal, use the denominator of the first column width
		// Example: 1/2 + 1/2 becomes 2
		// Example: 1/1 + 1/1 + 1/1 becomes 1
		// Example: 1/3 + 1/3 + 1/3 becomes 3
		// Example: 1/2 + 1/2 + 1/2 + 1/2 becomes 2
		if ( count( array_unique( $_cols_widths ) ) === 1 ) {
			$result_atts['columns'] = substr( $_cols_widths[0], 2 );

			// If TWO columns widths have the same denominator and differ numerators, combine numerators via "-" sign
			// Example: 1/3 + 2/3 becomes 1-2
			// Example: 2/5 + 3/5 becomes 2-3
			// Example: 1/4 + 1/2 becomes custom
		} elseif ( count( $_cols_widths ) === 2 ) {
			if ( substr( $_cols_widths[0], 2 ) === substr( $_cols_widths[1], 2 ) ) {
				$result_atts['columns'] = substr( $_cols_widths[0], 0, 1 ) . '-' . substr( $_cols_widths[1], 0, 1 );
			} else {
				$result_atts['columns'] = 'custom';
			}

			// Predefined value 1/4 + 1/2 + 1/4
		} elseif ( $_cols_widths === array( '1/4', '1/2', '1/4' ) ) {
			$result_atts['columns'] = '1-2-1';

			// Predefined value 1/5 + 3/5 + 1/5
		} elseif ( $_cols_widths === array( '1/5', '3/5', '1/5' ) ) {
			$result_atts['columns'] = '1-3-1';

			// Predefined value 1/6 + 2/3 + 1/6
		} elseif ( $_cols_widths === array( '1/6', '2/3', '1/6' ) ) {
			$result_atts['columns'] = '1-4-1';

			// If THREE or more columns have differ width, force the "custom" value
			// Example: 1/6 + 2/6 + 3/6 becomes 1fr 2fr 3fr
			// Example: 1/6 + 2/12 + 1/4 + 5/12 becomes 1fr 1fr 1.5fr 2.5fr
		} else {
			$result_atts['columns'] = 'custom';
			$result_atts['columns_layout'] = '';

			foreach ( $_cols_widths as $_width ) {
				$fr = explode( '/', $_width );

				$result_atts['columns_layout'] .= round( (int) $fr[0] / (int) $fr[1] * 6, 1 ) . 'fr ';
			}
		}

		return $result_atts;
	}
}

// Row
add_filter( 'vc_edit_form_fields_attributes_vc_row', 'us_fallback_atts_vc_row', 710, 1 );
add_filter( 'usb_fallback_atts_vc_row', 'us_fallback_atts_vc_row', 710, 2 );
function us_fallback_atts_vc_row( $atts, $content = '' ) {

	// Shape Divider (after version 7.1)
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

	// Row Height (after version 8.0)
	if ( ! empty( $atts['height'] ) AND $atts['height'] == 'full' ) {
		$atts['full_height'] = '1';
		$atts['height'] = 'medium';

		if ( ! empty( $atts['valign'] ) ) {
			$atts['v_align'] = $atts['valign'];
			$atts['valign'] = '';
		} else {
			$atts['v_align'] = 'top';
		}
	}

	// Columns gap, if NEW columns layout is enabled (after version 8.0)
	if ( us_get_option( 'live_builder' ) AND us_get_option( 'grid_columns_layout' ) AND empty( $atts['columns_gap'] ) ) {
		if ( ! empty( $atts['columns_type'] ) ) {

			if ( ! empty( $atts['gap'] ) AND preg_match( '~^(\d*\.?\d*)(.*)$~', $atts['gap'], $matches ) ) {
				$atts['columns_gap'] = ( $matches[1] * 2 ) . $matches[2];
			} else {
				$atts['columns_gap'] = '0rem';
			}

		} elseif ( ! empty( $atts['gap'] ) ) {

			// Avoid "calc" in the value
			if ( strpos( $atts['gap'], 'rem' ) !== FALSE ) {
				$atts['columns_gap'] = ( 3 + (float) $atts['gap'] ) . 'rem';
			} elseif ( strpos( $atts['gap'], 'px' ) !== FALSE ) {
				$atts['columns_gap'] = ( 48 + (int) $atts['gap'] ) . 'px';
			}
			$atts['gap'] = '';
		}
	}

	return $atts;
}

// Row in US Builder
add_filter( 'usb_fallback_atts_vc_row', 'usb_fallback_vc_row', 800, 2 );
function usb_fallback_vc_row( $atts, $content = '' ) {
	$columns_fallback_result = us_vc_row_columns_fallback_helper( 'vc_row', $content );

	if (
		(
			empty( $atts['columns'] )
			OR $atts['columns'] === '1'
		)
		AND ! empty( $columns_fallback_result['columns'] )
	) {
		$atts['columns'] = $columns_fallback_result['columns'];
	}
	if ( ! empty( $columns_fallback_result['columns_layout'] ) ) {
		$atts['columns_layout'] = $columns_fallback_result['columns_layout'];
	}

	return $atts;
}

// Inner Row in US Builder
add_filter( 'usb_fallback_atts_vc_row_inner', 'usb_fallback_vc_row_inner', 800, 2 );
function usb_fallback_vc_row_inner( $atts, $content = '' ) {
	$columns_fallback_result = us_vc_row_columns_fallback_helper( 'vc_row_inner', $content );

	if (
		(
			empty( $atts['columns'] )
			OR $atts['columns'] === '1'
		)
		AND ! empty( $columns_fallback_result['columns'] )
	) {
		$atts['columns'] = $columns_fallback_result['columns'];
	}
	if ( ! empty( $columns_fallback_result['columns_layout'] ) ) {
		$atts['columns_layout'] = $columns_fallback_result['columns_layout'];
	}

	return $atts;
}

// Column
add_filter( 'vc_edit_form_fields_attributes_vc_column', 'us_fallback_atts_vc_column', 710, 1 );
add_filter( 'usb_fallback_atts_vc_column', 'us_fallback_atts_vc_column', 710, 2 );
add_filter( 'vc_edit_form_fields_attributes_vc_column_inner', 'us_fallback_atts_vc_column', 710, 2 );
add_filter( 'usb_fallback_atts_vc_column_inner', 'us_fallback_atts_vc_column', 710, 2 );
function us_fallback_atts_vc_column( $atts, $content = '' ) {

	// Animation (after version 8.0)
	if ( ! empty( $atts['animate'] ) ) {
		if ( ! empty( $atts['css'] ) ) {
			$css_arr = json_decode( rawurldecode( $atts['css'] ), TRUE );
			if ( ! is_array( $css_arr ) ) {
				$css_arr = array();
			}
		} else {
			$css_arr = array();
		}
		if ( empty( $css_arr['default']['animation-name'] ) ) {
			if ( ! isset( $css_arr['default'] ) ) {
				$css_arr['default'] = array();
			}
			$css_arr['default']['animation-name'] = $atts['animate'];

			if ( ! empty( $atts['animate_delay'] ) ) {
				$css_arr['default']['animation-delay'] = (float) $atts['animate_delay'] . 's';
			}

			$atts['css'] = rawurlencode( json_encode( $css_arr ) );
		}

		$atts['animate'] = '';
		$atts['animate_delay'] = '';
	}

	return $atts;
}

// TTA Section
add_filter( 'vc_edit_form_fields_attributes_vc_tta_section', 'us_fallback_atts_vc_tta_section', 710, 1 );
add_filter( 'usb_fallback_atts_vc_tta_section', 'us_fallback_atts_vc_tta_section', 710, 2 );
function us_fallback_atts_vc_tta_section( $atts, $content = '' ) {

	// Tab ID (after version 8.0)
	if ( ! empty( $atts['tab_id'] ) ) {
		$atts['el_id'] = $atts['tab_id'];
		$atts['tab_id'] = '';
	}

	return $atts;
}

// Image
add_filter( 'vc_edit_form_fields_attributes_us_image', 'us_fallback_atts_us_image', 710, 1 );
add_filter( 'usb_fallback_atts_us_image', 'us_fallback_atts_us_image', 710, 2 );
function us_fallback_atts_us_image( $atts, $content = '' ) {

	// Animation (after version 8.0)
	if ( ! empty( $atts['animate'] ) ) {
		if ( ! empty( $atts['css'] ) ) {
			$css_arr = json_decode( rawurldecode( $atts['css'] ), TRUE );
			if ( ! is_array( $css_arr ) ) {
				$css_arr = array();
			}
		} else {
			$css_arr = array();
		}
		if ( empty( $css_arr['default']['animation-name'] ) ) {
			if ( ! isset( $css_arr['default'] ) ) {
				$css_arr['default'] = array();
			}
			$css_arr['default']['animation-name'] = $atts['animate'];

			if ( ! empty( $atts['animate_delay'] ) ) {
				$css_arr['default']['animation-delay'] = (float) $atts['animate_delay'] . 's';
			}

			$atts['css'] = rawurlencode( json_encode( $css_arr ) );
		}

		$atts['animate'] = '';
		$atts['animate_delay'] = '';
	}

	return $atts;
}

// Text
add_filter( 'vc_edit_form_fields_attributes_us_text', 'us_fallback_atts_us_text', 710, 1 );
add_filter( 'usb_fallback_atts_us_text', 'us_fallback_atts_us_text', 710, 2 );
function us_fallback_atts_us_text( $atts, $content = '' ) {

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

// Pricing Table
add_filter( 'vc_edit_form_fields_attributes_us_pricing', 'us_fallback_atts_us_pricing', 810, 1 );
add_filter( 'usb_fallback_atts_us_pricing', 'us_fallback_atts_us_pricing', 810, 2 );
function us_fallback_atts_us_pricing( $atts, $content = '' ) {

	// Type
	if ( ! empty( $atts['items'] ) ) {
		$items = (array) json_decode( urldecode( $atts['items'] ), TRUE );
		foreach ( $items as &$item ) {
			if ( ! isset( $item['type'] ) ) {
				continue;
			}
			if ( $item['type'] === 'featured' ) {
				$item['type'] = '1';
			}
		}
		unset( $item );
		$atts['items'] = rawurlencode( json_encode( $items ) );
	}

	return $atts;
}

// Interactive Banner
add_filter( 'vc_edit_form_fields_attributes_us_ibanner', 'us_fallback_atts_us_ibanner', 710, 1 );
add_filter( 'usb_fallback_atts_us_ibanner', 'us_fallback_atts_us_ibanner', 710, 2 );
function us_fallback_atts_us_ibanner( $atts, $content = '' ) {

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

// Search
add_filter( 'vc_edit_form_fields_attributes_us_search', 'us_fallback_atts_us_search', 710, 1 );
add_filter( 'usb_fallback_atts_us_search', 'us_fallback_atts_us_search', 710, 2 );
function us_fallback_atts_us_search( $atts, $content = '' ) {

	// Search Shop Products only
	if ( ! empty( $atts['product_search'] ) ) {
		$atts['search_post_type'] = 'product';
		$atts['product_search'] = '';
	}

	return $atts;
}

// Simple Menu
add_filter( 'vc_edit_form_fields_attributes_us_additional_menu', 'us_fallback_atts_us_additional_menu', 710, 1 );
add_filter( 'usb_fallback_atts_us_additional_menu', 'us_fallback_atts_us_additional_menu', 710, 2 );
function us_fallback_atts_us_additional_menu( $atts, $content = '' ) {

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
add_filter( 'vc_edit_form_fields_attributes_us_socials', 'us_fallback_atts_us_socials', 710, 1 );
add_filter( 'usb_fallback_atts_us_socials', 'us_fallback_atts_us_socials', 710, 2 );
function us_fallback_atts_us_socials( $atts, $content = '' ) {

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

// VC Raw HTML -> US Custom HTML (change the shortcode name only)
add_filter( 'usb_fallback_name_vc_raw_html', 'usb_fallback_name_vc_raw_html', 710, 0 );
function usb_fallback_name_vc_raw_html() {
	return 'us_html';
}

// US Grid
if ( ! function_exists( 'us_fallback_atts_us_grid' ) ) {
	add_filter( 'vc_edit_form_fields_attributes_us_grid', 'us_fallback_atts_us_grid', 710, 1 );
	add_filter( 'usb_fallback_atts_us_grid', 'us_fallback_atts_us_grid', 710, 2 );
	/**
	 * @param array $atts
	 * @return array
	 */
	function us_fallback_atts_us_grid( $atts, $content = '' ) {

		// Order by (after version 7.11)
		if ( isset( $atts['orderby'] ) AND $atts['orderby'] == 'alpha' ) {
			$atts['orderby'] = 'title';
		}

		// Check for cases when the grid was added without any atts
		if ( ! is_array( $atts ) ) {
			return $atts;
		}
		foreach ( $atts as $key => $values ) {

			// Replace taxonomy identifiers with slug
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
}
