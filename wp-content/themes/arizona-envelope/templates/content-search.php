<div class="col-md-3 button-down">
  <div class="product-category-extract search-result">
    <article <?php post_class(); ?>>
      <header>
        <img class="img-responsive" src="<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>" alt="">
        <h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <?php if (get_post_type() === 'post') { get_template_part('templates/entry-meta'); } ?>
      </header>
      <div class="entry-summary">
     
        <?php 
          global $product;
          $size = $product->get_attribute( 'size' ) ; 
        ?>
        <p>Size: <?php echo $size; ?></p>
      </div>
    </article>
  </div>
  <a href="<?php the_permalink(); ?>" class="btn orange-btn big">View Product</a>  
</div>