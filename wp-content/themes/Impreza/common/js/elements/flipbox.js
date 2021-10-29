/**
 * UpSolution Element: FlipBox
 */
! function( $ ) {
	"use strict";
	$us.WFlipBox = function( container ) {

		// Common dom elements
		this.$container = $( container );
		this.$front = this.$container.find( '.w-flipbox-front' );
		this.$frontH = this.$container.find( '.w-flipbox-front-h' );
		this.$back = this.$container.find( '.w-flipbox-back' );
		this.$backH = this.$container.find( '.w-flipbox-back-h' );
		this.$xFlank = this.$container.find( '.w-flipbox-xflank' );
		this.$yFlank = this.$container.find( '.w-flipbox-yflank' );
		this.$btn = this.$container.find( '.w-btn' );

		// Simplified animation for IE11
		if ( ! ! window.MSInputMethodContext && ! ! document.documentMode ) {
			this.$container.usMod( 'animation', 'cardflip' ).find( '.w-flipbox-h' ).css( {
				'transition-duration': '0s',
				'-webkit-transition-duration': '0s'
			} );
		}

		// In chrome cube flip animation makes button not clickable. Replacing it with cube tilt
		var isWebkit = 'WebkitAppearance' in document.documentElement.style;
		if ( isWebkit && this.$container.usMod( 'animation' ) === 'cubeflip' && this.$btn.length ) {
			this.$container.usMod( 'animation', 'cubetilt' );
		}

		// For diagonal cube animations height should equal width (heometrical restriction)
		var animation = this.$container.usMod( 'animation' ),
			direction = this.$container.usMod( 'direction' );
		this.forceSquare = ( animation == 'cubeflip' && [ 'ne', 'se', 'sw', 'nw' ].indexOf( direction ) != - 1 );

		// Container height is determined by the maximum content height
		this.autoSize = ( this.$front[ 0 ].style.height == '' && ! this.forceSquare );

		// Content is centered
		this.centerContent = ( this.$container.usMod( 'valign' ) == 'center' );

		if ( this._events === undefined ) {
			this._events = {};
		}
		$.extend( this._events, {
			resize: this.resize.bind( this )
		} );
		if ( this.centerContent || this.autoSize ) {
			this.padding = parseInt( this.$front.css( 'padding-top' ) );
		}
		if ( this.centerContent || this.forceSquare || this.autoSize ) {
			$us.$window.bind( 'resize load', this._events.resize );
			this.resize();
		}

		this.makeHoverable( '.w-btn' );

		// Fixing css3 animations rendering glitch on page load
		$us.timeout( function() {
			this.$back.css( 'display', '' );
			this.$yFlank.css( 'display', '' );
			this.$xFlank.css( 'display', '' );
			this.resize();
		}.bind( this ), 250 );
	};
	$us.WFlipBox.prototype = {
		resize: function() {
			var width = this.$container.width(),
				height;
			if ( this.autoSize || this.centerContent ) {
				var frontContentHeight = this.$frontH.height(),
					backContentHeight = this.$backH.height();
			}

			// Changing the whole container height
			if ( this.forceSquare || this.autoSize ) {
				height = this.forceSquare ? width : ( Math.max( frontContentHeight, backContentHeight ) + 2 * this.padding );
				this.$front.css( 'height', height + 'px' );
			} else {
				height = this.$container.height();
			}
			if ( this.centerContent ) {
				this.$front.css( 'padding-top', Math.max( this.padding, ( height - frontContentHeight ) / 2 ) );
				this.$back.css( 'padding-top', Math.max( this.padding, ( height - backContentHeight ) / 2 ) );
			}
		},
		makeHoverable: function( exclude ) {
			if ( this._events === undefined ) {
				this._events = {};
			}
			if ( jQuery.isMobile ) {

				// Mobile: Touch hover
				this._events.touchHoverStart = function() {
					this.$container.toggleClass( 'hover' );
				}.bind( this );
				this.$container.on( 'touchstart', this._events.touchHoverStart );
				if ( exclude ) {
					this._events.touchHoverPrevent = function( e ) {
						e.stopPropagation();
					};
					this.$container.find( exclude ).on( 'touchstart', this._events.touchHoverPrevent );
				}
			} else {

				// Desktop: Mouse hover
				this._mouseInside = false;
				this._focused = false;

				$.extend( this._events, {
					mouseHoverStart: function() {
						this.$container.addClass( 'hover' );
						this._mouseInside = true;
					}.bind( this ),
					mouseHoverEnd: function() {
						if ( ! this._focused ) {
							this.$container.removeClass( 'hover' );
						}
						this._mouseInside = false;
					}.bind( this ),
					focus: function() {
						this.$container.addClass( 'hover' );
						this._focused = true;
					}.bind( this ),
					blur: function() {
						if ( ! this._mouseInside ) {
							this.$container.removeClass( 'hover' );
						}
						this._focused = false;
					}.bind( this )
				} );
				this.$container.on( 'mouseenter', this._events.mouseHoverStart );
				this.$container.on( 'mouseleave', this._events.mouseHoverEnd );
				this.$focusable = this.$container.find( 'a' ).addBack( 'a' );
				this.$focusable.on( 'focus', this._events.focus );
				this.$focusable.on( 'blur', this._events.blur );
			}
		}
	};

	$.fn.wFlipBox = function( options ) {
		return this.each( function() {
			$( this ).data( 'wFlipBox', new $us.WFlipBox( this, options ) );
		} );
	};

	$( function() {
		$( '.w-flipbox' ).wFlipBox();
	} );

}( jQuery );


