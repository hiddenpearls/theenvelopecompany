<?php 
	if (!defined('ABSPATH'))
    die("Can't load this file directly");
	
	global $wooexim_product, $wooexim_order, $wooexim_user, $wooexim_product_cat, $wooexim_coupon;
	
	$product_cat = $wooexim_product -> wooexim_get_product_category();

	$product_list = $wooexim_product -> wooexim_get_product();
	
	$author_list = $wooexim_product -> wooexim_get_author_list();
	
	$order_status = $wooexim_order -> get_woo_order_status();
	
	$order_ids = $wooexim_order -> get_order_list();
	
	$get_schedules_list = wp_get_schedules();
	
	$user_list = $wooexim_user -> get_user_list();
	
	$coupon_list = $wooexim_coupon -> get_coupon_list();
	
	$product_total = count($product_list)<2000?count($product_list):'2000+';
	
	$order_total = count($order_ids)<2000?count($order_ids):'2000+';
	
	$user_total = count($user_list)<2000?count($user_list):'2000+';
	
	$product_cat_total = count($product_cat)<2000?count($product_cat):'2000+';
	
	$coupon_total = count($coupon_list)<2000?count($coupon_list):'2000+';

	
?>
<div class="wooexim_product_export_wrapper">
	<div class="woo-banner">
		<div class="woo-logo">
			<img src="<?php echo WOOEXIM_IMAGES_URL . '/wooexim.jpg'; ?>" />
			<h3><?php _e('WOOEXIM - WooCommerce Export Import Plugin',WOOEXIM_TEXTDOMAIN);?></h3>
		</div>
		<div class="wooexim_product_export_belt_wrapper">
			<div class="wooexim_product_export_belt wooexim_product_title_belt wooexim_selected">
				<?php _e('Products',WOOEXIM_TEXTDOMAIN);?>
				<div class="wooexim_total_export_count"><?php echo $product_total;?></div>
			</div>
			<div class="wooexim_product_export_belt wooexim_product_cat_title_belt">
				<?php _e('Categories',WOOEXIM_TEXTDOMAIN);?>
				<div class="wooexim_total_export_count"><?php echo $product_cat_total;?></div>
			</div>
			<div class="wooexim_product_export_belt wooexim_order_title_belt">
				<?php _e('Orders',WOOEXIM_TEXTDOMAIN);?>
				<div class="wooexim_total_export_count"><?php echo $order_total;?></div>
			</div>
			<div class="wooexim_product_export_belt wooexim_user_title_belt">
				<?php _e('Users',WOOEXIM_TEXTDOMAIN);?>
				<div class="wooexim_total_export_count"><?php echo $user_total;?></div>
			</div>
			<div class="wooexim_product_export_belt wooexim_coupons_title_belt">
				<?php _e('Coupons',WOOEXIM_TEXTDOMAIN);?>
				<div class="wooexim_total_export_count"><?php echo $coupon_total;?></div>
			</div>
		</div>
	</div>
	<div class="wooexim_product_export_container">
		<div class="wooexim_product_export_inner_container">
			<div class="wooexim_success_msg" style="display: block;">Only product export and import is enabled in the free version. To have all the features get the premium version here <a style="color: white;" target="_blank" href="http://wooexim.com/">WOOEXIM Pro</a></div>
			<div class="wooexim_success_msg wooexim_scheduled_export_success_msg"><?php _e('Export Data Successfully Scheduled.',WOOEXIM_TEXTDOMAIN);?></div>
			<form method="post" class="wooexim_product_export_frm wooexim_all_export_frm">
				<input type="hidden" value="0" name="wooexim_product_export_verify" class="wooexim_product_export_verify" />
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By Product Category',WOOEXIM_TEXTDOMAIN);?> <div class="wooexim_field_tital_recordes">(<?php echo  __('Total Categories',WOOEXIM_TEXTDOMAIN).' : '.$product_cat_total;?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_product_category[]" multiple="multiple" data-placeholder="<?php _e('Select Product Category',WOOEXIM_TEXTDOMAIN);?>">
 						</select>
						<div class="wooexim_default_notice"><?php _e('Default : All Categories(Premium Feature).',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By Product ID / Name',WOOEXIM_TEXTDOMAIN);?>  <div class="wooexim_field_tital_recordes">(<?php echo __('Total Products',WOOEXIM_TEXTDOMAIN).' : '.$product_total;?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_product_ids[]" multiple="multiple" data-placeholder="<?php _e('Select Product',WOOEXIM_TEXTDOMAIN);?>">
 						</select>
						<div class="wooexim_default_notice"><?php _e('Default : All Products(Premium Feature).',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By Product Author Name / Email',WOOEXIM_TEXTDOMAIN);?>  <div class="wooexim_field_tital_recordes">(<?php echo __('Total Authors',WOOEXIM_TEXTDOMAIN).' : '.$user_total;?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_product_author_ids[]" multiple="multiple" data-placeholder="<?php _e('Select Product Author',WOOEXIM_TEXTDOMAIN);?>">
 						</select>
						<div class="wooexim_default_notice"><?php _e('Default : All Authors(Premium Feature).',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Limit Records',WOOEXIM_TEXTDOMAIN);?> <div class="wooexim_field_tital_recordes"> (<?php echo __('Total Records',WOOEXIM_TEXTDOMAIN).' : '.$product_total;?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<input type="text" class="wooexim_export_field_input_element" name="total_records" value="100" disabled = "disabled" />
						<input type="hidden" name="wooexim_total_records" value="100" />
						<div class="wooexim_default_notice"><?php _e('Default : All Records(Max 100 for Free).',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Offset Records',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="text" class="wooexim_export_field_input_element" name="wooexim_offset_records" placeholder="<?php _e('Enter Offset Records',WOOEXIM_TEXTDOMAIN);?>"/>
						<div class="wooexim_default_notice"><?php _e('Default : 0.',WOOEXIM_TEXTDOMAIN);echo " ";_e('Note : Fetch Records after XX Records.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By Date',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<div class="wooexim_export_start_date_field_wrapper">
							<input type="text" class="wooexim_export_field_input_element wooexim_export_field_date_element" name="start_date" placeholder="<?php _e('Start Date',WOOEXIM_TEXTDOMAIN);?>"/>
							<div class="wooexim_default_notice"><?php _e('Date Format',WOOEXIM_TEXTDOMAIN);?> : mm-dd-yyyy</div>
						</div>
						<div class="wooexim_export_end_date_field_wrapper">
							<input type="text" class="wooexim_export_field_input_element wooexim_export_field_date_element" name="end_date" placeholder="<?php _e('End Date',WOOEXIM_TEXTDOMAIN);?>"/>
							<div class="wooexim_default_notice"><?php _e('Date Format',WOOEXIM_TEXTDOMAIN);?> : mm-dd-yyyy</div>
						</div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Scheduled Export(Premium Feature)',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="checkbox" id="wooexim_product_scheduled_export" class="wooexim_export_field_input_element wooexim_scheduled_export_check_element" name="roduct_scheduled_export" placeholder="<?php _e('Enter Offset Records',WOOEXIM_TEXTDOMAIN);?>"/><label for="wooexim_product_scheduled_export" class="wooexim_product_scheduled_export_label"><?php _e('Scheduled Export',WOOEXIM_TEXTDOMAIN);?></label>
						<div class="wooexim_scheduled_export_wrapper">
							<div class="wooexim_scheduled_export_outer_details">
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Export Interval',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<select class="wooexim_export_field_select_element" data-placeholder="<?php _e('Select Interval',WOOEXIM_TEXTDOMAIN);?>" name="export_interval">
											<?php foreach($get_schedules_list as $key=>$value){?>
											<option value="<?php echo $key;?>"><?php echo $value['display'];?></option>
											<?php }?>
										</select>
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Send E-mail',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input type="checkbox" class="wooexim_export_field_input_element wooexim_scheduled_send_email" name="product_scheduled_send_email" value="1"/>
										<div class="wooexim_default_notice"><?php _e('Send E-mail with attachment.',WOOEXIM_TEXTDOMAIN);?></div>
									</div>
								</div>
							</div>
							<div class="wooexim_scheduled_export_email_details">
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email Recipient(s)',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input class="wooexim_export_field_input_element wooexim_scheduled_export_email_recipients" type="text" placeholder="<?php _e('Enter Email Recipient(s)',WOOEXIM_TEXTDOMAIN);?>" name="scheduled_export_email_recipients">
										<div class="wooexim_default_notice">Exa. example@gmail.com, demo@yahoo.com</div>
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email Subject',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input class="wooexim_export_field_input_element wooexim_scheduled_export_email_subject" type="text" placeholder="<?php _e('Enter Email Subject',WOOEXIM_TEXTDOMAIN);?>" name="scheduled_export_email_subject">
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email message',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<textarea class="wooexim_scheduled_export_email_content wooexim_scheduled_export_text_area" name="scheduled_export_email_content" placeholder="<?php _e('Enter Email message',WOOEXIM_TEXTDOMAIN);?>"></textarea>
									</div>
								</div>
							</div>
						</div>
 					</div>
						
				</div>
				<div class="wooexim_export_field_btn_container">
					<div class="wooexim_export_field_btn_wrapper">
						
						<button class="wooexim_product_preview_btn wooexim_form_submit_btn" type="button"><div class="wooexim_ajax_loader"></div><?php _e('Preview',WOOEXIM_TEXTDOMAIN);?></button>
						<button class="wooexim_product_export_btn wooexim_form_submit_btn" type="button"><?php _e('Export',WOOEXIM_TEXTDOMAIN);?></button>
						<button class="wooexim_form_submit_btn" type="button"><?php _e('Save Scheduled',WOOEXIM_TEXTDOMAIN);?></button>
					</div>
				</div>
			</form>
			<form class="wooexim_order_export_frm wooexim_all_export_frm " method="post" name="wooexim_order_export_data">
				<input type="hidden" value="0" class="wooexim_ordert_export_verify" name="wooexim_ordert_export_verify"/>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By Order Status',WOOEXIM_TEXTDOMAIN);?> <div class="wooexim_field_tital_recordes"> (<?php echo __('Total Status',WOOEXIM_TEXTDOMAIN).' : '.count($order_status);?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_order_status[]" multiple="multiple" data-placeholder="<?php _e('Select Order Status',WOOEXIM_TEXTDOMAIN);?>">
 							<?php 
								if(function_exists('wc_get_order_statuses'))
								{
									
									global $wpdb;
									
									foreach ($order_status as $key=>$value){
									
										$total_query = ' SELECT COUNT(*) as nb from '.$wpdb->prefix.'posts where post_status="'.$key.'" and post_type="shop_order" ';
				
											$total = $wpdb->get_var($total_query);
										
										?>
										<option value="<?php echo $key;?>" ><?php echo $value; ?> (<?php echo $total; ?>)</option>
										
									<?php 
									}
								}
								else
								{	
									
								 foreach ($order_status as $status){ ?>
									<option value="<?php echo $status->term_id; ?>" ><?php _e($status->name, 'woocommerce'); ?> (<?php echo $status->count;?>)</option>				
								<?php } 
								
								}
								?>
  						</select>
						<div class="wooexim_default_notice"><?php _e('Default : All Status',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By Product Category',WOOEXIM_TEXTDOMAIN);?><div class="wooexim_field_tital_recordes"> (<?php echo __('Total Categories',WOOEXIM_TEXTDOMAIN).' : '.$product_cat_total;?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_product_category[]" multiple="multiple" data-placeholder="<?php _e('Select Product Category',WOOEXIM_TEXTDOMAIN);?>">
							<?php foreach($product_cat as $cat){?>
								<option value="<?php echo $cat->term_id;?>"><?php echo '('.__('ID',WOOEXIM_TEXTDOMAIN).' : '.$cat->term_id.') '. $cat->name;?> (<?php echo  __('Total Products',WOOEXIM_TEXTDOMAIN).' : '.$cat->count;?>)</option>
								<?php }?>
 						</select>
						<div class="wooexim_default_notice"><?php _e('Default : All Category.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By Product ID / Name',WOOEXIM_TEXTDOMAIN);?><div class="wooexim_field_tital_recordes"> (<?php echo __('Total Products',WOOEXIM_TEXTDOMAIN).' : '.$product_total;?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_product_ids[]" multiple="multiple" data-placeholder="<?php _e('Select Product',WOOEXIM_TEXTDOMAIN);?>">
							<?php foreach($product_list as $product_data){?>
								<option value="<?php echo $product_data->ID;?>"><?php echo '('.__('ID',WOOEXIM_TEXTDOMAIN).' : '.$product_data->ID.') '.$product_data->post_title;?></option>
								<?php }?>
 						</select>
						<div class="wooexim_default_notice"><?php _e('Default : All Product.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By Order Id',WOOEXIM_TEXTDOMAIN);?><div class="wooexim_field_tital_recordes"> (<?php echo __('Total Order',WOOEXIM_TEXTDOMAIN).' : '.$order_total;?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_order_ids[]" multiple="multiple" data-placeholder="<?php _e('Select Order ID',WOOEXIM_TEXTDOMAIN);?>">
							<?php foreach($order_ids as $order_data){?>
								<option value="<?php echo $order_data;?>"><?php _e('Order ID :',WOOEXIM_TEXTDOMAIN)?> <?php echo $order_data;?></option>
								<?php }?>
 						</select>
						<div class="wooexim_default_notice"><?php _e('Default : All Order ID.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
 				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By Date',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<div class="wooexim_export_start_date_field_wrapper">
							<input type="text" class="wooexim_export_field_input_element wooexim_export_field_date_element" name="wooexim_start_date" placeholder="<?php _e('Start Date',WOOEXIM_TEXTDOMAIN);?>"/>
							<div class="wooexim_default_notice"><?php _e('Date Format',WOOEXIM_TEXTDOMAIN);?> : mm-dd-yyyy</div>
						</div>
						<div class="wooexim_export_end_date_field_wrapper">
							<input type="text" class="wooexim_export_field_input_element wooexim_export_field_date_element" name="wooexim_end_date" placeholder="<?php _e('End Date',WOOEXIM_TEXTDOMAIN);?>"/>
							<div class="wooexim_default_notice"><?php _e('Date Format',WOOEXIM_TEXTDOMAIN);?> : mm-dd-yyyy</div>
						</div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Scheduled Export',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="checkbox" id="wooexim_order_scheduled_export" class="wooexim_export_field_input_element wooexim_scheduled_export_check_element" name="wooexim_product_scheduled_export" placeholder="<?php _e('Enter Offset Records',WOOEXIM_TEXTDOMAIN);?>"/><label for="wooexim_order_scheduled_export" class="wooexim_product_scheduled_export_label"><?php _e('Scheduled Export',WOOEXIM_TEXTDOMAIN);?></label>
						<div class="wooexim_scheduled_export_wrapper">
							<div class="wooexim_scheduled_export_outer_details">
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Export Interval',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<select class="wooexim_export_field_select_element" data-placeholder="<?php _e('Select Interval',WOOEXIM_TEXTDOMAIN);?>" name="wooexim_export_interval">
											<?php foreach($get_schedules_list as $key=>$value){?>
											<option value="<?php echo $key;?>"><?php echo $value['display'];?></option>
											<?php }?>
										</select>
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Send E-mail',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input type="checkbox" class="wooexim_export_field_input_element wooexim_scheduled_send_email" name="wooexim_order_scheduled_send_email" value="1"/>
										<div class="wooexim_default_notice"><?php _e('Send E-mail with attachment.',WOOEXIM_TEXTDOMAIN);?></div>
									</div>
								</div>
							</div>
							<div class="wooexim_scheduled_export_email_details">
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email Recipient(s)',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input class="wooexim_export_field_input_element wooexim_scheduled_export_email_recipients" type="text" placeholder="<?php _e('Enter Email Recipient(s)',WOOEXIM_TEXTDOMAIN);?>" name="wooexim_scheduled_export_email_recipients">
										<div class="wooexim_default_notice">Exa. example@gmail.com, demo@yahoo.com</div>
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email Subject',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input class="wooexim_export_field_input_element wooexim_scheduled_export_email_subject" type="text" placeholder="<?php _e('Enter Email Subject',WOOEXIM_TEXTDOMAIN);?>" name="wooexim_scheduled_export_email_subject">
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email message',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<textarea class="wooexim_scheduled_export_email_content wooexim_scheduled_export_text_area" name="wooexim_scheduled_export_email_content" placeholder="<?php _e('Enter Email message',WOOEXIM_TEXTDOMAIN);?>"></textarea>
									</div>
								</div>
							</div>
						</div>
 					</div>
						
				</div>
				<div class="wooexim_export_field_btn_container">
					<div class="wooexim_export_field_btn_wrapper">
						
						<button class="wooexim_form_submit_btn" type="button"><div class="wooexim_ajax_loader"></div><?php _e('Preview',WOOEXIM_TEXTDOMAIN);?></button>
						<button class="wooexim_form_submit_btn" type="button"><?php _e('Export',WOOEXIM_TEXTDOMAIN);?></button>
						
						<button class="wooexim_form_submit_btn" type="button"><?php _e('Save Scheduled',WOOEXIM_TEXTDOMAIN);?></button>
					</div>
				</div>
				
			</form>
			<form method="post" class="wooexim_user_export_frm wooexim_all_export_frm">
				<input type="hidden" value="0" name="wooexim_user_export_verify" class="wooexim_user_export_verify" />
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By User ID / Username / Email',WOOEXIM_TEXTDOMAIN);?><div class="wooexim_field_tital_recordes"> (<?php echo __('Total Users',WOOEXIM_TEXTDOMAIN).' : '.$user_total;?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_user_id[]" multiple="multiple" data-placeholder="<?php _e('Select User',WOOEXIM_TEXTDOMAIN);?>">
							<?php
							if(!empty($user_list) ){
								foreach($user_list as $new_user){?>
										<option value="<?php echo $new_user->ID;?>"><?php echo '('.__('ID',WOOEXIM_TEXTDOMAIN).' : '.$new_user->ID.') '.$new_user->display_name.' ( '.$new_user->user_email.' )';?></option>
								<?php }
							}?>
 						</select>
						<div class="wooexim_default_notice"><?php _e('Default : All Users.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By User Role',WOOEXIM_TEXTDOMAIN);?><div class="wooexim_field_tital_recordes"> (<?php echo __('Total Roles',WOOEXIM_TEXTDOMAIN).' : '.count(get_editable_roles());?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_user_role[]" multiple="multiple" data-placeholder="<?php _e('Select User Role',WOOEXIM_TEXTDOMAIN);?>">
						<?php wp_dropdown_roles();?>
							
 						</select>
						<div class="wooexim_default_notice"><?php _e('Default : All User Roles.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
                <div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By User Minimum Spend',WOOEXIM_TEXTDOMAIN);?><div class="wooexim_field_tital_recordes"></div></div>
					<div class="wooexim_export_field_wrapper">
						<input type="text" class="wooexim_export_field_input_element" name="wooexim_user_min_spend" placeholder="<?php _e('Enter Amount',WOOEXIM_TEXTDOMAIN);?>"/>
						<div class="wooexim_default_notice"><?php _e('Default',WOOEXIM_TEXTDOMAIN);?> : 0</div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Limit Records',WOOEXIM_TEXTDOMAIN);?><div class="wooexim_field_tital_recordes"> (<?php echo __('Total Users',WOOEXIM_TEXTDOMAIN).' : '.$user_total;?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<input type="text" class="wooexim_export_field_input_element" name="wooexim_total_records" placeholder="<?php _e('Enter Limit Records',WOOEXIM_TEXTDOMAIN);?>"/>
						<div class="wooexim_default_notice"><?php _e('Default : All Records.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Offset Records',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="text" class="wooexim_export_field_input_element" name="wooexim_offset_records" placeholder="<?php _e('Enter Offset Records',WOOEXIM_TEXTDOMAIN);?>"/>
						<div class="wooexim_default_notice"><?php _e('Default : 0.',WOOEXIM_TEXTDOMAIN);echo " ";_e('Note : Fetch Records after XX Records.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By Date',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<div class="wooexim_export_start_date_field_wrapper">
							<input type="text" class="wooexim_export_field_input_element wooexim_export_field_date_element" name="wooexim_start_date" placeholder="<?php _e('Start Date',WOOEXIM_TEXTDOMAIN);?>"/>
							<div class="wooexim_default_notice"><?php _e('Date Format',WOOEXIM_TEXTDOMAIN);?> : mm-dd-yyyy</div>
						</div>
						<div class="wooexim_export_end_date_field_wrapper">
							<input type="text" class="wooexim_export_field_input_element wooexim_export_field_date_element" name="wooexim_end_date" placeholder="<?php _e('End Date',WOOEXIM_TEXTDOMAIN);?>"/>
							<div class="wooexim_default_notice"><?php _e('Date Format',WOOEXIM_TEXTDOMAIN);?> : mm-dd-yyyy</div>
						</div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Scheduled Export',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="checkbox" id="wooexim_user_scheduled_export" class="wooexim_export_field_input_element wooexim_scheduled_export_check_element" name="wooexim_user_scheduled_export" placeholder="<?php _e('Enter Offset Records',WOOEXIM_TEXTDOMAIN);?>"/><label for="wooexim_user_scheduled_export" class="wooexim_product_scheduled_export_label"><?php _e('Scheduled Export',WOOEXIM_TEXTDOMAIN);?></label>
						<div class="wooexim_scheduled_export_wrapper">
							<div class="wooexim_scheduled_export_outer_details">
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Export Interval',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<select class="wooexim_export_field_select_element" data-placeholder="<?php _e('Select Interval',WOOEXIM_TEXTDOMAIN);?>" name="wooexim_export_interval">
											<?php foreach($get_schedules_list as $key=>$value){?>
											<option value="<?php echo $key;?>"><?php echo $value['display'];?></option>
											<?php }?>
										</select>
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Send E-mail',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input type="checkbox" class="wooexim_export_field_input_element wooexim_scheduled_send_email" name="wooexim_user_scheduled_send_email" value="1"/>
										<div class="wooexim_default_notice"><?php _e('Send E-mail with attachment.',WOOEXIM_TEXTDOMAIN);?></div>
									</div>
								</div>
							</div>
							<div class="wooexim_scheduled_export_email_details">
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email Recipient(s)',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input class="wooexim_export_field_input_element wooexim_scheduled_export_email_recipients" type="text" placeholder="<?php _e('Enter Email Recipient(s)',WOOEXIM_TEXTDOMAIN);?>" name="wooexim_scheduled_export_email_recipients">
										<div class="wooexim_default_notice">Exa. example@gmail.com, demo@yahoo.com</div>
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email Subject',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input class="wooexim_export_field_input_element wooexim_scheduled_export_email_subject" type="text" placeholder="<?php _e('Enter Email Subject',WOOEXIM_TEXTDOMAIN);?>" name="wooexim_scheduled_export_email_subject">
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email message',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<textarea class="wooexim_scheduled_export_email_content wooexim_scheduled_export_text_area" name="wooexim_scheduled_export_email_content" placeholder="<?php _e('Enter Email message',WOOEXIM_TEXTDOMAIN);?>"></textarea>
									</div>
								</div>
							</div>
						</div>
 					</div>
						
				</div>
				<div class="wooexim_export_field_btn_container">
					<div class="wooexim_export_field_btn_wrapper">
						
						<button class="wooexim_form_submit_btn" type="button"><div class="wooexim_ajax_loader"></div><?php _e('Preview',WOOEXIM_TEXTDOMAIN);?></button>
						<button class="wooexim_form_submit_btn" type="button"><?php _e('Export',WOOEXIM_TEXTDOMAIN);?></button>
						<button class="wooexim_form_submit_btn" type="button"><?php _e('Save Scheduled',WOOEXIM_TEXTDOMAIN);?></button>
					</div>
				</div>
			</form>
			<form method="post" class="wooexim_product_cat_export_frm wooexim_all_export_frm">
				<input type="hidden" value="0" name="wooexim_product_cat_export_verify" class="wooexim_product_cat_export_verify" />
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By Product Category',WOOEXIM_TEXTDOMAIN);?> <div class="wooexim_field_tital_recordes">(<?php echo  __('Total Categories',WOOEXIM_TEXTDOMAIN).' : '.$product_cat_total;?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_product_category[]" multiple="multiple" data-placeholder="<?php _e('Select Product Category',WOOEXIM_TEXTDOMAIN);?>">
							<?php foreach($product_cat as $cat){?>
								<option value="<?php echo $cat->term_id;?>"><?php echo '('.__('ID',WOOEXIM_TEXTDOMAIN).' : '.$cat->term_id.') '. $cat->name;?> (<?php echo  __('Total Products',WOOEXIM_TEXTDOMAIN).' : '.$cat->count;?>)</option>
								<?php }?>
 						</select>
						<div class="wooexim_default_notice"><?php _e('Default : All Categories.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Limit Records',WOOEXIM_TEXTDOMAIN);?> <div class="wooexim_field_tital_recordes"> (<?php echo __('Total Records',WOOEXIM_TEXTDOMAIN).' : '.$product_cat_total;?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<input type="text" class="wooexim_export_field_input_element" name="wooexim_total_records" placeholder="<?php _e('Enter Limit Records',WOOEXIM_TEXTDOMAIN);?>"/>
						<div class="wooexim_default_notice"><?php _e('Default : All Records.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Offset Records',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="text" class="wooexim_export_field_input_element" name="wooexim_offset_records" placeholder="<?php _e('Enter Offset Records',WOOEXIM_TEXTDOMAIN);?>"/>
						<div class="wooexim_default_notice"><?php _e('Default : 0.',WOOEXIM_TEXTDOMAIN);echo " ";_e('Note : Fetch Records after XX Records.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Scheduled Export',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="checkbox" id="wooexim_product_cat_scheduled_export" class="wooexim_export_field_input_element wooexim_scheduled_export_check_element" name="wooexim_product_scheduled_export" placeholder="<?php _e('Enter Offset Records',WOOEXIM_TEXTDOMAIN);?>"/><label for="wooexim_product_cat_scheduled_export" class="wooexim_product_scheduled_export_label"><?php _e('Scheduled Export',WOOEXIM_TEXTDOMAIN);?></label>
						<div class="wooexim_scheduled_export_wrapper">
							<div class="wooexim_scheduled_export_outer_details">
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Export Interval',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<select class="wooexim_export_field_select_element" data-placeholder="<?php _e('Select Interval',WOOEXIM_TEXTDOMAIN);?>" name="wooexim_export_interval">
											<?php foreach($get_schedules_list as $key=>$value){?>
											<option value="<?php echo $key;?>"><?php echo $value['display'];?></option>
											<?php }?>
										</select>
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Send E-mail',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input type="checkbox" class="wooexim_export_field_input_element wooexim_scheduled_send_email" name="wooexim_product_scheduled_send_email" value="1"/>
										<div class="wooexim_default_notice"><?php _e('Send E-mail with attachment.',WOOEXIM_TEXTDOMAIN);?></div>
									</div>
								</div>
							</div>
							<div class="wooexim_scheduled_export_email_details">
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email Recipient(s)',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input class="wooexim_export_field_input_element wooexim_scheduled_export_email_recipients" type="text" placeholder="<?php _e('Enter Email Recipient(s)',WOOEXIM_TEXTDOMAIN);?>" name="wooexim_scheduled_export_email_recipients">
										<div class="wooexim_default_notice">Exa. example@gmail.com, demo@yahoo.com</div>
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email Subject',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input class="wooexim_export_field_input_element wooexim_scheduled_export_email_subject" type="text" placeholder="<?php _e('Enter Email Subject',WOOEXIM_TEXTDOMAIN);?>" name="wooexim_scheduled_export_email_subject">
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email message',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<textarea class="wooexim_scheduled_export_email_content wooexim_scheduled_export_text_area" name="wooexim_scheduled_export_email_content" placeholder="<?php _e('Enter Email message',WOOEXIM_TEXTDOMAIN);?>"></textarea>
									</div>
								</div>
							</div>
						</div>
 					</div>
						
				</div>
				<div class="wooexim_export_field_btn_container">
					<div class="wooexim_export_field_btn_wrapper">
						
						<button class="wooexim_form_submit_btn" type="button"><div class="wooexim_ajax_loader"></div><?php _e('Preview',WOOEXIM_TEXTDOMAIN);?></button>
						<button class="wooexim_form_submit_btn" type="button"><?php _e('Export',WOOEXIM_TEXTDOMAIN);?></button>
						<button class="wooexim_form_submit_btn" type="button"><?php _e('Save Scheduled',WOOEXIM_TEXTDOMAIN);?></button>
					</div>
				</div>
			</form>
			<form method="post" class="wooexim_coupon_export_frm wooexim_all_export_frm">
				<input type="hidden" value="0" name="wooexim_coupon_export_verify" class="wooexim_coupon_export_verify" />
				
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By Coupons ID / Code',WOOEXIM_TEXTDOMAIN);?>  <div class="wooexim_field_tital_recordes">(<?php echo __('Total Coupons',WOOEXIM_TEXTDOMAIN).' : '.$coupon_total;?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_coupon_ids[]" multiple="multiple" data-placeholder="<?php _e('Select Coupons',WOOEXIM_TEXTDOMAIN);?>">
							<?php foreach($coupon_list as $coupon_data){?>
								<option value="<?php echo $coupon_data->ID;?>"><?php echo '('.__('ID',WOOEXIM_TEXTDOMAIN).' : '.$coupon_data->ID.') '.$coupon_data->post_title;?></option>
								<?php }?>
 						</select>
						<div class="wooexim_default_notice"><?php _e('Default : All Coupons.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Limit Records',WOOEXIM_TEXTDOMAIN);?> <div class="wooexim_field_tital_recordes"> (<?php echo __('Total Records',WOOEXIM_TEXTDOMAIN).' : '.$coupon_total;?>)</div></div>
					<div class="wooexim_export_field_wrapper">
						<input type="text" class="wooexim_export_field_input_element" name="wooexim_total_records" placeholder="<?php _e('Enter Limit Records',WOOEXIM_TEXTDOMAIN);?>"/>
						<div class="wooexim_default_notice"><?php _e('Default : All Records.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Offset Records',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="text" class="wooexim_export_field_input_element" name="wooexim_offset_records" placeholder="<?php _e('Enter Offset Records',WOOEXIM_TEXTDOMAIN);?>"/>
						<div class="wooexim_default_notice"><?php _e('Default : 0.',WOOEXIM_TEXTDOMAIN);echo " ";_e('Note : Fetch Records after XX Records.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Filter By Date',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<div class="wooexim_export_start_date_field_wrapper">
							<input type="text" class="wooexim_export_field_input_element wooexim_export_field_date_element" name="wooexim_start_date" placeholder="<?php _e('Start Date',WOOEXIM_TEXTDOMAIN);?>"/>
							<div class="wooexim_default_notice"><?php _e('Date Format',WOOEXIM_TEXTDOMAIN);?> : mm-dd-yyyy</div>
						</div>
						<div class="wooexim_export_end_date_field_wrapper">
							<input type="text" class="wooexim_export_field_input_element wooexim_export_field_date_element" name="wooexim_end_date" placeholder="<?php _e('End Date',WOOEXIM_TEXTDOMAIN);?>"/>
							<div class="wooexim_default_notice"><?php _e('Date Format',WOOEXIM_TEXTDOMAIN);?> : mm-dd-yyyy</div>
						</div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Scheduled Export',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="checkbox" id="wooexim_coupon_scheduled_export" class="wooexim_export_field_input_element wooexim_scheduled_export_check_element" name="wooexim_product_scheduled_export" placeholder="<?php _e('Enter Offset Records',WOOEXIM_TEXTDOMAIN);?>"/><label for="wooexim_coupon_scheduled_export" class="wooexim_product_scheduled_export_label"><?php _e('Scheduled Export',WOOEXIM_TEXTDOMAIN);?></label>
						<div class="wooexim_scheduled_export_wrapper">
							<div class="wooexim_scheduled_export_outer_details">
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Export Interval',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<select class="wooexim_export_field_select_element" data-placeholder="<?php _e('Select Interval',WOOEXIM_TEXTDOMAIN);?>" name="wooexim_export_interval">
											<?php foreach($get_schedules_list as $key=>$value){?>
											<option value="<?php echo $key;?>"><?php echo $value['display'];?></option>
											<?php }?>
										</select>
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Send E-mail',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input type="checkbox" class="wooexim_export_field_input_element wooexim_scheduled_send_email" name="wooexim_product_scheduled_send_email" value="1"/>
										<div class="wooexim_default_notice"><?php _e('Send E-mail with attachment.',WOOEXIM_TEXTDOMAIN);?></div>
									</div>
								</div>
							</div>
							<div class="wooexim_scheduled_export_email_details">
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email Recipient(s)',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input class="wooexim_export_field_input_element wooexim_scheduled_export_email_recipients" type="text" placeholder="<?php _e('Enter Email Recipient(s)',WOOEXIM_TEXTDOMAIN);?>" name="wooexim_scheduled_export_email_recipients">
										<div class="wooexim_default_notice">Exa. example@gmail.com, demo@yahoo.com</div>
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email Subject',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<input class="wooexim_export_field_input_element wooexim_scheduled_export_email_subject" type="text" placeholder="<?php _e('Enter Email Subject',WOOEXIM_TEXTDOMAIN);?>" name="wooexim_scheduled_export_email_subject">
									</div>
								</div>
								<div class="wooexim_scheduled_export_inner">
									<div class="wooexim_scheduled_export_data_label"><?php _e('Enter Email message',WOOEXIM_TEXTDOMAIN);?></div>
									<div class="wooexim_scheduled_export_data_element_wrapper">
										<textarea class="wooexim_scheduled_export_email_content wooexim_scheduled_export_text_area" name="wooexim_scheduled_export_email_content" placeholder="<?php _e('Enter Email message',WOOEXIM_TEXTDOMAIN);?>"></textarea>
									</div>
								</div>
							</div>
						</div>
 					</div>
						
				</div>
				<div class="wooexim_export_field_btn_container">
					<div class="wooexim_export_field_btn_wrapper">
						
						<button class="wooexim_form_submit_btn" type="button"><div class="wooexim_ajax_loader"></div><?php _e('Preview',WOOEXIM_TEXTDOMAIN);?></button>
						<button class="wooexim_form_submit_btn" type="button"><?php _e('Export',WOOEXIM_TEXTDOMAIN);?></button>
						<button class="wooexim_form_submit_btn" type="button"><?php _e('Save Scheduled',WOOEXIM_TEXTDOMAIN);?></button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="wooexim_export_preview_wrapper"></div>