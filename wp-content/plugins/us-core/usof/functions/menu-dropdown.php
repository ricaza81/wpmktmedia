<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

add_action( 'admin_init', 'us_menu_dropdown_init' );
function us_menu_dropdown_init() {
	global $pagenow;
	if ( $pagenow == 'nav-menus.php') {
		add_action( 'admin_footer', 'us_menu_dropdown_admin_footer' );
		add_thickbox();
	}
}

function us_menu_dropdown_admin_footer() {
	?>
	<script>
		jQuery(function ($) {
			"use strict";
			var menuId = $('input#menu').val();

			$('#menu-to-edit li.menu-item.menu-item-depth-0').each(function() {
				var $menuItem = $(this),
					itemId = parseInt($menuItem.attr('id').match(/[0-9]+/)[0], 10),
					$nextMenuItem = $menuItem.next();

				if ( $nextMenuItem.length == 0 || $nextMenuItem.is('li.menu-item.menu-item-depth-0') ) {
					return;
				}

				var $button = $('<a href="<?php echo admin_url(); ?>admin-ajax.php?action=usof_ajax_mega_menu&menu_id='+menuId+'&item_id='+itemId+'">')
					.addClass("us-mm-btn thickbox")
					.html('<?php _e( 'Dropdown Settings', 'us' ); ?>');

				$('.item-title', $menuItem).append($button);
			});
		});
	</script>
	<?php
}
