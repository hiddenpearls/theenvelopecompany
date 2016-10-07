<?php
/**
 * 
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Product_Page_Section')):

class WEPO_Product_Page_Section extends WEPO_Extra_Product_Options_Utils {
	public $id = '';
	public $name = '';
	public $position = '';
	public $type = '';
	public $cssclass = '';
	
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
	
	public $cssclass_str = '';
	public $title_class_str = '';
	public $subtitle_class_str = '';
	
	public $condition_sets = array();
	public $fields = array();
	
	public function __construct() {
		
	}	
	
	public function set_default_section(){
		$this->id    = 'default';
		$this->name  = 'default';
		$this->title = 'Default';
		$this->show_title = 0;
		$this->position = 'woo_before_add_to_cart_button';
	}
	
	public function populate_section($options){
		if(isset($options) && is_array($options)){
			$this->id    = isset($options['name']) ? $options['name'] : '';
			$this->name  = isset($options['name']) ? $options['name'] : '';
			$this->title = isset($options['title']) ? $options['title'] : '';		    
		}
	}
	
	public function prepare_properties(){
		$this->cssclass_str = str_replace(",", " ", $this->cssclass);
		$this->title_class_str = str_replace(",", " ", $this->title_class);
		$this->subtitle_class_str = str_replace(",", " ", $this->subtitle_class);
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
		return empty($this->fields) ? false : $this->fields;
	}

	public function get_title_html(){
		$title_html = '';
		if($this->title){
			$title_type  = $this->title_type ? $this->title_type : 'label';
			$title_style = $this->title_color ? 'style="color:'.$this->title_color.';"' : '';
			
			$title_html .= '<'.$title_type.' class="'.$this->title_class_str.'" '.$title_style.'>'. $this->esc_html__wepo($this->title) .'</'.$title_type.'>';
		}
		
		$subtitle_html = '';
		if($this->subtitle){
			$subtitle_type  = $this->subtitle_type ? $this->subtitle_type : 'span';
			$subtitle_style = $this->subtitle_color ? 'font-size:80%; style="color:'.$this->subtitle_color.';"' : 'font-size:80%;';
			
			$subtitle_html .= '<'.$subtitle_type.' class="'.$this->subtitle_class_str.'" '.$subtitle_style.'>'. $this->esc_html__wepo($this->subtitle) .'</'.$subtitle_type.'>';
		}
		
		$html = $title_html;
		if(!empty($subtitle_html)){
			$html .= $subtitle_html;
		}
		
		if(!empty($html)){
			$html = '<tr><td colspan="2" class="section-title">'.$html.'</td"></tr>';
		}else{
			$html = '<tr><td colspan="2" class="section-title">&nbsp;</td"></tr>';
		}		
		return $html;
	}
	
	public function get_html(){
		$fields = $this->get_fields();
		$html = '';
		if($fields){
			$html .= '<table class="extra-options '. $this->cssclass_str .'" cellspacing="0"><tbody>';
			
			if($this->get_property('show_title')){
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
		$condition_sets[] = $condition_set;
	}
	
	public function show_section($product, $categories){
		$show = true;
		if(!empty($condition_sets)){			
			foreach($condition_sets as $condition_set){
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
		$this->$name = $value;
	}
	
	public function get_property($name){
		return $this->$name;
	}
	
	public function is_custom_section(){
		return true;
	}
   /***********************************
	**** Setters & Getters - END ******
	***********************************/
}

endif;