<?php

class pb_backupbuddy_dashboard extends pb_backupbuddy_dashboardcore {


	/*	stats()
	 *	
	 *	Displays (echos out) an overview of stats into the WordPress Dashboard.
	 *	
	 *	@return		null
	 */
	function stats() {

		pb_backupbuddy::load_script( 'underscore' );

		$getOverview = backupbuddy_api::getOverview();
		
		if ( is_network_admin() ) {
			$backup_url = network_admin_url( 'admin.php' );
		} else {
			$backup_url = admin_url( 'admin.php' );
		}
		$backup_url .= '?page=pb_backupbuddy_backup';
		
		if ( is_network_admin() ) {
			$stashlive_url = network_admin_url( 'admin.php' );
		} else {
			$stashlive_url = admin_url( 'admin.php' );
		}
		$stashlive_url .= '?page=pb_backupbuddy_live';
		
		// Red-Green status for editsSinceLastBackup
		if ( $getOverview['editsSinceLastBackup'] == 0 )
			$status = 'green';
		else
			$status = 'red';
		

		// Format file archiveSize to readable format
		if ( isset( $getOverview['lastBackupStats']['archiveSize'] ) && ( is_numeric( $getOverview['lastBackupStats']['archiveSize'] ) ) ) {
			$file_size = $getOverview['lastBackupStats']['archiveSize'];

			if ( $file_size >= 1073741824 )
				$archiveSize = round( $file_size / 1024 / 1024 / 1024 , 2 ) . ' GB';

			elseif ( $file_size >= 1048576 )
				$archiveSize = round( $file_size / 1024 / 1024 , 1 ) . ' MB';

			elseif( $file_size >= 1024 )
				$archiveSize = round( $file_size / 1024 , 0 ) . ' KB';

			else
				$archiveSize = $file_size . ' bytes';
		} else {
			$archiveSize = 'Unknown';
		}

		// Format timestamp
		if ( isset( $getOverview['lastBackupStats']['finish'] ) ) {
			$time = pb_backupbuddy::$format->localize_time( $getOverview['lastBackupStats']['finish'] );
			$time_nice = date("M j - g:i A", $time);
		} else {
			$time_nice = 'Unknown';
		}
		
		// Format Type
		if ( isset( $getOverview['lastBackupStats']['type'] ) ) {
			if ( $getOverview['lastBackupStats']['type'] == 'full' )
				$backup_type = 'Full';
			elseif ( $getOverview['lastBackupStats']['type'] == 'db' )
				$backup_type = 'Database';
			else
				$backup_type = $getOverview['lastBackupStats']['type'];
		} else {
			$backup_type = 'Unknown';
		}
		
		// Build widget markup
		ob_start();
		?>
		
		
		<?php if ( false !== backupbuddy_live::getLiveID() ) : ?>
		<div class="tabs clearfix">
			<button class="tab-toggle stash-live selected">Stash Live</button>
			<button class="tab-toggle traditional">Traditional</button>
		</div>
		<div class="stash-live-wrapper"><div class="spinner is-active"></div></div>
		<script type="text/template" class="backupbuddy-stash-live-dashboard-widget-tmpl">
			<div class="backupbuddy-live-stats-currently">
				<span class="backupbuddy-pulsing-orb"></span>
				<div class="backupbuddy-currently-message">
					<span class="backupbuddy-inline-label"><?php _e( 'Currently', 'it-l10n-backupbuddy' ); ?></span>: {{ stats.current_function_pretty }}
				</div>
			</div>
			<div class="backupbuddy-live-stats-overview">
				<h3><?php _e( 'BackupBuddy Stash Live created new zip files for you as of', 'it-l10n-backupbuddy' ); ?>:</h3>
				<div class="backupbuddy-stats-time-ago">{{ stats.last_remote_snapshot_ago }}</div>

				<div class="backupbuddy-stats-overview-manage-live backup-now">
					<a href="<?php echo esc_url( $stashlive_url ); ?>" class="backupbuddy-live-button secondary"><?php _e( 'Manage Stash Live', 'it-l10n-backupbuddy' ); ?></a>
				</div>
			</div>
		</script>
		<?php endif; ?>

		<div class="traditional-backup-wrapper hidden">
			<div class="edits-since-wrapper">
				<p class="edits-since <?php echo $status; ?>">
					<?php echo $getOverview['editsSinceLastBackup']; ?>
				</p>
				<h4 class="number-heading">Edits since<br>last Backup</h4>
			</div>
			<?php if ( isset( $getOverview['lastBackupStats']['finish'] ) ) { // only show if a last backup exists. ?>
				<div class="info-group">
					<h3>Latest Backup</h3>
					<ul class="backup-list">
						<li>
							<div class="list-wrapper">
								<div class="list-title">
									<?php if ( isset( $getOverview['lastBackupStats']['archiveFile'] ) && file_exists( backupbuddy_core::getBackupDirectory() . $getOverview['lastBackupStats']['archiveFile'] ) ) { ?>
										<a href="<?php if ( isset( $getOverview['lastBackupStats']['archiveURL'] ) ) { echo $getOverview['lastBackupStats']['archiveURL']; } ?>"><?php _e( 'Download', 'it-l10n-backupbuddy' ); ?></a>
									<?php } else { ?>
										<i>Stored offsite or deleted</i>
									<?php } ?>
								</div>
								<div class="list-description">
									<div class="backup-type description-item">
										<span>Type</span><br>
										<?php echo $backup_type; ?>
									</div>
									<div class="backup-size description-item">
										<span>Size</span><br>
										<?php echo $archiveSize; ?>
									</div>
									<div class="backup-time description-item">
										<span>Time</span><br>
										<?php echo $time_nice; ?>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</div>
			<?php } ?>
	
			<div class="backup-now">
				<a href="<?php echo $backup_url; ?>"><?php _e( 'Backup Now', 'it-l10n-backupbuddy' ); ?></a>
			</div>
		</div>
		
		<?php if ( false !== backupbuddy_live::getLiveID() ) : ?>
			<script>
					function backupbuddy_live_dashboard_stats( stats ) {
						_.templateSettings.variable    = 'stats';
						_.templateSettings.evaluate    = /<#([\s\S]+?)#>/g;
						_.templateSettings.interpolate = /\{\{\{([\s\S]+?)\}\}\}/g;
						_.templateSettings.escape      = /\{\{([^\}]+?)\}\}(?!\})/g;
						var liveTemplate = _.template( jQuery( '#pb_backupbuddy_stats .backupbuddy-stash-live-dashboard-widget-tmpl' ).html() );
						jQuery('#pb_backupbuddy_stats .stash-live-wrapper' ).html( liveTemplate( stats ) );
					}
					
					jQuery(document).ready( function() {
						backupbuddy_live_dashboard_stats( jQuery.parseJSON( '<?php echo json_encode( backupbuddy_api::getLiveStats() ); ?>' ) ); // Initial stats to prevent loading from showing.
					});
			</script>
			<?php require_once( pb_backupbuddy::plugin_path() . '/destinations/live/_statsPoll.php' ); ?>
		<?php endif; ?>
		
		<script>
			jQuery(document).ready( function() {
				// UI for toggling the tabs
				jQuery( '#pb_backupbuddy_stats .tab-toggle' ).on( 'click', function( e ) {
					e.preventDefault();
					if ( jQuery(this).hasClass( 'stash-live' ) ) {
						jQuery(this).addClass('selected').siblings().removeClass('selected');
						jQuery( '#pb_backupbuddy_stats .stash-live-wrapper').removeClass('hidden');
						jQuery( '#pb_backupbuddy_stats .traditional-backup-wrapper').addClass('hidden');
					} else if ( jQuery(this).hasClass( 'traditional' ) ) {
						jQuery(this).addClass('selected').siblings().removeClass('selected');
						jQuery( '#pb_backupbuddy_stats .traditional-backup-wrapper').removeClass('hidden');
						jQuery( '#pb_backupbuddy_stats .stash-live-wrapper').addClass('hidden');
					}
				});
				
				<?php if ( false === backupbuddy_live::getLiveID() ) : ?>
					jQuery( '#pb_backupbuddy_stats .traditional-backup-wrapper').removeClass('hidden');
				<?php endif; ?>
			});
		</script>
		
		<?php
		ob_end_flush();
	}


}
?>