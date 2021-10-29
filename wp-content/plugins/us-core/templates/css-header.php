<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Generates and outputs header generated stylesheets
 *
 * @action Before the template: us_before_template:templates/css-header
 * @action After the template: us_after_template:templates/css-header
 */

/* Generate media queries basen on breakpoint values */
$laptops_breakpoint = us_get_header_option( 'custom_breakpoint', 'laptops' )
	? (int) us_get_header_option( 'breakpoint', 'laptops' )
	: (int) us_get_option( 'laptops_breakpoint' );

$tablets_breakpoint = us_get_header_option( 'custom_breakpoint', 'tablets' )
	? (int) us_get_header_option( 'breakpoint', 'tablets' )
	: (int) us_get_option( 'tablets_breakpoint' );

$mobiles_breakpoint = us_get_header_option( 'custom_breakpoint', 'mobiles' )
	? (int) us_get_header_option( 'breakpoint', 'mobiles' )
	: (int) us_get_option( 'mobiles_breakpoint' );

$media_queries = array(
	'default' => '@media (min-width:' . ( $laptops_breakpoint + 1 ). 'px)',
	'laptops' => '@media (min-width:' . ( $tablets_breakpoint + 1 ) . 'px) and (max-width:' . $laptops_breakpoint . 'px)',
	'tablets' => '@media (min-width:' . ( $mobiles_breakpoint + 1 ) . 'px) and (max-width:' . $tablets_breakpoint . 'px)',
	'mobiles' => '@media (max-width:' . $mobiles_breakpoint . 'px)',
);

/* Header styles as variables */
global $us_template_directory;
$header_hor_styles = file_get_contents( $us_template_directory . '/common/css/base/header-hor.css' );
$header_ver_styles = file_get_contents( $us_template_directory . '/common/css/base/header-ver.css' );
$header_hor_pos_styles = file_get_contents( $us_template_directory . '/common/css/base/header-hor-position.css' );
?>

/* =============================================== */
/* ================ Header Colors ================ */
/* =============================================== */

<?php foreach ( array( 'top', 'middle', 'bottom' ) as $area ) :

	// Do not output extra CSS, if top or bottom areas are disabled in all states
	$show_state = FALSE;
	foreach ( (array) us_get_responsive_states( /* Only keys */TRUE ) as $state ) {
		if ( us_get_header_option( $area . '_show', $state ) ) {
			$show_state = TRUE;
			break;
		}
	}
	if ( $area !== 'middle' AND ! $show_state ) {
		continue;
	}
	?>
	.l-subheader.at_<?= $area ?>,
	.l-subheader.at_<?= $area ?> .w-dropdown-list,
	.l-subheader.at_<?= $area ?> .type_mobile .w-nav-list.level_1 {
		background: <?= us_get_color( us_get_header_option( $area . '_bg_color' ), TRUE ) ?>;
		color: <?= us_get_color( us_get_header_option( $area . '_text_color' ) ) ?>;
		}
	.no-touch .l-subheader.at_<?= $area ?> a:hover,
	.no-touch .l-header.bg_transparent .l-subheader.at_<?= $area ?> .w-dropdown.opened a:hover {
		color: <?= us_get_color( us_get_header_option( $area . '_text_hover_color' ) ) ?>;
		}
	.l-header.bg_transparent:not(.sticky) .l-subheader.at_<?= $area ?> {
		background: <?= us_get_color( us_get_header_option( $area . '_transparent_bg_color' ), TRUE ) ?>;
		color: <?= us_get_color( us_get_header_option( $area . '_transparent_text_color' ) ) ?>;
		}
	.no-touch .l-header.bg_transparent:not(.sticky) .at_<?= $area ?> .w-cart-link:hover,
	.no-touch .l-header.bg_transparent:not(.sticky) .at_<?= $area ?> .w-text a:hover,
	.no-touch .l-header.bg_transparent:not(.sticky) .at_<?= $area ?> .w-html a:hover,
	.no-touch .l-header.bg_transparent:not(.sticky) .at_<?= $area ?> .w-nav > a:hover,
	.no-touch .l-header.bg_transparent:not(.sticky) .at_<?= $area ?> .w-menu a:hover,
	.no-touch .l-header.bg_transparent:not(.sticky) .at_<?= $area ?> .w-search > a:hover,
	.no-touch .l-header.bg_transparent:not(.sticky) .at_<?= $area ?> .w-dropdown a:hover,
	.no-touch .l-header.bg_transparent:not(.sticky) .at_<?= $area ?> .type_desktop .menu-item.level_1:hover > a {
		color: <?= us_get_color( us_get_header_option( $area . '_transparent_text_hover_color' ) ) ?>;
		}
<?php endforeach; ?>

.header_ver .l-header {
	background: <?= us_get_color( us_get_header_option( 'middle_bg_color' ), TRUE ) ?>;
	color: <?= us_get_color( us_get_header_option( 'middle_text_color' ) ) ?>;
	}



/* =============================================== */
/* ============== Responsive states ============== */
/* =============================================== */

<?php foreach ( $media_queries as $state => $media_query ) :

echo $media_query ?> {

	.hidden_for_default { display: none !important; }

	<?php if ( ! us_get_header_option( 'top_show', $state ) ) { ?>
		.l-subheader.at_top { display: none; }
	<?php }
	if ( ! us_get_header_option( 'bottom_show', $state ) ) { ?>
		.l-subheader.at_bottom { display: none; }
	<?php }
	if (
		$bg_image = us_get_header_option( 'bg_img', $state )
		AND $bg_image_url = wp_get_attachment_image_url( $bg_image, 'full' )
	) {
		?>
		.l-subheader.at_middle {
			background-image: url(<?= esc_url( $bg_image_url ) ?>);
			background-attachment: <?= ( us_get_header_option( 'bg_img_attachment', $state ) ) ? 'scroll' : 'fixed'; ?>;
			background-position: <?= us_get_header_option( 'bg_img_position', $state ) ?>;
			background-repeat: <?= us_get_header_option( 'bg_img_repeat', $state ) ?>;
			background-size: <?= us_get_header_option( 'bg_img_size', $state ) ?>;
			}
	<?php }

	// HORIZONTAL HEADER
	if ( us_get_header_option( 'orientation', $state ) == 'hor' ) {
		echo $header_hor_styles;

		// Calculate header Default and Sticky heights (in PX) for all states
		$header_height = (int) us_get_header_option( 'middle_height', $state );
		if ( us_get_header_option( 'top_show', $state ) ) {
			$header_height += (int) us_get_header_option( 'top_height', $state );
		}
		if ( us_get_header_option( 'bottom_show', $state ) ) {
			$header_height += (int) us_get_header_option( 'bottom_height', $state );
		}
		$header_sticky_height = (int) us_get_header_option( 'middle_sticky_height', $state );
		if ( us_get_header_option( 'top_show', $state ) ) {
			$header_sticky_height+= (int) us_get_header_option( 'top_sticky_height', $state );
		}
		if ( us_get_header_option( 'bottom_show', $state ) ) {
			$header_sticky_height += (int) us_get_header_option( 'bottom_sticky_height', $state );
		}
		?>
		:root {
			--header-height: <?= $header_height ?>px;
			--header-sticky-height: <?= $header_sticky_height ?>px;
			}
		.l-header:before {
			content: '<?= $header_height ?>';
			}
		.l-header.sticky:before {
			content: '<?= $header_sticky_height  ?>';
			}

		.l-subheader.at_top {
			line-height: <?= us_get_header_option( 'top_height', $state ) ?>;
			height: <?= us_get_header_option( 'top_height', $state ) ?>;
			}
		.l-header.sticky .l-subheader.at_top {
			line-height: <?= us_get_header_option( 'top_sticky_height', $state ) ?>;
			height: <?= us_get_header_option( 'top_sticky_height', $state ) ?>;
			<?php if ( (int) us_get_header_option( 'top_sticky_height', $state ) === 0 ) { ?>
			overflow: hidden;
			<?php } ?>
			}

		.l-subheader.at_middle {
			line-height: <?= us_get_header_option( 'middle_height', $state ) ?>;
			height: <?= us_get_header_option( 'middle_height', $state ) ?>;
			}
		.l-header.sticky .l-subheader.at_middle {
			line-height: <?= us_get_header_option( 'middle_sticky_height', $state ) ?>;
			height: <?= us_get_header_option( 'middle_sticky_height', $state ) ?>;
			<?php if ( (int) us_get_header_option( 'middle_sticky_height', $state ) == 0 ) { ?>
			overflow: hidden;
			<?php } ?>
			}

		.l-subheader.at_bottom {
			line-height: <?= us_get_header_option( 'bottom_height', $state ) ?>;
			height: <?= us_get_header_option( 'bottom_height', $state ) ?>;
			}
		.l-header.sticky .l-subheader.at_bottom {
			line-height: <?= us_get_header_option( 'bottom_sticky_height', $state ) ?>;
			height: <?= us_get_header_option( 'bottom_sticky_height', $state ) ?>;
			<?php if ( (int) us_get_header_option( 'bottom_sticky_height', $state ) === 0 ) { ?>
			overflow: hidden;
			<?php } ?>
			}

		<?php
		// Center the middle cell
		foreach ( array( 'top', 'middle', 'bottom' ) as $area ) {
			if ( us_get_header_option( $area . '_centering', $state ) ) {
				?>
				.l-subheader.at_<?= $area ?> .l-subheader-cell.at_left,
				.l-subheader.at_<?= $area ?> .l-subheader-cell.at_right {
					display: flex;
					flex-basis: 100px;
					}
				<?php
			}
		}

		// Styles are needed for Default and Laptops states only
		if ( in_array( $state, array( 'default', 'laptops' ) ) ) {
			echo $header_hor_pos_styles;
			?>
			.admin-bar .l-header.pos_static.bg_solid ~ .l-main .l-section.full_height:first-of-type {
				min-height: calc( 100vh - var(--header-height) - 32px );
				}
			.admin-bar .l-header.pos_fixed:not(.sticky_auto_hide) ~ .l-main .l-section.full_height:not(:first-of-type) {
				min-height: calc( 100vh - var(--header-sticky-height) - 32px );
				}
			.admin-bar.headerinpos_below .l-header.pos_fixed ~ .l-main .l-section.full_height:nth-of-type(2) {
				min-height: calc(100vh - 32px);
				}
		<?php }

		// VERTICAL HEADER
	} else {
		echo $header_ver_styles;
		?>
		.l-header,
		.l-header .w-cart-notification,
		.w-nav.type_mobile.m_layout_panel .w-nav-list.level_1 {
			width: <?= us_get_header_option( 'width', $state ) ?>;
			}
		<?php
		// Different styles for Default and Laptops states only
		if ( in_array( $state, array( 'default', 'laptops' ) ) ) { ?>
			
			.l-body {
				padding-left: <?= us_get_header_option( 'width', $state ) ?>;
				position: relative;
				}
			.l-body.rtl {
				padding-left: 0;
				padding-right: <?= us_get_header_option( 'width', $state ) ?>;
				}

			<?php if ( us_get_option( 'footer_reveal', 0 ) ) { ?>
			.l-body.footer_reveal .l-footer {
				width: calc(100% - <?= us_get_header_option( 'width', $state ) ?>);
				}
			.l-body.footer_reveal:not(.rtl) .l-canvas.type_boxed ~ .l-footer {
				left: <?= us_get_header_option( 'width', $state ) ?>;
				}
			.l-body.footer_reveal.rtl .l-canvas.type_boxed ~ .l-footer {
				right: <?= us_get_header_option( 'width', $state ) ?>;
				}
			<?php } ?>

			.l-body.rtl .l-header {
				left: auto;
				right: 0;
				}
			.l-body:not(.rtl) .post_navigation.layout_sided .order_first {
				left: calc(<?= us_get_header_option( 'width', $state ) ?> - 14rem);
				}
			.l-body:not(.rtl) .w-toplink.pos_left,
			.no-touch .l-body:not(.rtl) .post_navigation.layout_sided .order_first:hover {
				left: <?= us_get_header_option( 'width', $state ) ?>;
				}
			.l-body.rtl .post_navigation.layout_sided .order_second {
				right: calc(<?= us_get_header_option( 'width', $state ) ?> - 14rem);
				}
			.l-body.rtl .w-toplink.pos_right,
			.no-touch .l-body.rtl .post_navigation.layout_sided .order_second:hover {
				right: <?= us_get_header_option( 'width', $state ) ?>;
				}
			.w-nav.type_desktop [class*="columns"] .w-nav-list.level_2 {
				width: calc(100vw - <?= us_get_header_option( 'width', $state ) ?>);
				max-width: 980px;
				}
			.rtl .w-nav.type_desktop .w-nav-list.level_2 {
				left: auto;
				right: 100%;
				}

		<?php } else { ?>

			/* Slided vertical header for Tablets and Mobiles states */
			.w-header-show,
			body:not(.footer_reveal) .w-header-overlay {
				display: block;
				}
			.l-header {
				bottom: 0;
				overflow-y: auto;
				-webkit-overflow-scrolling: touch;
				box-shadow: none;
				transition: transform 0.3s;
				transform: translate3d(-100%,0,0);
				}
			.header-show .l-header {
				transform: translate3d(0,0,0);
				}
			.w-search.layout_simple,
			.w-search.layout_modern.active {
				width: calc(<?= us_get_header_option( 'width', $state ) ?> - 40px);
				}

		<?php }
		if ( us_get_header_option( 'elm_align', $state ) == 'left' ) { ?>
			.l-subheader-cell {
				text-align: left;
				align-items: flex-start;
				}
		<?php }
		if ( us_get_header_option( 'elm_align', $state ) == 'right' ) { ?>
			.l-subheader-cell {
				text-align: right;
				align-items: flex-end;
				}
		<?php }
		if ( us_get_header_option( 'elm_valign', $state ) == 'middle' ) { ?>
			.l-subheader.at_middle {
				display: flex;
				align-items: center;
				}
		<?php }
		if ( us_get_header_option( 'elm_valign', $state ) == 'bottom' ) { ?>
			.l-subheader.at_middle {
				display: flex;
				align-items: flex-end;
				}
		<?php }
	}
	?>
}

<?php endforeach; ?>



/* Image */

<?php foreach ( us_get_header_elms_of_a_type( 'image' ) as $class => $param ) {
	foreach ( us_get_responsive_states( TRUE ) as $state ) { ?>
	<?= $media_queries[ $state ] ?> {
		.<?= $class ?> {
			height: <?= $param[ 'height_' . $state ] ?> !important;
			}
		.l-header.sticky .<?= $class ?> {
			height: <?= ( $state === 'default' ) ? $param[ 'height_sticky' ] : $param[ 'height_sticky_' . $state ] ?> !important;
			}
	}
	<?php
	}
} ?>



/* Menu */

<?php foreach ( us_get_header_elms_of_a_type( 'menu' ) as $class => $param ): ?>
.header_hor .<?= $class ?>.type_desktop .menu-item.level_1 > a:not(.w-btn) {
	padding-left: <?= $param['indents'] ?>;
	padding-right: <?= $param['indents'] ?>;
	}
.header_hor .<?= $class ?>.type_desktop .menu-item.level_1 > a.w-btn {
	margin-left: <?= $param['indents'] ?>;
	margin-right: <?= $param['indents'] ?>;
	}
.header_ver .<?= $class ?>.type_desktop .menu-item.level_1 > a:not(.w-btn) {
	padding-top: <?= $param['indents'] ?>;
	padding-bottom: <?= $param['indents'] ?>;
	}
.header_ver .<?= $class ?>.type_desktop .menu-item.level_1 > a.w-btn {
	margin-top: <?= $param['indents'] ?>;
	margin-bottom: <?= $param['indents'] ?>;
	}
<?php if ( $param['dropdown_arrow'] ): ?>
.<?= $class ?>.type_desktop .menu-item-has-children.level_1 > a > .w-nav-arrow {
	display: inline-block;
	}
<?php endif; ?>
.<?= $class ?>.type_desktop .menu-item:not(.level_1) {
	font-size: <?= $param['dropdown_font_size'] ?>;
	}
<?php if ( $param['dropdown_width'] ): ?>
.<?= $class ?>.type_desktop {
	position: relative;
	}
<?php endif; ?>
.<?= $class ?>.type_mobile .w-nav-anchor.level_1,
.<?= $class ?>.type_mobile .w-nav-anchor.level_1 + .w-nav-arrow {
	font-size: <?= $param['mobile_font_size'] ?>;
	}
.<?= $class ?>.type_mobile .w-nav-anchor:not(.level_1),
.<?= $class ?>.type_mobile .w-nav-anchor:not(.level_1) + .w-nav-arrow {
	font-size: <?= $param['mobile_dropdown_font_size'] ?>;
	}
<?= $media_queries['default'] ?> {
	.<?= $class ?> .w-nav-icon {
		font-size: <?= $param['mobile_icon_size'] ?>;
		}
}
<?= $media_queries['laptops'] ?> {
	.<?= $class ?> .w-nav-icon {
		font-size: <?= $param['mobile_icon_size_laptops'] ?>;
		}
}
<?= $media_queries['tablets'] ?> {
	.<?= $class ?> .w-nav-icon {
		font-size: <?= $param['mobile_icon_size_tablets'] ?>;
		}
}
<?= $media_queries['mobiles'] ?> {
	.<?= $class ?> .w-nav-icon {
		font-size: <?= $param['mobile_icon_size_mobiles'] ?>;
		}
}
.<?= $class ?> .w-nav-icon > div {
	border-width: <?= $param['mobile_icon_thickness'] ?>;
	}

/* Show mobile menu instead of desktop */
@media screen and (max-width: <?= ( (int) $param['mobile_width'] - 1 ) ?>px) {
	.w-nav.<?= $class ?> > .w-nav-list.level_1 {
		display: none;
		}
	.<?= $class ?> .w-nav-control {
		display: block;
		}
}

/* MENU COLORS */

/* Menu Item on hover */
.<?= $class ?> .menu-item.level_1 > a:not(.w-btn):focus,
.no-touch .<?= $class ?> .menu-item.level_1.opened > a:not(.w-btn),
.no-touch .<?= $class ?> .menu-item.level_1:hover > a:not(.w-btn) {
	background: <?= us_get_color( $param['color_hover_bg'], /* Gradient */ TRUE ) ?>;
	color: <?= us_get_color( $param['color_hover_text'] ) ?>;
	}

/* Active Menu Item */
.<?= $class ?> .menu-item.level_1.current-menu-item > a:not(.w-btn),
.<?= $class ?> .menu-item.level_1.current-menu-ancestor > a:not(.w-btn),
.<?= $class ?> .menu-item.level_1.current-page-ancestor > a:not(.w-btn) {
	background: <?= us_get_color( $param['color_active_bg'], /* Gradient */ TRUE ) ?>;
	color: <?= us_get_color( $param['color_active_text'] ) ?>;
	}

/* Active Menu Item in transparent header */
.l-header.bg_transparent:not(.sticky) .<?= $class ?>.type_desktop .menu-item.level_1.current-menu-item > a:not(.w-btn),
.l-header.bg_transparent:not(.sticky) .<?= $class ?>.type_desktop .menu-item.level_1.current-menu-ancestor > a:not(.w-btn),
.l-header.bg_transparent:not(.sticky) .<?= $class ?>.type_desktop .menu-item.level_1.current-page-ancestor > a:not(.w-btn) {
	background: <?= us_get_color( $param['color_transparent_active_bg'], /* Gradient */ TRUE ) ?>;
	color: <?= us_get_color( $param['color_transparent_active_text'] ) ?>;
	}

/* Dropdowns */
.<?= $class ?> .w-nav-list:not(.level_1) {
	background: <?= us_get_color( $param['color_drop_bg'], /* Gradient */ TRUE ) ?>;
	color: <?= us_get_color( $param['color_drop_text'] ) ?>;
	}

/* Dropdown Item on hover */
.no-touch .<?= $class ?> .menu-item:not(.level_1) > a:focus,
.no-touch .<?= $class ?> .menu-item:not(.level_1):hover > a {
	background: <?= us_get_color( $param['color_drop_hover_bg'], /* Gradient */ TRUE ) ?>;
	color: <?= us_get_color( $param['color_drop_hover_text'] ) ?>;
	}

/* Dropdown Active Item */
.<?= $class ?> .menu-item:not(.level_1).current-menu-item > a,
.<?= $class ?> .menu-item:not(.level_1).current-menu-ancestor > a,
.<?= $class ?> .menu-item:not(.level_1).current-page-ancestor > a {
	background: <?= us_get_color( $param['color_drop_active_bg'], /* Gradient */ TRUE ) ?>;
	color: <?= us_get_color( $param['color_drop_active_text'] ) ?>;
	}

<?php endforeach; ?>



/* Simple Menu */

<?php foreach ( us_get_header_elms_of_a_type( 'additional_menu' ) as $class => $param ): ?>
	.header_hor .<?= $class ?> .menu {
		margin: 0 -<?= $param['main_gap'] ?>;
		}
	.header_hor .<?= $class ?>.spread .menu {
		width: calc(100% + <?= $param['main_gap'] ?> + <?= $param['main_gap'] ?>);
		}
	.header_hor .<?= $class ?> .menu-item {
		padding: 0 <?= $param['main_gap'] ?>;
		}
	.header_ver .<?= $class ?> .menu-item {
		padding: <?= $param['main_gap'] ?> 0;
		}
<?php endforeach; ?>



/* Search */

<?php foreach ( us_get_header_elms_of_a_type( 'search' ) as $class => $param ):

if (
	in_array( $param['layout'], array( 'simple', 'modern' ) )
	AND ( ! empty( $param['field_bg_color'] ) OR ! empty( $param['field_text_color'] ) )
) {
	echo '.' . $class . '.w-search input,';
	echo '.' . $class . '.w-search button {';
	echo sprintf( 'background:%s;', us_get_color( $param['field_bg_color'], TRUE ) );
	echo sprintf( 'color:%s;', us_get_color( $param['field_text_color'] ) );
	echo '}';
}
?>

.<?= $class ?> .w-search-form {
	background: <?php echo ! empty( $param['field_bg_color'] )
		? us_get_color( $param['field_bg_color'], /* Gradient */ TRUE )
		: 'var(--color-content-bg)' ?>;
	color: <?php echo ! empty( $param['field_text_color'] )
		? us_get_color( $param['field_text_color'] )
		: 'var(--color-content-text)' ?>;
}

<?= $media_queries['default'] ?> {
	.<?= $class ?>.layout_simple {
		max-width: <?= $param['field_width'] ?>;
		}
	.<?= $class ?>.layout_modern.active {
		width: <?= $param['field_width'] ?>;
		}
	.<?= $class ?> {
		font-size: <?= $param['icon_size'] ?>;
		}
}
<?= $media_queries['laptops'] ?> {
	.<?= $class ?>.layout_simple {
		max-width: <?= $param['field_width_laptops'] ?>;
		}
	.<?= $class ?>.layout_modern.active {
		width: <?= $param['field_width_laptops'] ?>;
		}
	.<?= $class ?> {
		font-size: <?= $param['icon_size_laptops'] ?>;
		}
}
<?= $media_queries['tablets'] ?> {
	.<?= $class ?>.layout_simple {
		max-width: <?= $param['field_width_tablets'] ?>;
		}
	.<?= $class ?>.layout_modern.active {
		width: <?= $param['field_width_tablets'] ?>;
		}
	.<?= $class ?> {
		font-size: <?= $param['icon_size_tablets'] ?>;
		}
}
<?= $media_queries['mobiles'] ?> {
	.<?= $class ?> {
		font-size: <?= $param['icon_size_mobiles'] ?>;
		}
}
<?php endforeach; ?>



/* Socials */

<?php foreach ( us_get_header_elms_of_a_type( 'socials' ) as $class => $param ): ?>
<?php if ( ! empty( $param['gap'] ) ): ?>
.<?= $class ?> .w-socials-list {
	margin: -<?= $param['gap'] ?>;
	}
.<?= $class ?> .w-socials-item {
	padding: <?= $param['gap'] ?>;
	}
<?php endif; ?>
<?php endforeach; ?>



/* Cart */

<?php foreach ( us_get_header_elms_of_a_type( 'cart' ) as $class => $param ): ?>
<?= $media_queries['default'] ?> {
	.<?= $class ?> .w-cart-link {
		font-size: <?= $param['size'] ?>;
		}
}
<?= $media_queries['laptops'] ?> {
	.<?= $class ?> .w-cart-link {
		font-size: <?= $param['size_laptops'] ?>;
		}
}
<?= $media_queries['tablets'] ?> {
	.<?= $class ?> .w-cart-link {
		font-size: <?= $param['size_tablets'] ?>;
		}
}
<?= $media_queries['mobiles'] ?> {
	.<?= $class ?> .w-cart-link {
		font-size: <?= $param['size_mobiles'] ?>;
		}
}
<?php endforeach; ?>



/* Design Options */

<?= us_get_header_design_options_css() ?>
