<?php
/**
 * Checkout Field Properties
 *
 * @author    ThemeHiGH
 * @category  Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Checkout_Field')):

class WCFE_Checkout_Field extends WCFE_Checkout_Fields_Utils{
	public $custom_field = 0;
	public $order = '';
	public $id   = '';
	
	public $name = '';	
	public $type = '';
	public $order_meta = 1;
	public $user_meta = 0;
	
	public $value = '';
	public $placeholder = '';
	public $validate = '';
	public $cssclass = '';
	//public $cssclass_str = '';
	
	public $price_field = false;
	public $price = 0;
	public $price_unit = 0;
	public $price_type = '';
	
	public $required = 0;
	public $enabled = 1;
	public $clear = 0;
	
	public $show_in_email = 1;
	public $show_in_order = 1;
	
	public $title = '';
	public $title_type  = '';
	public $title_color = '';
	public $title_class = '';
	///public $title_class_str = '';
	
	public $subtitle = '';
	public $subtitle_type  = '';
	public $subtitle_color = '';
	public $subtitle_class = '';
	//public $subtitle_class_str = '';
	
	public $minlength = '';
	public $maxlength = '';
	public $repeat_x = 1;
	
	public $options_json = '';
	public $options = array();
	
	//public $validator_arr = array();
	
	public $rules_action = '';
	public $rules_action_ajax = '';
	
	public $conditional_rules_json = '';
	public $conditional_rules = array();
	
	public $conditional_rules_ajax_json = '';
	public $conditional_rules_ajax = array();
	
	public $property_set = false;
		
	public function __construct() {
		
	}	
	
	public $boolean_props = array('custom_field', 'order_meta', 'user_meta', 'price_field', 'checked', 'required', 'enabled', 'clear', 'show_in_email', 'show_in_order');
	public $array_props = array('class', 'label_class', 'title_class', 'subtitle_class', 'validate');
	
	public $woo_default_field_props = array(
		'type'        => array('name'=>'type', 'value'=>'text'),
		'label' 	  => array('name'=>'title', 'value'=>''),
		//'description' => array('name'=>'subtitle', 'value'=>''),
		'placeholder' => array('name'=>'placeholder', 'value'=>''),
		
		'class' 	  => array('name'=>'cssclass', 'value'=>array()),
		'label_class' => array('name'=>'title_class', 'value'=>array()),
		
		'custom' 	  => array('name'=>'custom_field', 'value'=>0),
		'value' 	  => array('name'=>'value', 'value'=>''),
		'default' 	  => array('name'=>'value', 'value'=>''),
		'validate'	  => array('name'=>'validate', 'value'=>array()),
		
		'required' 	  => array('name'=>'required', 'value'=>0),
		'clear' 	  => array('name'=>'clear', 'value'=>0),
		'enabled' 	  => array('name'=>'enabled', 'value'=>1),
		
		'show_in_email' => array('name'=>'show_in_email', 'value'=>1),
		'show_in_order' => array('name'=>'show_in_order', 'value'=>1),
		'order' 	    => array('name'=>'order', 'value'=>''),
		
		/*$defaults = array(
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
		);*/
	);
	
	public $field_properties = array(
		'type' => array('name'=>'type', 'value'=>''),
		'name' => array('name'=>'name', 'value'=>''),
		'label' => array('name'=>'title', 'value'=>''),
		//'description' => array('name'=>'subtitle', 'value'=>''),
		'label_class' => array('name'=>'title_class', 'value'=>array(), 'value_type'=>'array'),
		'default'	  => array('name'=>'value', 'value'=>''),
		'validate'	  => array('name'=>'validate', 'value'=>array(), 'value_type'=>'array'),
	
		'placeholder' => array('name'=>'placeholder', 'value'=>''),
		'class' 	  => array('name'=>'cssclass', 'value'=>array(), 'value_type'=>'array'),
		
		'order_meta' => array('name'=>'order_meta', 'value'=>1),
		'user_meta'  => array('name'=>'user_meta', 'value'=>0),
		
		'checked'  => array('name'=>'checked', 'value'=>1),
		'required' => array('name'=>'required', 'value'=>0),
		'clear'    => array('name'=>'clear', 'value'=>0),
		'enabled'  => array('name'=>'enabled', 'value'=>1),
		
		'price' 	 => array('name'=>'price', 'value'=>''),
		'price_type' => array('name'=>'price_type', 'value'=>''),
		
		'title' 	  => array('name'=>'title', 'value'=>''),
		'title_type'  => array('name'=>'title_type', 'value'=>''),
		'title_color' => array('name'=>'title_color', 'value'=>''),
		'title_class' => array('name'=>'title_class', 'value'=>array(), 'value_type'=>'array'),
		
		'subtitle' 		 => array('name'=>'subtitle', 'value'=>''),
		'subtitle_type'  => array('name'=>'subtitle_type', 'value'=>''),
		'subtitle_color' => array('name'=>'subtitle_color', 'value'=>''),
		'subtitle_class' => array('name'=>'subtitle_class', 'value'=>array(), 'value_type'=>'array'),
		
		'minlength' => array('name'=>'minlength', 'value'=>''),
		'maxlength' => array('name'=>'maxlength', 'value'=>''),
		'repeat_x'  => array('name'=>'repeat_x', 'value'=>1),
		
		'date_format' 	=> array('name'=>'date_format', 'value'=>''),
		'default_date' 	=> array('name'=>'default_date', 'value'=>''),
		'max_date' 	  	=> array('name'=>'max_date', 'value'=>''),
		'min_date' 	    => array('name'=>'min_date', 'value'=>''),
		'year_range' 	=> array('name'=>'year_range', 'value'=>''),
		'number_months' => array('name'=>'number_months', 'value'=>''),
		'disabled_days' => array('name'=>'disabled_days', 'value'=>'', 'value_type'=>'array'),
		'disabled_dates' => array('name'=>'disabled_dates', 'value'=>''),
		
		'min_time' 	  => array('name'=>'min_time', 'value'=>''),
		'max_time' 	  => array('name'=>'max_time', 'value'=>''),
		'time_step'   => array('name'=>'time_step', 'value'=>''),
		'time_format' => array('name'=>'time_format', 'value'=>''),
	);
	
	public function prepare_properties(){
		$this->name = urldecode( sanitize_title(wc_clean($this->name)) );
		$this->id = $this->name;
		
		//$this->cssclass_str = $this->convert_to_cssclass_string($this->cssclass);
		//$this->title_class_str = $this->convert_to_cssclass_string($this->title_class);
		//$this->subtitle_class_str = $this->convert_to_cssclass_string($this->subtitle_class);
		
		if($this->type === 'radio' || $this->type === 'select' || $this->type === 'multiselect'){
			foreach($this->options as $option_key => $option){
				if(isset($option['price']) && is_numeric($option['price']) && $option['price'] != 0){
					$this->price_field = 1;
					break;
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
		
		$this->property_set = $this->get_property_array();
	}
	
	public function is_valid(){
		if(empty($name)){
			//return false;
		}
		return true;
	}
	
	public function prepare_field($name, $field){
		if(!empty($field) && is_array($field)){
			$this->set_property('id', $name);
			$this->set_property('name', $name);
			
			foreach($this->woo_default_field_props as $pname => $property){
				$pvalue = isset($field[$pname]) ? $field[$pname] : $property['value'];
				$pvalue = is_array($pvalue) ? implode(',', $pvalue) : $pvalue;
				
				$this->set_property($property['name'], $pvalue);
			}
			
			if(isset($field['options']) && is_array($field['options'])){
				$options_object = array();
				foreach($field['options'] as $option_key => $option_text){
					$option_object = array();
					$option_object['key'] = $option_key;
					$option_object['text'] = $option_key;
					
					$options_object[] = $option_object;
					$this->set_property( 'options', $options_object );
				}
			}else{
				$this->set_property( 'options', array() );
			}
			//$this->set_address_field( isset($field['is_address_field']) ? $field['is_address_field'] : array() ); TODO
		}
	}
	
	public function get_property_array(){
		if($this->is_valid()){
			$optionsObj = $this->get_property('options');
			$options = array();
			foreach($options as $option){
				$options[$option['key']] = $option['text'];
			}
			
			$field = array();
			foreach($this->field_properties as $pname => $props){
				$fvalue = $this->get_property($props['name']);
				
				if(in_array($pname, $this->array_props) && !empty($fvalue)){
					$fvalue = is_array($fvalue) ? $fvalue : explode(',', $fvalue);
				}
				
				if(!in_array($pname, $this->boolean_props)){
					$fvalue = empty($fvalue) ? $props['value'] : $fvalue;
				}
				
				$field[$pname] = $fvalue;
			}
			
			$field['custom'] = $this->is_custom_field();
			$field['label'] = $this->__wcfe($field['label']);
			//$field['description'] = $this->__wcfe($field['description']);
			$field['placeholder'] = $this->__wcfe($field['placeholder']);
			
			$field['options'] = $options;
			$field['options_object'] = $optionsObj;
			$field['rules_action'] = $this->get_property('rules_action_ajax'); 
			$field['rules'] = $this->get_property('conditional_rules_ajax_json'); 
			
			return $field;
		}else{
			return false;
		}
	}
	
	public function show_field($products, $categories){
		$valid = true;
		$conditional_rules = $this->get_property('conditional_rules');
		
		if(!empty($conditional_rules)){
			foreach($conditional_rules as $conditional_rule){				
				if(!$conditional_rule->is_satisfied($products, $categories)){
					$valid = false;
				}
			}
		}
		
		$show = true;
		if($this->get_property('rules_action') === 'hide'){
			$show = $valid ? false : true;
		}else{
			$show = $valid ? true : false;
		}
		
		return $show;
	}
	
	public function get_title_html(){
		$title_html = '';
		if($this->get_property('title')){
			$title_type  = $this->get_property('title_type') ? $this->get_property('title_type') : 'label';
			$title_style = $this->get_property('title_color') ? 'style="color:'.$this->get_property('title_color').';"' : '';
			
			$title_html .= '<'.$title_type.' class="'.$this->get_property('title_class_str').'" '.$title_style.'>'.$this->get_title().'</'.$title_type.'>';
		}
		
		$subtitle_html = '';
		if($this->get_subtitle()){
			$subtitle_type  = $this->get_property('subtitle_type') ? $this->get_property('subtitle_type') : 'span';
			$subtitle_style = $this->get_property('subtitle_color') ? 'font-size:80%; style="color:'.$this->get_property('subtitle_color').';"' : 'font-size:80%;';
			
			$subtitle_html .= '<'.$subtitle_type.' class="'.$this->get_property('subtitle_class_str').'" '.$subtitle_style.'>';
			$subtitle_html .= $this->get_subtitle().'</'.$subtitle_type.'>';
		}
		
		$html = $title_html;
		if(!empty($subtitle_html)){
			$html .= '<br/>'.$subtitle_html;
		}
		
		/*if(!empty($html)){
			$html = '<td class="'. $this->get_cssclass_str() .'">'.$html.'</td">';
		}else{
			$html = '<td class="'. $this->get_cssclass_str() .'">&nbsp;</td">';
		}*/		
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
	
   /**********************************
	**** Setters & Getters - START ****
	***********************************/
	public function set_property($name, $value){
		if(property_exists($this, $name)){
			$this->$name = $value;
		}
	}
	
	public function get_property($name){
		if(property_exists($this, $name)){
			return $this->$name;
		}else{
			return '';
		}
	}
	
	public function is_custom_field(){
		return $this->custom_field;
	}
	
	public function is_enabled(){
		return $this->enabled;
	}
	
	public function get_property_set(){
		if(!is_array($this->property_set)){
			$this->property_set = $this->get_property_array();
		}
		return $this->property_set;
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