<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Improvements of the "Menus" admin screen
 */

if ( ! function_exists( 'us_menu_item_custom_fields' ) ) {
	add_action( 'wp_nav_menu_item_custom_fields', 'us_menu_item_custom_fields', 10, 4 );
	function us_menu_item_custom_fields( $id, $item, $depth, $args ) {

		// Add "Exclude Rows and Columns" checkbox for Page Blocks
		if ( $item->object == 'us_page_block' ) { ?>
			<p class="field-us-custom">
				<label for="edit-menu-item-remove-rows-<?php echo $id; ?>">
					<input type="checkbox" id="edit-menu-item-remove-rows-<?php echo $id; ?>" name="menu-item-remove-rows[<?php echo $id; ?>]"<?php checked( get_post_meta( $id, '_menu_item_remove_rows', TRUE ) ) ?> />
					<?php echo strip_tags( __( 'Exclude Rows and Columns', 'us' ) ); ?>
				</label>
			</p>
		<?php }

		// Add Button Style dropdown to the first level menu items
		if ( $item->object != 'us_page_block' AND $depth === 0 ) {
			$btn_styles = us_get_btn_styles();

			$output = '<p class="field-us-custom description">';
			$output .= '<label for="edit-menu-item-btn-style-' . $id . '">';
			$output .= __( 'Show as Button', 'us' ) . '</br>';
			$output .= '<select name="edit-menu-item-btn-style[' . $id . ']">';
			$output .= '<option value="">– ' . us_translate( 'None' ) . ' –</option>';
			foreach ( $btn_styles as $key => $style ) {
				$output .= '<option value="' . $key . '"' . selected( get_post_meta( $id, '_menu_item_btn_style', TRUE ), $key, FALSE ) . '>' . $style . '</option>';
			}
			$output .= '</select>';
			$output .= '</label>';
			$output .= '</p>';

			echo $output;
		}
	}
}

// Menu Custom Fields data handler
if ( ! function_exists( 'us_update_menu_custom_field' ) ) {
	add_action( 'wp_update_nav_menu_item', 'us_update_menu_custom_field', 10, 3 );
	function us_update_menu_custom_field( $menu_id, $menu_item_db_id, $args ) {

		if ( $args['menu-item-object'] == 'us_page_block' ) {
			if ( isset( $_POST['menu-item-remove-rows'] ) ) {
				$custom_fields = $_POST['menu-item-remove-rows'];
				$value = isset( $custom_fields[ $menu_item_db_id ] ) ? '1' : '0';
			} else {
				// Enabled by default
				$value = empty( $_POST['menu-item-db-id'] ) ? '1' : '0';
			}

			update_post_meta( $menu_item_db_id, '_menu_item_remove_rows', $value );

		} else {
			if ( isset( $_POST['edit-menu-item-btn-style'] ) ) {
				$custom_fields = $_POST['edit-menu-item-btn-style'];
				$value = isset( $custom_fields[ $menu_item_db_id ] ) ? $custom_fields[ $menu_item_db_id ] : '';
			} else {
				// Default value
				$value = '';
			}

			update_post_meta( $menu_item_db_id, '_menu_item_btn_style', $value );
		}
	}
}