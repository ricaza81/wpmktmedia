// Animation of elements appearance
;( function( $ ) {
	"use strict";

	/**
	 * @class USAnimate (name)
	 * @param mixed container The container
	 * @return self
	 */
	var USAnimate = function( container ) {
		// Elements
		this.$container = $( container );
		this.$items = $( '[class*="us_animate_"]', this.$container ).not( '.off_autostart' );

		// Init waypoints
		this.$items.each( function( _, item ) {
			var $item = $( item );
			if ( $item.data( '_animate_inited' ) || $item.is( '.off_autostart' ) ) {
				return;
			}
			$item.data( '_animate_inited', true );
			$us.waypoints.add( $item, '12%', function( $elm ) {
				if ( ! $elm.hasClass( 'start' ) ) {
					$us.timeout( function() {
						$elm.addClass( 'start' );
					}, 20 );
				}
			} );
		} );
	};

	// Export API
	window.USAnimate = USAnimate;

	// Init for loaded document
	new USAnimate( document );

	// Start animation for WPB elements that use their own animation options
	$( '.wpb_animate_when_almost_visible' ).each( function() {
		$us.waypoints.add( $( this ), '12%', function( $elm ) {
			if ( ! $elm.hasClass( 'wpb_start_animation' ) ) {
				$us.timeout( function() {
					$elm.addClass( 'wpb_start_animation' );
				}, 20 );
			}
		} );
	} );
} )( jQuery );
