<?php 
	if (!defined('ABSPATH'))
    die("Can't load this file directly");
	
	global $wooexim_product;
	$log_list = $wooexim_product -> wooexim_get_product_import_export_log();
 ?>
<div class="wooexim_product_export_wrapper">
	<div class="woo-banner">
		<div class="woo-logo">
			<img src="<?php echo WOOEXIM_IMAGES_URL . '/wooexim.jpg'; ?>" />
			<h3><?php _e('WOOEXIM - WooCommerce Export Import Plugin',WOOEXIM_TEXTDOMAIN);?></h3>
		</div>
		<div class="wooexim_product_export_belt_wrapper">
			<div class="wooexim_product_export_belt wooexim_product_title_belt wooexim_selected"><?php _e('Export Archive',WOOEXIM_TEXTDOMAIN);?><div class="wooexim_total_export_count"><?php echo count($log_list);?></div></div>
		</div>
	</div>
	<div class="wooexim_product_export_container">
		<div class="wooexim_success_msg"><?php _e('Successfully Deleted.',WOOEXIM_TEXTDOMAIN);?></div>
		<div class="wooexim_product_export_inner_container">
			<table class="widefat wooexim_product_import_log" cellspacing="0">
				<thead>
					<tr>
						<th><?php _e('File Type',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('File Name',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Export Data',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Date',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Action',WOOEXIM_TEXTDOMAIN);?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(!empty($log_list)){?>
					<?php foreach($log_list as $log_data){?>
						<tr>
							<td><?php if($log_data->export_log_file_type=='csv'){?><img src="<?php echo WOOEXIM_IMAGES_URL.'/csv_logo.png';?>" class="wooexim_log_logo"/><?php }?></td>
							<td class="wooexim_filename_list"><?php echo $log_data->export_log_file_name;?></td>
							<td><?php echo $log_data->export_log_data;?></td>
							<td><?php echo $log_data->create_date;?></td>
							<td>
								<div class="wooexim_log_download_action"  file_name="<?php echo $log_data->export_log_file_name;?>"><?php _e('Download',WOOEXIM_TEXTDOMAIN);?></div><div class="wooexim_log_delete_action" log_id="<?php echo $log_data->export_log_id;?>" file_name="<?php echo $log_data->export_log_file_name;?>"><?php _e('Delete',WOOEXIM_TEXTDOMAIN);?></div>
							</td>
						</tr>
					<?php }?>
					<?php }else{?>
						<tr>
							<td colspan="5" class="wooexim_product_log"><?php _e('No Records found.',WOOEXIM_TEXTDOMAIN);?></td>
						</tr>
					<?php
					}
							
					?>
				</tbody>
				<tfoot>
					<tr>
						<th><?php _e('File Type',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('File Name',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Export Data',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Date',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Action',WOOEXIM_TEXTDOMAIN);?></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<form method="post" class="wooexim_download_exported_file_frm">
		<input type="hidden" class="wooexim_download_exported_file" name="wooexim_download_exported_file" value="" />
	</form>
</div>