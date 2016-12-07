<section>
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<?php if( get_field('404_page_title', 'options') ) : ?>
					<h1 class="text-center title-pages"><?php the_field('404_page_title', 'options'); ?></h1>
				<?php endif; ?>
				<?php if( get_field('404_page_text', 'options') ) : ?>
					<p class="text-center"><?php the_field('404_page_text', 'options'); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 content-404">
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>
</section>