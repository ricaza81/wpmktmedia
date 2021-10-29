/**
 * UpSolution Element: Grid Order
 */
;( function( $, undefined ) {
	"use strict";
	/**
	 * @class WGridOrder
	 * @param {string} container The container.
	 */
	$us.WGridOrder = function ( container ) {
		this.init( container );
	};

	// Export API
	$.extend( $us.WGridOrder.prototype, $us.mixins.Events, {
		init: function( container ) {
			// Elements
			this.$container = $( container );
			this.$select = $( 'select', this.$container );
			this.$grid = $( '.w-grid[data-filterable="true"]:first', $us.$canvas.find( '.l-main' ) );

			// Variables
			this.name = this.$select.attr( 'name' ) || /* Default */ 'order';

			// Events
			this.$container
				.on( 'change', 'select', this._events.changeSelect.bind( this ) );

			// Set class to define the grid is used by Grid Order.
			this.$grid.addClass( 'used_by_grid_order' );
		},
		/**
		 * Event handlers
		 * @private
		 */
		_events: {
			/**
			 * Changes to the select field.
			 *
			 * @param {Event} e
			 */
			changeSelect: function() {
				var value = this.$select.val() || '',
					matches = ( location.href.match( /page(=|\/)(\d+)(\/?)/ ) || [] ),
					page = parseInt( matches[2] || 1 /* Default first page */ );

				this.URLSearchValue( value );
				// Send an event and force to update the grid with new params
				this.triggerGrid( 'us_grid.updateOrderBy', [ value, page, this ] );
			}
		},
		/**
		 * Raises a private event in the grid.
		 *
		 * @param {string} eventType
		 * @param mixed extraParameters
		 */
		triggerGrid: function ( eventType, extraParameters ) {
			$us.debounce( function() { $us.$body.trigger( eventType, extraParameters ) }, 10 )();
		},
		/**
		 * Set search value in the url.
		 *
		 * @param {string} value The query value.
		 * TODO: Write functions for working from URL parameters, as this is used in many places.
		 */
		URLSearchValue: function( value ) {
			var orderby_search = '',
				url = location.origin + location.pathname + ( location.pathname.slice( -1 ) != '/' ? '/' : '' ),
				search = location.search
					.replace( new RegExp('[?&]' + this.name + '=[^&#]*(#.*)?$'), '$1' )
					.replace( new RegExp('([?&])' + this.name + '=[^&]*&'), '$1' );

			if ( search && search.substr( 0, 1 ) === '?' ) {
				search = search.slice( 1 );
			}
			if ( value ) {
				orderby_search += this.name + '=' + value;
			}
			if ( orderby_search && search ) {
				orderby_search += '&';
			}
			orderby_search += search;
			history.replaceState( document.title, document.title, url + ( orderby_search ? '?' + orderby_search : '' ) );
		}
	});

	// Add to jQuery
	$.fn.wGridOrder = function ( options ) {
		return this.each( function () {
			$( this ).data( 'wGridOrder', new $us.WGridOrder( this ) );
		} );
	};

	// Init
	$( function() {
		$( '.w-order', $us.$canvas ).wGridOrder();
	} );
})( jQuery );
