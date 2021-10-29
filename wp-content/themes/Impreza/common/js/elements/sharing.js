/**
 * UpSolution Element: Sharing Buttons
 */
! function( $ ) {
	"use strict";

	$us.UsSharing = function( container, options ) {
		this.init( container, options );
	};

	$us.UsSharing.prototype = {
		init: function( container, options ) {
			this.$container = $( container );

			// If no post image is set, try to get first image from content
			if ( !! this.$container.find( '.w-sharing-list' ).data( 'content-image' ) ) {
				if ( $( '.l-canvas img:first-child' ).length ) {
					this.sharingImage = $( '.l-canvas img:first-child' ).attr( 'src' );
				} else {
					this.sharingImage = '';
				}

				this.setSharingImage();
			}

			if ( ! this.$container.hasClass( 'w-sharing-tooltip' ) ) {
				// Change WhatsApp Mobile URL
				if ( this.$container.find( '.whatsapp' ).length && $.isMobile ) {
					this.setWhatsAppUrl( this.$container.find( '.whatsapp' ) );
				}
			} else {
				this.$copy2clipboard = this.$container.find( '.w-sharing-item.copy2clipboard' );
				this.selectedText = '';
				this.activeArea = '.l-main';

				// If Allow sharing in post content only
				if ( this.$container.data( 'sharing-area' ) === 'post_content' ) {
					this.activeArea = '.w-post-elm.post_content';
				}

				// Move the tooltip for better positioning
				this.$container.appendTo( "body" );

				// Close tooltip if click anywhere on page
				$( 'body' ).not( this.activeArea ).bind( 'mouseup', function() {
					var selection = this.getSelection();
					if ( selection === '' ) {
						this.$container.hide();
					}
				}.bind( this ) );

				// Show/Hide the tooltip
				$( this.activeArea ).on( 'mouseup', function( e ) {
					var selection = this.getSelection();

					// Copy selected text and show tooltip
					if ( selection !== '' ) {
						this.selectedText = selection;

						this.showTooltip( e );
					} else {
						this.selectedText = '';
						this.hideTooltip();
					}
				}.bind( this ) );

				this.$copy2clipboard.on( 'click', function() {
					this.copyToClipboard();
				}.bind( this ) );
			}
		},
		showTooltip: function( e ) {
			// Replace placeholder text with the copied one
			this.$container.find( '.w-sharing-item' ).each( function( index, elm ) {
				// Skip copy to clipboard item
				if ( $( elm ).hasClass( 'copy2clipboard' ) ) {
					return;
				}

				// Change WhatsApp Mobile URL
				if ( $.isMobile && $( elm ).hasClass( 'whatsapp' ) ) {
					this.setWhatsAppUrl( $( elm ) );
				}
				$( elm ).attr( 'href', $( elm ).data( 'url' ).replace( '{{text}}', this.selectedText ) );
			}.bind( this ) );

			// Show the tooltip
			this.$container.css( {
				"display": "inline-block", "left": e.pageX, "top": e.pageY - 50,
			} );
		},
		setSharingImage: function() {
			this.$container.find( '.w-sharing-item' ).each( function( index, elm ) {
				// Skip copy to clipboard item
				if ( $( elm ).hasClass( 'copy2clipboard' ) ) {
					return;
				}
				$( elm ).attr( 'href', $( elm ).attr( 'href' ).replace( '{{image}}', this.sharingImage ) );

				if ( $( elm ).attr( 'data-url' ) ) {
					$( elm ).attr( 'data-url', $( elm ).attr( 'data-url' ).replace( '{{image}}', this.sharingImage ) );
				}

			}.bind( this ) );
		},
		setWhatsAppUrl: function( $elm ) {
			$elm.attr( 'href', $elm.attr( 'href' ).replace( 'https://web', 'https://api' ) );
		},
		hideTooltip: function() {
			this.$container.hide();
		},
		copyToClipboard: function() {
			var url,
				el = document.createElement( 'textarea' );

			// Get url
			if ( this.$copy2clipboard.parent().data( 'sharing-url' ) !== undefined
				&& this.$copy2clipboard.parent().data( 'sharing-url' ) !== '' ) {
				url = this.$copy2clipboard.parent().attr( 'data-sharing-url' );
			} else {
				url = window.location;
			}

			// Create hidden element to manipulated the selected text
			el.value = this.selectedText + ' ' + url;
			el.setAttribute( 'readonly', '' );
			el.style.position = 'absolute';
			el.style.left = '-9999px';
			document.body.appendChild( el );
			el.select();
			document.execCommand( 'copy' );
			document.body.removeChild( el );
			this.hideTooltip();
		},
		getSelection: function() {
			var selection = '';
			if ( window.getSelection ) {
				selection = window.getSelection();
			} else if ( document.selection ) {
				selection = document.selection.createRange();
			}
			return selection.toString().trim();
		},
	};

	$.fn.UsSharing = function( options ) {
		return this.each( function() {
			$( this ).data( 'UsSharing', new $us.UsSharing( this, options ) );
		} );
	};

	$( function() {
		$( '.w-sharing-tooltip, .w-sharing' ).UsSharing();
	} );
}( jQuery );