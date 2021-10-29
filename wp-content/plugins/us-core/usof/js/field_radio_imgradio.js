/**
 * USOF Fields: Radio & Imgradio
 */
! function( $, undefined ) {
	var _window = window;
	if ( _window.$usof === undefined ) {
		return;
	}

	/**
	 * NOTE: Do not set the field `input[type=radio]` name to disable links between the selection by the browser itself!
	 */
	$usof.field[ 'radio' ] = $usof.field[ 'imgradio' ] = {
		/**
		 * Field initialization.
		 *
		 * @param {{}} options
		 */
		init: function() {
			// Elements
			this.$radio = $( 'input[type=radio]', this.$row );
			// Event handlers
			this._events = {
				changeValue: this._changeValue.bind( this )
			};
			// Events
			this.$row
				.on( 'click', 'input[type=radio]', this._events.changeValue );
		},

		/**
		 * This is a handler for changes to the selected buttons.
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_changeValue: function( e ) {
			var newValue;
			if ( e.target === undefined || e.target.value === undefined ) {
				newValue = this.getDefaultValue();
			} else {
				newValue = e.target.value;
			}
			this.setValue( newValue );
		},

		/**
		 * Get the value
		 *
		 * @return {mixed} Returning the value
		 */
		getValue: function() {
			var value;
			if ( this.$input === undefined || this.$input.val() === undefined ) {
				value = this.getDefaultValue();
			} else {
				value = this.$input.val();
			}
			return value;
		},

		/**
		 * Set the value.
		 *
		 * @param {string} value The value to be selected
		 * @param {boolean} quiet Sets in quiet mode without events
		 */
		setValue: function( value, quiet ) {
			value = value || '';
			// Save value in field
			this.$input.val( value );
			// Select button
			this.$radio
				.removeAttr( 'checked' )
				.filter( '[value="' + value + '"]' )
				.prop( 'checked', true );
			// Send a change signal
			if ( ! quiet ) {
				this.trigger( 'change', [ value ] );
			}
		}
	};

}( jQuery );
