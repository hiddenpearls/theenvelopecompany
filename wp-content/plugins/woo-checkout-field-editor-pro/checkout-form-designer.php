<?php
/**
 * Plugin Name: Woo Checkout Field Editor Pro
 * Description: Customize WooCommerce checkout fields(Add, Edit, Delete and re-arrange fields).
 * Author:      ThemeHiGH
 * Version:     1.1.2
 * Author URI:  http://www.themehigh.com
 * Plugin URI:  http://www.themehigh.com
 * Text Domain: thwcfd
 * Domain Path: /languages
 */
 
if(!defined( 'ABSPATH' )) exit;

if(in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	load_plugin_textdomain( 'thwcfd', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/**
	 * woocommerce_init_checkout_field_editor function.
	 */
	function thwcfd_init_checkout_field_editor_lite() {
		global $supress_field_modification;
		$supress_field_modification = false;

		if(!class_exists('WC_Checkout_Field_Editor')){
			require_once('classes/class-wc-checkout-field-editor.php');
		}

		if (!class_exists('WC_Checkout_Field_Editor_Export_Handler')){
			require_once('classes/class-wc-checkout-field-editor-export-handler.php');
		}
		new WC_Checkout_Field_Editor_Export_Handler();

		$GLOBALS['WC_Checkout_Field_Editor'] = new WC_Checkout_Field_Editor();
	}
	add_action('init', 'thwcfd_init_checkout_field_editor_lite');
	
	/**
	 * Hide Additional Fields title if no fields available.
	 *
	 * @param mixed $old
	 */
	function thwcfd_enable_order_notes_field() {
		global $supress_field_modification;

		if($supress_field_modification){
			return $fields;
		}

		$additional_fields = get_option('wc_fields_additional');
		if(is_array($additional_fields)){
			$enabled = 0;
			foreach($additional_fields as $field){
				if($field['enabled']){
					$enabled++;
				}
			}
			return $enabled > 0 ? true : false;
		}
		return true;
	}
	add_filter('woocommerce_enable_order_notes_field', 'thwcfd_enable_order_notes_field', 1000);
		
	/*function thwcfd_woo_default_address_fields( $address_fields ) {
		$address_fields['state']['label'] = 'County';
		return $address_fields;
	}	
	add_filter( 'woocommerce_default_address_fields' , 'thwcfd_woo_default_address_fields' );*/
			
	/**
	 * wc_checkout_fields_modify_billing_fields function.
	 *
	 * @param mixed $old
	 */
	function thwcfd_billing_fields_lite($old){
		global $supress_field_modification;

		if($supress_field_modification){
			return $old;
		}

		return thwcfd_prepare_checkout_fields_lite(get_option('wc_fields_billing'), $old);
	}
	add_filter('woocommerce_billing_fields', 'thwcfd_billing_fields_lite', 1000);

	/**
	 * wc_checkout_fields_modify_shipping_fields function.
	 *
	 * @param mixed $old
	 */
	function thwcfd_shipping_fields_lite($old){
		global $supress_field_modification;

		if ($supress_field_modification){
			return $old;
		}

		return thwcfd_prepare_checkout_fields_lite(get_option('wc_fields_shipping'), $old);
	}
	add_filter('woocommerce_shipping_fields', 'thwcfd_shipping_fields_lite', 1000);

	/**
	 * wc_checkout_fields_modify_shipping_fields function.
	 *
	 * @param mixed $old
	 */
	function thwcfd_checkout_fields_lite( $fields ) {
		global $supress_field_modification;

		if($supress_field_modification){
			return $fields;
		}

		if($additional_fields = get_option('wc_fields_additional')){
			$fields['order'] = $additional_fields + $fields['order'];

			// check if order_comments is enabled/disabled
			if(isset($additional_fields) && !$additional_fields['order_comments']['enabled']){
				unset($fields['order']['order_comments']);
			}
		}
		
		if(isset($fields['account']) && is_array($fields['account'])){
			foreach( $fields['account'] as $name => $values ) {
				if(isset($fields['account'][$name])){
					if(isset($fields['account'][$name]['label'])){
						$fields['account'][ $name ]['label'] = __($fields['account'][ $name ]['label'], 'woocommerce');
					}
					if(isset($fields['account'][$name]['placeholder'])){
						$fields['account'][ $name ]['placeholder'] = _x($fields['account'][ $name ]['placeholder'], 'placeholder', 'woocommerce');
					}
				}
			}
		}
		
		if(isset($fields['order']) && is_array($fields['order'])){
			foreach( $fields['order'] as $name => $values ) {
				if(isset($fields['order'][$name])){
					if (isset($values['enabled']) && $values['enabled'] == false ) {
						unset( $fields['order'][ $name ] );
					}else{
						if(isset($fields['order'][$name]['label'])){
							$fields['order'][ $name ]['label'] = __($fields['order'][ $name ]['label'], 'woocommerce');
						}
						if(isset($fields['order'][ $name ]['placeholder'])){
							$fields['order'][ $name ]['placeholder'] = _x($fields['order'][ $name ]['placeholder'], 'placeholder', 'woocommerce');	
						}
					}	
				}	
			}
		}
		
		return $fields;
	}
	add_filter('woocommerce_checkout_fields', 'thwcfd_checkout_fields_lite', 1000);

	/**
	 * checkout_fields_modify_fields function.
	 *
	 * @param mixed $data
	 * @param mixed $old
	 */
	function thwcfd_prepare_checkout_fields_lite( $data, $old_fields ) {
		global $WC_Checkout_Field_Editor;

		if( empty( $data ) ) {
			return $old_fields;
			
		}else {
			$fields = $data;			
			foreach( $fields as $name => $values ) {
				// enabled
				if ( $values['enabled'] == false ) {
					unset( $fields[ $name ] );
				}

				// Replace locale field properties so they are unchanged
				if ( in_array( $name, array(
					'billing_country', 'billing_state', 'billing_city', 'billing_postcode',
					'shipping_country', 'shipping_state', 'shipping_city', 'shipping_postcode',
					'order_comments'
				) ) ) {
					if ( isset( $fields[ $name ] ) ) {
						$fields[ $name ]          = $old_fields[ $name ];
						$fields[ $name ]['label'] = ! empty( $data[ $name ]['label'] ) ? $data[ $name ]['label'] : $old_fields[ $name ]['label'];

						if ( ! empty( $data[ $name ]['placeholder'] ) ) {
							$fields[ $name ]['placeholder'] = $data[ $name ]['placeholder'];

						} elseif ( ! empty( $old_fields[ $name ]['placeholder'] ) ) {
							$fields[ $name ]['placeholder'] = $old_fields[ $name ]['placeholder'];

						} else {
							$fields[ $name ]['placeholder'] = '';
						}

						$fields[ $name ]['class'] = $data[ $name ]['class'];
						$fields[ $name ]['clear'] = $data[ $name ]['clear'];
					}
				}
				
				if(isset($fields[$name])){
					if(isset($fields[$name]['label'])){
						$fields[ $name ]['label'] = __($fields[ $name ]['label'], 'woocommerce');
					}
					if(isset($fields[$name]['placeholder'])){
						$fields[ $name ]['placeholder'] = __($fields[ $name ]['placeholder'], 'woocommerce');
					}
				}
			}								
			return $fields;
		}
	}

	/**
	 * wc_checkout_fields_validation function.
	 *
	 * @param mixed $posted
	 */
	function thwcfd_checkout_fields_validation_lite($posted){
		foreach(WC()->checkout->checkout_fields as $fieldset_key => $fieldset){

			// Skip shipping if its not needed
			if($fieldset_key === 'shipping' && (WC()->cart->ship_to_billing_address_only() || !empty($posted['shiptobilling']) || 
			(!WC()->cart->needs_shipping() && get_option('woocommerce_require_shipping_address') === 'no'))){
				continue;
			}

			foreach($fieldset as $key => $field){
				if(!empty($field['validate']) && is_array($field['validate']) && !empty($posted[$key])){
					foreach($field['validate'] as $rule){
						switch($rule) {
							case 'number' :
								if(!is_numeric($posted[$key])){
									if(defined('WC_VERSION') && version_compare(WC_VERSION, '2.3.0', '>=')){
										wc_add_notice('<strong>'. $field['label'] .'</strong> '. sprintf(__('(%s) is not a valid number.', 'woocommerce'), $posted[$key]), 'error');
									} else {
										WC()->add_error('<strong>'. $field['label'] .'</strong> '. sprintf(__('(%s) is not a valid number.', 'woocommerce'), $posted[$key]));
									}
								}
								break;
							case 'email' :
								if(!is_email($posted[$key])){
									if(defined('WC_VERSION') && version_compare(WC_VERSION, '2.3.0', '<')){
										WC()->add_error('<strong>'. $field['label'] .'</strong> '. sprintf(__('(%s) is not a valid email address.', 'woocommerce'), $posted[$key]));
									}
								}
								break;
						}
					}
				}
			}
		}
	}
	add_action('woocommerce_after_checkout_validation', 'thwcfd_checkout_fields_validation_lite');
	
	/**
	 * Display custom fields in emails
	 *
	 * @param array $keys
	 * @return array
	 */
	function thwcfd_display_custom_fields_in_emails_lite($keys){
		$custom_keys = array();
		$fields = array_merge(WC_Checkout_Field_Editor::get_fields('billing'), WC_Checkout_Field_Editor::get_fields('shipping'), 
		WC_Checkout_Field_Editor::get_fields('additional'));

		// Loop through all custom fields to see if it should be added
		foreach( $fields as $name => $options ) {
			if(isset($options['show_in_email']) && $options['show_in_email']){
				$custom_keys[ esc_attr( $options['label'] ) ] = esc_attr( $name );
			}
		}

		return array_merge( $keys, $custom_keys );
	}	
	add_filter('woocommerce_email_order_meta_keys', 'thwcfd_display_custom_fields_in_emails_lite', 10, 1);

	/**
	 * Display custom checkout fields on view order pages
	 *
	 * @param  object $order
	 */
	function thwcfd_order_details_after_customer_details_lite($order){
		$order_id = $order->id;				
		
		$fields = array();		
		if(!wc_ship_to_billing_address_only() && $order->needs_shipping_address()){
			$fields = array_merge(WC_Checkout_Field_Editor::get_fields('billing'), WC_Checkout_Field_Editor::get_fields('shipping'), 
			WC_Checkout_Field_Editor::get_fields('additional'));
		}else{
			$fields = array_merge(WC_Checkout_Field_Editor::get_fields('billing'), WC_Checkout_Field_Editor::get_fields('additional'));
		}

		$found = false;
		$html = '';

		// Loop through all custom fields to see if it should be added
		foreach($fields as $name => $options){
			$enabled = (isset($options['enabled']) && $options['enabled'] == false) ? false : true;
			$is_custom_field = (isset($options['custom']) && $options['custom'] == true) ? true : false;
		
			if(isset($options['show_in_order']) && $options['show_in_order'] && $enabled && $is_custom_field){
				$found = true;
				$html .= '<dt>' . esc_attr( $options['label'] ) . ':</dt>';
				$html .= '<dd>' . get_post_meta( $order_id, $name, true ) . '</dd>';
			}
		}
		if($found){
			echo '<dl>'. $html .'</dl>';
		}
	}
	add_action('woocommerce_order_details_after_customer_details', 'thwcfd_order_details_after_customer_details_lite', 20, 1);

}
