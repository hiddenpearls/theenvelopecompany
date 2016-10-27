<?php
/**
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="container">
	<?php wc_print_notices(); ?>
	<div class="text-center">
		<h3 class="cart-empty">
			<?php _e( 'Your cart is currently empty.', 'woocommerce' ) ?>
		</h3>

		<?php do_action( 'woocommerce_cart_is_empty' ); ?>

		<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
			<p class="return-to-shop">
				<a class="button wc-backward woocommerce-Button" href="/product-category/most-popular-products/">
					<?php //echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>
					<?php _e( 'Return To Shop', 'woocommerce' ) ?>
				</a>
			</p>
		<?php endif; ?>
	</div>
</div>