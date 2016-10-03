<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.8
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

wc_print_notices();

do_action( 'woocommerce_before_cart' ); 
wp_enqueue_script('iosSlider'); ?>

<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">


<div class="row">
<div class="col span_8">
<?php do_action( 'woocommerce_before_cart_table' ); ?>

<table class="shop_table cart" cellspacing="0">
	<thead>
		<tr>
			<th class="product-remove">&nbsp;</th>
			<th class="product-thumbnail">&nbsp;</th>
			<th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-price"><?php _e( 'Price', 'woocommerce' ); ?></th>
			<th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th class="product-subtotal"><?php _e( 'Total', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
			$variation_id = $cart_item['variation_id'];
			if (empty($variation_id)){
				$variation_id = $product_id;
			}
			$original_product = wc_get_product($variation_id);
			
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<tr class="tm-epo-cart-row-product <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

					<td class="product-remove">
						<?php
							echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s">&times;</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key );
						?>
					</td>

					<td class="product-thumbnail">
						<?php
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

							if ( ! $_product->is_visible() )
								echo $thumbnail;
							else
								printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
						?>
					</td>

					<td class="product-name">
						<?php
							if ( ! $_product->is_visible() )
								echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
							else
								echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', $_product->get_permalink(), $_product->get_title() ), $cart_item, $cart_item_key );

							// Meta data
							echo WC()->cart->get_item_data( $cart_item );

               				// Backorder notification
               				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
               					echo '<p class="backorder_notification">' . __( 'Available on backorder', 'woocommerce' ) . '</p>';
						?>
					</td>

					<td class="product-price">
						<?php
							if (TM_EPO()->tm_epo_hide_options_in_cart=="normal"){
								echo apply_filters( 'wc_tm_epo_ac_product_price', apply_filters( 'woocommerce_cart_item_price', TM_EPO()->get_price_for_cart( $cart_item['tm_epo_product_original_price']  ,$cart_item,""), $cart_item, $cart_item_key ) , $cart_item_key, $cart_item, $original_product, $product_id );
							}else{
								echo apply_filters( 'wc_tm_epo_ac_product_price', apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ) , $cart_item_key, $cart_item, $_product, $product_id );
							}							
						?>
					</td>

					<td class="product-quantity">
						<?php
							if ( empty($cart_item["tmcartepo"]) && empty($cart_item["tmsubscriptionfee"]) ){//tmcartfee
								if ( $_product->is_sold_individually() ) {
									$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
								} else {
									$product_quantity = woocommerce_quantity_input( array(
										'input_name'  => "cart[{$cart_item_key}][qty]",
										'input_value' => $cart_item['quantity'],
										'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
										'min_value'   => '0'
									), $_product, false );
								}

							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
							}else{
								if ( $_product->is_sold_individually() ) {
									echo apply_filters( 'wc_tm_epo_ac_product_qty', '1', $cart_item_key, $cart_item, $_product, $product_id ) ;
								}else{
									echo apply_filters( 'wc_tm_epo_ac_product_qty', $cart_item['quantity'], $cart_item_key, $cart_item, $_product, $product_id ) ;
								}
							}
						?>
					</td>

					<td class="product-subtotal">
						<?php 
							if (TM_EPO()->tm_epo_cart_field_display=="advanced"){
								if (TM_EPO()->tm_epo_hide_options_in_cart=="normal"){
									if (isset($cart_item['tm_epo_product_after_adjustment']) && TM_EPO()->tm_epo_dpd_enable=="no"){
										$price = $cart_item['tm_epo_product_after_adjustment'];
									}else{
										$price=apply_filters('wc_epo_discounted_price', $cart_item['tm_epo_product_original_price'], wc_get_product($cart_item['data']->id), $cart_item_key);
									}
									$price=$price*$cart_item['quantity'];
									echo apply_filters( 'woocommerce_cart_item_subtotal', TM_EPO()->get_price_for_cart( $price,$cart_item,""), $cart_item, $cart_item_key );
								}else{
									echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
								}
							}else{
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $original_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
							}
						?>
					</td>
					<?php 
					do_action( 'tm_woocommerce_cart_after_column', $cart_item_key, $cart_item, $_product, $product_id );
					?>
				</tr>
				<?php 
				do_action( 'tm_woocommerce_cart_after_row', $cart_item_key, $cart_item, $_product, $product_id );
			}
		}

		do_action( 'woocommerce_cart_contents' );
		?>
		<tr>
			<td colspan="6" class="actions">

				<?php if ( WC()->cart->coupons_enabled() ) { ?>
					<div class="coupon">

						<label for="coupon_code"><?php _e( 'Coupon', 'woocommerce' ); ?>:</label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php _e( 'Coupon code', 'woocommerce' ); ?>" /> <input type="submit" class="button" name="apply_coupon" value="<?php _e( 'Apply Coupon', 'woocommerce' ); ?>" />

						<?php do_action('woocommerce_cart_coupon'); ?>

					</div>
				<?php } ?>

				
			</td>
		</tr>

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
	</tbody>
</table>


<?php do_action('woocommerce_cart_collaterals'); ?>

</div><!--/span-8-->

<div class="col span_4">

	<div class="cart-collaterals">

		<?php woocommerce_cart_totals(); ?>
		<?php woocommerce_shipping_calculator(); ?>
		<input type="submit" class="button" name="update_cart" value="<?php _e( 'Update Cart', 'woocommerce' ); ?>" /> <input type="submit" class="checkout-button button alt wc-forward" name="proceed" value="<?php _e( 'Proceed to Checkout', 'woocommerce' ); ?>" />

		<?php wp_nonce_field( 'woocommerce-cart' ); ?>

	</div>

</div><!--/span-4-->

</div><!--/row-->

<?php do_action( 'woocommerce_after_cart_table' ); ?>

</form>

<?php do_action( 'woocommerce_after_cart' ); ?>