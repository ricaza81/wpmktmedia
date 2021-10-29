<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! class_exists( 'US_Auto_Optimize_Assets' ) ) {
	/**
	 * Class for auto asset optimization
	 */
	class US_Auto_Optimize_Assets {

		/**
		 * The key by which data will be stored in the db
		 *
		 * @var string
		 */
		const TRANSIENT_KEY_NAME = 'us_auto_optimize_assets';

		/**
		 * This is the flag that will cause the script to collect debug data.
		 *
		 * @var bool
		 */
		const DEBUG_MODE = FALSE;

		/**
		 * @var US_Auto_Optimize_Assets
		 */
		protected static $instance;

		/**
		 * Status Data
		 *
		 * @var array
		 */
		private $data = array();

		/**
		 * Request type
		 *
		 * @var string
		 */
		private $type = '';

		/**
		 * Prefix to override keys for actions and filters
		 *
		 * @var string
		 */
		protected $hook_prefix = 'auto_optimize_assets';

		/**
		 * Functions for Defining Dependencies
		 *
		 * @var array
		 */
		protected $callbacks = array(
			// Functions for checking settings and content
			'theme_options' => array(),
			// Functions for checking shortcodes
			'shortcodes' => array(),
			// Functions for checking headers or grid layouts
			'headers_or_grid_layouts' => array(),
			// Functions for checking sidebars and widgets
			'sidebars_widgets' => array(),
		);

		/**
		 * Asset Dependencies
		 *
		 * @var array
		 */
		private $deps_assets = array();

		/**
		 * List of available shortcodes for parsing
		 *
		 * @var array
		 */
		private $available_shortcodes = array(
			// Including WPB WP Widgets by default
			'vc_wp_meta',
			'vc_wp_recentcomments',
			'vc_wp_calendar',
			'vc_wp_pages',
			'vc_wp_tagcloud',
			'vc_wp_custommenu',
			'vc_wp_categories',
			'vc_wp_posts',
			'vc_wp_archives',
			'vc_wp_rss',
		);

		/**
		 * List of predefined post types for parsing
		 */
		private $predefined_post_types = array(
			'us_header',
			'us_grid_layout',
			'us_page_block',
			'us_content_template',
			'us_testimonial',
			'templatera',
		);

		/**
		 * Class initialization
		 */
		public function __construct() {

			/**
			 * Get request type
			 *
			 * @var string
			 * value: string `request` A request that destroys all data and creates a new check
			 * value: string `iteration` Iteration that will continue until all data has been verified
			 */
			$this->type = ! empty( $_REQUEST['type'] )
				? (string) $_REQUEST['type']
				: 'request';

			// With a new request, delete all temporary data
			if ( $this->type === 'request' ) {
				delete_transient( self::TRANSIENT_KEY_NAME );
			}

			// Getting data from the last iteration, or a new array of parameters
			if (
				$this->type !== 'request'
				AND $data = get_transient( self::TRANSIENT_KEY_NAME )
			) {
				$this->data = (array) $data;
			} else {
				$this->data = array(
					// Assets to be included
					'used_assets' => array(),
					// Number of posts or posts processed
					'count_completed_posts' => 0,
					// Number of pages
					'max_num_pages' => 0,
					// Current page
					'paged' => 0,
				);
				// Сallbacks run microtime for debugging
				if ( self::DEBUG_MODE ) {
					$this->data['callbacks_microtime'] = array();
				}
			}

			// Getting the whole list of dependencies from the config
			foreach ( us_config( 'assets', array() ) as $asset_name => $asset_config ) {
				if ( ! empty( $asset_config['auto_optimize_callback'] ) ) {
					// Removing assets that are already found and no longer need to be searched
					if (
						is_array( $this->data['used_assets'] )
						AND in_array( $asset_name, $this->data['used_assets'] )
					) {
						continue;
					}
					if ( is_array( $asset_config['auto_optimize_callback'] ) ) {
						foreach ( $asset_config['auto_optimize_callback'] as $type => $callback ) {
							if ( is_callable( $callback ) AND isset( $this->callbacks[ $type ] ) ) {
								$this->callbacks[ $type ][ $asset_name ] = $callback;
							}
						}
					}
				}
				// List of dependencies that are interconnected
				if ( ! empty( $asset_config['dependencies'] ) ) {
					$this->deps_assets[ $asset_name ] = is_array( $asset_config['dependencies'] )
						? $asset_config['dependencies']
						: array( trim( $asset_config['dependencies'] ) );
				}
			}

			// Get a list of available shortcodes
			foreach ( us_config( 'shortcodes.theme_elements', array() ) as $shortcode ) {
				$this->available_shortcodes[] = 'us_' . $shortcode;
			}

			// Shortcodes with extended design options
			$this->available_shortcodes = array_merge(
				$this->available_shortcodes,
				us_config( 'shortcodes.added_design_options', array() )
			);

			// Run filters for callback functions
			$this->callbacks = apply_filters( 'us_filter_' . $this->hook_prefix . '_callbacks', $this->callbacks );
		}

		/**
		 * @return US_Auto_Optimize_Assets
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Get a list of used assets
		 *
		 * @return array
		 */
		public function get_used_assets() {
			if ( ! $this->is_processing() ) {
				$used_assets = array_unique( us_arr_path( $this->data, 'used_assets', array() ) );
				return apply_filters( 'us_' . $this->hook_prefix . '_get_used_assets', $used_assets );
			}

			return array();
		}

		/**
		 * Get the current page.
		 *
		 * @return int The current page.
		 */
		public function get_current_page() {
			return (int) us_arr_path( $this->data, 'paged', 0 );
		}

		/**
		 * Get process status
		 *
		 * @return bool TRUE - In processing, FALSE - Done
		 */
		public function is_processing() {
			if (
				$this->data['max_num_pages'] == 0
				OR (
					$this->data['max_num_pages']
					AND $this->data['max_num_pages'] <= $this->data['paged']
				)
			) {
				return FALSE;
			}

			return TRUE;
		}

		/**
		 * Check asset dependencies and add to result if necessary
		 *
		 * @param string $asset_name The asset
		 * @return bool
		 */
		private function check_assets_dependencies( $asset_name = '' ) {
			if (
				! empty( $asset_name )
				AND isset( $this->deps_assets[ $asset_name ] )
				AND in_array( $asset_name, $this->data['used_assets'] )
			) {
				$this->data['used_assets'] = array_merge(
					$this->data['used_assets'],
					$this->deps_assets[ $asset_name ]
				);
				foreach ( $this->deps_assets[ $asset_name ] as $item_asset_name ) {
					$this->check_assets_dependencies( $item_asset_name );
				}

				return TRUE;
			}

			return FALSE;
		}

		/**
		 * Start data processing
		 *
		 * @param intval $limit The number of records to be processed per iteration
		 * @return self
		 */
		public function run( $limit = 50 ) {

			// Checking theme settings
			if ( ! empty( $this->callbacks['theme_options'] ) AND empty( $this->data['count_completed_posts'] ) ) {
				global $usof_options;
				usof_load_options_once();

				foreach ( $this->callbacks['theme_options'] as $asset_name => $callback ) {
					if (
						is_callable( $callback )
						AND (
							self::DEBUG_MODE
							OR ! in_array( $asset_name, $this->data['used_assets'] )
						)
					) {

						$callback_data = array(
							'callback' => $callback,
							'debug_path' => $asset_name . '.theme_options',
						);

						/**
						 * @return bool
						 * @var array $callback_data
						 * @var array $usof_options
						 */
						if ( $this->call_user_func( $callback_data, $usof_options ) ) {
							$this->data['used_assets'][] = $asset_name;
							$this->check_assets_dependencies( $asset_name );
						}
					}
				}
				$this->data['count_completed_posts'] += 1;
			}

			// Get a list of post types, that needed to check
			$post_types = array_merge(
				$this->predefined_post_types,
				array_keys( us_get_public_post_types() )
			);

			global $wpdb;
			// Receive a batch of records for processing
			$sql = $wpdb->prepare( "
					SELECT *
					FROM {$wpdb->posts}
					WHERE post_status IN( 'publish', 'private' )
					LIMIT %d, %d;
				",
				(int) ceil( $this->data['paged'] * $limit ),
				$limit
			);

			$posts = $wpdb->get_results( $sql );

			// Getting the total number of posts
			if ( $this->type === 'request' ) {
				$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status IN( 'publish', 'private' );" );
				$this->data['max_num_pages'] = (int) ceil( $count / $limit );
			}

			foreach ( $posts as $post ) {

				$this->data['count_completed_posts'] += 1;

				// Headers and Grid Layouts
				if (
					in_array( $post->post_type, array( 'us_header', 'us_grid_layout' ) )
					AND ! empty( us_get_used_in_locations( $post->ID, FALSE ) )
				) {
					$callbacks = us_arr_path( $this->callbacks, 'headers_or_grid_layouts', array() );

					// Page Blocks and Content templates
				} elseif (
					in_array( $post->post_type, array( 'us_page_block', 'us_content_template' ) )
					AND ! empty( us_get_used_in_locations( $post->ID, FALSE ) )
				) {
					$callbacks = us_arr_path( $this->callbacks, 'shortcodes', array() );

					// Other post types
				} else {
					$callbacks = us_arr_path( $this->callbacks, 'shortcodes', array() );
				}

				// If there are no functions or contents of the post, go to the next post
				if ( empty( $callbacks ) OR empty( $post->post_content ) ) {
					continue;
				}

				// Calling all anonymous functions from the required arguments
				foreach ( $callbacks as $asset_name => $callback ) {
					if (
						is_callable( $callback )
						AND (
							self::DEBUG_MODE
							OR ! in_array( $asset_name, $this->data['used_assets'] )
						)
					) {
						// Anonymous functions for Header or Grid Layout
						if ( in_array( $post->post_type, array( 'us_header', 'us_grid_layout' ) ) ) {
							if ( ! $data = json_decode( $post->post_content, TRUE ) ) {
								continue;
							}
							foreach ( us_arr_path( $data, 'data', array() ) as $elm_name => $elm_options ) {
								if ( ! self::DEBUG_MODE AND in_array( $asset_name, $this->data['used_assets'] ) ) {
									break;
								}
								// Get normal element name without prefix `elm_name:prefix`
								$elm_name = substr( $elm_name, 0, strpos( $elm_name, ':' ) );

								$callback_data = array(
									'callback' => $callback,
									'debug_path' => $asset_name . '.headers_or_grid_layouts',
								);

								/**
								 * The function will be called only if the header or grid layout is used on the site.
								 * @return bool
								 * @var array $callback_data
								 * @var string $elm_name
								 * @var array $elm_options
								 * @var WP_Post $post
								 */
								if ( $elm_name AND $this->call_user_func( $callback_data, $elm_name, $elm_options, $post ) ) {
									$this->data['used_assets'][] = $asset_name;
									$this->check_assets_dependencies( $asset_name );
								}
							}
							// Anonymous functions for Shortcode
						} else {
							if ( empty( $this->available_shortcodes ) OR ! is_array( $this->available_shortcodes ) ) {
								continue;
							}
							// Parsing shortcodes and passing parameters to anonymous functions
							foreach ( $this->available_shortcodes as $shortcode_name ) {
								if ( ! self::DEBUG_MODE AND in_array( $asset_name, $this->data['used_assets'] ) ) {
									break;
								}
								// Get all available shortcodes
								$shortcode_regex = '/' . get_shortcode_regex( array( $shortcode_name ) ) . '/';
								if ( ! preg_match_all( $shortcode_regex, $post->post_content, $matches, PREG_SET_ORDER ) ) {
									// If there are no shortcodes in the post, then we will create an empty array for transferring data
									// in the verification function, so that other data could be checked in the post, for example, metadata
									$matches = array( array_fill( 0, 4, '' ) );
									//continue;
								}
								// Traverses all found shortcodes
								foreach ( $matches as $match ) {
									if ( ! self::DEBUG_MODE AND in_array( $asset_name, $this->data['used_assets'] ) ) {
										break;
									}
									$shortcode_name = $match[2];
									$shortcode_atts = ! empty( $match[3] )
										? shortcode_parse_atts( $match[3] )
										: array();

									$callback_data = array(
										'callback' => $callback,
										'debug_path' => $asset_name . '.shortcodes',
									);

									/**
									 * @return bool
									 * @var array $callback_data
									 * @var string $shortcode_name
									 * @var array $shortcode_atts
									 * @var WP_Post $post
									 */
									if ( $this->call_user_func( $callback_data, $shortcode_name, $shortcode_atts, $post ) ) {
										$this->data['used_assets'][] = $asset_name;
										$this->check_assets_dependencies( $asset_name );
									}
								}
							}
						}
					}
				}
			}

			// Checking for sidebars and widgets
			if (
				is_dynamic_sidebar()
				AND ! empty( $this->callbacks['sidebars_widgets'] )
			) {
				$us_sidebars_keys = array_keys( us_get_sidebars() );
				foreach ( get_option( 'sidebars_widgets', array() ) as $sidebar => $widgets ) {
					if ( ! is_active_sidebar( $sidebar ) OR ! in_array( $sidebar, $us_sidebars_keys ) ) {
						continue;
					}
					foreach ( $widgets as $index => $widget_binding ) {
						// Getting the name of the identifier
						if ( ! preg_match( '/^(.+)\-(\d+)$/', $widget_binding, $matches ) ) {
							continue;
						}
						$widget_name = $matches[1];
						$instance_id = $matches[2];

						// Getting settings for the widget
						$widgets_instance = get_option( 'widget_' . $widget_name, array() );
						if ( ! isset( $widgets_instance[ $instance_id ] ) ) {
							continue;
						}
						foreach ( $this->callbacks['sidebars_widgets'] as $asset_name => $callback ) {
							if (
								is_callable( $callback )
								AND (
									self::DEBUG_MODE
									OR ! in_array( $asset_name, $this->data['used_assets'] )
								)
							) {

								$callback_data = array(
									'callback' => $callback,
									'debug_path' => $asset_name . '.sidebars_widgets',
								);

								/**
								 * @return bool
								 * @var array $callback_data
								 * @var string $widget_name
								 * @var array $widgets_instance [ $instance_id ]
								 * @var integer $instance_id
								 */
								if ( $this->call_user_func( $callback_data, $widget_name, $widgets_instance[ $instance_id ], $instance_id ) ) {
									$this->data['used_assets'][] = $asset_name;
									$this->check_assets_dependencies( $asset_name );
								}
							}
						}
					}
				}
				$this->data['count_completed_posts'] += 1;
			}

			if ( $this->is_processing() ) {
				// Next page
				$this->data['paged'] += 1;
				// Save data
				set_transient( self::TRANSIENT_KEY_NAME, $this->data, 12 * HOUR_IN_SECONDS );
			}

			// The action that will be called every iteration of the check
			do_action( 'us_'. $this->hook_prefix .'_run', $this );

			return $this;
		}

		/**
		 * Run anonymous functions and measure runtime
		 *
		 * @return bool
		 */
		private function call_user_func() {
			$callback_data = func_get_arg( 0 );
			$args = func_get_args();
			unset( $args[0] );

			if (
				empty( $callback_data )
				OR ! is_array( $callback_data )
				OR empty( $args )
			) {
				return FALSE;
			}

			if ( isset( $callback_data[ 'callback' ] ) AND is_callable( $callback_data[ 'callback' ] ) ) {
				if ( self::DEBUG_MODE AND $debug_path = $callback_data[ 'debug_path' ] ) {
					$start_microtime = microtime( TRUE );
				}
				$return = call_user_func_array( $callback_data[ 'callback' ], $args );
				// Saving Debug Information
				if ( self::DEBUG_MODE AND $debug_path ) {
					$end_microtime = microtime( TRUE ) - $start_microtime;
					$prev_time = ! empty( $this->data['callbacks_microtime'][ $debug_path ] )
						? $this->data['callbacks_microtime'][ $debug_path ]
						: 0;
					$this->data['callbacks_microtime'][ $debug_path ] = sprintf( '%f', max( array( $end_microtime, $prev_time ) ) );
				}
			}
			return $return;
		}
	}
}

/**
 * For debugging url:
 * http://host/wp-admin/?us_dev_debug=optimize_assets_init
 * http://host/wp-admin/?us_dev_debug=optimize_assets_callbacks_microtime&type=iteration
 */
if (
	defined( 'US_DEV' )
	AND ! function_exists( 'us_debug_optimize_assets' )
	AND isset( $_GET['us_dev_debug'] )
	AND in_array( $_GET['us_dev_debug'], array( 'optimize_assets_init', 'optimize_assets_callbacks_microtime' ))
) {
	function us_debug_optimize_assets() {
		$instance = US_Auto_Optimize_Assets::instance();
		if ( 'optimize_assets_init' === $_GET['us_dev_debug'] ) {
			$instance->run();
			var_dump( $instance );
		}
		// The output maximum time run callbacks for current content
		if ( 'optimize_assets_callbacks_microtime' === $_GET['us_dev_debug'] ) {
			$data = get_transient( $instance::TRANSIENT_KEY_NAME );
			var_dump( us_arr_path( $data, 'callbacks_microtime', array() ) );
		}
		exit;
	}

	add_action( 'init', 'us_debug_optimize_assets', 10 );
}

if ( wp_doing_ajax() AND ! function_exists( 'us_ajax_auto_optimize_assets' ) ) {
	/**
	 * AJAX request handler for asset optimization
	 */
	function us_ajax_auto_optimize_assets() {
		if ( ! check_ajax_referer( 'us_ajax_auto_optimize_assets', '_nonce', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
			wp_die();
		}

		/* @var $instance US_Auto_Optimize_Assets */
		$instance = US_Auto_Optimize_Assets::instance();
		// Run next step or start
		$instance->run();

		$res = array(
			// Check is processing
			'processing' => $instance->is_processing(),
		);

		// Getting a list of used assets
		if ( ! us_arr_path( $res, 'processing', TRUE ) ) {
			$assets_config = us_config( 'assets', array() );
			$used_assets = $instance->get_used_assets();
			// Forming new value for Optimize JS/CSS assets option
			$assets_value = array();
			foreach ( $assets_config as $component => $component_atts ) {
				if ( in_array( $component, $used_assets) ) {
					$assets_value[ $component ] = 1;
				} else {
					$assets_value[ $component ] = 0;
				}
			}
			$res = array_merge(
				$res, array(
					'message' => __( 'Optimization completed', 'us' ),
					'used_assets' => $used_assets,
					'assets_value' => $assets_value,
				)
			);
		}

		wp_send_json_success( $res );
	}

	add_action( 'wp_ajax_us_auto_optimize_assets', 'us_ajax_auto_optimize_assets', 1 );
}
