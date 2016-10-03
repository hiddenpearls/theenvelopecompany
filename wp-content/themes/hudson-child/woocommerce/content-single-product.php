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
	
// ** customized by yahm - youareheremedia.com **//	
?>

<?php
global $product;

/**
 * woocommerce_before_single_product hook
 *
 * @hooked woocommerce_show_messages - 10
 */
?>
<?php
do_action('woocommerce_before_single_product');

?>

<div itemscope itemtype="http://schema.org/Product" id="product-<?php the_ID(); ?>" <?php post_class('single-big-item'); ?>>
    <div class="row img-color-bkgd">
        <div class="span4">
        	<div id="yahm_start_column"> 
               <h1><?php the_title(); ?></h1>
                <div class="mini_description"><?php echo apply_filters('woocommerce_short_description', $post->post_excerpt) ?></div>
                <div class="prod_slider">
                <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
                </div>
               </div>
        </div>
        <div class="span4" id="yahm-top-marg">
        	<div id="yahm_middle_column"> 
              <?php do_action('woocommerce_tm_custom_price_fields_only')?>
          </div>
         </div> 
        <div class="span4">
        	<div id="yahm_end_column"> 
            <div class="options_box">
            <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            </div>
     
                <div class="yahm-price-display">
                	<span id="yahm-unit-price" style="font-size:4em;color:#fe3815;">
				 		<?php echo custom_price_WPA111772($price,$product,2); ?>
                    </span><br />
                    <span id="yahm-unit-label">Per M (1,000)</span>
				</div>


                <meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
                <link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

            </div>
           <?php  woocommerce_template_single_add_to_cart(); ?>


			</div>
            <div class="_product_similar">
                  <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="product_aditional row">
        <div class="fullwidth">
            <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
        </div>
    </div>
</div>

<?php do_action('woocommerce_after_single_product'); ?>