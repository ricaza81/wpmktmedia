/**
 * Retrieve/set/erase dom modificator class <mod>_<value> for UpSolution CSS Framework
 * @param {String} mod Modificator namespace
 * @param {String} [value] Value
 * @returns {string|jQuery}
 */
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

	if ( window.$usof === undefined ) {
		window.$usof = {};
	}
	if ( $usof.mixins === undefined ) {
		$usof.mixins = {};
	}

	/**
	 * The generate unique identifier.
	 *
	 * @return {string}
	 */
	$usof.genUniqueId = function() {
		return Math.random().toString( 36 ).substr( 2, 9 );
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
	if ( $usof.ajaxQueues === undefined ) {
		$usof.ajaxQueues = $.noop;
		$.extend( $usof.ajaxQueues.prototype, $usof.mixins.Events, {
			$$events: {},
		});
		$usof.ajaxQueues = new $usof.ajaxQueues;
	}

	// Temporary global and isolated storage
	if ( $usof.cache === undefined ) {
		$usof.cache = $.noop;
		$.extend( $usof.cache.prototype, $usof.mixins.Events, {
			data: {},
			/**
			 * Set data
			 * @param {String} key
			 * @param {Mixed} data
			 * @return void
			 */
			set: function( key, data ) {
				this.trigger( 'set', [ key, data ] );
				this.data[ key ] = data;
			},
			/**
			 * Get data
			 * @param {String} key
			 * @return {Mixed}
			 */
			get: function( key ) {
				return this.has( key ) ? this.data[ key ] : null;
			},
			/**
			 * Has key and data
			 * @param {String} key
			 * @return {Boolean}
			 */
			has: function( key ) {
				return this.data[ key ] !== undefined && ! $.isEmptyObject( this.data[ key ] );
			},
			/**
			 * Removes the specified key.
			 * @param {String} key
			 * @return void
			 */
			remove: function( key ) {
				this.trigger( 'remove', key );
				delete this.data[ key ];
			},
			/**
			 * Flushes the object.
			 * return void
			 */
			flush: function() {
				this.trigger( 'flush' );
				this.data = {};
			},
		} );
		$usof.cache = new $usof.cache;
	}

	$usof.field = function( row, options ) {
		this.$row = $( row );
		this.type = this.$row.usMod( 'type' );
		this.name = this.$row.data( 'name' );
		this.id = this.$row.data( 'id' );
		this.$input = this.$row.find( '[name="' + this.name + '"]' );
		this.inited = this.$row.data( 'inited' ) || false;

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
		}.bind( this );
		this.on( 'beforeShow', initEvent );
	};
	$.extend( $usof.field.prototype, $usof.mixins.Events, {
		init: function() {
			if ( this._events === undefined ) {
				this._events = {};
			}
			this._events.change = function() {
				this.trigger( 'change', [this.getValue()] );
			}.bind( this );
			this.$input.on( 'change', this._events.change );
		},
		getValue: function() {
			return this.$input.val();
		},
		setValue: function( value, quiet ) {
			this.$input.val( value );
			if ( ! quiet ) {
				this.trigger( 'change', [value] );
			}
		}
	} );

	/**
	 * USOF Field: Backup
	 */
	$usof.field[ 'backup' ] = {

		init: function() {
			this.$backupStatus = this.$row.find( '.usof-backup-status' );
			this.$btnBackup = this.$row.find( '.usof-button.type_backup' ).on( 'click', this.backup.bind( this ) );
			this.$btnRestore = this.$row.find( '.usof-button.type_restore' ).on( 'click', this.restore.bind( this ) );

			// JS Translations
			var $i18n = this.$row.find( '.usof-backup-i18n' );
			this.i18n = {};
			if ( $i18n.length > 0 ) {
				this.i18n = $i18n[ 0 ].onclick() || {};
			}
		},

		backup: function() {
			this.$btnBackup.addClass( 'loading' );
			$.ajax( {
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_backup',
					_wpnonce: this.$row.closest( '.usof-form' ).find( '[name="_wpnonce"]' ).val(),
					_wp_http_referer: this.$row.closest( '.usof-form' ).find( '[name="_wp_http_referer"]' ).val()
				},
				success: function( result ) {
					this.$backupStatus.html( result.data.status );
					this.$btnBackup.removeClass( 'loading' );
					this.$btnRestore.show();
				}.bind( this )
			} );
		},

		restore: function() {
			if ( ! confirm( this.i18n.restore_confirm ) ) {
				return;
			}
			this.$btnRestore.addClass( 'loading' );
			$.ajax( {
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_restore_backup',
					_wpnonce: this.$row.closest( '.usof-form' ).find( '[name="_wpnonce"]' ).val(),
					_wp_http_referer: this.$row.closest( '.usof-form' ).find( '[name="_wp_http_referer"]' ).val()
				},
				success: function( result ) {
					this.$btnRestore.removeClass( 'loading' );
					alert( result.data.message );
					location.reload();
				}.bind( this )
			} );
		}

	};

	function onlyUnique( value, index, self ) {
		return self.indexOf( value ) === index;
	}

	/**
	 * USOF Field: Checkbox
	 */
	$usof.field[ 'checkboxes' ] = {

		getValue: function() {
			var value = [];
			$.each( this.$input, function() {
				if ( this.checked ) {
					value.push( this.value );
				}
			} );
			return value;
		},

		setValue: function( value, quiet ) {
			$.each( this.$input, function() {
				$( this ).attr( 'checked', ( $.inArray( this.value, value ) != - 1 ) ? 'checked' : false );
			} );
		}

	};

	$usof.field[ 'check_table' ] = {

		getValue: function() {
			var value = {};
			$.each( this.$input, function() {
				value[ this.value ] = ( this.checked ) ? 1 : 0;
			} );
			return value;
		},

		setValue: function( value, quiet ) {
			$.each( this.$input, function() {
				$( this ).attr( 'checked', ( value[ this.value ] === undefined || value[ this.value ] == 1 ) ? 'checked' : false );
			} );
		}

	};

	/**
	 * USOF Field: Color
	 */
	$usof.field[ 'color' ] = {

		init: function( options ) {
			// Elements
			this.$color = this.$row.find( '.usof-color' );
			this.$clear = $( '.usof-color-clear', this.$color );
			this.$list = $( '.usof-color-list', this.$color );
			this.$preview = $( '.usof-color-preview', this.$color );

			// Variables
			this.withGradient = !! this.$color.is( '.with-gradient' );
			this.isDynamicСolors = !! this.$color.is( '.dynamic_colors' );

			// Set white text color for dark backgrounds
			this._toggleInputColor( this.getColor() );

			// Init colpick on focus
			this.$input
				.off( 'focus' )
				.on( 'focus', this._events.initColpick.bind( this ) )
				.on( 'input', this._events.inputValue.bind( this ) )
				.on( 'change', this._events.changeValue.bind( this ) );

			this.$clear
				.on( 'click', this._events.inputClear.bind( this ) );

			// Init of a sheet of dynamic colors on click
			if ( this.isDynamicСolors ) {
				this.$color
					.on( 'click' , '.usof-color-arrow', this._events.toggleList.bind( this ) )
					.on( 'click', '.usof-color-list-item', this._events.selectedListItem.bind( this ) );
			}

			// If the sheet is open and there was a click outside the sheet, then close the sheet
			$( document )
				.mouseup( this._events.hideList.bind( this ) );
		},
		_events: {
			/**
			 * Init colpick
			 * @return void
			 */
			initColpick: function () {
				this.$input
					.usof_colpick( {
						input: this.$input,
						value: this.getColor(),
						onChange: function( colors ) {
							this._invertInputColors( colors.color.first.rgba );
						}.bind( this ),
					} );
			},
			/**
			 * Init of a sheet of dynamic variables
			 * @param void
			 */
			toggleList: function( e ) {
				if ( ! this.$color.is( '[data-loaded]' ) ) {
					this._ajax.call( this, e.currentTarget, function() {
						this.$color.toggleClass( 'show' );
					} );
				} else {
					this.$color.toggleClass( 'show' );
				}
			},
			/**
			 * Selected list item
			 * @param {EventObject} e
			 * @return void
			 */
			selectedListItem: function ( e ) {
				var $target = $( e.currentTarget ),
					value = $target.data( 'value' ) || '';

				this.$list
					.find( '[data-name]' )
					.removeClass( 'selected' );
				$target
					.addClass( 'selected' );
				this.$preview
					.css( 'background', value );
				this.$input
					.val( $target.data( 'name' ) || '' );

				this.trigger( 'change', this.$input.val() );

				// Set white text color for dark backgrounds
				this._toggleInputColor( value );

				this.$color
					.removeClass( 'show' );
			},
			/**
			 * Hides the list.
			 * @param {EventObject} e
			 * @return void
			 */
			hideList: function( e ) {
				if ( ! this.$color.is( '.show' ) ) {
					return;
				}
				if ( ! this.$color.is( e.target ) && this.$color.has( e.target ).length === 0 ) {
					this.$color.removeClass( 'show' );
				}
			},
			/**
			 * Input value
			 * @return value
			 */
			inputValue: function() {
				var value = this.getValue();
				// Preloading the list of variables if the value contains brackets
				if ( value.indexOf( '_' ) !== -1 ) {
					this._ajax( $( '.usof-color-arrow:first', this.$color ) );
				}
			},
			/**
			 * Changed value
			 * @return void
			 */
			changeValue: function() {
				var value = this.getValue();
				// Check the value for dynamic variables
				if ( value.indexOf( '_' ) !== -1 ) {
					$( '[data-name^="'+ value +'"]:first', this.$list )
						.trigger( 'click' );
				} else {
					this.setValue( value );
					this.trigger( 'change', value );
				}
			},
			/**
			 * Clear value
			 * @return void
			 */
			inputClear: function () {
				if ( this.$color.hasClass( 'show' ) ) {
					this.$color.removeClass( 'show' );
				}
				this.setValue( '' );
			}
		},
		/**
		 * AJAX Load data
		 * @param {jQueryElement} $loadedEl
		 * @param {Function} callback
		 * @return void
		 */
		_ajax: function( $loadedEl, callback ) {
			if ( ! this.$color.is( '[data-loaded]' ) ) {
				var $loadedEl = $( $loadedEl ),
					data = {
						action: this.$color.data( 'action' ),
						_nonce: this.$color.data( 'nonce' )
					},
					/**
					 * Add item to list
					 *
					 * @param jQueryElement $el
					 * @param object item
					 * @return void
					 */
					insertItem = function( $el, item ) {
						// Exclude yourself
						if ( this.name === item.name ) {
							return;
						}
						var $item = $( '<div></div>' ),
							$palette = $( '<div class="usof-colpick-palette-value"><span></span></div>' ),
							value = this.getValue();
						$palette
							.find( 'span' )
							.css( 'background', item.value )
							.attr( 'title', item.value )
						$item
							.addClass( 'usof-color-list-item' )
							.attr( 'data-name', item.name )
							.data( 'value', item.value )
							.append( $palette)
							.append( '<span class="usof-color-list-item-name">'+ item.title +'</span>' );
						if ( value.indexOf( '_' ) !== -1 && item.name === value) {
							$item.addClass( 'selected' );
						}
						$el.append( $item );
					},
					/**
					 * $param {Object} _
					 * @param {Object} res
					 * @return void
					 */
					_ajaxSuccess = function( _, res ) {
						$loadedEl
							.removeClass( 'loaded' );
						this.$color
							.attr( 'data-loaded','true' );
						if ( ! res.success ) {
							// TODO: Show error message
							console.error( res.data.message );
							return;
						}
						$.each( res.data.list || [], function( key, item ) {
							// Group options
							if ( $.isArray( item ) && item.length ) {
								$group = $( '> [data-group="'+ key +'"]:first', this.$list );
								if ( ! $group.length ) {
									$group = $( '<div class="usof-color-list-group" data-group="'+ key +'"></div>' );
									this.$list.append( $group );
								}
								$.each( item, function( _, _item ) {
									insertItem.call( this, $group, _item );
								}.bind( this ) );
								// Options
							} else {
								insertItem.call( this, this.$list, item );
							}
						}.bind( this ) );

						if ( $.isFunction( callback ) ) {
							callback.call( this, res.data );
						}
					}.bind( this );

				$loadedEl
					.addClass( 'loaded' );
				// Clear list
				this.$list
					.html();

				// If the cache is not empty then we will get data from there
				if ( $usof.cache.has( 'field.colors' ) ) {
					_ajaxSuccess.call( this, null, $usof.cache.get( 'field.colors' ) );
					return;
				}

				var hasQueues = $usof.ajaxQueues.has( 'field.colors' );
				$usof.ajaxQueues
					.on( 'field.colors', _ajaxSuccess.bind( this ) );
				if ( hasQueues ) {
					return;
				}

				// AJAX loading dynamic colors
				$.post( ajaxurl, data, function( res )  {
					$usof.cache.set( 'field.colors', res );
					$usof.ajaxQueues
						.trigger( 'field.colors', res )
						.off( 'field.colors' );
				}, 'json' );
			}
		},
		/**
		 * Set the value.
		 * @param {string} value
		 * @param {boolean} quiet
		 * @return void
		 */
		setValue: function( value, quiet ) {
			value = value.trim();

			// Check the value for dynamic variables
			if ( value.indexOf( '_' ) !== -1 ) {
				var selectedItem = function( value ) {
					$( '[data-name^="'+ value +'"]:first', this.$list )
						.trigger( 'click' );
				}.bind( this );
				if ( ! this.$color.is( '[data-loaded]' ) ) {
					this._ajax( $( '.usof-color-arrow:first', this.$color ), function() {
						selectedItem( value );
					} );
				} else {
					selectedItem( value );
				}
				return;
			}

			var r, g, b, a, hexR, hexG, hexB, gradient, rgba = {};

			this.convertRgbToHex = function( color ) {
				if ( m = /^([^0-9]{1,3})*(\d{1,3})[^,]*,([^0-9]{1,3})*(\d{1,3})[^,]*,([^0-9]{1,3})*(\d{1,3})[\s\S]*$/.exec( color ) ) {
					rgba = {
						r: m[ 2 ],
						g: m[ 4 ],
						b: m[ 6 ],
					};
					hexR = m[ 2 ] <= 255 ? ( "0" + parseInt( m[ 2 ], 10 ).toString( 16 ) ).slice( - 2 ) : 'ff';
					hexG = m[ 4 ] <= 255 ? ( "0" + parseInt( m[ 4 ], 10 ).toString( 16 ) ).slice( - 2 ) : 'ff';
					hexB = m[ 6 ] <= 255 ? ( "0" + parseInt( m[ 6 ], 10 ).toString( 16 ) ).slice( - 2 ) : 'ff';
					color = '#' + hexR + hexG + hexB;
					return color;
				}
			};

			if ( $.usof_colpick.isGradient( value ) ) {
				gradient = $.usof_colpick.gradientParser( value );
				rgba = $.usof_colpick.hexToRgba( gradient.hex );
			} else if ( ( m = /^[^,]*,[^,]*,[\s\S]*$/.exec( value ) ) ) {
				// Catch RGB and RGBa
				if ( m = /^[^,]*(,)[^,]*(,)[^,]*(,)[^.]*(\.|0)[\s\S]*$/.exec( value ) ) {
					// Catch only RGBa values
					if ( m[ 4 ] === '.' || m[ 4 ] == 0 ) {
						if ( m = /^([^0-9]{1,3})*(\d{1,3})[^,]*,([^0-9]{1,3})*(\d{1,3})[^,]*,([^0-9]{1,3})*(\d{1,3})[^,]*,[^.]*.([^0-9]{1,2})*(\d{1,2})[\s\S]*$/.exec( value ) ) {
							rgba = {
								r: m[ 2 ],
								g: m[ 4 ],
								b: m[ 6 ],
							};
							r = m[ 2 ] <= 255 ? m[ 2 ] : 255;
							g = m[ 4 ] <= 255 ? m[ 4 ] : 255;
							b = m[ 6 ] <= 255 ? m[ 6 ] : 255;
							a = m[ 8 ];
							value = 'rgba(' + r + ',' + g + ',' + b + ',0.' + a + ')';
						}
					} else {
						value = this.convertRgbToHex( value );
					}
				} else {
					value = this.convertRgbToHex( value );
				}
			} else {
				// Check Hex Colors
				if ( m = /^\#?[\s\S]*?([a-fA-F0-9]{1,6})[\s\S]*$/.exec( value ) ) {
					if ( value == 'inherit' || value == 'transparent' || $.usof_colpick.colorNameToHex( value ) ) {
						value = value;
					} else {
						value = $.usof_colpick.normalizeHex( m[ 1 ] );
						rgba = $.usof_colpick.hexToRgba( value );
					}
				}
			}

			if ( value == '' ) {
				this.$preview.removeAttr( 'style' );
				this.$input.removeClass( 'with_alpha' );
			} else {
				if ( value == 'inherit' || value == 'transparent' ) {
					this.$input.removeClass( 'white' );
					this.$preview.css( 'background', value );
				} else if ( gradient ) {
					if ( this.withGradient ) {
						this.$preview.css( 'background', gradient.gradient );
						this.$input.val( gradient.gradient );
					} else {
						// Don't allow to use gradient colors
						value = gradient.hex;
						this.$preview.css( 'background', value );
						this.$input.val( value );
					}
				} else {
					this.$preview.css( 'background', value );
					this.$input.val( value );
				}
			}

			if ( value == '' || value == 'inherit' || value == 'transparent' ) {
				this.$input.removeClass( 'white' );
			} else {
				this._invertInputColors( rgba );
			}

			this.parentSetValue( value, quiet );
		},
		/**
		 * Get the value.
		 * @return string
		 */
		getValue: function() {
			return $.trim( this.$input.val() ) || '';
		},
		/**
		 * Get color, variables will be replaced with value
		 * @return {string}
		 */
		getColor: function() {
			var value = this.getValue();
			if ( value.indexOf( '_' ) !== -1 ) {
				var itemValue = $( '[data-name="'+ value +'"]:first', this.$list ).data( 'value' ) || '';
				value = itemValue || this.$color.data( 'value' ) || value;
			}
			return $.trim( value );
		},
		/**
		 * Set white text color for dark backgrounds
		 *
		 * @param {string} value
		 */
		_toggleInputColor: function( value ) {
			if ( ! value ) {
				this.$input.removeClass( 'white' );
				return;
			}
			// If the HEX value is 3-digit, then convert it to 6-digit
			if ( value.slice( 0, 1 ) === '#' && value.length === 4 ) {
				value = value.replace( /^#([\dA-f])([\dA-f])([\dA-f])$/, "#$1$1$2$2$3$3" )
			}
			if (
				value !== 'inherit'
				&& value !== 'transparent'
				&& value.indexOf( 'linear-gradient' ) === - 1
			) {
				if ( $.usof_colpick.colorNameToHex( value ) ) {
					this._invertInputColors( $.usof_colpick.hexToRgba( $.usof_colpick.colorNameToHex( value ) ) );
				} else {
					this._invertInputColors( $.usof_colpick.hexToRgba( value ) );
				}
			} else if ( value.indexOf( 'linear-gradient' ) !== - 1 ) {
				var gradient = $.usof_colpick.gradientParser( value );
				// Make sure the gradient was parsed
				if ( gradient != false ) {
					this._invertInputColors( $.usof_colpick.hexToRgba( gradient.hex ) );
				}
			}
		},
		_invertInputColors: function( rgba ) {
			if ( ! rgba && ( typeof rgba != 'object' ) ) {
				return;
			}
			var r = rgba.r ? rgba.r : 0,
				g = rgba.g ? rgba.g : 0,
				b = rgba.b ? rgba.b : 0,
				a = ( rgba.a === 0 || rgba.a ) ? rgba.a : 1,
				light;
			// Determine lightness of color
			light = r * 0.213 + g * 0.715 + b * 0.072;
			// Increase lightness regarding color opacity
			if ( a < 1 ) {
				light = light + ( 1 - a ) * ( 1 - light / 255 ) * 235;
			}
			if ( light < 178 ) {
				this.$input.addClass( 'white' );
			} else {
				this.$input.removeClass( 'white' );
			}
		}
	};

	/**
	 * USOF Field: Css / Html
	 */
	$usof.field[ 'css' ] = $usof.field[ 'html' ] = {

		init: function() {
			// Variables
			this._params = {};
			this.editor = null;
			this.editorDoc = null;
			// Handlers
			this._events = {
				/**
				 * Editor change
				 *
				 * @param object doc CodeMirror
				 * @return void
				 */
				editorChange: function( doc ) {
					this.parentSetValue( this.getValue() );
				}
			};

			// Init CodeEditor
			if ( wp.hasOwnProperty( 'codeEditor' ) ) {
				this._params = this.$row.find( '.usof-form-row-control-params' )[ 0 ].onclick() || {};
				this.$row.find( '.usof-form-row-control-params' ).removeAttr( 'onclick' );
				if ( this._params.editor !== false ) {
					this.editor = wp.codeEditor.initialize( this.$input[ 0 ], this._params.editor || {} );
					this.editorDoc = this.editor.codemirror.getDoc();
					this.setValue( this.$input.val() );
				}
			} else {
				this.$input.on( 'keyup', function() {
					this.parentSetValue( this.getValue() );
					this.setValue( this.$input.val() );
				}.bind( this ) );
			}
		},
		setValue: function( value ) {
			if ( !! this._params && this._params.hasOwnProperty( 'encoded' ) && this._params.encoded ) {
				value = usof_rawurldecode( usof_base64_decode( value ) );
			}
			if ( this.editor !== undefined && wp.hasOwnProperty( 'codeEditor' ) ) {
				this.editorDoc.off( 'change', this._events.editorChange.bind( this ) );
				if ( !! this.pid ) {
					clearTimeout( this.pid );
				}
				this.pid = setTimeout( function() {
					this.editorDoc.cm.refresh();
					this.editorDoc.setValue( value );
					this.editorDoc.on( 'change', this._events.editorChange.bind( this ) );
					clearTimeout( this.pid );
				}.bind( this ), 1 );
			}
		},
		getValue: function() {
			var value = this.editor !== undefined && wp.hasOwnProperty( 'codeEditor' )
				? this.editorDoc.getValue()
				: this.$input.val();
			if ( this._params !== undefined && this._params.encoded ) {
				value = usof_base64_encode( usof_rawurlencode( value ) );
			}
			return value;
		}
	};

	/**
	 * USOF Field: Select
	 */
	$usof.field[ 'select' ] = {
		init: function( options ) {
			this.parentInit( options );
			this.$select = this.$row.find( 'select' );
			this.$hint = this.$row.find( '.usof-form-row-hint-text' );
			this.$hintsJson = this.$row.find( '.usof-form-row-hint-json' );
			this.hintsJson = {};

			if ( this.$hintsJson.length ) {
				this.hintsJson = this.$hintsJson[ 0 ].onclick() || {};
				this.$hintsJson.remove();
			}

			this.$select.on( 'change', function() {
				this.$row.attr( 'selected-value', this.$select.val() );

				if ( ! this.hintsJson.no_posts ) {
					if ( this.$select.val().length && this.$select.val().match( /\d+/ ) ) {
						var hint = '';
						if ( this.hintsJson.hasOwnProperty( 'edit_url' ) ) {
							var regex = /(<a [^{]+)({{post_id}})([^{]+)({{hint}})([^>]+>)/;
							hint = this.hintsJson.edit_url.replace( regex, '$1' + this.$select.val() + '$3' + this.hintsJson.edit + '$5' );
						}
						this.$hint.html( hint );
					} else {
						this.$hint.html( '' );
					}
				}
			}.bind( this ) );
			this.$row.attr( 'selected-value', this.$select.val() );
		}
	};

	/**
	 * USOF Field: Font
	 */
	$usof.field[ 'font' ] = {
		init: function( options ) {
			this.parentInit( options );
			// Elements
			this.$preview = this.$row.find( '.usof-font-preview' );
			this.$weightsContainer = this.$row.find( '.usof-checkbox-list' );
			this.$weightCheckboxes = this.$weightsContainer.find( '.usof-checkbox' );
			this.$weights = this.$weightsContainer.find( 'input' );
			// Variables
			this.fontStyleFields = this.$row.find( '.usof-font-style-fields-json' )[ 0 ].onclick() || {};
			this.notLoadedFonts = [];
			this.fontInited = false;
			this.$fieldFontName = {};

			// Init font autocomplete
			var $autocomplete = $( '.type_autocomplete', this.$row );
			this.fontsGroupKeys = $autocomplete[ 0 ].onclick() || {};
			$autocomplete.removeAttr( 'onclick' );

			if ( $autocomplete.length ) {
				this.$fieldFontName = new $usof.field( $autocomplete );
				this.$fieldFontName.trigger( 'beforeShow' );
			}

			this.curFont = this.$fieldFontName.getValue();
			this.isCurFontUploaded = this.fontHasGroup.call( this, this.curFont, 'uploaded' );

			if ( ! $usof.loadingFonts ) {
				$usof.loadingFonts = true;
				$.ajax( {
					type: 'POST',
					url: $usof.ajaxUrl,
					dataType: 'json',
					data: {
						action: 'usof_get_google_fonts',
						_wpnonce: this.$row.closest( '.usof-form' ).find( '[name="_wpnonce"]' ).val(),
						_wp_http_referer: this.$row.closest( '.usof-form' ).find( '[name="_wp_http_referer"]' ).val()
					},
					success: function( result ) {
						$usof.googleLoaded = true;
						$usof.googleFonts = result.data.google_fonts || {};
					},
					error: function() {
						$usof.googleLoaded = true;
						$usof.googleFonts = {};
					}
				} );
			}

			var that = this,
				fontsTimeoutId = setTimeout( function fontsTimeout() {
					if ( $usof.googleLoaded ) {
						that.fonts = $usof.googleFonts;
						that._init();
						clearTimeout( fontsTimeoutId );
					} else {
						fontsTimeoutId = setTimeout( fontsTimeout, 500 );
					}
				}, 500 );
		},
		_init: function() {
			/**
			 * Initializes not loaded fonts.
			 */
			var initNotLoadedFonts = function() {
				['websafe', 'uploaded'].map( function( groupName ) {
					if ( this.fontsGroupKeys.hasOwnProperty( groupName ) ) {
						$( '[data-group="' + this.fontsGroupKeys[ groupName ] + '"] > *', this.fontsGroupKeys.$list )
							.each( function( _, item ) {
								var value = $( item ).data( 'value' ) || '';
								if ( value && $.inArray( value, this.notLoadedFonts || [] ) === -1 ) {
									this.notLoadedFonts.push( value );
								}
							}.bind( this ) );
					}
				}.bind( this ) );
			};
			initNotLoadedFonts.call( this );

			if (
				this.curFont
				&& $.inArray( this.curFont, [ 'get_h1', 'none' ] ) === -1
				&& $.inArray( this.curFont, this.notLoadedFonts || [] ) === -1
			) {
				$( 'head' ).append( '<link href="//fonts.googleapis.com/css?family=' + this.curFont.replace( /\s+/g, '+' ) + '" rel="stylesheet" class="usof_font_' + this.id + '" />' );
				this.$preview.css( 'font-family', this.curFont + '' );
			} else if ( this.curFont != 'none' && $.inArray( this.curFont, this.notLoadedFonts || [] ) !== -1 ) {
				this.$preview.css( 'font-family', this.curFont + '' );
			}

			this.$fieldFontName
				.on( 'change', function() {
					this.isCurFontUploaded = this.fontHasGroup.call( this, this.$fieldFontName.getValue(), 'uploaded' );
					this.setValue( this._getValue.call( this ) );
				}.bind( this ) )
				.on( 'data.loaded', function() {
					initNotLoadedFonts.call( this );
				}.bind( this ) );

			this.$weights.on( 'change', function() {
				this.setValue( this._getValue.call( this ) );
			}.bind( this ) );

			if ( this.fontStyleFields.colorField != undefined ) {
				$usof.instance.fields[ this.fontStyleFields.colorField ].on( 'change', function() {
					this.$preview.css( 'color', $usof.instance.fields[ this.fontStyleFields.colorField ].getValue() );
				}.bind( this ) );
			}
			if ( this.fontStyleFields.sizeField != undefined ) {
				$usof.instance.fields[ this.fontStyleFields.sizeField ].on( 'change', function() {
					this.$preview.css( 'font-size', $usof.instance.fields[ this.fontStyleFields.sizeField ].getValue() );
				}.bind( this ) );
			}
			if ( this.fontStyleFields.lineheightField != undefined ) {
				$usof.instance.fields[ this.fontStyleFields.lineheightField ].on( 'change', function() {
					this.$preview.css( 'line-height', $usof.instance.fields[ this.fontStyleFields.lineheightField ].getValue() );
				}.bind( this ) );
			}
			if ( this.fontStyleFields.weightField != undefined ) {
				$usof.instance.fields[ this.fontStyleFields.weightField ].on( 'change', function() {
					this.$preview.css( 'font-weight', $usof.instance.fields[ this.fontStyleFields.weightField ].getValue() );
				}.bind( this ) );
			}
			if ( this.fontStyleFields.letterspacingField != undefined ) {
				$usof.instance.fields[ this.fontStyleFields.letterspacingField ].on( 'change', function() {
					this.$preview.css( 'letter-spacing', $usof.instance.fields[ this.fontStyleFields.letterspacingField ].getValue() );
				}.bind( this ) );
			}
			if ( this.fontStyleFields.transformField != undefined ) {
				$usof.instance.fields[ this.fontStyleFields.transformField ].on( 'change', function() {
					if ( $usof.instance.fields[ this.fontStyleFields.transformField ].getValue().indexOf( "uppercase" ) != - 1 ) {
						this.$preview.css( 'text-transform', 'uppercase' );
					} else {
						this.$preview.css( 'text-transform', '' );
					}
					if ( $usof.instance.fields[ this.fontStyleFields.transformField ].getValue().indexOf( "italic" ) != - 1 ) {
						this.$preview.css( 'font-style', 'italic' );
					} else {
						this.$preview.css( 'font-style', '' );
					}
				}.bind( this ) );
			}

			this.setValue( this._getValue(), true );
			this.fontInited = true;
		},
		/**
		 * Check whether the font belongs to the specified group
		 *
		 * @param string fontName
		 * @param string groupKey
		 * @return boolean
		 */
		fontHasGroup: function( fontName, groupKey ) {
			var $item = this.$fieldFontName.$list.find( '[data-value="' + fontName + '"]' ),
				$group = $item.closest( '.usof-autocomplete-list-group' );
			if ( $group.length && this.fontsGroupKeys.hasOwnProperty( groupKey ) ) {
				return $group.is( '[data-group="' + this.fontsGroupKeys[ groupKey ] + '"]' );
			}
			return false;
		},
		setValue: function( value, quiet ) {
			var h1_value, parts, fontName, fontWeights;
			// TODO: make this value-independent
			if ( value === 'get_h1|' ) {
				h1_value = $usof.instance.getValue( 'h1_font_family' );
				parts = h1_value.split( '|' );
			} else {
				parts = value.split( '|' );
			}

			fontName = parts[ 0 ] || 'none';
			fontWeights = parts[ 1 ] || '400,700';
			fontWeights = fontWeights.split( ',' );
			if ( fontName != this.curFont ) {
				$( '.usof_font_' + this.id ).remove();
				if ( fontName == 'none' ) {
					// Selected no-font
					this.$preview.css( 'font-family', '' );
				} else if ( $.inArray( fontName, this.notLoadedFonts || [] ) !== -1 ) {
					// Web-safe font combination and uploaded fonts
					this.$preview.css( 'font-family', fontName );
				} else {
					// Selected some google font: show preview
					if ( this.curFont !== 'get_h1' ) {
						$( 'head' )
							.append( '<link href="//fonts.googleapis.com/css?family=' + fontName.replace( /\s+/g, '+' ) + '" rel="stylesheet" class="usof_font_' + this.id + '" />' );
					}
					this.$preview.css( 'font-family', fontName + ', sans-serif' );
				}
				// setValue may be called both from inside and outside, so checking to avoid recursion
				if ( value === 'get_h1|' ) {
					if ( this.$fieldFontName.getValue() !== 'get_h1' ) {
						this.$fieldFontName.setValue( 'get_h1' );
					}
				} else if ( this.$fieldFontName.getValue() !== fontName ) {
					this.$fieldFontName.setValue( fontName );
				}
				this.curFont = fontName;
			}
			if ( this.fontStyleFields.weightField == undefined ) {
				if ( fontWeights.length == 0 ) {
					this.$preview.css( 'font-weight', '' );
				} else {
					this.$preview.css( 'font-weight', parseInt( fontWeights[ 0 ] ) );
				}
			}
			// Show the available weights
			if ( value === 'get_h1|' || this.fonts[ fontName ] === undefined || this.isCurFontUploaded ) {
				this.$weightCheckboxes.addClass( 'hidden' );
			} else {
				this.$weightCheckboxes.each( function( index, elm ) {
					var $elm = $( elm ),
						weightValue = $elm.data( 'value' ) + '';
					$elm.toggleClass( 'hidden', $.inArray( weightValue, this.fonts[ fontName ].variants ) == - 1 );
					$elm.attr( 'checked', ( $.inArray( weightValue, fontWeights ) == - 1 ) ? 'checked' : false );
				}.bind( this ) );
			}
			this.parentSetValue( value, quiet );

			// TODO: make this value-independent
			if ( this.name === 'h1_font_family' ) {
				for ( var i = 2; i <= 6; i ++ ) {
					var fontFieldId = 'h' + i + '_font_family',
						fontField;
					if ( $usof.instance.fields.hasOwnProperty( fontFieldId ) && $usof.instance.fields[ fontFieldId ].fontInited ) {
						fontField = $usof.instance.fields[ fontFieldId ];
						fontField.setValue( fontField._getValue() );
					}
				}
			}
		},
		_getValue: function() {
			var fontName = this.$fieldFontName.getValue(),
				fontWeights = [];
			if ( this.fonts[ fontName ] !== undefined && this.fonts[ fontName ].variants !== undefined ) {
				this.$weights.filter( ':checked' ).each( function( index, elm ) {
					var weightValue = $( elm ).val() + '';
					if ( $.inArray( weightValue, this.fonts[ fontName ].variants ) != - 1 ) {
						fontWeights.push( weightValue );
					}
				}.bind( this ) );
			}
			return fontName + '|' + fontWeights.join( ',' );
		}

	};

	/**
	 * USOF FIeld: Autocomplete
	 */
	$usof.field[ 'autocomplete' ] = {
		/**
		 * Initializes the object.
		 */
		init: function() {
			// Variables
			this.disableScrollLoad = false;
			// Prefix for get params
			this._prefix = 'params:';
			// Delay for search requests
			this._typingDelay = 0.5;
			// Event KeyCodes
			this.keyCodes = {
				ENTER: 13,
				BACKSPACE: 8
			};

			// Default settings structure
			var defaultSettings = {
				ajax_query_args: {
					action: 'unknown',
					_nonce: ''
				},
				multiple: false,
				sortable: false,
				params_separator: ','
			};

			// Elements
			this.$container = $( '.usof-autocomplete', this.$row );
			this.$toggle = $( '.usof-autocomplete-toggle', this.$container );
			this.$options = $( '.usof-autocomplete-options', this.$container );
			this.$search = $( 'input[type="text"]', this.$options );
			this.$list = $( '.usof-autocomplete-list', this.$container );
			this.$message = $( '.usof-autocomplete-message', this.$container );
			this.$value = $( '> .usof-autocomplete-value', this.$container );

			// Load settings
			this._settings = $.extend( defaultSettings, this.$container[0].onclick() || {} );
			//this.$container.removeAttr( 'onclick' );

			// List of all parameters
			this.items = {};
			$( '[data-value]', this.$list ).each( function( _, item ) {
				var $item = $( item );
				this.items[ $item.data( 'value' ) ] = $item;
			}.bind( this ) );

			// Events
			if ( !this._settings.multiple ) {
				this.$options
					.on( 'click', '.usof-autocomplete-selected', function() {
						var isShow = this.$toggle.hasClass( 'show' );
						this._events.toggleList.call( this, {
							type: isShow ? 'blur' : 'focus'
						} );
						if ( ! isShow ) {
							if ( !! this.pid ) {
								clearTimeout( this.pid );
							}
							this.pid = setTimeout( function() {
								this.$search.focus();
								clearTimeout( this.pid );
							}.bind( this ), 0 );
						}
					}.bind( this ) );
			} else {
				// For multiple
				this.$options
					.on( 'click', '.usof-autocomplete-selected-remove', this._events.remove.bind( this ) );
			}
			this.$list.off()
				.on( 'mousedown', '[data-value]', this._events.selected.bind( this ) )
				.on( 'scroll', this._events.scroll.bind( this ) );
			this.$search.off()
				.on( 'keyup', this._events.keyup.bind( this ) )
				.on( 'input', this._events.searchDelay.bind( this ) )
				.on( 'focus blur', this._events.toggleList.bind( this ) );

			this._initValues.call( this );
			this.$container
				.toggleClass( 'multiple', this._settings.multiple );

			if ( this._settings.multiple && this._settings.sortable ) {
				// Init Drag and Drop plugin
				this.dragdrop = new $usof.dragDrop( this.$options, {
					itemSelector: '> .usof-autocomplete-selected'
				} )
				// Watch events
				this.dragdrop
					.on( 'dragend', this._events.dragdrop.dragend.bind( this ) );
			}
		},
		/**
		 * State loaded
		 */
		loaded: false,
		/**
		 * Handlers
		 */
		_events: {
			// Drag and Drop Handlers
			dragdrop: {
				/**
				 * Set the value in the desired order.
				 * @param object target $usof.dragDrop
				 * @param object e jQueryEvent
				 * @return void
				 */
				dragend: function( target, e ) {
					var value = [],
						items = $( '> .usof-autocomplete-selected', target.$container ).toArray() || [],
						field = $( target.$container ).closest( '.type_autocomplete' ).data( 'usofField' );
					for ( var k in items ) {
						if ( items[ k ].hasAttribute( 'data-key' ) ) {
							value.push( items[ k ].getAttribute( 'data-key' ) );
						}
					}
					value = value.length
						? value.join( field._settings.params_separator )
						: '';
					if ( field instanceof $usof.field ) {
						field.setValue( value );
					}
				}
			},
			/**
			 * Remove selected
			 * @param object e jQueryEvent
			 * @return void
			 */
			remove: function( e ) {
				e.preventDefault();
				var $target = $( e.currentTarget ),
					$selected = $target.closest( '.usof-autocomplete-selected' ),
					key = $selected.data( 'key' );
				this._removeValue.call( this, key );
				$( '[data-value="' + key + '"]', this.$list ).removeClass( 'selected' );
				$selected.remove();
			},
			/**
			 * Delayed search to avoid premature queries
			 * @param object e jQueryEvent
			 * @return void
			 */
			searchDelay: function( e ) {
				if ( ! e.currentTarget.value ) {
					return;
				}
				if ( !! this._typingTimer ) {
					clearTimeout( this._typingTimer );
				}
				this._typingTimer = setTimeout( function() {
					this._events.search.call( this, e );
					clearTimeout( this._typingTimer );
				}.bind( this ), 1000 * this._typingDelay );
			},
			/**
			 * Filtering results when entering characters in the search field
			 * @param object e jQueryEvent
			 * @return void
			 */
			search: function( e ) {
				var $input = $( e.currentTarget ),
					value = ( $.trim( $input.val() ).toLowerCase() ).replace( /\=|\"|\s/, '' ),
					$items = $( '[data-value]', this.$list ),
					$groups = $( '[data-group]', this.$list ),
					/**
					 * Filters parameters by search text
					 * @param jQueryObject $items
					 * @return void
					 */
					filter = function( $items ) {
						$items
							.addClass( 'hidden' )
							.filter( '[data-text^="'+ value +'"], [data-text*="'+ value +'"]' )
							.removeClass( 'hidden' );
						$groups.each(function() {
							var $group = $( this );
							$group.toggleClass( 'hidden', !$group.find('[data-value]:not(.hidden)').length );
						});
					};

				// Check value
				if ( !value || value.length < 1 ) {
					$items.removeClass( 'hidden' );
					return;
				}

				// Filter by search text
				filter.call( this, $items );

				// Enable scrolling data loading
				this.disableScrollLoad = false;

				// Search preload
				this._ajax.call( this, function( items ) {
					// Filter by search text
					filter.call( this, this.$list.find( '> *' ) );
					// Messages no results found
					if ( value && !$( '[data-value]:not(.hidden)', this.$list ).length ) {
						this._showMessage.call( this, this._settings.no_results_found );
					} else {
						this._clearMessage.call( this );
						this.$toggle.addClass( 'show' );
					}
				}.bind( this ) );
			},
			/**
			 * Selected option
			 * @param object e jQueryEvent
			 * @return void
			 */
			selected: function( e ) {
				var $target = $( e.currentTarget ),
					selectedValue = $target.data( 'value' ) || '';
				if ( $target.hasClass( 'selected' ) && this._settings.multiple ) {
					// Remove item
					this._removeValue.call( this, selectedValue );
					$( '[data-key="' + selectedValue + '"]', this.$options ).remove();
					$target.removeClass( 'selected' );
				} else if ( this._addValue.call( this, selectedValue ) ) {
					if ( ! this._settings.multiple ) {
						$( '.usof-autocomplete-selected', this.$options ).remove();
						$( '[data-value]', this.$list ).removeClass( 'selected' );
					}
					this.$toggle.removeClass( 'show' );
					$target.addClass( 'selected' );
					// Added item
					this.$search
						.val( '' )
						.before( this._getSelectedTemplate.call( this, selectedValue ) );
				}
			},
			/**
			 * When scrolling a sheet, load the parameters
			 * @param object e jQueryEvent
			 * @return void
			 */
			scroll: function( e ) {
				var $target = $( e.currentTarget );
				if (
					!this.disableScrollLoad
					&& !this.loaded
					&& ( $target.scrollTop() + $target.height()  ) >= e.currentTarget.scrollHeight -1
				) {
					this._ajax.call( this, function( items ) {
						if ( $.isEmptyObject( items ) ) {
							this.disableScrollLoad = true;
						}
					}.bind( this ) );
				}
			},
			/**
			 * Input event handler for Search
			 * @param object e jQueryEvent
			 * @return void
			 */
			keyup: function( e ) {
				if ( e.keyCode === this.keyCodes.ENTER ) {
					// If you press enter and there are matching elements, then selected option.
					var search = $.trim( this.$search.val() ),
						$selected = $( '[data-text="'+ search +'"]:visible:first', this.$list );
					if ( !$selected.length ) {
						$selected = $( '[data-value]:visible:first', this.$list );
					}
					if ( $selected.length ) {
						$selected.trigger( 'click' );
					}
				}
				if( e.keyCode === this.keyCodes.BACKSPACE ) {
					if ( !$.trim( this.$search.val() ) ) {
						this._clearMessage.call( this );
						this.$list.find('.hidden').removeClass('hidden');
						this.$toggle.addClass( 'show' );
					}
				}
			},
			/**
			 * Show/Hide list
			 * @param {object} e jQueryEvent
			 * @return void
			 */
			toggleList: function( e ) {
				var isFocus = ( e.type === 'focus' ),
					pid = setTimeout( function() {
						this.$toggle.toggleClass( 'show', isFocus );
						clearTimeout( pid );
					}.bind( this ), ( isFocus ? 0 : 200 /* The delay for the blur event is necessary for the selection script to work out */ ) );
				// If there is no search text, then all parameters are show
				if ( !$.trim( this.$search.val() ) ) {
					$( '[data-value].hidden', this.$list )
						.removeClass( 'hidden' );
				}
			}
		},
		/**
		 * Load and search option
		 * @param function callback
		 * @return void
		 */
		_ajax: function( callback ) {
			if ( this.loaded ) {
				return;
			}

			var query_args = this._settings.ajax_query_args;
			// If the handler is not installed, then cancel the request
			if ( ( !query_args.hasOwnProperty( 'action' ) || query_args.action == 'unknown' ) && $.isFunction( callback )) {
				return callback.call( this, {} );
			}

			// Request data
			var data = $.extend( query_args || {}, {
				offset: $( '[data-value]:visible', this.$list ).length,
				search: $.trim( this.$search.val() ),
			});

			// Checking the last offset, it cannot be repeated repeating say that all data is loaded
			if ( this._offset && this._offset === data.offset ) {
				return;
			}

			this.loaded = true;
			this.$container.addClass( 'loaded' );
			this._clearMessage.call( this );

			this._offset = data.offset;

			// If the value is then add 1 to take into account the zero element of the array
			if ( data.offset ) {
				data.offset += 1;
			}

			/**
			 * Add option to sheet
			 *
			 * @param object $el jQueryElement
			 * @param string name
			 * @param string value
			 * @return void
			 */
			var insertItem = function( $el, name, value ) {
				if ( !this.items.hasOwnProperty( value )  ) {
					var text = ( name || '' ).replace( /\s/, '' ).toLowerCase(),
						$item = $( '<div data-value="'+ usof_strip_tags( value ) +'" data-text="'+ usof_strip_tags( text ) +'" tabindex="3">'+ name +'</div>' );
					$el.append( $item );
					this.items[ value ] = $item;
				}
			};

			// Get data
			$.get( ajaxurl, data, function( res ) {
				this.loaded = false;
				this.$container.removeClass( 'loaded' );
				this._clearMessage.call( this );

				if ( !res.success ) {
					this._showMessage.call( this, res.data.message );
					return;
				}

				// Add to the list of new parameters
				$.each( res.data.items, function( value, name ) {
					if ( $.isPlainObject( name ) ) {
						$.each( name, function( _value, _name ) {
							var $groupList = this.$list.find( '[data-group="'+ value +'"]:first' );
							if ( !$groupList.length ) {
								$groupList = $( '<div class="usof-autocomplete-list-group" data-group="'+ value +'"></div>' );
								this.$list.append( $groupList );
							}
							insertItem.call( this, $groupList, _name, _value );
						}.bind( this ) );
					} else {
						insertItem.call( this, this.$list, name, value );
					}
				}.bind( this ) );

				// Run callback function
				if ( $.isFunction( callback ) ) {
					callback.call( this, res.data.items );
				}

				// We’ll run an event for watches the data update.
				this.trigger( 'data.loaded', res.data.items );
			}.bind( this ), 'json' );
		},
		/**
		 * Initializes the values.
		 * @return void
		 */
		_initValues: function() {
			// Parameters which are not in the list and need to be loaded
			var loadParams = [],
				initValues = ( this.$value.val() || '' ).split( this._settings.params_separator ) || [];

			// Remove selecteds
			$( '.usof-autocomplete-selected', this.$options ).remove();

			// Selection of parameters during initialization
			initValues.map( function( key ) {
				if ( !key ) {
					return;
				}
				var $item = $( '[data-value="' + key + '"]:first', this.$list )
					.addClass( 'selected' );
				if ( $item.length ) {
					this.$search
						.before( this._getSelectedTemplate.call( this, key ) );
				} else {
					loadParams.push( key );
				}
			}.bind( this ) );

			// Loading and selection of parameters which are not in the list but must be displayed
			if ( loadParams.length ) {
				this.$search.val( this._prefix + loadParams.join( this._settings.params_separator ) );
				this._ajax.call( this, function( items ) {
					// Reset previously selected parameters to guarantee the desired order.
					$( '[data-key]', this.$options ).remove();
					$( '.selected', this.$list ).removeClass( 'selected' );

					// Selecting parameters by an array of identifiers, this guarantees the desired order
					$( initValues ).each( function( _, key ) {
						if ( this.items.hasOwnProperty( key ) && this.items[ key ] instanceof $ ) {
							this.items[ key ]
								.addClass( 'selected' );
							this.$search
								.before( this._getSelectedTemplate.call( this, key ) );
						}
					}.bind( this ) );

				}.bind( this ) );
				this.$search.val( '' );
			}
		},
		/**
		 * Show the message.
		 * @param string text The message text
		 * @return void
		 */
		_showMessage: function( text ) {
			this.$list.addClass( 'hidden' );
			this.$message
				.text( text )
				.removeClass( 'hidden' );
		},
		/**
		 * Clear this message
		 * @return void
		 */
		_clearMessage: function() {
			this.$list.removeClass( 'hidden' );
			this.$message
				.addClass( 'hidden' )
				.text('');
		},
		/**
		 * Adding a parameter to the result
		 * @param string key The key
		 * @return boolean
		 */
		_addValue: function( key ) {
			var isNotEnabled = false,
				values = [],
				value = key;
			if ( this._settings.multiple ) {
				values = ( this.$value.val() || '' ).split( this._settings.params_separator );
				for ( var k in values ) {
					if ( values[ k ] === key ) {
						isNotEnabled = true;
						break;
					}
				}
				if ( !isNotEnabled ) {
					values.push( key );
					value = ( values || [] ).join( this._settings.params_separator ).replace(/^\,/, '');
				}
			}
			if ( !isNotEnabled ) {
				this.$value.val( value );
				this.trigger( 'change', [ value ] );
				return true;
			}
			return false;
		},
		/**
		 * Removing a parameter from the result
		 * @param string key The key
		 * @return void
		 */
		_removeValue: function( key ) {
			var values = ( this.$value.val() || '' ).toLowerCase().split( this._settings.params_separator ),
				index = values.indexOf( '' + key );
			if ( index !== - 1 ) {
				delete values[ index ];
				// Reset indexes
				values = values.filter( function( item ) {
					return item !== undefined;
				} );
				this.$value.val( values.join( this._settings.params_separator ) );
			}
			this.trigger( 'change', [ this.getValue() ] );
		},
		/**
		 * Get the selected template.
		 * @param string key The key
		 * @return string
		 */
		_getSelectedTemplate: function( key ) {
			var $selected = $( '[data-value="' + key + '"]:first', this.$list );
			if ( !$selected.length ) {
				return '';
			}
			return '<span class="usof-autocomplete-selected" data-key="' + key + '">\
				' + $selected.html() + ' <a href="javascript:void(0)" title="Remove" class="usof-autocomplete-selected-remove fas fa-trash-alt"></a>\
			</span>';
		},
		/**
		 * Get value
		 * @return string
		 */
		getValue: function() {
			return this.$value.val();
		},
		/**
		 * Set values
		 * @param string value The value
		 * @param boolean quiet
		 * @return void
		 */
		setValue: function( value, quiet ) {
			this.$value.val( value );
			this._initValues.call( this );
			if ( !quiet ) {
				this.trigger( 'change', [value] );
			}
		}
	};

	/**
	 * USOF Field: Imgradio / Radio
	 */
	$usof.field[ 'imgradio' ] = $usof.field[ 'radio' ] = {
		getValue: function() {
			return this.$input.filter( ':checked' ).val();
		},
		setValue: function( value, quiet ) {
			if ( quiet ) {
				this.$input.off( 'change', this._events.change );
			}
			this.$input
				.removeAttr( 'checked' )
				.filter( '[value="' + value + '"]' )
				.prop( 'checked', true );
			if ( quiet ) {
				this.$input.on( 'change', this._events.change );
			}
		}
	};

	/**
	 * USOF Field: Link
	 */
	$usof.field[ 'link' ] = {

		init: function( options ) {
			this.parentInit( options );
			this.$mainField = this.$row.find( 'input[type="hidden"]:first' );
			this.$url = this.$row.find( 'input[type="text"]:first' );
			this.$target = this.$row.find( 'input[type="checkbox"]:first' );
			this.$row.on( 'click', '.usof-example', this.exampleClick.bind( this ) );

			this.$url.on( 'change', function() {
				this.$mainField.val( JSON.stringify( this.getValue() ) );
				this.trigger( 'change', this.getValue() );
			}.bind( this ) );

			this.$target.on( 'change', function() {
				this.$mainField.val( JSON.stringify( this.getValue() ) );
				this.trigger( 'change', this.getValue() );
			}.bind( this ) );
		},

		exampleClick: function( ev ) {
			var $target = $( ev.target ).closest( '.usof-example' ),
				example = $target.html();
			this.$url.val( example );
		},

		getValue: function() {
			if ( ! this.inited ) {
				return {};
			}
			return {
				url: this.$url.val(),
				target: this.$target.is( ':checked' ) ? '_blank' : ''
			};
		},

		setValue: function( value, quiet ) {
			if ( ! this.inited ) {
				return;
			}
			if ( typeof value != 'object' || value.url === undefined ) {
				value = {
					url: ( typeof value == 'string' ) ? value : ''
				}
			}
			this.$url.val( value.url );
			this.$target.attr( 'checked', ( value.target == '_blank' ) ? 'checked' : false );
		},

	};

	/**
	 * USOF Field: Reset
	 */
	$usof.field[ 'reset' ] = {

		init: function() {
			this.$btnReset = this.$row.find( '.usof-button.type_reset' ).on( 'click', this.reset.bind( this ) );
			this.resetStateTimer = null;
			this.i18n = ( this.$row.find( '.usof-form-row-control-i18n' )[ 0 ].onclick() || {} );
		},

		reset: function() {
			if ( ! confirm( this.i18n.reset_confirm ) ) {
				return;
			}
			clearTimeout( this.resetStateTimer );
			this.$btnReset.addClass( 'loading' );
			$.ajax( {
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_reset',
					_wpnonce: $usof.instance.$container.find( '[name="_wpnonce"]' ).val(),
					_wp_http_referer: $usof.instance.$container.find( '[name="_wp_http_referer"]' ).val()
				},
				success: function( result ) {
					this.$btnReset.removeClass( 'loading' );
					alert( this.i18n.reset_complete );
					location.reload();
				}.bind( this )
			} );
		}

	};

	/**
	 * USOF Field: Icon
	 */
	$usof.field[ 'icon' ] = {

		init: function( options ) {
			this.$value = this.$row.find( '.us-icon-value' );
			this.$select = this.$row.find( '.us-icon-select' );
			this.$text = this.$row.find( '.us-icon-text' );
			this.$preview = this.$row.find( '.us-icon-preview > i' );
			this.$setLink = this.$row.find( '.us-icon-set-link' );

			this.$select.on( 'change', function() {
				var $selectedOption = this.$select.find( ":selected" );
				if ( $selectedOption.length ) {
					this.$setLink.attr( 'href', $selectedOption.data( 'info-url' ) );
				}
				this.setIconValue();
			}.bind( this ) );

			this.$text.on( 'change keyup', function() {
				var val = this.$text.val();
				if ( val.toLowerCase().replace( /^\s+/g, '' ) !== val ) {
					this.$text.val( $.trim( val.toLowerCase() ) );
				}
				this.setIconValue();
			}.bind( this ) );
			this.$row.on( 'click', '.usof-example', this.exampleClick.bind( this ) );
		},
		exampleClick: function( ev ) {
			var $target = $( ev.target ).closest( '.usof-example' ),
				example = $target.html();
			this.$text.val( example );
			this.setIconValue();
		},
		setIconValue: function() {
			var icon_set = this.$select.val(),
				icon_name = $.trim( this.$text.val() ),
				icon_val = '';
			if ( icon_name != '' ) {
				if ( icon_set == 'material' ) {
					icon_name = icon_name.replace( / +/g, '_' );
				}
				icon_val = icon_set + '|' + icon_name;
			}
			this.renderPreview( icon_set, icon_name );
			this.$value.val( icon_val );
		},
		renderValue: function( value ) {
			var $selectedOption;
			value = value.trim().split( '|' );
			if ( value.length != 2 ) {
				$selectedOption = this.$select.find( 'option:first' );
				this.$text.val( '' );
			} else {
				value[ 0 ] = value[ 0 ].toLowerCase();
				$selectedOption = this.$select.find( 'option[value="' + value[ 0 ] + '"]' );
				this.$text.val( value[ 1 ] );
			}
			if ( $selectedOption.length ) {
				this.$select.find( 'option' ).prop( 'selected', false );
				$selectedOption.prop( 'selected', 'selected' );
			}

			this.renderPreview( value[ 0 ], value[ 1 ] );
		},
		renderPreview: function( icon_set, icon_name ) {
			if ( icon_name != '' ) {
				if ( icon_set == 'material' ) {
					this.$preview.attr( 'class', 'material-icons' ).html( icon_name );
				} else {
					if ( icon_name != undefined ) {
						icon_name = icon_name.replace( /fa-\dx/gi, ' ' );
					}
					this.$preview.attr( 'class', icon_set + ' fa-' + icon_name ).html( '' );
				}
			} else {
				this.$preview.attr( 'class', 'material-icons' ).html( '' );
			}
		},
		setValue: function( value, quiet ) {
			this.renderValue( value );
			this.parentSetValue( value, quiet );
		}
	};

	/**
	 * USOF Field: Slider with units
	 */
	$usof.field[ 'slider' ] = {
		init: function( options ) {
			this.$slider = this.$row.find( '.usof-slider' );
			this.$textfield = this.$row.find( 'input[type="text"]' );
			this.$box = this.$row.find( '.usof-slider-box' );
			this.$range = this.$row.find( '.usof-slider-range' );
			this.$unitsSelector = this.$row.find( '.usof-slider-selector-units' );
			this.$units = this.$row.find( '.usof-slider-selector-unit' );
			this.$body = $( document.body );
			this.$window = $( window );
			this.$usofContainer = $( '.usof-container' );
			// Default unit options
			this.defaultUnit = {};
			// Unit options (max, min, step, unit)
			this.unitsOptions = {};
			this.isFocused = false;

			this.$units.each( function( index, item ) {
				var $item = $( item ),
					data = $item.data(),
					unit = data.unit;
				if ( index == 0 ) {
					this.defaultUnit.max = data.max;
					this.defaultUnit.min = data.min;
					this.defaultUnit.step = data.step;
					this.defaultUnit.unit = unit;
				}
				// Fill with separate unit options from theme options config
				data = [unit, data.std, data.step, data.max, data.min];
				this.unitsOptions[ unit ] = data;

			}.bind( this ) );

			// Params
			this.unitsExpression = this.$unitsSelector.data( 'units_expression' ) || '\w+';
			this.max = parseFloat( this.$unitsSelector.data( 'max' ) ) || this.defaultUnit.max;
			this.min = parseFloat( this.$unitsSelector.data( 'min' ) ) || this.defaultUnit.min;
			this.step = parseFloat( this.$unitsSelector.data( 'step' ) ) || this.defaultUnit.step;
			this.unit = this.$unitsSelector.data( 'unit' ) || '';

			// Needed box dimensions
			this.sz = {};
			var draggedValue;

			this.$textfield.on( 'keyup', function( ev ) {
				if ( ev.key == 'Enter' ) {
					this.$textfield.blur();
				}
			}.bind( this ) );

			this.$unitsSelector.on( 'mousedown', function( ev ) {
				var $target = $( ev.target ).closest( '.usof-slider-selector-unit' ),
					data,
					value = this.$textfield.val();
				// Do nothing if unit wasn't selected
				if ( ! $target.length ) {
					return;
				}

				value = parseFloat( value.replace( '[^0-9.]+', '' ) );
				data = $target.data();
				this.min = data.min;
				this.max = data.max;
				this.step = data.step;
				this.unit = data.unit;
				this.$textfield.val( value + data.unit );
			}.bind( this ) );

			/**
			 * Mouse and trackpad scroll events
			 */
			this._mouseScrollEvents = [ 'wheel', 'mousewheel', 'DOMMouseScroll' ];

			/**
			 * Event handlers
			 */
			this._events = {
				dragstart: function( e ) {
					e.stopPropagation();
					this.$usofContainer.addClass( 'dragged' );
					this.$box.addClass( 'dragged' );
					this.sz = {
						left: this.$box.offset().left,
						right: this.$box.offset().left + this.$box.width(),
						width: this.$box.width()
					};
					this.$body.on( 'mousemove', this._events.dragmove );
					this.$window.on( 'mouseup', this._events.dragstop );
					this._events.dragmove( e );
				}.bind( this ),
				dragmove: function( e ) {
					e.stopPropagation();
					var x, value;
					if ( this.$body.hasClass( 'rtl' ) ) {
						x = Math.max( 0, Math.min( 1, ( this.sz == 0 ) ? 0 : ( ( this.sz.right - e.pageX ) / this.sz.width ) ) );
					} else {
						x = Math.max( 0, Math.min( 1, ( this.sz == 0 ) ? 0 : ( ( e.pageX - this.sz.left ) / this.sz.width ) ) )
					}
					value = parseFloat( this.min + x * ( this.max - this.min ) );
					value = Math.round( value / this.step ) * this.step;
					this.renderValue( value );
					draggedValue = value;
				}.bind( this ),
				dragstop: function( e ) {
					e.preventDefault();
					e.stopPropagation();
					this.$usofContainer.removeClass( 'dragged' );
					this.$box.removeClass( 'dragged' );
					this.$body.off( 'mousemove', this._events.dragmove );
					this.$window.off( 'mouseup', this._events.dragstop );
					this.setValue( draggedValue );
				}.bind( this ),
				mousewheel: function( e ) {
					e.preventDefault
						? e.preventDefault()
						: ( e.returnValue = false );
					e.stopPropagation();
					if ( ! this.isFocused ) {
						return false;
					}
					// wheelDelta doesn't let you know the number of pixels
					var direction = e.deltaY || e.detail || e.wheelDelta;

					if ( direction < 0 ) {
						var value = Math.min( this.max, parseFloat( this.getValue() ) + this.step );
					} else {
						var value = Math.max( this.min, parseFloat( this.getValue() ) - this.step );
					}
					value = Math.round( value / this.step ) * this.step;
					if ( $.isNumeric( value ) ) {
						value = this.getDecimal( value );
					}

					this.setValue( value );
				}.bind( this ),
				mouseenter: function( e ) {
					// https://developers.google.com/web/updates/2017/01/scrolling-intervention
					$.each( this._mouseScrollEvents, function( _, eventName ) {
						this.$window[0].addEventListener( eventName, this._events.mousewheel, { passive: false } );
					}.bind( this ) );
				}.bind( this ),
				mouseleave: function( e ) {
					// https://developers.google.com/web/updates/2017/01/scrolling-intervention
					$.each( this._mouseScrollEvents, function( _, eventName ) {
						this.$window[0].removeEventListener( eventName, this._events.mousewheel );
					}.bind( this ) );
				}.bind( this )
			};

			this.$textfield.on( 'focus', function() {
				this.$textfield.val( this.getValue() );
				this.oldTextFieldValue = this.getValue();
				this.isFocused = true;
			}.bind( this ) );

			this.$textfield.on( 'blur', function() {
				var rawValue = this.$textfield.val(),
					value = parseFloat( rawValue.replace( '[^0-9.]+', '' ) );
				this.isFocused = false;

				if ( ! $.isNumeric( rawValue ) ) {
					var pattern = new RegExp( '^(-?\\d+)(\\.)?(\\d+)?(' + this.unitsExpression + ')?$' ),
						matches = this.$textfield.val().match( pattern );
					if ( matches && matches[ 4 ] ) {
						for ( var i in this.unitsOptions ) {
							if ( ! this.unitsOptions.hasOwnProperty( i ) ) {
								continue;
							}

							if ( this.unitsOptions[ i ][ 0 ] == matches[ 4 ] ) {
								this.unit = matches[ 4 ];
								this.max = this.unitsOptions[ i ][ 3 ];
								this.min = this.unitsOptions[ i ][ 4 ];
								this.step = this.unitsOptions[ i ][ 2 ];
							}
						}
					}
				} else {
					this.unit = this.defaultUnit.unit;
					this.max = this.defaultUnit.max;
					this.min = this.defaultUnit.min;
					this.step = this.defaultUnit.step;
				}
				if ( ! this.unit ) {
					this.unit = this.defaultUnit.unit;
				}
				if ( ( value || parseFloat( value ) === 0 ) && value != this.oldTextFieldValue ) {
					this.setValue( value );
				} else {
					this.renderValue( this.oldTextFieldValue );
				}
			}.bind( this ) );
			this.$box.on( 'mousedown', this._events.dragstart );
			this.$textfield.on( 'mouseenter', this._events.mouseenter );
			this.$textfield.on( 'mouseleave', this._events.mouseleave );
		},
		getDecimal: function( value ) {
			value = parseFloat( value );
			var valueDecimalPart = Math.abs( value ) % 1 + '';

			if ( valueDecimalPart.charAt( 3 ) !== '' && valueDecimalPart.charAt( 3 ) !== '0' ) { // Decimal part has 1/100 part
				value = value.toFixed( 2 );
			} else if ( valueDecimalPart.charAt( 2 ) !== '' && valueDecimalPart.charAt( 2 ) !== '0' ) { // Decimal part has 1/10 part
				value = value.toFixed( 1 );
			} else { // Decimal part is less than 1/100 or it is just 0
				value = value.toFixed( 0 );
			}
			return value;
		},
		renderValue: function( value ) {
			if ( ! $.isNumeric( value ) ) {
				value = parseFloat( value.replace( '[^0-9.]+', '' ) );
			}
			var x = Math.max( 0, Math.min( 1, ( value - this.min ) / ( this.max - this.min ) ) );
			if ( this.$body.hasClass( 'rtl' ) ) {
				this.$range.css( 'right', x * 100 + '%' );
			} else {
				this.$range.css( 'left', x * 100 + '%' );
			}
			if ( $.isNumeric( value ) ) {
				value = this.getDecimal( value );
			}
			value = value + this.unit;
			this.$textfield.val( value );

			return value;
		},
		setValue: function( value, quiet ) {
			if ( this.unit ) {
				var valueStr = value + '',
					pattern = new RegExp( '^(-?\\d+)(\\.)?(\\d+)?(' + this.unitsExpression + ')?$' ),
					matches = valueStr.match( pattern );
				if ( matches != null ) {
					var unit = matches[ 4 ] || this.unit;
					if ( unit !== this.unit ) {
						this.unit = unit;
						this.$unitsSelector.data( 'unit', unit );
					}
					if ( matches[ 4 ] == undefined ) {
						value += this.unit;
					}
				}
			}
			value = this.renderValue( value );
			this.parentSetValue( value, quiet );
		}
	};

	/**
	 * USOF Field: Switch
	 */
	$usof.field[ 'switch' ] = {

		getValue: function() {
			return ( this.$input.is( ':checked' ) ? this.$input.get( 1 ).value : this.$input.get( 0 ).value );
		},

		setValue: function( value, quiet ) {
			if ( typeof value == 'string' ) {
				value = ( value == 'true' || value == 'True' || value == 'TRUE' || value == '1' );
			} else if ( typeof value != 'boolean' ) {
				value = !! parseInt( value );
			}
			this.$input.filter( '[type="checkbox"]' ).prop( 'checked', value );
		}

	};

	/**
	 * USOF Field: Text / Textarea
	 */
	$usof.field[ 'text' ] = $usof.field[ 'textarea' ] = {

		init: function() {
			this.$row.on( 'click', '.usof-example', this.exampleClick.bind( this ) );
			this.$input.on( 'change keyup', function() {
				this.trigger( 'change', this.getValue() );
			}.bind( this ) );
		},
		exampleClick: function( ev ) {
			var $target = $( ev.target ).closest( '.usof-example' ),
				example = $target.html();
			this.setValue( example );
		}
	};

	/**
	 * USOF Field: Transfer
	 */
	$usof.field[ 'transfer' ] = {

		init: function() {
			if ( $usof.instance.$sections[ 'headerbuilder' ] !== undefined ) {
				$usof.instance.fireFieldEvent( $usof.instance.$sections[ 'headerbuilder' ], 'beforeShow' );
				$usof.instance.fireFieldEvent( $usof.instance.$sections[ 'headerbuilder' ], 'beforeHide' );
			}

			this.$textarea = this.$row.find( 'textarea' );
			this.translations = ( this.$row.find( '.usof-transfer-translations' )[ 0 ].onclick() || {} );
			this.$btnImport = this.$row.find( '.usof-button.type_import' ).on( 'click', this.importValues.bind( this ) );

			this.hiddenFieldsValues = $( '.usof-hidden-fields' )[ 0 ].onclick() || {};
			// If there are no hidden values (array length == 0), hiddenFieldsValues JSON equals [] isntead of {}, but
			// wee need {}, so we can extend values of options correctly
			if ( this.hiddenFieldsValues.length == 0 ) {
				this.hiddenFieldsValues = {};
			}

			this.exportValues();
			this.on( 'beforeShow', this.exportValues.bind( this ) );
		},

		exportValues: function() {
			var values = $.extend( this.hiddenFieldsValues, $usof.instance.getValues() );
			this.$textarea.val( JSON.stringify( values ) );
		},

		importValues: function() {
			var encoded = this.$textarea.val(),
				values;

			if ( encoded.charAt( 0 ) == '{' ) {
				this.$btnImport.addClass( 'loading' );
				// New USOF export: json-encoded
				$.ajax( {
					type: 'POST',
					url: $usof.ajaxUrl,
					dataType: 'json',
					data: {
						action: 'usof_save',
						usof_options: encoded,
						_wpnonce: $usof.instance.$container.find( '[name="_wpnonce"]' ).val(),
						_wp_http_referer: $usof.instance.$container.find( '[name="_wp_http_referer"]' ).val()
					},
					success: function( result ) {
						this.$btnImport.removeClass( 'loading' );
						if ( result.success ) {
							alert( this.translations.importSuccess );
							location.reload();
						} else {
							alert( result.data.message );
						}
					}.bind( this )
				} );
			} else {
				try {
					// Old SMOF export: base64-encoded
					var serialized = window.atob( encoded ),
						matches = serialized.match( /(s\:[0-9]+\:\"(.*?)\"\;)|(i\:[0-9]+\;)/g ),
						_key = null,
						_value;
					values = {};
					for ( var i = 0; i < matches.length; i ++ ) {
						_value = matches[ i ].replace( ( matches[ i ].charAt( 0 ) == 's' ) ? /^s\:[0-9]+\:\"(.*?)\"\;$/ : /^i\:([0-9]+)\;$/, '$1' );
						if ( _key === null ) {
							_key = _value;
						} else {
							values[ _key ] = _value;
							_key = null;
						}
					}
					$usof.instance.setValues( values );
					this.valuesChanged = values;
					$usof.instance.save();
				}
				catch ( error ) {
					return alert( this.translations.importError );
				}

			}

		}

	};

	/**
	 * USOF Field: Style Scheme
	 */
	$usof.field[ 'style_scheme' ] = {

		init: function( options ) {
			this.$input = this.$row.find( 'input[name="' + this.name + '"]' );
			this.$schemesContainer = this.$row.find( '.usof-schemes-list' );
			this.$schemeItems = this.$row.find( '.usof-schemes-list > li' );
			this.$nameInput = this.$row.find( '#scheme_name' );
			this.$closeBtn = this.$row.find( '.us-bld-window-closer' );
			this.$saveNewBtn = this.$row.find( '#save_new_scheme' ).on( 'click', this.saveScheme.bind( this ) );
			this.colors = ( this.$row.find( '.usof-form-row-control-colors-json' )[ 0 ].onclick() || {} );
			this.i18n = ( this.$row.find( '.usof-form-row-control-i18n' )[ 0 ].onclick() || {} );
			this.$nameInput.on( 'keyup', this.setSchemeButtonStates.bind( this ) );
			this.initSchemes( true );
		},
		setSchemeButtonStates: function( action ) {
			if ( ! this.$schemesContainer ) {
				return;
			}
			if ( this.$nameInput.val().length ) {
				this.$saveNewBtn.removeAttr( 'disabled' );
				if ( action.key == 'Enter' ) {
					this.$saveNewBtn.click();
				}
			} else {
				this.$saveNewBtn.attr( 'disabled', '' );
			}
		},
		initSchemes: function( initialize, id ) {
			if ( initialize ) {
				// Hide Schemes on init
				this.$row.hide();

				// Close Schemes popup
				this.$closeBtn.on( 'click', function() {
					this.$row.hide();
				}.bind( this ) );

				$.ajax( {
					type: 'POST',
					url: $usof.ajaxUrl,
					dataType: 'json',
					data: {
						action: 'usof_get_style_schemes',
						_wpnonce: this.$row.closest( '.usof-form' ).find( '[name="_wpnonce"]' ).val(),
						_wp_http_referer: this.$row.closest( '.usof-form' ).find( '[name="_wp_http_referer"]' ).val()
					},
					success: function( result ) {
						this.schemes = result.data.schemes;
						this.customSchemes = result.data.custom_schemes;
					}.bind( this ),
					error: function() {
						this.schemes = {};
						this.customSchemes = {};
					}.bind( this ),
				} );
			}

			if ( id ) {
				var $savedScheme = this.$schemeItems.filter( '.type_custom[data-id="' + id + '"]' );
				$savedScheme.addClass( 'saved' );
				setTimeout( function() {
					$savedScheme.removeClass( 'saved' );
				}, 900 );
			}
			this.$schemeItems.each( function( index, item ) {
				var $item = $( item ),
					$deleteBtn = $item.find( '.usof-schemes-item-delete' ),
					schemeId = $item.data( 'id' ),
					isCustom = $item.hasClass( 'type_custom' ),
					colors;

				$item.find( '.usof-schemes-item-save' ).on( 'click', this.saveScheme.bind( this ) );

				$deleteBtn.on( 'click', function( event ) {
					event.preventDefault();
					event.stopPropagation();
					var $target = $( event.target ),
						$deletingScheme = $target.closest( '.usof-schemes-item' ),
						schemeId = $deletingScheme.data( 'id' );
					this.deleteScheme( schemeId, event );
				}.bind( this ) );

				$item.on( 'click', function() {
					if ( window.$usof !== undefined && $usof.instance !== undefined ) {
						if ( ( ! isCustom && this.schemes[ schemeId ] === undefined ) || ( isCustom && this.customSchemes[ schemeId ] === undefined ) || ( ! isCustom && this.schemes[ schemeId ].values === undefined ) || ( isCustom && this.customSchemes[ schemeId ].values === undefined ) ) {
							return;
						}
						this.setSchemeButtonStates();
						if ( isCustom ) {
							colors = this.customSchemes[ schemeId ].values;
							this.$input.val( 'custom-' + schemeId );
							this.trigger( 'change', 'custom-' + schemeId );
						} else {
							colors = this.schemes[ schemeId ].values;
							this.$input.val( schemeId );
							this.trigger( 'change', schemeId );
						}
						$.each( colors, function( id, value ) {
							$usof.instance.setValue( id, value );
						} );
					}
					this.$row.hide();
				}.bind( this ) );
			}.bind( this ) );
		},
		getColorValues: function() {
			var colors = {};
			if ( window.$usof === undefined || $usof.instance == undefined ) {
				return undefined;
			}
			if ( this.colors == undefined ) {
				return undefined;
			}
			$.each( this.colors, function( id, color ) {
				colors[ color ] = $usof.instance.getValue( color );
			} );

			return colors;
		},
		saveScheme: function( event ) {
			var colors = this.getColorValues(),
				name = this.$nameInput.val(),
				scheme = { name: name, colors: colors },
				$target = $( event.target ),
				$savingScheme = $target.closest( '.usof-schemes-item' ),
				$button = $( event.target.closest( 'button' ) );
			if ( ! $button.length ) {
				if ( $savingScheme.hasClass( 'type_custom' ) ) {
					scheme.name = $savingScheme.find( '.preview_heading' ).html();
					scheme.id = $savingScheme.data( 'id' );
					$savingScheme.addClass( 'saving' );
				} else {
					return false;
				}
			} else {
				this.$saveNewBtn.addClass( 'loading' );
			}
			$.ajax( {
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_save_style_scheme',
					scheme: JSON.stringify( scheme ),
					_wpnonce: this.$row.closest( '.usof-form' ).find( '[name="_wpnonce"]' ).val(),
					_wp_http_referer: this.$row.closest( '.usof-form' ).find( '[name="_wp_http_referer"]' ).val()
				},
				success: function( result ) {
					this.setSchemes( result.data.schemes, result.data.customSchemes, result.data.schemesHtml, scheme.id );
					this.$nameInput.val( '' );
					this.$saveNewBtn.removeClass( 'loading' ).attr( 'disabled', '' );
				}.bind( this )
			} );
			return false;
		},
		deleteScheme: function( schemeId, event ) {
			event.stopPropagation();
			if ( ! confirm( this.i18n.delete_confirm ) ) {
				return false;
			}
			var $target = $( event.target );
			$target.closest( '.usof-schemes-item' ).addClass( 'deleting' );
			$.ajax( {
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_delete_style_scheme',
					scheme: schemeId,
					_wpnonce: this.$row.closest( '.usof-form' ).find( '[name="_wpnonce"]' ).val(),
					_wp_http_referer: this.$row.closest( '.usof-form' ).find( '[name="_wp_http_referer"]' ).val()
				},
				success: function( result ) {
					this.setSchemes( result.data.schemes, result.data.customSchemes, result.data.schemesHtml );
				}.bind( this )
			} );
			return false;
		},
		setSchemes: function( schemes, customSchemes, schemesHtml, id ) {
			this.schemes = schemes;
			this.customSchemes = customSchemes;
			this.$schemesContainer.html( schemesHtml );
			this.$schemeItems = this.$row.find( '.usof-schemes-list > li' );
			this.initSchemes( false, id );
		}
	};

	/**
	 * USOF Field: Upload
	 */
	$usof.field[ 'upload' ] = {

		init: function( options ) {
			this.attachmentAtts = {};
			this.parentInit( options );
			// Cached URLs for certain values (images IDs and sizes)
			this.$upload = this.$row.find( '.usof-upload' );
			this.previewType = this.$upload.usMod( 'preview' );
			this.isMultiple = this.$upload.hasClass( 'is_multiple' ); // works for text preview only at the moment
			this.$btnSet = this.$row.find( '.usof-button.type_upload' );
			this.$btnRemove = this.$row.find( '.usof-button.type_remove' );
			this.$previewContainer = this.$row.find( '.usof-upload-preview' );
			this.placeholder = this.$row.find( 'input[name="placeholder"]' ).val();
			if ( this.previewType == 'image' ) {
				this.$previewImg = this.$previewContainer.find( 'img' );
			} else if ( this.previewType == 'text' ) {
				this.$fontName = this.$previewContainer.find( '.usof-upload-file' );
			}
			this.$btnSet.add( this.$row.find( '.usof-button.type_change' ) ).on( 'click', this.openMediaUploader.bind( this ) );
			this.$btnRemove.on( 'click', function() {
				this.setValue( '' );
			}.bind( this ) );
		},

		setValue: function( value, quiet ) {
			if ( value == '' ) {
				// Removed value
				if ( this.previewType == 'image' ) {
					if ( this.placeholder !== undefined ) {
						this.$btnRemove.addClass( 'hidden' );
						this.$previewImg.attr( 'src', this.placeholder );
					} else {
						this.$previewImg.attr( 'src', '' );
						this.$previewContainer.hide();
						this.$btnSet.show();
					}
				} else if ( this.previewType == 'text' ) {
					this.$fontName.html( '' );
					this.$previewContainer.hide();
					this.$btnSet.show();
				}
			} else {
				var files;
				if ( ! this.isMultiple ) {
					files = [value];
				} else {
					files = value;
				}
				if ( this.previewType == 'text' ) {
					this.$fontName.html( '' );
				}
				$.each( files, function( index, file ) {
					if ( file.match( /^[0-9]+(\|[a-z_\-0-9]+)?$/ ) ) {
						var attachment = wp.media.attachment( parseInt( file ) ),
							renderAttachmentImage = function() {
								var src = attachment.attributes.url;
								if ( attachment.attributes.sizes !== undefined ) {
									var size = ( attachment.attributes.sizes.medium !== undefined ) ? 'medium' : 'full';
									src = attachment.attributes.sizes[ size ].url;
								}
								if ( this.previewType == 'image' ) {
									this.$previewImg.attr( 'src', src );
								} else if ( this.previewType == 'text' ) {
									var html = this.$fontName.html() + '<span>' + this._baseName( src ) + '</span>';
									this.$fontName.html( html );
								}
							}.bind( this );
						if ( attachment.attributes.url !== undefined ) {
							renderAttachmentImage();
						} else {
							// Loading missing data via ajax
							attachment.fetch( { success: renderAttachmentImage } );
						}
					} else {
						// Direct image URL (for old SMOF framework compatibility)
						if ( this.previewType == 'image' ) {
							this.$previewImg.attr( 'src', file );
						} else if ( this.previewType == 'text' ) {
							this.$fontName.html( this._baseName( file ) );
						}
					}
				}.bind( this ) );

				this.$previewContainer.show();
				this.$btnSet.hide();
				if ( this.placeholder != undefined ) {
					this.$btnRemove.removeClass( 'hidden' );
				}
			}
			this.parentSetValue( value, quiet );
		},

		openMediaUploader: function() {
			if ( this.frame === undefined ) {
				var mediaSettings = {
					title: this.$btnSet.text(),
					multiple: false,
					button: { text: this.$btnSet.text() }
				};
				if ( this.previewType == 'image' ) {
					mediaSettings.library = { type: 'image' };
				}
				if ( this.isMultiple ) {
					mediaSettings.multiple = 'add';
				}
				this.frame = wp.media( mediaSettings );
				this.frame.on( 'open', function() {
					var value;
					if ( this.isMultiple ) {
						value = this.getValue().trim().split( ',' );
						$.each( value, function( index, file ) {
							var fileID = parseInt( file );
							if ( fileID ) {
								this.frame.state().get( 'selection' ).add( wp.media.attachment( fileID ) );
							}
						}.bind( this ) );
					} else {
						value = parseInt( this.getValue() );
						if ( value ) {
							this.frame.state().get( 'selection' ).add( wp.media.attachment( value ) );
						}
					}


				}.bind( this ) );
				this.frame.on( 'select', function() {
					if ( this.isMultiple ) {
						var attachments = [];
						this.frame.state().get( 'selection' ).each( function( attachment, index ) {
							attachments.push( attachment.id + '|full' );
						} );
						this.setValue( attachments );
					} else {
						var attachment = this.frame.state().get( 'selection' ).first();
						this.attachmentAtts = attachment.attributes;
						this.setValue( attachment.id + '|full' );
					}

				}.bind( this ) );
			}
			this.frame.open();
		},

		_baseName: function( src ) {
			var base = new String( src ).substring( src.lastIndexOf( '/' ) + 1 );
			return base;
		}

	};

	/**
	 * USOF Field: Design Options
	 */
	$usof.field[ 'design_options' ] = {
		init: function() {
			// Variables
			this.groupParams = {};
			this.defaultGroupValues = {}; // Default parameter values by groups
			this.defaultValues = {}; // Default inline values
			this.states = ['default'];
			this.extStates = [];

			// Elements
			this.$container = this.$row.find( '.usof-design-options' );
			this.$input = $( 'input.usof_design_value', this.$container );

			// Get states
			this.extStates = this.$container[ 0 ].onclick() || [];
			this.states = this.states.concat( this.extStates );
			this.$container.removeAttr( 'onclick' );

			// Fix live click for WPBakery Page Builder
			this.isWPBakery = this.$input.hasClass( 'wpb_vc_param_value' );
			if ( this.isWPBakery ) {
				this.$container
					.closest( '.edit_form_line' )
					.addClass( 'usof-not-live' );
			}

			// Creates copy settings for different screen sizes
			if ( this.extStates.length ) {
				$( '[data-device-type-content="default"]', this.$container )
					.each( function( _, content ) {
						var $content = $( content );
						this.extStates.map( function( deviceType ) {
							$content.after( $content.clone().attr( 'data-device-type-content', deviceType ).addClass( 'hidden' ) );
						}.bind( this ) );
					}.bind( this ) );
			}

			// State grouping
			this.states.map( function( deviceType ) {
				this.groupParams[ deviceType ] = new $usof.GroupParams(
					$( '[data-device-type-content="' + deviceType + '"]', this.$container )
				);
			}.bind( this ) );

			$.each( this.groupParams, function( deviceType, groupParams ) {
				// Group start parameters
				$.each( groupParams.fields, function( fieldName, field ) {
					var $group = field.$row.closest( '[data-accordion-content]' ),
						value = field.getValue();
					if ( $group.length ) {
						var groupKey = $group.data( 'accordion-content' );

						// Save groups
						if ( ! this.defaultGroupValues.hasOwnProperty( groupKey ) ) {
							this.defaultGroupValues[ groupKey ] = {};
						}
						if ( ! this.defaultGroupValues[ groupKey ].hasOwnProperty( deviceType ) ) {
							this.defaultGroupValues[ groupKey ][ deviceType ] = {};
						}
						this.defaultGroupValues[ groupKey ][ deviceType ][ fieldName ] = value;

						// Save default value
						if ( ! this.defaultValues.hasOwnProperty( deviceType ) ) {
							this.defaultValues[ deviceType ] = {};
						}
						this.defaultValues[ deviceType ][ fieldName ] = value;

						// Add devive type to group and field
						$group.data( 'device-type', deviceType )
						field.deviceType = deviceType;
					}
				}.bind( this ) );
				// Initializing control over parameter associations
				$.each( groupParams.fields, function( _, field ) {
					var $row = field.$row;
					if ( $row.attr( 'onclick' ) ) {
						field._data = $row[ 0 ].onclick() || '';
						$row.removeAttr( 'onclick' );
						if ( field._data.hasOwnProperty( 'relations' ) ) {
							$row.append( '<i class="fas fa-unlink"></i>' )
								.on( 'click', 'i.fas', this._events.watchAttrLink.bind( this, field ) );
						}
					}
					// Watch events
					field
						.trigger( 'beforeShow' )
						.on( 'change', this._events.changeValue.bind( this ) );
				}.bind( this ) );
			}.bind( this ) );

			// Initializing parameters for shortcodes
			var pid = setTimeout( function() {
				if ( ! this.inited ) {
					this.setValue( this.$input.val() );
					// Check for changes in the parameter group
					this.checkChangeValues.call( this );
				}
				clearTimeout( pid );
			}.bind( this ), 1 );

			// Hide/Show states panel
			this.$container.find( '.us-bld-states' ).toggleClass( 'hidden', ! this.extStates.length );

			// Watch events
			this.$container
				.on( 'click', '[data-accordion-id]', this._events.toggleAccordion.bind( this ) )
				.on( 'click', '.usof-design-options-reset', this._events.resetValues.bind( this ) )
				.on( 'click', '.usof-design-options-responsive', this._events.toggleResponsive.bind( this ) )
				.on( 'click', '[data-device-type]', this._events.changeDeviceTypes.bind( this ) );
		},
		// Event handlers
		_events: {
			/**
			 * Collects parameters into a string when changing any parameter.
			 * @return void
			 */
			changeValue: function() {
				var resultValue = {};
				$.each( this.groupParams, function( deviceType, groupParams ) {
					var groupValues = groupParams.getValues();
					// Check the parameters, if the value is not default then add the setting to the result value
					$.each( groupValues, function( param, value ) {
						var defaultValue = this.defaultValues[ deviceType ][ param ];
						if ( value !== defaultValue ) {
							if ( ! resultValue.hasOwnProperty( deviceType ) ) {
								resultValue[ deviceType ] = {};
							}
							// Image URL support
							if ( param === 'background-image' && /http/.test( value ) ) {
								value = 'url(' + value + ')';
							}
							resultValue[ deviceType ][ param ] = value;
						}
					}.bind( this ) );
				}.bind( this ) );

				resultValue = ( JSON.stringify( resultValue ) !== '{}' )
					// Due to the nature of WPBakery Page Builder, we convert special characters standard escape
					// function
					? escape( JSON.stringify( resultValue ) )
					: '';

				// Set result value
				this.$input.val( resultValue );

				// Check for changes in the parameter group
				if ( !! this._debounce ) {
					clearTimeout( this._debounce );
				}
				this._debounce = setTimeout( function() {
					this.checkChangeValues.call( this );
					clearTimeout( this._debounce );
				}.bind( this ), 1 );
			},
			/**
			 * Enable or disable duplication
			 * @param {object} field USOF Field
			 * @param {object} e jQuery EventObject
			 * @param {bool|undefined} state
			 * @return void
			 */
			watchAttrLink: function( field, e, state ) {
				var $target = $( e.currentTarget ),
					isUnlink = $target.is( '.fa-unlink' ),
					relations = [];
				if ( state !== undefined ) {
					isUnlink = state;
				}
				if ( field.hasOwnProperty( '_data' ) && field.hasOwnProperty( 'deviceType' ) ) {
					$.each( this.groupParams[ field.deviceType ].fields, function( _name, item ) {
						if ( $.inArray( item.name, field._data.relations || [] ) !== - 1 ) {
							relations.push( item );
						}
					} );
				}
				$target
					.toggleClass( 'fa-link', isUnlink )
					.toggleClass( 'fa-unlink', ! isUnlink );
				if ( relations.length ) {
					relations.map( function( item ) {
						item.$input.prop( 'disabled', isUnlink );
					} );
					field.watchValue = isUnlink;
					if ( isUnlink ) {
						field.$input
							.focus()
							.on( 'input', this._events.changeRelationsValue.bind( this, relations ) )
							.trigger( 'input' );
					} else {
						field.$input.off( 'input' );
					}
				}
			},
			/**
			 * Duplicates settings to related fields
			 * @param {object} fields jQuery Nodes
			 * @param {object} e jQuery EventObject
			 * @return void
			 */
			changeRelationsValue: function( fields, e ) {
				var $this = $( e.currentTarget ),
					value = $this.val();
				fields.map( function( item ) {
					if ( item instanceof $usof.field ) {
						item.setValue( value );
					}
				} );
			},

			/**
			 * Accordion Switch
			 * @param {object} e jQuery EventObject
			 * @return void
			 */
			toggleAccordion: function( e ) {
				var $target = $( e.currentTarget ),
					$content = $( '[data-accordion-content="' + $target.data( 'accordion-id' ) + '"]' );

				if ( $target.hasClass( 'active' ) ) {
					$target.removeClass( 'active' );
					$content.removeClass( 'active' );
				} else {
					$target.siblings().removeClass( 'active' );
					$content.siblings().removeClass( 'active' );
					$target.addClass( 'active' );
					$content.addClass( 'active' );
				}
			},
			/**
			 * ON/OFF Responsive options
			 * @param {object} e jQuery EventObject
			 * @return void
			 */
			toggleResponsive: function( e ) {
				e.preventDefault();
				e.stopPropagation();

				var $target = $( e.currentTarget ),
					$header = $target.closest( '[data-accordion-id]' ),
					groupKey = $header.data( 'accordion-id' ),
					isEnaled = $header.hasClass( 'responsive' );

				$header.toggleClass( 'responsive', ! isEnaled );

				if ( this.defaultGroupValues.hasOwnProperty( groupKey ) ) {
					if ( !! isEnaled ) {
						this.$container
							.find( '[data-accordion-content="' + groupKey + '"] > .us-bld-states > [data-device-type="default"]' )
							.trigger( 'click' );
					}
					this.extStates.map( function( deviceType ) {
						// Reset values for a group whose responsive support is enabled
						var values = $.extend( {}, this.defaultGroupValues[ groupKey ][ deviceType ] || {} );
						if ( ! isEnaled ) {
							// Set default values for current deviceType
							$.each( values, function( prop ) {
								if ( this.groupParams[ 'default' ].fields.hasOwnProperty( prop ) ) {
									values[ prop ] = this.groupParams[ 'default' ].fields[ prop ].getValue();
								}
							}.bind( this ) );
						}
						if ( this.groupParams.hasOwnProperty( deviceType ) && this.groupParams[ deviceType ] instanceof $usof.GroupParams ) {
							this.groupParams[ deviceType ].setValues( values );
						}
						// Checking and duplicating wiretap related fields
						if ( ! isEnaled && this.groupParams.hasOwnProperty( deviceType ) ) {
							$.each( this.groupParams[ 'default' ].fields, function( _, field ) {
								if ( field.hasOwnProperty( 'watchValue' ) ) {
									$( '.fas', this.groupParams[ deviceType ].fields[ field.name ].$row )
										.trigger( 'click', field.watchValue );
								}
							}.bind( this ) );
						}
					}.bind( this ) );
				}
			},
			/**
			 * Resets all group settings to default
			 * @param {object} e jQuery EventObject
			 * @return void
			 */
			resetValues: function( e ) {
				e.stopPropagation();
				var $target = $( e.currentTarget ),
					$groupHeader = $target.closest( '[data-accordion-id]' ),
					groupName = $groupHeader.data( 'accordion-id' );
				$target.addClass( 'fa-spin' );
				// Hide responsive options
				if ( $groupHeader.hasClass( 'responsive' ) ) {
					this._events.toggleResponsive.call( this, e );
				}
				if ( this.defaultGroupValues.hasOwnProperty( groupName ) ) {
					$.each( this.defaultGroupValues[ groupName ], function( deviceType, defaultValues ) {
						var groupParams = this.groupParams[ deviceType ];
						groupParams.setValues( defaultValues );
						// Didable fields link
						$.each( defaultValues, function( groupParams, name ) {
							var fields = groupParams.fields;
							if (
								fields.hasOwnProperty( name )
								&& fields[ name ].hasOwnProperty( '_data' )
								&& fields[ name ]._data.hasOwnProperty( 'relations' )
							) {
								var $link = $( 'i.fas', groupParams.$fields[ name ] );
								if ( $link.length && $link.is( '.fa-link' ) ) {
									$link.trigger( 'click' );
								}
							}
						}.bind( this, groupParams ) );
					}.bind( this ) );
				}
				var pid = setTimeout( function() {
					$target.removeClass( 'fa-spin' );
					$groupHeader.removeClass( 'changed' );
					clearTimeout( pid );
				}, 1000 * 0.5 );
			},
			/**
			 * Choosing a group of settings for devices
			 * @param {object} e jQuery EventObject
			 * @return void
			 */
			changeDeviceTypes: function( e ) {
				var $target = $( e.currentTarget );
				$target.siblings().removeClass( 'active' );
				$target
					.addClass( 'active' )
					.closest( '.usof-design-options-content' )
					.find( '> [data-device-type-content]' )
					.addClass( 'hidden' )
					.filter( '[data-device-type-content="' + $target.data( 'device-type' ) + '"]' )
					.removeClass( 'hidden' );
			}
		},
		/**
		 * Check for changes in the parameter group
		 * @return void
		 */
		checkChangeValues: function() {
			// Get current values
			var currentGroupValues = {};
			$.each( this.groupParams, function( deviceType, groupParams ) {
				$.each( groupParams.fields, function( _, field ) {
					var groupName = field.$row
						.closest( '[data-accordion-content]' )
						.data( 'accordion-content' );
					if ( ! currentGroupValues.hasOwnProperty( groupName ) ) {
						currentGroupValues[ groupName ] = {};
					}
					if ( ! currentGroupValues[ groupName ].hasOwnProperty( deviceType ) ) {
						currentGroupValues[ groupName ][ deviceType ] = {};
					}
					currentGroupValues[ groupName ][ deviceType ][ field.name ] = field.getValue();
				} );
			} );
			$.each( this.defaultGroupValues, function( groupName, devices ) {
				var change = false;
				$.each( devices, function( deviceType, values ) {
					if ( ! currentGroupValues.hasOwnProperty( groupName ) || ! currentGroupValues[ groupName ].hasOwnProperty( deviceType ) ) {
						return;
					}
					change = ( change || JSON.stringify( values ) !== JSON.stringify( currentGroupValues[ groupName ][ deviceType ] ) );
				}.bind( this ) );
				this.$container
					.find( '[data-accordion-id=' + groupName + ']' )
					.toggleClass( 'changed', change );
			}.bind( this ) );
		},
		/**
		 * Get the value.
		 * @return string
		 */
		getValue: function() {
			var value = $.trim( this.$input.val() );
			return this.isWPBakery ? value : JSON.parse( unescape( value ) || '{}' );
		},
		/**
		 * Set the value.
		 * @param string value
		 * @return void
		 */
		setValue: function( value ) {
			// Get saved parameter values
			var savedValues = {};
			if ( this.isWPBakery && typeof value === 'string' ) {
				try {
					savedValues = JSON.parse( unescape( value ) || '{}' );
				}
				catch ( err ) {
					console.error( value );
					savedValues = {};
				}
			} else if ( $.isPlainObject( value ) ) {
				savedValues = value;
			}
			var pid = setTimeout( function() {
				// Set values and check link
				$.each( this.groupParams, function( deviceType, groupParams ) {
					// Reset values
					if ( ! this.isWPBakery ) {
						groupParams.setValues( this.defaultValues[ deviceType ] || {}, true );
					}
					var values = savedValues[ deviceType ] || {},
						propName = 'background-image';
					// Image URL support
					if ( values.hasOwnProperty( propName ) && /url\(/.test( values[ propName ] || '' ) ) {
						values[ propName ] = values[ propName ]
							.replace( /\s?url\("?(.*?)"?\)/gi, '$1' );
					}
					// Set values
					groupParams.setValues( values, true );

					// Check relations link
					$.each( groupParams.fields, function( _, field ) {
						if ( field.hasOwnProperty( '_data' ) && field._data.hasOwnProperty( 'relations' ) ) {
							var $row = field.$row,
								value = $.trim( field.getValue() ),
								isLink = [];
							// // Matching all related parameters, and if necessary enable communication.
							( field._data.relations || [] ).map( function( name ) {
								if ( value && this.groupParams[ field.deviceType ].fields.hasOwnProperty( name ) ) {
									isLink.push( value === $.trim( this.groupParams[ field.deviceType ].fields[ name ].getValue() ) );
								}
							}.bind( this ) );
							if ( isLink.length ) {
								isLink = isLink.filter( function( v ) {
									return v == true
								} );
								if ( isLink.length === 3 ) {
									var pid = setTimeout( function() {
										$row.find( 'i.fas' ).trigger( 'click' );
										clearTimeout( pid );
									}, 1 );
								}
							}
						}
					}.bind( this ) );
				}.bind( this ) );

				// Check options for devices
				var responsiveGroups = {};
				this.extStates.map( function( deviceType ) {
					var values = savedValues[ deviceType ] || {};
					$.each( this.defaultGroupValues, function( groupKey, devices ) {
						var isEnable = false;
						$.each( devices[ deviceType ], function( prop ) {
							if ( ! responsiveGroups[ groupKey ] ) {
								responsiveGroups[ groupKey ] = values.hasOwnProperty( prop );
							}
						} );
					}.bind( this ) );
				}.bind( this ) );
				$.each( responsiveGroups, function( groupKey, isEnable ) {
					$( '[data-accordion-id="' + groupKey + '"]', this.$container )
						.toggleClass( 'responsive', isEnable );
				}.bind( this ) );

				// Check for changes in the parameter group
				this.checkChangeValues.call( this );

				// Default tab selection
				$( '[data-device-type="default"]', this.$container )
					.trigger( 'click' );

				clearTimeout( pid );
			}.bind( this ), 1 );

			// Set value
			this.$input.val( this.isWPBakery ? value : escape( JSON.stringify( value ) ) );

			// Hide all sections of the accordion
			if ( ! this.$input.hasClass( 'wpb_vc_param_value' ) ) {
				this.$container.find( '> div' ).removeClass( 'active' );
			}

		},
		/**
		 * Force value for WPBakery
		 * @return void
		 */
		forceWPBValue: function() {
			if ( this.isWPBakery ) {
				this.setValue( this.getValue() );
			}
		}
	};

	/**
	 * USOF Field: Used icons info
	 */
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
			 * @param EventObject e
			 * @return void
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
		 * Get a list of used icons
		 *
		 * @param string type
		 * @return void
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
	 */
	$usof.Group = function( row, options ) {
		this.init( row, options );
	};

	$usof.Group.prototype = {

		init: function( elm, options ) {
			this.$field = $( elm );
			this.$btnAddGroup = this.$field.find( '.usof-form-group-add' );
			this.$groupPrototype = this.$field.find( '.usof-form-group-prototype' );
			this.groupName = this.$field.data( 'name' );
			this.isBuilder = ( this.$field.parents( '.us-bld-window' ).length ) ? true : false;
			this.isSortable = this.$field.hasClass( 'sortable' );
			this.isAccordion = this.$field.hasClass( 'type_accordion' );
			this.isForButtons = this.$field.hasClass( 'preview_button' );
			this.groupParams = [];
			var $translations = this.$field.find( '.usof-form-group-translations' );
			this.groupTranslations = $translations.length ? ( $translations[ 0 ].onclick() || {} ) : {};

			if ( this.isBuilder ) {
				this.$parentElementForm = this.$field.closest( '.usof-form' );
				this.elementName = this.$parentElementForm.usMod( 'for' );
				this.$builderWindow = this.$field.closest( '.us-bld-window' );
			} else {
				this.$parentSection = this.$field.closest( '.usof-section' );
				this.$field.find( '.usof-form-group-item' ).each( function( i, groupParams ) {
					var $groupParams = $( groupParams );
					if ( ! $groupParams.parent().hasClass( 'usof-form-group-prototype' ) ) {
						this.groupParams.push( new $usof.GroupParams( $groupParams ) );
					}
				}.bind( this ) );
			}

			this.$btnAddGroup.on( 'click', this.addGroup.bind( this, undefined ) );
			this.$field.on( 'click', '.usof-control-duplicate', this.duplicateGroup.bind( this ) );

			this.$field.on( 'click', '.usof-control-delete', function( event ) {
				event.stopPropagation();
				var $btn = $( event.target ),
					$group = $btn.closest( '.usof-form-group-item' );
				this.groupDel( $group );
			}.bind( this ) );

			if ( this.isAccordion ) {

				this.$sections = this.$field.find( '.usof-form-group-item' );
				this.$field.on( 'click', '.usof-form-group-item-title', function( event ) {
					this.$sections = this.$field.find( '.usof-form-group-item' );
					var $parentSection = $( event.target ).closest( '.usof-form-group-item' );
					if ( $parentSection.hasClass( 'active' ) ) {
						$parentSection.removeClass( 'active' ).children( '.usof-form-group-item-content' ).slideUp();
					} else {
						$parentSection.addClass( 'active' ).children( '.usof-form-group-item-content' ).slideDown();
					}

				}.bind( this ) );
			}

			if ( this.isSortable ) {
				this.$field.on( 'dragstart', function( event ) {
					event.preventDefault();
				} );
				this.$body = $( document.body );
				this.$window = $( window );

				this.$dragshadow = $( '<div class="us-bld-editor-dragshadow"></div>' );

				this.$field.on( 'mousedown', '.usof-control-move', this._dragStart.bind( this ) );

				this._events = {
					_maybeDragMove: this._maybeDragMove.bind( this ),
					_dragMove: this._dragMove.bind( this ),
					_dragEnd: this._dragEnd.bind( this )
				};
			}
		},
		_reInitParams: function() {
			this.groupParams = [];
			this.$field.find( '.usof-form-group-item' ).each( function( i, groupParams ) {
				var $groupParams = $( groupParams );
				if ( ! $groupParams.parent().hasClass( 'usof-form-group-prototype' ) ) {
					this.groupParams.push( $groupParams.data( 'usofGroupParams' ) );
				}
			}.bind( this ) );
			if ( ! this.isBuilder ) {
				if ( $.isEmptyObject( $usof.instance.valuesChanged ) ) {
					clearTimeout( $usof.instance.saveStateTimer );
					$usof.instance.$saveControl.usMod( 'status', 'notsaved' );
				}
				$usof.instance.valuesChanged[ this.groupName ] = this.getValue();
			}
		},
		setValue: function( value ) {
			this.groupParams = [];
			this.$field.find( '.usof-form-group-item' ).each( function( i, groupParams ) {
				var $groupParams = $( groupParams );
				if ( ! $groupParams.parent().hasClass( 'usof-form-group-prototype' ) ) {
					$groupParams.remove();
				}
			}.bind( this ) );
			$.each( value, function( index, paramsValues ) {
				this.$btnAddGroup.before( this.$groupPrototype.html() );
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
		getValue: function() {
			var result = [];
			$.each( this.groupParams, function( i, groupParams ) {
				result.push( groupParams.getValues() );
			} );
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
			if ( ! this.isBuilder ) {
				if ( $.isEmptyObject( $usof.instance.valuesChanged ) ) {
					clearTimeout( $usof.instance.saveStateTimer );
					$usof.instance.$saveControl.usMod( 'status', 'notsaved' );
				}
				$usof.instance.valuesChanged[ this.groupName ] = this.getValue();
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
			this.$btnAddGroup.removeClass( 'adding' );
			return groupParams;
		},
		/**
		 * Duplicate group
		 * @param {EventObject} e
		 * @return void
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
		_hasClass: function( elm, cls ) {
			return ( ' ' + elm.className + ' ' ).indexOf( ' ' + cls + ' ' ) > - 1;
		},
		_isShadow: function( elm ) {
			return this._hasClass( elm, 'usof-form-group-dragshadow' );
		},
		_isSortable: function( elm ) {
			return this._hasClass( elm, 'usof-form-group-item' );
		}
	};

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
					e.preventDefault();
					this.trigger( 'dragdrop', e, this );
					return true;
				}.bind( this ) )

				.on( 'drop', itemSelector, function( e ) {
					var targetId = e.originalEvent.dataTransfer.getData( 'Text' ),
						$el = $( '> [usof-target-id="'+ targetId +'"]', this.$container ),
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
					e.preventDefault();
					$( e.currentTarget === e.target ? e.target : e.currentTarget )
						.addClass( this.css.over );
					this.trigger( 'over', e, this )
						.trigger( 'dragdrop', e, this );
					return true;
				}.bind( this ) )

				.on( 'dragleave', itemSelector, function( e ) {
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
					var $target = $( this._getTarget( e ) ),
						// Generate unique id for transfer text
						targetId = $usof.genUniqueId();
					$target.attr( 'usof-target-id', targetId );
					e.originalEvent.dataTransfer.effectAllowed = 'move';
					e.originalEvent.dataTransfer.setData( 'Text', targetId );
					e.originalEvent.dataTransfer.setDragImage( $target.get( 0 ), e.offsetX, e.offsetY );
					this.trigger( 'dragstart', e, this )
						.trigger( 'dragdrop', e, this );
					return true;
				}.bind( this ) )

				.on( 'dragend', function( e ) {
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
		 * Get the node to be moved
		 *
		 * @param eventObject e
		 * @return object.
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

	$usof.ajaxUrl = $( '.usof-container' ).data( 'ajaxurl' ) || /* WP variable */ ajaxurl;

	// Prototype mixin for all classes working with fields
	if ( $usof.mixins === undefined ) {
		$usof.mixins = {};
	}
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
					this.$fields[ name ] = $field;
					if ( $showIf.length > 0 ) {
						this.showIf[ name ] = $showIf[ 0 ].onclick() || [];
						// Writing dependencies
						var showIfVars = this.getShowIfVariables( this.showIf[ name ] );
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
				this.fields[ fieldName ].on( 'change', function( field ) {
					this.updateVisibility( field.name );
				}.bind( this ) );
				// Apply ShowIF conditions after field init
				this.updateVisibility( fieldName );
			}
		},
		/**
		 * Show / hide the field based on its showIf condition
		 */
		updateVisibility: function( fieldName, animate ) {
			animate = typeof animate !== 'undefined' ? animate : 1;
			var anmationDuration = ( animate ) ? 400 : 0;
			$.each( this.showIfDeps[ fieldName ], function( index, depFieldId ) {
				// Getting stored value to take animations into account as well
				var isShown = this.$fields[ depFieldId ].data( 'isShown' ),
					shouldBeShown = this.executeShowIf( this.showIf[ depFieldId ], this.getValue.bind( this ) );
				if ( isShown === undefined ) {
					isShown = ( this.$fields[ depFieldId ].css( 'display' ) != 'none' );
				}
				if ( shouldBeShown && ! isShown ) {
					this.fireFieldEvent( this.$fields[ depFieldId ], 'beforeShow' );
					this.$fields[ depFieldId ].stop( true, false ).slideDown( anmationDuration, function() {
						this.fireFieldEvent( this.$fields[ depFieldId ], 'afterShow' );
					}.bind( this ) );
					this.$fields[ depFieldId ].data( 'isShown', true );
				} else if ( ! shouldBeShown && isShown ) {
					this.fireFieldEvent( this.$fields[ depFieldId ], 'beforeHide' );
					this.$fields[ depFieldId ].stop( true, false ).slideUp( anmationDuration, function() {
						this.fireFieldEvent( this.$fields[ depFieldId ], 'afterHide' );
					}.bind( this ) );
					this.$fields[ depFieldId ].data( 'isShown', false );
				}
			}.bind( this ) );
		},
		/**
		 * Get all field names that affect the given 'show_if' condition
		 * @param {Array} condition
		 * @returns {Array}
		 */
		getShowIfVariables: function( condition ) {
			if ( ! $.isArray( condition ) || condition.length < 3 ) {
				return [];
			} else if ( $.inArray( condition[ 1 ].toLowerCase(), ['and', 'or'] ) != - 1 ) {
				// Complex or / and statement
				var vars = this.getShowIfVariables( condition[ 0 ] ),
					index = 2;
				while ( condition[ index ] !== undefined ) {
					vars = vars.concat( this.getShowIfVariables( condition[ index ] ) );
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
			var isRow = $container.hasClass( 'usof-form-row' ),
				hideShowEvent = (
					trigger == 'beforeShow'
					|| trigger == 'afterShow'
					|| trigger == 'beforeHide'
					|| trigger == 'afterHide'
				);
			if ( ! isRow ) {
				$container.find( '.usof-form-row' ).each( function( index, block ) {
					var $block = $( block ),
						isShown = $block.data( 'isShown' );
					if ( isShown === undefined ) {
						isShown = ( $block.css( 'display' ) != 'none' );
					}
					// The block is not actually shown or hidden in this case
					if ( hideShowEvent && ! isShown ) {
						return;
					}
					if ( $block.data( 'usofField' ) == undefined ) {
						return;
					}
					$block.data( 'usofField' ).trigger( trigger );
				}.bind( this ) );
			} else {
				$container.data( 'usofField' ).trigger( trigger );
			}
		},

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

		getValues: function( id ) {
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
			for ( var fieldId in values ) {
				if ( ! values.hasOwnProperty( fieldId ) || this.fields[ fieldId ] == undefined ) {
					continue;
				}
				this.setValue( fieldId, values[ fieldId ], quiet );
				if ( ! quiet ) {
					this.fields[ fieldId ].trigger( 'change', [values[ fieldId ]] );
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
					this.updateVisibility( fieldName, 0 );
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

	/**
	 * USOF Button Preview
	 */
	$usof.ButtonPreview = function( container ) {
		this.init( container );
	};
	$usof.ButtonPreview.prototype = {
		init: function( container ) {

			// Elements
			this.$container = $( container );
			this.$btn = this.$container.find( '.usof-btn' );
			this.$groupParams = this.$container.closest( '.usof-form-group-item' );
			this.$style = $( 'style:first', this.$groupParams );

			// Variables
			this.groupParams = this.$groupParams.data( 'usofGroupParams' );
			this.dependsOn = [
				'h1_font_family',
				'h2_font_family',
				'h3_font_family',
				'h4_font_family',
				'h5_font_family',
				'h6_font_family',
				'body_font_family',
			];

			// Apply style to button preview on dependant fields change
			for ( var fieldId in $usof.instance.fields ) {
				if ( ! $usof.instance.fields.hasOwnProperty( fieldId ) ) {
					continue;
				}
				if ( $.inArray( $usof.instance.fields[ fieldId ].name, this.dependsOn ) === - 1 ) {
					continue;
				}
				$usof.instance.fields[ fieldId ]
					.on( 'change', this.applyStyle.bind( this ) );
			}
			// Apply style to button preview on button's group params change
			for ( var fieldId in this.groupParams.fields ) {
				if ( ! this.groupParams.fields.hasOwnProperty( fieldId ) ) {
					continue;
				}
				this.groupParams.fields[ fieldId ]
					.on( 'change', this.applyStyle.bind( this ) );
			}

			// Apply style to button preview on the init
			this.applyStyle();
		},
		/**
		 * Get the color value.
		 * @param {String} key
		 * @return string.
		 */
		_getColorValue: function( key ) {
			if (
				this.groupParams instanceof $usof.GroupParams
				&& this.groupParams.fields[ key ] !== undefined
				&& this.groupParams.fields[ key ].type === 'color'
				&& this.groupParams.fields[ key ].hasOwnProperty( 'getColor' )
			) {
				return this.groupParams.fields[ key ].getColor();
			}
			return '';
		},
		/**
		 * Apply styles for form elements a preview
		 */
		applyStyle: function() {
			// Add unique class
			var classRandomPart = $usof.genUniqueId(),
				className = '.usof-btn_' + classRandomPart,
				style = {
					default: '',
					hover: '',
				};
			this.$btn.usMod( 'usof-btn', classRandomPart );

			// Font family
			var buttonFont = this.groupParams.getValue( 'font' ),
				fontFamily;
			if ( $.inArray( buttonFont, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'body'] ) !== - 1 ) {
				fontFamily = $usof.instance.getValue( buttonFont + '_font_family' ).split( '|' )[ 0 ];
			} else {
				fontFamily = buttonFont;
			}
			if ( fontFamily == 'none' ) {
				fontFamily = '';
			}
			if ( fontFamily ) {
				style.default += 'font-family: ' + fontFamily + ' !important;';
			}

			// Text style
			if ( $.inArray( 'italic', this.groupParams.getValue( 'text_style' ) ) !== - 1 ) {
				style.default += 'font-style: italic !important;';
			} else {
				style.default += 'font-style: normal !important;';
			}

			if ( $.inArray( 'uppercase', this.groupParams.getValue( 'text_style' ) ) !== - 1 ) {
				style.default += 'text-transform: uppercase !important;';
			} else {
				style.default += 'text-transform: none !important;';
			}

			// Font size
			style.default +='font-size:' + this.groupParams.getValue( 'font_size' ) + ' !important;';

			// Line height
			style.default += 'line-height:' + this.groupParams.getValue( 'line_height' ) + ' !important;';

			// Font weight
			style.default += 'font-weight:' + this.groupParams.getValue( 'font_weight' ) + ' !important;';

			// Height & Width
			style.default += 'padding:' + this.groupParams.getValue( 'height' ) + ' ' + this.groupParams.getValue( 'width' ) + ' !important;';

			// Corners radius
			style.default += 'border-radius:' + this.groupParams.getValue( 'border_radius' ) + ' !important;';

			// Letter spacing
			style.default += 'letter-spacing:' + this.groupParams.getValue( 'letter_spacing' ) + ' !important;';

			// Colors
			var colorBg = this._getColorValue( 'color_bg' ),
				colorBorder = this._getColorValue( 'color_border' ),
				colorBgHover = this._getColorValue( 'color_bg_hover' ),
				colorBorderHover = this._getColorValue( 'color_border_hover' ),
				colorShadow = this._getColorValue( 'color_shadow' ),
				colorShadowHover = this._getColorValue( 'color_shadow_hover' ),
				color;

			// Set default values if colors are empty
			if ( colorBg == '' ) {
				colorBg = 'transparent';
			}
			if ( colorBorder == '' ) {
				colorBorder = 'transparent';
			}
			if ( colorBgHover == '' ) {
				colorBgHover = 'transparent';
			}
			if ( colorBorderHover == '' ) {
				colorBorderHover = 'transparent';
			}
			if ( colorShadow == '' ) {
				colorShadow = 'rgba(0,0,0,0.2)';
			}
			if ( colorShadowHover == '' ) {
				colorShadowHover = 'rgba(0,0,0,0.2)';
			}

			style.default += 'background:' + colorBg + ' !important;';
			if ( colorBorder.indexOf( 'gradient' ) !== - 1 ) {
				style.default += 'border-image:' + colorBorder + ' 1 !important;';
			} else {
				style.default += 'border-color:' + colorBorder + ' !important;';
			}

			if ( this._getColorValue( 'color_text' ).indexOf( 'gradient' ) !== - 1 ) {
				color = $.usof_colpick.gradientParser( this._getColorValue( 'color_text' ) ).hex;
				style.default += 'color:' + color + ' !important;';
			} else {
				this.$btn.css('color', this._getColorValue('color_text'));
			}

			// Shadow
			style.default += 'box-shadow: 0 ' + parseFloat( this.groupParams.getValue( 'shadow' ) ) / 2 + 'em '
				+ this.groupParams.getValue( 'shadow' ) + ' ' + colorShadow + ' !important;';

			// Hover class
			this.$container.usMod( 'hov', this.groupParams.getValue( 'hover' ) );

			// Background;
			if ( this.groupParams.getValue( 'hover' ) == 'fade' ) {
				style.hover+= 'background:' + colorBgHover + ' !important;';
			} else if ( colorBgHover == 'transparent' ) {
				style.hover += 'background:' + colorBgHover + ' !important;';
			}

			// Shadow
			style.hover += 'box-shadow: 0 ' + parseFloat( this.groupParams.getValue( 'shadow_hover' ) ) / 2
				+ 'em ' + this.groupParams.getValue( 'shadow_hover' ) + ' ' + colorShadowHover + ' !important;';

			// Border color
			if ( colorBorderHover.indexOf( 'gradient' ) !== - 1 ) {
				style.hover += 'border-image:' + colorBorderHover + ' 1 !important;';
			} else {
				style.hover += 'border-color:' + colorBorderHover + ' !important;';
			}

			// Text color
			var colorHover;
			if ( this._getColorValue( 'color_text_hover' ).indexOf( 'gradient' ) !== - 1 ) {
				colorHover = ( $.usof_colpick.gradientParser( this._getColorValue( 'color_text_hover' ) ) ).hex;
			} else {
				colorHover = this._getColorValue( 'color_text_hover' );
			}
			style.hover += 'color:' + colorHover + ' !important;';

			var compiledStyle = className + '{%s}'.replace( '%s', style.default );

			// Border Width
			compiledStyle += className + ':before {border-width:' + this.groupParams.getValue( 'border_width' ) + ' !important;}';
			compiledStyle += className + ':hover{%s}'.replace( '%s', style.hover );

			// Extra layer for "Slide" hover type OR for gradient backgrounds (cause gradients don't support transition)
			if (
				this.groupParams.getValue( 'hover' ) == 'slide'
				|| (
					colorBorder.indexOf( 'gradient' ) !== - 1
					|| colorBgHover.indexOf( 'gradient' ) !== - 1
				)
			) {
				compiledStyle += className + ':after {background:'+ colorBgHover +'!important;}';
			}

			this.$style.text( compiledStyle );
		}
	};

	/**
	 * USOF Form Fields Preview
	 */
	$usof.FormElmsPreview = function( container ) {
		this.init( container );
	}
	$usof.FormElmsPreview.prototype = {
		init: function( container ) {
			// Elements
			this.$container = $( container );
			this.$group = this.$container.closest( '.usof-form-group-item' );
			this.$style = $( 'style:first', this.$group );
			this.$elms = $( '> *', this.$container );
			// Variables
			this.group = this.$group.data( 'usofGroupParams' ) || {};
			this.dependsOn = [
				'h1_font_family',
				'h2_font_family',
				'h3_font_family',
				'h4_font_family',
				'h5_font_family',
				'h6_font_family',
				'body_font_family',
			];
			// Watches all parameters
			if ( this.group instanceof $usof.GroupParams && this.group.hasOwnProperty( 'fields' ) ) {
				for ( var i in this.group.fields ) {
					this.group.fields[ i ].on( 'change', this.applyStyle.bind( this ) );
				}
			}
			// The apply styles
			this.applyStyle();
		},
		/**
		 * Get the color value.
		 * @param {String} key
		 * @return string.
		 */
		_getColorValue: function( key ) {
			if (
				this.group instanceof $usof.GroupParams
				&& this.group.fields[ key ] !== undefined
				&& this.group.fields[ key ].type === 'color'
				&& this.group.fields[ key ].hasOwnProperty( 'getColor' )
			) {
				return this.group.fields[ key ].getColor();
			}
			return '';
		},
		/**
		 * Apply styles for form elements a preview
		 */
		applyStyle: function() {
			var className = '.usof-input-preview-elm',
				style = {
					default: '',
					focus: ''
				};

			// Font family
			var buttonFont = this.group.getValue( 'font' ),
				fontFamily;
			if ( $.inArray( buttonFont, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'body'] ) !== - 1 ) {
				fontFamily = $usof.instance.getValue( buttonFont + '_font_family' ).split( '|' )[ 0 ];
			} else {
				fontFamily = buttonFont;
			}
			if ( fontFamily == 'none' ) {
				fontFamily = '';
			}
			style.default += 'font-family: ' + fontFamily + '!important;';

			// Font Size
			style.default += 'font-size:' + this.group.getValue( 'font_size' ) + '!important;';

			// Font Weight
			style.default += 'font-weight:' + this.group.getValue( 'font_weight' ) + '!important;';

			// Letter spacing
			style.default += 'letter-spacing:' + this.group.getValue( 'letter_spacing' ) + '!important;';

			// Height
			style.default += 'line-height:' + this.group.getValue( 'height' ) + '!important;';

			// Padding
			style.default += 'padding: 0 ' + this.group.getValue( 'padding' ) + '!important;';

			// Border radius
			style.default += 'border-radius:' + this.group.getValue( 'border_radius' ) + '!important;';

			// Border Width
			style.default += 'border-width:' + this.group.getValue( 'border_width' ) + '!important;';

			// Colors
			if ( this._getColorValue( 'color_bg' ) ) {
				style.default += 'background:' + this._getColorValue( 'color_bg' ) + '!important;';
			}
			if ( this._getColorValue( 'color_border' ) ) {
				style.default += 'border-color:' + this._getColorValue( 'color_border' ) + '!important;';
			}
			if ( this._getColorValue( 'color_text' ) ) {
				style.default += 'color:' + this._getColorValue( 'color_text' ) + '!important;';
			}

			// Colors on focus
			if ( this._getColorValue( 'color_bg_focus' ) ) {
				style.focus += 'background:' + this._getColorValue( 'color_bg_focus' ) + '!important;';
			}
			if ( this._getColorValue( 'color_border_focus' ) ) {
				style.focus += 'border-color:' + this._getColorValue( 'color_border_focus' ) + '!important;';
			}
			if ( this._getColorValue( 'color_text_focus' ) ) {
				style.focus += 'color:' + this._getColorValue( 'color_text_focus' ) + '!important;';
			}

			// Shadow
			if ( this._getColorValue( 'color_shadow' ) != '' ) {
				style.default += 'box-shadow:'
					+ this.group.getValue( 'shadow_offset_h' ) + ' '
					+ this.group.getValue( 'shadow_offset_v' ) + ' '
					+ this.group.getValue( 'shadow_blur' ) + ' '
					+ this.group.getValue( 'shadow_spread' ) + ' '
					+ this._getColorValue( 'color_shadow' ) + ' ';
				if ( $.inArray( '1', this.group.getValue( 'shadow_inset' ) ) !== - 1 ) {
					style.default += 'inset';
				}
				style.default += '!important;';
			}

			// Shadow on focus
			if ( this._getColorValue( 'color_shadow_focus' ) != '' || this._getColorValue( 'color_shadow' ) != '' ) {
				style.focus += 'box-shadow:'
					+ this.group.getValue( 'shadow_focus_offset_h' ) + ' '
					+ this.group.getValue( 'shadow_focus_offset_v' ) + ' '
					+ this.group.getValue( 'shadow_focus_blur' ) + ' '
					+ this.group.getValue( 'shadow_focus_spread' ) + ' ';

				if ( this._getColorValue( 'color_shadow_focus' ) != '' ) {
					style.focus += this._getColorValue( 'color_shadow_focus' ) + ' ';
				} else {
					style.focus += this._getColorValue( 'color_shadow' ) + ' ';
				}
				if ( $.inArray( '1', this.group.getValue( 'shadow_focus_inset' ) ) !== - 1 ) {
					style.focus += 'inset';
				}
				style.focus += '!important;';
			}

			var compiledStyle = className + '{%s}'.replace( '%s', style.default );
			compiledStyle += className + ':focus{%s}'.replace( '%s', style.focus );

			// Add styles for dropdown icon separately
			compiledStyle += '.usof-input-preview-select:after {';
			compiledStyle += 'font-size:' + this.group.getValue( 'font_size' ) + ';';
			compiledStyle += 'margin: 0 ' + this.group.getValue( 'padding' ) + ';';
			compiledStyle += 'color:' + this._getColorValue( 'color_text' ) + ';';
			compiledStyle += '}';

			this.$style.text( compiledStyle );
		},
	};

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

		for ( var fieldName in this.showIfDeps ) {
			this.updateVisibility( fieldName, 0 );
		}

		this.paramsTitle = ( this.$group.data( 'params_title' ) != undefined )
			? decodeURIComponent( this.$group.data( 'params_title' ) )
			: '';

		if ( this.paramsTitle != undefined && this.paramsTitle != '' ) {
			if ( this.isForButtons ) {
				this.$title = this.$container.find( '.usof-form-group-item-title .usof-btn-label' );
			} else {
				this.$title = this.$container.find( '.usof-form-group-item-title' );
			}
			for ( var fieldId in this.fields ) {
				if ( ! this.fields.hasOwnProperty( fieldId ) ) {
					continue;
				}
				this.fields[ fieldId ].on( 'change', function() {
					var paramsTitleResult = this.paramsTitle;
					for ( var fieldId in this.fields ) {
						if ( ! this.fields.hasOwnProperty( fieldId ) ) {
							continue;
						}
						if ( paramsTitleResult.indexOf( '{{' + fieldId + '}}' ) !== - 1 ) {
							paramsTitleResult = paramsTitleResult.replace( '{{' + fieldId + '}}', this.getValue( fieldId ) );
						}
					}
					this.$title.text( paramsTitleResult );
				}.bind( this ) );
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
					if ( this.group !== undefined ) {
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

	$.extend( $usof.GroupParams.prototype, $usof.mixins.Fieldset );

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
				$( document.body ).trigger( 'usof_mm_save' );
			}.bind( this ) );
		}

	};
	$.extend( USOF_Meta.prototype, $usof.mixins.Fieldset, {} );

	var USOF = function( container ) {
		if ( window.$usof === undefined ) {
			window.$usof = {};
		}
		$usof.instance = this;
		this.$container = $( container );
		this.$title = this.$container.find( '.usof-header-title h2' );

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
		if ( document.location.hash && document.location.hash.indexOf( '#!' ) == - 1 ) {
			this.openSection( document.location.hash.substring( 1 ) );
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

		this.$window = $( window );
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
			this.openSection( document.location.hash.substring( 1 ) );
		}.bind( this ) );

		$( window ).bind( 'keydown', function( event ) {
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
				var matches = document.cookie.match( /(?:^|; )usof_visited_new_sections=([^;]*)/ ),
					cookieValue = matches ? decodeURIComponent( matches[ 1 ] ) : '',
					visitedNewSections = ( cookieValue == '' ) ? [] : cookieValue.split( ',' );
				if ( visitedNewSections.indexOf( sectionId ) == - 1 ) {
					visitedNewSections.push( sectionId );
					document.cookie = 'usof_visited_new_sections=' + visitedNewSections.join( ',' )
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
		new USOF( '.usof-container' );

		$.each( $( '.usof-container.for_meta' ), function( index, item ) {
			new USOF_Meta( item );
		} );

		$( document.body ).off( 'usof_mm_load' ).on( 'usof_mm_load', function() {
			$.each( $( '.us-mm-settings' ), function( index, item ) {
				new USOF_Meta( item );
			} );
		} );
	} );


}( jQuery );

/**
 * Auto optimize assets
 */
;( function( $, undefined ) {
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
			 * Button click
			 * @param JQueryEventObject e
			 * @return void
			 */
			clickButton: function( e ) {
				if ( ! this.$button.hasClass( 'loading' ) ) {
					this.$button.addClass( 'loading' );
					this._request.call( this, 'request' );
					this._events.clearMessage.call( this );
				}
			},
			/**
			 * Clear message
			 * @param JQueryEventObject e
			 * @return void
			 */
			clearMessage: function( e ) {
				this.$message.addClass( 'hidden' ).html( '' );
			}
		},
		/**
		 * Asset Use Requests
		 * @param string type Request type
		 * @return void
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
		 * @param text html
		 * @return void
		 */
		showMessage: function( html ) {
			this.$message.html( html ).removeClass( 'hidden' );
		}
	};
	// Init AutoOptimizeAssets
	$( new AutoOptimizeAssets );
} )( jQuery );
