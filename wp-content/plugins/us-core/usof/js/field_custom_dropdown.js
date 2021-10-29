/**
 * USOF Field: Custom dropdown
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}


	$usof.field[ 'custom_dropdown' ] = {
		init: function() {
			// Elements
			this.$container = $( '.usof-custom-dropdown', this.$row );
			this.$options = $( '.usof-custom-dropdown-item', this.$container );

			// Events
			this.$options
				.on( 'click', this._events.selectItem.bind( this ) );
		},
		/**
		 * Event handlers
		 * @private
		 */
		_events: {
			/**
			 * Select item
			 *
			 * @param {Event} e
			 */
			selectItem: function( e ) {
				var $selected = $( e.currentTarget );
				this.setValue( $selected.data( 'value' ) || null, true );
			}
		},
		/**
		 * Sets the value.
		 *
		 * @param {string} value The value
		 * @param {boolean} quiet The quiet
		 */
		setValue: function( value ) {
			this.$options
				.removeClass( 'current' )
				.filter( '[data-value="'+ value +'"]' )
				.addClass( 'current', true );
			this.$input.val( value );
			// Sending an event about changes is necessary for correct operation of `show_if`.
			this.trigger( 'change', value );
		},
		/**
		 * Gets the value.
		 *
		 * @return {string}
		 */
		getValue: function() {
			return this.$input.val() || '';
		}
	};
}( jQuery );
