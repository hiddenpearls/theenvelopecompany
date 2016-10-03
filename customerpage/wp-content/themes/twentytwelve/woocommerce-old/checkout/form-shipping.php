<?php
/**
 * Checkout shipping information form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

global $woocommerce;
?>

<?php if ( ( $woocommerce->cart->needs_shipping() || get_option('woocommerce_require_shipping_address') == 'yes' ) && ! $woocommerce->cart->ship_to_billing_address_only() ) : ?>

	<?php
		if ( empty( $_POST ) ) :

			$shiptobilling = (get_option('woocommerce_ship_to_same_address')=='yes') ? 1 : 0;
			$shiptobilling = apply_filters('woocommerce_shiptobilling_default', $shiptobilling);

		else :

			$shiptobilling = $checkout->get_value('shiptobilling');

		endif;
	?>

<script language="JavaScript" type="text/javascript">
/*<![CDATA[*/
function Test(rad){
 var rads=document.getElementsByName(rad.name);
 document.getElementById('shipping').style.display=(rads[1].checked||rads[3].checked)?'none':'block';
 document.getElementById('pickup').style.display=(rads[1].checked||rads[3].checked)?'block':'none';
}
/*]]>*/
</script>

<!-- Remark Custom Pickup Field 
<div>
<h3>Delivery Options</h3>
</div><div style="clear:both;"></div>
<div>
<input name="selection" type="hidden" value="no pickup" onclick="Test(this);" />
<input name="selection" type="radio" value="pickup" onclick="Test(this);"/>&nbsp;Pickup/Willcall&nbsp;&nbsp;
<input name="selection" type="radio" value="shipping" onclick="Test(this);" checked="checked"/>&nbsp;Shipping
<input name="selection" type="hidden" value="no ship"  onclick="Test(this);"/>
</div>

<div id="pickup" style="display:none;">
<div><h3>Contact Info for Pickup</h3></div>
<div style="clear:both;"></div>  
<div>
<?php do_action('woocommerce_before_order_notes', $checkout); ?>
</div>
</div>

<div id="shipping" style="display:block;">

	<div><h3><?php _e('Shipping Address', 'woocommerce'); ?></h3><p>&nbsp;</p></div>
 
 -->
 
    <div style="clear:both;"></div>
<h3><?php _e('Shipping Address', 'woocommerce'); ?></h3>
    <div style="clear:both;"></div>

	<div style="float:left;"><p class="form-row2" id="shiptobilling">
		<input id="shiptobilling-checkbox" class="input-checkbox" <?php checked($shiptobilling, 1); ?> type="checkbox" name="shiptobilling" value="1" />
		<label for="shiptobilling-checkbox" class="checkbox"><?php _e('Ship to billing address?', 'woocommerce'); ?></label>
	</p></div>


	<div class="shipping_address">

		<?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>

		<?php foreach ($checkout->checkout_fields['shipping'] as $key => $field) : ?>

			<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

		<?php endforeach; ?>

		<?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>


	</div>
<!-- Remark (above)
</div>  
-->
<div style="clear:both;"></div>  
<div><h3>Order Information</h3></div><div style="clear:both;"></div>  
   

<?php endif; ?>


<?php if (get_option('woocommerce_enable_order_comments')!='no') : ?>

	<?php if ($woocommerce->cart->ship_to_billing_address_only()) : ?>

		<h3><?php _e('Additional Information', 'woocommerce'); ?></h3>

	<?php endif; ?>

	<?php foreach ($checkout->checkout_fields['order'] as $key => $field) : ?>

		<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

	<?php endforeach; ?>

<?php endif; ?>

<?php do_action('woocommerce_after_order_notes', $checkout); ?>