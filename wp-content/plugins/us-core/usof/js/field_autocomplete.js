/**
 * USOF FIeld: Autocomplete
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

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
				} );
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
				 *
				 * @param object target $usof.dragDrop
				 * @param {Event} e
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
			 * Remove selected.
			 *
			 * @param {Event} e
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
			 * Delayed search to avoid premature queries.
			 *
			 * @param {Event} e
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
			 * Filtering results when entering characters in the search field.
			 *
			 * @param {Event} e
			 */
			search: function( e ) {
				var $input = $( e.currentTarget ),
					value = ( $.trim( $input.val() ).toLowerCase() ).replace( /\=|\"|\s/, '' ),
					$items = $( '[data-value]', this.$list ),
					$groups = $( '[data-group]', this.$list ),
					/**
					 * Filters parameters by search text.
					 *
					 * @param {jQuery} $items
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
			 * Selected option.
			 *
			 * @param {Event} e
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
			 * When scrolling a sheet, load the parameters.
			 *
			 * @param {Event} e
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
			 * Input event handler for Search.
			 *
			 * @param {Event} e
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
			 * Show/Hide list.
			 *
			 * @param {Event} e
			 */
			toggleList: function( e ) {
				var isFocus = ( e.type === 'focus' ),
					pid = setTimeout( function() {
						this.$toggle.toggleClass( 'show', isFocus );
						clearTimeout( pid );
					}.bind( this ), ( isFocus ? 0 : 200 /* The delay for the blur event is necessary for the selection script to work out */ ) );
				// If there is no search text, then all parameters are show.
				if ( !$.trim( this.$search.val() ) ) {
					$( '[data-value].hidden', this.$list )
						.removeClass( 'hidden' );
				}
			}
		},
		/**
		 * Load and search option.
		 *
		 * @param function callback
		 */
		_ajax: function( callback ) {
			if ( this.loaded ) {
				return;
			}

			var query_args = this._settings.ajax_query_args;
			// If the handler is not installed, then cancel the request.
			if ( ( ! query_args.hasOwnProperty( 'action' ) || query_args.action === 'unknown' ) && $.isFunction( callback ) ) {
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
			 * Add option to sheet.
			 *
			 * @param {node} $el
			 * @param {string} name
			 * @param {string} value
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
		 *
		 * @param string text The message text
		 */
		_showMessage: function( text ) {
			this.$list.addClass( 'hidden' );
			this.$message
				.text( text )
				.removeClass( 'hidden' );
		},
		/**
		 * Clear this message.
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
		 * Removing a parameter from the result.
		 *
		 * @param string key The key
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
			return ( this.$value instanceof $ )
				? this.$value.val()
				: '';
		},
		/**
		 * Set values.
		 *
		 * @param string value The value
		 * @param boolean quiet
		 */
		setValue: function( value, quiet ) {
			this.$value.val( value );
			this._initValues.call( this );
			if ( !quiet ) {
				this.trigger( 'change', [value] );
			}
		}
	};
}( jQuery );
