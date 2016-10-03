<?php

class WC_TA_Transaction_Manager {

	/**
	 * Build the transaction lines
	 *
	 * @param array $items
	 * @param String $country
	 *
	 * @return array
	 */
	private function build_transaction_lines( $items, $country ) {

		// Transaction lines
		$transaction_lines = array();

		// Lo0p
		if ( count( $items ) > 0 ) {

			// Country manager
			$country_manager = new WC_TA_Country_Manager();

			foreach ( $items as $item_key => $item ) {

				if ( isset( $item['data'] ) ) {

					// The transaction line
					$transaction_line = array();

					$product = $item['data'];

					// Set the product type
					$type = 'default';

					$is_euexempt = get_post_meta( $item['id'], '_euexempt', true );

					// Check if this is a virtual product
					if ( $product->is_virtual() && $is_euexempt !== 'yes' ) {
						$type = 'e-service';
					} else {
						$transaction_line['informative'] = true;
					}

					// Check if this is an e-book
					$is_ebook = get_post_meta( $item['id'], '_ebook', true );

					if ( 'yes' === $is_ebook ) {
						$type = 'e-book';
					}

					// Check if Taxamo supports taxes for this country
					if ( false === $country_manager->is_tax_supported_for_country( $country ) ) {
						// Taxes aren't supported, set the line to informative
						$transaction_line['informative'] = true;
					}

					// Set the product type
					$transaction_line['product_type'] = $type;

					// Custom ID
					$transaction_line['custom_id'] = "" . $item_key;

					// Quantity
					$transaction_line['quantity'] = $item['quantity'];

					// Price
					if ( $product->is_taxable() ) {

						// Always without tax
						$transaction_line['amount'] = $item['line_total'];

						// Get the base tax rates
						if ( method_exists( 'WC_Tax', 'get_base_tax_rates' ) ) {
							$base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class() );
						} else {
							$base_tax_rates = WC_Tax::get_shop_base_rate( $product->get_tax_class() );
						}

						$base_tax_rate = array_shift( $base_tax_rates );

						// Set the tax rate
						$transaction_line['tax_rate'] = $base_tax_rate['rate'];

					} else {
						// Set Price
						$transaction_line['amount'] = $product->get_price();

						// Set 0 tax rate
						$transaction_line['tax_rate'] = 0;

						// Force non taxable
						$transaction_line['informative'] = true;
					}

					// What do we want? Floats! When do we want them? Now!

					// Check if amount is set
					if ( isset( $transaction_line['amount'] ) ) {
						$transaction_line['amount'] = floatval( $transaction_line['amount'] );
					}

					// Check if total_amount is set
					if ( isset( $transaction_line['total_amount'] ) ) {
						$transaction_line['total_amount'] = floatval( $transaction_line['total_amount'] );
					}

					// Float the tax rate
					$transaction_line['tax_rate'] = floatval( $transaction_line['tax_rate'] );

					// Add transaction line to transaction lines
					$transaction_lines[] = $transaction_line;
				}

			}


		}

		return $transaction_lines;
	}

	/**
	 * Get a transaction array from the current cart
	 *
	 * @param String $country
	 * @param array $items
	 * @param null|String $buyer_name
	 * @param null|String $buyer_email
	 * @param null|String $custom_transaction_id
	 * @param array $transaction_extra
	 *
	 * @return array
	 */
	public function build_transaction( $country, $items, $buyer_name = null, $buyer_email = null, $custom_transaction_id = null, $transaction_extra = array() ) {

		// Country manager
		$country_manager = new WC_TA_Country_Manager();

		// The transaction
		$transaction = array(
			'currency_code'        => get_woocommerce_currency(),
			'billing_country_code' => $country,
			'buyer_ip'             => $country_manager->get_user_ip_address()
		);

		// Add buyer name
		if ( null !== $buyer_name ) {
			$transaction['buyer_name'] = $buyer_name;
		}

		// Add buyer email
		if ( null !== $buyer_email ) {
			$transaction['buyer_email'] = $buyer_email;
		}

		// Add custom transaction ID
		if ( null !== $custom_transaction_id ) {
			$transaction['custom_id'] = '' . $custom_transaction_id;
		}

		// Get the transaction lines
		$transaction['transaction_lines'] = $this->build_transaction_lines( $items, $country );

		// Merge transaction extra values
		if ( is_array( $transaction_extra ) && count( $transaction_extra ) > 0 ) {
			$transaction = array_merge_recursive( $transaction, $transaction_extra );
		}

		return $transaction;
	}

}