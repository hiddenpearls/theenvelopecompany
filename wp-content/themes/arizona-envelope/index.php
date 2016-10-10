<?php get_template_part('templates/page', 'header'); ?>

<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'sage'); ?>
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>
<section class="products-section">
<?php $counter = 0; ?>
<?php while (have_posts()) : the_post(); ?>
	<?php if($counter == 0){echo '<div class="row row-eq-height">';} ?>
  	<?php get_template_part('templates/content', get_post_type() != 'post' ? get_post_type() : get_post_format()); ?>
  	<?php $counter++; ?>
  	<?php if($counter%3 == 0){echo '</div><div class="row row-eq-height">';} ?>
<?php endwhile; ?>
</section>

<?php the_posts_navigation(); ?>
