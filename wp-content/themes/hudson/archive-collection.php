<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 */
get_header();
?>


<!-- =====================================
    START CONTENT -->
<div class="content">
    <div class="container">
        <div class="path">
            <a href="<?php echo site_url('/'); ?>"><?php _e('Home', 'hudson'); ?></a> / <?php _e('COLLECTIONS', 'hudson'); ?>
        </div>

        <?php get_template_part('template_parts/cart', 'floating'); ?>

        <div class="rubric_b"><?php _e('collections', 'hudson'); ?></div>

    </div>

    <?php if (have_posts()) { ?>
        <!-- COLLECTIONS -->
        <div class="collection">
            <div class="container">
                <div class="row">
                    <?php
                    $i = 0;
                    while (have_posts()) {
                        ++$i;
                        the_post();
                        ?>
                        <?php
                        if ($i > 1 && ($i - 1) % 2 == 0) {
                            ?>
                        </div>
                    </div>
                    <div class="separation"></div>
                    <div class="container">
                        <div class="row">
                        <?php } ?>

                        <div class="span6">
                            <div class="collection_box">
                                <a href="<?php the_permalink(); ?>"><?php has_post_thumbnail() ? the_post_thumbnail('large') : ''; ?></a>
                                <p><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="container">
                <?php get_template_part('content', 'pagination'); ?>
            </div>
        </div>
        <!-- COLLECTIONS -->
    <?php } else { ?>
        <div class="container padd_bottom">
            <div class="row">
                <div class="span12">
                    <div class="alert alert-notice">
                        <?php _e('No collections posted yet.', 'hudson'); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>


    <div class="clear"></div>
</div>

<?php get_footer(); ?>