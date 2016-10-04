<?php

if (!defined('ABSPATH'))
    die("Can't load this file directly");
	
class wooexim_order{

	function wooexim_order()
	{
		add_action( 'wp_ajax_wooexim_get_order_details', array( &$this, 'wooexim_get_order_details' ));
		
		add_action( 'wp_ajax_wooexim_import_order', array( &$this, 'wooexim_import_order' ));
		
		add_action( 'wp_ajax_wooexim_save_order_fields', array( &$this, 'wooexim_save_order_fields' ));
		
 		add_action( 'wp_ajax_save_order_scheduled', array( &$this, 'save_order_scheduled' ));
		
 	}
	
	function get_woo_order_status()
	{
		
		$shop_order_status = array();

		if(function_exists('wc_get_order_statuses'))
		{
			$shop_order_status = wc_get_order_statuses();
								
		}	
		else
		{
			$shop_order_status = get_terms( 'shop_order_status', 'orderby=id&hide_empty=1' );
		}	
		
		return $shop_order_status;				
	}
	
	function get_order_list()
	{
		global $wooexim_order;
		
		$query_args = array(
		
						'posts_per_page' 	=> 2000,
						
						'post_type'   		=> 'shop_order',
						
						'post_status' 		=> 'publish',
						
						'orderby' 			=> 'ID',
						
						'order' 			=> 'ASC',
						
						'fields' 			=> 'ids',
						
						);
		if(function_exists('wc_get_order_statuses'))
        {
			
			$query_args['post_status']  =  array_keys($wooexim_order -> get_woo_order_status());
			
		}
								
		$orders_list = get_posts($query_args);
		
		return $orders_list;
	}
	function order_field_list()
	{		
			$field_list = array(
					
					'order_field' => array(
					
										array(
											'field_key' => 'id',
											'field_display' => 1,
											'field_title' => 'Id',
											'field_value' => 'Id',
										),
										array(
											'field_key' => 'order_final_status',
											'field_display' => 1,
											'field_title' => 'Status',
											'field_value' => 'Status',
										),
										array(
											'field_key' => 'order_date',
											'field_display' => 1,
											'field_title' => 'Order Date',
											'field_value' => 'Order Date',
										),
										array(
											'field_key' => '_billing_first_name',
											'field_display' => 1,
											'field_title' => 'First Name (Billing)',
											'field_value' => 'First Name (Billing)',
										),
										array(
											'field_key' => '_billing_last_name',
											'field_display' => 1,
											'field_title' => 'Last Name (Billing)',
											'field_value' => 'Last Name (Billing)',
										),
										array(
											'field_key' => '_billing_company',
											'field_display' => 1,
											'field_title' => 'Company (Billing)',
											'field_value' => 'Company (Billing)',
										),
										array(
											'field_key' => '_billing_address_1',
											'field_display' => 1,
											'field_title' => 'Address 1 (Billing)',
											'field_value' => 'Address 1 (Billing)',
										),
										array(
											'field_key' => '_billing_address_2',
											'field_display' => 1,
											'field_title' => 'Address 2 (Billing)',
											'field_value' => 'Address 2 (Billing)',
										),
										array(
											'field_key' => '_billing_city',
											'field_display' => 1,
											'field_title' => 'City (Billing)',
											'field_value' => 'City (Billing)',
										),
										array(
											'field_key' => '_billing_postcode',
											'field_display' => 1,
											'field_title' => 'Postcode (Billing)',
											'field_value' => 'Postcode (Billing)',
										),
										array(
											'field_key' => '_billing_country',
											'field_display' => 1,
											'field_title' => 'Country (Billing)',
											'field_value' => 'Country (Billing)',
										),
										array(
											'field_key' => '_billing_state',
											'field_display' => 1,
											'field_title' => 'State (Billing)',
											'field_value' => 'State (Billing)',
										),
										array(
											'field_key' => '_billing_email',
											'field_display' => 1,
											'field_title' => 'Email (Billing)',
											'field_value' => 'Email (Billing)',
										),
										array(
											'field_key' => '_billing_phone',
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
											'field_key' => '_shipping_last_name',
											'field_display' => 1,
											'field_title' => 'Last Name (Shipping)',
											'field_value' => 'Last Name (Shipping)',
										),
										array(
											'field_key' => '_shipping_company',
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
											'field_key' => '_shipping_address_2',
											'field_display' => 1,
											'field_title' => 'Address 2 (Shipping)',
											'field_value' => 'Address 2 (Shipping)',
										),
										array(
											'field_key' => '_shipping_city',
											'field_display' => 1,
											'field_title' => 'City (Shipping)',
											'field_value' => 'City (Shipping)',
										),
										array(
											'field_key' => '_shipping_postcode',
											'field_display' => 1,
											'field_title' => 'Postcode (Shipping)',
											'field_value' => 'Postcode (Shipping)',
										),
										array(
											'field_key' => '_shipping_state',
											'field_display' => 1,
											'field_title' => 'State (Shipping)',
											'field_value' => 'State (Shipping)',
										),
										array(
											'field_key' => '_shipping_country',
											'field_display' => 1,
											'field_title' => 'Country (Shipping)',
											'field_value' => 'Country (Shipping)',
										),
										array(
											'field_key' => 'customer_note',
											'field_display' => 1,
											'field_title' => 'Customer Note',
											'field_value' => 'Customer Note',
										),
										array(
											'field_key' => '_shipping_method_title',
											'field_display' => 1,
											'field_title' => 'Method Title (Shipping)',
											'field_value' => 'Method Title (Shipping)',
										),
										array(
											'field_key' => '_payment_method_title',
											'field_display' => 1,
											'field_title' => 'Payment Method Title',
											'field_value' => 'Payment Method Title',
										),
										array(
											'field_key' => '_cart_discount',
											'field_display' => 1,
											'field_title' => 'Cart Discount',
											'field_value' => 'Cart Discount',
										),
										array(
											'field_key' => '_order_tax',
											'field_display' => 1,
											'field_title' => 'Order Tax',
											'field_value' => 'Order Tax',
										),
										array(
											'field_key' => '_order_shipping_tax',
											'field_display' => 1,
											'field_title' => 'Order Tax (Shipping)',
											'field_value' => 'Order Tax (Shipping)',
										),
										array(
											'field_key' => '_order_total',
											'field_display' => 1,
											'field_title' => 'Order Total',
											'field_value' => 'Order Total',
										),
										array(
											'field_key' => '_completed_date',
											'field_display' => 1,
											'field_title' => 'Completed Date',
											'field_value' => 'Completed Date',
										),
										array(
											'field_key' => 'total_diff_no_product',
											'field_display' => 1,
											'field_title' => 'Number of different items',
											'field_value' => 'Number of different items',
										),
										array(
											'field_key' => 'totle_no_of_product',
											'field_display' => 1,
											'field_title' => 'Total number of items',
											'field_value' => 'Total number of items',
										),
										array(
											'field_key' => 'order_data_status',
											'field_display' => 1,
											'field_title' => 'Status Key',
											'field_value' => 'Status Key',
										),
										array(
											'field_key' => '_payment_method',
											'field_display' => 1,
											'field_title' => 'Payment Method',
											'field_value' => 'Payment Method',
										),
										array(
											'field_key' => '_order_discount',
											'field_display' => 1,
											'field_title' => 'Order Discount',
											'field_value' => 'Order Discount',
										),
										array(
											'field_key' => '_order_key',
											'field_display' => 1,
											'field_title' => 'Order Key',
											'field_value' => 'Order Key',
										),
										array(
											'field_key' => '_order_currency',
											'field_display' => 1,
											'field_title' => 'Order Currency',
											'field_value' => 'Order Currency',
										),
										array(
											'field_key' => 'order_other_data',
											'field_display' => 1,
											'field_title' => 'Order Other Data',
											'field_value' => 'Order Other Data',
										)
									),
						'product_field' => array(
										array(
											'field_key' => 'product_id',
											'field_display' => 1,
											'field_title' => 'Product Id',
											'field_value' => 'Product Id',
										),
										array(
											'field_key' => 'name',
											'field_display' => 1,
											'field_title' => 'Product Name',
											'field_value' => 'Product Name',
										),
										array(
											'field_key' => 'qty',
											'field_display' => 1,
											'field_title' => 'Product Quantity',
											'field_value' => 'Product Quantity',
										),
										array(
											'field_key' => 'line_subtotal',
											'field_display' => 1,
											'field_title' => 'Line price (without taxes)',
											'field_value' => 'Line price (without taxes)',
										),
										array(
											'field_key' => 'line_total',
											'field_display' => 1,
											'field_title' => 'Line price (including taxes)',
											'field_value' => 'Line price (including taxes)',
										),
										array(
											'field_key' => '_sku',
											'field_display' => 1,
											'field_title' => 'Product SKU',
											'field_value' => 'Product SKU',
										),
								),
						'order_coupon' => array(
										array(
											'field_key' => 'coupon_id',
											'field_display' => 1,
											'field_title' => 'Coupon Id',
											'field_value' => 'Coupon Id',
										),
										array(
											'field_key' => 'coupon_amount',
											'field_display' => 1,
											'field_title' => 'Coupon Discount',
											'field_value' => 'Coupon Discount',
										),
								),
						);
						
		return $field_list;
	}
	function wooexim_get_order_details()
 	{
	
		global $wooexim_order;
	
		$order_field_list = $wooexim_order -> get_updated_order_fields();
		
		$order_list_data = $wooexim_order -> wooexim_get_order_data($_POST);
		
		$total_diff_no_product = 0;
		
		$total_coupons = 0;
		
		foreach($order_list_data as $order_data)
		{
			if($order_data->total_diff_no_product > $total_diff_no_product)
			{
				$total_diff_no_product = $order_data->total_diff_no_product;
			}
			
			if(count($order_data->order_coupons_list) > $total_coupons)
			{
				$total_coupons = count($order_data->order_coupons_list);
			}
		}
		
		ob_start();
		
		?>
		<div class="wooexim_filter_data_container">
			<table class="wooexim_order_filter_data">
				<thead>
					<tr class="wooexim_order_filter_data_row">
						<th class="wooexim_order_filter_data_header"></th>
					<?php   foreach($order_field_list['order_field'] as $order_field){
							
								if($order_field['field_display']==1){?>
								
									<th class="wooexim_order_filter_data_header"><?php echo $order_field['field_value'];?></th>
									
								<?php 
								}
							}
							
							for($i=0;$i<$total_diff_no_product;$i++)
							{
								foreach($order_field_list['product_field'] as $order_field){
								
									if($order_field['field_display']==1){?>
									
										<th class="wooexim_order_filter_data_header"><?php echo $order_field['field_value'].' #'.($i+1);?></th>
										
									<?php }
	
								}
							}
							for($i=0;$i<$total_coupons;$i++)
							{
								foreach($order_field_list['order_coupon'] as $order_field){
								
									if($order_field['field_display']==1){?>
									
										<th class="wooexim_order_filter_data_header"><?php echo $order_field['field_value'].' #'.($i+1);?></th>
										
									<?php }
	
								}
							}
					?>
					</tr>
				</thead>
				<tbody>
					<?php 
					$temp_count_data = 0;
					foreach($order_list_data as $order_info){
					$temp_count_data++;
					?>
					<tr class="wooexim_order_filter_data_row">
						<td class="wooexim_order_filter_data_column"><?php echo $temp_count_data;?></td>
						<?php    foreach($order_field_list['order_field'] as $order_field){
								
									if($order_field['field_display']==1){?>
										
											<td class="wooexim_order_filter_data_column"><?php echo $order_info->$order_field['field_key'];?></td>
											
									<?php }
								
								}
								$temp_count = 0;
								foreach($order_info->product_list as $product_data)
								{
									foreach($order_field_list['product_field'] as $order_field){
									
										if($order_field['field_display']==1){?>
										
											<td class="wooexim_order_filter_data_column"><?php  echo $product_data[$order_field['field_key']];?></td>
											
										<?php }
		
									}
									$temp_count++;
								}
								
								for($i=$temp_count;$i<$total_diff_no_product;$i++)
								{
									foreach($order_field_list['product_field'] as $order_field){
									
										if($order_field['field_display']==1){?>
										
											<td class="wooexim_order_filter_data_column"></td>
											
										<?php }
		
									}
								}
								
								$temp_count = 0;
								
								foreach($order_info->order_coupons_list as $coupons_data)
								{
									foreach($order_field_list['order_coupon'] as $order_field){
									
										if($order_field['field_display']==1){?>
										
											<td class="wooexim_order_filter_data_column"><?php  echo $coupons_data[$order_field['field_key']];?></td>
											
										<?php }
		
									}
									$temp_count++;
								}
								
								for($i=$temp_count;$i<$total_coupons;$i++)
								{
									foreach($order_field_list['order_coupon'] as $order_field){
									
										if($order_field['field_display']==1){?>
										
											<td class="wooexim_order_filter_data_column"></td>
											
										<?php }
		
									}
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
	
	function wooexim_get_order_data($wooexim_data = array())
	{
		global $wooexim_order,$wooexim_product;
		
		$order_status 				= isset($wooexim_data['wooexim_order_status'])?$wooexim_data['wooexim_order_status']:array();
		
		$order_product_category 	= isset($wooexim_data['wooexim_product_category'])?$wooexim_data['wooexim_product_category']:array();
		
		$order_product 				= isset($wooexim_data['wooexim_product_ids'])?$wooexim_data['wooexim_product_ids']:array();




   		
		$order_ids 					= isset($wooexim_data['wooexim_order_ids'])?$wooexim_data['wooexim_order_ids']:array();
   		
		$temp_start_date 			= isset($wooexim_data['wooexim_start_date'])?$wooexim_data['wooexim_start_date']:"";
		
		$temp_end_date 				= isset($wooexim_data['wooexim_end_date'])?$wooexim_data['wooexim_end_date']:"";
		
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
						
						'post_type'   		=> 'shop_order',
						
						'post_status' 		=> 'publish',
						
						'orderby' 			=> 'ID',
						
						'order' 			=> 'ASC',
											
						);
						
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
			
			$query_args['date_query'] = array($date_data);
			
		}			
		if(!empty($order_ids))
		{
			$query_args['post__in'] = $order_ids;
		}
		
		if(function_exists('wc_get_order_statuses'))
        {
			if(!empty($order_status))
			{
				$query_args['post_status']  = $order_status;
			}else{
				$query_args['post_status']  =  array_keys($wooexim_order -> get_woo_order_status());
			}
			
			
		}
		else
		{
			if(!empty($order_status))
			{
				$query_args['tax_query']		= array(
													array(
								
														 'taxonomy' =>'shop_order_status',
														 'field' => 'id',
														 'terms' => $order_status
													)
											);
			}
		}
		$export_orders = new WP_Query( $query_args );
		
		$order_results = $export_orders->get_posts();
		
		$order_data = array();
		
		foreach($order_results as $order_result)
		{
			$wooexim_product->wooexim_set_time_limit(0);
 
 			if(function_exists('wc_get_order_statuses'))
			{
				$order = new WC_Order($order_result);
				$items = $order->get_items();
			}
			else
			{
				$order = new WC_Order();
				$order->populate( $order_result );
				$items = $order->get_items();
			}
			
			$order -> product_list = $items;
			
			if(!isset($order->order_custom_fields))
			{
        		$order->order_custom_fields = get_post_meta( $order->id );
        	}
			
			foreach ($order->order_custom_fields as $key => $value) {
				$order->$key = $value[0];
			}
			$order->_shipping_method_title = $order->get_shipping_method();
			
			$shop_order_status = $wooexim_order -> get_woo_order_status();
			
			if(function_exists('wc_get_order_statuses'))
			{
				$order -> order_final_status = $shop_order_status[$order->post_status];
				
				$order -> order_data_status = $order->post_status;
			
			}
			else
			{
				$order->order_final_status = $order->status;
				
				$order -> order_data_status = $order->status;
			}
			
			//unset( $order->order_custom_fields );
			
			// search product filter
			
			$filter_flag = 1;
			
			if(!empty($order_product))
			{
				$filter_flag = 0;
				foreach($order -> product_list as $new_product)
				{
					if(in_array($new_product['product_id'],$order_product))
					{
						$filter_flag = 1;
						break;
					}
				}
			}
			
			// se3arch product category filter
			
			if(!empty($order_product_category) && $filter_flag==1)
			{
				$filter_flag = 0;
				foreach($order -> product_list as $new_product)
				{
					$wooexim_product->wooexim_set_time_limit(0);
					
					$cat_list = wp_get_post_terms($new_product['product_id'], 'product_cat',array('fields'=>'ids'));
					if(!empty($cat_list))
					{
						foreach($cat_list as $product_cat)
						{
							if(in_array($product_cat,$order_product_category))
							{
								$filter_flag = 1;
								break;
							}
						}
					}
				}
			}
			
 			if($filter_flag==1)
			{
				$total_diff_no_product = 0;
			
				$totle_no_of_product = 0;
				
				$final_product_list = array();
				
				foreach($order -> product_list as $product_data)
				{
					$wooexim_product -> wooexim_set_time_limit(0);
					$totle_no_of_product += $product_data['qty'];
					$total_diff_no_product++;
					$product_data['_sku'] = get_post_meta($product_data['product_id'], '_sku',true); 
					$final_product_list[] = $product_data;
				}
				
				$order -> product_list = $final_product_list;
				
				$order -> total_diff_no_product = $total_diff_no_product;
				
				$order -> totle_no_of_product = $totle_no_of_product;
				
				$order_coupons_list = $order -> get_used_coupons();
				
				$coupon_detail = array();
				
				$temp_count = 0;
				
				if(!empty($order_coupons_list))
				{
					foreach($order_coupons_list as $key => $value)
					{
						$coupon_detail[$temp_count]['coupon_id'] = $value;
						 
						$coupon_detail[$temp_count]['coupon_amount'] = $wooexim_order -> get_coupon_amount($order->id, $value);
						
						$temp_count++;
					}
					$order -> coupon_used = $temp_count;
				}
				else
				{
					$order -> coupon_used = 0;
				}
				
				$order -> order_coupons_list = $coupon_detail;
				
				$child_post = get_posts(array('post_type'=>'shop_order_refund','post_status'=>'any','post_parent'=>$order->id));
				
 				$order_other_data = array();
				
				$order_other_data['order_custom_fields'] = maybe_serialize($order->order_custom_fields);
				
				$order_other_data['order_items'] = $order ->  get_items(array('line_item','coupon','shipping','tax'));
				
				if($child_post)
				{
					$order_other_data['shop_order_refund'] = $child_post;
					
					$order_other_data['shop_order_refund_attr'] = get_post_meta($child_post[0]->ID);
				}
				if(!empty($order_other_data))
				{
					$order_other_data = maybe_serialize($order_other_data);
				}
				else
				{
					$order_other_data = "";
				}
				
				$order -> order_other_data = $order_other_data;
				
				$order_data[] = $order;
			}
			unset( $order->order_custom_fields );
		}		
		
		return $order_data;
	}
	function get_coupon_amount($order_id, $coupon)
	{
		global $wpdb;
		
		$coupon_query = '
			SELECT meta_value
				FROM '.$wpdb->prefix.'woocommerce_order_items woi
				LEFT JOIN '.$wpdb->prefix.'woocommerce_order_itemmeta woim
					ON woi.order_item_id = woim.order_item_id
			WHERE 
				order_item_type = "coupon"
				AND order_id ='.$order_id.'
				AND order_item_name="%s"
				AND meta_key="discount_amount"
		';
		
		$coupon_results = $wpdb->get_results($wpdb->prepare($coupon_query, $coupon));

		if(isset($coupon_results[0]))
			return round($coupon_results[0]->meta_value, 2);
		else
			return 0;
	}
	function get_order_csv_data($wooexim_data = array())
	{
	
		global $wooexim_order;
	
		$csv_data = "";
		
		$count = 0;
		
		$order_field_list = $wooexim_order -> get_updated_order_fields();
				
		$order_list_data = $wooexim_order -> wooexim_get_order_data($wooexim_data);
		
		
		
		foreach($order_field_list['order_field'] as $field_data)
		{
			if($field_data['field_display']==1){
				
					$csv_data[$count][] = $field_data['field_value'];
			}
		}
		
		$total_diff_no_product = 0;
		
		$total_coupons = 0;
		
		foreach($order_list_data as $order_data)
		{
			if($order_data->total_diff_no_product > $total_diff_no_product)
			{
				$total_diff_no_product = $order_data->total_diff_no_product;
			}
			if(count($order_data->order_coupons_list) > $total_coupons)
			{
				$total_coupons = count($order_data->order_coupons_list);
			}
		}
		
		for($i=0;$i<$total_diff_no_product;$i++)
		{
			foreach($order_field_list['product_field'] as $field_data)
			{
				if($field_data['field_display']==1){
					
						$csv_data[$count][] = $field_data['field_value'].' #'.($i+1);
					
				}
				
			}
		}	
		for($i=0;$i<$total_coupons;$i++)
		{
			foreach($order_field_list['order_coupon'] as $field_data){
			
				if($field_data['field_display']==1){
					
						$csv_data[$count][] = $field_data['field_value'].' #'.($i+1);
					
				}

			}
		}
		
		
		foreach($order_list_data as $order_info)
		{

			$count++;
			
			$data_result = array();
			
			foreach($order_field_list['order_field'] as $field_data){
			
				if($field_data['field_display']==1){
			
					$data_result[] = $order_info->$field_data['field_key'];
					
				}
				
			}
			$temp_count = 0;
			foreach($order_info->product_list as $product_data)
			{
				foreach($order_field_list['product_field'] as $field_data){
				
					if($field_data['field_display']==1){
				
						$data_result[] = $product_data[$field_data['field_key']];
						
					}
					
				}
				$temp_count++;
			}	
			for($i=$temp_count;$i<$total_diff_no_product;$i++)
			{
				foreach($order_field_list['product_field'] as $order_field){
				
					if($field_data['field_display']==1){
				
						$data_result[] = "";
						
					}
				}
			}
			
			$temp_count = 0;
								
			foreach($order_info->order_coupons_list as $coupons_data)
			{
				foreach($order_field_list['order_coupon'] as $field_data){
				
					if($field_data['field_display']==1){
					
						$data_result[] = $coupons_data[$field_data['field_key']];
					}

				}
				$temp_count++;
			}
			
			for($i=$temp_count;$i<$total_coupons;$i++)
			{
				foreach($order_field_list['order_coupon'] as $field_data){
				
					if($field_data['field_display']==1){
					
						$data_result[] = "";
					}
				}
			}
			
			$csv_data[$count] = $data_result;
			
		}
			
		return $csv_data;
	}
	
	function wooexim_import_order()
	{
		global $wooexim_product,$wooexim_order, $wooexim_import_export;
		
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
		
		$order_field_list = $wooexim_order -> get_updated_order_fields();
		 
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
			
			$import_title_data = array();
			 
			if ( $fh !== FALSE ) { 
			
				while ( ( $new_line = fgetcsv($fh, 0) ) !== FALSE ) {
					
               		$import_data_temp[] = $new_line;
				}
				fclose( $fh );
				
				$import_title_data = $import_data_temp[0];
				
				unset( $import_data_temp[0]);
				
				$import_data = $import_data_temp;
				
				$return_value['message'] = 'success' ;
				
				$return_value['success_log'] = '<div class="wooexim_success_msg wooexim_order_import_success_msg">'.__('Order Imported successfully.',WOOEXIM_TEXTDOMAIN).'</div>';
				
 			}else{
			
 				$return_value['message_text'] =  __( 'Could not open file.', WOOEXIM_TEXTDOMAIN );
			}
			
			if(!empty($import_data))
			{
				$order_updated_data = $wooexim_order -> wooexim_create_new_order($import_data,$import_title_data);
				
				$error_msg = "";
				
				if(isset($order_updated_data['error']) && (!empty($order_updated_data['error'])))
				{
					foreach($order_updated_data['error'] as $error)
					{
						$error_msg = $error_msg.'<div class="wooexim_error_msg wooexim_order_import_error_msg" >'.$error.'</div>';
					}
				}
				if(isset($order_updated_data['wooexim_order_ids']) && (!empty($order_updated_data['wooexim_order_ids'])))
				{
					$return_value['data'] = $wooexim_order -> wooexim_get_new_order_data($order_updated_data);
				}
				
				$return_value['error_log'] = $error_msg;
				
 			}
		}
		
		echo json_encode($return_value );
		
		die();
	}
	function wooexim_create_new_order($import_data = array(), $import_title_data = array())
	{
		
		global $wooexim_product,$wooexim_order;
		
		$return_data = array();
		
		$order_field_key = 39;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$coupon_start_pos = 0;
		
		for($i=$order_field_key;$i<count($import_title_data);$i++)
		{
			$new_start_pos = strpos($import_title_data[$i],'Coupon Id');
			
			if($new_start_pos !== false)
			{
				$coupon_start_pos = $i;
				break;
			}
		}
		$product_filed_count = 6;
		
		$total_products = ceil(($i-$order_field_key)/$product_filed_count);
		
		$total_coupons = ceil((count($import_title_data) - $i)/2);
		
		$order_create_option = isset($_POST['wooexim_order_create_option'])?$_POST['wooexim_order_create_option']:"skip_order";
		
		foreach($import_data as $order_info)
		{
			$duplicate_order_id = $wooexim_order -> wc_get_order_id_by_order_key( $order_info[36] ) ;
			
			
 			if($duplicate_order_id>0 && $order_create_option=='skip_order')
			{
				$return_data['error'][] = sprintf( __( 'Order #%s already Exist.', WOOEXIM_TEXTDOMAIN ), $duplicate_order_id);
				
				$return_data['wooexim_order_ids'][] = $duplicate_order_id;
				
				continue;
			}
			
			$order_date = $order_info[2];
			
			$new_order_statuts = $order_info[33];
			
			if(function_exists('wc_get_order_statuses'))
			{
				if('wc-' !== substr( $new_order_statuts, 0, 3 ))
				{
					$new_order_statuts = 'wc-'.$new_order_statuts;
				}
			}
			else
			{
 				if('wc-' === substr( $new_order_statuts, 0, 3 ))
				{
					$new_order_statuts = substr( $new_order_statuts, 3 );
				}
			}
			
			$order_data = array(
				
				'post_name' => 'order-'.$order_date,
				'post_type' => 'shop_order',
				'post_title' => 'Order &ndash;'.$order_date,
				'post_status'=> 'publish',
				'ping_status' => 'closed',
				'post_excerpt' => 'Order place by '.$order_info[3].' '.$order_info[4],
				'post_date' => $order_date
				
			);
			
			if(function_exists('wc_get_order_statuses'))
			{
				$order_data['post_status'] = $new_order_statuts;
			}
	
			
			if($duplicate_order_id>0 && $order_create_option=='update_order')
			{
				$order_data['ID'] = $duplicate_order_id;
					
				$order_id = wp_update_post($order_data, false);
				
				$new_order = new WC_Order( $order_id );
				
				$old_order_items = $new_order -> get_items(array('line_item','coupon','shipping','tax'));
				
 				if(!empty($old_order_items))
				{
					foreach($old_order_items as $order_item_id => $item_value)
					{
						@wc_delete_order_item( $order_item_id );
					}
				}
				
			}
			else
			{
				$order_id = wp_insert_post($order_data,true);
				
				$new_order = new WC_Order( $order_id );
			}
			
			$new_order = new WC_Order( $order_id );
			
			if(!function_exists('wc_get_order_statuses'))
			{
				
 				$new_order->update_status( $new_order_statuts );
				
			}
 			
			$order_other_data = array();
			
			if($order_info[38]!="")
			{
				$order_other_data = maybe_unserialize($order_info[38]);
			}
			
			if(!empty($order_other_data))
			{
				if(isset($order_other_data['order_custom_fields']) && (!empty($order_other_data['order_custom_fields'])))
				{
					$order_custom_fields = maybe_unserialize($order_other_data['order_custom_fields']);
					
					foreach($order_custom_fields as $key=>$value)
					{
						update_post_meta($order_id, $key, $value[0]);
					}
				}
			}
			
			if($duplicate_order_id>0 && $order_create_option=='create_order')
			{
				update_post_meta( $order_id,'_order_key', 'wc_' . apply_filters('woocommerce_generate_order_key', uniqid('order_') ) );
			}
			else
			{
				update_post_meta($order_id,'_order_key',$order_info[36]);
			}
			
			$return_data['wooexim_order_ids'][] = $order_id;
			
			update_post_meta($order_id,'_billing_first_name',$order_info[3]);
			
			update_post_meta($order_id,'_billing_last_name',$order_info[4]);
			
			update_post_meta($order_id,'_billing_company',$order_info[5]);
			
			update_post_meta($order_id,'_billing_address_1',$order_info[6]);
			
			update_post_meta($order_id,'_billing_address_2',$order_info[7]);
			
			update_post_meta($order_id,'_billing_city',$order_info[8]);
			
			update_post_meta($order_id,'_billing_postcode',$order_info[9]);
			
			update_post_meta($order_id,'_billing_country',$order_info[10]);
			
			update_post_meta($order_id,'_billing_state',$order_info[11]);
			
			update_post_meta($order_id,'_billing_email',$order_info[12]);
			
			update_post_meta($order_id,'_billing_phone',$order_info[13]);
			
			update_post_meta($order_id,'_shipping_first_name',$order_info[14]);
			
			update_post_meta($order_id,'_shipping_last_name',$order_info[15]);
			
			update_post_meta($order_id,'_shipping_company',$order_info[16]);
			
			update_post_meta($order_id,'_shipping_address_1',$order_info[17]);
			
			update_post_meta($order_id,'_shipping_address_2',$order_info[18]);
			
			update_post_meta($order_id,'_shipping_city',$order_info[19]);
			
			update_post_meta($order_id,'_shipping_postcode',$order_info[20]);
			
			update_post_meta($order_id,'_shipping_state',$order_info[21]);
			
			update_post_meta($order_id,'_shipping_country',$order_info[22]);
			
			update_post_meta($order_id,'customer_note',$order_info[23]);
			
			update_post_meta($order_id,'_shipping_method_title',$order_info[24]);
			
			update_post_meta($order_id,'_payment_method_title',$order_info[25]);
			
			update_post_meta($order_id,'_cart_discount',$order_info[26]);
			
			update_post_meta($order_id,'_order_tax',$order_info[27]);
			
			update_post_meta($order_id,'_order_shipping_tax',$order_info[28]);
			
			update_post_meta($order_id,'_order_total',$order_info[29]);
			
			update_post_meta($order_id,'_completed_date',$order_info[30]);
			
			update_post_meta($order_id,'_payment_method',$order_info[34]);
			
			update_post_meta($order_id,'_order_discount',$order_info[35]);
			
			update_post_meta($order_id,'_order_currency',$order_info[37]);
			
			$customer_user = get_user_by('email',$order_info[12]);
			
			if($customer_user)
			{
				update_post_meta($order_id, '_customer_user', $customer_user->ID );
			}
						
			if(!empty($order_other_data))
			{
				if(isset($order_other_data['shop_order_refund']) && (!empty($order_other_data['shop_order_refund'])))
				{
					$shop_order_refund = array(
					
 						'post_name' => $order_other_data['shop_order_refund'][0] -> post_name,
						'post_type' => $order_other_data['shop_order_refund'][0]  -> post_type,
						'post_title' => $order_other_data['shop_order_refund'][0] -> post_title,
						'post_status'=> 'publish',
						'ping_status' => $order_other_data['shop_order_refund'][0] -> ping_status,
						'post_excerpt' => $order_other_data['shop_order_refund'][0] -> post_excerpt,
						'comment_status' => $order_other_data['shop_order_refund'][0] -> comment_status,
						'post_password' => $order_other_data['shop_order_refund'][0] -> post_password,
						'post_parent' => $order_id,
						'post_date' => $order_other_data['shop_order_refund'][0] -> post_date
					);
					
					if(function_exists('wc_get_order_statuses'))
					{
						$shop_order_refund['post_status'] = $order_other_data['shop_order_refund'][0] -> post_status;
					}
					
					if($duplicate_order_id>0 && $order_create_option=='update_order')
					{
						$child_post = get_posts(array('post_type'=>'shop_order_refund','post_status'=>'any','post_parent'=>$order->id,'fields' => 'ids'));
						
						if(!empty($child_post) && isset($child_post[0]) && $child_post[0]>0)
						{
						
 							$shop_order_refund['ID'] = $child_post[0];
						
							$shop_order_refund_id = wp_update_post($shop_order_refund, false);
							
						}
						else
						{
							$shop_order_refund_id = wp_insert_post($shop_order_refund,true);
						}
					}
					else
					{
						$shop_order_refund_id = wp_insert_post($shop_order_refund,true);
					}
					
					if(isset($order_other_data['shop_order_refund_attr']) && (!empty($order_other_data['shop_order_refund_attr'])))
					{
						foreach($order_other_data['shop_order_refund_attr'] as $key => $value)
						{
							update_post_meta($shop_order_refund_id,$key,$value[0]);
						}
					}
					
				}
			}
			
			$order_items = $order_other_data['order_items'];
			
			if(!empty($order_items) && isset($order_other_data['order_items']))
			{
				$temp_product_count = $order_field_key;
				
				foreach($order_items as $order_item)
				{
										
					$order_item_id = wc_add_order_item( $order_id, array(
						'order_item_name' 		=> $order_item['name'],
						'order_item_type' 		=> $order_item['type']
					) );
					
					if( $order_item_id  && is_array($order_item['item_meta']) && !empty($order_item['item_meta']))
					{
						if( $order_item['type'] == 'line_item')
						{
							if(isset($order_info[$temp_product_count + 5]) && $order_info[$temp_product_count + 5]!="")
							{
								$product_id_by_sku  = $wooexim_order -> wc_get_product_id_by_sku( $order_info[$temp_product_count + 5] );
								
								if($product_id_by_sku!="" && $product_id_by_sku>0)
								{
									$order_info[$temp_product_count] =  $product_id_by_sku;
								}
								
							}
							
							if(function_exists('get_product'))
							{
								$product = @get_product($order_info[$temp_product_count]);
							}
							else
							{
								$product = new WC_product($order_info[$temp_product_count]);
							}
							
							if($product)
							{
								if(isset($order_item['item_meta']['_product_id']))
								{
									$order_item['item_meta']['_product_id'][0] = $product->id;
								}
								if(isset($order_item['item_meta']['_variation_id']))
								{
									$order_item['item_meta']['_variation_id'][0] = isset( $product->variation_id ) ? $product->variation_id : 0;
								}
								
								if ( $product->backorders_require_notification() && $product->is_on_backorder($order_item['item_meta']['_qty'][0] ) ) 
								{
									wc_update_order_item_meta( $product_item_id, apply_filters( 'woocommerce_backordered_item_meta_name', __( 'Backordered',WOOEXIM_TEXTDOMAIN ) ), $product_qty - max( 0, $product->get_total_stock() ) );
								}
								
								if($product->is_downloadable())
								{
									$download_files = $product->get_files();
									
									foreach($download_files as $download_id=>$file)
									{
										@wc_downlodable_file_permission($download_id,$product->id,new WC_Order($order_id));
									}
								}
							}
							
							$temp_product_count = $temp_product_count + $product_filed_count;
							
						}
							
						foreach($order_item['item_meta'] as $order_item_meta_key => $order_item_meta_value)
						{
							wc_update_order_item_meta( $order_item_id, $order_item_meta_key, $order_item_meta_value[0] );	
						}
						
					}
				}
				
				
			}
			
			//Add product to Order
			
			if($total_products>0 && !isset($order_other_data['order_items']))
			{
				$temp_product_count = $order_field_key;
				
 				for($i=0;$i<$total_products;$i++)
				{
					if(isset($order_info[$temp_product_count + 5]) && $order_info[$temp_product_count + 5]!="")
					{
						$product_id_by_sku  = $wooexim_order -> wc_get_product_id_by_sku( $order_info[$temp_product_count + 5] );
						
						if($product_id_by_sku!="" && $product_id_by_sku>0)
						{
							$order_info[$temp_product_count] =  $product_id_by_sku;
						}
						
					}
					if(function_exists('get_product'))
					{
						$product = @get_product($order_info[$temp_product_count]);
					}
					else
					{
						$product = new WC_product($order_info[$temp_product_count]);
					}
					
 					if($product)
					{
						$product_qty = $order_info[$temp_product_count + 2];
						
						$line_subtotal_tax = $order_info[$temp_product_count + 3];
						
						$line_tax = $order_info[$temp_product_count + 4];
						
						$temp_product_count = $temp_product_count + $product_filed_count;
						
						
						$product_item_id = wc_add_order_item( $order_id, array(
						
							'order_item_name' => $product->get_title(),
							
							'order_item_type' => 'line_item'
							
						) );
				
						if ( $product_item_id ) {
							wc_update_order_item_meta( $product_item_id, '_qty',          intval( $product_qty ) );
							wc_update_order_item_meta( $product_item_id, '_tax_class',    $product->get_tax_class() );
							wc_update_order_item_meta( $product_item_id, '_product_id',   $product->id );
							wc_update_order_item_meta( $product_item_id, '_variation_id', isset( $product->variation_id ) ? $product->variation_id : 0 );
					
							// Set line item totals, either passed in or from the product
							wc_update_order_item_meta( $product_item_id, '_line_subtotal', $line_subtotal_tax);
							
							wc_update_order_item_meta( $product_item_id, '_line_total', $line_tax);
							
							wc_update_order_item_meta( $product_item_id, '_line_subtotal_tax',$line_subtotal_tax);
							
							wc_update_order_item_meta( $product_item_id, '_line_tax', $line_tax);
							
							wc_update_order_item_meta( $product_item_id, '_line_tax_data', array( 'total' => array(), 'subtotal' => array() ) );
					
							// Backorders
							if ( $product->backorders_require_notification() && $product->is_on_backorder( $product_qty ) ) {
								wc_update_order_item_meta( $product_item_id, apply_filters( 'woocommerce_backordered_item_meta_name', __( 'Backordered',WOOEXIM_TEXTDOMAIN ) ), $product_qty - max( 0, $product->get_total_stock() ) );
							}
							
							if($product->is_downloadable())
							{
								$download_files = $product->get_files();
								
								foreach($download_files as $download_id=>$file)
								{
									@wc_downlodable_file_permission($download_id,$product->id,new WC_Order($order_id));
								}
							}
					
							do_action( 'woocommerce_order_add_product', $order_id, $product_item_id, $product, $product_qty );
						}
			
					}
					
				}
			}
						
			
			//Add coupons to order
			if($total_coupons>0 && !isset($order_other_data['order_items']))
			{
				$temp_pos = $coupon_start_pos;
				
				for($i=0;$i<$total_coupons;$i++)
				{
					if($order_info[$temp_pos]!="")
					{
						$coupon_code = $order_info[$temp_pos];
						
						$discount_amount = $order_info[$temp_pos+1];
						
						$discount_amount_tax = 0;
						
						$temp_pos = $temp_pos+2;
						
						$coupon_item_id = wc_add_order_item( $order_id, array(
						'order_item_name' => $coupon_code,
						'order_item_type' => 'coupon'
						) );
				
						if ( $coupon_item_id ) {
						
							wc_update_order_item_meta( $coupon_item_id, 'discount_amount', $discount_amount );
							
							wc_update_order_item_meta( $coupon_item_id, 'discount_amount_tax', $discount_amount_tax );
					
							do_action( 'woocommerce_order_add_coupon',$order_id, $coupon_item_id, $coupon_code, $discount_amount, $discount_amount_tax );
						}
					}
				}
			}
			
			//$new_order->calculate_totals();
			
			wc_delete_shop_order_transients( $order_id );
			
			do_action('woocommerce_new_order',$order_id);
			
		}
		return $return_data;
		
	}
	
	function wc_get_order_id_by_order_key( $order_key = 0 ) 
	{
		global $wpdb;
	
		$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_order_key' AND meta_value = %s", $order_key ) );
	
		return $order_id;
	}
	
	function wc_get_product_id_by_sku( $sku ) 
	{
		global $wpdb;
	
		$product_id = $wpdb->get_var( $wpdb->prepare( "
			SELECT posts.ID
			FROM $wpdb->posts AS posts
			LEFT JOIN $wpdb->postmeta AS postmeta ON ( posts.ID = postmeta.post_id )
			WHERE posts.post_type IN ( 'product', 'product_variation' )
			AND postmeta.meta_key = '_sku' AND postmeta.meta_value = '%s'
			LIMIT 1
		 ", $sku ) );
	
		return ( $product_id ) ? intval( $product_id ) : 0;
	}
	function wooexim_get_new_order_data($wooexim_order_ids)
 	{
	
		global $wooexim_order;
	
		$order_field_list = $wooexim_order -> get_updated_order_fields();
		
		$order_list_data = $wooexim_order -> wooexim_get_order_data($wooexim_order_ids);
		
		$total_diff_no_product = 0;
		
		$total_coupons = 0;
		
		foreach($order_list_data as $order_data)
		{
			if($order_data->total_diff_no_product > $total_diff_no_product)
			{
				$total_diff_no_product = $order_data->total_diff_no_product;
			}
			
			if(count($order_data->order_coupons_list) > $total_coupons)
			{
				$total_coupons = count($order_data->order_coupons_list);
			}
		}
		
		ob_start();
		
		?>
		<div class="wooexim_filter_data_container">
			<div class="wooexim_product_imported_data_title_wrapper">
				<div class="wooexim_product_imported_data_title"><?php _e('Created/Existing Orders',WOOEXIM_TEXTDOMAIN);?></div>
			</div>
 			<div class="wooexim_product_filter_data_wrapper">
				<table class="wooexim_order_filter_data">
					<thead>
						<tr class="wooexim_order_filter_data_row">
							<th class="wooexim_order_filter_data_header"></th>
						<?php   foreach($order_field_list['order_field'] as $order_field){
									
									if($order_field['field_display']==1){?>
									
										<th class="wooexim_order_filter_data_header"><?php echo $order_field['field_value'];?></th>
										
									<?php 
									}
								}
								
								for($i=0;$i<$total_diff_no_product;$i++)
								{
									foreach($order_field_list['product_field'] as $order_field){
									
										if($order_field['field_display']==1){?>
										
											<th class="wooexim_order_filter_data_header"><?php echo $order_field['field_value'].' #'.($i+1);?></th>
											
										<?php }
		
									}
								}
								for($i=0;$i<$total_coupons;$i++)
								{
									foreach($order_field_list['order_coupon'] as $order_field){
									
										if($order_field['field_display']==1){?>
										
											<th class="wooexim_order_filter_data_header"><?php echo $order_field['field_value'].' #'.($i+1);?></th>
											
										<?php }
		
									}
								}
						?>
						</tr>
					</thead>
					<tbody>
						<?php 
						$temp_count_data = 0;
						foreach($order_list_data as $order_info){
						$temp_count_data++;
						?>
						<tr class="wooexim_order_filter_data_row">
							<td class="wooexim_order_filter_data_column"><?php echo $temp_count_data;?></td>
							<?php    foreach($order_field_list['order_field'] as $order_field){
									
										if($order_field['field_display']==1){?>
											
												<td class="wooexim_order_filter_data_column"><?php echo $order_info->$order_field['field_key'];?></td>
												
										<?php }
									
									}
									$temp_count = 0;
									foreach($order_info->product_list as $product_data)
									{
										foreach($order_field_list['product_field'] as $order_field){
										
											if($order_field['field_display']==1){?>
											
												<td class="wooexim_order_filter_data_column"><?php  echo $product_data[$order_field['field_key']];?></td>
												
											<?php }
			
										}
										$temp_count++;
									}
									
									for($i=$temp_count;$i<$total_diff_no_product;$i++)
									{
										foreach($order_field_list['product_field'] as $order_field){
										
											if($order_field['field_display']==1){?>
											
												<td class="wooexim_order_filter_data_column"></td>
												
											<?php }
			
										}
									}
									
									$temp_count = 0;
									
									foreach($order_info->order_coupons_list as $coupons_data)
									{
										foreach($order_field_list['order_coupon'] as $order_field){
										
											if($order_field['field_display']==1){?>
											
												<td class="wooexim_order_filter_data_column"><?php  echo $coupons_data[$order_field['field_key']];?></td>
												
											<?php }
			
										}
										$temp_count++;
									}
									
									for($i=$temp_count;$i<$total_coupons;$i++)
									{
										foreach($order_field_list['order_coupon'] as $order_field){
										
											if($order_field['field_display']==1){?>
											
												<td class="wooexim_order_filter_data_column"></td>
												
											<?php }
			
										}
									}
								
							?>
						</tr>
					   <?php }?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		
		$buffer_output = ob_get_contents();
		
		ob_end_clean();
		
		return $buffer_output ;

	}
	function get_new_order_fields()
	{
		global $wooexim_order;
		
		$order_fields = maybe_serialize($wooexim_order -> order_field_list());
		
		return $order_fields;
	}
	function get_updated_order_fields()
	{
		global $wooexim_order;
		
		$old_order_fields = $wooexim_order -> get_new_order_fields();
		
		$new_fields = get_option('wooexim_order_fields',$old_order_fields);
		
		$new_fields = maybe_unserialize($new_fields);
		
		return $new_fields;
	}
	function wooexim_save_order_fields()
	{
		global $wooexim_order;
		
		$old_order_fields = $wooexim_order -> get_updated_order_fields();
		
		$new_fields = array();
		
		foreach($old_order_fields as $order_fields_key=>$order_fields_data)
		{
 			foreach($order_fields_data as $key=>$value)
			{
				$new_fields[$order_fields_key][$key]['field_key'] = $value['field_key'];
				
				$new_fields[$order_fields_key][$key]['field_display'] = $value['field_display'];
				
				$new_fields[$order_fields_key][$key]['field_title'] = $value['field_title'];
				
				$new_fields[$order_fields_key][$key]['field_value'] = isset($_POST['wooexim_'.$value['field_key'].'_field'])?$_POST['wooexim_'.$value['field_key'].'_field']:"";
				
  			}
		}
		
		$new_fields_data = maybe_serialize($new_fields);
		
		update_option('wooexim_order_fields', $new_fields_data);
		
		$return_value = array();
		
		$return_value['message']	= 'success';
		
		$return_value['message_content']	= __('Changes Saved Successfully.',WOOEXIM_TEXTDOMAIN);
			
		echo json_encode($return_value );
		
		die();
	}
	function save_order_scheduled()
  	{
		global $wooexim_scheduled;
		
		$general_options_data 		= $_POST;
		
		$wooexim_export_interval 			= isset($_POST['wooexim_export_interval'])?$_POST['wooexim_export_interval']:"";
		
		$return_value = array();
		
		if($wooexim_export_interval!="")
		{
		
			$scheduled_id = uniqid();
			
			$scheduled_data = $wooexim_scheduled -> get_order_scheduled_data();
			
			$scheduled_data[$scheduled_id] = $general_options_data;
			
			$scheduled_new_data = @maybe_serialize($scheduled_data);
			
			update_option('wooexim_order_scheduled_data',$scheduled_new_data);
						
			wp_schedule_event( time(), $wooexim_export_interval, 'wooexim_cron_scheduled_order_export',array($scheduled_id) );
			
			$return_value['message']		= 'success';
			
		}		
		else
		{
			$return_value['message']		= 'error';
		}
		
		echo json_encode($return_value );
		
		die();
	}	
}
?>