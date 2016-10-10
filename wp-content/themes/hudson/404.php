<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 */
get_header();
?>

<!-- =====================================
    START CONTENT -->
<div class="content">
    <div class="container">
        <div class="path">
            <a href="<?php echo site_url('/'); ?>"><?php _e('Home', 'hudson'); ?></a> / <?php _e('404: Page not found', 'hudson'); ?>
        </div>

        <?php get_template_part('template_parts/cart', 'floating'); ?>

        <div class="rubric_b"><?php _e('error 404', 'hudson'); ?></div>

        <div class="error_404">


            <h1><?php _e('error 404', 'hudson'); ?></h1>
            <p><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'hudson'); ?></p>


            <?php get_search_form(); ?>

            <a class="error_link" href="<?php echo get_permalink(woocommerce_get_page_id('shop')); ?>"><?php _e('continue shopping', 'hudson'); ?></a>

        </div>
    </div>
    <div class="clear"></div>
</div>

<?php get_footer(); ?>