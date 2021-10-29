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

global $us_maps_index;
$us_maps_index = isset( $us_maps_index ) ? $us_maps_index + 1 : 1;

$_atts['class'] = 'w-map provider_' . $provider;
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' us_map_' . $us_maps_index;

if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
$_atts['id'] = ! empty( $el_id ) ? $el_id : 'us_map_' . $us_maps_index;

// Decoding base64-encoded HTML attributes
if ( ! empty( $marker_text ) ) {
	$marker_text = rawurldecode( base64_decode( $marker_text ) );
}

if ( ! in_array( $zoom, array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20 ) ) ) {
	$zoom = 14;
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

	} elseif ( $provider == 'osm'

		// Make sure we have coordinates, not address
		AND preg_match( '#([0-9]+.[0-9]+),(\s)?([0-9]+.[0-9]+)#', $marker_address, $matches ) ) {

		$_atts['src'] = 'https://www.openstreetmap.org/export/embed.html?bbox=' . us_map_get_bbox( $matches[1], $matches[3], $zoom ) . '&marker=' . $matches[1] . ',' . $matches[3] . '&layer=mapnik';
	}

	echo '<amp-iframe ' . us_implode_atts( $_atts ) . '>';

	// Placeholder image required for case when map is too close to the top of the page
	echo '<amp-img layout="fill" src="' . us_get_img_placeholder( 'full', TRUE ) . '" placeholder></amp-img>';
	echo '</amp-iframe>';

	return;
}

// Form all options needed for JS
$script_options = array();
if ( ! empty( $marker_address ) ) {
	$script_options['address'] = $marker_address;
} else {
	return NULL;
}
$script_options['markers'] = array(
	array_merge(
		$script_options, array(
			'html' => $marker_text,
			'infowindow' => $show_infowindow,
		)
	)
);

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
	 * Filtering the included markers
	 *
	 * @param $marker ['marker_address'] string Address
	 * @param $marker ['marker_text'] string Marker Text
	 * @param $marker ['marker_img'] string Marker Image
	 * @param $marker ['marker_size'] string Marker Size
	 */
	if ( ! empty( $marker['marker_address'] ) ) {
		$script_options['markers'][] = array(
			'html' => ! empty( $marker['marker_text'] ) ? $marker['marker_text'] : '',
			'address' => $marker['marker_address'],
			'marker_img' => ( ! empty( $marker['marker_img'] ) ) ? wp_get_attachment_image_src( intval( $marker['marker_img'] ), 'thumbnail' ) : NULL,
			'marker_size' => ( ! empty( $marker['marker_size'] ) ) ? array(
				$marker['marker_size'],
				$marker['marker_size'],
			) : NULL,
		);
	}
}

if ( ! empty( $zoom ) ) {
	$script_options['zoom'] = intval( $zoom );
}

if ( ! empty( $type ) AND $provider == 'google' ) {
	$type = strtoupper( $type );
	if ( in_array( $type, array( 'ROADMAP', 'SATELLITE', 'HYBRID', 'TERRAIN' ) ) ) {
		$script_options['maptype'] = $type;
	}
}

if ( ! empty( $map_bg_color ) ) {
	$script_options['mapBgColor'] = $map_bg_color;
}

if ( $custom_marker_img != '' AND $custom_marker_img_src = wp_get_attachment_image_url( $custom_marker_img, 'thumbnail' ) ) {
	$custom_marker_size = intval( $custom_marker_size );
	$script_options['icon'] = array(
		'url' => $custom_marker_img_src,
		'size' => array( $custom_marker_size, $custom_marker_size ),
	);
}

if ( empty( $height ) ) {
	$height = 400;
}
$script_options['height'] = $height;

if ( $provider == 'osm' ) {
	if ( ! empty( $layer_style ) ) {
		$script_options['style'] = $layer_style;
	} else {
		$script_options['style'] = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'; // default value for empty case
	}
}

if ( $hide_controls ) {
	$script_options['hideControls'] = TRUE;
}

if ( $disable_zoom ) {
	$script_options['disableZoom'] = TRUE;
}

if ( $disable_dragging ) {
	$script_options['disableDragging'] = TRUE;
}

$script_options = apply_filters( 'us_maps_js_options', $script_options, get_the_ID(), $us_maps_index );


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
$output = '<div ' . us_implode_atts( $_atts ) . '>';

// Add onclick options for not AMP version
if ( ! us_amp() ) {
	$output .= '<div class="w-map-json"' . us_pass_data_to_js( $script_options ) . '></div>';
	if ( $provider == 'google' AND $map_style_json != '' ) {
		$output .= '<div class="w-map-style-json" onclick=\'return ' . str_replace( "'", '&#39;', rawurldecode( base64_decode( $map_style_json ) ) ) . '\'></div>';
	}
}
$output .= '</div>';

// If we are in front end editor mode, apply JS to maps
if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) {
	if ( $provider == 'osm' ) {
		$output .= '<script>
		jQuery(function($){
			if (typeof $us !== "undefined" && typeof $us.WMaps === "function") {
				var $wLmap = $(".w-map.provider_osm");
				if ($wLmap.length){
					$us.getScript($us.templateDirectoryUri+"/common/js/vendor/leaflet.js", function(){
						$wLmap.WLmaps();
					});
				}
			}
		});
		</script>';
	} else {
		$output .= '<script>
		jQuery(function($){
			if (typeof $us !== "undefined" && typeof $us.WMaps === "function") {
				var $wMap = $(".w-map.provider_google");
				if ($wMap.length){
					$us.getScript($us.templateDirectoryUri+"/common/js/vendor/gmaps.js", function(){
						$wMap.wMaps();
					});
				}
			}
		});
		</script>';
	}

}

echo $output;
