<?php
/**
 * Customer custom completed order email
 *
 * @see 	    
 * @author 		Oktara
 * @package 	1.0
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf("Thank you for your order! You will receive a detailed order acknowledgment within 1 business day."); ?></p>

<?php
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
