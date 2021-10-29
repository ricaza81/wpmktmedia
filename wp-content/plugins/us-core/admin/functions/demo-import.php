<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Demo Import admin page
 */

global $help_portal_url, $help_portal_preview_url, $help_portal_api_url;

$help_portal_preview_url = $help_portal_url . '/uploads/demos/';
$help_portal_preview_url .= ( defined( 'US_ACTIVATION_THEMENAME' ) )
	? trailingslashit( strtolower( US_ACTIVATION_THEMENAME ) )
	: trailingslashit( strtolower( US_THEMENAME ) );

$help_portal_api_url = $help_portal_url . '/us.api/download_demo/';
$help_portal_api_url .= ( defined( 'US_ACTIVATION_THEMENAME' ) )
	? strtolower( US_ACTIVATION_THEMENAME )
	: strtolower( US_THEMENAME );

add_action( 'admin_menu', 'us_add_demo_import_page', 30 );
function us_add_demo_import_page() {
	add_submenu_page(
		'us-theme-options',
		__( 'Demo Import', 'us' ),
		__( 'Demo Import', 'us' ),
		'manage_options',
		'us-demo-import',
		'us_demo_import'
	);
}

// Page
function us_demo_import() {
	$config = us_get_demo_import_config();
	if ( empty( $config ) ) {
		echo '<div style="text-align: center; padding-top: 5%;"><h2>'. us_translate( 'Connection lost or the server is busy. Please try again later.' ) . '</h2></div>';
		return;
	}
	if ( count( $config ) < 1 ) {
		return;
	}
	reset( $config );

	// Deactivate WP importer plugin to avoid conflicts
	if ( is_plugin_active( 'wordpress-importer/wordpress-importer.php' ) ) {
		deactivate_plugins( 'wordpress-importer/wordpress-importer.php' );
	}

	$update_notification = '';
	$update_themes = get_site_transient( 'update_themes' );
	if ( ! empty( $update_themes->response ) AND isset( $update_themes->response[ US_THEMENAME ] ) ) {
		$update_notification = sprintf(
			 __( 'Some of demo data may be imported incorrectly, because you are using outdated %s version. %sUpdate the theme%s to import demos without possible issues.', 'us' ),
			 US_THEMENAME,
			 '<a href="' . admin_url( 'themes.php' ) . '">',
			 '</a>'
		);
	}

	?>
	<form class="w-importer" action="?page=us-demo-import" method="post">
		<h1 class="us-admin-title">
			<?php _e( 'Choose the demo for import', 'us' ) ?>
		</h1>
		<p class="us-admin-subtitle">
			<?php _e( 'The images used in live demos will be replaced by placeholders due to copyright/license reasons.', 'us' ) ?>
		</p>
		<p class="us-admin-subtitle">
			<strong><?php echo $update_notification; ?></strong>
		</p>
		<div class="w-importer-list">
			<?php echo us_render_for_importer( $config ) ?>
		</div>
		<?php if ( ! (
				get_option( 'us_license_activated', 0 )
				OR get_option( 'us_license_dev_activated', 0 )
				OR defined( 'US_DEV' ) ) ) { ?>
			<div class="us-screenlock">
				<div>
					<?php echo sprintf(
						__( '<a href="%s">Activate the theme</a> to unlock Demo Import', 'us' ),
						admin_url( 'admin.php?page=us-home#activation' )
					) ?>
				</div>
			</div>
		<?php } ?>
	</form>
	<script>
		;( function( $, undefined ) {
			"use strict";
			var Importer = function( container ) {
				// Elements
				this.$window = $( window );
				this.$document = $( document );
				this.$form = $( container );
				this.$list = $( '.w-importer-list', this.$form );

				// Variables
				this.data = this.$list.data() || {};

				// Watch events
				this.$form.on( 'submit', this.importDemo.bind( this ) );
				this.init.call( this, this.$list.find( '.w-importer-item' ) );
			};
			// Export API
			Importer.prototype = {
				/**
				 * State when run import
				 */
				importRunning: false,
				/**
				 * Event initialization for import demo
				 *
				 * @param object $items jQuery collections
				 */
				init: function( $items ) {
					$items.click( this._events.toggleSelected.bind( this ) );
					$items
						.on( 'change', '.usof-checkbox.content', this._events.groupCheckboxes.bind( this ) )
						.on( 'change', '.usof-checkbox.child', this._events.groupCheckboxControl.bind( this ) );
				},
				/**
				 * Event handlers
				 */
				_events: {
					/**
					 * Choosing a demo for import.
					 *
					 * @param {Event} e
					 */
					toggleSelected: function( e ) {
						var $this = $( e.currentTarget );
						if ( $this.hasClass( 'selected' ) ) {
							return;
						}
						$this.siblings().removeClass( 'selected' );
						$this.addClass( 'selected' );
					},
					/**
					 * @param {Event} e
					 */
					groupCheckboxes: function( e ) {
						var $target = $( e.currentTarget ),
							$options = $target.closest( '.w-importer-item-options-h' );
						$options
							.find( '.child_checkbox:not([disabled])' )
							.prop( 'checked', $( 'input[type="checkbox"]', $target ).is( ':checked' ) );
					},
					/**
					 * @param {Event} e
					 */
					groupCheckboxControl: function( e ) {
						var $target = $( e.currentTarget ),
							$options = $target.closest( '.w-importer-item-options-h' ),
							checked = true;
						$options
							.find( '.usof-checkbox.child input:not([disabled])' )
							.each( function( _, item ) {
								checked = checked && $( item ).is( ':checked' );
							} );
						$options
							.find( '.usof-checkbox.content input' )
							.prop( 'checked', checked );
					}
				},
				/**
				 * Demo import.
				 *
				 * @param {Event}
				 */
				importDemo: function( e ) {
					e.preventDefault();
					if ( this.importRunning ) {
						return;
					}
					var $target = $( e.target ),
						$item = $target.find( '.w-importer-item.selected' ),
						$checkboxes = $item.find( 'input[type="checkbox"]' ),
						data = $item.data() || {},
						importQueue = [];

					// Import Queue Processing
					var processQueue = function() {
						if ( importQueue.length ) {
							var importAction = importQueue.shift();
							$.post( '<?php echo admin_url( 'admin-ajax.php' ); ?>', {
								action: importAction,
								demo: data.demoId,
								security: '<?php echo wp_create_nonce( 'us-demo-import-actions' ); ?>'
							}, function( res ) {
								if ( res.success ) {
									processQueue.call( this );
								} else {
									this.$form.addClass( 'error' );
									$( '.w-importer-message.done h2', this.$form ).html(res.error_title);
									$( '.w-importer-message.done p', this.$form ).html(res.error_description);
								}
							}.bind( this ), 'json' );
						} else {
							// Import is completed
							this.$form.addClass('success');
							this.importRunning = true;
						}
					};

					if ( $checkboxes.filter( '[name="content_all"]' ).is( ':checked' ) ) {
						importQueue.push('us_demo_import_content_all');
					} else {
						$checkboxes
							.filter( '.child_checkbox' )
							.each( function( _, item ) {
								var $item = $( item );
								if ( $item.is( ':checked' ) ) {
									importQueue.push( 'us_demo_import_' + $item.attr( 'name' ) );
								}
							});
					}
					if ( $checkboxes.filter('input[name=theme_options]').is( ':checked' ) ) {
						importQueue.unshift('us_demo_import_options');
					}
					if ( $checkboxes.filter('input[name=content_woocommerce]').is( ':checked' ) ) {
						importQueue.push('us_demo_import_woocommerce');
					}
					if ( $checkboxes.filter('input[name=content_acf_fields]').is( ':checked' ) ) {
						importQueue.push('us_demo_import_acf_fields');
					}
					if ( ! importQueue.length ) {
						return;
					}
					if( data.withForceOptions ) {
						importQueue.unshift( 'us_demo_import_force_theme_option' );
					}

					this.$form.addClass( 'importing' );
					this.importRunning = true;
					processQueue.call( this );
				}
			};
			// Init importer
			new Importer( '.w-importer' );
		} )( jQuery );
	</script>
	<?php
}

/* Helpers
------------------------------------------------------------------------------------*/

if ( ! function_exists( 'us_render_for_importer' ) ) {
	/**
	 * Render demos to display on the page
	 *
	 * @param array $demos The demos list
	 * @return string
	 */
	function us_render_for_importer( $demos ) {
		global $help_portal_preview_url;
		ob_start();
		foreach ( $demos as $name => $import ) { ?>
			<div class="w-importer-item" data-demo-id="<?php echo $name; ?>" data-with-force-options="<?php echo (int) isset( $import[ 'force_theme_options' ] ); ?>">
				<input class="w-importer-item-radio" id="demo_<?php echo $name; ?>" type="radio" value="<?php echo $name; ?>" name="demo">
				<label class="w-importer-item-preview" for="demo_<?php echo $name; ?>" title="<?php _e( 'Click to choose', 'us' ) ?>">
					<h2 class="w-importer-item-title"><?php echo $import['title']; ?>
						<a class="btn" href="<?php echo $import['preview_url']; ?>" target="_blank" rel="noopener" title="<?php _e( 'View this demo in a new tab', 'us' ) ?>"><?php echo us_translate( 'Preview' ) ?></a>
					</h2>
					<img src="<?php echo $help_portal_preview_url . $name . '/preview.jpg' ?>" width="1070" height="730" loading="lazy" alt="">
				</label>
				<div class="w-importer-item-options">
					<div class="w-importer-item-options-h">
						<label class="usof-checkbox content">
							<input type="checkbox" value="ON" name="content_all" checked="checked" class="parent_checkbox">
							<span class="usof-checkbox-text"><?php echo us_translate( 'All content' ) ?></span>
						</label>
						<?php if ( in_array( 'pages', $import['content'] ) ) { ?>
							<label class="usof-checkbox child">
								<input type="checkbox" value="ON" name="content_pages" checked class="child_checkbox">
								<span class="usof-checkbox-text"><?php echo us_translate( 'Pages' ) ?></span>
							</label>
						<?php } ?>
						<?php if ( in_array( 'posts', $import['content'] ) ) { ?>
							<label class="usof-checkbox child">
								<input type="checkbox" value="ON" name="content_posts" checked class="child_checkbox">
								<span class="usof-checkbox-text"><?php echo us_translate( 'Posts' ) ?></span>
							</label>
						<?php } ?>
						<?php if ( in_array( 'portfolio_items', $import['content'] ) ) { ?>
							<label class="usof-checkbox child">
								<input type="checkbox" value="ON" name="content_portfolio" <?php if ( us_get_option( 'enable_portfolio', 1 ) == 0 ) {
									echo ' disabled="disabled"';
								} else {
									echo 'checked="checked"';
								} ?> class="child_checkbox">
								<span class="usof-checkbox-text"><?php echo _e( 'Portfolio', 'us' ) ?></span>
								<?php if ( us_get_option( 'enable_portfolio', 1 ) == 0 ) { ?>
									<span class="usof-checkbox-note"> &mdash;
										<a href="<?php echo admin_url( 'admin.php?page=us-theme-options#advanced' ) ?>"><?php echo sprintf( __( 'Enable %s module', 'us' ), __( 'Portfolio', 'us' ) ) ?></a>
									</span>
								<?php } ?>
							</label>
						<?php } ?>
						<?php if ( in_array( 'testimonials', $import['content'] ) ) { ?>
							<label class="usof-checkbox child">
								<input type="checkbox" value="ON" name="content_testimonials" <?php if ( us_get_option( 'enable_testimonials', 1 ) == 0 ) {
									echo ' disabled="disabled"';
								} else {
									echo 'checked="checked"';
								} ?> class="child_checkbox">
								<span class="usof-checkbox-text"><?php _e( 'Testimonials', 'us' ) ?></span>
								<?php if ( us_get_option( 'enable_testimonials', 1 ) == 0 ) { ?>
									<span class="usof-checkbox-note"> &mdash;
										<a href="<?php echo admin_url( 'admin.php?page=us-theme-options#advanced' ) ?>"><?php echo sprintf( __( 'Enable %s module', 'us' ), __( 'Testimonials', 'us' ) ) ?></a>
									</span>
								<?php } ?>
							</label>
						<?php } ?>
						<?php if ( in_array( 'headers', $import['content'] ) ) { ?>
							<label class="usof-checkbox child">
								<input type="checkbox" value="ON" name="content_headers" checked class="child_checkbox">
								<span class="usof-checkbox-text"><?php echo _x( 'Headers', 'site top area', 'us' ) ?></span>
							</label>
						<?php } ?>
						<?php if ( in_array( 'grid_layouts', $import['content'] ) ) { ?>
							<label class="usof-checkbox child">
								<input type="checkbox" value="ON" name="content_grid_layouts" checked class="child_checkbox">
								<span class="usof-checkbox-text"><?php echo __( 'Grid Layouts', 'us' ) ?></span>
							</label>
						<?php } ?>
						<?php if ( in_array( 'content_templates', $import['content'] ) ) { ?>
							<label class="usof-checkbox child">
								<input type="checkbox" value="ON" name="content_content_templates" checked class="child_checkbox">
								<span class="usof-checkbox-text"><?php echo __( 'Content templates', 'us' ) ?></span>
							</label>
						<?php } ?>
						<?php if ( in_array( 'page_blocks', $import['content'] ) ) { ?>
							<label class="usof-checkbox child">
								<input type="checkbox" value="ON" name="content_page_blocks" checked class="child_checkbox">
								<span class="usof-checkbox-text"><?php echo __( 'Page Blocks', 'us' ) ?></span>
							</label>
						<?php } ?>
						<label class="usof-checkbox theme-options">
							<input type="checkbox" value="ON" name="theme_options" checked>
							<span class="usof-checkbox-text"><?php _e( 'Theme Options', 'us' ) ?></span>
						</label>

						<?php if ( in_array( 'products', $import['content'] ) ) { ?>
							<label class="usof-checkbox woocommerce">
								<input type="checkbox" value="ON" name="content_woocommerce"<?php if ( ! class_exists( 'woocommerce' ) ) {
									echo ' disabled="disabled"';
								} ?>>
								<span class="usof-checkbox-text"><?php _e( 'Shop Products', 'us' ) ?></span>
								<?php if ( ! class_exists( 'woocommerce' ) ) { ?>
									<span class="usof-checkbox-note"> &mdash;
										<a href="<?php echo admin_url( 'admin.php?page=us-addons' ) ?>"><?php echo sprintf( us_translate( 'Install %s' ), 'WooCommerce' ) ?></a>
										</span>
								<?php } ?>
							</label>
						<?php } ?>

						<?php if ( in_array( 'acf_fields', $import['content'] ) ) { ?>
							<label class="usof-checkbox acf_fields">
								<input type="checkbox" value="ON" name="content_acf_fields"<?php if ( ! function_exists( 'get_field_object' ) ) {
									echo ' disabled="disabled"';
								} ?>>
								<span class="usof-checkbox-text"><?php echo us_translate( 'Custom Fields' ) ?></span>
								<?php if ( ! function_exists( 'get_field_object' ) ) { ?>
									<span class="usof-checkbox-note"> &mdash;
										<a href="<?php echo admin_url( 'admin.php?page=us-addons' ) ?>"><?php echo sprintf( us_translate( 'Install %s' ), 'Advanced Custom Fields' ) ?></a>
										</span>
								<?php } ?>
							</label>
						<?php } ?>

					</div>
					<input type="hidden" name="action" value="perform_import">
					<input class="usof-button import_demo_data" type="submit" value="<?php echo us_translate( 'Import' ) ?>">
				</div>
				<div class="w-importer-message progress">
					<div class="g-preloader type_1"></div>
					<h2><?php _e( 'Importing Demo Content...', 'us' ) ?></h2>
					<p><?php _e( 'Don\'t close or refresh this page to not interrupt the import.', 'us' ) ?></p>
				</div>
				<div class="w-importer-message done">
					<h2><?php _e( 'Import completed', 'us' ) ?></h2>
					<p><?php echo sprintf( __( 'Just check the result on %syour site%s or start customize via %sTheme Options%s.', 'us' ), '<a href="' . site_url() . '" target="_blank" rel="noopener">', '</a>', '<a href="' . admin_url( 'admin.php?page=us-theme-options' ) . '">', '</a>' ) ?></p>
				</div>
			</div>
		<?php }
		return ob_get_clean();
	}
}

if ( ! function_exists( 'us_get_demo_version' ) ) {
	/**
	 * Select which files to import
	 *
	 * @return string
	 */
	function us_get_demo_version() {
		$config = us_get_demo_import_config();
		$aviable_demos = array_keys( $config );
		$demo_version = $aviable_demos[0];
		if ( in_array( $_POST['demo'], $aviable_demos ) ) {
			$demo_version = $_POST['demo'];
		}
		return $demo_version;
	}
}

if ( ! function_exists( 'us_upload_demo_import_file' ) ) {
	/**
	 * Upload demo data to server
	 *
	 * @param string $filename
	 * @param string $extension
	 * @return mixed
	 */
	function us_upload_demo_import_file( $filename = '', $extension = 'xml' ) {
		global $help_portal_api_url;
		$file_copied = FALSE;

		$query = array(
			'demo' => us_get_demo_version(),
			'file' => urlencode( $filename ),
		);

		$upload_dir = wp_upload_dir();
		$file_url = sprintf( '%s?%s', $help_portal_api_url, http_build_query( $query ) );
		$file_path = $upload_dir['basedir'] . '/' . $filename . '.' . $extension;

		// Fetching file with cURL
		if ( function_exists( 'curl_init' ) ) {
			$curl = curl_init();
			curl_setopt_array( $curl, array(
				CURLOPT_URL => $file_url,
				CURLOPT_HEADER => 0,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_SSL_VERIFYHOST => FALSE,
				CURLOPT_SSL_VERIFYPEER => FALSE,
			) );
			$contents = curl_exec( $curl );
			curl_close( $curl );

			if ( strlen( $contents ) > 50 ) {
				$fp = fopen( $file_path, 'w' );
				fwrite( $fp, $contents );
				$file_copied = TRUE;
			}
		}

		// If something is wrong with cURL, trying to use copy function
		if ( ! $file_copied ) {
			if ( copy( $file_url, $file_path ) AND filesize( $file_path ) > 50 ) {
				$file_copied = TRUE;
			}
		}

		return $file_copied
			? $file_path
			: FALSE;
	}
}

if ( ! function_exists( 'us_demo_import_content' ) ) {
	/**
	 * @param string $file_path The file path
	 */
	function us_demo_import_content( $file_path ) {
		global $wp_import;

		us_set_time_limit();

		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
			define( 'WP_LOAD_IMPORTERS', TRUE );
		}

		if ( ! class_exists( 'WP_Import' ) ) {
			require_once( US_CORE_DIR . 'vendor/wordpress-importer/wordpress-importer.php' );
		}

		$wp_import = new WP_Import();
		$wp_import->fetch_attachments = TRUE;

		ob_start();
		$wp_import->import( $file_path );
		ob_end_clean();

		// Replace images in _wpb_shortcodes_custom_css meta with placeholder
		global $wpdb;
		$placeholder = $wpdb->get_col( "SELECT guid FROM $wpdb->posts WHERE guid like '%us-placeholder-landscape%';" );
		if ( is_array( $placeholder ) AND isset( $placeholder[0] ) ) {
			$placeholder = $placeholder[0];
		}

		if ( ! empty( $placeholder ) ) {
			$wpdb_results = $wpdb->get_results( "SELECT p.ID, pm.meta_value, pm.meta_key FROM {$wpdb->postmeta} pm LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE (pm.meta_key = '_wpb_shortcodes_custom_css' OR pm.meta_key = 'us_og_image') AND p.post_status = 'publish'" );
			foreach ( $wpdb_results as $meta_result ) {
				if ( $meta_result->ID ) {
					if ( $meta_result->meta_key == 'us_og_image' ) {
						$new_meta_value = preg_replace( '/(https?:\/\/[^ ,;]+\.(?:png|jpg))/i', $placeholder, $meta_result->meta_value );
						if ( $new_meta_value !== $meta_result->meta_value ) {
							update_post_meta( $meta_result->ID, 'us_og_image', $new_meta_value );
						}
					} else {
						$new_meta_value = preg_replace( '/(https?:\/\/[^ ,;]+\.(?:png|jpg))/i', $placeholder, $meta_result->meta_value );
						if ( $new_meta_value !== $meta_result->meta_value ) {
							update_post_meta( $meta_result->ID, '_wpb_shortcodes_custom_css', $new_meta_value );
						}
					}

				}
			}
		}

		// Remove meta dublicates for _wpb_shortcodes_custom_css
		$delete_meta_dublicates_sql = "DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ('_wpb_shortcodes_custom_css', 'us_og_image', 'us_grid_layout_ids') AND meta_id NOT IN (
					SELECT *
					FROM (
						SELECT MAX(meta_id)
						FROM {$wpdb->postmeta}
						WHERE meta_key IN ('_wpb_shortcodes_custom_css', 'us_og_image', 'us_grid_layout_ids')
						GROUP BY post_id, meta_key
					) AS x
				)";

		$wpdb->query( $delete_meta_dublicates_sql );

		unlink( $file_path );
	}
}

if ( ! function_exists( 'us_action_for_demo_import' ) ) {
	/**
	 * Global action for import demo
	 * @param string $filename
	 * @param string $err_title
	 * @return string
	 */
	function us_action_for_demo_import( $filename, $err_title_filename, $callback = NULL ) {
		if ( ! check_ajax_referer( 'us-demo-import-actions', 'security', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
		}

		if ( $file_path = us_upload_demo_import_file( $filename ) ) {
			us_demo_import_content( $file_path );
			if ( is_callable( $callback ) ) {
				call_user_func( $callback );
			}
			wp_send_json_success();
		} else {
			wp_send_json(
				array(
					'success' => FALSE,
					'error_title' => sprintf( __( 'Failed to import %s', 'us' ), $err_title_filename ),
					'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
				)
			);
		}
	}
}

/* Actions for load demo files
------------------------------------------------------------------------------------*/

// Import All Content
add_action( 'wp_ajax_us_demo_import_content_all', 'us_demo_import_content_all' );
function us_demo_import_content_all() {
	if ( ! check_ajax_referer( 'us-demo-import-actions', 'security', FALSE ) ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	$config = us_get_demo_import_config();
	$demo_version = us_get_demo_version();

	if ( $file_path = us_upload_demo_import_file( 'all_content' ) ) {
		// Mega menu import filters and actions - START
		add_filter( 'wp_import_post_data_raw', 'us_demo_import_all_wp_import_post_data_raw' );
		function us_demo_import_all_wp_import_post_data_raw( $post ) {
			global $us_demo_import_mega_menu_data;
			if ( $post['post_type'] != 'nav_menu_item' ) {
				return $post;
			}

			if ( isset( $post['postmeta'] ) AND is_array( $post['postmeta'] ) ) {
				foreach ( $post['postmeta'] as $postmeta ) {
					if ( is_array( $postmeta ) AND isset( $postmeta['key'] ) AND $postmeta['key'] == 'us_mega_menu_settings' AND ! empty( $postmeta['value'] ) ) {
						if ( ! isset( $us_demo_import_mega_menu_data ) OR ! is_array( $us_demo_import_mega_menu_data ) ) {
							$us_demo_import_mega_menu_data = array();
						}

						$us_demo_import_mega_menu_data[ (int) $post['post_id'] ] = $postmeta['value'];
					}
				}

			}

			return $post;
		}

		add_action( 'import_end', 'us_demo_import_all_import_end' );
		function us_demo_import_all_import_end() {
			global $wp_import, $us_demo_import_mega_menu_data;

			if ( is_array( $us_demo_import_mega_menu_data ) ) {
				foreach ( $us_demo_import_mega_menu_data as $menu_import_id => $mega_menu_data ) {
					if ( ! empty( $wp_import->processed_menu_items[ $menu_import_id ] ) ) {
						update_post_meta( (int) $wp_import->processed_menu_items[ $menu_import_id ], 'us_mega_menu_settings', maybe_unserialize( $mega_menu_data ) );
					}
				}
			}
		}

		// Mega menu import filters and actions - END

		us_demo_import_content( $file_path );

		// Set menu
		if ( isset( $config[ $demo_version ]['nav_menu_locations'] ) ) {
			$locations = get_theme_mod( 'nav_menu_locations' );
			$menus = array();
			foreach ( wp_get_nav_menus() as $menu ) {
				if ( is_object( $menu ) ) {
					$menus[ $menu->name ] = $menu->term_id;
				}
			}
			foreach ( $config[ $demo_version ]['nav_menu_locations'] as $nav_location_key => $menu_name ) {
				if ( isset( $menus[ $menu_name ] ) ) {
					$locations[ $nav_location_key ] = $menus[ $menu_name ];
				}
			}

			set_theme_mod( 'nav_menu_locations', $locations );
		}

		// Set Front Page
		if ( isset( $config[ $demo_version ]['front_page'] ) ) {
			$front_page = get_posts(
				array(
					'name' => $config[ $demo_version ]['front_page'],
					'post_type' => 'page',
					'post_status' => 'publish',
					'posts_per_page' => 1,
				)
			);

			if ( $front_page ) {
				$front_page = $front_page[0];
			}
			if ( isset( $front_page->ID ) ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $front_page->ID );
			}
		}
		// Trashing Hello World Post
		wp_trash_post( 1 );

		if ( function_exists( 'wc_delete_product_transients' ) ) {
			wc_delete_product_transients();
		}

		// Setting permalink structure
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		update_option( 'us_flush_rules', TRUE, /* autoload */ 'yes' );
		wp_send_json_success();

	} else {
		wp_send_json(
			array(
				'success' => FALSE,
				'error_title' => sprintf( __( 'Failed to import %s', 'us' ), us_translate( 'All content' ) ),
				'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
			)
		);
	}
}

// Pages
add_action( 'wp_ajax_us_demo_import_content_pages', 'us_demo_import_content_pages' );
function us_demo_import_content_pages() {
	us_action_for_demo_import( 'pages', us_translate( 'Pages' ) );
}

// Posts
add_action( 'wp_ajax_us_demo_import_content_posts', 'us_demo_import_content_posts' );
function us_demo_import_content_posts() {
	us_action_for_demo_import( 'posts', us_translate( 'Posts' ), function() {
		// Trashing Hello World Post
		wp_trash_post( 1 );
	} );
}

// Portfolio
add_action( 'wp_ajax_us_demo_import_content_portfolio', 'us_demo_import_content_portfolio' );
function us_demo_import_content_portfolio() {
	us_action_for_demo_import( 'portfolio_items', __( 'Portfolio', 'us' ) );
}

// Testimonials
add_action( 'wp_ajax_us_demo_import_content_testimonials', 'us_demo_import_content_testimonials' );
function us_demo_import_content_testimonials() {
	us_action_for_demo_import( 'testimonials', __( 'Testimonials', 'us' ) );
}

// Headers
add_action( 'wp_ajax_us_demo_import_content_headers', 'us_demo_import_content_headers' );
function us_demo_import_content_headers() {
	us_action_for_demo_import( 'headers', _x( 'Headers', 'site top area', 'us' ) );
}

// Grid Layouts
add_action( 'wp_ajax_us_demo_import_content_grid_layouts', 'us_demo_import_content_grid_layouts' );
function us_demo_import_content_grid_layouts() {
	us_action_for_demo_import( 'grid_layouts', __( 'Grid Layouts', 'us' ) );
}

// Page Blocks
add_action( 'wp_ajax_us_demo_import_content_page_blocks', 'us_demo_import_content_page_blocks' );
function us_demo_import_content_page_blocks() {
	us_action_for_demo_import( 'page_blocks', __( 'Page Blocks', 'us' ) );
}

// Content templates
add_action( 'wp_ajax_us_demo_import_content_content_templates', 'us_demo_import_content_content_templates' );
function us_demo_import_content_content_templates() {
	us_action_for_demo_import( 'content_templates', __( 'Content templates', 'us' ) );
}

// ACF fields
add_action( 'wp_ajax_us_demo_import_acf_fields', 'us_demo_import_acf_fields' );
function us_demo_import_acf_fields() {
	us_action_for_demo_import( 'acf_fields', us_translate( 'Custom Fields' ) );
}

// WooCommerce Import
add_action( 'wp_ajax_us_demo_import_woocommerce', 'us_demo_import_woocommerce' );
function us_demo_import_woocommerce() {
	if ( ! check_ajax_referer( 'us-demo-import-actions', 'security', FALSE ) ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	us_set_time_limit();

	if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
		define( 'WP_LOAD_IMPORTERS', TRUE );
	}

	$demo_version = us_get_demo_version();

	if ( $file_path = us_upload_demo_import_file( 'products' ) ) {
		if ( ! class_exists( 'WP_Import' ) ) {
			require_once( US_CORE_DIR . 'vendor/wordpress-importer/wordpress-importer.php' );
		}

		$wp_import = new WP_Import();
		$wp_import->fetch_attachments = TRUE;

		// Creating attributes taxonomies
		global $wpdb;
		$parser = new WXR_Parser();
		$import_data = $parser->parse( $file_path );

		if ( isset( $import_data['posts'] ) ) {

			$posts = $import_data['posts'];

			if ( $posts AND sizeof( $posts ) > 0 ) {
				foreach ( $posts as $post ) {
					if ( 'product' === $post['post_type'] ) {
						if ( ! empty( $post['terms'] ) ) {
							foreach ( $post['terms'] as $term ) {
								if ( strstr( $term['domain'], 'pa_' ) ) {
									if ( ! taxonomy_exists( $term['domain'] ) ) {
										$attribute_name = wc_sanitize_taxonomy_name( str_replace( 'pa_', '', $term['domain'] ) );

										// Create the taxonomy
										if ( ! in_array( $attribute_name, wc_get_attribute_taxonomies() ) ) {
											$attribute = array(
												'attribute_label' => $attribute_name,
												'attribute_name' => $attribute_name,
												'attribute_type' => 'select',
												'attribute_orderby' => 'menu_order',
												'attribute_public' => 1,
											);
											$wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );
											delete_transient( 'wc_attribute_taxonomies' );
										}

										// Register the taxonomy now so that the import works!
										register_taxonomy(
											$term['domain'], apply_filters( 'woocommerce_taxonomy_objects_' . $term['domain'], array( 'product' ) ), apply_filters(
												'woocommerce_taxonomy_args_' . $term['domain'], array(
													'hierarchical' => TRUE,
													'show_ui' => FALSE,
													'query_var' => TRUE,
													'rewrite' => FALSE,
												)
											)
										);
									}
								}
							}
						}
					}
				}
			}
		}

		ob_start();
		$wp_import->import( $file_path );
		ob_end_clean();

		// Set WooCommerce Pages
		$shop_page = get_page_by_title( 'Shop' );
		if ( isset( $shop_page->ID ) ) {
			update_option( 'woocommerce_shop_page_id', $shop_page->ID );
		}
		$cart_page = get_page_by_title( 'Cart' );
		if ( isset( $cart_page->ID ) ) {
			update_option( 'woocommerce_cart_page_id', $cart_page->ID );
		}
		$checkout_page = get_page_by_title( 'Checkout' );
		if ( isset( $checkout_page->ID ) ) {
			update_option( 'woocommerce_checkout_page_id', $checkout_page->ID );
		}
		$my_account_page = get_page_by_title( 'My Account' );
		if ( isset( $my_account_page->ID ) ) {
			update_option( 'woocommerce_myaccount_page_id', $my_account_page->ID );
		}

		unlink( $file_path );

		if ( function_exists( 'wc_delete_product_transients' ) ) {
			wc_delete_product_transients();
		}
		wp_send_json_success();
	} else {
		wp_send_json(
			array(
				'success' => FALSE,
				'error_title' => sprintf( __( 'Failed to import %s', 'us' ), __( 'Shop Products', 'us' ) ),
				'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
			)
		);
	}
}

// WooCommerce sales support, as the main import does not support this, we will check on the filter.
if ( ! function_exists( 'us_wp_import_post_meta' ) ) {
	add_filter( 'wp_import_post_meta', 'us_wp_import_post_meta', 10, 3 );
	function us_wp_import_post_meta( $postmeta, $post_id, $post ) {
		if ( empty( $postmeta ) OR empty( $post ) OR ! is_array( $postmeta ) OR ! is_array( $post ) ) {
			return;
		}
		global $wpdb;
		if ( in_array( $post['post_type'], array( 'product', 'product_variation' ) ) ) {
			$postmeta_keys = array_flip( wp_list_pluck( $postmeta, 'key' ) );
			$postmeta_value = ( ! empty( $postmeta_keys['_sale_price'] ) ) ? us_arr_path( $postmeta, $postmeta_keys['_sale_price'] . '.value' ) : NULL;
			if ( ! empty( $postmeta_value ) ) {
				$sql = "
					SELECT
						`onsale`
					FROM {$wpdb->wc_product_meta_lookup}
					WHERE
						`product_id` = %s
						AND `onsale` = 1
					LIMIT 1;
				";
				if ( $wpdb->get_row( $wpdb->prepare( $sql, $post_id ) ) === NULL ) {
					$wpdb->insert( $wpdb->wc_product_meta_lookup, array(
						'product_id' => $post_id,
						'onsale' => 1,
						'max_price' => sprintf('%0.2f', $postmeta_value ),
						'min_price' => sprintf('%0.2f', $postmeta_value ),
						'stock_status' => 'instock',
					) );
				}
			}
		}
		return $postmeta;
	}
}

// Force set some Theme Options before main import
add_action( 'wp_ajax_us_demo_import_force_theme_option', 'us_demo_import_force_theme_option' );
function us_demo_import_force_theme_option() {
	if ( ! check_ajax_referer( 'us-demo-import-actions', 'security', FALSE ) ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	$config = us_get_demo_import_config();

	//select which files to import
	$aviable_demos = array_keys( $config );
	// Demo version should be set precisely, so if it is not set in POST params, abort execution.
	if ( in_array( $_POST['demo'], $aviable_demos ) ) {
		$demo_version = $_POST['demo'];
	} else {
		// Using same error as for All Conent, because we are doing this for the content import.
		wp_send_json(
			array(
				'success' => FALSE,
				'error_title' => sprintf( __( 'Failed to import %s', 'us' ), us_translate( 'All content' ) ),
				'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
			)
		);
	}

	// Check if we have options to force set in config, otherwise abort execution.
	$options_to_set = $config[ $demo_version ][ 'force_theme_options' ];
	if ( ! is_array( $options_to_set ) OR count( $options_to_set ) == 0 ) {
		// Using same error as for All Conent, because we are doing this for the content import.
		wp_send_json(
			array(
				'success' => FALSE,
				'error_title' => sprintf( __( 'Failed to import %s', 'us' ), us_translate( 'All content' ) ),
				'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
			)
		);
	}

	global $usof_options;
	usof_load_options_once();

	foreach ( $options_to_set as $option => $value ) {
		$usof_options[ $option ] = $value;
	}

	update_option( 'usof_options_' . US_THEMENAME, $usof_options, TRUE );

	wp_send_json_success();
}

// Import Theme Options
add_action( 'wp_ajax_us_demo_import_options', 'us_demo_import_options' );
function us_demo_import_options() {
	if ( ! check_ajax_referer( 'us-demo-import-actions', 'security', FALSE ) ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	if ( empty( get_option( 'usof_backup_' . US_THEMENAME, NULL )['usof_options'] ) ) {
		usof_backup();
	}

	if ( $file_path = us_upload_demo_import_file( 'theme_options', 'json' ) ) {
		$updated_options = json_decode( file_get_contents( $file_path ), TRUE );

		if ( ! is_array( $updated_options ) ) {
			// Wrong file configuration
			wp_send_json(
				array(
					'success' => FALSE,
					'error_title' => sprintf( __( 'Failed to import %s', 'us' ), __( 'Theme Options', 'us' ) ),
					'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
				)
			);
		}

		// Save custom settings
		foreach ( [ 'custom_css', 'custom_html', 'custom_html_head' ] as $custom_field ) {
			if ( isset( $updated_options[ $custom_field ] ) ) {
				$updated_options[ $custom_field ] = usof_get_option( $custom_field );
			}
		}

		usof_save_options( $updated_options );
		unlink( $file_path );
		wp_send_json_success();
	} else {
		wp_send_json(
			array(
				'success' => FALSE,
				'error_title' => sprintf( __( 'Failed to import %s', 'us' ), __( 'Theme Options', 'us' ) ),
				'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
			)
		);
	}
}
