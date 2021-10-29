/**
 * WordPress Comments Form
 *
 */
! function( $ ) {
	"use strict";

	$us.CommnentsForm = function( container, options ) {
		this.init( container, options );
	};

	$us.CommnentsForm.prototype = {
		init: function( container, options ) {
			this.$container = $( container );
			this.$form = this.$container.find( 'form.comment-form' );
			if ( ! this.$form.length ) {
				return;
			}
			this.$jsonContainer = this.$container.find( '.us-comments-json' );
			if ( ! this.$jsonContainer.length ) {
				return;
			}
			this.jsonData = this.$jsonContainer[ 0 ].onclick() || {};
			this.$jsonContainer.remove();

			this.$fields = {
				content: {
					field: this.$form.find( 'textarea' ),
					msg: this.jsonData.no_content_msg || 'Please enter a Message'
				},
				name: {
					field: this.$form.find( '.for_text input[type="text"]' ),
					msg: this.jsonData.no_name_msg || 'Please enter your Name'
				},
				email: {
					field: this.$form.find( '.for_email input[type="email"]' ),
					msg: this.jsonData.no_email_msg || 'Please enter a valid email address.'
				}
			};

			this._events = {
				formSubmit: this.formSubmit.bind( this )
			};

			this.$form.on( 'submit', this._events.formSubmit );
		},
		formSubmit: function( event ) {
			// Clear errors
			this.$form.find( '.w-form-row.check_wrong' ).removeClass( 'check_wrong' );
			this.$form.find( '.w-form-state' ).html( '' );

			// Do not send empty fields
			for ( var i in this.$fields ) {
				if ( this.$fields[ i ].field.length == 0 ) {
					continue;
				}
				if ( this.$fields[ i ].field.val() == '' && this.$fields[ i ].field.attr( 'data-required' ) ) {
					this.$fields[ i ].field.closest( '.w-form-row' ).toggleClass( 'check_wrong' );
					this.$fields[ i ].field.closest( '.w-form-row' ).find( '.w-form-row-state' ).html( this.$fields[ i ].msg );
					event.preventDefault();
				}
			}
		}
	};

	$.fn.CommnentsForm = function( options ) {
		return this.each( function() {
			$( this ).data( 'CommnentsForm', new $us.CommnentsForm( this, options ) );
		} );
	};

	$( function() {
		$( '.w-post-elm.post_comments.layout_comments_template' ).CommnentsForm();
		$( '.l-section.for_comments' ).CommnentsForm();
	} );
}( jQuery );