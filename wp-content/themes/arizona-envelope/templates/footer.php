<footer class="site-footer">
  	<div class="container">
    	
		<div class="row top-footer">
			<?php dynamic_sidebar('sidebar-footer'); ?>
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
