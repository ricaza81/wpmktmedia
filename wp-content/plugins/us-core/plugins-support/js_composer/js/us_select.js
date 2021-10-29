! ( function( $, undefined ) {
	"use strict";
	// Init select for grid layouts
	$( '.vc_ui-panel-window.vc_active .us_select_for_vc.for_grid_layouts' )
		.each( function() {
			( new $usof.field( this ) ).init( this );
		} );
} )( jQuery );
