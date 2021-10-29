<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a form's agreement box
 *
 * @var $name        string Field name
 * @var $type        string Field type
 * @var $label       string Field label
 * @var $description string Field description
 * @var $value       string Field value
 * @var $field_id    string Field id
 * @var $classes     string Additional field classes
 * @var $checked     bool checked
 *
 * @action Before the template: 'us_before_template:templates/form/checkbox'
 * @action After the template: 'us_after_template:templates/form/checkbox'
 * @filter Template variables: 'us_template_vars:templates/form/checkbox'
 */

$default_params = us_config( 'elements/cform.params.items.params' );
foreach ( $default_params as $param => $params ) {
	if ( ! isset( $$param ) ) {
		$$param = $params['std'];
	}
}

global $us_cform_index;
$field_id = isset( $field_id ) ? $field_id : 1;
$field_id = 'us_form_' . $us_cform_index . '_' . $type . '_' . $field_id;

$_atts['class'] = 'w-form-row';
$_atts['class'] .= ' for_' . $type;
if ( ! empty( $classes ) ) {
	$_atts['class'] .= ' ' . $classes;
}
$_atts['class'] .= ' required';

$field_atts['type'] = 'checkbox';
$field_atts['class'] = 'screen-reader-text';
$field_atts['value'] = '1';
$field_atts['data-required'] = 'true';
$field_atts['aria-required'] = 'true';
$field_atts['name'] = ! empty( $name ) ? $name : $field_id;

if ( us_amp() ) {
	$field_atts['id'] = $field_id;
	$field_atts['required'] = TRUE;
}
?>
<div<?= us_implode_atts( $_atts ) ?>>
	<?php if ( ! empty( $label ) ) : ?>
		<div class="w-form-row-label">
			<span><?= strip_tags( $label, '<a><br><strong>' ) . ' <span class="required">*</span>' ?></span>
		</div>
	<?php endif; ?>
	<div class="w-form-row-field">
		<?php do_action( 'us_form_field_start', $vars ) ?>
		<label>
			<input<?= us_implode_atts( $field_atts ) ?>/>
			<span class="w-form-checkbox"></span>
			<span><?= strip_tags( $value, '<a><br><strong>' ) ?></span>
		</label>
		<?php do_action( 'us_form_field_end', $vars ) ?>
	</div>
	<?php if ( ! empty( $description ) ) : ?>
		<div class="w-form-row-description">
			<?= strip_tags( $description, '<a><br><strong>' ) ?>
		</div>
	<?php endif;
	$_state_atts['class'] = 'w-form-row-state';
	if ( us_amp() ) {
		$_state_atts['visible-when-invalid'] = 'valueMissing';
		$_state_atts['validation-for'] = $field_atts['id'];
	}
	?>
	<div<?= us_implode_atts( $_state_atts ) ?>><?php _e( 'You need to agree with the terms to proceed', 'us' ) ?></div>
</div>
