<?php

class WC_TA_Request_Countries extends WC_TA_Request {

	/**
	 * The constructor
	 */
	public function __construct() {

		// Set request method and endpoint
		$this->set_request_methods( array( 'dictionaries' ) );
		$this->set_request_endpoint( 'countries' );

		// Only return tax supported countries
		$this->set_request_body( array( 'tax_supported' => 'true' ) );

		parent::__construct();
	}

}