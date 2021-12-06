<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

global $pagenow;

/**
 * Configuration for shortcode: gmaps
 */

$design_options_params = us_config( 'elements_design_options' );

// Get options from "Advanced Custom Fields" plugin
$acf_custom_fields = array();
if (
	is_admin()
	AND (
		wp_doing_ajax()
		OR in_array( $pagenow, array( 'post.php', 'post-new.php' ) )
		OR usb_is_builder_page()
	)
	AND function_exists( 'acf_get_field_groups' )
	AND $acf_groups = acf_get_field_groups()
) {
	foreach ( $acf_groups as $group ) {
		foreach ( acf_get_fields( $group['ID'] ) as $field ) {

			// Get Google Maps fields
			if ( $field['type'] === 'google_map' ) {
				$acf_custom_fields[ $field['name'] ] = $group['title'] . ': ' . $field['label'];
			}
		}
	}
}

/**
 * @return array
 */
return array(
	'title' => __( 'Map', 'us' ),
	'icon' => 'fas fa-map-marked-alt',
	'params' => us_set_params_weight(

		// General section
		array(
			'source' => array(
				'title' => __( 'Address', 'us' ),
				'type' => 'select',
				'options' => array_merge(
					$acf_custom_fields,
					array( 'custom' => us_translate( 'Custom' ) )
				),
				'std' => 'custom',
				'usb_preview' => TRUE,
			),
			'marker_address' => array(
				'description' => __( 'Specify address in accordance with the format used by the national postal service of the country concerned.', 'us' ) . ' ' . __( 'Or use geo coordinates, for example:', 'us' ) . ' <span class="usof-example">38.6774156, 34.8520661</span>',
				'type' => 'text',
				'std' => '1600 Amphitheatre Parkway, Mountain View, CA 94043, United States',
				'holder' => 'div',
				'classes' => 'for_above',
				'show_if' => array( 'source', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'marker_text' => array(
				'title' => __( 'Marker Text', 'us' ),
				'description' => __( 'HTML tags are allowed.', 'us' ) . ' ' . sprintf( __( 'Use %s to show the address value.', 'us' ), '<span class="usof-example">{{address}}</span>' ),
				'type' => 'html',
				'encoded' => TRUE,
				'std' => base64_encode( '<h6>Hey, we are here!</h6><p>We will be glad to see you in our office.</p>' ),
				'classes' => 'vc_col-sm-12 pretend_textfield', // appearance fix in WPBakery editing window
				'usb_preview' => TRUE,
			),
			'show_infowindow' => array(
				'type' => 'switch',
				'switch_text' => __( 'Show Marker Text when map is loaded', 'us' ),
				'std' => FALSE,
				'show_if' => array( 'marker_text', '!=', '' ),
				'usb_preview' => TRUE,
			),
			'custom_marker_img' => array(
				'title' => __( 'Custom Marker Image', 'us' ),
				'type' => 'upload',
				'cols' => 2,
				'extension' => 'png,jpg,jpeg,gif,svg',
				'usb_preview' => TRUE,
			),
			'custom_marker_size' => array(
				'title' => __( 'Marker Image Size', 'us' ),
				'type' => 'select',
				'options' => array(
					'20' => '20px',
					'30' => '30px',
					'40' => '40px',
					'50' => '50px',
					'60' => '60px',
					'70' => '70px',
					'80' => '80px',
				),
				'std' => '30',
				'show_if' => array( 'custom_marker_img', '!=', '' ),
				'cols' => 2,
				'usb_preview' => TRUE,
			),

			// Additional Markers
			'markers' => array(
				'type' => 'group',
				'show_controls' => TRUE,
				'is_accordion' => TRUE,
				'accordion_title' => 'marker_address',
				'params' => array(
					'marker_address' => array(
						'title' => __( 'Address', 'us' ),
						'description' => __( 'Specify address in accordance with the format used by the national postal service of the country concerned.', 'us' ) . ' ' . sprintf( __( 'Or use geo coordinates, for example: %s', 'us' ), '38.6774156, 34.8520661' ),
						'type' => 'text',
						'std' => '',
						'admin_label' => TRUE,
					),
					'marker_text' => array(
						'title' => __( 'Marker Text', 'us' ),
						'description' => __( 'HTML tags are allowed.', 'us' ),
						'type' => 'textarea',
						'std' => '',
						'classes' => 'vc_col-sm-12 pretend_textfield', // appearance fix in shortcode editing window
					),
					'marker_img' => array(
						'title' => __( 'Custom Marker Image', 'us' ),
						'type' => 'upload',
						'cols' => 2,
						'extension' => 'png,jpg,jpeg,gif,svg',
					),
					'marker_size' => array(
						'title' => __( 'Marker Image Size', 'us' ),
						'type' => 'select',
						'options' => array(
							'20' => '20px',
							'30' => '30px',
							'40' => '40px',
							'50' => '50px',
							'60' => '60px',
							'70' => '70px',
							'80' => '80px',
						),
						'std' => '30',
						'show_if' => array( 'marker_img', '!=', '' ),
						'cols' => 2,
					),
				),
				'std' => array(),
				'group' => __( 'Additional Markers', 'us' ),
				'usb_preview' => TRUE,
			),
		),

		// More options section
		array(
			'provider' => array(
				'title' => __( 'Map Provider', 'us' ),
				'type' => 'radio',
				'options' => array(
					'google' => __( 'Google Maps', 'us' ),
					'osm' => 'OpenStreetMap',
				),
				'std' => 'google',
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'zoom' => array(
				'title' => __( 'Map Zoom', 'us' ),
				'type' => 'select',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
					'13' => '13',
					'14' => '14',
					'15' => '15',
					'16' => '16',
					'17' => '17',
					'18' => '18',
					'19' => '19',
					'20' => '20',
				),
				'std' => '14',
				'cols' => 2,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'type' => array(
				'title' => __( 'Map Type', 'us' ),
				'type' => 'select',
				'options' => array(
					'roadmap' => __( 'Roadmap', 'us' ),
					'terrain' => __( 'Roadmap + Terrain', 'us' ),
					'satellite' => __( 'Satellite', 'us' ),
					'hybrid' => __( 'Satellite + Roadmap', 'us' ),
				),
				'std' => 'roadmap',
				'cols' => 2,
				'show_if' => array( 'provider', '=', 'google' ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'hide_controls' => array(
				'type' => 'switch',
				'switch_text' => __( 'Hide all map controls', 'us' ),
				'std' => FALSE,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'disable_zoom' => array(
				'type' => 'switch',
				'switch_text' => __( 'Disable map zoom on mouse wheel scroll', 'us' ),
				'std' => FALSE,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'disable_dragging' => array(
				'type' => 'switch',
				'switch_text' => __( 'Disable dragging on touch screens', 'us' ),
				'std' => FALSE,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'map_style_json' => array(
				'title' => __( 'Map Style', 'us' ),
				'description' => sprintf( __( 'Check available styles on %s.', 'us' ), '<a href="https://snazzymaps.com/" target="_blank" rel="noopener">snazzymaps.com</a>' ),
				'type' => 'html',
				'std' => '',
				'show_if' => array( 'provider', '=', 'google' ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'layer_style' => array(
				'title' => __( 'Map Style', 'us' ),
				'description' => sprintf( __( 'Check available styles on %s.', 'us' ), '<a href="https://leaflet-extras.github.io/leaflet-providers/preview/" target="_blank" rel="noopener">Leaflet Provider Demo</a>' ) . ' ' . sprintf( __( 'Example: %s', 'us' ), 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png' ),
				'type' => 'text',
				'std' => '',
				'show_if' => array( 'provider', '=', 'osm' ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
		),

		$design_options_params
	),


	'usb_init_js' => '$elm.filter( \'.w-map.provider_google\' ).wMapsWithPreload(); $elm.filter( \'.w-map.provider_osm\' ).WLmapsWithPreload()',
);
