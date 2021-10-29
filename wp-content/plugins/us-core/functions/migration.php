<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

class US_Migration {

	/**
	 * @var US_Migration
	 */
	protected static $instance;

	protected $migration_needed_message = '';
	protected $migration_completed_message = '';

	/**
	 * Singleton pattern: US_Migration::instance()->do_something()
	 *
	 * @return US_Migration
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new US_Migration;
		}

		return self::$instance;
	}

	/**
	 * @var string current UpSolution Core plugin version
	 */
	public $core_version = NULL;

	/**
	 * @var array List of migration classes instances $version => $instance
	 */
	public $translators = array();
	public $current_translator;

	protected function __construct() {

		// Get the current DB version
		$db_version = $this->get_db_version();

		// Get the Core version
		if ( defined( 'US_CORE_VERSION' ) ) {
			$this->core_version = US_CORE_VERSION;
		} else {
			$this->core_version = self::get_latest_version_migration();
		}

		if ( $db_version == $this->core_version ) {
			return;
		}

		// Get available migrations as PHP files in the "migrations" directory
		$migrations_path = US_CORE_DIR . 'migrations/' . US_THEMENAME . '/';
		if ( is_dir( $migrations_path ) ) {
			foreach ( glob( $migrations_path . '*.php' ) as $migration_file ) {
				$class = basename( $migration_file, '.php' );
				if ( preg_match( '/^us_migration_([\d\_]+)$/', $class, $matches ) ) {

					// Get migration version from the file name
					$migration_version = str_replace( '_', '.', isset( $matches[1] ) ? $matches[1] : '' );
					if ( ! empty( $migration_version ) AND version_compare( $db_version, $migration_version, '<' ) ) {
						if ( file_exists( $migration_file ) ) {
							include $migration_file;
						}
						if ( class_exists( $class ) ) {
							$this->translators[ $migration_version ] = new $class;
						}
					}
				}
			}
		}

		if ( empty( $this->translators ) ) {

			// Regenerate theme CSS file on update
			if ( $db_version != $this->core_version ) {
				us_generate_asset_files();
			}

			// Set DB version if the theme was just installed
			$this->set_db_version();

			return;
		}

		if ( defined( 'DOING_AJAX' ) AND DOING_AJAX ) {
			// Providing fall-back compatibility for the previous website db versions
			if ( ! ( isset( $_POST['action'] ) AND $_POST['action'] === 'us_migrate' ) ) {
				// Prevent fall-back during actual migration
				$this->provide_fallback();
			}

		} else {
			if ( ! is_admin() ) {
				// Providing fall-back compatibility for the previous website db versions
				$this->provide_fallback();
			} else {
				// Registering migration page
				add_action( 'admin_menu', 'us_add_migration_page', 41 );

				// Notifying about the needed migrations
				add_action( 'admin_notices', array( $this, 'display_migration_needed' ), 1 );
				add_action( 'admin_footer', array( $this, 'output_migration_needed_js' ), 1 );

			}

		}
	}

	/**
	 * Get the theme's current database version
	 *
	 * @return string
	 */
	public function get_db_version() {
		// Getting from global options, not from the theme mods, as it affects content and general options (not theme mods)
		$version = get_option( 'us_db_version' );
		$version = ! empty( $version ) ? $version : US_THEMEVERSION;
		// removing 'betaX' suffix from version
		return preg_replace( '~beta\d~', '', $version );
	}

	/**
	 * Set the current database version
	 *
	 * @param string $version If not set will be updated to the current theme's version
	 */
	public function set_db_version( $version = NULL ) {
		if ( $version === NULL ) {
			$version = ( ! empty( $this->core_version ) ) ? $this->core_version : self::get_latest_version_migration();
		}

		// removing 'betaX' suffix from version
		$version = preg_replace( '~beta\d~', '', $version );

		$current_version = get_option( 'us_db_version' );
		if ( $current_version != $version ) {
			update_option( 'us_db_version', $version, TRUE );
		}
	}

	/**
	 * Latest version of migration
	 *
	 * @var string
	 */
	private static $_latest_version_migration;

	/**
	 * Get the latest migration
	 *
	 * @return string
	 */
	public static function get_latest_version_migration() {
		if ( ! empty( self::$_latest_version_migration ) ) {
			return self::$_latest_version_migration;
		}

		$versions = [];
		$migrations_path = US_CORE_DIR . 'migrations/' . US_THEMENAME . '/';
		if ( is_dir( $migrations_path ) ) {
			foreach ( glob( $migrations_path . '*.php' ) as $migration_file ) {
				if ( preg_match( '/^us_migration_([\d\_]+)$/', basename( $migration_file, '.php' ), $matches ) ) {
					$versions[] = str_replace( '_', '.', isset( $matches[1] ) ? $matches[1] : '' );
				}
			}
		}

		if ( ! empty( $versions ) ) {
			self::$_latest_version_migration = max( $versions );
		}

		return self::$_latest_version_migration;
	}

	public function provide_fallback() {
		global $us_migration_doing_fallback;
		$us_migration_doing_fallback = TRUE;
		// For both frontend and ajax requests
		add_action( 'init', array( $this, 'fallback_theme_options' ), 12 );
		add_filter( 'get_post_metadata', array( $this, 'fallback_meta' ), 10, 4 );
		if ( ! is_admin() ) {
			// For frontend requests only
			add_filter( 'theme_mod_nav_menu_locations', array( $this, 'fallback_menus' ), 5 );
			add_filter( 'the_content', array( $this, 'fallback_content' ), 5 );
			add_filter( 'us_load_header_settings', array( $this, 'fallback_header' ), 9 );
			add_filter( 'us_grid_layout_settings', array( $this, 'fallback_grid_layout' ), 5 );
			add_filter( 'us_page_block_the_content', array( $this, 'fallback_content' ), 5 );
			add_filter( 'us_content_template_the_content', array( $this, 'fallback_content' ), 5 );
		}
		if ( $this->has_widgets_translators() ) {
			add_action( 'init', array( $this, 'fallback_widgets' ), 12 );
		}
	}

	/**
	 * Method that is bound to 'theme_mod_nav_menu_locations' filter to provide live fallback compatibility for menus migrations
	 *
	 * @param string $locations
	 *
	 * @return mixed
	 */
	public function fallback_menus( $locations ) {
		foreach ( $this->translators as $version => $translator ) {
			if ( method_exists( $translator, 'translate_menus' ) ) {
				$translator->translate_menus( $locations );
			}
		}

		return $locations;
	}

	/**
	 * Method for providing live fallback compatibility for options migrations
	 */
	public function fallback_theme_options() {

		global $usof_options;
		usof_load_options_once();

		foreach ( $this->translators as $version => $translator ) {
			if ( method_exists( $translator, 'translate_theme_options' ) ) {
				$translator->translate_theme_options( $usof_options );
			}
		}

		$usof_options = array_merge( usof_defaults(), $usof_options );
	}

	/**
	 * Method for providing live fallback compatibility for metas migrations
	 *
	 * @param mixed $meta_val
	 * @param string $key
	 * @param array $args
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public function fallback_meta( $meta_val, $post_id, $key, $single ) {
		if ( ! get_the_ID() OR $post_id != get_the_ID() OR empty( $key ) ) {
			return $meta_val;
		}

		global $us_migration_current_post_id;
		$us_migration_current_post_id = $post_id;

		$meta = get_post_meta( $post_id );
		$post_type = get_post_type( $post_id );
		foreach ( $this->translators as $version => $translator ) {
			if ( method_exists( $translator, 'translate_meta' ) ) {
				$translator->translate_meta( $meta, $post_type );
			}
		}
		if ( isset( $meta[ $key ] ) ) {
			if ( $single ) {
				return $meta[ $key ][0];
			} else {
				return $meta[ $key ];
			}
		} else {
			return $meta_val;
		}
	}

	/**
	 * Method that is bound to 'the_content' filter to provide live fallback compatibility for content migrations
	 *
	 * @param $content
	 *
	 * @return mixed
	 */
	public function fallback_content( $content ) {
		global $post;
		$postID = ( isset( $post->ID ) ) ? $post->ID : NULL; // TODO check if we really use passed post ID
		foreach ( $this->translators as $version => $translator ) {
			if ( method_exists( $translator, 'translate_content' ) ) {
				$translator->translate_content( $content, $postID );
			}
		}

		return $content;
	}

	/**
	 * Method for providing live fallback compatibility for header
	 *
	 * @param array $header_settings
	 * @return array
	 */
	public function fallback_header( $header_settings ) {
		foreach ( $this->translators as $version => $translator ) {
			if ( method_exists( $translator, 'translate_header_settings' ) ) {
				$translator->translate_header_settings( $header_settings );
			}
		}

		return $header_settings;
	}

	/**
	 * Method for providing live fallback compatibility for grid layout
	 *
	 * @param array $grid_layout_settings
	 * @return array
	 */
	public function fallback_grid_layout( $grid_layout_settings ) {
		foreach ( $this->translators as $version => $translator ) {
			if ( method_exists( $translator, 'translate_grid_layout_settings' ) ) {
				$translator->translate_grid_layout_settings( $grid_layout_settings );
			}
		}

		return $grid_layout_settings;
	}

	public function has_widgets_translators() {
		foreach ( $this->translators as $version => $translator ) {
			if ( method_exists( $translator, 'translate_widgets' ) ) {
				return TRUE;
			}
		}

		return FALSE;
	}

	public $sidebars_widgets = array();
	public $widgets_instances = array();

	public function fallback_widgets() {
		$this->sidebars_widgets = array();
		$this->widgets_instances = array();
		$filters_added = array();
		foreach ( get_option( 'sidebars_widgets', array() ) as $sidebar => $widgets ) {
			if ( ! is_array( $widgets ) ) {
				// For non-widgets additional option data
				$this->sidebars_widgets[ $sidebar ] = $widgets;
				continue;
			}
			foreach ( $widgets as $index => $widget_binding ) {
				if ( ! preg_match( '@^(.+)\-(\d+)$@', $widget_binding, $matches ) ) {
					continue;
				}
				$widget_name = $original_widget_name = $matches[1];
				$instance_id = $matches[2];

				$this->widgets_instances[ $widget_name ] = get_option( 'widget_' . $widget_name, array() );
				if ( ! isset( $this->widgets_instances[ $widget_name ][ $instance_id ] ) ) {
					continue;
				}

				foreach ( $this->translators as $version => $translator ) {
					if ( ! method_exists( $translator, 'translate_widgets' ) ) {
						continue;
					}

					if ( $translator->translate_widgets( $widget_name, $this->widgets_instances[ $widget_name ][ $instance_id ] ) ) {
						if ( $widget_name != $original_widget_name ) {
							if ( ! isset( $this->widgets_instances[ $widget_name ] ) ) {
								// Widget name has changed
								$this->widgets_instances[ $widget_name ] = isset( $this->widgets_instances[ $original_widget_name ] ) ? $this->widgets_instances[ $original_widget_name ] : array();
							} else {
								$this->widgets_instances[ $widget_name ][ $instance_id ] = $this->widgets_instances[ $original_widget_name ][ $instance_id ];
							}
						}
						if ( ! in_array( $widget_name, $filters_added ) ) {
							// Binding each widget once
							add_filter(
								'pre_option_widget_' . $widget_name, array(
									$this,
									'fallback_widgets_instance',
								)
							);
							$filters_added[] = $widget_name;
						}
					}
				}

				$widgets[ $index ] = $widget_name . '-' . $instance_id;
			}
			$this->sidebars_widgets[ $sidebar ] = $widgets;
		}

		if ( count( $filters_added ) > 0 ) {
			add_filter( 'option_sidebars_widgets', array( $this, 'fallback_sidebars_widgets' ) );
		}
	}

	public function fallback_sidebars_widgets() {
		return $this->sidebars_widgets;
	}

	public function fallback_widgets_instance() {
		if ( ! preg_match( '@^pre_option_widget_(.+)$@', current_filter(), $matches ) OR ! isset( $this->widgets_instances[ $matches[1] ] ) ) {
			return FALSE;
		}

		return $this->widgets_instances[ $matches[1] ];
	}

	public function display_migration_needed() {
		global $pagenow;
		if ( ! empty( $pagenow ) AND $pagenow == 'admin.php' AND ! empty( $_GET['page'] ) AND $_GET['page'] == 'us-update-content' ) {
			return;
		}

		if ( $this->migration_needed_message != '' ) {
			$output = $this->migration_needed_message;
		} else {
			$output = '<div class="us-migration">';
			$output .= '<h2>Your website content needs to be changed to be compatible with ' . US_THEMENAME . ' ' . US_THEMEVERSION . '</h2>';

			// Prevent migration if PHP version < 5.6, PHP_VERSION_ID available from PHP 5.2.7
			if ( ! defined( 'PHP_VERSION_ID' ) OR PHP_VERSION_ID < 50600 ) {
				$output .= '<p>New ' . US_THEMENAME . ' is not compatible with PHP versions prior to 5.6. ';
				$output .= 'Update PHP to the latest supported version to proceed. ';
				$output .= '<a href="https://wordpress.org/support/update-php/" target="_blank" rel="noopener">Learn more about updating PHP</a></p>';
				$output .= '<p><input type="submit" value="Update website content" class="button" disabled></p>';

				// Prevent migration access when "Wordfence" or "iThemes Security" plugins are active
			} elseif ( class_exists( 'wordfence' ) OR class_exists( 'ITSEC_Core' ) ) {
				$output .= '<p>To proceed updating ';
				if ( get_current_screen()->id != 'plugins' ) {
					$output .= 'go to <a href="' . admin_url( 'plugins.php' ) . '">plugins page</a> and ';
				}
				$output .= 'deactivate <strong>';
				$output .= ( class_exists( 'wordfence' ) ) ? 'Wordfence Security' : 'iThemes Security';
				$output .= '</strong> plugin.</p>';
				$output .= '<p><input type="submit" value="Update website content" class="button button-primary" disabled></p>';
			} else {
				$output .= '<p><label><input type="checkbox" name="allow_migration" id="allow_migration"> I made a full backup of my website</p>';
				$output .= '<p><input type="submit" value="Update website content" class="button button-primary" id="migration-start"></p>';
			}
			$output .= '</div>';
			$output .= '<style>
				.us-migration {
					padding: 20px 20px 10px;
					margin-left: -20px;
					background-color: #ffe14c;
					color: #000;
					}
				.us-migration h2 {
					margin: 0 0 10px;
					color: inherit;
					}
				.us-migration p {
					font-size: 15px;
					}
				.us-migration .button {
					font-size: 15px;
					padding: 5px 20px;
					}
			</style>';
		}
		echo $output;
	}

	public function output_migration_needed_js() {

		$output = '<script type="text/javascript">';
		$output .= 'jQuery(function($){
			$(".us-migration input.button").attr("disabled", "");
			$(".error.us-migration.for-hb").hide();
			$("#allow_migration").removeAttr("checked").on("click", function(){
				if ($(this).is(":checked")){
					$(".us-migration input.button").removeAttr("disabled");
				}else{
					$(".us-migration input.button").attr("disabled", "");
				}
			});
			$(".us-migration input.button").click(function(){
				if ( ! $(".us-migration input[type=\"checkbox\"]").is(":checked")) return;
				$(this).val("Updating...");
				$(".us-migration input.button").attr("disabled", "");
				var locationHref = window.location.href;console.log(locationHref);
				location.assign("' . admin_url( 'admin.php?page=us-update-content&migration-start=1' ) . '");
			});
		});';
		$output .= '</script>';

		echo $output;
	}

	public function migration_completed_message() {
		global $help_portal_url;
		if ( $this->migration_completed_message != '' ) {
			$output = $this->migration_completed_message;
		} else {
			$output = '<h3>Update to ' . US_THEMENAME . ' ' . US_THEMEVERSION . ' is completed.<br>';
			$output .= 'Now check your website. If you notice some issues, <a href="' . $help_portal_url . '/' . strtolower( US_THEMENAME ) . '/tickets/" target="_blank">go to the support</a>.</h3>';
		}

		return $output;
	}

	/**
	 * Batch Data Migration
	 * @param int $num_posts Number of posts in one run
	 * @return float
	 */
	public function perform_migration_step( $num_posts = 50 ) {
		us_set_time_limit();
		$progress = 0;
		$migrations = array(
			'migrate_theme_options',
			'migrate_content_and_meta',
			'migrate_menus',
			'migrate_widgets',
			'migrate_wpb_templates',
		);

		// List post types, which can be migrated
		$predefined_posts_types = array_merge(
			array(
				'us_header',
				'us_grid_layout',
				'us_page_block',
				'us_content_template',
				'us_testimonial',
				'templatera',
			),
			array_keys( us_get_public_post_types() )
		);
		$query_post_args = array(
			'posts_per_page' => - 1,
			'post_type' => $predefined_posts_types,
			'post_status' => 'any',
			'numberposts' => - 1,
			'orderby' => 'ID',
			'order' => 'ASC',
		);

		if ( ! defined( 'US_DEV' ) OR ! US_DEV ) {
			ob_start();
		}

		$count_posts = 0;
		foreach ( $predefined_posts_types as $post_type ) {
			$posts_count = wp_count_posts( $post_type );
			foreach ( get_post_stati() as $post_status ) {
				if ( ! empty( $posts_count->$post_status ) ) {
					$count_posts += $posts_count->$post_status;
				}
			}
		}
		unset( $predefined_posts_types );

		$translators_keys = array_keys( $this->translators );

		if ( ! $data = get_transient( 'us_migrate_data' ) ) {
			$data = array(
				'version' => $translators_keys[0],
				'current_step' => 0,
				'method_index' => - 1,
				'count_posts' => $count_posts,
				'posts_args' => array(
					'offset' => 0,
					'posts_per_page' => (int) $num_posts,
				),
			);
		}

		$posts_index = array_search( 'migrate_content_and_meta', $migrations, TRUE );
		if ( $data['method_index'] != $posts_index OR $data['posts_args']['offset'] >= $data['count_posts'] ) {
			$data['method_index'] += 1;
		}

		$data['method_name'] = isset( $data['method_index'], $migrations[ $data['method_index'] ] )
			? $migrations[ $data['method_index'] ]
			: $migrations[0];

		if ( ! empty( $data['method_name'] ) AND method_exists( $this, $data['method_name'] ) ) {

			$post_steps = (int) ceil( $data['count_posts'] / $num_posts );
			$methods_count = count( $migrations ) - 1;
			$translator_steps = ( $post_steps + $methods_count );
			$count_all_steps = ( $translator_steps * count( $this->translators ) );
			$version = $translators_keys[ (int) floor( $data['current_step'] / $translator_steps ) ];
			$data['current_step'] += 1;

			// Next migration
			if ( $version > $data['version'] ) {
				$data['method_index'] = 0;
				$data['posts_args']['offset'] = 0;
			}

			$this->current_translator = $this->translators[ $version ];
			if ( $data['method_name'] == 'migrate_content_and_meta' ) {
				$query_post_args = array_merge( $query_post_args, $data['posts_args'] );
				foreach ( get_posts( $query_post_args ) as $post ) {
					if ( in_array( $post->post_type, array( 'us_header', 'us_grid_layout' ) ) ) {
						$this->migrate_header_and_grid_layout( $post );
					} else {
						$this->migrate_content_and_meta( $post );
					}

				}
			} else {
				call_user_func( array( $this, $data['method_name'] ) );
			}

			$data['version'] = $version;

			// The next portion of posts
			if ( $data['method_name'] == 'migrate_content_and_meta' ) {
				$data['posts_args']['offset'] += $num_posts;
			}

			$progress = ( 100 / $count_all_steps ) * $data['current_step'];
		}

		delete_transient( 'us_migrate_data' );
		if ( $progress < 100 ) {
			set_transient( 'us_migrate_data', $data, 12 * HOUR_IN_SECONDS );
		} else {
			// Regenerate theme asset file after migration
			us_generate_asset_files();
			$this->set_db_version();
		}

		if ( ! defined( 'US_DEV' ) OR ! US_DEV ) {
			ob_end_clean();
		}

		$res = array(
			'progress' => (float) sprintf( '%0.5f', $progress ),
		);

		return defined( 'US_DEV' )
			? array_merge( array( 'debug' => $data ), $res )
			: $res;
	}

	public function migrate_menus() {
		$locations = get_theme_mod( 'nav_menu_locations' );

		$menus_changed = FALSE;
		if ( method_exists( $this->current_translator, 'translate_menus' ) ) {
			$menus_changed = ( $this->current_translator->translate_menus( $locations ) OR $menus_changed );
		}

		if ( $menus_changed ) {
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}

	public function migrate_theme_options() {
		// Getting Options
		global $usof_options;
		$updated_options = $usof_options;

		usof_load_options_once();

		$options_changed = FALSE;
		if ( method_exists( $this->current_translator, 'translate_theme_options' ) ) {
			$options_changed = ( $this->current_translator->translate_theme_options( $updated_options ) OR $options_changed );
		}
		if ( $options_changed ) {
			// Filling the missed options with default values
			$updated_options = array_merge( usof_defaults(), $updated_options );
			// Saving the changed options
			ob_start();
			usof_save_options( $updated_options );
			ob_end_clean();
		}
	}

	public function migrate_widgets() {
		$sidebars_widgets = array();
		$widgets_instances = array();
		// Name of changed widgets
		$changed_widgets = array();
		foreach ( get_option( 'sidebars_widgets', array() ) as $sidebar => $widgets ) {
			if ( ! is_array( $widgets ) ) {
				// For non-widgets additional option data
				$sidebars_widgets[ $sidebar ] = $widgets;
				continue;
			}
			foreach ( $widgets as $index => $widget_binding ) {
				if ( ! preg_match( '@^(.+)\-(\d+)$@', $widget_binding, $matches ) ) {
					continue;
				}
				$widget_name = $original_widget_name = $matches[1];
				$instance_id = $matches[2];

				if ( ! isset( $widgets_instances[ $widget_name ] ) ) {
					$widgets_instances[ $widget_name ] = get_option( 'widget_' . $widget_name, array() );
				}

				if ( ! isset( $widgets_instances[ $widget_name ][ $instance_id ] ) ) {
					continue;
				}

				if ( ! method_exists( $this->current_translator, 'translate_widgets' ) ) {
					continue;
				}

				$widget_instance = $widgets_instances[ $widget_name ][ $instance_id ];

				if ( $this->current_translator->translate_widgets( $widget_name, $widget_instance ) ) {
					if ( $widget_name != $original_widget_name ) {
						if ( ! isset( $widgets_instances[ $widget_name ] ) ) {
							$widgets_instances[ $widget_name ] = get_option( 'widget_' . $widget_name, array() );
						}
						if ( in_array( $instance_id, array_keys( $widgets_instances[ $widget_name ] ) ) ) {
							$instance_id = max( array_keys( $widgets_instances[ $widget_name ] ) ) + 1;
						}
						delete_option( 'widget_' . $original_widget_name );
					}
					$widgets_instances[ $widget_name ][ $instance_id ] = $widget_instance;

					if ( ! in_array( $widget_name, $changed_widgets ) ) {
						$changed_widgets[] = $widget_name;
					}
				}

				$widgets[ $index ] = $widget_name . '-' . $instance_id;
			}
			$sidebars_widgets[ $sidebar ] = $widgets;
		}

		if ( count( $changed_widgets ) > 0 ) {
			update_option( 'sidebars_widgets', $sidebars_widgets );
			foreach ( $changed_widgets as $widget_name ) {
				update_option( 'widget_' . $widget_name, $widgets_instances[ $widget_name ], TRUE );
			}
		}
	}

	public function migrate_header_and_grid_layout( $post ) {
		if (
			$post instanceof WP_Post
			AND ! empty( $post->post_content )
			AND strpos( $post->post_content, '{' ) === 0
		) {
			try {
				$settings = json_decode( $post->post_content, TRUE );
				$settings_changed = FALSE;
				$translate_method_name = ( $post->post_type == 'us_header' ) ? 'translate_header_settings' : 'translate_grid_layout_settings';
				if ( method_exists( $this->current_translator, $translate_method_name ) AND
					isset( $settings['data'] ) and is_array( $settings['data'] ) ) {
					$settings_changed = ( $this->current_translator->$translate_method_name( $settings, $post->ID ) OR $settings_changed );
				}
				if ( $settings_changed ) {
					ob_start();
					wp_update_post(
						array(
							'ID' => $post->ID,
							'post_content' => str_replace( "\\'", "'", json_encode( wp_slash( $settings ), JSON_UNESCAPED_UNICODE ) ),
						)
					);
					ob_end_clean();
				}
			}
			catch ( Exception $e ) {
			}
		}
	}

	/**
	 * @param WP_Post $post
	 */
	public function migrate_content_and_meta( $post ) {
		global $vc_manager, $us_migration_current_post_id;
		if ( class_exists( 'Vc_Manager' ) AND isset( $vc_manager ) ) {
			$vc = $vc_manager->vc();
		}

		$us_migration_current_post_id = $post->ID;
		$content = $post->post_content;
		$content_changed = FALSE;

		if ( method_exists( $this->current_translator, 'translate_content' ) ) {
			$content_changed = ( $this->current_translator->translate_content( $content, $post->ID ) OR $content_changed );
		}

		if ( $content_changed ) {
			wp_update_post(
				array(
					'ID' => $post->ID,
					'post_content' => $content,
				)
			);
			if ( isset( $vc ) AND method_exists( $vc, 'buildShortcodesCustomCss' ) ) {
				$vc->buildShortcodesCustomCss( $post->ID );
			}
		}

		// Translating Meta fields
		$meta_changed = FALSE;
		$original_meta = $meta = get_post_meta( $post->ID );
		if ( method_exists( $this->current_translator, 'translate_meta' ) ) {
			$meta_changed = ( $this->current_translator->translate_meta( $meta, $post->post_type ) OR $meta_changed );
		}
		if ( $meta_changed ) {
			$deleted_meta = array_diff_key( $original_meta, $meta );
			foreach ( $meta as $key => $value ) {
				if ( ! isset( $original_meta[ $key ] ) OR $original_meta[ $key ] !== $value ) {
					update_post_meta( $post->ID, $key, $value[0] );
				}
			}
			foreach ( $deleted_meta as $key => $value ) {
				delete_post_meta( $post->ID, $key );
			}

		}
	}

	/**
	 * Migrate WPB Templates
	 *
	 * @return bool
	 */
	public function migrate_wpb_templates() {
		$changed = FALSE;
		if ( $wpb_js_templates = get_option( 'wpb_js_templates' ) AND is_array( $wpb_js_templates ) ) {
			foreach ( $wpb_js_templates as &$temp ) {
				if ( ! empty( $temp['template'] ) AND method_exists( $this->current_translator, 'translate_content' ) ) {
					if ( $this->current_translator->translate_content( $temp['template'] ) ) {
						$changed = TRUE;
					}
				}
			}
			unset( $temp );
		}
		if ( $changed ) {
			update_option( 'wpb_js_templates', $wpb_js_templates );
		}

		return $changed;
	}
}

abstract class US_Migration_Translator {

	/**
	 * @var bool Possibly dangerous translation that needs to be migrated manually (don't use this too often)
	 */
	public $should_be_manual = FALSE;

	/**
	 * @var string Extra css that will be appended to the end of the body
	 */
	public $_extra_css = '';

	public function __construct() {
		add_action( 'wp_footer', array( $this, 'append_css' ), 20 );
	}

	public function append_css() {
		if ( ! empty( $this->_extra_css ) ) {
			echo '<style id="' . get_class( $this ) . '">' . $this->_extra_css . '</style>';
		}
	}

	/**
	 *
	 *
	 * @param array $locations
	 * @param array $rules
	 *
	 * @return bool
	 */
	protected function _translate_menus( &$locations, $rules ) {
		$changed = FALSE;
		// Obtaining the valid menu ids
		$menu_ids = wp_get_nav_menus( array( 'fields' => 'ids' ) );
		foreach ( $rules as $old => $new ) {
			if ( isset( $locations[ $old ] ) AND in_array( $locations[ $old ], $menu_ids ) ) {
				$locations[ $new ] = $locations[ $old ];
				unset( $locations[ $old ] );
				$changed = TRUE;
			}
		}

		return $changed;
	}

	protected function _translate_theme_options( &$options, $rules ) {
		$changed = FALSE;
		foreach ( $rules as $option => $rule ) {
			if ( isset( $options[ $option ] ) ) {
				if ( isset( $rule['values'] ) ) {
					foreach ( $rule['values'] as $old_value => $new_value ) {
						if ( $options[ $option ] == $old_value ) {
							$options[ $option ] = $new_value;
							$changed = TRUE;
							break;
						}
					}
				}

				if ( isset( $rule['new_name'] ) ) {
					if ( ! is_array( $rule['new_name'] ) ) {
						$rule['new_name'] = array( $rule['new_name'] );
					}
					$option_value = $options[ $option ];
					unset( $options[ $option ] );
					foreach ( $rule['new_name'] as $new_name ) {
						if ( ! isset( $options[ $new_name ] ) ) {
							$options[ $new_name ] = $option_value;
						}
					}
					$changed = TRUE;
				}
			}
		}

		return $changed;
	}

	protected function _translate_meta( &$meta, $post_type, $rules ) {
		$changed = FALSE;
		foreach ( $rules as $meta_name => $rule ) {
			if ( isset( $meta[ $meta_name ] ) AND in_array( $post_type, $rule['post_types'] ) ) {
				if ( isset( $rule['values'] ) ) {
					foreach ( $rule['values'] as $old_value => $new_value ) {
						if ( $meta[ $meta_name ][0] == $old_value ) {
							$changed = TRUE;
							$meta[ $meta_name ][0] = $new_value;
							break;
						}
					}
				}

				if ( isset( $rule['new_name'] ) ) {
					if ( ! is_array( $rule['new_name'] ) ) {
						$rule['new_name'] = array( $rule['new_name'] );
					}
					$meta_value = $meta[ $meta_name ];
					//unset( $meta[$meta_name] );
					foreach ( $rule['new_name'] as $new_name ) {
						if ( ! isset( $meta[ $new_name ] ) ) {
							$changed = TRUE;
							$meta[ $new_name ] = $meta_value;
						}
					}
				}
			}
		}

		return $changed;
	}

	public function translate_params( &$params, $rules ) {
		$params_changed = FALSE;

		foreach ( $rules as $param => $rule ) {
			if ( isset( $params[ $param ] ) ) {
				if ( isset( $rule['values'] ) ) {
					foreach ( $rule['values'] as $old_value => $new_value ) {
						if ( $params[ $param ] == $old_value ) {
							if ( $new_value === NULL ) {
								unset( $params[ $param ] );
							} else {
								$params[ $param ] = $new_value;
							}
							$params_changed = TRUE;
							break;
						}
					}
				}

				if ( isset( $params[ $param ] ) AND isset( $rule['new_name'] ) ) {
					if ( $rule['new_name'] !== NULL ) {
						$params[ $rule['new_name'] ] = $params[ $param ];
					}
					unset( $params[ $param ] );
					$params_changed = TRUE;
				}
			} elseif ( isset( $rule['values'] ) AND isset( $rule['values'][ NULL ] ) ) {
				$params[ $param ] = $rule['values'][ NULL ];
			}
		}

		return $params_changed;
	}

	public function _translate_content( &$content ) {
		$content_changed = FALSE;

		// Searching for all shortcodes
		$shortcode_pattern = $this->get_shortcode_regex();
		if ( preg_match_all( '/' . $shortcode_pattern . '/s', $content, $matches ) ) {
			if ( count( $matches[2] ) ) {
				foreach ( $matches[2] as $i => $shortcode_name ) {
					$shortcode_content_changed = $shortcode_changed = FALSE;
					$shortcode_string = $matches[0][ $i ];
					$shortcode_params_string = $matches[3][ $i ];
					$shortcode_content = $matches[5][ $i ];

					// Check if params of this shortcode should be translated
					$translate_shortcode_method = 'translate_' . $shortcode_name;
					if ( method_exists( $this, $translate_shortcode_method ) ) {
						if ( ! empty( $shortcode_params_string ) ) {
							$shortcode_params = shortcode_parse_atts( $shortcode_params_string );
						} else {
							$shortcode_params = array();
						}
						$shortcode_changed = $this->$translate_shortcode_method( $shortcode_name, $shortcode_params, $shortcode_content );
						// If params should be changed, remaking params string
						if ( $shortcode_changed ) {
							$shortcode_params_string = '';
							foreach ( $shortcode_params as $param => $value ) {
								$shortcode_params_string .= ' ' . $param . '="' . $value . '"';
							}
						}
					}

					// Using recursion to translate shortcodes inside found shortcode
					if ( ! empty( $shortcode_content ) ) {
						$shortcode_content_changed = $this->translate_content( $shortcode_content );
					}

					// If it is a text containing pricing - leave just pricing
					if ( get_class( $this ) == 'us_migration_2_0' ) {
						if ( $shortcode_name == 'vc_column_text' AND preg_match( '/^' . $this->get_shortcode_regex( array( 'us_pricing' ) ) . '$/s', trim( $shortcode_content ) ) ) {
							$content = str_replace( $shortcode_string, $shortcode_content, $content );
							$content_changed = TRUE;
							continue;
						}
					}

					// If content or params of the shortcode have been changed, making new shortcode string and replacing it in the content
					if ( $shortcode_content_changed OR $shortcode_changed ) {
						$new_shortcode_string = '[' . $shortcode_name . $shortcode_params_string . ']';
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

	public $shortcode_tagnames = NULL;

	public function get_shortcode_regex( $tagnames = NULL ) {

		if ( empty( $tagnames ) OR ! is_array( $tagnames ) ) {
			if ( $this->shortcode_tagnames === NULL ) {
				// Retrieving list of possible shortcode translations from the class methods
				$this->shortcode_tagnames = array();
				foreach ( get_class_methods( $this ) as $method_name ) {
					if ( substr( $method_name, 0, 10 ) != 'translate_' ) {
						continue;
					}
					$tagname = substr( $method_name, 10 );
					if ( ! in_array( $tagname, explode( '|', 'menus|params|content|theme_options|meta|widgets' ) ) ) {
						$this->shortcode_tagnames[] = $tagname;
					}
				}
			}
			$tagnames = $this->shortcode_tagnames;
		}

		$tagregexp = implode( '|', array_map( 'preg_quote', $tagnames ) );

		// WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
		// Also, see shortcode_unautop() and shortcode.js.
		$this->shortcode_regex = '\\[' // Opening bracket
			. '(\\[?)' // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
			. "($tagregexp)" // 2: Shortcode name
			. '(?![\\w-])' // Not followed by word character or hyphen
			. '(' // 3: Unroll the loop: Inside the opening shortcode tag
			. '[^\\]\\/]*' // Not a closing bracket or forward slash
			. '(?:' . '\\/(?!\\])' // A forward slash not followed by a closing bracket
			. '[^\\]\\/]*' // Not a closing bracket or forward slash
			. ')*?' . ')' . '(?:' . '(\\/)' // 4: Self closing tag ...
			. '\\]' // ... and closing bracket
			. '|' . '\\]' // Closing bracket
			. '(?:' . '(' // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
			. '[^\\[]*+' // Not an opening bracket
			. '(?:' . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
			. '[^\\[]*+' // Not an opening bracket
			. ')*+' . ')' . '\\[\\/\\2\\]' // Closing shortcode tag
			. ')?' . ')' . '(\\]?)'; // 6: Optional second closing brocket for escaping shortcodes: [[tag]]

		return $this->shortcode_regex;
	}

}

global $us_migration;
$us_migration = US_Migration::instance();
