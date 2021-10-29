/**
 * $us.nav
 *
 * Header navigation with all the possible states
 *
 * @requires $us.canvas
 */
!function( $ ) {

	$us.Nav = function( container, options ) {
		this.init( container, options );
	};

	$us.mobileNavOpened = 0;

	$us.Nav.prototype = {
		init: function( container, options ) {
			// Commonly used dom elements
			this.$nav = $( container );
			if ( this.$nav.length == 0 ) {
				return;
			}
			this.$control = this.$nav.find( '.w-nav-control' );
			this.$close = this.$nav.find( '.w-nav-close' );
			this.$items = this.$nav.find( '.menu-item' );
			this.$list = this.$nav.find( '.w-nav-list.level_1' );
			this.$subItems = this.$list.find( '.menu-item-has-children' );
			this.$subAnchors = this.$list.find( '.menu-item-has-children > .w-nav-anchor' );
			this.$subLists = this.$list.find( '.menu-item-has-children > .w-nav-list' );
			this.$anchors = this.$nav.find( '.w-nav-anchor' );
			this.$arrows = $( '.w-nav-arrow' );

			// Setting options
			this.options = this.$nav.find( '.w-nav-options:first' )[ 0 ].onclick() || {};

			// In case the nav doesn't exist, do nothing
			if ( this.$nav.length == 0 ) {
				return;
			}

			this.type = this.$nav.usMod( 'type' );
			this.layout = this.$nav.usMod( 'layout' );
			this.mobileOpened = false;

			// For all tablets, after clicking on the submenu, it closes the main menu,
			// this is necessary because there is no hover event in the tablet
			if ( $.isMobile && this.type == 'desktop' ) {
				this.$list.on( 'click', '.w-nav-anchor[class*="level_"]', function( e ) {
					var $target = $( e.currentTarget ),
						$item = $target.closest( '.menu-item' );
					if ( $target.usMod('level') > 1 && ! $item.hasClass( 'menu-item-has-children' ) ) {
						$target
							.parents( '.menu-item.opened' )
							.removeClass( 'opened' );
					}
				} );
			}

			// Mobile menu toggler
			this.$control.on( 'click', function() {
				this.mobileOpened = !this.mobileOpened;

				this.setTabIndex( true );

				this.$anchors.each( function() {
					// Making empty links focusable and clickable to open a dropdown
					if ( $( this ).attr( 'href' ) == undefined ) {
						$( this ).attr( 'href', 'javascript:void(0)' );
					}
				} );

				if ( this.layout != 'dropdown' ) {
					this.$anchors.removeAttr( 'tabindex' );
				}

				if ( this.mobileOpened ) {
					// Closing all other menus if present
					$( '.l-header .w-nav' ).not( container ).each( function() {
						$( this ).trigger( 'USNavClose' );
					} );
					// Closing opened sublists
					this.$control.addClass( 'active' );
					this.$items.filter( '.opened' ).removeClass( 'opened' );
					this.$subLists.resetInlineCSS( 'display', 'height' );
					if ( this.layout == 'dropdown' ) {
						this.$list.slideDownCSS( 250, this._events.contentChanged );
					}
					$us.mobileNavOpened ++;
				} else {
					this.$control.removeClass( 'active' );
					if ( this.layout == 'dropdown' ) {
						this.$list.slideUpCSS( 250, this._events.contentChanged );
					}

					this.setTabIndex();

					if ( this.layout != 'dropdown' ) {
						this.$anchors.attr( 'tabindex', - 1 );
					}
					$us.mobileNavOpened --;
				}
				$us.$canvas.trigger( 'contentChange' );

			}.bind( this ) );

			this.$control.on( 'focusin', function( e ) {
				if ( this.type != 'mobile' || this.layout == 'dropdown' ) {
					return;
				}
				this.$anchors.attr( 'tabindex', - 1 );
			}.bind( this ) );

			// Close
			this.$close.on( 'click', function() {
				this.mobileOpened = false;
				this.$control.removeClass( 'active' );
				$us.mobileNavOpened --;
				$us.$canvas.trigger( 'contentChange' );
			}.bind( this ) );

			// Close on ESC key pressed
			$us.$document.keyup( function( e ) {
				if ( e.keyCode == 27 ) {
					if ( this.mobileOpened ) {
						if ( this.layout == 'dropdown' ) {
							this.$list.slideUpCSS( 250, this._events.contentChanged );
						}
						this.mobileOpened = false;
						this.$control.removeClass( 'active' );

						this.setTabIndex();

						if ( this.layout != 'dropdown' ) {
							this.$anchors.attr( 'tabindex', - 1 );
						}
						$us.mobileNavOpened --;
						$us.$canvas.trigger( 'contentChange' );
					}
				}
			}.bind( this ) );

			// Bindable events
			this._events = {
				// Mobile submenu togglers
				menuToggler: function( $item, show ) {
					if ( this.type != 'mobile' ) {
						return;
					}
					var $sublist = $item.children( '.w-nav-list' );
					if ( show ) {
						$item.addClass( 'opened' );
						$sublist.slideDownCSS( 250, this._events.contentChanged );
					} else {
						$item.removeClass( 'opened' );
						$sublist.slideUpCSS( 250, this._events.contentChanged );
					}
				}.bind( this ),

				focusHandler: function( e ) {
					if ( this.type == 'mobile' ) {
						return;
					}
					var $item = $( e.target ).closest( '.menu-item' ),
						$target = $( e.target );
					$item.parents( '.menu-item' ).addClass( 'opened' );
					$item.on( 'mouseleave', function() {
						$target.blur();
					} );
				}.bind( this ),

				blurHandler: function( e ) {
					if ( this.type == 'mobile' ) {
						return;
					}
					var $item = $( e.target ).closest( '.menu-item' );
					$item.parents( '.menu-item' ).removeClass( 'opened' );
				}.bind( this ),

				clickHandler: function( e ) {
					if ( this.type != 'mobile' ) {
						return;
					}
					e.stopPropagation();
					e.preventDefault();
					var $item = $( e.currentTarget ).closest( '.menu-item' ),
						isOpened = $item.hasClass( 'opened' );
					this._events.menuToggler( $item, !isOpened );
				}.bind( this ),

				keyDownHandler: function( e ) {
					if ( this.type != 'mobile' ) {
						return;
					}
					var keyCode = e.keyCode || e.which;
					// Enter Handler for arrows
					if ( keyCode == 13 ) {
						var $target = $( e.target ),
							$item = $target.closest( '.menu-item' ),
							isOpened = $item.hasClass( 'opened' );
						if ( !$target.is( this.$arrows ) ) {
							return;
						}
						e.stopPropagation();
						e.preventDefault();
						this._events.menuToggler( $item, !isOpened );
					}
					// Tab handler
					if ( keyCode == 9 ) {
						var $target = $( e.target ) ? $( e.target ) : {},
							i = this.$anchors.index( $target ),
							isDropdownLayout = this.layout == 'dropdown' ? true : false,
							closeMenu = function() {
								// Close whole dropdown when going outside
								if ( this.mobileOpened ) {
									if ( isDropdownLayout ) {
										this.$list.slideUpCSS( 250, this._events.contentChanged );
									}
									this.mobileOpened = false;
									this.$control.removeClass( 'active' );
									$us.mobileNavOpened --;
									$us.$canvas.trigger( 'contentChange' );

									this.setTabIndex();

									if ( this.layout != 'dropdown' ) {
										this.$anchors.attr( 'tabindex', - 1 );
									}
								}
							}.bind( this );

						if ( e.shiftKey ) {
							if ( ( i === this.$anchors.length - 1 ) && this.layout != 'dropdown' ) {
								this.$anchors.attr( 'tabindex', - 1 );
							}
							if ( i === 0 ) {
								closeMenu();
							}
						} else {
							if ( i === this.$anchors.length - 1 ) {
								closeMenu();
							}
						}
					}
				}.bind( this ),

				resize: this.resize.bind( this ),
				contentChanged: function() {
					if ( this.type == 'mobile' && $us.header.isHorizontal() && $us.canvas.headerPos == 'fixed' && this.layout == 'fixed' ) {
						this.setFixedMobileMaxHeight();
					}
					$us.header.$container.trigger( 'contentChange' );
				}.bind( this ),
				close: function() {
					if ( this.$list != undefined && jQuery.fn.slideUpCSS != undefined && this.mobileOpened && this.type == 'mobile' ) {
						this.mobileOpened = false;
						if ( this.layout == 'dropdown' && this.headerOrientation == 'hor' ) {
							this.$list.slideUpCSS( 250 );
						}
						$us.mobileNavOpened --;
						$us.$canvas.trigger( 'contentChange' );
					}
				}.bind( this )
			};

			// Menu items mobile behavior
			this.$subItems.each( function( index ) {
				var $item = $( this.$subItems[ index ] ),
					$arrow = $item.find( '.w-nav-arrow' ).first(),
					$subAnchor = $item.find( '.w-nav-anchor' ).first(),
					dropByLabel = $item.hasClass( 'mobile-drop-by_label' ) || $item.parents( '.menu-item' ).hasClass( 'mobile-drop-by_label' ),
					dropByArrow = $item.hasClass( 'mobile-drop-by_arrow' ) || $item.parents( '.menu-item' ).hasClass( 'mobile-drop-by_arrow' );

				if ( dropByLabel || ( this.options.mobileBehavior && !dropByArrow ) ) {
					$subAnchor.on( 'click', this._events.clickHandler );
				} else if ( dropByArrow || ( !this.options.mobileBehavior && !dropByLabel ) ) {
					$arrow.on( 'click', this._events.clickHandler );
					$arrow.on( 'click', this._events.keyDownHandler );
				}
			}.bind( this ) );

			// Mark all the togglable items
			this.$subItems.each( function() {
				var $this = $( this ),
					$parentItem = $this.parent().closest( '.menu-item' );
				if ( $parentItem.length == 0 || $parentItem.usMod( 'columns' ) === false ) {
					$this.addClass( 'togglable' );
				}
			} );

			// Touch screen handling for desktop type
			if ( !$us.$html.hasClass( 'no-touch' ) ) {
				this.$list.find( '.menu-item-has-children.togglable > .w-nav-anchor' ).on( 'click', function( e ) {
					if ( this.type == 'mobile' ) {
						return;
					}
					e.preventDefault();
					var $this = $( e.currentTarget ),
						$item = $this.parent();
					// Second tap: going to the URL
					if ( $item.hasClass( 'opened' ) ) {
						return location.assign( $this.attr( 'href' ) );
					}
					$item.addClass( 'opened' );
					var outsideClickEvent = function( e ) {
						if ( $.contains( $item[ 0 ], e.target ) ) {
							return;
						}
						$item.removeClass( 'opened' );
						$us.$body.off( 'touchstart', outsideClickEvent );
					};
					$us.$body.on( 'touchstart', outsideClickEvent );
				}.bind( this ) );
			}

			// Close on click outside of level 1 menu list
			$( $us.$document ).on( 'mouseup touchend', function( e ) {
				if ( this.mobileOpened && this.type == 'mobile' ) {
					if ( !this.$control.is( e.target ) && this.$control.has( e.target ).length === 0 && !this.$list.is( e.target ) && this.$list.has( e.target ).length === 0 ) {
						this.mobileOpened = false;
						this.$control.removeClass( 'active' );
						this.$items.filter( '.opened' ).removeClass( 'opened' );
						this.$subLists.slideUpCSS( 250 );
						if ( this.layout == 'dropdown' && this.headerOrientation == 'hor' ) {
							this.$list.slideUpCSS( 250 );
						}
						$us.mobileNavOpened --;
						$us.$canvas.trigger( 'contentChange' );
					}
				}
			}.bind( this ) );

			// Accessibility
			this.$anchors.on( 'focus.upsolution', this._events.focusHandler );
			this.$anchors.on( 'blur.upsolution', this._events.blurHandler );
			this.$nav.on( 'keydown.upsolution', this._events.keyDownHandler );

			// Close menu on anchor clicks
			this.$anchors.on( 'click', function( e ) {
				var $item = $( e.currentTarget ).closest( '.menu-item' ),
					dropByLabel = $item.hasClass( 'mobile-drop-by_label' ) || $item.parents( '.menu-item' ).hasClass( 'mobile-drop-by_label' ),
					dropByArrow = $item.hasClass( 'mobile-drop-by_arrow' ) || $item.parents( '.menu-item' ).hasClass( 'mobile-drop-by_arrow' );
				if ( this.type != 'mobile' || $us.header.isVertical() ) {
					return;
				}
				if ( dropByLabel || ( this.options.mobileBehavior && $item.hasClass( 'menu-item-has-children' ) && !dropByArrow ) ) {
					return;
				}
				this.mobileOpened = false;
				this.$control.removeClass( 'active' );
				if ( this.layout == 'dropdown' ) {
					this.$list.slideUpCSS( 250 );
				}
				$us.mobileNavOpened --;
				$us.$canvas.trigger( 'contentChange' );
			}.bind( this ) );

			$us.$window.on( 'resize', $us.debounce( this._events.resize, 5 ) );
			$us.timeout( function() {
				this.resize();
				$us.header.$container.trigger( 'contentChange' );
			}.bind( this ), 50 );
			this.$nav.on( 'USNavClose', this._events.close );
		},

		/**
		 * Add/remove tabindex to/from arrows to make them accessible
		 */
		setTabIndex: function( add ) {
			// Menu items mobile behavior
			this.$subItems.each( function( index ) {
				var $item = $( this.$subItems[ index ] );

				if ( $item.hasClass( 'mobile-drop-by_arrow' ) || $item.parents( '.menu-item' ).hasClass( 'mobile-drop-by_arrow' ) || ( !$item.hasClass( 'mobile-drop-by_label' ) && !this.options.mobileBehavior ) ) {
					if ( add ) {
						$item.find( '.w-nav-arrow' ).attr( 'tabindex', 0 );
					} else {
						$item.find( '.w-nav-arrow' ).removeAttr( 'tabindex' );
					}
				}
			}.bind( this ) );
		},

		/**
		 * Count proper dimensions
		 */
		setFixedMobileMaxHeight: function() {
			//var listTop = Math.min(this.$list.position().top, $us.header.scrolledOccupiedHeight);
			this.$list.css( 'max-height', $us.canvas.winHeight - $us.header.getCurrentHeight() + 'px' );
		},

		/**
		 * Resize handler
		 */
		resize: function() {
			if ( this.$nav.length == 0 ) {
				return;
			}
			var nextType = ( window.innerWidth < this.options.mobileWidth ) ? 'mobile' : 'desktop';
			if ( $us.header.orientation != this.headerOrientation || nextType != this.type ) {
				// Clearing the previous state
				this.$subLists.resetInlineCSS( 'display', 'height' );
				if ( this.headerOrientation == 'hor' && this.type == 'mobile' ) {
					this.$list.resetInlineCSS( 'display', 'height', 'max-height', 'opacity' );
				}
				// Closing opened sublists
				this.$items.removeClass( 'opened' );
				this.headerOrientation = $us.header.orientation;
				this.type = nextType;
				this.$nav.usMod( 'type', nextType );
				this.setTabIndex();

				if ( this.layout != 'dropdown' ) {
					this.$anchors.removeAttr( 'tabindex' );
				}
			}
			// Max-height limitation for fixed header layouts
			if ( $us.header.isHorizontal() && this.type == 'mobile' && this.layout == 'dropdown' && $us.header.isFixed() ) {
				this.setFixedMobileMaxHeight();
			}
			this.$list.removeClass( 'hide_for_mobiles' );
		}
	};

	$.fn.usNav = function( options ) {
		return this.each( function() {
			$( this ).data( 'usNav', new $us.Nav( this, options ) );
		} );
	};

	$( '.l-header .w-nav' ).usNav();

}( jQuery );
