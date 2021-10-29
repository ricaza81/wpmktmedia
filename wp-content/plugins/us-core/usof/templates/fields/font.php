<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Font
 *
 * Select font
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['only_google'] bool
 * @param $field ['preview'] array
 * @param $field ['preview']['text'] string Preview text
 * @param $field ['preview']['size'] string Font size in css format
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @var   $value array List of checked keys
 */

global $usof_options;

$output = '';
$font_value = explode( '|', $value, 2 );
if ( ! isset( $font_value[1] ) OR empty( $font_value[1] ) ) {
	$font_value[1] = '400,700';
}
$font_value[1] = explode( ',', $font_value[1] );

$output .= '<div class="usof-font">';

// Font preview
$field['preview'] = isset( $field['preview'] ) ? $field['preview'] : array();
$field['preview']['text'] = isset( $field['preview']['text'] ) ? $field['preview']['text'] : '0123456789 ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz';

$output .= '<div class="usof-font-preview" style="background: ' . $usof_options['color_content_bg'] . ';';

// Colors for Headings
$gradient_style = '';
if ( ! empty( $field['preview']['for_heading'] ) ) {
	if ( $color_value = $usof_options[ $field['preview']['color_field'] ] ) {

		// Show Headings color as gradient
		if ( strpos( $color_value, 'gradient' ) !== FALSE ) {
			$gradient_style .= ' style="';
			$gradient_style .= 'background-image:' . $color_value . ';';
			$gradient_style .= '-webkit-background-clip: text;';
			$gradient_style .= 'color: transparent;';
			$gradient_style .= '"';
		} else {
			$output .= 'color: ' . $color_value . ';';
		}

	} else {
		$output .= 'color: ' . $usof_options['color_content_heading'] . ';';
	}
} else {
	$output .= 'color: ' . $usof_options['color_content_text'] . ';';
}
if ( isset( $field['preview']['size_field'] ) AND ! empty( $usof_options[ $field['preview']['size_field'] ] ) ) {
	$output .= 'font-size: ' . $usof_options[ $field['preview']['size_field'] ] . ';';
}
if ( isset( $field['preview']['lineheight_field'] ) AND ! empty( $usof_options[ $field['preview']['lineheight_field'] ] ) ) {
	$output .= 'line-height: ' . $usof_options[ $field['preview']['lineheight_field'] ] . ';';
}
if ( isset( $field['preview']['letterspacing_field'] ) AND ! empty( $usof_options[ $field['preview']['letterspacing_field'] ] ) ) {
	$output .= 'letter-spacing: ' . $usof_options[ $field['preview']['letterspacing_field'] ] . ';';
}
if ( isset( $field['preview']['transform_field'] ) AND ! empty( $usof_options[ $field['preview']['transform_field'] ] ) ) {
	if ( strpos( $usof_options[ $field['preview']['transform_field'] ], 'uppercase' ) !== FALSE ) {
		$output .= 'text-transform: uppercase;';
	}
	if ( strpos( $usof_options[ $field['preview']['transform_field'] ], 'italic' ) !== FALSE ) {
		$output .= 'font-style: italic;';
	}
}
if ( isset( $field['preview']['weight_field'] ) AND ! empty( $usof_options[ $field['preview']['weight_field'] ] ) ) {
	$output .= 'font-weight: ' . $usof_options[ $field['preview']['weight_field'] ] . ';';
} elseif ( count( $font_value[1] ) > 0 ) {
	$output .= 'font-weight: ' . (int) $font_value[1][0] . ';';
}
$output .= '"><div' . $gradient_style . '>' . $field['preview']['text'] . '</div></div>';

$output .= '<input type="hidden" name="' . $name . '" value="' . esc_attr( $value ) . '" />';

// Get all fonts
$get_h1 = ( isset( $field['preview']['get_h1'] ) AND $field['preview']['get_h1'] );
$only_google = ( ! isset( $field['only_google'] ) OR ! $field['only_google'] );

$all_fonts = us_get_all_fonts( $only_google, $get_h1 );
// The number of options for the first part of the output or receive through autocomplete
$font_limit = ( isset( $field['font_limit'] ) AND (int) $field['font_limit'] ) ? $field['font_limit'] : 50;
$font_options = array();

// Get the set number of options
$i = 0;
$isset_value = FALSE;
foreach ( $all_fonts as $item_value => $item_name ) {
	if ( is_array( $item_name ) ) {
		foreach ( $item_name as $_item_value => $_item_name ) {
			$font_options[ $item_value ][ $_item_value ] = $_item_name;
			$i++;
			if ( ! empty( $font_value[0] ) AND $font_value[0] === $_item_name ) {
				$isset_value = TRUE;
			}
			if ( $i >= $font_limit ) {
				break 2;
			}
		}
	} else {
		$i++;
		$font_options[ $item_value ] = $item_name;
	}
	if ( $i >= $font_limit ) {
		break;
	}
}

// Add the selected font to the first part of the data.
if ( ! $isset_value AND ! empty( $font_value[0] ) ) {
	foreach ( $all_fonts as $item_value => $item_name ) {
		if ( is_array( $item_name ) ) {
			foreach ( $item_name as $_item_value => $_item_name ) {
				if ( $font_value[0] === $_item_name ) {
					$font_options[ $item_value ][ $_item_value ] = $_item_name;
					break 2;
				}
			}
		}
	}
}

// Check is not set any value, font has no name get_h1 and current font name not contains in fonts options
if ( ! $isset_value AND $font_value[0] !== 'get_h1' AND array_search( $font_value[0], array_column( $font_options, $font_value[0] ) ) === FALSE ) {
	$font_value[0] = 'none';
}
unset( $all_fonts, $isset_value );

$fonts_group_keys = array(
	'websafe' => __( 'Web safe font combinations (do not need to be loaded)', 'us' ),
	'google' => __( 'Google Fonts (loaded from Google servers)', 'us' ),
	'uploaded' => __( 'Uploaded Fonts', 'us' ),
);

// Field for getting font name through autocomplete
$output .= '<div class="type_autocomplete" data-name="font_name"'. us_pass_data_to_js( $fonts_group_keys ) .'>';
$output .= us_get_template( 'usof/templates/fields/autocomplete', array(
	'value' => $font_value[0],
	'field' => array(
		'settings' => array(
			'nonce_name' => 'usof_ajax_all_fonts_autocomplete',
			'action' => 'usof_all_fonts_autocomplete',
			'font_limit' => $font_limit,
			'get_h1' => $get_h1,
			'only_google' => $only_google,
		),
		'params_separator' => '|',
		'options' => $font_options,
	),
) );
$output .= '</div>';

// Font weights
if ( ! isset( $font_weights ) ) {
	$font_weights = array(
		'100' => '100 ' . __( 'thin', 'us' ),
		'100italic' => '100 ' . __( 'thin', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'200' => '200 ' . __( 'extra-light', 'us' ),
		'200italic' => '200 ' . __( 'extra-light', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'300' => '300 ' . __( 'light', 'us' ),
		'300italic' => '300 ' . __( 'light', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'400' => '400 ' . __( 'normal', 'us' ),
		'400italic' => '400 ' . __( 'normal', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'500' => '500 ' . __( 'medium', 'us' ),
		'500italic' => '500 ' . __( 'medium', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'600' => '600 ' . __( 'semi-bold', 'us' ),
		'600italic' => '600 ' . __( 'semi-bold', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'700' => '700 ' . __( 'bold', 'us' ),
		'700italic' => '700 ' . __( 'bold', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'800' => '800 ' . __( 'extra-bold', 'us' ),
		'800italic' => '800 ' . __( 'extra-bold', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'900' => '900 ' . __( 'ultra-bold', 'us' ),
		'900italic' => '900 ' . __( 'ultra-bold', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
	);
}
$show_weights = (array) us_config( 'google-fonts.' . $font_value[0] . '.variants', array() );

$output .= '<ul class="usof-checkbox-list">';
foreach ( $font_weights as $font_weight => $font_title ) {
	$font_weight = (string) $font_weight;
	$output .= '<li class="usof-checkbox' . ( in_array( $font_weight, $show_weights ) ? '' : ' hidden' ) . '" data-value="' . $font_weight . '">';
	$output .= '<label>';
	$output .= '<input type="checkbox" value="' . $font_weight . '"';
	$output .= ( array_search( $font_weight, $font_value[1], TRUE ) !== FALSE ? ' checked' : '' );
	$output .= '>';
	$output .= '<span class="usof-checkbox-text">';
	$output .= $font_title . '</span></label></li>';
}
$output .= '</ul>';

$font_style_fields_data = array();
if ( isset( $field['preview']['color_field'] ) ) {
	$font_style_fields_data['colorField'] = $field['preview']['color_field'];
}
if ( isset( $field['preview']['size_field'] ) ) {
	$font_style_fields_data['sizeField'] = $field['preview']['size_field'];
}
if ( isset( $field['preview']['lineheight_field'] ) ) {
	$font_style_fields_data['lineheightField'] = $field['preview']['lineheight_field'];
}
if ( isset( $field['preview']['weight_field'] ) ) {
	$font_style_fields_data['weightField'] = $field['preview']['weight_field'];
}
if ( isset( $field['preview']['letterspacing_field'] ) ) {
	$font_style_fields_data['letterspacingField'] = $field['preview']['letterspacing_field'];
}
if ( isset( $field['preview']['transform_field'] ) ) {
	$font_style_fields_data['transformField'] = $field['preview']['transform_field'];
}

$output .= '<div class="usof-font-style-fields-json"' . us_pass_data_to_js( $font_style_fields_data ) . '></div>';

$output .= '</div>';

echo $output;
