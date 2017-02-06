<?php
/**
 * 
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Condition')):

class WCFE_Condition {
	const PRODUCT = 'product';
	const CATEGORY = 'category';
	const FIELD = 'field';
	
	const CART_CONTAINS = 'cart_contains'; 
	const CART_NOT_CONTAINS = 'cart_not_contains'; 
	const CART_ONLY_CONTAINS = 'cart_only_contains';
	
	const CART_TOTAL_EQ = 'cart_total_eq'; 
	const CART_TOTAL_GT = 'cart_total_gt'; 
	const CART_TOTAL_LT = 'cart_total_lt';
	
	/*const COUNT_EQ = 'count_eq'; 
	const COUNT_GT = 'count_gt'; 
	const COUNT_LT = 'count_lt';*/
		
	const VALUE_EMPTY = 'empty';
	const VALUE_NOT_EMPTY = 'not_empty';
	
	const VALUE_EQ = 'value_eq';
	const VALUE_NE = 'value_ne'; 
	const VALUE_GT = 'value_gt'; 
	const VALUE_LT = 'value_le';
	
	public $operand_type = '';
	public $operand = '';
	public $operator = '';
	public $value = '';
	
	public $show_when_str = '';
		
	public function __construct() {
		
	}	
	
	public function is_valid(){
		if(!empty($this->operand_type) && !empty($this->operator)){
			return true;
		}else if(!empty($this->operator) && in_array($this->operator, array(self::CART_TOTAL_EQ, self::CART_TOTAL_GT, self::CART_TOTAL_LT)) && !empty($this->operand)){
			return true;
		}
		return false;
	}
	
	public function is_subset_of($arr1, $arr2){
		foreach($arr2 as $value){
			if(!in_array($value, $arr1)){
				return false;
			}
		}
		return true;
	}
	
	public function is_satisfied($products, $categories){
		$satisfied = true;
		if($this->is_valid()){
			$operands = $this->operand;
			
			if($this->operand_type == self::PRODUCT){
				$intersection = array_intersect($products, $operands);
				
				if($this->operator == self::CART_CONTAINS) {
					if(!$this->is_subset_of($products, $operands)){
					//if($intersection != $values){
						return false;
					}
				}else if($this->operator == self::CART_NOT_CONTAINS){
					if(!empty($intersection)){
						return false;
					}
				}else if($this->operator == self::CART_ONLY_CONTAINS){
					if($products != $operands){
						return false;
					}
				}
			}else if($this->operand_type == self::CATEGORY){
				$intersection = array_intersect($categories, $operands);
				
				if($this->operator == self::CART_CONTAINS) {
					if(!$this->is_subset_of($categories, $operands)){
						return false;
					}
				}else if($this->operator == self::CART_NOT_CONTAINS){
					if(!empty($intersection)){
						return false;
					}
				}else if($this->operator == self::CART_ONLY_CONTAINS){
					if($categories != $operands){
						return false;
					}
				}
			}else{
				if(is_numeric($operands)){
					if($this->operator == self::CART_TOTAL_EQ){
						$cart_total = WC()->cart->subtotal;
						
						if($cart_total != $operands){
							return false;
						}
					}else if($this->operator == self::CART_TOTAL_GT){
						$cart_total = WC()->cart->subtotal;
						
						if($cart_total <= $operands){
							return false;
						}
					}else if($this->operator == self::CART_TOTAL_LT){
						$cart_total = WC()->cart->subtotal;
						
						if($cart_total >= $operands){
							return false;
						}
					}
				}
			}
				/*else if($this->operator == self::EMPTY){
					
				}else if($this->operator == self::NOT_EMPTY){
					
				}*/
			
			
		}
		return $satisfied;
	}
			
	/*public function is_satisfied($product, $categories){
		$satisfied = true;
		if($this->is_valid()){
			if($this->operand_type == self::PRODUCT){
				if($this->operator == self::EQUALS) {
					if($this->value != $product){
						return false;
					}
				}else if($this->operator == self::NOT_EQUALS){
					if($this->value == $product){
						return false;
					}
				}
			}else if($this->operand_type == self::CATEGORY){
				if($this->operator == self::EQUALS) {
					if(!in_array($this->value, $categories)){
						return false;
					}
				}else if($this->operator == self::NOT_EQUALS){
					if(in_array($this->value, $categories)){
						return false;
					}
				}
			}
		}
		return $satisfied;
	}*/
	
	public function set_operand_type($operand_type){
		$this->operand_type = $operand_type;
	}	
	public function get_operand_type(){
		return $this->operand_type;
	}
	
	public function set_operand($operand){
		$this->operand = $operand;
	}	
	public function get_operand(){
		return $this->operand;
	}
	
	public function set_operator($operator){
		$this->operator = $operator;
	}	
	public function get_operator(){
		return $this->operator;
	}
	
	public function set_value($value){
		$this->value = $value;
	}	
	public function get_value(){
		return $this->value;
	}
}

endif;