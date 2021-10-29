<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Autocomplete
 *
 * @var $field array All passed parameters for the field
 * @var $field ['options'] array Initial Parameter List
 * @var $field ['ajax_query_args'] strung  Parameters to be passed in Ajax request
 * @var $field ['is_multiple'] boolean Multi Select Support
 * @var $field ['is_sortable'] boolean Drag and drop
 * @var $field ['params_separator'] string value params separator
 * @var $field ['classes'] string field container classes
 * @var $field ['settings'] array of settings for backend calls
 *
 * The Visual composer
 * @var $name string The name field
 * @var $value string The value of the selected parameters
 */

// For edit mode in USBuilder, these parameters are enabled


$name = isset( $name ) ? $name : '';
$value = isset( $value ) ? $value : '';

// Default field params values
$classes = isset( $field['classes'] ) ? $field['classes'] : '';
$multiple = isset( $field['is_multiple'] ) ? $field['is_multiple'] : FALSE;
$sortable = isset( $field['is_sortable'] ) ? $field['is_sortable'] : FALSE;
$params_separator = isset( $field['params_separator'] ) ? $field['params_separator'] : ',';
$options = isset( $field['options'] ) ? $field['options'] : array();

// Additional parameters that may be needed in Ajax request
$ajax_query_args = array();
// List of possible args
$possible_ajax_query_args = array(
	'action', // action name for backend
	'slug', // post type slug or taxonomy slug
	// Following args used in font autocomplete only
	'font_limit',
	'get_h1',
	'only_google'
);
// Adding only those ajax query args that are explicitly set in settings
foreach ( $possible_ajax_query_args as $arg ) {
	if ( isset( $field['settings'][ $arg ] ) ) {
		$ajax_query_args[ $arg ] = $field['settings'][ $arg ];
	}
}
// Separately adding nonce to ajax args, since it requires the function call
if ( isset( $field['settings']['nonce_name'] ) ) {
	$ajax_query_args['_nonce'] = wp_create_nonce( $field['settings']['nonce_name'] );
}

/**
 * Create options list
 *
 * @param array $options The options
 * @param int $level
 * @return string
 */
$func_create_options_list = function ( $options ) use ( &$func_create_options_list ) {
	$output = '';
	foreach ( $options as $value => $name ) {
		if ( is_array( $name ) ) {
			$output .= '<div class="usof-autocomplete-list-group" data-group="' . esc_attr( $value ) . '">';
			$output .= $func_create_options_list( $name );
			$output .= '</div>';
		} else {
			$atts = array(
				'data-value' => esc_attr( $value ),
				'data-text' => esc_attr( str_replace( ' ', '', strtolower( $name ) ) ),
				'tabindex' => '3',
			);
			$output .= '<div' . us_implode_atts( $atts ) . '>' .  $name . '</div>';
		}
	}

	return $output;
};

// If the site uses translations, then we will transfer the current language
if ( has_filter( 'us_tr_current_language' ) ) {
	$ajax_query_args['lang'] = apply_filters( 'us_tr_current_language', NULL );
}

// Export settings
$export_settings = array(
	'ajax_query_args' => $ajax_query_args,
	'multiple' => $multiple,
	'sortable' => $sortable,
	'no_results_found' => us_translate( 'No results found.' ),
	'params_separator' => $params_separator,
);

// Input atts
$atts = array(
	'type' => 'hidden',
	'class' => 'usof-autocomplete-value ' . esc_attr( $classes ),
	// removing unwanted spaces, so value is passed to the input tag without errors
	'value' => str_replace( $params_separator . ' ', $params_separator, $value ),
);

// Additional options to support Visual Composer
if ( strpos( $classes, 'wpb_vc_param_value' ) !== FALSE ) {
	$atts[ 'name' ] = esc_attr( $name );
}

// Output HTML
$output = '<div class="usof-autocomplete"' . us_pass_data_to_js( $export_settings ) . '>';
$output .= '<input' . us_implode_atts( $atts ) . '>';
$output .= '<div class="usof-autocomplete-toggle">';
$output .= '<div class="usof-autocomplete-options">';
$output .= '<input type="text" autocomplete="off" placeholder="' . us_translate_x( 'Search &hellip;', 'placeholder' ) . '" tabindex="2">';
$output .= '</div>';
$output .= '<div class="usof-autocomplete-list">' . $func_create_options_list( $options ) . '</div>';
$output .= '<div class="usof-autocomplete-message hidden"></div>';
$output .= '</div>';
$output .= '<div style="display: none;">';
ob_start();
$output .= ob_get_clean();
$output .= '</div>';
$output .= '</div>';

echo $output;
