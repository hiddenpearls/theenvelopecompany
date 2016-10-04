<?php
/**
 * Template Name: Online Quote Page Template
 */
?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/partials/content', 'online-quote'); ?>
<?php endwhile; ?>