<?php
/**
 * Product Field - Input Text
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Product_Field_InputText')):

class WEPO_Product_Field_InputText extends WEPO_Product_Field{
	public function __construct() {
		$this->type = 'inputtext';
	}	
		
	public function get_html(){
		$price_data = $this->get_price_data();
		$input_class = $this->price_field ? 'thwepo-price-field' : '';
		$value = isset($_POST[$this->name]) ? $_POST[$this->name] : $this->value;
		
		$field_props  = 'placeholder="'. $this->esc_html__wepo($this->placeholder) .'"';
		$field_props .= ' value="'.$value.'"';
		$field_props .= ' class="'.$input_class.'"';
		$field_props .= $price_data;
		
		$input_html = '<input type="text" id="'.$this->name.'" name="'.$this->name.'" '.$field_props.' />';
		
		$html = $this->prepare_field_html($input_html);
		return $html;
	}
}

endif;