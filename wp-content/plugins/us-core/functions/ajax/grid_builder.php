<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Load elements list HTML to choose from
 */
add_action( 'wp_ajax_usgb_get_elist_html', 'ajax_usgb_get_elist_html' );
function ajax_usgb_get_elist_html() {
	us_load_template( 'usof/templates/window_add', array(
		'elements' => us_config( 'grid-settings.elements', array() ),
	) );

	// We don't use JSON to reduce data size
	die;
}

/**
 * Load shortcode builder elements forms
 */
add_action( 'wp_ajax_usgb_get_ebuilder_html', 'ajax_usgb_get_ebuilder_html' );
function ajax_usgb_get_ebuilder_html() {
	$template_vars = array(
		'titles' => array(),
		'body' => '',
	);

	// Loading all the forms HTML
	foreach ( us_config( 'grid-settings.elements', array() ) as $type ) {
		$elm = us_config( 'elements/' . $type, array() );
		$template_vars['titles'][ $type ] = isset( $elm['title'] ) ? $elm['title'] : $type;
		$template_vars['body'] .= us_get_template( 'usof/templates/edit_form', array(
			'type' => $type,
			'params' => $elm['params'],
			'context' => 'grid',
		) );
	}

	us_load_template( 'usof/templates/window_edit', $template_vars );

	// We don't use JSON to reduce data size
	die;
}

/**
 * Load grid template selector forms
 */
add_action( 'wp_ajax_usgb_get_gtemplates_html', 'ajax_usgb_get_gtemplates_html' );
function ajax_usgb_get_gtemplates_html() {

	us_load_template( 'usof/templates/window_templates' );

	// We don't use JSON to reduce data size
	die;
}

/**
 * Save header
 */
add_action( 'wp_ajax_usgb_save', 'ajax_usgb_save' );
function ajax_usgb_save() {
	$post = array(
		'ID' => isset( $_POST['ID'] ) ? (int) $_POST['ID'] : NULL,
		'post_title' => isset( $_POST['post_title'] ) ? $_POST['post_title'] : NULL,
		'post_content' => isset( $_POST['post_content'] ) ? $_POST['post_content'] : NULL,
	);

	if ( ! check_admin_referer( 'usgb-update' ) OR ! current_user_can( 'edit_post', $post['ID'] ) ) {
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

add_action( 'wp_ajax_usgb_add_group_params', 'usgb_ajax_add_group_params' );
function usgb_ajax_add_group_params() {
	$element = sanitize_text_field( $_POST['element'] );
	$group = sanitize_text_field( $_POST['group'] );
	$index = sanitize_text_field( $_POST['index'] );

	$config = us_config( 'grid-settings', array() );
	$element_config = us_config( 'elements/' . $element );

	if ( isset( $element_config['params'][$group] ) ) {
		$field = $element_config['params'][$group];
		$result_html = '<div class="usof-form-group-item">';
		$result_html .= '<div class="usof-form-group-item-content">';
		ob_start();
		foreach ( $field['params'] as $param_name => $param ) {
			if ( isset( $param['show_if'] ) AND is_array( $param['show_if'] ) ) {
				$param['show_if'][0] = $group . '_' . $index . '_' . $param['show_if'][0];
			}
			us_load_template(
				'usof/templates/field', array(
					'name' => $group . '_' . $index . '_' . $param_name,
					'id' => 'usof_' . $group . '_' . $index . '_' . $param_name,
					'field' => $param,
					'values' => array(),
				)
			);
		}
		$result_html .= ob_get_clean();
		$result_html .= '</div>';
		$result_html .= '<div class="usof-form-group-delete" title="' . us_translate( 'Delete' ) . '"></div>';
		$result_html .= '</div>';

		wp_send_json_success(
			array(
				'paramsHtml' => $result_html,
			)
		);
	} else {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

}
