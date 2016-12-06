<?php
/**
 * Refund email (plain)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

printf( __( 'Hi %s,', 'woocommerce-advanced-notifications' ), esc_html( $recipient_name ) );

echo "\n\n";

printf( __( 'Order: %s has been refunded.', 'woocommerce-advanced-notifications' ) );

echo "\n\n";

echo "============================================================\n";

printf( '%s', date_i18n( __('jS F Y', 'woocommerce-advanced-notifications'), strtotime( $order->order_date ) ) );

echo "\n";

echo "============================================================";

$displayed_total = 0;

foreach ( $order->get_items() as $item_id => $item ) {

	$_product = $order->get_product_from_item( $item );

	$display = false;

	if ( $triggers['all'] || in_array( $_product->id, $triggers['product_ids'] ) || in_array( $_product->get_shipping_class_id(), $triggers['shipping_classes'] ) )
		$display = true;

	if ( ! $display ) {

		$cats = wp_get_post_terms( $_product->id, 'product_cat', array( "fields" => "ids" ) );

		if ( sizeof( array_intersect( $cats, $triggers['product_cats'] ) ) > 0 )
			$display = true;

	}

	if ( ! $display )
		continue;

	$displayed_total += $order->get_line_total( $item, true );

	if ( version_compare( WC_VERSION, '2.4.0', '<' ) ) {
		$item_meta = new WC_Order_Item_Meta( $item['item_meta'] );
	} else {
		$item_meta = new WC_Order_Item_Meta( $item );
	}

	// Product name
	echo "\n" . apply_filters( 'woocommerce_order_product_title', $item['name'], $_product );

	// SKU
	echo $_product->get_sku() ? ' (#' . $_product->get_sku() . ')' : '';

	if ( $show_prices )
		echo " (" . $order->get_line_subtotal( $item ) . ")";

	echo " X " . $item['qty'];

	// allow other plugins to add additional product information here
	do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text );

	// Variation
	echo $item_meta->meta ? ( "\n --> " . str_replace( "\n", '', $item_meta->display( true, true ) ) ) : '';

	// File URLs
	if ( $show_download_links ) {
		$order->display_item_downloads( $item );
	}

	// allow other plugins to add additional product information here
	do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text );

	echo "\n";

}

echo "============================================================\n";

if ( $show_totals ) {

	if ( $triggers['all'] && ( $totals = $order->get_order_item_totals() ) ) {
		foreach ( $totals as $total ) {
			echo $total['label'] . ' ';
			echo preg_replace( "/&#?[a-z0-9]{2,8};/i", "", $total['value'] );
			echo "\n";
		}
	} else {
		// Only show the total for displayed items
		echo __( 'Total', 'woocommerce-advanced-notifications' ) . ': ';
		echo $displayed_total;
		echo "\n";
	}

}

echo "\n\n";

/**
* @hooked WC_Emails::customer_details() Shows customer details
* @hooked WC_Emails::email_address() Shows email address
*/
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n\n";

echo "Regards,\n" . $blogname;