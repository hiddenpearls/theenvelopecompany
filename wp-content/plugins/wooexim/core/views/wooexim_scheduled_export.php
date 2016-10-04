<?php 
	if (!defined('ABSPATH'))
    die("Can't load this file directly");
	
	global $wooexim_scheduled;
	
	$order_scheduled_list = $wooexim_scheduled->get_order_scheduled_data();
	
	$product_scheduled_list = $wooexim_scheduled->get_product_scheduled_data();
	
	$user_scheduled_list = $wooexim_scheduled->get_user_scheduled_data();
	
	$product_cat_scheduled_list = $wooexim_scheduled->get_product_cat_scheduled_data();
	
	$coupon_scheduled_list = $wooexim_scheduled->get_coupon_scheduled_data();
	
	$get_schedules_list = wp_get_schedules();
	
	$total_records = 0;
	
	$total_order_records = 0;
	
	$total_products_records = 0;
	
	$total_products_cat_records = 0;
	
	$total_users_records = 0;
	
	$total_coupon_records = 0;
	
	if(!empty($order_scheduled_list))
	{
		$total_order_records = count($order_scheduled_list);
	}
	if(!empty($product_scheduled_list))
	{
		$total_products_records = count($product_scheduled_list);
	}
	if(!empty($product_cat_scheduled_list))
	{
		$total_products_cat_records = count($product_cat_scheduled_list);
	}
	if(!empty($user_scheduled_list))
	{
		$total_users_records = count($user_scheduled_list);
	}
	if(!empty($coupon_scheduled_list))
	{
		$total_coupon_records = count($coupon_scheduled_list);
	}
	
	$total_records = $total_order_records + $total_products_records + $total_users_records + $total_products_cat_records + $total_coupon_records;
	
	
?>
<div class="wooexim_product_export_wrapper">
	<div class="woo-banner">
		<div class="woo-logo">
			<img src="<?php echo WOOEXIM_IMAGES_URL . '/wooexim.jpg'; ?>" />
			<h3><?php _e('WOOEXIM - WooCommerce Export Import Plugin',WOOEXIM_TEXTDOMAIN);?></h3>
		</div>
		<div class="wooexim_product_export_belt_wrapper">
			<div class="wooexim_product_export_belt wooexim_selected"><?php _e('Scheduled Export',WOOEXIM_TEXTDOMAIN);?><div class="wooexim_total_export_count"><?php echo $total_records;?></div></div>
		</div>
	</div>
	<div class="wooexim_product_export_container">
		<div class="wooexim_success_msg"><?php _e('Successfully Deleted.',WOOEXIM_TEXTDOMAIN);?></div>
		<div class="wooexim_scheduled_export_title"><div class="wooexim_total_data_export_title"><?php _e('Product Scheduled Export',WOOEXIM_TEXTDOMAIN);?> (</div><div class="wooexim_total_product_export_count"><?php echo $total_products_records;?></div><div class="wooexim_total_data_export_title">)</div></div>
		<div class="wooexim_product_export_inner_container wooexim_scheduled_export_container">
			<table class="wooexim_product_scheduled_export wooexim_scheduled_export_list" cellspacing="0">
				<thead>
					<tr>
						<th><?php _e('Scheduled ID',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Recurrence Time',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Send E-mail',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Recipients',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Next event',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Actions',WOOEXIM_TEXTDOMAIN);?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(!empty($product_scheduled_list)){?>
						<?php foreach($product_scheduled_list as $key=>$value){?>
						<tr>
							<td><?php echo $key;?></td>
							<td><?php echo $get_schedules_list[$value['wooexim_export_interval']]['display'];?></td>
							<td><?php if(isset($value['wooexim_product_scheduled_send_email']) && $value['wooexim_product_scheduled_send_email']==1){_e('Yes',WOOEXIM_TEXTDOMAIN);}else{_e('No',WOOEXIM_TEXTDOMAIN);}?></td>
							<td><?php echo $value['wooexim_scheduled_export_email_recipients'];?></td>
							<td><?php echo date_i18n(get_option( 'date_format' ).' '.get_option( 'time_format' ),wp_next_scheduled( 'wooexim_cron_scheduled_product_export' ,array( $key ) ));?></td>
							<td><?php echo '<div class="wooexim_delete_product_cron" cron_id='.$key.'>DELETE</div>';?></td>
						</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="wooexim_scheduled_export_title"><div class="wooexim_total_data_export_title"><?php _e('Order Scheduled Export',WOOEXIM_TEXTDOMAIN);?> (</div><div class="wooexim_total_order_export_count"><?php echo $total_order_records;?></div><div class="wooexim_total_data_export_title">)</div></div>
		<div class="wooexim_product_export_inner_container wooexim_scheduled_export_container">
			<table class="wooexim_product_scheduled_export wooexim_scheduled_export_list" cellspacing="0">
				<thead>
					<tr>
						<th><?php _e('Scheduled ID',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Recurrence Time',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Send E-mail',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Recipients',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Next event',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Actions',WOOEXIM_TEXTDOMAIN);?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(!empty($order_scheduled_list)){?>
						<?php foreach($order_scheduled_list as $key=>$value){?>
						<tr>
							<td><?php echo $key;?></td>
							<td><?php echo $get_schedules_list[$value['wooexim_export_interval']]['display'];?></td>
							<td><?php if(isset($value['wooexim_order_scheduled_send_email']) && $value['wooexim_order_scheduled_send_email']==1){_e('Yes',WOOEXIM_TEXTDOMAIN);}else{_e('No',WOOEXIM_TEXTDOMAIN);}?></td>
							<td><?php echo $value['wooexim_scheduled_export_email_recipients'];?></td>
							<td><?php echo date_i18n(get_option( 'date_format' ).' '.get_option( 'time_format' ),wp_next_scheduled( 'wooexim_cron_scheduled_order_export' ,array( $key ) ));?></td>
							<td><?php echo '<div class="wooexim_delete_order_cron" cron_id='.$key.'>DELETE</div>';?></td>
						</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
		
		<div class="wooexim_scheduled_export_title"><div class="wooexim_total_data_export_title"><?php _e('User Scheduled Export',WOOEXIM_TEXTDOMAIN);?> (</div><div class="wooexim_total_user_export_count"><?php echo $total_users_records;?></div><div class="wooexim_total_data_export_title">)</div></div>
		<div class="wooexim_product_export_inner_container wooexim_scheduled_export_container">
			<table class="wooexim_user_scheduled_export wooexim_scheduled_export_list" cellspacing="0">
				<thead>
					<tr>
						<th><?php _e('Scheduled ID',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Recurrence Time',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Send E-mail',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Recipients',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Next event',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Actions',WOOEXIM_TEXTDOMAIN);?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(!empty($user_scheduled_list)){?>
						<?php foreach($user_scheduled_list as $key=>$value){?>
						<tr>
							<td><?php echo $key;?></td>
							<td><?php echo $get_schedules_list[$value['wooexim_export_interval']]['display'];?></td>
							<td><?php if(isset($value['wooexim_user_scheduled_send_email']) && $value['wooexim_user_scheduled_send_email']==1){_e('Yes',WOOEXIM_TEXTDOMAIN);}else{_e('No',WOOEXIM_TEXTDOMAIN);}?></td>
							<td><?php echo $value['wooexim_scheduled_export_email_recipients'];?></td>
							<td><?php echo date_i18n(get_option( 'date_format' ).' '.get_option( 'time_format' ),wp_next_scheduled( 'wooexim_cron_scheduled_product_export' ,array( $key ) ));?></td>
							<td><?php echo '<div class="wooexim_delete_user_cron" cron_id='.$key.'>DELETE</div>';?></td>
						</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
		
		<div class="wooexim_scheduled_export_title"><div class="wooexim_total_data_export_title"><?php _e('Product Category Scheduled Export',WOOEXIM_TEXTDOMAIN);?> (</div><div class="wooexim_total_product_cat_export_count"><?php echo $total_products_cat_records;?></div><div class="wooexim_total_data_export_title">)</div></div>
		<div class="wooexim_product_export_inner_container wooexim_scheduled_export_container">
			<table class="wooexim_product_cat_scheduled_export wooexim_scheduled_export_list" cellspacing="0">
				<thead>
					<tr>
						<th><?php _e('Scheduled ID',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Recurrence Time',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Send E-mail',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Recipients',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Next event',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Actions',WOOEXIM_TEXTDOMAIN);?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(!empty($product_cat_scheduled_list)){?>
						<?php foreach($product_cat_scheduled_list as $key=>$value){?>
						<tr>
							<td><?php echo $key;?></td>
							<td><?php echo $get_schedules_list[$value['wooexim_export_interval']]['display'];?></td>
							<td><?php if(isset($value['wooexim_product_scheduled_send_email']) && $value['wooexim_product_scheduled_send_email']==1){_e('Yes',WOOEXIM_TEXTDOMAIN);}else{_e('No',WOOEXIM_TEXTDOMAIN);}?></td>
							<td><?php echo $value['wooexim_scheduled_export_email_recipients'];?></td>
							<td><?php echo date_i18n(get_option( 'date_format' ).' '.get_option( 'time_format' ),wp_next_scheduled( 'wooexim_cron_scheduled_product_cat_export' ,array( $key ) ));?></td>
							<td><?php echo '<div class="wooexim_delete_product_cat_cron" cron_id='.$key.'>DELETE</div>';?></td>
						</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
		
		<div class="wooexim_scheduled_export_title"><div class="wooexim_total_data_export_title"><?php _e('Coupon Scheduled Export',WOOEXIM_TEXTDOMAIN);?> (</div><div class="wooexim_total_coupon_export_count"><?php echo $total_coupon_records;?></div><div class="wooexim_total_data_export_title">)</div></div>
		<div class="wooexim_product_export_inner_container wooexim_scheduled_export_container">
			<table class="wooexim_product_cat_scheduled_export wooexim_scheduled_export_list" cellspacing="0">
				<thead>
					<tr>
						<th><?php _e('Scheduled ID',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Recurrence Time',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Send E-mail',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Recipients',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Next event',WOOEXIM_TEXTDOMAIN);?></th>
						<th><?php _e('Actions',WOOEXIM_TEXTDOMAIN);?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(!empty($coupon_scheduled_list)){?>
						<?php foreach($coupon_scheduled_list as $key=>$value){?>
						<tr>
							<td><?php echo $key;?></td>
							<td><?php echo $get_schedules_list[$value['wooexim_export_interval']]['display'];?></td>
							<td><?php if(isset($value['wooexim_product_scheduled_send_email']) && $value['wooexim_product_scheduled_send_email']==1){_e('Yes',WOOEXIM_TEXTDOMAIN);}else{_e('No',WOOEXIM_TEXTDOMAIN);}?></td>
							<td><?php echo $value['wooexim_scheduled_export_email_recipients'];?></td>
							<td><?php echo date_i18n(get_option( 'date_format' ).' '.get_option( 'time_format' ),wp_next_scheduled( 'wooexim_cron_scheduled_coupon_export' ,array( $key ) ));?></td>
							<td><?php echo '<div class="wooexim_delete_coupon_cron" cron_id='.$key.'>DELETE</div>';?></td>
						</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>