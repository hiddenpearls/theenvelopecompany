<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class WC_TA_Taxamo_Manager
 *
 */
class WC_TA_Taxamo_Manager {

	const OIM_LINE_KEY = '_taxamo_line_key';

	public function setup() {

		// Add the Taxamo line key to ignored line item meta
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_line_key_from_meta_box' ), 10, 1 );

		// Store transaction
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'store_transaction_on_checkout' ), 10, 1 );

		// Register payment
		add_action( 'woocommerce_payment_complete', array( $this, 'register_payment' ), 10, 1 );

		// Confirm transaction
		add_action( 'woocommerce_order_status_completed', array( $this, 'confirm_transaction' ), 10, 1 );

		// Refund support
		add_action( 'woocommerce_order_refunded', array( $this, 'refund_transaction' ), 10, 2 );
	}

	/**
	 * Add the Taxamo line key to ignored line item meta
	 *
	 * @param $meta_keys
	 *
	 * @return array
	 */
	public function hide_line_key_from_meta_box( $meta_keys ) {
		$meta_keys[] = self::OIM_LINE_KEY;

		return $meta_keys;
	}

	/**
	 * Catch the new order action and update taxes to selected billing country
	 *
	 * @param $order_id
	 */
	public function store_transaction_on_checkout( $order_id ) {

		// Get the order
		$order = wc_get_order( $order_id );

		$force_country = false;

		// Location confirmation
		if ( isset( WC()->session ) && true === WC()->session->get( 'wc_ta_location_confirmation', false ) ) {
			// Set force country code
			$force_country = true;
		}

		// Store transaciton
		$this->store_transaction( $order, $force_country );

	}

	/**
	 * Store an order as a transaction in Taxamo
	 *
	 * @param WC_Order $order
	 * @param bool $force_country
	 */
	public function store_transaction( $order, $force_country ) {

		// Get the billing country
		$billing_country = $order->billing_country;

		// The order manager
		$order_manager = new WC_TA_Order_Manager();

		// The transaction extra
		$transaction_extra = array();

		// Location confirmation
		if ( true === $force_country ) {
			// Set force country code
			$transaction_extra['force_country_code'] = $billing_country;
		}

		// Check for VAT number
		$vat_number = get_post_meta( $order->id, WC_TA_Vat_Number_Field::META_KEY, true );
		if ( '' !== $vat_number ) {
			// Set VAT number
			$transaction_extra['buyer_tax_number'] = $vat_number;
		}

		// Check for shipping/handling cost
		if ( $order->order_shipping > 0 ) {

			// Transaction lines
			$transaction_extra['transaction_lines'] = array();

			// Get the shipping line items
			$shipping_line_items = $order->get_items( 'shipping' );

			// Check if there are line items
			if ( count( $shipping_line_items ) > 0 ) {

				// Loop
				foreach ( $shipping_line_items as $shipping_line_item_key => $shipping_line_item ) {

					// Calculate some taxes
					$line_cost = $shipping_line_item['cost'];

					// Array of taxes
					$line_taxes = maybe_unserialize( $shipping_line_item['taxes'] );

					// Get total tax
					$line_tax_total = 0;
					if ( is_array( $line_taxes ) ) {
						$line_tax_total = array_sum( $line_taxes );
					}

					// Calculate shipping tax of this line item
					$shipping_tax_rate = 0;
					if ( $order->order_shipping_tax > 0 ) {
						$shipping_tax_rate = ( $line_tax_total / $line_cost ) * 100;
					}

					// Add shipping line item to transaction line
					$transaction_extra['transaction_lines'][] = array(
						'product_type' => 'default',
						'custom_id'    => "" . $shipping_line_item_key,
						'quantity'     => 1,
						'tax_rate'     => $shipping_tax_rate,
						'total_amount' => wc_round_tax_total( $line_cost + $line_tax_total ),
						'description'  => $shipping_line_item['name'],
						'informative'  => true,
					);

				}
			}

		}

		// Set invoice details in transaction_extra variable
		$transaction_extra['invoice_address'] = array(
			'street_name' => $order->billing_address_1,
			'city'        => $order->billing_city,
			'postal_code' => $order->billing_postcode,
			'country'     => $order->billing_country
		);

		// Setup request
		$request_store_transaction = new WC_TA_Request_Store_Transaction( $billing_country, $order_manager->get_items_from_order( $order ), $order->billing_first_name . ' ' . $order->billing_last_name, $order->billing_email, $order->id, $transaction_extra );

		// Do request
		if ( $request_store_transaction->do_request() ) {

			// Get the body
			$response_body = $request_store_transaction->get_response_body();

			if ( isset( $response_body->transaction ) ) {

				// Attach transaction to order
				update_post_meta( $order->id, 'taxamo_transaction_key', $response_body->transaction->key );

				// Loop through line items
				if ( count( $response_body->transaction->transaction_lines ) > 0 ) {

					foreach ( $response_body->transaction->transaction_lines as $transaction_line ) {

						if ( isset( $transaction_line->custom_id ) && isset( $transaction_line->line_key ) ) {

							// Add Taxamo line key to order item
							wc_add_order_item_meta( $transaction_line->custom_id, self::OIM_LINE_KEY, $transaction_line->line_key, true );

						}

					}
				}

			}

		} else {

			/**
			 * @todo Better error handling. Check if AJAX is updated correctly is country doesn't match first try but does second try.
			 * Block order button
			 */

			if ( function_exists( 'wc_add_notice' ) ) {
				wc_add_notice( $request_store_transaction->get_error_message(), 'error' );
			}


		}
	}

	/**
	 * Register the payment in Taxamo
	 *
	 * @param int $order_id
	 */
	public function register_payment( $order_id ) {

		// Get the order
		$order = wc_get_order( $order_id );

		// Get payment method title
		$payment_method_title = get_post_meta( $order_id, '_payment_method_title', true );

		// Get the Taxamo transaction key
		$transaction_key = get_post_meta( $order_id, 'taxamo_transaction_key', true );

		$request_register_payment = new WC_TA_Request_Register_Payment( $transaction_key, $order->get_total(), $payment_method_title );

		// Do request
		if ( ! $request_register_payment->do_request() ) {
			/**
			 * @todo Better error handling e.g. trigger error.
			 */
		}

	}

	/**
	 * Confirm the transaction
	 *
	 * @param $order_id
	 */
	public function confirm_transaction( $order_id ) {
		// Get the order
		$order = wc_get_order( $order_id );

		// Get the Taxamo transaction key
		$transaction_key = get_post_meta( $order_id, 'taxamo_transaction_key', true );

		// Setup the Request
		$request_confirm_transaction = new WC_TA_Request_Confirm_Transaction( $transaction_key );

		// Do request
		$request_confirm_transaction->do_request();
	}

	/**
	 * Refund a transaction
	 *
	 * @param $order_id
	 * @param $refund_id
	 *
	 * @return bool
	 */
	public function refund_transaction( $order_id, $refund_id ) {

		// Get the Taxamo transaction key
		$transaction_key = get_post_meta( $order_id, 'taxamo_transaction_key', true );

		// Get refund
		$refund = wc_get_order( $refund_id );

		// Get refund line items
		$refund_items = $refund->get_items( array( 'line_item', 'shipping' ) );

		// Check & loop
		if ( count( $refund_items ) > 0 ) {
			foreach ( $refund_items as $refund_item ) {

				// Get the line key
				$line_key = wc_get_order_item_meta( $refund_item['refunded_item_id'], self::OIM_LINE_KEY, true );

				// Only do the refund when the line item exists
				if ( '' !== $line_key ) {

					// Get correct amount
					$amount = abs( ( isset( $refund_item['line_total'] ) ? $refund_item['line_total'] : ( isset( $refund_item['cost'] ) ? $refund_item['cost'] : 0 ) ) );

					// Only continue if amount > 0
					if ( $amount > 0 ) {

						// Create refund request
						$refund_request = new WC_TA_Request_Refund( $transaction_key, $line_key, $amount );

						// Do the rquest
						$refund_request->do_request();
					}

				}

			}
		}

		// Refund done
		return true;
	}

}