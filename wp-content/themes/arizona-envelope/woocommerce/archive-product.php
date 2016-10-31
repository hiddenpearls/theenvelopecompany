<?php
	/**
	 * woocommerce_before_main_content hook.
	 *
	 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
	 * @hooked woocommerce_breadcrumb - 20
	 */
	do_action( 'woocommerce_before_main_content' );
?>

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
	<!--<div class="cart-shop-header">
		<div class="container">
			<?php 
				/*global $woocommerce;
				$cart_url = $woocommerce->cart->get_cart_url(); */
			?>
			<a class="cart-btn" href="<?php //echo $cart_url; ?>"><i class="fa fa-shopping-cart"></i>My Cart</a>
			<a class="cart-contents" href="<?php //echo wc_get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>"><?php //echo sprintf ( _n( 'item: (%d)', 'items: (%d)', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?>  <?php echo WC()->cart->get_cart_total(); ?></a>
			<?php 
			/*global $woocommerce;

			if ( sizeof( $woocommerce->cart->cart_contents) > 0 ) :
				echo '<a class="btn white-btn" href="' . $woocommerce->cart->get_checkout_url() . '" title="' . __( 'Checkout' ) . '">' . __( 'Check Out' ) . '</a>';
			endif;
			*/
			?>
		</div>
	</div>-->
	<?php //if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>	
		<!--<h1 class="page-title"><?php //woocommerce_page_title(); ?></h1>-->
	<?php //endif; ?>
	<div class="container">
		<h1 class="text-center title-pages"><?php single_cat_title(); ?></h1>
	<?php
		/**
		 * woocommerce_archive_description hook.
		 *
		 * @hooked woocommerce_taxonomy_archive_description - 10
		 * @hooked woocommerce_product_archive_description - 10
		 */
		//do_action( 'woocommerce_archive_description' );
	?>

	<?php if ( have_posts() ) : ?>

		<?php
			/**
			 * woocommerce_before_shop_loop hook.
			 *
			 * @hooked woocommerce_result_count - 20
			 * @hooked woocommerce_catalog_ordering - 30
			 */
			if( is_shop() || is_product_category() ){
				//do nothing
			} else {
				do_action( 'woocommerce_before_shop_loop' );
			}
			
		?>
		<?php $counter = 0; ?>
		<?php woocommerce_product_loop_start(); ?>

			<?php woocommerce_product_subcategories(); ?>
	
			<?php while ( have_posts() ) : the_post(); ?>
				<?php if($counter == 0){echo '<div class="row-eq-height">';} ?>
				<?php wc_get_template_part( 'content', 'product' ); ?>
				<?php $counter++; ?>
				<?php if($counter%4==0){echo '</div><div class="row-eq-height">';} ?>
			<?php endwhile; // end of the loop. ?>

		<?php woocommerce_product_loop_end(); ?>

		<?php
			/**
			 * woocommerce_after_shop_loop hook.
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'woocommerce_after_shop_loop' );
		?>

	<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

		<?php wc_get_template( 'loop/no-products-found.php' ); ?>

	<?php endif; ?>
	</div>
<?php
	/**
	 * woocommerce_after_main_content hook.
	 *
	 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
	 */
	do_action( 'woocommerce_after_main_content' );
?>