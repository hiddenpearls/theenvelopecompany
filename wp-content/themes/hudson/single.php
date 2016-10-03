<?php
/**
 * The Template for displaying all single posts.
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
            <a href="<?php echo site_url('/'); ?>"><?php _e('Home', 'hudson'); ?></a><?php if (FALSE !== $_blog_url = tesla_blog_page_url()) { ?> / <a href="<?php echo $_blog_url; ?>"><?php _e('Blog', 'hudson'); ?></a><?php } ?> / <?php the_title(); ?>
        </div>
        <?php while (have_posts()) : the_post(); ?>
            <div class="row">
                <div class="span9">
                    <!-- START BLOG PAGE -->
                    <div class="h_blog">
                        <?php get_template_part('post_head', get_post_format()); ?>

                        <?php comments_template('', false); ?>
                    </div>

                    <!-- END BLOG PAGE -->
                </div>
                <div class="span3">
                    <?php get_sidebar('blog'); ?>
                </div>                
            </div>
        <?php endwhile; ?>
    </div>
    <div class="clear"></div>
</div>

<?php get_footer(); ?>