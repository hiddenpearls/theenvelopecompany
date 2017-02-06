<?php
/**
 * Template Name: Content Pages Template
 */
?>
<?php while (have_posts()) : the_post(); ?>
	<?php get_template_part('templates/page', 'header'); ?>
  	<?php get_template_part('templates/partials/content', 'custompage'); ?>
<?php endwhile; ?>
