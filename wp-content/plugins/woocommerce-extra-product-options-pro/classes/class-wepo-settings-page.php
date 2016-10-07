<?php
/**
 * Woo Extra Product Options Settings Page
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Settings_Page')) :

abstract class WEPO_Settings_Page extends WEPO_Extra_Product_Options_Utils{
	protected $page_id    = '';	
	protected $section_id = '';
	
	protected $tabs = '';
	protected $sections = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->tabs = array( 'options' => 'Product Options');
	}
	
	public function get_tabs(){
		return $this->tabs;
	}

	public function get_current_tab(){
		return $this->page_id;
	}
	
	public function get_current_section(){
		return isset( $_GET['section'] ) ? esc_attr( $_GET['section'] ) : $this->section_id;
	}
	
	public function output_tabs(){
		$current_tab = $this->get_current_tab();
		$tabs = $this->get_tabs();

		if(empty($tabs)){
			return;
		}
		
		echo '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">';
		foreach( $tabs as $id => $label ){
			$active = ( $current_tab == $id ) ? 'nav-tab-active' : '';
			$label = $this->__wepo($label);
			echo '<a class="nav-tab '.$active.'" href="'. $this->get_admin_url($id) .'">'.$label.'</a>';
		}
		echo '</h2>';		
	}
	
	public function output_sections() {
		$current_section = $this->get_current_section();
		$sections = $this->get_sections();

		if(empty($sections)){
			return;
		}
		
		$array_keys = array_keys( $sections );
		
		echo '<ul class="thwepo-sections">';
		foreach( $sections as $id => $label ){
			$label = $this->__wepo($label);
			//$url = admin_url('edit.php?post_type=product&page=th_extra_product_options_pro&tab='. $this->page_id .'&section='. sanitize_title($id));	
			$url = $this->get_admin_url($this->page_id, sanitize_title($id));	
			echo '<li><a href="'. $url .'" class="'. ( $current_section == $id ? 'current' : '' ) .'">'. $label .'</a> '. (end( $array_keys ) == $id ? '' : '|') .' </li>';
		}		
		echo '</ul>';
	}	
	
	public function get_admin_url($tab = false, $section = false){
		$url = 'edit.php?post_type=product&page=th_extra_product_options_pro';
		if($tab && !empty($tab)){
			$url .= '&tab='. $tab;
		}
		if($section && !empty($section)){
			$url .= '&section='. $section;
		}
		return admin_url($url);
	}
}

endif;