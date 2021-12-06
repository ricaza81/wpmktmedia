/**
 * UpSolution Element: Tabs
 *
 * @requires $us.canvas
 */
! function( $, undefined ) {
	"use strict";

	var _undefined = undefined;

	$us.WTabs = function( container, options ) {
		this.init( container, options );
	};

	$us.WTabs.prototype = {

		init: function( container, options ) {
			// Setting options
			var _defaults = {
				duration: 300,
				easing: 'cubic-bezier(.78,.13,.15,.86)'
			};
			this.options = $.extend( {}, _defaults, options );
			this.isRtl = $( '.l-body' ).hasClass( 'rtl' );

			// Commonly used dom elements
			this.$container = $( container );
			this.$tabsList = $( '> .w-tabs-list:first',  this.$container );
			this.$tabs = $( '.w-tabs-item', this.$tabsList );
			this.$sectionsWrapper = $( '> .w-tabs-sections:first', this.$container );
			this.$sections = $( '> .w-tabs-section', this.$sectionsWrapper );
			this.$headers = this.$sections.children( '.w-tabs-section-header' );
			this.$contents = this.$sections.children( '.w-tabs-section-content' );
			this.$line_charts = $( '.vc_line-chart', this.$container );
			this.$round_charts = $( '.vc_round-chart', this.$container );
			this.$tabsBar = $();

			// Overriding specific to Web Accessibility, it is not allowed to have several identical id and aria-content, aria-control.
			// http://web-accessibility.carnegiemuseums.org/code/accordions/
			if ( this.$container.hasClass( 'accordion' ) ) {
				this.$tabs = this.$headers;
			}

			// Class variables
			this.accordionAtWidth = this.$container.data( 'accordion-at-width' );
			this.align = this.$tabsList.usMod( 'align' );
			this.count = this.$tabs.length;
			this.hasScrolling = this.$container.hasClass( 'has_scrolling' ) || false;
			this.isAccordionAtWidth = $.isNumeric( parseInt( this.accordionAtWidth ) );
			this.isScrolling = false;
			this.isTogglable = ( this.$container.usMod( 'type' ) === 'togglable' );
			this.minWidth = 0; // Container width at which we should switch to accordion layout.
			this.tabHeights = [];
			this.tabLefts = [];
			this.tabTops = [];
			this.tabWidths = [];
			this.width = 0;

			// If there are no tabs, abort further execution.
			if ( this.count === 0 ) {
				return;
			}

			// Basic layout
			this.basicLayout = this.$container.hasClass( 'accordion' )
				? 'accordion'
				: ( this.$container.usMod( 'layout' ) || 'hor' );

			// Current active layout (may be switched to 'accordion').
			this.curLayout = this.basicLayout;

			// Array of active tabs indexes.
			this.active = [];
			this.activeOnInit = [];
			this.definedActive = [];

			// Preparing arrays of jQuery objects for easier manipulating in future.
			this.tabs = $.map( this.$tabs.toArray(), $ );
			this.sections = $.map( this.$sections.toArray(), $ );
			this.headers = $.map( this.$headers.toArray(), $ );
			this.contents = $.map( this.$contents.toArray(), $ );

			// Do nothing it there are no sections.
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
						// Toggling accordion sections.
						if ( this.curLayout === 'accordion' && this.isTogglable ) {
							// Cannot toggle the only active item.
							this.toggleSection( index );
						}
						// Setting tabs active item.
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
								this.active[ 0 ] = _undefined;
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
					// Stop animation when scrolling wheel.
					if ( this.isScrolling ) {
						$us.$htmlBody.stop( true, false );
					}
				}
			};

			// Starting everything.
			this.switchLayout( this.curLayout );

			$us.$window
				.on( 'resize', $us.debounce( this._events.resize, 5 ) )
				.on( 'hashchange', this._events.hashchange )
				.on( 'wheel', $us.debounce( this._events.wheel.bind( this ), 5 ) );

			$us.$document.ready( function() {
				this.resize();
				$us.timeout( this._events.resize, 50 );
				$us.timeout( function() {
					// TODO: move to a class function for code reading improvement.
					// Open tab on page load by hash.
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

			// Support for external links to tabs.
			$.each( this.tabs, function( index ) {
				if ( this.headers.length && this.headers[ index ].attr( 'href' ) != _undefined ) {
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

			// Gets the height of the header after animation.
			this.headerHeight = 0;
			$us.header.on( 'transitionEnd', function( header ) {
				this.headerHeight = header.getCurrentHeight();
			}.bind( this ) );

			if ( $us.usbPreview ) {
				/**
				 * Change handler via builder
				 */
				var usbContentChange = function() {
					if ( ! this.isTrendy() || this.curLayout == 'accordion') {
						return;
					}
					this.measure();
					// Set bar position for certain element index and current layout
					this.setBarPosition( this.active[ 0 ] || 0 );
				}.bind( this );
				this.$container // Watches changes in the builder
					.on( 'usb.contentChange', $us.debounce( usbContentChange, 1 ) );
			}
		},

		/**
		 * Determines if trendy style (Material style)
		 *
		 * @return {boolean} True if trendy, False otherwise
		 */
		isTrendy: function() {
			return this.$container.hasClass( 'style_trendy' );
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
		 * Clean up layout's special inline styles and/or dom elements.
		 *
		 * @param from
		 */
		cleanUpLayout: function( from ) {
			this.$sections.resetInlineCSS( 'display' );

			if ( from === 'accordion' ) {
				this.$container.removeClass( 'accordion' );
				this.$contents.resetInlineCSS( 'height', 'padding-top', 'padding-bottom', 'display', 'opacity' );
			}

			if ( this.isTrendy() && 'hor|ver'.indexOf( from ) > -1 ) {
				this.$tabsBar.remove();
			}
		},

		/**
		 * Apply layout's special inline styles and/or dom elements.
		 *
		 * @param to
		 */
		prepareLayout: function( to ) {
			if ( to !== 'accordion' && this.active[ 0 ] === _undefined ) {
				this.active[ 0 ] = this.activeOnInit[ 0 ];
				if ( this.active[ 0 ] !== _undefined ) {
					this.tabs[ this.active[ 0 ] ]
						.addClass( 'active' );
					this.sections[ this.active[ 0 ] ]
						.addClass( 'active' );
				}
			}

			if ( to === 'accordion' ) {
				this.$container.addClass( 'accordion' );
				this.$contents.hide();
				if ( this.curLayout !== 'accordion' && this.active[ 0 ] !== _undefined && this.active[ 0 ] !== this.definedActive[ 0 ] ) {
					this.headers[ this.active[ 0 ] ]
						.removeClass( 'active' );
					this.tabs[ this.active[ 0 ] ]
						.removeClass( 'active' );
					this.sections[ this.active[ 0 ] ]
						.removeClass( 'active' );
					this.active[ 0 ] = this.definedActive[ 0 ];

				}
				for ( var i = 0; i < this.active.length; i ++ ) {
					if ( this.contents[ this.active[ i ] ] !== _undefined ) {
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

			if ( this.isTrendy() && 'hor|ver'.indexOf( this.curLayout ) > -1 ) {
				this.$tabsBar = $( '<div class="w-tabs-list-bar"></div>' )
					.appendTo( this.$tabsList );
			}
		},

		/**
		 * Measure needed sizes.
		 */
		measure: function() {
			if ( this.basicLayout === 'ver' ) {
				// Get the specified minimum width or determine automatically
				if ( this.isAccordionAtWidth ) {
					this.minWidth = this.accordionAtWidth;
				} else {
					var // Measuring minimum tabs width.
						minTabWidth = this.$tabsList.outerWidth( true ),
						// Static value fo min content width
						minContentWidth = 300,
						// Measuring minimum tabs width for percent-based sizes.
						navWidth = this.$container.usMod( 'navwidth' );

					if ( navWidth !== 'auto' ) {
						minTabWidth = Math.max( minTabWidth, minContentWidth * parseInt( navWidth ) / ( 100 - parseInt( navWidth ) ) );
					}
					this.minWidth = Math.max( 480, minContentWidth + minTabWidth + 1 )
				}

				if ( this.isTrendy() ) {
					this.tabHeights = [];
					this.tabTops = [];
					for ( var index = 0; index < this.tabs.length; index ++ ) {
						this.tabHeights.push( this.tabs[ index ].outerHeight( true ) );
						this.tabTops.push(
							index
								? ( this.tabTops[ index - 1 ] + this.tabHeights[ index - 1 ] )
								: 0
						);
					}
				}

			} else {
				if ( this.basicLayout === 'hor' ) {
					this.$container.addClass( 'measure' );
					// Get the specified minimum width or determine automatically
					if ( this.isAccordionAtWidth ) {
						this.minWidth = this.accordionAtWidth;
					} else {
						this.minWidth = 0;
						for ( var index = 0; index < this.tabs.length; index++ ) {
							this.minWidth += this.tabs[ index ].outerWidth( true );
						}
					}
					this.$container.removeClass( 'measure' );
				}

				if ( this.isTrendy() ) {
					this.tabWidths = [];
					this.tabLefts = [];
					for ( var index = 0; index < this.tabs.length; index ++ ) {
						this.tabWidths.push( this.tabs[ index ].outerWidth( true ) );
						this.tabLefts.push( index
								? ( this.tabLefts[ index - 1 ] + this.tabWidths[ index - 1 ] )
								: this.tabs[ index ].position().left
						);
					}
					// Offset correction for RTL version with Trendy enabled
					if ( this.isRtl ) {
						var
							// Get the width of the first tab
							firstTabWidth = this.tabWidths[ 0 ],
							// Get X offsets
							offset = ( 'none' == this.align )
								? this.$tabsList.outerWidth( true )
								: this.tabWidths // Get the total width of all tambours
									.reduce( function ( a, b ) { return a + b }, /* Default */0 );
						// Calculate position based on offset
						this.tabLefts = this.tabLefts
							.map( function ( left ) { return Math.abs( left - offset + firstTabWidth ) } );
					}
				}
			}
		},

		/**
		 * Set bar position for certain element index and current layout
		 *
		 * @param {number} index The index element
		 * @param {boolean} animated Animating an element when updating css
		 */
		setBarPosition: function( index, animated ) {
			if (
				index === _undefined
				|| ! this.isTrendy()
				|| 'hor|ver'.indexOf( this.curLayout ) == -1
			) {
				return;
			}
			// Add a bar to the document if it does not exist
			if ( ! this.$tabsBar.length ) {
				this.$tabsBar = $( '<div class="w-tabs-list-bar"></div>' )
					.appendTo( this.$tabsList );
			}
			// Get bar position for certain element index and current layout
			var css = {};
			if ( this.curLayout === 'hor' ) {
				css = { width: this.tabWidths[ index ] };
				css[ this.isRtl ? 'right' : 'left' ] = this.tabLefts[ index ];
			} else if ( this.curLayout === 'ver' ) {
				css = {
					top: this.tabTops[ index ],
					height: this.tabHeights[ index ]
				};
			}
			// Set css properties for a bar element
			if ( ! animated ) {
				this.$tabsBar.css( css );
			} else {
				this.$tabsBar
					.performCSSTransition( css, this.options.duration, null, this.options.easing );
			}
		},

		/**
		 * Open tab section.
		 *
		 * @param index int
		 */
		openSection: function( index ) {
			if ( this.sections[ index ] === _undefined ) {
				return;
			}
			if ( this.curLayout === 'hor' ) {
				this.$sections
					.removeClass( 'active' )
					.css( 'display', 'none' );
				this.sections[ index ]
					.stop( true, true )
					.fadeIn( this.options.duration, function() {
						$( this ).addClass( 'active' );
					} );
			} else if ( this.curLayout === 'accordion' ) {
				if ( this.contents[ this.active[ 0 ] ] !== _undefined ) {
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

						// Scrolling to the opened section
						if ( this.hasScrolling && this.curLayout === 'accordion' && this.headerClicked == true ) {
							var top = this.headers[ index ].offset().top;
							if ( ! jQuery.isMobile ) {
								top -= $us.$canvas.offset().top || 0;
							}
							// If there is a sticky section in front of the current section,
							// then take into account the position this section.
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
								start: function() {
									this.isScrolling = true;
								}.bind( this ),
								always: function() {
									this.isScrolling = false;
								}.bind( this ),
								/**
								 * Get and applying new values during animation.
								 *
								 * @param number now
								 * @param object fx
								 */
								step: function( now, fx ) {
									var newTop = top;
									// Since the header at the moment of scrolling the scroll can change the height,
									// we will correct the position of the element.
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
				if ( this.contents[ this.active[ 0 ] ] !== _undefined ) {
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

			// Set bar position for certain element index and current layout
			this.setBarPosition( index, /* animated */true );
		},

		/**
		 * Toggle some togglable accordion section.
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
			this.width = this.isAccordionAtWidth
				? $us.$window.outerWidth()
				: this.$container.width();

			// Skip changing Tabs into Accordion inside header menu on Mobiles
			if (
				this.curLayout !== 'accordion'
				&& ! this.width
				&& this.$container.closest( '.w-nav' ).length
				&& ! jQuery.isMobile
			) {
				return;
			}

			var nextLayout = ( this.width <= this.minWidth )
				? 'accordion'
				: this.basicLayout;
			if ( nextLayout !== this.curLayout ) {
				this.switchLayout( nextLayout );
			}
			if ( this.curLayout !== 'accordion' ) {
				this.measure();
			}

			this._events.contentChanged();
			// Set bar position for certain element index and current layout
			this.setBarPosition( this.active[ 0 ] );
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
