!function( $, undefined ) {
	"use strict";
	/**
	 * Class for importing sections (shortcodes) into the WPBakery Page Builder
	 * @class US_VC_PasteSection
	 */
	let US_VC_PasteSection = function() {
		// Elements
		this.$window = $( '.us-paste-section-window:first' );
		this.$initButton = $( '#us_vc_paste_section_button' );
		this.$insertButton = $( '.vc_ui-button-action', this.$window );
		this.$input = this.$window.find( 'textarea' );
		this.$errMessage = this.$window.find( '.vc_description' );
		this.$vcComposerContent = $( '#visual_composer_content' );
		// Variables
		this.errors = {};
		// Event Assignments
		this.$initButton.on( 'click', this._events.showWindow.bind( this ) );
		this.$insertButton.on( 'click', this._events.addSections.bind( this ) );
		this.$window.on( 'click', '.vc_ui-close-button', this._events.hideWindow.bind( this ) );
		this.$vcComposerContent
			.on( 'click', '[data-vc-control="row-copy-clipboard"]', this._events.copyRowToClipboard.bind( this ) );
		// Load errors
		if ( this.$window.length ) {
			this.data = this.$window[ 0 ].onclick() || {};
		}
	};
	// Export API
	US_VC_PasteSection.prototype = {
		// Event handlers
		_events: {
			/**
			 * Show window
			 */
			showWindow: function() {
				this._hideError();
				this.$window.show();
				this.$input.focus();
			},
			/**
			 * Hide window
			 */
			hideWindow: function() {
				this.$window.hide();
				this.$input.val( '' );
				if ( this.$insertButton.hasClass( 'loading' ) ) {
					this.$input.prop( 'disabled', false );
					this.$insertButton.removeClass( 'loading' );
				}
			},
			/**
			 * Add section
			 */
			addSections: function() {
				this.value = $.trim( this.$input.val() );
				if ( !this._isValid() ) {
					return;
				}
				this.applyFilterToValue.call( this );
				$.each( vc.storage.parseContent( {}, this.value ), function( _, model ) {
					if ( model && model.hasOwnProperty( 'shortcode' ) ) {
						// Insert sections
						vc.shortcodes.create( model );
						this._events.hideWindow.call( this );
					}
				}.bind( this ) );
			},
			/**
			 * Copy row to clipboard
			 * @param object e jQueryEvent
			 * @return void
			 */
			copyRowToClipboard: function( e ) {
				e.stopPropagation();
				e.preventDefault();
				var $el = $( e.currentTarget ).closest( '[data-element_type="vc_row"]' ),
					model = $el.data( 'model' ) || {};
				if ( window.vc !== undefined && ! $.isEmptyObject( model ) ) {
					var shortcodeStr = vc.shortcodes.createShortcodeString( model );
					this.copyToClipboard( shortcodeStr );
				}
			}
		},
		/**
		 * Copying value to clipboard
		 * @param {string} value
		 * @return void
		 */
		copyToClipboard: function( value ) {
			var el = document.createElement( 'textarea' );
			el.value = value;
			el.setAttribute( 'readonly', '' );
			el.style.position = 'absolute';
			el.style.left = '-9999px';
			document.body.appendChild( el );
			var selected = document.getSelection().rangeCount > 0 ? document.getSelection().getRangeAt(0) : false;
			el.select();
			document.execCommand( 'copy' );
			document.body.removeChild( el );
			if (selected) {
				document.getSelection().removeAllRanges();
				document.getSelection().addRange( selected );
			}
		},
		/**
		 * Apply filters to shortcodes
		 * @return void
		 */
		applyFilterToValue: function() {
			var placeholder = this.data.placeholder || '';
			// Search and replace use:placeholder
			this.value = this.value.replace( /use:placeholder/g, placeholder );

			// Replacing images for new design options
			this.value = this.value.replace( /css="([^\"]+)"/g, function( matches, match ) {
				if ( match ) {
					var jsoncss = ( decodeURIComponent( match ) || '' )
						.replace( /("background-image":")(.*?)(")/g, "$1"+ placeholder +"$3" );
					return 'css="%s"'.replace( '%s', encodeURIComponent( jsoncss ) );
				}
				return matches;
			} );

			// Checking the post_type parameter
			this.value = this.value.replace( /\s?post_type="(.*?)"/g, function( match, post_type ) {
				if ( this.data.grid_post_types.indexOf( post_type ) === - 1 ) {
					// Default post_type
					return ' post_type="post"';
				}
				return match;
			}.bind( this ) );

			// Removing [us_post_content..] if post type is not us_content_template
			if ( this.data.post_type !== 'us_content_template' ) {
				this.value = this.value.replace( /(\[us_post_content.*?])/g, '' );
			}

			// Import data for grid layout
			this.value = this.value.replace( /(grid_layout_data="([^"]+)")/g, function( data ) {
				var matches = data.match( /grid_layout_data="([^"]+)/i );
				return 'items_layout="' + this.importShortcode.call( this, 'us_grid_layout', matches[ 1 ] || '' ) + '"';
			}.bind( this ) );
		},
		/**
		 * Importing Shortcode Data
		 *
		 * @param string postType
		 * @param string post_content
		 * @return string
		 */
		importShortcode: function( postType, post_content ) {
			this.$input.prop( 'disabled', true );
			this.$insertButton.addClass( 'loading' );
			var output = '';
			$.ajax( {
				type: 'POST',
				url: ajaxurl,
				async: false,
				data: {
					_nonce: this.$window.data( 'nonce' ),
					action: 'us_import_shortcode_data',
					post_content: post_content,
					post_type: postType,
				},
				dataType: 'json',
				success: function( res ) {
					if ( res.success || res.hasOwnProperty( 'data' ) ) {
						output = res.data;
					}
				}.bind( this ),
				error: console.error
			} );
			return output;
		},
		/**
		 * Validate value
		 * @return {boolean} True if valid, False otherwise.
		 */
		_isValid: function() {
			// Add notice if the text is empty
			if ( this.value === '' ) {
				this._showError( this.data.errors.empty );
				return false;
			}
			// Add a notification if the text does not contain the shortcode [vc_row ... [/vc_row]
			if ( !/\[vc_row([\s\S]*)\/vc_row\]/gim.test( this.value ) ) {
				this._showError( this.data.errors.not_valid );
				return false;
			}
			this._hideError();
			return true;
		},
		/**
		 * Show error message.
		 * @param {string} message Error text
		 */
		_showError: function( message ) {
			this.$errMessage
				.text( message )
				.show();
		},
		/**
		 * Hide error message.
		 */
		_hideError: function() {
			this.$errMessage
				.text( '' )
				.hide();
		}
	};

	// Init class
	$().ready( function() {
		new US_VC_PasteSection;
	} );
}( window.jQuery );
