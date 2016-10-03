<?php
/**
 * The template for displaying Category pages.
 *
 * Used to display archive-type pages for posts in a category.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
get_header();
?>

<!-- =====================================
    START CONTENT -->
<div class="content">
    <div class="container">
        <div class="path">
            <a href="<?php echo site_url('/'); ?>"><?php _e('Home', 'hudson'); ?></a><?php if (FALSE !== $_blog_url = tesla_blog_page_url()) { ?> / <a href="<?php echo $_blog_url; ?>"><?php _e('Blog', 'hudson'); ?></a><?php } ?> / <?php printf(__('Tag: %s', 'hudson'), '<span>' . single_tag_title('', false) . '</span>'); ?>
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