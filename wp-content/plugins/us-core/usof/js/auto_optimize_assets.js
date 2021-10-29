/**
 * Auto optimize assets
 */
jQuery( function( $ ) {
	/**
	 * @class AutoOptimizeAssets
	 */
	function AutoOptimizeAssets() {
		// Variabels
		this.assets = {};
		this._data = {
			action: 'us_auto_optimize_assets'
		};

		// Elements
		this.$container = $( '[data-name="optimize_assets_start"]' );
		this.$button = $( '.usof-button.type_auto_optimize', this.$container );
		this.$message = $( '.usof-message.type_auto_optimize', this.$container );

		// Load data
		if ( this.$button.is( '[onclick]' ) ) {
			this._data = $.extend( this._data, this.$button[ 0 ].onclick() || {} );
			this.$button.removeAttr( 'onclick' );
		}

		$( '.usof-checkbox-list input[name="assets"]', this.$container ).each( function( _, checkbox ) {
			this.assets[ $( checkbox ).attr( 'value' ) ] = checkbox;
		}.bind( this ) );

		// Events
		this.$container
			.on( 'click', '.usof-button.type_auto_optimize', this._events.clickButton.bind( this ) )
			.on( 'change', 'input[name="assets"]', this._events.clearMessage.bind( this ) );
	}

	// Export API
	AutoOptimizeAssets.prototype = {
		// Event handler's
		_events: {
			/**
			 * Button click.
			 *
			 * @param {Event} e
			 */
			clickButton: function( e ) {
				if ( ! this.$button.hasClass( 'loading' ) ) {
					this.$button.addClass( 'loading' );
					this._request.call( this, 'request' );
					this._events.clearMessage.call( this );
				}
			},
			/**
			 * Clear message.
			 *
			 * @param {Event} e
			 */
			clearMessage: function( e ) {
				this.$message.addClass( 'hidden' ).html( '' );
			}
		},
		/**
		 * Asset Use Requests
		 *
		 * @param string type Request type
		 */
		_request: function( type ) {
			$.post( $usof.ajaxUrl, $.extend( this._data, { type: type } ), function( res ) {
				if ( res.data.processing ) {
					this._request.call( this, 'iteration' );
				} else {
					this.$button.removeClass( 'loading' );

					// Show message
					if ( $.trim( res.data.message ) ) {
						this.showMessage.call( this, res.data.message );
					}

					// Reset checkboxes
					$( 'input[type="checkbox"]', this.$container )
						.prop( 'checked', false );

					// Selected checkboxes
					if ( res.data.used_assets ) {
						$.each( res.data.used_assets, function( _, asset_name ) {
							if ( this.assets.hasOwnProperty( asset_name ) ) {
								$( this.assets[ asset_name ] ).prop( 'checked', true );
							}
						}.bind( this ) );

						// Save Changes
						$usof.instance.valuesChanged[ 'assets' ] = res.data.assets_value;
						$usof.instance.save();
					}

				}
			}.bind( this ), 'json' );
		},
		/**
		 * Shows the message.
		 *
		 * @param text html
		 */
		showMessage: function( html ) {
			this.$message.html( html ).removeClass( 'hidden' );
		}
	};
	// Init AutoOptimizeAssets
	$( new AutoOptimizeAssets );
} );
