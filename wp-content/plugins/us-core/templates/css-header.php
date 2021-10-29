<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Generates and outputs header generated stylesheets
 *
 * @action Before the template: us_before_template:templates/css-header
 * @action After the template: us_after_template:templates/css-header
 */
global $us_template_directory;

/* Set breakpoint values */
$tablets_breakpoint = us_get_header_option( 'breakpoint', 'tablets' ) ? us_get_header_option( 'breakpoint', 'tablets' ) : '901';
$mobiles_breakpoint = us_get_header_option( 'breakpoint', 'mobiles' ) ? us_get_header_option( 'breakpoint', 'mobiles' ) : '601';
$desktop_query = '(min-width:' . intval( $tablets_breakpoint ) . 'px)';
$tablets_query = '(min-width:' . intval( $mobiles_breakpoint ) . 'px) and (max-width:' . ( intval( $tablets_breakpoint ) - 1 ) . 'px)';
$mobiles_query = '(max-width:' . ( intval( $mobiles_breakpoint ) - 1 ) . 'px)';

/* Header styles as variables */
$header_hor_styles = file_get_contents( $us_template_directory . '/common/css/base/header-hor.css' );
$header_ver_styles = file_get_contents( $us_template_directory . '/common/css/base/header-ver.css' );

/* Calculate header heights for all 3 states */
foreach ( array( 'default', 'tablets', 'mobiles' ) as $state ) {

	// Initial height
	${'header_height_' . $state} = intval( us_get_header_option( 'middle_height', $state ) );
	if ( us_get_header_option( 'top_show', $state ) ) {
		${'header_height_' . $state} += intval( us_get_header_option( 'top_height', $state ) );
	}
	if ( us_get_header_option( 'bottom_show', $state ) ) {
		${'header_height_' . $state} += intval( us_get_header_option( 'bottom_height', $state ) );
	}

	// Sticky height
	${'header_sticky_height_' . $state} = intval( us_get_header_option( 'middle_sticky_height', $state ) );
	if ( us_get_header_option( 'top_show', $state ) ) {
		${'header_sticky_height_' . $state} += intval( us_get_header_option( 'top_sticky_height', $state ) );
	}
	if ( us_get_header_option( 'bottom_show', $state ) ) {
		${'header_sticky_height_' . $state} += intval( us_get_header_option( 'bottom_sticky_height', $state ) );
	}
}
?>

/* =============================================== */
/* ================ Header Colors ================ */
/* =============================================== */

<?php foreach ( array( 'top', 'middle', 'bottom' ) as $area ):

	// Do not output extra CSS, if top or bottom areas are disabled in all states
	$show_state = FALSE;
	foreach ( array( 'default', 'tablets', 'mobiles' ) as $state ) {
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
/* ================ Default state ================ */
/* =============================================== */

@media <?= $desktop_query ?> {

	.hidden_for_default { display: none !important; }

<?php if ( ! us_get_header_option( 'top_show' ) ) { ?>
	.l-subheader.at_top { display: none; }
<?php }
if ( ! us_get_header_option( 'bottom_show' ) ) { ?>
	.l-subheader.at_bottom { display: none; }
<?php }
if ( $bg_image = us_get_header_option( 'bg_img' ) ) {
	$img_arr = explode( '|', $bg_image );
	$bg_image_url = wp_get_attachment_image_url( $img_arr[0], 'full' );

	?>
	.l-subheader.at_middle {
		background-image: url(<?= esc_url( $bg_image_url ) ?>);
		background-attachment: <?= ( us_get_header_option( 'bg_img_attachment' ) ) ? 'scroll' : 'fixed'; ?>;
		background-position: <?= us_get_header_option( 'bg_img_position' ) ?>;
		background-repeat: <?= us_get_header_option( 'bg_img_repeat' ) ?>;
		background-size: <?= us_get_header_option( 'bg_img_size' ) ?>;
	}
<?php }

// Horizontal header
if ( us_get_header_option( 'orientation' ) == 'hor' ) {
	echo $header_hor_styles;
?>
	.l-header:before {
		content: '<?= $header_height_default ?>';
	}
	.l-header.sticky:before {
		content: '<?= $header_sticky_height_default  ?>';
	}
	.l-subheader.at_top {
		line-height: <?= us_get_header_option( 'top_height' ) ?>;
		height: <?= us_get_header_option( 'top_height' ) ?>;
	}
	.l-header.sticky .l-subheader.at_top {
		line-height: <?= us_get_header_option( 'top_sticky_height' ) ?>;
		height: <?= us_get_header_option( 'top_sticky_height' ) ?>;
	<?php if ( (int) us_get_header_option( 'top_sticky_height' ) == 0 ): ?>
		overflow: hidden;
	<?php endif; ?>
	}

	.l-subheader.at_middle {
		line-height: <?= us_get_header_option( 'middle_height' ) ?>;
		height: <?= us_get_header_option( 'middle_height' ) ?>;
	}
	.l-header.sticky .l-subheader.at_middle {
		line-height: <?= us_get_header_option( 'middle_sticky_height' ) ?>;
		height: <?= us_get_header_option( 'middle_sticky_height' ) ?>;
	<?php if ( (int) us_get_header_option( 'middle_sticky_height' ) == 0 ): ?>
		overflow: hidden;
	<?php endif; ?>
	}

	.l-subheader.at_bottom {
		line-height: <?= us_get_header_option( 'bottom_height' ) ?>;
		height: <?= us_get_header_option( 'bottom_height' ) ?>;
	}
	.l-header.sticky .l-subheader.at_bottom {
		line-height: <?= us_get_header_option( 'bottom_sticky_height' ) ?>;
		height: <?= us_get_header_option( 'bottom_sticky_height' ) ?>;
	<?php if ( (int) us_get_header_option( 'bottom_sticky_height' ) == 0 ): ?>
		overflow: hidden;
	<?php endif; ?>
	}

	/* Center the middle cell */
	.l-subheader.with_centering .l-subheader-cell.at_left,
	.l-subheader.with_centering .l-subheader-cell.at_right {
		flex-basis: 100px;
		}

	/* Calculate top padding for content overlapped by sticky header */
	.l-header.pos_fixed ~ .l-main > .l-section:first-of-type > .l-section-h,
	.headerinpos_below .l-header.pos_fixed ~ .l-main > .l-section:nth-of-type(2) > .l-section-h,
	.l-header.pos_static.bg_transparent ~ .l-main > .l-section:first-of-type > .l-section-h {
		padding-top: <?= $header_height_default ?>px;
	}
	.headerinpos_bottom .l-header.pos_fixed ~ .l-main > .l-section:first-of-type > .l-section-h {
		padding-bottom: <?= $header_height_default ?>px;
	}

	/* Fix vertical centering of first section when header is transparent */
	.l-header.bg_transparent ~ .l-main .l-section.valign_center:first-of-type > .l-section-h {
		top: -<?= $header_height_default/2 ?>px;
	}
	.headerinpos_bottom .l-header.pos_fixed.bg_transparent ~ .l-main .l-section.valign_center:first-of-type > .l-section-h {
		top: <?= $header_height_default/2 ?>px;
	}

	/* Calculate max height for menu dropdowns */
	.menu-item-object-us_page_block {
		max-height: calc(100vh - <?= $header_height_default ?>px);
	}

	/* Position of "Sticky" rows */
	.l-header.pos_fixed:not(.down) ~ .l-main .l-section.type_sticky {
		top: <?= $header_sticky_height_default ?>px;
	}
	.admin-bar .l-header.pos_fixed:not(.down) ~ .l-main .l-section.type_sticky {
		top: <?= $header_sticky_height_default + 32 ?>px;
	}
	.l-header.pos_fixed.sticky:not(.down) ~ .l-main .l-section.type_sticky:first-of-type > .l-section-h {
		padding-top: <?= $header_sticky_height_default ?>px;
	}

	/* Position of "Sticky" columns */
	.l-header.pos_fixed ~ .l-main .vc_column-inner.type_sticky > .wpb_wrapper {
		top: calc(<?= $header_sticky_height_default ?>px + 4rem);
		}

	/* Position of WooCommerce Cart & Checkout blocks */
	.l-header.pos_fixed ~ .l-main .woocommerce .cart-collaterals,
	.l-header.pos_fixed ~ .l-main .woocommerce-checkout #order_review {
		top: <?= $header_sticky_height_default ?>px;
		}

	/* Calculate height of "Full Screen" rows */
	.l-header.pos_static.bg_solid ~ .l-main .l-section.height_full:first-of-type {
		min-height: calc(100vh - <?= $header_height_default ?>px);
	}
	.admin-bar .l-header.pos_static.bg_solid ~ .l-main .l-section.height_full:first-of-type {
		min-height: calc(100vh - <?= $header_height_default + /* admin_bar */32 ?>px);
	}
	.l-header.pos_fixed:not(.sticky_auto_hide) ~ .l-main .l-section.height_full:not(:first-of-type) {
		min-height: calc(100vh - <?= $header_sticky_height_default ?>px);
	}
	.admin-bar .l-header.pos_fixed:not(.sticky_auto_hide) ~ .l-main .l-section.height_full:not(:first-of-type) {
		min-height: calc(100vh - <?= $header_sticky_height_default + /* admin_bar */32 ?>px);
	}

	/* Initial header position BOTTOM & BELOW */
	.headerinpos_below .l-header.pos_fixed:not(.sticky) {
		position: absolute;
		top: 100%;
	}
	.headerinpos_bottom .l-header.pos_fixed:not(.sticky) {
		position: absolute;
		top: 100vh;
	}
	.headerinpos_below .l-header.pos_fixed ~ .l-main > .l-section:first-of-type > .l-section-h,
	.headerinpos_bottom .l-header.pos_fixed ~ .l-main > .l-section:first-of-type > .l-section-h {
		padding-top: 0 !important;
	}
	.headerinpos_below .l-header.pos_fixed ~ .l-main .l-section.height_full:nth-of-type(2) {
		min-height: 100vh;
	}
	.admin-bar.headerinpos_below .l-header.pos_fixed ~ .l-main .l-section.height_full:nth-of-type(2) {
		min-height: calc(100vh - 32px); /* WP admin bar height */
	}
	.headerinpos_bottom .l-header.pos_fixed:not(.sticky) .w-cart-dropdown,
	.headerinpos_bottom .l-header.pos_fixed:not(.sticky) .w-nav.type_desktop .w-nav-list.level_2 {
		bottom: 100%;
		transform-origin: 0 100%;
	}
	.headerinpos_bottom .l-header.pos_fixed:not(.sticky) .w-nav.type_mobile.m_layout_dropdown .w-nav-list.level_1 {
		top: auto;
		bottom: 100%;
		box-shadow: 0 -3px 3px rgba(0,0,0,0.1);
	}
	.headerinpos_bottom .l-header.pos_fixed:not(.sticky) .w-nav.type_desktop .w-nav-list.level_3,
	.headerinpos_bottom .l-header.pos_fixed:not(.sticky) .w-nav.type_desktop .w-nav-list.level_4 {
		top: auto;
		bottom: 0;
		transform-origin: 0 100%;
	}
<?php } else {

	// Vertical header
	echo $header_ver_styles;
?>
	html:not(.no-touch) .l-header.scrollable {
		position: absolute;
		height: 100%;
	}
	.l-body {
		padding-left: <?= us_get_header_option( 'width' ) ?>;
		position: relative;
	}
	.l-body.rtl {
		padding-left: 0;
		padding-right: <?= us_get_header_option( 'width' ) ?>;
	}
	<?php if ( us_get_option( 'footer_reveal', 0 ) ) { ?>
	.l-body.footer_reveal .l-footer {
		width: calc(100% - <?= us_get_header_option( 'width' ) ?>);
	}
	.l-body.footer_reveal:not(.rtl) .l-canvas.type_boxed ~ .l-footer {
		left: <?= us_get_header_option( 'width' ) ?>;
	}
	.l-body.footer_reveal.rtl .l-canvas.type_boxed ~ .l-footer {
		right: <?= us_get_header_option( 'width' ) ?>;
	}
	<?php } ?>
	.l-header,
	.l-header .w-cart-notification,
	.w-nav.type_mobile.m_layout_panel .w-nav-list.level_1 {
		width: <?= us_get_header_option( 'width' ) ?>;
	}
	.l-body.rtl .l-header {
		left: auto;
		right: 0;
	}
	.l-body:not(.rtl) .post_navigation.layout_sided .order_first {
		left: calc(<?= us_get_header_option( 'width' ) ?> - 14rem);
	}
	.l-body:not(.rtl) .w-toplink.pos_left,
	.no-touch .l-body:not(.rtl) .post_navigation.layout_sided .order_first:hover {
		left: <?= us_get_header_option( 'width' ) ?>;
	}
	.l-body.rtl .post_navigation.layout_sided .order_second {
		right: calc(<?= us_get_header_option( 'width' ) ?> - 14rem);
	}
	.l-body.rtl .w-toplink.pos_right,
	.no-touch .l-body.rtl .post_navigation.layout_sided .order_second:hover {
		right: <?= us_get_header_option( 'width' ) ?>;
	}
	.w-nav.type_desktop [class*="columns"] .w-nav-list.level_2 {
		width: calc(100vw - <?= us_get_header_option( 'width' ) ?>);
		max-width: 980px;
	}
	.rtl .w-nav.type_desktop .w-nav-list.level_2 {
		left: auto;
		right: 100%;
		}
<?php
if ( us_get_header_option( 'elm_align' ) == 'left' ) { ?>
	.l-subheader-cell {
		text-align: left;
		align-items: flex-start;
	}
<?php }
if ( us_get_header_option( 'elm_align' ) == 'right' ) { ?>
	.l-subheader-cell {
		text-align: right;
		align-items: flex-end;
	}
<?php }
if ( us_get_header_option( 'elm_valign' ) == 'middle' ) { ?>
	.l-subheader.at_middle {
		display: flex;
		align-items: center;
	}
<?php }
if ( us_get_header_option( 'elm_valign' ) == 'bottom' ) { ?>
	.l-subheader.at_middle {
		display: flex;
		align-items: flex-end;
	}
<?php }
}
?>

/* Fix Dropdown position on Default state */
.headerinpos_bottom .l-header.pos_fixed:not(.sticky) .w-dropdown-list {
	top: auto;
	bottom: -0.4em;
	padding-top: 0.4em;
	padding-bottom: 2.4em;
	}
}



/* =============================================== */
/* ================ Tablets state ================ */
/* =============================================== */

@media <?= $tablets_query ?> {

	.hidden_for_tablets { display: none !important; }

<?php if ( ! us_get_header_option( 'top_show', 'tablets' ) ) { ?>
	.l-subheader.at_top { display: none; }
<?php }
if ( ! us_get_header_option( 'bottom_show', 'tablets' ) ) { ?>
	.l-subheader.at_bottom { display: none; }
<?php }
if ( $bg_image = us_get_header_option( 'bg_img', 'tablets' ) ) {
	$img_arr = explode( '|', $bg_image );
	$bg_image_url = wp_get_attachment_image_url( $img_arr[0], 'full' );

	?>
	.l-subheader.at_middle {
		background-image: url(<?= esc_url( $bg_image_url ) ?>);
		background-attachment: <?= ( us_get_header_option( 'bg_img_attachment', 'tablets' ) ) ? 'scroll' : 'fixed'; ?>;
		background-position: <?= us_get_header_option( 'bg_img_position', 'tablets' ) ?>;
		background-repeat: <?= us_get_header_option( 'bg_img_repeat', 'tablets' ) ?>;
		background-size: <?= us_get_header_option( 'bg_img_size', 'tablets' ) ?>;
	}
<?php }

// Horizontal header on Tablets
if ( us_get_header_option( 'orientation', 'tablets' ) == 'hor' ) {
	echo $header_hor_styles;
?>
	.l-subheader.at_top {
		line-height: <?= us_get_header_option( 'top_height', 'tablets' ) ?>;
		height: <?= us_get_header_option( 'top_height', 'tablets' ) ?>;
	}
	.l-header.sticky .l-subheader.at_top {
		line-height: <?= us_get_header_option( 'top_sticky_height', 'tablets' ) ?>;
		height: <?= us_get_header_option( 'top_sticky_height', 'tablets' ) ?>;
	<?php if ( (int) us_get_header_option( 'top_sticky_height', 'tablets' ) == 0 ): ?>
		overflow: hidden;
	<?php endif; ?>
	}
	.l-header:before {
		content: '<?= $header_height_tablets ?>';
	}
	.l-header.sticky:before {
		content: '<?= $header_sticky_height_tablets  ?>';
	}

	.l-subheader.at_middle {
		line-height: <?= us_get_header_option( 'middle_height', 'tablets' ) ?>;
		height: <?= us_get_header_option( 'middle_height', 'tablets' ) ?>;
	}

	.l-header.sticky .l-subheader.at_middle {
		line-height: <?= us_get_header_option( 'middle_sticky_height', 'tablets' ) ?>;
		height: <?= us_get_header_option( 'middle_sticky_height', 'tablets' ) ?>;
	<?php if ( (int) us_get_header_option( 'middle_sticky_height', 'tablets' ) == 0 ): ?>
		overflow: hidden;
	<?php endif; ?>
	}

	.l-subheader.at_bottom {
		line-height: <?= us_get_header_option( 'bottom_height', 'tablets' ) ?>;
		height: <?= us_get_header_option( 'bottom_height', 'tablets' ) ?>;
	}
	.l-header.sticky .l-subheader.at_bottom {
		line-height: <?= us_get_header_option( 'bottom_sticky_height', 'tablets' ) ?>;
		height: <?= us_get_header_option( 'bottom_sticky_height', 'tablets' ) ?>;
	<?php if ( (int) us_get_header_option( 'bottom_sticky_height', 'tablets' ) == 0 ): ?>
		overflow: hidden;
	<?php endif; ?>
	}

	/* Center the middle cell for tablets */
	.l-subheader.with_centering_tablets .l-subheader-cell.at_left,
	.l-subheader.with_centering_tablets .l-subheader-cell.at_right {
		flex-basis: 100px;
	}

	/* Calculate top padding for content overlapped by Sticky header */
	.l-header.pos_fixed ~ .l-main > .l-section:first-of-type > .l-section-h,
	.l-header.pos_static.bg_transparent ~ .l-main > .l-section:first-of-type > .l-section-h {
		padding-top: <?= $header_height_tablets ?>px;
	}

	/* Fix vertical centering of first section when header is transparent */
	.l-header.bg_transparent ~ .l-main .l-section.valign_center:first-of-type > .l-section-h {
		top: -<?= $header_height_tablets/2 ?>px;
	}

	/* Calculate position of "Sticky" rows */
	.l-header.pos_fixed ~ .l-main .l-section.type_sticky {
		top: <?= $header_sticky_height_tablets ?>px;
	}
	.admin-bar .l-header.pos_fixed ~ .l-main .l-section.type_sticky {
		top: <?= $header_sticky_height_tablets + 32 ?>px;
	}
	.l-header.pos_fixed.sticky:not(.down) ~ .l-main .l-section.type_sticky:first-of-type > .l-section-h {
		padding-top: <?= $header_sticky_height_tablets ?>px;
	}

	/* Calculate height of "Full Screen" rows */
	.l-header.pos_static.bg_solid ~ .l-main .l-section.height_full:first-of-type {
		min-height: calc(100vh - <?= $header_height_tablets ?>px);
	}
	.l-header.pos_fixed:not(.sticky_auto_hide) ~ .l-main .l-section.height_full:not(:first-of-type) {
		min-height: calc(100vh - <?= $header_sticky_height_tablets ?>px);
	}

<?php } else {

	// Vertical header on Mobiles
	echo $header_ver_styles;
?>
	.l-header,
	.l-header .w-cart-notification,
	.w-nav.type_mobile.m_layout_panel .w-nav-list.level_1 {
		width: <?= us_get_header_option( 'width', 'tablets' ) ?>;
	}
	.w-search.layout_simple,
	.w-search.layout_modern.active {
		width: calc(<?= us_get_header_option( 'width', 'tablets' ) ?> - 40px);
	}

	/* Slided vertical header */
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
<?php if ( us_get_header_option( 'elm_align', 'tablets' ) == 'left' ) { ?>
	.l-subheader-cell {
		text-align: left;
		align-items: flex-start;
	}
<?php }
if ( us_get_header_option( 'elm_align', 'tablets' ) == 'right' ) { ?>
	.l-subheader-cell {
		text-align: right;
		align-items: flex-end;
	}
<?php }
}
?>
}



/* =============================================== */
/* ================ Mobiles state ================ */
/* =============================================== */

@media <?= $mobiles_query ?> {

	.hidden_for_mobiles { display: none !important; }

<?php if ( ! us_get_header_option( 'top_show', 'mobiles' ) ) { ?>
	.l-subheader.at_top { display: none; }
<?php }
if ( ! us_get_header_option( 'bottom_show', 'mobiles' ) ) { ?>
	.l-subheader.at_bottom { display: none; }
<?php }
if ( $bg_image = us_get_header_option( 'bg_img', 'mobiles' ) ) {
	$img_arr = explode( '|', $bg_image );
	$bg_image_url = wp_get_attachment_image_url( $img_arr[0], 'full' );

	?>
	.l-subheader.at_middle {
		background-image: url(<?= esc_url( $bg_image_url ) ?>);
		background-attachment: <?= ( us_get_header_option( 'bg_img_attachment', 'mobiles' ) ) ? 'scroll' : 'fixed'; ?>;
		background-position: <?= us_get_header_option( 'bg_img_position', 'mobiles' ) ?>;
		background-repeat: <?= us_get_header_option( 'bg_img_repeat', 'mobiles' ) ?>;
		background-size: <?= us_get_header_option( 'bg_img_size', 'mobiles' ) ?>;
	}
<?php }

// Horizontal header on Mobiles
if ( us_get_header_option( 'orientation', 'mobiles' ) == 'hor' ) {
	echo $header_hor_styles;
?>
	.l-header:before {
		content: '<?= $header_height_mobiles ?>';
	}
	.l-header.sticky:before {
		content: '<?= $header_sticky_height_mobiles  ?>';
	}
	.l-subheader.at_top {
		line-height: <?= us_get_header_option( 'top_height', 'mobiles' ) ?>;
		height: <?= us_get_header_option( 'top_height', 'mobiles' ) ?>;
	}
	.l-header.sticky .l-subheader.at_top {
		line-height: <?= us_get_header_option( 'top_sticky_height', 'mobiles' ) ?>;
		height: <?= us_get_header_option( 'top_sticky_height', 'mobiles' ) ?>;
	<?php if ( (int) us_get_header_option( 'top_sticky_height', 'mobiles' ) == 0 ): ?>
		overflow: hidden;
	<?php endif; ?>
	}

	.l-subheader.at_middle {
		line-height: <?= us_get_header_option( 'middle_height', 'mobiles' ) ?>;
		height: <?= us_get_header_option( 'middle_height', 'mobiles' ) ?>;
	}

	.l-header.sticky .l-subheader.at_middle {
		line-height: <?= us_get_header_option( 'middle_sticky_height', 'mobiles' ) ?>;
		height: <?= us_get_header_option( 'middle_sticky_height', 'mobiles' ) ?>;
	<?php if ( (int) us_get_header_option( 'middle_sticky_height', 'mobiles' ) == 0 ): ?>
		overflow: hidden;
	<?php endif; ?>
	}

	.l-subheader.at_bottom {
		line-height: <?= us_get_header_option( 'bottom_height', 'mobiles' ) ?>;
		height: <?= us_get_header_option( 'bottom_height', 'mobiles' ) ?>;
	}
	.l-header.sticky .l-subheader.at_bottom {
		line-height: <?= us_get_header_option( 'bottom_sticky_height', 'mobiles' ) ?>;
		height: <?= us_get_header_option( 'bottom_sticky_height', 'mobiles' ) ?>;
	<?php if ( (int) us_get_header_option( 'bottom_sticky_height', 'mobiles' ) == 0 ): ?>
		overflow: hidden;
	<?php endif; ?>
	}

	/* Center the middle cell for mobiles */
	.l-subheader.with_centering_mobiles .l-subheader-cell.at_left,
	.l-subheader.with_centering_mobiles .l-subheader-cell.at_right {
		flex-basis: 100px;
	}

	/* Calculate top padding for content overlapped by Sticky header */
	.l-header.pos_fixed ~ .l-main > .l-section:first-of-type > .l-section-h,
	.l-header.pos_static.bg_transparent ~ .l-main > .l-section:first-of-type > .l-section-h {
		padding-top: <?= $header_height_mobiles ?>px;
	}

	/* Fix vertical centering of first section when header is transparent */
	.l-header.bg_transparent ~ .l-main .l-section.valign_center:first-of-type > .l-section-h {
		top: -<?= $header_height_mobiles/2 ?>px;
	}

	/* Calculate position of "Sticky" rows */
	.l-header.pos_fixed ~ .l-main .l-section.type_sticky {
		top: <?= $header_sticky_height_mobiles ?>px;
	}
	.l-header.pos_fixed.sticky:not(.down) ~ .l-main .l-section.type_sticky:first-of-type > .l-section-h {
		padding-top: <?= $header_sticky_height_mobiles ?>px;
	}

	/* Calculate height of "Full Screen" rows */
	.l-header.pos_static.bg_solid ~ .l-main .l-section.height_full:first-of-type {
		min-height: calc(100vh - <?= $header_height_mobiles ?>px);
	}
	.l-header.pos_fixed:not(.sticky_auto_hide) ~ .l-main .l-section.height_full:not(:first-of-type) {
		min-height: calc(100vh - <?= $header_sticky_height_mobiles ?>px);
	}

<?php } else {

	// Vertical header on Mobiles
	echo $header_ver_styles;
?>
	.l-header,
	.l-header .w-cart-notification,
	.w-nav.type_mobile.m_layout_panel .w-nav-list.level_1 {
		width: <?= us_get_header_option( 'width', 'mobiles' ) ?>;
	}
	.w-search.layout_simple,
	.w-search.layout_modern.active {
		width: calc(<?= us_get_header_option( 'width', 'mobiles' ) ?> - 40px);
	}

	/* Slided vertical header */
	.w-header-show,
	.w-header-overlay {
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
<?php if ( us_get_header_option( 'elm_align', 'mobiles' ) == 'left' ) { ?>
	.l-subheader-cell {
		text-align: left;
		align-items: flex-start;
	}
<?php }
if ( us_get_header_option( 'elm_align', 'mobiles' ) == 'right' ) { ?>
	.l-subheader-cell {
		text-align: right;
		align-items: flex-end;
	}
<?php }
}
?>
}



/* Image */

<?php foreach ( us_get_header_elms_of_a_type( 'image' ) as $class => $param ): ?>
@media <?= $desktop_query ?> {
	.<?= $class ?> { height: <?= $param['height_default'] ?> !important; }
	.l-header.sticky .<?= $class ?> { height: <?= $param['height_sticky'] ?> !important; }
}
@media <?= $tablets_query ?> {
	.<?= $class ?> { height: <?= $param['height_tablets'] ?> !important; }
	.l-header.sticky .<?= $class ?> { height: <?= $param['height_sticky_tablets'] ?> !important; }
}
@media <?= $mobiles_query ?> {
	.<?= $class ?> { height: <?= $param['height_mobiles'] ?> !important; }
	.l-header.sticky .<?= $class ?> { height: <?= $param['height_sticky_mobiles'] ?> !important; }
}
<?php endforeach; ?>



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
@media <?= $desktop_query ?> {
	.<?= $class ?> .w-nav-icon {
		font-size: <?= $param['mobile_icon_size'] ?>;
	}
}
@media <?= $tablets_query ?> {
	.<?= $class ?> .w-nav-icon {
		font-size: <?= $param['mobile_icon_size_tablets'] ?>;
	}
}
@media <?= $mobiles_query ?> {
	.<?= $class ?> .w-nav-icon {
		font-size: <?= $param['mobile_icon_size_mobiles'] ?>;
	}
}
.<?= $class ?> .w-nav-icon i {
	border-width: <?= $param['mobile_icon_thickness'] ?>;
}
/* Show mobile menu instead of desktop */
@media screen and (max-width: <?= ( intval( $param['mobile_width'] ) - 1 ) ?>px) {
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

if ( in_array( $param['layout'], array( 'simple', 'modern' ) ) AND ( ! empty( $param['field_bg_color'] ) OR ! empty( $param['field_text_color'] ) ) ) {
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
		: us_get_color( 'color_content_bg', /* Gradient */ TRUE ) ?>;
	color: <?php echo ! empty( $param['field_text_color'] )
		? us_get_color( $param['field_text_color'] )
		: us_get_color( 'color_content_text' ) ?>;
}

@media <?= $desktop_query ?> {
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
@media <?= $tablets_query ?> {
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
@media <?= $mobiles_query ?> {
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
@media <?= $desktop_query ?> {
	.<?= $class ?> .w-cart-link {
		font-size: <?= $param['size'] ?>;
	}
}
@media <?= $tablets_query ?> {
	.<?= $class ?> .w-cart-link {
		font-size: <?= $param['size_tablets'] ?>;
	}
}
@media <?= $mobiles_query ?> {
	.<?= $class ?> .w-cart-link {
		font-size: <?= $param['size_mobiles'] ?>;
	}
}
<?php endforeach; ?>



/* Design Options */

<?= us_get_header_design_options_css() ?>
