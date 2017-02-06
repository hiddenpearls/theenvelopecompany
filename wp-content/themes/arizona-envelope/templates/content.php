<article <?php post_class(); ?>>
  <header>
  	<?php the_post_thumbnail(); ?>
    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php get_template_part('templates/entry-meta'); ?>
  </header>
  <div class="entry-summary">
    <?php echo get_excerpt(235); ?>
  </div>
  <a href="<?php the_permalink(); ?>" class="btn orange-btn small">View More</a>
</article>
