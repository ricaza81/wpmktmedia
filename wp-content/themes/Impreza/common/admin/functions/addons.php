<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Addons admin page
 */

if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! current_user_can( 'install_plugins' ) ) {
	return;
}

// Display admin notice when UpSolution Core is not active OR its version is less then theme version
if ( ! defined( 'US_CORE_VERSION' ) OR version_compare( US_CORE_VERSION, US_THEMEVERSION, '<' ) ) {
	add_action( 'admin_notices', 'us_core_admin_notice', 1 );
}
if ( ! function_exists( 'us_core_admin_notice' ) ) {
	function us_core_admin_notice() {
		global $help_portal_url;
		?>
		<div class="us-core-notice" style="padding: 20px; margin-left: -20px; background-color: #ffe14c;">
			<h2 style="margin: 0; color: #000;">
			<?php
			// Update notice
			if ( defined( 'US_CORE_VERSION' ) ) {
				echo sprintf(
					__( 'To enable all features used by %s theme, update %s plugin.', 'us' ),
					US_THEMENAME,
					'<a href="' . admin_url( 'plugins.php#us-core-update' ) . '">UpSolution Core</a>'
				);

				// Install and activate notice
			} else {
				echo sprintf(
					__( 'To enable all features used by %s theme, install and activate %s plugin.', 'us' ),
					US_THEMENAME,
					'<a href="' . admin_url( 'admin.php?page=us-addons' ) . '">UpSolution Core</a>'
				);
				echo ' <a href="' . esc_url( $help_portal_url ) . '/' . strtolower( US_THEMENAME ) . '/us-core/" target="_blank" rel="noopener">';
				echo __( 'Learn more', 'us' );
				echo '</a>';
			}
			?>
			</h2>
		</div>
		<?php
	}
}

// Add "Addons" in admin menu
add_action( 'admin_menu', 'us_add_addons_page', 20 );
function us_add_addons_page() {
	if ( ! defined( 'US_CORE_VERSION') ) {
		$parent_slug = 'us-home';
	} else {
		$parent_slug = 'us-theme-options';
	}
	add_submenu_page( $parent_slug, __( 'Addons', 'us' ), __( 'Addons', 'us' ), 'manage_options', 'us-addons', 'us_addons_page' );
}
function us_addons_page() {

	if ( ! empty( $_POST['action'] ) AND $_POST['action'] == 'activate' ) {
		us_activate_plugin();
		?>
		<script type='text/javascript'>
			window.location=document.location.href;
		</script>
		<?php

		return;
	}

	$plugins = us_config( 'addons', array() );

	foreach ( $plugins as $i => $plugin ) {
		if ( isset( $plugins[ $i ]['premium'] ) AND ! isset( $plugins[ $i ]['source'] ) ) {
			$plugins = us_api_addons( $plugins, TRUE );
			break;
		}
	}

	$installed_plugins = get_plugins();
	$activated_plugins_option = get_option( 'active_plugins' );
	$activated_plugins = array();
	foreach ( $activated_plugins_option as $plugin ) {
		if ( isset( $installed_plugins[ $plugin ] ) ) {
			$activated_plugins[ $plugin ] = $installed_plugins[ $plugin ];
		}
	}

	global $us_template_directory_uri;

	?>
	<div class="us-addons">
		<h1 class="us-admin-title"><?php echo __( 'Recommended Plugins', 'us' ); ?></h1>
		<p class="us-admin-subtitle"><?php echo sprintf( __( 'Premium and free plugins, compatible with %s', 'us' ), US_THEMENAME ) ?></p>
		<div class="us-addons-list"><?php
			foreach ( $plugins as $plugin ) {

				$keys = array_keys( $installed_plugins );

				$plugin['file_path'] = ( ! empty( $plugin['folder'] ) ) ? $plugin['folder'] : $plugin['slug'];
				foreach ( $keys as $key ) {
					if ( preg_match( '|^' . $plugin['file_path'] . '/|', $key ) ) {
						$plugin['file_path'] = $key;
						break;
					}
				}

				$classes = ' ' . $plugin['slug'];
				if ( ! isset( $plugin['premium'] ) ) {
					$classes .= ' is_free';
				} else {
					$classes .= ' is_premium';
				}

				// Separate params for FREE and PREMIUM plugins
				if ( ! isset( $plugin['premium'] ) ) {
					$icon_ext = isset( $plugin['icon_ext'] ) ? $plugin['icon_ext'] : 'png';
					$icon_url = 'https://ps.w.org/' . $plugin['slug'] . '/assets/icon-256x256.' . $icon_ext;
					$plugin['url'] = 'https://wordpress.org/plugins/' . $plugin['slug'] . '/';
				} else {
					$icon_url = $us_template_directory_uri . '/img/' . $plugin['slug'] . '.png';
				}

				// Use icon URL, if it's set
				if ( isset( $plugin['icon_url'] ) ) {
					$icon_url = $plugin['icon_url'];
				}

				// Generate status attributes
				$btn_text = '';
				$btn_class = 'button action-button';
				if ( isset( $activated_plugins[ $plugin['file_path'] ] ) ) {
					$classes .= ' status_active';

				} elseif ( ! isset( $installed_plugins[ $plugin['file_path'] ] ) ) {

					if (
						isset( $plugin['premium'] )
						AND empty( $plugin['source'] )
						AND ! (
							get_option( 'us_license_activated', 0 )
							OR get_option( 'us_license_dev_activated', 0 )
						)
					) {
						$classes .= ' status_locked';
					} else {
						$classes .= ' status_notinstalled';
						$btn_text = us_translate( 'Install Now' );
						$btn_action = 'install';
					}

				} elseif ( ! isset( $activated_plugins[ $plugin['file_path'] ] ) ) {
					$classes .= ' status_notactive';
					$btn_class .= ' button-primary';
					$btn_text = us_translate( 'Activate Plugin' );
					$btn_action = 'activate';
				}
				?>
				<div class="us-addon<?php echo esc_attr( $classes ); ?>">
					<div class="us-addon-content">
						<a href="<?php echo esc_url( $plugin['url'] ) ?>" target="_blank" rel="noopener">
							<img class="us-addon-icon" src="<?php echo esc_url( $icon_url ) ?>" loading="lazy" alt="icon">
							<h2 class="us-addon-title"><?php echo strip_tags( $plugin['name'] ) ?></h2>
						</a>
						<p class="us-addon-desc"><?php echo strip_tags( $plugin['description'] ) ?></p>
					</div>
					<div class="us-addon-control">
						<div class="us-addon-status"><?php echo us_translate_x( 'Active', 'plugin' ); ?></div>
						<?php
						// Screenlock for premium plugins
						if (
							isset( $plugin['premium'] )
							AND ! isset( $plugin['source'] )
							AND ! ( get_option( 'us_license_activated', 0 ) OR get_option( 'us_license_dev_activated', 0 ) )
						) {
							?>
							<div class="us-addon-lock">
								<div><?php echo sprintf( __( '<a href="%s">Activate the theme</a> to install premium addons', 'us' ), admin_url( 'admin.php?page=us-home#activation' ) ) ?></div>
							</div>
							<?php
						}
						if ( ! empty( $btn_text ) ) {
							?>
							<a class="<?php echo esc_attr( $btn_class ) ?>" href="javascript:void(0);" data-plugin="<?php echo esc_attr( $plugin['slug'] ) ?>" data-action="<?php echo esc_attr( $btn_action ) ?>">
								<span><?php echo strip_tags( $btn_text ) ?></span>
							</a>
							<?php
						}
						?>
					</div>
				</div>
			<?php
			}

		?></div>
		<form method="post" id="addons_form" style="display: none">
			<input type="hidden" name="action" value="activate">
			<input type="hidden" name="plugin">
		</form>
	</div>
	<script>
		jQuery(function($){
			var isRunning = false;
			$('.action-button').click(function(){
				if ( isRunning ) {
					return;
				}
				isRunning = true;
				$( '.us-addons-list' ).addClass( 'disable-buttons' );
				var plugin = $( this ).attr( 'data-plugin' ),
					action = $( this ).attr( 'data-action' ),
					$tile = $( this ).closest( '.us-addon' ),
					$status = $tile.find( '.us-addon-status' ),
					$button = $( this ),
					$form = $( '#addons_form' ),
					$plugin_field = $form.find( 'input[name=plugin]' );

				$tile.removeClass( function( index, css ) {
					return ( css.match( /(^|\s)status_\S+/g ) || [] ).join( ' ' );
				} );
				if ( action === 'install' ) {
					$tile.addClass( 'status_installing' );
					$button.html( '<i class="g-preloader type_1"></i><?php echo esc_js( us_translate( 'Installing...' ) ); ?>' );
				} else {
					$tile.addClass( 'status_activating' );
					$button.html( '</i><?php esc_js( _e( 'Activating...', 'us' ) ); ?>' );
				}
				if ( action === 'activate' ) {
					$plugin_field.val( plugin );
					$form.submit();
				} else if ( action === 'install' ) {
					$.ajax( {
						type: 'POST',
						url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
						data: {
							action: 'us_ajax_addons_install',
							plugin: plugin,
							security: '<?php echo wp_create_nonce( 'us-addons-actions' ); ?>'
						},
						success: function( data ) {
							isRunning = false;
							$( '.us-addons-list' ).removeClass( 'disable-buttons' );
							$button.hide();
							$tile.removeClass( function( index, css ) {
								return ( css.match( /(^|\s)status_\S+/g ) || [] ).join( ' ' );
							} );
							if ( data !== undefined && data.success ) {
								$tile.addClass( 'status_notactive' );
								$button.html( '<span><?php echo esc_js( us_translate( 'Activate Plugin' ) ); ?></span>' );
								$button.attr( 'data-action', 'activate' );
								$button.show();
							} else {
								$tile.addClass( 'status_error' );
								if ( data !== undefined && data.data !== undefined && data.data.message !== undefined ) {
									$status.html( data.data.message );
								} else {
									$status.html( '<?php echo esc_js( us_translate( 'An error has occurred. Please reload the page and try again.' ) ); ?>' );
								}
							}
						}
					} );
				}

				return false;
			});
		});
	</script>

	<?php
}



function filesystem_permission_check() {
	ob_start();
	$creds = request_filesystem_credentials( '', '', FALSE, FALSE, NULL );
	ob_get_clean();

	// Abort if permissions were not available.
	if ( ! WP_Filesystem( $creds ) ) {
		return FALSE;
	}

	return TRUE;
}

add_action( 'wp_ajax_us_ajax_addons_install', 'us_ajax_addons_install' );

function us_activate_plugin() {
	if ( empty( $_POST['plugin'] ) ) {
		return FALSE;
	}

	$plugins = us_config( 'addons', array() );

	if ( empty( $plugins ) ) {
		return FALSE;
	}

	$_plugins = array();
	foreach ( $plugins as $i => $plugin ) {
		$_plugins[ $plugin['slug'] ] = $plugin;
	}

	$plugins = $_plugins;

	$slug = urldecode( $_POST['plugin'] );

	if ( ! isset( $plugins[ $slug ] ) ) {
		return FALSE;
	}

	$plugin = $plugins[ $slug ];
	$plugin_folder = ( ! empty( $plugin['folder'] ) ) ? $plugin['folder'] : $plugin['slug'];

	$plugin_data = get_plugins( '/' . $plugin_folder ); // Retrieve all plugins.
	$plugin_file = array_keys( $plugin_data ); // Retrieve all plugin files from installed plugins.

	$plugin_to_activate = trailingslashit( $plugin_folder ) . $plugin_file[0]; // Match plugin slug with appropriate plugin file.
//	ob_start();
	$activate = activate_plugin( $plugin_to_activate ); // Activate the plugin.
//	ob_get_clean();

	if ( is_wp_error( $activate ) ) {
		return $activate;
	} else {
		return TRUE;
	}
}

function us_ajax_addons_install() {
	set_time_limit( 300 );

	if ( ! check_ajax_referer( 'us-addons-actions', 'security', FALSE ) ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	if ( ! isset( $_POST['plugin'] ) OR ! $_POST['plugin'] ) {
		wp_send_json_error( array( 'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ) ) );
	}

	$result = us_install_plugin();

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}

	wp_send_json_success( array( 'plugin' => $_POST['plugin'] ) );
}

function us_install_plugin() {
	if ( empty( $_POST['plugin'] ) ) {
		return FALSE;
	}

	$plugins = us_config( 'addons', array() );

	foreach ( $plugins as $i => $plugin ) {
		if ( ! isset( $plugins[ $i ]['premium'] ) ) {
			// Define WordPress repository plugin URL based on slug
			$plugins[ $i ]['source'] = 'https://downloads.wordpress.org/plugin/' . $plugin['slug'] . '.latest-stable.zip';
		} elseif ( empty( $plugins[ $i ]['source'] ) ) {
			unset( $plugins[ $i ] );
		}
	}

	if ( empty( $plugins ) ) {
		return FALSE;
	}

	$_plugins = array();
	foreach ( $plugins as $i => $plugin ) {
		$_plugins[ $plugin['slug'] ] = $plugin;
	}

	$plugins = $_plugins;

	$slug = urldecode( $_POST['plugin'] );

	if ( ! isset( $plugins[ $slug ] ) ) {
		return FALSE;
	}

	$plugin = $plugins[ $slug ];

	if ( file_exists( trailingslashit( WP_PLUGIN_DIR ) . $slug ) ) {
		return us_activate_plugin();
	}

	if ( ! filesystem_permission_check() ) {
		return new WP_Error( 'us-addons', __( 'Please adjust file permissions to allow plugins installation', 'us' ) );
	}

	$us_template_directory = get_template_directory();

	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	$skin = new WP_Ajax_Upgrader_Skin();
	$upgrader = new Plugin_Upgrader( $skin );

	$install_result = $upgrader->install( $plugin['source'] );

	if ( is_wp_error( $install_result ) ) {
		return $install_result;
	}

	if ( ! $install_result ) {
		return new WP_Error( 'plugin_error', us_translate( 'An error has occurred. Please reload the page and try again.' ) );
	}

	return $skin->result;
}
