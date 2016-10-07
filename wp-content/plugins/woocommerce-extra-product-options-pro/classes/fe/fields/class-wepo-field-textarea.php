<?php
/**
 * Product Field - Textarea
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Product_Field_Textarea')):

class WEPO_Product_Field_Textarea extends WEPO_Product_Field{
	public function __construct() {
		$this->type = 'textarea';
	}	
		
	public function get_html(){
		$price_data = $this->get_price_data();
		$input_class = $this->price_field ? 'thwepo-price-field' : '';
		$value = isset($_POST[$this->name]) ? $_POST[$this->name] : $this->value;
		
		$field_props  = 'placeholder="'. $this->esc_html__wepo($this->placeholder) .'"';
		$field_props .= ' class="'.$input_class.'"';
		$field_props .= $price_data;
		
		$input_html = '<textarea id="'.$this->name.'" name="'.$this->name.'" '.$field_props.' >'.$value.'</textarea>';
		
		$html = $this->prepare_field_html($input_html);
		return $html;
	}
}

endif;