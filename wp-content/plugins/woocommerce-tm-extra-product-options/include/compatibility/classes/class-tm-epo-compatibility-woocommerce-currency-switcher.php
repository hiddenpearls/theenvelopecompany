<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
    die();
}

final class TM_EPO_COMPATIBILITY_woocommerce_currency_switcher {

    protected static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {

        add_action( 'wc_epo_add_compatibility', array( $this, 'add_compatibility' ) );

    }

    public function init() {
        
    }

    public function add_compatibility(){
        /** WooCommerce Currency Switcher support (realmag777) **/
        add_filter('wc_epo_product_price', array( $this, 'wc_epo_product_price' ), 10, 3);

        add_filter('wc_epo_get_current_currency_price', array( $this, 'wc_epo_get_current_currency_price' ), 10, 3);
        add_filter('wc_epo_remove_current_currency_price', array( $this, 'wc_epo_remove_current_currency_price' ), 10, 2);
                
        add_filter('wc_epo_get_currency_price', array( $this, 'tm_wc_epo_get_currency_price' ), 10, 5);
        add_filter('woocommerce_tm_epo_price_add_on_cart', array( $this, 'tm_epo_price_add_on_cart' ), 10, 2);
        
        add_filter('woocommerce_tm_epo_price_per_currency_diff', array( $this, 'tm_epo_price_per_currency_diff' ), 10, 1);

        add_filter('wc_epo_get_price_html', array( $this, 'wc_epo_get_price_html' ), 10, 2);
    }

    /** Add additional info in price html **/
    public function wc_epo_get_price_html($price_html, $product){

        if (class_exists('WOOCS')){
            global $WOOCS;

            $currencies = $WOOCS->get_currencies();

            $customer_price_format = get_option('woocs_customer_price_format', '');

            if (!empty($customer_price_format)){
                $txt = '<span class="woocs_price_code" data-product-id="' . $product->id . '">' . $customer_price_format . '</span>';
                $txt = str_replace('__PRICE__', $price_html, $txt);
                $price_html = str_replace('__CODE__', $WOOCS->current_currency, $txt);
            }


            //hide cents on front as html element
            if (!in_array($WOOCS->current_currency, $WOOCS->no_cents)){
                if ($currencies[$WOOCS->current_currency]['hide_cents'] == 1)
                {
                    $price_html = preg_replace('/\.[0-9][0-9]/', '', $price_html);
                }
            }

            if ((get_option('woocs_price_info', 0) AND ! is_admin()) OR isset($_REQUEST['get_product_price_by_ajax'])){

                

                $info = "<ul>";
                $current_currency = $WOOCS->current_currency;
                foreach ($currencies as $сurr){
                    if ( !isset($сurr['name']) || $сurr['name'] == $current_currency){
                        continue;
                    }
                    $WOOCS->current_currency = $сurr['name'];
                    $value = $product->price * $currencies[$сurr['name']]['rate'];
                    $value = number_format($value, 2, $WOOCS->decimal_sep, '');
                    if ($product->product_type != 'variable'){
                        $info.= "<li><b>" . $сurr['name'] . "</b>: " . $WOOCS->wc_price($value, false, array('currency' => $сurr['name'])) . "</li>";
                    }else{
                        //https://gist.github.com/mikejolley/1600117
                        $min_value = $product->min_variation_price * $currencies[$сurr['name']]['rate'];
                        $min_value = number_format($min_value, 2, $WOOCS->decimal_sep, '');
                        //***
                        $max_value = $product->max_variation_price * $currencies[$сurr['name']]['rate'];
                        $max_value = number_format($max_value, 2, $WOOCS->decimal_sep, '');
                        //+++
                        $var_price = $WOOCS->wc_price($min_value, array('currency' => $сurr['name']));
                        $var_price.= ' - ';
                        $var_price.= $WOOCS->wc_price($max_value, array('currency' => $сurr['name']));
                        $info.= "<li><b>" . $сurr['name'] . "</b>: " . $var_price . "</li>";
                    }
                }
                $WOOCS->current_currency = $current_currency;
                $info.= "</ul>";
                $info = '<div class="woocs_price_info"><span class="woocs_price_info_icon"></span>' . $info . '</div>';
                $price_html.=$info;
            }
        }

        return $price_html;

    }

    /** Check WooCommerce Currency Switcher version **/
    private function _get_woos_price_calculation(){
        
        $oldway=false;
        if (class_exists('WOOCS')){
            global $WOOCS;
            $v=intval($WOOCS->the_plugin_version);
            if($v == 1){
                if (version_compare($WOOCS->the_plugin_version, '1.0.9', '<')){
                    $oldway=true;
                }
            }else{
                if (version_compare($WOOCS->the_plugin_version, '2.0.9', '<')){
                    $oldway=true;
                }
            }
        }

        return $oldway;

    }

    /** WooCommerce Currency Switcher support (realmag777)
     * This filter is currently only used for product prices.
     */
    public function wc_epo_product_price($price="",$type="",$is_meta_value=true,$currency=false){
        if (class_exists('WOOCS')){
            global $WOOCS;
            if (property_exists($WOOCS, 'the_plugin_version')){
                if ( !$is_meta_value && !$this->_get_woos_price_calculation() ){
                    if($WOOCS->is_multiple_allowed){
                        // no converting needed
                    }else{
                        $price = apply_filters('woocs_exchange_value', $price);
                    }
                }else{
                    $currencies = $WOOCS->get_currencies();
                    if (!$currency){
                        $current_currency=$WOOCS->current_currency;
                    }else{
                        $current_currency=$currency;
                    }
                    if (isset($currencies[$current_currency]) && isset($currencies[$current_currency]['rate'])){
                        $price=(double)$price* (double)$currencies[$current_currency]['rate'];
                    }
                }
            }
        }elseif (class_exists('WooCommerce_All_in_One_Currency_Converter_Main')){
            global $woocommerce_all_in_one_currency_converter;
            $user_currency = $woocommerce_all_in_one_currency_converter->settings->session_currency;
            $currency_data = $woocommerce_all_in_one_currency_converter->settings->get_currency_data();
            $conversion_method = $woocommerce_all_in_one_currency_converter->settings->get_conversion_method();

            if (!$currency){
                $current_currency=$user_currency;
            }else{
                $current_currency=$currency;
            }
            if (isset($currency_data[$current_currency]) && isset($currency_data[$current_currency]['rate'])){
                $price=(double)$price* (double)$currency_data[$current_currency]['rate'];    
            }            
        }
        
        return $price;
    }

    /** WooCommerce Currency Switcher support (realmag777) 
     * Adjusts option prices when using different currency price for versions > 2.0.9
     * MUST BE USED ONLY WHEN IT IS KNOWN THAT THE PRICE IS DIFFERENT !
     */
    public function tm_epo_price_per_currency_diff($price=0){
        if( class_exists('WOOCS') && !$this->_get_woos_price_calculation() ){
            $price = $this->wc_epo_remove_current_currency_price($price);
        }
        return $price;
    }

    /** WooCommerce Currency Switcher support (realmag777) **/
    public function wc_epo_get_current_currency_price($price="",$type="",$is_meta_value=true){
        global $woocommerce_wpml;
        if (is_array($type)){
            $type="";
        }
        // Check if the price should be processed only once
        if(in_array((string)$type, array('', 'char', 'step', 'intervalstep', 'charnofirst', 'charnospaces', 'charnon', 'charnonnospaces', 'fee', 'subscriptionfee'))) {

            if(TM_EPO_WPML()->is_active() && $woocommerce_wpml && property_exists($woocommerce_wpml, 'multi_currency') && $woocommerce_wpml->multi_currency){

                $price=apply_filters('wcml_raw_price_amount',$price);

            }elseif (class_exists('WOOCS') || class_exists('WooCommerce_All_in_One_Currency_Converter_Main')){
            
                $price=$this->wc_epo_product_price($price,$type,$is_meta_value);

            }
            else {

                $price = $this->get_price_in_currency($price);

            }

        }

        return $price;

    }

    public function tm_wc_epo_get_currency_price($price="", $currency=false, $type="", $is_meta_value=true, $current_currency=false){
        if (!$currency){
            return $this->wc_epo_get_current_currency_price($price,$type,$is_meta_value);
        }
        if ($current_currency && $current_currency==$currency){
            return $price;
        }
        
        global $woocommerce_wpml;
        if(TM_EPO_WPML()->is_active() && $woocommerce_wpml && property_exists($woocommerce_wpml, 'multi_currency') && $woocommerce_wpml->multi_currency){
            //todo:doesn't work at the moment
            $price=apply_filters('wcml_raw_price_amount',$price);

        }elseif (class_exists('WOOCS') || class_exists('WooCommerce_All_in_One_Currency_Converter_Main')){
            
            $price=$this->wc_epo_product_price($price,$type,$is_meta_value,$currency);

        }
        else {

            $price = $this->get_price_in_currency($price,$currency);

        }

        return $price;

    }

    /** WooCommerce Currency Switcher support (realmag777) **/
    public function wc_epo_remove_current_currency_price($price="",$type=""){
        global $woocommerce_wpml;
        if (class_exists('WOOCS')){
            global $WOOCS;
            $currencies = $WOOCS->get_currencies();
            $current_currency=$WOOCS->current_currency;
            if (!empty($currencies[$current_currency]['rate'])){
                $price=(double)$price/ $currencies[$current_currency]['rate'];  
            }
        }elseif (class_exists('WooCommerce_All_in_One_Currency_Converter_Main')){
            global $woocommerce_all_in_one_currency_converter;
            $user_currency = $woocommerce_all_in_one_currency_converter->settings->session_currency;
            $currency_data = $woocommerce_all_in_one_currency_converter->settings->get_currency_data();
            $conversion_method = $woocommerce_all_in_one_currency_converter->settings->get_conversion_method();

            if (!$currency){
                $current_currency=$user_currency;
            }else{
                $current_currency=$currency;
            }
            if (isset($currency_data[$current_currency]) && !empty($currency_data[$current_currency]['rate'])){
                $price=(double)$price/ (double)$currency_data[$current_currency]['rate'];
            }
        }elseif(TM_EPO_WPML()->is_active() && $woocommerce_wpml && property_exists($woocommerce_wpml, 'multi_currency') && $woocommerce_wpml->multi_currency){

            $price=$woocommerce_wpml->multi_currency->unconvert_price_amount($price);

        }else{
            $from_currency = get_option('woocommerce_currency');
            $to_currency = tc_get_woocommerce_currency();
            $price = $this->get_price_in_currency($price, $from_currency, $to_currency);
        }
        return $price;
    }

    /**
     * Basic integration with WooCommerce Currency Switcher, developed by Aelia
     * (http://aelia.co). This method can be used by any 3rd party plugin to
     * return prices converted to the active currency.
     *
     * @param double price The source price.
     * @param string to_currency The target currency. If empty, the active currency
     * will be taken.
     * @param string from_currency The source currency. If empty, WooCommerce base
     * currency will be taken.
     * @return double The price converted from source to destination currency.
     * @author Aelia <support@aelia.co>
     * @link http://aelia.co
     */
    protected function get_price_in_currency($price, $to_currency = null, $from_currency = null) {
        if(empty($from_currency)) {
            $from_currency = get_option('woocommerce_currency');
        }
        if(empty($to_currency)) {
            $to_currency = tc_get_woocommerce_currency();
        }
        return apply_filters('wc_aelia_cs_convert', $price, $from_currency, $to_currency);
    }

    /** WooCommerce Currency Switcher support (realmag777) **/
    public function tm_epo_price_add_on_cart($price="",$type=""){
        global $woocommerce_wpml;
        if (!class_exists('WOOCS') && !class_exists('WooCommerce_All_in_One_Currency_Converter_Main')){
            $price = apply_filters('wc_epo_get_current_currency_price',$price,$type);
        }
        return $price;
    }

}


?>