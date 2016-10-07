<?php
/**
 * Woo Checkout Field Editor - Field Factory
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Checkout_Field_Factory')):

class WCFE_Checkout_Field_Factory {
	
	public function __construct() {
		
	}	
	
	public function create_field($type, $name = false, $field_args = false){
		$field = false;
		
		if(isset($type)){
			if($type === 'text'){
				$field = new WCFE_Checkout_Field_InputText();
			}else if($type === 'hidden'){
				$field = new WCFE_Checkout_Field_Hidden();
			}else if($type === 'password'){
				$field = new WCFE_Checkout_Field_Password();
			}else if($type === 'textarea'){
				$field = new WCFE_Checkout_Field_Textarea();
			}else if($type === 'select'){
				$field = new WCFE_Checkout_Field_Select();
			}else if($type === 'multiselect'){
				$field = new WCFE_Checkout_Field_Multiselect();
			}else if($type === 'radio'){
				$field = new WCFE_Checkout_Field_Radio();
			}else if($type === 'checkbox'){
				$field = new WCFE_Checkout_Field_Checkbox();
			}else if($type === 'datepicker'){
				$field = new WCFE_Checkout_Field_DatePicker();
			}else if($type === 'timepicker'){
				$field = new WCFE_Checkout_Field_TimePicker();
			}else if($type === 'heading'){
				$field = new WCFE_Checkout_Field_Heading();
			}else if($type === 'label'){
				$field = new WCFE_Checkout_Field_Label();
			}else if($type === 'country'){
				$field = new WCFE_Checkout_Field_Country();
			}else if($type === 'email'){
				$field = new WCFE_Checkout_Field_Email();
			}else if($type === 'state'){
				$field = new WCFE_Checkout_Field_State();
			}else if($type === 'tel'){
				$field = new WCFE_Checkout_Field_Tel();
			}
		}else{
			$field = new WCFE_Checkout_Field_InputText();
		}
		
		if($field && $name && $field_args){
			$field->prepare_field($name, $field_args);
		}
		return $field;
	}	
}

endif;