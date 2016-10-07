<?php
/**
 * WooCommerce Checkout Field Editor Pro - Admin Utils
 *
 * @author      ThemeHiGH
 * @category    Admin
 */
 
if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Checkout_Fields_Admin_Utils')) :

class WCFE_Checkout_Fields_Admin_Utils extends WCFE_Checkout_Fields_Utils{
	protected static $_instance = null;	
	private $field_factory = NULL;
	
	public function __construct() {		
		$this->field_factory = new WCFE_Checkout_Field_Factory();
	}
	
	public static function instance() {
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function init() {	
		$this->prepare_sections_and_fields();
	}
	
	public function update_section($section){
	 	if($this->is_valid_section($section)){	
			$sections = $this->get_checkout_sections();
			$sections = (isset($sections) && is_array($sections)) ? $sections : array();
			
			$sections[$section->name] = $section;
			
			$result1 = $this->save_sections($sections);
			$result2 = $this->update_section_hook_map($section);
	
			return $result1;
		}
		return false;
	}
	
	public function save_sections($sections){
		$result = update_option(self::OPTION_KEY_CUSTOM_SECTIONS, $sections);
		return $result;
	}
	
	public function update_section_hook_map($section){
		$section_name = $section->name;
		$hook_name 	  = $section->position;
				
	 	if(isset($hook_name) && isset($section_name) && !empty($hook_name) && !empty($section_name)){	
			$hook_map = $this->get_section_hook_map();
			
			if($hook_map && is_array($hook_map)){
				foreach($hook_map as $hname => $hsections){
					if($hsections && is_array($hsections)){
						if(($key = array_search($section_name, $hsections)) !== false) {
							unset($hsections[$key]);
							$hook_map[$hname] = $hsections;
						}
					}
					
					if(empty($hsections)){
						unset($hook_map[$hname]);
					}
				}
			}
			
			if(isset($hook_map[$hook_name])){
				$hooked_sections = $hook_map[$hook_name];
				if(!in_array($section_name, $hooked_sections)){
					$hooked_sections[] = $section_name;
					$hook_map[$hook_name] = $hooked_sections;
					$this->save_section_hook_map($hook_map);
				}
			}else{
				$hooked_sections = array();
				$hooked_sections[] = $section_name;
				$hook_map[$hook_name] = $hooked_sections;
				$this->save_section_hook_map($hook_map);
			}					
		}
	}
	
	public function save_section_hook_map($section_hook_map){
		$result = update_option(self::OPTION_KEY_SECTION_HOOK_MAP, $section_hook_map);		
		return $result;
	}
	
	public function remove_section_from_hook($hook_name, $section_name){
		if(isset($hook_name) && isset($section_name) && !empty($hook_name) && !empty($section_name)){	
			$hook_map = $this->get_section_hook_map();
			if(isset($hook_map[$hook_name])){
				$hooked_sections = $hook_map[$hook_name];
				if(!in_array($section_name, $hooked_sections)){
					unset($hooked_sections[$section_name]);				
					$hook_map[$hook_name] = $hooked_sections;
					$this->save_section_hook_map($hook_map);
				}
			}				
		}
	}
	
	public function prepare_sections_and_fields(){
		$sections = $this->get_checkout_sections();
		if(empty($sections)){
			$sections = $this->get_default_sections();
			
			$old_custom_sections = get_option('thwcfd_custom_checkout_sections');
			$old_cfields = get_option('thwcfd_checkout_fields');
			
			if($sections && is_array($sections)){
				foreach($sections as $sname => $section){
					if($old_cfields && is_array($old_cfields) && isset($old_cfields['wcfd_fields_'.$sname])){
						$old_fields = $old_cfields['wcfd_fields_'.$sname];
						$fields = $this->prepare_fields_objects($old_fields);
						
						if(!empty($fields)){
							$section->set_fields($fields);
						}
					}
				}
				
				$this->save_sections($sections);
				
				if($old_custom_sections && is_array($old_custom_sections)){
					foreach($old_custom_sections as $old_csname => $old_csection){
						$section = $this->prepare_section_object($old_csection, $old_cfields);
						if($section){
							//$sections[$old_csname] = $section;
							$this->update_section($section);
						}
					}
				}
			}
			$this->clear_old_settings();
		}
	}
	
	public function prepare_section_object($section_arr, $fields_arr){
		$section = false;
		if($section_arr && is_array($section_arr)){
			$sname = $section_arr['name'];
			
			$section = new WCFE_Checkout_Section();
			$section->set_id($sname);
			$section->set_name($sname);
			$section->set_title($section_arr['label']);
			$section->set_position($section_arr['position']);
			$section->set_custom_section(1);
			$section->set_show_title($section_arr['use_as_title']);
			
			if($fields_arr && is_array($fields_arr) && isset($fields_arr['wcfd_fields_'.$sname])){
				$old_fields = $fields_arr['wcfd_fields_'.$sname];
				$fields = $this->prepare_fields_objects($old_fields);
				$section->set_fields($fields);
			}
		}
		return $section;
	}
	
	public function prepare_fields_objects($fields){
		$field_factory = new WCFE_Checkout_Field_Factory();
			
		$field_objects = array();
		if($fields && !empty($fields) && is_array($fields)){
			foreach($fields as $name => $field){
				if(!empty($name) && !empty($field) && is_array($field)){
					$field['type'] = isset($field['type']) ? $field['type'] : 'text';
					$field_object = $field_factory->create_field($field['type'], $name, $field); 
				
					if($field_object){
						$field_objects[$name] = $field_object;
					}
				}
			}
		}
		
		return $field_objects;
	}
	
	public function get_default_sections(){
		//$default_sections = array('billing' => 'Billing Fields', 'shipping' => 'Shipping Fields', 'additional' => 'Additional Fields', 'address' => 'Address Fields');
		$default_sections = array('billing' => 'Billing Fields', 'shipping' => 'Shipping Fields', 'additional' => 'Additional Fields');
		
		$sections = array();
		foreach($default_sections as $name => $title){
			$section = new WCFE_Checkout_Section();
			$section->set_id($name);
			$section->set_name($name);
			$section->set_title($title);
			$section->set_custom_section(0);
			$section->set_fields($this->get_default_fields($name));
			
			$sections[$name] = $section;
		}
		return $sections;
	}
	
	public function get_default_fields($section_name){
		$fields = false;
		if($section_name === 'billing' || $section_name === 'shipping'){
			$fields = WC()->countries->get_address_fields(WC()->countries->get_base_country(), $section_name . '_');
		}else if($section_name === 'additional'){
			$fields = array(
				'order_comments' => array(
					'type'        => 'textarea',
					'class'       => array('notes'),
					'label'       => __('Order Notes', 'woocommerce'),
					'placeholder' => _x('Notes about your order, e.g. special notes for delivery.', 'placeholder', 'woocommerce')
				)
			);
		}
			
		$field_objects = array();
		if(!empty($fields) && is_array($fields)){
			foreach($fields as $name => $field){
				if(!empty($name) && !empty($field) && is_array($field)){
					$field['type'] = isset($field['type']) ? $field['type'] : 'text';
					$field_object = $this->field_factory->create_field($field['type'], $name, $field); 
				
					if($field_object){
						$field_objects[$name] = $field_object;
					}
				}
			}
		}
		
		return $field_objects;
	}
	
	public function clear_old_settings(){
		delete_option("thwcfd_custom_checkout_sections");
		delete_option("thwcfd_section_hook_map");
		delete_option('thwcfd_checkout_fields');
	}
}

endif;