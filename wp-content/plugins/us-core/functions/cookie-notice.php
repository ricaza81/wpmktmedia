<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output cookie notice bar
 */
if ( ! function_exists( 'us_cookie_notice_output' ) ) {
	if ( us_get_option( 'cookie_notice', 0 ) ) {
		if ( ! isset( $_COOKIE ) OR ! isset( $_COOKIE['us_cookie_notice_accepted'] ) ) {
			add_action( 'wp_footer', 'us_cookie_notice_output', 90 );
		}
	}

	function us_cookie_notice_output() {

		// Do nothing since AMP-consent works only via SSL
		if ( us_amp() AND ! is_ssl() ) {
			return FALSE;
		}

		$output = '';

		$cookie_message = us_get_option( 'cookie_message', '' );

		// Add link to Privacy Policy page
		if ( ! empty( us_get_option( 'cookie_privacy' ) ) ) {
			$cookie_message .= ' ' . get_the_privacy_policy_link();
		}

		// Output bar, only if the message is not empty
		if ( $cookie_message ) {

			// Use specific component for AMP https://amp.dev/documentation/components/amp-consent/
			if ( us_amp() ) {
				$consent_data = array(
					'consentInstanceId' => 'us_cookie_notice_check_consent',
					'promptUI' => 'us_cookie_ui',
					'consentRequired' => TRUE,
					'onUpdateHref' => add_query_arg( 'action', 'us_cookie_set_amp_cookie', admin_url( 'admin-ajax.php' ) ),
				);
				$output .= '<amp-consent id="us_cookie" layout="nodisplay">';
				$output .= '<script type="application/json">';
				$output .= wp_json_encode( $consent_data );
				$output .= '</script>';
				$output .= '<div id="us_cookie_ui" class="l-cookie pos_' . us_get_option( 'cookie_message_pos', 'bottom' ) . ' ">';
				$output .= '<div class="l-cookie-message">' . $cookie_message . '</div>';

				// Accept button
				$output .= '<button on="tap:us_cookie.accept" class="w-btn us-btn-style_' . us_get_option( 'cookie_btn_style', '1' ) . '">';
				$output .= '<span>' . strip_tags( us_get_option( 'cookie_btn_label', 'Ok' ) ) . '</span>';
				$output .= '</button>';

				$output .= '</div>';
				$output .= '</amp-consent>';

			} else {
				$output .= '<div class="l-cookie pos_' . us_get_option( 'cookie_message_pos', 'bottom' ) . '">';
				$output .= '<div class="l-cookie-message">' . $cookie_message . '</div>';

				// Accept button
				$output .= '<a class="w-btn us-btn-style_' . us_get_option( 'cookie_btn_style', '1' ) . ' " id="us-set-cookie" href="javascript:void(0);">';
				$output .= '<span>' . strip_tags( us_get_option( 'cookie_btn_label', 'Ok' ) ) . '</span>';
				$output .= '</a>';

				$output .= '</div>';
			}
		}

		echo $output;
	}
}
