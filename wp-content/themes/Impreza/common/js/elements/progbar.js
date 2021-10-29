/**
 * UpSolution Element: Progbar
 */
;( function( $, undefined ) {
	"use strict";

	 $us.WProgbar = function( container, options ) {
		// Elements
		this.$container = $( container );
		this.$bar = $( '.w-progbar-bar-h', this.$container );
		this.$count = $( '.w-progbar-title-count, .w-progbar-bar-count', this.$container );
		this.$title = $( '.w-progbar-title', this.$container );

		// Default options
		this.options = {
			delay: 100,
			duration: 800,
			finalValue: 100,
			offset: '10%',
			startValue: 0,
			value: 50
		};

		// Get options
		if ( this.$container.is( '[onclick]' ) ) {
			$.extend( this.options, this.$container[0].onclick() || {} );
			this.$container.removeAttr( 'onclick' );
		}

		// Priority in transferred options through JS will be higher
		$.extend( this.options, options || {} );

		if ( /bot|googlebot|crawler|spider|robot|crawling/i.test( navigator.userAgent ) ) {
			this.$container.removeClass( 'initial' );
		}

		// Set start value
		this.$count.text( '' );

		// When an item falls into scope, a run callback function
		$us.waypoints.add( this.$container, this.options.offset, this.init.bind( this ) );
	};

	// Export API
	$.extend( $us.WProgbar.prototype, {
		/**
		 * Init the object.
		 * @return void
		 */
		init: function() {
			if ( this.running ) {
				return;
			}
			this.running = true;

			if ( this.$container.hasClass( 'initial' ) ) {
				this.$container.removeClass( 'initial' )
			}

			// Get all the necessary parameters for the meter and run it
			var
				loops = Math.ceil( this.options.duration / this.options.delay ),
				increment = parseFloat( this.options.value ) / loops,
				loopCount = 0,
				handle = null,
				startValue = 0;

			/**
			 * Anonymous function for creating an interval
			 * @return void
			 */
			var funLoop = function() {
				startValue += increment;
				loopCount++;
				if ( handle ) {
					$us.clearTimeout( handle );
				}
				if ( loopCount >= loops ) {
					var result = this.options.template;
					if ( this.options.hasOwnProperty( 'showFinalValue' ) ) {
						result += ' ' + this.options.showFinalValue;
					}
					this.$count.text( result );
					return;
				}
				this.render.call( this, startValue );
				handle = $us.timeout( funLoop.bind( this ), this.options.delay );
			};

			// Run loop
			funLoop.call( this );

			var finalValue = parseFloat( this.options.finalValue ),
				width =  ( ( parseFloat( parseFloat( this.options.value ) ) / parseFloat( finalValue ) ) * 100 )
				.toFixed( 0 );

			// Run the CSS animations to render a progress bar
			this.$bar
				.on( 'transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', this._events.transitionEnd.bind( this ) )
				.css( {
					width: width + '%',
					transitionDuration: parseInt( this.options.duration ) + 'ms'
				} );
		},
		// Event handlers
		_events: {
			/**
			 * Called after css animation finishes
			 * @return void
			 */
			transitionEnd: function() {
				var result = this.options.template;
				if ( this.options.hasOwnProperty( 'showFinalValue' ) ) {
					result += ' ' + this.options.showFinalValue;
				}
				this.$count.text( result );
				this.running = false;
			}
		},
		/**
		 * Render of the counter
		 * @param value numeric
		 * @return void
		 */
		render: function( value ) {
			var index = 0,
				// Result formatting
				result = ( '' + this.options.template )
					.replace( /([\-\d\.])/g, function( match ) {
						value += '';
						if ( index === 0 && match === '0' ) {
							// Skip point if float value
							if ( value.charAt( index + 1 ) === '.' || match === '.' ) {
								index++;
							}
							return match;
						}
						return value.charAt( index++ ) || '';
					}.bind( this ) );

			if ( result.charAt( index -1 ) === '.' ) {
				result = result.substr( 0, index -1 ) + result.substr( index );
			}

			if ( this.options.hasOwnProperty( 'showFinalValue' ) ) {
				result += ' ' + this.options.showFinalValue;
			}

			// Show result
			this.$count.text( result );
		}
	} );

	// The jQuery version
	$.fn.wProgbar = function( options ) {
		this.each( function() {
			$( this ).data( 'wProgbar', new $us.WProgbar( this, options ) );
		} );
	};

	$( function() {
		jQuery( '.w-progbar' ).wProgbar();
	} );
} )( jQuery );
