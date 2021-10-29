<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a form's text field
 *
 * @var $name        string Field name
 * @var $type        string Field type
 * @var $label       string Field label
 * @var $placeholder string Field placeholder
 * @var $description string Field description
 * @var $value       string Field value
 * @var $required    bool Is the field required?
 * @var $move_label  bool Move field's title on focus
 * @var $icon        string Field icon
 * @var $field_id    string Field id
 * @var $classes     string Additional field classes
 *
 * @action Before the template: 'us_before_template:templates/form/text'
 * @action After the template: 'us_after_template:templates/form/text'
 * @filter Template variables: 'us_template_vars:templates/form/text'
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

if ( ! empty( $label ) ) {
	$_atts['class'] .= ' has_label';
	if ( $move_label AND ! us_amp() ) {
		$_atts['class'] .= ' move_label';
	}
	$field_atts['aria-label'] = $label;
} elseif ( empty( $label ) AND ! empty( $placeholder ) ) {
	$field_atts['aria-label'] = $placeholder;
} else {
	$field_atts['aria-label'] = $field_id;
}

if ( $inputmode != 'text' ) {
	$field_atts['inputmode'] = $inputmode;
}

$field_atts['type'] = $type;
$field_atts['name'] = isset( $name ) ? $name : $field_id;
$field_atts['value'] = $value;
if ( $required AND ! empty( $placeholder ) AND empty( $label ) ) {
	$placeholder .= ' *';
}
$field_atts['placeholder'] = $placeholder;

if ( $required ) {
	$_atts['class'] .= ' required';
	$field_atts['data-required'] = 'true';
	$field_atts['aria-required'] = 'true';
	if ( ! empty( $label ) ) {
		$label .= ' <span class="required">*</span>';
	}
	if ( us_amp() ) {
		$field_atts['id'] = $field_id;
		$field_atts['required'] = TRUE;
	}
}
if ( ! empty( $icon ) ) {
	$_atts['class'] .= ' with_icon';
}
if ( ! empty( $cols ) AND $cols != 1 ) {
	$_atts['class'] .= ' cols_' . $cols;
}

?>
<div<?= us_implode_atts( $_atts ) ?>>
	<?php if ( ! empty( $label ) ) : ?>
		<div class="w-form-row-label">
			<span><?= strip_tags( $label, '<a><br><strong>' ) ?></span>
		</div>
	<?php endif; ?>
	<div class="w-form-row-field">
		<?php do_action( 'us_form_field_start', $vars ) ?>
		<?= us_prepare_icon_tag( $icon ) ?>
		<input<?= us_implode_atts( $field_atts ) ?>/>
		<?php do_action( 'us_form_field_end', $vars ) ?>
	</div>
	<?php if ( ! empty( $description ) ) : ?>
		<div class="w-form-row-description">
			<?= strip_tags( $description, '<a><br><strong>' ) ?>
		</div>
	<?php endif;
	$_state_atts['class'] = 'w-form-row-state';
	if ( us_amp() AND $required ) {
		$_state_atts['visible-when-invalid'] = 'valueMissing';
		$_state_atts['validation-for'] = $field_atts['id'];
	}
	?>
	<div<?= us_implode_atts( $_state_atts ) ?>><?php _e( 'Fill out this field', 'us' ) ?></div>
</div>
