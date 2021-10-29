/**
 * Search Form
 */

! function( $ ) {
	"use strict";

	$.fn.wSearch = function() {

		return this.each( function() {
			var $container = $( this ),
				$form = $container.find( '.w-search-form' ),
				$btnOpen = $container.find( '.w-search-open' ),
				$btnClose = $container.find( '.w-search-close' ),
				$input = $form.find( '[name="s"]' ),
				$overlay = $container.find( '.w-search-background' ),
				$window = $( window ),
				searchOverlayInitRadius = 25,
				isFullScreen = $container.hasClass( 'layout_fullscreen' ),
				isWithRipple = $container.hasClass( 'with_ripple' ),
				searchHide = function( e ) {
					e.preventDefault();
					e.stopPropagation();
					$container.removeClass( 'active' );
					$input.blur();
					if ( isWithRipple && isFullScreen ) {
						$form.css( {
							transition: 'opacity 0.4s'
						} );
						$us.timeout( function() {
							$overlay
								.removeClass( 'overlay-on' )
								.addClass( 'overlay-out' )
								.css( {
									'transform': 'scale(0.1)'
								} );
							$form.css( 'opacity', 0 );
							$us.debounce( function() {
								$form.css( 'display', 'none' );
								$overlay.css( 'display', 'none' );
							}, 600 )();
						}, 25 );
					}

				},
				searchShow = function() {

					$container.addClass( 'active' );

					if ( isWithRipple && isFullScreen ) {
						var searchPos = $btnOpen.offset(),
							searchWidth = $btnOpen.width(),
							searchHeight = $btnOpen.height();
						// Preserving scroll position
						searchPos.top -= $window.scrollTop();
						searchPos.left -= $window.scrollLeft();
						var overlayX = searchPos.left + searchWidth / 2,
							overlayY = searchPos.top + searchHeight / 2,
							winWidth = $us.canvas.winWidth,
							winHeight = $us.canvas.winHeight,
							// Counting distance to the nearest screen corner
							overlayRadius = Math.sqrt( Math.pow( Math.max( winWidth - overlayX, overlayX ), 2 ) + Math.pow( Math.max( winHeight - overlayY, overlayY ), 2 ) ),
							overlayScale = ( overlayRadius + 15 ) / searchOverlayInitRadius;

						$overlay.css( {
							width: searchOverlayInitRadius * 2,
							height: searchOverlayInitRadius * 2,
							left: overlayX,
							top: overlayY,
							"margin-left": - searchOverlayInitRadius,
							"margin-top": - searchOverlayInitRadius
						} );
						$overlay
							.removeClass( 'overlay-out' )
							.show();
						$form.css( {
							opacity: 0,
							display: 'block',
							transition: 'opacity 0.4s 0.3s'
						} );
						$us.timeout( function() {
							$overlay
								.addClass( 'overlay-on' )
								.css( {
									"transform": "scale(" + overlayScale + ")"
								} );
							$form.css( 'opacity', 1 );
						}, 25 );
						$input.trigger( 'focus' );
					} else {
						$input.trigger( 'focus' );
					}

				};

			$btnOpen.on( 'click', searchShow );
			$btnClose.on( 'click touchstart', searchHide );

			$input.keyup( function( e ) {
				if ( e.keyCode === 27 ) {
					searchHide( e );
				}
			} );


		} );
	};

	$( function() {
		jQuery( '.l-header .w-search' ).wSearch();
	} );
}( jQuery );
