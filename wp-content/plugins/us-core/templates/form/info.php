<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a form's info field
 *
 * @var $value   string Field value
 * @var $classes string Additional field classes
 *
 * @action Before the template: 'us_before_template:templates/form/info'
 * @action After the template: 'us_after_template:templates/form/info'
 * @filter Template variables: 'us_template_vars:templates/form/info'
 */

$default_params = us_config( 'elements/cform.params.items.params' );
foreach ( $default_params as $param => $params ) {
	if ( ! isset( $$param ) ) {
		$$param = $params['std'];
	}
}

$_atts['class'] = 'w-form-row';
$_atts['class'] .= ' for_' . $type;
if ( ! empty( $classes ) ) {
	$_atts['class'] .= ' ' . $classes;
}

?>
<div<?= us_implode_atts( $_atts ) ?>>
	<p><?= strip_tags( $value, '<a><br><strong>' ) ?></p>
</div>
