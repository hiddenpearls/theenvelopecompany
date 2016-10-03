<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_TA_Cart_Manager {

	/**
	 * Get the formatted items in current cart ready for transaction lines
	 *
	 * @return array
	 */
	public function get_items_from_cart() {

		// The items
		$items = array();

		// Pre Calculate totals
		WC()->cart->calculate_totals();

		// Loop through cart items
		$cart = WC()->cart->get_cart();

		if ( count( $cart ) > 0 ) {
			foreach ( $cart as $cart_key => $cart_item ) {

				$id = ( ( 'variation' === $cart_item['data']->product_type ) ? $cart_item['variation_id'] : $cart_item['product_id'] );

				$items[ $cart_key ] = array(
					'id'         => $id,
					'data'       => $cart_item['data'],
					'quantity'   => $cart_item['quantity'],
					'line_total' => $cart_item['line_total']
				);

			}
		}

		return $items;
	}

}