<?php 
if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Extra_Product_Options_Frontend')):

class WEPO_Extra_Product_Options_Frontend extends WEPO_Extra_Product_Options_Utils {
	public $sections_extra = array();
	public $options_extra = array();
	
	public function __construct(){
		/*if (session_status() == PHP_SESSION_NONE) { // PHP >= 5.4.0
			session_start();
		}*/
		if( !session_id()) session_start(); // PHP < 5.4.0
		
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		
		add_filter('woocommerce_loop_add_to_cart_link', array($this, 'woo_loop_add_to_cart_link'), 10, 2);
		
		add_action( 'woocommerce_before_single_product', array($this, 'woo_before_single_product') );
		
		add_action( 'woocommerce_before_add_to_cart_button', array($this, 'woo_before_add_to_cart_button'), 10, 3 );	
		add_action( 'woocommerce_after_add_to_cart_button', array($this, 'woo_after_add_to_cart_button'), 10, 3 );
		
		add_filter( 'woocommerce_add_to_cart_validation', array($this, 'woo_add_to_cart_validation'), 99, 3 );
		add_filter( 'woocommerce_add_cart_item_data', array($this, 'woo_add_cart_item_data'), 10, 2 );
		add_action( 'woocommerce_add_to_cart', array($this, 'woo_add_to_cart'), 1, 5 );
		add_action( 'woocommerce_before_calculate_totals', array($this, 'woo_before_calculate_totals'), 1, 1 );
		add_filter( 'woocommerce_get_item_data', array($this, 'woo_get_item_data'), 10, 2 );
		add_action( 'woocommerce_add_order_item_meta', array($this, 'woo_add_order_item_meta'), 1, 3 );
		add_filter( 'woocommerce_order_items_meta_get_formatted', array($this, 'woo_order_items_meta_get_formatted'), 10, 2 );
		
		add_action('wp_ajax_thwepo_calculate_extra_cost', array($this, 'calculate_extra_cost_handler'), 10);
    	add_action('wp_ajax_nopriv_thwepo_calculate_extra_cost', array($this, 'calculate_extra_cost_handler'), 10);
	}
	
	public function enqueue_scripts(){			
		global $wp_scripts;

		if(is_product()){
			$jquery_version = isset($wp_scripts->registered['jquery-ui-core']->ver) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
			
			wp_register_style('select2', TH_WEPO_WOO_ASSETS_URL.'/css/select2.css');

			wp_register_script('thwepo-timepicker-script', TH_WEPO_ASSETS_URL.'js/timepicker/jquery.timepicker.min.js', array('jquery'), '1.0.1');
			wp_register_script('jquery-ui-i18n', '//ajax.googleapis.com/ajax/libs/jqueryui/'.$jquery_version.'/i18n/jquery-ui-i18n.min.js',
			array('jquery','jquery-ui-datepicker'), true);
			//wp_register_script('jquery-ui-i18n', '//ajax.googleapis.com/ajax/libs/jqueryui/'.$jquery_version.'/i18n/jquery-ui-i18n.min.js', array('jquery'), true);
			wp_register_script('thwepo-extra-product-options-script', TH_WEPO_ASSETS_URL.'js/thwepo-extra-product-options-frontend.js', 
			array('jquery-ui-i18n', 'select2'), true);
			
			wp_enqueue_style('select2');
			wp_enqueue_style('jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/'. $jquery_version .'/themes/smoothness/jquery-ui.css');
			wp_enqueue_style('thwepo-timepicker-style', TH_WEPO_ASSETS_URL.'js/timepicker/jquery.timepicker.css');
			wp_enqueue_style('thwepo-extra-product-options-style', TH_WEPO_ASSETS_URL.'css/thwepo-extra-product-options-frontend.css');
						
			wp_enqueue_script('thwepo-timepicker-script');						
			wp_enqueue_script('thwepo-extra-product-options-script');	
								
			//$data = array('language' => $this->get_locale_code());
			$data = array(
				'lang' => array( 
							'am' => $this->__wepo('am'), 
							'pm' => $this->__wepo('pm'),  
							'AM' => $this->__wepo('AM'), 
							'PM' => $this->__wepo('PM'),
							'decimal' => $this->__wepo('.'), 
							'mins' => $this->__wepo('mins'), 
							'hr'   => $this->__wepo('hr'), 
							'hrs'  => $this->__wepo('hrs'),
						),
				'language' 	  => $this->get_locale_code(),
				'date_format' => $this->get_jquery_date_format(wc_date_format()),
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
			);
			wp_localize_script('thwepo-extra-product-options-script', 'thwepo_extra_product_options', $data);
		}
	}
	
	public function woo_loop_add_to_cart_link($link, $product){
		if($this->has_extra_options($product) && $product->is_in_stock()){
			$link = sprintf( '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>',
				esc_url( $product->get_permalink() ),
				esc_attr( isset( $quantity ) ? $quantity : 1 ),
				esc_attr( $product->id ),
				esc_attr( $product->get_sku() ),
				esc_attr( isset( $class ) ? $class : 'button' ),
				esc_html( $this->__wepo('Select options') )
			);
		}
		
		return $link;
	}
	
   /******************************************************
	**** SAVE AND GET OPTIONS FROM WP SESSION - START ****
	******************************************************/
	private function set_extra_options_in_wp_session_using_product_id($product_id, $extra_options){
		$SESSION_KEY = $product_id.'_thwepo_extra_options_pid';
		
		unset($_SESSION[$SESSION_KEY]);
		if(is_array($extra_options) && !empty($extra_options)){
			$_SESSION[$SESSION_KEY] = serialize($extra_options);
		}
	}
	private function get_extra_options_from_wp_session_by_product_id($product_id){
		$SESSION_KEY = $product_id.'_thwepo_extra_options_pid';
		$extra_options = false;
		
		if(isset($_SESSION[$SESSION_KEY])){
			$extra_options = unserialize($_SESSION[$SESSION_KEY]);	
			$extra_options = ($extra_options && is_array($extra_options)) ? $extra_options : false;
		}
		return $extra_options;
	}
	
	private function clear_extra_options_from_wp_session_by_product_id($product_id){
		$SESSION_KEY = $product_id.'_thwepo_extra_options_pid';
		unset($_SESSION[$SESSION_KEY]);
	}
   /******************************************************
	**** SAVE AND GET OPTIONS FROM WP SESSION - END ******
	******************************************************/
	
   /******************************************************
	**** SAVE AND GET OPTIONS FROM WOO SESSION - START ***
	******************************************************/
	private function set_extra_options_in_woo_session_using_product_id($product_id, $extra_options){
		WC()->session->__unset($product_id.'_thwepo_extra_options_pid');
		if(is_array($extra_options) && !empty($extra_options)){
			WC()->session->set($product_id.'_thwepo_extra_options_pid', $extra_options);
		}		
	}
	private function get_extra_options_from_woo_session_by_product_id($product_id){
		$extra_options = false;
		if( WC()->session->__isset( $product_id.'_thwepo_extra_options_pid' ) ){
			$extra_options = WC()->session->get($product_id.'_thwepo_extra_options_pid');	
			$extra_options = ($extra_options && is_array($extra_options)) ? $extra_options : false;
		}
		return $extra_options;
	}
	
	private function set_extra_options_in_woo_session_using_cart_item_key($cart_item_key, $extra_options){
		WC()->session->__unset($cart_item_key.'_thwepo_extra_options_cik');
		if(is_array($extra_options) && !empty($extra_options)){
			WC()->session->set($cart_item_key.'_thwepo_extra_options_cik', $extra_options);
		}
	}
	private function get_extra_options_from_woo_session_by_cart_item_key($cart_item_key){
		$extra_options = false;
		if( WC()->session->__isset( $cart_item_key.'_thwepo_extra_options_cik' ) ){
			$extra_options = WC()->session->get($cart_item_key.'_thwepo_extra_options_cik');
			$extra_options = ($extra_options && is_array($extra_options)) ? $extra_options : false;
		}	
		return $extra_options;
	}
   /******************************************************
	**** SAVE AND GET OPTIONS FROM WOO SESSION - END *****
	******************************************************/
	
   /***************************************************
	**** PREPARE CUSTOM SECTIONS & OPTIONS - START ****
	***************************************************/
	public function has_extra_options($product){
		$options_extra = array();
		
		$categories = array();
		$assigned_categories = wp_get_post_terms($product->id, 'product_cat');
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

		$sections = $this->get_custom_sections();	
		if($sections && is_array($sections) && !empty($sections)){
			foreach($sections as $section_name => $section){
				if($this->is_valid_section($section) && $section->show_section($product->id, $categories)){					
					$fields = $section->get_fields();					
					if($fields){
						foreach($fields as $field_name => $field){
							if($this->is_valid_field($field) && $field->get_property('enabled') && $field->show_field($product->id, $categories)){
								$options_extra[$field_name] = $field;
							}else{
								unset($fields[$field_name]);
							}
						}
						$section->set_fields($fields);
					}
				}else{
					unset($sections[$section_name]);
				}
			}
		}
		
		return empty($options_extra) ? false : true;		
	}
	
	public function woo_before_single_product(){
		$this->sections_extra = array();
		$this->options_extra = array();
		
		global $product;
		$categories = array();
		$assigned_categories = wp_get_post_terms($product->id, 'product_cat');
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

		$sections = $this->get_custom_sections();	
		if($sections && is_array($sections) && !empty($sections)){
			foreach($sections as $section_name => $section){
				if($this->is_valid_section($section) && $section->show_section($product->id, $categories)){					
					$fields = $section->get_fields();					
					if($fields){
						foreach($fields as $field_name => $field){
							if($this->is_valid_field($field) && $field->get_property('enabled') && $field->show_field($product->id, $categories)){
								$this->options_extra[$field_name] = $field;
							}else{
								unset($fields[$field_name]);
							}
						}
						$section->set_fields($fields);
						if($section->has_fields()){
							$hook_name = $section->get_property('position');
							if(array_key_exists($hook_name, $this->sections_extra) && is_array($this->sections_extra[$hook_name])) {
								$this->sections_extra[$hook_name][$section_name] = $section;
							}else{
								$this->sections_extra[$hook_name] = array();
								$this->sections_extra[$hook_name][$section_name] = $section;
							}	
						}					
					}
				}else{
					unset($sections[$section_name]);
				}
			}
		}
		
		$this->set_extra_options_in_wp_session_using_product_id($product->id, $this->options_extra);				
	}
			
	private function get_sections_by_hook($hook_name){
		$extra_sections = $this->get_product_sections_extra();
		if(array_key_exists($hook_name, $extra_sections)) {
			$hooked_sections = $extra_sections[$hook_name];
			return (is_array($hooked_sections) && !empty($hooked_sections)) ? $hooked_sections : false;
		}
		return false;
	}
	
	private function get_product_sections_extra(){
		return is_array($this->sections_extra) ? $this->sections_extra : array();
	}
	private function get_product_options_extra(){
		return $this->options_extra;
	}
	
   /***************************************************
	**** PREPARE CUSTOM SECTIONS & OPTIONS - END ******
	***************************************************/
	
	
   /***********************************************
	**** DISPLAY CUSTOM PRODUCT FIELDS - START ****
	***********************************************/	
	/*private function product_info(){
		global $product;
		$price = $product->price;
		echo '<input type="hidden" name="wepo-product-price" value="'.$price.'" >';
	}*/
	
	private function render_sections($hook_name){	
		$sections = $this->get_sections_by_hook($hook_name);
		if($sections){						
			foreach($sections as $section_name => $section){
				$section->render_section();
			}
		}
	}
	
	public function woo_before_add_to_cart_button(){
		//$this->product_info();
		$this->render_sections('woo_before_add_to_cart_button');
	}
	
	public function woo_after_add_to_cart_button(){
		$this->render_sections('woo_after_add_to_cart_button');
	}
	
	/*public function woo_single_product_before_title(){
		$this->render_sections('woo_single_product_before_title');
	}
	
	public function woo_single_product_after_title(){
		$this->render_sections('woo_single_product_after_title');
	}
	
	public function woo_single_product_before_rating(){
		$this->render_sections('woo_single_product_before_rating');
	}	
	public function woo_single_product_after_rating(){
		$this->render_sections('woo_single_product_after_rating');
	}
	
	public function woo_single_product_before_price(){
		$this->render_sections('woo_single_product_before_price');
	}	
	public function woo_single_product_after_price(){
		$this->render_sections('woo_single_product_after_price');
	}
	
	public function woo_single_product_before_excerpt(){
		$this->render_sections('woo_single_product_before_excerpt');
	}	
	public function woo_single_product_after_excerpt(){
		$this->render_sections('woo_single_product_after_excerpt');
	}
	
	public function woo_single_product_before_add_to_cart(){
		$this->render_sections('woo_single_product_before_add_to_cart');
	}	
	public function woo_single_product_after_add_to_cart(){
		$this->render_sections('woo_single_product_after_add_to_cart');
	}
		
	public function woo_single_product_before_meta(){
		$this->render_sections('woo_single_product_before_meta');
	}	
	public function woo_single_product_after_meta(){
		$this->render_sections('woo_single_product_after_meta');
	}	
	
	public function woo_single_product_before_sharing(){
		$this->render_sections('woo_single_product_before_sharing');
	}	
	public function woo_single_product_after_sharing(){
		$this->render_sections('woo_single_product_before_sharing');
	}*/
   /***********************************************
	**** DISPLAY CUSTOM PRODUCT FIELDS - END ******
	***********************************************/
	
	
   /***************************************************
	**** CUSTOM PRODUCT OPTIONS VALIDATION - START ****
	***************************************************/
	public function woo_add_to_cart_validation($valid, $product_id, $quantity) { 
		$extra_options = $this->get_extra_options_from_wp_session_by_product_id($product_id);
		if($extra_options){
			foreach($extra_options as $field_name => $field){
				if(isset($_POST[$field_name]) || isset($_REQUEST[$field_name])){
					$value = isset($_POST[$field_name]) && !empty($_POST[$field_name]) ? $_POST[$field_name] : '';
					$value = empty($value) && isset($_REQUEST[$field_name]) ? $_REQUEST[$field_name] : $value;
					$valid = $this->validate_field($valid, $field, $value);
				}
			}
		}
		return $valid;
	}
	
	private function validate_field($valid, $field, $value){
		if($field->get_property('required') && empty($value)) {
			$this->wepo_add_error($this->__wepo( 'Please enter a value for '.$field->get_property('title') ));
			$valid = false;
		}else{
			$validators = $field->get_property('validate');
			$validators = !empty($validators) ? explode("|", $validators) : false;

			if($validators && !empty($value)){
				foreach($validators as $validator){
					switch($validator) {
						case 'number' :
							if(!is_numeric($value)){
								$this->wepo_add_error('<strong>'.$field->get_property('title').'</strong> '. sprintf($this->__wepo('(%s) is not a valid number.'), $value));
								$valid = false;
							}
							break;

						case 'email' :
							if(!is_email($value)){
								$this->wepo_add_error('<strong>'.$field->get_property('title').'</strong> '. sprintf($this->__wepo('(%s) is not a valid email address.'), $value));
								$valid = false;
							}
							break;
					}
				}
			}
		}
		return $valid;
	}
   /*************************************************
	**** CUSTOM PRODUCT OPTIONS VALIDATION - END ****
	*************************************************/
	
	public function calculate_extra_cost_handler() {
		$request_data_json = isset($_POST['price_info']) ? stripslashes($_POST['price_info']) : '';
		
		if($request_data_json) {
			$request_data = json_decode($request_data_json, true);
			$result = $this->calculate_total_extra_cost($request_data);
			
			if($result){
				$return = array(
					'code' => 'E000',
					'message' => '',
					'result' => $result
				);
			}else{
				$price_html = $this->get_default_price($request_data);
				if($price_html){
					$return = array(
						'code' => 'E002',
						'message' => '',
						'result' => $price_html
					);
				}else{
					$return = array(
						'code' => 'E003',
						'message' => ''
					);
				}
			}
		
			wp_send_json($return);
		}else{
			$return = array(
				'code' => 'E001',
				'message' => ''
			);
		
			wp_send_json($return);
		}
	}
	
	public function get_default_price($request_data){
		$price_html = false;
		$product_id = isset($request_data['product_id']) ? $request_data['product_id'] : false;
		$variation_id = isset($request_data['variation_id']) ? $request_data['variation_id'] : false;
		
		if($variation_id){
			$product = new WC_Product_Variation( $variation_id );
			$price_html = $product->get_price_html();
		}else if($product_id){
			$pf = new WC_Product_Factory();  
			$product = $pf->get_product($product_id);
			$price_html = $product->get_price_html();
		}
		return $price_html;
	}
	
	public function get_product_price($request_data){
		$price = false;
		$product_id = isset($request_data['product_id']) ? $request_data['product_id'] : false;
		$variation_id = isset($request_data['variation_id']) ? $request_data['variation_id'] : false;
		
		if($variation_id){
			$product = new WC_Product_Variation( $variation_id );
			$price = $product->price;
		}else if($product_id){
			$pf = new WC_Product_Factory();  
			$product = $pf->get_product($product_id);
			$price = $product->price;
		}
		return $price;
	}
	
	public function calculate_total_extra_cost($request_data){
		$result = false;
		$product_id = isset($request_data['product_id']) ? $request_data['product_id'] : false;
		$price_info_list = isset($request_data['price_info']) ? $request_data['price_info'] : false;
		
		if($product_id && $price_info_list){
			//$pf = new WC_Product_Factory();  
			//$product = $pf->get_product($product_id);
			//$product_price = $product->price;
			$product_price = $this->get_product_price($request_data);
			
			$fprice = 0;
			
			foreach($price_info_list as $fname => $price_info){
				$price_type = isset($price_info['price_type']) ? $price_info['price_type'] : '';
				$price 		= isset($price_info['price']) ? $price_info['price'] : 0;
				$multiple   = isset($price_info['multiple']) ? $price_info['multiple'] : 0;
				
				if($multiple == 1){
					$price_arr = explode(",", $price);
					$price_type_arr = explode(",", $price_type);
					
					foreach($price_arr as $index => $oprice){
						$oprice_type = isset($price_type_arr[$index]) ? $price_type_arr[$index] : 'normal';
						
						$fprice += $this->calculate_extra_cost($product_price, $oprice_type, $oprice, false, false);
					}
				}else{
					$price_unit = isset($price_info['price_unit']) ? $price_info['price_unit'] : false;
					$value 		= isset($price_info['value']) ? $price_info['value'] : false;
				
					$fprice += $this->calculate_extra_cost($product_price, $price_type, $price, $price_unit, $value);
				}
			}
			
			$final_price = $product_price + $fprice;
			$display_price = wc_price($final_price);
			
			$result = array();
			$result['product_price'] = $product_price;
			$result['extra_cost'] = $fprice;
			$result['final_price'] = $final_price;
			$result['display_price'] = $display_price;
		}
		
		return $result;
	}
	
	public function calculate_extra_cost($product_price, $price_type, $price, $price_unit, $value){
		$fprice = 0;
		
		if($price_type === 'percentage'){
			if(is_numeric($price) && is_numeric($product_price)){
				$fprice = ($price/100)*$product_price;
			}
		}else if($price_type === 'dynamic' || $price_type === 'dynamic-excl-base-price'){
			if(is_numeric($price) && is_numeric($value) && is_numeric($price_unit) && $price_unit > 0){
				$fprice = $price*($value/$price_unit);
				
				if($price_type === 'dynamic-excl-base-price' && is_numeric($product_price) && $value >= $price_unit){
					$fprice = $fprice - $product_price;
				}
			}
		}else{
			if(is_numeric($price)){
				$fprice = $price;
			}
		}

		return $fprice;
	}
		
   /*********************************************************
	**** ADD CUSTOM OPTIONS & PRICE to CART ITEM - START ****
	*********************************************************/
	public function woo_add_cart_item_data( $cart_item_data, $product_id ) {
		$extra_options = $this->get_extra_options_from_wp_session_by_product_id($product_id);
		$this->set_extra_options_in_woo_session_using_product_id($product_id, $extra_options);
		$this->clear_extra_options_from_wp_session_by_product_id($product_id);
		
		if($extra_options){
			foreach($extra_options as $name => $data){
				if( isset($_POST[$name]) && !empty($_POST[$name]) ) {
					$cart_item_data[$name] = $_POST[$name];
					$cart_item_data['unique_key'] = md5( microtime().rand() );
				}
			}
		}
		return $cart_item_data;
	}
	
	public function woo_add_to_cart( $cart_item_key, $product_id = null, $quantity= null, $variation_id= null, $variation= null ) {
		$extra_options = $this->get_extra_options_from_woo_session_by_product_id($product_id);
		$extra_data = array();
		
		if($extra_options){
			foreach($extra_options as $name => $data){
				if( isset($_POST[$name]) && !empty($_POST[$name]) ) {
					$data->set_property('value', $_POST[$name]);
					$extra_data[$name] = $data;
				}
			}  	
		}	
		$this->set_extra_options_in_woo_session_using_cart_item_key($cart_item_key, $extra_data);		
	}
	
	public function woo_before_calculate_totals( $cart_object ) {				
		foreach($cart_object->cart_contents as $key => $value) { 		
			$extra_options = $this->get_extra_options_from_woo_session_by_cart_item_key($key);	      
			if($extra_options) {				
				$quantity = floatval( $value['quantity'] );
				$orgPrice = floatval( $value['data']->price );
				$extra_price = 0;
				foreach($extra_options as $name => $data){
					if($data->get_property('price_field')){
						$extra_price = $extra_price + $data->get_price_final($orgPrice);
					}
				}
				$value['data']->price = $orgPrice + $extra_price;
			}           
		}
	}
	
	public function woo_get_item_data( $cart_data, $cart_item = null ) {
		$custom_items = array();
		if(!empty( $cart_data ) ) {
			$custom_items = $cart_data;
		}		
				
		$extra_options = $this->get_extra_options_from_woo_session_by_product_id($cart_item['product_id']);
		if($extra_options){
			foreach($extra_options as $name => $data){
				if(isset($cart_item[$name])) {
					$value = is_array($cart_item[$name]) ? implode(",", $cart_item[$name]) : $cart_item[$name];
					$custom_items[] = array( "name" => $data->get_display_label(), "value" => $value );
				}
			}
		}
		return $custom_items;
	}
	
	public function woo_add_order_item_meta( $item_id, $values, $cart_item_key ) {
		$extra_options = $this->get_extra_options_from_woo_session_by_cart_item_key($cart_item_key);
		if($extra_options){			
			foreach($extra_options as $name => $data){
				$value = is_array($values[$name]) ? implode(",", $values[$name]) : $values[$name];
				wc_add_order_item_meta( $item_id, $data->get_property('name'), $value );
			}
		}		
	}
				
	public function woo_order_items_meta_get_formatted( $formatted_meta, $item_meta ) {
		if(!empty($formatted_meta)){
			$name_title_map = $this->get_options_name_title_map();
			if($name_title_map){
				foreach($formatted_meta as &$meta){
					if(array_key_exists($meta['key'], $name_title_map)) {
						$meta['label'] = $name_title_map[$meta['key']];
					}
				}
			}
		}
		return $formatted_meta;
	}
   /*********************************************************
	**** ADD CUSTOM OPTIONS & PRICE to CART ITEM - END ******
	*********************************************************/
	
}

endif;