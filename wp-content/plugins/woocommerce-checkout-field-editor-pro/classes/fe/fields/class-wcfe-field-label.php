<?php
/**
 * Checkout Field - Password
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Checkout_Field_Label')):

class WCFE_Checkout_Field_Label extends WCFE_Checkout_Field{
	
	public function __construct() {
		$this->type = 'label';
	}	
		
	/*public function get_html(){
		$html = '';
		if($this->is_enabled()){
			$title_html = '';
			if($this->get_title()){
				$title_type  = $this->get_title_type() ? $this->get_title_type() : 'p';
				$title_style = $this->get_title_color() ? 'style="color:'.$this->get_title_color().';"' : '';
				
				$title_html .= '<'.$title_type.' id="'.$this->get_name().'_title" class="'.$this->get_title_class_str().'" '.$title_style.'>';
				$title_html .= $this->__wepo($this->get_title());
				$title_html .= '</'.$title_type.'>';
			}
			
			$subtitle_html = '';
			if($this->get_subtitle()){
				$subtitle_type  = $this->get_subtitle_type() ? $this->get_subtitle_type() : 'span';
				$subtitle_style = $this->get_subtitle_color() ? 'font-size:80%; style="color:'.$this->get_subtitle_color().';"' : 'font-size:80%;';
				
				$subtitle_html .= '<'.$subtitle_type.' id="'.$this->get_name().'_subtitle" class="'.$this->get_subtitle_class_str().'" '.$subtitle_style.'>';
				$subtitle_html .= $this->__wepo($this->get_subtitle());
				$subtitle_html .= '</'.$subtitle_type.'>';
			}
			
			$html .= $title_html;
			if(!empty($subtitle_html)){
				$html .= '<br/>'.$subtitle_html;
			}
			
			if(!empty($html)){
				$html = '<tr><td colspan="2" class="'. $this->get_cssclass_str() .'">'.$html.'</td"></tr>';
			}else{
				$html = '<tr><td colspan="2" class="'. $this->get_cssclass_str() .'">&nbsp;</td"></tr>';
			}		
		}	
		return $html;
	}
	
	public function render_field(){
		echo $this->get_html();
	}	*/
}

endif;