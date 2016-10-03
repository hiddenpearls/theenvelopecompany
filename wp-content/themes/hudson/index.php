<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 */
get_header();
?>


<!-- =====================================
    START CONTENT -->
<div class="content">
    <div class="container">
        <div class="path">
            <a href="<?php echo site_url('/'); ?>"><?php _e('Home', 'hudson'); ?></a>
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