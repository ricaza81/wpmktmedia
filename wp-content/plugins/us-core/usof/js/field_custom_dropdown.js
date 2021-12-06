/**
 * USOF Field: Custom dropdown
 */
! function( $, undefined ) {
	var _window = window;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'custom_dropdown' ] = {
		init: function() {
			// Elements
			this.$container = $( '.usof-custom-dropdown', this.$row );
			this.$options = $( '.usof-custom-dropdown-item', this.$container );
			this.$list = $( '.usof-custom-dropdown-list', this.$container );

			/**
			 * Bondable events
			 *
			 * @private
			 * @var {{}}
			 */
			this._events = {
				hideList: this._hideList.bind( this ),
				selectItem: this._selectItem.bind( this ),
				toggleList: this._toggleList.bind( this ),
			};

			// Events
			this.$options
				.on( 'click', this._events.selectItem );
			this.$list
				.on( 'click', this._events.toggleList )
				.on( 'mouseleave', this._events.hideList );
		},
		/**
		 * Show/Hide the list
		 *
		 * @private
		 * @event handler
		 */
		_toggleList: function() {
			this.$list
				.toggleClass( 'show', ! this.$list.hasClass( 'show' ) );
		},

		/**
		 * Hide the list
		 *
		 * @private
		 * @event handler
		 */
		_hideList: function() {
			this.$list.removeClass( 'show' );
		},

		/**
		 * Selected an item from the list
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM
		 */
		_selectItem: function( e ) {
			this.setValue( $( e.currentTarget ).data( 'value' ) || null, true );
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
