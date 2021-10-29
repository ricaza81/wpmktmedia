/**
 * UpSolution Element: Gallery
 * Used for regular WordPress gallery design improvement
 */
jQuery( function( $ ) {
	$( '.w-gallery.link_file .w-gallery-list' ).each( function() {

		$us.getScript( $us.templateDirectoryUri + '/common/js/vendor/magnific-popup.js', function() {
			$( this ).magnificPopup( {
				type: 'image',
				delegate: 'a.w-gallery-item',
				gallery: {
					enabled: true,
					navigateByImgClick: true,
					preload: [ 0, 1 ],
					tPrev: $us.langOptions.magnificPopup.tPrev, // Alt text on left arrow
					tNext: $us.langOptions.magnificPopup.tNext, // Alt text on right arrow
					tCounter: $us.langOptions.magnificPopup.tCounter // Markup for "1 of 7" counter
				},
				removalDelay: 300,
				mainClass: 'mfp-fade',
				fixedContentPos: true
			} );
		}.bind( this ) );
	} );

	// Applying isotope to gallery
	$( '.w-gallery.type_masonry' ).each( function( index, gallery ) {
		$us.getScript( $us.templateDirectoryUri + '/common/js/vendor/isotope.js', function() {

			var $container = $( '.w-gallery-list', gallery ),
				isotopeOptions = {
					layoutMode: 'masonry',
					isOriginLeft: ! $( 'body' ).hasClass( 'rtl' )
				};
			if ( $container.parents( '.w-tabs-section-content-h' ).length ) {
				isotopeOptions.transitionDuration = 0;
			}
			$container.imagesLoaded( function() {
				$container.isotope( isotopeOptions );
				$container.isotope();
			} );
			$us.$canvas.on( 'contentChange', function() {
				$container.imagesLoaded( function() {
					$container.isotope();
				} );
			} );
		} );

	} );

} );
