/**
 * USOF Field: Used icons info
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'used_icons_info' ] = {
		init: function() {
			// Variables
			this.loaded = false;

			// Elements
			this.$container = $( '.usof-icons-info:first', this.$row );
			this.$button = $( '.usof-button.type_show_used_icons', this.$container );
			this.$content = $( '.usof-form-wrapper.for_used_icons', this.$container );

			// Watch events
			this.$button
				.on( 'click', this._events.showIcons.bind( this ) );
		},
		_events: {
			/**
			 * Show the icons.
			 *
			 * @param {Event} e
			 */
			showIcons: function ( e ) {
				e.preventDefault();
				if ( this.loaded ) {
					return;
				}
				this.$button.addClass( 'loading' );
				this._request.call( this, 'request' );
			}
		},
		/**
		 * Get a list of used icons.
		 *
		 * @param string type
		 */
		_request: function ( type ) {
			this.loaded = true;
			this.$content
				.addClass( 'hidden' )
				.html( '' );
			var data = {
				type: type,
				action: 'usof_used_icons_info',
				_nonce: this.$container.data( 'nonce' ) || ''
			};
			$.post( ajaxurl, data, function( res ) {
				if ( res.data.processing ) {
					this._request.call( this, 'iteration' );
				} else {
					this.$button.removeClass( 'loading' );
					this.loaded = false;
					if ( ! res.success ) {
						this._showMessage.call( this, res.data.message );
						return;
					}
					this.$content
						.toggleClass( 'hidden', ! res.data.result )
						.html( res.data.result );
				}
			}.bind( this ) );
		}
	};
}( jQuery );
