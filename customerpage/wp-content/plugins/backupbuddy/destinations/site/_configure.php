<?php
require_once( pb_backupbuddy::plugin_path() . '/classes/remote_api.php' );
$apiSettings = backupbuddy_remote_api::key_to_array( $destination_settings['api_key'] );

$default_name = NULL;
if ( 'add' == $mode ) {
	$default_name = 'My Deployment Site';
}
$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'title',
	'title'		=>		__( 'Destination name', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( 'Name of the new destination to create. This is for your convenience only.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|string[1-45]',
	'default'	=>		$default_name,
) );

if ( 'add' == $mode ) {
	$after = '';
} else {
	$after = '<br>' . __( 'Saved API key site URL', 'it-l10n-backupbuddy' ) . ': <span class="description">' . $apiSettings['siteurl'] . '</span>';
}
$settings_form->add_setting( array(
	'type'		=>		'textarea',
	'name'		=>		'api_key',
	'title'		=>		__( 'Remote API Key', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( 'Copy & paste the destination site\'s BackupBuddy API Key. Find this under the other remote site\'s BackupBuddy Remote Destinations page by clicking the \'Show Deployment Key\' button near the top.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|string[0-1000]',
	'css'		=>		'width: 680px; height: 110px; padding: 8;',
	'after'		=>		$after,
) );

$settings_form->add_setting( array(
	'type'		=>		'checkbox',
	'name'		=>		'sha1',
	'options'	=>		array( 'unchecked' => '0', 'checked' => '1' ),
	'title'		=>		__( 'Hash files for comparison', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Default: unchecked] - When checked, file differences between sites will make use of SHA1 hash comparisons for improved detection of file differences. Note that this uses more server resources.', 'it-l10n-backupbuddy' ),
	'css'		=>		'',
	'after'		=>		'<span class="description"> ' . __('Check to enable file hashing.', 'it-l10n-backupbuddy' ) . '</span>',
	'rules'		=>		'',
	//'row_class'	=>		'advanced-toggle',
) );


$settings_form->add_setting( array(
	'type'		=>		'title',
	'name'		=>		'advanced_begin',
	'title'		=>		'<span class="dashicons dashicons-arrow-right"></span> ' . __( 'Advanced Options', 'it-l10n-backupbuddy' ),
	'row_class'	=>		'advanced-toggle-title',
) );



$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'max_payload',
	'title'		=>		__( 'Max chunk size', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Example: 10] - Maximum size of any data payload when communicating with the remote server. If this size is to be surpassed when sending data it will automatically be broken up into smaller chunks no larger than this size.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|int[0-9999999]',
	'css'		=>		'width: 50px;',
	'after'		=>		' MB. <span class="description">' . __( 'Default', 'it-l10n-backupbuddy' ) . ': 10 MB</span>',
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