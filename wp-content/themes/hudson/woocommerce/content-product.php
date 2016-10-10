<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<div class='span3'>
    <div <?php post_class(); ?>>
        <div class="item">
            <?php do_action('woocommerce_before_shop_loop_item'); ?>
            <div class="item_image">
                <div class="item_view" data-product-id="<?php the_ID(); ?>"><?php _e('quick view', 'hudson'); ?> +</div>
                <a href="<?php the_permalink(); ?>"><?php echo woocommerce_get_product_thumbnail('shop_catalog'); ?></a>
            </div>
            <?php
                /**
                 * woocommerce_before_shop_loop_item_title hook
                 *
                 * @hooked woocommerce_show_product_loop_sale_flash - 10
                 * @hooked woocommerce_template_loop_product_thumbnail - 10
                 */
                do_action( 'woocommerce_before_shop_loop_item_title' );
                /**
                 * woocommerce_shop_loop_item_title hook
                 *
                 * @hooked woocommerce_template_loop_product_title - 10
                 */
                do_action( 'woocommerce_shop_loop_item_title' );
                /**
                 * woocommerce_after_shop_loop_item_title hook
                 *
                 * @hooked woocommerce_template_loop_rating - 5
                 * @hooked woocommerce_template_loop_price - 10
                 */
                //do_action( 'woocommerce_after_shop_loop_item_title' );
            ?>

            <div class="tt-short-description"><a href="<?php the_permalink(); ?>"><?php echo apply_filters('woocommerce_short_description', $post->post_excerpt) ?></a></div>
            <div class="tt-grid-item-footer">
                <?php woocommerce_template_loop_add_to_cart(); ?>
                <div class="item_price"><?php print $product->get_price_html(); ?></div>
                <div class="clear"></div>
            </div>
            <?php do_action('woocommerce_after_shop_loop_item'); ?>
        </div>    
    </div>
</div>