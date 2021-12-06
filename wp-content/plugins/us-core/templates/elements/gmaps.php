<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_gmaps
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @param  $marker_address             string Marker 1 address
 * @param  $marker_text                string Marker 1 text
 * @param  $show_infowindow            bool Show Marker's InfoWindow
 * @param  $custom_marker_img          int Custom marker image (from WordPress media)
 * @param  $custom_marker_size         int Custom marker size
 * @param  $markers                    array Additional Markers
 * @param  $provider                   string Map Provider: 'google' / 'osm'
 * @param  $type                       string Map type: 'roadmap' / 'satellite' / 'hybrid' / 'terrain'
 * @param  $height                     int Map height
 * @param  $zoom                       int Map zoom
 * @param  $hide_controls              bool Hide all map controls
 * @param  $disable_dragging           bool Disable dragging on touch screens
 * @param  $disable_zoom               bool Disable map zoom on mouse wheel scroll
 * @param  $map_bg_color               string Map Background Color
 * @param  $el_class                   string Extra class name
 * @param  $map_style_json             string Map Style
 * @param  $layer_style                string Leaflet Map TileLayer
 *
 * @filter 'us_maps_js_options' Allows to filter options, passed to JavaScript
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 */

$_atts['class'] = 'w-map provider_' . $provider;
$_atts['class'] .= isset( $classes ) ? $classes : '';

// When some values are set in Design options, add the specific classes
if ( us_design_options_has_property( $css, 'font-size' ) ) {
	$_atts['class'] .= ' has_font_size';
}

// Set unique map ID
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
} elseif (
	usb_is_preview_page()
	OR ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() )
) {
	$_atts['id'] = us_uniqid();
} else {
	global $us_maps_index;
	$us_maps_index = isset( $us_maps_index ) ? ( $us_maps_index + 1 ) : 1;
	$_atts['id'] = 'us_map_' . $us_maps_index;
}

// Generate specific HTML for AMP version
if ( us_amp() ) {
	$_atts['src'] = '';
	$_atts['width'] = '1000';
	$_atts['height'] = '400';
	$_atts['layout'] = 'responsive';
	$_atts['sandbox'] = 'allow-scripts allow-same-origin allow-popups';
	$_atts['frameborder'] = '0';

	if ( $provider == 'google' ) {

		// URL encode for embed compatibility
		$marker_address = urlencode( strip_tags( $marker_address ) );
		$_atts['src'] = 'https://www.google.com/maps/embed/v1/place?key=' . us_get_option( 'gmaps_api_key', '' ) . '&q=' . $marker_address . '&zoom=' . $zoom;

	} elseif (
		$provider == 'osm'
		AND preg_match( '#([0-9]+.[0-9]+),(\s)?([0-9]+.[0-9]+)#', $marker_address, $matches ) // make sure we have coordinates, not address
	) {
		$_atts['src'] = 'https://www.openstreetmap.org/export/embed.html?bbox=' . us_map_get_bbox( $matches[1], $matches[3], $zoom ) . '&marker=' . $matches[1] . ',' . $matches[3] . '&layer=mapnik';
	}

	echo '<amp-iframe' . us_implode_atts( $_atts ) . '>';

	// Placeholder image required for case when map is too close to the top of the page
	echo '<amp-img layout="fill" src="' . us_get_img_placeholder( 'full', TRUE ) . '" placeholder></amp-img>';
	echo '</amp-iframe>';

	return;
}

// Get address from specified ACF field
if (
	$source !== 'custom'
	AND function_exists( 'get_field' )
) {
	if ( $acf_map_field = get_field( $source ) ) {
		$marker_address = $acf_map_field['address'];
	} elseif ( ! usb_is_preview_page() ) {
		// Don't output the element if custom field has no value
		return;
	}

	// Apply filters to address
} else {
	$marker_address = us_replace_dynamic_value( $marker_address, 'any' );
}

if ( ! empty( $marker_text ) ) {

	// Decode base64-encoded HTML attributes
	$marker_text = rawurldecode( base64_decode( $marker_text ) );

	// Apply dynamic values, if set
	$marker_text = str_replace( '{{address}}', $marker_address, $marker_text );
	$marker_text = us_replace_dynamic_value( $marker_text, 'any' );
	$marker_text = do_shortcode( $marker_text );
}

// Form all options needed for JS API
$map_options = array(
	'address' => $marker_address,
	'zoom' => (int) $zoom,
	'hideControls' => (int) $hide_controls,
	'disableZoom' => (int) $disable_zoom,
	'disableDragging' => (int) $disable_dragging,
	'markers' => array(
		array(
			'address' => $marker_address,
			'html' => $marker_text,
			'infowindow' => $show_infowindow,
		),
	),
);

// Additional markers
if ( empty( $markers ) ) {
	$markers = array();
} else {
	$markers = json_decode( urldecode( $markers ), TRUE );
	if ( ! is_array( $markers ) ) {
		$markers = array();
	}
}

foreach ( $markers as $index => $marker ) {
	/**
	 * Filter the included markers
	 *
	 * @param $marker ['marker_address'] string Address
	 * @param $marker ['marker_text'] string Marker Text
	 * @param $marker ['marker_img'] string Marker Image
	 * @param $marker ['marker_size'] string Marker Size
	 */
	if ( ! empty( $marker['marker_address'] ) ) {

		// Apply dynamic values for marker text
		if ( ! empty( $marker['marker_text'] ) ) {
			$_marker_text = str_replace( '{{address}}', $marker['marker_address'], $marker['marker_text'] );
			$_marker_text = us_replace_dynamic_value( $_marker_text, 'any' );
			$_marker_text = do_shortcode( $_marker_text );
		}

		$map_options['markers'][] = array(
			'address' => $marker['marker_address'],
			'html' => $_marker_text,
			'marker_img' => ! empty( $marker['marker_img'] ) ? wp_get_attachment_image_src( $marker['marker_img'], 'thumbnail' ) : NULL,
			'marker_size' => ! empty( $marker['marker_size'] ) ? array(
				$marker['marker_size'],
				$marker['marker_size'],
			) : NULL,
		);
	}
}

// Custom Marker Image
if ( $custom_marker_img_url = wp_get_attachment_image_url( $custom_marker_img, 'thumbnail' ) ) {
	$map_options['icon'] = array(
		'url' => $custom_marker_img_url,
		'size' => array( (int) $custom_marker_size, (int) $custom_marker_size ),
	);
}

// Add map type for Google Maps
if ( $provider == 'google' ) {
	$map_options['maptype'] = strtoupper( $type );
}

// Layer style, required for OSM
if ( $provider == 'osm' ) {
	$map_options['style'] = ! empty( $layer_style ) ? $layer_style : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
}

$map_options = apply_filters( 'us_maps_js_options', $map_options, get_the_ID(), $_atts['id'] );

// Enqueue relevant scripts
if ( $provider == 'osm' ) {
	if ( us_get_option( 'ajax_load_js', 0 ) == 0 ) {
		wp_enqueue_script( 'us-lmap' );
	}
} elseif ( $provider == 'google' ) {
	wp_enqueue_script( 'us-google-maps' );
	if ( us_get_option( 'ajax_load_js', 0 ) == 0 ) {
		wp_enqueue_script( 'us-gmap' );
	}
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';

// Add onclick options for not AMP version
if ( ! us_amp() ) {
	$output .= '<div class="w-map-json"' . us_pass_data_to_js( $map_options ) . '></div>';
	if ( $provider == 'google' AND $map_style_json != '' ) {
		$output .= '<div class="w-map-style-json" onclick=\'return ' . str_replace( "'", '&#39;', rawurldecode( base64_decode( $map_style_json ) ) ) . '\'></div>';
	}
}
$output .= '</div>';

// If we are in WPB front end editor mode, apply JS to maps
if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) {
	if ( $provider == 'osm' ) {
		$output .= '<script>
		jQuery( function( $ ) {
			if ( typeof $us !== "undefined" && typeof $us.WMaps === "function" ) {
				var $elm = jQuery( "#' . $_atts['id'] . '" );
				$us.getScript($us.templateDirectoryUri+"/common/js/vendor/leaflet.js", function(){
					if ( $elm.data( "wLmaps" ) === undefined ) {
						$elm.WLmaps();
					}
				} );
			}
		} );
		</script>';
	} else {
		$output .= '<script>
		jQuery( function( $ ) {
			if ( typeof $us !== "undefined" && typeof $us.WMaps === "function" ) {
				var $elm = jQuery( "#' . $_atts['id'] . '" );
				$us.getScript($us.templateDirectoryUri+"/common/js/vendor/gmaps.js", function(){
					if ( $elm.data( "wMaps" ) === undefined ) {
						$elm.wMaps();
					}
				} );
			}
		} );
		</script>';
	}
}

echo $output;
