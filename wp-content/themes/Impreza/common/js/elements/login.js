/**
 * UpSolution Login Widget: widget_us_login
 *
 */
! function( $ ) {
	"use strict";

	$us.WLogin = function( container, options ) {
		this.init( container, options );
	};

	$us.WLogin.prototype = {
		init: function( container, options ) {
			this.$container = $( container );

			// Prevent double init
			if ( this.$container.data( 'loginInit' ) == 1 ) {
				return;
			}
			this.$container.data( 'loginInit', 1 );

			this.$submitBtn = this.$container.find( '.w-btn' );
			this.$username = this.$container.find( '.for_text input[type="text"]' );
			this.$password = this.$container.find( '.for_password input[type="password"]' );
			this.$preloader = this.$container.siblings( '.g-preloader' );
			this.$nonceVal = this.$container.find( '#us_login_nonce' ).val();
			this.$resultField = this.$container.find( '.w-form-message' );

			this.$jsonContainer = this.$container.find( '.w-form-json' );
			this.jsonData = this.$jsonContainer[ 0 ].onclick() || {};
			this.$jsonContainer.remove();

			this.ajaxUrl = this.jsonData.ajaxurl || '';
			this.loginRedirect = this.jsonData.login_redirect || '';
			this.logoutRedirect = this.jsonData.logout_redirect || window.location.href;
			this.use_ajax = !! this.jsonData.use_ajax;

			this._events = {
				formSubmit: this.formSubmit.bind( this )
			};

			this.$container.on( 'submit', this._events.formSubmit );

			if ( this.use_ajax ) {
				// Reload profile content to pass caching
				$.ajax( {
					type: 'post',
					url: this.ajaxUrl,
					data: {
						action: 'us_ajax_user_info',
						logout_redirect: this.logoutRedirect
					},
					success: function( result ) {
						if ( result.success ) {
							this.$container.closest( '.w-login' ).html( result.data );
						} else {
							this.$container.removeClass( 'hidden' );
						}
						this.$preloader.addClass( 'hidden' );
					}.bind( this )
				} );
			}
		},
		formSubmit: function( event ) {
			event.preventDefault();

			// Prevent double-sending
			if ( this.$submitBtn.hasClass( 'loading' ) ) {
				return;
			}

			// Clear errors
			this.$resultField.usMod( 'type', false ).html( '' );
			this.$container.find( '.w-form-row.check_wrong' ).removeClass( 'check_wrong' );
			this.$container.find( '.w-form-state' ).html( '' );

			// Prevent sending data with empty username
			if ( this.$container.find( '.for_text input[type="text"]' ).val() == '' ) {
				this.$username.closest( '.w-form-row' ).toggleClass( 'check_wrong' );
				return;
			}

			this.$submitBtn.addClass( 'loading' );

			$.ajax( {
				type: 'post',
				url: this.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'us_ajax_login',
					username: this.$username.val(),
					password: this.$password.val(),
					us_login_nonce: this.$nonceVal
				},
				success: function( result ) {
					if ( result.success ) {
						document.location.href = this.loginRedirect;
					} else {
						if ( result.data.code == 'invalid_username' ) {
							var $rowLog = this.$username.closest( '.w-form-row' );
							$rowLog.toggleClass( 'check_wrong' );
							$rowLog.find( '.w-form-row-state' ).html( result.data.message ? result.data.message : '' );
						} else if ( result.data.code == 'incorrect_password' || result.data.code == 'empty_password' ) {
							var $rowPwd = this.$password.closest( '.w-form-row' );
							$rowPwd.toggleClass( 'check_wrong' );
							$rowPwd.find( '.w-form-row-state' ).html( result.data.message ? result.data.message : '' );
						} else {
							this.$resultField.usMod( 'type', 'error' ).html( result.data.message );
						}
						this.$submitBtn.removeClass( 'loading' );
					}
				}.bind( this ),
			} );
		}
	};

	$.fn.wUsLogin = function( options ) {
		return this.each( function() {
			$( this ).data( 'wUsLogin', new $us.WLogin( this, options ) );
		} );
	};

	$( function() {
		$( '.w-login > .w-form' ).wUsLogin();
	} );
}( jQuery );