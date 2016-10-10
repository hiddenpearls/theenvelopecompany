<div class="category-shop-header">
	<div class="container">
		<div>
			<?php
                if (has_nav_menu('shop_navigation')) :
                    wp_nav_menu(['theme_location' => 'shop_navigation', 'menu_class' => 'nav shop-navigation-bar']);
                endif;
	            ?>
		</div>
	</div>
</div>
<div class="cart-shop-header">
	<div class="container">
		<?php 
			global $woocommerce;
			$cart_url = $woocommerce->cart->get_cart_url(); 
		?>
		<a class="cart-btn" href="<?php echo $cart_url; ?>"><i class="fa fa-shopping-cart"></i>My Cart</a>
		<a class="cart-contents" href="<?php echo wc_get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>"><?php echo sprintf ( _n( 'item: (%d)', 'items: (%d)', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?>  <?php echo WC()->cart->get_cart_total(); ?></a>
		<?php 
		global $woocommerce;

		if ( sizeof( $woocommerce->cart->cart_contents) > 0 ) :
			echo '<a class="btn white-btn" href="' . $woocommerce->cart->get_checkout_url() . '" title="' . __( 'Checkout' ) . '">' . __( 'Check Out' ) . '</a>';
		endif;

		?>
	</div>
</div>
<?php get_template_part('templates/page', 'header'); ?>
<div class="container">
	<?php if (!have_posts()) : ?>
		<div class="alert alert-warning">
			<?php _e('Sorry, no results were found.', 'sage'); ?>
		</div>
		<?php get_search_form(); ?>
	<?php endif; ?>
	<div class="products-section">
		<?php $counter=0; ?>
		<?php while (have_posts()) : the_post(); ?>
			<?php if($counter == 0){echo '<div class="row-eq-height">';} ?>
		  	<?php get_template_part('templates/content', 'search'); ?>
		  	<?php $counter++; ?>
			<?php if($counter%4==0){echo '</div><div class="row-eq-height">';} ?>
		<?php endwhile; ?>
	</div>
	<?php the_posts_navigation(); ?>
</div>