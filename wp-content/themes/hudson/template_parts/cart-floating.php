<?php if (!tesla_has_woocommerce()) return; ?>
<?php global $woocommerce;
?>
<div class="row">
    <div class="right_float">
        <div class="fixed_cart cart_affix">
            <div class="fixed_interior">
                <h1><?php _e('my cart', 'hudson'); ?></h1>
                <p><?php _e('Items', 'hudson'); ?> ( <span class="dynamic_cart_contents_count"><?php echo $woocommerce->cart->get_cart_contents_count(); ?></span> ) <span class="dynamic_cart_total"><?php echo $woocommerce->cart->get_cart_subtotal(); ?></span>
            </div>
            <a href="<?php echo $woocommerce->cart->get_checkout_url() ?>" class="fixed_cart_button"><?php _e('Checkout', 'hudson'); ?></a>
        </div>
    </div>
</div>