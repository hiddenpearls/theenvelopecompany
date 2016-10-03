<?php
/* BackupBuddy Stash Live Configuration. Shown for Settings screen.
 *
 * @author Dustin Bolton
 * @since 7.0
 *
 * Pre-populated variables coming into this script:
 *		$destination_id
 *		$destination_settings
 *		$mode
 */


$archive_types = array(
	'db' => __( 'Database Backups', 'it-l10n-backupbuddy' ),
	'full' => __( 'Full Backups', 'it-l10n-backupbuddy' ),
	'plugins' => __( 'Plugins Backups', 'it-l10n-backupbuddy' ),
	'themes' => __( 'Themes Backups', 'it-l10n-backupbuddy' ),
);

$archive_periods = array(
	'daily',
	'weekly',
	'monthly',
	'yearly',
);

// Handle saving archive limits.
if ( 'settings' == pb_backupbuddy::_POST( 'pb_backupbuddy_' ) ) {
	
	$save = true;
	foreach( $archive_types as $archive_type => $archive_type_name ) {
		foreach( $archive_periods as $archive_period ) {
			if ( '' == pb_backupbuddy::_POST( 'pb_backupbuddy_limit_' . $archive_type . '_' . $archive_period ) ) { // No limit.
				$archive_value = '';
			} else { // Numerical limit (if not numerical, error).
				$archive_value = (int)pb_backupbuddy::_POST( 'pb_backupbuddy_limit_' . $archive_type . '_' . $archive_period );
				if ( ! is_numeric( $archive_value ) ) {
					pb_backupbuddy::alert( 'Invalid non-numeric value for archive limit `' . htmlentities( $archive_value ) . '` for type `' . $archive_type_name . '`.' );
					$save = false;
					break 2;
				}
			}
			pb_backupbuddy::$options['remote_destinations'][$destination_id]['limit_' . $archive_type . '_' . $archive_period ] = $archive_value;
		}
	}
	
	if ( true === $save ) {
		pb_backupbuddy::save();
		$destination_settings = pb_backupbuddy::$options['remote_destinations'][$destination_id];
	}
}


$archive_limits_html = '<tr class="">
	<th scope="row" class="" style="">' . __( 'Snapshot Archive Limits', 'it-l10n-backupbuddy' ) . pb_backupbuddy::tip( 'Leave empty for unlimited backups of a type or 0 (zero) to limit to none. WARNING: Use caution when entering 0 (zero) for a type of backup as it could result in the loss of many backups.', '', $echo_tip = false ) . '</th>
	<td class="" style="padding: 0;">

		<table>
			
			<tr>
				<td>
					&nbsp;
				</td>
				<td>
					' . __( 'Daily', 'it-l10n-backupbuddy' ) . '
				</td>
				<td>
					' . __( 'Weekly', 'it-l10n-backupbuddy' ) . '
				</td>
				<td>
					' . __( 'Monthly', 'it-l10n-backupbuddy' ) . '
				</td>
				<td>
					' . __( 'Yearly', 'it-l10n-backupbuddy' ) . '
				</td>
			</tr>
			
			';

foreach( $archive_types as $archive_type => $archive_type_name ) {
	$archive_limits_html .= '<tr>';
	$archive_limits_html .= '<td class="label">' . $archive_type_name . '</td>';
	foreach( $archive_periods as $archive_period ) {
		$settings_name = 'limit_' . $archive_type . '_' . $archive_period;
		$archive_limits_html .= '<td><input size="4" type="text" class="small" name="pb_backupbuddy_' . 'limit_' . $archive_type . '_' . $archive_period . '" value="' . $destination_settings[ $settings_name ] . '" /></td>';
	}
	$archive_limits_html .= '</tr>';
}

$archive_limits_html .= '<tr>
			<td colspan="5">
				<span class="description">Set blank to keep unlimited backups of a type or 0 (zero) to limit to none.</span>
			</td>
			</tr>
			
		</table>

	</td>
</tr>';


//echo 'mode:' . $mode;

$default_name = NULL;
if ( 'add' == $mode ) {
	$default_name = 'BackupBuddy Stash Live';
}
$settings_form->add_setting( array(
	'type'		=>		'hidden',
	'name'		=>		'title',
	'title'		=>		__( 'Destination name', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( 'Name of the new destination to create. This is for your convenience only.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|string[1-45]',
	'default'	=>		$default_name,
) );


/*
$settings_form->add_setting( array(
	'type'		=>		'checkbox',
	'name'		=>		'scan_files',
	'options'	=>		array( 'unchecked' => '0', 'checked' => '1' ),
	'title'		=>		__( 'Scan files for changes', 'it-l10n-backupbuddy' ) . '*',
) );
*/


$intervalArray = array();
$schedule_intervals = wp_get_schedules();
foreach( $schedule_intervals as $interval_tag => $schedule_interval ) {
	if ( $schedule_interval['interval'] < 60*60 ) { // Omit anything under 1 hour.
		continue;
	}
	$intervalArray[ $schedule_interval['interval'] ] = array( $interval_tag, $schedule_interval['display'] );
}
ksort( $intervalArray );
$intervalArray = array_reverse( $intervalArray );
$intervals = array();
foreach( $intervalArray as $interval ) {
	$intervals[ $interval[0] ] = $interval[1];
}
unset( $intervalArray );


$settings_form->add_setting( array(
	'type'		=>		'select',
	'name'		=>		'periodic_process_period',
	'title'		=>		__( 'Full Scan Interval', 'it-l10n-backupbuddy' ),
	'options'	=>		$intervals,
	'tip'		=>		__('[Default: Twice Daily] - How often the local periodic site scan should run.  This process scans and uploads the current snapshot of the database and any local file changes found. It also audits and verifies remotely stored files. If a remote snapshot is due it will also be triggered. This period must be equal to or more often than the remote Snapshot period.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required',
) );
$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'email',
	'title'		=>		__('Notification email', 'it-l10n-backupbuddy' ),
	'tip'		=>		__('Email address to send notifications to upon successful Snapshot creation. If left blank your iThemes Member Account email address will be used.', 'it-l10n-backupbuddy' ),
	'css'		=>		'width: 300px;',
) );
$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'no_new_snapshots_error_days',
	'title'		=>		__('Send notification after period of no Snapshots', 'it-l10n-backupbuddy' ),
	'tip'		=>		__('[Example: 30] - Maximum number of days (set to 0 to disable) that may pass with no new Snapshots created before sending an error notifcation email.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|string[0-99999]',
	'css'		=>		'width: 50px;',
	'after'		=>		' days',
	'rules'		=>		'int',
) );
$settings_form->add_setting( array(
	'type'		=>		'checkbox',
	'name'		=>		'send_snapshot_notification',
	'options'	=>		array( 'unchecked' => '0', 'checked' => '1' ),
	'title'		=>		__( 'Snapshot success email', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Default: enabled] - When enabled, an email will be sent to the administrator email address for this site for each snapshot successfully created.', 'it-l10n-backupbuddy' ),
	'after'		=>		'&nbsp;' . __( 'Yes, send email.' ),
	'css'		=>		'',
	'rules'		=>		'',
	'row_class'	=>		'',
) );
$settings_form->add_setting( array(
	'type'		=>		'checkbox',
	'name'		=>		'show_admin_bar',
	'options'	=>		array( 'unchecked' => '0', 'checked' => '1' ),
	'title'		=>		__( 'Show admin bar', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Default: enabled] - When enabled, a brief Live status will be added to the admin bar at the top of the WordPress dashboard for admins.', 'it-l10n-backupbuddy' ),
	'after'		=>		'&nbsp;' . __( 'Yes, show stats in bar.' ),
	'css'		=>		'',
	'rules'		=>		'',
	'row_class'	=>		'',
) );



$settings_form->add_setting( array(
	'type'		=>		'html',
	'html'		=>		$archive_limits_html,
) );



$settings_form->add_setting( array(
	'type'		=>		'textarea',
	'name'		=>		'file_excludes',
	'title'		=>		__( 'Additional File Exclusions*', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( 'Additional files/directories to excludes BEYOND BackupBuddy\'s global default file exclusions.', 'it-l10n-backupbuddy' ),
	'css'		=>		'width: 100%;',
	'after'		=>		'<span class="description">* ' . __( 'Exclusions beyond global BackupBuddy settings.', 'it-l10n-backupbuddy' ) . '</span>',
) );
global $wpdb;
$settings_form->add_setting( array(
	'type'		=>		'textarea',
	'name'		=>		'table_excludes',
	'title'		=>		__( 'Additional Table Exclusions*', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( 'Additional database tables to excludes BEYOND BackupBuddy\'s global default database table exclusions. You may use {prefix} in place of the current database prefix.', 'it-l10n-backupbuddy' ) . ' Current prefix: `' . $wpdb->prefix . '`.',
	'css'		=>		'width: 100%;',
	'after'		=>		'<span class="description">* ' . __( 'Exclusions beyond global BackupBuddy settings.', 'it-l10n-backupbuddy' ) . '</span>',
) );


$settings_form->add_setting( array(
		'type'		=>		'title',
		'name'		=>		'advanced_begin',
		'title'		=>		'<span class="dashicons dashicons-arrow-right"></span> ' . __( 'Advanced Options', 'it-l10n-backupbuddy' ),
		'row_class'	=>		'advanced-toggle-title',
	) );



$settings_form->add_setting( array(
	'type'		=>		'checkbox',
	'name'		=>		'disable_logging',
	'options'	=>		array( 'unchecked' => '0', 'checked' => '1' ),
	'title'		=>		__( 'Disable Live Log', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( 'When enabled no logging details will be written to the Stash Live log during Stash Live periodic operations. Logs will still be written to the Extraneous Global Log file based on your traditional BackupBuddy Advanced Logging Settings. This reduces overhead and server resource usage.' ),
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'textarea',
	'name'		=>		'postmeta_key_excludes',
	'title'		=>		__( 'Additional Postmeta Key Exclusions', 'it-l10n-backupbuddy' ),
	'row_class'	=>		'advanced-toggle',
	'css'		=>		'width: 100%;',
) );
$settings_form->add_setting( array(
	'type'		=>		'textarea',
	'name'		=>		'options_excludes',
	'title'		=>		__( 'Additional Options Exclusions', 'it-l10n-backupbuddy' ),
	'row_class'	=>		'',
	'tip'		=>		__( 'Excludes certain options updates to the wp_options table (beyond hard-coded defaults) from being immediately backed up upon change and instead only backed up during the periodic (typically daily) database snapshot. This is useful for options which are updates very often. Supports regular expressions via preg_match().' ),
	'row_class'	=>		'advanced-toggle',
	'css'		=>		'width: 100%;',
) );
$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'max_burst',
	'title'		=>		__( 'Send per burst', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Example: 10] - This is the amount of data that will be sent per burst within a single PHP page load/chunk. Bursts happen within a single page load. Chunks occur when broken up between page loads/PHP instances. Reduce if hitting PHP memory limits. Chunking time limits will only be checked between bursts. Lower burst size if timeouts occur before chunking checks trigger.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|int[5-9999999]',
	'css'		=>		'width: 50px;',
	'after'		=>		' MB',
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'max_delete_burst',
	'title'		=>		__( 'Delete per burst', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Example: 100] - This is the maximum number of files which can be deleted per API call at a time. This helps reduce outgoing connections and improve performance.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|int[5-9999999]',
	'css'		=>		'width: 50px;',
	'after'		=>		' files',
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'max_filelist_keys',
	'title'		=>		__( 'Max number of files to list', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Example: 250] - This is the maximum number of files to return in a given file listing request from the Live servers.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|int[0-9999999]',
	'css'		=>		'width: 50px;',
	'after'		=>		' files',
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'max_daily_failures',
	'title'		=>		__( 'Max send fails per periodic process run', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Example: 50] - This is the maximum number of send failures which may occur per day before further sends are halted. This counter is reset at the beginning of the periodic process during daily initialization.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|int[0-9999999]',
	'css'		=>		'width: 50px;',
	'after'		=>		' failures',
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'max_wait_on_transfers_time',
	'title'		=>		__( 'Max time to wait on transfers before Snapshotting', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Example: 20] - Snapshots cannot be created until all files are uploaded. If when it is time to create Snapshot one or more files (including database tables) remain to send, we will wait for transfers to finish before Snapshotting.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|int[0-9999999]',
	'css'		=>		'width: 50px;',
	'after'		=>		' minutes',
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'max_time',
	'title'		=>		__( 'Max time per operation', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Example: 30] - Enter 0 for no limit (aka no chunking; bursts may still occur based on burst size setting). This is the maximum number of seconds per page load that operations will run for. If this time is exceeded when a burst finishes then the next burst will be chunked and ran on a new page load. Multiple bursts may be sent within each chunk. NOTE: This ONLY applies to file sends and some Stash Live procedures. Chunking of file and signature calculations may use global BackupBuddy setting.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'',
	'css'		=>		'width: 50px;',
	'after'		=>		' secs. <span class="description">' . __( 'Blank for detected default:', 'it-l10n-backupbuddy' )  . ' ' . backupbuddy_core::detectMaxExecutionTime() . ' sec. Change only if directed.</span>',
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'max_send_details_limit',
	'title'		=>		__( 'Max number of transfers to log', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Example: 5] - BackupBuddy will keep the details including logging of the last X number of remote transfers to the Live servers. Trimming these prevents large numbers of files from building up.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'',
	'css'		=>		'width: 50px;',
	'after'		=>		' transfers.',
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'checkbox',
	'name'		=>		'use_packaged_cert',
	'options'	=>		array( 'unchecked' => '0', 'checked' => '1' ),
	'title'		=>		__( 'Use included CA bundle', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Default: disabled] - When enabled, BackupBuddy will use its own bundled SSL certificate bundle for connecting to the server. Use this if SSL fails due to SSL certificate issues with your server.', 'it-l10n-backupbuddy' ),
	'css'		=>		'',
	'after'		=>		'<span class="description"> ' . __('Use included certificate bundle.', 'it-l10n-backupbuddy' ) . '</span>',
	'rules'		=>		'',
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'checkbox',
	'name'		=>		'ssl',
	'options'	=>		array( 'unchecked' => '0', 'checked' => '1' ),
	'title'		=>		__( 'Encrypt connection', 'it-l10n-backupbuddy' ) . '*',
	'tip'		=>		__( '[Default: enabled] - When enabled, all transfers will be encrypted with SSL encryption. Disabling this may aid in connection troubles but results in lessened security. Note: Once your files arrive on our server they are encrypted using AES256 encryption. They are automatically decrypted upon download as needed.', 'it-l10n-backupbuddy' ),
	'css'		=>		'',
	'after'		=>		'<span class="description"> ' . __('Enable connecting over SSL.', 'it-l10n-backupbuddy' ) . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* Files are always encrypted with AES256 upon arrival.</span>',
	'rules'		=>		'',
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'select',
	'name'		=>		'remote_snapshot_period',
	'title'		=>		__( 'Snapshot Interval', 'it-l10n-backupbuddy' ),
	'options'	=>		$intervals,
	'tip'		=>		__('[Default: Daily; Recommended] - How often snapshots should be made of all files, database content, and data stored on the remote Live servers. This period must be equal to or less often than the overall periodic scan period. NOTE: This remote snapshot will not occur until all local periodic process steps are completed. WARNING: Changing this from daily may result in unexpected behavior with archive limits as it expects once daily Snapshots. Change with caution.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required',
	'row_class'	=>		'advanced-toggle',
	'after'		=>		'<span class="description"> ' . __('Use caution changing. See tip for details.', 'it-l10n-backupbuddy' ),
) );
/*
$settings_form->add_setting( array(
	'type'		=>		'checkbox',
	'name'		=>		'pause_continuous',
	'options'	=>		array( 'unchecked' => '0', 'checked' => '1' ),
	'title'		=>		__( 'Pause continuous process', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Default: unchecked] - When checked, continuous processes will be paused, including live database backup.', 'it-l10n-backupbuddy' ),
	'css'		=>		'',
	'after'		=>		'<span class="description"> ' . __('Check to pause until re-enabled.', 'it-l10n-backupbuddy' ) . '</span>',
	'rules'		=>		'',
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'checkbox',
	'name'		=>		'pause_periodic',
	'options'	=>		array( 'unchecked' => '0', 'checked' => '1' ),
	'title'		=>		__( 'Pause periodic process', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Default: unchecked] - When checked, periodic processes will be paused, including file signature processing and remote file send queueing.', 'it-l10n-backupbuddy' ),
	'css'		=>		'',
	'after'		=>		'<span class="description"> ' . __('Check to pause until re-enabled.', 'it-l10n-backupbuddy' ) . '</span>',
	'rules'		=>		'',
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'checkbox',
	'name'		=>		'disabled',
	'options'	=>		array( 'unchecked' => '0', 'checked' => '1' ),
	'title'		=>		__( 'Disable destination', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Default: unchecked] - When checked, this destination will be disabled and unusable until re-enabled. Use this if you need to temporary turn a destination off but don\t want to delete it.', 'it-l10n-backupbuddy' ),
	'css'		=>		'',
	'after'		=>		'<span class="description"> ' . __('Check to disable this destination until re-enabled.', 'it-l10n-backupbuddy' ) . '</span>',
	'rules'		=>		'',
	'row_class'	=>		'advanced-toggle',
) );
*/
