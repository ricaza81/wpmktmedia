/**
 * USOF Fields: Checkbox & Check Table
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	/**
	 * Note: Do not set the field `input[type=checkbox]` name to disable links between the selection by the browser itself!
	 */
	$usof.field[ 'checkboxes' ] = {
		init: function() {
			// Variables
			this._separator = this.$input.data( 'separator' ) || ',';
			this._isMetabox = this.$input.data( 'metabox' ) || false;
			// Elements
			this.$checkboxes = $( 'input[type=checkbox]', this.$row );
			// Event handlers
			this._events = {
				changeValue: this._changeValue.bind( this )
			};
			// Events
			this.$row
				.on( 'click', 'input[type=checkbox]', this._events.changeValue );

			// For control in html output
			var value = this.$input.val();
			if ( value ) {
				this.setValue( value );
			}
		},

		/**
		 * This is the checkbox change handler.
		 *
		 * @private
		 * @event handler
		 */
		_changeValue: function() {
			var values = [],
				checkboxes = this.$checkboxes.toArray();
			for ( var i in checkboxes ) {
				if ( !! checkboxes[ i ].checked && !! checkboxes[ i ].value ) {
					values.push( checkboxes[ i ].value );
				}
			}
			var value = values.join( this._separator );
			this.$input
				.val( value );
			this.trigger( 'change', [ value ] );
		},

		/**
		 * Get the value
		 *
		 * @return {string} Returning the value
		 */
		getValue: function() {
			return this.$input.val();
		},

		/**
		 * Set the value.
		 *
		 * @param {string||[]} value The value to be selected ()
		 */
		setValue: function( value ) {
			/**
			 * The input array of values.
			 * @type {[]}
			 */
			var values = $.isArray( value )
				? value
				: ( '' + value ).split( this._separator );

			// Mark selected checkboxes
			this.$checkboxes.each( function() {
				$( this )
					.removeAttr( 'checked' )
					.prop( 'checked', $.inArray( this.value, values ) > -1 );
			});
			// Save value in field
			this.$input.val( $.isArray( value ) ? value.join( this._separator ) : value );
		}
	};

	$usof.field[ 'check_table' ] = {
		/**
		 * Get the value
		 *
		 * @return {[]} Returning the value
		 */
		getValue: function() {
			var value = {};
			$.each( this.$input, function() {
				value[ this.value ] = ( this.checked ) ? 1 : 0;
			} );
			return value;
		},

		/**
		 * Set the value.
		 *
		 * @param {[]} value The value to be selected
		 * @param {boolean} quiet Sets in quiet mode without events
		 */
		setValue: function( value, quiet ) {
			$.each( this.$input, function() {
				$( this ).attr( 'checked', ( value[ this.value ] === undefined || value[ this.value ] == 1 ) ? 'checked' : false );
			} );
		}
	};
}( jQuery );
