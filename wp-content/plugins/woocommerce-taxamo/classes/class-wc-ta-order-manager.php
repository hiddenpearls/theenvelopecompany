<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_TA_Order_Manager {

	/**
	 * Get the formatted items in current cart ready for transaction lines
	 *
	 * @param WC_Order $order
	 *
	 * @return array
	 */
	public function get_items_from_order( $order ) {

		// The items
		$items = array();

		// Loop through cart items
		$order_items = $order->get_items();

		// Loop cart items
		if ( count( $order_items ) > 0 ) {
			foreach ( $order_items as $line_item_id => $line_item ) {

				$id = ( ( 0 != $line_item['variation_id'] ) ? $line_item['variation_id'] : $line_item['product_id'] );

				$items[ $line_item_id ] = array(
					'id'         => $id,
					'data'       => wc_get_product( $id ),
					'quantity'   => $line_item['qty'],
					'line_total' => $line_item['line_total']
				);

			}
		}

		return $items;
	}

}