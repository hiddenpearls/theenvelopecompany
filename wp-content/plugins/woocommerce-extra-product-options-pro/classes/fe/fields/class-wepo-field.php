<?php
/**
 * Product Field Propertoes
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Product_Field')):

class WEPO_Product_Field extends WEPO_Extra_Product_Options_Utils{
	public $order = '';
	public $type = '';
	public $id   = '';
	public $name = '';	
	
	public $value = '';
	public $placeholder = '';
	public $options_json = '';
	public $options = array();
	public $validate = '';
	//public $validator = '';
	public $cssclass = '';
	public $cssclass_str = '';
		
	public $title = '';
	public $title_type  = '';
	public $title_color = '';
	public $title_position = '';
	public $title_class = '';
	public $title_class_str = '';
	
	public $subtitle = '';
	public $subtitle_type  = '';
	public $subtitle_color = '';
	public $subtitle_position = '';
	public $subtitle_class = '';
	public $subtitle_class_str = '';
	
	public $price_field = false;
	public $price = 0;
	public $price_unit = 0;
	public $price_type = '';
	public $price_prefix = '';
	public $price_suffix = '';
	
	public $required = false;
	public $enabled  = true;
	
	public $rules_action = '';
	public $rules_action_ajax = '';
	
	public $conditional_rules_json = '';
	public $conditional_rules = array();
	
	public $conditional_rules_ajax_json = '';
	public $conditional_rules_ajax = array();
	
	
	public $separator_type  = '';
	public $separator_hight = '';
		
	public function __construct() {
			
	}	
	
	public function prepare_properties(){
		$this->name = urldecode( sanitize_title(wc_clean($this->name)) );
		$this->id = $this->name;
		
		$this->cssclass_str = $this->convert_to_cssclass_string($this->cssclass);
		$this->title_class_str = $this->convert_to_cssclass_string($this->title_class);
		$this->subtitle_class_str = $this->convert_to_cssclass_string($this->subtitle_class);
		
		if($this->type === 'radio' || $this->type === 'select' || $this->type === 'multiselect'){
			foreach($this->options as $option_key => $option){
				if(isset($option['price']) && is_numeric($option['price']) && $option['price'] != 0){
					$this->price_field = 1;
				}
			}
		}else{
			if(is_numeric($this->price) && $this->price != 0){
				$this->price_field = 1;
			}
		}
		
		if($this->type === 'label' || $this->type === 'heading'){
			$this->price_field = 0;
			$this->price = 0;	
			$this->price_type = '';	
			$this->price_prefix = '';	
			$this->price_suffix = '';
				
			$this->required = 0;
		}
	}
	
	public function is_valid(){
		if(empty($name)){
			//return false;
		}
		return true;
	}
	
	public function show_field($product, $categories){
		$valid = true;
		$conditional_rules = $this->conditional_rules;
		if(!empty($conditional_rules)){
			foreach($conditional_rules as $conditional_rule){				
				if(!$conditional_rule->is_satisfied($product, $categories)){
					$valid = false;
				}
			}
		}
		
		$show = true;
		if($this->rules_action === 'hide'){
			$show = $valid ? false : true;
		}else{
			$show = $valid ? true : false;
		}
		
		return $show;
	}
	
	public function prepare_field_html($input_html, $show_price=true){
		$html = '';
		if($input_html){
			$cssclass_str = $this->cssclass_str;
			$conditions_data_str = $this->get_ajax_conditions_data_str();
			if($conditions_data_str){
				$cssclass_str .= empty($cssclass_str) ? 'thwepo-conditional-field' : ' thwepo-conditional-field';
			}
		
			$html .= '<tr class="'. $cssclass_str .'" '.$conditions_data_str.'>';
			$html .= '<td class="label '.$this->title_position.'">'.$this->get_title_html().'</td">';
			$html .= '<td class="value '.$this->title_position.'">';
			$html .= $input_html;
			if($show_price){
				$price_html = $this->get_display_price();
				$html .= $price_html ? ' '.$price_html : '';
			}
			$html .= '</td>';
			$html .= '</tr>';
		}	
		return $html;
	}
	
	public function get_price_data(){
		$price_data_html = '';
		if($this->price_field){
			$price_type = empty($this->price_type) ? 'normal' : $this->price_type;
			$price = is_numeric($this->price) ? $this->price : 0;
			
			$price_data_html  = ' data-price-type="'.$price_type.'"';
			$price_data_html .= ' data-price="'.$price.'"';
			
			if($price_type === 'dynamic' || $price_type === 'dynamic-excl-base-price'){
				$price_unit = is_numeric($this->price_unit) ? $this->price_unit : 0;
				$price_data_html .= ' data-price-unit="'.$price_unit.'"';
			}
		}
		
		return $price_data_html;
	}
	
	public function get_price_data_option($option){
		$price_data_html = '';
		if($this->price_field){
			$price_type = isset($option['price_type']) && !empty($option['price_type']) ? $option['price_type'] : 'normal';
			$price = isset($option['price']) && is_numeric($option['price']) ? $option['price'] : 0;
			
			$price_data_html = '';
			if($price != 0){
				$price_data_html .= ' data-price-type="'.$price_type.'"';
				$price_data_html .= ' data-price="'.$price.'"';
			}
		}
		return $price_data_html;
	}
	
	public function get_display_price(){
		$is_price_field = $this->price_field;
		$price_type = $this->price_type;
		$price = $this->price;
		
		return $this->get_price_html($is_price_field, $price_type, $price);
	}
	public function get_price_html($is_price_field, $price_type, $price){
		$html = '';
		if( $is_price_field ){
			if( $price_type === 'percentage' ){
				$html = $price.'%';
			}else{
				$html = wc_price($price);
			}
		}
		return $html;
	}
	
	public function get_title_html(){
		$title_html = '';
		if($this->title){
			$title_type  = $this->title_type ? $this->title_type : 'label';
			$title_style = $this->title_color ? 'style="color:'.$this->title_color.';"' : '';
			
			$title_html .= '<'.$this->title_type.' class="'.$this->title_class_str.'" '.$title_style.'>'. $this->esc_html__wepo($this->title) .'</'.$this->title_type.'>';
		}
		
		$subtitle_html = '';
		if($this->subtitle){
			$subtitle_type  = $this->subtitle_type ? $this->subtitle_type : 'span';
			$subtitle_style = $this->subtitle_color ? 'font-size:80%; color:'.$this->subtitle_color.';' : 'font-size:80%;';
			
			$subtitle_html .= '<'.$subtitle_type.' class="'.$this->subtitle_class_str.'" style="'.$subtitle_style.'">';
			$subtitle_html .= $this->esc_html__wepo($this->subtitle) .'</'.$subtitle_type.'>';
		}
		
		$html = $title_html;
		if(!empty($subtitle_html)){
			$html .= '<br/>'.$subtitle_html;
		}
	
		return $html;
	}
	
	public function convert_to_cssclass_string($cssclass){
		$cssclass_str = '';
		if(!empty($cssclass)){
			$class_arr = explode(',', $cssclass);
			$cssclass_str = implode(' ', $class_arr);
		}
		return $cssclass_str;
	}
	
	public function get_ajax_conditions_data_str(){
		$data_str = false;
		if($this->conditional_rules_ajax_json){
			$rules_action = $this->rules_action_ajax ? $this->rules_action_ajax : 'show';
			$rules = urldecode($this->conditional_rules_ajax_json);
			$rules = esc_js($rules);
			
			$data_str = 'id="'.$this->name.'_field" data-rules="'. $rules .'" data-rules-action="'. $rules_action .'"';
		}
		return $data_str;
	}
	
	public function get_html(){
		return '';
	}
	
	public function render_field(){
		echo $this->get_html();
	}
	
   /***********************************
	**** Setters & Getters - START ****
	***********************************/
	public function set_property($name, $value){
		$this->$name = $value;
	}
	
	public function get_property($name){
		if(property_exists($this, $name)){
			return $this->$name;
		}else{
			return '';
		}
	}
	
	public function get_display_label(){
		$label = !empty($this->title) ? $this->title : $this->placeholder;
		$label = !empty($label) ? $label : $this->name;
		return $label;
	}
	
	public function get_price_final($product_price){
		$fprice = 0;
		if($this->price_type === 'percentage'){
			if(is_numeric($this->price) && is_numeric($product_price)){
				$fprice = ($this->price/100)*$product_price;
			}
		}else if($this->price_type === 'dynamic' || $this->price_type === 'dynamic-excl-base-price'){
			if(is_numeric($this->price) && is_numeric($this->value) && is_numeric($this->price_unit) && $this->price_unit > 0){
				$fprice = $this->price*($this->value/$this->price_unit);
				
				if($this->price_type === 'dynamic-excl-base-price' && is_numeric($product_price) && $this->value >= $this->price_unit){
					$fprice = $fprice - $product_price;
				}
			}
		}else{
			$fprice = $this->price;
		}
		return $fprice;
	}
	/**********************************
	**** Setters & Getters - END ******
	***********************************/
}

endif;