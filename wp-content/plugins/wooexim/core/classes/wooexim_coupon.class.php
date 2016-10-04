<?php

if (!defined('ABSPATH'))
    die("Can't load this file directly");
	
class wooexim_coupon{

	function wooexim_coupon()
	{
		add_action( 'wp_ajax_wooexim_get_filter_coupon_results', array( &$this, 'wooexim_get_filter_coupon_results' ));
		
		add_action( 'wp_ajax_save_coupon_scheduled', array( &$this, 'save_coupon_scheduled' ));
		
		add_action( 'wp_ajax_wooexim_save_coupon_fields', array( &$this, 'wooexim_save_coupon_fields' ));
		
		add_action( 'wp_ajax_wooexim_import_coupon', array( &$this, 'wooexim_import_coupon' ));
		
 	}
	function get_coupon_list()
	{		
		$query_args = array(
		
						'posts_per_page' 	=> 2000,
						
						'post_type'   		=> 'shop_coupon',
						
						'post_status' 		=> 'publish',
						
						'orderby' 			=> 'ID',
						
						'order' 			=> 'ASC',
						
						);
								
		$coupon_list = get_posts($query_args);
		
		return $coupon_list;
	}
	function wooexim_get_filter_coupon_results()
	{
		global $wooexim_product, $wooexim_product_cat, $wooexim_coupon;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$coupon_list = $wooexim_coupon -> get_filter_coupon($_POST);
		
		$coupon_fields = $wooexim_coupon -> get_updated_coupon_fields();
		
		ob_start();
		
		?>
		
		<div class="wooexim_filter_data_container">
		
			<table class="wooexim_coupon_filter_data">
				<thead>
					<tr>
						<th></th>
						<?php foreach($coupon_fields['coupon_fields'] as $coupon_info){?>
							<th><?php echo $coupon_info['field_value'];?></th>
						<?php }?>
					</tr>
				</thead>
				<tbody>
					<?php 
					if(!empty($coupon_list)){
						$count = 0;
						
						foreach($coupon_list as $coupon_data){
							$count++;
							?>
							<tr>
								<td><?php echo $count;?></td>
								<?php 
									foreach($coupon_fields['coupon_fields'] as $coupon_info){
										?>
										<td><?php echo isset($coupon_data->$coupon_info['field_key'])?$coupon_data->$coupon_info['field_key']:"";?></td>
										<?php
										 
								}?>
							</tr>
							<?php 
						}
					}
					?>
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
	
	function get_filter_coupon($wooexim_data)
	{
		global $wooexim_product, $wooexim_coupon;
		
		$wooexim_product->wooexim_set_time_limit(0);
				
		$coupon_ids		= isset($wooexim_data['wooexim_coupon_ids'])?$wooexim_data['wooexim_coupon_ids']:array();
				
		$total_records		= isset($wooexim_data['wooexim_total_records'])?$wooexim_data['wooexim_total_records']:"";
		
		$offset_records 	= isset($wooexim_data['wooexim_offset_records'])?$wooexim_data['wooexim_offset_records']:"";
		
		$temp_start_date 	= isset($wooexim_data['wooexim_start_date'])?$wooexim_data['wooexim_start_date']:"";
		
		$temp_end_date 		= isset($wooexim_data['wooexim_end_date'])?$wooexim_data['wooexim_end_date']:"";
		
		$end_date           = "";
		
		$start_date         = ""; 
		
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
		
		
		$query_args = array(
		
						'posts_per_page' 	=> -1,
						
						'post_type'   		=> 'shop_coupon',
						
						'orderby' 			=> 'ID',
						
						'order' 			=> 'ASC',
																	
					);
		
		
		if(!empty($coupon_ids))
		{
			$query_args['post__in'] = $coupon_ids;
		}
		
		if($total_records!="" && $total_records>0 )
		{
			$query_args['posts_per_page'] = $total_records;
			
			if($offset_records!="" && $offset_records>=0) 
			{
				$query_args['offset'] = $offset_records;
			}
		}
		
		if($end_date!="" || $start_date!="")
		{
			$date_data = array();
			
			if($end_date!="")
			{
				$date_data['before'] =  $end_date." 23:59:59";
			}
			
			if($start_date!="")
			{
				$date_data['after'] =  $start_date." 00:00:00";
			}
			
			$date_data['inclusive'] = true;
			
			$query_args['date_query'] = array($date_data);
			
		}
		
		$coupon_results = new WP_Query( $query_args );
		
		$coupon_list = $coupon_results->get_posts();
		
		if(!empty($coupon_list))
		{
			foreach($coupon_list as $new_coupon)
			{
				$coupon_meta = get_post_meta( $new_coupon->ID );
				
				if(isset($coupon_meta['product_ids'][0]) && $coupon_meta['product_ids'][0] != "")
				{
					$coupon_product_ids = $wooexim_coupon -> get_coupons_product_sku($coupon_meta['product_ids'][0]);
				}
				else
				{
					$coupon_product_ids = "";
				}
				if(isset($coupon_meta['exclude_product_ids'][0]) && $coupon_meta['exclude_product_ids'][0] != "")
				{
					$exclude_product_ids = $wooexim_coupon -> get_coupons_product_sku($coupon_meta['exclude_product_ids'][0]);
				}
				else
				{
					$exclude_product_ids = "";
				}
				if(isset($coupon_meta['product_categories'][0]) && $coupon_meta['product_categories'][0] != "")
				{
					$coupon_product_categories = $wooexim_coupon -> get_product_categories_slug($coupon_meta['product_categories'][0]);
				}
				else
				{
					$coupon_product_categories = "";
				}
				if(isset($coupon_meta['exclude_product_categories'][0]) && $coupon_meta['exclude_product_categories'][0] != "")
				{
					$exclude_product_categories = $wooexim_coupon -> get_product_categories_slug($coupon_meta['exclude_product_categories'][0]);
				}
				else
				{
					$exclude_product_categories = "";
				}
				
				$coupon_meta['coupon_product_ids'][0] = $coupon_product_ids;
				
				$coupon_meta['coupon_exclude_product_ids'][0] = $exclude_product_ids;
				
				$coupon_meta['coupon_product_categories'][0] = $coupon_product_categories;
				
				$coupon_meta['coupon_exclude_product_categories'][0] = $exclude_product_categories;
				
				$new_coupon -> coupon_attributes = maybe_serialize($coupon_meta);
				
				$new_coupon -> coupon_amount = $coupon_meta['coupon_amount'][0];
				
				$new_coupon -> expiry_date = $coupon_meta['expiry_date'][0];
				
				$new_coupon -> discount_type = $coupon_meta['discount_type'][0];
				
				$new_coupon -> individual_use = $coupon_meta['individual_use'][0];
				
				$new_coupon -> product_ids = $coupon_meta['product_ids'][0];
				
				$new_coupon -> usage_limit = $coupon_meta['usage_limit'][0];
				
				$new_coupon -> free_shipping = $coupon_meta['free_shipping'][0];
			}
		}
		
		
		wp_reset_postdata();
		
		return $coupon_list;
	}
	function get_coupon_fields()
	{
		$get_coupon_fields = array(
					
						'coupon_fields' => array(
										 array(
											'field_key' => 'ID',
											'field_display' => 1,
											'field_title' =>'Id',
											'field_value' =>'Id', 
										),
										array(
											'field_key' => 'post_title',
											'field_display' => 1,
											'field_title' =>'Code',
											'field_value' =>'Code',
										),
										array(
											'field_key' => 'coupon_amount',
											'field_display' => 1,
											'field_title' =>'Coupon Amount',
											'field_value' =>'Coupon Amount',
										),
										array(
											'field_key' => 'post_date',
											'field_display' => 1,
											'field_title' =>'Created Date',
											'field_value' =>'Created Date',
										),
										array(
											'field_key' => 'expiry_date',
											'field_display' => 1,
											'field_title' =>'Expiry Date',
											'field_value' =>'Expiry Date',   
										),
										array(
											'field_key' => 'post_excerpt',
											'field_display' => 1,
											'field_title' =>'Description',
											'field_value' =>'Description',
										),
										array(
											'field_key' => 'discount_type',
											'field_display' => 1,
											'field_title' =>'Discount Type',
											'field_value' =>'Discount Type',
										),
										array(
											'field_key' => 'post_type',
											'field_display' => 1,
											'field_title' =>'Post Type',
											'field_value' =>'Post Type',  
										),
										array(
											'field_key' => 'post_name',
											'field_display' => 1,
											'field_title' =>'Coupon Name',
											'field_value' =>'Coupon Name', 
										),
										array(
											'field_key' => 'individual_use',
											'field_display' => 1,
											'field_title' =>'Individual Use',
											'field_value' =>'Individual Use', 
										),
										array(
											'field_key' => 'product_ids',
											'field_display' => 1,
											'field_title' =>'Product Ids',
											'field_value' =>'Product Ids',   
										),
										array(
											'field_key' => 'usage_limit',
											'field_display' => 1,
											'field_title' =>'Usage Limit',
											'field_value' =>'Usage Limit',   
										),
										array(
											'field_key' => 'free_shipping',
											'field_display' => 1,
											'field_title' =>'Free Shipping',
											'field_value' =>'Free Shipping',   
										),
										array(
											'field_key' => 'ping_status',
											'field_display' => 1,
											'field_title' =>'Ping Status',
											'field_value' =>'Ping Status',   
										),
										array(
											'field_key' => 'post_status',
											'field_display' => 1,
											'field_title' =>'Post Status',
											'field_value' =>'Post Status',
										),
  										array(
											'field_key' => 'coupon_attributes',
											'field_display' => 1,
											'field_title' =>'Attributes',
											'field_value' =>'Attributes',  
										),
										array(
											'field_key' => 'post_parent',
											'field_display' => 1,
											'field_title' =>'Product Parent id',
											'field_value' =>'Product Parent id',  
										),
										array(
											'field_key' => 'menu_order',
											'field_display' => 1,
											'field_title' =>'Menu Order',
											'field_value' =>'Menu Order',  
										),
										array(
											'field_key' => 'comment_status',
											'field_display' => 1,
											'field_title' =>'Comment Status',
											'field_value' =>'Comment Status',  
										),
										array(
											'field_key' => 'ping_status',
											'field_display' => 1,
											'field_title' =>'Ping Status',
											'field_value' =>'Ping Status',  
										),
										
									),
								);
								
		return $get_coupon_fields;
	}
	function get_updated_coupon_fields()
	{
		global $wooexim_coupon;
		
		$old_coupon_fields = $wooexim_coupon -> get_new_coupon_fields();
		
		$new_fields = get_option('wooexim_coupon_fields',$old_coupon_fields);
		
		$new_fields = maybe_unserialize($new_fields);
		
		return $new_fields;
	}
	
	function get_new_coupon_fields()
	{
		global $wooexim_coupon;
		
		$coupon_fields = maybe_serialize($wooexim_coupon -> get_coupon_fields());
		
		return $coupon_fields;
	}
	function get_coupon_export_data($wooexim_data = array())
 	{
		global $wooexim_product, $wooexim_coupon;
	
		$wooexim_product->wooexim_set_time_limit(0);
		
		$csv_data = "";
		
		$coupon_field_list = $wooexim_coupon -> get_updated_coupon_fields();
				
		$coupon_list_data = $wooexim_coupon -> get_filter_coupon($wooexim_data);
		
		$count = 0;
		
		foreach($coupon_field_list['coupon_fields'] as $field_data)
		{
			if($field_data['field_display']==1){
				
					$csv_data[$count][] = $field_data['field_value'];
			}
				
		}
		
		foreach($coupon_list_data as $coupon_info)
		{
			$count++;
			
			$data_result = array();
			
			foreach($coupon_field_list['coupon_fields'] as $field_data){
			
				
				if($field_data['field_display']==1){
			
					$data_result[] = isset($coupon_info->$field_data['field_key'])?$coupon_info->$field_data['field_key']:"";
					
				}
				
			}
			
			$csv_data[$count] = $data_result;
		}
			
		return $csv_data;
	}
	function save_coupon_scheduled()
 	{
		global $wooexim_scheduled;
		
		$general_options_data = $_POST;
		
		$wooexim_export_interval = isset($_POST['wooexim_export_interval'])?$_POST['wooexim_export_interval']:"";
		
		$return_value = array();
		
		if($wooexim_export_interval!="")
		{
		
			$scheduled_id = uniqid();
			
			$scheduled_data = $wooexim_scheduled -> get_coupon_scheduled_data();
			
			$scheduled_data[$scheduled_id] = $general_options_data;
			
			$scheduled_new_data = @maybe_serialize($scheduled_data);
			
			update_option('wooexim_coupon_scheduled_data',$scheduled_new_data);
						
			wp_schedule_event( time(), $wooexim_export_interval, 'wooexim_cron_scheduled_coupon_export',array($scheduled_id) );
			
			$return_value['message']		= 'success';
			
		}		
		else
		{
			$return_value['message']		= 'error';
		}
		
		echo json_encode($return_value );
		
		die();
	}
	function wooexim_save_coupon_fields()
	{
		global $wooexim_coupon;
		
		$old_coupon_fields = $wooexim_coupon -> get_updated_coupon_fields();
		
		$new_fields = array();
		
		foreach($old_coupon_fields as $coupon_fields_key=>$coupon_fields_data)
		{
 			foreach($coupon_fields_data as $key=>$value)
			{
				$new_fields[$coupon_fields_key][$key]['field_key'] = $value['field_key'];
				
				$new_fields[$coupon_fields_key][$key]['field_display'] = $value['field_display'];
				
				$new_fields[$coupon_fields_key][$key]['field_title'] = $value['field_title'];
				
				$new_fields[$coupon_fields_key][$key]['field_value'] = isset($_POST['wooexim_'.$value['field_key'].'_field'])?$_POST['wooexim_'.$value['field_key'].'_field']:"";
				
  			}
		}
		
		$new_fields_data = maybe_serialize($new_fields);
		
		update_option('wooexim_coupon_fields', $new_fields_data);
		
		$return_value = array();
		
		$return_value['message'] = 'success';
		
		$return_value['message_content'] = __('Changes Saved Successfully.',WOOEXIM_TEXTDOMAIN);
			
		echo json_encode($return_value );
		
		die();
	}
	function wooexim_import_coupon()
	{
		global $wooexim_product, $wooexim_coupon, $wooexim_import_export;
		
		$wooexim_product -> wooexim_set_time_limit(0);
		
		$return_value = array();
		
		$return_value['message'] = 'error';
		
		$plugin_data = $wooexim_import_export->get_wooexim_sort_order();
		if( $plugin_data['plugin_status']!='active' ){
			$return_value['message_text'] = __('Please activate plugin license.',WOOEXIM_TEXTDOMAIN);
			echo json_encode($return_value );
			die();
		}
		
		$file_url = isset($_POST['wooexim_import_file_url'])?$_POST['wooexim_import_file_url']:"";
		
		$coupon_field_list = $wooexim_coupon -> get_updated_coupon_fields();
		 
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
						$import_data[$count][$coupon_field_list['coupon_fields'][$key]['field_key']] = $value;
						
 					}
					$count++;
				}
  				
				$return_value['message'] = 'success' ;
				
				$return_value['success_log'] = '<div class="wooexim_success_msg wooexim_coupon_import_success_msg">'.__('Coupons Imported successfully.',WOOEXIM_TEXTDOMAIN).'</div>';
				
			}else{
			
 				$return_value['message_text'] = __( 'Could not open file.', WOOEXIM_TEXTDOMAIN );
			}
			if(!empty($import_data))
			{
				
				$coupon_updated_data = $wooexim_coupon -> wooexim_create_new_coupon($import_data);
				
				$return_value['data'] = @$coupon_updated_data['data'];
				
				$return_value['coupon_log'] = $wooexim_coupon -> set_coupon_import_errors($coupon_updated_data['coupon_log']);
 			}
		}
		
		echo json_encode($return_value );
		
		die();
		
	}	
	function wooexim_create_new_coupon($coupon_data = array())
	{
		global $wooexim_product, $wooexim_coupon;
		
		$wooexim_product -> wooexim_set_time_limit(0);
		
		$imported_ids = array();
		
		$wooexim_coupon_log = array();
  		
		$wooexim_coupon_create_method = isset($_POST['wooexim_coupon_create_method'])?$_POST['wooexim_coupon_create_method']:"";
		
		$cat_new_old_id_list = array(); 
		
		$parent_id_list = array();
				
		foreach($coupon_data as $coupon_info)
		{
 			
			$current_coupon_id = 0;
			
			if(isset($coupon_info['post_title']) && $coupon_info['post_title']!="")
			{
				$current_coupon_id = $wooexim_coupon -> get_coupon_id_from_code($coupon_info['post_title']);
			}
			
			if(($wooexim_coupon_create_method == 'update_coupon' || $wooexim_coupon_create_method == 'skip_coupon') && $current_coupon_id!="" && $current_coupon_id>0) 
			{				
				if( $wooexim_coupon_create_method == 'skip_coupon')
				{
					$imported_ids['wooexim_coupon_ids'][] = $current_coupon_id;
					
					$wooexim_coupon_log[] = sprintf( __( 'Coupon Code %s already Exist.', WOOEXIM_TEXTDOMAIN ), $coupon_info['post_title']);
									
					continue;
				}
				
				if($wooexim_coupon_create_method == 'update_coupon')
				{
					$update_coupon_data = array(
					
							'ID'		=>  $coupon_info['ID'],
							
							'post_name' =>  $coupon_info['post_name'],
							
							'post_type' =>  $coupon_info['post_type'],
							
							'post_title' =>  $coupon_info['post_title'],
							
							'post_status'=>  $coupon_info['post_status'],
							
							'ping_status' =>  $coupon_info['ping_status'],
							
							'post_excerpt' =>  $coupon_info['post_excerpt'],
							
							'post_date' =>  $coupon_info['post_date'],
							
						);
						
					$new_coupon_id = wp_update_post($update_coupon_data, false);
				}
			}
			
			if($current_coupon_id == 0 )
			{
				
				$new_coupon_data = array(
					
							'post_name' =>  $coupon_info['post_name'],
							
							'post_type' =>  $coupon_info['post_type'],
							
							'post_title' =>  $coupon_info['post_title'],
							
							'post_status'=>  $coupon_info['post_status'],
							
							'ping_status' =>  $coupon_info['ping_status'],
							
							'post_excerpt' =>  $coupon_info['post_excerpt'],
							
							'post_date' =>  $coupon_info['post_date'],
							
						);
						
				$current_coupon_id = wp_insert_post($new_coupon_data, false);
					
			}
			
			if($current_coupon_id > 0 && $coupon_info['coupon_attributes']!="")
			{
				$coupon_attributes = maybe_unserialize($coupon_info['coupon_attributes']);
				
				$imported_ids['wooexim_coupon_ids'][] = $current_coupon_id;
				
				if(!empty($coupon_attributes))
				{
					foreach($coupon_attributes as $key => $value)
					{
						
						if($key=='product_ids' || $key=='exclude_product_ids')
						{
						
							$product_new_ids = array();
							
							if($key=='product_ids' && isset($coupon_attributes['coupon_product_ids'][0]) && $coupon_attributes['coupon_product_ids'][0]!="")
							{
								$product_new_ids = maybe_unserialize($coupon_attributes['coupon_product_ids'][0]);
							}
							else if($key=='exclude_product_ids' && isset($coupon_attributes['coupon_exclude_product_ids'][0]) && $coupon_attributes['coupon_exclude_product_ids'][0]!="")
							{
								$product_new_ids = maybe_unserialize($coupon_attributes['coupon_exclude_product_ids'][0]);
							}
							
							if(is_array($product_new_ids) && !empty($product_new_ids))
							{
								$existing_post_query = array(
					
									'posts_per_page' 	=> 1000,
									
									'meta_query' => array(
									
										array(
										
											'key'=>'_sku',
											
											'value'=> array_values($product_new_ids),
											
											'compare' => 'IN'
										),
										
									),
									'post_type' => 'product',
									
									'fields' 	=> 'ids',
									
									);
									
								$existing_product = get_posts($existing_post_query);
								
								if(!empty($existing_product))
								{
									$updated_new_product_ids = @implode(',',array_values($existing_product));
								}
							}
							
							update_post_meta($current_coupon_id, $key ,$updated_new_product_ids);
						}
						else if(($key=='product_categories' || $key=='exclude_product_categories'))
						{
						
							$product_cat_data_new = "";
							
							if($key=='product_categories' && isset($coupon_attributes['coupon_product_categories'][0]) && $coupon_attributes['coupon_product_categories'][0]!="")
							{
								$product_cat_data_new = maybe_unserialize($coupon_attributes['coupon_product_categories'][0]);
							}
							else if($key=='exclude_product_ids' && isset($coupon_attributes['coupon_exclude_product_categories'][0]) && $coupon_attributes['coupon_exclude_product_categories'][0]!="")
							{
								$product_cat_data_new = maybe_unserialize($coupon_attributes['coupon_exclude_product_categories'][0]);
							}
						
							$updated_new_cat_ids =  maybe_unserialize($value[0]);
							
							$product_cat_data_new = maybe_unserialize($value[0]);
							
							if(is_array($product_cat_data_new) && !empty($product_cat_data_new))
							{
								$updated_new_cat = array();
								
								foreach($product_cat_data_new as $new_keys=>$new_values)
								{
									if($new_values!="")
									{
										$new_cat_data =  get_term_by('slug',$new_values,'product_cat');
										
										if(isset($new_cat_data->id) && $new_cat_data->id!="")
										{
											$updated_new_cat[] = $new_cat_data->id;
										}
									}
								}
								if(!empty($updated_new_cat))
								{
									$updated_new_cat_ids = @implode(',',$updated_new_cat);
								}
							}
							
							update_post_meta($current_coupon_id, $key ,$updated_new_cat_ids);
						}
						else if($key=='customer_email')
						{
							update_post_meta($current_coupon_id, $key ,maybe_unserialize(maybe_unserialize($value[0])));
						}
						else if($key!='coupon_product_ids' && $key!='coupon_exclude_product_ids' && $key!='coupon_product_categories' && $key!='coupon_exclude_product_categories')
						{
							update_post_meta($current_coupon_id, $key ,$value[0]);
						}
					}
				}
			}
		}
		
		$product_all_data  = array();
		
		$product_all_data['data'] = $wooexim_coupon -> get_imported_coupon($imported_ids);
		
		$product_all_data['coupon_log'] = $wooexim_coupon_log;
		
		return $product_all_data;
		
	}
	function get_coupon_id_from_code( $code ) {
	
		global $wpdb;

		return absint( $wpdb->get_var( $wpdb->prepare( apply_filters( 'woocommerce_coupon_code_query', "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish'" ), $code ) ) );
	}
	function get_imported_coupon($coupon_ids = array())
	{
		global $wooexim_product, $wooexim_coupon;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$coupon_list = $wooexim_coupon -> get_filter_coupon($coupon_ids);
		
		$coupon_fields = $wooexim_coupon -> get_updated_coupon_fields();
		
		ob_start();
		
		?>
		
		<div class="wooexim_filter_data_container">
		
			<table class="wooexim_coupon_filter_data">
				<thead>
					<tr>
						<th></th>
						<?php foreach($coupon_fields['coupon_fields'] as $coupon_info){?>
							<th><?php echo $coupon_info['field_value'];?></th>
						<?php }?>
					</tr>
				</thead>
				<tbody>
					<?php 
					if(!empty($coupon_list)){
						$count = 0;
						
						foreach($coupon_list as $coupon_data){
							$count++;
							?>
							<tr>
								<td><?php echo $count;?></td>
								<?php 
									foreach($coupon_fields['coupon_fields'] as $coupon_info){
										?>
										<td><?php echo isset($coupon_data->$coupon_info['field_key'])?$coupon_data->$coupon_info['field_key']:"";?></td>
										<?php
										 
								}?>
							</tr>
							<?php 
						}
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
		
		$buffer_output = ob_get_contents();
		
		ob_end_clean();
		
		return  $buffer_output ;
	}
	function set_coupon_import_errors($coupon_errors = array())
	{
		$coupon_all_errors =array();
		
		if(!empty($coupon_errors))
		{
			foreach($coupon_errors as $coupon_error)
			{
				if(is_array($coupon_error))
				{
					foreach($coupon_error as $coupon_sub_error)
					{
						if(is_array($coupon_sub_error))
						{
							foreach($coupon_sub_error as $coupon_sub1_error)
							{
								if($coupon_sub1_error!="" && !is_array($coupon_sub1_error))
								{
									$coupon_all_errors[] = $coupon_sub1_error;
								}
							}
						}
						else if($coupon_sub_error!="")
						{
							$coupon_all_errors[] = $coupon_sub_error;
						}
					}
				}
				else if($coupon_error!="")
				{
					$coupon_all_errors[] = $coupon_error;
				}
			 
			}
		}
		ob_start();
		
		
		if(!empty($coupon_all_errors))
		{
			foreach($coupon_all_errors as $wooexim_error)
			{
				?>
				<div class="wooexim_error_msg wooexim_import_coupon_error_log"><?php echo $wooexim_error;?></div>
				<?php
			}
		}
		
		$buffer_output = ob_get_contents();
		
		ob_end_clean();
		
		return $buffer_output ;
	}
	function get_coupons_product_sku($product_list = "")
	{
		$product_data = array();
		
		if($product_list!="")
		{
			$product_ids = explode(',',$product_list);
			
			if(!empty($product_ids))
			{
				foreach($product_ids as $product_id)
				{
					$new_product_sku = get_post_meta($product_id,'_sku',true);
					
					if($new_product_sku!="")
					{
						$product_data[$product_id] = $new_product_sku;
					}
				}
			}
		}
		return maybe_serialize($product_data);
	}
	function get_product_categories_slug($categories_list = "")
	{
		$categories_data = array();

		$categories_ids = maybe_unserialize(maybe_unserialize($categories_list));

		if(!empty($categories_ids))
		{
			foreach($categories_ids as $categories_id)
			{
				if(!isset($categories_data[$categories_id]) || $categories_data[$categories_id] =="")
				{
					$new_category = get_term_by('id',$categories_id,'product_cat');
					
					if(isset($new_category->slug) && $new_category->slug!="")
					{
						$categories_data[$categories_id] = $new_category->slug;
					}
				}
			}
		}
		
		return maybe_serialize($categories_data);
	}
}
?>