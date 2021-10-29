<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

// Get Page Blocks
global $pagenow;
$us_page_blocks_list = array();
if ( is_admin() AND
	( wp_doing_ajax() OR in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) ) {
	$us_page_blocks_list = us_get_posts_titles_for( 'us_page_block' );
}

// Visual Composer sends 'post_id' POST variable to the element popup. We will use it to remove current page block from list.
if ( ! empty( $_POST['post_id'] ) AND isset( $us_page_blocks_list[ $_POST['post_id'] ] ) ) {
	unset( $us_page_blocks_list[ $_POST['post_id'] ] );
}


return array(
	'title' => __( 'Page Block', 'us' ),
	'icon' => 'far fa-square',
	'params' => array(

		'id' => array(
			'title' => __( 'Page Block', 'us' ),
			'type' => 'select',
			'options' => $us_page_blocks_list,
			'std' => '',
			'admin_label' => TRUE,
		),
		'remove_rows' => array(
			'title' => __( 'Exclude Rows and Columns', 'us' ),
			'type' => 'select',
			'options' => array(
				'' => us_translate( 'None' ),
				'1' => __( 'Inside selected Page Block', 'us' ),
				'parent_row' => __( 'Around this element', 'us' ),
			),
			'std' => '',
		),
		'force_fullwidth_rows' => array(
			'switch_text' => __( 'Stretch content of Rows to the full width', 'us' ),
			'type' => 'switch',
			'std' => FALSE,
			'show_if' => array( 'remove_rows', '!=', '1' ),
		),

	),
);
