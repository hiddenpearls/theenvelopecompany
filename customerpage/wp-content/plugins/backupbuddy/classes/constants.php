<?php
class backupbuddy_constants {
	
	// General
	const NOTIFICATIONS_OPTION_SLUG =  'pb_backupbuddy_notifications'; // option name to store notifications into site options system.
	const NOTIFICATIONS_MAX_COUNT = 25; // Only keep the most recent notifications, up to this limit. Prevent too many building up.
	const SCHEDULE_RETRY_WAIT = 1; // Seconds to wait to retry scheduling with WordPress if WordPress reports failure.
	const PHP_MEMORY_RETEST_DELAY = 15; // Minimum amount of seconds which must have elapsed since the file modified time was updated to begin a retest. Prevents accidental overwrite of in-progress test.
	const PHP_RUNTIME_RETEST_DELAY = 15; // Minimum amount of seconds which must have elapsed since the file modified time was updated to begin a retest. Prevents accidental overwrite of in-progress test.
	const PHP_RUNTIME_TEST_MAX_TIME = 300; // Maximum number of seconds (loops with 1sec sleeps) to run PHP runtime test for. For huge runtimes prevents from running for full duration.
	const MINIMUM_CRON_KICK_INTERVAL = 600; // [10min] Minimum number of seconds between cron-kick calls to Stash API.
	const MIMIMUM_TIME_BETWEEN_ERROR_EMAILS = 60; // [1min] Minimum number of seconds between any error emails being sent, to prevent floods.
	
	// Cleanup
	const TIME_BEFORE_CONSIDERED_TIMEOUT = 86400; // If a backup OR remote send is has not made any progress in terms of a function finishing after X seconds (stored in updated_time in the backup fileoptions) then the backup will be considered a likely timeout.
	const MAX_SECONDS_TO_KEEP_ORPHANED_FILEOPTIONS_FILES = 2592000; // 30 days - Once this time has passed then the housekeeping cleanup function will be given the go-ahead to delete fileoptions files that have no local backup zip file that matches their serial. We keep these for a while so the Recent Backups page will keep them in its list.
	const CLEANUP_MAX_IMPORTBUDDY_AGE = 10800; // 3 hours - Max age, in seconds, importbuddy files can be there before cleaning up periodically (delay useful if just imported and testing out site).
	const CLEANUP_MAX_STATUS_LOG_AGE = 864000; // 10 days - Max age in seconds to keep old logs before cleaning up periodically.
	const CLEANUP_MAX_STATUS_LOG_COUNT = 200; // Maximum number of status log files to keep.
	const CLEANUP_MAX_AGE_TO_NOTIFY_TIMEOUT = 604800; // 7 days - Max age in seconds to send timeout emails for. Prevents very old backups being detected as timed out from sending an error notification email.
	const CLEANUP_FINISHED_FILEOPTIONS_AGE_DELAY = 15; // Number of seconds to allow a send fileoptions file to exist even if it exceeds the file limit count. Prevents removing fileoptions of finished files too early.
	
	// Live
	const MINIMUM_TIME_BETWEEN_ACTIVITY_AND_PERIODIC_CRON_RUN = 1800; // [30min] Minimum amount of time to allow between the last_activity state key and restarting the periodic process via cron.
	const TIMED_OUT_PROCESS_RESUME_WIGGLE_ROOM = 60; // The number of seconds added to the backupbuddy_core::detectLikelyHighestExecutionTime() after which we MAY restart the periodic process assuming it timed out. This happens in live_stats.php AJAX if a user is on the Live page and also is used for the fileoptions lock ignoring value.
	const DAYS_BEFORE_RUNNING_TROUBLESHOOTING_TEST = 3; // After this many days with no snapshot run the troubleshooting script to possibly pop up alert of issues on live page.
	
	// Cron
	const DEFAULT_CRON_PRIORITY = 5; // Default cron priority for registering via add_action().
	const CRON_SINGLE_PASS_RESCHEDULE_LIMIT = 5000; // Safety net to prevent infinite rescheduling. Should never happen, but just in case.
	
	// Deployment
	const DEPLOYMENT_REMOTE_API_DEFAULT_TIMEOUT = 30; // Default timeout (in seconds) for remote API calls (over HTTP) to timeout after if an overriding timeout is not passed in. Actual timeout used will be this value minus 2 seconds of wiggle room.
	
	// Backup
	const BACKUP_STATUS_BOX_LIMIT_OPTION_LINES = 100; // If limiting the status box is enabled, number of lines to limit to.
	const BACKUP_STATUS_FILEOPTIONS_WAIT_COUNT_LIMIT = 10; // Max number of times to wait for a fileoptions file to become viable. Eg a valid array inside when checking the backup status.
	public static $HARDCODED_DIR_EXCLUSIONS = array( // Directories to exclude from all backups (traditional & Live).
		'/.sucuriquarantine/', // Infected files.
		'/wp-content/uploads/sucuri/', // Temp files such as IP bans.
		'/wp-content/envato-backups/', // Don't backup backups of other plugins.
		'/wp-content/uploads/backwpup', // No trailing slash so anything after is removed. Don't backup backups of other plugins.
		'/wp-content/backup-db/', // Don't backup backups of other plugins.
		'/wp-snapshots/', // Don't backup backups of other plugins.
		'/wp-content/ai1wm-backups/', // All in One WordPress Migration backups. Don't backup backups of other plugins.
	);
	
	// Remote Sends
	const REMOTE_SEND_MAX_TIME_SINCE_START_TO_BAIL = 2592000; // 30 days. If this amount of time passed since the START of a remote send to consider bailing when retrying. This is basically a failsafe of the retry count fails and it keeps trying to send retries indefinitely. Only a failsafe.
	
	// PHP date() timestamp format for the backup archive filename. DATE is default.
	const ARCHIVE_NAME_FORMAT_DATE = 'Y_m_d';				// Format when archive_name_format = date.
	const ARCHIVE_NAME_FORMAT_DATETIME = 'Y_m_d-h_ia';		// Format when archive_name_format = datetime.
	const ARCHIVE_NAME_FORMAT_DATETIME24 = 'Y_m_d-H_i';		// Format when archive_name_format = datetime24.
} // end class.