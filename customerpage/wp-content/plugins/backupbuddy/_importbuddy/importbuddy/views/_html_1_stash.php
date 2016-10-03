<!-- _html_1_stash.php -->
<?php
global $wp_version;
$wp_version = 1;

function stashAPI( $settings, $action, $additionalParams = array() ) {
	$api_url = 'https://stash-api.ithemes.com';
	
	global $wp_version;
	$url_params = array(
		'action'    => $action,
		'user'      => $settings['itxapi_username'],
		'wp'        => $wp_version,
		'bb'		=> 0,
		'ib'		=> pb_backupbuddy::$options['bb_version'],
		'site'      => 'importbuddy',
		'timestamp' => time()
	);
	
	if ( isset( $settings['itxapi_password' ] ) ) { // Used on initital connection to  
		$params = array( 'auth_token' => $settings['itxapi_password'] ); // itxapi_password is a HASH of user's password.
	} elseif ( isset( $settings['itxapi_token' ] ) ) { // Used on initital connection to  
		$params = array( 'token' => $settings['itxapi_token'] ); // itxapi_password is a HASH of user's password.
	} else {
		$error = 'BackupBuddy Error #793749436: No valid token (itxapi_token) or hashed password (itxapi_password) specified. This should not happen.';
		self::_error( $error );
		trigger_error( $error, E_USER_NOTICE );
		return $error;
	}
	
	$params = array_merge( $params, $additionalParams );
	$body = array( 'request' => json_encode( $params ) );
	
	/*
	echo 'BODY:<pre>';
	print_r( $body );
	echo '</pre>';
	*/
	
	$post_url = $api_url . '/?' . http_build_query( $url_params, null, '&' );
	
	$request = new RequestCore( $post_url );
	$request->set_method( 'POST' );
	$request->set_body( $body );
	$response = $request->send_request(true);
	
	/*
	echo 'RESPONSE:<pre>';
	print_r( $response->body );
	echo '</pre>';
	*/
	
	if( ! $response->isOK() ) {
		pb_backupbuddy::status( 'error', 'Stash request for files failed.' );
		return $response->body;
	} else {
		// See if we got a json response.
		if ( ! $response_decoded = json_decode($response->body, true) ) {
			pb_backupbuddy::status( 'error', 'Stash did not get valid json response.' );
		}
		
		// Finally see if the API returned an error.
		if ( isset( $response_decoded['error'] ) ) {            
			if ( isset( $response_decoded['error']['message'] ) ) {
				$error = 'Error #39752893. Server reported an error performing action `' . $action . '` with additional params: `' . print_r( $additionalParams, true ) . '`. Details: `' . print_r( $response_decoded['error'], true ) . '`.';
				pb_backupbuddy::status( 'warning', $error );
				return $response_decoded['error']['message'];
			} else {
				$error = 'Error #3823973. Received Stash API error but no message found. Details: `' . print_r( $response_decoded, true ) . '`.';
				pb_backupbuddy::status( 'warning', $error );
				return $error;
			}
		} else { // NO ERRORS
			return $response_decoded;
		}
	}
}
?>


<style>
.widefat td {
	padding: 20px 7px;
	font-size: 14px;
}
.widefat td a:hover {
	text-decoration: none;
}
.widefat td form {
	margin-top: -12px;
	font-size: 12px;
}
.stash_backup_file {
	font-size: 1.2em;
}
</style>
<?php
$ITXAPI_KEY = 'ixho7dk0p244n0ob';
$ITXAPI_URL = 'http://api.ithemes.com';



$credentials_form = new pb_backupbuddy_settings( 'pre_settings', false, 'upload=stash#pluginbuddy-tabs-stash' ); // name, savepoint|false, additional querystring
/*
$credentials_form->add_setting( array(
	'type'		=>		'hidden',
	'name'		=>		'pass_hash',
	'default'	=>		PB_PASSWORD,
) );

$credentials_form->add_setting( array(
	'type'		=>		'hidden',
	'name'		=>		'options',
	'default'	=>		htmlspecialchars( serialize( pb_backupbuddy::$options ) ),
) );
*/

$credentials_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'itxapi_username',
	'title'		=>		__( 'iThemes username', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|string[1-45]',
) );
$credentials_form->add_setting( array(
	'type'		=>		'password',
	'name'		=>		'itxapi_password_raw',
	'title'		=>		__( 'iThemes password', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|string[1-45]',
) );

$settings_result = $credentials_form->process();
$login_welcome = '<center>' . __( 'Log in to Stash with your iThemes.com member account.', 'it-l10n-backupbuddy' ) . '<br><br>';

if ( count( $settings_result ) == 0 ) { // No form submitted.
	
	echo $login_welcome;
	$credentials_form->display_settings( 'Connect to Stash' );
	
} else { // Form submitted.
	if ( count( $settings_result['errors'] ) > 0 ) { // Form errors.
		echo $login_welcome;
		
		pb_backupbuddy::alert( implode( '<br>', $settings_result['errors'] ) );
		$credentials_form->display_settings( 'Connect to Stash' );
		
	} else { // No form errors; process!
		
		require_once( dirname( dirname( __FILE__ ) ) . '/lib/requestcore/requestcore.class.php' );
		//require_once( dirname( dirname( __FILE__ ) ) . '/lib/stash2/init.php' );
		require_once( dirname( dirname( __FILE__ ) ) . '/lib/stash2/class.itx_helper2.php' );
		
		global $wp_version;
		$itxapi_username = strtolower( $settings_result['data']['itxapi_username'] );
		$password_hash = iThemes_Credentials::get_password_hash( $itxapi_username, $settings_result['data']['itxapi_password_raw'] );
		$access_token = ITXAPI_Helper2::get_access_token( $itxapi_username, $password_hash, 'importbuddy', $wp_version );
		
		$settings = array(
			'itxapi_username' => $itxapi_username,
			'itxapi_password' => $access_token,
		);
		$response = stashAPI( $settings, 'connect' );
		
		$logged_in = false;
		if ( ! is_array( $response ) ) { // Error message.
			pb_backupbuddy::alert( 'Error #3983794 from server: `' . print_r( $response, true ) .'`.' );
			$credentials_form->display_settings( 'Submit' );
		} else {
			if ( isset( $response['error'] ) ) {
				pb_backupbuddy::alert( 'Error: ' . $response['error']['message'] );
				$credentials_form->display_settings( 'Submit' );
			} else {
				if ( isset( $response['token'] ) ) {
					$settings['itxapi_token'] = $response['token'];
					$itxapi_token = $settings['itxapi_token'];
					unset( $settings['itxapi_password'] ); // No longern eeded since we have token now.
					$logged_in = true;
				} else {
					pb_backupbuddy::alert( 'Error #34974734323: Unexpected server response. Token missing. Check your login and try again. Detailed response: `' . print_r( $response, true ) .'`.' );
					$credentials_form->display_settings( 'Submit' );
				}
			}
		}
		
		if ( true === $logged_in ) {
			// Get files.
			$stash_files = stashAPI( $settings, 'files' );
			
			
			
			/*
			echo 'FILES:<pre>';
			print_r( $stash_files );
			echo '</pre>';
			*/
			
			
			
			// Finally see if the API returned an error.
			if ( ! is_array( $stash_files ) ) {            
				pb_backupbuddy::alert( 'Stash Error: ' . $stash_files );
				$credentials_form->display_settings( 'Submit' );
			} else { // NO ERRORS
				
				
				// Sort in order & exclude unwanted backup types.
				$backup_list_temp = array();
				foreach( (array)$stash_files as $i => $stash_file ) {
					$file = $stash_file['filename'];
					$url = $stash_file['url'];
					$size = $stash_file['size'];
					$modified = $stash_file['uploaded_timestamp'];
					
					// Avoid collion for sorting.
					while( isset( $backup_list_temp[ $modified ] ) ) {
						$modified += 0.1;
					}
					
					if ( 'db' == $stash_file['backup_type']) {
						$backup_type = 'Database';
					} elseif( 'full' == $stash_file['backup_type'] ) {
						$backup_type = 'Full';
					} elseif( 'themes' == $stash_file['backup_type'] ) { // Omit from list.
						unset( $stash_files[ $i ] );
						continue;
					} elseif( 'plugins' == $stash_file['backup_type'] ) { // Omit from list.
						unset( $stash_files[ $i ] );
						continue;
					} else {
						$backup_type = 'Unknown';
						continue;
					}
					$stash_file['backup_type_pretty'] = $backup_type;
					
					$backup_list_temp[ $modified ] = $stash_file;
				}
				krsort( $backup_list_temp );
				$stash_files = $backup_list_temp;
				unset( $backup_list_temp );
				
				
				
				// Split up into sites.
				$backup_lists = array();
				foreach( $stash_files as $stash_file ) {
					$backup_lists[ $stash_file['site'] ][] = $stash_file;
				}
				unset( $stash_files );
				
				
				
				// Render table listing files.
				if ( count( $backup_lists ) == 0 ) {
					echo '<b>';
					_e( 'You have not sent any backups to Stash yet (or files are still transferring).', 'it-l10n-backupbuddy' );
					echo '</b>';
				} else {
					
					// Loop through each site.
					foreach( $backup_lists as $stash_files ) { // Each site.
						
						// Loop through each file within this site.
						$display_list = array();
						foreach( $stash_files as $stash_file ) { // Each backup in site.
							$site = $stash_file['site'];
							
							$display_list[] = array(
								$stash_file['backup_type_pretty'],
								'<span class="stash_backup_file">' . pb_backupbuddy::$format->date( pb_backupbuddy::$format->localize_time( $stash_file['uploaded_timestamp'] ), 'l, F j, Y - g:i A' ) . '</span> <span class="description">(' . pb_backupbuddy::$format->time_ago( $stash_file['uploaded_timestamp'] ) . ' ago)</span></a><br><a href="' . $stash_file['url'] . '">' . $stash_file['basename'] . '</a>',
								pb_backupbuddy::$format->file_size( $stash_file['size'] ),
								'<form action="?#pluginbuddy-tabs-server" method="POST">
									<input type="hidden" name="pass_hash" value="' . PB_PASSWORD . '">
									<input type="hidden" name="upload" value="stash">
									<input type="hidden" name="options" value="' . htmlspecialchars( serialize( pb_backupbuddy::$options ) ) . '">
									<input type="hidden" name="link" value="' . $stash_file['url'] . '">
									<input type="submit" name="submit" value="Restore" class="button-primary">
								</form>',
								
							);
						}
						
						echo '<h3>Site: ' . $site . '</h3>';
						pb_backupbuddy::$ui->list_table(
							$display_list,
							array(
								//'action'		=>	pb_backupbuddy::page_url() . '&custom=remoteclient&destination_id=' . htmlentities( pb_backupbuddy::_GET( 'destination_id' ) ) . '&remote_path=' . htmlentities( pb_backupbuddy::_GET( 'remote_path' ) ),
								'columns'		=>	array( 'Type', 'Uploaded<img src="' . pb_backupbuddy::plugin_url() . '/images/sort_down.png" style="vertical-align: 0px;" title="Sorted most recent first"><span class="description">(Click to download)</span>', 'File Size', 'Action' ),
								'css'			=>		'width: 100%;',
							)
						);
						echo '<br><br>';
						
					}
				}
				
				
			} // end no errors getting file info from API.
		} // end logged in.
	}
	
} // end form submitted.

?>

<br><hr>
<center>
	<a href="https://sync.ithemes.com/stash" target="_blank" class="button button-secondary">Manage your Stash files via iThemes Sync</a>
</center>
<br>
