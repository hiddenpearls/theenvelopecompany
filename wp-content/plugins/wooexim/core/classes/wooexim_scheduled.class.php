<?php

if (!defined('ABSPATH'))
    die("Can't load this file directly");
	
class wooexim_scheduled{

	function wooexim_scheduled()
	{
				
 		add_action( 'wooexim_cron_scheduled_product_export',array(&$this,'wooexim_cron_scheduled_product_export_data') );
		
		add_action( 'wooexim_cron_scheduled_product_cat_export',array(&$this,'wooexim_cron_scheduled_product_cat_export_data') );
		
		add_action( 'wooexim_cron_scheduled_order_export',array(&$this,'wooexim_cron_scheduled_order_export_data') );
		
		add_action( 'wooexim_cron_scheduled_user_export',array(&$this,'wooexim_cron_scheduled_user_export_data') );
		
		add_action( 'wooexim_cron_scheduled_coupon_export',array(&$this,'wooexim_cron_scheduled_coupon_export_data') );
		
 		add_filter( 'cron_schedules', array( &$this,'add_cron_schedules_option') );	
		
		add_action( 'wp_ajax_wooexim_delete_product_scheduled_cron', array( &$this, 'wooexim_delete_product_scheduled_cron' ));
		
		add_action( 'wp_ajax_wooexim_delete_product_cat_scheduled_cron', array( &$this, 'wooexim_delete_product_cat_scheduled_cron' ));
		
  		add_action( 'wp_ajax_wooexim_delete_order_scheduled_cron', array( &$this, 'wooexim_delete_order_scheduled_cron' ));
		
		add_action( 'wp_ajax_wooexim_delete_user_scheduled_cron', array( &$this, 'wooexim_delete_user_scheduled_cron' ));
		
		add_action( 'wp_ajax_wooexim_delete_coupon_scheduled_cron', array( &$this, 'wooexim_delete_coupon_scheduled_cron' ));
   	} 
	
	function add_cron_schedules_option( $schedules )
	{
	
		$schedules['2_minutes'] = array(
	 		'interval' => 120,
	 		'display' => __('2 minutes',WOOEXIM_TEXTDOMAIN),
	 	);
		$schedules['30_minutes'] = array(
	 		'interval' => 1800,
	 		'display' => __('30 minutes',WOOEXIM_TEXTDOMAIN),
	 	);
		$schedules['weekly'] = array(
	 		'interval' => 604800,
	 		'display' => __('Once Weekly',WOOEXIM_TEXTDOMAIN)
	 	);
	 	return $schedules;
	 	
 	} 
	function get_product_scheduled_data()
	{
		$product_scheduled = @maybe_unserialize(get_option('wooexim_product_scheduled_data'));
		
		return $product_scheduled;
	}
	function wooexim_cron_scheduled_product_export_data($wooexim_cron_data)
	{
		global $wooexim_product, $wooexim_scheduled, $wpdb;
		
		$scheduled_data = $wooexim_scheduled -> get_product_scheduled_data();
		
		$wooexim_data = $scheduled_data[$wooexim_cron_data];
		
 		$filename = 'product_'.date('Y_m_d_H_i_s').'.csv';
			
		$fh = @fopen(WOOEXIM_UPLOAD_DIR.'/'.$filename,'w+');
		
		$wooexim_data = @maybe_unserialize($wooexim_data);
		
		$product_export_data = $wooexim_product -> get_product_export_data($wooexim_data);
		
		foreach($product_export_data as $new_data)
		{
			@fputcsv($fh , $new_data);
		}
		
		@fclose($fh);
		
		$new_values = array();
			
		$new_values['export_log_file_type'] = 'csv';			
		$new_values['export_log_file_name'] = $filename;
		$new_values['export_log_data'] 		= 'Product';
		$new_values['create_date'] 		    = current_time('mysql');
		
		$res = $wpdb->insert($wpdb->prefix."wooexim_export_log", $new_values);
			
		
		if(isset($wooexim_data['wooexim_product_scheduled_send_email']) && $wooexim_data['wooexim_product_scheduled_send_email']==1 && isset($wooexim_data['wooexim_scheduled_export_email_recipients']) && $wooexim_data['wooexim_scheduled_export_email_recipients']!="")
		{
				
			$attachments = array(WOOEXIM_UPLOAD_DIR.'/'.$filename);
			
			$recipient = explode(',',$wooexim_data['wooexim_scheduled_export_email_recipients']);
			
			$subject = $wooexim_data['wooexim_scheduled_export_email_subject'];
			
			$message = $wooexim_data['wooexim_scheduled_export_email_content'];
			
			$admin_email = get_option('admin_email');
			
			$headers  = array();
			
			$headers[] = 'From: "'. get_option('blogname') .'" <'. $admin_email .'>';
			
			$headers[] = 'Reply-To: '. $admin_email;
			
			$headers[] = 'Content-Type:text/html; charset="'. get_option('blog_charset') . '"';
			
			$wooexim_scheduled -> wooexim_send_mail($recipient,$subject,$message,$header,$attachments);
		}		
		
	}
	
	
	
	function wooexim_delete_product_scheduled_cron()
	{
		global $wooexim_scheduled;
		
		$cron_id = isset($_POST['cron_id'])?$_POST['cron_id']:"";
		
		if($cron_id!="")
		{
			$scheduled_data = $wooexim_scheduled -> get_product_scheduled_data();
			
			unset($scheduled_data[$cron_id]);
			
			$scheduled_new_data = @maybe_serialize($scheduled_data);
				
			update_option('wooexim_product_scheduled_data',$scheduled_new_data);
			
			wp_clear_scheduled_hook('wooexim_cron_scheduled_product_export',array($cron_id));
		}
		
		$return_value = array();
		
		$return_value['message']	= 'success';
		
		echo json_encode($return_value);
		
		die();
	}
	function get_order_scheduled_data()
	{
		$order_scheduled = @maybe_unserialize(get_option('wooexim_order_scheduled_data'));
		
		return $order_scheduled;
	}
	function wooexim_cron_scheduled_order_export_data($wooexim_cron_data)
	{
		global $wooexim_order, $wooexim_scheduled, $wpdb;
		
		$scheduled_data = $wooexim_scheduled -> get_order_scheduled_data();
		
		$wooexim_data = $scheduled_data[$wooexim_cron_data];
		
		$filename = 'order_'.date('Y_m_d_H_i_s').'.csv';
			
  		$fh = @fopen(WOOEXIM_UPLOAD_DIR.'/'.$filename,'w+');
				
		$wooexim_data = @maybe_unserialize($wooexim_data);
		
		$order_export_data = $wooexim_order -> get_order_csv_data($wooexim_data);
		
		foreach($order_export_data as $new_data)
		{
			@fputcsv($fh , $new_data);
		}
		
		@fclose($fh);
		
		$new_values = array();
			
		$new_values['export_log_file_type'] = 'csv';			
		$new_values['export_log_file_name'] = $filename;
		$new_values['export_log_data'] 		= 'Order';
		$new_values['create_date'] 		    = current_time('mysql');
		
		$res = $wpdb->insert($wpdb->prefix."wooexim_export_log", $new_values);
		
		if(isset($wooexim_data['wooexim_order_scheduled_send_email']) && $wooexim_data['wooexim_order_scheduled_send_email']==1 && isset($wooexim_data['wooexim_scheduled_export_email_recipients']) && $wooexim_data['wooexim_scheduled_export_email_recipients']!="")
		{
				
			$attachments = array(WOOEXIM_UPLOAD_DIR.'/'.$filename);
			
			$recipient = explode(',',$wooexim_data['wooexim_scheduled_export_email_recipients']);
			
			$subject = $wooexim_data['wooexim_scheduled_export_email_subject'];
			
			$message = $wooexim_data['wooexim_scheduled_export_email_content'];
			
			$admin_email = get_option('admin_email');
			
			$headers  = array();
			
			$headers[] = 'From: "'. get_option('blogname') .'" <'. $admin_email .'>';
			
			$headers[] = 'Reply-To: '. $admin_email;
			
			$headers[] = 'Content-Type:text/html; charset="'. get_option('blog_charset') . '"';
			
			$wooexim_scheduled -> wooexim_send_mail($recipient,$subject,$message,$header,$attachments);
		}		
		
	}
 	
	function wooexim_delete_order_scheduled_cron()
	{
		global $wooexim_scheduled;
		
		$cron_id = isset($_POST['cron_id'])?$_POST['cron_id']:"";
		
		if($cron_id!="")
		{
			$scheduled_data = $wooexim_scheduled -> get_order_scheduled_data();
			
			unset($scheduled_data[$cron_id]);
			
			$scheduled_new_data = @maybe_serialize($scheduled_data);
				
			update_option('wooexim_order_scheduled_data',$scheduled_new_data);
			
			wp_clear_scheduled_hook('wooexim_cron_scheduled_order_export',array($cron_id));
		}
		
		$return_value = array();
		
		$return_value['message']	= 'success';
		
		echo json_encode($return_value);
		
		die();
	}
	function get_user_scheduled_data()
	{
		$user_scheduled = @maybe_unserialize(get_option('wooexim_user_scheduled_data'));
		
		return $user_scheduled;
	}
	function wooexim_cron_scheduled_user_export_data($wooexim_cron_data)
	{
		global $wooexim_user, $wooexim_scheduled, $wpdb;
		
		$scheduled_data = $wooexim_scheduled -> get_user_scheduled_data();
		
		$wooexim_data = $scheduled_data[$wooexim_cron_data];
		
		$filename = 'user_'.date('Y_m_d_H_i_s').'.csv';
			
  		$fh = @fopen(WOOEXIM_UPLOAD_DIR.'/'.$filename,'w+');
				
		$wooexim_data = @maybe_unserialize($wooexim_data);
		
		$user_export_data = $wooexim_user -> get_user_csv_data($wooexim_data);
		
		foreach($user_export_data as $new_data)
		{
			@fputcsv($fh , $new_data);
		}
		
		@fclose($fh);
		
		$new_values = array();
			
		$new_values['export_log_file_type'] = 'csv';			
		$new_values['export_log_file_name'] = $filename;
		$new_values['export_log_data'] 		= 'User';
		$new_values['create_date'] 		    = current_time('mysql');
		
		$res = $wpdb->insert($wpdb->prefix."wooexim_export_log", $new_values);
		
		if(isset($wooexim_data['wooexim_user_scheduled_send_email']) && $wooexim_data['wooexim_user_scheduled_send_email']==1 && isset($wooexim_data['wooexim_scheduled_export_email_recipients']) && $wooexim_data['wooexim_scheduled_export_email_recipients']!="")
		{
				
			$attachments = array(WOOEXIM_UPLOAD_DIR.'/'.$filename);
			
			$recipient = explode(',',$wooexim_data['wooexim_scheduled_export_email_recipients']);
			
			$subject = $wooexim_data['wooexim_scheduled_export_email_subject'];
			
			$message = $wooexim_data['wooexim_scheduled_export_email_content'];
			
			$admin_email = get_option('admin_email');
			
			$headers  = array();
			
			$headers[] = 'From: "'. get_option('blogname') .'" <'. $admin_email .'>';
			
			$headers[] = 'Reply-To: '. $admin_email;
			
			$headers[] = 'Content-Type:text/html; charset="'. get_option('blog_charset') . '"';
			
			$wooexim_scheduled -> wooexim_send_mail($recipient,$subject,$message,$header,$attachments);
		}		
		
	}
 	
	function wooexim_delete_user_scheduled_cron()
	{
		global $wooexim_scheduled;
		
		$cron_id = isset($_POST['cron_id'])?$_POST['cron_id']:"";
		
		if($cron_id!="")
		{
			$scheduled_data = $wooexim_scheduled -> get_user_scheduled_data();
			
			unset($scheduled_data[$cron_id]);
			
			$scheduled_new_data = @maybe_serialize($scheduled_data);
				
			update_option('wooexim_user_scheduled_data',$scheduled_new_data);
			
			wp_clear_scheduled_hook('wooexim_cron_scheduled_user_export',array($cron_id));
		}
		
		$return_value = array();
		
		$return_value['message']	= 'success';
		
		echo json_encode($return_value);
		
		die();
	}
	
	function wooexim_send_mail($recipient,$subject,$message,$header,$attachments)
	{
		if (!wp_mail($recipient, $subject, $message, $header, $attachments)){
			
			$semi_rand = md5(time()); 
			
			$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
			
			$headers = 'From: '. get_option('blogname') .' <'. $admin_email  .'>'. '\n';
			
			$date = date("Y-m-d H:i:s"); 
			
			$headers .= "\n". "Date:$date " ."\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
			
			$message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
			
			$message .= "--{$mime_boundary}\n"; 
			
			if( count($attachments) > 0 ){

				foreach($attachments as $filename){

						$attachmnt = @chunk_split(base64_encode(file_get_contents($filename)));
						
						$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"".basename($filename)."\"\n" . "Content-Disposition: attachment;\n" . " filename=\"".basename($filename)."\"\n" . "Content-Transfer-Encoding: base64\n\n" . $attachmnt . "\n\n"; 
						
						$message .= "--{$mime_boundary}\n"; 
				}
			}	
			 
			@mail($recipient, $subject, $message, $headers);
			
        }
	}	
	
	function get_product_cat_scheduled_data()
	{
		$product_cat_scheduled = @maybe_unserialize(get_option('wooexim_product_cat_scheduled_data'));
		
		return $product_cat_scheduled;
	}
	function wooexim_cron_scheduled_product_cat_export_data($wooexim_cron_data)
	{
		global $wooexim_product, $wooexim_scheduled, $wpdb, $wooexim_product_cat;
		
		$scheduled_data = $wooexim_scheduled -> get_product_cat_scheduled_data();
		
		$wooexim_data = $scheduled_data[$wooexim_cron_data];
		
 		$filename = 'product_category_'.date('Y_m_d_H_i_s').'.csv';
			
		$fh = @fopen(WOOEXIM_UPLOAD_DIR.'/'.$filename,'w+');
		
		$wooexim_data = @maybe_unserialize($wooexim_data);
		
		$product_cat_export_data = $wooexim_product_cat -> get_product_cat_export_data($wooexim_data);
		
		foreach($product_cat_export_data as $new_data)
		{
			@fputcsv($fh , $new_data);
		}
		
		@fclose($fh);
		
		$new_values = array();
			
		$new_values['export_log_file_type'] = 'csv';			
		$new_values['export_log_file_name'] = $filename;
		$new_values['export_log_data'] 		= 'Product Category';
		$new_values['create_date'] 		    = current_time('mysql');
		
		$res = $wpdb->insert($wpdb->prefix."wooexim_export_log", $new_values);
			
		
		if(isset($wooexim_data['wooexim_product_scheduled_send_email']) && $wooexim_data['wooexim_product_scheduled_send_email']==1 && isset($wooexim_data['wooexim_scheduled_export_email_recipients']) && $wooexim_data['wooexim_scheduled_export_email_recipients']!="")
		{
				
			$attachments = array(WOOEXIM_UPLOAD_DIR.'/'.$filename);
			
			$recipient = explode(',',$wooexim_data['wooexim_scheduled_export_email_recipients']);
			
			$subject = $wooexim_data['wooexim_scheduled_export_email_subject'];
			
			$message = $wooexim_data['wooexim_scheduled_export_email_content'];
			
			$admin_email = get_option('admin_email');
			
			$headers  = array();
			
			$headers[] = 'From: "'. get_option('blogname') .'" <'. $admin_email .'>';
			
			$headers[] = 'Reply-To: '. $admin_email;
			
			$headers[] = 'Content-Type:text/html; charset="'. get_option('blog_charset') . '"';
			
			$wooexim_scheduled -> wooexim_send_mail($recipient,$subject,$message,$header,$attachments);
		}		
		
	}
	
	
	
	function wooexim_delete_product_cat_scheduled_cron()
	{
		global $wooexim_scheduled;
		
		$cron_id = isset($_POST['cron_id'])?$_POST['cron_id']:"";
		
		if($cron_id!="")
		{
			$scheduled_data = $wooexim_scheduled -> get_product_cat_scheduled_data();
			
			unset($scheduled_data[$cron_id]);
			
			$scheduled_new_data = @maybe_serialize($scheduled_data);
				
			update_option('wooexim_product_cat_scheduled_data',$scheduled_new_data);
			
			wp_clear_scheduled_hook('wooexim_cron_scheduled_product_cat_export',array($cron_id));
		}
		
		$return_value = array();
		
		$return_value['message']	= 'success';
		
		echo json_encode($return_value);
		
		die();
	}
	function get_coupon_scheduled_data()
	{
		$coupon_scheduled = @maybe_unserialize(get_option('wooexim_coupon_scheduled_data'));
		
		return $coupon_scheduled;
	}
	function wooexim_cron_scheduled_coupon_export_data($wooexim_cron_data)
	{
		global $wooexim_product, $wooexim_scheduled, $wpdb, $wooexim_coupon;
		
		$scheduled_data = $wooexim_scheduled -> get_coupon_scheduled_data();
		
		$wooexim_data = $scheduled_data[$wooexim_cron_data];
		
 		$filename = 'coupon_'.date('Y_m_d_H_i_s').'.csv';
			
		$fh = @fopen(WOOEXIM_UPLOAD_DIR.'/'.$filename,'w+');
		
		$wooexim_data = @maybe_unserialize($wooexim_data);
		
		$coupon_export_data = $wooexim_coupon -> get_coupon_export_data($wooexim_data);
		
		foreach($coupon_export_data as $new_data)
		{
			@fputcsv($fh , $new_data);
		}
		
		@fclose($fh);
		
		$new_values = array();
			
		$new_values['export_log_file_type'] = 'csv';			
		$new_values['export_log_file_name'] = $filename;
		$new_values['export_log_data'] 		= 'Coupon';
		$new_values['create_date'] 		    = current_time('mysql');
		
		$res = $wpdb->insert($wpdb->prefix."wooexim_export_log", $new_values);
			
		
		if(isset($wooexim_data['wooexim_product_scheduled_send_email']) && $wooexim_data['wooexim_product_scheduled_send_email']==1 && isset($wooexim_data['wooexim_scheduled_export_email_recipients']) && $wooexim_data['wooexim_scheduled_export_email_recipients']!="")
		{
				
			$attachments = array(WOOEXIM_UPLOAD_DIR.'/'.$filename);
			
			$recipient = explode(',',$wooexim_data['wooexim_scheduled_export_email_recipients']);
			
			$subject = $wooexim_data['wooexim_scheduled_export_email_subject'];
			
			$message = $wooexim_data['wooexim_scheduled_export_email_content'];
			
			$admin_email = get_option('admin_email');
			
			$headers  = array();
			
			$headers[] = 'From: "'. get_option('blogname') .'" <'. $admin_email .'>';
			
			$headers[] = 'Reply-To: '. $admin_email;
			
			$headers[] = 'Content-Type:text/html; charset="'. get_option('blog_charset') . '"';
			
			$wooexim_scheduled -> wooexim_send_mail($recipient,$subject,$message,$header,$attachments);
		}		
		
	}
	function wooexim_delete_coupon_scheduled_cron()
	{
		global $wooexim_scheduled;
		
		$cron_id = isset($_POST['cron_id'])?$_POST['cron_id']:"";
		
		if($cron_id!="")
		{
			$scheduled_data = $wooexim_scheduled -> get_coupon_scheduled_data();
			
			unset($scheduled_data[$cron_id]);
			
			$scheduled_new_data = @maybe_serialize($scheduled_data);
				
			update_option('wooexim_coupon_scheduled_data',$scheduled_new_data);
			
			wp_clear_scheduled_hook('wooexim_cron_scheduled_coupon_export',array($cron_id));
		}
		
		$return_value = array();
		
		$return_value['message']	= 'success';
		
		echo json_encode($return_value);
		
		die();
	}
	
	function wooexim_delete_all_cron()
	{
		global $wooexim_scheduled;
		
		//delete order scheduled
		$order_scheduled_data = $wooexim_scheduled -> get_order_scheduled_data();
		
		if(!empty($order_scheduled_data))
		{	
			foreach($order_scheduled_data as $cron_id=>$value)	
			{
				wp_clear_scheduled_hook('wooexim_cron_scheduled_order_export',array($cron_id));
			}
		}
		
		//delete product scheduled
		$product_scheduled_data = $wooexim_scheduled -> get_product_scheduled_data();
		
		if(!empty($product_scheduled_data))
		{	
			foreach($product_scheduled_data as $cron_id=>$value)	
			{
				wp_clear_scheduled_hook('wooexim_cron_scheduled_product_export',array($cron_id));
			}
		}	
		
		//delete user scheduled
		$user_scheduled_data = $wooexim_scheduled -> get_user_scheduled_data();
		
		if(!empty($user_scheduled_data))
		{	
			foreach($user_scheduled_data as $cron_id=>$value)	
			{
				wp_clear_scheduled_hook('wooexim_cron_scheduled_user_export',array($cron_id));
			}
		}	
		
		//delete product category scheduled
		$product_cat_scheduled_data = $wooexim_scheduled -> get_product_cat_scheduled_data();
		
		if(!empty($product_cat_scheduled_data))
		{	
			foreach($product_cat_scheduled_data as $cron_id=>$value)	
			{
				wp_clear_scheduled_hook('wooexim_cron_scheduled_product_cat_export',array($cron_id));
			}
		}
		
		//delete coupon scheduled
		$coupon_scheduled_data = $wooexim_scheduled -> get_coupon_scheduled_data();
		
		if(!empty($coupon_scheduled_data))
		{	
			foreach($coupon_scheduled_data as $cron_id=>$value)	
			{
				wp_clear_scheduled_hook('wooexim_cron_scheduled_coupon_export',array($cron_id));
			}
		}	
	}
}
?>