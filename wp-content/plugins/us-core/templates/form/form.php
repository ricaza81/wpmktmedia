<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a single form
 *
 * @var $type          string Form type: 'contact' / 'search' / 'comment' / 'protectedpost' / ...
 * @var $action        string Form action
 * @var $method        string Form method: 'post' / 'get'
 * @var $fields        array Form fields (see any of the fields template header for details)
 * @var $json_data     array Json data to pass to JavaScript
 * @var $classes       string Additional classes to append to form
 * @var $start_html    string HTML to append to the form's start
 * @var $end_html      string HTML to append to the form's end
 *
 * @action Before the template: 'us_before_template:templates/form/form'
 * @action After the template:  'us_after_template:templates/form/form'
 * @filter Template variables:  'us_template_vars:templates/form/form'
 */

$fields = isset( $fields ) ? (array) $fields : array();
$start_html = isset( $start_html ) ? $start_html : '';
$end_html = isset( $end_html ) ? $end_html : '';

// Repeatable fields IDs start from 1
$repeatable_fields = array(
	'text' => 1,
	'email' => 1,
	'textarea' => 1,
	'select' => 1,
	'agreement' => 1,
	'checkboxes' => 1,
	'radio' => 1,
	'date' => 1,
);

foreach ( $fields as $field_name => $field ) {
	if ( isset( $field['type'] ) ) {
		$fields[ $field_name ]['type'] = $field['type'];
		if ( in_array( $field['type'], array_keys( $repeatable_fields ) ) ) {
			$fields[ $field_name ]['field_id'] = $repeatable_fields[ $field['type'] ];
			$repeatable_fields[ $field['type'] ] += 1;
		}

		// Define if the form has Date field type
		if ( $field['type'] == 'date' ) {
			$has_date_field = TRUE;
		}
	} else {
		$fields[ $field_name ]['type'] = 'text';
	}
}

// Add param to existing data, if set
if ( ! empty( $json_data ) AND is_array( $json_data ) ) {
	$json_data['ajaxurl'] = admin_url( 'admin-ajax.php' );
} else {
	$json_data = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
	);
}

global $us_cform_index;

$_atts = array(
	'class' => 'w-form',
	'autocomplete' => 'off',
	'action' => isset( $action ) ? $action : site_url( $_SERVER['REQUEST_URI'] ),
	'method' => isset( $method ) ? $method : 'post',
);
if ( ! empty( $classes ) ) {
	$_atts['class'] .= ' ' . $classes;
}
if ( ! empty( $type ) ) {
	$_atts['class'] .= ' for_' . $type;
}
if ( ! empty( $us_cform_index ) ) {
	$_atts['class'] .= ' us_form_' . $us_cform_index;
}

// Fallback for forms without layout class
if ( strpos( $_atts['class'], 'layout_' ) === FALSE ) {
	$_atts['class'] .= ' layout_ver';
}

// Set CSS inline var for gap between fields
if ( ! us_amp() AND isset( $fields_gap ) AND trim( $fields_gap ) != '1rem' ) {
	$_atts['style'] = '--fields-gap:' . $fields_gap;
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Add AMP related attributes
if ( us_amp() ) {
	$_atts['action'] = $json_data['ajaxurl'];
	$_atts['custom-validation-reporting'] = 'show-all-on-submit';
}

// Pass the URL to load jquery-ui-datepicker via AJAX
if ( ! empty( $has_date_field ) AND us_get_option( 'ajax_load_js', 0 ) ) {
	$_atts['data-jquery-ui'] = includes_url( 'js/jquery/ui/datepicker.min.js' );

	// Use part of wp_localize_jquery_ui_datepicker(), because the script included via AJAX
	global $wp_locale;

	// Pass localized date picker info to JS
	$json_data['jquery-ui-locale'] = array(
		'monthNames' => array_values( $wp_locale->month ),
		'monthNamesShort' => array_values( $wp_locale->month_abbrev ),
		'nextText' => us_translate( 'Next' ),
		'prevText' => us_translate( 'Previous' ),
		'dayNames' => array_values( $wp_locale->weekday ),
		'dayNamesShort' => array_values( $wp_locale->weekday_abbrev ),
		'dayNamesMin' => array_values( $wp_locale->weekday_initial ),
		'firstDay' => absint( get_option( 'start_of_week' ) ),
		'isRTL' => $wp_locale->is_rtl(),
	);
}

// Output the form
echo '<form' . us_implode_atts( $_atts ) . '>';
echo '<div class="w-form-h">';
echo $start_html;
foreach ( $fields as $field ) {
	us_load_template( 'templates/form/' . $field['type'], $field );
}
echo $end_html;
echo '</div>';
echo '<div class="w-form-message"></div>';
if ( ! us_amp() ) {
	echo '<div class="w-form-json hidden"' . us_pass_data_to_js( $json_data ) . '></div>';
}
echo '</form>';
