;( function( $, undefined ) {
	"use strict";
	$( '.wpb_el_type_us_autocomplete .type_autocomplete' ).each( function() {
		( new $usof.field( this ) ).init( this );
	} );

} )( jQuery );
