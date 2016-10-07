<?php
/**
 * Woo Extra Product Options common functions
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Extra_Product_Options_Utils')) :

class WEPO_Extra_Product_Options_Utils {
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
	
	public function get_options_name_title_map(){
		$name_title_map = get_option('thwepo_options_name_title_map');
		return empty($name_title_map) ? false : $name_title_map;
	}
	
	public function get_section_hook_map(){
		$section_hook_map = get_option('thwepo_section_hook_map');	
		$section_hook_map = is_array($section_hook_map) ? $section_hook_map : array();
		return $section_hook_map;
	}
		
	public function get_custom_sections(){
		$sections = get_option('thwepo_custom_sections');
		return empty($sections) ? false : $sections;
	}
	
	public function get_sections(){				
		$sections = $this->get_custom_sections();
		
		if($sections && is_array($sections) && !empty($sections)){
			return $sections;
		}else{
			$section = new WEPO_Product_Page_Section();
			$section->set_default_section();
			
			$sections = array();
			$sections[$section->name] = $section;
			return $sections;
		}		
	}
	
	public function is_valid_section($section){
		if(isset($section) && $section instanceof WEPO_Product_Page_Section && $section->is_valid()){
			return true;
		} 
		return false;
	}
	
	public function is_valid_field($field){
		if(isset($field) && $field instanceof WEPO_Product_Field && $field->is_valid()){
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
	
	public function __wepo($text){
		if(!empty($text)){							
			$text = __($text, 'woocommerce-extra-product-options-pro');			
			$text = __($text, 'woocommerce');
		}
		return $text;
	}
	
	public function _ewepo($text){
		if(!empty($text)){							
			$text = __($text, 'woocommerce-extra-product-options-pro');			
			$text = __($text, 'woocommerce');
		}
		echo $text;
	}
	
	public function esc_attr__wepo($text){
		if(!empty($text)){							
			$text = esc_attr__($text, 'woocommerce-extra-product-options-pro');			
			$text = esc_attr__($text, 'woocommerce');
		}
		return $text;
	}
	
	public function esc_html__wepo($text){
		if(!empty($text)){							
			$text = esc_html__($text, 'woocommerce-extra-product-options-pro');			
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
		
	public function wepo_add_error($msg){
		if(defined('WC_VERSION') && version_compare(WC_VERSION, '2.3.0', '>=')){
			wc_add_notice($msg, 'error');
		} else {
			WC()->add_error($msg);
		}
	}
	
	
		
	public function debug_info($description){
		$post_id = 125;
		$post = array(
			'ID'           => $post_id,
			'post_content' => $description,
		);
		wp_update_post( $post );
	}
}

endif;
