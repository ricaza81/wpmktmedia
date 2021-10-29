/**
 * Remove Video Overlay on click
 */
;( function( $, undefined ) {

	// Private variables that are used only in the context of this function, it is necessary to optimize the code.
	var _window = window,
		_document = document;

	// Global variable for YouTube player API objects
	_window.$us.YTPlayers = _window.$us.YTPlayers || {};

	"use strict";
	/* @class wVideo */
	$us.wVideo = function( container ) {
		// Elements
		this.$container = $( container );
		this.$videoH = $( '.w-video-h', this.$container );

		// Variables
		this.data = {};

		// Get data for initializing the player
		if ( this.$container.is( '[onclick]' ) ) {
			this.data = this.$container[0].onclick() || {};
			// Delete data everywhere except for the preview of the USBuilder, the data may be needed again to restore the elements.
			if ( ! $us.usbPreview ) this.$container.removeAttr( 'onclick' );
		}

		/**
		 * Bondable events.
		 *
		 * @private
		 * @var {{}}
		 */
		this._events = {
			overlayClick: this._overlayClick.bind( this ),
		};

		if ( this.$container.is( '.with_overlay' ) ) {
			// Events
			this.$container
				.one( 'click', '> *', this._events.overlayClick );

		} else if ( ! $.isEmptyObject( this.data ) ) {
			// Add player to document.
			this.insertPlayer();
		}
	};

	// Export API
	$.extend( $us.wVideo.prototype, {
		/**
		 * @private
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_overlayClick: function( e ) {
			e.preventDefault();
			// Add player to document.
			this.insertPlayer();
		},

		/**
		 * Add player to document.
		 */
		insertPlayer: function() {
			// Get player data.
			var data = $.extend( { player_id: '', player_api: '', player_html: '' }, this.data || {} );

			// If there is no API in the document yet, then add to the head.
			if ( data.player_api && ! $( 'script[src="'+ data.player_api +'"]', _document.head ).length ) {
				$( 'head' ).append( '<script src="'+ data.player_api +'"></script>' );
			}

			// Add init and container.
			this.$videoH.html( data.player_html );

			// Remove overlay.
			if ( this.$container.is( '.with_overlay' ) ) {
				this.$container
					.removeAttr( 'style' )
					.removeClass( 'with_overlay' );
			}
		}
	});

	$.fn.wVideo = function( options ) {
		return this.each( function() {
			$( this ).data( 'wVideo', new $us.wVideo( this, options ) );
		} );
	};

	$( function() {
		$( '.w-video' ).wVideo();
	} );
} )( jQuery );
