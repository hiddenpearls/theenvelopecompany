<?php
if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_WC_Zapier_Handler')):

class WCFE_WC_Zapier_Handler extends WCFE_Checkout_Fields_Utils{
	private $trigger_keys = array(
		'wc.new_order', // New Order
		'wc.order_status_change' // New Order Status Change
	);

	public function __construct() {
		foreach ( $this->trigger_keys as $trigger_key ) {
			add_filter( "wc_zapier_data_{$trigger_key}", array( $this, 'zapier_data_override' ), 10, 4 );
		}
		
		//add_filter( "wc_zapier_data_wc.new_order", array( $this, 'zapier_data_override' ), 10, 4 );
		//add_filter( "wc_zapier_data_wc.order_status_change", array( $this, 'zapier_data_override' ), 10, 4 );
		add_action( "thwcfe-checkout-fields-updated", array( $this, 'checkout_fields_updated' ), 10, 0 );
	}

	/**
	 * When sending WooCommerce Order data to Zapier, also send any additional checkout fields
	 * that have been created by the Checkout Field Editor plugin.
	 *
	 * @param             array  $order_data Order data that will be overridden.
	 * @param WC_Zapier_Trigger  $trigger Trigger that initiated the data send.
	 *
	 * @return mixed
	 */
	public function zapier_data_override( $order_data, WC_Zapier_Trigger $trigger ) {
		$sections = $this->get_checkout_sections();
		
		if($sections && is_array($sections)){
			foreach($sections as $sname => $section){
				if($this->is_valid_section($section)){
					$fields = $section->get_fields();
					if($fields){
						foreach($fields as $name => $field){	
							if ( $field->get_property('enabled') && ! isset( $order_data[$name] ) ) {
								if ( $trigger->is_sample() ) {
									// Sending sample data: Send the label of the custom checkout field as the field's value.
									$order_data[$name] = $field->get_property('title');
								} else {
									// Sending real data: Send the saved value of this checkout field.
									// If the order doesn't contain this custom field, an empty string will be used as the value.
									$order_data[$name] = get_post_meta( $order_data['id'], $name, true );
								}
							}
						}
					}
				}
			}
		}

		return $order_data;
	}

	/**
	 * Executed whenever the checkout field definitions are updated/saved.
	 * Schedule the feed refresh to occur asynchronously.
	 */
	public function checkout_fields_updated( ) {
		WC_Zapier::resend_sample_data_async( array('wc.new_order', 'wc.order_status_change') );
	}

}

endif;