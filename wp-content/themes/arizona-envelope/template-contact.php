<?php
/**
 * Template Name: Contact Template
 */
?>

<?php while (have_posts()) : the_post(); ?>
<div class="category-shop-header">
    <div class="container">
        <div>
          <?php
              if (has_nav_menu('shop_navigation')) :
                  wp_nav_menu(['theme_location' => 'shop_navigation', 'menu_class' => 'nav shop-navigation-bar']);
              endif;
              ?>
        </div>
    </div>
</div>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/partials/content', 'contact'); ?>
<?php endwhile; ?>