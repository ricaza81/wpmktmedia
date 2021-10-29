/**
 * USOF Field: Link
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'link' ] = {

		init: function( options ) {
			this.parentInit( options );
			// Elements
			this.$url = $( 'input[type="text"]:first',  this.$row );
			this.$target = $( 'input[type="checkbox"]:first', this.$row );
			// Get current format
			this.format = ( '' + this.$input.data( 'format' ) );
			// Format validation
			if ( [ /* object */'json', /* string */'jsons', /* string */'serialized' ].indexOf( this.format ) === -1 ) {
				this.format = 'jsons'; // Default JSON String (The line will be written everywhere)
			}
			// The checkboxes within the form must be unique, otherwise there may be problems
			// with the display of values set through the JS
			if ( ! this.$target.is( '[name]' ) ) {
				this.$target
					.attr( 'name', $usof.uniqid() );
			}
			/**
			 * Bondable events.
			 *
			 * @private
			 * @var {{}}
			 */
			this._events = {
				applyChange: this._applyChange.bind( this ),
				exampleClick: this._exampleClick.bind( this ),
			};
			// Events
			this.$row
				.on( 'click', '.usof-example', this._events.exampleClick );
			this.$url
				.on( 'change', this._events.applyChange );
			this.$target
				.on( 'change', this._events.applyChange );
		},

		/**
		 * Link field has 2 different formats to store its value depending on where it is used
		 * Note: Saving data only in string format.
		 * @event handler
		 * @event handler
		 */
		_applyChange: function() {
			var value = this.getValue();
			// Save value
			this.$input.val( ( typeof value !== 'string' ) ? JSON.stringify( value ) : value );
			this.trigger( 'change', [ value ] );
		},

		/**
		 * Add an example link to a field
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_exampleClick: function( e ) {
			var $example = $( e.target ).closest( '.usof-example' );
			if ( ! $example.length ) return;
			this.$url.val( $example.text() );
		},

		/**
		 * Get the value.
		 *
		 * @return {{}|string}
		 */
		getValue: function() {
			if ( ! this.inited ) return;
			// Get current value
			var value = {
				url: usof_rawurlencode( this.$url.val() ),
				target: this.$target.is( ':checked' ) ? '_blank' : ''
			};
			// In case the field is used for a shortcode - use serialized format
			if ( this.format === 'serialized' ) {
				var result = '';
				for ( var k in value ) {
					if ( value.hasOwnProperty( k ) && value[ k ] ) {
						result += k + ':' + usof_rawurlencode( value[ k ] ) + '|';
					}
				}
				if ( result.length > 0 ) {
					result = result.substring( 0, result.length - 1 );
				}
				// Return serialize value
				return result;

			} else if ( this.format === 'jsons' && $.isPlainObject( value ) ) {
				// Return JSON string
				return JSON.stringify( value );
			}
			// Return JSON object
			return value;
		},

		/**
		 * Set the value.
		 *
		 * @param {{}|string} value The value
		 * @param {string} quiet The quiet
		 */
		setValue: function( value, quiet ) {
			if ( ! this.inited ) return;
			var newValue = {
				url: '',
				target: ''
			};
			// Applying changes to the field according to its format
			if (
				this.format === 'serialized'
				&& (
					( '' + value ).substr( 0, 4 ) === 'url:'
					|| ( '' + value ).substr( 0, 7 ) === 'target:'
					|| ( '' + value ).indexOf( '|' ) !== -1
				)
			) {
				var pairs = value.trim().split( '|' );
				for ( var i = 0; i < pairs.length; i ++ ) {
					var param = pairs[ i ].split( ':' );
					if ( param[0] && param[1] ) {
						newValue[ param[0] ] = usof_rawurldecode( param[1] );
					}
				}

				// JSON string
			} else if ( value && this.format === 'jsons' )  {
				newValue = JSON.parse( value );

				// JSON object
			} else if ( $.isPlainObject( value ) )  {
				newValue = $.extend( newValue, value || {} );
			}
			// Decode URL-encoded strings
			if ( !! newValue.url ) {
				newValue.url = usof_rawurldecode( newValue.url );
			}
			// Save value to fields
			this.$url
				.val( newValue.url );
			this.$target
				.prop( 'checked', ( newValue.target === '_blank' ) );
			// Save value to main field
			if ( typeof value !== 'string' ) {
				value = JSON.stringify( value );
			}
			this.$input.val( value );
		}
	};
}( jQuery );
