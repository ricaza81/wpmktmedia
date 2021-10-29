<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a form's hidden field
 *
 * @var $name  string Field name
 * @var $value string Field value
 *
 * @action Before the template: 'us_before_template:templates/form/hidden'
 * @action After the template: 'us_after_template:templates/form/hidden'
 * @filter Template variables: 'us_template_vars:templates/form/hidden'
 */

$_atts['type'] = 'hidden';
$_atts['name'] = isset( $label ) ? $label : '';
$_atts['value'] = isset( $value ) ? $value : '';

?>
<input<?= us_implode_atts( $_atts ) ?> />
