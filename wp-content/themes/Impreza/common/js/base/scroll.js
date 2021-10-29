/**
 * $us.scroll
 *
 * ScrollSpy, Smooth scroll links and hash-based scrolling all-in-one.
 *
 * @requires $us.canvas
 */
! function( $ ) {
	"use strict";

	function USScroll( options ) {

		/**
		 * Setting options.
		 *
		 * @type {{}}
		 */
		var defaults = {
			/**
			 * @param {String|jQuery} Selector or object of hash scroll anchors that should be attached on init.
			 */
			attachOnInit: '\
				.menu-item a[href*="#"],\
				.menu-item[href*="#"],\
				a.w-btn[href*="#"]:not([onclick]),\
				.w-text a[href*="#"],\
				.vc_icon_element a[href*="#"],\
				.vc_custom_heading a[href*="#"],\
				a.w-grid-item-anchor[href*="#"],\
				.w-toplink,\
				.w-image a[href*="#"]:not([onclick]),\
				.w-iconbox a[href*="#"],\
				.w-comments-title a[href*="#"],\
				a.smooth-scroll[href*="#"]',
			/**
			 * @param {String} Classname that will be toggled on relevant buttons.
			 */
			buttonActiveClass: 'active',
			/**
			 * @param {String} Classname that will be toggled on relevant menu items.
			 */
			menuItemActiveClass: 'current-menu-item',
			/**
			 * @param {String} Classname that will be toggled on relevant menu ancestors.
			 */
			menuItemAncestorActiveClass: 'current-menu-ancestor',
			/**
			 * @param {Number} Duration of scroll animation.
			 */
			animationDuration: $us.canvasOptions.scrollDuration,
			/**
			 * @param {String} Easing for scroll animation.
			 */
			animationEasing: $us.getAnimationName( 'easeInOutExpo' ),

			/**
			 * @param {String} End easing for scroll animation.
			 */
			endAnimationEasing: $us.getAnimationName( 'easeOutExpo' )
		};
		this.options = $.extend( {}, defaults, options || {} );

		// Hash blocks with targets and activity indicators.
		this.blocks = {};

		// Is scrolling to some specific block at the moment?
		this.isScrolling = false;

		// Boundable events
		this._events = {
			cancel: this.cancel.bind( this ),
			scroll: this.scroll.bind( this ),
			resize: this.resize.bind( this )
		};

		this._canvasTopOffset = 0;
		$us.$window.on( 'resize load', $us.debounce( this._events.resize, 10 ) );
		$us.timeout( this._events.resize, 75 );

		$us.$window.on( 'scroll', this._events.scroll );
		$us.timeout( this._events.scroll, 75 );

		if ( this.options.attachOnInit ) {
			this.attach( this.options.attachOnInit );
		}

		// Recount scroll positions on any content changes.
		$us.$canvas.on( 'contentChange', this._countAllPositions.bind( this ) );

		// Handling initial document hash
		if ( document.location.hash && document.location.hash.indexOf( '#!' ) == - 1 ) {
			var hash = document.location.hash, scrollPlace = ( this.blocks[ hash ] !== undefined ) ? hash : undefined;
			if ( scrollPlace === undefined ) {
				try {
					var $target = $( hash );
					if ( $target.length != 0 ) {
						scrollPlace = $target;
					}
				}
				catch ( error ) {
					//Do not have to do anything here since scrollPlace is already undefined.
				}

			}
			if ( scrollPlace !== undefined ) {

				// While page loads, its content changes, and we'll keep the proper scroll on each sufficient content
				// change until the page finishes loading or user scrolls the page manually.
				var keepScrollPositionTimer = setInterval( function() {
					this.scrollTo( scrollPlace );
					// Additionally, let's check the states to avoid an infinite call.
					if ( document.readyState !== 'loading' ) {
						clearInterval( keepScrollPositionTimer );
					}
				}.bind( this ), 100 );
				var clearHashEvents = function() {
					$us.$window.off( 'load touchstart mousewheel DOMMouseScroll touchstart', clearHashEvents );
					// Content size still may change via other script right after page load
					$us.timeout( function() {
						$us.canvas._events.resize.call( $us.canvas );
						this._countAllPositions();
						// The size of the content can be changed using another script, so we recount the waypoints.
						if ( $us.hasOwnProperty( 'waypoints' ) ) {
							$us.waypoints._countAll();
						}
						this.scrollTo( scrollPlace );
					}.bind( this ), 100 );
				}.bind( this );
				$us.$window.on( 'load touchstart mousewheel DOMMouseScroll touchstart', clearHashEvents );
			}
		}

		// Gets the height of the header after animation.
		this.headerHeight = 0;
		this._hasHeaderTransitionEnd = false;
		$us.header.on( 'transitionEnd', function( header ) {
			this.headerHeight = header.getCurrentHeight() - header.getAdminBarHeight();
			this._hasHeaderTransitionEnd = true;
		}.bind( this ) );

		// Basic set of options that should be extended by scrollTo methods
		this.animationOptions = {
			duration: this.options.animationDuration,
			easing: this.options.animationEasing,
			start: function() {
				this.isScrolling = true;
			}.bind( this ),
			complete: function() {
				this.cancel.call( this );
			}.bind( this ),
		}
	}

	USScroll.prototype = {
		/**
		 * Count hash's target position and store it properly.
		 *
		 * @param {String} hash
		 * @private
		 */
		_countPosition: function( hash ) {
			var $target = this.blocks[ hash ].target,
				targetTop = $target.offset().top,
				state = $us.$body.usMod( 'state' );

			this.blocks[ hash ].top = Math.ceil( targetTop - this._canvasTopOffset );
		},
		/**
		 * Count all targets' positions for proper scrolling.
		 *
		 * @private
		 */
		_countAllPositions: function() {
			// Take into account #wpadminbar (and others possible) offset.
			this._canvasTopOffset = $us.$canvas.offset().top;
			// Counting all blocks
			for ( var hash in this.blocks ) {
				if ( this.blocks[ hash ] ) {
					this._countPosition( hash );
				}
			}
		},

		/**
		 * Indicate scroll position by hash.
		 *
		 * @param {String} activeHash
		 * @private
		 */
		_indicatePosition: function( activeHash ) {
			for ( var hash in this.blocks ) {
				if ( ! this.blocks[ hash ] ) {
					continue;
				}
				var block = this.blocks[ hash ];
				if ( block.buttons !== undefined ) {
					block.buttons
						.toggleClass( this.options.buttonActiveClass, hash === activeHash );
				}
				if ( block.menuItems !== undefined ) {
					block.menuItems
						.toggleClass( this.options.menuItemActiveClass, hash === activeHash );
				}
				// Removing active class for all Menu Ancestors first.
				if ( block.menuAncestors !== undefined ) {
					block.menuAncestors
						.removeClass( this.options.menuItemAncestorActiveClass );
				}
			}
			// Adding active class for activeHash Menu Ancestors after all Menu Ancestors active classes was removed in
			// previous loop. This way there would be no case when we first added classes for needed Menu Ancestors and
			// then removed those classes while checking sibling menu item's hash.
			if ( this.blocks[ activeHash ] !== undefined && this.blocks[ activeHash ].menuAncestors !== undefined ) {
				this.blocks[ activeHash ].menuAncestors.addClass( this.options.menuItemAncestorActiveClass );
			}
		},

		/**
		 * Attach anchors so their targets will be listened for possible scrolls.
		 *
		 * @param {String|jQuery} anchors Selector or list of anchors to attach.
		 */
		attach: function( anchors ) {
			// Decode pathname to compare non-latin letters.
			var pathname = decodeURIComponent( location.pathname ),
				// Location pattern to check absolute URLs for current location.
				locationPattern = new RegExp( '^' + pathname.replace( /[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&" ) + '#' );

			var $anchors = $( anchors );
			if ( $anchors.length == 0 ) {
				return;
			}
			$anchors.each( function( index, anchor ) {
				var $anchor = $( anchor ),
					href = $anchor.attr( 'href' ),
					hash = $anchor.prop( 'hash' );

				// Ignoring ajax links
				if ( hash.indexOf( '#!' ) != - 1 ) {
					return;
				}
				// Checking if the hash is connected with the current page.
				if ( ! ( // Link type: #something
					href.charAt( 0 ) == '#' || // Link type: /#something
					( href.charAt( 0 ) == '/' && locationPattern.test( href ) ) || // Link type:
					// http://example.com/some/path/#something
					href.indexOf( location.host + pathname + '#' ) > - 1 ) ) {
					return;
				}
				// Do we have an actual target, for which we'll need to count geometry?
				if ( hash != '' && hash != '#' ) {
					// Attach target
					if ( this.blocks[ hash ] === undefined ) {
						var $target = $( hash ), $type = '';

						// Don't attach anchors that actually have no target.
						if ( $target.length == 0 ) {
							return;
						}
						// If it's the only row in a section, than use section instead.
						if ( $target.hasClass( 'g-cols' ) && $target.parent().children().length == 1 ) {
							$target = $target.closest( '.l-section' );
						}
						// If it's a tabs or tour item, then use it's tabs container.
						if ( $target.hasClass( 'w-tabs-section' ) ) {
							var $newTarget = $target.closest( '.w-tabs' );
							if ( ! $newTarget.hasClass( 'accordion' ) ) {
								$target = $newTarget;
							}
							$type = 'tab';
						} else if ( $target.hasClass( 'w-tabs' ) ) {
							$type = 'tabs';
						}
						this.blocks[ hash ] = {
							target: $target, type: $type
						};
						this._countPosition( hash );
					}
					// Attach activity indicator
					if ( $anchor.parent().length > 0 && $anchor.parent().hasClass( 'menu-item' ) ) {
						var $menuIndicator = $anchor.closest( '.menu-item' );
						this.blocks[ hash ].menuItems = ( this.blocks[ hash ].menuItems || $() ).add( $menuIndicator );
						var $menuAncestors = $menuIndicator.parents( '.menu-item-has-children' );
						if ( $menuAncestors.length > 0 ) {
							this.blocks[ hash ].menuAncestors = ( this.blocks[ hash ].menuAncestors || $() ).add( $menuAncestors );
						}
					} else {
						this.blocks[ hash ].buttons = ( this.blocks[ hash ].buttons || $() ).add( $anchor );
					}
				}
				$anchor.on( 'click', function( event ) {
					event.preventDefault();
					this.scrollTo( hash, true );

					if ( typeof this.blocks[ hash ] !== 'undefined' ) {
						var block = this.blocks[ hash ];
						// When scrolling to an element, check for the presence of tabs, and if necessary, open the
						// first section.
						if ( $.inArray( block.type, ['tab', 'tabs'] ) !== - 1 ) {
							var $linkedSection = block.target.find( '.w-tabs-section[id="' + hash.substr( 1 ) + '"]' );
							if ( block.type === 'tabs' ) {
								// Selects the first section
								$linkedSection = block.target.find( '.w-tabs-section:first' );
							} else if ( block.target.hasClass( 'w-tabs-section' ) ) {
								// The selected section
								$linkedSection = block.target;
							}
							if ( $linkedSection.length ) {
								// Trigger a click event to open the first section.
								$linkedSection
									.find( '.w-tabs-section-header' )
									.trigger( 'click' );
							}
						} else if (
							block.menuItems !== undefined
							&& $.inArray( $us.$body.usMod( 'state' ), ['mobiles', 'tablets'] ) !== - 1
							&& $us.$body.hasClass( 'header-show' )
						) {
							$us.$body.removeClass( 'header-show' );
						}
					}
				}.bind( this ) );
			}.bind( this ) );
		},

		/**
		 * Gets the place position.
		 *
		 * @param mixed place
		 * @return object
		 */
		getPlacePosition: function( place ) {
			var data = { newY: 0, type: '' };
			// Scroll to top
			if ( place === '' || place === '#' ) {
				data.newY = 0;
				data.placeType = 'top';
			}
			// Scroll by hash
			else if ( this.blocks[ place ] !== undefined ) {
				// Position recalculation
				this._countPosition( place );
				data.newY = this.blocks[ place ].top;
				data.placeType = 'hash';
				place = this.blocks[ place ].target;

				// JQuery object handler
			} else if ( place instanceof $ ) {
				if ( place.hasClass( 'w-tabs-section' ) ) {
					var newPlace = place.closest( '.w-tabs' );
					if ( ! newPlace.hasClass( 'accordion' ) ) {
						place = newPlace;
					}
				}
				// Get the Y position, taking into account the height of the header, adminbar and sticky elements.
				data.newY = Math.floor( place.offset().top - this._canvasTopOffset );
				data.placeType = 'element';
			} else {
				// Get the Y position, taking into account the height of the header, adminbar and sticky elements.
				data.newY = Math.floor( place - this._canvasTopOffset );
			}

			// If the page has a sticky section, then consider the height of the sticky section.
			if ( $us.header.isHorizontal() && $us.canvas.hasStickySection() ) {
				data.newY -= $us.canvas.getHeightStickySection();
			}

			return data;
		},

		/**
		 * Scroll page to a certain position or hash.
		 *
		 * @param {Number|String|jQuery} place
		 * @param {Boolean} animate
		 */
		scrollTo: function( place, animate ) {

			if ( $( place ).closest( '.w-popup-wrap' ).length ) {
				this.scrollToPopupContent( place );
				return true;
			}

			var offset = this.getPlacePosition.call( this, place ),
				indicateActive = function() {
					if ( offset.type === 'hash' ) {
						this._indicatePosition( place );
					} else {
						this.scroll();
					}
				}.bind( this );

			if ( animate ) {
				// Fix for iPads since scrollTop returns 0 all the time.
				if ( navigator.userAgent.match( /iPad/i ) != null && $( '.us_iframe' ).length && offset.type == 'hash' ) {
					$( place )[ 0 ].scrollIntoView( { behavior: "smooth", block: "start" } );
				}

				var scrollTop = parseInt( $us.$window.scrollTop() ),
					// Determining the direction of scrolling - up or down.
					scrollDirections = scrollTop < offset.newY
						? 'down'
						: 'up';

				if ( scrollTop === offset.newY ) {
					return;
				}

				// When scrolling down, run a trigger to take into account the header height.
				if (
					! this.isScrolling
					&& $us.header.isHorizontal()
					&& ! this._hasHeaderTransitionEnd
				) {
					$us.header.trigger( 'transitionEnd' );
					this._hasHeaderTransitionEnd = true;
				}

				// Animate options
				var animateOptions = $.extend(
					{},
					this.animationOptions,
					{
						always: function() {
							this.isScrolling = false;
							indicateActive();
						}.bind( this )
					}
				);

				/**
				 * Get and applying new values during animation.
				 *
				 * @param number now
				 * @param object fx
				 */
				animateOptions.step = function( now, fx ) {
					// Checking the position of the element, since the position may change if the leading elements
					// were loaded with a lazy load
					var newY = this.getPlacePosition( place ).newY;
					// Since the header at the moment of scrolling the scroll can change the height,
					// we will correct the position of the element
					if ( $us.header.isHorizontal() && $us.header.isStickyEnabled() ) {
						newY -= this.headerHeight;
					}

					if ( fx.end !== newY ) {
						// Restart animation with new values
						$us.$htmlBody
							.stop( true, false )
							.animate( { scrollTop: newY + 'px' }, $.extend( {}, animateOptions, {
								easing: this.options.endAnimationEasing
							} ) );
					}
				}.bind( this );

				// Start animation
				$us.$htmlBody
					.stop( true, false )
					.animate( { scrollTop: offset.newY + 'px' }, animateOptions );

				// Allow user to stop scrolling manually.
				$us.$window
					.on( 'keydown mousewheel DOMMouseScroll touchstart', this._events.cancel );
			} else {

				// If scrolling without animation, then we get the height of the header and change the position.
				if ( $us.header.isStickyEnabled() && $us.header.isHorizontal() ) {
					$us.header.trigger( 'transitionEnd' );
					offset.newY -= this.headerHeight;
				}

				// Stop all animations and scroll to the set position.
				$us.$htmlBody
					.stop( true, false )
					.scrollTop( offset.newY );
				indicateActive();
			}
		},
		/**
		 * Scroll Popup's content to a certain hash.
		 *
		 * @param {Number|String|jQuery} place
		 */
		scrollToPopupContent: function( place ) {
			var id = place.replace( '#', '' ),
				elm = document.getElementById( id );

			// Animate options
			var animateOptions = $.extend(
				{},
				this.animationOptions,
				{
					always: function() {
						this.isScrolling = false;
					}.bind( this ),
				}
			);

			$( elm ).closest( '.w-popup-wrap' )
				.stop( true, false )
				.animate( { scrollTop: elm.offsetTop + 'px' }, animateOptions );

			$us.$window
				.on( 'keydown mousewheel DOMMouseScroll touchstart', this._events.cancel );
		},

		/**
		 * Cancel scroll.
		 */
		cancel: function() {
			$us.$htmlBody.stop( true, false );
			$us.$window.off( 'keydown mousewheel DOMMouseScroll touchstart', this._events.cancel );
			this.isScrolling = false;
		},

		/**
		 * Scroll handler
		 */
		scroll: function() {
			var scrollTop = $us.header.getScrollTop();
			// Safari negative scroller fix.
			scrollTop = ( scrollTop >= 0 )
				? scrollTop
				: 0;
			if ( ! this.isScrolling ) {
				var activeHash;
				for ( var hash in this.blocks ) {
					if ( ! this.blocks[ hash ] ) {
						continue;
					}
					var top = this.blocks[ hash ].top,
						$target = this.blocks[ hash ].target;
					if ( ! $us.header.isHorizontal() ) {
						// The with a vertical header, subtract only the height of the admin bar, if any.
						top -= $us.header.getAdminBarHeight();
					} else {
						// Since the header at the moment of scrolling the scroll can change the height,
						// we will correct the position of the element.
						if ( $us.header.isStickyEnabled() ) {
							top -= this.headerHeight;
						}
						// If the page has a sticky section, then consider the height of the sticky section.
						if ( $us.canvas.hasStickySection() ) {
							top -= $us.canvas.getHeightStickySection();
						}
					}
					if ( scrollTop >= top && scrollTop <= ( /* block bottom */top + $target.outerHeight( false ) ) ) {
						activeHash = hash;
					}
				}
				$us.debounce( this._indicatePosition.bind( this, activeHash ), 1 )();
			}
		},

		/**
		 * Resize handler.
		 */
		resize: function() {
			// Delaying the resize event to prevent glitches.
			$us.timeout( function() {
				this._countAllPositions();
				this.scroll();
			}.bind( this ), 150 );
			this._countAllPositions();
			this.scroll();
		}
	};

	$( function() {
		$us.scroll = new USScroll( $us.scrollOptions || {} );
	} );

}( jQuery );
