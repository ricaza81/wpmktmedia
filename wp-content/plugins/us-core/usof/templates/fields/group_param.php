<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Group
 *
 * Grouped options
 *
 * @var   $field array Group options
 * @var   $params_values array Group values
 *
 */

global $usof_options;

$output = '<div class="usof-form-group-item">';
$output .= '<style></style>';
$group_content_styles = '';

// Output group title block, if "is_accordion" is set
if ( ! empty( $field['is_accordion'] ) ) {
	$group_content_styles = ' style="display:none;"';

	$param_title = ! empty( $field['title'] ) ? $field['title'] : '';
	foreach ( $field['params'] as $param_name => $param ) {
		if ( strpos( $param_title, '{{' . $param_name . '}}' ) !== FALSE ) {
			$param_value = isset( $params_values[ $param_name ] ) ? $params_values[ $param_name ] : $field['params'][ $param_name ]['std'];
			$param_value = esc_attr( trim( $param_value ) );
			$param_title = str_replace( '{{' . $param_name . '}}', $param_value, $param_title );
		}
	}

	$output .= '<div class="usof-form-group-item-title">';

	// Output Button preview, if preview attribute is set as "button"
	if ( isset( $field['preview'] ) AND $field['preview'] == 'button' ) {
		$output .= '<div class="usof-btn-preview hov_fade">';
		$output .= '<div class="usof-btn"><span class="usof-btn-label">' . strip_tags( $param_title ) . '</span></div>';
		$output .= '</div>';
	} else {
		$output .= $param_title;
	}
	$output .= '</div>';

} elseif ( isset( $field['preview'] ) AND $field['preview'] == 'input_fields' ) {

	// Output Input Fields preview, if preview attribute is set as "input_fields"
	$output .= '<div class="usof-input-preview" style="background: ' . $usof_options['color_content_bg'] . '">';
	$output .= '<input class="usof-input-preview-elm" type="text"';
	$output .= ' value="' . esc_attr( us_translate( 'Text' ) . ' ' . __( '(single line)', 'us' ) ) . '"';
	$output .= ' placeholder="' . esc_attr( us_translate( 'Text' ) . ' ' . __( '(single line)', 'us' ) ) . '">';
	$output .= '<div class="usof-input-preview-select">';
	$output .= '<select class="usof-input-preview-elm">';
	$output .= '<option>' . __( 'Dropdown', 'us' ) . '</option>';
	$output .= '<option>' . __( 'Dropdown', 'us' ) . ' 2</option>';
	$output .= '<option>' . __( 'Dropdown', 'us' ) . ' 3</option>';
	$output .= '</select>';
	$output .= '</div></div>';
}

// Output group content block
$output .= '<div class="usof-form-group-item-content"' . $group_content_styles . '>';
ob_start();
foreach ( $field['params'] as $param_name => $param ) {
	us_load_template(
		'usof/templates/field', array(
			'name' => $param_name,
			'id' => 'usof_' . $param_name,
			'field' => $param,
			'values' => $params_values,
		)
	);
}
$output .= ob_get_clean();
$output .= '</div>';

// Output controls, if set
if ( ! empty( $field['show_controls'] ) ) {
	$output .= '<div class="usof-form-group-item-controls">';

	// Show "Move" button, if "is_sortable" is set
	if ( ! empty( $field['is_sortable'] ) ) {
		$output .= '<div class="usof-control-move" title="' . us_translate( 'Move' ) . '"></div>';
	}

	// Show "Duplicate" button, if "is_duplicate" is set
	if ( ! empty( $field['is_duplicate'] ) ) {
		$output .= '<div class="usof-control-duplicate" title="' . us_translate( 'Duplicate' ) . '"></div>';
	}
	$output .= '<div class="usof-control-delete" title="' . us_translate( 'Delete' ) . '"></div>';
	$output .= '</div>';
}

$output .= '</div>';

echo $output;
