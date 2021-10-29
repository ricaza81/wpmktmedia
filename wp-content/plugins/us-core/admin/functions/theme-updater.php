<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme updater for activated licenses
 */

if ( ! defined ( 'US_THEMENAME' ) ) {
	return;
}

$license_activated = get_option( 'us_license_activated', 0 );
$license_dev_activated = get_option( 'us_license_dev_activated', 0 );
$license_secret = get_option( 'us_license_secret' );

if ( ( $license_activated OR $license_dev_activated ) AND $license_secret != '' ) {
	function us_api_themes_update( $updates ) {

		$updates = us_check_theme_updates( $updates );

		return $updates;
	}

	add_filter( "pre_set_site_transient_update_themes", "us_api_themes_update" );
} else {
	function us_api_themes_update_deactivated( $updates ) {

		$updates = us_check_theme_updates_deactivated( $updates );

		return $updates;
	}

	add_filter( "pre_set_site_transient_update_themes", "us_api_themes_update_deactivated" );
}

function us_check_theme_updates_deactivated( $updates ) {
	$result = us_api_get_themes_deactivated();

	if ( ! empty( $result->data->new_version ) ) {

		$installed = wp_get_themes();
		$filtered = array();
		foreach ( $installed as $theme ) {
			$filtered[$theme->Name] = $theme;
		}

		if ( isset( $filtered[US_THEMENAME] ) ) {
			$current = $filtered[US_THEMENAME];

			if ( version_compare( $current->Version, $result->data->new_version, '<' ) ) {
				$update = array(
					"url" => $result->data->url,
					"new_version" => $result->data->new_version,
					"package" => NULL,
				);

				$updates->response[ US_THEMENAME ] = $update;
			}
		}
	}

	return $updates;
}

function us_api_get_themes_deactivated( $timeout = 86400 /* 24 hours */ ) {
	global $help_portal_url;
	$urlparts = parse_url( site_url() );

	$url = $help_portal_url . "/us.api/check_update/" . strtolower( US_THEMENAME ) . "?current_version=" . urlencode( US_THEMEVERSION );
	$transient = 'us_update_theme_data_deactivated_' . US_THEMENAME;

	/* create the cache and allow filtering before it's saved */
	if ( $results = us_api_remote_request( $url ) ) {
		set_transient( $transient, $results, $timeout );

		return $results;
	}
}

function us_check_theme_updates( $updates ) {
	$license_secret = get_option( 'us_license_secret' );

	$result = us_api_get_themes( $license_secret );

	if ( ! empty( $result->data->new_version ) ) {

		$installed = wp_get_themes();
		$filtered = array();
		foreach ( $installed as $theme ) {
			$filtered[$theme->Name] = $theme;
		}

		if ( isset( $filtered[US_THEMENAME] ) ) {
			$current = $filtered[US_THEMENAME];

			if ( version_compare( $current->Version, $result->data->new_version, '<' ) ) {
				$update = array(
					"url" => $result->data->url,
					"new_version" => $result->data->new_version,
					"package" => $result->data->package,
				);

				$updates->response[US_THEMENAME] = $update;
			}
		}
	}

	return $updates;
}

function us_api_get_themes( $license_secret, $timeout = 10800 /* 3 hours */ ) {
	global $help_portal_url;
	$urlparts = parse_url( site_url() );
	$domain = $urlparts['host'];

	$url = $help_portal_url . "/us.api/check_update/" . strtolower( US_THEMENAME ) . "?secret=" . urlencode( $license_secret ) . "&domain=" . urlencode( $domain ) . "&current_version=" . urlencode( US_THEMEVERSION );
	$transient = 'us_update_theme_data_' . US_THEMENAME;

	if ( FALSE !== $results = get_transient( $transient ) ) {
		return $results;
	}

	/* create the cache and allow filtering before it's saved */
	if ( $results = us_api_remote_request( $url ) ) {
		set_transient( $transient, $results, $timeout );

		return $results;
	}
}

add_filter( 'sanitize_key', 'us_sanitize_key_themename', 10, 2 );
function us_sanitize_key_themename( $key, $raw_key ) {
	if ( in_array( $raw_key, array( 'Impreza', 'Zephyr' ) ) ) {
		$key = $raw_key;
	}

	return $key;
}

add_action( '_network_admin_menu', 'us_add_theme_update_notice_to_menu' );
add_action( '_user_admin_menu', 'us_add_theme_update_notice_to_menu' );
add_action( '_admin_menu', 'us_add_theme_update_notice_to_menu' );
function us_add_theme_update_notice_to_menu() {
	global $menu;

	if ( isset($menu[60]) AND isset($menu[60][2]) AND $menu[60][2] == 'themes.php' ) {
		$update_notification = '';
		$update_themes = get_site_transient( 'update_themes' );
		if ( ! empty( $update_themes->response ) AND isset( $update_themes->response[US_THEMENAME] ) ) {
			$update_notification = ' <span class="update-plugins count-1"><span class="plugin-count">1</span></span>';
		}
		$menu[60][0] = us_translate( 'Appearance' ) . $update_notification;
	}

}

add_filter( 'wp_prepare_themes_for_js', 'us_wp_prepare_themes_for_js', 10 );
function us_wp_prepare_themes_for_js( $themes ) {
	if ( ! empty( $themes ) ) {
		if ( ! empty ( $themes[ US_THEMENAME ] ) ) {
			$theme_args = $themes[ US_THEMENAME ];

			if ( us_get_option( 'white_label', '' ) ) {
				if ( ! empty( us_get_option( 'white_label_theme_name', '' ) ) ) {
					$themes[ US_THEMENAME ]['name'] = wp_strip_all_tags( us_get_option( 'white_label_theme_name' ), TRUE );
				}
				if ( ! empty( us_get_option( 'white_label_theme_screenshot', '' ) ) ) {
					$screenshot_id = us_get_option( 'white_label_theme_screenshot', '' );
					$themes[ US_THEMENAME ]['screenshot'] = wp_get_attachment_image_src( $screenshot_id, 'full' );
				}
				if ( $theme_args['hasUpdate'] AND ( ! $theme_args['hasPackage'] ) ) {
					$theme_args['update'] = str_replace( ' ' . US_THEMENAME . ' ', ' ' . $themes[ US_THEMENAME ]['name'] . ' ', $theme_args['update'] );
				}
			}
			if ( $theme_args['hasUpdate'] AND ( ! $theme_args['hasPackage'] ) ) {
				$themes[ US_THEMENAME ]['update'] = $theme_args['update'] .
					'<p><strong>' . sprintf( __( '%sActivate the theme%s to update it', 'us' ), '<a href="' . admin_url( 'admin.php?page=us-home#activation' ) . '">', '</a>' ) . '.</strong></p>';
			}
		}

	}

	return $themes;
}

add_filter( 'site_transient_update_themes', 'us_site_transient_update_themes' );
function us_site_transient_update_themes( $current ) {
	global $pagenow;
	if ( ! empty( $pagenow ) AND $pagenow == 'update-core.php' ) {
		if ( ! empty( $current->response ) ) {
			foreach ( $current->response as $stylesheet => $data ) {
				if ( $stylesheet == US_THEMENAME AND empty( $data['package'] ) ) {
					$current->response[$stylesheet]['new_version'] .= '.<br>' . sprintf( __( '%sActivate the theme%s to update it', 'us' ), '<a href="' . admin_url( 'admin.php?page=us-home#activation' ) . '">', '</a>' );
				}
			}
		}

	}
	return $current;
}

add_action( 'upgrader_process_complete', 'us_upgrader_process_complete', 10, 2 );
function us_upgrader_process_complete( $upgrader, $atts ) {
	if ( $atts['action'] == 'update' AND $atts['type'] == 'theme' AND $atts['bulk'] == 1) {
		if ( ! empty( $atts['themes'] ) ) {
			foreach ( $atts['themes'] as $theme ) {
				if ( $theme == US_THEMENAME AND ! (
					get_option( 'us_license_activated', 0 ) == 1
					OR get_option( 'us_license_dev_activated', 0 ) == 1 ) ) {
					echo '<div class="error"><p>' . sprintf( __( '%sActivate the theme%s to update it', 'us' ), '<a target="_top" href="' . admin_url( 'admin.php?page=us-home#activation' ) . '">', '</a>' ) . '</p></div>';
				}
			}
		}

	}
}

if ( us_get_option( 'white_label', '' ) ) {

	if ( ! function_exists( 'us_theme_name_white_label' ) ) {
		add_filter( 'us_theme_name', 'us_theme_name_white_label' );
		function us_theme_name_white_label( $theme_name ) {
			if ( us_get_option( 'white_label_theme_name', '' ) != '' ) {
				$theme_name = wp_strip_all_tags( us_get_option( 'white_label_theme_name' ), TRUE );
			}

			return $theme_name;
		}
	}

	// White label Dashbord message
	if ( ! function_exists( 'us_update_right_now_text' ) ) {
		add_filter( 'update_right_now_text', 'us_update_right_now_text', 99 );
		function us_update_right_now_text( $content ) {
			if ( ! empty( us_get_option( 'white_label_theme_name', '' ) ) ) {
				$content = sprintf( $content, get_bloginfo( 'version', 'display' ), wp_strip_all_tags( us_get_option( 'white_label_theme_name' ), TRUE ) );
			}

			return $content;
		}
	}

}
