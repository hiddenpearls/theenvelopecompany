<footer class="site-footer">
  	<div class="container">
    	
		<div class="row top-footer">
			<?php dynamic_sidebar('sidebar-footer'); ?>
		</div>	
		<div class="row bottom-footer">
			<div class="col-md-12">
				<?php 
				if( have_rows('information_links', 'options') ):
					echo "<ul>";
					while( have_rows('information_links', 'options') ) : the_row();
				?>
						<li>
							<a href="<?php the_sub_field('button_url', 'options'); ?>">
								<?php the_sub_field('button_label', 'options'); ?>
							</a>
						</li>
				<?php
					endwhile;
					echo "</ul>";
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
