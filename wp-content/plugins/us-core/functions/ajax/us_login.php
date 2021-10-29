<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ajax methods for Login element
 */
add_action( 'init', 'us_ajax_login_init' );
function us_ajax_login_init() {
	if ( ! is_user_logged_in() ) {
		add_action( 'wp_ajax_nopriv_us_ajax_login', 'us_ajax_login' );
		add_action( 'wp_ajax_us_ajax_login', 'us_ajax_login' );
	}
}
function us_ajax_login() {

	// Check form nonce
	check_ajax_referer( 'us_ajax_login_nonce', 'us_login_nonce' );

	// Get form data
	$info = array(
		// Don't trust but pass as is, it will be sanitized by WordPress
		'user_login' => $_POST['username'],
		'user_password' => $_POST['password'],
		'remember' => TRUE,
	);

	// Logging
	$user_signon = wp_signon( $info, is_ssl() );
	$message = $user_signon->get_error_message();
	$error_code = $user_signon->get_error_code();

	// Format error message to cut a link and leading ERROR, NOTICE etc words
	$pattern = '#^(<strong>[^>]+>:\s)?((?:(?! <a href).)+)([\s\S]+)#i';
	$message = ucfirst( preg_replace( $pattern, '$2', $message ) );
	$result = array(
		'message' => $message,
		'code' => $error_code,
	);

	if ( is_wp_error( $user_signon ) ) {
		if ( us_amp() ) {
			wp_send_json( compact( 'message' ), 400 );
		} else {
			wp_send_json_error( $result );
		}
	} else {
		if ( us_amp() ) {
			// Redirect after logging in
			$redirect_url = ! empty( $_POST['redirect_to'] ) ? esc_url( $_POST['redirect_to'] ) : '';
			if ( $redirect_url ) {
				header( 'AMP-Redirect-To: ' . $redirect_url );
				header( 'Access-Control-Expose-Headers: AMP-Redirect-To' );
			}
			$message = us_translate( 'You have logged in successfully.' );
			wp_send_json( compact( 'message' ), 200 );
		} else {
			wp_send_json_success();
		}
	}
}

/**
 * Ajax methods to show user's profile in Login element/widget
 */
add_action( 'wp_ajax_nopriv_us_ajax_user_info', 'us_ajax_user_profile' );
add_action( 'wp_ajax_us_ajax_user_info', 'us_ajax_user_profile' );
function us_ajax_user_profile() {
	//	Send profile block if user is logged in
	if ( is_user_logged_in() ) {
		$logout_redirect = isset( $_POST['logout_redirect'] ) ? esc_url( $_POST['logout_redirect'] ) : site_url( $_SERVER['REQUEST_URI'] );
		$output = us_user_profile_html( $logout_redirect );

		wp_send_json_success( $output );
	} else {
		wp_send_json_error();
	}

}