<?php 
	if (!defined('ABSPATH'))
    die("Can't load this file directly");
	
	global $wooexim_product, $wooexim_order, $wooexim_user ,$wooexim_import_export, $wooexim_product_cat, $wooexim_coupon;
	
	$wooexim_hostname = $_SERVER["HTTP_HOST"]; 
	
	$plugin_data = $wooexim_import_export -> get_wooexim_sort_order();
	
	$updated_product_field = $wooexim_product -> get_updated_product_fields();
	
	$updated_order_field = $wooexim_order -> get_updated_order_fields();
	
	$updated_user_field = $wooexim_user -> get_updated_user_fields();
	
	$updated_product_cat_field = $wooexim_product_cat -> get_updated_product_cat_fields();
	
	$updated_coupon_field = $wooexim_coupon -> get_updated_coupon_fields();
		
 ?>
<div class="wooexim_product_export_wrapper wooexim_product_export_settings_wrapper">
	<div class="woo-banner">
		<div class="woo-logo">
			<img src="<?php echo WOOEXIM_IMAGES_URL . '/wooexim.jpg'; ?>" />
			<h3><?php _e('WOOEXIM - WooCommerce Export Import Plugin',WOOEXIM_TEXTDOMAIN);?></h3>
		</div>
		<div class="wooexim_product_export_belt_wrapper">
			<div class="wooexim_product_export_belt wooexim_product_title_belt wooexim_selected"><?php _e('Settings',WOOEXIM_TEXTDOMAIN);?></div>
		</div>
	</div>
	<div class="wooexim_product_export_container">
		<div class="wooexim_product_export_inner_container">
			<div class="wooexim_licence_settings_frm_success wooexim_success_msg"><?php _e('License Activated Successfully.',WOOEXIM_TEXTDOMAIN)?></div>
			<div class="wooexim_success_msg wooexim_save_fields"><?php _e('Changes Saved Successfully.',WOOEXIM_TEXTDOMAIN)?></div>
			<div class="wooexim_licence_settings_frm_error wooexim_error_msg"><?php _e('Invalid Request.',WOOEXIM_TEXTDOMAIN)?></div>
			<?php 
				if(isset($plugin_data['plugin_status']) && $plugin_data['plugin_status']=='active'){
					$active_style = 'display:none';
					$deactive_style = "";
				 }else{
					$active_style = '';
					$deactive_style = 'display:none';
				 }
			?>
			<form method="post" class="wooexim_settings_purchase_frm" style=" <?php echo $active_style;?>">
				<div class="wooexim_export_settings_field_container">
					<div class="wooexim_export_settings_title"><div class="wooexim_toggle_open"></div><div class="wooexim_toggle_close"></div><?php _e('Product License',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_setting_field_outer_wrapper wooexim_product_license_activate_outer_wrapper">
						<div class="wooexim_license_notice"><?php _e('A valid license key entitles you to support and enable automatic upgrades.',WOOEXIM_TEXTDOMAIN);?></div>
						<div class="wooexim_export_settings_field_wrapper">
							<div class="wooexim_export_settings_container">
								<div class="wooexim_export_settings_purchase_title"><?php _e('Customer Name',WOOEXIM_TEXTDOMAIN);?> *</div>
								<input type="text" class="wooexim_export_settings_purchase_element wooexim_product_customer_name" name="wooexim_customer_name" placeholder="<?php _e('Customer Name',WOOEXIM_TEXTDOMAIN);?>"/>
							</div>
						</div>
						<div class="wooexim_export_settings_field_wrapper">
							<div class="wooexim_export_settings_container">
								<div class="wooexim_export_settings_purchase_title"><?php _e('Customer Email',WOOEXIM_TEXTDOMAIN);?> *</div>
								<input type="text" class="wooexim_export_settings_purchase_element wooexim_product_customer_email" name="wooexim_customer_email" placeholder="<?php _e('Customer Email',WOOEXIM_TEXTDOMAIN);?>"/>
							</div>
						</div>
						<div class="wooexim_export_settings_field_wrapper">
							<div class="wooexim_export_settings_container">
								<div class="wooexim_export_settings_purchase_title"><?php _e('Purchase Code',WOOEXIM_TEXTDOMAIN);?> *</div>
								<input type="text" class="wooexim_export_settings_purchase_element wooexim_product_purchase_code" name="wooexim_customer_purchase_code" placeholder="<?php _e('Purchase Code',WOOEXIM_TEXTDOMAIN);?>"/>
							</div>
						</div>
						<div class="wooexim_export_settings_field_wrapper">
							<div class="wooexim_export_settings_container">
								<div class="wooexim_export_settings_purchase_title"><?php _e('Domain Name',WOOEXIM_TEXTDOMAIN);?> *</div>
								<div class="wooexim_export_settings_domain_name"><?php echo $wooexim_hostname;?></div>
								<input type="hidden" name="wooexim_product_domain_name" value="<?php echo $wooexim_hostname;?>">
							</div>
						</div>
						<div class="wooexim_export_field_btn_container">
							<div class="wooexim_export_field_btn_wrapper">
								<button class="wooexim_settings_btn wooexim_form_submit_btn wooexim_activate_license" type="button"><?php _e('Activate',WOOEXIM_TEXTDOMAIN);?></button>
								<div class="wooexim_activation_loader"></div>
							</div>
						</div>
					</div>
				</div>
			</form>
			<form class="wooexim_deactivate_licence_settings_frm" method="post" style=" <?php echo $deactive_style;?>">
				<div class="wooexim_export_settings_field_container">
					<div class="wooexim_export_settings_title"><div class="wooexim_toggle_open"></div><div class="wooexim_toggle_close"></div><?php _e('Product License',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_setting_field_outer_wrapper wooexim_product_license_deactivate_outer_wrapper">
						<div class="wooexim_export_settings_field_wrapper">
							<div class="wooexim_export_settings_container">
								<div class="wooexim_export_settings_purchase_title"><?php _e('License Status',WOOEXIM_TEXTDOMAIN);?></div>
								<div class="wooexim_product_licence_element">
									<div class="wooexim_license_status"><?php _e('Active',WOOEXIM_TEXTDOMAIN);?></div>
								</div>
							</div>
						</div>
						<div class="wooexim_export_field_btn_container">
							<div class="wooexim_export_field_btn_wrapper">
								<button class="wooexim_settings_btn wooexim_form_submit_btn wooexim_deactivate_license" type="button"><?php _e('Deactivate',WOOEXIM_TEXTDOMAIN);?></button>
								<div class="wooexim_deactivation_loader"></div>
							</div>
						</div>
					</div>
				</div>
			</form>
			<form class="wooexim_product_field_setting" method="post">
				<div class="wooexim_export_settings_field_container">
					<div class="wooexim_export_settings_title"><div class="wooexim_toggle_open"></div><div class="wooexim_toggle_close"></div><?php _e('Product Fields',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_setting_field_outer_wrapper wooexim_product_field_element_outer">
						<div class="wooexim_export_settings_field_wrapper">
							<div class="wooexim_export_settings_container">
								<?php 
									foreach($updated_product_field as $new_product_field)
									{
										foreach($new_product_field as $key => $value){?>
										<div class="wooexim_export_settings_field_wrapper">
											<div class="wooexim_export_settings_container">
												<div class="wooexim_export_settings_field_title"><?php echo $value['field_title'];?></div>
												<input type="text" class="wooexim_export_settings_field_element" name="<?php echo 'wooexim_'.$value['field_key'].'_field';?>" placeholder="<?php echo $value['field_title'];?>" value="<?php echo $value['field_value'];?>"/>
											</div>
										</div>
										<?php }
									}
								?>
							</div>
						</div>
						<div class="wooexim_export_field_btn_container">
							<div class="wooexim_export_field_btn_wrapper">
								<button class="wooexim_settings_btn wooexim_form_submit_btn wooexim_product_field_save" type="button"><?php _e('Save',WOOEXIM_TEXTDOMAIN);?></button>
								<div class="wooexim_product_field"></div>
							</div>
						</div>
					</div>
				</div>
			</form>
			<form class="wooexim_order_field_setting" method="post">
				<div class="wooexim_export_settings_field_container">
					<div class="wooexim_export_settings_title"><div class="wooexim_toggle_open"></div><div class="wooexim_toggle_close"></div><?php _e('Order Fields',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_setting_field_outer_wrapper wooexim_order_field_element_outer">
						<div class="wooexim_export_settings_field_wrapper">
							<div class="wooexim_export_settings_container">
								<?php
								foreach($updated_order_field as $new_order_field){
									 foreach($new_order_field  as $key => $value){?>
										<div class="wooexim_export_settings_field_wrapper">
											<div class="wooexim_export_settings_container">
												<div class="wooexim_export_settings_field_title"><?php echo $value['field_title'];?></div>
												<input type="text" class="wooexim_export_settings_field_element" name="<?php echo 'wooexim_'.$value['field_key'].'_field';?>" placeholder="<?php echo $value['field_title'];?>" value="<?php echo $value['field_value'];?>"/>
											</div>
										</div>
									<?php }
								}
								?>
							</div>
						</div>
						<div class="wooexim_export_field_btn_container">
							<div class="wooexim_export_field_btn_wrapper">
								<button class="wooexim_settings_btn wooexim_form_submit_btn wooexim_order_field_save" type="button"><?php _e('Save',WOOEXIM_TEXTDOMAIN);?></button>
								<div class="wooexim_order_field"></div>
							</div>
						</div>
					</div>
				</div>
			</form>
			<form class="wooexim_user_field_setting" method="post">
				<div class="wooexim_export_settings_field_container">
					<div class="wooexim_export_settings_title"><div class="wooexim_toggle_open"></div><div class="wooexim_toggle_close"></div><?php _e('User Fields',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_setting_field_outer_wrapper wooexim_order_field_element_outer">
						<div class="wooexim_export_settings_field_wrapper">
							<div class="wooexim_export_settings_container">
								<?php
								foreach($updated_user_field as $new_user_field){
									 foreach($new_user_field  as $key => $value){?>
										<div class="wooexim_export_settings_field_wrapper">
											<div class="wooexim_export_settings_container">
												<div class="wooexim_export_settings_field_title"><?php echo $value['field_title'];?></div>
												<input type="text" class="wooexim_export_settings_field_element" name="<?php echo 'wooexim_'.$value['field_key'].'_field';?>" placeholder="<?php echo $value['field_title'];?>" value="<?php echo $value['field_value'];?>"/>
											</div>
										</div>
									<?php }
								}
								?>
							</div>
						</div>
						<div class="wooexim_export_field_btn_container">
							<div class="wooexim_export_field_btn_wrapper">
								<button class="wooexim_settings_btn wooexim_form_submit_btn wooexim_user_field_save" type="button"><?php _e('Save',WOOEXIM_TEXTDOMAIN);?></button>
								<div class="wooexim_user_field"></div>
							</div>
						</div>
					</div>
				</div>
			</form>
			<form class="wooexim_product_cat_field_setting" method="post">
				<div class="wooexim_export_settings_field_container">
					<div class="wooexim_export_settings_title"><div class="wooexim_toggle_open"></div><div class="wooexim_toggle_close"></div><?php _e('Product Category Fields',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_setting_field_outer_wrapper wooexim_product_field_element_outer">
						<div class="wooexim_export_settings_field_wrapper">
							<div class="wooexim_export_settings_container">
								<?php 
									foreach($updated_product_cat_field as $new_product_cat_field)
									{
										foreach($new_product_cat_field as $key => $value){?>
										<div class="wooexim_export_settings_field_wrapper">
											<div class="wooexim_export_settings_container">
												<div class="wooexim_export_settings_field_title"><?php echo $value['field_title'];?></div>
												<input type="text" class="wooexim_export_settings_field_element" name="<?php echo 'wooexim_'.$value['field_key'].'_field';?>" placeholder="<?php echo $value['field_title'];?>" value="<?php echo $value['field_value'];?>"/>
											</div>
										</div>
										<?php }
									}
								?>
							</div>
						</div>
						<div class="wooexim_export_field_btn_container">
							<div class="wooexim_export_field_btn_wrapper">
								<button class="wooexim_settings_btn wooexim_form_submit_btn wooexim_product_cat_field_save" type="button"><?php _e('Save',WOOEXIM_TEXTDOMAIN);?></button>
								<div class="wooexim_product_field"></div>
							</div>
						</div>
					</div>
				</div>
			</form>
			<form class="wooexim_coupon_field_setting" method="post">
				<div class="wooexim_export_settings_field_container">
					<div class="wooexim_export_settings_title"><div class="wooexim_toggle_open"></div><div class="wooexim_toggle_close"></div><?php _e('Coupon Fields',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_setting_field_outer_wrapper wooexim_product_field_element_outer">
						<div class="wooexim_export_settings_field_wrapper">
							<div class="wooexim_export_settings_container">
								<?php 
									foreach($updated_coupon_field as $new_coupon_field)
									{
										foreach($new_coupon_field as $key => $value){?>
										<div class="wooexim_export_settings_field_wrapper">
											<div class="wooexim_export_settings_container">
												<div class="wooexim_export_settings_field_title"><?php echo $value['field_title'];?></div>
												<input type="text" class="wooexim_export_settings_field_element" name="<?php echo 'wooexim_'.$value['field_key'].'_field';?>" placeholder="<?php echo $value['field_title'];?>" value="<?php echo $value['field_value'];?>"/>
											</div>
										</div>
										<?php }
									}
								?>
							</div>
						</div>
						<div class="wooexim_export_field_btn_container">
							<div class="wooexim_export_field_btn_wrapper">
								<button class="wooexim_settings_btn wooexim_form_submit_btn wooexim_coupon_field_save" type="button"><?php _e('Save',WOOEXIM_TEXTDOMAIN);?></button>
								<div class="wooexim_product_field"></div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>