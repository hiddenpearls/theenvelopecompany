<?php
/**
 * 
 *
 * @author    ThemeHiGH
 * @category  Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Checkout_Section')):

class WCFE_Checkout_Section {
	public $id = '';
	public $name = '';
	public $position = '';
	public $type = '';
	public $cssclass = '';
	public $enabled = true;
	
	public $custom_section = 1;
	public $show_title = 1;
		
	public $title = '';
	public $title_type  = '';
	public $title_color = '';
	public $title_position = '';
	public $title_class = '';
	
	public $subtitle = '';
	public $subtitle_type  = '';
	public $subtitle_color = '';
	public $subtitle_position = '';
	public $subtitle_class = '';
	
	public $fields = array();
	public $condition_sets = array();
	
	public function __construct() {
		
	}	
	
	public function populate_section($options){
		if(isset($options) && is_array($options)){
			$this->id    = isset($options['name']) ? $options['name'] : '';
			$this->name  = isset($options['name']) ? $options['name'] : '';
			$this->title = isset($options['title']) ? $options['title'] : '';		    
		}
	}
	
	public function clear_fields(){
		$this->fields = array();
	}
	
	public function add_field($field){
		if($field){
			$this->fields[$field->get_property('name')] = $field;
		}
	}
	
	public function set_fields($fields){
		$this->fields = $fields;
	}
	public function get_fields(){
		return (is_array($this->fields) && !empty($this->fields)) ? $this->fields : array();
	}
	
	public function get_fieldset($products, $categories){
		$fieldset = array();
		if(is_array($this->fields) && !empty($this->fields)){
			foreach($this->fields as $name => $field){
				if($this->is_valid_field($field)){
					if($field->get_property('enabled')){
						if($field->show_field($products, $categories)){
							//$field_props = $field->get_field_array();
							$field_props = $field->get_property_set();
							$fieldset[$name] = $field_props; 
						}
					}
				} 
			}
		}
		return $fieldset;
	}
	
	public function is_valid_field($field){
		if(isset($field) && $field instanceof WCFE_Checkout_Field && $field->is_valid()){
			return true;
		} 
		return false;
	}

	public function get_title_html(){
		$title_html = '';
		if($this->get_title()){
			$title_type  = $this->get_title_type() ? $this->get_title_type() : 'label';
			$title_style = $this->get_title_color() ? 'style="color:'.$this->get_title_color().';"' : '';
			
			$title_html .= '<'.$title_type.' class="'.$this->get_title_class().'" '.$title_style.'>'.$this->get_title().'</'.$title_type.'>';
		}
		
		$subtitle_html = '';
		if($this->get_subtitle()){
			$subtitle_type  = $this->get_subtitle_type() ? $this->get_subtitle_type() : 'span';
			$subtitle_style = $this->get_subtitle_color() ? 'font-size:80%; style="color:'.$this->get_subtitle_color().';"' : 'font-size:80%;';
			
			$subtitle_html .= '<'.$subtitle_type.' class="'.$this->get_subtitle_class().'" '.$subtitle_style.'>'.$this->get_subtitle().'</'.$subtitle_type.'>';
		}
		
		$html = $title_html;
		if(!empty($subtitle_html)){
			$html .= $subtitle_html;
		}
		return $html;
	}
	
	public function get_html(){
		$fields = $this->get_fields();
		$html = '';
		if($fields){
			$html .= '<table class="extra-options" cellspacing="0"><tbody>';
			
			if($this->is_show_title()){
				$html .= $this->get_title_html();
			}
			
			foreach($fields as $field){
				$html .= $field->get_html();
			}		
			$html .= '</tbody></table>';
		}
		
		return $html;
	}
	
	public function render_section(){		
		echo $this->get_html();
	}
	
	public function is_valid(){
		if(empty($this->name)){
			return false;
		}
		return true;
	}
	
	public function has_fields(){
		if($this->get_fields()){
			return true;
		}
		return false;
	}
	
	public function add_condition_set($condition_set){
		$this->condition_sets[] = $condition_set;
	}
	
	public function show_section($product, $categories){
		$show = true;
		if(!empty($this->condition_sets)){			
			foreach($this->condition_sets as $condition_set){
				if($condition_set->show_element()){
					$show = false;
				}
			}
		}
		return $show;
	}
	
	public function sort_fields(){
		uasort($this->fields, array($this, 'sort_by_order'));
	}
	
	public function sort_by_order($a, $b){
	    if($a->get_property('order') == $b->get_property('order')){
	        return 0;
	    }
	    return ($a->get_property('order') < $b->get_property('order')) ? -1 : 1;
	}
	
   /***********************************
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
	
	
	
	
	public function set_id($id){
		$this->id = $id;
	}	
	public function set_name($name){
		$this->name = $name;
	}	
	public function set_position($position){
		$this->position = $position;
	}	
	public function set_type($type){
		$this->type = $type;
	}	
	public function set_cssclass($cssclass){
		$this->cssclass = $cssclass;
	}

	public function set_custom_section($custom_section){
		$this->custom_section = $custom_section;
	}
	public function set_show_title($show_title){
		$this->show_title = $show_title;
	}
	
	public function set_title($title){
		$this->title = $title;
	}
	public function set_title_type($title_type){
		$this->title_type = $title_type;
	}
	public function set_title_color($title_color){
		$this->title_color = $title_color;
	}
	public function set_title_position($title_position){
		$this->title_position = $title_position;
	}
	public function set_title_class($title_class){
		$this->title_class = $title_class;
	}
	
	public function set_subtitle($subtitle){
		$this->subtitle = $subtitle;
	}
	public function set_subtitle_type($subtitle_type){
		$this->subtitle_type = $subtitle_type;
	}
	public function set_subtitle_color($subtitle_color){
		$this->subtitle_color = $subtitle_color;
	}
	public function set_subtitle_position($subtitle_position){
		$this->subtitle_position = $subtitle_position;
	}
	public function set_subtitle_class($subtitle_class){
		$this->subtitle_class = $subtitle_class;
	}

	/*** Getters ***/	
	public function get_id(){
		return $this->id;
	}	
	public function get_name(){
		return $this->name;
	}	
	public function get_position(){
		return $this->position;
	}	
	public function get_type(){
		return $this->type;
	}	
	public function get_cssclass(){
		return $this->cssclass;
	}
	
	public function is_default_section(){
		return !$this->custom_section;
	}
	public function is_custom_section(){
		return $this->custom_section;
	}
	public function is_show_title(){
		return $this->show_title;
	}
	
	public function get_title(){
		return $this->title;
	}
	public function get_title_type(){
		return $this->title_type;
	}
	public function get_title_color(){
		return $this->title_color;
	}
	public function get_title_position(){
		return $this->title_position;
	}
	public function get_title_class(){
		return $this->title_class;
	}
	
	public function get_subtitle(){
		return $this->subtitle;
	}
	public function get_subtitle_type(){
		return $this->subtitle_type;
	}
	public function get_subtitle_color(){
		return $this->subtitle_color;
	}
	public function get_subtitle_position(){
		return $this->subtitle_position;
	}
	public function get_subtitle_class(){
		return $this->subtitle_class;
	}
    /**********************************
	**** Setters & Getters - END ******
	***********************************/
}

endif;