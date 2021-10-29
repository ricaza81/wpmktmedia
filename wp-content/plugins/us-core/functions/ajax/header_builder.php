<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Load elements list HTML to choose from
 */
if ( ! function_exists( 'us_ajax_hb_get_elist_html' ) ) {
	add_action( 'wp_ajax_us_ajax_hb_get_elist_html', 'us_ajax_hb_get_elist_html' );
	function us_ajax_hb_get_elist_html() {
		us_load_template(
			'usof/templates/window_add', array(
				'elements' => us_config( 'header-settings.elements', array() ),
			)
		);

		// We don't use JSON to reduce data size
		die;
	}
}


/**
 * Load shortcode builder elements forms
 */
if ( ! function_exists( 'us_ajax_hb_get_ebuilder_html' ) ) {
	add_action( 'wp_ajax_us_ajax_hb_get_ebuilder_html', 'us_ajax_hb_get_ebuilder_html' );
	function us_ajax_hb_get_ebuilder_html() {
		$template_vars = array(
			'titles' => array(),
			'body' => '',
		);

		// Loading all the forms HTML
		foreach ( us_config( 'header-settings.elements', array() ) as $type ) {
			$elm = us_config( 'elements/' . $type );
			$template_vars['titles'][ $type ] = isset( $elm['title'] ) ? $elm['title'] : $type;
			$template_vars['body'] .= us_get_template(
				'usof/templates/edit_form', array(
					'type' => $type,
					'params' => $elm['params'],
				)
			);
		}

		us_load_template( 'usof/templates/window_edit', $template_vars );

		// We don't use JSON to reduce data size
		die;
	}
}

/**
 * Load header template selector forms
 */
if ( ! function_exists( 'us_ajax_hb_get_htemplates_html' ) ) {
	add_action( 'wp_ajax_us_ajax_hb_get_htemplates_html', 'us_ajax_hb_get_htemplates_html' );
	function us_ajax_hb_get_htemplates_html() {
		us_load_template( 'usof/templates/window_header_templates' );

		// We don't use JSON to reduce data size
		die;
	}
}

/**
 * Save header
 */
if ( ! function_exists( 'us_ajax_hb_save' ) ) {
	add_action( 'wp_ajax_us_ajax_hb_save', 'us_ajax_hb_save' );
	function us_ajax_hb_save() {
		$post = array(
			'ID' => isset( $_POST['ID'] ) ? (int) $_POST['ID'] : NULL,
			'post_title' => isset( $_POST['post_title'] ) ? $_POST['post_title'] : NULL,
			'post_content' => isset( $_POST['post_content'] ) ? $_POST['post_content'] : NULL,
		);

		if ( ! check_admin_referer( 'ushb-update' ) OR ! current_user_can( 'edit_post', $post['ID'] ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
		}

		if ( ! $post['ID'] ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
		}

		if ( wp_update_post( $post ) !== $post['ID'] ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
		}

		// Fix: Save translations for Polylang
		if ( function_exists( 'pll_set_post_language' ) ) {
			if ( ! empty( $_POST['post_lang_choice'] ) ) {
				pll_set_post_language( $post['ID'], sanitize_text_field( $_POST['post_lang_choice'] ) );
			}
			if ( ! empty( $_POST['post_tr_lang'] ) AND is_array( $_POST['post_tr_lang'] ) ) {
				$translations = array();
				foreach ( (array) us_arr_path( $_POST, 'post_tr_lang', array() ) as $lang_code => $post_id ) {
					$translations[ $lang_code ] = (int) $post_id;
				}
				// Save translations for post
				if ( ! empty( $translations ) AND function_exists( 'PLL' )) {
					PLL()->model->post->save_translations( $post['ID'], array_map( 'absint', $translations ) );
				}
			}
		}

		wp_send_json_success(
			array(
				'message' => us_translate( 'Changes saved.' ),
			)
		);
	}
}
