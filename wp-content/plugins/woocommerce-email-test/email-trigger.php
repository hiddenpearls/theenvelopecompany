<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// run the script based on the trigger GET value populated

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	add_filter( 'query_vars', 'plugin_add_trigger' );
	function plugin_add_trigger( $vars ) {

		$vars[] = 'woocommerce_email_test';
		return $vars;
		
	}	
		
	add_action( 'template_redirect', 'plugin_trigger_check' );
	function plugin_trigger_check() {

		if( get_query_var( 'woocommerce_email_test' ) ) {
		
			// run the email script based on the woocommerce_email_test class	
			run_email_script();
			
			exit();
			
		}
	}

}