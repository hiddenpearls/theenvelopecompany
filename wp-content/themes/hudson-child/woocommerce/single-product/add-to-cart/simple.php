<?php
/**
 * Simple product add to cart
 *
 * @author 		Youareheremedia.com
 * @package 	WooCommerce/Templates
 * @version     2.1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

?>

<?php
	// Availability
	$availability      = $product->get_availability();
	$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';

	echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
?>

<?php if ( $product->is_in_stock() ) : ?>
<style>
<?php
  
    $aze_meta[0] = get_post_meta( get_the_ID(), 'wdm_upc_field', true ); 
	$aze_meta[1] = get_post_meta( get_the_ID(), 'wdm_is_purchasable', true ); 
	$aze_meta[2] = get_post_meta( get_the_ID(), 'wdm_is_purchasable1', true ); 
	$aze_meta[3] = get_post_meta( get_the_ID(), 'wdm_is_purchasable2', true );
	
	$aze_options[0] = "collapseme_print-div";
	$aze_options[1] = "collapseme_latex-div";
	$aze_options[2] = "collapseme_zip-div";
	$aze_options[3] = "collapseme_zip2-div";
	$i="0";
    // Checks and displays the retrieved value
	foreach ($aze_meta as $value) {
		echo '.cpf_hide_element.tm-cell.col-12.cpf-type-checkbox.';
		if($value=='yes'){
			echo $aze_options[$i]."{visibility:visible !important; height: auto !important;}";
		} 
		else{
			echo $aze_options[$i]."{visibility:collapse !important; height: 0px !important;}";
			
		};
		$i++;
}?>

</style>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" method="post" enctype='multipart/form-data'>
    
	 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
	 	<?php
	 		if ( ! $product->is_sold_individually() )
	 			woocommerce_quantity_input( array(
	 				'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
	 				'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
	 			) );
	 	?>
	 	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />
        <button type="submit" class="single_add_to_cart_button button alt" style="background:#fe3815 !important;">
        <span id="aze_buy_button" style="max-width:200px !important; padding:15px 40px 15px 40px;font-size:16px;display:block;">
		<?php echo $product->single_add_to_cart_text(); ?></span>
        </button>
		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	
    </form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
    
    <?php //do_action( 'fpd_product_designer' ); ?>

<?php endif; ?>
