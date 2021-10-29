<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a form's select field
 *
 * @var $name        string Field name
 * @var $type        string Field type
 * @var $label       string Field label
 * @var $placeholder string Field placeholder
 * @var $description string Field description
 * @var $icon        string Field icon
 * @var $field_id    string Field id
 * @var $classes     string Additional field classes
 * @var $values      string Field values
 *
 * @action Before the template: 'us_before_template:templates/form/select'
 * @action After the template: 'us_after_template:templates/form/select'
 * @filter Template variables: 'us_template_vars:templates/form/select'
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

// Do not show this field if it has no values
if ( empty( $values ) ) {
	return;

} else {
	$values = explode( "\n", $values );
}

$_atts['class'] = 'w-form-row';
$_atts['class'] .= ' for_' . $type;
if ( ! empty( $classes ) ) {
	$_atts['class'] .= ' ' . $classes;
}

if ( ! empty( $label ) ) {
	$_atts['class'] .= ' has_label';
	$field_atts['aria-label'] = $label;
} elseif ( empty( $label ) AND ! empty( $placeholder ) ) {
	$field_atts['aria-label'] = $placeholder;
} else {
	$field_atts['aria-label'] = $field_id;
}

$field_atts['name'] = isset( $name ) ? $name : $field_id;

if ( ! empty( $icon ) ) {
	$_atts['class'] .= ' with_icon';
}
if ( ! empty( $cols ) AND $cols != 1 ) {
	$_atts['class'] .= ' cols_' . $cols;
}

?>
<div <?= us_implode_atts( $_atts ) ?>>
	<?php if ( ! empty( $label ) ) : ?>
		<div class="w-form-row-label">
			<span><?= strip_tags( $label, '<a><br><strong>' ) ?></span>
		</div>
	<?php endif; ?>
	<div class="w-form-row-field">
		<?php do_action( 'us_form_field_start', $vars ) ?>
		<?= us_prepare_icon_tag( $icon ) ?>
		<select <?= us_implode_atts( $field_atts ) ?>>
			<?php
			foreach ( $values as $value ) {
				echo '<option value="' . esc_attr( $value ) . '">' . $value . '</option>';
			}
			?>
		</select>
		<?php do_action( 'us_form_field_end', $vars ) ?>
	</div>
	<?php if ( ! empty( $description ) ) : ?>
		<div class="w-form-row-description">
			<?= strip_tags( $description, '<a><br><strong>' ) ?>
		</div>
	<?php endif; ?>
	<div class="w-form-row-state"><?php _e( 'Select an option', 'us' ) ?></div>
</div>
