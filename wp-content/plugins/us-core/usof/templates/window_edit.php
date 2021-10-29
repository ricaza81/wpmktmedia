<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output element's Settings window
 *
 * @var $titles array Elements titles
 * @var $body string Body inner HTML
 */
$titles = ( isset( $titles ) AND is_array( $titles ) ) ? $titles : array();
$body = isset( $body ) ? $body : '';

?>
<div class="us-bld-window for_editing">
	<div class="us-bld-window-h">
		<div class="us-bld-window-header">
			<div class="us-bld-window-title"<?php echo us_pass_data_to_js($titles) ?>></div>
			<div class="us-bld-window-closer" title="<?php echo us_translate( 'Close' ) ?>"></div>
		</div>
		<div class="us-bld-window-body usof-container"><?php echo $body ?><span class="usof-preloader"></span></div>
		<div class="us-bld-window-footer">
			<div class="usof-button button-primary type_save"><span><?php echo us_translate( 'Save Changes' ) ?></span></div>
		</div>
	</div>
</div>
