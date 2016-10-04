<?php

if (!defined('ABSPATH'))
    die("Can't load this file directly");
	
class wooexim_import_export{

	function wooexim_import_export()
	{
		register_activation_hook( WOOEXIM_PLUGIN_DIR .'/wooexim.php',  array('wooexim_import_export', 'wooexim_install_data' ) );
		
		register_uninstall_hook(WOOEXIM_PLUGIN_DIR .'/wooexim.php', array( 'wooexim_import_export', 'wooexim_uninstall' ) );
		
		global $woocommerce;
    	
		$plugins = get_option('active_plugins');
		
		if(!function_exists('is_plugin_active_for_network'))
		{
			require_once(ABSPATH.'wp-admin/includes/plugin.php');
		}
	
		$required_woo_plugin = 'woocommerce/woocommerce.php';
			
		if (in_array( $required_woo_plugin , $plugins ) || is_plugin_active_for_network($required_woo_plugin)) {
		
			if( class_exists( 'Woocommerce' ) ){
				$this->wooexim_set_action();
			}else{
				add_action( 'woocommerce_loaded', array( &$this, 'wooexim_set_action' ) );
			}
		}
		
	}
	function wooexim_set_action()
	{
		add_action( 'plugins_loaded', array( &$this, 'wooexim_load_textdomain' ) );
		
		add_action( 'admin_enqueue_scripts', array( &$this, 'wooexim_set_admin_css' ),10);
				
		add_action( 'admin_enqueue_scripts', array( &$this, 'wooexim_set_admin_js' ),10);
		
		add_action( 'admin_menu', array( &$this, 'wooexim_set_menu' ) );
		
		add_action('admin_init', array(&$this, 'wooexim_db_check'));
  		
		add_action( 'admin_head',  array($this, 'wooexim_hide_all_notice_to_admin_side'), 10000 );
		
		add_filter('admin_footer_text', array(&$this, 'wooexim_replace_footer_admin'));
				
		add_filter( 'update_footer', array(&$this, 'wooexim_replace_footer_version'), '1234');
		
		add_action( 'wp_ajax_wooexim_deactivate_license', array( &$this, 'wooexim_deactivate_license' ));
		
 		add_action( 'wp_ajax_wooexim_activate_license', array( &$this, 'wooexim_activate_license' ));
		
		add_action( 'init', array( &$this, 'wooexim_download_exported_data' ));

  	} 
	function wooexim_install_data()
	{
		
		$wooexim_plugin_version = get_option('wooexim_plugin_version');
		
		if( !isset($wooexim_plugin_version) || $wooexim_plugin_version ==''  ) 
		{
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
								
				global $wpdb, $wooexim_product, $wooexim_order, $wooexim_user, $wooexim_product_cat, $wooexim_coupon;
				
				$charset_collate = '';
		
				if( $wpdb->has_cap( 'collation' ) ){
		
					if( !empty($wpdb->charset) )
						$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	
					if( !empty($wpdb->collate) )
						$charset_collate .= " COLLATE $wpdb->collate";
				}
				
				update_option('wooexim_plugin_version', WOOEXIM_PLUGIN_VERSION);
				
				$export_log = $wpdb->prefix.'wooexim_export_log';
				
				$product_fields = $wooexim_product -> get_new_product_fields();
				
				update_option('wooexim_product_fields', $product_fields);
				
				$order_fields = $wooexim_order -> get_new_order_fields();
				
				update_option('wooexim_order_fields', $order_fields);
				
				$user_fields = $wooexim_user -> get_new_user_fields();
				
				update_option('wooexim_user_fields', $user_fields);
				
				$product_cat_fields = $wooexim_product_cat -> get_new_product_cat_fields();
				
				update_option('wooexim_product_cat_fields', $product_cat_fields);
				
				$coupon_fields = $wooexim_coupon -> get_new_coupon_fields();
				
				update_option('wooexim_coupon_fields', $coupon_fields);
 
		}	
		
	}
	function wooexim_deactivate_license()
	{
		global $wooexim_import_export;
		
		$site_data = array();
		
		$return_value = array();
		
		$return_value['message']	= 'error';
			
 		$new_plugin_code = $wooexim_import_export -> generate_plugin_code();
		
		$plugin_data = $wooexim_import_export->get_wooexim_sort_order();
		
		$site_data['plugin_info'] = $plugin_data;
		
		$site_data['plugin_data'] = $new_plugin_code;
		
		$site_data['plugin_url']  = WOOEXIM_PLUGIN_URL;
			
		$site_data['plugin_version'] = get_option("wooexim_plugin_version");
		
		$site_data['plugin_status'] = 'deactive';
			
		$valstring = maybe_serialize($site_data);
			
		$post_data = base64_encode($valstring);
		
		$response = wp_remote_get( "http://wooexim.com/?wc-api=software-api&request=deactivation&email=" . $plugin_data['customer_email'] . "&license_key=" . $plugin_data['purchase_code'] . "&instance=" . $plugin_data['instance'] . "&product_id=WOOEXIMPROSS" );
			
		if(array_key_exists('body',$response) && isset($response["body"]) && $response["body"] != "")
		{
			try {
				$response_result = json_decode( $response['body'] );
			} catch ( Exception $ex ) {
				$response_result = null;
			}
			
			if($response_result->reset == 1)
			{
				update_option('wooexim_sort_order',$post_data);
				
				$return_value['message']	= 'success';
				
				$return_value['message_content']	= __('License Deactivated Successfully.',WOOEXIM_TEXTDOMAIN);
			}
			else
			{
				$return_value['message_content']	= __('Invalid Request Data.',WOOEXIM_TEXTDOMAIN);
			}
		}		
		
		echo json_encode($return_value );
		
		die();
	}
	function wooexim_db_check()
	{
		global $wooexim_import_export;
		
		$wooexim_plugin_version = get_option('wooexim_plugin_version');
 	
		if(( !isset($wooexim_plugin_version) || $wooexim_plugin_version =='') && is_multisite() ) 
		{
			$wooexim_import_export->wooexim_install_data();
		}
	}
	function wooexim_uninstall()
	{
 					
 			global $wpdb, $wooexim_scheduled;
			if ( is_multisite() ) {		
				$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
				if ($blogs) {
				
					foreach($blogs as $blog) {
					
						switch_to_blog($blog['blog_id']);
						
  						delete_option('wooexim_plugin_version');
						
						delete_option('wooexim_sort_order');
						
						delete_option('wooexim_product_scheduled_data');
						
						delete_option('wooexim_order_scheduled_data');
						
						delete_option('wooexim_user_scheduled_data');
						
						delete_option('wooexim_product_fields');
						
						delete_option('wooexim_product_cat_fields');
				
						delete_option('wooexim_order_fields');
				
						delete_option('wooexim_user_fields');
						
						delete_option('wooexim_coupon_fields');
												
 						$wooexim_export_log = $wpdb->prefix.'wooexim_export_log';
				 
						$wpdb->query("DROP TABLE IF EXISTS $wooexim_export_log");
						
						$wooexim_scheduled -> wooexim_delete_all_cron();
						
					}
					restore_current_blog();
				}
				
			} else {		
  						delete_option('wooexim_plugin_version');
						
						delete_option('wooexim_sort_order');
						
						delete_option('wooexim_product_scheduled_data');
						
						delete_option('wooexim_order_scheduled_data');
						
						delete_option('wooexim_user_scheduled_data');
						
						delete_option('wooexim_product_fields');
						
						delete_option('wooexim_product_cat_fields');
				
						delete_option('wooexim_order_fields');
				
						delete_option('wooexim_user_fields');
						
						delete_option('wooexim_coupon_fields');
						
						$wooexim_export_log = $wpdb->prefix.'wooexim_export_log';
				 
						$wpdb->query("DROP TABLE IF EXISTS $wooexim_export_log");
						
						$wooexim_scheduled -> wooexim_delete_all_cron();
 			}
		
	}
	function wooexim_load_textdomain()
	{
		load_plugin_textdomain( WOOEXIM_TEXTDOMAIN,false,'wooexim/languages/' );
	}
	function wooexim_activate_license()
	{
		global $wooexim_product, $wooexim_import_export;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$site_data = array();
		
		$return_value = array();
		
		$return_value['message']	= 'error';
			
		$site_data['customer_name']  = $_POST["wooexim_customer_name"];
		
		$site_data['customer_email'] = $_POST["wooexim_customer_email"];
		
		$site_data['purchase_code']  = $_POST["wooexim_customer_purchase_code"];
		
		$site_data['domain_name']    = $_POST["wooexim_product_domain_name"];
			
		if(!isset($_POST["wooexim_product_domain_name"]) || $_POST["wooexim_product_domain_name"]== "" || $_SERVER["HTTP_HOST"] != $_POST["wooexim_product_domain_name"])
		{
			
			$return_value['message_content'] = 'Invalid Host Name';
			
			echo json_encode($return_value );
			
			die();
			
		}
		
		$new_plugin_code = $wooexim_import_export -> generate_plugin_code();
		
		$site_data['plugin_data'] = $new_plugin_code;
		
		$site_data['plugin_url']  = WOOEXIM_PLUGIN_URL;
			
		$site_data['plugin_version'] = get_option("wooexim_plugin_version");
		
		$site_data['plugin_status'] = 'active';
			
		$response = wp_remote_get( "http://wooexim.com/?wc-api=software-api&request=activation&email=" . $site_data['customer_email'] . "&license_key=" . $site_data['purchase_code'] . "&product_id=WOOEXIMPROSS" );
			
		if(array_key_exists('body',$response) && isset($response["body"]) && $response["body"] != "")
		{
			try {
				$response_result = json_decode( $response['body'] );
			} catch ( Exception $ex ) {
				$response_result = null;
			}
			
			if(isset($response_result->activated) && $response_result->activated==1)
			{
				$site_data['instance'] = $response_result->instance;
				
				$valstring = maybe_serialize($site_data);
				
				$post_data = base64_encode($valstring);
				
				update_option('wooexim_sort_order',$post_data);
				
				$return_value['message']	= 'success';
				
				$return_value['message_content']	=  __('License Activated Successfully.',WOOEXIM_TEXTDOMAIN);
			}
			else {
				$return_value['message_content']	=  __('Invalid Request Data.',WOOEXIM_TEXTDOMAIN);
			}
		}	
		
		echo json_encode($return_value );
		
		die();
	}
	function wooexim_set_admin_css()
	{
		wp_register_style( 'wooexim_admin_css',WOOEXIM_CSS_URL.'/wooexim_admin.css');
				
		wp_register_style( 'wooexim_admin_chosen_css',WOOEXIM_CURRENT_PLUGIN_URL.'/woocommerce/assets/css/chosen.css');
		
		wp_register_style( 'wooexim_admin_flexi_grid_css',WOOEXIM_CSS_URL.'/wooexim_flexigrid.css');
		
		wp_register_style( 'wooexim_admin_jquery_ui_css',WOOEXIM_CSS_URL.'/jquery-ui.css');
		
		if(isset($_REQUEST['page']) &&  ($_REQUEST['page'] == 'wooexim-woo-export' || $_REQUEST['page'] == 'wooexim-woo-import' || $_REQUEST['page'] == 'wooexim-woo-log' || $_REQUEST['page'] == 'wooexim-settings' || $_REQUEST['page'] == 'wooexim-woo-scheduled-export'))
		{
			wp_enqueue_style( 'wooexim_admin_css' );
		}
 		if(isset($_REQUEST['page']) &&  ($_REQUEST['page'] == 'wooexim-woo-export'))
		{
 			wp_enqueue_style( 'wooexim_admin_chosen_css' );
			wp_enqueue_style( 'wooexim_admin_flexi_grid_css' );
			wp_enqueue_style( 'wooexim_admin_jquery_ui_css' );
		}
		if(isset($_REQUEST['page']) &&  ($_REQUEST['page'] == 'wooexim-woo-import'))
		{
 			wp_enqueue_style( 'wooexim_admin_chosen_css' );
			wp_enqueue_style( 'wooexim_admin_flexi_grid_css' );
		}
		if(isset($_REQUEST['page']) &&  ($_REQUEST['page'] == 'wooexim-woo-scheduled-export'))
		{
			wp_enqueue_style( 'wooexim_admin_flexi_grid_css' );
		}
	}
	function generate_plugin_code()
	{
		$site_info = array();
			
		$site_info['blog_name']     = get_bloginfo('name');
		
		$site_info['description']   = get_bloginfo('description');
		
		$site_info['site_home_url'] = home_url();
		
		$site_info['admin_email']   = get_bloginfo('admin_email');
		
		$site_info['server_addr']   = $_SERVER['SERVER_ADDR'];
		
		$new_str = implode("^|^",$site_info);
		
		$post_val = base64_encode($new_str);
			
		return $post_val;	
	}
	function wooexim_set_admin_js()
	{
		wp_register_script( 'wooexim_admin_js',WOOEXIM_JS_URL.'/wooexim_admin.js');
		
		wp_register_script( 'wooexim_admin_chosen_js',WOOEXIM_CURRENT_PLUGIN_URL.'/woocommerce/assets/js/chosen/chosen.jquery.min.js');
		
		wp_register_script( 'wooexim_admin_flexi_grid_js',WOOEXIM_JS_URL.'/wooexim_flexigrid.js');
		
		wp_register_script( 'wooexim_form_min_js',WOOEXIM_JS_URL.'/wooexim.form.min.js');
		
		
		if(isset($_REQUEST['page']) &&  ($_REQUEST['page'] == 'wooexim-woo-export' || $_REQUEST['page'] == 'wooexim-woo-import' || $_REQUEST['page'] == 'wooexim-woo-log' || $_REQUEST['page'] == 'wooexim-settings' || $_REQUEST['page'] == 'wooexim-woo-scheduled-export'))
		{
			wp_enqueue_script( 'jquery' );
 			wp_enqueue_script( 'wooexim_admin_js' );
 		}
		if(isset($_REQUEST['page']) &&  ($_REQUEST['page'] == 'wooexim-woo-export'))
		{
			wp_enqueue_script( 'jquery-ui-datepicker' );
			
			wp_enqueue_script( 'wooexim_admin_flexi_grid_js' );
			
			wp_enqueue_script( 'wooexim_admin_chosen_js' );
			
			wp_enqueue_script( 'wooexim_admin_flexi_grid_js' );
		}
		if(isset($_REQUEST['page']) &&  ($_REQUEST['page'] == 'wooexim-woo-import'))
		{
 			wp_enqueue_script( 'wooexim_admin_chosen_js' );
			
			wp_enqueue_script( 'wooexim_form_min_js' );
			
			wp_enqueue_script( 'wooexim_admin_flexi_grid_js' );
		}
		if(isset($_REQUEST['page']) &&  ($_REQUEST['page'] == 'wooexim-woo-scheduled-export'))
		{
  			
			wp_enqueue_script( 'wooexim_admin_flexi_grid_js' );
		}
		
	}
	function wooexim_hide_all_notice_to_admin_side()
	{
 		if(isset($_REQUEST['page']) &&  ($_REQUEST['page'] == 'wooexim-woo-export' || $_REQUEST['page'] == 'wooexim-woo-import' || $_REQUEST['page'] == 'wooexim-woo-log' || $_REQUEST['page'] == 'wooexim-settings'))
		{
			remove_all_actions( 'admin_notices',10000 );
			remove_all_actions( 'all_admin_notices',10000 );
			remove_all_actions( 'network_admin_notices',10000 );
			remove_all_actions( 'user_admin_notices',10000 );
			
		}
	}
	function wooexim_set_menu()
	{
		global $wooexim_import_export, $current_user;
		
		if(current_user_can('administrator')  || is_super_admin())
		{
 			$wooexim_caps= $wooexim_import_export->wooexim_capabilities();
			
			foreach($wooexim_caps as $wooexim_cap => $cap_desc)
			{
				$current_user->add_cap( $wooexim_cap );
			}
		}
		
  		$menu_place = $wooexim_import_export->get_dynamic_position(28.1 , .1);
				
		add_menu_page( __('Import Export',WOOEXIM_TEXTDOMAIN),  __('WOOEXIM',WOOEXIM_TEXTDOMAIN), 'wooexim_export', 'wooexim-woo-export', array(&$this,'wooexim_get_page'),WOOEXIM_IMAGES_URL.'/wooexim_logo.png',(string)$menu_place);

		add_submenu_page( 'wooexim-woo-export', __('Export',WOOEXIM_TEXTDOMAIN), __('Export',WOOEXIM_TEXTDOMAIN), 'wooexim_export', 'wooexim-woo-export', array(&$this,'wooexim_get_page')) ;
		
		add_submenu_page( 'wooexim-woo-export', __('Import',WOOEXIM_TEXTDOMAIN), __('Import',WOOEXIM_TEXTDOMAIN), 'wooexim_import', 'wooexim-woo-import', array(&$this,'wooexim_get_page')) ;
		
		add_submenu_page( 'wooexim-woo-export', __('Manage Scheduled',WOOEXIM_TEXTDOMAIN), __('Schedule',WOOEXIM_TEXTDOMAIN), 'wooexim_manage_scheduled_export', 'wooexim-woo-scheduled-export', array(&$this,'wooexim_get_page'));
		
		add_submenu_page( 'wooexim-woo-export', __('Export Log',WOOEXIM_TEXTDOMAIN), __('Archive',WOOEXIM_TEXTDOMAIN), 'wooexim_manage', 'wooexim-woo-log', array(&$this,'wooexim_get_page'));
		
		add_submenu_page( 'wooexim-woo-export', __('Settings',WOOEXIM_TEXTDOMAIN), __('Settings',WOOEXIM_TEXTDOMAIN), 'wooexim_settings', 'wooexim-settings', array(&$this,'wooexim_get_page'));
				
		
  	}
	function wooexim_get_page()
	{
		global $wooexim_import_export;
		
		if( isset($_REQUEST['page']) and $_REQUEST['page'] == 'wooexim-woo-export' ){	
			
			$wooexim_import_export->wooexim_get_export();
			
		}else if( isset($_REQUEST['page']) and $_REQUEST['page'] == 'wooexim-woo-import' ){	
			
			$wooexim_import_export->wooexim_get_import();
			
		}else if( isset($_REQUEST['page']) and $_REQUEST['page'] == 'wooexim-woo-scheduled-export' ){	
			
			$wooexim_import_export->wooexim_get_scheduled_export();
			
		}else if( isset($_REQUEST['page']) and $_REQUEST['page'] == 'wooexim-woo-log' ){	
			
			$wooexim_import_export->wooexim_get_product_log();
			
		}else if( isset($_REQUEST['page']) and $_REQUEST['page'] == 'wooexim-settings' ){	
			
			$wooexim_import_export->wooexim_get_settings();
		}
		
		global $WOOEXIM_AJAXURL;
		?>
 			<script type="text/javascript">var wooexim_ajax_url="<?php echo $WOOEXIM_AJAXURL;?>";</script>
 		<?php
	}
	function wooexim_capabilities()
	{
		$cap = array(
		
			'wooexim_export' => __('manage woocommerce export.', WOOEXIM_TEXTDOMAIN),
			'wooexim_import' => __('manage woocommerce import.', WOOEXIM_TEXTDOMAIN),
			'wooexim_manage' => __('manage woocommerce import/export.', WOOEXIM_TEXTDOMAIN),
			'wooexim_manage_scheduled_export' => __('manage scheduled export.', WOOEXIM_TEXTDOMAIN),
			'wooexim_settings' => __('manage woocommerce import/export settings.', WOOEXIM_TEXTDOMAIN),
		);

		return $cap;
	}
	function get_dynamic_position($start, $increment = 0.1)
	{
			foreach ($GLOBALS['menu'] as $key => $menu) {
				$menus_positions[] = $key;
			}
			if (!in_array($start, $menus_positions)) return $start;
			
 			while (in_array($start, $menus_positions)) {
				$start += $increment;
			}
			return $start;
	}
	function wooexim_get_export()
	{
		if( file_exists( WOOEXIM_VIEW_DIR.'/wooexim_export.php' ) ){
			include( WOOEXIM_VIEW_DIR.'/wooexim_export.php' );
		}
	}
	function wooexim_get_import()
	{
		if( file_exists( WOOEXIM_VIEW_DIR.'/wooexim_import.php' ) ){
			include( WOOEXIM_VIEW_DIR.'/wooexim_import.php' );
		}
	}
	function wooexim_get_scheduled_export()
	{
		if( file_exists( WOOEXIM_VIEW_DIR.'/wooexim_scheduled_export.php' ) ){
			include( WOOEXIM_VIEW_DIR.'/wooexim_scheduled_export.php' );
		}
	}
	function wooexim_get_product_log()
	{
		if( file_exists( WOOEXIM_VIEW_DIR.'/wooexim_export_log.php' ) ){
			include( WOOEXIM_VIEW_DIR.'/wooexim_export_log.php' );
		}
	}
	function wooexim_get_settings()
	{
		if( file_exists( WOOEXIM_VIEW_DIR.'/wooexim_settings.php' ) ){
			include( WOOEXIM_VIEW_DIR.'/wooexim_settings.php' );
		}
	}
	function wooexim_replace_footer_admin ()   
	{  
		echo '';
	}  
	
	function wooexim_replace_footer_version() 
	{
		return '';
	}
	function get_wooexim_sort_order()
	{
		$wooexim_sort_order = get_option('wooexim_sort_order');
		
		if($wooexim_sort_order && $wooexim_sort_order!="")
		{
			return @maybe_unserialize(base64_decode($wooexim_sort_order));
		}else{
			return "";
		} 
	}
	function wooexim_set_time_limit($time)
	{
		$safe_mode = ini_get('safe_mode');
		
		if(!$safe_mode or $safe_mode == 'Off' or $safe_mode == 'off' or $safe_mode == 'OFF')
		{
			@set_time_limit($time);
		}
	}	
	function wooexim_download_exported_data()
	{
		$plugin_data = $this->get_wooexim_sort_order();
		
		if( isset($_POST['wooexim_product_export_verify']) && $_POST['wooexim_product_export_verify'] == 1 && !isset($_POST['action'])){
		
			global $wooexim_product,$wpdb;
		
			$filename = 'wooexim_product_'.date('Y_m_d_H_i_s').'.csv';
			
			$product_export_data = $wooexim_product -> get_product_export_data($_POST);
  	  
			header( 'Cache-Control:must-revalidate,post-check=0,pre-check=0' );
			
			header( 'Content-Description: File Transfer' );
			
			header( 'Content-Type: text/csv;');
			
			header( 'Content-Disposition: attachment; filename=' . $filename );
			
			header( 'Expires:0' );
			
			header( 'Pragma: public');
			
			$fh = @fopen(WOOEXIM_UPLOAD_DIR.'/'.$filename,'w+');
			
 			foreach($product_export_data as $new_data)
			{
				@fputcsv($fh , $new_data);
			}
			
			@fclose($fh);
			
			readfile(WOOEXIM_UPLOAD_DIR.'/'.$filename );
			
  			$new_values = array();
			
			$new_values['export_log_file_type'] = 'csv';			
			$new_values['export_log_file_name'] = $filename;
			$new_values['export_log_data'] 		= 'Product';
 			$new_values['create_date'] 		    = current_time('mysql');
			
			$res = $wpdb->insert($wpdb->prefix."wooexim_export_log", $new_values);
						
			die();
			
		}else if( isset($_POST['wooexim_download_exported_file']) && $_POST['wooexim_download_exported_file'] != "" && !isset($_POST['action'])){
			
			$filename = $_POST['wooexim_download_exported_file'];
		
			if(file_exists(WOOEXIM_UPLOAD_DIR.'/'.$filename ))
			{
	  
				header( 'Cache-Control:must-revalidate,post-check=0,pre-check=0' );
				
				header( 'Content-Description: File Transfer' );
				
				header( 'Content-Type: text/csv;');
				
				header( 'Content-Disposition: attachment; filename=' . $filename );
				
				header( 'Expires:0' );
				
				header( 'Pragma: public');
				
				readfile(WOOEXIM_UPLOAD_DIR.'/'.$filename );
				
			}
			
			die();
			
		}else if( isset($plugin_data['plugin_status']) && $plugin_data['plugin_status'] == 'active' && isset($_POST['wooexim_ordert_export_verify']) && $_POST['wooexim_ordert_export_verify'] == 1 && !isset($_POST['action'])){
			
			global $wooexim_order,$wpdb;
		
			$filename = 'wooexim_order_'.date('Y_m_d_H_i_s').'.csv';
			
			$order_export_data = $wooexim_order -> get_order_csv_data($_POST);
	  
			header( 'Cache-Control:must-revalidate,post-check=0,pre-check=0' );
			
			header( 'Content-Description: File Transfer' );
			
			header( 'Content-Type: text/csv;');
			
			header( 'Content-Disposition: attachment; filename=' . $filename );
			
			header( 'Expires:0' );
			
			header( 'Pragma: public');
			
			$fh = @fopen(WOOEXIM_UPLOAD_DIR.'/'.$filename,'w+');
			
 			foreach($order_export_data as $new_data)
			{
				@fputcsv($fh , $new_data);
			}
			
 			@fclose($fh);
			
			readfile(WOOEXIM_UPLOAD_DIR.'/'.$filename );
			
  			$new_values = array();
			
			$new_values['export_log_file_type'] = 'csv';			
			$new_values['export_log_file_name'] = $filename;
			$new_values['export_log_data'] 		= 'Order';
 			$new_values['create_date'] 		    = current_time('mysql');
			
			$res = $wpdb->insert($wpdb->prefix."wooexim_export_log", $new_values);
						
			die();
			
		}else if( isset($plugin_data['plugin_status']) && $plugin_data['plugin_status'] == 'active' && isset($_POST['wooexim_user_export_verify']) && $_POST['wooexim_user_export_verify'] == 1 && !isset($_POST['action'])){
			
			global $wooexim_user,$wpdb;
		
			$filename = 'wooexim_user_'.date('Y_m_d_H_i_s').'.csv';
			
			$user_export_data = $wooexim_user -> get_user_csv_data($_POST);
	  
			header( 'Cache-Control:must-revalidate,post-check=0,pre-check=0' );
			
			header( 'Content-Description: File Transfer' );
			
			header( 'Content-Type: text/csv;');
			
			header( 'Content-Disposition: attachment; filename=' . $filename );
			
			header( 'Expires:0' );
			
			header( 'Pragma: public');
			
			$fh = @fopen(WOOEXIM_UPLOAD_DIR.'/'.$filename,'w+');
			
 			foreach($user_export_data as $new_data)
			{
				@fputcsv($fh , $new_data);
			}
			
 			@fclose($fh);
			
			readfile(WOOEXIM_UPLOAD_DIR.'/'.$filename );
			
  			$new_values = array();
			
			$new_values['export_log_file_type'] = 'csv';			
			$new_values['export_log_file_name'] = $filename;
			$new_values['export_log_data'] 		= 'User';
 			$new_values['create_date'] 		    = current_time('mysql');
			
			$res = $wpdb->insert($wpdb->prefix."wooexim_export_log", $new_values);
						
			die();
			
		}else if( isset($plugin_data['plugin_status']) && $plugin_data['plugin_status'] == 'active' && isset($_POST['wooexim_product_cat_export_verify']) && $_POST['wooexim_product_cat_export_verify'] == 1 && !isset($_POST['action'])){
		
			global $wooexim_product, $wpdb, $wooexim_product_cat;
		
			$filename = 'wooexim_product_category_'.date('Y_m_d_H_i_s').'.csv';
			
			$product_cat_export_data = $wooexim_product_cat -> get_product_cat_export_data($_POST);
  	  
			header( 'Cache-Control:must-revalidate,post-check=0,pre-check=0' );
			
			header( 'Content-Description: File Transfer' );
			
			header( 'Content-Type: text/csv;');
			
			header( 'Content-Disposition: attachment; filename=' . $filename );
			
			header( 'Expires:0' );
			
			header( 'Pragma: public');
			
			$fh = @fopen(WOOEXIM_UPLOAD_DIR.'/'.$filename,'w+');
			
 			foreach($product_cat_export_data as $new_data)
			{
				@fputcsv($fh , $new_data);
			}
			
			@fclose($fh);
			
			readfile(WOOEXIM_UPLOAD_DIR.'/'.$filename );
			
  			$new_values = array();
			
			$new_values['export_log_file_type'] = 'csv';			
			$new_values['export_log_file_name'] = $filename;
			$new_values['export_log_data'] 		= 'Product Category';
 			$new_values['create_date'] 		    = current_time('mysql');
			
			$res = $wpdb->insert($wpdb->prefix."wooexim_export_log", $new_values);
						
			die();
			
		}else if( isset($plugin_data['plugin_status']) && $plugin_data['plugin_status'] == 'active' && isset($_POST['wooexim_coupon_export_verify']) && $_POST['wooexim_coupon_export_verify'] == 1 && !isset($_POST['action'])){
		
			global $wooexim_product, $wpdb, $wooexim_coupon;
		
			$filename = 'wooexim_coupon_'.date('Y_m_d_H_i_s').'.csv';
			
			$coupon_export_data = $wooexim_coupon -> get_coupon_export_data($_POST);
  	  
			header( 'Cache-Control:must-revalidate,post-check=0,pre-check=0' );
			
			header( 'Content-Description: File Transfer' );
			
			header( 'Content-Type: text/csv;');
			
			header( 'Content-Disposition: attachment; filename=' . $filename );
			
			header( 'Expires:0' );
			
			header( 'Pragma: public');
			
			$fh = @fopen(WOOEXIM_UPLOAD_DIR.'/'.$filename,'w+');
			
 			foreach($coupon_export_data as $new_data)
			{
				@fputcsv($fh , $new_data);
			}
			
			@fclose($fh);
			
			readfile(WOOEXIM_UPLOAD_DIR.'/'.$filename );
			
  			$new_values = array();
			
			$new_values['export_log_file_type'] = 'csv';			
			$new_values['export_log_file_name'] = $filename;
			$new_values['export_log_data'] 		= 'Coupon';
 			$new_values['create_date'] 		    = current_time('mysql');
						
			die();
			
		}
	}
}
?>