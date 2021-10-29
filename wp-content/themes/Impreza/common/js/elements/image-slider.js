/**
 * UpSolution Element: Image Slider
 */
( function( $ ) {
	$.fn.wSlider = function() {
		return this.each( function() {
			var $this = $( this ),
				$frame = $this.find( '.w-slider-h' ),
				$slider = $this.find( '.royalSlider' ),
				$options = $this.find( '.w-slider-json' ),
				options = $options[ 0 ].onclick() || {};

			// Prevent double init
			if ( $this.data( 'sliderInit' ) == 1 ) {
				return;
			}
			$this.data( 'sliderInit', 1 );

			$us.getScript( $us.templateDirectoryUri + '/common/js/vendor/royalslider.js', function() {
				$options.remove();
				if ( ! $.fn.royalSlider ) {
					return;
				}
				// Always apply certain fit option for grid listing slider
				if ( $this.parent().hasClass( 'w-post-elm' ) ) {
					options[ 'imageScaleMode' ] = 'fill';
				}

				options[ 'usePreloader' ] = false;

				$slider.royalSlider( options );
				var slider = $slider.data( 'royalSlider' );
				if ( options.fullscreen && options.fullscreen.enabled ) {
					// Moving royal slider to the very end of body element to allow a proper fullscreen
					var rsEnterFullscreen = function() {
						$slider.appendTo( $( 'body' ) );
						slider.ev.off( 'rsEnterFullscreen', rsEnterFullscreen );
						slider.ev.on( 'rsExitFullscreen', rsExitFullscreen );
						slider.updateSliderSize();
					};
					slider.ev.on( 'rsEnterFullscreen', rsEnterFullscreen );
					var rsExitFullscreen = function() {
						$slider.prependTo( $frame );
						slider.ev.off( 'rsExitFullscreen', rsExitFullscreen );
						slider.ev.on( 'rsEnterFullscreen', rsEnterFullscreen );
					};
				}

				slider.ev.on( 'rsAfterContentSet', function() {
					slider.slides.forEach( function( slide ) {
						$( slide.content.find( 'img' )[ 0 ] ).attr( 'alt', slide.caption.attr( 'data-alt' ) );
					} );
				} );

				$us.$canvas.on( 'contentChange', function() {
					$slider.parent().imagesLoaded( function() {
						slider.updateSliderSize();
					} );
				} );
			} );
		} );
	};
	$( function() {
		jQuery( '.w-slider' ).wSlider();
	} );
} )( jQuery );