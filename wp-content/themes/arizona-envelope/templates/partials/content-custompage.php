<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php the_content(); ?>	
		</div>
	</div>

	<?php if( get_field('resources') ) { ?>
		<div class="row">
			<div class="col-md-12">
				<?php if( have_rows('resources') ): ?>
				<ul>
					<?php while ( have_rows('resources') ) : the_row(); ?>
						<li>
							<a href="<?php the_sub_field('resource_link_url'); ?>">
								<?php the_sub_field('resource_link_label'); ?>
							</a>
						</li>
				    <?php endwhile; ?>
				</ul>
				<?php endif; ?>
			</div>
		</div>
	<?php } ?>
	<?php if( get_field('sub_content') ){ ?>
		<div class="row">
			<div class="col-md-12">
				<?php the_field('sub_content'); ?>
			</div>
		</div>
	<?php } ?>
</div>
<?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>