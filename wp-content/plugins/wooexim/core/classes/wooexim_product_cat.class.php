<?php

if (!defined('ABSPATH'))
    die("Can't load this file directly");
	
class wooexim_product_cat
{

	function wooexim_product_cat()
	{
				
 		add_action( 'wp_ajax_wooexim_get_filter_product_cat_results', array( &$this, 'wooexim_get_filter_product_cat_results' ));
		
		add_action( 'wp_ajax_save_product_cat_scheduled', array( &$this, 'save_product_cat_scheduled' ));
		
		add_action( 'wp_ajax_wooexim_save_product_cat_fields', array( &$this, 'wooexim_save_product_cat_fields' ));
		
		add_action( 'wp_ajax_wooexim_import_products_cat', array( &$this, 'wooexim_import_products_cat' ));
						
   	} 
	function wooexim_get_filter_product_cat_results()
	{
		global $wooexim_product, $wooexim_product_cat;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$product_cat_list = $wooexim_product_cat -> get_filter_product_cat($_POST);
		
		$product_cat_fields = $wooexim_product_cat -> get_updated_product_cat_fields();
		
		ob_start();
		
		?>
		
		<div class="wooexim_filter_data_container">
		
			<table class="wooexim_product_cat_filter_data">
				<thead>
					<tr>
						<th></th>
						<?php foreach($product_cat_fields['product_cat_fields'] as $product_cat_info){?>
							<th><?php echo $product_cat_info['field_value'];?></th>
						<?php }?>
					</tr>
				</thead>
				<tbody>
					<?php 
					if(!empty($product_cat_list)){
						$count = 0;
						
						foreach($product_cat_list as $product_cat_data){
							$count++;
							?>
							<tr>
								<td><?php echo $count;?></td>
								<?php 
									foreach($product_cat_fields['product_cat_fields'] as $product_cat_info){
										?>
										<td><?php echo isset($product_cat_data->$product_cat_info['field_key'])?$product_cat_data->$product_cat_info['field_key']:"";?></td>
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
	function get_filter_product_cat($wooexim_data)
	{
		global $wooexim_product, $wooexim_product_cat;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$product_categories = isset($wooexim_data['wooexim_product_category'])?$wooexim_data['wooexim_product_category']:array();
		
		$total_records		= isset($wooexim_data['wooexim_total_records'])?$wooexim_data['wooexim_total_records']:"";
		
		$offset_records 	= isset($wooexim_data['wooexim_offset_records'])?$wooexim_data['wooexim_offset_records']:"";
		
		$query_args = array(
					
						'taxonomy'   => 'product_cat',
						
						'hide_empty' => false,
						
						'orderby' => 'id',
							
						'order' => 'ASC'
						
						
					);
			
		if(!empty($product_categories))
		{
			$query_args['include'] = $product_categories;
		}
		
		if($total_records!="" && $total_records>0 )
		{
			$query_args['number'] = $total_records;
			
			if($offset_records!="" && $offset_records>=0) 
			{
				$query_args['offset'] = $offset_records;
			}
		}
	
		$product_category = get_terms('product_cat',$query_args);
				
		$parent_ids = array();
		
		foreach($product_category as $new_category )
		{
			if( $new_category->parent != "" && $new_category->parent>0 )
			{
				$parent_ids[] = $new_category->parent;
			}
		}
		
		if(!empty($parent_ids))
		{
			$parents_query_args = array(
						
							'taxonomy'   => 'product_cat',
							
							'hide_empty' => false,
							
							'fields' => 'id=>slug',
							
							'include' => $parent_ids,
							
							'orderby' => 'id',
							
							'order' => 'ASC'
						);
			
			$parents_product_category = get_terms('product_cat',$parents_query_args);
			
			foreach($product_category as $new_category )
			{
				if( $new_category->parent != "" && $new_category->parent>0 )
				{
					$new_category->parent_slug = $parents_product_category[$new_category->parent];
				}
				
				$woocommerce_term_meta = get_metadata( 'woocommerce_term',$new_category->term_id);
				
				if(!empty($woocommerce_term_meta))
				{
				
					$new_category->woocommerce_term_meta = maybe_serialize($woocommerce_term_meta);
					
					if(isset($woocommerce_term_meta['thumbnail_id'][0]) && $woocommerce_term_meta['thumbnail_id'][0]!="" && $woocommerce_term_meta['thumbnail_id'][0]>0 )
					{
						
						$cat_thumbnail = wp_get_attachment_thumb_url($woocommerce_term_meta['thumbnail_id'][0] );
							
						
						if($cat_thumbnail!="")
						{
							$new_category->category_image = $cat_thumbnail;
						}
						else
						{	
							$new_category->category_image = "";
						}
					}
					else
					{	
						$new_category->category_image = "";
					}
				}
				else
				{
					$new_category->woocommerce_term_meta = "";
					
					$new_category->category_image = "";
				}
				
			}
		}
		else
		{
			foreach($product_category as $new_category )
			{
				
				$woocommerce_term_meta = get_metadata( 'woocommerce_term',$new_category->term_id);
				
				if(!empty($woocommerce_term_meta))
				{
				
					$new_category->woocommerce_term_meta = maybe_serialize($woocommerce_term_meta);
					
					if(isset($woocommerce_term_meta['thumbnail_id'][0]) && $woocommerce_term_meta['thumbnail_id'][0]!="" && $woocommerce_term_meta['thumbnail_id'][0]>0 )
					{
						
						$cat_thumbnail = wp_get_attachment_thumb_url($woocommerce_term_meta['thumbnail_id'][0] );
							
						
						if($cat_thumbnail!="")
						{
							$new_category->category_image = $cat_thumbnail;
						}
						else
						{	
							$new_category->category_image = "";
						}
					}
					else
					{	
						$new_category->category_image = "";
					}
				}
				else
				{
					$new_category->woocommerce_term_meta = "";
					
					$new_category->category_image = "";
				}
				
			}
		}
		
		return $product_category;
	}
	function get_new_product_cat_fields()
	{
		global $wooexim_product_cat;
		
		$product_cat_fields = maybe_serialize($wooexim_product_cat -> get_product_cat_fields());
		
		return $product_cat_fields;
	}
	function get_updated_product_cat_fields()
	{
		global $wooexim_product_cat;
		
		$old_product_cat_fields = $wooexim_product_cat -> get_new_product_cat_fields();
		
		$new_fields = get_option('wooexim_product_cat_fields',$old_product_cat_fields);
		
		$new_fields = maybe_unserialize($new_fields);
		
		return $new_fields;
	}
	function get_product_cat_fields()
	{
		$get_product_cat_fields = array(
					
						'product_cat_fields' => array(
										 array(
											'field_key' => 'term_id',
											'field_display' => 1,
											'field_title' =>'Id',
											'field_value' =>'Id', 
										),
										array(
											'field_key' => 'name',
											'field_display' => 1,
											'field_title' =>'Name',
											'field_value' =>'Name',
										),
										array(
											'field_key' => 'slug',
											'field_display' => 1,
											'field_title' =>'Slug',
											'field_value' =>'Slug',
										),
										array(
											'field_key' => 'term_taxonomy_id',
											'field_display' => 1,
											'field_title' =>'Term Taxonomy Id',
											'field_value' =>'Term Taxonomy Id',
										),
										array(
											'field_key' => 'taxonomy',
											'field_display' => 1,
											'field_title' =>'Taxonomy',
											'field_value' =>'Taxonomy',
										),
										array(
											'field_key' => 'parent',
											'field_display' => 1,
											'field_title' =>'Parent Id',
											'field_value' =>'Parent Id',
										),
										array(
											'field_key' => 'parent_slug',
											'field_display' => 1,
											'field_title' =>'Parent Slug',
											'field_value' =>'Parent Slug',
										),
										 array(
											'field_key' => 'description',
											'field_display' => 1,
											'field_title' =>'Description',
											'field_value' =>'Description', 
										),
										array(
											'field_key' => 'term_group',
											'field_display' => 1,
											'field_title' =>'Term Group',
											'field_value' =>'Term Group',
										),
										
										array(
											'field_key' => 'count',
											'field_display' => 1,
											'field_title' =>'Count',
											'field_value' =>'Count',
										),
										array(
											'field_key' => 'category_image',
											'field_display' => 1,
											'field_title' =>'Category Image',
											'field_value' =>'Category Image',
										),
										array(
											'field_key' => 'woocommerce_term_meta',
											'field_display' => 1,
											'field_title' =>'Woocommerce Term Meta',
											'field_value' =>'Woocommerce Term Meta',
										),
									),
								);
								
		return $get_product_cat_fields;
	}
	function get_product_cat_export_data($wooexim_data = array())
 	{
		global $wooexim_product, $wooexim_product_cat;
	
		$wooexim_product->wooexim_set_time_limit(0);
		
		$csv_data = "";
		
		$product_cat_field_list = $wooexim_product_cat -> get_updated_product_cat_fields();
				
		$product_cat_list_data = $wooexim_product_cat -> get_filter_product_cat($wooexim_data);
		
		$count = 0;
		
		foreach($product_cat_field_list['product_cat_fields'] as $field_data)
		{
			if($field_data['field_display']==1){
				
					$csv_data[$count][] = $field_data['field_value'];
			}
				
		}
		
		foreach($product_cat_list_data as $product_cat_info)
		{
			$count++;
			
			$data_result = array();
			
			foreach($product_cat_field_list['product_cat_fields'] as $field_data){
			
				
				if($field_data['field_display']==1){
			
					$data_result[] = isset($product_cat_info->$field_data['field_key'])?$product_cat_info->$field_data['field_key']:"";
					
				}
				
			}
			
			$csv_data[$count] = $data_result;
			
		}
			
		return $csv_data;
	}
	function save_product_cat_scheduled()
 	{
		global $wooexim_scheduled;
		
		$general_options_data = $_POST;
		
		$wooexim_export_interval = isset($_POST['wooexim_export_interval'])?$_POST['wooexim_export_interval']:"";
		
		$return_value = array();
		
		if($wooexim_export_interval!="")
		{
		
			$scheduled_id = uniqid();
			
			$scheduled_data = $wooexim_scheduled -> get_product_cat_scheduled_data();
			
			$scheduled_data[$scheduled_id] = $general_options_data;
			
			$scheduled_new_data = @maybe_serialize($scheduled_data);
			
			update_option('wooexim_product_cat_scheduled_data',$scheduled_new_data);
						
			wp_schedule_event( time(), $wooexim_export_interval, 'wooexim_cron_scheduled_product_cat_export',array($scheduled_id) );
			
			$return_value['message']		= 'success';
			
		}		
		else
		{
			$return_value['message']		= 'error';
		}
		
		echo json_encode($return_value );
		
		die();
	}	
	function wooexim_save_product_cat_fields()
	{
		global $wooexim_product_cat;
		
		$old_product_cat_fields = $wooexim_product_cat -> get_updated_product_cat_fields();
		
		$new_fields = array();
		
		foreach($old_product_cat_fields as $product_cat_fields_key=>$product_cat_fields_data)
		{
 			foreach($product_cat_fields_data as $key=>$value)
			{
				$new_fields[$product_cat_fields_key][$key]['field_key'] = $value['field_key'];
				
				$new_fields[$product_cat_fields_key][$key]['field_display'] = $value['field_display'];
				
				$new_fields[$product_cat_fields_key][$key]['field_title'] = $value['field_title'];
				
				$new_fields[$product_cat_fields_key][$key]['field_value'] = isset($_POST['wooexim_'.$value['field_key'].'_field'])?$_POST['wooexim_'.$value['field_key'].'_field']:"";
				
  			}
		}
		
		$new_fields_data = maybe_serialize($new_fields);
		
		update_option('wooexim_product_cat_fields', $new_fields_data);
		
		$return_value = array();
		
		$return_value['message'] = 'success';
		
		$return_value['message_content'] = __('Changes Saved Successfully.',WOOEXIM_TEXTDOMAIN);
			
		echo json_encode($return_value );
		
		die();
	}
	function wooexim_import_products_cat()
	{
		global $wooexim_product, $wooexim_product_cat, $wooexim_import_export;
		
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
		
		$product_cat_field_list = $wooexim_product_cat -> get_updated_product_cat_fields();
		 
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
						$import_data[$count][$product_cat_field_list['product_cat_fields'][$key]['field_key']] = $value;
						
 					}
					$count++;
				}
  				
				$return_value['message'] = 'success' ;
				
				$return_value['success_log'] = '<div class="wooexim_success_msg wooexim_product_cat_import_success_msg">'.__('Product Categories Imported successfully.',WOOEXIM_TEXTDOMAIN).'</div>';
				
			}else{
			
 				$return_value['message_text'] = __( 'Could not open file.', WOOEXIM_TEXTDOMAIN );
			}
			if(!empty($import_data))
			{
				$product_cat_updated_data = $wooexim_product_cat -> wooexim_create_new_product_cat($import_data);
				
				$return_value['data'] = @$product_cat_updated_data['data'];
				
				$return_value['product_cat_log'] = $wooexim_product_cat -> set_product_cat_import_errors($product_cat_updated_data['product_cat_log']);
 			}
		}
		
		echo json_encode($return_value );
		
		die();
		
	}
	function wooexim_create_new_product_cat($product_cat_data = array())
	{
		global $wooexim_product, $wooexim_product_cat;
		
		$wooexim_product -> wooexim_set_time_limit(0);
		
		$imported_ids = array();
		
		$wooexim_product_cat_log = array();
  		
		$product_cat_create_method = isset($_POST['wooexim_product_cat_create_method'])?$_POST['wooexim_product_cat_create_method']:"";
		
		$cat_new_old_id_list = array(); 
		
		$parent_id_list = array();
				
		foreach($product_cat_data as $product_cat_info)
		{
 			
			$current_cat_id = 0;
			
			$parent_cat_id = 0;
			
			if(isset($product_cat_info['parent_slug']) && $product_cat_info['parent_slug']!="")
			{
				$parent_cat = term_exists($product_cat_info['parent_slug'],'product_cat');
				
				if($parent_cat!==0 && $parent_cat!==null)
				{
					$parent_cat_id = $parent_cat_id['term_id'];
				}
			}
				
			if(isset($product_cat_info['slug']) && $product_cat_info['slug']!="" && ($product_cat_create_method == 'update_product_cat' || $product_cat_create_method == 'skip_product_cat')) 
			{
			
				if($parent_cat_id > 0)
				{
					$current_category = term_exists($product_cat_info['slug'],'product_cat',$parent_cat_id);
				}
				else
				{
					$current_category = term_exists($product_cat_info['slug'],'product_cat');
				}
				
				
				if($current_category!==0 && $current_category!==null)
				{
					$current_cat_id = $current_category['term_id'];
				}
				
				if( $product_cat_create_method == 'skip_product_cat' && $current_cat_id!="" && $current_cat_id>0)
				{
					$imported_ids['wooexim_product_category'][] = $current_cat_id;
					
					$wooexim_product_cat_log[] = sprintf( __( 'Product Category #%d %s already Exist.', WOOEXIM_TEXTDOMAIN ), $current_cat_id, $product_cat_info['name']);
					
					$cat_new_old_id_list[$product_cat_info['term_id']] = $current_cat_id;
					
					continue;
				}
				
				if($current_cat_id>0 && $product_cat_create_method == 'update_product_cat')
				{
					$product_cat_list = array(
						
											'description' => $product_cat_info['description'],
							
											'slug' => $product_cat_info['slug'],
											
											'parent' => $parent_cat_id,
											
											'name' => $product_cat_info['name'],
											
											'term_group' => $product_cat_info['term_group'],
											
											'term_texonomy_id' => $product_cat_info['term_taxonomy_id'],
											
											'count' => $product_cat_info['count'],
										
									);
					
					$product_cat_data = wp_update_term($current_cat_id, 'product_cat', $product_cat_list);
				}
				
				 
			}
			
			if($current_cat_id==0 )
			{
				
				$product_cat_list = array(
						
										'description' => $product_cat_info['description'],
										
										'slug' => $product_cat_info['slug'],
										
										'parent' => $parent_cat_id,
										
										'name' => $product_cat_info['name'],
										
										'term_group' => $product_cat_info['term_group'],
										
										'term_texonomy_id' => $product_cat_info['term_taxonomy_id'],
										
										'count' => $product_cat_info['count'],
							
								);
					
				$product_cat_data = wp_insert_term(	$product_cat_info['name'] , 'product_cat', $product_cat_list);
					
			}
			if(isset($product_cat_data['term_id']) && $product_cat_data['term_id']>0)
			{
				$current_cat_id = $product_cat_data['term_id'];
			}
			if(isset($product_cat_info['category_image']) && $product_cat_info['category_image']!="")
			{
				$wooexim_product_cat_log[] =  $wooexim_product_cat -> wooexim_get_product_cat_image($product_cat_info['category_image'], $product_cat_data['term_id']);
			}
			
			$imported_ids['wooexim_product_category'][] = $product_cat_data['term_id'];
			
			$cat_new_old_id_list[$product_cat_info['term_id']] = $current_cat_id;
			
			if($product_cat_info['parent']>0)
			{
				$parent_id_list[$current_cat_id] = $product_cat_info['parent'];
			}
			
			if($product_cat_info['woocommerce_term_meta']!="")
			{
				$woocommerce_term_meta = maybe_unserialize($product_cat_info['woocommerce_term_meta']);
				
				foreach($woocommerce_term_meta as $meta_key => $meta_value)
				{
					if( $meta_key != 'thumbnail_id')
					{
						@update_metadata( 'woocommerce_term', $current_cat_id, $meta_key, $meta_value);
					}
				}
			}
			
		}
		
		if(!empty($cat_new_old_id_list) && !empty($parent_id_list) )
		{
			foreach($cat_new_old_id_list as $key => $value)
			{
				foreach($parent_id_list as  $cat_new_id => $cat_parent_id)
				{
					if($key == $cat_parent_id)
					{
					
						$new_product_cat_list = array(
											
											'parent' => $value
											
											);
					
						$product_cat_data = wp_update_term($cat_new_id, 'product_cat', $new_product_cat_list);
						
					}
				}
			}
		}
		
		$product_all_data  = array();
		
		$product_all_data['data'] = $wooexim_product_cat -> get_imported_product_cat($imported_ids);
		
		$product_all_data['product_cat_log'] = $wooexim_product_cat_log;
		
		return $product_all_data;
		
	}
	function get_imported_product_cat($product_cat_ids = array())
	{
		global $wooexim_product, $wooexim_product_cat;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$product_cat_list = $wooexim_product_cat -> get_filter_product_cat($product_cat_ids);
		
		$product_cat_fields = $wooexim_product_cat -> get_updated_product_cat_fields();
		
		ob_start();
		
		?>
		
		<div class="wooexim_filter_data_container">
		
			<table class="wooexim_product_cat_filter_data">
				<thead>
					<tr>
						<th></th>
						<?php foreach($product_cat_fields['product_cat_fields'] as $product_cat_info){?>
							<th><?php echo $product_cat_info['field_value'];?></th>
						<?php }?>
					</tr>
				</thead>
				<tbody>
					<?php 
					if(!empty($product_cat_list)){
						$count = 0;
						
						foreach($product_cat_list as $product_cat_data){
							$count++;
							?>
							<tr>
								<td><?php echo $count;?></td>
								<?php 
									foreach($product_cat_fields['product_cat_fields'] as $product_cat_info){
										?>
										<td><?php echo isset($product_cat_data->$product_cat_info['field_key'])?$product_cat_data->$product_cat_info['field_key']:"";?></td>
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
	
	function set_product_cat_import_errors($product_cat_errors = array())
	{
		
		$product_all_errors =array();
		
		if(!empty($product_cat_errors))
		{
			foreach($product_cat_errors as $product_cat_error)
			{
				if(is_array($product_cat_error))
				{
					foreach($product_cat_error as $product_cat_sub_error)
					{
						if(is_array($product_cat_sub_error))
						{
							foreach($product_cat_sub_error as $product_cat_sub1_error)
							{
								if($product_cat_sub1_error!="" && !is_array($product_cat_sub1_error))
								{
									$product_all_errors[] = $product_cat_sub1_error;
								}
							}
						}
						else if($product_cat_sub_error!="")
						{
							$product_all_errors[] = $product_cat_sub_error;
						}
					}
				}
				else if($product_cat_error!="")
				{
					$product_all_errors[] = $product_cat_error;
				}
			 
			}
		}
		ob_start();
		
		
		if(!empty($product_all_errors))
		{
			foreach($product_all_errors as $wooexim_error)
			{
				?>
				<div class="wooexim_error_msg wooexim_import_product_cat_error_log"><?php echo $wooexim_error;?></div>
				<?php
			}
		}
		
		$buffer_output = ob_get_contents();
		
		ob_end_clean();
		
		return $buffer_output ;
	}
	function wooexim_get_product_cat_image($images = "", $cat_id = "")
	{
		global $wooexim_product, $wooexim_product_cat;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$image_list = @explode(',',$images);
		
		$new_product_cat_errors = array();
		
		if(!empty($image_list) )
		{
			$wp_upload_dir = wp_upload_dir();

			foreach($image_list as $image_index => $image_url) {

				if($image_url!="")
				{
					$image_url = str_replace(' ', '%20', trim($image_url));
	
					$parsed_url = parse_url($image_url);
					
					$pathinfo = pathinfo($parsed_url['path']);
	
					$allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
					
					$url_ext = @explode('.',$image_url);
					
					if(!empty($url_ext))
					{
						$image_ext = @strtolower(end($url_ext));
					}
					else
					{
						$image_ext = "";
					}
					
					if(!in_array($image_ext, $allowed_extensions)) {
					
						$new_product_cat_errors[] = sprintf( __( 'A valid file extension wasn\'t found in %s. Extension found was %s. Allowed extensions are: %s.', WOOEXIM_TEXTDOMAIN ), $image_url, $image_ext, implode( ', ', $allowed_extensions ) );
						
						continue;
					}
	
					$dest_filename = wp_unique_filename( $wp_upload_dir['path'], $pathinfo['basename'] );
					
					$dest_path = $wp_upload_dir['path'] . '/' . $dest_filename;
					
					$dest_url = $wp_upload_dir['url'] . '/' . $dest_filename;
	
					if(ini_get('allow_url_fopen')) {
	
						if( ! @copy($image_url, $dest_path)) {
						
							$http_status = $http_response_header[0];
							
							$new_product_cat_errors[] = sprintf( __( '%s encountered while attempting to download %s', WOOEXIM_TEXTDOMAIN ), $http_status, $image_url );
						}
	
					} elseif(function_exists('curl_init')) {
					
						$ch = curl_init($image_url);
						
						$fp = fopen($dest_path, "wb");
	
						$options = array(
						
							CURLOPT_FILE => $fp,
							
							CURLOPT_HEADER => 0,
							
							CURLOPT_FOLLOWLOCATION => 1,
							
							CURLOPT_TIMEOUT => 60);
	
						curl_setopt_array($ch, $options);
						
						curl_exec($ch);
						
						$http_status = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
						
						curl_close($ch);
						
						fclose($fp);
	
						if($http_status != 200) {
						
							unlink($dest_path);
							
							$new_product_cat_errors[] = sprintf( __( 'HTTP status %s encountered while attempting to download %s', WOOEXIM_TEXTDOMAIN ), $http_status, $image_url );
						}
					} else {
					
						$new_product_cat_errors[] = sprintf( __( 'Looks like %s is off and %s is not enabled. No images were imported.', WOOEXIM_TEXTDOMAIN), '<code>allow_url_fopen</code>', '<code>cURL</code>'  );
						
						break;
					}
	
					if(!file_exists($dest_path)) {
					
						$new_product_cat_errors[] = sprintf( __( 'Couldn\'t download file %s.', WOOEXIM_TEXTDOMAIN ), $image_url );
						
						continue;
					}
	
					$new_post_image_paths[] = array(
					
						'path' => $dest_path,
						
						'source' => $image_url
					);
					}
			}

			if(!empty($new_post_image_paths))
			{
				foreach($new_post_image_paths as $image_index => $dest_path_info) {

					if(!file_exists($dest_path_info['path'])) {
					
						$new_product_cat_errors[] = sprintf( __( 'Couldn\'t find local file %s.', WOOEXIM_TEXTDOMAIN ), $dest_path_info['path'] );
						
						continue;
					}
	
					$dest_url = str_ireplace(ABSPATH, home_url('/'), $dest_path_info['path']);
					
					$path_parts = pathinfo($dest_path_info['path']);
	
					$wp_filetype = wp_check_filetype($dest_path_info['path']);
					
					$attachment = array(
					
						'guid' => $dest_url,
						
						'post_mime_type' => $wp_filetype['type'],
						
						'post_title' => preg_replace('/\.[^.]+$/', '', $path_parts['filename']),
						
						'post_content' => '',
						
						'post_status' => 'inherit'
					);
					
					
					$attachment_id = wp_insert_attachment( $attachment, $dest_path_info['path'] );
					
					if($attachment_id && $attachment_id >0 )
					{
						update_metadata( 'woocommerce_term',  $cat_id, 'thumbnail_id', $attachment_id);
						
						require_once(ABSPATH . 'wp-admin/includes/image.php');
						
						$attach_data = wp_generate_attachment_metadata( $attachment_id, $dest_path_info['path'] );
						
						wp_update_attachment_metadata( $attachment_id, $attach_data );
						
					}
					
				}
			}
			
		}
		return $new_product_cat_errors;
	}
}
?>