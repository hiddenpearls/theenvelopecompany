<?php 
if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Ajax_Request')):

class WEPO_Ajax_Request {
	function __construct() {
		add_filter( 'thwepo_request', array( $this, 'prepare_request' ) );
	}
	
	function prepare_request() {
		if( isset( $_REQUEST["THWEPO_AJAX_PARAM"] ) ) {	
			$payload = json_decode( str_replace('\"','"', $_REQUEST["THWEPO_AJAX_PARAM"] ), true );			
			return array (
				"type" => $payload["request"],
				"context" => $payload["context"],
				"payload" => $payload["payload"]
			);
		}
	}
}
new WEPO_Ajax_Request();

endif;