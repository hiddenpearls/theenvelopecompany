<?php
/* * ******************************************************************************************** */
/*  child functions
/* * ******************************************************************************************** */

// Child CSS  
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

}

// Parent CSS  
function themeslug_enqueue_style() {
	wp_enqueue_style( 'core', 'style.css', false ); 
}

function themeslug_enqueue_script() {
	wp_enqueue_script( 'my-js', 'filename.js', false );
}

add_action( 'login_enqueue_scripts', 'themeslug_enqueue_style', 10 );
add_action( 'login_enqueue_scripts', 'themeslug_enqueue_script', 1 );


// Apply a different tax rate based on the user role.
function wc_diff_rate_for_user( $tax_class, $product ) {
	if ( is_user_logged_in() && current_user_can( 'non_taxed' ) ) {
		$tax_class = 'Zero Rate';
	}
	return $tax_class;
}
add_filter( 'woocommerce_product_tax_class', 'wc_diff_rate_for_user', 1, 2 );

// adds notice at single product page above add to cart
add_action( 'woocommerce_single_product_summary', 'return_policy', 20 );
function return_policy() {
    echo '<p id="rtrn">30-day return policy offered. See Terms and Conditions for details.</p>';
}

/**
 * Only display minimum price for WooCommerce variable products
 **
add_filter('woocommerce_variable_price_html', 'custom_variation_price', 10, 2);

function custom_variation_price( $price, $product ) {

     $price = '';
     $price .= woocommerce_price($product->get_price());
     return $price;
}

/**
 * Only display minimum price for WooCommerce variable products
 *
add_filter('get_price', 'custom_single_price', 10, 2);

function custom_single_price( $price, $product ) {

     $price = '';
     $price .= woocommerce_price($product->get_price());
	 $price = $price * 2;
     return $price;
}
add_filter( 'get_price_html', 'my_price_html', 100, 2 );

function my_price_html( $price, $product ){
    return 'Was:' . str_replace( '<ins>', ' Now:<ins>', $price );
}
	
/**
* WooCommerce: Show only one custom product attribute above Add-to-cart button on single product page.
*/
/* @@@ Turned off - start
function isa_woo_get_one_pa(){
 
    // Edit below with the title of the attribute you wish to display
    $desired_att = 'Size';
  
    global $product;
    $attributes = $product->get_attributes();
     
    if ( ! $attributes ) {
        return;
    }
     
    $out = '';
  
    foreach ( $attributes as $attribute ) {
         
        if ( $attribute['is_taxonomy'] ) {
         
            // sanitize the desired attribute into a taxonomy slug
            $tax_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $desired_att)));
         
            // if this is desired att, get value and label
             
            if ( $attribute['name'] == 'pa_' . $tax_slug ) {
             
                $terms = wp_get_post_terms( $product->id, $attribute['name'], 'all' );
                 
                // get the taxonomy
                $tax = $terms[0]->taxonomy;
                 
                // get the tax object
                $tax_object = get_taxonomy($tax);
                 
                // get tax label
                if ( isset ($tax_object->labels->name) ) {
                    $tax_label = $tax_object->labels->name;
                } elseif ( isset( $tax_object->label ) ) {
                    $tax_label = $tax_object->label;
                }
                 
                foreach ( $terms as $term ) {
      
                    $out .= $tax_label . ': ';
                    $out .= $term->name . '<br />';
                      
                }           
             
            } // our desired att
             
        } else {
         
            // for atts which are NOT registered as taxonomies
             
            // if this is desired att, get value and label
            if ( $attribute['name'] == $desired_att ) {
                $out .= $attribute['name'] . ': ';
                $out .= $attribute['value'];
            }
        }       
         
     
    }
     
    echo $out;
}
add_action('woocommerce_single_product_summary', 'isa_woo_get_one_pa');
// @@@ Turned off - end */

/**
 * Lists a table of attributes for the product page.
 */

function list_attributes_2() {
	wc_get_template( 'single-product/product-attributes_2.php', array(
		'product'    => $this
	) );
}

add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 4;' ), 20 );

add_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 4 );
add_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 4 );	

/* @@@ Turned off - end */ 


// GET 4 products
function woocommerce_upsell_display( $posts_per_page = 1, $columns = 4, $orderby = 'rand' ) {
woocommerce_get_template( 'single-product/up-sells.php', array(
'posts_per_page' => $posts_per_page,
'orderby' => $orderby,
'columns' => $columns
) );
}

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/* @@@ Turned off - start 
 
function is_user_discount(){
if ( is_user_logged_in() ) {
	$current_user = wp_get_current_user();
}
	return $my__name;
}

/* @@@ Turned off - end */


/**
 * Only display minimum price for WooCommerce variable products
 **/
/* @@@ Turned off - start 
 
add_filter('woocommerce_variable_price_html', 'custom_variation_price', 10, 2);

function custom_variation_price( $price, $product ) {

     $price = '';
     $price .= woocommerce_price($product->get_price());
     return $price;
}

/* @@@ Turned off - start */


/**
 * Only display minimum price for WooCommerce variable products
 */
 /* @@@ Turned off - start 

add_filter('get_price', 'custom_single_price', 10, 2);


function custom_single_price( $price, $product ) {

     $price = '';
     $price .= woocommerce_price($product->get_price());
	 $price = $price * 1.6;
     return $price;
}
/* @@@ Turned off - start */

/* @@@ Turned off - start 
add_filter( 'get_price_html', 'my_price_html', 100, 2 );

function my_price_html( $price, $product ){
    return 'Was:' . str_replace( '<ins>', ' Now:<ins>', $price );
}
/* @@@ Turned off - start */


/* @@@ Turned off - end */ 

if ( ! function_exists( 'woocommerce_pagination2' ) ) {

     /**
      * Output the pagination.
      *
      * @subpackage  Loop
      */
     function woocommerce_pagination2() {
         wc_get_template( 'loop/pagination2.php' );
	 }
}
 
 
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
add_action( 'woocommerce_after_shop_loop_item', 'my_woocommerce_template_loop_add_to_cart', 10 );


function my_woocommerce_template_loop_add_to_cart() {
    global $product;
    echo '';
}

function new_woocommerce_cart_item_name($title="", $cart_item=array(), $cart_item_key="" ){


// Chained prodcuts cannot be edited

	if ( class_exists('Chained_Products_WC_Compatibility') && isset ( Chained_Products_WC_Compatibility::global_wc()->cart->cart_contents[ $cart_item_key ]['chained_item_of'] ) ){
			return $title;	
		}
		$product=$cart_item['data'];
		$link=$product->get_permalink( $cart_item );
		$link = add_query_arg( 
			array(
				'tm_cart_item_key' => $cart_item_key,
				)

			, $link );

		//wp_nonce_url escapes the url

		 $link=wp_nonce_url($link,'tm-edit');

		return '<a href="'.$link.'" class="tm-cart-edit-options">'.((!empty($this->tm_epo_edit_options_text))?$this->tm_epo_edit_options_text:__( 'Edit options', TM_EPO_TRANSLATION )).'</a>';

	}
	

/**
* Preview WooCommerce Emails.
* @author WordImpress.com
* @url https://github.com/WordImpress/woocommerce-preview-emails
* If you are using a child-theme, then use get_stylesheet_directory() instead
*/

$preview = get_stylesheet_directory() . '/woocommerce/emails/woo-preview-emails.php';

if(file_exists($preview)) {
    require $preview;
}

// add a product type
add_filter( 'product_type_selector', 'wdm_add_custom_product_type' );
function wdm_add_custom_product_type( $types ){
    $types[ 'wdm_custom_product' ] = __( 'Wdm Product' );
    return $types;
}

add_action( 'plugins_loaded', 'wdm_create_custom_product_type' );
function wdm_create_custom_product_type(){
     // declare the product class
     class WC_Product_Wdm extends WC_Product{
        public function __construct( $product ) {
           $this->product_type = 'wdm_custom_product';
           parent::__construct( $product );
           // add additional functions here
        }
    }
}

// add the settings under ‘General’ sub-menu
add_action( 'woocommerce_product_options_general_product_data', 'wdm_add_custom_settings' );
function wdm_add_custom_settings() {
    global $woocommerce, $post;
    echo '<div class="options_group">';

    // Create a number field, for example for UPC
    woocommerce_wp_checkbox(
      array(
       'id'                => 'wdm_upc_field',
       'label'             => __( 'Upgrade Custom Print', 'woocommerce' ),
	   'placeholder'       => 'yes', 
       ));

    // Create a checkbox for product purchase status
      woocommerce_wp_checkbox(
       array(
       'id'            => 'wdm_is_purchasable',
       'label'         => __('Upgrade Latex', 'woocommerce' ),
	   'placeholder'       => 'yes', 	   
       ));

    // Create a checkbox for product purchase status
      woocommerce_wp_checkbox(
       array(
       'id'            => 'wdm_is_purchasable1',
       'label'         => __('Upgrade Zip', 'woocommerce' )
       ));
    // Create a checkbox for product purchase status
      woocommerce_wp_checkbox(
       array(
       'id'            => 'wdm_is_purchasable2',
       'label'         => __('Upgrade Zip #10S', 'woocommerce' )
       ));


    echo '</div>';
}
add_action( 'woocommerce_process_product_meta', 'wdm_save_custom_settings' );
function wdm_save_custom_settings( $post_id ){
// save UPC field
$wdm_product_upc = isset( $_POST['wdm_upc_field'] ) ? 'yes' : 'no';
update_post_meta( $post_id, 'wdm_upc_field', $wdm_product_upc );

// save purchasable option
$wdm_purchasable = isset( $_POST['wdm_is_purchasable'] ) ? 'yes' : 'no';
update_post_meta( $post_id, 'wdm_is_purchasable', $wdm_purchasable );


// save purchasable option
$wdm_purchasable1 = isset( $_POST['wdm_is_purchasable1'] ) ? 'yes' : 'no';
update_post_meta( $post_id, 'wdm_is_purchasable1', $wdm_purchasable1 );


// save purchasable option
$wdm_purchasable2 = isset( $_POST['wdm_is_purchasable2'] ) ? 'yes' : 'no';
update_post_meta( $post_id, 'wdm_is_purchasable2', $wdm_purchasable2 );
}

/* @@@ Turned off - start 

function enqueue_and_register_my_scripts(){

// AZE custom Qty .js.
//    wp_register_script( 'my-select-script', get_stylesheet_directory_uri() . '/js/selectvalnow.js' );

if (check_user_role(array('discount1','discount2','discount3'))) {
    	wp_register_script( 'my-select-script', get_stylesheet_directory_uri() . '/js/selectvalnow.js' );
	}
}
/* @@@ Turned off - end */


/* Addes new line item */
add_filter('woocommerce_add_cart_item_data','namespace_force_individual_cart_items',10,2);

function namespace_force_individual_cart_items($cart_item_data, $product_id)
{
	$unique_cart_item_key = md5(microtime().rand()."Hi Mom!");
	$cart_item_data['unique_key'] = $unique_cart_item_key;

	return $cart_item_data;
}
// First we hook our own function with the_content event
// add_filter( 'tm_epo_cart_options_prices', 'tm_epo_cart_options_prices_discounted' );
/*
add_filter( 'wc_tm_epo_ac_product_price', 'tm_epo_cart_options_prices_discounted' );

// Now we define what our function would do.
// In this example it displays an image if a post is in news category.
function tm_epo_cart_options_prices_discounted( $content ) {
 
        $content = '<span="product-subtotal">VALUE HERE:</span>'; //.$value['tm_total_price'].'</span>';

    // Returns the content.
    return $content;
}
*/

// Price Dropdown
function yahm_cart_sel(){
	$x=500; $yahm_arr = array();
		for($i=0; $i<60; $i++) {
			$j = number_format($x);	echo '<option '. ($i != 1 ? '' : 'selected=selected') . ' value="' . $j.'_'.$i.'">' . $j.'</option>';
			$x=$x+500;
	}
}

// check role

function check_user_role($roles, $user_id = NULL) {
	if ($user_id) $user = get_userdata($user_id);
	else $user = wp_get_current_user();
	if (empty($user)) return false;
	foreach ($user->roles as $role) {
		if (in_array($role, $roles)) {
			return true;
		}
	}
	return false;
}



//** added 2x price yahm **
function custom_price_WPA111772($price,$product,$attr) {
	if (check_user_role(array('discount-customer-10'))) {
			$j = 1.8; }
		elseif (check_user_role(array('discount-customer-20'))) {
			$j = 1.6; }
		else { $j = $attr; }
    if ($product && is_object($product) && method_exists($product, "get_price") ){
        $_price=$product->get_price();
		$price=$_price*$j;
		$price = number_format($price, 2, '.', '');
		$price = '$'.$price;
	}
	return $price;
}