<?php
/**
 * The template for displaying Archive pages.
 */
get_header();
?>


<!-- =====================================
    START CONTENT -->
<div class="content">
    <div class="container">
        <div class="path">
            <a href="<?php echo site_url('/'); ?>"><?php _e('Home', 'hudson'); ?></a><?php if (FALSE !== $_blog_url = tesla_blog_page_url()) { ?> / <a href="<?php echo esc_url($_blog_url); ?>"><?php _e('Blog', 'hudson'); ?></a><?php } ?> / 
            <?php
            if (is_day()) :
                printf(__('Daily Archives: %s', 'hudson'), '<span>' . get_the_date() . '</span>');
            elseif (is_month()) :
                printf(__('Monthly Archives: %s', 'hudson'), '<span>' . get_the_date(_x('F Y', 'monthly archives date format', 'hudson')) . '</span>');
            elseif (is_year()) :
                printf(__('Yearly Archives: %s', 'hudson'), '<span>' . get_the_date(_x('Y', 'yearly archives date format', 'hudson')) . '</span>');
            else :
                _e('Archives', 'hudson');
            endif;
            ?>
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