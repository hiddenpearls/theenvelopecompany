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
<div class="container">
	<?php if (!have_posts()) : ?>
		<div class="alert alert-warning">
			<?php _e('Sorry, no results were found.', 'sage'); ?>
		</div>
		<?php get_search_form(); ?>
	<?php endif; ?>
	<div class="products-section">
		<?php $counter=0; ?>
		<?php while (have_posts()) : the_post(); ?>
			<?php if($counter == 0){echo '<div class="row-eq-height">';} ?>
		  	<?php get_template_part('templates/content', 'search'); ?>
		  	<?php $counter++; ?>
			<?php if($counter%4==0){echo '</div><div class="row-eq-height">';} ?>
		<?php endwhile; ?>
	</div>
	<?php the_posts_navigation(); ?>
</div>