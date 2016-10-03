<?php
/**
 * Template Name: Page Hudson
 * Description: Default Page template for Hudson theme
 */
get_header();
?>

<div id="primary" class="site-content">
    <div id="content" role="main" class="container">

        <?php while (have_posts()) : the_post(); ?>
            <?php get_template_part('content', 'page_hudson'); ?>
            <?php comments_template('', true); ?>
        <?php endwhile; // end of the loop.  ?>

    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>