<?php
/**
 * Template Name: Resources Page Template
 */
?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/partials/content', 'resources'); ?>
<?php endwhile; ?>