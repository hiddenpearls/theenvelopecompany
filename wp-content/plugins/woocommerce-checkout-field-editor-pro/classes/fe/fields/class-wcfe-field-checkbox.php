<?php
/**
 * Checkout Field - Checkbox
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Checkout_Field_Checkbox')):

class WCFE_Checkout_Field_Checkbox extends WCFE_Checkout_Field{
	public $checked = 0;
	
	public function __construct() {
		$this->type = 'checkbox';
	}	
	
	public function prepare_field($name, $field){
		if(!empty($field) && is_array($field)){
			parent::prepare_field($name, $field);
			
			$this->set_property('checked', isset($field['checked']) ? $field['checked'] : 0 );
		}
	}
	
	/*public function get_html(){
		$html = '';
		if($this->is_enabled()){
			$checked = $this->is_checked();
			$value   = $this->get_value() ? $this->get_value() : 1;
			
			if($this->get_title_position() === 'left'){
				$html .= '<tr class="'. $this->get_cssclass_str() .'"><td colspan="2">';
				$html .= '<label for="'. $this->get_name() .'" class="checkbox '. $this->get_title_class_str() .'">';
				$html .= '<input type="checkbox" id="'.$this->get_name().'" name="'.$this->get_name().'" value="'.$value.'" '. $checked .' class="input-checkbox" /> ';
				$html .= $this->esc_html__wepo($this->get_title()) .'</label>';
				$html .= '</td></tr>';		
			}else{
				$html .= '<tr class="'. $this->get_cssclass_str() .'"><td colspan="2">';
				$html .= '<label for="'. $this->get_name() .'" class="checkbox '. $this->get_title_class_str() .'">';
				$html .= '<input type="checkbox" id="'.$this->get_name().'" name="'.$this->get_name().'" value="'.$value.'" '. $checked .' class="input-checkbox" /> ';
				$html .= $this->esc_html__wepo($this->get_title()) .'</label>';
				$html .= '</td></tr>';	
			}	
		}	
		return $html;
	}
	
	public function render_field(){
		echo $this->get_html();
	}*/
}

endif;