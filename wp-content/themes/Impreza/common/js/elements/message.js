/**
 * UpSolution Element: Message
 */
( function( $ ) {
	"use strict";

	$.fn.usMessage = function() {
		return this.each( function() {
			var $this = $( this ),
				$closer = $this.find( '.w-message-close' );
			$closer.click( function() {
				$this.wrap( '<div></div>' );
				var $wrapper = $this.parent();
				$wrapper.css( { overflow: 'hidden', height: $this.outerHeight( true ) } );
				$wrapper.performCSSTransition( {
					height: 0
				}, 300, function() {
					$wrapper.remove();
					$us.$canvas.trigger( 'contentChange' );
				}, 'cubic-bezier(.4,0,.2,1)' );
			} );
		} );
	};

	$( function() {
		$( '.w-message' ).usMessage();
	} );
} )( jQuery );