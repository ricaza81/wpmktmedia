<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

add_action( 'wp_ajax_usof_ajax_mega_menu_save_settings', 'usof_ajax_mega_menu_save_settings' );
function usof_ajax_mega_menu_save_settings() {
	$item_id = $_POST['item'];
	$settings = $_POST['settings'];

	if ( ! check_ajax_referer( 'us-menu-dropdown-save', 'security', FALSE ) ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	foreach ( $settings as $key => $val ) {
		if ( strip_tags( $val ) != $val ) {
			$settings[$key] = strip_tags( $val );
		}
	}

	update_post_meta( $item_id, 'us_mega_menu_settings', $settings );

	do_action( 'usof_ajax_mega_menu_save_settings' );

	wp_send_json_success(
		array(
			'message' => us_translate( 'Changes saved.' ),
		)
	);
}

add_action( 'wp_ajax_usof_ajax_mega_menu', 'usof_ajax_mega_menu' );
function usof_ajax_mega_menu() {
	$menu_id = $_GET['menu_id'];
	$item_id = $_GET['item_id'];

	$menu_items = wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'any' ) );

	$menu_label = '';
	if ( is_array( $menu_items ) AND count( $menu_items ) > 0 ) {
		foreach ( $menu_items as $menu_item ) {
			if ( $menu_item->ID == $item_id ) {
				$menu_label = $menu_item->title;
				break;
			}
		}
	}
	if ( $menu_label == '' ) {
		wp_send_json_error();
	}

	$settings_config = us_config( 'menu-dropdown', array() );

	?>
<div class="usof-header">
	<h2><?php echo $menu_label; ?><span> &ndash; <?php _e( 'Dropdown Settings', 'us' ) ?></span></h2>
	<div class="usof-control for_save status_clear">
		<button class="usof-button button-primary type_save" type="button">
			<span><?php echo us_translate( 'Save Changes' ) ?></span>
			<span class="usof-preloader"></span>
		</button>
		<div class="usof-control-message"></div>
	</div>
</div>
<div class="us-mm-body">
<div class="us-mm-settings" id="section_settings">
<?php

	$values = ( get_post_meta( $item_id, 'us_mega_menu_settings', TRUE ) ) ? get_post_meta( $item_id, 'us_mega_menu_settings', TRUE ) : array();
	foreach ( $settings_config as $field_id => $field ) {
		if ( ! isset( $values[$field_id] ) AND isset( $field['std'] ) ) {
			$values[$field_id] = $field['std'];
		}
	}

	foreach ( $settings_config as $field_id => $field ) {
		us_load_template(
			'usof/templates/field', array(
				'name' => $field_id,
				'id' => 'usof_' . $field_id,
				'field' => $field,
				'values' => &$values,
			)
		);
	}
?>
</div>
</div>
<script>
	jQuery(function ($) {
		$('#TB_window').addClass('usof-container');
		var $USMMHeader = $('.usof-header'),
			$saveButton = $USMMHeader.find('.usof-control.for_save'),
			$saveMessage = $saveButton.find('.usof-control-message'),
			saveStateTimer = null;

		"use strict";
		var USMMSave = function(){
			$saveButton.usMod('status', 'loading');
			$saveMessage.html('');
			$saveButton.off('click', USMMSave);

			$.ajax({
				type: 'POST',
				url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				dataType: 'json',
				data: {
					action: 'usof_ajax_mega_menu_save_settings',
					menu: <?php echo $menu_id; ?>,
					item: <?php echo $item_id; ?>,
					settings: USMMSettings,
					security: '<?php echo wp_create_nonce( 'us-menu-dropdown-save' ); ?>'
				},
				success: function(result){
					if ($saveButton.usMod('status') !== 'loading') return;
					$saveMessage.html(result.data.message);
					$saveButton.usMod('status', 'success');
					clearTimeout(saveStateTimer);
					saveStateTimer = setTimeout(function(){
						$saveMessage.html('');
						$saveButton.usMod('status', 'clear');
					}.bind(this), 4000);
				}
			});
		};

		$(document.body).trigger('usof_mm_load');

		$(document.body).off('usof_mm_save').on('usof_mm_save', function(){
			clearTimeout(saveStateTimer);
			$saveButton.usMod('status', 'notsaved');
			$saveButton.off('click').on('click', USMMSave);
		})

	});
</script>
<?php
	die();
}