<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$design_options = us_config( 'elements_design_options' );

return array(
	'title' => us_translate( 'Product gallery', 'woocommerce' ),
	'category' => __( 'Post Elements', 'us' ),
	'params' => array_merge( array(

		'hide_input' => array(
			'title' => sprintf( __( 'Edit Product gallery appearance on %sTheme Options%s.', 'us' ), '<a target="_blank" rel="noopener" href="' . admin_url() . 'admin.php?page=us-theme-options#woocommerce">', '</a>' ),
			'type' => 'info',
		),

	), $design_options ),
);
