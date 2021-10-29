/**
 * USOF Field: Backup
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

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
}( jQuery );
