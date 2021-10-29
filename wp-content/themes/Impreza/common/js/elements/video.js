/**
 * Remove Video Overlay on click
 */
;( function( $, undefined ) {
	"use strict";
	/* @class wVideo */
	$us.wVideo = function( container ) {
		// Elements
		this.$container = $( container );
		this.$videoH = $( '.w-video-h', this.$container );
		this.$template = $( 'script[type="us-template/html"]:first', this.$videoH );

		// Variables
		this.template = this.$template.html();
		this.$template.remove();

		// Events
		if ( this.$container.hasClass( 'with_overlay' ) ) {
			this.$container.one( 'click', this._events.overlayClick.bind( this ) );
		}
	};
	// Export API
	$.extend( $us.wVideo.prototype, {
		/**
		 * Event handlers
		 * @private
		 */
		_events: {
			/**
			 * @param EventObject e
			 * @return void
			 */
			overlayClick: function( e ) {
				e.preventDefault();
				this.$container
					.removeClass( 'with_overlay' )
					.css( 'background-image', 'none' );
				this.$videoH.html( this.template );
			}
		}
	} );

	$.fn.wVideo = function( options ) {
		return this.each( function() {
			$( this ).data( 'wVideo', new $us.wVideo( this, options ) );
		} );
	};

	$( function() {
		$( '.w-video' ).wVideo();
	} );
} )( jQuery );
