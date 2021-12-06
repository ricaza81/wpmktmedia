<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * This is a class for working with post
 */
final class USBuilder_Post {
	/**
	 * @var USBuilder_Shortcode
	 */
	protected static $instance;

	/**
	 * @access public
	 * @return USBuilder_Shortcode
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Save generated shortcodes, posts meta.
	 *
	 * @access public
	 * @param $post_id - current post id
	 */
	public function save_post( $post_id ) {
		if (
			defined( 'DOING_AUTOSAVE' )
			AND DOING_AUTOSAVE
			OR (
				function_exists( 'vc_is_inline' )
				AND vc_is_inline()
			)
		) {
			return;
		}

		if ( 'dopreview' !== us_arr_path( $_POST, 'wp-preview' ) ) {
			return;
		}

		if ( wp_revisions_enabled( get_post( $post_id ) ) ) {
			$latest_revision = wp_get_post_revisions( $post_id );
			if ( ! empty( $latest_revision ) ) {
				$array_values = array_values( $latest_revision );
				$post_id = $array_values[0]->ID;
			}
		}

		// Save custom css when preview changes
		$meta_key = USBuilder::KEY_CUSTOM_CSS;
		if ( $post_custom_css = us_arr_path( $_POST, $meta_key ) ) {
			update_metadata( 'post', $post_id, $meta_key, $post_custom_css );
		} else {
			delete_metadata( 'post', $post_id, $meta_key );
		}
	}
}
