<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Map', 'us' ),
	'icon' => 'icon-wpb-map-pin',
	'params' => array_merge( array(

		'marker_address' => array(
			'title' => __( 'Address', 'us' ),
			'description' => __( 'Specify address in accordance with the format used by the national postal service of the country concerned.', 'us' ) . ' ' . sprintf( __( 'Or use geo coordinates, for example: %s', 'us' ), '38.6774156, 34.8520661' ),
			'type' => 'text',
			'std' => '1600 Amphitheatre Parkway, Mountain View, CA 94043, United States',
			'holder' => 'div',
		),
		'marker_text' => array(
			'title' => __( 'Marker Text', 'us' ),
			'description' => __( 'HTML tags are allowed.', 'us' ),
			'type' => 'html',
			'encoded' => TRUE,
			'std' => base64_encode( '<h6>Hey, we are here!</h6><p>We will be glad to see you in our office.</p>' ),
			'classes' => 'vc_col-sm-12 pretend_textfield', // appearance fix in shortcode editing window
		),
		'show_infowindow' => array(
			'type' => 'switch',
			'switch_text' => __( 'Show Marker Text when map is loaded', 'us' ),
			'std' => FALSE,
			'show_if' => array( 'marker_text', '!=', '' ),
		),
		'custom_marker_img' => array(
			'title' => __( 'Custom Marker Image', 'us' ),
			'type' => 'upload',
			'cols' => 2,
			'extension' => 'png,jpg,jpeg,gif,svg',
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
		),

		// Additional Markers
		'markers' => array(
			'type' => 'group',
			'show_controls' => TRUE,
			'std' => array(),
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
			'group' => __( 'Additional Markers', 'us' ),
		),

		// More Options
		'provider' => array(
			'title' => __( 'Map Provider', 'us' ),
			'type' => 'select',
			'options' => array(
				'google' => __( 'Google Maps', 'us' ),
				'osm' => 'OpenStreetMap',
			),
			'std' => 'google',
			'group' => us_translate( 'Appearance' ),
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
		),
		'hide_controls' => array(
			'type' => 'switch',
			'switch_text' => __( 'Hide all map controls', 'us' ),
			'std' => FALSE,
			'group' => us_translate( 'Appearance' ),
		),
		'disable_zoom' => array(
			'type' => 'switch',
			'switch_text' => __( 'Disable map zoom on mouse wheel scroll', 'us' ),
			'std' => FALSE,
			'group' => us_translate( 'Appearance' ),
		),
		'disable_dragging' => array(
			'type' => 'switch',
			'switch_text' => __( 'Disable dragging on touch screens', 'us' ),
			'std' => FALSE,
			'group' => us_translate( 'Appearance' ),
		),
		'map_style_json' => array(
			'title' => __( 'Map Style', 'us' ),
			'description' => sprintf( __( 'Check available styles on %s.', 'us' ), '<a href="https://snazzymaps.com/" target="_blank" rel="noopener">snazzymaps.com</a>' ),
			'type' => 'html',
			'std' => '',
			'show_if' => array( 'provider', '=', 'google' ),
			'group' => us_translate( 'Appearance' ),
		),
		'layer_style' => array(
			'title' => __( 'Map Style', 'us' ),
			'description' => sprintf( __( 'Check available styles on %s.', 'us' ), '<a href="https://leaflet-extras.github.io/leaflet-providers/preview/" target="_blank" rel="noopener">Leaflet Provider Demo</a>' ) . ' ' . sprintf( __( 'Example: %s', 'us' ), 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png' ),
			'type' => 'text',
			'std' => '',
			'show_if' => array( 'provider', '=', 'osm' ),
			'group' => us_translate( 'Appearance' ),
		),

	), $design_options ),
);
