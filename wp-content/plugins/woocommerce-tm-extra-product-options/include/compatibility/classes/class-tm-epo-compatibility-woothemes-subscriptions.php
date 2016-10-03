<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
    die();
}

final class TM_EPO_COMPATIBILITY_woothemes_subscriptions {

    protected static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {

        add_action( 'plugins_loaded', array( $this, 'add_compatibility' ) );

    }

    public function init() {
        
    }

    public function add_compatibility(){
        if ( !class_exists('WC_Subscriptions') ){
            return;
        }
        /** WooCommerce Subscriptions (woothemes) support **/
        add_filter('woocommerce_subscriptions_product_sign_up_fee', array( $this, 'tm_subscriptions_product_sign_up_fee' ), 10, 2);
        add_action('woocommerce_before_calculate_totals', array( $this, 'tm_woocommerce_before_calculate_totals' ), 1);
        add_filter('woocommerce_subscriptions_renewal_order_items', array( $this, 'tm_woocommerce_subscriptions_renewal_order_items' ), 10, 5); 
    }

    /** WooCommerce Subscriptions (woothemes) support **/
    public function tm_woocommerce_subscriptions_renewal_order_items($order_items, $original_order_id, $renewal_order_id, $product_id, $args_new_order_role ){
        if (!defined('TM_IS_SUBSCRIPTIONS_RENEWAL')){
            define('TM_IS_SUBSCRIPTIONS_RENEWAL',1);
        }
        return $order_items;
    }

    /** WooCommerce Subscriptions (woothemes) support - Calculates the extra subscription fee **/
    public function tm_subscriptions_product_sign_up_fee( $subscription_sign_up_fee="", $product="" ) {     
        $options_fee=0;
        if (!is_product() && WC()->cart){
            $cart_contents = WC()->cart->get_cart();
            foreach ($cart_contents as $cart_key => $cart_item) {
                foreach ($cart_item as $key => $data) {
                    if ($key=="tmsubscriptionfee"){
                        $options_fee=$data;
                    }
                }
            }
            $subscription_sign_up_fee += $options_fee;
        }
        return $subscription_sign_up_fee;
    }

    /** WooCommerce Subscriptions (woothemes) support **/
    public function tm_woocommerce_before_calculate_totals(){
        // Subcriptions
        if ( class_exists('WC_Subscriptions_Product') && function_exists('wcs_cart_contains_renewal') && ! empty( WC()->cart->cart_contents ) && ! wcs_cart_contains_renewal() ) {
            foreach ( WC()->cart->cart_contents as $cart_key=>$cart_item ) {
                $options_fee=0;
                foreach ($cart_item as $key => $data) {
                    if ($key=="tmsubscriptionfee"){
                        $options_fee=$data;
                    }
                }

                if ( WC_Subscriptions_Product::is_subscription( $cart_item['data'] ) ) {
                    if (!isset($cart_item['data']->tm_subscription_sign_up_fee_added) && isset($cart_item['data']->subscription_sign_up_fee)){
                        WC()->cart->cart_contents[$cart_key]['data']->subscription_sign_up_fee += $options_fee;
                        WC()->cart->cart_contents[$cart_key]['data']->tm_subscription_sign_up_fee_added=1;
                    }
                }
            }
        }
    }
}


?>