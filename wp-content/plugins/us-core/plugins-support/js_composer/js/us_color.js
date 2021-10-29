!function( $ ) {
	"use strict";
	$( '.us_color .type_color' ).each( function() {
		var $this = $( this ),
			$color = $this.find( 'input[type="text"]' );

		$this.usofField();
		$this.data( 'usofField' ).trigger( 'beforeShow' ).on( 'change', function( ev ) {
			$this.prev().val( $color.val() );
		} );
	} );
}( window.jQuery );