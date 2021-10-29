<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Updater of the plugins from UpSolution Help Portal
 */

add_filter( 'us_config_addons', 'us_api_addons' );

function us_api_addons( $plugins, $force_request = FALSE ) {
	global $help_portal_url;
	$license_activated = get_option( 'us_license_activated', 0 );
	$license_dev_activated = get_option( 'us_license_dev_activated', 0 );
	$license_secret = get_option( 'us_license_secret' );
	$addons_version = get_option( 'us_addons_version' );

	if ( ( $license_activated OR $license_dev_activated ) AND $license_secret != '' ) {

		$urlparts = parse_url( site_url() );
		$domain = $urlparts['host'];

		$url = $help_portal_url . "/us.api/check_addons_update/" . strtolower( US_THEMENAME ) . "?secret=" . urlencode( $license_secret ) . "&domain=" . urlencode( $domain ) . "&current_version=" . urlencode( US_THEMEVERSION );
		$transient = 'us_update_addons_data_' . US_THEMENAME;

		if ( ( ! empty( $addons_version ) AND $addons_version == US_THEMEVERSION ) AND FALSE !== $results = get_transient( $transient ) ) {
			$update_addons_data = $results;
		}

		if ( ( empty( $update_addons_data ) OR $force_request ) AND $results = us_api_remote_request( $url ) ) {
			set_transient( $transient, $results, 1800 );
			$update_addons_data = $results;
			update_option( 'us_addons_version', US_THEMEVERSION );
		}

		if ( ! empty( $update_addons_data->data ) ) {
			foreach ( $plugins as $i => $plugin ) {
				$slug = $plugin['slug'];
				if ( isset( $update_addons_data->data->$slug ) ) {
					// Do not update version and source from Help Portal if config has higher version
					if ( isset( $plugins[$i]['version'] )
						AND version_compare( $update_addons_data->data->$slug->new_version, $plugins[$i]['version'], '<' ) ) {
						continue;
					}
					$plugins[$i]['version'] = $update_addons_data->data->$slug->new_version;
					$plugins[$i]['source'] = $update_addons_data->data->$slug->package;
				}
			}
		}
	}

	return $plugins;
}

$addons = us_config( 'addons', array() );

foreach ( $addons as $i => $addon ) {
	if ( empty( $addons[$i]['version'] ) OR empty( $addons[$i]['source'] ) ) {
		unset( $addons[$i] );
	}
}

if ( empty( $addons ) ) {
	return;
}

// Transient hook for automatical updates of bundled plugins
add_action( 'site_transient_update_plugins', 'us_addons_transient_update' );
function us_addons_transient_update( $transient ) {

	// Exit if no response is set
	if ( ! isset( $transient->response ) ) {
		return $transient;
	}

	$installed_plugins = get_plugins();

	$addons = us_config( 'addons', array() );

	foreach ( $addons as $i => $addon ) {
		if ( empty( $addons[$i]['version'] ) OR empty( $addons[$i]['source'] ) ) {
			unset( $addons[$i] );
		}
	}

	foreach ( $addons as $addon ) {
		$plugin_basename = sprintf( '%s/%s.php', $addon['slug'], $addon['slug'] );

		if ( ! isset( $installed_plugins[$plugin_basename] ) ) {
			continue;
		}

		if ( version_compare( $installed_plugins[$plugin_basename]['Version'], $addon['version'], '<' ) ) {
			$transient->response[$plugin_basename] = new StdClass();
			$transient->response[$plugin_basename]->plugin = $plugin_basename;
			$transient->response[$plugin_basename]->url = $addon['changelog_url'];
			$transient->response[$plugin_basename]->slug = $addon['slug'];
			$transient->response[$plugin_basename]->package = $addon['source'];
			$transient->response[$plugin_basename]->new_version = $addon['version'];
			$transient->response[$plugin_basename]->id = '0';
		}
	}

	return $transient;
}

// Seen when user clicks "view details" on the plugin listing page
add_action( 'install_plugins_pre_plugin-information', 'us_addons_update_popup' );
function us_addons_update_popup() {

	if ( ! isset( $_GET['plugin'] ) ) {
		return;
	}

	$plugin_slug = sanitize_file_name( $_GET['plugin'] );

	$addons = us_config( 'addons', array() );

	foreach ( $addons as $i => $addon ) {
		if ( empty( $addons[$i]['version'] ) OR empty( $addons[$i]['source'] ) ) {
			unset( $addons[$i] );
		}
	}

	foreach ( $addons as $addon ) {
		if ( $addon['slug'] == $plugin_slug ) {
			$changelog_url = $addon['changelog_url'];

			echo '<html><body style="height: 90%; background: #fcfcfc"><p>See the <a href="' . $changelog_url . '" ' . 'target="_blank" rel="noopener">' . $changelog_url . '</a> for the detailed changelog</p></body></html>';

			exit;
		}
	}
}

add_action( 'admin_head', 'us_remove_bsf_update_counter' );
function us_remove_bsf_update_counter() {
	remove_action( 'admin_head', 'bsf_update_counter', 999 );
}
