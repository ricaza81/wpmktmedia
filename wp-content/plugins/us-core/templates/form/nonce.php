<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a form's nonce field
 *
 * @var $name        string Nonce Name
 * @var $action      string Nonce Action
 */

if ( ! empty( $action ) AND ! empty( $name ) ) {
	wp_nonce_field( $action, $name );
}
