<?php 
$thecontent = get_the_content();
if(!empty($thecontent)) {
 ?>
<section>
	<div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <?php the_content(); ?>
            </div>
        </div>
    </div>
</section>
<?php 
}
 ?>
<section class="products-section">
	<div class="container">
		<?php 
			$args = array(
				'post_type'		=>	'news',
				'post_status'	=>	'publish',
				'posts_per_page'=>	 -1,
			);
			$news_query = null;
			$news_query = new WP_Query( $args );
			if ( $news_query->have_posts() ){

				$count=0; ?>
				<div class="row row-eq-height">
		    <?php 
		    	while( $news_query->have_posts() ) : $news_query->the_post(); ?>
					<div class="col-md-4 product-category-extract button-down">
						<?php  
						
						if( get_post_thumbnail_id() ){ 
						?>
						<img class="img-responsive" src="<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>" alt="">
						<?php } ?>
						<h3><?php the_title(); ?></h3>
						<p><?php the_excerpt(); ?></p>
						<a class="btn orange-btn small" href="<?php echo the_permalink(); ?>">View More</a>
					</div>
			<?php $count++;
                    if ($count%3 == 0){ ?>
                        </div><div class="row">
            <?php  }
                endwhile; ?>
            </div>
		<?php 
			}
			wp_reset_postdata();
		?>
	</div>
</section>
