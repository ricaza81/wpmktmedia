<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );


add_action( 'wp_ajax_nopriv_us_cookie_set_amp_cookie', 'us_cookie_set_amp_cookie' );
add_action( 'wp_ajax_us_cookie_set_amp_cookie', 'us_cookie_set_amp_cookie' );
function us_cookie_set_amp_cookie() {
	$cookie_status = setcookie( 'us_cookie_notice_accepted', TRUE, strtotime( '+1 week' ), '/', '', is_ssl() );

	wp_send_json( [ 'cookie_set' => $cookie_status ], 200 );
}
