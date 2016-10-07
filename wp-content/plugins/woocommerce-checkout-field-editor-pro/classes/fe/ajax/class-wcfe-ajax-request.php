<?php 
if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Ajax_Request')):

class WCFE_Ajax_Request {
	function __construct() {
		add_filter( 'thwcfe_request', array( $this, 'prepare_request' ) );
	}
	
	function prepare_request() {
		if( isset( $_REQUEST["THWCFE_AJAX_PARAM"] ) ) {	
			$payload = json_decode( str_replace('\"','"', $_REQUEST["THWCFE_AJAX_PARAM"] ), true );			
			return array (
				"type" => $payload["request"],
				"context" => $payload["context"],
				"payload" => $payload["payload"]
			);
		}
	}
}
new WCFE_Ajax_Request();

endif;