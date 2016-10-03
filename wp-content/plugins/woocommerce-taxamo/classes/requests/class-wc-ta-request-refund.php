<?php

class WC_TA_Request_Refund extends WC_TA_Request {

	/**
	 * The constructor
	 *
	 * @param String $transaction_key
	 * @param String $line_key
	 * @param float $amount
	 */
	public function __construct( $transaction_key, $line_key, $amount ) {

		// Set request method and endpoint
		$this->set_request_methods( array( 'transactions', $transaction_key ) );
		$this->set_request_endpoint( 'refunds' );

		// Set the HTTP method
		$this->set_http_method( 'POST' );

		// Set the body
		$this->set_request_body( array(
			'line_key' => $line_key,
			'amount'   => floatval( $amount )
		) );

		parent::__construct();
	}

}