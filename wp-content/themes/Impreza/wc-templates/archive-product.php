<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * Required for "Content template" option
 */

get_header( 'shop' );

?>
<main id="page-content" class="l-main"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemprop="mainContentOfPage"' : ''; ?>>
	<?php
	if ( us_get_option( 'enable_sidebar_titlebar', 0 ) ) {

		// Titlebar, if it is enabled in Theme Options
		us_load_template( 'templates/titlebar' );

		// START wrapper for Sidebar
		us_load_template( 'templates/sidebar', array( 'place' => 'before' ) );
	}

	us_load_template( 'templates/content' );

	if ( us_get_option( 'enable_sidebar_titlebar', 0 ) ) {
		// AFTER wrapper for Sidebar
		us_load_template( 'templates/sidebar', array( 'place' => 'after' ) );
	}

	?>
</main>
<?php

get_footer( 'shop' );
