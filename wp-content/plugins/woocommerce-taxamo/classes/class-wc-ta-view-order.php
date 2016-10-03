<?php

/**
 * Class WC_TA_view_order
 *
 * Adds invoice link to view order page if enabled
 */
class WC_TA_View_Order {

	/**
	 * Setup the class
	 */
	public function setup() {
		add_action( 'woocommerce_view_order', array( $this, 'show_invoice_link' ) );
	}
	
	
	/**
	 * Function show_invoice_link
	 * Works out if an invoice link should be shown, if so, shows it
	 */
	function show_invoice_link( $order_id ) {
		
		// Check to see if invoicing is turned on
		if ( WC_TA_Integration::$show_taxamo_invoice == 'yes' ) {
	

			$order = wc_get_order( $order_id );		

			// Get the transaction key in order to buidl the link
			$taxamo_transaction_key = get_post_meta( $order_id, 'taxamo_transaction_key', true );

			// Make sure this only happens for completed orders, as invoices are not available for other statuses
			if ( $order->post->post_status == 'wc-completed' && strlen( $taxamo_transaction_key ) > 0 ) {

				$invoice_url = esc_url( "https://dashboard.taxamo.com/api/v1/transactions/$taxamo_transaction_key/invoice?public_token=" . WC_TA_Integration::$public_token );
	
				// Load link template
				wc_get_template( 'view-order.php', array(
						'invoice_url' => $invoice_url
					),
					'woocommerce-view-order',
					untrailingslashit( plugin_dir_path( WooCommerce_Taxamo::get_plugin_file() ) ) . '/templates/' );

			}

		}
	}
	
}

