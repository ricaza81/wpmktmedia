<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a form's captcha field
 *
 * @var $name        string Field name
 * @var $type        string Field type
 * @var $label       string Field label
 * @var $placeholder string Field placeholder
 * @var $description string Field description
 * @var $value       string Field value
 * @var $move_label  bool Move field's title on focus
 * @var $icon        string Field icon
 * @var $field_id    string Field id
 * @var $classes     string Additional field classes
 *
 * @action Before the template: 'us_before_template:templates/form/captcha'
 * @action After the template: 'us_after_template:templates/form/captcha'
 * @filter Template variables: 'us_template_vars:templates/form/captcha'
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

$label = strip_tags( $label, '<a><br><strong>' );

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

$numbers = array( rand( 16, 30 ), rand( 1, 15 ) );
$sign = rand( 0, 1 );
$label .= ' <span>' . implode( $sign ? ' + ' : ' - ', $numbers );
$result_hash = md5( ( $numbers[0] + ( $sign ? 1 : - 1 ) * $numbers[1] ) . NONCE_SALT );

// Always required field
$_atts['class'] .= ' required';
if ( ! empty( $label ) ) {
	$label .= ' = ?</span>';
}

$field_atts['type'] = 'text';
$field_atts['name'] = isset( $name ) ? $name : $field_id;
$field_atts['placeholder'] = $placeholder;
$field_atts['data-required'] = 'true';
$field_atts['aria-required'] = 'true';
if ( ! empty( $icon ) ) {
	$_atts['class'] .= ' with_icon';
}
if ( ! empty( $cols ) AND $cols != 1 ) {
	$_atts['class'] .= ' cols_' . $cols;
}

if ( us_amp() ) {
	$field_atts['id'] = $field_id;
	$field_atts['required'] = TRUE;
}
?>
<div<?= us_implode_atts( $_atts ) ?>>
	<div class="w-form-row-label">
		<span><?= $label ?></span>
	</div>
	<div class="w-form-row-field">
		<?php do_action( 'us_form_captcha_start', $vars ) ?>
		<input type="hidden" name="<?= ( ! empty( $name ) ? $name : $field_id ) ?>_hash" value="<?= $result_hash ?>" />
		<?= us_prepare_icon_tag( $icon ) ?>
		<input<?= us_implode_atts( $field_atts ) ?>/>
		<?php do_action( 'us_form_captcha_end', $vars ) ?>
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
	<div<?= us_implode_atts( $_state_atts ) ?>><?php _e( 'Enter the equation result to proceed', 'us' ) ?></div>
</div>
