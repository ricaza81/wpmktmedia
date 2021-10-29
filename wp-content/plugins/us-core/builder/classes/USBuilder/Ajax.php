<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * This class describes an us builder ajax.
 */
final class USBuilder_Ajax {

	/**
	 * Prefix for internal AJAX request handlers to ensure uniqueness.
	 *
	 * @var string
	 */
	const PREFIX_FOR_HANDLER = 'usb_';

	/**
	 * Prefix for actions that will be called from the Frontend.
	 *
	 * @var string
	 */
	const PREFIX_FOR_ACTION = 'action_';

	/**
	 * Pattern for getting layout data from shortcodes (Used in importing shortcodes)
	 *
	 * @var string
	 */
	const PATTERN_GRID_LAYOUT_DATA = '/(\s?grid_layout_data="([^"]+)")/';

	/**
	 * Init hooks for AJAX actions
	 *
	 * @access public
	 */
	public static function init() {

		// TODO: add proper capability check, see #2232
		// Checking for edit permission
		if (
			! is_user_logged_in()
			AND (
				! current_user_can( 'edit_posts' )
				OR ! current_user_can( 'edit_pages' )
			)
		) {
			return;
		}

		// Check for an action in the list of all actions
		if (
			! $action = us_arr_path( $_POST, 'action' )
			OR ! in_array( $action, self::get_actions() )
		) {
			return;
		}

		// The check _nonce
		if ( ! check_ajax_referer( __CLASS__, '_nonce', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
		}

		// Setup postdata and add global $post, $wp_query for correct render of post related data (title, date, custom fields, etc.)
		if ( $post_id = (int) us_arr_path( $_REQUEST, 'post_id' ) ) {
			global $post, $wp_query;
			$query_args = array(
				'p' => $post_id,
				'post_type' => array_keys( us_get_public_post_types() ),
			);
			$wp_query->query( $query_args );
			$post = get_queried_object();
			setup_postdata( $post );
		}

		// Adds actions to process requests
		foreach ( self::get_actions() as $action_name ) {
			if ( ! empty( $action_name ) AND is_string( $action_name ) ) {
				// The add corresponding method from the class to the AJAX action
				$method_name = substr( $action_name, strlen( self::PREFIX_FOR_HANDLER ) );
				add_action( 'wp_ajax_' . $action_name, __CLASS__ . "::$method_name" );
			}
		}

		// For AJAX requests, we activate the definition of the builder page,
		// this is necessary for the correct loading of fieldsets
		add_filter( 'usb_is_builder_page', '__return_true' );
	}

	/**
	 * Get the actions
	 *
	 * @access public
	 * @return array The actions.
	 */
	public static function get_actions() {
		return array(
			self::PREFIX_FOR_ACTION . 'get_deferred_fieldsets' => self::PREFIX_FOR_HANDLER . 'get_deferred_fieldsets',
			self::PREFIX_FOR_ACTION . 'render_shortcode' => self::PREFIX_FOR_HANDLER . 'render_shortcode',
			self::PREFIX_FOR_ACTION . 'save_post' => self::PREFIX_FOR_HANDLER . 'save_post',
		);
	}

	/**
	 * Creates a nonce
	 *
	 * @access public
	 * @return string
	 */
	public static function create_nonce() {
		return wp_create_nonce( __CLASS__ );
	}

	/**
	 * The renders the resulting shortcodes via AJAX
	 *
	 * @access public
	 * @param string $shortcode The shortcode
	 */
	public static function render_shortcode( $shortcode ) {
		// Loading all shortcodes
		if ( class_exists( 'WPBMap' ) AND method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) {
			WPBMap::addAllMappedShortcodes();
		}

		$res = array( 'html' => '' );
		if ( $content = us_arr_path( $_POST, 'content' ) ) {
			$content = wp_unslash( $content );

			// If there is data of layouts, then we import layouts
			if ( strpos( $content, 'grid_layout_data' ) !== FALSE ) {
				$content = preg_replace_callback(
					self::PATTERN_GRID_LAYOUT_DATA,
					function ( $matches ) {
						return ' items_layout="' . us_import_grid_layout( $matches[2] ) . '"';
					},
					$content
				);
			}

			// Adds data-usbid attribute to html when output shortcode result
			add_filter( 'do_shortcode_tag', array( USBuilder_Shortcode::instance(), 'add_usbid_to_html' ), 9999, 3 );
			$res['html'] = (string) do_shortcode( $content );
		}

		// Add content to the result (This can be useful for complex changes)
		if ( us_arr_path( $_POST, 'isReturnContent' ) ) {
			$res['content'] = $content;
		}

		// The response data
		wp_send_json_success( $res );
	}

	/**
	 * Get all deferred fieldsets
	 *
	 * @access public
	 * @return string
	 */
	public static function get_deferred_fieldsets() {

		// Get a list of all elements in a theme
		$theme_elements = us_config( 'shortcodes.theme_elements', array(), TRUE );

		// If the element name was specified explicitly, then check the relevance and install it
		if ( $name = us_arr_path( $_POST, 'name' ) AND in_array( $name, $theme_elements ) ) {
			$theme_elements = array( $name );
		}

		// Get all elements available in the theme
		$fieldsets = array();
		foreach ( $theme_elements as $elm_filename ) {
			if ( $elm_config = us_config( "elements/$elm_filename", array() ) ) {
				if (
					// Ignore elements which are not available via condition
					( isset( $elm_config['place_if'] ) AND ! $elm_config['place_if'] )
					OR us_arr_path( $elm_config, 'usb_preload', FALSE )
				) {
					continue;
				}

				// Remove prefixes needed for compatibility from Visual Composer
				foreach ( us_arr_path( $elm_config, 'params', array() ) as $param_name => $options ) {
					if ( ! empty( $options['type'] ) ) {
						$elm_config['params'][ $param_name ]['type'] = USBuilder::get_clean_shortcode_name( $options['type'] );
					}
				}

				// Attributes for the form tag
				$form_atts = array(
					'class' => 'us-builder-panel-fieldset',
					'data-name' => $elm_filename,
				);

				$html = '<form ' . us_implode_atts( $form_atts ) . '>';
				$html .= us_get_template(
					'usof/templates/edit_form', array(
						'type' => $elm_filename,
						'params' => us_arr_path( $elm_config, 'params', array() ),
						'context' => 'shortcode',
					)
				);
				$html .= '</form>';

				$fieldsets[ $elm_filename ] = $html;
			}
		}
		wp_send_json_success( $fieldsets );
	}

	/**
	 * Updates post or term data
	 *
	 * @access public
	 */
	// TODO: check capabilities add support for translated posts
	public static function save_post() {

		if ( ! $post_id = us_arr_path( $_POST, 'post_id' ) ) {
			wp_send_json_error( array( 'message' => us_translate( 'Post ID not set' ) ) );
		}
		if ( ! $post = get_post( (int) $post_id ) ) {
			wp_send_json_error( array( 'message' => us_translate( 'Record could not be found' ) ) );
		}

		// Applying the WordPress stripslashes_deep function to remove all added slashes
		$_POST = array_map( 'stripslashes_deep', $_POST );

		if ( isset( $_POST['post_title'] ) ) {
			if ( empty( $_POST['post_title'] ) ) {
				wp_send_json_error( array( 'message' => us_translate( 'Post title cannot be empty' ) ) );
			} elseif ( mb_strlen( $_POST['post_title'] ) > 255 ) {
				wp_send_json_error( array( 'message' => us_translate( 'Post title cannot exceed 255 characters' ) ) );
			}
			$post->post_title = $_POST['post_title'];
		}

		// If content is set, then we get it and apply filters to it
		if ( isset( $_POST['post_content'] ) ) {
			// Get and clearing content from usbid="..."
			$post->post_content = preg_replace( '/(\s?usbid="([^\"]+)")/', '', (string) $_POST['post_content'] );
			// Additional cleaning of layout data
			$post->post_content = preg_replace( self::PATTERN_GRID_LAYOUT_DATA, '', $post->post_content );
		}

		// Updating post metadata
		if ( $metadata = us_arr_path( $_POST, 'pageMeta', /* Default */ array() ) ) {
			// Clearing metadata from HTML tags
			$key_custom_css = USBuilder::KEY_CUSTOM_CSS;
			if ( isset( $metadata[ $key_custom_css ] ) ) {
				$metadata[ $key_custom_css ] = wp_strip_all_tags( $metadata[ $key_custom_css ] );
				// Add entries for Visual Composer
				$vc_meta_keys = array( '_wpb_post_custom_css', 'vc_post_custom_css' );
				$metadata = array_merge( array_fill_keys( $vc_meta_keys, $metadata[ $key_custom_css ] ), $metadata );
			}

			$post->meta_input = (array) $metadata;
		}

		if ( isset( $_POST['post_status'] ) ) {
			if ( ! array_key_exists( $_POST['post_status'], get_post_stati() ) ) {
				wp_send_json_error( array( 'message' => us_translate( 'Invalid post status' ) ) );
			}
			$post->post_status = $_POST['post_status'];
		}

		wp_update_post( $post, /* WP_Error */ TRUE );
		$result = NULL; // TODO fix for error handling
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success();
	}
}
