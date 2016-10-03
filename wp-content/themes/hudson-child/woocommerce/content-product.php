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
 * @see     https://docs.woocommerce.com/document/template-structure/
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
<?php
// Store loop count we're currently on
if (empty($woocommerce_loop['loop']))
    $woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if (empty($woocommerce_loop['columns']))
    $woocommerce_loop['columns'] = apply_filters('loop_shop_columns', 4);

// Ensure visibility
if (!$product->is_visible())
    return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if (0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'])
    $classes[] = 'first';
if (0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'])
    $classes[] = 'last';
$classes[] = 'span3';
?>
<div <?php post_class($classes); ?>>
    <div class="item">
        <?php do_action('woocommerce_before_shop_loop_item'); ?>
                <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
        <div class="item_image">
            <div class="item_view" data-product-id="<?php the_ID(); ?>"><?php _e('quick view', 'hudson'); ?> +</div>
            <a href="<?php the_permalink(); ?>"><?php echo woocommerce_get_product_thumbnail('shop_catalog'); ?></a>
        </div>
        <h2><a href="<?php the_permalink(); ?>"><?php echo apply_filters('woocommerce_short_description', $post->post_excerpt) ?></a></h2>

        <?php woocommerce_template_loop_add_to_cart(); ?>
        <div class="item_price"><a href="<?php the_permalink(); ?>">
		<?php echo get_woocommerce_currency_symbol() . number_format($product->get_price() + $product->get_price(),2); ?><?php ?></a></div>
        <div class="clear"></div>
        <?php do_action('woocommerce_after_shop_loop_item'); ?>
    </div>    
</div>