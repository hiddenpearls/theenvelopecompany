<?php
/**
 * Product Field - Heading
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Product_Field_Heading')):

class WEPO_Product_Field_Heading extends WEPO_Product_Field{
	public function __construct() {
		$this->type = 'heading';
	}	
		
	public function get_html(){
		$html = '';
		if($this->enabled){
			$title_html = '';
			if($this->title){
				$title_type  = $this->title_type ? $this->title_type : 'label';
				$title_style = $this->title_color ? 'style="color:'.$this->title_color.';"' : '';
				
				$title_html .= '<'.$title_type.' id="'.$this->name.'_title" class="'.$this->title_class_str.'" '.$title_style.'>';
				$title_html .= $this->esc_html__wepo($this->title);
				$title_html .= '</'.$title_type.'>';
			}
			
			$subtitle_html = '';
			if($this->subtitle){
				$subtitle_type  = $this->subtitle_type ? $this->subtitle_type : 'span';
				$subtitle_style = $this->subtitle_color ? 'style="font-size:80%; color:'.$this->subtitle_color.';"' : 'style="font-size:80%;"';
				
				$subtitle_html .= '<'.$subtitle_type.' id="'.$this->name.'_subtitle" class="'.$this->subtitle_class_str.'" '.$subtitle_style.'>';
				$subtitle_html .= $this->esc_html__wepo($this->subtitle);
				$subtitle_html .= '</'.$subtitle_type.'>';
			}
			
			$html .= $title_html;
			if(!empty($subtitle_html)){
				$html .= '<br/>'.$subtitle_html;
			}
			
			$cssclass_str = $this->cssclass_str;
			$conditions_data_str = $this->get_ajax_conditions_data_str();
			if($conditions_data_str){
				$cssclass_str .= empty($cssclass_str) ? 'thwepo-conditional-field' : ' thwepo-conditional-field';
			}
			
			if(!empty($html)){
				$html = '<tr class="'. $cssclass_str .'" '.$conditions_data_str.'><td colspan="2" class="'. $this->cssclass_str .'">'.$html.'</td"></tr>';
			}else{
				$html = '<tr class="'. $cssclass_str .'" '.$conditions_data_str.'><td colspan="2" class="'. $this->cssclass_str .'">&nbsp;</td"></tr>';
			}		
		}	
		return $html;
	}	
}

endif;