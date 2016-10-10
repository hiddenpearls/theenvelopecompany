<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
?>

<?php
global $product;
/**
 * woocommerce_before_single_product hook
 *
 * @hooked woocommerce_show_messages - 10
 */
do_action('woocommerce_before_single_product');
?>

<div itemscope itemtype="http://schema.org/Product" id="product-<?php the_ID(); ?>" <?php post_class('single-big-item'); ?>>
    <div class="row">
        <div class="span4">

            <div class="prod_slider">
                <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
            </div>

        </div>
        
        <div class="span5">
            <h1><?php the_title(); ?></h1>
            <div class="mini_description"><?php echo apply_filters('woocommerce_short_description', $post->post_excerpt) ?></div>
            <div class="product_rate"><?php print $product->get_rating_html(); ?> <?php _e('Reviews', 'hudson'); ?> (<?php echo get_comments_number(); ?>) / <span><a href="#reviews" class="inline show_review_form"><?php _e('Write a review', 'hudson'); ?></a></span></div>
            <?php
            //if (!in_array($product->product_type, array('variable')))
            //    woocommerce_template_single_price();
            ?>
            <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

                <p itemprop="price" class="price"><?php print $product->get_price_html(); ?></p>

                <meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
                <link itemprop="availability" href="http://schema.org/<?php print $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

            </div>
            <?php woocommerce_template_single_add_to_cart(); ?>
        </div>
        <div class="span3">
            <div class="product_similar">
                <div class="title"><?php _e('people who viewed this also viewed', 'hudson'); ?></div>
                <?php 
                foreach ($product->get_upsells() as $upsell_prod_ID) {
                    $up_prod = get_product($upsell_prod_ID);
                    ?>
                    <div class="product_similar_item">
                        <div class="similar_item_img" data-toggle="tooltip" title="<?php echo esc_attr($up_prod->get_title()); ?>"><a href="<?php echo get_permalink($upsell_prod_ID); ?>"><?php print $up_prod->get_image(94); ?></a></div>
                        <p><?php print $up_prod->get_price_html(); ?></p>
                    </div>
                <?php } ?>                                       

                <div class="clear"></div>
            </div>
        </div>
    </div>


    <div class="product_aditional row">
        <div class="span12">
            <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
        </div>
    </div>
</div>

<?php do_action('woocommerce_after_single_product'); ?>