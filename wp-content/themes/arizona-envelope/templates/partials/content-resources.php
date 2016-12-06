<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2 text-center">
            <h1 class="title-pages text-center"><?php the_field('title');?></h1>
            <p><?php the_field('description');?></p>
        </div>
    </div>
</div>
<div class="container samples">
	<?php 
		$args = array(
			'post_type'		=>	'sample',
			'post_status'	=>	'publish',
			'posts_per_page'=>	 -1,
			'orderby'		=>	'menu_order',
			'order'			=> 	'DESC'
		);
		$samples_query = null;
		$samples_query = new WP_Query( $args );
		if ( $samples_query->have_posts() ){

			$count=0; ?>
			<div class="row row-eq-height">
	    <?php 
	    	while( $samples_query->have_posts() ) : $samples_query->the_post(); ?>
				<div class="col-sm-3 col-xs-6"><!--  product-category-extract title-down -->
					<img class="img-responsive" src="<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>" alt="<?php echo get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true);?>">
					<h3><?php echo ucwords(get_the_title()); ?></h3>
					<p><?php the_content(); ?></p>
				</div>
		<?php $count++;
                if ($count%4 == 0){ ?>
                    </div><div class="row row-eq-height">
        <?php  }
            endwhile; ?>
        </div>
	<?php 
		}
		wp_reset_postdata();
	?>
</div>