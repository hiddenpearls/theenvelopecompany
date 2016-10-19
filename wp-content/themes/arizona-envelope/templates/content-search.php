<article <?php post_class("col-md-3 product-category-extract button-down"); ?>>
  	<header>
  		<img class="img-responsive" src="<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>" alt="">
    	<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    	<?php if (get_post_type() === 'post') { get_template_part('templates/entry-meta'); } ?>
  	</header>
  	<div class="entry-summary">
   
    	<?php 
    		global $product;
    		$size = $product->get_attribute( 'size' ) ; 
    	?>
    	<p>Size: <?php echo $size; ?></p>
  	</div>
  	<a href="<?php the_permalink(); ?>" class="btn shop-btn small">View Product</a>
</article>

