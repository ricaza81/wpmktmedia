<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

class US_Shortcodes {

	/**
	 * @var {String} Template directory
	 */
	protected $_template_directory;

	protected $config;

	/**
	 * @var array Current shortcode config
	 */
	protected $_settings;

	/**
	 * Retrieve one setting (used for compatibility with VC)
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function settings( $key ) {
		return isset( $this->_settings[ $key ] ) ? $this->_settings[ $key ] : NULL;
	}

	/**
	 * @var US_Shortcodes
	 */
	protected static $instance;

	/**
	 * Singleton pattern: US_Shortcodes::instance()->us_grid($atts, $content)
	 *
	 * @return US_Shortcodes
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	protected function __construct() {
		global $us_template_directory, $us_stylesheet_directory;
		$this->config = us_config( 'shortcodes' );

		add_filter( 'the_content', array( $this, 'paragraph_fix' ) );
		add_filter( 'us_page_block_the_content', array( $this, 'paragraph_fix' ), 11 );
		add_filter( 'us_content_template_the_content', array( $this, 'paragraph_fix' ), 11 );

		add_filter( 'the_content', array( $this, 'a_to_img_magnific_pupup' ) );
		add_filter( 'us_page_block_the_content', array( $this, 'a_to_img_magnific_pupup' ) );
		add_filter( 'us_content_template_the_content', array( $this, 'a_to_img_magnific_pupup' ) );

		// Make sure that priority makes the class init after WPBakery Page Builder
		add_action( 'init', array( $this, 'init' ), 20 );

		$this->_template_directory = $us_template_directory;
		$this->_stylesheet_directory = $us_stylesheet_directory;
	}

	/**
	 * @var bool Is the shortcode inited?
	 */
	protected $inited = FALSE;

	public function init() {
		// Adding new shortcodes
		if ( isset( $this->config['theme_elements'] ) ) {
			foreach ( $this->config['theme_elements'] as $element ) {
				$shortcode = 'us_' . $element;
				add_shortcode( $shortcode, array( $this, $shortcode ) );
			}
		}

		// Adding aliases
		if ( isset( $this->config['alias'] ) ) {
			foreach ( $this->config['alias'] as $shortcode => $alias ) {
				// Overloading the previous declaration if exists
				if ( shortcode_exists( $shortcode ) ) {
					remove_shortcode( $shortcode );
				}
				add_shortcode( $shortcode, array( $this, $shortcode ) );
			}
		}

		// Modifying existing shortcodes
		if ( isset( $this->config['modified'] ) ) {
			foreach ( $this->config['modified'] as $shortcode => $shortcode_params ) {
				// Some shortcodes should not be overloaded
				if ( isset( $shortcode_params['overload'] ) AND ! $shortcode_params['overload'] ) {
					continue;
				}
				// Overloading the previous declaration if exists
				if ( shortcode_exists( $shortcode ) ) {
					remove_shortcode( $shortcode );
				}
				add_shortcode( $shortcode, array( $this, $shortcode ) );
			}
		}

		// Removing disabled shortcodes
		if ( us_get_option( 'disable_extra_vc', 1 ) == 1 AND isset( $this->config['disabled'] ) ) {
			foreach ( $this->config['disabled'] as $shortcode ) {
				if ( shortcode_exists( $shortcode ) ) {
					remove_shortcode( $shortcode );
				}
			}
		}

		$this->inited = TRUE;
	}

	/**
	 * Handling shortcodes
	 *
	 * @param string $shortcode Shortcode name
	 * @param array $args
	 *
	 * @return string Generated shortcode output
	 *
	 */
	public function __call( $shortcode, $args ) {
		$_output = '';
		$shortcode_base = $shortcode;
		// Checking wif it is alias and getting real shortcode name
		if ( isset( $this->config['alias'][ $shortcode ] ) ) {
			$shortcode = $this->config['alias'][ $shortcode ];
		}

		// Check if it is theme element or modified shortcode
		if ( substr( $shortcode, 0, 3 ) == 'us_' ) {
			$element = substr( $shortcode, 3 );
		} else {
			$element = $shortcode;
		}

		if ( ! in_array( $element, $this->config['theme_elements'] ) AND ! isset( $this->config['modified'][ $element ] ) ) {
			return $_output;
		}

		// Preparing params for shortcodes (can be used inside of the input)
		$atts = isset( $args[0] ) ? $args[0] : array();
		$content = isset( $args[1] ) ? $args[1] : '';

		// VC's special chars replacement
		if ( is_array( $atts ) ) {
			$atts_result = array();
			foreach ( $atts as $key => $val ) {
				$atts_result[ $key ] = str_replace(
					array(
						'`{`',
						'`}`',
						'``',
					), array(
					'[',
					']',
					'"',
				), $val
				);
			}
			$atts = $atts_result;
		}

		// Preserving VC before hook
		if ( substr( $shortcode_base, 0, 3 ) == 'vc_' AND defined( 'VC_SHORTCODE_BEFORE_CUSTOMIZE_PREFIX' ) ) {
			$custom_output_before = VC_SHORTCODE_BEFORE_CUSTOMIZE_PREFIX . $shortcode_base;
			if ( function_exists( $custom_output_before ) ) {
				$_output .= $custom_output_before( $atts, $content );
			}
			unset( $custom_output_before );
		}

		$_filename = us_locate_file( 'templates/elements/' . $element . '.php' );

		// We are using the context variable in some elements templates
		$us_elm_context = 'shortcode';

		// Fallback for elements that used both in shortcodes and in grid builder
		global $us_grid_object_type;
		if ( ! $us_grid_object_type ) {
			$us_grid_object_type = 'post';
		}

		$filled_atts = us_shortcode_atts( $atts, $shortcode );

		// Only for theme elements aliases: get params for both base and alias
		if ( $shortcode_base != $shortcode AND substr( $shortcode, 0, 3 ) == 'us_' ) {
			$filled_atts_base = us_shortcode_atts( $atts, $shortcode_base );
			$filled_atts = us_array_merge( $filled_atts, $filled_atts_base );
		}

		// Add custom CSS class name from Design Options
		if ( ! empty( $filled_atts['css'] ) ) {
			if ( function_exists( 'us_get_design_css_class' ) ) {
				$design_css_class = us_get_design_css_class( $filled_atts['css'] );
				$filled_atts['design_css_class'] = $design_css_class;

				if ( ! isset( $filled_atts['classes'] ) ) {
					$filled_atts['classes'] = ' ' . $design_css_class;
				} else {
					$filled_atts['classes'] .= ' ' . $design_css_class;
				}
			}
		}

		unset( $filled_atts['content'] );
		extract( $filled_atts );

		ob_start();
		require $_filename;
		$_output .= ob_get_clean();

		// Preserving VC after hooks
		if ( substr( $shortcode_base, 0, 3 ) == 'vc_' ) {
			if ( defined( 'VC_SHORTCODE_AFTER_CUSTOMIZE_PREFIX' ) ) {
				$custom_output_after = VC_SHORTCODE_AFTER_CUSTOMIZE_PREFIX . $shortcode_base;
				if ( function_exists( $custom_output_after ) ) {
					$_output .= $custom_output_after( $atts, $content );
				}
			}
			$this->_settings = array(
				'base' => $shortcode_base,
			);
			$_output = apply_filters( 'vc_shortcode_output', $_output, $this, isset( $args[0] ) ? $args[0] : array(), $shortcode );
		}

		return $_output;
	}

	public function paragraph_fix( $content ) {
		$array = array(
			'<p>[' => '[',
			']</p>' => ']',
			']<br />' => ']',
			']<br>' => ']',
		);

		$content = strtr( $content, $array );

		return $content;
	}

	public function a_to_img_magnific_pupup( $content ) {
		$pattern = "/<a(.*?)href=('|\")([^>]*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
		$replacement = '<a$1ref="magnificPopup" href=$2$3.$4$5$6>';
		$content = preg_replace( $pattern, $replacement, $content );

		return $content;
	}

	/**
	 * Remove some of the shortcodes handlers to use native VC shortcodes instead for front-end compatibility
	 */
	public function vc_front_end_compatibility() {
		if ( WP_DEBUG AND $this->inited ) {
			wp_die( 'Shortcodes VC front end compatibility should be provided before the shortcodes init' );
		}
		unset( $this->config['modified']['vc_tta_tabs'], $this->config['modified']['vc_tta_accordion'], $this->config['modified']['vc_tta_tour'], $this->config['alias']['vc_tta_accordion'], $this->config['alias']['vc_tta_tour'], $this->config['modified']['vc_tta_section'] );
	}

}

global $us_shortcodes;
$us_shortcodes = US_Shortcodes::instance();

// Add custom options to WP Gallery window
add_action( 'print_media_templates', 'us_media_templates' );
function us_media_templates() {
	?>
	<script type="text/html" id="tmpl-us-custom-gallery-setting">
		<label class="setting">
			<span><?php _e( 'Add indents between items', 'us' ) ?></span>
			<input type="checkbox" data-setting="indents">
		</label>
		<label class="setting">
			<span><?php _e( 'Display as', 'us' ) ?>&nbsp;<?php _e( 'Masonry', 'us' ) ?></span>
			<input type="checkbox" data-setting="masonry">
		</label>
		<label class="setting">
			<span><?php _e( 'Show image title and description', 'us' ) ?></span>
			<input type="checkbox" data-setting="meta">
		</label>
	</script>
	<script>
		jQuery( document ).ready( function() {
			if ( wp === undefined || wp.media === undefined ) {
				return;
			}

			// add your shortcode attribute and its default value to the
			// gallery settings list; $.extend should work as well...
			jQuery.extend( wp.media.gallery.defaults, {
				type: 'default_val'
			} );

			// merge default gallery settings template with yours
			wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend( {
				template: function( view ) {
					return wp.media.template( 'gallery-settings' )( view )
						+ wp.media.template( 'us-custom-gallery-setting' )( view );
				}
			} );

		} );
	</script>

	<script type="text/html" id="tmpl-us-custom-image-setting">
		<label class="setting" data-setting="us_attachment_link">
			<span class="name"><?php _e( 'Custom Link', 'us' ); ?></span>
			<input type="text" value="{{ data.us_attachment_link || '' }}"><?php /* value="{{ data.meta.us_attachment_link || '' }}" */ ?>
		</label>

	</script>
	<script>
		jQuery( document ).ready( function() {
			if ( wp === undefined || wp.media === undefined ) {
				return;
			}

			wp.media.view.Attachment.Details = wp.media.view.Attachment.Details.extend( {
				template: function( view ) {
					return wp.media.template( 'attachment-details' )( view ).replace( 'attachment-info">', 'attachment-info">' + wp.media.template( 'us-custom-image-setting' )( view ) );
				}
			} );
		} );
	</script>
	<?php
}

add_action( 'wp_ajax_save-attachment', 'us_ajax_save_attachment', 1 );
function us_ajax_save_attachment() {

	if ( ! isset( $_REQUEST['id'] ) || ! isset( $_REQUEST['changes'] ) ) {
		wp_send_json_error();
	}

	if ( ! $id = absint( $_REQUEST['id'] ) ) {
		wp_send_json_error();
	}

	check_ajax_referer( 'update-post_' . $id, 'nonce' );

	if ( ! current_user_can( 'edit_post', $id ) ) {
		wp_send_json_error();
	}

	$changes = $_REQUEST['changes'];
	$post = get_post( $id, ARRAY_A );

	if ( 'attachment' != $post['post_type'] ) {
		wp_send_json_error();
	}

	if ( isset( $changes['us_attachment_link'] ) ) {
		update_post_meta( $post['ID'], 'us_attachment_link', $changes['us_attachment_link'] );
	}
}

add_filter( 'wp_prepare_attachment_for_js', 'us_prepare_attachment_for_js', 10, 3 );
function us_prepare_attachment_for_js( $response, $attachment, $meta ) {
	$response['us_attachment_link'] = get_post_meta( $attachment->ID, 'us_attachment_link', TRUE );

	return $response;
}

// Add theme image sizes to WP selector in Gallery options
add_filter( 'image_size_names_choose', 'us_image_size_names_choose' );
function us_image_size_names_choose( $sizes ) {
	return us_get_image_sizes_list();
}
