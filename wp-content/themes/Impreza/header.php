<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying website header
 *
 * Do not overload this file directly. Instead have a look at templates/header.php file in us-core plugin folder:
 * you should find all the needed hooks there.
 */

if ( function_exists( 'us_load_template' ) ) {

	us_load_template( 'templates/header' );

} else {
	?>
	<!DOCTYPE HTML>
	<html class="no-touch" <?php language_attributes( 'html' ) ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<?php wp_head(); ?>
	</head>
	<body <?php body_class( 'l-body header_hor state_default NO_US_CORE' ); ?>>
	<?php wp_body_open(); ?>
		<div class="l-canvas">
			<header class="l-header pos_static">
				<div class="l-subheader at_middle">
					<div class="l-subheader-h">
						<div class="l-subheader-cell at_left">
							<div class="w-text">
								<a class="w-text-h" href="/">
									<span class="w-text-value"><?php bloginfo( 'name' ); ?></span>
								</a>
							</div>
						</div>
						<div class="l-subheader-cell at_center"></div>
						<div class="l-subheader-cell at_right">
							<nav class="w-nav height_full dropdown_opacity type_desktop">
								<ul class="w-nav-list level_1">
									<?php
									wp_nav_menu(
										array(
											'theme_location' => 'us_main_menu',
											'container' => FALSE,
											'walker' => new US_Walker_Nav_Menu,
											'items_wrap' => '%3$s',
											'fallback_cb' => FALSE,
										)
									);
									?>
								</ul>
							</nav>
						</div>
					</div>
				</div>
			</header>
	<?php
}
