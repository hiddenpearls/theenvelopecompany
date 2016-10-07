<?php
/**
 * 
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Condition_Rule')):

class WEPO_Condition_Rule {
	const LOGIC_AND = 'and';
	const LOGIC_OR  = 'or';
	
	//public $action = '';
	public $logic = self::LOGIC_OR;
	public $condition_sets = array();
	
	public function __construct() {
		/*$this->$condition_sets = array(
			'op' => 'or'
			'sets' = array(
				'op' => 'and'
				'or_sets' = array(
					'conditions' => array(
						'op' => 'and', 
						'conditions' => array('condition2')
					)
				)					
			),
		);*/
	}	
	
	/*public function show_element(){
		if($action == 'show'){
			return $this->is_satisfied();
		}else if($action == 'hide'){
			return $this->is_satisfied() ? false : true;
		}
		return true;
	}*/
	
	public function is_satisfied($product, $categories){
		$satisfied = true;
		$condition_sets = $this->get_condition_sets();
		if(!empty($condition_sets)){
			if($this->get_logic() === self::LOGIC_AND){			
				foreach($condition_sets as $condition_set){				
					if(!$condition_set->is_satisfied($product, $categories)){
						$satisfied = false;
						break;
					}
				}
			}else if($this->get_logic() === self::LOGIC_OR){
				$satisfied = false;
				foreach($condition_sets as $condition_set){				
					if($condition_set->is_satisfied($product, $categories)){
						$satisfied = true;
						break;
					}
				}
			}			
		}
		return $satisfied;
	}
	
	public function add_condition_set($condition_set){
		if(isset($condition_set) && $condition_set instanceof WEPO_Condition_Set){
			$this->condition_sets[] = $condition_set;
		} 
	}
	
	public function set_logic($logic){
		$this->logic = $logic;
	}	
	public function get_logic(){
		return $this->logic;
	}
		
	public function set_condition_sets($condition_sets){
		$this->condition_sets = $condition_sets;
	}	
	public function get_condition_sets(){
		return $this->condition_sets; 
	}	
}

endif;