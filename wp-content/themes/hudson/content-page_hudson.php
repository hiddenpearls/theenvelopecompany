<?php woocommerce_breadcrumb(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <div class="rubric_b"><h1><?php the_title(); ?></h1></div>
    </header>

    <div class="entry-content">
        <?php the_content(__('Continue reading <span class="meta-nav meta-read-more">&raquo;</span>', 'hudson')); ?>
        <?php wp_link_pages(array('before' => '<div class="page-links">' . __('Pages:', 'hudson'), 'after' => '</div>')); ?>
    </div><!-- .entry-content -->
</article><!-- #post -->