<?php 

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Ajax_Response')):

class WCFE_Ajax_Response {	
	function __construct() {
		add_filter( 'thwcfe_response', array( $this, 'prepare_response' ), 5, 3 );
	}	
	
	function prepare_response( $status, $msg, $data ) {
		return json_encode( array ( 
			"status" => $status, 
			"message"=>$msg, 
			"data"=>$data )
		);
	}	
}
new WCFE_Ajax_Response();

endif;