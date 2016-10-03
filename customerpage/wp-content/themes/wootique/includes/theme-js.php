<?php
if (!is_admin()) add_action( 'wp_print_scripts', 'woothemes_add_javascript' );
if (!function_exists('woothemes_add_javascript')) {
	function woothemes_add_javascript( ) {
		
		global $woo_options;
		
		wp_enqueue_script( 'jquery' );    
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'superfish', get_template_directory_uri().'/includes/js/superfish.js', array( 'jquery' ) );
		wp_enqueue_script( 'wootabs', get_template_directory_uri().'/includes/js/woo_tabs.js', array( 'jquery' ) );
		wp_enqueue_script( 'general', get_template_directory_uri().'/includes/js/general.js', array( 'jquery' ) );		
		wp_enqueue_script( 'uniform', get_template_directory_uri().'/includes/js/jquery.uniform.min.js', array( 'jquery' ) );
				
		// Featured slider
		if ( (is_home() || is_front_page()) && !is_paged() ) {
			wp_enqueue_script( 'jcarousel', get_template_directory_uri().'/includes/js/jquery.jcarousel.min.js', array( 'jquery' ) );
		}

	}
}

?>