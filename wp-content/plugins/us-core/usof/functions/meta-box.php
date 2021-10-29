<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'us_post_meta_boxes_setup' ) ) {
	add_action( 'load-post.php', 'us_post_meta_boxes_setup' );
	add_action( 'load-post-new.php', 'us_post_meta_boxes_setup' );

	function us_post_meta_boxes_setup() {

		$config = us_config( 'meta-boxes', array() );

		foreach ( $config as &$meta_box ) {
			new US_Meta_Box( $meta_box );
		}
	}
}

if ( has_filter( 'us_tr_default_language' ) AND ! function_exists( 'us_admin_body_class_non_default_lang' ) ) {
	/**
	 * Add 'us_lang_non_default' class to body element on non-default language admin pages
	 * to disable some of metaboxes
	 */
	function us_admin_body_class_non_default_lang( $class ) {
		$current_language = apply_filters( 'us_tr_current_language', NULL );
		$default_language = apply_filters( 'us_tr_default_language', NULL );
		if ( $current_language != $default_language ) {
			$class .= ' us_lang_non_default';
		}
		return $class;
	}
	add_filter( 'admin_body_class', 'us_admin_body_class_non_default_lang' );
}


class US_Meta_Box {

	public $meta_box;

	public function __construct( $meta_box ) {
		if ( ! is_admin() ) {
			return;
		}

		$this->meta_box = $meta_box;

		// Add meta box
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		// Save meta box
		foreach ( $this->meta_box['post_types'] as $post_type ) {
			add_action( 'save_post_' . $post_type, array( $this, 'save_meta_boxes' ) );
		}

	}

	public function add_meta_boxes() {
		foreach ( $this->meta_box['post_types'] as $post_type ) {
			$callback_args = NULL;

			add_meta_box(
				$this->meta_box['id'], $this->meta_box['title'], array(
				$this,
				'meta_box_body',
			), $post_type, $this->meta_box['context'], $this->meta_box['priority'], $callback_args
			);
		}
	}

	public function meta_box_body() {

		echo '<div class="usof-container for_meta">';
		global $us_metabox_marker_placed;
		if ( empty( $us_metabox_marker_placed ) ) {
			echo '<input type="hidden" name="us_metabox_save" value="1">';
			$us_metabox_marker_placed = TRUE;
		}

		$post = get_post();
		$post_id = isset( $post->ID ) ? $post->ID : 0;
		$values = array();

		// Output "Used in" locations for Page Blocks metabox
		if ( 'us_post_info' == $this->meta_box['id'] ) {
			$this->meta_box['fields']['used_in_locations']['description'] = us_get_used_in_locations( $post->ID, TRUE );
		}

		foreach ( $this->meta_box['fields'] as $field_id => $field ) {
			if ( $post_id ) {
				if ( metadata_exists( 'post', $post_id, $field_id ) ) {
					$values[ $field_id ] = get_post_meta( $post_id, $field_id, TRUE );
				} elseif ( isset( $field['std'] ) ) {
					$values[ $field_id ] = $field['std'];
				} else {
					$values[ $field_id ] = '';
				}
				if ( $field['type'] == 'link' ) {
					$values[ $field_id ] = json_decode( $values[ $field_id ], TRUE );
				}
			}

			// Reset to the default for select and radio types, if the value is not presented in options
			if (
				isset( $field['options'] )
				AND $field['type'] !== 'checkboxes'
				AND ! in_array( $values[ $field_id ], array_keys( $field['options'] ) )
			) {
				$values[ $field_id ] = isset( $field['std'] ) ? $field['std'] : '';
			}
		}

		foreach ( $this->meta_box['fields'] as $field_id => $field ) {
			us_load_template(
				'usof/templates/field', array(
					'name' => $field_id,
					'id' => 'usof_' . $field_id,
					'field' => $field,
					'values' => &$values,
					'context' => 'metabox',
				)
			);
		}

		echo '</div>';
	}

	public function save_meta_boxes( $post_id ) {
		if ( ! ( isset( $_POST['us_metabox_save'] ) AND $_POST['us_metabox_save'] == 1 ) ) {
			return;
		}
		foreach ( $this->meta_box['fields'] as $field_id => $field ) {
			if ( $field['type'] == 'heading' ) {
				continue;
			}
			// Don't save field's value if it is not placed on a post edit page
			if ( isset( $field['place_if'] ) AND ! $field['place_if'] ) {
				continue;
			}

			$new_value = isset( $_POST[ $field_id ] ) ? $_POST[ $field_id ] : NULL;

			update_post_meta( $post_id, $field_id, $new_value );
		}
	}
}
