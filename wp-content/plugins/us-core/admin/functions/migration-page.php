<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * US Migration page
 */

function us_add_migration_page() {
	add_submenu_page(
		NULL, 'Update Website Content', 'Update Website Content', 'manage_options', 'us-update-content', 'us_migration_page'
	);
}

if ( wp_doing_ajax() ) {

	// AJAX request handler for migration
	add_action( 'wp_ajax_us_migrate', 'us_ajax_migrate', 1 );
	if ( ! function_exists( 'us_ajax_migrate' ) ) {
		function us_ajax_migrate() {
			global $us_migration;
			if ( ! check_ajax_referer( 'us_ajax_migrate', '_nonce', FALSE ) ) {
				wp_send_json_error(
					array(
						'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
					)
				);
				wp_die();
			}

			$response = $us_migration->perform_migration_step();
			if ( isset( $response['progress'] ) AND $response['progress'] >= 100 ) {
				if ( ! defined( 'US_DEV' ) ) {
					$response = array();
				}
				// Stop miration
				$response = array_merge(
					$response, array(
						'message' => $us_migration->migration_completed_message(),
						'progress' => 100,
						'completed' => TRUE,
					)
				);
			}

			wp_send_json_success( $response );
		}
	}
}

// Migration Launch Page
function us_migration_page() {
	?>
	<!-- Begin page migration -->
	<div class="about-wrap us-migration-page">
		<h1><?php echo sprintf( 'Your website\'s content is being updated to be compatible with %s.', US_THEMENAME . ' ' . US_THEMEVERSION ); ?></h1>
		<div class="us-progress">
			<div class="us-progress-bar animated" style="width: 1%;">1%</div>
		</div>
		<div class="us-migration-message">
			<h3>The update time depends on the amount of website's pages.<br>Do not close this page to not interrupt the process.</h3>
		</div>
		<script type="text/javascript">
			;( function( $, undefined ) {
				// Variables
				var $container = $( '.us-migration-page:first' ),
					$usProgressBar = $container.find( '.us-progress-bar' ),
					$usMessage = $container.find( '.us-migration-message' ),
					us_ajax_migrate = function() {
						$.ajax( {
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'us_migrate',
								_nonce: '<?php echo wp_create_nonce( 'us_ajax_migrate' ) ?>',
							},
							dataType: 'json',
							success: function( res ) {
								if ( res.data.progress ) {
									var progress = parseInt( res.data.progress );
									if ( progress < 1 ) {
										progress = 1;
									}
									$usProgressBar
										.css( 'width', progress + '%' )
										.text( progress + '%' )
								}
								if ( res.data.message ) {
									$usMessage.html( res.data.message );
								}
								// Next migration step
								if ( res.data.progress < 100 ) {
									us_ajax_migrate();
								}
								if ( res.data.completed ) {
									$usProgressBar.removeClass( 'animated' );

									var pid = setTimeout( function() {
										clearTimeout( pid );
									}, 500 );
								}
							},
							error: function( err ) {
								console.error( err );
							}
						} );
					};

				$( document ).ready(function() {

					// Initialize the start of migration
					us_ajax_migrate();
				});

			} )( window.jQuery );
		</script>
	</div>
	<!-- End page migration -->
	<?php
}
