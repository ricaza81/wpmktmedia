<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Outputs page's Footer
 */

$us_layout = US_Layout::instance();
?>
</div>
<?php

// Show Footer when the page is not in iframe
global $us_iframe;
if ( ! isset( $us_iframe ) OR ! $us_iframe ) {
	do_action( 'us_before_footer' );
	us_register_context_layout( 'footer' );
	?>
	<footer id="page-footer" class="l-footer"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemscope itemtype="https://schema.org/WPFooter"' : ''; ?>>
		<?php
		$footer_id = us_get_page_area_id( 'footer' );

		// Output content of Page Block (us_page_block) post
		$footer_content = '';
		if ( $footer_id != '' ) {

			$footer = get_post( (int) $footer_id );

			us_open_wp_query_context();
			if ( $footer ) {
				$translated_footer_id = apply_filters( 'us_tr_object_id', $footer->ID, 'us_page_block', TRUE );
				if ( $translated_footer_id AND $translated_footer_id != $footer->ID ) {
					$footer = get_post( $translated_footer_id );
				}

				us_add_to_page_block_ids( $translated_footer_id );

				us_add_page_shortcodes_custom_css( $translated_footer_id );

				$footer_content = $footer->post_content;
			}
			us_close_wp_query_context();

			// Apply filters to Page Block content and echoing it ouside of us_open_wp_query_context,
			// so all WP widgets (like WP Nav Menu) would work as they should
			echo apply_filters( 'us_page_block_the_content', $footer_content );

			if ( $footer ) {
				us_remove_from_page_block_ids();
			}

		}
		?>
	</footer>
	<?php
	do_action( 'us_after_footer' );
}

// Output "Back to top" button
if ( us_get_option( 'back_to_top', 1 ) ) {
	$back_to_top_atts = array(
		'class' => 'w-toplink pos_' . us_get_option( 'back_to_top_pos', 'right' ),
		'href' => '#',
		'title' => __( 'Back to top', 'us' ),
		'aria-label' => __( 'Back to top', 'us' ),
	);
	if ( $back_to_top_style = us_get_option( 'back_to_top_style', '' ) ) {
		$back_to_top_atts['class'] .= ' w-btn us-btn-style_' . $back_to_top_style;
	}

	echo '<a' . us_implode_atts( $back_to_top_atts ) . '><span></span></a>';
}

if ( $us_layout->header_show != 'never' ) {
	$_header_show_atts['id'] = 'w-header-show';
	$_header_show_atts['class'] = 'w-header-show';
	if ( us_amp() ) {
		$_header_show_atts['on'] = 'tap:amp-body-id.toggleClass(class=\'header-show\')';
	} else {
		$_header_show_atts['href'] = 'javascript:void(0);';
	}
	?>
	<a<?= us_implode_atts( $_header_show_atts ) ?>><span><?= us_translate( 'Menu' ) ?></span></a>
	<div class="w-header-overlay"<?= us_amp() ? ( 'on="' . $_header_show_atts['on'] . '"' ) : '' ?>></div>
	<?php
}

if ( ! us_amp() ) {
	ob_start();
	?>
	<script>
		// Store some global theme options used in JS
		if ( window.$us === undefined ) {
			window.$us = {};
		}
		$us.canvasOptions = ( $us.canvasOptions || {} );
		$us.canvasOptions.disableEffectsWidth = <?php echo (int) us_get_option( 'disable_effects_width', 900 ) ?>;
		$us.canvasOptions.columnsStackingWidth = <?php echo (int) us_get_option( 'columns_stacking_width', 768 ) ?>;
		$us.canvasOptions.backToTopDisplay = <?php echo (int) us_get_option( 'back_to_top_display', 100 ) ?>;
		$us.canvasOptions.scrollDuration = <?php echo (int) us_get_option( 'smooth_scroll_duration', 1000 ) ?>;

		$us.langOptions = ( $us.langOptions || {} );
		$us.langOptions.magnificPopup = ( $us.langOptions.magnificPopup || {} );
		$us.langOptions.magnificPopup.tPrev = '<?php _e( 'Previous (Left arrow key)', 'us' ); ?>';
		$us.langOptions.magnificPopup.tNext = '<?php _e( 'Next (Right arrow key)', 'us' ); ?>';
		$us.langOptions.magnificPopup.tCounter = '<?php _ex( '%curr% of %total%', 'Example: 3 of 12', 'us' ); ?>';

		$us.navOptions = ( $us.navOptions || {} );
		$us.navOptions.mobileWidth = <?php echo (int) us_get_option( 'menu_mobile_width', 900 ) ?>;
		$us.navOptions.togglable = <?php echo us_get_option( 'menu_togglable_type', TRUE ) ? 'true' : 'false' ?>;
		$us.ajaxLoadJs = <?php echo us_get_option( 'ajax_load_js', 0 ) ? 'true' : 'false' ?>;
		$us.templateDirectoryUri = '<?php global $us_template_directory_uri; echo $us_template_directory_uri; ?>';
	</script>
	<?php
	/**
	 * Global Theme Options JS variables output filter
	 */
	echo apply_filters( 'us_global_theme_options_js', ob_get_clean() );
}
wp_footer();
?>
</body>
</html>
