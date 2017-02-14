<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Setup;

/**
 * Add <body> classes
 */
function body_class($classes) {
  // Add page slug if it doesn't exist
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }

  // Add class if sidebar is active
  if (Setup\display_sidebar()) {
    $classes[] = 'sidebar-primary';
  }

  return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\\body_class');

/**
 * Clean up the_excerpt()
 */
function excerpt_more() {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
}
add_filter('excerpt_more', __NAMESPACE__ . '\\excerpt_more');

/**
 * Add options page(s)
 */
if( function_exists('acf_add_options_page') ) {
  
  acf_add_options_page(array(
    'page_title'  => 'Theme General Settings',
    'menu_title'  => 'Theme Settings',
    'menu_slug'   => 'theme-general-settings',
    'capability'  => 'edit_posts',
    'redirect'    => false
  ));
  /*acf_add_options_sub_page(array(
    'page_title'  => 'Sale Popup',
    'menu_title'  => 'Sale Popup',
    'parent_slug' => 'theme-general-settings',
    'capability'  => 'edit_posts',
    'redirect'    => false
  ));*/
  acf_add_options_sub_page(array(
    'page_title'  => 'Analytics Settings',
    'menu_title'  => 'Analytics Settings',
    'parent_slug' => 'theme-general-settings',
    'capability'  => 'edit_posts',
    'redirect'    => false
  ));
  acf_add_options_sub_page(array(
    'page_title'  => '404 Page',
    'menu_title'  => '404 Page',
    'parent_slug' => 'theme-general-settings',
    'capability'  => 'edit_posts',
    'redirect'    => false
  ));
  
}
/**
 * Remove wordpress' auto <p> tags on the excerpt
 */
remove_filter( 'the_excerpt', 'wpautop' );

/**
 * WooCommerce Theme integration --- Move all wooCommerce code into oktara content type plugin for sanity(?)
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', 'Roots\Sage\Extras\my_theme_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'Roots\Sage\Extras\my_theme_wrapper_end', 10);

function my_theme_wrapper_start() {
  echo '<div class="main-content">';
}
function my_theme_wrapper_end() {
  echo '</div>';
}

/**
 * Remove WooCommerce Breadcrumbs
 */
add_action( 'init', 'Roots\Sage\Extras\shop_remove_wc_breadcrumbs' );
function shop_remove_wc_breadcrumbs() {
  remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
}

/**
 * Reinsert breadcrumbs
 */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
add_action( 'woocommerce_before_shop_loop', 'Roots\Sage\Extras\custom_breadcrumb', 30);
function custom_breadcrumb() {
  echo woocommerce_breadcrumb();
}

/**
 * Change product's name position within the DOM.
 */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'woocommerce_single_product_title', 'woocommerce_template_single_title', 5 );

/**
 * Change single product pages layouts
 */
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
//add excerpt to after add to cart form
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
add_action('woocommerce_after_add_to_cart_form', 'woocommerce_template_single_excerpt', 20);

//get list of attributes from product info and the product description
function product_long_desc() {
  global $woocommerce, $post;
 ?>
  <div itemprop="description">
    <?php 
    global $product;
    //echo $product->list_attributes();
    $sku = $product->get_sku();
    if( $sku ) {
      echo '<p>SKU: '.$sku.'<br>';  
    }
    $style = $product->get_attribute( 'style' );
    if( $style ) {
      echo 'Style: '.$style.'<br>';
    }
    $size = $product->get_attribute( 'size' ) ;
    if( $size ) {
      echo 'Size: '.$size.'<br>';
    }
    $flap_size = $product->get_attribute( 'flap size' );
    if ( $flap_size ) {
      echo 'Flap size: '.$flap_size.'<br>';
    }
    $paper_color = $product->get_attribute( 'color' ) ;
    if( $paper_color ) {
      echo 'Paper Color: '.$paper_color.'<br>';
    }
    $paper_weight = $product->get_attribute( 'paper weight' ) ;
    if( $paper_weight ) {
      echo 'Paper Weight: '.$paper_weight.'<br>';
    }
    $sealing_method = $product->get_attribute( 'sealing method' ) ;
    if( $sealing_method ) {
      echo 'Sealing Method: '.$sealing_method.'<br>';
    }
    $security_tint = $product->get_attribute( 'security tint' );
    if( $security_tint ) {
      echo 'Security Tint: '.$security_tint.'<br>';
    }
    $window_size = $product->get_attribute( 'window size' );
    if( $window_size ) {
      echo 'Window size: '.$window_size.'<br>';
    }
    $window_position = $product->get_attribute( 'window position' );
    if( $window_position ) {
      echo "Window position: ".$window_position.'<br>';
    }
    if ( $post->post_content ) {
      echo "Description: ".get_the_content()."</p>";
    } ?>
  </div>
<?php
}
add_action( 'woocommerce_single_product_summary', 'Roots\Sage\Extras\product_long_desc', 20 );

/**
 * Show attributes within product loop and print size, color and paper weight along with product name and image and view more link
 * INSIDE the loop
 */
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'Roots\Sage\Extras\woocommerce_template_loop_attributes', 10 );
function woocommerce_template_loop_attributes(){
  global $product;
  //echo $product->list_attributes();
  $size = $product->get_attribute( 'size' ) ;
  echo '<p>'.$size.'</p>';
  $color = $product->get_attribute( 'color' ) ;
  /*if ( $product->has_weight() ) {
    $weight = $product->get_weight() . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) );
  }*/
  $paper_weight = $product->get_attribute( 'Paper Weight' ) ;
  echo '<p>'.$paper_weight.' - '.$color.'</p>';
}
/**
 * Remove add to cart button from product summary
 */
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

/**
 * Change Add To Cart to Select Options
 */
function sfws_woocommerce_product_add_to_cart_text() {
  global $product;
  return __( 'View Product', 'woocommerce' );
}
//* Change the Add To Cart Link
add_filter( 'woocommerce_loop_add_to_cart_link', 'Roots\Sage\Extras\sfws_add_product_link' );
function sfws_add_product_link( $link ) {
 global $product;
 $product_id = $product->id;
 $product_sku = $product->get_sku();
 $link = '<a href="'.get_permalink().'" rel="nofollow" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" data-quantity="1" class="button add_to_cart_button product_type_variable">'.sfws_woocommerce_product_add_to_cart_text().'</a>';
 return $link;
}

/*add_filter( 'wp_nav_menu_cart', 'Roots\Sage\Extras\cart_items_menu_link', 10, 2 );
function cart_items_menu_link( $items, $args ){
  if (($args->theme_location == 'top_navigation')) {
    
  }
  return $items;
}
*/
/**
 * Add cart button with subtotals and login/logout link to the top navigation
 */
function get_cart_count(){

  return sizeof(WC()->cart->cart_contents);

}
//add login/logout links 
add_filter( 'wp_nav_menu_items', 'Roots\Sage\Extras\wti_loginout_menu_link', 10, 2 );
function wti_loginout_menu_link( $items, $args ) {
   if (($args->theme_location == 'top_navigation')) {
      if (is_user_logged_in()) {
         $items .= '<li class="pull-left menu-item"><a href="'. wp_logout_url("/") .'">'. __("Logout") .'</a></li>';
         $items .= '<li class="pull-left menu-item"><a href="/my-account/">'. __("My Account") .'</a></li>';
      } else {
        //wp_login_url(get_permalink()) >> wordpress login URL
         $items .= '<li class="pull-left menu-item"><a href="/my-account/">'. __("Login") .'</a></li>';
         $items .= '<li class="pull-left menu-item"><a href="/my-account/">'. __("Create Account") .'</a></li>';
      }
      //$items .= '<li class="menu-item"><a href="#">'. __("Hello World") .'</a></li>';
      global $woocommerce;
      $cart_url = $woocommerce->cart->get_cart_url();
      $items .= '<li class="menu-item cart-icon"><a class="cart-btn cart-contents" title="View your shopping cart" href="'. $cart_url.'">My Cart';
      $items .= ' ('.get_cart_count().') '.sprintf (WC()->cart->get_cart_total()).'</a></li>';
   }
   return $items;
}



/**
 * Redirect to my account page on login 
 */
add_filter('woocommerce_login_redirect', 'Roots\Sage\Extras\wc_login_redirect'); 
function wc_login_redirect( $redirect_to ) {
     $redirect_to = '/my-account/';
     return $redirect_to;
}

/**
 * remove product data tabs
 */
remove_action('woocommerce_after_single_product_summary','woocommerce_output_product_data_tabs', 10);

/**
 * Change wooCommerce my acccount nav endpoints
 */
function wpb_woo_my_account_order() {
  $myorder = array(
    'edit-account'       => __( 'Account Details', 'woocommerce' ),
    'edit-address'       => __( 'Addresses', 'woocommerce' ),
    'orders'             => __( 'Orders', 'woocommerce' ),
    //'payment-methods'    => __( 'Payment Methods', 'woocommerce' ),
  );
  return $myorder;
}
add_filter ( 'woocommerce_account_menu_items', 'Roots\Sage\Extras\wpb_woo_my_account_order' );



//old functions from old theme
// Price Dropdown
function yahm_cart_sel($product, $attr){
    if (check_user_role(array('discount-customer-10'))) {
        $j = 1.8; 
    }
    elseif (check_user_role(array('discount-customer-20'))) {
        $j = 1.6; }
    else { 
        $j = $attr; 
    }
    if ($product && is_object($product) && method_exists($product, "get_price") ){
        $_price=$product->get_price();
        $price=$_price*$j;
        $price = number_format($price, 2, '.', '');

        //var k = '#yahm-tm-price-tmcp_radio_'+j; 
        //alert(k);
        //var kk = $(k).text(); // 


        //$price = '$'.$price;
        //print_r($price);
    }
    $x=500; $yahm_arr = array();
    for($i=0; $i<60; $i++) {
        $j = number_format($x); 
        //this total price matches the price of the product with the correct per lot quantity, use if client requests again. Taken out by request.
        if( $i == 0){
            $totalPrice = $price*0.5;
        } else {
            $totalPrice = $price*(($i/2)+0.5);
        }
        
        echo '<option '. ($i != 1 ? '' : 'selected=selected') . ' value="' . $j.'_'.$i.'">' . $j.'</option>';
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
    $j = 1.8; 
  }
  elseif (check_user_role(array('discount-customer-20'))) {
    $j = 1.6; }
    else { $j = $attr; 
  }
  if ($product && is_object($product) && method_exists($product, "get_price") ){
    $_price=$product->get_price();
    $price=$_price*$j;
    $price = number_format($price, 2, '.', '');
    $price = '$'.$price;
  }
  return $price;
}

/* Addes new line item */
add_filter('woocommerce_add_cart_item_data','Roots\Sage\Extras\namespace_force_individual_cart_items',10,2);

function namespace_force_individual_cart_items($cart_item_data, $product_id)
{
  $unique_cart_item_key = md5(microtime().rand()."Hi Mom!");
  $cart_item_data['unique_key'] = $unique_cart_item_key;

  return $cart_item_data;
}

/**
 * Lists a table of attributes for the product page.
 */
function list_attributes_2() {
  wc_get_template( 'single-product/product-attributes_2.php', array(
    'product'    => $this
  ) );
}

// Apply a different tax rate based on the user role.
function wc_diff_rate_for_user( $tax_class, $product ) {
  if ( is_user_logged_in() && current_user_can( 'non_taxed' ) ) {
    $tax_class = 'Zero Rate';
  }
  return $tax_class;
}
add_filter( 'woocommerce_product_tax_class', 'Roots\Sage\Extras\wc_diff_rate_for_user', 1, 2 );

// Calculate order total without shipping
/*function order_total_no_shipping( $order_id ) {
  $order = new WC_Order( $order_id );
  $order_total = $order->get_total();
  $order_total_without_shipping = $order->get_subtotal();
}*/
//Replace register text for create account
add_filter(  'gettext',  'Roots\Sage\Extras\register_text'  );
add_filter(  'ngettext',  'Roots\Sage\Extras\register_text'  );
function register_text( $translated ) {
     $translated = str_ireplace(  'Register',  'Create Account',  $translated );
     return $translated;
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
/* Filter to override cart_item_name */
//add_filter( 'woocommerce_cart_item_name', 'Roots\Sage\Extras\new_woocommerce_cart_item_name', 10, 2 );
/*
function my_custom_function(){
  do_action("woocommerce_tm_epo");
}

add_action( 'woocommerce_cart_contents','Roots\Sage\Extras\my_custom_function');*/

function get_product_options_cart($cart_item){
  $selected_options = array();
  foreach($cart_item['tmcartepo'] as $product_option ){
    if( $product_option['cssclass'] == 'right_1' || $product_option['cssclass'] == 'middle_2' || $product_option['cssclass'] == 'collapseme_latex'){
      $selected_options[] = $product_option['value'];
    }
  }
  return implode(", ", $selected_options);
}

function get_product_options_order($item){
  $selected_options = array();
  foreach($item['item_meta_array'] as $product_option){
    if( $product_option->key == "Please enter a second PMS Color code:" ){
      $selected_options[] = $product_option->value;
    } 
    elseif( $product_option->key == "Please enter a single PMS Color code:" ){
      $selected_options[] = $product_option->value;
    } 
    elseif( $product_option->value == "Add Latex Seal"){
      $selected_options[] = $product_option->value;
    }
  }
  return implode(", ", $selected_options);
}

function get_product_options_email($item){
  $selected_options = array();
  foreach($item['item_meta_array'] as $product_option){
    if( $product_option->key == "Please enter a second PMS Color code:" ){
      $selected_options[] = ' & '.$product_option->value;
    } 
    elseif( $product_option->key == "Please enter a single PMS Color code:" ){
      $selected_options[] = $product_option->value;
    } 
    elseif( $product_option->value == "Add Latex Seal"){
      $selected_options[] = '<br>Latex';
    }
  }
  return implode(" ", $selected_options);
}

//product thumbnail alt text the same as the product title
function woocommerce_get_product_thumbnail( $size = 'shop_catalog', $deprecated1 = 0, $deprecated2 = 0 ) {
  global $post;
  $image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );

  if ( has_post_thumbnail() ) {
      $props = wc_get_product_attachment_props( get_post_thumbnail_id(), $post );
      return get_the_post_thumbnail( $post->ID, $image_size, array(
          'title'  => $props['title'],
          'alt'    => get_the_title()." image."
      ) );
  } elseif ( wc_placeholder_img_src() ) {
      return wc_placeholder_img( $image_size );
  }
}
function woocommerce_template_loop_product_thumbnail() {
  echo woocommerce_get_product_thumbnail();
}

remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
add_action('woocommerce_before_shop_loop_item_title', 'Roots\Sage\Extras\woocommerce_template_loop_product_thumbnail', 10);

//show all search results in 1 page in search results page
function change_wp_search_size($query) {
    if ( $query->is_search ) // Make sure it is a search page
        $query->query_vars['posts_per_page'] = -1; // Change 10 to the number of posts you would like to show

    return $query; // Return our modified query variables
}
add_filter('pre_get_posts', 'Roots\Sage\Extras\change_wp_search_size'); // Hook our custom function onto the request filter

/** 
 *Reduce the strength requirement on the woocommerce password.
    Taken from: https://gist.github.com/BurlesonBrad/c89a825a64732a46b87c
 * 
 * Strength Settings
 * 3 = Strong (default)
 * 2 = Medium
 * 1 = Weak
 * 0 = Very Weak / Anything
 */
function reduce_woocommerce_min_strength_requirement( $strength ) {
    return 1;
}
add_filter( 'woocommerce_min_password_strength', 'reduce_woocommerce_min_strength_requirement' );

/** 
 * Change WooCommerce's password srength message labels
 * By Ryan Lanese
 */
function custom_password_message($vars) {
    $new_vars = array(
        'i18n_password_error' => esc_attr__( '', 'woocommerce' ),
        'i18n_password_hint' => esc_attr__( 'The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).', 'woocommerce' )
    );
    return array_merge($vars, $new_vars);
}

add_filter('wc_password_strength_meter_params', 'Roots\Sage\Extras\custom_password_message');