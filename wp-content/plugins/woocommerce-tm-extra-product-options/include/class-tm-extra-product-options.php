<?php
// Direct access security
if ( !defined( 'TM_EPO_PLUGIN_SECURITY' ) ) {
	die();
}

/**
 * Main plugin class responsible for displaying the Extra Product Options on the frontend
 */
final class TM_Extra_Product_Options {

	public $version        = TM_EPO_VERSION;

	private $_namespace     = 'tm-extra-product-options';

	/** Holds the current post id **/
	private $postid_pre=false;
	
	/** Helper for determining various conditionals **/
	private $wc_vars=array(
		"is_product" 			=> false,
		"is_shop" 				=> false,
		"is_product_category" 	=> false,
		"is_product_tag" 		=> false,
		"is_cart"				=> false,
		"is_checkout" 			=> false,
		"is_account_page" 		=> false,
		"is_ajax" 				=> false,
		);

	/** Product custom settings **/
	public $tm_meta_cpf = array();

	/** Product custom settings options **/
	public $meta_fields = array(
			'exclude' 					=> '',
			'price_override' 			=> '',
			'override_display' 			=> '',
			'override_final_total_box' 	=> '',
			'override_enabled_roles' 	=> '',
			'override_disabled_roles' 	=> ''
		);

	/** Cache for all the extra options **/
	private $cpf=array();

	/** Hold the upload directory for the upload element **/
	public $upload_dir="/extra_product_options/";

	/** Replacement name for Subscription fee fields **/
	public $fee_name="tmfee_";
	public $fee_name_class="tmcp-sub-fee-field";
	/** Holds the total fee added by Subscription fee fields **/
	public $tmfee=0;

	/** Replacement name for cart fee fields **/
	public $cart_fee_name="tmcartfee_";
	public $cart_fee_class="tmcp-fee-field";

	/** Array of element types that get posted **/
	private $element_post_types=array();

	/** Holds builder element attributes **/
	private $tm_original_builder_elements=array();

	/** Holds modified builder element attributes **/
	public $tm_builder_elements=array();

	/** Inline styles **/
	public $inline_styles;
	public $inline_styles_head;

	/** Edit option in cart helper **/
	private $new_add_to_cart_key = false;
	/** Holds the cart key when editing a product in the cart **/
	public $cart_edit_key = null;

	/** Holds all of the plugin settings **/
	private $tm_plugin_settings=array(
			// General
			"tm_epo_roles_enabled" 						=> "@everyone",
			"tm_epo_roles_disabled" 					=> "",
			"tm_epo_final_total_box" 					=> "normal",
			"tm_epo_enable_final_total_box_all" 		=> "no",
			"tm_epo_strip_html_from_emails" 			=> "yes",
			"tm_epo_no_lazy_load" 						=> "yes",
			"tm_epo_enable_shortcodes" 					=> "no",
			// Display
			"tm_epo_display" 							=> "normal",
			"tm_epo_options_placement" 					=> "woocommerce_before_add_to_cart_button",
			"tm_epo_options_placement_custom_hook" 		=> "",
			"tm_epo_totals_box_placement" 				=> "woocommerce_before_add_to_cart_button",
			"tm_epo_totals_box_placement_custom_hook" 	=> "",
			"tm_epo_floating_totals_box" 				=> "disable",
			"tm_epo_force_select_options" 				=> "normal",
			"tm_epo_enable_in_shop" 					=> "no",
			"tm_epo_remove_free_price_label" 			=> "no",
			"tm_epo_hide_upload_file_path" 				=> "yes",
			"tm_epo_show_only_active_quantities"		=> "",
			"tm_epo_hide_add_cart_button" 				=> "no",
			"tm_epo_auto_hide_price_if_zero" 			=> "no",
			"tm_epo_wpml_order_translate" 				=> "no",
			"tm_epo_include_possible_option_pricing" 	=> "no",
			"tm_epo_use_from_on_price" 					=> "no",
			// Cart
			"tm_epo_clear_cart_button" 					=> "normal",
			"tm_epo_cart_field_display" 				=> "normal",
			"tm_epo_hide_options_in_cart" 				=> "normal",
			"tm_epo_hide_options_prices_in_cart" 		=> "normal",
			"tm_epo_no_negative_priced_products" 		=> "no",
			"tm_epo_show_image_replacement" 			=> "no",
			// Strings
			"tm_epo_separator_cart_text" 				=> "", // allow blank separator
			"tm_epo_final_total_text" 					=> "",
			"tm_epo_options_total_text" 				=> "",
			"tm_epo_subscription_fee_text" 				=> "",
			"tm_epo_replacement_free_price_text" 		=> "",
			"tm_epo_reset_variation_text" 				=> "",
			"tm_epo_edit_options_text" 					=> "",
			"tm_epo_additional_options_text" 			=> "",
			"tm_epo_close_button_text" 					=> "",
			"tm_epo_closeText" 							=> "",
			"tm_epo_currentText" 						=> "",
			"tm_epo_slider_prev_text" 					=> "",
			"tm_epo_slider_next_text" 					=> "",
			"tm_epo_force_select_text" 					=> "",
			"tm_epo_empty_cart_text" 					=> "",
			"tm_epo_this_field_is_required_text" 		=> "",
			"tm_epo_characters_remaining_text" 			=> "",
			// Style
			"tm_epo_css_styles" 						=> "",
			"tm_epo_css_styles_style" 					=> "round",
			"tm_epo_css_selected_border" 				=> "",
			// Global
			"tm_epo_global_enable_validation" 			=> "yes",
			"tm_epo_global_prevent_options_from_emails" => "no",
			"tm_epo_global_override_product_price" 		=> "",
			"tm_epo_global_reset_options_after_add" 	=> "no",
			"tm_epo_global_input_decimal_separator" 	=> "",
			"tm_epo_global_displayed_decimal_separator" => "",
			"tm_epo_global_radio_undo_button" 			=> "",
			"tm_epo_global_required_indicator" 			=> "*",
			"tm_epo_global_required_indicator_position" => "right",
			"tm_epo_global_tax_string_suffix" 			=> "no",
			"tm_epo_global_datepicker_theme" 			=> "",
			"tm_epo_global_datepicker_size" 			=> "",
			"tm_epo_global_datepicker_position" 		=> "",
			"tm_epo_global_load_generated_styles_inline"=> "yes",
			// Upload manager
			"tm_epo_upload_folder" 						=> "extra_product_options",
			// DPD plugin
			//"tm_epo_dpd_enable" 						=> array("no","is_dpd_enabled"),
			//"tm_epo_dpd_prefix" 						=> array("","is_dpd_enabled"),
			//"tm_epo_dpd_suffix" 						=> array("","is_dpd_enabled"),
			// Bookings
			//"tm_epo_bookings_person" 					=> "yes",
			//"tm_epo_bookings_block" 					=> "yes"
		);

	/** Prevent option duplication for bad coded themes **/
	private $tm_options_have_been_displayed=false;
	private $tm_options_single_have_been_displayed=false;
	private $tm_options_totals_have_been_displayed=false;

	/** Enable/disable flag for outputing plugin specific classes to the post_class filter  **/
	private $tm_related_products_output=true;
	
	/** Set of variables to ensure that the correct options are displayed on complex layouts **/	
	private $epo_id=0;
	private $epo_internal_counter=0;
	private $epo_internal_counter_check=array();
	private $current_product_id_to_be_displayed=0;
	private $current_product_id_to_be_displayed_check=array();

	/** Float direction for radio and checkboxes image replacements **/
	public $float_direction="left";
	public $float_direction_opposite="right";
	private $is_get_from_session=false;

	/** Cart edit key**/
	public $cart_edit_key_var = 'tm_cart_item_key';
	public $cart_edit_key_var_alt = 'tc_cart_edit_key';

	/** TM_Extra_Product_Options single instance **/
	protected static $_instance = null;

	/**
	 * Returns the instance of the plugin.
	 *
	 * @return TM_Extra_Product_Options.
	 */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /** Dummy function **/
    public function init(){
        return;
    }

	/** Initializes the plugin **/
	public function __construct() {
		$this->inline_styles 		= '';
		$this->inline_styles_head 	= '';
		$this->is_bto 				= false;
		$this->noactiondisplay 		= false;
		
		$this->cart_edit_key_var 			= apply_filters('wc_epo_cart_edit_key_var','tm_cart_item_key');
		$this->cart_edit_key_var_alt 		= apply_filters('wc_epo_cart_edit_key_var_alt','tc_cart_edit_key');

		$this->cart_edit_key 		= isset($_REQUEST[$this->cart_edit_key_var])
										?
											$_REQUEST[$this->cart_edit_key_var]
										: 
											(
												( isset($_REQUEST[$this->cart_edit_key_var_alt]) ) 
													?
														$_REQUEST[$this->cart_edit_key_var_alt]
													:
														null
											);
		
		$this->get_plugin_settings();
		$this->get_override_settings();
		$this->add_plugin_actions();
		
		/** Add compatibility actions and filters with other plugins and themes **/
		TM_EPO_COMPATIBILITY()->init();

		add_action( 'plugins_loaded', array($this,'get_plugin_settings'), 11 );
		add_action( 'plugins_loaded', array($this,'tm_epo_add_elements'), 12 );
		
	}

	/** Gets all of the plugin settings **/
	public function get_plugin_settings(){

		foreach ( apply_filters('wc_epo_get_settings',$this->tm_plugin_settings) as $key => $value) {
			if (is_array($value)){
				$method = $value[2];
				$classname = $value[1];
				if ( call_user_func(array($classname, $method)) ){
					$this->$key = get_option( $key );
					if (!$this->$key){
						$this->$key = $value[0];
					}

				}else{
					$this->$key = $value[0];
				}
			}else{
				$this->$key = get_option( $key );
				if (!$this->$key){
					$this->$key = $value;
				}
			}
		}

		if ($this->tm_epo_options_placement=="custom"){
			$this->tm_epo_options_placement = $this->tm_epo_options_placement_custom_hook;
		}
		
		if ($this->tm_epo_totals_box_placement=="custom"){
			$this->tm_epo_totals_box_placement = $this->tm_epo_totals_box_placement_custom_hook;
		}

		$this->upload_dir=$this->tm_epo_upload_folder;
		$this->upload_dir=str_replace("/", "", $this->upload_dir);
		$this->upload_dir=sanitize_file_name( $this->upload_dir );
		$this->upload_dir="/".$this->tm_epo_upload_folder."/";
		
	}

	/** Gets custom settings for the current product **/
	public function get_override_settings(){
		foreach ( $this->meta_fields as $key=>$value ) {
			$this->tm_meta_cpf[$key] = $value;
		}
	}

	/** Add required actions and filters **/
	public function add_plugin_actions(){

		/** Initialize custom product settings **/
		if ( $this->is_enabled_shortcodes() && !$this->is_quick_view() ){
			add_action( 'init', array( $this, 'init_settings_pre' ) );
		}else{
			if ( $this->is_quick_view() ){
				add_action( 'init', array( $this, 'init_settings' ) );
			}else{
				add_action( 'template_redirect', array( $this, 'init_settings' ) );	
			}					
		}

		/** Add custom inline css **/
		add_action( 'template_redirect', array( $this, 'tm_variation_css_check' ),9999 );

		/** Load js,css files **/
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ), 5 );
		add_action( 'woocommerce_tm_custom_price_fields_enqueue_scripts', array( $this, 'custom_frontend_scripts' ) );
		add_action( 'woocommerce_tm_epo_enqueue_scripts', array( $this, 'custom_frontend_scripts' ) );

		/** Custom optional dequeue_scripts **/
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_scripts' ) ,9999);

		/** Ensures the correct display order of options when multiple prodcuts are displayed **/
		add_action( 'woocommerce_before_single_product', array( $this, 'tm_woocommerce_before_single_product' ) ,1);
		add_action( 'woocommerce_after_single_product', array( $this, 'tm_woocommerce_after_single_product' ) ,9999);

		/** Display in frontend **/
		add_action( 'woocommerce_tm_epo', array( $this, 'frontend_display' ) );
		add_action( 'woocommerce_tm_epo_fields', array( $this, 'tm_epo_fields' ) );
		add_action( 'woocommerce_tm_epo_totals', array( $this, 'tm_epo_totals' ) );

		/** Compatibility for older plugin versions  **/
		add_action( 'woocommerce_tm_custom_price_fields', array( $this, 'frontend_display' ) );
		add_action( 'woocommerce_tm_custom_price_fields_only', array( $this, 'tm_epo_fields' ) );
		add_action( 'woocommerce_tm_custom_price_fields_totals', array( $this, 'tm_epo_totals' ) );

		/** Cart manipulation **/
		// Modifies the cart item
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 9999, 1 );
		// Load cart data on every page load
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 9999, 2 );
		// Gets cart item to display in the frontend
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 50, 2 );
		// Add item data to the cart
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 50, 3 );
		// Add meta to order
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 50, 2 );
		// Validate upon adding to cart
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 50, 6 );
		// Gets saved option when using the order again function
		add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'order_again_cart_item_data' ), 50, 3 );
		// Alter the product thumbnail in cart
		add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'tm_woocommerce_cart_item_thumbnail' ), 50, 3 );
		// Alter the product thumbnail in order
		add_filter( 'woocommerce_admin_order_item_thumbnail', array( $this, 'tm_woocommerce_admin_order_item_thumbnail' ), 50, 3 );
		// Ensures correct price is shown on minicart
		add_action( 'woocommerce_before_mini_cart', array( $this, 'tm_recalculate_total' ) );

		// Cart edit key
		add_action( 'woocommerce_cart_loaded_from_session', array($this, 'cart_loaded_from_session_1'), 1 );
		// Calculate totals on remove from cart/update
		add_action( 'woocommerce_update_cart_action_cart_updated', array($this, 'tm_woocommerce_update_cart_action_cart_updated'), 9999,1 );

		//add_action( 'woocommerce_calculate_totals', array( $this, 'tm_woocommerce_calculate_totals' ), 50 );

		/** Empty cart button **/
		if ($this->tm_epo_clear_cart_button=="show"){
			add_action( 'woocommerce_cart_actions', array( $this, 'add_empty_cart_button' ) );
			// check for empty-cart get param to clear the cart
			add_action( 'init', array( $this, 'clear_cart' ) );
		}

		/** Force Select Options **/
		add_filter( 'woocommerce_add_to_cart_url', array( $this, 'add_to_cart_url' ), 50, 1 );
		add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_to_cart_url' ), 50, 1 );
		add_action( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text' ), 10, 1 );		

		/** Enable shortcodes for element labels **/
		add_filter( 'woocommerce_tm_epo_option_name', array( $this, 'tm_epo_option_name' ), 10, 3);
		
		/** Hides uploaded file path **/
		add_filter( 'woocommerce_order_item_display_meta_value', array( $this, 'tm_order_item_display_meta_value' ), 10, 1);

		/** Support for fee price types **/
		add_action( 'woocommerce_cart_calculate_fees', array( $this,'tm_calculate_cart_fee' ) );

		/** Adds options to the array of items/products of an order **/
		add_filter( 'woocommerce_order_get_items', array( $this,'tm_woocommerce_order_get_items'), 10, 2 );

		/** Cart advanced template system **/
		// Override templates
		if(apply_filters( 'tm_get_template',true)){
			add_filter( 'wc_get_template', array( $this,'tm_wc_get_template'), 10, 5 );
		}
		// Custom actions running for advanced template system
		add_action( 'tm_woocommerce_cart_after_row', array( $this,'tm_woocommerce_cart_after_row' ),10,4 );
		add_action( 'tm_woocommerce_checkout_after_row', array( $this,'tm_woocommerce_checkout_after_row' ),10,4 );

		/** Display fields on admin Order **/
		// Display options on admin Order page
		add_action( 'woocommerce_order_item_' . 'line_item' . '_html', array( $this,'tm_woocommerce_order_item_line_item_html'), 10, 2);
		// Update option date on the order upon saving the order on admin Order page 
		add_action( 'woocommerce_saved_order_items', array( $this,'tm_woocommerce_saved_order_items'), 10, 2);

		/* Edit cart item */
		// Alters add to cart text when editing a product
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'tm_woocommerce_before_add_to_cart_button' ) );
		// Adds edit link on product title in cart 
		add_filter( 'woocommerce_cart_item_name', array( $this, 'tm_woocommerce_cart_item_name' ), 50, 3 );
		// Alters the cart item key when editing a product
		add_action( 'woocommerce_add_to_cart', array( $this,'tm_woocommerce_add_to_cart'), 10, 6);
		// Redirect to cart when updating information for a cart item
		add_filter( 'woocommerce_add_to_cart_redirect', array( $this,'tm_woocommerce_add_to_cart_redirect'), 9999, 1 );
		// Remove product from cart when editing a product
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'tm_remove_previous_product_from_cart' ), 99999, 6 );
		// Change quantity value when editing a cart item
		add_filter( 'woocommerce_quantity_input_args', array( $this,'tm_woocommerce_quantity_input_args'), 9999, 2 );

		/** Add custom class to product div used to initialize the plugin JavaScript **/
		add_filter( 'post_class', array( $this,'tm_post_class') );
		add_filter( 'woocommerce_related_products_args', array( $this,'tm_woocommerce_related_products_args'),10,1 );
		add_action( 'woocommerce_before_single_product', array( $this,'tm_enable_post_class'), 1);		
		add_action( 'woocommerce_after_single_product', array( $this,'tm_enable_post_class'), 1);		
		add_action( 'woocommerce_upsells_orderby', array( $this,'tm_woocommerce_related_products_args'), 10,1);
		
		/** Image filter **/
		add_filter( 'tm_image_url',array( $this,'tm_image_url') );

		/** Alter product display price to include possible option pricing **/
		if ($this->tm_epo_include_possible_option_pricing=="yes"){		
			add_filter( 'woocommerce_get_price', array( $this,'tm_woocommerce_get_price'), 1, 2 );
		}
		if ($this->tm_epo_use_from_on_price=="yes"){			
			add_filter( 'woocommerce_show_variation_price', array( $this,'tm_woocommerce_show_variation_price'), 50, 3 );
		}
		//add_filter( 'woocommerce_free_price_html', array( $this,'tm_woocommerce_free_price_html'), 49, 2 );

		//add_filter( 'woocommerce_product_weight', array( $this,'tm_woocommerce_product_weight'), 50, 2 );
		//add_filter( 'woocommerce_cart_contents_weight', array( $this,'tm_woocommerce_cart_contents_weight'), 50, 1 );
		
	}

	/*public function tm_woocommerce_cart_contents_weight($weight=''){
		$weight = 0;

			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				foreach ($values['tmcartepo'] as $keyepo => $epo) {
					if($epo['name']=='sdfsdfsdfsdf'){
						$weight +=10* $values['quantity'];
					}
				}
				$weight += ($values['data']->get_weight() * $values['quantity']);
			}

			return $weight;
	}
	public function tm_woocommerce_product_weight($weight='', $product=false){
		if ($product){
			//$weight++;
		}

		return $weight;
	}*/

	/*public function tm_woocommerce_calculate_totals($obj){//var_dump("expression");
		if (!$obj){
			return;
		}

		$price = 100;

		$cart = $obj->get_cart();

		foreach ( $cart as $cart_item_key => $values ) {var_dump($values['tmcartepo']);die();
			
			if ($values['quantity']<=0){
				continue;
			}

			$_product = $values['data'];

			// Store costs + taxes for lines
			$line_total 		= $obj->cart_contents[ $cart_item_key ]['line_total'];
			$line_tax 			= $obj->cart_contents[ $cart_item_key ]['line_tax'];
			$line_subtotal 		= $obj->cart_contents[ $cart_item_key ]['line_subtotal'];
			$line_subtotal_tax 	= $obj->cart_contents[ $cart_item_key ]['line_subtotal_tax'];

			// Store rates ID and costs - Since 2.2
			$line_tax_data 		= $obj->cart_contents[ $cart_item_key ]['line_tax_data'];//array( 'total' => $discounted_taxes, 'subtotal' => $taxes );


			$obj->cart_contents[ $cart_item_key ]['line_total'] += $price;
			$obj->cart_contents[ $cart_item_key ]['line_subtotal'] += $price;

			$obj->subtotal        += $price;//$line_subtotal + $line_subtotal_tax;
			$obj->subtotal_ex_tax += $price;//$line_subtotal;

			$obj->cart_contents_total += $price;

			$_product->adjust_price($price/$values['quantity']);
		}

	}*/

	/** Cart edit key **/
    public function cart_loaded_from_session_1(){
        $cart_contents = WC()->cart->cart_contents;

        if (is_array($cart_contents)){
            foreach ($cart_contents as $cart_item_key => $cart_item) {
                WC()->cart->cart_contents[$cart_item_key][TM_EPO()->cart_edit_key_var] = $cart_item_key;
            }
        }

    }

	/** Calculate totals on remove from cart/update **/
    public function tm_woocommerce_update_cart_action_cart_updated($cart_updated=false){

        $cart_contents = WC()->cart->cart_contents;
        if (is_array($cart_contents)){
            foreach ($cart_contents as $cart_item_key => $cart_item) {
                if (isset($cart_item['tm_epo_options_prices'])){
                    $cart_updated = true;
                }
            }
        }
        return $cart_updated;
    }

	/** Initialize custom product settings **/
	public function init_settings_pre(){
		$url = 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$postid = TM_EPO_HELPER()->get_url_to_postid($url);
		$this->postid_pre=$postid;
		$product=wc_get_product($postid);
		if (($product 
			&& is_object($product) 
			&& property_exists($product,'post') 
			&& property_exists($product->post,'post_type') 
			&& (in_array( $product->post->post_type, array( 'product', 'product_variation' ) ) )) || $postid==0){
			add_action( 'template_redirect', array( $this, 'init_settings' ) );
		}else{
			$this->init_settings();
		}
	}

	/** Initialize custom product settings **/
	public function init_settings(){
		
		if (is_admin() && !$this->is_quick_view()){
			return;
		}
		$this->wc_vars=array(
			"is_product" 			=> is_product(),
			"is_shop" 				=> is_shop(),
			"is_product_category" 	=> is_product_category(),
			"is_product_tag" 		=> is_product_tag(),
			"is_cart"				=> is_cart(),
			"is_checkout" 			=> is_checkout(),
			"is_account_page" 		=> is_account_page(),
			"is_ajax" 				=> is_ajax(),
		);
		// Re populate options for WPML
		if (TM_EPO_WPML()->is_active()){
			//todo:Find another place to re init settings for WPML
			$this->get_plugin_settings();
		}
		
		if (class_exists('WOOCS')){
			global $WOOCS;
			remove_filter('woocommerce_order_amount_total', array( $WOOCS, 'woocommerce_order_amount_total' ), 999);
		}

		$postMax = ini_get('post_max_size');
		
		// post_max_size debug
		if(empty($_FILES) 
			&& empty($_POST) 
			&& isset($_SERVER['REQUEST_METHOD']) 
			&& strtolower($_SERVER['REQUEST_METHOD']) == 'post'
			&& isset($_SERVER['CONTENT_LENGTH'])
			&& (float)$_SERVER['CONTENT_LENGTH']>$postMax
			){

			wc_add_notice( sprintf( __( 'Trying to upload files larger than %s is not allowed!', 'woocommerce-tm-extra-product-options' ), $postMax ) , 'error' );
		
		}

		global $post,$product;
		$this->set_tm_meta();
		$this->init_settings_after();		

	}

	/** Initialize custom product settings **/
	public function init_settings_after(){
		global $post,$product;
		// Check if the plugin is active for the user 
		if ($this->check_enable()){
			if (( $this->is_enabled_shortcodes() || is_product() || $this->is_quick_view() ) 
				&& ($this->tm_epo_display=='normal' || $this->tm_meta_cpf['override_display']=='normal') 
				&& $this->tm_meta_cpf['override_display']!='action'){
				$this->noactiondisplay=true;
				// Add options to the page
				add_action( $this->tm_epo_options_placement, array( $this, 'tm_epo_fields' ), 50 );
				add_action( $this->tm_epo_options_placement, array( $this, 'tm_add_inline_style' ), 99999 );
				add_action( $this->tm_epo_totals_box_placement, array( $this, 'tm_epo_totals' ), 50 );
			}
		}

		if ($this->tm_epo_enable_in_shop=="yes" && ( is_shop() || is_product_category() || is_product_tag() ) ){
			add_action( 'woocommerce_after_shop_loop_item',array( $this,'tm_woocommerce_after_shop_loop_item'), 9 );	
		}

		if ($this->tm_epo_remove_free_price_label=='yes'){
			if ($post || $this->postid_pre){

				if ($post){
					$thiscpf=$this->get_product_tm_epos($post->ID);
				}
				
				if (is_product() && is_array($thiscpf) && (!empty($thiscpf['global']) || !empty($thiscpf['local']))) {
					if ($product && 
						(is_object($product) && !method_exists($product, "get_price")) ||
						(!is_object($product) ) 
						){
						$product=wc_get_product($post->ID);
					}
					if ($product && 
						is_object($product) && method_exists($product, "get_price") 
						){

						if (!(float)$product->get_price()>0){
							if ($this->tm_epo_replacement_free_price_text){
								add_filter('woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2);
							}else{
								if ($this->tm_epo_use_from_on_price=="no")
								remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' ,10);					
							}							
						}
						
						add_filter('woocommerce_get_price_html', array( $this, 'related_get_price_html' ), 10, 2);
						
					}
				}else{
					if ( is_shop() || is_product_category() || is_product_tag() ){
						add_filter('woocommerce_get_price_html', array( $this, 'get_price_html_shop' ), 10, 2);	
					}elseif( !is_product() && $this->is_enabled_shortcodes() ){//&& $this->postid_pre removed to allow all pages
						if ($this->tm_epo_replacement_free_price_text){
							add_filter('woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2);
						}else{
							if ($this->tm_epo_use_from_on_price=="no")
							remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' ,10);
							add_filter('woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2);							
						}
					}elseif(is_product()){
						add_filter('woocommerce_get_price_html', array( $this, 'related_get_price_html2' ), 10, 2);
					}
				}
			}else{
				if ($this->is_quick_view()){
					if ($this->tm_epo_replacement_free_price_text){
						add_filter('woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2);
					}else{
						add_filter('woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2);
						if ($this->tm_epo_use_from_on_price=="no")
						remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' ,10);
					}
				}
			}
		}
	}

	/** Checks if the for extra options presence **/
	public function has_options($product_id=0){

		$post_id=get_the_ID();

		if ($product_id && $product_id!==$post_id){
			$post_id = $product_id;
		}

		$cpf_price_array=TM_EPO()->get_product_tm_epos($post_id);

		if ($cpf_price_array){
			$global_price_array = $cpf_price_array['global'];
			$local_price_array  = $cpf_price_array['local'];
			if ( empty($global_price_array) && empty($local_price_array) ){
				return false;
			}

			$has_epo = array();
			if ( empty($global_price_array) ){
				$has_epo['global'] = true;
			}
			if ( empty($global_price_array) ){
				$has_epo['local'] = true;
			}
			$found=false;
			foreach ( $global_price_array as $priority=>$priorities ) {
				foreach ( $priorities as $pid=>$field ) {
					if (isset($field['sections']) && is_array($field['sections'])){
						foreach ( $field['sections'] as $section_id=>$section ) {
							if ( isset( $section['elements'] ) ) {
								foreach ( $section['elements'] as $elid=>$el ) {
									if ( isset( $el['builder'] ) ) {
										if ( isset( $el['builder']['element_type'] ) ) {
											foreach ( $el['builder']['element_type'] as $bid=>$b ) {
												if (strtolower($b)=="variations"){
													$found=true;
													break 5;
												}
											}
										}
									}									
								}								
							}
						}
					}
				}
			}
			if($found){
				$has_epo['variations'] = true; 				
			}

			return $has_epo;
		}

		return false;

	}

	/** Add custom inline css **/
	public function tm_variation_css_check($echo=0,$product_id=0){
		if(is_rtl()){
			$this->float_direction="right";
			$this->float_direction_opposite="left";
		}

		$post_id=get_the_ID();

		if ($product_id && $product_id!==$post_id){
			$post_id = $product_id;
		}
		$cpf_price_array=TM_EPO()->get_product_tm_epos($post_id);
		if ($cpf_price_array){
			$global_price_array = $cpf_price_array['global'];
			$local_price_array  = $cpf_price_array['local'];
			if ( empty($global_price_array)  ){
				return;
			}
			$found=false;
			foreach ( $global_price_array as $priority=>$priorities ) {
				foreach ( $priorities as $pid=>$field ) {
					if (isset($field['sections']) && is_array($field['sections'])){
						foreach ( $field['sections'] as $section_id=>$section ) {
							if ( isset( $section['elements'] ) ) {
								foreach ( $section['elements'] as $elid=>$el ) {
									if ( isset( $el['builder'] ) ) {
										if ( isset( $el['builder']['element_type'] ) ) {
											foreach ( $el['builder']['element_type'] as $bid=>$b ) {
												if (strtolower($b)=="variations"){
													$found=true;
													break 5;
												}
											}
										}
									}									
								}								
							}
						}
					}
				}
			}
			if($found){
				if ($product_id){
					$css_string="#product-".$product_id." form .variations,.post-".$product_id." form .variations {display:none;}";
				}else{
					$css_string="form .variations{display:none;}";	
				}
				
				$this->inline_styles_head=$this->inline_styles_head.$css_string;
				if ($echo){
					$this->tm_variation_css_check_do();
				}else{
					add_action( 'wp_head', array( $this,'tm_variation_css_check_do') );				
				}				
			}
		}
	}

	/** Print inline css **/
	public function tm_variation_css_check_do(){
		if (!empty($this->inline_styles_head)){
			echo '<style type="text/css">';
			echo $this->inline_styles_head;
			echo '</style>';
		}
	}

	/** Adds additional builder elements from 3rd party plugins. **/
	public final function tm_epo_add_elements(){

		do_action('tm_epo_register_addons');
		do_action('tm_epo_register_extra_multiple_choices');

		$this->tm_original_builder_elements=TM_EPO_BUILDER()->get_elements();

		if (is_array($this->tm_original_builder_elements)){
			foreach ($this->tm_original_builder_elements as $key => $value) {
				
				if ($value["is_post"]=="post"){
					$this->element_post_types[] = $value["post_name_prefix"];
				}

				if ($value["is_post"]=="post" || $value["is_post"]=="display"){
					$this->tm_builder_elements[$value["post_name_prefix"]] = $value;
				}
				
			}
		}

	}

	/** Load js,css files **/
	public function frontend_scripts() {
		global $product;
		if ( 
			( (class_exists( 'WC_Quick_View' ) || $this->is_supported_quick_view()) && ( is_shop() || is_product_category() || is_product_tag() ) ) 
			|| $this->is_enabled_shortcodes() 
			|| is_product() 
			|| is_cart() 
			|| is_checkout() 
			|| is_order_received_page() 
			|| ($this->tm_epo_enable_in_shop=="yes" && ( is_shop() || is_product_category() || is_product_tag() ) )
			) {

			do_action('wc_epo_enqueue_scripts_before');
			
			$this->custom_frontend_scripts();
			
			do_action('wc_epo_enqueue_scripts_after');

		}else{
			return;
		}		
	}
	
	/** Custom optional dequeue_scripts **/
	public function dequeue_scripts() {
		if ( 
			( (class_exists( 'WC_Quick_View' ) || $this->is_supported_quick_view()) && ( is_shop() || is_product_category() || is_product_tag() ) ) 
			|| $this->is_enabled_shortcodes() 
			|| is_product() 
			|| is_cart() 
			|| is_checkout() 
			|| is_order_received_page() 
			|| ($this->tm_epo_enable_in_shop=="yes" && ( is_shop() || is_product_category() || is_product_tag() ) )
			) {
			
			do_action('wc_epo_dequeue_scripts');
		}
		
	}

	/** Displays options in shop page **/
	public function tm_woocommerce_after_shop_loop_item(){
		$post_id = get_the_ID();
		$cpf_price_array=TM_EPO()->get_product_tm_epos($post_id);
		if ($cpf_price_array){
			echo '<div class="tm-has-options"><form class="cart">';
			$this->frontend_display($post_id,"tc_".$post_id,true); 
			echo '</form></div>';
		}
	}

	/** Alter product display price to include possible option pricing **/
	public function tm_woocommerce_get_price($price=0,$product=false){
		 
		 if ($product){
		 		
		 	$epos = $this->get_product_tm_epos($product->id);
								
			if (is_array($epos) && (!empty($epos['global']) || !empty($epos['local']))) {
				if (!empty($epos['price'])){

					$minmax = TM_EPO_HELPER()->sum_array_values($epos['price']);
					if (!isset($minmax['min'])){
						$minmax['min'] = 0;
					}
					if (!isset($minmax['max'])){
						$minmax['max'] = 0;
					}
					$min = $minmax['min'];
					$max = $minmax['max'];
					$product->tc_min_price = $min;
					$product->tc_max_price = $max;
					
					$product->tc_new_price = floatval($price)+$minmax['min'];
					add_filter( 'woocommerce_get_variation_price_html',array( $this,'tm_get_price_html'), 1, 2 );
					add_filter( 'woocommerce_get_price_html',array( $this,'tm_get_price_html'), 1, 2 );

					if ($price ===''){
						$price = 0;
						if (!empty($max)){							
							if (method_exists($product, 'get_composite_price')){
								$price=$product->get_composite_price('min');
							}
							$product->tc_new_price = $max;
						}
					}

				}					
			}
		 	
		 }

		 return $price;

	}

	/** Alter product display price to include possible option pricing **/
	public function tm_woocommerce_show_variation_price($show=true,$product=false,$variation=false){
		
		if ($product && $variation){
			$epos = $this->get_product_tm_epos($product->id);
			if (is_array($epos) && (!empty($epos['global']) || !empty($epos['local']))) {
				if (!empty($epos['price'])){
					$minmax = TM_EPO_HELPER()->sum_array_values($epos['price']);
					if (!empty($minmax['max'])){
						$show = true;
					}
				}					
			}
		}

		return $show;
	
	}

	/** Returns the product's active price **/
	public function tc_get_price( $product=false ) {

		return apply_filters('woocommerce_get_price', $product->price + $product->tc_min_price, $product );

	}

	/** Returns the price including or excluding tax, based on the 'woocommerce_tax_display_shop' setting. **/
	public function tc_get_display_price( $product=false, $price = '', $qty = 1 ) {

		if ( $price === '' ) {
			$price = $this->tc_get_price($product);
		}

		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$display_price    = $tax_display_mode == 'incl' ? $product->get_price_including_tax( $qty, $price ) : $product->get_price_excluding_tax( $qty, $price );

		return $display_price;
	}

	/** Returns the product's regular price. **/
	public function tc_get_regular_price($product=false) {
		return apply_filters( 'woocommerce_get_regular_price', $product->regular_price+ $product->tc_min_price, $product);
	}

	/** Alter product display price to include possible option pricing **/
	public function tm_get_price_html($price='',$product=false){
		$type = $product->get_type();
		$min = apply_filters( 'wc_epo_options_min_price', $product->tc_min_price, $product, $price );
		$max = apply_filters( 'wc_epo_options_max_price', $product->tc_max_price, $product, $price );
		$use_from = ($this->tm_epo_use_from_on_price=="yes");
		$free_text = ($this->tm_epo_remove_free_price_label=='yes')
						? 
							($this->tm_epo_replacement_free_price_text)
							? $this->tm_epo_replacement_free_price_text
							: ''
						: __( 'Free!', 'woocommerce' );
		if ($type == 'variable' || $type == 'variable-subscription'){
			$prices = $product->get_variation_prices( true );

			$min_price = current( $prices['price'] )+$min;
			$max_price = end( $prices['price'] )+$max;
			$price     = $min_price !== $max_price 
				? !$use_from 
					? sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), wc_price( $min_price ), wc_price( $max_price ) ) 
					: $product->get_price_html_from_text() . wc_price( $min_price )
				: wc_price( $min_price );
			$is_free   = $min_price == 0 && $max_price == 0;

			if ( $product->is_on_sale() ) {
				$min_regular_price = current( $prices['regular_price'] )+$min;
				$max_regular_price = end( $prices['regular_price'] )+$max;
				$regular_price     = $min_regular_price !== $max_regular_price 
					? !$use_from
						? sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), wc_price( $min_regular_price ), wc_price( $max_regular_price ) ) 
						: $product->get_price_html_from_text() . wc_price( $min_regular_price )
					: wc_price( $min_regular_price );
				$price             = (!$use_from?$product->get_price_html_from_to( $regular_price, $price ):$price) . $product->get_price_suffix();
			} elseif ( $is_free ) {
				$price = apply_filters( 'woocommerce_variable_free_price_html', $free_text, $product );
			} else {
				$price = $price . $product->get_price_suffix();
			}
		}	
		else{
			$display_price         = $this->tc_get_display_price($product);
			$display_regular_price = $this->tc_get_display_price( $product, $this->tc_get_regular_price($product) );
			$price='';
			if ( $this->tc_get_price($product) > 0 ) {
				
				if ( $product->is_on_sale() && $this->tc_get_regular_price($product) ) {
					if ($use_from && ($max>0 || $max>$min)){
						$price .= $product->get_price_html_from_text().wc_price( $display_price );
					}else{
						$price .= $product->get_price_html_from_to( $display_regular_price, $display_price );
					}
					$price .=  $product->get_price_suffix();
					
				} else {
					if ($use_from && ($max>0 || $max>$min)){
						$price .= $product->get_price_html_from_text();
					}
					$price .= wc_price( $display_price ) . $product->get_price_suffix();

				}
			} elseif ( $this->tc_get_price($product) === '' ) {

				$price = apply_filters( 'woocommerce_empty_price_html', '', $product );

			} elseif ( $this->tc_get_price($product) == 0 ) {
				if ( $product->is_on_sale() && $this->tc_get_regular_price($product) ) {
					if ($use_from && ($max>0 || $max>$min)){
						$price .= $product->get_price_html_from_text().wc_price( 0 );
					}else{
					
						$price .= $product->get_price_html_from_to( $display_regular_price, __( 'Free!', 'woocommerce' ) );

						$price = apply_filters( 'woocommerce_free_sale_price_html', $price, $product );
					}

				} else {
					if ($use_from && ($max>0 || $max>$min)){
						$price .= $product->get_price_html_from_text().wc_price( 0 );
					}else{
						
						$price = '<span class="amount">' . $free_text . '</span>';

						$price = apply_filters( 'woocommerce_free_price_html', $price, $product );
					}

				}
			}
		}
		return apply_filters( 'wc_epo_get_price_html', $price, $product );
	}

	/** Alter product display price to include possible option pricing **/
	public function tm_woocommerce_free_price_html($price='',$product=false){
		if ($product->tc_new_price!==''){
			if($product->tc_new_price == 0){
				$price = $this->get_price_html($product->tc_new_price,$product);
			}else{
				$price = $product->get_price_html_from_text().wc_price($product->tc_new_price);
			}
		}

		return $price;
	}

	/** Ensures correct price is shown on minicart **/
	public function tm_recalculate_total(){
		 WC()->cart->calculate_totals(); 
	}

	/** Image filter **/
	public function tm_image_url($url=""){
		// WP Rocket cdn
		if (defined('WP_ROCKET_VERSION') && function_exists('get_rocket_cdn_cnames') && function_exists('get_rocket_cdn_url')){
			$zone = array( 'all', 'images' );
			if (is_array($url)){
				foreach ($url as $key => $value) {
					$ext = pathinfo( $value, PATHINFO_EXTENSION );
					if ( is_admin() && $ext != 'php' ) {
						continue;
					}
					if ( $cnames = get_rocket_cdn_cnames( $zone ) ) {
						$url[$key] = get_rocket_cdn_url( $value, $zone );
					}
				}

			}else{
				$ext = pathinfo( $url, PATHINFO_EXTENSION );
				if ( is_admin() && $ext != 'php' ) {
					//skip
				}else{
					if ( $cnames = get_rocket_cdn_cnames( $zone ) ) {
						$url = get_rocket_cdn_url( $url, $zone );
					}
				}
			}			

		}
		// SSL support
		if (is_ssl()){
			$url = preg_replace("/^http:/i", "https:", $url);
		}
		return $url;
	}

	/** Add custom class to product div **/
	public function tm_enable_post_class(){
		$this->tm_related_products_output=true;
	}

	/** Add custom class to product div **/
	public function tm_disable_post_class(){
		$this->tm_related_products_output=false;
	}

	/** Add custom class to product div **/
	public function tm_woocommerce_related_products_args($args){
		$this->tm_disable_post_class();
		return $args;
	}

	/** Add custom class to product div used to initialize the plugin JavaScript **/
	public function tm_post_class($classes=""){

		if(is_admin() || !$this->tm_related_products_output){
			return $classes;
		}
		if (($this->wc_vars['is_shop'] || $this->wc_vars['is_product_category'] || $this->wc_vars['is_product_tag']) && $this->tm_epo_enable_in_shop=="no"){
			return $classes;
	  	}
		global $post;
		
		$post_id = get_the_ID();
		
		if ( $post && ('product' == get_post_type( $post_id ) || $this->wc_vars['is_product']) ) {

			$has_options = $this->get_product_tm_epos($post_id);

			// Product has extra options
			if (is_array($has_options) && (!empty($has_options['global']) || !empty($has_options['local']))) {
				
				$classes[] = 'tm-has-options';
			
			// Product doens't have extra options but the final total box is enabled for all products
			}elseif($this->tm_epo_enable_final_total_box_all=="yes"){
				
				$classes[] = 'tm-no-options-pxq';
			
			// Search for composite products extra options
			}else{
				
				$terms 			= get_the_terms( $post_id, 'product_type' );
				$product_type 	= ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';
				
				if ( ($product_type == 'bto' || $product_type == 'composite') 
					&& !(is_array($has_options) && (!empty($has_options['global']) || !empty($has_options['local'])))
					&&  $this->tm_epo_enable_final_total_box_all!="yes"
					) {
					
					// search components for options
					$product=wc_get_product($post_id);
					if (method_exists($product, 'get_composite_data')){
						$composite_data = $product->get_composite_data();
					
						foreach ( $composite_data as $component_id => $component_data ) {

							$component_options = array();

							if (function_exists('WC_CP')){
								$component_options = WC_CP()->api->get_component_options( $component_data );
							}else{
								global $woocommerce_composite_products;
								if (is_object($woocommerce_composite_products) && function_exists('WC_CP')){
									$component_options = WC_CP()->api->get_component_options( $component_data );
								}else{
									if (isset( $component_data['assigned_ids'] ) && is_array($component_data['assigned_ids'])){
										$component_options = $component_data['assigned_ids'];
									}
								}
							}
							
							foreach ($component_options as $key => $pid) {
								$has_options = $this->get_product_tm_epos($pid);
								if (is_array($has_options) && (!empty($has_options['global']) || !empty($has_options['local']))) {
									$classes[] = 'tm-no-options-composite';
									return $classes;
								}
							}

						}
					}

				}

				$classes[] = 'tm-no-options';
			}
		}
		return $classes;
	}

	/** Alters add to cart text when editing a product **/
	public function tm_woocommerce_before_add_to_cart_button(){
		if ( !empty( $this->cart_edit_key) && isset($_GET['_wpnonce']) && wp_verify_nonce( $_GET['_wpnonce'], 'tm-edit' ) ){
			add_filter('woocommerce_product_single_add_to_cart_text',array($this,'tm_woocommerce_product_single_add_to_cart_text'),9999);
			echo '<input type="hidden" name="'.$this->cart_edit_key_var_alt.'" value="'.esc_attr( $this->cart_edit_key ).'" />';
		}

		$this->epo_id++;
		echo '<input type="hidden" class="tm-epo-counter" name="tm-epo-counter" value="'.esc_attr( $this->epo_id ).'" />';

		global $product;
		if ($product && is_object($product) && property_exists($product, "id") ){
			echo '<input type="hidden" class="tc-add-to-cart" name="tcaddtocart" value="'.esc_attr( $product->id ).'" />';
		}		
	}
	
	/** Adds edit link on product title in cart **/
	public function tm_woocommerce_cart_item_name($title="", $cart_item=array(), $cart_item_key="" ){
		if ( apply_filters('wc_epo_no_edit_options',false) ){
			return $title;
		}	
		if ( !(is_cart() || is_checkout()) || !isset($cart_item['data']) || !isset($cart_item['tmhasepo']) || isset($cart_item['composite_item']) || isset($cart_item['composite_data']) ){
			return $title;
		}
		// Chained prodcuts cannot be edited
		if ( class_exists('Chained_Products_WC_Compatibility') && isset ( Chained_Products_WC_Compatibility::global_wc()->cart->cart_contents[ $cart_item_key ]['chained_item_of'] ) ){
			return $title;	
		}
		$product=$cart_item['data'];

		$link=$product->get_permalink( $cart_item );
		$link = add_query_arg( 
			array(
				$this->cart_edit_key_var => $cart_item_key,
				'cart_item_key' => $cart_item_key,
				)
			, $link );
		//wp_nonce_url escapes the url
		$link=wp_nonce_url($link,'tm-edit');
		$title .='<a href="'.$link.'" class="tm-cart-edit-options">'.((!empty($this->tm_epo_edit_options_text))?$this->tm_epo_edit_options_text:__( 'Edit options', 'woocommerce-tm-extra-product-options' )).'</a>';
		return apply_filters('wc_epo_edit_options_link',$title);
	}

	/** Alters the cart item key when editing a product **/
	public function tm_woocommerce_add_to_cart( $cart_item_key="", $product_id="", $quantity="", $variation_id="", $variation="", $cart_item_data="" ){
		if($this->cart_edit_key){
			$this->new_add_to_cart_key = $cart_item_key;
		}else{
			if(is_array($cart_item_data) && isset($cart_item_data['tmhasepo'])){
				
				$cart_contents = WC()->cart->cart_contents;
				
				if (is_array($cart_contents) && isset($cart_contents[$cart_item_key]) && !isset($cart_contents[$cart_item_key][$this->cart_edit_key_var])){
					WC()->cart->cart_contents[$cart_item_key][$this->cart_edit_key_var] = $cart_item_key;
				}

			}
		}
	}

	/** Redirect to cart when updating information for a cart item **/
	public function tm_woocommerce_add_to_cart_redirect($url=""){
		if ( empty( $_REQUEST['add-to-cart'] ) || ! is_numeric( $_REQUEST['add-to-cart'] ) ) {
			return $url;
		}
		if($this->cart_edit_key){
			if (!TM_EPO_HELPER()->is_ajax_request()){
				$url = WC()->cart->get_cart_url();	
			}			
		}
		return $url;
	}
	
	/** Remove product from cart when editing a product **/
	public function tm_remove_previous_product_from_cart( $passed, $product_id, $qty, $variation_id = '', $variations = array(), $cart_item_data = array() ) {
		if($this->cart_edit_key){
			$cart_item_key = $this->cart_edit_key;
			if (isset($this->new_add_to_cart_key)){
				if ($this->new_add_to_cart_key == $cart_item_key && isset($_POST['quantity'])){
					WC()->cart->set_quantity( $this->new_add_to_cart_key, $_POST['quantity'], true );
				}else{
					WC()->cart->remove_cart_item( $cart_item_key );
					unset(WC()->cart->removed_cart_contents[ $cart_item_key ]);
				}
			}		
		}
		return $passed;
	}

	/** Change quantity value when editing a cart item **/
	public function tm_woocommerce_quantity_input_args($args="", $product=""){
		if($this->cart_edit_key){
			$cart_item_key = $this->cart_edit_key;
			$cart_item = WC()->cart->get_cart_item( $cart_item_key );

			if (isset($cart_item["quantity"])){
				$args["input_value"] = $cart_item["quantity"];
			}
		}
		
		return $args;
	}

	/** Adds options to the array of items/products of an order **/
	public function tm_woocommerce_order_get_items($items=array(), $order=false){
		if ( !is_array($items) || defined('TM_IS_SUBSCRIPTIONS_RENEWAL') || ($this->tm_epo_global_prevent_options_from_emails=="yes") ){
			return $items;
		}

		$order_currency = $order->get_order_currency();
		$mt_prefix = $order_currency;
		
		foreach ( $items as $item_id => $item ){
			$has_epo = isset($item['item_meta']) && isset($item['item_meta']['_tmcartepo_data']) && isset($item['item_meta']['_tmcartepo_data'][0]);
			
			if ($has_epo){
				$epos = maybe_unserialize($item['item_meta']['_tmcartepo_data'][0]);
				if (!is_array($epos)){
					return $items;
				}
				$current_product_id=$item['product_id'];
				$original_product_id = floatval(TM_EPO_WPML()->get_original_id( $current_product_id,'product' ));
				if (TM_EPO_WPML()->get_lang()==TM_EPO_WPML()->get_default_lang() && $original_product_id!=$current_product_id){
					$current_product_id = $original_product_id;
				}
				$wpml_translation_by_id=TM_EPO_WPML()->get_wpml_translation_by_id( $current_product_id );
				$_unique_elements_added=array();
				$_items_to_add=array();
				foreach ($epos as $key => $epo) {
					if ($epo && is_array($epo) && isset($epo['section'])){
						if(!isset($epo['quantity'])){
							$epo['quantity'] = 1;
						}
						if ($epo['quantity']<1){
							$epo['quantity'] = 1;
						}
						if(isset($wpml_translation_by_id[$epo['section']])){
							$epo['name'] = $wpml_translation_by_id[$epo['section']];
						}
						if(!empty($epo['multiple']) && !empty($epo['key'])){
							$pos = strrpos($epo['key'], '_');
							if($pos!==false) {
								if (isset($wpml_translation_by_id["options_".$epo['section']]) && is_array($wpml_translation_by_id["options_".$epo['section']])){
									$av=array_values( $wpml_translation_by_id["options_".$epo['section']] );
									if (isset($av[substr($epo['key'], $pos+1)])){
										$epo['value'] = $av[substr($epo['key'], $pos+1)];
									}
								}
							}
						}
						$epo['value'] = $this->tm_order_item_display_meta_value($epo['value']);

						$new_currency = false;
						if (isset($epo['price_per_currency'])){
							$_current_currency_prices = $epo['price_per_currency'];
							if ($mt_prefix!=='' 
									&& $_current_currency_prices!=='' 
									&& is_array($_current_currency_prices) 
									&& isset($_current_currency_prices[$mt_prefix])
									&& $_current_currency_prices[$mt_prefix]!=''){

								$new_currency = true;
								$epo['price'] = $_current_currency_prices[$mt_prefix];
										
							}
						}
						if(!$new_currency){
							$epo['price'] = apply_filters('wc_epo_get_current_currency_price',$epo['price']);
						}

						if (!empty($epo['multiple_values'])){
							$display_value_array=explode($epo['multiple_values'],$epo['value']);
							$display_value="";
							foreach ($display_value_array as $d => $dv) {
								$display_value .='<span class="cpf-data-on-cart">'.$dv.'</span>';
							}
							$epo['value'] = $display_value;
						}

						$epovalue = '';
						if ($this->tm_epo_hide_options_prices_in_cart=="normal" && !empty($epo['price'])){
							$epovalue .= ' '.(( !empty( $item['item_meta']['tm_has_dpd'] ) || !empty( $item['item_meta']['_tm_has_dpd'] ) )?'':(wc_price(  (float) $epo['price']/(float) $epo['quantity']  )));
						}
						if ($epo['quantity']>1){
							$epovalue .= ' &times; '. $epo['quantity'];	
						}
						if ($epovalue!==''){
							$epo['value'] .= '<small>'.$epovalue.'</small>';
						}

						if (is_array($epo['value'])){
							$epo['value'] = array_map(array(TM_EPO_HELPER(), 'html_entity_decode'), $epo['value']);

						}else{
							$epo['value']=TM_EPO_HELPER()->html_entity_decode($epo['value']);
						}
						
						if ($this->tm_epo_strip_html_from_emails=="yes"){
							$epo['value']=strip_tags($epo['value']);
						}else{
							if (!empty($epo['images']) && $this->tm_epo_show_image_replacement=="yes"){
								$display_value ='<span class="cpf-img-on-cart"><img alt="" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="'.
												apply_filters("tm_image_url",$epo['images']).'" /></span>';
								$epo['value']=$display_value.$epo['value'];
							}
							
							if (isset($epo['element']) && isset($epo['element']['type']) && $epo['element']['type'] == "upload"){
								$check = wp_check_filetype( $epo['value'] );
								if ( !empty( $check['ext'] ) ) {
		                			$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );
		                			if ( in_array( $check['ext'], $image_exts ) ){
		                				$display_value ='<span class="cpf-img-on-cart"><img alt="" class="attachment-shop_thumbnail wp-post-image epo-option-image epo-upload-image" src="'.
											apply_filters("tm_image_url",$epo['value']).'" /><span>';
										$epo['value']=$display_value;
		                			}
		        				}
							}


						}
						if (empty($epo['hidelabelinorder']) || empty($epo['hidevalueinorder'])){
							if (!empty($epo['hidelabelinorder'])){
								$epo['name']="";
							}
							if (!empty($epo['hidevalueinorder'])){
								$epo['value']="";
							}
							if (isset($_unique_elements_added[$epo['section']]) && isset($_items_to_add[$epo['section']])){
								$_ta=$_items_to_add[$epo['section']];
								$_ta[$epo['name']][]=$epo['value'];
								$_items_to_add[$epo['section']]=$_ta;
							}else{
								$_ta=array();							
								$_ta[$epo['name']]=array($epo['value']);
								$_items_to_add[$epo['section']]=$_ta;
							}
							$_unique_elements_added[$epo['section']]=$epo['section'];							
						}
					}
				}
				
				foreach ($_items_to_add as $uniquid => $element) {
					foreach ($element as $key => $value) {
						if (is_array($value)){
							$value=implode(",",$value);
						}
						$items[$item_id]['item_meta'][$key][] = $value;
						$items[$item_id]['item_meta_array'][count($items[$item_id]['item_meta_array'])] = (object) array( 'key' => $key, 'value' => $value );
					}
				}
			}
		}
		return $items;
	}

	/** Update option date on the order upon saving the order on admin Order page **/
	public function tm_woocommerce_saved_order_items($order_id=0, $items=array() ){

		if (is_array($items) && isset($items['tm_epo'])){
			$order = TM_EPO_HELPER()->tm_get_order_object();
			$order_currency = $order->get_order_currency();
			$mt_prefix = $order_currency;

			foreach ($items['tm_epo'] as $item_id => $epos) {
				$item_meta = $order->get_item_meta( $item_id );
				$qty=(float) $item_meta['_qty'][0];
				$line_total = floatval($item_meta['_line_total'][0]);
				$line_subtotal = isset($item_meta['_line_subtotal'])?floatval($item_meta['_line_subtotal'][0]):$line_total;
				$has_epo = is_array($item_meta)
					&& isset($item_meta['_tmcartepo_data']) 
					&& isset($item_meta['_tmcartepo_data'][0])
					&& isset($item_meta['_tm_epo']);
		
				$has_fee = is_array($item_meta)
					&& isset($item_meta['_tmcartfee_data']) 
					&& isset($item_meta['_tmcartfee_data'][0]);

				$saved_epos = false;
				if ($has_epo || $has_fee){
					$saved_epos = maybe_unserialize($item_meta['_tmcartepo_data'][0]);
				}
				
				$do_update = false;

				if($saved_epos){
					foreach ($epos as $key => $epo) {
						if(isset($items['tm_item_id']) && isset($items['tm_key']) && $items['tm_key']==$key && $items['tm_item_id']==$item_id){
							$line_total = $line_total - $saved_epos[$key]['price'];
							$line_subtotal = $line_subtotal - $saved_epos[$key]['price'];
							unset($saved_epos[$key]);
							$do_update = true;
						}else{
							if(isset($epo['quantity'])){
								$line_total = $line_total - ($saved_epos[$key]['price']*$qty);
								$line_subtotal = $line_subtotal - ($saved_epos[$key]['price']*$qty);
								
								if ($saved_epos[$key]['quantity']>=0){
									//todo
								}else{
									$saved_epos[$key]['price'] = ($saved_epos[$key]['price']/$saved_epos[$key]['quantity']) * $epo['quantity'];	
								}								
								$saved_epos[$key]['quantity'] = $epo['quantity'];

								$line_total = $line_total + ($saved_epos[$key]['price']*$qty);
								$line_subtotal = $line_subtotal + ($saved_epos[$key]['price']*$qty);
								$do_update = true;
							}
							if(isset($epo['price'])){
								$new_currency = false;
								$_current_currency_prices = $saved_epos[$key]['price_per_currency'];
								if ($mt_prefix!=='' 
										&& $_current_currency_prices!=='' 
										&& is_array($_current_currency_prices) 
										&& isset($_current_currency_prices[$mt_prefix])
										&& $_current_currency_prices[$mt_prefix]!=''){

									$new_currency = true;
									//don't change price as it is currency custom
									
								}
								if(!$new_currency){
									$epo['price'] = apply_filters('wc_epo_remove_current_currency_price',$epo['price']);
								}

								$line_total = $line_total - ($saved_epos[$key]['price']*$qty);
								$line_subtotal = $line_subtotal - ($saved_epos[$key]['price']*$qty);

								$saved_epos[$key]['price'] = $epo['price'] * $saved_epos[$key]['quantity'];

								$line_total = $line_total + ($saved_epos[$key]['price']*$qty);
								$line_subtotal = $line_subtotal + ($saved_epos[$key]['price']*$qty);
								$do_update = true;
							}
							if(isset($epo['value'])){
								$saved_epos[$key]['value'] = $epo['value'];
								if(isset($saved_epos[$key]['multiple']) && isset($saved_epos[$key]['key'])){

									$current_product_id=isset($item_meta['_product_id'][0])?$item_meta['_product_id'][0]:NULL;
									$original_product_id = floatval(TM_EPO_WPML()->get_original_id( $current_product_id,'product' ));
									if (TM_EPO_WPML()->get_lang()==TM_EPO_WPML()->get_default_lang() && $original_product_id!=$current_product_id){
										$current_product_id = $original_product_id;
									}
									if ($current_product_id){
										$get_saved_order_multiple_keys=TM_EPO_HELPER()->get_saved_order_multiple_keys($current_product_id);
										if (isset($get_saved_order_multiple_keys["options_".$saved_epos[$key]['section']])){
											$new_key = array_search( $epo['value'], $get_saved_order_multiple_keys["options_".$saved_epos[$key]['section']] );
											if ($new_key){
												$saved_epos[$key]['key'] = $new_key;
											}else{
												$saved_epos[$key]['key'] = '';
											}
										}	
									}else{
										$saved_epos[$key]['key'] = '';
									}
								}
								$do_update = true;
							}
						}
					}
				}

				if ($do_update){
					
					wc_update_order_item_meta( $item_id, '_line_total', wc_format_decimal( $line_total ) );
					wc_update_order_item_meta( $item_id, '_line_subtotal', wc_format_decimal( $line_subtotal ) );

					wc_update_order_item_meta( $item_id, '_tmcartepo_data', $saved_epos );
					//if (isset($item_meta['tm_has_dpd'])){
					//	wc_update_order_item_meta( $item_id, 'tm_has_dpd', $saved_epos );
					//}

					wp_cache_delete( $item_id, 'order_item_meta' );
				}
			}
		}

	}

	/** Display options on admin Order page **/
	public function tm_woocommerce_order_item_line_item_html($item_id="", $item=array()){
		
		$order = TM_EPO_HELPER()->tm_get_order_object();

		$order_currency = $order->get_order_currency();
		$mt_prefix = $order_currency;

		$html = '';
		
		$check_box_html = ( version_compare( get_option( 'woocommerce_db_version' ), '2.6', '>=' ) )?'':'<td class="check-column">&nbsp;</td>';
		$has_epo = is_array($item)
				&& isset($item['item_meta']) 
				&& isset($item['item_meta']['_tmcartepo_data']) 
				&& isset($item['item_meta']['_tmcartepo_data'][0])
				&& isset($item['item_meta']['_tm_epo']);
		
		$has_fee = is_array($item)
				&& isset($item['item_meta']) 
				&& isset($item['item_meta']['_tmcartfee_data']) 
				&& isset($item['item_meta']['_tmcartfee_data'][0]);

		if ($has_epo || $has_fee){
			$current_product_id=$item['product_id'];
			$original_product_id = floatval(TM_EPO_WPML()->get_original_id( $current_product_id,'product' ));
			if (TM_EPO_WPML()->get_lang()==TM_EPO_WPML()->get_default_lang() && $original_product_id!=$current_product_id){
				$current_product_id = $original_product_id;
			}
			$wpml_translation_by_id=TM_EPO_WPML()->get_wpml_translation_by_id( $current_product_id );
		}

		if ($has_epo){
			$epos = maybe_unserialize($item['item_meta']['_tmcartepo_data'][0]);

			if ($epos && is_array($epos) ){
				$html_toadd = '<tr class="tm-order-line item '.apply_filters( 'woocommerce_admin_html_order_item_class', ( ! empty( $class ) ? $class : '' ), $item ) .'">'
						.$check_box_html
						.'<td class="thumb">&nbsp;</td>';
				$html_toadd .= '<td class="tm-c name"><div class="tm-view tm-order-header">'.__('Extra Product Options','woocommerce-tm-extra-product-options').'</div><div class="tm-view tm-header"><div class="tm-50">'.__('Option','woocommerce-tm-extra-product-options').'</div><div class="tm-50">'.__('Value','woocommerce-tm-extra-product-options').'</div></div></td>';
				if(empty($item['item_meta']['tm_has_dpd']) && empty($item['item_meta']['_tm_has_dpd'])){
					$html_toadd .= '<td class="tm-c item_cost" width="1%"><div class="tm-view"><div class="tm-view tm-order-header">&nbsp;</div>'.__('Price','woocommerce-tm-extra-product-options').'</div></td>';
					$html_toadd .= '<td class="tm-c tm_quantity" width="1%"><div class="tm-view"><div class="tm-view tm-order-header">&nbsp;</div>'.__('Qty','woocommerce-tm-extra-product-options').'</div></td>';
					$html_toadd .= '<td class="tm-c line_cost" width="1%"><div class="tm-view"><div class="tm-view tm-order-header">&nbsp;</div>'.__('Total','woocommerce-tm-extra-product-options').'</div></td>';
				}else{
					$html_toadd .= '<td class="tm-c item_cost" width="1%"><div class="tm-view"><div class="tm-view tm-order-header">&nbsp;</div>&nbsp;</div></td>';
					$html_toadd .= '<td class="tm-c tm_quantity" width="1%"><div class="tm-view"><div class="tm-view tm-order-header">&nbsp;</div>'.__('Qty','woocommerce-tm-extra-product-options').'</div></td>';
					$html_toadd .= '<td class="tm-c line_cost" width="1%"><div class="tm-view"><div class="tm-view tm-order-header">&nbsp;</div>&nbsp;</div></td>';
				}
				$html_toadd .= '<td class="tm-c wc-order-edit-line-item">&nbsp;</td></tr>';
				$hasit=false;
				$html_epo='';
				foreach ($epos as $key => $epo) {
					if ($epo && is_array($epo)){

						$new_currency = false;
						if (isset($epo['price_per_currency'])){
							$_current_currency_prices = $epo['price_per_currency'];
							if ($mt_prefix!=='' 
									&& $_current_currency_prices!=='' 
									&& is_array($_current_currency_prices) 
									&& isset($_current_currency_prices[$mt_prefix])
									&& $_current_currency_prices[$mt_prefix]!=''){

								$new_currency = true;
								$epo['price'] = $_current_currency_prices[$mt_prefix];
								
							}
						}
						if(!$new_currency){
							$type="";
							if (isset($epo['element']) && isset($epo['element']['_']) &&isset($epo['element']['_']['price_type'])){
								$type=$epo['element']["_"]['price_type'];
							}
							$epo['price'] = apply_filters('wc_epo_get_current_currency_price',$epo['price'],$type);
						}

						$hasit=true;
						if(!isset($epo['quantity'])){
							$epo['quantity'] = 1;
						}
						if(isset($wpml_translation_by_id[$epo['section']])){
							$epo['name'] = $wpml_translation_by_id[$epo['section']];
						}
						// local mode
						if ( !isset($epo['price_per_currency']) && taxonomy_exists( $epo['name'] ) ) {
							$epo['name'] = wc_attribute_label( $epo['name'] );
						}
						if(isset($wpml_translation_by_id["options_".$epo['section']]) 
							&& is_array($wpml_translation_by_id["options_".$epo['section']]) 
							&& !empty($epo['multiple']) 
							&& !empty($epo['key'])){
							
							$pos = strrpos($epo['key'], '_');
							
							if($pos!==false) {
								
								$av=array_values( $wpml_translation_by_id["options_".$epo['section']] );
								
								if (isset($av[substr($epo['key'], $pos+1)])){
									
									$epo['value'] = $av[substr($epo['key'], $pos+1)];
									
								}

							}

						}
						$display_value = $epo['value'];
						if (is_array($epo['value'])){
							$display_value = array_map('html_entity_decode', $display_value, version_compare(phpversion(), '5.4', '<') ? ENT_COMPAT : (ENT_COMPAT | ENT_HTML401) ,'UTF-8');
						}else{
							$display_value = html_entity_decode($display_value, version_compare(phpversion(), '5.4', '<') ? ENT_COMPAT : (ENT_COMPAT | ENT_HTML401) ,'UTF-8');
						}
						
						if (!empty($epo['use_images']) && !empty($epo['images']) && $epo['use_images']=="images"){
							$display_value ='<div class="cpf-img-on-cart"><img alt="" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="'.apply_filters("tm_image_url",$epo['images']).'" /></div>'.esc_attr($display_value);
						}

						$html_epo .= '<tr class="tm-order-line-option item" data-tm_item_id="'.esc_attr($item_id).'" data-tm_key_id="'.esc_attr($key).'">';
						$html_epo .= $check_box_html;
						$html_epo .= '<td class="thumb">&nbsp;</td>';

						// name
						$html_epo .= '<td class="tm-c name">';
						$html_epo .= '<div class="tm-50">'.apply_filters('tm_translate',$epo['name']).'</div>';
						
						// value
						$display_value = apply_filters('tm_translate',$display_value);

						if (isset($epo['element']) && isset($epo['element']['type']) && $epo['element']['type'] == 'upload'){
							$check = wp_check_filetype( $epo['value'] );
							if ( !empty( $check['ext'] ) ) {
	                			$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );
	                			if ( in_array( $check['ext'], $image_exts ) ){
	                				$display_value ='<a target="_blank" href="'.$display_value.'"><span class="cpf-img-on-cart"><img alt="" class="attachment-shop_thumbnail wp-post-image epo-option-image epo-upload-image" src="'.
										apply_filters("tm_image_url",$epo['value']).'" /></span></a>';
	                			}
	        				}
						}

						if (!empty($epo['multiple_values'])){
							$display_value_array=explode($epo['multiple_values'],$display_value);
							$display_value="";
							foreach ($display_value_array as $d => $dv) {
								$display_value .='<span class="cpf-data-on-cart">'.$dv.'</span>';
							}
							//$epo['value'] = $display_value;
						}

						$html_epo .= '<div class="view"><div class="tm-50">'.make_clickable( $display_value ).'</div></div>';
						$html_epo .= '<div class="edit" style="display: none;"><div class="tm-50"><textarea name="tm_epo['.$item_id.']['.$key.'][value]" class="value">'.( $epo['value']  ).'</textarea></div></div>';
						
						$html_epo .= '</td>';
						
						if(empty($item['item_meta']['tm_has_dpd']) && empty($item['item_meta']['_tm_has_dpd'])){
							//price
							$html_epo .= '<td class="tm-c item_cost" width="1%">';
							if ($epo['quantity'] <=0){
								$html_epo .= '<div class="view">'.wc_price( 0 ).'</div>';
								$html_epo .= '<div class="edit" style="display: none;"><input type="number" name="tm_epo['.$item_id.']['.$key.'][price]" placeholder="0" value="0" data-qty="0" size="4" class="price" /></div>';
							}else{
								$html_epo .= '<div class="view">'.wc_price( $epo['price']/$epo['quantity'] ).'</div>';	
								$html_epo .= '<div class="edit" style="display: none;"><input type="number" name="tm_epo['.$item_id.']['.$key.'][price]" placeholder="0" value="'.( $epo['price']/$epo['quantity'] ).'" data-qty="'.( $epo['price']/$epo['quantity']  ).'" size="4" class="price" /></div>';
							}
							
							
							$html_epo .= '</td>';
							//quantity
							$html_epo .= '<td class="tm-c tm_quantity" width="1%">';
							$html_epo .= '<div class="view">'.( $epo['quantity'] * (float) $item['item_meta']['_qty'][0] ).' <small>('.$epo['quantity'].'&times;'.(float) $item['item_meta']['_qty'][0].')</small></div>';
							$html_epo .= '<div class="edit" style="display: none;"><input type="number" name="tm_epo['.$item_id.']['.$key.'][quantity]" placeholder="0" value="'.( $epo['quantity'] ).'" data-qty="'.( $epo['quantity']  ).'" size="4" class="quantity" /> <small>&times;'.(float) $item['item_meta']['_qty'][0].'</small></div>';
							$html_epo .= '</td>';
							//total cost
							$html_epo .= '<td class="tm-c line_cost" width="1%">';
							$html_epo .= '<div class="view"><span class="amount">'.wc_price(  (float) $epo['price'] * (float) $item['item_meta']['_qty'][0] ).'</span></div>';
							$html_epo .= '</td>';
						}else{
							//price
							$html_epo .= '<td class="tm-c item_cost" width="1%">';
							$html_epo .= '<div class="view">&nbsp;</div>';
							$html_epo .= '</td>';
							//quantity
							$html_epo .= '<td class="tm-c tm_quantity" width="1%">';
							$html_epo .= '<div class="view">'.( $epo['quantity'] * (float) $item['item_meta']['_qty'][0] ).' <small>('.$epo['quantity'].'&times;'.(float) $item['item_meta']['_qty'][0].')</small></div>';
							$html_epo .= '</td>';
							//total cost
							$html_epo .= '<td class="tm-c line_cost" width="1%">';
							$html_epo .= '<div class="view">&nbsp;</div>';
							$html_epo .= '</td>';
						}
						$html_epo .= '<td class="tm-c wc-order-edit-line-item">';
						if ( $order && is_object($order) && method_exists($order, 'is_editable') && $order->is_editable() ){
							$html_epo .= '<div class="wc-order-edit-line-item-actions"><a class="edit-order-item" href="#"></a><a href="#" class="tm-delete-order-item"></a></div>';
						}else{
							$html_epo .= '&nbsp;';
						}
						$html_epo .= '</td></tr>';
					}
				}

				if ($hasit){
					$html .= $html_toadd.$html_epo;
				}
			}
		}

		if ($has_fee){
			$epos = maybe_unserialize($item['item_meta']['_tmcartfee_data'][0]);
			if (isset($epos[0])){
				$epos = $epos[0];				
			}else{
				$epos = false;
			}


			if ($epos && is_array($epos) && !empty($epos[0])){
				$html .= '<tr class="tm-order-line item '.apply_filters( 'woocommerce_admin_html_order_item_class', ( ! empty( $class ) ? $class : '' ), $item ) .'">'
						. $check_box_html
						.'<td class="thumb">&nbsp;</td>';
				$html .= '<td class="tm-c name"><div class="view tm-order-header">'.__('Extra Product Options Fees','woocommerce-tm-extra-product-options').'</div><div class="view tm-header"><div class="tm-50">'.__('Option','woocommerce-tm-extra-product-options').'</div><div class="tm-50">'.__('Value','woocommerce-tm-extra-product-options').'</div></div></td>';
				$html .= '<td class="tm-c tm_quantity" width="1%"><div class="view"><div class="view tm-order-header">&nbsp;</div>'.__('Qty','woocommerce-tm-extra-product-options').'</div></td>';

				$html .= '<td class="tm-c wc-order-edit-line-item">&nbsp;</td>';
				
				foreach ($epos as $key => $epo) {
					if ($epo && is_array($epo)){
						if(!isset($epo['quantity'])){
							$epo['quantity'] = 1;
						}
						if(isset($wpml_translation_by_id[$epo['section']])){
							$epo['name'] = $wpml_translation_by_id[$epo['section']];
						}
						if(isset($wpml_translation_by_id["options_".$epo['section']]) && is_array($wpml_translation_by_id["options_".$epo['section']]) && !empty($epo['multiple']) && !empty($epo['key'])){
							$pos = strrpos($epo['key'], '_');
							if($pos!==false) {
								$av=array_values( $wpml_translation_by_id["options_".$epo['section']] );
								if (isset($av[substr($epo['key'], $pos+1)])){
									$epo['value'] = $av[substr($epo['key'], $pos+1)];
									if (!empty($epo['use_images']) && !empty($epo['images']) && $epo['use_images']=="images"){
										$epo['value'] ='<div class="cpf-img-on-cart"><img alt="" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="'.apply_filters("tm_image_url",$epo['images']).'" /></div>'.$epo['value'];
									}
								}
							}
						}
						$html .= '<tr class="tm-order-line-option item">';
						$html .= $check_box_html;
						$html .= '<td class="thumb">&nbsp;</td>';
						$html .= '<td class="tm-c name"><div class="view"><div class="tm-50">'.apply_filters('tm_translate',$epo['name']).'</div><div class="tm-50">'.apply_filters('tm_translate',$epo['value']).'</div></div></td>';
						$html .= '<td class="tm-c tm_quantity" width="1%"><div class="view">'.( $epo['quantity'] * (float) $item['item_meta']['_qty'][0] ).' <small>('.$epo['quantity'].'&times;'.(float) $item['item_meta']['_qty'][0].')</small></div></td>';
						$html .= '<td class="tm-c wc-order-edit-line-item">&nbsp;</td>';
						$html .= '</tr>';
					}
				}
			}
		}

		echo $html;
	}

	/** Alter the product thumbnail in order **/
	public function tm_woocommerce_admin_order_item_thumbnail($image="", $item_id="", $item=""){
		$_image=array();
		$_alt=array();

		$has_epo = isset($item['item_meta']) && isset($item['item_meta']['_tmcartepo_data']) && isset($item['item_meta']['_tmcartepo_data'][0]);
		$has_epo_fee = isset($item['item_meta']) && isset($item['item_meta']['_tmcartfee_data']) && isset($item['item_meta']['_tmcartfee_data'][0]);
		
		if ($has_epo){
			$epos = maybe_unserialize($item['item_meta']['_tmcartepo_data'][0]);
			if (!is_array($epos)){
				return $image;
			}

			if ($epos){
				foreach ($epos as $key => $value) {
					if (!empty($value['changes_product_image'])){
						if ($value['changes_product_image']=='images'){
							if (isset($value['use_images']) && $value['use_images']=='images' && isset($value['images'])){
								$_image[] = $value['images'];
								$_alt[] =  $value['value'];
							}
						}elseif($value['changes_product_image']=='custom'){
							if (isset($value['imagesp'])){
								$_image[] = $value['imagesp'];
								$_alt[] =  $value['value'];
							}
						}
					}
				}
			}
		}
		
		if (count($_image)==0){
			if ($has_epo_fee){
				$epos = maybe_unserialize($item['item_meta']['_tmcartfee_data'][0]);
				if (!is_array($epos)){
					return $image;
				}

				if ($epos){
					foreach ($epos as $key => $value) {
						if (!empty($value['changes_product_image'])){
							if ($value['changes_product_image']=='images'){
								if (isset($value['use_images']) && $value['use_images']=='images' && isset($value['images'])){
									$_image[] = $value['images'];
									$_alt[] =  $value['value'];
								}
							}elseif($value['changes_product_image']=='custom'){
								if (isset($value['imagesp'])){
									$_image[] = $value['imagesp'];
									$_alt[] =  $value['value'];
								}
							}
						}
					}
				}
			}
		}

		if (count($_image)>0){
			$current=0;
			for ($i=count($_image); $i >0 ; $i--) { 
				if (!empty($_image[$i])){
					$current=$i;
				}
			}
			$size = 'shop_thumbnail';
			$dimensions = wc_get_image_size( $size );
			$image = apply_filters('tm_woocommerce_img', 
				'<img src="' . 	apply_filters( 'tm_woocommerce_img_src', $_image[$current] )
				 . '" alt="' 
				 . esc_attr($_alt[$current])
				 . '" width="' . esc_attr( $dimensions['width'] ) 
			 	 . '" class="woocommerce-placeholder wp-post-image" height="' 
				 . esc_attr( $dimensions['height'] ) 
				 . '" />', $size, $dimensions );
		}
		
		
		return $image;
	}

	/** Alter the product thumbnail in cart **/
	public function tm_woocommerce_cart_item_thumbnail($image="", $cart_item=array(), $cart_item_key=""){
		$_image=array();
		$_alt=array();
		if (isset($cart_item['tmcartepo']) && is_array($cart_item['tmcartepo'])){
			foreach ($cart_item['tmcartepo'] as $key => $value) {
				if (!empty($value['changes_product_image'])){
					if ($value['changes_product_image']=='images'){
						if (isset($value['use_images']) && $value['use_images']=='images' && isset($value['images'])){
							$_image[] = $value['images'];
							$_alt[] =  $value['value'];
						}
					}elseif($value['changes_product_image']=='custom'){
						if (isset($value['imagesp'])){
							$_image[] = $value['imagesp'];
							$_alt[] =  $value['value'];
						}
					}
				}
			}
		}
		if (count($_image)==0){
			if (isset($cart_item['tmcartfee']) && is_array($cart_item['tmcartfee'])){
				foreach ($cart_item['tmcartfee'] as $key => $value) {
					if (!empty($value['changes_product_image'])){
						if ($value['changes_product_image']=='images'){
							if (isset($value['use_images']) && $value['use_images']=='images' && isset($value['images'])){
								$_image[] = $value['images'];
								$_alt[] =  $value['value'];
							}
						}elseif($value['changes_product_image']=='custom'){
							if (isset($value['imagesp'])){
								$_image[] = $value['imagesp'];
								$_alt[] =  $value['value'];
							}
						}
					}
				}
			}
		}
		if (count($_image)>0){
			$current=0;
			for ($i=count($_image); $i >0 ; $i--) { 
				if (!empty($_image[$i])){
					$current=$i;
				}
			}
			$size = 'shop_thumbnail';
			$dimensions = wc_get_image_size( $size );
			$image = apply_filters('tm_woocommerce_img', 
				'<img src="' . 	apply_filters( 'tm_woocommerce_img_src', $_image[$current] )
				 . '" alt="' 
				 . esc_attr($_alt[$current])
				 . '" width="' . esc_attr( $dimensions['width'] ) 
				 . '" class="woocommerce-placeholder wp-post-image" height="' 
				 . esc_attr( $dimensions['height'] ) 
				 . '" />', $size, $dimensions );
		}
		
		return $image;
	}

	/** Custom actions running for advanced template system **/
	public function tm_woocommerce_checkout_after_row($cart_item_key="", $cart_item="", $_product="", $product_id=""){
		$out = '';
		$other_data = array();
		if ($this->tm_epo_hide_options_in_cart=="normal"){
			$other_data = $this->get_item_data_array( array(), $cart_item );
		}
		$odd=1;
		foreach ($other_data as $key => $value) {
			$zebra_class="odd ";
			if (!$odd){
				$zebra_class="even ";
				$odd=2;
			}
			$out .= '<tr class="tm-epo-cart-row '.$zebra_class.esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ).'">';
			
			$name = '<div class="tm-epo-cart-option-value tm-epo-cart-no-label">'.$value['tm_value'].' <strong class="product-quantity">' . sprintf( '&times; %s', $value['tm_quantity']*$cart_item['quantity'] ) . '</strong>'.'</div>';
			if (!empty($value['tm_label'])){
				$name = '<div class="tm-epo-cart-option-label">'.$value['tm_label'].' <strong class="product-quantity">' 
					. apply_filters( 'wc_tm_epo_ac_qty', sprintf( '&times; %s', $value['tm_quantity']*$cart_item['quantity'] ), $cart_item_key, $cart_item, $value, $_product, $product_id  )
					. '</strong>'.'</div>'.'<div class="tm-epo-cart-option-value">'.$value['tm_value'].'</div>';
			}
			$out .= '<td class="product-name">'.$name.'</td>';			
			
			$out .= '<td class="product-subtotal">'.$value['tm_total_price'].'</td>';
			$out .= '</tr>';
			$odd--;
		}
		
		echo $out;
	}

	/** Custom actions running for advanced template system **/
	public function tm_woocommerce_cart_after_row($cart_item_key="", $cart_item="", $_product="", $product_id=""){
		$out = '';
		$other_data = array();
		if ($this->tm_epo_hide_options_in_cart=="normal"){
			$other_data = $this->get_item_data_array( array(), $cart_item );
		}
		$odd=1;
		foreach ($other_data as $key => $value) {
			$zebra_class="odd ";
			if (!$odd){
				$zebra_class="even ";
				$odd=2;
			}
			$out .= '<tr class="tm-epo-cart-row '.$zebra_class.esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ).'">';
			$out .= '<td class="product-remove">&nbsp;</td>';
			$thumbnail='&nbsp;';

			$out .= '<td class="product-thumbnail">'.$thumbnail.'</td>';
			$name = '<div class="tm-epo-cart-option-value tm-epo-cart-no-label">'.$value['tm_value'].'</div>';
			if (!empty($value['tm_label'])){
				$name = '<div class="tm-epo-cart-option-label">'.$value['tm_label'].'</div>'.'<div class="tm-epo-cart-option-value">'.$value['tm_value'].'</div>';
			}
			$out .= '<td class="product-name">'.$name.'</td>';
			$out .= '<td class="product-price">'.$value['tm_price'].'</td>';
			$out .= '<td class="product-quantity">'.apply_filters( 'wc_tm_epo_ac_qty', $value['tm_quantity']*$cart_item['quantity'], $cart_item_key, $cart_item, $value, $_product, $product_id  ).'</td>';
			$out .= '<td class="product-subtotal">'.$value['tm_total_price'].'</td>';
			$out .= '</tr>';
			$odd--;
		}
		if (is_array($other_data) && count($other_data)>0){
			$out .= '<tr class="tm-epo-cart-row tm-epo-cart-row-total '.esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ).'">';
			$out .= '<td class="product-remove">&nbsp;</td>';
			$out .= '<td class="product-thumbnail">&nbsp;</td>';
			$out .= '<td class="product-name">&nbsp;</td>';
			$out .= '<td class="product-price">&nbsp;</td>';
			if ( $_product->is_sold_individually() ) {
				$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
			} else {
				$product_quantity = woocommerce_quantity_input( array(
					'input_name'  => "cart[{$cart_item_key}][qty]",
					'input_value' => $cart_item['quantity'],
					'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
					'min_value'   => '0'
				), $_product, false );
			}			
			$out .= '<td class="product-quantity">'.apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key ).'</td>';
			$out .= '<td class="product-subtotal">'.apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ).'</td>';
			$out .= '</tr>';			
		}
		
		echo $out;
	}

	/** Override templates for Cart advanced template system **/
	public function tm_wc_get_template($located="", $template_name="", $args="", $template_path="", $default_path="") {

		$templates = array("cart/cart-item-data.php");
		if ($this->tm_epo_cart_field_display=="advanced"){
			
			$templates = array_merge($templates,array('cart/cart.php','checkout/review-order.php')); 
			
		}
		//$templates[]='single-product/product-image.php';
		if (in_array($template_name, $templates)){
			$_located = wc_locate_template( $template_name, $this->_namespace, TM_EPO_TEMPLATE_PATH );
			if ( file_exists( $_located ) ) {
				$located = $_located;
			}
		}

		return $located;

	}

	/** Support for fee price types **/
	public function tm_calculate_cart_fee( $cart_object=array() ) {

		if (is_array($cart_object->cart_contents )){

			$to_currency = tc_get_woocommerce_currency();
			
		    foreach ( $cart_object->cart_contents as $key => $value ) {
		    	
		    	//if(get_option( 'woocommerce_calc_taxes' )=="yes" && $value["data"]->tax_status=="taxable" && !$cart_object->prices_include_tax){
		    	if(get_option( 'woocommerce_calc_taxes' )=="yes" && $value["data"]->tax_status=="taxable"){
		    		$tax=true;
		    		$tax_class=$value["data"]->tax_class;
		    	}else{
		    		$tax=false;
		    		$tax_class=$value["data"]->tax_class;
		    	}
		    	
		    	$tmcartfee=isset($value['tmcartfee'])?$value['tmcartfee']:false;
		    	if ($tmcartfee && is_array($tmcartfee)){
		    		foreach ( $tmcartfee as $cartfee ) {
						$new_price = $cartfee["price"];
						
						if(isset($cartfee['price_per_currency']) && isset($cartfee['price_per_currency'][$to_currency]) && $cartfee['price_per_currency'][$to_currency]!=''){
							$new_price=(float)wc_format_decimal($cartfee['price_per_currency'][$to_currency],false,true);
						}else{
							$new_price = apply_filters( 'wc_epo_get_current_currency_price', apply_filters( 'woocommerce_tm_epo_price_on_cart',$new_price) );
						}

						if ($cart_object->prices_include_tax){
							$new_price =$value['data']->get_price_excluding_tax(1,$new_price);
						}
						
						$new_name = $cartfee["name"];
						if (empty($new_name)){
							$new_name=__("Extra fee",'woocommerce-tm-extra-product-options');
						}
						$canbadded=true;
				        if (is_array($cart_object->fees )){    
							foreach ( $cart_object->fees as $fee ) {
								if ( $fee->id == sanitize_title($new_name) ) {
									$fee->amount=$fee->amount+(float) $new_price;
									$canbadded=false;
									break;
								}
							}
						}
						if($canbadded){												
							$new_name .= apply_filters( 'wc_epo_fee_quantity_separator', " - ").$cartfee["value"];
							if (floatval($cartfee["quantity"])>1){
								$new_name .= apply_filters( 'wc_epo_fee_quantity_times', " &times; ").$cartfee["quantity"];
							}
							$current_tax = $tax;
							$current_tax_class = $tax_class;
							if (isset($cartfee["include_tax_for_fee_price_type"]) && $cartfee["include_tax_for_fee_price_type"]!==''){
								if ($cartfee["include_tax_for_fee_price_type"]=="yes"){
									$current_tax = true;
								}elseif($cartfee["include_tax_for_fee_price_type"]=="no"){
									$current_tax = false;
								}
							}
							if (isset($cartfee["tax_class_for_fee_price_type"]) && $cartfee["tax_class_for_fee_price_type"]!==''){
								$current_tax_class = $cartfee["tax_class_for_fee_price_type"];
							}
							$cart_object->add_fee( $new_name, $new_price,$current_tax,$current_tax_class );	
						}
					}
				}
			}
		}
	}

	/** Check if the plugin is active for the user **/
	public function check_enable(){
		$enable=false;
		$enabled_roles=$this->tm_epo_roles_enabled;
		$disabled_roles=$this->tm_epo_roles_disabled;

		if (isset($this->tm_meta_cpf['override_enabled_roles']) && $this->tm_meta_cpf['override_enabled_roles']!==""){
			$enabled_roles = $this->tm_meta_cpf['override_enabled_roles'];
		}
		if (isset($this->tm_meta_cpf['override_disabled_roles']) && $this->tm_meta_cpf['override_disabled_roles']!==""){
			$disabled_roles = $this->tm_meta_cpf['override_disabled_roles'];
		}
		// Get all roles
		$current_user = wp_get_current_user();

		if (!is_array($enabled_roles)){
			$enabled_roles=array($enabled_roles);
		}
		if (!is_array($disabled_roles)){
			$disabled_roles=array($disabled_roles);
		}

		//Check if plugin is enabled for everyone
		foreach ($enabled_roles as $key => $value) {
			if($value=="@everyone"){
				$enable=true;
			}
			if($value=="@loggedin" && is_user_logged_in()){
				$enable=true;
			}
		}

		if ( $current_user instanceof WP_User ){	
			$roles = $current_user->roles;			
			// Check if plugin is enabled for current user 
			if (is_array($roles)){

				foreach ($roles as $key => $value) {
					if (in_array($value, $enabled_roles)){
						$enable=true;
						break;
					}
				}

				foreach ($roles as $key => $value) {
					if (in_array($value, $disabled_roles)){
						$enable=false;
						break;
					}
				}

			}
		}

		return $enable;
	}

	/** Check if we are on a supported quickview mode **/
	public function is_quick_view(){
		$qv=false;
		$woothemes_quick_view=( isset($_GET['wc-api']) && $_GET['wc-api']=='WC_Quick_View' );
		$theme_flatsome_quick_view=( isset($_POST['action']) && ($_POST['action']=='jck_quickview' || $_POST['action']=='ux_quickview')  );
		$theme_kleo_quick_view=( isset($_POST['action']) && ($_POST['action']=='woo_quickview') );
		$yith_quick_view=( isset($_POST['action']) && ($_POST['action']=='yith_load_product_quick_view') );
		$venedor_quick_view=( isset($_GET['action']) && ($_GET['action']=='venedor_product_quickview') );
		$rubbez_quick_view=( isset($_POST['action']) && ($_POST['action']=='product_quickview') );
		$jckqv_quick_view=( isset($_POST['action']) && ($_POST['action']=='jckqv') );//http://codecanyon.net/item/woocommerce-quickview/4378284
		$themify_quick_view=( isset($_GET['ajax']) && $_GET['ajax']=='true' );
		$porto_quick_view=( isset($_GET['action']) && ($_GET['action']=='porto_product_quickview') );
		$woocommerce_product_layouts=( isset($_POST['action']) && ($_POST['action']=='dhvc_woo_product_quickview') );//http://codecanyon.net/item/woocommerce-products-layouts/7384574?
		$nm_getproduct=( isset($_POST['action']) && ($_POST['action']=='nm_getproduct') );//Woo Product Quick View http://codecanyon.net/item/woocommerce-product-quick-view/11293528? 
		if ( $woothemes_quick_view 
			|| $theme_flatsome_quick_view 
			|| $theme_kleo_quick_view 
			|| $yith_quick_view
			|| $venedor_quick_view
			|| $rubbez_quick_view
			|| $jckqv_quick_view
			|| $themify_quick_view
			|| $porto_quick_view
			|| $woocommerce_product_layouts
			|| $nm_getproduct
			){
			$qv=true;
		}
		return apply_filters( 'woocommerce_tm_quick_view',$qv);
	}

	/** Check if the setting "Enable plugin for WooCommerce shortcodes" is active **/
	public function is_enabled_shortcodes(){
		return ($this->tm_epo_enable_shortcodes=="yes");
	}

	public function tm_epo_price_filtered($price="",$type=""){
		return apply_filters( 'wc_epo_get_current_currency_price',$price,$type);
	}

	/** For hiding uploaded file path **/
	public function tm_order_item_display_meta_value($value="",$override=0){
		$original_value=$value;
		
		if (is_array($value)){
			$new_value=array();
			foreach ($value as $k => $v) {
				if (is_array($v)){
					foreach ($v as $k2 => $v2) {
						$original_value=$v2;
						$found=(strpos($v2, $this->upload_dir) !== FALSE);
						if (($found && empty($override) ) || !empty($override)){
							if($this->tm_epo_hide_upload_file_path != 'no' && filter_var($v2 , FILTER_VALIDATE_URL)){
								$v2=basename($v2);
							}
						}
						if (!empty($override)){
							$v2='<a href="'.$original_value.'">'.$v2.'</a>';
						}
						$v[$k2]=$v2;
					}
				}else{

					$original_value=$v;
					$found=(strpos($v, $this->upload_dir) !== FALSE);
					if (($found && empty($override) ) || !empty($override)){
						if($this->tm_epo_hide_upload_file_path != 'no' && filter_var($v , FILTER_VALIDATE_URL)){
							$v=basename($v);
						}
					}
					if (!empty($override)){
						$v='<a href="'.$original_value.'">'.$v.'</a>';
					}

				}
				$new_value[$k]=$v;
			}
			$value=$new_value;
		}else{
			$found=(strpos($value, $this->upload_dir) !== FALSE);
			if (($found && empty($override) ) || !empty($override)){
				if($this->tm_epo_hide_upload_file_path != 'no' && filter_var($value , FILTER_VALIDATE_URL)){
					$value=basename($value);
				}
			}
			if (!empty($override)){
				$value='<a href="'.$original_value.'">'.$value.'</a>';
			}
		}
		if (is_array($value)){
			$value=implode(",",$value);
		}
		return $value;
	}

	/** Enable shortcodes for labels **/	
	public function tm_epo_option_name( $label="", $args=null, $counter=null ) {
		return do_shortcode($label);
	}

	/** Alters the Free label html **/
	public function get_price_html( $price="", $product="" ) {
		if ($product && is_object($product) && method_exists($product, "get_price") ){
			if((float)$product->get_price()>0){
				return $price;
			}else{
				return sprintf( $this->tm_epo_replacement_free_price_text, $price );
			}
		}else{
			return sprintf( $this->tm_epo_replacement_free_price_text, $price );	
		}		
	}

	/** Fix for related products when replacing free label **/
	public function related_get_price_html( $price="", $product="" ) {
		if ($product && is_object($product) && method_exists($product, "get_price") ){
			if((float)$product->get_price()>0){
				return $price;
			}else{
				if ($this->tm_epo_replacement_free_price_text){
					return sprintf( $this->tm_epo_replacement_free_price_text, $price );
				}else{
					$price='';
				}
			}
		}else{
			if ($this->tm_epo_replacement_free_price_text){
				return sprintf( $this->tm_epo_replacement_free_price_text, $price );
			}else{
				$price='';
			}
		}		

		return $price;
	}

	/** Fix for related products when replacing free label **/
	public function related_get_price_html2( $price="", $product="" ) {
		if ($product && is_object($product) && method_exists($product, "get_price") ){

			if((float)$product->get_price()>0){
				return $price;
			}else{

				$thiscpf=$this->get_product_tm_epos($product->id);

				if (is_array($thiscpf) && (!empty($thiscpf['global']) || !empty($thiscpf['local']))) {
					if ($this->tm_epo_replacement_free_price_text){
						return sprintf( $this->tm_epo_replacement_free_price_text, $price );
					}else{
						$price='';
					}
				}
			}
		}

		return $price;
	}

	/** Free label text replacement **/
	public function get_price_html_shop( $price="", $product="" ) {
		if ($product && 
			is_object($product) && method_exists($product, "get_price") 
			&& !(float)$product->get_price()>0 ){
			
			if ($this->tm_epo_replacement_free_price_text){
				$price=sprintf( $this->tm_epo_replacement_free_price_text, $price );
			}else{
				$price='';
			}
		}
		return $price;
	}

	/** Replaces add to cart text when the force select setting is enabled **/
	public function add_to_cart_text( $text="" ) {
		global $product;
			
		if ( $this->tm_epo_enable_in_shop=="no" && $this->tm_epo_force_select_options=="display" && is_object($product) && property_exists($product,'id') ) {
			$cpf=$this->get_product_tm_epos($product->id);
			if (is_array($cpf) && (!empty($cpf['global']) || !empty($cpf['local']))) {
				$text = (!empty($this->tm_epo_force_select_text)) ?$this->tm_epo_force_select_text :__( 'Select options', 'woocommerce-tm-extra-product-options' );
			}
		}
		if ( $this->tm_epo_enable_in_shop=="yes"){
			$text = __( 'Add to cart', 'woocommerce' );
		}

		return $text;
	}

	/** Prevenets ajax add to cart when product has extra options and the force select setting is enabled **/
	public function add_to_cart_url( $url="" ) {
		global $product;

		if ( 
			/*(
				is_shop() 
				|| is_product_category() 
				|| is_product_tag()
			) 
			&&*/
			!is_product()
			&& $this->tm_epo_force_select_options=="display" 
			&& is_object($product) 
			&& property_exists($product,'id') 
		) {
			
			$cpf=$this->get_product_tm_epos($product->id);
			
			if (is_array($cpf) && (!empty($cpf['global']) || !empty($cpf['local']))) {
				$url = get_permalink( $product->id );
			}
		}

		return $url;
	}

	/** Empties the cart **/
	public function tm_empty_cart() {
		if ( ! isset( WC()->cart ) || WC()->cart == '' ) {
			WC()->cart = new WC_Cart();
		}
		WC()->cart->empty_cart( true );
	}

	/** Empties the cart **/
	public function clear_cart() {
		if ( isset( $_POST['tm_empty_cart'] ) ) {
			$this->tm_empty_cart();
		}
	}

	/** Adds the Empty cart button **/
	public function add_empty_cart_button(){
		$text = (!empty($this->tm_epo_empty_cart_text)) ?$this->tm_epo_empty_cart_text :__( 'Empty cart', 'woocommerce-tm-extra-product-options' );
		echo '<input type="submit" class="tm-clear-cart-button checkout-button button wc-forward" name="tm_empty_cart" value="'.$text.'" />';
	}

	/** Sets current product settings **/
	public function set_tm_meta($override_id=0){
		if (empty($override_id)){
			if (isset($_REQUEST['add-to-cart'])){
				$override_id=$_REQUEST['add-to-cart'];
			}else{
				global $post;
				if (!is_null($post) && property_exists($post,'ID') && property_exists($post,'post_type')){
					if ($post->post_type!="product"){
						return;
					}
					$override_id=$post->ID;
				}				
			}
		}
		if (empty($override_id)){
			return;
		}

		// translated products inherit original product meta overrides
		$override_id=floatval(TM_EPO_WPML()->get_original_id( $override_id,'product' ));

		$this->tm_meta_cpf = get_post_meta( $override_id, 'tm_meta_cpf', true );		
		if (!is_array($this->tm_meta_cpf)){
			$this->tm_meta_cpf=array();
		}
		foreach ( $this->meta_fields as $key=>$value ) {
			$this->tm_meta_cpf[$key] = isset( $this->tm_meta_cpf[$key] ) ? $this->tm_meta_cpf[$key] : $value;
		}
		$this->tm_meta_cpf['metainit']=1;
	}

	/** Modifies the cart item. **/
	public function add_cart_item( $cart_item=array() ) {
		
		$cart_item['tm_epo_product_original_price']=$cart_item['data']->price;

		$cart_item['tm_epo_options_prices']=0;
		$cart_item['tm_epo_product_price_with_options']=$cart_item['data']->price;

		if ( ! empty( $cart_item['tmcartepo'] ) ) {
		
			$tmcp_prices = 0;
			$to_currency = tc_get_woocommerce_currency();
			if (is_array($cart_item['tmcartepo'] )){
				foreach ( $cart_item['tmcartepo'] as $tmcp ) {
					if(isset($tmcp['subscription_fees'])){
						continue;
					}
					if(isset($tmcp['price_per_currency']) && isset($tmcp['price_per_currency'][$to_currency]) && $tmcp['price_per_currency'][$to_currency]!=''){
						$tmcp['price']=apply_filters( 'woocommerce_tm_epo_price_per_currency_diff', (float)wc_format_decimal($tmcp['price_per_currency'][$to_currency],false,true) );
						$tmcp_prices += $tmcp['price'];
					}else{
						$tmcp['price']=(float)wc_format_decimal($tmcp['price'],false,true);
						$tmcp_prices += apply_filters( 'woocommerce_tm_epo_price_add_on_cart', 
							$tmcp['price'], isset($tmcp['element'])?$tmcp['element']['rules_type'][isset($tmcp['key'])?$tmcp['key']:0][0]:'');
					}
				}
			}

			$tmcp_prices = apply_filters('wc_epo_cart_options_prices',$tmcp_prices,$cart_item);

			if (apply_filters('tm_epo_cart_options_prices',$tmcp_prices,$cart_item)<0){
				if ($this->tm_epo_no_negative_priced_products=="yes"){
					throw new Exception( __( "You cannot add negative priced products to the cart.", 'woocommerce-tm-extra-product-options'  ) );
				}
			}

			$cart_item['tm_epo_options_prices']=$tmcp_prices;

			$cart_item['tm_epo_product_original_price']=$cart_item['data']->price;

			if ( apply_filters( 'wc_epo_adjust_price', true, $cart_item ) ){
				if (!empty($cart_item['epo_price_override']) && $tmcp_prices>0){
					$cart_item['data']->set_price( $tmcp_prices );
				}else{
					$cart_item['data']->adjust_price( $tmcp_prices );
				}				
			}

			$cart_item['tm_epo_product_price_with_options']=$cart_item['data']->price;

		}

		// variation slug-to-name-for order again
		if ( isset( $cart_item["variation"] ) && is_array( $cart_item["variation"] ) ) {
			$_variation_name_fix=array();
			$_temp=array();
			foreach ( $cart_item["variation"] as $meta_name => $meta_value ) {
				if ( strpos( $meta_name, "attribute_" )!==0 ) {
					$_variation_name_fix["attribute_".$meta_name]=$meta_value;
					$_temp[$meta_name]=$meta_value;
				}
			}
			$cart_item["variation"]=array_diff_key( $cart_item["variation"], $_temp );
			$cart_item["variation"]=array_merge( $cart_item["variation"], $_variation_name_fix );
		}

		return apply_filters("wc_epo_adjust_cart_item",$cart_item);
	}

	/** Gets the cart from session. **/
	public function get_cart_item_from_session( $cart_item=array(), $values=array() ) {
		$this->is_get_from_session=true;
		if ( ! empty( $values['tmcartepo'] ) ) {
			$cart_item['tmcartepo'] = $values['tmcartepo'];
			$cart_item = $this->add_cart_item( $cart_item );
			if (empty($cart_item['addons'])){
				$cart_item['addons']=array("epo"=>true,'price'=>0);
			}
		}
		if ( ! empty( $values['tmcartepo_bto'] ) ) {
			$cart_item['tmcartepo_bto'] = $values['tmcartepo_bto'];
		}
		if ( ! empty( $values['tmsubscriptionfee'] ) ) {
			$cart_item['tmsubscriptionfee'] = $values['tmsubscriptionfee'];
		}
		if ( ! empty( $values['tmcartfee'] ) ) {
			$cart_item['tmcartfee'] = $values['tmcartfee'];
		}

		if ( ! empty( $values['tm_epo_product_original_price'] ) ) {
			$cart_item['tm_epo_product_original_price'] = $values['tm_epo_product_original_price'];
		}
		if ( ! empty( $values['tm_epo_options_prices'] ) ) {
			$cart_item['tm_epo_options_prices'] = $values['tm_epo_options_prices'];
		}
		if ( ! empty( $values['tm_epo_product_price_with_options'] ) ) {
			$cart_item['tm_epo_product_price_with_options'] = $values['tm_epo_product_price_with_options'];
		}
		
		return apply_filters('tm_cart_contents', $cart_item, $values);
	}

	/** Returns correct formated price for the cart table **/
	public function get_price_for_cart($price=0,$cart_item=array(),$symbol=false,$currencies=NULL,$quantity_divide=0,$quantity=0){
		global $woocommerce;
		$product 			= $cart_item['data'];
		$cart 				= $woocommerce->cart;
		$taxable 			= $product->is_taxable();
		$tax_display_cart 	= $cart->tax_display_cart;
		$tax_string="";

		if ($price===false){
			if (isset($cart_item['data']) && is_object($cart_item['data']) && property_exists($cart_item['data'], "price")){
				$price=$cart_item['data']->price;
			}else{
				$price=$product->price;
			}			
		}

		$price = apply_filters( 'woocommerce_tm_epo_price_on_cart',$price);
		// Taxable
		if ( $taxable ) {

			if ( $tax_display_cart == 'excl' ) {

				if ( $cart->tax_total > 0 && $cart->prices_include_tax ) {
					$tax_string = ' <small>' . WC()->countries->ex_tax_or_vat() . '</small>';
				}
				if (floatval($price)!=0){
					$price = $product->get_price_excluding_tax(1,$price);
				}

			} else {

				if ( $cart->tax_total > 0 && !$cart->prices_include_tax ) {
					$tax_string = ' <small>' . WC()->countries->inc_tax_or_vat() . '</small>';
				}
				if (floatval($price)!=0){
					$price = $product->get_price_including_tax(1,$price);
				}
			}

		}
		if ($symbol===false){
			if ($this->tm_epo_cart_field_display!="advanced"){
				$symbol="+";
			}
			if (floatval($price)<0){
				$symbol="-";
			}
		}
		
		if (!empty($quantity)){
			$price=floatval($price)*floatval($quantity);
		}

		if (floatval($price)==0){
			$symbol="";
		}else{
			$price = ' <span>' .( tc_price( abs($price) ) ). '</span>';
						
			$symbol=" $symbol" .$price.$tax_string."";

			if ($this->tm_epo_strip_html_from_emails=="yes"){
				$symbol=strip_tags($symbol);
			}
		}
		return $symbol;
	}

	/** Calculates the fee price **/
	public function cacl_fee_price($price="", $product_id="", $element=false, $attribute=""){
		global $woocommerce;
		$product 			= wc_get_product($product_id);
		$cart 				= $woocommerce->cart;
		$taxable 			= $product->is_taxable();
		$tax_display_cart 	= get_option( 'woocommerce_tax_display_shop' );
		$tax_string="";

		if ($element && isset($element['price_type']) && isset($_POST[$attribute])){
			switch($element['price_type']){
				case 'stepfee':
					$_price=floatval($price*floatval(stripcslashes($_POST[$attribute])));
					break;
				case 'currentstepfee':
					$price=floatval(stripcslashes($_POST[$attribute]));
					break;
			}
		}

		// Taxable
		if ( $taxable ) {

			if ( $tax_display_cart == 'excl' ) {

				if (floatval($price)!=0){
					$price = $product->get_price_excluding_tax(1,$price);
				}

			} else {

				if (floatval($price)!=0){
					$price = $product->get_price_including_tax(1,$price);
				}
			}

		}
		return $price;
	}

	/** Helper function for filtered_get_item_data **/
	private function filtered_get_item_data_get_array_data( $tmcp=array() ) {
		return array(
						'label' 				=> $tmcp['section_label'],
						'type' 					=> isset($tmcp['element']) && isset($tmcp['element']['type'])?$tmcp['element']['type']:'',
						'other_data' 			=> array( 
								array(
									'name'    	=> $tmcp['name'],
									'value'   	=> $tmcp['value'],
									'unit_price' => $tmcp['price'],
									'unit_price_per_currency' => (isset($tmcp['price_per_currency']))?$tmcp['price_per_currency']:array(),
									'display' 	=> isset( $tmcp['display'] ) ? $tmcp['display'] : '',
									'images' 	=> isset( $tmcp['images'] ) ? $tmcp['images'] : '',
									'quantity' 	=> isset($tmcp['quantity'])?$tmcp['quantity']:1
								) ),
						'price' 				=> $tmcp['price'],
						'currencies' 			=> isset($tmcp['currencies'])?$tmcp['currencies']:array(),
						'price_per_currency' 	=> isset($tmcp['price_per_currency'])?$tmcp['price_per_currency']:array(),
						'quantity' 				=> isset($tmcp['quantity'])?$tmcp['quantity']:1,
						'percentcurrenttotal' 	=> isset($tmcp['percentcurrenttotal'])?$tmcp['percentcurrenttotal']:0,
						'items' 				=> 1,
						'multiple_values' 		=> isset($tmcp['multiple_values'])?$tmcp['multiple_values']:'',
						'hidelabelincart' 		=> isset($tmcp['hidelabelincart'])?$tmcp['hidelabelincart']:'',
						'hidevalueincart' 		=> isset($tmcp['hidevalueincart'])?$tmcp['hidevalueincart']:'',
					);
	}

	/** Filters our cart items. **/
	private function filtered_get_item_data( $cart_item=array() ) {
		$to_currency = tc_get_woocommerce_currency();
		$filtered_array=array();
		if (isset($cart_item['tmcartepo'] ) && is_array($cart_item['tmcartepo'])){
			foreach ( $cart_item['tmcartepo'] as $tmcp ) {
				if($tmcp){

					if(isset($tmcp['price_per_currency']) && isset($tmcp['price_per_currency'][$to_currency]) && $tmcp['price_per_currency'][$to_currency]!==''){
						$tmcp['price']=(float)wc_format_decimal($tmcp['price_per_currency'][$to_currency],false,true);
					}else{
						$tmcp['price']=(float)wc_format_decimal($tmcp['price'],false,true);
						$tmcp['price']= apply_filters( 'wc_epo_get_current_currency_price', $tmcp['price'], isset($tmcp['element'])?$tmcp['element']['rules_type'][isset($tmcp['key'])?$tmcp['key']:0][0]:'');
					}

					if ( !isset( $filtered_array[$tmcp['section']] ) ) {
						$filtered_array[$tmcp['section']]=$this->filtered_get_item_data_get_array_data( $tmcp );
					}else {
						if ($this->tm_epo_cart_field_display=="advanced" || $this->tm_epo_cart_field_display=="link"){
							$filtered_array[$tmcp['section']."_".TM_EPO_HELPER()->tm_uniqid()]=$this->filtered_get_item_data_get_array_data( $tmcp );
						}else{						
							$filtered_array[$tmcp['section']]['items'] +=1;
							$filtered_array[$tmcp['section']]['price'] +=$tmcp['price'];
							
							if(isset($tmcp['price_per_currency'])){
								$filtered_array[$tmcp['section']]['price_per_currency'] = TM_EPO_HELPER()->add_array_values( $filtered_array[$tmcp['section']]['price_per_currency'] , $tmcp['price_per_currency'] );	
							}
							
							$filtered_array[$tmcp['section']]['quantity'] += isset($tmcp['quantity'])?$tmcp['quantity']:1;
							$filtered_array[$tmcp['section']]['other_data'][] =  array(
								'name'    	=> $tmcp['name'],
								'value'   	=> $tmcp['value'],
								'unit_price' => $tmcp['price'],
								'unit_price_per_currency' => (isset($tmcp['price_per_currency']))?$tmcp['price_per_currency']:array(),
								'display' 	=> isset( $tmcp['display'] ) ? $tmcp['display'] : '',
								'images' 	=> isset( $tmcp['images'] ) ? $tmcp['images'] : '',
								'quantity' 	=> isset( $tmcp['quantity'] )?$tmcp['quantity']:1,
							);
						}
					}
				}
			}
		}

		return $filtered_array;
	}

	/** Return formatted cart items **/
	public function get_item_data_array( $other_data, $cart_item=array() ) {
		$filtered_array=$this->filtered_get_item_data( $cart_item );
		$price=0;
		$link_data=array();
		$quantity=$cart_item['quantity'];
		if (is_array($filtered_array)){
			foreach ( $filtered_array as $section ) {
				$value=array();
				$quantity_string_shown=false;
				$format_price_shown=false;
				$do_unique_values=false;
				$prev_unit_price=false;
				$prev_unit_quantity=false;
				$dont_show_mass_quantity=false;
				$format_price="";
				if (isset($section['other_data'] ) && is_array($section['other_data'] )){
					foreach ( $section['other_data'] as $key=>$data ) {
						$display_value = ! empty( $data['display'] ) ? $data['display'] : $data['value'];
						
						if (!empty($data['images']) && $this->tm_epo_show_image_replacement=="yes"){
							if ($this->tm_epo_hide_options_prices_in_cart=="normal"){
								$original_price=$data['unit_price']/$data['quantity'];
								$new_price=apply_filters('wc_epo_discounted_price', $data['unit_price'], $cart_item['data'], $cart_item[$this->cart_edit_key_var]);
								$after_price=$new_price/$data['quantity'];
								$format_price=$this->get_price_for_cart( $after_price ,$cart_item,false,$data['unit_price_per_currency'],$data['quantity']);
								
								if ($original_price!=$after_price){
									$original_price=$this->get_price_for_cart($original_price,$cart_item,false,$data['unit_price_per_currency'],$data['quantity']);
									$format_price = '<span class="rp_wcdpd_cart_price"><del>' . $original_price . '</del> <ins>' . $format_price . '</ins></span>';
								}
								$format_price_shown=true;
							}else{
								$format_price='';
							}
							$quantity_string = ($data['quantity']>1)?' &times; '.$data['quantity']:'';
							$display_value ='<span class="cpf-img-on-cart"><img alt="" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="'.
								apply_filters("tm_image_url",$data['images']).'" />'.$display_value. '<small>'.$format_price . $quantity_string . '</small></span>';
							$quantity_string_shown=true;
						}else{
							if($prev_unit_quantity===false){
								$prev_unit_quantity=$data['quantity'];
							}
							if($prev_unit_price===false){
								$prev_unit_price=$data['unit_price'];
							}
							elseif($prev_unit_price!==$data['unit_price'] || $prev_unit_quantity!=$data['quantity'] || $data['quantity']>1){
								$do_unique_values=true;
								if($this->tm_epo_hide_options_prices_in_cart=="normal"){
									break;
								}else{
									$dont_show_mass_quantity=true;
								}
							}
							$prev_unit_price=$data['unit_price'];
							$prev_unit_quantity=$data['quantity'];

						}
						if ($section['type'] == "upload"){
							$check = wp_check_filetype( $data['value'] );
							if ( !empty( $check['ext'] ) ) {
	                			$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );
	                			if ( in_array( $check['ext'], $image_exts ) ){
	                				$display_value ='<span class="cpf-img-on-cart"><img alt="" class="attachment-shop_thumbnail wp-post-image epo-option-image epo-upload-image" src="'.
										apply_filters("tm_image_url",$data['value']).'" /><span>';
	                			}
	        				}
						}
						$value[]=$display_value;
					}

					if (!empty($section['multiple_values'])){
						$do_unique_values=true;
					}

					if($do_unique_values){
						$value=array();
						foreach ( $section['other_data'] as $key=>$data ) {
							$display_value = ! empty( $data['display'] ) ? $data['display'] : $data['value'];
							$original_price=$data['unit_price']/$data['quantity'];
							$new_price=apply_filters('wc_epo_discounted_price', $data['unit_price'], $cart_item['data'], $cart_item[$this->cart_edit_key_var]);
							$after_price=$new_price/$data['quantity'];
							$format_price=$this->get_price_for_cart( $after_price ,$cart_item,false,$data['unit_price_per_currency'],$data['quantity']);
								
							if ($original_price!=$after_price){
								$original_price=$this->get_price_for_cart($original_price,$cart_item,false,$data['unit_price_per_currency'],$data['quantity']);
								$format_price = '<span class="rp_wcdpd_cart_price"><del>' . $original_price . '</del> <ins>' . $format_price . '</ins></span>';
							}
							$format_price_shown=true;
							$quantity_string = ($data['quantity']>1)?' &times; '.$data['quantity']:'';
							if( $this->tm_epo_hide_options_prices_in_cart!="normal"){
								$format_price='';
							}
							if (!empty($section['multiple_values'])){
								$display_value_array=explode($section['multiple_values'],$display_value);
								$display_value="";
								foreach ($display_value_array as $d => $dv) {
									$display_value .='<span class="cpf-data-on-cart">'.$dv.'</span>';
								}
								$display_value .=' <small>'.$format_price . $quantity_string . '</small>';
							}else{
								$display_value ='<span class="cpf-data-on-cart">'.$display_value.' <small>'.$format_price . $quantity_string . '</small></span>';	
							}							
							$quantity_string_shown=true;
							$value[]=$display_value;
						}
					}
				}

				if ( !empty( $value ) && count( $value )>0 ) {
					if($quantity_string_shown){						
						if (is_array($value[0])){
							$temp='';
							foreach ($value as $k => $v) {
								$temp .=implode( "", $v );
							}
							$value=$temp;
						}else{
							$value=implode( "", $value );
						}
					}else{
						if (is_array($value[0])){
							$temp='';
							foreach ($value as $k => $v) {
								$temp .=implode( " , ", $v );
							}
							$value=$temp;
						}else{
							if (!empty($section['multiple_values'])){
								
								$value=implode( " , ", $value );
								
							}else{
								$value=implode( " , ", $value );
							}							
						}
						
					}					
				}else {
					$value="";
				}

				if (empty($section['quantity'])){
					$section['quantity']=1;
				}

				// WooCommerce Dynamic Pricing & Discounts
				$original_price=$section['price']/$section['quantity'];
				$original_price_q=$original_price*$quantity*$section['quantity'];

				$section['price']=apply_filters('wc_epo_discounted_price', $section['price'], $cart_item['data'], $cart_item[$this->cart_edit_key_var] );
				$after_price=$section['price']/$section['quantity'];
					
				$price=$price+(float)$section['price'];
				
				if ($this->tm_epo_hide_options_prices_in_cart=="normal"){
					$format_price=$this->get_price_for_cart( $after_price ,$cart_item,false,$section['price_per_currency'],$section['quantity']);
					$format_price_total=$this->get_price_for_cart($section['price'],$cart_item,false,$section['price_per_currency'],0,$quantity);
					$format_price_total2=$this->get_price_for_cart($section['price'] ,$cart_item,false,$section['price_per_currency']);
					if ($original_price!=$after_price){
						$original_price=$this->get_price_for_cart($original_price,$cart_item,false,$section['price_per_currency']);
						$original_price_total=$this->get_price_for_cart($original_price_q,$cart_item,false,$section['price_per_currency']);
						$format_price = '<span class="rp_wcdpd_cart_price"><del>' . $original_price . '</del> <ins>' . $format_price . '</ins></span>';
					}
				}else{
					$format_price='';
					$format_price_total='';
					$format_price_total2='';
				}
				$single_price=$this->get_price_for_cart((float)$section['price']/$section['quantity'],$cart_item,false,$section['price_per_currency']);
				$quantity_string = ($section['quantity']>1)?' &times; '.$section['quantity']:'';
				if ($quantity_string_shown || $dont_show_mass_quantity){
					$quantity_string="";
				}			
				if ($this->tm_epo_cart_field_display!="link"){
					if (empty($section['hidelabelincart']) || empty($section['hidevalueincart'])){
						$other_data[] = array(
							'name'    			=> empty($section['hidelabelincart'])?$section['label']:'',
							'value'   			=> empty($section['hidevalueincart'])
													?
														do_shortcode(TM_EPO_HELPER()->html_entity_decode($value)). 
														((!$format_price_shown && $format_price && $quantity_string)
															?'<small>'.$format_price.$quantity_string . '</small>'
															:
																(
																	($format_price)
																	?'<span class="tc-average-price">'.$format_price.'<span>'
																	:'' 
																)
														)
													:'',
							'tm_label' 			=> $section['label'],
							'tm_value' 			=> do_shortcode(TM_EPO_HELPER()->html_entity_decode($value)),
							'tm_price' 			=> $format_price,
							'tm_total_price' 	=> $format_price_total,
							'tm_quantity' 		=> $section['quantity'],
							'tm_image' 			=> $section['other_data'][0]['images'],
						);
					}
				}
				if (empty($section['hidelabelincart']) || empty($section['hidevalueincart'])){
					$link_data[] = array(
						'name'    			=> empty($section['hidelabelincart'])?$section['label']:'',
						'value'   			=> empty($section['hidevalueincart'])?$value:'',
						'price'   			=> $format_price,
						'tm_price' 			=> $single_price,
						'tm_total_price' 	=> $format_price_total,
						'tm_quantity' 		=> $section['quantity'],
						'tm_total_price2' 	=> $format_price_total2
					);
				}
			}
		}

		if ($this->tm_epo_cart_field_display=="link"){
			if (empty($price) || $this->tm_epo_hide_options_prices_in_cart!="normal"){
				$price='';
			}else{
				$price=$this->get_price_for_cart($price,$cart_item,false);
			}
			$uni=uniqid('');
			$data='<div class="tm-extra-product-options">';
				$data .= '<div class="tm-row tm-cart-row">'
						. '<div class="tm-cell col-4 cpf-name">&nbsp;</div>'
						. '<div class="tm-cell col-4 cpf-value">&nbsp;</div>'
						. '<div class="tm-cell col-2 cpf-price">'.__( 'Price', 'woocommerce' ).'</div>'
						. '<div class="tm-cell col-1 cpf-quantity">'.__( 'Quantity', 'woocommerce' ).'</div>'
						. '<div class="tm-cell col-1 cpf-total-price">'.__( 'Total', 'woocommerce' ).'</div>'
						. '</div>';
				foreach ( $link_data as $link ) {
				$data .= '<div class="tm-row tm-cart-row">'
						. '<div class="tm-cell col-4 cpf-name">'.$link['name'].'</div>'
						. '<div class="tm-cell col-4 cpf-value">'.do_shortcode(TM_EPO_HELPER()->html_entity_decode($link['value'])).'</div>'
						. '<div class="tm-cell col-2 cpf-price">'.$link['tm_price'].'</div>'
						. '<div class="tm-cell col-1 cpf-quantity">'.(($link['tm_price']=='')?'':$link['tm_quantity']).'</div>'
						. '<div class="tm-cell col-1 cpf-total-price">'.$link['tm_total_price2'].'</div>'
						. '</div>';

			}
			$data .='</div>';
			$other_data[]=array(
					
					'name' 	=> '<a href="#tm-cart-link-data-'.$uni.'" class="tm-cart-link">'.((!empty($this->tm_epo_additional_options_text))?$this->tm_epo_additional_options_text:__( 'Additional options', 'woocommerce-tm-extra-product-options' )).'</a>',
					'value' => $price.'<div id="tm-cart-link-data-'.$uni.'" class="tm-cart-link-data tm-hidden">'.$data.'</div>'
					
				);
		}
		return $other_data;
	}

	/** Gets cart item to display in the frontend. **/
	public function get_item_data( $other_data, $cart_item ) {

		if ( $this->tm_epo_hide_options_in_cart=="normal" &&  $this->tm_epo_cart_field_display!="advanced" && !empty( $cart_item['tmcartepo'] ) ) {

			$other_data = $this->get_item_data_array( $other_data, $cart_item );

		}	
		
		return $other_data;
	}

	/** Calculates the correct option price **/
	public function calculate_price( $post_data=null, $element, $key, $attribute, $per_product_pricing, $cpf_product_price=false, $variation_id,$price_default_value=0,$currency=false,$current_currency=false ) {
		if ( is_null( $post_data ) && isset( $_POST ) ) {
			$post_data = $_POST;
		}
		$_price=$price_default_value;
		$_price_type="";
		// this currently happens for multiple file uploads
		if (is_array($key)){
			$key=0;
		}
		$key=esc_attr($key);
		if ($per_product_pricing){

			if ( !isset( $element['price_rules'][$key] ) ) {// field price rule
				if ( $variation_id && isset( $element['price_rules'][0][$variation_id] ) ) {// general variation rule
					$_price=$element['price_rules'][0][$variation_id];
				}elseif ( isset( $element['price_rules'][0][0] ) ) {// general rule
					$_price=$element['price_rules'][0][0];
				}
			}else {
				if ( $variation_id && isset( $element['price_rules'][$key][$variation_id] ) ) {// field price rule
					$_price=$element['price_rules'][$key][$variation_id];
				}elseif ( isset( $element['price_rules'][$key][0] ) ) {// general field variation rule
					$_price=$element['price_rules'][$key][0];
				}elseif ( $variation_id && isset( $element['price_rules'][0][$variation_id] ) ) {// general variation rule
					$_price=$element['price_rules'][0][$variation_id];
				}elseif ( isset( $element['price_rules'][0][0] ) ) {// general rule
					$_price=$element['price_rules'][0][0];
				}
			}

			if ( !isset( $element['price_rules_type'][$key] ) ) {// field price rule
				if ( $variation_id && isset( $element['price_rules_type'][0][$variation_id] ) ) {// general variation rule
					$_price_type=$element['price_rules_type'][0][$variation_id];
				}elseif ( isset( $element['price_rules_type'][0][0] ) ) {// general rule
					$_price_type=$element['price_rules_type'][0][0];
				}
			}else {
				if ( $variation_id && isset( $element['price_rules_type'][$key][$variation_id] ) ) {// field price rule
					$_price_type=$element['price_rules_type'][$key][$variation_id];
				}elseif ( isset( $element['price_rules_type'][$key][0] ) ) {// general field variation rule
					$_price_type=$element['price_rules_type'][$key][0];
				}elseif ( $variation_id && isset( $element['price_rules_type'][0][$variation_id] ) ) {// general variation rule
					$_price_type=$element['price_rules_type'][0][$variation_id];
				}elseif ( isset( $element['price_rules_type'][0][0] ) ) {// general rule
					$_price_type=$element['price_rules_type'][0][0];
				}
			}
			$_price= wc_format_decimal( $_price, false, true );

			switch ($_price_type) {
				case 'percent':
					if ($cpf_product_price!==false){
						$cpf_product_price=apply_filters( 'wc_epo_remove_current_currency_price',$cpf_product_price,"percent");
						if ($currency){
							$cpf_product_price=apply_filters( 'wc_epo_get_currency_price',$cpf_product_price,$currency,"percent",false,$current_currency);
						}
						$_price=($_price/100)*floatval($cpf_product_price);
					}
					break;
				case 'percentcurrenttotal':
					if ($_price!='' && isset($post_data[$attribute.'_hidden'])){
						$_price=floatval($post_data[$attribute.'_hidden']);
						$_price=apply_filters( 'wc_epo_remove_current_currency_price',$_price,"percentcurrenttotal");
						if ($currency){
							$_price=apply_filters( 'wc_epo_get_currency_price',$_price,$currency,"percentcurrenttotal",false,$current_currency);
						}
						if (isset($post_data[$attribute.'_quantity']) && $post_data[$attribute.'_quantity']>0){
							$_price=$_price/floatval($post_data[$attribute.'_quantity']);
						}
					}
					break;
				case 'char':
					$_price=floatval($_price*strlen(stripcslashes( utf8_decode($post_data[$attribute]) )));
					if ($currency){
						$_price=apply_filters( 'wc_epo_get_currency_price',$_price,$currency,$_price_type,false,$current_currency);
					}
					break;
				case 'charpercent':
					if ($cpf_product_price!==false){
						$cpf_product_price=apply_filters( 'wc_epo_remove_current_currency_price',$cpf_product_price,"percent");
						if ($currency){
							$cpf_product_price=apply_filters( 'wc_epo_get_currency_price',$cpf_product_price,$currency,"charpercent",false,$current_currency);
						}
						$_price=floatval(strlen(stripcslashes( utf8_decode($post_data[$attribute]) )))*(($_price/100)*floatval($cpf_product_price));
					}
					break;
				case 'charnofirst':
					$_textlength=floatval(strlen(stripcslashes( utf8_decode($post_data[$attribute]) )))-1;
					if ($_textlength<0){
						$_textlength=0;
					}
					$_price=floatval($_price*$_textlength);
					if ($currency){
						$_price=apply_filters( 'wc_epo_get_currency_price',$_price,$currency,$_price_type,false,$current_currency);
					}
					break;

				case 'charnon':
					$freechars=absint($element['freechars']);
					$_textlength=floatval(strlen(stripcslashes( utf8_decode($post_data[$attribute]) )))-$freechars;
					if ($_textlength<0){
						$_textlength=0;
					}
					$_price=floatval($_price*$_textlength);
					if ($currency){
						$_price=apply_filters( 'wc_epo_get_currency_price',$_price,$currency,$_price_type,false,$current_currency);
					}
					break;
				case 'charpercentnon':
					if ($cpf_product_price!==false){
						$freechars=absint($element['freechars']);
						$cpf_product_price=apply_filters( 'wc_epo_remove_current_currency_price',$cpf_product_price,"percent");
						if ($currency){
							$cpf_product_price=apply_filters( 'wc_epo_get_currency_price',$cpf_product_price,$currency,"charpercentnon",false,$current_currency);
						}
						$_textlength=floatval(strlen(stripcslashes( utf8_decode($post_data[$attribute]) )))-$freechars;
						if ($_textlength<0){
							$_textlength=0;
						}
						$_price=floatval($_textlength)*(($_price/100)*floatval($cpf_product_price));
					}
					break;
				case 'charnonnospaces':
					$freechars=absint($element['freechars']);
					$_textlength=floatval( strlen( preg_replace( "/\s+/", "", stripcslashes( utf8_decode( $post_data[$attribute] ) ) ) ) )-$freechars;
					if ($_textlength<0){
						$_textlength=0;
					}
					$_price=floatval($_price*$_textlength);
					if ($currency){
						$_price=apply_filters( 'wc_epo_get_currency_price',$_price,$currency,$_price_type,false,$current_currency);
					}
					break;
				case 'charpercentnonnospaces':
					if ($cpf_product_price!==false){
						$freechars=absint($element['freechars']);
						$cpf_product_price=apply_filters( 'wc_epo_remove_current_currency_price',$cpf_product_price,"percent");
						if ($currency){
							$cpf_product_price=apply_filters( 'wc_epo_get_currency_price',$cpf_product_price,$currency,"charpercentnonnospaces",false,$current_currency);
						}
						$_textlength=floatval( strlen( preg_replace( "/\s+/", "", stripcslashes( utf8_decode( $post_data[$attribute] ) ) ) ) )-$freechars;
						if ($_textlength<0){
							$_textlength=0;
						}
						$_price=floatval($_textlength)*(($_price/100)*floatval($cpf_product_price));
					}
					break;

				case 'charnospaces':
				 	$_price=floatval($_price * strlen(preg_replace("/\s+/", "", stripcslashes( utf8_decode($post_data[$attribute]) )))); 
					if ($currency){
						$_price=apply_filters( 'wc_epo_get_currency_price',$_price,$currency,$_price_type,false,$current_currency);
					}
					break;
				case 'charpercentnofirst':
					if ($cpf_product_price!==false){
						$cpf_product_price=apply_filters( 'wc_epo_remove_current_currency_price',$cpf_product_price,"percent");
						if ($currency){
							$cpf_product_price=apply_filters( 'wc_epo_get_currency_price',$cpf_product_price,$currency,"charpercentnofirst",false,$current_currency);
						}
						$_textlength=floatval(strlen(stripcslashes( utf8_decode($post_data[$attribute]) )))-1;
						if ($_textlength<0){
							$_textlength=0;
						}
						$_price=floatval($_textlength)*(($_price/100)*floatval($cpf_product_price));
					}
					break;
				case 'step':
				case 'stepfee':
					$_price=floatval($_price*floatval(stripcslashes($post_data[$attribute])));
					if ($currency){
						$_price=apply_filters( 'wc_epo_get_currency_price',$_price,$currency,$_price_type,false,$current_currency);
					}
					break;
				case 'currentstep':
				case 'currentstepfee':
					$_price=floatval(stripcslashes($post_data[$attribute]));
					if ($currency){
						$_price=apply_filters( 'wc_epo_get_currency_price',$_price,$currency,$_price_type,false,$current_currency);
					}
					break;
				case 'intervalstep':
					if (isset($element["min"])){
						$_min=floatval($element["min"]);
						$_price=floatval($_price* (floatval(stripcslashes($post_data[$attribute]))-$_min) );
						if ($currency){
							$_price=apply_filters( 'wc_epo_get_currency_price',$_price,$currency,$_price_type,false,$current_currency);
						}
					}					
					break;
				case 'row':
					$_price=floatval( $_price* (substr_count(stripcslashes( utf8_decode($post_data[$attribute])),"\r\n")+1) );
					if ($currency){
						$_price=apply_filters( 'wc_epo_get_currency_price',$_price,$currency,$_price_type,false,$current_currency);
					}
					break;
				default:
					// fixed price
					if ($currency){
						$_price=apply_filters( 'wc_epo_get_currency_price',$_price,$currency,$_price_type,false,$current_currency);
					}
					break;
			}

			// quantity button
			if (isset($post_data[$attribute.'_quantity'])){
				$_price=$_price*floatval($post_data[$attribute.'_quantity']);
			}

			if ($price_default_value==='' && $_price==0){
				$_price='';
			}

		}

		return apply_filters('tm_wcml_raw_price_amount',$_price);
	}

	/** Adds meta data to the order. **/
	public function order_item_meta( $item_id, $values ) {
		if ( ! empty( $values['tmcartepo'] ) ) {			
			wc_add_order_item_meta( $item_id, '_tmcartepo_data', $values['tmcartepo'] );
			wc_add_order_item_meta( $item_id, '_tm_epo_product_original_price', array($values['tm_epo_product_original_price']) );
			wc_add_order_item_meta( $item_id, '_tm_epo', array(1) );
		}
		if ( ! empty( $values['tmsubscriptionfee'] ) ) {
			wc_add_order_item_meta( $item_id, '_tmsubscriptionfee_data', array($values['tmsubscriptionfee']) );
			wc_add_order_item_meta( $item_id, __("Options Subscription fee",'woocommerce-tm-extra-product-options'), $values['tmsubscriptionfee'] );
		}
		if ( ! empty( $values['tmcartfee'] ) ) {
			wc_add_order_item_meta( $item_id, '_tmcartfee_data', array($values['tmcartfee']) );
		}
		do_action('wc_epo_order_item_meta',$item_id, $values);
	}

	/** Validates the cart data. **/
	public function add_to_cart_validation( $passed, $product_id, $qty, $variation_id = '', $variations = array(), $cart_item_data = array() ) {
		// disables add_to_cart_button class on shop page
		if (is_ajax() && $this->tm_epo_force_select_options=="display" && !isset($_REQUEST['tcaddtocart'])){
			
			$cpf=$this->get_product_tm_epos($product_id);

			if (is_array($cpf) && (!empty($cpf['global']) || !empty($cpf['local']))) {
				return false;
			}
		 
		}

		$is_validate=true;

		// Get product type
		$terms 			= get_the_terms( $product_id, 'product_type' );
		$product_type 	= ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';
		if ( $product_type == 'bto' || $product_type == 'composite') {

			$bto_data 	= maybe_unserialize( get_post_meta( $product_id, '_bto_data', true ) );
			$valid_ids 	= array();
			if (is_array($bto_data )){
				$valid_ids 	= array_keys( $bto_data );
			}
			foreach ( $valid_ids as $bundled_item_id ) {

				if ( isset( $_REQUEST[ 'add-product-to-cart' ][ $bundled_item_id ] ) && $_REQUEST[ 'add-product-to-cart' ][ $bundled_item_id ] !== '' ) {
					$bundled_product_id = $_REQUEST[ 'add-product-to-cart' ][ $bundled_item_id ];
				} elseif ( isset( $cart_item_data[ 'composite_data' ][ $bundled_item_id ][ 'product_id' ] ) && isset( $_GET[ 'order_again' ] ) ) {
					$bundled_product_id = $cart_item_data[ 'composite_data' ][ $bundled_item_id ][ 'product_id' ];
				}

				if (isset($bundled_product_id) && !empty($bundled_product_id)){

					$_passed=true;

					if ( isset( $_REQUEST[ 'item_quantity' ][ $bundled_item_id ] ) && is_numeric( $_REQUEST[ 'item_quantity' ][ $bundled_item_id ] ) ) {
						$item_quantity = absint( $_REQUEST[ 'item_quantity' ][ $bundled_item_id ] );
					} elseif ( isset( $cart_item_data[ 'composite_data' ][ $bundled_item_id ][ 'quantity' ] ) && isset( $_GET[ 'order_again' ] ) ) {
						$item_quantity = $cart_item_data[ 'composite_data' ][ $bundled_item_id ][ 'quantity' ];
					}
					if ( !empty($item_quantity)){
						$item_quantity = absint( $item_quantity );
						
						$_passed = $this->validate_product_id( $bundled_product_id, $item_quantity, $bundled_item_id );
						
					}

					if (!$_passed){
						$is_validate=false;
					}
					
				}
			}
		}

		if (!$this->validate_product_id( $product_id, $qty )){
			$passed=false;
		}

		// Try to validate uploads before they happen
		$files=array();
		foreach ( $_FILES as $k=>$file){
			if (!empty($file['name'])){
				$file_name=$file['name'];
				if(!empty($file['error'])){
					$file_error=$file['error'];
					
					// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
					$upload_error_strings = array( false,
						__( "The uploaded file exceeds the upload_max_filesize directive in php.ini.", 'woocommerce-tm-extra-product-options'  ),
						__( "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.", 'woocommerce-tm-extra-product-options'  ),
						__( "The uploaded file was only partially uploaded.", 'woocommerce-tm-extra-product-options'  ),
						__( "No file was uploaded.", 'woocommerce-tm-extra-product-options'  ),
						'',
						__( "Missing a temporary folder.", 'woocommerce-tm-extra-product-options'  ),
						__( "Failed to write file to disk.", 'woocommerce-tm-extra-product-options'  ),
						__( "File upload stopped by extension.", 'woocommerce-tm-extra-product-options'  ));

					if (is_array($file_error)){
						foreach ($file_error as $key => $value) {
							if(!empty($value) && !empty($file_name[$key])){
								$passed=false;
								if (isset($upload_error_strings[$value])){
									wc_add_notice( $upload_error_strings[$value] , 'error' );
								}
							}								
						}
					}else{
						$passed=false;
						if (isset($upload_error_strings[$file_error])){
							wc_add_notice( $upload_error_strings[$file_error] , 'error' );
						}
					}
					
				}
				
				if (is_array($file_name)){
					foreach ($file_name as $key => $value) {
						if (!empty($value)){
							$check_filetype=wp_check_filetype( $value ) ;
							$check_filetype=$check_filetype['ext'];
							if (!$check_filetype && !empty($file['name'])){
								$passed=false;
								wc_add_notice( __( "Sorry, this file type is not permitted for security reasons.", 'woocommerce-tm-extra-product-options'  ). ' ('.pathinfo( $value, PATHINFO_EXTENSION ) .')' , 'error' );
							}
						}
					}
				}else{
					$check_filetype=wp_check_filetype( $file['name'] ) ;
					$check_filetype=$check_filetype['ext'];
					if (!$check_filetype && !empty($file['name'])){
						$passed=false;
						wc_add_notice( __( "Sorry, this file type is not permitted for security reasons.", 'woocommerce-tm-extra-product-options'  ). ' ('.pathinfo( $file['name'], PATHINFO_EXTENSION ) .')' , 'error' );
					}
				}
				
			}			

		}

		if (!$is_validate){
			$passed=false;
		}		

		return apply_filters('tm_add_to_cart_validation',$passed);

	}

	/** Conditional logic (checks if an element is visible) **/
	public function is_visible($element=array(), $section=array(), $sections=array() , $form_prefix=""){
		
		// Element
		$logic = false;
		if (isset($element['section'])){
			if(!$this->is_visible($section, array(), $sections, $form_prefix)){
				return false;
			}
			if (!isset($element['logic']) || empty($element['logic'])){
				return true;
			}
			$logic= (array) json_decode($element['clogic']) ;
		// Section
		}else{
			if (!isset($element['sections_logic']) || empty($element['sections_logic'])){
				return true;
			}
			$logic= (array) json_decode($element['sections_clogic']);
		}

		if ($logic){
			$rule_toggle=$logic['toggle'];
			$rule_what=$logic['what'];
			$matches = 0;
			$checked = 0;
			$show=true;
			switch ($rule_toggle){
				case "show":
                	$show=false;
					break;
				case "hide":
					$show=true;
					break;
			}
			
			foreach($logic['rules'] as $key=>$rule){
				$matches++;

				if ($this->tm_check_field_match($rule, $sections, $form_prefix)){
                    $checked++;
                }
				
			}
			
			if ($rule_what=="all"){
				if ($checked==$matches){
					$show=!$show;
				}
			}else{
				if ($checked>0){
					$show=!$show;
				}
			}
			return $show;

		}

		return false;
	}

	/** Conditional logic **/
	public function tm_check_section_match($element_id,$operator,$rule=false, $sections=false, $form_prefix=""){
		$all_checked=true;
		$section_id=$element_id;
		if (isset($sections[$section_id]) && isset($sections[$section_id]['elements'])){
			foreach ($sections[$section_id]['elements'] as $id => $element) {
				if ($this->is_visible($element, $sections[$section_id], $sections, $form_prefix)){
					$element_to_check = $sections[$section_id]['elements'][$id]['name_inc'];
					$element_type = $sections[$section_id]['elements'][$id]['type'];
					$posted_value = null;

					switch ($element_type){
						case "radio":
							$radio_checked_length=0;
							$element_to_check=array_unique($element_to_check);

							$element_to_check=$element_to_check[0].$form_prefix;
					
							if (isset($_POST[$element_to_check])){
								$radio_checked_length++;
								$posted_value = $_POST[$element_to_check];
								$posted_value = stripslashes( $posted_value );
								$posted_value=TM_EPO_HELPER()->encodeURIComponent($posted_value);
								$posted_value=TM_EPO_HELPER()->reverse_strrchr($posted_value,"_");
							}
							if ($operator=='isnotempty'){
								$all_checked=$all_checked && $radio_checked_length>0;
							}else if ($operator=='isempty'){
								$all_checked=$all_checked && $radio_checked_length==0;
							} 
						break;
						case "checkbox":
							$checkbox_checked_length=0;
							
							$element_to_check=array_unique($element_to_check);
							foreach ($element_to_check as $key => $name_value) {
								$element_to_check[$key]=$name_value.$form_prefix;	
								if (isset($_POST[$element_to_check[$key]])){
									$checkbox_checked_length++;
									$posted_value=$_POST[$element_to_check[$key]];
									$posted_value = stripslashes( $posted_value );
									$posted_value=TM_EPO_HELPER()->encodeURIComponent($posted_value);
									$posted_value=TM_EPO_HELPER()->reverse_strrchr($posted_value,"_");
								}
								
							}
							if ($operator=='isnotempty'){
								$all_checked=$all_checked && $checkbox_checked_length>0;
							}else if ($operator=='isempty'){
								$all_checked=$all_checked && $checkbox_checked_length==0;
							} 
						break;
						case "select":
						case "textarea":
						case "textfield":
							$element_to_check .=$form_prefix;
							if (isset($_POST[$element_to_check])){
								$posted_value = $_POST[$element_to_check];
								$posted_value = stripslashes( $posted_value );
								if ($element_type=="select"){
									$posted_value=TM_EPO_HELPER()->encodeURIComponent($posted_value);
									$posted_value=TM_EPO_HELPER()->reverse_strrchr($posted_value,"_");
								}
							}
						break;
					}
					$all_checked=$all_checked && $this->tm_check_match($posted_value,'',$operator);
				}
			}
		}
		return $all_checked;
	}

	/** Conditional logic **/
	public function tm_check_field_match($rule=false, $sections=false, $form_prefix=""){
		if (empty($rule) || empty($sections)){
			return false;
		}

		$section_id=$rule->section;
		$element_id=$rule->element;
		$operator=$rule->operator;
		$value=$rule->value;

		if ($section_id==$element_id){
			return $this->tm_check_section_match($element_id,$operator,$rule, $sections, $form_prefix);
		}
		if (!isset($sections[$section_id]) 
			|| !isset($sections[$section_id]['elements']) 
			|| !isset($sections[$section_id]['elements'][$element_id]) 
			|| !isset($sections[$section_id]['elements'][$element_id]['type']) 
			){
			return false;
		}

		// variations logic		
		if ($sections[$section_id]['elements'][$element_id]['type']=="variations"){
			return $this->tm_variation_check_match($form_prefix,$value,$operator);
		}

		if (!isset($sections[$section_id]['elements'][$element_id]['name_inc'])){
			return false;
		}

		// element array cannot hold the form_prefix for bto support, so we append manually
		$element_to_check = $sections[$section_id]['elements'][$element_id]['name_inc'];		
		$element_type = $sections[$section_id]['elements'][$element_id]['type'];
		$posted_value = null;

		switch ($element_type){
			case "radio":
				$radio_checked_length=0;
				$element_to_check=array_unique($element_to_check);

				$element_to_check=$element_to_check[0].$form_prefix;
		
				if (isset($_POST[$element_to_check])){
					$radio_checked_length++;
					$posted_value = $_POST[$element_to_check];
					$posted_value = stripslashes( $posted_value );
					$posted_value=TM_EPO_HELPER()->encodeURIComponent($posted_value);
					$posted_value=TM_EPO_HELPER()->reverse_strrchr($posted_value,"_");
				}
				if ($operator=='is' || $operator=='isnot'){
					if ($radio_checked_length==0){
						return false;
					}
				}else if ($operator=='isnotempty'){
					return $radio_checked_length>0;
				}else if ($operator=='isempty'){
					return $radio_checked_length==0;
				} 
			break;
			case "checkbox":
				$checkbox_checked_length=0;
				$ret=false;
				$element_to_check=array_unique($element_to_check);
				foreach ($element_to_check as $key => $name_value) {
					$element_to_check[$key]=$name_value.$form_prefix;	
					if (isset($_POST[$element_to_check[$key]])){
						$checkbox_checked_length++;
						$posted_value=$_POST[$element_to_check[$key]];
						$posted_value = stripslashes( $posted_value );
						$posted_value=TM_EPO_HELPER()->encodeURIComponent($posted_value);
						$posted_value=TM_EPO_HELPER()->reverse_strrchr($posted_value,"_");
					}
					if ($this->tm_check_match($posted_value,$value,$operator)){
						$ret=true;
					}
				}
				if ($operator=='is' || $operator=='isnot'){
					if ($checkbox_checked_length==0){
						return false;
					}
					return $ret;
				}else if ($operator=='isnotempty'){
					return $checkbox_checked_length>0;
				}else if ($operator=='isempty'){
					return $checkbox_checked_length==0;
				} 
			break;
			case "select":
			case "textarea":
			case "textfield":
				$element_to_check .=$form_prefix;
				if (isset($_POST[$element_to_check])){
					$posted_value = $_POST[$element_to_check];
					$posted_value = stripslashes( $posted_value );
					if ($element_type=="select"){
						$posted_value=TM_EPO_HELPER()->encodeURIComponent($posted_value);
						$posted_value=TM_EPO_HELPER()->reverse_strrchr($posted_value,"_");
					}
				}
			break;
		}
		return $this->tm_check_match($posted_value,$value,$operator);
	}

	/** Conditional logic **/
	public function tm_variation_check_match($form_prefix,$value,$operator){
		$posted_value = $this->get_posted_variation_id($form_prefix);
		return $this->tm_check_match($posted_value,$value,$operator);
	}

	/** Conditional logic **/
	public function tm_check_match($posted_value,$value,$operator){
		$posted_value = rawurlencode(apply_filters('tm_translate',rawurldecode($posted_value)));
		$value = rawurlencode(apply_filters('tm_translate',rawurldecode($value)));
		switch ($operator){
		case "is":
			return ($posted_value!=null && $value == $posted_value);
			break;
		case "isnot":
			return ($posted_value!=null && $value != $posted_value);
			break;
		case "isempty":								
			return (!(($posted_value != null && $posted_value!='')));
			break;
		case "isnotempty":
			return (($posted_value != null && $posted_value!=''));
			break;
		case "startswith" :
            return TM_EPO_HELPER()->str_startswith($posted_value,$value);
            break;
        case "endswith" :
            return TM_EPO_HELPER()->str_endsswith($posted_value,$value);
            break;
        case "greaterthan" :
            return floatval($posted_value)>floatval($value);
            break;
        case "lessthan" :
            return floatval($posted_value)<floatval($value);
            break;
		}
		return false;
	}

	/** Gets the stored card data for the order again functionality. **/
	public function order_again_cart_item_data( $cart_item_meta, $product, $order ) {
		global $woocommerce;

		// Disable validation
		remove_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 50, 6 );

		$_backup_cart = isset( $product['item_meta']['tmcartepo_data'] ) ? $product['item_meta']['tmcartepo_data'] : false;
		if ( !$_backup_cart ) {
			$_backup_cart = isset( $product['item_meta']['_tmcartepo_data'] ) ? $product['item_meta']['_tmcartepo_data'] : false;
		}
		if ( $_backup_cart && is_array( $_backup_cart ) && isset( $_backup_cart[0] ) ) {
			$_backup_cart=maybe_unserialize( $_backup_cart[0] );
			$cart_item_meta['tmcartepo'] = $_backup_cart;
		}

		$_backup_cart = isset( $product['item_meta']['tmsubscriptionfee_data'] ) ? $product['item_meta']['tmsubscriptionfee_data'] : false;
		if ( !$_backup_cart ) {
			$_backup_cart = isset( $product['item_meta']['_tmsubscriptionfee_data'] ) ? $product['item_meta']['_tmsubscriptionfee_data'] : false;
		}
		if ( $_backup_cart && is_array( $_backup_cart ) && isset( $_backup_cart[0] ) ) {
			$_backup_cart=maybe_unserialize( $_backup_cart[0] );
			$cart_item_meta['tmsubscriptionfee'] = $_backup_cart[0];
		}
		
		$_backup_cart = isset( $product['item_meta']['tmcartfee_data'] ) ? $product['item_meta']['tmcartfee_data'] : false;
		if ( !$_backup_cart ) {
			$_backup_cart = isset( $product['item_meta']['_tmcartfee_data'] ) ? $product['item_meta']['_tmcartfee_data'] : false;
		}
		if ( $_backup_cart && is_array( $_backup_cart ) && isset( $_backup_cart[0] ) ) {
			$_backup_cart=maybe_unserialize( $_backup_cart[0] );
			$cart_item_meta['tmcartfee'] = $_backup_cart[0];
		}

		return $cart_item_meta;
	}

	/**
	 * Handles the display of all the extra options on the product page.
	 *
	 * IMPORTANT:
	 * We do not support plugins that pollute the global $woocommerce.
	 *
	 */
	public function frontend_display($product_id=0, $form_prefix="", $dummy_prefix=false) {
		global $product,$woocommerce;
		if (!property_exists( $woocommerce, 'product_factory') 
			|| $woocommerce->product_factory===NULL 
			|| ($this->tm_options_have_been_displayed && (!($this->is_bto || ($this->is_enabled_shortcodes() && !is_product()) || ((is_shop() || is_product_category() || is_product_tag()) && $this->tm_epo_enable_in_shop=="yes") ) ) )
		){
			return;// bad function call
		}

			
		$this->tm_epo_fields($product_id, $form_prefix, false, $dummy_prefix);
		$this->tm_add_inline_style();
		$this->tm_epo_totals($product_id, $form_prefix);
		if (!$this->is_bto){
			$this->tm_options_have_been_displayed=true;	
		}		

	}

	public function tm_epo_totals($product_id=0, $form_prefix="", $is_from_shortcode=false) {
		global $product,$woocommerce;
		if (!property_exists( $woocommerce, 'product_factory') 
			|| $woocommerce->product_factory===NULL 
			|| ($this->tm_options_have_been_displayed && (!($this->is_bto || (($this->is_enabled_shortcodes() && !$is_from_shortcode) && !is_product()) || ((is_shop() || is_product_category() || is_product_tag()) && $this->tm_epo_enable_in_shop=="yes") ) ) )
		){
			return;// bad function call
		}
		$this->print_price_fields( $product_id, $form_prefix, $is_from_shortcode );
		if (!$this->is_bto){
			$this->tm_options_totals_have_been_displayed=true;
		}
	}

	public function tm_woocommerce_before_single_product(){

		global $woocommerce;
		if (!property_exists( $woocommerce, 'product_factory') 
			|| $woocommerce->product_factory===NULL 
			){
			return;// bad function call
		}
		global $product;
		if ($product){
			$this->current_product_id_to_be_displayed = $product->id;
			$this->current_product_id_to_be_displayed_check["tc"."-".count($this->current_product_id_to_be_displayed_check)."-".$this->current_product_id_to_be_displayed]=$this->current_product_id_to_be_displayed;
		}
	}

	public function tm_woocommerce_after_single_product(){

		$this->current_product_id_to_be_displayed=0;

	}

	private function tm_epo_fields_batch($form_prefix="", $dummy_prefix=false) {
		foreach ($this->current_product_id_to_be_displayed_check as $key => $product_id) {
			if (!empty($product_id)){
				$this->inline_styles='';
				$this->inline_styles_head='';

				$this->tm_variation_css_check(1,$product_id);

				$this->tm_epo_fields($product_id, $form_prefix, false, $dummy_prefix);
				$this->tm_add_inline_style();

				if ($this->tm_epo_options_placement==$this->tm_epo_totals_box_placement){
					$this->tm_epo_totals($product_id, $form_prefix);
				}else{
					if (!$this->is_bto){
						unset($this->epo_internal_counter_check["tc".$this->epo_internal_counter]);
					}
				}
			}			
		}
		if (!$this->is_bto){
			if ($this->tm_epo_options_placement!=$this->tm_epo_totals_box_placement){
				$this->epo_internal_counter=0;
				$this->epo_internal_counter_check=array();
			}
		}
	}

	public function tm_epo_fields($product_id=0, $form_prefix="", $is_from_shortcode=false, $dummy_prefix=false) {
		global $woocommerce;

		if (!property_exists( $woocommerce, 'product_factory') 
			|| $woocommerce->product_factory===NULL 
			|| ($this->tm_options_have_been_displayed && (!($this->is_bto || (($this->is_enabled_shortcodes() && !$is_from_shortcode) && !is_product()) || ((is_shop() || is_product_category() || is_product_tag()) && $this->tm_epo_enable_in_shop=="yes") ) ) )){
			return;// bad function call
		}
		if (!$product_id){
			global $product;
			if ($product){
				$product_id=$product->id;
			}
		}else{
			$product=wc_get_product($product_id);
		}
		if (!$product_id || empty($product) ){
			if (!empty($this->current_product_id_to_be_displayed)){
				$product_id=$this->current_product_id_to_be_displayed;
				$product=wc_get_product($product_id);
			}else{
				$this->tm_epo_fields_batch($form_prefix, $dummy_prefix);
				return;
			}
		}
		if (!$product_id || empty($product) ){
			return;
		}
		
		$post_id=$product_id;

		if ($form_prefix){
			$_bto_id=$form_prefix;
			$form_prefix="_".$form_prefix;
			echo '<input type="hidden" class="cpf-bto-id" name="cpf_bto_id[]" value="'.$form_prefix.'" />';
			echo '<input type="hidden" value="" name="cpf_bto_price['.$_bto_id.']" class="cpf-bto-price" />';
			echo '<input type="hidden" value="0" name="cpf_bto_optionsprice[]" class="cpf-bto-optionsprice" />';
		}
 
		$cpf_price_array=$this->get_product_tm_epos($post_id);

		if (!$cpf_price_array){
			return;
		}
		$global_price_array = $cpf_price_array['global'];
		$local_price_array  = $cpf_price_array['local'];
		if ( empty($global_price_array) && empty($local_price_array) ){
			if (!$this->is_bto){
				if (empty($this->epo_internal_counter) || !isset($this->epo_internal_counter_check["tc".$this->epo_internal_counter])){
					// First time displaying the fields and totals havenn't been displayed
					$this->epo_internal_counter++;
					$this->epo_internal_counter_check["tc".$this->epo_internal_counter]=$this->epo_internal_counter;
				}else{
					// Totals have already been displayed
					unset($this->epo_internal_counter_check["tc".$this->epo_internal_counter]);
		
					$this->current_product_id_to_be_displayed=0;
				}
				$_epo_internal_counter=$this->epo_internal_counter;
			}else{
				$_epo_internal_counter=0;
			}
			return;
		}

		$global_prices=array( 'before'=>array(), 'after'=>array() );
		foreach ( $global_price_array as $priority=>$priorities ) {
			foreach ( $priorities as $pid=>$field ) {
				if (isset($field['sections']) && is_array($field['sections'])){
					foreach ( $field['sections'] as $section_id=>$section ) {
						if ( isset( $section['sections_placement'] ) ) {
							$global_prices[$section['sections_placement']][$priority][$pid]['sections'][$section_id]=$section;
						}
					}
				}
			}
		}

		$tabindex   		= 0;
		$_currency   		= get_woocommerce_currency_symbol();
		$unit_counter  		= 0;
		$field_counter  	= 0;
		$element_counter	= 0;
		
		if (!$this->is_bto){
			if (empty($this->epo_internal_counter) || !isset($this->epo_internal_counter_check["tc".$this->epo_internal_counter])){
				// First time displaying the fields and totals havenn't been displayed
				$this->epo_internal_counter++;
				$this->epo_internal_counter_check["tc".$this->epo_internal_counter]=$this->epo_internal_counter;
			}else{
				// Totals have already been displayed
				unset($this->epo_internal_counter_check["tc".$this->epo_internal_counter]);
	
				$this->current_product_id_to_be_displayed=0;
			}
			$_epo_internal_counter=$this->epo_internal_counter;
		}else{
			$_epo_internal_counter=0;
		}		
		
		wc_get_template(
			'tm-start.php',
			array(
				'form_prefix' => $form_prefix, 
				'product_id'=>$product_id,
				'epo_internal_counter' => $_epo_internal_counter,
				'is_from_shortcode' => $is_from_shortcode
				),
			$this->_namespace,
			TM_EPO_TEMPLATE_PATH
		);

		// global options before local
		foreach ( $global_prices['before'] as $priorities ) {
			foreach ( $priorities as $field ) {
				$args=array(
					'tabindex'   		=> $tabindex,
					'unit_counter'  	=> $unit_counter,
					'field_counter'  	=> $field_counter,
					'element_counter'  	=> $element_counter,
					'_currency'   		=> $_currency,
					'product_id' 		=> $product_id
				);
				$_return=$this->get_builder_display( $field, 'before', $args , $form_prefix, $product_id, $dummy_prefix);
				extract( $_return, EXTR_OVERWRITE );
			}
		}

		// local options
		if ( is_array( $local_price_array ) && sizeof( $local_price_array ) > 0 ) {

			$attributes = maybe_unserialize( get_post_meta( floatval(TM_EPO_WPML()->get_original_id( $post_id ) ), '_product_attributes', true ) );
			$wpml_attributes = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );
                            
			if ( is_array( $attributes ) && count( $attributes )>0 ) {
				foreach ( $local_price_array as $field ) {
					if ( isset( $field['name'] ) && isset( $attributes[$field['name']] ) && !$attributes[$field['name']]['is_variation'] ) {

						$attribute=$attributes[$field['name']];
						$wpml_attribute=isset($wpml_attributes[$field['name']])?$wpml_attributes[$field['name']]:array();					

						$empty_rules="";
						if ( isset( $field['rules_filtered'][0] ) ) {
							$empty_rules=esc_html( json_encode( ( $field['rules_filtered'][0] ) ) );
						}
						$empty_rules_type="";
						if ( isset( $field['rules_type'][0] ) ) {
							$empty_rules_type=esc_html( json_encode( ( $field['rules_type'][0] ) ) );
						}

						$args = array(
							'title'  	=> ( !$attribute['is_taxonomy'] && isset($attributes[$field['name']]["name"]))
							?esc_html(wc_attribute_label($attributes[$field['name']]["name"]))
							:esc_html( wc_attribute_label( $field['name'] ) ),
							'required'  => esc_html( wc_attribute_label( $field['required'] ) ),
							'field_id'  => 'tm-epo-field-'.$unit_counter,
							'type'      => $field['type'],
							'rules'     => $empty_rules,
							'rules_type'     => $empty_rules_type
						);
						wc_get_template(
							'tm-field-start.php',
							$args ,
							$this->_namespace,
							TM_EPO_TEMPLATE_PATH
						);

						$name_inc="";
						$field_counter=0;
						if ( $attribute['is_taxonomy'] ) {

							$_original_terms = TM_EPO_WPML()->get_terms( TM_EPO_WPML()->get_lang(), $attribute['name'], 'orderby=name&hide_empty=0',$post_id );
							$_default_terms = TM_EPO_WPML()->get_terms( TM_EPO_WPML()->get_default_lang(), $attribute['name'], 'orderby=name&hide_empty=0',TM_EPO_WPML()->get_current_id($post_id,'product',TM_EPO_WPML()->get_default_lang()) );
							$_tems_to_use = TM_EPO_WPML()->merge_terms($_original_terms, $_default_terms);
							$_default_terms = TM_EPO_WPML()->merge_terms($_default_terms, $_default_terms);
$_default_terms = $_original_terms;
							switch ( $field['type'] ) {

								case "select":
									$name_inc ="select_".$element_counter;
									$tabindex++;

									$args = array(
										'options'   	=> '',
										'textafterprice' => '',
										'id'    		=> 'tmcp_select_'.$tabindex.$form_prefix,
										'name'    		=> 'tmcp_'.$name_inc.($dummy_prefix?"": $form_prefix),
										'amount'     	=> '0 '.$_currency,
										'original_amount'=> '0 '.$_currency,
										'hide_amount'  	=> !empty( $field['hide_price'] )?" hidden":"",
										'tabindex'   	=> $tabindex
									);
									if ( $_default_terms && is_array($_default_terms) ) {
						                foreach ( $_default_terms as $trid => $term  ) {
											$has_term = has_term( (int) $term->term_id, $attribute['name'], floatval(TM_EPO_WPML()->get_original_id( $post_id )) ) ? 1 : 0;
											
											//$has_term = $has_term || isset($_tems_to_use[$trid]);

						                    if ($has_term ){
						                    	$wpml_term_id = TM_EPO_WPML()->is_active()? icl_object_id($term->term_id,  $attribute['name'], false):false;
												if ($wpml_term_id){
													$wpml_term = get_term( $wpml_term_id, $attribute['name'] );
												}else{
													$wpml_term = $term;
												}
						                    	$args['options'] .='<option '.( isset( $_POST['tmcp_'.$name_inc.$form_prefix] )?selected( $_POST['tmcp_'.$name_inc.$form_prefix], esc_attr( sanitize_title( $term->slug ) ), 0 ) :"" ).' value="'.sanitize_title( $term->slug ).'" data-price="" data-rules="'.( isset( $field['rules_filtered'][$term->slug] )?esc_html( json_encode( ( $field['rules_filtered'][$term->slug] ) ) ):'' ).'" data-rulestype="'.( isset( $field['rules_type'][$term->slug] )?esc_html( json_encode( ( $field['rules_type'][$term->slug] ) ) ):'' ).'">'.wptexturize( $wpml_term->name ).'</option>';					                        
						                    }
						                }
						            }
									
									wc_get_template(
										'tm-'.$field['type'].'.php',
										$args ,
										$this->_namespace,
										TM_EPO_TEMPLATE_PATH
									);
									$element_counter++;
									break;

								case "radio":
								case "checkbox":
									if ( $_default_terms && is_array($_default_terms) ) {
										foreach ( $_default_terms as $trid => $term ) {


											$has_term = has_term( (int) $term->term_id, $attribute['name'], floatval(TM_EPO_WPML()->get_original_id( $post_id )) ) ? 1 : 0;
											
											//$has_term = $has_term || isset($_tems_to_use[$trid]);
											
											

											if ($has_term ){

												$wpml_term_id = TM_EPO_WPML()->is_active()?icl_object_id($term->term_id,  $attribute['name'], false):false;

												if ($wpml_term_id){
													$wpml_term = get_term( $wpml_term_id, $attribute['name'] );
												}else{;
													$wpml_term = $term;
												}
												
												$tabindex++;

												if ( $field['type']=='radio' ) {
													$name_inc ="radio_".$element_counter;
												}
												if ( $field['type']=='checkbox' ) {
													$name_inc ="checkbox_".$element_counter."_".$field_counter;
												}

												$args = array(
													'label'   		=> wptexturize( $wpml_term->name ),
													'textafterprice' => '',
													'value'   		=> sanitize_title( $term->slug ),
													'rules'   		=> isset( $field['rules_filtered'][$term->slug] )?esc_html( json_encode( ( $field['rules_filtered'][$term->slug] ) ) ):'',
													'rules_type' 	=> isset( $field['rules_type'][$term->slug] )?esc_html( json_encode( ( $field['rules_type'][$term->slug] ) ) ):'',
													'id'    		=> 'tmcp_choice_'.$element_counter."_".$field_counter."_".$tabindex.$form_prefix,
													'name'    		=> 'tmcp_'.$name_inc.($dummy_prefix?"":$form_prefix),
													'amount'     	=> '0 '.$_currency,
													'original_amount'=> '0 '.$_currency,
													'hide_amount'  	=> !empty( $field['hide_price'] )?" hidden":"",
													'tabindex'   	=> $tabindex,
													'use_images'	=> "",
													'grid_break'	=> "",
													'percent'		=> "",
													'limit' 		=> empty( $field['limit'] )?"":$field['limit']
												);
												wc_get_template(
													'tm-'.$field['type'].'.php',
													$args ,
													$this->_namespace,
													TM_EPO_TEMPLATE_PATH
												);

												$field_counter++;
											}
						                }
						            }								

									$element_counter++;
									break;

							}
						} else {

							$options = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
							$wpml_options = isset($wpml_attribute['value'])?array_map( 'trim', explode( WC_DELIMITER, $wpml_attribute['value'] ) ):$options;

							switch ( $field['type'] ) {

								case "select":
									$name_inc ="select_".$element_counter;
									$tabindex++;

									$args = array(
										'options'   	=> '',
										'textafterprice' => '',
										'id'    		=> 'tmcp_select_'.$tabindex.$form_prefix,
										'name'    		=> 'tmcp_'.$name_inc.($dummy_prefix?"":$form_prefix),
										'amount'     	=> '0 '.$_currency,
										'original_amount'=> '0 '.$_currency,
										'hide_amount'  	=> !empty( $field['hide_price'] )?" hidden":"",
										'tabindex'   	=> $tabindex
									);
									foreach ( $options as $k=>$option ) {
										$args['options'] .='<option '.( isset( $_POST['tmcp_'.$name_inc.$form_prefix] )?selected( $_POST['tmcp_'.$name_inc.$form_prefix], esc_attr( sanitize_title( $option ) ), 0 ) :"" ).' value="'.esc_attr( sanitize_title( $option ) ).'" data-price="" data-rules="'.( isset( $field['rules_filtered'][esc_attr( sanitize_title( $option ) )] )?esc_html( json_encode( ( $field['rules_filtered'][esc_attr( sanitize_title( $option ) )] ) ) ):'' ).'" data-rulestype="'.( isset( $field['rules_type'][esc_attr( sanitize_title( $option ) )] )?esc_html( json_encode( ( $field['rules_type'][esc_attr( sanitize_title( $option ) )] ) ) ):'' ).'">'.wptexturize( apply_filters( 'woocommerce_tm_epo_option_name', isset($wpml_options[$k])?$wpml_options[$k]:$option, null, null ) ).'</option>';
									}
									wc_get_template(
										'tm-'.$field['type'].'.php',
										$args ,
										$this->_namespace,
										TM_EPO_TEMPLATE_PATH
									);
									$element_counter++;
									break;

								case "radio":
								case "checkbox":
									foreach ( $options as $k=> $option ) {
										$tabindex++;

										if ( $field['type']=='radio' ) {
											$name_inc ="radio_".$element_counter;
										}
										if ( $field['type']=='checkbox' ) {
											$name_inc ="checkbox_".$element_counter."_".$field_counter;
										}

										$args = array(
											'label'   		=> wptexturize( apply_filters( 'woocommerce_tm_epo_option_name', isset($wpml_options[$k])?$wpml_options[$k]:$option, null, null ) ),
											'textafterprice' => '',
											'value'   		=> esc_attr( sanitize_title( $option ) ),
											'rules'   		=> isset( $field['rules_filtered'][sanitize_title( $option )] )?esc_html( json_encode( ( $field['rules_filtered'][sanitize_title( $option )] ) ) ):'',
											'rules_type' 	=> isset( $field['rules_type'][sanitize_title( $option )] )?esc_html( json_encode( ( $field['rules_type'][sanitize_title( $option )] ) ) ):'',
											'id'    		=> 'tmcp_choice_'.$element_counter."_".$field_counter."_".$tabindex.$form_prefix,
											'name'    		=> 'tmcp_'.$name_inc.($dummy_prefix?"":$form_prefix),
											'amount'     	=> '0 '.$_currency,
											'original_amount'=> '0 '.$_currency,
											'hide_amount'  	=> !empty( $field['hide_price'] )?" hidden":"",
											'tabindex'   	=> $tabindex,
											'use_images'	=> "",
											'grid_break'	=> "",
											'percent'		=> "",
											'limit' 		=> empty( $field['limit'] )?"":$field['limit']
										);
										wc_get_template(
											'tm-'.$field['type'].'.php',
											$args ,
											$this->_namespace,
											TM_EPO_TEMPLATE_PATH
										);
										$field_counter++;
									}
									$element_counter++;
									break;

							}
						}

						wc_get_template(
							'tm-field-end.php',
							array() ,
							$this->_namespace,
							TM_EPO_TEMPLATE_PATH
						);

						$unit_counter++;
					}
				}
			}
		}

		// global options after local
		foreach ( $global_prices['after'] as $priorities ) {
			foreach ( $priorities as $field ) {
				$args=array(
					'tabindex'   		=> $tabindex,
					'unit_counter'  	=> $unit_counter,
					'field_counter'  	=> $field_counter,
					'element_counter'  	=> $element_counter,
					'_currency'   		=> $_currency,
					'product_id' 		=> $product_id
				);
				$_return=$this->get_builder_display( $field, 'after', $args, $form_prefix, $product_id, $dummy_prefix );
				extract( $_return, EXTR_OVERWRITE );
			}
		}

		wc_get_template(
			'tm-end.php',
			array() ,
			$this->_namespace,
			TM_EPO_TEMPLATE_PATH
		);
		
		$this->tm_options_single_have_been_displayed=true;
	}

	public function is_supported_quick_view(){
		$theme=$this->get_theme('Name');
		if ($theme=='Flatsome' || $theme=="Kleo" || $theme=="Venedor" || $theme=="Elise" || $theme="Minshop" || $theme="Porto" ){
			return true;
		}
		return false;
	}

	public function css_array() {
		$css_array = array(
			'tc-font-awesome' => array(
				'src'     => TM_EPO_PLUGIN_URL .'/external/font-awesome/css/font-awesome.min.css',
				'deps'    => false,
				'version' => '4.4',
				'media'   => 'screen'
			),
			'tc-epo-animate-css' => array(
				'src'     =>  TM_EPO_PLUGIN_URL  . '/assets/css/animate.css',
				'deps'    => false,
				'version' => $this->version,
				//'media'   => 'only screen and (min-width: ' . apply_filters( 'woocommerce_style_smallscreen_breakpoint', $breakpoint = '768px' ) . ')'
				'media'   => 'all'
			),
			'tc-epo-css' => array(
				'src'     => TM_EPO_PLUGIN_URL . '/assets/css/tm-epo.css',
				'deps'    => false,
				'version' => $this->version,
				'media'   => 'all'
			),
			'tc-spectrum-css' => array(
				'src'     => TM_EPO_PLUGIN_URL . '/assets/css/tm-spectrum.css',
				'deps'    => false,
				'version' => '1.7.1',
				'media'   => 'screen'
			),
		);
		return $css_array;
	}

	public function custom_frontend_scripts() {

		do_action('tm_epo_register_addons_scripts');
		if ( apply_filters( 'wc_epo_register_addons_scripts', false ) ) {
			return;
		}
		$product = wc_get_product();
		
		if ( $enqueue_styles = apply_filters( 'tm_epo_enqueue_styles', $this->css_array() ) ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				wp_enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
			}
			if (is_rtl()){
				wp_enqueue_style( 'tm-epo-css-rtl' , TM_EPO_PLUGIN_URL . '/assets/css/tm-epo-rtl.css', false, $this->version, 'all' );	
			}
		}
		if(defined( 'QTRANSLATE_FILE')){
			global $q_config;
			if (isset($q_config['enabled_languages'])){
				wp_enqueue_script( 'tm-epo-q-translate-x-clogic', TM_EPO_PLUGIN_URL. '/assets/js/tm-epo-q-translate-x-clogic.js', array( 'jquery' ), $this->version, true );
				$args = array(
					'enabled_languages' => $q_config['enabled_languages'],
					'language' 			=> $q_config['language'],
				);
				wp_localize_script( 'tm-epo-q-translate-x-clogic', 'tm_epo_q_translate_x_clogic_js', $args );				
			}
		}
		wp_register_script( 'tm-accounting', TM_EPO_PLUGIN_URL . '/assets/js/accounting.min.js', '', '0.3.2', true );
		wp_register_script( 'tm-modernizr', TM_EPO_PLUGIN_URL. '/assets/js/modernizr.js', '', '3.3.1' );
		wp_register_script( 'tm-scripts', TM_EPO_PLUGIN_URL . '/assets/js/tm-scripts.js', '', $this->version, true );
		
		wp_enqueue_script( 'tm-datepicker', TM_EPO_PLUGIN_URL. '/assets/js/tm-datepicker.js', array( 'jquery','jquery-ui-core' ), $this->version, true );
		wp_enqueue_script( 'tm-timepicker', TM_EPO_PLUGIN_URL. '/assets/js/tm-timepicker.js', array( 'jquery','jquery-ui-core','tm-datepicker' ), $this->version, true );
		wp_enqueue_script( 'tm-owl-carousel', TM_EPO_PLUGIN_URL. '/assets/js/owl.carousel.min.js', array('jquery'), $this->version, true );

		wc_get_template( 'tc-js-templates.php', array(), $this->_namespace, TM_EPO_TEMPLATE_PATH );

		wp_enqueue_script( 'tm-epo', TM_EPO_PLUGIN_URL. '/assets/js/tm-epo.js', array( 'jquery', 'jquery-ui-slider', 'wp-util', 'tm-owl-carousel','tm-datepicker', 'tm-accounting', 'tm-modernizr', 'tm-scripts' ), $this->version, true );

		$extra_fee=0;
		global $wp_locale;
		$args = array(
			'ajax_url'              				=> admin_url( 'admin-ajax' ).'.php',//WPML 3.3.3 fix
			'extra_fee' 							=> apply_filters( 'woocommerce_tm_final_price_extra_fee', $extra_fee,$product ),
			'tm_epo_final_total_box' 				=> (empty($this->tm_meta_cpf['override_final_total_box']))?$this->tm_epo_final_total_box:$this->tm_meta_cpf['override_final_total_box'],
			'i18n_extra_fee'           				=> __( 'Extra fee', 'woocommerce-tm-extra-product-options' ),
			'i18n_options_total'           			=> (!empty($this->tm_epo_options_total_text))?$this->tm_epo_options_total_text:__( 'Options amount', 'woocommerce-tm-extra-product-options' ),
			'i18n_final_total'             			=> (!empty($this->tm_epo_final_total_text))?$this->tm_epo_final_total_text:__( 'Final total', 'woocommerce-tm-extra-product-options' ),
			'i18n_prev_text'             			=> (!empty($this->tm_epo_slider_prev_text))?$this->tm_epo_slider_prev_text:__( 'Prev', 'woocommerce-tm-extra-product-options' ),
			'i18n_next_text'             			=> (!empty($this->tm_epo_slider_next_text))?$this->tm_epo_slider_next_text:__( 'Next', 'woocommerce-tm-extra-product-options' ),
			'i18n_sign_up_fee' 						=> (!empty($this->tm_epo_subscription_fee_text))?$this->tm_epo_subscription_fee_text:__( 'Sign up fee', 'woocommerce-tm-extra-product-options' ),
			'i18n_cancel'							=> __( 'Cancel', 'woocommerce-tm-extra-product-options' ),
			'i18n_close'							=> (!empty($this->tm_epo_close_button_text))?$this->tm_epo_close_button_text:__( 'Close', 'woocommerce-tm-extra-product-options' ),
			'i18n_addition_options'					=> (!empty($this->tm_epo_additional_options_text))?$this->tm_epo_additional_options_text:__( 'Additional options', 'woocommerce-tm-extra-product-options' ),
			'i18n_characters_remaining' 			=> (!empty($this->tm_epo_characters_remaining_text))?$this->tm_epo_characters_remaining_text:__( 'characters remaining', 'woocommerce-tm-extra-product-options' ),

			'i18n_option_label'						=> __( 'Label', 'woocommerce-tm-extra-product-options' ),
			'i18n_option_value'						=> __( 'Value', 'woocommerce-tm-extra-product-options' ),
			'i18n_option_qty'						=> __( 'Qty', 'woocommerce-tm-extra-product-options' ),
			'i18n_option_price'						=> __( 'Price', 'woocommerce-tm-extra-product-options' ),

			'currency_format_num_decimals' 			=> absint( get_option( 'woocommerce_price_num_decimals' ) ),
			'currency_format_symbol'       			=> get_woocommerce_currency_symbol(),
			'currency_format_decimal_sep'  			=> esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
			'currency_format_thousand_sep' 			=> esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
			'currency_format'              			=> esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ),
			'css_styles' 							=> $this->tm_epo_css_styles,
			'css_styles_style' 						=> $this->tm_epo_css_styles_style,
			'tm_epo_options_placement' 				=> $this->tm_epo_options_placement,
			'tm_epo_totals_box_placement' 			=> $this->tm_epo_totals_box_placement,
			'tm_epo_no_lazy_load' 					=> $this->tm_epo_no_lazy_load,
			'tm_epo_show_only_active_quantities' 	=> $this->tm_epo_show_only_active_quantities,
			'tm_epo_hide_add_cart_button' 			=> $this->tm_epo_hide_add_cart_button,
			'tm_epo_auto_hide_price_if_zero' 		=> $this->tm_epo_auto_hide_price_if_zero,
			"tm_epo_global_enable_validation" 		=> $this->tm_epo_global_enable_validation,
			"tm_epo_global_input_decimal_separator" => $this->tm_epo_global_input_decimal_separator,
			"tm_epo_global_displayed_decimal_separator" => $this->tm_epo_global_displayed_decimal_separator,
			"tm_epo_remove_free_price_label" 		=> $this->tm_epo_remove_free_price_label,

			"tm_epo_global_validator_messages" 		=> array(
				"required" 	=> (!empty($this->tm_epo_this_field_is_required_text))?$this->tm_epo_this_field_is_required_text :__("This field is required.", 'woocommerce-tm-extra-product-options' ),
		        "email" 	=> __("Please enter a valid email address.", 'woocommerce-tm-extra-product-options' ),
		        "url" 		=> __("Please enter a valid URL.", 'woocommerce-tm-extra-product-options' ),
		        "number" 	=> __("Please enter a valid number.", 'woocommerce-tm-extra-product-options' ),
		        "digits" 	=> __("Please enter only digits.", 'woocommerce-tm-extra-product-options' ),
		        "maxlength" => __("Please enter no more than {0} characters.", 'woocommerce-tm-extra-product-options' ) ,
		        "minlength" => __("Please enter at least {0} characters.", 'woocommerce-tm-extra-product-options' ) ,
		        "max" 		=> __("Please enter a value less than or equal to {0}.", 'woocommerce-tm-extra-product-options' ) ,
		        "min" 		=> __("Please enter a value greater than or equal to {0}.", 'woocommerce-tm-extra-product-options' ),

		        "epolimit" 	=> __("Please select up to {0} choices.", 'woocommerce-tm-extra-product-options' ),
		        "epoexact" 	=> __("Please select exactly {0} choices.", 'woocommerce-tm-extra-product-options' ),
		        "epomin" 	=> __("Please select at least {0} choices.", 'woocommerce-tm-extra-product-options' ),
			),
			
			'first_day' 							=> intval( get_option('start_of_week') ),
			'monthNames'        					=> $this->strip_array_indices( $wp_locale->month ),
			'monthNamesShort'   					=> $this->strip_array_indices( $wp_locale->month_abbrev ),
			'dayNames' 								=> $this->strip_array_indices( $wp_locale->weekday ),
 			'dayNamesShort' 						=> $this->strip_array_indices( $wp_locale->weekday_abbrev ),
			'dayNamesMin' 							=> $this->strip_array_indices( $wp_locale->weekday_initial ),
 			'isRTL'             					=> $wp_locale->text_direction=='rtl',
 			'text_direction'             			=> $wp_locale->text_direction,
 			'is_rtl' 								=> is_rtl(),
 			'closeText' 							=> (!empty($this->tm_epo_closeText))?$this->tm_epo_closeText:__( 'Done', 'woocommerce-tm-extra-product-options' ),
 			'currentText' 							=> (!empty($this->tm_epo_currentText))?$this->tm_epo_currentText:__( 'Today', 'woocommerce-tm-extra-product-options' ),

 			'hourText' 								=> __('Hour', 'woocommerce-tm-extra-product-options'),
 			'minuteText' 							=> __('Minute', 'woocommerce-tm-extra-product-options'),
 			'secondText'							=> __('Second', 'woocommerce-tm-extra-product-options'),

 			'floating_totals_box' 					=> $this->tm_epo_floating_totals_box,
 			'floating_totals_box_html_before'		=> apply_filters( 'floating_totals_box_html_before', '' ),
 			'floating_totals_box_html_after'		=> apply_filters( 'floating_totals_box_html_after', '' ),
 			'tm_epo_enable_final_total_box_all' 	=> $this->tm_epo_enable_final_total_box_all,
 			'tm_epo_enable_in_shop' 				=> $this->tm_epo_enable_in_shop,

 			'wc_booking_person_qty_multiplier' 		=> isset($this->tm_epo_bookings_person) && ($this->tm_epo_bookings_person=="yes")?1:0,
        	'wc_booking_block_qty_multiplier' 		=> isset($this->tm_epo_bookings_block) && ($this->tm_epo_bookings_block=="yes")?1:0,


		);
		wp_localize_script( 'tm-epo', 'tm_epo_js', $args );
	}

	/**
	 * Format array for the datepicker
	 *
	 * WordPress stores the locale information in an array with a alphanumeric index, and
	 * the datepicker wants a numerical index. This function replaces the index with a number
	*/
	private function strip_array_indices( $ArrayToStrip=array() ) {
		$NewArray=array();
		foreach( $ArrayToStrip as $objArrayItem) {
			$NewArray[] = $objArrayItem;
		}
		return( $NewArray );
	}

	private function tm_epo_totals_batch( $form_prefix="") {
		foreach ($this->current_product_id_to_be_displayed_check as $key => $product_id) {
			if (!empty($product_id)){
				$this->print_price_fields($product_id, $form_prefix);
				if ($this->tm_epo_options_placement!=$this->tm_epo_totals_box_placement){
					if (!$this->is_bto){
						unset($this->epo_internal_counter_check["tc".$this->epo_internal_counter]);
					}
				}
			}

			
			//$this->tm_epo_totals($product_id, $form_prefix);
		}
		if (!$this->is_bto){
			if ($this->tm_epo_options_placement!=$this->tm_epo_totals_box_placement){
				$this->epo_internal_counter=0;
				$this->epo_internal_counter_check=array();
			}
		}

	}

	private function print_price_fields( $product_id=0, $form_prefix="", $is_from_shortcode=false) {
		if (!$product_id){
			global $product;
			if ($product){
				$product_id=$product->id;
			}
		}else{
			$product=wc_get_product($product_id);
		}
		if (!$product_id || empty($product) ){
			if (!empty($this->current_product_id_to_be_displayed)){
				$product_id=$this->current_product_id_to_be_displayed;
				$product=wc_get_product($product_id);
			}else{
				$this->tm_epo_totals_batch($form_prefix);
				return;
			}
		}
		if (!$product_id || empty($product) ){
			return;
		}

		$cpf_price_array=$this->get_product_tm_epos($product_id);
		if (!$cpf_price_array){
			return;
		}	

		if($cpf_price_array && $this->tm_epo_enable_final_total_box_all=="no"){
			$global_price_array = $cpf_price_array['global'];
			$local_price_array  = $cpf_price_array['local'];
			if (empty($global_price_array) && empty($local_price_array)){
				if (!$this->is_bto){
					if ( empty($this->epo_internal_counter) || !isset($this->epo_internal_counter_check["tc".$this->epo_internal_counter])){
						// First time displaying totals and fields haven't been displayed
						$this->epo_internal_counter++;
						$this->epo_internal_counter_check["tc".$this->epo_internal_counter]=$this->epo_internal_counter;
					}else{
						// Fields have already been displayed
						unset($this->epo_internal_counter_check["tc".$this->epo_internal_counter]);
						$this->current_product_id_to_be_displayed=0;
					}
					$_epo_internal_counter=$this->epo_internal_counter;
				}else{
					$_epo_internal_counter=0;
				}
				return;
			}
		}
		if (!$cpf_price_array && $this->tm_epo_enable_final_total_box_all=="no"){
			return;
		}
		if ($form_prefix){	
			$form_prefix="_".$form_prefix;
		}

		$_price=apply_filters( 'woocommerce_tm_epo_price_compatibility',$product->get_price(),$product);
		$price=array();
		$price['product']=array(); // product price rules
		$price['price']=apply_filters( 'wc_epo_product_price', $_price,"",false); // product price

		$price = apply_filters('wc_epo_product_price_rules', $price, $product );		

		// Woothemes Dynamic Pricing (not yet fully compatible)
		if (class_exists('WC_Dynamic_Pricing')){
			$id = isset($product->variation_id) ? $product->variation_id : $product->id;
			$dp=WC_Dynamic_Pricing::instance();
			if ($dp && 
				is_object($dp) && property_exists($dp, "discounted_products") 
				&& isset($dp->discounted_products[$id]) ){
				$_price= $dp->discounted_products[$id];
			}else{
				$_price=$product->get_price();
			}
			$price['price']=apply_filters( 'wc_epo_product_price', $_price,"",false); // product price
		}
			
		$variations = array();
		$variations_subscription_period = array();
		
		$variations_subscription_sign_up_fee = array();
		foreach ( $product->get_children() as $child_id ) {

			$variation = $product->get_child( $child_id );
			if ( ! $variation || ! $variation->exists() ){
				continue;
			}
			if (class_exists('WC_Subscriptions_Product')){
				$variations_subscription_period[$child_id] = WC_Subscriptions_Product::get_price_string(
					$variation,
					array(
						'subscription_price' 	=> false,
						'sign_up_fee' 			=> false,
						'trial_length' 			=> false
					)
				);
				$variations_subscription_sign_up_fee[$child_id] = $variation->subscription_sign_up_fee;
			}else{
				$variations_subscription_period[$child_id] = '';
				$variations_subscription_sign_up_fee[$child_id] = '';
			}

			$variations[$child_id] = apply_filters( 'woocommerce_tm_epo_price_compatibility', apply_filters( 'wc_epo_product_price', $variation->get_price(),"",false) ,$variation, $child_id);
			
		}

		$is_subscription=false;
		$subscription_period='';
		
		$subscription_sign_up_fee=0;
		if (class_exists('WC_Subscriptions_Product')){
			if (WC_Subscriptions_Product::is_subscription( $product )){
				$is_subscription=true;
				$subscription_period = WC_Subscriptions_Product::get_price_string(
					$product,
					array(
						'subscription_price' 	=> false,
						'sign_up_fee' 			=> false,
						'trial_length' 			=> false
					)
				);
				
				$subscription_sign_up_fee= WC_Subscriptions_Product::get_sign_up_fee( $product );
			}
		}

		global $woocommerce;
		$cart = $woocommerce->cart;
		$_tax  = new WC_Tax();
		$taxrates=$_tax->get_rates( $product->get_tax_class() );
		unset($_tax);
		$tax_rate=0;
		foreach ($taxrates as $key => $value) {
			$tax_rate=$tax_rate+floatval($value['rate']);
		}
		$taxable 			= $product->is_taxable();
		$tax_display_mode 	= get_option( 'woocommerce_tax_display_shop' );
		$tax_string 		= "";
		if ( $taxable && $this->tm_epo_global_tax_string_suffix=="yes") {

			if ( $tax_display_mode == 'excl' ) {

				//if ( $cart->tax_total > 0 && $cart->prices_include_tax ) {
					$tax_string = ' <small>' . WC()->countries->ex_tax_or_vat() . '</small>';
				//}
		
			} else {

				//if ( $cart->tax_total > 0 && !$cart->prices_include_tax ) {
					$tax_string = ' <small>' . WC()->countries->inc_tax_or_vat() . '</small>';
				//}

			}

		}
		$force_quantity=0;
		if($this->cart_edit_key){
			$cart_item_key = $this->cart_edit_key;
			$cart_item = WC()->cart->get_cart_item( $cart_item_key );

			if (isset($cart_item["quantity"])){
				$force_quantity = $cart_item["quantity"];
			}
		}
		if (!$this->is_bto){
			if ( empty($this->epo_internal_counter) || !isset($this->epo_internal_counter_check["tc".$this->epo_internal_counter])){
				// First time displaying totals and fields haven't been displayed
				$this->epo_internal_counter++;
				$this->epo_internal_counter_check["tc".$this->epo_internal_counter]=$this->epo_internal_counter;
			}else{
				// Fields have already been displayed
				unset($this->epo_internal_counter_check["tc".$this->epo_internal_counter]);
				$this->current_product_id_to_be_displayed=0;
			}
			$_epo_internal_counter=$this->epo_internal_counter;
		}else{
			$_epo_internal_counter=0;
		}

		$is_vat_exempt  = -1;
		$non_base_location_prices  = -1;
		$base_tax_rate=$tax_rate;
		if (class_exists('WC_Tax') && version_compare( get_option( 'woocommerce_version' ), '2.4', '>=' ) ){
			$tax_rates      = WC_Tax::get_rates( $product->get_tax_class() );
			$base_tax_rates = WC_Tax::get_base_tax_rates( $product->tax_class );
			$base_tax_rate=0;
			foreach ($base_tax_rates as $key => $value) {
				$base_tax_rate=$base_tax_rate+floatval($value['rate']);
			}
			$is_vat_exempt = (! empty( WC()->customer ) && WC()->customer->is_vat_exempt() )==true?1:0;
			$non_base_location_prices = (  $tax_rates !== $base_tax_rates && apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) )==true?1:0;
		}

		wc_get_template(
			'tm-totals.php',
			apply_filters('wc_epo_template_args_tm_totals',
			array(
				'theme_name' 							=> $this->get_theme('Name'),
				'variations' 							=> esc_html(json_encode( (array) $variations ) ),
				'variations_subscription_period' 		=> esc_html(json_encode( (array) $variations_subscription_period ) ),
				'variations_subscription_sign_up_fee' 	=> esc_html(json_encode( (array) $variations_subscription_sign_up_fee ) ),
				'subscription_period' 					=> $subscription_period,
				'subscription_sign_up_fee' 				=> $subscription_sign_up_fee,
				'is_subscription' 						=> $is_subscription,
				'is_sold_individually' 					=> $product->is_sold_individually(),
				'hidden' 								=> ($this->tm_meta_cpf['override_final_total_box'])?(($this->tm_epo_final_total_box=='hide')?' hidden':''):(($this->tm_meta_cpf['override_final_total_box']=='hide')?' hidden':''),
				'price_override' 						=> ($this->tm_epo_global_override_product_price=='no')
															? 0
															: ($this->tm_epo_global_override_product_price=='yes')
																? 1
																: !empty($this->tm_meta_cpf['price_override'])?1:0,
				'form_prefix' 							=> $form_prefix,
				'type'  								=> esc_html( $product->product_type ),
				'price' 								=> esc_html( ( is_object( $product ) ? apply_filters( 'woocommerce_tm_final_price', $price['price'],$product ) : '' ) ),
				'is_vat_exempt' 						=> $is_vat_exempt,
				'non_base_location_prices' 				=> $non_base_location_prices,
				'taxable' 								=> $taxable,
				'tax_display_mode' 						=> $tax_display_mode,
				'prices_include_tax' 					=> $cart->prices_include_tax,
				'tax_rate' 								=> $tax_rate,
				'base_tax_rate' 						=> $base_tax_rate,
				'tax_string' 							=> $tax_string,
				'product_price_rules' 					=> esc_html(json_encode( (array) $price['product'] ) ),
				'fields_price_rules' 					=> 0,
				//'tm_epo_dpd_suffix' 					=> $this->tm_epo_dpd_suffix,
				//'tm_epo_dpd_prefix' 					=> $this->tm_epo_dpd_prefix,
				'force_quantity' 						=> $force_quantity,
				'product_id' 							=> $product_id,
				'epo_internal_counter' 					=> $_epo_internal_counter,
				'is_from_shortcode' 					=> $is_from_shortcode
			) ),
			$this->_namespace,
			TM_EPO_TEMPLATE_PATH
		);

	}

	public function get_theme($var=''){
		$out='';
		if (function_exists('wp_get_theme')){
			$theme = wp_get_theme();
			if ($theme){
				$out=$theme->get( $var );
			}			
		}
		return $out;
	}
	
	public function tm_add_inline_style_qv(){
		if (!empty($this->inline_styles)){
			echo '<style type="text/css">';
			echo $this->inline_styles;
			echo '</style>';
		}
	}

	public function tm_add_inline_style(){
		if (!empty($this->inline_styles)){
			if ($this->is_quick_view() || $this->is_bto || $this->tm_epo_global_load_generated_styles_inline=="yes"){
				$this->tm_add_inline_style_qv();
			}else{
				echo '<script type="text/javascript">var data=\''.$this->inline_styles.'\';jQuery("<style type=\"text/css\">" + data + "</style>").appendTo(document.head);</script>';				
			}			
		}
	}

	public function upload_file($file) {
		include_once( ABSPATH . 'wp-admin/includes/file.php' );
		include_once( ABSPATH . 'wp-admin/includes/media.php' );
		add_filter( 'upload_dir',  array( $this, 'upload_dir_trick' ) );
		$upload = wp_handle_upload( $file, array( 'test_form' => false ) );
		remove_filter( 'upload_dir',  array( $this, 'upload_dir_trick' ) );
		return $upload;
	}

	public function upload_dir_trick( $param ) {
		global $woocommerce;
		$this->unique_dir = apply_filters('wc_epo_upload_unique_dir',md5( $woocommerce->session->get_customer_id() ) );
		$subdir = $this->upload_dir . $this->unique_dir;
		if ( empty( $param['subdir'] ) ) {
			$param['path']   = $param['path'] . $subdir;
			$param['url']    = $param['url']. $subdir;
			$param['subdir'] = $subdir;
		} else {
			$param['path']   = str_replace( $param['subdir'], $subdir, $param['path'] );
			$param['url']    = str_replace( $param['subdir'], $subdir, $param['url'] );
			$param['subdir'] = str_replace( $param['subdir'], $subdir, $param['subdir'] );
		}
		return $param;
	}	

	/* Append name_inc functions (required for condition logic to check if an element is visible) */
	public function tm_fill_element_names($post_id=0, $global_epos=array(), $product_epos=array(), $form_prefix="") {
		$global_price_array = $global_epos;
		$local_price_array  = $product_epos;

		$global_prices=array( 'before'=>array(), 'after'=>array() );
		foreach ( $global_price_array as $priority=>$priorities ) {
			foreach ( $priorities as $pid=>$field ) {
				if (isset($field['sections']) && is_array($field['sections'])){
					foreach ( $field['sections'] as $section_id=>$section ) {
						if ( isset( $section['sections_placement'] ) ) {
							$global_prices[$section['sections_placement']][$priority][$pid]['sections'][$section_id]=$section;
						}
					}
				}
			}
		}
		$unit_counter  		= 0;
		$field_counter  	= 0;
		$element_counter	= 0;
		// global options before local
		foreach ( $global_prices['before'] as $priority=>$priorities ) {
			foreach ( $priorities as $pid=>$field ) {
				$args=array(
					'priority'  		=> $priority,
					'pid'  				=> $pid,
					'unit_counter'  	=> $unit_counter,
					'field_counter'  	=> $field_counter,
					'element_counter'  	=> $element_counter
				);
				$_return=$this->fill_builder_display( $global_epos, $field, 'before', $args , $form_prefix);
				extract( $_return, EXTR_OVERWRITE );
			}
		}
		// local options
		if ( is_array( $local_price_array ) && sizeof( $local_price_array ) > 0 ) {
			$attributes = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );
			if ( is_array( $attributes ) && count( $attributes )>0 ) {
				foreach ( $local_price_array as $field ) {
					if ( isset( $field['name'] ) && isset( $attributes[$field['name']] ) && !$attributes[$field['name']]['is_variation'] ) {
						$attribute=$attributes[$field['name']];
						$name_inc="";
						$field_counter=0;
						if ( $attribute['is_taxonomy'] ) {													
							switch ( $field['type'] ) {
							case "select":								
								$element_counter++;
								break;
							case "radio":
							case "checkbox":					
								$element_counter++;
								break;
							}
						} else {
							switch ( $field['type'] ) {
							case "select":
								$element_counter++;
								break;
							case "radio":
							case "checkbox":
								$element_counter++;
								break;
							}
						}
						$unit_counter++;
					}
				}
			}
		}
		// global options after local
		foreach ( $global_prices['after'] as $priority=>$priorities ) {
			foreach ( $priorities as $pid=>$field ) {
				$args=array(
					'priority'  		=> $priority,
					'pid'  				=> $pid,
					'unit_counter'  	=> $unit_counter,
					'field_counter'  	=> $field_counter,
					'element_counter'  	=> $element_counter
				);
				$_return=$this->fill_builder_display( $global_epos, $field, 'after', $args, $form_prefix );
				extract( $_return, EXTR_OVERWRITE );
			}
		}
		return $global_epos;
	}

	private function tm_apply_filter($value="",$filter=""){
		if (!empty($filter)){
			return apply_filters($filter,$value);
		}
		return $value;
	}

	public function get_builder_element($element,$builder,$current_builder,$index=false,$alt="",$wpml_section_fields=array(),$identifier="sections",$apply_filters=""){
		$use_wpml=false;
		$use_original_builder=false;
		if(TM_EPO_WPML()->is_active() && $index!==false){
			if(isset( $current_builder[$identifier."_uniqid"] ) 
				&& isset($builder[$identifier."_uniqid"]) 
				&& isset($builder[$identifier."_uniqid"][$index]) ){
				// get index of element id in internal array
				$get_current_builder_uniqid_index = array_search($builder[$identifier."_uniqid"][$index], $current_builder[$identifier."_uniqid"] );
				if ($get_current_builder_uniqid_index!==NULL && $get_current_builder_uniqid_index!==FALSE){
					$index = $get_current_builder_uniqid_index;
					$use_wpml=true;
				}else{
					$use_original_builder=true;
				}
			}				
		}
		if ( isset($builder[$element]) ){
			if(!$use_original_builder && $use_wpml && ( (is_array($wpml_section_fields) && in_array($element, $wpml_section_fields)) || $wpml_section_fields===true)){
				if(isset($current_builder[$element])){
					if($index!==false){
						if(isset($current_builder[$element][$index])){
							return $this->tm_apply_filter(TM_EPO_HELPER()->build_array($current_builder[$element][$index],$builder[$element][$index]),$apply_filters);							
						}else{
							return $this->tm_apply_filter($alt,$apply_filters);
						}
					}else{
						return $this->tm_apply_filter(TM_EPO_HELPER()->build_array($current_builder[$element],$builder[$element]),$apply_filters);
					}
				}
			}
			if($index!==false){
				if(isset($builder[$element][$index])){
					return $this->tm_apply_filter($builder[$element][$index],$apply_filters);
				}else{
					return $this->tm_apply_filter($alt,$apply_filters);
				}
			}else{
				return $this->tm_apply_filter($builder[$element],$apply_filters);
			}
		}else{
			return $this->tm_apply_filter($alt,$apply_filters);
		}
		
	}

	/**
	 * Gets a list of all the Extra Product Options (local and global)
	 * for the specific $post_id.
	 */
	public function get_product_tm_epos( $post_id=0 ,$form_prefix="") {

		if ( empty( $post_id ) || apply_filters('wc_epo_disable',false,$post_id) ) {
			return array();
		}

		if(!empty($this->cpf[$post_id])){
			return $this->cpf[$post_id];
		}
		$this->set_tm_meta($post_id);
		if ( !$this->check_enable()) {
			return array();
		}
		$in_cat=array();

		$tmglobalprices=array();

		$terms = get_the_terms( $post_id, 'product_cat' );
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$in_cat[] = $term->term_id;
			}
		}

		// get all categories (no matter the language)
		$_all_categories = TM_EPO_WPML()->get_terms( null, 'product_cat', array( 'fields' => "ids", 'hide_empty' => false ) );

		if ( !$_all_categories ) {
			$_all_categories = array();
		}
		
		/* Get Local options */
		$args = array(
			'post_type'     => TM_EPO_LOCAL_POST_TYPE,
			'post_status'   => array( 'publish' ), // get only enabled extra options
			'numberposts'   => -1,
			'orderby'       => 'menu_order',
			'order'       	=> 'asc','suppress_filters' => true,
			'post_parent'   => floatval(TM_EPO_WPML()->get_original_id( $post_id ))
		);
		TM_EPO_WPML()->remove_sql_filter();
		$tmlocalprices = get_posts( $args );
		TM_EPO_WPML()->restore_sql_filter();

		
		$tm_meta_cpf_global_forms=(isset($this->tm_meta_cpf['global_forms']) && is_array($this->tm_meta_cpf['global_forms']) )?$this->tm_meta_cpf['global_forms']:array();
		foreach ($tm_meta_cpf_global_forms as $key => $value) {
			$tm_meta_cpf_global_forms[$key]=absint($value);
		}
		$tm_meta_cpf_global_forms_added=array();
		if (!$this->tm_meta_cpf['exclude']){
			
			$meta_array = TM_EPO_HELPER()->build_meta_query('OR','tm_meta_disable_categories',1,'!=', 'NOT EXISTS');
			
			$args = array(
				'post_type'     => TM_EPO_GLOBAL_POST_TYPE,
				'post_status'   => array( 'publish' ), // get only enabled global extra options
				'numberposts'   => -1,
				'orderby'       => 'date',
				'order'       	=> 'asc',
				'meta_query' => $meta_array
			);
			$tmp_tmglobalprices  = get_posts( $args );
						
			if ( $tmp_tmglobalprices ) {
				$wpml_tmp_tmglobalprices=array();
				$wpml_tmp_tmglobalprices_added=array();
				foreach ( $tmp_tmglobalprices as $price ) {
					/* Get Global options that belong to the product categories */		
					if(!empty($in_cat) && has_term( $in_cat, 'product_cat', $price) ) {
						if (TM_EPO_WPML()->is_active()){
							$price_meta_lang=get_post_meta( $price->ID, TM_EPO_WPML_LANG_META, true );
							$original_product_id = floatval(TM_EPO_WPML()->get_original_id( $price->ID,$price->post_type ));
							$double_check_disable_categories=get_post_meta( $original_product_id, "tm_meta_disable_categories", true );
							if (!$double_check_disable_categories){
								if ($price_meta_lang==TM_EPO_WPML()->get_lang() 
									|| ($price_meta_lang=='' && TM_EPO_WPML()->get_lang()==TM_EPO_WPML()->get_default_lang()) 
									){
									$tmglobalprices[]=$price;
									$tm_meta_cpf_global_forms_added[]=$price->ID;
									if ($price_meta_lang!=TM_EPO_WPML()->get_default_lang() && $price_meta_lang!=''){
										$wpml_tmp_tmglobalprices_added[$original_product_id]=$price;
									}
								}else{
									if ($price_meta_lang==TM_EPO_WPML()->get_default_lang() || $price_meta_lang==''){
										$wpml_tmp_tmglobalprices[$original_product_id]=$price;
									}
								}
							}
						}else{
							$tmglobalprices[]=$price;
							$tm_meta_cpf_global_forms_added[]=$price->ID;
						}
					}
					/* Get Global options that have no catergory set (they apply to all products) */
					if( !has_term( $_all_categories, 'product_cat', $price) ) {
						if (TM_EPO_WPML()->is_active()){
							$price_meta_lang=get_post_meta( $price->ID, TM_EPO_WPML_LANG_META, true );
							$original_product_id = floatval(TM_EPO_WPML()->get_original_id( $price->ID,$price->post_type ));
							$double_check_disable_categories=get_post_meta( $original_product_id, "tm_meta_disable_categories", true );
							if (!$double_check_disable_categories){
								if ($price_meta_lang==TM_EPO_WPML()->get_lang() 
									|| ($price_meta_lang=='' && TM_EPO_WPML()->get_lang()==TM_EPO_WPML()->get_default_lang()) 
									){
									$tmglobalprices[]=$price;
									$tm_meta_cpf_global_forms_added[]=$price->ID;
									if ($price_meta_lang!=TM_EPO_WPML()->get_default_lang() && $price_meta_lang!=''){
										$wpml_tmp_tmglobalprices_added[$original_product_id]=$price;
									}
								}else{
									if ($price_meta_lang==TM_EPO_WPML()->get_default_lang() || $price_meta_lang==''){
										$wpml_tmp_tmglobalprices[$original_product_id]=$price;
									}
								}
							}
						}else{
							$tmglobalprices[]=$price;
							$tm_meta_cpf_global_forms_added[]=$price->ID;
						}
					}
				}
				// replace missing translation with original
				if (TM_EPO_WPML()->is_active()){
					$wpml_gp_keys = array_keys($wpml_tmp_tmglobalprices);
					foreach ($wpml_gp_keys as $key => $value) {
						if (!isset($wpml_tmp_tmglobalprices_added[$value])){
							$tmglobalprices[]=$wpml_tmp_tmglobalprices[$value];
							$tm_meta_cpf_global_forms_added[]=$price->ID;
						}
					}
				}

			}
			
			/* Get Global options that apply to the product */
			$args = array(
				'post_type'     => TM_EPO_GLOBAL_POST_TYPE,
				'post_status'   => array( 'publish' ), // get only enabled global extra options
				'numberposts'   => -1,
				'orderby'       => 'date',
				'order'       	=> 'asc',
				'meta_query' => array(
					array(
						'key' => 'tm_meta_product_ids',
						'value' => '"'.$post_id.'";',
						'compare' => 'LIKE'
					)
				)
			);
			$tmglobalprices_products = get_posts( $args );
			
			/* Merge Global options */
			if ( $tmglobalprices_products ) {
				$global_id_array=array();
				if ( isset($tmglobalprices) ) {
					foreach ( $tmglobalprices as $price ) {
						$global_id_array[]=$price->ID;
					}
				}else{
					$tmglobalprices=array();
				}
				foreach ( $tmglobalprices_products as $price ) {
					if (!in_array($price->ID, $global_id_array)){
						$tmglobalprices[]=$price;
						$tm_meta_cpf_global_forms_added[]=$price->ID;
					}				
				}
			}
		}
		$tm_meta_cpf_global_forms_added=array_unique($tm_meta_cpf_global_forms_added);
		foreach ($tm_meta_cpf_global_forms as $key => $value) {
			if (!in_array($value, $tm_meta_cpf_global_forms_added)){
				if (TM_EPO_WPML()->is_active()){

					$tm_meta_lang= get_post_meta( $value , TM_EPO_WPML_LANG_META , true );
	                if (empty($tm_meta_lang)){
	                    $tm_meta_lang=TM_EPO_WPML()->get_default_lang();
	                }
	                $meta_query=TM_EPO_HELPER()->build_meta_query('AND',TM_EPO_WPML_LANG_META,TM_EPO_WPML()->get_lang(),'=', 'EXISTS');
					$meta_query[] =  array(
						'key' => TM_EPO_WPML_PARENT_POSTID, 
						'value' => $value,
						'compare' => '='
					);	                
	                $query = new WP_Query( 
                        array(
                            'post_type'     => TM_EPO_GLOBAL_POST_TYPE,
                            'post_status'   => array( 'publish' ), 
                            'numberposts'   => -1,
                            'orderby'       => 'date',
                            'order'         => 'asc',
                            'meta_query'    => $meta_query
                        ));
	                if ( !empty($query->posts)  ){
						$tmglobalprices[]=get_post($query->post->ID);
					}elseif(empty($query->posts)){
						$tmglobalprices[]=get_post($value);
					}
				}else{
					$ispostactive=get_post($value);
					if ($ispostactive->post_status=='publish'){
						$tmglobalprices[]=get_post($value);
					}					
				}				
			}
		}

		// Add current product to Global options array (has to be last to not conflict)
		$tmglobalprices[]=get_post($post_id);

		// End of DB init

		$product_epos=array();
		$global_epos=array();
		$epos_prices=array();

		if ( $tmglobalprices ) {
			$wpml_section_fields=array();			
			foreach (TM_EPO_BUILDER()->_section_elements as $key => $value) {
				if(isset($value['id']) && empty($value['wpmldisable'])){
					$wpml_section_fields[$value['id']]=$value['id'];
				}
			}			
			
			foreach ( $tmglobalprices as $price ) {
				if (!is_object($price)){
					continue;
				}

				$original_product_id = $price->ID;
				if(TM_EPO_WPML()->is_active()){
					$wpml_is_original_product=TM_EPO_WPML()->is_original_product($price->ID,$price->post_type);
					if (!$wpml_is_original_product){
						$original_product_id = floatval(TM_EPO_WPML()->get_original_id( $price->ID,$price->post_type ));
					}
		        }

				$tmcp_id  	= absint( $original_product_id );
				$tmcp_meta	= get_post_meta( $original_product_id, 'tm_meta', true );
				$enabled_roles	= get_post_meta( $original_product_id, 'tm_meta_enabled_roles', true );
				$disabled_roles	= get_post_meta( $original_product_id, 'tm_meta_disabled_roles', true );

				if (!empty($enabled_roles) || !empty($disabled_roles)){
					$enable=false;
					if (!is_array($enabled_roles)){
						$enabled_roles=array($enabled_roles);
					}
					if (!is_array($disabled_roles)){
						$disabled_roles=array($disabled_roles);
					}
					// Get all roles
					$current_user = wp_get_current_user();

					foreach ($enabled_roles as $key => $value) {
						if($value=="@everyone"){
							$enable=true;
						}
						if($value=="@loggedin" && is_user_logged_in()){
							$enable=true;
						}
					}

					if ( $current_user instanceof WP_User ){	
						$roles = $current_user->roles;			
						
						if (is_array($roles)){

							foreach ($roles as $key => $value) {
								if (in_array($value, $enabled_roles)){
									$enable=true;
									break;
								}
							}

							foreach ($roles as $key => $value) {
								if (in_array($value, $disabled_roles)){
									$enable=false;
									break;
								}
							}

						}

					}

					if( !$enable){
						continue;
					}
				}

				$current_builder	= get_post_meta( $price->ID, 'tm_meta_wpml', true );
				if (!$current_builder){
					$current_builder=array();
				}else{
					if (!isset($current_builder['tmfbuilder'])){
						$current_builder['tmfbuilder'] = array();
					}
					$current_builder = $current_builder['tmfbuilder'];
				}

				$priority  	= isset( $tmcp_meta['priority'] )?absint( $tmcp_meta['priority'] ):1000;

				if ( isset( $tmcp_meta['tmfbuilder'] ) ) {

					$global_epos[$priority][$tmcp_id]['is_form']   		= 1;
					$global_epos[$priority][$tmcp_id]['is_taxonomy'] 	= 0;
					$global_epos[$priority][$tmcp_id]['name']    		= $price->post_title;
					$global_epos[$priority][$tmcp_id]['description'] 	= $price->post_excerpt;
					$global_epos[$priority][$tmcp_id]['sections'] 		= array();

					$builder=$tmcp_meta['tmfbuilder'];
					if ( is_array( $builder ) && count( $builder )>0 && isset( $builder['element_type'] ) && is_array( $builder['element_type'] ) && count( $builder['element_type'] )>0 ) {
						// All the elements
						$_elements=$builder['element_type'];
						// All element sizes
						$_div_size=$builder['div_size'];

						// All sections (holds element count for each section)
						$_sections=$builder['sections'];
						// All section sizes
						$_sections_size=$builder['sections_size'];
						// All section styles
						$_sections_style=$builder['sections_style'];
						// All section placements
						$_sections_placement=$builder['sections_placement'];
						
						$_sections_slides=isset($builder['sections_slides'])?$builder['sections_slides']:'';


						if ( !is_array( $_sections ) ) {
							$_sections=array( count( $_elements ) );
						}
						if ( !is_array( $_sections_size ) ) {
							$_sections_size=array_fill(0, count( $_sections ) ,"w100");
						}
						if ( !is_array( $_sections_style ) ) {
							$_sections_style=array_fill(0, count( $_sections ) ,"");
						}
						if ( !is_array( $_sections_placement ) ) {
							$_sections_placement=array_fill(0, count( $_sections ) ,"before");
						}

						if ( !is_array( $_sections_slides ) ) {
							$_sections_slides=array_fill(0, count( $_sections ) ,"");
						}

						$_helper_counter=0;
						$_counter=array();

						for ( $_s = 0; $_s < count( $_sections ); $_s++ ) {
							$_sections_uniqid 	= $this->get_builder_element('sections_uniqid',$builder,$current_builder,$_s,TM_EPO_HELPER()->tm_temp_uniqid(count( $_sections )),$wpml_section_fields);
							
							$global_epos[$priority][$tmcp_id]['sections'][$_s]=array(
								'total_elements'		=> $_sections[$_s],
								'sections_size'			=> $_sections_size[$_s],
								'sections_slides' 		=> isset($_sections_slides[$_s])?$_sections_slides[$_s]:"",
								'sections_style'		=> $_sections_style[$_s],
								'sections_placement'	=> $_sections_placement[$_s],
								'sections_uniqid'		=> $_sections_uniqid,
								'sections_clogic'		=> $this->get_builder_element('sections_clogic',$builder,$current_builder,$_s,false,$wpml_section_fields),
								'sections_logic'		=> $this->get_builder_element('sections_logic',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'sections_class'		=> $this->get_builder_element('sections_class',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'sections_type'			=> $this->get_builder_element('sections_type',$builder,$current_builder,$_s,"",$wpml_section_fields),

								'label_size'			=> $this->get_builder_element('section_header_size',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'label'					=> $this->get_builder_element('section_header_title',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'label_color' 			=> $this->get_builder_element('section_header_title_color',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'label_position' 		=> $this->get_builder_element('section_header_title_position',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'description' 			=> $this->get_builder_element('section_header_subtitle',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'description_position' 	=> $this->get_builder_element('section_header_subtitle_position',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'description_color' 	=> $this->get_builder_element('section_header_subtitle_color',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'divider_type' 			=> $this->get_builder_element('section_divider_type',$builder,$current_builder,$_s,"",$wpml_section_fields),
							);
							
							for ( $k0 = $_helper_counter; $k0 < intval( $_helper_counter+intval( $_sections[$_s] ) ); $k0++ ) {
								if (!isset($_elements[$k0])){
									continue;
								}
								$current_element = $_elements[$k0];
								$wpml_element_fields=array();
								if (isset(TM_EPO_BUILDER()->elements_array[$current_element])){
									foreach (TM_EPO_BUILDER()->elements_array[$current_element] as $key => $value) {
										if(isset($value['id']) && empty($value['wpmldisable'])){
											$wpml_element_fields[$value['id']]=$value['id'];
										}
									}
								}
								if ( isset( $current_element ) && isset($this->tm_original_builder_elements[$current_element]) ) {
									if ( !isset( $_counter[$current_element] ) ) {
										$_counter[$current_element]=0;
									}else {
										$_counter[$current_element]++;
									}
									$current_counter = $_counter[$current_element];

									$_options=array();									
									$_regular_price=array();
									$_regular_price_filtered=array();
									$_original_regular_price_filtered=array();
									$_regular_price_type=array();
									$_new_type=$current_element;
									$_prefix="";
									$_min_price0='';
									$_min_price='';
									$_max_price='';
									$_regular_currencies=array();
									$price_per_currencies=array();
									$_description=false;
									$_extra_multiple_choices=false;
									$_use_lightbox='';
									if ($this->tm_original_builder_elements[$current_element]){
										if ($this->tm_original_builder_elements[$current_element]["_is_addon"]==true && $this->tm_original_builder_elements[$current_element]["is_post"]=="display"){											
											$_prefix=$current_element."_";
										}
										if ($this->tm_original_builder_elements[$current_element]["type"]=="single" || $this->tm_original_builder_elements[$current_element]["type"]=="multipleallsingle"){
											$_prefix=$current_element."_";
											$_is_field_required = $this->get_builder_element($_prefix.'required',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element);
											$_changes_product_image=$this->get_builder_element($_prefix.'changes_product_image',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element);
											$_use_images=$this->get_builder_element($_prefix.'use_images',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element);
											
											if ( empty( $builder[$current_element.'_price'][$current_counter] ) ) {
												//$builder[$current_element.'_price'][$current_counter]=0;
											}

											$new_currency = false;
											$mt_prefix = TM_EPO_HELPER()->get_currency_price_prefix();
											
											$_price	= $builder[$current_element.'_price'][$current_counter];
											$_original_regular_price_filtered = $_price;
											if(isset($builder[$current_element.'_sale_price'][$current_counter]) && $builder[$current_element.'_sale_price'][$current_counter]!==''){
												$_price	= $builder[$current_element.'_sale_price'][$current_counter];
											}

											$_current_currency_price = isset($builder[$current_element.'_price'.$mt_prefix]) && isset($builder[$current_element.'_price'.$mt_prefix][$current_counter])?$builder[$current_element.'_price'.$mt_prefix][$current_counter]:'';
											$_current_currency_sale_price = isset($builder[$current_element.'_sale_price'.$mt_prefix]) && isset($builder[$current_element.'_sale_price'.$mt_prefix][$current_counter])?$builder[$current_element.'_sale_price'.$mt_prefix][$current_counter]:'';
											if($mt_prefix!=='' && $_current_currency_price && $_current_currency_price!==''){
												$_price = $_current_currency_price;
												$_original_regular_price_filtered = $_price;
												if($_current_currency_sale_price && $_current_currency_sale_price!==''){
													$_price = $_current_currency_sale_price;
												}
												$_regular_currencies = array(get_woocommerce_currency());
												$new_currency = true;
											}
											foreach (TM_EPO_HELPER()->get_currencies() as $currency) {
												$mt_prefix = TM_EPO_HELPER()->get_currency_price_prefix($currency);
												$_current_currency_price = isset($builder[$current_element.'_price'.$mt_prefix][$current_counter])?$builder[$current_element.'_price'.$mt_prefix][$current_counter]:'';
												$_current_currency_sale_price = isset($builder[$current_element.'_sale_price'.$mt_prefix][$current_counter])?$builder[$current_element.'_sale_price'.$mt_prefix][$current_counter]:'';
												
												if($_current_currency_sale_price && $_current_currency_sale_price!==''){
													$_current_currency_price=$_current_currency_sale_price;
												}
												if ($_current_currency_price!==''){
													$price_per_currencies[$currency]=array( array( wc_format_decimal( $_current_currency_price, false, true ) ) );
												}												
											}
											
											$_regular_price=array( array( wc_format_decimal( $_price, false, true ) ) );
											
											$_regular_price_type=isset($builder[$current_element.'_price_type'][$current_counter])
												?array( array( ( $builder[$current_element.'_price_type'][$current_counter] ) ) )
												:array();

											$_for_filter_price_type=isset($builder[$current_element.'_price_type'][$current_counter])
												?$builder[$current_element.'_price_type'][$current_counter]
												:"";

											if (!$new_currency){
												$_price	= apply_filters( 'wc_epo_get_current_currency_price', $_price, $_for_filter_price_type );
												$_original_regular_price_filtered = apply_filters( 'wc_epo_get_current_currency_price', $_original_regular_price_filtered, $_for_filter_price_type );
											}
											
											$_price	= apply_filters( 'wc_epo_price', $_price, $_for_filter_price_type, $post_id );
											$_original_regular_price_filtered = apply_filters( 'wc_epo_price', $_original_regular_price_filtered, $_for_filter_price_type, $post_id );
											
											if ($_price!=='' && isset($builder[$current_element.'_price_type'][$current_counter]) && $builder[$current_element.'_price_type'][$current_counter]==''){
												$_min_price = $_max_price = wc_format_decimal( $_price, false, true );
												if ($_is_field_required){
													$_min_price0 = $_min_price;
												}else{
													$_min_price0 = 0;	
												}
											}else{
												$_min_price = $_max_price = false;
												$_min_price0 = 0;
											}
											
											$_regular_price_filtered=array( array( wc_format_decimal( $_price, false, true ) ) );
											$_original_regular_price_filtered = array( array( wc_format_decimal( $_original_regular_price_filtered, false, true ) ) );

										}elseif ($this->tm_original_builder_elements[$current_element]["type"]=="multiple" || $this->tm_original_builder_elements[$current_element]["type"]=="multipleall"  || $this->tm_original_builder_elements[$current_element]["type"]=="multiplesingle"){
											$_prefix=$current_element."_";
											$_is_field_required = $this->get_builder_element($_prefix.'required',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element);
											$_changes_product_image=$this->get_builder_element($_prefix.'changes_product_image',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element);
											$_use_images=$this->get_builder_element($_prefix.'use_images',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element);
											$_use_lightbox=$this->get_builder_element($_prefix.'use_lightbox',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element);

											if ( isset( $builder['multiple_'.$current_element.'_options_price'][$current_counter] ) ) {
												if ( empty( $builder['multiple_'.$current_element.'_options_price'][$current_counter] ) ) {
													//$builder['multiple_'.$current_element.'_options_price'][$current_counter]=0;
												}

												$_prices=$builder['multiple_'.$current_element.'_options_price'][$current_counter];
												$_original_prices = $_prices;
												$_sale_prices=$_prices;												
												if ( isset( $builder['multiple_'.$current_element.'_options_sale_price'][$current_counter] ) ) {
													$_sale_prices=$builder['multiple_'.$current_element.'_options_sale_price'][$current_counter];
												}
												$_prices=TM_EPO_HELPER()->merge_price_array($_prices,$_sale_prices);
												
												$mt_prefix = TM_EPO_HELPER()->get_currency_price_prefix();
												$_current_currency_prices = isset($builder['multiple_'.$current_element.'_options_price'.$mt_prefix][$current_counter])?$builder['multiple_'.$current_element.'_options_price'.$mt_prefix][$current_counter]:'';
												$_original_current_currency_prices = $_current_currency_prices;
												$_current_currency_sale_prices = isset($builder['multiple_'.$current_element.'_options_sale_price'.$mt_prefix][$current_counter])?$builder['multiple_'.$current_element.'_options_sale_price'.$mt_prefix][$current_counter]:'';
												$_current_currency_prices=TM_EPO_HELPER()->merge_price_array($_current_currency_prices,$_current_currency_sale_prices);

												$_values=$this->get_builder_element('multiple_'.$current_element.'_options_value',$builder,$current_builder,$current_counter,"",true,$current_element);
												$_titles=$this->get_builder_element('multiple_'.$current_element.'_options_title',$builder,$current_builder,$current_counter,"",true,$current_element);
												$_images=$this->get_builder_element('multiple_'.$current_element.'_options_image',$builder,$current_builder,$current_counter,array(),true,$current_element,'tm_image_url');
												$_imagesp=$this->get_builder_element('multiple_'.$current_element.'_options_imagep',$builder,$current_builder,$current_counter,array(),true,$current_element,'tm_image_url');
												$_imagesl=$this->get_builder_element('multiple_'.$current_element.'_options_imagel',$builder,$current_builder,$current_counter,array(),true,$current_element,'tm_image_url');
												
												foreach (TM_EPO_HELPER()->get_currencies() as $currency) {
													$mt_prefix = TM_EPO_HELPER()->get_currency_price_prefix($currency);
													$_current_currency_price = isset($builder['multiple_'.$current_element.'_options_price'.$mt_prefix][$current_counter])
																				?$builder['multiple_'.$current_element.'_options_price'.$mt_prefix][$current_counter]
																				:'';
													$_current_currency_sale_price = isset($builder['multiple_'.$current_element.'_options_sale_price'.$mt_prefix][$current_counter])
																				?$builder['multiple_'.$current_element.'_options_sale_price'.$mt_prefix][$current_counter]
																				:'';

													$_current_currency_price=TM_EPO_HELPER()->merge_price_array($_current_currency_price,$_current_currency_sale_price);
													
													$price_per_currencies[$currency]=$_current_currency_price;
													foreach ( $_prices as $_n=>$_price ) {
														$to_price = '';
														if(is_array($_current_currency_price) && isset($_current_currency_price[$_n])){
															$to_price=$_current_currency_price[$_n];
														}
														$price_per_currencies[$currency][esc_attr( ( $_values[$_n] ) )."_".$_n] = array( wc_format_decimal( $to_price, false, true ) );
													}
												}
												
												if ($_changes_product_image=="images" && $_use_images==""){
													$_imagesp=$_images;
													$_images=array();
													$_changes_product_image="custom";
												}
												if($_use_images==""){
													$_use_lightbox="";
												}

												$_url=$this->get_builder_element('multiple_'.$current_element.'_options_url',$builder,$current_builder,$current_counter,array(),true,$current_element);
												$_description=$this->get_builder_element('multiple_'.$current_element.'_options_description',$builder,$current_builder,$current_counter,array(),true,$current_element);
												
												foreach (TM_EPO_BUILDER()->extra_multiple_options as $__key => $__name) {
													$_extra_name = $__name["name"];
													$_extra_multiple_choices[$_extra_name]=$this->get_builder_element('multiple_'.$current_element.'_options_'.$_extra_name,$builder,$current_builder,$current_counter,array(),true,$current_element);													
												}

												$_prices_type=$this->get_builder_element('multiple_'.$current_element.'_options_price_type',$builder,$current_builder,$current_counter,array(),true,$current_element);
												$_values_c=$_values;
												$mt_prefix = TM_EPO_HELPER()->get_currency_price_prefix();
												foreach ( $_prices as $_n=>$_price ) {
													$new_currency = false;
													if ($mt_prefix!=='' 
														&& $_current_currency_prices!=='' 
														&& is_array($_current_currency_prices) 
														&& isset($_current_currency_prices[$_n])
														&& $_current_currency_prices[$_n]!=''){
														$new_currency = true;
														$_price = $_current_currency_prices[$_n];
														$_original_prices[$_n]=$_original_current_currency_prices[$_n];
														$_regular_currencies[esc_attr( ( $_values[$_n] ) )."_".$_n] = array(get_woocommerce_currency());
													}
													$_f_price = wc_format_decimal( $_price, false, true );
													$_regular_price[esc_attr( ( $_values[$_n] ) )."_".$_n]=array( $_f_price );
													$_for_filter_price_type = isset($_prices_type[$_n])? $_prices_type[$_n] :"";
													
													if(!$new_currency){
														$_price	= apply_filters( 'wc_epo_get_current_currency_price',$_price, $_for_filter_price_type);
														$_original_prices[$_n]= apply_filters( 'wc_epo_get_current_currency_price',$_original_prices[$_n], $_for_filter_price_type);
													}else{

													}
													$_price	= apply_filters( 'wc_epo_price', $_price, $_for_filter_price_type, $post_id );
													$_original_prices[$_n] = apply_filters( 'wc_epo_price', $_original_prices[$_n], $_for_filter_price_type, $post_id );
													$_f_price = wc_format_decimal( $_price, false, true );

													$_regular_price_filtered[esc_attr( ( $_values[$_n] ) )."_".$_n]=array( wc_format_decimal( $_price, false, true ) );
													$_original_regular_price_filtered [esc_attr( ( $_values[$_n] ) )."_".$_n]=array( wc_format_decimal( $_original_prices[$_n], false, true ) );
													$_regular_price_type[esc_attr( ( $_values[$_n] ) )."_".$_n]=isset($_prices_type[$_n])?array( ( $_prices_type[$_n] ) ):array('');
													$_options[esc_attr( ( $_values[$_n] ) )."_".$_n]=$_titles[$_n];	
													$_values_c[$_n]=$_values[$_n]."_".$_n;

													if(isset($_prices_type[$_n]) && $_prices_type[$_n]=='' && ((isset($builder[$current_element.'_price_type'][$current_counter]) && $builder[$current_element.'_price_type'][$current_counter]=='') || !isset($builder[$current_element.'_price_type'][$current_counter]))){
														if ($_min_price!==false && $_price!==''){
															if ($_min_price ===''){
																$_min_price = $_f_price;
															}else{
																if ($_min_price > $_f_price){
																	$_min_price = $_f_price;
																}
															}
															if ($_min_price0 ===''){
																if($_is_field_required){
																	$_min_price0 = floatval($_min_price);
																}else{
																	$_min_price0 = 0;
																}																
															}else{
																if ($_is_field_required && $_min_price0 > floatval($_min_price)){
																	$_min_price0 = floatval($_min_price);
																}
															}
															if ($_max_price ===''){
																$_max_price = $_f_price;
															}else{
																if ($_max_price < $_f_price){
																	$_max_price = $_f_price;
																}
															}
														}else{
															if ($_price===''){
																$_min_price0 = 0;
															}
														}
													}else{
														$_min_price = $_max_price = false;
														if ($_min_price0 ===''){
															$_min_price0 = 0;
														}else{
															if ($_min_price0 > floatval($_min_price)){
																$_min_price0 = floatval($_min_price);
															}
														}
													}
												}
											}											
										}
									}
									$default_value ="";
									if(isset( $builder['multiple_'.$current_element.'_options_default_value'][$current_counter] )){
										$default_value = $builder['multiple_'.$current_element.'_options_default_value'][$current_counter];
									}elseif( isset($builder[$_prefix.'default_value']) && isset( $builder[$_prefix.'default_value'][$current_counter] ) ){
										$default_value = $builder[$_prefix.'default_value'][$current_counter];
									}
									$selectbox_fee=false;
									$selectbox_cart_fee=false;
									switch ( $current_element ) {

										case "selectbox":
											$_new_type="select";
											$selectbox_fee=isset($builder[$current_element.'_price_type'][$current_counter])?array( array( ( $builder[$current_element.'_price_type'][$current_counter] ) ) ):false;
											$selectbox_cart_fee=isset($builder[$current_element.'_price_type'][$current_counter])?array( array( ( $builder[$current_element.'_price_type'][$current_counter] ) ) ):false;
											break;

										case "radiobuttons":
											$_new_type="radio";
											break;

										case "checkboxes":
											$_new_type="checkbox";
											break;

									}

									$_rules=$_regular_price;
									$_rules_filtered=$_regular_price_filtered;
									foreach ( $_regular_price as $key=>$value ) {
										foreach ( $value as $k=>$v ) {																						
											$_regular_price[$key][$k]=wc_format_localized_price( $v );
											$_regular_price_filtered[$key][$k]=wc_format_localized_price( $v );
										}
									}
									$_rules_type=$_regular_price_type;
									foreach ( $_regular_price_type as $key=>$value ) {
										foreach ( $value as $k=>$v ) {
											$_regular_price_type[$key][$k]= $v ;
										}
									}
									$epos_prices[]=array(
										"min" 			=> floatval($_min_price0),
										"max" 			=> floatval($_max_price),
										"logic" 		=> $this->get_builder_element($_prefix.'logic',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
										"section_logic" => $global_epos[$priority][$tmcp_id]['sections'][$_s]['sections_logic']
									);
									if($_min_price!==false){
										$_min_price = wc_format_localized_price( $_min_price );
									}
									if($_max_price!==false){
										$_max_price = wc_format_localized_price( $_max_price );
									}
									
									/** Fix for getting right results for dates even if the users enters wrong format **/
									$format = $this->get_builder_element($_prefix.'format',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element);
									switch ($format){
										case "0":
											$date_format='d/m/Y';
											$sep="/";
											break;
										case "1":
											$date_format='m/d/Y';
											$sep="/";
											break;
										case "2":
											$date_format='d.m.Y';
											$sep=".";
											break;
										case "3":
											$date_format='m.d.Y';
											$sep=".";
											break;
										case "4":
											$date_format='d-m-Y';
											$sep="-";
											break;
										case "5":
											$date_format='m-d-Y';
											$sep="-";
											break;
									}
									$disabled_dates = $this->get_builder_element($_prefix.'disabled_dates',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element);
									if($disabled_dates){
										$disabled_dates = explode(",", $disabled_dates);
										foreach ($disabled_dates as $key => $value) {
											if (!$value){
												continue;
											}
											$value=str_replace(".", "-", $value);
											$value=str_replace("/", "-", $value);
											$value = explode("-", $value);
											switch($format){
												case "0":
												case "2":
												case "4":
													$value=$value[2]."-".$value[1]."-".$value[0];
													break;
												case "1":
												case "3":
												case "5":
													$value=$value[2]."-".$value[0]."-".$value[1];
													break;
											}
											$value_to_date = date_create($value);
											if (!$value_to_date){
												continue;
											}
											$value = date_format($value_to_date, $date_format);
											$disabled_dates[$key] = $value;											
										}
										$disabled_dates=implode(",", $disabled_dates);
										
									}
									$enabled_only_dates = $this->get_builder_element($_prefix.'enabled_only_dates',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element);
									if($enabled_only_dates){
										$enabled_only_dates = explode(",", $enabled_only_dates);
										foreach ($enabled_only_dates as $key => $value) {
											if (!$value){
												continue;
											}
											$value=str_replace(".", "-", $value);
											$value=str_replace("/", "-", $value);
											$value = explode("-", $value);
											switch($format){
												case "0":
												case "2":
												case "4":
													$value=$value[2]."-".$value[1]."-".$value[0];
													break;
												case "1":
												case "3":
												case "5":
													$value=$value[2]."-".$value[0]."-".$value[1];
													break;
											}
											$value_to_date = date_create($value);
											if (!$value_to_date){
												continue;
											}
											$value = date_format($value_to_date, $date_format);
											$enabled_only_dates[$key] = $value;
										}
										$enabled_only_dates=implode(",", $enabled_only_dates);
									}
									
									if ($current_element!="header" && $current_element!="divider"){

										$global_epos[$priority][$tmcp_id]['sections'][$_s]['elements'][]=
										array_merge(
											TM_EPO_BUILDER()->get_custom_properties($builder,$_prefix,$_counter,$_elements,$k0 , $current_builder,$current_counter,$wpml_element_fields,$current_element),
										array(
											'_' 							=> TM_EPO_BUILDER()->get_default_properties($builder,$_prefix,$_counter,$_elements,$k0),
											'internal_name'					=> $this->get_builder_element($_prefix.'internal_name',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'builder' 						=> (isset($wpml_is_original_product) && empty($wpml_is_original_product))?$current_builder:$builder,
											'section' 						=> $_sections_uniqid,
											'type'							=> $_new_type,
											'size'							=> $_div_size[$k0],

											'include_tax_for_fee_price_type'=> $this->get_builder_element($_prefix.'include_tax_for_fee_price_type',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'tax_class_for_fee_price_type' 	=> $this->get_builder_element($_prefix.'tax_class_for_fee_price_type',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),

											'hide_element_label_in_cart' 	=> $this->get_builder_element($_prefix.'hide_element_label_in_cart',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'hide_element_value_in_cart' 	=> $this->get_builder_element($_prefix.'hide_element_value_in_cart',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'hide_element_label_in_order' 	=> $this->get_builder_element($_prefix.'hide_element_label_in_order',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'hide_element_value_in_order' 	=> $this->get_builder_element($_prefix.'hide_element_value_in_order',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'hide_element_label_in_floatbox'=> $this->get_builder_element($_prefix.'hide_element_label_in_floatbox',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'hide_element_value_in_floatbox'=> $this->get_builder_element($_prefix.'hide_element_value_in_floatbox',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),

											'required'						=> $this->get_builder_element($_prefix.'required',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'use_images'					=> isset($_use_images)?$_use_images:"",
											'use_lightbox'					=> $_use_lightbox,
											'use_url'						=> $this->get_builder_element($_prefix.'use_url',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'items_per_row'					=> $this->get_builder_element($_prefix.'items_per_row',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),

											'items_per_row_r'				=> array(
																				"desktop" 			=> $this->get_builder_element($_prefix.'items_per_row',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
																				"tablets_galaxy" 	=> $this->get_builder_element($_prefix.'items_per_row_tablets_galaxy',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
																				"tablets" 			=> $this->get_builder_element($_prefix.'items_per_row_tablets',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
																				"tablets_small" 	=> $this->get_builder_element($_prefix.'items_per_row_tablets_small',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
																				"iphone6_plus" 		=> $this->get_builder_element($_prefix.'items_per_row_iphone6_plus',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
																				"iphone6" 			=> $this->get_builder_element($_prefix.'items_per_row_iphone6',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
																				"galaxy" 			=> $this->get_builder_element($_prefix.'items_per_row_samsung_galaxy',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
																				"iphone5" 			=> $this->get_builder_element($_prefix.'items_per_row_iphone5',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
																				"smartphones" 		=> $this->get_builder_element($_prefix.'items_per_row_smartphones',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
																			   ),											

											'label_size'					=> $this->get_builder_element($_prefix.'header_size',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'label'							=> $this->get_builder_element($_prefix.'header_title',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'label_position' 				=> $this->get_builder_element($_prefix.'header_title_position',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'label_color'					=> $this->get_builder_element($_prefix.'header_title_color',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'description'					=> $this->get_builder_element($_prefix.'header_subtitle',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'description_position' 			=> $this->get_builder_element($_prefix.'header_subtitle_position',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'description_color'				=> $this->get_builder_element($_prefix.'header_subtitle_color',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'divider_type'					=> $this->get_builder_element($_prefix.'divider_type',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'placeholder'					=> $this->get_builder_element($_prefix.'placeholder',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'min_chars'						=> $this->get_builder_element($_prefix.'min_chars',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'max_chars'						=> $this->get_builder_element($_prefix.'max_chars',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'hide_amount'					=> $this->get_builder_element($_prefix.'hide_amount',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'text_before_price'				=> $this->get_builder_element($_prefix.'text_before_price',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'text_after_price'				=> $this->get_builder_element($_prefix.'text_after_price',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'options'						=> $_options,
											'min_price' 					=> $_min_price,
											'max_price' 					=> $_max_price,
											'rules'							=> $_rules,
											'price_rules'					=> $_regular_price,
											'rules_filtered' 				=> $_rules_filtered,
											'price_rules_filtered' 			=> $_regular_price_filtered,
											'original_rules_filtered'		=> $_original_regular_price_filtered,
											'price_rules_type'				=> $_regular_price_type,
											'rules_type'					=> $_rules_type,
											'currencies' 					=> $_regular_currencies,
											'price_per_currencies' 			=> $price_per_currencies,
											'images'						=> isset( $_images )?$_images:"",
											'imagesp'						=> isset( $_imagesp )?$_imagesp:"",
											'imagesl'						=> isset( $_imagesl )?$_imagesl:"",
											'url' 							=> isset( $_url )?$_url:"",
											'cdescription' 					=> ($_description!==false )?$_description:"",
											'extra_multiple_choices' 		=> ($_extra_multiple_choices!==false )?$_extra_multiple_choices:array(),
											'limit'							=> $this->get_builder_element($_prefix.'limit_choices',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'exactlimit' 					=> $this->get_builder_element($_prefix.'exactlimit_choices',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'minimumlimit' 					=> $this->get_builder_element($_prefix.'minimumlimit_choices',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'clear_options' 				=> $this->get_builder_element($_prefix.'clear_options',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'option_values'					=> isset( $_values_c )?$_values_c:array(),
											'button_type' 					=> $this->get_builder_element($_prefix.'button_type',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'uniqid' 						=> $this->get_builder_element($_prefix.'uniqid',$builder,$current_builder,$current_counter,TM_EPO_HELPER()->tm_uniqid() ,$wpml_element_fields,$current_element),
											'clogic' 						=> $this->get_builder_element($_prefix.'clogic',$builder,$current_builder,$current_counter,false,$wpml_element_fields,$current_element),
											'logic' 						=> $this->get_builder_element($_prefix.'logic',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'format' 						=> $format,
											'start_year' 					=> $this->get_builder_element($_prefix.'start_year',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'end_year' 						=> $this->get_builder_element($_prefix.'end_year',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'min_date' 						=> $this->get_builder_element($_prefix.'min_date',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'max_date' 						=> $this->get_builder_element($_prefix.'max_date',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'disabled_dates' 				=> $disabled_dates,
											'enabled_only_dates' 			=> $enabled_only_dates,
											'disabled_weekdays' 			=> $this->get_builder_element($_prefix.'disabled_weekdays',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),

											'time_format' 					=> $this->get_builder_element($_prefix.'time_format',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'min_time' 						=> $this->get_builder_element($_prefix.'min_time',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'max_time' 						=> $this->get_builder_element($_prefix.'max_time',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'showhour' 						=> $this->get_builder_element($_prefix.'showhour',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'showminute' 					=> $this->get_builder_element($_prefix.'showminute',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'showsecond' 					=> $this->get_builder_element($_prefix.'showsecond',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'tranlation_hour' 				=> $this->get_builder_element($_prefix.'tranlation_hour',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'tranlation_minute' 			=> $this->get_builder_element($_prefix.'tranlation_minute',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'tranlation_second' 			=> $this->get_builder_element($_prefix.'tranlation_second',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											
											'theme' 						=> $this->get_builder_element($_prefix.'theme',$builder,$current_builder,$current_counter,"epo",$wpml_element_fields,$current_element),
											'theme_size' 					=> $this->get_builder_element($_prefix.'theme_size',$builder,$current_builder,$current_counter,"medium",$wpml_element_fields,$current_element),
											'theme_position' 				=> $this->get_builder_element($_prefix.'theme_position',$builder,$current_builder,$current_counter,"normal",$wpml_element_fields,$current_element),

											'tranlation_day' 				=> $this->get_builder_element($_prefix.'tranlation_day',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'tranlation_month' 				=> $this->get_builder_element($_prefix.'tranlation_month',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'tranlation_year' 				=> $this->get_builder_element($_prefix.'tranlation_year',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											"default_value" 				=> $default_value,
											'text_after_price' 				=> $this->get_builder_element($_prefix.'text_before_price',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'text_after_price' 				=> $this->get_builder_element($_prefix.'text_after_price',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'selectbox_fee' 				=> $selectbox_fee,
											'selectbox_cart_fee' 			=> $selectbox_cart_fee,
											'class' 						=> $this->get_builder_element($_prefix.'class',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'container_id' 					=> $this->get_builder_element($_prefix.'container_id',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'swatchmode' 					=> $this->get_builder_element($_prefix.'swatchmode',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'changes_product_image' 		=> isset($_changes_product_image)?$_changes_product_image:"",
											'min' 							=> $this->get_builder_element($_prefix.'min',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'max' 							=> $this->get_builder_element($_prefix.'max',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'freechars' 					=> $this->get_builder_element($_prefix.'freechars',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'step' 							=> $this->get_builder_element($_prefix.'step',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'pips' 							=> $this->get_builder_element($_prefix.'pips',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'noofpips' 						=> $this->get_builder_element($_prefix.'noofpips',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'show_picker_value' 			=> $this->get_builder_element($_prefix.'show_picker_value',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											
											'quantity' 						=> $this->get_builder_element($_prefix.'quantity',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'quantity_min' 					=> $this->get_builder_element($_prefix.'quantity_min',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'quantity_max' 					=> $this->get_builder_element($_prefix.'quantity_max',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'quantity_step' 				=> $this->get_builder_element($_prefix.'quantity_step',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'quantity_default_value' 		=> $this->get_builder_element($_prefix.'quantity_default_value',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),

											'validation1' 					=> $this->get_builder_element($_prefix.'validation1',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
										));
									
									}elseif ($current_element=="header"){

										$global_epos[$priority][$tmcp_id]['sections'][$_s]['elements'][]=array(
											'internal_name'			=> $this->get_builder_element($_prefix.'internal_name',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'section' 				=> $_sections_uniqid,
											'type'					=> $_new_type,
											'size'					=> $_div_size[$k0],
											'required'				=> "",
											'use_images' 			=> "",
											'use_url' 				=> "",
											'items_per_row' 		=> "",
											'label_size'			=> $this->get_builder_element($_prefix.'header_size',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'label'					=> $this->get_builder_element($_prefix.'header_title',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'label_position' 		=> $this->get_builder_element($_prefix.'header_title_position',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'label_color'			=> $this->get_builder_element($_prefix.'header_title_color',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'description'			=> $this->get_builder_element($_prefix.'header_subtitle',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'description_color'		=> $this->get_builder_element($_prefix.'header_subtitle_color',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'description_position' 	=> $this->get_builder_element($_prefix.'header_subtitle_position',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'divider_type'			=> "",
											'placeholder'			=> "",
											'max_chars'				=> "",
											'hide_amount'			=> "",
											"options"				=> $_options,
											'min_price' 			=> $_min_price,
											'max_price' 			=> $_max_price,
											'rules'					=> $_rules,
											'price_rules'			=> $_regular_price,
											'rules_filtered' 		=> $_rules_filtered,
											'price_rules_filtered' 	=> $_regular_price_filtered,
											'price_rules_type'		=> $_regular_price_type,
											'rules_type'			=> $_rules_type,
											'images'				=> "",
											'limit'					=> "",
											'exactlimit' 			=> "",
											'minimumlimit' 			=> "",
											'option_values'			=> array(),
											'button_type' 			=>'',
											'class' 				=> $this->get_builder_element('header_class',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'uniqid' 				=> $this->get_builder_element('header_uniqid',$builder,$current_builder,$current_counter,TM_EPO_HELPER()->tm_uniqid() ,$wpml_element_fields,$current_element), 
											'clogic' 				=> $this->get_builder_element('header_clogic',$builder,$current_builder,$current_counter,false,$wpml_element_fields,$current_element),
											'logic' 				=> $this->get_builder_element('header_logic',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'format' 				=> '',
											'start_year' 			=> '',
											'end_year' 				=> '',
											'tranlation_day' 		=> '',
											'tranlation_month' 		=> '',
											'tranlation_year' 		=> '',
											'swatchmode' 			=> "",
											'changes_product_image' => "",
											'min' 					=> "",
											'max' 					=> "",
											'step' 					=> "",
											'pips' 					=> "",

										);									

									}elseif ($current_element=="divider"){

										$global_epos[$priority][$tmcp_id]['sections'][$_s]['elements'][]=array(
											'internal_name'			=> $this->get_builder_element($_prefix.'internal_name',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'section' 				=> $_sections_uniqid,
											'type'					=> $_new_type,
											'size'					=> $_div_size[$k0],
											'required'				=> "",
											'use_images' 			=> "",
											'use_url' 				=> "",
											'items_per_row' 		=> "",
											'label_size'			=> "",
											'label'					=> "",
											'label_color'			=> "",
											'label_position' 		=> "",
											'description'			=> "",
											'description_color'		=> "",
											'divider_type'			=> $this->get_builder_element($_prefix.'divider_type',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'placeholder'			=> "",
											'max_chars'				=> "",
											'hide_amount'			=> "",
											"options"				=> $_options,
											'min_price' 			=> $_min_price,
											'max_price' 			=> $_max_price,
											'rules'					=> $_rules,
											'price_rules'			=> $_regular_price,
											'rules_filtered' 		=> $_rules_filtered,
											'price_rules_filtered' 	=> $_regular_price_filtered,
											'price_rules_type'		=> $_regular_price_type,
											'rules_type'			=> $_rules_type,
											'images'				=> "",
											'limit'					=> "",
											'exactlimit' 			=> "",
											'minimumlimit' 			=> "",
											'option_values'			=> array(),
											'button_type' 			=> '',
											'class' 				=> $this->get_builder_element('divider_class',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'uniqid' 				=> $this->get_builder_element('divider_uniqid',$builder,$current_builder,$current_counter,TM_EPO_HELPER()->tm_uniqid(),$wpml_element_fields,$current_element),
											'clogic' 				=> $this->get_builder_element('divider_clogic',$builder,$current_builder,$current_counter,false,$wpml_element_fields,$current_element),
											'logic' 				=> $this->get_builder_element('divider_logic',$builder,$current_builder,$current_counter,"",$wpml_element_fields,$current_element),
											'format' 				=> '',
											'start_year' 			=> '',
											'end_year' 				=> '',
											'tranlation_day' 		=> '',
											'tranlation_month' 		=> '',
											'tranlation_year' 		=> '',
											'swatchmode' 			=> "",
											'changes_product_image' => "",
											'min' 					=> "",
											'max' 					=> "",
											'step' 					=> "",
											'pips' 					=> "",
										);									

									}
								}
							}

							$_helper_counter=intval( $_helper_counter+intval( $_sections[$_s] ) );

						}
					}
				}
			}
		}

		ksort( $global_epos );

		if ( $tmlocalprices ) {
			TM_EPO_WPML()->remove_sql_filter();
			$attributes = maybe_unserialize( get_post_meta( floatval(TM_EPO_WPML()->get_original_id( $post_id )), '_product_attributes', true ) );
			$wpml_attributes = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

			foreach ( $tmlocalprices as $price ) {
				$tmcp_id           								= absint( $price->ID );
				$tmcp_required          						= get_post_meta( $tmcp_id, 'tmcp_required', true );
				$tmcp_hide_price          						= get_post_meta( $tmcp_id, 'tmcp_hide_price', true );
				$tmcp_limit          							= get_post_meta( $tmcp_id, 'tmcp_limit', true );
				$product_epos[$tmcp_id]['is_form']  			= 0;
				$product_epos[$tmcp_id]['required']  			= empty( $tmcp_required )?0:1;
				$product_epos[$tmcp_id]['hide_price']  			= empty( $tmcp_hide_price )?0:1;
				$product_epos[$tmcp_id]['limit']  				= empty( $tmcp_limit )?"":$tmcp_limit;
				$product_epos[$tmcp_id]['name']   				= get_post_meta( $tmcp_id, 'tmcp_attribute', true );
				$product_epos[$tmcp_id]['is_taxonomy'] 			= get_post_meta( $tmcp_id, 'tmcp_attribute_is_taxonomy', true );
				$product_epos[$tmcp_id]['label']   				= wc_attribute_label( $product_epos[$tmcp_id]['name'] );
				$product_epos[$tmcp_id]['type']   				= get_post_meta( $tmcp_id, 'tmcp_type', true );
											
				// Retrieve attributes
				$product_epos[$tmcp_id]['attributes']  = array();
				$product_epos[$tmcp_id]['attributes_wpml']  = array();
				if ( $product_epos[$tmcp_id]['is_taxonomy'] ) {
					if ( !( $attributes[$product_epos[$tmcp_id]['name']]['is_variation'] ) ) {
						$all_terms = TM_EPO_WPML()->get_terms( null, $attributes[$product_epos[$tmcp_id]['name']]['name'] , 'orderby=name&hide_empty=0' );
						if ( $all_terms ) {
			                foreach ( $all_terms as $term ) {
			                    $has_term = has_term( (int) $term->term_id, $attributes[$product_epos[$tmcp_id]['name']]['name'], floatval(TM_EPO_WPML()->get_original_id( $post_id )) ) ? 1 : 0;
			                    $wpml_term_id = TM_EPO_WPML()->is_active()?icl_object_id($term->term_id,  $attributes[$product_epos[$tmcp_id]['name']]['name'], false):false;
			                    if ($has_term ){
			                        $product_epos[$tmcp_id]['attributes'][esc_attr( $term->slug )]=apply_filters( 'woocommerce_tm_epo_option_name', esc_html( $term->name ), null, null ) ;
			                        if ($wpml_term_id){
										$wpml_term = get_term( $wpml_term_id, $attributes[$product_epos[$tmcp_id]['name']]['name'] );
										$product_epos[$tmcp_id]['attributes_wpml'][esc_attr( $term->slug )]=apply_filters( 'woocommerce_tm_epo_option_name', esc_html( $wpml_term->name ), null, null ) ;
									}else{;
										$product_epos[$tmcp_id]['attributes_wpml'][esc_attr( $term->slug )]=$product_epos[$tmcp_id]['attributes'][esc_attr( $term->slug )];
									}
			                    }
			                }
			            }
						
					}
				}else {
					if ( isset( $attributes[$product_epos[$tmcp_id]['name']] ) ) {
						$options = array_map( 'trim', explode( WC_DELIMITER, $attributes[$product_epos[$tmcp_id]['name']]['value'] ) );
						$wpml_options = isset($wpml_attributes[$product_epos[$tmcp_id]['name']]['value'])?array_map( 'trim', explode( WC_DELIMITER, $wpml_attributes[$product_epos[$tmcp_id]['name']]['value'] ) ):$options;
						foreach ( $options as $k=>$option ) {
							$product_epos[$tmcp_id]['attributes'][esc_attr( sanitize_title( $option ) )]=esc_html( apply_filters( 'woocommerce_tm_epo_option_name', $option, null, null ) ) ;
							$product_epos[$tmcp_id]['attributes_wpml'][esc_attr( sanitize_title( $option ) )]=esc_html( apply_filters( 'woocommerce_tm_epo_option_name', isset($wpml_options[$k])?$wpml_options[$k]:$option, null, null ) ) ;
						}
					}
				}

				// Retrieve price rules
				$_regular_price=get_post_meta( $tmcp_id, '_regular_price', true );
				$_regular_price_type=get_post_meta( $tmcp_id, '_regular_price_type', true );
				$product_epos[$tmcp_id]['rules']=$_regular_price;
				
				$_regular_price_filtered= TM_EPO_HELPER()->array_map_deep($_regular_price, $_regular_price_type, array($this, 'tm_epo_price_filtered'));
				$product_epos[$tmcp_id]['rules_filtered']=$_regular_price_filtered;

				$product_epos[$tmcp_id]['rules_type']=$_regular_price_type;
				if ( !is_array( $_regular_price ) ) {
					$_regular_price=array();
				}
				if ( !is_array( $_regular_price_type ) ) {
					$_regular_price_type=array();
				}
				foreach ( $_regular_price as $key=>$value ) {
					foreach ( $value as $k=>$v ) {
						$_regular_price[$key][$k]=wc_format_localized_price( $v );						
					}
				}
				foreach ( $_regular_price_type as $key=>$value ) {
					foreach ( $value as $k=>$v ) {
						$_regular_price_type[$key][$k]= $v ;
					}
				}
				$product_epos[$tmcp_id]['price_rules']=$_regular_price;
				$product_epos[$tmcp_id]['price_rules_filtered']=$_regular_price_filtered;
				$product_epos[$tmcp_id]['price_rules_type']=$_regular_price_type;
			}
			TM_EPO_WPML()->restore_sql_filter();
		}
		$global_epos = $this->tm_fill_element_names($post_id,$global_epos, $product_epos, $form_prefix );

		$epos = array(
			'global'=> $global_epos,
			'local' => $product_epos,
			'price' => $epos_prices
		);

		$this->cpf[$post_id] = $epos;

		return $epos;
	}

	/**
	 * Translate $attributes to post names.
	 */
	public function translate_fields( $attributes, $type, $section, $form_prefix="",$name_prefix="" ) {
		$fields=array();
		$loop=0;

		/* $form_prefix should be passed with _ if not empty */
		if ( !empty( $attributes ) ) {

			foreach ( $attributes as $key=>$attribute ) {
				$name_inc="";
				if ( !empty($this->tm_builder_elements[$type]["post_name_prefix"]) ){
					if ($this->tm_builder_elements[$type]["type"]=="multiple" || $this->tm_builder_elements[$type]["type"]=="multiplesingle"){
						$name_inc ="tmcp_".$name_prefix.$this->tm_builder_elements[$type]["post_name_prefix"]."_".$section.$form_prefix;
					}elseif ($this->tm_builder_elements[$type]["type"]=="multipleall"){
						$name_inc ="tmcp_".$name_prefix.$this->tm_builder_elements[$type]["post_name_prefix"]."_".$section."_".$loop.$form_prefix;
					}
				}				
				$fields[]=$name_inc;
				$loop++;
			}

		}else {
			if ( !empty($this->tm_builder_elements[$type]["type"]) && !empty($this->tm_builder_elements[$type]["post_name_prefix"]) ){
				$name_inc ="tmcp_".$name_prefix.$this->tm_builder_elements[$type]["post_name_prefix"]."_".$section.$form_prefix;
			}
			
			if (!empty($name_inc)){
				$fields[]=$name_inc;
			}

		}

		return $fields;
	}

	public function add_cart_item_data_loop($global_prices,$where,$cart_item_meta,$tmcp_post_fields,$product_id,$per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $form_prefix,$post_data){
		
		foreach ( $global_prices[$where] as $priorities ) {
			foreach ( $priorities as $field ) {
				foreach ( $field['sections'] as $section_id=>$section ) {
					if ( isset( $section['elements'] ) ) {
						foreach ( $section['elements'] as $element ) {

							$init_class="TM_EPO_FIELDS_".$element['type'];
							if( !class_exists($init_class) && !empty($this->tm_builder_elements[$element['type']]["_is_addon"]) ){
								$init_class="TM_EPO_FIELDS";
							}
							if(class_exists($init_class)){
								$field_obj= new $init_class( $product_id, $element, $per_product_pricing, $cpf_product_price, $variation_id, $post_data );

								/* Cart fees */
								$current_tmcp_post_fields=array_intersect_key( $tmcp_post_fields , array_flip( $this->translate_fields( $element['options'], $element['type'], $field_loop, $form_prefix ,$this->cart_fee_name) ) );
								foreach ( $current_tmcp_post_fields as $attribute=>$key ) {
									if (!empty($field_obj->holder_cart_fees)){
										$meta = $field_obj->add_cart_item_data_cart_fees($attribute, $key);
										if (is_array($meta)){
											if (isset($meta[0]) && is_array($meta[0])){
												foreach ($meta as $k => $value) {
													$cart_item_meta['tmcartfee'][] = $value;
												}
											}else{
												$cart_item_meta['tmcartfee'][] = $meta;
											}
										}
									}
								}

								/* Subscription fees */
								$current_tmcp_post_fields=array_intersect_key( $tmcp_post_fields , array_flip( $this->translate_fields( $element['options'], $element['type'], $field_loop, $form_prefix ,$this->fee_name) ) );								
								foreach ( $current_tmcp_post_fields as $attribute=>$key ) {;
									if (!empty($field_obj->holder_subscription_fees)){
										$meta = $field_obj->add_cart_item_data_subscription_fees($attribute, $key);
										if (is_array($meta)){
											if (isset($meta[0]) && is_array($meta[0])){
												foreach ($meta as $k => $value) {
													$cart_item_meta['tmcartepo'][] = $value;
												}
											}else{
												$cart_item_meta['tmcartepo'][] = $meta;
											}
										}
									}
									$cart_item_meta['tmsubscriptionfee'] = $this->tmfee;							
								}
								
								/* Normal fields */
								$current_tmcp_post_fields=array_intersect_key( $tmcp_post_fields , array_flip( $this->translate_fields( $element['options'], $element['type'], $field_loop, $form_prefix ,"") ) );
								foreach ( $current_tmcp_post_fields as $attribute=>$key ) {
									if (!empty($field_obj->holder)){
										$meta = $field_obj->add_cart_item_data($attribute, $key);
										if (is_array($meta)){
											if (isset($meta[0]) && is_array($meta[0])){
												foreach ($meta as $k => $value) {
													$cart_item_meta['tmcartepo'][] = $value;
												}
											}else{
												$cart_item_meta['tmcartepo'][] = $meta;
											}
										}										
									}								
								}

								unset($field_obj); // clear memory
							}

							if (in_array($element['type'], $this->element_post_types)   ){
								$field_loop++;
							}
							$loop++;							

						}
					}
				}
			}
		}
		return array('loop'=>$loop,'field_loop'=>$field_loop,'cart_item_meta'=>$cart_item_meta);

	}

	/* LOCAL FIELDS (to be deprecated) */
	public function add_cart_item_data_loop_local($local_price_array,$cart_item_meta,$tmcp_post_fields,$product_id,$per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $form_prefix,$post_data){
		if ( ! empty( $local_price_array ) && is_array( $local_price_array ) && count( $local_price_array ) > 0 ) {

			if ( is_array( $tmcp_post_fields ) ) {

				foreach ( $local_price_array as $tmcp ) {
					if ( empty( $tmcp['type'] ) ) {
						continue;
					}

					$current_tmcp_post_fields=array_intersect_key(  $tmcp_post_fields , array_flip( $this->translate_fields( $tmcp['attributes'], $tmcp['type'], $field_loop, $form_prefix ) ) );

					foreach ( $current_tmcp_post_fields as $attribute=>$key ) {
						
						switch ( $tmcp['type'] ) {

						case "checkbox" :
						case "radio" :
						case "select" :
							$_price=$this->calculate_price( $_POST, $tmcp, $key, $attribute, $per_product_pricing, $cpf_product_price, $variation_id );
							
							$cart_item_meta['tmcartepo'][] = array(
								'mode' 	=> 'local',
								'key' => $key,
								'is_taxonomy' => $tmcp['is_taxonomy'],
								'name'   => esc_html( $tmcp['name'] ),
								'value'  =>  esc_html( wc_attribute_label($tmcp['attributes_wpml'][$key])  ),
								'price'  => esc_attr( $_price ),
								'section'  => esc_html( $tmcp['name'] ),
								'section_label'  => esc_html( urldecode($tmcp['label']) ),
								'percentcurrenttotal' => isset($post_data[$attribute.'_hidden'])?1:0,
								'quantity' => 1
							);
							break;

						}
					}
					if (in_array($tmcp['type'], $this->element_post_types)   ){
						$field_loop++;
					}
					$loop++;

				}
			}
		}

		return array('loop'=>$loop,'field_loop'=>$field_loop,'cart_item_meta'=>$cart_item_meta);
	
	}

	public function get_posted_variation_id($form_prefix=""){
		$variation_id=null;
		if (isset($_POST['variation_id'.$form_prefix])){
			$variation_id=$_POST['variation_id'.$form_prefix];
		}
		return $variation_id;
	}

	/**
	 * Adds data to the cart.
	 */
	public function tm_add_cart_item_data( $cart_item_meta, $product_id, $post_data = null ) {
		return $this->add_cart_item_data_helper( $cart_item_meta, $product_id, $post_data);
	}
	public function add_cart_item_data( $cart_item_meta, $product_id) {
		return $this->add_cart_item_data_helper( $cart_item_meta, $product_id,$_POST);
	}
	public function add_cart_item_data_helper( $cart_item_meta, $product_id, $post_data = null ) {
		if ( is_null( $post_data ) && isset( $_POST ) ) {
			$post_data = $_POST;
		}
		/* Workaround to get unique items in cart for bto */
		$terms 			= get_the_terms( $product_id, 'product_type' );
		$product_type 	= ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';
		if ( ($product_type == 'bto' || $product_type == 'composite') && isset( $_REQUEST[ 'add-product-to-cart' ] ) && is_array( $_REQUEST[ 'add-product-to-cart' ] ) ) {
			$copy=array();
			foreach ( $_REQUEST[ 'add-product-to-cart' ] as $bundled_item_id => $bundled_product_id ) {
				$copy=array_merge($copy,TM_EPO_HELPER()->array_filter_key( $post_data ,$bundled_item_id,"end"));				
			}
			$copy=TM_EPO_HELPER()->array_filter_key( $copy);
			$cart_item_meta['tmcartepo_bto']=$copy;
		}

		$form_prefix="";
		$variation_id=false;
		$cpf_product_price=false;
		$per_product_pricing=true;

		if (isset($cart_item_meta['composite_item'])){
			global $woocommerce;
			$cart_contents = $woocommerce->cart->get_cart();

			if ( isset( $cart_item_meta[ 'composite_parent' ] ) && ! empty( $cart_item_meta[ 'composite_parent' ] ) ) {
				$parent_cart_key = $cart_item_meta[ 'composite_parent' ];
				$per_product_pricing 	= $cart_contents[ $parent_cart_key ][ 'data' ]->per_product_pricing;
				if ( $per_product_pricing == 'no' ) {
					$per_product_pricing=false;
				}
			}
			
			$form_prefix="_".$cart_item_meta['composite_item'];
			$bundled_item_id= $cart_item_meta['composite_item'];
			if (isset($_REQUEST[ 'bto_variation_id' ][ $bundled_item_id ])){
				$variation_id=$_REQUEST[ 'bto_variation_id' ][ $bundled_item_id ];
			}
			if (isset($post_data['cpf_bto_price'][$bundled_item_id])){
				$cpf_product_price=$post_data['cpf_bto_price'][$bundled_item_id];
			}
		}else{
			if (isset($post_data['variation_id'])){
				$variation_id=$post_data['variation_id'];
			}
			if (isset($post_data['cpf_product_price'])){
				$cpf_product_price=$post_data['cpf_product_price'];
			}		
		}

		$cpf_price_array  = $this->get_product_tm_epos( $product_id , $form_prefix);
		if ( empty($cpf_price_array) ){
			return $cart_item_meta;
		}
		$global_price_array = $cpf_price_array['global'];
		$local_price_array  = $cpf_price_array['local'];

		if ( empty($global_price_array) && empty($local_price_array) ){
			return $cart_item_meta;
		}

		// if the following key doens't extist the edit cart link is not being displayed.
		if(in_array($product_type, array("simple","variable","subscription","variable-subscription"))){
			$cart_item_meta['tmhasepo']=1;
		}

		$tm_meta_cpf = get_post_meta( $product_id, 'tm_meta_cpf', true );		
		if (!is_array($tm_meta_cpf)){
			$tm_meta_cpf=array();
		}
		foreach ( $this->meta_fields as $key=>$value ) {
			$tm_meta_cpf[$key] = isset( $tm_meta_cpf[$key] ) ? $tm_meta_cpf[$key] : $value;
		}
		if (!empty($tm_meta_cpf['price_override'])){
			$cart_item_meta['epo_price_override']=1;
		}

		$global_prices=array( 'before'=>array(), 'after'=>array() );
		foreach ( $global_price_array as $priority=>$priorities ) {
			foreach ( $priorities as $pid=>$field ) {
				if(isset($field['sections'])){
					foreach ( $field['sections'] as $section_id=>$section ) {
						if ( isset( $section['sections_placement'] ) ) {
							$global_prices[$section['sections_placement']][$priority][$pid]['sections'][$section_id]=$section;
						}
					}
				}
			}
		}

		$files=array();
		foreach ( $_FILES as $k=>$file){
			if (!empty($file['name'])){
				$files[$k]=$file['name'];
			}
		}

		$tmcp_post_fields = array_merge(TM_EPO_HELPER()->array_filter_key( $post_data ),TM_EPO_HELPER()->array_filter_key( $files ));
		if ( is_array( $tmcp_post_fields ) ) {
			$tmcp_post_fields = array_map( 'stripslashes_deep', $tmcp_post_fields );
		}

		if ( empty( $cart_item_meta['tmcartepo'] ) ) {
			$cart_item_meta['tmcartepo'] = array();
		}
		if ( empty( $cart_item_meta['tmsubscriptionfee'] ) ) {
			$cart_item_meta['tmsubscriptionfee'] = 0;
		}
		if ( empty( $cart_item_meta['tmcartfee'] ) ) {
			$cart_item_meta['tmcartfee'] = array();
		}

		$loop=0;
		$field_loop=0;

		$_return=$this->add_cart_item_data_loop($global_prices,'before',$cart_item_meta,$tmcp_post_fields,$product_id,$per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $form_prefix,$post_data);
		extract( $_return, EXTR_OVERWRITE );

		/* LOCAL FIELDS (to be deprecated) */
		$_return=$this->add_cart_item_data_loop_local($local_price_array,$cart_item_meta,$tmcp_post_fields,$product_id,$per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $form_prefix,$post_data);
		extract( $_return, EXTR_OVERWRITE );

		$_return=$this->add_cart_item_data_loop($global_prices,'after',$cart_item_meta,$tmcp_post_fields,$product_id,$per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $form_prefix,$post_data);
		extract( $_return, EXTR_OVERWRITE );

		return $cart_item_meta;
	}

	public function validate_product_id_loop($global_sections,$global_prices,$where,$tmcp_post_fields, $passed, $loop, $form_prefix){

		foreach ( $global_prices[$where] as $priorities ) {
			foreach ( $priorities as $field ) {
				foreach ( $field['sections'] as $section_id=>$section ) {
					if ( isset( $section['elements'] ) ) {
						foreach ( $section['elements'] as $element ) {
							
							if (in_array($element['type'], $this->element_post_types)   ){
								$loop++;
							}
							
							if ( isset($this->tm_builder_elements[$element['type']]) 
								&& isset($this->tm_builder_elements[$element['type']]) && $this->tm_builder_elements[$element['type']]["is_post"]!="display" 
								&& $this->is_visible($element, $section, $global_sections, $form_prefix)
								) {
								
								$_passed = true;
								$_message = false;

								$init_class="TM_EPO_FIELDS_".$element['type'];
								if( !class_exists($init_class) && !empty($this->tm_builder_elements[$element['type']]["_is_addon"]) ){
									$init_class="TM_EPO_FIELDS";
								}								
								if (class_exists($init_class)){
									$field_obj= new $init_class();
									$_passed = $field_obj->validate_field($tmcp_post_fields,$element,$loop, $form_prefix);
									$_message = isset($_passed["message"])?$_passed["message"]:false;
									$_passed = isset($_passed["passed"])?$_passed["passed"]:false;
									unset($field_obj); // clear memory
								}

								if ( ! $_passed ) {

									$passed = false;
									if($_message!==false && is_array($_message)){
										foreach ($_message as $key => $value) {
											if($value=='required'){
												wc_add_notice( sprintf( __( '"%s" is a required field.', 'woocommerce-tm-extra-product-options' ), $element['label'] ) , 'error' );
											}else{
												wc_add_notice( $value , 'error' );		
											}											
										}
									}
									
								}
							}

						}
					}
				}
			}
		}

		return array('loop'=>$loop,'passed'=>$passed);
	}

	public function validate_product_id( $product_id, $qty, $form_prefix="" ) {		

		$passed=true;

		if ($form_prefix){
			$form_prefix="_".$form_prefix;
		}
		$cpf_price_array=$this->get_product_tm_epos($product_id);
		if ( empty($cpf_price_array) ){
			return $passed;
		}
		$global_price_array = $cpf_price_array['global'];
		$local_price_array  = $cpf_price_array['local'];
		if ( empty($global_price_array) && empty($local_price_array) ){
			return $passed;
		}
		$global_prices=array( 'before'=>array(), 'after'=>array() );
		$global_sections=array();
		foreach ( $global_price_array as $priority=>$priorities ) {
			foreach ( $priorities as $pid=>$field ) {
				if (isset($field['sections'])){
					foreach ( $field['sections'] as $section_id=>$section ) {
						if ( isset( $section['sections_placement'] ) ) {
							$global_prices[$section['sections_placement']][$priority][$pid]['sections'][$section_id]=$section;
							$global_sections[$section['sections_uniqid']]=$section;
						}
					}
				}
			}
		}

		if ( ( ! empty( $global_price_array ) && is_array( $global_price_array ) && count( $global_price_array ) > 0 ) || ( ! empty( $local_price_array ) && is_array( $local_price_array ) && count( $local_price_array ) > 0 ) ) {
			$tmcp_post_fields = TM_EPO_HELPER()->array_filter_key( $_POST);			
			if ( is_array( $tmcp_post_fields ) && !empty( $tmcp_post_fields ) && count( $tmcp_post_fields )>0  ) {
				$tmcp_post_fields = array_map( 'stripslashes_deep', $tmcp_post_fields );
			}
		

			$loop=-1;

			$_return=$this->validate_product_id_loop($global_sections,$global_prices,'before',$tmcp_post_fields, $passed, $loop, $form_prefix);
			extract( $_return, EXTR_OVERWRITE );
			// todo: move this code to a function
			if ( ! empty( $local_price_array ) && is_array( $local_price_array ) && count( $local_price_array ) > 0 ) {

				foreach ( $local_price_array as $tmcp ) {

					if (in_array($tmcp['type'], $this->element_post_types)   ){
						$loop++;
					}
					if ( empty( $tmcp['type'] ) || empty( $tmcp['required'] ) ) {
						continue;
					}

					if ( $tmcp['required'] ) {

						$tmcp_attributes=$this->translate_fields( $tmcp['attributes'], $tmcp['type'], $loop, $form_prefix );
						$_passed=true;

						switch ( $tmcp['type'] ) {

						case "checkbox" :
							$_check=array_intersect( $tmcp_attributes, array_keys( $tmcp_post_fields ) );
							if ( empty( $_check ) || count( $_check )==0 ) {
								$_passed = false;
							}
							break;

						case "radio" :
							foreach ( $tmcp_attributes as $attribute ) {
								if ( !isset( $tmcp_post_fields[$attribute] ) ) {
									$_passed = false;
								}
							}
							break;

						case "select" :
							foreach ( $tmcp_attributes as $attribute ) {
								if ( !isset( $tmcp_post_fields[$attribute] ) ||  $tmcp_post_fields[$attribute]=="" ) {
									$_passed = false;
								}
							}
							break;

						}

						if ( ! $_passed ) {
							$passed=false;
							wc_add_notice( sprintf( __( '"%s" is a required field.', 'woocommerce-tm-extra-product-options' ), $tmcp['label'] ) , 'error' );
							
						}
					}
				}

			}

			$_return=$this->validate_product_id_loop($global_sections,$global_prices,'after',$tmcp_post_fields, $passed, $loop, $form_prefix);
			extract( $_return, EXTR_OVERWRITE );

		}

		return $passed;
	}

	public function tm_woocommerce_product_single_add_to_cart_text(){
		return __('Update Cart','woocommerce');
	}

	public function get_tm_validation_rules($element){
		$rules=array();
		if ($element['required']){
			$rules['required'] = true;
		}
		if (isset($element['min_chars']) && $element['min_chars']!=''){
			$rules['minlength'] = absint($element['min_chars']);
		}
		if (isset($element['max_chars']) && $element['max_chars']!==''){
			$rules['maxlength'] = absint($element['max_chars']);
		}
		if (isset($element['min']) && $element['min']!==''){
			$rules['min'] = intval($element['min']);
		}
		if (isset($element['max']) && $element['max']!==''){
			$rules['max'] = intval($element['max']);
		}
		if (!empty($element['validation1'])){
			$rules[$element['validation1']] = true;
		}
		return $rules;
	}
	/**
	 * Handles the display of builder sections.
	 */
	public function get_builder_display( $field, $where, $args, $form_prefix="", $product_id=0, $dummy_prefix=false ) {
		
		/* $form_prefix	shoud be passed with _ if not empty */			
		
		$columns=array(
			"w25"=>array( "col-3", 25 ),
			"w33"=>array( "col-4", 33 ),
			"w50"=>array( "col-6", 50 ),
			"w66"=>array( "col-8", 66 ),
			"w75"=>array( "col-9", 75 ),
			"w100"=>array( "col-12", 100 )
		);

		extract( $args, EXTR_OVERWRITE );

		if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {

			$args = array(
				'field_id'  => 'tm-epo-field-'.$unit_counter
			);
			wc_get_template(
				'tm-builder-start.php',
				$args ,
				$this->_namespace,
				TM_EPO_TEMPLATE_PATH
			);

			$_section_totals=0;

			foreach ( $field['sections'] as $section ) {
				if ( !isset( $section['sections_placement'] ) || $section['sections_placement']!=$where ) {
					continue;
				}
				if ( isset( $section['sections_size'] ) && isset( $columns[$section['sections_size']] ) ) {
					$size=$columns[$section['sections_size']][0];
				}else {
					$size="col-12";
				}

				$_section_totals=$_section_totals+$columns[$section['sections_size']][1];
				if ( $_section_totals>100 ) {
					$_section_totals=$columns[$section['sections_size']][1];
					echo '<div class="cpfclear"></div>';
				}

				$divider="";
				if ( isset( $section['divider_type'] ) ) {
					switch ( $section['divider_type'] ) {
					case "hr":
						$divider='<hr>';
						break;
					case "divider":
						$divider='<div class="tm_divider"></div>';
						break;
					case "padding":
						$divider='<div class="tm_padding"></div>';
						break;
					}
				}
				$label_size='h3';
				if ( !empty( $section['label_size'] )){
					switch($section['label_size']){
						case "1":
							$label_size='h1';
						break;
						case "2":
							$label_size='h2';
						break;
						case "3":
							$label_size='h3';
						break;
						case "4":
							$label_size='h4';
						break;
						case "5":
							$label_size='h5';
						break;
						case "6":
							$label_size='h6';
						break;
						case "7":
							$label_size='p';
						break;
						case "8":
							$label_size='div';
						break;
						case "9":
							$label_size='span';
						break;
					}
				}

				$args = array(
					'column' 			=> $size,
					'style' 			=> $section['sections_style'],
					'uniqid' 			=> $section['sections_uniqid'],
					'logic' 			=> esc_html(json_encode( (array) json_decode( $section['sections_clogic']) ) ),
					'haslogic' 			=> $section['sections_logic'],
					'sections_class' 	=> $section['sections_class'],
					'sections_type' 	=> $section['sections_type'],
					'title_size'   		=> $label_size,
					'title'    			=> !empty( $section['label'] )? $section['label'] :"",
					'title_color'   	=> !empty( $section['label_color'] )? $section['label_color'] :"",
					'title_position'   	=> !empty( $section['label_position'] )? $section['label_position'] :"",
					'description'   	=> !empty( $section['description'] )?  $section['description']  :"",
					'description_color' => !empty( $section['description_color'] )? $section['description_color'] :"",
					'description_position' => !empty( $section['description_position'] )? $section['description_position'] :"",
					'divider'    		=> $divider							
				);
				// custom variations check
				if ( 
					isset( $section['elements'] ) 
					&& is_array( $section['elements'] ) 
					&& isset($section['elements'][0])
					&& is_array( $section['elements'][0] )
					&& isset($section['elements'][0]['type'])
					&& $section['elements'][0]['type']=='variations'
					) {
					$args['sections_class'] = $args['sections_class']." tm-epo-variation-section";
				}
				wc_get_template(
					'tm-builder-section-start.php',
					$args ,
					$this->_namespace,
					TM_EPO_TEMPLATE_PATH
				);

				if ( isset( $section['elements'] ) && is_array( $section['elements'] ) ) {
					$totals=0;

					$slide_counter=0;
					$use_slides=false;
					$doing_slides=false;
					if($section['sections_slides']!=="" && $section['sections_type']=="slider"){
						$sections_slides= explode( ",", $section['sections_slides'] );
						$use_slides=true;
					}

					foreach ( $section['elements'] as $element ) {

						$empty_rules="";
						if ( isset( $element['rules_filtered'] ) ) {
							$empty_rules=esc_html( json_encode( ( $element['rules_filtered'] ) ) );
						}
						$empty_original_rules="";
						if ( isset( $element['original_rules_filtered'] ) ) {
							$empty_original_rules=esc_html( json_encode( ( $element['original_rules_filtered'] ) ) );
						}
						$empty_rules_type="";
						if ( isset( $element['rules_type'] ) ) {
							$empty_rules_type=esc_html( json_encode( ( $element['rules_type'] ) ) );
						}
						if ( isset( $element['size'] ) && isset( $columns[$element['size']] ) ) {
							$size=$columns[$element['size']][0];
						}else {
							$size="col-12";
						}
						$test_for_first_slide=false;
						if ($use_slides && isset($sections_slides[$slide_counter])){
							$sections_slides[$slide_counter]=intval($sections_slides[$slide_counter]);
							
							if ($sections_slides[$slide_counter]>0 && !$doing_slides){
								echo '<div class="tm-slide">';
								$doing_slides=true;
								$test_for_first_slide=true;
							}
						}

						$fee_name=$this->fee_name;
						$cart_fee_name=$this->cart_fee_name;
						$totals=$totals+$columns[$element['size']][1];
						if ( $totals>100 && !$test_for_first_slide ) {
							$totals=$columns[$element['size']][1];
							echo '<div class="cpfclear"></div>';
						}

						$divider="";
						if ( isset( $element['divider_type'] ) ) {
							$divider_class="";
							if ( $element['type']=='divider' && !empty( $element['class'] ) ) {
								$divider_class=" ".$element['class'];
							}
							switch ( $element['divider_type'] ) {
							case "hr":
								$divider='<hr'.$divider_class.'>';
								break;
							case "divider":
								$divider='<div class="tm_divider'.$divider_class.'"></div>';
								break;
							case "padding":
								$divider='<div class="tm_padding'.$divider_class.'"></div>';
								break;
							}
						}
						$label_size='h3';
						if ( !empty( $element['label_size'] )){
							switch($element['label_size']){
								case "1":
									$label_size='h1';
								break;
								case "2":
									$label_size='h2';
								break;
								case "3":
									$label_size='h3';
								break;
								case "4":
									$label_size='h4';
								break;
								case "5":
									$label_size='h5';
								break;
								case "6":
									$label_size='h6';
								break;
								case "7":
									$label_size='p';
								break;
								case "8":
									$label_size='div';
								break;
								case "9":
									$label_size='span';
								break;
								case "10":
									$label_size='label';
								break;
							}
						}

						$variations_builder_element_start_args = array();
						$tm_validation = $this->get_tm_validation_rules($element);
						$args = array(
							'tm_element_settings' 	=> $element,
							'column'    			=> $size,
							'class'   				=> !empty( $element['class'] )? $element['class'] :"",
							'container_id' 			=> !empty( $element['container_id'] )? $element['container_id'] :"",
							'title_size'   			=> $label_size,
							'title'    				=> !empty( $element['label'] )? $element['label'] :"",
							'title_position'   		=> !empty( $element['label_position'] )? $element['label_position'] :"",
							'title_color'   		=> !empty( $element['label_color'] )? $element['label_color'] :"",
							'description'   		=> !empty( $element['description'] )?  $element['description']  :"",
							'description_color' 	=> !empty( $element['description_color'] )? $element['description_color'] :"",
							'description_position' 	=> !empty( $element['description_position'] )? $element['description_position'] :"",
							'divider'    			=> $divider,
							'required'    			=> $element['required'],
							'type'        			=> $element['type'],
							'use_images'        	=> $element['use_images'],
							'use_url'        		=> $element['use_url'],
							'rules'       			=> $empty_rules,
							'original_rules' 		=> $empty_original_rules,
							'rules_type' 			=> $empty_rules_type,
							'element'				=> $element['type'],
							'class_id'				=> "tm-element-ul-".$element['type']." element_".$element_counter.$form_prefix,// this goes on ul
							'uniqid' 				=> $element['uniqid'],
							'logic' 				=> esc_html(json_encode( (array) json_decode($element['clogic']) ) ),
							'haslogic' 				=> $element['logic'],
							'clear_options' 		=> empty( $element['clear_options'] )?"":$element['clear_options'],
							'exactlimit' 			=> empty( $element['exactlimit'] )?"":'tm-exactlimit',
							'minimumlimit' 			=> empty( $element['minimumlimit'] )?"":'tm-minimumlimit',
							'tm_validation' 		=> esc_html( json_encode( ( $tm_validation ) ) )
						);
						if ($element['type']!="variations"){
							wc_get_template(
								'tm-builder-element-start.php',
								$args ,
								$this->_namespace,
								TM_EPO_TEMPLATE_PATH
							);
						}else{
							$variations_builder_element_start_args = $args;
						}
						$field_counter=0;

						$init_class="TM_EPO_FIELDS_".$element['type'];
						if( !class_exists($init_class) && !empty($this->tm_builder_elements[$element['type']]["_is_addon"]) ){
							$init_class="TM_EPO_FIELDS";
						}				
						
						if (isset($this->tm_builder_elements[$element['type']]) 
							&& ($this->tm_builder_elements[$element['type']]["is_post"]=="post" || $this->tm_builder_elements[$element['type']]["is_post"]=="display")
							&& class_exists($init_class)
							){
							
							$field_obj= new $init_class();

							if ( $this->tm_builder_elements[$element['type']]["is_post"]=="post" ){
								
								if($this->tm_builder_elements[$element['type']]["type"]=="single" || $this->tm_builder_elements[$element['type']]["type"]=="multipleallsingle" ||  $this->tm_builder_elements[$element['type']]["type"]=="multiplesingle"){
									
									$tabindex++;
									$name_inc =$this->tm_builder_elements[$element['type']]["post_name_prefix"]."_".$element_counter.($dummy_prefix?"":$form_prefix);
									if($this->tm_builder_elements[$element['type']]["type"]=="single" || $this->tm_builder_elements[$element['type']]["type"]=="multipleallsingle"){
										$is_fee=(!empty( $element['rules_type'] ) && $element['rules_type'][0][0]=="subscriptionfee");
										$is_cart_fee=(!empty( $element['rules_type'] ) && isset($element['rules_type'][0]) && isset($element['rules_type'][0][0]) && in_array( $element['rules_type'][0][0], array("fee","stepfee","currentstepfee") ));
									}elseif($this->tm_builder_elements[$element['type']]["type"]=="multiplesingle"){
										$is_fee=(!empty( $element['selectbox_fee'] ) && $element['selectbox_fee'][0][0]=="subscriptionfee");
										$is_cart_fee=(!empty( $element['selectbox_cart_fee'] ) && isset($element['selectbox_cart_fee'][0]) && isset($element['selectbox_cart_fee'][0][0]) && in_array($element['selectbox_cart_fee'][0][0], array("fee","stepfee","currentstepfee")));
									}
									if ($is_fee){
										$name_inc = $fee_name.$name_inc;
									}elseif ($is_cart_fee){
										$name_inc = $cart_fee_name.$name_inc;
									}


									if ( isset ( $_GET['switch-subscription'] ) && ( function_exists('wcs_get_subscription') || (class_exists('WC_Subscriptions_Manager') && class_exists('WC_Subscriptions_Order')) ) ) {
										$item=false;
										if (function_exists('wcs_get_subscription')){
											$subscription = wcs_get_subscription( $_GET['switch-subscription'] );
											if ( $subscription instanceof WC_Subscription ) {
												$original_order = new WC_Order( $subscription->order->id );
												$item           = WC_Subscriptions_Order::get_item_by_product_id( $original_order, $subscription->id );
											}
										}else{
											$subscription = WC_Subscriptions_Manager::get_subscription( $_GET['switch-subscription'] );
											$original_order = new WC_Order( $subscription['order_id'] );
											$item           = WC_Subscriptions_Order::get_item_by_product_id( $original_order, $subscription['product_id'] );
										}
										
										if($item){
											$saved_data=maybe_unserialize($item["item_meta"]["_tmcartepo_data"][0]);
											foreach ($saved_data as $key => $val) {
												if (isset($val["key"])){
													if ($element['uniqid']==$val["section"] ){
														$_GET['tmcp_'.$name_inc]=$val["key"];
														if (isset($val['quantity'])){
															$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
														}
													}
												}else{
													if ($element['uniqid']==$val["section"]){
														$_GET['tmcp_'.$name_inc]=$val["value"];
														if (isset($val['quantity'])){
															$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
														}
													}
												}
											}
										}

									}elseif ( !empty( $this->cart_edit_key) && isset($_GET['_wpnonce']) && wp_verify_nonce( $_GET['_wpnonce'], 'tm-edit' ) ){
										$_cart=WC()->cart;
										if(isset($_cart->cart_contents) && isset($_cart->cart_contents[$this->cart_edit_key]) ){
											if(!empty($_cart->cart_contents[$this->cart_edit_key]['tmcartepo'])){
												$saved_epos=$_cart->cart_contents[$this->cart_edit_key]['tmcartepo'];												
												foreach ($saved_epos as $key => $val) {
													if (isset($val["key"])){
														if ($element['uniqid']==$val["section"] ){
															$_GET['tmcp_'.$name_inc]=$val["key"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}else{
														if ($element['uniqid']==$val["section"]){
															$_GET['tmcp_'.$name_inc]=$val["value"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}														
												}												
											}
											if(!empty($_cart->cart_contents[$this->cart_edit_key]['tmcartfee'])){
												$saved_fees=$_cart->cart_contents[$this->cart_edit_key]['tmcartfee'];
												foreach ($saved_fees as $key => $val) {
													if (isset($val["key"])){
														if ($element['uniqid']==$val["section"] ){
															$_GET['tmcp_'.$name_inc]=$val["key"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}else{
														if ($element['uniqid']==$val["section"]){
															$_GET['tmcp_'.$name_inc]=$val["value"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}
												}
											}
											if(!empty($_cart->cart_contents[$this->cart_edit_key]['tmsubscriptionfee'])){
												$saved_subscriptionfees=$_cart->cart_contents[$this->cart_edit_key]['tmsubscriptionfee'];
												foreach ($saved_subscriptionfees as $key => $val) {
													if (isset($val["key"])){
														if ($element['uniqid']==$val["section"] ){
															$_GET['tmcp_'.$name_inc]=$val["key"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}else{
														if ($element['uniqid']==$val["section"]){
															$_GET['tmcp_'.$name_inc]=$val["value"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}
												}
											}
										}
									}

									$display = $field_obj->display_field($element, array(
											'name' => 'tmcp_'.$name_inc,
											'name_inc'=>$name_inc, 
											'element_counter'=>$element_counter, 
											'tabindex'=>$tabindex, 
											'form_prefix'=>$form_prefix, 
											'field_counter'=>$field_counter) );

									if (is_array($display)){
										
										$original_amount = "";
										if (isset($element['original_rules_filtered'][0]) && isset($element['original_rules_filtered'][0][0])){
											$original_amount = $element['original_rules_filtered'][0][0];
										}else{
											$original_amount = $element['original_rules_filtered'][esc_attr(reset($element['options']))."_0"];
											if (isset($original_amount[0])){
												$original_amount=$original_amount[0];
											}else{
												$original_amount="";
											}
										}
										if (isset($display["default_value_counter"]) && $display["default_value_counter"]!==false ){
											$original_amount = $element['original_rules_filtered'][$display['default_value_counter']][0];
										}

										$amount = "";
										if (isset($element['rules_filtered'][0]) && isset($element['rules_filtered'][0][0])){
											$amount = $element['rules_filtered'][0][0];
										}else{
											$amount = $element['rules_filtered'][esc_attr(reset($element['options']))."_0"];
											if (isset($amount[0])){
												$amount=$amount[0];
											}else{
												$amount="";
											}
										}
										if (isset($display["default_value_counter"]) && $display["default_value_counter"]!==false ){
											$amount = $element['rules_filtered'][$display['default_value_counter']][0];
										}
										$args = array(
											'tm_element_settings' 	=> $element,
											'id'    				=> 'tmcp_'.$this->tm_builder_elements[$element['type']]["post_name_prefix"].'_'.$tabindex.$form_prefix,
											'name'    				=> 'tmcp_'.$name_inc,
											'class'   				=> !empty( $element['class'] )? $element['class'] :"",								
											'tabindex'  			=> $tabindex,
											'rules'   				=> isset( $element['rules_filtered'] )?esc_html( json_encode( ( $element['rules_filtered'] ) ) ):'',
											'original_rules' 		=> isset( $element['original_rules_filtered'] )?esc_html( json_encode( ( $element['original_rules_filtered'] ) ) ):'',
											'rules_type'   			=> isset( $element['rules_type'] )?esc_html( json_encode( ( $element['rules_type'] ) ) ):'',
											'amount'     			=> $amount.' '.$_currency,
											'original_amount' 		=> $original_amount.' '.$_currency,
											'fieldtype' 			=> $is_fee?$this->fee_name_class:($is_cart_fee?$this->cart_fee_class:"tmcp-field"),
											'field_counter' 		=> $field_counter
										);

										$args=array_merge($args,$display);
										
										if( $this->tm_builder_elements[$element['type']]["_is_addon"] ){
											do_action( "tm_epo_display_addons" , $element, $args, array(
											'name_inc'=>$name_inc, 
											'element_counter'=>$element_counter, 
											'tabindex'=>$tabindex, 
											'form_prefix'=>$form_prefix, 
											'field_counter'=>$field_counter), $this->tm_builder_elements[$element['type']]["namespace"] );
										}

										elseif(is_readable(TM_EPO_TEMPLATE_PATH.'tm-'.$element['type'].'.php')){
											wc_get_template(
												'tm-'.$element['type'].'.php',
												$args ,
												$this->_namespace,
												TM_EPO_TEMPLATE_PATH
											);
										}										
									}

								}elseif($this->tm_builder_elements[$element['type']]["type"]=="multipleall" || $this->tm_builder_elements[$element['type']]["type"]=="multiple"){
									
									$field_obj->display_field_pre($element, array(
											'element_counter'=>$element_counter, 
											'tabindex'=>$tabindex, 
											'form_prefix'=>$form_prefix, 
											'field_counter'=>$field_counter,
											'product_id'=>isset($product_id)?$product_id:0
											) );

									foreach ( $element['options'] as $value=>$label ) {

										$tabindex++;
										if ($this->tm_builder_elements[$element['type']]["type"]=="multipleall"){
											$name_inc = $this->tm_builder_elements[$element['type']]["post_name_prefix"]."_".$element_counter."_".$field_counter.($dummy_prefix?"":$form_prefix);
										}else{
											$name_inc = $this->tm_builder_elements[$element['type']]["post_name_prefix"]."_".$element_counter.($dummy_prefix?"":$form_prefix);
										}										
										
										$is_fee=(isset( $element['rules_type'][$value] ) && $element['rules_type'][$value][0]=="subscriptionfee");
										$is_cart_fee=(isset( $element['rules_type'][$value]) && in_array($element['rules_type'][$value][0], array("fee","stepfee","currentstepfee")));
										if ($is_fee){
											$name_inc = $fee_name.$name_inc;
										}elseif($is_cart_fee){
											$name_inc = $cart_fee_name.$name_inc;
										}
										if ( isset ( $_GET['switch-subscription'] ) && ( function_exists('wcs_get_subscription') || (class_exists('WC_Subscriptions_Manager') && class_exists('WC_Subscriptions_Order')) ) ) {
											$item=false;
											if (function_exists('wcs_get_subscription')){
												$subscription = wcs_get_subscription( $_GET['switch-subscription'] );
												if ( $subscription instanceof WC_Subscription ) {
													$original_order = new WC_Order( $subscription->order->id );
													$item           = WC_Subscriptions_Order::get_item_by_product_id( $original_order, $subscription->id );
												}
											}else{
												$subscription = WC_Subscriptions_Manager::get_subscription( $_GET['switch-subscription'] );
												$original_order = new WC_Order( $subscription['order_id'] );
												$item           = WC_Subscriptions_Order::get_item_by_product_id( $original_order, $subscription['product_id'] );
											}

												
											if($item){
												$saved_data=maybe_unserialize($item["item_meta"]["_tmcartepo_data"][0]);
												foreach ($saved_data as $key => $val) {
													if ($element['uniqid']==$val["section"] && $value==$val["key"]){
														$_GET['tmcp_'.$name_inc]=$val["key"];
														if (isset($val['quantity'])){
															$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
														}
													}
												}													
											}
										}elseif ( !empty( $this->cart_edit_key) && isset($_GET['_wpnonce']) && wp_verify_nonce( $_GET['_wpnonce'], 'tm-edit' ) ){
											$_cart=WC()->cart;
											if(isset($_cart->cart_contents) && isset($_cart->cart_contents[$this->cart_edit_key]) ){
												if(!empty($_cart->cart_contents[$this->cart_edit_key]['tmcartepo'])){
													$saved_epos=$_cart->cart_contents[$this->cart_edit_key]['tmcartepo'];
													foreach ($saved_epos as $key => $val) {
														if ($element['uniqid']==$val["section"] && $value==$val["key"]){
															$_GET['tmcp_'.$name_inc]=$val["key"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}
												}
												if(!empty($_cart->cart_contents[$this->cart_edit_key]['tmcartfee'])){
													$saved_fees=$_cart->cart_contents[$this->cart_edit_key]['tmcartfee'];
													foreach ($saved_fees as $key => $val) {
														if ($element['uniqid']==$val["section"] && $value==$val["key"]){
															$_GET['tmcp_'.$name_inc]=$val["key"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}
												}
												if(!empty($_cart->cart_contents[$this->cart_edit_key]['tmsubscriptionfee'])){
													$saved_subscriptionfees=$_cart->cart_contents[$this->cart_edit_key]['tmsubscriptionfee'];
													foreach ($saved_subscriptionfees as $key => $val) {
														if ($element['uniqid']==$val["section"] && $value==$val["key"]){
															$_GET['tmcp_'.$name_inc]=$val["key"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}
												}
											}
										}

										$display = $field_obj->display_field($element, array(
											'name' => 'tmcp_'.$name_inc,
											'name_inc'=>$name_inc, 
											'value'=>$value, 
											'label'=>$label, 
											'element_counter'=>$element_counter, 
											'tabindex'=>$tabindex, 
											'form_prefix'=>$form_prefix, 
											'field_counter'=>$field_counter) );

										if (is_array($display)){
											
											$original_amount = "";											
											//if (isset($display["default_value"]) && $display["default_value"]!==false ){
												$original_amount = $element['original_rules_filtered'][$value][0];
											//}

											$amount = "";											
											//if (isset($display["default_value"]) && $display["default_value"]!==false ){
												$amount = $element['rules_filtered'][$value][0];
											//}

											$args = array(
												'tm_element_settings' 	=> $element,
												'id'    				=> 'tmcp_'.$this->tm_builder_elements[$element['type']]["post_name_prefix"].'_'.$element_counter."_".$field_counter."_".$tabindex.$form_prefix,
												'name'    				=> 'tmcp_'.$name_inc,
												'class'   				=> !empty( $element['class'] )? $element['class'] :"",								
												'tabindex'  			=> $tabindex,
												'rules'   				=> isset( $element['rules_filtered'][$value] )?esc_html( json_encode( ( $element['rules_filtered'][$value] ) ) ):'',
												'original_rules' 		=> isset( $element['original_rules_filtered'][$value])?esc_html( json_encode( ( $element['original_rules_filtered'][$value] ) ) ):'',
												'rules_type' 			=> isset( $element['rules_type'][$value] )?esc_html( json_encode( ( $element['rules_type'][$value] ) ) ):'',
												'amount'     			=> $amount.' '.$_currency,
												'original_amount' 		=> $original_amount.' '.$_currency,
												'fieldtype' 			=> $is_fee?$this->fee_name_class:($is_cart_fee?$this->cart_fee_class:"tmcp-field"),
												'border_type' 			=> $this->tm_epo_css_selected_border,
												'field_counter' 		=> $field_counter
											);

											$args=array_merge($args,$display);
											
											if( $this->tm_builder_elements[$element['type']]["_is_addon"] ){
												do_action( "tm_epo_display_addons" , $element, $args, array(
												'name_inc'=>$name_inc, 
												'element_counter'=>$element_counter, 
												'tabindex'=>$tabindex, 
												'form_prefix'=>$form_prefix, 
												'field_counter'=>$field_counter,
												'border_type'=> $this->tm_epo_css_selected_border), $this->tm_builder_elements[$element['type']]["namespace"] );
											}

											elseif(is_readable(TM_EPO_TEMPLATE_PATH.'tm-'.$element['type'].'.php')){
												wc_get_template(
													'tm-'.$element['type'].'.php',
													$args ,
													$this->_namespace,
													TM_EPO_TEMPLATE_PATH
												);
											}
										}

										$field_counter++;

									}

								}

								$element_counter++;
								
							}elseif ( $this->tm_builder_elements[$element['type']]["is_post"]=="display" ){
								
								$display = $field_obj->display_field($element, array(
											'element_counter'=>$element_counter, 
											'tabindex'=>$tabindex, 
											'form_prefix'=>$form_prefix, 
											'field_counter'=>$field_counter) );

								if (is_array($display)){
									$args = array(
										'tm_element_settings' 	=> $element,
										'class'   				=> !empty( $element['class'] )? $element['class'] :"",
										'form_prefix' 			=> $form_prefix,
										'field_counter' 		=> $field_counter,
										'tm_element' 			=> $element,
										'tm__namespace' 		=> $this->_namespace,
										'tm_template_path' 		=> TM_EPO_TEMPLATE_PATH,
										'tm_product_id' 		=> $product_id
									);

									if ($element['type']=="variations"){
										$args["variations_builder_element_start_args"] = $variations_builder_element_start_args;
										$args["variations_builder_element_end_args"] = array(
											'tm_element_settings' 	=> $element,
											'element' 				=> $element['type'],
											'description'   		=> !empty( $element['description'] )?  $element['description']  :"",
											'description_color' 	=> !empty( $element['description_color'] )? $element['description_color'] :"",
											'description_position' 	=> !empty( $element['description_position'] )? $element['description_position'] :"",
										);
									}

									$args=array_merge($args,$display);

									if( $this->tm_builder_elements[$element['type']]["_is_addon"] ){
										do_action( "tm_epo_display_addons" , $element, $args, array(
										'name_inc'=>'', 
										'element_counter'=>$element_counter, 
										'tabindex'=>$tabindex, 
										'form_prefix'=>$form_prefix, 
										'field_counter'=>$field_counter), $this->tm_builder_elements[$element['type']]["namespace"] );
									}

									elseif(is_readable(TM_EPO_TEMPLATE_PATH.'tm-'.$element['type'].'.php')){
										wc_get_template(
											'tm-'.$element['type'].'.php',
											$args ,
											$this->_namespace,
											TM_EPO_TEMPLATE_PATH
										);
									}
								}
							}

							unset($field_obj); // clear memory	
						}

						if ($element['type']!="variations"){
							wc_get_template(
								'tm-builder-element-end.php',
								array(
									'tm_element_settings' 	=> $element,
									'element' 				=> $element['type'],
									'description'   		=> !empty( $element['description'] )?  $element['description']  :"",
									'description_color' 	=> !empty( $element['description_color'] )? $element['description_color'] :"",
									'description_position' 	=> !empty( $element['description_position'] )? $element['description_position'] :"",
								) ,
								$this->_namespace,
								TM_EPO_TEMPLATE_PATH
							);
						}
						
						if ($use_slides && isset($sections_slides[$slide_counter])){
							$sections_slides[$slide_counter]=$sections_slides[$slide_counter]-1;

							if ($sections_slides[$slide_counter]<=0){
								echo '</div>';
								$slide_counter++;
								$doing_slides=false;
							}
						}

					}
				}
				$args = array(					
					'column' 		=> $size,
					'style' 		=> $section['sections_style'],
					'sections_type' => $section['sections_type'],
					'title_size'   		=> $label_size,
					'title'    			=> !empty( $section['label'] )? $section['label'] :"",
					'title_color'   	=> !empty( $section['label_color'] )? $section['label_color'] :"",
					'description'   	=> !empty( $section['description'] )?  $section['description']  :"",
					'description_color' => !empty( $section['description_color'] )? $section['description_color'] :"",
					'description_position' => !empty( $section['description_position'] )? $section['description_position'] :""
				);
				wc_get_template(
					'tm-builder-section-end.php',
					$args ,
					$this->_namespace,
					TM_EPO_TEMPLATE_PATH
				);

			}

			wc_get_template(
				'tm-builder-end.php',
				array() ,
				$this->_namespace,
				TM_EPO_TEMPLATE_PATH
			);

			$unit_counter++;

		}
		return array(
			'tabindex'   		=> $tabindex,
			'unit_counter'  	=> $unit_counter,
			'field_counter'  	=> $field_counter,
			'element_counter'  	=> $element_counter,
			'_currency'   		=> $_currency
		);

	}

	public function fill_builder_display( $global_epos, $field, $where, $args, $form_prefix="" ) {
		/* $form_prefix	shoud be passed with _ if not empty */			
		extract( $args, EXTR_OVERWRITE );
		if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {
			foreach ( $field['sections'] as $_s => $section ) {
				if ( !isset( $section['sections_placement'] ) || $section['sections_placement']!=$where ) {
					continue;
				}
				if ( isset( $section['elements'] ) && is_array( $section['elements'] ) ) {
					foreach ( $section['elements'] as $arr_element_counter=>$element ) {
						$fee_name=$this->fee_name;
						$cart_fee_name=$this->cart_fee_name;
						$field_counter=0;
						
						if ( isset($this->tm_builder_elements[$element['type']]) && $this->tm_builder_elements[$element['type']]["is_post"]=="post" ){

							if ($this->tm_builder_elements[$element['type']]["type"]=="multipleall" || $this->tm_builder_elements[$element['type']]["type"]=="multiple"){

								foreach ( $element['options'] as $value=>$label ) {

									if ($this->tm_builder_elements[$element['type']]["type"]=="multipleall"){
										$name_inc = $this->tm_builder_elements[$element['type']]["post_name_prefix"]."_".$element_counter."_".$field_counter.$form_prefix;
									}else{
										$name_inc = $this->tm_builder_elements[$element['type']]["post_name_prefix"]."_".$element_counter.$form_prefix;
									}

									$is_fee=(!empty( $element['rules_type'][$value] ) && $element['rules_type'][$value][0]=="subscriptionfee");
									$is_cart_fee=(!empty( $element['rules_type'][$value] ) && in_array($element['rules_type'][$value][0], array("fee","stepfee","currentstepfee")));
									if ($is_fee){
										$name_inc = $fee_name.$name_inc;
									}elseif($is_cart_fee){
										$name_inc = $cart_fee_name.$name_inc;
									}
									$name_inc = 'tmcp_'.$name_inc.$form_prefix;
									$global_epos[$priority][$pid]['sections'][$_s]['elements'][$arr_element_counter]['name_inc'][]=$name_inc;
									$global_epos[$priority][$pid]['sections'][$_s]['elements'][$arr_element_counter]['is_fee'][]=$is_fee;
									$global_epos[$priority][$pid]['sections'][$_s]['elements'][$arr_element_counter]['is_cart_fee'][]=$is_cart_fee;

									$field_counter++;

								}

							}elseif ($this->tm_builder_elements[$element['type']]["type"]=="single" || $this->tm_builder_elements[$element['type']]["type"]=="multipleallsingle" || $this->tm_builder_elements[$element['type']]["type"]=="multiplesingle"){

								$name_inc = $this->tm_builder_elements[$element['type']]["post_name_prefix"]."_".$element_counter.$form_prefix;
								if($this->tm_builder_elements[$element['type']]["type"]=="single" || $this->tm_builder_elements[$element['type']]["type"]=="multipleallsingle"){
									$is_fee=(!empty( $element['rules_type'] ) && $element['rules_type'][0][0]=="subscriptionfee");
									$is_cart_fee=(!empty( $element['rules_type'] ) && isset($element['rules_type'][0]) && isset($element['rules_type'][0][0]) && in_array($element['rules_type'][0][0], array("fee","stepfee","currentstepfee")));
								}elseif($this->tm_builder_elements[$element['type']]["type"]=="multiplesingle"){
									$is_fee=(!empty( $element['selectbox_fee'] ) && $element['selectbox_fee'][0][0]=="subscriptionfee");
									$is_cart_fee=(!empty( $element['selectbox_cart_fee'] ) && in_array($element['selectbox_cart_fee'][0][0], array("fee","stepfee","currentstepfee")));
								}
								if ($is_fee){
									$name_inc = $fee_name.$name_inc;
								}elseif ($is_cart_fee){
									$name_inc = $cart_fee_name.$name_inc;
								}
								$name_inc = 'tmcp_'.$name_inc.$form_prefix;
								$global_epos[$priority][$pid]['sections'][$_s]['elements'][$arr_element_counter]['name_inc']=$name_inc;
								$global_epos[$priority][$pid]['sections'][$_s]['elements'][$arr_element_counter]['is_fee']=$is_fee;
								$global_epos[$priority][$pid]['sections'][$_s]['elements'][$arr_element_counter]['is_cart_fee']=$is_cart_fee;
								
							}
							$element_counter++;
						}
											
					}
				}				
			}
			$unit_counter++;
		}
		return array(
			'global_epos' 		=> $global_epos,
			'unit_counter'  	=> $unit_counter,
			'field_counter'  	=> $field_counter,
			'element_counter'  	=> $element_counter
		);

	}

}

define('TM_EPO_INCLUDED',1);

?>