<article <?php post_class("col-md-4 product-category-extract button-down"); ?>>
  <header>
    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php if (get_post_type() === 'post') { get_template_part('templates/entry-meta'); } ?>
  </header>
  <div class="entry-summary">
    <?php the_excerpt(); ?>
  </div>
  <a href="<?php the_permalink(); ?>" class="btn shop-btn small">View Product</a>
</article>
