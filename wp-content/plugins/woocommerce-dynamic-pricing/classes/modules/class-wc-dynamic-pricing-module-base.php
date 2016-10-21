<?php

abstract class WC_Dynamic_Pricing_Module_Base {

	public $module_id;
	public $module_type;

	public function __construct( $module_id, $module_type ) {
		$this->module_id = $module_id;
		$this->module_type = $module_type;
	}

	public abstract function adjust_cart( $cart );

	public function get_price_to_discount( $cart_item, $cart_item_key, $stack_rules = false ) {
		global $woocommerce;

		$result = false;
		do_action( 'wc_memberships_discounts_disable_price_adjustments' );

		$filter_cart_item = $cart_item;
		if ( isset( $woocommerce->cart->cart_contents[$cart_item_key] ) ) {
			$filter_cart_item = $woocommerce->cart->cart_contents[$cart_item_key];

			if ( isset( $woocommerce->cart->cart_contents[$cart_item_key]['discounts'] ) ) {
				if ( $this->is_cumulative( $cart_item, $cart_item_key ) || $stack_rules ) {
					$result = $woocommerce->cart->cart_contents[$cart_item_key]['discounts']['price_adjusted'];
				} else {
					$result = $woocommerce->cart->cart_contents[$cart_item_key]['discounts']['price_base'];
				}
			} else {

				if ( apply_filters( 'wc_dynamic_pricing_get_use_sale_price', true, $filter_cart_item['data'] ) ) {
					$result = $woocommerce->cart->cart_contents[$cart_item_key]['data']->get_price();
				} else {
					$result = $woocommerce->cart->cart_contents[$cart_item_key]['data']->get_regular_price();
				}
			}
		}

		do_action( 'wc_memberships_discounts_enable_price_adjustments' );
		return apply_filters( 'woocommerce_dynamic_pricing_get_price_to_discount', $result, $filter_cart_item, $cart_item_key );
	}

	protected function is_item_discounted( $cart_item, $cart_item_key ) {
		global $woocommerce;

		return isset( $woocommerce->cart->cart_contents[$cart_item_key]['discounts'] );
	}

	protected function is_cumulative( $cart_item, $cart_item_key, $default = false ) {
		//Check to make sure the item has not already been discounted by this module.  This could happen if update_totals is called more than once in the cart. 
		$cart = WC()->cart->get_cart();
		if ( isset( $cart ) && is_array( $cart ) && isset( $cart[$cart_item_key]['discounts'] ) && in_array( $this->module_id, WC()->cart->cart_contents[$cart_item_key]['discounts']['by'] ) ) {
			return false;
		} else {
			return apply_filters( 'woocommerce_dynamic_pricing_is_cumulative', $default, $this->module_id, $cart_item, $cart_item_key );
		}
	}

	protected function reset_cart_item( &$cart_item, $cart_item_key ) {
		global $woocommerce;
		if ( isset( $woocommerce->cart->cart_contents[$cart_item_key]['discounts'] ) && in_array( $this->module_id, $woocommerce->cart->cart_contents[$cart_item_key]['discounts']['by'] ) ) {
			foreach ( $woocommerce->cart->cart_contents[$cart_item_key]['discounts'] as $module ) {
				
			}
		}
	}

}

?>