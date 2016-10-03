<?php // This code runs everywhere. pb_backupbuddy::$options preloaded.


/***** BEGIN BackupBuddy Stash Live Init *****/
//pb_backupbuddy::load();
foreach( pb_backupbuddy::$options['remote_destinations'] as $destination ) { // See if we have Live activated.
	if ( 'live' == $destination['type'] ) {
		include( 'destinations/live/live_continuous.php' );
		backupbuddy_live_continuous::init();
		break;
	}
}
/***** END BackupBuddy Stash Live Init *****/



include( 'classes/constants.php' );
include( 'classes/api.php' );

// Handle API calls if backupbuddy_api_key is posted. If anything fails security checks pretend nothing at all happened.
if ( '' != pb_backupbuddy::_POST( 'backupbuddy_api_key' ) ) { // Remote API access.
	if ( isset( pb_backupbuddy::$options['remote_api'] ) && ( count( pb_backupbuddy::$options['remote_api']['keys'] ) > 0 ) && defined( 'BACKUPBUDDY_API_ENABLE' ) && ( TRUE == BACKUPBUDDY_API_ENABLE ) ) { // Verify API is enabled. && defined( 'BACKUPBUDDY_API_SALT' ) && ( 'CHANGEME' != BACKUPBUDDY_API_SALT ) && ( strlen( BACKUPBUDDY_API_SALT ) >= 5 )
		include( 'classes/remote_api.php' );
		backupbuddy_remote_api::localCall( $secure = true );
		die();
	}
}

// Internal cron (disabled by default).
if ( ( '1' == pb_backupbuddy::$options['use_internal_cron'] ) && ( 'process_backup' == pb_backupbuddy::_POST( 'backupbuddy_cron_action' ) ) ) {
	// Verify access to trigger cron.
	if ( pb_backupbuddy::$options['log_serial'] != pb_backupbuddy::_POST( 'backupbuddy_key' ) ) {
		die( 'Access Denied.' );
	}
	
	// Ignore anything older than 5 min.
	$max_cron_age = 60 * 5;
	if ( time() - pb_backupbuddy::_POST( 'backupbuddy_time' ) > ( $max_cron_age ) ) {
		die( 'Cron action too old.' );
	}
	
	// Try to prevent any caching layer.
	@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
	@header("Cache-Control: no-store, no-cache, must-revalidate"); 
	@header("Cache-Control: post-check=0, pre-check=0", false);
	@header("Pragma: no-cache");
	
	// Process.
	require_once( pb_backupbuddy::plugin_path() . '/classes/core.php' );
	backupbuddy_core::process_backup( pb_backupbuddy::_POST( 'backupbuddy_serial' ) );
	die( '`' . microtime( true ) . '`' );
}


// Make localization happen.
if ( ( ! defined( 'PB_STANDALONE' ) ) && ( '1' != pb_backupbuddy::$options['disable_localization'] ) ) {
	load_plugin_textdomain( 'it-l10n-backupbuddy', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}



/********** ACTIONS (global) **********/



/********** AJAX (global) **********/



/********** CRON (global) **********/
pb_backupbuddy::add_cron( 'cron', backupbuddy_constants::DEFAULT_CRON_PRIORITY, 3 ); // Master CRON handler as of v6.4.0.9. Pass all cron functionality through this. Params: cron_method, args, reschedulecount(optional).



/********** FILTERS (global) **********/
pb_backupbuddy::add_filter( 'cron_schedules' ); // Add schedule periods such as bimonthly, etc into cron. By default passes 1 param at priority 10.
if ( '1' == pb_backupbuddy::$options['disable_https_local_ssl_verify'] ) {
	$disable_local_ssl_verify_anon_function = create_function( '', 'return false;' );
	add_filter( 'https_local_ssl_verify', $disable_local_ssl_verify_anon_function, 100 );
}



/********** OTHER (global) **********/



// WP-CLI tool support for command line access to BackupBuddy. http://wp-cli.org/
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	include( pb_backupbuddy::plugin_path() . '/classes/wp-cli.php' );
}

/*
add_filter( 'cron_request', 'backupbuddy_spoof_cron_agent' );
function backupbuddy_spoof_cron_agent( $cron ) {
	$cron['args']['user-agent'] = 'Mozilla';
	return $cron;
}
*/


// Jetpack Security Report. As of Jan 2015.
function backupbuddy_jetpack_security_report() {
	
	$maxTimeWithoutBackupBeforeWarn = 60*60*24*32; // After this amount of time (default 32 days) we will warn the user that they have not completed a backup in too long.
	
	// Default arguments.
	$args = array(
		'status' => 'ok',
		'message' => '',
	);
	
	// Determine last completed backup.
	$lastRun = pb_backupbuddy::$options['last_backup_finish'];
	if ( 0 === $lastRun ) { // never made a backup.
		$args['status'] = 'warning';
		$args['message'] = __( 'You have not completed your first BackupBuddy backup.', 'it-l10n-backupbuddy' );
	} else { // have made a backup.
		$args['last'] = $lastRun;
		
		// If the last backup was too long ago then change status to warning. Only calculate a backup was ever made.
		if ( ( time() - $lastRun ) > $maxTimeWithoutBackupBeforeWarn ) {
			$args['status'] = 'warning';
			$args['message'] .= ' ' . __( 'It has been over a month since your last BackupBuddy backup completed.', 'it-l10n-backupbuddy' );
		}
	}
	
	// Determine next run.
	$nextRun = 0;
	foreach ( pb_backupbuddy::$options['schedules'] as $schedule_id => $schedule ) { // Find soonest schedule to run next.
		$thisRun = wp_next_scheduled( 'backupbuddy_cron', array( 'run_scheduled_backup', array( (int)$schedule_id ) ) );
		if ( false !== $thisRun ) {
			if ( ( 0 === $nextRun ) || ( $thisRun < $nextRun ) ) { // Set next run if $thisRun is going to run sooner than schedule in $nextRun.
				$nextRun = $thisRun;
			}
		}
	}
	if ( 0 === $nextRun ) {
		$args['status'] = 'warning';
		$args['message'] .= ' ' . __( 'You do not currently have any backup schedules in BackupBuddy.', 'it-l10n-backupbuddy' );
	} else {
		$args['next'] = $nextRun;
	}
	
	// Cleanup.
	$args['message'] = trim( $args['message'] );
	if ( '' == $args['message'] ) {
		unset( $args['message'] );
	}
	
	// Call security report.
	Jetpack::submit_security_report(
		'backup',
		dirname( __FILE__ ) . '/backupbuddy.php',
		$args
	);
	
} // End backupbuddy_jetpack_security_report().
add_action( 'jetpack_security_report', 'backupbuddy_jetpack_security_report' );


if ( isset( pb_backupbuddy::$options['cron_request_timeout_override'] ) && ( '' != pb_backupbuddy::$options['cron_request_timeout_override'] ) && ( pb_backupbuddy::$options['cron_request_timeout_override'] > 0 ) ) {
	add_filter( 'cron_request', 'backupbuddy_cron_request_adjust' );
	function backupbuddy_cron_request_adjust( $cron ) {
		$cron['args']['timeout'] = pb_backupbuddy::$options['cron_request_timeout_override'];
		return $cron;
	}
}



// iThemes Sync Verb Support
function backupbuddy_register_sync_verbs( $api ) {
	$verbs = array(
		'backupbuddy-run-backup'				=> 'Ithemes_Sync_Verb_Backupbuddy_Run_Backup',
		'backupbuddy-list-profiles'				=> 'Ithemes_Sync_Verb_Backupbuddy_List_Profiles',
		'backupbuddy-list-schedules'			=> 'Ithemes_Sync_Verb_Backupbuddy_List_Schedules',
		'backupbuddy-list-destinations'			=> 'Ithemes_Sync_Verb_Backupbuddy_List_Destinations',
		'backupbuddy-list-destinationTypes'		=> 'Ithemes_Sync_Verb_Backupbuddy_List_DestinationTypes',
		'backupbuddy-get-overview'				=> 'Ithemes_Sync_Verb_Backupbuddy_Get_Overview',
		'backupbuddy-get-latestBackupProcess'	=> 'Ithemes_Sync_Verb_Backupbuddy_Get_LatestBackupProcess',
		'backupbuddy-get-everything'			=> 'Ithemes_Sync_Verb_Backupbuddy_Get_Everything',
		'backupbuddy-get-importbuddy'			=> 'Ithemes_Sync_Verb_Backupbuddy_Get_Importbuddy',
		'backupbuddy-add-schedule'				=> 'Ithemes_Sync_Verb_Backupbuddy_Add_Schedule',
		'backupbuddy-test-destination'			=> 'Ithemes_Sync_Verb_Backupbuddy_Test_Destination',
		'backupbuddy-delete-destination'		=> 'Ithemes_Sync_Verb_Backupbuddy_Delete_Destination',
		'backupbuddy-delete-schedule'			=> 'Ithemes_Sync_Verb_Backupbuddy_Delete_Schedule',
		'backupbuddy-get-destinationSettings'	=> 'Ithemes_Sync_Verb_Backupbuddy_Get_DestinationSettings',
		'backupbuddy-add-destination'			=> 'Ithemes_Sync_Verb_Backupbuddy_Add_Destination',
		'backupbuddy-edit-destination'			=> 'Ithemes_Sync_Verb_Backupbuddy_Edit_Destination',
		'backupbuddy-get-backupStatus'			=> 'Ithemes_Sync_Verb_Backupbuddy_Get_BackupStatus',
		'backupbuddy-get-liveStats'				=> 'Ithemes_Sync_Verb_Backupbuddy_Get_LiveStats',
		'backupbuddy-set-liveStatus'			=> 'Ithemes_Sync_Verb_Backupbuddy_Set_LiveStatus',
		'backupbuddy-run-liveSnapshot'			=> 'Ithemes_Sync_Verb_Backupbuddy_Run_LiveSnapshot',
	);
	foreach( $verbs as $name => $class ) {
		$api->register( $name, $class, pb_backupbuddy::plugin_path() . "/classes/ithemes-sync/$name.php" );
	}
}
add_action( 'ithemes_sync_register_verbs', 'backupbuddy_register_sync_verbs' );