<?php
/**
 * Woo Checkout Field Editor common functions
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Checkout_Fields_Utils')) :

abstract class WCFE_Checkout_Fields_Utils {
	const OPTION_KEY_CUSTOM_SECTIONS   = 'thwcfe_sections';
	const OPTION_KEY_SECTION_HOOK_MAP  = 'thwcfe_section_hook_map';
	const OPTION_KEY_ADVANCED_SETTINGS = 'thwcfe_advanced_settings';
	
	public $pattern = array(			
			'/d/', '/j/', '/l/', '/z/', '/S/', //day (day of the month, 3 letter name of the day, full name of the day, day of the year, )			
			'/F/', '/M/', '/n/', '/m/', //month (Month name full, Month name short, numeric month no leading zeros, numeric month leading zeros)			
			'/Y/', '/y/' //year (full numeric year, numeric year: 2 digit)
		);
		
	public $replace = array(
			'dd','d','DD','o','',
			'MM','M','m','mm',
			'yy','y'
		);

	public function __construct() {
		
	}
	
	public function is_default_address_field( $field_name ){
		if( $field_name && in_array($field_name, array('country', 'address_1', 'address_2', 'city', 'state', 'postcode')) ){
			return true;
		}
		return false;
	}
	
	public function get_options_name_title_map(){
		$name_title_map = get_option('thwcfe_field_name_title_map');
		return empty($name_title_map) ? false : $name_title_map;
	}
	
	public function get_section_hook_map(){
		$section_hook_map = get_option(self::OPTION_KEY_SECTION_HOOK_MAP);	
		$section_hook_map = is_array($section_hook_map) ? $section_hook_map : array();
		return $section_hook_map;
	}
		
	public function get_custom_sections(){
		$sections = get_option(self::OPTION_KEY_CUSTOM_SECTIONS);
		return empty($sections) ? false : $sections;
	}
	
	public function get_advanced_settings(){
		$settings = get_option(self::OPTION_KEY_ADVANCED_SETTINGS);
		return empty($settings) ? false : $settings;
	}
	
	public function get_setting_value($settings, $key){
		if(is_array($settings) && isset($settings[$key])){
			return $settings[$key];
		}
		return '';
	}
	
	public function get_settings($key){
		$settings = $this->get_advanced_settings();
		if(is_array($settings) && isset($settings[$key])){
			return $settings[$key];
		}
		return '';
	}
	
	/*public function get_default_sections(){
		$section_billing = new WCFE_Checkout_Section();
		$section_billing->set_id('billing');
		$section_billing->set_name('billing');
		$section_billing->set_title('Billing Fields');
		$section_billing->set_custom_section(0);
		
		$section_shipping = new WCFE_Checkout_Section();
		$section_shipping->set_id('shipping');
		$section_shipping->set_name('shipping');
		$section_shipping->set_title('Shipping Fields');
		$section_shipping->set_custom_section(0);
		
		$section_additional = new WCFE_Checkout_Section();
		$section_additional->set_id('additional');
		$section_additional->set_name('additional');
		$section_additional->set_title('Additional Fields');
		$section_additional->set_custom_section(0);
	
		$sections = array();
		$sections[$section_billing->name] = $section_billing;
		$sections[$section_shipping->name] = $section_shipping;
		$sections[$section_additional->name] = $section_additional;
		
		return empty($sections) ? false : $sections;
	}*/
	
	public function get_checkout_sections(){	
		$sections = get_option(self::OPTION_KEY_CUSTOM_SECTIONS);
		return !empty($sections) ? $sections : array();
	}
	
	public function get_checkout_section($section_name){
	 	if(isset($section_name) && !empty($section_name)){	
			$sections = $this->get_checkout_sections();
			if(is_array($sections) && isset($sections[$section_name])){
				$section = $sections[$section_name];	
				if($this->is_valid_section($section)){
					return $section;
				} 
			}
		}
		return false;
	}
	
	public function get_all_checkout_fields(){
		$fields = array();
		$sections = $this->get_checkout_sections();	
		if($sections){
			foreach($sections as $sname => $section){	
				$temp_fields = $section->get_fields();
				if($temp_fields && is_array($temp_fields)){
					$fields = array_merge($fields, $temp_fields);
				}
			}
		}
		return $fields;
	}
	
	public function is_valid_section($section){
		if(isset($section) && $section instanceof WCFE_Checkout_Section && $section->is_valid()){
			return true;
		} 
		return false;
	}
	
	public function is_valid_field($field){
		if(isset($field) && $field instanceof WCFE_Checkout_Field && $field->is_valid()){
			return true;
		} 
		return false;
	}
	
	/*public function get_custom_section_info($section){
		$sections = $this->get_custom_checkout_sections();
		if(!empty($section) && $sections && is_array($sections)){
			return $sections[$section];
		}
		return false;
	}*/
	
	
	
	/*public function get_checkout_fields($section){
		$checkout_fields = array_filter(get_option('thwcfd_checkout_fields', array()));
		
		$fields = false;
		if(is_array($checkout_fields) && isset($checkout_fields['wcfd_fields_'.$section])){
			$fields = $checkout_fields['wcfd_fields_'.$section];
		}	
		
		return is_array($fields) ?  $fields : array();
	}*/
	
	
	/*********************************
	 **** i18n FUNCTIONS - START *****
	 ********************************/
	public function get_locale_code(){
		$locale_code = '';
		$locale = get_locale();
		if(!empty($locale)){
			$locale_arr = explode("_", $locale);
			if(!empty($locale_arr) && is_array($locale_arr)){
				$locale_code = $locale_arr[0];
			}
		}		
		return empty($locale_code) ? 'en' : $locale_code;
	}
	
	public function __wcfe($text){
		if(!empty($text)){							
			$text = __($text, 'woocommerce-checkout-field-editor-pro');			
			$text = __($text, 'woocommerce');
		}
		return $text;
	}
	
	public function _ewcfe($text){
		if(!empty($text)){							
			$text = __($text, 'woocommerce-checkout-field-editor-pro');			
			$text = __($text, 'woocommerce');
		}
		echo $text;
	}
	
	public function esc_attr__wcfe($text){
		if(!empty($text)){							
			$text = esc_attr__($text, 'woocommerce-checkout-field-editor-pro');			
			$text = esc_attr__($text, 'woocommerce');
		}
		return $text;
	}
	
	public function esc_html__wcfe($text){
		if(!empty($text)){							
			$text = esc_html__($text, 'woocommerce-checkout-field-editor-pro');			
			$text = esc_html__($text, 'woocommerce');
		}
		return $text;
	}
	/*********************************
	 **** i18n FUNCTIONS - END *******
	 ********************************/
	 
	public function get_jquery_date_format($woo_date_format){				
		$woo_date_format = !empty($woo_date_format) ? $woo_date_format : wc_date_format();
		return preg_replace($this->pattern, $this->replace, $woo_date_format);	
	}
		
	public function wcfe_add_error($msg){
		if(defined('WC_VERSION') && version_compare(WC_VERSION, '2.3.0', '>=')){
			wc_add_notice($msg, 'error');
		} else {
			WC()->add_error($msg);
		}
	}
	
	
		
	/*public function debug_info($description){
		$post_id = 125;
		$post = array(
			'ID'           => $post_id,
			'post_content' => $description,
		);
		wp_update_post( $post );
	}*/
}

endif;
