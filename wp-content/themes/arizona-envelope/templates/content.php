<article <?php post_class("col-md-4 product-category-extract button-down"); ?>>
  <header>
  	<?php the_post_thumbnail(); ?>
    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php get_template_part('templates/entry-meta'); ?>
  </header>
  <div class="entry-summary">
    <?php the_excerpt(); ?>
  </div>
  <a href="<?php the_permalink(); ?>" class="btn shop-btn small">View More</a>
</article>
