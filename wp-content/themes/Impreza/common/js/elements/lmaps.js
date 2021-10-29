/**
 * UpSolution Element: Leaflet Maps
 *
 * Used for [us_gmaps] shortcode
 *
 * Leaflet JS Official Docs https://leafletjs.com/
 */
!function( $ ) {
	"use strict";

	$us.WLmaps = function( container, options ) {
		this.init( container, options );
	};

	$us.WLmaps.prototype = {
		init: function( container, options ) {
			this.$container = $( container );
			this.mapId = this.$container.attr( 'id' );
			var $jsonContainer = this.$container.find( '.w-map-json' ),
				jsonOptions = $jsonContainer[ 0 ].onclick() || {},
				defaults = {};
			$jsonContainer.remove();

			this.options = $.extend( {}, defaults, jsonOptions, options );
			this._events = {
				redraw: this.redraw.bind( this ),
			};

			$us.$canvas.on( 'contentChange', this._events.redraw );
			this.beforeRender();
		},
		beforeRender: function() {
			var matches = this.options.address.match( /^(\d+.\d+)\s?,?\s?(\d+.\d+)$/ );
			if ( matches ) {
				this.center = [matches[ 1 ], matches[ 2 ]];
				this.renderMap();
			} else {
				this.geocoder( this.options.address );
			}
		},
		redraw: function() {
			if ( !this.lmap || this.$container.is( ':hidden' ) ) {
				return;
			}
			this.lmap.invalidateSize( true );
		},
		geocoder: function( request, markerOptions, popup ) {
			var endPoint = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q=",
				that = this;
			// Get coordinates from the search engine
			$.getJSON( endPoint + encodeURI( request ), function( json ) {
				// First Success
			} ).done( function( json ) {
				if ( !json.length ) {
					// Return in case no coordinates were found
					return;
				}
				// Get Coordinates
				var bBox = json[ 0 ].boundingbox;

				if ( !markerOptions ) {
					// Get coordinates to set map center and add 1st marker
					that.center = [bBox[ 1 ], bBox[ 3 ]];
					that.renderMap();
				} else {
					that.marker = L.marker( [bBox[ 1 ], bBox[ 3 ]], markerOptions ).addTo( that.lmap );
					// Add marker popups
					if ( popup ) {
						that.marker.bindPopup( popup );
					}
				}
			} );
		},
		renderMap: function() {
			var lmapsOptions = {
				// Basic Map setup
				center: this.center,
				zoom: this.options.zoom,
			};

			if ( this.options.hideControls ) {
				// Hide zooming buttons
				lmapsOptions.zoomControl = false;
			}
			if ( this.options.disableZoom ) {
				// Disable Mouse Zooming
				lmapsOptions.scrollWheelZoom = false;
			}


			// Main map object
			this.lmap = L.map( this.mapId, lmapsOptions );

			// Add a Raster layer to the map. Copyright layer required by OSM https://www.openstreetmap.org/copyright
			L.tileLayer( this.options.style, {
					attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'}
			).addTo( this.lmap );

			this.renderMarkers();

			if ( this.options.disableDragging && ( !$us.$html.hasClass( 'no-touch' ) ) ) {
				// Disable dragging on mobiles
				this.lmap.dragging.disable();
			}
		},
		renderMarkers: function() {
			if ( this.options.markers.length ) {
				// Add markers
				var mainOptions = {};

				for ( var i = 0; i < this.options.markers.length; i ++ ) {
					if ( i == 0 ) {
						// Handle first marker separately
						if ( this.options.icon != null ) {
							var mainMarkerSizes = this.options.icon.size[ 0 ],
								markerImg = L.icon( {
									iconUrl: this.options.icon.url,
									iconSize: mainMarkerSizes,
								} );
							// Set icon offset
							markerImg.options.iconAnchor = [mainMarkerSizes / 2, mainMarkerSizes];
							// Set popup offset
							markerImg.options.popupAnchor = [0, - mainMarkerSizes];
							// Push Marker Icons to Options object
							mainOptions.icon = markerImg;
						}

						// Add main marker with calculated coordinates
						var marker = L.marker( this.center, mainOptions ).addTo( this.lmap );

						// Add a popup to the 1st marker
						if ( this.options.markers[ i ].html ) {
							if ( this.options.markers[ i ].infowindow ) {
								marker.bindPopup( this.options.markers[ i ].html ).openPopup();
							} else {
								marker.bindPopup( this.options.markers[ i ].html );
							}
						}
					} else {
						var markerOptions = {};
						// All markers but first
						if ( this.options.markers[ i ].marker_img != null ) {
							var markerSizes = this.options.markers[ i ].marker_size[ 0 ],
								markerImg = L.icon( {
									iconUrl: this.options.markers[ i ].marker_img[ 0 ],
									iconSize: markerSizes,
								} );
							// Set icon offset
							markerImg.options.iconAnchor = [markerSizes / 2, markerSizes];
							// Set popup offset
							markerImg.options.popupAnchor = [0, - markerSizes];
							markerOptions.icon = markerImg;

						} else {
							markerOptions = mainOptions;
						}

						var matches = this.options.markers[ i ].address.match( /^(-?\d+.\d+)\s?,?\s?(-?\d+.\d+)$/ );
						if ( matches ) {
							this.marker = L.marker( [matches[ 1 ], matches[ 2 ]], markerOptions ).addTo( this.lmap );
							if ( this.options.markers[ i ].html ) {
								// Add a popup if marker has some text
								this.marker.bindPopup( this.options.markers[ i ].html )
							}
						} else {
							this.geocoder( this.options.markers[ i ].address, markerOptions, this.options.markers[ i ].html );
						}
					}

				}
			}
		}
	};

	$.fn.WLmaps = function( options ) {
		return this.each( function() {
			$( this ).data( 'wLmaps', new $us.WLmaps( this, options ) );
		} );
	};

	$.fn.WLmapsWithPreload = function( options ) {
		$us.getScript( $us.templateDirectoryUri + '/common/js/vendor/leaflet.js', function() {
			return this.each( function() {
				$( this ).data( 'wLmaps', new $us.WLmaps( this, options ) );
			} );
		}.bind( this ) );
	};

	$( function() {
		var $wLmap = $( '.w-map.provider_osm' );
		if ( $wLmap.length ) {
			$us.getScript( $us.templateDirectoryUri + '/common/js/vendor/leaflet.js', function() {
				$wLmap.WLmaps();
			} );
		}
	} );

}( jQuery );