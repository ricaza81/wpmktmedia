/**
 * Base class to working with a $us.header
 * Dev note: should be initialized after $us.canvas
 */
! function( $, undefined ) {
	"use strict";

	/**
	 * @class USHeader
	 *
	 * @param {object} settings - The header settings
	 * @return void
	 */
	function USHeader( settings ) {

		// Elements
		this.$container = $( '.l-header', $us.$canvas );
		this.$showBtn = $( '.w-header-show:first', $us.$body );

		// Variables
		this.settings = settings || {};
		this.state = 'default'; // possible values: default|tablets|mobiles
		this.$elms = {};

		if ( this.$container.length === 0 ) {
			return;
		}

		this.$places = {
			hidden: $( '.l-subheader.for_hidden', this.$container )
		};

		// Data for the current states of various settings
		this._states = {
			sticky: false,
			sticky_auto_hide: false,
			scroll_direction: 'down',
			vertical_scrollable: false,
			init_height: this.getHeight()
		};

		// Get the settings via css classes
		this.pos = this.$container.usMod( 'pos' ); // possible values: fixed|static
		this.bg = this.$container.usMod( 'bg' ); // possible values: solid|transparent
		this.shadow = this.$container.usMod( 'shadow' ); // possible values: none|thin|wide
		this.orientation = $us.$body.usMod( 'header' ); // possible values: hor|ver

		// Screen Width Breakpoints
		this.tabletsBreakpoint = parseInt( settings.tablets && settings.tablets.options && settings.tablets.options.breakpoint ) || /* Default */900;
		this.mobilesBreakpoint = parseInt( settings.mobiles && settings.mobiles.options && settings.mobiles.options.breakpoint ) || /* Default */600;

		// Get all places in the header
		$( '.l-subheader-cell', this.$container ).each( function( _, place ) {
			var $place = $( place ),
				key = $place.parent().parent().usMod( 'at' ) + '_' + $place.usMod( 'at' );
			this.$places[ key ] = $place;
		}.bind( this ) );

		// Get all header elements and save them into the this.$elms list
		// example: menu:1, text:1, socials:1 etc.
		$( '[class*=ush_]', this.$container ).each( function( _, elm ) {
			var $elm = $( elm ),
				// Regular expression to find elements in the header via class names
				matches = /(^| )ush_([a-z_]+)_([0-9]+)(\s|$)/.exec( elm.className );
			if ( ! matches ) {
				return;
			}
			var id = matches[ 2 ] + ':' + matches[ 3 ];
			this.$elms[ id ] = $elm;
			// If the element is a wrapper, store it into the this.$places list
			if ( $elm.is( '.w-vwrapper, .w-hwrapper' ) ) {
				this.$places[ id ] = $elm;
			}
		}.bind( this ) );

		// Events
		$us.$window
			.on( 'scroll', $us.debounce( this._events.scroll.bind( this ), 10 ) )
			.on( 'resize load', $us.debounce( this._events.resize.bind( this ), 10 ) );
		this.$container
			.on( 'contentChange', this._events.contentChange.bind( this ) );
		this.$showBtn
			.on( 'click', this._events.showBtn.bind( this ) );
		this // Private events
			.on( 'changeSticky', this._events._changeSticky.bind( this ) )
			.on( 'swichVerticalScrollable', this._events._swichVerticalScrollable.bind( this ) );

		this._events.resize.call( this );

		// If auto-hide is enabled, then add a class for the css styles to work correctly
		if ( this.isStickyAutoHideEnabled() ) {
			this.$container
				.addClass( 'sticky_auto_hide' );
		}

		// Triggering an event in the internal event system, this will allow subscribing
		// to external scripts to understand when the animation ends in the header
		this.$container
			.on( 'transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', function() {
				$us.debounce( this.trigger.bind( this, 'transitionEnd' ), 1 )();
			}.bind( this ) );
	}

	// Export API
	$.extend( USHeader.prototype, $us.mixins.Events, {

		// Stores the previous scroll position to determine the direction of scrolling
		prevScrollTop: 0,

		/**
		 * Checks if given state is current state.
		 *
		 * @param {string} state State to be compared with.
		 * @return {boolean} True if the state matches, False otherwise.
		 */
		currentStateIs: function( state ) {
			return ( state && ['default', 'tablets', 'mobiles'].indexOf( state ) !== - 1 && this.state === state );
		},

		/**
		 * Determines if the header is vertical.
		 *
		 * @return {boolean} True if vertical, False otherwise.
		 */
		isVertical: function() {
			return this.orientation === 'ver';
		},

		/**
		 * Determines if the header is horizontal.
		 *
		 * @return {boolean} True if horizontal, False otherwise.
		 */
		isHorizontal: function() {
			return this.orientation === 'hor';
		},

		/**
		 * Determines if the header is fixed.
		 *
		 * @return {boolean} True if fixed, False otherwise.
		 */
		isFixed: function() {
			return this.pos === 'fixed';
		},

		/**
		 * Determines if the header is transparent.
		 *
		 * @return {boolean} True if transparent, False otherwise.
		 */
		isTransparent: function() {
			return this.bg === 'transparent';
		},

		/**
		 * Safari overscroll Fix.
		 *
		 * @private This private method is not intended to be called by other scripts.
		 * @param {number} scrollTop The scroll top.
		 * @return {boolean} True if within scroll boundaries, False otherwise.
		 */
		_isWithinScrollBoundaries: function( scrollTop ) {
			scrollTop = parseInt( scrollTop );
			return ( scrollTop + window.innerHeight >= $us.$document.height() ) || scrollTop <= 0;
		},

		/**
		 * Check if the header is hidden.
		 *
		 * @return {boolean} True if hidden, False otherwise.
		 */
		isHidden: function() {
			return !! $us.header.settings.is_hidden;
		},

		/**
		 * Check if sticky is enabled.
		 *
		 * @return {boolean} True if sticky is enabled, False otherwise.
		 */
		isStickyEnabled: function() {
			return this.settings[ this.state ].options.sticky || false;
		},

		/**
		 * Check if sticky auto hide is enabled.
		 *
		 * @return {boolean} True if sticky auto hide is enabled, False otherwise.
		 */
		isStickyAutoHideEnabled: function() {
			return this.isStickyEnabled() && ( this.settings[ this.state ].options.sticky_auto_hide || false );
		},

		/**
		 * Check if sticky.
		 *
		 * @return {boolean} True if sticky, False otherwise.
		 */
		isSticky: function() {
			return this._states.sticky || false;
		},

		/**
		 * Check if the header is in automatic hide state.
		 *
		 * @return {boolean} True if in automatic hide state, False otherwise.
		 */
		isStickyAutoHidden: function() {
			return this._states.sticky_auto_hide || false;
		},

		/**
		 * Get the scroll direction.
		 *
		 * @return {string} Scroll direction.
		 */
		getScrollDirection: function() {
			return this._states.scroll_direction || 'down';
		},

		/**
		 * Get the height of admin bar.
		 *
		 * @return {number} Height of admin bar.
		 */
		getAdminBarHeight: function() {
			var $wpAdminBar = $( '#wpadminbar', $us.$body );
			return $wpAdminBar.length
				? parseInt( $wpAdminBar.height() )
				: 0;
		},

		/**
		 * Get the header height in px.
		 *
		 * This method returns the actual height of the header taking into account
		 * all settings in the current position.
		 *
		 * @return {number} The header height.
		 */
		getHeight: function() {
			var height = 0,
				// Get height value for .l-header through pseudo-element css ( content: 'value' );
				beforeContent = getComputedStyle( this.$container.get( 0 ), ':before' ).content;

			// This approach is used to determine the correct height if there are lazy-load images in the header.
			if ( beforeContent && ['none', 'auto'].indexOf( beforeContent ) === - 1 ) {
				// Delete all characters except numbers
				height = beforeContent.replace( /[^+\d]/g, '' );
			}

			// This is an alternative height if there is no data from css, this option does not work
			// correctly if the header contains images from lazy-load, but it still makes the header work more reliable
			// NOTE: Used in a vertical header that ignores pseudo-element :before!
			if ( ! height ) {
				height = this.$container.outerHeight();
			}

			return ! isNaN( height )
				? parseInt( height )
				: 0;
		},

		/**
		 * Get the initial height.
		 *
		 * @return {number} Initial height.
		 */
		getInitHeight: function() {
			return parseInt( this._states.init_height ) || this.getHeight();
		},

		/**
		 * Get current header height in px.
		 *
		 * This method returns the height of the header,
		 * taking into account all settings that may affect the height at the time of the call of the current method.
		 *
		 * @return {number} Current header height + admin bar height if displayed.
		 */
		getCurrentHeight: function() {
			var height = 0,
				adminBarHeight = this.getAdminBarHeight();

			// If there is an admin bar, add its height to the height
			if (
				this.isHorizontal()
				&& (
					! this.currentStateIs( 'mobiles' )
					|| ( adminBarHeight && adminBarHeight >= this.getScrollTop() )
				)
			) {
				height += adminBarHeight;
			}

			// Adding the header height if it is not hidden
			if ( ! this.isStickyAutoHidden() ) {
				height += this.getHeight();
			}

			return height;
		},

		/**
		 * Get the scroll top.
		 *
		 * In this method, the scroll position includes an additional check of the previous value.
		 *
		 * @return {number} Scroll top.
		 */
		getScrollTop: function() {
			return parseInt( $us.$window.scrollTop() ) || this.prevScrollTop;
		},

		/**
		 * Gets the offset top.
		 *
		 * @return {number} The offset top.
		 */
		getOffsetTop: function() {
			var top = parseInt( this.$container.css( 'top' ) );
			return ! isNaN( top ) ? top : 0;
		},

		/**
		 * Determines if scroll at the top position.
		 *
		 * @return {boolean} True if scroll at the top position, False otherwise.
		 */
		isScrollAtTopPosition: function() {
			return parseInt( $us.$window.scrollTop() ) === 0;
		},

		/**
		 * Set the state.
		 *
		 * @param {string} state The new state.
		 * @return void
		 */
		setState: function( state ) {
			if ( this.currentStateIs( state ) ) {
				return;
			}

			var options = this.settings[ state ].options || {},
				orientation = options.orientation || 'hor',
				pos = ( $us.toBool( options.sticky ) ? 'fixed' : 'static' ),
				bg = ( $us.toBool( options.transparent ) ? 'transparent' : 'solid' ),
				shadow = options.shadow || 'thin';

			if ( orientation === 'ver' ) {
				pos = 'fixed';
				bg = 'solid';
			}

			// Dev note: don't change the order: orientation -> pos -> bg -> layout
			this._setOrientation( orientation );
			this._setPos( pos );
			this._setBg( bg );
			this._setShadow( shadow );
			this._setLayout( this.settings[ state ].layout || {} );
			$us.$body.usMod( 'state', this.state = state );

			if ( this.currentStateIs( 'default' ) ) {
				$us.$body.removeClass( 'header-show' );
			}

			// Updating the menu because of dependencies
			if ( $us.nav !== undefined ) {
				$us.nav.resize();
			}

			if ( this.isStickyAutoHideEnabled() ) {
				this.$container.removeClass( 'down' );
			}
		},

		/**
		 * Set new position.
		 *
		 * @private This private method is not intended to be called by other scripts.
		 * @param {string} pos New position (possible values: fixed|static).
		 * @return void
		 */
		_setPos: function( pos ) {
			if ( pos === this.pos ) {
				return;
			}
			this.$container.usMod( 'pos', this.pos = pos );
			if ( this.pos === 'static' ) {
				this.trigger( 'changeSticky', false );
			}
		},

		/**
		 * Set the background.
		 *
		 * @private This private method is not intended to be called by other scripts.
		 * @param {string} bg New background (possible values: solid|transparent)
		 * @return void
		 */
		_setBg: function( bg ) {
			if ( bg != this.bg ) {
				this.$container.usMod( 'bg', this.bg = bg );
			}
		},

		/**
		 * Set the shadow.
		 *
		 * @private This private method is not intended to be called by other scripts.
		 * @param {string} shadow New shadow (possible values: none|thin|wide)
		 * @return void
		 */
		_setShadow: function( shadow ) {
			if ( shadow != this.shadow ) {
				this.$container.usMod( 'shadow', this.shadow = shadow );
			}
		},

		/**
		 * Set the layout.
		 *
		 * @private This private method is not intended to be called by other scripts.
		 * @param {string} value New layout.
		 * @return void
		 */
		_setLayout: function( layout ) {
			for ( var place in layout ) {
				if ( ! layout[ place ] || ! this.$places[ place ] ) {
					continue;
				}
				this._placeElements( layout[ place ], this.$places[ place ] );
			}
		},

		/**
		 * Sets the orientation.
		 *
		 * @private This private method is not intended to be called by other scripts.
		 * @param {string} orientation New orientation ( possible values: hor|ver ).
		 * @return void
		 */
		_setOrientation: function( orientation ) {
			if ( orientation != this.orientation ) {
				$us.$body.usMod( 'header', this.orientation = orientation );
			}
		},

		/**
		 * Recursive function to place elements based on their ids
		 *
		 * @private This private method is not intended to be called by other scripts.
		 * @param {array} elms This is a list of all the elements in the header.
		 * @param {jqueryObject} $place
		 * @return void
		 */
		_placeElements: function( elms, $place ) {
			for ( var i = 0; i < elms.length; i ++ ) {
				var elmId;
				if ( typeof elms[ i ] == 'object' ) {
					// Wrapper
					elmId = elms[ i ][ 0 ];
					if ( ! this.$places[ elmId ] || ! this.$elms[ elmId ] ) {
						continue;
					}
					this.$elms[ elmId ].appendTo( $place );
					this._placeElements( elms[ i ].shift(), this.$places[ elmId ] );
				} else {
					// Element
					elmId = elms[ i ];
					if ( ! this.$elms[ elmId ] ) {
						continue;
					}
					this.$elms[ elmId ].appendTo( $place );
				}
			}
		},

		/**
		 * Check vertical scrolling capability for the header
		 *
		 * This method compares the header height and the window height
		 * and optionally enables or disables scrolling for the header content.
		 *
		 * @private This private method is not intended to be called by other scripts.
		 * @return void
		 */
		_isVerticalScrollable: function() {
			if ( ! this.isVertical() ) {
				return;
			}

			if (
				this.currentStateIs( 'default' )
				&& this.isFixed()
			) {
				// Initially, let's add a class to override the styles and get the correct values.
				this.$container.addClass( 'scrollable' );

				var headerHeight = this.getHeight(),
					canvasHeight = parseInt( $us.canvas.winHeight ),
					documentHeight = parseInt( $us.$document.height() );

				// Removing a class after getting all values
				this.$container.removeClass( 'scrollable' );

				if ( headerHeight > canvasHeight ) {
					this.trigger( 'swichVerticalScrollable', true );

				} else if ( this._states.vertical_scrollable ) {
					this.trigger( 'swichVerticalScrollable', false );
				}

				if ( headerHeight > documentHeight ) {
					this.$container.css( {
						position: 'absolute',
						top: 0
					} );
				}

				// Remove ability to scroll header
			} else if ( this._states.vertical_scrollable ) {
				this.trigger( 'swichVerticalScrollable', false );
			}
		},

		/**
		 * Event handlers
		 *
		 * @private
		 */
		_events: {
			/**
			 * Switch vertical scroll for the header.
			 *
			 * @private This private handler is intended for the needs of the current script.
			 * @param {object} _ The self object.
			 * @param {boolean} state Is scrollable.
			 * @return void
			 */
			_swichVerticalScrollable: function( _, state ) {
				this.$container
					.toggleClass( 'scrollable', this._states.vertical_scrollable = !! state );
				if ( ! this._states.vertical_scrollable ) {
					this.$container
						.resetInlineCSS( 'position', 'top', 'bottom' );
					delete this._headerScrollRange;
				}
			},

			/**
			 * Change the state of the sticky header.
			 *
			 * @private This private handler is intended for the needs of the current script.
			 * @param {object} _ The self object.
			 * @param {boolean} state Is sticky.
			 * @return void
			 */
			_changeSticky: function( _, state ) {
				this._states.sticky = !! state;
				var currentHeight = this.getCurrentHeight();
				// Let's limit the number of calls to the DOM element.
				$us.debounce( function() {
					this.$container
						.toggleClass( 'sticky', this._states.sticky )
						// Reset the indent if it was set.
						.resetInlineCSS( 'position', 'top', 'bottom' );
					// If the height of the header after sticky does not change, we will fire an
					// event so that additional libraries know that the change has occurred
					if ( currentHeight == this.getCurrentHeight() ) {
						this.trigger( 'transitionEnd' );
					}
				}.bind( this ), 10 )();
			},

			/**
			 * Content change event.
			 *
			 * @return void
			 */
			contentChange: function() {
				this._isVerticalScrollable.call( this );
			},

			/**
			 * Show the button.
			 *
			 * @param {object} e The jQuery event object.
			 * @return void
			 */
			showBtn: function( e ) {
				if ( $us.$body.hasClass( 'header-show' ) ) {
					return;
				}
				e.stopPropagation();
				$us.$body
					.addClass( 'header-show' )
					.on( ( $.isMobile ? 'touchstart' : 'click' ), this._events.hideMobileVerticalHeader.bind( this ) );
			},

			/**
			 * Hide mobile vertical header.
			 *
			 * @param {object} e The jQuery event object.
			 */
			hideMobileVerticalHeader: function( e ) {
				if ( $.contains( this.$container[ 0 ], e.target ) ) {
					return;
				}
				$us.$body
					.off( ( $.isMobile ? 'touchstart' : 'click' ), this._events.hideMobileVerticalHeader.bind( this ) );
				$us.timeout( function() {
					$us.$body.removeClass( 'header-show' );
				}, 10 );
			},

			/**
			 * Page scroll event.
			 *
			 * Dev note: This event is fired very often when the page is scrolled.
			 *
			 * @return void
			 */
			scroll: function() {
				// Get the current scroll position.
				var scrollTop = this.getScrollTop(),
					// The header is hidden but when scrolling appears at the top of the page.
					headerAbovePosition = ( $us.canvas.headerInitialPos === 'above' );

				// Case `this.prevScrollTop == scrollTop` must be excluded, since we will not be able
				// to determine the direction correctly. And this can cause crashes.
				if ( this.prevScrollTop != scrollTop ) {
					// Saving scroll direction
					this._states.scroll_direction = ( this.prevScrollTop <= scrollTop )
						? 'down'
						: 'up';
				}
				this.prevScrollTop = scrollTop;

				// Check if the scroll is in the `up` position,
				// if so, forcibly set scroll direction to 'up' so the header is shown.
				if ( this.isScrollAtTopPosition() ) {
					this._states.scroll_direction = 'up';
				}

				// Sets the class of the scroll state by which the header will be either shown or hidden.
				if (
					this.isStickyAutoHideEnabled()
					&& this.isSticky()
					&& ! this._isWithinScrollBoundaries( scrollTop )
					&& ! headerAbovePosition
				) {
					this._states.sticky_auto_hide = ( this.getScrollDirection() === 'down' );
					this.$container.toggleClass( 'down', this._states.sticky_auto_hide );
				}

				// If the position of the header is not fixed, then we will abort following processing.
				if ( ! this.isFixed() ) {
					return;
				}

				// Header is attached to the first section bottom or below position
				var headerAttachedFirstSection = ['bottom', 'below'].indexOf( $us.canvas.headerInitialPos ) !== - 1;

				// Logic for a horizontal header located at the top of the page
				if (
					this.isHorizontal()
					&& (
						headerAbovePosition
						|| (
							// Forced for tablets and mobiles devices. This is done in order to avoid on small screens mismatched cases
							// with a mobile menu and other header elements when it is NOT on top
							headerAttachedFirstSection
							&& ! this.currentStateIs( 'default' )
						)
						|| ! headerAttachedFirstSection
					)
				) {
					if ( this.isStickyEnabled() ) {
						// We observe the movement of the scroll and when the change breakpoint is reached, we will launch
						// the event
						var scrollBreakpoint = parseInt( this.settings[ this.state ].options.scroll_breakpoint ) || /* Default */100,
							isSticky = scrollTop >= scrollBreakpoint;
						if ( isSticky != this.isSticky() ) {
							this.trigger( 'changeSticky', isSticky );
						}
					}

					// Additional check for delay scroll position as working with the DOM can take time
					if ( this.isSticky() ) {
						$us.debounce( function() {
							if ( ! $us.$window.scrollTop() ) {
								this.trigger( 'changeSticky', false );
							}
						}.bind( this ), 1 )();
					}
				}

				// Logic for a horizontal header located at the bottom or below the first section,
				// these checks only work for default (desktop) devices.
				if (
					this.isHorizontal()
					&& headerAttachedFirstSection
					&& ! headerAbovePosition
					&& this.currentStateIs( 'default' )
				) {
					// The height of the first section for placing the header under it
					var top = ( $us.canvas.getHeightFirstSection() + this.getAdminBarHeight() );

					// The calculate height of the header from the height of the first section
					// so that it is at the bottom of the first section
					if ( $us.canvas.headerInitialPos == 'bottom' ) {
						top -= this.getInitHeight();
					}

					// Checking the position of the header relative to the scroll to sticky it at the page top
					if ( this.isStickyEnabled() ) {
						var isSticky = scrollTop >= top;
						if ( isSticky != this.isSticky() ) {
							$us.debounce( function() {
								this.trigger( 'changeSticky', isSticky );
							}.bind( this ), 1 )();
						}
					}

					// Sets the heading padding if the heading should be placed at the bottom or below the first
					// section
					if ( ! this.isSticky() && top != this.getOffsetTop() ) {
						this.$container.css( 'top', top );
					}
				}

				// Logic for a vertical header located on the left or right,
				// with content scrolling implemented
				var headerHeight = this.getHeight(),
					documentHeight = parseInt( $us.$document.height() );

				if (
					this.isVertical()
					&& ! headerAttachedFirstSection
					&& ! headerAbovePosition
					&& ! jQuery.isMobile
					&& this._states.vertical_scrollable
					&& documentHeight > headerHeight
				) {
					var canvasHeight = parseInt( $us.canvas.winHeight ),
						scrollRangeDiff = ( headerHeight - canvasHeight ),
						cssProps;

					if ( this._headerScrollRange === undefined ) {
						this._headerScrollRange = [ 0, scrollRangeDiff ];
					}

					if ( scrollTop <= this._headerScrollRange[ 0 ] ) {
						this._headerScrollRange[ 0 ] = Math.max( 0, scrollTop );
						this._headerScrollRange[ 1 ] = ( this._headerScrollRange[ 0 ] + scrollRangeDiff );
						cssProps = {
							position: 'fixed',
							top: this.getAdminBarHeight()
						};
					} else if (
						this._headerScrollRange[ 0 ] < scrollTop
						&& scrollTop < this._headerScrollRange[ 1 ]
					) {
						cssProps = {
							position: 'absolute',
							top: this._headerScrollRange[ 0 ]
						};
					} else if ( this._headerScrollRange[ 1 ] <= scrollTop ) {
						this._headerScrollRange[ 1 ] = Math.min( documentHeight - canvasHeight, scrollTop );
						this._headerScrollRange[ 0 ] = ( this._headerScrollRange[ 1 ] - scrollRangeDiff );
						cssProps = {
							position: 'fixed',
							top: ( canvasHeight - headerHeight )
						};
					}

					// Add styles from variable cssProps
					if ( cssProps ) {
						this.$container.css( cssProps );
					}
				}
			},

			/**
			 * This method is called every time the browser window is resized.
			 *
			 * @return void
			 */
			resize: function() {

				// Determine the state based on the current size of the browser window
				var newState = 'default';
				if ( window.innerWidth < this.tabletsBreakpoint ) {
					newState = ( window.innerWidth < this.mobilesBreakpoint )
						? 'mobiles'
						: 'tablets';
				}
				this.setState( newState );

				// Stop all transitions of CSS animations
				if ( this.isFixed() && this.isHorizontal() ) {
					this.$container.addClass( 'notransition' )

					// Remove class with a small delay to prevent css glitch
					$us.timeout( function() {
						this.$container.removeClass( 'notransition' );
					}.bind( this ), 50 );
				}

				this._isVerticalScrollable.call( this );
				this._events.scroll.call( this );
			}
		}
	} );

	// Init header
	$us.header = new USHeader($us.headerSettings || {} );
}( window.jQuery );
