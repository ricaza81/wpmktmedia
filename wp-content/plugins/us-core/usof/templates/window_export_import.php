<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output export-import dialog
 * @var string $title
 * @var string $text
 * @var string $save_text
 */

$title = isset( $title ) ? $title : '';
$text = isset( $text ) ? $text : '';
$save_text = isset( $save_text ) ? $save_text : '';

?>
<div class="us-bld-window for_export_import">
	<div class="us-bld-window-h">
		<div class="us-bld-window-header">
			<div class="us-bld-window-title"><?php echo $title ?></div>
			<div class="us-bld-window-closer" title="<?php echo us_translate( 'Close' ) ?>"></div>
		</div>
		<div class="us-bld-window-body usof-container">
			<div class="usof-form-row type_transfer desc_1">
				<div class="usof-form-row-title">
					<span><?php echo $text ?></span>
				</div>
				<div class="usof-form-row-field">
					<div class="usof-form-row-control">
						<textarea></textarea>
					</div>
					<div class="usof-form-row-state"><?php echo us_translate( 'Invalid data provided.' ) ?></div>
				</div>
			</div>
		</div>
		<div class="us-bld-window-footer">
			<div class="usof-button button-primary type_save"><span><?php echo $save_text ?></span></div>
		</div>
	</div>
</div>
