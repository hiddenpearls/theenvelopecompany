<?php
/**
 * Entire Layout Edited by jason petzke 10/3/14
 * 
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
global $product, $woocommerce_loop;
// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;
// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
// Ensure visibilty
if ( ! $product->is_visible() )
	return;
// Increase loop count
$woocommerce_loop['loop']++;
?>
<div class="new-product <?php
	if ( $woocommerce_loop['loop'] % $woocommerce_loop['columns'] == 0 )
		echo 'last';
	elseif ( ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] == 0 )
		echo 'first';
	?>" style="width:550px;">
	<div class="new-image" style="float:left; margin-right:15px;">
	<a href="<?php the_permalink(); ?>">
	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
		<?php
			/**
			 * woocommerce_before_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woocommerce_template_loop_product_thumbnail - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item_title' );
		?>
	</a>
	</div>
    <div class="new-product-description" style="float:left; width:450px;">
	<a href="<?php the_permalink(); ?>">
		<h3><?php the_title(); ?></h3>
	</a><br />
    <link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />
		<?php
			/**
			 * woocommerce_after_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_template_loop_price - 10
			 */
			do_action( 'woocommerce_after_shop_loop_item_title' );
		?>
	<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
    </div>
    <div style="clear:both;"></div>
    <hr style="color:#ccc;background-color:#ccc; height:1px;></div><br />
</div>