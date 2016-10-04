<?php 
	if (!defined('ABSPATH'))
    die("Can't load this file directly");
	
	global $wooexim_product,$download_product_errors,$new_product_errors;
	
	$product_cat = $wooexim_product -> wooexim_get_product_category();
	
 ?>
<div class="wooexim_product_export_wrapper">
	<div class="woo-banner">
		<div class="woo-logo">
			<img src="<?php echo WOOEXIM_IMAGES_URL . '/wooexim.jpg'; ?>" />
			<h3><?php _e('WOOEXIM - WooCommerce Export Import Plugin',WOOEXIM_TEXTDOMAIN);?></h3>
		</div>
		<div class="wooexim_product_export_belt_wrapper">
			<div class="wooexim_product_export_belt wooexim_product_import_belt  wooexim_selected"><?php _e('Products',WOOEXIM_TEXTDOMAIN);?></div>
			<div class="wooexim_product_export_belt wooexim_order_import_belt"><?php _e('Orders',WOOEXIM_TEXTDOMAIN);?></div>
			<div class="wooexim_product_export_belt wooexim_user_import_belt"><?php _e('Users',WOOEXIM_TEXTDOMAIN);?></div>
			<div class="wooexim_product_export_belt wooexim_product_category_import_belt"><?php _e('Product Categories',WOOEXIM_TEXTDOMAIN);?></div>
			<div class="wooexim_product_export_belt wooexim_coupon_import_belt"><?php _e('Coupons',WOOEXIM_TEXTDOMAIN);?></div>
		</div>
	</div>
	<div class="wooexim_product_export_container">
		<div class="wooexim_product_export_inner_container">
			<div class="wooexim_success_msg" style="display: block;">Only product export and import is enabled in the free version. To have all the features get the premium version here <a style="color: white;" target="_blank" href="http://wooexim.com/">WOOEXIM Pro</a></div>
			<div class="wooexim_success_msg wooexim_import_success_msg"><?php _e('Product Imported successfully.',WOOEXIM_TEXTDOMAIN);?></div>
			<div class="wooexim_error_msg wooexim_import_error_msg"></div>
			<div class="wooexim_process_bar_wrapper">
				<span class="wooexim_process_bar"></span>
				<div class="wooexim_process_bar_process">0%</div>
			</div>
 			<div id="wooexim_targetLayer"></div>
			<form method="post" class="wooexim_product_import_frm wooexim_data_import_frm" enctype="multipart/form-data">
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Select File to Import',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="file" name="wooexim_import_file" class="wooexim_export_field_file_element wooexim_import_file"/>
						<div class="wooexim_default_notice"><?php _e('Note : Select only this plugin exported file.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Enter URL to Import',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="text" name="import_file_url" class="wooexim_export_field_input_element wooexim_import_file_url" placeholder="<?php _e('Enter URL',WOOEXIM_TEXTDOMAIN);?>"/>
						<input type="hidden" name="wooexim_import_file_url"/>
						<div class="wooexim_default_notice"><?php _e('Note : Leave blank if upload file (Premium Feature).',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Select Category for Product',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="product_category[]"  data-placeholder="<?php _e('Select Product Category',WOOEXIM_TEXTDOMAIN);?>" multiple="multiple">
							<?php foreach($product_cat as $cat){?>
								<option value="<?php echo $cat->name;?>"><?php echo '('.__('ID',WOOEXIM_TEXTDOMAIN).' : '.$cat->term_id.') '. $cat->name;?></option>
								<?php }?>
 						</select>
						<input type="hidden" name="wooexim_product_category[]"/>
						<div class="wooexim_default_notice"><?php _e('Note : Category for new product imported(Premium Feature).',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Product Create / Update / Skip',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<select class="export_field_select_element" name="wooexim_product_create_method"  data-placeholder="<?php _e('Select Product Create',WOOEXIM_TEXTDOMAIN);?>" >
								<option value="skip_product"><?php _e('Skip Product if Exist.',WOOEXIM_TEXTDOMAIN);?></option>
								<option value="create_product"><?php _e('Create New Product and ignore if Exist.',WOOEXIM_TEXTDOMAIN);?></option>
								<option value="update_product"><?php _e('Update Product if Exist.',WOOEXIM_TEXTDOMAIN);?></option>
								
 						</select>
						<input type="hidden" name="wooexim_product_create_method"/>
						<div class="wooexim_default_notice"><?php _e('Note : Imported product is skip, updated or created if already exist (Premium Feature).',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_btn_container">
					<div class="wooexim_export_field_btn_wrapper">
						<button class="wooexim_product_import_btn wooexim_form_submit_btn" type="button"><?php _e('Import',WOOEXIM_TEXTDOMAIN);?></button><div class="wooexim_loader_icon_wrapper" id="loader-icon" ><img class="wooexim_loader_icon" src="<?php echo WOOEXIM_IMAGES_URL;?>/wooexim_loader.gif" /></div>
					</div>
				</div>
			</form>
			<form method="post" class="wooexim_order_import_frm wooexim_data_import_frm" style="display:none;" enctype="multipart/form-data">
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Select File to Import',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="file" name="wooexim_import_file" class="wooexim_export_field_file_element wooexim_import_file"/>
						<div class="wooexim_default_notice"><?php _e('Note : Select only this plugin exported file.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Enter URL to Import',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="text" name="wooexim_import_file_url" class="wooexim_export_field_input_element wooexim_import_file_url" placeholder="<?php _e('Enter URL',WOOEXIM_TEXTDOMAIN);?>"/>
						<div class="wooexim_default_notice"><?php _e('Note : Leave blank if upload file.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Order Create / Update / Skip',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_order_create_option"  data-placeholder="<?php _e('Select order create option',WOOEXIM_TEXTDOMAIN);?>" >
								<option value="skip_order"><?php _e('Skip Order if Exist.',WOOEXIM_TEXTDOMAIN);?></option>
								<option value="create_order"><?php _e('Create New Order and ignore if Exist.',WOOEXIM_TEXTDOMAIN);?></option>
								<option value="update_order"><?php _e('Update Order if Exist.',WOOEXIM_TEXTDOMAIN);?></option>
								
 						</select>
						<div class="wooexim_default_notice"><?php _e('Note : Imported order is created, updated or skip if already exist.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_order_import_notice_wrapper">
					<div class="wooexim_default_import_notice"><?php _e('Note : Product SKU or ID must be same for import and export both side.',WOOEXIM_TEXTDOMAIN);?></div>
				</div>
				<div class="wooexim_export_field_btn_container">
					<div class="wooexim_export_field_btn_wrapper">
						<button class="wooexim_form_submit_btn" type="button"><?php _e('Import',WOOEXIM_TEXTDOMAIN);?></button><div class="wooexim_loader_icon_wrapper" id="loader-icon" ><img class="wooexim_loader_icon" src="<?php echo WOOEXIM_IMAGES_URL;?>/wooexim_loader.gif" /></div>
					</div>
				</div>
			</form>
			<form method="post" class="wooexim_user_import_frm wooexim_data_import_frm" style="display:none;" enctype="multipart/form-data">
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Select File to Import',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="file" name="wooexim_import_file" class="wooexim_export_field_file_element wooexim_import_file"/>
						<div class="wooexim_default_notice"><?php _e('Note : Select only this plugin exported file.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Enter URL to Import',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="text" name="wooexim_import_file_url" class="wooexim_export_field_input_element wooexim_import_file_url" placeholder="<?php _e('Enter URL',WOOEXIM_TEXTDOMAIN);?>"/>
						<div class="wooexim_default_notice"><?php _e('Note : Leave blank if upload file.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('User Create / Update / Skip',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_user_create_option"  data-placeholder="<?php _e('Select user create option',WOOEXIM_TEXTDOMAIN);?>" >
								<option value="skip_user"><?php _e('Skip User if Exist.',WOOEXIM_TEXTDOMAIN);?></option>
								<?php /*?><option value="create_user"><?php _e('Create New User and ignore if Exist.',WOOEXIM_TEXTDOMAIN);?></option><?php */?>
								<option value="update_user"><?php _e('Update User if Exist.',WOOEXIM_TEXTDOMAIN);?></option>
								
 						</select>
						<div class="wooexim_default_notice"><?php _e('Note : Imported user is created, updated or skip if already exist.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_btn_container">
					<div class="wooexim_export_field_btn_wrapper">
						<button class="wooexim_form_submit_btn" type="button"><?php _e('Import',WOOEXIM_TEXTDOMAIN);?></button><div class="wooexim_loader_icon_wrapper" id="loader-icon" ><img class="wooexim_loader_icon" src="<?php echo WOOEXIM_IMAGES_URL;?>/wooexim_loader.gif" /></div>
					</div>
				</div>
			</form>
			<form method="post" class="wooexim_product_cat_import_frm wooexim_data_import_frm" style="display:none;" enctype="multipart/form-data">
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Select File to Import',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="file" name="wooexim_import_file" class="wooexim_export_field_file_element wooexim_import_file"/>
						<div class="wooexim_default_notice"><?php _e('Note : Select only this plugin exported file.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Enter URL to Import',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="text" name="wooexim_import_file_url" class="wooexim_export_field_input_element wooexim_import_file_url" placeholder="<?php _e('Enter URL',WOOEXIM_TEXTDOMAIN);?>"/>
						<div class="wooexim_default_notice"><?php _e('Note : Leave blank if upload file.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Product Category Update / Skip',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_product_cat_create_method"  data-placeholder="<?php _e('Select Product Category Create',WOOEXIM_TEXTDOMAIN);?>" >
								<option value="skip_product_cat"><?php _e('Skip Product Category if Exist.',WOOEXIM_TEXTDOMAIN);?></option>
								<!--<option value="create_product_cat"><?php _e('Create New Product Category and ignore if Exist.',WOOEXIM_TEXTDOMAIN);?></option>-->
								<option value="update_product_cat"><?php _e('Update Product Category if Exist.',WOOEXIM_TEXTDOMAIN);?></option>
								
 						</select>
						<div class="wooexim_default_notice"><?php _e('Note : Imported product category is skip or updated if already exist.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_btn_container">
					<div class="wooexim_export_field_btn_wrapper">
						<button class="wooexim_form_submit_btn" type="button"><?php _e('Import',WOOEXIM_TEXTDOMAIN);?></button><div class="wooexim_loader_icon_wrapper" id="loader-icon" ><img class="wooexim_loader_icon" src="<?php echo WOOEXIM_IMAGES_URL;?>/wooexim_loader.gif" /></div>
					</div>
				</div>
			</form>
			<form method="post" class="wooexim_coupon_import_frm wooexim_data_import_frm" style="display:none;" enctype="multipart/form-data">
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Select File to Import',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="file" name="wooexim_import_file" class="wooexim_export_field_file_element wooexim_import_file"/>
						<div class="wooexim_default_notice"><?php _e('Note : Select only this plugin exported file.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Enter URL to Import',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<input type="text" name="wooexim_import_file_url" class="wooexim_export_field_input_element wooexim_import_file_url" placeholder="<?php _e('Enter URL',WOOEXIM_TEXTDOMAIN);?>"/>
						<div class="wooexim_default_notice"><?php _e('Note : Leave blank if upload file.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_container">
					<div class="wooexim_export_field_title"><?php _e('Coupon Update / Skip',WOOEXIM_TEXTDOMAIN);?></div>
					<div class="wooexim_export_field_wrapper">
						<select class="wooexim_export_field_select_element" name="wooexim_coupon_create_method"  data-placeholder="<?php _e('Select Coupon Create',WOOEXIM_TEXTDOMAIN);?>" >
								<option value="skip_coupon"><?php _e('Skip Coupon if Exist.',WOOEXIM_TEXTDOMAIN);?></option>
								<!--<option value="create_coupon"><?php _e('Create New Coupon and ignore if Exist.',WOOEXIM_TEXTDOMAIN);?></option>-->
								<option value="update_coupon"><?php _e('Update Coupon if Exist.',WOOEXIM_TEXTDOMAIN);?></option>
								
 						</select>
						<div class="wooexim_default_notice"><?php _e('Note : Imported Coupon is skip or updated if already exist.',WOOEXIM_TEXTDOMAIN);?></div>
					</div>
				</div>
				<div class="wooexim_export_field_btn_container">
					<div class="wooexim_export_field_btn_wrapper">
						<button class="wooexim_form_submit_btn" type="button"><?php _e('Import',WOOEXIM_TEXTDOMAIN);?></button><div class="wooexim_loader_icon_wrapper" id="loader-icon" ><img class="wooexim_loader_icon" src="<?php echo WOOEXIM_IMAGES_URL;?>/wooexim_loader.gif" /></div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="wooexim_import_preview_wrapper"></div>