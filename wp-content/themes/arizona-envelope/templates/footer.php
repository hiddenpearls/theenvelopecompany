<footer class="site-footer">
  	<div class="container">
    	
		<div class="row top-footer">
			<div class="widget-footer">
				<?php dynamic_sidebar('sidebar-footer'); ?>	
			</div>
			<div class="subscribe-footer">
				<?php echo do_shortcode(get_field('subscribe_form_shortcode', 'options')); ?>	
			</div>
			
			
		</div>	
		<div class="row bottom-footer">
			<div class="col-md-12">
				<?php 
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
