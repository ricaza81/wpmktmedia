/**
 * Retrieve/set/erase dom modificator class <mod>_<value> for UpSolution CSS Framework
 * @param {String} mod Modificator namespace
 * @param {String} [value] Value
 * @returns {string|jQuery}
 */
// TODO: add support for multiple (array) values
jQuery.fn.usMod = function( mod, value ) {
	if ( this.length == 0 ) {
		return this;
	}
	// Remove class modificator
	if ( value === false ) {
		return this.each( function() {
			this.className = this.className.replace( new RegExp( '(^| )' + mod + '\_[a-zA-Z0-9\_\-]+( |$)' ), '$2' );
		} );
	}
	var pcre = new RegExp( '^.*?' + mod + '\_([a-zA-Z0-9\_\-]+).*?$' ),
		arr;
	// Retrieve modificator
	if ( value === undefined ) {
		return ( arr = pcre.exec( this.get( 0 ).className ) ) ? arr[ 1 ] : false;
	}
	// Set modificator
	else {
		var regexp = new RegExp( '(^| )' + mod + '\_[a-zA-Z0-9\_\-]+( |$)' );
		return this.each( function() {
			if ( this.className.match( regexp ) ) {
				this.className = this.className.replace( regexp, '$1' + mod + '_' + value + '$2' );
			} else {
				this.className += ' ' + mod + '_' + value;
			}
		} ).trigger( 'usof.' + mod, value );
	}
};

/**
 * More info at: http://phpjs.org
 */
function usof_base64_decode( data ) {
	var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	var o1, o2, o3, h1, h2, h3, h4, bits, i = 0, ac = 0, dec = "", tmp_arr = [];
	if ( ! data ) {
		return data;
	}
	data += '';
	do {
		h1 = b64.indexOf( data.charAt( i ++ ) );
		h2 = b64.indexOf( data.charAt( i ++ ) );
		h3 = b64.indexOf( data.charAt( i ++ ) );
		h4 = b64.indexOf( data.charAt( i ++ ) );
		bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;
		o1 = bits >> 16 & 0xff;
		o2 = bits >> 8 & 0xff;
		o3 = bits & 0xff;
		if ( h3 == 64 ) {
			tmp_arr[ ac ++ ] = String.fromCharCode( o1 );
		} else if ( h4 == 64 ) {
			tmp_arr[ ac ++ ] = String.fromCharCode( o1, o2 );
		} else {
			tmp_arr[ ac ++ ] = String.fromCharCode( o1, o2, o3 );
		}
	} while ( i < data.length );
	dec = tmp_arr.join( '' );
	dec = usof_utf8_decode( dec );
	return dec;
}

function usof_base64_encode( data ) {
	var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	var o1, o2, o3, h1, h2, h3, h4, bits, i = 0, ac = 0, enc = "", tmp_arr = [];
	if ( ! data ) {
		return data;
	}
	data = usof_utf8_encode( data + '' );
	do {
		o1 = data.charCodeAt( i ++ );
		o2 = data.charCodeAt( i ++ );
		o3 = data.charCodeAt( i ++ );
		bits = o1 << 16 | o2 << 8 | o3;
		h1 = bits >> 18 & 0x3f;
		h2 = bits >> 12 & 0x3f;
		h3 = bits >> 6 & 0x3f;
		h4 = bits & 0x3f;
		tmp_arr[ ac ++ ] = b64.charAt( h1 ) + b64.charAt( h2 ) + b64.charAt( h3 ) + b64.charAt( h4 );
	} while ( i < data.length );
	enc = tmp_arr.join( '' );
	var r = data.length % 3;
	return ( r ? enc.slice( 0, r - 3 ) : enc ) + '==='.slice( r || 3 );
}

function usof_rawurldecode( str ) {
	return decodeURIComponent( str + '' );
}

function usof_rawurlencode( str ) {
	str = ( str + '' ).toString();
	return encodeURIComponent( str ).replace( /!/g, '%21' ).replace( /'/g, '%27' ).replace( /\(/g, '%28' ).replace( /\)/g, '%29' ).replace( /\*/g, '%2A' );
}

function usof_strip_tags( str ) {
	return str.replace( /(<([^>]+)>)/ig, '' ).replace( '"', '&quot;' );
}

function usof_utf8_decode( str_data ) {
	var tmp_arr = [], i = 0, ac = 0, c1 = 0, c2 = 0, c3 = 0;
	str_data += '';
	while ( i < str_data.length ) {
		c1 = str_data.charCodeAt( i );
		if ( c1 < 128 ) {
			tmp_arr[ ac ++ ] = String.fromCharCode( c1 );
			i ++;
		} else if ( c1 > 191 && c1 < 224 ) {
			c2 = str_data.charCodeAt( i + 1 );
			tmp_arr[ ac ++ ] = String.fromCharCode( ( ( c1 & 31 ) << 6 ) | ( c2 & 63 ) );
			i += 2;
		} else {
			c2 = str_data.charCodeAt( i + 1 );
			c3 = str_data.charCodeAt( i + 2 );
			tmp_arr[ ac ++ ] = String.fromCharCode( ( ( c1 & 15 ) << 12 ) | ( ( c2 & 63 ) << 6 ) | ( c3 & 63 ) );
			i += 3;
		}
	}
	return tmp_arr.join( '' );
}

function usof_utf8_encode( argString ) {
	if ( argString === null || typeof argString === "undefined" ) {
		return "";
	}
	var string = ( argString + '' );
	var utftext = "", start, end, stringl = 0;
	start = end = 0;
	stringl = string.length;
	for ( var n = 0; n < stringl; n ++ ) {
		var c1 = string.charCodeAt( n );
		var enc = null;
		if ( c1 < 128 ) {
			end ++;
		} else if ( c1 > 127 && c1 < 2048 ) {
			enc = String.fromCharCode( ( c1 >> 6 ) | 192 ) + String.fromCharCode( ( c1 & 63 ) | 128 );
		} else {
			enc = String.fromCharCode( ( c1 >> 12 ) | 224 ) + String.fromCharCode( ( ( c1 >> 6 ) & 63 ) | 128 ) + String.fromCharCode( ( c1 & 63 ) | 128 );
		}
		if ( enc !== null ) {
			if ( end > start ) {
				utftext += string.slice( start, end );
			}
			utftext += enc;
			start = end = n + 1;
		}
	}
	if ( end > start ) {
		utftext += string.slice( start, stringl );
	}
	return utftext;
}

/**
 * USOF Fields
 */
! function( $, undefined ) {

	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		_window.$usof = {};
	}
	if ( $usof.mixins === undefined ) {
		$usof.mixins = {};
	}

	/**
	 * Generate unique ID with specified length, will not affect uniqueness!
	 *
	 * @param {string} prefix The prefix to be added to the beginning of the result line
	 * @return {string} Returns unique id
	 * TODO: Move to one library and remove duplicates.
	 */
	$usof.uniqid = function( prefix ) {
		return ( prefix || '' ) + Math.random().toString( 36 ).substr( 2, 9 );
	};

	// Prototype mixin for all classes working with events
	$usof.mixins.Events = {
		/**
		 * Attach a handler to an event for the class instance
		 * @param {String} eventType A string containing event type, such as 'beforeShow' or 'change'
		 * @param {Function} handler A function to execute each time the event is triggered
		 */
		on: function( eventType, handler ) {
			if ( this.$$events === undefined ) {
				this.$$events = {};
			}
			if ( this.$$events[ eventType ] === undefined ) {
				this.$$events[ eventType ] = [];
			}
			this.$$events[ eventType ].push( handler );
			return this;
		},
		/**
		 * Remove a previously-attached event handler from the class instance
		 * @param {String} eventType A string containing event type, such as 'beforeShow' or 'change'
		 * @param {Function} [handler] The function that is to be no longer executed.
		 * @chainable
		 */
		off: function( eventType, handler ) {
			if ( this.$$events === undefined || this.$$events[ eventType ] === undefined ) {
				return this;
			}
			if ( handler !== undefined ) {
				var handlerPos = $.inArray( handler, this.$$events[ eventType ] );
				if ( handlerPos != - 1 ) {
					this.$$events[ eventType ].splice( handlerPos, 1 );
				}
			} else {
				this.$$events[ eventType ] = [];
			}
			return this;
		},
		/**
		 * @param {String} eventType
		 * @return {Boolean}
		 */
		has: function( eventType ) {
			return this.$$events[ eventType ] !== undefined && this.$$events[ eventType ].length;
		},
		/**
		 * Execute all handlers and behaviours attached to the class instance for the given event type
		 * @param {String} eventType A string containing event type, such as 'beforeShow' or 'change'
		 * @param {Array} extraParameters Additional parameters to pass along to the event handler
		 * @chainable
		 */
		trigger: function( eventType, extraParameters ) {
			if ( this.$$events === undefined || this.$$events[ eventType ] === undefined || this.$$events[ eventType ].length == 0 ) {
				return this;
			}
			var params = ( arguments.length > 2 || ! $.isArray( extraParameters ) ) ? Array.prototype.slice.call( arguments, 1 ) : extraParameters;
			// First argument is the current class instance
			params.unshift( this );
			for ( var index = 0; index < this.$$events[ eventType ].length; index ++ ) {
				this.$$events[ eventType ][ index ].apply( this.$$events[ eventType ][ index ], params );
			}
			return this;
		}
	};

	// Queues of requests, this allows you to receive data
	// with one request and send it to all subscribers
	// Note: The code is not used.
	if ( $usof.ajaxQueues === undefined ) {
		$usof.ajaxQueues = $.noop;
		$.extend( $usof.ajaxQueues.prototype, $usof.mixins.Events, {
			$$events: {},
		});
		$usof.ajaxQueues = new $usof.ajaxQueues;
	}

	/**
	 * Get the dynamic colors
	 * NOTE: Globally defining dynamic colors to optimize $usof.field[ 'color' ]
	 *
	 * @return {{}} The dynamic colors.
	 */
	$usof.getDynamicColors = function() {
		if ( $.isPlainObject( this._dynamicColors ) ) return this._dynamicColors;
		try {
			this._dynamicColors = JSON.parse( this.dynamicColors || '{}' );
		} catch ( e ) {
			this._dynamicColors = {};
		}
		return this._dynamicColors;
	};

	$usof.field = function( row, options ) {
		this.$row = $( row );
		this.type = this.$row.usMod( 'type' );
		// Get field data
		var data = this.$row.data() || {};
		this.id = data.id;
		this.name = data.name;
		this.inited = data.inited || false;
		this.$input = this.$row.find( '[name="' + data.name + '"]' );

		if ( this.inited ) {
			return;
		}

		/**
		 * Boundable field events
		 */
		this.$$events = {
			beforeShow: [],
			afterShow: [],
			change: [],
			beforeHide: [],
			afterHide: []
		};

		// Overloading selected functions, moving parent functions to "parent" namespace: init => parentInit
		if ( $usof.field[ this.type ] !== undefined ) {
			for ( var fn in $usof.field[ this.type ] ) {
				if ( ! $usof.field[ this.type ].hasOwnProperty( fn ) ) {
					continue;
				}
				if ( this[ fn ] !== undefined ) {
					var parentFn = 'parent' + fn.charAt( 0 ).toUpperCase() + fn.slice( 1 );
					this[ parentFn ] = this[ fn ];
				}
				this[ fn ] = $usof.field[ this.type ][ fn ];
			}
		}

		this.$row.data( 'usofField', this );

		// Init on first show
		var initEvent = function() {
			this.init( options );
			this.inited = true;
			this.$row.data( 'inited', this.inited );
			this.off( 'beforeShow', initEvent );
			// Remember the default value
			this._std = data.hasOwnProperty( 'std' )
				? data.std // NOTE: Used for now only for `type=select`
				: this.getValue();
		}.bind( this );
		this.on( 'beforeShow', initEvent );
	};

	$.extend( $usof.field.prototype, $usof.mixins.Events, {
		init: function() {
			if ( this._events === undefined ) {
				this._events = {};
			}
			this._events.change = function() {
				this.trigger( 'change', [ this.getValue() ] );
			}.bind( this );
			this.$input.on( 'change', this._events.change );
		},
		/**
		 * Get the value
		 *
		 * @return {mixed} The value.
		 */
		getValue: function() {
			return this.$input.val();
		},
		/**
		 * Get the default field value
		 *
		 * @return {mixed} The default value.
		 */
		getDefaultValue: function() {
			return this._std || '';
		},
		/**
		 * Set the value.
		 *
		 * @param {mixed} value The value
		 * @param {boolean} quiet The quiet
		 */
		setValue: function( value, quiet ) {
			this.$input.val( value );
			if ( ! quiet ) {
				this.trigger( 'change', [value] );
			}
		},
		/**
		 * Determine the presence of USBuilder as a context.
		 *
		 * @return {boolean} True if us builder, False otherwise.
		 */
		isUSBuilder: function() {
			return !! this.$row.closest( '.us-builder-panel-fieldset' ).length;
		}
	} );

	/**
	 * Field initialization
	 *
	 * @param options object
	 * @returns {$usof.field}
	 */
	$.fn.usofField = function( options ) {
		return new $usof.field( this, options );
	};

	/**
	 * USOF Group
	 * TODO: Need to refactor and get rid of dependencies, the object must provide an API!
	 */
	$usof.Group = function( row, options ) {
		this.init( row, options );
	};

	$.extend( $usof.Group.prototype, $usof.mixins.Events, {

		init: function( elm, options ) {

			// Elements
			this.$field = $( elm );
			this.$btnAddGroup = $( '.usof-form-group-add', this.$field );
			this.$groupPrototype = $( '.usof-form-group-prototype', this.$field );

			// Variables
			this.groupName = this.$field.data( 'name' );

			this.isBuilder = !! this.$field.parents( '.us-bld-window' ).length; // This is the builder located in the admin panel
			this.isUSBuilder = !! this.$field.parents( '.us-builder-panel-fieldset' ).length;
			this.isSortable = this.$field.hasClass( 'sortable' );
			this.isAccordion = this.$field.hasClass( 'type_accordion' );
			this.isForButtons = this.$field.hasClass( 'preview_button' );

			this.groupParams = [];
			this._changeField = {}; // The variable stores the field that has changed

			var $translations = this.$field.find( '.usof-form-group-translations' );
			this.groupTranslations = $translations.length ? ( $translations[ 0 ].onclick() || {} ) : {};

			if ( this.isBuilder ) {
				this.$parentElementForm = this.$field.closest( '.usof-form' );
				this.elementName = this.$parentElementForm.usMod( 'for' );
				this.$builderWindow = this.$field.closest( '.us-bld-window' );
			} else {
				this.$parentSection = this.$field.closest( '.usof-section' );
				$( '.usof-form-group-item', this.$field )
					.each( function( i, groupParams ) {
						var $groupParams = $( groupParams );
						if( $groupParams.closest( '.usof-form-group-prototype' ).length ) return;
						this.groupParams.push( new $usof.GroupParams( $groupParams ) );
					}.bind( this ) );
			}

			// The value is a string otherwise it will be an object
			this.hasStringValue = !! this.$field.closest( '.us-builder-panel-fieldset' ).length;

			// Remember the default value
			this._std = this.getValue();

			// Events
			this.$btnAddGroup
				.off( 'click' ) // TODO: Fix double initialization for USBuilder
				.on( 'click', this.addGroup.bind( this, undefined ) );
			this.$field
				.on( 'change', function() {
					this.trigger( 'change', this );
				}.bind( this ) )
				.on( 'click', '.ui-icon_duplicate', this.duplicateGroup.bind( this ) )
				.on( 'click', '.usof-form-group-item-controls > .ui-icon_delete', function( event ) {
					event.stopPropagation();
					var $btn = $( event.target ),
						$group = $btn
							.closest( '.usof-form-group-item' );
					this.groupDel( $group );
				}.bind( this ) );

			if ( this.isAccordion ) {
				this.$sections = this.$field.find( '.usof-form-group-item' );
				this.$field
					.on( 'click', '.usof-form-group-item-title', function( event ) {
						this.$sections = this.$field
							.find( '.usof-form-group-item' );
						var $parentSection = $( event.target )
							.closest( '.usof-form-group-item' );
						if ( $parentSection.hasClass( 'active' ) ) {
							$parentSection
								.removeClass( 'active' )
								.children( '.usof-form-group-item-content' )
								.slideUp();
						} else {
							$parentSection
								.addClass( 'active' )
								.children( '.usof-form-group-item-content' )
								.slideDown();
						}
					}.bind( this ) );
			}

			if ( this.isSortable ) {
				// Elements
				this.$body = $( _document.body );
				this.$window = $( _window );
				this.$dragshadow = $( '<div class="us-bld-editor-dragshadow"></div>' );
				// Events
				this.$field
					.on( 'dragstart', function( e ) { e.preventDefault(); })
					.on( 'mousedown', '.ui-icon_move', this._dragStart.bind( this ) );
				// Event handlers
				this._events = {
					_maybeDragMove: this._maybeDragMove.bind( this ),
					_dragMove: this._dragMove.bind( this ),
					_dragEnd: this._dragEnd.bind( this )
				};
			}
		},
		_hasClass: function( elm, cls ) {
			return ( ' ' + elm.className + ' ' ).indexOf( ' ' + cls + ' ' ) > - 1;
		},
		_isShadow: function( elm ) {
			return this._hasClass( elm, 'usof-form-group-dragshadow' );
		},
		_isSortable: function( elm ) {
			return this._hasClass( elm, 'usof-form-group-item' );
		},
		/**
		 * Reinit params
		 *
		 * @private
		 */
		_reInitParams: function() {
			this.groupParams = [];
			$( '.usof-form-group-item', this.$field )
				.each( function( i, groupParams ) {
					var $groupParams = $( groupParams );
					if( $groupParams.closest( '.usof-form-group-prototype' ).length ) return;
					var groupParams = $groupParams.data( 'usofGroupParams' );
					// Watch field changes
					$.each( groupParams.fields, function( groupParamsIndex, _, field ) {
						field.on( 'change', this._updateChangeField.bind( this, groupParamsIndex ) );
					}.bind( this, this.groupParams.length ) );
					this.groupParams.push( groupParams );
				}.bind( this ) );
			if ( ! this.isBuilder ) {
				if ( $.isEmptyObject( $usof.instance.valuesChanged ) ) {
					clearTimeout( $usof.instance.saveStateTimer );
					$usof.instance.$saveControl.usMod( 'status', 'notsaved' );
				}
				var value = this.getValue();
				$usof.instance.valuesChanged[ this.groupName ] = value;
				this.$field
					.trigger( 'change', value );
			}
		},
		/**
		 * Get the default field value
		 *
		 * @return {mixed} The default value.
		 */
		getDefaultValue: function() {
			return this._std || '';
		},
		/**
		 * Sets the value.
		 *
		 * @param {string|array} value The value
		 */
		setValue: function( value ) {
			// If the value came as a string, then we will try to convert it into an object
			if ( typeof value === 'string' && this.hasStringValue ) {
				try {
					value = JSON.parse( usof_rawurldecode( value ) || '[]' );
				} catch ( err ) {
					console.error( value, err );
					value = [];
				}
			}
			this.groupParams = [];
			$( '.usof-form-group-item', this.$field )
				.each( function( i, groupParams ) {
					var $groupParams = $( groupParams );
					if ( ! $groupParams.parent().hasClass( 'usof-form-group-prototype' ) ) {
						$groupParams.remove();
					}
				}.bind( this ) );
			$.each( value, function( index, paramsValues ) {
				var groupPrototype = this.$groupPrototype.html();
				if ( this.$btnAddGroup.length ) {
					this.$btnAddGroup.before( groupPrototype );
				} else {
					this.$field.append( groupPrototype );
				}
				var $groupParams = this.$field.find( '.usof-form-group-item' ).last();
				var groupParams = new $usof.GroupParams( $groupParams );
				groupParams.setValues( paramsValues, 1 );
				for ( var fieldId in groupParams.fields ) {
					if ( ! groupParams.fields.hasOwnProperty( fieldId ) ) {
						continue;
					}
					groupParams.fields[ fieldId ].trigger( 'change' );
					break;
				}
			}.bind( this ) );
			this._reInitParams();
		},
		/**
		 * Gets the value.
		 *
		 * @return {string|array} The value.
		 */
		getValue: function() {
			var result = [];
			$.each( this.groupParams, function( i, groupParams ) {
				result.push( groupParams.getValues() );
			} );

			if ( this.hasStringValue ) {
				try {
					result = usof_rawurlencode( JSON.stringify( result ) );
				} catch ( err ) {
					console.error( result, err );
					result = '';
				}
			}

			return result;
		},
		/**
		 * Add group
		 * @param {number} index Add a group after the specified index
		 * @return {object} $usof.GroupParams
		 */
		addGroup: function( index ) {
			this.$btnAddGroup.addClass( 'adding' );
			var $groupPrototype = $( this.$groupPrototype.html() );
			if ( this.isForButtons && index !== undefined ) {
				this.$btnAddGroup
					.closest( '.usof-form-group' )
					.find( ' > .usof-form-group-item:eq(' + parseInt( index ) + ')' )
					.after( $groupPrototype );
			} else {
				this.$btnAddGroup.before( $groupPrototype );
			}
			var groupParams = new $usof.GroupParams( $groupPrototype );
			if ( this.isForButtons && index !== undefined ) {
				this.groupParams.splice( index + 1, 0, groupParams );
			} else {
				this.groupParams.push( groupParams )
			}
			// Watch field changes
			$.each( groupParams.fields, function( groupParamsIndex, _, field ) {
				field.on( 'change', this._updateChangeField.bind( this, groupParamsIndex ) );
			}.bind( this, this.groupParams.length ) );
			if ( ! this.isBuilder ) {
				if ( $.isEmptyObject( $usof.instance.valuesChanged ) ) {
					clearTimeout( $usof.instance.saveStateTimer );
					$usof.instance.$saveControl.usMod( 'status', 'notsaved' );
				}
				var value = this.getValue();
				$usof.instance.valuesChanged[ this.groupName ] = value;
				this.$field
					.trigger( 'change', value );
			}
			// TODO: Need to get rid of the crutch this.isForButtons
			if ( this.isForButtons ) {
				var newIndex = this.groupParams.length,
					newId = 1,
					newIndexIsUnique;
				for ( var i in this.groupParams ) {
					newId = Math.max( ( parseInt( this.groupParams[ i ].fields.id.getValue() ) || 0 ) + 1, newId );
				}
				do {
					newIndexIsUnique = true;
					for ( var i in this.groupParams ) {
						if ( this.groupParams[ i ].fields.name.getValue() == this.groupTranslations.style + ' ' + newIndex ) {
							newIndex ++;
							newIndexIsUnique = false;
							break;
						}
					}
				} while ( ! newIndexIsUnique );
				groupParams.fields.name.setValue( this.groupTranslations.style + ' ' + newIndex );
				groupParams.fields.id.setValue( newId );
			}
			// If the group is running in a USBuilder context then set the title for accordion
			// NOTE: This is a forced decision that will be fixed when refactoring the code!
			if ( this.isUSBuilder ) {
				groupParams._setTitleForAccordion();
			}
			this.$btnAddGroup.removeClass( 'adding' );
			return groupParams;
		},
		/**
		 * Duplicate group.
		 *
		 * @param {Event} e
		 */
		duplicateGroup: function( e ) {
			var $target = $( e.currentTarget ),
				$group = $target.closest( '.usof-form-group-item' ),
				index = $group.index() - 1;
			if ( this.groupParams.hasOwnProperty( index ) ) {
				var $item = this.groupParams[ index ],
					values = $item.getValues(),
					number = 0;
				values.name = $.trim( values.name.replace( /\s?\(.*\)$/, '' ) );
				// Create new group name
				for ( var i in this.groupParams ) {
					var name = this.groupParams[ i ].getValue( 'name' ) || '',
						copyPattern = new RegExp( values.name + '\\s?\\((\\d+)*', 'm' );
					var numMatches = name.match( copyPattern );
					if ( numMatches !== null ) {
						number = Math.max( number, parseInt( numMatches[ 1 ] || 1 ) );
					}
				}
				values.name += ' (' + ( ++ number ) + ')';
				var newGroup = this.addGroup( index );
				newGroup.setValues( $.extend( values, {
					id: newGroup.getValue( 'id' )
				} ) );
			}
		},
		groupDel: function( $group ) {
			if ( ! confirm( this.groupTranslations.deleteConfirm ) ) {
				return false;
			}
			$group.addClass( 'deleting' );
			$group.remove();
			this._reInitParams();
		},
		// Drag'n'drop functions
		_dragStart: function( event ) {
			event.stopPropagation();
			this.$draggedElm = $( event.target ).closest( '.usof-form-group-item' );
			this.detached = false;
			this._updateBlindSpot( event );
			this.elmPointerOffset = [parseInt( event.pageX ), parseInt( event.pageY )];
			this.$body.on( 'mousemove', this._events._maybeDragMove );
			this.$window.on( 'mouseup', this._events._dragEnd );
		},
		_updateBlindSpot: function( event ) {
			this.blindSpot = [event.pageX, event.pageY];
		},
		_isInBlindSpot: function( event ) {
			return Math.abs( event.pageX - this.blindSpot[ 0 ] ) <= 20 && Math.abs( event.pageY - this.blindSpot[ 1 ] ) <= 20;
		},
		_maybeDragMove: function( event ) {
			event.stopPropagation();
			if ( this._isInBlindSpot( event ) ) {
				return;
			}
			this.$body.off( 'mousemove', this._events._maybeDragMove );
			this._detach();
			this.$body.on( 'mousemove', this._events._dragMove );
		},
		_detach: function( event ) {
			var offset = this.$draggedElm.offset();
			this.elmPointerOffset[ 0 ] -= offset.left;
			this.elmPointerOffset[ 1 ] -= offset.top;
			this.$draggedElm.find( '.usof-form-group-item-title' ).hide();
			if ( ! this.isAccordion || this.$draggedElm.hasClass( 'active' ) ) {
				this.$draggedElm.find( '.usof-form-group-item-content' ).hide();
			}
			this.$dragshadow.css( {
				width: this.$draggedElm.outerWidth()
			} ).insertBefore( this.$draggedElm );
			this.$draggedElm.addClass( 'dragged' ).css( {
				position: 'absolute',
				'pointer-events': 'none',
				zIndex: 10000,
				width: this.$draggedElm.width(),
				height: this.$draggedElm.height()
			} ).css( offset ).appendTo( this.$body );
			if ( this.isBuilder ) {
				this.$builderWindow.addClass( 'dragged' );
			}
			this.detached = true;
		},
		_dragMove: function( event ) {
			event.stopPropagation();
			this.$draggedElm.css( {
				left: event.pageX - this.elmPointerOffset[ 0 ],
				top: event.pageY - this.elmPointerOffset[ 1 ]
			} );
			if ( this._isInBlindSpot( event ) ) {
				return;
			}
			var elm = event.target;
			// Checking two levels up
			for ( var level = 0; level <= 2; level ++, elm = elm.parentNode ) {
				if ( this._isShadow( elm ) ) {
					return;
				}
				// Workaround for IE9-10 that don't support css pointer-events property
				if ( this._hasClass( elm, 'detached' ) ) {
					this.$draggedElm.detach();
					break;
				}
				if ( this._isSortable( elm ) ) {

					// Dropping element before or after sortables based on their relative position in DOM
					var nextElm = elm.previousSibling,
						shadowAtLeft = false;
					while ( nextElm ) {
						if ( nextElm == this.$dragshadow[ 0 ] ) {
							shadowAtLeft = true;
							break;
						}
						nextElm = nextElm.previousSibling;
					}
					this.$dragshadow[ shadowAtLeft ? 'insertAfter' : 'insertBefore' ]( elm );
					this._dragDrop( event );
					break;
				}
			}
		},
		/**
		 * Complete drop
		 * @param event
		 */
		_dragDrop: function( event ) {
			this._updateBlindSpot( event );
		},
		_dragEnd: function( event ) {
			this.$body.off( 'mousemove', this._events._maybeDragMove ).off( 'mousemove', this._events._dragMove );
			this.$window.off( 'mouseup', this._events._dragEnd );
			if ( this.detached ) {
				this.$draggedElm.removeClass( 'dragged' ).removeAttr( 'style' ).insertBefore( this.$dragshadow );
				this.$dragshadow.detach();
				if ( this.isBuilder ) {
					this.$builderWindow.removeClass( 'dragged' );
				}
				this.$draggedElm.find( '.usof-form-group-item-title' ).show();
				if ( ! this.isAccordion || this.$draggedElm.hasClass( 'active' ) ) {
					this.$draggedElm.find( '.usof-form-group-item-content' ).show();
				}
				this._reInitParams();
			}
		},
		/**
		 * Get the change field
		 * NOTE: The method returns an object that contains the index of the group
		 * and the object of the field that was changed.
		 *
		 * @return {{}}
		 */
		getChangeField: function() {
			var changeField = this._changeField;
			this._changeField = {};
			return changeField;
		},
		/**
		 * Handler for changing the field and saving the change to a variable.
		 *
		 * @private
		 */
		_updateChangeField: function( groupParamsIndex, field ) {
			if ( ! this.isBuilder ) {
				this.trigger( 'change', this.getValue() );
			}
			this._changeField = {
				index: groupParamsIndex,
				field: field
			};
		}
	} );

	/**
	 * Group initialization
	 */
	$.fn.usofGroup = function( options ) {
		return new $usof.Group( this, options );
	};
}( jQuery );

/**
 * USOF Drag and Drop
 **/
;( function( $, undefined ) {
	/**
	 * Drag and Drop Plugin
	 * Triggers: init, dragdrop, dragstart, dragend, drop, over, leave
	 *
	 * @param string || object container
	 * @param object options
	 * @return $usof.dragDrop
	 */
	$usof.dragDrop = function( container, options ) {
		// Variables
		this._defaults = {
			// The selector that will move
			itemSelector: '.usof-draggable-selector',
			// CSS classes for displaying states
			css: {
				moving: 'usof-dragdrop-moving',
				active: 'usof-dragdrop-active',
				over: 'usof-dragdrop-over'
			},
		};
		this._name = '$usof.dragDrop'; // The export plugin name
		this.options = $.extend( {}, this._defaults, options || {} );

		// CSS Classes for the plugin that reflect the actions within the plugin
		this.css = this.options.css;

		// Elements
		this.$container = $( container );

		// Plugin initialization
		this.init.call( this );
	};

	// Extend prototype with events and new methods
	$.extend( $usof.dragDrop.prototype, $usof.mixins.Events, {
		/**
		 * Initializes the object.
		 *
		 * @return self
		 */
		init: function() {
			var itemSelector = this.options.itemSelector;
			if ( ! itemSelector ) {
				return;
			}

			this.$container.data( 'usofDragDrop', this );
			this.trigger( 'init', this );

			this.$container
				.addClass( 'usof-dragdrop' )

				/* Begin handler's from item selector
				 ------------------------------------------------------------*/
				.on( 'mouseup', itemSelector, function( e ) {
					this.$container
						.removeClass( this.css.moving )
						.find( '> [draggable]' )
						.removeAttr( 'draggable' );
					$( '> .' + this.css.active, this.$container )
						.removeClass( this.css.active );
					this.trigger( 'dragdrop', e, this );
					return true;
				}.bind( this ) )

				.on( 'dragenter', itemSelector, function( e ) {
					e.stopPropagation();
					e.preventDefault();
					this.trigger( 'dragdrop', e, this );
					return true;
				}.bind( this ) )

				.on( 'drop', itemSelector, function( e ) {
					var targetId = e.originalEvent.dataTransfer.getData( 'Text' ),
						$el = $( '> [usof-target-id="' + targetId + '"]', this.$container ),
						$target = $( e.currentTarget );
					$el.removeAttr( 'usof-target-id' );

					$target
						.before( $el );
					$( '> .' + this.css.active, this.$container )
						.removeClass( this.css.active );
					$( '> .' + this.css.over, this.$container )
						.removeClass( this.css.over );
					e.stopPropagation();
					this.trigger( 'drop', e, this )
						.trigger( 'dragdrop', e, this );
					return false;
				}.bind( this ) )

				.on( 'dragover', itemSelector, function( e ) {
					e.stopPropagation();
					e.preventDefault();
					$( e.currentTarget === e.target ? e.target : e.currentTarget )
						.addClass( this.css.over );
					this.trigger( 'over', e, this )
						.trigger( 'dragdrop', e, this );
					return true;
				}.bind( this ) )

				.on( 'dragleave', itemSelector, function( e ) {
					e.stopPropagation();
					e.preventDefault();
					$( e.target ).removeClass( this.css.over );
					this.trigger( 'leave', e, this )
						.trigger( 'dragdrop', e, this );
					return true;
				}.bind( this ) )

				/* Begin handler's from container
				 ------------------------------------------------------------*/

				.on( 'mousedown', itemSelector, function( e ) {
					this.$container
						.addClass( this.css.moving );
					var $target = $( this._getTarget( e ) );
					$target.addClass( this.css.active );
					if ( ! $target.is( '[draggable="false"]' ) && ! $target.is( 'input' ) ) {
						$target.attr( 'draggable', true );
					}
					this.trigger( 'dragdrop', e, this );
					return true;
				}.bind( this ) )

				.on( 'mouseup', itemSelector, function( e ) {
					$( '> .' + this.css.active, this.$container )
						.removeClass( this.css.active );
					this.trigger( 'dragdrop', e, this );
					return true;
				}.bind( this ) )

				.on( 'dragstart', itemSelector, function( e ) {
					e.stopPropagation();
					var $target = $( this._getTarget( e ) ),
						// Generate unique id for transfer text
						targetId = $usof.uniqid();
					$target.attr( 'usof-target-id', targetId );
					e.originalEvent.dataTransfer.effectAllowed = 'move';
					e.originalEvent.dataTransfer.setData( 'Text', targetId );
					e.originalEvent.dataTransfer.setDragImage( $target.get( 0 ), e.offsetX, e.offsetY );
					this.trigger( 'dragstart', e, this )
						.trigger( 'dragdrop', e, this );
					return true;
				}.bind( this ) )

				.on( 'dragend', function( e ) {
					e.stopPropagation();
					this.$container
						.removeClass( this.css.moving )
						.find( '> [draggable]' )
						.removeAttr( 'draggable' );
					this.trigger( 'dragend', e, this )
						.trigger( 'dragdrop', e, this );
					return true;
				}.bind( this ) );

			return this;
		},
		/**
		 * Get the node to be moved.
		 *
		 * @param {Event} e
		 * @return node
		 */
		_getTarget: function( e ) {
			var $target = $( e.target ),
				itemSelector = ( this.options.itemSelector || '' ).replace( '>', '' ).trim();
			if ( itemSelector && !! $target.parent( itemSelector ).length ) {
				$target = $target.parent( itemSelector );
			}
			return $target.get( 0 );
		}
	} );

	/**
	 * @param object options The options
	 * @return jQuery object
	 */
	$.fn.usofDragDrop = function( options ) {
		return this.each( function() {
			if ( ! $.data( this, 'usofDragDrop' ) ) {
				$.data( this, 'usofDragDrop', new $usof.dragDrop( this, options ) );
			}
		} );
	};

} )( jQuery );

/**
 * USOF Core
 */
;! function( $ ) {

	var _window = window,
		_document = document;

	$usof.ajaxUrl = $( '.usof-container' ).data( 'ajaxurl' ) || /* WP variable */ ajaxurl;

	// Prototype mixin for all classes working with fields
	if ( $usof.mixins === undefined ) {
		$usof.mixins = {};
	}
	// TODO: Need to refactor and get rid of dependencies, the object must provide an API!
	$usof.mixins.Fieldset = {
		/**
		 * Initialize fields inside of a container
		 * @param {jQuery} $container
		 */
		initFields: function( $container ) {
			if ( this.$fields === undefined ) {
				this.$fields = {};
			}
			if ( this.fields === undefined ) {
				this.fields = {};
			}
			if ( this.groups === undefined ) {
				this.groups = {};
			}
			// Showing conditions (fieldId => condition)
			if ( this.showIf === undefined ) {
				this.showIf = {};
			}
			// Showing dependencies (fieldId => affected field ids)
			if ( this.showIfDeps === undefined ) {
				this.showIfDeps = {};
			}
			var groupElms = [];
			$( '.usof-form-row, .usof-form-wrapper, .usof-form-group', $container )
				.each(function( index, elm ) {
					var $field = $( elm ),
						name = $field.data( 'name' ),
						isRow = $field.hasClass( 'usof-form-row' ),
						isGroup = $field.hasClass( 'usof-form-group' ),
						isInGroup = $field.parents( '.usof-form-group' ).length,
						$showIf = $field.find(
							( isRow || isGroup )
								? '> .usof-form-row-showif'
								: '> .usof-form-wrapper-content > .usof-form-wrapper-showif'
						);

					// If the element is in the prototype, then we will ignore the init
					if ( $field.closest( '.usof-form-group-prototype' ).length ) {
						return;
					}

					// Exclude fields for `design_options` as they have their own group
					if (
						isRow
						&& $field.closest( '.usof-design-options' ).length
						&& ! $container.is('[data-responsive-state-content]')
					) {
						return;
					}

					this.$fields[ name ] = $field;
					if ( $showIf.length > 0 ) {
						this.showIf[ name ] = $showIf[ 0 ].onclick() || [];
						// Writing dependencies
						var showIfVars = this._getShowIfVariables( this.showIf[ name ] );
						for ( var i = 0; i < showIfVars.length; i ++ ) {
							if ( this.showIfDeps[ showIfVars[ i ] ] === undefined ) {
								this.showIfDeps[ showIfVars[ i ] ] = [];
							}
							this.showIfDeps[ showIfVars[ i ] ].push( name );
						}
					}
					if ( isRow && ( ! isInGroup || this.isGroupParams ) ) {
						this.fields[ name ] = $field.usofField( elm );
					} else if ( isGroup ) {
						this.groups[ name ] = $field.usofGroup( elm );
					}
				}.bind( this ) );
			for ( var fieldName in this.showIfDeps ) {
				if ( ! this.showIfDeps.hasOwnProperty( fieldName ) || this.fields[ fieldName ] === undefined ) {
					continue;
				}
				this.fields[ fieldName ]
					.on( 'change', function( field ) {
						this.updateVisibility( field.name );
					}.bind( this ) );
				// Update displayed fields on initialization
				if ( !! this.isGroupParams ) {
					this.updateVisibility( fieldName, /* isAnimated */false, /* isCurrentShown */this.getCurrentShown( fieldName ) );
				}
			}

			// Get default values for fields
			if ( this._defaultValues === undefined ) {
				this._defaultValues = this.getValues();
			}
		},

		/**
		 * Show / hide the field based on its showIf condition.
		 *
		 * @param {string} fieldName The field name
		 * @param {boolean} isAnimated  Indicates if animated
		 * @param {boolean} isCurrentShown Indicates if parent
		 */
		updateVisibility: function( fieldName, isAnimated, isCurrentShown ) {
			if ( ! fieldName || ! this.showIfDeps[ fieldName ] ) return;

			// TODO: Clear code
			if ( isAnimated === undefined ) {
				isAnimated = true;
			}
			if ( isCurrentShown === undefined ) {
				isCurrentShown = true;
			}

			this.showIfDeps[ fieldName ]
				.map( function( depFieldName ) {
					var field = this.fields[ depFieldName ],
						$field = this.$fields[ depFieldName ],
						isShown = this.getCurrentShown( depFieldName ),
						shouldBeShown = this.executeShowIf( this.showIf[ depFieldName ], this.getValue.bind( this ) );

					// Check visible
					if ( ( ! shouldBeShown && isShown ) || ! isCurrentShown ) {
						isShown = false;
					} else if ( shouldBeShown && ! isShown ) {
						isShown = true;
					}

					// Set current visibility
					$field
						.stop( true, false )
						.data( 'isShown', isShown );

					if ( isShown ) {
						this.fireFieldEvent( $field, 'beforeShow' );
						// TODO: Add css animations is enabled isAnimated
						$field.show();
						this.fireFieldEvent( $field, 'afterShow' );
						if ( field instanceof $usof.field ) {
							field.trigger( 'change', field.getValue() );
						}
					} else {
						this.fireFieldEvent( $field, 'beforeHide' );
						// TODO: Add css animations is enabled isAnimated
						$field.hide();
						this.fireFieldEvent( $field, 'afterHide' );
					}

					// Set visibility for tree dependencies
					if ( !! this.showIfDeps[ depFieldName ] ) {
						this.updateVisibility( depFieldName, isAnimated, isShown );
					}

				}.bind( this ) );
		},

		/**
		 * Get a shown state.
		 *
		 * @param {string} fieldName The field name
		 * @return {boolean} True if the specified field identifier is shown, False otherwise.
		 */
		getCurrentShown: function( fieldName ) {
			if ( ! fieldName || ! this.$fields[ fieldName ] ) return true;
			var $field = this.$fields[ fieldName ],
				isShown = $field.data( 'isShow' );
			if ( isShown === undefined ) {
				isShown = $field.css( 'display' ) !== 'none';
			}
			return !! isShown;
		},

		/**
		 * Get all field names that affect the given 'show_if' condition
		 *
		 * @param {Array} condition
		 * @returns {Array}
		 */
		_getShowIfVariables: function( condition ) {
			if ( ! $.isArray( condition ) || condition.length < 3 ) {
				return [];
			} else if ( $.inArray( condition[ 1 ].toLowerCase(), ['and', 'or'] ) != - 1 ) {
				// Complex or / and statement
				var vars = this._getShowIfVariables( condition[ 0 ] ),
					index = 2;
				while ( condition[ index ] !== undefined ) {
					vars = vars.concat( this._getShowIfVariables( condition[ index ] ) );
					index = index + 2;
				}
				return vars;
			} else {
				return [condition[ 0 ]];
			}
		},

		/**
		 * Execute 'show_if' condition
		 * @param {Array} condition
		 * @param {Function} getValue Function to get the needed value
		 * @returns {Boolean} Should be shown?
		 */
		executeShowIf: function( condition, getValue ) {
			var result = true;
			if ( ! $.isArray( condition ) || condition.length < 3 ) {
				return result;
			} else if ( $.inArray( condition[ 1 ].toLowerCase(), ['and', 'or'] ) != - 1 ) {
				// Complex or / and statement
				result = this.executeShowIf( condition[ 0 ], getValue );
				var index = 2;
				while ( condition[ index ] !== undefined ) {
					condition[ index - 1 ] = condition[ index - 1 ].toLowerCase();
					if ( condition[ index - 1 ] == 'and' ) {
						result = ( result && this.executeShowIf( condition[ index ], getValue ) );
					} else if ( condition[ index - 1 ] == 'or' ) {
						result = ( result || this.executeShowIf( condition[ index ], getValue ) );
					}
					index = index + 2;
				}
			} else {
				var value = getValue( condition[ 0 ] );
				if ( value === undefined ) {
					return true;
				}
				if ( condition[ 1 ] == '=' ) {
					if ( $.isArray( condition[ 2 ] ) ) {
						result = ( $.inArray( value, condition[ 2 ] ) != - 1 );
					} else {
						result = ( value == condition[ 2 ] );
					}
				} else if ( condition[ 1 ] == '!=' ) {
					if ( $.isArray( condition[ 2 ] ) ) {
						result = ( $.inArray( value, condition[ 2 ] ) == - 1 );
					} else {
						result = ( value != condition[ 2 ] );
					}
				} else if ( condition[ 1 ] == 'has' ) {
					result = ( ! $.isArray( value ) || $.inArray( condition[ 2 ], value ) != - 1 );
				} else if ( condition[ 1 ] == '<=' ) {
					result = ( value <= condition[ 2 ] );
				} else if ( condition[ 1 ] == '<' ) {
					result = ( value < condition[ 2 ] );
				} else if ( condition[ 1 ] == '>' ) {
					result = ( value > condition[ 2 ] );
				} else if ( condition[ 1 ] == '>=' ) {
					result = ( value >= condition[ 2 ] );
				} else {
					result = true;
				}
			}
			return result;
		},

		/**
		 * Find all the fields within $container and fire a certain event there
		 * @param $container jQuery
		 * @param trigger string
		 */
		fireFieldEvent: function( $container, trigger ) {
			if ( ! $container.hasClass( 'usof-form-row' ) ) {
				$container.find( '.usof-form-row' ).each( function( index, block ) {
					var $block = $( block ),
						isShown = $block.data( 'isShown' );
					if ( isShown === undefined ) {
						isShown = ( $block.css( 'display' ) != 'none' );
					}
					// The block is not actually shown or hidden in this case
					if ( ! isShown && ['beforeShow', 'afterShow', 'beforeHide', 'afterHide'].indexOf( trigger ) !== -1 ) {
						return;
					}
					if ( $block.data( 'usofField' ) == undefined ) {
						return;
					}
					$block.data( 'usofField' ).trigger( trigger );
				}.bind( this ) );

			} else if ( $container.data( 'usofField' ) instanceof $usof.field ) {
				$container.data( 'usofField' ).trigger( trigger );
			}
		},

		/**
		 * Get the value
		 *
		 * @param {string} id The identifier
		 * @return {mixed} The value.
		 */
		getValue: function( id ) {
			if ( this.fields[ id ] === undefined ) {
				return undefined;
			}
			return this.fields[ id ].getValue();
		},

		/**
		 * Set some particular field value
		 * @param {String} id
		 * @param {String} value
		 * @param {Boolean} quiet Don't fire onchange events
		 */
		setValue: function( id, value, quiet ) {
			if ( this.fields[ id ] === undefined ) {
				return;
			}
			var shouldFireShow = ! this.fields[ id ].inited;
			if ( shouldFireShow ) {
				this.fields[ id ].trigger( 'beforeShow' );
				this.fields[ id ].trigger( 'afterShow' );
			}
			this.fields[ id ].setValue( value, quiet );
			if ( shouldFireShow ) {
				this.fields[ id ].trigger( 'beforeHide' );
				this.fields[ id ].trigger( 'afterHide' );
			}
		},

		/**
		 * Get the values.
		 *
		 * @return {mixed} The values.
		 */
		getValues: function() {
			var values = {};
			// Regular values
			for ( var fieldId in this.fields ) {
				if ( ! this.fields.hasOwnProperty( fieldId ) ) {
					continue;
				}
				values[ fieldId ] = this.getValue( fieldId );
			}
			// Groups
			for ( var groupId in this.groups ) {
				values[ groupId ] = this.groups[ groupId ].getValue();
			}
			return values;
		},

		/**
		 * Set the values
		 * @param {Object} values
		 * @param {Boolean} quiet Don't fire onchange events, just change the interface
		 */
		setValues: function( values, quiet ) {
			// Regular values
			for ( fieldId in this.fields ) {
				if ( values.hasOwnProperty( fieldId ) ) {
					var currentValue = values[ fieldId ];
					this.setValue( fieldId, currentValue, quiet );
					if ( ! quiet ) {
						this.fields[ fieldId ].trigger( 'change', [ currentValue ] );
					}

					// Restoring the default value
				} else if( this._defaultValues.hasOwnProperty( fieldId ) ) {
					var defaultValue = this._defaultValues[ fieldId ];
					this.setValue( fieldId, defaultValue, quiet );
				}
			}
			// Groups
			for ( var groupId in this.groups ) {
				this.groups[ groupId ].setValue( values[ groupId ] );
			}
			if ( quiet ) {
				// Update fields visibility anyway
				for ( var fieldName in this.showIfDeps ) {
					if ( ! this.showIfDeps.hasOwnProperty( fieldName ) || this.fields[ fieldName ] === undefined ) {
						continue;
					}
					this.updateVisibility( fieldName, /* isAnimated */false );
				}
			}
		},

		/**
		 * JavaScript representation of us_prepare_icon_tag helper function + removal of wrong symbols
		 * @param {String} iconClass
		 * @returns {String}
		 */
		prepareIconTag: function( iconValue ) {
			iconValue = iconValue.trim().split( '|' );
			if ( iconValue.length != 2 ) {
				return '';
			}
			var iconTag = '';
			iconValue[ 0 ] = iconValue[ 0 ].toLowerCase();
			if ( iconValue[ 0 ] == 'material' ) {
				iconTag = '<i class="material-icons">' + iconValue[ 1 ] + '</i>';
			} else {
				if ( iconValue[ 1 ].substr( 0, 3 ) == 'fa-' ) {
					iconTag = '<i class="' + iconValue[ 0 ] + ' ' + iconValue[ 1 ] + '"></i>';
				} else {
					iconTag = '<i class="' + iconValue[ 0 ] + ' fa-' + iconValue[ 1 ] + '"></i>';
				}
			}

			return iconTag
		}
	};

	// TODO: Need to refactor and get rid of dependencies, the object must provide an API!
	$usof.GroupParams = function( container ) {
		this.$container = $( container );
		this.$group = this.$container.closest( '.usof-form-group' );
		this.group = this.$group.data( 'name' );

		this.isGroupParams = true;
		this.isBuilder = !! this.$container.parents( '.us-bld-window' ).length;
		this.isForButtons = this.$group.hasClass( 'preview_button' );
		this.isForFormElms = this.$group.hasClass( 'preview_input_fields' );

		this.initFields( this.$container );
		this.fireFieldEvent( this.$container, 'beforeShow' );
		this.fireFieldEvent( this.$container, 'afterShow' );

		this.accordionTitle = ( this.$group.data( 'accordion-title' ) != undefined )
			? decodeURIComponent( this.$group.data( 'accordion-title' ) )
			: '';

		// If the title for the accordion is not empty then we will watch
		// the changes in the fields in order to correctly update the title
		if ( ! this._isEmptyAccordionTitle() ) {
			for ( var fieldId in this.fields ) {
				if ( ! this.fields.hasOwnProperty( fieldId ) ) {
					continue;
				}
				this.fields[ fieldId ].on( 'change', this._setTitleForAccordion.bind( this ) );
			}
		}

		if ( ! this.isBuilder ) {
			for ( var fieldId in this.fields ) {
				if ( ! this.fields.hasOwnProperty( fieldId ) ) {
					continue;
				}
				this.fields[ fieldId ].on( 'change', function( field, value ) {
					if ( $.isEmptyObject( $usof.instance.valuesChanged ) ) {
						clearTimeout( $usof.instance.saveStateTimer );
						$usof.instance.$saveControl.usMod( 'status', 'notsaved' );
					}
					if (
						this.group !== undefined
						&& $usof.instance.groups[ this.group ] instanceof $usof.Group
					) {
						$usof.instance.valuesChanged[ this.group ] = $usof.instance.groups[ this.group ].getValue();
					}
				}.bind( this ) );
			}
		}

		this.$container.data( 'usofGroupParams', this );

		if ( this.isForButtons ) {
			this.$buttonPreview = this.$container.find( '.usof-form-group-item-title .usof-btn-preview' );
			new $usof.ButtonPreview( this.$buttonPreview );
		} else if ( this.isForFormElms ) {
			new $usof.FormElmsPreview( this.$container.find( '.usof-input-preview' ) );
		}
	};

	$.extend( $usof.GroupParams.prototype, $usof.mixins.Fieldset, {

		/**
		 * Determines if empty accordion title
		 *
		 * @private
		 * @return {boolean} True if empty accordion title, False otherwise.
		 */
		_isEmptyAccordionTitle: function() {
			return this.accordionTitle === undefined || this.accordionTitle === '';
		},

		/**
		 * Sets the title for accordion
		 *
		 * @private
		 */
		_setTitleForAccordion: function() {
			if ( this._isEmptyAccordionTitle() ) return;
			// Get element $title
			this.$title = this.$container.find( '.usof-form-group-item-title' );
			if ( this.isForButtons ) {
				this.$title = this.$title.find( '.usof-btn-label' );
			}
			var title = this.accordionTitle;
			for ( var fieldId in this.fields ) {
				if (
					! this.fields.hasOwnProperty( fieldId )
					|| title.indexOf( fieldId ) < 0
				) {
					continue;
				}
				var field = this.fields[ fieldId ],
					value = this.getValue( fieldId );
				if (
					field.hasOwnProperty( 'type' )
					&& field.type === 'select'
				) {
					var $option = $( 'option[value="' + value + '"]', field.$container );
					if ( $option.length && $option.html() !== '' ) {
						value = $option.html();
					}
				}
				title = title.replace( fieldId, value );
			}

			this.$title.text( title );
		}
	} );

	var USOF_Meta = function( container ) {
		this.$container = $( container );
		this.initFields( this.$container );

		this.fireFieldEvent( this.$container, 'beforeShow' );
		this.fireFieldEvent( this.$container, 'afterShow' );

		for ( var fieldId in this.fields ) {
			if ( ! this.fields.hasOwnProperty( fieldId ) ) {
				continue;
			}
			this.fields[ fieldId ].on( 'change', function( field, value ) {
				USMMSettings = {};
				for ( var savingFieldId in this.fields ) {
					USMMSettings[ savingFieldId ] = this.fields[ savingFieldId ].getValue();
				}
				$( _document.body ).trigger( 'usof_mm_save' );
			}.bind( this ) );
		}

	};
	$.extend( USOF_Meta.prototype, $usof.mixins.Fieldset, {} );

	var USOF = function( container ) {
		if ( _window.$usof === undefined ) {
			_window.$usof = {};
		}
		$usof.instance = this;
		this.$container = $( container );
		this.$title = this.$container.find( '.usof-header-title h2' );

		this.$container.addClass( 'inited' );

		this.initFields( this.$container );

		this.active = null;
		this.$sections = {};
		this.$sectionContents = {};
		this.sectionFields = {};
		$.each( this.$container.find( '.usof-section' ), function( index, section ) {
			var $section = $( section ),
				sectionId = $section.data( 'id' );
			this.$sections[ sectionId ] = $section;
			this.$sectionContents[ sectionId ] = $section.find( '.usof-section-content' );
			if ( $section.hasClass( 'current' ) ) {
				this.active = sectionId;
			}
			this.sectionFields[ sectionId ] = [];
			$.each( $section.find( '.usof-form-row' ), function( index, row ) {
				var $row = $( row ),
					fieldName = $row.data( 'name' );
				if ( fieldName ) {
					this.sectionFields[ sectionId ].push( fieldName );
				}
			}.bind( this ) );
		}.bind( this ) );

		this.sectionTitles = {};
		$.each( this.$container.find( '.usof-nav-item.level_1' ), function( index, item ) {
			var $item = $( item ),
				sectionId = $item.data( 'id' );
			this.sectionTitles[ sectionId ] = $item.find( '.usof-nav-title' ).html();
		}.bind( this ) );

		this.navItems = this.$container.find( '.usof-nav-item.level_1, .usof-section-header' );
		this.sectionHeaders = this.$container.find( '.usof-section-header' );
		this.sectionHeaders.each( function( index, item ) {
			var $item = $( item ),
				sectionId = $item.data( 'id' );
			$item.on( 'click', function() {
				this.openSection( sectionId );
			}.bind( this ) );
		}.bind( this ) );

		// Handling initial document hash
		if ( _document.location.hash && _document.location.hash.indexOf( '#!' ) == - 1 ) {
			this.openSection( _document.location.hash.substring( 1 ) );
		}

		// Initializing fields at the shown section
		if ( this.$sections[ this.active ] !== undefined ) {
			this.fireFieldEvent( this.$sections[ this.active ], 'beforeShow' );
			this.fireFieldEvent( this.$sections[ this.active ], 'afterShow' );
		}

		// Save action
		this.$saveControl = this.$container.find( '.usof-control.for_save' );
		this.$saveBtn = this.$saveControl.find( '.usof-button' ).on( 'click', this.save.bind( this ) );
		this.$saveMessage = this.$saveControl.find( '.usof-control-message' );
		this.valuesChanged = {};
		this.saveStateTimer = null;
		for ( var fieldId in this.fields ) {
			if ( ! this.fields.hasOwnProperty( fieldId ) ) {
				continue;
			}
			this.fields[ fieldId ].on( 'change', function( field, value ) {
				if ( $.isEmptyObject( this.valuesChanged ) ) {
					clearTimeout( this.saveStateTimer );
					this.$saveControl.usMod( 'status', 'notsaved' );
				}
				this.valuesChanged[ field.name ] = value;
			}.bind( this ) );
		}

		this.$window = $( _window );
		this.$header = this.$container.find( '.usof-header' );
		this.$schemeBtn = this.$container.find( '.for_schemes' );
		this.$schemeBtn.on( 'click', function() {
			$( '.usof-form-row.type_style_scheme' ).show()
		}.bind( this ) );

		this._events = {
			scroll: this.scroll.bind( this ),
			resize: this.resize.bind( this )
		};

		this.resize();
		this.$window.on( 'resize load', this._events.resize );
		this.$window.on( 'scroll', this._events.scroll );
		this.$window.on( 'hashchange', function() {
			this.openSection( _document.location.hash.substring( 1 ) );
		}.bind( this ) );

		$( _window ).bind( 'keydown', function( event ) {
			if ( event.ctrlKey || event.metaKey ) {
				if ( String.fromCharCode( event.which ).toLowerCase() == 's' ) {
					event.preventDefault();
					$usof.instance.save();
				}
			}
		} );
	};
	$.extend( USOF.prototype, $usof.mixins.Fieldset, {
		scroll: function() {
			this.$container.toggleClass( 'footer_fixed', this.$window.scrollTop() > this.headerAreaSize );
		},

		resize: function() {
			if ( ! this.$header.length ) {
				return;
			}
			this.headerAreaSize = this.$header.offset().top + this.$header.outerHeight();
			this.scroll();
		},

		openSection: function( sectionId ) {
			if ( sectionId == this.active || this.$sections[ sectionId ] === undefined ) {
				return;
			}
			if ( this.$sections[ this.active ] !== undefined ) {
				this.hideSection();
			}
			this.showSection( sectionId );

			this.$schemeBtn = this.$container.find( '.for_schemes' );
			if ( sectionId == 'colors' ) {
				this.$schemeBtn.removeClass( 'hidden' );
			} else {
				this.$schemeBtn.addClass( 'hidden' );
			}
		},

		showSection: function( sectionId ) {
			var curItem = this.navItems.filter( '[data-id="' + sectionId + '"]' );
			curItem.addClass( 'current' );
			this.fireFieldEvent( this.$sectionContents[ sectionId ], 'beforeShow' );
			this.$sectionContents[ sectionId ].stop( true, false ).fadeIn();
			this.$title.html( this.sectionTitles[ sectionId ] );
			this.fireFieldEvent( this.$sectionContents[ sectionId ], 'afterShow' );
			// Item popup
			var itemPopup = curItem.find( '.usof-nav-popup' );
			if ( itemPopup.length > 0 ) {
				// Current usof_visited_new_sections cookie
				var matches = _document.cookie.match( /(?:^|; )usof_visited_new_sections=([^;]*)/ ),
					cookieValue = matches ? decodeURIComponent( matches[ 1 ] ) : '',
					visitedNewSections = ( cookieValue == '' ) ? [] : cookieValue.split( ',' );
				if ( visitedNewSections.indexOf( sectionId ) == - 1 ) {
					visitedNewSections.push( sectionId );
					_document.cookie = 'usof_visited_new_sections=' + visitedNewSections.join( ',' )
				}
				itemPopup.remove();
			}
			this.active = sectionId;
		},

		hideSection: function() {
			this.navItems.filter( '[data-id="' + this.active + '"]' ).removeClass( 'current' );
			this.fireFieldEvent( this.$sectionContents[ this.active ], 'beforeHide' );
			this.$sectionContents[ this.active ].stop( true, false ).hide();
			this.$title.html( '' );
			this.fireFieldEvent( this.$sectionContents[ this.active ], 'afterHide' );
			this.active = null;
		},

		/**
		 * Save the new values
		 */
		save: function() {
			if ( $.isEmptyObject( this.valuesChanged ) ) {
				return;
			}
			clearTimeout( this.saveStateTimer );
			this.$saveMessage.html( '' );
			this.$saveControl.usMod( 'status', 'loading' );

			$.ajax( {
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_save',
					usof_options: JSON.stringify( this.valuesChanged ),
					_wpnonce: this.$container.find( '[name="_wpnonce"]' ).val(),
					_wp_http_referer: this.$container.find( '[name="_wp_http_referer"]' ).val()
				},
				success: function( result ) {
					if ( result.success ) {
						this.valuesChanged = {};
						this.$saveMessage.html( result.data.message );
						this.$saveControl.usMod( 'status', 'success' );
						this.saveStateTimer = setTimeout( function() {
							this.$saveMessage.html( '' );
							this.$saveControl.usMod( 'status', 'clear' );
						}.bind( this ), 4000 );
					} else {
						this.$saveMessage.html( result.data.message );
						this.$saveControl.usMod( 'status', 'error' );
						this.saveStateTimer = setTimeout( function() {
							this.$saveMessage.html( '' );
							this.$saveControl.usMod( 'status', 'notsaved' );
						}.bind( this ), 4000 );
					}
				}.bind( this )
			} );
		}
	} );

	$( function() {
		new USOF( '.usof-container:not(.inited)' );

		$.each( $( '.usof-container.for_meta' ), function( index, item ) {
			new USOF_Meta( item );
		} );

		$( _document.body ).off( 'usof_mm_load' ).on( 'usof_mm_load', function() {
			$.each( $( '.us-mm-settings' ), function( index, item ) {
				new USOF_Meta( item );
			} );
		} );
	} );


}( jQuery );

/**
 * Returns a function, that, as long as it continues to be invoked, will not
 * be triggered. The function will be called after it stops being called for
 * N milliseconds. If `immediate` is passed, trigger the function on the
 * leading edge, instead of the trailing. The function also has a property 'clear'
 * that is a function which will clear the timer to prevent previously scheduled executions.
 *
 * @param {Function} function to wrap
 * @param {Number} timeout in ms (`100`)
 * @param {Boolean} whether to execute at the beginning (`false`)
 * @return {Function}
 */
 // TODO: Create a single helper file and get rid of `$us.debounce()`, `$usbcore.debounce()` and `usof_debounce()`
function usof_debounce( fn, wait, immediate ) {
	var timeout, args, context, timestamp, result;
	if ( null == wait ) wait = 100;
	function later() {
		var last = Date.now() - timestamp;
		if ( last < wait && last >= 0 ) {
			timeout = setTimeout( later, wait - last );
		} else {
			timeout = null;
			if ( ! immediate ) {
				result = fn.apply( context, args );
				context = args = null;
			}
		}
	}
	var debounced = function() {
		context = this;
		args = arguments;
		timestamp = Date.now();
		var callNow = immediate && ! timeout;
		if ( ! timeout ) timeout = setTimeout( later, wait );
		if ( callNow ) {
			result = fn.apply( context, args );
			context = args = null;
		}
		return result;
	};
	debounced.prototype = {
		clear: function() {
			if ( timeout ) {
				clearTimeout( timeout );
				timeout = null;
			}
		},
		flush: function() {
			if ( timeout ) {
				result = fn.apply( context, args );
				context = args = null;
				clearTimeout( timeout );
				timeout = null;
			}
		}
	};
	return debounced;
};
