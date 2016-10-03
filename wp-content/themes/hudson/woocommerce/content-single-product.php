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

if (is_user_logged_in() && current_user_can( 'site_discount' ) ) { ?>
<div class="rubric_b">Welcome, 
<?php  global $current_user;
       get_currentuserinfo();
       echo ' ' . $current_user->display_name; ?></div>
       <p class="myaccount_user">
	Hello <strong>user One</strong> (not user One? <a href="http://aze.youareheremedia.com/my-account/customer-logout/">Sign out</a>). From your account dashboard you can view your recent orders, manage your shipping and billing addresses and <a href="http://aze.youareheremedia.com/my-account/edit-account/">edit your password and account details</a>.</p>
<?php
$pricediscount=$product->get_price_html();
$pricediscountnew=$pricediscount*.8;

}

?>

<div itemscope itemtype="http://schema.org/Product" id="product-<?php the_ID(); ?>" <?php post_class('single-big-item'); ?>>
    <div class="row img-color-bkgd">
        <div class="span4">
<br />
            <h1><?php the_title(); ?></h1>
			<div class="mini_description"><?php echo apply_filters('woocommerce_short_description', $post->post_excerpt) ?></div>

			 <div class="prod_slider">
                <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
             </div>

        </div>
        

        <div class="span7"> 
              
            
            <div class="options_box">

            <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            </div>

                <p itemprop="price" class="price"><?php //echo $product->get_price_html(); ?></p>

                <meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
                <link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

            </div>
            <?php woocommerce_template_single_add_to_cart(); ?>
        </div>
        <div class="span001">
            <div class="_product_similar">

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