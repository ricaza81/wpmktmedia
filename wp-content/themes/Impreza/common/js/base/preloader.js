/**
 * UpSolution Element: l-preloader
 */
! function( $ ) {
	"use strict";

	if ( $( '.l-preloader' ).length ) {
		$( 'document' ).ready( function() {
			$us.timeout( function() {
				$( '.l-preloader' ).addClass( 'done' );
			}, 500 );
			$us.timeout( function() {
				$( '.l-preloader' ).addClass( 'hidden' );
			}, 1000 ); // 500 ms after 'done' class is added
		} );
	}
}( jQuery );
