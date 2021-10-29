<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options
 *
 * @filter us_config_theme-options
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

global $usof_options, $help_portal_url;

$usof_enable_portfolio = ! empty( $usof_options['enable_portfolio'] ) ? TRUE : FALSE;
$usof_sidebar_titlebar = ! empty( $usof_options['enable_sidebar_titlebar'] ) ? TRUE : FALSE;

if ( ! empty( $usof_options['portfolio_rename'] ) ) {
	$renamed_portfolio_label = ' (' . wp_strip_all_tags( $usof_options['portfolio_label_name'], TRUE ) . ')';
} else {
	$renamed_portfolio_label = '';
}

global $pagenow;
$posts_titles = array();
if (
	! wp_doing_ajax()
	AND $pagenow == 'admin.php'
	AND isset( $_GET['page'] )
	AND $_GET['page'] == 'us-theme-options'
) {
	$posts_titles = ( array ) us_get_all_posts_titles_for( array(
		'page',
		'us_header',
		'us_page_block',
		'us_content_template',
	) );
}

// Get Pages and order alphabetically
$us_page_list = us_filter_posts_by_language( us_arr_path( $posts_titles, 'page', array() ) );

// Get Headers
$us_headers_list = us_filter_posts_by_language( us_arr_path( $posts_titles, 'us_header', array() ) );

// Get Page Blocks
$us_page_blocks_list = us_filter_posts_by_language( us_arr_path( $posts_titles, 'us_page_block', array() ) );

// Get Content templates
$us_content_templates_list = us_filter_posts_by_language( us_arr_path( $posts_titles, 'us_content_template', array() ) );

// Use Page Blocks as Sidebars, if set in Theme Options
if ( ! empty( $usof_options['enable_page_blocks_for_sidebars'] ) ) {
	$sidebars_list = $us_page_blocks_list;
	$sidebar_hints_for = 'us_page_block';

	// else use regular sidebars
} else {
	$sidebars_list = us_get_sidebars();
	$sidebar_hints_for = NULL;
}
// Descriptions
$misc = us_config( 'elements_misc' );
$misc['headers_description'] .= '<br><img src="' . US_CORE_URI . '/admin/img/l-header.png">';
$misc['content_description'] .= '<br><img src="' . US_CORE_URI . '/admin/img/l-content.png">';
$misc['footers_description'] .= '<br><img src="' . US_CORE_URI . '/admin/img/l-footer.png">';

// Get CSS & JS assets
$usof_assets = $usof_assets_std = array();
$assets_config = us_config( 'assets', array() );
foreach ( $assets_config as $component => $component_atts ) {
	if ( isset( $component_atts['hidden'] ) AND $component_atts['hidden'] ) {
		continue;
	}
	$usof_assets[ $component ] = array(
		'title' => $component_atts['title'],
		'group' => isset( $component_atts['group'] ) ? $component_atts['group'] : NULL,
	);
	if ( isset( $component_atts['apply_if'] ) ) {
		$usof_assets[ $component ]['apply_if'] = $component_atts['apply_if'];
	}
	$usof_assets_std[ $component ] = 1;

	// Count files sizes for admin area only
	if ( is_admin() ) {
		if ( isset( $component_atts['css'] ) ) {
			$usof_assets[ $component ]['css_size'] = file_exists( $us_template_directory . $component_atts['css'] )
				? number_format_i18n( filesize( $us_template_directory . $component_atts['css'] ) / 1024 * 0.8, 1 )
				: NULL;
		}
		if ( isset( $component_atts['js'] ) ) {
			$js_filename = str_replace( '.js', '.min.js', $us_template_directory . $component_atts['js'] );
			$usof_assets[ $component ]['js_size'] = file_exists( $js_filename )
				? number_format_i18n( filesize( $js_filename ) / 1024, 1 )
				: NULL;
		}
	}

}

// Check if "uploads" directory is writable
$upload_dir = wp_get_upload_dir();
$upload_dir_not_writable = wp_is_writable( $upload_dir['basedir'] ) ? FALSE : TRUE;

// Generate 'Pages Layout' options
$pages_layout_config = array();
foreach ( us_get_public_post_types( array( 'page', 'product' ) ) as $type => $title ) {

	// Rename "us_portfolio" suffix to avoid migration from old theme options
	if ( $type == 'us_portfolio' ) {
		$type = 'portfolio';
	}

	$pages_layout_config = array_merge(
		$pages_layout_config, array(
			'h_' . $type => array(
				'title' => $title,
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			// Header
			'header_' . $type . '_id' => array(
				'title' => _x( 'Header', 'site top area', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'hints_for' => 'us_header',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
						'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
					), $us_headers_list
				),
				'std' => '__defaults__',
			),
			// Titlebar
			'titlebar_' . $type . '_id' => array(
				'title' => __( 'Titlebar', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'hints_for' => 'us_page_block',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
						'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
					), $us_page_blocks_list
				),
				'std' => '__defaults__',
				'place_if' => $usof_sidebar_titlebar,
			),
			// Content
			'content_' . $type . '_id' => array(
				'title' => __( 'Content template', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'hints_for' => 'us_content_template',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
						'' => '&ndash; ' . __( 'Show content as is', 'us' ) . ' &ndash;',
					), $us_content_templates_list
				),
				'std' => '__defaults__',
			),
			// Sidebar
			'sidebar_' . $type . '_id' => array(
				'title' => __( 'Sidebar', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
						'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
					), $sidebars_list
				),
				'std' => '__defaults__',
				'hints_for' => $sidebar_hints_for,
				'place_if' => $usof_sidebar_titlebar,
			),
			// Sidebar Position
			'sidebar_' . $type . '_pos' => array(
				'title_pos' => 'side',
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'right',
				'classes' => 'for_above',
				'show_if' => array( 'sidebar_' . $type . '_id', '!=', array( '', '__defaults__' ) ),
				'place_if' => $usof_sidebar_titlebar,
			),
			// Footer
			'footer_' . $type . '_id' => array(
				'title' => __( 'Footer', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'hints_for' => 'us_page_block',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
						'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
					), $us_page_blocks_list
				),
				'std' => '__defaults__',
			),
		)
	);
}

// Generate 'Archives Layout' options
$archives_layout_config = array();
$public_taxonomies = us_get_taxonomies( TRUE, FALSE, 'woocommerce_exclude' );
foreach ( $public_taxonomies as $type => $title ) {

	$archives_layout_config = array_merge(
		$archives_layout_config, array(
			'h_tax_' . $type => array(
				'title' => $title,
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			// Header
			'header_tax_' . $type . '_id' => array(
				'title' => _x( 'Header', 'site top area', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'hints_for' => 'us_header',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
						'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
					), $us_headers_list
				),
				'std' => '__defaults__',
			),
			// Titlebar
			'titlebar_tax_' . $type . '_id' => array(
				'title' => __( 'Titlebar', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'hints_for' => 'us_page_block',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
						'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
					), $us_page_blocks_list
				),
				'std' => '__defaults__',
				'place_if' => $usof_sidebar_titlebar,
			),
			// Content
			'content_tax_' . $type . '_id' => array(
				'title' => __( 'Content template', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'hints_for' => 'us_content_template',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
					), $us_content_templates_list
				),
				'std' => '__defaults__',
			),
			// Sidebar
			'sidebar_tax_' . $type . '_id' => array(
				'title' => __( 'Sidebar', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
						'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
					), $sidebars_list
				),
				'hints_for' => $sidebar_hints_for,
				'std' => '__defaults__',
				'place_if' => $usof_sidebar_titlebar,
			),
			// Sidebar Position
			'sidebar_tax_' . $type . '_pos' => array(
				'title_pos' => 'side',
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'right',
				'classes' => 'for_above',
				'show_if' => array( 'sidebar_tax_' . $type . '_id', '!=', array( '', '__defaults__' ) ),
				'place_if' => $usof_sidebar_titlebar,
			),
			// Footer
			'footer_tax_' . $type . '_id' => array(
				'title' => __( 'Footer', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'hints_for' => 'us_page_block',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
						'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
					), $us_page_blocks_list
				),
				'std' => '__defaults__',
			),
		)
	);

}

// Generate Product taxonomies Layout options
$shop_layout_config = array();
if ( class_exists( 'woocommerce' ) ) {
	$product_taxonomies = us_get_taxonomies( TRUE, FALSE, 'woocommerce_only' );
	foreach ( $product_taxonomies as $type => $title ) {

		$shop_layout_config = array_merge(
			$shop_layout_config, array(
				'h_tax_' . $type => array(
					'title' => $title,
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				// Header
				'header_tax_' . $type . '_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_header',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_headers_list
					),
					'std' => '__defaults__',
				),
				// Titlebar
				'titlebar_tax_' . $type . '_id' => array(
					'title' => __( 'Titlebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
					'place_if' => $usof_sidebar_titlebar,
				),
				// Content
				'content_tax_' . $type . '_id' => array(
					'title' => __( 'Content template', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_content_template',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
						), $us_content_templates_list
					),
					'std' => '__defaults__',
				),
				// Sidebar
				'sidebar_tax_' . $type . '_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $sidebars_list
					),
					'std' => '__defaults__',
					'hints_for' => $sidebar_hints_for,
					'place_if' => $usof_sidebar_titlebar,
				),
				// Sidebar Position
				'sidebar_tax_' . $type . '_pos' => array(
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'classes' => 'for_above',
					'show_if' => array( 'sidebar_tax_' . $type . '_id', '!=', array( '', '__defaults__' ) ),
					'place_if' => $usof_sidebar_titlebar,
				),
				// Footer
				'footer_tax_' . $type . '_id' => array(
					'title' => __( 'Footer', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
				),
			)
		);

	}
}

// Generate Images Sizes description
$img_size_info = '';
if ( ! wp_doing_ajax() AND $pagenow == 'admin.php' ) {
	$img_size_info .= '<span class="usof-tooltip"><strong>';
	$img_size_info .= sprintf( __( '%s different images sizes are registered.', 'us' ), count( us_get_image_sizes_list( FALSE ) ) );
	$img_size_info .= '</strong><span class="usof-tooltip-text">';
	foreach ( us_get_image_sizes_list( FALSE ) as $size_name => $size_title ) {
		$img_size_info .= $size_title . '<br>';
	}
	$img_size_info .= '</span></span><br>';

	// Add link to Media Settings admin page
	$img_size_info .= sprintf( __( 'To change the default image sizes, go to %s.', 'us' ), '<a target="_blank" rel="noopener" href="' . admin_url( 'options-media.php' ) . '">' . us_translate( 'Media Settings' ) . '</a>' );

	// Add link to Customizing > WooCommerce > Product Images
	if ( class_exists( 'woocommerce' ) ) {
		$img_size_info .= '<br>' . sprintf(
				__( 'To change the Product image sizes, go to %s.', 'us' ), '<a target="_blank" rel="noopener" href="' . esc_url(
					add_query_arg(
						array(
							'autofocus' => array(
								'panel' => 'woocommerce',
								'section' => 'woocommerce_product_images',
							),
							'url' => wc_get_page_permalink( 'shop' ),
						), admin_url( 'customize.php' )
					)
				) . '">' . us_translate( 'WooCommerce settings', 'woocommerce' ) . '</a>'
			);
	}
}

// Generate Icon Sets settings
$icon_sets_config = array();
$icon_sets = us_config( 'icon-sets', array() );
foreach ( $icon_sets as $icon_set_slug => $icon_set ) {

	$icon_sets_config = array_merge(
		$icon_sets_config, array(
			'icons_' . $icon_set_slug => array(
				'title' => $icon_set['set_name'],
				'title_pos' => 'side',
				'type' => 'radio',
				'options' => array(
					'default' => us_translate( 'Default' ),
					'custom' => __( 'Custom', 'us' ),
					'none' => us_translate( 'None' ),
				),
				'std' => 'default',
			),
			'icons_' . $icon_set_slug . '_custom_font' => array(
				'title_pos' => 'side',
				'description' => __( 'Link to "woff2" font file.', 'us' ),
				'type' => 'text',
				'std' => '',
				'show_if' => array( 'icons_' . $icon_set_slug, '=', 'custom' ),
				'classes' => 'for_above',
			),
		)
	);

}

// Generate Typography Headings
$headings_typography_config = array();
for ( $i = 1; $i <= 6; $i ++ ) {

	// Generate default value based on heading number
	$k = round( 0.6 + 2.4 / $i, 1 );
	$default_fontsize = sprintf( 'max( %srem, %svw )', $k, $k );

	$headings_typography_config = array_merge(
		$headings_typography_config, array(
			'h' . $i . '_font_family' => array(
				'type' => 'font',
				'preview' => array(
					'text' => sprintf( __( 'Heading %s Preview', 'us' ), $i ),
					'size_field' => 'h' . $i . '_fontsize',
					'weight_field' => 'h' . $i . '_fontweight',
					'letterspacing_field' => 'h' . $i . '_letterspacing',
					'transform_field' => 'h' . $i . '_transform',
					'color_field' => 'h' . $i . '_color',
					'for_heading' => TRUE,
					'get_h1' => ( $i == 1 ) ? FALSE : TRUE,
				),
				'std' => ( $i == 1 ) ? 'none|' : 'get_h1|',
			),
			'h' . $i . '_fontsize' => array(
				'title' => __( 'Font Size', 'us' ),
				'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">36px</span>, <span class="usof-example">2.8rem</span>, <span class="usof-example">max( 2rem, 2vw )</span>, <span class="usof-example">calc( 15px + 1.5vmax )</span>',
				'type' => 'text',
				'std' => $default_fontsize,
				'cols' => 2,
			),
			'h' . $i . '_fontsize_mobile' => array(
				'title' => __( 'Font Size on Mobiles', 'us' ),
				'description' => __( 'Leave blank to use the main Font Size value', 'us' ),
				'type' => 'text',
				'std' => '',
				'cols' => 2,
			),
			'h' . $i . '_lineheight' => array(
				'title' => __( 'Line height', 'us' ),
				'type' => 'slider',
				'std' => '1.2',
				'options' => array(
					'' => array(
						'min' => 1.00,
						'max' => 2.00,
						'step' => 0.01,
					),
					'px' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'classes' => 'for_above',
				'cols' => 2,
			),
			'h' . $i . '_letterspacing' => array(
				'title' => __( 'Letter Spacing', 'us' ),
				'type' => 'slider',
				'std' => '0',
				'options' => array(
					'em' => array(
						'min' => - 0.10,
						'max' => 0.20,
						'step' => 0.01,
					),
				),
				'classes' => 'for_above',
				'cols' => 2,
			),
			'h' . $i . '_fontweight' => array(
				'title' => __( 'Font Weight', 'us' ),
				'type' => 'slider',
				'std' => '400',
				'options' => array(
					'' => array(
						'min' => 100,
						'max' => 900,
						'step' => 100,
					),
				),
				'classes' => 'for_above',
				'cols' => 2,
			),
			'h' . $i . '_bottom_indent' => array(
				'title' => __( 'Bottom indent', 'us' ),
				'type' => 'slider',
				'std' => '1.5rem',
				'options' => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
					'em' => array(
						'min' => 0.1,
						'max' => 5.0,
						'step' => 0.1,
					),
					'rem' => array(
						'min' => 0.1,
						'max' => 5.0,
						'step' => 0.1,
					),
				),
				'classes' => 'for_above',
				'cols' => 2,
			),
			'h' . $i . '_color' => array(
				'title' => us_translate( 'Color' ),
				'type' => 'color',
				'with_gradient' => TRUE,
				'clear_pos' => 'right',
				'std' => '',
				'classes' => 'for_above',
				'cols' => 2,
			),
			'h' . $i . '_transform' => array(
				'title' => __( 'Text Styles', 'us' ),
				'type' => 'checkboxes',
				'options' => array(
					'uppercase' => __( 'Uppercase', 'us' ),
					'italic' => __( 'Italic', 'us' ),
				),
				'std' => '',
				'classes' => 'for_above',
				'cols' => 2,
			),
			'h' . $i . '_color_override' => array(
				'type' => 'checkboxes',
				'options' => array(
					'1' => __( 'Override color globally', 'us' ),
				),
				'std' => '',
				'classes' => 'for_above',
				'show_if' => array( 'h' . $i . '_color', '!=', '' ),
				'cols' => 2,
			),
		)
	);
}
$white_label_config = us_config( 'white-label', array(), TRUE )['white_label'];
$white_label_config['place_if'] = FALSE;

// Theme Options Config
return array(
	'general' => array(
		'title' => us_translate_x( 'General', 'settings screen' ),
		'fields' => array(

			'maintenance_mode' => array(
				'title' => __( 'Maintenance Mode', 'us' ),
				'title_pos' => 'side',
				'description' => __( 'When this option is ON, all non-logged in users will see only the selected page. This is useful when your site is under construction.', 'us' ),
				'type' => 'switch',
				'switch_text' => __( 'Show site visitors only one specific page', 'us' ),
				'std' => 0,
				'classes' => 'color_yellow desc_3',
				// show the setting, but disable it, if true
				'disabled' => get_option( 'us_license_dev_activated', 0 ),
			),
			'maintenance_mode_alert' => array(
				'title_pos' => 'side',
				'description' => sprintf( __( 'It\'s not possible to switch off this setting, while %s is activated for development.', 'us' ), US_THEMENAME ) . ' ' . sprintf( __( 'You can deactivate it on your %sLicenses%s page.', 'us' ), '<a href="' . $help_portal_url . '/user/licenses/" target="_blank" rel="noopener">', '</a>' ),
				'type' => 'message',
				'classes' => 'string',
				'place_if' => get_option( 'us_license_dev_activated', 0 ),
			),
			'maintenance_page' => array(
				'title_pos' => 'side',
				'type' => 'select',
				'options' => $us_page_list,
				'std' => '',
				'hints_for' => 'page',
				'classes' => 'for_above',
				'show_if' => array( 'maintenance_mode', '=', TRUE ),
			),
			'maintenance_503' => array(
				'title_pos' => 'side',
				'description' => __( 'When this option is ON, your site will send HTTP 503 response to search engines. Use this option only for short period of time.', 'us' ),
				'type' => 'switch',
				'switch_text' => __( 'Enable "503 Service Unavailable" status', 'us' ),
				'std' => 0,
				'classes' => 'for_above desc_3',
				'show_if' => array( 'maintenance_mode', '=', TRUE ),
			),
			'site_icon' => array(
				'title' => us_translate( 'Site Icon' ),
				'title_pos' => 'side',
				'description' => us_translate( 'Site Icons are what you see in browser tabs, bookmark bars, and within the WordPress mobile apps. Upload one here!' ) . '<br>' . sprintf( us_translate( 'Site Icons should be square and at least %s pixels.' ), '<strong>512</strong>' ),
				'type' => 'upload',
				'classes' => 'desc_3',
			),
			'dark_theme' => array(
				'title' => __( 'Dark Theme', 'us' ),
				'title_pos' => 'side',
				'description' => __( 'The selected color scheme will be automatically applied when the device is switched to a dark theme.', 'us' ),
				'type' => 'select',
				'options' => array_merge(
					array(
						'none' => '&ndash; ' . us_translate( 'None' ) . ' &ndash;',
					),
					us_get_color_schemes( TRUE )
				),
				'std' => 'none',
				'classes' => 'desc_3',
			),
			'preloader' => array(
				'title' => __( 'Preloader Screen', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'options' => array(
					'disabled' => '&ndash; ' . us_translate( 'None' ) . ' &ndash;',
					'1' => sprintf( __( 'Shows Preloader %d', 'us' ), 1 ),
					'2' => sprintf( __( 'Shows Preloader %d', 'us' ), 2 ),
					'3' => sprintf( __( 'Shows Preloader %d', 'us' ), 3 ),
					'4' => sprintf( __( 'Shows Preloader %d', 'us' ), 4 ),
					'5' => sprintf( __( 'Shows Preloader %d', 'us' ), 5 ),
					'custom' => __( 'Shows Custom Image', 'us' ),
				),
				'std' => 'disabled',
			),
			'preloader_image' => array(
				'title' => '',
				'title_pos' => 'side',
				'type' => 'upload',
				'classes' => 'for_above',
				'show_if' => array( 'preloader', '=', 'custom' ),
			),
			'img_placeholder' => array(
				'title' => __( 'Images Placeholder', 'us' ),
				'title_pos' => 'side',
				'type' => 'upload',
				'std' => sprintf( '%s/assets/images/placeholder.svg', US_CORE_URI ),
			),
			'ripple_effect' => array(
				'title' => __( 'Ripple Effect', 'us' ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Show the ripple effect when theme elements are clicked', 'us' ),
				'std' => 0,
			),
			'rounded_corners' => array(
				'title' => __( 'Rounded Corners', 'us' ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Round corners of theme elements', 'us' ),
				'std' => 1,
			),
			'links_underline' => array(
				'title' => __( 'Underlining Links', 'us' ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Underline text links on hover', 'us' ),
				'std' => 0,
			),
			'keyboard_accessibility' => array(
				'title' => __( 'Keyboard Accessibility', 'us' ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Highlight theme elements on focus', 'us' ),
				'std' => 0,
			),

			// Back to Top
			'back_to_top' => array(
				'title' => sprintf( __( '"%s" Button', 'us' ), __( 'Back to Top', 'us' ) ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Show the button that helps users navigate to the top of long pages', 'us' ),
				'std' => 1,
			),
			'wrapper_back_to_top_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array( 'back_to_top', '=', TRUE ),
			),
			'back_to_top_style' => array(
				'title' => __( 'Button Style', 'us' ),
				'description' => '<a href="' . admin_url() . 'admin.php?page=us-theme-options#buttons">' . __( 'Edit Button Styles', 'us' ) . '</a>',
				'type' => 'select',
				'options' => us_array_merge(
					array(
						'' => '&ndash; ' . us_translate( 'Default' ) . ' &ndash;',
					), us_get_btn_styles()
				),
				'std' => '',
			),
			'back_to_top_pos' => array(
				'title' => __( 'Button Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'right',
				'classes' => 'cols_2',
			),
			'back_to_top_color' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'title' => __( 'Button Color', 'us' ),
				'std' => 'rgba(0,0,0,0.3)',
				'classes' => 'cols_2',
				'show_if' => array( 'back_to_top_style', '=', '' ),
			),
			'back_to_top_display' => array(
				'title' => __( 'Page Scroll Amount to Show the Button', 'us' ),
				'type' => 'slider',
				'std' => '100vh',
				'options' => array(
					'vh' => array(
						'min' => 10,
						'max' => 200,
						'step' => 10,
					),
				),
				'classes' => 'desc_3',
			),
			'wrapper_back_to_top_end' => array(
				'type' => 'wrapper_end',
			),

			// Cookie Notice
			'cookie_notice' => array(
				'title' => __( 'Cookie Notice', 'us' ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Show floating notice for new site visitors', 'us' ),
				'std' => 0,
			),
			'wrapper_cookie_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array( 'cookie_notice', '=', TRUE ),
			),
			'cookie_message' => array(
				'title' => us_translate( 'Message' ),
				'type' => 'textarea',
				'std' => 'This website uses cookies to improve your experience. If you continue to use this site, you agree with it.',
				'classes' => 'desc_3',
			),
			'cookie_privacy' => array(
				'type' => 'checkboxes',
				'options' => array(
					'page_link' => sprintf( __( 'Show link to the %s page', 'us' ), '<a href="' . admin_url( 'options-privacy.php' ) . '" target="_blank">' . us_translate( 'Privacy Policy' ) . '</a>' ),
				),
				'std' => '',
				'classes' => 'for_above',
			),
			'cookie_message_pos' => array(
				'title' => us_translate( 'Position' ),
				'type' => 'radio',
				'options' => array(
					'top' => us_translate( 'Top' ),
					'bottom' => us_translate( 'Bottom' ),
				),
				'std' => 'bottom',
			),
			'cookie_btn_label' => array(
				'title' => __( 'Button Label', 'us' ),
				'type' => 'text',
				'std' => 'Ok',
				'classes' => 'cols_2',
			),
			'cookie_btn_style' => array(
				'title' => __( 'Button Style', 'us' ),
				'description' => '<a href="' . admin_url() . 'admin.php?page=us-theme-options#buttons">' . __( 'Edit Button Styles', 'us' ) . '</a>',
				'type' => 'select',
				'options' => us_get_btn_styles(),
				'std' => '1',
				'classes' => 'cols_2',
			),
			'wrapper_cookie_end' => array(
				'type' => 'wrapper_end',
			),

			'smooth_scroll_duration' => array(
				'title' => __( 'Smooth Scroll Duration', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '1000ms',
				'options' => array(
					'ms' => array(
						'min' => 0,
						'max' => 3000,
						'step' => 100,
					),
				),
			),
		),
	),

	'layout' => array(
		'title' => __( 'Site Layout', 'us' ),
		'fields' => array(
			'canvas_layout' => array(
				'title' => __( 'Site Canvas Layout', 'us' ),
				'title_pos' => 'side',
				'type' => 'imgradio',
				'preview_path' => '/admin/img/%s.png',
				'options' => array(
					'wide' => '',
					'boxed' => '',
				),
				'std' => 'wide',
			),
			'color_body_bg' => array(
				'title_pos' => 'side',
				'type' => 'color',
				'with_gradient' => TRUE,
				'title' => __( 'Body Background Color', 'us' ),
				'std' => '_content_bg_alt',
				'show_if' => array( 'canvas_layout', '=', 'boxed' ),
			),
			'body_bg_image' => array(
				'title' => __( 'Body Background Image', 'us' ),
				'title_pos' => 'side',
				'type' => 'upload',
				'show_if' => array( 'canvas_layout', '=', 'boxed' ),
			),
			'wrapper_body_bg_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array(
					array( 'canvas_layout', '=', 'boxed' ),
					'and',
					array( 'body_bg_image', '!=', '' ),
				),
			),
			'body_bg_image_size' => array(
				'title' => __( 'Background Image Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'cover' => __( 'Fill Area', 'us' ),
					'contain' => __( 'Fit to Area', 'us' ),
					'initial' => __( 'Initial', 'us' ),
				),
				'std' => 'cover',
			),
			'body_bg_image_repeat' => array(
				'title' => __( 'Background Image Repeat', 'us' ),
				'type' => 'radio',
				'options' => array(
					'repeat' => __( 'Repeat', 'us' ),
					'repeat-x' => __( 'Horizontally', 'us' ),
					'repeat-y' => __( 'Vertically', 'us' ),
					'no-repeat' => us_translate( 'None' ),
				),
				'std' => 'repeat',
			),
			'body_bg_image_position' => array(
				'title' => __( 'Background Image Position', 'us' ),
				'type' => 'radio',
				'labels_as_icons' => 'fas fa-arrow-up',
				'options' => array(
					'top left' => us_translate( 'Top Left' ),
					'top center' => us_translate( 'Top' ),
					'top right' => us_translate( 'Top Right' ),
					'center left' => us_translate( 'Left' ),
					'center center' => us_translate( 'Center' ),
					'center right' => us_translate( 'Right' ),
					'bottom left' => us_translate( 'Bottom Left' ),
					'bottom center' => us_translate( 'Bottom' ),
					'bottom right' => us_translate( 'Bottom Right' ),
				),
				'std' => 'top left',
				'classes' => 'bgpos',
			),
			'body_bg_image_attachment' => array(
				'type' => 'switch',
				'switch_text' => us_translate( 'Scroll with Page' ),
				'std' => 1,
			),
			'wrapper_body_bg_end' => array(
				'type' => 'wrapper_end',
			),
			'site_canvas_width' => array(
				'title' => __( 'Site Canvas Width', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '1300px',
				'options' => array(
					'px' => array(
						'min' => 1000,
						'max' => 1700,
						'step' => 10,
					),
				),
				'show_if' => array( 'canvas_layout', '=', 'boxed' ),
			),
			'site_content_width' => array(
				'title' => __( 'Site Content Width', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '1140px',
				'options' => array(
					'px' => array(
						'min' => 900,
						'max' => 1600,
						'step' => 10,
					),
				),
			),
			'sidebar_width' => array(
				'title' => __( 'Sidebar Width', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '25%',
				'options' => array(
					'%' => array(
						'min' => 20,
						'max' => 40,
						'step' => 0.1,
					),
				),
				'place_if' => $usof_sidebar_titlebar,
			),
			'row_height' => array(
				'title' => __( 'Default Vertical Row Indents', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'options' => array(
					'auto' => us_translate( 'None' ),
					'small' => 'S',
					'medium' => 'M',
					'large' => 'L',
					'huge' => 'XL',
					'custom' => __( 'Custom', 'us' ),
				),
				'std' => 'medium',
			),
			'row_height_custom' => array(
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '5vmax',
				'classes' => 'for_above',
				'options' => array(
					'rem' => array(
						'min' => 0,
						'max' => 8,
						'step' => 0.5,
					),
					'vh' => array(
						'min' => 0,
						'max' => 25,
					),
					'vmax' => array(
						'min' => 0,
						'max' => 25,
					),
				),
				'show_if' => array( 'row_height', '=', 'custom' ),
			),
			'text_bottom_indent' => array(
				'title' => __( 'Bottom Indent of Text Blocks', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '1.5rem',
				'options' => array(
					'rem' => array(
						'min' => 0,
						'max' => 3,
						'step' => 0.1,
					),
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
			),
			'footer_reveal' => array(
				'title' => __( 'Footer', 'us' ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Enable Footer Reveal Effect', 'us' ),
				'std' => FALSE,
			),
			'disable_effects_width' => array(
				'title' => __( 'Animations Disable Width', 'us' ),
				'title_pos' => 'side',
				'description' => __( 'When the screen width is less than this value, vertical parallax and appearance animations are disabled.', 'us' ),
				'type' => 'slider',
				'std' => '900px',
				'options' => array(
					'px' => array(
						'min' => 300,
						'max' => 1025,
					),
				),
				'classes' => 'desc_3',
			),
			'columns_stacking_width' => array(
				'title' => __( 'Columns Stacking Width', 'us' ),
				'title_pos' => 'side',
				'description' => __( 'When screen width is less than this value, all columns within a row become a single column.', 'us' ),
				'type' => 'slider',
				'std' => '768px',
				'options' => array(
					'px' => array(
						'min' => 768,
						'max' => 1025,
					),
				),
				'classes' => 'desc_3',
			),
			'laptops_breakpoint' => array(
				'title' => __( 'Laptops Screen Width', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '1280px',
				'options' => array(
					'px' => array(
						'min' => 1024,
						'max' => 1400,
					),
				),
				'classes' => 'desc_3',
			),
			'tablets_breakpoint' => array(
				'title' => __( 'Tablets Screen Width', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '1024px',
				'options' => array(
					'px' => array(
						'min' => 768,
						'max' => 1280,
					),
				),
				'classes' => 'desc_3',
			),
			'mobiles_breakpoint' => array(
				'title' => __( 'Mobiles Screen Width', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '600px',
				'options' => array(
					'px' => array(
						'min' => 320,
						'max' => 768,
					),
				),
				'classes' => 'desc_3',
			),
		),
	),

	// Pages Layout
	'pages_layout' => array(
		'title' => __( 'Pages Layout', 'us' ),
		'fields' => array_merge(
			array(

				// Search Results
				'search_page' => array(
					'title' => __( 'Search Results', 'us' ),
					'title_pos' => 'side',
					'description' => __( 'The selected page must contain a Grid element showing items of the current query.', 'us' ),
					'type' => 'select',
					'options' => us_array_merge(
						array( 'default' => '&ndash; ' . __( 'Show results via Grid element with defaults', 'us' ) . ' &ndash;' ), $us_page_list
					),
					'std' => 'default',
					'hints_for' => 'page',
					'classes' => 'desc_3',
				),
				'exclude_post_types_in_search' => array(
					'title' => __( 'Exclude from Search Results', 'us' ),
					'title_pos' => 'side',
					'type' => 'checkboxes',
					'options' => us_get_public_post_types(),
					'std' => '',
				),

				// Posts page is set in Settings > Reading
				'posts_page' => array(
					'title' => us_translate( 'Posts page' ),
					'title_pos' => 'side',
					'description' => __( 'The selected page must contain a Grid element showing items of the current query.', 'us' ),
					'type' => 'select',
					'options' => us_array_merge(
						array( 'default' => '&ndash; ' . __( 'Show results via Grid element with defaults', 'us' ) . ' &ndash;' ), $us_page_list
					),
					'std' => 'default',
					'hints_for' => 'page',
					'classes' => 'desc_3',
				),

				// 404 page
				'page_404' => array(
					'title' => __( 'Page "404 Not Found"', 'us' ),
					'title_pos' => 'side',
					'description' => __( 'The selected page will be shown instead of the "Page not found" message.', 'us' ),
					'type' => 'select',
					'options' => us_array_merge(
						array( 'default' => '&ndash; ' . us_translate( 'Default' ) . ' &ndash;' ), $us_page_list
					),
					'std' => 'default',
					'hints_for' => 'page',
					'classes' => 'desc_3',
				),

				// Pages
				'h_defaults' => array(
					'title' => us_translate_x( 'Pages', 'post type general name' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'header_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'title_pos' => 'side',
					'description' => $misc['headers_description'],
					'type' => 'select',
					'hints_for' => 'us_header',
					'options' => us_array_merge(
						array( '' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;' ), $us_headers_list
					),
					'std' => key( $us_headers_list ),
					'classes' => 'desc_3',
				),
				'titlebar_id' => array(
					'title' => __( 'Titlebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '',
					'place_if' => $usof_sidebar_titlebar,
				),
				'content_id' => array(
					'title' => __( 'Content template', 'us' ),
					'title_pos' => 'side',
					'description' => $usof_sidebar_titlebar ? '' : $misc['content_description'],
					'type' => 'select',
					'hints_for' => 'us_content_template',
					'options' => us_array_merge(
						array( '' => '&ndash; ' . __( 'Show content as is', 'us' ) . ' &ndash;' ), $us_content_templates_list
					),
					'std' => '',
					'classes' => 'desc_3',
				),
				'sidebar_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $sidebars_list
					),
					'std' => '',
					'hints_for' => $sidebar_hints_for,
					'place_if' => $usof_sidebar_titlebar,
				),
				'sidebar_pos' => array(
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'classes' => 'for_above',
					'show_if' => array( 'sidebar_id', '!=', '' ),
					'place_if' => $usof_sidebar_titlebar,
				),
				'footer_id' => array(
					'title' => __( 'Footer', 'us' ),
					'title_pos' => 'side',
					'description' => $misc['footers_description'],
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array( '' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;' ), $us_page_blocks_list
					),
					'std' => '',
					'classes' => 'desc_3',
				),

			), $pages_layout_config
		),
	),

	// Archives Layout
	'archives_layout' => array(
		'title' => __( 'Archives Layout', 'us' ),
		'fields' => array_merge(
			array(

				// Archives
				'h_archive_defaults' => array(
					'title' => us_translate( 'Archives' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'header_archive_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'title_pos' => 'side',
					'description' => $misc['headers_description'],
					'type' => 'select',
					'hints_for' => 'us_header',
					'options' => us_array_merge(
						array( '' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;' ), $us_headers_list
					),
					'std' => key( $us_headers_list ),
					'classes' => 'desc_3',
				),
				'titlebar_archive_id' => array(
					'title' => __( 'Titlebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '',
					'place_if' => $usof_sidebar_titlebar,
				),
				'content_archive_id' => array(
					'title' => __( 'Content template', 'us' ),
					'title_pos' => 'side',
					'description' => $usof_sidebar_titlebar ? '' : $misc['content_description'],
					'type' => 'select',
					'hints_for' => 'us_content_template',
					'options' => us_array_merge(
						array( '' => '&ndash; ' . __( 'Show results via Grid element with defaults', 'us' ) . ' &ndash;' ), $us_content_templates_list
					),
					'std' => '',
					'classes' => 'desc_3',
				),
				'sidebar_archive_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $sidebars_list
					),
					'std' => '',
					'hints_for' => $sidebar_hints_for,
					'place_if' => $usof_sidebar_titlebar,
				),
				'sidebar_archive_pos' => array(
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'classes' => 'for_above',
					'show_if' => array( 'sidebar_archive_id', '!=', '' ),
					'place_if' => $usof_sidebar_titlebar,
				),
				'footer_archive_id' => array(
					'title' => __( 'Footer', 'us' ),
					'title_pos' => 'side',
					'description' => $misc['footers_description'],
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array( '' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;' ), $us_page_blocks_list
					),
					'std' => '',
					'classes' => 'desc_3',
				),

			), $archives_layout_config, array(

				// Authors
				'h_authors' => array(
					'title' => __( 'Authors', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'header_author_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_header',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_headers_list
					),
					'std' => '__defaults__',
				),
				'titlebar_author_id' => array(
					'title' => __( 'Titlebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
					'place_if' => $usof_sidebar_titlebar,
				),
				'content_author_id' => array(
					'title' => __( 'Content template', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
						), $us_content_templates_list
					),
					'std' => '__defaults__',
				),
				'sidebar_author_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $sidebars_list
					),
					'std' => '__defaults__',
					'hints_for' => $sidebar_hints_for,
					'place_if' => $usof_sidebar_titlebar,
				),
				'sidebar_author_pos' => array(
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'classes' => 'for_above',
					'show_if' => array( 'sidebar_author_id', '!=', array( '', '__defaults__' ) ),
					'place_if' => $usof_sidebar_titlebar,
				),
				'footer_author_id' => array(
					'title' => __( 'Footer', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
				),

			)

		),
	),

	// Colors
	'colors' => array(
		'title' => us_translate( 'Colors' ),
		'fields' => array(

			// Color Schemes
			'style_scheme' => array(
				'type' => 'style_scheme',
			),

			// Header colors
			'change_header_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'for_colors',
			),
			'h_colors_1' => array(
				'title' => __( 'Header colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'color_header_middle_bg' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => us_translate( 'Background' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_header_middle_text' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => us_translate( 'Text' ) . ' / ' . us_translate( 'Link' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_header_middle_text_hover' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => __( 'Link on hover', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_header_transparent_bg' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'std' => 'transparent',
				'text' => __( 'Transparent Header', 'us' ) . ': ' . us_translate( 'Background' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_header_transparent_text' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => __( 'Transparent Header', 'us' ) . ': ' . us_translate( 'Text' ) . ' / ' . us_translate( 'Link' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_header_transparent_text_hover' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => __( 'Transparent Header', 'us' ) . ': ' . __( 'Link on hover', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_chrome_toolbar' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => __( 'Toolbar in Chrome for Android', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'change_header_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Alternate Header colors
			'change_header_alt_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'for_colors',
			),
			'h_colors_2' => array(
				'title' => __( 'Alternate Header colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'color_header_top_bg' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => us_translate( 'Background' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_header_top_text' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => us_translate( 'Text' ) . ' / ' . us_translate( 'Link' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_header_top_text_hover' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => __( 'Link on hover', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_header_top_transparent_bg' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'std' => 'rgba(0,0,0,0.2)',
				'text' => __( 'Transparent Header', 'us' ) . ': ' . us_translate( 'Background' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_header_top_transparent_text' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'std' => 'rgba(255,255,255,0.66)',
				'text' => __( 'Transparent Header', 'us' ) . ': ' . us_translate( 'Text' ) . ' / ' . us_translate( 'Link' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_header_top_transparent_text_hover' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'std' => '#fff',
				'text' => __( 'Transparent Header', 'us' ) . ': ' . __( 'Link on hover', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'change_header_alt_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Content colors
			'change_content_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'for_colors',
			),
			'h_colors_3' => array(
				'title' => __( 'Content colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'color_content_bg' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => us_translate( 'Background' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_content_bg_alt' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => __( 'Alternate Background', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_content_border' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => us_translate( 'Border' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_content_heading' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => __( 'Headings', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_content_text' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => us_translate( 'Text' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_content_link' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => us_translate( 'Link' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_content_link_hover' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => __( 'Link on hover', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_content_primary' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => __( 'Primary Color', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_content_secondary' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => __( 'Secondary Color', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_content_faded' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => __( 'Faded Text', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_content_overlay' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'std' => 'rgba(0,0,0,0.75)',
				'text' => __( 'Background Overlay', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'change_content_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Alternate Content colors
			'change_alt_content_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'for_colors',
			),
			'h_colors_4' => array(
				'title' => __( 'Alternate Content colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'color_alt_content_bg' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => us_translate( 'Background' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_alt_content_bg_alt' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => __( 'Alternate Background', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_alt_content_border' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => us_translate( 'Border' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_alt_content_heading' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => __( 'Headings', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_alt_content_text' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => us_translate( 'Text' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_alt_content_link' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => us_translate( 'Link' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_alt_content_link_hover' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => __( 'Link on hover', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_alt_content_primary' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => __( 'Primary Color', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_alt_content_secondary' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => __( 'Secondary Color', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_alt_content_faded' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => __( 'Faded Text', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_alt_content_overlay' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'std' => 'rgba(0,0,0,0.75)',
				'text' => __( 'Background Overlay', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'change_alt_content_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Footer colors
			'change_footer_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'for_colors',
			),
			'h_colors_6' => array(
				'title' => __( 'Footer colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'color_footer_bg' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => us_translate( 'Background' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_footer_bg_alt' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => __( 'Alternate Background', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_footer_border' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => us_translate( 'Border' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_footer_heading' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => __( 'Headings', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_footer_text' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => us_translate( 'Text' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_footer_link' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => us_translate( 'Link' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_footer_link_hover' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => __( 'Link on hover', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'change_footer_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Alternate Footer colors
			'change_subfooter_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'for_colors',
			),
			'h_colors_5' => array(
				'title' => __( 'Alternate Footer colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'color_subfooter_bg' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => us_translate( 'Background' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_subfooter_bg_alt' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => __( 'Alternate Background', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_subfooter_border' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => us_translate( 'Border' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_subfooter_heading' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'text' => __( 'Headings', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_subfooter_text' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => us_translate( 'Text' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_subfooter_link' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => us_translate( 'Link' ),
				'disable_dynamic_vars' => TRUE,
			),
			'color_subfooter_link_hover' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'text' => __( 'Link on hover', 'us' ),
				'disable_dynamic_vars' => TRUE,
			),
			'change_subfooter_colors_end' => array(
				'type' => 'wrapper_end',
			),

		),
	),

	// Typography
	'typography' => array(
		'title' => __( 'Typography', 'us' ),
		'fields' => array_merge(
			array(

				// Global Text
				'body_font_family' => array(
					'type' => 'font',
					'preview' => array(
						'text' => __( 'Here\'s a preview of what your website\'s text will look like <strong>by default</strong>. You can also adjust the typography of most elements separately. Note that the Font Size setting affects all the sizes defined in "rem" units, that is, almost all areas of your site.', 'us' ),
						'size_field' => 'body_fontsize',
						'lineheight_field' => 'body_lineheight',
					),
					'std' => 'Georgia, serif',
				),
				'body_fontsize' => array(
					'title' => __( 'Font Size', 'us' ),
					'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">18px</span>, <span class="usof-example">1.1rem</span>, <span class="usof-example">max( 16px, 1vw )</span>, <span class="usof-example">calc( 14px + 0.3vmax )</span>',
					'type' => 'text',
					'std' => '18px',
					'cols' => 2,
				),
				'body_fontsize_mobile' => array(
					'title' => __( 'Font Size on Mobiles', 'us' ),
					'description' => __( 'Leave blank to use the main Font Size value', 'us' ),
					'type' => 'text',
					'std' => '16px',
					'cols' => 2,
				),
				'body_lineheight' => array(
					'title' => __( 'Line height', 'us' ),
					'type' => 'slider',
					'std' => '28px',
					'options' => array(
						'' => array(
							'min' => 1.00,
							'max' => 2.00,
							'step' => 0.01,
						),
						'px' => array(
							'min' => 15,
							'max' => 50,
						),
					),
					'classes' => 'for_above',
					'cols' => 2,
				),
				'body_lineheight_mobile' => array(
					'title' => __( 'Line height on Mobiles', 'us' ),
					'type' => 'slider',
					'std' => '28px',
					'options' => array(
						'' => array(
							'min' => 1.00,
							'max' => 2.00,
							'step' => 0.01,
						),
						'px' => array(
							'min' => 15,
							'max' => 50,
						),
					),
					'classes' => 'for_above',
					'cols' => 2,
				),

			), $headings_typography_config, array(

				// Additional Google Fonts
				'h_typography_3' => array(
					'title' => __( 'Additional Google Fonts', 'us' ),
					'description' => __( 'In case when you need more Google Fonts in theme elements.', 'us' ),
					'type' => 'heading',
				),
				'custom_font' => array(
					'type' => 'group',
					'accordion_title' => 'font_family',
					'is_accordion' => FALSE,
					'is_duplicate' => FALSE,
					'show_controls' => TRUE,
					'std' => array(),
					'params' => array(
						'font_family' => array(
							'type' => 'font',
							'only_google' => TRUE,
							'preview' => array(
								'text' => __( 'Google Font Preview', 'us' ),
							),
							'std' => 'Open Sans',
						),
					),
				),

				// Uploaded Fonts
				'h_typography_4' => array(
					'title' => __( 'Uploaded Fonts', 'us' ),
					'description' => sprintf( __( 'Add custom fonts via uploading %s files.', 'us' ), '<strong>woff</strong>, <strong>woff2</strong>' ) . ' <a target="_blank" rel="noopener" href="' . $help_portal_url . '/' . strtolower( US_THEMENAME ) . '/options/typography/#uploaded-fonts">' . __( 'Learn more', 'us' ) . '</a>.',
					'type' => 'heading',
				),
				'uploaded_fonts' => array(
					'type' => 'group',
					'accordion_title' => 'name',
					'is_accordion' => FALSE,
					'is_duplicate' => FALSE,
					'show_controls' => TRUE,
					'classes' => 'with_wrapper',
					'std' => array(),
					'params' => array(
						'uploaded_font_start' => array(
							'type' => 'wrapper_start',
						),
						'name' => array(
							'title' => __( 'Font Name', 'us' ),
							'type' => 'text',
							'std' => 'Uploaded Font',
							'classes' => 'cols_2',
						),
						'weight' => array(
							'title' => __( 'Font Weight', 'us' ),
							'type' => 'slider',
							'std' => 400,
							'options' => array(
								'' => array(
									'min' => 100,
									'max' => 900,
									'step' => 100,
								),
							),
							'classes' => 'cols_2',
						),
						'italic' => array(
							'type' => 'checkboxes',
							'options' => array(
								'italic' => __( 'Italic', 'us' ),
							),
							'std' => '',
							'classes' => 'for_above',
						),
						'files' => array(
							'title' => __( 'Font Files', 'us' ),
							'type' => 'upload',
							'is_multiple' => TRUE,
							'preview_type' => 'text',
						),
						'uploaded_font_end' => array(
							'type' => 'wrapper_end',
						),
					),
				),

				// Font Display
				'h_typography_5' => array(
					'title' => __( 'Font Display', 'us' ),
					'description' => __( 'Sets behavior of fonts rendering.', 'us' ) . ' <a href="https://font-display.glitch.me/" target="_blank" rel="noopener">' . sprintf( __( 'Read about %s property', 'us' ), '"font-display"' ) . '</a>.',
					'type' => 'heading',
				),
				'font_display' => array(
					'type' => 'radio',
					'options' => array(
						'block' => 'block',
						'swap' => 'swap',
						'fallback' => 'fallback',
						'optional' => 'optional',
					),
					'std' => 'swap',
					'classes' => 'for_above',
				),

			)
		),
	),

	'buttons' => array(
		'title' => __( 'Button Styles', 'us' ),
		'fields' => array(
			'buttons' => array(
				'type' => 'group',
				'preview' => 'button',
				'is_accordion' => TRUE,
				'is_duplicate' => TRUE,
				'show_controls' => TRUE,
				'accordion_title' => 'name',
				'classes' => 'compact',
				'params' => array(
					'id' => array(
						'type' => 'text',
						'std' => NULL,
						'classes' => 'hidden',
					),
					'name' => array(
						'title' => __( 'Button Style Name', 'us' ),
						'type' => 'text',
						'std' => us_translate( 'Style' ),
						'cols' => 2,
					),
					'hover' => array(
						'title' => __( 'Hover Style', 'us' ),
						'description' => __( '"Slide background from the top" may not work with buttons of 3rd-party plugins.', 'us' ),
						'type' => 'select',
						'options' => array(
							'fade' => __( 'Simple color change', 'us' ),
							'slide' => __( 'Slide background from the top', 'us' ),
						),
						'std' => 'fade',
						'classes' => 'cols_2 desc_4',
					),

					// Button Colors
					'color_bg' => array(
						'title' => us_translate( 'Colors' ),
						'type' => 'color',
						'clear_pos' => 'left',
						'std' => '_content_secondary',
						'text' => us_translate( 'Background' ),
						'cols' => 2,
					),
					'color_bg_hover' => array(
						'title' => __( 'Colors on hover', 'us' ),
						'type' => 'color',
						'clear_pos' => 'left',
						'std' => '',
						'text' => us_translate( 'Background' ),
						'cols' => 2,
					),
					'color_border' => array(
						'type' => 'color',
						'clear_pos' => 'left',
						'std' => '',
						'text' => us_translate( 'Border' ),
						'cols' => 2,
					),
					'color_border_hover' => array(
						'type' => 'color',
						'clear_pos' => 'left',
						'std' => '_content_secondary',
						'text' => us_translate( 'Border' ),
						'cols' => 2,
					),
					'color_text' => array(
						'type' => 'color',
						'clear_pos' => 'left',
						'with_gradient' => FALSE,
						'std' => '#fff',
						'text' => us_translate( 'Text' ),
						'cols' => 2,
					),
					'color_text_hover' => array(
						'type' => 'color',
						'clear_pos' => 'left',
						'with_gradient' => FALSE,
						'std' => '_content_secondary',
						'text' => us_translate( 'Text' ),
						'cols' => 2,
					),
					'color_shadow' => array(
						'type' => 'color',
						'clear_pos' => 'left',
						'with_gradient' => FALSE,
						'std' => 'rgba(0,0,0,0.2)',
						'text' => __( 'Shadow', 'us' ),
						'cols' => 2,
					),
					'color_shadow_hover' => array(
						'type' => 'color',
						'clear_pos' => 'left',
						'with_gradient' => FALSE,
						'std' => 'rgba(0,0,0,0.2)',
						'text' => __( 'Shadow', 'us' ),
						'cols' => 2,
					),
					'shadow' => array(
						'title' => __( 'Shadow', 'us' ),
						'type' => 'slider',
						'std' => 0,
						'options' => array(
							'em' => array(
								'min' => 0.0,
								'max' => 2.0,
								'step' => 0.1,
							),
						),
						'classes' => 'cols_2 leave_padding',
					),
					'shadow_hover' => array(
						'title' => __( 'Shadow on hover', 'us' ),
						'type' => 'slider',
						'std' => 0,
						'options' => array(
							'em' => array(
								'min' => 0.0,
								'max' => 2.0,
								'step' => 0.1,
							),
						),
						'classes' => 'cols_2 leave_padding',
					),

					// Typography & Sizes
					'font' => array(
						'title' => __( 'Font', 'us' ),
						'type' => 'select',
						'options' => us_get_fonts(),
						'std' => 'body',
						'cols' => 2,
					),
					'height' => array(
						'title' => __( 'Relative Height', 'us' ),
						'type' => 'slider',
						'std' => '0.8em',
						'options' => array(
							'em' => array(
								'min' => 0.0,
								'max' => 2.0,
								'step' => 0.1,
							),
						),
						'cols' => 2,
					),
					'font_size' => array(
						'title' => __( 'Font Size', 'us' ),
						'type' => 'slider',
						'std' => '1rem',
						'options' => array(
							'px' => array(
								'min' => 10,
								'max' => 50,
							),
							'em' => array(
								'min' => 0.6,
								'max' => 3.0,
								'step' => 0.1,
							),
							'rem' => array(
								'min' => 0.6,
								'max' => 3.0,
								'step' => 0.1,
							),
						),
						'cols' => 2,
					),
					'width' => array(
						'title' => __( 'Relative Width', 'us' ),
						'type' => 'slider',
						'std' => '1.8em',
						'options' => array(
							'em' => array(
								'min' => 0.0,
								'max' => 5.0,
								'step' => 0.1,
							),
						),
						'cols' => 2,
					),
					'line_height' => array(
						'title' => __( 'Line height', 'us' ),
						'type' => 'slider',
						'std' => '1.2',
						'options' => array(
							'' => array(
								'min' => 1.00,
								'max' => 2.00,
								'step' => 0.01,
							),
							'px' => array(
								'min' => 10,
								'max' => 50,
							),
						),
						'cols' => 2,
					),
					'border_radius' => array(
						'title' => __( 'Border Radius', 'us' ),
						'type' => 'slider',
						'std' => '0.3em',
						'options' => array(
							'px' => array(
								'min' => 0,
								'max' => 30,
							),
							'em' => array(
								'min' => 0.0,
								'max' => 4.0,
								'step' => 0.1,
							),
							'rem' => array(
								'min' => 0.0,
								'max' => 4.0,
								'step' => 0.1,
							),
						),
						'cols' => 2,
					),
					'font_weight' => array(
						'title' => __( 'Font Weight', 'us' ),
						'type' => 'slider',
						'std' => 400,
						'options' => array(
							'' => array(
								'min' => 100,
								'max' => 900,
								'step' => 100,
							),
						),
						'cols' => 2,
					),
					'border_width' => array(
						'title' => __( 'Border Width', 'us' ),
						'type' => 'slider',
						'std' => '2px',
						'options' => array(
							'px' => array(
								'min' => 0,
								'max' => 10,
							),
						),
						'cols' => 2,
					),
					'letter_spacing' => array(
						'title' => __( 'Letter Spacing', 'us' ),
						'type' => 'slider',
						'std' => '0',
						'options' => array(
							'em' => array(
								'min' => - 0.10,
								'max' => 0.20,
								'step' => 0.01,
							),
						),
						'cols' => 2,
					),
					'text_style' => array(
						'title' => __( 'Text Styles', 'us' ),
						'type' => 'checkboxes',
						'options' => array(
							'uppercase' => __( 'Uppercase', 'us' ),
							'italic' => __( 'Italic', 'us' ),
						),
						'std' => '',
						'cols' => 2,
					),
				),
				'std' => array(
					array(
						'id' => 1,
						'name' => __( 'Default Button', 'us' ),
						'hover' => 'fade',
						// predefined colors after options reset
						'color_bg' => '_content_primary',
						'color_bg_hover' => '_content_secondary',
						'color_border' => '',
						'color_border_hover' => '',
						'color_text' => '#fff',
						'color_text_hover' => '#fff',
						'shadow' => 0,
						'shadow_hover' => 0,
						'font' => 'body',
						'text_style' => '',
						'font_size' => '16px',
						'line_height' => 1.2,
						'font_weight' => 700,
						'letter_spacing' => 0,
						'height' => '0.8em',
						'width' => '1.8em',
						'border_radius' => '0.3em',
						'border_width' => '0px',
					),
					array(
						'id' => 2,
						'name' => __( 'Button', 'us' ) . ' 2',
						'hover' => 'fade',
						// predefined colors after options reset
						'color_bg' => '_content_border',
						'color_bg_hover' => '_content_text',
						'color_border' => '',
						'color_border_hover' => '',
						'color_text' => '_content_text',
						'color_text_hover' => '_content_bg',
						'shadow' => 0,
						'shadow_hover' => 0,
						'font' => 'body',
						'text_style' => '',
						'font_size' => '16px',
						'line_height' => 1.2,
						'font_weight' => 700,
						'letter_spacing' => 0,
						'height' => '0.8em',
						'width' => '1.8em',
						'border_radius' => '0.3em',
						'border_width' => '0px',
					),
				),
			),

		),
	),

	// Fields Style
	'input_fields' => array(
		'title' => __( 'Fields Style', 'us' ),
		'fields' => array(
			'input_fields' => array(
				'type' => 'group',
				'preview' => 'input_fields',
				'is_accordion' => FALSE,
				'is_duplicate' => FALSE,
				'show_controls' => FALSE,
				'classes' => 'compact',
				'params' => array(

					// Colors
					'color_bg' => array(
						'title' => us_translate( 'Colors' ),
						'type' => 'color',
						'clear_pos' => 'left',
						'std' => '',
						'text' => us_translate( 'Background' ),
						'cols' => 2,
					),
					'color_bg_focus' => array(
						'title' => __( 'Colors on focus', 'us' ),
						'type' => 'color',
						'clear_pos' => 'left',
						'std' => '',
						'text' => us_translate( 'Background' ),
						'cols' => 2,
					),
					'color_border' => array(
						'type' => 'color',
						'clear_pos' => 'left',
						'with_gradient' => FALSE,
						'std' => '',
						'text' => us_translate( 'Border' ),
						'cols' => 2,
					),
					'color_border_focus' => array(
						'type' => 'color',
						'clear_pos' => 'left',
						'with_gradient' => FALSE,
						'std' => '',
						'text' => us_translate( 'Border' ),
						'cols' => 2,
					),
					'color_text' => array(
						'type' => 'color',
						'clear_pos' => 'left',
						'with_gradient' => FALSE,
						'std' => '',
						'text' => us_translate( 'Text' ),
						'cols' => 2,
					),
					'color_text_focus' => array(
						'type' => 'color',
						'clear_pos' => 'left',
						'with_gradient' => FALSE,
						'std' => '',
						'text' => us_translate( 'Text' ),
						'cols' => 2,
					),
					'color_shadow' => array(
						'type' => 'color',
						'clear_pos' => 'left',
						'with_gradient' => FALSE,
						'std' => 'rgba(0,0,0,0.2)',
						'text' => __( 'Shadow', 'us' ),
						'cols' => 2,
					),
					'color_shadow_focus' => array(
						'type' => 'color',
						'clear_pos' => 'left',
						'with_gradient' => FALSE,
						'std' => '',
						'text' => __( 'Shadow', 'us' ),
						'cols' => 2,
					),

					// Shadow
					'wrapper_shadow_start' => array(
						'title' => __( 'Shadow', 'us' ),
						'type' => 'wrapper_start',
						'classes' => 'for_shadow',
					),
					'shadow_offset_h' => array(
						'description' => __( 'Hor. offset', 'us' ),
						'type' => 'slider',
						'std' => '0px',
						'options' => array(
							'px' => array(
								'min' => - 10,
								'max' => 10,
							),
						),
						'classes' => 'slider_hide',
					),
					'shadow_offset_v' => array(
						'description' => __( 'Ver. offset', 'us' ),
						'type' => 'slider',
						'std' => '1px',
						'options' => array(
							'px' => array(
								'min' => - 10,
								'max' => 10,
							),
						),
						'classes' => 'slider_hide',
					),
					'shadow_blur' => array(
						'description' => __( 'Blur', 'us' ),
						'type' => 'slider',
						'std' => '0px',
						'options' => array(
							'px' => array(
								'min' => 0,
								'max' => 30,
							),
						),
						'classes' => 'slider_hide',
					),
					'shadow_spread' => array(
						'description' => __( 'Spread', 'us' ),
						'type' => 'slider',
						'std' => '0px',
						'options' => array(
							'px' => array(
								'min' => 0,
								'max' => 10,
							),
						),
						'classes' => 'slider_hide',
					),
					'wrapper_shadow_end' => array(
						'type' => 'wrapper_end',
					),

					// Shadow on focus
					'wrapper_shadow_focus_start' => array(
						'title' => __( 'Shadow on focus', 'us' ),
						'type' => 'wrapper_start',
						'classes' => 'for_shadow',
					),
					'shadow_focus_offset_h' => array(
						'description' => __( 'Hor. offset', 'us' ),
						'type' => 'slider',
						'std' => '0px',
						'options' => array(
							'px' => array(
								'min' => - 10,
								'max' => 10,
							),
						),
						'classes' => 'slider_hide',
					),
					'shadow_focus_offset_v' => array(
						'description' => __( 'Ver. offset', 'us' ),
						'type' => 'slider',
						'std' => '0px',
						'options' => array(
							'px' => array(
								'min' => - 10,
								'max' => 10,
							),
						),
						'classes' => 'slider_hide',
					),
					'shadow_focus_blur' => array(
						'description' => __( 'Blur', 'us' ),
						'type' => 'slider',
						'std' => '0px',
						'options' => array(
							'px' => array(
								'min' => 0,
								'max' => 30,
							),
						),
						'classes' => 'slider_hide',
					),
					'shadow_focus_spread' => array(
						'description' => __( 'Spread', 'us' ),
						'type' => 'slider',
						'std' => '2px',
						'options' => array(
							'px' => array(
								'min' => 0,
								'max' => 10,
							),
						),
						'classes' => 'slider_hide',
					),
					'wrapper_shadow_focus_end' => array(
						'type' => 'wrapper_end',
					),

					'shadow_inset' => array(
						'type' => 'checkboxes',
						'options' => array(
							'1' => __( 'Inner shadow', 'us' ),
						),
						'std' => '1',
						'cols' => 2,
					),
					'shadow_focus_inset' => array(
						'type' => 'checkboxes',
						'options' => array(
							'1' => __( 'Inner shadow', 'us' ),
						),
						'std' => '',
						'cols' => 2,
					),

					// Typography & Sizes
					'font' => array(
						'title' => __( 'Font', 'us' ),
						'type' => 'select',
						'options' => us_get_fonts(),
						'std' => '',
						'cols' => 2,
					),
					'height' => array(
						'title' => us_translate( 'Height' ),
						'type' => 'slider',
						'std' => '2.8rem',
						'options' => array(
							'px' => array(
								'min' => 30,
								'max' => 80,
							),
							'em' => array(
								'min' => 2.0,
								'max' => 5.0,
								'step' => 0.1,
							),
							'rem' => array(
								'min' => 2.0,
								'max' => 5.0,
								'step' => 0.1,
							),
						),
						'cols' => 2,
					),
					'font_size' => array(
						'title' => __( 'Font Size', 'us' ),
						'type' => 'slider',
						'std' => '1rem',
						'options' => array(
							'px' => array(
								'min' => 10,
								'max' => 30,
							),
							'em' => array(
								'min' => 0.8,
								'max' => 2.0,
								'step' => 0.1,
							),
							'rem' => array(
								'min' => 0.8,
								'max' => 2.0,
								'step' => 0.1,
							),
						),
						'cols' => 2,
					),
					'padding' => array(
						'title' => __( 'Side Indents', 'us' ),
						'type' => 'slider',
						'std' => '0.8rem',
						'options' => array(
							'px' => array(
								'min' => 0,
								'max' => 30,
							),
							'em' => array(
								'min' => 0.0,
								'max' => 2.0,
								'step' => 0.1,
							),
							'rem' => array(
								'min' => 0.0,
								'max' => 2.0,
								'step' => 0.1,
							),
						),
						'cols' => 2,
					),
					'font_weight' => array(
						'title' => __( 'Font Weight', 'us' ),
						'type' => 'slider',
						'std' => 400,
						'options' => array(
							'' => array(
								'min' => 100,
								'max' => 900,
								'step' => 100,
							),
						),
						'cols' => 2,
					),
					'border_radius' => array(
						'title' => __( 'Border Radius', 'us' ),
						'type' => 'slider',
						'std' => '0rem',
						'options' => array(
							'px' => array(
								'min' => 0,
								'max' => 30,
							),
							'em' => array(
								'min' => 0.0,
								'max' => 4.0,
								'step' => 0.1,
							),
							'rem' => array(
								'min' => 0.0,
								'max' => 4.0,
								'step' => 0.1,
							),
						),
						'cols' => 2,
					),
					'letter_spacing' => array(
						'title' => __( 'Letter Spacing', 'us' ),
						'type' => 'slider',
						'std' => '0em',
						'options' => array(
							'em' => array(
								'min' => - 0.10,
								'max' => 0.20,
								'step' => 0.01,
							),
						),
						'cols' => 2,
					),
					'border_width' => array(
						'title' => __( 'Border Width', 'us' ),
						'type' => 'slider',
						'std' => '0px',
						'options' => array(
							'px' => array(
								'min' => 0,
								'max' => 10,
							),
						),
						'cols' => 2,
					),
				),
				'std' => array(
					array(
						'color_bg' => '_content_bg_alt',
						'color_bg_focus' => '',
						'color_border' => '_content_border',
						'color_border_focus' => '',
						'color_text' => '_content_text',
						'color_text_focus' => '',
						'color_shadow' => 'rgba(0,0,0,0.08)',
						'color_shadow_focus' => '_content_primary',
						'shadow_offset_h' => '0px',
						'shadow_offset_v' => '1px',
						'shadow_blur' => '0px',
						'shadow_spread' => '0px',
						'shadow_inset' => '1',
						'shadow_focus_offset_h' => '0px',
						'shadow_focus_offset_v' => '0px',
						'shadow_focus_blur' => '0px',
						'shadow_focus_spread' => '2px',
						'shadow_focus_inset' => '',
						'font' => '',
						'font_size' => '1rem',
						'font_weight' => '400',
						'letter_spacing' => '0em',
						'height' => '2.8rem',
						'padding' => '0.8rem',
						'border_radius' => ! empty( $usof_options['rounded_corners'] ) ? '0.3rem' : '0rem',
						'border_width' => '0px',
					),
				),
			),
		),
	),

	// Portfolio
	'portfolio' => array(
		'title' => __( 'Portfolio', 'us' ) . $renamed_portfolio_label,
		'place_if' => ( $usof_enable_portfolio == 1 ),
		'fields' => array(

			'portfolio_breadcrumbs_page' => array(
				'title' => __( 'Intermediate Breadcrumbs Page', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'options' => us_array_merge(
					array( '' => '&ndash; ' . us_translate( 'None' ) . ' &ndash;' ), $us_page_list
				),
				'std' => '',
			),

			// Slugs
			'portfolio_slug' => array(
				'title' => __( 'Portfolio Page Slug', 'us' ),
				'title_pos' => 'side',
				'type' => 'text',
				'std' => 'portfolio',
			),
			'portfolio_category_slug' => array(
				'title' => __( 'Portfolio Category Slug', 'us' ),
				'title_pos' => 'side',
				'type' => 'text',
				'std' => 'portfolio_category',
				'classes' => 'for_above',
			),
			'portfolio_tag_slug' => array(
				'title' => __( 'Portfolio Tag Slug', 'us' ),
				'title_pos' => 'side',
				'type' => 'text',
				'std' => 'portfolio_tag',
				'classes' => 'for_above',
			),

			// Rename Portfolio
			'portfolio_rename' => array(
				'switch_text' => sprintf( __( 'Rename "%s" labels', 'us' ), __( 'Portfolio', 'us' ) ),
				'type' => 'switch',
				'std' => 0,
			),
			'portfolio_label_name' => array(
				'title' => __( 'Portfolio', 'us' ),
				'title_pos' => 'side',
				'std' => __( 'Portfolio', 'us' ),
				'type' => 'text',
				'classes' => 'for_above',
				'show_if' => array( 'portfolio_rename', '=', 1 ),
			),
			'portfolio_label_singular_name' => array(
				'title' => __( 'Portfolio Page', 'us' ),
				'title_pos' => 'side',
				'std' => __( 'Portfolio Page', 'us' ),
				'type' => 'text',
				'classes' => 'for_above',
				'show_if' => array( 'portfolio_rename', '=', 1 ),
			),
			'portfolio_label_add_new' => array(
				'title' => __( 'Add Portfolio Page', 'us' ),
				'title_pos' => 'side',
				'std' => __( 'Add Portfolio Page', 'us' ),
				'type' => 'text',
				'classes' => 'for_above',
				'show_if' => array( 'portfolio_rename', '=', 1 ),
			),
			'portfolio_label_edit_item' => array(
				'title' => __( 'Edit Portfolio Page', 'us' ),
				'title_pos' => 'side',
				'std' => __( 'Edit Portfolio Page', 'us' ),
				'type' => 'text',
				'classes' => 'for_above',
				'show_if' => array( 'portfolio_rename', '=', 1 ),
			),
			'portfolio_label_category' => array(
				'title' => __( 'Portfolio Categories', 'us' ),
				'title_pos' => 'side',
				'std' => __( 'Portfolio Categories', 'us' ),
				'type' => 'text',
				'classes' => 'for_above',
				'show_if' => array( 'portfolio_rename', '=', 1 ),
			),
			'portfolio_label_tag' => array(
				'title' => __( 'Portfolio Tags', 'us' ),
				'title_pos' => 'side',
				'std' => __( 'Portfolio Tags', 'us' ),
				'type' => 'text',
				'classes' => 'for_above',
				'show_if' => array( 'portfolio_rename', '=', 1 ),
			),
		),
	),

	// Shop
	'woocommerce' => array(
		'title' => us_translate_x( 'Shop', 'Page title', 'woocommerce' ),
		'place_if' => class_exists( 'woocommerce' ),
		'fields' => array_merge(
			array(

				// Global Settings
				'h_more' => array(
					'title' => us_translate( 'Global Settings' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'shop_catalog' => array(
					'title' => __( 'Catalog Mode', 'us' ),
					'title_pos' => 'side',
					'type' => 'switch',
					'switch_text' => sprintf( __( 'Remove "%s" buttons', 'us' ), us_translate( 'Add to cart', 'woocommerce' ) ),
					'std' => 0,
				),
				'shop_primary_btn_style' => array(
					'title' => __( 'Primary Buttons Style', 'us' ),
					'title_pos' => 'side',
					'description' => '<a href="' . admin_url() . 'admin.php?page=us-theme-options#buttons">' . __( 'Edit Button Styles', 'us' ) . '</a>',
					'type' => 'select',
					'options' => us_get_btn_styles(),
					'std' => '1',
				),
				'shop_secondary_btn_style' => array(
					'title' => __( 'Secondary Buttons Style', 'us' ),
					'title_pos' => 'side',
					'description' => '<a href="' . admin_url() . 'admin.php?page=us-theme-options#buttons">' . __( 'Edit Button Styles', 'us' ) . '</a>',
					'type' => 'select',
					'options' => us_get_btn_styles(),
					'std' => '2',
				),

				// Product gallery
				'product_gallery' => array(
					'title' => us_translate( 'Product gallery', 'woocommerce' ),
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'slider' => __( 'Slider', 'us' ),
						'gallery' => us_translate( 'Gallery' ),
					),
					'std' => 'slider',
				),
				'wrapper_product_gallery_start' => array(
					'type' => 'wrapper_start',
					'classes' => 'force_right',
				),
				'product_gallery_thumbs_pos' => array(
					'title' => __( 'Thumbnails Position', 'us' ),
					'type' => 'radio',
					'options' => array(
						'bottom' => us_translate( 'Bottom' ),
						'left' => us_translate( 'Left' ),
					),
					'std' => 'bottom',
					'show_if' => array( 'product_gallery', '=', 'slider' ),
				),
				'product_gallery_thumbs_cols' => array(
					'title' => us_translate( 'Columns' ),
					'type' => 'radio',
					'options' => array(
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
					),
					'std' => '4',
					'show_if' => array(
						array( 'product_gallery', '=', 'slider' ),
						'and',
						array( 'product_gallery_thumbs_pos', '=', 'bottom' ),
					),
				),
				'product_gallery_thumbs_width' => array(
					'title' => __( 'Thumbnails Width', 'us' ),
					'type' => 'slider',
					'options' => array(
						'px' => array(
							'min' => 40,
							'max' => 200,
						),
						'rem' => array(
							'min' => 3,
							'max' => 15,
							'step' => 0.1,
						),
					),
					'std' => '6rem',
					'show_if' => array(
						array( 'product_gallery', '=', 'slider' ),
						'and',
						array( 'product_gallery_thumbs_pos', '=', array( 'left', 'right' ) ),
					),
				),
				'product_gallery_thumbs_gap' => array(
					'title' => __( 'Gap between Thumbnails', 'us' ),
					'type' => 'slider',
					'options' => array(
						'px' => array(
							'min' => 0,
							'max' => 20,
						),
					),
					'std' => '4px',
					'show_if' => array( 'product_gallery', '=', 'slider' ),
				),
				'product_gallery_options' => array(
					'type' => 'checkboxes',
					'options' => array(
						'zoom' => __( 'Zoom images on hover', 'us' ),
						'lightbox' => __( 'Allow Full Screen view', 'us' ),
					),
					'std' => 'zoom,lightbox',
					'classes' => 'vertical',
				),
				'wrapper_product_gallery_end' => array(
					'type' => 'wrapper_end',
				),

				// Products
				'h_product' => array(
					'title' => us_translate( 'Products', 'woocommerce' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'header_product_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_header',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_headers_list
					),
					'std' => '__defaults__',
				),
				'titlebar_product_id' => array(
					'title' => __( 'Titlebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
					'place_if' => $usof_sidebar_titlebar,
				),
				'content_product_id' => array(
					'title' => __( 'Content template', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'' => '&ndash; ' . __( 'Default WooCommerce template', 'us' ) . ' &ndash;',
						), $us_content_templates_list
					),
					'std' => '',
				),
				'sidebar_product_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $sidebars_list
					),
					'std' => '__defaults__',
					'hints_for' => $sidebar_hints_for,
					'place_if' => $usof_sidebar_titlebar,
				),
				'sidebar_product_pos' => array(
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'classes' => 'for_above',
					'show_if' => array( 'sidebar_product_id', '!=', array( '', '__defaults__' ) ),
					'place_if' => $usof_sidebar_titlebar,
				),
				'footer_product_id' => array(
					'title' => __( 'Footer', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
				),

				// Shop page
				'h_shop' => array(
					'title' => us_translate( 'Shop Page', 'woocommerce' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'header_shop_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_header',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_headers_list
					),
					'std' => '__defaults__',
				),
				'titlebar_shop_id' => array(
					'title' => __( 'Titlebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
					'place_if' => $usof_sidebar_titlebar,
				),
				'content_shop_id' => array(
					'title' => __( 'Content template', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'' => '&ndash; ' . __( 'Default WooCommerce template', 'us' ) . ' &ndash;',
						), $us_content_templates_list
					),
					'std' => '',
				),
				'wrapper_shop_start' => array(
					'type' => 'wrapper_start',
					'classes' => 'force_right',
					'show_if' => array( 'content_shop_id', '=', '' ),
				),
				'shop_columns' => array(
					'title' => us_translate( 'Columns' ),
					'type' => 'radio',
					'options' => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'std' => '3',
				),
				'wrapper_shop_end' => array(
					'type' => 'wrapper_end',
				),
				'sidebar_shop_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $sidebars_list
					),
					'std' => '__defaults__',
					'hints_for' => $sidebar_hints_for,
					'place_if' => $usof_sidebar_titlebar,
				),
				'sidebar_shop_pos' => array(
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'classes' => 'for_above',
					'show_if' => array( 'sidebar_shop_id', '!=', array( '', '__defaults__' ) ),
					'place_if' => $usof_sidebar_titlebar,
				),
				'footer_shop_id' => array(
					'title' => __( 'Footer', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
				),

			), $shop_layout_config, array(

				// Cart page
				'h_cart' => array(
					'title' => us_translate( 'Cart Page', 'woocommerce' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'shop_cart' => array(
					'title' => __( 'Layout', 'us' ),
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'standard' => __( 'Standard', 'us' ),
						'compact' => __( 'Compact', 'us' ),
					),
					'std' => 'compact',
				),
				'product_related_qty' => array(
					'title' => us_translate( 'Cross-sells', 'woocommerce' ),
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'std' => '3',
				),
			)

		),
	),

	// Icons
	'icons' => array(
		'title' => __( 'Icons', 'us' ),
		'fields' => array_merge(
			array(
				'used_icons_info' => array(
					'button_text' => __( 'Show used icons', 'us' ),
					'type' => 'used_icons_info',
					'classes' => 'desc_4',
				),
				'h_icons_2' => array(
					'title' => __( 'Icon Sets', 'us' ),
					'description' => __( 'If "None" is selected, the corresponding icon set won\'t load font files and won\'t appear in the icon selection of elements settings.', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
				),
			),
			$icon_sets_config,
			array(
				'fallback_icon_font' => array(
					'title' => __( 'Fallback icon font', 'us' ),
					'description' => '<a href="' . $help_portal_url . '/' . strtolower( US_THEMENAME ) . '/general/icons/#fallback-icon-font" target="_blank">' . __( 'Learn more', 'us' ) . '</a>',
					'type' => 'switch',
					'switch_text' => __( 'Use fallback icon font for theme UI controls', 'us' ),
					'std' => 1,
					'classes' => 'desc_3',
					'place_if' => ( US_THEMENAME === 'Impreza' ), // fallback icon font exists in Impreza only
					'show_if' => array(
						array( 'icons_fas', '!=', 'default' ),
						'and',
						array( 'icons_far', '!=', 'default' ),
						'and',
						array( 'icons_fal', '!=', 'default' ),
					),
				),
			)
		),
	),

	// Image Sizes
	'image_sizes' => array(
		'title' => us_translate( 'Image sizes' ),
		'fields' => array(

			'img_size_info' => array(
				'description' => $img_size_info,
				'type' => 'message',
				'classes' => 'color_blue for_above',
			),

			'h_image_sizes' => array(
				'title' => __( 'Additional Image Sizes', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'img_size' => array(
				'type' => 'group',
				'is_accordion' => FALSE,
				'is_duplicate' => FALSE,
				'show_controls' => TRUE,
				'classes' => 'for_inline',
				'params' => array(
					'width' => array(
						'title' => us_translate( 'Max Width' ),
						'type' => 'slider',
						'std' => '600px',
						'options' => array(
							'px' => array(
								'min' => 0,
								'max' => 1000,
							),
						),
						'classes' => 'inline slider_below',
					),
					'height' => array(
						'title' => us_translate( 'Max Height' ),
						'type' => 'slider',
						'std' => '400px',
						'options' => array(
							'px' => array(
								'min' => 0,
								'max' => 1000,
							),
						),
						'classes' => 'inline slider_below',
					),
					'crop' => array(
						'type' => 'checkboxes',
						'options' => array(
							'crop' => __( 'Crop to exact dimensions', 'us' ),
						),
						'std' => '',
						'classes' => 'inline',
					),
				),
				'std' => array(),
			),

			'h_more_options' => array(
				'title' => __( 'More Options', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'big_image_size_threshold' => array(
				'title' => __( 'Big Image Size Threshold', 'us' ),
				'title_pos' => 'side',
				'description' => sprintf( __( 'If an image height or width is above this threshold, it will be scaled down and used as the "%s".', 'us' ), us_translate( 'Full Size' ) ) . '<br><br><strong>' . __( 'Set "0px" to disable threshold.', 'us' ) . '</strong><br><br>' . sprintf( __( 'It is a built-in WordPress feature described in %sthe article%s.', 'us' ), '<a target="blank" href="https://make.wordpress.org/core/2019/10/09/introducing-handling-of-big-images-in-wordpress-5-3/">', '</a>' ),
				'type' => 'slider',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 4000,
						'step' => 20,
					),
				),
				'std' => '2560px',
				'classes' => 'desc_3',
			),
			'delete_unused_images' => array(
				'title' => __( 'Unused Thumbnails', 'us' ),
				'title_pos' => 'side',
				'description' => __( 'When this option is ON, all the thumbnails of non-registered image sizes are deleted.', 'us' ) . ' ' . __( 'It helps keep free space in your storage.', 'us' ),
				'type' => 'switch',
				'switch_text' => __( 'Delete unused image thumbnails', 'us' ),
				'std' => 0,
				'classes' => 'desc_3',
			),
		),
	),

	// Advanced
	'advanced' => array(
		'title' => _x( 'Advanced', 'Advanced Settings', 'us' ),
		'fields' => array(
			'h_advanced_1' => array(
				'title' => __( 'Theme Modules', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'live_builder' => array(
				'type' => 'switch',
				'switch_text' => '"Live Builder"', // do not translate for a while
				'description' => US_THEMENAME . ' 8 introduces a new live builder, which allows to edit pages on the front end. This should save you a lot of time. Try edit pages via green "Edit Live" button.',
				'std' => 1,
				'classes' => 'desc_2',
			),
			'grid_columns_layout' => array(
				'type' => 'switch',
				'switch_text' => __( 'Columns Layout via CSS grid', 'us' ),
				'std' => 1,
				'show_if' => array( 'live_builder', '=', '1' ),
				'classes' => 'for_above',
			),
			'block_editor' => array(
				'type' => 'switch',
				'switch_text' => __( 'Gutenberg (block editor)', 'us' ),
				'std' => 0,
				'classes' => 'for_above',
			),
			'enable_sidebar_titlebar' => array(
				'type' => 'switch',
				'switch_text' => __( 'Titlebars & Sidebars', 'us' ),
				'std' => 0,
				'classes' => 'for_above',
			),
			'enable_page_blocks_for_sidebars' => array(
				'type' => 'switch',
				'switch_text' => __( 'Use Page Blocks for Sidebars', 'us' ),
				'std' => 0,
				'classes' => 'for_above',
				'show_if' => array( 'enable_sidebar_titlebar', '=', TRUE ),
			),
			'enable_portfolio' => array(
				'type' => 'switch',
				'switch_text' => __( 'Portfolio', 'us' ) . $renamed_portfolio_label,
				'std' => 1,
				'classes' => 'for_above',
			),
			'enable_testimonials' => array(
				'type' => 'switch',
				'switch_text' => __( 'Testimonials', 'us' ),
				'std' => 1,
				'classes' => 'for_above',
			),
			'media_category' => array(
				'type' => 'switch',
				'switch_text' => __( 'Media Categories', 'us' ),
				'std' => 1,
				'classes' => 'for_above',
			),
			'og_enabled' => array(
				'type' => 'switch',
				'switch_text' => __( 'SEO meta tags', 'us' ),
				'description' => '<a href="' . $help_portal_url . '/' . strtolower( US_THEMENAME ) . '/general/seo/" target="_blank">' . __( 'Learn more', 'us' ) . '</a>.<br>' . __( 'If you\'re using any SEO plugin, turn OFF this option to avoid conflicts.', 'us' ),
				'std' => 1,
				'classes' => 'desc_2 for_above',
			),
			'schema_markup' => array(
				'type' => 'switch',
				'switch_text' => __( 'Schema.org markup', 'us' ),
				'std' => 1,
				'classes' => 'for_above',
			),
			'schema_faqs_page' => array(
				'title' => __( 'FAQs page', 'us' ),
				'description' => sprintf( __( 'The selected page must contain a "%s" element.', 'us' ), us_translate( 'Accordion', 'js_composer' ) ) . ' <a href="https://developers.google.com/search/docs/data-types/faqpage" target="_blank">' . __( 'Learn more', 'us' ) . '</a>',
				'type' => 'select',
				'options' => us_array_merge(
					array( '' => sprintf( '&ndash; %s &ndash;', us_translate( 'None' ) ) ),
					$us_page_list
				),
				'std' => '',
				'hints_for' => 'page',
				'show_if' => array( 'schema_markup', '=', '1' ),
				'classes' => 'for_above desc_4',
			),

			// Global Values
			'h_advanced_2' => array(
				'title' => __( 'Global Values', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'gmaps_api_key' => array(
				'title' => __( 'Google Maps API Key', 'us' ),
				'title_pos' => 'side',
				'description' => '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank" rel="noopener">' . strip_tags( __( 'Get API key', 'us' ) ) . '</a>',
				'type' => 'text',
				'std' => '',
				'classes' => 'desc_3',
			),
			'facebook_app_id' => array(
				'title' => __( 'Facebook Application ID', 'us' ),
				'title_pos' => 'side',
				'description' => __( 'Required for Sharing Buttons on AMP version of website.', 'us' ) . ' <a href="https://developers.facebook.com/apps" target="_blank" rel="noopener">developers.facebook.com</a>',
				'type' => 'text',
				'std' => '',
				'classes' => 'desc_3',
				'place_if' => function_exists( 'amp_is_request' ),
			),
			'grid_filter_url_prefix' => array(
				'title' => __( 'Grid Filter URL prefix', 'us' ),
				'title_pos' => 'side',
				'type' => 'text',
				'placeholder' => 'filter',
				'std' => '',
			),
			'grid_order_url_prefix' => array(
				'title' => __( 'Grid Order URL prefix', 'us' ),
				'title_pos' => 'side',
				'type' => 'text',
				'placeholder' => 'order',
				'std' => '',
			),

			// Website Performance
			'h_advanced_3' => array(
				'title' => __( 'Website Performance', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'keep_url_protocol' => array(
				'type' => 'switch',
				'switch_text' => __( 'Keep "http/https" in the paths to files', 'us' ),
				'description' => __( 'If your site uses both "HTTP" and "HTTPS" and has some appearance issues, turn OFF this option.', 'us' ),
				'std' => TRUE,
				'classes' => 'desc_2 for_above',
			),
			'disable_jquery_migrate' => array(
				'type' => 'switch',
				'switch_text' => __( 'Disable jQuery migrate script', 'us' ),
				'description' => __( 'When this option is ON, "jquery-migrate.min.js" file won\'t be loaded on the front end.', 'us' ) . ' ' . __( 'This will improve page loading speed.', 'us' ),
				'std' => TRUE,
				'classes' => 'desc_2 for_above',
			),
			'jquery_footer' => array(
				'type' => 'switch',
				'switch_text' => __( 'Move jQuery scripts to the footer', 'us' ),
				'description' => __( 'When this option is ON, jQuery library files will be loaded after the page content.', 'us' ) . ' ' . __( 'This will improve page loading speed.', 'us' ),
				'std' => TRUE,
				'classes' => 'desc_2 for_above',
			),
			'ajax_load_js' => array(
				'type' => 'switch',
				'switch_text' => __( 'Dynamically load theme JS components', 'us' ),
				'description' => __( 'When this option is ON, theme components JS files will be loaded dynamically without additional external requests.', 'us' ) . ' ' . __( 'This will improve page loading speed.', 'us' ),
				'std' => TRUE,
				'classes' => 'desc_2 for_above',
			),
			'disable_extra_vc' => array(
				'type' => 'switch',
				'switch_text' => __( 'Disable extra features of WPBakery Page Builder', 'us' ),
				'description' => __( 'When this option is ON, the original CSS and JS files of WPBakery Page Builder won\'t be loaded on the front end.', 'us' ) . ' ' . __( 'This will improve page loading speed.', 'us' ),
				'std' => TRUE,
				'place_if' => class_exists( 'Vc_Manager' ),
				'classes' => 'desc_2 for_above',
			),

			'optimize_assets' => array(
				'type' => 'switch',
				'switch_text' => __( 'Optimize JS and CSS size', 'us' ),
				'description' => __( 'When this option is ON, your site will compress scripts to a single JS file and compress styles to a single CSS file. You can disable unused components to reduce their sizes.', 'us' ) . ' ' . __( 'This will improve page loading speed.', 'us' ),
				'std' => FALSE,
				'classes' => 'desc_2 for_above',
				'disabled' => $upload_dir_not_writable,
			),
			'optimize_assets_alert' => array(
				'description' => __( 'Your uploads folder is not writable. Change your server permissions to use this option.', 'us' ),
				'type' => 'message',
				'classes' => 'string',
				'place_if' => $upload_dir_not_writable,
			),
			'optimize_assets_start' => array(
				'type' => 'wrapper_start',
				'show_if' => array( 'optimize_assets', '=', TRUE ),
			),
			'assets' => array(
				'type' => 'check_table',
				'show_auto_optimize_button' => TRUE,
				'options' => $usof_assets,
				'std' => $usof_assets_std,
				'classes' => 'desc_4',
			),
			'optimize_assets_end' => array(
				'type' => 'wrapper_end',
			),
			'include_gfonts_css' => array(
				'type' => 'switch',
				'switch_text' => __( 'Merge Google Fonts styles into single CSS file', 'us' ),
				'description' => __( 'When this option is ON, Google Fonts CSS file won\'t be loaded separately.', 'us' ) . ' ' . __( 'This will improve page loading speed.', 'us' ), // TODO: describe better
				'std' => FALSE,
				'classes' => 'desc_2',
				'show_if' => array( 'optimize_assets', '=', TRUE ),
			),

		),
	),

	// Custom Code
	'code' => array(
		'title' => __( 'Custom Code', 'us' ),
		'fields' => array(
			'custom_css' => array(
				'title' => __( 'Custom CSS', 'us' ),
				'description' => sprintf( __( 'CSS code from this field will overwrite theme styles. It will be located inside the %s tags just before the %s tag of every site page.', 'us' ), '<code>&lt;style&gt;&lt;/style&gt;</code>', '<code>&lt;/head&gt;</code>' ),
				'type' => 'css',
				'classes' => 'desc_4',
			),
			'custom_html_head' => array(
				'title' => sprintf( __( 'Code before %s', 'us' ), '&lt;/head&gt;' ),
				'description' => sprintf( __( 'Use this field for Google Analytics code or other tracking code. If you paste custom JavaScript, use it inside the %s tags.', 'us' ), '<code>&lt;script&gt;&lt;/script&gt;</code>' ) . '<br><br>' . sprintf( __( 'Content from this field will be located just before the %s tag of every site page.', 'us' ), '<code>&lt;/head&gt;</code>' ),
				'type' => 'html',
				'classes' => 'desc_4',
			),
			'custom_html' => array(
				'title' => sprintf( __( 'Code before %s', 'us' ), '&lt;/body&gt;' ),
				'description' => sprintf( __( 'Use this field for Google Analytics code or other tracking code. If you paste custom JavaScript, use it inside the %s tags.', 'us' ), '<code>&lt;script&gt;&lt;/script&gt;</code>' ) . '<br><br>' . sprintf( __( 'Content from this field will be located just before the %s tag of every site page.', 'us' ), '<code>&lt;/body&gt;</code>' ),
				'type' => 'html',
				'classes' => 'desc_4',
			),
		),
	),

	'manage' => array(
		'title' => __( 'Manage Options', 'us' ),
		'fields' => array(
			'of_reset' => array(
				'title' => __( 'Reset Theme Options', 'us' ),
				'title_pos' => 'side',
				'type' => 'reset',
			),
			'of_backup' => array(
				'title' => __( 'Backup Theme Options', 'us' ),
				'title_pos' => 'side',
				'type' => 'backup',
			),
			'of_transfer' => array(
				'title' => __( 'Transfer Theme Options', 'us' ),
				'title_pos' => 'side',
				'type' => 'transfer',
				'description' => __( 'You can transfer the saved options data between different installations by copying the text inside the text box. To import data from another installation, replace the data in the text box with the one from another installation and click "Import Options".', 'us' ),
				'classes' => 'desc_3',
			),
		),
	),

	'white_label' => $white_label_config,
);
