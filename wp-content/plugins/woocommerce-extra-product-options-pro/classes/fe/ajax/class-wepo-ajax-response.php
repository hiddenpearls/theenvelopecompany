<?php 

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Ajax_Response')):

class WEPO_Ajax_Response {	
	function __construct() {
		add_filter( 'thwepo_response', array( $this, 'prepare_response' ), 5, 3 );
	}	
	
	function prepare_response( $status, $msg, $data ) {
		return json_encode( array ( 
			"status" => $status, 
			"message"=>$msg, 
			"data"=>$data )
		);
	}	
}
new WEPO_Ajax_Response();

endif;