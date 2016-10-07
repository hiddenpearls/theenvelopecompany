<?php
/**
 * Product Field - Radio
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Product_Field_Radio')):

class WEPO_Product_Field_Radio extends WEPO_Product_Field{
	public $options = array();
	
	public function __construct() {
		$this->type = 'radio';
	}	
	
	public function get_html(){
		$value = isset($_POST[$this->name]) ? $_POST[$this->name] : $this->value;
		$input_class = $this->price_field ? 'thwepo-price-field' : '';
		
		$input_html = '';
		foreach($this->options as $option_key => $option){
			$checked = checked($value, esc_attr($option_key), false);
			$price_html   = $this->get_display_price_option($option);
			
			$price_data = $this->get_price_data_option($option);
		
			$field_props  = 'value="'.esc_attr($option_key).'"';
			$field_props .= ' class="'.$input_class.'"';
			$field_props .= ' '.$checked;
			$field_props .= $price_data;
			
			$option_text  = $this->esc_html__wepo($option['text']);
			$option_text .= !empty($price_html) ? ' (+'.$price_html.')' : '';
			
			$input_html .= '<label for="'. $this->name.'_'.esc_attr($option_key) .'" class="radio '. $this->title_class_str .'" style="margin-right: 10px;">';
			$input_html .= '<input type="radio" id="'. $this->name.'_'.esc_attr($option_key) .'" name="'. $this->name .'" '. $field_props .'/> ';
			$input_html .= $option_text .'</label>';
		}
		
		$html = $this->prepare_field_html($input_html, false);
		return $html;
	}
	
	public function get_display_price_option($option){
		$is_price_field = is_numeric($option['price']);
		$price_type = $option['price_type'];
		$price = $option['price'];
		
		return $this->get_price_html($is_price_field, $price_type, $price);
	}
	
	public function get_price_final($product_price){
		$fprice = 0;
		$options = $this->options;
		if(is_array($options) && isset($options[$this->value])){
			$selected_option = $options[$this->value];
			if(isset($selected_option['price'])){
				$fprice = $selected_option['price'];
				
				if(isset($selected_option['price_type']) && $selected_option['price_type'] === 'percentage'){
					if(is_numeric($fprice) && is_numeric($product_price)){
						$fprice = ($fprice/100)*$product_price;
					}	
				}
			}
		}
		return $fprice;
	}
}

endif;