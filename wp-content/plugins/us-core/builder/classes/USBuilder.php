<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Main class of US Bbuilder
 */

if ( ! class_exists( 'USBuilder' ) ) {

	final class USBuilder {

		/**
		 * Param name in the URL address bar
		 * Note: This value must have valid characters for hooks and URL
		 */
		const BUILDER_SLUG = 'us-builder';

		/**
		 * Main container
		 *
		 * @var string
		 */
		const MAIN_CONTAINER = 'container';

		/**
		 * Key for storing custom styles
		 *
		 * @var string
		 */
		const KEY_CUSTOM_CSS = 'usb_post_custom_css';

		/**
		 * Css classes for the body element
		 *
		 * @var array
		 */
		private $_body_classes = array( 'us-builder', US_THEMENAME ); // Theme name is used for UI icons

		/**
		 * @access public
		 */
		public function __construct() {
			global $wpdb;

			// Get current builder slug
			$builder_slug = static::get_slug();

			// TODO: add proper capability check, see #2232
			if ( is_admin() ) {

				// On the builder page, we initialize all the necessary components
				if ( static::is_builder_page() ) {

					// Get the id of the post being edited
					$post_id = static::get_post_id();

					// Get minimal information about a record
					$post = get_post( $post_id );

					// Check if a post exists
					if ( ! $post_id OR is_null( $post ) ) {
						wp_die( us_translate( 'You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?' ) );
					}

					// Checking edit permission by post type
					if ( ! in_array( $post->post_type, $this->get_allowed_edit_post_types() ) ) {
						wp_die( 'Editing of this page is not supported.' );
					}

					// Checking edit permission by post ID
					if ( ! current_user_can( 'edit_post', $post_id ) ) {
						wp_die( us_translate( 'Sorry, you are not allowed to access this page.' ) );
					}

					// Publish the post if it doesn't exist
					if ( $post->post_status === 'auto-draft' ) {
						wp_update_post(
							array(
								'ID' => $post_id,
								'post_title' => '#' . $post_id,
								'post_status' => 'publish',
							)
						);
					}

					// Initializing the builder page post.
					add_action( "admin_action_{$builder_slug}", array( $this, 'init_builder_page' ), 1 );

					// Fires when scripts and styles are enqueued for the code editor.
					add_action( 'wp_enqueue_code_editor', array( $this, 'wp_enqueue_code_editor' ), 501, 1 );

					// Filters a string cleaned and escaped for output as a URL.
					add_filter( 'clean_url', array( $this, 'defer_admin_assets' ), 101, 1 );

					// Add styles and scripts on the builder page
					add_action( 'wp_print_styles', array( $this, 'enqueue_assets_for_builder' ), 1 );

					// Outputs the editor scripts, stylesheets, and default settings.
					add_action( 'usb_admin_footer_scripts', array( $this, 'admin_footer_scripts' ), 1 );

					// At regular admin pages ...
				} else {

					// Save generated shortcodes, posts meta.
					add_action( 'save_post', array( USBuilder_Post::instance(), 'save_post' ), 0, 1 );

					// .. adding a link to US Builder editor for posts and pages
					add_filter( 'post_row_actions', array( $this, 'row_actions' ), 501, 2 );
					add_filter( 'page_row_actions', array( $this, 'row_actions' ), 501, 2 );

					add_action( 'edit_form_after_title', array( $this, 'output_builder_switch' ) );
				}

			} else {

				// The output custom css for a page
				add_action( 'us_before_closing_head_tag', array( $this, 'output_post_custom_css' ), 9 );

				// If the site is open in the builder, then hide the admin bar
				if ( static::is_preview_page() ) {
					// Hide admin bar
					add_filter( 'show_admin_bar', '__return_false' );

					// Disable output of WPB custom styles
					add_filter( 'vc_post_custom_css', '__return_false', 1 );

					// Cancel the output of styles for content on the preview page
					add_filter( 'us_is_output_design_css_for_content', '__return_false', 1 );

					// This is an instance of the class for working with shortcodes
					$shortcode = USBuilder_Shortcode::instance();

					// Prepares shortcodes for display on the preview page
					add_action( 'the_content', array( $shortcode, 'prepare_text' ), 1, 1 );

					// Add data-usbid attribute to html when output shortcode result
					add_filter( 'do_shortcode_tag', array( $shortcode, 'add_usbid_to_html' ), 9999, 3 );

					// Export of sources such as content and custom css
					add_action( 'wp_footer', array( $shortcode, 'export_page_sources' ), 9999 );

					// Substitution of metadata to preview changes before saving.
					add_filter( 'usof_meta', array( $this, 'meta_to_preview' ), 1, 2 );

					// Add styles and scripts on the preview page
					add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets_for_preview' ) );

					// The output html for highlighting the selected element
					add_action( 'us_after_footer', array( $this, 'after_footer_action' ) );

					// Disabling header / footer for Page Block and Content Template preview pages
					add_action( 'us_get_page_area_id', array( $this, 'get_page_area_id_filter' ) );

					// Bind a method to manipulate the admin menu
				} elseif ( has_action( 'admin_bar_menu' ) ) {
					add_action( 'admin_bar_menu', [ $this, 'admin_bar_menu_action' ], 501 );
				}
			}

			// Init the class for handling AJAX requests
			if ( wp_doing_ajax() ) {
				USBuilder_Ajax::init();
			}
		}

		/**
		 * Get the builder slug
		 *
		 * @access public
		 * @return string The builder slug
		 */
		static public function get_slug() {
			return sanitize_title( self::BUILDER_SLUG );
		}

		/**
		 * Get the id of the edited page or term
		 *
		 * @access public
		 * @return int Returns the id of the page being edited
		 */
		static public function get_post_id() {
			return (int) us_arr_path( $_REQUEST, 'post', get_queried_object_id() );
		}

		/**
		 * Add styles and scripts on the preview page
		 *
		 * @access public
		 */
		public function enqueue_assets_for_preview() {
			wp_enqueue_script( 'usb-preview-js', US_BUILDER_URL . '/assets/js/us-builder-preview.js', array( 'jquery' ), US_CORE_VERSION );
			wp_enqueue_style( 'usb-preview-css', US_BUILDER_URL . '/assets/css/us-builder-preview.css', array(), US_CORE_VERSION );
		}

		/**
		 * Add styles and scripts on the builder page
		 *
		 * @access public
		 */
		public function enqueue_assets_for_builder() {
			global $wp_scripts, $wp_styles;

			// Reset scripts
			$wp_scripts = new WP_Scripts;
			// wp_default_scripts( $wp_scripts );

			// Reset styles
			$wp_styles = new WP_Styles;
			wp_default_styles( $wp_styles );

			// Remove assets that are not in use
			wp_dequeue_script( 'admin-bar' );

			// Include all files needed to use the WordPress media API
			wp_enqueue_media();
			wp_enqueue_editor();

			// WordPress styles for correct appearance of fields
			wp_enqueue_style( 'forms' );

			/**
			 * Hook for changing the output of assets on the constructor page.
			 * Note: Execute before outputting builder and theme files.
			 */
			do_action( 'usb_enqueue_assets_for_builder' );

			// Theme admin styles
			us_admin_print_styles();

			// Get the builder slug
			$builder_slug = static::get_slug();

			// Builder styles
			wp_enqueue_style( $builder_slug, US_BUILDER_URL . '/assets/css/us-builder.css', array(), US_CORE_VERSION );

			// Enqueue USOF JS files separately, when US_DEV is set
			if ( defined( 'US_DEV' ) ) {
				foreach ( us_config( 'assets-admin.js', array() ) as $i => $src ) {
					wp_enqueue_script( "usof-{$i}", US_CORE_URI . $src, array(), US_CORE_VERSION );
				}
			} else {
				wp_enqueue_script( 'usof-scripts', US_CORE_URI . '/usof/js/usof.min.js', array( 'jquery' ), US_CORE_VERSION, TRUE );
			}

			wp_enqueue_script( $builder_slug, US_BUILDER_URL . '/assets/js/builder.js', array( 'jquery' ), US_CORE_VERSION );
		}

		/**
		 * Filters a string cleaned and escaped for output as a URL
		 *
		 * @access public
		 * @param string $url The cleaned URL to be returned
		 * @return string
		 */
		public function defer_admin_assets( $url ) {
			$basename = wp_basename( $url );
			if (
				strpos( $basename, '.css' ) !== FALSE
				OR strpos( $basename, 'usof-' ) === 0 // All USOF files in DEV mode
			) {
				return "$url' defer='defer";
			}

			return $url;
		}

		/**
		 * Including additional scripts or settings in the output
		 * Note: The output of scripts in this method should exclude the initialization of the wp editor,
		 * the initialization is performed in the USOF
		 *
		 * @access public
		 */
		public function admin_footer_scripts() {
			// Get output footer scripts
			do_action( 'admin_print_footer_scripts' );

			// Prints scripts in document head that are in the $handles queue.
			if ( function_exists( 'wp_print_scripts' ) ) {
				wp_print_scripts();
			}

			// Get data for deferred assets
			$deferred_assets = array();
			foreach ( us_config( 'us-builder.deferred_assets', array() ) as $name => $handles ) {
				$deferred_assets[ $name ] = USBuilder_Assets::instance( $name )
					->add( $handles )
					->get_assets();
			}

			// This is the output of methods for params callbacks
			$js_callbacks = array();
			foreach ( us_config( 'shortcodes.theme_elements', array(), /* Reload */ TRUE ) as $elm_filename ) {
				if ( $elm_config = us_config( "elements/$elm_filename", array() ) ) {
					// Ignore elements which are not available via condition
					if ( isset( $elm_config['place_if'] ) AND ! $elm_config['place_if'] ) {
						continue;
					}
					// Do not run further code in cycle if the element has no params
					if ( empty( $elm_config['params'] ) ) {
						continue;
					}
					$elm_filename = us_get_shortcode_full_name( $elm_filename );
					foreach ( $elm_config['params'] as $param_name => $param_config ) {
						if ( us_arr_path( $param_config, 'usb_preview', TRUE ) === TRUE ) {
							continue;
						}
						if ( empty( $param_config['usb_preview'][0] ) ) {
							$param_config['usb_preview'] = array( $param_config['usb_preview'] );
						}
						foreach ( $param_config['usb_preview'] as $instructions ) {
							if ( empty( $instructions['callback'] ) ) {
								continue;
							}
							$callback_body = (string) $instructions['callback'];
							$js_callbacks[] = $elm_filename . '_' . $param_name . ':function( value ){' . $callback_body . '}';
						}
					}
				}
			}
			$jscode = '
				window.$usbdata = window.$usbdata || {}; // Single space for data
				window.$usbdata.previewCallbacks = {' . implode( ",", $js_callbacks ) . '};
				window.$usbdata.deferredAssets = ' . json_encode( $deferred_assets ) . ';
			';
			echo '<script>' . $jscode . '</script>';

			// Prints the templates used in the media manager.
			if ( function_exists( 'wp_print_media_templates' ) ) {
				wp_print_media_templates();
			}
		}

		/**
		 * Return the html for highlighting the selected element
		 *
		 * @access public
		 */
		public function after_footer_action() {
			echo '
			<!-- Begin builder hover -->
			<div class="usb-builder-hover">
				<div class="usb-builder-hover-panel">
					<div class="usb-builder-hover-panel-name">Element</div>
					<a class="usb-builder-hover-panel-edit" href="javascript:void(0)" target="_blank">' . __( 'Edit Page Block', 'us' ) . '</a>
					<div class="usb-builder-hover-panel-btn type_copy ui-icon_copy" title="' . esc_attr( us_translate( 'Copy' ) ) . '"></div>
					<div class="usb-builder-hover-panel-btn ui-icon_duplicate" title="' . esc_attr( __( 'Duplicate', 'us' ) ) . '"></div>
					<div class="usb-builder-hover-panel-btn ui-icon_delete" title="' . esc_attr( us_translate( 'Delete' ) ) . '"></div>
				</div>
				<div class="usb-builder-hover-h"></div>
			</div>
			<!-- End builder hover -->';

			$js_init_methods = array();
			foreach ( us_config( 'shortcodes.theme_elements', array(), /* Reload */ TRUE ) as $elm_filename ) {
				if ( $elm_config = us_config( "elements/$elm_filename", array() ) ) {
					// Ignore elements which are not available via condition
					if ( isset( $elm_config['place_if'] ) AND ! $elm_config['place_if'] ) {
						continue;
					}
					$elm_filename = us_get_shortcode_full_name( $elm_filename );
					// Add function for JS initialization of preview from `usb_init_js` option
					if ( ! empty( $elm_config['usb_init_js'] ) ) {
						$js_init_methods[] = $elm_filename . ':function( $elm ){' . (string) $elm_config['usb_init_js'] . '}';
					}

				}
			}
			// This is the output of methods for initializing JS scripts
			$jscode = '
				window.$usbdata = window.$usbdata || {}; // Single space for data
				window.$usbdata.elmsInitJSMethods = {' . implode( ",", $js_init_methods ) . '};
			';
			echo '<script>' . $jscode . '</script>';
		}

		/**
		 * Filter for Page Area ID (like header, footer, etc).
		 * Disables all page areas except own content of edited post for the Builder preview page ...
		 * ... while editing Page Block and Content Template
		 *
		 * Used in 'us_get_page_area_id' fiter
		 *
		 * @param int $area_id Original area ID
		 * @return int / string
		 */
		public function get_page_area_id_filter( $area_id ) {
			if ( in_array( get_post_type(), array( 'us_page_block', 'us_content_template' ) ) ) {
				return '';
			}
			return $area_id;
		}

		/**
		 * Fires when scripts and styles are enqueued for the code editor
		 *
		 * @access public
		 * @param array $settings Settings for the enqueued code editor
		 */
		public function wp_enqueue_code_editor( $settings ) {
			// Remove assets from the general output, they will be loaded as
			// needed at the time of initialization of the code editor
			if (
				us_arr_path( $settings, 'codemirror.mode' ) === 'text/css'
				AND wp_script_is( 'code-editor' )
			) {
				wp_dequeue_script( 'code-editor' );
				wp_dequeue_style( 'code-editor' );
				wp_dequeue_script( 'csslint' );
			}
		}

		/**
		 * This is the hook used to add, remove, or manipulate admin bar items
		 *
		 * @access public
		 * @param WP_Admin_Bar $wp_admin_bar The admin bar
		 */
		public function admin_bar_menu_action( \WP_Admin_Bar $wp_admin_bar ) {
			// Get the post id
			if ( is_front_page() ) {
				$post_id = get_option( 'page_on_front' );

			} elseif ( is_home() ) {
				$post_id = us_get_option( 'posts_page' );

			} elseif ( is_404() ) {
				$post_id = us_get_option( 'page_404' );

			} elseif ( is_search() AND ! is_post_type_archive( 'product' ) ) {
				$post_id = us_get_option( 'search_page' );

			} elseif ( is_singular( $this->get_allowed_edit_post_types() ) ) {
				$post_id = get_queried_object_id();

			} else {
				$post_id = ''; // No ID to disable Live link
			}

			// If there is no ID, then terminate the execution of the method.
			if ( ! is_numeric( $post_id ) ) {
				return;
			}

			$edit_permalink = static::get_edit_permalink( $post_id );
			if ( empty( $edit_permalink ) OR ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			$wp_admin_bar->add_node(
				array(
					'id' => 'edit_us_builder',
					'title' => __( 'Edit Live', 'us' ),
					'href' => $edit_permalink,
					'meta' => array(
						'class' => 'us-builder',
						'html' => '<style>.us-builder > a{font-weight:600!important;color:#23ccaa!important}</style>',
					),
				)
			);
		}

		/**
		 * Post types that are available for editing by the builder
		 *
		 * @return array List of post types names
		 */
		private function get_allowed_edit_post_types() {
			return array_merge(
				array_keys( us_get_public_post_types( array( /* add post types that should not yet be supported by US Builder here */ ) ) ),
				array( 'us_page_block', 'us_content_template' )
			);
		}

		/**
		 * Add a link that will be displayed under the title of the record in the records table in the admin panel
		 *
		 * @access public
		 * @param array $actions
		 * @param \WP_Post $post The current post object.
		 * @return array
		 */
		public function row_actions( $actions, \WP_Post $post ) {
			if (
				in_array( $post->post_type, $this->get_allowed_edit_post_types() )
				AND $post->post_status !== 'trash' // Don't add link for deleted posts.
			) {
				// Add a link to edit with USBuilder
				$edit_url = static::get_edit_permalink( $post->ID );
				$actions['edit_us_builder'] = '<a href="' . esc_url( $edit_url ) . '">' . strip_tags( __( 'Edit Live', 'us' ) ) . '</a>';
			}

			return $actions;
		}

		/**
		 * Add a button that switched editing to US builder
		 *
		 * @access public
		 * @param \WP_Post $post The current post object
		 */
		public function output_builder_switch( \WP_Post $post ) {
			if ( ! in_array( $post->post_type, $this->get_allowed_edit_post_types() ) ) {
				return;
			}
			?>
			<div id="us-builder-switch">
				<a href="<?php echo static::get_edit_permalink( $post->ID ) ?>" class="button button-primary">
					<span><?php echo __( 'Edit Live', 'us' ); ?></span>
				</a>
			</div>
			<?php
		}

		/**
		 * Output custom css for a page
		 *
		 * @access public
		 */
		public function output_post_custom_css() {
			$post_id = self::get_post_id();

			// Get custom css for latest revision
			if ( 'true' === us_arr_path( $_GET, 'preview' ) && wp_revisions_enabled( get_post( $post_id ) ) ) {
				$latest_revision = wp_get_post_revisions( $post_id );
				if ( ! empty( $latest_revision ) ) {
					$array_values = array_values( $latest_revision );
					$post_id = $array_values[0]->ID;
				}
			}

			// Get and output custom css to current page
			$post_custom_css = get_metadata( 'post', $post_id, USBuilder::KEY_CUSTOM_CSS, TRUE );
			$post_custom_css = apply_filters( 'usb_post_custom_css', $post_custom_css, $post_id );
			if ( ! empty( $post_custom_css ) ) {
				echo '<style type="text/css" data-type="' . USBuilder::KEY_CUSTOM_CSS . '">';
				echo wp_strip_all_tags( $post_custom_css );
				echo '</style>';
			}
		}

		/**
		 * Substitution of metadata to preview changes before saving
		 *
		 * @access public
		 * @param null|array|string $value The value get_metadata() should return a single metadata value, or an array of values
		 * @param string $meta_key Meta key.
		 * @return array|null|string The attachment metadata value, array of values, or null
		 */
		public function meta_to_preview( $value, $meta_key ) {
			return us_arr_path( $_REQUEST, "meta.{$meta_key}", /* Default value */ $value );
		}

		/**
		 * The determines if builder page.
		 *
		 * @access public
		 * @return bool TRUE if builder page, FALSE otherwise
		 */
		final static public function is_builder_page() {
			global $pagenow;

			return (
				us_strtolower( basename( $pagenow, '.php' ) ) === 'post'
				AND ! empty( $_REQUEST['post'] )
				AND us_strtolower( us_arr_path( $_REQUEST, 'action' ) ) === static::get_slug()
			);
		}

		/**
		 *  The determines if builder preview page
		 *
		 * @access public
		 * @return bool TRUE if builder page, FALSE otherwise
		 */
		final static public function is_preview_page() {
			$builder_slug = static::get_slug();
			if ( $nonce = us_arr_path( $_REQUEST, $builder_slug ) ) {
				return (bool) wp_verify_nonce( $nonce, $builder_slug );
			}

			return FALSE;
		}

		/**
		 * Get the edit permalink
		 *
		 * @access public
		 * @param int $post_id The post ID
		 * @return string Link to edit page
		 */
		static public function get_edit_permalink( $post_id ) {
			return admin_url( 'post.php?post=' . (int) $post_id . '&action=' . static::get_slug() );
		}

		/**
		 * Remove prefix from shortcode name
		 *
		 * @access private
		 * @param string $shortcode_name The shortcode name
		 * @param string $prefix The shortcode prefix
		 * @return string
		 */
		static public function get_clean_shortcode_name( $shortcode_name, $prefix = 'us_' ) {
			return ( strpos( $shortcode_name, $prefix ) === 0 )
				? substr( $shortcode_name, strlen( $prefix ) )
				: $shortcode_name;
		}

		/**
		 * Initializing the builder page
		 *
		 * @access public
		 */
		public function init_builder_page() {
			$post_id = static::get_post_id();
			$builder_slug = static::get_slug();

			// Key and signature array
			$nonce_args = array(
				$builder_slug => wp_create_nonce( $builder_slug ),
			);

			// Get edit page url
			$edit_page_link = get_edit_post_link( $post_id );

			// Get a link to a page
			$page_link = apply_filters( 'the_permalink', get_permalink( $post_id ) );

			// Get options for jsoncss generator
			$jsoncss_options = us_get_jsoncss_options();
			unset( $jsoncss_options['css_mask'] );

			// Create a list of colors variables, based on CSS vars
			$color_vars = array();
			foreach ( us_config( 'theme-options.colors.fields', array() ) as $color_option => $color_option_params ) {
				// Do not add empty color values
				if ( us_get_color( $color_option, TRUE, FALSE ) === '' ) {
					continue;
				}
				// Do not add variables without "color" prefix in its names
				if ( strpos( $color_option, 'color' ) !== 0 ) {
					continue;
				}
				// Remove "color" prefix
				$color_option = substr( $color_option, strlen( 'color' ) );
				// Add color to general list
				$color_vars[ $color_option ] = us_get_color( $color_option, /* Gradient */ TRUE, /* CSS var */ TRUE );
			}

			// Create a list of global fonts variables, based on CSS vars
			// TODO: avoid hardcode when #2183 will be done
			$font_vars = array();
			foreach ( array( 'body', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $font_name ) {
				$font_vars[ $font_name ] = us_get_font_family( $font_name );
			}

			/**
			 * Get responsive states
			 * @var array
			 */
			$states = (array) us_get_responsive_states();

			// Mask for the title of the edited page
			$admin_page_title = strip_tags( __( 'Edit Live', 'us' ) . ' - %s' );

			/**
			 * The general settings for US Builder
			 *
			 * Note: All parameters and data are used on the front-end in USBuilder
			 * and changing or deleting may break the work of the USBuilder.
			 */
			$usb_settings = (array) USBuilder_Ajax::get_actions();
			$usb_settings += array(
				'_nonce' => USBuilder_Ajax::create_nonce(),

				// Mask for the title of the edited page
				'adminPageTitleMask' => $admin_page_title,

				// Meta key for post custom css
				'keyCustomCss' => USBuilder::KEY_CUSTOM_CSS,

				// Settings for shortcodes
				'shortcode' => array(
					// List of container shortcodes (e.g. [us_hwrapper]...[/us_hwrapper])
					'containers' => array(),
					// List of shortcodes which have inner content (e.g. [us_message]...[/us_message]), but shouldn't be containers
					'edit_content' => array(),
					// List of default values for shortcodes
					'default_values' => array(),
					// The a list of strict relations between shortcodes (separate multiple values with comma)
					'relations' => array(
						'as_parent' => array(
							// Since we introduced a new type of root container at the level of shortcodes and builder,
							// then we will add a rule for it that should be ignored when adding a new element
							self::MAIN_CONTAINER => array(
								'only' => 'vc_row',
							),
						),
					),
					// Elements, when changed or added, which must be updated inclusively from the parent
					'update_parent' => array(),
				),

				// Dynamic assets for the correct operation of fieldsets
				'dynamicFieldsetAssets' => array(),
				// List of elements that have movement along axis X enabled
				'moving_x_direction' => array(),

				// Available shortcodes and their titles
				'elm_titles' => array(),

				// Default placeholder (Used in importing shortcodes)
				'placeholder' => us_get_img_placeholder( 'full', /* src only */TRUE ),

				// Post types for selection in Grid element (Used in importing shortcodes)
				'grid_post_types' => us_grid_available_post_types_for_import(),

				// Templates shortcodes or html
				'template' => us_config( 'us-builder.templates', array() ),

				// Default parameters for AJAX requests
				'ajaxArgs' => array_merge( array( 'post' => $post_id ), $nonce_args ),

				// The set responsive states
				'responsiveStates' => array_keys( $states ),

				// Get breakpoints of responsive states
				'breakpoints' => $states,

				// Settings for the css compiler
				'designOptions' => array_merge(
					$jsoncss_options,
					array(
						// prefix for custom classes when generating styles from design options
						'customPrefix' => 'usb_custom_',
						'fontVars' => $font_vars,
						'colorVars' => $color_vars,
					)
				),

				// Maximum size of changes in the data history
				'maxDataHistory' => (int) us_config( 'us-builder.max_data_history', /* Default */100 ),

				// List of usof field types for which to use throttle
				'useThrottleForFields' => (array) us_config( 'us-builder.use_throttle_for_fields', /* Default */array() ),

				// List of usof field types for which the update interval is used
				'useLongUpdateForFields' => (array) us_config( 'us-builder.use_long_update_for_fields', /* Default */array() ),

				// Link to preview page
				'previewUrl' => add_query_arg( $nonce_args, $page_link ),

				// Columns Layout via CSS grid
				'isGridColumnsLayout' => (bool) us_get_option( 'grid_columns_layout' ),

				// List of selectors for overriding the root node in containers
				'rootContainerSelectors' => array(),
			);

			unset( $vc_row_template, $vc_row_html_template, $handler_add_usbid_to_html );

			if ( is_rtl() ) {
				$this->_body_classes[] = 'rtl';
			}

			$fieldsets = $elms_categories = array();

			// Get all elements available in the theme
			foreach ( us_config( 'shortcodes.theme_elements', array(), TRUE ) as $elm_filename ) {
				if ( $elm_config = us_config( "elements/$elm_filename", array() ) ) {
					// Ignore elements which are not available via condition
					if ( isset( $elm_config['place_if'] ) AND ! $elm_config['place_if'] ) {
						continue;
					}
					$fieldsets[ $elm_filename ] = $elm_config;
					$is_container = ! empty( $elm_config['is_container'] );
					// The list of all containers
					if ( $is_container ) {
						$usb_settings['shortcode']['containers'][] = $elm_filename;
					}
					// Check for a selector to find the root container
					if ( $is_container AND $root_container_selector = us_arr_path( $elm_config, 'usb_root_container_selector' ) ) {
						$usb_settings['rootContainerSelectors'][ $elm_filename ] = (string) $root_container_selector;
					}
					// Elements, when changed or added, which must be updated inclusively from the parent
					if ( ! empty( $elm_config['usb_update_parent'] ) ) {
						$usb_settings['shortcode']['update_parent'][] = $elm_filename;
					}
					// List of elements that have movement along axis X enabled
					if ( ! empty( $elm_config['usb_moving_child_x_direction'] ) ) {
						$usb_settings['moving_child_x_direction'][] = $elm_filename;
					}
					// The list of strict relations between shortcodes
					// All permissions are extracted from WPB settings for compatibility and correct work both on the USBuilder and WPB
					foreach ( array( 'as_parent', 'as_child' ) as $relation ) {
						if (
							isset( $elm_config[ $relation ] ) AND
							! empty( $elm_config[ $relation ] ) AND
							is_array( $elm_config[ $relation ] )
						) {
							$separator = ',';
							foreach ( $elm_config[ $relation ] as $condition => $shortcodes ) {
								if ( $shortcodes = explode( $separator, $shortcodes ) ) {
									foreach ( $shortcodes as &$shortcode ) {
										$shortcode = static::get_clean_shortcode_name( $shortcode );
									}
								}
								if ( is_array( $shortcodes ) ) {
									/**
									 * Checking a condition for correctness or absence ( Required only|except )
									 * @link https://kb.wpbakery.com/docs/developers-how-tos/nested-shortcodes-container/
									 * @var string
									 */
									$condition = in_array( $condition, array( 'only', 'except' ) )
										? $condition
										: 'only';
									// Separate multiple values with comma
									$shortcodes = implode( $separator, $shortcodes );
									$usb_settings['shortcode']['relations'][ $relation ][ $elm_filename ][ $condition ] = $shortcodes;
								}
							}
						}
					}
					// Create elements list
					$elm_filename = us_get_shortcode_full_name( $elm_filename );
					$elms_categories[ us_arr_path( $elm_config, 'category', '' ) ][ $elm_filename ] = array(
						'hide_on_adding_list' => us_arr_path( $elm_config, 'hide_on_adding_list', '' ),
						'icon' => us_arr_path( $elm_config, 'icon', '' ),
						'is_container' => us_arr_path( $elm_config, 'is_container', FALSE ),
						'shortcode_post_type' => us_arr_path( $elm_config, 'shortcode_post_type' ),
						'title' => us_arr_path( $elm_config, 'title', $elm_filename ),
					);
				}
			}

			// Shortcodes that contain inner content for the editor as a value
			foreach ( $fieldsets as $elm_name => $fieldset ) {
				foreach ( us_arr_path( $fieldset, 'params', array() ) as $param_name => $options ) {

					// Get default values for the edited content, if any
					if ( $param_name === 'content' OR $options['type'] === 'editor' ) {
						$elm_name = static::get_clean_shortcode_name( $elm_name );
						$usb_settings['shortcode']['edit_content'][ $elm_name ] = $param_name;
						if ( ! empty( $options['std'] ) ) {
							$usb_settings['shortcode']['default_values'][ $elm_name ][ $param_name ] = $options['std'];
						}
					}

					// Get default values for select
					if ( $options['type'] === 'select' AND empty( $options['std'] ) AND is_array( $options['options'] ) ) {
						$keys = array_keys( $options['options'] );
						if ( $value = us_arr_path( $keys, '0' ) ) {
							$usb_settings['shortcode']['default_values'][ $elm_name ][ $param_name ] = $value;
						}
					}

					// For the Group default value transform array to a string (compatibility with WPBakery builder)
					if ( $options['type'] == 'group' AND ! empty( $options['std'] ) ) {
						$elm_name = static::get_clean_shortcode_name( $elm_name );
						$usb_settings['shortcode']['default_values'][ $elm_name ][ $param_name ] = rawurlencode( json_encode( $options['std'] ) );
					}

					// Remove prefixes needed for compatibility from Visual Composer
					if ( ! empty( $options['type'] ) ) {
						$fieldsets[ $elm_name ]['params'][ $param_name ]['type'] = static::get_clean_shortcode_name( $options['type'] );
					}

					// Determine the availability of dynamic assets for fieldsets
					if ( us_arr_path( $options, 'encoded' ) === TRUE ) {
						$usb_settings['dynamicFieldsetAssets'][ 'codeEditor' ][] = $elm_name;
						$_codeEditor = &$usb_settings['dynamicFieldsetAssets'][ 'codeEditor' ];
						$_codeEditor = array_unique( $_codeEditor );
					}
				}

				// Available shortcodes and their titles
				$usb_settings['elm_titles'][ $elm_name ] = us_arr_path( $fieldset, 'title', $elm_name );

				// All fieldsets that are loaded via AJAX are excluded from the output
				if ( ! us_arr_path( $fieldset, 'usb_preload', FALSE ) ) {
					unset( $fieldsets[ $elm_name ] );
				}
			}

			// Get current post type
			$post_type = get_post_type( $post_id );

			/**
			 * Texts for the builder and different custom messages
			 * Note: Translation keys are duplicated in JavaScript files!
			 * @var array
			 */
			$text_translations = array(
				'all_inner_elms_del' => __( 'All inner elements will also be deleted.', 'us' ),
				'editing_not_supported' => __( 'Editing of this element is not supported.', 'us' ) . sprintf( '<br><a href="%s" target="_blank">%s</a>', $edit_page_link, __( 'Edit page in Backend', 'us' ) ),
				'invalid_data' => us_translate( 'Invalid data provided.' ),
				'page_custom_css' => __( 'Page Custom CSS', 'us' ),
				'page_leave_warning' => us_translate( 'The changes you made will be lost if you navigate away from this page.' ),
				'page_settings' => __( 'Page Settings', 'us' ),
				'paste_row' => __( 'Paste Row/Section', 'us' ),
				'section' => __( 'Section', 'us' ),
			);

			// Notification text depending on the message type when the page is saved
			// For content templated display only notification ...
			if ( $post_type === 'us_page_block' ) {
				$text_translations['page_updated'] = __( 'Page Block updated', 'us' );

			} elseif ( $post_type === 'us_content_template' ) {
				$text_translations['page_updated'] = __( 'Content Template updated', 'us' );

				// ... for posts, pages and other CPT also display link to its page on site
			} else {
				$text_translations['page_updated'] = sprintf(
					'%s <a href="%s" target="_blank">%s</a>',
					us_translate( 'Page updated.' ),
					$page_link,
					us_translate( 'View Page' )
				);
			}


			// Formation of the title of the edited page
			$admin_page_title = sprintf( $admin_page_title, get_the_title( $post_id ) );

			// The formation of the main page
			us_load_template(
				'builder/templates/main', array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'body_class' => implode( ' ', $this->_body_classes ),
					'edit_page_link' => $edit_page_link,
					'elms_categories' => $elms_categories,
					'fieldsets' => $fieldsets,
					'page_link' => $page_link,
					'post_type' => $post_type,
					'text_translations' => $text_translations,
					'title' => $admin_page_title,
					'usb_settings' => $usb_settings,
				)
			);
			exit;
		}
	}
}
