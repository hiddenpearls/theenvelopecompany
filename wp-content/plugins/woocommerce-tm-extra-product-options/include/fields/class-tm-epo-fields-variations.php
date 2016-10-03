<?php
class TM_EPO_FIELDS_variations extends TM_EPO_FIELDS {

	public function display_field( $element=array(), $args=array() ) {
		$display =  array(
				'builder'   	=> $element['builder'],
			);
		
		return $display;
	}

	public function validate() {
		return array('passed'=>true,'message'=>false);
	}
	
}