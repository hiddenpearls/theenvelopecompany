<?php 

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Ajax_Listener')):
class WCFE_Ajax_Listener {	
	function __construct() {		
		add_action("wp_ajax_thwcfe_ajax", array( $this, "listen" ) );		
	}
	
	function listen() {
		/* Parse the incoming request */
		wccpf()->request = apply_filters( 'thwcfe_request', array() );		
		/* Handle the request */
		$this->handleRequest();
		/* Respond the request */
		echo wccpf()->response;
		/* end the request - response cycle */
		die();
	}
	
	function handleRequest() {				
		if( wccpf()->request["context"] == "product" ) {		
			if( wccpf()->request["type"] == "GET" ) {
				$products = apply_filters( 'wccpf/build/products_list', "wccpf_condition_value" );				
				wccpf()->response = apply_filters( 'wccpf/response', true, "Success", $products );	
			}			
		} else if( wccpf()->request["context"] == "product_cat" ) {
			if( wccpf()->request["type"] == "GET" ) {
				$products_cat = apply_filters( 'wccpf/build/products_cat_list', "wccpf_condition_value" );
				wccpf()->response = apply_filters( 'wccpf/response', true, "Success", $products_cat );
			}
		}		
	}	
}

/* Init wepo ajax object */
new WCFE_Ajax_Listener();

endif;