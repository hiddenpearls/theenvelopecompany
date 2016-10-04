<?php

if (!defined('ABSPATH'))
    die("Can't load this file directly");
	
class wooexim_user{

	function wooexim_user()
	{
				
 		add_action( 'wp_ajax_wooexim_save_user_fields', array( &$this, 'wooexim_save_user_fields' ));
		
		add_action( 'wp_ajax_wooexim_get_user_details', array( &$this, 'wooexim_get_user_details' ));
		
		add_action( 'wp_ajax_wooexim_import_users', array( &$this, 'wooexim_import_users' ));
		
		add_action( 'wp_ajax_save_user_scheduled', array( &$this, 'save_user_scheduled' ));
		
		add_action( 'wp_ajax_wooexim_import_users', array( &$this, 'wooexim_import_users' ));
						
   	} 
	
	function user_export_field_list()
	{		
			$field_list = array(
					
					'user_field' => array(
					
										array(
											'field_key' => 'ID',
											'field_display' => 1,
											'field_title' => 'Id',
											'field_value' => 'Id',
										),
										array(
											'field_key' => 'user_role',
											'field_display' => 1,
											'field_title' => 'User Role',
											'field_value' => 'User Role',
										),
										array(
											'field_key' => 'user_email',
											'field_display' => 1,
											'field_title' => 'User Email',
											'field_value' => 'User Email',
										),
										array(
											'field_key' => 'user_login',
											'field_display' => 1,
											'field_title' => 'Username',
											'field_value' => 'Username',
										),
										array(
											'field_key' => 'user_pass',
											'field_display' => 1,
											'field_title' => 'Password',
											'field_value' => 'Password',
										),
										array(
											'field_key' => 'user_registered',
											'field_display' => 1,
											'field_title' => 'User Registered',
											'field_value' => 'User Registered',
										),
										array(
											'field_key' => 'user_url',
											'field_display' => 1,
											'field_title' => 'Website',
											'field_value' => 'Website',
										),
										array(
											'field_key' => 'billing_first_name',
											'field_display' => 1,
											'field_title' => 'First Name (Billing)',
											'field_value' => 'First Name (Billing)',
										),
										array(
											'field_key' => 'billing_last_name',
											'field_display' => 1,
											'field_title' => 'Last Name (Billing)',
											'field_value' => 'Last Name (Billing)',
										),
										array(
											'field_key' => 'billing_company',
											'field_display' => 1,
											'field_title' => 'Company (Billing)',
											'field_value' => 'Company (Billing)',
										),
										array(
											'field_key' => 'billing_address_1',
											'field_display' => 1,
											'field_title' => 'Address 1 (Billing)',
											'field_value' => 'Address 1 (Billing)',
										),
										array(
											'field_key' => 'billing_address_2',
											'field_display' => 1,
											'field_title' => 'Address 2 (Billing)',
											'field_value' => 'Address 2 (Billing)',
										),
										array(
											'field_key' => 'billing_city',
											'field_display' => 1,
											'field_title' => 'City (Billing)',
											'field_value' => 'City (Billing)',
										),
										array(
											'field_key' => 'billing_postcode',
											'field_display' => 1,
											'field_title' => 'Postcode (Billing)',
											'field_value' => 'Postcode (Billing)',
										),
										array(
											'field_key' => 'billing_country',
											'field_display' => 1,
											'field_title' => 'Country (Billing)',
											'field_value' => 'Country (Billing)',
										),
										array(
											'field_key' => 'billing_state',
											'field_display' => 1,
											'field_title' => 'State (Billing)',
											'field_value' => 'State (Billing)',
										),
										array(
											'field_key' => 'billing_email',
											'field_display' => 1,
											'field_title' => 'Email (Billing)',
											'field_value' => 'Email (Billing)',
										),
										array(
											'field_key' => 'billing_phone',
											'field_display' => 1,
											'field_title' => 'Phone (Billing)',
											'field_value' => 'Phone (Billing)',
										),
										array(
											'field_key' => 'shipping_first_name',
											'field_display' => 1,
											'field_title' => 'First Name (Shipping)',
											'field_value' => 'First Name (Shipping)',
										),
										array(
											'field_key' => 'shipping_last_name',
											'field_display' => 1,
											'field_title' => 'Last Name (Shipping)',
											'field_value' => 'Last Name (Shipping)',
										),
										array(
											'field_key' => 'shipping_company',
											'field_display' => 1,
											'field_title' => 'Company (Shipping)',
											'field_value' => 'Company (Shipping)',
										),
										array(
											'field_key' => 'shipping_address_1',
											'field_display' => 1,
											'field_title' => 'Address 1 (Shipping)',
											'field_value' => 'Address 1 (Shipping)',
										),
										array(
											'field_key' => 'shipping_address_2',
											'field_display' => 1,
											'field_title' => 'Address 2 (Shipping)',
											'field_value' => 'Address 2 (Shipping)',
										),
										array(
											'field_key' => 'shipping_city',
											'field_display' => 1,
											'field_title' => 'City (Shipping)',
											'field_value' => 'City (Shipping)',
										),
										array(
											'field_key' => 'shipping_postcode',
											'field_display' => 1,
											'field_title' => 'Postcode (Shipping)',
											'field_value' => 'Postcode (Shipping)',
										),
										array(
											'field_key' => 'shipping_state',
											'field_display' => 1,
											'field_title' => 'State (Shipping)',
											'field_value' => 'State (Shipping)',
										),
										array(
											'field_key' => 'shipping_country',
											'field_display' => 1,
											'field_title' => 'Country (Shipping)',
											'field_value' => 'Country (Shipping)',
										),
										array(
											'field_key' => 'wooexim_user_meta',
											'field_display' => 1,
											'field_title' => 'User Meta',
											'field_value' => 'User Meta',
										),
										array(
											'field_key' => 'wooexim_user_capabilities',
											'field_display' => 1,
											'field_title' => 'User Capabilities',
											'field_value' => 'User Capabilities',
										),
										
									),
						);
						
		return $field_list;
	}
	function get_new_user_fields()
	{
		global $wooexim_user;
		
		$user_fields = maybe_serialize($wooexim_user -> user_export_field_list());
		
		return $user_fields;
	}
	function get_updated_user_fields()
	{
		global $wooexim_user;
		
		$old_user_fields = $wooexim_user -> get_new_user_fields();
		
		$new_fields = get_option('wooexim_user_fields',$old_user_fields);
		
		$new_fields = maybe_unserialize($new_fields);
		
		return $new_fields;
	}
	function wooexim_save_user_fields()
	{
		global $wooexim_user;
		
		$old_user_fields = $wooexim_user -> get_updated_user_fields();
		
		$new_fields = array();
		
		foreach($old_user_fields as $user_fields_key=>$user_fields_data)
		{
 			foreach($user_fields_data as $key=>$value)
			{
				$new_fields[$user_fields_key][$key]['field_key'] = $value['field_key'];
				
				$new_fields[$user_fields_key][$key]['field_display'] = $value['field_display'];
				
				$new_fields[$user_fields_key][$key]['field_title'] = $value['field_title'];
				
				$new_fields[$user_fields_key][$key]['field_value'] = isset($_POST['wooexim_'.$value['field_key'].'_field'])?$_POST['wooexim_'.$value['field_key'].'_field']:"";
				
  			}
		}
		
		$new_fields_data = maybe_serialize($new_fields);
		
		update_option('wooexim_user_fields', $new_fields_data);
		
		$return_value = array();
		
		$return_value['message']	= 'success';
		
		$return_value['message_content']	= __('Changes Saved Successfully.',WOOEXIM_TEXTDOMAIN);
			
		echo json_encode($return_value );
		
		die();
	}
	
	function get_user_list()
	{
		$user_query = array();
		
		$user_query['fields'] = array('ID','display_name','user_email');
		
		$user_query['number'] = 2000;
		
		$user_list = get_users($user_query);
		
		return $user_list;
	}
	function wooexim_get_user_details()
 	{
	
		global $wooexim_user;
	
		$user_field_list = $wooexim_user -> get_updated_user_fields();
		
		$user_list_data = $wooexim_user -> wooexim_get_user_data($_POST);
		
 		ob_start();
		
		?>
		<div class="wooexim_filter_data_container">
			<table class="wooexim_user_filter_data">
				<thead>
					<tr class="wooexim_user_filter_data_row">
						<th class="wooexim_user_filter_data_header"></th>
					<?php   foreach($user_field_list['user_field'] as $user_field){
							
								if($user_field['field_display']==1){?>
								
									<th class="wooexim_user_filter_data_header"><?php echo $user_field['field_value'];?></th>
									
								<?php 
								}
							}
  					?>
					</tr>
				</thead>
				<tbody>
					<?php 
					$temp_count_data = 0;
					foreach($user_list_data as $user_info){
					$temp_count_data++;
					?>
					<tr class="wooexim_user_filter_data_row">
						<td class="wooexim_user_filter_data_column"><?php echo $temp_count_data;?></td>
						<?php    foreach($user_field_list['user_field'] as $user_field){
								
									if($user_field['field_display']==1){?>
										
											<td class="wooexim_user_filter_data_column"><?php echo $user_info->$user_field['field_key'];?></td>
											
									<?php }
								
								}
								?>
					</tr>
				   <?php }?>
				</tbody>
			</table>
		</div>
		<?php
		
		$buffer_output = ob_get_contents();
		
		ob_end_clean();
		
		$return_value = array();
		
		
		$return_value['message']	= 'success';
		
		$return_value['data']		= $buffer_output ;
				
		echo json_encode($return_value );
		
		die();
	}
	function wooexim_get_user_data($wooexim_data = array())
	{
		
		global $wooexim_user,$advanced_export, $wpdb;
		
		$blog_id = get_current_blog_id();
		
		$user_ids 					= isset($wooexim_data['wooexim_user_id'])?$wooexim_data['wooexim_user_id']:array();
		
		$user_role					= isset($wooexim_data['wooexim_user_role'])?$wooexim_data['wooexim_user_role']:array();
		
		$user_total_record			= isset($wooexim_data['wooexim_total_records'])?$wooexim_data['wooexim_total_records']:"";
		
		$user_offset 				= isset($wooexim_data['wooexim_offset_records'])?$wooexim_data['wooexim_offset_records']:"";
		
		$temp_start_date 			= isset($wooexim_data['wooexim_start_date'])?$wooexim_data['wooexim_start_date']:"";
		
		$temp_end_date 				= isset($wooexim_data['wooexim_end_date'])?$wooexim_data['wooexim_end_date']:"";
		
		$user_min_spend 			= isset($wooexim_data['wooexim_user_min_spend'])?$wooexim_data['wooexim_user_min_spend']:0;
		
		$start_date = "";
		
		$end_date = "";
		
		if($temp_start_date!="")
		{
			$temp_start_date			= explode('-',$temp_start_date );
		
			$start_date 				= $temp_start_date[2].'-'.$temp_start_date[0].'-'.$temp_start_date[1];
		}
		if($temp_end_date!="")
		{		
			$temp_end_date				= explode('-',$temp_end_date );
			
			$end_date 					= $temp_end_date[2].'-'.$temp_end_date[0].'-'.$temp_end_date[1];
		}	
		
		$user_query = array();
		
		if($temp_end_date!="" || $temp_start_date!="")
		{
			$date_data = array();
			
			if($temp_end_date!="")
			{
				$date_data['before'] =  $end_date." 23:59:59";
			}
			if($temp_start_date!="")
			{
				$date_data['after'] =  $start_date." 00:00:00";
			}
			
			$date_data['inclusive'] = true;
			
			$user_query['date_query'] = array($date_data);
			
		}	
		
		if(!empty($user_ids))
		{
			$user_query['include'] = $user_ids;
		}
		
		if($user_total_record!="" && $user_total_record>0 )
		{
			$user_query['number'] = $user_total_record;
			
			if($user_offset!="" && $user_offset>0) 
			{
				$user_query['offset'] = $user_offset;
			}
		}
		
		$user_query['fields'] = 'all_with_meta';

		if(!empty($user_role))
		{
			if(count($user_role)==1)
			{
				$user_query['role'] = $user_role[0];
			}		
			else if(count($user_role) > 1)
			{
				
				$user_query['meta_query'] = array(array(
							'key' => $wpdb->get_blog_prefix($blog_id).'capabilities',
							'value' => '"('.implode('|',array_map('preg_quote',$user_role)).')"',
							'compare' => 'REGEXP'
					));
			
			}
		}
		if($user_min_spend>0)
		{
			$user_query['meta_query'][] = array(
							'key' => '_money_spent',
							'value' => $user_min_spend,
							'compare' => '>=',
					);
		}
		
		$user_query['orderby'] = 'ID';
		
		$user_query['order'] = 'ASC';
		
		$user_list = get_users($user_query);
						
		foreach($user_list as $new_user)
		{
			foreach($new_user->roles as $key=>$value)
			{
				$new_user->user_role = $value;
			}
			
			$new_user->wooexim_user_meta = @maybe_serialize( get_user_meta($new_user->ID));
			
			$new_user->wooexim_user_capabilities = @maybe_serialize( $new_user->allcaps);
		}
						
		return $user_list;
	}
	function get_user_csv_data($wooexim_data = array())
	{
	
		global $wooexim_user;
	
		$csv_data = "";
		
		$count = 0;
		
		$user_field_lists = $wooexim_user -> get_updated_user_fields();
				
		$user_list_data = $wooexim_user -> wooexim_get_user_data($wooexim_data);
		
		
		
		foreach($user_field_lists as $user_field_list)
		{
			foreach($user_field_list as $field_data)
			{
				if($field_data['field_display']==1){
					
						$csv_data[$count][] = $field_data['field_value'];
				}
			}
		}
		
		foreach($user_list_data as $user_info)
		{
			$count++;
			
			$data_result = array();
			
			foreach($user_field_lists as $user_field_list)
			{
				foreach($user_field_list as $field_data){
				
					if($field_data['field_display']==1){
				
						$data_result[] = $user_info->$field_data['field_key'];
						
					}
					
				}
			}
			
 			$csv_data[$count] = $data_result;
			
		}
			
		return $csv_data;
	}
	function save_user_scheduled()
 	{
		global $wooexim_scheduled;
		
		$general_options_data 		= $_POST;
		
		$wooexim_export_interval 			= isset($_POST['wooexim_export_interval'])?$_POST['wooexim_export_interval']:"";
		
		$return_value = array();
		
		if($wooexim_export_interval!="")
		{
		
			$scheduled_id = uniqid();
			
			$scheduled_data = $wooexim_scheduled -> get_user_scheduled_data();
			
			$scheduled_data[$scheduled_id] = $general_options_data;
			
			$scheduled_new_data = @maybe_serialize($scheduled_data);
			
			update_option('wooexim_user_scheduled_data',$scheduled_new_data);
						
			wp_schedule_event( time(), $wooexim_export_interval, 'wooexim_cron_scheduled_user_export',array($scheduled_id) );
			
			$return_value['message']		= 'success';
			
		}		
		else
		{
			$return_value['message']		= 'error';
		}
		
		echo json_encode($return_value );
		
		die();
	}	
	
	function wooexim_import_users()
	{
		global $wooexim_product, $wooexim_user, $wooexim_import_export;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$return_value = array();
		
		$return_value['message'] = 'error';
		
		$plugin_data = $wooexim_import_export->get_wooexim_sort_order();
		if( $plugin_data['plugin_status']!='active' ){
			$return_value['message_text'] = __('Please activate plugin license.',WOOEXIM_TEXTDOMAIN);
			echo json_encode($return_value );
			die();
		}
		
		$file_url = isset($_POST['wooexim_import_file_url'])?$_POST['wooexim_import_file_url']:"";
		
		$user_field_list = $wooexim_user -> get_updated_user_fields();
		 
		$file_path = "";
		
		if(isset($_FILES['wooexim_import_file']['name']) && $_FILES['wooexim_import_file']['name']!="")
		{
			$file_name = time().'_'.$_FILES['wooexim_import_file']['name'];
			 
			if(move_uploaded_file($_FILES['wooexim_import_file']['tmp_name'],WOOEXIM_UPLOAD_DIR.'/'.$file_name ))
			{
			
				$file_path = WOOEXIM_UPLOAD_DIR.'/'.$file_name;
			}
			else
			{
			
				$return_value['message_text'] = __('File Not Uploaded. Please check file size for exceeds the limit.',WOOEXIM_TEXTDOMAIN);
				
				echo json_encode($return_value );
		
				die();
			}
			
		}
		else if($file_url!="")
		{
			$file_path = $file_url;
		}
		
		if($file_path!="")
		{
			
			$fh = @fopen($file_path, 'r' );
			
			$import_data = array();
			 
			if ( $fh !== FALSE ) { 
			
				while ( ( $new_line = fgetcsv($fh, 0) ) !== FALSE ) {
					
               		$import_data_temp[] = $new_line;
				}
				fclose( $fh );

 				unset($import_data_temp[0]);
				
				$count = 0;
				
 				foreach($import_data_temp as $data)
				{
					foreach($data as $key =>$value )
					{
						$import_data[$count][$user_field_list['user_field'][$key]['field_key']] = $value;
						
 					}
					$count++;
				}
  				
				$return_value['message'] = 'success' ;
				
				$return_value['success_log'] = '<div class="wooexim_success_msg wooexim_user_import_success_msg">'.__('User Imported successfully.',WOOEXIM_TEXTDOMAIN).'</div>';
				
			}else{
			
 				$return_value['message_text'] = __( 'Could not open file.', WOOEXIM_TEXTDOMAIN );
			}
			
			if(!empty($import_data))
			{
				$user_updated_data = $wooexim_user -> wooexim_create_new_user($import_data);
				
				$return_value['data'] = @$user_updated_data['data'];
				
				$return_value['error_log'] = $wooexim_user -> set_user_import_errors($user_updated_data['user_log']);
 			}
		}
		
		echo json_encode($return_value );
		
		die();
		
	}
	function wooexim_create_new_user($user_data = array())
	{
		global $wooexim_product, $wooexim_user, $wpdb;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$user_create_method = isset($_POST['wooexim_user_create_option'])?$_POST['wooexim_user_create_option']:"";
		
		$imported_ids = array();
		
		$wooexim_user_log = array();
		
		foreach($user_data as $user_info)
		{
			$existing_user = get_user_by('email',$user_info['user_email']);
			
			$new_user_id = 0;
			
			if(!$existing_user)
			{
				$existing_user = get_user_by('login',$user_info['user_login'] );
			}
			
			if($user_create_method == 'skip_user' && $existing_user)
			{
				$imported_ids['wooexim_user_id'][] = $existing_user->ID;
					
				$wooexim_user_log[] = sprintf( __( 'User #%s %s already Exist.', WOOEXIM_TEXTDOMAIN ), $existing_user->ID,$existing_user->user_email);
				
				continue;
			}
			else if($user_create_method == 'update_user' && $existing_user)
			{
				
				$new_user_data = array(
										'ID'	=> $existing_user->ID,
										'user_login' => $user_info['user_login'],
										'user_url' => $user_info['user_url'],
										'user_pass' =>  $user_info['user_pass'],
										'user_email' =>  $user_info['user_email'],
										'user_registered' =>  $user_info['user_registered'],
										'role' =>  $user_info['user_role'],
									);
				
				$new_user_id = wp_update_user($new_user_data);
				
				if($new_user_id)
				{						
					$wpdb->update($wpdb->users, array('user_pass' => $user_info['user_pass']), array('ID' => $new_user_id) );

					wp_cache_delete($new_user_id, 'users');
				}
			}
			else
			{
				if($existing_user)
				{
					$imported_ids['wooexim_user_id'][] = $existing_user->ID;
					
					$wooexim_user_log[] = sprintf( __( 'User #%s %s already Exist.', WOOEXIM_TEXTDOMAIN ), $existing_user->ID,$existing_user->user_email);
					
					continue;
				}
				else
				{
					$new_user_data = array(
										'user_login' => $user_info['user_login'],
										'user_url' => $user_info['user_url'],
										'user_pass' =>  $user_info['user_pass'],
										'user_email' =>  $user_info['user_email'],
										'user_registered' =>  $user_info['user_registered'],
										'role' =>  $user_info['user_role'],
									);
					
					$new_user_id = wp_insert_user($new_user_data);
					
					if($new_user_id)
					{						
						$wpdb->update($wpdb->users, array('user_pass' => $user_info['user_pass']), array('ID' => $new_user_id) );

						wp_cache_delete($new_user_id, 'users');
  					}
				}
			}
			if($new_user_id !="" && $new_user_id > 0)
			{
				$imported_ids['wooexim_user_id'][] = $new_user_id;
				
				$new_user = new WP_User($new_user_id);
				
				$new_user_meta = @maybe_unserialize($user_info['wooexim_user_meta']);
				
				if(!empty($new_user_meta))
				{
					foreach($new_user_meta as $meta_key=>$meta_value)
					{
						foreach($meta_value as $key=>$value)
						{
							@update_user_meta($new_user_id,$meta_key,$value);
						}
					}
				}
				
				$new_user_cap = @maybe_unserialize($user_info['wooexim_user_capabilities']);
				
				if(!empty($new_user_cap))
				{
					foreach($new_user_cap as $key=>$value)
					{
						$new_user -> add_cap($key);
					}
				}
				
			}
			
		}
		
		$user_all_data  = array();
		
		$user_all_data['data'] = $wooexim_user -> get_imported_users($imported_ids);
		
		$user_all_data['user_log'] = $wooexim_user_log;
		
		return $user_all_data;
	}
	function get_imported_users($imported_ids)
 	{
		global $wooexim_user;
	
		$user_field_list = $wooexim_user -> get_updated_user_fields();
		
		$user_list_data = $wooexim_user -> wooexim_get_user_data($imported_ids);
		
 		ob_start();
		
		?>
		<div class="wooexim_filter_data_container">
			<table class="wooexim_user_filter_data">
				<thead>
					<tr class="wooexim_user_filter_data_row">
						<th class="wooexim_user_filter_data_header"></th>
					<?php   foreach($user_field_list['user_field'] as $user_field){
							
								if($user_field['field_display']==1){?>
								
									<th class="wooexim_user_filter_data_header"><?php echo $user_field['field_value'];?></th>
									
								<?php 
								}
							}
  					?>
					</tr>
				</thead>
				<tbody>
					<?php 
					$temp_count_data = 0;
					foreach($user_list_data as $user_info){
					$temp_count_data++;
					?>
					<tr class="wooexim_user_filter_data_row">
						<td class="wooexim_user_filter_data_column"><?php echo $temp_count_data;?></td>
						<?php    foreach($user_field_list['user_field'] as $user_field){
								
									if($user_field['field_display']==1){?>
										
											<td class="wooexim_user_filter_data_column"><?php echo $user_info->$user_field['field_key'];?></td>
											
									<?php }
								
								}
								?>
					</tr>
				   <?php }?>
				</tbody>
			</table>
		</div>
		<?php
		
		$buffer_output = ob_get_contents();
		
		ob_end_clean();
		
		return $buffer_output ;
	}
	function set_user_import_errors($user_errors = array())
	{
		
		
		$final_output = "";
		
		if(!empty($user_errors))
		{
			ob_start();
			
			if(!empty($user_errors))
			{
				foreach($user_errors as $wooexim_error)
				{
					?>
					<div class="wooexim_error_msg wooexim_user_import_error_msg"><?php echo $wooexim_error;?></div>
					<?php
				}
			}
			
			$final_output = ob_get_contents();
			
			ob_end_clean();
		}
		
		return $final_output ;
	}
}
?>