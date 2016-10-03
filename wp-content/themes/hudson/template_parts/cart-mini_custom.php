<?php global $woocommerce; ?>
<!-- CARt TOP -->
<div class="cart_top_region">
    <div class="cart_top">
        <div class="cart_top_interior">

            <?php
            if (count($woocommerce->cart->get_cart()) > 0) {
                foreach ($woocommerce->cart->get_cart() as $cart_item_key => $cart_item) {
                    $_product = $cart_item['data'];
                    // Only display if allowed
                    if (!apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key) || !$_product->exists() || $cart_item['quantity'] == 0)
                        continue;

                    // Get price
                    $product_price = get_option('woocommerce_tax_display_cart') == 'excl' ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();

                    $product_price = apply_filters('woocommerce_cart_item_price_html', woocommerce_price($product_price), $cart_item, $cart_item_key);
                    ?>

                    <div class="cart_top_item">
                        <div class="cart_top_item_img">
                            <a href="<?php echo get_permalink($cart_item['product_id']); ?>"><?php echo $_product->get_image(); ?></a>
                        </div>

                        <div class="cart_top_item_info">
                            <div class="cart_top_item_remove">
                                <?php
                                echo apply_filters('woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="remove" title="%s">&times;</a>', esc_url($woocommerce->cart->get_remove_url($cart_item_key)), __('Remove this item', 'woocommerce')), $cart_item_key);
                                ?>
                            </div>
                            <h2><a href="<?php echo get_permalink($cart_item['product_id']); ?>"><?php echo apply_filters('woocommerce_widget_cart_product_title', $_product->get_title(), $_product); ?></a></h2>
 							<p>Sub-total: <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?></p>
                        </div> 
                    </div>

                <?php } ?>

                <div class="cart_top_item_all">
                    <?php _e('Total', 'hudson'); ?> : <span class="dynamic_cart_total"><?php echo $woocommerce->cart->get_cart_subtotal(); ?></span>
                </div>
            <?php } else { ?>
                <?php _e('No products in cart. Keep shopping.', 'hudson'); ?>
            <?php } ?>
        </div>
        <a href="<?php echo $woocommerce->cart->get_checkout_url(); ?>" class="cart_top_button"><?php _e('Checkout', 'woocommerce'); ?></a>
        <a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" class="cart_top_button"><?php _e('View Cart', 'woocommerce'); ?></a>        
    </div>
</div>
<!-- CARt TOP -->