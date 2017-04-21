<?php
/**
 *
 *
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo "= " . $email_heading . " =\n\n";
?>
<p><?php printf( __( "Hi there. Your recent order on %s has been completed.  You will receive a detailed order acknowledgment within 1 business day.", 'woocommerce' ), get_option( 'blogname' ) ); ?></p>

<?php
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );