/**
 * USOF Field: Select
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'select' ] = {
		init: function( options ) {
			this.parentInit( options );

			// Elements
			this.$container = $( '.usof-select', this.$row );
			this.$hint = $( '.usof-form-row-hint-text', this.$row );
			this.$hintsJson = $( '.usof-form-row-hint-json', this.$row );

			// Variables
			this.hintsJson = {};

			// Load hints
			if ( this.$hintsJson.length ) {
				this.hintsJson = this.$hintsJson[ 0 ].onclick() || {};
				this.$hintsJson.remove();
			}

			// Events
			this.$input
				.on( 'change', this._events.changeSelect.bind( this ) );

			// Set double value for css selectors
			this.$container
				.attr( 'selected-value', this.$input.val() );

			// Dynamic description toggles
			this._changeSelect.call( this );
			this._toggleGridLayoutDesc.call( this );
		},
		/**
		 * Event handlers
		 * @private
		 */
		_events: {
			/**
			 * Change select.
			 */
			changeSelect: function() {
				// Dynamic description toggles
				this._changeSelect.call( this );
				this._toggleGridLayoutDesc.call( this );
			}
		},
		_changeSelect: function() {
			var value = '' + this.$input.val(),
				$selectedOption = this.$input.find( ":selected" ),
				selectedDataID = $selectedOption.data( 'id' ),
				selectedDataTitle = $selectedOption.data( 'title' );
			this.$container
				.attr( 'selected-value', value );

			// Setting Editr URL
			if ( selectedDataID && ( '' + selectedDataID ).match( /\d+/ ) ) {
				value = '' + selectedDataID;
			}
			if ( ! this.hintsJson.no_posts ) {
				if ( value.length && value.match( /\d+/ ) ) {
					var hint = '';
					if ( this.hintsJson.hasOwnProperty( 'edit_url' ) ) {
						var regex = /(<a [^{]+)({{post_id}})([^{]+)({{hint}})([^>]+>)/,
							editTitle = this.hintsJson.edit;
						if ( selectedDataTitle ) {
							editTitle = this.hintsJson.edit_specific + ' ' + selectedDataTitle;
						}
						hint = this.hintsJson.edit_url.replace( regex, '$1' + value + '$3' + editTitle + '$5' );
					}
					this.$hint.html( hint );
				} else {
					this.$hint.html( '' );
				}
			}
		},
		/**
		 * Dynamic description toggles for Grig Layout.
		 *
		 * Implemented compatibility US Builder and Visual Composer
		 */
		_toggleGridLayoutDesc: function() {
			if ( ! this.$row.hasClass( 'for_grid_layouts' ) ) {
				return;
			}
			var value = this.getValue(),
				isVC = this.$row.hasClass( 'us_select_for_vc' ),
				$addDesc = $( '.us-grid-layout-desc-add', isVC ? this.$row.parent() : this.$row ),
				$editLink = $( '.us-grid-layout-desc-edit', isVC ? this.$row.parent() : this.$row );
			if ( $.isNumeric( value ) ) {
				$( '.edit-link', $editLink )
					.attr( 'href', ( this.$container.data( 'edit_link' ) || '' ).replace( '%d', value ) );
				$addDesc.addClass( 'hidden' );
				$editLink.removeClass( 'hidden' );
			} else {
				$addDesc.removeClass( 'hidden' );
				$editLink.addClass( 'hidden' );
			}
		},
		setValue: function( value, quiet ) {
			this.parentSetValue( value, quiet );

			this._changeSelect.call( this );
			this._toggleGridLayoutDesc.call( this );
		}
	};
}( jQuery );
