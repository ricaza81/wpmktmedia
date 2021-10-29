!function( $, undefined ) {
	"use strict";

	/**
	 * Processing radio buttons from the preview
	 */
	$( '.wpb_edit_form_elements .usof-imgradio' ).each( function() {
		var $this = $( this ),
			$input = $this.find( 'input[type="hidden"]' );
		$this
			.find( 'input[type="radio"]' )
			.on( 'change', function() {
				$input
					.val( $.trim( this.value ) )
					.trigger( 'change' );
			} );
	} );
}( window.jQuery );
