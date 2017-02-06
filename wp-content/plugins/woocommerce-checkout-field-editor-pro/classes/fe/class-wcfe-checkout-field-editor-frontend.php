<?php
if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Checkout_Field_Editor_Frontend')):

class WCFE_Checkout_Field_Editor_Frontend extends WCFE_Checkout_Fields_Utils {
	public $sections_extra = array();
	public $options_extra = array();
	public $advanced_settings = array();
	
	public function __construct(){
		$advanced_settings = $this->get_advanced_settings();
		
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		
		//Show Custome Fields in Checkout Page
		add_action('woocommerce_checkout_before_customer_details', array($this, 'woo_checkout_before_customer_details'));
		add_action('woocommerce_checkout_after_customer_details', array($this, 'woo_checkout_after_customer_details'));
		
		add_action('woocommerce_before_checkout_billing_form', array($this, 'woo_before_checkout_billing_form'));
		add_action('woocommerce_after_checkout_billing_form', array($this, 'woo_after_checkout_billing_form'));
		
		add_action('woocommerce_before_checkout_shipping_form', array($this, 'woo_before_checkout_shipping_form'));
		add_action('woocommerce_after_checkout_shipping_form', array($this, 'woo_after_checkout_shipping_form'));
		
		add_action('woocommerce_before_checkout_registration_form', array($this, 'woo_before_checkout_registration_form'));
		add_action('woocommerce_after_checkout_registration_form', array($this, 'woo_after_checkout_registration_form'));
		
		add_action('woocommerce_before_order_notes', array($this, 'woo_before_order_notes'));
		add_action('woocommerce_after_order_notes', array($this, 'woo_after_order_notes'));
		
		add_filter('woocommerce_enable_order_notes_field', array($this, 'woo_enable_order_notes_field'), 1000);
		
		// Checkout init
		add_filter('woocommerce_checkout_fields', array($this, 'woo_checkout_fields'), 1000);
		
		//Checkout Process(Validate checkout fields, save user meta and save order meta
		add_action('woocommerce_checkout_process', array($this, 'woo_checkout_process'));
		add_action('woocommerce_after_checkout_validation', array($this, 'woo_checkout_fields_validation')); 
		add_action('woocommerce_checkout_update_user_meta', array($this, 'woo_checkout_update_user_meta'), 10, 2); 
		add_action('woocommerce_checkout_update_order_meta', array($this, 'woo_checkout_update_order_meta'), 10, 2); 
		
		//Show in Order Completed Page
		add_action('woocommerce_order_details_after_customer_details', array($this, 'woo_order_details_after_customer_details'), 20, 1);
		
		//Show in Email
		if($this->get_setting_value($advanced_settings, 'custom_fields_position_email') === 'woocommerce_email_customer_details_fields' ){
			add_filter('woocommerce_email_customer_details_fields', array($this, 'woo_display_custom_fields_in_emails'), 10, 3);
		}else{
			add_filter('woocommerce_email_order_meta_fields', array($this, 'woo_display_custom_fields_in_emails'), 10, 3);
		}
		
		// Define address formats
		// woocommerce_localisation_address_formats (WC_Countries)
		// woocommerce_formatted_address_force_country_display
		// woocommerce_formatted_address_replacements
		
		
		//Check Cart Items
		
		//Create Order
		//woocommerce_checkout_update_user_meta
		//woocommerce_checkout_update_order_meta
		//add_action('woocommerce_checkout_update_order_meta', array($this, 'save_order_meta'), 10, 2);
		

		add_filter('woocommerce_billing_fields', array($this, 'woo_billing_fields'), 1000);
		add_filter('woocommerce_shipping_fields', array($this, 'woo_shipping_fields'), 1000);
		add_filter('woocommerce_default_address_fields', array($this, 'woo_default_address_fields'), 1000);
		
		add_action('wp_ajax_thwcfe_calculate_extra_cost', array($this, 'thwcfe_calculate_extra_cost'), 10);
    	add_action('wp_ajax_nopriv_thwcfe_calculate_extra_cost', array($this, 'thwcfe_calculate_extra_cost'), 10);
		add_action('woocommerce_cart_calculate_fees', array($this, 'woo_cart_calculate_fees') );
		
		
		add_filter('woocommerce_form_field_hidden', array($this, 'woo_form_field_hidden'), 10, 4);
		add_filter('woocommerce_form_field_heading', array($this, 'woo_form_field_heading'), 10, 4);
		add_filter('woocommerce_form_field_label', array($this, 'woo_form_field_label'), 10, 4);
		add_filter('woocommerce_form_field_textarea', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_checkbox', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_checkboxgroup', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_password', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_text', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_email', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_tel', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_number', array($this, 'woo_form_field'), 10, 4);		
		add_filter('woocommerce_form_field_select', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_multiselect', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_radio', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_datepicker', array($this, 'woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_timepicker', array($this, 'woo_form_field'), 10, 4);
		
		if($this->get_setting_value($advanced_settings, 'enable_conditions_country') === 'yes'){
			add_filter('woocommerce_form_field_country', array($this, 'woo_form_field'), 10, 4);
		}
		if($this->get_setting_value($advanced_settings, 'enable_conditions_state') === 'yes'){
			add_filter('woocommerce_form_field_state', array($this, 'woo_form_field'), 10, 4);
		}
		
		// Deprecated
		//add_filter('woocommerce_email_order_meta_keys', array($this, 'woo_display_custom_fields_in_emails'), 10, 1);
		
		//Custom user meta data
		add_filter( 'woocommerce_checkout_get_value', array($this, 'woo_checkout_get_value'), 10, 2 );
		add_action( 'woocommerce_edit_account_form', array($this, 'woo_edit_account_form') );
		add_action( 'woocommerce_save_account_details', array($this, 'woo_save_account_details') );
	}
	
	public function enqueue_scripts(){	
		global $wp_scripts;

		if(is_checkout()){
			$jquery_version = isset($wp_scripts->registered['jquery-ui-core']->ver) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
			
			wp_register_script('wcfe-timepicker', TH_WCFE_ASSETS_URL.'js/timepicker/jquery.timepicker.min.js', array('jquery'), '1.0.1');
			wp_register_script('jquery-ui-i18n', '//ajax.googleapis.com/ajax/libs/jqueryui/'.$jquery_version.'/i18n/jquery-ui-i18n.min.js',
			array('jquery','jquery-ui-datepicker'), true);
			wp_register_script('wcfe-field-editor-script', TH_WCFE_ASSETS_URL.'js/thwcfe-checkout-field-editor-frontend.js', array('jquery-ui-i18n'), true);
			
			wp_enqueue_script('wcfe-timepicker');						
			wp_enqueue_script('wcfe-field-editor-script');	
			
			wp_enqueue_style('wcfe-timepicker-style', TH_WCFE_ASSETS_URL.'js/timepicker/jquery.timepicker.css');
			wp_enqueue_style('jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/'. $jquery_version .'/themes/smoothness/jquery-ui.css');
		
			$data = array(
				'lang' => array( 
							'am' => $this->__wcfe('am'), 
							'pm' => $this->__wcfe('pm'),  
							'AM' => $this->__wcfe('AM'), 
							'PM' => $this->__wcfe('PM'),
							'decimal' => $this->__wcfe('.'), 
							'mins' => $this->__wcfe('mins'), 
							'hr'   => $this->__wcfe('hr'), 
							'hrs'  => $this->__wcfe('hrs'),
						),
				'language' 	  => $this->get_locale_code(),
				'date_format' => $this->get_jquery_date_format(wc_date_format()),
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
			);

			wp_localize_script('wcfe-field-editor-script', 'wcfe_checkout_fields', $data);
		}
	}
	
	public function is_local_field( $field_name ){
		if($field_name && in_array($field_name, array(
			'billing_country', 'billing_state', 'billing_city', 'billing_postcode',
			'shipping_country', 'shipping_state', 'shipping_city', 'shipping_postcode',
			'order_comments'
		))){
			return true;
		}
		return false;
	}
	
	public function get_product_categories($product_id){
		$categories = array();
		$assigned_categories = wp_get_post_terms($product_id, 'product_cat');
		foreach($assigned_categories as $category){
			$parent_categories = get_ancestors( $category->term_id, 'product_cat' ); 
			if(is_array($parent_categories)){
				foreach($parent_categories as $pcat_id){
					$pcat = get_term( $pcat_id, 'product_cat' );
					$categories[] = $pcat->slug;
				}
			}
			
			$categories[] = $category->slug;
		}
		return $categories;
	}
	
	public function require_shipping_address($posted){
		if((WC()->cart->ship_to_billing_address_only() || !empty($posted['shiptobilling']) || 
			(!WC()->cart->needs_shipping() && get_option('woocommerce_require_shipping_address') === 'no'))){
			return false;
		}
		return true;
	}
	
	public function get_cart_summary(){
		$items = WC()->cart->get_cart();
		
		$cart = array();
		$cart['products'] = array();
		$cart['categories'] = array();
		
		foreach($items as $item => $values) { 
			$_product = $values['data']->post; 
			$cart['products'][] = $values['product_id'];
			$cart['categories'] = array_merge($cart['categories'], $this->get_product_categories($values['product_id']));
		} 
		
		$cart['products'] = array_values($cart['products']);
		$cart['categories'] = array_values($cart['categories']);
		
		return $cart;
	}
	
	public function get_fieldset($section){
		$cart       = $this->get_cart_summary();
		$products   = $cart['products'];
		$categories = $cart['categories'];
		
		$fieldset = array();
		if($this->is_valid_section($section) && $section->get_property('enabled')){
			$fieldset = $section->get_fieldset($products, $categories);
		}
		
		return !empty($fieldset) ? $fieldset : false;
	}
	
	public function get_checkout_fields_full(){
		$fields = array();
		
		$sections = $this->get_checkout_sections();	
		foreach($sections as $sname => $section){	
			$temp_fields = $section->get_fields();
			if($temp_fields && is_array($temp_fields)){
				$fields = array_merge($fields, $temp_fields);
			}
		}
		
		return $fields;
	}
	
	public function get_user_fields_full(){
		$user_fields = array();
		
		$sections = $this->get_checkout_sections();	
		foreach($sections as $sname => $section){	
			$fields = $section->get_fields();
			if($fields && is_array($fields)){
				foreach($fields as $key => $field){
					if($field->is_custom_field() && $field->is_enabled() && $field->get_property('user_meta')){
						$user_fields[$key] = $field;
					}
				}
			}
		}
		
		return $user_fields;
	}
	
	
   /*********************************************************
	******* DISPLAY DEFAULT SECTIONS & FIELDS - START *******
	*********************************************************/
	public function woo_checkout_fields( $checkout_fields ) {
		global $supress_field_modification;
		if($supress_field_modification){
			return $checkout_fields;
		}
		
		$sections = $this->get_checkout_sections();
		foreach($sections as $sname => $section) {
			$fieldset = $this->get_fieldset($section);
			$fieldset = $fieldset ? $fieldset : array();
			
			if(is_array($fieldset)){
				$sname = $sname === 'additional' ? 'order' : $sname;
				$checkout_fields[$sname] = $fieldset; //TODO merge instead repolacing existing fields to avoid losing any other non identified property
			}
		}
		
		return $checkout_fields;
	}
	
	public function woo_billing_fields($fields){
		global $supress_field_modification;
		if($supress_field_modification){
			return $fields;
		}
		
		$section = $this->get_checkout_section('billing');
		if($this->is_valid_section($section)){
			$fieldset = $this->get_fieldset($section);
			if($fieldset){
				$fields = $fieldset;
			}
		}
		
		//$fields = $this->prepare_checkout_fields($this->get_checkout_fields('billing'), $original);
		//$fields = $this->unset_conditionally_hidden_fields($fields);
				
		return $fields;
	}
	
	public function woo_shipping_fields($fields){
		global $supress_field_modification;
		if($supress_field_modification){
			return $fields;
		}
		
		$section = $this->get_checkout_section('shipping');
		if($this->is_valid_section($section)){
			$fieldset = $this->get_fieldset($section);
			if($fieldset){
				$fields = $fieldset;
			}
		}
				
		return $fields;
	}
	
	public function woo_default_address_fields($original){
		global $supress_field_modification;
		if($supress_field_modification){
			return $original;
		}
		
		$fields = $original;
		$section = $this->get_checkout_section('billing');
		if($this->is_valid_section($section)){
			$address_fields = $this->get_fieldset($section);
			
			foreach($original as $name => $values) {
				if($this->is_default_address_field($name)){
					//$new_field = $address_fields['billing_'.$name];
					
					//if(!( isset($new_field['enabled']) && $new_field['enabled'] == false )){
					$new_field = isset($address_fields['billing_'.$name]) ? $address_fields['billing_'.$name] : false;
					if($new_field && !( isset($new_field['enabled']) && $new_field['enabled'] == false )){
						if(isset($original['autocomplete'])){
							$new_field['autocomplete'] = $original['autocomplete'];
						}
						$fields[$name] = $new_field;
					}
				}
			}
		}
		
		return $fields;
	}
	
	/* Hide Additional Fields title if no fields available. */
	public function woo_enable_order_notes_field() {
		global $supress_field_modification;
		if($supress_field_modification){
			return $original;
		}
		
		$section = $this->get_checkout_section('additional');
		if($this->is_valid_section($section)){
			$fieldset = $this->get_fieldset($section);
			if($fieldset){
				$enabled = 0;
				foreach($fieldset as $field){
					if($field['enabled']){
						$enabled++;
					}
				}
				return $enabled > 0 ? true : false;
			}else{
				return false;
			}
		}
		return true;
	}
   /*********************************************************
	******* DISPLAY DEFAULT SECTIONS & FIELDS - END *********
	*********************************************************/
	
	
   /********************************************************
	******** DISPLAY CUSTOM SECTIONS & FIELDS - START ******
	********************************************************/
	public function get_custom_sections_by_hook($hook_name){
		$section_hook_map = $this->get_section_hook_map();
		
		$sections = false;
		if(is_array($section_hook_map) && isset($section_hook_map[$hook_name])){
			$sections = $section_hook_map[$hook_name];
		}	
						
		return empty($sections) ? false : $sections;
	}
	
	public function output_custom_section($sections, $checkout=false){
		if($sections && is_array($sections)){
			foreach($sections as $sname){
				$section = $this->get_checkout_section($sname);
				
				if($this->is_valid_section($section)){
					$fields = $this->get_fieldset($section);
					//$fields = $this->prepare_checkout_fields($fields);
					
					if(is_array($fields) && sizeof($fields) > 0){						
						if($section->is_show_title()){
							echo $section->get_title_html();
						}
						
						foreach($fields as $name => $field){
							if(!(isset($field['enabled']) && $field['enabled'] == false)) {
								if(is_object($checkout)){
									woocommerce_form_field($name, $field, $checkout->get_value($name));
								}else{
									woocommerce_form_field($name, $field);
								}
							}
						}
					}
				}
			}
		}
	}
		
	public function woo_before_checkout_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('before_checkout_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_after_checkout_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('after_checkout_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_checkout_before_customer_details() {
		$sections = $this->get_custom_sections_by_hook('before_customer_details');
		$this->output_custom_section($sections);	
	}
	public function woo_checkout_after_customer_details() {
		$this->output_disabled_field_names_hidden_field();
		
		$sections = $this->get_custom_sections_by_hook('after_customer_details');
		$this->output_custom_section($sections);	
	}
	public function woo_before_checkout_billing_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('before_checkout_billing_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_after_checkout_billing_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('after_checkout_billing_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_before_checkout_shipping_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('before_checkout_shipping_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_after_checkout_shipping_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('after_checkout_shipping_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_before_checkout_registration_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('before_checkout_registration_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_after_checkout_registration_form($checkout) {
		$sections = $this->get_custom_sections_by_hook('after_checkout_registration_form');
		$this->output_custom_section($sections, $checkout);	
	}
	public function woo_before_order_notes($checkout) {
		$sections = $this->get_custom_sections_by_hook('before_order_notes');
		$this->output_custom_section($sections, $checkout);	
	}
		
	public function woo_after_order_notes($checkout) {	
		$sections = $this->get_custom_sections_by_hook('after_order_notes');
		$this->output_custom_section($sections, $checkout);	
	}	
   /*********************************************************
	******** DISPLAY CUSTOM SECTIONS & FIELDS - END *********
	*********************************************************/
	
	
   /*******************************************
	******** CHECKOUT PROCESS - START *********
	*******************************************/
	// Prepare Checkout Fields
	public function woo_checkout_process(){
		$disabled_fields = isset( $_POST['thwcfe_disabled_fields'] ) ? wc_clean( $_POST['thwcfe_disabled_fields'] ) : '';
		
		if($disabled_fields){
			$dis_fields = explode(",", $disabled_fields);
			
			if(is_array($dis_fields) && !empty($dis_fields)){
				$checkout_fields = WC()->checkout->checkout_fields;
				$modified = false;
				
				if(is_array($checkout_fields)){
					foreach($checkout_fields as $fieldset_key => $fieldset) {
						foreach($dis_fields as $fname){
							if(isset($fieldset[$fname])){
								unset($checkout_fields[$fieldset_key][$fname]);
								$modified = true;
							}
						}
					}
				}
				
				if($modified){
					WC()->checkout->checkout_fields = $checkout_fields;
				}
			}
		}
	}
	
	// Validate Checkout Fields
	public function woo_checkout_fields_validation($posted){
		$checkout_fields = WC()->checkout->checkout_fields;
		
		foreach($checkout_fields as $fieldset_key => $fieldset){
			if($fieldset_key == 'shipping' && ($posted['ship_to_different_address'] == false || ! WC()->cart->needs_shipping_address())){
				continue;
			}
				
			foreach($fieldset as $key => $field) {
				//Fix for checkbox field required validation issue				
				if(isset($field['custom']) && $field['custom'] && isset($field['type']) && $field['type'] === 'checkbox'){	
					if(isset($field['required']) && $field['required'] && ( !isset($posted[$key]) || !$posted[$key]) ){
						wc_add_notice( apply_filters( 'woocommerce_checkout_required_field_notice', sprintf( _x( '%s is a required field.', 'FIELDNAME is a required field.', 'woocommerce' ), '<strong>' . $field['label'] . '</strong>' ), $field['label'] ), 'error' );
					}
				}
			
				if(isset($posted[$key]) && !empty($posted[$key])){
					$value = $posted[$key];
					$validate = isset($field['validate']) ? $field['validate'] : '';
					
					if(is_array($validate) && !empty($validate)){
						foreach($validate as $rule){
							switch($rule) {
								case 'number' :
									if(!is_numeric($value)){
										$err_msg = '<strong>'. $this->__wcfe($field['label']) .'</strong> '. $this->__wcfe( 'is not a valid number.' );							
										$this->wcfe_add_error($err_msg);
									}
									break;
								default:
									$custom_validators = $this->get_settings('custom_validators');
									$validator = is_array($custom_validators) && isset($custom_validators[$rule]) ? $custom_validators[$rule] : false;
									if(is_array($validator)){
										$pattern = $validator['pattern'];
										
										if(preg_match($pattern, $value) === 0) {
											$err_msg = sprintf( $this->__wcfe( $validator['message'] ), $this->__wcfe($field['label']) );
											$this->wcfe_add_error($err_msg);
										}
										break;
									}
							}
						}
					}
				}
			}
		}
	}
	
	// Save User Meta
	public function woo_checkout_update_user_meta($customer_id, $posted){
		$checkout_fields = WC()->checkout->checkout_fields;
		
		foreach($checkout_fields as $fieldset_key => $fieldset){
			if($fieldset_key === 'shipping' && !WC()->cart->needs_shipping()){
				continue;
			}
			
			foreach($fieldset as $key => $field) {
				if(isset($field['custom']) && $field['custom'] && isset($posted[$key]) && !empty($posted[$key])){	
					if(isset($field['user_meta']) && $field['user_meta']){
						$value  = $posted[$key];
						$fvalue = $field['default'];
						
						if($field['type'] === 'checkbox' && !empty($fvalue)){
							$value = $value == 1 ? $fvalue : ''; 
						}
						
						update_user_meta($customer_id, $key, $value );
					}
				}
			}
		}
	}
	
	// Save Order Meta
	public function woo_checkout_update_order_meta($order_id, $posted){
		$checkout_fields = WC()->checkout->checkout_fields;
		
		foreach($checkout_fields as $fieldset_key => $fieldset){
			if($fieldset_key === 'shipping' && ($posted['ship_to_different_address'] == false || !WC()->cart->needs_shipping_address())){
				continue;
			}
			
			foreach($fieldset as $key => $field) {
				if(isset($field['custom']) && $field['custom'] && isset($posted[$key]) && !empty($posted[$key])){	
					if(isset($field['order_meta']) && $field['order_meta']){
						$value  = $posted[$key];
						$fvalue = $field['default'];
						
						if($field['type'] === 'checkbox' && !empty($fvalue)){
							$value = $value == 1 ? $fvalue : ''; 
							/*$values = explode("|", $fvalue);
							if(is_array($values)){
								$true_val = isset($values[0]) ? $values[0] : '';
								$false_val = isset($values[1]) ? $values[1] : '';
								$value = $value == 1 ? $true_val : $false_val; 
							}*/
						}
						
						update_post_meta($order_id, $key, $value);
					}
				}
			}
		}
	}
   /*******************************************
	******** CHECKOUT PROCESS - END ***********
	*******************************************/
	
	
   /*******************************************************
	******** DISPLAY CUSTOM FIELDS VALUES - START *********
	*******************************************************/
	// Display Custom Fields in Thank You Page
	public function woo_order_details_after_customer_details($order){
		//$needs_shipping_address = !wc_ship_to_billing_address_only() && $order->needs_shipping_address();
		$fieldset = $this->get_checkout_fields_full();
		
		foreach($fieldset as $key => $field) {
			if($this->is_valid_field($field) && $field->is_custom_field() && $field->get_property('show_in_order')){	
				$value = get_post_meta( $order->id, $key, true );
				$value = is_array($value) ? implode(",", $value) : $value;
				
				if(!empty($value)){
					$label = $field->get_property('title') ? $field->get_property('title') : $key;
					$label = $this->esc_attr__wcfe($label);
					
					echo '<tr><th>'. $label .':</th><td>'. $value .'</td></tr>';
				}
			}
		}
	}
	
	// Display Custom Fields in Completed Order(Customer Copy) email 
	public function woo_display_custom_fields_in_emails($ofields, $sent_to_admin, $order){
		$custom_fields = array();
		$fieldset = $this->get_checkout_fields_full();
		
		foreach($fieldset as $key => $field) {
			if($this->is_valid_field($field) && $field->is_custom_field() && $field->get_property('show_in_email')){	
				$value = get_post_meta( $order->id, $key, true );
				$value = is_array($value) ? implode(",", $value) : $value;
				
				if(!empty($value)){
					$label = $field->get_property('title') ? $field->get_property('title') : $key;
					$label = $this->esc_attr__wcfe($label);
					
					$custom_field = array();
					$custom_field['label'] = $label;
					$custom_field['value'] = $value;
					
					$custom_fields[$key] = $custom_field;
				}
			}
		}
		
		return array_merge($ofields, $custom_fields);
	}
   /*******************************************************
	******** DISPLAY CUSTOM FIELDS VALUES - END ***********
	*******************************************************/
	
	
	
	
	
	
	// Validate Checkout Fields
	/*public function woo_checkout_fields_validation($posted){
		$sections = $this->get_checkout_sections();	
		
		foreach($sections as $sname => $section){
			if($sname == 'shipping' && ($posted['ship_to_different_address'] == false || ! WC()->cart->needs_shipping_address())){
				continue;
			}
			
			if($this->is_valid_section($section)){
				$fieldset = $section->get_fields();
				
				foreach($fieldset as $key => $field) {
					if($this->is_valid_field($field) && $field->get_property('enabled') && isset($posted[$key])){
						$value = $posted[$key];
						$label = $this->__wcfe($field->get_property('title'));
						
						$validate = $field->get_property('validate');
						if(!empty($validate) && is_array($validate) && !empty($value)){
							foreach($validate as $rule){
								switch($rule) {
									case 'number' :
										if(!is_numeric($value)){
											$err_msg = '<strong>'. $label .'</strong> '. $this->__wcfe( 'is not a valid number.' );							
											$this->wcfe_add_error($err_msg);
										}
										break;
								}
							}
						}
					}
				}
			}
		}
	}*/
	
	// Save User Meta
	/*public function woo_checkout_update_user_meta($customer_id, $posted){
		$sections = $this->get_checkout_sections();
						
		foreach($sections as $sname => $section){
			if($sname === 'shipping' && !WC()->cart->needs_shipping()){
				continue;
			}
			
			if($this->is_valid_section($section)){
				$fieldset = $section->get_fields();
				
				foreach($fieldset as $key => $field) {
					//if($this->is_valid_field($field) && $field->is_enabled() && $field->show_field($product->id, $categories)){ //TODO
					if($this->is_valid_field($field) && $field->get_property('enabled') && isset($posted[$key])){
						$value = $posted[$key];
						
						$value = $this->remove_dummy_value($value); //TODO Remove this workaround
						
						if($value){
							$value  = is_array($value) ? implode(",", $value) : $value;
							$fvalue = $field->get_property('value');
							
							if($field->get_property('type') === 'checkbox' && !empty($fvalue) && $section->is_default_section()){
								$value = $value == 1 ? $fvalue : ''; 
							}
							
							update_user_meta($customer_id, $key, $value );
						}
					}
				}
			}
		}
	}*/
	
	// Save Order Meta
	/*public function woo_checkout_update_order_meta($order_id, $posted){
		$sections = $this->get_checkout_sections();
						
		foreach($sections as $sname => $section){
			if($sname == 'shipping' && ($posted['ship_to_different_address'] == false || !WC()->cart->needs_shipping_address())){
				continue;
			}
			
			if($this->is_valid_section($section)){
				$fieldset = $section->get_fields();
				
				foreach($fieldset as $key => $field) {
					//if($this->is_valid_field($field) && $field->is_enabled() && $field->show_field($product->id, $categories)){ //TODO
					if($this->is_valid_field($field) && $field->get_property('enabled') && isset($posted[$key])){
						$value = $posted[$key];
						
						$value = $this->remove_dummy_value($value); //TODO Remove this workaround
						
						if($value){
							$value  = is_array($value) ? implode(",", $value) : $value;
							$fvalue = $field->get_property('value');
							
							if($field->get_property('type') === 'checkbox' && !empty($fvalue) && $section->is_default_section()){
								$value = $value == 1 ? $fvalue : ''; 
							}
							
							update_post_meta($order_id, $field_name, $value);
						}
					}
				}
			}
		}
	}*/
   
	
	
	
	/*public function get_checkout_fields($section_name){
		$cart       = $this->get_cart_summary();
		$products   = $cart['products'];
		$categories = $cart['categories'];
		
		$checkout_fields = array();
		$section = $this->get_checkout_section($section_name);
		
		if($section){
			$fields = $section->get_fields();
			if($fields){
				foreach($fields as $field_name => $field){
					if($field->show_field($products, $categories)){
						$checkout_fields[$field_name] = $field->get_field_array();
					}
				}
			}
		}
		
		return !empty($checkout_fields) ? $checkout_fields : false;
	}*/
	
	
	
	/*public function prepare_checkout_fields($new_fields, $original = false){
		if(empty($new_fields))
			return $original;
			
		$fields = $new_fields;			
		foreach( $fields as $name => $values ) {
			if(isset($values['enabled']) && $values['enabled'] == false ){
				unset($fields[$name]);
			}

			// Replace locale field properties so they are unchanged
			if($this->is_local_field($name) && $original) {
				if ( isset( $fields[$name] ) ) {
					$fields[$name] = $original[$name];
					
					if(isset($new_fields[$name]['label']) && !empty($new_fields[$name]['label'])){
						$fields[$name]['label'] = $new_fields[$name]['label'];
					}

					if (!empty($new_fields[$name]['placeholder'])) {
						$fields[$name]['placeholder'] = $new_fields[$name]['placeholder'];

					} elseif (!empty($original[$name]['placeholder'])) {
						$fields[$name]['placeholder'] = $original[$name]['placeholder'];

					} else {
						$fields[$name]['placeholder'] = '';
					}
					
					if(isset($fields[$name]['class'])){
						$fields[$name]['class'] = $new_fields[$name]['class'];
					}
					
					if(isset($fields[$name]['clear'])){
						$fields[$name]['clear'] = $new_fields[$name]['clear'];
					}
				}
			}
			
			if(isset($fields[$name])){	
				if(isset($fields[$name]['label'])){					
					$fields[$name]['label'] = $this->__wcfe($fields[$name]['label']);	
				}
				
				if(isset($fields[$name]['placeholder'])){		
					$fields[$name]['placeholder'] = $this->__wcfe($fields[$name]['placeholder']);
				}
				
				if(isset($fields[$name]['value'])){
					$fields[$name]['value'] = $this->__wcfe($fields[$name]['value']);
				}
			}
		}
		return $fields;
	}*/
	
	/***************************************************************
	******** CUSTOM VALIDATIONS & SAVE ORDER META DATA - START ****
	***************************************************************/	
	/*public function woo_checkout_fields_validation($posted){
		$sections = $this->get_checkout_sections();
		
		//$err_msg = "DEBUG DATA: ".print_r($posted, true)." :END";												
		//$this->wcfe_add_error($err_msg);
		
		if($sections && is_array($sections)){
			foreach($sections as $sname => $section){
				// Skip shipping if its not needed
				if($sname === 'shipping' && !$this->require_shipping_address($posted)){
					continue;
				}
							
				if($this->is_valid_section($section)){
					$fields = $section->get_fields();
					if($fields){
						foreach($fields as $name => $field){	
							$is_custom_section = $section->is_custom_section();
							$label = $this->__wcfe($field->get_title());
							
							$value = isset($posted[$name]) ? wc_clean($posted[$name]) : false;					
							if(!$value){
								$value = isset($_POST[$name]) ? $_POST[$name] : '';
							}
							
							if($name === 'accept'){
								/*if ( isset( $field->required ) && $field->required && ( ! isset( $this->posted[ $name ] ) || "" === $this->posted[ $name ] ) ) {
									switch ( $sname ) {
										case 'shipping' :
											$field_label = sprintf( _x( 'Shipping %s', 'Shipping FIELDNAME', 'woocommerce' ), $field->label );
										break;
										case 'billing' :
											$field_label = sprintf( _x( 'Billing %s', 'Billing FIELDNAME', 'woocommerce' ), $field->label );
											//$field_label = $name;
										break;
										default :
											$field_label = $field->label;
										break;
									}
									//wc_add_notice( apply_filters( 'woocommerce_checkout_required_field_notice', sprintf( _x( '%s is a required field.', 'FIELDNAME is a required field.', 'woocommerce' ), '<strong>' . $field_label . '</strong>' ), $field_label ), 'error' );
								}*/
								
								/*$fieldsss = WC()->countries->get_address_fields( 'IN', 'billing_' );
							
								$err_msg = "DEBUG DATA: ".print_r($fieldsss, true)." :END";												
								$this->wcfe_add_error($err_msg);*/
							/*}
																									
							if($is_custom_section && $field->is_enabled()){	
								if(isset($_POST[$name]) && $field->is_required() && empty($value)){
									$err_msg = '<strong>'. $label .'</strong> ' . $this->__wcfe( 'is a required field.' );												
									$this->wcfe_add_error($err_msg);				
								}
							}else if( $field->get_type() === 'checkbox' && $field->is_enabled() && $field->is_custom_field() ){
								if( $field->is_required() && 0 === $posted[ $name ] ) {
									switch ( $sname ) {
										case 'shipping' :
											$field_label = sprintf( _x( 'Shipping %s', 'Shipping FIELDNAME', 'woocommerce' ), $field->get_title() );
										break;
										case 'billing' :
											$field_label = sprintf( _x( 'Billing %s', 'Billing FIELDNAME', 'woocommerce' ), $field->get_title() );
										break;
										default :
											$field_label = $field->get_title();
										break;
									}
									wc_add_notice( apply_filters( 'woocommerce_checkout_required_field_notice', sprintf( _x( '%s is a required field.', 'FIELDNAME is a required field.', 'woocommerce' ), '<strong>' . $field_label . '</strong>' ), $field_label ), 'error' );
								}
							}
							
							$value = $this->remove_dummy_value($value);
							
							$validator = $field->get_validator_arr();
							if(!empty($validator) && is_array($validator) && !empty($value)){
								foreach($validator as $rule){
									// Default validators email, phone, state, postcode
									switch($rule) {
										case 'number' :
											if(!is_numeric($value)){
												$err_msg = '<strong>'. $label .'</strong> '. $this->__wcfe( 'is not a valid number.' );							
												$this->wcfe_add_error($err_msg);
											}
											break;
										case 'email' :
											if(!is_email($value) && $is_custom_section){
												$err_msg = '<strong>'. $label .'</strong> '. $this->__wcfe( 'is not a valid email address.' );
												$this->wcfe_add_error($err_msg);
											}
											break;
									}
								}
							}
						}
					}
				}
			}
		}
	}*/
	
	/*public function disable_validations(){
		$field_name = '';
		$sections = $this->get_checkout_sections();
		
		if($sections && is_array($sections)){
			foreach($sections as $sname => $section){
				// Skip shipping if its not needed
				if($sname === 'shipping' && !$this->require_shipping_address($posted)){
					continue;
				}
							
				if($this->is_valid_section($section)){
					$fields = $section->get_fields();
					if($fields){
						foreach($fields as $name => $field){	
							if($name === $field_name){
								$field[] = array();
							}
						}
					}
				}
			}
		}
	}*/
	/*public function disable_field_callback(){
		$field_name = $_POST['fname'] ;
		$disabled_fields = $this->get_disabled_fields_from_session();
		
		if($field_name){
			$disabled_fields[] = $field_name;
			$this->save_disabled_fields_in_session($disabled_fields);
		}
	}
	public function enable_field_callback(){
		$field_name = $_POST['fname'] ;
		$disabled_fields = $this->get_disabled_fields_from_session();
		
		if($field_name){
			unset($disabled_fields[$field_name]);
			$this->save_disabled_fields_in_session($disabled_fields);
		}
	}
	public function save_disabled_fields_in_session($disabled_fields) {
		if(!isset($_SESSION)){
			session_start();
		}
		$this->clear_disabled_fields_from_session();
		$_SESSION['thwcfe-disabled-fields'] = $disabled_fields;
	}
	public function get_disabled_fields_from_session() {
		if(!isset($_SESSION)){
			session_start();
		}
    	$disabled_fields = isset($_SESSION['thwcfe-disabled-fields']) ? $_SESSION['thwcfe-disabled-fields'] : false;
		return is_array($disabled_fields) ? $disabled_fields : array();
	}
	public function clear_disabled_fields_from_session() {
		unset($_SESSION['thwcfe-disabled-fields']);
	}*/
	
	/*public function save_order_meta($order_id, $posted){
		$sections = $this->get_checkout_sections();
						
		foreach($sections as $sname => $section){
			if($sname === 'shipping' && !(isset($posted['ship_to_different_address']) && $posted['ship_to_different_address'])){
				continue;
			}
			
			$fields = $section->get_fields();					
			if($fields){
				foreach($fields as $field_name => $field){
					//if($this->is_valid_field($field) && $field->is_enabled() && $field->is_enabled() && $field->show_field($product->id, $categories)){ //TODO
					if($this->is_valid_field($field) && $field->is_enabled()){
						$value = empty($posted[$field_name]) ? false : wc_clean($posted[$field_name]);
						$value = (!$value && isset($_POST[$field_name])) ? $_POST[$field_name] : $value;
						
						$value = $this->remove_dummy_value($value);
						
						if($value){
							$value = is_array($value) ? implode(",", $value) : $value;
							$type = $field->get_type();
							$field_value = $field->get_value();
							
							if($type === 'checkbox' && !empty($field_value) && $section->is_default_section()){
								$value = $value == 1 ? $field_value : ''; 
							}
							update_post_meta($order_id, $field_name, $value);
						}
					}
				}
			}
		}
	}*/
   /***************************************************************
	******** CUSTOM VALIDATIONS & SAVE ORDER META DATA - END ******
	***************************************************************/	
	
   /***********************************************
	******** CUSTOM CHECKOUT FIELDS - START *******
	******** Billing, Shipping & Additional *******
	***********************************************/
	
	/*public function merge_field_props( $a1, $a2 ) {
		foreach ( $a2 as $k => $v ) {
			$a1[$k] = $v;
		
		
			if ( ! array_key_exists( $k, $a2 ) ) {
				continue;
			}
			if ( is_array( $v ) && is_array( $a2[ $k ] ) ) {
				$a1[ $k ] = wc_array_overlay( $v, $a2[ $k ] );
			} else {
				$a1[ $k ] = $a2[ $k ];
			}
		}
		return $a1;
	}*/

	
	
	
	
	
	
	/*public function woo_checkout_fields( $fields ) {
		global $supress_field_modification;
		if($supress_field_modification){
			return $fields;
		}

		if($additional_fields = $this->get_checkout_fields('additional')){
			$fields['order'] = $additional_fields + $fields['order'];

			// check if order_comments is enabled/disabled
			if(isset($additional_fields['order_comments']['enabled']) && !$additional_fields['order_comments']['enabled']){
				unset($fields['order']['order_comments']);
			}
		}
		
		if(isset($fields['account']) && is_array($fields['account'])){
			foreach( $fields['account'] as $name => $values ) {
				$fields['account'][ $name ]['label'] 	   = $this->__wcfe($fields['account'][ $name ]['label']);
				$fields['account'][ $name ]['placeholder'] = $this->__wcfe($fields['account'][ $name ]['placeholder']);
				
				if(isset($fields['account'][ $name ]['value'])){
					$fields['account'][ $name ]['value'] = $this->__wcfe($fields['account'][ $name ]['value']);
				}
			}
			
			$fields['account'] = $this->unset_conditionally_hidden_fields($fields['account']);
		}
		
		if(isset($fields['order']) && is_array($fields['order'])){
			foreach( $fields['order'] as $name => $values ) {
				if(isset($values['enabled']) && $values['enabled'] == false ) {
					unset( $fields['order'][ $name ] );
				}else{
					if(isset($fields['order'][ $name ]['label'])){
						$fields['order'][ $name ]['label'] = $this->__wcfe($fields['order'][ $name ]['label']);	
					}
								
					if(isset($fields['order'][ $name ]['placeholder'])){
						$fields['order'][ $name ]['placeholder'] = $this->__wcfe($fields['order'][ $name ]['placeholder']);	
					}
					
					if(isset($fields['order'][ $name ]['value'])){
						$fields['order'][ $name ]['value'] = $this->__wcfe($fields['order'][ $name ]['value']);
					}
				}		
			}
			
			$fields['order'] = $this->unset_conditionally_hidden_fields($fields['order']);
		}
		
		return $fields;
	}*/
	
	/*public function unset_conditionally_hidden_fields($fields){
		foreach($fields as $fname => $field){
			if(isset($_POST[$fname]) && $_POST[$fname] === 'mockvalthXXX'){
				unset($fields[$fname]);
			}
		}
		return $fields;
	}
	public function remove_dummy_value($value){
		if($value == "mockvalthXXX"){
			$value = '';
		}
		return $value;
	}*/
	
	
	
   /**********************************************
	******** CUSTOM CHECKOUT FIELDS - END ********
	**********************************************/
	public function save_extra_cost_in_session($price_info) {
		if(!isset($_SESSION)){
			session_start();
		}
		$this->clear_extra_cost_info_from_session();
		$_SESSION['thwcfe-extra-cost-info'] = $price_info;
	}
	
	public function get_extra_cost_from_session() {
		if(!isset($_SESSION)){
			session_start();
		}
    	$extra_cost = isset($_SESSION['thwcfe-extra-cost-info']) ? $_SESSION['thwcfe-extra-cost-info'] : false;
		return is_array($extra_cost) ? $extra_cost : array();
	}
	
	public function clear_extra_cost_info_from_session() {
		unset($_SESSION['thwcfe-extra-cost-info']);
	}
	
	public function thwcfe_calculate_extra_cost() {
		$price_info_json = isset($_POST['price_info']) ? stripslashes($_POST['price_info']) : '';
		
		if($price_info_json) {
			$price_info = json_decode($price_info_json, true);
			$this->save_extra_cost_in_session($price_info);
		}else{
			$this->clear_extra_cost_info_from_session();
		}
	}
	
	public function calculate_extra_cost($price_info){
		$fprice = 0;
		$price_type = isset($price_info['price_type']) ? $price_info['price_type'] : '';
		$price 		= isset($price_info['price']) ? $price_info['price'] : 0;
		$multiple   = isset($price_info['multiple']) ? $price_info['multiple'] : 0;
		
		global $woocommerce;
		$cart_total = $woocommerce->cart->cart_contents_total; //$woocommerce->cart->get_cart_total();
		
		if($multiple == 1){
			$price_arr = explode(",", $price);
			$price_type_arr = explode(",", $price_type);
			
			foreach($price_arr as $index => $oprice){
				$oprice_type = isset($price_type_arr[$index]) ? $price_type_arr[$index] : 'normal';
				
				if($oprice_type === 'percentage'){
					if(is_numeric($oprice) && is_numeric($cart_total)){
						$fprice = $fprice + ($oprice/100)*$cart_total;
					}
				}else{
					if(is_numeric($oprice)){
						$fprice = $fprice + $oprice;
					}
				}	
			}
		}else{
			if($price_type === 'percentage'){
				if(is_numeric($price) && is_numeric($cart_total)){
					$fprice = ($price/100)*$cart_total;
				}
			}else{
				if(is_numeric($price)){
					$fprice = $price;
				}
			}
		}
		return $fprice;
	}
	
	public function woo_cart_calculate_fees(){
		if(is_checkout()){
			global $woocommerce;
			$extra_cost = $this->get_extra_cost_from_session();
			
			foreach($extra_cost as $name => $price_info){
				$fee = $this->calculate_extra_cost($price_info);
				$woocommerce->cart->add_fee($price_info['label'], $fee);
			}
		}
	}
	
   
	
   /****************************************
	******** CUSTOM FIELD TYPES - START ****
	****************************************/	
	public function output_disabled_field_names_hidden_field(){
		echo '<input type="hidden" id="thwcfe_disabled_fields" name="thwcfe_disabled_fields" value=""/>';
	}
	
	public function prepare_price_data_string($args){
		$price_info = '';
		if( isset($args['price']) && !empty($args['price']) ){
			$label = !empty($args['title']) ? $this->__wcfe($args['title']) : $args['name'];
			$price_info = 'data-price="'.$args['price'].'" data-price-type="'.$args['price_type'].'" data-price-label="'.esc_attr($label).'"';
		}
		return $price_info;
	}
	
	public function prepare_price_data_option_string($args){
		$price_info = '';
		if( isset($args['price']) && !empty($args['price']) ){
			$price_info = 'data-price="'.$args['price'].'" data-price-type="'.$args['price_type'].'"';
		}
		return $price_info;
	}
	
	public function woo_form_field_hidden($field = '', $key, $args, $value){
		$price_info = $this->prepare_price_data_string($args);
		
		$css_class = array();
		if( isset($args['price']) && !empty($args['price']) ){
			$css_class[] = 'thwcfe-price-field';
		}
		
		if(is_null($value)){
            $value = $args['default'];
        }
		
		$rules = '';
		$rules_action = '';
		if(isset($args['rules']) && !empty($args['rules'])){
			$rules_action = isset($args['rules_action']) ? $args['rules_action'] : 'show';
			$rules = urldecode($args['rules']);
			$rules = esc_js($rules);
			$css_class[] = 'thwcfe-conditional-field';
		}

		$field  = '<input type="hidden" name="'. esc_attr($key) .'" value="'. esc_attr( $value ) .'" class="'.esc_attr(implode(' ', $css_class)).'" '.$price_info.' ';
		$field .= 'data-rules="'.$rules.'" data-rules-action="'.$rules_action.'" />';
		return $field;
	}
	
	public function woo_form_field_heading($field = '', $key, $args, $value){
		//$field = '<h3 class="form-row '.esc_attr(implode(' ', $args['class'])).'" id="'.esc_attr($key).'_field">'. $this->__wcfe($args['label']) .'</h3>';
		$rules = '';
		$rules_action = '';
		if(isset($args['rules']) && !empty($args['rules'])){
			$rules_action = isset($args['rules_action']) ? $args['rules_action'] : 'show';
			$rules = urldecode($args['rules']);
			$rules = esc_js($rules);
			$args['class'][] = 'thwcfe-conditional-field';
		}
		$data_rules = 'data-rules="'.$rules.'" data-rules-action="'.$rules_action.'"';
		
		$title_html = $this->get_title_html($args);
		$field  = '';
		if(!empty($title_html)){
			$field .= '<div class="form-row '.esc_attr(implode(' ', $args['class'])).'" id="'.esc_attr($key).'_field" '.$data_rules.' >'. $title_html .'</div>';
		}
		return $field;
		
		//$field = $this->get_title_html($args);
		//return $field;
	}
	
	public function woo_form_field_label($field = '', $key, $args, $value){
		$rules = '';
		$rules_action = '';
		if(isset($args['rules']) && !empty($args['rules'])){
			$rules_action = isset($args['rules_action']) ? $args['rules_action'] : 'show';
			$rules = urldecode($args['rules']);
			$rules = esc_js($rules);
			$args['class'][] = 'thwcfe-conditional-field';
		}
		$data_rules = 'data-rules="'.$rules.'" data-rules-action="'.$rules_action.'"';
		
		$title_html = $this->get_title_html($args);
		$field  = '';
		if(!empty($title_html)){
			$field .= '<div class="form-row '.esc_attr(implode(' ', $args['class'])).'" id="'.esc_attr($key).'_field" '.$data_rules.' >'. $title_html .'</div>';
		}
		return $field;
	}
	
	public function get_title_html($args){
		$title_html = '';
		if(isset($args['label']) && !empty($args['label'])){
			$title_type  = $args['title_type'] ? $args['title_type'] : 'label';
			$title_style = $args['title_color'] ? 'style="display:block; color:'.$args['title_color'].';"' : 'display:block;';
			
			$title_html .= '<'. $title_type .' class="'. implode(' ', $args['label_class']) .'" '. $title_style .'>'. $this->__wcfe($args['label']) .'</'. $title_type .'>';
		}
		
		$subtitle_html = '';
		if(isset($args['subtitle']) && !empty($args['subtitle'])){
			$subtitle_type  = $args['subtitle_type'] ? $args['subtitle_type'] : 'span';
			$subtitle_style = $args['subtitle_color'] ? 'font-size:80%; style="color:'. $args['subtitle_color'] .';"' : 'font-size:80%;';
			$subtitle_class = is_array($args['subtitle_class']) ? implode(' ', $args['subtitle_class']) : $args['subtitle_class'];
			
			$subtitle_html .= '<'. $subtitle_type .' class="'. $subtitle_class .'" '. $subtitle_style .'>';
			$subtitle_html .= $this->__wcfe($args['subtitle']) .'</'. $subtitle_type .'>';
		}
		
		$html = $title_html;
		if(!empty($subtitle_html)){
			$html .= $subtitle_html;
		}
	
		return $html;
	}
	
	/**
     * Outputs a checkout/address form field.
     *
     * @subpackage  Forms
     * @param string $key
     * @param mixed $args
     * @param string $value (default: null)
     * @todo This function needs to be broken up in smaller pieces
     */
    public function woo_form_field($ofield = '', $key, $args, $value = null ) {
        $defaults = array(
            'type'              => 'text',
            'label'             => '',
            'description'       => '',
            'placeholder'       => '',
            'maxlength'         => false,
            'required'          => false,
            'id'                => $key,
            'class'             => array(),
            'label_class'       => array(),
            'input_class'       => array(),
            'return'            => false,
            'options'           => array(),
            'custom_attributes' => array(),
            'validate'          => array(),
            'default'           => '',
        );

        $args = wp_parse_args( $args, $defaults );
        $args = apply_filters( 'woocommerce_form_field_args', $args, $key, $value );
		
		/*if(isset($args['label'])){
			$args['label'] = $this->__wcfe($args['label']);
		}
		if(isset($args['description'])){
			$args['description'] = $this->__wcfe($args['description']);
		}
		if(isset($args['placeholder'])){
			$args['placeholder'] = $this->__wcfe($args['placeholder']);
		}*/
		
		$args['input_class'][] = 'thwcfe-input-field';
		$validations = array();
		
        if($args['required'] ) {
            $args['class'][] = 'validate-required';
			$validations[] = 'validate-required';
            $required = ' <abbr class="required" title="' . esc_attr__( 'required', 'woocommerce'  ) . '">*</abbr>';
        } else {
            $required = '';
        }
		
		if( is_numeric($args['maxlength']) ){
        	$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';
		}

        if(is_string($args['label_class'])) {
            $args['label_class'] = array( $args['label_class'] );
        }

        if(is_null($value)){
            $value = $args['default'];
        }

        // Custom attribute handling
        $custom_attributes = array();

        if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
            foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
                $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
            }
        }
		
		/*if(isset($args['value']) && !empty($args['value'])){
			$custom_attributes[] = 'data-default-value="' . $args['value'] . '"';
		}*/

        if ( ! empty( $args['validate'] ) ) {
            foreach( $args['validate'] as $validate ) {
                $args['class'][] = 'validate-' . $validate;
				//$validations[] = 'validate-' . $validate;
            }
        }
		
		$rules = '';
		$rules_action = '';
		if(isset($args['rules']) && !empty($args['rules'])){
			$rules_action = isset($args['rules_action']) ? $args['rules_action'] : 'show';
			$rules = urldecode($args['rules']);
			$rules = esc_js($rules);
			$args['class'][] = 'thwcfe-conditional-field';
		}

        $field = '';
        $label_id = $args['id'];
		$validations_str = implode(" ", $validations);
        $field_container = '<p class="form-row %1$s" id="%2$s" data-rules="'.$rules.'" data-rules-action="'.$rules_action.'" data-validations="'.$validations_str.'" >%3$s</p>';

        switch ( $args['type'] ) {
            case 'country' :
                $field .= $this->woo_form_field_fragment_country( $key, $args, $value, $custom_attributes );
                break;
				
            case 'state' :
				$field .= $this->woo_form_field_fragment_state( $key, $args, $value, $custom_attributes );
                break;
				
            case 'textarea' :
				$field .= $this->woo_form_field_fragment_textarea( $key, $args, $value, $custom_attributes );
                break;
				
            case 'checkbox' :
                $field = $this->woo_form_field_fragment_checkbox( $key, $args, $value, $custom_attributes, $required );
                break;
			
			case 'checkboxgroup' :
                $field = $this->woo_form_field_fragment_checkbox( $key, $args, $value, $custom_attributes, $required );
                break;	
				
            case 'password' :
            case 'text' :
            case 'email' :
            case 'tel' :
            case 'number' :
                $field .= $this->woo_form_field_fragment_general( $key, $args, $value, $custom_attributes );
                break;
				
            case 'select' :
				$field .= $this->woo_form_field_fragment_select( $key, $args, $value, $custom_attributes );
                break;
				
			case 'multiselect' :
				$field .= $this->woo_form_field_fragment_multiselect( $key, $args, $value, $custom_attributes );
                break;	
				
            case 'radio' :
				$label_id = current( array_keys( $args['options'] ) );
				$field .= $this->woo_form_field_fragment_radio( $key, $args, $value);
                break;
				
			case 'datepicker' :
				$field .= $this->woo_form_field_fragment_datepicker( $key, $args, $value, $custom_attributes );
                break;
				
			case 'timepicker' :
				$field .= $this->woo_form_field_fragment_timepicker( $key, $args, $value, $custom_attributes );
                break;
        }

        if ( ! empty( $field ) ) {
            $field_html = '';

            if ( $args['label'] && 'checkbox' != $args['type'] ) {
				$label = $this->__wcfe($args['label']);
                $field_html .= '<label for="'. esc_attr( $label_id ) .'" class="'. esc_attr(implode(' ', $args['label_class'])) .'">'. $label . $required .'</label>';
            }

            $field_html .= $field;

            if ( $args['description'] ) {
                $field_html .= '<span class="description">' . $this->__wcfe( esc_html($args['description']) ) . '</span>';
            }

            $container_class = 'form-row ' . esc_attr( implode( ' ', $args['class'] ) );
            $container_id = esc_attr( $args['id'] ) . '_field';

            $after = ! empty( $args['clear'] ) ? '<div class="clear"></div>' : '';
            $field = sprintf( $field_container, $container_class, $container_id, $field_html ) . $after;
			
			return $field;
        }

		return $ofield;
    }
	
	public function woo_form_field_fragment_country( $key, $args, $value, $custom_attributes ) { 
		$countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();
		$field = '';
		
		if ( 1 === sizeof( $countries ) ) {
			$field .= '<strong>' . current( array_values( $countries ) ) . '</strong>';
	
			$field .= '<input type="hidden" name="'. esc_attr( $key ) .'" id="'. esc_attr( $args['id'] ) .'" value="'. current( array_keys($countries ) ) .'" ';
			$field .= implode( ' ', $custom_attributes ) . ' class="country_to_state" />';
	
		} else {
			$field  = '<select name="'.esc_attr($key).'" id="'.esc_attr($args['id']).'" class="country_to_state country_select '.esc_attr(implode(' ', $args['input_class'])).'" ';
			$field .= implode( ' ', $custom_attributes ) . '><option value="">'.__( 'Select a country&hellip;', 'woocommerce' ) .'</option>';
	
			foreach ( $countries as $ckey => $cvalue ) {
				$field .= '<option value="' . esc_attr( $ckey ) . '" '. selected( $value, $ckey, false ) . '>'.__( $cvalue, 'woocommerce' ) .'</option>';
			}
	
			$field .= '</select>';
			$field .= '<noscript><input type="submit" name="woocommerce_checkout_update_totals" value="'. esc_attr__( 'Update country', 'woocommerce' ) .'" /></noscript>';
		}
		return $field;
	}
	
	public function woo_form_field_fragment_state( $key, $args, $value, $custom_attributes ) { 
		/* Get Country */
		$country_key = 'billing_state' === $key ? 'billing_country' : 'shipping_country';
		$current_cc  = WC()->checkout->get_value( $country_key );
		$states      = WC()->countries->get_states( $current_cc );
		
		$field = '';
		if ( is_array( $states ) && empty( $states ) ) {
			$field_container = '<p class="form-row %1$s" id="%2$s" style="display: none">%3$s</p>';

			$field .= '<input type="hidden" class="hidden" name="'. esc_attr($key) .'" id="'. esc_attr($args['id']) .'" value="" ';
			$field .= implode(' ', $custom_attributes) .' placeholder="'. esc_attr($args['placeholder']) .'" />';

		} elseif ( is_array( $states ) ) {
			$field .= '<select name="'. esc_attr( $key ) .'" id="'. esc_attr( $args['id'] ) .'" class="state_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" ';
			$field .= implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">';
			$field .= '<option value="">'.__( 'Select a state&hellip;', 'woocommerce' ) .'</option>';

			foreach ( $states as $ckey => $cvalue ) {
				$field .= '<option value="' . esc_attr( $ckey ) . '" '.selected( $value, $ckey, false ) .'>'.__( $cvalue, 'woocommerce' ) .'</option>';
			}

			$field .= '</select>';
		} else {
			$field .= '<input type="text" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" value="' . esc_attr( $value ) . '" ';
			$field .= 'placeholder="'. esc_attr($args['placeholder']) .'" name="'. esc_attr($key) .'" id="'. esc_attr($args['id']) .'" '. implode(' ', $custom_attributes) .' />';
		}
		return $field;
	}
	
	public function woo_form_field_fragment_textarea( $key, $args, $value, $custom_attributes ) {
		$price_info = $this->prepare_price_data_string($args);
		if( isset($args['price']) && !empty($args['price']) ){
			$args['input_class'][] = 'thwcfe-price-field';
		}
	
		$field  = '<textarea name="'. esc_attr($key) .'" class="input-text '. esc_attr(implode(' ', $args['input_class'])) .'" id="'. esc_attr($args['id']) .'" ';
		$field .= 'placeholder="'. esc_attr($args['placeholder']) .'" '. $args['maxlength'] .' ';
		$field .= (empty($args['custom_attributes']['rows']) ? ' rows="2"' : '');
		$field .= (empty($args['custom_attributes']['cols']) ? ' cols="5"' : '');
		$field .= implode(' ', $custom_attributes) .' '.$price_info.'>'. esc_textarea($value) .'</textarea>';
		
		return $field;
	}
	
	public function woo_form_field_fragment_checkbox( $key, $args, $value, $custom_attributes, $required ) {  
		$price_info = $this->prepare_price_data_string($args);
		if( isset($args['price']) && !empty($args['price']) ){
			$args['input_class'][] = 'thwcfe-price-field';
		}
		
		$args['default'] = !empty($args['default']) ? $args['default'] : 1;
		$checked = $args['checked'] ? 'checked' : '';
	
		$field  = '<label class="checkbox ' . implode( ' ', $args['label_class'] ) .'" ' . implode( ' ', $custom_attributes ) . '>';  
        $field .= '<input type="'. esc_attr($args['type']) .'" class="input-checkbox '. esc_attr(implode(' ', $args['input_class'])) .'" name="' . esc_attr( $key ) . '" '; 
		$field .= 'id="' .esc_attr($args['id']). '" value="'. $args['default'] .'" '. $checked .' '.$price_info.' /> '. $args['label'] . $required . '</label>';
		
		return $field;
	}
	
	public function woo_form_field_fragment_checkboxgroup( $key, $args, $value, $custom_attributes, $required ) {  
		$field = '';
		if(!empty($args['options_object'])) {
			foreach($args['options_object'] as $option) {
				$option_key = $option['key'];
				$option_text = $option['text'];
				
				$field .= '<input type="checkbox" class="input-checkbox '. esc_attr(implode(' ', $args['input_class'])) .'" value="'. esc_attr( $option_key ) .'" '; 
				$field .= 'name="'. esc_attr($key) .'" id="'. esc_attr($args['id']) .'_'. esc_attr($option_key) .'"'. checked($value, $option_key, false) .' />';
				$field .= '<label for="'. esc_attr($args['id']) .'_'. esc_attr($option_key) .'" class="checkbox '. implode(' ', $args['label_class']) .'">'.$option_text.'</label>';
			}
		}
		return $field;
	}
		
	public function woo_form_field_fragment_general( $key, $args, $value, $custom_attributes ) {
		$price_info = $this->prepare_price_data_string($args);
		if( isset($args['price']) && !empty($args['price']) ){
			$args['input_class'][] = 'thwcfe-price-field';
		}
		
		$field  = '<input type="' . esc_attr( $args['type'] ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" name="' . esc_attr( $key ) . '" '; 
		$field .= 'id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . $args['maxlength'] . ' value="' . esc_attr( $value ) . '" ';
		$field .= implode( ' ', $custom_attributes ) . ' '.$price_info.' />';
		
		return $field;
	}
	
	public function woo_form_field_fragment_select( $key, $args, $value, $custom_attributes ) { 
		$options = $field = '';
		
		if(!empty($args['options_object'])){
			$price_field = false;
			
			/*if(isset($args['placeholder']) && !empty( $args['placeholder'])){
				$options .= '<option disabled="">'. esc_attr( $args['placeholder'] ) .'</option>';
			}*/
			
			foreach($args['options_object'] as $option){
				$option_key = $option['key'];
				$option_text = $option['text'];
				
				$price_info = $this->prepare_price_data_option_string($option);
				if( isset($option['price']) && !empty($option['price']) ){
					$price_field = true;
				}
				
				if('' === $option_key){ // If we have a blank option, select2 needs a placeholder
					if(empty( $args['placeholder'])) {
						$args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'woocommerce' );
					}
					$custom_attributes[] = 'data-allow_clear="true"';
				}
				$options .= '<option value="'. esc_attr($option_key) .'" '. selected($value, $option_key, false) .' '.$price_info.' >'. esc_attr( $option_text ) .'</option>';
			}
			
			$price_data = '';
			if($price_field){
				$args['input_class'][] = 'thwcfe-price-field';
				$args['input_class'][] = 'thwcfe-price-option-field';
				
				$label = !empty($args['title']) ? $this->__wcfe($args['title']) : $args['name'];
				$price_data = 'data-price-label="'.esc_attr($label).'"';
			}

			$field .= '<select name="'.esc_attr($key).'" id="'.esc_attr($args['id']).'" class="select thwcfe-enhanced-select '.esc_attr(implode(' ', $args['input_class'])).'" '; 
			$field .= implode(' ', $custom_attributes) .' data-placeholder="'. esc_attr($args['placeholder']) .'" '.$price_data.'>'. $options .'</select>';
		}
		return $field;
	}
	
	public function woo_form_field_fragment_multiselect( $key, $args, $value, $custom_attributes ) { 
		$options = $field = '';

		if(!empty($args['options_object'])){
			$price_field = false;
			
			foreach($args['options_object'] as $option){
				$option_key = $option['key'];
				$option_text = $option['text'];
				
				$price_info = $this->prepare_price_data_option_string($option);
				if( isset($option['price']) && !empty($option['price']) ){
					$price_field = true;
				}
				
				if('' === $option_key){  // If we have a blank option, select2 needs a placeholder
					if(empty( $args['placeholder'])) {
						$args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'woocommerce' );
					}
					$custom_attributes[] = 'data-allow_clear="true"';
				}
				$options .= '<option value="'. esc_attr($option_key) .'" '. selected($value, $option_key, false) .' '.$price_info.' >'. esc_attr( $option_text ) .'</option>';
			}
			
			$price_data = '';
			if($price_field){
				$args['input_class'][] = 'thwcfe-price-field';
				$args['input_class'][] = 'thwcfe-price-option-field';
				
				$label = !empty($args['title']) ? $this->__wcfe($args['title']) : $args['name'];
				$price_data = 'data-price-label="'.esc_attr($label).'"';
			}

			$field .= '<select multiple="multiple" name="'. esc_attr($key) .'[]" id="'. esc_attr($args['id']) .'" '; 
			$field .= 'class="thwcfe-enhanced-multi-select '. esc_attr(implode(' ', $args['input_class'])) .'" ';
			$field .= implode(' ', $custom_attributes) .' data-placeholder="'. esc_attr($args['placeholder']) .'" '.$price_data.'>'. $options .'</select>';
		}
		return $field;
	}
	
	public function woo_form_field_fragment_radio( $key, $args, $value) { 
		$field = '';
		if(!empty($args['options_object'])) {
			foreach($args['options_object'] as $option) {
				$option_key = $option['key'];
				$option_text = $option['text'];
				
				$price_info = $this->prepare_price_data_option_string($option);
				$price_data = '';
				if( isset($option['price']) && !empty($option['price']) ){
					$args['input_class'][] = 'thwcfe-price-field';
					
					$label = !empty($args['title']) ? $this->__wcfe($args['title']) : $args['name'];
					$price_data = 'data-price-label="'.esc_attr($label).'"';
				}
				
				$field .= '<input type="radio" class="input-radio '. esc_attr(implode(' ', $args['input_class'])) .'" value="'. esc_attr( $option_key ) .'" '; 
				$field .= $price_info.' '.$price_data.' ';
				$field .= 'name="'. esc_attr($key) .'" id="'. esc_attr($args['id']) .'_'. esc_attr($option_key) .'"'. checked($value, $option_key, false) .' />';
				$field .= '<label for="'. esc_attr($args['id']) .'_'. esc_attr($option_key) .'" '; 
				$field .= 'class="radio '. implode(' ', $args['label_class']) .'" style="display:inline; margin-right: 10px;"> '. $option_text .'</label>';
				
				if(in_array("valign", $args['class'])){
					$field .= '<br/>';
				}
			}
		}
		return $field;
	}
	
	public function woo_form_field_fragment_datepicker( $key, $args, $value, $custom_attributes ) { 
		$price_info = $this->prepare_price_data_string($args);
		if( isset($args['price']) && !empty($args['price']) ){
			$args['input_class'][] = 'thwcfe-price-field';
		}
		
		$dateFormat = isset($args['date_format']) ? $args['date_format'] : $this->get_jquery_date_format(wc_date_format());	
		$defaultDate = isset($args['default_date']) ? $args['default_date'] : '';
		$maxDate = isset($args['max_date']) ? $args['max_date'] : '';
		$minDate = isset($args['min_date']) ? $args['min_date'] : '';
		$yearRange = isset($args['year_range']) ? $args['year_range'] : '-100:+1';
		$numberOfMonths = isset($args['number_months']) ? $args['number_months'] : 1; 
		$disabledDays = isset($args['disabled_days']) ? $args['disabled_days'] : '';
		$disabledDates = isset($args['disabled_dates']) ? $args['disabled_dates'] : '';
				
		$field  = '<input type="text" class="thwcfe-checkout-date-picker input-text '. esc_attr(implode(' ', $args['input_class'])) .'" name="'. esc_attr($key) .'" '; 
		$field .= 'id="'. esc_attr($args['id']) .'" placeholder="'. esc_attr($args['placeholder']) .'" '. $args['maxlength'] .' value="'. esc_attr($value) .'" ';
		$field .= implode(' ', $custom_attributes) .' '.$price_info.' ';
		$field .= 'data-date-format="'. $dateFormat .'" data-default-date="'. $defaultDate .'" data-max-date="'. $maxDate .'" data-min-date="'. $minDate .'" ';
		$field .= 'data-year-range="'. $yearRange .'" data-number-months="'. $numberOfMonths .'" ';
		$field .= 'data-disabled-days="'. $disabledDays .'" data-disabled-dates="'. $disabledDates .'" />';
		
		return $field;
	}
	
	public function woo_form_field_fragment_timepicker( $key, $args, $value, $custom_attributes ) { 
		$price_info = $this->prepare_price_data_string($args);
		if( isset($args['price']) && !empty($args['price']) ){
			$args['input_class'][] = 'thwcfe-price-field';
		}
		
		$args['min_time']  = isset($args['min_time']) ? $args['min_time'] : '';
		$args['max_time']  = isset($args['max_time']) ? $args['max_time'] : '';
		$args['time_step'] = isset($args['time_step']) ? $args['time_step'] : '';
		$args['time_format'] = isset($args['time_format']) ? $args['time_format'] : '';
		
		$field  = '<input type="text" class="thwcfe-checkout-time-picker input-text '. esc_attr(implode(' ', $args['input_class'])) .'" name="'. esc_attr($key) .'" '; 
		$field .= 'id="'. esc_attr($args['id']) .'" placeholder="'. esc_attr($args['placeholder']) .'" '. $args['maxlength'] .' value="'. esc_attr($value) .'" ';
		$field .= implode(' ', $custom_attributes) .' '.$price_info.' ';
		$field .= 'data-min-time="'.$args['min_time'].'" data-max-time="'.$args['max_time'].'" data-step="'.$args['time_step'].'" data-format="'.$args['time_format'].'" />';
		
		return $field;
	}
	
   /****************************************
	******** CUSTOM FIELD TYPES - END ******
	****************************************/
	

   /***********************************************************
	******** DISPLAY & SAVE CUSTOM USER META FIELDS - START ***
	***********************************************************/
	public function woo_checkout_get_value($value, $key){
		$user_fields = $this->get_user_fields_full();
		
		if(is_user_logged_in() && is_array( $user_fields ) && array_key_exists( $key, $user_fields )) {
			$current_user = wp_get_current_user();

			if($meta = get_user_meta( $current_user->ID, $key, true )){
				return $meta;
			}
		}
		
		return $value;
	}
	
	public function woo_edit_account_form() {
	  	$user_id = get_current_user_id();
	  	$user = get_userdata( $user_id );
	 
	  	if ( !$user )
			return;
			
		$html = '';
			
		$sections = $this->get_checkout_sections();
		foreach($sections as $sname => $section) {
			$fieldset = $this->get_fieldset($section);
			
			if($fieldset){
				foreach($fieldset as $key => $field) {
					if(isset($field['custom']) && $field['custom'] && isset($field['user_meta']) && $field['user_meta']){	
						$value = get_user_meta( $user_id, $key, true );
						$cssclass = 'woocommerce-Input woocommerce-Input--text input-text';
						$required = '<span class="required">*</span>';
						
						$field_html  = '<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">';
						$field_html .= '<label for="'.$key.'">'. $field['label'] . $required .'</label>';
						$field_html .= '<input type="text" class="'. $cssclass .'" name="'. $key .'" id="'. $key .'" value="'. $value .'" />';
						$field_html .= '</p>';
						
						$html .= $field_html;
					}
				
					/*if(isset($posted[$key]) && !empty($posted[$key])){
						$value = $posted[$key];
						$validate = isset($field['validate']) ? $field['validate'] : '';
					}*/
				}
			}
		}

	 	if(!empty($html)){
			?>
	  		<fieldset>
				<?php /*?><legend><?php _e( 'Additional Information', 'woocommerce' ); ?></legend><?php */?>
                <?php echo $html; ?>
            </fieldset>
            <?php
		}
	}
	
	public function woo_save_account_details( $user_id ) {
		$sections = $this->get_checkout_sections();
		foreach($sections as $sname => $section) {
			$fieldset = $this->get_fieldset($section);
			
			if($fieldset){
				foreach($fieldset as $key => $field) {
					if(isset($field['custom']) && $field['custom'] && isset($field['user_meta']) && $field['user_meta']){	
						if(isset($_POST[ $key ])){
							update_user_meta( $user_id, $key, htmlentities( $_POST[ $key ] ) );
						}
					}
				}
			}
		}
	}
	
   /***********************************************************
	******** DISPLAY & SAVE CUSTOM USER META FIELDS - END *****
	***********************************************************/
	
	
	/**************************************************
	******** DISPLAY CUSTOM FIELDS & VALUES - START ***
	**************************************************/
	/*public function woo_display_custom_fields_in_emails($keys){
		$custom_keys = array();
		$fields = array();
		$sections = $this->get_checkout_sections();	
			
		foreach($sections as $sname => $section){	
			$temp_fields = $section->get_fields();
			if($temp_fields && is_array($temp_fields)){
				$fields = array_merge($fields, $temp_fields);
			}
		}

		foreach( $fields as $name => $field ) {
			if($this->is_valid_field($field) && $field->is_show_in_email() && $field->is_custom_field()){	
				$label = $field->get_title() ? $field->get_title() : $name;
				$custom_keys[ $this->esc_attr__wcfe($label) ] = esc_attr($name);
			}
		}
		
		return array_merge($keys, $custom_keys);
	}*/
	
	
	
	/*public function woo_order_details_after_customer_details($order){
		$needs_shipping_address = !wc_ship_to_billing_address_only() && $order->needs_shipping_address();
		
		$fields = $this->get_checkout_fields_full();

		foreach($fields as $name => $field){	
			if($this->is_valid_field($field) && $field->get_property('show_in_order') && $field->get_property('custom_field')){	
				$value = get_post_meta( $order->id, $name, true );
				$value = is_array($value) ? implode(",", $value) : $value;
				
				if(!empty($value)){
					$label = $field->get_property('title') ? $field->get_property('title') : $name;
					$label = $this->esc_attr__wcfe($label);
					
					echo '<tr><th>'. $label .':</th><td>'. $value .'</td></tr>';
				}
			}
		}
	}*/
	
	/*public function woo_order_details_after_customer_details_1($order){
		$order_id = $order->id;
		$fields = array();
		$found = false;
		$html = '';
		$needs_shipping_address = !wc_ship_to_billing_address_only() && $order->needs_shipping_address();
		$sections = $this->get_checkout_sections();	
			
		if($sections && is_array($sections)){
			foreach($sections as $sname => $section){	
				//if(!($section_name === 'shipping' && !$needs_shipping_address)){
					$temp_fields = $section->get_fields();
					if($temp_fields && is_array($temp_fields)){
						$fields = array_merge($fields, $temp_fields);
					}
				//}			
			}
		}

		foreach($fields as $name => $field){	
			if($this->is_valid_field($field) && $field->is_show_in_order() && $field->is_custom_field()){	
				$value = get_post_meta( $order_id, $name, true );
				$value = is_array($value) ? implode(",", $value) : $value;
				
				if(!empty($value)){
					$label = $field->get_title() ? $field->get_title() : $name;
					$found = true;
					$html .= '<dt>'. $this->esc_attr__wcfe($label) .':</dt>';
					$html .= '<dd>'. $value .'</dd>';
				}
			}
		}
		
		if($found){
			echo '<dl>'. $html .'</dl>';			
		}
	}*/	
	
	
	
	/************************************************
	******** DISPLAY CUSTOM FIELDS & VALUES - END ***
	************************************************/
}

endif;