/**
 * USOF Field: Switch
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'switch' ] = {
		init: function() {
			// For control in html output
			this.$input.on( 'change', function( e ) {
				var value = this.getValue();
				e.target.value = value;
				this.trigger( 'change', [ value ] );
			}.bind( this ) );
		},

		/**
		 * Get the value
		 *
		 * @return {mixed} The value
		 */
		getValue: function() {
			return this.$input.is( ':checked' ) ? 1 : '';
		},
		/**
		 * Set the value.
		 *
		 * @param {mixed} value The value
		 * @param {boolean} quiet The quiet
		 */
		setValue: function( value, quiet ) {
			if ( typeof value !== 'boolean' ) {
				value = parseInt( value ) || /* Fix NaN */0;
			}
			this.$input
				.prop( 'checked', !! value )
				.val( value );
			if ( ! quiet ) {
				this.trigger( 'change', [value] );
			}
		}
	};
}( jQuery );
