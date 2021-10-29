/**
 * UpSolution Element: Tabs
 *
 * @requires $us.canvas
 */
! function( $ ) {
	"use strict";

	$us.WTabs = function( container, options ) {
		this.init( container, options );
	};

	$us.WTabs.prototype = {

		init: function( container, options ) {
			// Setting options
			var defaults = {
				duration: 300,
				easing: 'cubic-bezier(.78,.13,.15,.86)'
			};
			this.options = $.extend( {}, defaults, options );
			this.isRtl = $( '.l-body' ).hasClass( 'rtl' );

			// Commonly used dom elements
			this.$container = $( container );
			this.$tabsList = this.$container.find( '> .w-tabs-list:first' );
			this.$tabs = this.$tabsList.find( '.w-tabs-item' );
			this.$sectionsWrapper = this.$container.find( '> .w-tabs-sections:first' );
			this.$sectionsHelper = this.$sectionsWrapper.children();
			this.$sections = this.$sectionsHelper.find( '> .w-tabs-section' );
			this.$headers = this.$sections.children( '.w-tabs-section-header' );
			this.$contents = this.$sections.children( '.w-tabs-section-content' );
			this.$line_charts = this.$container.find( ".vc_line-chart" );
			this.$round_charts = this.$container.find( ".vc_round-chart" );

			// Overriding specific to Web Accessibility, it is not allowed to have several identical id and aria-content, aria-control
			// http://web-accessibility.carnegiemuseums.org/code/accordions/
			if ( this.$container.hasClass( 'accordion' ) ) {
				this.$tabs = this.$headers;
			}

			// Class variables
			this.width = 0;
			this.tabWidths = [];
			this.tabHeights = [];
			this.tabTops = [];
			this.tabLefts = [];
			this.isScrolling = false;
			this.hasScrolling = this.$container.hasClass( 'has_scrolling' ) || false;
			this.isTogglable = ( this.$container.usMod( 'type' ) === 'togglable' );
			this.isStretched = this.$tabsList.hasClass( 'stretch' );
			this.minWidth = 0; // Container width at which we should switch to accordion layout
			this.count = this.$tabs.length;

			// If there are no tabs, abort further execution
			if ( this.count === 0 ) {
				return;
			}

			// Basic layout
			this.basicLayout = this.$container.hasClass( 'accordion' )
				? 'accordion'
				: ( this.$container.usMod( 'layout' ) || 'hor' );

			// Current active layout (may be switched to 'accordion')
			this.curLayout = this.basicLayout;

			// Array of active tabs indexes
			this.active = [];
			this.activeOnInit = [];
			this.definedActive = [];

			// Material style bar for Trendy tabs
			this.isTrendy = this.$container.hasClass( 'style_trendy' );
			if ( this.isTrendy ) {
				this.$tabsBar = $();
			}

			// Preparing arrays of jQuery objects for easier manipulating in future
			this.tabs = $.map( this.$tabs.toArray(), $ );
			this.sections = $.map( this.$sections.toArray(), $ );
			this.headers = $.map( this.$headers.toArray(), $ );
			this.contents = $.map( this.$contents.toArray(), $ );

			// Do nothing it there are no sections
			if ( ! this.sections.length ) {
				return;
			}

			$.each( this.tabs, function( index ) {

				if ( this.sections[ index ].hasClass( 'content-empty' ) ) {
					this.tabs[ index ].hide();
					this.sections[ index ].hide();
				}

				if ( this.tabs[ index ].hasClass( 'active' ) ) {
					this.active.push( index );
					this.activeOnInit.push( index );
				}
				if ( this.tabs[ index ].hasClass( 'defined-active' ) ) {
					this.definedActive.push( index );
				}
				this.tabs[ index ]
					.add( this.headers[ index ] )
					.on( 'click mouseover', function( e ) {
						var $link = this.tabs[ index ];
						if ( ! $link.is( 'a' ) ) {
							$link = $link.find( 'a' );
						}
						if (
							! $link.length
							|| (
								$link.is( '[href]' )
								&& $link.attr( 'href' ).indexOf( 'http' ) === - 1
							)
						) {
							e.preventDefault();
						}
						if (
							e.type == 'mouseover'
							&& (
								this.$container.hasClass( 'accordion' )
								|| ! this.$container.hasClass( 'switch_hover' )
							)
						) {
							return;
						}
						// Toggling accordion sections
						if ( this.curLayout === 'accordion' && this.isTogglable ) {
							// Cannot toggle the only active item
							this.toggleSection( index );
						}
						// Setting tabs active item
						else {
							if ( index != this.active[ 0 ] ) {
								this.headerClicked = true;
								this.openSection( index );
							} else if ( this.curLayout === 'accordion' ) {
								this.contents[ index ]
									.css( 'display', 'block' )
									.attr( 'aria-expanded', 'true' )
									.slideUp( this.options.duration, this._events.contentChanged );
								this.tabs[ index ]
									.removeClass( 'active' );
								this.sections[ index ]
									.removeClass( 'active' );
								this.active[ 0 ] = undefined;
							}
						}
					}.bind( this ) );
				}.bind( this ) );

			// Bindable events
			this._events = {
				resize: this.resize.bind( this ),
				hashchange: this.hashchange.bind( this ),
				contentChanged: function() {
					$.each( this.contents, function( _, item ) {
						var $content = $( item );
						$content.attr( 'aria-expanded', $content.is( ':visible' ) );
					} )
					$us.$canvas.trigger( 'contentChange' );
					// TODO: check if we can do this without hardcoding line charts init here;
					this.$line_charts.length && jQuery.fn.vcLineChart && this.$line_charts.vcLineChart( { reload: ! 1 } );
					this.$round_charts.length && jQuery.fn.vcRoundChart && this.$round_charts.vcRoundChart( { reload: ! 1 } );
				}.bind( this ),
				wheel: function() {
					// Stop animation when scrolling wheel
					if ( this.isScrolling ) {
						$us.$htmlBody.stop( true, false );
					}
				}
			};

			// Starting everything
			this.switchLayout( this.curLayout );

			$us.$window
				.on( 'resize', $us.debounce( this._events.resize, 5 ) )
				.on( 'hashchange', this._events.hashchange )
				.on( 'wheel', $us.debounce( this._events.wheel.bind( this ), 5 ) )

			$us.$document.ready( function() {
				this.resize();
				$us.timeout( this._events.resize, 50 );
				$us.timeout( function() {
					// TODO: move to a class function for code reading improvement
					// Open tab on page load by hash
					if ( window.location.hash ) {
						var hash = window.location.hash.substr( 1 ),
							$linkedSection = this.$sectionsWrapper.find( '.w-tabs-section[id="' + hash + '"]' );
						if ( $linkedSection.length && ( ! $linkedSection.hasClass( 'active' ) ) ) {
							$linkedSection
								.find( '.w-tabs-section-header' )
								.trigger( 'click' );
						}
					}
				}.bind( this ), 150 );
			}.bind( this ) );

			// Support for external links to tabs
			$.each( this.tabs, function( index ) {
				if ( this.headers.length && this.headers[ index ].attr( 'href' ) != undefined ) {
					var tabHref = this.headers[ index ].attr( 'href' ),
						tabHeader = this.headers[ index ];
					$( 'a[href="' + tabHref + '"]', this.$container ).on( 'click', function( e ) {
						e.preventDefault();
						if ( $( this ).hasClass( 'w-tabs-section-header', 'w-tabs-item' ) ) {
							return;
						}
						if ( ! $( tabHeader ).parent('.w-tabs-section').hasClass( 'active' )  ) {
							tabHeader.trigger( 'click' );
						}
					} );
				}
			}.bind( this ) );

			this.$container.addClass( 'initialized' );

			// Gets the height of the header after animation
			this.headerHeight = 0;
			$us.header.on( 'transitionEnd', function( header ) {
				this.headerHeight = header.getCurrentHeight();
			}.bind( this ) );
		},

		hashchange: function() {
			if ( window.location.hash ) {
				var hash = window.location.hash.substr( 1 ),
					$linkedSection = this.$sectionsWrapper.find( '.w-tabs-section[id="' + hash + '"]' );
				if ( $linkedSection.length && ( ! $linkedSection.hasClass( 'active' ) ) ) {
					var $header = $linkedSection.find( '.w-tabs-section-header' );
					$header.click();
				}
			}
		},

		switchLayout: function( to ) {
			this.cleanUpLayout( this.curLayout );
			this.prepareLayout( to );
			this.curLayout = to;
		},

		/**
		 * Clean up layout's special inline styles and/or dom elements
		 * @param from
		 */
		cleanUpLayout: function( from ) {
			if ( from === 'hor' ) {
				this.$sectionsWrapper.clearPreviousTransitions().resetInlineCSS( 'width', 'height' );
				this.$sectionsHelper.clearPreviousTransitions().resetInlineCSS( 'position', 'width', 'left' );
				this.$sections.resetInlineCSS( 'width', 'display' );
				this.$container.removeClass( 'autoresize' );
			} else if ( from === 'accordion' ) {
				this.$container.removeClass( 'accordion' );
				this.$sections.resetInlineCSS( 'display' );
				this.$contents.resetInlineCSS( 'height', 'padding-top', 'padding-bottom', 'display', 'opacity' );
			} else if ( from === 'ver' ) {
				this.$contents.resetInlineCSS( 'height', 'padding-top', 'padding-bottom', 'display', 'opacity' );
			}

			if ( this.isTrendy && ( from === 'hor' || from === 'ver' ) ) {
				this.$tabsBar.remove();
			}
		},

		/**
		 * Apply layout's special inline styles and/or dom elements
		 * @param to
		 */
		prepareLayout: function( to ) {
			if ( to !== 'accordion' && this.active[ 0 ] === undefined ) {
				this.active[ 0 ] = this.activeOnInit[ 0 ];
				if ( this.active[ 0 ] !== undefined ) {
					this.tabs[ this.active[ 0 ] ]
						.addClass( 'active' );
					this.sections[ this.active[ 0 ] ]
						.addClass( 'active' );
				}
			}

			if ( to === 'hor' ) {
				this.$container.addClass( 'autoresize' );
				this.$sectionsHelper.css( 'position', 'absolute' );

			} else if ( to === 'accordion' ) {
				this.$container.addClass( 'accordion' );
				this.$contents.hide();
				if ( this.curLayout !== 'accordion' && this.active[ 0 ] !== undefined && this.active[ 0 ] !== this.definedActive[ 0 ] ) {
					this.tabs[ this.active[ 0 ] ]
						.removeClass( 'active' );
					this.sections[ this.active[ 0 ] ]
						.removeClass( 'active' );
					this.active[ 0 ] = this.definedActive[ 0 ];

				}
				for ( var i = 0; i < this.active.length; i ++ ) {
					if ( this.contents[ this.active[ i ] ] !== undefined ) {
						this.tabs[ this.active[ i ] ]
							.addClass( 'active' );
						this.sections[ this.active[ i ] ]
							.addClass( 'active' );
						this.contents[ this.active[ i ] ]
							.attr( 'aria-expanded', 'true' )
							.show();
					}
				}

			} else if ( to === 'ver' ) {
				this.$contents.hide();
				this.contents[ this.active[ 0 ] ]
					.attr( 'aria-expanded', 'true' )
					.show();
			}

			if ( this.isTrendy && ( to === 'hor' || to === 'ver' ) ) {
				this.$tabsBar = $( '<div class="w-tabs-list-bar"></div>' ).appendTo( this.$tabsList );
			}

		},

		/**
		 * Measure needed sizes
		 */
		measure: function() {
			if ( this.basicLayout === 'ver' ) {
				// Measuring minimum tabs width
				this.$tabsList.css( 'width', 0 );
				var minTabWidth = this.$tabsList.outerWidth( true );
				this.$tabsList.css( 'width', '' );
				// Measuring the mininum content width
				this.$container.addClass( 'measure' );
				var minContentWidth = this.$sectionsWrapper.outerWidth( true );
				this.$container.removeClass( 'measure' );
				// Measuring minimum tabs width for percent-based sizes
				var navWidth = this.$container.usMod( 'navwidth' );
				if ( navWidth !== 'auto' ) {
					// Percent-based measure
					minTabWidth = Math.max( minTabWidth, minContentWidth * parseInt( navWidth ) / ( 100 - parseInt( navWidth ) ) );
				}
				var width = ( ! window.ontouchstart && this.$container.closest('.vc_col-sm-6').parent('.type_boxes').length )
					? 480 - ( parseInt( this.$container.closest('.vc_col-sm-6').width() ) - this.$container.width() )
					: 480;
				this.minWidth = Math.max( width, minContentWidth + minTabWidth + 1 );

				if ( this.isTrendy ) {
					this.tabHeights = [];
					this.tabTops = [];
					for ( var index = 0; index < this.tabs.length; index ++ ) {
						this.tabHeights.push( this.tabs[ index ].outerHeight( true ) );
						this.tabTops.push( index ? ( this.tabTops[ index - 1 ] + this.tabHeights[ index - 1 ] ) : 0 );
					}
				}

			} else {
				if ( this.basicLayout === 'hor' ) {
					this.$container.addClass( 'measure' );
					this.minWidth = 0;
					for ( var index = 0; index < this.tabs.length; index ++ ) {
						this.minWidth += this.tabs[ index ].outerWidth( true );
					}
					this.$container.removeClass( 'measure' );
				}

				if ( this.isTrendy ) {
					this.tabWidths = [];
					this.tabLefts = [];
					for ( var index = 0; index < this.tabs.length; index ++ ) {
						this.tabWidths.push( this.tabs[ index ].outerWidth( true ) );
						this.tabLefts.push( index ? ( this.tabLefts[ index - 1 ] + this.tabWidths[ index - 1 ] ) : 0 );
					}
				}
			}
		},

		/**
		 * Counts bar position for certain element index and current layout
		 *
		 * @param index
		 */
		barPosition: function( index ) {
			if ( this.curLayout === 'hor' ) {
				var result = { width: this.tabWidths[ index ] };
				result[ this.isRtl ? 'right' : 'left' ] = this.tabLefts[ index ];
				return result;
			}
			else if ( this.curLayout === 'ver' ) {
				return {
					top: this.tabTops[ index ],
					height: this.tabHeights[ index ]
				};
			}
			else {
				return {};
			}
		},

		/**
		 * Open tab section
		 *
		 * @param index int
		 */
		openSection: function( index ) {
			if ( this.sections[ index ] === undefined ) {
				return;
			}
			if ( this.curLayout === 'hor' ) {
				this.$container.addClass( 'autoresize' );
				this.$sections
					.removeClass( 'active' )
					.css( 'display', 'none' );
				this.sections[ index ]
					.stop( true, true )
					.fadeIn( this.options.duration, function() {
						$( this ).addClass( 'active' );
					} );
			} else if ( this.curLayout === 'accordion' ) {
				if ( this.contents[ this.active[ 0 ] ] !== undefined ) {
					this.contents[ this.active[ 0 ] ]
						.css( 'display', 'block' )
						.attr( 'aria-expanded', 'true' )
						.stop( true, false )
						.slideUp( this.options.duration );
				}
				this.contents[ index ]
					.css( 'display', 'none' )
					.attr( 'aria-expanded', 'false' )
					.stop( true, false )
					.slideDown( this.options.duration, function() {
						this._events.contentChanged.call( this );
						// If the basic layout for current element is not accordion, but the current layout is accordion,
						// then we will allow scrolling by opening tabs for mobile devices
						if ( this.basicLayout != 'accordion' && this.curLayout == 'accordion' ) {
							this.hasScrolling = true;
						}
						// Scrolling to the opened section
						if ( this.hasScrolling && this.curLayout === 'accordion' && this.headerClicked == true ) {
							var top = this.headers[ index ].offset().top;
							if ( ! jQuery.isMobile ) {
								top -= $us.$canvas.offset().top || 0;
							}
							// If there is a sticky section in front of the current section,
							// then take into account the position this section
							var $prevStickySection = this.$container
								.closest('.l-section')
								.prevAll( '.l-section.type_sticky' );

							if ( $prevStickySection.length ) {
								top -= parseInt( $prevStickySection.outerHeight( true ) );
							}

							// Animate options
							var animateOptions = {
								duration: $us.canvasOptions.scrollDuration,
								easing: $us.getAnimationName( 'easeInOutExpo' ),
								/**
								 * @return void
								 */
								start: function() {
									this.isScrolling = true;
								}.bind( this ),
								/**
								 * @return void
								 */
								always: function() {
									this.isScrolling = false;
								}.bind( this ),
								/**
								 * Get and applying new values during animation
								 * @param number now
								 * @param object fx
								 * @return void
								 */
								step: function( now, fx ) {
									var newTop = top;
									// Since the header at the moment of scrolling the scroll can change the height,
									// we will correct the position of the element
									if ( $us.header.isStickyEnabled() ) {
										newTop -= this.headerHeight;
									}
									if ( fx.end !== newTop ) {
										$us.$htmlBody
											.stop( true, false )
											.animate( { scrollTop: newTop }, $.extend( animateOptions, {
												easing: $us.getAnimationName( 'easeOutExpo' )
											} ) );
									}
								}.bind( this )
							};
							$us.$htmlBody
								.stop( true, false )
								.animate( { scrollTop: top }, animateOptions );
							this.headerClicked = false;
						}
					}.bind( this ) );
				this.$sections
					.removeClass( 'active' );
				this.sections[ index ]
					.addClass( 'active' );
			} else if ( this.curLayout === 'ver' ) {
				if ( this.contents[ this.active[ 0 ] ] !== undefined ) {
					this.contents[ this.active[ 0 ] ]
						.css( 'display', 'none' )
						.attr( 'aria-expanded', 'false' );
				}
				this.contents[ index ]
					.css( 'display', 'none' )
					.attr( 'aria-expanded', 'false' )
					.stop( true, true )
					.fadeIn( this.options.duration, this._events.contentChanged );
				this.$sections
					.removeClass( 'active' );
				this.sections[ index ]
					.addClass( 'active' );
			}

			this._events.contentChanged();
			this.$tabs.removeClass( 'active' );
			this.tabs[ index ].addClass( 'active' );
			this.active[ 0 ] = index;

			if ( this.isTrendy && ( this.curLayout === 'hor' || this.curLayout === 'ver' ) ) {
				this.$tabsBar.performCSSTransition( this.barPosition( index ), this.options.duration, null, this.options.easing );
			}
		},

		/**
		 * Toggle some togglable accordion section
		 *
		 * @param index
		 */
		toggleSection: function( index ) {
			// (!) Can only be used within accordion state
			var indexPos = $.inArray( index, this.active );
			if ( indexPos != - 1 ) {
				this.contents[ index ]
					.css( 'display', 'block' )
					.attr( 'aria-expanded', 'true' )
					.slideUp( this.options.duration, this._events.contentChanged );
				this.tabs[ index ]
					.removeClass( 'active' );
				this.sections[ index ]
					.removeClass( 'active' );
				this.active.splice( indexPos, 1 );
			} else {
				this.contents[ index ]
					.css( 'display', 'none' )
					.attr( 'aria-expanded', 'false' )
					.slideDown( this.options.duration, this._events.contentChanged );
				this.tabs[ index ]
					.addClass( 'active' );
				this.sections[ index ]
					.addClass( 'active' );
				this.active.push( index );
			}
		},

		/**
		 * Resize-driven logics
		 */
		resize: function() {
			this.width = this.$container.innerWidth();
			this.$tabsList.removeClass( 'hidden' );

			// Tabs in navigation appear as tabs
			if (
				this.curLayout !== 'accordion'
				&& ! this.width
				&& this.$container.closest( '.w-nav' ).length
				&& ! jQuery.isMobile
			) {
				return;
			}

			// Basic layout maybe overriden
			var nextLayout = ( this.width < this.minWidth )
				? 'accordion'
				: this.basicLayout;
			if ( nextLayout !== this.curLayout ) {
				this.switchLayout( nextLayout );
			}
			if ( this.curLayout !== 'accordion' ) {
				this.measure();
			}

			// Fixing tabs display
			if ( this.curLayout === 'hor' ) {
				this.$container.addClass( 'autoresize' );
				this.$sectionsWrapper.css( 'width', this.width );
				this.$sectionsHelper.css( 'width', this.count * this.width );
				this.$sections.css( 'width', this.width );
				if ( this.contents[ this.active[ 0 ] ] !== undefined ) {
					this.$sectionsHelper.css( 'left', - this.width * ( this.isRtl ? ( this.count - this.active[ 0 ] - 1 ) : this.active[ 0 ] ) );
					var height = this.sections[ this.active[ 0 ] ].height();
					this.$sectionsWrapper.css( 'height', height );
				}
			}
			this._events.contentChanged();

			if ( this.isTrendy && ( this.curLayout === 'hor' || this.curLayout === 'ver' ) ) {
				this.$tabsBar.css( this.barPosition( this.active[ 0 ] ), this.options.duration, null, this.options.easing );
			}
		}
	};

	$.fn.wTabs = function( options ) {
		return this.each( function() {
			$( this ).data( 'wTabs', new $us.WTabs( this, options ) );
		} );
	};

	jQuery( '.w-tabs' ).wTabs();

}( jQuery );

/* RevSlider support for our tabs */
jQuery( function( $ ) {
	$( '.w-tabs .rev_slider' ).each( function() {
		var $slider = $( this );
		$slider.bind( "revolution.slide.onloaded", function( e ) {
			$us.$canvas.on( 'contentChange', function() {
				$slider.revredraw();
			} );
		} );
	} );
} );
