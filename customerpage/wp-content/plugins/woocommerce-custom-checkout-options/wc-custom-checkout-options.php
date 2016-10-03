<?php
/*
Plugin Name: WooCommerce Custom Checkout Options
Plugin URI: http://terrytsang.com/shop/shop/woocommerce-custom-checkout-options/
Description: Customize WooCommerce Checkout fields options and allow to insert additional fields
Version: 1.0.9
Author: Terry Tsang
Author URI: http://www.terrytsang.com
*/

/*  
Copyright (C) 2012-2014  Terry Tsang (email : terrytsang811@gmail.com)
License: Single Site
*/

// Define plugin name.
define('wc_custom_checkout_options_plugin_name', 'WooCommerce Custom Checkout Options');

// Checks if the WooCommerce plugins is installed and active.
if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){
	if(!class_exists('WooCommerce_Custom_Checkout_Options')){
		class WooCommerce_Custom_Checkout_Options{

			public static $plugin_prefix;
			public static $plugin_url;
			public static $plugin_path;
			public static $plugin_basefile;

			var $tab_name;
			var $first_load;
			var $hidden_submit;
			var $current_tab;
			var $field_types;
			var $position_types;
			var $yes_no;
			var $allowed_fields;
			var $field_options;
			var $all_field_options;
			var $max_limit_field;
			var $custom_field_name;
			
			var $enabled_billing;
			var $disabled_billing;
			var $enabled_shipping;
			var $disabled_shipping;
			var $enabled_account;
			var $disabled_account;
			var $enabled_order;
			var $disabled_order;
			
			var $enabled_array;
			
			/**
			 * initialize this plugin
			 */
			public function __construct(){
				global $woocommerce;
				
				self::$plugin_prefix = 'wc_custom_checkout_';
				self::$plugin_basefile = plugin_basename(__FILE__);
				self::$plugin_url = plugin_dir_url(self::$plugin_basefile);
				self::$plugin_path = trailingslashit(dirname(__FILE__));
				
				$this->tab_name = 'custom-checkout-options';
				$this->first_load = false;
				$this->hidden_submit = self::$plugin_prefix . 'submit';
				$this->max_limit_field = 100;
				$this->custom_field_name = 'newfield';
				
				$this->current_tab = ( isset($_GET['tab']) ) ? $_GET['tab'] : 'general';
				$this->settings_tabs = array(
						'custom-checkout-options' => __('Custom Checkout', 'woothemes')
				);

				$this->enabled_billing = array();
				$this->disabled_billing = array();
				$this->order_billing = array();
				
				$this->enabled_shipping = array();
				$this->disabled_shipping = array();
				$this->order_shipping = array();
				
				$this->enabled_account = array();
				$this->disabled_account = array();
				$this->order_account = array();
				
				$this->enabled_order = array();
				$this->disabled_order = array();
				$this->order_order = array();
				
				$this->enabled_array = array();
				
				$this->field_types = array('text' => 'Text', 'textarea' => 'Textarea', 'password' => 'Password', 'date' => 'Date', 'country' => 'Country', 'state' => 'State', 'select' => 'Select', 'checkbox' => 'Checkbox');
				$this->position_types = array('billing' => 'Billing', 'shipping' => 'Shipping', 'account' => 'Account', 'order' => 'Order');
				$this->yes_no = array(1 => 'Yes', 0 => 'No');
				$this->allowed_fields = array('name', 'type', 'label', 'placeholder', 'class', 'label_class', 'required', 'options', 'order' );
				$this->field_options = array('enabled', 'type', 'label', 'placeholder', 'class', 'label_class', 'required', 'default', 'options', 'order', 'section' );
				$this->all_field_options = array('enabled', 'type', 'field_name', 'label', 'placeholder', 'class', 'label_class', 'required', 'default', 'options', 'order', 'section' );
				
				add_action('woocommerce_init', array(&$this, 'init'));
				add_action('admin_init', array(&$this, 'custom_order_admin_init'));
			}
			
			/**
			 * Load custom meta box
			 */
			public function custom_order_admin_init() {
				add_meta_box( 'custom_order_details_meta_box',
				'Custom Order Data',
				array(&$this, 'custom_order_display_details_meta_box'),
				'shop_order', 'normal', 'default' );
				
				add_action('save_post', array(&$this, 'custom_save_order_fields'), 10, 2 );
				
			}
			
			public function custom_order_display_details_meta_box( $order ) { 
		    
				// Retrieve all custom fields based on order ID
				$custom_fields = array();
				$new_custom_fields_array = $this->get_new_custom_fields();
				$boolNewFields = false;
				
				if($new_custom_fields_array && count($new_custom_fields_array) > 0)
				{
					$boolNewFields = true;
					foreach($new_custom_fields_array as $field_name => $field_array)
					{
						if($field_array)
							$label_field = $field_array['label'];
						else
							$label_field = '_' . $field_name;
						
						$custom_fields[$field_name] = $label_field;
					}
				}
				
				if($boolNewFields)
				{
					echo "<table>";	
					foreach($custom_fields as $field => $label)
					{
						$field_value = "";
				
						if(get_post_meta($order->ID, '_' . $field, true))
							$field_value = get_post_meta($order->ID, '_' . $field, true);
						else
							$field_value = get_post_meta($order->ID, $label, true);
				
						?>
			        	<tr>
			            	<td style="width: 40%"><?php echo $label; ?></td>
			            	<td><input type="text" size="30" name="custom_order<?php echo '_' . $field; ?>" value="<?php echo $field_value; ?>" /></td>
			        	</tr>
			        	<?php
					}
					echo "</table>";
				}

			}
			
			public function custom_save_order_fields( $order_id, $order ) {
				
			    // Check post type for shop order
			    if ( $order->post_type == 'shop_order' ) { 
			    	
			    	$custom_fields = array(); 
			    	$new_custom_fields_array = $this->get_new_custom_fields();
			    	$boolNewFields = false;
			    	
			    	if($new_custom_fields_array && count($new_custom_fields_array) > 0)
			    	{
			    		$boolNewFields = true;
			    		foreach($new_custom_fields_array as $field_name => $field_array)
			    		{
			    			if($field_array)
			    				$label_field = $field_array['label'];
			    			else
			    				$label_field = '_' . $field_name;
			    	
			    			$custom_fields[$field_name] = $label_field;
			    		}
			    	}
			    	
			    	if($boolNewFields)
			    	{
			    		foreach($custom_fields as $field => $label) 
			    		{
			    			// save data in post meta table if present in POST data
			    			if ( isset( $_POST['custom_order_'.$field] ) && $_POST['custom_order_'.$field] != '' ) {
			    				update_post_meta( $order->ID, $label, $_POST['custom_order_'.$field] );
			    			}
			    		}
			    	}
  
			        
			    }
			}
			
			/**
			 * Load javascript for the page
			 */
			public function custom_plugin_script()
			{
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'custom-plugin-script', plugins_url('/js/script.js', __FILE__));
			}
			
			/**
			 * Load stylesheet for the page
			 */
			public function custom_plugin_stylesheet() {
				wp_register_style( 'custom-plugin-stylesheet', plugins_url('/css/jquery-ui.css', __FILE__) );
				wp_enqueue_style( 'custom-plugin-stylesheet' );
			}
			
			
			/**
			 * Check if woocommerce is activated
			 */
			public function check_woocommerce_activated() {
				if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
					return true;
				} else {
					return false;
				}
			}

			
			/**
			 * Init WooCommerce Custom Checkout Options
			 */
			public function init(){
				global $woocommerce;
				
				// load localization
				if ( $this->check_woocommerce_activated() ) {
					load_plugin_textdomain( 'woocommerce-custom-checkout-options', false, dirname( self::$plugin_basefile ) . '/languages' );
				}
				
				if( isset($_GET['action']) )
				{
					if($_GET['action'] == 'delete' && isset($_GET['option']))
					{
						$option = $_GET['option'];
						$option_name = self::$plugin_prefix.$option;
						
						foreach($this->all_field_options as $field_option)
						{
							delete_option($option_name . '_' . $field_option);
						}
					}
				}
				
				//load javascript & stylesheet
				add_action( 'wp_enqueue_scripts', array(&$this, 'custom_plugin_stylesheet') );
				add_action( 'wp_enqueue_scripts', array(&$this, 'custom_plugin_script') );
				
				//set the default checkout fields for woocommerce
				$this->checkout_fields = array(
						'billing_first_name' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('First Name',$this->tab_name),
								'placeholder'  	=> __('First Name',$this->tab_name),
								'class'   		=> 'form-row-first',
								'label_class'   => '',
								'required'      => true,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'billing'
						),
						'billing_last_name' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Last Name',$this->tab_name),
								'placeholder'  	=> __('Last Name',$this->tab_name),
								'class'   		=> 'form-row-last',
								'label_class'   => '',
								'required'      => true,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'billing'
						),
						'billing_company' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Company (optional)',$this->tab_name),
								'placeholder'  	=> __('Company (optional)',$this->tab_name),
								'class'  		=> '',
								'label_class'   => '',
								'required'      => false,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'billing'
						),
						'billing_address_1' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Address',$this->tab_name),
								'placeholder'  	=> __('Address',$this->tab_name),
								'class'   		=> 'form-row-first',
								'label_class'   => '',
								'required'      => true,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'billing'
						),
						'billing_address_2' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Address 2',$this->tab_name),
								'placeholder'  	=> __('Address 2',$this->tab_name),
								'class'   		=> 'form-row-last',
								'label_class'   => 'hidden',
								'required'      => true,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'billing'
						),
						'billing_city' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Town/City',$this->tab_name),
								'placeholder'  	=> __('Town/City',$this->tab_name),
								'class'   		=> 'form-row-first',
								'label_class'   => '',
								'required'      => true,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'billing'
						),
						'billing_postcode' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Postcode/Zip',$this->tab_name),
								'placeholder'  	=> __('Postcode/Zip',$this->tab_name),
								'class'   		=> 'form-row-last,update_totals_on_change',
								'label_class'   => '',
								'required'      => true,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'billing'
						),
						'billing_country' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'country',
								'label'         => __('Country',$this->tab_name),
								'placeholder'  	=> __('Country',$this->tab_name),
								'class'   		=> 'form-row-first,update_totals_on_change,country_select',
								'label_class'   => '',
								'required'      => true,
								'default'      	=> true,
								'options'       => array( '' => __( 'Select a country&hellip;', $this->tab_name ) ) + $woocommerce->countries->get_allowed_countries(),
								'order'         => '',
								'section'		=> 'billing'
						),
						'billing_state' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'state',
								'label'         => __('State/County',$this->tab_name),
								'placeholder'  	=> __('State/County',$this->tab_name),
								'class'   		=> 'form-row-last,update_totals_on_change',
								'label_class'   => '',
								'required'      => false,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'billing'
						),
						'billing_email' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Email Address',$this->tab_name),
								'placeholder'  	=> __('Email Address',$this->tab_name),
								'class'   		=> 'form-row-first',
								'label_class'   => '',
								'required'      => true,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'billing'
						),
						'billing_phone' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Phone',$this->tab_name),
								'placeholder'  	=> __('Phone',$this->tab_name),
								'class'  	 	=> 'form-row-last',
								'label_class'   => '',
								'required'      => true,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'billing'
						),
						'shipping_first_name' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('First Name',$this->tab_name),
								'placeholder'  	=> __('First Name',$this->tab_name),
								'class'   		=> 'form-row-first',
								'label_class'   => '',
								'required'      => true,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'shipping'
						),
						'shipping_last_name' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Last Name',$this->tab_name),
								'placeholder'  	=> __('Last Name',$this->tab_name),
								'class'   		=> 'form-row-last',
								'label_class'   => '',
								'required'      => true,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'shipping'
						),
						'shipping_company' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Company (optional)',$this->tab_name),
								'placeholder'  	=> __('Company (optional)',$this->tab_name),
								'class'   		=> '',
								'label_class'   => '',
								'required'      => false,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'shipping'
						),
						'shipping_address_1' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Address',$this->tab_name),
								'placeholder'  	=> __('Address',$this->tab_name),
								'class'   		=> 'form-row-first',
								'label_class'   => '',
								'required'      => true,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'shipping'
						),
						'shipping_address_2' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Address 2',$this->tab_name),
								'placeholder'  	=> __('Address 2',$this->tab_name),
								'class'   		=> 'form-row-last',
								'label_class'   => 'hidden',
								'required'      => true,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'shipping'
						),
						'shipping_city' 		=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Town/City',$this->tab_name),
								'placeholder'  	=> __('Town/City',$this->tab_name),
								'class'   		=> 'form-row-first',
								'label_class'   => '',
								'required'      => true,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'shipping'
						),
						'shipping_postcode' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Postcode/Zip',$this->tab_name),
								'placeholder'  	=> __('Postcode/Zip',$this->tab_name),
								'class'   		=> 'form-row-last,update_totals_on_change',
								'label_class'   => '',
								'required'      => true,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'shipping'
						),
						'shipping_country' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'country',
								'label'         => __('Country',$this->tab_name),
								'placeholder'  	=> __('Country',$this->tab_name),
								'class'   		=> 'form-row-first,update_totals_on_change,country_select',
								'label_class'   => '',
								'required'      => true,
								'default'      	=> true,
								'options'       => array( '' => __( 'Select a country&hellip;', $this->tab_name ) ) + $woocommerce->countries->get_allowed_countries(),
								'order'         => '',
								'section'		=> 'shipping'
						),
						'shipping_state' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'state',
								'label'         => __('State/County',$this->tab_name),
								'placeholder'  	=> __('State/County',$this->tab_name),
								'class'   		=> 'form-row-last,update_totals_on_change',
								'label_class'   => '',
								'required'      => false,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'shipping'
						),
						'account_username' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'text',
								'label'         => __('Account username',$this->tab_name),
								'placeholder'  	=> __('Username',$this->tab_name),
								'class'   		=> '',
								'label_class'   => '',
								'required'      => false,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'account'
						),
						'account_password' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'password',
								'label'         => __('Account password',$this->tab_name),
								'placeholder'  	=> __('Password',$this->tab_name),
								'class'   		=> 'form-row-first',
								'label_class'   => '',
								'required'      => false,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'account'
						),
						'account_password-2' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'password',
								'label'         => __('Account password',$this->tab_name),
								'placeholder'  	=> __('Password',$this->tab_name),
								'class'   		=> 'form-row-last',
								'label_class'   => 'hidden',
								'required'      => false,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'account'
						),
						'order_comments' 	=> array(
								'enabled'		=>	true,
								'type'      	=> 	'textarea',
								'label'         => __('Order Notes',$this->tab_name),
								'placeholder'  	=> __('Notes about your order, e.g. special notes for delivery.',$this->tab_name),
								'class'   		=> 'notes',
								'label_class'   => '',
								'required'      => false,
								'default'      	=> true,
								'options'       => '',
								'order'         => '',
								'section'		=> 'order'
						),
				);
				
				$new_custom_fields_array = $this->get_new_custom_fields();
	
				if($new_custom_fields_array && count($new_custom_fields_array) > 0)
				{
					foreach($new_custom_fields_array as $field_name => $field_array)
						$this->checkout_fields[$field_name] = $field_array;
				}

				$this->update_checkout_settings();
				
				add_action('woocommerce_settings_tabs', array(&$this, 'add_settings_tab'), 10);
				add_action( 'woocommerce_settings_tabs_' . $this->tab_name, array( $this, 'create_settings_page' ) );
				add_action( 'woocommerce_update_options_' . $this->tab_name, array( $this, 'save_settings_page' ) );

				//custom non-default field functions
				//add_action('woocommerce_checkout_process', array(&$this, 'custom_checkout_field_process'));
				add_action('woocommerce_checkout_update_order_meta', array(&$this, 'custom_checkout_field_update_order_meta'));
				
				//update My Account order details page
				add_action('woocommerce_order_details_after_order_table', array(&$this, 'custom_order_details_after_order_table' ) );
				
				//add custom fields to the customer email
				add_filter('woocommerce_email_order_meta_keys', array(&$this, 'custom_email_order_meta_keys') );
			}
			
			
			/**
			 * Add a tab to the woocommerce settings page
			 */
			public function add_settings_tab() {
				foreach ( $this->settings_tabs as $name => $label ) :
		        	$class = 'nav-tab';
		      		if ( $this->current_tab == $name )
		            	$class .= ' nav-tab-active';
		      		echo '<a href="' . admin_url('admin.php?page=wc-settings&tab=' . $name) . '" class="' . $class . '">' . $label . '</a>';
		     	endforeach;
			}

			
			/**
			 * Create the settings page content
			 */
			public function create_settings_page() {
			?>
				<br /><br />
				<?php if( isset($_GET['action']) && $_GET['action'] == 'delete' && $_GET['saved'] == "" ) { ?>
				    <div id="message" class="updated">
				        <p><strong><?php _e('Custom field has been deleted.') ?></strong></p>
				    </div>
				<?php } ?>
				<h3><?php _e( 'Customize Checkout Options', 'woocommerce-custom-checkout-options' ); ?></h3>
				
				<table class="wc_payment widefat" cellspacing="0">
					<tbody>
						<thead>
							<th width="1%">Enable</th>
							<th>Position</th>
							<th>Type</th>
							<th>Label</th>
							<th>Field Name</th>
							<th>Placeholder</th>
							<th>Class</th>
							<th>Options</th>
							<th>Order</th>
							<th>Required</th>
							<th>Default</th>
						</thead>
						<tbody class="ui-sortable">
						<?php 
							//check saved option and use default if the field not set
							$preload_option = get_option(WooCommerce_Custom_Checkout_Options::$plugin_prefix .'preload');
							if( $preload_option && $preload_option != "")
							{
								$this->first_load = false;
							}
							else
							{
								add_option( WooCommerce_Custom_Checkout_Options::$plugin_prefix .'preload', true );
								$this->first_load = true;
							}
							
							foreach($this->checkout_fields as $field_name => $arrayFieldRow):

							if($this->first_load)
							{
								$arrayOptions = $this->get_setting_field($field_name, $arrayFieldRow);
								
								$enabled 		= $arrayOptions['enabled'];
								$type 			= $arrayOptions['type'];
								$label 			= $arrayOptions['label'];
								$placeholder 	= $arrayOptions['placeholder'];
								$class 			= $arrayOptions['class'];
								$label_class 	= $arrayOptions['label_class'];
								$required 		= $arrayOptions['required'];
								$default 		= $arrayOptions['default'];
								$options 		= $arrayOptions['options'];
								$order 			= $arrayOptions['order'];
								$section 		= $arrayOptions['section'];
							}
							else
							{
								$enabled 		= $this->get_setting($field_name, 'enabled');
								$type 			= $this->get_setting($field_name, 'type');
								$label 			= $this->get_setting($field_name, 'label');
								$placeholder 	= $this->get_setting($field_name, 'placeholder');
								$class 			= $this->get_setting($field_name, 'class');
								$label_class 	= $this->get_setting($field_name, 'label_class');
								$required 		= $this->get_setting($field_name, 'required');
								$default 		= $this->get_setting($field_name, 'default');
								$options 		= $this->get_setting($field_name, 'options');
								$order 			= $this->get_setting($field_name, 'order');
								$section 		= $this->get_setting($field_name, 'section');
							}
							
							
						?>
							<tr>
								<td align="center">
									<input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_enabled" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_enabled" type="hidden" value="0" />
									<input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_enabled" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_enabled" type="checkbox" value="1" <?php checked( $enabled, true );?> />
								</td>
								<td>
									<select name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_section" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_section">
										<?php foreach($this->position_types as $position_type => $position_name): ?>
											<?php if($position_type == $section): ?>
												<option selected="selected" value="<?php echo $position_type?>"><?php echo $position_name; ?></option>
											<?php else: ?>
												<option value="<?php echo $position_type?>"><?php echo $position_name; ?></option>
											<?php endif; ?>
											
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<select name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_type" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_type">
										<?php foreach($this->field_types as $field_type => $type_name): ?>
											<?php if($field_type == $type): ?>
												<option selected="selected" value="<?php echo $field_type?>"><?php echo $type_name; ?></option>
											<?php else: ?>
												<option value="<?php echo $field_type?>"><?php echo $type_name; ?></option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
								</td>
								<td><input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_label" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_label" type="text" value="<?php echo $label; ?>" /></td>
								<td>
									<input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_field_name" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_field_name" type="hidden" value="<?php echo $field_name; ?>" />
									<input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_field_name" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_field_name" type="text" value="<?php echo $field_name; ?>" disabled="disabled" />
								</td>
								<td><input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_placeholder" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_placeholder" type="text" value="<?php echo $placeholder; ?>" /></td>
								<td><input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_class" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_class" type="text" value="<?php echo $class; ?>" size="12" /></td>
								<td><input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_options" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_options" type="text" value="<?php echo $options; ?>" size="12" /></td>
								<td><input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_order" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_order" type="text" value="<?php echo $order; ?>" size="6" /></td>
								<td align="center">
									<input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_required" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_required" type="hidden" value="0"  />
									<?php if($field_name == 'billing_state' || $field_name == 'shipping_state'): ?>
									<input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_required" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_required" type="checkbox" value="1" <?php checked( $required, true );?> disabled="disabled" />
									<?php else: ?>
									<input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_required" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_required" type="checkbox" value="1" <?php checked( $required, true );?> />
									<?php endif; ?>
								</td>
								<td align="center">
									<?php if($default): ?>
										<input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_default" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_default" type="hidden" value="1"  />
										<input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_default" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_default" type="checkbox" value="<?php echo $default; ?>" <?php checked( $default, true );?> disabled="disabled" />
									<?php else: ?>
										<input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_default" id="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?><?php echo $field_name; ?>_default" type="hidden" value="0"  />
										<a href="?page=woocommerce&tab=custom-checkout-options&option=<?php echo $field_name; ?>&action=delete"><input id="removeField" type="button" value="X" title="remove this record"></a>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						<tr>
							<td colspan="10">
							
							<input id="addRow" type="button" value="Add New Field" class="button-secondary" />

							<table id="block" class="wc_payment widefat" cellspacing="0">
							<thead>
								<th width="1%">Enable</th>
								<th>Position</th>
								<th>Type</th>
								<th>Label</th>
								<th>Field Name</th>
								<th>Placeholder</th>
								<th>Class</th>
								<th>Options</th>
								<th>Order</th>
								<th>Required</th>
								<th>Action</th>
							</thead>
							<tbody class="ui-sortable">
							
							</table>
							
							<script>
							jQuery(document).ready(function(){
								 var newRow = '\
									 <tr>\
										<td align="center">\
											<select name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?>newfield[enabled][]">\
												<?php foreach($this->yes_no as $choice => $choice_name): ?>
													<option value="<?php echo $choice?>"><?php echo $choice_name; ?></option>\
												<?php endforeach; ?>
											</select>\
										</td>\
										<td>\
											<select name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?>newfield[section][]">\
												<?php foreach($this->position_types as $position_type => $position_name): ?>
													<option value="<?php echo $position_type?>"><?php echo $position_name; ?></option>\
												<?php endforeach; ?>
											</select>\
										</td>\
										<td>\
											<select name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?>newfield[type][]">\
												<?php foreach($this->field_types as $field_type => $type_name): ?>
													<option value="<?php echo $field_type?>"><?php echo $type_name; ?></option>\
												<?php endforeach; ?>
											</select>\
										</td>\
										<td><input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?>newfield[label][]" type="text" value="New Field Label" /></td>\
										<td>\
											<input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?>newfield[field_name][]" type="hidden" value="newfield" />\
											<input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?>newfield[field_name][]" type="text" value="newfield" disabled="disabled" />\
										</td>\
										<td><input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?>newfield[placeholder][]" type="text" value="New Field" /></td>\
										<td><input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?>newfield[class][]" type="text" value="form-row-wide" size="12" /></td>\
										<td><input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?>newfield[options][]" type="text" value="" size="12" /></td>\
										<td><input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?>newfield[order][]" type="text" value="" size="6" /></td>\
										<td align="center">\
											<select name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?>newfield[required][]">\
												<?php foreach($this->yes_no as $choice => $choice_name): ?>
													<option value="<?php echo $choice?>"><?php echo $choice_name; ?></option>\
												<?php endforeach; ?>
											</select>\
										</td>\
										<td align="center">\
											<input name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?>newfield[default][]" type="hidden" value="0" />\
											<input id="removeRow" type="button" value="X" title="remove this row">\
										</td>\
									</tr>';

								 jQuery('#addRow').click(function(){
							      jQuery('#block').append(newRow);
								  reIndex();
								 })

								 jQuery('#removeRow').live('click', function(){
							      jQuery(this).closest('tr').remove();
								  reIndex();
								 })

								 function reIndex(){
								   jQuery('#block').find('.index').each(function(i){
								   jQuery(this).html(i+2);
								  })
								 }

								})
								</script>

				    
							</td>
						</tr>
						<tr><td colspan="10"><input type="hidden" name="<?php echo WooCommerce_Custom_Checkout_Options::$plugin_prefix; ?>submit" value="submitted"></td></tr>
						</tbody>
				</table>
				<?php
			}
			
			
			/**
			 * Get the content for an option
			 */
			public function get_setting( $field_name, $name ) {
				return get_option( WooCommerce_Custom_Checkout_Options::$plugin_prefix . $field_name . '_' . $name);
			}
			
			
			/**
			 * Get the array for an options to show in the form
			 */
			public function get_setting_field( $field_name, $array_options )
			{
				$array_result = array();
				$boolDefault = false;
				
				if($field_name != "" && is_array($array_options) && count($array_options) > 0)
				{
					foreach($array_options as $column_name => $valueField)
					{
						$option = get_option(WooCommerce_Custom_Checkout_Options::$plugin_prefix . $field_name . '_' . $column_name);
						
						if(!$option)
						{
							update_option(WooCommerce_Custom_Checkout_Options::$plugin_prefix . $field_name . '_' . $column_name, $valueField);
							
							$boolDefault = true;
						}
						
						$array_result[$column_name] = get_option(WooCommerce_Custom_Checkout_Options::$plugin_prefix . $field_name . '_' . $column_name);;
					}
				}
				
				return $array_result;
			}
			
			/**
			 * Get the array for the custom fields
			 */
			public function get_new_custom_fields()
			{
				$array_result = array();

				//get latest new field index
				//$latest_index = $this->get_latest_newfield_index();
				
				for($i = 0; $i < $this->max_limit_field; $i++)
				{
					$field_name = $this->custom_field_name.$i;

					$type = $this->get_setting($field_name, 'type');
					if($type && $type != "")
					{
						$array_result[$field_name] = array();
						
						foreach($this->field_options as $field_option)
						{
							$value_field = $this->get_setting($field_name, $field_option);
							
							$array_result[$field_name][$field_option] = $value_field;
						}
					}
				}
				
				return $array_result;
			}
			
			/**
			 * get latest index for newfield
			 */
			public function get_latest_newfield_index()
			{
				$index = 0;
				
				for($i = 0; $i < $this->max_limit_field; $i++)
				{
					$option = get_option(WooCommerce_Custom_Checkout_Options::$plugin_prefix . $this->custom_field_name . $i . '_type');
					
					if(!$option)
					{
						return $i;
					}
				}

				return $index;
			}
			
			
			/**
			 * Save all settings
			 */
			public function save_settings_page() {
				if ( isset( $_POST[ $this->hidden_submit ] ) && $_POST[ $this->hidden_submit ] == 'submitted' ) {
				
				//to check new added custom fields
				$newfield = WooCommerce_Custom_Checkout_Options::$plugin_prefix . 'newfield';
				$field_name = 'newfield';
				
				
				if(isset( $_POST[$newfield] ) && $_POST[$newfield] && count($_POST[$newfield]) > 0)
				{
					$total_index = count($_POST[$newfield]['type']);

					if($total_index > 0)
					{
						for($i = 0; $i < $total_index; $i++)
						{
							$latest_index = $this->get_latest_newfield_index();
							
							foreach($this->field_options as $field_option)
							{
								if(isset($_POST[$newfield][$field_option][$i]))
								{
									$valueField = $_POST[$newfield][$field_option][$i];
										
									$option = get_option(WooCommerce_Custom_Checkout_Options::$plugin_prefix . $field_name . $latest_index . '_' . $field_option);
									
									if(!$option)
									{
										add_option(WooCommerce_Custom_Checkout_Options::$plugin_prefix . $field_name . $latest_index . '_' . $field_option, $valueField);
									}
								}
							}
						}
					}
				}
				
				foreach ( $_POST as $key => $value ) {
						if ( $key != $this->hidden_submit && strpos( $key, WooCommerce_Custom_Checkout_Options::$plugin_prefix ) !== false ) {
							if ( get_option( $key ) != $value ) {
								update_option( $key, $value );
							}	
						}
					}
				}
				
			}
			
			
			/**
			 * Update checkout settings
			 */
			public function update_checkout_settings() {
				
				$sort_order = 1;
				
				$this->enabled_billing = array();
				$this->disabled_billing = array();
				$this->order_billing = array();
				
				$this->enabled_shipping = array();
				$this->disabled_shipping = array();
				$this->order_shipping = array();
				
				$this->enabled_account = array();
				$this->disabled_account = array();
				$this->order_account = array();
				
				$this->enabled_order = array();
				$this->disabled_order = array();
				$this->order_order = array();
				
				//loop the checkout fields
				foreach($this->checkout_fields as $field_name => $arrayFieldRow) {

					$enabled 		= $this->get_setting($field_name, 'enabled');
					$type 			= $this->get_setting($field_name, 'type');
					$label 			= $this->get_setting($field_name, 'label');
					$placeholder 	= $this->get_setting($field_name, 'placeholder');
					$class 			= $this->get_setting($field_name, 'class');
					$label_class 	= $this->get_setting($field_name, 'label_class');
					$required 		= $this->get_setting($field_name, 'required');
					$default 		= $this->get_setting($field_name, 'default');
					$options 		= $this->get_setting($field_name, 'options');
					$section 		= $this->get_setting($field_name, 'section');
					$order 			= $this->get_setting($field_name, 'order') ? $this->get_setting($field_name, 'order') + $this->max_limit_field : $sort_order;
					
					$sort_order++;
					
					//filter
					$class = explode(",", $class);
					$label_class = explode(",", $label_class);
					
					if($type == 'date')
					{
						$type = 'text';
						$class[] = 'datepicker';
					}
					
					if($type == 'select' && !is_null($options))
					{
						$array_options = explode(",", $options);
						$options = array();
						foreach($array_options as $value)
						{
							$options[$value] = $value;
						}
					}
					
					if($field_name == 'billing_state' || $field_name == 'shipping_state')
					{
						$field_name_array = array(
								'name'			=>	$field_name,
								'type'      	=> 	$type,
								'label'         => __($label ,$this->tab_name),
								'placeholder'  	=> __($placeholder,$this->tab_name),
								'class'   		=> $class,
								'label_class'   => $label_class,
								'default'     	=> $default,
								'options'		=> $options
						);
					}
					else
					{
						$field_name_array = array(
								'name'			=>	$field_name,
								'type'      	=> 	$type,
								'label'         => __($label ,$this->tab_name),
								'placeholder'  	=> __($placeholder,$this->tab_name),
								'class'   		=> $class,
								'label_class'   => $label_class,
								'required'      => $required,
								'default'     	=> $default,
								'options'		=> $options
						);
					}

					if($default)
					{
					
						if($section == 'billing')
						{
							if($enabled)
							{
								$this->enabled_billing[$field_name] = $field_name_array;
								$this->order_billing[$field_name] = $order;
							}
							else
							{
								$this->disabled_billing[] = $field_name;
							}
						}
						else if($section == 'shipping')
						{
							if($enabled)
							{
								$this->enabled_shipping[$field_name] = $field_name_array;
								$this->order_shipping[$field_name] = $order;
							}
							else
							{
								$this->disabled_shipping[] = $field_name;
							}
						}
						else if($section == 'account')
						{
							if($enabled)
							{
								$this->enabled_account[$field_name] = $field_name_array;
								$this->order_account[$field_name] = $order;
							}
							else
							{
								$this->disabled_account[] = $field_name;
							}
						}
						else
						{
							if($enabled)
							{
								$this->enabled_order[$field_name] = $field_name_array;
								$this->order_order[$field_name] = $order;
							}
							else
							{
								$this->disabled_order[] = $field_name;
							}
						}	
						
					}
					else
					{
						if($enabled)
						{
							if($section == 'billing')
							{
								$this->enabled_billing[$field_name] = $field_name_array;
								
								$this->order_billing[$field_name] = $order;
							}
							else if($section == 'shipping')
							{
								$this->enabled_shipping[$field_name] = $field_name_array;
								
								$this->order_shipping[$field_name] = $order;
							}
							else if($section == 'account')
							{
								$this->enabled_account[$field_name] = $field_name_array;
								
								$this->order_account[$field_name] = $order;
							}
							else
							{
								$this->enabled_order[$field_name] = $field_name_array;
								
								$this->order_order[$field_name] = $order;
							}
							$this->enabled_array[$field_name] = $field_name_array;
						}
					}
				}
				
				//echo 'before $this->order_billing :'.nl2br(print_r($this->order_billing, true)).'<br />';
				//uasort($this->order_billing, array( &$this, 'compare' ));
				//uasort($this->order_shipping, array( &$this, 'compare' ));
				//uasort($this->order_account, array( &$this, 'compare' ));
				//uasort($this->order_order, array( &$this, 'compare' ));
				
				asort($this->order_billing, SORT_NUMERIC);
				asort($this->order_shipping, SORT_NUMERIC);
				asort($this->order_account, SORT_NUMERIC);
				asort($this->order_order, SORT_NUMERIC);
				
				//echo 'after $this->order_billing :'.nl2br(print_r($this->order_billing, true)).'<br />';
				
				add_filter( 'woocommerce_checkout_fields' , array(&$this, 'custom_checkout_fields') );
				
			}
			
			
			/*public function compare($value1, $value2) {
				if ($value1 == $value2) {
					return 0;
				}

				return ($value1 < $value2) ? -1 : 1;
			}*/
			
			
			/**
			 * set checkout fields and unset disabled fields from the checkout form
			 */
			public function custom_checkout_fields( $fields ) {
				
				//Step 1 : reset [section] details
				$section_array = array('billing', 'shipping', 'account', 'order');
				
				$preload_option = get_option(WooCommerce_Custom_Checkout_Options::$plugin_prefix .'preload');
				if($preload_option)	
					unset($fields);
				
				foreach($section_array as $section_name)
				{
					$enabled_string = 'enabled_'.$section_name;
					$disabled_string = 'disabled_'.$section_name;
					$order_string = 'order_'.$section_name;
					
					foreach($this->$order_string as $field_name => $sort_order)
					{
						$enabled_field = $this->$enabled_string;
						
						$array_fields = $enabled_field[$field_name];
						
						if(count($array_fields) > 0)
						{
							foreach($array_fields as $index_name => $value_field) 
							{
								if(in_array($index_name, $this->allowed_fields))
								{
									$fields[$section_name][$field_name][$index_name] = $value_field;
								}
							}
							
							//insert parameter clear = true option if form-row-last and form-row-wide
							if(isset($fields[$section_name][$field_name]['class']))
							{
								if(!in_array("form-row-first", $fields[$section_name][$field_name]['class']))
									$fields[$section_name][$field_name]['clear'] = true;
							}
						}
					}
					
					
					//Step 1(b) : unset / remove the [section] field form checkout form
					foreach($this->$disabled_string as $field_name)
						unset($fields[$section_name][$field_name]);
				}
				
				return $fields;
			}
			
			
			/**
			 * add custom validation for extra fields(required)
			 */
			public function custom_checkout_field_process() {
			    global $woocommerce;
			    
			    $array_custom_fields = $this->enabled_array;
			 
			    foreach($array_custom_fields as $field_name => $array_fields)
			    {
			    	$label = $array_fields['label'] ? $array_fields['label'] : $field_name;
			    	
			    	if($array_fields['required'])
			    		$field_name_required = true; 
			    	else
			    		$field_name_required = false;
			    	
				    // Check if set, if its not set add an error.
				    if (!$_POST[$field_name] && $field_name_required)
				         $woocommerce->add_error( __($label.' is a required field.') );
			    }
			}
			
			
			/**
			 * Update the order meta with custom field value
			 **/
			public function custom_checkout_field_update_order_meta( $order_id ) {
				
				$array_custom_fields = $this->enabled_array;
				foreach($array_custom_fields as $field_name => $array_fields)
				{
					$label = $array_fields['label'] ? $array_fields['label'] : '_'.$field_name;
					$type = $array_fields['type'];

					if ($_POST[$field_name])
					{
						if($type == "checkbox")
						{
							$_POST[$field_name] = $_POST[$field_name] ? 'Yes' : 'No';
							update_post_meta( $order_id, $label, esc_attr($_POST[$field_name]));
						}
						else
						{
							update_post_meta( $order_id, $label, esc_attr($_POST[$field_name]));
						}
					}
					else
					{
						if($type == "checkbox")
						{
							$_POST[$field_name] = $_POST[$field_name] ? 'Yes' : 'No';
							update_post_meta( $order_id, $label, esc_attr($_POST[$field_name]));
						}
					}
				}
				
			}
			
			
			/**
			 * Add custom order data to order details page
			 **/
			public function custom_order_details_after_order_table ( $order )
			{
				$custom_fields = array();
				$html = "";
				$new_custom_fields_array = $this->get_new_custom_fields();
				$boolNewFields = false;
				
				if($new_custom_fields_array && count($new_custom_fields_array) > 0)
				{
					$boolNewFields = true;
					foreach($new_custom_fields_array as $field_name => $field_array)
					{
						if($field_array)
							$label_field = $field_array['label'];
						else
							$label_field = '_' . $field_name;
						
						$custom_fields[$field_name] = $label_field;
					}
				}
				
				if($boolNewFields)
				{
					//$html .= "<h2>Custom Order Data</h2>";
					$html .= "<dl class=\"customer_details\">";
					
					foreach($custom_fields as $field => $label)
					{
						$field_value = "";
						
						if(get_post_meta($order->id, '_' . $field, true))
							$field_value = get_post_meta($order->id, '_' . $field, true);
						else
							$field_value = get_post_meta($order->id, $label, true);
						
						$html .= '<dt>'.$label.'</dt>';
						$html .= '<dd>'.$field_value.'</dd>';
					}
					$html .= "</dl>";
						
					echo $html;
				}
				
				
			}
			
			
			/**
			 * Add the custom field to order emails
			 **/
			public function custom_email_order_meta_keys( $keys ) {
				$custom_fields = array();
				$new_custom_fields_array = $this->get_new_custom_fields();
				
				if($new_custom_fields_array && count($new_custom_fields_array) > 0)
				{
					foreach($new_custom_fields_array as $field_name => $field_array)
					{
						if($field_array)
							$label_field = $field_array['label'];

						$custom_fields[$field_name] = $label_field;
					}
				}
				
				foreach($custom_fields as $field => $label)
					$keys[] = $label;
					//$keys[] = '_' . $field;
				
				return $keys;
			}

	
		}
	}

	/* 
	 * Instantiate plugin class and add it to the set of globals.
	 */
	$woocommerce_custom_checkout_options = new WooCommerce_Custom_Checkout_Options();
}
else{
	add_action('admin_notices', 'wc_custom_checkout_options_notice');
	function wc_custom_checkout_options_notice(){
		global $current_screen;
		if($current_screen->parent_base == 'plugins'){
			echo '<div class="error"><p>For your information, '.__(wc_custom_checkout_options_plugin_name.' requires <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> to be installed and activated. Please install and activate <a href="'.admin_url('plugin-install.php?tab=search&type=term&s=WooCommerce').'" target="_blank">WooCommerce</a> first.').'</p></div>';
		}
	}
}
?>