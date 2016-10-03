<?php
/**
 * The template for displaying Search Results pages.
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
            <a href="<?php echo site_url('/'); ?>"><?php _e('Home', 'hudson'); ?></a> / <?php echo __('Search results for: ', 'hudson'); ?><strong><?php echo get_search_query(); ?></strong>
        </div>
        <div class="row">
            <div class="span9">
                <!-- START BLOG PAGE -->
                <div class="h_blog">

                    <?php if (have_posts()) : ?>

                        <?php /* Start the Loop */ ?>
                        <?php
                        while (have_posts()) : the_post();
                            echo get_post_format();
                            ?>
                            <?php get_template_part('post_head', get_post_format()); ?>
                        <?php endwhile; ?>

                    <?php else : ?>
                        <div class="error_404">


                            <h1><?php _e('No results', 'hudson'); ?></h1>
                            <p><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for.', 'hudson'); ?></p>


                            <?php get_search_form(); ?>

                            <a class="error_link" href="<?php echo get_permalink(woocommerce_get_page_id('shop')); ?>"><?php _e('continue shopping', 'hudson'); ?> </a>

                        </div>
                    <?php endif; ?>

                    <?php get_template_part('content', 'pagination'); ?>


                </div>

                <!-- END BLOG PAGE -->
            </div>
            <div class="span3">
                <?php get_sidebar('blog'); ?>
            </div>                
        </div>
    </div>
    <div class="clear"></div>
</div>

<?php get_footer(); ?>