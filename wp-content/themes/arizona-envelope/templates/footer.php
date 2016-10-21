<footer class="site-footer">
  	<div class="container">
		<div class="row top-footer">
			<div class="widget-footer">
				<?php //dynamic_sidebar('sidebar-footer'); ?>
				<a class="brand" href="<?= esc_url(home_url('/')); ?>">
	                <img class="regular-logo" src="<?php the_field('site_logo', 'option'); ?>" alt="<?php echo get_bloginfo(); ?> logo">
	                <img class="retina-logo" src="<?php the_field('retina_site_logo', 'option'); ?>" alt="<?php echo get_bloginfo(); ?> logo">
	            </a>
				<?php
                    /*if (has_nav_menu('footer_navigation')) :
                        wp_nav_menu(['theme_location' => 'footer_navigation', 'menu_class' => 'nav site-navigation']);
                    endif;*/
                ?>
			</div>
			<div class="subscribe-footer">
				<?php echo do_shortcode(get_field('subscribe_form_shortcode', 'options')); ?>	
			</div>

		</div>	
		<div class="row bottom-footer">
			<div class="col-md-12">
				<?php 
				if (has_nav_menu('footer_navigation')) :
                    wp_nav_menu(['theme_location' => 'footer_navigation', 'menu_class' => 'nav site-navigation']);
                endif;
				if( get_field('copyright_information', 'options') ) :
				?>
					<small><?php the_field('copyright_information', 'options'); ?></small>
				<?php
				endif;
				?>

			</div>
		</div>
  	</div>
</footer>
