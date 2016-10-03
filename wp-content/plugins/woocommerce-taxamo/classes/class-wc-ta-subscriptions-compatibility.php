<?php

class WC_TA_Subscriptions_Compatibility {

	public function setup_hooks() {
		// Add Taxamo transaction key order meta to exclude list
		add_filter( 'woocommerce_subscriptions_renewal_order_meta_query', array(
			$this,
			'filter_renewal_meta_query'
		), 10, 1 );

		// Store renewal order in Taxamo
		add_action( 'woocommerce_subscriptions_renewal_order_created', array( $this, 'store_renewal_order' ), 10, 1 );
	}

	/**
	 * Add Taxamo transaction key order meta to exclude list
	 *
	 * @param $order_meta_query
	 *
	 * @return string
	 */
	public function filter_renewal_meta_query( $order_meta_query ) {
		return $order_meta_query . " AND `meta_key` NOT LIKE 'taxamo_transaction_key' ";
	}

	/**
	 * Store renewal order in Taxamo
	 *
	 * @param WC_Order $renewal_order
	 */
	public function store_renewal_order( $renewal_order ) {
		$taxamo_manager = new WC_TA_Taxamo_Manager();
		$taxamo_manager->store_transaction( $renewal_order, true );
	}

}