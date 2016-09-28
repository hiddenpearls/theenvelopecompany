<div class="container">
	<?php 
		$args = array(
			'post_type'		=>	'sample',
			'post_status'	=>	'publish',
			'posts_per_page'=>	 -1,
		);

		$samples_query = null;
		$samples_query = new WP_Query( $args );
		if ( $samples_query->have_posts() ){
			while( $samples_query->have_posts() ) : $samples_query->the_post(); ?>

				<div class="row">
					<div class="col-md-12">
						<img src="<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>" alt="">
						<h2></h2><?php the_title(); ?></h2>
						<p><?php the_content(); ?></p>
					</div>
				</div>
	<?php   endwhile;
		}
		wp_reset_postdata();
	?>
</div>
