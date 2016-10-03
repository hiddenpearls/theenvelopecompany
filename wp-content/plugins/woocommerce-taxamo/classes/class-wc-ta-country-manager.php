<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_TA_Country_Manager {

	/**
	 * The transient key
	 */
	const TRANSIENT_COUNTRY = 'wc_ta_countries';

	/**
	 * Get tax supported countries
	 *
	 * @return array
	 */
	private function get_tax_supported_countries() {

		// Get countries from transient
		$countries = get_transient( self::TRANSIENT_COUNTRY );

		// Get countries from Taxamo if transient isn't set
		if ( false === $countries ) {

			// Setup Array
			$countries = array();

			// Setup request
			$request_countries = new WC_TA_Request_Countries();

			// Do request
			if ( $request_countries->do_request() ) {

				// Get the body
				$response_body = $request_countries->get_response_body();

				if ( isset( $response_body->dictionary ) && count( $response_body->dictionary ) > 0 ) {

					// Add the dictionary countries to array
					foreach ( $response_body->dictionary as $country ) {
						$countries[] = $country->code;
					}

					// Set transient for one day
					set_transient( self::TRANSIENT_COUNTRY, $countries, DAY_IN_SECONDS );

				}

			}

		}

		// Return countries
		return $countries;
	}

	/**
	 * Check if tax is support for given country
	 *
	 * @param $country_code
	 *
	 * @return boolean
	 */
	public function is_tax_supported_for_country( $country_code ) {

		// Get tax supported countries
		$tax_supported_countries = $this->get_tax_supported_countries();

		// Return if the country code is set in the array of tax supported countries
		return in_array( $country_code, $tax_supported_countries );
	}

	/**
	 * Get the user's IP address
	 *
	 * @return string
	 */
	public function get_user_ip_address() {
		if ( isset( $_SERVER['X-Real-IP'] ) ) {
			return $_SERVER['X-Real-IP'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
			// Make sure we always only send through the first IP in the list which should always be the client IP.
			return trim( current( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			return $_SERVER['REMOTE_ADDR'];
		}

		return '';
	}

}