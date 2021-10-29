<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * This is a class for working with shortcodes
 */
final class USBuilder_Shortcode {

	/**
	 * Shortcodes with IDs for the builder
	 */
	private $content;

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
	 * Prepares shortcodes for display on the preview page
	 *
	 * @access public
	 * @param string $content This is the content of the page
	 * @return string
	 */
	public function prepare_text( $content ) {
		if ( empty( $content ) ) {
			return $content;
		}

		$this->fallback_content( $content );

		$post_id = (int) USBuilder::get_post_id();

		// Checking if we are preparing the edited post content
		$preparing_main_content = FALSE;
		if ( $post_id == get_the_ID() ) {
			$preparing_main_content = TRUE;
		}

		// For the edited post, we are forcing row / column structure for now, later this will be improved
		if ( $preparing_main_content ) {
			if (
				! defined( 'USB_REMOVE_ROWS' )
				AND strpos( $content, '[vc_row' ) === FALSE
			) {
				$content = '[vc_row][vc_column][vc_column_text]' . $content . '[/vc_column_text][/vc_column][/vc_row]';

			} elseif (
				defined( 'USB_REMOVE_ROWS' )
				AND strpos( $content, '[vc_column_text' ) === FALSE
			) {
				$content = '[vc_column_text]' . $content . '[/vc_column_text]';
			}

		}

		$indexes = array(); // The indexes for shortcodes

		/**
		 * Adds usbid for shortcodes
		 *
		 * @param $matches The matches
		 * @return string Modified shortcode
		 */
		$func_prepare_shortcode = function ( $matches ) use ( &$indexes ) {
			// Matched variables
			$shortcode_name = $matches[2];
			$shortcode_atts = $matches[3];
			$shortcode_content = $matches[5];

			// A shortcode can have only one identifier, so if there is an identifier,
			// we will return the result unchanged.
			if ( strpos( $shortcode_atts, 'usbid="' ) !== FALSE ) {
				return $matches[0]; // Original shortcode unchanged
			}

			// Gets a unique index for a shortcode
			if ( empty( $indexes[ $shortcode_name ] ) ) {
				$indexes[ $shortcode_name ] = 1;
			} else {
				$indexes[ $shortcode_name ]++;
			}

			// Creating a unique tag ID
			$usbid = $shortcode_name . ':' . $indexes[ $shortcode_name ];

			// Add the usbid to the general list of shortcode attributes
			return '[' . $shortcode_name.$shortcode_atts . ' usbid="' . $usbid .'"]' . $shortcode_content;
		};
		$pattern = '/'. get_shortcode_regex() .'/Ui';
		$content = preg_replace_callback( $pattern, $func_prepare_shortcode, $content );

		// Saving only page shortcodes, ignore everything else,
		// since I can parse templates or other components.
		if ( $preparing_main_content ) {
			$this->content = trim( $content, "\n" );
		}

		return (string) apply_filters( 'usb_shortcode_preparate_text', $content );
	}

	/**
	 * Adds data-usbid attribute to html when output shortcode result
	 *
	 * @access public
	 * @param string $output The shortcode output
	 * @param string $tag The shortcode name
	 * @param array $atts The shortcode attributes array or empty string
	 * @return string
	 */
	public function add_usbid_to_html( $output, $tag, $atts ) {
		if ( ! ( $usbid = us_arr_path( $atts, 'usbid' ) ) ) {
			return $output;
		}

		// If `$tag` is `us_page_block` then add a wrapper to merge the output
		if ( strpos( $tag , 'us_page_block' ) !== FALSE ) {
			// Output styles from Page Block
			if ( $page_block_id = us_arr_path( $atts, 'id' ) ) {
				$jsoncss_data = get_post_meta( $page_block_id, '_us_jsoncss_data', TRUE );
				if ( is_array( $jsoncss_data ) AND ! empty( $jsoncss_data ) ) {
					$jsoncss_data_collection = array();
					foreach ( $jsoncss_data as $jsoncss ) {
						us_add_jsoncss_to_collection( $jsoncss, $jsoncss_data_collection );
					}
					if ( $custom_css = (string) us_jsoncss_compile( $jsoncss_data_collection ) ) {
						$output .= '<style>'. $custom_css .'</style>';
					}
				}
			}
			// Add a wrapper for the output
			$output = '<div class="w-page-block" data-edit_link="' . esc_attr( USBuilder::get_edit_permalink( $page_block_id ) ) . '">' . $output . '</div>';
		}

		// Additional attributes for output
		$output = preg_replace( '/(<[a-z\d]+)(.*)/', '$1 ' . 'data-usbid="' . $usbid . '"' . '$2', $output, 1 );

		// Add custom styles to the output
		if ( $jsoncss = us_arr_path( $atts, 'css', /* Default */FALSE ) ) {
			$jsoncss_collection = array();
			$unique_classname = (string) us_add_jsoncss_to_collection( $jsoncss, $jsoncss_collection );

			// Replacing the existing class with a new one to avoid duplicates with the same design settings.
			$new_unique_classname = 'usb_custom_' . str_replace( ':' , '', $usbid );
			$output = str_replace( $unique_classname , $new_unique_classname, $output );

			// Replacing classes in a jsoncss collection
			$new_jsoncss_collection = array();
			foreach ( $jsoncss_collection as $state => $collection ) {
				$new_jsoncss_collection[ $state ][ $new_unique_classname ] = $collection[ $unique_classname ];
			}
			unset( $jsoncss_collection );

			if ( $custom_css = (string) us_jsoncss_compile( $new_jsoncss_collection ) ) {
				$output .= '<style data-for="' . $usbid . '" data-classname="' . $new_unique_classname . '">' . $custom_css . '</style>';
			}
		}

		return $output;
	}

	/**
	 * Export of sources such as content and custom css
	 *
	 * Note:
	 * windov.$usb.content This is the content of the page
	 * window.$usb.pageCustomCss This is a custom custom css for the page
	 *
	 * @access public
	 */
	public function export_page_sources() {

		/**
		 * Selector for find style node.
		 * NOTE: Since this is outputed in the bowels of the WPBakery Page Builder, we can correct it here.
		 *
		 * @var string
		 */
		$custom_css_selector = 'style[data-type='. USBuilder::KEY_CUSTOM_CSS .']';

		/**
		 * Page fields such as post_title, post_name, post_status etc.
		 *
		 * @var array
		 */
		$page_fields = array(
			// Get the title of the current page
			'post_title' => esc_attr( get_the_title() ),
		);

		/**
		 * Current metadata settings for the page
		 *
		 * @var array
		 */
		$page_meta = array();

		/**
		 * Get post metadata based on meta-boxes config.
		 * Note: In `usof_meta`, metadata can be overridden for preview in the USBuilder.
		 *
		 * @var array
		 */
		$metadata = get_post_custom( (int) USBuilder::get_post_id() );
		foreach ( us_config( 'meta-boxes', array() ) as $metabox_config ) {
			if (
				! us_arr_path( $metabox_config, 'usb_context' )
				OR ! in_array( get_post_type(), us_arr_path( $metabox_config, 'post_types', array() ) )
			) {
				continue;
			}

			foreach ( array_keys( us_arr_path( $metabox_config, 'fields', array() ) ) as $prop ) {
				$value = us_arr_path( $metadata, "{$prop}.0", '' );
				$page_meta[ $prop ] = is_serialized( $value )
					? unserialize( $value )
					: $value;
			}
		}
		unset( $metadata );

		// JS code for import page data to USBuilder.
		$jscode = '
			// Check the is iframe current window
			if ( window.self !== window.top ) {
				window.$usbdata = window.$usbdata || {};
				window.$usbdata.pageData = window.$usbdata.pageData || {};
				// Export page data.
				var pageData = window.$usbdata.pageData;
				pageData.content = document.getElementById("usb-content").innerHTML || "";
				pageData.fields = ' . json_encode( $page_fields ) . ';
				pageData.pageMeta = ' . json_encode( $page_meta ) . ';
				// Get data from stdout
				pageData.customCss = ( document.querySelector("'. $custom_css_selector .'") || {} ).innerHTML || "";
			}
		';
		// This is the content of the page
		echo '<script id="usb-content" type="text/post_content">' . $this->content .'</script><script>' . $jscode . '</script>';

	}

	/**
	 * @param string $content
	 * @return bool
	 */
	public function fallback_content( &$content ) {
		$content_changed = FALSE;
		if ( preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches ) ) {
			if ( count( $matches[2] ) ) {
				foreach ( $matches[2] as $i => $shortcode_name ) {
					$shortcode_content_changed = $shortcode_changed = FALSE;
					$shortcode_string = $matches[0][ $i ];
					$shortcode_atts_string = $matches[3][ $i ];
					$shortcode_content = $matches[5][ $i ];

					$atts_filter = 'usb_fallback_atts_' . $shortcode_name;
					$name_filter = 'usb_fallback_name_' . $shortcode_name;

					if ( has_filter( $atts_filter ) ) {
						$shortcode_atts = shortcode_parse_atts( $shortcode_atts_string );
						if ( ! is_array( $shortcode_atts ) ) {
							$shortcode_atts = array();
						}
						$fallback_atts = (array) apply_filters( $atts_filter, $shortcode_atts, $shortcode_content );
						$shortcode_changed = TRUE;
						$shortcode_atts_string = us_implode_atts( $fallback_atts, /* is shortcode */ TRUE );
					}
					if ( has_filter( $name_filter ) ) {
						$shortcode_changed = TRUE;
						$shortcode_name = apply_filters( $name_filter, $shortcode_name );
					}

					// Using recursion to fallback shortcodes inside this shortcode content
					if ( ! empty( $shortcode_content ) ) {
						$shortcode_content_changed = $this->fallback_content( $shortcode_content );
					}

					if ( $shortcode_changed OR $shortcode_content_changed ) {
						$new_shortcode_string = '[' . $shortcode_name . $shortcode_atts_string . ']';
						if ( ! empty( $shortcode_content ) ) {
							$new_shortcode_string .= $shortcode_content;
						}
						if ( strpos( $shortcode_string, '[/' . $matches[2][ $i ] . ']' ) ) {
							$new_shortcode_string .= '[/' . $shortcode_name . ']';
						}

						// Doing str_replace only once to avoid collisions
						$pos = strpos( $content, $shortcode_string );
						if ( $pos !== FALSE ) {
							$content = substr_replace( $content, $new_shortcode_string, $pos, strlen( $shortcode_string ) );
						}

						$content_changed = TRUE;
					}
				}
			}
		}

		return $content_changed;
	}

}
