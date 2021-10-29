<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

add_filter( 'usof_container_classes', 'usgb_usof_container_classes' );// TODO: do we need this?
function usgb_usof_container_classes( $classes ) {
	return $classes . ' with_gb';
}

/**
 * Get available grids
 * @return array
 */
function usgb_get_existing_grids() {
	$result = array();
	$grids = get_posts(
		array(
			'post_type' => 'us_grid_layout',
			'posts_per_page' => - 1,
			'post_status' => 'any',
			'suppress_filters' => 0,
		)
	);
	foreach ( $grids as $grid ) {
		$result[$grid->ID] = $grid->post_title;
	}

	return $result;
}

function usgb_enqueue_scripts() {

	// Appending dependencies
	usof_print_scripts();

	// Appending required assets
	wp_enqueue_script( 'us-grid-builder', US_CORE_URI . '/admin/js/grid-builder.js', array(), TRUE ); // TODO: check if we need deps

	// Disabling WP auto-save
	wp_dequeue_script( 'autosave' );
}

function usgb_edit_form_top( $post ) {
	global $help_portal_url;
	$post = get_post( $post->ID );
	echo '<div class="usof-container type_builder" data-ajaxurl="' . esc_attr( admin_url( 'admin-ajax.php' ) ) . '" data-id="' . esc_attr( $post->ID ) . '">';
	echo '<form class="usof-form" method="post" action="#" autocomplete="off">';
	// Output _nonce and _wp_http_referer hidden fields for ajax secuirity checks
	wp_nonce_field( 'usgb-update' );
	echo '<div class="usof-header">';
	echo '<div class="usof-header-title">' . __( 'Grid Layout', 'us' ) . '</div>';

	us_load_template(
		'usof/templates/field', array(
			'name' => 'post_title',
			'id' => 'usof_header_title',
			'field' => array(
				'type' => 'text',
				'placeholder' => __( 'Grid Layout Name', 'us' ),
				'classes' => 'desc_0', // Reset desc position of global GB field
			),
			'values' => array(
				'post_title' => $post->post_title,
			),
		)
	);

	echo '<div class="usof-control for_help"><a href="'. $help_portal_url .'/' . strtolower( US_THEMENAME ) . '/grid/" target="_blank" rel="noopener" title="' . us_translate( 'Help' ) . '"></a></div>';
	echo '<div class="usof-control for_import"><a href="#">' . __( 'Export / Import', 'us' ) . '</a></div>';
	echo '<div class="usof-control for_templates"><a href="#">' . us_translate_x( 'Templates', 'TinyMCE' ) . '</a>';
	echo '<div class="usof-control-desc"><span>' . __( 'Choose Grid Layout Template to start with', 'us' ) . '</span></div>';
	echo '</div>';
	echo '<div class="usof-control for_save status_clear">';
	echo '<button class="usof-button button-primary type_save" type="button"><span>' . us_translate( 'Save Changes' ) . '</span>';
	echo '<span class="usof-preloader"></span></button>';
	echo '<div class="usof-control-message"></div></div></div>';

	us_load_template(
		'usof/templates/field', array(
			'name' => 'post_content',
			'id' => 'usof_header',
			'field' => array(
				'type' => 'grid_builder',
				'classes' => 'desc_0', // Reset desc position of global GB field
			),
			'values' => array(
				'post_content' => $post->post_content,
			),
		)
	);

	echo '</form>';
	echo '</div>';
}

// Add "Duplicate" link for Grid Layouts admin page
add_filter( 'post_row_actions', 'usgb_post_row_actions', 10, 2 );
function usgb_post_row_actions( $actions, $post ) {
	if ( $post->post_type === 'us_grid_layout' ) {
		// Removing duplicate post plugin affection
		unset( $actions['duplicate'], $actions['edit_as_new_draft'] );

		if ( empty( $actions ) ) {
			$actions = array();
		}

		$actions = us_array_merge_insert(
			$actions, array(
			'duplicate' => '<a href="' . admin_url( 'post-new.php?post_type=us_grid_layout&duplicate_from=' . $post->ID ) . '" aria-label="' . esc_attr__( 'Duplicate', 'us' ) . '">' . esc_html__( 'Duplicate', 'us' ) . '</a>',
		), 'before', isset( $actions['trash'] ) ? 'trash' : 'untrash'
		);
	}

	return $actions;
}
