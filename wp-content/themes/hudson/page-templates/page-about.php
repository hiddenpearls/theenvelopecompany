<?php
/**
 * Template Name: About US Page Template
 *
 * Description: About US page for your website.
 *
 */
get_header();
?>

<div id="primary" class="site-content">
    <div id="content" role="main">

        <?php while (have_posts()) : the_post(); ?>
            <!-- =====================================
                START CONTENT -->
            <div class="content">
                <div class="container">
                    <div class="path">
                        <a href="<?php echo site_url('/'); ?>"><?php _e('Home', 'hudson'); ?></a> / <?php the_title(); ?>
                    </div>

                    <div class="about_slide">
                        <?php the_post_thumbnail('large'); ?>
                    </div>


                    <div class="short_description">
                        <h1><?php the_title(); ?></h1>
                        <?php the_content(); ?>
                    </div>

                    <?php echo do_shortcode('[tesla_team_show headline="' . __('Meet our team', 'hudson') . '"]'); ?>

                    <?php echo do_shortcode('[tesla_offers_about_services headline="' . __('Our services', 'hudson') . '"]'); ?>

                </div>
                <div class="clear"></div>
            </div>
        <?php endwhile; // end of the loop. ?>

    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>