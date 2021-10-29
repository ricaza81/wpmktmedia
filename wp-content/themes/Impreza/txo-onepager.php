<?php
		/**
		 * Template Name: TX OnePage Home
		 * The template for displaying TemplatesNext OnePager Home Page
		 *
		 *
		 * @package templatesnext-onepager
		 * @since templatesnext-onepager 1.1.0
		 */
		
		if (function_exists('txo_sections_show')) {  
			tx_add_menu();
		}
		
		get_header(); ?>
			<div id="primary" class="content-area">
				<div id="content" class="site-content" role="main">
				<?php
				
					if (function_exists('txo_sections_show')) {
						txo_sections_show();
					}				
					
				 ?>
				</div><!-- #content -->
			</div><!-- #primary -->
		<?php get_footer();