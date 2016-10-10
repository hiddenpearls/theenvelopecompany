<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

get_header();
?>
<?php
/**
 * woocommerce_before_main_content hook
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 */
do_action('woocommerce_before_main_content');
?>

<?php if (1 == 2 && apply_filters('woocommerce_show_page_title', true)) : ?>

    <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>

<?php endif; ?>

<?php do_action('woocommerce_archive_description'); ?>

<?php if (is_user_logged_in() ) { //only logged in user can see this CUSTOM ?>
<?php get_template_part('template_parts/cart', 'floating'); ?>
<?php } ?>
<div class="row">
    <div class="span3">
        <div class="rubric_w"><?php _e('filter by', 'woocommerce'); ?></div>
    </div>
    <?php if (_go('show_best_sellers')) : ?>
        <div class="span9">
            <div class="rubric_b"><?php _e('best sellers', 'woocommerce'); ?></div>
        </div>
    <?php endif; ?>
</div>

<div class="row">

    <?php
    /**
     * woocommerce_sidebar hook
     *
     * @hooked woocommerce_get_sidebar - 10
     */
    do_action('woocommerce_sidebar');
    ?>

    <div class="span9">
        <?php if (_go('show_best_sellers')) : ?>
            <div class="top_seller">
                <?php echo do_shortcode('[best_selling_products per_page="3" columns="3"]'); ?>           
            </div>
        <?php endif; ?>

        <div class="options">
            <?php do_action('woocommerce_before_shop_loop'); ?>
            <div class="clear"></div>
        </div>

        <div class="row products">
            <?php if (have_posts()) : ?>

                <?php //woocommerce_product_loop_start(); ?>

                <?php woocommerce_product_subcategories(); ?>

                <?php while (have_posts()) : the_post(); ?>

                    <?php woocommerce_get_template_part('content', 'product'); ?>

                <?php endwhile; // end of the loop.  ?>

                <?php //woocommerce_product_loop_end(); ?>

                <?php
                /**
                 * woocommerce_after_shop_loop hook
                 *
                 * @hooked woocommerce_pagination - 10
                 */
                do_action('woocommerce_after_shop_loop');
                ?>

            <?php elseif (!woocommerce_product_subcategories(array('before' => woocommerce_product_loop_start(false), 'after' => woocommerce_product_loop_end(false)))) : ?>

                <?php woocommerce_get_template('loop/no-products-found.php'); ?>

            <?php endif; ?>

        </div>

        <div class="options">
            <?php woocommerce_result_count(); ?>
            <?php woocommerce_pagination(); ?>            
            <div class="clear"></div>
        </div>
        
        <?php if (_go('show_recent')) : ?>
            <div class="rubric_b"><?php _e('recent products', 'woocommerce'); ?></div>
            <div class="top_seller">
                <?php echo do_shortcode('[recent_products per_page="3" columns="3"]'); ?>
            </div>
        <?php endif; ?>

        <?php if (_go('show_top_rated')) : ?>
            <div class="rubric_b"><?php _e('People considered these products', 'woocommerce'); ?></div>
            <div class="top_seller">
                <?php echo do_shortcode('[top_rated_products per_page="3" columns="3"]'); ?>
            </div>
        <?php endif; ?>


    </div>

</div>
<?php
/**
 * woocommerce_after_main_content hook
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('woocommerce_after_main_content');
?>
<?php get_footer(); ?>