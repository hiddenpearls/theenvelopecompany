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
  /* acf_add_options_sub_page(array(
    'page_title'  => 'Theme Footer Settings',
    'menu_title'  => 'Footer',
    'parent_slug' => 'theme-general-settings',
  ));
  acf_add_options_sub_page(array(
    'page_title'  => 'Theme Header Settings',
    'menu_title'  => 'Header',
    'parent_slug' => 'theme-general-settings',
  ));
  
  */
  
}
/**
 * Remove wordpress' auto <p> tags on the excerpt
 */
remove_filter( 'the_excerpt', 'wpautop' );

/**
 * WooCommerce Theme integration
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
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 5 );

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'Roots\Sage\Extras\woocommerce_template_loop_attributes', 10 );
function woocommerce_template_loop_attributes(){
  global $product;
  //echo $product->list_attributes();
  $size = $product->get_attribute( 'size' ) ;
  echo '<p>'.$size.'</p>';
  $color = $product->get_attribute( 'color' ) ;
  echo '<p>'.$color.'</p>';
  

}

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

/**
 * Add login/logout link to the top navigation
 */
add_filter( 'wp_nav_menu_items', 'Roots\Sage\Extras\wti_loginout_menu_link', 10, 2 );
function wti_loginout_menu_link( $items, $args ) {
   if (($args->theme_location == 'top_navigation')||($args->theme_location == 'footer_navigation')) {
      if (is_user_logged_in()) {
         $items .= '<li class="pull-left menu-item"><a href="'. wp_logout_url("/") .'">'. __("Log Out") .'</a></li>';
         $items .= '<li class="pull-left menu-item"><a href="/my-account/">'. __("My Account") .'</a></li>';
      } else {
        //wp_login_url(get_permalink()) >> wordpress login URL
         $items .= '<li class="pull-left menu-item"><a href="/my-account/">'. __("Login") .'</a></li>';
         $items .= '<li class="pull-left menu-item"><a href="/my-account/">'. __("Create Account") .'</a></li>';
      }
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
    'payment-methods'    => __( 'Payment Methods', 'woocommerce' ),
  );
  return $myorder;
}
add_filter ( 'woocommerce_account_menu_items', 'Roots\Sage\Extras\wpb_woo_my_account_order' );

/**

* Add new register fields for WooCommerce registration.

*

* @return string Register fields HTML.

*/

function wooc_extra_register_fields() {
       ?>
       <p class="form-row form-row-first">
       <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?><span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
       </p>
       <p class="form-row form-row-last">
       <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
       </p>
       <div class="clear"></div>
       <!--<p class="form-row form-row-wide">
       <label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?><span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php //if ( ! empty( $_POST['billing_phone'] ) ) esc_attr_e( $_POST['billing_phone'] ); ?>" />
       </p>-->
       <?php
}
add_action( 'woocommerce_register_form_start', 'Roots\Sage\Extras\wooc_extra_register_fields' );
/**
* Validate the extra register fields.
*
* @param string $username         Current username.
* @param string $email             Current email.
* @param object $validation_errorsWP_Error object.
*
* @return void
*/

function wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {
       if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
              $validation_errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'woocommerce' ) );
       }
       if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
              $validation_errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );
       }
       /*if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
              $validation_errors->add( 'billing_phone_error', __( '<strong>Error</strong>: Phone is required!.', 'woocommerce' ) );
       }*/
}
add_action( 'woocommerce_register_post', 'Roots\Sage\Extras\wooc_validate_extra_register_fields', 10, 3 );
/**
* Save the extra register fields.
*
* @paramint $customer_id Current customer ID.
*
* @return void
*/
function wooc_save_extra_register_fields( $customer_id ) {
       if ( isset( $_POST['billing_first_name'] ) ) {
              // WordPress default first name field.
              update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
              // WooCommerce billing first name.
              update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
       }
       if ( isset( $_POST['billing_last_name'] ) ) {
              // WordPress default last name field.
              update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
              // WooCommerce billing last name.
              update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
       }
       /*if ( isset( $_POST['billing_phone'] ) ) {
              // WooCommerce billing phone
              update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
       }*/
}
add_action( 'woocommerce_created_customer', 'Roots\Sage\Extras\wooc_save_extra_register_fields' );