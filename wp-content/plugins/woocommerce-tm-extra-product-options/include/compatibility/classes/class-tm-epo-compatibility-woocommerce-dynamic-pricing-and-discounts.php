<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
    die();
}

final class TM_EPO_COMPATIBILITY_woocommerce_dynamic_pricing_and_discounts {

    protected static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {

        add_action( 'init', array( $this, 'add_compatibility' ) );
        add_filter( 'plugins_loaded', array( $this, 'add_compatibility_settings' ), 10, 1 );
    }

    public function init() {
        add_filter( 'wc_epo_autoload_path', array($this, 'wc_epo_autoload_path'), 10, 2 ); 
        add_filter( 'wc_epo_autoload_file', array($this, 'wc_epo_autoload_file'), 10, 2 );  
    }

    public function add_compatibility_settings(){
        add_filter( 'wc_epo_get_settings', array( $this, 'wc_epo_get_settings' ), 10, 1 );
    }

    public function wc_epo_get_settings($settings=array()){
        if(class_exists('RP_WCDPD')){
            $settings["tm_epo_dpd_enable"] = array("no",$this,"is_dpd_enabled");
            $settings["tm_epo_dpd_prefix"] = array("",$this,"is_dpd_enabled");
            $settings["tm_epo_dpd_suffix"] = array("",$this,"is_dpd_enabled");
        }
        return $settings;
    }
    
    public function is_dpd_enabled(){
        return class_exists('RP_WCDPD');
    }

    public function add_compatibility(){

        /** WooCommerce Dynamic Pricing & Discounts support **/
        if(!class_exists('RP_WCDPD')){
            return;
        }

        if (TM_EPO()->tm_epo_dpd_enable=="no"){
            add_action( 'woocommerce_cart_loaded_from_session', array($this, 'cart_loaded_from_session_2'), 2 );
            add_action( 'woocommerce_cart_loaded_from_session', array($this, 'cart_loaded_from_session_99999'), 99999 );
        }
        add_filter( 'woocommerce_cart_item_price', array($this, 'cart_item_price'), 101, 3 );       
        add_action( 'wc_epo_order_item_meta', array($this, 'wc_epo_order_item_meta'), 10,2 );

        add_filter( 'wc_epo_discounted_price', array($this, 'get_RP_WCDPD'), 10, 3 );

        add_filter( 'tm_epo_settings_headers', array($this, 'tm_epo_settings_headers'), 10, 1 );
        add_filter( 'tm_epo_settings_settings', array($this, 'tm_epo_settings_settings'), 10, 1 );

        add_filter( 'wc_epo_product_price_rules', array($this, 'wc_epo_product_price_rules'), 10, 2 );
        add_filter( 'wc_epo_template_args_tm_totals', array($this, 'wc_epo_template_args_tm_totals'), 10, 1 );
        add_action( 'wc_epo_template_tm_totals', array($this, 'wc_epo_template_tm_totals'), 10, 1 );
    }

    public function wc_epo_template_tm_totals($args){
        $tm_epo_dpd_prefix = $args['tm_epo_dpd_prefix'];
        $tm_epo_dpd_suffix = $args['tm_epo_dpd_suffix'];
        echo 'data-tm-epo-dpd-prefix="'.esc_attr($tm_epo_dpd_prefix).'" data-tm-epo-dpd-suffix="'.esc_attr($tm_epo_dpd_suffix).'" ';
    }
    public function wc_epo_template_args_tm_totals($args){
        $args["tm_epo_dpd_suffix"] = TM_EPO()->tm_epo_dpd_suffix;
        $args["tm_epo_dpd_prefix"] = TM_EPO()->tm_epo_dpd_prefix;
        $args["fields_price_rules"] = (TM_EPO()->tm_epo_dpd_enable=="no")?$args["fields_price_rules"]:1;
        return $args;
    }

    public function wc_epo_product_price_rules($price=array(), $product){
        if(!empty($GLOBALS['RP_WCDPD'])){
            $check_price=apply_filters('wc_epo_discounted_price', NULL, $product, NULL);
            if ($check_price){
                $price['product']=array();
                if ($check_price['is_multiprice']){
                    foreach ($check_price['rules'] as $variation_id => $variation_rule) {
                        foreach ($variation_rule as $rulekey => $pricerule) {
                            $price['product'][$variation_id][]=array(
                                "min"=>$pricerule["min"],
                                "max"=>$pricerule["max"],
                                "value"=>($pricerule["type"]!="percentage")?apply_filters( 'wc_epo_product_price', $pricerule["value"],"",false):$pricerule["value"],
                                "type"=>$pricerule["type"]
                                );
                        }
                    }
                }else{
                    foreach ($check_price['rules'] as $rulekey => $pricerule) {
                        $price['product'][0][]=array(
                            "min"=>$pricerule["min"],
                            "max"=>$pricerule["max"],
                            "value"=>($pricerule["type"]!="percentage")?apply_filters( 'wc_epo_product_price', $pricerule["value"],"",false):$pricerule["value"],
                            "type"=>$pricerule["type"]
                            );
                    }
                }
            }
            $price['price']=apply_filters( 'wc_epo_product_price', $product->get_price(),"",false);            
        }        
        return $price;
    }

    public function wc_epo_order_item_meta($item_id, $values){
        if ( ! empty( $values['tmcartepo'] ) ) {
            wc_add_order_item_meta( $item_id, '_tm_has_dpd', 1 );
        }
    }

    public function wc_epo_autoload_path($path,$original_class){
        // Composite products sometimes do not load the Discount and Pricing classes
        if ( $original_class=="RP_WCDPD_Pricing" && defined('TM_EPO_INCLUDED') && defined('RP_WCDPD_PLUGIN_PATH') ){
            $path = RP_WCDPD_PLUGIN_PATH . 'includes/classes/';
        }  
        return $path;
    }

    public function wc_epo_autoload_file($file,$original_class){
        // Composite products sometimes do not load the Discount and Pricing classes
        if ( $original_class=="RP_WCDPD_Pricing" && defined('TM_EPO_INCLUDED') && defined('RP_WCDPD_PLUGIN_PATH') ){
            $file = 'Pricing.php';
        }  
        return $file;
    }

    /** Admin settings **/
    public function tm_epo_settings_headers($headers=array()){
        $headers["dpd"] = __( 'Dynamic Pricing & Discounts', 'woocommerce-tm-extra-product-options' );
        return $headers;
    }

    /** Admin settings **/
    public function tm_epo_settings_settings($settings=array()){
        $label = __( 'Dynamic Pricing & Discounts', 'woocommerce-tm-extra-product-options' );;
        $settings["dpd"] =array(
            array(  
                'type' => 'tm_title',               
                'id' => 'epo_page_options',
                'title' => $label 
            ),
            array(
                'title' => __( 'Enable discounts on extra options', 'woocommerce-tm-extra-product-options' ),
                'desc'      => '<span>'.__( 'Enabling this will apply the product discounts to the extra options as well.', 'woocommerce-tm-extra-product-options' ).'</span>',
                'id'        => 'tm_epo_dpd_enable',
                'class'     => 'chosen_select',
                'css'       => 'min-width:300px;',
                'default'   => 'no',
                'type'      => 'select',
                'options'   => array(
                    'no'    => __( 'Disable', 'woocommerce-tm-extra-product-options' ),
                    'yes'   => __( 'Enable', 'woocommerce-tm-extra-product-options' )                       
                ),
                'desc_tip'  =>  false,
            ),
            array(
                'title' => __( 'Prefix label', 'woocommerce-tm-extra-product-options' ),
                'desc'      => '<span>'.__( 'Display a prefix label on product page.', 'woocommerce-tm-extra-product-options' ).'</span>',
                'id'        => 'tm_epo_dpd_prefix',
                'default'   => '',
                'type'      => 'text',                  
                'desc_tip'  =>  false,
            ),
            array(
                'title' => __( 'Suffix label', 'woocommerce-tm-extra-product-options' ),
                'desc'      => '<span>'.__( 'Display a suffix label on product page.', 'woocommerce-tm-extra-product-options' ).'</span>',
                'id'        => 'tm_epo_dpd_suffix',
                'default'   => '',
                'type'      => 'text',                  
                'desc_tip'  =>  false,
            ),
            array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),

        );
        return $settings;
    }

    /** WooCommerce Dynamic Pricing & Discounts support **/
    public function cart_loaded_from_session_2(){

        $cart_contents = WC()->cart->cart_contents;

        if (is_array($cart_contents)){
            foreach ($cart_contents as $cart_item_key => $cart_item) {
                if (isset($cart_item['tm_epo_product_original_price'])){
                    WC()->cart->cart_contents[$cart_item_key]['data']->price = $cart_item['tm_epo_product_original_price'];
                    WC()->cart->cart_contents[$cart_item_key]['tm_epo_doing_adjustment'] = true;
                }
            }
        }

    }

    /** WooCommerce Dynamic Pricing & Discounts support **/
    public function cart_loaded_from_session_99999(){

        $cart_contents = WC()->cart->cart_contents;
        if (is_array($cart_contents)){
            foreach ($cart_contents as $cart_item_key => $cart_item) {
                $current_product_price=WC()->cart->cart_contents[$cart_item_key]['data']->price;

                if (isset($cart_item['tm_epo_options_prices']) && !empty($cart_item['tm_epo_doing_adjustment'])){
                    WC()->cart->cart_contents[$cart_item_key]['tm_epo_product_after_adjustment']=$current_product_price;
                    WC()->cart->cart_contents[$cart_item_key]['data']->adjust_price($cart_item['tm_epo_options_prices']);
                    unset(WC()->cart->cart_contents[$cart_item_key]['tm_epo_doing_adjustment']);
                }
            }
        }

    }

    /**
     * Replace cart html prices for WooCommerce Dynamic Pricing & Discounts
     * 
     * @access public
     * @param string $item_price
     * @param array $cart_item
     * @param string $cart_item_key
     * @return string
     */
     public function cart_item_price($item_price="", $cart_item="", $cart_item_key=""){

        if (!isset($cart_item['tmcartepo'])) {
            return $item_price;
        }
        if (!isset($cart_item['rp_wcdpd'])) {
            return $item_price;
        }

        // Get price to display
        $price = TM_EPO()->get_price_for_cart(false,$cart_item,"");

        // Format price to display
        $price_to_display = $price;
        if (TM_EPO()->tm_epo_cart_field_display=="advanced"){
            $original_price_to_display = TM_EPO()->get_price_for_cart($cart_item['tm_epo_product_original_price'],$cart_item,"");
            if (TM_EPO()->tm_epo_dpd_enable=="yes"){
                $price=$this->get_RP_WCDPD($cart_item['tm_epo_product_original_price'], wc_get_product($cart_item['data']->id), $cart_item_key);
                $price_to_display = TM_EPO()->get_price_for_cart($price,$cart_item,"");
            }else{
                $price=$cart_item['data']->price;
                $price=$price-$cart_item['tm_epo_options_prices'];
                $price_to_display = TM_EPO()->get_price_for_cart($price,$cart_item,"");
            }
        }else{
            $original_price_to_display = TM_EPO()->get_price_for_cart($cart_item['tm_epo_product_price_with_options'],$cart_item,"");
        }

        $item_price = '<span class="rp_wcdpd_cart_price"><del>' . $original_price_to_display . '</del> <ins>' . $price_to_display . '</ins></span>';

        return $item_price;
    }

    // get WooCommerce Dynamic Pricing & Discounts price for options
    // modified from get version from Pricing class
    private function get_RP_WCDPD_single($field_price,$cart_item_key,$pricing){
        if (TM_EPO()->tm_epo_dpd_enable == 'no' || !isset($pricing->items[$cart_item_key])) {
            return $field_price;
        }

        $price = $field_price;
        $original_price = $price;

        if (in_array($pricing->pricing_settings['apply_multiple'], array('all', 'first'))) {
            foreach ($pricing->apply['global'] as $rule_key => $apply) {
                if ($deduction = $pricing->apply_rule_to_item($rule_key, $apply, $cart_item_key, $pricing->items[$cart_item_key], false, $price)) {

                    if ($apply['if_matched'] == 'other' && isset($pricing->applied) && isset($pricing->applied['global'])) {
                        if (count($pricing->applied['global']) > 1 || !isset($pricing->applied['global'][$rule_key])) {
                            continue;
                        }
                    }

                    $pricing->applied['global'][$rule_key] = 1;
                    $price = $price - $deduction;
                }
            }
        }
        else if ($pricing->pricing_settings['apply_multiple'] == 'biggest') {

            $price_deductions = array();

            foreach ($pricing->apply['global'] as $rule_key => $apply) {

                if ($apply['if_matched'] == 'other' && isset($pricing->applied) && isset($pricing->applied['global'])) {
                    if (count($pricing->applied['global']) > 1 || !isset($pricing->applied['global'][$rule_key])) {
                        continue;
                    }
                }

                if ($deduction = $pricing->apply_rule_to_item($rule_key, $apply, $cart_item_key, $pricing->items[$cart_item_key], false)) {
                    $price_deductions[$rule_key] = $deduction;
                }
            }

            if (!empty($price_deductions)) {
                $max_deduction = max($price_deductions);
                $rule_key = array_search($max_deduction, $price_deductions);
                $pricing->applied['global'][$rule_key] = 1;
                $price = $price - $max_deduction;
            }

        }

        // Make sure price is not negative
        // $price = ($price < 0) ? 0 : $price;

        if ($price != $original_price) {
            return $price;
        }
        else {
            return $field_price;
        }
    }

    // get WooCommerce Dynamic Pricing & Discounts price rules
    public function get_RP_WCDPD($field_price=null,$product,$cart_item_key=null){
        $price = null;
                
        if(class_exists('RP_WCDPD') && class_exists('RP_WCDPD_Pricing') && !empty($GLOBALS['RP_WCDPD'])){
            
            $tm_RP_WCDPD=$GLOBALS['RP_WCDPD'];

            $selected_rule = null;

            if ($field_price!==null && $cart_item_key!==null){
                return $this->get_RP_WCDPD_single($field_price,$cart_item_key,$tm_RP_WCDPD->pricing);
            }
            
            $dpd_version_compare=version_compare( RP_WCDPD_VERSION, '1.0.13', '<' );
            // Iterate over pricing rules and use the first one that has this product in conditions (or does not have if condition "not in list")
            if (isset($tm_RP_WCDPD->opt['pricing']['sets']) 
                && count($tm_RP_WCDPD->opt['pricing']['sets']) ) {
                foreach ($tm_RP_WCDPD->opt['pricing']['sets'] as $rule_key => $rule) {
                    if ($rule['method'] == 'quantity' && $validated_rule = RP_WCDPD_Pricing::validate_rule($rule)) {
                        if ($dpd_version_compare){
                            if ($validated_rule['selection_method'] == 'all' && $tm_RP_WCDPD->user_matches_rule($validated_rule['user_method'], $validated_rule['roles'])) {
                                $selected_rule = $validated_rule;
                                break;
                            }
                            if ($validated_rule['selection_method'] == 'categories_include' && count(array_intersect($tm_RP_WCDPD->get_product_categories($product->id), $validated_rule['categories'])) > 0 && $tm_RP_WCDPD->user_matches_rule($validated_rule['user_method'], $validated_rule['roles'])) {
                                $selected_rule = $validated_rule;
                                break;
                            }
                            if ($validated_rule['selection_method'] == 'categories_exclude' && count(array_intersect($tm_RP_WCDPD->get_product_categories($product->id), $validated_rule['categories'])) == 0 && $tm_RP_WCDPD->user_matches_rule($validated_rule['user_method'], $validated_rule['roles'])) {
                                $selected_rule = $validated_rule;
                                break;
                            }
                            if ($validated_rule['selection_method'] == 'products_include' && in_array($product->id, $validated_rule['products']) && $tm_RP_WCDPD->user_matches_rule($validated_rule['user_method'], $validated_rule['roles'])) {
                                $selected_rule = $validated_rule;
                                break;
                            }
                            if ($validated_rule['selection_method'] == 'products_exclude' && !in_array($product->id, $validated_rule['products']) && $tm_RP_WCDPD->user_matches_rule($validated_rule['user_method'], $validated_rule['roles'])) {
                                $selected_rule = $validated_rule;
                                break;
                            }
                        }else{
                            if ($validated_rule['selection_method'] == 'all' && $tm_RP_WCDPD->user_matches_rule($validated_rule)) {
                                $selected_rule = $validated_rule;
                                break;
                            }
                            if ($validated_rule['selection_method'] == 'categories_include' && count(array_intersect($tm_RP_WCDPD->get_product_categories($product->id), $validated_rule['categories'])) > 0 && $tm_RP_WCDPD->user_matches_rule($validated_rule)) {
                                $selected_rule = $validated_rule;
                                break;
                            }
                            if ($validated_rule['selection_method'] == 'categories_exclude' && count(array_intersect($tm_RP_WCDPD->get_product_categories($product->id), $validated_rule['categories'])) == 0 && $tm_RP_WCDPD->user_matches_rule($validated_rule)) {
                                $selected_rule = $validated_rule;
                                break;
                            }
                            if ($validated_rule['selection_method'] == 'products_include' && in_array($product->id, $validated_rule['products']) && $tm_RP_WCDPD->user_matches_rule($validated_rule)) {
                                $selected_rule = $validated_rule;
                                break;
                            }
                            if ($validated_rule['selection_method'] == 'products_exclude' && !in_array($product->id, $validated_rule['products']) && $tm_RP_WCDPD->user_matches_rule($validated_rule)) {
                                $selected_rule = $validated_rule;
                                break;
                            }
                        }
                    }
                }
            }
            
            if (is_array($selected_rule)) {

                // Quantity
                if ($selected_rule['method'] == 'quantity' && isset($selected_rule['pricing']) && in_array($selected_rule['quantities_based_on'], array('exclusive_product','exclusive_variation','exclusive_configuration')) ) {

                        if ($product->product_type == 'variable' || $product->product_type == 'variable-subscription') {
                            $product_variations = $product->get_available_variations();
                        }

                        // For variable products only - check if prices differ for different variations
                        $multiprice_variable_product = false;

                        if ( ($product->product_type == 'variable' || $product->product_type == 'variable') && !empty($product_variations)) {
                            $last_product_variation = array_slice($product_variations, -1);
                            $last_product_variation_object = new WC_Product_Variable($last_product_variation[0]['variation_id']);
                            $last_product_variation_price = $last_product_variation_object->get_price();

                            foreach ($product_variations as $variation) {
                                $variation_object = new WC_Product_Variable($variation['variation_id']);

                                if ($variation_object->get_price() != $last_product_variation_price) {
                                    $multiprice_variable_product = true;
                                }
                            }
                        }

                        if ($multiprice_variable_product) {
                            $variation_table_data = array();

                            foreach ($product_variations as $variation) {
                                $variation_product = new WC_Product_Variation($variation['variation_id']);
                                $variation_table_data[$variation['variation_id']] = $tm_RP_WCDPD->pricing_table_calculate_adjusted_prices($selected_rule['pricing'], $variation_product->get_price());
                            }
                            $price=array();
                            $price['is_multiprice']=true;
                            $price['rules']=$variation_table_data;
                        }
                        else {
                            if ($product->product_type == 'variable' && !empty($product_variations)) {
                                $variation_product = new WC_Product_Variation($last_product_variation[0]['variation_id']);
                                $table_data = $tm_RP_WCDPD->pricing_table_calculate_adjusted_prices($selected_rule['pricing'], $variation_product->get_price());
                            }
                            else {
                                $table_data = $tm_RP_WCDPD->pricing_table_calculate_adjusted_prices($selected_rule['pricing'], $product->get_price());
                            }
                            $price=array();
                            $price['is_multiprice']=false;
                            $price['rules']=$table_data;
                        }                   
                }

            }
        }
        if ($field_price!==null){
            $price=$field_price;
        }
        return $price;
    }

}


?>