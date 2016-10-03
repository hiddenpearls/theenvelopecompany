<?php
//check if Woocommerce page and replace with custom one
if (tesla_has_woocommerce() && (is_cart() || is_checkout() || is_account_page() || is_woocommerce() || is_order_received_page())) {
    get_template_part('page', 'hudson');
    return;
}
get_header();
?>

<div id="primary" class="site-content">
    <div id="content" role="main" class="container">

        <div class="path">
            <a href="<?php echo site_url('/'); ?>"><?php _e('Home', 'hudson'); ?></a> / <?php the_title(); ?>
        </div>

        <?php while (have_posts()) : the_post(); ?>
            <?php get_template_part('content', 'page'); ?>
            <?php comments_template('', true); ?>
        <?php endwhile; // end of the loop.  ?>

    </div><!-- #content -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>