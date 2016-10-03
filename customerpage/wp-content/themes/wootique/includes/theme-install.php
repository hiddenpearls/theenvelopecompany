<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Hook in on activation
- Install

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Hook in on activation */
/*-----------------------------------------------------------------------------------*/

global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) add_action('init', 'woo_install_theme', 1);

/*-----------------------------------------------------------------------------------*/
/* Install */
/*-----------------------------------------------------------------------------------*/

function woo_install_theme() {
	
update_option( 'woocommerce_thumbnail_image_width', '200' );
update_option( 'woocommerce_thumbnail_image_height', '200' );
update_option( 'woocommerce_single_image_width', '350' ); // Single 
update_option( 'woocommerce_single_image_height', '350' ); // Single 
update_option( 'woocommerce_catalog_image_width', '200' ); // Catalog 
update_option( 'woocommerce_catalog_image_height', '200' ); // Catlog 

}