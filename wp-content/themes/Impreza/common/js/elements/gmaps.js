/**
 * UpSolution Element: Google Maps
 *
 * Used for [us_gmaps] shortcode
 */
! function( $ ) {
	"use strict";

	$us.WMapsGeocodesCounter = 0; // counter of total geocode requests number
	$us.WMapsGeocodesRunning = false;
	$us.WMapsCurrentGeocode = 0; // current processing geocode
	$us.WMapsGeocodesMax = 5; // max number of simultaneous geocode requests allowed
	$us.WMapsGeocodesStack = {};

	$us.WMapsRunGeoCode = function() {
		if ( $us.WMapsCurrentGeocode <= $us.WMapsGeocodesCounter ) {
			$us.WMapsGeocodesRunning = true;
			if ( $us.WMapsGeocodesStack[ $us.WMapsCurrentGeocode ] != null ) {
				$us.WMapsGeocodesStack[ $us.WMapsCurrentGeocode ]();
			}
		} else {
			$us.WMapsGeocodesRunning = false;
		}
	};

	$us.WMaps = function( container, options ) {

		this.$container = $( container );

		// Prevent double init
		if ( this.$container.data( 'mapInit' ) == 1 ) {
			return;
		}
		this.$container.data( 'mapInit', 1 );

		var $jsonContainer = this.$container.find( '.w-map-json' ),
			jsonOptions = $jsonContainer[ 0 ].onclick() || {},
			$jsonStyleContainer = this.$container.find( '.w-map-style-json' ),
			jsonStyleOptions,
			shouldRunGeoCode = false;
		$jsonContainer.remove();
		if ( $jsonStyleContainer.length ) {
			jsonStyleOptions = $jsonStyleContainer[ 0 ].onclick() || {};
			$jsonStyleContainer.remove();
		}

		// Setting options
		var defaults = {};
		this.options = $.extend( {}, defaults, jsonOptions, options );

		this._events = {
			redraw: this.redraw.bind( this )
		};

		var gmapsOptions = {
			el: '#' + this.$container.attr( 'id' ),
			lat: 0,
			lng: 0,
			zoom: this.options.zoom,
			type: this.options.type,
			height: this.options.height + 'px',
			width: '100%',
			mapTypeId: google.maps.MapTypeId[ this.options.maptype ]
		};

		if ( this.options.hideControls ) {
			gmapsOptions.disableDefaultUI = true;
		}
		if ( this.options.disableZoom ) {
			gmapsOptions.scrollwheel = false;
		}
		if ( this.options.disableDragging && ( ! $us.$html.hasClass( 'no-touch' ) ) ) {
			gmapsOptions.draggable = false;
		}
		if ( this.options.mapBgColor ) {
			gmapsOptions.backgroundColor = this.options.mapBgColor;
		}

		this.GMapsObj = new GMaps( gmapsOptions );
		if ( jsonStyleOptions != null && jsonStyleOptions != {} ) {
			this.GMapsObj.map.setOptions( { styles: jsonStyleOptions } );
		}

		var matches = this.options.address.match( /^(-?\d+.\d+)\s?,?\s?(-?\d+.\d+)$/ ),
			that = this;

		if ( matches ) {
			this.options.latitude = matches[ 1 ];
			this.options.longitude = matches[ 2 ];
			this.GMapsObj.setCenter( this.options.latitude, this.options.longitude );
		} else {
			var mapGeoCode = function() {
				GMaps.geocode( {
					address: that.options.address,
					callback: function( results, status ) {
						if ( status == 'OK' ) {
							var latlng = results[ 0 ].geometry.location;
							that.options.latitude = latlng.lat();
							that.options.longitude = latlng.lng();
							that.GMapsObj.setCenter( that.options.latitude, that.options.longitude );
							$us.WMapsCurrentGeocode ++;
							$us.WMapsRunGeoCode();
						} else if ( status == "OVER_QUERY_LIMIT" ) {
							$us.timeout( function() {
								$us.WMapsRunGeoCode()
							}, 2000 );
						}
					}
				} );
			};
			shouldRunGeoCode = true;
			$us.WMapsGeocodesStack[ $us.WMapsGeocodesCounter ] = mapGeoCode;
			$us.WMapsGeocodesCounter ++;
		}

		// Map Markers
		$.each( this.options.markers, function( i, val ) {
			var markerOptions = {};
			if ( that.options.icon != null || that.options.markers[ i ].marker_img != null ) {
				var url, size, width, height;
				if ( that.options.markers[ i ].marker_img != null ) {
					url = that.options.markers[ i ].marker_img[ 0 ];
					width = parseInt( that.options.markers[ i ].marker_size[ 0 ] );
					height = parseInt( that.options.markers[ i ].marker_size[ 1 ] );
					size = new google.maps.Size( width, height );
				} else {
					url = that.options.icon.url;
					size = new google.maps.Size( that.options.icon.size[ 0 ], that.options.icon.size[ 1 ] );
				}
				markerOptions.icon = {
					url: url,
					size: size,
					scaledSize: size,
				};
			}
			if ( that.options.markers[ i ] != null ) {
				var matches = that.options.markers[ i ].address.match( /^(-?\d+.\d+)\s?,?\s?(-?\d+.\d+)$/ );
				if ( matches ) {
					markerOptions.lat = matches[ 1 ];
					markerOptions.lng = matches[ 2 ];
					// Do not output empty tooltips
					if ( that.options.markers[ i ].html ) {
						markerOptions.infoWindow = { content: that.options.markers[ i ].html };
					}
					var marker = that.GMapsObj.addMarker( markerOptions );
					if ( that.options.markers[ i ].infowindow ) {
						marker.infoWindow.open( that.GMapsObj.map, marker );
					}
				} else {
					var markerGeoCode = function() {
						GMaps.geocode( {
							address: that.options.markers[ i ].address,
							callback: function( results, status ) {
								if ( status == 'OK' ) {
									var latlng = results[ 0 ].geometry.location;
									markerOptions.lat = latlng.lat();
									markerOptions.lng = latlng.lng();
									markerOptions.infoWindow = { content: that.options.markers[ i ].html };
									var marker = that.GMapsObj.addMarker( markerOptions );
									if ( that.options.markers[ i ].infowindow ) {
										marker.infoWindow.open( that.GMapsObj.map, marker );
									}
									$us.WMapsCurrentGeocode ++;
									$us.WMapsRunGeoCode();
								} else if ( status == "OVER_QUERY_LIMIT" ) {
									$us.timeout( function() {
										$us.WMapsRunGeoCode()
									}, 2000 );
								}
							}
						} );
					};
					shouldRunGeoCode = true;
					$us.WMapsGeocodesStack[ $us.WMapsGeocodesCounter ] = markerGeoCode;
					$us.WMapsGeocodesCounter ++;
				}
			}
		} );

		if ( shouldRunGeoCode && ( ! $us.WMapsGeocodesRunning ) ) {
			$us.WMapsRunGeoCode();
		}

		$us.$canvas.on( 'contentChange', this._events.redraw );

		// In case some toggler was opened before the actual page load
		$us.$window.on( 'load', this._events.redraw );
	};

	$us.WMaps.prototype = {
		// Fixing hidden and other breaking-cases maps
		redraw: function() {
			if ( this.$container.is( ':hidden' ) ) {
				return;
			}
			this.GMapsObj.refresh();
			if ( this.options.latitude != null && this.options.longitude != null ) {
				this.GMapsObj.setCenter( this.options.latitude, this.options.longitude );
			}

		}
	};

	$.fn.wMaps = function( options ) {
		return this.each( function() {
			$( this ).data( 'wMaps', new $us.WMaps( this, options ) );
		} );
	};

	$( function() {
		var $wMap = $( '.w-map.provider_google' );
		if ( $wMap.length ) {
			$us.getScript( $us.templateDirectoryUri + '/common/js/vendor/gmaps.js', function() {
				$wMap.wMaps();
			} );
		}
	} );
}( jQuery );
