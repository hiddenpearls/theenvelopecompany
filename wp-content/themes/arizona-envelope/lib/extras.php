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
  acf_add_options_sub_page(array(
    'page_title'  => 'Analytics Settings',
    'menu_title'  => 'Analytics Settings',
    'parent_slug' => 'theme-general-settings',
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
 * Change single product pages
 */
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
function product_long_desc() {
  global $woocommerce, $post;

  if ( $post->post_content ) : ?>
    <div itemprop="description">

      <?php the_content(); ?>

    </div>
  <?php endif;
}
add_action( 'woocommerce_single_product_summary', 'Roots\Sage\Extras\product_long_desc', 20 );

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
        <label for="reg_password2"><?php _e( 'Password Repeat', 'woocommerce' ); ?> <span class="required">*</span></label>
        <input type="password" class="input-text" name="password2" id="reg_password2" value="<?php //if ( ! empty( $_POST['password2'] ) ) echo esc_attr( $_POST['password2'] ); ?>" />
      </p>-->
       <?php
}
add_action( 'woocommerce_register_form_start', 'Roots\Sage\Extras\wooc_extra_register_fields' );
// 
add_filter('woocommerce_registration_errors', 'Roots\Sage\Extras\registration_errors_validation', 10,3);
function registration_errors_validation($reg_errors, $sanitized_user_login, $user_email) {
  global $woocommerce;
  extract( $_POST );
  if ( strcmp( $password, $password2 ) !== 0 ) {
    return new WP_Error( 'registration-error', __( 'Passwords do not match.', 'woocommerce' ) );
  }
  return $reg_errors;
}
add_action( 'woocommerce_register_form', 'Roots\Sage\Extras\wc_register_form_password_repeat' );
function wc_register_form_password_repeat() {
  ?>
  <p class="form-row form-row-wide">
    <label for="reg_password2"><?php _e( 'Password Confirmation', 'woocommerce' ); ?> <span class="required">*</span></label>
    <input type="password" class="input-text" name="password2" id="reg_password2" value="<?php if ( ! empty( $_POST['password2'] ) ) echo esc_attr( $_POST['password2'] ); ?>" />
  </p>
  <?php
}
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
       if( isset($_POST['password']) && empty ( $_POST['password']) || isset($_POST['password2']) && empty ( $_POST['password2']) ){
        $validation_errors->add( 'registration-error', __( 'Passwords mismatch.', 'woocommerce' ) );
       }
      //return $reg_errors;
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

//old functions from old theme
// Price Dropdown
function yahm_cart_sel($product, $attr){
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
  //print_r($price);
  }
  $x=500; $yahm_arr = array();
    for($i=0; $i<60; $i++) {
      $j = number_format($x); 
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

/**
 * Gravity Wiz // Gravity Forms // Rename Uploaded Files
 *
 * Rename uploaded files for Gravity Forms. You can create a static naming template or using merge tags to base names on user input.
 *
 * Features:
 *  + supports single and multi-file upload fields
 *  + flexible naming template with support for static and dynamic values via GF merge tags
 *
 * Uses:
 *  + add a prefix or suffix to file uploads
 *  + include identifying submitted data in the file name like the user's first and last name
 *
 * @version   1.4
 * @author    David Smith <david@gravitywiz.com>
 * @license   GPL-2.0+
 * @link      http://gravitywiz.com/...
 */
class GW_Rename_Uploaded_Files {

    public function __construct( $args = array() ) {

        // set our default arguments, parse against the provided arguments, and store for use throughout the class
        $this->_args = wp_parse_args( $args, array(
            'form_id'  => false,
            'field_id' => false,
            'template' => ''
        ) );

        // do version check in the init to make sure if GF is going to be loaded, it is already loaded
        add_action( 'init', array( $this, 'init' ) );

    }

    public function init() {

        // make sure we're running the required minimum version of Gravity Forms
        if( ! is_callable( array( 'GFFormsModel', 'get_phsyical_file_path' ) ) ) {
            return;
        }

        //add_action( 'gform_pre_submission', array( $this, 'rename_uploaded_files' ) );
        add_filter( 'gform_entry_post_save', array( $this, 'rename_uploaded_files' ), 10, 2 );

    }

    function rename_uploaded_files( $entry, $form ) {

        if( ! $this->is_applicable_form( $form ) ) {
            return $entry;
        }

        foreach( $form['fields'] as &$field ) {

            if( ! $this->is_applicable_field( $field ) ) {
                continue;
            }

            $uploaded_files = rgar( $entry, $field->id );

            if( empty( $uploaded_files ) ) {
                continue;
            }

            if( $field->get_input_type() == 'post_image' ) {
                $file_bits = explode( '|:|', $uploaded_files );
                $uploaded_files = array( $file_bits[0] );
            } else if( $field->multipleFiles ) {
                $uploaded_files = json_decode( $uploaded_files );
            } else {
                $uploaded_files = array( $uploaded_files );
            }

            $renamed_files = array();

            foreach( $uploaded_files as $file ) {

                $orig_file_name = basename( $file );
                $new_file_name  = $this->rename_file( $orig_file_name, $entry );
                $new_file       = $this->increment_file_name( str_replace( $orig_file_name, $new_file_name, $file ) );

                if( ! file_exists( GFFormsModel::get_phsyical_file_path( $file ) ) ) {
                    continue;
                }

                rename( GFFormsModel::get_phsyical_file_path( $file ), GFFormsModel::get_phsyical_file_path( $new_file ) );

                $renamed_files[] = $new_file;

            }

            if( $field->get_input_type() == 'post_image' ) {
                $value = str_replace( $uploaded_files[0], $renamed_files[0], rgar( $entry, $field->id ) );
            } else if( $field->multipleFiles ) {
                $value = json_encode( $renamed_files );
            } else {
                $value = $renamed_files[0];
            }

            GFAPI::update_entry_field( $entry['id'], $field->id, $value );

            $entry[ $field->id ] = $value;

        }

        return $entry;
    }

    function increment_file_name( $file ) {

        $file_path = GFFormsModel::get_phsyical_file_path( $file );
        $pathinfo  = pathinfo( $file_path );
        $counter   = 1;

        // increment the filename if it already exists (i.e. balloons.jpg, balloons1.jpg, balloons2.jpg)
        while ( file_exists( $file_path ) ) {
            $file_path = str_replace( ".{$pathinfo['extension']}", "{$counter}.{$pathinfo['extension']}", GFFormsModel::get_phsyical_file_path( $file ) );
            $counter++;
        }

        $file = str_replace( basename( $file ), basename( $file_path ), $file );

        return $file;
    }

    function _rename_uploaded_files( $form ) {

        if( ! $this->is_applicable_form( $form ) ) {
            return;
        }

        foreach( $form['fields'] as &$field ) {

            if( ! $this->is_applicable_field( $field ) ) {
                continue;
            }

            $is_multi_file  = rgar( $field, 'multipleFiles' ) == true;
            $input_name     = sprintf( 'input_%s', $field['id'] );
            $uploaded_files = rgars( GFFormsModel::$uploaded_files, "{$form['id']}/{$input_name}" );

            if( $is_multi_file && ! empty( $uploaded_files ) && is_array( $uploaded_files ) ) {

                foreach( $uploaded_files as &$file ) {
                    $file['uploaded_filename'] = $this->rename_file( $file['uploaded_filename'] );
                }

                GFFormsModel::$uploaded_files[ $form['id'] ][ $input_name ] = $uploaded_files;

            } else {

                if( empty( $uploaded_files ) ) {

                    $uploaded_files = rgar( $_FILES, $input_name );
                    if( empty( $uploaded_files ) || empty( $uploaded_files['name'] ) ) {
                        continue;
                    }

                    $uploaded_files['name'] = $this->rename_file( $uploaded_files['name'] );
                    $_FILES[ $input_name ] = $uploaded_files;

                } else {

                    $uploaded_files = $this->rename_file( $uploaded_files );
                    GFFormsModel::$uploaded_files[ $form['id'] ][ $input_name ] = $uploaded_files;

                }

            }

        }

    }

    function rename_file( $filename, $entry ) {

        $file_info = pathinfo( $filename );
        $new_filename = $this->remove_slashes( $this->get_template_value( $this->_args['template'], $entry, $file_info['filename'] ) );

        return sprintf( '%s.%s', $new_filename, rgar( $file_info, 'extension' ) );
    }

    function get_template_value( $template, $entry, $filename ) {

        // replace our custom "{filename}" psuedo-merge-tag
        $template = str_replace( '{filename}', $filename, $template );

        $form = GFAPI::get_form( $entry['form_id'] );
        $template = $this->clean( GFCommon::replace_variables( $template, $form, $entry, false, true, false, 'text' ) );

        return $template;
    }

    function remove_slashes( $value ) {
        return stripslashes( str_replace( '/', '', $value ) );
    }

    function is_applicable_form( $form ) {

        $form_id = isset( $form['id'] ) ? $form['id'] : $form;

        return $form_id == $this->_args['form_id'];
    }

    function is_applicable_field( $field ) {

        $is_file_upload_field   = in_array( GFFormsModel::get_input_type( $field ), array( 'fileupload', 'post_image' ) );
        $is_applicable_field_id = $this->_args['field_id'] ? $field['id'] == $this->_args['field_id'] : true;

        return $is_file_upload_field && $is_applicable_field_id;
    }

    function clean( $str ) {
        return sanitize_title_with_dashes( strtr(
            utf8_decode( $str ), 
            utf8_decode( 'ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'),
            'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy'
        ), 'save' );
    }

}

# Configuration
//Original Template: Name (First):1.3}-{Name (Last):1.6}
new GW_Rename_Uploaded_Files( array(
    'form_id' => 12,
    'field_id' => 47,
    'template' => '{user:user_email}-{filename}' // most merge tags are supported, original file extension is preserved
) );
new GW_Rename_Uploaded_Files( array(
    'form_id' => 12,
    'field_id' => 70,
    'template' => '{user:user_email}-{filename}' // most merge tags are supported, original file extension is preserved
) );

/*
 *  Get endpoint titles
 */
/*function get_endpoint_title( $endpoint ) {
    global $wp;

    switch ( $endpoint ) {
      case 'order-pay' :
        $title = __( 'Pay for Order', 'woocommerce' );
      break;
      case 'order-received' :
        $title = __( 'Order Received', 'woocommerce' );
      break;
      case 'orders' :
        if ( ! empty( $wp->query_vars['orders'] ) ) {
          $title = sprintf( __( 'Orders (page %d)', 'woocommerce' ), intval( $wp->query_vars['orders'] ) );
        } else {
          $title = __( 'Orders', 'woocommerce' );
        }
      break;
      case 'view-order' :
        $order = wc_get_order( $wp->query_vars['view-order'] );
        $title = ( $order ) ? sprintf( __( 'Order #%s', 'woocommerce' ), $order->get_order_number() ) : '';
      break;
      case 'downloads' :
        $title = __( 'Downloads', 'woocommerce' );
      break;
      case 'edit-account' :
        $title = __( 'Account Details', 'woocommerce' );
      break;
      case 'edit-address' :
        $title = __( 'Addresses', 'woocommerce' );
      break;
      case 'payment-methods' :
        $title = __( 'Payment Methods', 'woocommerce' );
      break;
      case 'add-payment-method' :
        $title = __( 'Add Payment Method', 'woocommerce' );
      break;
      case 'lost-password' :
        $title = __( 'Lost Password', 'woocommerce' );
      break;
      default :
        $title = apply_filters( 'woocommerce_endpoint_' . $endpoint . '_title', '' );
      break;
    }

    return $title;
  }*/
 
/*
 *  Temporary piece of code for debugging
 */
/*add_action ('deprecated_argument_run', 'Roots\Sage\Extras\deprecated_argument_run', 10, 3);
function deprecated_argument_run ($function, $message, $version) {
    error_log ('Deprecated Argument Detected');
    $trace = debug_backtrace ();
    foreach ($trace as $frame) {
        error_log (var_export ($frame, true));
    }
}*/