<?php
/**
 * 
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Condition_Set')):

class WEPO_Condition_Set {
	const LOGIC_AND = 'and';
	const LOGIC_OR  = 'or';
	
	//public $action = '';
	public $logic = self::LOGIC_AND;
	public $conditions = array();
	
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
		$conditions = $this->get_conditions();
		if(!empty($conditions)){			 
			if($this->get_logic() === self::LOGIC_AND){			
				foreach($conditions as $condition){				
					if(!$condition->is_satisfied($product, $categories)){
						$satisfied = false;
						break;
					}
				}
			}else if($this->get_logic() === self::LOGIC_OR){
				$satisfied = false;
				foreach($conditions as $condition){				
					if($condition->is_satisfied($product, $categories)){
						$satisfied = true;
						break;
					}
				}
			}
		}
		return $satisfied;
	}
	
	public function add_condition($condition){
		if(isset($condition) && $condition instanceof WEPO_Condition && $condition->is_valid()){
			$this->conditions[] = $condition;
		} 
	}
	
	public function set_logic($logic){
		$this->logic = $logic;
	}	
	public function get_logic(){
		return $this->logic;
	}
		
	public function set_conditions($conditions){
		$this->conditions = $conditions;
	}	
	public function get_conditions(){
		return $this->conditions; 
	}	
}

endif;